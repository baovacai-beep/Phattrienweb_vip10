<?php
/**
 * Lớp bảo vệ CSRF (core/Csrf.php)
 * Sinh token ngẫu nhiên lưu trong Session; client gửi lại qua header X-CSRF-Token (AJAX)
 * hoặc trường POST csrf_token (form truyền thống).
 */

final class Csrf {
    private const SESSION_KEY = 'fk_csrf_token';

    private function __construct() {}

    /**
     * Lấy (hoặc tạo mới) token của phiên hiện tại
     */
    public static function token(): string {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return '';
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Kiểm tra token do client gửi lên có khớp với token trong Session không
     */
    public static function validate(): bool {
        $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        $real = $_SESSION[self::SESSION_KEY] ?? '';
        return is_string($sent) && $real !== '' && hash_equals($real, $sent);
    }
}
