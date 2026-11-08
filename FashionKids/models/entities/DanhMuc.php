<?php
/**
 * Lớp Danh Mục Sản Phẩm (models/entities/DanhMuc.php)
 * Đảm nhiệm: Phân loại thời trang trẻ em theo giới tính và nhóm hàng (Lê Bảo Ngọc - lbn_)
 */

class DanhMuc {
    private int $maDanhMuc;
    private string $tenDanhMuc;
    private string $gioiTinh;
    private string $loaiHang;

    public function __construct(
        int $maDanhMuc = 0,
        string $tenDanhMuc = '',
        string $gioiTinh = 'unisex',
        string $loaiHang = 'quan_ao'
    ) {
        $this->maDanhMuc  = $maDanhMuc;
        $this->tenDanhMuc = $tenDanhMuc;
        $this->gioiTinh   = $gioiTinh;
        $this->loaiHang   = $loaiHang;
    }

    public function getMaDanhMuc(): int { return $this->maDanhMuc; }
    public function getTenDanhMuc(): string { return $this->tenDanhMuc; }
    public function getGioiTinh(): string { return $this->gioiTinh; }
    public function getLoaiHang(): string { return $this->loaiHang; }

    public function setMaDanhMuc(int $val): void { $this->maDanhMuc = $val; }
    public function setTenDanhMuc(string $val): void { $this->tenDanhMuc = $val; }
    public function setGioiTinh(string $val): void { $this->gioiTinh = $val; }
    public function setLoaiHang(string $val): void { $this->loaiHang = $val; }
}
