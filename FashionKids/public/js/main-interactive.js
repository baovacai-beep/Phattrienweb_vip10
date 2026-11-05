
/**
 * FASHIONKIDS D2C E-COMMERCE - MAIN INTERACTIVE SCRIPT
 * Dam nhiem boi: Ho Gia Bao (Nhom truong)
 * Chuc nang: Tu dong focus OTP, paste ma 6 so, dem nguoc 60s
 */

$(document).ready(function () {
    // 1. Tu dong chuyen tieu diem (Auto-focus) cho cum 6 o nhap OTP (MH 20)
    $('.otp-box').on('keyup', function (e) {
        let currentInput = $(this);
        let val = currentInput.val();

        // Loc chi cho phep so
        if (!/^\d*$/.test(val)) {
            currentInput.val('');
            return;
        }

        // Neu da nhap 1 ky tu -> Nhay sang o tiep theo
        if (val.length === 1) {
            currentInput.next('.otp-box').focus();
        } 
        // Neu nhan Backspace hoac Delete -> Quay lai o truoc do
        else if (e.key === 'Backspace' || e.key === 'Delete') {
            currentInput.prev('.otp-box').focus();
        }
    });

    // 2. Ho tro Paste truc tiep chuoi 6 chu so OTP tu clipboard
    $('.otp-box').first().on('paste', function (e) {
        let pasteData = e.originalEvent.clipboardData.getData('text').trim();
        if (/^\d{6}$/.test(pasteData)) {
            $('.otp-box').each(function (index) {
                $(this).val(pasteData[index]);
            });
            $('.otp-box').last().focus();
            e.preventDefault();
        }
    });

    // 3. Bo dem nguoc 60 giay khi mo Modal OTP
    let timerInterval = null;
    window.startOtpTimer = function () {
        let timeLeft = 60;
        let timerDisplay = $('#countdownTimer');
        let resendBtn = $('#btnResendOtp');

        timerDisplay.text(timeLeft + 's').removeClass('d-none');
        resendBtn.addClass('d-none');

        if (timerInterval) {
            clearInterval(timerInterval);
        }

        timerInterval = setInterval(function () {
            timeLeft--;
            timerDisplay.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerDisplay.addClass('d-none');
                resendBtn.removeClass('d-none');
            }
        }, 1000);
    };

    // 4. Su kien nut Gui lai ma OTP
    $('#btnResendOtp').on('click', function () {
        $('.otp-box').val('');
        $('.otp-box').first().focus();
        startOtpTimer();
        alert('Ma xac thuc OTP moi da duoc gui toi So dien thoai / Email!');
    });

    // 5. Kich hoat xac nhan OTP thanh cong
    $('#btnConfirmOtp').on('click', function () {
        let enteredOtp = '';
        $('.otp-box').each(function () {
            enteredOtp += $(this).val();
        });

        if (enteredOtp.length === 6) {
            alert('Xac thuc OTP thanh cong! Chao mung den voi chuong trinh VIP FashionKids.');
            $('#modalVerifyOtp').modal('hide');
            window.location.href = '/FashionKids/views/pages/profile.php';
        } else {
            alert('Vui long nhap du 6 chu so xac thuc!');
            $('.otp-box').filter(function () { return !this.value; }).first().focus();
        }
    });
});