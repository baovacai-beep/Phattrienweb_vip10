<?php
$page_title = "Trang Chủ & BST Mùa Hè Sợi Tre Kháng Khuẩn (MH 01)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh sách sản phẩm tiêu biểu BST Mùa Hè 2026 trên trang chủ
$lnkt_featured_products = [
    [
        'id'       => 101,
        'name'     => 'Đầm Xòe Sợi Tre Hoa Cúc Họa Mi',
        'category' => 'Bé Gái',
        'badge'    => 'badge-mint',
        'tag'      => '100% Bamboo Organic',
        'price'    => 285000,
        'size'     => 'Size 2 (2-3T)',
        'icon'     => 'fa-shirt text-peach'
    ],
    [
        'id'       => 102,
        'name'     => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí',
        'category' => 'Bé Trai',
        'badge'    => 'badge-sky',
        'tag'      => 'Chống Nóng Mùa Hè',
        'price'    => 245000,
        'size'     => 'Size 3 (3-4T)',
        'icon'     => 'fa-child-reaching text-info'
    ],
    [
        'id'       => 103,
        'name'     => 'Sandal Tập Đi Đế Mềm Chống Trượt Pastel',
        'category' => 'Giày dép',
        'badge'    => 'badge-gold',
        'tag'      => 'Đế Cao Su Non',
        'price'    => 210000,
        'size'     => 'Size 24 (14cm)',
        'icon'     => 'fa-shoe-prints text-warning'
    ],
    [
        'id'       => 104,
        'name'     => 'Bodysuit Sợi Tre Kháng Khuẩn Sơ Sinh',
        'category' => 'Bé Gái / Bé Trai',
        'badge'    => 'badge-mint',
        'tag'      => 'Da Nhạy Cảm',
        'price'    => 195000,
        'size'     => 'Size 1 (0-1T)',
        'icon'     => 'fa-baby text-success'
    ]
];
?>

<div class="container my-4 my-md-5" id="lnkt_home_container">
    <!-- ====================================================================
         1. BANNER CAROUSEL TỰ ĐỘNG CHUYỂN SLIDE MỖI 4 GIÂY (MH 01)
         Cấu hình kỹ thuật: data-ride="carousel" và data-interval="4000"
         ==================================================================== -->
    <div id="lnkt_homeHeroCarousel" class="carousel slide lnkt-carousel-banner mb-5" data-ride="carousel" data-interval="4000">
        <!-- Chỉ mục Slide (Indicators) -->
        <ol class="carousel-indicators">
            <li data-target="#lnkt_homeHeroCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#lnkt_homeHeroCarousel" data-slide-to="1"></li>
        </ol>

        <div class="carousel-inner">
            <!-- SLIDE 1: BST Mùa Hè 2026 - Vải sợi tre làm mát chống nóng dành cho làn da nhạy cảm -->
            <div class="carousel-item active lnkt-slide-summer p-4 p-md-5">
                <div class="row align-items-center py-md-3">
                    <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                        <span class="badge badge-pill badge-mint px-3 py-2 mb-3 shadow-sm">
                            <i class="fa-solid fa-sun mr-1"></i> BỘ SƯU TẬP MỚI NHẤT
                        </span>
                        <h2 class="font-weight-bold text-slate mb-3">
                            BST Mùa Hè 2026 – Vải sợi tre làm mát chống nóng dành cho làn da nhạy cảm
                        </h2>
                        <p class="text-slate mb-4">
                            Chất liệu 100% sợi tre hữu cơ (Organic Bamboo) thoáng khí gấp 2 lần cotton, kháng khuẩn tự nhiên và hạ nhiệt dịu mát cho bé yêu tự do vui chơi suốt ngày hè.
                        </p>
                        <div class="d-flex flex-wrap align-items-center">
                            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary font-weight-bold shadow-sm mr-3 mb-2">
                                <i class="fa-solid fa-bag-shopping mr-1"></i> Khám Phá BST Hè 2026
                            </a>
                            <button type="button" class="btn btn-light rounded-pill font-weight-bold px-4 py-2 shadow-sm border mb-2 text-slate" data-toggle="modal" data-target="#modalSuggestSize">
                                <i class="fa-solid fa-ruler-horizontal text-peach mr-1"></i> Gợi Ý Size Thước Dây
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 text-center">
                        <div class="lnkt-hero-illustration">
                            <div class="text-center">
                                <i class="fa-solid fa-leaf text-mint-dark display-3 mb-2 d-block"></i>
                                <span class="badge badge-pill badge-light px-3 py-1 font-weight-bold text-slate shadow-sm">
                                    Sợi Tre Kháng Khuẩn 99.8%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: Sự kiện tuyển chọn Siêu sao nhí KOL với nút CTA cam đào (#FF9E93) -->
            <div class="carousel-item lnkt-slide-kol p-4 p-md-5">
                <div class="row align-items-center py-md-3">
                    <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                        <span class="badge badge-pill badge-gold px-3 py-2 mb-3 shadow-sm">
                            <i class="fa-solid fa-crown mr-1"></i> SỰ KIỆN ĐẶC BIỆT FASHIONKIDS 2026
                        </span>
                        <h2 class="font-weight-bold text-slate mb-3">
                            Sự kiện tuyển chọn Siêu sao nhí KOL – Gương mặt đại diện thương hiệu
                        </h2>
                        <p class="text-slate mb-4">
                            Cơ hội để bé yêu tỏa sáng trong Lookbook Thời trang Hè 2026 cùng gói quà tặng trang phục sợi tre cao cấp và học bổng tài năng nhí trị giá 10.000.000đ.
                        </p>
                        <!-- Nút CTA cam đào (#FF9E93) điều hướng trực tiếp đến MH 16 -->
                        <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-pastel-primary font-weight-bold shadow-sm px-4 py-2">
                            Đăng ký cho bé tham gia ngay &raquo;
                        </a>
                    </div>
                    <div class="col-12 col-lg-5 text-center">
                        <div class="lnkt-hero-illustration">
                            <div class="text-center">
                                <i class="fa-solid fa-camera-retro text-peach display-3 mb-2 d-block"></i>
                                <span class="badge badge-pill badge-light px-3 py-1 font-weight-bold text-slate shadow-sm">
                                    Casting KOL Nhí (0 - 6 Tuổi)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nút điều hướng Trước / Sau của Carousel -->
        <a class="carousel-control-prev" href="#lnkt_homeHeroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="sr-only">Slide trước</span>
        </a>
        <a class="carousel-control-next" href="#lnkt_homeHeroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="sr-only">Slide tiếp theo</span>
        </a>
    </div>

    <!-- ====================================================================
         2. THÀNH PHẦN ĐIỀU HƯỚNG NHANH: CỤM NÚT TRÒN CHỌN DANH MỤC BST
         Danh mục: Bé Gái, Bé Trai, Giày dép (+ Tiện ích Đo Size Thước Dây)
         ==================================================================== -->
    <div class="card card-pastel p-4 mb-5 shadow-sm bg-white">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-slate mb-1">Khám Phá Nhanh Danh Mục Bộ Sưu Tập</h4>
            <small class="text-muted-custom">Chọn nhanh phân khu thời trang phù hợp cho bé yêu của bạn</small>
        </div>

        <div class="row justify-content-center text-center">
            <!-- Nút tròn 1: Bé Gái -->
            <div class="col-6 col-sm-4 col-md-3 mb-3">
                <a href="/FashionKids/views/pages/shop-catalog.php?gender=girl" class="text-decoration-none lnkt-cat-link d-inline-block">
                    <div class="lnkt-cat-circle lnkt-cat-girl mb-2">
                        <i class="fa-solid fa-wand-magic-sparkles h2 mb-0"></i>
                    </div>
                    <h6 class="font-weight-bold text-slate mb-0">Bé Gái</h6>
                    <small class="text-muted-custom">Đầm xòe &amp; Đồ bộ dịu dàng</small>
                </a>
            </div>

            <!-- Nút tròn 2: Bé Trai -->
            <div class="col-6 col-sm-4 col-md-3 mb-3">
                <a href="/FashionKids/views/pages/shop-catalog.php?gender=boy" class="text-decoration-none lnkt-cat-link d-inline-block">
                    <div class="lnkt-cat-circle lnkt-cat-boy mb-2">
                        <i class="fa-solid fa-rocket h2 mb-0"></i>
                    </div>
                    <h6 class="font-weight-bold text-slate mb-0">Bé Trai</h6>
                    <small class="text-muted-custom">Áo thun &amp; Short năng động</small>
                </a>
            </div>

            <!-- Nút tròn 3: Giày dép -->
            <div class="col-6 col-sm-4 col-md-3 mb-3">
                <a href="/FashionKids/views/pages/shop-catalog.php?type=giay_dep" class="text-decoration-none lnkt-cat-link d-inline-block">
                    <div class="lnkt-cat-circle lnkt-cat-shoes mb-2">
                        <i class="fa-solid fa-shoe-prints h2 mb-0"></i>
                    </div>
                    <h6 class="font-weight-bold text-slate mb-0">Giày dép</h6>
                    <small class="text-muted-custom">Sandal tập đi &amp; Sneaker êm</small>
                </a>
            </div>

            <!-- Nút tròn 4: Bảng Size Thước Dây Thông Minh (MH 04) -->
            <div class="col-6 col-sm-4 col-md-3 mb-3">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalSuggestSize" class="text-decoration-none lnkt-cat-link d-inline-block">
                    <div class="lnkt-cat-circle lnkt-cat-size mb-2">
                        <i class="fa-solid fa-ruler-combined h2 mb-0"></i>
                    </div>
                    <h6 class="font-weight-bold text-slate mb-0">Đo Size Nhanh</h6>
                    <small class="text-muted-custom">Gợi ý size theo số đo (cm)</small>
                </a>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         3. SẢN PHẨM TIÊU BIỂU TRONG BST MÙA HÈ 2026
         ==================================================================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">Sản Phẩm Nổi Bật – BST Mùa Hè 2026</h4>
            <small class="text-muted-custom">Thiết kế chuẩn phom trẻ em Việt Nam từ 0 đến 6 tuổi</small>
        </div>
        <button type="button" class="btn btn-pastel-outline btn-sm font-weight-bold" data-toggle="modal" data-target="#modalSuggestSize">
            <i class="fa-solid fa-ruler mr-1"></i> Mở Thước Dây Chọn Size (MH 04)
        </button>
    </div>

    <div class="row">
        <?php foreach ($lnkt_featured_products as $prod): ?>
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="card card-pastel h-100 shadow-sm">
                    <div class="lnkt-product-thumb position-relative">
                        <i class="fa-solid <?= $prod['icon'] ?> display-3"></i>
                    </div>
                    <div class="card-body d-flex flex-column p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge <?= $prod['badge'] ?> px-2 py-1 rounded-pill small">
                                <?= htmlspecialchars($prod['category']) ?>
                            </span>
                            <small class="text-muted-custom font-weight-bold"><?= htmlspecialchars($prod['size']) ?></small>
                        </div>
                        <h6 class="font-weight-bold text-slate mb-1">
                            <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="text-slate text-decoration-none">
                                <?= htmlspecialchars($prod['name']) ?>
                            </a>
                        </h6>
                        <small class="text-muted-custom mb-3">
                            <i class="fa-solid fa-check-double text-success mr-1"></i><?= htmlspecialchars($prod['tag']) ?>
                        </small>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="h6 font-weight-bold text-peach mb-0">
                                <?= number_format($prod['price'], 0, ',', '.') ?> đ
                            </span>
                            <a href="/FashionKids/views/pages/checkout.php" class="btn btn-pastel-primary btn-sm px-3 py-1">
                                <i class="fa-solid fa-cart-plus mr-1"></i> Mua
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once "../modals/modal-suggest-size.php";
require_once "../layouts/footer.php";
?>
