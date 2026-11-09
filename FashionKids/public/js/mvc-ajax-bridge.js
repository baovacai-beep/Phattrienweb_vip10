/**
 * FASHIONKIDS D2C PLATFORM - MVC & AJAX BRIDGE SCRIPT (GIAI ĐOẠN 3 & 4)
 * Cầu nối giao tiếp AJAX giữa Giao diện Bootstrap 4 và Tầng Backend OOP/MVC.
 * Các tác vụ chính (Gợi ý Size MH 04, Lọc sản phẩm MH 02, Kiểm tra tồn kho MH 03,
 * và Provinces Open API MH 05) đã được hợp nhất đồng bộ trong main-interactive.js.
 */

$(document).ready(function () {
    'use strict';

    // Hỗ trợ nút đặt hàng AJAX #lnkt_btnSubmitOrder (nếu có) gửi tới OrderController@ajaxCreateOrder
    $('#lnkt_btnSubmitOrder').on('click', function () {
        let receiverName  = ($('#lnkt_receiverName').val() || '').trim();
        let receiverPhone = ($('#lnkt_receiverPhone').val() || '').trim();
        let province      = $('#tgb_select_province, #lnkt_provinceSelect').val() || '';
        let district      = $('#tgb_select_district, #lnkt_districtSelect').val() || '';
        let address       = ($('#lnkt_shippingAddress').val() || '').trim();

        if (!receiverName || !receiverPhone || !address) {
            return;
        }

        let orderPayload = {
            receiver_name:    receiverName,
            receiver_phone:   receiverPhone,
            receiver_email:   $('#tgb_receiver_email, #lnkt_receiverEmail').val() || 'customer@fashionkids.vn',
            shipping_address: $('#tgb_full_address').val() || address,
            province:         province,
            district:         district,
            product_id:       parseInt($('#tgb_checkout_product_id').val(), 10) || 101,
            size:             $('#tgb_checkout_size').val() || 'Size 2 (2-3T)',
            quantity:         parseInt($('#tgb_checkout_qty').val(), 10) || 1
        };

        $.ajax({
            url: '/FashionKids/index.php?c=order&a=ajaxCreateOrder',
            type: 'POST',
            data: JSON.stringify(orderPayload),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            timeout: 3000,
            success: function (res) {
                if (res && res.status === 'success') {
                    console.log('Tạo đơn hàng thành công qua Stored Procedure, Mã đơn:', res.order_id);
                }
            }
        });
    });
});