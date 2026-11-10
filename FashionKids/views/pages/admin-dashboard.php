<?php
$page_title = "Bàn Làm Việc Quản Trị Hệ Thống (MH 12)";
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess(__FILE__); // RBAC: chặn trước khi xuất bất kỳ nội dung nào
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Dữ liệu mô phỏng đơn hàng mới nhất cần duyệt (lnkt_orders)
$recent_orders = [
    [
        'order_id'       => 1085,
        'customer_name'  => 'Võ Thị Thanh Thảo',
        'phone'          => '0918889999',
        'address'        => 'Số 120 Điện Biên Phủ, Phường 17, Bình Thạnh, TP.HCM',
        'items_count'    => 3,
        'total_amount'   => 648000,
        'vip_tier'       => 'Hạng Vàng (-10%)',
        'payment_method' => 'COD',
        'status'         => 'cho_xu_ly',
        'status_label'   => 'Chờ xử lý',
        'status_badge'   => 'badge-warning',
        'time'           => '10 phút trước'
    ],
    [
        'order_id'       => 1084,
        'customer_name'  => 'Trần Thị Bông',
        'phone'          => '0903112233',
        'address'        => 'Số 45 Lê Duẩn, Quận 1, TP.HCM',
        'items_count'    => 1,
        'total_amount'   => 285000,
        'vip_tier'       => 'Hạng Đồng (0%)',
        'payment_method' => 'VIETQR',
        'status'         => 'dang_dong_goi',
        'status_label'   => 'Đang đóng gói',
        'status_badge'   => 'badge-info',
        'time'           => '35 phút trước'
    ],
    [
        'order_id'       => 1083,
        'customer_name'  => 'Lê Hoàng Nam',
        'phone'          => '0988776655',
        'address'        => 'Tòa Park 6, Times City, Hai Bà Trưng, Hà Nội',
        'items_count'    => 2,
        'total_amount'   => 545000,
        'vip_tier'       => 'Hạng Bạc (-5%)',
        'payment_method' => 'COD',
        'status'         => 'dang_giao',
        'status_label'   => 'Đang vận chuyển',
        'status_badge'   => 'badge-primary',
        'time'           => '2 giờ trước'
    ],
    [
        'order_id'       => 1082,
        'customer_name'  => 'Nguyễn Mai Anh',
        'phone'          => '0934556677',
        'address'        => 'Số 88 Nguyễn Văn Linh, Hải Châu, Đà Nẵng',
        'items_count'    => 4,
        'total_amount'   => 920000,
        'vip_tier'       => 'Kim Cương (-15%)',
        'payment_method' => 'VIETQR',
        'status'         => 'hoan_tat',
        'status_label'   => 'Hoàn tất',
        'status_badge'   => 'badge-success',
        'time'           => '5 giờ trước'
    ]
];

// Danh sách sản phẩm cảnh báo tồn kho <= 5 (lbn_products)
$low_stock_products = [
    [
        'id'             => 107,
        'sku'            => 'FK-LBN-07',
        'name'           => 'Áo Khoác Hoodie Tai Thỏ Sợi Tre Ấm Áp Cho Bé Gái',
        'category_name'  => 'Áo khoác',
        'price'          => 340000,
        'stock_quantity' => 4,
        'reorder_level'  => 15
    ],
    [
        'id'             => 104,
        'sku'            => 'FK-LBN-04',
        'name'           => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'category_name'  => 'Áo khoác',
        'price'          => 320000,
        'stock_quantity' => 5,
        'reorder_level'  => 20
    ],
    [
        'id'             => 109,
        'sku'            => 'FK-LBN-09',
        'name'           => 'Áo Khoác Bomber Sợi Tre Phi Hành Gia Nhí Bé Trai',
        'category_name'  => 'Áo khoác',
        'price'          => 350000,
        'stock_quantity' => 9,
        'reorder_level'  => 20
    ]
];
?>

<div class="container-fluid my-4 px-lg-5" id="admin_dashboard_section">
    <!-- Tiêu Đề Bàn Làm Việc & Nút Tác Vụ -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-gauge-high text-peach mr-2"></i>Bàn Làm Việc Quản Trị Hệ Thống (MH 12)
            </h3>
            <small class="text-muted-custom">
                Thống kê dòng tiền, điều phối vận đơn, cảnh báo ngưỡng tồn kho tự động &amp; phân quyền người dùng
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge badge-pill badge-mint px-3 py-2 mr-2 font-weight-bold">
                <i class="fa-solid fa-shield-halved mr-1"></i>Phiên Quản Trị: Hồ Gia Bảo (Admin)
            </span>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-pastel-primary btn-sm font-weight-bold">
                <i class="fa-solid fa-boxes-stacked mr-1"></i> Nhập Kho Ngay (MH 13)
            </a>
        </div>
    </div>

    <!-- Thanh Điều Hướng Các Phân Hệ Quản Trị (Admin Navigation Bar) -->
    <div class="card card-pastel border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap justify-content-start align-items-center">
            <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold mr-2 mb-1">
                <i class="fa-solid fa-chart-pie mr-1"></i> Tổng Quan
            </a>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-boxes-stacked text-peach mr-1"></i> Quản Lý Kho &amp; SP (MH 13)
            </a>
            <a href="/FashionKids/views/pages/admin-size-charts.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-ruler-combined text-info mr-1"></i> Bảng Quy Chuẩn Size (MH 14)
            </a>
            <a href="/FashionKids/views/pages/admin-users.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-users text-success mr-1"></i> Người Dùng &amp; Hạng VIP (MH 15)
            </a>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-truck-ramp-box text-warning mr-1"></i> Xử Lý Đơn Hàng (MH 10)
            </a>
            <a href="/FashionKids/views/pages/staff-chat.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-headset text-danger mr-1"></i> CSKH Trực Tuyến (MH 11)
            </a>
            <a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-images text-secondary mr-1"></i> Banner Carousel (MH 23)
            </a>
            <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mb-1 border">
                <i class="fa-solid fa-newspaper text-primary mr-1"></i> Tạp Chí Bài Viết
            </a>
        </div>
    </div>

    <!-- Hàng 4 Card KPI Đo Lường Hiệu Năng Vận Hành -->
    <div class="row mb-4">
        <!-- KPI 1: Doanh Thu Tháng -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Doanh Thu Tháng 11</small>
                        <h4 class="font-weight-bold text-slate mb-1">128.450.000 đ</h4>
                        <span class="badge badge-pill badge-mint small">
                            <i class="fa-solid fa-arrow-trend-up mr-1"></i>+14.2% so với tháng trước
                        </span>
                    </div>
                    <div class="rounded-circle p-3 bg-peach text-white d-flex align-items-center justify-content-center avatar-56">
                        <i class="fa-solid fa-sack-dollar h4 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Tổng Đơn Hàng -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Tổng Đơn Hàng</small>
                        <h4 class="font-weight-bold text-slate mb-1">342 đơn</h4>
                        <span class="badge badge-pill badge-warning small text-dark">
                            <i class="fa-solid fa-clock mr-1"></i>28 đơn đang chờ xử lý
                        </span>
                    </div>
                    <div class="rounded-circle p-3 bg-sky text-dark d-flex align-items-center justify-content-center avatar-56">
                        <i class="fa-solid fa-cart-shopping h4 mb-0 text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Cảnh Báo Tồn Kho Thấp -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Cảnh Báo Tồn Kho (≤ 5)</small>
                        <h4 class="font-weight-bold text-danger mb-1"><?= count($low_stock_products) ?> mã SP</h4>
                        <a href="/FashionKids/views/pages/admin-inventory.php" class="small font-weight-bold text-danger">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>Xem danh sách thiếu hàng
                        </a>
                    </div>
                    <div class="rounded-circle p-3 bg-danger text-white d-flex align-items-center justify-content-center avatar-56">
                        <i class="fa-solid fa-box-open h4 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Khách Hàng VIP Mới -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Hội Viên VIP Mới</small>
                        <h4 class="font-weight-bold text-slate mb-1">48 phụ huynh</h4>
                        <span class="badge badge-pill badge-gold small">
                            <i class="fa-solid fa-crown mr-1"></i>Tổng 1.250 khách hàng VIP
                        </span>
                    </div>
                    <div class="rounded-circle p-3 badge-gold d-flex align-items-center justify-content-center avatar-56">
                        <i class="fa-solid fa-award h4 mb-0 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 2: Biểu Đồ Doanh Thu & Cơ Cấu Danh Mục -->
    <div class="row mb-4">
        <!-- Cột Trái (8 Cột): Phân Tích Doanh Thu 7 Ngày Gần Nhất -->
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
            <div class="card card-pastel shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-chart-column text-peach mr-2"></i>Biểu Đồ Doanh Thu Tuần Hiện Tại (VNĐ)
                        </h6>
                        <small class="text-muted-custom">Theo dõi tốc độ tăng trưởng doanh số bán hàng D2C hàng ngày</small>
                    </div>
                    <span class="badge badge-pill badge-mint px-3 py-1 font-weight-bold">Đạt 112% Chỉ Tiêu</span>
                </div>
                <div class="card-body p-4 pt-2">
                    <!-- Mô phỏng biểu đồ cột 7 ngày -->
                    <div class="d-flex justify-content-between align-items-end pt-4 pb-2 text-center chart-container-220">
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-slate mb-1">14.2M</small>
                            <div class="w-100 bg-sky rounded-top" title="Thứ Hai: 14.200.000 đ" data-fk-height="110"></div>
                            <small class="text-muted-custom mt-2 font-weight-bold">T2 (02/11)</small>
                        </div>
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-slate mb-1">16.8M</small>
                            <div class="w-100 bg-sky rounded-top" title="Thứ Ba: 16.800.000 đ" data-fk-height="135"></div>
                            <small class="text-muted-custom mt-2 font-weight-bold">T3 (03/11)</small>
                        </div>
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-slate mb-1">19.5M</small>
                            <div class="w-100 bg-sky rounded-top" title="Thứ Tư: 19.500.000 đ" data-fk-height="155"></div>
                            <small class="text-muted-custom mt-2 font-weight-bold">T4 (04/11)</small>
                        </div>
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-slate mb-1">17.2M</small>
                            <div class="w-100 bg-sky rounded-top" title="Thứ Năm: 17.200.000 đ" data-fk-height="138"></div>
                            <small class="text-muted-custom mt-2 font-weight-bold">T5 (05/11)</small>
                        </div>
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-peach mb-1">24.6M</small>
                            <div class="w-100 bg-peach rounded-top" title="Thứ Sáu: 24.600.000 đ" data-fk-height="190"></div>
                            <small class="text-peach mt-2 font-weight-bold">T6 (06/11)</small>
                        </div>
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-peach mb-1">21.8M</small>
                            <div class="w-100 bg-peach rounded-top" title="Thứ Bảy: 21.800.000 đ" data-fk-height="172"></div>
                            <small class="text-peach mt-2 font-weight-bold">T7 (07/11)</small>
                        </div>
                        <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center">
                            <small class="font-weight-bold text-slate mb-1">14.3M</small>
                            <div class="w-100 bg-mint-soft rounded-top" title="Hôm nay: 14.350.000 đ" data-fk-height="115"></div>
                            <small class="text-success mt-2 font-weight-bold">CN (Hôm nay)</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 small text-muted-custom">
                        <span><i class="fa-solid fa-circle text-sky mr-1"></i>Ngày thường trong tuần</span>
                        <span><i class="fa-solid fa-circle text-peach mr-1"></i>Cao điểm cuối tuần (Flash Sale)</span>
                        <span><i class="fa-solid fa-circle text-mint-dark mr-1"></i>Doanh thu thời gian thực hôm nay</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải (4 Cột): Tỷ Trọng Doanh Thu Theo Danh Mục (lbn_categories) -->
        <div class="col-12 col-xl-4">
            <div class="card card-pastel shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-chart-pie text-peach mr-2"></i>Cơ Cấu Doanh Số Danh Mục
                    </h6>
                    <small class="text-muted-custom">4 nhóm sản phẩm chính của hệ thống FashionKids</small>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 small font-weight-bold">
                            <span><i class="fa-solid fa-wand-magic-sparkles text-peach mr-2"></i>Váy Công Chúa (Bé gái)</span>
                            <span class="text-slate">38% (48.8M)</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-peach" role="progressbar" data-fk-width="38"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 small font-weight-bold">
                            <span><i class="fa-solid fa-shirt text-info mr-2"></i>Bộ Thun Sợi Tre (Bé trai &amp; Unisex)</span>
                            <span class="text-slate">32% (41.1M)</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-sky" role="progressbar" data-fk-width="32"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 small font-weight-bold">
                            <span><i class="fa-solid fa-shoe-prints text-warning mr-2"></i>Giày Sandal Tập Đi (Đế cao su)</span>
                            <span class="text-slate">18% (23.1M)</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar badge-gold" role="progressbar" data-fk-width="18"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 small font-weight-bold">
                            <span><i class="fa-solid fa-vest-patches text-success mr-2"></i>Áo Khoác Chống Nắng UV</span>
                            <span class="text-slate">12% (15.4M)</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-mint-soft" role="progressbar" data-fk-width="12"></div>
                        </div>
                    </div>

                    <div class="p-3 rounded bg-milk-cream border mt-4 text-center">
                        <small class="text-muted-custom d-block mb-1">Chất liệu sợi tre hữu cơ đóng góp</small>
                        <h5 class="font-weight-bold text-success mb-0">86.4% Tổng Lượng Tiêu Thụ</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 3: Hai Bảng Dữ Liệu Trọng Yếu: Đơn Hàng Mới Cần Duyệt & Cảnh Báo Tồn Kho -->
    <div class="row">
        <!-- Bảng 1: Đơn Hàng Mới Nhất Chờ Xử Lý (lnkt_orders) -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-receipt text-peach mr-2"></i>Đơn Hàng Mới Chờ Xử Lý (lnkt_orders)
                        </h6>
                        <small class="text-muted-custom">Tiếp nhận đơn hàng đặt trực tiếp qua giỏ hàng MH 05</small>
                    </div>
                    <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                        Xem Tất Cả Đơn <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Phụ Huynh &amp; SĐT</th>
                                    <th>Địa Chỉ Giao</th>
                                    <th>Hạng VIP</th>
                                    <th class="text-right">Tổng Tiền</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $ord): ?>
                                    <tr>
                                        <td class="font-weight-bold text-slate">#<?= $ord['order_id'] ?></td>
                                        <td>
                                            <div class="font-weight-bold text-slate"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                            <small class="text-muted-custom"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($ord['phone']) ?></small>
                                        </td>
                                        <td>
                                            <small class="text-slate d-inline-block fk-ellipsis-220" title="<?= htmlspecialchars($ord['address']) ?>">
                                                <?= htmlspecialchars($ord['address']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-light border text-slate small font-weight-bold">
                                                <?= htmlspecialchars($ord['vip_tier']) ?>
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold text-peach text-nowrap">
                                            <?= number_format($ord['total_amount'], 0, ',', '.') ?> đ
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pill <?= $ord['status_badge'] ?> px-2 py-1">
                                                <?= htmlspecialchars($ord['status_label']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="/FashionKids/views/pages/staff-orders.php?order_id=<?= $ord['order_id'] ?>" class="btn btn-sm btn-pastel-sky font-weight-bold rounded-pill px-3">
                                                Duyệt
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng 2: Cảnh Báo Ngưỡng Tồn Kho Thấp (lbn_products) -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-danger mb-0">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i>Cảnh Báo Tồn Kho (≤ 5 Cái)
                        </h6>
                        <small class="text-muted-custom">Kích hoạt quy trình nhập bổ sung hàng hóa D2C</small>
                    </div>
                    <a href="/FashionKids/views/pages/admin-inventory.php" class="small font-weight-bold text-peach">
                        Kho hàng <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($low_stock_products as $lsp): ?>
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold text-slate small"><?= htmlspecialchars($lsp['name']) ?></div>
                                    <small class="text-muted-custom">
                                        Mã: <code><?= htmlspecialchars($lsp['sku']) ?></code> • Giá: <?= number_format($lsp['price'], 0, ',', '.') ?> đ
                                    </small>
                                </div>
                                <div class="text-right">
                                    <span class="badge lbn-badge-low-stock font-weight-bold px-2 py-1 d-block mb-1">
                                        Còn <?= $lsp['stock_quantity'] ?> cái
                                    </span>
                                    <a href="/FashionKids/views/pages/admin-inventory.php" class="small font-weight-bold text-primary">
                                        <i class="fa-solid fa-plus-circle mr-1"></i>Nhập kho
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-outline-danger btn-sm btn-block font-weight-bold rounded-pill">
                        <i class="fa-solid fa-boxes-packing mr-1"></i> Mở Bảng Quản Lý Tồn Kho Chi Tiết (MH 13)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../layouts/footer.php";
?>

