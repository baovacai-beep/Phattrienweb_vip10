<?php
/**
 * Lớp AdminController (controllers/AdminController.php)
 * Điều hướng Tổng quan báo cáo (MH 12), Quản trị kho (MH 13) và Quy chuẩn Size (MH 14)
 * Tích hợp kiểm soát truy cập RBAC:
 *   - Admin (quan_tri): Toàn quyền tất cả các trang và CRUD sản phẩm
 *   - Staff (nhan_vien): Được phép xem kho & nhập thêm tồn kho (sp_lbn_nhap_them_kho_hang)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Rbac.php';

class AdminController extends Controller {
    private object $productModel;
    private object $sizeChartModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        Rbac::syncSession();
        $this->productModel   = $this->model('ProductModel');
        $this->sizeChartModel = $this->model('SizeChartModel');
    }

    /**
     * Bảng điều khiển Quản trị viên (MH 12) - Chỉ Admin
     */
    public function dashboard(): void {
        if (!Rbac::isAdmin()) {
            $this->redirect(APP_BASE_URL . '/views/pages/admin-dashboard.php');
        }
        $this->view('admin-dashboard', [
            'page_title' => 'Bảng Điều Khiển Quản Trị Viên'
        ]);
    }

    /**
     * Quản lý Kho hàng & Tồn kho (MH 13) - Admin & Nhân viên
     */
    public function inventory(): void {
        if (!Rbac::isStaffOrAdmin()) {
            $this->redirect(APP_BASE_URL . '/views/pages/admin-inventory.php');
        }
        $products = $this->productModel->getAllProducts();

        $this->view('admin-inventory', [
            'page_title' => 'Quản Trị Kho Hàng & Tồn Kho (MH 13)',
            'products'   => $products
        ]);
    }

    /**
     * Quản lý Bảng quy chuẩn kích cỡ (MH 14) - Chỉ Admin
     */
    public function sizeCharts(): void {
        if (!Rbac::isAdmin()) {
            $this->redirect(APP_BASE_URL . '/views/pages/admin-size-charts.php');
        }
        $charts = $this->sizeChartModel->getAllCharts();

        $this->view('admin-size-charts', [
            'page_title'  => 'Quản Trị Bảng Quy Chuẩn Kích Cỡ (MH 14)',
            'size_charts' => $charts
        ]);
    }

    /**
     * AJAX Cập nhật tồn kho (triệu gọi sp_lbn_nhap_them_kho_hang) - Cho phép Staff & Admin
     */
    public function ajaxUpdateStock(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Truy cập bị từ chối (403): Chỉ Nhân viên kho và Quản trị viên mới được phép nhập kho (sp_lbn_nhap_them_kho_hang)!'
        );

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
     * AJAX Cập nhật tồn kho chi tiết & cảnh báo <= 5 (Giai đoạn 4 - Lê Bảo Ngọc) - Cho phép Staff & Admin
     */
    public function ajaxUpdateInventoryStock(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Truy cập bị từ chối (403): Chỉ Nhân viên kho và Quản trị viên mới được phép cập nhật tồn kho!'
        );
        require_once __DIR__ . '/ProductController.php';
        $productCtrl = new ProductController();
        $productCtrl->ajaxUpdateInventoryStock();
    }

    /**
     * AJAX Thêm mới / Cập nhật sản phẩm kho hàng (sp_lbn_them_san_pham_moi) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxSaveProduct(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Truy cập bị từ chối (403): Theo phân quyền RBAC, Nhân viên chỉ được nhập thêm tồn kho. Quyền Thêm/Sửa sản phẩm mới (sp_lbn_them_san_pham_moi) chỉ dành cho Quản trị viên!'
        );
        require_once __DIR__ . '/ProductController.php';
        $productCtrl = new ProductController();
        $productCtrl->ajaxSaveProduct();
    }
}
