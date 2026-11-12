<?php
$page_title = "Bảng Điều Khiển Quản Trị Hệ Thống";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();
require_once __DIR__ . "/../../models/OrderModel.php";

$dashOrderModel       = new OrderModel();
$dashData             = $dashOrderModel->getAdminDashboardData();
$lnkt_return_requests = $dashOrderModel->getAllReturnRequests('all', '');
$returnCounts         = $dashData['return_counts'] ?? $dashOrderModel->getReturnStatusCounts();
$pendingReturnCount   = (int)($returnCounts['cho_duyet_hoan'] ?? 0);
$initTabReturns       = (($_GET['tab'] ?? '') === 'returns');

$completedRevenue  = (float)($dashData['completed_revenue'] ?? 0);
$pipelineRevenue   = (float)($dashData['pipeline_revenue'] ?? 0);
$totalOrders       = (int)($dashData['total_orders'] ?? 0);
$pendingOrders     = (int)($dashData['pending_orders'] ?? 0);
$completedOrders   = (int)($dashData['completed_orders'] ?? 0);
$totalCustomers    = (int)($dashData['total_customers'] ?? 0);
$totalKolProfiles  = (int)($dashData['total_kol_profiles'] ?? 0);
$lowStockProducts  = $dashData['low_stock_products'] ?? [];
$lowStockCount     = (int)($dashData['low_stock_count'] ?? count($lowStockProducts));
$recent_orders     = $dashData['recent_orders'] ?? [];
$revenue7Days      = $dashData['revenue_7_days'] ?? [];
$catBreakdown      = $dashData['category_breakdown'] ?? [];

$maxDayRev = 0.0;
foreach ($revenue7Days as $dayRow) {
    $rev = (float)($dayRow['revenue'] ?? 0);
    if ($rev > $maxDayRev) {
        $maxDayRev = $rev;
    }
}

$totalCatRev = 0.0;
foreach ($catBreakdown as $catRow) {
    $totalCatRev += (float)($catRow['revenue'] ?? 0);
}

function fkFormatCompactVnd(float $amount): string {
    if ($amount >= 1000000) {
        return number_format($amount / 1000000, 1, ',', '.') . 'M';
    }
    if ($amount >= 1000) {
        return number_format($amount / 1000, 0, ',', '.') . 'K';
    }
    return '0 đ';
}

$catColors = [
    ['bar' => 'bg-peach',     'icon' => 'fa-wand-magic-sparkles text-peach'],
    ['bar' => 'bg-sky',       'icon' => 'fa-shirt text-info'],
    ['bar' => 'bg-mint-soft', 'icon' => 'fa-vest-patches text-success'],
    ['bar' => 'badge-gold',   'icon' => 'fa-shoe-prints text-warning'],
];
?>

<div class="container-fluid my-4 px-lg-5" id="admin_dashboard_section">
    <!-- Tiêu Đề Bàn Làm Việc & Nút Tác Vụ -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-gauge-high text-peach mr-2"></i>Bàn Làm Việc Quản Trị Hệ Thống
            </h3>
            <small class="text-muted-custom">
                Thống kê doanh thu thực tế, điều phối vận đơn, cảnh báo tồn kho (&le; 5) &amp; quản trị thành viên
            </small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center fk-btn-toolbar">
            <span class="badge badge-pill badge-mint px-3 py-2 font-weight-bold text-nowrap">
                <i class="fa-solid fa-shield-halved mr-1"></i>Phiên Quản Trị: <?= htmlspecialchars($_SESSION['user']['ho_va_ten'] ?? 'Quản trị viên') ?>
            </span>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-pastel-primary btn-sm font-weight-bold text-nowrap">
                <i class="fa-solid fa-boxes-stacked mr-1"></i> Nhập Kho Ngay
            </a>
        </div>
    </div>

    <!-- Thanh Điều Hướng Các Phân Hệ Quản Trị -->
    <div class="card card-pastel border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap justify-content-start align-items-center fk-btn-toolbar">
            <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold text-nowrap">
                <i class="fa-solid fa-chart-pie mr-1"></i> Tổng Quan
            </a>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-boxes-stacked text-peach mr-1"></i> Quản Lý Kho &amp; SP
            </a>
            <a href="/FashionKids/views/pages/admin-size-charts.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-ruler-combined text-info mr-1"></i> Bảng Quy Chuẩn Size
            </a>
            <a href="/FashionKids/views/pages/admin-users.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-users text-success mr-1"></i> Người Dùng &amp; Hạng VIP
            </a>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-truck-ramp-box text-warning mr-1"></i> Xử Lý Đơn Hàng
            </a>
            <a href="/FashionKids/views/pages/staff-chat.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-headset text-danger mr-1"></i> CSKH Trực Tuyến
            </a>
            <a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-images text-secondary mr-1"></i> Banner Chiến Dịch
            </a>
            <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-newspaper text-primary mr-1"></i> Tạp Chí Bài Viết
            </a>
        </div>
    </div>

    <!-- Thanh chuyển Tab: Tổng quan hệ thống & Quản lý hoàn hàng -->
    <ul class="nav nav-pills fk-nav-pills-pastel mb-3" id="admin_dashTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $initTabReturns ? '' : 'active' ?>"
               id="admin_tabOverviewLink"
               data-toggle="pill"
               href="#tab-overview"
               role="tab"
               aria-controls="tab-overview"
               aria-selected="<?= $initTabReturns ? 'false' : 'true' ?>">
                <i class="fa-solid fa-chart-line mr-1"></i>Tổng Quan Kinh Doanh &amp; Kho
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $initTabReturns ? 'active' : '' ?>"
               id="admin_tabReturnsLink"
               data-toggle="pill"
               href="#tab-return-requests"
               role="tab"
               aria-controls="tab-return-requests"
               aria-selected="<?= $initTabReturns ? 'true' : 'false' ?>">
                <i class="fa-solid fa-rotate-left mr-1"></i>Quản lý Hoàn hàng &amp; Trả hàng
                <span id="lnkt_pendingReturnBadge" class="badge badge-pill badge-danger ml-1 <?= $pendingReturnCount > 0 ? '' : 'd-none' ?>"><?= $pendingReturnCount ?></span>
            </a>
        </li>
    </ul>

    <div class="tab-content" id="admin_dashTabContent">
        <div class="tab-pane fade <?= $initTabReturns ? '' : 'show active' ?>" id="tab-overview" role="tabpanel" aria-labelledby="admin_tabOverviewLink">

    <!-- Hàng 4 Card KPI Thực Tế Từ CSDL -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Doanh Thu Hoàn Tất</small>
                        <h4 class="font-weight-bold text-slate mb-1"><?= number_format($completedRevenue, 0, ',', '.') ?> đ</h4>
                        <span class="badge badge-pill badge-mint small badge-wrap-safe">
                            <i class="fa-solid fa-coins mr-1"></i>Đơn hiệu lực: <?= number_format($pipelineRevenue, 0, ',', '.') ?> đ
                        </span>
                    </div>
                    <div class="rounded-circle p-3 bg-peach text-white d-flex align-items-center justify-content-center avatar-56 flex-shrink-0">
                        <i class="fa-solid fa-sack-dollar h4 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Tổng Đơn Hàng</small>
                        <h4 class="font-weight-bold text-slate mb-1"><?= number_format($totalOrders) ?> đơn</h4>
                        <span class="badge badge-pill badge-warning small text-dark badge-wrap-safe">
                            <i class="fa-solid fa-clock mr-1"></i><?= number_format($pendingOrders) ?> chờ duyệt • <?= number_format($completedOrders) ?> hoàn tất
                        </span>
                    </div>
                    <div class="rounded-circle p-3 bg-sky text-dark d-flex align-items-center justify-content-center avatar-56 flex-shrink-0">
                        <i class="fa-solid fa-cart-shopping h4 mb-0 text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Cảnh Báo Tồn Kho (&le; 5)</small>
                        <h4 class="font-weight-bold text-danger mb-1"><span id="admin_lowStockCount"><?= $lowStockCount ?></span> mã SP</h4>
                        <a href="/FashionKids/views/pages/admin-inventory.php" class="small font-weight-bold text-danger">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>Xem danh sách thiếu hàng
                        </a>
                    </div>
                    <div class="rounded-circle p-3 bg-danger text-white d-flex align-items-center justify-content-center avatar-56 flex-shrink-0">
                        <i class="fa-solid fa-box-open h4 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card card-pastel h-100 shadow-sm border-0">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold d-block text-uppercase">Khách Hàng Thành Viên</small>
                        <h4 class="font-weight-bold text-slate mb-1"><?= number_format($totalCustomers) ?> phụ huynh</h4>
                        <span class="badge badge-pill badge-gold small badge-wrap-safe">
                            <i class="fa-solid fa-crown mr-1"></i><?= number_format($totalKolProfiles) ?> hồ sơ KOL Nhí
                        </span>
                    </div>
                    <div class="rounded-circle p-3 badge-gold d-flex align-items-center justify-content-center avatar-56 flex-shrink-0">
                        <i class="fa-solid fa-award h4 mb-0 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 2: Biểu Đồ Doanh Thu 7 Ngày & Cơ Cấu Danh Mục -->
    <div class="row mb-4">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
            <div class="card card-pastel shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <div class="fk-header-title-grow">
                        <h6 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-chart-column text-peach mr-2"></i>Biểu Đồ Doanh Thu 7 Ngày Gần Nhất (VNĐ)
                        </h6>
                        <small class="text-muted-custom">Tổng hợp giá trị đơn hàng hợp lệ phát sinh mỗi ngày</small>
                    </div>
                    <span class="badge badge-pill badge-mint px-3 py-1 font-weight-bold text-nowrap flex-shrink-0">Đồng bộ trực tiếp</span>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="d-flex justify-content-between align-items-end pt-4 pb-2 text-center chart-container-220">
                        <?php $totalDays = count($revenue7Days); ?>
                        <?php foreach ($revenue7Days as $idx => $dayRow):
                            $dayRev    = (float)($dayRow['revenue'] ?? 0);
                            $dayOrders = (int)($dayRow['orders'] ?? 0);
                            $isToday   = ($idx === $totalDays - 1);
                            $isPeak    = ($maxDayRev > 0 && $dayRev >= $maxDayRev);
                            $barHeight = ($maxDayRev > 0 && $dayRev > 0)
                                ? max(22, (int)round(($dayRev / $maxDayRev) * 180))
                                : 10;
                            $barColor  = $isToday ? 'bg-mint-soft' : ($isPeak ? 'bg-peach' : 'bg-sky');
                            $lblClass  = $isToday ? 'text-success' : ($isPeak ? 'text-peach' : 'text-muted-custom');
                        ?>
                            <div class="flex-grow-1 mx-1 d-flex flex-column align-items-center min-w-0">
                                <small class="font-weight-bold <?= $isPeak ? 'text-peach' : 'text-slate' ?> mb-1 text-nowrap">
                                    <?= htmlspecialchars(fkFormatCompactVnd($dayRev)) ?>
                                </small>
                                <div class="w-100 <?= $barColor ?> rounded-top"
                                     title="<?= htmlspecialchars($dayRow['label'] . ': ' . number_format($dayRev, 0, ',', '.') . ' đ (' . $dayOrders . ' đơn)') ?>"
                                     data-fk-height="<?= $barHeight ?>"></div>
                                <small class="<?= $lblClass ?> mt-2 font-weight-bold text-nowrap">
                                    <?= htmlspecialchars($isToday ? ('Hôm nay') : $dayRow['label']) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 small text-muted-custom flex-wrap fk-btn-toolbar">
                        <span class="text-nowrap"><i class="fa-solid fa-circle text-sky mr-1"></i>Ngày trong tuần</span>
                        <span class="text-nowrap"><i class="fa-solid fa-circle text-peach mr-1"></i>Ngày doanh thu cao nhất</span>
                        <span class="text-nowrap"><i class="fa-solid fa-circle text-mint-dark mr-1"></i>Doanh thu hôm nay</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card card-pastel shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-chart-pie text-peach mr-2"></i>Cơ Cấu Doanh Số Danh Mục
                    </h6>
                    <small class="text-muted-custom">4 nhóm sản phẩm sợi tre chính của FashionKids</small>
                </div>
                <div class="card-body p-4 pt-2">
                    <?php foreach ($catBreakdown as $cIdx => $catRow):
                        $catRev = (float)($catRow['revenue'] ?? 0);
                        $catQty = (int)($catRow['qty'] ?? 0);
                        $pct    = ($totalCatRev > 0) ? (int)round(($catRev / $totalCatRev) * 100) : 0;
                        $styleMeta = $catColors[$cIdx % count($catColors)];
                    ?>
                        <div class="mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-1 small font-weight-bold">
                                <span class="min-w-0"><i class="fa-solid <?= $styleMeta['icon'] ?> mr-2"></i><?= htmlspecialchars((string)$catRow['ten_danh_muc']) ?></span>
                                <span class="text-slate text-nowrap flex-shrink-0"><?= $pct ?>% (<?= htmlspecialchars(fkFormatCompactVnd($catRev)) ?> • <?= $catQty ?> SP)</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar <?= $styleMeta['bar'] ?>" role="progressbar" data-fk-width="<?= $pct ?>"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="p-3 rounded bg-milk-cream border mt-4 text-center">
                        <small class="text-muted-custom d-block mb-1">Chứng nhận chất liệu an toàn cho bé</small>
                        <h6 class="font-weight-bold text-success mb-0">100% Vải Sợi Tre Chuẩn OEKO-TEX®</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 3: Đơn Hàng Mới & Cảnh Báo Tồn Kho -->
    <div class="row">
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <div class="fk-header-title-grow">
                        <h6 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-receipt text-peach mr-2"></i>Đơn Hàng Mới Nhất Hệ Thống
                        </h6>
                        <small class="text-muted-custom">Theo dõi nhanh dòng tiền và chuyển sang xử lý chi tiết</small>
                    </div>
                    <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3 text-nowrap flex-shrink-0">
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
                                    <th>Thanh Toán</th>
                                    <th class="text-right">Tổng Tiền</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_orders)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted-custom">Chưa có đơn hàng nào được ghi nhận.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($recent_orders as $ord):
                                    $oid       = (int)$ord['ma_don_hang'];
                                    $ocode     = (string)$ord['ma_van_don'];
                                    $stCode    = (string)($ord['trang_thai_don_hang'] ?? 'cho_duyet');
                                    $stMeta    = $ord['status_meta'] ?? [];
                                    $pm        = strtolower((string)($ord['phuong_thuc_thanh_toan'] ?? 'cod'));
                                    $isPaid    = !empty($ord['is_paid']);
                                    $isExpired = !empty($ord['is_payment_expired']);
                                    $totalFmt  = number_format((float)$ord['tong_tien_thanh_toan'], 0, ',', '.') . ' đ';
                                    $addr      = trim(implode(', ', array_filter([$ord['dia_chi_giao_hang'] ?? '', $ord['quan_huyen'] ?? '', $ord['tinh_thanh_pho'] ?? ''])));
                                ?>
                                    <tr>
                                        <td class="font-weight-bold text-slate text-nowrap">#<?= htmlspecialchars($ocode) ?></td>
                                        <td>
                                            <div class="font-weight-bold text-slate"><?= htmlspecialchars((string)$ord['ten_nguoi_nhan']) ?></div>
                                            <small class="text-muted-custom"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars((string)$ord['so_dien_thoai_nhan']) ?></small>
                                        </td>
                                        <td>
                                            <small class="text-slate d-inline-block fk-ellipsis-220" title="<?= htmlspecialchars($addr) ?>">
                                                <?= htmlspecialchars($addr) ?>
                                            </small>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="badge badge-pill <?= $pm === 'vietqr' ? 'badge-sky' : 'badge-light border' ?> small font-weight-bold">
                                                <?= $pm === 'vietqr' ? 'VietQR MB' : 'COD' ?>
                                            </span>
                                            <?php if ($isPaid): ?>
                                                <span class="badge badge-pill badge-mint small">Đã TT • Thu 0 đ</span>
                                            <?php elseif ($stCode === 'da_huy' && $isExpired): ?>
                                                <span class="badge badge-pill badge-danger small">Quá hạn 24h</span>
                                            <?php elseif ($pm === 'vietqr' && $stCode !== 'da_huy'): ?>
                                                <span class="badge badge-pill badge-warning text-dark small">Chờ CK</span>
                                            <?php elseif ($stCode !== 'da_huy'): ?>
                                                <span class="badge badge-pill badge-butter small">Thu tiền mặt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-peach text-nowrap">
                                            <?= htmlspecialchars($totalFmt) ?>
                                            <small class="d-block <?= $isPaid ? 'text-success' : 'text-muted-custom' ?>">
                                                <?= $isPaid ? 'Thu hộ: 0 đ' : ($stCode === 'da_huy' ? 'Đã hủy' : ($pm === 'cod' ? ('Thu COD: ' . $totalFmt) : 'Chờ CK đủ')) ?>
                                            </small>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <span class="badge badge-pill <?= htmlspecialchars((string)($stMeta['badge_class'] ?? 'badge-warning')) ?> px-2 py-1">
                                                <?= htmlspecialchars((string)($stMeta['label'] ?? 'Chờ duyệt')) ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="/FashionKids/views/pages/staff-orders.php?order_id=<?= $oid ?>" class="btn btn-sm btn-pastel-sky font-weight-bold rounded-pill px-2 mr-1">
                                                Duyệt
                                            </a>
                                            <button type="button" onclick="lnktOpenShippingLabelModal(<?= $oid ?>)" class="btn btn-sm btn-light border font-weight-bold rounded-pill px-2" title="In phiếu giao hàng &amp; thu hộ">
                                                <i class="fa-solid fa-barcode text-peach"></i> In tem
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng 2: Cảnh Báo Ngưỡng Tồn Kho Thấp -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <div class="fk-header-title-grow">
                        <h6 class="font-weight-bold text-danger mb-0">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i>Cảnh Báo Tồn Kho (&le; 5 Cái)
                        </h6>
                        <small class="text-muted-custom">Nhập bổ sung nhanh hoặc mở trang kho chi tiết</small>
                    </div>
                    <a href="/FashionKids/views/pages/admin-inventory.php" class="small font-weight-bold text-peach text-nowrap flex-shrink-0">
                        Kho hàng <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div id="admin_dashRestockAlert" class="alert small mx-3 mt-3 mb-0 py-2 d-none" role="alert"></div>
                    <ul class="list-group list-group-flush" id="admin_lowStockList">
                        <?php if (empty($lowStockProducts)): ?>
                            <li class="list-group-item p-4 text-center text-success font-weight-bold small">
                                <i class="fa-solid fa-circle-check mr-1"></i>Tất cả sản phẩm hiện đều còn đủ tồn kho (&gt; 5 sản phẩm).
                            </li>
                        <?php endif; ?>
                        <?php foreach ($lowStockProducts as $lsp):
                            $pid    = (int)$lsp['ma_san_pham'];
                            $skuNum = $pid >= 100 ? ($pid - 100) : $pid;
                            $sku    = 'FK-LBN-' . str_pad((string)$skuNum, 2, '0', STR_PAD_LEFT);
                            $qty    = (int)$lsp['so_luong_ton_kho'];
                        ?>
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center fk-card-header-row" id="dash_lowStockRow_<?= $pid ?>">
                                <div class="pr-2 min-w-0">
                                    <div class="font-weight-bold text-slate small"><?= htmlspecialchars((string)$lsp['ten_san_pham']) ?></div>
                                    <small class="text-muted-custom">
                                        Mã: <strong class="text-slate"><?= htmlspecialchars($sku) ?></strong> • Giá: <?= number_format((float)$lsp['gia_ban'], 0, ',', '.') ?> đ
                                    </small>
                                </div>
                                <div class="text-right text-nowrap flex-shrink-0">
                                    <span id="dash_stockBadge_<?= $pid ?>" class="badge lbn-badge-low-stock font-weight-bold px-2 py-1 d-block mb-1">
                                        Còn <?= $qty ?> cái
                                    </span>
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 small font-weight-bold"
                                            onclick="lbnDashboardQuickRestock(<?= $pid ?>, 10, this)">
                                        <i class="fa-solid fa-plus-circle mr-1"></i>+10 kho
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-outline-danger btn-sm btn-block font-weight-bold rounded-pill">
                        <i class="fa-solid fa-boxes-packing mr-1"></i> Mở Bảng Quản Lý Tồn Kho Chi Tiết
                    </a>
                </div>
            </div>
        </div>
    </div>
        </div>

        <div class="tab-pane fade <?= $initTabReturns ? 'show active' : '' ?>" id="tab-return-requests" role="tabpanel" aria-labelledby="admin_tabReturnsLink">
            <?php require __DIR__ . "/../partials/return-management-panel.php"; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-shipping-label.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
