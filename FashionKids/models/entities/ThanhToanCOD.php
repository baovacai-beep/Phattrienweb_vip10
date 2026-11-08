<?php
/**
 * Lớp Thanh toán COD khi nhận hàng (models/entities/ThanhToanCOD.php)
 * Hiện thực hóa interface IPhuongThucThanhToan
 */

require_once __DIR__ . '/../interfaces/IPhuongThucThanhToan.php';

class ThanhToanCOD implements IPhuongThucThanhToan {
    public function thucHienThanhToan(int $maDonHang, float $soTien): array {
        return [
            'phuong_thuc' => 'COD',
            'trang_thai'  => 'chua_thanh_toan',
            'qr_image'    => null,
            'thong_tin'   => "Thanh toán bằng tiền mặt khi nhận hàng tại nhà."
        ];
    }
}
