
<?php
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess('admin-users.php');
$page_title = "Quản Trị Người Dùng & Cấu Hình Hạng VIP (MH 15)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Du lieu gia lap nguoi dung
$users_list = [
    ['id' => 1, 'username' => 'admin_fashion', 'name' => 'Hồ Gia Bảo', 'email' => 'admin@fashionkids.vn', 'role' => 'quan_tri', 'tier' => 'Quản trị viên', 'points' => 0, 'status' => 1],
    ['id' => 2, 'username' => 'staff_tuvan', 'name' => 'Lê Nguyễn Khánh Trình', 'email' => 'staff@fashionkids.vn', 'role' => 'nhan_vien', 'tier' => 'Nhân viên CSKH', 'points' => 0, 'status' => 1],
    ['id' => 3, 'username' => 'me_be_sam', 'name' => 'Võ Thị Thanh Thảo', 'email' => 'thanhthao@gmail.com', 'role' => 'khach_hang', 'tier' => 'Vàng (Gold)', 'points' => 1650, 'status' => 1],
    ['id' => 4, 'username' => 'me_be_bong', 'name' => 'Trần Thị Bông', 'email' => 'bong@gmail.com', 'role' => 'khach_hang', 'tier' => 'Đồng (Bronze)', 'points' => 120, 'status' => 1],
    ['id' => 5, 'username' => 'spammer_99', 'name' => 'Nguyễn Văn Spam', 'email' => 'spam@gmail.com', 'role' => 'khach_hang', 'tier' => 'Đồng (Bronze)', 'points' => 0, 'status' => 0]
];

// 4 Cap do Hang The VIP
$tiers = [
    ['id' => 1, 'name' => 'Đồng (Bronze)', 'min' => 0, 'discount' => 0, 'badge' => 'badge-secondary'],
    ['id' => 2, 'name' => 'Bạc (Silver)', 'min' => 500, 'discount' => 5, 'badge' => 'badge-sky'],
    ['id' => 3, 'name' => 'Vàng (Gold)', 'min' => 1500, 'discount' => 10, 'badge' => 'badge-gold'],
    ['id' => 4, 'name' => 'Kim Cương (Diamond)', 'min' => 3000, 'discount' => 15, 'badge' => 'badge-primary']
];
?>

<div class="container-fluid my-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">Quản Trị Người Dùng & Cấu Hình Hạng VIP (MH 15)</h4>
            <small class="text-muted-custom">Phân quyền vai trò hệ thống và thiết lập tỷ lệ chiết khấu thẻ thành viên</small>
        </div>
        <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-left mr-1"></i> Bảng Điều Khiển
        </a>
    </div>

    <div class="row">
        <!-- Cot 1: Danh Sach Tai Khoan Nguoi Dung -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-0 text-slate">Danh Sách Tài Khoản Hệ Thống</h6>
                    <span class="badge badge-pill badge-primary px-3 py-1"><?= count($users_list) ?> tài khoản</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Họ và Tên</th>
                                    <th>Tài Khoản / Email</th>
                                    <th>Vai Trò</th>
                                    <th>Hạng Thẻ</th>
                                    <th class="text-right">Điểm</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users_list as $u): ?>
                                    <tr>
                                        <td class="font-weight-bold">#<?= $u['id'] ?></td>
                                        <td>
                                            <div class="font-weight-bold text-slate"><?= htmlspecialchars($u['name']) ?></div>
                                            <span class="badge badge-mint small py-0 px-2 rounded-pill">Đã OTP</span>
                                        </td>
                                        <td>
                                            <div class="small font-weight-bold"><?= htmlspecialchars($u['username']) ?></div>
                                            <small class="text-muted-custom"><?= htmlspecialchars($u['email']) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($u['role'] === 'quan_tri'): ?>
                                                <span class="badge badge-danger px-2 py-1">Quản Trị</span>
                                            <?php elseif ($u['role'] === 'nhan_vien'): ?>
                                                <span class="badge badge-info px-2 py-1">Nhân Viên</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-1">Khách Hàng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= $u['tier'] ?></strong>
                                        </td>
                                        <td class="text-right text-primary font-weight-bold"><?= number_format($u['points']) ?></td>
                                        <td class="text-center" id="hgb_userStatusCell_<?= $u['id'] ?>">
                                            <?php if ($u['status'] == 1): ?>
                                                <span class="badge badge-success px-2 py-1">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger px-2 py-1">Đã khóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center" id="hgb_userActionCell_<?= $u['id'] ?>">
                                            <?php if ($u['role'] !== 'quan_tri'): ?>
                                                <?php if ($u['status'] == 1): ?>
                                                    <button type="button" onclick="hgbToggleUserStatus(<?= $u['id'] ?>, 0)" class="btn btn-outline-danger btn-sm rounded-circle" title="Khóa tài khoản">
                                                        <i class="fa-solid fa-lock"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" onclick="hgbToggleUserStatus(<?= $u['id'] ?>, 1)" class="btn btn-outline-success btn-sm rounded-circle" title="Mở khóa tài khoản">
                                                        <i class="fa-solid fa-lock-open"></i>
                                                    </button>
                                                <?php endif; ?>
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

        <!-- Cot 2: Form Cau Hinh Diem San & Chiet Khau VIP -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold mb-0 text-slate">Cấu Hình Điểm Sàn & % Giảm Giá VIP</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div id="hgb_vipConfigAlert" class="alert d-none small font-weight-bold mb-3" role="alert"></div>
                    <form id="hgb_formAdminVipTiers">
                        <?php foreach ($tiers as $t): ?>
                            <div class="p-3 mb-3 rounded bg-milk-cream border hgb-vip-tier-config-item" data-tier-id="<?= $t['id'] ?>" data-tier-name="<?= htmlspecialchars($t['name']) ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="font-weight-bold text-slate">
                                        <i class="fa-solid fa-medal mr-1 text-peach"></i> Hạng <?= $t['name'] ?>
                                    </span>
                                    <span class="badge <?= $t['badge'] ?> px-2 py-1">Cấp <?= $t['id'] ?></span>
                                </div>
                                <div class="form-row">
                                    <div class="col-6">
                                        <label class="small text-muted-custom mb-1 font-weight-bold">Điểm sàn (pts):</label>
                                        <input type="number" id="hgb_tierMinPts_<?= $t['id'] ?>" class="form-control form-control-sm hgb-input-min-pts" value="<?= $t['min'] ?>" min="0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted-custom mb-1 font-weight-bold">Giảm giá (%):</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="hgb_tierDiscount_<?= $t['id'] ?>" class="form-control hgb-input-discount" value="<?= $t['discount'] ?>" min="0" max="100" step="0.5" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" id="hgb_btnSaveVipTiers" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Cấu Hình Hạng Thẻ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
