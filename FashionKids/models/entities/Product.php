<?php
/**
 * Lớp trừu tượng Sản phẩm (models/entities/Product.php)
 * Hiện thực hóa tính ĐÓNG GÓI (Encapsulation) & Ràng buộc toàn vẹn dữ liệu
 */

abstract class Product {
    private int $ma_san_pham;
    private int $ma_danh_muc;
    private string $ten_san_pham;
    private float $gia_ban;
    private int $so_luong_ton_kho;
    private string $duong_dan_anh;
    private string $loai_kich_co;

    public function __construct(
        int $ma_san_pham = 0,
        int $ma_danh_muc = 0,
        string $ten_san_pham = '',
        float $gia_ban = 1000.0,
        int $so_luong_ton_kho = 0,
        string $duong_dan_anh = '',
        string $loai_kich_co = 'quan_ao'
    ) {
        $this->ma_san_pham  = $ma_san_pham;
        $this->ma_danh_muc  = $ma_danh_muc;
        $this->ten_san_pham = $ten_san_pham;
        $this->setGiaBan($gia_ban);
        $this->setSoLuongTonKho($so_luong_ton_kho);
        $this->duong_dan_anh = $duong_dan_anh;
        $this->loai_kich_co  = $loai_kich_co;
    }

    public function getMaSanPham(): int { return $this->ma_san_pham; }
    public function getMaDanhMuc(): int { return $this->ma_danh_muc; }
    public function getTenSanPham(): string { return $this->ten_san_pham; }
    public function getGiaBan(): float { return $this->gia_ban; }
    public function getSoLuongTonKho(): int { return $this->so_luong_ton_kho; }
    public function getDuongDanAnh(): string { return $this->duong_dan_anh; }
    public function getLoaiKichCo(): string { return $this->loai_kich_co; }

    public function setMaSanPham(int $val): void { $this->ma_san_pham = $val; }
    public function setMaDanhMuc(int $val): void { $this->ma_danh_muc = $val; }
    public function setTenSanPham(string $val): void { $this->ten_san_pham = $val; }
    public function setDuongDanAnh(string $val): void { $this->duong_dan_anh = $val; }
    public function setLoaiKichCo(string $val): void { $this->loai_kich_co = $val; }

    public function setGiaBan(float $gia_ban): void {
        if ($gia_ban <= 0) {
            throw new InvalidArgumentException("Đơn giá bán sản phẩm phải lớn hơn 0 VNĐ.");
        }
        $this->gia_ban = $gia_ban;
    }

    public function setSoLuongTonKho(int $so_luong): void {
        if ($so_luong < 0) {
            throw new InvalidArgumentException("Số lượng tồn kho không được là số âm.");
        }
        $this->so_luong_ton_kho = $so_luong;
    }
}
