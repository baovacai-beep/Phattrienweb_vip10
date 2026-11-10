<?php
/**
 * Lớp OrderController (controllers/OrderController.php)
 * Điều phối Giỏ hàng, Checkout, và Đặt hàng tự động qua Stored Procedure (MH 05)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/entities/ThanhToanVietQR.php';
require_once __DIR__ . '/../models/entities/ThanhToanCOD.php';

class OrderController extends Controller {
    private OrderModel $orderModel;
    private ProductModel $productModel;

    public function __construct() {
        $this->orderModel   = new OrderModel();
        $this->productModel = new ProductModel();
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
     */
    public function processCheckout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php");
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
        $paymentType = trim($_POST['phuong_thuc_thanh_toan'] ?? 'cod');
        $totalAmount = (float)($_POST['tong_tien_sau_giam'] ?? 285000);

        try {
            // 1. Gọi Model chạy Stored Procedure tạo đơn và trừ kho
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
     */
    public function ajaxCreateOrder(): void {
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
            $_SESSION['user_id'] ?? null,
            $name, $phone,
            $data['receiver_email'] ?? '',
            $address, $province, $district,
            (int)($data['product_id'] ?? 101),
            $data['size'] ?? 'Size 2',
            (int)($data['quantity'] ?? 1)
        );

        $this->jsonResponse([
            'status'   => 'success',
            'order_id' => $orderId,
            'message'  => 'Đơn hàng đã được khởi tạo thành công qua Stored Procedure!'
        ]);
    }

    /**
     * API AJAX: Nhân viên tiếp nhận đơn và cập nhật tiến trình giao hàng (MH 10)
     * Triệu gọi Stored Procedure sp_lnkt_cap_nhat_trang_thai_don_hang.
     */
    public function ajaxUpdateStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ (Chỉ chấp nhận POST)'], 405);
        }

        try {
            $maDonHang    = (int)($_POST['ma_don_hang'] ?? $_POST['order_id'] ?? 0);
            $trangThaiMoi = trim((string)($_POST['trang_thai_moi'] ?? $_POST['new_status'] ?? ''));
            $maNhanVien   = Rbac::currentUserId();

            if ($maDonHang <= 0) {
                throw new InvalidArgumentException('Mã đơn hàng cần xử lý không hợp lệ!');
            }
            if ($trangThaiMoi === '') {
                throw new InvalidArgumentException('Vui lòng chọn trạng thái đơn hàng mới!');
            }

            $this->orderModel->capNhatTrangThai($maDonHang, $maNhanVien, $trangThaiMoi);

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'     => 'success',
                'order_id'   => $maDonHang,
                'new_status' => $trangThaiMoi,
                'staff_id'   => $maNhanVien,
                'message'    => "Đã thực thi sp_lnkt_cap_nhat_trang_thai_don_hang cho đơn #{$maDonHang} -> Trạng thái mới: {$trangThaiMoi}"
            ], 200);
        } catch (Exception $e) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $this->jsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
