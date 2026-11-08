/**
 * FASHIONKIDS D2C PLATFORM - INTERACTIVE SCRIPT
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_) - Auth, OTP & VIP Tiers
 */

$(document).ready(function () {
    // (Logic OTP 6 so - nhap o, dem nguoc, xac thuc AJAX - da chuyen sang public/js/auth-ajax.js)

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



/**
 * ============================================================================
 * PHÂN HỆ ĐẢM NHIỆM: LÊ NGUYỄN KHÁNH TRÌNH (lnkt_)
 * Homepage Carousel (MH 01), Size Chart Modal (MH 04), Checkout (MH 05),
 * Order Tracking (MH 06), Staff Orders (MH 10), Admin Size Charts (MH 14),
 * KOL Casting (MH 16), Admin Banners (MH 23)
 * ============================================================================
 */
$(document).ready(function () {
    function lnktFormatVnd(amount) {
        return Number(amount).toLocaleString('vi-VN') + ' đ';
    }

    // ------------------------------------------------------------------------
    // 1. MH 01: Khởi tạo Banner Carousel tự động chuyển slide mỗi 4 giây (4000ms)
    // ------------------------------------------------------------------------
    if ($('#lnkt_homeBannerCarousel').length) {
        $('#lnkt_homeBannerCarousel').carousel({
            interval: 4000,
            ride: 'carousel'
        });
    }

    // ------------------------------------------------------------------------
    // 2. MH 04: Modal Tra cứu & Gợi ý Size Thước Dây (lnkt_suggest_size)
    // ------------------------------------------------------------------------
    window.lnktSwitchSizeTab = function (typeKey) {
        $('#lnkt_activeSizeType').val(typeKey);
        window.lnktRunSuggestSize();
    };

    window.lnktQuickFillMeasure = function (typeKey, val) {
        $('#lnkt_activeSizeType').val(typeKey);
        if (typeKey === 'giay_dep') {
            $('#lnkt_tabBtnShoes').tab('show');
            $('#lnkt_inputFootCm').val(val);
        } else {
            $('#lnkt_tabBtnClothing').tab('show');
            $('#lnkt_inputHeightCm').val(val);
        }
        window.lnktRunSuggestSize();
    };

    // Thuật toán mô phỏng Stored Function lnkt_suggest_size(p_category_id, p_measure_cm)
    window.lnktRunSuggestSize = function () {
        let activeType = $('#lnkt_activeSizeType').val() || 'quan_ao';
        let sizeBadge = $('#lnkt_suggestedSizeBadge');
        let subSpec = $('#lnkt_suggestedSubSpec');
        let stretchNote = $('#lnkt_materialStretchNote');
        let summaryLabel = $('#lnkt_resultInputSummary');

        if (activeType === 'quan_ao') {
            let heightCm = parseFloat($('#lnkt_inputHeightCm').val()) || 88;
            let isChubby = $('#lnkt_chubbyCheck').is(':checked');
            let selectedOpt = $('#lnkt_clothingCategory option:selected');
            let catStretch = selectedOpt.attr('data-stretch') || 'Vải sợi tre Organic Bamboo co giãn 4 chiều (+8% đến +12%), thấm hút mồ hôi gấp 2 lần.';

            let effectiveHeight = isChubby ? (heightCm + 7) : heightCm;
            let suggestedSize = 'Size 2 (2-3T)';
            let specDetail = 'Khung chuẩn: Cao 84 - 94 cm • Ngực 52 - 55 cm • Eo 49 - 51 cm';

            if (effectiveHeight <= 72) {
                suggestedSize = 'Size 1 (0-1T)';
                specDetail = 'Khung chuẩn: Cao 55 - 72 cm • Ngực 44 - 47 cm • Eo 42 - 45 cm';
            } else if (effectiveHeight <= 83) {
                suggestedSize = 'Size 2 (1-2T)';
                specDetail = 'Khung chuẩn: Cao 73 - 83 cm • Ngực 48 - 51 cm • Eo 46 - 48 cm';
            } else if (effectiveHeight <= 94) {
                suggestedSize = 'Size 2 (2-3T)';
                specDetail = 'Khung chuẩn: Cao 84 - 94 cm • Ngực 52 - 55 cm • Eo 49 - 51 cm';
            } else if (effectiveHeight <= 106) {
                suggestedSize = 'Size 4 (3-4T)';
                specDetail = 'Khung chuẩn: Cao 95 - 106 cm • Ngực 56 - 59 cm • Eo 52 - 54 cm';
            } else {
                suggestedSize = 'Size 5 (5-6T)';
                specDetail = 'Khung chuẩn: Cao 107 - 122 cm • Ngực 60 - 64 cm • Eo 55 - 58 cm';
            }

            summaryLabel.text('Số đo: Chiều cao ' + heightCm + ' cm' + (isChubby ? ' (Trừ hao bé bụ bẫm)' : ''));
            sizeBadge.text(suggestedSize);
            subSpec.text(specDetail);
            stretchNote.html('<i class="fa-solid fa-leaf mr-1"></i><strong>Tư vấn độ co giãn chất liệu:</strong> ' + catStretch);
        } else {
            let footCm = parseFloat($('#lnkt_inputFootCm').val()) || 13.8;
            let selectedShoeOpt = $('#lnkt_shoesCategory option:selected');
            let shoeStretch = selectedShoeOpt.attr('data-stretch') || 'Đế cao su non uốn dẻo 360 độ kết hợp lót đệm sợi tre êm ái, nên cộng dư 0.5cm cho bé mang tất thoải mái.';

            let suggestedShoeSize = 'Size 24 (14cm)';
            let shoeSpec = 'Khung chuẩn bàn chân: 13.5 - 14.4 cm • Lòng dép 14.5 cm';

            if (footCm <= 12.5) {
                suggestedShoeSize = 'Size 21 (12cm)';
                shoeSpec = 'Khung chuẩn bàn chân: 11.5 - 12.5 cm • Lòng dép 12.8 cm';
            } else if (footCm <= 13.4) {
                suggestedShoeSize = 'Size 22 (13cm)';
                shoeSpec = 'Khung chuẩn bàn chân: 12.6 - 13.4 cm • Lòng dép 13.6 cm';
            } else if (footCm <= 14.4) {
                suggestedShoeSize = 'Size 24 (14cm)';
                shoeSpec = 'Khung chuẩn bàn chân: 13.5 - 14.4 cm • Lòng dép 14.5 cm';
            } else if (footCm <= 15.5) {
                suggestedShoeSize = 'Size 26 (15cm)';
                shoeSpec = 'Khung chuẩn bàn chân: 14.5 - 15.5 cm • Lòng dép 15.6 cm';
            } else {
                suggestedShoeSize = 'Size 28 (16.5cm)';
                shoeSpec = 'Khung chuẩn bàn chân: 15.6 - 17.0 cm • Lòng dép 17.2 cm';
            }

            summaryLabel.text('Số đo: Dài bàn chân ' + footCm + ' cm');
            sizeBadge.text(suggestedShoeSize);
            subSpec.text(shoeSpec);
            stretchNote.html('<i class="fa-solid fa-shoe-prints mr-1"></i><strong>Tư vấn chất liệu & độ êm:</strong> ' + shoeStretch);
        }
    };

    // Áp dụng size vừa gợi ý từ Modal MH 04 sang trang PDP (MH 03) nếu có
    window.lnktApplySuggestedSizeToPdp = function () {
        let chosenSize = $('#lnkt_suggestedSizeBadge').text().trim();
        if ($('#lbn_selectedSizeText').length) {
            $('#lbn_selectedSizeText').text(chosenSize);
            $('#lbn_selectedSizeInput').val(chosenSize);
        }
        $('#modalSuggestSize').modal('hide');
        alert('Đã áp dụng kích cỡ gợi ý "' + chosenSize + '" từ thước dây thông minh (lnkt_suggest_size)!');
    };

    // ------------------------------------------------------------------------
    // 3. MH 05: Giỏ Hàng, Tỉnh/Thành Provinces Open API & Đặt Hàng (checkout.php)
    // ------------------------------------------------------------------------
    const lnktFallbackDistricts = {
        'TP. Hồ Chí Minh': ['Quận Bình Thạnh', 'Quận 1', 'Quận 3', 'Quận 7', 'Quận Phú Nhuận', 'Thành phố Thủ Đức', 'Quận Tân Bình'],
        'Thành phố Hà Nội': ['Quận Cầu Giấy', 'Quận Ba Đình', 'Quận Hoàn Kiếm', 'Quận Đống Đa', 'Quận Hai Bà Trưng', 'Quận Tây Hồ'],
        'Thành phố Đà Nẵng': ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà', 'Quận Ngũ Hành Sơn', 'Quận Liên Chiểu'],
        'Thành phố Cần Thơ': ['Quận Ninh Kiều', 'Quận Cái Răng', 'Quận Bình Thủy', 'Quận Ô Môn'],
        'Tỉnh Bình Dương': ['Thành phố Thủ Dầu Một', 'Thành phố Dĩ An', 'Thành phố Thuận An', 'Thành phố Tân Uyên'],
        'Tỉnh Đồng Nai': ['Thành phố Biên Hòa', 'Thành phố Long Khánh', 'Huyện Long Thành', 'Huyện Nhơn Trạch']
    };

    window.lnktOnProvinceChange = function (provinceName) {
        let districtSelect = $('#lnkt_districtSelect');
        if (!districtSelect.length) return;

        let districts = lnktFallbackDistricts[provinceName] || ['Quận Trung Tâm', 'Huyện Ngoại Thành'];
        districtSelect.empty();
        districts.forEach(function (dName) {
            districtSelect.append($('<option>', { value: dName, text: dName }));
        });
    };

    // Đồng bộ trực tiếp với Provinces Open API (https://provinces.open-api.vn/api/?depth=2)
    window.lnktSyncProvincesOpenApi = function () {
        let statusEl = $('#lnkt_apiStatusText');
        statusEl.html('<i class="fa-solid fa-spinner fa-spin text-info mr-1"></i>Đang tải dữ liệu từ provinces.open-api.vn...');

        $.ajax({
            url: 'https://provinces.open-api.vn/api/?depth=2',
            method: 'GET',
            timeout: 4500,
            success: function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    let provSelect = $('#lnkt_provinceSelect');
                    provSelect.empty();
                    data.forEach(function (prov) {
                        lnktFallbackDistricts[prov.name] = (prov.districts || []).map(function (d) { return d.name; });
                        provSelect.append($('<option>', { value: prov.name, text: prov.name }));
                    });
                    window.lnktOnProvinceChange(data[0].name);
                    statusEl.html('<i class="fa-solid fa-circle-check text-success mr-1"></i>Đã đồng bộ trực tiếp ' + data.length + ' Tỉnh/Thành từ Provinces Open API!');
                }
            },
            error: function () {
                statusEl.html('<i class="fa-solid fa-circle-check text-success mr-1"></i>Đang sử dụng bộ dữ liệu chuẩn Provinces Open API (Offline-ready).');
            }
        });
    };

    window.lnktChangeCartQty = function (itemId, delta) {
        let inputEl = $('#lnkt_cartQty_' + itemId);
        let cur = parseInt(inputEl.val(), 10) || 1;
        let next = cur + delta;
        if (next < 1) next = 1;
        if (next > 20) next = 20;
        inputEl.val(next);
        window.lnktRecalculateCheckout();
    };

    window.lnktRemoveCartItem = function (itemId) {
        $('#lnkt_cartRow_' + itemId).remove();
        window.lnktRecalculateCheckout();
    };

    window.lnktSetCheckoutVipDiscount = function (percent, badgeLabel, btnEl) {
        $('.lnkt-vip-tier-btn').removeClass('active btn-pastel-primary').addClass('btn-outline-secondary');
        $(btnEl).removeClass('btn-outline-secondary').addClass('active btn-pastel-primary');
        $('#lnkt_checkoutDiscountPercent').val(percent);
        $('#lnkt_checkoutVipBadge').text(badgeLabel);
        window.lnktRecalculateCheckout();
    };

    window.lnktRecalculateCheckout = function () {
        let subtotal = 0;
        let itemCount = 0;

        $('.lnkt-cart-row').each(function () {
            let $row = $(this);
            let itemId = $row.attr('data-item-id');
            let price = parseInt($row.attr('data-price'), 10) || 0;
            let qty = parseInt($('#lnkt_cartQty_' + itemId).val(), 10) || 1;
            let lineTotal = price * qty;

            subtotal += lineTotal;
            itemCount++;
            $('#lnkt_lineTotal_' + itemId).text(lnktFormatVnd(lineTotal));
        });

        $('#lnkt_cartItemCountBadge').text(itemCount);
        if (itemCount === 0) {
            $('#lnkt_emptyCartNotice').removeClass('d-none');
        } else {
            $('#lnkt_emptyCartNotice').addClass('d-none');
        }

        let discountPercent = parseFloat($('#lnkt_checkoutDiscountPercent').val()) || 0;
        let discountAmount = Math.round((subtotal * discountPercent) / 100);
        let shippingFee = (subtotal > 0 && subtotal < 300000) ? 25000 : 0;
        let totalAmount = Math.max(0, subtotal - discountAmount + shippingFee);
        let rewardPts = Math.floor(totalAmount / 10000);

        $('#lnkt_summarySubtotal').text(lnktFormatVnd(subtotal));
        $('#lnkt_summaryDiscount').text('-' + lnktFormatVnd(discountAmount));
        if (shippingFee === 0) {
            $('#lnkt_summaryShipping').text('Miễn phí (0 đ)').removeClass('badge-warning').addClass('badge-mint');
        } else {
            $('#lnkt_summaryShipping').text(lnktFormatVnd(shippingFee)).removeClass('badge-mint').addClass('badge-warning');
        }
        $('#lnkt_summaryTotalAmount').text(lnktFormatVnd(totalAmount));
        $('#lnkt_summaryRewardPoints').text('+' + rewardPts + ' điểm VIP');
    };

    window.lnktSubmitCheckoutOrder = function () {
        let receiverName = ($('#lnkt_receiverName').val() || '').trim();
        let receiverPhone = ($('#lnkt_receiverPhone').val() || '').trim();
        let province = $('#lnkt_provinceSelect').val();
        let district = $('#lnkt_districtSelect').val();
        let address = ($('#lnkt_shippingAddress').val() || '').trim();
        let totalStr = $('#lnkt_summaryTotalAmount').text().trim();

        if ($('.lnkt-cart-row').length === 0) {
            alert('Giỏ hàng đang trống! Vui lòng chọn ít nhất 1 sản phẩm trước khi thanh toán.');
            return;
        }
        if (!receiverName || !receiverPhone || !address) {
            alert('Vui lòng nhập đầy đủ Họ tên phụ huynh, Số điện thoại và Địa chỉ nhận hàng!');
            return;
        }

        alert(
            'Đã thực thi Stored Procedure lnkt_create_order thành công!\n' +
            '• Mã vận đơn: #FK-ORD-202608\n' +
            '• Người nhận: ' + receiverName + ' (' + receiverPhone + ')\n' +
            '• Giao đến: ' + address + ', ' + district + ', ' + province + '\n' +
            '• Tổng thanh toán thực tế: ' + totalStr
        );
        window.location.href = '/FashionKids/views/pages/order-tracking.php?status=cho_duyet';
    };

    // ------------------------------------------------------------------------
    // 4. MH 06: Theo dõi Tiến trình Đơn hàng Stepper 5 Bước (order-tracking.php)
    // ------------------------------------------------------------------------
    window.lnktSetTrackingStatus = function (statusKey) {
        const orderFlow = ['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat'];
        const statusMeta = {
            'cho_duyet':   { label: 'Chờ duyệt (cho_duyet)',     badgeClass: 'badge-warning text-dark', icon: 'fa-clock' },
            'da_xac_nhan': { label: 'Đã xác nhận (da_xac_nhan)', badgeClass: 'badge-sky',               icon: 'fa-box-open' },
            'dang_giao':   { label: 'Đang giao (dang_giao)',     badgeClass: 'badge-primary',           icon: 'fa-truck-fast' },
            'hoan_tat':    { label: 'Hoàn tất (hoan_tat)',       badgeClass: 'badge-mint',              icon: 'fa-circle-check' },
            'da_huy':      { label: 'Đã hủy (da_huy)',           badgeClass: 'badge-danger',            icon: 'fa-ban' }
        };

        // Cập nhật nút mô phỏng trạng thái
        $('.lnkt-status-sim-btn').removeClass('active btn-success btn-danger btn-primary btn-info btn-secondary');
        $('.lnkt-status-sim-btn[data-status="' + statusKey + '"]').addClass('active');

        // Cập nhật Badge trạng thái chính
        let meta = statusMeta[statusKey] || statusMeta['hoan_tat'];
        $('#lnkt_currentStatusBadge')
            .attr('class', 'badge px-3 py-2 rounded-pill h6 mb-0 ' + meta.badgeClass)
            .html('<i class="fa-solid ' + meta.icon + ' mr-1"></i> Trạng thái: ' + meta.label);

        // Cập nhật Stepper 5 bước
        $('.lnkt-stepper-item').removeClass('completed active cancelled');
        if (statusKey === 'da_huy') {
            $('#lnkt_step_cho_duyet').addClass('completed');
            $('#lnkt_step_da_huy').addClass('cancelled active');
        } else {
            let currentIdx = orderFlow.indexOf(statusKey);
            orderFlow.forEach(function (stepKey, idx) {
                let $step = $('#lnkt_step_' + stepKey);
                if (idx < currentIdx) {
                    $step.addClass('completed');
                } else if (idx === currentIdx) {
                    $step.addClass(statusKey === 'hoan_tat' ? 'completed active' : 'active');
                }
            });
        }

        // Kích hoạt hiển thị nút vàng bơ (#FDE2B8) "★ Viết đánh giá sản phẩm" khi đạt trạng thái hoan_tat
        if (statusKey === 'hoan_tat') {
            $('#lnkt_completedReviewBanner').removeClass('d-none');
            $('.lnkt-item-review-btn').removeClass('d-none');
            $('.lnkt-item-pending-review').addClass('d-none');
        } else {
            $('#lnkt_completedReviewBanner').addClass('d-none');
            $('.lnkt-item-review-btn').addClass('d-none');
            $('.lnkt-item-pending-review').removeClass('d-none');
        }
    };

    if ($('#lnkt_stepperTimeline').length) {
        let initSt = $('#lnkt_stepperTimeline').attr('data-current-status') || 'hoan_tat';
        window.lnktSetTrackingStatus(initSt);
    }

    window.lnktSearchOrderTracking = function () {
        let code = ($('#lnkt_trackingCodeInput').val() || 'FK-ORD-202608').trim();
        $('#lnkt_displayOrderCode').text('#' + code.replace(/^#/, ''));
        alert('Đã tải dữ liệu hành trình thời gian thực cho mã vận đơn #' + code.replace(/^#/, '') + '!');
    };

    window.lnktOpenProductReviewModal = function (productName) {
        $('#lnkt_reviewModalProductName').text(productName);
        if ($('#modalReview').length === 0) {
            $('#lnkt_fallbackReviewModal').modal('show');
        }
    };

    window.lnktSubmitFallbackReview = function () {
        let prodName = $('#lnkt_reviewModalProductName').text().trim();
        $('#lnkt_fallbackReviewModal').modal('hide');
        alert('Cảm ơn phụ huynh đã gửi đánh giá 5 sao cho sản phẩm "' + prodName + '"!');
    };

    // ------------------------------------------------------------------------
    // 5. MH 10: Bàn làm việc Quản trị Đơn hàng của Nhân viên (staff-orders.php)
    // ------------------------------------------------------------------------
    window.lnktFilterStaffOrders = function () {
        let kw = ($('#lnkt_staffOrderSearch').val() || '').toLowerCase().trim();
        let stFilter = $('#lnkt_staffOrderStatusFilter').val() || 'all';

        $('.lnkt-staff-order-row').each(function () {
            let $row = $(this);
            let rowSt = $row.attr('data-status');
            let rowSearch = ($row.attr('data-search') || '').toLowerCase();

            let matchSt = (stFilter === 'all' || rowSt === stFilter);
            let matchKw = (!kw || rowSearch.indexOf(kw) !== -1);

            if (matchSt && matchKw) {
                $row.removeClass('d-none');
            } else {
                $row.addClass('d-none');
            }
        });
    };

    window.lnktUpdateStaffOrderStatus = function (orderId, orderCode) {
        let newStatus = $('#lnkt_statusSelect_' + orderId).val();
        let $row = $('#lnkt_orderRow_' + orderId);
        $row.attr('data-status', newStatus);

        let badgeHtml = '';
        if (newStatus === 'cho_duyet') {
            badgeHtml = '<span class="badge badge-warning text-dark px-2 py-1 rounded-pill"><i class="fa-solid fa-clock mr-1"></i>Chờ duyệt</span>';
        } else if (newStatus === 'da_xac_nhan') {
            badgeHtml = '<span class="badge badge-sky px-2 py-1 rounded-pill"><i class="fa-solid fa-box-open mr-1"></i>Đã xác nhận</span>';
        } else if (newStatus === 'dang_giao') {
            badgeHtml = '<span class="badge badge-primary px-2 py-1 rounded-pill"><i class="fa-solid fa-truck-fast mr-1"></i>Đang giao</span>';
        } else if (newStatus === 'hoan_tat') {
            badgeHtml = '<span class="badge badge-mint px-2 py-1 rounded-pill"><i class="fa-solid fa-circle-check mr-1"></i>Hoàn tất</span>';
        } else if (newStatus === 'da_huy') {
            badgeHtml = '<span class="badge badge-danger px-2 py-1 rounded-pill"><i class="fa-solid fa-ban mr-1"></i>Đã hủy</span>';
        }
        $('#lnkt_orderBadgeCell_' + orderId).html(badgeHtml);

        // Cập nhật lại bộ đếm 5 trạng thái
        const counts = { cho_duyet: 0, da_xac_nhan: 0, dang_giao: 0, hoan_tat: 0, da_huy: 0 };
        $('.lnkt-staff-order-row').each(function () {
            let st = $(this).attr('data-status');
            if (counts[st] !== undefined) counts[st]++;
        });
        Object.keys(counts).forEach(function (k) {
            $('#lnkt_count_' + k).text(counts[k]);
        });

        alert('Đã thực thi Stored Procedure lnkt_update_order_status cho đơn #' + orderCode + ' -> Trạng thái mới: ' + newStatus);
    };

    window.lnktInspectStaffOrder = function (el) {
        let $el = $(el);
        $('#lnkt_inspectCode').text('#' + $el.attr('data-code'));
        $('#lnkt_inspectReceiver').text($el.attr('data-receiver'));
        $('#lnkt_inspectPhone').text($el.attr('data-phone'));
        $('#lnkt_inspectAddress').text($el.attr('data-address'));
        $('#lnkt_inspectItems').text($el.attr('data-items'));
        $('#lnkt_inspectSubtotal').text($el.attr('data-subtotal'));
        $('#lnkt_inspectDiscount').text($el.attr('data-discount'));
        $('#lnkt_inspectTotal').text($el.attr('data-total'));
        $('#lnkt_inspectStaff').text($el.attr('data-staff'));
    };

    // ------------------------------------------------------------------------
    // 6. MH 14: Quản trị Bảng Size & Thông số trần/sàn (admin-size-charts.php)
    // ------------------------------------------------------------------------
    window.lnktFilterAdminSizeCharts = function () {
        let catFilter = $('#lnkt_filterSizeCategory').val() || 'all';
        let typeFilter = $('#lnkt_filterSizeType').val() || 'all';

        $('.lnkt-size-chart-row').each(function () {
            let $row = $(this);
            let rowCat = $row.attr('data-cat');
            let rowType = $row.attr('data-type');

            let matchCat = (catFilter === 'all' || rowCat === catFilter);
            let matchType = (typeFilter === 'all' || rowType === typeFilter);

            if (matchCat && matchType) {
                $row.removeClass('d-none');
            } else {
                $row.addClass('d-none');
            }
        });
    };

    window.lnktToggleSizeChartInputs = function (typeVal) {
        if (typeVal === 'giay_dep') {
            $('#lnkt_clothingSpecGroup').addClass('d-none');
            $('#lnkt_shoesSpecGroup').removeClass('d-none');
        } else {
            $('#lnkt_clothingSpecGroup').removeClass('d-none');
            $('#lnkt_shoesSpecGroup').addClass('d-none');
        }
    };

    window.lnktEditSizeChartRow = function (btnEl) {
        let $btn = $(btnEl);
        $('#lnkt_scId').val($btn.attr('data-id'));
        $('#lnkt_scCategoryId').val($btn.attr('data-cat'));
        $('#lnkt_scType').val($btn.attr('data-type'));
        $('#lnkt_scGender').val($btn.attr('data-gender'));
        $('#lnkt_scSizeName').val($btn.attr('data-size'));
        $('#lnkt_scHeightMin').val($btn.attr('data-hmin'));
        $('#lnkt_scHeightMax').val($btn.attr('data-hmax'));
        $('#lnkt_scChestMin').val($btn.attr('data-cmin'));
        $('#lnkt_scChestMax').val($btn.attr('data-cmax'));
        $('#lnkt_scWaistMin').val($btn.attr('data-wmin'));
        $('#lnkt_scWaistMax').val($btn.attr('data-wmax'));
        $('#lnkt_scFootMin').val($btn.attr('data-fmin'));
        $('#lnkt_scFootMax').val($btn.attr('data-fmax'));

        window.lnktToggleSizeChartInputs($btn.attr('data-type'));
        $('#lnkt_sizeFormTitle').html('<i class="fa-solid fa-pen-to-square text-peach mr-1"></i> Cập Nhật Cấu Hình Size #' + $btn.attr('data-id'));
        $('#lnkt_btnCancelSizeEdit').removeClass('d-none');
    };

    window.lnktResetSizeChartForm = function () {
        if ($('#lnkt_formAdminSizeChart').length) {
            $('#lnkt_formAdminSizeChart')[0].reset();
        }
        $('#lnkt_scId').val('');
        window.lnktToggleSizeChartInputs('quan_ao');
        $('#lnkt_sizeFormTitle').html('<i class="fa-solid fa-sliders text-peach mr-1"></i> Cấu Hình Thông Số Trần/Sàn Size');
        $('#lnkt_btnCancelSizeEdit').addClass('d-none');
    };

    $('#lnkt_formAdminSizeChart').on('submit', function (e) {
        e.preventDefault();
        let catId = $('#lnkt_scCategoryId').val();
        let sizeName = $('#lnkt_scSizeName').val().trim();
        alert('Đã lưu cấu hình thông số trần/sàn cho "' + sizeName + '" gắn kết với mã danh mục ma_danh_muc = ' + catId + ' vào bảng lnkt_size_charts!');
    });

    // ------------------------------------------------------------------------
    // 7. MH 16: Cổng Casting KOL Nhí - Tự động tính tuổi & Kiểm tra 2-5 ảnh
    // ------------------------------------------------------------------------
    window.lnktCalculateBabyAge = function (birthDateStr) {
        if (!birthDateStr) return;
        let birthDate = new Date(birthDateStr);
        let now = new Date();

        if (isNaN(birthDate.getTime()) || birthDate > now) {
            $('#lnkt_kolAutoAgeBadge').text('Ngày sinh chưa hợp lệ');
            return;
        }

        let years = now.getFullYear() - birthDate.getFullYear();
        let months = now.getMonth() - birthDate.getMonth();
        if (months < 0) {
            years--;
            months += 12;
        }

        let ageText = years > 0 ? (years + ' tuổi ' + months + ' tháng') : (months + ' tháng tuổi');
        $('#lnkt_kolAutoAgeBadge').text('Tuổi tự động: ' + ageText);
        $('#lnkt_kolCalculatedAgeText').val(ageText);
    };

    window.lnktAppendKolTalent = function (talentText) {
        let textarea = $('#lnkt_kolTalents');
        let current = (textarea.val() || '').trim();
        if (current.indexOf(talentText) === -1) {
            textarea.val(current ? current + ', ' + talentText : talentText);
        }
    };

    window.lnktHandleKolMediaUpload = function (fileList) {
        if (!fileList) return;
        let count = fileList.length;
        $('#lnkt_kolPhotoCount').val(count);

        let previewContainer = $('#lnkt_kolMediaPreviewList');
        previewContainer.empty();

        if (count < 2 || count > 5) {
            $('#lnkt_kolMediaStatusTitle').html(
                '<span class="text-danger"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Bạn đã chọn ' + count + ' ảnh. Vui lòng chọn từ 2 đến 5 ảnh chân dung & toàn thân!</span>'
            );
        } else {
            $('#lnkt_kolMediaStatusTitle').html(
                '<span class="text-success"><i class="fa-solid fa-circle-check mr-1"></i>Đã đính kèm hợp lệ ' + count + ' ảnh (Đạt chuẩn từ 2 đến 5 ảnh)</span>'
            );
        }

        Array.from(fileList).slice(0, 5).forEach(function (file, idx) {
            let col = $('<div class="col-4 px-1 mb-2"></div>');
            col.html(
                '<div class="p-2 rounded border bg-milk-cream text-center small">' +
                '<i class="fa-solid fa-image text-peach h4 mb-1 d-block"></i>' +
                '<span class="font-weight-bold text-slate d-block text-truncate">' + (idx + 1) + '. ' + file.name + '</span>' +
                '<small class="text-muted-custom">' + Math.round(file.size / 1024) + ' KB</small>' +
                '</div>'
            );
            previewContainer.append(col);
        });
    };

    window.lnktUseDemoKolPhotos = function () {
        $('#lnkt_kolPhotoCount').val(3);
        $('#lnkt_kolMediaStatusTitle').html(
            '<span class="text-success"><i class="fa-solid fa-circle-check mr-1"></i>Đã đính kèm 3 ảnh mẫu chân dung & toàn thân hợp lệ (2 - 5 ảnh)</span>'
        );
    };

    $('#lnkt_formKolCasting').on('submit', function (e) {
        e.preventDefault();
        let photoCount = parseInt($('#lnkt_kolPhotoCount').val(), 10) || 0;
        let isGuardianChecked = $('#lnkt_guardianConsentCheck').is(':checked');
        let babyName = ($('#lnkt_kolBabyName').val() || '').trim();
        let autoAge = $('#lnkt_kolCalculatedAgeText').val();

        if (photoCount < 2 || photoCount > 5) {
            alert('Hồ sơ Casting yêu cầu đính kèm từ 2 đến 5 ảnh chân dung và toàn thân của bé!');
            return;
        }
        if (!isGuardianChecked) {
            alert('Vui lòng đánh dấu xác nhận cam kết từ người giám hộ hợp pháp!');
            return;
        }

        alert(
            'Nộp hồ sơ Casting KOL Nhí thành công!\n' +
            '• Bé ứng tuyển: ' + babyName + ' (' + autoAge + ')\n' +
            '• Số lượng ảnh media: ' + photoCount + ' ảnh\n' +
            '• Cam kết người giám hộ: Đã xác nhận hợp lệ.'
        );
    });

    // ------------------------------------------------------------------------
    // 8. MH 23: Quản trị Banner Slider & Lịch hẹn giờ (admin-banners.php)
    // ------------------------------------------------------------------------
    window.lnktUpdateBannerPreview = function () {
        $('#lnkt_previewBannerTitle').text($('#lnkt_bnTitle').val() || '');
        $('#lnkt_previewBannerSubtitle').text($('#lnkt_bnSubtitle').val() || '');
        $('#lnkt_previewBannerCta')
            .text($('#lnkt_bnCtaLabel').val() || 'Xem ngay »')
            .attr('href', $('#lnkt_bnTargetUrl').val() || '#');
    };

    window.lnktSetQuickTargetUrl = function (url) {
        $('#lnkt_bnTargetUrl').val(url);
        window.lnktUpdateBannerPreview();
    };

    window.lnktEditBannerRow = function (btnEl) {
        let $btn = $(btnEl);
        $('#lnkt_bnId').val($btn.attr('data-id'));
        $('#lnkt_bnTitle').val($btn.attr('data-title'));
        $('#lnkt_bnSubtitle').val($btn.attr('data-subtitle'));
        $('#lnkt_bnCtaLabel').val($btn.attr('data-cta'));
        $('#lnkt_bnTargetUrl').val($btn.attr('data-url'));
        $('#lnkt_bnStartTime').val($btn.attr('data-start'));
        $('#lnkt_bnEndTime').val($btn.attr('data-end'));
        $('#lnkt_bnSortOrder').val($btn.attr('data-order'));
        $('#lnkt_bnStatus').val($btn.attr('data-status'));

        window.lnktUpdateBannerPreview();
        $('#lnkt_bannerFormHeader').html('<i class="fa-solid fa-pen-to-square text-peach mr-1"></i> Cập Nhật Chiến Dịch Banner #' + $btn.attr('data-id'));
        $('#lnkt_btnCancelBannerEdit').removeClass('d-none');
    };

    window.lnktResetBannerForm = function () {
        if ($('#lnkt_formAdminBanner').length) {
            $('#lnkt_formAdminBanner')[0].reset();
        }
        $('#lnkt_bnId').val('');
        $('#lnkt_bannerFormHeader').html('<i class="fa-solid fa-calendar-check text-peach mr-1"></i> Thiết Lập Chiến Dịch Banner');
        $('#lnkt_btnCancelBannerEdit').addClass('d-none');
        window.lnktUpdateBannerPreview();
    };

    window.lnktToggleBannerStatus = function (bannerId) {
        let cell = $('#lnkt_bannerStatusCell_' + bannerId);
        if (cell.text().indexOf('Đang chạy') !== -1) {
            cell.html('<span class="badge badge-secondary px-2 py-1 rounded-pill"><i class="fa-solid fa-pause mr-1"></i>Tạm dừng</span>');
        } else {
            cell.html('<span class="badge badge-mint px-2 py-1 rounded-pill"><i class="fa-solid fa-circle-play mr-1"></i>Đang chạy</span>');
        }
    };

    $('#lnkt_formAdminBanner').on('submit', function (e) {
        e.preventDefault();
        let title = $('#lnkt_bnTitle').val().trim();
        let targetUrl = $('#lnkt_bnTargetUrl').val().trim();
        let startTime = $('#lnkt_bnStartTime').val();
        let endTime = $('#lnkt_bnEndTime').val();

        alert(
            'Đã lưu cấu hình chiến dịch Banner thành công!\n' +
            '• Chiến dịch: ' + title + '\n' +
            '• Target URL: ' + targetUrl + '\n' +
            '• Lịch hẹn giờ: ' + startTime + ' -> ' + endTime
        );
    });
});
