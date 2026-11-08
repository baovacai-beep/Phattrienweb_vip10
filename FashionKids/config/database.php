<?php
/**
 * Cấu hình kết nối Cơ sở dữ liệu MySQL (config/database.php)
 * Hỗ trợ linh hoạt cả Localhost & Mạng nội bộ LAN (Shared Database 192.168.1.17)
 */

$serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

// Tự động nhận diện: Nếu máy khách trong mạng LAN truy cập vào máy chủ
$isRemoteClient = false;
if (!empty($serverAddr) || !empty($remoteAddr)) {
    if ($serverAddr !== '192.168.1.17' && $remoteAddr !== '127.0.0.1' && $remoteAddr !== '::1') {
        $isRemoteClient = true;
    }
}

if (!defined('DB_HOST')) {
    $envHost = getenv('DB_HOST');
    define('DB_HOST', $envHost ?: ($isRemoteClient ? '192.168.1.17' : 'localhost'));
}
if (!defined('DB_NAME'))    define('DB_NAME', 'db_fashionkids');
if (!defined('DB_USER'))    define('DB_USER', 'root');
if (!defined('DB_PASS'))    define('DB_PASS', ''); // Điền mật khẩu MySQL nếu có đặt
if (!defined('DB_PORT'))    define('DB_PORT', '3306');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

return [
    'host'     => DB_HOST,
    'port'     => (int)DB_PORT,
    'dbname'   => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASS,
    'charset'  => DB_CHARSET,
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
