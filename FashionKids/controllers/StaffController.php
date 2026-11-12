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
require_once __DIR__ . '/../core/ShippingService.php';
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
     * Màn hình Bàn Làm Việc CSKH & Tra cứu tồn kho nhanh (MH 11)
     */
    public function support(): void {
        if (!Rbac::enforceCurrentViewAccess('staff-support.php')) {
            http_response_code(403);
        }
        $this->view('staff-support', [
            'page_title' => 'Bàn Tư Vấn & CSKH Trực Tuyến (Nhân Viên)'
        ]);
    }

    public function chat(): void {
        $this->support();
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
     * MH 10 (Orders): AJAX Đối soát thanh toán VietQR qua Casso cho 1 đơn hàng
     * URL: index.php?c=staff&a=ajaxVerifyOrderPayment
     */
    public function ajaxVerifyOrderPayment(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép đối soát giao dịch ngân hàng đơn hàng!'
        );

        $data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? $_GET['order_id'] ?? 0);
        if ($orderId <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã đơn hàng cần đối soát không hợp lệ!'], 400);
        }

        try {
            $staffId = (int)(Rbac::currentUserId() ?? 0);
            $res     = $this->orderModel->checkAndReconcileVietQr($orderId, $staffId, true, true);
            $counts  = $this->orderModel->getOrderStatusCounts();
            $this->json([
                'status'       => 'success',
                'paid'         => !empty($res['paid']),
                'check_status' => $res['status'] ?? 'pending',
                'tx_key'       => $res['tx_key'] ?? null,
                'amount'       => $res['amount'] ?? ($res['received_amount'] ?? 0),
                'shortage'     => $res['shortage'] ?? 0,
                'order'        => $res['order'] ?? null,
                'counts'       => $counts,
                'message'      => (string)($res['message'] ?? 'Đã hoàn tất đối soát giao dịch Casso.')
            ], 200);
        } catch (Throwable $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * MH 10 (Orders): AJAX Đồng bộ nền toàn bộ đơn VietQR đang chờ chuyển khoản qua Casso
     * URL: index.php?c=staff&a=ajaxSyncPendingPayments
     */
    public function ajaxSyncPendingPayments(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép đồng bộ giao dịch chờ thanh toán!'
        );

        try {
            $syncRes  = $this->orderModel->reconcileAllPendingVietQrFromCasso(false);
            $pollData = $this->orderModel->getNewOrdersSince(0);
            $this->json([
                'status'            => 'success',
                'confirmed_ids'     => $syncRes['confirmed_ids'] ?? [],
                'expired_count'     => (int)($syncRes['expired_count'] ?? 0),
                'api_error'         => $syncRes['api_error'] ?? null,
                'pending_sync_rows' => $pollData['pending_sync_rows'] ?? [],
                'counts'            => $pollData['counts'] ?? [],
                'message'           => !empty($syncRes['confirmed_ids'])
                    ? ('Vừa đối soát thành công ' . count($syncRes['confirmed_ids']) . ' đơn chuyển khoản MB Bank!')
                    : 'Đã đồng bộ dữ liệu thanh toán.'
            ], 200);
        } catch (Throwable $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * MH 10 & MH 12: AJAX Tạo dữ liệu Tem/Phiếu Giao Hàng & Hóa Đơn Thu Hộ (COD = 0 đồng nếu đã TT)
     * URL: index.php?c=staff&a=ajaxGetShippingLabel&order_id=...
     */
    public function ajaxGetShippingLabel(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép xuất phiếu giao hàng & mã vận đơn!'
        );

        $orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã đơn hàng không hợp lệ!'], 400);
        }

        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng #' . $orderId . '!'], 404);
        }

        $isPaid      = !empty($order['is_paid']);
        $statusKey   = (string)($order['trang_thai_don_hang'] ?? 'cho_duyet');
        $payMethod   = (string)($order['phuong_thuc_thanh_toan'] ?? 'cod');
        $isCancelled = ($statusKey === 'da_huy');
        $isExpired   = !empty($order['is_payment_expired']);
        $totalAmount = (float)($order['tong_tien_thanh_toan'] ?? 0);
        $codCollect  = ($isPaid || $isCancelled) ? 0.0 : $totalAmount;

        $baseUrl     = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';
        $scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host        = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $trackingUrl = $scheme . '://' . $host . $baseUrl . '/views/pages/order-tracking.php?code=' . urlencode((string)$order['ma_van_don']);
        $qrTrackImg  = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&margin=4&data=' . rawurlencode($trackingUrl);
        $barcodeCode = 'FKORD' . str_pad((string)$orderId, 6, '0', STR_PAD_LEFT);

        if ($isPaid) {
            $codCollectText = '0 đồng';
            $codInstruction = ($payMethod === 'vietqr')
                ? ('ĐÃ THANH TOÁN CHUYỂN KHOẢN MB BANK' . (!empty($order['ma_giao_dich_ngan_hang']) ? (' (Mã GD: ' . $order['ma_giao_dich_ngan_hang'] . ')') : '') . ' — Bưu tá KHÔNG thu thêm tiền hàng.')
                : 'ĐÃ THANH TOÁN HOÀN TẤT — Bưu tá KHÔNG thu thêm tiền hàng.';
        } elseif ($isCancelled) {
            $codCollectText = '0 đồng (Đơn đã hủy)';
            $codInstruction = $isExpired
                ? 'Đơn đã tự động hủy do quá hạn thanh toán 24 giờ — KHÔNG xuất kho giao hàng.'
                : 'Đơn hàng đã hủy — KHÔNG xuất kho giao hàng.';
        } elseif ($payMethod === 'vietqr') {
            $codCollectText = number_format($totalAmount, 0, ',', '.') . ' đồng (CHỜ CHUYỂN KHOẢN)';
            $codInstruction = 'Khách chọn VietQR nhưng CHƯA chuyển khoản đủ — Chờ đối soát Casso hoặc đổi sang COD trước khi bàn giao bưu tá.';
        } else {
            $codCollectText = number_format($totalAmount, 0, ',', '.') . ' đồng';
            $codInstruction = 'THANH TOÁN TIỀN MẶT KHI NHẬN HÀNG (COD) — Bưu tá thu đúng ' . number_format($totalAmount, 0, ',', '.') . ' đồng từ người nhận (được đồng kiểm).';
        }

        $fullAddr = trim(implode(', ', array_filter([
            $order['dia_chi_giao_hang'] ?? '',
            $order['phuong_xa'] ?? '',
            $order['quan_huyen'] ?? '',
            $order['tinh_thanh_pho'] ?? '',
        ])));

        $this->json([
            'status' => 'success',
            'order'  => $order,
            'label'  => [
                'order_id'            => $orderId,
                'ma_van_don'          => (string)$order['ma_van_don'],
                'barcode_value'       => $barcodeCode,
                'tracking_url'        => $trackingUrl,
                'qr_tracking_img'     => $qrTrackImg,
                'created_at_label'    => !empty($order['ngay_dat_hang']) ? date('d/m/Y H:i', strtotime((string)$order['ngay_dat_hang'])) : date('d/m/Y H:i'),
                'printed_at_label'    => date('d/m/Y H:i'),
                'status_code'         => $statusKey,
                'status_label'        => (string)($order['status_meta']['label'] ?? 'Chờ duyệt'),
                'handled_by_name'     => (string)($order['nhan_vien_phu_trach'] ?? ($_SESSION['user']['ho_va_ten'] ?? 'Nhân viên FashionKids')),
                'sender'              => [
                    'name'    => 'FASHIONKIDS OFFICIAL',
                    'hotline' => '1900 6868',
                    'address' => 'Kho Vận Hành Thời Trang Sợi Tre FashionKids, TP. Hồ Chí Minh',
                ],
                'recipient'           => [
                    'name'         => (string)($order['ten_nguoi_nhan'] ?? ''),
                    'phone'        => (string)($order['so_dien_thoai_nhan'] ?? ''),
                    'full_address' => $fullAddr,
                    'province'     => (string)($order['tinh_thanh_pho'] ?? ''),
                    'district'     => (string)($order['quan_huyen'] ?? ''),
                    'note'         => (string)($order['ghi_chu'] ?? ''),
                ],
                'payment_method'      => $payMethod,
                'payment_method_name' => $payMethod === 'vietqr' ? 'Chuyển khoản VietQR MB Bank' : 'Tiền mặt khi nhận hàng (COD)',
                'payment_status'      => (string)($order['trang_thai_thanh_toan'] ?? 'chua_thanh_toan'),
                'is_paid'             => $isPaid,
                'is_cancelled'        => $isCancelled,
                'is_expired'          => $isExpired,
                'tx_code'             => (string)($order['ma_giao_dich_ngan_hang'] ?? ''),
                'paid_at_label'       => !empty($order['thoi_gian_thanh_toan']) ? date('d/m/Y H:i', strtotime((string)$order['thoi_gian_thanh_toan'])) : '',
                'subtotal'              => (float)($order['tien_hang_tam_tinh'] ?? 0),
                'discount'              => (float)($order['tien_giam_gia'] ?? 0),
                'voucher_code'          => (string)($order['ma_voucher'] ?? ''),
                'voucher_discount'      => (float)($order['tien_giam_voucher'] ?? 0),
                'shipping_fee'          => (float)($order['phi_van_chuyen'] ?? 0),
                'carrier_name'          => (string)($order['don_vi_van_chuyen'] ?? 'GHN'),
                'carrier_tracking_code' => (string)($order['ma_van_don_hang_vc'] ?? ''),
                'total_amount'          => $totalAmount,
                'cod_collect_amount'  => $codCollect,
                'cod_collect_text'    => $codCollectText,
                'cod_instruction'     => $codInstruction,
                'items'               => $order['items'] ?? [],
            ],
            'message' => 'Đã tạo phiếu giao hàng & mã vạch vận đơn #' . $order['ma_van_don']
        ], 200);
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
            'message'      => 'Đã đồng bộ trạng thái đã đọc cho phiên hỗ trợ ' . $sessionId
        ]);
    }

    public function ajaxMarkSupportRead(): void {
        $this->ajaxMarkChatRead();
    }

    /**
     * MH 11 (CSKH): AJAX Gửi phản hồi tư vấn trực tuyến
     * URL: index.php?c=staff&a=ajaxSendSupport
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

    public function ajaxSendSupport(): void {
        $this->ajaxSendChat();
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

    /**
     * 6. MH 10 (Hoàn hàng): AJAX Lấy danh sách yêu cầu hoàn trả & đếm theo trạng thái
     * URL: index.php?c=staff&a=ajaxGetReturnRequests
     */
    public function ajaxGetReturnRequests(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép xem danh sách yêu cầu hoàn hàng!'
        );

        require_once __DIR__ . '/OrderController.php';
        $orderCtrl = new OrderController();
        $orderCtrl->ajaxGetReturnRequests();
    }

    /**
     * 7. MH 10 (Hoàn hàng): AJAX Duyệt / Từ chối / Thu hồi / Hoàn tiền & Nhập kho yêu cầu hoàn hàng
     * URL: index.php?c=staff&a=ajaxProcessReturn
     */
    public function ajaxProcessReturn(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép xử lý yêu cầu hoàn hàng!'
        );

        require_once __DIR__ . '/OrderController.php';
        $orderCtrl = new OrderController();
        $orderCtrl->ajaxProcessReturn();
    }

    /**
     * 8. MH 10 (Vận chuyển GHN): AJAX Tạo vận đơn đối tác GHN & chuyển trạng thái Đang giao
     * URL: index.php?c=staff&a=ajaxCreateCarrierOrder
     */
    public function ajaxCreateCarrierOrder(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được tạo vận đơn GHN!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? 0);
        $staffId = (int)(Rbac::currentUserId() ?? 2);
        if ($orderId <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã đơn hàng không hợp lệ!'], 400);
        }

        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng #' . $orderId], 404);
        }

        $payMethod = strtolower((string)($order['phuong_thuc_thanh_toan'] ?? 'cod'));
        $payStatus = strtolower((string)($order['trang_thai_thanh_toan'] ?? 'chua_thanh_toan'));
        if ($payMethod === 'vietqr' && $payStatus !== 'da_thanh_toan') {
            $this->json([
                'status'  => 'error',
                'message' => 'Đơn VietQR #' . $orderId . ' chưa xác nhận nhận tiền chuyển khoản, không thể đẩy vận đơn GHN!'
            ], 400);
        }

        try {
            $shipRes = ShippingService::createCarrierOrder($order);
            $updated = $this->orderModel->updateCarrierTracking(
                $orderId,
                $staffId,
                (string)$shipRes['carrier_name'],
                (string)$shipRes['tracking_code']
            );
            $this->json([
                'status'        => 'success',
                'mode'          => $shipRes['mode'],
                'carrier_name'  => $shipRes['carrier_name'],
                'tracking_code' => $shipRes['tracking_code'],
                'order'         => $updated,
                'counts'        => $this->orderModel->getOrderStatusCounts(),
                'message'       => $shipRes['message'],
            ], 200);
        } catch (Throwable $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
