<?php
/**
 * Lớp Khách Hàng (models/entities/KhachHang.php)
 * Kế thừa từ NguoiDung - Đóng gói hành vi mua hàng, tích điểm VIP và gửi OTP
 */

require_once __DIR__ . '/NguoiDung.php';
require_once __DIR__ . '/HangThanhVien.php';

class KhachHang extends NguoiDung {
    private int $diemTichLuy;
    private bool $daXacThucOtp;
    private ?HangThanhVien $hangThanhVien;

    public function __construct(
        int $maNguoiDung = 0,
        string $tenDangNhap = '',
        string $matKhau = '',
        string $hoVaTen = '',
        string $thuDienTu = '',
        string $soDienThoai = '',
        string $diaChiNha = '',
        int $diemTichLuy = 0,
        bool $daXacThucOtp = false,
        ?HangThanhVien $hangThanhVien = null
    ) {
        parent::__construct(
            $maNguoiDung, $tenDangNhap, $matKhau, $hoVaTen, $thuDienTu, $soDienThoai, $diaChiNha, 'khach_hang'
        );
        $this->diemTichLuy   = $diemTichLuy;
        $this->daXacThucOtp  = $daXacThucOtp;
        $this->hangThanhVien = $hangThanhVien;
    }

    public function getDiemTichLuy(): int { return $this->diemTichLuy; }
    public function isDaXacThucOtp(): bool { return $this->daXacThucOtp; }
    public function getHangThanhVien(): ?HangThanhVien { return $this->hangThanhVien; }

    public function setDiemTichLuy(int $val): void { $this->diemTichLuy = $val; }
    public function setDaXacThucOtp(bool $val): void { $this->daXacThucOtp = $val; }
    public function setHangThanhVien(?HangThanhVien $tier): void { $this->hangThanhVien = $tier; }

    /**
     * Xác thực mã OTP 6 số
     */
    public function xacThucOTP(string $maOtp): bool {
        // Trong môi trường demo chấp nhận mã 6 số hợp lệ
        if (strlen(trim($maOtp)) === 6 && ctype_digit($maOtp)) {
            $this->daXacThucOtp = true;
            return true;
        }
        return false;
    }

    /**
     * Cộng dồn điểm thưởng sau mỗi đơn hàng (10.000 VNĐ = 1 điểm)
     */
    public function tichLuyDiem(int $diemCong): void {
        if ($diemCong > 0) {
            $this->diemTichLuy += $diemCong;
        }
    }

    /**
     * Lấy tỷ lệ chiết khấu theo hạng thẻ VIP
     */
    public function layPhanTramGiamGia(): float {
        return $this->hangThanhVien ? $this->hangThanhVien->layPhanTramGiamGia() : 0.0;
    }

    /**
     * Đặt đơn hàng mới
     */
    public function datDonHang(object $donHang): bool {
        return true;
    }

    /**
     * Gửi đánh giá phản hồi sản phẩm
     */
    public function vietDanhGia(object $danhGia): bool {
        return true;
    }

    /**
     * Nộp hồ sơ ứng tuyển KOL Nhí FashionKids
     */
    public function nopHoSoKOL(object $hoSo): bool {
        return true;
    }
}
