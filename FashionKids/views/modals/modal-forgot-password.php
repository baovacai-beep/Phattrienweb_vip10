<!-- views/modals/modal-forgot-password.php -->
<div class="modal fade" id="modalForgotPassword" tabindex="-1" role="dialog" aria-labelledby="modalForgotPasswordTitle" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-card border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-slate" id="modalForgotPasswordTitle">
                    <i class="fa-solid fa-key text-peach mr-2"></i>Khôi Phục Mật Khẩu
                </h5>
                <button type="button" class="close text-slate" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div id="hgb_fp_alert" class="alert d-none small py-2 mb-3" role="alert"></div>
                <input type="hidden" id="hgb_fp_verified_email" value="">

                <!-- Bước 1: Nhập Email nhận OTP -->
                <div id="hgb_fp_step1">
                    <p class="small text-muted-custom mb-3">
                        Nhập địa chỉ Email đã đăng ký với FashionKids. Hệ thống sẽ gửi mã OTP 6 chữ số để xác minh và đặt lại mật khẩu mới.
                    </p>
                    <form id="hgb_fp_form_email" novalidate>
                        <div class="form-group">
                            <label for="hgb_fp_email" class="small font-weight-bold text-slate">Địa chỉ Email tài khoản:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fa-regular fa-envelope text-peach"></i></span>
                                </div>
                                <input type="email" class="form-control" id="hgb_fp_email" placeholder="email@gmail.com" autocomplete="email" required>
                            </div>
                        </div>

                        <?php if (class_exists('TurnstileService') && TurnstileService::isConfigured()): ?>
                            <div id="hgb_turnstile_forgot" class="cf-turnstile mb-3" data-sitekey="<?= htmlspecialchars(TurnstileService::getSiteKey()) ?>" data-theme="light"></div>
                        <?php endif; ?>

                        <button type="submit" id="hgb_fp_btn_send_otp" class="btn btn-pastel-primary btn-block rounded-pill py-2 font-weight-bold shadow-sm">
                            <i class="fa-solid fa-paper-plane mr-1"></i>Gửi Mã OTP Khôi Phục
                        </button>
                    </form>
                </div>

                <!-- Bước 2: Nhập mã OTP 6 số -->
                <div id="hgb_fp_step2" class="d-none">
                    <p class="small text-muted-custom mb-3">
                        Mã OTP 6 chữ số đã được gửi tới <strong id="hgb_fp_display_email" class="text-slate"></strong>.
                    </p>
                    <form id="hgb_fp_form_otp" novalidate autocomplete="off">
                        <div class="form-group text-center">
                            <label for="hgb_fp_otp" class="sr-only">Mã OTP khôi phục</label>
                            <input type="text" class="form-control text-center font-weight-bold text-primary rounded-pill letter-spacing-lg hgb-otp-input"
                                   id="hgb_fp_otp" maxlength="6" placeholder="• • • • • •" inputmode="numeric" pattern="[0-9]*" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 small">
                            <span class="text-muted">
                                Chưa nhận được mã?
                                <span id="hgb_fp_countdown" class="font-weight-bold text-danger ml-1"></span>
                            </span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" id="hgb_fp_btn_resend">Gửi lại mã</button>
                        </div>
                        <button type="submit" id="hgb_fp_btn_verify_otp" class="btn btn-pastel-primary btn-block rounded-pill py-2 font-weight-bold mb-2">
                            <i class="fa-solid fa-shield-check mr-1"></i>Xác Nhận Mã OTP
                        </button>
                        <button type="button" id="hgb_fp_btn_back_email" class="btn btn-link btn-sm btn-block text-muted-custom">
                            <i class="fa-solid fa-arrow-left mr-1"></i>Đổi địa chỉ Email khác
                        </button>
                    </form>
                </div>

                <!-- Bước 3: Đặt mật khẩu mới -->
                <div id="hgb_fp_step3" class="d-none">
                    <p class="small text-muted-custom mb-3">
                        Email đã xác thực! Vui lòng thiết lập mật khẩu mới (tối thiểu 6 ký tự) cho tài khoản của bạn.
                    </p>
                    <form id="hgb_fp_form_reset" novalidate>
                        <div class="form-group">
                            <label for="hgb_fp_new_pass" class="small font-weight-bold text-slate">Mật khẩu mới:</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-right-0" id="hgb_fp_new_pass" placeholder="Tối thiểu 6 ký tự..." autocomplete="new-password" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary bg-white border-left-0 hgb-toggle-pass-btn" data-target="#hgb_fp_new_pass" title="Hiện / Ẩn mật khẩu">
                                        <i class="fa-regular fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="hgb_fp_confirm_pass" class="small font-weight-bold text-slate">Nhập lại mật khẩu mới:</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-right-0" id="hgb_fp_confirm_pass" placeholder="Nhập lại mật khẩu mới..." autocomplete="new-password" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary bg-white border-left-0 hgb-toggle-pass-btn" data-target="#hgb_fp_confirm_pass" title="Hiện / Ẩn mật khẩu">
                                        <i class="fa-regular fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="hgb_fp_btn_reset_submit" class="btn btn-pastel-primary btn-block rounded-pill py-2 font-weight-bold shadow-sm">
                            <i class="fa-solid fa-check mr-1"></i>Đặt Lại Mật Khẩu Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
