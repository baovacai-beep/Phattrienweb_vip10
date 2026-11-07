<?php
$page_title = "Chi Tiết Sản Phẩm PDP & Gợi Ý Cùng Danh Mục (MH 03)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Kho dữ liệu sản phẩm phục vụ PDP và thuật toán lọc 4 sản phẩm tương tự cùng danh mục
$lbn_all_products = [
    101 => [
        'id'             => 101,
        'sku'            => 'FK-LBN-2026-01',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'gender_label'   => 'Bé gái',
        'price'          => 285000,
        'stock_quantity' => 18,
        'rating'         => 4.9,
        'reviews'        => 42,
        'type'           => 'quan_ao',
        'icon'           => 'fa-person-dress text-peach',
        'material_desc'  => '100% sợi tre hữu cơ (Organic Bamboo) kết hợp lót cotton mềm mại, kháng khuẩn tự nhiên 99.8%, thấm hút mồ hôi gấp 2 lần và không gây kích ứng làn da nhạy cảm của bé.',
        'sizes'          => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'angles'         => [
            ['title' => 'Góc Chính Diện Toàn Thân',       'sub' => 'Phom tùng xòe bồng bềnh chuẩn châu Á',          'icon' => 'fa-person-dress text-peach'],
            ['title' => 'Góc Chụp Nghiêng & Dáng Xòe',    'sub' => 'Độ rủ tự nhiên, co giãn 4 chiều thoải mái',     'icon' => 'fa-wand-magic-sparkles text-info'],
            ['title' => 'Cận Cảnh Bề Mặt Vải Sợi Tre',    'sub' => 'Sợi dệt thoáng khí, làm mát tự nhiên -2°C',     'icon' => 'fa-leaf text-success'],
            ['title' => 'Chi Tiết Đường May & Cúc Bấm',   'sub' => 'Chỉ tơ không cộm ngứa, cúc bấm không chứa BPA', 'icon' => 'fa-shield-heart text-warning']
        ]
    ],
    102 => [
        'id'             => 102,
        'sku'            => 'FK-LBN-2026-02',
        'category_id'    => 2,
        'category_slug'  => 'bo_thun',
        'category_name'  => 'Bộ thun',
        'name'           => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'gender_label'   => 'Bé trai',
        'price'          => 225000,
        'stock_quantity' => 24,
        'rating'         => 4.8,
        'reviews'        => 36,
        'type'           => 'quan_ao',
        'icon'           => 'fa-shirt text-info',
        'material_desc'  => 'Vải thun sợi tre Bamboo kháng khuẩn co giãn 4 chiều, bo chun bụng siêu mềm không để lại vết hằn trên da bé.',
        'sizes'          => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'angles'         => [
            ['title' => 'Góc Chính Diện Bộ Thun',         'sub' => 'Thiết kế năng động cho bé vận động cả ngày',   'icon' => 'fa-shirt text-info'],
            ['title' => 'Góc Sau Lưng & Bo Cổ Co Giãn',   'sub' => 'Bo cổ dệt kim đàn hồi không baião',             'icon' => 'fa-child-reaching text-peach'],
            ['title' => 'Cận Cảnh Sợi Tre Kháng Khuẩn',   'sub' => 'Thấm hút mồ hôi lưng cực nhanh',                'icon' => 'fa-leaf text-success'],
            ['title' => 'Tem Nhãn Ép Nhiệt Không Ngứa',   'sub' => 'Loại bỏ hoàn toàn mác vải cứng ở cổ áo',        'icon' => 'fa-shield-halved text-warning']
        ]
    ],
    103 => [
        'id'             => 103,
        'sku'            => 'FK-LBN-2026-03',
        'category_id'    => 4,
        'category_slug'  => 'giay_sandal',
        'category_name'  => 'Giày sandal',
        'name'           => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'gender_label'   => 'Unisex',
        'price'          => 210000,
        'stock_quantity' => 14,
        'rating'         => 5.0,
        'reviews'        => 58,
        'type'           => 'giay_dep',
        'icon'           => 'fa-shoe-prints text-warning',
        'material_desc'  => 'Đế cao su non tự nhiên uốn dẻo 360 độ chống trơn trượt kết hợp lót đệm vải sợi tre khử mùi và thấm hút mồ hôi chân bé.',
        'sizes'          => ['Size 21 (12cm)', 'Size 22 (13cm)', 'Size 24 (14cm)', 'Size 26 (15cm)', 'Size 28 (16.5cm)'],
        'angles'         => [
            ['title' => 'Góc Nghiêng Đôi Sandal',         'sub' => 'Thiết kế ôm gọn bảo vệ cổ chân bé tập đi',      'icon' => 'fa-shoe-prints text-warning'],
            ['title' => 'Mặt Đế Rãnh Chống Trượt',        'sub' => 'Cao su non nguyên sinh bám sàn vượt trội',      'icon' => 'fa-shield-heart text-peach'],
            ['title' => 'Lót Đệm Sợi Tre Kháng Khuẩn',    'sub' => 'Êm ái, thoáng khí không gây bí chân',           'icon' => 'fa-leaf text-success'],
            ['title' => 'Quai Dán Velcro Siêu Bền',       'sub' => 'Bé dễ dàng tự mang và tháo giày',               'icon' => 'fa-check-double text-info']
        ]
    ],
    104 => [
        'id'             => 104,
        'sku'            => 'FK-LBN-2026-04',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'gender_label'   => 'Unisex',
        'price'          => 320000,
        'stock_quantity' => 5,
        'rating'         => 4.9,
        'reviews'        => 29,
        'type'           => 'quan_ao',
        'icon'           => 'fa-vest-patches text-success',
        'material_desc'  => 'Vải sợi tre dệt kim 2 lớp chống tia UV UPF 50+, giữ ấm nhẹ trong phòng điều hòa và thoáng mát khi ra nắng.',
        'sizes'          => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'angles'         => [
            ['title' => 'Góc Chính Diện Áo Khoác',        'sub' => 'Kiểu dáng thanh lịch dễ phối đồ cho bé',        'icon' => 'fa-vest-patches text-success'],
            ['title' => 'Góc Chụp Nghiêng & Tay Bo',      'sub' => 'Bo tay co giãn cản gió nhẹ nhàng',              'icon' => 'fa-wind text-info'],
            ['title' => 'Cận Cảnh Vải Sợi Tre UPF 50+',   'sub' => 'Chống tia cực tím bảo vệ làn da bé',            'icon' => 'fa-sun text-warning'],
            ['title' => 'Cúc Gỗ Tự Nhiên An Toàn',        'sub' => 'Mài nhẵn bo tròn không góc cạnh',               'icon' => 'fa-leaf text-peach']
        ]
    ],
    // Các sản phẩm cùng danh mục Váy công chúa để thuật toán gợi ý hiển thị đủ 4 thẻ cùng danh mục
    105 => [
        'id'             => 105,
        'sku'            => 'FK-LBN-2026-05',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Tùng Xòe Pastel Nơ Bản To Dự Tiệc',
        'gender_label'   => 'Bé gái',
        'price'          => 365000,
        'stock_quantity' => 11,
        'rating'         => 5.0,
        'reviews'        => 64,
        'type'           => 'quan_ao',
        'icon'           => 'fa-crown text-peach',
        'material_desc'  => 'Lớp lót 100% vải sợi tre kháng khuẩn tiếp xúc trực tiếp với da bé kết hợp voan mềm cao cấp bên ngoài.',
        'sizes'          => ['Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'angles'         => []
    ],
    110 => [
        'id'             => 110,
        'sku'            => 'FK-LBN-2026-10',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Cổ Sen Thêu Hoa Nhí Sợi Tre Mùa Hè',
        'gender_label'   => 'Bé gái',
        'price'          => 295000,
        'stock_quantity' => 15,
        'rating'         => 4.9,
        'reviews'        => 38,
        'type'           => 'quan_ao',
        'icon'           => 'fa-wand-magic-sparkles text-peach',
        'material_desc'  => '100% sợi tre hữu cơ kháng khuẩn, cổ sen thêu tay tinh xảo.',
        'sizes'          => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)'],
        'angles'         => []
    ],
    111 => [
        'id'             => 111,
        'sku'            => 'FK-LBN-2026-11',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Tay Phồng Hồng Đào Bamboo Organic',
        'gender_label'   => 'Bé gái',
        'price'          => 315000,
        'stock_quantity' => 9,
        'rating'         => 4.8,
        'reviews'        => 27,
        'type'           => 'quan_ao',
        'icon'           => 'fa-person-dress text-danger',
        'material_desc'  => 'Chất vải sợi tre tự nhiên mềm mịn như lụa, điều hòa thân nhiệt cho bé.',
        'sizes'          => ['Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'angles'         => []
    ],
    112 => [
        'id'             => 112,
        'sku'            => 'FK-LBN-2026-12',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Hai Dây Nơ Vai Sợi Tre Mát Lạnh',
        'gender_label'   => 'Bé gái',
        'price'          => 260000,
        'stock_quantity' => 21,
        'rating'         => 4.9,
        'reviews'        => 45,
        'type'           => 'quan_ao',
        'icon'           => 'fa-heart text-peach',
        'material_desc'  => '100% sợi tre kháng khuẩn thoáng mát cho ngày hè đi biển.',
        'sizes'          => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)'],
        'angles'         => []
    ]
];

$requested_id = isset($_GET['id']) ? (int)$_GET['id'] : 101;
$current_product = isset($lbn_all_products[$requested_id]) ? $lbn_all_products[$requested_id] : $lbn_all_products[101];
if (empty($current_product['angles'])) {
    $current_product['angles'] = $lbn_all_products[101]['angles'];
}

// Tính giá tự động theo cấp bậc VIP mặc định (Hạng Vàng - Giảm 10% theo hgb_get_discount_percent)
$vip_tier_name = "Vàng (Gold)";
$vip_discount_percent = 10;
$base_price = $current_product['price'];
$discount_amount = round(($base_price * $vip_discount_percent) / 100);
$vip_final_price = $base_price - $discount_amount;

// Thuật toán lọc 4 thẻ sản phẩm tương tự cùng danh mục ở cuối trang
$lbn_related_products = [];
foreach ($lbn_all_products as $item) {
    if ($item['id'] !== $current_product['id'] && $item['category_id'] === $current_product['category_id']) {
        $lbn_related_products[] = $item;
    }
    if (count($lbn_related_products) === 4) {
        break;
    }
}
// Nếu danh mục đang xem chưa đủ 4 SP mẫu thì bổ sung các SP nổi bật khác để luôn hiển thị đủ 4 thẻ
if (count($lbn_related_products) < 4) {
    foreach ($lbn_all_products as $item) {
        if ($item['id'] !== $current_product['id'] && !in_array($item, $lbn_related_products, true)) {
            $lbn_related_products[] = $item;
        }
        if (count($lbn_related_products) === 4) {
            break;
        }
    }
}
?>

<div class="container my-4 my-md-5" id="lbn_pdp_container">
    <!-- Breadcrumb điều hướng -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 mb-0 small">
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php?category=<?= $current_product['category_slug'] ?>" class="text-muted-custom"><?= htmlspecialchars($current_product['category_name']) ?></a></li>
            <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page"><?= htmlspecialchars($current_product['name']) ?></li>
        </ol>
    </nav>

    <!-- ====================================================================
         KHỐI THÔNG TIN CHÍNH PDP (MH 03 - KHỐI TRÊN)
         ==================================================================== -->
    <div class="card card-pastel p-4 p-lg-5 shadow-sm mb-5">
        <div class="row">
            <!-- CỘT TRÁI: SLIDER ẢNH SẢN PHẨM ĐA GÓC CHỤP -->
            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                <div id="lbn_pdpImageSlider" class="carousel slide lbn-pdp-slider-main shadow-sm mb-3" data-ride="carousel" data-interval="false">
                    <div class="carousel-inner">
                        <?php foreach ($current_product['angles'] as $idx => $angle): ?>
                            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                <div class="lbn-pdp-slide-item text-center position-relative">
                                    <span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">
                                        <i class="fa-solid fa-leaf mr-1"></i>100% Vải Sợi Tre Kháng Khuẩn
                                    </span>
                                    <span class="badge badge-pill badge-light border lbn-badge-gender-overlay px-3 py-1 font-weight-bold text-slate">
                                        Góc chụp <?= $idx + 1 ?>/<?= count($current_product['angles']) ?>
                                    </span>

                                    <div class="my-4">
                                        <i class="fa-solid <?= $angle['icon'] ?> display-1"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-slate mb-1"><?= htmlspecialchars($angle['title']) ?></h5>
                                    <p class="small text-muted-custom mb-0"><?= htmlspecialchars($angle['sub']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Nút điều hướng Trái / Phải trên Slider -->
                    <a class="carousel-control-prev" href="#lbn_pdpImageSlider" role="button" data-slide="prev">
                        <span class="btn btn-light rounded-circle shadow-sm border d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa-solid fa-chevron-left text-slate"></i>
                        </span>
                    </a>
                    <a class="carousel-control-next" href="#lbn_pdpImageSlider" role="button" data-slide="next">
                        <span class="btn btn-light rounded-circle shadow-sm border d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa-solid fa-chevron-right text-slate"></i>
                        </span>
                    </a>
                </div>

                <!-- Dãy 4 Thumbnail chọn góc chụp nhanh -->
                <div class="row mx-n1">
                    <?php foreach ($current_product['angles'] as $idx => $angle): ?>
                        <div class="col-3 px-1">
                            <div class="lbn-pdp-thumb <?= $idx === 0 ? 'active' : '' ?> p-2 text-center"
                                 onclick="lbnSwitchPdpSlide(<?= $idx ?>, this)">
                                <i class="fa-solid <?= $angle['icon'] ?> h4 mb-1"></i>
                                <small class="text-slate font-weight-bold d-block text-truncate w-100" style="font-size: 0.7rem;">
                                    Góc <?= $idx + 1 ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CỘT PHẢI: THÔNG TIN CHI TIẾT, GIÁ VIP & CỤM NÚT TƯƠNG TÁC -->
            <div class="col-12 col-lg-6">
                <!-- Danh mục & Nhãn tình trạng kho -->
                <div class="d-flex flex-wrap align-items-center mb-2">
                    <span class="badge badge-pill badge-sky px-3 py-1 mr-2 mb-1">
                        <?= htmlspecialchars($current_product['category_name']) ?> • <?= htmlspecialchars($current_product['gender_label']) ?>
                    </span>
                    <?php if ($current_product['stock_quantity'] <= 5): ?>
                        <span class="badge lbn-badge-low-stock mb-1">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>Sắp hết hàng (Còn <?= $current_product['stock_quantity'] ?> SP)
                        </span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-mint px-3 py-1 mb-1">
                            <i class="fa-solid fa-check-circle mr-1"></i>Còn sẵn <?= $current_product['stock_quantity'] ?> sản phẩm
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Tên sản phẩm -->
                <h3 class="font-weight-bold text-slate mb-2" id="lbn_pdpProductTitle">
                    <?= htmlspecialchars($current_product['name']) ?>
                </h3>

                <!-- Mã SKU & Điểm sao trung bình -->
                <div class="d-flex flex-wrap align-items-center small text-muted-custom mb-3 pb-3 border-bottom">
                    <div class="mr-3">
                        <span class="font-weight-bold text-slate">Mã SKU:</span>
                        <code class="text-peach font-weight-bold ml-1"><?= htmlspecialchars($current_product['sku']) ?></code>
                    </div>
                    <div class="mr-3 text-warning font-weight-bold">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                        <span class="text-slate ml-1"><?= number_format($current_product['rating'], 1) ?>/5</span>
                    </div>
                    <div>
                        (<strong><?= $current_product['reviews'] ?></strong> đánh giá từ phụ huynh)
                    </div>
                </div>

                <!-- KHỐI GIÁ NIÊM YẾT & NHÃN GIẢM GIÁ TỰ ĐỘNG THEO CẤP BẬC VIP -->
                <div class="lbn-vip-price-box p-3 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <span id="lbn_pdpVipBadgeLabel" class="badge badge-gold px-3 py-1 rounded-pill shadow-sm">
                            <i class="fa-solid fa-crown mr-1"></i> Đặc quyền VIP <?= $vip_tier_name ?>: Giảm tự động <?= $vip_discount_percent ?>%
                        </span>
                        <span id="lbn_pdpSavedAmount" class="small font-weight-bold text-success">
                            Tiết kiệm ngay <?= number_format($discount_amount, 0, ',', '.') ?> đ
                        </span>
                    </div>

                    <div class="d-flex align-items-baseline">
                        <span id="lbn_pdpFinalVipPrice" class="h3 font-weight-bold text-peach mb-0 mr-3">
                            <?= number_format($vip_final_price, 0, ',', '.') ?> đ
                        </span>
                        <del id="lbn_pdpBasePrice" data-base-price="<?= $base_price ?>" class="text-muted-custom font-weight-bold">
                            Giá niêm yết: <?= number_format($base_price, 0, ',', '.') ?> đ
                        </del>
                    </div>

                    <!-- Bộ nút mô phỏng tự động tính chiết khấu theo 4 hạng VIP (hgb_get_discount_percent) -->
                    <div class="mt-2 pt-2 border-top d-flex flex-wrap align-items-center justify-content-between">
                        <small class="text-muted-custom font-weight-bold mb-1 mb-sm-0">
                            <i class="fa-solid fa-calculator mr-1"></i>Xem giá tự động theo hạng VIP:
                        </small>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" id="lbn_vipBtn_bronze" class="btn btn-outline-secondary lbn-vip-tier-btn font-weight-bold"
                                    onclick="lbnSwitchVipTierDiscount('bronze', 0, 'Đồng (0%)')">Đồng 0%</button>
                            <button type="button" id="lbn_vipBtn_silver" class="btn btn-outline-secondary lbn-vip-tier-btn font-weight-bold"
                                    onclick="lbnSwitchVipTierDiscount('silver', 5, 'Bạc (5%)')">Bạc -5%</button>
                            <button type="button" id="lbn_vipBtn_gold" class="btn btn-pastel-primary active lbn-vip-tier-btn font-weight-bold"
                                    onclick="lbnSwitchVipTierDiscount('gold', 10, 'Vàng (10%)')">Vàng -10%</button>
                            <button type="button" id="lbn_vipBtn_diamond" class="btn btn-outline-secondary lbn-vip-tier-btn font-weight-bold"
                                    onclick="lbnSwitchVipTierDiscount('diamond', 15, 'Kim Cương (15%)')">Kim Cương -15%</button>
                        </div>
                    </div>
                </div>

                <!-- Mô tả chất liệu an toàn -->
                <p class="small text-slate mb-3">
                    <i class="fa-solid fa-shield-heart text-success mr-1"></i>
                    <strong>Chất liệu an toàn:</strong> <?= htmlspecialchars($current_product['material_desc']) ?>
                </p>

                <!-- CHỌN KÍCH CỠ & NÚT "BÉ MẶC VỪA SIZE NÀO?" (MỞ MODAL MH 04) -->
                <div class="mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <label class="small font-weight-bold text-slate mb-0">
                            Chọn kích cỡ: <span id="lbn_selectedSizeText" class="text-peach"><?= htmlspecialchars($current_product['sizes'][1]) ?></span>
                        </label>
                        <input type="hidden" id="lbn_selectedSizeInput" value="<?= htmlspecialchars($current_product['sizes'][1]) ?>">

                        <!-- Nút mở Modal Gợi ý Size Thước dây MH 04 -->
                        <button type="button" class="btn btn-pastel-sky btn-sm py-1 px-3 shadow-sm"
                                data-toggle="modal" data-target="#modalSuggestSize">
                            <i class="fa-solid fa-ruler-horizontal mr-1 text-peach"></i> Bé mặc vừa size nào? (MH 04)
                        </button>
                    </div>

                    <div class="d-flex flex-wrap">
                        <?php foreach ($current_product['sizes'] as $sIdx => $sizeLabel): ?>
                            <button type="button"
                                    class="lbn-size-option-btn mr-2 mb-2 small <?= $sIdx === 1 ? 'active' : '' ?>"
                                    onclick="lbnSelectPdpSize('<?= htmlspecialchars($sizeLabel, ENT_QUOTES) ?>', this)">
                                <?= htmlspecialchars($sizeLabel) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- CỤM NÚT TƯƠNG TÁC: SỐ LƯỢNG, THÊM VÀO GIỎ, MUA NGAY -->
                <div class="row align-items-center mb-4">
                    <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-light border font-weight-bold" type="button" onclick="lbnChangePdpQty(-1)">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                            </div>
                            <input type="number" id="lbn_pdpQuantity" class="form-control text-center font-weight-bold"
                                   value="1" min="1" max="<?= $current_product['stock_quantity'] ?>" data-max="<?= $current_product['stock_quantity'] ?>">
                            <div class="input-group-append">
                                <button class="btn btn-light border font-weight-bold" type="button" onclick="lbnChangePdpQty(1)">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-4">
                        <button type="button" id="lbn_btnAddToCart" onclick="lbnAddToCartPdp()"
                                class="btn btn-pastel-outline btn-block font-weight-bold py-2">
                            <i class="fa-solid fa-cart-plus mr-1"></i> Thêm Vào Giỏ
                        </button>
                    </div>

                    <div class="col-6 col-sm-4">
                        <button type="button" id="lbn_btnBuyNow" onclick="lbnBuyNowPdp()"
                                class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-bolt mr-1"></i> Mua Ngay
                        </button>
                    </div>
                </div>

                <!-- CỤM NÚT CHIA SẺ LÊN MẠNG XÃ HỘI -->
                <div class="pt-3 border-top d-flex flex-wrap justify-content-between align-items-center">
                    <span class="small font-weight-bold text-muted-custom mb-2 mb-sm-0">
                        <i class="fa-solid fa-share-nodes mr-1 text-peach"></i> Chia sẻ sản phẩm cho hội bỉm sữa:
                    </span>
                    <div class="d-flex align-items-center">
                        <button type="button" onclick="lbnShareProductSocial('Facebook')"
                                class="btn btn-sm btn-light rounded-pill border mr-2 px-3 text-primary font-weight-bold shadow-sm">
                            <i class="fa-brands fa-facebook-f mr-1"></i> Facebook
                        </button>
                        <button type="button" onclick="lbnShareProductSocial('Messenger / Zalo')"
                                class="btn btn-sm btn-light rounded-pill border mr-2 px-3 text-info font-weight-bold shadow-sm">
                            <i class="fa-brands fa-facebook-messenger mr-1"></i> Messenger
                        </button>
                        <button type="button" onclick="lbnShareProductSocial('copy')"
                                class="btn btn-sm btn-light rounded-pill border px-3 text-slate font-weight-bold shadow-sm">
                            <i class="fa-solid fa-link mr-1"></i> Sao chép Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         KHỐI GỢI Ý: THUẬT TOÁN HIỂN THỊ 4 THẺ SẢN PHẨM TƯƠNG TỰ CÙNG DANH MỤC
         ==================================================================== -->
    <div class="mt-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="font-weight-bold text-slate mb-1">
                    <i class="fa-solid fa-sparkles text-peach mr-2"></i>Sản Phẩm Tương Tự Cùng Danh Mục "<?= htmlspecialchars($current_product['category_name']) ?>"
                </h4>
                <small class="text-muted-custom">
                    Thuật toán gợi ý 4 sản phẩm cùng nhóm danh mục và chất liệu sợi tre an toàn cho bé
                </small>
            </div>
            <a href="/FashionKids/views/pages/shop-catalog.php?category=<?= $current_product['category_slug'] ?>"
               class="btn btn-pastel-outline btn-sm font-weight-bold mt-2 mt-sm-0">
                Xem tất cả <?= htmlspecialchars($current_product['category_name']) ?> <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($lbn_related_products as $rel): ?>
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="lbn-product-card h-100 d-flex flex-column shadow-sm">
                        <div class="lbn-product-img-wrap">
                            <span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">
                                <i class="fa-solid fa-leaf mr-1"></i>Sợi Tre Kháng Khuẩn
                            </span>
                            <a href="/FashionKids/views/pages/product-detail.php?id=<?= $rel['id'] ?>" class="text-decoration-none">
                                <i class="fa-solid <?= $rel['icon'] ?> lbn-main-icon"></i>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-pill badge-sky px-2 py-1 small">
                                    <?= htmlspecialchars($rel['category_name']) ?>
                                </span>
                                <span class="small text-warning font-weight-bold">
                                    <i class="fa-solid fa-star"></i> <?= number_format($rel['rating'], 1) ?>
                                </span>
                            </div>

                            <h6 class="font-weight-bold text-slate mb-2">
                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $rel['id'] ?>" class="text-slate text-decoration-none">
                                    <?= htmlspecialchars($rel['name']) ?>
                                </a>
                            </h6>

                            <small class="text-muted-custom mb-3">Mã SP: <?= htmlspecialchars($rel['sku']) ?></small>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="h6 font-weight-bold text-peach mb-0">
                                    <?= number_format($rel['price'], 0, ',', '.') ?> đ
                                </span>
                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $rel['id'] ?>"
                                   class="btn btn-pastel-primary btn-sm px-3 py-1">
                                    Xem SP
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
require_once "../modals/modal-suggest-size.php";
require_once "../layouts/footer.php";
?>
