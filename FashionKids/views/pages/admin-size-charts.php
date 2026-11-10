<?php
$page_title = "Quản Trị Bảng Quy Chuẩn Size & Ràng Buộc Danh Mục (MH 14)";
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess(__FILE__); // RBAC: chặn trước khi xuất bất kỳ nội dung nào
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Danh sách Danh mục chuẩn lbn_categories gắn kết trực tiếp qua ma_danh_muc (category_id)
$lnkt_categories_ref = [
    1 => ['id' => 1, 'code' => 'DM01', 'name' => 'Váy công chúa', 'type' => 'quan_ao',  'gender' => 'girl'],
    2 => ['id' => 2, 'code' => 'DM02', 'name' => 'Bộ thun',       'type' => 'quan_ao',  'gender' => 'boy'],
    3 => ['id' => 3, 'code' => 'DM03', 'name' => 'Áo khoác',      'type' => 'quan_ao',  'gender' => 'unisex'],
    4 => ['id' => 4, 'code' => 'DM04', 'name' => 'Giày sandal',   'type' => 'giay_dep', 'gender' => 'unisex']
];

// Bảng quy chuẩn kích cỡ thật từ CSDL (lnkt_bang_kich_co JOIN lbn_danh_muc)
require_once __DIR__ . "/../../models/SizeChartModel.php";
$lnkt_size_charts = [];
$fk_sc_error      = '';
$fk_gender_view   = ['be_gai' => 'girl', 'be_trai' => 'boy', 'chung' => 'unisex'];
try {
    foreach ((new SizeChartModel())->getAllCharts() as $r) {
        $lnkt_size_charts[] = [
            'id'              => (int)$r['ma_kich_co'],
            'ma_danh_muc'     => (int)$r['ma_danh_muc'],
            'category_code'   => 'DM' . str_pad((string)$r['ma_danh_muc'], 2, '0', STR_PAD_LEFT),
            'category_name'   => $r['ten_danh_muc'],
            'type'            => $r['loai_hang'],
            'gender'          => $fk_gender_view[$r['gioi_tinh']] ?? 'unisex',
            'size_name'       => $r['ten_kich_co'],
            'height_min'      => (float)$r['chieu_cao_toi_thieu'],
            'height_max'      => (float)$r['chieu_cao_toi_da'],
            'chest_min'       => (float)$r['vong_nguc_toi_thieu'],
            'chest_max'       => (float)$r['vong_nguc_toi_da'],
            'waist_min'       => (float)$r['vong_eo_toi_thieu'],
            'waist_max'       => (float)$r['vong_eo_toi_da'],
            'foot_length_min' => (float)$r['chieu_dai_chan_toi_thieu'],
            'foot_length_max' => (float)$r['chieu_dai_chan_toi_da'],
        ];
    }
} catch (Throwable $e) {
    $fk_sc_error = 'Không tải được bảng kích cỡ từ cơ sở dữ liệu.';
}
?>

<div class="container-fluid my-4 px-lg-5" id="lnkt_admin_size_charts_section">
    <!-- Header Quản Trị Bảng Size (MH 14) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-ruler-combined text-peach mr-2"></i>Quản Trị Bảng Quy Chuẩn Size &amp; Thông Số Trần/Sàn (MH 14)
            </h4>
            <small class="text-muted-custom">
                Đảm nhiệm: Lê Nguyễn Khánh Trình (<code>lnkt_</code>) — Cấu hình bảng <code>lnkt_size_charts</code> ràng buộc khóa ngoại <code>ma_danh_muc</code> (<code>category_id</code>)
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-pastel-sky btn-sm font-weight-bold rounded-pill px-3 mr-2 shadow-sm"
                    data-toggle="modal" data-target="#modalSuggestSize">
                <i class="fa-solid fa-wand-magic-sparkles text-peach mr-1"></i> Kiểm Thử Modal Gợi Ý Size (MH 04)
            </button>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                <i class="fa-solid fa-clipboard-check mr-1"></i> Duyệt Đơn Hàng (MH 10)
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (8 CỘT): BẢNG CẤU HÌNH THÔNG SỐ TRẦN/SÀN LNKT_SIZE_CHARTS
             ================================================================ -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-bold text-slate mb-0">
                                Danh Sách Quy Chuẩn Trần/Sàn Gắn Kết Mã Danh Mục (<code>ma_danh_muc</code>)
                            </h6>
                            <small class="text-muted-custom">
                                Dữ liệu đầu vào trực tiếp cho Stored Function <code>lnkt_suggest_size</code>
                            </small>
                        </div>
                        <button type="button" onclick="lnktResetSizeChartForm()" class="btn btn-pastel-primary btn-sm px-3 mt-2 mt-sm-0">
                            <i class="fa-solid fa-plus mr-1"></i> Thêm Cấu Hình Size Mới
                        </button>
                    </div>

                    <!-- Bộ lọc theo ma_danh_muc và phân loại -->
                    <div class="form-row">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <select id="lnkt_filterSizeCategory" class="custom-select custom-select-sm rounded-pill"
                                    onchange="lnktFilterAdminSizeCharts()">
                                <option value="all">-- Lọc theo tất cả Mã danh mục (ma_danh_muc) --</option>
                                <?php foreach ($lnkt_categories_ref as $cat): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        ma_danh_muc = <?= $cat['id'] ?> (<?= htmlspecialchars($cat['code'] . ' - ' . $cat['name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <select id="lnkt_filterSizeType" class="custom-select custom-select-sm rounded-pill"
                                    onchange="lnktFilterAdminSizeCharts()">
                                <option value="all">-- Tất cả nhóm kích cỡ (Quần áo &amp; Giày dép) --</option>
                                <option value="quan_ao">Quần áo (Đo Chiều cao, Vòng ngực, Vòng eo)</option>
                                <option value="giay_dep">Giày dép (Đo Chiều dài bàn chân)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mã DM (<code>ma_danh_muc</code>)</th>
                                    <th>Ký Hiệu Size</th>
                                    <th class="text-center">Chiều Cao (cm)</th>
                                    <th class="text-center">Vòng Ngực (cm)</th>
                                    <th class="text-center">Vòng Eo (cm)</th>
                                    <th class="text-center">Dài Bàn Chân (cm)</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_size_charts as $sc): ?>
                                    <tr class="lnkt-size-chart-row"
                                        data-cat="<?= $sc['ma_danh_muc'] ?>"
                                        data-type="<?= $sc['type'] ?>">
                                        <td>
                                            <span class="badge badge-pill badge-sky px-2 py-1">
                                                <?= htmlspecialchars($sc['category_code']) ?> (#<?= $sc['ma_danh_muc'] ?>)
                                            </span>
                                            <div class="small font-weight-bold text-slate mt-1">
                                                <?= htmlspecialchars($sc['category_name']) ?>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="font-weight-bold text-peach"><?= htmlspecialchars($sc['size_name']) ?></div>
                                            <small class="text-muted-custom">
                                                <?= $sc['type'] === 'quan_ao' ? 'Quần áo' : 'Giày dép' ?> • <?= strtoupper($sc['gender']) ?>
                                            </small>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($sc['type'] === 'quan_ao'): ?>
                                                <span class="badge badge-mint px-2 py-1">
                                                    <?= $sc['height_min'] ?> - <?= $sc['height_max'] ?> cm
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($sc['type'] === 'quan_ao'): ?>
                                                <span class="small font-weight-bold text-slate">
                                                    <?= $sc['chest_min'] ?> - <?= $sc['chest_max'] ?> cm
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($sc['type'] === 'quan_ao'): ?>
                                                <span class="small font-weight-bold text-slate">
                                                    <?= $sc['waist_min'] ?> - <?= $sc['waist_max'] ?> cm
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($sc['type'] === 'giay_dep'): ?>
                                                <span class="badge badge-gold px-2 py-1">
                                                    <?= $sc['foot_length_min'] ?> - <?= $sc['foot_length_max'] ?> cm
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center text-nowrap">
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm rounded-pill px-2"
                                                    data-id="<?= $sc['id'] ?>"
                                                    data-cat="<?= $sc['ma_danh_muc'] ?>"
                                                    data-type="<?= $sc['type'] ?>"
                                                    data-gender="<?= $sc['gender'] ?>"
                                                    data-size="<?= htmlspecialchars($sc['size_name']) ?>"
                                                    data-hmin="<?= $sc['height_min'] ?>"
                                                    data-hmax="<?= $sc['height_max'] ?>"
                                                    data-cmin="<?= $sc['chest_min'] ?>"
                                                    data-cmax="<?= $sc['chest_max'] ?>"
                                                    data-wmin="<?= $sc['waist_min'] ?>"
                                                    data-wmax="<?= $sc['waist_max'] ?>"
                                                    data-fmin="<?= $sc['foot_length_min'] ?>"
                                                    data-fmax="<?= $sc['foot_length_max'] ?>"
                                                    onclick="lnktEditSizeChartRow(this)">
                                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2" title="Xóa cấu hình size" onclick="fkDeleteSizeChart(<?= (int)$sc['id'] ?>, this)"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (4 CỘT): FORM CẤU HÌNH TRẦN/SÀN & RÀNG BUỘC MA_DANH_MUC
             ================================================================ -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm" id="lnkt_sizeChartFormCard">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate mb-0" id="lnkt_sizeFormTitle">
                        <i class="fa-solid fa-Sliders text-peach mr-1"></i> Cấu Hình Thông Số Trần/Sàn Size
                    </h6>
                    <button type="button" id="lnkt_btnCancelSizeEdit" onclick="lnktResetSizeChartForm()"
                            class="btn btn-light btn-sm rounded-pill border d-none">
                        Hủy sửa
                    </button>
                </div>

                <div class="card-body p-4 pt-2">
                    <div id="fk_sizeAlert" class="d-none"></div>
                    <form id="lnkt_formAdminSizeChart" autocomplete="off">
                        <input type="hidden" id="lnkt_scId" value="">

                        <!-- Ràng buộc trực tiếp với ma_danh_muc -->
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lnkt_scCategoryId">
                                Ràng buộc Mã Danh Mục (<code>ma_danh_muc</code> / FK <code>lbn_categories</code>):
                            </label>
                            <select id="lnkt_scCategoryId" class="custom-select form-control-sm" required>
                                <option value="1">ma_danh_muc = 1 (DM01 - Váy công chúa)</option>
                                <option value="2">ma_danh_muc = 2 (DM02 - Bộ thun)</option>
                                <option value="3">ma_danh_muc = 3 (DM03 - Áo khoác)</option>
                                <option value="4">ma_danh_muc = 4 (DM04 - Giày sandal)</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-5">
                                <label class="small font-weight-bold text-slate mb-1">Phân nhóm (<code>type</code>):</label>
                                <select id="lnkt_scType" class="custom-select form-control-sm" onchange="lnktToggleSizeChartInputs(this.value)">
                                    <option value="quan_ao">Quần áo</option>
                                    <option value="giay_dep">Giày dép</option>
                                </select>
                            </div>
                            <div class="form-group col-3">
                                <label class="small font-weight-bold text-slate mb-1">Giới tính:</label>
                                <select id="lnkt_scGender" class="custom-select form-control-sm">
                                    <option value="girl">Girl</option>
                                    <option value="boy">Boy</option>
                                    <option value="unisex">Unisex</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label class="small font-weight-bold text-slate mb-1">Ký hiệu Size:</label>
                                <input type="text" id="lnkt_scSizeName" class="form-control form-control-sm font-weight-bold"
                                       placeholder="VD: Size 2 (2-3T)" value="Size 2 (2-3T)" required>
                            </div>
                        </div>

                        <!-- Khối thông số Quần áo: Chiều cao, Vòng ngực, Vòng eo -->
                        <div id="lnkt_clothingSpecGroup" class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="small font-weight-bold text-peach mb-2">
                                <i class="fa-solid fa-shirt mr-1"></i>Thông Số Trần / Sàn Quần Áo (Đơn vị: cm)
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6 mb-2">
                                    <label class="small font-weight-bold text-slate mb-1">Chiều cao sàn (Min):</label>
                                    <input type="number" id="lnkt_scHeightMin" class="form-control form-control-sm" value="84" step="0.5">
                                </div>
                                <div class="form-group col-6 mb-2">
                                    <label class="small font-weight-bold text-slate mb-1">Chiều cao trần (Max):</label>
                                    <input type="number" id="lnkt_scHeightMax" class="form-control form-control-sm" value="94" step="0.5">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6 mb-2">
                                    <label class="small font-weight-bold text-slate mb-1">Vòng ngực sàn (Min):</label>
                                    <input type="number" id="lnkt_scChestMin" class="form-control form-control-sm" value="52" step="0.5">
                                </div>
                                <div class="form-group col-6 mb-2">
                                    <label class="small font-weight-bold text-slate mb-1">Vòng ngực trần (Max):</label>
                                    <input type="number" id="lnkt_scChestMax" class="form-control form-control-sm" value="55" step="0.5">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6 mb-0">
                                    <label class="small font-weight-bold text-slate mb-1">Vòng eo sàn (Min):</label>
                                    <input type="number" id="lnkt_scWaistMin" class="form-control form-control-sm" value="49" step="0.5">
                                </div>
                                <div class="form-group col-6 mb-0">
                                    <label class="small font-weight-bold text-slate mb-1">Vòng eo trần (Max):</label>
                                    <input type="number" id="lnkt_scWaistMax" class="form-control form-control-sm" value="51" step="0.5">
                                </div>
                            </div>
                        </div>

                        <!-- Khối thông số Giày dép: Chiều dài bàn chân -->
                        <div id="lnkt_shoesSpecGroup" class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="small font-weight-bold text-warning mb-2">
                                <i class="fa-solid fa-shoe-prints mr-1"></i>Thông Số Trần / Sàn Giày Dép (Đơn vị: cm)
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6 mb-0">
                                    <label class="small font-weight-bold text-slate mb-1">Dài chân sàn (Min):</label>
                                    <input type="number" id="lnkt_scFootMin" class="form-control form-control-sm" value="0" step="0.1">
                                </div>
                                <div class="form-group col-6 mb-0">
                                    <label class="small font-weight-bold text-slate mb-1">Dài chân trần (Max):</label>
                                    <input type="number" id="lnkt_scFootMax" class="form-control form-control-sm" value="0" step="0.1">
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="lnkt_btnSaveSizeChart" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Cấu Hình Bảng Size
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../layouts/footer.php";
?>

