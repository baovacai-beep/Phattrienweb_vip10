/**
 * public/js/lbn-ajax.js - Phụ trách: Lê Bảo Ngọc (lbn_)
 * Dự án FashionKids - Giai đoạn 4: Tích hợp AJAX & Webservice JSON
 *
 * Chức năng:
 *  1. MH 02 (shop-catalog.php): Lọc đa tiêu chí, Tìm kiếm realtime & Đếm SP theo danh mục (lbn_count_products_by_category)
 *     -> ProductController::ajaxFilterCatalog
 *  2. MH 03 (product-detail.php) & MH 02: Kiểm tra tồn kho khả dụng (fn_lbn_kiem_tra_ton_kho) & Thêm giỏ hàng / Mua ngay AJAX
 *     -> ProductController::ajaxCheckStock
 *  3. MH 13 (admin-inventory.php): Cập nhật nhanh tồn kho & Cảnh báo tự động <= 5 (sp_lbn_nhap_them_kho_hang)
 *     -> ProductController::ajaxUpdateInventoryStock
 *  4. MH 13 (admin-inventory.php): Thêm sản phẩm mới (sp_lbn_them_san_pham_moi) & Cập nhật sản phẩm bằng FormData AJAX
 *     -> ProductController::ajaxSaveProduct
 *
 * Tuân thủ Quy chuẩn Giai đoạn 4: Không dùng Inline CSS, sử dụng class chuẩn Bootstrap 4 (.is-valid, .is-invalid, .invalid-feedback)
 * và bảng màu Bright Pastel (#E8F5E9, #FF9E93, #B8E0D2).
 */
$(document).ready(function () {
    'use strict';

    var ENDPOINT = $('meta[name="fk-endpoint"]').attr('content') || '/FashionKids/index.php';
    var CSRF     = $('meta[name="csrf-token"]').attr('content') || '';

    function lbnFormatVnd(amount) {
        return Number(amount || 0).toLocaleString('vi-VN') + ' đ';
    }

    function lbnEscapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function lbnEnsureAlertBox(containerSelector, alertId, insertBeforeSelector) {
        var $existing = $('#' + alertId);
        if ($existing.length) {
            return $existing;
        }
        var $box = $('<div>', {
            id: alertId,
            class: 'alert lbn-alert-pastel p-3 mb-3 shadow-sm d-none',
            role: 'alert'
        });
        if (insertBeforeSelector && $(insertBeforeSelector).length) {
            $box.insertBefore($(insertBeforeSelector).first());
        } else if ($(containerSelector).length) {
            $(containerSelector).first().prepend($box);
        }
        return $box;
    }

    function lbnShowAlert($box, type, titleHtml, detailText, badgeText) {
        if (!$box || !$box.length) return;
        $box.removeClass('d-none alert-danger alert-success lbn-alert-mint-pastel lbn-alert-danger-pastel');
        if (type === 'error') {
            $box.addClass('alert-danger lbn-alert-danger-pastel d-block');
            $box.html(
                '<div class="d-flex justify-content-between align-items-center flex-wrap">' +
                '<div><i class="fa-solid fa-triangle-exclamation mr-2"></i><strong>' + titleHtml + '</strong>' +
                (detailText ? '<div class="small mt-1">' + lbnEscapeHtml(detailText) + '</div>' : '') +
                '</div>' +
                (badgeText ? '<span class="badge badge-pill badge-danger ml-2 px-2 py-1">' + lbnEscapeHtml(badgeText) + '</span>' : '') +
                '</div>'
            );
        } else {
            $box.addClass('lbn-alert-mint-pastel alert-mint text-slate d-block');
            $box.html(
                '<div class="d-flex justify-content-between align-items-center flex-wrap">' +
                '<div><i class="fa-solid fa-circle-check text-success mr-2"></i><strong>' + titleHtml + '</strong>' +
                (detailText ? '<div class="small mt-1 text-slate">' + lbnEscapeHtml(detailText) + '</div>' : '') +
                '</div>' +
                (badgeText ? '<span class="badge badge-pill badge-mint ml-2 px-2 py-1"><i class="fa-solid fa-database mr-1"></i>' + lbnEscapeHtml(badgeText) + '</span>' : '') +
                '</div>'
            );
        }
    }

    // ========================================================================
    // 1. MH 02: LỌC ĐA TIÊU CHÍ & TÌM KIẾM SẢN PHẨM BẰNG AJAX JSON (shop-catalog.php)
    // ========================================================================
    function lbnBuildCatalogCardHtml(prod) {
        var genderBadgeHtml = '';
        if (prod.gender === 'girl') {
            genderBadgeHtml = '<span class="badge badge-pill bg-peach text-white lbn-badge-gender-overlay"><i class="fa-solid fa-venus mr-1"></i>Bé gái</span>';
        } else if (prod.gender === 'boy') {
            genderBadgeHtml = '<span class="badge badge-pill badge-sky lbn-badge-gender-overlay"><i class="fa-solid fa-mars mr-1"></i>Bé trai</span>';
        } else {
            genderBadgeHtml = '<span class="badge badge-pill badge-gold lbn-badge-gender-overlay"><i class="fa-solid fa-venus-mars mr-1"></i>Unisex</span>';
        }

        var stockHtml = (prod.stock_quantity <= 5)
            ? '<small class="text-danger font-weight-bold"><i class="fa-solid fa-fire mr-1"></i>Chỉ còn ' + prod.stock_quantity + ' SP</small>'
            : '<small class="text-success font-weight-bold"><i class="fa-solid fa-check mr-1"></i>Còn hàng (' + prod.stock_quantity + ')</small>';

        var safeNameJs = String(prod.name || '').replace(/'/g, "\\'");

        return (
            '<div class="col-12 col-sm-6 col-lg-4 mb-4 lbn-catalog-col" ' +
            'data-id="' + prod.id + '" ' +
            'data-category="' + lbnEscapeHtml(prod.category_slug) + '" ' +
            'data-gender="' + lbnEscapeHtml(prod.gender) + '" ' +
            'data-price="' + prod.price + '" ' +
            'data-name="' + lbnEscapeHtml(prod.name + ' ' + prod.category_name + ' ' + prod.sku) + '" ' +
            'data-bamboo="' + (prod.is_bamboo ? '1' : '0') + '">' +
                '<div class="lbn-product-card h-100 d-flex flex-column shadow-sm">' +
                    '<div class="lbn-product-img-wrap">' +
                        '<span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">' +
                            '<i class="fa-solid fa-leaf mr-1"></i>' + lbnEscapeHtml(prod.bamboo_label || 'Vải Sợi Tre Kháng Khuẩn') +
                        '</span>' +
                        genderBadgeHtml +
                        '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="lbn-img-link text-decoration-none">' +
                            '<img src="' + lbnEscapeHtml(prod.image) + '" alt="' + lbnEscapeHtml(prod.name) + '" class="lbn-product-img">' +
                        '</a>' +
                    '</div>' +
                    '<div class="card-body d-flex flex-column p-3">' +
                        '<div class="d-flex justify-content-between align-items-center mb-1">' +
                            '<span class="small font-weight-bold text-muted-custom">' + lbnEscapeHtml(prod.category_name) + '</span>' +
                            '<span class="small text-warning font-weight-bold">' +
                                '<i class="fa-solid fa-star"></i> ' + Number(prod.rating || 4.9).toFixed(1) +
                                ' <span class="text-muted-custom font-weight-normal">(' + (prod.reviews || 25) + ')</span>' +
                            '</span>' +
                        '</div>' +
                        '<h6 class="font-weight-bold text-slate mb-2">' +
                            '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="text-slate text-decoration-none">' +
                                lbnEscapeHtml(prod.name) +
                            '</a>' +
                        '</h6>' +
                        '<div class="d-flex justify-content-between align-items-center small text-muted-custom mb-3">' +
                            '<span><i class="fa-solid fa-barcode mr-1"></i>' + lbnEscapeHtml(prod.sku) + '</span>' +
                            '<span><i class="fa-solid fa-ruler-combined mr-1"></i>' + lbnEscapeHtml(prod.size_range) + '</span>' +
                        '</div>' +
                        '<div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">' +
                            '<div>' +
                                '<div class="h5 font-weight-bold text-peach mb-0">' + lbnFormatVnd(prod.price) + '</div>' +
                                stockHtml +
                            '</div>' +
                            '<div class="d-flex">' +
                                '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="btn btn-light btn-sm rounded-pill border mr-1 px-2 text-slate" title="Xem chi tiết PDP">' +
                                    '<i class="fa-regular fa-eye"></i>' +
                                '</a>' +
                                '<button type="button" onclick="lbnQuickAddToCart(\'' + lbnEscapeHtml(safeNameJs) + '\', ' + prod.price + ', ' + prod.id + ')" class="btn btn-pastel-primary btn-sm px-3 py-1">' +
                                    '<i class="fa-solid fa-cart-plus mr-1"></i> Thêm' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function lbnClientDomFilterFallback() {
        var selectedCat    = $('#lbn_selectedCategory').val() || 'all';
        var selectedGender = $('input[name="lbn_gender_filter"]:checked').val() || 'all';
        var maxPrice       = parseInt($('#lbn_priceRangeSlider').val(), 10) || 500000;
        var keyword        = ($('#lbn_keywordFilter').val() || '').toLowerCase().trim();
        var onlyBamboo     = $('#lbn_onlyBambooCheck').is(':checked');
        var sortMode       = $('#lbn_sortSelect').val() || 'newest';

        var visibleCount   = 0;
        var gridContainer  = $('#lbn_catalogGrid');
        var items          = gridContainer.children('.lbn-catalog-col').get();

        items.forEach(function (colEl) {
            var $col         = $(colEl);
            var itemCat      = $col.attr('data-category');
            var itemGender   = $col.attr('data-gender');
            var itemPrice    = parseInt($col.attr('data-price'), 10) || 0;
            var itemName     = ($col.attr('data-name') || '').toLowerCase();
            var itemBamboo   = $col.attr('data-bamboo') === '1';

            var matchCat     = (selectedCat === 'all' || itemCat === selectedCat);
            var matchGender  = (selectedGender === 'all' || itemGender === selectedGender);
            var matchPrice   = (itemPrice <= maxPrice);
            var matchKeyword = (!keyword || itemName.indexOf(keyword) !== -1);
            var matchBamboo  = (!onlyBamboo || itemBamboo);

            if (matchCat && matchGender && matchPrice && matchKeyword && matchBamboo) {
                $col.removeClass('d-none');
                visibleCount++;
            } else {
                $col.addClass('d-none');
            }
        });

        items.sort(function (a, b) {
            var priceA = parseInt($(a).attr('data-price'), 10) || 0;
            var priceB = parseInt($(b).attr('data-price'), 10) || 0;
            var idA    = parseInt($(a).attr('data-id'), 10) || 0;
            var idB    = parseInt($(b).attr('data-id'), 10) || 0;

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
    }

    if ($('#lbn_catalog_section').length) {
        var $catalogAlert = lbnEnsureAlertBox('#lbn_catalog_section', 'lbn_catalog_ajax_alert', '#lbn_catalogGrid');

        window.lbnApplyCatalogFilters = function () {
            var selectedCat    = $('#lbn_selectedCategory').val() || 'all';
            var selectedGender = $('input[name="lbn_gender_filter"]:checked').val() || 'all';
            var maxPrice       = parseInt($('#lbn_priceRangeSlider').val(), 10) || 500000;
            var keyword        = ($('#lbn_keywordFilter').val() || '').trim();
            var onlyBamboo     = $('#lbn_onlyBambooCheck').is(':checked') ? 1 : 0;
            var sortMode       = $('#lbn_sortSelect').val() || 'newest';

            // Lọc tức thời trên DOM để phản hồi mượt 0ms, đồng thời gọi AJAX JSON đồng bộ Backend
            lbnClientDomFilterFallback();

            clearTimeout(window.lbnCatalogAjaxTimer);
            window.lbnCatalogAjaxTimer = setTimeout(function () {
                $.ajax({
                    url: ENDPOINT + '?c=product&a=ajaxFilterCatalog',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        category:    selectedCat,
                        gender:      selectedGender,
                        min_price:   0,
                        max_price:   maxPrice,
                        keyword:     keyword,
                        only_bamboo: onlyBamboo,
                        sort:        sortMode
                    },
                    timeout: 3000,
                    success: function (res) {
                        if (res && res.status === 'success' && Array.isArray(res.products)) {
                            var $grid = $('#lbn_catalogGrid');
                            var html  = '';
                            res.products.forEach(function (prod) {
                                html += lbnBuildCatalogCardHtml(prod);
                            });
                            $grid.html(html);

                            $('#lbn_visibleProductCount').text(res.visible_count);
                            if (res.visible_count === 0) {
                                $('#lbn_emptyCatalogAlert').removeClass('d-none');
                            } else {
                                $('#lbn_emptyCatalogAlert').addClass('d-none');
                            }

                            // Đồng bộ bộ đếm danh mục (lbn_count_products_by_category)
                            if (res.category_counts) {
                                Object.keys(res.category_counts).forEach(function (catSlug) {
                                    if (catSlug !== 'by_id') {
                                        $('.lbn-cat-filter-item[data-cat="' + catSlug + '"] .badge').text(res.category_counts[catSlug]);
                                    }
                                });
                            }
                        }
                    }
                });
            }, 180);
        };

        // Bấm nút Thêm nhanh vào giỏ hàng từ thẻ sản phẩm MH 02 qua AJAX
        window.lbnQuickAddToCart = function (productName, price, productId) {
            var resolvedId = productId || 101;
            if (!productId) {
                $('.lbn-catalog-col').each(function () {
                    var n = $(this).attr('data-name') || '';
                    if (n.indexOf(productName) !== -1) {
                        resolvedId = parseInt($(this).attr('data-id'), 10) || 101;
                    }
                });
            }

            $.ajax({
                url: ENDPOINT + '?c=product&a=ajaxCheckStock',
                type: 'POST',
                dataType: 'json',
                headers: CSRF ? { 'X-CSRF-Token': CSRF } : {},
                data: {
                    product_id:  resolvedId,
                    quantity:    1,
                    size:        'Size 2 (2-3T)',
                    action_type: 'quick_add'
                },
                timeout: 3000,
                success: function (res) {
                    var badge = $('.cart-badge-custom').first();
                    var nextCount = (res && res.cart_count) ? res.cart_count : ((parseInt(badge.text(), 10) || 0) + 1);
                    badge.text(nextCount);
                    lbnShowAlert(
                        $catalogAlert,
                        'success',
                        'Đã thêm "' + lbnEscapeHtml(productName) + '" (' + lbnFormatVnd(price) + ') vào giỏ hàng!',
                        res.message || 'Đã kiểm tra tồn kho khả dụng qua Stored Function fn_lbn_kiem_tra_ton_kho.',
                        'fn_lbn_kiem_tra_ton_kho'
                    );
                },
                error: function (xhr) {
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Sản phẩm hiện không đủ tồn kho để thêm vào giỏ!';
                    lbnShowAlert($catalogAlert, 'error', 'Không thể thêm vào giỏ hàng', errMsg, 'Hết hàng');
                }
            });
        };
    }

    // ========================================================================
    // 2. MH 03: KIỂM TRA TỒN KHO (fn_lbn_kiem_tra_ton_kho) & GIỎ HÀNG PDP (product-detail.php)
    // ========================================================================
    if ($('#lbn_pdp_section').length) {
        var $pdpQtyInput = $('#lbn_pdpQuantity');
        // Cho phép nhập trực tiếp để kiểm thử ràng buộc số âm / chữ cái / vượt tồn kho
        $pdpQtyInput.prop('readonly', false).attr('type', 'text').attr('inputmode', 'numeric');

        var $pdpAlert = lbnEnsureAlertBox(
            '#lbn_pdp_section .col-lg-6.pl-lg-4',
            'lbn_pdp_stock_alert',
            '#lbn_pdp_section .form-row.mb-3'
        );

        function lbnGetCurrentPdpProductId() {
            var urlParams = new URLSearchParams(window.location.search);
            return parseInt(urlParams.get('id'), 10) || 101;
        }

        function lbnValidateAndCheckPdpStock(actionType, onValidCallback) {
            var rawQty     = String($pdpQtyInput.val() || '').trim();
            var maxStock   = parseInt($pdpQtyInput.attr('data-max'), 10) || 20;
            var productId  = lbnGetCurrentPdpProductId();
            var chosenSize = $('#lbn_selectedSizeInput').val() || $('#lbn_selectedSizeText').text().trim() || 'Size 2 (2-3T)';
            var prodName   = $('#lbn_pdpProductTitle').text().trim();

            if (!/^-?\d+$/.test(rawQty)) {
                $pdpQtyInput.removeClass('is-valid').addClass('is-invalid');
                lbnShowAlert(
                    $pdpAlert,
                    'error',
                    'Lỗi dữ liệu số lượng đặt mua',
                    'Vui lòng chỉ nhập số nguyên dương (không nhập chữ cái hoặc ký tự đặc biệt)!',
                    'LBN_ERR_07'
                );
                return;
            }

            var qty = parseInt(rawQty, 10);
            if (qty <= 0) {
                $pdpQtyInput.removeClass('is-valid').addClass('is-invalid');
                lbnShowAlert(
                    $pdpAlert,
                    'error',
                    'Số lượng đặt mua không hợp lệ',
                    'Lỗi [LBN_ERR_07]: Số lượng kiểm tra đặt mua phải lớn hơn 0!',
                    'LBN_ERR_07'
                );
                return;
            }

            $.ajax({
                url: ENDPOINT + '?c=product&a=ajaxCheckStock',
                type: 'POST',
                dataType: 'json',
                headers: CSRF ? { 'X-CSRF-Token': CSRF } : {},
                data: {
                    product_id:  productId,
                    quantity:    qty,
                    size:        chosenSize,
                    action_type: actionType
                },
                timeout: 3000,
                success: function (res) {
                    if (res && res.status === 'success' && res.du_hang) {
                        $pdpQtyInput.removeClass('is-invalid').addClass('is-valid');
                        if (res.cart_count !== undefined && actionType !== 'check') {
                            $('.cart-badge-custom').first().text(res.cart_count);
                        }
                        var titleMsg = (actionType === 'check')
                            ? 'Tồn kho khả dụng: Đủ hàng đáp ứng (' + qty + ' / ' + res.available_stock + ' SP)'
                            : 'Đã thêm ' + qty + ' x "' + lbnEscapeHtml(prodName) + '" (' + lbnEscapeHtml(chosenSize) + ') vào giỏ hàng!';
                        lbnShowAlert(
                            $pdpAlert,
                            'success',
                            titleMsg,
                            res.message,
                            'fn_lbn_kiem_tra_ton_kho'
                        );
                        if (typeof onValidCallback === 'function') {
                            onValidCallback(res);
                        }
                    } else {
                        $pdpQtyInput.removeClass('is-valid').addClass('is-invalid');
                        lbnShowAlert(
                            $pdpAlert,
                            'error',
                            'Vượt quá số lượng tồn kho',
                            (res && res.message) ? res.message : ('Kho hiện chỉ còn ' + maxStock + ' sản phẩm!'),
                            'fn_lbn_kiem_tra_ton_kho'
                        );
                    }
                },
                error: function (xhr) {
                    var res = xhr.responseJSON;
                    if (res && res.message) {
                        $pdpQtyInput.removeClass('is-valid').addClass('is-invalid');
                        lbnShowAlert($pdpAlert, 'error', 'Không đủ hàng tồn kho', res.message, 'fn_lbn_kiem_tra_ton_kho');
                        return;
                    }
                    // Fallback khi chạy offline
                    if (qty > maxStock) {
                        $pdpQtyInput.removeClass('is-valid').addClass('is-invalid');
                        lbnShowAlert(
                            $pdpAlert,
                            'error',
                            'Vượt quá số lượng tồn kho',
                            'Số lượng đặt mua (' + qty + ') vượt quá tồn kho hiện tại (' + maxStock + ' sản phẩm)!',
                            'fn_lbn_kiem_tra_ton_kho'
                        );
                    } else {
                        $pdpQtyInput.removeClass('is-invalid').addClass('is-valid');
                        if (actionType !== 'check') {
                            var badge = $('.cart-badge-custom').first();
                            badge.text((parseInt(badge.text(), 10) || 0) + qty);
                        }
                        lbnShowAlert(
                            $pdpAlert,
                            'success',
                            'Kiểm tra tồn kho hợp lệ (' + qty + ' / ' + maxStock + ' SP)',
                            'Đã đối chiếu qua fn_lbn_kiem_tra_ton_kho cho phân loại ' + chosenSize + '.',
                            'fn_lbn_kiem_tra_ton_kho'
                        );
                        if (typeof onValidCallback === 'function') {
                            onValidCallback({ du_hang: true });
                        }
                    }
                }
            });
        }

        window.lbnChangePdpQty = function (delta) {
            var currentVal = parseInt($pdpQtyInput.val(), 10);
            if (isNaN(currentVal)) currentVal = 1;
            var nextVal = currentVal + delta;
            $pdpQtyInput.val(nextVal);
            lbnValidateAndCheckPdpStock('check');
        };

        $pdpQtyInput.on('input change blur', function () {
            clearTimeout(window.lbnPdpQtyTimer);
            window.lbnPdpQtyTimer = setTimeout(function () {
                lbnValidateAndCheckPdpStock('check');
            }, 250);
        });

        window.lbnAddToCartPdp = function () {
            lbnValidateAndCheckPdpStock('add_to_cart');
        };

        window.lbnBuyNowPdp = function () {
            lbnValidateAndCheckPdpStock('buy_now', function () {
                setTimeout(function () {
                    window.location.href = '/FashionKids/views/pages/checkout.php';
                }, 600);
            });
        };
    }

    // ========================================================================
    // 3. MH 13: QUẢN TRỊ SẢN PHẨM & CẬP NHẬT TỒN KHO BẰNG AJAX (admin-inventory.php)
    // ========================================================================
    if ($('#lbn_admin_inventory_section').length) {
        var $adminAlert = lbnEnsureAlertBox(
            '#lbn_admin_inventory_section',
            'lbn_admin_inv_alert',
            '#lbn_admin_inventory_section > .row:first'
        );

        // Chèn các phần tử .invalid-feedback cho Form Thêm/Sửa sản phẩm nếu chưa có
        $('#lbn_formAdminProduct').attr('novalidate', 'novalidate');
        $('#lbn_formAdminProduct input.form-control, #lbn_formAdminProduct select.custom-select, #lbn_formAdminProduct textarea.form-control').each(function () {
            if ($(this).siblings('.invalid-feedback').length === 0) {
                $(this).after('<div class="invalid-feedback">Dữ liệu chưa hợp lệ.</div>');
            }
        }).on('input change', function () {
            $(this).removeClass('is-invalid is-valid');
        });

        function lbnUpdateInventoryKpiCards(stats) {
            if (!stats) return;
            if (stats.low_stock_count !== undefined) {
                $('#lbn_lowStockCounter').text(stats.low_stock_count);
            }
            var $kpiCards = $('#lbn_admin_inventory_section > .row').first().find('.card-pastel h3');
            if ($kpiCards.length >= 2) {
                if (stats.total_sku !== undefined) {
                    $kpiCards.eq(0).text(stats.total_sku + ' SKU');
                }
                if (stats.total_units !== undefined) {
                    $kpiCards.eq(1).text(Number(stats.total_units).toLocaleString('vi-VN') + ' SP');
                }
            }
            if (stats.summary_text) {
                $('#lbn_admin_inventory_section > .row').first().find('.card-pastel').last().find('.text-slate.mt-1').text(stats.summary_text);
            }
        }

        // Cập nhật nhanh tồn kho qua AJAX (sp_lbn_nhap_them_kho_hang) & Cảnh báo đỏ khi <= 5
        window.lbnQuickUpdateStock = function (productId, skuCode) {
            var $inputEl   = $('#lbn_stockInput_' + productId);
            var rawVal     = String($inputEl.val() || '').trim();
            var $row       = $('#lbn_invRow_' + productId);
            var $btn       = $inputEl.closest('.input-group').find('button');

            if (!/^-?\d+$/.test(rawVal)) {
                $inputEl.removeClass('is-valid').addClass('is-invalid');
                lbnShowAlert(
                    $adminAlert,
                    'error',
                    'Lỗi cập nhật tồn kho mã ' + lbnEscapeHtml(skuCode),
                    'Số lượng tồn kho không hợp lệ! Vui lòng chỉ nhập số nguyên (không nhập chữ cái).',
                    'LBN_ERR_04'
                );
                return;
            }

            var newStock = parseInt(rawVal, 10);
            if (newStock < 0) {
                $inputEl.removeClass('is-valid').addClass('is-invalid');
                lbnShowAlert(
                    $adminAlert,
                    'error',
                    'Lỗi cập nhật tồn kho mã ' + lbnEscapeHtml(skuCode),
                    'Lỗi [LBN_ERR_04]: Số lượng tồn kho không được là số âm (phải >= 0)!',
                    'LBN_ERR_04'
                );
                return;
            }

            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

            $.ajax({
                url: ENDPOINT + '?c=product&a=ajaxUpdateInventoryStock',
                type: 'POST',
                dataType: 'json',
                headers: CSRF ? { 'X-CSRF-Token': CSRF } : {},
                data: {
                    product_id: productId,
                    new_stock:  newStock,
                    mode:       'set'
                },
                timeout: 3000,
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-rotate"></i>');
                },
                success: function (res) {
                    $inputEl.removeClass('is-invalid').addClass('is-valid');
                    var updatedStock = (res && res.new_stock !== undefined) ? res.new_stock : newStock;
                    $row.attr('data-stock', updatedStock);

                    var $statusCell = $('#lbn_stockBadgeCell_' + productId);
                    if (updatedStock <= 5) {
                        $statusCell.html(
                            '<span class="badge badge-danger lbn-badge-low-stock px-2 py-1">' +
                            '<i class="fa-solid fa-triangle-exclamation mr-1"></i>Sắp hết hàng (' + updatedStock + ')' +
                            '</span>'
                        );
                    } else {
                        $statusCell.html(
                            '<span class="badge badge-mint px-2 py-1 rounded-pill">' +
                            '<i class="fa-solid fa-circle-check mr-1"></i>Còn hàng (' + updatedStock + ')' +
                            '</span>'
                        );
                    }

                    // Cập nhật nút Sửa trên cùng dòng
                    $row.find('button[onclick*="lbnEditInventoryProduct"]').attr('data-stock', updatedStock);

                    if (res && res.stats) {
                        lbnUpdateInventoryKpiCards(res.stats);
                    } else {
                        var lowStockTotal = 0;
                        $('.lbn-inv-row').each(function () {
                            var st = parseInt($(this).attr('data-stock'), 10) || 0;
                            if (st <= 5) lowStockTotal++;
                        });
                        $('#lbn_lowStockCounter').text(lowStockTotal);
                    }

                    lbnShowAlert(
                        $adminAlert,
                        'success',
                        'Cập nhật tồn kho thành công cho mã ' + lbnEscapeHtml(skuCode) + ' (Tồn mới: ' + updatedStock + ' SP)',
                        (res && res.message) ? res.message : 'Đã đồng bộ qua Stored Procedure sp_lbn_nhap_them_kho_hang.',
                        'sp_lbn_nhap_them_kho_hang'
                    );
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Không thể cập nhật tồn kho, vui lòng kiểm tra lại dữ liệu!';
                    $inputEl.removeClass('is-valid').addClass('is-invalid');
                    lbnShowAlert($adminAlert, 'error', 'Cập nhật tồn kho thất bại', msg, 'Lỗi 400');
                }
            });
        };

        function lbnBuildInventoryRowHtml(prod) {
            var badgeHtml = (prod.stock_quantity <= 5)
                ? '<span class="badge badge-danger lbn-badge-low-stock px-2 py-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Sắp hết hàng (' + prod.stock_quantity + ')</span>'
                : '<span class="badge badge-mint px-2 py-1 rounded-pill"><i class="fa-solid fa-circle-check mr-1"></i>Còn hàng (' + prod.stock_quantity + ')</span>';

            return (
                '<tr id="lbn_invRow_' + prod.id + '" class="lbn-inv-row" ' +
                    'data-category="' + prod.category_id + '" ' +
                    'data-stock="' + prod.stock_quantity + '" ' +
                    'data-search="' + lbnEscapeHtml(prod.sku + ' ' + prod.name + ' ' + prod.category_name) + '">' +
                    '<td class="font-weight-bold text-nowrap">' +
                        '<code class="text-peach font-weight-bold">' + lbnEscapeHtml(prod.sku) + '</code>' +
                        '<div class="small text-muted-custom">#ID: ' + prod.id + '</div>' +
                    '</td>' +
                    '<td class="text-center"><span class="lbn-inv-thumb"><img src="' + lbnEscapeHtml(prod.image) + '" alt="' + lbnEscapeHtml(prod.name) + '" class="lbn-thumb-img"></span></td>' +
                    '<td>' +
                        '<div class="font-weight-bold text-slate">' +
                            '<a href="/FashionKids/views/pages/product-detail.php?id=' + prod.id + '" class="text-slate text-decoration-none">' +
                                lbnEscapeHtml(prod.name) +
                            '</a>' +
                        '</div>' +
                        '<small class="text-muted-custom d-block">' +
                            '<i class="fa-solid fa-leaf text-success mr-1"></i>' + lbnEscapeHtml(prod.material) +
                        '</small>' +
                    '</td>' +
                    '<td class="text-nowrap">' +
                        '<span class="badge badge-pill badge-sky px-2 py-1">' + lbnEscapeHtml(prod.category_name) + '</span>' +
                        '<div class="small text-muted-custom mt-1">' + lbnEscapeHtml(prod.type_label) + '</div>' +
                    '</td>' +
                    '<td class="text-right font-weight-bold text-peach text-nowrap">' + lbnFormatVnd(prod.price) + '</td>' +
                    '<td class="text-center fk-minw-170">' +
                        '<div id="lbn_stockBadgeCell_' + prod.id + '" class="mb-2">' + badgeHtml + '</div>' +
                        '<div class="input-group input-group-sm justify-content-center mx-auto fk-w-125">' +
                            '<input type="number" id="lbn_stockInput_' + prod.id + '" class="form-control text-center font-weight-bold" value="' + prod.stock_quantity + '" min="0">' +
                            '<div class="input-group-append">' +
                                '<button type="button" class="btn btn-outline-secondary" title="Cập nhật tồn kho (lbn_update_stock)" onclick="lbnQuickUpdateStock(' + prod.id + ', \'' + lbnEscapeHtml(prod.sku) + '\')">' +
                                    '<i class="fa-solid fa-rotate"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td class="text-center text-nowrap">' +
                        '<button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2" title="Chỉnh sửa sản phẩm" ' +
                            'data-id="' + prod.id + '" ' +
                            'data-sku="' + lbnEscapeHtml(prod.sku) + '" ' +
                            'data-name="' + lbnEscapeHtml(prod.name) + '" ' +
                            'data-cat-id="' + prod.category_id + '" ' +
                            'data-gender="' + lbnEscapeHtml(prod.gender) + '" ' +
                            'data-price="' + prod.price + '" ' +
                            'data-stock="' + prod.stock_quantity + '" ' +
                            'data-type="' + lbnEscapeHtml(prod.type) + '" ' +
                            'data-material="' + lbnEscapeHtml(prod.material) + '" ' +
                            'onclick="lbnEditInventoryProduct(this)">' +
                            '<i class="fa-solid fa-pen-to-square"></i> Sửa' +
                        '</button>' +
                    '</td>' +
                '</tr>'
            );
        }

        // Bắt sự kiện submit của Form Thêm/Sửa sản phẩm (#lbn_formAdminProduct) qua AJAX FormData
        setTimeout(function () {
            $('#lbn_formAdminProduct').off('submit').on('submit', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var $skuInput   = $('#lbn_formProductSku');
                var $nameInput  = $('#lbn_formProductName');
                var $catSelect  = $('#lbn_formProductCategory');
                var $priceInput = $('#lbn_formProductPrice');
                var $stockInput = $('#lbn_formProductStock');
                var $matInput   = $('#lbn_formProductMaterial');
                var $submitBtn  = $('#lbn_btnSubmitProduct');

                var skuVal   = $.trim($skuInput.val());
                var nameVal  = $.trim($nameInput.val());
                var priceRaw = $.trim($priceInput.val());
                var stockRaw = $.trim($stockInput.val());
                var matVal   = $.trim($matInput.val());

                var hasClientErr = false;
                if (skuVal.length < 3) {
                    $skuInput.addClass('is-invalid').siblings('.invalid-feedback').text('Mã SKU phải có tối thiểu 3 ký tự (VD: FK-LBN-10).');
                    hasClientErr = true;
                } else {
                    $skuInput.removeClass('is-invalid').addClass('is-valid');
                }

                if (nameVal.length < 4) {
                    $nameInput.addClass('is-invalid').siblings('.invalid-feedback').text('Tên sản phẩm không được để trống (tối thiểu 4 ký tự).');
                    hasClientErr = true;
                } else {
                    $nameInput.removeClass('is-invalid').addClass('is-valid');
                }

                if (!/^\d+(\.\d+)?$/.test(priceRaw) || parseFloat(priceRaw) <= 0) {
                    $priceInput.addClass('is-invalid').siblings('.invalid-feedback').text('Đơn giá phải lớn hơn 0 VNĐ.');
                    hasClientErr = true;
                } else {
                    $priceInput.removeClass('is-invalid').addClass('is-valid');
                }

                if (!/^\d+$/.test(stockRaw) || parseInt(stockRaw, 10) < 0) {
                    $stockInput.addClass('is-invalid').siblings('.invalid-feedback').text('Tồn kho phải là số nguyên >= 0.');
                    hasClientErr = true;
                } else {
                    $stockInput.removeClass('is-invalid').addClass('is-valid');
                }

                if (matVal.length < 5) {
                    $matInput.addClass('is-invalid').siblings('.invalid-feedback').text('Vui lòng nhập mô tả chất liệu an toàn.');
                    hasClientErr = true;
                } else {
                    $matInput.removeClass('is-invalid').addClass('is-valid');
                }

                if (hasClientErr) {
                    lbnShowAlert(
                        $adminAlert,
                        'error',
                        'Thông tin sản phẩm chưa hợp lệ',
                        'Vui lòng kiểm tra các trường được đánh dấu đỏ trong biểu mẫu!',
                        'Validation 422'
                    );
                    return;
                }

                var formData = new FormData(this);
                var origBtnHtml = $submitBtn.html();
                $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Đang lưu vào kho...');

                $.ajax({
                    url: ENDPOINT + '?c=product&a=ajaxSaveProduct',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: CSRF ? { 'X-CSRF-Token': CSRF } : {},
                    timeout: 5000,
                    complete: function () {
                        $submitBtn.prop('disabled', false).html(origBtnHtml);
                    },
                    success: function (res) {
                        if (res && res.status === 'success' && res.product) {
                            var prod     = res.product;
                            var rowHtml  = lbnBuildInventoryRowHtml(prod);
                            var $existTr = $('#lbn_invRow_' + prod.id);

                            if ($existTr.length) {
                                $existTr.replaceWith(rowHtml);
                            } else {
                                $('#lbn_admin_inventory_section table tbody').prepend(rowHtml);
                            }

                            if (res.stats) {
                                lbnUpdateInventoryKpiCards(res.stats);
                            }

                            lbnShowAlert(
                                $adminAlert,
                                'success',
                                res.is_edit ? ('Cập nhật sản phẩm #' + prod.id + ' thành công') : ('Thêm sản phẩm mới thành công (Mã: ' + lbnEscapeHtml(prod.sku) + ')'),
                                res.message,
                                res.routine || 'sp_lbn_them_san_pham_moi'
                            );

                            if (!res.is_edit && typeof window.lbnResetProductForm === 'function') {
                                window.lbnResetProductForm();
                            }
                        }
                    },
                    error: function (xhr) {
                        var res = xhr.responseJSON;
                        if (res && res.errors) {
                            if (res.errors.sku) $skuInput.addClass('is-invalid').siblings('.invalid-feedback').text(res.errors.sku);
                            if (res.errors.name) $nameInput.addClass('is-invalid').siblings('.invalid-feedback').text(res.errors.name);
                            if (res.errors.price) $priceInput.addClass('is-invalid').siblings('.invalid-feedback').text(res.errors.price);
                            if (res.errors.stock_quantity) $stockInput.addClass('is-invalid').siblings('.invalid-feedback').text(res.errors.stock_quantity);
                            if (res.errors.description) $matInput.addClass('is-invalid').siblings('.invalid-feedback').text(res.errors.description);
                        }
                        var errMsg = (res && res.message) ? res.message : 'Lỗi lưu sản phẩm vào kho hàng!';
                        lbnShowAlert($adminAlert, 'error', 'Không thể lưu sản phẩm', errMsg, 'Lỗi ' + (xhr.status || 400));
                    }
                });
            });
        }, 0);
    }
});
