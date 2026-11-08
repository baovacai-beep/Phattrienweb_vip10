<?php
/**
 * Lớp Base Model (core/Model.php)
 * Cung cấp kết nối PDO dùng chung cho toàn bộ tầng Model.
 * Cho phép tiêm (inject) một PDO có sẵn qua constructor để kiểm thử độc lập.
 */

require_once __DIR__ . '/Database.php';

abstract class Model {
    protected ?PDO $db = null;

    public function __construct(?PDO $db = null) {
        if ($db !== null) {
            $this->db = $db;
            return;
        }
        try {
            $this->db = Database::getInstance();
        } catch (PDOException $e) {
            $this->db = null;
        }
    }

    public function hasConnection(): bool {
        return $this->db instanceof PDO;
    }
}
