<?php
$page_title = "Hồ Sơ Cá Nhân & Thẻ VIP Điện Tử (MH 07)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

$user = [
    'ho_va_ten'      => 'Võ Thị Thanh Thảo',
    'ten_dang_nhap'  => 'me_be_sam',
    'thu_dien_tu'    => 'thanhthao@gmail.com',
    'so_dien_thoai'  => '0918889999',
    'diem_tich_luy'  => 1650,
    'ten_hang'       => 'VÀNG (GOLD)',
    'hang_class'     => 'vip-card-gold',
    'phan_tram_giam' => 10,
    'diem_ke_tiep'   => 3000,
    'da_xac_thuc'    => 1
];

$progress = min(100, round(($user['diem_tich_luy'] / $user['diem_ke_tiep']) * 100));
?>

<div class="container my-4 my-md-5" id="hgb_profile_section">
    <!-- Bộ chuyển đổi Demo nhanh 4 cấp độ thẻ VIP phục vụ chụp ảnh báo cáo -->
    <div class="card card-pastel p-3 mb-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <span class="small font-weight-bold text-slate mb-2 mb-md-0">
                <i class="fa-solid fa-wand-magic-sparkles text-peach mr-1"></i> Chế độ xem mô phỏng màu thẻ VIP:
            </span>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary font-weight-bold" onclick="hgbSwitchVipCard('bronze')">Đồng</button>
                <button type="button" class="btn btn-outline-info font-weight-bold" onclick="hgbSwitchVipCard('silver')">Bạc</button>
                <button type="button" class="btn btn-outline-warning font-weight-bold" onclick="hgbSwitchVipCard('gold')">Vàng</button>
                <button type="button" class="btn btn-outline-primary font-weight-bold" onclick="hgbSwitchVipCard('diamond')">Kim Cương</button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cột Trái: Thẻ VIP Điện Tử Mô Phỏng & Tiến Trình -->
        <div class="col-12 col-lg-5 mb-4">
            <div id="hgb_vipCardPreview" class="vip-card-box <?= $user['hang_class'] ?> mb-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="font-weight-bold text-uppercase">
                        <i class="fa-solid fa-crown mr-2"></i>FASHIONKIDS VIP
                    </span>
                    <span id="hgb_vipBadgeTitle" class="badge badge-light px-3 py-1 font-weight-bold rounded-pill shadow-sm">
                        HẠNG <?= $user['ten_hang'] ?>
                    </span>
                </div>
                
                <h4 class="font-weight-bold mb-1"><?= htmlspecialchars($user['ho_va_ten']) ?></h4>
                <div class="small mb-4 text-muted">Mã thẻ: #FK-2026-VIP09</div>

                <div class="d-flex justify-content-between align-items-end pt-3 vip-divider-gold">
                    <div>
                        <div class="small text-uppercase font-weight-bold">Đặc quyền chiết khấu</div>
                        <div id="hgb_vipDiscount" class="h4 font-weight-bold mb-0">GIẢM <?= $user['phan_tram_giam'] ?>% MỌI ĐƠN</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-uppercase font-weight-bold">Điểm tích lũy</div>
                        <div class="h5 font-weight-bold mb-0"><?= number_format($user['diem_tich_luy']) ?> PTS</div>
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
                    <i class="fa-solid fa-circle-info text-info mr-1"></i> Tích lũy thêm <strong><?= number_format($user['diem_ke_tiep'] - $user['diem_tich_luy']) ?> điểm</strong> từ các đơn hàng may đo sợi tre để tự động nâng hạng đặc quyền.
                </small>
            </div>
        </div>

        <!-- Cột Phải: Form Thông Tin Định Danh & Địa Chỉ -->
        <div class="col-12 col-lg-7">
            <div class="card card-pastel p-4 p-md-5 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold mb-0 text-slate">Thông Tin Tài Khoản</h5>
                    <span class="badge badge-pill badge-mint px-3 py-2">
                        <i class="fa-solid fa-shield-check mr-1"></i> Tài khoản đã xác minh OTP
                    </span>
                </div>

                <form id="hgb_formProfileUpdate">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Tên đăng nhập:</label>
                            <input type="text" class="form-control" name="ten_dang_nhap" value="<?= htmlspecialchars($user['ten_dang_nhap']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Họ và tên:</label>
                            <input type="text" class="form-control" name="ho_va_ten" value="<?= htmlspecialchars($user['ho_va_ten']) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Địa chỉ Email:</label>
                            <input type="email" class="form-control" name="thu_dien_tu" value="<?= htmlspecialchars($user['thu_dien_tu']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Số điện thoại:</label>
                            <input type="text" class="form-control" name="so_dien_thoai" value="<?= htmlspecialchars($user['so_dien_thoai']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-slate">Địa chỉ giao hàng mặc định:</label>
                        <input type="text" class="form-control" name="dia_chi" value="Số 120 Điện Biên Phủ, Phường 17, Quận Bình Thạnh, TP.HCM">
                    </div>

                    <button type="button" class="btn btn-pastel-primary font-weight-bold px-4">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật Thông Tin
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../layouts/footer.php"; ?>