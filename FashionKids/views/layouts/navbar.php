<?php
require_once __DIR__ . '/../../core/Rbac.php';
Rbac::syncSession();
if (Rbac::isLoggedIn() && Rbac::currentUserId()) {
    require_once __DIR__ . '/../../models/UserModel.php';
    (new UserModel())->dongBoSessionNguoiDung(Rbac::currentUserId());
}
$currentRole = Rbac::currentRole();
$roleBadgeClass = match ($currentRole) {
    'quan_tri'   => 'badge-danger',
    'nhan_vien'  => 'badge-info',
    'khach_hang' => 'badge-success',
    default      => 'badge-secondary',
};
$roleShortLabel = match ($currentRole) {
    'quan_tri'   => 'Quản trị',
    'nhan_vien'  => 'Nhân viên',
    'khach_hang' => 'Thành viên',
    default      => 'Khách',
};
$cartTotalQty = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $_cartItem) {
        $cartTotalQty += max(1, (int)($_cartItem['quantity'] ?? $_cartItem['so_luong'] ?? 1));
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top shadow-sm py-2">
    <div class="container fk-navbar-container">
        <!-- Logo Thương Hiệu -->
        <a class="navbar-brand brand-logo d-flex align-items-center flex-shrink-0 mr-2 mr-xl-3" href="/FashionKids/views/pages/home.php">
            <img src="/FashionKids/images/logo-fashionkids.jpg" alt="FashionKids Logo" class="fk-brand-logo mr-2"><span>Fashion<span class="text-slate">Kids</span></span>
        </a>

        <!-- Nút Hamburger Menu Chuẩn Bootstrap cho Mobile -->
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarFashionKids" aria-controls="navbarFashionKids" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarFashionKids">
            <!-- Thanh Tìm Kiếm Co Giãn (Không dùng form-inline để không bị xuống dòng/vỡ nút trên màn hình hẹp) -->
            <form class="my-2 my-lg-0 mx-lg-2 fk-nav-search px-0" action="/FashionKids/views/pages/shop-catalog.php" method="GET">
                <div class="input-group fk-search-group w-100">
                    <input class="form-control search-input-custom" type="search" name="keyword" placeholder="Tìm đầm bé gái, bộ thun bé trai, sandal..." aria-label="Search">
                    <div class="input-group-append fk-search-append">
                        <button class="btn search-btn-custom" type="submit" aria-label="Tìm kiếm">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Menu Điều Hướng Chính -->
            <ul class="navbar-nav ml-auto align-items-lg-center flex-shrink-0">
                <li class="nav-item">
                    <a class="nav-link fk-nav-link font-weight-bold px-2 text-slate" href="/FashionKids/views/pages/shop-catalog.php">Cửa Hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fk-nav-link font-weight-bold px-2 text-peach" href="/FashionKids/views/pages/kol-casting.php">
                        <i class="fa-solid fa-crown mr-1"></i>Tuyển KOL Nhí
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fk-nav-link font-weight-bold px-2 text-slate" href="/FashionKids/views/pages/blog-feeds.php">Tạp Chí</a>
                </li>
                
                <!-- Tiện Ích Giỏ Hàng -->
                <li class="nav-item mx-lg-1 mx-xl-2 my-2 my-lg-0">
                    <a href="/FashionKids/views/pages/checkout.php" class="btn btn-light fk-btn-cart position-relative rounded-pill px-3 py-2 shadow-sm bg-milk-cream border d-inline-flex align-items-center">
                        <i class="fa-solid fa-basket-shopping text-slate mr-1"></i>
                        <span class="small font-weight-bold">Giỏ Hàng</span>
                        <span class="badge badge-pill cart-badge-custom ml-1">
                            <?= $cartTotalQty ?>
                        </span>
                    </a>
                </li>

                <!-- Cụm Tài Khoản -->
                <li class="nav-item my-2 my-lg-0">
                    <?php if (isset($_SESSION['user'])): ?>
                        <?php $vipDiscount = (float)($_SESSION['user']['phan_tram_chiet_khau'] ?? $_SESSION['user']['phan_tram_giam_gia'] ?? 0); ?>
                        <div class="dropdown">
                            <button class="btn btn-light fk-btn-user dropdown-toggle rounded-pill shadow-sm py-2 px-3 border d-inline-flex align-items-center" type="button" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-circle-user mr-1 text-peach"></i>
                                <span class="font-weight-bold text-slate fk-user-name-truncate mr-1"><?= htmlspecialchars($_SESSION['user']['ho_va_ten']) ?></span>
                                <?php if (in_array($currentRole, ['quan_tri', 'nhan_vien'], true)): ?>
                                    <span class="badge <?= $roleBadgeClass ?> badge-pill small flex-shrink-0"><?= htmlspecialchars($roleShortLabel) ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom py-2 mt-2" aria-labelledby="userMenuDropdown">
                                <div class="px-3 py-2 border-bottom bg-milk-cream">
                                    <div class="small text-muted-custom">Tài khoản đang hoạt động:</div>
                                    <div class="font-weight-bold text-slate small"><?= htmlspecialchars(Rbac::roleLabel($currentRole)) ?></div>
                                    <?php if ($currentRole === 'khach_hang' && !empty($_SESSION['user']['ten_hang'])): ?>
                                        <span class="badge badge-warning badge-pill mt-1">
                                            <i class="fa-solid fa-gem mr-1"></i><?= htmlspecialchars($_SESSION['user']['ten_hang']) ?><?= $vipDiscount > 0 ? ' (-' . $vipDiscount . '%)' : '' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a class="dropdown-item py-2" href="/FashionKids/views/pages/profile.php">
                                    <i class="fa-solid fa-id-badge mr-2 text-peach"></i>Hồ sơ thành viên &amp; Ưu đãi VIP
                                </a>
                                <a class="dropdown-item py-2" href="/FashionKids/views/pages/order-tracking.php">
                                    <i class="fa-solid fa-truck-fast mr-2 text-info"></i>Tra cứu &amp; Lịch sử đơn hàng
                                </a>

                                <?php if (Rbac::isStaff()): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header text-info font-weight-bold">
                                        <i class="fa-solid fa-user-tie mr-1"></i>Nghiệp Vụ Nhân Viên
                                    </h6>
                                    <a class="dropdown-item py-2 font-weight-bold text-slate" href="/FashionKids/views/pages/staff-orders.php">
                                        <i class="fa-solid fa-boxes-packing mr-2 text-warning"></i>Xử lý đơn hàng
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/staff-support.php">
                                        <i class="fa-solid fa-headset mr-2 text-danger"></i>CSKH Trực tuyến
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/staff-casting-review.php">
                                        <i class="fa-solid fa-crown mr-2 text-warning"></i>Sơ tuyển KOL Nhí
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-inventory.php">
                                        <i class="fa-solid fa-warehouse mr-2 text-success"></i>Nhập kho hàng
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-cms-blog.php">
                                        <i class="fa-solid fa-pen-nib mr-2 text-info"></i>Biên tập Tạp chí
                                    </a>
                                <?php endif; ?>

                                <?php if (Rbac::isAdmin()): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header text-danger font-weight-bold">
                                        <i class="fa-solid fa-shield-halved mr-1"></i>Quản Trị Hệ Thống
                                    </h6>
                                    <a class="dropdown-item py-2 text-primary font-weight-bold" href="/FashionKids/views/pages/admin-dashboard.php">
                                        <i class="fa-solid fa-gauge-high mr-2"></i>Tổng quan hệ thống
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-users.php">
                                        <i class="fa-solid fa-users-gear mr-2 text-info"></i>Quản lý Thành viên &amp; Hạng VIP
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-inventory.php">
                                        <i class="fa-solid fa-boxes-stacked mr-2 text-success"></i>Quản lý Kho &amp; Sản phẩm
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-size-charts.php">
                                        <i class="fa-solid fa-ruler-combined mr-2 text-warning"></i>Cấu hình Bảng Size
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-banners.php">
                                        <i class="fa-solid fa-panorama mr-2 text-peach"></i>Quản lý Banner Sự kiện
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-cms-blog.php">
                                        <i class="fa-solid fa-newspaper mr-2 text-primary"></i>Quản lý Tạp chí &amp; Báo chí
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/staff-orders.php">
                                        <i class="fa-solid fa-clipboard-check mr-2 text-secondary"></i>Điều phối Đơn hàng &amp; CSKH
                                    </a>
                                <?php endif; ?>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item py-2 text-danger font-weight-bold" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Đăng xuất
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/FashionKids/views/pages/login-register.php" class="btn btn-pastel-primary btn-sm px-4 py-2 rounded-pill font-weight-bold shadow-sm text-nowrap flex-shrink-0">
                            <i class="fa-solid fa-right-to-bracket mr-1"></i>Đăng Nhập
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
if ((isset($fk_rbac_access_granted) && !$fk_rbac_access_granted) || !Rbac::enforceCurrentViewAccess()):
    $blockedPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $allowedRolesForPage = Rbac::PAGE_PERMISSIONS[$blockedPage] ?? [];
    $allowedNames = array_map(fn($r) => Rbac::roleLabel($r), $allowedRolesForPage);
?>
<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-md-5 text-center fk-rbac-denied-card">
                    <div class="mb-3">
                        <span class="badge badge-danger badge-pill px-3 py-2 font-weight-bold">
                            <i class="fa-solid fa-shield-halved mr-1"></i>KHU VỰC GIỚI HẠN QUYỀN TRUY CẬP
                        </span>
                    </div>
                    <h3 class="font-weight-bold text-slate mb-2">Bạn Không Có Quyền Truy Cập Trang Này</h3>
                    <p class="text-muted-custom mb-4">
                        Tài khoản hiện tại của bạn đang ở vai trò <strong><?= htmlspecialchars(Rbac::roleLabel($currentRole)) ?></strong> nên không thể truy cập khu vực quản lý nội bộ.
                    </p>

                    <div class="p-3 bg-milk-cream rounded mb-4 text-left border">
                        <div class="small font-weight-bold text-slate mb-1">
                            <i class="fa-solid fa-lock text-peach mr-1"></i>Yêu cầu quyền truy cập:
                        </div>
                        <div class="small text-muted-custom mb-0">
                            Trang này chỉ dành cho: <strong><?= htmlspecialchars(implode(' / ', $allowedNames)) ?></strong>.
                        </div>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-center flex-wrap fk-btn-toolbar">
                        <a href="/FashionKids/views/pages/home.php" class="btn btn-light border rounded-pill px-4 font-weight-bold text-slate text-nowrap">
                            <i class="fa-solid fa-house mr-1"></i>Về Trang Chủ
                        </a>
                        <a href="/FashionKids/views/pages/login-register.php" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold text-nowrap">
                            <i class="fa-solid fa-right-to-bracket mr-1"></i>Đăng Nhập Tài Khoản Khác
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
    require_once __DIR__ . '/footer.php';
    exit;
endif;
?>
