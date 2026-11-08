<?php
$page_title = "Giỏ Hàng & Thanh Toán Đặt Hàng (MH 05)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Danh sách món đồ trong giỏ hàng mẫu (liên kết tgb_order_items & lbn_products)
$lnkt_cart_items = [
    [
        'item_id'       => 1,
        'product_id'    => 101,
        'sku'           => 'FK-LBN-01',
        'name'          => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'category_name' => 'Váy công chúa',
        'size'          => 'Size 2 (2-3T)',
        'price'         => 285000,
        'quantity'      => 1,
        'icon'          => 'fa-person-dress text-peach'
    ],
    [
        'item_id'       => 2,
        'product_id'    => 102,
        'sku'           => 'FK-LBN-02',
        'name'          => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'category_name' => 'Bộ thun',
        'size'          => 'Size 3 (2-3T)',
        'price'         => 225000,
        'quantity'      => 2,
        'icon'          => 'fa-shirt text-info'
    ],
    [
        'item_id'       => 3,
        'product_id'    => 103,
        'sku'           => 'FK-LBN-03',
        'name'          => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'category_name' => 'Giày sandal',
        'size'          => 'Size 22 (13cm)',
        'price'         => 210000,
        'quantity'      => 1,
        'icon'          => 'fa-shoe-prints text-warning'
    ]
];

// Tính toán chi phí khởi tạo (Mặc định Hạng Vàng - Giảm 10%)
$init_subtotal = 0;
foreach ($lnkt_cart_items as $item) {
    $init_subtotal += $item['price'] * $item['quantity'];
}
$init_vip_percent = 10;
$init_discount_amount = round(($init_subtotal * $init_vip_percent) / 100);
$init_total_amount = $init_subtotal - $init_discount_amount;
$init_reward_points = floor($init_total_amount / 10000);
?>

<div class="container my-4 my-md-5" id="lnkt_checkout_section">
    <!-- Breadcrumb & Tiêu đề trang MH 05 -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php" class="text-muted-custom">Cửa hàng</a></li>
                    <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page">Giỏ hàng &amp; Thanh toán (MH 05)</li>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-basket-shopping text-peach mr-2"></i>Giỏ Hàng &amp; Thanh Toán Đặt Hàng
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-pastel-sky btn-sm font-weight-bold shadow-sm mr-2"
                    data-toggle="modal" data-target="#modalSuggestSize">
                <i class="fa-solid fa-ruler-horizontal text-peach mr-1"></i> Kiểm Tra Lại Size (MH 04)
            </button>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-outline btn-sm font-weight-bold">
                <i class="fa-solid fa-plus mr-1"></i> Mua Thêm Đồ Cho Bé
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (7 CỘT): BẢNG TÓM TẮT ĐƠN HÀNG & FORM THÔNG TIN GIAO HÀNG
             ================================================================ -->
        <div class="col-12 col-lg-7 mb-4">
            <!-- 1. BẢNG TÓM TẮT ĐƠN HÀNG (DANH SÁCH MÓN ĐỒ, SỐ LƯỢNG ĐIỀU CHỈNH, ĐƠN GIÁ CHỐT) -->
            <div class="card card-pastel shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-bag-shopping text-peach mr-2"></i>1. Bảng Tóm Tắt Đơn Hàng
                        </h5>
                        <small class="text-muted-custom">Điều chỉnh số lượng, kích cỡ và kiểm tra đơn giá chốt cuối cùng (<code>tgb_order_items</code>)</small>
                    </div>
                    <span class="badge badge-pill badge-peach bg-peach text-white px-3 py-1 font-weight-bold">
                        <span id="lnkt_cartItemCountBadge"><?= count($lnkt_cart_items) ?></span> dòng SP
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="lnkt_checkoutCartTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sản Phẩm &amp; Phân Loại Size</th>
                                    <th class="text-right">Đơn Giá</th>
                                    <th class="text-center fk-w-140">Số Lượng</th>
                                    <th class="text-right">Đơn Giá Chốt</th>
                                    <th class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="lnkt_checkoutCartBody">
                                <?php foreach ($lnkt_cart_items as $item): ?>
                                    <?php $line_total = $item['price'] * $item['quantity']; ?>
                                    <tr class="lnkt-cart-row"
                                        id="lnkt_cartRow_<?= $item['item_id'] ?>"
                                        data-item-id="<?= $item['item_id'] ?>"
                                        data-price="<?= $item['price'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="lbn-inv-thumb mr-3 flex-shrink-0">
                                                    <i class="fa-solid <?= $item['icon'] ?> h4 mb-0"></i>
                                                </span>
                                                <div>
                                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $item['product_id'] ?>"
                                                       class="font-weight-bold text-slate text-decoration-none d-block">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <div class="mt-1 d-flex flex-wrap align-items-center">
                                                        <span class="badge badge-pill badge-sky px-2 py-1 mr-2">
                                                            <?= htmlspecialchars($item['size']) ?>
                                                        </span>
                                                        <small class="text-muted-custom">Mã: <code><?= htmlspecialchars($item['sku']) ?></code></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-right font-weight-bold text-slate text-nowrap">
                                            <?= number_format($item['price'], 0, ',', '.') ?> đ
                                        </td>

                                        <td class="text-center">
                                            <div class="input-group input-group-sm mx-auto fk-w-112">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-light border font-weight-bold"
                                                            onclick="lnktChangeCartQty(<?= $item['item_id'] ?>, -1)">
                                                        <i class="fa-solid fa-minus"></i>
                                                    </button>
                                                </div>
                                                <input type="number"
                                                       id="lnkt_cartQty_<?= $item['item_id'] ?>"
                                                       class="form-control text-center font-weight-bold lnkt-cart-qty-input"
                                                       value="<?= $item['quantity'] ?>" min="1" max="20"
                                                       onchange="lnktRecalculateCheckout()">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-light border font-weight-bold"
                                                            onclick="lnktChangeCartQty(<?= $item['item_id'] ?>, 1)">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-right font-weight-bold text-peach text-nowrap"
                                            id="lnkt_lineTotal_<?= $item['item_id'] ?>">
                                            <?= number_format($line_total, 0, ',', '.') ?> đ
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                                    title="Xóa khỏi giỏ hàng"
                                                    onclick="lnktRemoveCartItem(<?= $item['item_id'] ?>)">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Thông báo khi giỏ hàng trống -->
                    <div id="lnkt_emptyCartNotice" class="p-4 text-center d-none">
                        <i class="fa-solid fa-basket-shopping text-muted display-4 mb-2"></i>
                        <h6 class="font-weight-bold text-slate">Giỏ hàng của phụ huynh đang trống</h6>
                        <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary btn-sm mt-2">
                            Quay lại Cửa hàng chọn đồ cho bé
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. FORM THÔNG TIN GIAO HÀNG (PROVINCES OPEN API READY) -->
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-truck-fast text-peach mr-2"></i>2. Form Thông Tin Giao Hàng
                        </h5>
                        <small class="text-muted-custom">Chuẩn hóa địa chỉ nhận hàng tích hợp Provinces Open API (<code>lnkt_orders</code>)</small>
                    </div>
                    <button type="button" onclick="lnktSyncProvincesOpenApi()" class="btn btn-light btn-sm border rounded-pill small font-weight-bold text-slate">
                        <i class="fa-solid fa-cloud-arrow-down text-info mr-1"></i>Đồng bộ Provinces API
                    </button>
                </div>

                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formCheckoutOrder">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_receiverName">
                                    Họ và tên phụ huynh nhận hàng (<span class="text-danger">*</span>):
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-user text-peach"></i></span>
                                    </div>
                                    <input type="text" id="lnkt_receiverName" name="receiver_name" class="form-control"
                                           value="Võ Thị Thanh Thảo" placeholder="Nhập họ tên phụ huynh..." required>
                                </div>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_receiverPhone">
                                    Số điện thoại liên hệ vận chuyển (<span class="text-danger">*</span>):
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-peach"></i></span>
                                    </div>
                                    <input type="tel" id="lnkt_receiverPhone" name="receiver_phone" class="form-control"
                                           value="0918889999" placeholder="VD: 0918889999" pattern="[0-9]{10,11}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Hệ thống Dropdown chọn Tỉnh/Thành phố và Quận/Huyện (Provinces Open API) -->
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_provinceSelect">
                                    Tỉnh / Thành phố (<code>province</code>) (<span class="text-danger">*</span>):
                                </label>
                                <select id="lnkt_provinceSelect" name="province" class="custom-select form-control"
                                        onchange="lnktOnProvinceChange(this.value)" required>
                                    <option value="TP. Hồ Chí Minh" selected>TP. Hồ Chí Minh</option>
                                    <option value="Thành phố Hà Nội">Thành phố Hà Nội</option>
                                    <option value="Thành phố Đà Nẵng">Thành phố Đà Nẵng</option>
                                    <option value="Thành phố Cần Thơ">Thành phố Cần Thơ</option>
                                    <option value="Tỉnh Bình Dương">Tỉnh Bình Dương</option>
                                    <option value="Tỉnh Đồng Nai">Tỉnh Đồng Nai</option>
                                </select>
                                <small id="lnkt_apiStatusText" class="text-muted-custom d-block mt-1">
                                    <i class="fa-solid fa-circle-check text-success mr-1"></i>Sẵn sàng kết nối <code>provinces.open-api.vn/api/?depth=2</code>
                                </small>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_districtSelect">
                                    Quận / Huyện (<code>district</code>) (<span class="text-danger">*</span>):
                                </label>
                                <select id="lnkt_districtSelect" name="district" class="custom-select form-control" required>
                                    <option value="Quận Bình Thạnh" selected>Quận Bình Thạnh</option>
                                    <option value="Quận 1">Quận 1</option>
                                    <option value="Quận 3">Quận 3</option>
                                    <option value="Quận 7">Quận 7</option>
                                    <option value="Quận Phú Nhuận">Quận Phú Nhuận</option>
                                    <option value="Thành phố Thủ Đức">Thành phố Thủ Đức</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate" for="lnkt_shippingAddress">
                                Địa chỉ giao hàng chi tiết (Số nhà, Tên đường, Phường/Xã) (<span class="text-danger">*</span>):
                            </label>
                            <input type="text" id="lnkt_shippingAddress" name="shipping_address" class="form-control"
                                   value="Số 120 Điện Biên Phủ, Phường 17" placeholder="Nhập số nhà, tên đường, phường/xã..." required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-slate" for="lnkt_orderNote">
                                Ghi chú cho nhân viên đóng gói &amp; bưu tá:
                            </label>
                            <textarea id="lnkt_orderNote" rows="2" class="form-control"
                                      placeholder="VD: Giao giờ hành chính, gọi trước cho mẹ bé 15 phút...">Giao giờ hành chính, đóng gói hộp quà pastel giúp mẹ bé nhé!</textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (5 CỘT): TÍNH TOÁN CHI PHÍ TỰ ĐỘNG & CHỐT ĐẶT HÀNG
             ================================================================ -->
        <div class="col-12 col-lg-5">
            <div class="card card-pastel shadow-sm sticky-top fk-sticky-85">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-calculator text-peach mr-2"></i>3. Bảng Tổng Hợp Dòng Tiền Tự Động
                    </h5>
                    <small class="text-muted-custom">Tự động khấu trừ chiết khấu VIP (<code>hgb_get_discount_percent</code>)</small>
                </div>

                <div class="card-body p-4 pt-2">
                    <!-- Bộ chọn mô phỏng hạng thẻ VIP để kiểm thử chiết khấu tự động -->
                    <div class="p-3 rounded mb-3 lbn-vip-price-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small font-weight-bold text-slate">
                                <i class="fa-solid fa-crown text-warning mr-1"></i>Hạng thẻ VIP áp dụng:
                            </span>
                            <span id="lnkt_checkoutVipBadge" class="badge badge-gold px-2 py-1 rounded-pill">
                                Hạng Vàng (-10%)
                            </span>
                        </div>
                        <div class="btn-group btn-group-sm btn-block" role="group">
                            <button type="button" class="btn btn-outline-secondary font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(0, 'Hạng Đồng (0%)', this)">Đồng 0%</button>
                            <button type="button" class="btn btn-outline-secondary font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(5, 'Hạng Bạc (-5%)', this)">Bạc -5%</button>
                            <button type="button" class="btn btn-pastel-primary active font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(10, 'Hạng Vàng (-10%)', this)">Vàng -10%</button>
                            <button type="button" class="btn btn-outline-secondary font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(15, 'Kim Cương (-15%)', this)">K.Cương -15%</button>
                        </div>
                        <input type="hidden" id="lnkt_checkoutDiscountPercent" value="<?= $init_vip_percent ?>">
                    </div>

                    <!-- Bảng tổng hợp dòng tiền: Tiền tạm tính, Số tiền giảm giá VIP, Tổng tiền thu thực tế -->
                    <div class="border rounded p-3 bg-milk-cream mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted-custom font-weight-bold">1. Tiền tạm tính (<code>subtotal</code>):</span>
                            <span id="lnkt_summarySubtotal" class="font-weight-bold text-slate h6 mb-0">
                                <?= number_format($init_subtotal, 0, ',', '.') ?> đ
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted-custom font-weight-bold">
                                2. Số tiền giảm giá VIP (<code>discount_amount</code>):
                            </span>
                            <span id="lnkt_summaryDiscount" class="font-weight-bold text-success h6 mb-0">
                                -<?= number_format($init_discount_amount, 0, ',', '.') ?> đ
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted-custom font-weight-bold">Phí vận chuyển tiêu chuẩn:</span>
                            <span id="lnkt_summaryShipping" class="badge badge-mint px-2 py-1 rounded-pill">
                                Miễn phí (0 đ)
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <div>
                                <span class="font-weight-bold text-slate h6 mb-0 d-block">3. Tổng tiền thu thực tế:</span>
                                <small class="text-muted-custom">(<code>total_amount</code> đã gồm VAT)</small>
                            </div>
                            <span id="lnkt_summaryTotalAmount" class="h4 font-weight-bold text-peach mb-0">
                                <?= number_format($init_total_amount, 0, ',', '.') ?> đ
                            </span>
                        </div>
                    </div>

                    <!-- Điểm thưởng tích lũy sau đơn hàng -->
                    <div class="d-flex justify-content-between align-items-center p-2 px-3 rounded bg-buttercup-soft mb-3 small">
                        <span class="font-weight-bold text-slate">
                            <i class="fa-solid fa-coins text-warning mr-1"></i>Điểm thưởng tích lũy (<code>hgb_update_user_points</code>):
                        </span>
                        <span id="lnkt_summaryRewardPoints" class="font-weight-bold text-slate">
                            +<?= number_format($init_reward_points) ?> điểm VIP
                        </span>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="mb-4">
                        <label class="small font-weight-bold text-slate mb-2 d-block">Phương thức thanh toán:</label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="lnkt_payCod" name="payment_method" class="custom-control-input" value="COD" checked>
                            <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_payCod">
                                <i class="fa-solid fa-money-bill-wave text-success mr-1"></i> Thanh toán khi nhận hàng (COD - Được đồng kiểm)
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="lnkt_payBank" name="payment_method" class="custom-control-input" value="VIETQR">
                            <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_payBank">
                                <i class="fa-solid fa-qrcode text-info mr-1"></i> Chuyển khoản nhanh VietQR / Ví MoMo
                            </label>
                        </div>
                    </div>

                    <!-- Nút Chốt Đặt Hàng -->
                    <button type="button" id="lnkt_btnSubmitCheckout" onclick="lnktSubmitCheckoutOrder()"
                            class="btn btn-pastel-primary btn-block font-weight-bold py-3 shadow">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Xác Nhận Đặt Hàng Ngay (<code>lnkt_create_order</code>)
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted-custom">
                            <i class="fa-solid fa-shield-halved text-success mr-1"></i>Đổi trả miễn phí trong 15 ngày nếu bé mặc chật
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../layouts/footer.php";
?>

