<?php
/**
 * Cấu hình ứng dụng chung (config/app.php)
 * Các hằng số nghiệp vụ dùng cho phân hệ xác thực hgb_ (OTP, đăng nhập).
 * Chỉ định nghĩa nếu chưa tồn tại để có thể ghi đè từ môi trường kiểm thử.
 */

// Đường dẫn gốc của ứng dụng trên web server (XAMPP: htdocs/FashionKids)
if (!defined('APP_BASE_URL'))   define('APP_BASE_URL', '/FashionKids');
if (!defined('APP_ENDPOINT'))   define('APP_ENDPOINT', APP_BASE_URL . '/index.php');

/**
 * Chế độ DEMO cho OTP:
 *  - true  : trả mã OTP trong phản hồi JSON + ghi error_log (phục vụ quay demo / bảo vệ đồ án,
 *            vì XAMPP chưa cấu hình SMTP).
 *  - false : KHÔNG bao giờ lộ mã OTP cho trình duyệt; mã được gửi bằng mail().
 * Khi triển khai thật BẮT BUỘC đặt về false.
 */
if (!defined('OTP_DEMO_MODE'))      define('OTP_DEMO_MODE', true);
if (!defined('OTP_TTL'))            define('OTP_TTL', 300);          // Mã OTP có hiệu lực 5 phút
if (!defined('OTP_RESEND_COOLDOWN'))define('OTP_RESEND_COOLDOWN', 60); // Cho phép gửi lại sau 60 giây
if (!defined('OTP_MAX_ATTEMPTS'))   define('OTP_MAX_ATTEMPTS', 5);   // Nhập sai tối đa 5 lần
if (!defined('OTP_VERIFIED_TTL'))   define('OTP_VERIFIED_TTL', 1800);// Email đã xác thực có hiệu lực 30 phút để hoàn tất đăng ký

// Chống dò mật khẩu: khóa tạm form đăng nhập sau N lần sai liên tiếp
if (!defined('LOGIN_MAX_FAILS'))    define('LOGIN_MAX_FAILS', 5);
if (!defined('LOGIN_LOCK_SECONDS')) define('LOGIN_LOCK_SECONDS', 60);
