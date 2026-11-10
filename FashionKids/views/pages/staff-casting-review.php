<?php
$page_title = "Xét Duyệt Hồ Sơ Siêu Sao Nhí KOL (MH 17)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";

$rtModel  = new RealtimeContentModel();
$kolList  = $rtModel->getKolApplications();

$statusCounts = [
    'all'           => count($kolList),
    'cho_duyet'     => 0,
    'moi_phong_van' => 0,
    'trung_tuyen'   => 0,
    'tu_choi'       => 0
];
foreach ($kolList as $k) {
    if (isset($statusCounts[$k['status']])) {
        $statusCounts[$k['status']]++;
    }
}
?>

<div class="container-fluid my-4 px-lg-5" id="fk_staff_casting_review_section">
    <!-- Header Xét Duyệt Hồ Sơ Siêu Sao Nhí KOL (MH 17) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-crown text-peach mr-2"></i>Xét Duyệt Hồ Sơ Tuyển Chọn Siêu Sao Nhí KOL (MH 17)
            </h3>
            <small class="text-muted-custom">
                Đảm nhiệm: Lê Nguyễn Khánh Trình (<code>lnkt_dang_ky_kol_nhi</code>, Stored Procedure <code>sp_lnkt_xet_duyet_kol_nhi</code>) — Phân loại độ tuổi &amp; hẹn lịch chụp Lookbook Studio
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3 mr-2">
                <i class="fa-solid fa-id-card-clip mr-1"></i> Cổng Đăng Ký KOL (MH 16)
            </a>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Bàn Làm Việc Admin
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3">
                    <i class="fa-solid fa-boxes-packing mr-1"></i> Bàn Duyệt Đơn (MH 10)
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thanh Điều Hướng Quản Trị Hệ Thống -->
    <div class="card card-pastel border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap justify-content-start align-items-center">
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                    <i class="fa-solid fa-chart-pie text-peach mr-1"></i> Tổng Quan (MH 12)
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-boxes-stacked text-peach mr-1"></i> Kho Hàng (MH 13)
            </a>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-truck-ramp-box text-warning mr-1"></i> Duyệt Đơn (MH 10)
            </a>
            <a href="/FashionKids/views/pages/staff-chat.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-headset text-danger mr-1"></i> CSKH Realtime (MH 11)
            </a>
            <a href="/FashionKids/views/pages/staff-casting-review.php" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold mr-2 mb-1">
                <i class="fa-solid fa-crown mr-1"></i> Duyệt KOL Nhí (MH 17)
            </a>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                    <i class="fa-solid fa-images text-info mr-1"></i> Lịch Sự Kiện Banner (MH 23)
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mb-1 border">
                <i class="fa-solid fa-newspaper text-primary mr-1"></i> CMS Tạp Chí (MH 21)
            </a>
        </div>
    </div>

    <!-- Thống kê nhanh 4 trạng thái xét duyệt hồ sơ -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-pastel border-0 shadow-sm p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                <div>
                    <small class="text-muted-custom font-weight-bold d-block">Chờ Xét Duyệt</small>
                    <h4 class="font-weight-bold text-warning mb-0" id="fk_kolCount_cho_duyet"><?= $statusCounts['cho_duyet'] ?></h4>
                </div>
                <span class="badge badge-gold p-3 rounded-circle"><i class="fa-solid fa-hourglass-half h5 mb-0"></i></span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-pastel border-0 shadow-sm p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                <div>
                    <small class="text-muted-custom font-weight-bold d-block">Hẹn Thử Đồ Studio</small>
                    <h4 class="font-weight-bold text-info mb-0" id="fk_kolCount_moi_phong_van"><?= $statusCounts['moi_phong_van'] ?></h4>
                </div>
                <span class="badge badge-sky p-3 rounded-circle"><i class="fa-solid fa-camera-retro h5 mb-0"></i></span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-pastel border-0 shadow-sm p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                <div>
                    <small class="text-muted-custom font-weight-bold d-block">Đã Trúng Tuyển KOL</small>
                    <h4 class="font-weight-bold text-success mb-0" id="fk_kolCount_trung_tuyen"><?= $statusCounts['trung_tuyen'] ?></h4>
                </div>
                <span class="badge badge-mint p-3 rounded-circle"><i class="fa-solid fa-trophy h5 mb-0"></i></span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-pastel border-0 shadow-sm p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                <div>
                    <small class="text-muted-custom font-weight-bold d-block">Hồ Sơ Từ Chối</small>
                    <h4 class="font-weight-bold text-danger mb-0" id="fk_kolCount_tu_choi"><?= $statusCounts['tu_choi'] ?></h4>
                </div>
                <span class="badge badge-danger p-3 rounded-circle"><i class="fa-solid fa-ban h5 mb-0"></i></span>
            </div>
        </div>
    </div>

    <!-- Bộ Lọc Nhóm Tuổi & Trạng Thái Hồ Sơ -->
    <div class="card card-pastel border-0 shadow-sm p-3 mb-4 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-wrap align-items-center mb-2 mb-md-0">
                <span class="small font-weight-bold text-slate mr-2"><i class="fa-solid fa-filter text-peach mr-1"></i>Lọc nhóm tuổi:</span>
                <button type="button" class="btn fk-blog-filter-btn active mr-2 mb-1" data-age="all" onclick="fkFilterKolByAge('all', this)">
                    Tất cả hồ sơ (<?= count($kolList) ?>)
                </button>
                <button type="button" class="btn fk-blog-filter-btn mr-2 mb-1" data-age="1_2" onclick="fkFilterKolByAge('1_2', this)">
                    Nhóm 1 - 2 Tuổi
                </button>
                <button type="button" class="btn fk-blog-filter-btn mr-2 mb-1" data-age="3_4" onclick="fkFilterKolByAge('3_4', this)">
                    Nhóm 3 - 4 Tuổi
                </button>
                <button type="button" class="btn fk-blog-filter-btn mb-1" data-age="5_6" onclick="fkFilterKolByAge('5_6', this)">
                    Nhóm 5 - 6 Tuổi
                </button>
                <input type="hidden" id="fk_activeKolAgeGroup" value="all">
            </div>

            <div class="d-flex align-items-center">
                <select id="fk_kolStatusFilterSelect" class="custom-select custom-select-sm rounded-pill font-weight-bold text-slate" onchange="fkApplyKolFilters()">
                    <option value="all">Tất cả trạng thái xét duyệt</option>
                    <option value="cho_duyet">Chờ xét duyệt (cho_duyet)</option>
                    <option value="moi_phong_van">Hẹn thử đồ Studio (moi_phong_van)</option>
                    <option value="trung_tuyen">Đã trúng tuyển (trung_tuyen)</option>
                    <option value="tu_choi">Từ chối (tu_choi)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lưới Hồ Sơ Ứng Tuyển KOL Nhí -->
    <div class="row" id="fk_kolReviewGrid">
        <?php foreach ($kolList as $kol): ?>
            <?php
            $statusBadgeHtml = '<span class="badge badge-gold px-3 py-1 rounded-pill"><i class="fa-solid fa-clock mr-1"></i>Chờ xét duyệt</span>';
            if ($kol['status'] === 'moi_phong_van') {
                $statusBadgeHtml = '<span class="badge badge-sky px-3 py-1 rounded-pill"><i class="fa-solid fa-camera-retro mr-1"></i>Hẹn thử đồ Studio</span>';
            } elseif ($kol['status'] === 'trung_tuyen') {
                $statusBadgeHtml = '<span class="badge badge-mint px-3 py-1 rounded-pill"><i class="fa-solid fa-trophy mr-1"></i>Đã trúng tuyển KOL</span>';
            } elseif ($kol['status'] === 'tu_choi') {
                $statusBadgeHtml = '<span class="badge badge-danger px-3 py-1 rounded-pill"><i class="fa-solid fa-ban mr-1"></i>Từ chối</span>';
            }
            ?>
            <div class="col-12 col-md-6 col-lg-3 mb-4 fk-kol-card-col"
                 id="fk_kolCard_<?= $kol['id'] ?>"
                 data-age="<?= htmlspecialchars($kol['age_group']) ?>"
                 data-status="<?= htmlspecialchars($kol['status']) ?>">
                <div class="card card-pastel fk-casting-card border-0 shadow-sm h-100 overflow-hidden d-flex flex-column bg-white">
                    <div class="position-relative overflow-hidden">
                        <img src="<?= htmlspecialchars($kol['image']) ?>" class="casting-card-img" alt="<?= htmlspecialchars($kol['baby_name']) ?>">
                        <div class="fk-blog-cat-overlay" id="fk_kolStatusBadge_<?= $kol['id'] ?>">
                            <?= $statusBadgeHtml ?>
                        </div>
                    </div>

                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted-custom font-weight-bold">Mã: <code><?= htmlspecialchars($kol['code']) ?></code></small>
                            <span class="badge badge-pill badge-light border text-peach font-weight-bold"><?= htmlspecialchars($kol['age_label']) ?></span>
                        </div>
                        <h6 class="font-weight-bold text-slate mb-1"><?= htmlspecialchars($kol['baby_name']) ?></h6>
                        <p class="small text-muted-custom mb-2">
                            Phụ huynh: <strong><?= htmlspecialchars($kol['guardian_name']) ?></strong> • <i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($kol['phone']) ?>
                        </p>

                        <div class="bg-milk-cream p-2 rounded mb-2 small d-flex justify-content-around text-center border">
                            <div>
                                <span class="text-muted-custom d-block">Chiều cao</span>
                                <strong class="text-slate"><?= $kol['height_cm'] ?> cm</strong>
                            </div>
                            <div class="border-left"></div>
                            <div>
                                <span class="text-muted-custom d-block">Cân nặng</span>
                                <strong class="text-slate"><?= $kol['weight_kg'] ?> kg</strong>
                            </div>
                        </div>

                        <p class="small text-slate mb-3">
                            <i class="fa-solid fa-sparkles text-warning mr-1"></i><?= htmlspecialchars($kol['talent']) ?>
                        </p>

                        <!-- Cụm nút xét duyệt gọi Stored Procedure sp_lnkt_xet_duyet_kol_nhi (Theo RBAC: Staff duyệt Vòng 1, Admin Quyết định) -->
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between">
                            <?php if (Rbac::isAdmin()): ?>
                                <button type="button"
                                        onclick="fkReviewKolApplication(<?= $kol['id'] ?>, 'trung_tuyen', '<?= htmlspecialchars($kol['baby_name'], ENT_QUOTES) ?>')"
                                        class="btn btn-sm btn-pastel-mint font-weight-bold flex-fill mr-1">
                                    <i class="fa-solid fa-check mr-1"></i>Duyệt
                                </button>
                                <button type="button"
                                        onclick="fkReviewKolApplication(<?= $kol['id'] ?>, 'moi_phong_van', '<?= htmlspecialchars($kol['baby_name'], ENT_QUOTES) ?>')"
                                        class="btn btn-sm btn-pastel-sky font-weight-bold flex-fill mx-1">
                                    <i class="fa-solid fa-camera mr-1"></i>Studio
                                </button>
                                <button type="button"
                                        onclick="fkReviewKolApplication(<?= $kol['id'] ?>, 'tu_choi', '<?= htmlspecialchars($kol['baby_name'], ENT_QUOTES) ?>')"
                                        class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold flex-fill ml-1">
                                    <i class="fa-solid fa-xmark mr-1"></i>Loại
                                </button>
                            <?php else: ?>
                                <button type="button"
                                        onclick="fkReviewKolApplication(<?= $kol['id'] ?>, 'moi_phong_van', '<?= htmlspecialchars($kol['baby_name'], ENT_QUOTES) ?>')"
                                        class="btn btn-sm btn-pastel-sky font-weight-bold btn-block">
                                    <i class="fa-solid fa-camera mr-1"></i>Duyệt Vòng 1: Hẹn Studio (Staff)
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
