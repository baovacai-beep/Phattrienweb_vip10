<?php
/**
 * Lớp kết nối PDO Singleton (core/Database.php)
 * Đảm bảo chỉ khởi tạo duy nhất một phiên kết nối PDO, bắt lỗi ngoại lệ PDOException.
 * Cung cấp đồng thời cả 2 phương thức getConnection() và getInstance() để tương thích
 * xuyên suốt giữa Giai đoạn 3, 4 và Giai đoạn 5 (Localhost, Shared Database LAN & InfinityFree Hosting).
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static ?PDO $instance = null;
    private static ?PDOException $failure = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * Phương thức chuẩn Giai đoạn 5: Trả về kết nối PDO duy nhất (Singleton)
     */
    public static function getConnection(): PDO {
        return self::getInstance();
    }

    /**
     * Phương thức Singleton khởi tạo và trả về đối tượng PDO
     */
    public static function getInstance(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }
        if (self::$failure !== null) {
            throw self::$failure;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 3,
        ];

        // Danh sách ứng viên kết nối (Ưu tiên cấu hình chính từ config/database.php,
        // kèm cơ chế tự động dự phòng giữa IP LAN 192.168.1.15 / 192.168.1.17 và localhost)
        $candidates = [
            ['host' => DB_HOST, 'dbname' => DB_NAME, 'user' => DB_USER, 'pass' => DB_PASS],
        ];

        $isHostingEnv = (strpos(DB_HOST, 'infinityfree.com') !== false || strpos(DB_HOST, 'epizy.com') !== false);
        if (!$isHostingEnv) {
            if (DB_HOST === '192.168.1.15') {
                $candidates[] = ['host' => '192.168.1.17', 'dbname' => DB_NAME, 'user' => DB_USER, 'pass' => DB_PASS];
                $candidates[] = ['host' => '192.168.1.17', 'dbname' => DB_NAME, 'user' => 'root', 'pass' => ''];
            }
            if (DB_HOST !== 'localhost') {
                $candidates[] = ['host' => 'localhost', 'dbname' => DB_NAME, 'user' => 'root', 'pass' => ''];
            }
        }

        $lastException = null;
        foreach ($candidates as $cfg) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;port=%s;charset=%s',
                $cfg['host'], $cfg['dbname'], DB_PORT, DB_CHARSET
            );
            try {
                self::$instance = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
                return self::$instance;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        self::$failure = $lastException;
        error_log('[FashionKids] Lỗi kết nối CSDL ' . DB_HOST . ': ' . ($lastException ? $lastException->getMessage() : 'Unknown'));
        throw $lastException;
    }

    /**
     * Gắn sẵn một kết nối PDO (dùng cho kiểm thử tự động).
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
