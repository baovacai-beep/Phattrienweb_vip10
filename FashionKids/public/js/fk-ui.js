/**
 * FASHIONKIDS - UI HELPERS (thay thế Inline CSS)
 * Các giá trị động (chiều cao cột biểu đồ, % thanh tiến trình, màu nền từ dữ liệu) được
 * khai báo bằng thuộc tính data-* trong View và gán qua CSSOM tại đây, nhờ đó mã HTML
 * không còn thuộc tính style="..." (tương thích Content-Security-Policy, dễ bảo trì).
 *
 *   data-fk-width="38"      -> width: 38%
 *   data-fk-height="110"    -> height: 110px
 *   data-fk-bg="#FF9E93"    -> background-color (chỉ nhận mã màu HEX hợp lệ)
 */
$(function () {
    'use strict';

    $('[data-fk-width]').each(function () {
        var v = parseFloat($(this).attr('data-fk-width'));
        if (!isNaN(v)) { $(this).css('width', Math.max(0, Math.min(100, v)) + '%'); }
    });

    $('[data-fk-height]').each(function () {
        var v = parseFloat($(this).attr('data-fk-height'));
        if (!isNaN(v)) { $(this).css('height', Math.max(0, v) + 'px'); }
    });

    $('[data-fk-bg]').each(function () {
        var c = ($(this).attr('data-fk-bg') || '').trim();
        if (/^#[0-9a-fA-F]{3,8}$/.test(c)) { $(this).css('background-color', c); }
    });
});
