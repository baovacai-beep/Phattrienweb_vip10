<?php
$page_title = "Quản Trị Nội Dung Tạp Chí & Báo Chí";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();
require_once __DIR__ . "/../../models/RealtimeContentModel.php";

$rtModel  = new RealtimeContentModel();
$articles = $rtModel->getArticles('all', '', 'newest', true);
$comments = $rtModel->getCommentsByArticle(1);
$isAdminUser = Rbac::isAdmin();
?>

<div class="container-fluid my-4 px-lg-5" id="fk_admin_cms_blog_section">
    <!-- Header Bàn Làm Việc CMS Tạp Chí -->
    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-4 pb-2 border-bottom">
        <div class="fk-header-title-grow">
            <h3 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-newspaper text-peach mr-2"></i><?= $isAdminUser ? 'Quản Trị Nội Dung Tạp Chí &amp; Điểm Tin Báo Chí' : 'Bàn Soạn Thảo Bản Nháp Tạp Chí Nhân Viên' ?>
            </h3>
            <small class="text-muted-custom">Phân quyền hiện tại: <strong><?= htmlspecialchars(Rbac::roleLabel()) ?></strong>
            </small>
        </div>
        <div class="d-flex flex-wrap align-items-center fk-btn-toolbar">
            <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                <i class="fa-solid fa-eye mr-1"></i> Xem Trang Tạp Chí
            </a>
            <?php if ($isAdminUser): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Bàn Làm Việc Admin
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                    <i class="fa-solid fa-boxes-packing mr-1"></i> Bàn Nhân Viên
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thanh Điều Hướng Quản Trị Hệ Thống (Lọc theo Vai trò RBAC) -->
    <div class="card card-pastel border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap justify-content-start align-items-center fk-btn-toolbar">
            <?php if ($isAdminUser): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate border text-nowrap">
                    <i class="fa-solid fa-chart-pie text-peach mr-1"></i> Tổng Quan
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate border text-nowrap">
                <i class="fa-solid fa-boxes-stacked text-peach mr-1"></i> Kho Hàng
            </a>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate border text-nowrap">
                <i class="fa-solid fa-truck-ramp-box text-warning mr-1"></i> Duyệt Đơn
            </a>
            <a href="/FashionKids/views/pages/staff-support.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate border text-nowrap">
                <i class="fa-solid fa-headset text-danger mr-1"></i> CSKH Trực Tuyến
            </a>
            <a href="/FashionKids/views/pages/staff-casting-review.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate border text-nowrap">
                <i class="fa-solid fa-crown text-warning mr-1"></i> Duyệt KOL Nhí
            </a>
            <?php if ($isAdminUser): ?>
                <a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate border text-nowrap">
                    <i class="fa-solid fa-images text-info mr-1"></i> Lịch Sự Kiện Banner
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold text-nowrap">
                <i class="fa-solid fa-newspaper mr-1"></i> Quản Lý Tạp Chí
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (5 CỘT): FORM SOẠN THẢO & XUẤT BẢN BÀI VIẾT MỚI REALTIME
             ================================================================ -->
        <div class="col-12 col-xl-5 mb-4">
            <div class="card card-pastel border-0 shadow-sm bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <h6 class="font-weight-bold text-slate mb-0 fk-header-title-grow" id="fk_cmsFormTitle">
                        <i class="fa-solid fa-pen-to-square text-peach mr-2"></i><?= $isAdminUser ? 'Soạn Thảo &amp; Xuất Bản Bài Viết' : 'Soạn Bản Nháp Bài Viết' ?>
                    </h6>
                    <span class="badge badge-pill badge-mint px-2 py-1 text-nowrap flex-shrink-0">Đồng bộ hệ thống</span>
                </div>
                <div class="card-body p-4">
                    <?php if (!$isAdminUser): ?>
                        <div class="alert alert-info small py-2 mb-3">
                            <i class="fa-solid fa-circle-info mr-1"></i><strong>Phân quyền Nhân viên:</strong> Bạn được phép tạo bài viết ở trạng thái <strong>Bản nháp</strong>. Quyền Xuất bản chính thức và Xóa bài viết thuộc về Quản trị viên.
                        </div>
                    <?php endif; ?>
                    <form id="fk_formCmsBlog">
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate" for="fk_cmsTitle">Tiêu đề bài viết / Điểm tin báo chí (<span class="text-danger">*</span>):</label>
                            <input type="text" id="fk_cmsTitle" name="title" class="form-control"
                                   value="Gợi Ý 4 Set Đồ Sợi Tre Đi Học Mầm Non Siêu Thoáng Mát Cho Bé"
                                   placeholder="Nhập tiêu đề bài viết..." required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="fk_cmsCategory">Chuyên mục tạp chí:</label>
                                <select id="fk_cmsCategory" name="category_key" class="custom-select form-control">
                                    <option value="meo_phoi_do">Mẹo phối đồ</option>
                                    <option value="hau_truong">Hậu trường mẫu nhí</option>
                                    <option value="cham_soc_be">Chăm sóc bé</option>
                                    <option value="bao_chi">Báo chí &amp; Sự kiện</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="fk_cmsStatus">Trạng thái xuất bản:</label>
                                <select id="fk_cmsStatus" name="status" class="custom-select form-control">
                                    <?php if ($isAdminUser): ?>
                                        <option value="xuat_ban" selected>Xuất bản ngay</option>
                                        <option value="ban_nhap">Lưu bản nháp</option>
                                    <?php else: ?>
                                        <option value="ban_nhap" selected>Lưu bản nháp</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate" for="fk_cmsImageSelect">Chọn ảnh bìa tạp chí:</label>
                            <select id="fk_cmsImageSelect" name="image" class="custom-select form-control">
                                <option value="/FashionKids/images/magazine/mag-set-do-di-hoc-mam-non.jpg">Set đồ sợi tre đi học mầm non</option>
                                <option value="/FashionKids/images/magazine/mag-phoi-do-du-tiec.jpg">Đầm dự tiệc bé gái Pastel</option>
                                <option value="/FashionKids/images/magazine/mag-studio-kol-nhi.jpg">Hậu trường Studio KOL Nhí</option>
                                <option value="/FashionKids/images/magazine/mag-soi-tre-khang-khuan.jpg">Vải sợi tre kháng khuẩn tự nhiên</option>
                                <option value="/FashionKids/images/magazine/mag-chung-nhan-oekotex.jpg">Chứng nhận quốc tế OEKO-TEX® 100</option>
                                <option value="/FashionKids/images/magazine/mag-chon-sandal-tap-di.jpg">Chọn sandal tập đi chống trượt</option>
                                <option value="/FashionKids/images/magazine/mag-giat-bao-quan-soi-tre.jpg">Bảo quản đồ sợi tre bền đẹp</option>
                                <option value="/FashionKids/images/magazine/mag-chong-nang-uv-mua-he.jpg">Chống nắng UV mùa hè cho bé</option>
                                <option value="/FashionKids/images/magazine/mag-catwalk-tuan-le-thoi-trang.jpg">Catwalk Tuần lễ Thời trang Nhí</option>
                                <option value="/FashionKids/images/magazine/mag-phong-ngua-rom-say-be.jpg">Phòng ngừa rôm sảy mùa hè cho bé</option>
                                <option value="/FashionKids/images/magazine/mag-phoi-do-anh-em-unisex.jpg">Phối đồ đôi Anh Em Pastel</option>
                                <option value="/FashionKids/images/magazine/mag-bao-chi-thuong-hieu-xanh.jpg">Báo chí Thương hiệu Thời trang Xanh</option>
                                <option value="/FashionKids/images/magazine/mag-bi-quyet-anh-casting-kol.jpg">Mẹo chụp ảnh Casting KOL Nhí</option>
                                <option value="/FashionKids/images/magazine/mag-phu-lieu-cuc-go-an-toan.jpg">Phụ liệu cúc gỗ tự nhiên an toàn</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate" for="fk_cmsSummary">Tóm tắt ngắn hiển thị trên thẻ bài viết (<span class="text-danger">*</span>):</label>
                            <textarea id="fk_cmsSummary" name="summary" class="form-control" rows="2" required>Tổng hợp những bộ trang phục sợi tre co giãn 4 chiều giúp bé vận động thoải mái suốt cả ngày ở trường mầm non.</textarea>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate" for="fk_cmsContent">Nội dung chi tiết bài viết (<span class="text-danger">*</span>):</label>
                            <textarea id="fk_cmsContent" name="content" class="form-control" rows="5" required>Khi bé bắt đầu đi học mầm non, ba mẹ nên ưu tiên chuẩn bị từ 3 đến 4 bộ quần áo thun sợi tre mỏng nhẹ, thấm hút mồ hôi nhanh và có cạp chun mềm co giãn để bé dễ dàng tự thay đồ và ngủ trưa thoải mái.</textarea>
                        </div>

                        <button type="submit" id="fk_btnSaveCmsBlog" class="btn btn-pastel-primary btn-block py-2 font-weight-bold shadow-sm">
                            <i class="fa-solid fa-paper-plane mr-1"></i> <?= $isAdminUser ? 'Xuất Bản Bài Viết Lên Tạp Chí Ngay' : 'Lưu Bản Nháp Trình Admin Duyệt' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (7 CỘT): DANH SÁCH BÀI VIẾT ĐÃ ĐĂNG & KIỂM DUYỆT BÌNH LUẬN
             ================================================================ -->
        <div class="col-12 col-xl-7">
            <!-- Bảng 1: Danh sách bài viết trong CSDL tgb_bai_viet_tin_tuc -->
            <div class="card card-pastel border-0 shadow-sm mb-4 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <h6 class="font-weight-bold text-slate mb-0 fk-header-title-grow">
                        <i class="fa-solid fa-list-check text-peach mr-2"></i>Danh Sách Bài Viết Đã Lưu (<span id="fk_cmsTotalArticles"><?= count($articles) ?></span> bài)
                    </h6>
                    <span class="badge badge-pill badge-sky px-3 py-1 text-nowrap flex-shrink-0">Đồng bộ trực tiếp</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Bài Viết &amp; Chuyên Mục</th>
                                    <th class="text-center">Lượt Đọc / Tim</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="fk_cmsArticlesTableBody">
                                <?php foreach ($articles as $art): ?>
                                    <tr id="fk_cmsArticleRow_<?= $art['id'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="lbn-inv-thumb mr-3 flex-shrink-0">
                                                    <img src="<?= htmlspecialchars($art['image']) ?>" alt="Thumb" class="lbn-thumb-img">
                                                </span>
                                                <div class="min-w-0">
                                                    <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $art['id'] ?>" class="font-weight-bold text-slate d-block text-decoration-none">
                                                        <?= htmlspecialchars($art['title']) ?>
                                                    </a>
                                                    <span class="badge <?= htmlspecialchars($art['badge_class']) ?> mr-1 text-nowrap"><?= htmlspecialchars($art['category_name']) ?></span>
                                                    <small class="text-muted-custom text-nowrap"><?= htmlspecialchars($art['published_at']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-nowrap small font-weight-bold">
                                            <span class="text-info mr-2"><i class="fa-regular fa-eye mr-1"></i><?= number_format($art['views']) ?></span>
                                            <span class="text-danger"><i class="fa-solid fa-heart mr-1"></i><?= number_format($art['likes']) ?></span>
                                        </td>
                                        <td class="text-center text-nowrap" id="fk_cmsStatusCell_<?= $art['id'] ?>">
                                            <?php if (($art['status'] ?? 'xuat_ban') === 'xuat_ban'): ?>
                                                <span class="badge badge-mint px-2 py-1 rounded-pill text-nowrap">Đã xuất bản</span>
                                            <?php else: ?>
                                                <span class="badge badge-gold px-2 py-1 rounded-pill text-nowrap">Bản nháp</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $art['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill px-2 mr-1" title="Xem bài viết">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <?php if ($isAdminUser): ?>
                                                <?php if (($art['status'] ?? 'xuat_ban') !== 'xuat_ban'): ?>
                                                    <button type="button" id="fk_btnPublishArticle_<?= $art['id'] ?>" onclick="fkPublishCmsArticleRow(<?= $art['id'] ?>)" class="btn btn-pastel-primary btn-sm rounded-pill px-2 mr-1" title="Xuất bản chính thức bài viết">
                                                        <i class="fa-solid fa-check-double mr-1"></i>Xuất bản
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" onclick="fkDeleteCmsArticleRow(<?= $art['id'] ?>)" class="btn btn-outline-danger btn-sm rounded-circle" title="Xóa/Ẩn bài viết">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bảng 2: Kiểm duyệt bình luận mới nhất từ bảng tgb_binh_luan_bai_viet -->
            <div class="card card-pastel border-0 shadow-sm bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <h6 class="font-weight-bold text-slate mb-0 fk-header-title-grow">
                        <i class="fa-solid fa-comments text-peach mr-2"></i>Kiểm Duyệt Bình Luận Mới Nhất
                    </h6>
                    <span class="badge badge-pill badge-mint px-3 py-1 text-nowrap flex-shrink-0">Tự động lọc từ khóa</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Phụ Huynh</th>
                                    <th>Nội Dung Bình Luận</th>
                                    <th>Thời Gian</th>
                                    <th class="text-center">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comments as $cm): ?>
                                    <tr id="fk_cmsCommentRow_<?= $cm['id'] ?>">
                                        <td class="font-weight-bold text-slate text-nowrap">
                                            <?= htmlspecialchars($cm['author_name']) ?>
                                        </td>
                                        <td class="small text-slate">
                                            <?= htmlspecialchars($cm['content']) ?>
                                        </td>
                                        <td class="small text-muted-custom text-nowrap">
                                            <?= htmlspecialchars($cm['created_at']) ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="/FashionKids/views/pages/blog-detail.php?id=1#fk_commentsSection" class="btn btn-outline-primary btn-sm rounded-pill px-2 mr-1">
                                                Trả lời
                                            </a>
                                            <?php if ($isAdminUser): ?>
                                                <button type="button" onclick="$('#fk_cmsCommentRow_<?= $cm['id'] ?>').fadeOut()" class="btn btn-outline-danger btn-sm rounded-pill px-2">
                                                    Xóa
                                                </button>
                                            <?php endif; ?>
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

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
