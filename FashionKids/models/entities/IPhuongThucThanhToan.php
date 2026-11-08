<?php
/**
 * Interface Phương thức thanh toán (models/entities/IPhuongThucThanhToan.php)
 * Đa hình xử lý thanh toán: COD, Chuyển khoản VNPAY, Ví điện tử MoMo.
 */

interface IPhuongThucThanhToan {
    /**
     * Thực thi thanh toán số tiền đơn hàng
     *
     * @param float $soTien Số tiền cần thanh toán
     * @return bool Trạng thái thành công
     */
    public function xuLyThanhToan(float $soTien): bool;
}

class ThanhToanCOD implements IPhuongThucThanhToan {
    public function xuLyThanhToan(float $soTien): bool {
        // Ghi nhận hình thức thanh toán khi nhận hàng
        return true;
    }
}

class ThanhToanVNPAY implements IPhuongThucThanhToan {
    public function xuLyThanhToan(float $soTien): bool {
        // Xử lý cổng VNPAY
        return $soTien > 0;
    }
}
