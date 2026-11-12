<?php
require_once __DIR__ . '/../../core/Rbac.php';
?>
<footer class="mt-5 border-top pt-5 pb-4 bg-white">
    <div class="container">
        <div class="row">
            <!-- Cot 1: Thuong Hieu FashionKids -->
            <div class="col-12 col-md-4 mb-4">
                <h5 class="font-weight-bold text-peach mb-3">
                    <img src="/FashionKids/images/logo-fashionkids.jpg" alt="FashionKids Logo" class="fk-brand-logo-sm mr-2">FashionKids Official
                </h5>
                <p class="text-muted-custom small">
                    Cửa hàng thời trang trẻ em chuyên phân phối các sản phẩm sản xuất từ 100% sợi tre tự nhiên và cotton hữu cơ. Chứng nhận an toàn tuyệt đối cho làn da bé.
                </p>
                <div class="d-flex mt-3">
                    <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-sm btn-light rounded-circle mr-2 text-primary shadow-sm border" title="Cộng đồng Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-sm btn-light rounded-circle mr-2 text-danger shadow-sm border" title="Lookbook Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-sm btn-light rounded-circle text-dark shadow-sm border" title="Kênh TikTok Mẫu Nhí"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Cot 2: Bo Suu Tap -->
            <div class="col-6 col-md-2 mb-4">
                <h6 class="font-weight-bold mb-3 text-slate">Bộ Sưu Tập</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/FashionKids/views/pages/shop-catalog.php?gender=girl" class="text-muted-custom">Đầm Bé Gái</a></li>
                    <li class="mb-2"><a href="/FashionKids/views/pages/shop-catalog.php?gender=boy" class="text-muted-custom">Bộ Thun Bé Trai</a></li>
                    <li class="mb-2"><a href="/FashionKids/views/pages/shop-catalog.php?type=giay_dep" class="text-muted-custom">Sandal Tập Đi</a></li>
                    <li class="mb-2"><a href="/FashionKids/views/pages/shop-catalog.php?tag=organic" class="text-muted-custom">Vải Sợi Tre</a></li>
                </ul>
            </div>

            <!-- Cot 3: Ho Tro Khach Hang -->
            <div class="col-6 col-md-2 mb-4">
                <h6 class="font-weight-bold mb-3 text-slate">Khách Hàng</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Bảng Quy Đổi Size</a></li>
                    <li class="mb-2"><a href="/FashionKids/views/pages/order-tracking.php" class="text-muted-custom">Tra Cứu Vận Đơn</a></li>
                    <li class="mb-2"><a href="/FashionKids/views/pages/kol-casting.php" class="text-muted-custom">Tuyển Đại Sứ Nhí</a></li>
                    <li class="mb-2"><a href="/FashionKids/views/pages/blog-feeds.php" class="text-muted-custom">Tạp Chí &amp; Báo Chí</a></li>
                </ul>
            </div>

            <!-- Cot 4: Tong Dai CSKH -->
            <div class="col-12 col-md-4 mb-4">
                <h6 class="font-weight-bold mb-3 text-slate">Tổng Đài Hỗ Trợ &amp; CSKH Trực Tuyến</h6>
                <div class="p-3 footer-support-box mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                        <div class="min-w-0">
                            <div class="font-weight-bold text-slate h5 mb-0">1900 6868</div>
                            <small class="text-muted-custom">Hỗ trợ phụ huynh từ 8:00 - 21:00 (Cả T7 &amp; CN)</small>
                        </div>
                        <?php if (Rbac::isStaffOrAdmin()): ?>
                            <a href="/FashionKids/views/pages/staff-chat.php" class="btn btn-light btn-sm rounded-pill border font-weight-bold text-peach text-nowrap flex-shrink-0">
                                <i class="fa-solid fa-headset mr-1"></i>Bàn CSKH
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-light btn-sm rounded-pill border font-weight-bold text-peach text-nowrap flex-shrink-0" data-toggle="collapse" data-target="#customerChatWidget">
                                <i class="fa-solid fa-comments mr-1"></i>Chat Ngay
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <small class="text-muted-custom d-block">
                    <i class="fa-solid fa-shield-halved text-success mr-1"></i> Cam kết 100% sợi tre kháng khuẩn thiên nhiên
                </small>
            </div>
        </div>

        <div class="border-top pt-4 mt-2 text-center small text-muted-custom">
            &copy; 2026 FashionKids. Thời trang trẻ em sợi tre kháng khuẩn an toàn cho bé.
        </div>
    </div>
</footer>

<?php
// Nhung Widget Chat toan trang
require_once __DIR__ . "/chat-widget.php";

$appBase = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';
$jsFiles = [
    'main-interactive.js',
    'mvc-ajax-bridge.js',
    'auth-ajax.js',
    'lbn-ajax.js',
    'fk-ui.js',
];
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<?php foreach ($jsFiles as $_jsFile):
    $_jsVer = @filemtime(__DIR__ . '/../../public/js/' . $_jsFile) ?: '20261110';
?>
<script src="<?= $appBase ?>/public/js/<?= $_jsFile ?>?v=<?= $_jsVer ?>"></script>
<?php endforeach; ?>
</body>
</html>
