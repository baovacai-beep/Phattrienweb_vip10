<?php
$page_title = "Bàn Làm Việc Nhân Viên & Duyệt Đơn Hàng";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();
require_once __DIR__ . "/../../models/OrderModel.php";

$staffOrderModel = new OrderModel();
$lnkt_staff_orders = $staffOrderModel->getAllOrdersForStaff('all', '');
$statusCounts      = $staffOrderModel->getOrderStatusCounts();
$pollMeta          = $staffOrderModel->getNewOrdersSince(0);
$maxOrderId        = (int)($pollMeta['max_order_id'] ?? 0);

$highlightOrderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$inspectOrder     = null;
foreach ($lnkt_staff_orders as $ordRow) {
    if ($highlightOrderId > 0 && (int)$ordRow['ma_don_hang'] === $highlightOrderId) {
        $inspectOrder = $ordRow;
        break;
    }
}
if (!$inspectOrder && !empty($lnkt_staff_orders)) {
    $inspectOrder = $lnkt_staff_orders[0];
}

function lnktRenderOrderStatusBadge(string $status): string {
    switch ($status) {
        case 'cho_duyet':
            return '<span class="badge badge-warning text-dark px-2 py-1 rounded-pill"><i class="fa-solid fa-clock mr-1"></i>Chờ duyệt</span>';
        case 'da_xac_nhan':
            return '<span class="badge badge-sky px-2 py-1 rounded-pill"><i class="fa-solid fa-box-open mr-1"></i>Đã xác nhận</span>';
        case 'dang_giao':
            return '<span class="badge badge-primary px-2 py-1 rounded-pill"><i class="fa-solid fa-truck-fast mr-1"></i>Đang giao</span>';
        case 'hoan_tat':
            return '<span class="badge badge-mint px-2 py-1 rounded-pill"><i class="fa-solid fa-circle-check mr-1"></i>Hoàn tất</span>';
        case 'da_huy':
            return '<span class="badge badge-danger px-2 py-1 rounded-pill"><i class="fa-solid fa-ban mr-1"></i>Đã hủy</span>';
        default:
            return '<span class="badge badge-secondary px-2 py-1 rounded-pill">' . htmlspecialchars($status) . '</span>';
    }
}

$inspCode       = $inspectOrder ? ('#' . $inspectOrder['ma_van_don']) : 'Chưa chọn đơn';
$inspReceiver   = $inspectOrder ? (string)$inspectOrder['ten_nguoi_nhan'] : '---';
$inspPhone      = $inspectOrder ? (string)$inspectOrder['so_dien_thoai_nhan'] : '---';
$inspAddress    = $inspectOrder ? trim(implode(', ', array_filter([$inspectOrder['dia_chi_giao_hang'] ?? '', $inspectOrder['quan_huyen'] ?? '', $inspectOrder['tinh_thanh_pho'] ?? '']))) : '---';
$inspStaff      = ($inspectOrder && !empty($inspectOrder['nhan_vien_phu_trach'])) ? (string)$inspectOrder['nhan_vien_phu_trach'] : 'Đang chờ tiếp nhận';
$inspItems      = $inspectOrder ? (string)($inspectOrder['item_summary'] ?? 'Sản phẩm FashionKids') : '---';
$inspSubtotal   = $inspectOrder ? (number_format((float)$inspectOrder['tien_hang_tam_tinh'], 0, ',', '.') . ' đ') : '0 đ';
$inspDiscountV  = $inspectOrder ? (float)($inspectOrder['tien_giam_gia'] ?? 0) : 0;
$inspDiscount   = $inspDiscountV > 0 ? ('-' . number_format($inspDiscountV, 0, ',', '.') . ' đ') : '0 đ';
$inspTotal      = $inspectOrder ? (number_format((float)$inspectOrder['tong_tien_thanh_toan'], 0, ',', '.') . ' đ') : '0 đ';
$inspPm         = $inspectOrder ? strtolower((string)($inspectOrder['phuong_thuc_thanh_toan'] ?? 'cod')) : 'cod';
$inspPs         = $inspectOrder ? strtolower((string)($inspectOrder['trang_thai_thanh_toan'] ?? 'chua_thanh_toan')) : 'chua_thanh_toan';
$inspPayText    = ($inspPm === 'vietqr' ? 'Chuyển khoản VietQR' : 'Thu hộ COD') . ' (' . ($inspPs === 'da_thanh_toan' ? 'Đã thanh toán' : ($inspPm === 'vietqr' ? 'Chờ đối soát CK' : 'Chưa thu tiền')) . ')';
$inspNote       = ($inspectOrder && !empty($inspectOrder['ghi_chu'])) ? (string)$inspectOrder['ghi_chu'] : 'Không có ghi chú thêm.';
?>

<div class="container-fluid my-4 px-lg-5"
     id="lnkt_staff_orders_section"
     data-max-order-id="<?= $maxOrderId ?>">
    <!-- Header Bàn Làm Việc Nhân Viên Vận Hành -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-clipboard-check text-peach mr-2"></i>Bàn Làm Việc Nhân Viên &amp; Điều Phối Đơn Hàng
            </h4>
            <small class="text-muted-custom">
                Tiếp nhận đơn mới thời gian thực, duyệt đóng gói, giao vận và tự động tích điểm VIP khi hoàn tất
            </small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center">
            <span id="new_order_badge"
                  class="badge badge-pill badge-danger px-3 py-2 mr-2 d-none lnkt-new-order-pulse"
                  role="button"
                  onclick="lnktAcknowledgeNewOrders()">
                <i class="fa-solid fa-bell mr-1"></i>Đơn mới: <span id="new_order_count">0</span>
            </span>

            <button type="button" id="lnkt_btnToggleSound" onclick="lnktToggleOrderSound()"
                    class="btn btn-light btn-sm border rounded-pill px-3 mr-2 font-weight-bold text-slate">
                <i class="fa-solid fa-volume-high text-peach mr-1"></i>Âm báo: Bật
            </button>

            <a href="/FashionKids/views/pages/order-tracking.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3 mr-2">
                <i class="fa-solid fa-route mr-1"></i> Xem Tiến Trình Khách Hàng
            </a>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Bảng Điều Khiển
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                    <i class="fa-solid fa-boxes-stacked mr-1"></i> Nhập Thêm Kho
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div id="lnkt_staffOrderAlert" class="alert small py-2 mb-3 d-none" role="alert"></div>

    <!-- 5 Thẻ Thống Kê Thời Gian Thực Theo Trạng Thái Đơn Hàng -->
    <div class="row mb-4">
        <div class="col-6 col-md-4 col-xl mb-3 mb-xl-0">
            <div class="card card-pastel p-3 shadow-sm h-100 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold text-uppercase">1. Chờ Duyệt</small>
                        <h3 class="font-weight-bold text-warning mb-0" id="lnkt_count_cho_duyet"><?= (int)($statusCounts['cho_duyet'] ?? 0) ?></h3>
                        <small class="text-slate">Cần xác nhận ngay</small>
                    </div>
                    <span class="lbn-inv-thumb"><i class="fa-solid fa-clock text-warning h4 mb-0"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl mb-3 mb-xl-0">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold text-uppercase">2. Đã Xác Nhận</small>
                        <h3 class="font-weight-bold text-info mb-0" id="lnkt_count_da_xac_nhan"><?= (int)($statusCounts['da_xac_nhan'] ?? 0) ?></h3>
                        <small class="text-slate">Đang đóng gói</small>
                    </div>
                    <span class="lbn-inv-thumb"><i class="fa-solid fa-box-open text-info h4 mb-0"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl mb-3 mb-xl-0">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold text-uppercase">3. Đang Giao</small>
                        <h3 class="font-weight-bold text-primary mb-0" id="lnkt_count_dang_giao"><?= (int)($statusCounts['dang_giao'] ?? 0) ?></h3>
                        <small class="text-slate">Đã bàn giao bưu cục</small>
                    </div>
                    <span class="lbn-inv-thumb"><i class="fa-solid fa-truck-fast text-primary h4 mb-0"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-xl mb-3 mb-md-0">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold text-uppercase">4. Hoàn Tất</small>
                        <h3 class="font-weight-bold text-success mb-0" id="lnkt_count_hoan_tat"><?= (int)($statusCounts['hoan_tat'] ?? 0) ?></h3>
                        <small class="text-mint-dark font-weight-bold">Đã đối soát &amp; cộng điểm</small>
                    </div>
                    <span class="lbn-inv-thumb"><i class="fa-solid fa-circle-check text-success h4 mb-0"></i></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl">
            <div class="card card-pastel p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold text-uppercase">5. Đã Hủy</small>
                        <h3 class="font-weight-bold text-danger mb-0" id="lnkt_count_da_huy"><?= (int)($statusCounts['da_huy'] ?? 0) ?></h3>
                        <small class="text-slate">Hoàn kho tự động</small>
                    </div>
                    <span class="lbn-inv-thumb"><i class="fa-solid fa-ban text-danger h4 mb-0"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI (8 CỘT): BẢNG XỬ LÝ & CHUYỂN ĐỔI TRẠNG THÁI GIAO VẬN -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-bold text-slate mb-0">
                                Danh Sách Đơn Đặt Hàng &amp; Điều Phối Vận Chuyển Thời Gian Thực
                            </h6>
                            <small class="text-muted-custom">
                                Hệ thống tự động kiểm tra đơn hàng mới mỗi 4 giây • Chọn trạng thái mới và nhấn <strong>Lưu</strong> hoặc nút xử lý nhanh
                            </small>
                        </div>
                        <span class="badge badge-pill badge-peach bg-peach text-white px-3 py-1 mt-2 mt-sm-0">
                            Nhân viên trực: <?= htmlspecialchars($_SESSION['user']['ho_va_ten'] ?? 'Nhân viên FashionKids') ?>
                        </span>
                    </div>

                    <!-- Bộ lọc đơn hàng cho nhân viên -->
                    <div class="form-row">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <input type="text" id="lnkt_staffOrderSearch" class="form-control form-control-sm rounded-pill"
                                   placeholder="Tìm theo Mã đơn (FK-ORD...), Tên phụ huynh, SĐT, Tỉnh thành..."
                                   onkeyup="lnktFilterStaffOrders()">
                        </div>
                        <div class="col-12 col-md-6">
                            <select id="lnkt_staffOrderStatusFilter" class="custom-select custom-select-sm rounded-pill"
                                    onchange="lnktFilterStaffOrders()">
                                <option value="all">-- Tất cả trạng thái đơn hàng (5 trạng thái) --</option>
                                <option value="cho_duyet">1. Chờ duyệt</option>
                                <option value="da_xac_nhan">2. Đã xác nhận</option>
                                <option value="dang_giao">3. Đang giao</option>
                                <option value="hoan_tat">4. Hoàn tất</option>
                                <option value="da_huy">5. Đã hủy</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mã Đơn &amp; Ngày Đặt</th>
                                    <th>Phụ Huynh Nhận Hàng</th>
                                    <th>Khu Vực &amp; Ghi Chú</th>
                                    <th class="text-right">Thanh Toán</th>
                                    <th class="text-center">Trạng Thái Hiện Tại</th>
                                    <th class="text-center fk-minw-215">Cập Nhật Giao Vận</th>
                                </tr>
                            </thead>
                            <tbody id="lnkt_staffOrdersBody">
                                <?php if (empty($lnkt_staff_orders)): ?>
                                    <tr id="lnkt_staffEmptyRow">
                                        <td colspan="6" class="text-center py-4 text-muted-custom">
                                            <i class="fa-solid fa-box-open mr-2"></i>Hiện chưa có đơn đặt hàng nào trên hệ thống.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($lnkt_staff_orders as $ord):
                                    $oid        = (int)$ord['ma_don_hang'];
                                    $ocode      = (string)$ord['ma_van_don'];
                                    $st         = $staffOrderModel->normalizeStatus((string)$ord['trang_thai_don_hang']);
                                    $isLocked   = in_array($st, ['hoan_tat', 'da_huy'], true);
                                    $pm         = strtolower((string)($ord['phuong_thuc_thanh_toan'] ?? 'cod'));
                                    $ps         = strtolower((string)($ord['trang_thai_thanh_toan'] ?? 'chua_thanh_toan'));
                                    $fullAddr   = trim(implode(', ', array_filter([$ord['dia_chi_giao_hang'] ?? '', $ord['quan_huyen'] ?? '', $ord['tinh_thanh_pho'] ?? ''])));
                                    $createdStr = !empty($ord['ngay_dat_hang']) ? date('d/m/Y H:i', strtotime($ord['ngay_dat_hang'])) : '';
                                    $handledBy  = trim((string)($ord['nhan_vien_phu_trach'] ?? '')) ?: 'Chưa tiếp nhận';
                                    $noteStr    = trim((string)($ord['ghi_chu'] ?? ''));
                                    $payLabel   = ($pm === 'vietqr' ? 'Chuyển khoản VietQR' : 'Thu hộ COD') . ' (' . ($ps === 'da_thanh_toan' ? 'Đã thanh toán' : ($pm === 'vietqr' ? 'Chờ đối soát CK' : 'Chưa thu tiền')) . ')';
                                    $subtotalF  = number_format((float)$ord['tien_hang_tam_tinh'], 0, ',', '.') . ' đ';
                                    $discVal    = (float)($ord['tien_giam_gia'] ?? 0);
                                    $discountF  = $discVal > 0 ? ('-' . number_format($discVal, 0, ',', '.') . ' đ') : '0 đ';
                                    $totalF     = number_format((float)$ord['tong_tien_thanh_toan'], 0, ',', '.') . ' đ';
                                ?>
                                    <tr class="lnkt-staff-order-row <?= ($highlightOrderId === $oid) ? 'lnkt-order-highlight-row' : '' ?>"
                                        id="lnkt_orderRow_<?= $oid ?>"
                                        data-order-id="<?= $oid ?>"
                                        data-status="<?= htmlspecialchars($st) ?>"
                                        data-search="<?= htmlspecialchars($ocode . ' ' . $oid . ' ' . $ord['ten_nguoi_nhan'] . ' ' . $ord['so_dien_thoai_nhan'] . ' ' . ($ord['tinh_thanh_pho'] ?? '') . ' ' . $noteStr) ?>">
                                        <td>
                                            <a href="javascript:void(0)"
                                               onclick="lnktInspectStaffOrder(this)"
                                               data-code="<?= htmlspecialchars($ocode) ?>"
                                               data-receiver="<?= htmlspecialchars((string)$ord['ten_nguoi_nhan']) ?>"
                                               data-phone="<?= htmlspecialchars((string)$ord['so_dien_thoai_nhan']) ?>"
                                               data-address="<?= htmlspecialchars($fullAddr) ?>"
                                               data-items="<?= htmlspecialchars((string)($ord['item_summary'] ?? 'Sản phẩm FashionKids')) ?>"
                                               data-subtotal="<?= htmlspecialchars($subtotalF) ?>"
                                               data-discount="<?= htmlspecialchars($discountF) ?>"
                                               data-total="<?= htmlspecialchars($totalF) ?>"
                                               data-staff="<?= htmlspecialchars($handledBy) ?>"
                                               data-payment="<?= htmlspecialchars($payLabel) ?>"
                                               data-note="<?= htmlspecialchars($noteStr !== '' ? $noteStr : 'Không có ghi chú thêm.') ?>"
                                               class="font-weight-bold text-peach text-decoration-none d-block">
                                                #<?= htmlspecialchars($ocode) ?>
                                            </a>
                                            <small class="text-muted-custom d-block"><?= htmlspecialchars($createdStr) ?></small>
                                            <small class="text-slate" id="lnkt_rowStaff_<?= $oid ?>">Phụ trách: <strong><?= htmlspecialchars($handledBy) ?></strong></small>
                                        </td>

                                        <td>
                                            <div class="font-weight-bold text-slate"><?= htmlspecialchars((string)$ord['ten_nguoi_nhan']) ?></div>
                                            <small class="text-muted-custom d-block">
                                                <i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars((string)$ord['so_dien_thoai_nhan']) ?>
                                            </small>
                                            <span class="badge <?= $pm === 'vietqr' ? 'badge-sky' : 'badge-light border' ?> small py-0 px-2 rounded-pill">
                                                <?= $pm === 'vietqr' ? 'VietQR' : 'COD' ?>
                                            </span>
                                            <?php if ($ps === 'da_thanh_toan'): ?>
                                                <span class="badge badge-mint small py-0 px-2 rounded-pill">Đã TT</span>
                                            <?php elseif ($pm === 'vietqr'): ?>
                                                <span class="badge badge-warning text-dark small py-0 px-2 rounded-pill">Chờ CK</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="small font-weight-bold text-slate"><?= htmlspecialchars((string)($ord['tinh_thanh_pho'] ?? '')) ?></div>
                                            <small class="text-muted-custom d-block"><?= htmlspecialchars((string)($ord['quan_huyen'] ?? '')) ?></small>
                                            <?php if ($noteStr !== ''): ?>
                                                <small class="text-info d-block text-truncate fk-ellipsis-220" title="<?= htmlspecialchars($noteStr) ?>">
                                                    <i class="fa-regular fa-note-sticky mr-1"></i><?= htmlspecialchars($noteStr) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-right text-nowrap">
                                            <div class="font-weight-bold text-peach"><?= htmlspecialchars($totalF) ?></div>
                                            <small class="text-muted-custom">Gốc: <?= htmlspecialchars($subtotalF) ?></small>
                                        </td>

                                        <td class="text-center" id="lnkt_orderBadgeCell_<?= $oid ?>">
                                            <?= lnktRenderOrderStatusBadge($st) ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="input-group input-group-sm mb-1">
                                                <select id="lnkt_statusSelect_<?= $oid ?>" class="custom-select font-weight-bold" <?= $isLocked ? 'disabled' : '' ?>>
                                                    <option value="cho_duyet" <?= $st === 'cho_duyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                                                    <option value="da_xac_nhan" <?= $st === 'da_xac_nhan' ? 'selected' : '' ?>>Đã xác nhận</option>
                                                    <option value="dang_giao" <?= $st === 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                                                    <option value="hoan_tat" <?= $st === 'hoan_tat' ? 'selected' : '' ?>>Hoàn tất</option>
                                                    <option value="da_huy" <?= $st === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            id="lnkt_saveStatusBtn_<?= $oid ?>"
                                                            class="btn btn-pastel-primary px-2"
                                                            title="Lưu trạng thái đơn hàng"
                                                            <?= $isLocked ? 'disabled' : '' ?>
                                                            onclick="lnktUpdateStaffOrderStatus(<?= $oid ?>, '<?= htmlspecialchars($ocode, ENT_QUOTES) ?>')">
                                                        <i class="fa-solid fa-check"></i> Lưu
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="lnkt_quickActionCell_<?= $oid ?>">
                                                <?php if ($st === 'cho_duyet'): ?>
                                                    <button type="button"
                                                            class="btn btn-outline-info btn-sm btn-block rounded-pill py-0 small font-weight-bold"
                                                            onclick="lnktUpdateStaffOrderStatus(<?= $oid ?>, '<?= htmlspecialchars($ocode, ENT_QUOTES) ?>', 'da_xac_nhan')">
                                                        <i class="fa-solid fa-box-open mr-1"></i>Tiếp nhận &amp; Xác nhận
                                                    </button>
                                                <?php elseif ($st === 'da_xac_nhan'): ?>
                                                    <button type="button"
                                                            class="btn btn-outline-primary btn-sm btn-block rounded-pill py-0 small font-weight-bold"
                                                            onclick="lnktUpdateStaffOrderStatus(<?= $oid ?>, '<?= htmlspecialchars($ocode, ENT_QUOTES) ?>', 'dang_giao')">
                                                        <i class="fa-solid fa-truck-fast mr-1"></i>Chuyển Giao Hàng
                                                    </button>
                                                <?php elseif ($st === 'dang_giao'): ?>
                                                    <button type="button"
                                                            class="btn btn-outline-success btn-sm btn-block rounded-pill py-0 small font-weight-bold"
                                                            onclick="lnktUpdateStaffOrderStatus(<?= $oid ?>, '<?= htmlspecialchars($ocode, ENT_QUOTES) ?>', 'hoan_tat')">
                                                        <i class="fa-solid fa-circle-check mr-1"></i>Hoàn Tất &amp; Tích Điểm
                                                    </button>
                                                <?php else: ?>
                                                    <small class="text-muted-custom">Đã khóa chỉnh sửa</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI (4 CỘT): PHIẾU KIỂM ĐƠN & ĐIỀU PHỐI NHANH -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm sticky-top fk-sticky-85">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-file-invoice text-peach mr-1"></i> Phiếu Chi Tiết Vận Đơn Đang Chọn
                    </h6>
                    <span class="badge badge-pill badge-sky px-2 py-1" id="lnkt_inspectCode"><?= htmlspecialchars($inspCode) ?></span>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="p-3 rounded bg-milk-cream border mb-3 small">
                        <div class="mb-2">
                            <span class="text-muted-custom">Khách hàng nhận:</span>
                            <strong class="text-slate float-right" id="lnkt_inspectReceiver"><?= htmlspecialchars($inspReceiver) ?></strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted-custom">Số điện thoại:</span>
                            <strong class="text-peach float-right" id="lnkt_inspectPhone"><?= htmlspecialchars($inspPhone) ?></strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted-custom d-block">Địa chỉ giao vận:</span>
                            <strong class="text-slate" id="lnkt_inspectAddress"><?= htmlspecialchars($inspAddress) ?></strong>
                        </div>
                        <div class="mb-2 pt-2 border-top">
                            <span class="text-muted-custom d-block">Thanh toán:</span>
                            <strong class="text-slate" id="lnkt_inspectPayment"><?= htmlspecialchars($inspPayText) ?></strong>
                        </div>
                        <div class="mb-2 pt-2 border-top">
                            <span class="text-muted-custom d-block">Ghi chú đơn hàng:</span>
                            <span class="text-slate" id="lnkt_inspectNote"><?= htmlspecialchars($inspNote) ?></span>
                        </div>
                        <div class="pt-2 border-top">
                            <span class="text-muted-custom d-block">Nhân viên thụ lý:</span>
                            <strong class="text-mint-dark" id="lnkt_inspectStaff"><?= htmlspecialchars($inspStaff) ?></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small font-weight-bold text-slate mb-1">Danh mục sản phẩm đóng gói:</label>
                        <div class="p-3 rounded border bg-white small font-weight-bold text-slate" id="lnkt_inspectItems">
                            <?= htmlspecialchars($inspItems) ?>
                        </div>
                    </div>

                    <div class="border rounded p-3 bg-white mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted-custom">Tiền tạm tính:</span>
                            <strong id="lnkt_inspectSubtotal"><?= htmlspecialchars($inspSubtotal) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted-custom">Giảm giá VIP:</span>
                            <strong class="text-success" id="lnkt_inspectDiscount"><?= htmlspecialchars($inspDiscount) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="font-weight-bold text-slate">Tổng thanh toán:</span>
                            <strong class="text-peach h6 mb-0" id="lnkt_inspectTotal"><?= htmlspecialchars($inspTotal) ?></strong>
                        </div>
                    </div>

                    <button type="button" onclick="window.print()" class="btn btn-pastel-outline btn-block font-weight-bold py-2">
                        <i class="fa-solid fa-print mr-1"></i> In Phiếu Giao Hàng &amp; Mã Vạch
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
