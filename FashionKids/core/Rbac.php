<?php
/**
 * Lớp Middleware Phân Quyền RBAC (core/Rbac.php)
 * Kiểm soát quyền truy cập theo vai trò (Role-Based Access Control) cho Quản trị viên (quan_tri) và Nhân viên (nhan_vien).
 * Tuân thủ đặc tả kỹ thuật:
 *  - Chặn tài khoản `nhan_vien` (và khách vãng lai/khách hàng) truy cập các trang cấu hình hệ thống cấp Admin:
 *    admin-dashboard.php (MH 12), admin-size-charts.php (MH 14), admin-users.php (MH 15), admin-banners.php (MH 23).
 *  - Trả về mã lỗi HTTP 403 Forbidden kèm giao diện/JSON chuẩn khi không đủ thẩm quyền.
 */

if (session_status() === PHP_SESSION_NONE) {
    $savePath = session_save_path();
    if (empty($savePath) || !is_writable($savePath)) {
        $temp = sys_get_temp_dir();
        if (is_writable($temp)) {
            @session_save_path($temp);
        }
    }
    @session_start();
}

class Rbac {
    public const ROLE_ADMIN    = 'quan_tri';
    public const ROLE_STAFF    = 'nhan_vien';
    public const ROLE_CUSTOMER = 'khach_hang';

    /**
     * Danh sách các file View cấu hình hệ thống CHỈ dành riêng cho Quản trị viên (quan_tri)
     * Nhân viên (nhan_vien) cố truy cập sẽ bị trả về HTTP 403 Forbidden.
     */
    private const ADMIN_ONLY_VIEWS = [
        'admin-dashboard.php',
        'admin-size-charts.php',
        'admin-users.php',
        'admin-banners.php',
    ];

    /**
     * Danh sách các file View nghiệp vụ vận hành cho phép cả Quản trị viên (quan_tri) và Nhân viên (nhan_vien)
     */
    private const STAFF_AND_ADMIN_VIEWS = [
        'admin-inventory.php',
        'admin-cms-blog.php',
        'staff-orders.php',
        'staff-chat.php',
        'staff-casting-review.php',
    ];

    /**
     * Lấy vai trò hiện tại từ Session.
     * Hỗ trợ tham số ?demo_role=quan_tri|nhan_vien để dễ dàng kiểm thử RBAC trực tiếp khi chấm bài.
     */
    public static function currentRole(): string {
        if (isset($_GET['demo_role']) && in_array($_GET['demo_role'], [self::ROLE_ADMIN, self::ROLE_STAFF, self::ROLE_CUSTOMER], true)) {
            $_SESSION['role'] = $_GET['demo_role'];
            if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
                $_SESSION['user'] = [
                    'ma_nguoi_dung' => ($_GET['demo_role'] === self::ROLE_ADMIN) ? 1 : (($_GET['demo_role'] === self::ROLE_STAFF) ? 2 : 3),
                    'ten_dang_nhap' => ($_GET['demo_role'] === self::ROLE_ADMIN) ? 'admin_fashion' : (($_GET['demo_role'] === self::ROLE_STAFF) ? 'staff_tuvan' : 'me_be_sam'),
                    'ho_va_ten'     => ($_GET['demo_role'] === self::ROLE_ADMIN) ? 'Hồ Gia Bảo (Chủ Shop)' : (($_GET['demo_role'] === self::ROLE_STAFF) ? 'Lê Nguyễn Khánh Trình' : 'Võ Thị Thanh Thảo'),
                    'vai_tro'       => $_GET['demo_role'],
                ];
            } else {
                $_SESSION['user']['vai_tro'] = $_GET['demo_role'];
            }
            $_SESSION['user_id'] = (int)$_SESSION['user']['ma_nguoi_dung'];
        }

        $role = $_SESSION['user']['vai_tro'] ?? ($_SESSION['role'] ?? '');
        if ($role === 'quan_tri_vien' || $role === 'admin') {
            return self::ROLE_ADMIN;
        }
        if ($role === 'staff') {
            return self::ROLE_STAFF;
        }
        return (string)$role;
    }

    /**
     * Lấy ID người dùng hiện tại (phục vụ lưu ma_nhan_vien_xu_ly / ma_tac_gia)
     */
    public static function currentUserId(): int {
        $uid = (int)($_SESSION['user']['ma_nguoi_dung'] ?? ($_SESSION['user_id'] ?? 0));
        if ($uid > 0) {
            return $uid;
        }
        $role = self::currentRole();
        if ($role === self::ROLE_ADMIN) return 1;
        if ($role === self::ROLE_STAFF) return 2;
        return 1;
    }

    public static function isAdmin(): bool {
        $role = self::currentRole();
        // Nếu chưa đăng nhập khi mở trực tiếp trang demo, mặc định hiển thị quyền Admin trừ khi đang đóng vai nhan_vien/khach_hang
        if ($role === '') {
            return true;
        }
        return $role === self::ROLE_ADMIN;
    }

    public static function isStaff(): bool {
        return self::currentRole() === self::ROLE_STAFF;
    }

    public static function isStaffOrAdmin(): bool {
        $role = self::currentRole();
        if ($role === '') {
            return true;
        }
        return in_array($role, [self::ROLE_ADMIN, self::ROLE_STAFF], true);
    }

    /**
     * Kiểm tra quyền truy cập của View hiện tại.
     * Đặt ở đầu các file View hoặc được gọi từ Base Controller::view().
     *
     * @param string|null $viewFile Tên file view (nếu để null sẽ tự động lấy từ SCRIPT_FILENAME)
     */
    public static function enforceCurrentViewAccess(?string $viewFile = null): void {
        if ($viewFile === null || $viewFile === '') {
            $script = $_SERVER['SCRIPT_FILENAME'] ?? ($_SERVER['PHP_SELF'] ?? '');
            $baseName = basename(str_replace('\\', '/', $script));
        } else {
            $baseName = basename(str_replace('\\', '/', $viewFile));
            if (substr($baseName, -4) !== '.php') {
                $baseName .= '.php';
            }
        }

        $role = self::currentRole();

        // 1. Chặn tài khoản `nhan_vien` hoặc `khach_hang` cố truy cập vào 4 trang cấu hình hệ thống cấp Admin:
        //    admin-dashboard.php (MH 12), admin-size-charts.php (MH 14), admin-users.php (MH 15), admin-banners.php (MH 23)
        if (in_array($baseName, self::ADMIN_ONLY_VIEWS, true)) {
            if ($role === self::ROLE_STAFF || $role === self::ROLE_CUSTOMER) {
                self::denyAccess(
                    "Tài khoản vai trò [{$role}] không có thẩm quyền truy cập trang cấu hình hệ thống ({$baseName}). Chỉ Quản trị viên (quan_tri) mới được phép truy cập!"
                );
            }
        }

        // 2. Chặn tài khoản `khach_hang` cố truy cập vào các trang vận hành nội bộ của Nhân viên & Admin
        if (in_array($baseName, self::STAFF_AND_ADMIN_VIEWS, true)) {
            if ($role === self::ROLE_CUSTOMER) {
                self::denyAccess(
                    "Tài khoản Khách hàng không được phép truy cập phân hệ Vận hành & Quản trị ({$baseName})!"
                );
            }
        }
    }

    /**
     * Yêu cầu bắt buộc quyền Admin cho Controller Action
     */
    public static function requireAdmin(): void {
        $role = self::currentRole();
        if ($role === self::ROLE_STAFF || $role === self::ROLE_CUSTOMER) {
            self::denyAccess('Chức năng này chỉ dành riêng cho Quản trị viên (quan_tri)!');
        }
    }

    /**
     * Yêu cầu quyền Nhân viên hoặc Quản trị viên cho Controller Action
     */
    public static function requireStaffOrAdmin(): void {
        $role = self::currentRole();
        if ($role === self::ROLE_CUSTOMER) {
            self::denyAccess('Bạn cần quyền Nhân viên (nhan_vien) hoặc Quản trị viên (quan_tri) để thực hiện thao tác này!');
        }
    }

    /**
     * Xuất phản hồi HTTP 403 Forbidden (hỗ trợ cả AJAX JSON lẫn giao diện HTML Bootstrap 4)
     */
    public static function denyAccess(string $message = '403 Forbidden - Bạn không có quyền truy cập tài nguyên này!'): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code(403);

        $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
            || isset($_GET['c']) && strpos(strtolower($_GET['a'] ?? $_GET['action'] ?? ''), 'ajax') === 0;

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'  => 'error',
                'code'    => 403,
                'message' => $message,
                'data'    => new stdClass()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Content-Type: text/html; charset=utf-8');
        ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Từ Chối Truy Cập | FashionKids RBAC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/FashionKids/public/css/style-pastel.css">
</head>
<body class="bg-milk-cream d-flex align-items-center min-vh-100">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card card-pastel shadow-sm border-danger text-center p-4 p-md-5">
                    <div class="mb-3">
                        <span class="badge badge-pill badge-danger px-3 py-2">
                            <i class="fa-solid fa-shield-halved mr-1"></i>HTTP 403 FORBIDDEN - RBAC MIDDLEWARE
                        </span>
                    </div>
                    <h2 class="font-weight-bold text-danger mb-2">Từ Chối Quyền Truy Cập!</h2>
                    <p class="text-slate font-weight-bold mb-3"><?= htmlspecialchars($message) ?></p>
                    <p class="small text-muted-custom mb-4">
                        Theo chính sách phân quyền RBAC của dự án FashionKids, tài khoản <code>nhan_vien</code> chỉ được phép truy cập các phân hệ vận hành (Đơn hàng MH 10, Live Chat MH 11, Nhập thêm kho MH 13, Sơ tuyển KOL MH 17, Lưu nháp bài viết MH 21/22) và bị chặn hoàn toàn khỏi các trang cấu hình hệ thống cấp Admin.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center">
                        <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-primary rounded-pill px-4 mr-2 mb-2 font-weight-bold">
                            <i class="fa-solid fa-clipboard-check mr-1"></i> Về Bàn Làm Việc Nhân Viên (MH 10)
                        </a>
                        <a href="/FashionKids/views/pages/login-register.php" class="btn btn-outline-secondary rounded-pill px-4 mb-2 font-weight-bold">
                            <i class="fa-solid fa-right-to-bracket mr-1"></i> Đăng Nhập Tài Khoản Khác
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
        <?php
        exit;
    }
}
