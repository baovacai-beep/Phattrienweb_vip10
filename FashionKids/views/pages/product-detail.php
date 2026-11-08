<?php
$page_title = "Chi Tiết Sản Phẩm & Đánh Giá Thực Tế (MH 03)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh mục chuẩn của Lê Bảo Ngọc (lbn_categories)
$lbn_categories = [
    1 => ['id' => 1, 'slug' => 'vay_cong_chua', 'name' => 'Váy công chúa', 'type' => 'quan_ao',  'icon' => 'fa-wand-magic-sparkles text-peach'],
    2 => ['id' => 2, 'slug' => 'bo_thun',       'name' => 'Bộ thun',       'type' => 'quan_ao',  'icon' => 'fa-shirt text-info'],
    3 => ['id' => 3, 'slug' => 'ao_khoac',      'name' => 'Áo khoác',      'type' => 'quan_ao',  'icon' => 'fa-vest-patches text-success'],
    4 => ['id' => 4, 'slug' => 'giay_sandal',   'name' => 'Giày sandal',   'type' => 'giay_dep', 'icon' => 'fa-shoe-prints text-warning']
];

// Danh sách sản phẩm mẫu chuẩn lbn_products
$lbn_products = [
    101 => [
        'id'             => 101,
        'sku'            => 'FK-LBN-01',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 285000,
        'original_price' => 340000,
        'stock_quantity' => 18,
        'rating'         => 4.9,
        'reviews'        => 128,
        'sold_count'     => 420,
        'is_bamboo'      => 1,
        'bamboo_label'   => '100% Sợi Tre Kháng Khuẩn Tự Nhiên',
        'size_range'     => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-person-dress text-peach',
        'colors'         => [
            ['name' => 'Hồng Phấn Pastel', 'code' => '#FFD1DC', 'active' => true],
            ['name' => 'Kem Sữa Dịu Ngọt', 'code' => '#FFFDD0', 'active' => false],
            ['name' => 'Xanh Bạc Hà Mint', 'code' => '#B8E0D2', 'active' => false]
        ],
        'description'    => 'Váy công chúa được dệt từ 100% sợi tre Organic kết hợp tùng ren hoa cúc họa mi thêu nổi mềm mại. Chất vải kháng khuẩn tự nhiên, chống nấm mốc và giảm tĩnh điện, bảo vệ làn da mỏng manh của bé suốt cả ngày dài.',
        'material'       => '95% Vải sợi tre hữu cơ tự nhiên (Organic Bamboo Fiber), 5% Spandex tăng đàn hồi co giãn 4 chiều.',
        'certifications' => ['OEKO-TEX® Standard 100', 'Kháng khuẩn 99.8% Test Intertek', 'Không hóa chất Formaldehyde'],
        'wash_guide'     => 'Giặt máy chế độ nhẹ hoặc giặt tay nước ấm < 30°C. Tránh phơi trực tiếp dưới nắng gắt để giữ màu pastel tươi sáng.'
    ],
    102 => [
        'id'             => 102,
        'sku'            => 'FK-LBN-02',
        'category_id'    => 2,
        'category_slug'  => 'bo_thun',
        'category_name'  => 'Bộ thun',
        'name'           => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 225000,
        'original_price' => 280000,
        'stock_quantity' => 24,
        'rating'         => 4.8,
        'reviews'        => 95,
        'sold_count'     => 560,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Sợi Tre Bamboo Thoáng Khí Làm Mát -3°C',
        'size_range'     => ['Size 1 (0-1T)', 'Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (1-2T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-shirt text-info',
        'colors'         => [
            ['name' => 'Xanh Da Trời Soft Sky', 'code' => '#A8DADC', 'active' => true],
            ['name' => 'Vàng Bơ Buttercup', 'code' => '#FDE2B8', 'active' => false],
            ['name' => 'Ghi Sáng Pastel', 'code' => '#E2E8F0', 'active' => false]
        ],
        'description'    => 'Bộ thun cộc tay sợi tre mát lạnh, in hình chú khủng long nhí tinh nghịch bằng mực in hữu cơ gốc nước không bong tróc, bo chun bụng siêu mềm không tạo vết hằn đỏ trên bụng bé.',
        'material'       => '100% sợi tre Bamboo kết hợp công nghệ dệt Cool-Touch làm mát tức thì.',
        'certifications' => ['Chứng nhận an toàn da liễu cho trẻ sơ sinh', 'OEKO-TEX Class 1 Baby'],
        'wash_guide'     => 'Có thể giặt máy, lộn mặt trái khi giặt và là ủi ở nhiệt độ trung bình.'
    ],
    103 => [
        'id'             => 103,
        'sku'            => 'FK-LBN-03',
        'category_id'    => 4,
        'category_slug'  => 'giay_sandal',
        'category_name'  => 'Giày sandal',
        'name'           => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-gold',
        'price'          => 210000,
        'original_price' => 260000,
        'stock_quantity' => 14,
        'rating'         => 5.0,
        'reviews'        => 74,
        'sold_count'     => 310,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Lót Sợi Tre Khử Mùi & Đế Uốn Gập 360°',
        'size_range'     => ['Size 21 (12cm)', 'Size 22 (13cm)', 'Size 24 (14cm)', 'Size 26 (15cm)', 'Size 28 (16.5cm)'],
        'default_size'   => 'Size 22 (13cm)',
        'type'           => 'giay_dep',
        'icon'           => 'fa-shoe-prints text-warning',
        'colors'         => [
            ['name' => 'Nâu Be Tự Nhiên', 'code' => '#E2D4C0', 'active' => true],
            ['name' => 'Hồng Pastel', 'code' => '#FFD1DC', 'active' => false],
            ['name' => 'Xanh Mint Dịu Nhẹ', 'code' => '#B8E0D2', 'active' => false]
        ],
        'description'    => 'Sandal hỗ trợ những bước đi đầu đời vững chắc cho bé. Đế cao su non tự nhiên uốn gập 360 độ siêu dẻo, rãnh chống trượt tổ ong cùng lót đệm sợi tre kháng khuẩn khử mùi hôi chân hiệu quả.',
        'material'       => 'Thân quai dệt sợi tổng hợp cao cấp, lót trong sợi tre êm ái, đế cao su tự nhiên an toàn.',
        'certifications' => ['Đạt chuẩn công thái học tập đi cho trẻ 1-4 tuổi', 'Không kim loại nặng'],
        'wash_guide'     => 'Lau sạch bằng khăn ẩm hoặc giặt tay với xà phòng dịu nhẹ, phơi nơi râm mát thoáng gió.'
    ],
    104 => [
        'id'             => 104,
        'sku'            => 'FK-LBN-04',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-mint',
        'price'          => 320000,
        'original_price' => 380000,
        'stock_quantity' => 5,
        'rating'         => 4.9,
        'reviews'        => 29,
        'sold_count'     => 185,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Sợi Tre Dệt Kim UPF 50+ Chống Tia Cực Tím',
        'size_range'     => ['Size 2 (1-2T)', 'Size 3 (2-3T)', 'Size 4 (3-4T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (1-2T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-vest-patches text-success',
        'colors'         => [
            ['name' => 'Xanh Mint Pastel', 'code' => '#B8E0D2', 'active' => true],
            ['name' => 'Vàng Bơ Dịu Dàng', 'code' => '#FDE2B8', 'active' => false]
        ],
        'description'    => 'Áo khoác Cardigan sợi tre dệt 2 lớp mềm xốp, trang bị chỉ số chống nắng tự nhiên UPF 50+ cản 98% tia UVA/UVB, cúc gỗ mài nhẵn không xước da bé.',
        'material'       => '100% Sợi tre dệt kim tự nhiên Organic Bamboo.',
        'certifications' => ['Chứng nhận chống tia cực tím UPF 50+', 'OEKO-TEX Standard 100'],
        'wash_guide'     => 'Giặt tay nhẹ nhàng hoặc dùng túi giặt chế độ đồ len em bé.'
    ]
];

// Lấy ID sản phẩm từ URL (mặc định ID 101)
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 101;
if (!isset($lbn_products[$product_id])) {
    $product_id = 101;
}
$prod = $lbn_products[$product_id];

// Tính toán chiết khấu thẻ VIP mẫu (Mặc định Hạng Vàng - Giảm 10%)
$init_discount_percent = 10;
$init_discount_amount = round(($prod['price'] * $init_discount_percent) / 100);
$init_vip_price = $prod['price'] - $init_discount_amount;
?>

<div class="container my-4 my-md-5" id="lbn_pdp_section">
    <!-- Breadcrumb Điều Hướng -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3 small">
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php" class="text-muted-custom">Cửa hàng</a></li>
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php?category=<?= $prod['category_slug'] ?>" class="text-muted-custom"><?= htmlspecialchars($prod['category_name']) ?></a></li>
            <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page"><?= htmlspecialchars($prod['name']) ?></li>
        </ol>
    </nav>

    <!-- ======================================================================
         KHỐI TRÊN PDP: HÌNH ẢNH, CHI TIẾT SẢN PHẨM & TƯƠNG TÁC (MH 03)
         Đảm nhiệm: Lê Bảo Ngọc (lbn_) - Giai đoạn 2 Bootstrap 4 Responsive
         ====================================================================== -->
    <div class="card card-pastel border-0 shadow-sm mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="row">
                <!-- CỘT TRÁI (6 CỘT): SLIDER ẢNH ĐA GÓC CHỤP & THUMBNAILS -->
                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <!-- Khung Carousel Ảnh Chính -->
                    <div id="lbn_pdpImageSlider" class="carousel slide lbn-pdp-slider-main shadow-sm position-relative mb-3" data-ride="carousel" data-interval="false">
                        <!-- Badges Nổi Bật -->
                        <div class="position-absolute" style="top: 15px; left: 15px; z-index: 5;">
                            <span class="badge lbn-badge-bamboo px-3 py-2 shadow-sm d-inline-block mb-1">
                                <i class="fa-solid fa-leaf mr-1"></i><?= htmlspecialchars($prod['bamboo_label']) ?>
                            </span>
                            <br>
                            <span class="badge badge-pill <?= $prod['gender_badge'] ?> px-2 py-1 shadow-sm font-weight-bold">
                                <?= htmlspecialchars($prod['gender_label']) ?>
                            </span>
                        </div>

                        <!-- Cảnh báo tồn kho nếu <= 5 -->
                        <?php if ($prod['stock_quantity'] <= 5): ?>
                            <div class="position-absolute" style="top: 15px; right: 15px; z-index: 5;">
                                <span class="badge lbn-badge-low-stock shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>Chỉ còn <?= $prod['stock_quantity'] ?> cái
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="carousel-inner text-center">
                            <div class="carousel-item active">
                                <div class="lbn-pdp-slide-item">
                                    <i class="fa-solid <?= $prod['icon'] ?> display-1 text-peach mb-3" style="font-size: 8rem; opacity: 0.9;"></i>
                                    <h6 class="font-weight-bold text-slate">Ảnh Mặt Trước (Góc Chụp Chuẩn)</h6>
                                    <small class="text-muted-custom">100% Sợi Tre Organic • Đường May Chìm Êm Da</small>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="lbn-pdp-slide-item">
                                    <i class="fa-solid fa-shirt display-1 text-info mb-3" style="font-size: 8rem; opacity: 0.9;"></i>
                                    <h6 class="font-weight-bold text-slate">Ảnh Mặt Sau &amp; Chi Tiết Khóa/Bo Chun</h6>
                                    <small class="text-muted-custom">Khóa Kéo Giọt Nước An Toàn • Không Cấn Gáy Bé</small>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="lbn-pdp-slide-item">
                                    <i class="fa-solid fa-microscope display-1 text-success mb-3" style="font-size: 8rem; opacity: 0.9;"></i>
                                    <h6 class="font-weight-bold text-slate">Cận Cảnh Sớ Vải Dệt Kháng Khuẩn</h6>
                                    <small class="text-muted-custom">Mềm Mịn Như Lụa • Thấm Hút Mồ Hôi Gấp 2 Lần Cotton</small>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="lbn-pdp-slide-item">
                                    <i class="fa-solid fa-camera display-1 text-warning mb-3" style="font-size: 8rem; opacity: 0.9;"></i>
                                    <h6 class="font-weight-bold text-slate">Ảnh Bé Mẫu Thực Tế (Lookbook 2026)</h6>
                                    <small class="text-muted-custom">Bé Sam (28 tháng - 13kg) Mặc Vừa Vặn Size 2</small>
                                </div>
                            </div>
                        </div>

                        <!-- Điều Hướng Trái / Phải -->
                        <a class="carousel-control-prev" href="#lbn_pdpImageSlider" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon rounded-circle bg-dark p-2" aria-hidden="true" style="opacity: 0.5;"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#lbn_pdpImageSlider" role="button" data-slide="next">
                            <span class="carousel-control-next-icon rounded-circle bg-dark p-2" aria-hidden="true" style="opacity: 0.5;"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

                    <!-- Hàng Thumbnail Bấm Đổi Slide Ảnh -->
                    <div class="row no-gutters">
                        <div class="col-3 p-1">
                            <div class="lbn-pdp-thumb active" onclick="lbnSwitchPdpSlide(0, this)">
                                <i class="fa-solid <?= $prod['icon'] ?> text-peach mb-1"></i>
                                <span class="small font-weight-bold text-slate" style="font-size: 0.75rem;">Mặt Trước</span>
                            </div>
                        </div>
                        <div class="col-3 p-1">
                            <div class="lbn-pdp-thumb" onclick="lbnSwitchPdpSlide(1, this)">
                                <i class="fa-solid fa-shirt text-info mb-1"></i>
                                <span class="small font-weight-bold text-slate" style="font-size: 0.75rem;">Mặt Sau</span>
                            </div>
                        </div>
                        <div class="col-3 p-1">
                            <div class="lbn-pdp-thumb" onclick="lbnSwitchPdpSlide(2, this)">
                                <i class="fa-solid fa-microscope text-success mb-1"></i>
                                <span class="small font-weight-bold text-slate" style="font-size: 0.75rem;">Cận Vải</span>
                            </div>
                        </div>
                        <div class="col-3 p-1">
                            <div class="lbn-pdp-thumb" onclick="lbnSwitchPdpSlide(3, this)">
                                <i class="fa-solid fa-camera text-warning mb-1"></i>
                                <span class="small font-weight-bold text-slate" style="font-size: 0.75rem;">Bé Mặc</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Cam Kết Vàng Về Sản Phẩm -->
                    <div class="row mt-4 pt-3 border-top text-center text-muted-custom small">
                        <div class="col-4">
                            <i class="fa-solid fa-shield-halved text-success h5 d-block mb-1"></i>
                            <span class="font-weight-bold text-slate d-block">100% Sợi Tre</span>
                            Chuẩn OEKO-TEX
                        </div>
                        <div class="col-4 border-left border-right">
                            <i class="fa-solid fa-rotate-left text-peach h5 d-block mb-1"></i>
                            <span class="font-weight-bold text-slate d-block">15 Ngày Đổi Trả</span>
                            Miễn phí nếu bé chật
                        </div>
                        <div class="col-4">
                            <i class="fa-solid fa-truck-fast text-info h5 d-block mb-1"></i>
                            <span class="font-weight-bold text-slate d-block">Giao Hỏa Tốc 2H</span>
                            Được kiểm hàng trước
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI (6 CỘT): THÔNG TIN GIÁ, SIZE & ĐẶT MUA -->
                <div class="col-12 col-lg-6 pl-lg-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-pill badge-light border mr-2 font-weight-bold text-slate">
                            Mã SP: <code><?= htmlspecialchars($prod['sku']) ?></code>
                        </span>
                        <span class="badge badge-pill badge-light border text-muted-custom">
                            Danh mục: <?= htmlspecialchars($prod['category_name']) ?>
                        </span>
                    </div>

                    <!-- Tiêu Đề Sản Phẩm -->
                    <h1 class="h3 font-weight-bold text-slate mb-2" id="lbn_pdpProductTitle">
                        <?= htmlspecialchars($prod['name']) ?>
                    </h1>

                    <!-- Đánh Giá Sao & Lượt Bán -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-warning mr-2">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span class="font-weight-bold text-slate ml-1"><?= $prod['rating'] ?></span>
                        </div>
                        <span class="text-muted-custom small border-left pl-2 mr-2">
                            <a href="#pdp_review_section" class="text-muted-custom text-decoration-none"><strong><?= $prod['reviews'] ?></strong> đánh giá</a>
                        </span>
                        <span class="text-muted-custom small border-left pl-2">
                            Đã bán <strong><?= $prod['sold_count'] ?></strong> món
                        </span>
                    </div>

                    <!-- Khung Giá & Bảng Khấu Trừ Chiết Khấu VIP Tự Động (hgb_get_discount_percent) -->
                    <div class="p-3 rounded mb-3 bg-milk-cream border">
                        <div class="d-flex align-items-baseline mb-2">
                            <span class="h2 font-weight-bold text-peach mb-0 mr-3" id="lbn_pdpBasePrice" data-base-price="<?= $prod['price'] ?>">
                                <?= number_format($prod['price'], 0, ',', '.') ?> đ
                            </span>
                            <span class="text-muted-custom text-decoration-line-through small mr-2">
                                <?= number_format($prod['original_price'], 0, ',', '.') ?> đ
                            </span>
                            <span class="badge badge-pill badge-danger small font-weight-bold">
                                -<?= round((($prod['original_price'] - $prod['price']) / $prod['original_price']) * 100) ?>%
                            </span>
                        </div>

                        <!-- Khung Mô Phỏng Giá VIP -->
                        <div class="p-3 rounded lbn-vip-price-box">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small font-weight-bold text-slate" id="lbn_pdpVipBadgeLabel">
                                    <i class="fa-solid fa-crown text-warning mr-1"></i>Đặc quyền VIP Hạng Vàng: Giảm tự động 10%
                                </span>
                                <span class="h5 font-weight-bold text-success mb-0" id="lbn_pdpFinalVipPrice">
                                    <?= number_format($init_vip_price, 0, ',', '.') ?> đ
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted-custom" id="lbn_pdpSavedAmount">
                                    Tiết kiệm ngay <?= number_format($init_discount_amount, 0, ',', '.') ?> đ với thẻ VIP
                                </small>
                                <small class="text-slate font-weight-bold">Tích lũy: +<?= floor($init_vip_price / 10000) ?> điểm</small>
                            </div>

                            <!-- Nút chọn nhanh hạng thẻ để xem mức giảm trực quan -->
                            <div class="btn-group btn-group-sm btn-block" role="group">
                                <button type="button" class="btn btn-outline-secondary font-weight-bold lbn-vip-tier-btn" id="lbn_vipBtn_bronze"
                                        onclick="lbnSwitchVipTierDiscount('bronze', 0, 'Đồng')">Đồng 0%</button>
                                <button type="button" class="btn btn-outline-secondary font-weight-bold lbn-vip-tier-btn" id="lbn_vipBtn_silver"
                                        onclick="lbnSwitchVipTierDiscount('silver', 5, 'Bạc')">Bạc -5%</button>
                                <button type="button" class="btn btn-pastel-primary active font-weight-bold lbn-vip-tier-btn" id="lbn_vipBtn_gold"
                                        onclick="lbnSwitchVipTierDiscount('gold', 10, 'Vàng')">Vàng -10%</button>
                                <button type="button" class="btn btn-outline-secondary font-weight-bold lbn-vip-tier-btn" id="lbn_vipBtn_diamond"
                                        onclick="lbnSwitchVipTierDiscount('diamond', 15, 'Kim Cương')">K.Cương -15%</button>
                            </div>
                        </div>
                    </div>

                    <!-- Chọn Màu Sắc -->
                    <div class="mb-3">
                        <label class="small font-weight-bold text-slate mb-2 d-block">
                            Màu sắc trang phục:
                        </label>
                        <div class="d-flex align-items-center flex-wrap">
                            <?php foreach ($prod['colors'] as $c): ?>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill mr-2 mb-2 font-weight-bold text-slate <?= $c['active'] ? 'border-primary' : '' ?>">
                                    <span class="d-inline-block rounded-circle mr-1" style="width: 12px; height: 12px; background-color: <?= $c['code'] ?>; border: 1px solid #cbd5e0;"></span>
                                    <?= htmlspecialchars($c['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Chọn Kích Cỡ & Nút Gợi Ý Size Thước Dây (lnkt_suggest_size) -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="small font-weight-bold text-slate mb-0">
                                Kích cỡ đã chọn: <span id="lbn_selectedSizeText" class="text-peach font-weight-bold"><?= htmlspecialchars($prod['default_size']) ?></span>
                            </label>
                            <!-- NÚT MỞ MODAL THƯỚC DÂY (MH 04) -->
                            <button type="button" class="btn btn-pastel-sky btn-sm font-weight-bold shadow-sm"
                                    data-toggle="modal" data-target="#modalSuggestSize">
                                <i class="fa-solid fa-ruler-combined text-peach mr-1"></i> Thước Dây Gợi Ý Size (MH 04)
                            </button>
                        </div>

                        <!-- Danh Sách Các Nút Chọn Size -->
                        <div class="d-flex flex-wrap" id="lbn_pdpSizeOptionsGroup">
                            <?php foreach ($prod['size_range'] as $idx => $s): ?>
                                <button type="button"
                                        class="lbn-size-option-btn mr-2 mb-2 <?= $s === $prod['default_size'] ? 'active' : '' ?>"
                                        onclick="lbnSelectPdpSize('<?= htmlspecialchars($s) ?>', this)">
                                    <?= htmlspecialchars($s) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="lbn_selectedSizeInput" name="size" value="<?= htmlspecialchars($prod['default_size']) ?>">
                    </div>

                    <!-- Chọn Số Lượng & Tồn Kho (lbn_products.stock_quantity) -->
                    <div class="mb-4">
                        <label class="small font-weight-bold text-slate mb-2 d-block">
                            Số lượng đặt mua:
                        </label>
                        <div class="d-flex align-items-center">
                            <div class="input-group input-group-sm mr-3" style="width: 120px;">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-light border font-weight-bold" onclick="lbnChangePdpQty(-1)">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </div>
                                <input type="number" id="lbn_pdpQuantity" name="quantity" class="form-control text-center font-weight-bold"
                                       value="1" min="1" max="<?= $prod['stock_quantity'] ?>" data-max="<?= $prod['stock_quantity'] ?>" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-light border font-weight-bold" onclick="lbnChangePdpQty(1)">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <span class="small text-muted-custom">
                                Còn <strong class="text-slate"><?= $prod['stock_quantity'] ?></strong> sản phẩm sẵn tại kho D2C
                            </span>
                        </div>
                    </div>

                    <!-- Cụm Nút Hành Động: Thêm Vào Giỏ & Mua Ngay -->
                    <div class="form-row mb-3">
                        <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                            <button type="button" class="btn btn-pastel-outline btn-block py-3 font-weight-bold" onclick="lbnAddToCartPdp()">
                                <i class="fa-solid fa-cart-plus mr-2"></i>Thêm Vào Giỏ Hàng
                            </button>
                        </div>
                        <div class="col-12 col-sm-6">
                            <button type="button" class="btn btn-pastel-primary btn-block py-3 font-weight-bold shadow" onclick="lbnBuyNowPdp()">
                                <i class="fa-solid fa-bolt mr-2"></i>Mua Ngay Cho Bé
                            </button>
                        </div>
                    </div>

                    <!-- Chia Sẻ Mạng Xã Hội -->
                    <div class="d-flex align-items-center pt-2 border-top small text-muted-custom">
                        <span class="mr-3 font-weight-bold text-slate"><i class="fa-solid fa-share-nodes mr-1"></i>Chia sẻ:</span>
                        <a href="javascript:void(0)" onclick="lbnShareProductSocial('Facebook')" class="btn btn-sm btn-light rounded-circle mr-2 text-primary border shadow-sm">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="lbnShareProductSocial('Zalo')" class="btn btn-sm btn-light rounded-circle mr-2 text-info border shadow-sm">
                            <i class="fa-solid fa-comment-dots"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="lbnShareProductSocial('copy')" class="btn btn-sm btn-light rounded-pill px-3 border shadow-sm font-weight-bold text-slate">
                            <i class="fa-solid fa-link mr-1"></i>Sao chép link
                        </a>
                    </div>
                </div>
            </div>

            <!-- TAB CHI TIẾT THÔNG SỐ VẢI SỢI TRE & HƯỚNG DẪN GIẶT -->
            <div class="row mt-5 pt-4 border-top">
                <div class="col-12">
                    <ul class="nav nav-pills nav-pills-custom mb-3" id="pdpTabList" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-desc-link" data-toggle="pill" href="#tab-desc" role="tab">
                                <i class="fa-solid fa-circle-info mr-1"></i> Mô Tả &amp; Chất Liệu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-cert-link" data-toggle="pill" href="#tab-cert" role="tab">
                                <i class="fa-solid fa-shield-halved mr-1"></i> Chứng Nhận An Toàn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-wash-link" data-toggle="pill" href="#tab-wash" role="tab">
                                <i class="fa-solid fa-shirt mr-1"></i> Hướng Dẫn Giặt Giũ
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4 rounded bg-white border" id="pdpTabContent">
                        <div class="tab-pane fade show active" id="tab-desc" role="tabpanel">
                            <h6 class="font-weight-bold text-slate mb-2">Đặc Tính Sợi Tre Tự Nhiên Cho Làn Da Nhạy Cảm Của Bé</h6>
                            <p class="text-muted-custom small mb-3"><?= htmlspecialchars($prod['description']) ?></p>
                            <div class="p-3 rounded bg-milk-cream border small">
                                <strong>Thành phần chi tiết:</strong> <?= htmlspecialchars($prod['material']) ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-cert" role="tabpanel">
                            <h6 class="font-weight-bold text-slate mb-2">Chứng Nhận Kiểm Định Chất Lượng Quốc Tế</h6>
                            <ul class="list-unstyled small mb-0">
                                <?php foreach ($prod['certifications'] as $cert): ?>
                                    <li class="mb-2 text-slate">
                                        <i class="fa-solid fa-circle-check text-success mr-2"></i><?= htmlspecialchars($cert) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="tab-wash" role="tabpanel">
                            <h6 class="font-weight-bold text-slate mb-2">Bảo Quản Trang Phục Bền Màu &amp; Mềm Mại</h6>
                            <p class="text-muted-custom small mb-0"><?= htmlspecialchars($prod['wash_guide']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         KHỐI DƯỚI PDP: HỆ THỐNG ĐÁNH GIÁ THỰC TẾ & BÌNH LUẬN (MH 03)
         Đảm nhiệm: Trần Gia Bảo (tgb_) - Tích hợp Đánh giá sao, Lọc ảnh, Modal Review
         ====================================================================== -->
    <section class="reviews-section bg-milk-cream py-4 rounded-lg border shadow-sm mb-5" id="pdp_review_section">
        <div class="container-fluid px-3 px-md-4">
            <!-- Hàng tiêu đề phân tích đánh giá -->
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="font-weight-bold text-slate mb-1">
                            <i class="fa-solid fa-comments text-peach mr-2"></i>Đánh Giá Từ Phụ Huynh Đã Mua Hàng
                        </h4>
                        <p class="text-muted-custom small mb-0">Nhận xét thực tế &amp; đối chiếu kích cỡ từ các phụ huynh đã mua tại FashionKids</p>
                    </div>
                    <button class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm mt-2 mt-md-0" data-toggle="modal" data-target="#modalReview">
                        <i class="fa-solid fa-pen-to-square mr-2"></i>Viết Đánh Giá
                    </button>
                </div>
            </div>

            <!-- Hàng thống kê tổng quan và tỷ lệ sao -->
            <div class="row align-items-center mb-4 bg-white p-4 rounded-lg shadow-sm border">
                <div class="col-12 col-md-4 text-center border-right mb-3 mb-md-0">
                    <div class="display-4 font-weight-bold text-warning mb-1"><?= $prod['rating'] ?></div>
                    <div class="mb-2 text-warning">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <span class="text-muted-custom small">Dựa trên <?= $prod['reviews'] ?> nhận xét thực tế đã xác thực</span>
                </div>

                <div class="col-12 col-md-8 pl-md-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="small font-weight-bold mr-2 text-nowrap" style="width: 50px;">5 <i class="fa-solid fa-star text-warning"></i></span>
                        <div class="progress flex-grow-1 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 78%;" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted-custom ml-2 font-weight-bold" style="width: 35px;">96</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="small font-weight-bold mr-2 text-nowrap" style="width: 50px;">4 <i class="fa-solid fa-star text-warning"></i></span>
                        <div class="progress flex-grow-1 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 18%;" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted-custom ml-2 font-weight-bold" style="width: 35px;">22</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="small font-weight-bold mr-2 text-nowrap" style="width: 50px;">3 <i class="fa-solid fa-star text-warning"></i></span>
                        <div class="progress flex-grow-1 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 5%;" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted-custom ml-2 font-weight-bold" style="width: 35px;">6</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="small font-weight-bold mr-2 text-nowrap" style="width: 50px;">2 <i class="fa-solid fa-star text-warning"></i></span>
                        <div class="progress flex-grow-1 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 2%;" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted-custom ml-2 font-weight-bold" style="width: 35px;">3</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="small font-weight-bold mr-2 text-nowrap" style="width: 50px;">1 <i class="fa-solid fa-star text-warning"></i></span>
                        <div class="progress flex-grow-1 rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted-custom ml-2 font-weight-bold" style="width: 35px;">1</span>
                    </div>
                </div>
            </div>

            <!-- Hàng nút lọc đánh giá nhanh -->
            <div class="d-flex flex-wrap align-items-center mb-4">
                <span class="font-weight-bold small mr-3 mb-2 text-slate">Lọc đánh giá:</span>
                <button class="btn btn-pastel-primary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">Tất cả (<?= $prod['reviews'] ?>)</button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold"><i class="fa-solid fa-camera mr-1"></i> Có hình ảnh/video (42)</button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">5 Sao (96)</button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">4 Sao (22)</button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">3 Sao (6)</button>
            </div>

            <!-- Danh Sách Đánh Giá Chi Tiết -->
            <div class="row">
                <!-- Đánh giá 1 -->
                <div class="col-12 mb-3">
                    <div class="card border-0 rounded-lg shadow-sm p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-sky text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3" style="width: 44px; height: 44px;">
                                    MB
                                </div>
                                <div>
                                    <div class="font-weight-bold text-slate">
                                        Mẹ Bé Sam (m***8)
                                        <span class="badge badge-pill badge-mint ml-2">
                                            <i class="fa-solid fa-circle-check mr-1"></i>Đã mua tại FashionKids
                                        </span>
                                    </div>
                                    <div class="small">
                                        <span class="text-warning mr-2">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                        </span>
                                        <span class="text-muted-custom">Phân loại: <?= htmlspecialchars($prod['default_size']) ?> • Hồng phấn pastel</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted-custom small">06/11/2026</span>
                        </div>

                        <p class="text-slate small mb-3">
                            Vải sợi tre sờ vào mát rượi và siêu mịn màng, hoàn toàn không gây ráp hay ngứa ngáy cho bé. Lúc đầu mình đắn đo size nhưng dùng thước dây thông minh của web đo bé cao 88cm gợi ý Size 2 mặc vừa in luôn! Bé mặc tùng váy xòe đi tiệc ai cũng khen xinh xắn.
                        </p>

                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-light text-muted-custom rounded-pill mr-2 border">
                                <i class="fa-regular fa-thumbs-up mr-1"></i> Hữu ích (18)
                            </button>
                            <span class="small text-muted-custom"><i class="fa-solid fa-store mr-1 text-peach"></i>Đã phản hồi bởi Chăm sóc khách hàng FashionKids</span>
                        </div>
                    </div>
                </div>

                <!-- Đánh giá 2 -->
                <div class="col-12 mb-3">
                    <div class="card border-0 rounded-lg shadow-sm p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle badge-gold text-dark font-weight-bold d-flex align-items-center justify-content-center mr-3" style="width: 44px; height: 44px;">
                                    TB
                                </div>
                                <div>
                                    <div class="font-weight-bold text-slate">
                                        Ba Bo (t***9)
                                        <span class="badge badge-pill badge-mint ml-2">
                                            <i class="fa-solid fa-circle-check mr-1"></i>Đã mua tại FashionKids
                                        </span>
                                    </div>
                                    <div class="small">
                                        <span class="text-warning mr-2">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                        </span>
                                        <span class="text-muted-custom">Đơn hàng giao trong 2h tại TP.HCM</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted-custom small">04/11/2026</span>
                        </div>

                        <p class="text-slate small mb-3">
                            Giao hàng siêu nhanh đóng hộp pastel cứng cáp sang trọng. Đường may phẳng mượt giặt máy không bị bai nhão hay phai màu. Rất đáng đồng tiền, ba mẹ nên ủng hộ thương hiệu Việt Nam!
                        </p>

                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-light text-muted-custom rounded-pill mr-2 border">
                                <i class="fa-regular fa-thumbs-up mr-1"></i> Hữu ích (9)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================================
         SẢN PHẨM LIÊN QUAN CÙNG BỘ SƯU TẬP
         ====================================================================== -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-sparkles text-peach mr-2"></i>Bé Cũng Sẽ Thích Những Món Này
            </h4>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="small font-weight-bold text-peach">
                Xem tất cả cửa hàng <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($lbn_products as $other_id => $other_p): ?>
                <?php if ($other_id !== $product_id): ?>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card lbn-product-card h-100 shadow-sm border-0">
                            <div class="lbn-product-img-box text-center p-4 bg-milk-cream position-relative">
                                <span class="badge badge-pill <?= $other_p['gender_badge'] ?> position-absolute" style="top: 10px; left: 10px;">
                                    <?= htmlspecialchars($other_p['gender_label']) ?>
                                </span>
                                <i class="fa-solid <?= $other_p['icon'] ?> display-4 my-2 text-peach" style="font-size: 3.5rem;"></i>
                            </div>
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <small class="text-muted-custom"><?= htmlspecialchars($other_p['category_name']) ?></small>
                                    <h6 class="font-weight-bold text-slate mb-1">
                                        <a href="/FashionKids/views/pages/product-detail.php?id=<?= $other_id ?>" class="text-slate text-decoration-none">
                                            <?= htmlspecialchars($other_p['name']) ?>
                                        </a>
                                    </h6>
                                    <div class="small text-warning mb-2">
                                        <i class="fa-solid fa-star"></i> <?= $other_p['rating'] ?> <span class="text-muted-custom">(<?= $other_p['reviews'] ?>)</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="font-weight-bold text-peach h6 mb-0"><?= number_format($other_p['price'], 0, ',', '.') ?> đ</span>
                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $other_id ?>" class="btn btn-sm btn-pastel-primary rounded-pill px-3">
                                        Xem Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
require_once "../modals/modal-suggest-size.php";
require_once "../modals/modal-review.php";
require_once "../layouts/footer.php";
?>
