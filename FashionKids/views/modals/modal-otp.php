<!-- MODAL XÁC THỰC OTP 6 SỐ - MH 20 (Đảm nhiệm: Hồ Gia Bảo - hgb_)
     Logic xử lý nằm trong public/js/auth-ajax.js (AJAX tới AuthController::ajaxSendOtp / ajaxVerifyOtp) -->
<div class="modal fade" id="hgb_modal_otp" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="hgb_modalOtpTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg text-center card-pastel">
            <div class="modal-body p-4 p-md-5">
                <button type="button" class="close text-slate position-absolute hgb-otp-close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>

                <div class="mb-3">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm p-3 bg-milk-cream border">
                        <i class="fa-solid fa-shield-halved text-success h2 mb-0"></i>
                    </span>
                </div>

                <h4 class="font-weight-bold mb-1 text-slate" id="hgb_modalOtpTitle">Xác Thực Mã OTP</h4>
                <p class="text-muted-custom small mb-3">
                    Mã xác thực gồm 6 chữ số đã được gửi tới email
                    <strong id="hgb_display_otp_email" class="text-slate"></strong>.
                    Vui lòng nhập mã để kích hoạt tài khoản.
                </p>

                <div id="hgb_otp_alert" class="alert small py-2 d-none" role="alert"></div>

                <form id="hgb_form_verify_otp" novalidate autocomplete="off">
                    <!-- Cụm 6 ô nhập mã OTP -->
                    <div class="d-flex justify-content-center mb-4">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <input type="text" maxlength="1" class="form-control otp-box hgb-otp-box mx-1"
                                   data-index="<?= $i ?>" pattern="[0-9]*" inputmode="numeric"
                                   autocomplete="one-time-code" aria-label="Chữ số OTP thứ <?= $i ?>">
                        <?php endfor; ?>
                    </div>

                    <button type="submit" id="hgb_btn_submit_otp" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                        <i class="fa-solid fa-circle-check mr-1"></i> Xác Nhận OTP
                    </button>
                </form>

                <div class="small text-muted-custom">
                    Chưa nhận được mã?
                    <span id="hgb_countdownTimer" class="font-weight-bold text-danger ml-1"></span>
                    <button type="button" id="hgb_btn_resend_otp" class="btn btn-link btn-sm p-0 align-baseline d-none font-weight-bold text-peach ml-1">
                        Gửi lại mã OTP
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
