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
 * PHÂN HỆ ĐẢM NHIỆM: LÊ BẢO NGỌC (lbn_)
 * Catalog & Multi-Filter (MH 02), Product Detail PDP (MH 03), Admin Inventory (MH 13)
 * ============================================================================
 */
$(document).ready(function () {
    // ------------------------------------------------------------------------
    // 1. MH 02: Bộ lọc Đa tiêu chí & Thanh trượt Khoảng giá (shop-catalog.php)
    // ------------------------------------------------------------------------
    function lbnFormatVnd(amount) {
        return Number(amount).toLocaleString('vi-VN') + ' đ';
    }

    // Cập nhật giá trị hiển thị khi kéo Price Range Slider
    $('#lbn_priceRangeSlider').on('input change', function () {
        let maxPrice = parseInt($(this).val(), 10) || 500000;
        $('#lbn_priceRangeValue').text(lbnFormatVnd(maxPrice));
        window.lbnApplyCatalogFilters();
    });

    // Chọn danh mục bên cột trái (Sidebar Filter)
    window.lbnSelectCategoryFilter = function (catSlug, el) {
        $('.lbn-cat-filter-item').removeClass('active');
        $(el).addClass('active');
        $('#lbn_selectedCategory').val(catSlug);
        window.lbnApplyCatalogFilters();
    };

    // Lọc theo radio giới tính, từ khóa, hoặc dropdown sắp xếp
    $('input[name="lbn_gender_filter"], #lbn_sortSelect, #lbn_onlyBambooCheck').on('change', function () {
        window.lbnApplyCatalogFilters();
    });

    $('#lbn_keywordFilter').on('keyup', function () {
        window.lbnApplyCatalogFilters();
    });

    // Hàm thực thi lọc đa tiêu chí trên lưới sản phẩm 3 cột
    window.lbnApplyCatalogFilters = function () {
        let selectedCat = $('#lbn_selectedCategory').val() || 'all';
        let selectedGender = $('input[name="lbn_gender_filter"]:checked').val() || 'all';
        let maxPrice = parseInt($('#lbn_priceRangeSlider').val(), 10) || 500000;
        let keyword = ($('#lbn_keywordFilter').val() || '').toLowerCase().trim();
        let onlyBamboo = $('#lbn_onlyBambooCheck').is(':checked');
        let sortMode = $('#lbn_sortSelect').val() || 'newest';

        let visibleCount = 0;
        let gridContainer = $('#lbn_catalogGrid');
        let items = gridContainer.children('.lbn-catalog-col').get();

        items.forEach(function (colEl) {
            let $col = $(colEl);
            let itemCat = $col.attr('data-category');
            let itemGender = $col.attr('data-gender');
            let itemPrice = parseInt($col.attr('data-price'), 10) || 0;
            let itemName = ($col.attr('data-name') || '').toLowerCase();
            let itemBamboo = $col.attr('data-bamboo') === '1';

            let matchCat = (selectedCat === 'all' || itemCat === selectedCat);
            let matchGender = (selectedGender === 'all' || itemGender === selectedGender);
            let matchPrice = (itemPrice <= maxPrice);
            let matchKeyword = (!keyword || itemName.indexOf(keyword) !== -1);
            let matchBamboo = (!onlyBamboo || itemBamboo);

            if (matchCat && matchGender && matchPrice && matchKeyword && matchBamboo) {
                $col.removeClass('d-none');
                visibleCount++;
            } else {
                $col.addClass('d-none');
            }
        });

        // Sắp xếp các thẻ sản phẩm
        items.sort(function (a, b) {
            let priceA = parseInt($(a).attr('data-price'), 10) || 0;
            let priceB = parseInt($(b).attr('data-price'), 10) || 0;
            let idA = parseInt($(a).attr('data-id'), 10) || 0;
            let idB = parseInt($(b).attr('data-id'), 10) || 0;

            if (sortMode === 'price_asc') return priceA - priceB;
            if (sortMode === 'price_desc') return priceB - priceA;
            return idB - idA;
        });

        $.each(items, function (idx, item) {
            gridContainer.append(item);
        });

        $('#lbn_visibleProductCount').text(visibleCount);
        if (visibleCount === 0) {
            $('#lbn_emptyCatalogAlert').removeClass('d-none');
        } else {
            $('#lbn_emptyCatalogAlert').addClass('d-none');
        }
    };

    // Đặt lại bộ lọc MH 02
    window.lbnResetCatalogFilters = function () {
        $('#lbn_selectedCategory').val('all');
        $('.lbn-cat-filter-item').removeClass('active');
        $('.lbn-cat-filter-item[data-cat="all"]').addClass('active');
        $('input[name="lbn_gender_filter"][value="all"]').prop('checked', true);
        $('#lbn_priceRangeSlider').val(500000);
        $('#lbn_priceRangeValue').text('500.000 đ');
        $('#lbn_keywordFilter').val('');
        $('#lbn_onlyBambooCheck').prop('checked', false);
        $('#lbn_sortSelect').val('newest');
        window.lbnApplyCatalogFilters();
    };

    // Thêm nhanh vào giỏ hàng từ thẻ sản phẩm
    window.lbnQuickAddToCart = function (productName, price) {
        let badge = $('.cart-badge-custom').first();
        let currentCount = parseInt(badge.text(), 10) || 0;
        badge.text(currentCount + 1);
        alert('Đã thêm "' + productName + '" (' + lbnFormatVnd(price) + ') vào giỏ hàng!');
    };

    // ------------------------------------------------------------------------
    // 2. MH 03: Trang Chi tiết Sản phẩm PDP (product-detail.php)
    // ------------------------------------------------------------------------
    window.lbnSwitchPdpSlide = function (slideIndex, thumbEl) {
        $('#lbn_pdpImageSlider').carousel(slideIndex);
        $('.lbn-pdp-thumb').removeClass('active');
        $(thumbEl).addClass('active');
    };

    $('#lbn_pdpImageSlider').on('slid.bs.carousel', function (e) {
        let idx = $(e.relatedTarget).index();
        $('.lbn-pdp-thumb').removeClass('active');
        $('.lbn-pdp-thumb').eq(idx).addClass('active');
    });

    // Chọn kích cỡ trên PDP
    window.lbnSelectPdpSize = function (sizeText, btnEl) {
        $('.lbn-size-option-btn').removeClass('active');
        $(btnEl).addClass('active');
        $('#lbn_selectedSizeText').text(sizeText);
        $('#lbn_selectedSizeInput').val(sizeText);
    };

    // Tăng/giảm số lượng sản phẩm trên PDP
    window.lbnChangePdpQty = function (delta) {
        let qtyInput = $('#lbn_pdpQuantity');
        let maxStock = parseInt(qtyInput.attr('data-max'), 10) || 20;
        let currentVal = parseInt(qtyInput.val(), 10) || 1;
        let nextVal = currentVal + delta;

        if (nextVal < 1) nextVal = 1;
        if (nextVal > maxStock) {
            alert('Số lượng tồn kho hiện tại chỉ còn ' + maxStock + ' sản phẩm!');
            nextVal = maxStock;
        }
        qtyInput.val(nextVal);
    };

    // Tự động cập nhật nhãn giảm giá & giá ưu đãi theo cấp bậc VIP (hgb_get_discount_percent)
    window.lbnSwitchVipTierDiscount = function (tierKey, discountPercent, tierLabel) {
        let basePrice = parseInt($('#lbn_pdpBasePrice').attr('data-base-price'), 10) || 285000;
        let discountAmount = Math.round((basePrice * discountPercent) / 100);
        let finalPrice = basePrice - discountAmount;

        $('.lbn-vip-tier-btn').removeClass('active btn-pastel-primary').addClass('btn-outline-secondary');
        $('#lbn_vipBtn_' + tierKey).removeClass('btn-outline-secondary').addClass('active btn-pastel-primary');

        $('#lbn_pdpVipBadgeLabel').html(
            '<i class="fa-solid fa-crown mr-1"></i> Đặc quyền VIP ' + tierLabel + ': Giảm tự động ' + discountPercent + '%'
        );
        $('#lbn_pdpFinalVipPrice').text(lbnFormatVnd(finalPrice));
        $('#lbn_pdpSavedAmount').text('Tiết kiệm ngay ' + lbnFormatVnd(discountAmount));
    };

    // Nút Thêm vào giỏ & Mua ngay trên PDP
    window.lbnAddToCartPdp = function () {
        let prodName = $('#lbn_pdpProductTitle').text().trim();
        let chosenSize = $('#lbn_selectedSizeInput').val();
        let qty = $('#lbn_pdpQuantity').val();
        let badge = $('.cart-badge-custom').first();
        let currentCount = parseInt(badge.text(), 10) || 0;
        badge.text(currentCount + parseInt(qty, 10));

        alert('Đã thêm ' + qty + ' x "' + prodName + '" (Phân loại: ' + chosenSize + ') vào giỏ hàng thành công!');
    };

    window.lbnBuyNowPdp = function () {
        let prodName = $('#lbn_pdpProductTitle').text().trim();
        let chosenSize = $('#lbn_selectedSizeInput').val();
        let qty = $('#lbn_pdpQuantity').val();
        alert('Chuyển đến trang Thanh Toán với ' + qty + ' x "' + prodName + '" (' + chosenSize + ').');
        window.location.href = '/FashionKids/views/pages/checkout.php';
    };

    // Nút Chia sẻ mạng xã hội
    window.lbnShareProductSocial = function (platform) {
        let prodName = $('#lbn_pdpProductTitle').text().trim();
        let shareUrl = window.location.href;
        if (platform === 'copy') {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(shareUrl);
            }
            alert('Đã sao chép liên kết sản phẩm "' + prodName + '" vào bộ nhớ tạm!');
        } else {
            alert('Đang mở cửa sổ chia sẻ sản phẩm "' + prodName + '" lên ' + platform + '!');
        }
    };

    // ------------------------------------------------------------------------
    // 3. MH 13: Bàn làm việc Quản lý Sản phẩm & Tồn kho (admin-inventory.php)
    // ------------------------------------------------------------------------
    window.lbnFilterAdminInventory = function () {
        let catFilter = $('#lbn_invFilterCategory').val() || 'all';
        let stockFilter = $('#lbn_invFilterStockStatus').val() || 'all';
        let searchKw = ($('#lbn_invSearchInput').val() || '').toLowerCase().trim();

        $('.lbn-inv-row').each(function () {
            let $row = $(this);
            let rowCat = $row.attr('data-category');
            let rowStock = parseInt($row.attr('data-stock'), 10) || 0;
            let rowSearch = ($row.attr('data-search') || '').toLowerCase();

            let matchCat = (catFilter === 'all' || rowCat === catFilter);
            let matchStock = true;
            if (stockFilter === 'low') {
                matchStock = (rowStock <= 5);
            } else if (stockFilter === 'available') {
                matchStock = (rowStock > 5);
            }
            let matchKw = (!searchKw || rowSearch.indexOf(searchKw) !== -1);

            if (matchCat && matchStock && matchKw) {
                $row.removeClass('d-none');
            } else {
                $row.addClass('d-none');
            }
        });
    };

    $('#lbn_invFilterCategory, #lbn_invFilterStockStatus').on('change', function () {
        window.lbnFilterAdminInventory();
    });

    $('#lbn_invSearchInput').on('keyup', function () {
        window.lbnFilterAdminInventory();
    });

    // Cập nhật nhanh tồn kho (Stored Procedure lbn_update_stock) & Cảnh báo đỏ khi tồn kho <= 5
    window.lbnQuickUpdateStock = function (productId, skuCode) {
        let inputEl = $('#lbn_stockInput_' + productId);
        let newStock = parseInt(inputEl.val(), 10);

        if (isNaN(newStock) || newStock < 0) {
            alert('Số lượng tồn kho không hợp lệ (phải >= 0)!');
            return;
        }

        let $row = $('#lbn_invRow_' + productId);
        $row.attr('data-stock', newStock);
        let statusCell = $('#lbn_stockBadgeCell_' + productId);

        if (newStock <= 5) {
            statusCell.html(
                '<span class="badge badge-danger lbn-badge-low-stock px-2 py-1">' +
                '<i class="fa-solid fa-triangle-exclamation mr-1"></i>Sắp hết hàng (' + newStock + ')' +
                '</span>'
            );
        } else {
            statusCell.html(
                '<span class="badge badge-mint px-2 py-1 rounded-pill">' +
                '<i class="fa-solid fa-circle-check mr-1"></i>Còn hàng (' + newStock + ')' +
                '</span>'
            );
        }

        // Cập nhật lại bộ đếm tổng sản phẩm sắp hết hàng (<= 5)
        let lowStockTotal = 0;
        $('.lbn-inv-row').each(function () {
            let st = parseInt($(this).attr('data-stock'), 10) || 0;
            if (st <= 5) lowStockTotal++;
        });
        $('#lbn_lowStockCounter').text(lowStockTotal);

        alert('Đã thực thi lbn_update_stock cho mã SP ' + skuCode + ' -> Tồn kho mới: ' + newStock + ' sản phẩm.');
    };

    // Chuyển đổi hướng dẫn loại kích cỡ (Quần áo vs Giày dép) trong Form MH 13
    window.lbnToggleSizeTypeGuide = function (typeVal) {
        let guideEl = $('#lbn_sizeTypeGuideText');
        if (typeVal === 'giay_dep') {
            guideEl.html('<i class="fa-solid fa-shoe-prints text-peach mr-1"></i> Dải size áp dụng: <strong>Size 21, 22, 24, 26, 28, 30</strong> (Theo chiều dài bàn chân 11.5 - 19.0 cm).');
        } else {
            guideEl.html('<i class="fa-solid fa-shirt text-peach mr-1"></i> Dải size áp dụng: <strong>Size 1 (0-1T) đến Size 5 (5-6T)</strong> (Theo chiều cao 50 - 130 cm).');
        }
    };

    // Chèn nhanh nhãn mô tả chất liệu an toàn
    window.lbnAppendSafeMaterialTag = function (tagText) {
        let textarea = $('#lbn_formProductMaterial');
        let current = textarea.val().trim();
        if (current.indexOf(tagText) === -1) {
            textarea.val(current ? current + ' • ' + tagText : tagText);
        }
    };

    // Xem trước ảnh tải lên trong Form Thêm/Sửa sản phẩm (MH 13)
    $('#lbn_productImageInput').on('change', function (e) {
        let file = e.target.files[0];
        if (file) {
            $('#lbn_uploadFileName').text('Đã chọn: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)');
            $('#lbn_uploadPreviewBadge').removeClass('d-none');
        }
    });

    // Đổ dữ liệu sản phẩm cần sửa lên Form quản lý (MH 13)
    window.lbnEditInventoryProduct = function (btnEl) {
        let $btn = $(btnEl);
        $('#lbn_formModeTitle').html('<i class="fa-solid fa-pen-to-square text-peach mr-1"></i> Cập Nhật Sản Phẩm #' + $btn.attr('data-id'));
        $('#lbn_formProductId').val($btn.attr('data-id'));
        $('#lbn_formProductSku').val($btn.attr('data-sku'));
        $('#lbn_formProductName').val($btn.attr('data-name'));
        $('#lbn_formProductCategory').val($btn.attr('data-cat-id'));
        $('#lbn_formProductGender').val($btn.attr('data-gender'));
        $('#lbn_formProductPrice').val($btn.attr('data-price'));
        $('#lbn_formProductStock').val($btn.attr('data-stock'));

        let sizeType = $btn.attr('data-type') || 'quan_ao';
        $('input[name="lbn_size_type"][value="' + sizeType + '"]').prop('checked', true);
        window.lbnToggleSizeTypeGuide(sizeType);

        $('#lbn_formProductMaterial').val($btn.attr('data-material'));
        $('#lbn_btnSubmitProduct').html('<i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Cập Nhật Sản Phẩm');
        $('#lbn_btnResetForm').removeClass('d-none');

        $('html, body').animate({
            scrollTop: $('#lbn_adminProductFormCard').offset().top - 90
        }, 300);
    };

    window.lbnResetProductForm = function () {
        $('#lbn_formAdminProduct')[0].reset();
        $('#lbn_formProductId').val('');
        $('#lbn_formModeTitle').html('<i class="fa-solid fa-plus-circle text-peach mr-1"></i> Thêm Sản Phẩm Mới (lbn_insert_product)');
        $('#lbn_btnSubmitProduct').html('<i class="fa-solid fa-cloud-arrow-up mr-1"></i> Thêm Sản Phẩm Vào Kho');
        $('#lbn_btnResetForm').addClass('d-none');
        $('#lbn_uploadFileName').text('Kéo thả hoặc nhấn để chọn ảnh sản phẩm (JPG, PNG, WEBP)');
        $('#lbn_uploadPreviewBadge').addClass('d-none');
        window.lbnToggleSizeTypeGuide('quan_ao');
    };

    $('#lbn_formAdminProduct').on('submit', function (e) {
        e.preventDefault();
        let prodName = $('#lbn_formProductName').val().trim();
        let prodStock = parseInt($('#lbn_formProductStock').val(), 10) || 0;
        let isEdit = $('#lbn_formProductId').val() !== '';

        if (isEdit) {
            alert('Đã cập nhật thông tin sản phẩm "' + prodName + '" (Tồn kho: ' + prodStock + ') thành công!');
        } else {
            alert('Đã thực thi Stored Procedure lbn_insert_product: Thêm mới sản phẩm "' + prodName + '" vào kho thành công!');
        }
    });
});
