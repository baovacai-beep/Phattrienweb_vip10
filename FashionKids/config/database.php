<?php
/**
 * Cấu hình hằng số kết nối Cơ sở dữ liệu MySQL
 * FashionKids D2C Platform - Nhóm 10 (UEH)
 */

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'db_fashionkids');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_PORT')) define('DB_PORT', '3306');
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
