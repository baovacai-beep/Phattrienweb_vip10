/**
 * FASHIONKIDS D2C PLATFORM - INTERACTIVE SCRIPT
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_) - Auth, OTP & VIP Tiers
 */

$(document).ready(function () {
    // 1. Tự động nhảy ô (Auto-focus) và lùi ô cho mã OTP 6 số (MH 20)
    $('.hgb-otp-box').on('keyup', function (e) {
        let currentInput = $(this);
        let val = currentInput.val();

        // Chỉ cho phép nhập số
        if (!/^\d*$/.test(val)) {
            currentInput.val('');
            return;
        }

        if (val.length === 1) {
            currentInput.next('.hgb-otp-box').focus();
        } else if (e.key === 'Backspace' || e.key === 'Delete') {
            currentInput.prev('.hgb-otp-box').focus();
        }
    });

    // 2. Hỗ trợ Paste trực tiếp chuỗi 6 số từ clipboard
    $('.hgb-otp-box').first().on('paste', function (e) {
        let pasteData = (e.originalEvent.clipboardData || window.clipboardData).getData('text').trim();
        if (/^\d{6}$/.test(pasteData)) {
            $('.hgb-otp-box').each(function (index) {
                $(this).val(pasteData[index]);
            });
            $('.hgb-otp-box').last().focus();
            e.preventDefault();
        }
    });

    // 3. Bộ đếm ngược OTP 60 giây (setInterval)
    let hgbTimerInterval = null;
    window.hgbStartOtpTimer = function () {
        let timeLeft = 60;
        let timerDisplay = $('#hgb_countdownTimer');
        let resendBtn = $('#hgb_btnResendOtp');

        timerDisplay.text(timeLeft + 's').removeClass('d-none');
        resendBtn.addClass('d-none');

        if (hgbTimerInterval) {
            clearInterval(hgbTimerInterval);
        }

        hgbTimerInterval = setInterval(function () {
            timeLeft--;
            timerDisplay.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(hgbTimerInterval);
                timerDisplay.addClass('d-none');
                resendBtn.removeClass('d-none');
            }
        }, 1000);
    };

    // 4. Nút gửi lại mã OTP
    $('#hgb_btnResendOtp').on('click', function () {
        $('.hgb-otp-box').val('');
        $('.hgb-otp-box').first().focus();
        window.hgbStartOtpTimer();
        alert('Mã OTP xác thực mới đã được gửi lại vào số điện thoại!');
    });

    // 5. Xác nhận OTP thành công -> chuyển về trang Profile
    $('#hgb_btnConfirmOtp').on('click', function () {
        let otpCode = '';
        $('.hgb-otp-box').each(function () {
            otpCode += $(this).val();
        });

        if (otpCode.length === 6) {
            alert('Xác thực OTP thành công! Chào mừng phụ huynh đến với hạng thẻ VIP.');
            $('#modalVerifyOtp').modal('hide');
            window.location.href = '/FashionKids/views/pages/profile.php';
        } else {
            alert('Vui lòng nhập đủ 6 chữ số OTP!');
            $('.hgb-otp-box').filter(function () { return !this.value; }).first().focus();
        }
    });

    // 6. Tự động kích hoạt Progress Bar không dùng Inline Style (MH 07)
    $('.hgb-progress-dynamic').each(function () {
        let progressVal = $(this).attr('data-progress');
        $(this).css('width', progressVal + '%');
    });

    // 7. Chuyển đổi trực quan 4 hạng thẻ VIP phục vụ demo & chụp ảnh báo cáo
    window.hgbSwitchVipCard = function (tier) {
        let cardBox = $('#hgb_vipCardPreview');
        let badge = $('#hgb_vipBadgeTitle');
        let discount = $('#hgb_vipDiscount');
        let progressBar = $('#hgb_vipProgressBar');
        let ptsInfo = $('#hgb_vipPtsInfo');

        cardBox.removeClass('vip-card-bronze vip-card-silver vip-card-gold vip-card-diamond');

        if (tier === 'bronze') {
            cardBox.addClass('vip-card-bronze');
            badge.text('HẠNG ĐỒNG (BRONZE)');
            discount.text('GIẢM 0%');
            progressBar.css('width', '25%');
            ptsInfo.text('120 / 500 PTS');
        } else if (tier === 'silver') {
            cardBox.addClass('vip-card-silver');
            badge.text('HẠNG BẠC (SILVER)');
            discount.text('GIẢM 5% MỌI ĐƠN');
            progressBar.css('width', '55%');
            ptsInfo.text('850 / 1.500 PTS');
        } else if (tier === 'gold') {
            cardBox.addClass('vip-card-gold');
            badge.text('HẠNG VÀNG (GOLD)');
            discount.text('GIẢM 10% MỌI ĐƠN');
            progressBar.css('width', '55%');
            ptsInfo.text('1.650 / 3.000 PTS');
        } else if (tier === 'diamond') {
            cardBox.addClass('vip-card-diamond');
            badge.text('HẠNG KIM CƯƠNG (DIAMOND)');
            discount.text('GIẢM 15% CAO CẤP');
            progressBar.css('width', '100%');
            ptsInfo.text('3.500 PTS (TỐI ĐA)');
        }
    };
});