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

    // 7. Hạng thẻ VIP được đồng bộ tự động từ CSDL (chỉ đọc, không cho phép chuyển đổi thủ công)
    window.hgbSwitchVipCard = function () {
        return false;
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
        let reviewsCount = parseInt(prod.reviews, 10) || 0;
        let ratingStr = reviewsCount > 0 ? Number(prod.rating || 0).toFixed(1) : '0.0';
        let buyerCount = parseInt(prod.buyer_count, 10) || 0;
        let ratingHtml = (reviewsCount > 0)
            ? ('<span class="small text-warning font-weight-bold text-nowrap flex-shrink-0">' +
                '<i class="fa-solid fa-star"></i> ' + ratingStr + ' ' +
                '<span class="text-muted-custom font-weight-normal">(' + reviewsCount + ')</span>' +
              '</span>')
            : '<span class="badge badge-pill fk-no-review-badge text-nowrap flex-shrink-0">Chưa có đánh giá</span>';

        return (
            '<div class="col-12 col-sm-6 col-xl-4 mb-4 lbn-catalog-col" ' +
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
                    '<div class="card-body d-flex flex-column p-3 min-w-0">' +
                        '<div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-1">' +
                            '<span class="small font-weight-bold text-muted-custom">' + safeCatName + '</span>' +
                            ratingHtml +
                        '</div>' +
                        '<h6 class="font-weight-bold text-slate mb-2">' +
                            '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="text-slate text-decoration-none">' +
                                safeName +
                            '</a>' +
                        '</h6>' +
                        '<div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row small text-muted-custom mb-2">' +
                            '<span class="text-nowrap"><i class="fa-solid fa-barcode mr-1"></i>' + safeSku + '</span>' +
                            '<span class="text-nowrap"><i class="fa-solid fa-ruler-combined mr-1"></i>' + safeSizeRange + '</span>' +
                        '</div>' +
                        '<div class="mb-3">' +
                            '<span class="fk-buyer-count-pill">' +
                                '<i class="fa-solid fa-bag-shopping text-peach mr-1"></i>' + buyerCount + ' người mua' +
                            '</span>' +
                        '</div>' +
                        '<div class="mt-auto pt-3 border-top d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">' +
                            '<div class="min-w-0">' +
                                '<div class="h5 font-weight-bold text-peach mb-0 text-nowrap">' + priceFormatted + '</div>' +
                                stockHtml +
                            '</div>' +
                            '<div class="d-flex align-items-center flex-shrink-0">' +
                                '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" ' +
                                   'class="btn btn-light btn-sm rounded-pill border mr-1 px-2 text-slate flex-shrink-0" title="Xem chi tiết sản phẩm">' +
                                    '<i class="fa-regular fa-eye"></i>' +
                                '</a>' +
                                '<button type="button" ' +
                                        'onclick="lbnQuickAddToCart(\'' + safeName.replace(/'/g, "\\'") + '\', ' + prod.price + ', ' + prod.id + ')" ' +
                                        'class="btn btn-pastel-primary btn-sm px-3 py-1 text-nowrap flex-shrink-0" ' + (stockQty <= 0 ? 'disabled' : '') + '>' +
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
    window.lbnQuickAddToCart = function (productName, price, productId, defaultSize) {
        let pid = parseInt(productId, 10) || 101;
        let chosenSize = defaultSize || 'Size 2 (2-3T)';
        $.ajax({
            url: '/FashionKids/index.php?c=product&a=ajaxCheckStock',
            type: 'POST',
            dataType: 'json',
            data: {
                ma_san_pham: pid,
                product_id:  pid,
                so_luong:    1,
                quantity:    1,
                size:        chosenSize,
                action_type: 'quick_add'
            },
            timeout: 3000,
            success: function (res) {
                if (res && res.status === 'success') {
                    let $badges = $('.cart-badge-custom');
                    let nextCount = (res.cart_count !== undefined) ? res.cart_count : ((parseInt($badges.first().text(), 10) || 0) + 1);
                    $badges.text(nextCount);
                    alert('Đã thêm "' + productName + '" (' + lbnFormatVnd(price) + ') vào giỏ hàng!');
                } else {
                    alert((res && res.message) ? res.message : 'Sản phẩm hiện đã hết hàng!');
                }
            },
            error: function (xhr) {
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                    return;
                }
                alert('Không thể thêm sản phẩm vào giỏ hàng lúc này. Vui lòng thử lại!');
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
                .html('<i class="fa-solid fa-circle-exclamation mr-1"></i><strong>Tạm hết hàng:</strong> Sản phẩm kích cỡ <strong>' + lbnEscapeHtml(sizeLabel || '') + '</strong> hiện đã hết hàng tại cửa hàng.');
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
                .html('<i class="fa-solid fa-circle-check text-success mr-1"></i><strong>Tồn kho khả dụng:</strong> Phân loại <strong>' + lbnEscapeHtml(sizeLabel || '') + '</strong> còn <strong>' + stock + '</strong> sản phẩm tại kho.');
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

    // Chiết khấu VIP được tính tự động theo tài khoản từ CSDL (không cho đổi thủ công)
    window.lbnSwitchVipTierDiscount = function () {
        return false;
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
            alert('Đã kiểm tra tồn kho & thêm ' + qty + ' x "' + prodName + '" (Phân loại: ' + chosenSize + ') vào giỏ hàng thành công!');
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

    // Cập nhật nhanh tồn kho (Stored Procedure sp_lbn_nhap_them_kho_hang) & Cảnh báo đỏ khi tồn kho <= 5
    window.lbnQuickUpdateStock = function (productId, skuCode) {
        let inputEl = $('#lbn_stockInput_' + productId);
        let newStock = parseInt(inputEl.val(), 10);

        if (isNaN(newStock) || newStock < 0) {
            alert('Lỗi ngoại lệ: Số lượng tồn kho không được nhỏ hơn 0 (phải >= 0)!');
            return;
        }

        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxRestockInventory',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: productId,
                new_stock:  newStock,
                mode:       'set'
            },
            success: function (res) {
                let finalStock = (res && res.new_stock !== undefined) ? parseInt(res.new_stock, 10) : newStock;
                inputEl.val(finalStock);

                let $row = $('#lbn_invRow_' + productId);
                $row.attr('data-stock', finalStock);
                let statusCell = $('#lbn_stockBadgeCell_' + productId);

                if (finalStock <= 5) {
                    statusCell.html(
                        '<span class="badge badge-danger lbn-badge-low-stock px-2 py-1">' +
                        '<i class="fa-solid fa-triangle-exclamation mr-1"></i>Sắp hết hàng (' + finalStock + ')' +
                        '</span>'
                    );
                } else {
                    statusCell.html(
                        '<span class="badge badge-mint px-2 py-1 rounded-pill">' +
                        '<i class="fa-solid fa-circle-check mr-1"></i>Còn hàng (' + finalStock + ')' +
                        '</span>'
                    );
                }

                let lowStockTotal = 0;
                $('.lbn-inv-row').each(function () {
                    let st = parseInt($(this).attr('data-stock'), 10) || 0;
                    if (st <= 5) lowStockTotal++;
                });
                $('#lbn_lowStockCounter').text(lowStockTotal);

                alert((res && res.message) ? res.message : ('Đã cập nhật tồn kho cho mã sản phẩm ' + skuCode + ': ' + finalStock + ' sản phẩm.'));
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể cập nhật tồn kho lúc này!';
                alert(errMsg);
            }
        });
    };

    // Xử lý Form Nhập thêm kho nhanh dành riêng cho Nhân viên (MH 13)
    window.lbnSubmitStaffQuickRestock = function (e) {
        if (e && e.preventDefault) e.preventDefault();
        let productId = parseInt($('#lbn_staffRestockProductId').val(), 10) || 0;
        let skuCode   = $('#lbn_staffRestockProductId option:selected').attr('data-sku') || ('FK-LBN-' + productId);
        let newQty    = parseInt($('#lbn_staffRestockQty').val(), 10);

        if (isNaN(newQty) || newQty < 0) {
            alert('Số lượng tồn kho mới không được nhỏ hơn 0!');
            return false;
        }
        if ($('#lbn_stockInput_' + productId).length) {
            $('#lbn_stockInput_' + productId).val(newQty);
        }
        window.lbnQuickUpdateStock(productId, skuCode);
        return false;
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
        $('#lbn_formModeTitle').html('<i class="fa-solid fa-plus-circle text-peach mr-1"></i> Thêm Sản Phẩm Mới');
        $('#lbn_btnSubmitProduct').html('<i class="fa-solid fa-cloud-arrow-up mr-1"></i> Thêm Sản Phẩm Vào Kho');
        $('#lbn_btnResetForm').addClass('d-none');
        $('#lbn_uploadFileName').text('Kéo thả hoặc nhấn để chọn ảnh sản phẩm (JPG, PNG, WEBP)');
        $('#lbn_uploadPreviewBadge').addClass('d-none');
        window.lbnToggleSizeTypeGuide('quan_ao');
    };

    // MH 13 (Admin Only): Tích hợp AJAX gọi ProductController / AdminController (sp_lbn_them_san_pham_moi)
    // Bắt lỗi ngoại lệ chặt chẽ nếu giá bán <= 0 hoặc tồn kho < 0
    $('#lbn_formAdminProduct').on('submit', function (e) {
        e.preventDefault();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();

        let prodName  = ($('#lbn_formProductName').val() || '').trim();
        let rawPrice  = parseFloat($('#lbn_formProductPrice').val());
        let rawStock  = parseInt($('#lbn_formProductStock').val(), 10);
        let formEl    = this;
        let $btn      = $('#lbn_btnSubmitProduct');

        if (isNaN(rawPrice) || rawPrice <= 0) {
            alert('Lỗi ngoại lệ: Đơn giá bán sản phẩm phải lớn hơn 0 VNĐ (gia_ban > 0)!');
            $('#lbn_formProductPrice').focus();
            return false;
        }
        if (isNaN(rawStock) || rawStock < 0) {
            alert('Lỗi ngoại lệ: Số lượng tồn kho không được nhỏ hơn 0 (so_luong_ton_kho >= 0)!');
            $('#lbn_formProductStock').focus();
            return false;
        }

        let formData = new FormData(formEl);
        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu kho...');

        $.ajax({
            url: '/FashionKids/index.php?c=product&a=ajaxSaveProduct',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                if (res && res.status === 'success') {
                    alert(res.message || ('Đã lưu sản phẩm "' + prodName + '" thành công!'));
                    window.lbnResetProductForm();
                } else {
                    alert((res && res.message) ? res.message : 'Không thể lưu sản phẩm!');
                }
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Lỗi ngoại lệ khi lưu sản phẩm vào kho!';
                alert(errMsg);
            }
        });
    });
});



/**
 * ============================================================================
 * PHÂN HỆ ĐẢM NHIỆM: LÊ NGUYỄN KHÁNH TRÌNH (lnkt_)
 * Homepage Carousel (MH 01), Size Chart Modal, Checkout (MH 05),
 * Order Tracking, Staff Orders (MH 10), Admin Size Charts (MH 14),
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
                '<span class="badge badge-pill badge-mint ml-2 px-2 py-1"><i class="fa-solid fa-bolt mr-1"></i>Đã áp dụng</span>' +
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
        alert('Đã áp dụng kích cỡ gợi ý "' + chosenSize + '" vào sản phẩm!');
    };

    // ------------------------------------------------------------------------
    // 3. MH 05: Giỏ Hàng, Tỉnh/Huyện/Xã Provinces Open API (depth=3), GHN & Voucher (checkout.php)
    // ------------------------------------------------------------------------
    const lnktProvinceApiUrl = 'https://provinces.open-api.vn/api/?depth=3';
    const lnktProvinceSelect = $('#tgb_select_province');
    const lnktDistrictSelect = $('#tgb_select_district');
    const lnktWardSelect = $('#tgb_select_ward');
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

        if (lnktWardSelect.length) {
            lnktWardSelect
                .empty()
                .append($('<option>', { value: '', text: '-- Chọn Phường / Xã --' }))
                .prop('disabled', true);
        }

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

    window.lnktOnDistrictChange = function (districtKey) {
        if (!lnktWardSelect.length) return;
        const provKey = String(lnktProvinceSelect.val() || '');
        const selectedProvince = lnktProvinceData.find(function (p) {
            return String(p.code) === provKey || p.name === provKey;
        });
        const districts = selectedProvince && Array.isArray(selectedProvince.districts) ? selectedProvince.districts : [];
        const selectedDistrict = districts.find(function (d) {
            return String(d.code) === String(districtKey) || d.name === districtKey;
        });

        if (!districtKey || !selectedDistrict) {
            lnktWardSelect
                .empty()
                .append($('<option>', { value: '', text: '-- Chọn Phường / Xã --' }))
                .prop('disabled', true);
            return;
        }

        const wards = Array.isArray(selectedDistrict.wards) ? selectedDistrict.wards : [];
        lnktWardSelect
            .empty()
            .append($('<option>', { value: '', text: '-- Chọn Phường / Xã --' }));

        if (wards.length > 0) {
            wards.forEach(function (w) {
                lnktWardSelect.append($('<option>', { value: w.name, text: w.name }));
            });
        } else {
            lnktWardSelect.append($('<option>', { value: 'Trung tâm ' + selectedDistrict.name, text: 'Trung tâm ' + selectedDistrict.name }));
        }
        lnktWardSelect.prop('disabled', false);
    };

    // Chỉ tải API một lần; đổi tỉnh/huyện sử dụng dữ liệu 3 cấp đã nhận được.
    window.lnktSyncProvincesOpenApi = function () {
        if (!lnktProvinceSelect.length) return;

        lnktProvinceData = [];
        lnktProvinceSelect
            .prop('disabled', true)
            .empty()
            .append($('<option>', { value: '', text: 'Đang tải Tỉnh / TP...' }));
        lnktDistrictSelect
            .prop('disabled', true)
            .empty()
            .append($('<option>', { value: '', text: '-- Chọn Quận / Huyện --' }));
        if (lnktWardSelect.length) {
            lnktWardSelect
                .prop('disabled', true)
                .empty()
                .append($('<option>', { value: '', text: '-- Chọn Phường / Xã --' }));
        }
        lnktSetProvinceStatus('Đang tải địa giới hành chính 3 cấp (Tỉnh/Huyện/Xã)...', 'fa-spinner fa-spin text-info');

        $.getJSON(lnktProvinceApiUrl)
            .done(function (provinces) {
                if (!Array.isArray(provinces) || provinces.length === 0) {
                    lnktSetProvinceStatus('Chưa tải được danh sách Tỉnh / Thành phố.', 'fa-circle-exclamation text-danger');
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
                lnktSetProvinceStatus('Đã tải sẵn sàng ' + provinces.length + ' Tỉnh / Thành phố (đầy đủ 3 cấp Phường/Xã).', 'fa-circle-check text-success');
            })
            .fail(function () {
                lnktProvinceData = [
                    { name: 'Thành phố Hồ Chí Minh', districts: [
                        { name: 'Quận 1', wards: [{ name: 'Phường Bến Nghé' }, { name: 'Phường Bến Thành' }, { name: 'Phường Đa Kao' }] },
                        { name: 'Quận 3', wards: [{ name: 'Phường Võ Thị Sáu' }, { name: 'Phường 1' }, { name: 'Phường 5' }] },
                        { name: 'Quận 7', wards: [{ name: 'Phường Tân Phong' }, { name: 'Phường Tân Phú' }, { name: 'Phường Phú Mỹ' }] },
                        { name: 'Quận Bình Thạnh', wards: [{ name: 'Phường 1' }, { name: 'Phường 14' }, { name: 'Phường 25' }] },
                        { name: 'Quận Phú Nhuận', wards: [{ name: 'Phường 1' }, { name: 'Phường 7' }, { name: 'Phường 15' }] },
                        { name: 'Thành phố Thủ Đức', wards: [{ name: 'Phường Thảo Điền' }, { name: 'Phường Hiệp Bình Chánh' }, { name: 'Phường Linh Trung' }] }
                    ] },
                    { name: 'Thành phố Hà Nội', districts: [
                        { name: 'Quận Hoàn Kiếm', wards: [{ name: 'Phường Hàng Bạc' }, { name: 'Phường Tràng Tiền' }] },
                        { name: 'Quận Ba Đình', wards: [{ name: 'Phường Điện Biên' }, { name: 'Phường Đội Cấn' }] },
                        { name: 'Quận Cầu Giấy', wards: [{ name: 'Phường Dịch Vọng' }, { name: 'Phường Trung Hòa' }] },
                        { name: 'Quận Đống Đa', wards: [{ name: 'Phường Láng Hạ' }, { name: 'Phường Ô Chợ Dừa' }] }
                    ] },
                    { name: 'Thành phố Đà Nẵng', districts: [{ name: 'Quận Hải Châu', wards: [{ name: 'Phường Thạch Thang' }, { name: 'Phường Hải Châu 1' }] }, { name: 'Quận Sơn Trà', wards: [{ name: 'Phường An Hải Bắc' }] }] },
                    { name: 'Thành phố Cần Thơ', districts: [{ name: 'Quận Ninh Kiều', wards: [{ name: 'Phường Tân An' }, { name: 'Phường Xuân Khánh' }] }] },
                    { name: 'Tỉnh Bình Dương', districts: [{ name: 'Thành phố Thủ Dầu Một', wards: [{ name: 'Phường Phú Cường' }, { name: 'Phường Hiệp Thành' }] }, { name: 'Thành phố Thuận An', wards: [{ name: 'Phường Lái Thiêu' }] }] },
                    { name: 'Tỉnh Đồng Nai', districts: [{ name: 'Thành phố Biên Hòa', wards: [{ name: 'Phường Quyết Thắng' }, { name: 'Phường Tân Hiệp' }] }] }
                ];
                lnktProvinceSelect
                    .empty()
                    .append($('<option>', { value: '', text: '-- Chọn Tỉnh / Thành phố --' }));
                lnktProvinceData.forEach(function (province) {
                    lnktProvinceSelect.append($('<option>', {
                        value: province.name,
                        text: province.name
                    }));
                });
                lnktProvinceSelect.prop('disabled', false);
                lnktSetProvinceStatus('Đang dùng bộ dữ liệu địa giới 3 cấp dự phòng (' + lnktProvinceData.length + ' Tỉnh/TP).', 'fa-circle-check text-success');
            })
            .always(function () {
                if (lnktProvinceData.length > 0) {
                    lnktProvinceSelect.prop('disabled', false);
                }
            });
    };

    // Ghép chuỗi địa chỉ đầy đủ vào trường ẩn #tgb_full_address
    window.tgbComposeFullAddress = function () {
        let addr = ($('#lnkt_shippingAddress').val() || '').trim();
        let ward = ($('#tgb_select_ward').val() || '').trim();
        let dist = ($('#tgb_select_district').val() || '').trim();
        let prov = ($('#tgb_select_province').val() || '').trim();
        let parts = [];
        if (addr) parts.push(addr);
        if (ward) parts.push(ward);
        if (dist) parts.push(dist);
        if (prov) parts.push(prov);
        let fullStr = parts.join(', ');
        if ($('#tgb_full_address').length) {
            $('#tgb_full_address').val(fullStr);
        }
        return fullStr;
    };

    window.lnktRefreshCheckoutShipping = function () {
        if (!$('#lnkt_checkout_section').length) return;
        let province = ($('#tgb_select_province').val() || '').trim();
        let district = ($('#tgb_select_district').val() || '').trim();
        let ward = ($('#tgb_select_ward').val() || '').trim();
        let serviceType = ($('input[name="dich_vu_van_chuyen"]:checked').val() || 'standard').trim();

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxCalculateShipping',
            type: 'POST',
            dataType: 'json',
            data: {
                province: province,
                district: district,
                ward: ward,
                service_type: serviceType
            },
            timeout: 8000,
            success: function (res) {
                if (res && res.cart_summary) {
                    window.lnktApplyServerCartSummary(res.cart_summary);
                }
            }
        });
    };

    window.lnktApplyVoucherCode = function () {
        let code = ($('#lnkt_voucherCodeInput').val() || '').trim().toUpperCase();
        let $alert = $('#lnkt_voucherAlert');
        let $btn = $('#lnkt_btnApplyVoucher');
        if (!code) {
            $alert.removeClass('d-none text-success').addClass('text-danger font-weight-bold').text('Vui lòng nhập mã giảm giá!');
            $('#lnkt_voucherCodeInput').focus();
            return;
        }
        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxApplyVoucher',
            type: 'POST',
            dataType: 'json',
            data: {
                voucher_code: code,
                province: ($('#tgb_select_province').val() || '').trim(),
                district: ($('#tgb_select_district').val() || '').trim(),
                ward: ($('#tgb_select_ward').val() || '').trim(),
                service_type: ($('input[name="dich_vu_van_chuyen"]:checked').val() || 'standard').trim()
            },
            timeout: 6000,
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                $alert.removeClass('d-none text-danger').addClass('text-success font-weight-bold')
                    .html('<i class="fa-solid fa-circle-check mr-1"></i>' + lnktEscapeHtml((res && res.message) ? res.message : 'Áp dụng mã thành công!'));
                if (res && res.cart_summary) {
                    window.lnktApplyServerCartSummary(res.cart_summary);
                }
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Mã giảm giá không hợp lệ hoặc chưa đủ điều kiện!';
                $alert.removeClass('d-none text-success').addClass('text-danger font-weight-bold')
                    .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(errMsg));
                if (xhr.responseJSON && xhr.responseJSON.cart_summary) {
                    window.lnktApplyServerCartSummary(xhr.responseJSON.cart_summary);
                }
            }
        });
    };

    window.lnktRemoveVoucherCode = function () {
        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxRemoveVoucher',
            type: 'POST',
            dataType: 'json',
            timeout: 5000,
            success: function (res) {
                $('#lnkt_voucherCodeInput').val('');
                $('#lnkt_voucherAlert').addClass('d-none').text('');
                if (res && res.cart_summary) {
                    window.lnktApplyServerCartSummary(res.cart_summary);
                }
            }
        });
    };

    if (lnktProvinceSelect.length) {
        lnktProvinceSelect.on('change', function () {
            window.lnktOnProvinceChange($(this).val());
            window.tgbComposeFullAddress();
            window.lnktRefreshCheckoutShipping();
        });
        lnktDistrictSelect.on('change', function () {
            window.lnktOnDistrictChange($(this).val());
            window.tgbComposeFullAddress();
            window.lnktRefreshCheckoutShipping();
        });
        lnktWardSelect.on('change', function () {
            window.tgbComposeFullAddress();
            window.lnktRefreshCheckoutShipping();
        });
        $(document).on('change', 'input[name="dich_vu_van_chuyen"]', function () {
            window.lnktRefreshCheckoutShipping();
        });
        $('#lnkt_shippingAddress').on('input change', function () {
            window.tgbComposeFullAddress();
        });
        window.lnktSyncProvincesOpenApi();
        window.tgbComposeFullAddress();
    }

    function lnktEscapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function lnktFormatMysqlDt(dtStr) {
        if (!dtStr) return '--:--';
        let parsed = new Date(String(dtStr).replace(' ', 'T'));
        if (isNaN(parsed.getTime())) return String(dtStr);
        let dd = String(parsed.getDate()).padStart(2, '0');
        let mm = String(parsed.getMonth() + 1).padStart(2, '0');
        let yyyy = parsed.getFullYear();
        let hh = String(parsed.getHours()).padStart(2, '0');
        let mi = String(parsed.getMinutes()).padStart(2, '0');
        return dd + '/' + mm + '/' + yyyy + ' ' + hh + ':' + mi;
    }

    function lnktParseMysqlTs(dtStr) {
        if (!dtStr) return Math.floor(Date.now() / 1000);
        let parsed = new Date(String(dtStr).replace(' ', 'T'));
        if (isNaN(parsed.getTime())) return Math.floor(Date.now() / 1000);
        return Math.floor(parsed.getTime() / 1000);
    }

    window.lnktShowCheckoutAlert = function (type, msg) {
        let $box = $('#lnkt_checkoutAlert');
        if (!$box.length) return;
        $box.removeClass('d-none alert-danger alert-mint lnkt-alert-mint-pastel text-slate');
        if (type === 'success') {
            $box.addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
                .html('<i class="fa-solid fa-circle-check text-success mr-1"></i>' + lnktEscapeHtml(msg));
        } else {
            $box.addClass('alert-danger d-block')
                .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(msg));
        }
    };

    window.lnktApplyServerCartSummary = function (summary) {
        if (!summary) return;
        let items = Array.isArray(summary.items) ? summary.items : [];
        let byKey = {};
        items.forEach(function (it) {
            byKey[String(it.cart_key)] = it;
        });

        $('.lnkt-cart-row').each(function () {
            let $row = $(this);
            let rowKey = String($row.attr('data-cart-key') || '');
            let itemId = $row.attr('data-item-id');
            if (!rowKey || !byKey[rowKey]) {
                $row.remove();
                return;
            }
            let srv = byKey[rowKey];
            $row.attr('data-price', srv.price).attr('data-stock', srv.stock);
            $('#lnkt_cartQty_' + itemId).val(srv.quantity).attr('max', Math.max(1, srv.stock || 1));
            $('#lnkt_lineTotal_' + itemId).text(lnktFormatVnd(srv.line_total));
        });

        let lineCount = (summary.line_count !== undefined) ? summary.line_count : $('.lnkt-cart-row').length;
        let totalQty = (summary.total_qty !== undefined) ? summary.total_qty : 0;
        $('#lnkt_cartItemCountBadge').text(lineCount);
        $('.cart-badge-custom').text(totalQty);

        if (lineCount === 0) {
            $('#lnkt_emptyCartNotice').removeClass('d-none');
            $('#lnkt_btnSubmitCheckout').prop('disabled', true).addClass('disabled');
        } else {
            $('#lnkt_emptyCartNotice').addClass('d-none');
            $('#lnkt_btnSubmitCheckout').prop('disabled', false).removeClass('disabled');
            if (items[0]) {
                $('#tgb_checkout_product_id').val(items[0].product_id);
                $('#tgb_checkout_size').val(items[0].size);
                $('#tgb_checkout_qty').val(items[0].quantity);
            }
        }

        let discountAmt = Number(summary.discount_amount || 0);
        let shipFee = Number(summary.shipping_fee || 0);
        let voucherDisc = Number(summary.voucher_discount || 0);
        let voucherCode = String(summary.voucher_code || '').trim();
        let totalAmt = Number(summary.total_amount || 0);
        let rewardPts = Number(summary.reward_points || 0);

        $('#lnkt_summarySubtotal').text(lnktFormatVnd(summary.subtotal || 0));
        $('#lnkt_summaryDiscount').text(discountAmt > 0 ? ('-' + lnktFormatVnd(discountAmt)) : '0 đ');

        let $shipBadge = $('#lnkt_summaryShipping');
        $shipBadge.attr('data-fee', shipFee);
        if (shipFee <= 0) {
            $shipBadge.text('Miễn phí (0 đ)').removeClass('badge-warning badge-sky').addClass('badge-mint');
        } else {
            $shipBadge.text(lnktFormatVnd(shipFee)).removeClass('badge-mint badge-warning').addClass('badge-sky');
        }

        if (summary.shipping_meta && $('#lnkt_shippingMetaInfo').length) {
            let sm = summary.shipping_meta;
            let wg = summary.weight_gram || sm.weight_gram || 250;
            let metaLine = [sm.service_label, sm.zone_label, 'Khối lượng: ' + wg + 'g', sm.eta_text].filter(Boolean).join(' • ');
            $('#lnkt_shippingMetaInfo').html('<i class="fa-solid fa-truck text-peach mr-1"></i>' + lnktEscapeHtml(metaLine));
        }

        if ($('#lnkt_appliedVoucherInput').length) {
            $('#lnkt_appliedVoucherInput').val(voucherCode);
        }
        if ($('#lnkt_btnRemoveVoucher').length) {
            $('#lnkt_btnRemoveVoucher').toggleClass('d-none', voucherCode === '');
        }
        if ($('#lnkt_summaryVoucherRow').length) {
            $('#lnkt_summaryVoucherRow').attr('data-voucher-discount', voucherDisc);
            if (voucherDisc > 0 && voucherCode !== '') {
                $('#lnkt_summaryVoucherRow').removeClass('d-none');
                $('#lnkt_summaryVoucherLabel').text('4. Voucher (' + voucherCode + '):');
                $('#lnkt_summaryVoucherDiscount').text('-' + lnktFormatVnd(voucherDisc));
            } else {
                $('#lnkt_summaryVoucherRow').addClass('d-none');
                $('#lnkt_summaryVoucherLabel').text('4. Voucher:');
                $('#lnkt_summaryVoucherDiscount').text('0 đ');
            }
        }

        $('#lnkt_summaryTotalAmount').text(lnktFormatVnd(totalAmt));
        if ($('#tgb_checkout_total').length) { $('#tgb_checkout_total').val(totalAmt); }
        $('#lnkt_summaryRewardPoints').text('+' + rewardPts.toLocaleString('vi-VN') + ' điểm VIP');
    };

    function lnktSyncCartQtyWithServer(itemId, targetQty) {
        let $inputEl = $('#lnkt_cartQty_' + itemId);
        let $row = $('#lnkt_cartRow_' + itemId);
        let cartKey = $row.attr('data-cart-key') || $inputEl.attr('data-cart-key') || '';
        let qty = Math.max(1, parseInt(targetQty, 10) || 1);
        $inputEl.val(qty);

        if (!cartKey) {
            window.lnktRecalculateCheckout();
            return;
        }

        $('#lnkt_checkoutAlert').addClass('d-none').text('');
        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxUpdateCartItem',
            type: 'POST',
            dataType: 'json',
            data: { cart_key: cartKey, quantity: qty },
            timeout: 4000,
            success: function (res) {
                if (res && res.cart_summary) {
                    window.lnktApplyServerCartSummary(res.cart_summary);
                }
            },
            error: function (xhr) {
                let res = xhr.responseJSON || {};
                let msg = res.message || 'Số lượng đặt mua vượt quá tồn kho thực tế!';
                window.lnktShowCheckoutAlert('error', msg);
                if (res.max_for_line) {
                    $inputEl.val(res.max_for_line);
                }
                if (res.cart_summary) {
                    window.lnktApplyServerCartSummary(res.cart_summary);
                } else {
                    window.lnktRecalculateCheckout();
                }
            }
        });
    }

    window.lnktChangeCartQty = function (itemId, delta) {
        let $inputEl = $('#lnkt_cartQty_' + itemId);
        let cur = parseInt($inputEl.val(), 10) || 1;
        let next = Math.max(1, cur + delta);
        lnktSyncCartQtyWithServer(itemId, next);
    };

    window.lnktOnCartQtyInputChange = function (itemId) {
        let $inputEl = $('#lnkt_cartQty_' + itemId);
        let rawVal = parseInt($inputEl.val(), 10);
        if (isNaN(rawVal) || rawVal < 1) {
            rawVal = 1;
        }
        lnktSyncCartQtyWithServer(itemId, rawVal);
    };

    window.lnktRemoveCartItem = function (itemId) {
        let $row = $('#lnkt_cartRow_' + itemId);
        let cartKey = $row.attr('data-cart-key') || '';
        if (!cartKey) {
            $row.remove();
            window.lnktRecalculateCheckout();
            return;
        }

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxRemoveCartItem',
            type: 'POST',
            dataType: 'json',
            data: { cart_key: cartKey },
            timeout: 4000,
            success: function (res) {
                $row.remove();
                if (res && res.cart_summary) {
                    window.lnktApplyServerCartSummary(res.cart_summary);
                } else {
                    window.lnktRecalculateCheckout();
                }
            },
            error: function () {
                window.lnktShowCheckoutAlert('error', 'Không thể xóa sản phẩm khỏi giỏ hàng lúc này. Vui lòng thử lại!');
            }
        });
    };

    // Chiết khấu VIP ở trang Checkout do Server tự tính theo tài khoản (không cho đổi thủ công)
    window.lnktSetCheckoutVipDiscount = function () {
        return false;
    };

    window.lnktRecalculateCheckout = function () {
        let subtotal = 0;
        let itemCount = 0;
        let totalQty = 0;

        $('.lnkt-cart-row').each(function () {
            let $row = $(this);
            let itemId = $row.attr('data-item-id');
            let price = parseInt($row.attr('data-price'), 10) || 0;
            let qty = Math.max(1, parseInt($('#lnkt_cartQty_' + itemId).val(), 10) || 1);
            let lineTotal = price * qty;

            subtotal += lineTotal;
            totalQty += qty;
            itemCount++;
            $('#lnkt_lineTotal_' + itemId).text(lnktFormatVnd(lineTotal));
        });

        $('#lnkt_cartItemCountBadge').text(itemCount);
        if ($('.lnkt-cart-row').length === 0) {
            $('.cart-badge-custom').text(0);
            $('#lnkt_emptyCartNotice').removeClass('d-none');
        } else {
            $('.cart-badge-custom').text(totalQty);
            $('#lnkt_emptyCartNotice').addClass('d-none');
        }

        let discountPercent = parseFloat($('#lnkt_checkoutDiscountPercent').val()) || 0;
        let discountAmount = Math.round((subtotal * discountPercent) / 100);
        let curShipFee = parseFloat($('#lnkt_summaryShipping').attr('data-fee')) || 0;
        if ($('.lnkt-cart-row').length === 0) curShipFee = 0;
        let curVoucherDisc = parseFloat($('#lnkt_summaryVoucherRow').attr('data-voucher-discount')) || 0;
        if ($('.lnkt-cart-row').length === 0) curVoucherDisc = 0;
        let totalAmount = Math.max(0, subtotal - discountAmount - curVoucherDisc + curShipFee);
        let rewardPts = Math.floor(totalAmount / 10000);

        $('#lnkt_summarySubtotal').text(lnktFormatVnd(subtotal));
        $('#lnkt_summaryDiscount').text(discountAmount > 0 ? ('-' + lnktFormatVnd(discountAmount)) : '0 đ');
        if (curShipFee <= 0) {
            $('#lnkt_summaryShipping').text('Miễn phí (0 đ)').removeClass('badge-warning badge-sky').addClass('badge-mint');
        } else {
            $('#lnkt_summaryShipping').text(lnktFormatVnd(curShipFee)).removeClass('badge-mint badge-warning').addClass('badge-sky');
        }
        $('#lnkt_summaryTotalAmount').text(lnktFormatVnd(totalAmount));
        if ($('#tgb_checkout_total').length) { $('#tgb_checkout_total').val(totalAmount); }
        $('#lnkt_summaryRewardPoints').text('+' + rewardPts.toLocaleString('vi-VN') + ' điểm VIP');
    };

    // ------------------------------------------------------------------------
    // 4. MH 06: Theo dõi Tiến trình Đơn hàng Stepper 5 Bước (order-tracking.php)
    // ------------------------------------------------------------------------
    window.lnktSetTrackingStatus = function (statusKey, isOwner) {
        const orderFlow = ['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat'];
        const statusMeta = {
            'cho_duyet':   { label: 'Chờ duyệt',     badgeClass: 'badge-warning text-dark', icon: 'fa-clock' },
            'da_xac_nhan': { label: 'Đã xác nhận',   badgeClass: 'badge-sky',               icon: 'fa-box-open' },
            'dang_giao':   { label: 'Đang giao',     badgeClass: 'badge-primary',           icon: 'fa-truck-fast' },
            'hoan_tat':    { label: 'Hoàn tất',      badgeClass: 'badge-mint',              icon: 'fa-circle-check' },
            'da_huy':      { label: 'Đã hủy',        badgeClass: 'badge-danger',            icon: 'fa-ban' }
        };

        let meta = statusMeta[statusKey] || statusMeta['cho_duyet'];
        $('#lnkt_currentStatusBadge')
            .attr('class', 'badge px-3 py-2 rounded-pill h6 mb-1 ' + meta.badgeClass)
            .html('<i class="fa-solid ' + meta.icon + ' mr-1"></i> Trạng thái: ' + meta.label);

        $('#lnkt_stepperTimeline').attr('data-current-status', statusKey);
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

        if (statusKey === 'hoan_tat') {
            $('#lnkt_completedReviewBanner').removeClass('d-none');
            if (isOwner === false) {
                $('#lnkt_bannerReviewBtnWrap').empty();
                $('#lnkt_btnOpenReturnModal').addClass('d-none');
            }
        } else {
            $('#lnkt_completedReviewBanner').addClass('d-none');
            $('#lnkt_btnOpenReturnModal').addClass('d-none');
        }
    };

    window.lnktCopyBankText = function (text, btnEl) {
        let val = String(text || '').trim();
        if (!val) return;
        let finishUi = function () {
            if (!btnEl) return;
            let $b = $(btnEl);
            let orig = $b.data('orig-html') || $b.html();
            $b.data('orig-html', orig);
            $b.html('<i class="fa-solid fa-check text-success mr-1"></i>Đã chép');
            setTimeout(function () {
                $b.html(orig);
            }, 1600);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(val).then(finishUi).catch(function () {
                let $tmp = $('<textarea>').val(val).appendTo('body').select();
                try { document.execCommand('copy'); } catch (e) {}
                $tmp.remove();
                finishUi();
            });
        } else {
            let $tmp = $('<textarea>').val(val).appendTo('body').select();
            try { document.execCommand('copy'); } catch (e) {}
            $tmp.remove();
            finishUi();
        }
    };

    window.lnktRenderTrackedOrder = function (payload) {
        if (!payload || !payload.order) return;
        let ord = payload.order;
        let oid = parseInt(ord.ma_don_hang, 10) || 0;
        let ocode = ord.ma_van_don || ('FK-ORD-' + String(oid).padStart(6, '0'));
        let statusKey = ord.trang_thai_don_hang || 'cho_duyet';
        let isOwner = !!payload.is_owner;
        let canCancel = !!payload.can_cancel;
        let canConfirmQr = !!payload.can_confirm_qr;
        let canSwitch = !!payload.can_switch_payment;
        let payMethod = (ord.phuong_thuc_thanh_toan || 'cod').toLowerCase();
        let payStatus = (ord.trang_thai_thanh_toan || 'chua_thanh_toan').toLowerCase();
        let isPaid = (!!ord.is_paid || payStatus === 'da_thanh_toan');
        let isExpired = !!ord.is_payment_expired;
        let createdLabel = lnktFormatMysqlDt(ord.ngay_dat_hang);
        let updatedLabel = ord.ngay_cap_nhat ? lnktFormatMysqlDt(ord.ngay_cap_nhat) : createdLabel;
        let deadlineLabel = ord.han_thanh_toan ? lnktFormatMysqlDt(ord.han_thanh_toan) : '--:--';
        let createdTs = parseInt(ord.created_ts, 10) || lnktParseMysqlTs(ord.ngay_dat_hang);
        let deadlineTs = parseInt(ord.han_thanh_toan_ts, 10) || (createdTs + 86400);
        let fullAddress = [ord.dia_chi_giao_hang, ord.phuong_xa, ord.quan_huyen, ord.tinh_thanh_pho].filter(Boolean).join(', ');
        let totalAmt = Number(ord.tong_tien_thanh_toan || 0);
        let subtotalAmt = Number(ord.tien_hang_tam_tinh || 0);
        let discountAmt = Number(ord.tien_giam_gia || 0);
        let shipFeeAmt = Number(ord.phi_van_chuyen || 0);
        let voucherDiscAmt = Number(ord.tien_giam_voucher || 0);
        let voucherCodeStr = String(ord.ma_voucher || '').trim();
        let carrierCode = String(ord.ma_van_don_hang_vc || '').trim();
        let carrierName = String(ord.don_vi_van_chuyen || 'GHN').trim();
        let codCollectAmt = (ord.cod_collect_amount !== undefined)
            ? Number(ord.cod_collect_amount)
            : ((isPaid || statusKey === 'da_huy') ? 0 : totalAmt);
        let rewardPts = Math.floor(totalAmt / 10000);
        let transferMemo = ord.noi_dung_chuyen_khoan || ('FK' + oid);

        $('#lnkt_order_tracking_section')
            .attr('data-order-id', oid)
            .attr('data-order-created-ts', createdTs)
            .attr('data-deadline-ts', deadlineTs)
            .attr('data-payment-method', payMethod)
            .attr('data-payment-status', payStatus)
            .attr('data-is-expired', isExpired ? '1' : '0');
        $('#lnkt_trackingEmptyCard').addClass('d-none');
        $('#lnkt_trackingContentWrap').removeClass('d-none');

        $('#lnkt_displayOrderCode').text('#' + ocode);
        $('#lnkt_receiverMeta').html(
            'Người nhận: <strong>' + lnktEscapeHtml(ord.ten_nguoi_nhan) + '</strong> (' +
            lnktEscapeHtml(ord.so_dien_thoai_nhan) + ') • ' + lnktEscapeHtml(fullAddress)
        );
        $('#lnkt_createdTimeMeta').html('Ngày đặt: <strong>' + lnktEscapeHtml(createdLabel) + '</strong>');
        $('#lnkt_staffMeta').html('Nhân viên phụ trách: <strong>' + lnktEscapeHtml(ord.nhan_vien_phu_trach || 'Đang chờ tiếp nhận') + '</strong>');
        if ($('#lnkt_carrierMeta').length) {
            if (carrierCode !== '') {
                $('#lnkt_carrierMeta').removeClass('d-none').html('Vận đơn <strong>' + lnktEscapeHtml(carrierName || 'GHN') + '</strong>: <strong id="lnkt_carrierCodeText">' + lnktEscapeHtml(carrierCode) + '</strong>');
            } else {
                $('#lnkt_carrierMeta').addClass('d-none');
            }
        }

        if (canCancel) {
            $('#lnkt_ownerCancelBar').removeClass('d-none');
            $('#lnkt_btnCancelMyOrder').attr('onclick', 'lnktCancelMyOrder(' + oid + ')').prop('disabled', false);
        } else {
            $('#lnkt_ownerCancelBar').addClass('d-none');
        }

        $('.lnkt-stepper-time').text('--:--');
        $('#lnkt_stepTime_cho_duyet').text(createdLabel);
        $('#lnkt_stepTime_' + statusKey).text(updatedLabel);
        window.lnktSetTrackingStatus(statusKey, isOwner);

        // 1. Panel VietQR (chỉ hiện khi chọn VietQR và CHƯA thanh toán, CHƯA hủy)
        if (payMethod === 'vietqr' && !isPaid && statusKey !== 'da_huy') {
            $('#lnkt_vietQrPanel').removeClass('d-none');
            if (ord.qr_url) {
                $('#lnkt_vietQrImg').attr('src', ord.qr_url);
            }
            $('#lnkt_qrDeadlineLabel').text(deadlineLabel);
            $('#lnkt_vietQrAmount').text(lnktFormatVnd(totalAmt));
            $('#lnkt_vietQrContent').text(transferMemo);
            $('#lnkt_btnCopyAmount').attr('onclick', "lnktCopyBankText('" + Math.round(totalAmt) + "', this)");
            $('#lnkt_btnCopyMemo').attr('onclick', "lnktCopyBankText('" + lnktEscapeHtml(transferMemo) + "', this)");

            if (canConfirmQr || canSwitch) {
                $('#lnkt_vietQrActionRow').removeClass('d-none');
            } else {
                $('#lnkt_vietQrActionRow').addClass('d-none');
            }
            $('#lnkt_btnConfirmVietQr')
                .toggleClass('d-none', !canConfirmQr)
                .attr('onclick', 'lnktCheckVietQrPayment(' + oid + ', true)')
                .prop('disabled', false);
            $('#lnkt_btnSwitchToCod')
                .toggleClass('d-none', !canSwitch)
                .attr('onclick', "lnktSwitchOrderPaymentMethod(" + oid + ", 'cod')")
                .prop('disabled', false);

            if (payload.check_result && payload.check_result.status === 'underpaid' && payload.check_result.message) {
                $('#lnkt_vietQrAlert')
                    .removeClass('d-none alert-mint text-slate')
                    .addClass('alert-danger d-block')
                    .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(payload.check_result.message));
            }
        } else {
            $('#lnkt_vietQrPanel').addClass('d-none');
        }

        // 2. Banner Đã thanh toán hoàn tất (Thu hộ bưu tá: 0 đồng)
        if (isPaid) {
            $('#lnkt_paidReceiptBanner').removeClass('d-none');
            if (ord.ma_giao_dich_ngan_hang) {
                let paidTimeStr = ord.thoi_gian_thanh_toan ? (' • Lúc ' + lnktFormatMysqlDt(ord.thoi_gian_thanh_toan)) : '';
                $('#lnkt_paidTxMeta').text('MB Bank • Mã GD: ' + ord.ma_giao_dich_ngan_hang + paidTimeStr);
            } else {
                $('#lnkt_paidTxMeta').text('Đã ghi nhận thanh toán thành công trên hệ thống FashionKids');
            }
            let paidMethodText = (payMethod === 'vietqr') ? 'chuyển khoản MB Bank' : 'tiền mặt hoàn tất';
            $('#lnkt_paidHeadline').text('Shop đã nhận đủ ' + Math.round(totalAmt).toLocaleString('vi-VN') + ' đồng qua ' + paidMethodText);
        } else {
            $('#lnkt_paidReceiptBanner').addClass('d-none');
        }

        // 3. Panel Tiền mặt COD (khi chọn COD & chưa hoàn tất/hủy)
        if (payMethod === 'cod' && !isPaid && statusKey !== 'da_huy') {
            $('#lnkt_codPaymentPanel').removeClass('d-none');
            $('#lnkt_codPanelAmount').text(Math.round(totalAmt).toLocaleString('vi-VN') + ' đồng');
            $('#lnkt_btnSwitchToVietQr')
                .toggleClass('d-none', !canSwitch)
                .attr('onclick', "lnktSwitchOrderPaymentMethod(" + oid + ", 'vietqr')")
                .prop('disabled', false);
        } else {
            $('#lnkt_codPaymentPanel').addClass('d-none');
        }

        // 4. Banner Quá hạn 24h tự hủy
        if (statusKey === 'da_huy' && isExpired) {
            $('#lnkt_expiredPaymentBanner').removeClass('d-none');
        } else {
            $('#lnkt_expiredPaymentBanner').addClass('d-none');
        }

        $('#lnkt_completedPointsText').text(
            'Đã tích lũy +' + rewardPts.toLocaleString('vi-VN') + ' điểm thưởng vào thẻ VIP ' + (ord.khach_hang_hang || 'thành viên')
        );

        let items = Array.isArray(ord.items) ? ord.items : [];
        $('#lnkt_trackedItemsCountLabel').text(items.length + ' sản phẩm');

        let firstPending = null;
        let rowsHtml = '';
        items.forEach(function (it) {
            let pid = parseInt(it.ma_san_pham, 10) || 0;
            let qty = parseInt(it.so_luong, 10) || 1;
            let unitP = Number(it.don_gia_mua || 0);
            let lineSum = qty * unitP;
            let reviewed = !!it.da_danh_gia;
            if (!reviewed && !firstPending) {
                firstPending = it;
            }

            let safeName = lnktEscapeHtml(it.ten_san_pham);
            let safeJsName = String(it.ten_san_pham || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            let reviewCell = '';
            if (statusKey === 'hoan_tat' && isOwner) {
                if (reviewed) {
                    reviewCell = '<span class="badge badge-mint px-2 py-1 rounded-pill font-weight-bold"><i class="fa-solid fa-star text-warning mr-1"></i>Đã đánh giá (' + (parseInt(it.so_sao_danh_gia, 10) || 5) + '/5)</span>';
                } else {
                    reviewCell = '<button type="button" class="btn btn-sm lnkt-btn-buttercup lnkt-item-review-btn px-3 py-1 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalReview" onclick="lnktOpenProductReviewModal(\'' + lnktEscapeHtml(safeJsName) + '\', ' + pid + ', ' + oid + ')">&#9733; Viết đánh giá sản phẩm</button>';
                }
            } else if (statusKey === 'da_huy') {
                reviewCell = '<span class="small text-muted-custom">Đơn đã hủy</span>';
            } else if (statusKey === 'hoan_tat' && !isOwner) {
                reviewCell = '<span class="small text-muted-custom">Chỉ chủ đơn mới đánh giá</span>';
            } else {
                reviewCell = '<span class="small text-muted-custom lnkt-item-pending-review">Chờ hoàn tất đơn</span>';
            }

            rowsHtml +=
                '<tr>' +
                    '<td>' +
                        '<div class="d-flex align-items-center">' +
                            '<span class="lbn-inv-thumb mr-3 flex-shrink-0"><img src="' + lnktEscapeHtml(it.image || '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg') + '" alt="' + safeName + '" class="lbn-thumb-img"></span>' +
                            '<div>' +
                                '<a href="/FashionKids/views/pages/product-detail.php?id=' + pid + '" class="font-weight-bold text-slate text-decoration-none d-block">' + safeName + '</a>' +
                                '<small class="text-muted-custom">Đơn giá: <strong class="text-slate">' + lnktFormatVnd(unitP) + '</strong></small>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td class="text-center"><span class="badge badge-pill badge-sky px-2 py-1">' + lnktEscapeHtml(it.kich_co_chon) + '</span></td>' +
                    '<td class="text-center font-weight-bold">x' + qty + '</td>' +
                    '<td class="text-right font-weight-bold text-peach text-nowrap">' + lnktFormatVnd(lineSum) + '</td>' +
                    '<td class="text-center text-nowrap" data-review-cell-pid="' + pid + '">' + reviewCell + '</td>' +
                '</tr>';
        });
        $('#lnkt_trackedItemsBody').html(rowsHtml);

        let canRequestReturn = !!payload.can_request_return;
        let retReqData = payload.return_request || ord.return_request || null;
        if (statusKey === 'hoan_tat' && isOwner && firstPending) {
            let safeFirstJs = String(firstPending.ten_san_pham || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            $('#lnkt_bannerReviewBtnWrap').html(
                '<button type="button" class="btn lnkt-btn-buttercup px-4 py-2 shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalReview" onclick="lnktOpenProductReviewModal(\'' + lnktEscapeHtml(safeFirstJs) + '\', ' + (parseInt(firstPending.ma_san_pham, 10) || 0) + ', ' + oid + ')">&#9733; Viết đánh giá sản phẩm</button>'
            );
        } else {
            $('#lnkt_bannerReviewBtnWrap').empty();
        }
        let safeOcodeRetJs = String(ocode || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        let safeTotalRetJs = lnktFormatVnd(totalAmt).replace(/'/g, "\\'");
        $('#lnkt_btnOpenReturnModal')
            .toggleClass('d-none', !canRequestReturn)
            .attr('onclick', "lnktOpenReturnRequestModal(" + oid + ", '" + lnktEscapeHtml(safeOcodeRetJs) + "', '" + lnktEscapeHtml(safeTotalRetJs) + "')");
        if (typeof window.lnktRenderReturnProgress === 'function') {
            window.lnktRenderReturnProgress(retReqData);
        }

        $('#lnkt_sumSubtotal').text(lnktFormatVnd(subtotalAmt));
        $('#lnkt_sumVipLabel').text('Ưu đãi VIP (' + (ord.khach_hang_hang || 'Thành viên') + '):');
        $('#lnkt_sumDiscount').text(discountAmt > 0 ? ('-' + lnktFormatVnd(discountAmt)) : '0 đ');
        if ($('#lnkt_sumShippingFee').length) {
            if (shipFeeAmt <= 0) {
                $('#lnkt_sumShippingFee').text('Miễn phí (0 đ)').removeClass('badge-sky badge-warning').addClass('badge-mint');
            } else {
                $('#lnkt_sumShippingFee').text(lnktFormatVnd(shipFeeAmt)).removeClass('badge-mint badge-warning').addClass('badge-sky');
            }
        }
        if ($('#lnkt_sumVoucherRow').length) {
            if (voucherDiscAmt > 0) {
                $('#lnkt_sumVoucherRow').removeClass('d-none');
                $('#lnkt_sumVoucherLabel').text('Mã giảm giá' + (voucherCodeStr !== '' ? (' (' + voucherCodeStr + ')') : '') + ':');
                $('#lnkt_sumVoucherDiscount').text('-' + lnktFormatVnd(voucherDiscAmt));
            } else {
                $('#lnkt_sumVoucherRow').addClass('d-none');
                $('#lnkt_sumVoucherLabel').text('Mã giảm giá:');
                $('#lnkt_sumVoucherDiscount').text('0 đ');
            }
        }
        $('#lnkt_sumPaymentMethod').text(payMethod === 'vietqr' ? 'Chuyển khoản VietQR MB Bank' : 'Thanh toán khi nhận hàng (COD)');
        $('#lnkt_sumPaymentStatus')
            .removeClass('text-success text-danger text-warning')
            .addClass(isPaid ? 'text-success' : (isExpired ? 'text-danger' : 'text-warning'))
            .text(isPaid ? 'Đã thanh toán' : (isExpired ? 'Quá hạn 24h (Đã hủy)' : (payMethod === 'vietqr' ? 'Chờ chuyển khoản' : 'Thanh toán khi giao')));
        $('#lnkt_sumCodCollect')
            .removeClass('text-success text-peach')
            .addClass(codCollectAmt <= 0 ? 'text-success' : 'text-peach')
            .text(Math.round(codCollectAmt).toLocaleString('vi-VN') + ' đồng');
        $('#lnkt_sumTotal').text(lnktFormatVnd(totalAmt));
        $('#lnkt_sumAddress').text(fullAddress);

        if (ord.ghi_chu) {
            $('#lnkt_sumNoteWrap').removeClass('d-none');
            $('#lnkt_sumNote').text(ord.ghi_chu);
        } else {
            $('#lnkt_sumNoteWrap').addClass('d-none');
            $('#lnkt_sumNote').text('');
        }

        // Đồng bộ danh sách pill đơn hàng gần đây
        $('.lnkt-order-history-pill').removeClass('btn-pastel-primary active').addClass('btn-light border text-slate');
        let $activePill = $('.lnkt-order-history-pill[data-order-id="' + oid + '"]');
        if ($activePill.length) {
            $activePill.removeClass('btn-light border text-slate').addClass('btn-pastel-primary active');
            if (ord.status_meta && ord.status_meta.label) {
                $activePill.find('.badge').text(ord.status_meta.label);
            }
        }
    };

    if ($('#lnkt_stepperTimeline').length) {
        let initSt = $('#lnkt_stepperTimeline').attr('data-current-status') || 'cho_duyet';
        let initHasOwnerCancel = !$('#lnkt_ownerCancelBar').hasClass('d-none') || $('.lnkt-item-review-btn').length > 0;
        window.lnktSetTrackingStatus(initSt, initHasOwnerCancel || $('body').attr('data-user-role') !== 'guest');
    }

    function lnktShowTrackingAlert(type, msg) {
        let $box = $('#lnkt_trackingAlert');
        if (!$box.length) return;
        $box.removeClass('d-none alert-danger alert-mint lnkt-alert-mint-pastel text-slate');
        if (type === 'success') {
            $box.addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
                .html('<i class="fa-solid fa-circle-check text-success mr-1"></i>' + lnktEscapeHtml(msg));
        } else {
            $box.addClass('alert-danger d-block')
                .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(msg));
        }
    }

    window.lnktSearchOrderTracking = function () {
        let query = ($('#lnkt_trackingCodeInput').val() || '').trim();
        if (!query) {
            lnktShowTrackingAlert('error', 'Vui lòng nhập Mã vận đơn (VD: FK-ORD-000001) hoặc Số điện thoại đặt hàng.');
            return;
        }
        $('#lnkt_trackingAlert').addClass('d-none').text('');
        let $btn = $('#lnkt_btnTrackSearch');
        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang tìm...');

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxTrackOrder',
            type: 'GET',
            dataType: 'json',
            data: { q: query },
            timeout: 15000,
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                if (res && res.status === 'success' && res.data) {
                    window.lnktRenderTrackedOrder(res.data);
                } else {
                    lnktShowTrackingAlert('error', (res && res.message) ? res.message : 'Không tìm thấy đơn hàng phù hợp!');
                }
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không tìm thấy đơn hàng khớp với mã vận đơn hoặc số điện thoại vừa nhập!';
                lnktShowTrackingAlert('error', msg);
            }
        });
    };

    window.lnktLoadTrackedOrderById = function (orderId) {
        $('#lnkt_trackingAlert').addClass('d-none').text('');
        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxTrackOrder',
            type: 'GET',
            dataType: 'json',
            data: { order_id: orderId },
            timeout: 15000,
            success: function (res) {
                if (res && res.status === 'success' && res.data && res.data.order) {
                    $('#lnkt_trackingCodeInput').val(res.data.order.ma_van_don || orderId);
                    window.lnktRenderTrackedOrder(res.data);
                }
            },
            error: function () {
                lnktShowTrackingAlert('error', 'Không thể tải chi tiết đơn hàng #' + orderId);
            }
        });
    };

    $('#lnkt_trackingCodeInput').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            window.lnktSearchOrderTracking();
        }
    });

    window.lnktCheckVietQrPayment = function (orderId, isManual) {
        let $btn = $('#lnkt_btnConfirmVietQr');
        let $alert = $('#lnkt_vietQrAlert');
        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Đang kiểm tra MB Bank...');
        $alert.addClass('d-none').removeClass('alert-danger alert-mint alert-warning text-slate');

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxCheckVietQrPayment',
            type: 'POST',
            dataType: 'json',
            data: { order_id: orderId, manual: (isManual !== false ? 1 : 0) },
            timeout: 15000,
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                if (res && res.data) {
                    window.lnktRenderTrackedOrder(res.data);
                }
                if (res && res.paid) {
                    lnktShowTrackingAlert('success', res.message || 'Thanh toán thành công! Số tiền cần thanh toán khi nhận hàng: 0 đồng.');
                    return;
                }
                let st = res ? res.check_status : 'pending';
                let msg = (res && res.message) ? res.message : 'Chưa phát hiện giao dịch chuyển khoản mới.';
                if (st === 'expired') {
                    lnktShowTrackingAlert('error', msg);
                    return;
                }
                $alert
                    .removeClass('d-none')
                    .addClass((st === 'underpaid' ? 'alert-danger' : 'alert-warning text-slate') + ' d-block')
                    .html('<i class="fa-solid ' + (st === 'underpaid' ? 'fa-triangle-exclamation' : 'fa-circle-info') + ' mr-1"></i>' + lnktEscapeHtml(msg));
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể kết nối máy chủ đối soát MB Bank lúc này.';
                $alert.removeClass('d-none').addClass('alert-danger d-block').text(msg);
            }
        });
    };

    window.lnktConfirmVietQr = function (orderId) {
        return window.lnktCheckVietQrPayment(orderId, true);
    };

    window.lnktSwitchOrderPaymentMethod = function (orderId, targetMethod) {
        let confirmMsg = (targetMethod === 'cod')
            ? 'Bạn muốn chuyển đơn hàng #' + orderId + ' sang Thanh toán tiền mặt khi nhận hàng (COD)?'
            : 'Bạn muốn chuyển đơn hàng #' + orderId + ' sang Quét mã VietQR MB Bank (tự động xác nhận khi chuyển đủ tiền)?';
        if (!confirm(confirmMsg)) {
            return;
        }
        let $btn = targetMethod === 'cod' ? $('#lnkt_btnSwitchToCod') : $('#lnkt_btnSwitchToVietQr');
        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang chuyển...');

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxSwitchPaymentMethod',
            type: 'POST',
            dataType: 'json',
            data: { order_id: orderId, payment_method: targetMethod },
            timeout: 8000,
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                if (res && res.data) {
                    window.lnktRenderTrackedOrder(res.data);
                }
                lnktShowTrackingAlert('success', (res && res.message) ? res.message : 'Đã thay đổi phương thức thanh toán thành công!');
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể thay đổi phương thức thanh toán lúc này.';
                lnktShowTrackingAlert('error', msg);
            }
        });
    };

    window.lnktCancelMyOrder = function (orderId) {
        if (!confirm('Bạn chắc chắn muốn hủy đơn hàng #' + orderId + '? Sản phẩm sẽ được hoàn trả tự động về kho hàng.')) {
            return;
        }
        let $btn = $('#lnkt_btnCancelMyOrder');
        $btn.prop('disabled', true);
        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxCancelMyOrder',
            type: 'POST',
            dataType: 'json',
            data: { order_id: orderId },
            timeout: 6000,
            complete: function () {
                $btn.prop('disabled', false);
            },
            success: function (res) {
                lnktShowTrackingAlert('success', (res && res.message) ? res.message : 'Đã hủy đơn hàng và hoàn kho thành công!');
                window.lnktLoadTrackedOrderById(orderId);
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể hủy đơn hàng này.';
                lnktShowTrackingAlert('error', msg);
            }
        });
    };

    if ($('#lnkt_order_tracking_section').length) {
        let lnktExpiredCheckFiredForOrder = 0;
        let lnktTrackPollInFlight = false;

        function lnktUpdateQrCountdownTick() {
            if ($('#lnkt_vietQrPanel').hasClass('d-none') || $('#lnkt_vietQrCountdownWrap').hasClass('d-none')) {
                return;
            }
            let $sec = $('#lnkt_order_tracking_section');
            let oid = parseInt($sec.attr('data-order-id'), 10) || 0;
            let deadlineTs = parseInt($sec.attr('data-deadline-ts'), 10) || 0;
            if (!deadlineTs) {
                let createdTs = parseInt($sec.attr('data-order-created-ts'), 10) || Math.floor(Date.now() / 1000);
                deadlineTs = createdTs + 86400;
            }
            let remaining = Math.max(0, deadlineTs - Math.floor(Date.now() / 1000));
            let hh = String(Math.floor(remaining / 3600)).padStart(2, '0');
            let mm = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            let ss = String(remaining % 60).padStart(2, '0');
            $('#lnkt_qrCountdown').text(hh + ':' + mm + ':' + ss);

            if (remaining === 0 && oid > 0 && lnktExpiredCheckFiredForOrder !== oid) {
                lnktExpiredCheckFiredForOrder = oid;
                window.lnktLoadTrackedOrderById(oid);
            }
        }
        lnktUpdateQrCountdownTick();
        setInterval(lnktUpdateQrCountdownTick, 1000);

        // Tự động đồng bộ tiến trình & kiểm tra Casso MB Bank mỗi 5 giây khi đơn chưa kết thúc
        setInterval(function () {
            if (lnktTrackPollInFlight) return;
            let oid = parseInt($('#lnkt_order_tracking_section').attr('data-order-id'), 10) || 0;
            let curSt = $('#lnkt_stepperTimeline').attr('data-current-status') || '';
            let wasPaid = ($('#lnkt_order_tracking_section').attr('data-payment-status') === 'da_thanh_toan');
            if (oid > 0 && curSt !== 'hoan_tat' && curSt !== 'da_huy') {
                lnktTrackPollInFlight = true;
                $.ajax({
                    url: '/FashionKids/index.php?c=order&a=ajaxTrackOrder',
                    type: 'GET',
                    dataType: 'json',
                    data: { order_id: oid },
                    timeout: 12000,
                    complete: function () {
                        lnktTrackPollInFlight = false;
                    },
                    success: function (res) {
                        if (res && res.status === 'success' && res.data) {
                            window.lnktRenderTrackedOrder(res.data);
                            let nowPaid = !!(res.data.order && res.data.order.is_paid);
                            if (!wasPaid && nowPaid) {
                                lnktShowTrackingAlert('success', res.message || 'Đã tự động đối soát thanh toán MB Bank thành công! Số tiền cần thu khi nhận hàng: 0 đồng.');
                            }
                        }
                    }
                });
            }
        }, 5000);

        // Tự động đồng bộ tiến trình hoàn hàng / trả hàng mỗi 4 giây (Req 4)
        let lnktRetTrackPollInFlight = false;
        setInterval(function () {
            if (lnktRetTrackPollInFlight) return;
            let oid = parseInt($('#lnkt_order_tracking_section').attr('data-order-id'), 10) || 0;
            let curSt = $('#lnkt_stepperTimeline').attr('data-current-status') || '';
            let retSt = $('#lnkt_returnProgressCard').attr('data-return-status') || '';
            let hasReturnCardVisible = $('#lnkt_returnProgressCard').length && !$('#lnkt_returnProgressCard').hasClass('d-none');
            if (oid <= 0) return;
            if (curSt !== 'hoan_tat' && !hasReturnCardVisible) return;
            if (retSt === 'hoan_tat_hoan' || retSt === 'tu_choi_hoan') return;

            lnktRetTrackPollInFlight = true;
            $.ajax({
                url: '/FashionKids/index.php?c=order&a=ajaxGetReturnStatus',
                type: 'GET',
                dataType: 'json',
                data: { order_id: oid },
                timeout: 8000,
                complete: function () {
                    lnktRetTrackPollInFlight = false;
                },
                success: function (res) {
                    if (!res || res.status !== 'success') return;
                    let retReq = res.return_request || (res.data && res.data.return_request) || null;
                    let canReq = (res.can_request_return !== undefined)
                        ? !!res.can_request_return
                        : !!(res.data && res.data.can_request_return);
                    if (retReq && typeof window.lnktRenderReturnProgress === 'function') {
                        window.lnktRenderReturnProgress(retReq);
                    }
                    if ($('#lnkt_btnOpenReturnModal').length) {
                        $('#lnkt_btnOpenReturnModal').toggleClass('d-none', !canReq);
                    }
                }
            });
        }, 4000);
    }

    window.lnktOpenProductReviewModal = function (productName, productId, orderId) {
        $('#modalReview').attr('data-context-lock', '1');
        $('#lnkt_reviewModalProductName').text(productName);
        if (productId && parseInt(productId, 10) > 0 && $('#tgb_review_product_id').length) {
            $('#tgb_review_product_id').val(parseInt(productId, 10));
        }
        if ($('#tgb_review_order_id').length) {
            $('#tgb_review_order_id').val(parseInt(orderId, 10) || 0);
        }
        if ($('#tgb_reviewProductContext').length) {
            if (productName) {
                $('#tgb_reviewProductContext')
                    .removeClass('d-none')
                    .html('<i class="fa-solid fa-box-open text-peach mr-1"></i>Sản phẩm: <strong>' + lnktEscapeHtml(productName) + '</strong>');
            } else {
                $('#tgb_reviewProductContext').addClass('d-none').text('');
            }
        }
        if ($('#modalReview').length === 0 && $('#lnkt_fallbackReviewModal').length) {
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
    let lnktOrderSoundEnabled = true;

    window.lnktPlayNewOrderChime = function () {
        if (!lnktOrderSoundEnabled) return;
        try {
            let AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            let ctx = new AudioCtx();
            let now = ctx.currentTime;
            [523.25, 659.25, 783.99].forEach(function (freq, idx) {
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now + idx * 0.11);
                gain.gain.setValueAtTime(0.08, now + idx * 0.11);
                gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.11 + 0.25);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now + idx * 0.11);
                osc.stop(now + idx * 0.11 + 0.26);
            });
        } catch (err) {}
    };

    window.lnktToggleOrderSound = function () {
        lnktOrderSoundEnabled = !lnktOrderSoundEnabled;
        let $btn = $('#lnkt_btnToggleSound');
        if (lnktOrderSoundEnabled) {
            $btn.html('<i class="fa-solid fa-volume-high text-peach mr-1"></i>Âm báo: Bật');
            window.lnktPlayNewOrderChime();
        } else {
            $btn.html('<i class="fa-solid fa-volume-xmark text-muted mr-1"></i>Âm báo: Tắt');
        }
    };

    window.lnktAcknowledgeNewOrders = function () {
        $('#new_order_count').text('0');
        $('#new_order_badge').addClass('d-none');
    };

    function lnktShowStaffOrderAlert(type, msg) {
        let $box = $('#lnkt_staffOrderAlert');
        if (!$box.length) return;
        $box.removeClass('d-none alert-danger alert-mint lnkt-alert-mint-pastel text-slate');
        if (type === 'success') {
            $box.addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
                .html('<i class="fa-solid fa-circle-check text-success mr-1"></i>' + lnktEscapeHtml(msg));
        } else {
            $box.addClass('alert-danger d-block')
                .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(msg));
        }
    }

    function lnktBuildStaffStatusBadgeHtml(uiStatus) {
        if (uiStatus === 'cho_duyet') {
            return '<span class="badge badge-warning text-dark px-2 py-1 rounded-pill text-nowrap"><i class="fa-solid fa-clock mr-1"></i>Chờ duyệt</span>';
        }
        if (uiStatus === 'da_xac_nhan') {
            return '<span class="badge badge-sky px-2 py-1 rounded-pill text-nowrap"><i class="fa-solid fa-box-open mr-1"></i>Đã xác nhận</span>';
        }
        if (uiStatus === 'dang_giao') {
            return '<span class="badge badge-primary px-2 py-1 rounded-pill text-nowrap"><i class="fa-solid fa-truck-fast mr-1"></i>Đang giao</span>';
        }
        if (uiStatus === 'hoan_tat') {
            return '<span class="badge badge-mint px-2 py-1 rounded-pill text-nowrap"><i class="fa-solid fa-circle-check mr-1"></i>Hoàn tất</span>';
        }
        if (uiStatus === 'da_huy') {
            return '<span class="badge badge-danger px-2 py-1 rounded-pill text-nowrap"><i class="fa-solid fa-ban mr-1"></i>Đã hủy</span>';
        }
        return '<span class="badge badge-secondary px-2 py-1 rounded-pill text-nowrap">' + lnktEscapeHtml(uiStatus) + '</span>';
    }

    function lnktBuildStaffQuickActionHtml(oid, ocode, st, pm, isPaid, carrierCode) {
        let safeCode = String(ocode || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        if (st === 'cho_duyet' && pm === 'vietqr' && !isPaid) {
            return (
                '<button type="button" class="btn btn-outline-primary btn-sm btn-block rounded-pill py-0 small font-weight-bold mb-1" onclick="lnktStaffVerifyOrderPayment(' + oid + ', this)"><i class="fa-solid fa-rotate mr-1"></i>Kiểm tra Casso MB</button>' +
                '<button type="button" class="btn btn-outline-secondary btn-sm btn-block rounded-pill py-0 small font-weight-bold mb-1" disabled title="Đơn VietQR chưa nhận đủ tiền qua MB Bank — Shop tự động không xác nhận"><i class="fa-solid fa-lock mr-1"></i>Chờ CK để xác nhận</button>'
            );
        }
        if (st === 'cho_duyet') {
            return '<button type="button" class="btn btn-outline-info btn-sm btn-block rounded-pill py-0 small font-weight-bold mb-1" onclick="lnktUpdateStaffOrderStatus(' + oid + ', \'' + lnktEscapeHtml(safeCode) + '\', \'da_xac_nhan\')"><i class="fa-solid fa-box-open mr-1"></i>Tiếp nhận &amp; Xác nhận</button>';
        }
        if (st === 'da_xac_nhan') {
            let pushBtn = !carrierCode
                ? '<button type="button" class="btn btn-pastel-sky btn-sm btn-block rounded-pill py-0 small font-weight-bold text-slate mb-1" onclick="lnktCreateCarrierWaybill(' + oid + ', this)"><i class="fa-solid fa-truck-pickup text-info mr-1"></i>Đẩy đơn GHN</button>'
                : '';
            return pushBtn + '<button type="button" class="btn btn-outline-primary btn-sm btn-block rounded-pill py-0 small font-weight-bold mb-1" onclick="lnktUpdateStaffOrderStatus(' + oid + ', \'' + lnktEscapeHtml(safeCode) + '\', \'dang_giao\')"><i class="fa-solid fa-truck-fast mr-1"></i>Chuyển Giao Hàng</button>';
        }
        if (st === 'dang_giao') {
            return '<button type="button" class="btn btn-outline-success btn-sm btn-block rounded-pill py-0 small font-weight-bold mb-1" onclick="lnktUpdateStaffOrderStatus(' + oid + ', \'' + lnktEscapeHtml(safeCode) + '\', \'hoan_tat\')"><i class="fa-solid fa-circle-check mr-1"></i>Hoàn Tất &amp; Tích Điểm</button>';
        }
        return '<small class="text-muted-custom d-block mb-1">Đã khóa chỉnh sửa</small>';
    }

    function lnktRenderStaffPaymentBadgesHtml(ord) {
        let pm = (ord.phuong_thuc_thanh_toan || 'cod').toLowerCase();
        let st = ord.trang_thai_don_hang || 'cho_duyet';
        let isPaid = (!!ord.is_paid || (ord.trang_thai_thanh_toan || '') === 'da_thanh_toan');
        let isExpired = !!ord.is_payment_expired;
        let remSec = parseInt(ord.remaining_seconds, 10) || 0;
        let remHrs = remSec > 0 ? Math.ceil(remSec / 3600) : 0;
        let txCode = ord.ma_giao_dich_ngan_hang || '';

        let html = '<span class="badge ' + (pm === 'vietqr' ? 'badge-sky' : 'badge-light border') + ' small py-1 px-2 rounded-pill text-nowrap">' + (pm === 'vietqr' ? 'VietQR MB' : 'COD') + '</span> ';
        if (isPaid) {
            html += '<span class="badge badge-mint small py-1 px-2 rounded-pill text-nowrap"><i class="fa-solid fa-circle-check mr-1"></i>Đã TT • Thu 0 đ</span>';
            if (txCode) {
                html += '<small class="d-block text-success font-weight-bold text-nowrap">GD: ' + lnktEscapeHtml(txCode) + '</small>';
            }
        } else if (st === 'da_huy' && isExpired) {
            html += '<span class="badge badge-danger small py-1 px-2 rounded-pill text-nowrap">Quá hạn 24h</span>';
        } else if (pm === 'vietqr') {
            html += '<span class="badge badge-warning text-dark small py-1 px-2 rounded-pill text-nowrap">Chờ CK (còn ~' + remHrs + 'h)</span>';
        } else {
            html += '<span class="badge badge-butter small py-1 px-2 rounded-pill text-nowrap">Thu tiền mặt</span>';
        }
        return html;
    }

    function lnktRenderStaffCollectHtml(ord) {
        let pm = (ord.phuong_thuc_thanh_toan || 'cod').toLowerCase();
        let st = ord.trang_thai_don_hang || 'cho_duyet';
        let isPaid = (!!ord.is_paid || (ord.trang_thai_thanh_toan || '') === 'da_thanh_toan');
        let totalF = lnktFormatVnd(ord.tong_tien_thanh_toan || 0);
        if (isPaid) {
            return '<small class="text-success font-weight-bold">Thu hộ: 0 đ</small>';
        }
        if (st === 'da_huy') {
            return '<small class="text-danger font-weight-bold">Đã hủy (0 đ)</small>';
        }
        if (pm === 'vietqr') {
            return '<small class="text-warning font-weight-bold">Chờ CK: ' + lnktEscapeHtml(totalF) + '</small>';
        }
        return '<small class="text-peach font-weight-bold">Thu hộ: ' + lnktEscapeHtml(totalF) + '</small>';
    }

    function lnktBuildStaffInspectCodText(ord) {
        let pm = (ord.phuong_thuc_thanh_toan || 'cod').toLowerCase();
        let st = ord.trang_thai_don_hang || 'cho_duyet';
        let isPaid = (!!ord.is_paid || (ord.trang_thai_thanh_toan || '') === 'da_thanh_toan');
        let amt = Number((ord.cod_collect_amount !== undefined) ? ord.cod_collect_amount : (ord.tong_tien_thanh_toan || 0));
        if (isPaid) {
            return { text: '0 đồng (Đã thanh toán — Không thu tiền)', cls: 'text-success' };
        }
        if (st === 'da_huy') {
            return { text: '0 đồng (Đơn đã hủy)', cls: 'text-danger' };
        }
        return {
            text: Math.round(amt).toLocaleString('vi-VN') + ' đồng' + (pm === 'vietqr' ? ' (Chờ CK)' : ' (Thu hộ tiền mặt COD)'),
            cls: 'text-peach'
        };
    }

    function lnktSyncExistingStaffRow(ord) {
        if (!ord || !ord.ma_don_hang) return;
        let oid = parseInt(ord.ma_don_hang, 10) || 0;
        let $row = $('#lnkt_orderRow_' + oid);
        if (!$row.length) return;

        let ocode = ord.ma_van_don || ('FK-ORD-' + String(oid).padStart(6, '0'));
        let st = ord.trang_thai_don_hang || 'cho_duyet';
        let pm = (ord.phuong_thuc_thanh_toan || 'cod').toLowerCase();
        let ps = (ord.trang_thai_thanh_toan || 'chua_thanh_toan').toLowerCase();
        let isPaid = (!!ord.is_paid || ps === 'da_thanh_toan');
        let isLocked = (st === 'hoan_tat' || st === 'da_huy');
        let lockConfirm = (st === 'cho_duyet' && pm === 'vietqr' && !isPaid);
        let carrierCode = String(ord.ma_van_don_hang_vc || '').trim();
        let carrierName = String(ord.don_vi_van_chuyen || 'GHN').trim();
        let shipVal = Number(ord.phi_van_chuyen || 0);
        let shipFmt = shipVal > 0 ? lnktFormatVnd(shipVal) : 'Miễn phí (0 đ)';
        let vouchVal = Number(ord.tien_giam_voucher || 0);
        let vouchCode = String(ord.ma_voucher || '').trim();
        let vouchFmt = vouchVal > 0 ? ('-' + lnktFormatVnd(vouchVal) + (vouchCode !== '' ? (' (' + vouchCode + ')') : '')) : '0 đ';

        $row.attr('data-status', st)
            .attr('data-payment-method', pm)
            .attr('data-payment-status', ps);

        $('#lnkt_payBadgeWrap_' + oid).html(lnktRenderStaffPaymentBadgesHtml(ord));
        $('#lnkt_collectBadge_' + oid).html(lnktRenderStaffCollectHtml(ord));
        $('#lnkt_orderBadgeCell_' + oid).html(lnktBuildStaffStatusBadgeHtml(st));
        if ($('#lnkt_carrierBadgeWrap_' + oid).length) {
            $('#lnkt_carrierBadgeWrap_' + oid).html(
                carrierCode
                    ? ('<span class="badge badge-light border text-info small py-0 px-2 rounded-pill mt-1 text-nowrap"><i class="fa-solid fa-barcode mr-1"></i>' + lnktEscapeHtml(carrierName + ': ' + carrierCode) + '</span>')
                    : ''
            );
        }
        $('#lnkt_quickActionCell_' + oid).html(lnktBuildStaffQuickActionHtml(oid, ocode, st, pm, isPaid, carrierCode));

        let $select = $('#lnkt_statusSelect_' + oid);
        if ($select.length) {
            $select.val(st).prop('disabled', isLocked);
            $select.find('option[value="da_xac_nhan"], option[value="dang_giao"], option[value="hoan_tat"]').prop('disabled', lockConfirm);
            let $optConfirm = $select.find('option[value="da_xac_nhan"]');
            $optConfirm.text(lockConfirm ? 'Đã xác nhận (Chờ TT)' : 'Đã xác nhận');
        }
        $('#lnkt_saveStatusBtn_' + oid).prop('disabled', isLocked);

        if (ord.nhan_vien_phu_trach) {
            $('#lnkt_rowStaff_' + oid).html('Phụ trách: <strong>' + lnktEscapeHtml(ord.nhan_vien_phu_trach) + '</strong>');
        }

        let codMeta = lnktBuildStaffInspectCodText(ord);
        let payText = ord.payment_display_label || (pm === 'vietqr' ? 'Chuyển khoản VietQR MB Bank' : 'Thu hộ COD');
        let $inspLink = $row.find('a[onclick^="lnktInspectStaffOrder"]');
        if ($inspLink.length) {
            $inspLink
                .attr('data-payment', payText)
                .attr('data-cod-collect', codMeta.text)
                .attr('data-cod-class', codMeta.cls)
                .attr('data-ship-fee', shipFmt)
                .attr('data-voucher', vouchFmt)
                .attr('data-staff', ord.nhan_vien_phu_trach || 'Chưa tiếp nhận');
            if (ord.ghi_chu) {
                $inspLink.attr('data-note', ord.ghi_chu);
            }
        }
        if ($('#lnkt_inspectCode').text().trim() === '#' + ocode) {
            $('#lnkt_inspectPayment').text(payText);
            if ($('#lnkt_inspectShipFee').length) $('#lnkt_inspectShipFee').text(shipFmt);
            if ($('#lnkt_inspectVoucher').length) $('#lnkt_inspectVoucher').text(vouchFmt);
            $('#lnkt_inspectCodCollect')
                .removeClass('text-success text-danger text-peach')
                .addClass(codMeta.cls)
                .text(codMeta.text);
            if (ord.nhan_vien_phu_trach) {
                $('#lnkt_inspectStaff').text(ord.nhan_vien_phu_trach);
            }
            if (ord.ghi_chu) {
                $('#lnkt_inspectNote').text(ord.ghi_chu);
            }
        }
    }

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

    // MH 10 (Orders): AJAX tiếp nhận đơn và cập nhật tiến trình giao hàng qua OrderController (sp_lnkt_cap_nhat_trang_thai_don_hang)
    window.lnktUpdateStaffOrderStatus = function (orderId, orderCode, overrideStatus) {
        let $select = $('#lnkt_statusSelect_' + orderId);
        let uiStatus = overrideStatus || $select.val();
        if (overrideStatus) {
            $select.val(overrideStatus);
        }
        let $row = $('#lnkt_orderRow_' + orderId);
        let prevUiStatus = $row.attr('data-status') || 'cho_duyet';
        let $saveBtn = $('#lnkt_saveStatusBtn_' + orderId);
        $saveBtn.prop('disabled', true);

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxUpdateOrderStatus',
            type: 'POST',
            dataType: 'json',
            data: {
                order_id:   orderId,
                new_status: uiStatus
            },
            success: function (res) {
                if (!res || res.status !== 'success') {
                    $select.val(prevUiStatus);
                    $saveBtn.prop('disabled', false);
                    lnktShowStaffOrderAlert('error', (res && res.message) ? res.message : 'Không thể cập nhật trạng thái đơn hàng!');
                    return;
                }

                let finalSt = res.new_status || uiStatus;
                let finalPm = $row.attr('data-payment-method') || 'cod';
                let finalIsPaid = (res.trang_thai_thanh_toan === 'da_thanh_toan');
                if (res.order) {
                    lnktSyncExistingStaffRow(res.order);
                } else {
                    $row.attr('data-status', finalSt);
                    if (res.trang_thai_thanh_toan) {
                        $row.attr('data-payment-status', res.trang_thai_thanh_toan);
                    }
                    $select.val(finalSt);
                    $('#lnkt_orderBadgeCell_' + orderId).html(lnktBuildStaffStatusBadgeHtml(finalSt));
                    $('#lnkt_quickActionCell_' + orderId).html(lnktBuildStaffQuickActionHtml(orderId, orderCode, finalSt, finalPm, finalIsPaid));

                    if (finalIsPaid) {
                        $('#lnkt_collectBadge_' + orderId).html('<small class="text-success font-weight-bold">Thu hộ: 0 đ</small>');
                    } else if (finalSt === 'da_huy') {
                        $('#lnkt_collectBadge_' + orderId).html('<small class="text-danger font-weight-bold">Đã hủy (0 đ)</small>');
                    }

                    if (res.handled_by_name) {
                        $('#lnkt_rowStaff_' + orderId).html('Phụ trách: <strong>' + lnktEscapeHtml(res.handled_by_name) + '</strong>');
                        $row.find('a[data-staff]').attr('data-staff', res.handled_by_name);
                        if ($('#lnkt_inspectCode').text().trim() === '#' + orderCode) {
                            $('#lnkt_inspectStaff').text(res.handled_by_name);
                        }
                    }

                    let isLocked = (finalSt === 'hoan_tat' || finalSt === 'da_huy');
                    $select.prop('disabled', isLocked);
                    $saveBtn.prop('disabled', isLocked);
                }

                if (res.counts) {
                    Object.keys(res.counts).forEach(function (k) {
                        $('#lnkt_count_' + k).text(res.counts[k]);
                    });
                }

                lnktShowStaffOrderAlert('success', res.message || ('Đã cập nhật trạng thái đơn #' + orderCode + ' thành công!'));
                window.lnktFilterStaffOrders();
            },
            error: function (xhr) {
                $select.val(prevUiStatus);
                $saveBtn.prop('disabled', false);
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Lỗi ngoại lệ khi cập nhật tiến trình đơn hàng!';
                lnktShowStaffOrderAlert('error', errMsg);
            }
        });
    };

    window.lnktStaffVerifyOrderPayment = function (orderId, btnEl) {
        let $btn = btnEl ? $(btnEl) : $();
        let origHtml = $btn.length ? $btn.html() : '';
        if ($btn.length) {
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang kiểm tra...');
        }

        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxVerifyOrderPayment',
            type: 'POST',
            dataType: 'json',
            data: { order_id: orderId },
            timeout: 15000,
            complete: function () {
                if ($btn.length && document.body.contains($btn[0])) {
                    $btn.prop('disabled', false).html(origHtml);
                }
            },
            success: function (res) {
                if (res && res.order) {
                    lnktSyncExistingStaffRow(res.order);
                }
                if (res && res.counts) {
                    Object.keys(res.counts).forEach(function (k) {
                        $('#lnkt_count_' + k).text(res.counts[k]);
                    });
                }
                window.lnktFilterStaffOrders();
                lnktShowStaffOrderAlert((res && res.paid) ? 'success' : 'error', (res && res.message) ? res.message : 'Đã kiểm tra giao dịch Casso.');
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể kết nối Casso để đối soát đơn #' + orderId;
                lnktShowStaffOrderAlert('error', errMsg);
            }
        });
    };

    window.lnktStaffSyncAllPendingCasso = function (btnEl, isSilent) {
        let $btn = btnEl ? $(btnEl) : $();
        let origHtml = $btn.length ? $btn.html() : '';
        if ($btn.length && !isSilent) {
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang đối soát...');
        }

        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxSyncPendingPayments',
            type: 'GET',
            dataType: 'json',
            timeout: 15000,
            complete: function () {
                if ($btn.length && !isSilent) {
                    $btn.prop('disabled', false).html(origHtml);
                }
            },
            success: function (res) {
                if (!res || res.status !== 'success') return;
                if (res.counts) {
                    Object.keys(res.counts).forEach(function (k) {
                        $('#lnkt_count_' + k).text(res.counts[k]);
                    });
                }
                if (Array.isArray(res.pending_sync_rows)) {
                    res.pending_sync_rows.forEach(function (syncOrd) {
                        lnktSyncExistingStaffRow(syncOrd);
                    });
                }
                window.lnktFilterStaffOrders();
                if (!isSilent || (Array.isArray(res.confirmed_ids) && res.confirmed_ids.length > 0)) {
                    let msg = res.message || 'Đã đồng bộ dữ liệu thanh toán MB Bank.';
                    if (res.api_error && !isSilent) {
                        msg = 'Cảnh báo kết nối Casso: ' + res.api_error;
                    }
                    lnktShowStaffOrderAlert(res.api_error && !isSilent ? 'error' : 'success', msg);
                }
            }
        });
    };

    window.lnktCreateCarrierWaybill = function (orderId, btnEl) {
        let $btn = btnEl ? $(btnEl) : $();
        let origHtml = $btn.length ? $btn.html() : '';
        if ($btn.length) {
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang tạo vận đơn...');
        }

        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxCreateCarrierOrder',
            type: 'POST',
            dataType: 'json',
            data: { order_id: orderId },
            timeout: 15000,
            complete: function () {
                if ($btn.length && document.body.contains($btn[0])) {
                    $btn.prop('disabled', false).html(origHtml);
                }
            },
            success: function (res) {
                if (!res || res.status !== 'success') {
                    lnktShowStaffOrderAlert('error', (res && res.message) ? res.message : 'Không thể đẩy đơn sang GHN.');
                    return;
                }
                if (res.order) {
                    lnktSyncExistingStaffRow(res.order);
                }
                lnktShowStaffOrderAlert('success', res.message || ('Đã tạo vận đơn GHN: ' + (res.tracking_code || '')));
                window.lnktFilterStaffOrders();
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Lỗi tạo mã vận đơn GHN lúc này!';
                lnktShowStaffOrderAlert('error', errMsg);
            }
        });
    };

    window.lnktInspectStaffOrder = function (el) {
        let $el = $(el);
        let oid = parseInt($el.attr('data-order-id'), 10) || 0;
        $('#lnkt_inspectCode').text('#' + $el.attr('data-code'));
        $('#lnkt_inspectReceiver').text($el.attr('data-receiver'));
        $('#lnkt_inspectPhone').text($el.attr('data-phone'));
        $('#lnkt_inspectAddress').text($el.attr('data-address'));
        $('#lnkt_inspectItems').text($el.attr('data-items'));
        $('#lnkt_inspectSubtotal').text($el.attr('data-subtotal'));
        $('#lnkt_inspectDiscount').text($el.attr('data-discount'));
        if ($('#lnkt_inspectShipFee').length) {
            $('#lnkt_inspectShipFee').text($el.attr('data-ship-fee') || 'Miễn phí (0 đ)');
        }
        if ($('#lnkt_inspectVoucher').length) {
            $('#lnkt_inspectVoucher').text($el.attr('data-voucher') || '0 đ');
        }
        $('#lnkt_inspectTotal').text($el.attr('data-total'));
        $('#lnkt_inspectStaff').text($el.attr('data-staff'));
        if ($('#lnkt_inspectPayment').length && $el.attr('data-payment')) {
            $('#lnkt_inspectPayment').text($el.attr('data-payment'));
        }
        if ($('#lnkt_inspectCodCollect').length) {
            let codTxt = $el.attr('data-cod-collect') || '0 đồng';
            let codCls = $el.attr('data-cod-class') || 'text-peach';
            $('#lnkt_inspectCodCollect')
                .removeClass('text-success text-danger text-peach')
                .addClass(codCls)
                .text(codTxt);
        }
        if ($('#lnkt_inspectNote').length && $el.attr('data-note')) {
            $('#lnkt_inspectNote').text($el.attr('data-note'));
        }
        if (oid > 0 && $('#lnkt_btnOpenInspectLabel').length) {
            $('#lnkt_btnOpenInspectLabel').attr('data-order-id', oid);
        }
    };

    window.lnktGenerateBarcodeSvg = function (code) {
        let svgEl = document.getElementById('lnkt_labelBarcodeSvg');
        if (!svgEl) return;
        let text = String(code || 'FKORD000001').trim();
        let bars = [2, 1, 1, 2, 1, 2]; // Start pattern
        for (let i = 0; i < text.length; i++) {
            let ch = text.charCodeAt(i);
            bars.push((ch & 1) ? 2 : 1);
            bars.push(((ch >> 1) & 1) ? 1 : 2);
            bars.push(((ch >> 2) & 1) ? 3 : 1);
            bars.push(((ch >> 3) & 1) ? 1 : 2);
            bars.push(((ch >> 4) & 1) ? 2 : 1);
            bars.push(1);
        }
        bars.push(2, 1, 2, 1, 1, 2); // Stop pattern

        let totalUnits = bars.reduce(function (a, b) { return a + b; }, 0);
        let scale = 232 / Math.max(1, totalUnits);
        let x = 4;
        let rects = '';
        for (let idx = 0; idx < bars.length; idx++) {
            let w = bars[idx] * scale;
            if (idx % 2 === 0) {
                rects += '<rect x="' + x.toFixed(2) + '" y="2" width="' + Math.max(0.8, w).toFixed(2) + '" height="48" fill="#1A202C"></rect>';
            }
            x += w;
        }
        svgEl.innerHTML = rects;
    };

    window.lnktOpenShippingLabelModal = function (orderId) {
        let oid = parseInt(orderId, 10) || 0;
        if (oid <= 0) {
            alert('Vui lòng chọn một đơn hàng hợp lệ để tạo phiếu giao hàng!');
            return;
        }
        let $modal = $('#modalShippingLabel');
        if (!$modal.length) return;

        $('#lnkt_labelLoadingState').removeClass('d-none');
        $('#lnkt_shippingLabelPrintSheet').addClass('d-none');
        $('#lnkt_labelErrorState').addClass('d-none').text('');
        $modal.modal('show');

        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxGetShippingLabel',
            type: 'GET',
            dataType: 'json',
            data: { order_id: oid },
            timeout: 12000,
            success: function (res) {
                $('#lnkt_labelLoadingState').addClass('d-none');
                if (!res || res.status !== 'success' || !res.label) {
                    $('#lnkt_labelErrorState').removeClass('d-none').text((res && res.message) ? res.message : 'Không tải được dữ liệu phiếu giao hàng.');
                    return;
                }
                let lbl = res.label;
                $('#lnkt_shippingLabelPrintSheet').removeClass('d-none');
                $('#lnkt_labelBarcodeText').text(lbl.barcode_value || ('FKORD' + String(oid).padStart(6, '0')));
                window.lnktGenerateBarcodeSvg(lbl.barcode_value || ('FKORD' + oid));
                $('#lnkt_labelWaybillCode').text('#' + (lbl.ma_van_don || ('FK-ORD-' + String(oid).padStart(6, '0'))));
                if ($('#lnkt_labelCarrierRow').length) {
                    if (lbl.carrier_tracking_code) {
                        $('#lnkt_labelCarrierRow').removeClass('d-none');
                        $('#lnkt_labelCarrierCode').text((lbl.carrier_name ? (lbl.carrier_name + ': ') : '') + lbl.carrier_tracking_code);
                    } else {
                        $('#lnkt_labelCarrierRow').addClass('d-none');
                        $('#lnkt_labelCarrierCode').text('--');
                    }
                }

                let $qrImg = $('#lnkt_labelQrImg');
                $qrImg.attr('src', lbl.qr_tracking_img || '').off('error').on('error', function () {
                    $(this).addClass('d-none');
                }).removeClass('d-none');

                $('#lnkt_labelSenderName').text((lbl.sender && lbl.sender.name) ? lbl.sender.name : 'FASHIONKIDS OFFICIAL');
                $('#lnkt_labelSenderPhone').text((lbl.sender && lbl.sender.hotline) ? lbl.sender.hotline : '1900 6868');
                $('#lnkt_labelSenderAddr').text((lbl.sender && lbl.sender.address) ? lbl.sender.address : 'TP. Hồ Chí Minh');
                $('#lnkt_labelCreatedAt').text(lbl.created_at_label || '--');
                $('#lnkt_labelHandledBy').text(lbl.handled_by_name || 'Nhân viên FashionKids');
                $('#lnkt_labelPrintTime').text('In lúc: ' + (lbl.printed_at_label || '--'));

                $('#lnkt_labelRecvName').text((lbl.recipient && lbl.recipient.name) ? lbl.recipient.name : '--');
                $('#lnkt_labelRecvPhone').text((lbl.recipient && lbl.recipient.phone) ? lbl.recipient.phone : '--');
                $('#lnkt_labelRecvAddr').text((lbl.recipient && lbl.recipient.full_address) ? lbl.recipient.full_address : '--');
                $('#lnkt_labelRecvNote').text((lbl.recipient && lbl.recipient.note) ? lbl.recipient.note : 'Không có');

                let items = Array.isArray(lbl.items) ? lbl.items : [];
                let itemsHtml = '';
                items.forEach(function (it, idx) {
                    let qty = parseInt(it.so_luong, 10) || 1;
                    let price = Number(it.don_gia_mua || 0);
                    itemsHtml +=
                        '<tr>' +
                            '<td class="text-center">' + (idx + 1) + '</td>' +
                            '<td class="font-weight-bold">' + lnktEscapeHtml(it.ten_san_pham) + '</td>' +
                            '<td class="text-center">' + lnktEscapeHtml(it.kich_co_chon) + '</td>' +
                            '<td class="text-center font-weight-bold">' + qty + '</td>' +
                            '<td class="text-right">' + lnktFormatVnd(price) + '</td>' +
                            '<td class="text-right font-weight-bold">' + lnktFormatVnd(qty * price) + '</td>' +
                        '</tr>';
                });
                $('#lnkt_labelItemsBody').html(itemsHtml || '<tr><td colspan="6" class="text-center text-muted">Không có sản phẩm</td></tr>');

                $('#lnkt_labelSubtotal').text(lnktFormatVnd(lbl.subtotal || 0));
                $('#lnkt_labelDiscount').text((lbl.discount || 0) > 0 ? ('-' + lnktFormatVnd(lbl.discount)) : '0 đ');
                if ($('#lnkt_labelShippingFee').length) {
                    let sFee = Number(lbl.shipping_fee || 0);
                    $('#lnkt_labelShippingFee').text(sFee > 0 ? lnktFormatVnd(sFee) : 'Miễn phí (0 đ)');
                }
                if ($('#lnkt_labelVoucherRow').length) {
                    let vDisc = Number(lbl.voucher_discount || 0);
                    let vCd = String(lbl.voucher_code || '').trim();
                    if (vDisc > 0) {
                        $('#lnkt_labelVoucherRow').removeClass('d-none');
                        $('#lnkt_labelVoucherLabel').text('Voucher' + (vCd ? (' (' + vCd + ')') : '') + ':');
                        $('#lnkt_labelVoucherDiscount').text('-' + lnktFormatVnd(vDisc));
                    } else {
                        $('#lnkt_labelVoucherRow').addClass('d-none');
                    }
                }
                $('#lnkt_labelTotal').text(lnktFormatVnd(lbl.total_amount || 0));
                $('#lnkt_labelPayMethodName').text(lbl.payment_method_name || '--');

                if (lbl.tx_code) {
                    $('#lnkt_labelTxRow').removeClass('d-none');
                    $('#lnkt_labelTxCode').text(lbl.tx_code + (lbl.paid_at_label ? (' (' + lbl.paid_at_label + ')') : ''));
                } else {
                    $('#lnkt_labelTxRow').addClass('d-none');
                    $('#lnkt_labelTxCode').text('');
                }

                let $statusAlert = $('#lnkt_labelStatusAlert');
                $statusAlert.addClass('d-none').removeClass('alert-danger alert-warning');
                if (lbl.is_cancelled) {
                    $statusAlert.removeClass('d-none').addClass('alert-danger').html(
                        '<i class="fa-solid fa-ban mr-1"></i><strong>CẢNH BÁO:</strong> ' +
                        (lbl.is_expired ? 'Đơn đã tự động hủy do quá hạn thanh toán 24 giờ!' : 'Đơn hàng này đã ở trạng thái Đã hủy!')
                    );
                } else if (lbl.payment_method === 'vietqr' && !lbl.is_paid) {
                    $statusAlert.removeClass('d-none').addClass('alert-warning text-dark').html(
                        '<i class="fa-solid fa-triangle-exclamation mr-1"></i><strong>LƯU Ý:</strong> Đơn VietQR chưa nhận đủ chuyển khoản tại MB Bank — Shop chưa được xác nhận xuất kho.'
                    );
                }

                let $codBox = $('#lnkt_labelCodBox');
                $codBox.removeClass('fk-cod-zero fk-cod-cash fk-cod-cancelled');
                if (lbl.is_cancelled) {
                    $codBox.addClass('fk-cod-cancelled');
                    $('#lnkt_labelCodTitle').text('TRẠNG THÁI THU HỘ:');
                } else if (lbl.is_paid || Number(lbl.cod_collect_amount) <= 0) {
                    $codBox.addClass('fk-cod-zero');
                    $('#lnkt_labelCodTitle').text('SỐ TIỀN CẦN THANH TOÁN KHI GIAO (THU HỘ):');
                } else {
                    $codBox.addClass('fk-cod-cash');
                    $('#lnkt_labelCodTitle').text('TỔNG SỐ TIỀN CẦN THU KHI GIAO HÀNG (COD):');
                }
                $('#lnkt_labelCodAmount').text(lbl.cod_collect_text || '0 đồng');
                $('#lnkt_labelCodInstruction').text(lbl.cod_instruction || '');
            },
            error: function (xhr) {
                $('#lnkt_labelLoadingState').addClass('d-none');
                let msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể tạo phiếu giao hàng lúc này.';
                $('#lnkt_labelErrorState').removeClass('d-none').text(msg);
            }
        });
    };

    window.lnktPrintShippingLabel = function () {
        let $sheet = $('#lnkt_shippingLabelPrintSheet');
        if (!$sheet.length || $sheet.hasClass('d-none')) {
            return;
        }
        let stage = document.getElementById('fk_printStageContainer');
        if (!stage) {
            stage = document.createElement('div');
            stage.id = 'fk_printStageContainer';
            document.body.appendChild(stage);
        }
        stage.innerHTML = '';
        stage.appendChild($sheet[0].cloneNode(true));
        document.body.classList.add('fk-printing-label');

        let cleaned = false;
        let cleanup = function () {
            if (cleaned) return;
            cleaned = true;
            document.body.classList.remove('fk-printing-label');
            if (stage) stage.innerHTML = '';
            window.removeEventListener('afterprint', cleanup);
        };
        window.addEventListener('afterprint', cleanup);
        setTimeout(function () {
            window.print();
            setTimeout(cleanup, 1200);
        }, 60);
    };

    // Polling đơn hàng mới mỗi 4 giây tại Cổng Nhân viên (staff-orders.php)
    if ($('#lnkt_staff_orders_section').length) {
        function lnktBuildStaffOrderRowHtml(ord) {
            let oid = parseInt(ord.ma_don_hang, 10) || 0;
            let ocode = ord.ma_van_don || ('FK-ORD-' + String(oid).padStart(6, '0'));
            let st = ord.trang_thai_don_hang || 'cho_duyet';
            let pm = (ord.phuong_thuc_thanh_toan || 'cod').toLowerCase();
            let ps = (ord.trang_thai_thanh_toan || 'chua_thanh_toan').toLowerCase();
            let isPaid = (!!ord.is_paid || ps === 'da_thanh_toan');
            let lockConfirm = (st === 'cho_duyet' && pm === 'vietqr' && !isPaid);
            let fullAddr = [ord.dia_chi_giao_hang, ord.phuong_xa, ord.quan_huyen, ord.tinh_thanh_pho].filter(Boolean).join(', ');
            let wardDistrict = [ord.phuong_xa, ord.quan_huyen].filter(Boolean).join(', ');
            let createdStr = lnktFormatMysqlDt(ord.ngay_dat_hang);
            let handledBy = ord.nhan_vien_phu_trach || 'Chưa tiếp nhận';
            let noteStr = ord.ghi_chu || '';
            let payLabel = ord.payment_display_label || ((pm === 'vietqr' ? 'Chuyển khoản VietQR MB Bank' : 'Thu hộ COD'));
            let codMeta = lnktBuildStaffInspectCodText(ord);
            let subtotalF = lnktFormatVnd(ord.tien_hang_tam_tinh || 0);
            let discVal = Number(ord.tien_giam_gia || 0);
            let discountF = discVal > 0 ? ('-' + lnktFormatVnd(discVal)) : '0 đ';
            let shipVal = Number(ord.phi_van_chuyen || 0);
            let shipF = shipVal > 0 ? lnktFormatVnd(shipVal) : 'Miễn phí (0 đ)';
            let vouchVal = Number(ord.tien_giam_voucher || 0);
            let vouchCode = String(ord.ma_voucher || '').trim();
            let voucherF = vouchVal > 0 ? ('-' + lnktFormatVnd(vouchVal) + (vouchCode !== '' ? (' (' + vouchCode + ')') : '')) : '0 đ';
            let carrierCode = String(ord.ma_van_don_hang_vc || '').trim();
            let carrierName = String(ord.don_vi_van_chuyen || 'GHN').trim();
            let totalF = lnktFormatVnd(ord.tong_tien_thanh_toan || 0);
            let isLocked = (st === 'hoan_tat' || st === 'da_huy');
            let safeJsCode = String(ocode).replace(/\\/g, '\\\\').replace(/'/g, "\\'");

            return (
                '<tr class="lnkt-staff-order-row lnkt-order-highlight-row" id="lnkt_orderRow_' + oid + '" data-order-id="' + oid + '" data-status="' + lnktEscapeHtml(st) + '" data-payment-method="' + lnktEscapeHtml(pm) + '" data-payment-status="' + lnktEscapeHtml(ps) + '" data-search="' + lnktEscapeHtml(ocode + ' ' + oid + ' ' + ord.ten_nguoi_nhan + ' ' + ord.so_dien_thoai_nhan + ' ' + (ord.tinh_thanh_pho || '') + ' ' + noteStr) + '">' +
                    '<td>' +
                        '<a href="javascript:void(0)" onclick="lnktInspectStaffOrder(this)" ' +
                           'data-order-id="' + oid + '" ' +
                           'data-code="' + lnktEscapeHtml(ocode) + '" ' +
                           'data-receiver="' + lnktEscapeHtml(ord.ten_nguoi_nhan) + '" ' +
                           'data-phone="' + lnktEscapeHtml(ord.so_dien_thoai_nhan) + '" ' +
                           'data-address="' + lnktEscapeHtml(fullAddr) + '" ' +
                           'data-items="' + lnktEscapeHtml(ord.item_summary || 'Sản phẩm FashionKids') + '" ' +
                           'data-subtotal="' + lnktEscapeHtml(subtotalF) + '" ' +
                           'data-discount="' + lnktEscapeHtml(discountF) + '" ' +
                           'data-ship-fee="' + lnktEscapeHtml(shipF) + '" ' +
                           'data-voucher="' + lnktEscapeHtml(voucherF) + '" ' +
                           'data-total="' + lnktEscapeHtml(totalF) + '" ' +
                           'data-staff="' + lnktEscapeHtml(handledBy) + '" ' +
                           'data-payment="' + lnktEscapeHtml(payLabel) + '" ' +
                           'data-cod-collect="' + lnktEscapeHtml(codMeta.text) + '" ' +
                           'data-cod-class="' + lnktEscapeHtml(codMeta.cls) + '" ' +
                           'data-note="' + lnktEscapeHtml(noteStr || 'Không có ghi chú thêm.') + '" ' +
                           'class="font-weight-bold text-peach text-decoration-none d-block">#' + lnktEscapeHtml(ocode) + '</a>' +
                        '<small class="text-muted-custom d-block">' + lnktEscapeHtml(createdStr) + '</small>' +
                        '<small class="text-slate" id="lnkt_rowStaff_' + oid + '">Phụ trách: <strong>' + lnktEscapeHtml(handledBy) + '</strong></small>' +
                    '</td>' +
                    '<td>' +
                        '<div class="font-weight-bold text-slate">' + lnktEscapeHtml(ord.ten_nguoi_nhan) + '</div>' +
                        '<small class="text-muted-custom d-block"><i class="fa-solid fa-phone mr-1"></i>' + lnktEscapeHtml(ord.so_dien_thoai_nhan) + '</small>' +
                        '<div id="lnkt_payBadgeWrap_' + oid + '" class="mt-1">' + lnktRenderStaffPaymentBadgesHtml(ord) + '</div>' +
                    '</td>' +
                    '<td>' +
                        '<div class="small font-weight-bold text-slate">' + lnktEscapeHtml(ord.tinh_thanh_pho || '') + '</div>' +
                        '<small class="text-muted-custom d-block">' + lnktEscapeHtml(wardDistrict) + '</small>' +
                        '<div id="lnkt_carrierBadgeWrap_' + oid + '">' + (carrierCode ? '<span class="badge badge-light border text-info small py-0 px-2 rounded-pill mt-1 text-nowrap"><i class="fa-solid fa-barcode mr-1"></i>' + lnktEscapeHtml(carrierName + ': ' + carrierCode) + '</span>' : '') + '</div>' +
                        (noteStr ? '<small class="text-info d-block text-truncate fk-ellipsis-220" title="' + lnktEscapeHtml(noteStr) + '"><i class="fa-regular fa-note-sticky mr-1"></i>' + lnktEscapeHtml(noteStr) + '</small>' : '') +
                    '</td>' +
                    '<td class="text-right text-nowrap">' +
                        '<div class="font-weight-bold text-slate">' + lnktEscapeHtml(totalF) + '</div>' +
                        '<div id="lnkt_collectBadge_' + oid + '">' + lnktRenderStaffCollectHtml(ord) + '</div>' +
                    '</td>' +
                    '<td class="text-center" id="lnkt_orderBadgeCell_' + oid + '">' + lnktBuildStaffStatusBadgeHtml(st) + '</td>' +
                    '<td class="text-center">' +
                        '<div class="input-group input-group-sm mb-1">' +
                            '<select id="lnkt_statusSelect_' + oid + '" class="custom-select font-weight-bold" ' + (isLocked ? 'disabled' : '') + '>' +
                                '<option value="cho_duyet"' + (st === 'cho_duyet' ? ' selected' : '') + '>Chờ duyệt</option>' +
                                '<option value="da_xac_nhan"' + (st === 'da_xac_nhan' ? ' selected' : '') + (lockConfirm ? ' disabled' : '') + '>Đã xác nhận' + (lockConfirm ? ' (Chờ TT)' : '') + '</option>' +
                                '<option value="dang_giao"' + (st === 'dang_giao' ? ' selected' : '') + (lockConfirm ? ' disabled' : '') + '>Đang giao</option>' +
                                '<option value="hoan_tat"' + (st === 'hoan_tat' ? ' selected' : '') + (lockConfirm ? ' disabled' : '') + '>Hoàn tất</option>' +
                                '<option value="da_huy"' + (st === 'da_huy' ? ' selected' : '') + '>Đã hủy</option>' +
                            '</select>' +
                            '<div class="input-group-append">' +
                                '<button type="button" id="lnkt_saveStatusBtn_' + oid + '" class="btn btn-pastel-primary px-2" ' + (isLocked ? 'disabled' : '') + ' onclick="lnktUpdateStaffOrderStatus(' + oid + ', \'' + lnktEscapeHtml(safeJsCode) + '\')"><i class="fa-solid fa-check"></i> Lưu</button>' +
                            '</div>' +
                        '</div>' +
                        '<div id="lnkt_quickActionCell_' + oid + '">' + lnktBuildStaffQuickActionHtml(oid, ocode, st, pm, isPaid, carrierCode) + '</div>' +
                        '<button type="button" class="btn btn-light btn-sm btn-block border rounded-pill py-0 small font-weight-bold text-slate" onclick="lnktOpenShippingLabelModal(' + oid + ')"><i class="fa-solid fa-barcode text-peach mr-1"></i>In tem giao hàng</button>' +
                    '</td>' +
                '</tr>'
            );
        }

        setInterval(function () {
            let lastId = parseInt($('#lnkt_staff_orders_section').attr('data-max-order-id'), 10) || 0;
            $.ajax({
                url: '/FashionKids/index.php?c=order&a=ajaxCheckNewOrders',
                type: 'GET',
                dataType: 'json',
                data: { last_id: lastId },
                timeout: 4500,
                success: function (res) {
                    if (!res || res.status !== 'success') return;
                    if (res.counts) {
                        Object.keys(res.counts).forEach(function (k) {
                            $('#lnkt_count_' + k).text(res.counts[k]);
                        });
                    }
                    if (res.max_order_id && res.max_order_id > lastId) {
                        $('#lnkt_staff_orders_section').attr('data-max-order-id', res.max_order_id);
                    }
                    let syncRows = res.pending_sync_rows || (res.data && res.data.pending_sync_rows) || [];
                    if (Array.isArray(syncRows) && syncRows.length > 0) {
                        syncRows.forEach(function (syncOrd) {
                            lnktSyncExistingStaffRow(syncOrd);
                        });
                    }
                    if (Array.isArray(res.new_orders) && res.new_orders.length > 0) {
                        $('#lnkt_staffEmptyRow').remove();
                        let addedCount = 0;
                        res.new_orders.slice().reverse().forEach(function (ord) {
                            let oid = parseInt(ord.ma_don_hang, 10) || 0;
                            if (oid > 0 && $('#lnkt_orderRow_' + oid).length === 0) {
                                $('#lnkt_staffOrdersBody').prepend(lnktBuildStaffOrderRowHtml(ord));
                                addedCount++;
                            }
                        });
                        if (addedCount > 0) {
                            let curBadge = parseInt($('#new_order_count').text(), 10) || 0;
                            $('#new_order_count').text(curBadge + addedCount);
                            $('#new_order_badge').removeClass('d-none');
                            lnktShowStaffOrderAlert('success', 'Có ' + addedCount + ' đơn hàng mới vừa được phụ huynh đặt!');
                            window.lnktPlayNewOrderChime();
                        }
                    }
                    window.lnktFilterStaffOrders();
                }
            });
        }, 4000);

        // Đồng bộ ngầm Casso mỗi 10 giây nếu còn đơn VietQR chờ thanh toán trên bảng Nhân viên
        setInterval(function () {
            let hasPendingVietQr = $('.lnkt-staff-order-row[data-payment-method="vietqr"][data-status="cho_duyet"]').filter(function () {
                return $(this).attr('data-payment-status') !== 'da_thanh_toan';
            }).length > 0;
            if (hasPendingVietQr) {
                window.lnktStaffSyncAllPendingCasso(null, true);
            }
        }, 10000);
    }

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
        $('#lnkt_sizeFormTitle').html('<i class="fa-solid fa-sliders text-peach mr-1"></i> Cấu Hình Thông Số Bảng Size');
        $('#lnkt_btnCancelSizeEdit').addClass('d-none');
    };

    // MH 14 (Admin Only): AJAX lưu cấu hình Bảng kích cỡ chuẩn (lnkt_bang_kich_co)
    $('#lnkt_formAdminSizeChart').on('submit', function (e) {
        e.preventDefault();
        let scId     = $('#lnkt_scId').val() || '';
        let catId    = parseInt($('#lnkt_scCategoryId').val(), 10) || 1;
        let typeVal  = $('#lnkt_scType').val() || (catId === 4 ? 'giay_dep' : 'quan_ao');
        let sizeName = ($('#lnkt_scSizeName').val() || '').trim();
        let minVal   = (typeVal === 'giay_dep')
            ? parseFloat($('#lnkt_scFootMin').val() || 12)
            : parseFloat($('#lnkt_scHeightMin').val() || 75);
        let maxVal   = (typeVal === 'giay_dep')
            ? parseFloat($('#lnkt_scFootMax').val() || 13.5)
            : parseFloat($('#lnkt_scHeightMax').val() || 89);
        let chestMax = $('#lnkt_scChestMax').val() || '';
        let waistMax = $('#lnkt_scWaistMax').val() || '';

        if (!sizeName) {
            alert('Vui lòng nhập tên kích cỡ (VD: Size 2 (2-3T))!');
            return;
        }
        if (isNaN(minVal) || isNaN(maxVal) || minVal <= 0 || minVal >= maxVal) {
            alert('Khoảng số đo tối thiểu và tối đa không hợp lệ!');
            return;
        }

        $.ajax({
            url: '/FashionKids/index.php?c=admin&a=ajaxSaveSizeChart',
            type: 'POST',
            dataType: 'json',
            data: {
                size_id:     scId,
                category_id: catId,
                size_type:   typeVal,
                size_label:  sizeName,
                min_val:     minVal,
                max_val:     maxVal,
                chest_max:   chestMax,
                waist_max:   waistMax
            },
            success: function (res) {
                alert((res && res.message) ? res.message : ('Đã lưu cấu hình thông số kích cỡ "' + sizeName + '" thành công!'));
                window.lnktResetSizeChartForm();
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể lưu cấu hình bảng kích cỡ!';
                alert(errMsg);
            }
        });
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
            let card = $('<div class="p-2 rounded border bg-milk-cream text-center small"></div>');
            let objectUrl = (window.URL && URL.createObjectURL && file.type && file.type.indexOf('image/') === 0)
                ? URL.createObjectURL(file)
                : '';

            if (objectUrl) {
                let imgEl = $('<img>')
                    .attr('src', objectUrl)
                    .attr('alt', file.name || ('Ảnh ứng tuyển ' + (idx + 1)))
                    .addClass('img-fluid rounded mb-1 fk-kol-preview-img');
                card.append(imgEl);
            } else {
                card.append('<i class="fa-solid fa-image text-peach h4 mb-1 d-block"></i>');
            }

            let nameEl = $('<span class="font-weight-bold text-slate d-block text-truncate"></span>')
                .text((idx + 1) + '. ' + (file.name || 'Anh_be.jpg'));
            let sizeEl = $('<small class="text-muted-custom"></small>')
                .text(Math.round((file.size || 0) / 1024) + ' KB');

            card.append(nameEl, sizeEl);
            col.append(card);
            previewContainer.append(col);
        });
    };

    window.lnktUseDemoKolPhotos = function () {
        let inputEl = document.getElementById('lnkt_kolMediaInput');
        if (inputEl) {
            inputEl.value = '';
        }
        $('#lnkt_kolPhotoCount').val(3);
        $('#lnkt_kolMediaStatusTitle').html(
            '<span class="text-success"><i class="fa-solid fa-circle-check mr-1"></i>Đã đính kèm 3 ảnh mẫu chân dung & toàn thân hợp lệ (2 - 5 ảnh)</span>'
        );
        const demoItems = [
            { src: '/FashionKids/images/kol/kol-bao-sam.jpg', title: '1. Ảnh chân dung bé', tag: 'Ảnh chân dung', alt: 'Chân dung bé Sam' },
            { src: '/FashionKids/images/kol/kol-thien-than-hoa-cuc.jpg', title: '2. Ảnh toàn thân trang phục', tag: 'Ảnh toàn thân', alt: 'Toàn thân váy hoa cúc' },
            { src: '/FashionKids/images/kol/kol-casting-studio-demo.jpg', title: '3. Ảnh góc nghiêng tự nhiên', tag: 'Góc tự nhiên', alt: 'Ảnh casting studio tự nhiên' }
        ];
        let previewContainer = $('#lnkt_kolMediaPreviewList');
        if (previewContainer.length) {
            previewContainer.empty();
            demoItems.forEach(function (item) {
                let col = $('<div class="col-4 px-1 mb-2"></div>');
                col.html(
                    '<div class="p-2 rounded border bg-milk-cream text-center small">' +
                    '<img src="' + item.src + '" alt="' + item.alt + '" class="img-fluid rounded mb-1 fk-kol-preview-img">' +
                    '<span class="font-weight-bold text-slate d-block text-truncate">' + item.title + '</span>' +
                    '<small class="text-success">' + item.tag + '</small>' +
                    '</div>'
                );
                previewContainer.append(col);
            });
        }
    };

    $('#lnkt_formKolCasting').off('submit').on('submit', function (e) {
        e.preventDefault();
        let role = $('body').attr('data-user-role') || 'guest';
        if (role === 'guest') {
            if (confirm('Vui lòng đăng nhập tài khoản thành viên để nộp hồ sơ xét tuyển KOL Nhí!')) {
                window.location.href = '/FashionKids/views/pages/login-register.php?redirect=' + encodeURIComponent('/FashionKids/views/pages/kol-casting.php');
            }
            return;
        }

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

        let $form = $(this);
        let $btn = $form.find('button[type="submit"]');
        let origBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang gửi hồ sơ...');

        let formData = new FormData(this);
        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxSubmitKolCasting',
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            complete: function () {
                $btn.prop('disabled', false).html(origBtnHtml);
            },
            success: function (res) {
                alert(
                    (res && res.message) ? res.message : (
                        'Nộp hồ sơ Casting KOL Nhí thành công!\n' +
                        '• Bé ứng tuyển: ' + babyName + (autoAge ? ' (' + autoAge + ')' : '') + '\n' +
                        '• Số lượng ảnh đính kèm: ' + photoCount + ' ảnh\n' +
                        '• Cam kết người giám hộ: Đã xác nhận hợp lệ.'
                    )
                );
            },
            error: function (xhr) {
                if (window.turnstile && typeof window.turnstile.reset === 'function') {
                    try { window.turnstile.reset(); } catch (e) {}
                }
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể gửi hồ sơ lúc này. Vui lòng thử lại!';
                alert(errMsg);
            }
        });
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
            '• Liên kết đích: ' + targetUrl + '\n' +
            '• Lịch hiển thị: ' + startTime + ' -> ' + endTime
        );
    });
});



/**
 * ============================================================================
 * PHÂN HỆ ĐẢM NHIỆM: TẠP CHÍ & BÁO CHÍ REALTIME, LIVE CHAT & DUYỆT KOL NHÍ
 * Đồng bộ CSDL: tgb_bai_viet_tin_tuc, tgb_binh_luan_bai_viet,
 *               tgb_tin_nhan_tu_van, lnkt_banner_su_kien, lnkt_dang_ky_kol_nhi
 * ============================================================================
 */
$(document).ready(function () {
    function fkEsc(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ------------------------------------------------------------------------
    // 1. LIVE PRESS TICKER & EVENT COUNTDOWN REALTIME (blog-feeds.php & home.php)
    // ------------------------------------------------------------------------
    const fkPressHeadlines = [
        '[Mới cập nhật] FashionKids chính thức nhận Chứng nhận Quốc tế OEKO-TEX® Standard 100 Hạng I cho dòng Vải Sợi Tre Mùa Hè 2026!',
        '[Sự kiện KOL Nhí] Hơn 120 hồ sơ thiên thần nhí đã đăng ký tham gia vòng Casting Lookbook Studio tháng 11/2026!',
        '[Báo chí Tiêu dùng] Vải sợi tre kháng khuẩn Bamboo Kun giúp hạ nhiệt độ bề mặt da bé tới -2°C trong mùa nắng nóng.',
        '[Ưu đãi Thành viên VIP] Tích điểm tự động nâng hạng Vàng (-10%) và Kim Cương (-15%) trên mọi đơn hàng!'
    ];
    let fkTickerIdx = 0;
    if ($('#fk_livePressTickerText').length) {
        setInterval(function () {
            fkTickerIdx = (fkTickerIdx + 1) % fkPressHeadlines.length;
            $('#fk_livePressTickerText').fadeOut(200, function () {
                $(this).text(fkPressHeadlines[fkTickerIdx]).fadeIn(250);
            });
        }, 5000);
    }

    // Đồng bộ Carousel Banner Tạp Chí & Thumbnail Switcher (blog-feeds.php)
    if ($('#fk_magHeroCarousel').length) {
        let $magCarousel = $('#fk_magHeroCarousel');
        $magCarousel.carousel({ interval: 4600, pause: 'hover', ride: 'carousel' });
        $magCarousel.on('slide.bs.carousel', function (e) {
            let nextIdx = typeof e.to === 'number' ? e.to : 0;
            $('.fk-mag-thumb-item').removeClass('active').eq(nextIdx).addClass('active');
        });
        $('.fk-mag-thumb-item').on('click', function () {
            let idx = parseInt($(this).attr('data-slide-to'), 10) || 0;
            $magCarousel.carousel(idx);
        });
    }

    window.fkCopyMagVoucher = function (code, btnEl) {
        let text = code || 'FASHION10';
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).catch(function () {});
        }
        if (btnEl) {
            let $b = $(btnEl);
            let origHtml = $b.data('orig-html') || $b.html();
            $b.data('orig-html', origHtml);
            $b.html('<i class="fa-solid fa-check text-success"></i> Đã sao chép mã <strong>' + text + '</strong>');
            setTimeout(function () {
                $b.html(origHtml);
            }, 2200);
        }
        if (typeof fkShowToast === 'function') {
            fkShowToast('Đã sao chép mã ưu đãi ' + text + ' (Giảm 10% đơn từ 200K)!', 'success');
        }
    };

    // Bộ lọc & Modal Hồ sơ Chi tiết Danh sách Sao Nhí (kol-stars.php)
    window.fkActiveKolFilter = 'all';
    window.fkFilterKolStars = function (filterKey, btnEl) {
        window.fkActiveKolFilter = filterKey || 'all';
        $('#fk_kolFilterGroup .fk-blog-filter-btn').removeClass('active');
        if (btnEl) $(btnEl).addClass('active');
        window.fkApplyKolSearch();
    };

    window.fkApplyKolSearch = function () {
        let $cols = $('#fk_kolStarsGrid .fk-kol-star-col');
        if (!$cols.length) return;
        let kw = ($('#fk_kolSearchInput').val() || '').toLowerCase().trim();
        let filterKey = window.fkActiveKolFilter || 'all';
        let visibleCount = 0;

        $cols.each(function () {
            let $el = $(this);
            let st = $el.attr('data-status') || '';
            let gd = $el.attr('data-gender') || '';
            let blob = ($el.attr('data-search') || '').toLowerCase();

            let matchFilter = (filterKey === 'all')
                || (filterKey === st)
                || (filterKey === gd);
            let matchSearch = (!kw || blob.indexOf(kw) !== -1);

            if (matchFilter && matchSearch) {
                $el.removeClass('d-none');
                visibleCount++;
            } else {
                $el.addClass('d-none');
            }
        });

        if (visibleCount === 0) {
            $('#fk_kolEmptyAlert').removeClass('d-none');
        } else {
            $('#fk_kolEmptyAlert').addClass('d-none');
        }
    };

    window.fkResetKolFilters = function () {
        $('#fk_kolSearchInput').val('');
        let $allBtn = $('#fk_kolFilterGroup [data-filter="all"]');
        window.fkFilterKolStars('all', $allBtn.get(0));
    };

    window.fkOpenKolStarModal = function (btnEl) {
        if (!btnEl) return;
        let $b = $(btnEl);
        let photos = [];
        try {
            photos = JSON.parse($b.attr('data-photos') || '[]');
        } catch (e) {
            photos = [];
        }
        if (!photos.length) {
            photos = ['/FashionKids/images/kol/kol-bao-sam.jpg'];
        }

        $('#fk_kolModalName').text($b.attr('data-name') || 'Siêu Sao Nhí');
        $('#fk_kolModalCode').text($b.attr('data-code') || '');
        $('#fk_kolModalStatus')
            .attr('class', 'badge badge-pill px-3 py-1 ' + ($b.attr('data-badge') || 'badge-mint'))
            .text($b.attr('data-status') || 'Gương mặt nổi bật');
        $('#fk_kolModalAgeGender').text(($b.attr('data-age') || '') + ' • ' + ($b.attr('data-gender') || ''));
        $('#fk_kolModalSize').text($b.attr('data-size') || '');
        $('#fk_kolModalHeight').text(($b.attr('data-height') || '') + ' cm');
        $('#fk_kolModalWeight').text(($b.attr('data-weight') || '') + ' kg');
        $('#fk_kolModalTalent').text($b.attr('data-talent') || '');
        $('#fk_kolModalGuardian').text($b.attr('data-guardian') || 'Phụ huynh đồng hành');
        $('#fk_kolModalMainPhoto').attr('src', photos[0]);

        let $thumbs = $('#fk_kolModalThumbs').empty();
        if (photos.length > 1) {
            photos.forEach(function (src, idx) {
                let $btn = $('<button type="button" class="fk-kol-modal-thumb ' + (idx === 0 ? 'active' : '') + '"></button>');
                $btn.append($('<img>').attr('src', src).attr('alt', 'Lookbook photo ' + (idx + 1)));
                $btn.on('click', function () {
                    $('#fk_kolModalMainPhoto').attr('src', src);
                    $thumbs.find('.fk-kol-modal-thumb').removeClass('active');
                    $btn.addClass('active');
                });
                $thumbs.append($btn);
            });
        }

        $('#fk_modalKolStarDetail').modal('show');
    };

    // Đồng hồ đếm ngược Lịch Sự Kiện Realtime trên Trang chủ
    if ($('#fk_homeLiveCountdown').length) {
        let remainingSec = (5 * 86400) + (14 * 3600) + (28 * 60) + 45;
        setInterval(function () {
            if (remainingSec > 0) remainingSec--;
            let d = Math.floor(remainingSec / 86400);
            let h = Math.floor((remainingSec % 86400) / 3600);
            let m = Math.floor((remainingSec % 3600) / 60);
            let s = remainingSec % 60;
            let pad = function (n) { return n < 10 ? '0' + n : String(n); };
            $('#fk_homeLiveCountdown').text(pad(d) + ' Ngày : ' + pad(h) + ' Giờ : ' + pad(m) + ' Phút : ' + pad(s) + ' Giây');
        }, 1000);
    }

    // ------------------------------------------------------------------------
    // ------------------------------------------------------------------------
    // 2. MH 18: LỌC CHUYÊN MỤC, TÌM KIẾM, PHÂN TRANG 6 BÀI/TRANG, THẢ TIM & BOOKMARK (blog-feeds.php)
    // ------------------------------------------------------------------------
    window.fkOnlyBookmarkedMode = false;
    const FK_BLOG_PER_PAGE = 6;

    window.fkSelectBlogCategory = function (catKey, btnEl) {
        window.fkOnlyBookmarkedMode = false;
        $('#fk_btnShowBookmarks').removeClass('btn-pastel-primary').addClass('btn-light');
        $('#fk_blogCategoryFilterGroup .fk-blog-filter-btn').removeClass('active');
        $(btnEl).addClass('active');
        $('#fk_activeBlogCategory').val(catKey);
        window.fkApplyBlogFilters(1);
    };

    window.fkFilterSavedBookmarks = function () {
        window.fkOnlyBookmarkedMode = !window.fkOnlyBookmarkedMode;
        if (window.fkOnlyBookmarkedMode) {
            $('#fk_btnShowBookmarks').removeClass('btn-light').addClass('btn-pastel-primary');
        } else {
            $('#fk_btnShowBookmarks').removeClass('btn-pastel-primary').addClass('btn-light');
        }
        window.fkApplyBlogFilters(1);
    };

    window.fkGoToBlogPage = function (pageNum) {
        window.fkApplyBlogFilters(pageNum, true);
    };

    window.fkApplyBlogFilters = function (targetPage, shouldScroll) {
        let activeCat = $('#fk_activeBlogCategory').val() || 'all';
        let keyword = ($('#fk_blogSearchInput').val() || '').toLowerCase().trim();
        let sortBy = $('#fk_blogSortSelect').val() || 'newest';

        let $grid = $('#fk_blogFeedGrid');
        if (!$grid.length) return;

        let cols = $grid.children('.fk-blog-col').get();

        // Sắp xếp toàn bộ bài viết trước khi lọc & chia trang
        cols.sort(function (a, b) {
            let idA = parseInt($(a).attr('data-id'), 10) || 0;
            let idB = parseInt($(b).attr('data-id'), 10) || 0;
            let viewsA = parseInt($(a).attr('data-views'), 10) || 0;
            let viewsB = parseInt($(b).attr('data-views'), 10) || 0;
            let likesA = parseInt($(a).attr('data-likes'), 10) || 0;
            let likesB = parseInt($(b).attr('data-likes'), 10) || 0;

            if (sortBy === 'views') return viewsB - viewsA;
            if (sortBy === 'likes') return likesB - likesA;

            let isCustomA = (idA > 14) ? 1 : 0;
            let isCustomB = (idB > 14) ? 1 : 0;
            if (isCustomA !== isCustomB) return isCustomB - isCustomA;
            if (isCustomA === 1) return idB - idA;
            return idA - idB;
        });

        $.each(cols, function (i, el) {
            $grid.append(el);
        });

        // Lọc danh sách bài viết khớp điều kiện
        let matchedCols = [];
        cols.forEach(function (col) {
            let $col = $(col);
            let itemCat = $col.attr('data-category') || 'meo_phoi_do';
            let itemSearch = ($col.attr('data-search') || '').toLowerCase();
            let itemSaved = $col.attr('data-bookmarked') === '1';

            let matchCat = (activeCat === 'all' || itemCat === activeCat);
            let matchKw = (!keyword || itemSearch.indexOf(keyword) !== -1);
            let matchSaved = (!window.fkOnlyBookmarkedMode || itemSaved);

            if (matchCat && matchKw && matchSaved) {
                matchedCols.push($col);
            } else {
                $col.addClass('d-none');
            }
        });

        let totalMatched = matchedCols.length;
        let totalPages = Math.max(1, Math.ceil(totalMatched / FK_BLOG_PER_PAGE));
        let curPage = parseInt(targetPage, 10);
        if (isNaN(curPage) || curPage < 1) {
            curPage = parseInt($('#fk_currentBlogPage').val(), 10) || 1;
        }
        if (curPage > totalPages) curPage = totalPages;
        if (curPage < 1) curPage = 1;
        $('#fk_currentBlogPage').val(curPage);

        let startIdx = (curPage - 1) * FK_BLOG_PER_PAGE;
        let endIdx = Math.min(totalMatched, startIdx + FK_BLOG_PER_PAGE);

        // Chỉ hiển thị tối đa 6 bài viết của trang hiện tại
        matchedCols.forEach(function ($col, idx) {
            if (idx >= startIdx && idx < endIdx) {
                $col.removeClass('d-none');
            } else {
                $col.addClass('d-none');
            }
        });

        // Cập nhật bộ đếm & chỉ báo trang
        $('#fk_blogVisibleCount').text(totalMatched);
        if (totalMatched === 0) {
            $('#fk_blogPageRangeText').text('0');
            $('#fk_blogPageIndicator').text('Trang 0 / 0');
            $('#fk_emptyBlogAlert').removeClass('d-none');
            $('#fk_blogPaginationWrapper').addClass('d-none');
        } else {
            $('#fk_blogPageRangeText').text((startIdx + 1) + '–' + endIdx);
            $('#fk_blogPageIndicator').text('Trang ' + curPage + ' / ' + totalPages + ' • 6 bài/trang');
            $('#fk_blogPaginationSummaryText').text('Trang ' + curPage + ' / ' + totalPages + ' (Hiển thị bài ' + (startIdx + 1) + '–' + endIdx + ' trên tổng ' + totalMatched + ' bài)');
            $('#fk_emptyBlogAlert').addClass('d-none');

            // Render lại các nút Trang 1, 2, 3...
            let $pageList = $('#fk_blogPaginationList');
            if ($pageList.length) {
                let prevDisabled = (curPage <= 1) ? 'disabled' : '';
                let nextDisabled = (curPage >= totalPages) ? 'disabled' : '';
                let html =
                    '<li class="page-item ' + prevDisabled + '">' +
                        '<button type="button" class="page-link fk-blog-page-btn" onclick="fkGoToBlogPage(' + Math.max(1, curPage - 1) + ')">' +
                            '<i class="fa-solid fa-chevron-left mr-1"></i>Trước' +
                        '</button>' +
                    '</li>';

                for (let p = 1; p <= totalPages; p++) {
                    let activeCls = (p === curPage) ? 'active' : '';
                    html +=
                        '<li class="page-item ' + activeCls + '">' +
                            '<button type="button" class="page-link fk-blog-page-btn" onclick="fkGoToBlogPage(' + p + ')">' +
                                'Trang ' + p +
                            '</button>' +
                        '</li>';
                }

                html +=
                    '<li class="page-item ' + nextDisabled + '">' +
                        '<button type="button" class="page-link fk-blog-page-btn" onclick="fkGoToBlogPage(' + Math.min(totalPages, curPage + 1) + ')">' +
                            'Tiếp<i class="fa-solid fa-chevron-right ml-1"></i>' +
                        '</button>' +
                    '</li>';

                $pageList.html(html);
            }

            if (totalPages > 1) {
                $('#fk_blogPaginationWrapper').removeClass('d-none');
            } else {
                $('#fk_blogPaginationWrapper').addClass('d-none');
            }
        }

        // Cập nhật URL query ?page=... mà không reload trang
        if (window.history && window.history.replaceState) {
            let url = new URL(window.location.href);
            if (curPage > 1) {
                url.searchParams.set('page', String(curPage));
            } else {
                url.searchParams.delete('page');
            }
            window.history.replaceState({}, '', url.toString());
        }

        if (shouldScroll && $('#fk_blogCategoryFilterGroup').length) {
            $('html, body').animate({
                scrollTop: $('#fk_blogCategoryFilterGroup').offset().top - 110
            }, 250);
        }
    };

    window.fkResetBlogFilters = function () {
        window.fkOnlyBookmarkedMode = false;
        $('#fk_btnShowBookmarks').removeClass('btn-pastel-primary').addClass('btn-light');
        $('#fk_activeBlogCategory').val('all');
        $('#fk_blogCategoryFilterGroup .fk-blog-filter-btn').removeClass('active');
        $('#fk_blogCategoryFilterGroup .fk-blog-filter-btn[data-cat="all"]').addClass('active');
        $('#fk_blogSearchInput').val('');
        $('#fk_blogSortSelect').val('newest');
        window.fkApplyBlogFilters(1);
    };

    // Thả tim bài viết tạp chí Realtime (cập nhật CSDL tgb_bai_viet_tin_tuc)
    window.fkToggleLikeArticle = function (articleId, btnEl) {
        let $btn = $(btnEl);
        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxLikeBlog',
            type: 'POST',
            dataType: 'json',
            data: { article_id: articleId },
            success: function (res) {
                if (res && res.status === 'success') {
                    $('.fk-like-count-' + articleId).text(Number(res.likes).toLocaleString('vi-VN'));
                    $('.fk-blog-col[data-id="' + articleId + '"]').attr('data-likes', res.likes);
                    if (res.is_liked) {
                        $('.fk-btn-like-blog[data-article-id="' + articleId + '"]').addClass('liked');
                        $btn.addClass('liked');
                    } else {
                        $('.fk-btn-like-blog[data-article-id="' + articleId + '"]').removeClass('liked');
                        $btn.removeClass('liked');
                    }
                }
            },
            error: function () {
                let $counters = $('.fk-like-count-' + articleId);
                let cur = parseInt(($counters.first().text() || '0').replace(/\D/g, ''), 10) || 0;
                let liked = $btn.hasClass('liked');
                let next = liked ? Math.max(0, cur - 1) : cur + 1;
                $counters.text(next.toLocaleString('vi-VN'));
                $btn.toggleClass('liked');
            }
        });
    };

    // Lưu bài viết đọc sau (Bookmark)
    window.fkToggleBookmarkArticle = function (articleId, btnEl) {
        let $btn = $(btnEl);
        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxBookmarkBlog',
            type: 'POST',
            dataType: 'json',
            data: { article_id: articleId },
            success: function (res) {
                if (res && res.status === 'success') {
                    $('#fk_savedCountBadge').text(res.total_saved);
                    let $col = $('.fk-blog-col[data-id="' + articleId + '"]');
                    if (res.is_bookmarked) {
                        $btn.addClass('bookmarked');
                        $col.attr('data-bookmarked', '1');
                    } else {
                        $btn.removeClass('bookmarked');
                        $col.attr('data-bookmarked', '0');
                    }
                }
            }
        });
    };

    // Chia sẻ liên kết bài viết
    window.fkShareBlogLink = function (articleId, title) {
        let shareUrl = window.location.origin + '/FashionKids/views/pages/blog-detail.php?id=' + articleId;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareUrl);
        }
        alert('Đã sao chép liên kết bài viết "' + title + '" vào bộ nhớ tạm:\n' + shareUrl);
    };

    // Mở Modal đọc nhanh bài viết tại chỗ
    window.fkOpenQuickReadModal = function (articleId) {
        let $dataBox = $('#fk_quickReadData_' + articleId);
        if (!$dataBox.length) {
            window.location.href = '/FashionKids/views/pages/blog-detail.php?id=' + articleId;
            return;
        }
        $('#fk_qrCategoryBadge').text($dataBox.attr('data-cat') || 'Tạp chí FashionKids');
        $('#fk_qrTitle').text($dataBox.attr('data-title') || '');
        $('#fk_qrMeta').text($dataBox.attr('data-author') || '');
        $('#fk_qrImage').attr('src', $dataBox.attr('data-img') || '/FashionKids/images/vay-cong-chua-no-pastel.jpg');
        $('#fk_qrFullLink').attr('href', $dataBox.attr('data-url') || ('/FashionKids/views/pages/blog-detail.php?id=' + articleId));
        $('#fk_qrBodyContent').html($dataBox.html());
        $('#fk_modalQuickReadBlog').modal('show');
    };

    // ------------------------------------------------------------------------
    // 3. MH 19: BÌNH LUẬN ĐA CẤP REALTIME TRONG BÀI VIẾT (blog-detail.php)
    // ------------------------------------------------------------------------
    window.fkQuickInsertComment = function (sampleText) {
        $('#fk_commentContent').val(sampleText).focus();
    };

    window.fkToggleReplyForm = function (commentId) {
        let $box = $('#fk_replyFormBox_' + commentId);
        $box.toggleClass('d-none');
        if (!$box.hasClass('d-none')) {
            $('#fk_replyInput_' + commentId).focus();
        }
    };

    $('#fk_formBlogComment').on('submit', function (e) {
        e.preventDefault();
        let articleId  = parseInt($('#fk_commentArticleId').val(), 10) || 1;
        let authorName = ($('#fk_commentAuthorName').val() || 'Phụ Huynh Thành Viên').trim();
        let content    = ($('#fk_commentContent').val() || '').trim();
        if (!content) return;

        $('#fk_btnSubmitComment').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang gửi...');

        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxPostBlogComment',
            type: 'POST',
            dataType: 'json',
            data: {
                article_id:  articleId,
                author_name: authorName,
                content:     content
            },
            complete: function () {
                $('#fk_btnSubmitComment').prop('disabled', false).html('<i class="fa-solid fa-paper-plane mr-1"></i>Gửi Bình Luận');
            },
            success: function (res) {
                let c = (res && res.comment) ? res.comment : {
                    id: Date.now(),
                    author_name: authorName,
                    vip_badge: 'Thành Viên VIP',
                    content: content,
                    created_at: 'Vừa xong'
                };
                let html =
                    '<div class="fk-comment-item mb-3" id="fk_commentThread_' + c.id + '">' +
                        '<div class="fk-comment-box p-3">' +
                            '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                '<div class="d-flex align-items-center">' +
                                    '<span class="rounded-circle bg-peach text-white d-inline-flex align-items-center justify-content-center avatar-40 mr-2 font-weight-bold"><i class="fa-solid fa-user"></i></span>' +
                                    '<div><strong class="text-slate mr-2">' + fkEsc(c.author_name) + '</strong>' +
                                    '<span class="badge badge-pill badge-gold small">' + fkEsc(c.vip_badge || 'Thành Viên') + '</span></div>' +
                                '</div>' +
                                '<small class="text-muted-custom">' + fkEsc(c.created_at) + '</small>' +
                            '</div>' +
                            '<p class="mb-2 text-slate small pl-5">' + fkEsc(c.content) + '</p>' +
                            '<div class="pl-5">' +
                                '<button type="button" onclick="fkToggleReplyForm(' + c.id + ')" class="btn btn-link btn-sm p-0 text-peach font-weight-bold text-decoration-none mr-3"><i class="fa-solid fa-reply mr-1"></i>Phản hồi</button>' +
                            '</div>' +
                            '<div id="fk_replyFormBox_' + c.id + '" class="mt-3 pl-4 d-none">' +
                                '<div class="input-group input-group-sm">' +
                                    '<input type="text" id="fk_replyInput_' + c.id + '" class="form-control rounded-pill mr-2" placeholder="Viết phản hồi...">' +
                                    '<div class="input-group-append">' +
                                        '<button type="button" onclick="fkSubmitReplyComment(' + articleId + ', ' + c.id + ')" class="btn btn-pastel-primary btn-sm rounded-pill px-3 font-weight-bold">Gửi phản hồi</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="ml-4 ml-md-5 mt-2" id="fk_repliesContainer_' + c.id + '"></div>' +
                    '</div>';
                $('#fk_blogCommentsList').prepend(html);
                $('#fk_commentContent').val('');
                let curCnt = parseInt($('#fk_totalCommentsCounter').text(), 10) || 0;
                $('#fk_totalCommentsCounter').text(curCnt + 1);
            }
        });
    });

    window.fkSubmitReplyComment = function (articleId, parentId) {
        let $input = $('#fk_replyInput_' + parentId);
        let content = ($input.val() || '').trim();
        if (!content) return;

        let authorName = ($('#fk_commentAuthorName').val() || 'Phụ Huynh Thành Viên').trim();

        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxPostBlogComment',
            type: 'POST',
            dataType: 'json',
            data: {
                article_id:  articleId,
                parent_id:   parentId,
                author_name: authorName,
                content:     content
            },
            success: function (res) {
                let c = (res && res.comment) ? res.comment : {
                    author_name: authorName,
                    vip_badge: 'Phản Hồi Trực Tiếp',
                    content: content,
                    created_at: 'Vừa xong'
                };
                let replyHtml =
                    '<div class="fk-comment-reply-box p-3 mb-2 shadow-sm">' +
                        '<div class="d-flex justify-content-between align-items-center mb-1">' +
                            '<div><strong class="text-slate small mr-1"><i class="fa-solid fa-circle-check text-success mr-1"></i>' + fkEsc(c.author_name) + '</strong>' +
                            '<span class="badge badge-pill badge-mint small">' + fkEsc(c.vip_badge || 'Thành Viên') + '</span></div>' +
                            '<small class="text-muted-custom">' + fkEsc(c.created_at) + '</small>' +
                        '</div>' +
                        '<p class="mb-0 text-slate small">' + fkEsc(c.content) + '</p>' +
                    '</div>';
                $('#fk_repliesContainer_' + parentId).append(replyHtml);
                $input.val('');
                $('#fk_replyFormBox_' + parentId).addClass('d-none');
            }
        });
    };

    // ------------------------------------------------------------------------
    // 4. MH 21: QUẢN TRỊ NỘI DUNG TẠP CHÍ CMS REALTIME (admin-cms-blog.php)
    // ------------------------------------------------------------------------
    $('#fk_formCmsBlog').on('submit', function (e) {
        e.preventDefault();
        let payload = {
            title:        ($('#fk_cmsTitle').val() || '').trim(),
            category_key: $('#fk_cmsCategory').val() || 'meo_phoi_do',
            status:       $('#fk_cmsStatus').val() || 'xuat_ban',
            image:        $('#fk_cmsImageSelect').val() || '/FashionKids/images/vay-cong-chua-no-pastel.jpg',
            summary:      ($('#fk_cmsSummary').val() || '').trim(),
            content:      ($('#fk_cmsContent').val() || '').trim()
        };
        if (!payload.title) return;

        $('#fk_btnSaveCmsBlog').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang xuất bản...');

        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxSaveBlogArticle',
            type: 'POST',
            dataType: 'json',
            data: payload,
            complete: function () {
                $('#fk_btnSaveCmsBlog').prop('disabled', false).html('<i class="fa-solid fa-paper-plane mr-1"></i>Xuất Bản Bài Viết Lên Tạp Chí Ngay');
            },
            success: function (res) {
                if (res && res.status === 'success' && res.article) {
                    let a = res.article;
                    let statusBadge = (a.status === 'xuat_ban')
                        ? '<span class="badge badge-mint px-2 py-1 rounded-pill">Đã xuất bản</span>'
                        : '<span class="badge badge-gold px-2 py-1 rounded-pill">Bản nháp</span>';
                    let rowHtml =
                        '<tr id="fk_cmsArticleRow_' + a.id + '">' +
                            '<td><div class="d-flex align-items-center">' +
                                '<span class="lbn-inv-thumb mr-3 flex-shrink-0"><img src="' + fkEsc(a.image) + '" alt="Thumb" class="lbn-thumb-img"></span>' +
                                '<div><a href="/FashionKids/views/pages/blog-detail.php?id=' + a.id + '" class="font-weight-bold text-slate d-block text-decoration-none">' + fkEsc(a.title) + '</a>' +
                                '<span class="badge ' + fkEsc(a.badge_class) + ' mr-1">' + fkEsc(a.category_name) + '</span>' +
                                '<small class="text-muted-custom">' + fkEsc(a.published_at) + '</small></div>' +
                            '</div></td>' +
                            '<td class="text-center text-nowrap small font-weight-bold">' +
                                '<span class="text-info mr-2"><i class="fa-regular fa-eye mr-1"></i>' + a.views + '</span>' +
                                '<span class="text-danger"><i class="fa-solid fa-heart mr-1"></i>' + a.likes + '</span>' +
                            '</td>' +
                            '<td class="text-center">' + statusBadge + '</td>' +
                            '<td class="text-center text-nowrap">' +
                                '<a href="/FashionKids/views/pages/blog-detail.php?id=' + a.id + '" class="btn btn-outline-info btn-sm rounded-pill px-2 mr-1"><i class="fa-regular fa-eye"></i></a>' +
                                '<button type="button" onclick="fkDeleteCmsArticleRow(' + a.id + ')" class="btn btn-outline-danger btn-sm rounded-circle"><i class="fa-solid fa-trash-can"></i></button>' +
                            '</td>' +
                        '</tr>';
                    $('#fk_cmsArticlesTableBody').prepend(rowHtml);
                    let total = parseInt($('#fk_cmsTotalArticles').text(), 10) || 0;
                    $('#fk_cmsTotalArticles').text(total + 1);
                    alert('Đã xuất bản bài viết "' + a.title + '" lên trang Tạp Chí thành công!');
                }
            }
        });
    });

    // MH 21/22: Xuất bản bài viết từ Bản nháp (Chỉ Admin) & Xóa bài viết Tạp chí (Chỉ Admin)
    window.fkPublishCmsArticleRow = function (id) {
        $.ajax({
            url: '/FashionKids/index.php?c=admin&a=ajaxPublishBlogArticle',
            type: 'POST',
            dataType: 'json',
            data: { article_id: id },
            success: function (res) {
                if (res && res.status === 'success') {
                    $('#fk_cmsStatusCell_' + id).html('<span class="badge badge-mint px-2 py-1 rounded-pill">Đã xuất bản</span>');
                    $('#fk_btnPublishArticle_' + id).remove();
                    alert(res.message || ('Đã xuất bản chính thức bài viết #' + id + ' thành công!'));
                } else {
                    alert((res && res.message) ? res.message : 'Không thể xuất bản bài viết!');
                }
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Truy cập bị từ chối (403 Forbidden): Chỉ Quản trị viên mới được xuất bản bài viết!';
                alert(errMsg);
            }
        });
    };

    window.fkDeleteCmsArticleRow = function (id) {
        if (!confirm('Bạn có chắc chắn muốn xóa bài viết #' + id + ' khỏi hệ thống Tạp chí?')) {
            return;
        }
        $.ajax({
            url: '/FashionKids/index.php?c=admin&a=ajaxDeleteBlogArticle',
            type: 'POST',
            dataType: 'json',
            data: { article_id: id },
            success: function (res) {
                if (res && res.status === 'success') {
                    $('#fk_cmsArticleRow_' + id).fadeOut(250, function () {
                        $(this).remove();
                    });
                    let total = Math.max(0, (parseInt($('#fk_cmsTotalArticles').text(), 10) || 1) - 1);
                    $('#fk_cmsTotalArticles').text(total);
                    alert(res.message || ('Đã xóa bài viết #' + id + ' thành công!'));
                } else {
                    alert((res && res.message) ? res.message : 'Không thể xóa bài viết!');
                }
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Truy cập bị từ chối (403 Forbidden): Chỉ Quản trị viên mới được xóa bài viết!';
                alert(errMsg);
            }
        });
    };

    // ------------------------------------------------------------------------
    // 5. MH 08 & MH 11: TIN NHẮN TƯ VẤN TRỰC TUYẾN REALTIME (tgb_tin_nhan_tu_van)
    // ------------------------------------------------------------------------
    function fkRenderClientChatList(messages) {
        let $area = $('#fk_clientChatMessagesArea');
        if (!$area.length || !Array.isArray(messages)) return;
        let html = '';
        messages.forEach(function (m) {
            if (m.sender === 'khach_hang') {
                html +=
                    '<div class="d-flex justify-content-end mb-3">' +
                        '<div class="p-3 small shadow-sm chat-bubble-sent">' + fkEsc(m.content) + '</div>' +
                    '</div>';
            } else {
                html +=
                    '<div class="d-flex mb-3">' +
                        '<div class="p-3 bg-white text-slate small shadow-sm chat-bubble-received">' + fkEsc(m.content) + '</div>' +
                    '</div>';
            }
        });
        $area.html(html);
        $area.scrollTop($area[0].scrollHeight);
    }

    function fkRenderStaffChatList(messages) {
        let $box = $('#fk_staffChatMessagesBox');
        if (!$box.length || !Array.isArray(messages)) return;
        let html = '';
        messages.forEach(function (m) {
            if (m.sender === 'khach_hang') {
                html +=
                    '<div class="d-flex mb-3">' +
                        '<div class="p-3 bg-white text-slate shadow-sm chat-bubble-received border">' +
                            '<div class="small font-weight-bold text-peach mb-1"><i class="fa-solid fa-user mr-1"></i>Phụ huynh • ' + fkEsc(m.time || '') + '</div>' +
                            '<div>' + fkEsc(m.content) + '</div>' +
                        '</div>' +
                    '</div>';
            } else {
                html +=
                    '<div class="d-flex justify-content-end mb-3">' +
                        '<div class="p-3 shadow-sm chat-bubble-sent">' +
                            '<div class="small text-light mb-1"><i class="fa-solid fa-headset mr-1"></i>Chuyên viên CSKH • ' + fkEsc(m.time || '') + '</div>' +
                            '<div>' + fkEsc(m.content) + '</div>' +
                        '</div>' +
                    '</div>';
            }
        });
        $box.html(html);
        $box.scrollTop($box[0].scrollHeight);
    }

    window.fkSendQuickClientChat = function (questionText) {
        $('#fk_clientChatInput').val(questionText);
        $('#clientChatForm').trigger('submit');
    };

    $('#clientChatForm').on('submit', function (e) {
        e.preventDefault();
        let $input = $('#fk_clientChatInput');
        let msg = ($input.val() || '').trim();
        if (!msg) return;

        let sessionId = $('#fk_clientChatMessagesArea').attr('data-session-id') || 'sess_guest_123456';
        $input.val('');

        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxSendChat',
            type: 'POST',
            dataType: 'json',
            data: {
                session_id: sessionId,
                sender:     'khach_hang',
                message:    msg
            },
            success: function (res) {
                if (res && res.status === 'success' && Array.isArray(res.messages)) {
                    fkRenderClientChatList(res.messages);
                }
            }
        });
    });

    // Nhân viên CSKH gửi tin nhắn từ trang staff-chat.php (MH 11)
    window.fkStaffQuickReply = function (replyText) {
        $('#fk_staffChatInput').val(replyText);
        $('#fk_formStaffChat').trigger('submit');
    };

    $('#fk_formStaffChat').on('submit', function (e) {
        e.preventDefault();
        let $input = $('#fk_staffChatInput');
        let msg = ($input.val() || '').trim();
        if (!msg) return;

        let sessionId = $('#fk_staffCurrentSessionId').val() || 'sess_guest_123456';
        $input.val('');

        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxSendChat',
            type: 'POST',
            dataType: 'json',
            data: {
                session_id: sessionId,
                sender:     'nhan_vien',
                message:    msg
            },
            success: function (res) {
                if (res && res.status === 'success' && Array.isArray(res.messages)) {
                    fkRenderStaffChatList(res.messages);
                }
            }
        });
    });

    window.fkSwitchStaffChatSession = function (sessionId, customerName, vipLabel, el) {
        $('.fk-staff-session-item').removeClass('bg-milk-cream');
        $(el).addClass('bg-milk-cream');
        $(el).find('.fk-session-unread').remove();

        $('#fk_staffCurrentSessionId').val(sessionId);
        $('#fk_activeChatSessionCode').text(sessionId);
        $('#fk_activeChatCustomerName').text(customerName);
        $('#fk_activeChatVipBadge').text(vipLabel);

        // Đồng bộ trạng thái "Đã đọc" (tgb_danh_dau_tin_da_doc) qua StaffController
        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxMarkChatRead',
            type: 'POST',
            dataType: 'json',
            data: { session_id: sessionId },
            success: function (res) {
                if (res && res.status === 'success' && Array.isArray(res.messages)) {
                    fkRenderStaffChatList(res.messages);
                } else {
                    window.fkPollStaffChatNow();
                }
            },
            error: function () {
                window.fkPollStaffChatNow();
            }
        });
    };

    window.fkPollStaffChatNow = function () {
        let sessionId = $('#fk_staffCurrentSessionId').val() || $('#fk_clientChatMessagesArea').attr('data-session-id') || 'sess_guest_123456';
        let isStaffDesk = $('#fk_staff_chat_section').length > 0 ? 1 : 0;
        $.ajax({
            url: '/FashionKids/index.php?c=home&a=ajaxPollChat',
            type: 'GET',
            dataType: 'json',
            data: { session_id: sessionId, mark_read: isStaffDesk },
            success: function (res) {
                if (res && res.status === 'success') {
                    if ($('#fk_staffPollClock').length && res.timestamp) {
                        $('#fk_staffPollClock').text(res.timestamp);
                    }
                    if ($('#fk_staffChatMessagesBox').length && Array.isArray(res.messages)) {
                        fkRenderStaffChatList(res.messages);
                    }
                    if ($('#customerChatWidget').hasClass('show') && Array.isArray(res.messages)) {
                        fkRenderClientChatList(res.messages);
                    }
                }
            }
        });
    };

    // Tự động đồng bộ tin nhắn Realtime mỗi 3.5 giây khi đang ở trang CSKH hoặc mở hộp thoại Chat
    if ($('#fk_staff_chat_section').length || $('#customerChatWidget').length) {
        setInterval(function () {
            if ($('#fk_staff_chat_section').length || $('#customerChatWidget').hasClass('show')) {
                window.fkPollStaffChatNow();
            }
        }, 3500);
    }

    // Lọc nhanh tồn kho sản phẩm ở cột phải màn hình staff-chat.php
    window.fkFilterStaffQuickStock = function (kw) {
        let q = String(kw || '').toLowerCase().trim();
        $('.fk-staff-stock-card').each(function () {
            let hay = ($(this).attr('data-search') || '').toLowerCase();
            if (!q || hay.indexOf(q) !== -1) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    };

    // ------------------------------------------------------------------------
    // 6. MH 16 & MH 17: XÉT DUYỆT HỒ SƠ KOL NHÍ REALTIME (sp_lnkt_xet_duyet_kol_nhi)
    // ------------------------------------------------------------------------
    window.fkFilterKolByAge = function (ageKey, btnEl) {
        $('#fk_staff_casting_review_section .fk-blog-filter-btn').removeClass('active');
        $(btnEl).addClass('active');
        $('#fk_activeKolAgeGroup').val(ageKey);
        window.fkApplyKolFilters();
    };

    window.fkApplyKolFilters = function () {
        let ageGroup = $('#fk_activeKolAgeGroup').val() || 'all';
        let stFilter = $('#fk_kolStatusFilterSelect').val() || 'all';

        $('.fk-kol-card-col').each(function () {
            let $c = $(this);
            let itemAge = $c.attr('data-age') || '3_4';
            let itemSt = $c.attr('data-status') || 'cho_duyet';

            let matchAge = (ageGroup === 'all' || itemAge === ageGroup);
            let matchSt = (stFilter === 'all' || itemSt === stFilter);

            if (matchAge && matchSt) {
                $c.removeClass('d-none');
            } else {
                $c.addClass('d-none');
            }
        });
    };

    window.fkReviewKolApplication = function (id, newStatus, babyName) {
        let isFinalDecision = (newStatus === 'trung_tuyen' || newStatus === 'tu_choi');
        let targetEndpoint = isFinalDecision
            ? '/FashionKids/index.php?c=admin&a=ajaxReviewKolCasting'
            : '/FashionKids/index.php?c=staff&a=ajaxPreliminaryKolReview';

        $.ajax({
            url: targetEndpoint,
            type: 'POST',
            dataType: 'json',
            data: { id: id, status: newStatus },
            success: function (res) {
                if (!res || res.status !== 'success') {
                    alert((res && res.message) ? res.message : 'Không thể cập nhật trạng thái hồ sơ KOL!');
                    return;
                }
                let badgeHtml = '';
                if (newStatus === 'moi_phong_van') {
                    badgeHtml = '<span class="badge badge-sky px-3 py-1 rounded-pill"><i class="fa-solid fa-camera-retro mr-1"></i>Hẹn thử đồ Studio</span>';
                } else if (newStatus === 'trung_tuyen') {
                    badgeHtml = '<span class="badge badge-mint px-3 py-1 rounded-pill"><i class="fa-solid fa-trophy mr-1"></i>Đã trúng tuyển KOL</span>';
                } else if (newStatus === 'tu_choi') {
                    badgeHtml = '<span class="badge badge-danger px-3 py-1 rounded-pill"><i class="fa-solid fa-ban mr-1"></i>Từ chối</span>';
                } else {
                    badgeHtml = '<span class="badge badge-gold px-3 py-1 rounded-pill"><i class="fa-solid fa-clock mr-1"></i>Chờ xét duyệt</span>';
                }
                $('#fk_kolStatusBadge_' + id).html(badgeHtml);
                $('#fk_kolCard_' + id).attr('data-status', newStatus);

                if (res && res.counts) {
                    $('#fk_kolCount_cho_duyet').text(res.counts.cho_duyet || 0);
                    $('#fk_kolCount_moi_phong_van').text(res.counts.moi_phong_van || 0);
                    $('#fk_kolCount_trung_tuyen').text(res.counts.trung_tuyen || 0);
                    $('#fk_kolCount_tu_choi').text(res.counts.tu_choi || 0);
                }
                const kolLabelMap = {
                    cho_duyet: 'Chờ xét duyệt',
                    moi_phong_van: 'Hẹn thử đồ Studio',
                    trung_tuyen: 'Đã trúng tuyển KOL',
                    tu_choi: 'Từ chối'
                };
                alert(res.message || ('Đã cập nhật trạng thái hồ sơ "' + babyName + '": ' + (kolLabelMap[newStatus] || newStatus)));
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Truy cập bị từ chối (403 Forbidden): Bạn không có quyền thực hiện thao tác này!';
                alert(errMsg);
            }
        });
    };
});


/**
 * ============================================================================
 * KIỂM SOÁT TRUY CẬP THEO VAI TRÒ & TƯƠNG TÁC ĐÁNH GIÁ SẢN PHẨM
 * ============================================================================
 */
$(document).ready(function () {
    // 1. Yêu cầu đăng nhập khi Khách ghé thăm bấm Thả tim bài viết tạp chí
    let origToggleLike = window.fkToggleLikeArticle;
    window.fkToggleLikeArticle = function (articleId, btnEl) {
        let role = $('body').attr('data-user-role') || 'guest';
        if (role === 'guest') {
            if (confirm('Vui lòng đăng nhập tài khoản thành viên để thả tim & bình luận bài viết!')) {
                window.location.href = '/FashionKids/views/pages/login-register.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            }
            return;
        }
        if (typeof origToggleLike === 'function') {
            origToggleLike(articleId, btnEl);
        }
    };

    // 2. Xử lý Đặt hàng AJAX hoàn chỉnh & Checkout Gate (checkout.php)
    if ($('#lnkt_formCheckoutOrder').length) {
        $('#lnkt_formCheckoutOrder').attr('novalidate', 'novalidate');

        function fkShowCheckoutFormError(msg, $focusEl) {
            if (typeof window.lnktShowCheckoutAlert === 'function') {
                window.lnktShowCheckoutAlert('error', msg);
            } else {
                $('#lnkt_checkoutAlert').removeClass('d-none').addClass('alert-danger d-block').text(msg);
            }
            if ($focusEl && $focusEl.length && !$focusEl.prop('disabled')) {
                $focusEl.focus();
            }
            if ($('#lnkt_checkoutAlert').length) {
                $('html, body').animate({
                    scrollTop: Math.max(0, $('#lnkt_checkoutAlert').offset().top - 110)
                }, 220);
            }
        }

        $('#lnkt_formCheckoutOrder').off('submit').on('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            let role = $('body').attr('data-user-role') || 'guest';
            if (role === 'guest') {
                if (confirm('Giỏ hàng của bạn vẫn được lưu an toàn. Vui lòng đăng nhập tài khoản thành viên để hoàn tất đặt hàng và tích điểm VIP!')) {
                    window.location.href = '/FashionKids/views/pages/login-register.php?redirect=' + encodeURIComponent('/FashionKids/views/pages/checkout.php');
                }
                return false;
            }

            if ($('.lnkt-cart-row').length === 0) {
                fkShowCheckoutFormError('Giỏ hàng của bạn đang trống, vui lòng chọn sản phẩm trước khi đặt hàng!');
                return false;
            }

            if (typeof window.tgbComposeFullAddress === 'function') {
                window.tgbComposeFullAddress();
            }

            let receiverName = ($('#lnkt_receiverName').val() || '').trim();
            let receiverPhone = ($('#lnkt_receiverPhone').val() || '').trim();
            let receiverEmail = ($('#tgb_receiver_email').val() || '').trim();
            let province = ($('#tgb_select_province').val() || '').trim();
            let district = ($('#tgb_select_district').val() || '').trim();
            let ward = ($('#tgb_select_ward').val() || '').trim();
            let shippingAddress = ($('#lnkt_shippingAddress').val() || '').trim();
            let serviceType = ($('input[name="dich_vu_van_chuyen"]:checked').val() || 'standard').trim();
            let voucherCode = ($('#lnkt_appliedVoucherInput').val() || $('#lnkt_voucherCodeInput').val() || '').trim().toUpperCase();
            let paymentMethod = ($('input[name="phuong_thuc_thanh_toan"]:checked').val() || 'cod').trim();
            let orderNote = ($('#lnkt_orderNote').val() || '').trim();
            let csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

            if (!receiverName) {
                fkShowCheckoutFormError('Vui lòng nhập họ và tên phụ huynh nhận hàng!', $('#lnkt_receiverName'));
                return false;
            }
            if (!/^[0-9]{9,11}$/.test(receiverPhone.replace(/\s+/g, ''))) {
                fkShowCheckoutFormError('Vui lòng nhập số điện thoại liên hệ hợp lệ (9-11 chữ số)!', $('#lnkt_receiverPhone'));
                return false;
            }
            if (!province) {
                fkShowCheckoutFormError('Vui lòng chọn Tỉnh / Thành phố giao hàng!', $('#tgb_select_province'));
                return false;
            }
            if (!district) {
                fkShowCheckoutFormError('Vui lòng chọn Quận / Huyện giao hàng!', $('#tgb_select_district'));
                return false;
            }
            if ($('#tgb_select_ward').length && !$('#tgb_select_ward').prop('disabled') && $('#tgb_select_ward option').length > 1 && !ward) {
                fkShowCheckoutFormError('Vui lòng chọn Phường / Xã giao hàng!', $('#tgb_select_ward'));
                return false;
            }
            if (!shippingAddress) {
                fkShowCheckoutFormError('Vui lòng nhập địa chỉ giao hàng chi tiết (Số nhà, Tên đường)!', $('#lnkt_shippingAddress'));
                return false;
            }

            $('#lnkt_checkoutAlert').addClass('d-none').text('');
            let $submitBtn = $('#lnkt_btnSubmitCheckout');
            let origBtnHtml = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Đang xử lý đặt hàng...');

            $.ajax({
                url: '/FashionKids/index.php?c=order&a=ajaxCreateOrder',
                type: 'POST',
                dataType: 'json',
                headers: csrfToken ? { 'X-CSRF-Token': csrfToken } : {},
                data: {
                    ten_nguoi_nhan:         receiverName,
                    so_dien_thoai_nhan:     receiverPhone.replace(/\s+/g, ''),
                    thu_dien_tu_nhan:       receiverEmail,
                    tinh_thanh_pho:         province,
                    quan_huyen:             district,
                    phuong_xa:              ward,
                    dia_chi_giao_hang:      shippingAddress,
                    dich_vu_van_chuyen:     serviceType,
                    ma_voucher:             voucherCode,
                    phuong_thuc_thanh_toan: paymentMethod,
                    ghi_chu:                orderNote,
                    csrf_token:             csrfToken
                },
                timeout: 8000,
                success: function (res) {
                    if (res && res.status === 'success') {
                        $('.cart-badge-custom').text('0');
                        window.location.href = res.redirect || ('/FashionKids/index.php?c=order&a=success&order_id=' + (res.order_id || 0));
                        return;
                    }
                    $submitBtn.prop('disabled', false).html(origBtnHtml);
                    fkShowCheckoutFormError((res && res.message) ? res.message : 'Không thể hoàn tất đặt hàng lúc này.');
                },
                error: function (xhr) {
                    $submitBtn.prop('disabled', false).html(origBtnHtml);
                    let res = xhr.responseJSON || {};
                    if (xhr.status === 401 || res.code === 401) {
                        if (confirm(res.message || 'Vui lòng đăng nhập tài khoản thành viên để hoàn tất đặt hàng!')) {
                            window.location.href = res.redirect || ('/FashionKids/views/pages/login-register.php?redirect=' + encodeURIComponent('/FashionKids/views/pages/checkout.php'));
                        }
                        return;
                    }
                    fkShowCheckoutFormError(res.message || 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng kiểm tra lại tồn kho hoặc thông tin nhận hàng!');
                }
            });

            return false;
        });
    }

    // 3. Tương tác chọn sao & gửi Đánh giá sản phẩm AJAX trong #modalReview
    const starLabels = {
        1: 'Chưa hài lòng',
        2: 'Tạm ổn',
        3: 'Hài lòng',
        4: 'Rất tốt',
        5: 'Tuyệt vời'
    };

    function renderStarPicker(score) {
        let val = parseInt(score, 10) || 5;
        $('.star-picker-group .star-picker-item').each(function () {
            let itemVal = parseInt($(this).attr('data-value'), 10) || 0;
            $(this).toggleClass('active', itemVal <= val);
        });
        $('#ratingScoreInput').val(val);
        $('#starRatingLabel').text(starLabels[val] || 'Tuyệt vời');
    }

    $(document).on('click', '.star-picker-group .star-picker-item', function () {
        renderStarPicker($(this).attr('data-value'));
    });

    $(document).on('show.bs.modal', '#modalReview', function () {
        if ($(this).attr('data-context-lock') !== '1') {
            if ($('#tgb_review_order_id').length) {
                $('#tgb_review_order_id').val('0');
            }
            if ($('#tgb_reviewProductContext').length) {
                $('#tgb_reviewProductContext').addClass('d-none').text('');
            }
            if ($('#lbn_pdpProductId').length && $('#tgb_review_product_id').length) {
                let pdpId = $('#lbn_pdpProductId').val() || 101;
                $('#tgb_review_product_id').val(pdpId);
            }
        }
        $('#tgb_reviewAlert').addClass('d-none').removeClass('alert-danger alert-success').text('');
        renderStarPicker($('#ratingScoreInput').val() || 5);
    });

    $(document).on('hidden.bs.modal', '#modalReview', function () {
        $(this).removeAttr('data-context-lock');
    });

    $('#tgb_formReview').on('submit', function (e) {
        e.preventDefault();
        let role = $('body').attr('data-user-role') || 'guest';
        if (role === 'guest') {
            if (confirm('Vui lòng đăng nhập tài khoản thành viên để gửi đánh giá sản phẩm!')) {
                window.location.href = '/FashionKids/views/pages/login-register.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            }
            return;
        }

        let productId = parseInt($('#tgb_review_product_id').val() || $('#lbn_pdpProductId').val() || 101, 10);
        let orderId = parseInt($('#tgb_review_order_id').val() || 0, 10);
        let ratingScore = parseInt($('#ratingScoreInput').val() || 5, 10);
        let content = ($('#tgb_review_content').val() || '').trim();
        let $alert = $('#tgb_reviewAlert');
        let $btn = $('#tgb_btnSubmitReview');

        $alert.addClass('d-none').removeClass('alert-danger alert-success');
        if (content.length < 5) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Vui lòng nhập cảm nhận tối thiểu 5 ký tự để chia sẻ cùng các phụ huynh khác.');
            $('#tgb_review_content').focus();
            return;
        }

        let origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang gửi...');

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxSubmitReview',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: productId,
                order_id: orderId,
                rating_score: ratingScore,
                review_content: content
            },
            complete: function () {
                $btn.prop('disabled', false).html(origText);
            },
            success: function (res) {
                $alert.removeClass('d-none alert-danger').addClass('alert-success d-block').text((res && res.message) ? res.message : 'Cảm ơn ba mẹ đã gửi đánh giá sản phẩm!');
                let r = (res && res.review) ? res.review : {
                    author_name: 'Phụ huynh thành viên',
                    rating: ratingScore,
                    content: content,
                    order_id: orderId,
                    date: 'Hôm nay'
                };
                let finalStars = parseInt(r.rating || ratingScore, 10) || 5;

                // Cập nhật trực tiếp bảng theo dõi đơn hàng nếu đang ở order-tracking.php
                let $trackCell = $('[data-review-cell-pid="' + productId + '"]');
                if ($trackCell.length) {
                    $trackCell.html(
                        '<span class="badge badge-mint px-2 py-1 rounded-pill font-weight-bold">' +
                        '<i class="fa-solid fa-star text-warning mr-1"></i>Đã đánh giá (' + finalStars + '/5)</span>'
                    );
                    if ($('.lnkt-item-review-btn').length === 0 && $('#lnkt_bannerReviewBtnWrap').length) {
                        $('#lnkt_bannerReviewBtnWrap').html(
                            '<span class="badge badge-mint px-3 py-2 rounded-pill font-weight-bold"><i class="fa-solid fa-circle-check mr-1"></i>Đã đánh giá đủ sản phẩm</span>'
                        );
                    }
                }

                // Cập nhật trực tiếp danh sách đánh giá trên PDP nếu đang ở product-detail.php
                if ($('#pdp_review_list').length) {
                    $('#pdp_empty_review_notice').remove();
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        starsHtml += '<i class="fa-' + (i <= finalStars ? 'solid' : 'regular') + ' fa-star"></i>';
                    }
                    let esc = function (s) {
                        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                    };
                    let initials = String(r.author_name || 'FK').trim().slice(0, 2).toUpperCase();
                    let newCard =
                        '<div class="col-12 mb-3">' +
                            '<div class="card border-0 rounded-lg shadow-sm p-3 bg-white">' +
                                '<div class="d-flex justify-content-between align-items-start mb-2">' +
                                    '<div class="d-flex align-items-center">' +
                                        '<div class="rounded-circle bg-sky text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 avatar-44">' + esc(initials) + '</div>' +
                                        '<div>' +
                                            '<div class="font-weight-bold text-slate">' + esc(r.author_name) +
                                                '<span class="badge badge-pill badge-mint ml-2"><i class="fa-solid fa-circle-check mr-1"></i>Đã mua tại FashionKids</span>' +
                                            '</div>' +
                                            '<div class="small"><span class="text-warning mr-2">' + starsHtml + '</span>' +
                                                (r.order_id ? ('<span class="text-muted-custom">Đơn hàng #' + r.order_id + '</span>') : '') +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<span class="text-muted-custom small">' + esc(r.date) + '</span>' +
                                '</div>' +
                                '<p class="text-slate small mb-2">' + esc(r.content) + '</p>' +
                            '</div>' +
                        '</div>';
                    $('#pdp_review_list').prepend(newCard);

                    let curCount = parseInt($('#pdp_review_count_top').text(), 10) || 0;
                    let newRevCount = (res && res.review_count !== undefined) ? parseInt(res.review_count, 10) : (curCount + 1);
                    $('#pdp_review_count_top').text(newRevCount);
                    $('#pdp_review_count_card').text(newRevCount);
                    if (newRevCount > 0) {
                        $('#pdp_rating_stars_top').removeClass('d-none');
                        $('#pdp_no_review_badge_top').addClass('d-none');
                        $('#pdp_rating_stars_card').removeClass('d-none');
                        $('#pdp_no_review_badge_card').addClass('d-none');
                    }
                    if (res && res.avg_rating !== undefined) {
                        let avgStr = Number(res.avg_rating).toFixed(1);
                        $('#pdp_avg_rating_top').text(avgStr);
                        $('#pdp_avg_rating_card').text(avgStr);
                    }
                    if (res && res.buyer_count !== undefined) {
                        let buyerCnt = parseInt(res.buyer_count, 10) || 0;
                        $('#pdp_buyer_count_top').text(buyerCnt);
                        $('#pdp_buyer_count_card').text(buyerCnt);
                    }
                }
                $('#tgb_review_content').val('');
                setTimeout(function () {
                    $('#modalReview').modal('hide');
                }, 900);
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể gửi đánh giá lúc này. Vui lòng thử lại!';
                $alert.removeClass('d-none alert-success').addClass('alert-danger d-block').text(errMsg);
            }
        });
    });

    // 4. Nhập kho nhanh trực tiếp trên Bảng điều khiển Quản trị (admin-dashboard.php)
    window.lbnDashboardQuickRestock = function (productId, addQty, btnEl) {
        let $btn = $(btnEl);
        let $alert = $('#admin_dashRestockAlert');
        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        $alert.addClass('d-none').removeClass('alert-danger alert-mint text-slate');

        $.ajax({
            url: '/FashionKids/index.php?c=staff&a=ajaxRestockInventory',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: productId,
                new_stock: addQty,
                mode: 'add'
            },
            timeout: 4000,
            success: function (res) {
                let updatedStock = (res && res.new_stock !== undefined) ? parseInt(res.new_stock, 10) : 0;
                if (updatedStock > 5) {
                    $('#dash_lowStockRow_' + productId).fadeOut(220, function () {
                        $(this).remove();
                        if ($('#admin_lowStockList li').length === 0) {
                            $('#admin_lowStockList').html(
                                '<li class="list-group-item p-4 text-center text-success font-weight-bold small">' +
                                '<i class="fa-solid fa-circle-check mr-1"></i>Tất cả sản phẩm hiện đều còn đủ tồn kho (&gt; 5 sản phẩm).</li>'
                            );
                        }
                    });
                } else {
                    $('#dash_stockBadge_' + productId).text('Còn ' + updatedStock + ' cái');
                    $btn.prop('disabled', false).html(origHtml);
                }

                if (res && res.stats && res.stats.low_stock_count !== undefined) {
                    $('#admin_lowStockCount').text(res.stats.low_stock_count);
                } else {
                    let curCnt = parseInt($('#admin_lowStockCount').text(), 10) || 0;
                    if (updatedStock > 5 && curCnt > 0) {
                        $('#admin_lowStockCount').text(curCnt - 1);
                    }
                }

                $alert.removeClass('d-none alert-danger').addClass('alert-mint text-slate d-block')
                    .html('<i class="fa-solid fa-circle-check text-success mr-1"></i>' + ((res && res.message) ? res.message : ('Đã nhập thêm +' + addQty + ' sản phẩm vào kho!')));
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html(origHtml);
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Không thể nhập thêm kho hàng lúc này!';
                $alert.removeClass('d-none alert-mint').addClass('alert-danger d-block').text(errMsg);
            }
        });
    };

    // ========================================================================
    // 7. YÊU CẦU 3 & 4: QUẢN LÝ HOÀN HÀNG / TRẢ HÀNG & ĐỐI SOÁT KHO / VIP
    // ========================================================================
    let lnktReturnPreviewUrls = [];
    let lnktActiveReturnFilter = 'all';
    let lnktRetMgmtPollInFlight = false;
    let lnktBanksLoaded = false;

    window.lnktLoadReturnBanks = function () {
        let $sel = $('#lnkt_returnBankSelect');
        if (!$sel.length || lnktBanksLoaded) return;

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxGetVietQrBanks',
            type: 'GET',
            dataType: 'json',
            timeout: 8000,
            success: function (res) {
                let banks = (res && Array.isArray(res.banks)) ? res.banks : [];
                $sel.empty().append('<option value="" data-short="" data-bin="">-- Chọn Ngân hàng / Ví nhận hoàn tiền --</option>');
                $sel.append('<option value="MOMO" data-short="Ví MoMo" data-bin="">Ví điện tử MoMo (SĐT đăng ký)</option>');
                banks.forEach(function (b) {
                    let shortName = String(b.shortName || b.code || '');
                    let fullName = String(b.name || shortName);
                    let bin = String(b.bin || '');
                    $sel.append(
                        $('<option>')
                            .val(shortName)
                            .attr('data-short', shortName)
                            .attr('data-bin', bin)
                            .text(shortName + ' - ' + fullName + (bin ? ' (BIN ' + bin + ')' : ''))
                    );
                });
                lnktBanksLoaded = true;
            },
            error: function () {
                $sel.empty()
                    .append('<option value="" data-short="" data-bin="">-- Chọn Ngân hàng (hoặc gõ tay ở ô bên dưới) --</option>')
                    .append('<option value="MBBank" data-short="MBBank" data-bin="970422">MBBank - Ngân hàng TMCP Quân đội</option>')
                    .append('<option value="Vietcombank" data-short="Vietcombank" data-bin="970436">Vietcombank - Ngoại thương VN</option>')
                    .append('<option value="Techcombank" data-short="Techcombank" data-bin="970407">Techcombank - Kỹ thương VN</option>')
                    .append('<option value="BIDV" data-short="BIDV" data-bin="970418">BIDV - Đầu tư và Phát triển VN</option>')
                    .append('<option value="MOMO" data-short="Ví MoMo" data-bin="">Ví điện tử MoMo</option>');
            }
        });
    };

    window.lnktSyncReturnBankInfoText = function () {
        let $sel = $('#lnkt_returnBankSelect');
        let $opt = $sel.find('option:selected');
        let bankShort = String($opt.attr('data-short') || $sel.val() || '').trim();
        let bankBin = String($opt.attr('data-bin') || '').trim();
        let accNo = ($('#lnkt_returnBankAccNo').val() || '').trim();
        let accName = ($('#lnkt_returnBankAccName').val() || '').trim().toUpperCase();
        if (!bankShort && !accNo) return;

        let parts = [];
        if (bankShort) parts.push(bankShort);
        if (accNo) parts.push(accNo);
        if (accName) parts.push(accName);
        let composed = parts.join(' - ');
        if (bankBin) {
            composed += ' [BIN:' + bankBin + ']';
        }
        $('#lnkt_returnBankInfo').val(composed);
    };

    $(document).on('change input', '#lnkt_returnBankSelect, #lnkt_returnBankAccNo, #lnkt_returnBankAccName', function () {
        window.lnktSyncReturnBankInfoText();
    });

    window.lnktOpenRefundQrModal = function (returnId, returnCode, amount, bankInfo) {
        let $modal = $('#modalRefundQr');
        if (!$modal.length) return;
        let amt = Math.round(Number(amount || 0));
        let infoStr = String(bankInfo || '').trim();
        let rcodeLabel = String(returnCode || ('FK-RET-' + String(returnId).padStart(5, '0'))).replace(/^#/, '');

        $('#lnkt_refundQrCode').text('#' + rcodeLabel);
        $('#lnkt_refundQrAmount').text(lnktFormatVnd(amt));
        $('#lnkt_refundQrBankText').text(infoStr || 'Khách hàng chưa cung cấp tài khoản');

        let binMatch = infoStr.match(/\[BIN:(\d{4,8})\]/i);
        let cleanNoBin = infoStr.replace(/\[BIN:[^\]]+\]/ig, '').trim();
        let parts = cleanNoBin.split(/\s*-\s*/);
        let accNo = '';
        let accName = '';
        for (let i = 0; i < parts.length; i++) {
            let p = parts[i].trim();
            if (!accNo && /^[0-9]{6,20}$/.test(p.replace(/\s+/g, ''))) {
                accNo = p.replace(/\s+/g, '');
            } else if (accNo && !accName) {
                accName = p;
            }
        }
        if (!accNo) {
            let numMatch = cleanNoBin.match(/\b(\d{6,20})\b/);
            if (numMatch) accNo = numMatch[1];
        }

        let $notice = $('#lnkt_refundQrNotice');
        let $img = $('#lnkt_refundQrImg');
        if (binMatch && accNo) {
            let bin = binMatch[1];
            let memo = 'Hoan don ' + rcodeLabel.replace(/[^A-Za-z0-9]/g, '');
            let qrUrl = 'https://img.vietqr.io/image/' + encodeURIComponent(bin) + '-' + encodeURIComponent(accNo) +
                '-compact2.png?amount=' + amt +
                '&addInfo=' + encodeURIComponent(memo) +
                (accName ? ('&accountName=' + encodeURIComponent(accName)) : '');
            $notice.addClass('d-none').text('');
            $img.attr('src', qrUrl).removeClass('d-none');
        } else {
            $img.addClass('d-none').attr('src', '');
            $notice.removeClass('d-none').html(
                '<i class="fa-solid fa-circle-info mr-1"></i>Tài khoản khách chọn Ví MoMo hoặc chưa có mã BIN VietQR chuẩn. Vui lòng chuyển khoản thủ công theo thông tin: <strong>' +
                lnktEscapeHtml(infoStr) + '</strong>'
            );
        }
        $modal.modal('show');
    };

    window.lnktOpenReturnLightbox = function (imgUrl) {
        if (!imgUrl) return;
        $('#lnkt_returnLightboxImg').attr('src', imgUrl);
        if ($('#modalReturnImagePreview').length) {
            $('#modalReturnImagePreview').modal('show');
        }
    };

    window.lnktOpenReturnRequestModal = function (orderId, orderCode, refundFormatted) {
        let $modal = $('#modalReturnRequest');
        if (!$modal.length) return;
        let formEl = document.getElementById('lnkt_formReturnRequest');
        if (formEl) {
            formEl.reset();
        }
        lnktReturnPreviewUrls.forEach(function (u) {
            try { URL.revokeObjectURL(u); } catch (e) {}
        });
        lnktReturnPreviewUrls = [];

        $('#lnkt_returnOrderId').val(parseInt(orderId, 10) || 0);
        let codeLabel = String(orderCode || ('FK-ORD-' + String(orderId).padStart(6, '0'))).replace(/^#/, '');
        $('#lnkt_returnModalOrderCode').text('#' + codeLabel);
        $('#lnkt_returnModalRefundAmount').text(refundFormatted || '0 đ');
        $('#lnkt_returnFormAlert').addClass('d-none').removeClass('alert-danger alert-mint lnkt-alert-mint-pastel text-slate').text('');
        $('#lnkt_returnPreviewGrid').empty().addClass('d-none');
        $('#lnkt_returnImagesLabel').text('Chọn 1 - 3 ảnh chụp sản phẩm thực tế & tem mác...');
        window.lnktLoadReturnBanks();
        $modal.modal('show');
    };

    window.lnktRenderReturnProgress = function (ret) {
        let $card = $('#lnkt_returnProgressCard');
        if (!$card.length) return;
        if (!ret || typeof ret !== 'object') {
            $card.addClass('d-none').attr('data-return-status', '');
            return;
        }

        const retStatusMap = {
            'cho_duyet_hoan': { label: 'Chờ duyệt hoàn hàng',   badgeClass: 'badge-gold',   icon: 'fa-hourglass-half' },
            'da_duyet_hoan':  { label: 'Đã duyệt hoàn hàng',    badgeClass: 'badge-sky',    icon: 'fa-circle-check' },
            'dang_thu_hoi':   { label: 'Đang thu hồi hàng',     badgeClass: 'badge-mint',   icon: 'fa-truck-arrow-right' },
            'hoan_tat_hoan':  { label: 'Đã hoàn tiền & Nhập kho', badgeClass: 'badge-mint', icon: 'fa-hand-holding-dollar' },
            'tu_choi_hoan':   { label: 'Đã từ chối hoàn hàng',  badgeClass: 'badge-danger', icon: 'fa-circle-xmark' }
        };
        const stepOrder = {
            'cho_duyet_hoan': 1,
            'da_duyet_hoan': 2,
            'dang_thu_hoi': 3,
            'hoan_tat_hoan': 4
        };

        let st = String(ret.trang_thai_hoan || 'cho_duyet_hoan');
        let meta = ret.status_meta || retStatusMap[st] || retStatusMap['cho_duyet_hoan'];
        let codeStr = ret.ma_hoan_code || ('FK-RET-' + String(ret.ma_hoan_hang || 0).padStart(5, '0'));
        let reqFmt = ret.ngay_yeu_cau_formatted || lnktFormatMysqlDt(ret.ngay_yeu_cau);
        let updFmt = ret.ngay_cap_nhat_formatted || lnktFormatMysqlDt(ret.ngay_cap_nhat || ret.ngay_yeu_cau);

        $card.removeClass('d-none').attr('data-return-status', st);
        $('#lnkt_retDisplayCode').text('#' + codeStr);
        $('#lnkt_retTimeMeta').text('Gửi lúc: ' + reqFmt + ' • Cập nhật: ' + updFmt);
        $('#lnkt_retStatusBadge')
            .attr('class', 'badge badge-pill ' + (meta.badgeClass || meta.badge_class || 'badge-gold') + ' px-3 py-2 text-nowrap')
            .html('<i class="fa-solid ' + lnktEscapeHtml(meta.icon || 'fa-hourglass-half') + ' mr-1"></i> ' + lnktEscapeHtml(meta.label || 'Chờ duyệt hoàn hàng'));

        if (st === 'tu_choi_hoan') {
            $('#lnkt_retStepperWrap').addClass('d-none');
            $('#lnkt_retRejectBox').removeClass('d-none');
            $('#lnkt_retRejectNoteText').text(ret.ghi_chu_xu_ly || 'Yêu cầu chưa đáp ứng điều kiện chính sách đổi trả của FashionKids.');
        } else {
            $('#lnkt_retStepperWrap').removeClass('d-none');
            $('#lnkt_retRejectBox').addClass('d-none');
            let curNum = stepOrder[st] || 1;
            $('#lnkt_retStepperWrap .lnkt-return-step').each(function () {
                let $stEl = $(this);
                let sk = $stEl.attr('data-ret-step');
                let sNum = stepOrder[sk] || 0;
                $stEl.removeClass('done active rejected');
                if (sNum > 0) {
                    if (sNum < curNum) {
                        $stEl.addClass('done');
                    } else if (sNum === curNum) {
                        $stEl.addClass(st === 'hoan_tat_hoan' ? 'done active' : 'active');
                    }
                }
            });
        }

        $('#lnkt_retReasonText').text(ret.ly_do_hoan || '');
        let amountFmt = ret.so_tien_hoan_formatted || lnktFormatVnd(ret.so_tien_hoan || 0);
        $('#lnkt_retBankText').html(
            lnktEscapeHtml(ret.thong_tin_nhan_tien || '') +
            ' (<span id="lnkt_retAmountText" class="text-peach">' + lnktEscapeHtml(amountFmt) + '</span>)'
        );
        $('#lnkt_retDescText').text(ret.mo_ta_chi_tiet || '');

        let imgs = Array.isArray(ret.hinh_anh_list) ? ret.hinh_anh_list : [];
        if (imgs.length > 0) {
            let thumbsHtml = '';
            imgs.forEach(function (imgUrl) {
                let safeUrlAttr = lnktEscapeHtml(String(imgUrl || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'"));
                thumbsHtml += '<img src="' + lnktEscapeHtml(imgUrl) + '" alt="Minh chứng hoàn hàng" class="lnkt-return-thumb" onclick="lnktOpenReturnLightbox(\'' + safeUrlAttr + '\')">';
            });
            $('#lnkt_retEvidenceThumbs').html(thumbsHtml);
            $('#lnkt_retEvidenceRow').removeClass('d-none');
        } else {
            $('#lnkt_retEvidenceThumbs').empty();
            $('#lnkt_retEvidenceRow').addClass('d-none');
        }
    };

    function lnktValidateReturnFiles(files) {
        if (!files || files.length < 1 || files.length > 3) {
            return 'Vui lòng chọn từ 1 đến 3 ảnh minh chứng thực tế (JPG, PNG, WEBP).';
        }
        const allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        const allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        for (let i = 0; i < files.length; i++) {
            let f = files[i];
            let nameParts = String(f.name || '').toLowerCase().split('.');
            let ext = nameParts.length > 1 ? nameParts.pop() : '';
            if (allowedExt.indexOf(ext) === -1 && allowedMime.indexOf(String(f.type || '').toLowerCase()) === -1) {
                return 'Ảnh "' + (f.name || ('#' + (i + 1))) + '" không đúng định dạng JPG, PNG hoặc WEBP.';
            }
            if (f.size <= 0 || f.size > 5 * 1024 * 1024) {
                return 'Ảnh "' + (f.name || ('#' + (i + 1))) + '" vượt quá dung lượng tối đa 5MB.';
            }
        }
        return '';
    }

    $(document).on('change', '#lnkt_returnImagesInput', function () {
        let files = this.files;
        let $alert = $('#lnkt_returnFormAlert');
        let $grid = $('#lnkt_returnPreviewGrid');
        let $label = $('#lnkt_returnImagesLabel');

        lnktReturnPreviewUrls.forEach(function (u) {
            try { URL.revokeObjectURL(u); } catch (e) {}
        });
        lnktReturnPreviewUrls = [];
        $grid.empty().addClass('d-none');
        $alert.addClass('d-none').removeClass('alert-danger alert-mint lnkt-alert-mint-pastel text-slate').text('');

        if (!files || files.length === 0) {
            $label.text('Chọn 1 - 3 ảnh chụp sản phẩm thực tế & tem mác...');
            return;
        }

        let errMsg = lnktValidateReturnFiles(files);
        if (errMsg) {
            $label.text('Chưa đạt yêu cầu tệp ảnh...');
            $alert.removeClass('d-none').addClass('alert-danger d-block').html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(errMsg));
            return;
        }

        $label.text('Đã chọn ' + files.length + ' ảnh minh chứng');
        for (let i = 0; i < files.length; i++) {
            try {
                let objUrl = URL.createObjectURL(files[i]);
                lnktReturnPreviewUrls.push(objUrl);
                $grid.append('<img src="' + objUrl + '" alt="Xem trước ảnh minh chứng" class="lnkt-return-preview-item">');
            } catch (e) {}
        }
        $grid.removeClass('d-none');
    });

    $(document).on('submit', '#lnkt_formReturnRequest', function (e) {
        e.preventDefault();
        let $form = $(this);
        let $alert = $('#lnkt_returnFormAlert');
        let $btn = $('#lnkt_btnSubmitReturn');
        let orderId = parseInt($('#lnkt_returnOrderId').val(), 10) || 0;
        let reason = ($('#lnkt_returnReason').val() || '').trim();
        let desc = ($('#lnkt_returnDescription').val() || '').trim();
        let bankInfo = ($('#lnkt_returnBankInfo').val() || '').trim();
        let fileInput = document.getElementById('lnkt_returnImagesInput');
        let files = fileInput ? fileInput.files : null;

        $alert.addClass('d-none').removeClass('alert-danger alert-mint lnkt-alert-mint-pastel text-slate').text('');

        if (orderId <= 0) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Không xác định được đơn hàng cần hoàn trả.');
            return;
        }
        if (!reason) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Vui lòng chọn lý do hoàn hàng / trả hàng.');
            return;
        }
        if (desc.length < 5) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Vui lòng nhập mô tả tình trạng sản phẩm tối thiểu 5 ký tự.');
            return;
        }
        if (bankInfo.length < 6) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text('Vui lòng nhập đầy đủ số tài khoản Ngân hàng / Ví nhận tiền hoàn.');
            return;
        }
        let fileErr = lnktValidateReturnFiles(files);
        if (fileErr) {
            $alert.removeClass('d-none').addClass('alert-danger d-block').text(fileErr);
            return;
        }

        let origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang gửi yêu cầu...');

        let formData = new FormData($form[0]);
        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxSubmitReturn',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 20000,
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            },
            success: function (res) {
                if (!res || res.status !== 'success') {
                    $alert.removeClass('d-none').addClass('alert-danger d-block').text((res && res.message) ? res.message : 'Không thể gửi yêu cầu hoàn hàng lúc này!');
                    return;
                }
                let retReq = res.return_request || (res.data && res.data.return_request) || null;
                if (retReq && typeof window.lnktRenderReturnProgress === 'function') {
                    window.lnktRenderReturnProgress(retReq);
                }
                $('#lnkt_btnOpenReturnModal').addClass('d-none');
                lnktShowTrackingAlert('success', res.message || 'Đã gửi yêu cầu hoàn hàng thành công!');
                $alert.removeClass('d-none alert-danger')
                    .addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
                    .html('<i class="fa-solid fa-circle-check text-success mr-1"></i>' + lnktEscapeHtml(res.message || 'Đã gửi yêu cầu hoàn hàng thành công!'));
                setTimeout(function () {
                    $('#modalReturnRequest').modal('hide');
                }, 650);
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Lỗi gửi yêu cầu hoàn hàng. Vui lòng kiểm tra ảnh tải lên và thử lại!';
                $alert.removeClass('d-none').addClass('alert-danger d-block').text(errMsg);
            }
        });
    });

    function lnktShowReturnMgmtAlert(type, msg) {
        let $box = $('#lnkt_returnMgmtAlert');
        if (!$box.length) return;
        $box.removeClass('d-none alert-danger alert-mint lnkt-alert-mint-pastel text-slate');
        if (type === 'success') {
            $box.addClass('lnkt-alert-mint-pastel alert-mint text-slate d-block')
                .html('<i class="fa-solid fa-circle-check text-success mr-1"></i>' + lnktEscapeHtml(msg));
        } else {
            $box.addClass('alert-danger d-block')
                .html('<i class="fa-solid fa-triangle-exclamation mr-1"></i>' + lnktEscapeHtml(msg));
        }
    }

    function lnktSyncReturnCounts(counts) {
        if (!counts || typeof counts !== 'object') return;
        ['all', 'cho_duyet_hoan', 'da_duyet_hoan', 'dang_thu_hoi', 'hoan_tat_hoan', 'tu_choi_hoan'].forEach(function (k) {
            if (counts[k] !== undefined) {
                $('#lnkt_retCnt_' + k).text(parseInt(counts[k], 10) || 0);
            }
        });
        let pendingCnt = parseInt(counts.cho_duyet_hoan, 10) || 0;
        let $badge = $('#lnkt_pendingReturnBadge');
        if ($badge.length) {
            $badge.text(pendingCnt).toggleClass('d-none', pendingCnt <= 0);
        }
    }

    function lnktBuildReturnMgmtRowHtml(ret) {
        let rid = parseInt(ret.ma_hoan_hang, 10) || 0;
        let rcode = String(ret.ma_hoan_code || ('FK-RET-' + String(rid).padStart(5, '0')));
        let oid = parseInt(ret.ma_don_hang, 10) || 0;
        let ocode = String(ret.ma_van_don || ('FK-ORD-' + String(oid).padStart(6, '0')));
        let rst = String(ret.trang_thai_hoan || 'cho_duyet_hoan');
        let meta = ret.status_meta || {};
        let badgeCls = meta.badge_class || 'badge-gold';
        let badgeIcon = meta.icon || 'fa-hourglass-half';
        let badgeLbl = meta.label || 'Chờ duyệt hoàn';
        let custName = String(ret.khach_hang_ten || ret.ten_nguoi_nhan || 'Khách hàng');
        let custPhone = String(ret.khach_hang_sdt || ret.so_dien_thoai_nhan || '');
        let imgs = Array.isArray(ret.hinh_anh_list) ? ret.hinh_anh_list : [];
        let noteExec = String(ret.ghi_chu_xu_ly || '').trim();
        let searchTxt = [rcode, ocode, rid, oid, custName, custPhone, ret.ly_do_hoan || ''].join(' ');
        let safeRcodeJs = rcode.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

        let imgsHtml = '<span class="small text-muted-custom">Không có ảnh</span>';
        if (imgs.length > 0) {
            imgsHtml = '<div class="d-flex flex-wrap fk-btn-toolbar">';
            imgs.forEach(function (u) {
                let safeUrlJs = String(u || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                imgsHtml += '<img src="' + lnktEscapeHtml(u) + '" alt="Ảnh minh chứng hoàn hàng" class="lnkt-return-thumb" onclick="lnktOpenReturnLightbox(\'' + lnktEscapeHtml(safeUrlJs) + '\')">';
            });
            imgsHtml += '</div>';
        }

        let actionsHtml = '<span class="small text-muted-custom">Đã hoàn tất xử lý</span>';
        if (rst === 'cho_duyet_hoan') {
            actionsHtml =
                '<button type="button" class="btn btn-outline-info btn-sm btn-block rounded-pill font-weight-bold mb-1" onclick="lnktProcessReturnRequest(' + rid + ', \'da_duyet_hoan\', \'\', this)">' +
                    '<i class="fa-solid fa-circle-check mr-1"></i>Chấp nhận hoàn hàng' +
                '</button>' +
                '<button type="button" class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold" onclick="lnktOpenReturnRejectModal(' + rid + ', \'' + lnktEscapeHtml(safeRcodeJs) + '\')">' +
                    '<i class="fa-solid fa-circle-xmark mr-1"></i>Từ chối yêu cầu' +
                '</button>';
        } else if (rst === 'da_duyet_hoan') {
            actionsHtml =
                '<button type="button" class="btn btn-outline-primary btn-sm btn-block rounded-pill font-weight-bold mb-1" onclick="lnktProcessReturnRequest(' + rid + ', \'dang_thu_hoi\', \'\', this)">' +
                    '<i class="fa-solid fa-truck-arrow-right mr-1"></i>Shipper đang thu hồi hàng' +
                '</button>' +
                '<button type="button" class="btn btn-outline-success btn-sm btn-block rounded-pill font-weight-bold" onclick="lnktProcessReturnRequest(' + rid + ', \'hoan_tat_hoan\', \'\', this)">' +
                    '<i class="fa-solid fa-hand-holding-dollar mr-1"></i>Hoàn tiền &amp; Nhập kho' +
                '</button>';
        } else if (rst === 'dang_thu_hoi') {
            actionsHtml =
                '<button type="button" class="btn btn-outline-success btn-sm btn-block rounded-pill font-weight-bold" onclick="lnktProcessReturnRequest(' + rid + ', \'hoan_tat_hoan\', \'\', this)">' +
                    '<i class="fa-solid fa-hand-holding-dollar mr-1"></i>Xác nhận hoàn tiền &amp; Nhập lại kho' +
                '</button>';
        }
        if (rst !== 'tu_choi_hoan') {
            let safeBankJs = String(ret.thong_tin_nhan_tien || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            let refundAmtNum = Number(ret.so_tien_hoan || 0);
            actionsHtml +=
                '<button type="button" class="btn btn-outline-warning text-dark btn-sm btn-block rounded-pill font-weight-bold mt-1" ' +
                'onclick="lnktOpenRefundQrModal(' + rid + ', \'' + lnktEscapeHtml(safeRcodeJs) + '\', ' + refundAmtNum + ', \'' + lnktEscapeHtml(safeBankJs) + '\')">' +
                    '<i class="fa-solid fa-qrcode mr-1"></i>QR Hoàn tiền' +
                '</button>';
        }

        return (
            '<tr class="lnkt-return-mgmt-row" id="lnkt_returnRow_' + rid + '" data-return-id="' + rid + '" data-status="' + lnktEscapeHtml(rst) + '" data-search="' + lnktEscapeHtml(searchTxt) + '">' +
                '<td>' +
                    '<div class="font-weight-bold text-peach text-nowrap">#' + lnktEscapeHtml(rcode) + '</div>' +
                    '<a href="/FashionKids/index.php?c=order&a=tracking&id=' + oid + '" target="_blank" class="small font-weight-bold text-slate text-nowrap">Đơn #' + lnktEscapeHtml(ocode) + '</a>' +
                    '<small class="d-block text-muted-custom">' + lnktEscapeHtml(ret.ngay_yeu_cau_formatted || lnktFormatMysqlDt(ret.ngay_yeu_cau)) + '</small>' +
                    (ret.nhan_vien_xu_ly_ten ? '<small class="d-block text-mint-dark">NV: <strong>' + lnktEscapeHtml(ret.nhan_vien_xu_ly_ten) + '</strong></small>' : '') +
                '</td>' +
                '<td>' +
                    '<div class="font-weight-bold text-slate">' + lnktEscapeHtml(custName) + '</div>' +
                    '<small class="text-muted-custom d-block mb-1"><i class="fa-solid fa-phone mr-1"></i>' + lnktEscapeHtml(custPhone) + '</small>' +
                    '<div class="small font-weight-bold text-danger">' + lnktEscapeHtml(ret.ly_do_hoan || '') + '</div>' +
                    (ret.mo_ta_chi_tiet ? '<small class="text-muted-custom d-block">' + lnktEscapeHtml(ret.mo_ta_chi_tiet) + '</small>' : '') +
                    (ret.item_summary ? '<small class="text-slate d-block mt-1"><i class="fa-solid fa-shirt mr-1 text-peach"></i>' + lnktEscapeHtml(ret.item_summary) + '</small>' : '') +
                '</td>' +
                '<td>' + imgsHtml + '</td>' +
                '<td class="text-right">' +
                    '<div class="font-weight-bold text-peach text-nowrap">' + lnktEscapeHtml(ret.so_tien_hoan_formatted || lnktFormatVnd(ret.so_tien_hoan || 0)) + '</div>' +
                    '<small class="text-slate d-block font-weight-bold">' + lnktEscapeHtml(ret.thong_tin_nhan_tien || 'Chưa ghi TK') + '</small>' +
                '</td>' +
                '<td class="text-center">' +
                    '<span class="badge badge-pill ' + lnktEscapeHtml(badgeCls) + ' px-2 py-1 text-nowrap">' +
                        '<i class="fa-solid ' + lnktEscapeHtml(badgeIcon) + ' mr-1"></i>' + lnktEscapeHtml(badgeLbl) +
                    '</span>' +
                    (noteExec !== '' ? '<small class="d-block text-muted-custom mt-1">Ghi chú: ' + lnktEscapeHtml(noteExec) + '</small>' : '') +
                '</td>' +
                '<td class="text-center">' + actionsHtml + '</td>' +
            '</tr>'
        );
    }

    window.lnktApplyReturnClientFilter = function () {
        let kw = ($('#lnkt_returnSearchInput').val() || '').toLowerCase().trim();
        let stFilter = lnktActiveReturnFilter || 'all';
        let visible = 0;

        $('.lnkt-return-mgmt-row').each(function () {
            let $r = $(this);
            let rSt = $r.attr('data-status') || '';
            let rSearch = ($r.attr('data-search') || '').toLowerCase();
            let okSt = (stFilter === 'all' || rSt === stFilter);
            let okKw = (!kw || rSearch.indexOf(kw) !== -1);
            if (okSt && okKw) {
                $r.removeClass('d-none');
                visible++;
            } else {
                $r.addClass('d-none');
            }
        });

        $('#lnkt_returnFilteredEmptyRow').remove();
        if (visible === 0 && $('#lnkt_returnMgmtBody .lnkt-return-mgmt-row').length > 0) {
            $('#lnkt_returnMgmtBody').append(
                '<tr id="lnkt_returnFilteredEmptyRow"><td colspan="6" class="text-center py-4 text-muted-custom">' +
                '<i class="fa-solid fa-filter mr-2"></i>Không có yêu cầu hoàn hàng phù hợp với bộ lọc hiện tại.</td></tr>'
            );
        }
    };

    window.lnktFilterReturnRequests = function (statusKey, btnEl) {
        lnktActiveReturnFilter = statusKey || 'all';
        $('#lnkt_returnStatusFilterPills .lnkt-ret-filter-pill')
            .removeClass('btn-pastel-primary active')
            .addClass('btn-light border text-slate');
        if (btnEl) {
            $(btnEl)
                .removeClass('btn-light border text-slate')
                .addClass('btn-pastel-primary active');
        }
        window.lnktApplyReturnClientFilter();
    };

    window.lnktRefreshReturnRequests = function (isSilent) {
        if (!$('#lnkt_returnMgmtPanel').length) return;
        if (isSilent && lnktRetMgmtPollInFlight) return;
        lnktRetMgmtPollInFlight = true;

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxGetReturnRequests',
            type: 'GET',
            dataType: 'json',
            data: { status: 'all', q: '' },
            timeout: 8000,
            complete: function () {
                lnktRetMgmtPollInFlight = false;
            },
            success: function (res) {
                if (!res || res.status !== 'success') return;
                let counts = res.counts || (res.data && res.data.counts) || null;
                if (counts) {
                    lnktSyncReturnCounts(counts);
                }
                let list = Array.isArray(res.requests) ? res.requests : (res.data && Array.isArray(res.data.requests) ? res.data.requests : []);
                let $tbody = $('#lnkt_returnMgmtBody');
                if ($tbody.length) {
                    if (list.length === 0) {
                        $tbody.html(
                            '<tr id="lnkt_returnEmptyRow"><td colspan="6" class="text-center py-4 text-muted-custom">' +
                            '<i class="fa-solid fa-box-open mr-2"></i>Hiện chưa có yêu cầu hoàn hàng / trả hàng nào từ khách hàng.</td></tr>'
                        );
                    } else {
                        let html = '';
                        list.forEach(function (item) {
                            html += lnktBuildReturnMgmtRowHtml(item);
                        });
                        $tbody.html(html);
                    }
                }
                window.lnktApplyReturnClientFilter();
                if (!isSilent) {
                    lnktShowReturnMgmtAlert('success', res.message || 'Đã làm mới danh sách yêu cầu hoàn hàng.');
                }
            },
            error: function () {
                if (!isSilent) {
                    lnktShowReturnMgmtAlert('error', 'Không thể tải danh sách yêu cầu hoàn hàng lúc này!');
                }
            }
        });
    };

    window.lnktOpenReturnRejectModal = function (returnId, returnCode) {
        $('#lnkt_rejectReturnId').val(parseInt(returnId, 10) || 0);
        $('#lnkt_rejectModalCode').text('#' + String(returnCode || returnId));
        $('#lnkt_rejectReasonNoteInput').val('');
        $('#lnkt_rejectNoteAlert').addClass('d-none').text('');
        $('#modalReturnRejectNote').modal('show');
    };

    $(document).on('submit', '#lnkt_formRejectReturnNote', function (e) {
        e.preventDefault();
        let returnId = parseInt($('#lnkt_rejectReturnId').val(), 10) || 0;
        let note = ($('#lnkt_rejectReasonNoteInput').val() || '').trim();
        let $alert = $('#lnkt_rejectNoteAlert');
        $alert.addClass('d-none').text('');
        if (returnId <= 0) {
            $alert.removeClass('d-none').text('Không xác định được yêu cầu hoàn hàng!');
            return;
        }
        if (note.length < 3) {
            $alert.removeClass('d-none').text('Vui lòng nhập lý do từ chối tối thiểu 3 ký tự để thông báo cho khách hàng.');
            return;
        }
        window.lnktProcessReturnRequest(returnId, 'tu_choi_hoan', note, '#lnkt_btnConfirmRejectReturn', function (ok, errMsg) {
            if (ok) {
                $('#modalReturnRejectNote').modal('hide');
            } else if (errMsg) {
                $alert.removeClass('d-none').text(errMsg);
            }
        });
    });

    window.lnktProcessReturnRequest = function (returnId, newStatus, note, btnEl, onDone) {
        let $btn = btnEl ? $(btnEl) : $();
        let origHtml = $btn.length ? $btn.html() : '';
        if ($btn.length) {
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>Đang xử lý...');
        }

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxProcessReturn',
            type: 'POST',
            dataType: 'json',
            data: {
                return_id: returnId,
                new_status: newStatus,
                note: note || ''
            },
            timeout: 12000,
            complete: function () {
                if ($btn.length && document.body.contains($btn[0])) {
                    $btn.prop('disabled', false).html(origHtml);
                }
            },
            success: function (res) {
                if (!res || res.status !== 'success') {
                    let msg = (res && res.message) ? res.message : 'Không thể cập nhật yêu cầu hoàn hàng!';
                    lnktShowReturnMgmtAlert('error', msg);
                    if (typeof onDone === 'function') onDone(false, msg);
                    return;
                }
                let counts = res.counts || (res.data && res.data.counts) || null;
                if (counts) {
                    lnktSyncReturnCounts(counts);
                }
                let ret = res.return_request || (res.data && res.data.return_request) || null;
                if (ret && ret.ma_hoan_hang) {
                    let $existing = $('#lnkt_returnRow_' + ret.ma_hoan_hang);
                    let rowHtml = lnktBuildReturnMgmtRowHtml(ret);
                    if ($existing.length) {
                        $existing.replaceWith(rowHtml);
                    } else {
                        $('#lnkt_returnEmptyRow').remove();
                        $('#lnkt_returnMgmtBody').prepend(rowHtml);
                    }
                    window.lnktApplyReturnClientFilter();
                } else {
                    window.lnktRefreshReturnRequests(true);
                }

                let successMsg = res.message || 'Đã cập nhật trạng thái hoàn hàng thành công!';
                if (newStatus === 'hoan_tat_hoan' && res.customer_tier) {
                    successMsg += ' Điểm VIP khách còn: ' + Number(res.customer_points || 0).toLocaleString('vi-VN') + ' điểm (' + res.customer_tier + ').';
                }
                lnktShowReturnMgmtAlert('success', successMsg);
                if (typeof onDone === 'function') onDone(true);
            },
            error: function (xhr) {
                let errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Lỗi máy chủ khi xử lý yêu cầu hoàn hàng!';
                lnktShowReturnMgmtAlert('error', errMsg);
                if (typeof onDone === 'function') onDone(false, errMsg);
            }
        });
    };

    if ($('#lnkt_returnMgmtPanel').length) {
        setInterval(function () {
            window.lnktRefreshReturnRequests(true);
        }, 4000);
    }
});
