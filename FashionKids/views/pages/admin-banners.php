<?php
$page_title = "Quản Trị Banner Slider & Lịch Chiến Dịch";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();

// Danh sách các chiến dịch Banner Carousel toàn trang (đồng bộ trực tiếp với MH 01 home.php)
$lnkt_banners_list = [
    [
        'id'          => 1,
        'code'        => 'BN-2026-01',
        'title'       => 'BST Mùa Hè 2026 – Vải sợi tre làm mát chống nóng dành cho làn da nhạy cảm',
        'subtitle'    => 'Công nghệ dệt sợi tre tự nhiên kháng khuẩn 99.8%, hạ nhiệt bề mặt da bé -2°C.',
        'cta_label'   => 'Khám Phá BST Hè 2026 »',
        'cta_color'   => '#FF9E93',
        'target_url'  => '/FashionKids/views/pages/shop-catalog.php',
        'start_time'  => '2026-05-01T00:00',
        'end_time'    => '2026-08-31T23:59',
        'sort_order'  => 1,
        'interval_ms' => 4000,
        'status'      => 'active',
        'icon'        => 'fa-sun text-warning'
    ],
    [
        'id'          => 2,
        'code'        => 'BN-2026-02',
        'title'       => 'Sự kiện tuyển chọn Siêu sao nhí KOL FashionKids 2026',
        'subtitle'    => 'Cơ hội tỏa sáng trên Lookbook Mùa Hè, nhận gói tài trợ trang phục 15.000.000đ.',
        'cta_label'   => 'Đăng ký cho bé tham gia ngay »',
        'cta_color'   => '#FF9E93',
        'target_url'  => '/FashionKids/views/pages/kol-casting.php',
        'start_time'  => '2026-05-15T08:00',
        'end_time'    => '2026-07-30T22:00',
        'sort_order'  => 2,
        'interval_ms' => 4000,
        'status'      => 'active',
        'icon'        => 'fa-crown text-peach'
    ],
    [
        'id'          => 3,
        'code'        => 'BN-2026-03',
        'title'       => 'Tuần Lễ Sandal Tập Đi Đế Cao Su Non & Thước Đo Size Thông Minh',
        'subtitle'    => 'Miễn phí vận chuyển toàn quốc và giảm thêm đến 15% cho chủ thẻ VIP.',
        'cta_label'   => 'Mua Sắm Sandal Tập Đi »',
        'cta_color'   => '#A8DADC',
        'target_url'  => '/FashionKids/views/pages/shop-catalog.php?type=giay_dep',
        'start_time'  => '2026-09-01T00:00',
        'end_time'    => '2026-10-15T23:59',
        'sort_order'  => 3,
        'interval_ms' => 4000,
        'status'      => 'scheduled',
        'icon'        => 'fa-shoe-prints text-info'
    ]
];
?>

<div class="container-fluid my-4 px-lg-5" id="lnkt_admin_banners_section">
    <!-- Header Quản Trị Banner -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-images text-peach mr-2"></i>Bảng Điều Khiển Quản Trị Banner Slider &amp; Lịch Chiến Dịch
            </h4>
            <small class="text-muted-custom">
                Quản lý Banner Trang chủ, lịch hẹn giờ và đường dẫn đích
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="/FashionKids/views/pages/home.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3 mr-2">
                <i class="fa-solid fa-eye mr-1"></i> Xem Carousel Trang Chủ
            </a>
            <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3">
                <i class="fa-solid fa-crown mr-1"></i> Cổng Casting KOL
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (8 CỘT): DANH SÁCH SLIDER CAROUSEL & LỊCH HẸN GIỜ
             ================================================================ -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold text-slate mb-0">
                            Danh Sách Chiến Dịch Slider Carousel Toàn Trang
                        </h6>
                        <small class="text-muted-custom">
                            Tốc độ chuyển cảnh mặc định: <strong>4.000 ms (4 giây / slide)</strong>
                        </small>
                    </div>
                    <button type="button" onclick="lnktResetBannerForm()" class="btn btn-pastel-primary btn-sm px-3 mt-2 mt-sm-0">
                        <i class="fa-solid fa-plus mr-1"></i> Tạo Chiến Dịch Banner Mới
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">Thứ Tự</th>
                                    <th>Thông Điệp Chiến Dịch &amp; Nút Bấm</th>
                                    <th>Đường Dẫn Điều Hướng</th>
                                    <th>Lịch Hẹn Giờ (Bắt Đầu - Kết Thúc)</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_banners_list as $bn): ?>
                                    <tr id="lnkt_bannerRow_<?= $bn['id'] ?>">
                                        <td class="text-center">
                                            <span class="badge badge-pill badge-light border font-weight-bold px-2 py-1">
                                                Slide #<?= $bn['sort_order'] ?>
                                            </span>
                                            <div class="small text-muted-custom mt-1"><?= $bn['interval_ms'] / 1000 ?>s</div>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-start">
                                                <span class="lbn-inv-thumb mr-3 flex-shrink-0">
                                                    <i class="fa-solid <?= $bn['icon'] ?> h4 mb-0"></i>
                                                </span>
                                                <div>
                                                    <div class="font-weight-bold text-slate"><?= htmlspecialchars($bn['title']) ?></div>
                                                    <small class="text-muted-custom d-block mb-1"><?= htmlspecialchars($bn['subtitle']) ?></small>
                                                    <span class="badge px-2 py-1 text-white" data-fk-bg="<?= htmlspecialchars($bn['cta_color']) ?>">
                                                        Nút bấm: <?= htmlspecialchars($bn['cta_label']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="<?= htmlspecialchars($bn['target_url']) ?>" class="small font-weight-bold text-peach text-decoration-none d-block">
                                                <i class="fa-solid fa-up-right-from-square mr-1"></i><strong class="text-slate"><?= htmlspecialchars($bn['target_url']) ?></strong>
                                            </a>
                                        </td>

                                        <td class="small text-nowrap">
                                            <div>
                                                <span class="text-success font-weight-bold">Bắt đầu:</span>
                                                <?= date('d/m/Y H:i', strtotime($bn['start_time'])) ?>
                                            </div>
                                            <div>
                                                <span class="text-danger font-weight-bold">Kết thúc:</span>
                                                <?= date('d/m/Y H:i', strtotime($bn['end_time'])) ?>
                                            </div>
                                        </td>

                                        <td class="text-center" id="lnkt_bannerStatusCell_<?= $bn['id'] ?>">
                                            <?php if ($bn['status'] === 'active'): ?>
                                                <span class="badge badge-mint px-2 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-play mr-1"></i>Đang chạy
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-gold px-2 py-1 rounded-pill">
                                                    <i class="fa-solid fa-calendar-clock mr-1"></i>Hẹn giờ
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center text-nowrap">
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm rounded-pill px-2 mr-1"
                                                    data-id="<?= $bn['id'] ?>"
                                                    data-title="<?= htmlspecialchars($bn['title']) ?>"
                                                    data-subtitle="<?= htmlspecialchars($bn['subtitle']) ?>"
                                                    data-cta="<?= htmlspecialchars($bn['cta_label']) ?>"
                                                    data-url="<?= htmlspecialchars($bn['target_url']) ?>"
                                                    data-start="<?= htmlspecialchars($bn['start_time']) ?>"
                                                    data-end="<?= htmlspecialchars($bn['end_time']) ?>"
                                                    data-order="<?= $bn['sort_order'] ?>"
                                                    data-status="<?= $bn['status'] ?>"
                                                    onclick="lnktEditBannerRow(this)">
                                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm rounded-circle"
                                                    title="Bật / Tắt hiển thị Slide"
                                                    onclick="lnktToggleBannerStatus(<?= $bn['id'] ?>)">
                                                <i class="fa-solid fa-power-off"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Khung Xem Trước Trực Tiếp (Live Preview Banner) -->
            <div class="card card-pastel p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-desktop text-peach mr-2"></i>Xem Trước Giao Diện Hiển Thị Banner Trực Quan
                    </h6>
                    <span class="badge badge-pill badge-sky px-3 py-1">Chuyển ảnh tự động 4 giây</span>
                </div>
                <div class="lnkt-hero-slide lnkt-slide-kol p-4 rounded border">
                    <span class="badge badge-gold px-3 py-1 rounded-pill mb-2">Xem trước Chiến dịch</span>
                    <h4 class="font-weight-bold text-slate mb-2" id="lnkt_previewBannerTitle">
                        Sự kiện tuyển chọn Siêu sao nhí KOL FashionKids 2026
                    </h4>
                    <p class="small text-muted-custom mb-3" id="lnkt_previewBannerSubtitle">
                        Cơ hội tỏa sáng trên Lookbook Mùa Hè, nhận gói tài trợ trang phục 15.000.000đ.
                    </p>
                    <a href="/FashionKids/views/pages/kol-casting.php" id="lnkt_previewBannerCta"
                       class="btn lnkt-btn-cta-peach px-4 py-2 shadow-sm">
                        Đăng ký cho bé tham gia ngay &raquo;
                    </a>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (4 CỘT): FORM THIẾT LẬP LỊCH HẸN GIỜ & TARGET URL
             ================================================================ -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm" id="lnkt_bannerFormCard">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate mb-0" id="lnkt_bannerFormHeader">
                        <i class="fa-solid fa-calendar-check text-peach mr-1"></i> Thiết Lập Chiến Dịch Banner
                    </h6>
                    <button type="button" id="lnkt_btnCancelBannerEdit" onclick="lnktResetBannerForm()"
                            class="btn btn-light btn-sm rounded-pill border d-none">
                        Hủy sửa
                    </button>
                </div>

                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formAdminBanner">
                        <input type="hidden" id="lnkt_bnId" value="">

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lnkt_bnTitle">
                                Tiêu đề chiến dịch truyền thông (<span class="text-danger">*</span>):
                            </label>
                            <input type="text" id="lnkt_bnTitle" class="form-control form-control-sm"
                                   value="Sự kiện tuyển chọn Siêu sao nhí KOL FashionKids 2026"
                                   onkeyup="lnktUpdateBannerPreview()" required>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lnkt_bnSubtitle">
                                Nội dung mô tả ngắn:
                            </label>
                            <textarea id="lnkt_bnSubtitle" rows="2" class="form-control form-control-sm"
                                      onkeyup="lnktUpdateBannerPreview()">Cơ hội tỏa sáng trên Lookbook Mùa Hè, nhận gói tài trợ trang phục 15.000.000đ.</textarea>
                        </div>

                        <!-- Trường nhập Target URL điều hướng -->
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lnkt_bnTargetUrl">
                                Đường dẫn điều hướng (<span class="text-danger">*</span>):
                            </label>
                            <div class="input-group input-group-sm mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-link text-peach"></i></span>
                                </div>
                                <input type="text" id="lnkt_bnTargetUrl" class="form-control font-weight-bold text-peach"
                                       value="/FashionKids/views/pages/kol-casting.php"
                                       onkeyup="lnktUpdateBannerPreview()" required>
                            </div>
                            <div class="d-flex flex-wrap">
                                <button type="button" onclick="lnktSetQuickTargetUrl('/FashionKids/views/pages/kol-casting.php')"
                                        class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">
                                    Cổng Casting KOL
                                </button>
                                <button type="button" onclick="lnktSetQuickTargetUrl('/FashionKids/views/pages/shop-catalog.php')"
                                        class="btn btn-light btn-sm border rounded-pill mb-1 py-0 px-2 small">
                                    Cửa Hàng BST Hè
                                </button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-7">
                                <label class="small font-weight-bold text-slate mb-1" for="lnkt_bnCtaLabel">
                                    Nhãn nút bấm:
                                </label>
                                <input type="text" id="lnkt_bnCtaLabel" class="form-control form-control-sm"
                                       value="Đăng ký cho bé tham gia ngay »"
                                       onkeyup="lnktUpdateBannerPreview()" required>
                            </div>
                            <div class="form-group col-5">
                                <label class="small font-weight-bold text-slate mb-1" for="lnkt_bnSortOrder">
                                    Thứ tự Slide:
                                </label>
                                <input type="number" id="lnkt_bnSortOrder" class="form-control form-control-sm"
                                       value="2" min="1" max="10" required>
                            </div>
                        </div>

                        <!-- Thiết lập lịch hẹn giờ bắt đầu / kết thúc chiến dịch -->
                        <div class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="small font-weight-bold text-slate mb-2">
                                <i class="fa-solid fa-clock text-peach mr-1"></i>Lịch Hẹn Giờ Tự Động Bật / Tắt Banner
                            </div>
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold text-success mb-1" for="lnkt_bnStartTime">
                                    Thời gian bắt đầu hiển thị:
                                </label>
                                <input type="datetime-local" id="lnkt_bnStartTime" class="form-control form-control-sm"
                                       value="2026-05-15T08:00" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-danger mb-1" for="lnkt_bnEndTime">
                                    Thời gian kết thúc chiến dịch:
                                </label>
                                <input type="datetime-local" id="lnkt_bnEndTime" class="form-control form-control-sm"
                                       value="2026-07-30T22:00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lnkt_bnStatus">
                                Trạng thái kích hoạt:
                            </label>
                            <select id="lnkt_bnStatus" class="custom-select form-control-sm">
                                <option value="active">Đang hiển thị trên Trang chủ (Active)</option>
                                <option value="scheduled">Chờ đến lịch hẹn giờ (Scheduled)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Cấu Hình Chiến Dịch Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>

