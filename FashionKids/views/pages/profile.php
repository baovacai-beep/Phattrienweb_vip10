
<?php
$page_title = "Hồ Sơ Cá Nhân & Thẻ VIP Thành Viên (MH 07)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Du lieu gia lap nguoi dung hien tai
$user = [
    'ho_va_ten'      => 'Võ Thị Thanh Thảo',
    'ten_dang_nhap'  => 'me_be_sam',
    'thu_dien_tu'    => 'thanhthao@gmail.com',
    'so_dien_thoai'  => '0918889999',
    'diem_tich_luy'  => 1650,
    'ten_hang'       => 'Vàng (Gold)',
    'hang_class'     => 'vip-card-gold',
    'phan_tram_giam' => 10,
    'diem_ke_tiep'   => 3000,
    'da_xac_thuc'    => 1
];

$progress = min(100, round(($user['diem_tich_luy'] / $user['diem_ke_tiep']) * 100));
?>

<div class="container my-4 my-md-5">
    <div class="row">
        <!-- Cot Trai: The VIP Dien Tu Mo Phong & Tien Trinh -->
        <div class="col-12 col-lg-5 mb-4">
            <div class="vip-card-box <?= $user['hang_class'] ?> mb-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="font-weight-bold text-uppercase">
                        <i class="fa-solid fa-crown mr-2"></i>FASHIONKIDS VIP
                    </span>
                    <span class="badge badge-light px-3 py-1 font-weight-bold rounded-pill shadow-sm">
                        HẠNG <?= mb_strtoupper($user['ten_hang']) ?>
                    </span>
                </div>
                
                <h4 class="font-weight-bold mb-1"><?= htmlspecialchars($user['ho_va_ten']) ?></h4>
                <div class="small mb-4 text-muted">Mã thành viên: #HGB-0003</div>

                <div class="d-flex justify-content-between align-items-end pt-3 vip-divider-gold">
                    <div>
                        <div class="small text-uppercase font-weight-bold">Đặc quyền chiết khấu</div>
                        <div class="h4 font-weight-bold mb-0">GIẢM <?= $user['phan_tram_giam'] ?>% MỌI ĐƠN</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-uppercase font-weight-bold">Điểm thưởng</div>
                        <div class="h5 font-weight-bold mb-0"><?= number_format($user['diem_tich_luy']) ?> PTS</div>
                    </div>
                </div>
            </div>

            <!-- Thanh Tien Trinh Nang Hang VIP -->
            <div class="card card-pastel p-4 shadow-sm">
                <h6 class="font-weight-bold mb-2 text-slate">Tiến trình lên Hạng Kim Cương</h6>
                <div class="d-flex justify-content-between small text-muted-custom mb-1 font-weight-bold">
                    <span>Hiện có: <?= number_format($user['diem_tich_luy']) ?> pts</span>
                    <span>Mục tiêu: <?= number_format($user['diem_ke_tiep']) ?> pts</span>
                </div>
                <div class="progress mb-3 rounded-pill">
                    <div class="progress-bar bg-peach" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted-custom">
                    <i class="fa-solid fa-circle-info text-info mr-1"></i> Tích lũy thêm <strong><?= number_format($user['diem_ke_tiep'] - $user['diem_tich_luy']) ?> điểm</strong> để nhận đặc quyền giảm giá 15% của hạng Kim Cương!
                </small>
            </div>
        </div>

        <!-- Cot Phai: Form Thong Tin Dinh Danh & So Dia Chi -->
        <div class="col-12 col-lg-7">
            <div class="card card-pastel p-4 p-md-5 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold mb-0 text-slate">Thông Tin Định Danh Khách Hàng</h5>
                    <span class="badge badge-pill badge-mint px-3 py-2">
                        <i class="fa-solid fa-shield-check mr-1"></i> Đã Xác Thực OTP
                    </span>
                </div>

                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Tên đăng nhập:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['ten_dang_nhap']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Họ và tên:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['ho_va_ten']) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Địa chỉ Email:</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['thu_dien_tu']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold text-slate">Số điện thoại nhận hàng:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['so_dien_thoai']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-slate">Địa chỉ giao hàng mặc định:</label>
                        <input type="text" class="form-control" value="Số 120 Điện Biên Phủ, Phường 17, Quận Bình Thạnh, TP.HCM">
                    </div>

                    <button type="button" class="btn btn-pastel-primary font-weight-bold px-4">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật Hồ Sơ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../layouts/footer.php"; ?>