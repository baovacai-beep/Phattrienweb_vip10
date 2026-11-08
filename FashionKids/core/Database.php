<?php
/**
 * Lớp kết nối PDO Singleton (core/Database.php)
 * Đảm bảo chỉ khởi tạo duy nhất một phiên kết nối PDO, bắt lỗi ngoại lệ PDOException.
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static ?PDO $instance = null;
    private static ?string $lastError = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $host    = defined('DB_HOST') ? DB_HOST : 'localhost';
            $dbname  = defined('DB_NAME') ? DB_NAME : 'db_fashionkids';
            $port    = defined('DB_PORT') ? DB_PORT : '3306';
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $user    = defined('DB_USER') ? DB_USER : 'root';
            $pass    = defined('DB_PASS') ? DB_PASS : '';

            $dsn = "mysql:host={$host};dbname={$dbname};port={$port};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                self::$lastError = $e->getMessage();
                error_log("Lỗi kết nối CSDL FashionKids: " . $e->getMessage());
                throw $e;
            }
        }
        return self::$instance;
    }

    public static function isConnected(): bool {
        try {
            return self::getInstance() instanceof PDO;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getLastError(): ?string {
        return self::$lastError;
    }
}
