
<!-- MODAL XAC THUC OTP 6 SO - MH 20 -->
<div class="modal fade" id="modalVerifyOtp" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalOtpTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg text-center card-pastel">
            <div class="modal-body p-4 p-md-5">
                <div class="mb-3">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm p-3 bg-milk-cream border">
                        <i class="fa-solid fa-shield-cat text-success h2 mb-0"></i>
                    </span>
                </div>
                
                <h4 class="font-weight-bold mb-1 text-slate" id="modalOtpTitle">Xác Thực Mã OTP</h4>
                <p class="text-muted-custom small mb-4">
                    Mã số xác minh bảo mật 6 số đã được gửi tới số điện thoại/email của bạn. Vui lòng nhập để kích hoạt tài khoản thành viên VIP.
                </p>

                <!-- Cum 6 o nhap OTP 48x56px -->
                <div class="d-flex justify-content-center mb-4">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <input type="text" maxlength="1" class="form-control otp-box mx-1" 
                               data-index="<?= $i ?>" pattern="[0-9]*" inputmode="numeric" required>
                    <?php endfor; ?>
                </div>

                <button type="button" id="btnConfirmOtp" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                    <i class="fa-solid fa-circle-check mr-1"></i> Kích Hoạt Tài Khoản VIP
                </button>

                <div class="small text-muted-custom">
                    Chưa nhận được mã? 
                    <span id="countdownTimer" class="font-weight-bold text-danger ml-1">60s</span>
                    <a href="javascript:void(0)" id="btnResendOtp" class="d-none font-weight-bold text-peach ml-1">
                        Gửi lại mã OTP
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>