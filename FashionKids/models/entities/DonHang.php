<?php
/**
 * Lớp Đơn Hàng (models/entities/DonHang.php)
 * Đóng gói quy trình tạo đơn, tính giảm giá VIP và xác nhận hoàn tất (Lê Nguyễn Khánh Trình - lnkt_)
 */

require_once __DIR__ . '/ChiTietDonHang.php';

class DonHang {
    private int $maDonHang;
    private ?int $maNguoiDung;
    private ?int $maNhanVienXuLy;
    private float $tienHangTamTinh;
    private float $tienGiamGia;
    private float $tongTienThanhToan;
    private string $trangThaiDonHang;
    /** @var ChiTietDonHang[] */
    private array $danhSachChiTiet;

    public function __construct(
        int $maDonHang = 0,
        ?int $maNguoiDung = null,
        ?int $maNhanVienXuLy = null,
        float $tienHangTamTinh = 0.0,
        float $tienGiamGia = 0.0,
        float $tongTienThanhToan = 0.0,
        string $trangThaiDonHang = 'cho_xac_nhan',
        array $danhSachChiTiet = []
    ) {
        $this->maDonHang         = $maDonHang;
        $this->maNguoiDung       = $maNguoiDung;
        $this->maNhanVienXuLy    = $maNhanVienXuLy;
        $this->tienHangTamTinh   = $tienHangTamTinh;
        $this->tienGiamGia       = $tienGiamGia;
        $this->tongTienThanhToan = $tongTienThanhToan;
        $this->trangThaiDonHang  = $trangThaiDonHang;
        $this->danhSachChiTiet   = $danhSachChiTiet;
    }

    public function getMaDonHang(): int { return $this->maDonHang; }
    public function getMaNguoiDung(): ?int { return $this->maNguoiDung; }
    public function getMaNhanVienXuLy(): ?int { return $this->maNhanVienXuLy; }
    public function getTienHangTamTinh(): float { return $this->tienHangTamTinh; }
    public function getTienGiamGia(): float { return $this->tienGiamGia; }
    public function getTongTienThanhToan(): float { return $this->tongTienThanhToan; }
    public function getTrangThaiDonHang(): string { return $this->trangThaiDonHang; }
    public function getDanhSachChiTiet(): array { return $this->danhSachChiTiet; }

    public function setTrangThaiDonHang(string $val): void { $this->trangThaiDonHang = $val; }
    public function setMaNhanVienXuLy(?int $val): void { $this->maNhanVienXuLy = $val; }

    /**
     * Thêm một dòng chi tiết đơn hàng
     */
    public function themChiTiet(ChiTietDonHang $chiTiet): void {
        $this->danhSachChiTiet[] = $chiTiet;
    }

    /**
     * Tính toán tổng tiền thanh toán dựa trên chiết khấu VIP
     */
    public function tinhTongTien(float $phanTramVip = 0.0): void {
        $this->tienHangTamTinh = 0.0;
        foreach ($this->danhSachChiTiet as $item) {
            $this->tienHangTamTinh += $item->tinhThanhTien();
        }

        $this->tienGiamGia = ($this->tienHangTamTinh * $phanTramVip) / 100;
        $this->tongTienThanhToan = max(0, $this->tienHangTamTinh - $this->tienGiamGia);
    }

    /**
     * Xác nhận đơn hàng đã thanh toán và hoàn tất
     */
    public function xacNhanHoanTat(): void {
        $this->trangThaiDonHang = 'hoan_tat';
    }
}
