<?php
/**
 * Lớp AdminController (controllers/AdminController.php)
 * Điều hướng Tổng quan báo cáo (MH 12), Quản trị kho (MH 13) và Quy chuẩn Size (MH 15)
 */

require_once __DIR__ . '/../core/Controller.php';

class AdminController extends Controller {
    private object $productModel;
    private object $sizeChartModel;

    public function __construct() {
        $this->productModel    = $this->model('ProductModel');
        $this->sizeChartModel = $this->model('SizeChartModel');
    }

    /**
     * Bảng điều khiển Quản trị viên (MH 12)
     */
    public function dashboard(): void {
        $this->view('admin-dashboard', [
            'page_title' => 'Bảng Điều Khiển Quản Trị Viên'
        ]);
    }

    /**
     * Quản lý Kho hàng & Tồn kho (MH 13)
     */
    public function inventory(): void {
        $products = $this->productModel->getAllProducts();

        $this->view('admin-inventory', [
            'page_title' => 'Quản Trị Kho Hàng & Tồn Kho (MH 13)',
            'products'   => $products
        ]);
    }

    /**
     * Quản lý Bảng quy chuẩn kích cỡ (MH 15)
     */
    public function sizeCharts(): void {
        $charts = $this->sizeChartModel->getAllCharts();

        $this->view('admin-size-charts', [
            'page_title'  => 'Quản Trị Bảng Quy Chuẩn Kích Cỡ (MH 15)',
            'size_charts' => $charts
        ]);
    }

    /**
     * AJAX Cập nhật tồn kho (triệu gọi lbn_update_stock)
     */
    public function ajaxUpdateStock(): void {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $qtyChange = filter_input(INPUT_POST, 'qty_change', FILTER_VALIDATE_INT);

        if (!$productId || $qtyChange === false) {
            $this->json(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!'], 400);
            return;
        }

        $ok = $this->productModel->updateStock($productId, $qtyChange);

        if ($ok) {
            $this->json(['status' => 'success', 'message' => 'Cập nhật tồn kho thành công!']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Cập nhật tồn kho thất bại hoặc tồn kho không đủ!'], 400);
        }
    }

    /**
     * AJAX Cập nhật tồn kho chi tiết & cảnh báo <= 5 (Giai đoạn 4 - Lê Bảo Ngọc)
     */
    public function ajaxUpdateInventoryStock(): void {
        require_once __DIR__ . '/ProductController.php';
        $productCtrl = new ProductController();
        $productCtrl->ajaxUpdateInventoryStock();
    }

    /**
     * AJAX Thêm mới / Cập nhật sản phẩm kho hàng (Giai đoạn 4 - Lê Bảo Ngọc)
     */
    public function ajaxSaveProduct(): void {
        require_once __DIR__ . '/ProductController.php';
        $productCtrl = new ProductController();
        $productCtrl->ajaxSaveProduct();
    }
}
