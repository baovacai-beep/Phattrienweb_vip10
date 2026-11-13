<?php
$page_title = "Quản Lý Sản Phẩm & Tồn Kho";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();
$isAdminUser = Rbac::isAdmin();
require_once __DIR__ . "/../../models/ProductModel.php";

$invProductModel    = new ProductModel();
$lbn_inventory_list = $invProductModel->layTatCaSanPhamChuanHoa();
$invStats           = $invProductModel->layThongKeKhoHang();

$lbn_admin_categories = [
    1 => ['id' => 1, 'name' => 'Váy công chúa', 'gender' => 'girl',   'type' => 'quan_ao'],
    2 => ['id' => 2, 'name' => 'Bộ thun',       'gender' => 'boy',    'type' => 'quan_ao'],
    3 => ['id' => 3, 'name' => 'Áo khoác',      'gender' => 'unisex', 'type' => 'quan_ao'],
    4 => ['id' => 4, 'name' => 'Giày sandal',   'gender' => 'unisex', 'type' => 'giay_dep']
];

$low_stock_count = (int)($invStats['low_stock_count'] ?? 0);
$total_units     = (int)($invStats['total_units'] ?? 0);
$total_sku       = (int)($invStats['total_sku'] ?? count($lbn_inventory_list));
$summary_text    = (string)($invStats['summary_text'] ?? 'Váy: 0 • Bộ thun: 0 • Khoác: 0 • Sandal: 0');
?>

<div class="container-fluid my-4 px-lg-5" id="lbn_admin_inventory_section">
    <!-- Header Bàn Làm Việc Quản Lý Sản Phẩm & Tồn Kho -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-boxes-stacked text-peach mr-2"></i>Bàn Làm Việc Quản Lý Sản Phẩm &amp; Tồn Kho
            </h4>
            <small class="text-muted-custom">
                Quản trị danh mục, sản phẩm và cảnh báo tồn kho tự động (&le; 5)
            </small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap fk-btn-toolbar">
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                <i class="fa-solid fa-store mr-1"></i> Xem Cửa Hàng
            </a>
            <?php if ($isAdminUser): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Bảng Điều Khiển
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                    <i class="fa-solid fa-boxes-packing mr-1"></i> Bàn Nhân Viên
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 4 Thẻ Thống Kê Nhanh Kho Hàng -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold text-uppercase">Tổng Mã Sản Phẩm</small>
                        <h3 class="font-weight-bold text-slate mb-0"><?= $total_sku ?> SKU</h3>
                        <small class="text-success font-weight-bold">4 danh mục hoạt động</small>
                    </div>
                    <span class="lbn-inv-thumb">
                        <i class="fa-solid fa-tags text-peach h4 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold text-uppercase">Tổng Tồn Kho Sẵn Sàng</small>
                        <h3 class="font-weight-bold text-slate mb-0"><?= number_format($total_units) ?> SP</h3>
                        <small class="text-muted-custom">Đang có sẵn trong kho</small>
                    </div>
                    <span class="lbn-inv-thumb">
                        <i class="fa-solid fa-warehouse text-info h4 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-sm-0">
            <div class="card card-pastel p-3 shadow-sm h-100 border-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-danger font-weight-bold text-uppercase">Cảnh Báo Sắp Hết Hàng (&le; 5)</small>
                        <h3 class="font-weight-bold text-danger mb-0">
                            <span id="lbn_lowStockCounter"><?= $low_stock_count ?></span> mã SP
                        </h3>
                        <small class="text-danger font-weight-bold">Cần nhập bổ sung ngay</small>
                    </div>
                    <span class="lbn-inv-thumb fk-bg-soft-danger">
                        <i class="fa-solid fa-triangle-exclamation text-danger h4 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="min-w-0 pr-2">
                        <small class="text-muted-custom font-weight-bold text-uppercase">Thống Kê Theo Danh Mục</small>
                        <div class="small font-weight-bold text-slate mt-1">
                            <?= htmlspecialchars($summary_text) ?>
                        </div>
                        <small class="text-mint-dark font-weight-bold">4 nhóm sản phẩm</small>
                    </div>
                    <span class="lbn-inv-thumb">
                        <i class="fa-solid fa-chart-pie text-success h4 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI (8 CỘT): BẢNG DỮ LIỆU SẢN PHẨM & TỒN KHO -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-3">
                        <div class="fk-header-title-grow">
                            <h6 class="font-weight-bold mb-0 text-slate">
                                Bảng Thống Kê Sản Phẩm &amp; Kiểm Soát Tồn Kho
                            </h6>
                            <small class="text-muted-custom">
                                Tự động gắn huy hiệu đỏ <strong>"Sắp hết hàng"</strong> khi số lượng tồn kho &le; 5
                            </small>
                        </div>
                        <?php if ($isAdminUser): ?>
                            <button type="button" onclick="lbnResetProductForm()" class="btn btn-pastel-primary btn-sm px-3 text-nowrap flex-shrink-0">
                                <i class="fa-solid fa-plus mr-1"></i> Thêm SP Mới
                            </button>
                        <?php else: ?>
                            <span class="badge badge-sky px-3 py-2 rounded-pill text-nowrap flex-shrink-0">
                                <i class="fa-solid fa-boxes-packing mr-1"></i>Chế độ Nhân viên: Chỉ Nhập thêm kho
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Thanh lọc nhanh trong Kho -->
                    <div class="form-row">
                        <div class="col-12 col-md-5 mb-2 mb-md-0">
                            <input type="text" id="lbn_invSearchInput" class="form-control form-control-sm rounded-pill"
                                   placeholder="Tìm theo Mã SP (FK-LBN...) hoặc Tên SP...">
                        </div>
                        <div class="col-6 col-md-4">
                            <select id="lbn_invFilterCategory" class="custom-select custom-select-sm rounded-pill">
                                <option value="all">-- Tất cả danh mục --</option>
                                <?php foreach ($lbn_admin_categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <select id="lbn_invFilterStockStatus" class="custom-select custom-select-sm rounded-pill">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="low">Sắp hết hàng (&le; 5)</option>
                                <option value="available">Còn hàng (&gt; 5)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mã SP</th>
                                    <th class="text-center">Ảnh</th>
                                    <th>Tên Sản Phẩm &amp; Chất Liệu</th>
                                    <th>Danh Mục</th>
                                    <th class="text-right">Đơn Giá</th>
                                    <th class="text-center">Tồn Kho &amp; Cảnh Báo</th>
                                    <th class="text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="lbn_inventoryBody">
                                <?php foreach ($lbn_inventory_list as $prod): ?>
                                    <tr id="lbn_invRow_<?= $prod['id'] ?>"
                                        class="lbn-inv-row"
                                        data-category="<?= $prod['category_id'] ?>"
                                        data-stock="<?= $prod['stock_quantity'] ?>"
                                        data-search="<?= htmlspecialchars($prod['sku'] . ' ' . $prod['name'] . ' ' . $prod['category_name']) ?>">

                                        <td class="font-weight-bold text-nowrap">
                                            <span class="text-peach font-weight-bold"><?= htmlspecialchars($prod['sku']) ?></span>
                                            <div class="small text-muted-custom">Mã SP #<?= $prod['id'] ?></div>
                                        </td>

                                        <td class="text-center">
                                            <span class="lbn-inv-thumb"><img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="lbn-thumb-img"></span>
                                            <?php $invImgCount = max(1, (int)($prod['image_count'] ?? 1)); ?>
                                            <div class="mt-1">
                                                <span class="badge badge-light border text-muted-custom fk-fs-075">
                                                    <i class="fa-regular fa-images mr-1 text-peach"></i><?= $invImgCount ?> ảnh
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="font-weight-bold text-slate">
                                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $prod['id'] ?>" class="text-slate text-decoration-none">
                                                    <?= htmlspecialchars($prod['name']) ?>
                                                </a>
                                            </div>
                                            <small class="text-muted-custom d-block">
                                                <i class="fa-solid fa-leaf text-success mr-1"></i><?= htmlspecialchars($prod['material']) ?>
                                            </small>
                                            <?php $rowColors = !empty($prod['colors']) && is_array($prod['colors']) ? $prod['colors'] : []; ?>
                                            <?php if (!empty($rowColors)): ?>
                                                <div class="d-flex flex-wrap align-items-center mt-1">
                                                    <?php foreach ($rowColors as $rc): ?>
                                                        <span class="badge badge-light border rounded-pill mr-1 mb-1 py-0 px-2 d-inline-flex align-items-center fk-fs-075">
                                                            <span class="d-inline-block rounded-circle mr-1 fk-color-dot" data-fk-bg="<?= htmlspecialchars($rc['code'] ?? '#FAF8F5') ?>"></span>
                                                            <?= htmlspecialchars($rc['name'] ?? '') ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-nowrap">
                                            <span class="badge badge-pill badge-sky px-2 py-1">
                                                <?= htmlspecialchars($prod['category_name']) ?>
                                            </span>
                                            <div class="small text-muted-custom mt-1"><?= htmlspecialchars($prod['type_label']) ?></div>
                                        </td>

                                        <td class="text-right font-weight-bold text-peach text-nowrap">
                                            <?= number_format($prod['price'], 0, ',', '.') ?> đ
                                        </td>

                                        <td class="text-center fk-minw-170">
                                            <div id="lbn_stockBadgeCell_<?= $prod['id'] ?>" class="mb-2">
                                                <?php if ($prod['stock_quantity'] <= 5): ?>
                                                    <span class="badge badge-danger lbn-badge-low-stock px-2 py-1 text-nowrap">
                                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>Sắp hết hàng (<?= $prod['stock_quantity'] ?>)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-mint px-2 py-1 rounded-pill text-nowrap">
                                                        <i class="fa-solid fa-circle-check mr-1"></i>Còn hàng (<?= $prod['stock_quantity'] ?>)
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="input-group input-group-sm justify-content-center mx-auto fk-w-125">
                                                <input type="number" id="lbn_stockInput_<?= $prod['id'] ?>"
                                                       class="form-control text-center font-weight-bold"
                                                       value="<?= $prod['stock_quantity'] ?>" min="0">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                            title="Cập nhật tồn kho"
                                                            onclick="lbnQuickUpdateStock(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['sku'], ENT_QUOTES) ?>')">
                                                        <i class="fa-solid fa-rotate"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center text-nowrap">
                                            <?php if ($isAdminUser): ?>
                                                <button type="button"
                                                        class="btn btn-outline-primary btn-sm rounded-pill px-2"
                                                        title="Chỉnh sửa sản phẩm"
                                                        data-id="<?= $prod['id'] ?>"
                                                        data-sku="<?= htmlspecialchars($prod['sku']) ?>"
                                                        data-name="<?= htmlspecialchars($prod['name']) ?>"
                                                        data-cat-id="<?= $prod['category_id'] ?>"
                                                        data-gender="<?= $prod['gender'] ?>"
                                                        data-price="<?= $prod['price'] ?>"
                                                        data-stock="<?= $prod['stock_quantity'] ?>"
                                                        data-type="<?= $prod['type'] ?>"
                                                        data-material="<?= htmlspecialchars($prod['material']) ?>"
                                                        data-gallery="<?= htmlspecialchars(json_encode($prod['gallery'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>"
                                                        data-colors="<?= htmlspecialchars(json_encode($prod['colors'] ?? [], JSON_UNESCAPED_UNICODE)) ?>"
                                                        onclick="lbnEditInventoryProduct(this)">
                                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                                </button>
                                            <?php else: ?>
                                                <button type="button"
                                                        class="btn btn-pastel-primary btn-sm rounded-pill px-3"
                                                        title="Nhập thêm số lượng tồn kho"
                                                        onclick="lbnQuickUpdateStock(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['sku'], ENT_QUOTES) ?>')">
                                                    <i class="fa-solid fa-boxes-packing mr-1"></i> Nhập thêm kho
                                                </button>
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

        <!-- CỘT PHẢI (4 CỘT): FORM THÊM / SỬA SẢN PHẨM (ADMIN) HOẶC NHẬP KHO (NHÂN VIÊN) -->
        <div class="col-12 col-xl-4 mb-4">
            <?php if ($isAdminUser): ?>
            <div class="card card-pastel shadow-sm" id="lbn_adminProductFormCard">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <h6 class="font-weight-bold mb-0 text-slate fk-header-title-grow" id="lbn_formModeTitle">
                        <i class="fa-solid fa-plus-circle text-peach mr-1"></i> Thêm Sản Phẩm Mới
                    </h6>
                    <button type="button" id="lbn_btnResetForm" onclick="lbnResetProductForm()"
                            class="btn btn-light btn-sm rounded-pill border d-none text-nowrap flex-shrink-0">
                        Hủy sửa
                    </button>
                </div>

                <div class="card-body p-4 pt-0">
                    <form id="lbn_formAdminProduct" enctype="multipart/form-data">
                        <input type="hidden" id="lbn_formProductId" name="product_id" value="">

                        <div class="form-row">
                            <div class="form-group col-12 col-sm-5">
                                <label class="small font-weight-bold text-slate mb-1 text-nowrap">Mã SKU:</label>
                                <input type="text" id="lbn_formProductSku" name="sku" class="form-control form-control-sm"
                                       placeholder="VD: FK-LBN-10" required>
                            </div>
                            <div class="form-group col-12 col-sm-7">
                                <label class="small font-weight-bold text-slate mb-1 text-nowrap">Danh mục:</label>
                                <select id="lbn_formProductCategory" name="category_id" class="custom-select custom-select-sm" required>
                                    <option value="1">Váy công chúa</option>
                                    <option value="2">Bộ thun</option>
                                    <option value="3">Áo khoác</option>
                                    <option value="4">Giày sandal</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1">Tên sản phẩm:</label>
                            <input type="text" id="lbn_formProductName" name="name" class="form-control form-control-sm"
                                   placeholder="Nhập tên sản phẩm thời trang cho bé..." required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12 col-sm-4">
                                <label class="small font-weight-bold text-slate mb-1 text-nowrap">Giới tính:</label>
                                <select id="lbn_formProductGender" name="gender" class="custom-select custom-select-sm">
                                    <option value="girl">Bé gái</option>
                                    <option value="boy">Bé trai</option>
                                    <option value="unisex">Unisex</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-sm-4">
                                <label class="small font-weight-bold text-slate mb-1 text-nowrap">Đơn giá (VNĐ):</label>
                                <input type="number" id="lbn_formProductPrice" name="price" class="form-control form-control-sm"
                                       placeholder="250000" min="10000" step="5000" required>
                            </div>
                            <div class="form-group col-12 col-sm-4">
                                <label class="small font-weight-bold text-slate mb-1 text-nowrap">Tồn kho:</label>
                                <input type="number" id="lbn_formProductStock" name="stock_quantity" class="form-control form-control-sm"
                                       placeholder="20" min="0" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1 d-block">
                                Loại kích cỡ áp dụng:
                            </label>
                            <div class="p-2 rounded bg-milk-cream border">
                                <div class="d-flex justify-content-around mb-1">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="lbn_sizeTypeClothing" name="lbn_size_type"
                                               class="custom-control-input" value="quan_ao" checked
                                               onchange="lbnToggleSizeTypeGuide('quan_ao')">
                                        <label class="custom-control-label small font-weight-bold text-slate" for="lbn_sizeTypeClothing">
                                            <i class="fa-solid fa-shirt text-peach mr-1"></i>Quần áo
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="lbn_sizeTypeShoes" name="lbn_size_type"
                                               class="custom-control-input" value="giay_dep"
                                               onchange="lbnToggleSizeTypeGuide('giay_dep')">
                                        <label class="custom-control-label small font-weight-bold text-slate" for="lbn_sizeTypeShoes">
                                            <i class="fa-solid fa-shoe-prints text-warning mr-1"></i>Giày dép
                                        </label>
                                    </div>
                                </div>
                                <small id="lbn_sizeTypeGuideText" class="text-muted-custom d-block text-center">
                                    <i class="fa-solid fa-shirt text-peach mr-1"></i> Dải size áp dụng: <strong>Size 1 (0-1T) đến Size 5 (5-6T)</strong> (Theo chiều cao 50 - 130 cm).
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="small font-weight-bold text-slate mb-0">
                                    Bộ Sưu Tập Ảnh Sản Phẩm (Tải nhiều góc chụp):
                                </label>
                                <span class="badge badge-pastel-mint px-2 py-0 small">4 góc + bổ sung</span>
                            </div>
                            <input type="hidden" id="lbn_formExistingGalleryJson" name="existing_gallery_json" value="">

                            <div class="row no-gutters mx-n1 mb-2">
                                <?php
                                $uploadSlots = [
                                    ['key' => 'front',  'input_id' => 'lbn_productImageInput',       'name' => 'image',        'title' => 'Ảnh chính / Mặt trước', 'icon' => 'fa-shirt'],
                                    ['key' => 'detail', 'input_id' => 'lbn_productDetailImageInput', 'name' => 'image_detail', 'title' => 'Ảnh chi tiết thiết kế', 'icon' => 'fa-magnifying-glass-plus'],
                                    ['key' => 'fabric', 'input_id' => 'lbn_productFabricImageInput', 'name' => 'image_fabric', 'title' => 'Ảnh cận cảnh sớ vải',  'icon' => 'fa-layer-group'],
                                    ['key' => 'model',  'input_id' => 'lbn_productModelImageInput',  'name' => 'image_model',  'title' => 'Ảnh bé mẫu mặc',       'icon' => 'fa-child-reaching'],
                                ];
                                foreach ($uploadSlots as $slotCfg):
                                    $sk = $slotCfg['key'];
                                ?>
                                    <div class="col-6 p-1">
                                        <div class="border rounded bg-light p-2 text-center h-100 position-relative">
                                            <button type="button"
                                                    id="lbn_clearSlotBtn_<?= $sk ?>"
                                                    class="btn btn-light btn-sm border rounded-circle py-0 px-1 position-absolute d-none lbn-slot-clear-btn"
                                                    title="Xóa ảnh góc này"
                                                    onclick="lbnClearGallerySlot('<?= $sk ?>')">
                                                <i class="fa-solid fa-xmark text-danger"></i>
                                            </button>
                                            <label for="<?= $slotCfg['input_id'] ?>" class="mb-0 d-block fk-cursor-pointer">
                                                <img id="lbn_slotPreviewImg_<?= $sk ?>" src="" alt="<?= htmlspecialchars($slotCfg['title']) ?>"
                                                     class="img-fluid rounded border mb-1 d-none lbn-slot-thumb-preview">
                                                <div id="lbn_slotPlaceholder_<?= $sk ?>" class="py-2 text-muted-custom">
                                                    <i class="fa-solid <?= $slotCfg['icon'] ?> text-peach h5 mb-1 d-block"></i>
                                                </div>
                                                <span class="d-block font-weight-bold text-slate fk-fs-075"><?= htmlspecialchars($slotCfg['title']) ?></span>
                                                <small id="lbn_slotStatusText_<?= $sk ?>" class="text-muted-custom d-block text-truncate fk-fs-070">Chọn ảnh</small>
                                            </label>
                                            <input type="file"
                                                   id="<?= $slotCfg['input_id'] ?>"
                                                   name="<?= $slotCfg['name'] ?>"
                                                   class="d-none lbn-slot-file-input"
                                                   data-slot="<?= $sk ?>"
                                                   accept="image/*">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <label for="lbn_productExtraImagesInput" class="lbn-upload-dropzone w-100 mb-0 py-2 px-3 text-center">
                                <i class="fa-solid fa-images text-peach mr-1"></i>
                                <span id="lbn_uploadFileName" class="small font-weight-bold text-slate">
                                    Chọn thêm nhiều ảnh bổ sung cùng lúc (JPG, PNG, WEBP)
                                </span>
                                <div id="lbn_uploadPreviewBadge" class="mt-1 d-none">
                                    <span class="badge badge-mint px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-check mr-1"></i>Đã chọn ảnh sẵn sàng tải lên
                                    </span>
                                </div>
                            </label>
                            <input type="file" id="lbn_productExtraImagesInput" name="gallery_images[]" class="d-none" accept="image/*" multiple>
                            <div id="lbn_extraImagesPreviewList" class="d-flex flex-wrap mt-2"></div>
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="small font-weight-bold text-slate mb-0">
                                    Màu Sắc Trang Phục (Chọn nhiều màu):
                                </label>
                                <small class="text-muted-custom" id="lbn_selectedColorsCountLabel">2 màu</small>
                            </div>
                            <input type="hidden" id="lbn_formColorsJson" name="colors_json" value="">

                            <div class="d-flex flex-wrap mb-2" id="lbn_presetColorPalette">
                                <?php
                                $presetColors = [
                                    ['name' => 'Trắng Sữa',    'code' => '#FAF8F5'],
                                    ['name' => 'Hồng Phấn',    'code' => '#FFD9D4'],
                                    ['name' => 'Xanh Mint',    'code' => '#A8E6CF'],
                                    ['name' => 'Vàng Kem',     'code' => '#FFF3B0'],
                                    ['name' => 'Xanh Denim',   'code' => '#A2D2FF'],
                                    ['name' => 'Tím Lavender', 'code' => '#E0C3FC'],
                                    ['name' => 'Cam Đào',      'code' => '#FFAD9F'],
                                    ['name' => 'Be Nude',      'code' => '#E6CCB2'],
                                    ['name' => 'Đỏ Berry',     'code' => '#F28482'],
                                    ['name' => 'Đen Charcoal', 'code' => '#3D405B'],
                                ];
                                foreach ($presetColors as $pc):
                                ?>
                                    <button type="button"
                                            class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 fk-fs-075 lbn-preset-color-btn"
                                            data-color-name="<?= htmlspecialchars($pc['name']) ?>"
                                            data-color-code="<?= htmlspecialchars($pc['code']) ?>"
                                            onclick="lbnTogglePresetProductColor('<?= htmlspecialchars($pc['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($pc['code'], ENT_QUOTES) ?>')">
                                        <span class="d-inline-block rounded-circle mr-1 fk-color-dot" data-fk-bg="<?= htmlspecialchars($pc['code']) ?>"></span>
                                        <?= htmlspecialchars($pc['name']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <div class="input-group input-group-sm mb-2">
                                <div class="input-group-prepend">
                                    <input type="color" id="lbn_customColorPicker" class="form-control p-1 fk-color-picker-input" value="#FFD9D4" title="Chọn mã màu tùy chỉnh">
                                </div>
                                <input type="text" id="lbn_customColorName" class="form-control" placeholder="Nhập tên màu tùy chỉnh (VD: Xanh Bơ, Đỏ Đô...)">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary font-weight-bold" onclick="lbnAddCustomProductColor()">
                                        <i class="fa-solid fa-plus mr-1"></i>Thêm màu
                                    </button>
                                </div>
                            </div>

                            <div class="p-2 rounded border bg-light">
                                <small class="text-muted-custom d-block mb-1">Các màu sẽ hiển thị cho khách hàng chọn:</small>
                                <div id="lbn_selectedColorsList" class="d-flex flex-wrap"></div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-1">
                                <label class="small font-weight-bold text-slate mb-0 min-w-0" for="lbn_formProductMaterial">
                                    Mô tả chất liệu an toàn cho bé:
                                </label>
                                <span class="badge lbn-badge-bamboo py-0 px-2 text-nowrap flex-shrink-0">OEKO-TEX® 100</span>
                            </div>
                            <textarea id="lbn_formProductMaterial" name="description" rows="3" class="form-control form-control-sm mb-2"
                                      placeholder="Mô tả thành phần sợi tre kháng khuẩn, độ co giãn, phụ liệu cúc bấm an toàn..." required>100% sợi tre hữu cơ kháng khuẩn tự nhiên 99.8%, thấm hút mồ hôi gấp 2 lần cotton.</textarea>

                            <div class="d-flex flex-wrap fk-btn-toolbar">
                                <button type="button" onclick="lbnAppendSafeMaterialTag('100% Sợi tre kháng khuẩn')"
                                        class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-mint-dark text-nowrap">
                                    + Sợi tre kháng khuẩn
                                </button>
                                <button type="button" onclick="lbnAppendSafeMaterialTag('Không chứa Formaldehyde & BPA')"
                                        class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                                    + Không BPA
                                </button>
                                <button type="button" onclick="lbnAppendSafeMaterialTag('Đế cao su non chống trượt')"
                                        class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                                    + Đế cao su non
                                </button>
                            </div>
                        </div>

                        <button type="submit" id="lbn_btnSubmitProduct" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Thêm Sản Phẩm Vào Kho
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card card-pastel shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <h6 class="font-weight-bold mb-0 text-slate fk-header-title-grow">
                        <i class="fa-solid fa-boxes-packing text-peach mr-2"></i>Nhập Thêm Kho Hàng
                    </h6>
                    <span class="badge badge-sky px-2 py-1 rounded-pill text-nowrap flex-shrink-0">Nhân viên kho</span>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info small py-2 mb-3">
                        <i class="fa-solid fa-shield-halved mr-1"></i><strong>Nghiệp vụ Nhân viên kho:</strong> Bạn được cấp quyền cập nhật và nhập bổ sung số lượng tồn kho cho các mã sản phẩm hiện có. Quyền tạo mới hoặc chỉnh sửa thông tin sản phẩm thuộc về Quản trị viên.
                    </div>
                    <form id="lbn_formStaffQuickRestock" onsubmit="return lbnSubmitStaffQuickRestock(event)">
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lbn_staffRestockProductId">Chọn sản phẩm cần nhập thêm kho:</label>
                            <select id="lbn_staffRestockProductId" class="custom-select custom-select-sm">
                                <?php foreach ($lbn_inventory_list as $prod): ?>
                                    <option value="<?= $prod['id'] ?>" data-sku="<?= htmlspecialchars($prod['sku']) ?>">
                                        <?= htmlspecialchars($prod['sku'] . ' - ' . $prod['name']) ?> (Hiện có: <?= $prod['stock_quantity'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lbn_staffRestockQty">Số lượng tồn kho mới (&ge; 0):</label>
                            <input type="number" id="lbn_staffRestockQty" class="form-control form-control-sm font-weight-bold" value="25" min="0" required>
                            <small class="text-muted-custom">Hệ thống tự động cảnh báo đỏ nếu số lượng &le; 5 sản phẩm.</small>
                        </div>
                        <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                            <i class="fa-solid fa-rotate mr-1"></i> Xác Nhận Nhập Thêm Kho
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
