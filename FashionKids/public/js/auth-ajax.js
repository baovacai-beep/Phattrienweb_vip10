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
    let fpCountdownTimer = null;

    function hgbGetTurnstileToken($scope) {
        if (!$scope || !$scope.length) return '';
        const val = $scope.find('input[name="cf-turnstile-response"]').val();
        return val ? String(val).trim() : '';
    }

    function hgbResetTurnstile() {
        if (window.turnstile && typeof window.turnstile.reset === 'function') {
            try {
                window.turnstile.reset();
            } catch (e) {}
        }
    }

    function hgbDetectEmailDomainTypo(email) {
        const atPos = email.lastIndexOf('@');
        if (atPos === -1) return 'Địa chỉ Email không đúng định dạng.';
        const domain = email.slice(atPos + 1).trim().toLowerCase();
        if (!domain || domain.indexOf('.') === -1) {
            return 'Tên miền Email không hợp lệ (ví dụ hợp lệ: ten@gmail.com).';
        }
        const exactTypos = {
            'gmail.cc': '@gmail.com',
            'gmail.co': '@gmail.com',
            'gmail.con': '@gmail.com',
            'gmail.cm': '@gmail.com',
            'gmail.om': '@gmail.com',
            'gmail.cmo': '@gmail.com',
            'gmail.cim': '@gmail.com',
            'gmail.vom': '@gmail.com',
            'gmail.xom': '@gmail.com',
            'gmail.coom': '@gmail.com',
            'gamil.com': '@gmail.com',
            'gmial.com': '@gmail.com',
            'gmal.com': '@gmail.com',
            'gmaill.com': '@gmail.com',
            'gnail.com': '@gmail.com',
            'gmai.com': '@gmail.com',
            'yahoo.cc': '@yahoo.com',
            'yahoo.con': '@yahoo.com',
            'yaho.com': '@yahoo.com',
            'yahho.com': '@yahoo.com',
            'outlook.cc': '@outlook.com',
            'outlok.com': '@outlook.com',
            'hotmail.cc': '@hotmail.com',
            'hotmal.com': '@hotmail.com'
        };
        if (exactTypos[domain]) {
            return 'Địa chỉ Email có vẻ bị gõ nhầm tên miền (@' + domain + '). Ý bạn có phải là ' + exactTypos[domain] + '?';
        }
        const labels = domain.split('.');
        const lastLabel = labels[labels.length - 1] || '';
        const badTlds = ['cc', 'con', 'cm', 'om', 'cmo', 'cim', 'vom', 'xom', 'coom'];
        if (lastLabel.length < 2 || badTlds.indexOf(lastLabel) !== -1) {
            return 'Đuôi tên miền Email ".' + lastLabel + '" không hợp lệ. Vui lòng kiểm tra lại (ví dụ @gmail.com).';
        }
        return null;
    }

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
        const email = input.val().trim().toLowerCase();
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

        const typoErr = hgbDetectEmailDomainTypo(email);
        if (typoErr) {
            input.removeClass('is-valid').addClass('is-invalid');
            feedback.text(typoErr);
            return;
        }

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxCheckDuplicate',
            type: 'POST',
            dataType: 'json',
            data: { field: 'thu_dien_tu', value: email },
            success: function (res) {
                if (res.status === 'duplicate' || res.exists === true) {
                    input.removeClass('is-valid').addClass('is-invalid');
                    feedback.text(res.message);
                } else {
                    input.removeClass('is-invalid').addClass('is-valid');
                }
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Địa chỉ Email chưa hợp lệ.';
                input.removeClass('is-valid').addClass('is-invalid');
                feedback.text(msg);
            }
        });
    });

    // Ẩn / Hiện mật khẩu bằng nút mắt
    $(document).on('click', '.hgb-toggle-pass-btn', function () {
        const targetSel = $(this).attr('data-target');
        const $input = $(targetSel);
        if (!$input.length) return;
        const isPass = $input.attr('type') === 'password';
        $input.attr('type', isPass ? 'text' : 'password');
        const $icon = $(this).find('i');
        $icon.toggleClass('fa-eye', !isPass).toggleClass('fa-eye-slash', isPass);
    });

    // Chuẩn hóa nhập số điện thoại chỉ chứa chữ số (tối đa 11 số)
    $('#hgb_reg_phone, #hgb_profile_phone').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
        $(this).removeClass('is-invalid');
    });

    // 3. Bấm Lấy mã OTP -> Gửi mail thật & Mở Modal
    function sendOtpRequest($triggerBtn, isResend) {
        const emailInput = $('#hgb_reg_email');
        const email = emailInput.val().trim().toLowerCase();
        const fullname = $('#hgb_reg_fullname').val().trim();
        const alertReg = $('#hgb_register_alert');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const typoErr = email ? hgbDetectEmailDomainTypo(email) : null;

        if (!email || !emailRegex.test(email) || typoErr || emailInput.hasClass('is-invalid')) {
            const msg = typoErr || 'Vui lòng nhập Email hợp lệ và không bị trùng trước khi lấy mã OTP!';
            emailInput.addClass('is-invalid').focus();
            if (isResend) {
                $('#hgb_otp_alert').removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
            } else {
                alertReg.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
            }
            return;
        }

        alertReg.addClass('d-none').removeClass('alert-danger alert-success');
        const originalHtml = isResend ? 'Gửi lại mã' : 'Lấy mã OTP';
        $triggerBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang gửi...');

        const tsRegisterToken = hgbGetTurnstileToken($('#hgb_turnstile_register'));

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxSendOtp',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                name: fullname,
                'cf-turnstile-response': tsRegisterToken
            },
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
                hgbResetTurnstile();
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Không thể gửi mã OTP lúc này. Vui lòng thử lại!';
                if (isResend) {
                    $('#hgb_otp_alert').removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
                } else {
                    alertReg.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
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

        btnSubmit.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xác thực...');

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
        const $username = $('#hgb_reg_username');
        const $fullname = $('#hgb_reg_fullname');
        const $phone = $('#hgb_reg_phone');
        const $pass1 = $('#hgb_reg_password');
        const $pass2 = $('#hgb_reg_password2');

        const username = $username.val().trim();
        const fullname = $fullname.val().trim();
        const phone = $phone.val().trim();
        const password = $pass1.val();
        const password2 = $pass2.val();

        $('#hgb_reg_username, #hgb_reg_fullname, #hgb_reg_phone, #hgb_reg_password, #hgb_reg_password2').removeClass('is-invalid');
        alertReg.addClass('d-none').removeClass('alert-danger alert-success');

        if (username.length < 4) {
            $username.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('Tên đăng nhập phải có ít nhất 4 ký tự.');
            $username.focus();
            return;
        }
        if (fullname.length < 2) {
            $fullname.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('Vui lòng nhập đầy đủ họ và tên.');
            $fullname.focus();
            return;
        }
        if (!/^\d{9,11}$/.test(phone)) {
            $phone.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('Số điện thoại phải gồm từ 9 đến 11 chữ số.');
            $phone.focus();
            return;
        }
        if (password.length < 6) {
            $pass1.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('Mật khẩu tối thiểu 6 ký tự.');
            $pass1.focus();
            return;
        }
        if (password !== password2) {
            $pass2.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('Mật khẩu nhập lại không khớp.');
            $pass2.focus();
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang tạo tài khoản...');

        const formData = {
            username: username,
            password: password,
            fullname: fullname,
            email:    $('#hgb_reg_email').val().trim(),
            phone:    phone,
            address:  $('#hgb_reg_address').length ? $('#hgb_reg_address').val().trim() : ''
        };

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxFinalRegister',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (res) {
                btn.prop('disabled', false).text('Đăng ký tài khoản');
                alertReg.addClass('d-none').removeClass('alert-danger alert-success');
                $('#tab-login').tab('show');
                $('#hgb_btn_login').prop('disabled', false).text('Đăng Nhập Ngay');
                $('#hgb_login_username').val(formData.username).removeClass('is-invalid');
                $('#hgb_login_password').val(formData.password).removeClass('is-invalid');
                $('#hgb_login_alert')
                    .removeClass('d-none alert-danger')
                    .addClass('alert-success d-block')
                    .text(res.message || 'Đăng ký tài khoản thành công! Bạn có thể bấm Đăng Nhập Ngay.');
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
        const username = $('#hgb_login_username').val().trim();
        const password = $('#hgb_login_password').val();
        const csrfToken = $(this).find('input[name="csrf_token"]').val() || $('meta[name="csrf-token"]').attr('content') || '';
        const redirectUrl = $(this).find('input[name="redirect"]').val() || '';

        if (!username || !password) {
            alertBox.removeClass('d-none alert-success').addClass('alert-danger d-block').text('Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.');
            return;
        }

        alertBox.addClass('d-none').removeClass('alert-danger alert-success');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang đăng nhập...');
        const tsLoginToken = hgbGetTurnstileToken($('#hgb_turnstile_login'));

        $.ajax({
            url: ENDPOINT + '?c=auth&a=login',
            type: 'POST',
            dataType: 'json',
            data: {
                ten_dang_nhap: username,
                mat_khau: password,
                csrf_token: csrfToken,
                redirect: redirectUrl,
                'cf-turnstile-response': tsLoginToken
            },
            success: function (res) {
                alertBox.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message);
                window.location.href = res.redirect || '/FashionKids/views/pages/profile.php';
            },
            error: function (xhr) {
                btn.prop('disabled', false).text('Đăng Nhập Ngay');
                hgbResetTurnstile();
                const errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Tên đăng nhập hoặc mật khẩu không chính xác!';
                alertBox.removeClass('d-none alert-success').addClass('alert-danger d-block').text(errMsg);
            }
        });
    });

    // 7. Cập nhật thông tin hồ sơ thành viên AJAX
    $('#hgb_formProfileUpdate').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#hgb_btnUpdateProfile');
        const $alert = $('#hgb_profile_alert');
        const $unameInput = $('#hgb_profile_username');
        const canEditUname = $unameInput.length && !$unameInput.prop('readonly');
        const username = canEditUname ? ($unameInput.val() || '').trim() : '';
        const fullname = ($('#hgb_profile_fullname').val() || '').trim();
        const phone = ($('#hgb_profile_phone').val() || '').trim();
        const address = ($('#hgb_profile_address').val() || '').trim();

        $alert.addClass('d-none').removeClass('alert-danger alert-success');
        if (canEditUname && !/^[a-zA-Z0-9_]{4,30}$/.test(username)) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Tên đăng nhập phải từ 4-30 ký tự (chữ không dấu, số hoặc _).');
            $unameInput.focus();
            return;
        }
        if (fullname.length < 2) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Họ và tên phải có ít nhất 2 ký tự.');
            return;
        }
        if (phone !== '' && !/^\d{9,11}$/.test(phone)) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Số điện thoại phải gồm từ 9 đến 11 chữ số.');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu...');

        const payload = {
            ho_va_ten: fullname,
            so_dien_thoai: phone,
            dia_chi: address
        };
        if (canEditUname) {
            payload.ten_dang_nhap = username;
        }

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxUpdateProfile',
            type: 'POST',
            dataType: 'json',
            data: payload,
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật Thông Tin');
            },
            success: function (res) {
                $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message || 'Cập nhật thông tin thành công!');
                $('.vip-card-holder').text(fullname);
                $('#userMenuDropdown > span.font-weight-bold').text(fullname);
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Không thể cập nhật thông tin!';
                $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
            }
        });
    });

    // 8. Quản trị Người dùng: Khóa / Mở khóa tài khoản AJAX
    $(document).on('click', '.hgb-btn-toggle-user-status', function () {
        const $btn = $(this);
        const userId = parseInt($btn.attr('data-user-id'), 10) || 0;
        const nextStatus = parseInt($btn.attr('data-next-status'), 10);
        const $alert = $('#hgb_admin_users_alert');
        if (!userId || (nextStatus !== 0 && nextStatus !== 1)) {
            return;
        }

        $alert.addClass('d-none').removeClass('alert-danger alert-success');
        $btn.prop('disabled', true);

        $.ajax({
            url: ENDPOINT + '?c=admin&a=ajaxToggleUserStatus',
            type: 'POST',
            dataType: 'json',
            data: { user_id: userId, status: nextStatus },
            complete: function () {
                $btn.prop('disabled', false);
            },
            success: function (res) {
                const isActive = parseInt(res.new_status, 10) === 1;
                const $statusCell = $('#hgb_userStatusCell_' + userId);
                if (isActive) {
                    $statusCell.html('<span class="badge badge-success badge-pill px-2 py-1">Hoạt động</span>');
                    $btn.removeClass('btn-outline-success').addClass('btn-outline-danger')
                        .attr('title', 'Khóa tài khoản')
                        .attr('data-next-status', '0')
                        .html('<i class="fa-solid fa-lock"></i>');
                } else {
                    $statusCell.html('<span class="badge badge-danger badge-pill px-2 py-1">Đã khóa</span>');
                    $btn.removeClass('btn-outline-danger').addClass('btn-outline-success')
                        .attr('title', 'Mở khóa tài khoản')
                        .attr('data-next-status', '1')
                        .html('<i class="fa-solid fa-lock-open"></i>');
                }
                $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message || 'Cập nhật trạng thái thành công.');
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Không thể thay đổi trạng thái tài khoản.';
                $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
            }
        });
    });

    // 9. Quản trị Người dùng: Lưu cấu hình mức điểm tối thiểu & % giảm giá VIP AJAX
    $('#hgb_formVipTiers').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#hgb_btnSaveVipTiers');
        const $alert = $('#hgb_admin_users_alert');
        const origHtml = $btn.html();

        $alert.addClass('d-none').removeClass('alert-danger alert-success');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu...');

        $.ajax({
            url: ENDPOINT + '?c=admin&a=ajaxSaveVipTiers',
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message || 'Đã lưu cấu hình hạng VIP thành công!');
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Không thể lưu cấu hình hạng VIP!';
                $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
            }
        });
    });

    // 10. Quên mật khẩu qua Email OTP 3 bước (#modalForgotPassword)
    function startFpCountdown(seconds) {
        clearInterval(fpCountdownTimer);
        let remaining = parseInt(seconds, 10) || 60;
        const $btnResend = $('#hgb_fp_btn_resend');
        const $timerText = $('#hgb_fp_countdown');
        $btnResend.prop('disabled', true).addClass('disabled text-muted').removeClass('text-primary');
        $timerText.text(remaining + 's');

        fpCountdownTimer = setInterval(function () {
            remaining--;
            if (remaining <= 0) {
                clearInterval(fpCountdownTimer);
                $timerText.text('');
                $btnResend.prop('disabled', false).removeClass('disabled text-muted').addClass('text-primary');
            } else {
                $timerText.text(remaining + 's');
            }
        }, 1000);
    }

    function showFpAlert(type, msg) {
        const $alert = $('#hgb_fp_alert');
        $alert.removeClass('d-none alert-danger alert-success alert-info');
        if (type === 'success') {
            $alert.addClass('alert-success d-block').text(msg);
        } else {
            $alert.addClass('alert-danger d-block').text(msg);
        }
    }

    $('#modalForgotPassword').on('show.bs.modal', function () {
        clearInterval(fpCountdownTimer);
        $('#hgb_fp_alert').addClass('d-none').removeClass('alert-danger alert-success').text('');
        $('#hgb_fp_step1').removeClass('d-none');
        $('#hgb_fp_step2, #hgb_fp_step3').addClass('d-none');
        $('#hgb_fp_email').val('');
        $('#hgb_fp_verified_email').val('');
        $('#hgb_fp_otp').val('');
        $('#hgb_fp_new_pass, #hgb_fp_confirm_pass').val('');
        $('#hgb_fp_countdown').text('');
    });

    $('#hgb_fp_otp').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    function requestResetOtp(email, $btn, origHtml) {
        const tsForgotToken = hgbGetTurnstileToken($('#hgb_turnstile_forgot'));
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang gửi...');
        $('#hgb_fp_alert').addClass('d-none').removeClass('alert-danger alert-success').text('');

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxSendResetOtp',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                'cf-turnstile-response': tsForgotToken
            },
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                $('#hgb_fp_verified_email').val(email);
                $('#hgb_fp_display_email').text(email);
                $('#hgb_fp_step1').addClass('d-none');
                $('#hgb_fp_step2').removeClass('d-none');
                $('#hgb_fp_otp').val('').focus();
                showFpAlert('success', (res && res.message) ? res.message : 'Mã OTP khôi phục đã được gửi tới Email của bạn.');
                startFpCountdown((res && res.resend_in) ? res.resend_in : 60);
            },
            error: function (xhr) {
                hgbResetTurnstile();
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể gửi mã OTP khôi phục lúc này. Vui lòng thử lại!';
                showFpAlert('error', msg);
            }
        });
    }

    $('#hgb_fp_form_email').on('submit', function (e) {
        e.preventDefault();
        const email = ($('#hgb_fp_email').val() || '').trim().toLowerCase();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const typoErr = email ? hgbDetectEmailDomainTypo(email) : null;

        if (!email || !emailRegex.test(email) || typoErr) {
            showFpAlert('error', typoErr || 'Vui lòng nhập địa chỉ Email hợp lệ đã đăng ký!');
            $('#hgb_fp_email').focus();
            return;
        }
        const $btn = $('#hgb_fp_btn_send_otp');
        requestResetOtp(email, $btn, '<i class="fa-solid fa-paper-plane mr-1"></i>Gửi Mã OTP Khôi Phục');
    });

    $('#hgb_fp_btn_resend').on('click', function () {
        const email = ($('#hgb_fp_verified_email').val() || '').trim().toLowerCase();
        if (!email) return;
        requestResetOtp(email, $(this), 'Gửi lại mã');
    });

    $('#hgb_fp_btn_back_email').on('click', function () {
        clearInterval(fpCountdownTimer);
        $('#hgb_fp_step2').addClass('d-none');
        $('#hgb_fp_step1').removeClass('d-none');
        $('#hgb_fp_alert').addClass('d-none').text('');
        $('#hgb_fp_email').focus();
    });

    $('#hgb_fp_form_otp').on('submit', function (e) {
        e.preventDefault();
        const email = ($('#hgb_fp_verified_email').val() || '').trim().toLowerCase();
        const otp = ($('#hgb_fp_otp').val() || '').trim();
        if (!/^\d{6}$/.test(otp)) {
            showFpAlert('error', 'Mã OTP khôi phục phải gồm đúng 6 chữ số.');
            $('#hgb_fp_otp').focus();
            return;
        }
        const $btn = $('#hgb_fp_btn_verify_otp');
        const origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xác nhận...');

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxVerifyResetOtp',
            type: 'POST',
            dataType: 'json',
            data: { email: email, otp: otp },
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                clearInterval(fpCountdownTimer);
                $('#hgb_fp_step2').addClass('d-none');
                $('#hgb_fp_step3').removeClass('d-none');
                showFpAlert('success', (res && res.message) ? res.message : 'Xác thực OTP thành công! Mời nhập mật khẩu mới.');
                $('#hgb_fp_new_pass').focus();
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Mã OTP không chính xác hoặc đã hết hạn!';
                showFpAlert('error', msg);
            }
        });
    });

    $('#hgb_fp_form_reset').on('submit', function (e) {
        e.preventDefault();
        const email = ($('#hgb_fp_verified_email').val() || '').trim().toLowerCase();
        const newPass = $('#hgb_fp_new_pass').val() || '';
        const confirmPass = $('#hgb_fp_confirm_pass').val() || '';

        if (newPass.length < 6) {
            showFpAlert('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
            $('#hgb_fp_new_pass').focus();
            return;
        }
        if (newPass !== confirmPass) {
            showFpAlert('error', 'Mật khẩu nhập lại không khớp.');
            $('#hgb_fp_confirm_pass').focus();
            return;
        }

        const $btn = $('#hgb_fp_btn_reset_submit');
        const origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang cập nhật...');

        $.ajax({
            url: ENDPOINT + '?c=auth&a=ajaxResetPassword',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                new_password: newPass,
                confirm_password: confirmPass
            },
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                const msg = (res && res.message) ? res.message : 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay.';
                $('#modalForgotPassword').modal('hide');
                $('#tab-login').tab('show');
                $('#hgb_login_alert')
                    .removeClass('d-none alert-danger')
                    .addClass('alert-success d-block')
                    .text(msg);
                $('#hgb_login_password').val('').focus();
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể đặt lại mật khẩu lúc này. Vui lòng thử lại!';
                showFpAlert('error', msg);
            }
        });
    });

    // 11. Google OAuth Onboarding (#hgb_formGoogleComplete)
    if ($('#hgb_formGoogleComplete').length) {
        let ggProvinces = [];

        function syncGgAddressPreview() {
            const parts = [];
            const street = ($('#hgb_gg_street').val() || '').trim();
            const ward = ($('#hgb_gg_ward').val() || '').trim();
            const dist = ($('#hgb_gg_district').val() || '').trim();
            const prov = ($('#hgb_gg_province').val() || '').trim();
            if (street) parts.push(street);
            if (ward) parts.push(ward);
            if (dist) parts.push(dist);
            if (prov) parts.push(prov);
            if (parts.length > 0) {
                $('#hgb_gg_address').val(parts.join(', ')).removeClass('is-invalid');
            }
        }

        // Tải danh sách Tỉnh/Huyện/Xã 3 cấp cho form Google Onboarding
        fetch('https://provinces.open-api.vn/api/?depth=3')
            .then(function (r) { return r.ok ? r.json() : []; })
            .then(function (data) {
                if (!Array.isArray(data) || !data.length) return;
                ggProvinces = data;
                const $prov = $('#hgb_gg_province');
                data.forEach(function (p) {
                    $prov.append($('<option>').val(p.name).text(p.name));
                });
            })
            .catch(function () {});

        $('#hgb_gg_province').on('change', function () {
            const pName = $(this).val();
            const $dist = $('#hgb_gg_district').empty().append('<option value="">-- Quận / Huyện --</option>');
            const $ward = $('#hgb_gg_ward').empty().append('<option value="">-- Phường / Xã --</option>').prop('disabled', true);
            const pObj = ggProvinces.find(function (p) { return p.name === pName; });
            if (pObj && Array.isArray(pObj.districts)) {
                pObj.districts.forEach(function (d) {
                    $dist.append($('<option>').val(d.name).text(d.name));
                });
                $dist.prop('disabled', false);
            } else {
                $dist.prop('disabled', true);
            }
            $ward.prop('disabled', true);
            syncGgAddressPreview();
        });

        $('#hgb_gg_district').on('change', function () {
            const pName = $('#hgb_gg_province').val();
            const dName = $(this).val();
            const $ward = $('#hgb_gg_ward').empty().append('<option value="">-- Phường / Xã --</option>');
            const pObj = ggProvinces.find(function (p) { return p.name === pName; });
            const dObj = pObj && Array.isArray(pObj.districts)
                ? pObj.districts.find(function (d) { return d.name === dName; })
                : null;
            if (dObj && Array.isArray(dObj.wards)) {
                dObj.wards.forEach(function (w) {
                    $ward.append($('<option>').val(w.name).text(w.name));
                });
                $ward.prop('disabled', false);
            } else {
                $ward.prop('disabled', true);
            }
            syncGgAddressPreview();
        });

        $('#hgb_gg_ward').on('change', syncGgAddressPreview);
        $('#hgb_gg_street').on('input', syncGgAddressPreview);

        $('#hgb_gg_phone').on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 11);
            $(this).removeClass('is-invalid');
        });

        $('#hgb_gg_username').on('blur', function () {
            const $input = $(this);
            const uname = ($input.val() || '').trim();
            const $fb = $input.siblings('.invalid-feedback');
            if (!uname) {
                $input.removeClass('is-valid is-invalid');
                return;
            }
            if (!/^[a-zA-Z0-9_]{4,30}$/.test(uname)) {
                $input.removeClass('is-valid').addClass('is-invalid');
                $fb.text('Tên đăng nhập phải từ 4-30 ký tự (chữ không dấu, số hoặc _).');
                return;
            }
            $.post(ENDPOINT + '?c=auth&a=ajaxCheckDuplicate', {
                field: 'ten_dang_nhap',
                value: uname,
                exclude_self: 1
            }, function (res) {
                if (res.status === 'duplicate' || res.exists === true) {
                    $input.removeClass('is-valid').addClass('is-invalid');
                    $fb.text(res.message || 'Tên đăng nhập này đã có người dùng!');
                } else {
                    $input.removeClass('is-invalid').addClass('is-valid');
                }
            }, 'json');
        });

        $('#hgb_formGoogleComplete').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $alert = $('#hgb_gg_alert');
            const $btn = $('#hgb_btn_gg_complete');
            const $uname = $('#hgb_gg_username');
            const $fname = $('#hgb_gg_fullname');
            const $phone = $('#hgb_gg_phone');
            const $addr = $('#hgb_gg_address');
            const $pass = $('#hgb_gg_password');

            const uname = ($uname.val() || '').trim();
            const fname = ($fname.val() || '').trim();
            const phone = ($phone.val() || '').trim();
            const addr = ($addr.val() || '').trim();
            const pass = $pass.val() || '';

            $form.find('.is-invalid').removeClass('is-invalid');
            $alert.addClass('d-none').removeClass('alert-danger alert-success');

            if (!/^[a-zA-Z0-9_]{4,30}$/.test(uname)) {
                $uname.addClass('is-invalid').siblings('.invalid-feedback').text('Tên đăng nhập phải từ 4-30 ký tự (chữ không dấu, số hoặc _).');
                $uname.focus();
                return;
            }
            if (fname.length < 2) {
                $fname.addClass('is-invalid').siblings('.invalid-feedback').text('Họ và tên phải có ít nhất 2 ký tự.');
                $fname.focus();
                return;
            }
            if (!/^\d{9,11}$/.test(phone)) {
                $phone.addClass('is-invalid').siblings('.invalid-feedback').text('Số điện thoại phải gồm từ 9 đến 11 chữ số.');
                $phone.focus();
                return;
            }
            if (addr.length < 5) {
                $addr.addClass('is-invalid').siblings('.invalid-feedback').text('Vui lòng nhập đầy đủ địa chỉ giao hàng (ít nhất 5 ký tự).');
                $addr.focus();
                return;
            }
            if (pass !== '' && pass.length < 6) {
                $pass.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('Mật khẩu tối thiểu 6 ký tự (hoặc để trống).');
                $pass.focus();
                return;
            }

            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu hồ sơ...');

            $.ajax({
                url: ENDPOINT + '?c=auth&a=ajaxCompleteGoogleProfile',
                type: 'POST',
                dataType: 'json',
                data: $form.serialize(),
                complete: function () {
                    $btn.prop('disabled', false).html(origHtml);
                },
                success: function (res) {
                    $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text(res.message || 'Hoàn tất tài khoản thành công!');
                    window.location.href = res.redirect || '/FashionKids/views/pages/profile.php';
                },
                error: function (xhr) {
                    const res = xhr.responseJSON || {};
                    const msg = res.message || 'Không thể hoàn tất thông tin tài khoản Google lúc này.';
                    $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(msg);
                    if (res.expired) {
                        setTimeout(function () {
                            window.location.href = '/FashionKids/views/pages/login-register.php';
                        }, 1800);
                    }
                }
            });
        });
    }
});
