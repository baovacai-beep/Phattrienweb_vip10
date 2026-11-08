<?php
/**
 * Lớp AuthController (controllers/AuthController.php)
 * Điều phối Đăng ký, Đăng nhập và Xác thực OTP (MH 19, 20)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends Controller {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Màn hình Đăng nhập / Đăng ký
     */
    public function index(): void {
        $this->view('pages/login-register', [
            'page_title' => 'Đăng Nhập & Đăng Ký Tài Khoản'
        ]);
    }

    /**
     * Xử lý Đăng nhập qua Form hoặc AJAX
     */
    public function login(): void {
        $username = trim($_POST['username'] ?? $_POST['ten_dang_nhap'] ?? '');
        $password = trim($_POST['password'] ?? $_POST['mat_khau'] ?? '');

        if (empty($username) || empty($password)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!'], 400);
        }

        $user = $this->userModel->dangNhap($username, $password);

        if ($user) {
            $_SESSION['user']      = $user;
            $_SESSION['user_id']   = $user['ma_nguoi_dung'];
            $_SESSION['username']  = $user['ten_dang_nhap'];
            $_SESSION['full_name'] = $user['ho_va_ten'];
            $_SESSION['role']      = $user['vai_tro'];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->jsonResponse(['status' => 'success', 'user' => $user, 'message' => 'Đăng nhập thành công!']);
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->jsonResponse(['status' => 'error', 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác!'], 401);
            } else {
                die("Tên đăng nhập hoặc mật khẩu không chính xác!");
            }
        }
    }

    /**
     * Xử lý Đăng ký tài khoản mới qua Stored Procedure
     */
    public function register(): void {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $fullname = trim($_POST['full_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $address  = trim($_POST['address'] ?? '');

        if (!$username || !$password || !$fullname || !$email) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng điền đủ các trường bắt buộc!'], 400);
        }

        $newId = $this->userModel->dangKy($username, $password, $fullname, $email, $phone, $address);

        if ($newId > 0) {
            $this->jsonResponse([
                'status'  => 'success',
                'user_id' => $newId,
                'message' => 'Đăng ký tài khoản thành công! Vui lòng xác thực OTP.'
            ]);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Tên đăng nhập hoặc email đã tồn tại trên hệ thống!'], 400);
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(): void {
        unset($_SESSION['user'], $_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name'], $_SESSION['role']);
        header("Location: index.php");
        exit;
    }
}
