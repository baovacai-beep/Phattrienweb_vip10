<?php
/**
 * Lớp Chi Tiết Dòng Hóa Đơn (models/entities/ChiTietDonHang.php)
 * Đóng gói thông tin từng sản phẩm được chốt mua (Trần Gia Bảo - tgb_)
 */

class ChiTietDonHang {
    private int $maChiTietDon;
    private int $maDonHang;
    private int $maSanPham;
    private string $kichCoChon;
    private int $soLuong;
    private float $donGiaMua;

    public function __construct(
        int $maChiTietDon = 0,
        int $maDonHang = 0,
        int $maSanPham = 0,
        string $kichCoChon = '',
        int $soLuong = 1,
        float $donGiaMua = 0.0
    ) {
        $this->maChiTietDon = $maChiTietDon;
        $this->maDonHang    = $maDonHang;
        $this->maSanPham    = $maSanPham;
        $this->kichCoChon   = $kichCoChon;
        $this->soLuong      = $soLuong;
        $this->donGiaMua    = $donGiaMua;
    }

    public function getMaChiTietDon(): int { return $this->maChiTietDon; }
    public function getMaDonHang(): int { return $this->maDonHang; }
    public function getMaSanPham(): int { return $this->maSanPham; }
    public function getKichCoChon(): string { return $this->kichCoChon; }
    public function getSoLuong(): int { return $this->soLuong; }
    public function getDonGiaMua(): float { return $this->donGiaMua; }

    /**
     * Tính thành tiền của dòng chi tiết
     */
    public function tinhThanhTien(): float {
        return $this->soLuong * $this->donGiaMua;
    }
}
