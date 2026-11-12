<?php
if (ob_get_level() === 0) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    $savePath = session_save_path();
    $canWrite = false;
    if (!empty($savePath) && is_dir($savePath)) {
        $probe = @tempnam($savePath, 'fk_');
        if ($probe !== false) {
            @unlink($probe);
            $canWrite = true;
        }
    }
    if (!$canWrite) {
        $temp = sys_get_temp_dir();
        if (is_dir($temp)) {
            @session_save_path($temp);
        }
    }
    @session_start();
}
require_once __DIR__ . '/../../config/app.php';

$pendingGoogle = $_SESSION['hgb_pending_google_oauth'] ?? null;
if (!is_array($pendingGoogle) || empty($pendingGoogle['google_id']) || (time() - (int)($pendingGoogle['created_at'] ?? 0)) > 1800) {
    unset($_SESSION['hgb_pending_google_oauth']);
    header('Location: ' . APP_BASE_URL . '/views/pages/login-register.php?error=' . urlencode('Phiên xác thực Google đã hết hạn hoặc chưa hợp lệ. Vui lòng chọn lại tài khoản Google.'));
    exit;
}

$page_title = "Hoàn Tất Tài Khoản Google - FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

$ggMode        = (string)($pendingGoogle['mode'] ?? 'new_user');
$ggEmail       = (string)($pendingGoogle['email'] ?? '');
$ggName        = (string)($pendingGoogle['name'] ?? '');
$ggAvatar      = (string)($pendingGoogle['avatar'] ?? '');
$existingUser  = is_array($pendingGoogle['existing_user'] ?? null) ? $pendingGoogle['existing_user'] : null;

$initUsername  = (string)($pendingGoogle['suggested_username'] ?? '');
if ($initUsername === '' && $existingUser) {
    $initUsername = (string)($existingUser['ten_dang_nhap'] ?? '');
}
$initFullname  = ($existingUser && !empty($existingUser['ho_va_ten']))
    ? (string)$existingUser['ho_va_ten']
    : $ggName;
$initPhone     = $existingUser ? (string)($existingUser['so_dien_thoai'] ?? '') : '';
$initAddress   = $existingUser ? (string)($existingUser['dia_chi_nha'] ?? '') : '';

$isLinkMode    = ($ggMode === 'link_existing' && $existingUser !== null);
$isMissingMode = ($ggMode === 'complete_missing');
?>

<div class="container my-5" id="hgb_google_onboarding_container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7">
            <div class="card card-pastel shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <span class="badge badge-pill badge-mint px-3 py-2 mb-2">
                            <i class="fa-brands fa-google mr-1"></i>Xác thực Google Thành Công
                        </span>
                        <h4 class="font-weight-bold text-slate mb-1">
                            <?php if ($isLinkMode): ?>
                                Xác Nhận Liên Kết &amp; Cập Nhật Tài Khoản
                            <?php elseif ($isMissingMode): ?>
                                Bổ Sung Thông Tin Tài Khoản Mới
                            <?php else: ?>
                                Thiết Lập Tài Khoản Mới Bằng Google
                            <?php endif; ?>
                        </h4>
                        <p class="small text-muted-custom mb-0">
                            Đặt tên tài khoản riêng, tên hiển thị và địa chỉ giao hàng cơ bản cho hồ sơ của bạn.
                        </p>
                    </div>

                    <!-- Thẻ tài khoản Google đang chọn -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded border bg-milk-cream mb-4">
                        <div class="d-flex align-items-center min-w-0">
                            <?php if ($ggAvatar !== ''): ?>
                                <img src="<?= htmlspecialchars($ggAvatar) ?>" alt="Google Avatar" class="rounded-circle mr-3 border shadow-sm" style="width:48px;height:48px;object-fit:cover;">
                            <?php else: ?>
                                <div class="rounded-circle mr-3 bg-white border d-flex align-items-center justify-content-center text-peach font-weight-bold" style="width:48px;height:48px;">
                                    <i class="fa-brands fa-google fa-lg"></i>
                                </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <div class="font-weight-bold text-slate text-truncate"><?= htmlspecialchars($ggName !== '' ? $ggName : $ggEmail) ?></div>
                                <div class="small text-muted-custom text-truncate">
                                    <i class="fa-solid fa-envelope-circle-check text-success mr-1"></i><?= htmlspecialchars($ggEmail) ?>
                                </div>
                            </div>
                        </div>
                        <a href="<?= htmlspecialchars(APP_ENDPOINT) ?>?c=auth&amp;a=googleSwitchAccount"
                           class="btn btn-sm btn-outline-secondary rounded-pill font-weight-bold mt-2 mt-sm-0 text-nowrap">
                            <i class="fa-solid fa-arrows-rotate mr-1"></i>Chọn Gmail khác
                        </a>
                    </div>

                    <?php if ($isLinkMode): ?>
                        <div class="alert alert-info small py-2 mb-4">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Email <strong><?= htmlspecialchars($ggEmail) ?></strong> hiện đã gắn với tài khoản
                            <strong><?= htmlspecialchars((string)$existingUser['ten_dang_nhap']) ?></strong>
                            (Tên: <strong><?= htmlspecialchars((string)$existingUser['ho_va_ten']) ?></strong>).
                            Bạn có thể chỉnh sửa lại tên đăng nhập, họ tên, số điện thoại &amp; địa chỉ ngay bên dưới rồi bấm hoàn tất để liên kết, hoặc bấm <strong>Chọn Gmail khác</strong> để tạo tài khoản bằng một email Google mới.
                        </div>
                    <?php endif; ?>

                    <div id="hgb_gg_alert" class="alert small py-2 d-none" role="alert"></div>

                    <form id="hgb_formGoogleComplete" novalidate autocomplete="off">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token()) ?>">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="hgb_gg_username" class="small font-weight-bold text-slate">
                                    Tên đăng nhập tài khoản <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="hgb_gg_username" name="ten_dang_nhap"
                                       value="<?= htmlspecialchars($initUsername) ?>"
                                       placeholder="Ví dụ: mebe_sam2026" maxlength="30" required>
                                <div class="invalid-feedback"></div>
                                <div class="valid-feedback">Tên đăng nhập hợp lệ, có thể sử dụng.</div>
                                <small class="form-text text-muted-custom">4-30 ký tự (chữ không dấu, số hoặc <code>_</code>).</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="hgb_gg_fullname" class="small font-weight-bold text-slate">
                                    Họ và tên hiển thị <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="hgb_gg_fullname" name="ho_va_ten"
                                       value="<?= htmlspecialchars($initFullname) ?>"
                                       placeholder="Nhập họ và tên của bạn..." maxlength="100" required>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted-custom">Bạn có thể đổi sang tên bất kỳ thay cho tên hiển thị trên Gmail.</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold text-slate">Email Google đã xác thực</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($ggEmail) ?>" readonly>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="hgb_gg_phone" class="small font-weight-bold text-slate">
                                    Số điện thoại nhận hàng <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="hgb_gg_phone" name="so_dien_thoai"
                                       value="<?= htmlspecialchars($initPhone) ?>"
                                       placeholder="09xxxxxxxx" maxlength="11" inputmode="numeric" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Chọn nhanh địa giới hành chính Việt Nam 3 cấp -->
                        <div class="p-3 rounded border bg-light mb-3">
                            <div class="small font-weight-bold text-slate mb-2">
                                <i class="fa-solid fa-location-dot text-peach mr-1"></i>Địa chỉ giao hàng cơ bản <span class="text-danger">*</span>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4 mb-2">
                                    <select class="form-control form-control-sm" id="hgb_gg_province" name="tinh_thanh">
                                        <option value="">-- Tỉnh / Thành phố --</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 mb-2">
                                    <select class="form-control form-control-sm" id="hgb_gg_district" name="quan_huyen" disabled>
                                        <option value="">-- Quận / Huyện --</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 mb-2">
                                    <select class="form-control form-control-sm" id="hgb_gg_ward" name="phuong_xa" disabled>
                                        <option value="">-- Phường / Xã --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <input type="text" class="form-control form-control-sm" id="hgb_gg_street" name="so_nha"
                                       placeholder="Số nhà, tên đường (ví dụ: 18 Hoa Cúc)">
                            </div>
                            <div class="form-group mb-0">
                                <label for="hgb_gg_address" class="small text-muted-custom mb-1">Địa chỉ đầy đủ sẽ lưu vào hồ sơ:</label>
                                <input type="text" class="form-control" id="hgb_gg_address" name="dia_chi_nha"
                                       value="<?= htmlspecialchars($initAddress) ?>"
                                       placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" maxlength="255" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="hgb_gg_password" class="small font-weight-bold text-slate">
                                Đặt mật khẩu đăng nhập nội bộ <span class="text-muted-custom font-weight-normal">(Tùy chọn)</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control border-right-0" id="hgb_gg_password" name="mat_khau"
                                       placeholder="Để trống nếu chỉ dùng nút Google, hoặc nhập tối thiểu 6 ký tự..." autocomplete="new-password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary bg-white border-left-0 hgb-toggle-pass-btn" data-target="#hgb_gg_password" title="Hiện / Ẩn mật khẩu">
                                        <i class="fa-regular fa-eye text-muted"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <small class="form-text text-muted-custom">Nếu đặt mật khẩu, bạn có thể đăng nhập bằng cả tên tài khoản + mật khẩu lẫn Google.</small>
                        </div>

                        <button type="submit" id="hgb_btn_gg_complete" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-circle-check mr-1"></i>Hoàn Tất &amp; Vào Tài Khoản
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
