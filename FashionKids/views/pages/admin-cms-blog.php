<?php
$page_title = "Quản Trị Tạp Chí & Bài Viết (MH 21/22)";
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess(__FILE__); // RBAC: Admin + Nhân viên
require_once __DIR__ . "/../../models/StaffModel.php";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

$fk_posts    = [];
$fk_db_error = '';
try {
    $fk_posts = (new StaffModel())->layDanhSachBaiViet();
} catch (Throwable $e) {
    $fk_db_error = 'Không tải được danh sách bài viết từ cơ sở dữ liệu. Vui lòng kiểm tra MySQL.';
}
$fk_post_status = [
    'ban_nhap' => ['Bản nháp', 'badge-secondary'],
    'xuat_ban' => ['Đã xuất bản', 'badge-mint'],
    'luu_tru'  => ['Lưu trữ', 'badge-sky'],
];
?>

<div class="container-fluid my-4 px-lg-5" id="fk_cms_section">
    <div class="mb-4">
        <h4 class="font-weight-bold text-slate mb-1">Quản Trị Tạp Chí (MH 21/22)</h4>
        <small class="text-muted-custom">
            <?php if (Rbac::can('blog.publish')): ?>
                Quản trị viên: soạn, <strong>xuất bản</strong> và <strong>xóa</strong> bài viết.
            <?php else: ?>
                Nhân viên: soạn thảo và lưu <strong>Bản nháp</strong>; việc xuất bản/xóa thuộc Quản trị viên.
            <?php endif; ?>
        </small>
    </div>

    <div id="fk_postAlert" class="d-none"></div>
    <?php if ($fk_db_error): ?><div class="alert alert-danger"><?= htmlspecialchars($fk_db_error) ?></div><?php endif; ?>

    <div class="row">
        <!-- Form soạn thảo -->
        <div class="col-12 col-xl-5 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold mb-0 text-slate"><i class="fa-solid fa-pen-nib text-peach mr-1"></i> Soạn Bài Viết Mới</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form id="fk_postForm" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="ma_bai_viet" value="">
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="fk_postTitle">Tiêu đề</label>
                            <input type="text" id="fk_postTitle" name="tieu_de" class="form-control form-control-sm" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="fk_postExcerpt">Tóm tắt ngắn</label>
                            <textarea id="fk_postExcerpt" name="tom_tat" rows="2" class="form-control form-control-sm" maxlength="500" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="fk_postContent">Nội dung chi tiết</label>
                            <textarea id="fk_postContent" name="noi_dung" rows="8" class="form-control form-control-sm" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="fk_postThumb">Ảnh bìa (JPG/PNG/WEBP ≤ 2MB)</label>
                            <input type="file" id="fk_postThumb" name="thumbnail" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp">
                        </div>
                        <button type="button" class="btn btn-pastel-outline btn-block font-weight-bold py-2" onclick="fkSavePost(false)">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Bản Nháp
                        </button>
                        <?php if (Rbac::can('blog.publish')): ?>
                            <button type="button" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm" onclick="fkSavePost(true)">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Lưu &amp; Xuất Bản
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách bài viết -->
        <div class="col-12 col-xl-7 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold mb-0 text-slate"><i class="fa-solid fa-newspaper text-peach mr-1"></i> Danh Sách Bài Viết</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Bài viết</th>
                                <th>Tác giả</th>
                                <th class="text-center">Trạng thái</th>
                                <?php if (Rbac::can('blog.publish') || Rbac::can('blog.delete')): ?><th class="text-center">Thao tác</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="fk_postTableBody">
                            <?php foreach ($fk_posts as $post): ?>
                                <?php [$pl, $pc] = $fk_post_status[$post['trang_thai_bai_viet']] ?? [$post['trang_thai_bai_viet'], 'badge-secondary']; ?>
                                <tr id="fk_postRow_<?= (int)$post['ma_bai_viet'] ?>">
                                    <td>
                                        <div class="font-weight-bold text-slate"><?= htmlspecialchars($post['tieu_de']) ?></div>
                                        <small class="text-muted-custom"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($post['ngay_dang']))) ?></small>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($post['tac_gia']) ?></td>
                                    <td class="text-center"><span class="badge <?= $pc ?> px-2 py-1 rounded-pill fk-post-badge"><?= htmlspecialchars($pl) ?></span></td>
                                    <?php if (Rbac::can('blog.publish') || Rbac::can('blog.delete')): ?>
                                        <td class="text-center text-nowrap">
                                            <?php if (Rbac::can('blog.publish') && $post['trang_thai_bai_viet'] !== 'xuat_ban'): ?>
                                                <button type="button" class="btn btn-success btn-sm rounded-pill font-weight-bold fk-btn-publish"
                                                        onclick="fkPublishPost(<?= (int)$post['ma_bai_viet'] ?>, this)">Xuất bản</button>
                                            <?php endif; ?>
                                            <?php if (Rbac::can('blog.delete')): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill font-weight-bold"
                                                        onclick="fkDeletePost(<?= (int)$post['ma_bai_viet'] ?>, this)">Xóa</button>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($fk_posts)): ?>
                                <tr><td colspan="4" class="text-center text-muted-custom small py-4">Chưa có bài viết nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
