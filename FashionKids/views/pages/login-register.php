<?php
$page_title = "Cổng Xác Thực Đăng Nhập / Đăng Ký (MH 09)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
?>

<div class="container my-5" id="hgb_auth_container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card card-pastel shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <!-- Nav Tabs Pill Bootstrap -->
                    <ul class="nav nav-pills nav-fill nav-pills-custom mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-login" data-toggle="pill" href="#pane-login" role="tab">
                                ĐĂNG NHẬP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-register" data-toggle="pill" href="#pane-register" role="tab">
                                ĐĂNG KÝ
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB 1: FORM ĐĂNG NHẬP -->
                        <div class="tab-pane fade show active" id="pane-login" role="tabpanel">
                            <form id="hgb_formLogin" method="POST" action="/FashionKids/controllers/AuthController.php?action=login">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-slate">Tên đăng nhập hoặc Email:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fa-regular fa-user text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" name="ten_dang_nhap" placeholder="Nhập username hoặc email..." required>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="small font-weight-bold text-slate mb-0">Mật khẩu:</label>
                                        <a href="#" class="small text-muted-custom">Quên mật khẩu?</a>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="password" class="form-control border-left-0" name="mat_khau" placeholder="Nhập mật khẩu..." required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                                    Đăng Nhập Ngay
                                </button>
                            </form>
                        </div>

                        <!-- TAB 2: FORM ĐĂNG KÝ (Kích hoạt mở Modal OTP MH 20) -->
                        <div class="tab-pane fade" id="pane-register" role="tabpanel">
                            <form id="hgb_formRegister" onsubmit="event.preventDefault(); $('#modalVerifyOtp').modal('show'); window.hgbStartOtpTimer();">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-slate">Tên đăng nhập:</label>
                                    <input type="text" class="form-control" name="ten_dang_nhap" placeholder="Ví dụ: me_be_sam" required>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-slate">Họ và tên phụ huynh:</label>
                                    <input type="text" class="form-control" name="ho_va_ten" placeholder="Ví dụ: Võ Thị Thanh Thảo" required>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-slate">Địa chỉ Email:</label>
                                    <input type="email" class="form-control" name="thu_dien_tu" placeholder="email@gmail.com" required>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-slate">Số điện thoại nhận mã OTP:</label>
                                    <input type="tel" class="form-control" name="so_dien_thoai" placeholder="09xxxxxxxx" pattern="[0-9]{10}" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-slate">Mật khẩu:</label>
                                    <input type="password" class="form-control" name="mat_khau" placeholder="Tối thiểu 6 ký tự..." required>
                                </div>

                                <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                                    Đăng Ký Tài Khoản & Nhận OTP <i class="fa-solid fa-arrow-right ml-1"></i>
                                </button>
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
