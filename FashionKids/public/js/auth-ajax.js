/**
 * public/js/auth-ajax.js - Hồ Gia Bảo (hgb_)
 * Kích hoạt toàn bộ luồng:
 *  1. Kiểm tra Username trùng lặp (blur) -> index.php?c=auth&a=ajaxCheckDuplicate
 *  2. Kiểm tra Email trùng lặp (blur) -> index.php?c=auth&a=ajaxCheckDuplicate
 *  3. Lấy mã OTP gửi qua Gmail SMTP & Mở Modal -> index.php?c=auth&a=ajaxSendOtp
 *  4. Xác nhận OTP 6 chữ số trong Modal -> index.php?c=auth&a=ajaxVerifyOtp
 *  5. Submit Form đăng ký hoàn tất qua sp_hgb_dang_ky_thanh_vien -> index.php?c=auth&a=ajaxFinalRegister
 */
$(document).ready(function () {
    'use strict';

    const ENDPOINT = $('meta[name="fk-endpoint"]').attr('content') || 'index.php';
    let otpCountdownTimer = null;

    function startOtpCountdown(seconds) {
        clearInterval(otpCountdownTimer);
        let left = seconds > 0 ? seconds : 60;
        const $timer = $('#hgb_countdownTimer');
        const $btnResend = $('#hgb_btn_resend_otp');

        $btnResend.prop('disabled', true).addClass('d-none');
        $timer.removeClass('d-none').text('(' + left + 's)');

        otpCountdownTimer = setInterval(function () {
            left--;
            if (left <= 0) {
                clearInterval(otpCountdownTimer);
                $timer.addClass('d-none').text('');
                $btnResend.prop('disabled', false).removeClass('d-none').text('Gửi lại mã');
            } else {
                $timer.text('(' + left + 's)');
            }
        }, 1000);
    }

    // 1. Kiểm tra Username trùng (blur)
    $('#hgb_reg_username').on('blur', function () {
        const input = $(this);
        const username = input.val().trim();
        const feedback = input.siblings('.invalid-feedback');

        if (username.length === 0) {
            input.removeClass('is-valid is-invalid');
            return;
        }

        if (username.length < 4) {
            input.removeClass('is-valid').addClass('is-invalid');
            feedback.text('Tên đăng nhập phải có ít nhất 4 ký tự.');
            return;
        }

        $.post(ENDPOINT + '?c=auth&a=ajaxCheckDuplicate', { field: 'ten_dang_nhap', value: username }, function (res) {
            if (res.status === 'duplicate' || res.exists === true) {
                input.removeClass('is-valid').addClass('is-invalid');
                feedback.text(res.message);
            } else {
                input.removeClass('is-invalid').addClass('is-valid');
            }
        }, 'json');
    });

    // 2. Kiểm tra Email trùng (blur)
    $('#hgb_reg_email').on('blur', function () {
        const input = $(this);
        const email = input.val().trim();
        let feedback = input.siblings('.invalid-feedback');
        if (!feedback.length) {
            feedback = input.closest('.input-group').find('.invalid-feedback');
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (input.prop('readonly') || email.length === 0) {
            return;
        }

        if (!emailRegex.test(email)) {
            input.removeClass('is-valid').addClass('is-invalid');
            feedback.text('Định dạng email không hợp lệ.');
            return;
        }

        $.post(ENDPOINT + '?c=auth&a=ajaxCheckDuplicate', { field: 'thu_dien_tu', value: email }, function (res) {
            if (res.status === 'duplicate' || res.exists === true) {
                input.removeClass('is-valid').addClass('is-invalid');
                feedback.text(res.message);
            } else {
                input.removeClass('is-invalid').addClass('is-valid');
            }
        }, 'json');
    });

    // 3. Bấm Lấy mã OTP -> Gửi mail thật & Mở Modal
    function sendOtpRequest($triggerBtn, isResend) {
        const emailInput = $('#hgb_reg_email');
        const email = emailInput.val().trim();
        const fullname = $('#hgb_reg_fullname').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email || !emailRegex.test(email) || emailInput.hasClass('is-invalid')) {
            alert('Vui lòng nhập Email hợp lệ và không bị trùng trước khi lấy mã OTP!');
            emailInput.focus();
            return;
        }

        const originalHtml = isResend ? 'Gửi lại mã' : 'Lấy mã OTP';
        $triggerBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang gửi mail...');

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxSendOtp',
            type: 'POST',
            dataType: 'json',
            data: { email: email, name: fullname },
            success: function (res) {
                $triggerBtn.prop('disabled', false).html(originalHtml);
                $('#hgb_display_otp_email').text(email);
                $('#hgb_otp_alert').addClass('d-none').removeClass('alert-danger alert-success');
                $('#hgb_otp_input').val('').removeClass('is-invalid is-valid');
                if (!isResend) {
                    $('#hgb_modal_otp').modal('show');
                }
                startOtpCountdown(res.resend_in || 60);
            },
            error: function (xhr) {
                $triggerBtn.prop('disabled', false).html(originalHtml);
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Lỗi gửi mail OTP. Kiểm tra kết nối mạng!';
                if (isResend) {
                    $('#hgb_otp_alert').removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
                } else {
                    alert(msg);
                }
            }
        });
    }

    $('#hgb_btn_request_otp').on('click', function () {
        sendOtpRequest($(this), false);
    });

    $('#hgb_btn_resend_otp').on('click', function () {
        sendOtpRequest($(this), true);
    });

    // 4. Xác nhận OTP trong Modal
    $('#hgb_form_verify_otp').on('submit', function (e) {
        e.preventDefault();
        const email = $('#hgb_display_otp_email').text().trim();
        const otpInput = $('#hgb_otp_input');
        const otp = otpInput.val().trim();
        const alertBox = $('#hgb_otp_alert');
        const btnSubmit = $('#hgb_btn_submit_otp');

        if (!/^\d{6}$/.test(otp)) {
            otpInput.addClass('is-invalid');
            $('#hgb_otp_feedback').text('Mã OTP phải bao gồm đúng 6 chữ số.');
            return;
        }

        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang xác thực...');

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxVerifyOtp',
            type: 'POST',
            dataType: 'json',
            data: { email: email, otp: otp },
            success: function (res) {
                clearInterval(otpCountdownTimer);
                btnSubmit.prop('disabled', false).text('Xác nhận OTP');
                alertBox.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message);

                setTimeout(function () {
                    $('#hgb_modal_otp').modal('hide');
                    $('#hgb_reg_email').prop('readonly', true).removeClass('is-invalid').addClass('is-valid');
                    $('#hgb_otp_action').addClass('d-none');
                    $('#hgb_otp_verified_badge').removeClass('d-none');
                    $('#hgb_btn_final_register').prop('disabled', false); // Mở khóa nút Đăng ký
                    $('#hgb_register_hint').addClass('d-none');
                }, 800);
            },
            error: function (xhr) {
                btnSubmit.prop('disabled', false).text('Xác nhận OTP');
                const errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Mã OTP không chính xác!';
                alertBox.removeClass('d-none alert-success').addClass('alert-danger d-block').text(errMsg);
                otpInput.addClass('is-invalid');
            }
        });
    });

    // Nút Đổi email sau khi xác thực OTP
    $('#hgb_btn_change_email').on('click', function () {
        $('#hgb_reg_email').prop('readonly', false).removeClass('is-valid is-invalid').focus();
        $('#hgb_otp_action').removeClass('d-none');
        $('#hgb_otp_verified_badge').addClass('d-none');
        $('#hgb_btn_final_register').prop('disabled', true);
        $('#hgb_register_hint').removeClass('d-none');
    });

    // Chỉ cho phép nhập tối đa 6 chữ số ở ô OTP
    $('#hgb_otp_input').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
        $(this).removeClass('is-invalid');
    });

    // 5. Submit Form đăng ký hoàn tất
    $('#hgb_form_register, #hgb_formRegister').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#hgb_btn_final_register');
        const alertReg = $('#hgb_register_alert');
        const password = $('#hgb_reg_password').val();
        const password2 = $('#hgb_reg_password2').val();

        $('#hgb_reg_password, #hgb_reg_password2').removeClass('is-invalid');
        alertReg.addClass('d-none').removeClass('alert-danger alert-success');

        if (password.length < 6) {
            $('#hgb_reg_password').addClass('is-invalid').siblings('.invalid-feedback').text('Mật khẩu tối thiểu 6 ký tự.');
            $('#hgb_reg_password').focus();
            return;
        }
        if (password !== password2) {
            $('#hgb_reg_password2').addClass('is-invalid').siblings('.invalid-feedback').text('Mật khẩu nhập lại không khớp.');
            $('#hgb_reg_password2').focus();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang tạo tài khoản...');

        const formData = {
            username: $('#hgb_reg_username').val().trim(),
            password: password,
            fullname: $('#hgb_reg_fullname').val().trim(),
            email:    $('#hgb_reg_email').val().trim(),
            phone:    $('#hgb_reg_phone').val().trim(),
            address:  $('#hgb_reg_address').length ? $('#hgb_reg_address').val().trim() : ''
        };

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxFinalRegister',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (res) {
                alert(res.message);
                window.location.href = ENDPOINT + '?c=auth&a=login';
            },
            error: function (xhr) {
                btn.prop('disabled', false).text('Đăng ký tài khoản');
                const errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Lỗi đăng ký tài khoản!';
                alertReg.removeClass('d-none alert-success').addClass('alert-danger d-block').text(errMsg);
            }
        });
    });

    // 6. Đăng nhập AJAX
    $('#hgb_formLogin').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#hgb_btn_login');
        const alertBox = $('#hgb_login_alert');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang đăng nhập...');

        $.ajax({
            url: ENDPOINT + '?c=auth&a=login',
            type: 'POST',
            dataType: 'json',
            data: {
                ten_dang_nhap: $('#hgb_login_username').val().trim(),
                mat_khau: $('#hgb_login_password').val()
            },
            success: function (res) {
                alertBox.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message);
                window.location.href = res.redirect || '/FashionKids/views/pages/profile.php';
            },
            error: function (xhr) {
                btn.prop('disabled', false).text('Đăng Nhập Ngay');
                const errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Tên đăng nhập hoặc mật khẩu không chính xác!';
                alertBox.removeClass('d-none alert-success').addClass('alert-danger d-block').text(errMsg);
            }
        });
    });
});
