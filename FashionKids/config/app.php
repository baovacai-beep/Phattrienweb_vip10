<?php
/**
 * Cấu hình ứng dụng chung (config/app.php)
 * Tự động nhận diện đường dẫn gốc (APP_BASE_URL) cho cả:
 *  - XAMPP Local/LAN (/FashionKids)
 *  - InfinityFree Hosting khi giải nén trực tiếp vào /htdocs/ ('')
 */

if (!defined('APP_BASE_URL')) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $httpHost   = $_SERVER['HTTP_HOST'] ?? '';
    $isRootHost = (
        !empty($httpHost) && (
            strpos($httpHost, 'infinityfreeapp.com') !== false ||
            strpos($httpHost, 'epizy.com') !== false ||
            strpos($httpHost, 'rf.gd') !== false
        ) && strpos($scriptName, '/FashionKids/') === false
    );
    if ($isRootHost || ($scriptName !== '' && dirname($scriptName) === '/')) {
        define('APP_BASE_URL', '');
    } else {
        define('APP_BASE_URL', '/FashionKids');
    }
}
if (!defined('APP_ENDPOINT'))   define('APP_ENDPOINT', (APP_BASE_URL !== '' ? APP_BASE_URL : '') . '/index.php');

/**
 * Chế độ DEMO cho OTP:
 *  - true  : trả mã OTP trong phản hồi JSON + ghi error_log (phục vụ quay demo / bảo vệ đồ án,
 *            vì XAMPP và Free Hosting chặn cổng SMTP).
 *  - false : KHÔNG bao giờ lộ mã OTP cho trình duyệt; mã được gửi bằng mail().
 */
if (!defined('OTP_DEMO_MODE'))      define('OTP_DEMO_MODE', true);
if (!defined('OTP_TTL'))            define('OTP_TTL', 300);          // Mã OTP có hiệu lực 5 phút
if (!defined('OTP_RESEND_COOLDOWN'))define('OTP_RESEND_COOLDOWN', 60); // Cho phép gửi lại sau 60 giây
if (!defined('OTP_MAX_ATTEMPTS'))   define('OTP_MAX_ATTEMPTS', 5);   // Nhập sai tối đa 5 lần
if (!defined('OTP_VERIFIED_TTL'))   define('OTP_VERIFIED_TTL', 1800);// Email đã xác thực có hiệu lực 30 phút để hoàn tất đăng ký

// Cấu hình máy chủ gửi thư thực tế Gmail SMTP (PHPMailer - Hồ Gia Bảo)
if (!defined('SMTP_HOST'))          define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_PORT'))          define('SMTP_PORT', 587);
if (!defined('SMTP_USER'))          define('SMTP_USER', 'hogiabaobc@gmail.com');
if (!defined('SMTP_PASS'))          define('SMTP_PASS', 'tkhfyuarjhhdifbn'); // App Password 16 ký tự: tkhf yuar jhhd ifbn
if (!defined('SMTP_FROM_NAME'))     define('SMTP_FROM_NAME', 'FashionKids Support');

// Chống dò mật khẩu: khóa tạm form đăng nhập sau N lần sai liên tiếp
if (!defined('LOGIN_MAX_FAILS'))    define('LOGIN_MAX_FAILS', 5);
if (!defined('LOGIN_LOCK_SECONDS')) define('LOGIN_LOCK_SECONDS', 60);
