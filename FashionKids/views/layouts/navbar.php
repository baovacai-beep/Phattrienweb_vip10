<?php
require_once __DIR__ . '/../../core/Rbac.php';
Rbac::syncSession();
$currentRole = Rbac::currentRole();
$roleBadgeClass = match ($currentRole) {
    'quan_tri'   => 'badge-danger',
    'nhan_vien'  => 'badge-info',
    'khach_hang' => 'badge-success',
    default      => 'badge-secondary',
};
$roleShortLabel = match ($currentRole) {
    'quan_tri'   => 'Admin',
    'nhan_vien'  => 'Staff',
    'khach_hang' => 'Customer',
    default      => 'Guest',
};
?>
<nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top shadow-sm py-2">
    <div class="container">
        <!-- Logo Thuong Hieu -->
        <a class="navbar-brand brand-logo d-flex align-items-center" href="/FashionKids/views/pages/home.php">
            <img src="/FashionKids/images/logo-fashionkids.jpg" alt="FashionKids Logo" class="fk-brand-logo mr-2"><span>Fashion<span class="text-slate">Kids</span></span>
        </a>

        <!-- Nut Hamburger Menu Chuan Bootstrap cho Mobile -->
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarFashionKids" aria-controls="navbarFashionKids" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarFashionKids">
            <!-- Thanh Tim Kiem Co Gian: Chiem 5 cot tren Desktop, Co vao Menu tren Mobile -->
            <form class="form-inline my-3 my-lg-0 mx-auto col-12 col-lg-5 px-0" action="/FashionKids/views/pages/shop-catalog.php" method="GET">
                <div class="input-group w-100">
                    <input class="form-control search-input-custom" type="search" name="keyword" placeholder="Tìm đầm bé gái, bộ thun bé trai, sandal..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary search-btn-custom px-3" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Menu Dieu Huong Chinh -->
            <ul class="navbar-nav ml-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 text-slate" href="/FashionKids/views/pages/shop-catalog.php">Cửa Hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 text-peach" href="/FashionKids/views/pages/kol-casting.php">
                        <i class="fa-solid fa-crown mr-1"></i>Tuyển KOL Nhí
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 text-slate" href="/FashionKids/views/pages/blog-feeds.php">Tạp Chí</a>
                </li>
                
                <!-- Tien Ich Gio Hang -->
                <li class="nav-item mx-lg-2 my-2 my-lg-0">
                    <a href="/FashionKids/views/pages/checkout.php" class="btn btn-light position-relative rounded-pill px-3 py-2 shadow-sm bg-milk-cream border">
                        <i class="fa-solid fa-basket-shopping text-slate mr-1"></i>
                        <span class="small font-weight-bold">Giỏ Hàng</span>
                        <span class="badge badge-pill cart-badge-custom ml-1">
                            <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                        </span>
                    </a>
                </li>

                <!-- Cum Tai Khoan & Phan Luong RBAC 4 Vai Tro -->
                <li class="nav-item my-2 my-lg-0">
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle rounded-pill shadow-sm py-2 px-3 border d-flex align-items-center" type="button" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-circle-user mr-1 text-peach"></i>
                                <span class="font-weight-bold text-slate mr-1"><?= htmlspecialchars($_SESSION['user']['ho_va_ten']) ?></span>
                                <span class="badge <?= $roleBadgeClass ?> badge-pill small"><?= htmlspecialchars($roleShortLabel) ?></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom py-2 mt-2" aria-labelledby="userMenuDropdown">
                                <div class="px-3 py-2 border-bottom bg-milk-cream">
                                    <div class="small text-muted-custom">Định danh phiên RBAC:</div>
                                    <div class="font-weight-bold text-slate small"><?= htmlspecialchars(Rbac::roleLabel($currentRole)) ?></div>
                                    <?php if ($currentRole === 'khach_hang' && !empty($_SESSION['user']['ten_hang'])): ?>
                                        <span class="badge badge-warning badge-pill mt-1">
                                            <i class="fa-solid fa-gem mr-1"></i><?= htmlspecialchars($_SESSION['user']['ten_hang']) ?> (-<?= (float)($_SESSION['user']['phan_tram_chiet_khau'] ?? 0) ?>%)
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a class="dropdown-item py-2" href="/FashionKids/views/pages/profile.php">
                                    <i class="fa-solid fa-id-badge mr-2 text-peach"></i>Hồ sơ VIP &amp; Điểm thưởng (MH 09)
                                </a>
                                <a class="dropdown-item py-2" href="/FashionKids/views/pages/order-tracking.php">
                                    <i class="fa-solid fa-truck-fast mr-2 text-info"></i>Tra cứu &amp; Lịch sử đơn hàng (MH 07)
                                </a>

                                <?php if (Rbac::isStaff()): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header text-info font-weight-bold">
                                        <i class="fa-solid fa-user-tie mr-1"></i>Phân Hệ Nhân Viên Vận Hành
                                    </h6>
                                    <a class="dropdown-item py-2 font-weight-bold text-slate" href="/FashionKids/views/pages/staff-orders.php">
                                        <i class="fa-solid fa-boxes-packing mr-2 text-warning"></i>Xử lý đơn hàng (MH 10)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/staff-chat.php">
                                        <i class="fa-solid fa-headset mr-2 text-danger"></i>CSKH Trực tuyến (MH 11)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/staff-casting-review.php">
                                        <i class="fa-solid fa-crown mr-2 text-warning"></i>Sơ tuyển KOL Nhí Vòng 1 (MH 17)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-inventory.php">
                                        <i class="fa-solid fa-warehouse mr-2 text-success"></i>Nhập thêm kho hàng (MH 13)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-cms-blog.php">
                                        <i class="fa-solid fa-pen-nib mr-2 text-info"></i>Soạn bản nháp Tạp chí (MH 21)
                                    </a>
                                <?php endif; ?>

                                <?php if (Rbac::isAdmin()): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header text-danger font-weight-bold">
                                        <i class="fa-solid fa-shield-halved mr-1"></i>Quản Trị Viên Toàn Quyền (Admin)
                                    </h6>
                                    <a class="dropdown-item py-2 text-primary font-weight-bold" href="/FashionKids/views/pages/admin-dashboard.php">
                                        <i class="fa-solid fa-gauge-high mr-2"></i>Bàn Làm Việc Admin (MH 12)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-users.php">
                                        <i class="fa-solid fa-users-gear mr-2 text-info"></i>Quản trị Người dùng &amp; VIP (MH 15)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-inventory.php">
                                        <i class="fa-solid fa-boxes-stacked mr-2 text-success"></i>Quản trị Kho &amp; Sản phẩm (MH 13)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-size-charts.php">
                                        <i class="fa-solid fa-ruler-combined mr-2 text-warning"></i>Cấu hình Bảng Size (MH 14)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-banners.php">
                                        <i class="fa-solid fa-panorama mr-2 text-peach"></i>Quản trị Banner Sự kiện (MH 23)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/admin-cms-blog.php">
                                        <i class="fa-solid fa-newspaper mr-2 text-primary"></i>Xuất bản Tạp chí &amp; Báo chí (MH 21)
                                    </a>
                                    <a class="dropdown-item py-2 text-slate" href="/FashionKids/views/pages/staff-orders.php">
                                        <i class="fa-solid fa-clipboard-check mr-2 text-secondary"></i>Điều phối Đơn &amp; CSKH (MH 10-11)
                                    </a>
                                <?php endif; ?>

                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header text-muted-custom">
                                    <i class="fa-solid fa-repeat mr-1"></i>Chuyển Nhanh Tài Khoản Mẫu (Demo)
                                </h6>
                                <a class="dropdown-item small py-1" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=me_be_sam">
                                    <span class="badge badge-success mr-1">Customer</span> Mẹ Bé Sam (VIP Vàng -10%)
                                </a>
                                <a class="dropdown-item small py-1" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=staff_tuvan">
                                    <span class="badge badge-info mr-1">Staff</span> Nhân viên CSKH &amp; Kho
                                </a>
                                <a class="dropdown-item small py-1" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=admin_fashion">
                                    <span class="badge badge-danger mr-1">Admin</span> Quản trị viên Hệ thống
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item py-2 text-danger font-weight-bold" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Đăng xuất (Về Guest)
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="btn-group">
                            <a href="/FashionKids/views/pages/login-register.php" class="btn btn-pastel-primary btn-sm px-3 rounded-pill font-weight-bold">
                                <i class="fa-solid fa-right-to-bracket mr-1"></i>Đăng Nhập
                            </a>
                            <button type="button" class="btn btn-light btn-sm rounded-pill border ml-1 dropdown-toggle px-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Chọn nhanh tài khoản mẫu để test 4 luồng phân quyền">
                                <i class="fa-solid fa-key text-peach"></i> TK Mẫu
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom py-2 mt-2">
                                <h6 class="dropdown-header text-slate font-weight-bold">
                                    <i class="fa-solid fa-user-check mr-1 text-peach"></i>Đăng Nhập Nhanh 4 Luồng RBAC
                                </h6>
                                <a class="dropdown-item small py-2" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=me_be_sam">
                                    <span class="badge badge-success mr-1">Customer</span> <strong>me_be_sam</strong> (Hạng Vàng -10%)
                                </a>
                                <a class="dropdown-item small py-2" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=vip_kim_cuong">
                                    <span class="badge badge-primary mr-1">VIP</span> <strong>vip_kim_cuong</strong> (Kim Cương -15%)
                                </a>
                                <a class="dropdown-item small py-2" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=staff_tuvan">
                                    <span class="badge badge-info mr-1">Staff</span> <strong>staff_tuvan</strong> (Nhân viên Vận hành)
                                </a>
                                <a class="dropdown-item small py-2" href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=admin_fashion">
                                    <span class="badge badge-danger mr-1">Admin</span> <strong>admin_fashion</strong> (Quản trị Toàn quyền)
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small py-1 text-center font-weight-bold text-peach" href="/FashionKids/views/pages/login-register.php">
                                    Xem bảng thông tin mật khẩu chi tiết &rarr;
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
// ============================================================================
// CHỐT CHẶN BẢO MẬT RBAC (ROLE-BASED ACCESS CONTROL GUARD)
// Tự động chặn Khách vãng lai (guest) hoặc Khách hàng (khach_hang) cố tình
// truy cập trực tiếp URL của Nhân viên (staff-*.php) hoặc Quản trị (admin-*.php)
// ============================================================================
if (!Rbac::enforceCurrentViewAccess()):
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
                            <i class="fa-solid fa-shield-halved mr-1"></i>HTTP 403 FORBIDDEN • KIỂM SOÁT TRUY CẬP RBAC
                        </span>
                    </div>
                    <h3 class="font-weight-bold text-slate mb-2">Khu Vực Giới Hạn Quyền Truy Cập</h3>
                    <p class="text-muted-custom mb-4">
                        Tài khoản hiện tại của bạn đang ở luồng <strong><?= htmlspecialchars(Rbac::roleLabel($currentRole)) ?></strong> nên không được phép truy cập phân hệ <code><?= htmlspecialchars($blockedPage) ?></code>.
                    </p>

                    <div class="p-3 bg-milk-cream rounded mb-4 text-left border">
                        <div class="small font-weight-bold text-slate mb-1">
                            <i class="fa-solid fa-lock text-peach mr-1"></i>Yêu cầu phân quyền của trang này:
                        </div>
                        <div class="small text-muted-custom mb-2">
                            Chỉ dành cho: <strong><?= htmlspecialchars(implode(' / ', $allowedNames)) ?></strong>.
                        </div>
                        <div class="small text-muted-custom">
                            <i class="fa-solid fa-circle-info text-info mr-1"></i>Theo Ma trận Phân quyền FashionKids: Khách hàng (<code>khach_hang</code>) và Khách vãng lai (<code>guest</code>) chỉ thao tác trên luồng Mua sắm, Tính Size, Gửi đánh giá, Ứng tuyển KOL và Đọc/Bình luận Tạp chí; tuyệt đối không được chỉnh sửa CMS, Kho hàng hay Đơn hàng hệ thống.
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="small font-weight-bold text-slate mb-2">
                            Chuyển nhanh sang Tài khoản Mẫu có quyền hợp lệ để kiểm thử trang này:
                        </div>
                        <div class="d-flex flex-wrap justify-content-center">
                            <?php if (in_array('quan_tri', $allowedRolesForPage, true)): ?>
                                <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=admin_fashion" class="btn btn-danger rounded-pill px-4 m-1 font-weight-bold shadow-sm">
                                    <i class="fa-solid fa-user-shield mr-1"></i>Đăng nhập mẫu Admin (admin_fashion)
                                </a>
                            <?php endif; ?>
                            <?php if (in_array('nhan_vien', $allowedRolesForPage, true)): ?>
                                <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=staff_tuvan" class="btn btn-info rounded-pill px-4 m-1 font-weight-bold shadow-sm">
                                    <i class="fa-solid fa-user-tie mr-1"></i>Đăng nhập mẫu Nhân viên (staff_tuvan)
                                </a>
                            <?php endif; ?>
                            <?php if (in_array('khach_hang', $allowedRolesForPage, true)): ?>
                                <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=quickSwitchRole&amp;user=me_be_sam" class="btn btn-success rounded-pill px-4 m-1 font-weight-bold shadow-sm">
                                    <i class="fa-solid fa-user-check mr-1"></i>Đăng nhập mẫu Khách VIP (me_be_sam)
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-center flex-wrap">
                        <a href="/FashionKids/views/pages/home.php" class="btn btn-light border rounded-pill px-4 m-1 font-weight-bold text-slate">
                            <i class="fa-solid fa-house mr-1"></i>Về Trang Chủ
                        </a>
                        <a href="/FashionKids/views/pages/login-register.php" class="btn btn-pastel-primary rounded-pill px-4 m-1 font-weight-bold">
                            <i class="fa-solid fa-right-to-bracket mr-1"></i>Trang Đăng Nhập &amp; Bảng TK Mẫu
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
