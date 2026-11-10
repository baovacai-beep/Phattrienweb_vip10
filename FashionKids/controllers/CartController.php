<?php
/**
 * Lớp Controller Giỏ Hàng, Đặt Hàng AJAX & Đánh Giá (controllers/CartController.php)
 * Phụ trách nhánh: feature/tgb-checkout-ajax (Trần Gia Bảo - tgb_)
 * Đảm nhiệm điều phối Giỏ hàng, Thanh toán AJAX (sp_tgb_tao_don_hang_va_chi_tiet) và Đánh giá 5 sao.
 */

require_once __DIR__ . '/OrderController.php';

class CartController extends OrderController {

    public function index(): void {
        $this->checkout();
    }
}
