<?php
$page_title = "Cổng Xác Thực Đăng Nhập / Đăng Ký (MH 09)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Thông báo lỗi khi đăng nhập bằng form truyền thống (không bật JavaScript)
$hgb_flash_error = isset($_GET['error']) ? htmlspecialchars((string)$_GET['error']) : '';
?>

<div class="container my-5" id="hgb_auth_container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card card-pastel shadow-sm hgb-auth-card">
                <div class="card-body p-4 p-md-5">
                    <!-- Logo Thuong Hieu FashionKids -->
                    <div class="text-center mb-4">
                        <a href="/FashionKids/views/pages/home.php" class="text-decoration-none d-inline-flex align-items-center justify-content-center mb-1">
                            <img src="/FashionKids/images/logo-fashionkids.jpg" alt="FashionKids Logo" class="fk-brand-logo mr-2">
                            <span class="brand-logo">Fashion<span class="text-slate">Kids</span></span>
                        </a>
                        <div class="small text-muted-custom">Thời trang sợi tre kháng khuẩn an toàn cho bé</div>
                    </div>

                    <!-- Nav Tabs Pill Bootstrap -->
                    <ul class="nav nav-pills nav-fill nav-pills-custom mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-login" data-toggle="pill" href="#pane-login" role="tab" aria-controls="pane-login" aria-selected="true">
                                ĐĂNG NHẬP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-register" data-toggle="pill" href="#pane-register" role="tab" aria-controls="pane-register" aria-selected="false">
                                ĐĂNG KÝ
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB 1: FORM ĐĂNG NHẬP -->
                        <div class="tab-pane fade show active" id="pane-login" role="tabpanel">
                            <div id="hgb_login_alert" class="alert small py-2 <?= $hgb_flash_error ? 'alert-danger' : 'd-none' ?>" role="alert"><?= $hgb_flash_error ?></div>

                            <form id="hgb_formLogin" method="POST" action="<?= APP_ENDPOINT ?>?c=auth&amp;a=login" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token()) ?>">

                                <div class="form-group">
                                    <label for="hgb_login_username" class="small font-weight-bold text-slate">Tên đăng nhập hoặc Email:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fa-regular fa-user text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" id="hgb_login_username" name="ten_dang_nhap" placeholder="Nhập username hoặc email..." autocomplete="username" required>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="hgb_login_password" class="small font-weight-bold text-slate mb-0">Mật khẩu:</label>
                                        <a href="#" class="small text-muted-custom">Quên mật khẩu?</a>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="password" class="form-control border-left-0" id="hgb_login_password" name="mat_khau" placeholder="Nhập mật khẩu..." autocomplete="current-password" required>
                                    </div>
                                </div>

                                <button type="submit" id="hgb_btn_login" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                                    Đăng Nhập Ngay
                                </button>
                            </form>
                        </div>

                        <!-- TAB 2: FORM ĐĂNG KÝ (kiểm tra trùng realtime + xác thực OTP MH 20) -->
                        <div class="tab-pane fade" id="pane-register" role="tabpanel">
                            <div id="hgb_register_alert" class="alert small py-2 d-none" role="alert"></div>

                            <form id="hgb_formRegister" novalidate autocomplete="off">
                                <div class="form-group">
                                    <label for="hgb_reg_username" class="small font-weight-bold text-slate">Tên đăng nhập:</label>
                                    <input type="text" class="form-control" id="hgb_reg_username" name="ten_dang_nhap" placeholder="Ví dụ: me_be_sam" maxlength="30" autocomplete="off" required>
                                    <div class="invalid-feedback"></div>
                                    <div class="valid-feedback">Tên đăng nhập hợp lệ, có thể đăng ký.</div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_fullname" class="small font-weight-bold text-slate">Họ và tên phụ huynh:</label>
                                    <input type="text" class="form-control" id="hgb_reg_fullname" name="ho_va_ten" placeholder="Ví dụ: Võ Thị Thanh Thảo" maxlength="100" autocomplete="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_email" class="small font-weight-bold text-slate">Địa chỉ Email (nhận mã OTP):</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="hgb_reg_email" name="thu_dien_tu" placeholder="email@gmail.com" autocomplete="off" required>
                                        <div class="input-group-append" id="hgb_otp_action">
                                            <button type="button" class="btn btn-pastel-sky btn-sm px-3" id="hgb_btn_request_otp">
                                                <i class="fa-solid fa-paper-plane mr-1"></i>Lấy mã OTP
                                            </button>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback">Email hợp lệ, có thể đăng ký.</div>
                                    </div>
                                    <div id="hgb_otp_verified_badge" class="mt-2 d-none">
                                        <span class="badge badge-mint px-3 py-2"><i class="fa-solid fa-circle-check mr-1"></i>Email đã xác thực OTP</span>
                                        <button type="button" class="btn btn-link btn-sm text-muted-custom p-0 ml-2" id="hgb_btn_change_email">Đổi email</button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_phone" class="small font-weight-bold text-slate">Số điện thoại:</label>
                                    <input type="tel" class="form-control" id="hgb_reg_phone" name="so_dien_thoai" placeholder="09xxxxxxxx" maxlength="10" inputmode="numeric" autocomplete="tel" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_password" class="small font-weight-bold text-slate">Mật khẩu:</label>
                                    <input type="password" class="form-control" id="hgb_reg_password" name="mat_khau" placeholder="Tối thiểu 6 ký tự..." autocomplete="new-password" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="hgb_reg_password2" class="small font-weight-bold text-slate">Nhập lại mật khẩu:</label>
                                    <input type="password" class="form-control" id="hgb_reg_password2" placeholder="Nhập lại mật khẩu..." autocomplete="new-password" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <button type="submit" id="hgb_btn_final_register" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-2" disabled>
                                    Hoàn Tất Đăng Ký <i class="fa-solid fa-arrow-right ml-1"></i>
                                </button>
                                <p class="small text-muted-custom text-center mb-0" id="hgb_register_hint">
                                    <i class="fa-solid fa-circle-info mr-1"></i>Hãy xác thực email bằng mã OTP để mở khóa nút đăng ký.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-otp.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
