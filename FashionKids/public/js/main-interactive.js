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
    // 1. MH 02: Bộ lọc Đa tiêu chí & Thanh trượt Khoảng giá AJAX (shop-catalog.php)
    //    Gửi request ngầm tới ProductController@ajaxFilter & cập nhật .product-grid
    // ------------------------------------------------------------------------
    function lbnFormatVnd(amount) {
        return Number(amount).toLocaleString('vi-VN') + ' đ';
    }

    function lbnEscapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function lbnBuildCatalogCardHtml(prod) {
        let genderHtml = '';
        if (prod.gender === 'girl') {
            genderHtml = '<span class="badge badge-pill bg-peach text-white lbn-badge-gender-overlay"><i class="fa-solid fa-venus mr-1"></i>Bé gái</span>';
        } else if (prod.gender === 'boy') {
            genderHtml = '<span class="badge badge-pill badge-sky lbn-badge-gender-overlay"><i class="fa-solid fa-mars mr-1"></i>Bé trai</span>';
        } else {
            genderHtml = '<span class="badge badge-pill badge-gold lbn-badge-gender-overlay"><i class="fa-solid fa-venus-mars mr-1"></i>Unisex</span>';
        }

        let stockQty = parseInt(prod.stock_quantity, 10) || 0;
        let stockHtml = '';
        if (stockQty <= 0) {
            stockHtml = '<small class="text-danger font-weight-bold"><i class="fa-solid fa-circle-xmark mr-1"></i>Tạm hết hàng</small>';
        } else if (stockQty <= 5) {
            stockHtml = '<small class="text-danger font-weight-bold"><i class="fa-solid fa-fire mr-1"></i>Chỉ còn ' + stockQty + ' SP</small>';
        } else {
            stockHtml = '<small class="text-success font-weight-bold"><i class="fa-solid fa-check mr-1"></i>Còn hàng (' + stockQty + ')</small>';
        }

        let safeName = lbnEscapeHtml(prod.name);
        let safeCatName = lbnEscapeHtml(prod.category_name);
        let safeSku = lbnEscapeHtml(prod.sku);
        let safeImg = lbnEscapeHtml(prod.image || '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg');
        let safeBamboo = lbnEscapeHtml(prod.bamboo_label || 'Vải Sợi Tre Kháng Khuẩn');
        let safeSizeRange = lbnEscapeHtml(prod.size_range || 'Size 1 - Size 5');
        let priceFormatted = lbnFormatVnd(prod.price);
        let ratingStr = Number(prod.rating || 4.9).toFixed(1);
        let reviewsCount = parseInt(prod.reviews, 10) || 25;

        return (
            '<div class="col-12 col-sm-6 col-lg-4 mb-4 lbn-catalog-col" ' +
                'data-id="' + prod.id + '" ' +
                'data-category="' + lbnEscapeHtml(prod.category_slug) + '" ' +
                'data-gender="' + lbnEscapeHtml(prod.gender) + '" ' +
                'data-price="' + prod.price + '" ' +
                'data-name="' + lbnEscapeHtml(prod.name + ' ' + prod.category_name + ' ' + prod.sku) + '" ' +
                'data-bamboo="' + (prod.is_bamboo ? 1 : 0) + '">' +
                '<div class="lbn-product-card h-100 d-flex flex-column shadow-sm">' +
                    '<div class="lbn-product-img-wrap">' +
                        '<span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">' +
                            '<i class="fa-solid fa-leaf mr-1"></i>' + safeBamboo +
                        '</span>' +
                        genderHtml +
                        '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="lbn-img-link text-decoration-none">' +
                            '<img src="' + safeImg + '" alt="' + safeName + '" class="lbn-product-img">' +
                        '</a>' +
                    '</div>' +
                    '<div class="card-body d-flex flex-column p-3">' +
                        '<div class="d-flex justify-content-between align-items-center mb-1">' +
                            '<span class="small font-weight-bold text-muted-custom">' + safeCatName + '</span>' +
                            '<span class="small text-warning font-weight-bold">' +
                                '<i class="fa-solid fa-star"></i> ' + ratingStr + ' ' +
                                '<span class="text-muted-custom font-weight-normal">(' + reviewsCount + ')</span>' +
                            '</span>' +
                        '</div>' +
                        '<h6 class="font-weight-bold text-slate mb-2">' +
                            '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="text-slate text-decoration-none">' +
                                safeName +
                            '</a>' +
                        '</h6>' +
                        '<div class="d-flex justify-content-between align-items-center small text-muted-custom mb-3">' +
                            '<span><i class="fa-solid fa-barcode mr-1"></i>' + safeSku + '</span>' +
                            '<span><i class="fa-solid fa-ruler-combined mr-1"></i>' + safeSizeRange + '</span>' +
                        '</div>' +
                        '<div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">' +
                            '<div>' +
                                '<div class="h5 font-weight-bold text-peach mb-0">' + priceFormatted + '</div>' +
                                stockHtml +
                            '</div>' +
                            '<div class="d-flex">' +
                                '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" ' +
                                   'class="btn btn-light btn-sm rounded-pill border mr-1 px-2 text-slate" title="Xem chi tiết PDP">' +
                                    '<i class="fa-regular fa-eye"></i>' +
                                '</a>' +
                                '<button type="button" ' +
                                        'onclick="lbnQuickAddToCart(\'' + safeName.replace(/'/g, "\\'") + '\', ' + prod.price + ', ' + prod.id + ')" ' +
                                        'class="btn btn-pastel-primary btn-sm px-3 py-1" ' + (stockQty <= 0 ? 'disabled' : '') + '>' +
                                    '<i class="fa-solid fa-cart-plus mr-1"></i> ' + (stockQty <= 0 ? 'Hết hàng' : 'Thêm') +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
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

    // Hàm thực thi lọc đa tiêu chí trên lưới sản phẩm .product-grid (#lbn_catalogGrid) kết hợp AJAX ProductController@ajaxFilter
    window.lbnApplyCatalogFilters = function () {
        let selectedCat = $('#lbn_selectedCategory').val() || 'all';
        let selectedGender = $('input[name="lbn_gender_filter"]:checked').val() || 'all';
        let maxPrice = parseInt($('#lbn_priceRangeSlider').val(), 10) || 500000;
        let keyword = ($('#lbn_keywordFilter').val() || '').toLowerCase().trim();
        let onlyBamboo = $('#lbn_onlyBambooCheck').is(':checked');
        let sortMode = $('#lbn_sortSelect').val() || 'newest';

        // 1. Phản hồi tức thì trên DOM (0ms lag)
        let visibleCount = 0;
        let gridContainer = $('.product-grid, #lbn_catalogGrid').first();
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

        // 2. Gửi request ngầm AJAX về ProductController@ajaxFilter để đồng bộ dữ liệu thực từ CSDL lbn_san_pham
        clearTimeout(window.lbnAjaxFilterDebounce);
        window.lbnAjaxFilterDebounce = setTimeout(function () {
            $.ajax({
                url: '/FashionKids/index.php?c=product&a=ajaxFilter',
                type: 'GET',
                dataType: 'json',
                data: {
                    category:    selectedCat,
                    gender:      selectedGender,
                    min_price:   0,
                    max_price:   maxPrice,
                    keyword:     keyword,
                    only_bamboo: onlyBamboo ? 1 : 0,
                    sort:        sortMode
                },
                timeout: 3000,
                success: function (res) {
                    if (res && res.status === 'success' && Array.isArray(res.products)) {
                        let htmlCards = '';
                        res.products.forEach(function (prod) {
                            htmlCards += lbnBuildCatalogCardHtml(prod);
                        });
                        gridContainer.html(htmlCards);
                        $('#lbn_visibleProductCount').text(res.products.length);

                        if (res.products.length === 0) {
                            $('#lbn_emptyCatalogAlert').removeClass('d-none');
                        } else {
                            $('#lbn_emptyCatalogAlert').addClass('d-none');
                        }

                        if (res.category_counts) {
                            Object.keys(res.category_counts).forEach(function (slugKey) {
                                if (slugKey !== 'by_id') {
                                    $('#lbn_catCount_' + slugKey).text(res.category_counts[slugKey]);
                                }
                            });
                        }
                    }
                }
            });
        }, 120);
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

    // Thêm nhanh vào giỏ hàng từ thẻ sản phẩm (kiểm tra tồn kho qua ProductController@ajaxCheckStock)
    window.lbnQuickAddToCart = function (productName, price, productId) {
        let pid = parseInt(productId, 10) || 101;
        $.ajax({
            url: '/FashionKids/index.php?c=product&a=ajaxCheckStock',
            type: 'POST',
            dataType: 'json',
            data: {
                ma_san_pham: pid,
                product_id:  pid,
                so_luong:    1,
                quantity:    1,
                size:        'Size 2 (2-3T)',
                action_type: 'quick_add'
            },
            timeout: 3000,
            success: function (res) {
                if (res && res.status === 'success') {
                    let badge = $('.cart-badge-custom').first();
                    let nextCount = res.cart_count || ((parseInt(badge.text(), 10) || 0) + 1);
                    badge.text(nextCount);
                    alert('Đã kiểm tra tồn kho & thêm "' + productName + '" (' + lbnFormatVnd(price) + ') vào giỏ hàng!');
                } else {
                    alert((res && res.message) ? res.message : 'Sản phẩm hiện đã hết hàng!');
                }
            },
            error: function (xhr) {
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                    return;
                }
                let badge = $('.cart-badge-custom').first();
                let currentCount = parseInt(badge.text(), 10) || 0;
                badge.text(currentCount + 1);
                alert('Đã thêm "' + productName + '" (' + lbnFormatVnd(price) + ') vào giỏ hàng!');
            }
        });
    };

    // ------------------------------------------------------------------------
    // 2. MH 03: Trang Chi tiết Sản phẩm PDP & Kiểm tra tồn kho Realtime AJAX
    //    Gọi Stored Function fn_lbn_kiem_tra_ton_kho(ma_san_pham, so_luong)
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

    // Cập nhật giao diện nút đặt hàng & cảnh báo tồn kho trên PDP
    window.lbnRenderPdpStockState = function (availableStock, requestedQty, sizeLabel) {
        let stock = parseInt(availableStock, 10);
        if (isNaN(stock)) stock = 0;
        let reqQty = parseInt(requestedQty, 10) || 1;

        $('#lbn_pdpStockCount').text(stock);
        $('#lbn_pdpQuantity').attr('max', Math.max(1, stock)).attr('data-max', stock);

        let $btnAdd = $('#lbn_btnAddToCartPdp');
        let $btnAddText = $('#lbn_btnAddToCartText');
        let $btnBuy = $('#lbn_btnBuyNowPdp');
        let $alertBox = $('#lbn_pdpStockAlert');

        if (stock <= 0) {
            // Tồn kho = 0: Vô hiệu hóa nút "Thêm vào giỏ" và đổi nhãn thành "Tạm hết hàng"
            $btnAdd.prop('disabled', true).addClass('disabled');
            if ($btnAddText.length) {
                $btnAddText.text('Tạm hết hàng');
            } else {
                $btnAdd.html('<i class="fa-solid fa-ban mr-2"></i>Tạm hết hàng');
            }
            $btnBuy.prop('disabled', true).addClass('disabled');

            $alertBox
                .removeClass('d-none alert-mint text-slate')
                .addClass('alert-danger d-block')
                .html('<i class="fa-solid fa-circle-exclamation mr-1"></i><strong>Tạm hết hàng:</strong> Sản phẩm kích cỡ <strong>' + lbnEscapeHtml(sizeLabel || '') + '</strong> hiện có tồn kho = 0 trong CSDL <code>lbn_san_pham</code>.');
        } else if (reqQty > stock) {
            // Chặn đặt hàng nếu số lượng chọn vượt quá tồn kho thực tế
            $btnAdd.prop('disabled', true).addClass('disabled');
            if ($btnAddText.length) {
                $btnAddText.text('Vượt quá tồn kho');
            }
            $btnBuy.prop('disabled', true).addClass('disabled');

            $alertBox
                .removeClass('d-none alert-mint text-slate')
                .addClass('alert-danger d-block')
                .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i><strong>Chặn đặt hàng:</strong> Số lượng chọn (<strong>' + reqQty + '</strong>) vượt quá tồn kho thực tế (<strong>' + stock + '</strong> sản phẩm)!');
        } else {
            // Đủ hàng hợp lệ
            $btnAdd.prop('disabled', false).removeClass('disabled');
            if ($btnAddText.length) {
                $btnAddText.text('Thêm Vào Giỏ Hàng');
            } else {
                $btnAdd.html('<i class="fa-solid fa-cart-plus mr-2"></i>Thêm Vào Giỏ Hàng');
            }
            $btnBuy.prop('disabled', false).removeClass('disabled');

            $alertBox
                .removeClass('d-none alert-danger')
                .addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
                .html('<i class="fa-solid fa-circle-check text-success mr-1"></i><strong>Tồn kho khả dụng (<code>fn_lbn_kiem_tra_ton_kho</code>):</strong> Phân loại <strong>' + lbnEscapeHtml(sizeLabel || '') + '</strong> còn <strong>' + stock + '</strong> sản phẩm tại kho.');
        }
    };

    // Gọi API ProductController@ajaxCheckStock kiểm tra tồn kho realtime
    window.lbnCheckPdpStockAjax = function (actionType, onDone) {
        let productId = parseInt($('#lbn_pdpProductId').val(), 10) || 101;
        let qty = parseInt($('#lbn_pdpQuantity').val(), 10) || 1;
        let chosenSize = $('#lbn_selectedSizeInput').val() || $('#lbn_selectedSizeText').text().trim() || 'Size 2 (2-3T)';

        $.ajax({
            url: '/FashionKids/index.php?c=product&a=ajaxCheckStock',
            type: 'POST',
            dataType: 'json',
            data: {
                ma_san_pham: productId,
                product_id:  productId,
                so_luong:    qty,
                quantity:    qty,
                size:        chosenSize,
                action_type: actionType || 'check'
            },
            timeout: 3000,
            success: function (res) {
                let stock = (res && res.available_stock !== undefined)
                    ? parseInt(res.available_stock, 10)
                    : (parseInt($('#lbn_pdpQuantity').attr('data-max'), 10) || 0);
                window.lbnRenderPdpStockState(stock, qty, chosenSize);
                if (typeof onDone === 'function') {
                    onDone(true, res);
                }
            },
            error: function (xhr) {
                let res = (xhr && xhr.responseJSON) ? xhr.responseJSON : null;
                if (res && res.available_stock !== undefined) {
                    window.lbnRenderPdpStockState(res.available_stock, qty, chosenSize);
                    if (typeof onDone === 'function') {
                        onDone(false, res);
                    }
                    return;
                }
                let fallbackStock = parseInt($('#lbn_pdpQuantity').attr('data-max'), 10) || 0;
                window.lbnRenderPdpStockState(fallbackStock, qty, chosenSize);
                if (typeof onDone === 'function') {
                    onDone(fallbackStock > 0 && qty <= fallbackStock, { available_stock: fallbackStock, du_hang: fallbackStock >= qty });
                }
            }
        });
    };

    // Chọn kích cỡ trên PDP -> Gọi AJAX kiểm tra tồn kho tương ứng từ lbn_san_pham
    window.lbnSelectPdpSize = function (sizeText, btnEl) {
        $('.lbn-size-option-btn').removeClass('active');
        $(btnEl).addClass('active');
        $('#lbn_selectedSizeText').text(sizeText);
        $('#lbn_selectedSizeInput').val(sizeText);
        window.lbnCheckPdpStockAjax('check');
    };

    // Tăng/giảm số lượng sản phẩm trên PDP -> Gọi AJAX kiểm tra tồn kho và chặn nếu vượt quá tồn kho thực tế
    window.lbnChangePdpQty = function (delta) {
        let qtyInput = $('#lbn_pdpQuantity');
        let maxStock = parseInt(qtyInput.attr('data-max'), 10);
        if (isNaN(maxStock)) maxStock = 20;
        let currentVal = parseInt(qtyInput.val(), 10) || 1;
        let nextVal = currentVal + delta;

        if (nextVal < 1) nextVal = 1;
        qtyInput.val(nextVal);

        if (maxStock <= 0) {
            window.lbnRenderPdpStockState(0, nextVal, $('#lbn_selectedSizeInput').val());
            return;
        }
        if (nextVal > maxStock) {
            window.lbnCheckPdpStockAjax('check');
            alert('Số lượng đặt mua (' + nextVal + ') vượt quá tồn kho thực tế (' + maxStock + ' sản phẩm)!');
            return;
        }
        window.lbnCheckPdpStockAjax('check');
    };

    $('#lbn_pdpQuantity').on('input change', function () {
        window.lbnCheckPdpStockAjax('check');
    });

    if ($('#lbn_pdpProductId').length) {
        window.lbnCheckPdpStockAjax('check');
    }

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

    // Nút Thêm vào giỏ & Mua ngay trên PDP (Chặn đặt hàng nếu vượt tồn kho thực tế qua fn_lbn_kiem_tra_ton_kho)
    window.lbnAddToCartPdp = function () {
        let prodName = $('#lbn_pdpProductTitle').text().trim();
        let chosenSize = $('#lbn_selectedSizeInput').val();
        let qty = parseInt($('#lbn_pdpQuantity').val(), 10) || 1;

        window.lbnCheckPdpStockAjax('add_to_cart', function (ok, res) {
            if (!ok || (res && res.du_hang === false)) {
                alert((res && res.message) ? res.message : 'Số lượng chọn vượt quá tồn kho thực tế!');
                return;
            }
            let badge = $('.cart-badge-custom').first();
            let nextCount = (res && res.cart_count) ? res.cart_count : ((parseInt(badge.text(), 10) || 0) + qty);
            badge.text(nextCount);
            alert('Đã kiểm tra tồn kho (fn_lbn_kiem_tra_ton_kho) & thêm ' + qty + ' x "' + prodName + '" (Phân loại: ' + chosenSize + ') vào giỏ hàng thành công!');
        });
    };

    window.lbnBuyNowPdp = function () {
        let prodName = $('#lbn_pdpProductTitle').text().trim();
        let chosenSize = $('#lbn_selectedSizeInput').val();
        let qty = parseInt($('#lbn_pdpQuantity').val(), 10) || 1;

        window.lbnCheckPdpStockAjax('buy_now', function (ok, res) {
            if (!ok || (res && res.du_hang === false)) {
                alert((res && res.message) ? res.message : 'Số lượng chọn vượt quá tồn kho thực tế!');
                return;
            }
            alert('Chuyển đến trang Thanh Toán với ' + qty + ' x "' + prodName + '" (' + chosenSize + ').');
            window.location.href = '/FashionKids/views/pages/checkout.php';
        });
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
    // 2. MH 04: Modal Tra cứu & Gợi ý Size Thước Dây (lnkt_suggest_size - Giai đoạn 4 AJAX)
    // ------------------------------------------------------------------------
    // Khởi tạo radio button ẩn đồng bộ trên trang Chi tiết sản phẩm (PDP) nếu chưa có
    if ($('#lbn_pdpSizeOptionsGroup').length && $('input[type="radio"][name="kich_co_chon"]').length === 0) {
        $('#lbn_pdpSizeOptionsGroup .lbn-size-option-btn').each(function () {
            let sVal = $(this).text().trim();
            let isChecked = $(this).hasClass('active');
            let $radio = $('<input>', {
                type: 'radio',
                name: 'kich_co_chon',
                value: sVal,
                class: 'd-none lnkt-pdp-size-radio',
                checked: isChecked
            });
            $('#lbn_pdpSizeOptionsGroup').append($radio);
        });
    }

    // Đồng bộ tự động kích cỡ gợi ý vào Radio Button & Ô chọn Size trên trang PDP / Đặt hàng
    window.lnktSyncSizeToPdpAndOrder = function (recommendedSize) {
        let cleanSize = String(recommendedSize || '').replace(/\s*Stored Function.*$/i, '').trim();
        if (!cleanSize) return;

        let prefixMatch = cleanSize.match(/^Size\s+\d+/i);
        let sizePrefix = prefixMatch ? prefixMatch[0].toLowerCase() : cleanSize.toLowerCase();

        // 1. Trigger chọn đúng radio button size tương ứng
        let $radios = $('input[type="radio"][name="kich_co_chon"], input[type="radio"][name="size"], input[name="kich_co_chon"]');
        if ($radios.length) {
            let matchedRadio = $radios.filter(function () {
                return $(this).val().trim().toLowerCase() === cleanSize.toLowerCase();
            });
            if (!matchedRadio.length && sizePrefix) {
                matchedRadio = $radios.filter(function () {
                    return $(this).val().trim().toLowerCase().indexOf(sizePrefix) === 0;
                });
            }
            if (matchedRadio.length) {
                $radios.prop('checked', false);
                matchedRadio.first().prop('checked', true).trigger('change');
            }
        }

        // 2. Đồng bộ nút chọn size hiển thị trên PDP (.lbn-size-option-btn) & input ẩn
        let matchedBtnText = cleanSize;
        if ($('.lbn-size-option-btn').length) {
            let $matchedBtn = $('.lbn-size-option-btn').filter(function () {
                return $(this).text().trim().toLowerCase() === cleanSize.toLowerCase();
            });
            if (!$matchedBtn.length && sizePrefix) {
                $matchedBtn = $('.lbn-size-option-btn').filter(function () {
                    return $(this).text().trim().toLowerCase().indexOf(sizePrefix) === 0;
                });
            }
            if ($matchedBtn.length) {
                $('.lbn-size-option-btn').removeClass('active');
                $matchedBtn.first().addClass('active');
                matchedBtnText = $matchedBtn.first().text().trim();
            }
        }

        if ($('#lbn_selectedSizeText').length) {
            $('#lbn_selectedSizeText').text(matchedBtnText);
        }
        if ($('#lbn_selectedSizeInput').length) {
            $('#lbn_selectedSizeInput').val(matchedBtnText);
        }
        if ($('#tgb_checkout_size').length) {
            $('#tgb_checkout_size').val(matchedBtnText);
        }
        if ($('#lbn_pdpProductId').length && typeof window.lbnCheckPdpStockAjax === 'function') {
            window.lbnCheckPdpStockAjax('check');
        }
    };

    // Cập nhật thanh thước dây ảo trực quan
    function lnktUpdateVirtualTape(activeType, numVal) {
        let badge = $('#lnkt_tapeValueBadge');
        let fill = $('#lnkt_tapeProgressFill');
        if (!badge.length || !fill.length) return;

        if (isNaN(numVal) || numVal <= 0) {
            badge.text('Số đo chưa hợp lệ').removeClass('badge-peach').addClass('badge-danger');
            fill.css('width', '0%');
            return;
        }

        badge.removeClass('badge-danger').addClass('badge-peach');
        if (activeType === 'giay_dep') {
            badge.text('Dài chân: ' + numVal + ' cm');
            let pct = Math.max(5, Math.min(100, ((numVal - 10) / (25 - 10)) * 100));
            fill.css('width', pct + '%');
        } else {
            badge.text('Chiều cao: ' + numVal + ' cm');
            let pct = Math.max(5, Math.min(100, ((numVal - 50) / (160 - 50)) * 100));
            fill.css('width', pct + '%');
        }
    }

    // Kiểm tra hợp lệ thông số đầu vào chặt chẽ (số âm, ký tự chữ, vượt ngưỡng 50-160cm)
    function lnktValidateSizeInput(activeType, rawHeightStr, rawFootStr) {
        let numericRegex = /^-?\d+(\.\d+)?$/;
        if (activeType === 'quan_ao') {
            if (!rawHeightStr) {
                return 'Vui lòng nhập chiều cao của bé (cm)!';
            }
            if (!numericRegex.test(rawHeightStr)) {
                return 'Chiều cao không hợp lệ! Vui lòng chỉ nhập chữ số (cm), không nhập chữ cái.';
            }
            let h = parseFloat(rawHeightStr);
            if (h <= 0) {
                return 'Số đo chiều cao không được là số âm hoặc bằng 0!';
            }
            if (h < 50 || h > 160) {
                return 'Chiều cao của bé vượt ngưỡng cho phép! Vui lòng nhập trong khoảng từ 50cm đến 160cm.';
            }
        } else {
            if (!rawFootStr) {
                return 'Vui lòng nhập chiều dài bàn chân của bé (cm)!';
            }
            if (!numericRegex.test(rawFootStr)) {
                return 'Chiều dài bàn chân không hợp lệ! Vui lòng chỉ nhập chữ số (cm), không nhập chữ cái.';
            }
            let f = parseFloat(rawFootStr);
            if (f <= 0) {
                return 'Số đo chiều dài bàn chân không được là số âm hoặc bằng 0!';
            }
            if (f < 10 || f > 25) {
                return 'Chiều dài bàn chân trẻ em phải nằm trong khoảng từ 10cm đến 25cm!';
            }
        }
        return null;
    }

    function lnktShowSizeError(errMsg, activeType) {
        let $activeInput = (activeType === 'giay_dep') ? $('#lnkt_inputFootCm') : $('#lnkt_inputHeightCm');
        $('#lnkt_inputHeightCm, #lnkt_inputFootCm').removeClass('is-valid is-invalid');
        $activeInput.addClass('is-invalid');

        $('#lnkt_size_result_box')
            .removeClass('d-none lnkt-alert-mint-pastel alert-mint')
            .addClass('alert-danger d-block')
            .html('<i class="fa-solid fa-triangle-exclamation mr-2"></i><strong>Lỗi dữ liệu đo:</strong> ' + errMsg);

        $('#lnkt_suggestedSizeBadge').text('Chưa hợp lệ');
        $('#lnkt_suggestedSubSpec').text(errMsg);
    }

    function lnktComputeLocalFallback(activeType, effectiveHeight, footCm) {
        if (activeType === 'quan_ao') {
            if (effectiveHeight <= 72) {
                return { size: 'Size 1 (0-1T)', spec: 'Khung chuẩn: Cao 50 - 72 cm • Ngực 44 - 47 cm • Eo 42 - 45 cm' };
            } else if (effectiveHeight < 90) {
                return { size: 'Size 1 (1-2T)', spec: 'Khung chuẩn: Cao 73 - 89 cm • Ngực 48 - 52 cm • Eo 46 - 50 cm' };
            } else if (effectiveHeight <= 100) {
                return { size: 'Size 2 (2-3T)', spec: 'Khung chuẩn: Cao 90 - 100 cm • Ngực 52 - 56 cm • Eo 50 - 53 cm' };
            } else if (effectiveHeight <= 110) {
                return { size: 'Size 3 (3-4T)', spec: 'Khung chuẩn: Cao 101 - 110 cm • Ngực 56 - 60 cm • Eo 53 - 55 cm' };
            } else if (effectiveHeight <= 122) {
                return { size: 'Size 4 (4-5T)', spec: 'Khung chuẩn: Cao 111 - 122 cm • Ngực 60 - 64 cm • Eo 55 - 57 cm' };
            }
            return { size: 'Size 5 (5-6T)', spec: 'Khung chuẩn: Cao 123 - 160 cm • Ngực 64 - 70 cm • Eo 58 - 62 cm' };
        } else {
            if (footCm <= 12.5) {
                return { size: 'Size 21 (12cm)', spec: 'Khung chuẩn bàn chân: 10.0 - 12.5 cm • Lòng dép 12.8 cm' };
            } else if (footCm <= 13.4) {
                return { size: 'Size 22 (13cm)', spec: 'Khung chuẩn bàn chân: 12.6 - 13.4 cm • Lòng dép 13.6 cm' };
            } else if (footCm <= 14.9) {
                return { size: 'Size 24 (14cm)', spec: 'Khung chuẩn bàn chân: 13.5 - 14.9 cm • Lòng dép 14.5 cm' };
            } else if (footCm <= 15.9) {
                return { size: 'Size 26 (15cm)', spec: 'Khung chuẩn bàn chân: 15.0 - 15.9 cm • Lòng dép 15.6 cm' };
            }
            return { size: 'Size 28 (16.5cm)', spec: 'Khung chuẩn bàn chân: 16.0 - 25.0 cm • Lòng dép 17.2 cm' };
        }
    }

    function lnktRenderSuccessResult(recommendedSize, noteText, activeType, heightCm, footCm, isChubby) {
        let sizeBadge = $('#lnkt_suggestedSizeBadge');
        let subSpec = $('#lnkt_suggestedSubSpec');
        let stretchNote = $('#lnkt_materialStretchNote');
        let summaryLabel = $('#lnkt_resultInputSummary');
        let effectiveHeight = isChubby ? (heightCm + 7) : heightCm;
        let localMeta = lnktComputeLocalFallback(activeType, effectiveHeight, footCm);
        let finalSize = recommendedSize || localMeta.size;

        $('#lnkt_inputHeightCm, #lnkt_inputFootCm').removeClass('is-invalid');
        if (activeType === 'giay_dep') {
            $('#lnkt_inputFootCm').addClass('is-valid');
            summaryLabel.text('Số đo: Dài bàn chân ' + footCm + ' cm');
        } else {
            $('#lnkt_inputHeightCm').addClass('is-valid');
            summaryLabel.text('Số đo: Chiều cao ' + heightCm + ' cm' + (isChubby ? ' (Trừ hao bé bụ bẫm)' : ''));
        }

        sizeBadge.text(finalSize);
        subSpec.text(localMeta.spec);
        stretchNote.html('<i class="fa-solid fa-leaf mr-1"></i><strong>Tư vấn độ co giãn chất liệu:</strong> ' + noteText);

        // Hiển thị hộp thông báo kết quả dạng Alert Pastel màu xanh mint (#E8F5E9)
        $('#lnkt_size_result_box')
            .removeClass('d-none alert-danger')
            .addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
            .html(
                '<div class="d-flex justify-content-between align-items-center">' +
                '<div><strong><i class="fa-solid fa-circle-check text-success mr-1"></i> Kích cỡ gợi ý chuẩn: <span class="text-peach">' + finalSize + '</span></strong>' +
                '<div class="small mt-1">' + noteText + '</div></div>' +
                '<span class="badge badge-pill badge-mint ml-2 px-2 py-1"><i class="fa-solid fa-bolt mr-1"></i>Đã đồng bộ PDP</span>' +
                '</div>'
            );

        // Tự động đồng bộ kích cỡ gợi ý xuống radio button & form đặt hàng trên PDP
        window.lnktSyncSizeToPdpAndOrder(finalSize);
    }

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

    // Gửi AJAX tới ProductController@ajaxSuggestSize và đồng bộ kết quả
    window.lnktRunSuggestSize = function () {
        let activeType = $('#lnkt_activeSizeType').val() || 'quan_ao';
        let rawHeightStr = String($('#lnkt_inputHeightCm').val() || '').trim();
        let rawFootStr = String($('#lnkt_inputFootCm').val() || '').trim();
        let isChubby = $('#lnkt_chubbyCheck').is(':checked');

        let catId = (activeType === 'quan_ao')
            ? (parseInt($('#lnkt_clothingCategory').val(), 10) || 1)
            : (parseInt($('#lnkt_shoesCategory').val(), 10) || 4);
        $('#lnkt_modal_cat_id').val(catId);

        let parsedVal = (activeType === 'giay_dep') ? parseFloat(rawFootStr) : parseFloat(rawHeightStr);
        lnktUpdateVirtualTape(activeType, parsedVal);

        // Kiểm tra ngoại lệ phía Client trước
        let clientErr = lnktValidateSizeInput(activeType, rawHeightStr, rawFootStr);
        let heightNum = parseFloat(rawHeightStr);
        let footNum = parseFloat(rawFootStr);
        let effectiveHeight = (!isNaN(heightNum) && heightNum >= 50 && heightNum <= 160 && isChubby) ? Math.min(160, heightNum + 7) : rawHeightStr;

        // Hiển thị hiệu ứng UX Spinner Loading
        $('#lnkt_btnSpinner').removeClass('d-none');
        $('#lnkt_btnMagicIcon').addClass('d-none');
        $('#lnkt_btnCalculateText').text('Đang tính kích cỡ chuẩn...');
        $('#lnkt_btnCalculateSize').prop('disabled', true);

        $.ajax({
            url: '/FashionKids/index.php?c=product&a=ajaxSuggestSize',
            type: 'POST',
            data: {
                ma_danh_muc: catId,
                chieu_cao:   (activeType === 'quan_ao') ? effectiveHeight : 88,
                dai_chan:    (activeType === 'giay_dep') ? rawFootStr : (isNaN(footNum) ? 13.8 : footNum)
            },
            dataType: 'json',
            timeout: 2000,
            complete: function () {
                $('#lnkt_btnSpinner').addClass('d-none');
                $('#lnkt_btnMagicIcon').removeClass('d-none');
                $('#lnkt_btnCalculateText').text('Gợi Ý Kích Cỡ Ngay');
                $('#lnkt_btnCalculateSize').prop('disabled', false);
            },
            success: function (res) {
                if (clientErr) {
                    lnktShowSizeError(clientErr, activeType);
                    return;
                }
                if (res && res.status === 'success' && res.recommended_size) {
                    let selectedOpt = (activeType === 'quan_ao')
                        ? $('#lnkt_clothingCategory option:selected')
                        : $('#lnkt_shoesCategory option:selected');
                    let noteText = res.note || selectedOpt.attr('data-stretch') || '';
                    lnktRenderSuccessResult(res.recommended_size, noteText, activeType, heightNum, footNum, isChubby);
                } else {
                    let msg = (res && (res.message || res.note)) ? (res.message || res.note) : 'Dữ liệu số đo không hợp lệ!';
                    lnktShowSizeError(msg, activeType);
                }
            },
            error: function (xhr) {
                if (clientErr) {
                    lnktShowSizeError(clientErr, activeType);
                    return;
                }
                if (xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.note)) {
                    lnktShowSizeError(xhr.responseJSON.message || xhr.responseJSON.note, activeType);
                    return;
                }
                // Fallback nếu chạy tĩnh chưa bật server
                let selectedOpt = (activeType === 'quan_ao')
                    ? $('#lnkt_clothingCategory option:selected')
                    : $('#lnkt_shoesCategory option:selected');
                let fallbackMeta = lnktComputeLocalFallback(activeType, (!isNaN(heightNum) && isChubby) ? (heightNum + 7) : heightNum, footNum);
                let noteText = selectedOpt.attr('data-stretch') || 'Kích cỡ tính toán tự động dựa trên quy chuẩn bảng size FashionKids.';
                lnktRenderSuccessResult(fallbackMeta.size, noteText, activeType, heightNum, footNum, isChubby);
            }
        });
    };

    // Bắt sự kiện submit của form thước dây đo size bằng jQuery AJAX (ưu tiên xử lý chuẩn Giai đoạn 4)
    setTimeout(function () {
        $('#lnkt_form_suggest_size').off('submit').on('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.lnktRunSuggestSize();
        });

        $('#lnkt_inputHeightCm, #lnkt_inputFootCm, #lnkt_clothingCategory, #lnkt_shoesCategory, #lnkt_chubbyCheck')
            .off('input change')
            .on('input change', function () {
                clearTimeout(window.lnktAjaxSuggestDebounce);
                window.lnktAjaxSuggestDebounce = setTimeout(function () {
                    window.lnktRunSuggestSize();
                }, 250);
            });
    }, 0);

    // Áp dụng size vừa gợi ý từ Modal MH 04 sang trang PDP (MH 03) nếu có
    window.lnktApplySuggestedSizeToPdp = function () {
        let chosenSize = $('#lnkt_suggestedSizeBadge').text().trim();
        if (!chosenSize || chosenSize === 'Chưa hợp lệ') {
            alert('Vui lòng nhập số đo hợp lệ (Chiều cao từ 50cm đến 160cm) để nhận kích cỡ gợi ý trước khi áp dụng!');
            return;
        }
        window.lnktSyncSizeToPdpAndOrder(chosenSize);
        $('#modalSuggestSize').modal('hide');
        alert('Đã áp dụng kích cỡ gợi ý "' + chosenSize + '" từ thước dây thông minh (fn_lnkt_goi_y_kich_co_chuan)!');
    };

    // ------------------------------------------------------------------------
    // 3. MH 05: Giỏ Hàng, Tỉnh/Thành Provinces Open API & Đặt Hàng (checkout.php)
    // ------------------------------------------------------------------------
    const lnktProvinceApiUrl = 'https://provinces.open-api.vn/api/?depth=2';
    const lnktProvinceSelect = $('#tgb_select_province');
    const lnktDistrictSelect = $('#tgb_select_district');
    const lnktProvinceStatus = $('#lnkt_apiStatusText');
    let lnktProvinceData = [];

    function lnktSetProvinceStatus(message, iconClass) {
        if (lnktProvinceStatus.length) {
            lnktProvinceStatus
                .empty()
                .append($('<i>').addClass('fa-solid ' + iconClass + ' mr-1'))
                .append(document.createTextNode(message));
        }
    }

    window.lnktOnProvinceChange = function (provinceKey) {
        if (!lnktDistrictSelect.length) return;

        const selectedProvince = lnktProvinceData.find(function (province) {
            return String(province.code) === String(provinceKey) || province.name === provinceKey;
        });

        if (!provinceKey || !selectedProvince) {
            lnktDistrictSelect
                .empty()
                .append($('<option>', { value: '', text: '-- Chọn Quận / Huyện --' }))
                .prop('disabled', true);
            return;
        }

        const selectedKey = String(provinceKey);
        lnktDistrictSelect
            .empty()
            .append($('<option>', { value: '', text: 'Đang tải Quận/Huyện...' }))
            .prop('disabled', true);

        window.requestAnimationFrame(function () {
            if (String(lnktProvinceSelect.val()) !== selectedKey) return;

            const districts = Array.isArray(selectedProvince.districts) ? selectedProvince.districts : [];
            lnktDistrictSelect
                .empty()
                .append($('<option>', { value: '', text: '-- Chọn Quận / Huyện --' }));

            districts.forEach(function (district) {
                lnktDistrictSelect.append($('<option>', {
                    value: district.name,
                    text: district.name
                }));
            });

            lnktDistrictSelect.prop('disabled', districts.length === 0);
        });
    };

    // Chỉ tải API một lần; đổi tỉnh sử dụng dữ liệu districts đã nhận được.
    window.lnktSyncProvincesOpenApi = function () {
        if (!lnktProvinceSelect.length) return;

        lnktProvinceData = [];
        lnktProvinceSelect
            .prop('disabled', true)
            .empty()
            .append($('<option>', { value: '', text: 'Đang tải Tỉnh / Thành phố...' }));
        lnktDistrictSelect
            .prop('disabled', true)
            .empty()
            .append($('<option>', { value: '', text: '-- Chọn Quận / Huyện --' }));
        lnktSetProvinceStatus('Đang tải dữ liệu từ Provinces Open API...', 'fa-spinner fa-spin text-info');

        $.getJSON(lnktProvinceApiUrl)
            .done(function (provinces) {
                if (!Array.isArray(provinces) || provinces.length === 0) {
                    lnktSetProvinceStatus('API không trả về danh sách Tỉnh / Thành phố hợp lệ.', 'fa-circle-exclamation text-danger');
                    return;
                }

                lnktProvinceData = provinces;
                lnktProvinceSelect
                    .empty()
                    .append($('<option>', { value: '', text: '-- Chọn Tỉnh / Thành phố --' }));

                provinces.forEach(function (province) {
                    lnktProvinceSelect.append($('<option>', {
                        value: province.name,
                        text: province.name
                    }));
                });

                lnktProvinceSelect.prop('disabled', false);
                lnktSetProvinceStatus('Đã tải ' + provinces.length + ' Tỉnh / Thành phố từ Provinces Open API.', 'fa-circle-check text-success');
            })
            .fail(function () {
                lnktProvinceData = [];
                lnktProvinceSelect
                    .empty()
                    .append($('<option>', { value: '', text: '-- Không tải được danh sách Tỉnh / Thành phố --' }));
                lnktSetProvinceStatus('Không thể tải dữ liệu từ Provinces Open API. Vui lòng thử lại.', 'fa-circle-exclamation text-danger');
            })
            .always(function () {
                if (lnktProvinceData.length > 0) {
                    lnktProvinceSelect.prop('disabled', false);
                }
            });
    };

    // Ghép chuỗi địa chỉ đầy đủ vào trường ẩn #tgb_full_address để nộp cùng form sp_tgb_tao_don_hang_va_chi_tiet
    window.tgbComposeFullAddress = function () {
        let addr = ($('#lnkt_shippingAddress').val() || '').trim();
        let dist = ($('#tgb_select_district').val() || '').trim();
        let prov = ($('#tgb_select_province').val() || '').trim();
        let parts = [];
        if (addr) parts.push(addr);
        if (dist) parts.push(dist);
        if (prov) parts.push(prov);
        let fullStr = parts.join(', ');
        if ($('#tgb_full_address').length) {
            $('#tgb_full_address').val(fullStr);
        }
        return fullStr;
    };

    if (lnktProvinceSelect.length) {
        lnktProvinceSelect.on('change', function () {
            window.lnktOnProvinceChange($(this).val());
            window.tgbComposeFullAddress();
        });
        lnktDistrictSelect.on('change', function () {
            window.tgbComposeFullAddress();
        });
        $('#lnkt_shippingAddress').on('input change', function () {
            window.tgbComposeFullAddress();
        });
        window.lnktSyncProvincesOpenApi();
        window.tgbComposeFullAddress();
    }

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
        if ($('#tgb_checkout_total').length) { $('#tgb_checkout_total').val(totalAmount); }
        $('#lnkt_summaryRewardPoints').text('+' + rewardPts + ' điểm VIP');
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
