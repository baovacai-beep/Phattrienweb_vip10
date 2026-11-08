/**
 * public/js/auth-ajax.js - Phụ trách: Hồ Gia Bảo (hgb_)
 * Dự án FashionKids - Giai đoạn 4: Tích hợp AJAX & Webservice JSON
 *
 * Chức năng:
 *  1. Kiểm tra Tên đăng nhập & Email trùng lặp realtime (sự kiện blur) -> AuthController::ajaxCheckDuplicate
 *  2. Gửi mã OTP ngầm đến Email & hiển thị Modal -> AuthController::ajaxSendOtp
 *  3. Xác thực mã OTP 6 số không tải lại trang -> AuthController::ajaxVerifyOtp
 *  4. Hoàn tất đăng ký người dùng -> AuthController::ajaxRegister
 *  5. Đăng nhập AJAX linh hoạt -> AuthController::login
 *
 * Tuân thủ Quy chuẩn: Không inline CSS, dùng class native Bootstrap 4 (.is-valid, .is-invalid, .invalid-feedback).
 */
$(document).ready(function () {
    'use strict';

    // Xác định Endpoint và CSRF token
    var ENDPOINT = $('meta[name="fk-endpoint"]').attr('content') || 'index.php';
    var CSRF     = $('meta[name="csrf-token"]').attr('content') || '';

    // Hàm tiện ích gửi AJAX POST có kèm CSRF header
    function sendPost(action, data) {
        return $.ajax({
            url: ENDPOINT + '?c=auth&a=' + action,
            type: 'POST',
            dataType: 'json',
            data: data,
            headers: CSRF ? { 'X-CSRF-Token': CSRF } : {},
            timeout: 10000
        });
    }

    // ========================================================================
    // 1. KIỂM TRA TÊN ĐĂNG NHẬP TRÙNG LẶP (Sự kiện blur)
    // ========================================================================
    $('#hgb_reg_username').on('blur', function () {
        var input = $(this);
        var username = $.trim(input.val());
        var feedback = input.siblings('.invalid-feedback');

        if (username.length === 0) {
            input.removeClass('is-valid is-invalid');
            return;
        }

        if (username.length < 4) {
            input.removeClass('is-valid').addClass('is-invalid');
            feedback.text('Tên đăng nhập phải có ít nhất 4 ký tự.');
            return;
        }

        sendPost('ajaxCheckDuplicate', { field: 'ten_dang_nhap', value: username })
            .done(function (res) {
                if (res.status === 'duplicate' || res.exists === true) {
                    input.removeClass('is-valid').addClass('is-invalid');
                    feedback.text(res.message);
                } else {
                    input.removeClass('is-invalid').addClass('is-valid');
                }
            })
            .fail(function (xhr) {
                var res = xhr.responseJSON;
                if (res && res.status === 'duplicate') {
                    input.removeClass('is-valid').addClass('is-invalid');
                    feedback.text(res.message);
                }
            });
    }).on('input', function () {
        $(this).removeClass('is-valid is-invalid');
    });

    // ========================================================================
    // 2. KIỂM TRA EMAIL TRÙNG LẶP (Sự kiện blur)
    // ========================================================================
    $('#hgb_reg_email').on('blur', function () {
        var input = $(this);
        var email = $.trim(input.val());
        var feedback = input.closest('.input-group').find('.invalid-feedback');
        if (!feedback.length) {
            feedback = input.siblings('.invalid-feedback');
        }
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email.length === 0 || input.prop('readonly')) {
            return;
        }

        if (!emailRegex.test(email)) {
            input.removeClass('is-valid').addClass('is-invalid');
            feedback.text('Định dạng email không hợp lệ.');
            return;
        }

        sendPost('ajaxCheckDuplicate', { field: 'thu_dien_tu', value: email })
            .done(function (res) {
                if (res.status === 'duplicate' || res.exists === true) {
                    input.removeClass('is-valid').addClass('is-invalid');
                    feedback.text(res.message);
                } else {
                    input.removeClass('is-invalid').addClass('is-valid');
                }
            })
            .fail(function (xhr) {
                var res = xhr.responseJSON;
                if (res && res.status === 'duplicate') {
                    input.removeClass('is-valid').addClass('is-invalid');
                    feedback.text(res.message);
                }
            });
    }).on('input', function () {
        if (!$(this).prop('readonly')) {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // ========================================================================
    // 3. KÍCH HOẠT NHẬN MÃ OTP VÀ MỞ MODAL
    // ========================================================================
    var timerInterval = null;

    function startOtpCountdown(seconds) {
        clearInterval(timerInterval);
        var timeLeft = seconds > 0 ? seconds : 60;
        var $timer = $('#hgb_countdownTimer');
        var $btnResend = $('#hgb_btn_resend_otp');

        $btnResend.prop('disabled', true).addClass('d-none');
        $timer.removeClass('d-none').text('(' + timeLeft + 's)');

        timerInterval = setInterval(function () {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                $timer.addClass('d-none').text('');
                $btnResend.prop('disabled', false).removeClass('d-none');
            } else {
                $timer.text('(' + timeLeft + 's)');
            }
        }, 1000);
    }

    function triggerRequestOtp(btn) {
        var emailInput = $('#hgb_reg_email');
        var email = $.trim(emailInput.val());

        if (!email || emailInput.hasClass('is-invalid')) {
            alert('Vui lòng nhập Email hợp lệ và không bị trùng trước khi lấy mã OTP!');
            emailInput.focus();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang gửi...');

        sendPost('ajaxSendOtp', { email: email })
            .done(function (res) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane mr-1"></i>Lấy mã OTP');
                $('#hgb_display_otp_email').text(email);
                $('#hgb_otp_alert').addClass('d-none').removeClass('alert-success alert-danger');
                $('#hgb_otp_input').val('').removeClass('is-invalid is-valid');
                $('#hgb_modal_otp').modal('show');

                // Bắt đầu đếm ngược gửi lại
                startOtpCountdown(res.resend_in || 60);

                // Hỗ trợ test demo: Log mã ra console
                var demoCode = res.demo_debug || res.demo_otp || (res.data ? res.data.demo_debug : null);
                if (demoCode) {
                    console.log('%c[FashionKids DEMO OTP]: ' + demoCode, 'color: #10B981; font-weight: bold; font-size: 14px;');
                }
            })
            .fail(function (xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane mr-1"></i>Lấy mã OTP');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi gửi mã OTP.';
                alert(msg);
            });
    }

    $('#hgb_btn_request_otp').on('click', function () {
        triggerRequestOtp($(this));
    });

    $('#hgb_btn_resend_otp').on('click', function () {
        triggerRequestOtp($(this));
    });

    // ========================================================================
    // 4. XÁC NHẬN MÃ OTP TRONG MODAL KHÔNG TẢI LẠI TRANG
    // ========================================================================
    $('#hgb_form_verify_otp').on('submit', function (e) {
        e.preventDefault();
        var email = $.trim($('#hgb_display_otp_email').text());
        var otpInput = $('#hgb_otp_input');
        var otp = $.trim(otpInput.val());
        var alertBox = $('#hgb_otp_alert');
        var btnSubmit = $('#hgb_btn_submit_otp');

        if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
            otpInput.addClass('is-invalid');
            $('#hgb_otp_feedback').text('Mã xác thực phải gồm đúng 6 chữ số.');
            return;
        }

        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang xác thực...');

        sendPost('ajaxVerifyOtp', { email: email, otp: otp })
            .done(function (res) {
                btnSubmit.prop('disabled', false).text('Xác nhận OTP');
                alertBox.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message);
                clearInterval(timerInterval);

                setTimeout(function () {
                    $('#hgb_modal_otp').modal('hide');

                    // Khóa email và đánh dấu đã xác thực thành công
                    $('#hgb_reg_email').prop('readonly', true).addClass('is-valid');
                    $('#hgb_otp_action').addClass('d-none');
                    $('#hgb_otp_verified_badge').removeClass('d-none');

                    // Nếu chưa có badge, thay thế nút
                    if (!$('#hgb_otp_badge').length && !$('#hgb_otp_verified_badge').length) {
                        $('#hgb_btn_request_otp').replaceWith(
                            '<span class="badge badge-success px-3 py-2" id="hgb_otp_badge"><i class="fas fa-check-circle mr-1"></i> Đã xác thực OTP</span>'
                        );
                    }

                    // Mở khóa nút Đăng ký hoàn tất
                    $('#hgb_btn_final_register').prop('disabled', false);
                    $('#hgb_register_hint').addClass('d-none');
                }, 1000);
            })
            .fail(function (xhr) {
                btnSubmit.prop('disabled', false).text('Xác nhận OTP');
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Mã OTP không đúng!';
                alertBox.removeClass('d-none alert-success').addClass('alert-danger d-block').text(errMsg);
                otpInput.addClass('is-invalid');
                $('#hgb_otp_feedback').text(errMsg);
            });
    });

    // Cho phép đổi email nếu người dùng bấm "Đổi email"
    $('#hgb_btn_change_email').on('click', function () {
        $('#hgb_reg_email').prop('readonly', false).removeClass('is-valid is-invalid').focus();
        $('#hgb_otp_action').removeClass('d-none');
        $('#hgb_otp_verified_badge').addClass('d-none');
        $('#hgb_btn_final_register').prop('disabled', true);
        $('#hgb_register_hint').removeClass('d-none');
    });

    // Format chỉ nhận số cho ô OTP
    $('#hgb_otp_input').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
        $(this).removeClass('is-invalid');
        $('#hgb_otp_alert').addClass('d-none');
    });

    // Tự động focus ô nhập khi mở modal
    $('#hgb_modal_otp').on('shown.bs.modal', function () {
        $('#hgb_otp_input').trigger('focus');
    });

    // ========================================================================
    // 5. HOÀN TẤT ĐĂNG KÝ (AJAX)
    // ========================================================================
    $('#hgb_formRegister').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#hgb_btn_final_register');
        var $alert = $('#hgb_register_alert');

        if ($btn.prop('disabled')) {
            alert('Vui lòng xác thực mã OTP trước khi hoàn tất đăng ký!');
            return;
        }

        var postData = {
            ten_dang_nhap: $.trim($('#hgb_reg_username').val()),
            ho_va_ten:     $.trim($('#hgb_reg_fullname').val()),
            thu_dien_tu:   $.trim($('#hgb_reg_email').val()),
            so_dien_thoai: $.trim($('#hgb_reg_phone').val()),
            mat_khau:      $('#hgb_reg_password').val()
        };

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang đăng ký...');

        sendPost('ajaxRegister', postData)
            .done(function (res) {
                $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message);
                setTimeout(function () {
                    // Chuyển sang tab Đăng nhập
                    $('#tab-login').tab('show');
                    $('#hgb_login_username').val(postData.ten_dang_nhap);
                    $('#hgb_login_password').focus();
                }, 1200);
            })
            .fail(function (xhr) {
                var res = xhr.responseJSON;
                var msg = (res && res.message) ? res.message : 'Đăng ký thất bại, vui lòng kiểm tra lại thông tin.';
                $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
                $btn.prop('disabled', false).html('Hoàn Tất Đăng Ký <i class="fa-solid fa-arrow-right ml-1"></i>');
            });
    });

    // ========================================================================
    // 6. ĐĂNG NHẬP AJAX
    // ========================================================================
    $('#hgb_formLogin').on('submit', function (e) {
        e.preventDefault();
        var $alert = $('#hgb_login_alert');
        var $btn   = $('#hgb_btn_login');
        var user   = $.trim($('#hgb_login_username').val());
        var pass   = $('#hgb_login_password').val();

        if (!user || !pass) {
            $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text('Vui lòng nhập đầy đủ tài khoản và mật khẩu.');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang đăng nhập...');

        sendPost('login', { ten_dang_nhap: user, mat_khau: pass })
            .done(function (res) {
                $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message);
                var target = res.redirect || 'index.php?c=home';
                window.location.href = target;
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Đăng nhập thất bại, vui lòng kiểm tra lại.';
                $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
                $btn.prop('disabled', false).html('Đăng Nhập Ngay');
            });
    });
});
