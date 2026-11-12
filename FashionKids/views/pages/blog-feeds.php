<?php
$page_title = "Tạp Chí Mẫu Nhí & Điểm Tin Báo Chí";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";

$rtModel  = new RealtimeContentModel();
$articles = $rtModel->getArticles('all', '', 'newest');
$featured = $articles[0] ?? null;
$kolSpotlight = array_slice($rtModel->getKolApplications(), 0, 4);

$magSlides = [
    [
        'eyebrow'   => '<i class="fa-solid fa-wand-magic-sparkles mr-1"></i>Lookbook Mùa Hè 2026 • Ấn Phẩm Số 11',
        'badge_cls' => 'badge-mint',
        'title'     => 'Tạp Chí <span class="text-peach">Mẫu Nhí FashionKids</span> &amp; Cẩm Nang Sợi Tre',
        'desc'      => 'Khám phá xu hướng phối đồ Bright Pastel cho bé, bí quyết bảo vệ làn da nhạy cảm từ chuyên gia nhi khoa và toàn cảnh hậu trường Lookbook Siêu Sao Nhí 2026.',
        'image'     => '/FashionKids/images/banners/mag-banner-kol-2026.jpg',
        'img_pos'   => 'center 22%',
        'thumb'     => '/FashionKids/images/banners/mag-banner-kol-2026.jpg',
        'thumb_title' => 'Lookbook Mẫu Nhí 2026',
        'thumb_sub'   => 'Xu hướng Bright Pastel',
        'photo_tag' => 'Lookbook Mùa Hè • Vải Sợi Tre 100%',
        'photo_sub' => 'Đầm công chúa nhún bèo &amp; Polo sợi tre kháng khuẩn',
        'kpis'      => ['100% Bamboo Organic', count($articles) . ' Chuyên Đề', 'OEKO-TEX® Hạng I'],
        'cta1_text' => 'Khám Phá Bộ Sưu Tập',
        'cta1_url'  => '/FashionKids/views/pages/shop-catalog.php',
        'cta1_icon' => 'fa-bag-shopping',
        'cta2_text' => 'Đọc Tiêu Điểm',
        'cta2_js'   => $featured ? 'fkOpenQuickReadModal(' . (int)$featured['id'] . ')' : '',
        'cta2_url'  => '#fk_spotlightArticleCard',
    ],
    [
        'eyebrow'   => '<i class="fa-solid fa-crown mr-1"></i>Casting KOL Nhí Toàn Quốc • Đang Mở Hồ Sơ',
        'badge_cls' => 'badge-peach',
        'title'     => 'Tìm Kiếm <span class="text-peach">Đại Sứ Mẫu Nhí</span> Tỏa Sáng Sân Khấu 2026',
        'desc'      => 'Cơ hội sải bước trên sàn diễn Vietnam Kids Fashion Week, chụp bộ ảnh Studio miễn phí và nhận gói tài trợ trang phục lên đến 15.000.000đ cho bé từ 1–6 tuổi.',
        'image'     => '/FashionKids/images/banners/banner-kol-nhi-2026.jpg',
        'img_pos'   => 'center 20%',
        'thumb'     => '/FashionKids/images/banners/banner-kol-nhi-2026.jpg',
        'thumb_title' => 'Casting KOL Nhí 2026',
        'thumb_sub'   => 'Tài trợ tới 15 Triệu',
        'photo_tag' => 'Sân Khấu Casting Studio • FashionKids',
        'photo_sub' => 'Ekip stylist &amp; nhiếp ảnh chuyên nghiệp đồng hành cùng bé',
        'kpis'      => ['120+ Mẫu Nhí', 'Chụp Studio Miễn Phí', 'Bé Từ 1–6 Tuổi'],
        'cta1_text' => 'Đăng Ký Casting Ngay',
        'cta1_url'  => '/FashionKids/views/pages/kol-casting.php',
        'cta1_icon' => 'fa-star',
        'cta2_text' => 'Gương Mặt Tiêu Biểu',
        'cta2_js'   => '',
        'cta2_url'  => '/FashionKids/views/pages/kol-stars.php',
    ],
    [
        'eyebrow'   => '<i class="fa-solid fa-leaf mr-1"></i>Chứng Nhận OEKO-TEX® Hạng I • An Toàn Tuyệt Đối',
        'badge_cls' => 'badge-sky',
        'title'     => 'Thời Trang <span class="text-peach">Sợi Tre Kháng Khuẩn</span> Dịu Mát Làn Da Bé',
        'desc'      => 'Chất liệu sợi tre tự nhiên Bamboo Kun thoáng khí gấp 3 lần cotton thông thường, hỗ trợ hạ nhiệt bề mặt da bé tới -2°C và ngăn ngừa rôm sảy suốt mùa nắng nóng.',
        'image'     => '/FashionKids/images/banners/banner-mua-he-2026.jpg',
        'img_pos'   => 'center 18%',
        'thumb'     => '/FashionKids/images/banners/banner-mua-he-2026.jpg',
        'thumb_title' => 'Công Nghệ Sợi Tre',
        'thumb_sub'   => 'Hạ nhiệt -2°C • UPF 50+',
        'photo_tag' => 'Premium Organic Bamboo Wear',
        'photo_sub' => 'Kháng khuẩn 99.8% • Êm mịn an toàn cho da nhạy cảm',
        'kpis'      => ['Hạ Nhiệt -2°C', 'Chống Tia UV UPF 50+', 'Kháng Khuẩn 99.8%'],
        'cta1_text' => 'Mua Sắm BST Sợi Tre',
        'cta1_url'  => '/FashionKids/views/pages/shop-catalog.php?tag=organic',
        'cta1_icon' => 'fa-shirt',
        'cta2_text' => 'Cẩm Nang Chăm Sóc',
        'cta2_js'   => "fkSelectBlogCategory('cham_soc_be', document.querySelector('#fk_blogCategoryFilterGroup [data-cat=cham_soc_be]'))",
        'cta2_url'  => '#fk_blogCategoryFilterGroup',
    ],
    [
        'eyebrow'   => '<i class="fa-solid fa-camera-retro mr-1"></i>Behind The Scenes • Hậu Trường Lookbook',
        'badge_cls' => 'badge-gold',
        'title'     => 'Toàn Cảnh <span class="text-peach">Hậu Trường Studio</span> &amp; Bí Quyết Tạo Dáng Nhí',
        'desc'      => 'Theo chân Đạo diễn hình ảnh và Stylist FashionKids khám phá cách tạo tâm lý thoải mái giúp các bạn nhỏ tự tin cười rạng rỡ trước ống kính máy ảnh.',
        'image'     => '/FashionKids/images/magazine/mag-studio-kol-nhi.jpg',
        'img_pos'   => 'center 35%',
        'thumb'     => '/FashionKids/images/magazine/mag-studio-kol-nhi.jpg',
        'thumb_title' => 'Hậu Trường Studio',
        'thumb_sub'   => 'Bí quyết tạo dáng nhí',
        'photo_tag' => 'Lookbook Studio Session 2026',
        'photo_sub' => 'Ánh sáng tản dịu an toàn cho mắt trẻ nhỏ',
        'kpis'      => ['Stylist Bảo Ngọc', 'Ảnh Thực Tế 100%', 'Không Gây Mệt Cho Bé'],
        'cta1_text' => 'Xem Hậu Trường',
        'cta1_url'  => '#fk_blogCategoryFilterGroup',
        'cta1_js'   => "fkSelectBlogCategory('hau_truong', document.querySelector('#fk_blogCategoryFilterGroup [data-cat=hau_truong]'))",
        'cta1_icon' => 'fa-clapperboard',
        'cta2_text' => 'Ứng Tuyển KOL Nhí',
        'cta2_js'   => '',
        'cta2_url'  => '/FashionKids/views/pages/kol-casting.php',
    ]
];

$perPage       = 6;
$totalArticles = count($articles);
$totalPages    = max(1, (int)ceil($totalArticles / $perPage));
$currentPage   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $totalPages) $currentPage = $totalPages;
$startIndex    = ($currentPage - 1) * $perPage;
$endIndex      = min($totalArticles, $startIndex + $perPage);

$catCounts = [
    'all'         => count($articles),
    'meo_phoi_do' => 0,
    'hau_truong'  => 0,
    'cham_soc_be' => 0,
    'bao_chi'     => 0
];
foreach ($articles as $a) {
    $ck = $a['category_key'] ?? 'meo_phoi_do';
    if (isset($catCounts[$ck])) {
        $catCounts[$ck]++;
    }
}
?>

<div class="container my-4 my-md-5" id="fk_blog_feeds_section">
    <!-- ====================================================================
         1. THANH ĐIỂM TIN BÁO CHÍ & SỰ KIỆN REALTIME (LIVE PRESS TICKER)
         ==================================================================== -->
    <div class="fk-press-ticker-bar d-flex flex-wrap flex-lg-nowrap justify-content-between align-items-center fk-card-header-row mb-4 shadow-sm">
        <div class="d-flex flex-wrap flex-sm-nowrap align-items-center fk-press-ticker-main py-1">
            <span class="d-inline-flex align-items-center flex-shrink-0">
                <span class="fk-live-pulse-dot mr-2 flex-shrink-0"></span>
                <span class="badge badge-pill badge-peach px-3 py-1 text-nowrap flex-shrink-0">
                    <i class="fa-solid fa-bolt mr-1"></i>ĐIỂM TIN MỚI
                </span>
            </span>
            <span id="fk_livePressTickerText" class="small font-weight-bold text-slate min-w-0">
                [Mới cập nhật] FashionKids chính thức nhận Chứng nhận Quốc tế OEKO-TEX® Standard 100 Hạng I cho dòng Vải Sợi Tre Mùa Hè 2026!
            </span>
        </div>
        <div class="d-flex flex-wrap align-items-center fk-btn-toolbar flex-shrink-0">
            <a href="/FashionKids/views/pages/kol-casting.php" class="badge badge-pill badge-mint px-3 py-2 text-decoration-none text-nowrap flex-shrink-0 shadow-sm">
                <i class="fa-solid fa-calendar-check mr-1"></i>Casting KOL Nhí: Đang mở
            </a>
            <?php if (Rbac::isStaffOrAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-light btn-sm rounded-pill border px-3 py-1 small font-weight-bold text-slate text-nowrap flex-shrink-0">
                    <i class="fa-solid fa-pen-nib text-peach mr-1"></i><?= Rbac::isAdmin() ? 'Quản lý bài viết' : 'Soạn bản nháp' ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ====================================================================
         2. EDITORIAL CAROUSEL BANNER TẠP CHÍ MẪU NHÍ & CHIẾN DỊCH (XOAY VÒNG TỰ ĐỘNG)
         ==================================================================== -->
    <div class="fk-mag-showcase-shell mb-4">
        <!-- Thanh Masthead Cố Định (Giữ nút Đã lưu & Viết bài luôn hiển thị) -->
        <div class="fk-mag-masthead d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                <span class="fk-mag-issue-pill">
                    <i class="fa-solid fa-book-open-reader text-peach"></i>
                    TẠP CHÍ MẪU NHÍ FASHIONKIDS • SỐ 11/2026
                </span>
                <span class="small font-weight-bold text-muted-custom d-none d-md-inline">
                    <i class="fa-solid fa-camera text-info mr-1"></i>Chuyên trang Lookbook, Sợi Tre &amp; KOL Nhí
                </span>
            </div>
            <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                <?php if (Rbac::isStaffOrAdmin()): ?>
                    <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-pastel-primary btn-sm px-3 py-2 shadow-sm font-weight-bold text-nowrap">
                        <i class="fa-solid fa-feather-pointed mr-1"></i><?= Rbac::isAdmin() ? 'Đăng Bài Viết Mới' : 'Tạo Bản Nháp Bài Viết' ?>
                    </a>
                <?php endif; ?>
                <button type="button" onclick="fkFilterSavedBookmarks()" id="fk_btnShowBookmarks" class="btn btn-light btn-sm border rounded-pill px-3 py-2 font-weight-bold text-slate shadow-sm text-nowrap">
                    <i class="fa-solid fa-bookmark text-warning mr-1"></i>Đã lưu (<span id="fk_savedCountBadge"><?= count($_SESSION['fk_bookmarked_articles'] ?? []) ?></span>)
                </button>
            </div>
        </div>

        <!-- Bootstrap 4 Auto-Rotating Carousel -->
        <div id="fk_magHeroCarousel" class="carousel slide" data-ride="carousel" data-interval="4600">
            <div class="carousel-inner">
                <?php foreach ($magSlides as $sIdx => $slide):
                    $slideNumStr = str_pad((string)($sIdx + 1), 2, '0', STR_PAD_LEFT) . ' / ' . str_pad((string)count($magSlides), 2, '0', STR_PAD_LEFT);
                ?>
                    <div class="carousel-item <?= $sIdx === 0 ? 'active' : '' ?>">
                        <div class="fk-mag-slide">
                            <div class="row align-items-center">
                                <!-- Cột Trái: Editorial Copy & CTA (Căn giữa trên Mobile, gọn gàng trên Desktop) -->
                                <div class="col-12 col-lg-6 order-2 order-lg-1 fk-mag-slide-copy text-center text-lg-left">
                                    <div class="mb-2">
                                        <span class="badge badge-pill <?= htmlspecialchars($slide['badge_cls']) ?> px-3 py-2 shadow-sm">
                                            <?= $slide['eyebrow'] ?>
                                        </span>
                                    </div>
                                    <h1 class="fk-mag-slide-title mb-3">
                                        <?= $slide['title'] ?>
                                    </h1>
                                    <p class="text-muted-custom mb-3" style="font-size: 0.94rem; line-height: 1.62;">
                                        <?= htmlspecialchars($slide['desc']) ?>
                                    </p>
                                    <div class="fk-mag-slide-kpis justify-content-center justify-content-lg-start mb-4">
                                        <?php foreach ($slide['kpis'] as $kpi): ?>
                                            <span class="fk-mag-kpi-chip">
                                                <i class="fa-solid fa-circle-check text-success mr-1"></i><?= htmlspecialchars($kpi) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start" style="gap: 10px;">
                                        <?php if (!empty($slide['cta1_js'])): ?>
                                            <a href="<?= htmlspecialchars($slide['cta1_url']) ?>"
                                               onclick="<?= htmlspecialchars($slide['cta1_js']) ?>;"
                                               class="btn btn-pastel-primary btn-sm px-4 py-2 font-weight-bold shadow-sm text-nowrap">
                                                <i class="fa-solid <?= htmlspecialchars($slide['cta1_icon']) ?> mr-1"></i><?= htmlspecialchars($slide['cta1_text']) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= htmlspecialchars($slide['cta1_url']) ?>"
                                               class="btn btn-pastel-primary btn-sm px-4 py-2 font-weight-bold shadow-sm text-nowrap">
                                                <i class="fa-solid <?= htmlspecialchars($slide['cta1_icon']) ?> mr-1"></i><?= htmlspecialchars($slide['cta1_text']) ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($slide['cta2_js'])): ?>
                                            <button type="button"
                                                    onclick="<?= htmlspecialchars($slide['cta2_js']) ?>"
                                                    class="btn btn-light btn-sm border rounded-pill px-3 py-2 font-weight-bold text-slate shadow-sm text-nowrap">
                                                <?= htmlspecialchars($slide['cta2_text']) ?> <i class="fa-solid fa-arrow-right ml-1 text-peach"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= htmlspecialchars($slide['cta2_url']) ?>"
                                               class="btn btn-light btn-sm border rounded-pill px-3 py-2 font-weight-bold text-slate shadow-sm text-nowrap">
                                                <?= htmlspecialchars($slide['cta2_text']) ?> <i class="fa-solid fa-arrow-right ml-1 text-peach"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Cột Phải: Khung Ảnh Lookbook 16:9 Nguyên Vẹn (Không bị che hay cắt góc) -->
                                <div class="col-12 col-lg-6 order-1 order-lg-2">
                                    <div class="fk-mag-photo-frame mx-auto">
                                        <div class="fk-mag-photo-stage">
                                            <img src="<?= htmlspecialchars($slide['image']) ?>"
                                                 alt="<?= htmlspecialchars($slide['thumb_title']) ?>"
                                                 class="fk-mag-slide-img"
                                                 style="object-position: <?= htmlspecialchars($slide['img_pos'] ?? 'center 22%') ?>;">
                                        </div>
                                        <div class="fk-mag-photo-caption">
                                            <div class="min-w-0">
                                                <div class="font-weight-bold text-slate small text-truncate">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-peach mr-1"></i><?= $slide['photo_tag'] ?>
                                                </div>
                                                <div class="text-muted-custom text-truncate" style="font-size: 0.74rem;">
                                                    <?= $slide['photo_sub'] ?>
                                                </div>
                                            </div>
                                            <span class="badge badge-pill badge-peach px-3 py-1 flex-shrink-0 text-nowrap">
                                                <?= $slideNumStr ?> • LOOKBOOK
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Nút điều hướng Trước / Sau dạng Kính Mờ -->
            <a class="fk-mag-carousel-control fk-mag-carousel-prev" href="#fk_magHeroCarousel" role="button" data-slide="prev" aria-label="Banner trước">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <a class="fk-mag-carousel-control fk-mag-carousel-next" href="#fk_magHeroCarousel" role="button" data-slide="next" aria-label="Banner tiếp theo">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <!-- Thanh Thumbnail Switcher Tương Tác 4 Chiến Dịch -->
        <div class="fk-mag-thumb-bar">
            <?php foreach ($magSlides as $tIdx => $slide): ?>
                <button type="button"
                        class="fk-mag-thumb-item <?= $tIdx === 0 ? 'active' : '' ?>"
                        data-target="#fk_magHeroCarousel"
                        data-slide-to="<?= $tIdx ?>">
                    <img src="<?= htmlspecialchars($slide['thumb']) ?>" alt="<?= htmlspecialchars($slide['thumb_title']) ?>" class="fk-mag-thumb-img">
                    <div class="min-w-0">
                        <span class="fk-mag-thumb-label"><?= htmlspecialchars($slide['thumb_title']) ?></span>
                        <span class="fk-mag-thumb-sub"><?= htmlspecialchars($slide['thumb_sub']) ?></span>
                    </div>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ====================================================================
         2B. SHOWCASE GƯƠNG MẶT KOL NHÍ TIÊU BIỂU & POSTER QUẢNG CÁO CHIẾN DỊCH
         ==================================================================== -->
    <div class="row align-items-stretch mb-5" id="fk_kolShowcaseSection">
        <!-- Cột Trái (8 cột): Gương Mặt KOL Nhí FashionKids 2026 -->
        <div class="col-12 col-lg-8 mb-4 mb-lg-0">
            <div class="fk-kol-showcase-panel p-3 p-md-4 h-100 d-flex flex-column">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap: 10px;">
                    <div>
                        <span class="badge badge-pill badge-peach px-3 py-1 mb-1">
                            <i class="fa-solid fa-crown mr-1"></i>ĐẠI SỨ NHÍ 2026
                        </span>
                        <h4 class="font-weight-bold text-slate mb-0">
                            Gương Mặt <span class="text-peach">KOL Nhí</span> Tỏa Sáng Trên Tạp Chí
                        </h4>
                    </div>
                    <a href="/FashionKids/views/pages/kol-stars.php" class="btn btn-light btn-sm rounded-pill border px-3 py-2 font-weight-bold text-slate text-nowrap">
                        Xem tất cả Sao Nhí <i class="fa-solid fa-arrow-right text-peach ml-1"></i>
                    </a>
                </div>

                <div class="row flex-grow-1">
                    <?php foreach ($kolSpotlight as $kol):
                        $stMap = [
                            'trung_tuyen'   => ['Trúng tuyển', 'badge-mint'],
                            'moi_phong_van' => ['Mời phỏng vấn', 'badge-peach'],
                            'cho_duyet'     => ['Nổi bật', 'badge-sky'],
                        ];
                        $stInfo = $stMap[$kol['status'] ?? ''] ?? ['Mẫu Nhí', 'badge-gold'];
                    ?>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <a href="/FashionKids/views/pages/kol-stars.php?kol_id=<?= (int)$kol['id'] ?>" class="text-decoration-none d-block h-100">
                                <div class="fk-kol-mini-card">
                                    <div class="fk-kol-mini-photo-wrap">
                                        <img src="<?= htmlspecialchars($kol['image']) ?>"
                                             alt="<?= htmlspecialchars($kol['baby_name']) ?>"
                                             class="fk-kol-mini-photo">
                                        <span class="badge <?= $stInfo[1] ?> fk-kol-mini-badge"><?= $stInfo[0] ?></span>
                                        <span class="fk-kol-age-chip">
                                            <?= htmlspecialchars($kol['age_label']) ?> • <?= (int)($kol['height_cm'] ?? 95) ?>cm
                                        </span>
                                    </div>
                                    <div class="p-2 p-xl-3 d-flex flex-column flex-grow-1">
                                        <div class="font-weight-bold text-slate small mb-1" style="line-height: 1.3;">
                                            <?= htmlspecialchars($kol['baby_name']) ?>
                                        </div>
                                        <p class="text-muted-custom mb-0" style="font-size: 0.74rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= htmlspecialchars($kol['talent']) ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Cột Phải (4 cột): Poster Quảng Cáo BST Sợi Tre & Voucher Độc Quyền -->
        <div class="col-12 col-lg-4">
            <div class="fk-mag-promo-poster">
                <img src="/FashionKids/images/magazine/mag-catwalk-tuan-le-thoi-trang.jpg"
                     alt="Quảng cáo BST Tuần Lễ Thời Trang Trẻ Em 2026"
                     class="fk-mag-promo-poster-img">
                <div class="fk-mag-promo-scrim"></div>
                <div class="fk-mag-promo-body">
                    <span class="badge badge-pill badge-peach px-3 py-1 mb-2">
                        <i class="fa-solid fa-fire mr-1"></i>QUẢNG CÁO CHIẾN DỊCH HÈ 2026
                    </span>
                    <h5 class="font-weight-bold text-white mb-2">
                        BST Sợi Tre Bright Pastel — Mặc Êm Như Mây
                    </h5>
                    <p class="small mb-3" style="color: rgba(255, 255, 255, 0.88); line-height: 1.45;">
                        Nhập mã độc quyền dành cho độc giả Tạp chí khi thanh toán đơn từ 200.000đ.
                    </p>
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                        <button type="button" onclick="fkCopyMagVoucher('FASHION10', this)" class="fk-mag-voucher-pill" title="Bấm để sao chép mã giảm giá">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Mã: <strong>FASHION10</strong> (-10%)</span>
                        </button>
                        <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary btn-sm rounded-pill px-3 py-2 font-weight-bold shadow-sm text-nowrap">
                            Mua ngay <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         3. BỘ LỌC CHUYÊN MỤC, TÌM KIẾM TỪ KHÓA & SẮP XẾP REALTIME
         ==================================================================== -->
    <div class="card card-pastel p-3 p-md-4 mb-4 shadow-sm border-0">
        <div class="row align-items-center">
            <!-- Nút lọc danh mục bài viết -->
            <div class="col-12 col-xl-7 mb-3 mb-xl-0">
                <div class="d-flex flex-wrap" id="fk_blogCategoryFilterGroup">
                    <button type="button"
                            class="btn fk-blog-filter-btn active mr-2 mb-2"
                            data-cat="all"
                            onclick="fkSelectBlogCategory('all', this)">
                        <i class="fa-solid fa-layer-group mr-1"></i>Tất cả tạp chí
                        <span class="badge badge-pill badge-light ml-1"><?= $catCounts['all'] ?></span>
                    </button>
                    <button type="button"
                            class="btn fk-blog-filter-btn mr-2 mb-2"
                            data-cat="meo_phoi_do"
                            onclick="fkSelectBlogCategory('meo_phoi_do', this)">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>Mẹo phối đồ
                        <span class="badge badge-pill badge-light ml-1"><?= $catCounts['meo_phoi_do'] ?></span>
                    </button>
                    <button type="button"
                            class="btn fk-blog-filter-btn mr-2 mb-2"
                            data-cat="hau_truong"
                            onclick="fkSelectBlogCategory('hau_truong', this)">
                        <i class="fa-solid fa-camera-retro mr-1"></i>Hậu trường mẫu nhí
                        <span class="badge badge-pill badge-light ml-1"><?= $catCounts['hau_truong'] ?></span>
                    </button>
                    <button type="button"
                            class="btn fk-blog-filter-btn mr-2 mb-2"
                            data-cat="cham_soc_be"
                            onclick="fkSelectBlogCategory('cham_soc_be', this)">
                        <i class="fa-solid fa-leaf mr-1"></i>Chăm sóc bé
                        <span class="badge badge-pill badge-light ml-1"><?= $catCounts['cham_soc_be'] ?></span>
                    </button>
                    <button type="button"
                            class="btn fk-blog-filter-btn mb-2"
                            data-cat="bao_chi"
                            onclick="fkSelectBlogCategory('bao_chi', this)">
                        <i class="fa-solid fa-newspaper mr-1"></i>Báo chí &amp; Sự kiện
                        <span class="badge badge-pill badge-light ml-1"><?= $catCounts['bao_chi'] ?></span>
                    </button>
                </div>
                <input type="hidden" id="fk_activeBlogCategory" value="all">
                <input type="hidden" id="fk_currentBlogPage" value="<?= $currentPage ?>">
            </div>

            <!-- Tìm kiếm & Sắp xếp -->
            <div class="col-12 col-xl-5">
                <div class="form-row align-items-center">
                    <div class="col-12 col-sm-7 mb-2 mb-sm-0">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 rounded-pill-left">
                                    <i class="fa-solid fa-magnifying-glass text-peach"></i>
                                </span>
                            </div>
                            <input type="text" id="fk_blogSearchInput" class="form-control border-left-0"
                                   placeholder="Tìm mẹo phối đồ, sợi tre, KOL nhí..."
                                   onkeyup="fkApplyBlogFilters(1)">
                        </div>
                    </div>
                    <div class="col-12 col-sm-5">
                        <select id="fk_blogSortSelect" class="custom-select custom-select-sm rounded-pill font-weight-bold text-slate"
                                onchange="fkApplyBlogFilters(1)">
                            <option value="newest">Sắp xếp: Mới nhất</option>
                            <option value="views">Đọc nhiều nhất</option>
                            <option value="likes">Yêu thích nhất (♥)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông báo số lượng bài viết hiển thị & Trang hiện tại -->
    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-3 px-1">
        <small class="text-muted-custom font-weight-bold min-w-0">
            <i class="fa-solid fa-book-open text-peach mr-1"></i>
            Đang hiển thị <strong id="fk_blogPageRangeText" class="text-slate"><?= ($totalArticles > 0 ? ($startIndex + 1) : 0) ?>–<?= $endIndex ?></strong>
            trên tổng số <strong id="fk_blogVisibleCount" class="text-peach"><?= $totalArticles ?></strong> chuyên đề tạp chí
            (<span id="fk_blogPageIndicator" class="text-info">Trang <?= $currentPage ?> / <?= $totalPages ?> • 6 bài/trang</span>)
        </small>
        <button type="button" onclick="fkResetBlogFilters()" class="btn btn-link btn-sm p-0 text-peach font-weight-bold text-decoration-none text-nowrap flex-shrink-0">
            <i class="fa-solid fa-rotate-left mr-1"></i>Xem tất cả chuyên mục
        </button>
    </div>

    <!-- ====================================================================
         4. LƯỚI BÀI VIẾT TẠP CHÍ 6 BÀI / TRANG (3 CỘT X 2 HÀNG CHUẨN BRIGHT PASTEL)
         ==================================================================== -->
    <div class="row" id="fk_blogFeedGrid">
        <?php foreach ($articles as $idx => $art):
            $isOnCurrentPage = ($idx >= $startIndex && $idx < $endIndex);
        ?>
            <div class="col-12 col-md-6 col-lg-4 mb-4 fk-blog-col <?= $isOnCurrentPage ? '' : 'd-none' ?>"
                 data-id="<?= $art['id'] ?>"
                 data-category="<?= htmlspecialchars($art['category_key']) ?>"
                 data-views="<?= $art['views'] ?>"
                 data-likes="<?= $art['likes'] ?>"
                 data-bookmarked="<?= !empty($art['is_bookmarked']) ? '1' : '0' ?>"
                 data-search="<?= htmlspecialchars(mb_strtolower($art['title'] . ' ' . $art['summary'] . ' ' . $art['category_name'] . ' ' . implode(' ', $art['tags']), 'UTF-8')) ?>">
                <div class="card fk-blog-card h-100 shadow-sm d-flex flex-column">
                    <!-- Ảnh bìa tạp chí chuyên biệt (/FashionKids/images/magazine/) -->
                    <div class="fk-blog-img-wrap">
                        <span class="badge <?= htmlspecialchars($art['badge_class']) ?> fk-blog-cat-overlay">
                            <?= htmlspecialchars($art['category_name']) ?>
                        </span>
                        <span class="fk-blog-readtime-overlay shadow-sm">
                            <i class="fa-regular fa-clock text-peach mr-1"></i><?= htmlspecialchars($art['read_time']) ?>
                        </span>
                        <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $art['id'] ?>">
                            <img src="<?= htmlspecialchars($art['image']) ?>" class="blog-card-img" alt="<?= htmlspecialchars($art['title']) ?>">
                        </a>
                    </div>

                    <div class="card-body d-flex flex-column p-3 p-xl-4 min-w-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-2 small text-muted-custom">
                            <span class="text-nowrap"><i class="fa-regular fa-calendar mr-1 text-peach"></i><?= htmlspecialchars($art['published_at']) ?></span>
                            <span class="text-nowrap"><i class="fa-solid fa-user-check mr-1 text-info"></i><?= htmlspecialchars($art['author']) ?></span>
                        </div>

                        <h5 class="font-weight-bold text-slate mb-2">
                            <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $art['id'] ?>" class="text-slate text-decoration-none">
                                <?= htmlspecialchars($art['title']) ?>
                            </a>
                        </h5>

                        <p class="text-muted-custom small mb-3">
                            <?= htmlspecialchars($art['summary']) ?>
                        </p>

                        <!-- Tags -->
                        <div class="mb-3">
                            <?php foreach (array_slice($art['tags'], 0, 3) as $tg): ?>
                                <span class="badge badge-pill badge-light border text-muted-custom mr-1 mb-1"><?= htmlspecialchars($tg) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Cụm nút tương tác Realtime ở chân Card -->
                        <div class="mt-auto pt-3 border-top d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                            <div class="d-flex align-items-center flex-shrink-0">
                                <button type="button"
                                        onclick="fkToggleLikeArticle(<?= $art['id'] ?>, this)"
                                        class="btn btn-light btn-sm border fk-btn-like-blog mr-1 px-2 py-1 text-nowrap <?= !empty($art['is_liked']) ? 'liked' : '' ?>"
                                        data-article-id="<?= $art['id'] ?>"
                                        title="Thả tim bài viết">
                                    <i class="fa-solid fa-heart text-danger mr-1"></i>
                                    <span class="fk-like-count-<?= $art['id'] ?>"><?= number_format($art['likes']) ?></span>
                                </button>
                                <button type="button"
                                        onclick="fkToggleBookmarkArticle(<?= $art['id'] ?>, this)"
                                        class="btn btn-light btn-sm border fk-btn-bookmark-blog mr-1 px-2 py-1 <?= !empty($art['is_bookmarked']) ? 'bookmarked' : '' ?>"
                                        data-article-id="<?= $art['id'] ?>"
                                        title="Lưu bài viết đọc sau">
                                    <i class="fa-solid fa-bookmark text-warning"></i>
                                </button>
                                <button type="button"
                                        onclick="fkShareBlogLink(<?= $art['id'] ?>, '<?= htmlspecialchars($art['title'], ENT_QUOTES) ?>')"
                                        class="btn btn-light btn-sm border rounded-pill px-2 py-1 text-slate"
                                        title="Sao chép liên kết chia sẻ">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </button>
                            </div>

                            <div class="d-flex align-items-center flex-shrink-0">
                                <button type="button"
                                        onclick="fkOpenQuickReadModal(<?= $art['id'] ?>)"
                                        class="btn btn-light btn-sm rounded-pill border px-2 py-1 mr-1 text-slate small font-weight-bold flex-shrink-0"
                                        title="Đọc nhanh tại chỗ">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $art['id'] ?>"
                                   class="btn btn-pastel-primary btn-sm px-3 py-1 font-weight-bold text-nowrap flex-shrink-0">
                                    Đọc tiếp <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dữ liệu ẩn phục vụ Modal Đọc Nhanh -->
                <div class="d-none" id="fk_quickReadData_<?= $art['id'] ?>"
                     data-title="<?= htmlspecialchars($art['title']) ?>"
                     data-cat="<?= htmlspecialchars($art['category_name']) ?>"
                     data-author="<?= htmlspecialchars($art['author'] . ' • ' . $art['published_at']) ?>"
                     data-img="<?= htmlspecialchars($art['image']) ?>"
                     data-url="/FashionKids/views/pages/blog-detail.php?id=<?= $art['id'] ?>">
                    <?= $art['content'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ====================================================================
         5. THANH PHÂN TRANG TẠP CHÍ (6 BÀI / TRANG -> TRANG 1, 2, 3...)
         ==================================================================== -->
    <div id="fk_blogPaginationWrapper" class="card card-pastel p-3 my-3 shadow-sm border-0 <?= $totalPages <= 1 ? 'd-none' : '' ?>">
        <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
            <div class="small font-weight-bold text-slate min-w-0">
                <i class="fa-solid fa-layer-group text-peach mr-1"></i>
                Phân trang Tạp chí: <span id="fk_blogPaginationSummaryText">Trang <?= $currentPage ?> / <?= $totalPages ?> (Hiển thị 6 bài viết mỗi trang)</span>
            </div>
            <nav aria-label="Phân trang tạp chí FashionKids">
                <ul class="pagination pagination-sm mb-0" id="fk_blogPaginationList">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <button type="button" class="page-link fk-blog-page-btn" onclick="fkGoToBlogPage(<?= max(1, $currentPage - 1) ?>)">
                            <i class="fa-solid fa-chevron-left mr-1"></i>Trước
                        </button>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                            <button type="button" class="page-link fk-blog-page-btn" onclick="fkGoToBlogPage(<?= $p ?>)">
                                Trang <?= $p ?>
                            </button>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <button type="button" class="page-link fk-blog-page-btn" onclick="fkGoToBlogPage(<?= min($totalPages, $currentPage + 1) ?>)">
                            Tiếp<i class="fa-solid fa-chevron-right ml-1"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Cảnh báo khi không tìm thấy bài viết phù hợp -->
    <div id="fk_emptyBlogAlert" class="card card-pastel p-5 text-center d-none my-3">
        <i class="fa-solid fa-book-open text-peach h1 mb-3"></i>
        <h5 class="font-weight-bold text-slate">Không tìm thấy bài viết phù hợp</h5>
        <p class="text-muted-custom small mb-3">Ba mẹ hãy thử từ khóa khác hoặc bấm xem tất cả các chuyên mục tạp chí nhé.</p>
        <div>
            <button type="button" onclick="fkResetBlogFilters()" class="btn btn-pastel-primary px-4 py-2 font-weight-bold">
                Hiển thị toàn bộ bài viết
            </button>
        </div>
    </div>

    <!-- ====================================================================
         6. CHUYÊN ĐỀ TIÊU ĐIỂM TUẦN NÀY (SPOTLIGHT ARTICLE HERO CARD)
         ==================================================================== -->
    <?php if ($featured): ?>
        <div class="fk-blog-spotlight-card shadow-sm mt-5" id="fk_spotlightArticleCard">
            <div class="row no-gutters align-items-stretch">
                <div class="col-12 col-lg-6">
                    <div class="fk-blog-spotlight-img-wrap">
                        <span class="badge badge-pill badge-peach fk-blog-cat-overlay">
                            <i class="fa-solid fa-fire mr-1"></i>Tiêu Điểm Tuần Này • <?= htmlspecialchars($featured['category_name']) ?>
                        </span>
                        <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $featured['id'] ?>">
                            <img src="<?= htmlspecialchars($featured['image']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>" class="fk-blog-spotlight-img">
                        </a>
                    </div>
                </div>
                <div class="col-12 col-lg-6 d-flex flex-column justify-content-center p-4 p-md-5">
                    <div class="d-flex flex-wrap align-items-center small text-muted-custom mb-2">
                        <span class="mr-3"><i class="fa-regular fa-calendar-check text-peach mr-1"></i><?= htmlspecialchars($featured['published_at']) ?></span>
                        <span class="mr-3"><i class="fa-regular fa-clock text-info mr-1"></i><?= htmlspecialchars($featured['read_time']) ?></span>
                        <span><i class="fa-solid fa-user-pen text-success mr-1"></i><?= htmlspecialchars($featured['author']) ?></span>
                    </div>
                    <h3 class="font-weight-bold text-slate mb-3">
                        <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $featured['id'] ?>" class="text-slate text-decoration-none">
                            <?= htmlspecialchars($featured['title']) ?>
                        </a>
                    </h3>
                    <p class="text-muted-custom mb-3">
                        <?= htmlspecialchars($featured['summary']) ?>
                    </p>
                    <div class="mb-4">
                        <?php foreach ($featured['tags'] as $tag): ?>
                            <span class="badge badge-pill badge-light border text-muted-custom mr-1 mb-1 px-2 py-1"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row pt-3 border-top mt-auto">
                        <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                            <button type="button"
                                    onclick="fkToggleLikeArticle(<?= $featured['id'] ?>, this)"
                                    class="btn btn-sm btn-light border fk-btn-like-blog px-3 py-1 text-nowrap <?= !empty($featured['is_liked']) ? 'liked' : '' ?>"
                                    data-article-id="<?= $featured['id'] ?>">
                                <i class="fa-solid fa-heart text-danger mr-1"></i>
                                <span class="fk-like-count-<?= $featured['id'] ?>"><?= number_format($featured['likes']) ?></span> Yêu thích
                            </button>
                            <span class="small text-muted-custom font-weight-bold text-nowrap">
                                <i class="fa-regular fa-eye mr-1"></i><?= number_format($featured['views']) ?> lượt đọc
                            </span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                            <button type="button"
                                    onclick="fkOpenQuickReadModal(<?= $featured['id'] ?>)"
                                    class="btn btn-light btn-sm rounded-pill border px-3 py-2 font-weight-bold text-slate text-nowrap">
                                <i class="fa-solid fa-expand text-peach mr-1"></i>Đọc nhanh
                            </button>
                            <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $featured['id'] ?>"
                               class="btn btn-pastel-primary btn-sm px-4 py-2 font-weight-bold shadow-sm text-nowrap">
                                Đọc chi tiết <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================================================
     MODAL ĐỌC NHANH BÀI VIẾT TẠP CHÍ (QUICK READ MODAL)
     ======================================================================== -->
<div class="modal fade" id="fk_modalQuickReadBlog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content card-pastel border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-3">
                <div>
                    <span id="fk_qrCategoryBadge" class="badge badge-pill badge-peach px-3 py-1 mb-1">Mẹo phối đồ</span>
                    <h5 class="modal-title font-weight-bold text-slate" id="fk_qrTitle">Tiêu đề bài viết</h5>
                    <small class="text-muted-custom" id="fk_qrMeta">Tác giả • Ngày đăng</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <img id="fk_qrImage" src="/FashionKids/images/vay-cong-chua-no-pastel.jpg" alt="Preview" class="blog-header-banner mb-4 shadow-sm">
                <div id="fk_qrBodyContent" class="text-slate"></div>
            </div>
            <div class="modal-footer bg-white border-top d-flex justify-content-between">
                <button type="button" class="btn btn-light border rounded-pill px-4 font-weight-bold" data-dismiss="modal">
                    Đóng cửa sổ
                </button>
                <a href="/FashionKids/views/pages/blog-detail.php?id=1" id="fk_qrFullLink" class="btn btn-pastel-primary px-4 font-weight-bold">
                    <i class="fa-solid fa-comments mr-1"></i>Mở trang chi tiết &amp; Bình luận
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
