<?php
/**
 * Lớp AuthController (controllers/AuthController.php)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_) - Đăng ký, Đăng nhập, kiểm tra trùng lặp realtime
 * và xác thực OTP 6 chữ số (MH 09 & MH 20).
 *
 * Các endpoint AJAX (đều là POST, trả JSON, bảo vệ bằng CSRF token):
 *   index.php?c=auth&a=ajaxCheckDuplicate
 *   index.php?c=auth&a=ajaxSendOtp
 *   index.php?c=auth&a=ajaxVerifyOtp
 *   index.php?c=auth&a=ajaxRegister
 *   index.php?c=auth&a=login   (AJAX hoặc form truyền thống)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends Controller {
    private UserModel $userModel;

    public function __construct(?UserModel $userModel = null) {
        $this->userModel = $userModel ?? new UserModel();
    }

    // ------------------------------------------------------------------------
    // Trang giao diện
    // ------------------------------------------------------------------------

    public function index(): void {
        $this->view('pages/login-register', [
            'page_title' => 'Đăng Nhập & Đăng Ký Tài Khoản',
        ]);
    }

    // ------------------------------------------------------------------------
    // Endpoint 1: Bắt trùng Tên đăng nhập / Email (sự kiện blur)
    // ------------------------------------------------------------------------

    public function ajaxCheckDuplicate(): void {
        $this->baoVeAjax();

        $field = trim($_POST['field'] ?? '');
        $value = trim($_POST['value'] ?? '');

        if (!in_array($field, ['ten_dang_nhap', 'thu_dien_tu'], true) || $value === '') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Dữ liệu kiểm tra rỗng hoặc không đúng quy cách.'], 400);
        }

        $tenTruong = ($field === 'ten_dang_nhap') ? 'Tên đăng nhập' : 'Địa chỉ Email';

        try {
            $tonTai = $this->userModel->kiemTraTrungLap($field, $value);
        } catch (RuntimeException $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 503);
        }

        if ($tonTai) {
            $this->jsonResponse([
                'status'  => 'duplicate',
                'exists'  => true,
                'message' => "{$tenTruong} này đã được sử dụng. Vui lòng chọn giá trị khác.",
            ]);
        }
        $this->jsonResponse([
            'status'  => 'success',
            'exists'  => false,
            'message' => "{$tenTruong} hợp lệ, có thể đăng ký.",
        ]);
    }

    // ------------------------------------------------------------------------
    // Endpoint 2: Gửi mã OTP đến Email
    // ------------------------------------------------------------------------

    public function ajaxSendOtp(): void {
        $this->baoVeAjax();

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Địa chỉ Email không đúng định dạng.'], 400);
        }

        // Không gửi mã cho email đã có tài khoản
        try {
            if ($this->userModel->kiemTraTrungLap('thu_dien_tu', $email)) {
                $this->jsonResponse(['status' => 'duplicate', 'exists' => true, 'message' => 'Địa chỉ Email này đã được sử dụng.'], 409);
            }
        } catch (RuntimeException $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 503);
        }

        $kq = $this->userModel->taoMaOtp($email);
        if (!$kq['ok']) {
            $this->jsonResponse([
                'status'      => 'throttled',
                'message'     => $kq['message'],
                'retry_after' => $kq['retry_after'],
            ], 429);
        }

        if (!$this->guiEmailOtp($email, $kq['otp'])) {
            // Không gửi được thì hủy mã để tránh trạng thái treo
            $this->userModel->xoaTrangThaiXacThuc();
            $this->jsonResponse(['status' => 'error', 'message' => 'Không thể gửi email lúc này, vui lòng thử lại sau.'], 500);
        }

        $phanHoi = [
            'status'      => 'success',
            'message'     => 'Mã OTP 6 số đã được gửi tới email của bạn (hiệu lực ' . (OTP_TTL / 60) . ' phút).',
            'expires_in'  => OTP_TTL,
            'resend_in'   => OTP_RESEND_COOLDOWN,
        ];
        if (OTP_DEMO_MODE) {
            $phanHoi['demo_otp'] = $kq['otp']; // Chỉ có khi bật chế độ demo
        }
        $this->jsonResponse($phanHoi);
    }

    // ------------------------------------------------------------------------
    // Endpoint 3: Xác thực mã OTP 6 số từ Modal
    // ------------------------------------------------------------------------

    public function ajaxVerifyOtp(): void {
        $this->baoVeAjax();

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $otp   = trim($_POST['otp'] ?? '');

        if (!$email || strlen($otp) !== 6 || !ctype_digit($otp)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mã OTP phải bao gồm đúng 6 chữ số.'], 400);
        }

        $kq = $this->userModel->xacThucOtp($email, $otp);
        if ($kq['valid']) {
            $this->jsonResponse(['status' => 'success', 'message' => $kq['message']]);
        }
        $this->jsonResponse(['status' => 'error', 'code' => $kq['code'] ?? 'invalid', 'message' => $kq['message']], 400);
    }

    // ------------------------------------------------------------------------
    // Endpoint 4: Hoàn tất đăng ký (chỉ khi email đã xác thực OTP)
    // ------------------------------------------------------------------------

    public function ajaxRegister(): void {
        $this->baoVeAjax();

        $username = trim($_POST['ten_dang_nhap'] ?? '');
        $fullname = trim($_POST['ho_va_ten'] ?? '');
        $email    = trim($_POST['thu_dien_tu'] ?? '');
        $phone    = trim($_POST['so_dien_thoai'] ?? '');
        $password = (string)($_POST['mat_khau'] ?? '');

        $loi = [];
        if (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $username)) {
            $loi['ten_dang_nhap'] = 'Tên đăng nhập 4-30 ký tự, chỉ gồm chữ không dấu, số và dấu gạch dưới.';
        }
        if (mb_strlen($fullname) < 2 || mb_strlen($fullname) > 100) {
            $loi['ho_va_ten'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loi['thu_dien_tu'] = 'Định dạng email không hợp lệ.';
        }
        if (!preg_match('/^0\d{9}$/', $phone)) {
            $loi['so_dien_thoai'] = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
        }
        if (strlen($password) < 6) {
            $loi['mat_khau'] = 'Mật khẩu tối thiểu 6 ký tự.';
        }
        if ($loi) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Thông tin đăng ký chưa hợp lệ.', 'errors' => $loi], 422);
        }

        // Chốt chặn phía server: client không thể bỏ qua bước OTP
        if (!$this->userModel->emailDaXacThuc($email)) {
            $this->jsonResponse(['status' => 'error', 'code' => 'otp_required', 'message' => 'Email chưa được xác thực OTP hoặc phiên xác thực đã hết hạn.'], 403);
        }

        try {
            $maMoi = $this->userModel->dangKy($username, $password, $fullname, $email, $phone, '');
        } catch (DomainException $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 409);
        } catch (RuntimeException $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 503);
        }

        if ($maMoi <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Không thể tạo tài khoản, vui lòng thử lại.'], 500);
        }

        $this->userModel->danhDauDaXacThuc($maMoi);
        $this->userModel->xoaTrangThaiXacThuc();

        $this->jsonResponse([
            'status'   => 'success',
            'user_id'  => $maMoi,
            'username' => $username,
            'message'  => 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.',
        ], 201);
    }

    // ------------------------------------------------------------------------
    // Đăng nhập / Đăng xuất
    // ------------------------------------------------------------------------

    public function login(): void {
        $ajax = $this->laAjax();
        $this->baoVeAjax($ajax);

        // Chống dò mật khẩu: khóa tạm sau nhiều lần sai liên tiếp
        $khoaDen = (int)($_SESSION['hgb_login_lock_until'] ?? 0);
        if ($khoaDen > time()) {
            $this->traLoiDangNhap($ajax, false, 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau ' . ($khoaDen - time()) . ' giây.', 429);
        }

        $username = trim($_POST['ten_dang_nhap'] ?? '');
        $password = (string)($_POST['mat_khau'] ?? '');
        if ($username === '' || $password === '') {
            $this->traLoiDangNhap($ajax, false, 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.', 400);
        }

        try {
            $user = $this->userModel->dangNhap($username, $password);
        } catch (RuntimeException $e) {
            $this->traLoiDangNhap($ajax, false, $e->getMessage(), 503);
        }

        if (!$user) {
            $_SESSION['hgb_login_fails'] = (int)($_SESSION['hgb_login_fails'] ?? 0) + 1;
            if ($_SESSION['hgb_login_fails'] >= LOGIN_MAX_FAILS) {
                $_SESSION['hgb_login_lock_until'] = time() + LOGIN_LOCK_SECONDS;
                $_SESSION['hgb_login_fails']      = 0;
            }
            $this->traLoiDangNhap($ajax, false, 'Tên đăng nhập hoặc mật khẩu không chính xác.', 401);
        }

        // Đăng nhập thành công: đổi session id để chống session fixation
        session_regenerate_id(true);
        unset($_SESSION['hgb_login_fails'], $_SESSION['hgb_login_lock_until']);
        $_SESSION['user']      = $user;
        $_SESSION['user_id']   = (int)$user['ma_nguoi_dung'];
        $_SESSION['username']  = $user['ten_dang_nhap'];
        $_SESSION['full_name'] = $user['ho_va_ten'];
        $_SESSION['role']      = $user['vai_tro'];

        $this->traLoiDangNhap($ajax, true, 'Đăng nhập thành công!', 200, $this->trangDichTheoVaiTro($user['vai_tro']));
    }

    public function logout(): void {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $this->redirect(APP_BASE_URL . '/views/pages/login-register.php');
    }

    // ------------------------------------------------------------------------
    // Hàm hỗ trợ
    // ------------------------------------------------------------------------

    /** Request hiện tại có phải AJAX (jQuery tự gửi header X-Requested-With) */
    private function laAjax(): bool {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    /**
     * Chốt chặn chung cho endpoint ghi dữ liệu: chỉ nhận POST và phải có CSRF token hợp lệ
     */
    private function baoVeAjax(bool $ajax = true): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            if ($ajax) {
                $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ.'], 405);
            }
            $this->redirect(APP_BASE_URL . '/views/pages/login-register.php');
        }
        if (!Csrf::validate()) {
            $msg = 'Phiên làm việc không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
            if ($ajax) {
                $this->jsonResponse(['status' => 'error', 'code' => 'csrf', 'message' => $msg], 419);
            }
            $this->redirect(APP_BASE_URL . '/views/pages/login-register.php?error=' . urlencode($msg));
        }
    }

    private function traLoiDangNhap(bool $ajax, bool $thanhCong, string $message, int $httpCode, ?string $redirect = null): void {
        if ($ajax) {
            $payload = ['status' => $thanhCong ? 'success' : 'error', 'message' => $message];
            if ($redirect !== null) {
                $payload['redirect'] = $redirect;
            }
            $this->jsonResponse($payload, $httpCode);
        }
        // Form truyền thống (không bật JavaScript)
        if ($thanhCong) {
            $this->redirect($redirect ?? APP_BASE_URL . '/views/pages/home.php');
        }
        $this->redirect(APP_BASE_URL . '/views/pages/login-register.php?error=' . urlencode($message));
    }

    private function trangDichTheoVaiTro(string $vaiTro): string {
        return match ($vaiTro) {
            'quan_tri'  => APP_BASE_URL . '/views/pages/admin-dashboard.php',
            'nhan_vien' => APP_BASE_URL . '/views/pages/staff-orders.php',
            default     => APP_BASE_URL . '/views/pages/profile.php',
        };
    }

    /**
     * Gửi mã OTP tới email. Ở chế độ DEMO chỉ ghi log (XAMPP mặc định chưa cấu hình SMTP).
     */
    private function guiEmailOtp(string $email, string $otp): bool {
        if (OTP_DEMO_MODE) {
            error_log("[FashionKids OTP] Mã xác thực cho {$email} là: {$otp}");
            return true;
        }
        $subject = '=?UTF-8?B?' . base64_encode('Mã xác thực OTP - FashionKids') . '?=';
        $body    = "Mã xác thực OTP của bạn là: {$otp}\nMã có hiệu lực trong " . (OTP_TTL / 60) . " phút. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.";
        $headers = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nFrom: FashionKids <no-reply@fashionkids.vn>";
        return @mail($email, $subject, $body, $headers);
    }
}
