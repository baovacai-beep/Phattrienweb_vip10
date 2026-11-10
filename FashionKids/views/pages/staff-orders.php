<?php
$page_title = "Bàn Làm Việc Quản Trị & Duyệt Đơn Hàng";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
Rbac::enforceCurrentViewAccess();

// Danh sách đơn hàng mẫu chuẩn bảng lnkt_orders
$lnkt_staff_orders = [
    [
        'id'               => 202608,
        'order_code'       => 'FK-ORD-202608',
        'user_id'          => 3,
        'vip_tier'         => 'Vàng (-10%)',
        'handled_by'       => 'Lê Nguyễn Khánh Trình',
        'receiver_name'    => 'Võ Thị Thanh Thảo',
        'receiver_phone'   => '0918889999',
        'shipping_address' => 'Số 120 Điện Biên Phủ, Phường 17',
        'district'         => 'Quận Bình Thạnh',
        'province'         => 'TP. Hồ Chí Minh',
        'subtotal'         => 945000,
        'discount_amount'  => 94500,
        'total_amount'     => 850500,
        'status'           => 'cho_duyet',
        'created_at'       => '08/11/2026 08:15',
        'items_summary'    => '1x Váy Công Chúa Sợi Tre (Size 2), 2x Bộ Thun Khủng Long (Size 3), 1x Sandal Tập Đi (Size 22)'
    ],
    [
        'id'               => 202607,
        'order_code'       => 'FK-ORD-202607',
        'user_id'          => 4,
        'vip_tier'         => 'Đồng (0%)',
        'handled_by'       => 'Lê Nguyễn Khánh Trình',
        'receiver_name'    => 'Trần Thị Bông',
        'receiver_phone'   => '0903112233',
        'shipping_address' => 'Số 45 Nguyễn Thị Minh Khai, Phường Bến Nghé',
        'district'         => 'Quận 1',
        'province'         => 'TP. Hồ Chí Minh',
        'subtotal'         => 565000,
        'discount_amount'  => 0,
        'total_amount'     => 565000,
        'status'           => 'da_xac_nhan',
        'created_at'       => '08/11/2026 07:40',
        'items_summary'    => '1x Áo Khoác Cardigan UV (Size 3), 1x Giày Sandal Bé Trai (Size 24)'
    ],
    [
        'id'               => 202606,
        'order_code'       => 'FK-ORD-202606',
        'user_id'          => 6,
        'vip_tier'         => 'Bạc (-5%)',
        'handled_by'       => 'Lê Nguyễn Khánh Trình',
        'receiver_name'    => 'Nguyễn Hoàng Minh Anh',
        'receiver_phone'   => '0987654321',
        'shipping_address' => 'Số 88 Cầu Giấy, Phường Quan Hoa',
        'district'         => 'Quận Cầu Giấy',
        'province'         => 'Thành phố Hà Nội',
        'subtotal'         => 730000,
        'discount_amount'  => 36500,
        'total_amount'     => 693500,
        'status'           => 'dang_giao',
        'created_at'       => '07/11/2026 19:20',
        'items_summary'    => '2x Váy Công Chúa Tùng Xòe Pastel (Size 4)'
    ],
    [
        'id'               => 202605,
        'order_code'       => 'FK-ORD-202605',
        'user_id'          => 7,
        'vip_tier'         => 'Kim Cương (-15%)',
        'handled_by'       => 'Lê Nguyễn Khánh Trình',
        'receiver_name'    => 'Phạm Ngọc Lan Khuê',
        'receiver_phone'   => '0939556677',
        'shipping_address' => 'Số 210 Bạch Đằng, Phường Phước Ninh',
        'district'         => 'Quận Hải Châu',
        'province'         => 'Thành phố Đà Nẵng',
        'subtotal'         => 1120000,
        'discount_amount'  => 168000,
        'total_amount'     => 952000,
        'status'           => 'hoan_tat',
        'created_at'       => '06/11/2026 14:10',
        'items_summary'    => '2x Áo Khoác Hoodie Tai Thỏ (Size 3), 2x Bộ Thun Gấu Nhí (Size 2)'
    ],
    [
        'id'               => 202604,
        'order_code'       => 'FK-ORD-202604',
        'user_id'          => 8,
        'vip_tier'         => 'Đồng (0%)',
        'handled_by'       => 'Lê Nguyễn Khánh Trình',
        'receiver_name'    => 'Lê Quang Hải',
        'receiver_phone'   => '0977123123',
        'shipping_address' => 'Số 15 Đại lộ Hòa Bình',
        'district'         => 'Quận Ninh Kiều',
        'province'         => 'Thành phố Cần Thơ',
        'subtotal'         => 210000,
        'discount_amount'  => 0,
        'total_amount'     => 210000,
        'status'           => 'da_huy',
        'created_at'       => '05/11/2026 10:05',
        'items_summary'    => '1x Giày Sandal Tập Đi Đế Cao Su Non (Size 21) - Khách đổi sang mẫu khác'
    ]
];

// Hàm trả về nhãn & màu sắc badge theo trạng thái đơn
function lnktRenderOrderStatusBadge($status) {
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
?>

<div class="container-fluid my-4 px-lg-5" id="lnkt_staff_orders_section">
    <!-- Header Bàn Làm Việc Nhân Viên Vận Hành -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-clipboard-check text-peach mr-2"></i>Bàn Làm Việc Nhân Viên Quản Trị &amp; Duyệt Đơn Hàng
            </h4>
            <small class="text-muted-custom">
                Quản lý đơn hàng và cập nhật trạng thái xử lý nhanh chóng
            </small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="/FashionKids/views/pages/order-tracking.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3 mr-2">
                <i class="fa-solid fa-route mr-1"></i> Xem Tiến Trình Khách Hàng
            </a>
            <a href="/FashionKids/views/pages/admin-size-charts.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
                <i class="fa-solid fa-ruler-combined mr-1"></i> Quản Trị Bảng Size
            </a>
        </div>
    </div>

    <!-- 5 Thẻ Thống Kê Thời Gian Thực Theo Trạng Thái Đơn Hàng -->
    <div class="row mb-4">
        <div class="col-6 col-md-4 col-xl mb-3 mb-xl-0">
            <div class="card card-pastel p-3 shadow-sm h-100 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted-custom font-weight-bold text-uppercase">1. Chờ Duyệt</small>
                        <h3 class="font-weight-bold text-warning mb-0" id="lnkt_count_cho_duyet">1</h3>
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
                        <h3 class="font-weight-bold text-info mb-0" id="lnkt_count_da_xac_nhan">1</h3>
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
                        <h3 class="font-weight-bold text-primary mb-0" id="lnkt_count_dang_giao">1</h3>
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
                        <h3 class="font-weight-bold text-success mb-0" id="lnkt_count_hoan_tat">1</h3>
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
                        <h3 class="font-weight-bold text-danger mb-0" id="lnkt_count_da_huy">1</h3>
                        <small class="text-slate">Hoàn kho tự động</small>
                    </div>
                    <span class="lbn-inv-thumb"><i class="fa-solid fa-ban text-danger h4 mb-0"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (8 CỘT): BẢNG XỬ LÝ & CHUYỂN ĐỔI TRẠNG THÁI GIAO VẬN
             ================================================================ -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-bold text-slate mb-0">
                                Danh Sách Đơn Đặt Hàng &amp; Điều Phối Vận Chuyển Thời Gian Thực
                            </h6>
                            <small class="text-muted-custom">
                                Chọn trạng thái mới và nhấn <strong>Lưu</strong> để cập nhật đơn hàng
                            </small>
                        </div>
                        <span class="badge badge-pill badge-peach bg-peach text-white px-3 py-1">
                            Nhân viên trực: <?= htmlspecialchars($_SESSION['user']['ho_va_ten'] ?? 'Lê Nguyễn Khánh Trình') ?>
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
                                <option value="cho_duyet">1. Chờ duyệt </option>
                                <option value="da_xac_nhan">2. Đã xác nhận </option>
                                <option value="dang_giao">3. Đang giao </option>
                                <option value="hoan_tat">4. Hoàn tất </option>
                                <option value="da_huy">5. Đã hủy </option>
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
                                    <th>Khu Vực Giao Vận</th>
                                    <th class="text-right">Dòng Tiền Đơn</th>
                                    <th class="text-center">Trạng Thái Hiện Tại</th>
                                    <th class="text-center fk-minw-215">Cập Nhật Giao Vận</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_staff_orders as $ord): ?>
                                    <tr class="lnkt-staff-order-row"
                                        id="lnkt_orderRow_<?= $ord['id'] ?>"
                                        data-status="<?= $ord['status'] ?>"
                                        data-search="<?= htmlspecialchars($ord['order_code'] . ' ' . $ord['receiver_name'] . ' ' . $ord['receiver_phone'] . ' ' . $ord['province']) ?>">
                                        <td>
                                            <a href="javascript:void(0)"
                                               onclick="lnktInspectStaffOrder(this)"
                                               data-code="<?= htmlspecialchars($ord['order_code']) ?>"
                                               data-receiver="<?= htmlspecialchars($ord['receiver_name']) ?>"
                                               data-phone="<?= htmlspecialchars($ord['receiver_phone']) ?>"
                                               data-address="<?= htmlspecialchars($ord['shipping_address'] . ', ' . $ord['district'] . ', ' . $ord['province']) ?>"
                                               data-items="<?= htmlspecialchars($ord['items_summary']) ?>"
                                               data-subtotal="<?= number_format($ord['subtotal'], 0, ',', '.') ?> đ"
                                               data-discount="-<?= number_format($ord['discount_amount'], 0, ',', '.') ?> đ"
                                               data-total="<?= number_format($ord['total_amount'], 0, ',', '.') ?> đ"
                                               data-staff="<?= htmlspecialchars($ord['handled_by']) ?>"
                                               class="font-weight-bold text-peach text-decoration-none d-block">
                                                #<?= htmlspecialchars($ord['order_code']) ?>
                                            </a>
                                            <small class="text-muted-custom"><?= htmlspecialchars($ord['created_at']) ?></small>
                                        </td>

                                        <td>
                                            <div class="font-weight-bold text-slate"><?= htmlspecialchars($ord['receiver_name']) ?></div>
                                            <small class="text-muted-custom d-block">
                                                <i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($ord['receiver_phone']) ?>
                                            </small>
                                            <span class="badge badge-gold small py-0 px-2 rounded-pill"><?= htmlspecialchars($ord['vip_tier']) ?></span>
                                        </td>

                                        <td>
                                            <div class="small font-weight-bold text-slate"><?= htmlspecialchars($ord['province']) ?></div>
                                            <small class="text-muted-custom"><?= htmlspecialchars($ord['district']) ?></small>
                                        </td>

                                        <td class="text-right text-nowrap">
                                            <div class="font-weight-bold text-peach"><?= number_format($ord['total_amount'], 0, ',', '.') ?> đ</div>
                                            <small class="text-muted-custom">Gốc: <?= number_format($ord['subtotal'], 0, ',', '.') ?> đ</small>
                                        </td>

                                        <td class="text-center" id="lnkt_orderBadgeCell_<?= $ord['id'] ?>">
                                            <?= lnktRenderOrderStatusBadge($ord['status']) ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="input-group input-group-sm">
                                                <select id="lnkt_statusSelect_<?= $ord['id'] ?>" class="custom-select font-weight-bold">
                                                    <option value="cho_duyet" <?= $ord['status'] === 'cho_duyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                                                    <option value="da_xac_nhan" <?= $ord['status'] === 'da_xac_nhan' ? 'selected' : '' ?>>Đã xác nhận</option>
                                                    <option value="dang_giao" <?= $ord['status'] === 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                                                    <option value="hoan_tat" <?= $ord['status'] === 'hoan_tat' ? 'selected' : '' ?>>Hoàn tất</option>
                                                    <option value="da_huy" <?= $ord['status'] === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-pastel-primary px-2"
                                                            title="Cập nhật trạng thái đơn hàng"
                                                            onclick="lnktUpdateStaffOrderStatus(<?= $ord['id'] ?>, '<?= $ord['order_code'] ?>')">
                                                        <i class="fa-solid fa-check"></i> Lưu
                                                    </button>
                                                </div>
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

        <!-- ================================================================
             CỘT PHẢI (4 CỘT): PHIẾU KIỂM ĐƠN & ĐIỀU PHỐI NHANH
             ================================================================ -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-pastel shadow-sm sticky-top fk-sticky-85">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-file-invoice text-peach mr-1"></i> Phiếu Chi Tiết Vận Đơn Đang Chọn
                    </h6>
                    <span class="badge badge-pill badge-sky px-2 py-1" id="lnkt_inspectCode">#FK-ORD-202608</span>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="p-3 rounded bg-milk-cream border mb-3 small">
                        <div class="mb-2">
                            <span class="text-muted-custom">Khách hàng nhận:</span>
                            <strong class="text-slate float-right" id="lnkt_inspectReceiver">Võ Thị Thanh Thảo</strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted-custom">Số điện thoại:</span>
                            <strong class="text-peach float-right" id="lnkt_inspectPhone">0918889999</strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted-custom d-block">Địa chỉ giao vận:</span>
                            <strong class="text-slate" id="lnkt_inspectAddress">Số 120 Điện Biên Phủ, Phường 17, Quận Bình Thạnh, TP. Hồ Chí Minh</strong>
                        </div>
                        <div class="pt-2 border-top">
                            <span class="text-muted-custom d-block">Nhân viên thụ lý:</span>
                            <strong class="text-mint-dark" id="lnkt_inspectStaff">Lê Nguyễn Khánh Trình</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small font-weight-bold text-slate mb-1">Danh mục sản phẩm đóng gói:</label>
                        <div class="p-3 rounded border bg-white small font-weight-bold text-slate" id="lnkt_inspectItems">
                            1x Váy Công Chúa Sợi Tre (Size 2), 2x Bộ Thun Khủng Long (Size 3), 1x Sandal Tập Đi (Size 22)
                        </div>
                    </div>

                    <div class="border rounded p-3 bg-white mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted-custom">Tiền tạm tính:</span>
                            <strong id="lnkt_inspectSubtotal">945.000 đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted-custom">Giảm giá VIP:</span>
                            <strong class="text-success" id="lnkt_inspectDiscount">-94.500 đ</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="font-weight-bold text-slate">Tổng thu COD:</span>
                            <strong class="text-peach h6 mb-0" id="lnkt_inspectTotal">850.500 đ</strong>
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

