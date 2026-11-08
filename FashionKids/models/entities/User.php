<?php
/**
 * Lớp trừu tượng Người dùng (models/entities/User.php)
 * Hiện thực hóa tính TRỪU TƯỢNG (Abstraction) & ĐÓNG GÓI (Encapsulation)
 */

abstract class User {
    protected int $ma_nguoi_dung;
    protected string $ten_dang_nhap;
    protected string $ho_va_ten;
    protected string $thu_dien_tu;
    protected string $so_dien_thoai;
    protected string $vai_tro;

    public function __construct(
        int $id = 0,
        string $username = '',
        string $name = '',
        string $email = '',
        string $phone = '',
        string $role = 'khach_hang'
    ) {
        $this->ma_nguoi_dung = $id;
        $this->ten_dang_nhap = $username;
        $this->ho_va_ten     = $name;
        $this->thu_dien_tu   = $email;
        $this->so_dien_thoai = $phone;
        $this->vai_tro       = $role;
    }

    public function getMaNguoiDung(): int { return $this->ma_nguoi_dung; }
    public function getTenDangNhap(): string { return $this->ten_dang_nhap; }
    public function getHoVaTen(): string { return $this->ho_va_ten; }
    public function getThuDienTu(): string { return $this->thu_dien_tu; }
    public function getSoDienThoai(): string { return $this->so_dien_thoai; }
    public function getVaiTro(): string { return $this->vai_tro; }

    // Phương thức kiểm tra quyền hạn đa hình
    abstract public function coQuyenQuanTri(): bool;
}
