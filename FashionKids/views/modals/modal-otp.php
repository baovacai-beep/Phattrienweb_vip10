<!-- MODAL XÁC THỰC OTP 6 SỐ - MH 20 ( - hgb_)
     Logic xử lý nằm trong public/js/auth-ajax.js (AJAX tới AuthController::ajaxSendOtp / ajaxVerifyOtp) -->
<div class="modal fade" id="hgb_modal_otp" tabindex="-1" role="dialog" aria-labelledby="modalOtpLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-card border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-slate" id="modalOtpLabel">
                    <i class="fas fa-shield-alt text-primary mr-2"></i>Xác thực mã bảo vệ OTP
                </h5>
                <button type="button" class="close text-slate" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Hệ thống đã gửi mã OTP 6 chữ số đến địa chỉ email: <strong id="hgb_display_otp_email" class="text-dark"></strong>. Vui lòng nhập mã để kích hoạt tài khoản.
                </p>

                <div id="hgb_otp_alert" class="alert d-none small py-2"></div>

                <form id="hgb_form_verify_otp" novalidate autocomplete="off">
                    <div class="form-group text-center">
                        <label for="hgb_otp_input" class="sr-only">Mã OTP</label>
                        <input type="text" class="form-control text-center font-weight-bold text-primary rounded-pill letter-spacing-lg hgb-otp-input" 
                               id="hgb_otp_input" name="otp" maxlength="6" placeholder="• • • • • •" 
                               required autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]*">
                        <div class="invalid-feedback text-left" id="hgb_otp_feedback">Mã xác thực gồm 6 chữ số.</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="small text-muted">
                            Không nhận được mã?
                            <span id="hgb_countdownTimer" class="font-weight-bold text-danger ml-1"></span>
                        </span>
                        <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" id="hgb_btn_resend_otp">Gửi lại mã</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block rounded-pill py-2 font-weight-bold" id="hgb_btn_submit_otp">
                        Xác nhận OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
