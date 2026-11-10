<?php
$page_title = "Cổng Xác Thực Đăng Nhập / Đăng Ký & Phân Luồng RBAC (MH 09)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Thông báo lỗi khi đăng nhập bằng form truyền thống (không bật JavaScript)
$hgb_flash_error = isset($_GET['error']) ? htmlspecialchars((string)$_GET['error']) : '';
$hgb_redirect_after = isset($_GET['redirect']) ? htmlspecialchars((string)$_GET['redirect']) : '';
?>

<div class="container my-5" id="hgb_auth_container">
    <div class="row">
        <!-- CỘT TRÁI: KHUNG ĐĂNG NHẬP & ĐĂNG KÝ OTP -->
        <div class="col-12 col-lg-5 mb-4">
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
                                <?php if ($hgb_redirect_after !== ''): ?>
                                    <input type="hidden" name="redirect" value="<?= $hgb_redirect_after ?>">
                                <?php endif; ?>

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
                                        <a href="#rbacDemoPanel" class="small text-peach font-weight-bold">Xem TK mẫu &rarr;</a>
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

                        <!-- TAB 2: FORM ĐĂNG KÝ (kiểm tra trùng realtime + xác thực OTP qua Gmail SMTP MH 20) -->
                        <div class="tab-pane fade" id="pane-register" role="tabpanel">
                            <div id="hgb_register_alert" class="alert small py-2 d-none" role="alert"></div>

                            <form id="hgb_form_register" novalidate autocomplete="off">
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
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_phone" class="small font-weight-bold text-slate">Số điện thoại:</label>
                                    <input type="tel" class="form-control" id="hgb_reg_phone" name="so_dien_thoai" placeholder="09xxxxxxxx" maxlength="10" inputmode="numeric" autocomplete="tel" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label for="hgb_reg_address" class="small font-weight-bold text-slate">Địa chỉ nhận hàng:</label>
                                    <input type="text" class="form-control" id="hgb_reg_address" name="dia_chi_nha" placeholder="Ví dụ: Số 120 Điện Biên Phủ, Q. Bình Thạnh, TP.HCM" maxlength="255" autocomplete="street-address" required>
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

        <!-- CỘT PHẢI: BẢNG TÀI KHOẢN MẪU KIỂM THỬ 4 LUỒNG PHÂN QUYỀN RBAC -->
        <div class="col-12 col-lg-7 mb-4" id="rbacDemoPanel">
            <div class="card border-0 shadow-sm p-4 h-100 fk-rbac-demo-panel">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 border-bottom pb-3">
                    <div>
                        <span class="badge badge-mint px-3 py-1 mb-1 font-weight-bold">
                            <i class="fa-solid fa-shield-halved mr-1"></i>MA TRẬN PHÂN QUYỀN RBAC • 4 LUỒNG NGƯỜI DÙNG
                        </span>
                        <h5 class="font-weight-bold text-slate mb-0">Tài Khoản Mẫu Kiểm Thử Từng Chức Vụ</h5>
                    </div>
                    <span class="badge badge-light border px-3 py-2 text-slate small">
                        Mật khẩu chung: <code>123456</code>
                    </span>
                </div>

                <p class="small text-muted-custom mb-3">
                    Bấm <strong>"Điền vào form"</strong> để tự động điền Username/Password vào khung bên trái, hoặc bấm <strong>"Đăng nhập ngay"</strong> để chuyển thẳng vào luồng nghiệp vụ tương ứng:
                </p>

                <!-- 1. QUẢN TRỊ VIÊN (ADMIN) -->
                <div class="p-3 rounded mb-3 border bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <span class="badge badge-danger badge-pill px-2 py-1 mr-1">1. Quản Trị Viên (Admin)</span>
                            <strong class="text-slate">Hồ Gia Bảo - Admin</strong>
                            <div class="small text-muted-custom mt-1">
                                Tài khoản: <code>admin_fashion</code> &bull; Mật khẩu: <code>123456</code> &bull; Vai trò: <code>quan_tri</code>
                            </div>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <button type="button" class="btn btn-light btn-sm border rounded-pill font-weight-bold mr-1" onclick="fkFillDemoLogin('admin_fashion', '123456')">
                                <i class="fa-solid fa-keyboard mr-1"></i>Điền vào form
                            </button>
                            <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=admin_fashion" class="btn btn-danger btn-sm rounded-pill font-weight-bold">
                                <i class="fa-solid fa-bolt mr-1"></i>Đăng nhập Admin
                            </a>
                        </div>
                    </div>
                    <div class="small text-muted-custom mt-2 pt-2 border-top">
                        <strong>Quyền hạn:</strong> Toàn quyền hệ thống (Dashboard MH 12, Thêm SP mới &amp; Kho MH 13, Bảng Size MH 14, Quản lý User &amp; Hạng VIP MH 15, Banner Sự kiện MH 23, Xuất bản/Xóa Tạp chí MH 21). Bảo vệ chống Session Hijacking theo IP/User-Agent.
                    </div>
                </div>

                <!-- 2. NHÂN VIÊN VẬN HÀNH (STAFF) -->
                <div class="p-3 rounded mb-3 border bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <span class="badge badge-info badge-pill px-2 py-1 mr-1">2. Nhân Viên (Staff)</span>
                            <strong class="text-slate">Lê Bảo Ngọc - CSKH &amp; Kho</strong>
                            <div class="small text-muted-custom mt-1">
                                Tài khoản: <code>staff_tuvan</code> &bull; Mật khẩu: <code>123456</code> &bull; Vai trò: <code>nhan_vien</code>
                            </div>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <button type="button" class="btn btn-light btn-sm border rounded-pill font-weight-bold mr-1" onclick="fkFillDemoLogin('staff_tuvan', '123456')">
                                <i class="fa-solid fa-keyboard mr-1"></i>Điền vào form
                            </button>
                            <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=staff_tuvan" class="btn btn-info btn-sm rounded-pill font-weight-bold">
                                <i class="fa-solid fa-bolt mr-1"></i>Đăng nhập Staff
                            </a>
                        </div>
                    </div>
                    <div class="small text-muted-custom mt-2 pt-2 border-top">
                        <strong>Quyền hạn:</strong> Xử lý đơn hàng (MH 10), Phản hồi Live Chat (MH 11), Sơ tuyển KOL Nhí Vòng 1 (MH 17), Nhập thêm kho (MH 13), Soạn bản nháp Tạp chí (<code>ban_nhap</code> MH 21). <strong>Bị chặn (403)</strong> khi vào trang Cấu hình Admin (MH 12, 14, 15, 23).
                    </div>
                </div>

                <!-- 3. KHÁCH HÀNG THÀNH VIÊN (CUSTOMER) -->
                <div class="p-3 rounded mb-3 border bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <span class="badge badge-success badge-pill px-2 py-1 mr-1">3. Khách Hàng (Customer)</span>
                            <strong class="text-slate">Võ Thị Thanh Thảo (Mẹ Bé Sam)</strong>
                            <div class="small text-muted-custom mt-1">
                                Tài khoản: <code>me_be_sam</code> (VIP Vàng -10%) &bull; hoặc <code>vip_kim_cuong</code> (-15%) &bull; hoặc <code>khach_dong</code> (0%) &bull; MK: <code>123456</code>
                            </div>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <button type="button" class="btn btn-light btn-sm border rounded-pill font-weight-bold mr-1" onclick="fkFillDemoLogin('me_be_sam', '123456')">
                                <i class="fa-solid fa-keyboard mr-1"></i>Điền vào form
                            </button>
                            <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=me_be_sam" class="btn btn-success btn-sm rounded-pill font-weight-bold">
                                <i class="fa-solid fa-bolt mr-1"></i>Đăng nhập Khách VIP
                            </a>
                        </div>
                    </div>
                    <div class="small text-muted-custom mt-2 pt-2 border-top">
                        <strong>Quyền hạn:</strong> Mua hàng hưởng chiết khấu VIP (<code>fn_hgb_tinh_phan_tram_giam_gia</code>), Đánh giá SP đã mua hoàn tất, Nộp hồ sơ KOL Nhí, Thả tim &amp; Bình luận Tạp chí. <strong>Ẩn toàn bộ nút Quản trị &amp; Chặn 403</strong> nếu cố tình vào URL Admin/Staff.
                    </div>
                </div>

                <!-- 4. KHÁCH VÃNG LAI (GUEST) -->
                <div class="p-3 rounded border bg-milk-cream">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <span class="badge badge-secondary badge-pill px-2 py-1 mr-1">4. Khách Vãng Lai (Guest)</span>
                            <strong class="text-slate">Định danh qua <code>session_id()</code></strong>
                            <div class="small text-muted-custom mt-1">
                                Xem Lookbook, Tính Size, Lưu giỏ hàng tạm, Đọc Tạp chí, Chat qua Session ID. Khi bấm Đặt hàng / Nộp KOL / Bình luận sẽ yêu cầu đăng nhập.
                            </div>
                        </div>
                        <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=guest" class="btn btn-outline-secondary btn-sm rounded-pill font-weight-bold mt-2 mt-sm-0">
                            <i class="fa-solid fa-user-clock mr-1"></i>Trải nghiệm chế độ Guest
                        </a>
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
