<?php
/**
 * Lớp AdminController (controllers/AdminController.php)
 * Phân hệ Quản trị viên (vai trò quan_tri) – RBAC được ép ở 2 lớp:
 *   1. View   : Rbac::enforceCurrentViewAccess() ở đầu mỗi trang.
 *   2. AJAX   : $this->guardAjax('<quyền>') ở đầu mỗi action (RBAC + POST + CSRF + ob_clean).
 *
 * Màn hình : MH 12 dashboard, MH 13 inventory, MH 14 sizeCharts, MH 15 users, MH 21/22 cmsBlog, MH 23 banners
 * AJAX     : ajaxSaveProduct / ajaxUpdateInventoryStock (ủy quyền ProductController),
 *            ajaxSaveVipTiers, ajaxSaveSizeChart, ajaxDeleteSizeChart,
 *            ajaxDecideKol, ajaxPublishPost, ajaxDeletePost
 * Phản hồi : JSON { "status": "success|error", "message": "..." } (kèm code/data của Controller::jsonResponse)
 */

require_once __DIR__ . '/../core/Controller.php';

class AdminController extends Controller {
    private object $productModel;
    private object $sizeChartModel;
    private ?AdminModel $adminModel = null;

    public function __construct() {
        $this->productModel    = $this->model('ProductModel');
        $this->sizeChartModel = $this->model('SizeChartModel');
    }

    private function admin(): AdminModel {
        if ($this->adminModel === null) {
            $this->adminModel = $this->model('AdminModel');
        }
        return $this->adminModel;
    }

    // ====================================================================== Trang (View tự gọi Rbac)

    /** Bảng điều khiển Quản trị viên (MH 12) */
    public function dashboard(): void {
        $this->view('admin-dashboard', ['page_title' => 'Bảng Điều Khiển Quản Trị Viên']);
    }

    /** Quản lý Kho hàng & Tồn kho (MH 13) – Admin + Staff */
    public function inventory(): void {
        Rbac::enforceCurrentViewAccess(__DIR__ . '/../views/pages/admin-inventory.php');
        $products = $this->productModel->getAllProducts();
        $this->view('admin-inventory', [
            'page_title' => 'Quản Trị Kho Hàng & Tồn Kho (MH 13)',
            'products'   => $products
        ]);
    }

    /** Quản lý Bảng quy chuẩn kích cỡ (MH 14) */
    public function sizeCharts(): void {
        Rbac::enforceCurrentViewAccess(__DIR__ . '/../views/pages/admin-size-charts.php');
        $this->view('admin-size-charts', [
            'page_title'  => 'Quản Trị Bảng Quy Chuẩn Kích Cỡ (MH 14)',
            'size_charts' => $this->sizeChartModel->getAllCharts()
        ]);
    }

    /** Người dùng & cấu hình hạng VIP (MH 15) */
    public function users(): void {
        $this->view('admin-users', ['page_title' => 'Quản Trị Người Dùng & Hạng VIP (MH 15)']);
    }

    /** Banner & chiến dịch (MH 23) */
    public function banners(): void {
        $this->view('admin-banners', ['page_title' => 'Quản Trị Banner (MH 23)']);
    }

    /** Tạp chí / CMS (MH 21/22) – Admin + Staff (nút quyền cao chỉ hiện với Admin) */
    public function cmsBlog(): void {
        $this->view('admin-cms-blog', ['page_title' => 'Quản Trị Tạp Chí (MH 21/22)']);
    }

    // ====================================================================== AJAX – MH 13 (Inventory)

    /** AJAX cũ: cập nhật tồn kho theo biến động (chỉ Admin vì có thể giảm kho) */
    public function ajaxUpdateStock(): void {
        $this->guardAjax('product.manage');

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $qtyChange = filter_input(INPUT_POST, 'qty_change', FILTER_VALIDATE_INT);
        if (!$productId || $qtyChange === false || $qtyChange === null) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!'], 400);
        }
        $ok = $this->productModel->updateStock($productId, $qtyChange);
        if ($ok) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Cập nhật tồn kho thành công!']);
        }
        $this->jsonResponse(['status' => 'error', 'message' => 'Cập nhật tồn kho thất bại hoặc tồn kho không đủ!'], 400);
    }

    /** AJAX chỉnh tồn kho (set/add) – ProductController tự kiểm tra quyền & ràng buộc theo vai trò */
    public function ajaxUpdateInventoryStock(): void {
        require_once __DIR__ . '/ProductController.php';
        (new ProductController())->ajaxUpdateInventoryStock();
    }

    /**
     * AJAX thêm mới / sửa sản phẩm: ủy quyền ProductController::ajaxSaveProduct
     * (gọi sp_lbn_them_san_pham_moi, bắt lỗi giá bán <= 0 và tồn kho < 0).
     */
    public function ajaxSaveProduct(): void {
        require_once __DIR__ . '/ProductController.php';
        (new ProductController())->ajaxSaveProduct();
    }

    // ====================================================================== AJAX – MH 15 (VIP)

    /** Lưu cấu hình điểm sàn & % chiết khấu các hạng thẻ (hgb_hang_thanh_vien) */
    public function ajaxSaveVipTiers(): void {
        $this->guardAjax('vip.config');
        $tiers = $_POST['tiers'] ?? [];
        if (!is_array($tiers)) {
            $tiers = [];
        }
        $this->runJson(function () use ($tiers) {
            $n = $this->admin()->luuHangThanhVien($tiers);
            return [
                'updated' => $n,
                'message' => $n > 0 ? "Đã lưu cấu hình hạng thành viên ($n hạng thay đổi)." : 'Không có thay đổi nào cần lưu.',
            ];
        });
    }

    // ====================================================================== AJAX – MH 14 (Size)

    public function ajaxSaveSizeChart(): void {
        $this->guardAjax('size.config');
        $post = $_POST;
        $this->runJson(function () use ($post) {
            $isEdit = (int)($post['ma_kich_co'] ?? 0) > 0;
            $id = $this->admin()->luuBangKichCo($post);
            return [
                'ma_kich_co' => $id,
                'is_edit'    => $isEdit,
                'message'    => $isEdit ? "Đã cập nhật cấu hình size #$id." : "Đã thêm mới cấu hình size #$id.",
            ];
        });
    }

    public function ajaxDeleteSizeChart(): void {
        $this->guardAjax('size.config');
        $id = (int)($_POST['ma_kich_co'] ?? 0);
        $this->runJson(function () use ($id) {
            $this->admin()->xoaBangKichCo($id);
            return ['ma_kich_co' => $id, 'message' => "Đã xóa cấu hình size #$id."];
        });
    }

    // ====================================================================== AJAX – MH 17 (KOL nhí)

    /** Quyết định Trúng tuyển / Từ chối – quyền cao nhất, chỉ Admin (sp_lnkt_duyet_ho_so_kol_nhi) */
    public function ajaxDecideKol(): void {
        $this->guardAjax('kol.decide');
        $id       = (int)($_POST['ma_ho_so'] ?? 0);
        $decision = (string)($_POST['decision'] ?? '');
        $this->runJson(function () use ($id, $decision) {
            $final = $this->admin()->quyetDinhKol($id, Rbac::userId(), $decision);
            return [
                'ma_ho_so'  => $id,
                'trang_thai' => $final,
                'message'   => $final === 'trung_tuyen' ? "Đã quyết định TRÚNG TUYỂN hồ sơ #$id." : "Đã TỪ CHỐI hồ sơ #$id.",
            ];
        });
    }

    // ====================================================================== AJAX – MH 21/22 (Tạp chí)

    public function ajaxPublishPost(): void {
        $this->guardAjax('blog.publish');
        $id = (int)($_POST['ma_bai_viet'] ?? 0);
        $this->runJson(function () use ($id) {
            $this->admin()->xuatBanBaiViet($id);
            return ['ma_bai_viet' => $id, 'message' => "Đã xuất bản bài viết #$id."];
        });
    }

    public function ajaxDeletePost(): void {
        $this->guardAjax('blog.delete');
        $id = (int)($_POST['ma_bai_viet'] ?? 0);
        $this->runJson(function () use ($id) {
            $this->admin()->xoaBaiViet($id);
            return ['ma_bai_viet' => $id, 'message' => "Đã xóa bài viết #$id."];
        });
    }
}
