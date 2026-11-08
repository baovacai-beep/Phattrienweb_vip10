<?php
/**
 * Lớp kết nối PDO Singleton (core/Database.php)
 * Đảm bảo chỉ khởi tạo duy nhất một phiên kết nối PDO, bắt lỗi ngoại lệ PDOException.
 * Nếu lần kết nối đầu thất bại, kết quả lỗi được ghi nhớ trong suốt request để các Model
 * không thử lại (và không ghi log lặp) nhiều lần.
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static ?PDO $instance = null;
    private static ?PDOException $failure = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }
        if (self::$failure !== null) {
            throw self::$failure;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%s;charset=%s',
            DB_HOST, DB_NAME, DB_PORT, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 3,
        ];

        try {
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            self::$failure = $e;
            error_log('[FashionKids] Lỗi kết nối CSDL ' . DB_HOST . ': ' . $e->getMessage());
            throw $e;
        }
        return self::$instance;
    }

    /**
     * Gắn sẵn một kết nối PDO (dùng cho kiểm thử tự động, ví dụ SQLite in-memory).
     * Truyền null để xóa trạng thái.
     */
    public static function setInstance(?PDO $pdo): void {
        self::$instance = $pdo;
        self::$failure  = null;
    }

    public static function isConnected(): bool {
        try {
            return self::getInstance() instanceof PDO;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getLastError(): ?string {
        return self::$failure?->getMessage();
    }
}
