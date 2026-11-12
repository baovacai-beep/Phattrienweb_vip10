<?php
/**
 * Lớp AdminController (controllers/AdminController.php)
 * Điều hướng Tổng quan báo cáo (MH 12), Quản trị kho (MH 13), Quy chuẩn Size (MH 14),
 * Quản lý Người dùng & Hạng VIP (MH 15), Xét duyệt Chung khảo KOL Nhí (MH 17),
 * Quản trị Xuất bản/Xóa Tạp chí (MH 21/22) và Quản lý Banner Sự kiện (MH 23).
 *
 * Tích hợp kiểm soát truy cập RBAC:
 *   - Admin (quan_tri): Toàn quyền tất cả các trang cấu hình và thao tác cấp cao nhất
 *   - Staff (nhan_vien): Bị chặn HTTP 403 Forbidden nếu cố truy cập MH 12, MH 14, MH 15, MH 23
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';
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
     * Bảng điều khiển Quản trị viên (MH 12) - Chỉ Admin (Trả về HTTP 403 nếu là nhan_vien)
     */
    public function dashboard(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-dashboard.php')) {
            http_response_code(403);
        }
        $this->view('admin-dashboard', [
            'page_title' => 'Bảng Điều Khiển Quản Trị Viên'
        ]);
    }

    /**
     * AJAX Lấy dữ liệu thống kê KPI, đơn hàng & cảnh báo tồn kho cho Dashboard Admin (MH 12)
     */
    public function ajaxDashboardStats(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới được phép truy cập số liệu tổng quan Dashboard.'
        );
        require_once __DIR__ . '/../models/OrderModel.php';
        $orderModel = new OrderModel();
        $dashData   = $orderModel->getAdminDashboardData();
        $this->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $dashData,
            'message'=> 'Đã đồng bộ dữ liệu KPI từ cơ sở dữ liệu.'
        ], 200);
    }

    /**
     * Quản lý Kho hàng & Tồn kho (MH 13) - Admin & Nhân viên
     */
    public function inventory(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-inventory.php')) {
            http_response_code(403);
        }
        $products = $this->productModel->getAllProducts();

        $this->view('admin-inventory', [
            'page_title' => 'Quản Trị Kho Hàng & Tồn Kho',
            'products'   => $products
        ]);
    }

    /**
     * Quản lý Bảng quy chuẩn kích cỡ (MH 14) - Chỉ Admin (Trả về HTTP 403 nếu là nhan_vien)
     */
    public function sizeCharts(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-size-charts.php')) {
            http_response_code(403);
        }
        $charts = $this->sizeChartModel->getAllCharts();

        $this->view('admin-size-charts', [
            'page_title'  => 'Quản Trị Bảng Quy Chuẩn Kích Cỡ',
            'size_charts' => $charts
        ]);
    }

    /**
     * Quản lý Người dùng & Cấu hình Hạng VIP (MH 15) - Chỉ Admin (Trả về HTTP 403 nếu là nhan_vien)
     */
    public function users(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-users.php')) {
            http_response_code(403);
        }
        $this->view('admin-users', [
            'page_title' => 'Quản Lý Tài Khoản & Phân Hạng Thành Viên VIP'
        ]);
    }

    /**
     * Quản lý Banner & Lịch sự kiện (MH 23) - Chỉ Admin (Trả về HTTP 403 nếu là nhan_vien)
     */
    public function banners(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-banners.php')) {
            http_response_code(403);
        }
        $this->view('admin-banners', [
            'page_title' => 'Quản Lý Banner Chiến Dịch & Lịch Sự Kiện'
        ]);
    }

    /**
     * Quản trị Tạp chí & Điểm tin báo chí (MH 21/22) - Admin & Nhân viên
     */
    public function cmsBlog(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-cms-blog.php')) {
            http_response_code(403);
        }
        $this->view('admin-cms-blog', [
            'page_title' => 'Quản Trị Nội Dung Tạp Chí & Báo Chí'
        ]);
    }

    /**
     * AJAX Cập nhật tồn kho (sp_lbn_nhap_them_kho_hang) - Cho phép Staff & Admin
     */
    public function ajaxUpdateStock(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên kho và Quản trị viên mới được phép nhập kho.'
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
     * AJAX Cập nhật tồn kho chi tiết & cảnh báo <= 5 (MH 13 - sp_lbn_nhap_them_kho_hang) - Cho phép Staff & Admin
     */
    public function ajaxUpdateInventoryStock(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên kho và Quản trị viên mới được phép cập nhật tồn kho.'
        );
        require_once __DIR__ . '/ProductController.php';
        $productCtrl = new ProductController();
        $productCtrl->ajaxUpdateInventoryStock();
    }

    /**
     * AJAX Thêm mới / Cập nhật sản phẩm kho hàng (MH 13 - sp_lbn_them_san_pham_moi) - CHỈ ADMIN (quan_tri)
     * Bắt lỗi ngoại lệ nếu giá bán <= 0 hoặc tồn kho < 0, đóng con trỏ $stmt->closeCursor() tránh lỗi PDO 2014
     */
    public function ajaxSaveProduct(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Nhân viên chỉ được phép nhập thêm tồn kho. Quyền thêm hoặc chỉnh sửa sản phẩm mới chỉ dành cho Quản trị viên.'
        );

        try {
            $rawPrice = trim((string)($_POST['price'] ?? $_POST['gia_ban'] ?? ''));
            $rawStock = trim((string)($_POST['stock_quantity'] ?? $_POST['so_luong_ton_kho'] ?? ''));

            if ($rawPrice === '' || !is_numeric($rawPrice) || (float)$rawPrice <= 0) {
                throw new InvalidArgumentException('Đơn giá bán sản phẩm phải lớn hơn 0 VNĐ (gia_ban > 0)!');
            }
            if ($rawStock === '' || !is_numeric($rawStock) || (int)$rawStock < 0) {
                throw new InvalidArgumentException('Số lượng tồn kho ban đầu không được nhỏ hơn 0 (so_luong_ton_kho >= 0)!');
            }

            require_once __DIR__ . '/ProductController.php';
            $productCtrl = new ProductController();
            $productCtrl->ajaxSaveProduct();
        } catch (Throwable $e) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $this->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Alias gọi thêm sản phẩm mới qua sp_lbn_them_san_pham_moi (MH 13)
     */
    public function ajaxAddProduct(): void {
        $this->ajaxSaveProduct();
    }

    /**
     * AJAX Lưu cấu hình Bảng kích cỡ chuẩn (MH 14 - lnkt_bang_kich_co) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxSaveSizeChart(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới được phép cấu hình bảng quy chuẩn kích cỡ (lnkt_bang_kich_co).'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'Phương thức không hợp lệ (Chỉ chấp nhận POST).'], 405);
        }

        try {
            $saved = $this->sizeChartModel->luuCauHinhSizeChart($_POST);
            $this->json([
                'status'     => 'success',
                'size_chart' => $saved,
                'message'    => 'Đã lưu cấu hình kích cỡ "' . $saved['size_label'] . '" (' . $saved['min_val'] . ' - ' . $saved['max_val'] . ' cm) vào bảng lnkt_bang_kich_co thành công!'
            ]);
        } catch (Throwable $e) {
            $this->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * AJAX Khóa / Mở khóa tài khoản người dùng (MH 15 - hgb_nguoi_dung) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxToggleUserStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới được phép thay đổi trạng thái tài khoản.'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'Phương thức không hợp lệ.'], 405);
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $status = isset($_POST['status']) ? (int)$_POST['status'] : -1;
        if ($userId <= 0 || !in_array($status, [0, 1], true)) {
            $this->json(['status' => 'error', 'message' => 'Thông tin tài khoản không hợp lệ.'], 400);
        }

        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        $ok = $userModel->capNhatTrangThaiNguoiDung($userId, $status);
        if ($ok) {
            $this->json([
                'status'     => 'success',
                'user_id'    => $userId,
                'new_status' => $status,
                'message'    => $status === 1 ? 'Đã mở khóa tài khoản thành công.' : 'Đã tạm khóa tài khoản thành công.'
            ]);
        } else {
            $this->json(['status' => 'error', 'message' => 'Không thể khóa tài khoản Quản trị viên.'], 400);
        }
    }

    /**
     * AJAX Lưu cấu hình mức điểm tối thiểu và phần trăm giảm giá VIP (MH 15 - hgb_hang_thanh_vien) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxSaveVipTiers(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới được phép cấu hình hạng thẻ thành viên.'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'Phương thức không hợp lệ.'], 405);
        }

        $tiersInput = $_POST['tiers'] ?? [];
        if (!is_array($tiersInput) || empty($tiersInput)) {
            $this->json(['status' => 'error', 'message' => 'Dữ liệu cấu hình hạng thẻ không hợp lệ.'], 400);
        }

        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        $saved = $userModel->luuCauHinhHangThanhVien($tiersInput);
        $this->json([
            'status'  => 'success',
            'tiers'   => $saved,
            'message' => 'Đã lưu cấu hình mức điểm tối thiểu và mức chiết khấu VIP thành công!'
        ]);
    }

    /**
     * AJAX Quyết định Trúng tuyển / Từ chối hồ sơ KOL Nhí (MH 17 - sp_lnkt_duyet_ho_so_kol_nhi) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxReviewKolCasting(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Quyết định Trúng tuyển hoặc Từ chối hồ sơ KOL Nhí là thẩm quyền tối cao của Quản trị viên!'
        );

        $id        = (int)($_POST['id'] ?? $_POST['ma_ho_so'] ?? 0);
        $newStatus = trim((string)($_POST['status'] ?? $_POST['trang_thai_moi'] ?? 'trung_tuyen'));
        $adminId   = (int)(Rbac::currentUserId() ?? 1);

        if ($id <= 0 || !in_array($newStatus, ['cho_duyet', 'moi_phong_van', 'trung_tuyen', 'tu_choi'], true)) {
            $this->json(['status' => 'error', 'message' => 'Thông tin hồ sơ KOL hoặc trạng thái phê duyệt không hợp lệ!'], 400);
        }

        require_once __DIR__ . '/../models/RealtimeContentModel.php';
        $rtModel = new RealtimeContentModel();
        $res     = $rtModel->reviewKolApplication($id, $newStatus, $adminId);

        $labelMap = [
            'cho_duyet'     => 'Chờ xét duyệt',
            'moi_phong_van' => 'Hẹn thử đồ Studio',
            'trung_tuyen'   => 'Quyết định Trúng tuyển KOL Nhí',
            'tu_choi'       => 'Từ chối hồ sơ'
        ];

        $this->json([
            'status'     => 'success',
            'id'         => $res['id'],
            'new_status' => $res['new_status'],
            'counts'     => $res['counts'],
            'routine'    => 'sp_lnkt_duyet_ho_so_kol_nhi',
            'message'    => 'Đã phê duyệt hồ sơ #' . $id . ': ' . ($labelMap[$newStatus] ?? $newStatus)
        ]);
    }

    /**
     * AJAX Xuất bản bài viết Tạp chí (MH 21/22 - tgb_bai_viet_tin_tuc) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxPublishBlogArticle(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới có thẩm quyền xuất bản chính thức bài viết Tạp chí!'
        );

        $articleId = (int)($_POST['article_id'] ?? $_POST['id'] ?? 0);
        if ($articleId <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã bài viết cần xuất bản không hợp lệ!'], 400);
        }

        require_once __DIR__ . '/../models/RealtimeContentModel.php';
        $rtModel = new RealtimeContentModel();
        $res     = $rtModel->publishArticle($articleId);

        $this->json([
            'status'     => 'success',
            'article_id' => $articleId,
            'article'    => $res,
            'message'    => 'Đã xuất bản chính thức bài viết #' . $articleId . ' lên Tạp chí FashionKids!'
        ]);
    }

    /**
     * AJAX Xóa bài viết Tạp chí (MH 21/22 - tgb_bai_viet_tin_tuc) - CHỈ ADMIN (quan_tri)
     */
    public function ajaxDeleteBlogArticle(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới có thẩm quyền xóa bài viết khỏi hệ thống Tạp chí!'
        );

        $articleId = (int)($_POST['article_id'] ?? $_POST['id'] ?? 0);
        if ($articleId <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã bài viết cần xóa không hợp lệ!'], 400);
        }

        require_once __DIR__ . '/../models/RealtimeContentModel.php';
        $rtModel = new RealtimeContentModel();
        $rtModel->deleteArticle($articleId);

        $this->json([
            'status'     => 'success',
            'article_id' => $articleId,
            'message'    => 'Đã xóa bài viết #' . $articleId . ' khỏi hệ thống Tạp chí thành công!'
        ]);
    }

    /**
     * AJAX Lấy danh sách yêu cầu hoàn hàng / trả hàng (MH 12 Tab Hoàn hàng) - Admin & Nhân viên
     */
    public function ajaxGetReturnRequests(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri', 'nhan_vien'],
            'Chỉ Quản trị viên và Nhân viên vận hành mới được phép xem danh sách hoàn hàng!'
        );

        require_once __DIR__ . '/OrderController.php';
        $orderCtrl = new OrderController();
        $orderCtrl->ajaxGetReturnRequests();
    }

    /**
     * AJAX Xử lý hoàn hàng / trả hàng (duyệt, từ chối, đang thu hồi, hoàn tất hoàn tiền & nhập kho)
     */
    public function ajaxProcessReturn(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri', 'nhan_vien'],
            'Chỉ Quản trị viên và Nhân viên vận hành mới được phép xử lý hoàn hàng!'
        );

        require_once __DIR__ . '/OrderController.php';
        $orderCtrl = new OrderController();
        $orderCtrl->ajaxProcessReturn();
    }
}
