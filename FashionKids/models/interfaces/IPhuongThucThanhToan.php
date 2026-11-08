<?php
/**
 * Interface Phương thức thanh toán (models/interfaces/IPhuongThucThanhToan.php)
 * Hiện thực hóa tính TRỪU TƯỢNG (Abstraction) trong OOP:
 * Trừu tượng hóa cổng thanh toán độc lập; tầng nghiệp vụ chỉ cần gọi thucHienThanhToan().
 */

interface IPhuongThucThanhToan {
    /**
     * Thực thi quy trình thanh toán cho đơn hàng
     *
     * @param int $maDonHang Mã hóa đơn phát sinh
     * @param float $soTien Tổng số tiền thực thanh toán sau chiết khấu
     * @return array Kết quả thanh toán kèm trạng thái và thông điệp hiển thị
     */
    public function thucHienThanhToan(int $maDonHang, float $soTien): array;
}
