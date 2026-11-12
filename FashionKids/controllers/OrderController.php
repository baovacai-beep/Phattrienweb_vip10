<?php
/**
 * Lớp OrderController (controllers/OrderController.php)
 * Điều phối Giỏ hàng, Checkout Gate (RBAC), Thanh toán & Theo dõi đơn hàng khép kín (MH 05, MH 06, MH 10)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../core/Mailer.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/entities/ThanhToanVietQR.php';
require_once __DIR__ . '/../models/entities/ThanhToanCOD.php';

class OrderController extends Controller {
    private OrderModel $orderModel;
    private ProductModel $productModel;
    private ReviewModel $reviewModel;
    private UserModel $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        Rbac::syncSession();
        $this->orderModel   = new OrderModel();
        $this->productModel = new ProductModel();
        $this->reviewModel  = new ReviewModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Đồng bộ giỏ hàng hiện tại với đơn giá, tồn kho thực trong DB và hạng VIP tự động từ DB
     */
    private function buildLiveCartSummary(): array {
        $sessCart = (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) ? $_SESSION['cart'] : [];
        $items    = [];
        $subtotal = 0.0;
        $totalQty = 0;

        foreach ($sessCart as $cartKey => $cItem) {
            $pid  = (int)($cItem['product_id'] ?? 0);
            if ($pid <= 0) {
                continue;
            }
            $qty  = max(1, (int)($cItem['quantity'] ?? 1));
            $size = (string)($cItem['size'] ?? 'Size 2 (2-3T)');

            $liveInfo = $this->productModel->kiemTraTonKhoChiTiet($pid, $qty);
            $unitPrice = (float)($liveInfo['price'] ?? ($cItem['price'] ?? 0));
            $stock     = (int)($liveInfo['available_stock'] ?? 0);
            $prodName  = (string)($liveInfo['product_name'] ?? ($cItem['product_name'] ?? 'Sản phẩm FashionKids'));
            $sku       = (string)($liveInfo['sku'] ?? ($cItem['sku'] ?? ('FK-LBN-' . $pid)));
            $image     = (string)($liveInfo['image'] ?? ($cItem['image'] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg'));

            // Đồng bộ ngược lại đơn giá chuẩn DB vào session cart
            $_SESSION['cart'][$cartKey]['price']        = $unitPrice;
            $_SESSION['cart'][$cartKey]['product_name'] = $prodName;
            $_SESSION['cart'][$cartKey]['sku']          = $sku;
            $_SESSION['cart'][$cartKey]['image']        = $image;

            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;
            $totalQty += $qty;

            $items[] = [
                'cart_key'   => (string)$cartKey,
                'product_id' => $pid,
                'sku'        => $sku,
                'name'       => $prodName,
                'image'      => $image,
                'size'       => $size,
                'price'      => $unitPrice,
                'quantity'   => $qty,
                'stock'      => $stock,
                'line_total' => $lineTotal,
            ];
        }

        $uid = Rbac::currentUserId();
        $vipInfo = $this->userModel->layThongTinVipHienTai($uid);
        $discountPercent = (float)($vipInfo['discount_percent'] ?? 0.0);
        $discountAmount  = round(($subtotal * $discountPercent) / 100.0, 0);
        $shippingFee     = 0.0;
        $totalAmount     = max(0.0, round($subtotal - $discountAmount, 0));
        $rewardPoints    = (int)floor($totalAmount / 10000);

        return [
            'items'            => $items,
            'line_count'       => count($items),
            'total_qty'        => $totalQty,
            'subtotal'         => $subtotal,
            'vip'              => $vipInfo,
            'discount_percent' => $discountPercent,
            'discount_amount'  => $discountAmount,
            'shipping_fee'     => $shippingFee,
            'total_amount'     => $totalAmount,
            'reward_points'    => $rewardPoints,
        ];
    }

    /**
     * Hiển thị màn hình Checkout (MH 05)
     */
    public function checkout(): void {
        $uid = Rbac::currentUserId();
        if ($uid) {
            $this->userModel->dongBoSessionNguoiDung($uid);
        }

        $cartSummary = $this->buildLiveCartSummary();

        $this->view('pages/checkout', [
            'page_title'  => 'Giỏ Hàng & Thanh Toán Đơn Hàng',
            'cartSummary' => $cartSummary,
            'user'        => $_SESSION['user'] ?? null,
        ]);
    }

    /**
     * AJAX Cập nhật số lượng 1 dòng trong Giỏ hàng & Tính lại tổng tiền từ Server
     * URL: index.php?c=order&a=ajaxUpdateCartItem
     */
    public function ajaxUpdateCartItem(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $cartKey = trim((string)($data['cart_key'] ?? ''));
        $qty     = (int)($data['quantity'] ?? 0);

        if ($cartKey === '' || empty($_SESSION['cart'][$cartKey])) {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng!'], 404);
        }
        if ($qty <= 0) {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Số lượng tối thiểu là 1 sản phẩm!'], 400);
        }

        $productId = (int)($_SESSION['cart'][$cartKey]['product_id'] ?? 0);
        $otherQty  = 0;
        foreach ($_SESSION['cart'] as $k => $item) {
            if ((string)$k !== $cartKey && (int)($item['product_id'] ?? 0) === $productId) {
                $otherQty += (int)($item['quantity'] ?? 0);
            }
        }

        $stockInfo = $this->productModel->kiemTraTonKhoChiTiet($productId, $otherQty + $qty);
        if (!$stockInfo['du_hang']) {
            $maxForLine = max(1, (int)$stockInfo['available_stock'] - $otherQty);
            $summary    = $this->buildLiveCartSummary();
            $this->jsonResponse([
                'status'          => 'error',
                'code'            => 409,
                'available_stock' => (int)$stockInfo['available_stock'],
                'max_for_line'    => $maxForLine,
                'cart_summary'    => $summary,
                'data'            => null,
                'message'         => 'Kho chỉ còn ' . $stockInfo['available_stock'] . ' sản phẩm "' . $stockInfo['product_name'] . '"!'
            ], 409);
        }

        $_SESSION['cart'][$cartKey]['quantity'] = $qty;
        $summary = $this->buildLiveCartSummary();

        $this->jsonResponse([
            'status'       => 'success',
            'code'         => 200,
            'cart_summary' => $summary,
            'cart_count'   => $summary['total_qty'],
            'data'         => $summary,
            'message'      => 'Đã cập nhật số lượng giỏ hàng!'
        ], 200);
    }

    /**
     * AJAX Xóa 1 dòng khỏi Giỏ hàng & Tính lại tổng tiền từ Server
     * URL: index.php?c=order&a=ajaxRemoveCartItem
     */
    public function ajaxRemoveCartItem(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $cartKey = trim((string)($data['cart_key'] ?? ''));

        if ($cartKey !== '' && isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
        }

        $summary = $this->buildLiveCartSummary();
        $this->jsonResponse([
            'status'       => 'success',
            'code'         => 200,
            'cart_summary' => $summary,
            'cart_count'   => $summary['total_qty'],
            'data'         => $summary,
            'message'      => 'Đã xóa sản phẩm khỏi giỏ hàng!'
        ], 200);
    }

    /**
     * Xử lý nộp đơn hàng qua Form POST truyền thống (fallback khi không dùng JS AJAX)
     */
    public function processCheckout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . APP_BASE_URL . "/index.php");
            exit;
        }

        if (Rbac::isGuest()) {
            $redirectUrl = APP_BASE_URL . '/views/pages/login-register.php?redirect=' . urlencode(APP_BASE_URL . '/views/pages/checkout.php') . '&error=' . urlencode('Vui lòng đăng nhập tài khoản thành viên để hoàn tất đặt hàng và tích điểm VIP (Giỏ hàng của bạn vẫn được giữ nguyên).');
            header("Location: " . $redirectUrl);
            exit;
        }

        $userId = Rbac::currentUserId();
        $items  = $this->resolveCheckoutItems($_POST);

        $payload = [
            'user_id'          => $userId,
            'receiver_name'    => trim((string)($_POST['ten_nguoi_nhan'] ?? '')),
            'receiver_phone'   => trim((string)($_POST['so_dien_thoai_nhan'] ?? '')),
            'receiver_email'   => trim((string)($_POST['thu_dien_tu_nhan'] ?? ($_SESSION['user']['thu_dien_tu'] ?? ''))),
            'shipping_address' => trim((string)($_POST['dia_chi_giao_hang'] ?? '')),
            'province'         => trim((string)($_POST['tinh_thanh_pho'] ?? '')),
            'district'         => trim((string)($_POST['quan_huyen'] ?? '')),
            'payment_method'   => strtolower(trim((string)($_POST['phuong_thuc_thanh_toan'] ?? 'cod'))),
            'note'             => trim((string)($_POST['ghi_chu'] ?? '')),
        ];

        try {
            $result = $this->orderModel->taoDonHangHoanChinh($payload, $items);

            // Làm sạch giỏ hàng & lưu thông tin đơn vừa đặt
            $_SESSION['cart'] = [];
            $_SESSION['recent_order'] = [
                'order_id'       => $result['ma_don_hang'],
                'ma_van_don'     => $result['ma_van_don'],
                'payment_result' => $result['payment_result'],
                'qr_url'         => $result['qr_url'],
            ];

            header("Location: " . APP_BASE_URL . "/index.php?c=order&a=success&order_id=" . $result['ma_don_hang']);
            exit;
        } catch (Throwable $e) {
            $_SESSION['checkout_flash_error'] = $e->getMessage();
            header("Location: " . APP_BASE_URL . "/views/pages/checkout.php");
            exit;
        }
    }

    /**
     * Trích xuất danh sách sản phẩm đặt hàng từ Session Cart (ưu tiên tuyệt đối, không tin giá client)
     */
    private function resolveCheckoutItems(array $requestData): array {
        $items = [];
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $cItem) {
                $pid = (int)($cItem['product_id'] ?? 0);
                $qty = (int)($cItem['quantity'] ?? 0);
                if ($pid > 0 && $qty > 0) {
                    $items[] = [
                        'product_id' => $pid,
                        'size'       => (string)($cItem['size'] ?? 'Size 2 (2-3T)'),
                        'quantity'   => $qty,
                    ];
                }
            }
        }

        // Chỉ fallback sang payload nếu session cart đang trống và caller truyền items / product_id tường minh
        if (empty($items)) {
            if (!empty($requestData['items']) && is_array($requestData['items'])) {
                foreach ($requestData['items'] as $rItem) {
                    $pid = (int)($rItem['product_id'] ?? $rItem['ma_san_pham'] ?? 0);
                    $qty = (int)($rItem['quantity'] ?? $rItem['so_luong'] ?? 0);
                    if ($pid > 0 && $qty > 0) {
                        $items[] = [
                            'product_id' => $pid,
                            'size'       => (string)($rItem['size'] ?? $rItem['kich_co_chon'] ?? 'Size 2 (2-3T)'),
                            'quantity'   => $qty,
                        ];
                    }
                }
            } elseif (!empty($requestData['product_id']) || !empty($requestData['ma_san_pham'])) {
                $pid = (int)($requestData['product_id'] ?? $requestData['ma_san_pham']);
                $qty = (int)($requestData['quantity'] ?? $requestData['so_luong'] ?? 1);
                if ($pid > 0 && $qty > 0) {
                    $items[] = [
                        'product_id' => $pid,
                        'size'       => (string)($requestData['size'] ?? $requestData['kich_co_chon'] ?? 'Size 2 (2-3T)'),
                        'quantity'   => $qty,
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * Màn hình thông báo hoàn tất và theo dõi tiến trình đơn hàng (MH 06)
     */
    public function success(): void {
        $uid = Rbac::currentUserId();
        if ($uid) {
            $this->userModel->dongBoSessionNguoiDung($uid);
        }

        $orderId = (int)($_GET['order_id'] ?? ($_GET['id'] ?? ($_SESSION['recent_order']['order_id'] ?? 0)));
        $code    = trim((string)($_GET['code'] ?? ''));

        $trackedOrder = null;
        if ($orderId > 0) {
            $trackedOrder = $this->orderModel->getOrderById($orderId);
        } elseif ($code !== '') {
            $trackedOrder = $this->orderModel->findOrderForTracking($code);
        } elseif ($uid) {
            $myOrders = $this->orderModel->getOrdersByUserId($uid, 1);
            if (!empty($myOrders[0]['ma_don_hang'])) {
                $trackedOrder = $this->orderModel->getOrderById((int)$myOrders[0]['ma_don_hang']);
            }
        }

        $this->view('pages/order-tracking', [
            'page_title'   => $trackedOrder
                ? ('Tra Cứu Vận Đơn - #' . $trackedOrder['ma_van_don'])
                : 'Theo Dõi Tiến Trình Đơn Hàng',
            'orderData'    => $_SESSION['recent_order'] ?? null,
            'trackedOrder' => $trackedOrder,
        ]);
    }

    /**
     * Alias route ?c=order&a=tracking&id={maDonHang} cho email thư mời đánh giá & theo dõi hoàn hàng
     */
    public function tracking(): void {
        $this->success();
    }

    /**
     * API AJAX Tạo đơn hàng bước 4 (Hỗ trợ cả COD & VietQR, Transaction ACID, chống gian lận giá/VIP)
     * URL: index.php?c=order&a=ajaxCreateOrder
     */
    public function ajaxCreateOrder(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'code' => 405, 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        // Checkout Gate: Khách vãng lai -> 401 giữ nguyên $_SESSION['cart']
        if (Rbac::isGuest()) {
            $this->jsonResponse([
                'status'   => 'error',
                'code'     => 401,
                'data'     => null,
                'redirect' => APP_BASE_URL . '/views/pages/login-register.php?redirect=' . urlencode(APP_BASE_URL . '/views/pages/checkout.php'),
                'message'  => 'Vui lòng đăng nhập tài khoản thành viên để hoàn tất đặt hàng và tích điểm VIP!'
            ], 401);
        }

        $data  = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $items = $this->resolveCheckoutItems($data);

        if (empty($items)) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'data'    => null,
                'message' => 'Giỏ hàng của bạn đang trống, vui lòng chọn sản phẩm trước khi đặt hàng!'
            ], 400);
        }

        $userId = Rbac::currentUserId();
        $payload = [
            'user_id'          => $userId,
            'receiver_name'    => trim((string)($data['receiver_name'] ?? $data['ten_nguoi_nhan'] ?? '')),
            'receiver_phone'   => trim((string)($data['receiver_phone'] ?? $data['so_dien_thoai_nhan'] ?? '')),
            'receiver_email'   => trim((string)($data['receiver_email'] ?? $data['thu_dien_tu_nhan'] ?? ($_SESSION['user']['thu_dien_tu'] ?? ''))),
            'shipping_address' => trim((string)($data['shipping_address'] ?? $data['dia_chi_giao_hang'] ?? '')),
            'province'         => trim((string)($data['province'] ?? $data['tinh_thanh_pho'] ?? '')),
            'district'         => trim((string)($data['district'] ?? $data['quan_huyen'] ?? '')),
            'payment_method'   => strtolower(trim((string)($data['payment_method'] ?? $data['phuong_thuc_thanh_toan'] ?? 'cod'))),
            'note'             => trim((string)($data['note'] ?? $data['ghi_chu'] ?? '')),
        ];

        try {
            $result = $this->orderModel->taoDonHangHoanChinh($payload, $items);

            $_SESSION['cart'] = [];
            $_SESSION['recent_order'] = [
                'order_id'       => $result['ma_don_hang'],
                'ma_van_don'     => $result['ma_van_don'],
                'payment_result' => $result['payment_result'],
                'qr_url'         => $result['qr_url'],
            ];

            $redirectUrl = APP_BASE_URL . '/index.php?c=order&a=success&order_id=' . $result['ma_don_hang'];

            $this->jsonResponse([
                'status'     => 'success',
                'code'       => 200,
                'order_id'   => $result['ma_don_hang'],
                'ma_van_don' => $result['ma_van_don'],
                'redirect'   => $redirectUrl,
                'cart_count' => 0,
                'data'       => [
                    'ma_don_hang'           => $result['ma_don_hang'],
                    'ma_van_don'            => $result['ma_van_don'],
                    'tien_hang_tam_tinh'    => $result['tien_hang_tam_tinh'],
                    'phan_tram_giam'        => $result['phan_tram_giam'],
                    'tien_giam_vip'         => $result['tien_giam_vip'],
                    'tong_tien'             => $result['tong_tien'],
                    'phuong_thuc'           => $result['phuong_thuc'],
                    'trang_thai_thanh_toan' => $result['trang_thai_thanh_toan'],
                    'han_thanh_toan'        => $result['han_thanh_toan'] ?? null,
                    'han_thanh_toan_ts'     => $result['han_thanh_toan_ts'] ?? 0,
                    'noi_dung_chuyen_khoan' => $result['noi_dung_chuyen_khoan'] ?? '',
                    'qr_url'                => $result['qr_url'],
                ],
                'message'    => 'Đặt hàng thành công! Mã đơn hàng #' . $result['ma_don_hang'] . ' đã được gửi tới bộ phận xử lý.'
            ], 200);
        } catch (Throwable $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'data'    => null,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Xây dựng payload chuẩn cho màn hình Theo dõi đơn hàng (MH 06)
     */
    private function buildTrackOrderPayload(array $order, ?array $checkResult = null): array {
        $currentUid = Rbac::currentUserId();
        $isOwner    = ($currentUid !== null && (int)($order['ma_nguoi_dung'] ?? 0) === $currentUid);
        $isStaffAdm = Rbac::isStaffOrAdmin();
        $statusCode = (string)($order['trang_thai_don_hang'] ?? 'cho_duyet');
        $payMethod  = (string)($order['phuong_thuc_thanh_toan'] ?? 'cod');
        $payStatus  = (string)($order['trang_thai_thanh_toan'] ?? 'chua_thanh_toan');
        $isExpired  = !empty($order['is_payment_expired']);
        $isPaid     = ($payStatus === 'da_thanh_toan');

        if ($isOwner && $currentUid) {
            $this->userModel->dongBoSessionNguoiDung($currentUid);
        }

        return [
            'order'              => $order,
            'is_owner'           => $isOwner,
            'can_cancel'         => ($isOwner && $statusCode === 'cho_duyet'),
            'can_confirm_qr'     => (($isOwner || $isStaffAdm) && $payMethod === 'vietqr' && !$isPaid && $statusCode === 'cho_duyet' && !$isExpired),
            'can_switch_payment' => (($isOwner || $isStaffAdm) && !$isPaid && $statusCode === 'cho_duyet' && !$isExpired),
            'can_review'         => ($isOwner && $statusCode === 'hoan_tat'),
            'return_request'     => $order['return_request'] ?? null,
            'can_request_return' => ($isOwner && $statusCode === 'hoan_tat' && empty($order['return_request'])),
            'check_result'       => $checkResult,
            'user_points'        => $currentUid ? (int)($_SESSION['user']['diem_tich_luy'] ?? 0) : null,
            'user_tier'          => $currentUid ? (string)($_SESSION['user']['ten_hang'] ?? '') : null,
        ];
    }

    /**
     * API AJAX Tra cứu vận đơn & Cập nhật trạng thái + Tự động đối soát Casso Realtime (MH 06)
     * URL: index.php?c=order&a=ajaxTrackOrder
     */
    public function ajaxTrackOrder(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        $orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
        $query   = trim((string)($_GET['q'] ?? $_POST['q'] ?? $_GET['code'] ?? ''));

        $order = null;
        if ($orderId > 0) {
            $order = $this->orderModel->getOrderById($orderId);
        } elseif ($query !== '') {
            $order = $this->orderModel->findOrderForTracking($query);
        }

        if (!$order) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 404,
                'data'    => null,
                'message' => 'Không tìm thấy đơn hàng khớp với mã vận đơn hoặc số điện thoại vừa nhập!'
            ], 404);
        }

        $checkResult = null;
        if (
            strtolower((string)($order['phuong_thuc_thanh_toan'] ?? 'cod')) === 'vietqr'
            && strtolower((string)($order['trang_thai_thanh_toan'] ?? 'chua_thanh_toan')) !== 'da_thanh_toan'
            && (string)($order['trang_thai_don_hang'] ?? 'cho_duyet') === 'cho_duyet'
        ) {
            $checkResult = $this->orderModel->checkAndReconcileVietQr(
                (int)$order['ma_don_hang'],
                (int)(Rbac::currentUserId() ?? 0),
                Rbac::isStaffOrAdmin(),
                false,
                null,
                true
            );
            if (!empty($checkResult['order'])) {
                $order = $checkResult['order'];
            }
        }

        $payload = $this->buildTrackOrderPayload($order, $checkResult);

        $this->jsonResponse([
            'status'  => 'success',
            'code'    => 200,
            'data'    => $payload,
            'message' => (!empty($checkResult['newly_paid']))
                ? (string)$checkResult['message']
                : ('Đã tải thông tin đơn hàng #' . $order['ma_don_hang'])
        ], 200);
    }

    /**
     * API AJAX Chủ đơn / Nhân viên chủ động bấm kiểm tra giao dịch Casso MB Bank ngay
     * URL: index.php?c=order&a=ajaxCheckVietQrPayment
     */
    public function ajaxCheckVietQrPayment(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập để kiểm tra trạng thái thanh toán đơn hàng!'
        );

        $data       = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId    = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? $_GET['order_id'] ?? 0);
        $manualSync = isset($data['manual']) ? (bool)$data['manual'] : (isset($_GET['manual']) ? (bool)$_GET['manual'] : true);
        $userId     = (int)(Rbac::currentUserId() ?? 0);

        if ($orderId <= 0) {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => 'Mã đơn hàng không hợp lệ!'], 400);
        }

        try {
            $res   = $this->orderModel->checkAndReconcileVietQr($orderId, $userId, Rbac::isStaffOrAdmin(), $manualSync);
            $order = !empty($res['order']) ? $res['order'] : ($this->orderModel->getOrderById($orderId) ?? []);
            $trackPayload = !empty($order) ? $this->buildTrackOrderPayload($order, $res) : null;

            $this->jsonResponse([
                'status'       => 'success',
                'code'         => 200,
                'paid'         => !empty($res['paid']),
                'check_status' => $res['status'] ?? 'pending',
                'tx_key'       => $res['tx_key'] ?? null,
                'amount'       => $res['amount'] ?? ($res['received_amount'] ?? 0),
                'shortage'     => $res['shortage'] ?? 0,
                'data'         => $trackPayload,
                'check_result' => $res,
                'message'      => (string)($res['message'] ?? 'Đã kiểm tra giao dịch ngân hàng.')
            ], 200);
        } catch (Throwable $e) {
            $code = (strpos($e->getMessage(), 'quyền') !== false) ? 403 : 400;
            $this->jsonResponse(['status' => 'error', 'code' => $code, 'data' => null, 'message' => $e->getMessage()], $code);
        }
    }

    /**
     * API AJAX Đổi phương thức thanh toán (VietQR <-> COD) khi đơn chưa thanh toán & đang 'cho_duyet'
     * URL: index.php?c=order&a=ajaxSwitchPaymentMethod
     */
    public function ajaxSwitchPaymentMethod(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập để thay đổi phương thức thanh toán!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'code' => 405, 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data      = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId   = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? 0);
        $newMethod = strtolower(trim((string)($data['payment_method'] ?? $data['phuong_thuc_thanh_toan'] ?? '')));
        $userId    = (int)(Rbac::currentUserId() ?? 0);

        if ($orderId <= 0 || !in_array($newMethod, ['cod', 'vietqr'], true)) {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => 'Thông tin đơn hàng hoặc phương thức thanh toán mới không hợp lệ!'], 400);
        }

        try {
            $order   = $this->orderModel->switchPaymentMethod($orderId, $userId, $newMethod, Rbac::isStaffOrAdmin());
            $payload = $this->buildTrackOrderPayload($order);
            $msg = ($newMethod === 'cod')
                ? ('Đã chuyển đơn #' . $orderId . ' sang hình thức Thanh toán tiền mặt khi nhận hàng (COD)!')
                : ('Đã chuyển đơn #' . $orderId . ' sang hình thức Quét mã VietQR MB Bank (hạn thanh toán 24 giờ)!');

            $this->jsonResponse([
                'status'  => 'success',
                'code'    => 200,
                'data'    => $payload,
                'message' => $msg
            ], 200);
        } catch (Throwable $e) {
            $code = (strpos($e->getMessage(), 'quyền') !== false) ? 403 : 400;
            $this->jsonResponse(['status' => 'error', 'code' => $code, 'data' => null, 'message' => $e->getMessage()], $code);
        }
    }

    /**
     * Webhook v2 nhận thông báo giao dịch tức thì từ Casso
     * URL: index.php?c=order&a=cassoWebhook
     */
    public function cassoWebhook(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $rawBody = (string)file_get_contents('php://input');
        $token   = (string)($_SERVER['HTTP_SECURE_TOKEN'] ?? $_SERVER['HTTP_X_SECURE_TOKEN'] ?? '');
        $casso   = $this->orderModel->getCassoService();

        if (!$casso->verifyWebhookToken($token)) {
            $this->jsonResponse([
                'error'   => 1,
                'message' => 'Invalid Secure-Token'
            ], 401);
        }

        try {
            $records = $casso->parseWebhookPayload($rawBody);
            $res     = $this->orderModel->reconcilePendingFromWebhook($records);
            $this->jsonResponse([
                'error'   => 0,
                'message' => 'ok',
                'data'    => $res
            ], 200);
        } catch (Throwable $e) {
            $this->jsonResponse([
                'error'   => 1,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API AJAX Xác nhận thanh toán VietQR (BẮT BUỘC đối soát giao dịch thật qua Casso, chặn đánh dấu mù)
     * URL: index.php?c=order&a=ajaxConfirmVietQr
     */
    public function ajaxConfirmVietQr(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập để xác nhận thanh toán đơn hàng!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? 0);
        $userId  = (int)(Rbac::currentUserId() ?? 0);

        try {
            $res = $this->orderModel->confirmVietQrPaid($orderId, $userId, Rbac::isStaffOrAdmin());
            $this->jsonResponse([
                'status'  => 'success',
                'code'    => 200,
                'data'    => $res,
                'message' => (string)($res['message'] ?? ('Đã đối soát thanh toán VietQR thành công cho đơn #' . $orderId . '!'))
            ], 200);
        } catch (Throwable $e) {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API AJAX Khách hàng tự hủy đơn hàng khi đang 'cho_duyet'
     * URL: index.php?c=order&a=ajaxCancelMyOrder
     */
    public function ajaxCancelMyOrder(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập để thực hiện hủy đơn hàng!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? 0);
        $userId  = (int)(Rbac::currentUserId() ?? 0);

        try {
            $res = $this->orderModel->cancelOrderByOwner($orderId, $userId);
            $this->jsonResponse([
                'status'  => 'success',
                'code'    => 200,
                'data'    => $res,
                'message' => 'Đã hủy đơn hàng #' . $orderId . ' và hoàn trả sản phẩm về kho thành công!'
            ], 200);
        } catch (Throwable $e) {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API AJAX Polling đơn hàng mới cho Cổng Nhân viên (4 giây/lần)
     * URL: index.php?c=order&a=ajaxCheckNewOrders
     */
    public function ajaxCheckNewOrders(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép theo dõi luồng đơn hàng mới!'
        );

        $lastId = (int)($_GET['last_id'] ?? $_POST['last_id'] ?? 0);
        $res    = $this->orderModel->getNewOrdersSince($lastId);

        $this->jsonResponse([
            'status'       => 'success',
            'code'         => 200,
            'new_orders'   => $res['new_orders'],
            'max_order_id' => $res['max_order_id'],
            'counts'       => $res['counts'],
            'data'         => $res,
            'message'      => count($res['new_orders']) > 0
                ? ('Có ' . count($res['new_orders']) . ' đơn hàng mới vừa được tạo!')
                : 'Hệ thống đơn hàng đã đồng bộ mới nhất.'
        ], 200);
    }

    /**
     * API AJAX Gửi đánh giá sản phẩm (Bắt buộc đơn hàng 'hoan_tat' thuộc về user qua sp_tgb_gui_danh_gia_san_pham)
     * URL: index.php?c=order&a=ajaxSubmitReview
     */
    public function ajaxSubmitReview(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập tài khoản thành viên để gửi đánh giá sản phẩm!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data      = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $productId = (int)($data['product_id'] ?? 0);
        $rating    = (int)($data['rating_score'] ?? 5);
        $content   = trim((string)($data['review_content'] ?? ''));
        $orderId   = (int)($data['order_id'] ?? 0);
        $userId    = (int)(Rbac::currentUserId() ?? 0);
        $userName  = (string)($_SESSION['user']['ho_va_ten'] ?? 'Phụ huynh thành viên');

        if ($productId <= 0) {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Không xác định được sản phẩm cần đánh giá.'], 422);
        }
        if ($rating < 1 || $rating > 5) {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Vui lòng chọn số sao đánh giá từ 1 đến 5 sao.'], 422);
        }
        if (mb_strlen($content, 'UTF-8') < 5) {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Vui lòng nhập cảm nhận tối thiểu 5 ký tự để chia sẻ cùng các phụ huynh khác.'], 422);
        }

        if ($this->reviewModel->hasConnection()) {
            // Nếu client không truyền order_id (VD từ trang PDP), tự tìm đơn 'hoan_tat' hợp lệ của user cho sản phẩm này
            if ($orderId <= 0) {
                $elig = $this->reviewModel->kiemTraQuyenDanhGia($userId, $productId);
                if (!empty($elig['eligible_order_id'])) {
                    $orderId = (int)$elig['eligible_order_id'];
                } elseif (!empty($elig['already_reviewed'])) {
                    $this->jsonResponse([
                        'status'  => 'error',
                        'code'    => 400,
                        'data'    => null,
                        'message' => 'Bạn đã gửi đánh giá cho sản phẩm này trong đơn hàng đã mua rồi!'
                    ], 400);
                } else {
                    $this->jsonResponse([
                        'status'  => 'error',
                        'code'    => 400,
                        'data'    => null,
                        'message' => 'Chỉ khách hàng có đơn hàng đã Hoàn tất chứa sản phẩm này mới được phép gửi đánh giá!'
                    ], 400);
                }
            }

            try {
                $this->reviewModel->guiDanhGiaSanPham($productId, $userId, $orderId, $rating, $content);
            } catch (Throwable $e) {
                $this->jsonResponse([
                    'status'  => 'error',
                    'code'    => 400,
                    'data'    => null,
                    'message' => $e->getMessage()
                ], 400);
            }
        }

        $avgRating   = $this->reviewModel->tinhSaoTrungBinh($productId);
        $reviewCount = count($this->reviewModel->layDanhGiaTheoSanPham($productId));
        $buyerCount  = $this->reviewModel->demSoNguoiMua($productId);
        $newReview = [
            'id'          => time(),
            'product_id'  => $productId,
            'order_id'    => $orderId,
            'author_name' => $userName,
            'rating'      => $rating,
            'content'     => $content,
            'date'        => date('d/m/Y H:i'),
            'verified'    => true,
        ];

        $this->jsonResponse([
            'status'       => 'success',
            'code'         => 200,
            'message'      => 'Cảm ơn ba mẹ đã gửi đánh giá sản phẩm thành công!',
            'review'       => $newReview,
            'avg_rating'   => $avgRating,
            'review_count' => $reviewCount,
            'buyer_count'  => $buyerCount,
            'data'         => [
                'review'       => $newReview,
                'avg_rating'   => $avgRating,
                'review_count' => $reviewCount,
                'buyer_count'  => $buyerCount,
            ],
        ], 200);
    }

    /**
     * API AJAX Tiếp nhận đơn & Cập nhật tiến trình giao hàng (MH 10 - sp_lnkt_cap_nhat_trang_thai_don_hang)
     * URL: index.php?c=order&a=ajaxUpdateOrderStatus
     * RBAC: Chỉ Nhân viên vận hành (nhan_vien) và Quản trị viên (quan_tri)
     */
    public function ajaxUpdateOrderStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép cập nhật tiến trình đơn hàng!'
        );

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'data' => null, 'message' => 'Phương thức không hợp lệ (Chỉ chấp nhận POST)!'], 405);
        }

        $data      = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId   = (int)($data['order_id'] ?? $data['ma_don_hang'] ?? 0);
        $newStatus = trim((string)($data['new_status'] ?? $data['trang_thai_moi'] ?? $data['status'] ?? ''));
        $staffId   = (int)(Rbac::currentUserId() ?? ($_SESSION['user']['ma_nguoi_dung'] ?? 2));

        if ($orderId <= 0 || $newStatus === '') {
            $this->jsonResponse([
                'status'  => 'error',
                'data'    => null,
                'message' => 'Mã đơn hàng hoặc trạng thái vận chuyển mới không hợp lệ!'
            ], 400);
        }

        try {
            $result = $this->orderModel->capNhatTrangThaiDonHangChiTiet($orderId, $staffId, $newStatus);
            $vipMsg = '';
            if ($result['new_status'] === 'hoan_tat' && !empty($result['points_added'])) {
                $vipMsg = ' (Khách được +' . (int)$result['points_added'] . ' điểm VIP';
                if (!empty($result['customer_tier'])) {
                    $vipMsg .= ', hạng hiện tại: ' . $result['customer_tier'];
                }
                $vipMsg .= ')';
            }

            $emailSent = false;
            if ($result['new_status'] === 'hoan_tat') {
                $ordData = is_array($result['order'] ?? null) ? $result['order'] : ($this->orderModel->getOrderById($orderId) ?? []);
                $toEmail = trim((string)($ordData['thu_dien_tu_nhan'] ?? ''));
                if ($toEmail === '') {
                    $toEmail = trim((string)($ordData['khach_hang_email'] ?? ''));
                }
                $toName = trim((string)($ordData['ten_nguoi_nhan'] ?? 'Ba Mẹ'));
                $items  = is_array($ordData['items'] ?? null) ? $ordData['items'] : [];
                if ($toEmail !== '' && filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $emailSent = Mailer::guiThuMoiDanhGia($toEmail, $toName, $orderId, $items);
                        if (!$emailSent) {
                            error_log('[tgb] guiThuMoiDanhGia returned false for order #' . $orderId . ': ' . Mailer::getLastError());
                        }
                    } catch (Throwable $mailErr) {
                        error_log('[tgb] guiThuMoiDanhGia exception for order #' . $orderId . ': ' . $mailErr->getMessage());
                    }
                }
            }

            $this->jsonResponse(array_merge($result, [
                'status'            => 'success',
                'code'              => 200,
                'review_email_sent' => $emailSent,
                'data'              => $result,
                'message'           => 'Đã cập nhật tiến trình đơn hàng #' . $orderId . ' sang trạng thái "' . $result['status_label'] . '" thành công!' . $vipMsg
            ]), 200);
        } catch (Throwable $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'data'    => null,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function ajaxUpdateStatus(): void {
        $this->ajaxUpdateOrderStatus();
    }

    /**
     * API AJAX Khách hàng gửi Yêu cầu Hoàn hàng / Trả hàng (upload 1-3 ảnh minh chứng)
     * URL: index.php?c=order&a=ajaxSubmitReturn
     */
    public function ajaxSubmitReturn(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập tài khoản thành viên để gửi yêu cầu hoàn hàng!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'code' => 405, 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $userId      = (int)(Rbac::currentUserId() ?? 0);
        $orderId     = (int)($_POST['order_id'] ?? $_POST['ma_don_hang'] ?? 0);
        $reason      = trim((string)($_POST['ly_do_hoan'] ?? $_POST['reason'] ?? ''));
        $description = trim((string)($_POST['mo_ta_chi_tiet'] ?? $_POST['description'] ?? ''));
        $bankInfo    = trim((string)($_POST['thong_tin_nhan_tien'] ?? $_POST['bank_info'] ?? ''));

        if ($orderId <= 0) {
            $this->jsonResponse(['status' => 'error', 'code' => 422, 'data' => null, 'message' => 'Không xác định được đơn hàng cần hoàn trả!'], 422);
        }
        if ($reason === '') {
            $this->jsonResponse(['status' => 'error', 'code' => 422, 'data' => null, 'message' => 'Vui lòng chọn lý do hoàn hàng / trả hàng!'], 422);
        }
        if (mb_strlen($description, 'UTF-8') < 5) {
            $this->jsonResponse(['status' => 'error', 'code' => 422, 'data' => null, 'message' => 'Vui lòng mô tả chi tiết tình trạng sản phẩm tối thiểu 5 ký tự!'], 422);
        }
        if (mb_strlen($bankInfo, 'UTF-8') < 6) {
            $this->jsonResponse(['status' => 'error', 'code' => 422, 'data' => null, 'message' => 'Vui lòng nhập đầy đủ thông tin Ngân hàng / Ví điện tử nhận tiền hoàn!'], 422);
        }

        $orderCheck = $this->orderModel->getOrderById($orderId);
        if (!$orderCheck) {
            $this->jsonResponse(['status' => 'error', 'code' => 404, 'data' => null, 'message' => 'Không tìm thấy đơn hàng cần hoàn trả!'], 404);
        }
        if ((int)($orderCheck['ma_nguoi_dung'] ?? 0) !== $userId) {
            $this->jsonResponse(['status' => 'error', 'code' => 403, 'data' => null, 'message' => 'Chỉ chủ sở hữu đơn hàng mới được gửi yêu cầu hoàn hàng!'], 403);
        }
        if ((string)($orderCheck['trang_thai_don_hang'] ?? '') !== 'hoan_tat') {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => 'Chỉ đơn hàng đã giao thành công (hoàn tất) mới có thể yêu cầu hoàn trả!'], 400);
        }
        if (!empty($orderCheck['return_request'])) {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => 'Đơn hàng này đã có yêu cầu hoàn hàng trước đó!'], 400);
        }

        // Chuẩn hóa danh sách tệp tải lên
        $rawFiles = $_FILES['evidence_images'] ?? $_FILES['images'] ?? null;
        $normalizedFiles = [];
        if (is_array($rawFiles) && isset($rawFiles['name'])) {
            if (is_array($rawFiles['name'])) {
                $cnt = count($rawFiles['name']);
                for ($i = 0; $i < $cnt; $i++) {
                    $err = (int)($rawFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE);
                    if ($err === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    $normalizedFiles[] = [
                        'name'     => (string)($rawFiles['name'][$i] ?? ''),
                        'tmp_name' => (string)($rawFiles['tmp_name'][$i] ?? ''),
                        'error'    => $err,
                        'size'     => (int)($rawFiles['size'][$i] ?? 0),
                    ];
                }
            } elseif ((int)($rawFiles['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $normalizedFiles[] = [
                    'name'     => (string)($rawFiles['name'] ?? ''),
                    'tmp_name' => (string)($rawFiles['tmp_name'] ?? ''),
                    'error'    => (int)($rawFiles['error'] ?? UPLOAD_ERR_NO_FILE),
                    'size'     => (int)($rawFiles['size'] ?? 0),
                ];
            }
        }

        if (count($normalizedFiles) < 1 || count($normalizedFiles) > 3) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 422,
                'data'    => null,
                'message' => 'Vui lòng tải lên từ 1 đến 3 ảnh minh chứng thực tế (JPG, PNG, WEBP)!'
            ], 422);
        }

        $uploadDir = __DIR__ . '/../public/uploads/returns';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo       = function_exists('finfo_open') ? @finfo_open(FILEINFO_MIME_TYPE) : false;

        $savedAbsPaths = [];
        $webPaths      = [];

        try {
            foreach ($normalizedFiles as $idx => $file) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new RuntimeException('Lỗi tải tệp minh chứng thứ ' . ($idx + 1) . ' lên máy chủ.');
                }
                if ($file['size'] <= 0 || $file['size'] > 5 * 1024 * 1024) {
                    throw new RuntimeException('Mỗi ảnh minh chứng phải có dung lượng tối đa 5MB.');
                }
                $ext = strtolower((string)pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt, true)) {
                    throw new RuntimeException('Chỉ chấp nhận định dạng ảnh JPG, JPEG, PNG hoặc WEBP.');
                }
                $mime = $finfo ? (string)@finfo_file($finfo, $file['tmp_name']) : '';
                if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                    throw new RuntimeException('Tệp tải lên không phải là hình ảnh hợp lệ (' . $mime . ').');
                }

                $safeExt  = ($ext === 'jpeg') ? 'jpg' : $ext;
                $fileName = sprintf('ret_%d_%s.%s', $orderId, bin2hex(random_bytes(5)), $safeExt);
                $destPath = $uploadDir . '/' . $fileName;

                if (!@move_uploaded_file($file['tmp_name'], $destPath)) {
                    if (!@copy($file['tmp_name'], $destPath)) {
                        throw new RuntimeException('Không thể lưu ảnh minh chứng vào thư mục máy chủ.');
                    }
                }
                $savedAbsPaths[] = $destPath;
                $webPaths[]      = '/FashionKids/public/uploads/returns/' . $fileName;
            }
            if ($finfo) {
                @finfo_close($finfo);
            }

            $returnReq = $this->orderModel->createReturnRequest(
                $orderId,
                $userId,
                $reason,
                $description,
                $webPaths,
                $bankInfo
            );

            $this->jsonResponse([
                'status'         => 'success',
                'code'           => 200,
                'return_request' => $returnReq,
                'data'           => ['return_request' => $returnReq],
                'message'        => 'Đã gửi yêu cầu hoàn hàng #' . ($returnReq['ma_hoan_code'] ?? $orderId) . ' thành công! Nhân viên FashionKids sẽ kiểm tra sớm nhất.'
            ], 200);
        } catch (Throwable $e) {
            if ($finfo) {
                @finfo_close($finfo);
            }
            foreach ($savedAbsPaths as $abs) {
                if (is_file($abs)) {
                    @unlink($abs);
                }
            }
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'data'    => null,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API AJAX Polling tiến trình xử lý hoàn hàng theo đơn (4 giây/lần trên order-tracking.php)
     * URL: index.php?c=order&a=ajaxGetReturnStatus&order_id=...
     */
    public function ajaxGetReturnStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => 'Mã đơn hàng không hợp lệ!'], 400);
        }
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            $this->jsonResponse(['status' => 'error', 'code' => 404, 'data' => null, 'message' => 'Không tìm thấy đơn hàng!'], 404);
        }
        $retReq     = $order['return_request'] ?? null;
        $currentUid = Rbac::currentUserId();
        $isOwner    = ($currentUid !== null && (int)($order['ma_nguoi_dung'] ?? 0) === $currentUid);
        $canReq     = ($isOwner && ($order['trang_thai_don_hang'] ?? '') === 'hoan_tat' && empty($retReq));

        $this->jsonResponse([
            'status'             => 'success',
            'code'               => 200,
            'return_request'     => $retReq,
            'can_request_return' => $canReq,
            'data'               => [
                'return_request'     => $retReq,
                'can_request_return' => $canReq,
            ],
            'message'            => 'Đã đồng bộ trạng thái hoàn hàng.'
        ], 200);
    }

    /**
     * API AJAX Lấy danh sách yêu cầu hoàn hàng cho Cổng Nhân viên & Admin
     * URL: index.php?c=order&a=ajaxGetReturnRequests
     */
    public function ajaxGetReturnRequests(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép xem danh sách yêu cầu hoàn hàng!'
        );
        $statusFilter = trim((string)($_GET['status'] ?? $_POST['status'] ?? 'all'));
        $keyword      = trim((string)($_GET['q'] ?? $_POST['q'] ?? ''));
        $requests     = $this->orderModel->getAllReturnRequests($statusFilter, $keyword);
        $counts       = $this->orderModel->getReturnStatusCounts();

        $this->jsonResponse([
            'status'   => 'success',
            'code'     => 200,
            'requests' => $requests,
            'counts'   => $counts,
            'data'     => ['requests' => $requests, 'counts' => $counts],
            'message'  => 'Đã tải ' . count($requests) . ' yêu cầu hoàn hàng.'
        ], 200);
    }

    /**
     * API AJAX Duyệt / Từ chối / Đang thu hồi / Hoàn tất hoàn hàng & nhập kho
     * URL: index.php?c=order&a=ajaxProcessReturn
     */
    public function ajaxProcessReturn(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên vận hành và Quản trị viên mới được phép xử lý yêu cầu hoàn hàng!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'code' => 405, 'data' => null, 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $data      = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $returnId  = (int)($data['return_id'] ?? $data['ma_hoan_hang'] ?? 0);
        $newStatus = trim((string)($data['new_status'] ?? $data['trang_thai_moi'] ?? ''));
        $note      = trim((string)($data['note'] ?? $data['ghi_chu_xu_ly'] ?? ''));
        $staffId   = (int)(Rbac::currentUserId() ?? 0);

        if ($returnId <= 0 || $newStatus === '') {
            $this->jsonResponse(['status' => 'error', 'code' => 400, 'data' => null, 'message' => 'Mã yêu cầu hoàn hàng hoặc trạng thái xử lý không hợp lệ!'], 400);
        }

        try {
            $res = $this->orderModel->processReturnRequest($returnId, $staffId, $newStatus, $note);
            $ret = $res['return_request'] ?? [];
            $rcode = $ret['ma_hoan_code'] ?? ('#' . $returnId);
            $msgMap = [
                'da_duyet_hoan' => 'Đã chấp nhận yêu cầu hoàn hàng #' . $rcode . ' — Vui lòng điều phối bưu tá thu hồi sản phẩm!',
                'dang_thu_hoi'  => 'Đã chuyển yêu cầu #' . $rcode . ' sang trạng thái Đang thu hồi hàng!',
                'hoan_tat_hoan' => 'Đã xác nhận hoàn tiền & nhập lại tồn kho cho #' . $rcode . ' (đã tự động cập nhật điểm VIP)!',
                'tu_choi_hoan'  => 'Đã từ chối yêu cầu hoàn hàng #' . $rcode . ' và gửi lý do tới khách hàng!',
            ];

            $this->jsonResponse(array_merge($res, [
                'status'  => 'success',
                'code'    => 200,
                'data'    => $res,
                'message' => $msgMap[$newStatus] ?? ('Đã cập nhật trạng thái hoàn hàng #' . $rcode . '!')
            ]), 200);
        } catch (Throwable $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'data'    => null,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
