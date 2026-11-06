<?php
$page_title = "Bàn Làm Việc Nhân Viên – Quản Trị & Điều Phối Đơn Hàng (MH 10)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh sách đơn hàng trên bàn làm việc nhân viên vận hành (lnkt_orders)
$lnkt_staff_orders = [
    [
        'id'            => 892,
        'order_code'    => '#FK-2026-0892',
        'receiver_name' => 'Võ Thị Thanh Thảo',
        'receiver_phone'=> '0918889999',
        'location'      => 'Quận Bình Thạnh, TP. Hồ Chí Minh',
        'items_summary' => 'Đầm Sợi Tre Hoa Cúc (x2), Bộ Thun Khủng Long (x1), Sandal (x1)',
        'subtotal'      => 1025000,
        'discount'      => 102500,
        'total_amount'  => 922500,
        'status'        => 'hoan_tat',
        'status_text'   => 'Hoàn tất',
        'badge_class'   => 'badge-mint',
        'handled_by'    => 'Lê Nguyễn Khánh Trình',
        'created_at'    => '06/11/2026 09:30'
    ],
    [
        'id'            => 893,
        'order_code'    => '#FK-2026-0893',
        'receiver_name' => 'Trần Thị Bông',
        'receiver_phone'=> '0903112233',
        'location'      => 'Quận Cầu Giấy, Thành phố Hà Nội',
        'items_summary' => 'Bodysuit Sợi Tre Sơ Sinh (x3), Mũ Che Thóp Organic (x2)',
        'subtotal'      => 685000,
        'discount'      => 0,
        'total_amount'  => 685000,
        'status'        => 'cho_duyet',
        'status_text'   => 'Chờ duyệt',
        'badge_class'   => 'badge-warning',
        'handled_by'    => 'Chưa phân công',
        'created_at'    => '06/11/2026 11:15'
    ],
    [
        'id'            => 894,
        'order_code'    => '#FK-2026-0894',
        'receiver_name' => 'Nguyễn Minh Hoàng',
        'receiver_phone'=> '0987654321',
        'location'      => 'Quận Hải Châu, Thành phố Đà Nẵng',
        'items_summary' => 'Bộ Thun Sợi Tre Bé Trai Size 4 (x2), Giày Sneaker Canvas Size 26 (x1)',
        'subtotal'      => 740000,
        'discount'      => 37000,
        'total_amount'  => 703000,
        'status'        => 'da_xac_nhan',
        'status_text'   => 'Đã xác nhận',
        'badge_class'   => 'badge-sky',
        'handled_by'    => 'Lê Nguyễn Khánh Trình',
        'created_at'    => '06/11/2026 13:20'
    ],
    [
        'id'            => 895,
        'order_code'    => '#FK-2026-0895',
        'receiver_name' => 'Lê Hoàng Bảo Ngọc',
        'receiver_phone'=> '0938556677',
        'location'      => 'TP. Thủ Đức, TP. Hồ Chí Minh',
        'items_summary' => 'Đầm Công Chúa Cotton Hữu Cơ Size 3 (x1), Giày Búp Bê Size 24 (x1)',
        'subtotal'      => 560000,
        'discount'      => 84000,
        'total_amount'  => 476000,
        'status'        => 'dang_giao',
        'status_text'   => 'Đang giao',
        'badge_class'   => 'badge-info',
        'handled_by'    => 'Lê Nguyễn Khánh Trình',
        'created_at'    => '06/11/2026 15:05'
    ],
    [
        'id'            => 896,
        'order_code'    => '#FK-2026-0896',
        'receiver_name' => 'Phạm Quốc Khánh',
        'receiver_phone'=> '0977123456',
        'location'      => 'TP. Dĩ An, Tỉnh Bình Dương',
        'items_summary' => 'Áo Khoác Nắng Sợi Tre UV50+ Size 5 (x1)',
        'subtotal'      => 320000,
        'discount'      => 0,
        'total_amount'  => 320000,
        'status'        => 'da_huy',
        'status_text'   => 'Đã hủy',
        'badge_class'   => 'badge-danger',
        'handled_by'    => 'Lê Nguyễn Khánh Trình',
        'created_at'    => '05/11/2026 19:40'
    ]
];
?>

<div class="container-fluid my-4 px-lg-5" id="lnkt_staff_orders_workspace">
    <!-- Tiêu đề bàn làm việc Nhân viên Vận hành -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-boxes-packing text-peach mr-2"></i>Bàn Làm Việc Vận Hành – Quản Trị Đơn Hàng (MH 10)
            </h4>
            <small class="text-muted-custom">
                Xử lý, duyệt đơn và cập nhật chuyển đổi trạng thái giao vận theo thời gian thực qua <code>lnkt_update_order_status</code>
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="/FashionKids/views/pages/admin-size-charts.php" class="btn btn-pastel-outline btn-sm font-weight-bold mr-2">
                <i class="fa-solid fa-ruler-combined mr-1"></i> Quản Trị Bảng Size (MH 14)
            </a>
            <a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                <i class="fa-solid fa-images mr-1"></i> Quản Trị Banner (MH 23)
            </a>
        </div>
    </div>

    <!-- Thẻ Thống Kê Nhanh Theo Trạng Thái Đơn Hàng -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="card card-pastel p-3 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block">ĐƠN CHỜ DUYỆT</small>
                        <h4 class="font-weight-bold text-warning mb-0">01</h4>
                    </div>
                    <span class="rounded-circle p-3 bg-milk-cream text-warning">
                        <i class="fa-solid fa-clock-rotate-left h5 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="card card-pastel p-3 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block">ĐÃ XÁC NHẬN &amp; ĐÓNG GÓI</small>
                        <h4 class="font-weight-bold text-info mb-0">01</h4>
                    </div>
                    <span class="rounded-circle p-3 bg-milk-cream text-info">
                        <i class="fa-solid fa-box-open h5 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-pastel p-3 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block">ĐANG GIAO VẬN</small>
                        <h4 class="font-weight-bold text-primary mb-0">01</h4>
                    </div>
                    <span class="rounded-circle p-3 bg-milk-cream text-primary">
                        <i class="fa-solid fa-truck-fast h5 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-pastel p-3 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold d-block">HOÀN TẤT GIAO HÀNG</small>
                        <h4 class="font-weight-bold text-success mb-0">01</h4>
                    </div>
                    <span class="rounded-circle p-3 bg-mint-soft text-mint-dark">
                        <i class="fa-solid fa-circle-check h5 mb-0"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ Lọc Trạng Thái Trực Quan & Bảng Điều Phối Đơn Hàng -->
    <div class="card card-pastel shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="mb-2 mb-lg-0">
                    <button type="button" class="btn btn-sm btn-pastel-primary lnkt-staff-filter-btn active mr-1 mb-1" onclick="lnktFilterStaffOrders('all', this)">
                        Tất cả (<?= count($lnkt_staff_orders) ?>)
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill font-weight-bold lnkt-staff-filter-btn mr-1 mb-1" onclick="lnktFilterStaffOrders('cho_duyet', this)">
                        Chờ duyệt
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill font-weight-bold lnkt-staff-filter-btn mr-1 mb-1" onclick="lnktFilterStaffOrders('da_xac_nhan', this)">
                        Đã xác nhận
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill font-weight-bold lnkt-staff-filter-btn mr-1 mb-1" onclick="lnktFilterStaffOrders('dang_giao', this)">
                        Đang giao
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill font-weight-bold lnkt-staff-filter-btn mr-1 mb-1" onclick="lnktFilterStaffOrders('hoan_tat', this)">
                        Hoàn tất
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill font-weight-bold lnkt-staff-filter-btn mb-1" onclick="lnktFilterStaffOrders('da_huy', this)">
                        Đã hủy
                    </button>
                </div>

                <div class="text-muted-custom small font-weight-bold">
                    <i class="fa-solid fa-user-check text-peach mr-1"></i> Nhân viên trực ca: <strong>Lê Nguyễn Khánh Trình (#NV02)</strong>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light small">
                        <tr>
                            <th>Mã Vận Đơn</th>
                            <th>Phụ Huynh Nhận Hàng</th>
                            <th>Khu Vực Giao Vận</th>
                            <th>Chi Tiết Sản Phẩm Đặt Mua</th>
                            <th class="text-right">Tổng Thu Thực Tế</th>
                            <th class="text-center">Trạng Thái Hiện Tại</th>
                            <th>Cập Nhật Giao Vận (Real-time)</th>
                            <th class="text-center">Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lnkt_staff_orders as $ord): ?>
                            <tr class="lnkt-staff-order-row" data-status="<?= $ord['status'] ?>">
                                <td>
                                    <div class="font-weight-bold text-slate"><?= htmlspecialchars($ord['order_code']) ?></div>
                                    <small class="text-muted-custom"><?= htmlspecialchars($ord['created_at']) ?></small>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-slate"><?= htmlspecialchars($ord['receiver_name']) ?></div>
                                    <small class="text-muted-custom"><i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($ord['receiver_phone']) ?></small>
                                </td>
                                <td>
                                    <span class="small font-weight-bold text-slate"><?= htmlspecialchars($ord['location']) ?></span>
                                    <small class="d-block text-muted-custom">NV thụ lý: <?= htmlspecialchars($ord['handled_by']) ?></small>
                                </td>
                                <td>
                                    <small class="text-slate font-weight-bold"><?= htmlspecialchars($ord['items_summary']) ?></small>
                                </td>
                                <td class="text-right">
                                    <div class="font-weight-bold text-peach"><?= number_format($ord['total_amount'], 0, ',', '.') ?> đ</div>
                                    <?php if ($ord['discount'] > 0): ?>
                                        <small class="text-success">Đã giảm VIP: -<?= number_format($ord['discount'], 0, ',', '.') ?>đ</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill <?= $ord['badge_class'] ?> px-3 py-1 lnkt-order-status-badge">
                                        <?= htmlspecialchars($ord['status_text']) ?>
                                    </span>
                                </td>
                                <td>
                                    <select class="form-control form-control-sm font-weight-bold" onchange="lnktUpdateStaffOrderStatus('<?= $ord['order_code'] ?>', this)">
                                        <option value="cho_duyet" <?= $ord['status'] === 'cho_duyet' ? 'selected' : '' ?>>1. Chờ duyệt</option>
                                        <option value="da_xac_nhan" <?= $ord['status'] === 'da_xac_nhan' ? 'selected' : '' ?>>2. Đã xác nhận</option>
                                        <option value="dang_giao" <?= $ord['status'] === 'dang_giao' ? 'selected' : '' ?>>3. Đang giao</option>
                                        <option value="hoan_tat" <?= $ord['status'] === 'hoan_tat' ? 'selected' : '' ?>>4. Hoàn tất (Cộng điểm VIP)</option>
                                        <option value="da_huy" <?= $ord['status'] === 'da_huy' ? 'selected' : '' ?>>5. Đã hủy (Hoàn kho)</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <a href="/FashionKids/views/pages/order-tracking.php?status=<?= $ord['status'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Xem tiến trình vận đơn">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once "../layouts/footer.php"; ?>
