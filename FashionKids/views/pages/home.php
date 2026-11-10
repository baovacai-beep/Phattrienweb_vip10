<?php
$page_title = "Trang Chủ - FashionKids Thời Trang Sợi Tre Cho Bé";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";
require_once __DIR__ . "/../../models/ProductModel.php";

$rtHomeModel       = new RealtimeContentModel();
$homeArticles      = array_slice($rtHomeModel->getArticles('all', '', 'newest'), 0, 3);
$homeProductModel  = new ProductModel();
$allNormalizedProd = $homeProductModel->layTatCaSanPhamChuanHoa();
$lnkt_featured_products = array_slice($allNormalizedProd, 0, 4);
?>

<div class="container my-4 my-md-5" id="lnkt_home_section">
    <!-- 0. DẢI LỊCH SỰ KIỆN & ĐẾM NGƯỢC CHIẾN DỊCH -->
    <div class="fk-press-ticker-bar d-flex flex-wrap justify-content-between align-items-center mb-4 shadow-sm">
        <div class="d-flex align-items-center py-1">
            <span class="fk-live-pulse-dot mr-2"></span>
            <span class="badge badge-pill badge-peach px-3 py-1 mr-2">
                <i class="fa-solid fa-calendar-check mr-1"></i>LỊCH SỰ KIỆN NỔI BẬT
            </span>
            <span class="small font-weight-bold text-slate">
                Chiến dịch Casting Siêu Sao Nhí KOL 2026 &amp; Flash Sale BST Sợi Tre đang diễn ra!
            </span>
        </div>
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <span class="small text-muted-custom font-weight-bold mr-2">Kết thúc đợt 1 sau:</span>
            <span class="badge badge-pill badge-gold px-3 py-1 font-weight-bold mr-2" id="fk_homeLiveCountdown">
                05 Ngày : 14 Giờ : 28 Phút : 45 Giây
            </span>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-banners.php" class="small font-weight-bold text-peach text-decoration-none">
                    <i class="fa-solid fa-sliders mr-1"></i>Quản lý Banner
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/kol-casting.php" class="small font-weight-bold text-peach text-decoration-none">
                    <i class="fa-solid fa-crown mr-1"></i>Thể lệ sự kiện
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1. BANNER CAROUSEL -->
    <div id="lnkt_homeBannerCarousel" class="carousel slide lnkt-hero-carousel shadow-sm mb-5" data-ride="carousel" data-interval="4000">
        <ol class="carousel-indicators">
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="1"></li>
        </ol>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="lnkt-hero-slide lnkt-slide-summer p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <div class="d-inline-flex align-items-center badge badge-mint px-3 py-2 rounded-pill mb-3 shadow-sm">
                                <i class="fa-solid fa-leaf mr-2"></i>100% Organic Bamboo • Chuẩn OEKO-TEX® Standard 100
                            </div>
                            <h1 class="font-weight-bold text-slate mb-3 lnkt-hero-title">
                                BST Mùa Hè 2026 – <span class="text-peach">Vải Sợi Tre Làm Mát</span> Chống Nóng Dành Cho Làn Da Nhạy Cảm
                            </h1>
                            <p class="text-muted-custom mb-4 h6 font-weight-normal">
                                Công nghệ dệt sợi tre tự nhiên kháng khuẩn 99.8%, hạ nhiệt độ bề mặt da bé -2°C và thấm hút mồ hôi gấp 2 lần cotton truyền thống.
                            </p>
                            <div class="d-flex flex-wrap align-items-center">
                                <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary px-4 py-2 mr-3 mb-2 shadow-sm">
                                    <i class="fa-solid fa-bag-shopping mr-2"></i>Khám Phá BST Hè 2026 &raquo;
                                </a>
                                <button type="button" class="btn btn-pastel-sky px-4 py-2 mb-2 shadow-sm"
                                        data-toggle="modal" data-target="#modalSuggestSize">
                                    <i class="fa-solid fa-ruler-horizontal mr-1 text-peach"></i>Đo Size Cho Bé
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 text-center">
                            <div class="lnkt-hero-visual-box mx-auto p-4 shadow-sm">
                                <div class="lnkt-floating-badge-top badge badge-gold px-3 py-1 rounded-pill shadow-sm">
                                    <i class="fa-solid fa-sun mr-1"></i>Chống Tia UV UPF 50+
                                </div>
                                <img src="/FashionKids/images/banner-mua-he-2026.jpg" alt="BST Mùa Hè 2026" class="lnkt-hero-banner-img my-2">
                                <h5 class="font-weight-bold text-slate mb-1">Mềm Mịn Như Vòng Tay Mẹ</h5>
                                <small class="text-muted-custom d-block">Dành cho bé từ Sơ sinh đến 6 tuổi (50cm - 125cm)</small>
                                <div class="mt-3 d-flex justify-content-center">
                                    <span class="badge badge-mint mr-2 px-2 py-1">Kháng khuẩn 99.8%</span>
                                    <span class="badge badge-sky px-2 py-1">Co giãn 4 chiều</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="lnkt-hero-slide lnkt-slide-kol p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <div class="d-inline-flex align-items-center badge badge-gold px-3 py-2 rounded-pill mb-3 shadow-sm">
                                <i class="fa-solid fa-crown mr-2"></i>Chiến Dịch Truyền Thông Đặc Biệt • Casting Đại Sứ Nhí
                            </div>
                            <h2 class="font-weight-bold text-slate mb-3 lnkt-hero-title">
                                Sự Kiện Tuyển Chọn <span class="text-peach">Siêu Sao Nhí KOL</span> FashionKids 2026
                            </h2>
                            <p class="text-muted-custom mb-4 h6 font-weight-normal">
                                Cơ hội để bé tỏa sáng trên Lookbook BST Mùa Hè 2026, nhận gói tài trợ trang phục sợi tre cả năm trị giá 15.000.000đ và thẻ VIP Kim Cương.
                            </p>
                            <div class="d-flex flex-wrap align-items-center">
                                <a href="/FashionKids/views/pages/kol-casting.php"
                                   class="btn lnkt-btn-cta-peach px-4 py-2 mr-3 mb-2 shadow">
                                    Đăng ký cho bé tham gia ngay &raquo;
                                </a>
                                <?php if (Rbac::isAdmin()): ?>
                                    <a href="/FashionKids/views/pages/admin-banners.php"
                                       class="btn btn-light border rounded-pill px-3 py-2 mb-2 small font-weight-bold text-slate">
                                        <i class="fa-solid fa-sliders mr-1 text-peach"></i>Quản lý Banner
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 text-center">
                            <div class="lnkt-hero-visual-box mx-auto p-4 shadow-sm">
                                <div class="lnkt-floating-badge-top badge badge-mint px-3 py-1 rounded-pill shadow-sm">
                                    <i class="fa-solid fa-camera-retro mr-1"></i>Casting Toàn Quốc
                                </div>
                                <img src="/FashionKids/images/banner-kol-nhi-2026.jpg" alt="Siêu Sao Nhí KOL 2026" class="lnkt-hero-banner-img my-2">
                                <h5 class="font-weight-bold text-slate mb-1">Gương Mặt Đại Sứ Nhí 2026</h5>
                                <small class="text-muted-custom d-block">Dành cho bé từ 6 tháng đến 6 tuổi tự tin trước ống kính</small>
                                <div class="mt-3 d-flex justify-content-center">
                                    <span class="badge badge-gold mr-2 px-2 py-1">Quà tặng 15 Triệu</span>
                                    <span class="badge badge-sky px-2 py-1">Chụp Lookbook Studio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a class="carousel-control-prev" href="#lnkt_homeBannerCarousel" role="button" data-slide="prev">
            <span class="lnkt-carousel-nav-btn shadow-sm" aria-hidden="true">
                <i class="fa-solid fa-chevron-left text-slate"></i>
            </span>
            <span class="sr-only">Slide trước</span>
        </a>
        <a class="carousel-control-next" href="#lnkt_homeBannerCarousel" role="button" data-slide="next">
            <span class="lnkt-carousel-nav-btn shadow-sm" aria-hidden="true">
                <i class="fa-solid fa-chevron-right text-slate"></i>
            </span>
            <span class="sr-only">Slide tiếp theo</span>
        </a>
    </div>

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
                <div class="d-flex align-items-center mb-2">
                    <span class="badge badge-mint px-3 py-1 rounded-pill mr-2">
                        <i class="fa-solid fa-ruler-combined mr-1"></i>Công Nghệ Chọn Size Chuẩn 99%
                    </span>
                    <small class="font-weight-bold text-slate">Trợ lý đo kích cỡ theo chiều cao &amp; bàn chân</small>
                </div>
                <h4 class="font-weight-bold text-slate mb-1">
                    Phụ Huynh Phân Vân Bé Mặc Size Nào Vừa Vặn Nhất?
                </h4>
                <p class="small text-muted-custom mb-0">
                    Chỉ cần nhập chiều cao (cm) cho quần áo hoặc chiều dài bàn chân (cm) cho giày dép, thước dây thông minh sẽ tư vấn ngay size chuẩn kèm độ co giãn của vải sợi tre.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right">
                <button type="button" class="btn btn-pastel-primary px-4 py-2 shadow-sm font-weight-bold"
                        data-toggle="modal" data-target="#modalSuggestSize">
                    <i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Mở Thước Dây Gợi Ý Size
                </button>
            </div>
        </div>
    </div>

    <!-- 4. SẢN PHẨM NỔI BẬT (ĐỒNG BỘ TỪ CSDL) -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge badge-pill badge-gold px-3 py-1 mb-1">
                    <i class="fa-solid fa-fire text-danger mr-1"></i>Best Sellers Mùa Hè 2026
                </span>
                <h3 class="font-weight-bold text-slate mb-0">Sản Phẩm Vải Sợi Tre Được Yêu Thích Nhất</h3>
            </div>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-outline btn-sm font-weight-bold mt-2 mt-sm-0">
                Xem toàn bộ cửa hàng <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($lnkt_featured_products as $prod):
                $stockQty    = (int)$prod['stock_quantity'];
                $defaultSize = ($prod['type'] === 'giay_dep') ? 'Size 22 (13cm)' : 'Size 2 (2-3T)';
            ?>
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
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

                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted-custom font-weight-bold"><?= htmlspecialchars($prod['category_name']) ?></small>
                                <span class="small text-warning font-weight-bold">
                                    <i class="fa-solid fa-star"></i> <?= number_format((float)$prod['rating'], 1) ?> (<?= (int)$prod['reviews'] ?>)
                                </span>
                            </div>

                            <h6 class="font-weight-bold text-slate mb-2">
                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="text-slate text-decoration-none">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>
                            </h6>

                            <div class="d-flex justify-content-between align-items-center small text-muted-custom mb-3">
                                <span>Mã SP: <strong class="text-slate"><?= htmlspecialchars($prod['sku']) ?></strong></span>
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize" class="text-peach font-weight-bold text-decoration-none">
                                    <i class="fa-solid fa-ruler-horizontal mr-1"></i><?= htmlspecialchars($prod['size_range']) ?>
                                </a>
                            </div>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h6 font-weight-bold text-peach mb-0 d-block">
                                        <?= number_format((float)$prod['price'], 0, ',', '.') ?> đ
                                    </span>
                                    <?php if ($stockQty <= 0): ?>
                                        <small class="text-danger font-weight-bold">Hết hàng</small>
                                    <?php elseif ($stockQty <= 5): ?>
                                        <small class="text-danger font-weight-bold">Còn <?= $stockQty ?> SP</small>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($stockQty > 0): ?>
                                        <button type="button"
                                                onclick="lbnQuickAddToCart('<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>', <?= (float)$prod['price'] ?>, <?= (int)$prod['id'] ?>, '<?= htmlspecialchars($defaultSize, ENT_QUOTES) ?>')"
                                                class="btn btn-light btn-sm rounded-circle border mr-1" title="Thêm nhanh vào giỏ">
                                            <i class="fa-solid fa-cart-plus text-peach"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>"
                                       class="btn btn-pastel-primary btn-sm px-3 py-1">
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

    <!-- 5. TIÊU ĐIỂM TẠP CHÍ MẪU NHÍ & ĐIỂM TIN BÁO CHÍ -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge badge-pill badge-mint px-3 py-1 mb-1">
                    <i class="fa-solid fa-newspaper mr-1"></i>Tạp Chí &amp; Báo Chí FashionKids
                </span>
                <h3 class="font-weight-bold text-slate mb-0">Cẩm Nang Phối Đồ &amp; Chăm Sóc Làn Da Bé</h3>
            </div>
            <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-pastel-outline btn-sm font-weight-bold mt-2 mt-sm-0">
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
                        <div class="card-body d-flex flex-column p-3">
                            <small class="text-muted-custom mb-1">
                                <i class="fa-regular fa-calendar text-peach mr-1"></i><?= htmlspecialchars($hArt['published_at']) ?> • <?= htmlspecialchars($hArt['read_time']) ?>
                            </small>
                            <h6 class="font-weight-bold text-slate mb-2">
                                <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $hArt['id'] ?>" class="text-slate text-decoration-none">
                                    <?= htmlspecialchars($hArt['title']) ?>
                                </a>
                            </h6>
                            <p class="small text-muted-custom mb-3"><?= htmlspecialchars($hArt['summary']) ?></p>
                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <button type="button"
                                        onclick="fkToggleLikeArticle(<?= $hArt['id'] ?>, this)"
                                        class="btn btn-light btn-sm border fk-btn-like-blog px-2 py-0 <?= !empty($hArt['is_liked']) ? 'liked' : '' ?>">
                                    <i class="fa-solid fa-heart text-danger mr-1"></i>
                                    <span class="fk-like-count-<?= $hArt['id'] ?>"><?= number_format($hArt['likes']) ?></span>
                                </button>
                                <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $hArt['id'] ?>" class="small font-weight-bold text-peach text-decoration-none">
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
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h6 class="font-weight-bold text-slate mb-0">
                    <i class="fa-solid fa-compass text-peach mr-2"></i>Tiện Ích Trực Tuyến FashionKids
                </h6>
                <small class="text-muted-custom">Truy cập nhanh các dịch vụ chăm sóc khách hàng và tra cứu thông tin</small>
            </div>
            <span class="badge badge-pill badge-mint px-3 py-1">Hỗ trợ phụ huynh 24/7</span>
        </div>
        <div class="row">
            <div class="col-6 col-md-3 mb-2">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-ruler-combined text-peach mr-1"></i> Thước Dây Gợi Ý Size
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <a href="/FashionKids/views/pages/checkout.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-basket-shopping text-peach mr-1"></i> Giỏ Hàng &amp; Đặt Hàng
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <a href="/FashionKids/views/pages/order-tracking.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-truck-fast text-info mr-1"></i> Tra Cứu Đơn Hàng
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <a href="/FashionKids/views/pages/kol-casting.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-crown text-warning mr-1"></i> Ứng Tuyển KOL Nhí
                </a>
            </div>
            <?php if (Rbac::isStaffOrAdmin()): ?>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <a href="/FashionKids/views/pages/staff-orders.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                        <i class="fa-solid fa-clipboard-check text-success mr-1"></i> Duyệt Đơn Hàng
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <a href="/FashionKids/views/pages/staff-chat.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                        <i class="fa-solid fa-headset text-danger mr-1"></i> CSKH Trực Tuyến
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/FashionKids/views/pages/staff-casting-review.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                        <i class="fa-solid fa-star text-warning mr-1"></i> Sơ Tuyển KOL Nhí
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/FashionKids/views/pages/admin-cms-blog.php"
                       class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                        <i class="fa-solid fa-newspaper text-primary mr-1"></i> Biên Tập Tạp Chí
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
