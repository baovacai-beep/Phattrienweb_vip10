<?php
/**
 * Lớp Nhân Viên (models/entities/Staff.php)
 * Kế thừa User - Đóng gói quyền duyệt đơn hàng và hỗ trợ CSKH
 */

require_once __DIR__ . '/User.php';

class Staff extends User {
    public function __construct(
        int $id = 0,
        string $username = '',
        string $name = '',
        string $email = '',
        string $phone = ''
    ) {
        parent::__construct($id, $username, $name, $email, $phone, 'nhan_vien');
    }

    public function coQuyenQuanTri(): bool {
        return false;
    }

    public function coQuyenDuyetDon(): bool {
        return true;
    }
}
