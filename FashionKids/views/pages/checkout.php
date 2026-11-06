<?php
$page_title = "Giỏ Hàng & Thanh Toán Đặt Hàng (MH 05)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh sách món đồ đã chọn trong giỏ hàng (tương ứng tgb_order_items & lnkt_orders)
$lnkt_cart_items = [
    [
        'product_id' => 101,
        'name'       => 'Đầm Xòe Sợi Tre Hoa Cúc Họa Mi (Organic Bamboo)',
        'size'       => 'Size 2 (2-3T)',
        'category'   => 'Bé Gái',
        'price'      => 285000,
        'quantity'   => 2,
        'icon'       => 'fa-shirt text-peach'
    ],
    [
        'product_id' => 102,
        'name'       => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí',
        'size'       => 'Size 3 (3-4T)',
        'category'   => 'Bé Trai',
        'price'      => 245000,
        'quantity'   => 1,
        'icon'       => 'fa-child-reaching text-info'
    ],
    [
        'product_id' => 103,
        'name'       => 'Sandal Tập Đi Đế Cao Su Non Chống Trượt',
        'size'       => 'Size 24 (14cm)',
        'category'   => 'Giày dép',
        'price'      => 210000,
        'quantity'   => 1,
        'icon'       => 'fa-shoe-prints text-warning'
    ]
];

$lnkt_subtotal = 0;
$lnkt_total_qty = 0;
foreach ($lnkt_cart_items as $item) {
    $lnkt_subtotal += $item['price'] * $item['quantity'];
    $lnkt_total_qty += $item['quantity'];
}
$lnkt_vip_discount_percent = 10; // Hạng Vàng (Gold) giảm 10%
$lnkt_discount_amount = round($lnkt_subtotal * ($lnkt_vip_discount_percent / 100));
$lnkt_total_amount = $lnkt_subtotal - $lnkt_discount_amount;
$lnkt_reward_points = floor($lnkt_total_amount / 10000);
?>

<div class="container my-4 my-md-5" id="lnkt_checkout_container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-basket-shopping text-peach mr-2"></i>Giỏ Hàng &amp; Thanh Toán Đặt Hàng (MH 05)
            </h4>
            <small class="text-muted-custom">
                Khởi tạo đơn hàng qua Stored Procedure <code>lnkt_create_order</code> &amp; tự động khấu trừ chiết khấu VIP
            </small>
        </div>
        <button type="button" class="btn btn-pastel-outline btn-sm font-weight-bold" data-toggle="modal" data-target="#modalSuggestSize">
            <i class="fa-solid fa-ruler-combined mr-1"></i> Kiểm Tra Lại Size Cho Bé
        </button>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI: BẢNG TÓM TẮT ĐƠN HÀNG & FORM THÔNG TIN GIAO HÀNG
             ================================================================ -->
        <div class="col-12 col-lg-7 mb-4">
            <!-- 1. BẢNG TÓM TẮT ĐƠN HÀNG -->
            <div class="card card-pastel shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-0 text-slate">
                        <i class="fa-solid fa-bag-shopping text-peach mr-1"></i> Bảng Tóm Tắt Các Món Đồ Đã Chọn
                    </h6>
                    <span id="lnkt_cartCountBadge" class="badge badge-pill badge-mint px-3 py-1">
                        <?= $lnkt_total_qty ?> sản phẩm
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light small">
                                <tr>
                                    <th>Sản Phẩm &amp; Kích Cỡ</th>
                                    <th class="text-right">Đơn Giá Chốt</th>
                                    <th class="text-center">Số Lượng Điều Chỉnh</th>
                                    <th class="text-right">Thành Tiền</th>
                                    <th class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_cart_items as $item): ?>
                                    <?php $line_total = $item['price'] * $item['quantity']; ?>
                                    <tr class="lnkt-cart-row" data-price="<?= $item['price'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="lnkt-cart-item-icon mr-3 border">
                                                    <i class="fa-solid <?= $item['icon'] ?> h4 mb-0"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-slate small">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </div>
                                                    <span class="badge badge-sky small px-2 py-0 mr-1"><?= htmlspecialchars($item['size']) ?></span>
                                                    <small class="text-muted-custom"><?= htmlspecialchars($item['category']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right font-weight-bold small text-slate">
                                            <?= number_format($item['price'], 0, ',', '.') ?> đ
                                        </td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm justify-content-center">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-secondary lnkt-btn-qty-minus">-</button>
                                                </div>
                                                <input type="number" min="1" max="20" class="form-control lnkt-qty-input" value="<?= $item['quantity'] ?>">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary lnkt-btn-qty-plus">+</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right font-weight-bold text-peach lnkt-line-total">
                                            <?= number_format($line_total, 0, ',', '.') ?> đ
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle lnkt-btn-remove-item" title="Xóa khỏi giỏ">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. FORM THÔNG TIN GIAO HÀNG (TÍCH HỢP PROVINCES OPEN API) -->
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-0 text-slate">
                        <i class="fa-solid fa-truck-fast text-peach mr-1"></i> Form Thông Tin Giao Hàng (<code>lnkt_orders</code>)
                    </h6>
                    <span class="badge badge-pill badge-sky px-3 py-1 small">
                        <i class="fa-solid fa-plug mr-1"></i> Provinces Open API Ready
                    </span>
                </div>
                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formCheckout" method="POST" action="/FashionKids/views/pages/order-tracking.php">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Họ và tên phụ huynh nhận hàng (<span class="text-danger">*</span>):</label>
                                <input type="text" id="lnkt_receiverName" name="receiver_name" class="form-control" value="Võ Thị Thanh Thảo" placeholder="Nhập họ tên phụ huynh..." required>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Số điện thoại liên hệ vận chuyển (<span class="text-danger">*</span>):</label>
                                <input type="tel" id="lnkt_receiverPhone" name="receiver_phone" class="form-control" value="0918889999" pattern="[0-9]{10,11}" placeholder="09xxxxxxxx" required>
                            </div>
                        </div>

                        <!-- Hệ thống Dropdown chọn Tỉnh/Thành phố và Quận/Huyện -->
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Tỉnh / Thành phố (<span class="text-danger">*</span>):</label>
                                <select id="lnkt_province" name="province" class="form-control" required>
                                    <option value="">-- Chọn Tỉnh / Thành phố --</option>
                                    <option value="TP. Hồ Chí Minh" selected>TP. Hồ Chí Minh</option>
                                    <option value="Thành phố Hà Nội">Thành phố Hà Nội</option>
                                    <option value="Thành phố Đà Nẵng">Thành phố Đà Nẵng</option>
                                    <option value="Tỉnh Bình Dương">Tỉnh Bình Dương</option>
                                    <option value="Tỉnh Đồng Nai">Tỉnh Đồng Nai</option>
                                    <option value="Thành phố Cần Thơ">Thành phố Cần Thơ</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Quận / Huyện (<span class="text-danger">*</span>):</label>
                                <select id="lnkt_district" name="district" class="form-control" required>
                                    <option value="">-- Chọn Quận / Huyện --</option>
                                    <option value="Quận Bình Thạnh" selected>Quận Bình Thạnh</option>
                                    <option value="Quận 1">Quận 1</option>
                                    <option value="Quận 3">Quận 3</option>
                                    <option value="Quận 7">Quận 7</option>
                                    <option value="TP. Thủ Đức">TP. Thủ Đức</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-slate">Địa chỉ nhận hàng chi tiết (Số nhà, tên đường, phường/xã):</label>
                            <input type="text" id="lnkt_shippingAddress" name="shipping_address" class="form-control" value="Số 120 Điện Biên Phủ, Phường 17" placeholder="Ví dụ: Số 120 Điện Biên Phủ, Phường 17..." required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-slate">Ghi chú cho nhân viên đóng gói / shipper:</label>
                            <textarea class="form-control" name="order_note" rows="2" placeholder="Ví dụ: Giao giờ hành chính, cắt mác vải giúp bé..."></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI: BẢNG TỔNG HỢP DÒNG TIỀN TÍNH TOÁN TỰ ĐỘNG
             ================================================================ -->
        <div class="col-12 col-lg-5">
            <div class="card card-pastel shadow-sm p-4 mb-4">
                <h5 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-receipt text-peach mr-2"></i>Tổng Hợp Chi Phí Đơn Hàng
                </h5>

                <!-- Chọn mô phỏng hạng thẻ VIP để kiểm thử chiết khấu tự động -->
                <div class="p-3 rounded bg-milk-cream border mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small font-weight-bold text-slate">
                            <i class="fa-solid fa-crown text-warning mr-1"></i> Hạng thẻ VIP áp dụng:
                        </span>
                        <span class="badge badge-gold px-2 py-1">Tự động giảm giá</span>
                    </div>
                    <select id="lnkt_vipDiscountPercent" class="form-control form-control-sm font-weight-bold">
                        <option value="0">Hạng Đồng (Bronze) - Giảm 0%</option>
                        <option value="5">Hạng Bạc (Silver) - Giảm 5%</option>
                        <option value="10" selected>Hạng Vàng (Gold) - Giảm 10%</option>
                        <option value="15">Hạng Kim Cương (Diamond) - Giảm 15%</option>
                    </select>
                </div>

                <!-- Bảng tổng hợp dòng tiền: Tiền tạm tính, Số tiền giảm giá VIP, Tổng tiền thu thực tế -->
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted-custom">Tiền tạm tính (<code>subtotal</code>):</span>
                        <span id="lnkt_subtotalText" class="font-weight-bold text-slate">
                            <?= number_format($lnkt_subtotal, 0, ',', '.') ?> đ
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted-custom">Số tiền giảm giá VIP (<code>discount_amount</code>):</span>
                        <span id="lnkt_discountText" class="font-weight-bold text-success">
                            - <?= number_format($lnkt_discount_amount, 0, ',', '.') ?> đ
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted-custom">Phí vận chuyển tiêu chuẩn:</span>
                        <span class="badge badge-mint px-2 py-1">Miễn phí (Freeship VIP)</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mb-2">
                        <span class="h6 font-weight-bold text-slate mb-0">Tổng tiền thu thực tế (<code>total_amount</code>):</span>
                        <span id="lnkt_totalAmountText" class="h4 font-weight-bold text-peach mb-0">
                            <?= number_format($lnkt_total_amount, 0, ',', '.') ?> đ
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center small text-muted-custom mb-4">
                        <span>Điểm thưởng tích lũy dự kiến (10.000đ = 1 điểm):</span>
                        <span id="lnkt_rewardPointsText" class="font-weight-bold text-primary">
                            +<?= $lnkt_reward_points ?> điểm VIP
                        </span>
                    </div>
                </div>

                <button type="submit" form="lnkt_formCheckout" class="btn btn-pastel-primary btn-block font-weight-bold py-3 shadow-sm mb-3">
                    <i class="fa-solid fa-circle-check mr-1"></i> Xác Nhận Đặt Hàng Ngay
                </button>

                <div class="small text-muted-custom text-center">
                    <i class="fa-solid fa-shield-halved text-success mr-1"></i>
                    Được phép kiểm tra chất vải sợi tre trước khi thanh toán COD
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "../modals/modal-suggest-size.php";
require_once "../layouts/footer.php";
?>
