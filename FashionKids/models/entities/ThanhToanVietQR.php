<?php
/**
 * Lớp Thanh toán VietQR Napas 247 (models/entities/ThanhToanVietQR.php)
 * Hiện thực hóa interface IPhuongThucThanhToan - Sinh mã QR thanh toán động
 */

require_once __DIR__ . '/../interfaces/IPhuongThucThanhToan.php';

class ThanhToanVietQR implements IPhuongThucThanhToan {
    private string $bankId = 'MB';
    private string $accountNo = '0901234567';

    public function thucHienThanhToan(int $maDonHang, float $soTien): array {
        $noiDung = "FK" . $maDonHang;
        $qrUrl = "https://img.vietqr.io/image/{$this->bankId}-{$this->accountNo}-compact2.png?amount={$soTien}&addInfo=" . urlencode($noiDung);

        return [
            'phuong_thuc' => 'VietQR',
            'trang_thai'  => 'cho_chuyen_khoan',
            'qr_image'    => $qrUrl,
            'thong_tin'   => "Quét mã để thanh toán tự động qua tài khoản ngân hàng MB Bank (Napas 247)."
        ];
    }
}
