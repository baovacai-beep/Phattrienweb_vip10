/**
 * FASHIONKIDS D2C PLATFORM - MVC & AJAX BRIDGE SCRIPT (GIAI ĐOẠN 3)
 * Cầu nối giao tiếp AJAX giữa Giao diện Bootstrap 4 và Tầng Backend OOP/MVC
 * Tương thích linh hoạt với cả 2 bộ định tuyến: index.php?c=product&a=ajaxSuggestSize và index.php?url=product/ajaxSuggestSize
 */

$(document).ready(function () {
    // -------------------------------------------------------------------------
    // 1. AJAX GỢI Ý SIZE THƯỚC DÂY (MH 04)
    // -------------------------------------------------------------------------
    function executeAjaxSuggestSize(catId, heightVal, footVal) {
        const resultBox = $('#lnkt_size_result_box, #lnkt_suggestedSizeBadge');
        const specBox   = $('#lnkt_suggestedSubSpec');

        $.ajax({
            url: '/FashionKids/index.php?c=product&a=ajaxSuggestSize',
            type: 'POST',
            data: {
                ma_danh_muc: catId,
                chieu_cao:   heightVal,
                dai_chan:    footVal
            },
            dataType: 'json',
            timeout: 2500,
            success: function (res) {
                if (res && res.status === 'success' && res.recommended_size) {
                    $('#lnkt_suggestedSizeBadge').html(
                        res.recommended_size +
                        ' <span class="badge badge-mint ml-1 text-dark small" title="Đối chiếu qua Stored Function MySQL">' +
                        '<i class="fa-solid fa-server mr-1"></i>Stored Function</span>'
                    );
                    $('#lnkt_suggestedSubSpec').text(
                        'Dữ liệu chuẩn từ Stored Routine: Số đo ' + (catId == 4 ? footVal : heightVal) + ' cm khớp với bảng lnkt_bang_kich_co'
                    );

                    // Cập nhật alert box nếu có
                    if ($('#lnkt_size_result_box').length) {
                        $('#lnkt_size_result_box')
                            .removeClass('d-none alert-danger')
                            .addClass('alert-mint text-slate d-block')
                            .html(`<strong><i class="fas fa-check-circle mr-1"></i> Size khuyên dùng: ${res.recommended_size}</strong><br><small>${res.note}</small>`);
                    }

                    // Tự động tick chọn kích cỡ ở chi tiết sản phẩm nếu có
                    $(`input[name="kich_co_chon"][value="${res.recommended_size}"]`).prop('checked', true);
                    if ($('#lbn_selectedSizeText').length) {
                        $('#lbn_selectedSizeText').text(res.recommended_size);
                        $('#lbn_selectedSizeInput').val(res.recommended_size);
                    }
                }
            },
            error: function () {
                // Fallback nếu máy chủ CSDL chưa bật
            }
        });
    }

    // Bắt sự kiện form submit #lnkt_form_suggest_size
    $('#lnkt_form_suggest_size').on('submit', function (e) {
        e.preventDefault();
        const catId   = parseInt($('#lnkt_modal_cat_id').val() || $('#lnkt_clothingCategory').val() || 1);
        const height  = parseFloat($('#lnkt_input_height').val() || $('#lnkt_inputHeightCm').val() || 88);
        const foot    = parseFloat($('#lnkt_input_foot').val() || $('#lnkt_inputFootCm').val() || 13.8);
        executeAjaxSuggestSize(catId, height, foot);
    });

    // Lắng nghe thay đổi trên modal tra cứu size
    $('#lnkt_inputHeightCm, #lnkt_inputFootCm, #lnkt_clothingCategory, #lnkt_shoesCategory, #lnkt_chubbyCheck').on('input change', function () {
        clearTimeout(window.lnktAjaxSuggestDebounce);
        window.lnktAjaxSuggestDebounce = setTimeout(function () {
            const activeType = $('#lnkt_activeSizeType').val() || 'quan_ao';
            const catId      = (activeType === 'quan_ao') ? (parseInt($('#lnkt_clothingCategory').val()) || 1) : (parseInt($('#lnkt_shoesCategory').val()) || 4);
            const height     = parseFloat($('#lnkt_inputHeightCm').val()) || 88;
            const foot       = parseFloat($('#lnkt_inputFootCm').val()) || 13.8;
            const isChubby   = $('#lnkt_chubbyCheck').is(':checked');
            executeAjaxSuggestSize(catId, isChubby ? (height + 7) : height, foot);
        }, 300);
    });

});
