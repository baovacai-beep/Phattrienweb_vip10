/**
 * FASHIONKIDS - AUTH AJAX (MH 09 & MH 20)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_)
 *
 *  1. Kiểm tra trùng Tên đăng nhập / Email realtime (sự kiện blur)  -> AuthController::ajaxCheckDuplicate
 *  2. Gửi & xác thực mã OTP 6 chữ số qua Modal                      -> ajaxSendOtp / ajaxVerifyOtp
 *  3. Hoàn tất đăng ký (chỉ mở khóa khi OTP đã xác thực)           -> ajaxRegister
 *  4. Đăng nhập không tải lại trang                                 -> login
 *
 * Không dùng Inline CSS: mọi trạng thái hiển thị đều thay đổi qua class Bootstrap 4
 * (is-valid / is-invalid / d-none / alert-*).
 */
(function ($) {
    'use strict';

    // Chỉ chạy trên trang có form xác thực
    if (!$('#hgb_formRegister').length && !$('#hgb_formLogin').length) {
        return;
    }

    // ------------------------------------------------------------------------
    // Cấu hình & tiện ích chung
    // ------------------------------------------------------------------------
    var ENDPOINT = $('meta[name="fk-endpoint"]').attr('content') || '/FashionKids/index.php';
    var CSRF     = $('meta[name="csrf-token"]').attr('content') || '';

    var RE_USERNAME = /^[A-Za-z0-9_]{4,30}$/;
    var RE_EMAIL    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var RE_PHONE    = /^0\d{9}$/;

    // Gửi CSRF token bằng header RIÊNG cho từng request (không dùng $.ajaxSetup để tránh
    // làm hỏng các request cross-origin khác như Provinces Open API).
    function post(action, data) {
        return $.ajax({
            url: ENDPOINT + '?c=auth&a=' + action,
            type: 'POST',
            dataType: 'json',
            data: data,
            headers: { 'X-CSRF-Token': CSRF },
            timeout: 10000
        });
    }

    function errMsg(xhr, fallback) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }
        return xhr.status === 0 ? 'Không kết nối được máy chủ. Vui lòng kiểm tra mạng.' : fallback;
    }

    function showAlert($box, type, msg) {
        $box.removeClass('d-none alert-success alert-danger alert-info alert-warning')
            .addClass('alert-' + type)
            .text(msg);
    }

    function hideAlert($box) {
        $box.addClass('d-none').text('');
    }

    function setBusy($btn, busy, busyHtml) {
        if (busy) {
            if ($btn.data('orig') === undefined) {
                $btn.data('orig', $btn.html());
            }
            $btn.prop('disabled', true).html(busyHtml || '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang xử lý...');
        } else {
            $btn.html($btn.data('orig')).prop('disabled', false);
            $btn.removeData('orig');
        }
    }

    /** ok === true -> .is-valid | ok === false -> .is-invalid + thông điệp | null -> xóa trạng thái */
    function mark($input, ok, msg) {
        $input.removeClass('is-valid is-invalid');
        if (ok === true) {
            $input.addClass('is-valid');
        } else if (ok === false) {
            $input.addClass('is-invalid');
            $input.closest('.form-group').find('.invalid-feedback').first().text(msg || '');
        }
    }

    // ------------------------------------------------------------------------
    // Các ô nhập của form đăng ký
    // ------------------------------------------------------------------------
    var $form      = $('#hgb_formRegister');
    var $username  = $('#hgb_reg_username');
    var $fullname  = $('#hgb_reg_fullname');
    var $email     = $('#hgb_reg_email');
    var $phone     = $('#hgb_reg_phone');
    var $pass      = $('#hgb_reg_password');
    var $pass2     = $('#hgb_reg_password2');
    var $btnFinal  = $('#hgb_btn_final_register');
    var $hint      = $('#hgb_register_hint');
    var $regAlert  = $('#hgb_register_alert');

    var state   = { otpVerified: false };
    var reqSeq  = { username: 0, email: 0 };   // Bỏ qua phản hồi cũ khi người dùng gõ tiếp

    /** Nút "Hoàn tất đăng ký" chỉ mở khi đã xác thực OTP và không còn ô nào đang lỗi */
    function refreshSubmit() {
        var hasError = $form.find('.is-invalid').length > 0;
        var enabled  = state.otpVerified && !hasError;
        $btnFinal.prop('disabled', !enabled);
        $hint.toggleClass('d-none', state.otpVerified);
    }

    // ---- Validator phía client (server vẫn kiểm tra lại toàn bộ) ----
    function validateUsernameFormat() {
        var v = $.trim($username.val());
        if (!RE_USERNAME.test(v)) {
            mark($username, false, 'Tên đăng nhập 4-30 ký tự, chỉ gồm chữ không dấu, số và dấu gạch dưới.');
            return false;
        }
        return true;
    }

    function validateEmailFormat() {
        var v = $.trim($email.val());
        if (!RE_EMAIL.test(v)) {
            mark($email, false, 'Định dạng email không hợp lệ.');
            return false;
        }
        return true;
    }

    function validateFullname() {
        var v = $.trim($fullname.val());
        if (v.length < 2) {
            mark($fullname, false, 'Vui lòng nhập họ và tên (tối thiểu 2 ký tự).');
            return false;
        }
        mark($fullname, true);
        return true;
    }

    function validatePhone() {
        if (!RE_PHONE.test($.trim($phone.val()))) {
            mark($phone, false, 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.');
            return false;
        }
        mark($phone, true);
        return true;
    }

    function validatePassword() {
        if ($pass.val().length < 6) {
            mark($pass, false, 'Mật khẩu tối thiểu 6 ký tự.');
            return false;
        }
        mark($pass, true);
        return true;
    }

    function validatePassword2() {
        if ($pass2.val() === '' || $pass2.val() !== $pass.val()) {
            mark($pass2, false, 'Mật khẩu nhập lại không trùng khớp.');
            return false;
        }
        mark($pass2, true);
        return true;
    }

    // ------------------------------------------------------------------------
    // 1. Kiểm tra trùng lặp realtime (blur)
    // ------------------------------------------------------------------------
    function checkDuplicate(field, $input, key, formatOk) {
        if (!formatOk()) {
            refreshSubmit();
            return;
        }
        var id = ++reqSeq[key];
        post('ajaxCheckDuplicate', { field: field, value: $.trim($input.val()) })
            .done(function (res) {
                if (id !== reqSeq[key]) { return; }          // Phản hồi cũ -> bỏ qua
                if (res.status === 'duplicate') {
                    mark($input, false, res.message);
                } else {
                    mark($input, true);
                }
            })
            .fail(function (xhr) {
                if (id !== reqSeq[key]) { return; }
                // Lỗi hạ tầng (CSDL tắt, mất mạng...) không phải lỗi của người dùng -> không tô đỏ ô nhập
                mark($input, null);
                showAlert($regAlert, 'warning', errMsg(xhr, 'Không thể kiểm tra trùng lặp lúc này.'));
            })
            .always(refreshSubmit);
    }

    $username.on('input', function () {
        reqSeq.username++;
        mark($username, null);
        hideAlert($regAlert);
        refreshSubmit();
    }).on('blur', function () {
        checkDuplicate('ten_dang_nhap', $username, 'username', validateUsernameFormat);
    });

    $email.on('input', function () {
        reqSeq.email++;
        mark($email, null);
        hideAlert($regAlert);
        refreshSubmit();
    }).on('blur', function () {
        if ($email.prop('readonly')) { return; }
        checkDuplicate('thu_dien_tu', $email, 'email', validateEmailFormat);
    });

    $fullname.on('blur', function () { validateFullname(); refreshSubmit(); })
             .on('input', function () { mark($fullname, null); refreshSubmit(); });
    $phone.on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);   // Chỉ cho nhập số
        mark($phone, null);
        refreshSubmit();
    }).on('blur', function () { validatePhone(); refreshSubmit(); });
    $pass.on('blur', function () { validatePassword(); if ($pass2.val()) { validatePassword2(); } refreshSubmit(); })
         .on('input', function () { mark($pass, null); refreshSubmit(); });
    $pass2.on('blur input', function () { validatePassword2(); refreshSubmit(); });

    // ------------------------------------------------------------------------
    // 2. OTP: gửi mã, mở modal, đếm ngược, xác thực
    // ------------------------------------------------------------------------
    var $modal      = $('#hgb_modal_otp');
    var $otpAlert   = $('#hgb_otp_alert');
    var $boxes      = $('.hgb-otp-box');
    var $btnRequest = $('#hgb_btn_request_otp');
    var $btnResend  = $('#hgb_btn_resend_otp');
    var $timer      = $('#hgb_countdownTimer');
    var timerId     = null;

    function startTimer(seconds) {
        var left = Math.max(parseInt(seconds, 10) || 0, 0);
        clearInterval(timerId);
        if (left <= 0) {
            $timer.addClass('d-none');
            $btnResend.removeClass('d-none');
            return;
        }
        $btnResend.addClass('d-none');
        $timer.removeClass('d-none').text(left + 's');
        timerId = setInterval(function () {
            left--;
            if (left <= 0) {
                clearInterval(timerId);
                $timer.addClass('d-none');
                $btnResend.removeClass('d-none');
            } else {
                $timer.text(left + 's');
            }
        }, 1000);
    }

    function getOtp() {
        var code = '';
        $boxes.each(function () { code += $(this).val(); });
        return code;
    }

    function clearBoxes() {
        $boxes.val('').removeClass('is-invalid');
        $boxes.first().trigger('focus');
    }

    function openOtpModal(email, resendIn, note) {
        $('#hgb_display_otp_email').text(email);
        hideAlert($otpAlert);
        if (note) { showAlert($otpAlert, 'info', note); }
        $boxes.val('').removeClass('is-invalid');
        startTimer(resendIn);
        $modal.modal('show');
    }

    $modal.on('shown.bs.modal', function () { $boxes.first().trigger('focus'); });

    // Ô OTP: chỉ nhận số, tự nhảy ô, lùi ô khi Backspace, hỗ trợ dán cả chuỗi 6 số
    $boxes.on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 1);
        $boxes.removeClass('is-invalid');
        if (this.value) { $(this).next('.hgb-otp-box').trigger('focus'); }
    }).on('keydown', function (e) {
        if (e.key === 'Backspace' && !this.value) {
            $(this).prev('.hgb-otp-box').trigger('focus').val('');
        }
    }).on('paste', function (e) {
        var data = ((e.originalEvent.clipboardData || window.clipboardData).getData('text') || '').replace(/\D/g, '').slice(0, 6);
        if (!data) { return; }
        e.preventDefault();
        $boxes.each(function (i) { this.value = data.charAt(i) || ''; });
        $boxes.eq(Math.min(data.length, 6) - 1).trigger('focus');
    });

    function requestOtp($trigger) {
        var email = $.trim($email.val());
        if (!RE_EMAIL.test(email)) {
            mark($email, false, 'Vui lòng nhập Email hợp lệ trước khi lấy mã OTP.');
            $email.trigger('focus');
            refreshSubmit();
            return;
        }

        setBusy($trigger, true, '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang gửi...');
        post('ajaxSendOtp', { email: email })
            .done(function (res) {
                mark($email, true);
                var note = res.demo_otp ? ('[CHẾ ĐỘ DEMO] Mã OTP của bạn là: ' + res.demo_otp) : '';
                if (res.demo_otp && window.console) {
                    console.info('[FashionKids DEMO OTP]: ' + res.demo_otp);
                }
                openOtpModal(email, res.resend_in, note);
            })
            .fail(function (xhr) {
                var r = xhr.responseJSON || {};
                if (xhr.status === 409) {                      // Email đã có tài khoản
                    mark($email, false, r.message);
                } else if (xhr.status === 429) {               // Gửi quá nhanh -> mở lại modal với thời gian chờ còn lại
                    openOtpModal(email, r.retry_after, r.message);
                } else {
                    showAlert($regAlert, 'danger', errMsg(xhr, 'Không thể gửi mã OTP.'));
                }
            })
            .always(function () {
                setBusy($trigger, false);
                refreshSubmit();
            });
    }

    $btnRequest.on('click', function () { requestOtp($btnRequest); });
    $btnResend.on('click', function () { requestOtp($btnResend); });

    $('#hgb_form_verify_otp').on('submit', function (e) {
        e.preventDefault();
        var email = $('#hgb_display_otp_email').text();
        var otp   = getOtp();
        var $btn  = $('#hgb_btn_submit_otp');

        if (otp.length !== 6) {
            showAlert($otpAlert, 'danger', 'Vui lòng nhập đủ 6 chữ số OTP.');
            $boxes.filter(function () { return !this.value; }).addClass('is-invalid').first().trigger('focus');
            return;
        }

        setBusy($btn, true, '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang xác thực...');
        post('ajaxVerifyOtp', { email: email, otp: otp })
            .done(function (res) {
                showAlert($otpAlert, 'success', res.message);
                state.otpVerified = true;
                clearInterval(timerId);
                setTimeout(function () {
                    $modal.modal('hide');
                    lockVerifiedEmail();
                    refreshSubmit();
                }, 800);
            })
            .fail(function (xhr) {
                var r = xhr.responseJSON || {};
                showAlert($otpAlert, 'danger', errMsg(xhr, 'Mã OTP không đúng!'));
                $boxes.addClass('is-invalid');
                // Mã đã bị hủy phía server (hết hạn / sai quá số lần) -> cho phép gửi lại ngay
                if (r.code === 'expired' || r.code === 'locked' || r.code === 'missing') {
                    startTimer(0);
                }
                $boxes.first().trigger('focus');
            })
            .always(function () { setBusy($btn, false); });
    });

    // Sau khi xác thực: khóa ô email, thay nút lấy mã bằng huy hiệu "đã xác thực"
    function lockVerifiedEmail() {
        $email.prop('readonly', true).addClass('rounded');
        mark($email, true);
        $('#hgb_otp_action').addClass('d-none');
        $('#hgb_otp_verified_badge').removeClass('d-none');
    }

    $('#hgb_btn_change_email').on('click', function () {
        state.otpVerified = false;
        $email.prop('readonly', false).removeClass('rounded').trigger('focus');
        mark($email, null);
        $('#hgb_otp_action').removeClass('d-none');
        $('#hgb_otp_verified_badge').addClass('d-none');
        refreshSubmit();
    });

    // ------------------------------------------------------------------------
    // 3. Hoàn tất đăng ký
    // ------------------------------------------------------------------------
    function resetRegisterForm() {
        $form[0].reset();
        $form.find('.form-control').removeClass('is-valid is-invalid rounded').prop('readonly', false);
        $('#hgb_otp_action').removeClass('d-none');
        $('#hgb_otp_verified_badge').addClass('d-none');
        state.otpVerified = false;
        refreshSubmit();
    }

    $form.on('submit', function (e) {
        e.preventDefault();
        hideAlert($regAlert);

        var allOk = [validateUsernameFormat(), validateFullname(), validateEmailFormat(),
                     validatePhone(), validatePassword(), validatePassword2()].every(Boolean);
        if (!state.otpVerified) {
            showAlert($regAlert, 'warning', 'Vui lòng xác thực email bằng mã OTP trước khi đăng ký.');
            return;
        }
        if (!allOk) { refreshSubmit(); return; }

        setBusy($btnFinal, true, '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang tạo tài khoản...');
        post('ajaxRegister', {
            ten_dang_nhap: $.trim($username.val()),
            ho_va_ten:     $.trim($fullname.val()),
            thu_dien_tu:   $.trim($email.val()),
            so_dien_thoai: $.trim($phone.val()),
            mat_khau:      $pass.val()
        }).done(function (res) {
            resetRegisterForm();
            $('#tab-login').tab('show');
            $('#hgb_login_username').val(res.username);
            showAlert($('#hgb_login_alert'), 'success', res.message);
            $('#hgb_login_password').trigger('focus');
        }).fail(function (xhr) {
            var r = xhr.responseJSON || {};
            if (r.errors) {                                   // Lỗi từng trường do server trả về
                var map = { ten_dang_nhap: $username, ho_va_ten: $fullname, thu_dien_tu: $email,
                            so_dien_thoai: $phone, mat_khau: $pass };
                $.each(r.errors, function (field, msg) {
                    if (map[field]) { mark(map[field], false, msg); }
                });
            }
            if (r.code === 'otp_required') {                  // Phiên xác thực hết hạn -> yêu cầu xác thực lại
                $('#hgb_btn_change_email').trigger('click');
            }
            showAlert($regAlert, 'danger', errMsg(xhr, 'Đăng ký thất bại, vui lòng thử lại.'));
        }).always(function () {
            setBusy($btnFinal, false);
            refreshSubmit();
        });
    });

    // ------------------------------------------------------------------------
    // 4. Đăng nhập (AJAX, không tải lại trang)
    // ------------------------------------------------------------------------
    $('#hgb_formLogin').on('submit', function (e) {
        e.preventDefault();
        var $alert = $('#hgb_login_alert');
        var $btn   = $('#hgb_btn_login');
        var user   = $.trim($('#hgb_login_username').val());
        var pass   = $('#hgb_login_password').val();

        if (!user || !pass) {
            showAlert($alert, 'danger', 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.');
            return;
        }

        setBusy($btn, true, '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang đăng nhập...');
        post('login', { ten_dang_nhap: user, mat_khau: pass })
            .done(function (res) {
                showAlert($alert, 'success', res.message);
                window.location.href = res.redirect || '/FashionKids/views/pages/home.php';
            })
            .fail(function (xhr) {
                showAlert($alert, 'danger', errMsg(xhr, 'Đăng nhập thất bại, vui lòng thử lại.'));
                setBusy($btn, false);
            });
    });

    refreshSubmit();
})(jQuery);
