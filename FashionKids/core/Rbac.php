<?php
/**
 * Middleware phân quyền RBAC (core/Rbac.php)
 * Ba vai trò trong CSDL (hgb_nguoi_dung.vai_tro): quan_tri | nhan_vien | khach_hang
 *
 *  - enforceCurrentViewAccess(): đặt ở ĐẦU file View (trước header.php) để chặn truy cập trang.
 *  - requirePermission($perm)  : đặt ở đầu mỗi action AJAX của Controller.
 *
 * Chưa đăng nhập: trang HTML được chuyển về trang đăng nhập; AJAX nhận JSON 401.
 * Sai quyền    : HTTP 403 Forbidden.
 */

final class Rbac {
    public const ADMIN    = 'quan_tri';
    public const STAFF    = 'nhan_vien';
    public const CUSTOMER = 'khach_hang';

    /** Vai trò được vào từng View (khóa = tên file, không gồm .php). */
    private const VIEW_ROLES = [
        // Cấu hình hệ thống: CHỈ Quản trị viên (MH 12, 14, 15, 23)
        'admin-dashboard'      => [self::ADMIN],
        'admin-size-charts'    => [self::ADMIN],
        'admin-users'          => [self::ADMIN],
        'admin-banners'        => [self::ADMIN],
        // Màn hình vận hành: Quản trị viên + Nhân viên (MH 10, 11, 13, 17, 21/22)
        'admin-inventory'      => [self::ADMIN, self::STAFF],
        'admin-cms-blog'       => [self::ADMIN, self::STAFF],
        'staff-orders'         => [self::ADMIN, self::STAFF],
        'staff-chat'           => [self::ADMIN, self::STAFF],
        'staff-casting-review' => [self::ADMIN, self::STAFF],
    ];

    /** Vai trò được thực hiện từng hành động AJAX. */
    private const PERMISSIONS = [
        // Cả hai vai trò
        'order.process'   => [self::ADMIN, self::STAFF],
        'chat.reply'      => [self::ADMIN, self::STAFF],
        'stock.lookup'    => [self::ADMIN, self::STAFF],
        'stock.restock'   => [self::ADMIN, self::STAFF],
        'kol.invite'      => [self::ADMIN, self::STAFF],
        'blog.draft'      => [self::ADMIN, self::STAFF],
        // Chỉ Quản trị viên
        'product.manage'  => [self::ADMIN],
        'vip.config'      => [self::ADMIN],
        'size.config'     => [self::ADMIN],
        'kol.decide'      => [self::ADMIN],
        'blog.publish'    => [self::ADMIN],
        'blog.delete'     => [self::ADMIN],
    ];

    private function __construct() {}

    private static function ensureSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $savePath = session_save_path();
            if (empty($savePath) || !is_writable($savePath)) {
                $tempDir = sys_get_temp_dir();
                if (is_writable($tempDir)) {
                    @session_save_path($tempDir);
                }
            }
            @session_start();
        }
    }

    public static function role(): ?string {
        self::ensureSession();
        $role = $_SESSION['user']['vai_tro'] ?? $_SESSION['role'] ?? null;
        return is_string($role) ? $role : null;
    }

    public static function userId(): int {
        self::ensureSession();
        return (int)($_SESSION['user']['ma_nguoi_dung'] ?? $_SESSION['user_id'] ?? 0);
    }

    public static function isAdmin(): bool {
        return self::role() === self::ADMIN;
    }

    public static function isStaff(): bool {
        return self::role() === self::STAFF;
    }

    /** Dùng trong View để ẩn/hiện nút theo quyền. */
    public static function can(string $permission): bool {
        $allowed = self::PERMISSIONS[$permission] ?? [];
        return in_array(self::role(), $allowed, true);
    }

    /**
     * Chặn truy cập View hiện tại.
     * @param string|null $viewFile Truyền __FILE__ khi gọi từ View nằm trong views/pages/
     *                              (khi View được nạp qua index.php thì SCRIPT_FILENAME là index.php).
     */
    public static function enforceCurrentViewAccess(?string $viewFile = null): void {
        self::ensureSession();
        $file = $viewFile ?? ($_SERVER['SCRIPT_FILENAME'] ?? '');
        $key  = pathinfo((string)$file, PATHINFO_FILENAME);

        // Mặc định từ chối: View không khai báo trong bảng quyền thì không ai được vào
        $allowed = self::VIEW_ROLES[$key] ?? [];
        $role    = self::role();

        if ($role === null) {
            self::denyUnauthenticated(false);
        }
        if (!in_array($role, $allowed, true)) {
            self::deny403(false);
        }
    }

    /** Dùng trong Controller (AJAX) – trả JSON 401/403 nếu không đủ quyền. */
    public static function requirePermission(string $permission): void {
        self::ensureSession();
        $role = self::role();
        if ($role === null) {
            self::denyUnauthenticated(true);
        }
        if (!self::can($permission)) {
            self::deny403(true);
        }
    }

    private static function clearBuffers(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    private static function denyUnauthenticated(bool $json): void {
        self::clearBuffers();
        if ($json) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $base = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';
        header('Location: ' . $base . '/views/pages/login-register.php');
        exit;
    }

    private static function deny403(bool $json): void {
        self::clearBuffers();
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'  => 'error',
                'code'    => 403,
                'message' => 'Truy cập bị từ chối (403 Forbidden): tài khoản không đủ quyền thực hiện thao tác này.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $base = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8">'
           . '<meta name="viewport" content="width=device-width, initial-scale=1">'
           . '<title>403 Forbidden - FashionKids</title>'
           . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">'
           . '<link rel="stylesheet" href="' . htmlspecialchars($base) . '/public/css/style-pastel.css">'
           . '</head><body class="bg-milk-cream"><div class="container text-center py-5">'
           . '<h1 class="display-1 font-weight-bold text-peach">403</h1>'
           . '<h4 class="text-slate mb-3">Bạn không có quyền truy cập trang này</h4>'
           . '<p class="text-muted-custom mb-4">Chức năng này chỉ dành cho Quản trị viên hệ thống.</p>'
           . '<a href="' . htmlspecialchars($base) . '/views/pages/staff-orders.php" class="btn btn-pastel-primary rounded-pill px-4 mr-2">Về bàn làm việc</a>'
           . '<a href="' . htmlspecialchars($base) . '/index.php" class="btn btn-pastel-outline rounded-pill px-4">Về trang chủ</a>'
           . '</div></body></html>';
        exit;
    }
}
