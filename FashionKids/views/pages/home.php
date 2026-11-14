<?php
$page_title = "Trang Chủ - FashionKids Thời Trang Sợi Tre Cho Bé";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";
require_once __DIR__ . "/../../models/ProductModel.php";

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

    <!-- 1. BANNER CAROUSEL -->
    <div id="lnkt_homeBannerCarousel" class="carousel slide lnkt-hero-carousel shadow-sm mb-5" data-ride="carousel" data-interval="4000">
        <ol class="carousel-indicators">
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="1"></li>
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="2"></li>
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="3"></li>
        </ol>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="lnkt-hero-slide lnkt-slide-summer p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <div class="badge badge-mint badge-wrap-safe px-3 py-2 rounded-pill mb-3 shadow-sm">
                                <i class="fa-solid fa-leaf mr-2"></i>100% Organic Bamboo • Chuẩn OEKO-TEX® Standard 100
                            </div>
                            <h1 class="font-weight-bold text-slate mb-3 lnkt-hero-title">
                                BST Mùa Hè 2026 – <span class="text-peach">Vải Sợi Tre Làm Mát</span> Chống Nóng Dành Cho Làn Da Nhạy Cảm
                            </h1>
                            <p class="text-muted-custom mb-4 h6 font-weight-normal">
                                Công nghệ dệt sợi tre tự nhiên kháng khuẩn 99.8%, hạ nhiệt độ bề mặt da bé -2°C và thấm hút mồ hôi gấp 2 lần cotton truyền thống.
                            </p>
                            <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                                <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary px-4 py-2 shadow-sm text-nowrap">
                                    <i class="fa-solid fa-bag-shopping mr-2"></i>Khám Phá BST Hè 2026 &raquo;
                                </a>
                                <button type="button" class="btn btn-pastel-sky px-4 py-2 shadow-sm text-nowrap"
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
                                <div class="mt-3 d-flex flex-wrap justify-content-center fk-btn-toolbar">
                                    <span class="badge badge-mint px-2 py-1 text-nowrap">Kháng khuẩn 99.8%</span>
                                    <span class="badge badge-sky px-2 py-1 text-nowrap">Co giãn 4 chiều</span>
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
                            <div class="badge badge-gold badge-wrap-safe px-3 py-2 rounded-pill mb-3 shadow-sm">
                                <i class="fa-solid fa-crown mr-2"></i>Chiến Dịch Truyền Thông Đặc Biệt • Casting Đại Sứ Nhí
                            </div>
                            <h2 class="font-weight-bold text-slate mb-3 lnkt-hero-title">
                                Sự Kiện Tuyển Chọn <span class="text-peach">Siêu Sao Nhí KOL</span> FashionKids 2026
                            </h2>
                            <p class="text-muted-custom mb-4 h6 font-weight-normal">
                                Cơ hội để bé tỏa sáng trên Lookbook BST Mùa Hè 2026, nhận gói tài trợ trang phục sợi tre cả năm trị giá 15.000.000đ và thẻ VIP Kim Cương.
                            </p>
                            <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                                <a href="/FashionKids/views/pages/kol-casting.php"
                                   class="btn lnkt-btn-cta-peach px-4 py-2 shadow text-nowrap">
                                    Đăng ký cho bé tham gia ngay &raquo;
                                </a>
                                <?php if (Rbac::isAdmin()): ?>
                                    <a href="/FashionKids/views/pages/admin-banners.php"
                                       class="btn btn-light border rounded-pill px-3 py-2 small font-weight-bold text-slate text-nowrap">
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
                                <div class="mt-3 d-flex flex-wrap justify-content-center fk-btn-toolbar">
                                    <span class="badge badge-gold px-2 py-1 text-nowrap">Quà tặng 15 Triệu</span>
                                    <span class="badge badge-sky px-2 py-1 text-nowrap">Chụp Lookbook Studio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Sự Kiện Sinh Nhật & Nhật Ký Bé Yêu -->
            <div class="carousel-item">
                <div class="lnkt-hero-slide lnkt-slide-birthday p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <span class="badge badge-peach px-3 py-2 rounded-pill mb-3 shadow-sm badge-wrap-safe">
                                <i class="fa-solid fa-cake-candles mr-1"></i>Sự Kiện Sinh Nhật &amp; Nhật Ký Bé Yêu
                            </span>
                            <h1 class="display-4 font-weight-bold text-slate mb-3 lnkt-hero-title">
                                Khoảnh Khắc <span class="text-peach">Sinh Nhật &amp; Nhật Ký</span> Lớn Khôn Của Bé
                            </h1>
                            <p class="lead text-muted-custom mb-4 lnkt-hero-subtitle">
                                Lưu giữ từng dấu mốc đáng yêu từ 0–6 tuổi cùng đồ tiệc &amp; đồ mặc nhà sợi tre Organic.
                                Tặng ngay voucher sinh nhật -15% và hộp quà kỷ niệm cho đơn từ 350.000đ.
                            </p>
                            <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                                <a href="/FashionKids/views/pages/shop-catalog.php"
                                   class="btn btn-pastel-primary px-4 py-2 font-weight-bold shadow-sm text-nowrap">
                                    <i class="fa-solid fa-gift mr-2"></i>Chọn Quà Sinh Nhật Cho Bé &raquo;
                                </a>
                                <a href="#fk_featuredEventsSection"
                                   class="btn btn-light border rounded-pill px-3 py-2 small font-weight-bold text-slate text-nowrap">
                                    <i class="fa-solid fa-calendar-star mr-1 text-peach"></i>Xem Sự Kiện Tiêu Biểu
                                </a>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 text-center">
                            <div class="lnkt-hero-visual-box mx-auto p-4 shadow-sm">
                                <div class="lnkt-floating-badge-top badge badge-peach px-3 py-1 rounded-pill shadow-sm">
                                    <i class="fa-solid fa-heart mr-1"></i>Nhật Ký Bé Yêu
                                </div>
                                <img src="/FashionKids/images/banners/banner-sinh-nhat-nhat-ky-be-yeu.webp" alt="Khoảnh Khắc Sinh Nhật & Nhật Ký Bé Yêu" class="lnkt-hero-banner-img lnkt-hero-banner-img-poster my-2">
                                <h5 class="font-weight-bold text-slate mb-1">Khoảnh Khắc Sinh Nhật Bé</h5>
                                <small class="text-muted-custom d-block">Kỷ niệm hành trình lớn khôn từ những ngày đầu tiên</small>
                                <div class="mt-3 d-flex flex-wrap justify-content-center fk-btn-toolbar">
                                    <span class="badge badge-peach px-2 py-1 text-nowrap">Giảm 15% Tuần Lễ SN</span>
                                    <span class="badge badge-gold px-2 py-1 text-nowrap">Tặng Hộp Quà &amp; Thiệp</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: Lễ Hội Ngôi Sao Nhỏ & Quà Tặng Thú Bông -->
            <div class="carousel-item">
                <div class="lnkt-hero-slide lnkt-slide-littlestar p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <span class="badge badge-mint px-3 py-2 rounded-pill mb-3 shadow-sm badge-wrap-safe">
                                <i class="fa-solid fa-star mr-1"></i>Lễ Hội Ngôi Sao Nhỏ • Quà Tặng Độc Quyền
                            </span>
                            <h1 class="display-4 font-weight-bold text-slate mb-3 lnkt-hero-title">
                                Lễ Hội <span class="text-peach">Ngôi Sao Nhỏ</span> – Tỏa Sáng Cổ Tích
                            </h1>
                            <p class="lead text-muted-custom mb-4 lnkt-hero-subtitle">
                                Chào đón ấn phẩm đặc biệt Ngôi Sao Nhỏ kết hợp sân chơi Siêu Sao Nhí KOL 2026.
                                Tặng kèm ngay 01 thú bông xinh xắn phiên bản giới hạn cho bé tỏa sáng rực rỡ.
                            </p>
                            <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
                                <a href="/FashionKids/views/pages/kol-stars.php"
                                   class="btn btn-pastel-primary px-4 py-2 font-weight-bold shadow-sm text-nowrap">
                                    <i class="fa-solid fa-crown mr-2"></i>Khám Phá Sao KOL Nhí &raquo;
                                </a>
                                <a href="/FashionKids/views/pages/kol-casting.php"
                                   class="btn btn-light border rounded-pill px-3 py-2 small font-weight-bold text-slate text-nowrap">
                                    <i class="fa-solid fa-wand-magic-sparkles mr-1 text-mint"></i>Ứng Tuyển Ngay
                                </a>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 text-center">
                            <div class="lnkt-hero-visual-box mx-auto p-4 shadow-sm">
                                <div class="lnkt-floating-badge-top badge badge-mint px-3 py-1 rounded-pill shadow-sm">
                                    <i class="fa-solid fa-gift mr-1"></i>Tặng 1 Thú Bông Xinh
                                </div>
                                <img src="/FashionKids/images/banners/banner-ngoi-sao-nho-qua-tang.webp" alt="Lễ Hội Ngôi Sao Nhỏ & Quà Tặng Thú Bông" class="lnkt-hero-banner-img lnkt-hero-banner-img-poster my-2">
                                <h5 class="font-weight-bold text-slate mb-1">Ấn Phẩm Ngôi Sao Nhỏ</h5>
                                <small class="text-muted-custom d-block">Đặc cách chụp hình Lookbook &amp; quà tặng thú bông</small>
                                <div class="mt-3 d-flex flex-wrap justify-content-center fk-btn-toolbar">
                                    <span class="badge badge-mint px-2 py-1 text-nowrap">Tặng Kèm Thú Bông</span>
                                    <span class="badge badge-sky px-2 py-1 text-nowrap">Studio Cổ Tích</span>
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

    <!-- 4B. CÁC SỰ KIỆN TIÊU BIỂU FASHIONKIDS 2026 -->
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
            <?php foreach ($homeFeaturedEvents as $fEv): ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card fk-event-featured-card h-100 shadow-sm d-flex flex-column">
                        <div class="fk-event-banner-wrap">
                            <span class="badge <?= htmlspecialchars($fEv['badge_class'] ?? 'badge-peach') ?> badge-pill px-3 py-1 fk-event-badge-overlay">
                                <?= htmlspecialchars($fEv['badge_text'] ?? 'Sự Kiện') ?>
                            </span>
                            <span class="fk-event-date-pill">
                                <i class="fa-regular fa-calendar-check text-peach mr-1"></i><?= htmlspecialchars($fEv['date_label'] ?? '') ?>
                            </span>
                            <a href="<?= htmlspecialchars($fEv['target_url'] ?? '#') ?>" class="d-block w-100 h-100">
                                <img src="<?= htmlspecialchars($fEv['image'] ?? '') ?>"
                                     alt="<?= htmlspecialchars($fEv['title'] ?? '') ?>"
                                     class="fk-event-banner-img">
                            </a>
                        </div>

                        <div class="card-body d-flex flex-column p-3 p-xl-4">
                            <h5 class="font-weight-bold text-slate mb-2">
                                <a href="<?= htmlspecialchars($fEv['target_url'] ?? '#') ?>" class="text-slate text-decoration-none">
                                    <?= htmlspecialchars($fEv['title'] ?? '') ?>
                                </a>
                            </h5>
                            <p class="text-muted-custom small mb-3 flex-grow-1" style="line-height: 1.55;">
                                <?= htmlspecialchars($fEv['subtitle'] ?? '') ?>
                            </p>

                            <?php if (!empty($fEv['perk_pills'])): ?>
                                <div class="d-flex flex-wrap mb-3" style="gap: 6px;">
                                    <?php foreach ($fEv['perk_pills'] as $pText): ?>
                                        <span class="badge badge-pill badge-light border text-slate px-2 py-1 small">
                                            <i class="fa-solid fa-gift text-peach mr-1"></i><?= htmlspecialchars($pText) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                <small class="text-muted-custom font-weight-bold"><?= htmlspecialchars($fEv['code'] ?? '') ?></small>
                                <a href="<?= htmlspecialchars($fEv['target_url'] ?? '#') ?>" class="btn btn-pastel-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm text-nowrap">
                                    <?= htmlspecialchars($fEv['cta_label'] ?? 'Xem chi tiết') ?> <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
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

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
