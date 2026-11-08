<?php
/**
 * Lớp trừu tượng Sản Phẩm (models/entities/SanPham.php)
 * Đóng gói dữ liệu nền tảng của hàng hóa thời trang trẻ em (Lê Bảo Ngọc - lbn_)
 */

abstract class SanPham {
    protected int $maSanPham;
    protected int $maDanhMuc;
    protected string $tenSanPham;
    protected float $giaBan;
    protected int $soLuongTonKho;
    protected string $duongDanAnh;
    protected string $moTaChatLieu;
    protected string $loaiKichCo;

    public function __construct(
        int $maSanPham = 0,
        int $maDanhMuc = 0,
        string $tenSanPham = '',
        float $giaBan = 0.0,
        int $soLuongTonKho = 0,
        string $duongDanAnh = '',
        string $moTaChatLieu = '',
        string $loaiKichCo = 'quan_ao'
    ) {
        $this->maSanPham      = $maSanPham;
        $this->maDanhMuc      = $maDanhMuc;
        $this->tenSanPham     = $tenSanPham;
        $this->giaBan         = $giaBan;
        $this->soLuongTonKho  = $soLuongTonKho;
        $this->duongDanAnh    = $duongDanAnh;
        $this->moTaChatLieu   = $moTaChatLieu;
        $this->loaiKichCo     = $loaiKichCo;
    }

    // Encapsulation: Getters
    public function getMaSanPham(): int { return $this->maSanPham; }
    public function getMaDanhMuc(): int { return $this->maDanhMuc; }
    public function getTenSanPham(): string { return $this->tenSanPham; }
    public function getGiaBan(): float { return $this->giaBan; }
    public function getSoLuongTonKho(): int { return $this->soLuongTonKho; }
    public function getDuongDanAnh(): string { return $this->duongDanAnh; }
    public function getMoTaChatLieu(): string { return $this->moTaChatLieu; }
    public function getLoaiKichCo(): string { return $this->loaiKichCo; }

    // Encapsulation: Setters
    public function setMaSanPham(int $val): void { $this->maSanPham = $val; }
    public function setMaDanhMuc(int $val): void { $this->maDanhMuc = $val; }
    public function setTenSanPham(string $val): void { $this->tenSanPham = $val; }
    public function setGiaBan(float $val): void { $this->giaBan = $val; }
    public function setSoLuongTonKho(int $val): void { $this->soLuongTonKho = $val; }
    public function setDuongDanAnh(string $val): void { $this->duongDanAnh = $val; }
    public function setMoTaChatLieu(string $val): void { $this->moTaChatLieu = $val; }
    public function setLoaiKichCo(string $val): void { $this->loaiKichCo = $val; }

    /**
     * Cập nhật số lượng tồn kho (tăng/giảm)
     */
    public function capNhatTonKho(int $soLuongThayDoi): bool {
        if ($this->soLuongTonKho + $soLuongThayDoi < 0) {
            return false;
        }
        $this->soLuongTonKho += $soLuongThayDoi;
        return true;
    }

    /**
     * Lấy giá niêm yết hiện tại
     */
    public function layGiaBanHienTai(): float {
        return $this->giaBan;
    }

    /**
     * Kiểm tra số lượng tồn kho có đủ cho khách đặt hàng
     */
    public function kiemTraConHang(int $soLuongMua = 1): bool {
        return $this->soLuongTonKho >= $soLuongMua;
    }
}
