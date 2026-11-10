<?php
$page_title = "Duyệt Hồ Sơ KOL Nhí (MH 17)";
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess(__FILE__); // RBAC: Admin + Nhân viên
require_once __DIR__ . "/../../models/StaffModel.php";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

$fk_kol_profiles = [];
$fk_db_error     = '';
try {
    $fk_kol_profiles = (new StaffModel())->layHoSoKol();
} catch (Throwable $e) {
    $fk_db_error = 'Không tải được hồ sơ KOL nhí từ cơ sở dữ liệu. Vui lòng kiểm tra MySQL.';
}

$fk_status_map = [
    'cho_duyet'     => ['Chờ duyệt',      'badge-warning text-dark'],
    'moi_phong_van' => ['Đã mời phỏng vấn', 'badge-sky'],
    'trung_tuyen'   => ['Trúng tuyển',    'badge-mint'],
    'tu_choi'       => ['Từ chối',        'badge-danger'],
];

function fkKolAge($birth) {
    $diff = (new DateTime($birth))->diff(new DateTime());
    return $diff->y > 0 ? $diff->y . ' tuổi' : $diff->m . ' tháng';
}
function fkKolFirstPhoto($raw) {
    $list = json_decode((string)$raw, true);
    if (!is_array($list)) {
        $list = preg_split('/[,\n;]+/', (string)$raw);
    }
    $first = trim((string)($list[0] ?? ''));
    return $first !== '' ? $first : '/FashionKids/images/banner-kol-nhi-2026.jpg';
}
?>

<div class="container-fluid my-4 px-lg-5" id="fk_kol_review_section">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">Duyệt Hồ Sơ KOL Nhí / Casting (MH 17)</h4>
            <small class="text-muted-custom">
                <?php if (Rbac::can('kol.decide')): ?>
                    Quản trị viên: có quyền quyết định cuối cùng <strong>Trúng tuyển / Từ chối</strong>.
                <?php else: ?>
                    Nhân viên: sơ tuyển và <strong>Mời phỏng vấn</strong>; quyết định cuối cùng thuộc Quản trị viên.
                <?php endif; ?>
            </small>
        </div>
        <div class="btn-group mt-2 mt-md-0" role="group" id="fk_kolFilter">
            <button type="button" class="btn btn-outline-primary btn-sm active font-weight-bold" onclick="fkFilterKol('all', this)">Tất cả</button>
            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="fkFilterKol('cho_duyet', this)">Chờ duyệt</button>
            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="fkFilterKol('moi_phong_van', this)">Đã mời PV</button>
            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="fkFilterKol('trung_tuyen', this)">Trúng tuyển</button>
        </div>
    </div>

    <div id="fk_kolAlert" class="d-none"></div>
    <?php if ($fk_db_error): ?><div class="alert alert-danger"><?= htmlspecialchars($fk_db_error) ?></div><?php endif; ?>

    <div class="row">
        <?php foreach ($fk_kol_profiles as $kol): ?>
            <?php [$stLabel, $stClass] = $fk_status_map[$kol['trang_thai_duyet']] ?? [$kol['trang_thai_duyet'], 'badge-secondary']; ?>
            <div class="col-12 col-md-6 col-lg-4 mb-4 fk-kol-card" data-status="<?= htmlspecialchars($kol['trang_thai_duyet']) ?>" id="fk_kolCard_<?= (int)$kol['ma_ho_so'] ?>">
                <div class="card card-pastel shadow-sm h-100 overflow-hidden">
                    <img src="<?= htmlspecialchars(fkKolFirstPhoto($kol['danh_sach_anh'])) ?>" class="card-img-top fk-casting-img" alt="<?= htmlspecialchars($kol['ho_ten_be']) ?>">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="font-weight-bold text-slate mb-0"><?= htmlspecialchars($kol['ho_ten_be']) ?> <small class="text-muted-custom">#<?= (int)$kol['ma_ho_so'] ?></small></h6>
                            <span class="badge badge-pill badge-sky"><?= htmlspecialchars(fkKolAge($kol['ngay_sinh_be'])) ?></span>
                        </div>
                        <div class="small text-muted-custom mb-2">
                            <div><i class="fa-solid fa-ruler-vertical mr-2"></i>Chiều cao: <?= (float)$kol['chieu_cao_be'] ?> cm</div>
                            <div><i class="fa-solid fa-weight-scale mr-2"></i>Cân nặng: <?= (float)$kol['can_nang_be'] ?> kg</div>
                            <div><i class="fa-solid fa-phone mr-2"></i><?= htmlspecialchars($kol['ho_ten_phu_huynh']) ?> (<?= htmlspecialchars($kol['so_dien_thoai']) ?>)</div>
                        </div>
                        <p class="small text-slate flex-grow-1"><?= htmlspecialchars((string)$kol['nang_khieu_kinh_nghiem']) ?></p>
                        <div class="mb-2">
                            <span class="badge <?= $stClass ?> px-2 py-1 rounded-pill fk-kol-badge"><?= htmlspecialchars($stLabel) ?></span>
                        </div>
                        <div class="pt-3 border-top fk-kol-actions">
                            <?php if ($kol['trang_thai_duyet'] === 'cho_duyet' && Rbac::can('kol.invite')): ?>
                                <button type="button" class="btn btn-pastel-sky btn-sm rounded-pill font-weight-bold mr-1 mb-1"
                                        onclick="fkInviteKol(<?= (int)$kol['ma_ho_so'] ?>, this)">
                                    <i class="fa-solid fa-user-clock mr-1"></i>Mời phỏng vấn
                                </button>
                            <?php endif; ?>
                            <?php if (in_array($kol['trang_thai_duyet'], ['cho_duyet', 'moi_phong_van'], true) && Rbac::can('kol.decide')): ?>
                                <button type="button" class="btn btn-success btn-sm rounded-pill font-weight-bold mr-1 mb-1"
                                        onclick="fkDecideKol(<?= (int)$kol['ma_ho_so'] ?>, 'trung_tuyen', this)">
                                    <i class="fa-solid fa-trophy mr-1"></i>Trúng tuyển
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill font-weight-bold mb-1"
                                        onclick="fkDecideKol(<?= (int)$kol['ma_ho_so'] ?>, 'tu_choi', this)">
                                    <i class="fa-solid fa-ban mr-1"></i>Từ chối
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($fk_kol_profiles) && !$fk_db_error): ?>
            <div class="col-12"><div class="alert alert-light border">Chưa có hồ sơ ứng tuyển nào.</div></div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
