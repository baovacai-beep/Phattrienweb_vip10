<?php
$page_title = "Quản Trị Banner Slider Carousel & Lịch Chiến Dịch (MH 23)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh sách Slider Banner Carousel toàn trang (đồng bộ với MH 01)
$lnkt_banners_list = [
    [
        'id'           => 1,
        'title'        => 'BST Mùa Hè 2026 – Vải sợi tre làm mát chống nóng dành cho làn da nhạy cảm',
        'subtitle'     => '100% Organic Bamboo kháng khuẩn tự nhiên cho bé từ 0 - 6 tuổi',
        'cta_label'    => 'Khám Phá BST Hè 2026',
        'target_url'   => '/FashionKids/views/pages/shop-catalog.php',
        'start_time'   => '2026-05-01T08:00',
        'end_time'     => '2026-12-31T23:59',
        'display_order'=> 1,
        'theme_class'  => 'lnkt-slide-summer',
        'status'       => 1
    ],
    [
        'id'           => 2,
        'title'        => 'Sự kiện tuyển chọn Siêu sao nhí KOL – Gương mặt đại diện thương hiệu',
        'subtitle'     => 'Tặng học bổng tài năng nhí 10.000.000đ & gói quà tặng trang phục sợi tre',
        'cta_label'    => 'Đăng ký cho bé tham gia ngay »',
        'target_url'   => '/FashionKids/views/pages/kol-casting.php',
        'start_time'   => '2026-10-15T00:00',
        'end_time'     => '2026-11-30T23:59',
        'display_order'=> 2,
        'theme_class'  => 'lnkt-slide-kol',
        'status'       => 1
    ],
    [
        'id'           => 3,
        'title'        => 'Tuần Lễ Đặc Quyền Thẻ VIP – Nhân Đôi Điểm Thưởng Mọi Đơn Hàng',
        'subtitle'     => 'Ưu đãi cộng hưởng giảm thêm đến 15% dành riêng cho Hạng Vàng & Kim Cương',
        'cta_label'    => 'Săn Ưu Đãi VIP Ngay',
        'target_url'   => '/FashionKids/views/pages/profile.php',
        'start_time'   => '2026-12-01T00:00',
        'end_time'     => '2026-12-15T23:59',
        'display_order'=> 3,
        'theme_class'  => 'lnkt-slide-summer',
        'status'       => 0
    ]
];
?>

<div class="container-fluid my-4 px-lg-5" id="lnkt_admin_banners_page">
    <!-- Tiêu đề trang Quản trị Banner (MH 23) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-panorama text-peach mr-2"></i>Quản Trị Banner Slider Carousel Toàn Trang (MH 23)
            </h4>
            <small class="text-muted-custom">
                Thiết lập lịch hẹn giờ bắt đầu/kết thúc chiến dịch truyền thông và cấu hình Target URL điều hướng cho Carousel 4 giây (MH 01)
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="/FashionKids/views/pages/home.php" class="btn btn-pastel-outline btn-sm font-weight-bold mr-2">
                <i class="fa-solid fa-eye mr-1"></i> Xem Trực Tiếp Trên Trang Chủ (MH 01)
            </a>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                <i class="fa-solid fa-arrow-left mr-1"></i> Bàn Làm Việc Đơn Hàng
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI: FORM THÊM / LÊN LỊCH HẸN GIỜ CHIẾN DỊCH BANNER
             ================================================================ -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold mb-0 text-slate">
                        <i class="fa-solid fa-calendar-plus text-peach mr-1"></i> Thiết Lập Chiến Dịch Banner Mới
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formSaveBanner" onsubmit="event.preventDefault(); alert('Đã lưu cấu hình Banner Slider, lịch hẹn giờ chiến dịch và Target URL thành công!');">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-slate">Tiêu đề chiến dịch truyền thông (<span class="text-danger">*</span>):</label>
                            <input type="text" name="banner_title" class="form-control" value="Sự kiện tuyển chọn Siêu sao nhí KOL" placeholder="Nhập tiêu đề hiển thị trên slide..." required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-slate">Thông điệp phụ &amp; Nhãn nút CTA:</label>
                            <input type="text" name="cta_label" class="form-control" value="Đăng ký cho bé tham gia ngay »" placeholder="Ví dụ: Đăng ký cho bé tham gia ngay »" required>
                        </div>

                        <!-- Trường nhập Target URL phục vụ việc điều hướng người dùng khi nhấp vào banner -->
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-slate">
                                <i class="fa-solid fa-link text-peach mr-1"></i> Trường nhập Target URL điều hướng (<span class="text-danger">*</span>):
                            </label>
                            <input type="text" name="target_url" class="form-control font-weight-bold text-primary" value="/FashionKids/views/pages/kol-casting.php" placeholder="/FashionKids/views/pages/kol-casting.php" required>
                            <small class="text-muted-custom">Đường dẫn đích khi phụ huynh nhấp vào nút CTA trên Banner Carousel.</small>
                        </div>

                        <!-- Hỗ trợ thiết lập lịch hẹn giờ bắt đầu / kết thúc cho từng chiến dịch truyền thông -->
                        <div class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="small font-weight-bold text-slate mb-2">
                                <i class="fa-solid fa-clock text-info mr-1"></i> Lịch Hẹn Giờ Bắt Đầu / Kết Thúc Chiến Dịch
                            </div>
                            <div class="form-group mb-2">
                                <label class="small text-muted-custom mb-1 font-weight-bold">Thời điểm bắt đầu hiển thị (Start Time):</label>
                                <input type="datetime-local" name="start_time" class="form-control form-control-sm" value="2026-10-15T00:00" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small text-muted-custom mb-1 font-weight-bold">Thời điểm kết thúc tự động gỡ (End Time):</label>
                                <input type="datetime-local" name="end_time" class="form-control form-control-sm" value="2026-11-30T23:59" required>
                            </div>
                        </div>

                        <div class="form-row mb-4">
                            <div class="col-6">
                                <label class="small font-weight-bold text-slate">Thứ tự ưu tiên Slide:</label>
                                <input type="number" min="1" max="10" name="display_order" class="form-control" value="2" required>
                            </div>
                            <div class="col-6">
                                <label class="small font-weight-bold text-slate">Tốc độ chuyển (ms):</label>
                                <input type="number" class="form-control bg-milk-cream font-weight-bold" value="4000" readonly>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu &amp; Kích Hoạt Chiến Dịch Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI: BẢNG ĐIỀU KHIỂN DANH SÁCH SLIDER CAROUSEL TOÀN TRANG
             ================================================================ -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-0 text-slate">
                        Danh Sách Slider Carousel Toàn Trang (Tự động chuyển mỗi 4 giây)
                    </h6>
                    <span class="badge badge-pill badge-mint px-3 py-1"><?= count($lnkt_banners_list) ?> chiến dịch</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light small">
                                <tr>
                                    <th class="text-center">Thứ Tự</th>
                                    <th>Minh Họa &amp; Nội Dung Chiến Dịch</th>
                                    <th>Target URL Điều Hướng</th>
                                    <th>Lịch Hẹn Giờ Bắt Đầu / Kết Thúc</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_banners_list as $b): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold h6 text-slate">#<?= $b['display_order'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="lnkt-banner-thumb <?= $b['theme_class'] ?> mr-3 border shadow-sm flex-shrink-0">
                                                    <i class="fa-solid fa-image text-slate h4 mb-0"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-slate small mb-1"><?= htmlspecialchars($b['title']) ?></div>
                                                    <span class="badge badge-gold small px-2 py-1">CTA: <?= htmlspecialchars($b['cta_label']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="<?= htmlspecialchars($b['target_url']) ?>" class="small font-weight-bold text-primary d-block">
                                                <i class="fa-solid fa-up-right-from-square mr-1"></i><?= htmlspecialchars($b['target_url']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <span class="text-success font-weight-bold">Bắt đầu:</span>
                                                <?= str_replace('T', ' ', $b['start_time']) ?>
                                            </div>
                                            <div class="small">
                                                <span class="text-danger font-weight-bold">Kết thúc:</span>
                                                <?= str_replace('T', ' ', $b['end_time']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($b['status'] == 1): ?>
                                                <span class="badge badge-mint px-3 py-1 rounded-pill">Đang hiển thị</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-3 py-1 rounded-pill">Hẹn giờ chờ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle mr-1" title="Chỉnh sửa lịch & URL" onclick="alert('Đã nạp chiến dịch Slide #' + '<?= $b['display_order'] ?>' + ' lên khung cấu hình!');">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" title="Tạm dừng / Xóa" onclick="if(confirm('Tạm ẩn banner chiến dịch này khỏi Carousel trang chủ?')) $(this).closest('tr').remove();">
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
        </div>
    </div>
</div>

<?php require_once "../layouts/footer.php"; ?>
