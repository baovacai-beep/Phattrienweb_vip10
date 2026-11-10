<?php
/**
 * Lớp AuthController (controllers/AuthController.php)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_) - Đăng ký, Đăng nhập, Phân quyền RBAC (4 vai trò),
 * kiểm tra trùng lặp realtime, gửi mã OTP 6 chữ số thực tế qua Gmail SMTP (PHPMailer)
 * và tạo tài khoản qua sp_hgb_dang_ky_thanh_vien.
 *
 * Các endpoint AJAX & Action:
 *   index.php?c=auth&a=ajaxCheckDuplicate
 *   index.php?c=auth&a=ajaxSendOtp
 *   index.php?c=auth&a=ajaxVerifyOtp
 *   index.php?c=auth&a=ajaxFinalRegister
 *   index.php?c=auth&a=ajaxRegister
 *   index.php?c=auth&a=login
 *   index.php?c=auth&a=logout
 *   index.php?c=auth&a=quickSwitchRole&user=admin_fashion|staff_tuvan|me_be_sam|vip_kim_cuong|khach_dong|guest
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Mailer.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends Controller {
    private UserModel $userModel;

    public function __construct(?UserModel $userModel = null) {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
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
    // Endpoint 1: Bắt trùng Username / Email (Sự kiện blur)
    // URL: index.php?c=auth&a=ajaxCheckDuplicate
    // ------------------------------------------------------------------------

    public function ajaxCheckDuplicate(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ'], 405);
        }

        $field = trim($_POST['field'] ?? '');
        $value = trim($_POST['value'] ?? '');

        if (!in_array($field, ['ten_dang_nhap', 'thu_dien_tu'], true) || empty($value)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Dữ liệu không đúng quy cách'], 400);
        }

        try {
            if ($field === 'thu_dien_tu') {
                $typoErr = $this->phatHienLoiGoTenMienEmail($value);
                if ($typoErr !== null) {
                    $this->jsonResponse([
                        'status'  => 'invalid',
                        'exists'  => false,
                        'message' => $typoErr
                    ], 422);
                }
            }

            $exists = $this->userModel->kiemTraTrungLap($field, $value);
            $fieldNameVi = ($field === 'ten_dang_nhap') ? 'Tên đăng nhập' : 'Địa chỉ Email';

            if ($exists) {
                $this->jsonResponse([
                    'status'  => 'duplicate',
                    'exists'  => true,
                    'message' => "{$fieldNameVi} này đã tồn tại trên hệ thống!"
                ], 200);
            } else {
                $this->jsonResponse([
                    'status'  => 'success',
                    'exists'  => false,
                    'message' => "{$fieldNameVi} hợp lệ."
                ], 200);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ------------------------------------------------------------------------
    // Endpoint 2: Gửi mã OTP THỰC TẾ qua Gmail SMTP
    // URL: index.php?c=auth&a=ajaxSendOtp
    // ------------------------------------------------------------------------

    public function ajaxSendOtp(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ'], 405);
        }

        $email = filter_var(strtolower(trim((string)($_POST['email'] ?? ''))), FILTER_VALIDATE_EMAIL);
        $name  = trim($_POST['name'] ?? 'Quý khách');
        if ($name === '') {
            $name = 'Quý khách';
        }

        if (!$email) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Địa chỉ Email không đúng định dạng'], 400);
        }

        $typoErr = $this->phatHienLoiGoTenMienEmail($email);
        if ($typoErr !== null) {
            $this->jsonResponse(['status' => 'error', 'message' => $typoErr], 422);
        }

        try {
            if ($this->userModel->kiemTraTrungLap('thu_dien_tu', $email)) {
                $this->jsonResponse([
                    'status'  => 'duplicate',
                    'exists'  => true,
                    'message' => 'Địa chỉ Email này đã tồn tại trên hệ thống!'
                ], 409);
            }
        } catch (Exception $e) {
            error_log('[hgb] ajaxSendOtp check: ' . $e->getMessage());
        }

        // Đặt lại bộ đếm khóa tạm thời khi người dùng bắt đầu quy trình xác thực đăng ký mới
        unset($_SESSION['hgb_login_fails'], $_SESSION['hgb_login_lock_until']);

        // 1. Tạo OTP 6 số lưu Session
        $otp = $this->userModel->taoMaOtp($email);

        // 2. Gửi thư thực tế qua SMTP Gmail (PHPMailer)
        $sent = Mailer::guiOtpEmail($email, $name, $otp);

        if ($sent) {
            $this->jsonResponse([
                'status'    => 'success',
                'message'   => "Mã OTP đã được gửi đến hộp thư {$email}. Vui lòng kiểm tra (kể cả mục Spam).",
                'resend_in' => 60,
            ], 200);
        } else {
            $smtpError = Mailer::getLastError();
            if ($smtpError !== '') {
                error_log('[hgb] SMTP OTP Error: ' . $smtpError);
            }

            if (strpos($smtpError, '535') !== false || strpos($smtpError, 'BadCredentials') !== false || strpos($smtpError, 'OpenSSL') !== false) {
                $friendlyMsg = 'Dịch vụ gửi mã xác thực qua email hiện đang bận. Vui lòng thử lại sau ít phút hoặc liên hệ bộ phận hỗ trợ.';
            } else {
                $friendlyMsg = 'Không thể gửi mã xác thực OTP tới "' . $email . '". Vui lòng kiểm tra lại địa chỉ email (ví dụ đuôi @gmail.com) và thử lại.';
            }

            $this->jsonResponse([
                'status'  => 'error',
                'message' => $friendlyMsg,
            ], 500);
        }
    }

    // ------------------------------------------------------------------------
    // Endpoint 3: Xác thực OTP từ Modal
    // URL: index.php?c=auth&a=ajaxVerifyOtp
    // ------------------------------------------------------------------------

    public function ajaxVerifyOtp(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ'], 405);
        }

        $email = filter_var(strtolower(trim((string)($_POST['email'] ?? ''))), FILTER_VALIDATE_EMAIL);
        $otp   = trim($_POST['otp'] ?? '');

        if (!$email || strlen($otp) !== 6 || !ctype_digit($otp)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mã OTP phải bao gồm đúng 6 chữ số'], 400);
        }

        $check = $this->userModel->xacThucOtp($email, $otp);

        if ($check['valid']) {
            unset($_SESSION['hgb_login_fails'], $_SESSION['hgb_login_lock_until']);
            $this->jsonResponse(['status' => 'success', 'message' => $check['message']], 200);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => $check['message']], 400);
        }
    }

    // ------------------------------------------------------------------------
    // Endpoint 4: Đăng ký tạo tài khoản hoàn tất qua Stored Procedure
    // URL: index.php?c=auth&a=ajaxFinalRegister
    // ------------------------------------------------------------------------

    public function ajaxFinalRegister(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ'], 405);
        }

        $username = trim($_POST['username'] ?? $_POST['ten_dang_nhap'] ?? '');
        $password = (string)($_POST['password'] ?? $_POST['mat_khau'] ?? '');
        $fullname = trim($_POST['fullname'] ?? $_POST['ho_va_ten'] ?? '');
        $email    = strtolower(trim($_POST['email'] ?? $_POST['thu_dien_tu'] ?? ''));
        $phone    = trim($_POST['phone'] ?? $_POST['so_dien_thoai'] ?? '');
        $address  = trim($_POST['address'] ?? $_POST['dia_chi_nha'] ?? '');

        if (strlen($username) < 4) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Tên đăng nhập phải có ít nhất 4 ký tự!'], 422);
        }
        if (mb_strlen($fullname) < 2) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ họ và tên!'], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Địa chỉ email không hợp lệ!'], 422);
        }
        $typoErr = $this->phatHienLoiGoTenMienEmail($email);
        if ($typoErr !== null) {
            $this->jsonResponse(['status' => 'error', 'message' => $typoErr], 422);
        }
        if (!preg_match('/^\d{9,11}$/', $phone)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Số điện thoại phải gồm từ 9 đến 11 chữ số!'], 422);
        }
        if (strlen($password) < 6) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mật khẩu phải có ít nhất 6 ký tự!'], 422);
        }

        if (!$this->userModel->emailDaXacThuc($email)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Địa chỉ email này chưa được xác thực mã OTP!'], 400);
        }

        try {
            // Mã hóa mật khẩu chuẩn MD5 (32 ký tự hex) đồng bộ với CSDL hgb_nguoi_dung
            $hashedPassword = md5($password);
            $newUserId = $this->userModel->dangKyNguoiDung($username, $hashedPassword, $fullname, $email, $phone, $address);

            unset(
                $_SESSION['hgb_email_verified'],
                $_SESSION['hgb_otp_verified_email'],
                $_SESSION['hgb_login_fails'],
                $_SESSION['hgb_login_lock_until']
            );

            $this->jsonResponse([
                'status'  => 'success',
                'message' => 'Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay.',
                'user_id' => $newUserId
            ], 200);
        } catch (DomainException $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        } catch (PDOException $e) {
            $cleanMsg = preg_replace('/^SQLSTATE\[45000\]:.*?:\s*\d+\s*/u', '', $e->getMessage());
            $cleanMsg = preg_replace('/^(?:Lỗi\s*)?\[(?:HGB|LBN|LNKT|TGB)_ERR_\d+\]\s*:\s*/iu', '', (string)$cleanMsg);
            $this->jsonResponse(['status' => 'error', 'message' => trim($cleanMsg ?: $e->getMessage())], 400);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Alias tương thích ngược cho ajaxRegister
     */
    public function ajaxRegister(): void {
        $this->ajaxFinalRegister();
    }

    // ------------------------------------------------------------------------
    // Đăng nhập / Đăng xuất / Chuyển nhanh vai trò mẫu (RBAC Demo Switcher)
    // ------------------------------------------------------------------------

    public function login(): void {
        if (ob_get_level() > 0 && $this->laAjax()) {
            ob_clean();
        }
        $ajax = $this->laAjax();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            if ($ajax) {
                $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ.'], 405);
            }
            $this->index();
            return;
        }

        $username = trim($_POST['ten_dang_nhap'] ?? $_POST['username'] ?? '');
        $password = (string)($_POST['mat_khau'] ?? $_POST['password'] ?? '');
        if ($username === '' || $password === '') {
            $this->traLoiDangNhap($ajax, false, 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.', 400);
        }

        $khoaDen = (int)($_SESSION['hgb_login_lock_until'] ?? 0);
        if ($khoaDen > time()) {
            $this->traLoiDangNhap($ajax, false, 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau ' . ($khoaDen - time()) . ' giây.', 429);
        } elseif ($khoaDen > 0) {
            unset($_SESSION['hgb_login_fails'], $_SESSION['hgb_login_lock_until']);
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
                $this->traLoiDangNhap($ajax, false, 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau ' . LOGIN_LOCK_SECONDS . ' giây.', 429);
            }
            $this->traLoiDangNhap($ajax, false, 'Tên đăng nhập hoặc mật khẩu không chính xác.', 401);
        }

        if (isset($user['trang_thai']) && (int)$user['trang_thai'] === 0) {
            $this->traLoiDangNhap($ajax, false, 'Tài khoản này hiện đang bị tạm khóa. Vui lòng liên hệ bộ phận hỗ trợ.', 403);
        }

        // Đăng nhập đúng -> xóa ngay bộ đếm khóa tạm thời
        unset($_SESSION['hgb_login_fails'], $_SESSION['hgb_login_lock_until']);

        $this->thietLapPhienDangNhap($user);

        $customRedirect = trim($_POST['redirect'] ?? $_GET['redirect'] ?? '');
        $isSafeLocalRedirect = ($customRedirect !== '' && strpos($customRedirect, '/') === 0 && strpos($customRedirect, '//') !== 0 && strpos($customRedirect, '\\') === false);
        $targetUrl = $isSafeLocalRedirect
            ? $customRedirect
            : $this->trangDichTheoVaiTro($user['vai_tro']);

        $this->traLoiDangNhap($ajax, true, 'Đăng nhập thành công!', 200, $targetUrl);
    }

    /**
     * Đăng xuất về chế độ Khách vãng lai (giữ nguyên giỏ hàng), các vai trò có mật khẩu phải đăng nhập hợp lệ.
     */
    public function quickSwitchRole(): void {
        $target = trim($_GET['user'] ?? $_POST['user'] ?? '');
        if ($target === 'guest') {
            $preservedCart = $_SESSION['cart'] ?? [];
            unset(
                $_SESSION['auth_user'],
                $_SESSION['user'],
                $_SESSION['user_id'],
                $_SESSION['username'],
                $_SESSION['full_name'],
                $_SESSION['role']
            );
            $_SESSION['cart'] = $preservedCart;
            $this->redirect(APP_BASE_URL . '/views/pages/home.php');
        }

        $this->redirect(APP_BASE_URL . '/views/pages/login-register.php?error=' . urlencode('Vui lòng đăng nhập bằng tên đăng nhập và mật khẩu để truy cập tài khoản.'));
    }

    /**
     * Cập nhật thông tin hồ sơ tài khoản thành viên qua AJAX
     * URL: index.php?c=auth&a=ajaxUpdateProfile
     */
    public function ajaxUpdateProfile(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập tài khoản để cập nhật thông tin cá nhân!'
        );
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ.'], 405);
        }

        $userId   = (int)($_SESSION['user']['ma_nguoi_dung'] ?? $_SESSION['user_id'] ?? 0);
        $fullname = trim((string)($_POST['ho_va_ten'] ?? ''));
        $phone    = trim((string)($_POST['so_dien_thoai'] ?? ''));
        $address  = trim((string)($_POST['dia_chi'] ?? $_POST['dia_chi_nha'] ?? ''));

        try {
            $updated = $this->userModel->capNhatThongTinCaNhan($userId, $fullname, $phone, $address);

            $_SESSION['user']['ho_va_ten']     = $updated['ho_va_ten'];
            $_SESSION['user']['so_dien_thoai'] = $updated['so_dien_thoai'];
            $_SESSION['user']['dia_chi_nha']   = $updated['dia_chi_nha'];
            $_SESSION['full_name']             = $updated['ho_va_ten'];

            if (isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user'])) {
                $_SESSION['auth_user']['full_name'] = $updated['ho_va_ten'];
                $_SESSION['auth_user']['phone']     = $updated['so_dien_thoai'];
                $_SESSION['auth_user']['address']   = $updated['dia_chi_nha'];
            }
            Rbac::syncSession();

            $this->jsonResponse([
                'status'  => 'success',
                'message' => 'Đã cập nhật thông tin tài khoản thành công!',
                'user'    => $updated,
            ], 200);
        } catch (DomainException $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 422);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Không thể cập nhật thông tin lúc này.'], 500);
        }
    }

    public function logout(): void {
        // Giữ lại giỏ hàng tạm trong phiên nếu khách vãng lai tiếp tục mua sắm
        $preservedCart = $_SESSION['cart'] ?? [];
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        if (!empty($preservedCart)) {
            $_SESSION['cart'] = $preservedCart;
        }
        $this->redirect(APP_BASE_URL . '/views/pages/login-register.php');
    }

    // ------------------------------------------------------------------------
    // Hàm hỗ trợ
    // ------------------------------------------------------------------------

    private function thietLapPhienDangNhap(array $user): void {
        $preservedCart = $_SESSION['cart'] ?? [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        unset($_SESSION['hgb_login_fails'], $_SESSION['hgb_login_lock_until']);

        $tierId   = (int)($user['ma_hang_thanh_vien'] ?? $user['ma_hang'] ?? 1);
        $tierName = (string)($user['ten_hang'] ?? 'Đồng (Bronze)');
        $discount = (float)($user['phan_tram_giam_gia'] ?? $user['phan_tram_chiet_khau'] ?? 0);

        $user['ma_hang_thanh_vien']   = $tierId;
        $user['ma_hang']              = $tierId;
        $user['ten_hang']             = $tierName;
        $user['phan_tram_giam_gia']   = $discount;
        $user['phan_tram_chiet_khau'] = $discount;

        $_SESSION['user']      = $user;
        $_SESSION['user_id']   = (int)$user['ma_nguoi_dung'];
        $_SESSION['username']  = (string)$user['ten_dang_nhap'];
        $_SESSION['full_name'] = (string)$user['ho_va_ten'];
        $_SESSION['role']      = (string)$user['vai_tro'];
        $_SESSION['auth_user'] = [
            'id'               => (int)$user['ma_nguoi_dung'],
            'username'         => (string)$user['ten_dang_nhap'],
            'full_name'        => (string)$user['ho_va_ten'],
            'email'            => (string)($user['thu_dien_tu'] ?? ''),
            'phone'            => (string)($user['so_dien_thoai'] ?? ''),
            'address'          => (string)($user['dia_chi_nha'] ?? ''),
            'role'             => (string)$user['vai_tro'],
            'tier_id'          => $tierId,
            'tier_name'        => $tierName,
            'discount'         => $discount,
            'discount_percent' => $discount,
            'points'           => (int)($user['diem_tich_luy'] ?? 0),
            'ip'               => Rbac::normalizeIp($_SERVER['REMOTE_ADDR'] ?? null),
            'ua'               => Rbac::normalizeUa($_SERVER['HTTP_USER_AGENT'] ?? null),
        ];
        if (!empty($preservedCart)) {
            $_SESSION['cart'] = $preservedCart;
        }
        Rbac::syncSession();
    }

    /**
     * Phát hiện lỗi gõ nhầm tên miền email phổ biến (ví dụ @gmail.cc, @gamil.com, @gmail.con...) trước khi gọi SMTP
     */
    private function phatHienLoiGoTenMienEmail(string $email): ?string {
        $atPos = strrpos($email, '@');
        if ($atPos === false) {
            return 'Địa chỉ Email không đúng định dạng.';
        }
        $domain = strtolower(trim(substr($email, $atPos + 1)));
        if ($domain === '' || strpos($domain, '.') === false) {
            return 'Tên miền Email không hợp lệ (ví dụ hợp lệ: ten@gmail.com).';
        }

        $exactTypos = [
            'gmail.cc'    => '@gmail.com',
            'gmail.co'    => '@gmail.com',
            'gmail.con'   => '@gmail.com',
            'gmail.cm'    => '@gmail.com',
            'gmail.om'    => '@gmail.com',
            'gmail.cmo'   => '@gmail.com',
            'gmail.cim'   => '@gmail.com',
            'gmail.vom'   => '@gmail.com',
            'gmail.xom'   => '@gmail.com',
            'gmail.coom'  => '@gmail.com',
            'gamil.com'   => '@gmail.com',
            'gmial.com'   => '@gmail.com',
            'gmal.com'    => '@gmail.com',
            'gmaill.com'  => '@gmail.com',
            'gnail.com'   => '@gmail.com',
            'gmai.com'    => '@gmail.com',
            'yahoo.cc'    => '@yahoo.com',
            'yahoo.con'   => '@yahoo.com',
            'yaho.com'    => '@yahoo.com',
            'yahho.com'   => '@yahoo.com',
            'outlook.cc'  => '@outlook.com',
            'outlok.com'  => '@outlook.com',
            'hotmail.cc'  => '@hotmail.com',
            'hotmal.com'  => '@hotmail.com',
        ];
        if (isset($exactTypos[$domain])) {
            return 'Địa chỉ Email có vẻ bị gõ nhầm tên miền (@' . $domain . '). Ý bạn có phải là ' . $exactTypos[$domain] . '?';
        }

        $labels = explode('.', $domain);
        $lastLabel = end($labels);
        $typoTlds = ['cc', 'con', 'cm', 'om', 'cmo', 'cim', 'vom', 'xom', 'coom'];
        if ($lastLabel === '' || strlen((string)$lastLabel) < 2 || in_array($lastLabel, $typoTlds, true)) {
            return 'Đuôi tên miền Email ".' . $lastLabel . '" không hợp lệ. Vui lòng kiểm tra lại (ví dụ @gmail.com).';
        }

        return null;
    }

    private function laAjax(): bool {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }

    private function traLoiDangNhap(bool $ajax, bool $thanhCong, string $message, int $httpCode, ?string $redirect = null): void {
        if ($ajax) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $payload = ['status' => $thanhCong ? 'success' : 'error', 'message' => $message];
            if ($redirect !== null) {
                $payload['redirect'] = $redirect;
            }
            $this->jsonResponse($payload, $httpCode);
        }
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
}
