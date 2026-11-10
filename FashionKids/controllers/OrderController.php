<?php
/**
 * Lớp OrderController (controllers/OrderController.php)
 * Điều phối Giỏ hàng, Checkout Gate (RBAC), và Đặt hàng tự động qua Stored Procedure (MH 05)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/entities/ThanhToanVietQR.php';
require_once __DIR__ . '/../models/entities/ThanhToanCOD.php';

class OrderController extends Controller {
    private OrderModel $orderModel;
    private ProductModel $productModel;
    private ReviewModel $reviewModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        Rbac::syncSession();
        $this->orderModel   = new OrderModel();
        $this->productModel = new ProductModel();
        $this->reviewModel  = new ReviewModel();
    }

    /**
     * Hiển thị màn hình Checkout (MH 05)
     */
    public function checkout(): void {
        $productId = (int)($_GET['product_id'] ?? 101);
        $product   = $this->productModel->layChiTietSanPham($productId);

        $this->view('pages/checkout', [
            'page_title' => 'Giỏ Hàng & Thanh Toán Đơn Hàng',
            'product'    => $product,
            'user'       => $_SESSION['user'] ?? null
        ]);
    }

    /**
     * Xử lý nộp đơn hàng qua Form POST truyền thống
     * Checkout Gate: Nếu là Khách vãng lai (guest), bảo toàn $_SESSION['cart'] và điều hướng sang login-register.php
     */
    public function processCheckout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php");
            exit;
        }

        if (Rbac::isGuest()) {
            $redirectUrl = APP_BASE_URL . '/views/pages/login-register.php?redirect=' . urlencode(APP_BASE_URL . '/views/pages/checkout.php') . '&error=' . urlencode('Vui lòng đăng nhập tài khoản thành viên để hoàn tất đặt hàng và tích điểm VIP (Giỏ hàng của bạn vẫn được giữ nguyên).');
            header("Location: " . $redirectUrl);
            exit;
        }

        $userId      = $_SESSION['user']['ma_nguoi_dung'] ?? null;
        $name        = trim($_POST['ten_nguoi_nhan'] ?? '');
        $phone       = trim($_POST['so_dien_thoai_nhan'] ?? '');
        $email       = trim($_POST['thu_dien_tu_nhan'] ?? '');
        $address     = trim($_POST['dia_chi_giao_hang'] ?? '');
        $province    = trim($_POST['tinh_thanh_pho'] ?? '');
        $district    = trim($_POST['quan_huyen'] ?? '');
        $productId   = (int)($_POST['ma_san_pham'] ?? 101);
        $size        = trim($_POST['kich_co_chon'] ?? 'Size 2 (2-3T)');
        $quantity    = (int)($_POST['so_luong'] ?? 1);
        $paymentType = strtolower(trim($_POST['phuong_thuc_thanh_toan'] ?? 'cod'));
        $totalAmount = (float)($_POST['tong_tien_sau_giam'] ?? 285000);

        try {
            if ($province === '' || $district === '') {
                throw new InvalidArgumentException('Vui lòng chọn Tỉnh / Thành phố và Quận / Huyện.');
            }

            // 1. Gọi Model chạy Stored Procedure tạo đơn và trừ kho (sp_tgb_tao_don_hang_va_chi_tiet)
            $orderId = $this->orderModel->taoDonHang(
                $userId, $name, $phone, $email, $address, $province, $district, $productId, $size, $quantity
            );

            // 2. Ứng dụng Abstraction: Xử lý phương thức thanh toán tương ứng
            $paymentMethod = ($paymentType === 'vietqr') ? new ThanhToanVietQR() : new ThanhToanCOD();
            $paymentResult = $paymentMethod->thucHienThanhToan($orderId, $totalAmount);

            // 3. Lưu thông tin đơn vừa tạo vào session và chuyển hướng sang trang hoàn tất
            $_SESSION['recent_order'] = [
                'order_id'       => $orderId,
                'payment_result' => $paymentResult
            ];

            header("Location: index.php?c=order&a=success");
            exit;
        } catch (Exception $e) {
            die("Lỗi xử lý đặt hàng: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Màn hình thông báo hoàn tất và theo dõi tiến trình đơn hàng (MH 06)
     */
    public function success(): void {
        $orderData = $_SESSION['recent_order'] ?? null;
        if (!$orderData) {
            header("Location: index.php");
            exit;
        }

        $this->view('pages/order-tracking', [
            'page_title' => 'Tra Cứu Vận Đơn - Đơn Hàng #' . $orderData['order_id'],
            'orderData'  => $orderData
        ]);
    }

    /**
     * API AJAX Tạo đơn hàng (dành cho client gửi JSON)
     * Checkout Gate: Yêu cầu đã đăng nhập (Customer / Staff hỗ trợ đặt hộ / Admin)
     */
    public function ajaxCreateOrder(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (Rbac::isGuest()) {
            $this->jsonResponse([
                'status'   => 'error',
                'code'     => 401,
                'redirect' => APP_BASE_URL . '/views/pages/login-register.php?redirect=' . urlencode(APP_BASE_URL . '/views/pages/checkout.php'),
                'message'  => 'Vui lòng đăng nhập tài khoản thành viên để hoàn tất đặt hàng và tích điểm VIP!'
            ], 401);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $name     = trim($data['receiver_name'] ?? $data['ten_nguoi_nhan'] ?? '');
        $phone    = trim($data['receiver_phone'] ?? $data['so_dien_thoai_nhan'] ?? '');
        $address  = trim($data['shipping_address'] ?? $data['dia_chi_giao_hang'] ?? '');
        $province = trim($data['province'] ?? $data['tinh_thanh_pho'] ?? '');
        $district = trim($data['district'] ?? $data['quan_huyen'] ?? '');

        if (!$name || !$phone || !$address) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng điền đủ thông tin người nhận!'], 400);
        }

        $orderId = $this->orderModel->taoDonHang(
            $_SESSION['user']['ma_nguoi_dung'] ?? ($_SESSION['user_id'] ?? null),
            $name, $phone,
            $data['receiver_email'] ?? ($_SESSION['user']['thu_dien_tu'] ?? ''),
            $address, $province, $district,
            (int)($data['product_id'] ?? 101),
            $data['size'] ?? 'Size 2',
            (int)($data['quantity'] ?? 1)
        );

        $this->jsonResponse([
            'status'   => 'success',
            'order_id' => $orderId,
            'message'  => 'Đơn hàng #' . $orderId . ' đã được khởi tạo thành công!'
        ]);
    }

    /**
     * API AJAX Gửi đánh giá sản phẩm từ Modal PDP
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
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ!'], 405);
        }

        $productId = (int)($_POST['product_id'] ?? 101);
        $rating    = (int)($_POST['rating_score'] ?? 5);
        $content   = trim((string)($_POST['review_content'] ?? ''));
        $orderId   = (int)($_POST['order_id'] ?? 1);
        $userId    = (int)($_SESSION['user']['ma_nguoi_dung'] ?? $_SESSION['user_id'] ?? 3);
        $userName  = (string)($_SESSION['user']['ho_va_ten'] ?? 'Phụ huynh thành viên');

        if ($productId <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Không xác định được sản phẩm cần đánh giá.'], 422);
        }
        if ($rating < 1 || $rating > 5) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng chọn số sao đánh giá từ 1 đến 5 sao.'], 422);
        }
        if (mb_strlen($content, 'UTF-8') < 5) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng nhập cảm nhận tối thiểu 5 ký tự để chia sẻ cùng các phụ huynh khác.'], 422);
        }

        // Thử lưu vào CSDL qua ReviewModel nếu có đơn hàng hoàn tất tương ứng
        if ($this->reviewModel->hasConnection()) {
            try {
                $this->reviewModel->guiDanhGiaSanPham($productId, $userId, $orderId, $rating, $content);
            } catch (Throwable $e) {
                error_log('[tgb] ajaxSubmitReview SP fallback: ' . $e->getMessage());
            }
        }

        $newReview = [
            'id'          => time(),
            'product_id'  => $productId,
            'author_name' => $userName,
            'rating'      => $rating,
            'content'     => $content,
            'date'        => date('d/m/Y'),
            'verified'    => true,
        ];

        if (!isset($_SESSION['tgb_product_reviews']) || !is_array($_SESSION['tgb_product_reviews'])) {
            $_SESSION['tgb_product_reviews'] = [];
        }
        if (!isset($_SESSION['tgb_product_reviews'][$productId]) || !is_array($_SESSION['tgb_product_reviews'][$productId])) {
            $_SESSION['tgb_product_reviews'][$productId] = [];
        }
        array_unshift($_SESSION['tgb_product_reviews'][$productId], $newReview);

        $this->jsonResponse([
            'status'  => 'success',
            'message' => 'Cảm ơn ba mẹ đã gửi đánh giá sản phẩm thành công!',
            'review'  => $newReview,
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
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ (Chỉ chấp nhận POST)!'], 405);
        }

        $orderId   = (int)($_POST['order_id'] ?? $_POST['ma_don_hang'] ?? 0);
        $newStatus = trim((string)($_POST['new_status'] ?? $_POST['trang_thai_moi'] ?? $_POST['status'] ?? ''));
        $staffId   = (int)(Rbac::currentUserId() ?? ($_SESSION['user']['ma_nguoi_dung'] ?? 2));

        if ($orderId <= 0 || $newStatus === '') {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Mã đơn hàng hoặc trạng thái vận chuyển mới không hợp lệ!'
            ], 400);
        }

        try {
            $result = $this->orderModel->capNhatTrangThaiDonHangChiTiet($orderId, $staffId, $newStatus);
            $this->jsonResponse(array_merge($result, [
                'status'  => 'success',
                'message' => 'Đã cập nhật tiến trình đơn hàng #' . $orderId . ' sang trạng thái "' . $result['status_label'] . '" thành công!'
            ]), 200);
        } catch (Throwable $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function ajaxUpdateStatus(): void {
        $this->ajaxUpdateOrderStatus();
    }
}
