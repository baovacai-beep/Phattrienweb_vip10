<?php
/**
 * Lớp Quản Trị Viên (models/entities/Admin.php)
 * Kế thừa User - Toàn quyền quản trị hệ thống
 */

require_once __DIR__ . '/User.php';

class Admin extends User {
    public function __construct(
        int $id = 0,
        string $username = '',
        string $name = '',
        string $email = '',
        string $phone = ''
    ) {
        parent::__construct($id, $username, $name, $email, $phone, 'quan_tri');
    }

    public function coQuyenQuanTri(): bool {
        return true;
    }
}
