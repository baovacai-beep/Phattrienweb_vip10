<?php
/**
 * Lớp Controller Danh Mục & Quản Lý Tồn Kho (controllers/CatalogController.php)
 * Phụ trách nhánh: feature/lbn-catalog-stock (Lê Bảo Ngọc - lbn_)
 * Đảm nhiệm điều phối Danh mục sản phẩm, Lọc sản phẩm đa tiêu chí AJAX và Kiểm tra tồn kho.
 */

require_once __DIR__ . '/ProductController.php';

class CatalogController extends ProductController {

    public function index(): void {
        $this->catalog();
    }
}
