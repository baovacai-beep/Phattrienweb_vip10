<?php
/**
 * Lớp StaffController (controllers/StaffController.php)
 * Điều phối toàn bộ nghiệp vụ của Nhân viên Vận hành (nhan_vien) & Quản trị viên (quan_tri):
 *  - MH 10 (staff-orders.php)         : Tiếp nhận đơn & cập nhật tiến trình giao hàng (sp_lnkt_cap_nhat_trang_thai_don_hang)
 *  - MH 11 (staff-chat.php)           : Live Chat CSKH 3 cột, đồng bộ tin nhắn đã đọc (tgb_danh_dau_tin_da_doc) & tra cứu nhanh tồn kho
 *  - MH 13 (admin-inventory.php)      : Chỉ cho phép Nhập thêm kho hàng (sp_lbn_nhap_them_kho_hang) đối với Nhân viên
 *  - MH 17 (staff-casting-review.php) : Sơ tuyển KOL Nhí vòng 1 ("Mời phỏng vấn" - moi_phong_van)
 *  - MH 21/22 (admin-cms-blog.php)    : Soạn thảo và lưu "Bản nháp" (ban_nhap) bài viết Tạp chí
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/RealtimeContentModel.php';

class StaffController extends Controller {
    private OrderModel $orderModel;
    private ProductModel $productModel;
    private RealtimeContentModel $realtimeModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        Rbac::syncSession();
        $this->orderModel    = new OrderModel();
        $this->productModel  = new ProductModel();
        $this->realtimeModel = new RealtimeContentModel();
    }

    /**
     * Màn hình Quản lý & Duyệt Đơn Hàng (MH 10)
     */
    public function orders(): void {
        if (!Rbac::enforceCurrentViewAccess('staff-orders.php')) {
            http_response_code(403);
        }
        $this->view('staff-orders', [
            'page_title' => 'Quản Lý & Duyệt Đơn Hàng (Nhân Viên)'
        ]);
    }

    public function index(): void {
        $this->orders();
    }

    /**
     * Màn hình Live Chat CSKH 3 cột & Tra cứu tồn kho nhanh (MH 11)
     */
    public function chat(): void {
        if (!Rbac::enforceCurrentViewAccess('staff-chat.php')) {
            http_response_code(403);
        }
        $this->view('staff-chat', [
            'page_title' => 'Bàn Tư Vấn & CSKH Trực Tuyến (Nhân Viên)'
        ]);
    }

    /**
     * Màn hình Nhập kho sản phẩm (MH 13)
     */
    public function inventory(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-inventory.php')) {
            http_response_code(403);
        }
        $this->view('admin-inventory', [
            'page_title' => 'Quản Lý Sản Phẩm & Nhập Thêm Kho Hàng'
        ]);
    }

    /**
     * Màn hình Sơ tuyển Hồ sơ KOL Nhí Vòng 1 (MH 17)
     */
    public function castingReview(): void {
        if (!Rbac::enforceCurrentViewAccess('staff-casting-review.php')) {
            http_response_code(403);
        }
        $this->view('staff-casting-review', [
            'page_title' => 'Xét Duyệt Hồ Sơ Siêu Sao Nhí KOL 2026'
        ]);
    }

    /**
     * Màn hình Soạn Bản nháp Tạp chí (MH 21/22)
     */
    public function cmsBlog(): void {
        if (!Rbac::enforceCurrentViewAccess('admin-cms-blog.php')) {
            http_response_code(403);
        }
        $this->view('admin-cms-blog', [
            'page_title' => 'Bàn Soạn Thảo Bản Nháp Tạp Chí Nhân Viên'
        ]);
    }

    /**
     * 1. MH 10 (Orders): AJAX Tiếp nhận đơn & Cập nhật tiến trình giao hàng
     * Triệu gọi OrderController / sp_lnkt_cap_nhat_trang_thai_don_hang kèm $stmt->closeCursor()
     * URL: index.php?c=staff&a=ajaxUpdateOrderStatus
     */
    public function ajaxUpdateOrderStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép tiếp nhận và cập nhật tiến trình đơn hàng!'
        );

        require_once __DIR__ . '/OrderController.php';
        $orderCtrl = new OrderController();
        $orderCtrl->ajaxUpdateOrderStatus();
    }

    /**
     * MH 10 (Orders): AJAX Polling nhận thông báo đơn hàng mới mỗi 4 giây
     * URL: index.php?c=staff&a=ajaxCheckNewOrders
     */
    public function ajaxCheckNewOrders(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép theo dõi đơn hàng mới!'
        );

        require_once __DIR__ . '/OrderController.php';
        $orderCtrl = new OrderController();
        $orderCtrl->ajaxCheckNewOrders();
    }

    /**
     * 2. MH 11 (Live Chat): AJAX Đồng bộ trạng thái "Đã đọc" (tgb_danh_dau_tin_da_doc)
     * Sử dụng PDO Singleton từ core/Database.php và $stmt->closeCursor()
     * URL: index.php?c=staff&a=ajaxMarkChatRead
     */
    public function ajaxMarkChatRead(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên CSKH và Quản trị viên mới được phép đánh dấu tin nhắn đã đọc.'
        );

        $sessionId = trim((string)($_POST['session_id'] ?? $_GET['session_id'] ?? 'sess_guest_123456'));
        if ($sessionId === '') {
            $this->json(['status' => 'error', 'message' => 'Mã phiên hội thoại không hợp lệ!'], 400);
        }

        $updatedRows = $this->realtimeModel->markChatSessionAsRead($sessionId);

        $this->json([
            'status'       => 'success',
            'session_id'   => $sessionId,
            'updated_rows' => $updatedRows,
            'sessions'     => $this->realtimeModel->getChatSessions(),
            'messages'     => $this->realtimeModel->getChatMessages($sessionId),
            'routine'      => 'tgb_danh_dau_tin_da_doc',
            'message'      => 'Đã đồng bộ trạng thái đã đọc cho phiên chat ' . $sessionId
        ]);
    }

    /**
     * MH 11 (Live Chat): AJAX Gửi phản hồi tư vấn trực tuyến
     * URL: index.php?c=staff&a=ajaxSendChat
     */
    public function ajaxSendChat(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên tư vấn và Quản trị viên mới được gửi phản hồi CSKH.'
        );

        $sessionId = trim((string)($_POST['session_id'] ?? 'sess_guest_123456'));
        $message   = trim((string)($_POST['message'] ?? ''));

        if ($message === '') {
            $this->json(['status' => 'error', 'message' => 'Nội dung tin nhắn tư vấn không được để trống!'], 400);
        }

        $this->realtimeModel->markChatSessionAsRead($sessionId);
        $data = $this->realtimeModel->sendChatMessage($sessionId, 'nhan_vien', $message, false);

        $this->json([
            'status'   => 'success',
            'sent'     => $data['sent'],
            'messages' => $data['messages'],
            'sessions' => $this->realtimeModel->getChatSessions(),
            'message'  => 'Đã gửi tin nhắn tư vấn thành công!'
        ]);
    }

    /**
     * MH 11 (Live Chat): AJAX Tra cứu nhanh tồn kho sản phẩm tại cột phải
     * URL: index.php?c=staff&a=ajaxQuickStockLookup
     */
    public function ajaxQuickStockLookup(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên và Quản trị viên mới được phép tra cứu kho nội bộ.'
        );

        $keyword = trim((string)($_POST['keyword'] ?? $_GET['keyword'] ?? ''));
        $items   = $this->productModel->locSanPhamDaTieuChi([
            'keyword'   => $keyword,
            'min_price' => 0,
            'max_price' => 10000000
        ]);

        $this->json([
            'status'   => 'success',
            'keyword'  => $keyword,
            'count'    => count($items),
            'products' => $items,
            'message'  => 'Tìm thấy ' . count($items) . ' sản phẩm khớp từ khóa tra cứu.'
        ]);
    }

    /**
     * 3. MH 13 (Inventory): AJAX Nhập thêm kho hàng (sp_lbn_nhap_them_kho_hang)
     * Nhân viên được phép nhập thêm số lượng tồn kho, KHÔNG được thêm/sửa thông tin sản phẩm mới.
     * URL: index.php?c=staff&a=ajaxRestockInventory
     */
    public function ajaxRestockInventory(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên kho và Quản trị viên mới được phép nhập thêm kho hàng (sp_lbn_nhap_them_kho_hang).'
        );

        require_once __DIR__ . '/ProductController.php';
        $productCtrl = new ProductController();
        $productCtrl->ajaxUpdateInventoryStock();
    }

    /**
     * 4. MH 17 (KOL Casting): AJAX Sơ tuyển KOL Nhí Vòng 1 ("Mời phỏng vấn" - moi_phong_van)
     * Chặn nghiêm ngặt Nhân viên nếu cố tình gửi trạng thái trung_tuyen hoặc tu_choi (HTTP 403 Forbidden)
     * URL: index.php?c=staff&a=ajaxPreliminaryKolReview
     */
    public function ajaxPreliminaryKolReview(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép sơ tuyển hồ sơ KOL Nhí.'
        );

        $id        = (int)($_POST['id'] ?? $_POST['ma_ho_so'] ?? 0);
        $newStatus = trim((string)($_POST['status'] ?? $_POST['trang_thai_moi'] ?? 'moi_phong_van'));
        $staffId   = (int)(Rbac::currentUserId() ?? 2);

        if ($id <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã hồ sơ ứng viên KOL không hợp lệ!'], 400);
        }

        // Ràng buộc RBAC: Nhân viên chỉ được phép bấm "Mời phỏng vấn" (moi_phong_van)
        if (Rbac::isStaff() && in_array($newStatus, ['trung_tuyen', 'tu_choi'], true)) {
            $this->json([
                'status'  => 'error',
                'code'    => 'RBAC_FORBIDDEN',
                'message' => 'Nhân viên chỉ được phép sơ tuyển Vòng 1 ("Mời phỏng vấn"). Quyền Quyết định Trúng tuyển hoặc Từ chối thuộc về Quản trị viên!'
            ], 403);
        }

        $res = $this->realtimeModel->reviewKolApplication($id, $newStatus, $staffId);

        $this->json([
            'status'     => 'success',
            'id'         => $res['id'],
            'new_status' => $res['new_status'],
            'counts'     => $res['counts'],
            'routine'    => 'sp_lnkt_duyet_ho_so_kol_nhi',
            'message'    => 'Đã cập nhật sơ tuyển hồ sơ KOL #' . $id . ' sang trạng thái "Mời phỏng vấn Studio"!'
        ]);
    }

    /**
     * 5. MH 21/22 (CMS Blog): AJAX Lưu "Bản nháp" bài viết Tạp chí (ban_nhap)
     * Nhân viên chỉ được phép lưu ở trạng thái Bản nháp trình Admin duyệt xuất bản.
     * URL: index.php?c=staff&a=ajaxSaveBlogDraft
     */
    public function ajaxSaveBlogDraft(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên và Quản trị viên mới được phép lưu bài viết Tạp chí.'
        );

        $title = trim((string)($_POST['title'] ?? ''));
        if ($title === '') {
            $this->json(['status' => 'error', 'message' => 'Tiêu đề bài viết không được để trống!'], 400);
        }

        $payload = $_POST;
        if (Rbac::isStaff()) {
            $payload['status'] = 'ban_nhap';
        }

        $article = $this->realtimeModel->saveArticle($payload);
        $msg = Rbac::isStaff()
            ? 'Đã lưu bài viết ở trạng thái "Bản nháp" chờ Quản trị viên duyệt xuất bản!'
            : 'Đã lưu và xuất bản bài viết Tạp chí thành công!';

        $this->json([
            'status'  => 'success',
            'article' => $article,
            'message' => $msg
        ]);
    }
}
