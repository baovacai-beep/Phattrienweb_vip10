<?php
$page_title = "Giỏ Hàng & Thanh Toán - FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

$lnkt_cart_items = [];
$sessCart = (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) ? $_SESSION['cart'] : [];
$seqId = 1;
foreach ($sessCart as $cItem) {
    $qty = max(1, (int)($cItem['quantity'] ?? 1));
    $price = max(0, (int)($cItem['price'] ?? 0));
    $lnkt_cart_items[] = [
        'item_id'    => $seqId++,
        'product_id' => (int)($cItem['product_id'] ?? 101),
        'sku'        => (string)($cItem['sku'] ?? 'FK-LBN-01'),
        'image'      => (string)($cItem['image'] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg'),
        'name'       => (string)($cItem['product_name'] ?? $cItem['name'] ?? 'Sản phẩm FashionKids'),
        'size'       => (string)($cItem['size'] ?? 'Size 2 (2-3T)'),
        'price'      => $price,
        'quantity'   => $qty,
    ];
}

$sessUser = (!empty($_SESSION['user']) && is_array($_SESSION['user'])) ? $_SESSION['user'] : null;
$recvName  = $sessUser ? (string)($sessUser['ho_va_ten'] ?? '') : '';
$recvPhone = $sessUser ? (string)($sessUser['so_dien_thoai'] ?? '') : '';
$recvAddr  = $sessUser ? (string)($sessUser['dia_chi_nha'] ?? '') : '';
$recvEmail = $sessUser ? (string)($sessUser['thu_dien_tu'] ?? '') : '';

$vipDiem = $sessUser ? (int)($sessUser['diem_tich_luy'] ?? 0) : 0;
if ($vipDiem >= 3000) {
    $init_vip_percent  = 15;
    $vipTierTitle      = 'Kim Cương (-15%)';
    $vipTierBadgeClass = 'badge-primary';
} elseif ($vipDiem >= 1500) {
    $init_vip_percent  = 10;
    $vipTierTitle      = 'Hạng Vàng (-10%)';
    $vipTierBadgeClass = 'badge-gold';
} elseif ($vipDiem >= 500) {
    $init_vip_percent  = 5;
    $vipTierTitle      = 'Hạng Bạc (-5%)';
    $vipTierBadgeClass = 'badge-sky';
} else {
    $init_vip_percent  = 0;
    $vipTierTitle      = 'Hạng Đồng (0%)';
    $vipTierBadgeClass = 'badge-secondary';
}

$firstCartProductId = !empty($lnkt_cart_items) ? (int)$lnkt_cart_items[0]['product_id'] : 101;
$firstCartSize      = !empty($lnkt_cart_items) ? (string)$lnkt_cart_items[0]['size'] : 'Size 2 (2-3T)';
$firstCartQty       = !empty($lnkt_cart_items) ? (int)$lnkt_cart_items[0]['quantity'] : 1;

// Tính toán chi phí khởi tạo từ giỏ hàng & hạng thẻ thực tế
$init_subtotal = 0;
foreach ($lnkt_cart_items as $item) {
    $init_subtotal += $item['price'] * $item['quantity'];
}
$init_discount_amount = round(($init_subtotal * $init_vip_percent) / 100);
$init_shipping_fee = ($init_subtotal > 0 && $init_subtotal < 300000) ? 25000 : 0;
$init_total_amount = max(0, $init_subtotal - $init_discount_amount + $init_shipping_fee);
$init_reward_points = (int)floor($init_total_amount / 10000);
?>

<div class="container my-4 my-md-5" id="lnkt_checkout_section">
    <!-- Breadcrumb & Tiêu đề trang -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php" class="text-muted-custom">Cửa hàng</a></li>
                    <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page">Giỏ hàng &amp; Thanh toán</li>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-basket-shopping text-peach mr-2"></i>Giỏ Hàng &amp; Thanh Toán Đặt Hàng
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-pastel-sky btn-sm font-weight-bold shadow-sm mr-2"
                    data-toggle="modal" data-target="#modalSuggestSize">
                <i class="fa-solid fa-ruler-horizontal text-peach mr-1"></i> Kiểm Tra Lại Size
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
            <!-- 1. BẢNG TÓM TẮT ĐƠN HÀNG -->
            <div class="card card-pastel shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-bag-shopping text-peach mr-2"></i>1. Bảng Tóm Tắt Đơn Hàng
                        </h5>
                        <small class="text-muted-custom">Điều chỉnh số lượng, kích cỡ và kiểm tra đơn giá sản phẩm</small>
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
                                                <span class="lbn-inv-thumb mr-3 flex-shrink-0"><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="lbn-thumb-img"></span>
                                                <div>
                                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $item['product_id'] ?>"
                                                       class="font-weight-bold text-slate text-decoration-none d-block">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <div class="mt-1 d-flex flex-wrap align-items-center">
                                                        <span class="badge badge-pill badge-sky px-2 py-1 mr-2">
                                                            <?= htmlspecialchars($item['size']) ?>
                                                        </span>
                                                        <small class="text-muted-custom">Mã SP: <strong class="text-slate"><?= htmlspecialchars($item['sku']) ?></strong></small>
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
                    <div id="lnkt_emptyCartNotice" class="p-4 text-center <?= empty($lnkt_cart_items) ? '' : 'd-none' ?>">
                        <i class="fa-solid fa-basket-shopping text-muted display-4 mb-2"></i>
                        <h6 class="font-weight-bold text-slate">Giỏ hàng của phụ huynh đang trống</h6>
                        <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary btn-sm mt-2">
                            Quay lại Cửa hàng chọn đồ cho bé
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. FORM THÔNG TIN GIAO HÀNG -->
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-truck-fast text-peach mr-2"></i>2. Thông Tin Giao Hàng
                        </h5>
                        <small class="text-muted-custom">Điền thông tin người nhận và chọn địa chỉ giao hàng tận nơi</small>
                    </div>
                    <button type="button" onclick="lnktSyncProvincesOpenApi()" class="btn btn-light btn-sm border rounded-pill small font-weight-bold text-slate">
                        <i class="fa-solid fa-cloud-arrow-down text-info mr-1"></i>Làm mới địa giới hành chính
                    </button>
                </div>

                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formCheckoutOrder" method="post" action="/FashionKids/index.php?c=order&amp;a=processCheckout">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_receiverName">
                                    Họ và tên phụ huynh nhận hàng (<span class="text-danger">*</span>):
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-user text-peach"></i></span>
                                    </div>
                                    <input type="text" id="lnkt_receiverName" name="ten_nguoi_nhan" class="form-control"
                                           value="<?= htmlspecialchars($recvName) ?>" placeholder="Nhập họ tên phụ huynh..." required>
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
                                    <input type="tel" id="lnkt_receiverPhone" name="so_dien_thoai_nhan" class="form-control"
                                           value="<?= htmlspecialchars($recvPhone) ?>" placeholder="VD: 0918889999" pattern="[0-9]{10,11}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Hệ thống Dropdown chọn Tỉnh/Thành phố và Quận/Huyện -->
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="tgb_select_province">
                                    Tỉnh / Thành phố (<span class="text-danger">*</span>):
                                </label>
                                <select id="tgb_select_province" name="tinh_thanh_pho" class="custom-select form-control"
                                        required disabled>
                                    <option value="">Đang tải Tỉnh / Thành phố...</option>
                                </select>
                                <small id="lnkt_apiStatusText" class="text-muted-custom d-block mt-1">
                                    <i class="fa-solid fa-spinner fa-spin text-info mr-1"></i>Đang tải danh sách Tỉnh / Thành phố...
                                </small>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="tgb_select_district">
                                    Quận / Huyện (<span class="text-danger">*</span>):
                                </label>
                                <select id="tgb_select_district" name="quan_huyen" class="custom-select form-control"
                                        required disabled>
                                    <option value="">-- Chọn Quận / Huyện --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate" for="lnkt_shippingAddress">
                                Địa chỉ giao hàng chi tiết (Số nhà, Tên đường, Phường/Xã) (<span class="text-danger">*</span>):
                            </label>
                            <input type="text" id="lnkt_shippingAddress" name="dia_chi_giao_hang" class="form-control"
                                   value="<?= htmlspecialchars($recvAddr) ?>" placeholder="Nhập số nhà, tên đường, phường/xã..." required>
                        </div>
                        <input type="hidden" name="thu_dien_tu_nhan" id="tgb_receiver_email" value="<?= htmlspecialchars($recvEmail) ?>">
                        <input type="hidden" id="tgb_full_address" name="dia_chi_day_du" value="<?= htmlspecialchars($recvAddr) ?>">
                        <input type="hidden" id="tgb_checkout_product_id" name="ma_san_pham" value="<?= $firstCartProductId ?>">
                        <input type="hidden" id="tgb_checkout_size" name="kich_co_chon" value="<?= htmlspecialchars($firstCartSize) ?>">
                        <input type="hidden" id="tgb_checkout_qty" name="so_luong" value="<?= $firstCartQty ?>">
                        <input type="hidden" id="tgb_checkout_total" name="tong_tien_sau_giam" value="<?= $init_total_amount ?>">

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-slate" for="lnkt_orderNote">
                                Ghi chú cho nhân viên đóng gói &amp; bưu tá:
                            </label>
                            <textarea id="lnkt_orderNote" rows="2" class="form-control"
                                      placeholder="VD: Giao giờ hành chính, gọi trước cho mẹ bé 15 phút..."></textarea>
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
                        <i class="fa-solid fa-calculator text-peach mr-2"></i>3. Chi Tiết Thanh Toán
                    </h5>
                    <small class="text-muted-custom">Tự động áp dụng ưu đãi theo hạng thẻ thành viên</small>
                </div>

                <div class="card-body p-4 pt-2">
                    <!-- Bộ chọn mô phỏng hạng thẻ VIP để kiểm thử chiết khấu tự động -->
                    <div class="p-3 rounded mb-3 lbn-vip-price-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small font-weight-bold text-slate">
                                <i class="fa-solid fa-crown text-warning mr-1"></i>Hạng thẻ VIP áp dụng:
                            </span>
                            <span id="lnkt_checkoutVipBadge" class="badge <?= htmlspecialchars($vipTierBadgeClass) ?> px-2 py-1 rounded-pill">
                                <?= htmlspecialchars($vipTierTitle) ?>
                            </span>
                        </div>
                        <div class="btn-group btn-group-sm btn-block" role="group">
                            <button type="button" class="btn <?= $init_vip_percent === 0 ? 'btn-pastel-primary active' : 'btn-outline-secondary' ?> font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(0, 'Hạng Đồng (0%)', this)">Đồng 0%</button>
                            <button type="button" class="btn <?= $init_vip_percent === 5 ? 'btn-pastel-primary active' : 'btn-outline-secondary' ?> font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(5, 'Hạng Bạc (-5%)', this)">Bạc -5%</button>
                            <button type="button" class="btn <?= $init_vip_percent === 10 ? 'btn-pastel-primary active' : 'btn-outline-secondary' ?> font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(10, 'Hạng Vàng (-10%)', this)">Vàng -10%</button>
                            <button type="button" class="btn <?= $init_vip_percent === 15 ? 'btn-pastel-primary active' : 'btn-outline-secondary' ?> font-weight-bold lnkt-vip-tier-btn"
                                    onclick="lnktSetCheckoutVipDiscount(15, 'Kim Cương (-15%)', this)">K.Cương -15%</button>
                        </div>
                        <input type="hidden" id="lnkt_checkoutDiscountPercent" value="<?= $init_vip_percent ?>">
                    </div>

                    <!-- Bảng tổng hợp dòng tiền -->
                    <div class="border rounded p-3 bg-milk-cream mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted-custom font-weight-bold">1. Tiền hàng tạm tính:</span>
                            <span id="lnkt_summarySubtotal" class="font-weight-bold text-slate h6 mb-0">
                                <?= number_format($init_subtotal, 0, ',', '.') ?> đ
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted-custom font-weight-bold">
                                2. Ưu đãi thành viên VIP:
                            </span>
                            <span id="lnkt_summaryDiscount" class="font-weight-bold text-success h6 mb-0">
                                <?= $init_discount_amount > 0 ? ('-' . number_format($init_discount_amount, 0, ',', '.') . ' đ') : '0 đ' ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted-custom font-weight-bold">Phí vận chuyển tiêu chuẩn:</span>
                            <span id="lnkt_summaryShipping" class="badge <?= $init_shipping_fee > 0 ? 'badge-warning' : 'badge-mint' ?> px-2 py-1 rounded-pill">
                                <?= $init_shipping_fee > 0 ? number_format($init_shipping_fee, 0, ',', '.') . ' đ' : 'Miễn phí (0 đ)' ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <div>
                                <span class="font-weight-bold text-slate h6 mb-0 d-block">3. Tổng thanh toán:</span>
                                <small class="text-muted-custom">(Đã bao gồm VAT)</small>
                            </div>
                            <span id="lnkt_summaryTotalAmount" class="h4 font-weight-bold text-peach mb-0">
                                <?= number_format($init_total_amount, 0, ',', '.') ?> đ
                            </span>
                        </div>
                    </div>

                    <!-- Điểm thưởng tích lũy sau đơn hàng -->
                    <div class="d-flex justify-content-between align-items-center p-2 px-3 rounded bg-buttercup-soft mb-3 small">
                        <span class="font-weight-bold text-slate">
                            <i class="fa-solid fa-coins text-warning mr-1"></i>Điểm thưởng tích lũy sau đơn hàng:
                        </span>
                        <span id="lnkt_summaryRewardPoints" class="font-weight-bold text-slate">
                            +<?= number_format($init_reward_points) ?> điểm VIP
                        </span>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="mb-4">
                        <label class="small font-weight-bold text-slate mb-2 d-block">Phương thức thanh toán:</label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="lnkt_payCod" name="phuong_thuc_thanh_toan" form="lnkt_formCheckoutOrder" class="custom-control-input" value="cod" checked>
                            <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_payCod">
                                <i class="fa-solid fa-money-bill-wave text-success mr-1"></i> Thanh toán khi nhận hàng (COD - Được đồng kiểm)
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="lnkt_payBank" name="phuong_thuc_thanh_toan" form="lnkt_formCheckoutOrder" class="custom-control-input" value="vietqr">
                            <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_payBank">
                                <i class="fa-solid fa-qrcode text-info mr-1"></i> Chuyển khoản nhanh VietQR / Ví MoMo
                            </label>
                        </div>
                    </div>

                    <!-- Nút Chốt Đặt Hàng -->
                    <button type="submit" id="lnkt_btnSubmitCheckout" form="lnkt_formCheckoutOrder"
                            class="btn btn-pastel-primary btn-block font-weight-bold py-3 shadow">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Xác Nhận Đặt Hàng Ngay
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
