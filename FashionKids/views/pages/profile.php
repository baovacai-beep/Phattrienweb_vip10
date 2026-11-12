<?php
$page_title = "Hồ Sơ Thành Viên & Thẻ VIP - FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

require_once __DIR__ . '/../../models/UserModel.php';
$hgbProfileModel = new UserModel();
$currentProfileUid = Rbac::currentUserId();
if ($currentProfileUid) {
    $hgbProfileModel->dongBoSessionNguoiDung($currentProfileUid);
}
$vipInfo  = $hgbProfileModel->layThongTinVipHienTai($currentProfileUid);
$sessUser = $_SESSION['user'] ?? null;

if (is_array($sessUser) && !empty($sessUser['ten_dang_nhap'])) {
    $diem         = (int)($vipInfo['points'] ?? ($sessUser['diem_tich_luy'] ?? 0));
    $phanTramGiam = (float)($vipInfo['discount_percent'] ?? ($sessUser['phan_tram_giam_gia'] ?? 0));
    $rawTierName  = (string)($vipInfo['tier_name'] ?? ($sessUser['ten_hang'] ?? 'Đồng (Bronze)'));
    $tierClassMap = [
        'tier-diamond' => 'vip-card-diamond',
        'tier-gold'    => 'vip-card-gold',
        'tier-silver'  => 'vip-card-silver',
        'tier-bronze'  => 'vip-card-bronze',
    ];
    $hangClass    = $tierClassMap[$vipInfo['badge_class'] ?? 'tier-bronze'] ?? 'vip-card-bronze';
    $tenHang      = mb_strtoupper($rawTierName, 'UTF-8');
    $diemKeTiep   = (int)($vipInfo['next_tier_min'] ?? 500);
    $nextTierName = $vipInfo['next_tier_name'] ?? null;

    $user = [
        'ma_nguoi_dung'  => (int)($sessUser['ma_nguoi_dung'] ?? 0),
        'ho_va_ten'      => (string)($sessUser['ho_va_ten'] ?? ''),
        'ten_dang_nhap'  => (string)($sessUser['ten_dang_nhap'] ?? ''),
        'thu_dien_tu'    => (string)($sessUser['thu_dien_tu'] ?? ''),
        'so_dien_thoai'  => (string)($sessUser['so_dien_thoai'] ?? ''),
        'dia_chi_nha'    => (string)($sessUser['dia_chi_nha'] ?? ''),
        'diem_tich_luy'  => $diem,
        'ten_hang'       => $tenHang,
        'hang_class'     => $hangClass,
        'phan_tram_giam' => $phanTramGiam,
        'diem_ke_tiep'   => $diemKeTiep,
        'next_tier_name' => $nextTierName,
        'da_xac_thuc'    => (int)($sessUser['da_xac_thuc_otp'] ?? 1),
    ];
} else {
    $user = [
        'ma_nguoi_dung'  => 0,
        'ho_va_ten'      => 'Khách hàng',
        'ten_dang_nhap'  => '',
        'thu_dien_tu'    => '',
        'so_dien_thoai'  => '',
        'dia_chi_nha'    => '',
        'diem_tich_luy'  => 0,
        'ten_hang'       => 'ĐỒNG (BRONZE)',
        'hang_class'     => 'vip-card-bronze',
        'phan_tram_giam' => 0,
        'diem_ke_tiep'   => 500,
        'next_tier_name' => 'Bạc (Silver)',
        'da_xac_thuc'    => 0,
    ];
}

$progress     = (int)($vipInfo['progress_percent'] ?? ($user['diem_ke_tiep'] > 0 ? min(100, round(($user['diem_tich_luy'] / $user['diem_ke_tiep']) * 100)) : 100));
$diemConThieu = (int)($vipInfo['points_needed'] ?? max(0, $user['diem_ke_tiep'] - $user['diem_tich_luy']));
$maTheHienThi = ((int)$user['ma_nguoi_dung'] > 0) ? sprintf('#FK-2026-VIP%02d', (int)$user['ma_nguoi_dung']) : '#FK-GUEST';
?>

<div class="container my-4 my-md-5" id="hgb_profile_section">
    <div class="row">
        <!-- Cột Trái: Thẻ VIP Thành Viên & Tiến Trình -->
        <div class="col-12 col-lg-5 mb-4">
            <div id="hgb_vipCardPreview" class="vip-card-box <?= $user['hang_class'] ?> mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-4">
                    <span class="font-weight-bold text-uppercase vip-card-brand text-nowrap">
                        <i class="fa-solid fa-crown mr-2"></i>FASHIONKIDS VIP
                    </span>
                    <span id="hgb_vipBadgeTitle" class="badge badge-light vip-card-tier-badge px-3 py-1 font-weight-bold rounded-pill shadow-sm text-nowrap flex-shrink-0">
                        HẠNG <?= htmlspecialchars($user['ten_hang']) ?>
                    </span>
                </div>
                
                <h4 class="font-weight-bold mb-1 vip-card-holder"><?= htmlspecialchars($user['ho_va_ten']) ?></h4>
                <div class="small mb-4 font-weight-bold vip-card-code">Mã thẻ: <?= htmlspecialchars($maTheHienThi) ?></div>

                <div class="d-flex flex-wrap justify-content-between align-items-end fk-card-header-row pt-3 vip-divider">
                    <div>
                        <div class="small text-uppercase font-weight-bold vip-card-label">Đặc quyền chiết khấu</div>
                        <div id="hgb_vipDiscount" class="h4 font-weight-bold mb-0 vip-card-val text-nowrap">GIẢM <?= (int)$user['phan_tram_giam'] ?>% MỌI ĐƠN</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-uppercase font-weight-bold vip-card-label">Điểm tích lũy</div>
                        <div id="hgb_vipCardPts" class="h5 font-weight-bold mb-0 vip-card-val text-nowrap"><?= number_format($user['diem_tich_luy']) ?> PTS</div>
                    </div>
                </div>
            </div>

            <!-- Thanh Tiến Trình Nâng Hạng VIP (Không dùng inline style) -->
            <div class="card card-pastel p-4 shadow-sm">
                <h6 class="font-weight-bold mb-2 text-slate">Tiến trình lên Hạng Kế Tiếp</h6>
                <div class="d-flex justify-content-between small text-muted-custom mb-1 font-weight-bold">
                    <span>Điểm tích lũy</span>
                    <span id="hgb_vipPtsInfo"><?= number_format($user['diem_tich_luy']) ?> / <?= number_format($user['diem_ke_tiep']) ?> PTS</span>
                </div>
                <div class="progress mb-3 rounded-pill">
                    <div id="hgb_vipProgressBar" class="progress-bar bg-peach hgb-progress-dynamic" 
                         role="progressbar" data-progress="<?= $progress ?>" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted-custom">
                    <i class="fa-solid fa-circle-info text-info mr-1"></i> Tích lũy thêm <strong><?= number_format($diemConThieu) ?> điểm</strong> từ các đơn hàng may đo sợi tre để tự động nâng hạng đặc quyền.
                </small>
            </div>
        </div>

        <!-- Cột Phải: Form Thông Tin Định Danh & Địa Chỉ -->
        <div class="col-12 col-lg-7">
            <div class="card card-pastel p-4 p-md-5 shadow-sm mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-4">
                    <h5 class="font-weight-bold mb-0 text-slate fk-header-title-grow">Thông Tin Tài Khoản</h5>
                    <span class="badge badge-pill <?= !empty($user['da_xac_thuc']) ? 'badge-mint' : 'badge-gold' ?> px-3 py-2 text-nowrap flex-shrink-0">
                        <i class="fa-solid <?= !empty($user['da_xac_thuc']) ? 'fa-circle-check' : 'fa-clock' ?> mr-1"></i> <?= !empty($user['da_xac_thuc']) ? 'Tài khoản đã xác thực' : 'Chưa xác thực OTP' ?>
                    </span>
                </div>

                <?php
                $isCustomerAccount = (Rbac::currentRole() === 'khach_hang');
                $isGoogleLinked = !empty($sessUser['google_id']) || (($sessUser['auth_provider'] ?? '') === 'google');
                ?>

                <?php if ($isGoogleLinked): ?>
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded border bg-milk-cream mb-4">
                        <div class="small text-slate pr-2">
                            <i class="fa-brands fa-google text-danger mr-1"></i>
                            Đang liên kết với Google Email <strong><?= htmlspecialchars($user['thu_dien_tu']) ?></strong>.
                        </div>
                        <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=googleSwitchAccount&amp;logout=1"
                           class="btn btn-sm btn-outline-secondary rounded-pill font-weight-bold mt-2 mt-sm-0 text-nowrap">
                            <i class="fa-solid fa-arrows-rotate mr-1"></i>Đổi tài khoản Google khác
                        </a>
                    </div>
                <?php endif; ?>

                <div id="hgb_profile_alert" class="alert small py-2 d-none" role="alert"></div>

                <form id="hgb_formProfileUpdate" novalidate>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="hgb_profile_username" class="small font-weight-bold text-slate">Tên đăng nhập:</label>
                            <input type="text" class="form-control" id="hgb_profile_username" name="ten_dang_nhap" maxlength="30"
                                   value="<?= htmlspecialchars($user['ten_dang_nhap']) ?>" <?= $isCustomerAccount ? '' : 'readonly' ?>>
                            <?php if ($isCustomerAccount): ?>
                                <small class="form-text text-muted-custom">4-30 ký tự (chữ không dấu, số, <code>_</code>).</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="hgb_profile_fullname" class="small font-weight-bold text-slate">Họ và tên:</label>
                            <input type="text" class="form-control" id="hgb_profile_fullname" name="ho_va_ten" value="<?= htmlspecialchars($user['ho_va_ten']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Địa chỉ Email:</label>
                            <input type="email" class="form-control" name="thu_dien_tu" value="<?= htmlspecialchars($user['thu_dien_tu']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Số điện thoại:</label>
                            <input type="tel" class="form-control" id="hgb_profile_phone" name="so_dien_thoai" maxlength="11" inputmode="numeric" value="<?= htmlspecialchars($user['so_dien_thoai']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-slate">Địa chỉ giao hàng mặc định:</label>
                        <input type="text" class="form-control" id="hgb_profile_address" name="dia_chi" value="<?= htmlspecialchars($user['dia_chi_nha']) ?>">
                    </div>

                    <button type="submit" id="hgb_btnUpdateProfile" class="btn btn-pastel-primary font-weight-bold px-4">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật Thông Tin
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
