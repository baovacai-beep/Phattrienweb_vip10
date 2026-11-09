<?php
$page_title = "Cửa Hàng & Bộ Lọc Đa Tiêu Chí (MH 02)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Danh mục chuẩn của Lê Bảo Ngọc (lbn_categories)
$lbn_categories = [
    ['id' => 1, 'slug' => 'vay_cong_chua', 'name' => 'Váy công chúa', 'type' => 'quan_ao',  'icon' => 'fa-wand-magic-sparkles text-peach'],
    ['id' => 2, 'slug' => 'bo_thun',       'name' => 'Bộ thun',       'type' => 'quan_ao',  'icon' => 'fa-shirt text-info'],
    ['id' => 3, 'slug' => 'ao_khoac',      'name' => 'Áo khoác',      'type' => 'quan_ao',  'icon' => 'fa-vest-patches text-success'],
    ['id' => 4, 'slug' => 'giay_sandal',   'name' => 'Giày sandal',   'type' => 'giay_dep', 'icon' => 'fa-shoe-prints text-warning']
];

// Danh sách sản phẩm mẫu chuẩn bảng lbn_products (đầy đủ 4 danh mục & 3 giới tính)
$lbn_products = [
    [
        'id'             => 101,
        'sku'            => 'FK-LBN-01',
        'image'          => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 285000,
        'stock_quantity' => 18,
        'rating'         => 4.9,
        'reviews'        => 42,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 1 - Size 5',
        'type'           => 'quan_ao',
        'icon'           => 'fa-person-dress text-peach'
    ],
    [
        'id'             => 102,
        'sku'            => 'FK-LBN-02',
        'image'          => '/FashionKids/images/bo-thun-khung-long.jpg',
        'category_id'    => 2,
        'category_slug'  => 'bo_thun',
        'category_name'  => 'Bộ thun',
        'name'           => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 225000,
        'stock_quantity' => 24,
        'rating'         => 4.8,
        'reviews'        => 36,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 1 - Size 5',
        'type'           => 'quan_ao',
        'icon'           => 'fa-shirt text-info'
    ],
    [
        'id'             => 103,
        'sku'            => 'FK-LBN-03',
        'image'          => '/FashionKids/images/giay-sandal-tap-di.jpg',
        'category_id'    => 4,
        'category_slug'  => 'giay_sandal',
        'category_name'  => 'Giày sandal',
        'name'           => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-gold',
        'price'          => 210000,
        'stock_quantity' => 14,
        'rating'         => 5.0,
        'reviews'        => 58,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 21 - Size 28',
        'type'           => 'giay_dep',
        'icon'           => 'fa-shoe-prints text-warning'
    ],
    [
        'id'             => 104,
        'sku'            => 'FK-LBN-04',
        'image'          => '/FashionKids/images/ao-khoac-cardigan.jpg',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-gold',
        'price'          => 320000,
        'stock_quantity' => 5,
        'rating'         => 4.9,
        'reviews'        => 29,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 2 - Size 5',
        'type'           => 'quan_ao',
        'icon'           => 'fa-vest-patches text-success'
    ],
    [
        'id'             => 105,
        'sku'            => 'FK-LBN-05',
        'image'          => '/FashionKids/images/vay-cong-chua-no-pastel.jpg',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Tùng Xòe Pastel Nơ Bản To Dự Tiệc',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 365000,
        'stock_quantity' => 11,
        'rating'         => 5.0,
        'reviews'        => 64,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 2 - Size 5',
        'type'           => 'quan_ao',
        'icon'           => 'fa-crown text-peach'
    ],
    [
        'id'             => 106,
        'sku'            => 'FK-LBN-06',
        'image'          => '/FashionKids/images/bo-thun-gau-nhi.jpg',
        'category_id'    => 2,
        'category_slug'  => 'bo_thun',
        'category_name'  => 'Bộ thun',
        'name'           => 'Bộ Thun Sợi Tre Cộc Tay Gấu Nhí Thoáng Khí Mùa Hè',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-gold',
        'price'          => 195000,
        'stock_quantity' => 32,
        'rating'         => 4.8,
        'reviews'        => 51,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 1 - Size 4',
        'type'           => 'quan_ao',
        'icon'           => 'fa-baby text-success'
    ],
    [
        'id'             => 107,
        'sku'            => 'FK-LBN-07',
        'image'          => '/FashionKids/images/ao-khoac-hoodie-tai-tho.jpg',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Hoodie Tai Thỏ Sợi Tre Ấm Áp Cho Bé Gái',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 340000,
        'stock_quantity' => 4,
        'rating'         => 4.9,
        'reviews'        => 19,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 2 - Size 5',
        'type'           => 'quan_ao',
        'icon'           => 'fa-shirt text-peach'
    ],
    [
        'id'             => 108,
        'sku'            => 'FK-LBN-08',
        'image'          => '/FashionKids/images/giay-sandal-be-trai.jpg',
        'category_id'    => 4,
        'category_slug'  => 'giay_sandal',
        'category_name'  => 'Giày sandal',
        'name'           => 'Giày Sandal Bé Trai Quai Dán Chống Trượt Siêu Nhẹ',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 245000,
        'stock_quantity' => 16,
        'rating'         => 4.7,
        'reviews'        => 23,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 22 - Size 30',
        'type'           => 'giay_dep',
        'icon'           => 'fa-shoe-prints text-info'
    ],
    [
        'id'             => 109,
        'sku'            => 'FK-LBN-09',
        'image'          => '/FashionKids/images/ao-khoac-bomber.jpg',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Bomber Sợi Tre Phi Hành Gia Nhí Bé Trai',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 350000,
        'stock_quantity' => 9,
        'rating'         => 4.9,
        'reviews'        => 31,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Vải Sợi Tre Kháng Khuẩn',
        'size_range'     => 'Size 2 - Size 5',
        'type'           => 'quan_ao',
        'icon'           => 'fa-user-astronaut text-primary'
    ]
];

// Hàm mô phỏng Stored Function lbn_count_products_by_category
function lbnCountByCatSlug($products, $slug) {
    if ($slug === 'all') return count($products);
    $count = 0;
    foreach ($products as $p) {
        if ($p['category_slug'] === $slug) {
            $count++;
        }
    }
    return $count;
}

// Nhận tham số lọc từ URL (nếu điều hướng từ Navbar / Footer / Trang chủ)
$init_cat = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'all';
if (isset($_GET['type']) && $_GET['type'] === 'giay_dep') {
    $init_cat = 'giay_sandal';
}
$init_gender = isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : 'all';
$init_keyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>

<div class="container my-4 my-md-5" id="lbn_catalog_section">
    <!-- Breadcrumb & Header Danh Mục -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page">Danh mục sản phẩm (MH 02)</li>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-store text-peach mr-2"></i>Cửa Hàng Thời Trang Sợi Tre Cho Bé
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge lbn-badge-bamboo">
                <i class="fa-solid fa-leaf mr-1"></i> Chuẩn OEKO-TEX® 100 &amp; Sợi Tre Kháng Khuẩn Tự Nhiên
            </span>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (SIDEBAR FILTER): DANH MỤC, GIỚI TÍNH, KHOẢNG GIÁ
             ================================================================ -->
        <div class="col-12 col-lg-3 mb-4">
            <div class="lbn-filter-sidebar p-4 shadow-sm sticky-top fk-sticky-85">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-sliders text-peach mr-2"></i>Bộ Lọc Đa Tiêu Chí
                    </h6>
                    <a href="javascript:void(0)" onclick="lbnResetCatalogFilters()" class="small font-weight-bold text-peach text-decoration-none">
                        <i class="fa-solid fa-rotate-left mr-1"></i>Đặt lại
                    </a>
                </div>

                <!-- Ô tìm kiếm nhanh tên / mã SP -->
                <div class="mb-4">
                    <label class="lbn-filter-group-title mb-2 d-block">Tìm kiếm sản phẩm</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="lbn_keywordFilter" class="form-control rounded-pill"
                               placeholder="Nhập tên đầm, bộ thun..." value="<?= $init_keyword ?>">
                    </div>
                </div>

                <!-- 1. BỘ LỌC DANH MỤC (Váy công chúa, Bộ thun, Áo khoác, Giày sandal) -->
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="lbn-filter-group-title mb-0">Danh Mục Sản Phẩm</label>
                        <small class="text-muted-custom" title="Stored Function: lbn_count_products_by_category">SL</small>
                    </div>
                    <input type="hidden" id="lbn_selectedCategory" value="<?= $init_cat ?>">

                    <div class="lbn-cat-filter-item d-flex justify-content-between align-items-center mb-1 <?= $init_cat === 'all' ? 'active' : '' ?>"
                         data-cat="all" onclick="lbnSelectCategoryFilter('all', this)">
                        <span class="small"><i class="fa-solid fa-border-all mr-2 text-muted"></i>Tất cả danh mục</span>
                        <span class="badge badge-pill badge-light border" id="lbn_catCount_all"><?= lbnCountByCatSlug($lbn_products, 'all') ?></span>
                    </div>

                    <?php foreach ($lbn_categories as $cat): ?>
                        <?php $catCount = lbnCountByCatSlug($lbn_products, $cat['slug']); ?>
                        <div class="lbn-cat-filter-item d-flex justify-content-between align-items-center mb-1 <?= $init_cat === $cat['slug'] ? 'active' : '' ?>"
                             data-cat="<?= $cat['slug'] ?>" onclick="lbnSelectCategoryFilter('<?= $cat['slug'] ?>', this)">
                            <span class="small">
                                <i class="fa-solid <?= $cat['icon'] ?> mr-2"></i><?= htmlspecialchars($cat['name']) ?>
                            </span>
                            <span class="badge badge-pill badge-light border" id="lbn_catCount_<?= $cat['slug'] ?>"><?= $catCount ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 2. BỘ LỌC GIỚI TÍNH (Bé trai, Bé gái, Unisex) -->
                <div class="mb-4 pb-3 border-bottom">
                    <label class="lbn-filter-group-title mb-2 d-block">Giới Tính Của Bé</label>

                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="lbn_gender_all" name="lbn_gender_filter" class="custom-control-input" value="all" <?= $init_gender === 'all' ? 'checked' : '' ?>>
                        <label class="custom-control-label small font-weight-bold text-slate" for="lbn_gender_all">
                            Tất cả giới tính
                        </label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="lbn_gender_girl" name="lbn_gender_filter" class="custom-control-input" value="girl" <?= $init_gender === 'girl' ? 'checked' : '' ?>>
                        <label class="custom-control-label small text-slate" for="lbn_gender_girl">
                            <i class="fa-solid fa-venus text-peach mr-1"></i> Bé gái
                        </label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="lbn_gender_boy" name="lbn_gender_filter" class="custom-control-input" value="boy" <?= $init_gender === 'boy' ? 'checked' : '' ?>>
                        <label class="custom-control-label small text-slate" for="lbn_gender_boy">
                            <i class="fa-solid fa-mars text-info mr-1"></i> Bé trai
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="lbn_gender_unisex" name="lbn_gender_filter" class="custom-control-input" value="unisex" <?= $init_gender === 'unisex' ? 'checked' : '' ?>>
                        <label class="custom-control-label small text-slate" for="lbn_gender_unisex">
                            <i class="fa-solid fa-venus-mars text-warning mr-1"></i> Unisex (Cả bé trai &amp; gái)
                        </label>
                    </div>
                </div>

                <!-- 3. THANH TRƯỢT CHỌN KHOẢNG GIÁ (PRICE RANGE SLIDER) -->
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="lbn-filter-group-title mb-0" for="lbn_priceRangeSlider">Khoảng Giá Tối Đa</label>
                        <span id="lbn_priceRangeValue" class="badge badge-pill bg-peach text-white px-2 py-1 font-weight-bold">
                            500.000 đ
                        </span>
                    </div>
                    <input type="range" class="custom-range lbn-price-slider" id="lbn_priceRangeSlider"
                           min="150000" max="500000" step="15000" value="500000">
                    <div class="d-flex justify-content-between small text-muted-custom mt-1">
                        <span>150.000 đ</span>
                        <span>300.000 đ</span>
                        <span>500.000 đ</span>
                    </div>
                </div>

                <!-- Chứng nhận chất liệu vải sợi tre kháng khuẩn -->
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="lbn_onlyBambooCheck" checked>
                    <label class="custom-control-label small font-weight-bold text-mint-dark" for="lbn_onlyBambooCheck">
                        100% Vải sợi tre kháng khuẩn
                    </label>
                </div>

                <button type="button" onclick="lbnApplyCatalogFilters()" class="btn btn-pastel-primary btn-block btn-sm font-weight-bold py-2 shadow-sm">
                    <i class="fa-solid fa-filter mr-1"></i> Áp Dụng Bộ Lọc
                </button>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (LƯỚI SẢN PHẨM): GRID 3 CỘT DESKTOP, CARD BO TRÒN 16PX
             ================================================================ -->
        <div class="col-12 col-lg-9">
            <!-- Thanh công cụ phía trên Lưới sản phẩm -->
            <div class="card card-pastel p-3 mb-4 shadow-sm bg-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div class="small text-slate mb-2 mb-sm-0">
                        Hiển thị <strong id="lbn_visibleProductCount" class="text-peach"><?= count($lbn_products) ?></strong> trên tổng số <strong><?= count($lbn_products) ?></strong> sản phẩm an toàn cho bé
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="lbn_sortSelect" class="small font-weight-bold text-muted-custom mb-0 mr-2">Sắp xếp:</label>
                        <select id="lbn_sortSelect" class="custom-select custom-select-sm rounded-pill font-weight-bold fk-w-185">
                            <option value="newest">Mới nhất (BST 2026)</option>
                            <option value="price_asc">Giá: Thấp đến Cao</option>
                            <option value="price_desc">Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Lưới sản phẩm 3 cột trên Desktop (col-lg-4) -->
            <div class="row product-grid" id="lbn_catalogGrid">
                <?php foreach ($lbn_products as $prod): ?>
                    <div class="col-12 col-sm-6 col-lg-4 mb-4 lbn-catalog-col"
                         data-id="<?= $prod['id'] ?>"
                         data-category="<?= $prod['category_slug'] ?>"
                         data-gender="<?= $prod['gender'] ?>"
                         data-price="<?= $prod['price'] ?>"
                         data-name="<?= htmlspecialchars($prod['name'] . ' ' . $prod['category_name'] . ' ' . $prod['sku']) ?>"
                         data-bamboo="<?= $prod['is_bamboo'] ?>">

                        <!-- Card sản phẩm bo tròn 16px + Hover translateY(-5px) -->
                        <div class="lbn-product-card h-100 d-flex flex-column shadow-sm">
                            <!-- Khung hình ảnh + Huy hiệu vải sợi tre kháng khuẩn -->
                            <div class="lbn-product-img-wrap">
                                <span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">
                                    <i class="fa-solid fa-leaf mr-1"></i><?= htmlspecialchars($prod['bamboo_label']) ?>
                                </span>

                                <?php if ($prod['gender'] === 'girl'): ?>
                                    <span class="badge badge-pill bg-peach text-white lbn-badge-gender-overlay">
                                        <i class="fa-solid fa-venus mr-1"></i>Bé gái
                                    </span>
                                <?php elseif ($prod['gender'] === 'boy'): ?>
                                    <span class="badge badge-pill badge-sky lbn-badge-gender-overlay">
                                        <i class="fa-solid fa-mars mr-1"></i>Bé trai
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-pill badge-gold lbn-badge-gender-overlay">
                                        <i class="fa-solid fa-venus-mars mr-1"></i>Unisex
                                    </span>
                                <?php endif; ?>

                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="lbn-img-link text-decoration-none">
                                    <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="lbn-product-img">
                                </a>
                            </div>

                            <!-- Thân Card Sản Phẩm -->
                            <div class="card-body d-flex flex-column p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small font-weight-bold text-muted-custom">
                                        <?= htmlspecialchars($prod['category_name']) ?>
                                    </span>
                                    <span class="small text-warning font-weight-bold">
                                        <i class="fa-solid fa-star"></i> <?= number_format($prod['rating'], 1) ?>
                                        <span class="text-muted-custom font-weight-normal">(<?= $prod['reviews'] ?>)</span>
                                    </span>
                                </div>

                                <h6 class="font-weight-bold text-slate mb-2">
                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="text-slate text-decoration-none">
                                        <?= htmlspecialchars($prod['name']) ?>
                                    </a>
                                </h6>

                                <div class="d-flex justify-content-between align-items-center small text-muted-custom mb-3">
                                    <span><i class="fa-solid fa-barcode mr-1"></i><?= $prod['sku'] ?></span>
                                    <span><i class="fa-solid fa-ruler-combined mr-1"></i><?= $prod['size_range'] ?></span>
                                </div>

                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="h5 font-weight-bold text-peach mb-0">
                                            <?= number_format($prod['price'], 0, ',', '.') ?> đ
                                        </div>
                                        <?php if ($prod['stock_quantity'] <= 5): ?>
                                            <small class="text-danger font-weight-bold">
                                                <i class="fa-solid fa-fire mr-1"></i>Chỉ còn <?= $prod['stock_quantity'] ?> SP
                                            </small>
                                        <?php else: ?>
                                            <small class="text-success font-weight-bold">
                                                <i class="fa-solid fa-check mr-1"></i>Còn hàng (<?= $prod['stock_quantity'] ?>)
                                            </small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex">
                                        <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>"
                                           class="btn btn-light btn-sm rounded-pill border mr-1 px-2 text-slate" title="Xem chi tiết PDP">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <button type="button"
                                                onclick="lbnQuickAddToCart('<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>', <?= $prod['price'] ?>, <?= (int)$prod['id'] ?>)"
                                                class="btn btn-pastel-primary btn-sm px-3 py-1">
                                            <i class="fa-solid fa-cart-plus mr-1"></i> Thêm
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Thông báo khi không có sản phẩm khớp bộ lọc -->
            <div id="lbn_emptyCatalogAlert" class="card card-pastel p-5 text-center shadow-sm d-none">
                <div class="mb-3">
                    <i class="fa-solid fa-box-open display-4 text-muted"></i>
                </div>
                <h5 class="font-weight-bold text-slate">Không tìm thấy sản phẩm phù hợp</h5>
                <p class="text-muted-custom small mb-3">
                    Phụ huynh vui lòng điều chỉnh lại khoảng giá hoặc chọn danh mục khác để xem thêm sản phẩm.
                </p>
                <div>
                    <button type="button" onclick="lbnResetCatalogFilters()" class="btn btn-pastel-primary btn-sm px-4">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Xem Tất Cả Sản Phẩm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($init_cat !== 'all' || $init_gender !== 'all' || $init_keyword !== ''): ?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (typeof window.lbnApplyCatalogFilters === 'function') {
            window.lbnApplyCatalogFilters();
        }
    });
</script>
<?php endif; ?>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>

