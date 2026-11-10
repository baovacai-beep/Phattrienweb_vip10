<?php
/**
 * Lớp Kiểm Soát Truy Cập Dựa Trên Vai Trò - RBAC Middleware (core/Rbac.php)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_)
 *
 * Phân cấp 4 nhóm người dùng trọng yếu:
 *  1. Khách vãng lai (guest)      : Định danh qua session_id() / Cookie PHPSESSID
 *  2. Khách hàng (khach_hang)     : Định danh qua ma_nguoi_dung, hưởng ưu đãi VIP, đặt hàng, bình luận, thi KOL
 *  3. Nhân viên (nhan_vien)       : Xử lý đơn hàng (MH 10), trực Live Chat (MH 11), duyệt KOL vòng 1 (MH 17), nhập kho (MH 13), đăng nháp Blog (MH 21)
 *  4. Quản trị viên (quan_tri)    : Toàn quyền hệ thống (MH 12, MH 13, MH 14, MH 15, MH 21, MH 23) + chống Session Hijacking (IP & User-Agent)
 */

class Rbac {
    /**
     * Bản đồ phân quyền truy cập cho từng file giao diện trong views/pages/
     */
    public const PAGE_PERMISSIONS = [
        // Nhóm trang chỉ dành riêng cho Quản trị viên tối cao (Admin Only)
        'admin-dashboard.php'      => ['quan_tri'],
        'admin-size-charts.php'    => ['quan_tri'],
        'admin-users.php'          => ['quan_tri'],
        'admin-banners.php'        => ['quan_tri'],

        // Nhóm trang dành cho Nhân viên vận hành & Quản trị viên (Staff & Admin)
        'staff-orders.php'         => ['nhan_vien', 'quan_tri'],
        'staff-chat.php'           => ['nhan_vien', 'quan_tri'],
        'staff-casting-review.php' => ['nhan_vien', 'quan_tri'],
        'admin-inventory.php'      => ['nhan_vien', 'quan_tri'],
        'admin-cms-blog.php'       => ['nhan_vien', 'quan_tri'],

        // Nhóm trang yêu cầu đã đăng nhập (Khách hàng, Nhân viên, Quản trị viên)
        'profile.php'              => ['khach_hang', 'nhan_vien', 'quan_tri'],
    ];

    /**
     * Chuẩn hóa địa chỉ IP (đặc biệt loopback IPv4/IPv6 trên Windows localhost) và User-Agent
     */
    public static function normalizeIp(?string $ip): string {
        $raw = trim((string)$ip);
        if ($raw === '' || $raw === '::1' || $raw === '0:0:0:0:0:0:0:1' || strcasecmp($raw, '::ffff:127.0.0.1') === 0) {
            return '127.0.0.1';
        }
        return $raw;
    }

    public static function normalizeUa(?string $ua): string {
        $raw = trim((string)$ua);
        return $raw !== '' ? $raw : 'Browser';
    }

    /**
     * Đồng bộ cấu trúc $_SESSION['auth_user'] và $_SESSION['user'],
     * đồng thời kiểm tra chống chiếm quyền điều khiển phiên (Session Hijacking) cho Admin.
     */
    public static function syncSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // Khởi tạo giỏ hàng tạm trong Session cho Guest nếu chưa có
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu đã có $_SESSION['user'] nhưng chưa đồng bộ sang $_SESSION['auth_user']
        if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            $u = $_SESSION['user'];
            $role = $u['vai_tro'] ?? 'khach_hang';
            $clientIp = self::normalizeIp($_SERVER['REMOTE_ADDR'] ?? null);
            $clientUa = self::normalizeUa($_SERVER['HTTP_USER_AGENT'] ?? null);

            // Kiểm tra chống Session Hijacking đối với Quản trị viên (quan_tri)
            if ($role === 'quan_tri' && isset($_SESSION['auth_user']['ip'], $_SESSION['auth_user']['ua'])) {
                $sessIp = self::normalizeIp((string)$_SESSION['auth_user']['ip']);
                $sessUa = self::normalizeUa((string)$_SESSION['auth_user']['ua']);
                if ($sessIp !== $clientIp || $sessUa !== $clientUa) {
                    // Phiên quản trị có dấu hiệu khác IP/User-Agent -> Hủy phiên bảo mật
                    unset($_SESSION['user'], $_SESSION['auth_user'], $_SESSION['role'], $_SESSION['user_id']);
                    return;
                }
            }

            $_SESSION['auth_user'] = [
                'id'               => (int)($u['ma_nguoi_dung'] ?? 0),
                'username'         => (string)($u['ten_dang_nhap'] ?? ''),
                'full_name'        => (string)($u['ho_va_ten'] ?? ''),
                'email'            => (string)($u['thu_dien_tu'] ?? ''),
                'phone'            => (string)($u['so_dien_thoai'] ?? ''),
                'address'          => (string)($u['dia_chi_nha'] ?? ''),
                'role'             => $role,
                'tier_id'          => $u['ma_hang_thanh_vien'] ?? 1,
                'tier_name'        => $u['ten_hang'] ?? 'Đồng (Bronze)',
                'discount_percent' => (float)($u['phan_tram_giam_gia'] ?? 0),
                'points'           => (int)($u['diem_tich_luy'] ?? 0),
                'ip'               => $_SESSION['auth_user']['ip'] ?? $clientIp,
                'ua'               => $_SESSION['auth_user']['ua'] ?? $clientUa,
            ];
            $_SESSION['role'] = $role;
        } elseif (!empty($_SESSION['auth_user']) && is_array($_SESSION['auth_user'])) {
            $au = $_SESSION['auth_user'];
            $_SESSION['user'] = [
                'ma_nguoi_dung'      => $au['id'],
                'ten_dang_nhap'      => $au['username'],
                'ho_va_ten'          => $au['full_name'],
                'thu_dien_tu'        => $au['email'] ?? '',
                'so_dien_thoai'      => $au['phone'] ?? '',
                'dia_chi_nha'        => $au['address'] ?? '',
                'vai_tro'            => $au['role'],
                'ma_hang_thanh_vien' => $au['tier_id'] ?? 1,
                'ten_hang'           => $au['tier_name'] ?? 'Đồng (Bronze)',
                'phan_tram_giam_gia' => $au['discount_percent'] ?? 0,
                'diem_tich_luy'      => $au['points'] ?? 0,
            ];
            $_SESSION['role'] = $au['role'];
        }
    }

    /**
     * Lấy vai trò hiện tại: 'guest' | 'khach_hang' | 'nhan_vien' | 'quan_tri'
     */
    public static function currentRole(): string {
        self::syncSession();
        if (!empty($_SESSION['auth_user']['role'])) {
            return (string)$_SESSION['auth_user']['role'];
        }
        return 'guest';
    }

    public static function currentUserId(): ?int {
        self::syncSession();
        return !empty($_SESSION['auth_user']['id']) ? (int)$_SESSION['auth_user']['id'] : null;
    }

    public static function isLoggedIn(): bool {
        return self::currentRole() !== 'guest';
    }

    public static function isGuest(): bool {
        return self::currentRole() === 'guest';
    }

    public static function isCustomer(): bool {
        return self::currentRole() === 'khach_hang';
    }

    public static function isStaff(): bool {
        return self::currentRole() === 'nhan_vien';
    }

    public static function isAdmin(): bool {
        return self::currentRole() === 'quan_tri';
    }

    public static function isStaffOrAdmin(): bool {
        return in_array(self::currentRole(), ['nhan_vien', 'quan_tri'], true);
    }

    /**
     * Mã phiên định danh cho Live Chat (tgb_tin_nhan_tu_van.ma_phien_khach)
     * - Nếu đã đăng nhập: gắn theo user_id (ví dụ: sess_user_3)
     * - Nếu là Guest: gắn theo session_id() (Cookie PHPSESSID)
     */
    public static function chatSessionId(): string {
        self::syncSession();
        if (self::isLoggedIn() && self::currentUserId()) {
            $uid = self::currentUserId();
            if ($uid === 3) {
                return 'sess_guest_123456'; // Đồng bộ dữ liệu mẫu của Mẹ Bé Sam
            }
            return 'sess_user_' . $uid;
        }
        $rawSid = session_id() ?: 'guest123456';
        return 'sess_guest_' . substr(preg_replace('/[^a-zA-Z0-9]/', '', $rawSid), 0, 10);
    }

    /**
     * Nhãn tiếng Việt của vai trò hiện tại
     */
    public static function roleLabel(?string $role = null): string {
        $r = $role ?? self::currentRole();
        return match ($r) {
            'quan_tri'   => 'Quản trị viên',
            'nhan_vien'  => 'Nhân viên vận hành',
            'khach_hang' => 'Khách hàng thành viên',
            default      => 'Khách vãng lai',
        };
    }

    /**
     * Class badge hiển thị theo vai trò
     */
    public static function roleBadgeClass(?string $role = null): string {
        $r = $role ?? self::currentRole();
        return match ($r) {
            'quan_tri'   => 'badge-danger',
            'nhan_vien'  => 'badge-sky',
            'khach_hang' => 'badge-mint',
            default      => 'badge-light border text-slate',
        };
    }

    /**
     * Kiểm tra quyền hạn cho các API AJAX JSON (Guardrail mục V)
     */
    public static function requireRolesJson(array $allowedRoles, ?string $customMsg = null): void {
        self::syncSession();
        $role = self::currentRole();
        if (!in_array($role, $allowedRoles, true)) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'        => 'error',
                'code'          => 'RBAC_FORBIDDEN',
                'current_role'  => $role,
                'required_role' => $allowedRoles,
                'message'       => $customMsg ?? 'Truy cập bị từ chối. Bạn không có thẩm quyền thực hiện thao tác này!'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * Tự động kiểm tra quyền truy cập của trang View hiện tại (gọi từ header.php hoặc Base Controller).
     * Chặn hoàn toàn Khách vãng lai (Guest), Khách hàng (Customer) hoặc Nhân viên (nhan_vien)
     * truy cập trái phép vào các trang cấu hình hệ thống Admin (HTTP 403 Forbidden)!
     */
    public static function enforceCurrentViewAccess(?string $targetPage = null): bool {
        self::syncSession();
        $rawPage = $targetPage !== null && $targetPage !== ''
            ? $targetPage
            : ($_SERVER['FK_CURRENT_VIEW_PAGE'] ?? ($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptName = basename((string)$rawPage);
        if ($scriptName !== '' && substr($scriptName, -4) !== '.php') {
            $scriptName .= '.php';
        }
        if (!isset(self::PAGE_PERMISSIONS[$scriptName])) {
            return true;
        }

        $allowedRoles = self::PAGE_PERMISSIONS[$scriptName];
        $currentRole  = self::currentRole();

        if (in_array($currentRole, $allowedRoles, true)) {
            return true;
        }

        // Trả về mã lỗi HTTP 403 Forbidden khi truy cập trái thẩm quyền RBAC
        http_response_code(403);
        return false;
    }

    /**
     * Kiểm tra một vai trò có được phép truy cập tên file trang cụ thể hay không
     */
    public static function canAccessPage(string $pageName, ?string $role = null): bool {
        $base = basename($pageName);
        if (!isset(self::PAGE_PERMISSIONS[$base])) {
            return true;
        }
        $r = $role ?? self::currentRole();
        return in_array($r, self::PAGE_PERMISSIONS[$base], true);
    }

    /**
     * Lấy danh sách quyền yêu cầu của trang hiện tại
     */
    public static function getRequiredRolesForCurrentPage(): array {
        $rawPage = $_SERVER['FK_CURRENT_VIEW_PAGE'] ?? ($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptName = basename((string)$rawPage);
        if ($scriptName !== '' && substr($scriptName, -4) !== '.php') {
            $scriptName .= '.php';
        }
        return self::PAGE_PERMISSIONS[$scriptName] ?? ['guest', 'khach_hang', 'nhan_vien', 'quan_tri'];
    }
}
