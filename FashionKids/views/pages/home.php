<?php
$page_title = "Trang Chủ & Banner Carousel 4 Giây (MH 01)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Danh sách sản phẩm nổi bật BST Mùa Hè 2026 trên Trang chủ (liên kết trực tiếp sang MH 02, MH 03, MH 05)
$lnkt_featured_products = [
    [
        'id'            => 101,
        'sku'           => 'FK-LBN-01',
        'image'         => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
        'name'          => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'category_name' => 'Váy công chúa',
        'gender_label'  => 'Bé Gái',
        'gender_badge'  => 'badge-peach',
        'price'         => 285000,
        'rating'        => 4.9,
        'reviews'       => 42,
        'icon'          => 'fa-person-dress text-peach',
        'size_hint'     => 'Size 1 - Size 5'
    ],
    [
        'id'            => 102,
        'sku'           => 'FK-LBN-02',
        'image'         => '/FashionKids/images/bo-thun-khung-long.jpg',
        'name'          => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'category_name' => 'Bộ thun',
        'gender_label'  => 'Bé Trai',
        'gender_badge'  => 'badge-sky',
        'price'         => 225000,
        'rating'        => 4.8,
        'reviews'       => 36,
        'icon'          => 'fa-shirt text-info',
        'size_hint'     => 'Size 1 - Size 5'
    ],
    [
        'id'            => 103,
        'sku'           => 'FK-LBN-03',
        'image'         => '/FashionKids/images/giay-sandal-tap-di.jpg',
        'name'          => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'category_name' => 'Giày dép',
        'gender_label'  => 'Unisex',
        'gender_badge'  => 'badge-gold',
        'price'         => 210000,
        'rating'        => 5.0,
        'reviews'       => 58,
        'icon'          => 'fa-shoe-prints text-warning',
        'size_hint'     => 'Size 21 - Size 28'
    ],
    [
        'id'            => 104,
        'sku'           => 'FK-LBN-04',
        'image'         => '/FashionKids/images/ao-khoac-cardigan.jpg',
        'name'          => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'category_name' => 'Áo khoác',
        'gender_label'  => 'Unisex',
        'gender_badge'  => 'badge-mint',
        'price'         => 320000,
        'rating'        => 4.9,
        'reviews'       => 29,
        'icon'          => 'fa-vest-patches text-success',
        'size_hint'     => 'Size 2 - Size 5'
    ]
];
?>

<div class="container my-4 my-md-5" id="lnkt_home_section">
    <!-- ====================================================================
         1. BANNER CAROUSEL TỰ ĐỘNG CHUYỂN SLIDE MỖI 4 GIÂY (MH 01)
         Cấu hình kỹ thuật: data-ride="carousel" và data-interval="4000"
         ==================================================================== -->
    <div id="lnkt_homeBannerCarousel" class="carousel slide lnkt-hero-carousel shadow-sm mb-5" data-ride="carousel" data-interval="4000">
        <!-- Dấu chấm chỉ báo Slide -->
        <ol class="carousel-indicators">
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#lnkt_homeBannerCarousel" data-slide-to="1"></li>
        </ol>

        <div class="carousel-inner">
            <!-- SLIDE 1: BST Mùa Hè 2026 – Vải sợi tre làm mát chống nóng dành cho làn da nhạy cảm -->
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
                                    <i class="fa-solid fa-ruler-horizontal mr-1 text-peach"></i>Đo Size Cho Bé (MH 04)
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

            <!-- SLIDE 2: Sự kiện tuyển chọn Siêu sao nhí KOL (CTA cam đào #FF9E93 -> MH 16) -->
            <div class="carousel-item">
                <div class="lnkt-hero-slide lnkt-slide-kol p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                            <div class="d-inline-flex align-items-center badge badge-gold px-3 py-2 rounded-pill mb-3 shadow-sm">
                                <i class="fa-solid fa-crown mr-2"></i>Chiến Dịch Truyền Thông Đặc Biệt • Cổng Casting MH 16
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
                                <a href="/FashionKids/views/pages/admin-banners.php"
                                   class="btn btn-light border rounded-pill px-3 py-2 mb-2 small font-weight-bold text-slate">
                                    <i class="fa-solid fa-sliders mr-1 text-peach"></i>Quản trị Banner (MH 23)
                                </a>
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

        <!-- Nút điều hướng Carousel Trái / Phải -->
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

    <!-- ====================================================================
         2. ĐIỀU HƯỚNG NHANH: CỤM NÚT TRÒN CHỌN DANH MỤC BST (BÉ GÁI, BÉ TRAI, GIÀY DÉP)
         ==================================================================== -->
    <div class="text-center mb-5">
        <span class="badge badge-pill badge-sky px-3 py-1 mb-2">Danh Mục Bộ Sưu Tập Chủ Đạo</span>
        <h3 class="font-weight-bold text-slate mb-2">Chọn Nhanh Phân Khu Thời Trang Cho Bé</h3>
        <p class="text-muted-custom small mb-4">
            Chạm vào từng biểu tượng tròn để lọc ngay các mẫu thiết kế sợi tre mềm mát theo giới tính và dòng sản phẩm
        </p>

        <div class="row justify-content-center">
            <!-- Nút tròn 1: Bé Gái -->
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

            <!-- Nút tròn 2: Bé Trai -->
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

            <!-- Nút tròn 3: Giày dép -->
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

    <!-- ====================================================================
         3. DẢI TIỆN ÍCH THÔNG MINH & GỢI Ý SIZE THƯỚC DÂY (MH 04)
         ==================================================================== -->
    <div class="card card-pastel p-4 mb-5 shadow-sm border-0 lnkt-measure-banner">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge badge-mint px-3 py-1 rounded-pill mr-2">
                        <i class="fa-solid fa-ruler-combined mr-1"></i>Công Nghệ Chọn Size Chuẩn 99%
                    </span>
                    <small class="font-weight-bold text-slate">Stored Function: <code>lnkt_suggest_size</code></small>
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
                    <i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Mở Thước Dây Gợi Ý Size (MH 04)
                </button>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         4. SẢN PHẨM NỔI BẬT TRONG BST MÙA HÈ 2026
         ==================================================================== -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge badge-pill badge-gold px-3 py-1 mb-1">
                    <i class="fa-solid fa-fire text-danger mr-1"></i>Best Sellers Mùa Hè 2026
                </span>
                <h3 class="font-weight-bold text-slate mb-0">Sản Phẩm Vải Sợi Tre Được Yêu Thích Nhất</h3>
            </div>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-outline btn-sm font-weight-bold mt-2 mt-sm-0">
                Xem toàn bộ cửa hàng (MH 02) <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($lnkt_featured_products as $prod): ?>
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="lbn-product-card h-100 d-flex flex-column shadow-sm">
                        <div class="lbn-product-img-wrap">
                            <span class="lbn-badge-bamboo lbn-badge-bamboo-overlay">
                                <i class="fa-solid fa-leaf mr-1"></i>Sợi Tre Làm Mát
                            </span>
                            <span class="badge badge-pill <?= $prod['gender_badge'] ?> lbn-badge-gender-overlay">
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
                                    <i class="fa-solid fa-star"></i> <?= number_format($prod['rating'], 1) ?> (<?= $prod['reviews'] ?>)
                                </span>
                            </div>

                            <h6 class="font-weight-bold text-slate mb-2">
                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="text-slate text-decoration-none">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>
                            </h6>

                            <div class="d-flex justify-content-between align-items-center small text-muted-custom mb-3">
                                <span>Mã: <code><?= htmlspecialchars($prod['sku']) ?></code></span>
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize" class="text-peach font-weight-bold text-decoration-none">
                                    <i class="fa-solid fa-ruler-horizontal mr-1"></i><?= htmlspecialchars($prod['size_hint']) ?>
                                </a>
                            </div>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="h6 font-weight-bold text-peach mb-0">
                                    <?= number_format($prod['price'], 0, ',', '.') ?> đ
                                </span>
                                <div>
                                    <button type="button"
                                            onclick="lbnQuickAddToCart('<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>', <?= $prod['price'] ?>)"
                                            class="btn btn-light btn-sm rounded-circle border mr-1" title="Thêm nhanh vào giỏ">
                                        <i class="fa-solid fa-cart-plus text-peach"></i>
                                    </button>
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

    <!-- ====================================================================
         5. ĐIỀU HƯỚNG NHANH PHÂN HỆ LÊ NGUYỄN KHÁNH TRÌNH (lnkt_)
         ==================================================================== -->
    <div class="card card-pastel p-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h6 class="font-weight-bold text-slate mb-0">
                    <i class="fa-solid fa-compass text-peach mr-2"></i>Lối Tắt Điều Hướng Nhanh Phân Hệ Orders, Size Chart &amp; KOL Casting (<code>lnkt_</code>)
                </h6>
                <small class="text-muted-custom">Truy cập trực tiếp các màn hình nghiệp vụ khách hàng, nhân viên vận hành và quản trị viên</small>
            </div>
            <span class="badge badge-pill badge-mint px-3 py-1">Nhánh: feature/lnkt-orders-kol</span>
        </div>
        <div class="row">
            <div class="col-6 col-md-3 mb-2">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-ruler-combined text-peach mr-1"></i> MH 04: Modal Gợi Ý Size
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <a href="/FashionKids/views/pages/checkout.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-basket-shopping text-peach mr-1"></i> MH 05: Giỏ Hàng &amp; Đặt Hàng
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <a href="/FashionKids/views/pages/order-tracking.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-truck-fast text-info mr-1"></i> MH 06: Tiến Trình Đơn Hàng
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <a href="/FashionKids/views/pages/kol-casting.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-crown text-warning mr-1"></i> MH 16: Cổng Casting KOL Nhí
                </a>
            </div>
            <div class="col-6 col-md-4 mb-2 mb-md-0">
                <a href="/FashionKids/views/pages/staff-orders.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-clipboard-check text-success mr-1"></i> MH 10: Nhân Viên Duyệt Đơn
                </a>
            </div>
            <div class="col-6 col-md-4 mb-2 mb-md-0">
                <a href="/FashionKids/views/pages/admin-size-charts.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-table-cells text-primary mr-1"></i> MH 14: Quản Trị Bảng Size
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="/FashionKids/views/pages/admin-banners.php"
                   class="btn btn-light border btn-block text-left small font-weight-bold text-slate rounded-pill px-3 py-2">
                    <i class="fa-solid fa-images text-danger mr-1"></i> MH 23: Quản Trị Banner Slider
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../layouts/footer.php";
?>

