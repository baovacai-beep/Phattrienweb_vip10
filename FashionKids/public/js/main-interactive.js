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


/**
 * ============================================================================
 * PHÂN HỆ ĐẢM NHIỆM: LÊ NGUYỄN KHÁNH TRÌNH (lnkt_)
 * Homepage Carousel, Size Chart Modal, Checkout, Order Tracking, KOL & Banners
 * ============================================================================
 */
$(document).ready(function () {
    // ------------------------------------------------------------------------
    // 1. MH 04: Thuật toán Gợi ý Size Thước Dây (lnkt_suggest_size)
    // ------------------------------------------------------------------------
    window.lnktSuggestClothingSize = function () {
        let heightVal = parseFloat($('#lnkt_inputHeight').val());
        let genderText = $('#lnkt_selectClothingGender option:selected').text();
        let resultBox = $('#lnkt_clothingResultBox');
        let sizeTitle = $('#lnkt_clothingSizeResult');
        let sizeAdvice = $('#lnkt_clothingSizeAdvice');

        if (isNaN(heightVal) || heightVal < 45 || heightVal > 135) {
            alert('Vui lòng nhập chiều cao hợp lệ của bé (từ 45 cm đến 135 cm)!');
            $('#lnkt_inputHeight').focus();
            return;
        }

        let suggested = '';
        let detailNote = '';

        if (heightVal < 75) {
            suggested = 'Size 1 (0-1T)';
            detailNote = 'Phù hợp cho bé sơ sinh đến 12 tháng (50 - 74 cm). Chất vải sợi tre kháng khuẩn co giãn 4 chiều cực êm cho làn da non nớt.';
        } else if (heightVal <= 85) {
            suggested = 'Size 2 (1-2T)';
            detailNote = 'Phù hợp chiều cao 75 - 85 cm. Vải sợi tre hữu cơ co giãn tốt (+3cm biên độ), bé mặc thoải mái vận động cả ngày.';
        } else if (heightVal <= 95) {
            suggested = 'Size 2 (2-3T)';
            detailNote = 'Phù hợp chiều cao 86 - 95 cm. Chất liệu sợi tre làm mát tự nhiên có độ đàn hồi cao; nếu bé bụ bẫm mẹ có thể chọn nhích lên Size 3 (3-4T).';
        } else if (heightVal <= 105) {
            suggested = 'Size 3 (3-4T)';
            detailNote = 'Phù hợp chiều cao 96 - 105 cm. Phom dáng chuẩn châu Á kết hợp bo chun mềm co giãn 4 chiều không gây hằn bụng bé.';
        } else if (heightVal <= 115) {
            suggested = 'Size 4 (4-5T)';
            detailNote = 'Phù hợp chiều cao 106 - 115 cm. Độ co giãn sợi cotton hữu cơ & bamboo giúp bé thoải mái chạy nhảy ngoài trời.';
        } else {
            suggested = 'Size 5 (5-6T)';
            detailNote = 'Phù hợp chiều cao 116 - 130 cm. Phom rộng rãi, thoáng khí và thấm hút mồ hôi gấp 2 lần cotton thường.';
        }

        sizeTitle.text(suggested + ' — ' + genderText);
        sizeAdvice.text(detailNote);
        resultBox.removeClass('d-none');
    };

    window.lnktSuggestShoeSize = function () {
        let footVal = parseFloat($('#lnkt_inputFootLength').val());
        let resultBox = $('#lnkt_shoeResultBox');
        let sizeTitle = $('#lnkt_shoeSizeResult');
        let sizeAdvice = $('#lnkt_shoeSizeAdvice');

        if (isNaN(footVal) || footVal < 10 || footVal > 22) {
            alert('Vui lòng nhập chiều dài bàn chân của bé (từ 10.0 cm đến 22.0 cm)!');
            $('#lnkt_inputFootLength').focus();
            return;
        }

        let shoeSize = '';
        if (footVal <= 12.5) {
            shoeSize = 'Size 21 (11.5 - 12.5 cm)';
        } else if (footVal <= 13.5) {
            shoeSize = 'Size 22 (12.6 - 13.5 cm)';
        } else if (footVal <= 14.5) {
            shoeSize = 'Size 24 (13.6 - 14.5 cm)';
        } else if (footVal <= 15.5) {
            shoeSize = 'Size 26 (14.6 - 15.5 cm)';
        } else if (footVal <= 17.0) {
            shoeSize = 'Size 28 (15.6 - 17.0 cm)';
        } else {
            shoeSize = 'Size 30 (17.1 - 19.0 cm)';
        }

        sizeTitle.text(shoeSize);
        sizeAdvice.text('Tư vấn bổ sung: Đế cao su non mềm dẻo chống trượt, quai vải êm co giãn nhẹ. Hệ thống đã cộng trừ hao 0.5 cm để bé mang kèm tất/vớ thoải mái.');
        resultBox.removeClass('d-none');
    };

    // ------------------------------------------------------------------------
    // 2. MH 05: Giỏ Hàng & Thanh Toán + Tích hợp Tỉnh/Thành (Provinces API)
    // ------------------------------------------------------------------------
    const lnktFallbackProvinces = {
        'TP. Hồ Chí Minh': ['Quận 1', 'Quận 3', 'Quận 7', 'Quận 10', 'Quận Bình Thạnh', 'Quận Phú Nhuận', 'TP. Thủ Đức', 'Quận Tân Bình'],
        'Thành phố Hà Nội': ['Quận Ba Đình', 'Quận Hoàn Kiếm', 'Quận Cầu Giấy', 'Quận Đống Đa', 'Quận Hai Bà Trưng', 'Quận Tây Hồ', 'Quận Thanh Xuân'],
        'Thành phố Đà Nẵng': ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà', 'Quận Ngũ Hành Sơn', 'Quận Liên Chiểu'],
        'Tỉnh Bình Dương': ['TP. Thủ Dầu Một', 'TP. Dĩ An', 'TP. Thuận An', 'TP. Tân Uyên'],
        'Tỉnh Đồng Nai': ['TP. Biên Hòa', 'TP. Long Khánh', 'Huyện Long Thành'],
        'Thành phố Cần Thơ': ['Quận Ninh Kiều', 'Quận Cái Răng', 'Quận Bình Thủy']
    };

    if ($('#lnkt_province').length > 0) {
        // Thử gọi Provinces Open API, nếu môi trường offline/sandbox thì dùng dữ liệu chuẩn hóa có sẵn
        fetch('https://provinces.open-api.vn/api/?depth=2')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    let provSelect = $('#lnkt_province');
                    provSelect.empty().append('<option value="">-- Chọn Tỉnh / Thành phố --</option>');
                    window.lnktApiProvincesData = {};
                    data.forEach(function (item) {
                        window.lnktApiProvincesData[item.name] = (item.districts || []).map(function (d) { return d.name; });
                        provSelect.append($('<option></option>').val(item.name).text(item.name));
                    });
                }
            })
            .catch(function () {
                // Giữ nguyên danh sách Tỉnh/Thành chuẩn hóa mặc định khi không có mạng
            });

        $('#lnkt_province').on('change', function () {
            let selectedProv = $(this).val();
            let distSelect = $('#lnkt_district');
            distSelect.empty().append('<option value="">-- Chọn Quận / Huyện --</option>');

            let districts = (window.lnktApiProvincesData && window.lnktApiProvincesData[selectedProv])
                ? window.lnktApiProvincesData[selectedProv]
                : (lnktFallbackProvinces[selectedProv] || []);

            districts.forEach(function (dName) {
                distSelect.append($('<option></option>').val(dName).text(dName));
            });
        });
    }

    window.lnktFormatVnd = function (amount) {
        return Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' đ';
    };

    window.lnktRecalculateCheckout = function () {
        let subtotal = 0;
        let totalItems = 0;

        $('.lnkt-cart-row').each(function () {
            let row = $(this);
            let price = parseFloat(row.attr('data-price')) || 0;
            let qty = parseInt(row.find('.lnkt-qty-input').val(), 10) || 1;
            if (qty < 1) {
                qty = 1;
                row.find('.lnkt-qty-input').val(1);
            }
            let lineTotal = price * qty;
            subtotal += lineTotal;
            totalItems += qty;
            row.find('.lnkt-line-total').text(window.lnktFormatVnd(lineTotal));
        });

        let discountPercent = parseFloat($('#lnkt_vipDiscountPercent').val()) || 10;
        let discountAmount = Math.round(subtotal * (discountPercent / 100));
        let finalTotal = Math.max(0, subtotal - discountAmount);
        let rewardPoints = Math.floor(finalTotal / 10000);

        $('#lnkt_cartCountBadge').text(totalItems + ' sản phẩm');
        $('#lnkt_subtotalText').text(window.lnktFormatVnd(subtotal));
        $('#lnkt_discountText').text('- ' + window.lnktFormatVnd(discountAmount));
        $('#lnkt_totalAmountText').text(window.lnktFormatVnd(finalTotal));
        $('#lnkt_rewardPointsText').text('+' + rewardPoints + ' điểm VIP');
    };

    $(document).on('click', '.lnkt-btn-qty-minus', function () {
        let input = $(this).closest('.input-group').find('.lnkt-qty-input');
        let currentVal = parseInt(input.val(), 10) || 1;
        if (currentVal > 1) {
            input.val(currentVal - 1);
            window.lnktRecalculateCheckout();
        }
    });

    $(document).on('click', '.lnkt-btn-qty-plus', function () {
        let input = $(this).closest('.input-group').find('.lnkt-qty-input');
        let currentVal = parseInt(input.val(), 10) || 1;
        input.val(currentVal + 1);
        window.lnktRecalculateCheckout();
    });

    $(document).on('change keyup', '.lnkt-qty-input, #lnkt_vipDiscountPercent', function () {
        window.lnktRecalculateCheckout();
    });

    $(document).on('click', '.lnkt-btn-remove-item', function () {
        if ($('.lnkt-cart-row').length <= 1) {
            alert('Giỏ hàng cần tối thiểu 1 sản phẩm để tiến hành đặt hàng!');
            return;
        }
        $(this).closest('.lnkt-cart-row').remove();
        window.lnktRecalculateCheckout();
    });

    $('#lnkt_formCheckout').on('submit', function (e) {
        e.preventDefault();
        let receiver = $('#lnkt_receiverName').val().trim();
        let phone = $('#lnkt_receiverPhone').val().trim();
        let prov = $('#lnkt_province').val();
        let dist = $('#lnkt_district').val();

        if (!receiver || !phone || !prov || !dist) {
            alert('Vui lòng điền đầy đủ thông tin phụ huynh nhận hàng và chọn Tỉnh/Thành phố, Quận/Huyện!');
            return;
        }

        alert('Đặt hàng thành công (lnkt_create_order)! Đang chuyển tới trang Theo dõi tiến trình đơn hàng...');
        window.location.href = '/FashionKids/views/pages/order-tracking.php?status=hoan_tat';
    });

    // ------------------------------------------------------------------------
    // 3. MH 06: Stepper Timeline 5 Bước Theo Dõi Đơn Hàng
    // ------------------------------------------------------------------------
    window.lnktSwitchOrderStatus = function (statusKey) {
        const orderSteps = ['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy'];
        const statusLabels = {
            'cho_duyet': { text: 'CHỜ DUYỆT', badge: 'badge-warning', desc: 'Đơn hàng vừa được khởi tạo, đang chờ nhân viên CSKH xác nhận tồn kho.' },
            'da_xac_nhan': { text: 'ĐÃ XÁC NHẬN', badge: 'badge-sky', desc: 'Nhân viên đã duyệt đơn và đang đóng gói hộp quà sợi tre kháng khuẩn.' },
            'dang_giao': { text: 'ĐANG GIAO HÀNG', badge: 'badge-info', desc: 'Đơn vị vận chuyển đang trên đường giao kiện hàng đến địa chỉ của phụ huynh.' },
            'hoan_tat': { text: 'HOÀN TẤT ĐƠN HÀNG', badge: 'badge-mint', desc: 'Giao hàng thành công! Điểm thưởng VIP đã được cộng vào tài khoản của mẹ.' },
            'da_huy': { text: 'ĐÃ HỦY ĐƠN', badge: 'badge-danger', desc: 'Đơn hàng đã được hủy theo yêu cầu hoặc do không liên hệ được người nhận.' }
        };

        $('.lnkt-stepper-item').removeClass('completed active cancelled');

        if (statusKey === 'da_huy') {
            $('#lnkt_step_cho_duyet').addClass('completed');
            $('#lnkt_step_da_huy').addClass('cancelled');
        } else {
            let targetIdx = orderSteps.indexOf(statusKey);
            for (let i = 0; i < 4; i++) {
                let stepEl = $('#lnkt_step_' + orderSteps[i]);
                if (i < targetIdx) {
                    stepEl.addClass('completed');
                } else if (i === targetIdx) {
                    stepEl.addClass(statusKey === 'hoan_tat' ? 'completed active' : 'active');
                }
            }
        }

        let info = statusLabels[statusKey] || statusLabels['hoan_tat'];
        $('#lnkt_currentOrderBadge')
            .removeClass('badge-warning badge-sky badge-info badge-mint badge-danger')
            .addClass(info.badge)
            .text(info.text);
        $('#lnkt_currentOrderDesc').text(info.desc);

        // Kích hoạt hiển thị nút vàng bơ (#FDE2B8) "★ Viết đánh giá sản phẩm" khi đơn đạt trạng thái hoan_tat
        if (statusKey === 'hoan_tat') {
            $('.lnkt-review-cta-wrap').removeClass('d-none');
        } else {
            $('.lnkt-review-cta-wrap').addClass('d-none');
        }
    };

    // ------------------------------------------------------------------------
    // 4. MH 10: Bàn làm việc Nhân viên Quản trị Đơn hàng (staff-orders.php)
    // ------------------------------------------------------------------------
    window.lnktFilterStaffOrders = function (statusFilter, btnEl) {
        if (btnEl) {
            $('.lnkt-staff-filter-btn').removeClass('active btn-pastel-primary').addClass('btn-light');
            $(btnEl).removeClass('btn-light').addClass('active btn-pastel-primary');
        }

        $('.lnkt-staff-order-row').each(function () {
            let rowStatus = $(this).attr('data-status');
            if (statusFilter === 'all' || rowStatus === statusFilter) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    };

    window.lnktUpdateStaffOrderStatus = function (orderCode, selectEl) {
        let newStatus = $(selectEl).val();
        let row = $(selectEl).closest('.lnkt-staff-order-row');
        let badge = row.find('.lnkt-order-status-badge');

        row.attr('data-status', newStatus);
        badge.removeClass('badge-warning badge-sky badge-info badge-mint badge-danger');

        if (newStatus === 'cho_duyet') {
            badge.addClass('badge-warning').text('Chờ duyệt');
        } else if (newStatus === 'da_xac_nhan') {
            badge.addClass('badge-sky').text('Đã xác nhận');
        } else if (newStatus === 'dang_giao') {
            badge.addClass('badge-info').text('Đang giao');
        } else if (newStatus === 'hoan_tat') {
            badge.addClass('badge-mint').text('Hoàn tất');
        } else if (newStatus === 'da_huy') {
            badge.addClass('badge-danger').text('Đã hủy');
        }

        alert('Đã thực thi lnkt_update_order_status cho đơn ' + orderCode + ' -> Trạng thái mới: ' + badge.text());
    };

    // ------------------------------------------------------------------------
    // 5. MH 14: Quản trị Bảng Size (admin-size-charts.php)
    // ------------------------------------------------------------------------
    $('#lnkt_filterSizeCategory').on('change', function () {
        let catId = $(this).val();
        $('.lnkt-size-chart-row').each(function () {
            if (catId === 'all' || $(this).attr('data-category-id') === catId) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    // ------------------------------------------------------------------------
    // 6. MH 16: Cổng Casting KOL Nhí - Tự động tính tuổi & Upload 2-5 ảnh
    // ------------------------------------------------------------------------
    $('#lnkt_babyDob').on('change', function () {
        let dobStr = $(this).val();
        if (!dobStr) return;

        let dob = new Date(dobStr);
        let now = new Date();
        let months = (now.getFullYear() - dob.getFullYear()) * 12 + (now.getMonth() - dob.getMonth());

        if (months < 0) {
            $('#lnkt_babyAgeDisplay').val('Ngày sinh không hợp lệ');
            return;
        }

        let years = Math.floor(months / 12);
        let remMonths = months % 12;
        let ageText = years > 0
            ? years + ' tuổi ' + (remMonths > 0 ? remMonths + ' tháng' : '(Tròn tuổi)')
            : Math.max(1, remMonths) + ' tháng tuổi';

        $('#lnkt_babyAgeDisplay').val(ageText);
    });

    $('#lnkt_castingPhotos').on('change', function (e) {
        let files = e.target.files;
        let previewContainer = $('#lnkt_photoPreviewContainer');
        let countBadge = $('#lnkt_photoCountBadge');

        if (files.length < 2 || files.length > 5) {
            alert('Vui lòng chọn từ 2 đến 5 ảnh chân dung và toàn thân của bé! (Hiện tại bạn chọn: ' + files.length + ' ảnh)');
            $(this).val('');
            countBadge.removeClass('badge-mint').addClass('badge-warning').text('Chưa đủ 2 - 5 ảnh');
            return;
        }

        countBadge.removeClass('badge-warning').addClass('badge-mint').text('Đã đính kèm ' + files.length + '/5 ảnh hợp lệ');
        previewContainer.empty();

        Array.from(files).forEach(function (file, idx) {
            let card = $('<div class="lnkt-preview-card mr-2 mb-2 shadow-sm text-center p-1"></div>');
            let label = $('<div class="small font-weight-bold text-slate text-truncate px-1"></div>').text((idx + 1) + '. ' + file.name);
            card.append('<div><i class="fa-solid fa-image text-peach h4 mb-1"></i></div>').append(label);
            previewContainer.append(card);
        });
    });

    $('#lnkt_formKolCasting').on('submit', function (e) {
        e.preventDefault();
        if (!$('#lnkt_guardianConsent').is(':checked')) {
            alert('Phụ huynh vui lòng đánh dấu xác nhận cam kết từ người giám hộ hợp pháp!');
            return;
        }
        alert('Nộp hồ sơ Casting Siêu Sao Nhí KOL thành công! Ban tổ chức FashionKids sẽ liên hệ phụ huynh trong 48 giờ.');
    });
});
