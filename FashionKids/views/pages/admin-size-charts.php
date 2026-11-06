<?php
$page_title = "Quản Trị Bảng Quy Chuẩn Size & Thông Số Kỹ Thuật (MH 14)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh sách Mã danh mục (ma_danh_muc - lbn_categories) gắn kết với bảng size (lnkt_size_charts)
$lnkt_categories = [
    ['ma_danh_muc' => 1, 'code' => 'DM01', 'name' => 'Đầm & Đồ Bộ Bé Gái Sợi Tre', 'gender' => 'Bé Gái', 'type' => 'Quần áo'],
    ['ma_danh_muc' => 2, 'code' => 'DM02', 'name' => 'Áo Thun & Đồ Bộ Bé Trai', 'gender' => 'Bé Trai', 'type' => 'Quần áo'],
    ['ma_danh_muc' => 3, 'code' => 'DM03', 'name' => 'Bodysuit Sơ Sinh Hữu Cơ', 'gender' => 'Unisex', 'type' => 'Quần áo'],
    ['ma_danh_muc' => 4, 'code' => 'DM04', 'name' => 'Sandal Tập Đi & Giày Dép Trẻ Em', 'gender' => 'Unisex', 'type' => 'Giày dép']
];

// Dữ liệu cấu hình trần/sàn chi tiết trong bảng lnkt_size_charts
$lnkt_size_charts_data = [
    [
        'id'              => 1,
        'ma_danh_muc'     => 3,
        'cat_label'       => 'DM03 - Bodysuit Sơ Sinh Hữu Cơ',
        'type'            => 'quan_ao',
        'gender'          => 'Unisex',
        'size_name'       => 'Size 1 (0-1T)',
        'height_min'      => 50.0,
        'height_max'      => 74.0,
        'chest_range'     => '44.0 - 48.0',
        'waist_range'     => '42.0 - 46.0',
        'foot_length_min' => null,
        'foot_length_max' => null
    ],
    [
        'id'              => 2,
        'ma_danh_muc'     => 1,
        'cat_label'       => 'DM01 - Đầm & Đồ Bộ Bé Gái Sợi Tre',
        'type'            => 'quan_ao',
        'gender'          => 'Bé Gái',
        'size_name'       => 'Size 2 (2-3T)',
        'height_min'      => 86.0,
        'height_max'      => 95.0,
        'chest_range'     => '52.0 - 55.0',
        'waist_range'     => '49.0 - 52.0',
        'foot_length_min' => null,
        'foot_length_max' => null
    ],
    [
        'id'              => 3,
        'ma_danh_muc'     => 1,
        'cat_label'       => 'DM01 - Đầm & Đồ Bộ Bé Gái Sợi Tre',
        'type'            => 'quan_ao',
        'gender'          => 'Bé Gái',
        'size_name'       => 'Size 3 (3-4T)',
        'height_min'      => 96.0,
        'height_max'      => 105.0,
        'chest_range'     => '55.0 - 58.0',
        'waist_range'     => '51.0 - 54.0',
        'foot_length_min' => null,
        'foot_length_max' => null
    ],
    [
        'id'              => 4,
        'ma_danh_muc'     => 2,
        'cat_label'       => 'DM02 - Áo Thun & Đồ Bộ Bé Trai',
        'type'            => 'quan_ao',
        'gender'          => 'Bé Trai',
        'size_name'       => 'Size 4 (4-5T)',
        'height_min'      => 106.0,
        'height_max'      => 115.0,
        'chest_range'     => '58.0 - 61.0',
        'waist_range'     => '53.0 - 56.0',
        'foot_length_min' => null,
        'foot_length_max' => null
    ],
    [
        'id'              => 5,
        'ma_danh_muc'     => 2,
        'cat_label'       => 'DM02 - Áo Thun & Đồ Bộ Bé Trai',
        'type'            => 'quan_ao',
        'gender'          => 'Bé Trai',
        'size_name'       => 'Size 5 (5-6T)',
        'height_min'      => 116.0,
        'height_max'      => 130.0,
        'chest_range'     => '61.0 - 65.0',
        'waist_range'     => '56.0 - 60.0',
        'foot_length_min' => null,
        'foot_length_max' => null
    ],
    [
        'id'              => 6,
        'ma_danh_muc'     => 4,
        'cat_label'       => 'DM04 - Sandal Tập Đi & Giày Dép',
        'type'            => 'giay_dep',
        'gender'          => 'Unisex',
        'size_name'       => 'Size 22 (Tập đi)',
        'height_min'      => null,
        'height_max'      => null,
        'chest_range'     => '—',
        'waist_range'     => '—',
        'foot_length_min' => 12.6,
        'foot_length_max' => 13.5
    ],
    [
        'id'              => 7,
        'ma_danh_muc'     => 4,
        'cat_label'       => 'DM04 - Sandal Tập Đi & Giày Dép',
        'type'            => 'giay_dep',
        'gender'          => 'Unisex',
        'size_name'       => 'Size 24 (1-2T)',
        'height_min'      => null,
        'height_max'      => null,
        'chest_range'     => '—',
        'waist_range'     => '—',
        'foot_length_min' => 13.6,
        'foot_length_max' => 14.5
    ]
];
?>

<div class="container-fluid my-4 px-lg-5" id="lnkt_admin_size_charts_page">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-ruler-combined text-peach mr-2"></i>Quản Trị Bảng Size &amp; Thông Số Trần / Sàn (MH 14)
            </h4>
            <small class="text-muted-custom">
                Cấu hình chiều cao, vòng ngực, vòng eo, chiều dài bàn chân gắn kết trực tiếp với mã danh mục (<code>ma_danh_muc</code>)
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-pastel-outline btn-sm font-weight-bold mr-2" data-toggle="modal" data-target="#modalSuggestSize">
                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Kiểm Thử Modal Gợi Ý Size (MH 04)
            </button>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                <i class="fa-solid fa-arrow-left mr-1"></i> Bàn Làm Việc Đơn Hàng
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI: FORM CẤU HÌNH THÔNG SỐ TRẦN / SÀN GẮN MÃ DANH MỤC
             ================================================================ -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold mb-0 text-slate">
                        <i class="fa-solid fa-Sliders text-peach mr-1"></i> Thêm / Cấu Hình Quy Chuẩn Size Mới
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formSaveSizeChart" onsubmit="event.preventDefault(); alert('Đã lưu thông số kỹ thuật vào bảng lnkt_size_charts (ràng buộc khóa ngoại ma_danh_muc) thành công!');">
                        <!-- Ràng buộc trực tiếp với Mã Danh Mục (ma_danh_muc) -->
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-slate">Ràng buộc Mã Danh Mục (<code>ma_danh_muc</code> <span class="text-danger">*</span>):</label>
                            <select name="ma_danh_muc" class="form-control font-weight-bold" required>
                                <?php foreach ($lnkt_categories as $cat): ?>
                                    <option value="<?= $cat['ma_danh_muc'] ?>">
                                        [<?= $cat['code'] ?>] <?= htmlspecialchars($cat['name']) ?> (<?= $cat['type'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="small font-weight-bold text-slate">Ký hiệu Size (<span class="text-danger">*</span>):</label>
                                <input type="text" name="size_name" class="form-control" value="Size 2 (2-3T)" placeholder="VD: Size 2 (2-3T)" required>
                            </div>
                            <div class="form-group col-6">
                                <label class="small font-weight-bold text-slate">Giới tính áp dụng:</label>
                                <select name="gender" class="form-control">
                                    <option value="girl">Bé Gái (Girl)</option>
                                    <option value="boy">Bé Trai (Boy)</option>
                                    <option value="unisex">Unisex (Cả hai)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Thông số Trần / Sàn: Chiều cao (cm) -->
                        <div class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="small font-weight-bold text-slate mb-2">
                                <i class="fa-solid fa-arrows-up-down text-peach mr-1"></i> 1. Thông Số Chiều Cao Sàn - Trần (Quần áo)
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <label class="small text-muted-custom mb-1">Chiều cao sàn (<code>height_min</code>):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.5" name="height_min" class="form-control" value="86.0">
                                        <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted-custom mb-1">Chiều cao trần (<code>height_max</code>):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.5" name="height_max" class="form-control" value="95.0">
                                        <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông số Vòng ngực & Vòng eo (cm) -->
                        <div class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="small font-weight-bold text-slate mb-2">
                                <i class="fa-solid fa-shirt text-info mr-1"></i> 2. Thông Số Vòng Ngực &amp; Vòng Eo Chi Tiết
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <label class="small text-muted-custom mb-1">Vòng ngực sàn - trần (cm):</label>
                                    <input type="text" name="chest_range" class="form-control form-control-sm" value="52.0 - 55.0" placeholder="VD: 52.0 - 55.0">
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted-custom mb-1">Vòng eo sàn - trần (cm):</label>
                                    <input type="text" name="waist_range" class="form-control form-control-sm" value="49.0 - 52.0" placeholder="VD: 49.0 - 52.0">
                                </div>
                            </div>
                        </div>

                        <!-- Thông số Trần / Sàn: Chiều dài bàn chân (cm) -->
                        <div class="p-3 rounded bg-milk-cream border mb-4">
                            <div class="small font-weight-bold text-slate mb-2">
                                <i class="fa-solid fa-shoe-prints text-warning mr-1"></i> 3. Chiều Dài Bàn Chân Sàn - Trần (Giày dép)
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <label class="small text-muted-custom mb-1">Chân sàn (<code>foot_length_min</code>):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="foot_length_min" class="form-control" placeholder="13.6">
                                        <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted-custom mb-1">Chân trần (<code>foot_length_max</code>):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="foot_length_max" class="form-control" placeholder="14.5">
                                        <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Cấu Hình Bảng Size
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI: DANH SÁCH BẢNG QUY CHUẨN SIZE HIỆN HÀNH
             ================================================================ -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-2 mb-md-0 text-slate">
                        Danh Sách Cấu Hình Bảng Size (<code>lnkt_size_charts</code>)
                    </h6>
                    <div class="d-flex align-items-center">
                        <span class="small text-muted-custom mr-2 font-weight-bold">Lọc theo <code>ma_danh_muc</code>:</span>
                        <select id="lnkt_filterSizeCategory" class="form-control form-control-sm font-weight-bold w-auto">
                            <option value="all">Tất cả danh mục (<?= count($lnkt_size_charts_data) ?> dòng)</option>
                            <?php foreach ($lnkt_categories as $cat): ?>
                                <option value="<?= $cat['ma_danh_muc'] ?>">[<?= $cat['code'] ?>] <?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light small">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã Danh Mục (<code>ma_danh_muc</code>)</th>
                                    <th>Ký Hiệu Size</th>
                                    <th class="text-center">Chiều Cao Sàn - Trần</th>
                                    <th class="text-center">Vòng Ngực</th>
                                    <th class="text-center">Vòng Eo</th>
                                    <th class="text-center">Dài Chân Sàn - Trần</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_size_charts_data as $row): ?>
                                    <tr class="lnkt-size-chart-row" data-category-id="<?= $row['ma_danh_muc'] ?>">
                                        <td class="font-weight-bold">#<?= $row['id'] ?></td>
                                        <td>
                                            <span class="badge badge-sky px-2 py-1 mb-1">FK: #<?= $row['ma_danh_muc'] ?></span>
                                            <div class="small font-weight-bold text-slate"><?= htmlspecialchars($row['cat_label']) ?></div>
                                        </td>
                                        <td>
                                            <strong class="text-peach"><?= htmlspecialchars($row['size_name']) ?></strong>
                                            <small class="d-block text-muted-custom"><?= htmlspecialchars($row['gender']) ?></small>
                                        </td>
                                        <td class="text-center font-weight-bold small">
                                            <?php if ($row['height_min'] !== null): ?>
                                                <span class="badge badge-mint px-2 py-1"><?= $row['height_min'] ?> - <?= $row['height_max'] ?> cm</span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center small font-weight-bold"><?= $row['chest_range'] ?> cm</td>
                                        <td class="text-center small font-weight-bold"><?= $row['waist_range'] ?> cm</td>
                                        <td class="text-center font-weight-bold small">
                                            <?php if ($row['foot_length_min'] !== null): ?>
                                                <span class="badge badge-gold px-2 py-1"><?= $row['foot_length_min'] ?> - <?= $row['foot_length_max'] ?> cm</span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle mr-1" title="Chỉnh sửa thông số" onclick="alert('Đã nạp thông số ' + '<?= $row['size_name'] ?>' + ' lên biểu mẫu cấu hình!');">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" title="Xóa quy chuẩn" onclick="if(confirm('Xác nhận xóa cấu hình size này?')) $(this).closest('tr').remove();">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
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

<?php
require_once "../modals/modal-suggest-size.php";
require_once "../layouts/footer.php";
?>
