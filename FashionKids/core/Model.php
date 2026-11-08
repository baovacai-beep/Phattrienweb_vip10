<?php
/**
 * Lớp Base Model (core/Model.php)
 * Đóng vai trò lớp cơ sở cung cấp kết nối PDO dùng chung cho toàn bộ tầng Model.
 */

require_once __DIR__ . '/Database.php';

abstract class Model {
    protected ?PDO $db = null;

    public function __construct() {
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
