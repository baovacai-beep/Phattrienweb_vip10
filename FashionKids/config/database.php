<?php
/**
 * Tệp: config/database.php - Cấu hình đa môi trường (Localhost, LAN & Hosting InfinityFree)
 * Tự động nhận diện 3 môi trường chạy:
 *  1. Môi trường Production (InfinityFree Hosting: *.infinityfreeapp.com)
 *  2. Môi trường Máy chủ XAMPP cục bộ (Localhost - Máy của Hồ Gia Bảo)
 *  3. Môi trường Mạng LAN nội bộ (Shared Database LAN - Máy của Bảo Ngọc, Khánh Trình, Trần Gia Bảo)
 */

$httpHost   = $_SERVER['HTTP_HOST'] ?? '';
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

$isHosting = (
    !empty($httpHost) && (
        strpos($httpHost, 'infinityfree.com') !== false ||
        strpos($httpHost, 'infinityfreeapp.com') !== false ||
        strpos($httpHost, 'free.nf') !== false ||
        strpos($httpHost, 'great-site.net') !== false ||
        strpos($httpHost, 'epizy.com') !== false ||
        strpos($httpHost, 'rf.gd') !== false ||
        strpos($httpHost, 'kesug.com') !== false
    )
);
$isLocalhost = in_array($remoteAddr, ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true)
    || stripos($httpHost, 'localhost') === 0
    || strpos($httpHost, '127.0.0.1') === 0;

if ($isHosting) {
    // 1. Môi trường Production (InfinityFree Hosting - qrrhmzrn.infinityfree.com)
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'sql301.infinityfree.com');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'if0_43426037_fashionkids');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'if0_43426037');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: 'YMIDguvMTUj7ir');
} elseif ($isLocalhost) {
    // 2. Môi trường Máy chủ XAMPP cục bộ (Máy của Hồ Gia Bảo)
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'db_fashionkids');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
} else {
    // 3. Mạng LAN nội bộ (Máy của Bảo Ngọc, Khánh Trình, Trần Gia Bảo kết nối tới máy chủ Hồ Gia Bảo)
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '192.168.1.15'); // IP LAN máy chủ của Hồ Gia Bảo (hỗ trợ cả 192.168.1.17)
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'db_fashionkids');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'team_fashionkids');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '123456');
}

if (!defined('DB_PORT'))    define('DB_PORT', getenv('DB_PORT') ?: '3306');
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
