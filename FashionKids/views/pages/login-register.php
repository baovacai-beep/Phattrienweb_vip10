<?php
$page_title = "Đăng Nhập & Đăng Ký Thành Viên - FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../core/GoogleOAuthService.php";

// Thông báo lỗi khi đăng nhập bằng form truyền thống (không bật JavaScript)
$hgb_flash_error = isset($_GET['error']) ? htmlspecialchars((string)$_GET['error']) : '';
$hgb_redirect_after = isset($_GET['redirect']) ? htmlspecialchars((string)$_GET['redirect']) : '';
$hgb_google_enabled = GoogleOAuthService::isConfigured();
$hgb_turnstile_enabled = TurnstileService::isConfigured();
$hgb_google_url = APP_ENDPOINT . '?c=auth&a=googleLogin' . ($hgb_redirect_after !== '' ? ('&redirect=' . urlencode((string)$_GET['redirect'])) : '');
?>

<div class="container my-5" id="hgb_auth_container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card card-pastel shadow-sm hgb-auth-card border-0">
                <div class="card-body p-4 p-md-5">
                    <!-- Logo Thương Hiệu FashionKids -->
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
                            <a class="nav-link active text-nowrap" id="tab-login" data-toggle="pill" href="#pane-login" role="tab" aria-controls="pane-login" aria-selected="true">
                                ĐĂNG NHẬP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap" id="tab-register" data-toggle="pill" href="#pane-register" role="tab" aria-controls="pane-register" aria-selected="false">
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
                                <?php if ($hgb_redirect_after !== ''): ?>
                                    <input type="hidden" name="redirect" value="<?= $hgb_redirect_after ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="hgb_login_username" class="small font-weight-bold text-slate">Tên đăng nhập hoặc Email:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fa-regular fa-user text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" id="hgb_login_username" name="ten_dang_nhap" placeholder="Nhập tên đăng nhập hoặc email..." autocomplete="username" required>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="hgb_login_password" class="small font-weight-bold text-slate mb-0">Mật khẩu:</label>
                                        <a href="javascript:void(0)" class="small font-weight-bold text-peach text-decoration-none" data-toggle="modal" data-target="#modalForgotPassword">
                                            Quên mật khẩu?
                                        </a>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="password" class="form-control border-left-0 border-right-0" id="hgb_login_password" name="mat_khau" placeholder="Nhập mật khẩu..." autocomplete="current-password" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary bg-white border-left-0 hgb-toggle-pass-btn" data-target="#hgb_login_password" title="Hiện / Ẩn mật khẩu">
                                                <i class="fa-regular fa-eye text-muted"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($hgb_turnstile_enabled): ?>
                                    <div id="hgb_turnstile_login" class="cf-turnstile mb-3" data-sitekey="<?= htmlspecialchars(TurnstileService::getSiteKey()) ?>" data-theme="light"></div>
                                <?php endif; ?>

                                <button type="submit" id="hgb_btn_login" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                                    Đăng Nhập Ngay
                                </button>

                                <div class="d-flex align-items-center my-3">
                                    <hr class="flex-grow-1 my-0">
                                    <span class="px-2 small text-muted-custom font-weight-bold">Hoặc</span>
                                    <hr class="flex-grow-1 my-0">
                                </div>

                                <a href="<?= htmlspecialchars($hgb_google_url) ?>"
                                   class="btn btn-light border btn-block font-weight-bold py-2 d-flex align-items-center justify-content-center shadow-sm text-slate">
                                    <i class="fa-brands fa-google text-danger mr-2"></i>Đăng nhập nhanh bằng Google
                                </a>
                                <?php if (!$hgb_google_enabled): ?>
                                    <small class="d-block text-center text-muted-custom mt-2">
                                        <i class="fa-solid fa-circle-info mr-1"></i>Chưa điền <code>GOOGLE_CLIENT_ID</code> trong <code>.env</code>
                                    </small>
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- TAB 2: FORM ĐĂNG KÝ -->
                        <div class="tab-pane fade" id="pane-register" role="tabpanel">
                            <div id="hgb_register_alert" class="alert small py-2 d-none" role="alert"></div>

                            <form id="hgb_form_register" novalidate autocomplete="off">
                                <div class="form-group">
                                    <label for="hgb_reg_username" class="small font-weight-bold text-slate">Tên đăng nhập:</label>
                                    <input type="text" class="form-control" id="hgb_reg_username" name="ten_dang_nhap" placeholder="Nhập tên đăng nhập (tối thiểu 4 ký tự)" maxlength="30" autocomplete="off" required>
                                    <div class="invalid-feedback"></div>
                                    <div class="valid-feedback">Tên đăng nhập hợp lệ, có thể đăng ký.</div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_fullname" class="small font-weight-bold text-slate">Họ và tên phụ huynh:</label>
                                    <input type="text" class="form-control" id="hgb_reg_fullname" name="ho_va_ten" placeholder="Nhập họ và tên đầy đủ" maxlength="100" autocomplete="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_email" class="small font-weight-bold text-slate">Địa chỉ Email (nhận mã OTP):</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="hgb_reg_email" name="thu_dien_tu" placeholder="email@gmail.com" autocomplete="off" required>
                                        <div class="input-group-append" id="hgb_otp_action">
                                            <button type="button" class="btn btn-pastel-sky btn-sm px-3 text-nowrap" id="hgb_btn_request_otp">
                                                Lấy mã OTP
                                            </button>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback">Email hợp lệ, có thể đăng ký.</div>
                                    </div>
                                    <div id="hgb_otp_verified_badge" class="mt-2 d-none">
                                        <span class="badge badge-mint px-3 py-2"><i class="fa-solid fa-circle-check mr-1"></i>Email đã xác thực OTP</span>
                                        <button type="button" class="btn btn-link btn-sm text-muted-custom p-0 ml-2" id="hgb_btn_change_email">Đổi email</button>
                                    </div>
                                    <?php if ($hgb_turnstile_enabled): ?>
                                        <div id="hgb_turnstile_register" class="cf-turnstile mt-2" data-sitekey="<?= htmlspecialchars(TurnstileService::getSiteKey()) ?>" data-theme="light"></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_phone" class="small font-weight-bold text-slate">Số điện thoại:</label>
                                    <input type="tel" class="form-control" id="hgb_reg_phone" name="so_dien_thoai" placeholder="09xxxxxxxx" maxlength="11" inputmode="numeric" autocomplete="tel" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_address" class="small font-weight-bold text-slate">Địa chỉ nhận hàng:</label>
                                    <input type="text" class="form-control" id="hgb_reg_address" name="dia_chi_nha" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" maxlength="255" autocomplete="street-address" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_password" class="small font-weight-bold text-slate">Mật khẩu:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control border-right-0" id="hgb_reg_password" name="mat_khau" placeholder="Tối thiểu 6 ký tự..." autocomplete="new-password" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary bg-white border-left-0 hgb-toggle-pass-btn" data-target="#hgb_reg_password" title="Hiện / Ẩn mật khẩu">
                                                <i class="fa-regular fa-eye text-muted"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="hgb_reg_password2" class="small font-weight-bold text-slate">Nhập lại mật khẩu:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control border-right-0" id="hgb_reg_password2" placeholder="Nhập lại mật khẩu..." autocomplete="new-password" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary bg-white border-left-0 hgb-toggle-pass-btn" data-target="#hgb_reg_password2" title="Hiện / Ẩn mật khẩu">
                                                <i class="fa-regular fa-eye text-muted"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <button type="submit" id="hgb_btn_final_register" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-2" disabled>
                                    Đăng ký tài khoản
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
require_once __DIR__ . "/../modals/modal-forgot-password.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
