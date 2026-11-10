<?php
/**
 * Lớp Controller Tư Vấn Chọn Kích Cỡ & KOL Nhí (controllers/SizeController.php)
 * Phụ trách nhánh: feature/lnkt-order-size (Lê Nguyễn Khánh Trình - lnkt_)
 * Đảm nhiệm điều phối Gợi ý size thông minh qua fn_lnkt_goi_y_kich_co_chuan và Xét duyệt KOL nhí.
 */

require_once __DIR__ . '/ProductController.php';

class SizeController extends ProductController {

    public function index(): void {
        $this->ajaxSuggestSize();
    }
}
