<?php
$page_title = "Quản Trị Người Dùng & Cấu Hình Hạng VIP";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();

require_once __DIR__ . "/../../models/UserModel.php";

$userModel = new UserModel();
$users_list = $userModel->layDanhSachNguoiDung();
$rawTiers = $userModel->layDanhSachHangThanhVien();
$tierBadgeMap = [
    1 => 'badge-secondary',
    2 => 'badge-sky',
    3 => 'badge-gold',
    4 => 'badge-primary',
];
$tiers = [];
foreach ($rawTiers as $rt) {
    $tid = (int)($rt['ma_hang'] ?? 1);
    $tiers[] = [
        'id'       => $tid,
        'name'     => (string)($rt['ten_hang'] ?? ''),
        'min'      => (int)($rt['diem_toi_thieu'] ?? 0),
        'discount' => (float)($rt['phan_tram_giam_gia'] ?? 0),
        'badge'    => $tierBadgeMap[$tid] ?? 'badge-secondary',
    ];
}
?>

<div class="container-fluid my-4 px-lg-5" id="hgb_admin_users_section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">Quản Trị Người Dùng &amp; Cấu Hình Hạng VIP</h4>
            <small class="text-muted-custom">Phân quyền vai trò hệ thống và thiết lập tỷ lệ chiết khấu thẻ thành viên</small>
        </div>
        <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-left mr-1"></i> Bảng Điều Khiển
        </a>
    </div>

    <div id="hgb_admin_users_alert" class="alert small py-2 d-none" role="alert"></div>

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
                                            <?php if (!empty($u['otp_verified'])): ?>
                                                <span class="badge badge-mint small py-0 px-2 rounded-pill">Đã xác thực</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-dark small py-0 px-2 rounded-pill">Chờ xác thực</span>
                                            <?php endif; ?>
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
                                            <strong><?= htmlspecialchars($u['tier']) ?></strong>
                                        </td>
                                        <td class="text-right text-primary font-weight-bold"><?= number_format($u['points']) ?></td>
                                        <td class="text-center" id="hgb_userStatusCell_<?= (int)$u['id'] ?>">
                                            <?php if ($u['status'] == 1): ?>
                                                <span class="badge badge-success badge-pill px-2 py-1">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger badge-pill px-2 py-1">Đã khóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($u['role'] !== 'quan_tri'): ?>
                                                <?php if ($u['status'] == 1): ?>
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm rounded-circle hgb-btn-toggle-user-status"
                                                            data-user-id="<?= (int)$u['id'] ?>"
                                                            data-next-status="0"
                                                            title="Khóa tài khoản">
                                                        <i class="fa-solid fa-lock"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button"
                                                            class="btn btn-outline-success btn-sm rounded-circle hgb-btn-toggle-user-status"
                                                            data-user-id="<?= (int)$u['id'] ?>"
                                                            data-next-status="1"
                                                            title="Mở khóa tài khoản">
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

        <!-- Cot 2: Form Cau Hinh Muc Diem Toi Thieu & Chiet Khau VIP -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold mb-0 text-slate">Cấu Hình Mức Điểm Tối Thiểu &amp; % Giảm Giá VIP</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form id="hgb_formVipTiers">
                        <?php foreach ($tiers as $t): ?>
                            <div class="p-3 mb-3 rounded bg-milk-cream border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="font-weight-bold text-slate">
                                        <i class="fa-solid fa-medal mr-1 text-peach"></i> Hạng <?= htmlspecialchars($t['name']) ?>
                                    </span>
                                    <span class="badge <?= $t['badge'] ?> px-2 py-1">Cấp <?= (int)$t['id'] ?></span>
                                </div>
                                <div class="form-row">
                                    <div class="col-6">
                                        <label class="small text-muted-custom mb-1 font-weight-bold">Mức điểm tối thiểu (pts):</label>
                                        <input type="number" class="form-control form-control-sm" name="tiers[<?= (int)$t['id'] ?>][min]" value="<?= (int)$t['min'] ?>" min="0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted-custom mb-1 font-weight-bold">Giảm giá (%):</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" name="tiers[<?= (int)$t['id'] ?>][discount]" value="<?= (float)$t['discount'] ?>" min="0" max="50" required>
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
