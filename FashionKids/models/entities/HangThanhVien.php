<?php
/**
 * Thực thể Hạng Thành Viên VIP (models/entities/HangThanhVien.php)
 * Đảm nhiệm: Phân cấp ưu đãi Đồng, Bạc, Vàng, Kim Cương (Hồ Gia Bảo - hgb_)
 */

class HangThanhVien {
    private int $maHang;
    private string $tenHang;
    private int $diemToiThieu;
    private float $phanTramGiamGia;
    private string $moTaQuyenLoi;

    public function __construct(
        int $maHang = 0,
        string $tenHang = '',
        int $diemToiThieu = 0,
        float $phanTramGiamGia = 0.0,
        string $moTaQuyenLoi = ''
    ) {
        $this->maHang          = $maHang;
        $this->tenHang         = $tenHang;
        $this->diemToiThieu    = $diemToiThieu;
        $this->phanTramGiamGia = $phanTramGiamGia;
        $this->moTaQuyenLoi    = $moTaQuyenLoi;
    }

    public function getMaHang(): int { return $this->maHang; }
    public function getTenHang(): string { return $this->tenHang; }
    public function getDiemToiThieu(): int { return $this->diemToiThieu; }
    public function getPhanTramGiamGia(): float { return $this->phanTramGiamGia; }
    public function getMoTaQuyenLoi(): string { return $this->moTaQuyenLoi; }

    public function setMaHang(int $val): void { $this->maHang = $val; }
    public function setTenHang(string $val): void { $this->tenHang = $val; }
    public function setDiemToiThieu(int $val): void { $this->diemToiThieu = $val; }
    public function setPhanTramGiamGia(float $val): void { $this->phanTramGiamGia = $val; }
    public function setMoTaQuyenLoi(string $val): void { $this->moTaQuyenLoi = $val; }

    /**
     * Kiểm tra người dùng có đủ điểm để thăng lên hạng này hay không
     */
    public function kiemTraThangHang(int $diemHienTai): bool {
        return $diemHienTai >= $this->diemToiThieu;
    }

    /**
     * Lấy mức giảm giá tương ứng
     */
    public function layPhanTramGiamGia(): float {
        return $this->phanTramGiamGia;
    }
}
