
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

                <!-- Cum Tai Khoan: Phan biet Chua dang nhap vs Da dang nhap -->
                <li class="nav-item my-2 my-lg-0">
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle rounded-pill shadow-sm py-2 px-3 border" type="button" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-circle-user mr-1 text-peach"></i>
                                <?= htmlspecialchars($_SESSION['user']['ho_va_ten']) ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom py-2 mt-2" aria-labelledby="userMenuDropdown">
                                <a class="dropdown-item py-2" href="/FashionKids/views/pages/profile.php">
                                    <i class="fa-solid fa-id-badge mr-2 text-muted"></i>Hồ sơ VIP & Điểm thưởng
                                </a>
                                <a class="dropdown-item py-2" href="/FashionKids/views/pages/order-tracking.php">
                                    <i class="fa-solid fa-truck-fast mr-2 text-muted"></i>Đơn hàng của tôi
                                </a>
                                <?php if (isset($_SESSION['user']['vai_tro']) && in_array($_SESSION['user']['vai_tro'], ['quan_tri', 'nhan_vien'])): ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item py-2 text-primary font-weight-bold" href="/FashionKids/views/pages/<?= $_SESSION['user']['vai_tro'] === 'quan_tri' ? 'admin-dashboard.php' : 'staff-orders.php' ?>">
                                        <i class="fa-solid fa-gauge-high mr-2"></i>Bàn Làm Việc Admin
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item py-2 text-danger font-weight-bold" href="/FashionKids/controllers/AuthController.php?action=logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Đăng xuất
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/FashionKids/views/pages/login-register.php" class="btn btn-pastel-primary btn-sm px-4 rounded-pill">
                            Đăng Nhập
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>