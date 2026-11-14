<?php
$page_title = "Trang Chủ - FashionKids Thời Trang Sợi Tre Cho Bé";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";
require_once __DIR__ . "/../../models/ProductModel.php";
require_once __DIR__ . "/../partials/editorial-carousel.php";

$rtHomeModel       = new RealtimeContentModel();
$homeArticles      = array_slice($rtHomeModel->getArticles('all', '', 'newest'), 0, 3);
$homeFeaturedEvents = $rtHomeModel->getFeaturedEvents();
$homeProductModel  = new ProductModel();
$allNormalizedProd = $homeProductModel->layTatCaSanPhamChuanHoa();
$lnkt_featured_products = array_slice($allNormalizedProd, 0, 4);
?>

<div class="container my-4 my-md-5" id="lnkt_home_section">
    <!-- 0. DẢI LỊCH SỰ KIỆN & ĐẾM NGƯỢC CHIẾN DỊCH -->
    <div class="fk-press-ticker-bar d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-4 shadow-sm">
        <div class="d-flex flex-wrap align-items-center fk-card-header-row min-w-0 py-1">
            <span class="d-inline-flex align-items-center flex-shrink-0">
                <span class="fk-live-pulse-dot mr-2 flex-shrink-0"></span>
                <span class="badge badge-pill badge-peach px-3 py-1 text-nowrap flex-shrink-0">
                    <i class="fa-solid fa-calendar-check mr-1"></i>LỊCH SỰ KIỆN NỔI BẬT
                </span>
            </span>
            <span class="small font-weight-bold text-slate min-w-0">
                Chiến dịch Casting Siêu Sao Nhí KOL 2026 &amp; Flash Sale BST Sợi Tre đang diễn ra!
            </span>
        </div>
        <div class="d-flex flex-wrap align-items-center fk-card-header-row mt-2 mt-lg-0">
            <span class="small text-muted-custom font-weight-bold text-nowrap">Kết thúc đợt 1 sau:</span>
            <span class="badge badge-pill badge-gold px-3 py-1 font-weight-bold text-nowrap flex-shrink-0" id="fk_homeLiveCountdown">
                05 Ngày : 14 Giờ : 28 Phút : 45 Giây
            </span>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-banners.php" class="small font-weight-bold text-peach text-decoration-none text-nowrap flex-shrink-0">
                    <i class="fa-solid fa-sliders mr-1"></i>Quản lý Banner
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/kol-casting.php" class="small font-weight-bold text-peach text-decoration-none text-nowrap flex-shrink-0">
                    <i class="fa-solid fa-crown mr-1"></i>Thể lệ sự kiện
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1. BANNER CAROUSEL FULL-BLEED EDITORIAL (XOAY VÒNG ĐA CHỦ ĐỀ THỜI TRANG) -->
    <?php
    $homeHeroSlides = [
        [
            'badge_cls' => 'badge-mint',
            'eyebrow'   => '<i class="fa-solid fa-leaf mr-1"></i>BST MÙA HÈ 2026 • SỢI TRE ORGANIC',
            'title'     => 'Thời Trang <span class="text-peach">Vải Sợi Tre</span> Mát Dịu &amp; Đa Phong Cách',
            'sub'       => 'Lookbook mùa hè vải sợi tre Organic chuẩn OEKO-TEX® cho bé 0–6 tuổi.',
            'image'     => '/FashionKids/images/banners/theme-home-lookbook-pastels.jpg',
            'url'       => '/FashionKids/views/pages/shop-catalog.php',
            'cta_text'  => 'Khám Phá BST »',
            'cta_icon'  => 'fa-bag-shopping',
            'extra_btn' => '<button type="button" class="btn btn-light btn-sm border rounded-pill px-3 py-1 font-weight-bold text-slate text-nowrap" data-toggle="modal" data-target="#modalSuggestSize"><i class="fa-solid fa-ruler-horizontal mr-1 text-peach"></i>Đo Size Bé</button>',
        ],
        [
            'badge_cls' => 'badge-peach',
            'eyebrow'   => '<i class="fa-solid fa-wand-magic-sparkles mr-1"></i>PHÒNG THỬ ĐỒ &amp; PHỐI SET TRONG SHOP',
            'title'     => 'Thử Đồ Xinh <span class="text-peach">Phối Set Cực Chất</span> Cùng FashionKids',
            'sub'       => 'Kết hợp bộ thun Khủng Long, áo khoác Bomber & sơ mi Linen sành điệu cho bé.',
            'image'     => '/FashionKids/images/banners/theme-shop-tryon-boutique.jpg',
            'url'       => '/FashionKids/views/pages/shop-catalog.php',
            'cta_text'  => 'Mặc Thử Ngay »',
            'cta_icon'  => 'fa-shirt',
            'cta2_text' => 'Đo Size Bé',
            'cta2_icon' => 'fa-ruler-horizontal',
            'cta2_js'   => "$('#modalSuggestSize').modal('show')",
        ],
        [
            'badge_cls' => 'badge-gold',
            'eyebrow'   => '<i class="fa-solid fa-crown mr-1"></i>TUẦN LỄ THỜI TRANG NHÍ • RUNWAY SHOW',
            'title'     => 'Sân Khấu <span class="text-peach">Trình Diễn Thời Trang</span> Siêu Sao Nhí KOL',
            'sub'       => 'Tỏa sáng trên sàn catwalk chuyên nghiệp & nhận gói tài trợ 15 triệu đồng.',
            'image'     => '/FashionKids/images/banners/theme-kol-runway-catwalk.jpg',
            'url'       => '/FashionKids/views/pages/kol-stars.php',
            'cta_text'  => 'Xem Sao Runway »',
            'cta_icon'  => 'fa-crown',
            'extra_btn' => Rbac::isAdmin() ? '<a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-light btn-sm border rounded-pill px-3 py-1 font-weight-bold text-slate text-nowrap"><i class="fa-solid fa-sliders mr-1 text-peach"></i>Quản lý Banner</a>' : '',
        ],
        [
            'badge_cls' => 'badge-sky',
            'eyebrow'   => '<i class="fa-solid fa-sparkles mr-1"></i>CHỦ ĐỀ XỨ SỞ THẦN TIÊN 2026',
            'title'     => 'Công Chúa <span class="text-peach">Xứ Sở Thần Tiên</span> &amp; Vườn Hoa Cổ Tích',
            'sub'       => 'Váy voan pastel bồng bềnh & khung cảnh cổ tích lung linh cho tiệc sinh nhật.',
            'image'     => '/FashionKids/images/banners/theme-kol-wonderland.jpg',
            'url'       => '/FashionKids/views/pages/shop-catalog.php?category=vay_cong_chua',
            'cta_text'  => 'Xem Đầm Cổ Tích »',
            'cta_icon'  => 'fa-wand-magic-sparkles',
            'cta2_text' => 'Sự Kiện Lễ Hội',
            'cta2_icon' => 'fa-calendar-star',
            'cta2_url'  => '#fk_featuredEventsSection',
        ],
        [
            'badge_cls' => 'badge-gold',
            'eyebrow'   => '<i class="fa-solid fa-video mr-1"></i>CASTING QUAY PHIM • CHỤP HÌNH • ĐÓNG PHIM',
            'title'     => 'Tuyển Diễn Viên <span class="text-peach">Phim Trường Nhí</span> &amp; Mẫu Studio',
            'sub'       => 'Trải nghiệm quay TVC, chụp Lookbook thực tế cùng đạo diễn hình ảnh & stylist.',
            'image'     => '/FashionKids/images/banners/theme-kol-film-studio.jpg',
            'url'       => '/FashionKids/views/pages/kol-casting.php',
            'cta_text'  => 'Ứng Tuyển Ngay »',
            'cta_icon'  => 'fa-clapperboard',
        ],
        [
            'badge_cls' => 'badge-peach',
            'eyebrow'   => '<i class="fa-solid fa-cake-candles mr-1"></i>SỰ KIỆN SINH NHẬT &amp; LỄ HỘI NGÔI SAO NHỎ',
            'title'     => 'Khoảnh Khắc <span class="text-peach">Sinh Nhật &amp; Nhật Ký</span> Lớn Khôn',
            'sub'       => 'Tặng hộp quà kỷ niệm, thú bông xinh & ưu đãi -15% tuần lễ sinh nhật bé.',
            'image'     => '/FashionKids/images/banners/hero-event-birthday-2026.jpg',
            'url'       => '#fk_featuredEventsSection',
            'cta_text'  => 'Khám Phá Sự Kiện »',
            'cta_icon'  => 'fa-gift',
            'cta2_text' => 'Chọn Quà Cho Bé',
            'cta2_icon' => 'fa-bag-shopping',
            'cta2_url'  => '/FashionKids/views/pages/shop-catalog.php',
        ],
    ];

    fkRenderEditorialCarousel('lnkt_homeBannerCarousel', $homeHeroSlides, [
        'wrapper_cls'   => 'lnkt-hero-carousel shadow-sm mb-5',
        'banner_cls'    => '',
        'interval'      => 4500,
        'dot_cls'       => 'fk-home-dot-btn',
        'counter_label' => 'Trang sự kiện',
    ]);
    ?>

    <!-- 2. ĐIỀU HƯỚNG NHANH DANH MỤC -->
    <div class="text-center mb-5">
        <span class="badge badge-pill badge-sky px-3 py-1 mb-2">Danh Mục Bộ Sưu Tập Chủ Đạo</span>
        <h3 class="font-weight-bold text-slate mb-2">Chọn Nhanh Phân Khu Thời Trang Cho Bé</h3>
        <p class="text-muted-custom small mb-4">
            Chạm vào từng biểu tượng tròn để lọc ngay các mẫu thiết kế sợi tre mềm mát theo giới tính và dòng sản phẩm
        </p>

        <div class="row justify-content-center">
            <div class="col-6 col-sm-4 col-md-3 mb-4">
                <a href="/FashionKids/views/pages/shop-catalog.php?gender=girl" class="lnkt-category-circle-link text-decoration-none d-block">
                    <div class="lnkt-category-circle lnkt-circle-girl mx-auto mb-3 shadow-sm"><img src="/FashionKids/images/vay-cong-chua-hoa-cuc.jpg" alt="Bé Gái" class="lnkt-cat-circle-img"></div>
                    <h5 class="font-weight-bold text-slate mb-1">Bé Gái</h5>
                    <small class="text-muted-custom d-block">Đầm công chúa, váy xòe sợi tre</small>
                    <span class="badge badge-pill badge-light border mt-2 px-3 py-1 text-peach font-weight-bold">
                        Xem BST Bé Gái &raquo;
                    </span>
                </a>
            </div>

            <div class="col-6 col-sm-4 col-md-3 mb-4">
                <a href="/FashionKids/views/pages/shop-catalog.php?gender=boy" class="lnkt-category-circle-link text-decoration-none d-block">
                    <div class="lnkt-category-circle lnkt-circle-boy mx-auto mb-3 shadow-sm"><img src="/FashionKids/images/bo-thun-khung-long.jpg" alt="Bé Trai" class="lnkt-cat-circle-img"></div>
                    <h5 class="font-weight-bold text-slate mb-1">Bé Trai</h5>
                    <small class="text-muted-custom d-block">Bộ thun làm mát, áo khoác năng động</small>
                    <span class="badge badge-pill badge-light border mt-2 px-3 py-1 text-info font-weight-bold">
                        Xem BST Bé Trai &raquo;
                    </span>
                </a>
            </div>

            <div class="col-6 col-sm-4 col-md-3 mb-4">
                <a href="/FashionKids/views/pages/shop-catalog.php?type=giay_dep" class="lnkt-category-circle-link text-decoration-none d-block">
                    <div class="lnkt-category-circle lnkt-circle-shoes mx-auto mb-3 shadow-sm"><img src="/FashionKids/images/giay-sandal-tap-di.jpg" alt="Giày Dép" class="lnkt-cat-circle-img"></div>
                    <h5 class="font-weight-bold text-slate mb-1">Giày dép</h5>
                    <small class="text-muted-custom d-block">Sandal tập đi đế cao su non chống trượt</small>
                    <span class="badge badge-pill badge-light border mt-2 px-3 py-1 text-warning font-weight-bold">
                        Xem Giày Dép &raquo;
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. DẢI TIỆN ÍCH GỢI Ý SIZE -->
    <div class="card card-pastel p-4 mb-5 shadow-sm border-0 lnkt-measure-banner">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex flex-wrap align-items-center fk-card-header-row mb-2">
                    <span class="badge badge-mint px-3 py-1 rounded-pill text-nowrap flex-shrink-0">
                        <i class="fa-solid fa-ruler-combined mr-1"></i>Công Nghệ Chọn Size Chuẩn 99%
                    </span>
                    <small class="font-weight-bold text-slate min-w-0">Trợ lý đo kích cỡ theo chiều cao &amp; bàn chân</small>
                </div>
                <h4 class="font-weight-bold text-slate mb-1">
                    Phụ Huynh Phân Vân Bé Mặc Size Nào Vừa Vặn Nhất?
                </h4>
                <p class="small text-muted-custom mb-0">
                    Chỉ cần nhập chiều cao (cm) cho quần áo hoặc chiều dài bàn chân (cm) cho giày dép, thước dây thông minh sẽ tư vấn ngay size chuẩn kèm độ co giãn của vải sợi tre.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right">
                <button type="button" class="btn btn-pastel-primary px-4 py-2 shadow-sm font-weight-bold text-nowrap"
                        data-toggle="modal" data-target="#modalSuggestSize">
                    <i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Mở Thước Dây Gợi Ý Size
                </button>
            </div>
        </div>
    </div>

    <!-- 4. SẢN PHẨM NỔI BẬT (ĐỒNG BỘ TỪ CSDL) -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end fk-card-header-row mb-4">
            <div class="min-w-0">
                <span class="badge badge-pill badge-gold px-3 py-1 mb-1">
                    <i class="fa-solid fa-fire text-danger mr-1"></i>Best Sellers Mùa Hè 2026
                </span>
                <h3 class="font-weight-bold text-slate mb-0">Sản Phẩm Vải Sợi Tre Được Yêu Thích Nhất</h3>
            </div>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-outline btn-sm font-weight-bold text-nowrap flex-shrink-0">
                Xem toàn bộ cửa hàng <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($lnkt_featured_products as $prod):
                $stockQty    = (int)$prod['stock_quantity'];
                $defaultSize = ($prod['type'] === 'giay_dep') ? 'Size 22 (13cm)' : 'Size 2 (2-3T)';
            ?>
                <div class="col-12 col-sm-6 col-xl-3 mb-4">
                    <div class="lbn-product-card h-100 d-flex flex-column shadow-sm">
                        <div class="lbn-product-img-wrap">
                            <span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">
                                <i class="fa-solid fa-leaf mr-1"></i>Sợi Tre Làm Mát
                            </span>
                            <span class="badge badge-pill <?= htmlspecialchars($prod['gender_badge']) ?> lbn-badge-gender-overlay">
                                <?= htmlspecialchars($prod['gender_label']) ?>
                            </span>
                            <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="lbn-img-link text-decoration-none">
                                <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="lbn-product-img">
                            </a>
                        </div>

                        <div class="card-body d-flex flex-column p-3 min-w-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-1">
                                <small class="text-muted-custom font-weight-bold"><?= htmlspecialchars($prod['category_name']) ?></small>
                                <?php if ((int)($prod['reviews'] ?? 0) > 0): ?>
                                    <span class="small text-warning font-weight-bold text-nowrap flex-shrink-0">
                                        <i class="fa-solid fa-star"></i> <?= number_format((float)$prod['rating'], 1) ?> (<?= (int)$prod['reviews'] ?>)
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
                                <span class="text-nowrap">Mã SP: <strong class="text-slate"><?= htmlspecialchars($prod['sku']) ?></strong></span>
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize" class="text-peach font-weight-bold text-decoration-none text-nowrap">
                                    <i class="fa-solid fa-ruler-horizontal mr-1"></i><?= htmlspecialchars($prod['size_range']) ?>
                                </a>
                            </div>
                            <div class="mb-3">
                                <span class="fk-buyer-count-pill">
                                    <i class="fa-solid fa-bag-shopping text-peach mr-1"></i><?= (int)($prod['buyer_count'] ?? 0) ?> người mua
                                </span>
                            </div>

                            <div class="mt-auto pt-2 border-top d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                                <div class="min-w-0">
                                    <span class="h6 font-weight-bold text-peach mb-0 d-block text-nowrap">
                                        <?= number_format((float)$prod['price'], 0, ',', '.') ?> đ
                                    </span>
                                    <?php if ($stockQty <= 0): ?>
                                        <small class="text-danger font-weight-bold">Hết hàng</small>
                                    <?php elseif ($stockQty <= 5): ?>
                                        <small class="text-danger font-weight-bold">Còn <?= $stockQty ?> SP</small>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center flex-shrink-0">
                                    <?php if ($stockQty > 0): ?>
                                        <button type="button"
                                                onclick="lbnQuickAddToCart('<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>', <?= (float)$prod['price'] ?>, <?= (int)$prod['id'] ?>, '<?= htmlspecialchars($defaultSize, ENT_QUOTES) ?>')"
                                                class="btn btn-light btn-sm rounded-circle border mr-1 flex-shrink-0" title="Thêm nhanh vào giỏ">
                                            <i class="fa-solid fa-cart-plus text-peach"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>"
                                       class="btn btn-pastel-primary btn-sm px-3 py-1 text-nowrap flex-shrink-0">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4B. CÁC SỰ KIỆN TIÊU BIỂU FASHIONKIDS 2026 (CLICK MỞ CHI TIẾT) -->
    <div class="mb-5" id="fk_featuredEventsSection">
        <div class="d-flex flex-wrap justify-content-between align-items-end fk-card-header-row mb-4">
            <div class="min-w-0">
                <span class="badge badge-pill badge-peach px-3 py-1 mb-1">
                    <i class="fa-solid fa-calendar-star mr-1"></i>Lễ Hội &amp; Hoạt Động Nổi Bật
                </span>
                <h3 class="font-weight-bold text-slate mb-0">Các Sự Kiện Tiêu Biểu Cho Bé Yêu</h3>
            </div>
            <a href="/FashionKids/views/pages/kol-stars.php" class="btn btn-pastel-outline btn-sm font-weight-bold text-nowrap flex-shrink-0">
                Khám phá Sao KOL Nhí <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($homeFeaturedEvents as $fEv):
                $evPerksJson = htmlspecialchars(json_encode($fEv['perk_pills'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
            ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="fk-event-poster-card shadow-sm"
                         role="button"
                         tabindex="0"
                         data-title="<?= htmlspecialchars($fEv['title'] ?? '') ?>"
                         data-badge="<?= htmlspecialchars($fEv['badge_text'] ?? 'Sự Kiện') ?>"
                         data-badge-cls="<?= htmlspecialchars($fEv['badge_class'] ?? 'badge-peach') ?>"
                         data-subtitle="<?= htmlspecialchars($fEv['subtitle'] ?? '') ?>"
                         data-date="<?= htmlspecialchars($fEv['date_label'] ?? '') ?>"
                         data-image="<?= htmlspecialchars($fEv['image'] ?? '') ?>"
                         data-url="<?= htmlspecialchars($fEv['target_url'] ?? '#') ?>"
                         data-cta="<?= htmlspecialchars($fEv['cta_label'] ?? 'Xem sự kiện') ?>"
                         data-perks="<?= $evPerksJson ?>"
                         onclick="fkOpenEventDetailModal(this)"
                         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();fkOpenEventDetailModal(this);}">
                        <img src="<?= htmlspecialchars($fEv['image'] ?? '') ?>"
                             alt="<?= htmlspecialchars($fEv['title'] ?? '') ?>"
                             class="fk-event-poster-bg"
                             onerror="this.onerror=null;this.src='/FashionKids/images/banner-kol-nhi-2026.jpg';">
                        <div class="fk-event-poster-scrim"></div>
                        <span class="badge <?= htmlspecialchars($fEv['badge_class'] ?? 'badge-peach') ?> badge-pill px-3 py-1 fk-event-poster-badge">
                            <?= htmlspecialchars($fEv['badge_text'] ?? 'Sự Kiện') ?>
                        </span>
                        <div class="fk-event-poster-footer">
                            <h5 class="fk-event-poster-title"><?= htmlspecialchars($fEv['title'] ?? '') ?></h5>
                            <span class="fk-event-poster-hint">
                                Nhấn xem thông tin sự kiện <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 5. TIÊU ĐIỂM TẠP CHÍ MẪU NHÍ & ĐIỂM TIN BÁO CHÍ -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end fk-card-header-row mb-4">
            <div class="min-w-0">
                <span class="badge badge-pill badge-mint px-3 py-1 mb-1">
                    <i class="fa-solid fa-newspaper mr-1"></i>Tạp Chí &amp; Báo Chí FashionKids
                </span>
                <h3 class="font-weight-bold text-slate mb-0">Cẩm Nang Phối Đồ &amp; Chăm Sóc Làn Da Bé</h3>
            </div>
            <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-pastel-outline btn-sm font-weight-bold text-nowrap flex-shrink-0">
                Xem toàn bộ tạp chí <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($homeArticles as $hArt): ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card fk-blog-card h-100 shadow-sm d-flex flex-column">
                        <div class="fk-blog-img-wrap">
                            <span class="badge <?= htmlspecialchars($hArt['badge_class']) ?> fk-blog-cat-overlay">
                                <?= htmlspecialchars($hArt['category_name']) ?>
                            </span>
                            <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $hArt['id'] ?>">
                                <img src="<?= htmlspecialchars($hArt['image']) ?>" alt="<?= htmlspecialchars($hArt['title']) ?>" class="blog-card-img">
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column p-3 min-w-0">
                            <small class="text-muted-custom mb-1">
                                <i class="fa-regular fa-calendar text-peach mr-1"></i><?= htmlspecialchars($hArt['published_at']) ?> • <?= htmlspecialchars($hArt['read_time']) ?>
                            </small>
                            <h6 class="font-weight-bold text-slate mb-2">
                                <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $hArt['id'] ?>" class="text-slate text-decoration-none">
                                    <?= htmlspecialchars($hArt['title']) ?>
                                </a>
                            </h6>
                            <p class="small text-muted-custom mb-3"><?= htmlspecialchars($hArt['summary']) ?></p>
                            <div class="mt-auto pt-2 border-top d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                                <button type="button"
                                        onclick="fkToggleLikeArticle(<?= $hArt['id'] ?>, this)"
                                        class="btn btn-light btn-sm border fk-btn-like-blog px-2 py-0 text-nowrap <?= !empty($hArt['is_liked']) ? 'liked' : '' ?>">
                                    <i class="fa-solid fa-heart text-danger mr-1"></i>
                                    <span class="fk-like-count-<?= $hArt['id'] ?>"><?= number_format($hArt['likes']) ?></span>
                                </button>
                                <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $hArt['id'] ?>" class="small font-weight-bold text-peach text-decoration-none text-nowrap flex-shrink-0">
                                    Đọc bài viết &raquo;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 6. TIỆN ÍCH MUA SẮM NHANH DÀNH CHO PHỤ HUYNH -->
    <div class="card card-pastel p-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-3">
            <div class="min-w-0">
                <h6 class="font-weight-bold text-slate mb-0">
                    <i class="fa-solid fa-compass text-peach mr-2"></i>Tiện Ích Trực Tuyến FashionKids
                </h6>
                <small class="text-muted-custom">Truy cập nhanh các dịch vụ chăm sóc khách hàng và tra cứu thông tin</small>
            </div>
            <span class="badge badge-pill badge-mint px-3 py-1 text-nowrap flex-shrink-0">Hỗ trợ phụ huynh 24/7</span>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3 mb-2">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                    <i class="fa-solid fa-ruler-combined text-peach mr-2 flex-shrink-0"></i><span>Thước Dây Gợi Ý Size</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-2">
                <a href="/FashionKids/views/pages/checkout.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                    <i class="fa-solid fa-basket-shopping text-peach mr-2 flex-shrink-0"></i><span>Giỏ Hàng &amp; Đặt Hàng</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-2">
                <a href="/FashionKids/views/pages/order-tracking.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                    <i class="fa-solid fa-truck-fast text-info mr-2 flex-shrink-0"></i><span>Tra Cứu Đơn Hàng</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-2">
                <a href="/FashionKids/views/pages/kol-casting.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                    <i class="fa-solid fa-crown text-warning mr-2 flex-shrink-0"></i><span>Ứng Tuyển KOL Nhí</span>
                </a>
            </div>
            <?php if (Rbac::isStaffOrAdmin()): ?>
                <div class="col-12 col-sm-6 col-lg-3 mb-2 mb-lg-0">
                    <a href="/FashionKids/views/pages/staff-orders.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                        <i class="fa-solid fa-clipboard-check text-success mr-2 flex-shrink-0"></i><span>Duyệt Đơn Hàng</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-2 mb-lg-0">
                    <a href="/FashionKids/views/pages/staff-support.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                        <i class="fa-solid fa-headset text-danger mr-2 flex-shrink-0"></i><span>CSKH Trực Tuyến</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-2 mb-sm-0">
                    <a href="/FashionKids/views/pages/staff-casting-review.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                        <i class="fa-solid fa-star text-warning mr-2 flex-shrink-0"></i><span>Sơ Tuyển KOL Nhí</span>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="/FashionKids/views/pages/admin-cms-blog.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2 fk-utility-pill">
                        <i class="fa-solid fa-newspaper text-primary mr-2 flex-shrink-0"></i><span>Biên Tập Tạp Chí</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Chi Tiết Sự Kiện Tiêu Biểu -->
<div class="modal fade" id="fk_modalEventDetail" tabindex="-1" role="dialog" aria-labelledby="fk_evModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="position-relative" style="background: #FFF9F5;">
                <img id="fk_evModalImage" src="" alt="Banner Sự Kiện" class="w-100" style="max-height: 360px; object-fit: cover; object-position: center 22%;">
                <button type="button" class="close position-absolute bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                        data-dismiss="modal" aria-label="Đóng"
                        style="top: 14px; right: 14px; width: 38px; height: 38px; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <span id="fk_evModalBadge" class="badge badge-pill badge-peach px-3 py-2 position-absolute shadow-sm" style="bottom: 14px; left: 18px;">
                    Sự Kiện
                </span>
            </div>
            <div class="modal-body p-4">
                <div class="small font-weight-bold text-peach mb-2">
                    <i class="fa-regular fa-calendar-check mr-1"></i><span id="fk_evModalDate">Đang diễn ra</span>
                </div>
                <h4 class="font-weight-bold text-slate mb-3" id="fk_evModalTitle">Tiêu đề sự kiện</h4>
                <p class="text-muted-custom mb-3" id="fk_evModalSubtitle" style="line-height: 1.65;"></p>
                <div id="fk_evModalPerks" class="d-flex flex-wrap mb-2" style="gap: 8px;"></div>
            </div>
            <div class="modal-footer bg-milk-cream border-top d-flex justify-content-between align-items-center px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-4 font-weight-bold text-slate" data-dismiss="modal">Đóng</button>
                <a id="fk_evModalActionBtn" href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold shadow-sm">
                    <span id="fk_evModalActionText">Khám phá ngay</span> <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
