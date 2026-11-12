<?php
$page_title = "Cửa Hàng Thời Trang Sợi Tre - FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/ProductModel.php";

$catProductModel = new ProductModel();
$lbn_products    = $catProductModel->layTatCaSanPhamChuanHoa();
$catCounts       = $catProductModel->demSanPhamTheoDanhMuc();

$lbn_categories = [
    ['id' => 1, 'slug' => 'vay_cong_chua', 'name' => 'Váy công chúa', 'type' => 'quan_ao',  'icon' => 'fa-wand-magic-sparkles text-peach'],
    ['id' => 2, 'slug' => 'bo_thun',       'name' => 'Bộ thun',       'type' => 'quan_ao',  'icon' => 'fa-shirt text-info'],
    ['id' => 3, 'slug' => 'ao_khoac',      'name' => 'Áo khoác',      'type' => 'quan_ao',  'icon' => 'fa-vest-patches text-success'],
    ['id' => 4, 'slug' => 'giay_sandal',   'name' => 'Giày sandal',   'type' => 'giay_dep', 'icon' => 'fa-shoe-prints text-warning']
];

$init_cat = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'all';
if (isset($_GET['type']) && $_GET['type'] === 'giay_dep') {
    $init_cat = 'giay_sandal';
}
$init_gender  = isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : 'all';
$init_keyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>

<div class="container my-4 my-md-5" id="lbn_catalog_section">
    <!-- Breadcrumb & Header Danh Mục -->
    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-4">
        <div class="min-w-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page">Cửa hàng sản phẩm</li>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-store text-peach mr-2"></i>Cửa Hàng Thời Trang Sợi Tre Cho Bé
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge lbn-badge-bamboo badge-wrap-safe">
                <i class="fa-solid fa-leaf mr-1"></i> Chuẩn OEKO-TEX® 100 &amp; Sợi Tre Kháng Khuẩn Tự Nhiên
            </span>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI (SIDEBAR FILTER): DANH MỤC, GIỚI TÍNH, KHOẢNG GIÁ -->
        <div class="col-12 col-lg-4 col-xl-3 lbn-catalog-sidebar-col mb-4">
            <div class="lbn-filter-sidebar p-3 shadow-sm sticky-top fk-sticky-85">
                <div class="lbn-filter-header d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                    <h6 class="lbn-filter-header-title font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-sliders text-peach mr-1"></i>Bộ Lọc Đa Tiêu Chí
                    </h6>
                    <a href="javascript:void(0)" onclick="lbnResetCatalogFilters()" class="lbn-filter-reset-link font-weight-bold text-peach text-decoration-none">
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

                <!-- 1. BỘ LỌC DANH MỤC -->
                <div class="mb-4 pb-3 border-bottom">
                    <div class="lbn-filter-subheader d-flex justify-content-between align-items-center mb-2">
                        <label class="lbn-filter-group-title mb-0">Danh Mục Sản Phẩm</label>
                        <small class="text-muted-custom flex-shrink-0 font-weight-bold">SL</small>
                    </div>
                    <input type="hidden" id="lbn_selectedCategory" value="<?= $init_cat ?>">

                    <div class="lbn-cat-filter-item d-flex justify-content-between align-items-center mb-1 <?= $init_cat === 'all' ? 'active' : '' ?>"
                         data-cat="all" onclick="lbnSelectCategoryFilter('all', this)">
                        <span class="small text-nowrap"><i class="fa-solid fa-border-all mr-2 text-muted"></i>Tất cả danh mục</span>
                        <span class="badge badge-pill badge-light border flex-shrink-0" id="lbn_catCount_all"><?= (int)($catCounts['all'] ?? count($lbn_products)) ?></span>
                    </div>

                    <?php foreach ($lbn_categories as $cat): ?>
                        <?php $catCount = (int)($catCounts[$cat['slug']] ?? 0); ?>
                        <div class="lbn-cat-filter-item d-flex justify-content-between align-items-center mb-1 <?= $init_cat === $cat['slug'] ? 'active' : '' ?>"
                             data-cat="<?= $cat['slug'] ?>" onclick="lbnSelectCategoryFilter('<?= $cat['slug'] ?>', this)">
                            <span class="small text-nowrap">
                                <i class="fa-solid <?= $cat['icon'] ?> mr-2"></i><?= htmlspecialchars($cat['name']) ?>
                            </span>
                            <span class="badge badge-pill badge-light border flex-shrink-0" id="lbn_catCount_<?= $cat['slug'] ?>"><?= $catCount ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 2. BỘ LỌC GIỚI TÍNH -->
                <div class="mb-4 pb-3 border-bottom">
                    <label class="lbn-filter-group-title mb-2 d-block">Giới Tính Của Bé</label>

                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="lbn_gender_all" name="lbn_gender_filter" class="custom-control-input" value="all" <?= $init_gender === 'all' ? 'checked' : '' ?>>
                        <label class="custom-control-label lbn-filter-option-label font-weight-bold text-slate" for="lbn_gender_all">
                            Tất cả giới tính
                        </label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="lbn_gender_girl" name="lbn_gender_filter" class="custom-control-input" value="girl" <?= $init_gender === 'girl' ? 'checked' : '' ?>>
                        <label class="custom-control-label lbn-filter-option-label text-slate" for="lbn_gender_girl">
                            <i class="fa-solid fa-venus text-peach mr-1"></i> Bé gái
                        </label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="lbn_gender_boy" name="lbn_gender_filter" class="custom-control-input" value="boy" <?= $init_gender === 'boy' ? 'checked' : '' ?>>
                        <label class="custom-control-label lbn-filter-option-label text-slate" for="lbn_gender_boy">
                            <i class="fa-solid fa-mars text-info mr-1"></i> Bé trai
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="lbn_gender_unisex" name="lbn_gender_filter" class="custom-control-input" value="unisex" <?= $init_gender === 'unisex' ? 'checked' : '' ?>>
                        <label class="custom-control-label lbn-filter-option-label text-slate" for="lbn_gender_unisex">
                            <i class="fa-solid fa-venus-mars text-warning mr-1"></i> Unisex (Bé trai &amp; gái)
                        </label>
                    </div>
                </div>

                <!-- 3. KHOẢNG GIÁ -->
                <div class="mb-4 pb-3 border-bottom">
                    <div class="lbn-filter-subheader d-flex justify-content-between align-items-center mb-2">
                        <label class="lbn-filter-group-title mb-0" for="lbn_priceRangeSlider">Khoảng Giá Tối Đa</label>
                        <span id="lbn_priceRangeValue" class="badge badge-pill bg-peach text-white px-2 py-1 font-weight-bold flex-shrink-0 text-nowrap">
                            500.000 đ
                        </span>
                    </div>
                    <input type="range" class="custom-range lbn-price-slider" id="lbn_priceRangeSlider"
                           min="150000" max="500000" step="15000" value="500000">
                    <div class="d-flex justify-content-between lbn-price-scale text-muted-custom mt-1">
                        <span>150.000 đ</span>
                        <span>300.000 đ</span>
                        <span>500.000 đ</span>
                    </div>
                </div>

                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="lbn_onlyBambooCheck" checked>
                    <label class="custom-control-label lbn-filter-option-label font-weight-bold text-mint-dark" for="lbn_onlyBambooCheck">
                        100% Vải sợi tre kháng khuẩn
                    </label>
                </div>

                <button type="button" onclick="lbnApplyCatalogFilters()" class="btn btn-pastel-primary btn-block btn-sm font-weight-bold py-2 shadow-sm text-nowrap">
                    <i class="fa-solid fa-filter mr-1"></i> Áp Dụng Bộ Lọc
                </button>
            </div>
        </div>

        <!-- CỘT PHẢI: LƯỚI SẢN PHẨM -->
        <div class="col-12 col-lg-8 col-xl-9 lbn-catalog-main-col">
            <div class="card card-pastel p-3 mb-4 shadow-sm bg-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <div class="small text-slate min-w-0">
                        Hiển thị <strong id="lbn_visibleProductCount" class="text-peach"><?= count($lbn_products) ?></strong> trên tổng số <strong><?= count($lbn_products) ?></strong> sản phẩm an toàn cho bé
                    </div>
                    <div class="d-flex align-items-center flex-shrink-0">
                        <label for="lbn_sortSelect" class="small font-weight-bold text-muted-custom mb-0 mr-2 text-nowrap">Sắp xếp:</label>
                        <select id="lbn_sortSelect" class="custom-select custom-select-sm rounded-pill font-weight-bold fk-w-185">
                            <option value="newest">Mới nhất (BST 2026)</option>
                            <option value="price_asc">Giá: Thấp đến Cao</option>
                            <option value="price_desc">Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row product-grid" id="lbn_catalogGrid">
                <?php foreach ($lbn_products as $prod):
                    $stockQty = (int)$prod['stock_quantity'];
                    $defaultSize = ($prod['type'] === 'giay_dep') ? 'Size 22 (13cm)' : 'Size 2 (2-3T)';
                ?>
                    <div class="col-12 col-sm-6 col-xl-4 mb-4 lbn-catalog-col"
                         data-id="<?= $prod['id'] ?>"
                         data-category="<?= htmlspecialchars($prod['category_slug']) ?>"
                         data-gender="<?= htmlspecialchars($prod['gender']) ?>"
                         data-price="<?= $prod['price'] ?>"
                         data-name="<?= htmlspecialchars($prod['name'] . ' ' . $prod['category_name'] . ' ' . $prod['sku']) ?>"
                         data-bamboo="<?= (int)$prod['is_bamboo'] ?>">

                        <div class="lbn-product-card h-100 d-flex flex-column shadow-sm">
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

                            <div class="card-body d-flex flex-column p-3 min-w-0">
                                <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-1">
                                    <span class="small font-weight-bold text-muted-custom">
                                        <?= htmlspecialchars($prod['category_name']) ?>
                                    </span>
                                    <?php if ((int)($prod['reviews'] ?? 0) > 0): ?>
                                        <span class="small text-warning font-weight-bold text-nowrap flex-shrink-0">
                                            <i class="fa-solid fa-star"></i> <?= number_format((float)$prod['rating'], 1) ?>
                                            <span class="text-muted-custom font-weight-normal">(<?= (int)$prod['reviews'] ?>)</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-pill fk-no-review-badge text-nowrap flex-shrink-0">
                                            Chưa có đánh giá
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h6 class="font-weight-bold text-slate mb-2">
                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="text-slate text-decoration-none">
                                        <?= htmlspecialchars($prod['name']) ?>
                                    </a>
                                </h6>

                                <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row small text-muted-custom mb-2">
                                    <span class="text-nowrap"><i class="fa-solid fa-barcode mr-1"></i><?= htmlspecialchars($prod['sku']) ?></span>
                                    <span class="text-nowrap"><i class="fa-solid fa-ruler-combined mr-1"></i><?= htmlspecialchars($prod['size_range']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <span class="fk-buyer-count-pill">
                                        <i class="fa-solid fa-bag-shopping text-peach mr-1"></i><?= (int)($prod['buyer_count'] ?? 0) ?> người mua
                                    </span>
                                </div>

                                <div class="mt-auto pt-3 border-top d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                                    <div class="min-w-0">
                                        <div class="h5 font-weight-bold text-peach mb-0 text-nowrap">
                                            <?= number_format((float)$prod['price'], 0, ',', '.') ?> đ
                                        </div>
                                        <?php if ($stockQty <= 0): ?>
                                            <small class="text-danger font-weight-bold">
                                                <i class="fa-solid fa-circle-xmark mr-1"></i>Tạm hết hàng
                                            </small>
                                        <?php elseif ($stockQty <= 5): ?>
                                            <small class="text-danger font-weight-bold">
                                                <i class="fa-solid fa-fire mr-1"></i>Chỉ còn <?= $stockQty ?> SP
                                            </small>
                                        <?php else: ?>
                                            <small class="text-success font-weight-bold">
                                                <i class="fa-solid fa-check mr-1"></i>Còn hàng (<?= $stockQty ?>)
                                            </small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex align-items-center flex-shrink-0">
                                        <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>"
                                           class="btn btn-light btn-sm rounded-pill border mr-1 px-2 text-slate flex-shrink-0" title="Xem chi tiết sản phẩm">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <?php if ($stockQty > 0): ?>
                                            <button type="button"
                                                    onclick="lbnQuickAddToCart('<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>', <?= (float)$prod['price'] ?>, <?= (int)$prod['id'] ?>, '<?= htmlspecialchars($defaultSize, ENT_QUOTES) ?>')"
                                                    class="btn btn-pastel-primary btn-sm px-3 py-1 text-nowrap flex-shrink-0">
                                                <i class="fa-solid fa-cart-plus mr-1"></i> Thêm
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary btn-sm px-3 py-1 text-nowrap flex-shrink-0" disabled>
                                                Hết hàng
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

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
