<?php
$page_title = "Theo Dõi Tiến Trình Đơn Hàng (MH 06)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Trạng thái đơn hàng hiện tại (mặc định hoan_tat để hiển thị nút ★ Viết đánh giá sản phẩm #FDE2B8)
$lnkt_current_status = isset($_GET['status']) ? $_GET['status'] : 'hoan_tat';

$lnkt_order_info = [
    'order_code'      => '#FK-2026-0892',
    'created_at'      => '06/11/2026 09:30',
    'receiver_name'   => 'Võ Thị Thanh Thảo',
    'receiver_phone'  => '0918889999',
    'shipping_address'=> 'Số 120 Điện Biên Phủ, Phường 17',
    'district'        => 'Quận Bình Thạnh',
    'province'        => 'TP. Hồ Chí Minh',
    'handled_by'      => 'Lê Nguyễn Khánh Trình (NV Vận Hành #02)',
    'subtotal'        => 1025000,
    'discount_amount' => 102500,
    'total_amount'    => 922500
];

$lnkt_order_items = [
    ['id' => 101, 'name' => 'Đầm Xòe Sợi Tre Hoa Cúc Họa Mi', 'size' => 'Size 2 (2-3T)', 'qty' => 2, 'price' => 285000],
    ['id' => 102, 'name' => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí', 'size' => 'Size 3 (3-4T)', 'qty' => 1, 'price' => 245000],
    ['id' => 103, 'name' => 'Sandal Tập Đi Đế Cao Su Non Chống Trượt', 'size' => 'Size 24 (14cm)', 'qty' => 1, 'price' => 210000]
];
?>

<div class="container my-4 my-md-5" id="lnkt_order_tracking_section">
    <!-- Bộ chuyển đổi Demo nhanh 5 trạng thái đơn hàng phục vụ kiểm thử & chụp ảnh báo cáo -->
    <div class="card card-pastel p-3 mb-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <span class="small font-weight-bold text-slate mb-2 mb-md-0">
                <i class="fa-solid fa-sliders text-peach mr-1"></i> Mô phỏng chuyển đổi trạng thái đơn hàng (<code>lnkt_orders.status</code>):
            </span>
            <div class="btn-group btn-group-sm flex-wrap" role="group">
                <button type="button" class="btn btn-outline-secondary font-weight-bold" onclick="lnktSwitchOrderStatus('cho_duyet')">1. Chờ duyệt</button>
                <button type="button" class="btn btn-outline-info font-weight-bold" onclick="lnktSwitchOrderStatus('da_xac_nhan')">2. Đã xác nhận</button>
                <button type="button" class="btn btn-outline-primary font-weight-bold" onclick="lnktSwitchOrderStatus('dang_giao')">3. Đang giao</button>
                <button type="button" class="btn btn-outline-success font-weight-bold" onclick="lnktSwitchOrderStatus('hoan_tat')">4. Hoàn tất (hoan_tat)</button>
                <button type="button" class="btn btn-outline-danger font-weight-bold" onclick="lnktSwitchOrderStatus('da_huy')">5. Đã hủy</button>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         1. QUY TRÌNH STEPPER TIMELINE 5 BƯỚC TRỰC QUAN (MH 06)
         Chờ duyệt -> Đã xác nhận -> Đang giao -> Hoàn tất -> Đã hủy
         ==================================================================== -->
    <div class="card card-pastel p-4 p-md-5 shadow-sm mb-4 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="font-weight-bold text-slate mb-1">
                    Theo Dõi Tiến Trình Vận Đơn <span class="text-peach"><?= $lnkt_order_info['order_code'] ?></span>
                </h4>
                <small class="text-muted-custom">
                    Ngày đặt hàng: <?= $lnkt_order_info['created_at'] ?> | Thụ lý duyệt: <strong><?= $lnkt_order_info['handled_by'] ?></strong>
                </small>
            </div>
            <div class="mt-2 mt-md-0">
                <span id="lnkt_currentOrderBadge" class="badge badge-pill badge-mint px-3 py-2 font-weight-bold">
                    HOÀN TẤT ĐƠN HÀNG
                </span>
            </div>
        </div>

        <!-- Thanh tiến trình 5 bước Stepper Timeline -->
        <div class="lnkt-stepper-wrapper my-3">
            <!-- Bước 1: Chờ duyệt -->
            <div id="lnkt_step_cho_duyet" class="lnkt-stepper-item completed">
                <div class="lnkt-step-circle shadow-sm">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div class="font-weight-bold small text-slate">Chờ duyệt</div>
                <small class="text-muted-custom d-none d-md-block">09:30 - 06/11</small>
            </div>

            <!-- Bước 2: Đã xác nhận -->
            <div id="lnkt_step_da_xac_nhan" class="lnkt-stepper-item completed">
                <div class="lnkt-step-circle shadow-sm">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="font-weight-bold small text-slate">Đã xác nhận</div>
                <small class="text-muted-custom d-none d-md-block">10:15 - 06/11</small>
            </div>

            <!-- Bước 3: Đang giao -->
            <div id="lnkt_step_dang_giao" class="lnkt-stepper-item completed">
                <div class="lnkt-step-circle shadow-sm">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="font-weight-bold small text-slate">Đang giao</div>
                <small class="text-muted-custom d-none d-md-block">14:00 - 06/11</small>
            </div>

            <!-- Bước 4: Hoàn tất (hoan_tat) -->
            <div id="lnkt_step_hoan_tat" class="lnkt-stepper-item completed active">
                <div class="lnkt-step-circle shadow-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="font-weight-bold small text-slate">Hoàn tất</div>
                <small class="text-muted-custom d-none d-md-block">17:45 - 06/11</small>
            </div>

            <!-- Bước 5: Đã hủy -->
            <div id="lnkt_step_da_huy" class="lnkt-stepper-item">
                <div class="lnkt-step-circle shadow-sm">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div class="font-weight-bold small text-slate">Đã hủy</div>
                <small class="text-muted-custom d-none d-md-block">Trạng thái hủy</small>
            </div>
        </div>

        <!-- Thông báo trạng thái & Nút vàng bơ (#FDE2B8) "★ Viết đánh giá sản phẩm" khi đạt hoan_tat -->
        <div class="p-3 rounded bg-milk-cream border d-flex flex-wrap justify-content-between align-items-center mt-3">
            <div class="mb-2 mb-md-0">
                <i class="fa-solid fa-bell text-peach mr-2"></i>
                <span id="lnkt_currentOrderDesc" class="font-weight-bold text-slate small">
                    Giao hàng thành công! Điểm thưởng VIP (+92 PTS) đã được cộng vào tài khoản của mẹ.
                </span>
            </div>

            <div class="lnkt-review-cta-wrap">
                <button type="button" id="lnkt_btnWriteReview" class="btn btn-buttercup-custom btn-sm shadow-sm" data-toggle="modal" data-target="#modalReviewProduct">
                    &#9733; Viết đánh giá sản phẩm
                </button>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         2. CHI TIẾT THÔNG TIN NHẬN HÀNG & DANH SÁCH SẢN PHẨM TRONG ĐƠN
         ==================================================================== -->
    <div class="row">
        <!-- Cột Trái: Địa chỉ nhận hàng -->
        <div class="col-12 col-lg-5 mb-4">
            <div class="card card-pastel p-4 shadow-sm h-100">
                <h6 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-location-dot text-peach mr-2"></i>Thông Tin Người Nhận &amp; Giao Vận
                </h6>
                <div class="small mb-2">
                    <span class="text-muted-custom">Phụ huynh nhận hàng:</span>
                    <strong class="text-slate float-right"><?= htmlspecialchars($lnkt_order_info['receiver_name']) ?></strong>
                </div>
                <div class="small mb-2">
                    <span class="text-muted-custom">Số điện thoại:</span>
                    <strong class="text-slate float-right"><?= htmlspecialchars($lnkt_order_info['receiver_phone']) ?></strong>
                </div>
                <div class="small mb-2">
                    <span class="text-muted-custom">Tỉnh / Thành phố:</span>
                    <strong class="text-slate float-right"><?= htmlspecialchars($lnkt_order_info['province']) ?></strong>
                </div>
                <div class="small mb-2">
                    <span class="text-muted-custom">Quận / Huyện:</span>
                    <strong class="text-slate float-right"><?= htmlspecialchars($lnkt_order_info['district']) ?></strong>
                </div>
                <div class="small mb-3 pb-3 border-bottom">
                    <span class="text-muted-custom d-block mb-1">Địa chỉ giao hàng chi tiết:</span>
                    <strong class="text-slate"><?= htmlspecialchars($lnkt_order_info['shipping_address']) ?>, <?= htmlspecialchars($lnkt_order_info['district']) ?>, <?= htmlspecialchars($lnkt_order_info['province']) ?></strong>
                </div>

                <!-- Tổng hợp thanh toán của đơn -->
                <div class="small mb-2 d-flex justify-content-between">
                    <span class="text-muted-custom">Tiền tạm tính (<code>subtotal</code>):</span>
                    <span class="font-weight-bold"><?= number_format($lnkt_order_info['subtotal'], 0, ',', '.') ?> đ</span>
                </div>
                <div class="small mb-2 d-flex justify-content-between">
                    <span class="text-muted-custom">Chiết khấu hạng VIP (<code>discount_amount</code>):</span>
                    <span class="font-weight-bold text-success">- <?= number_format($lnkt_order_info['discount_amount'], 0, ',', '.') ?> đ</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                    <span class="font-weight-bold text-slate">Tổng thanh toán (<code>total_amount</code>):</span>
                    <span class="h5 font-weight-bold text-peach mb-0"><?= number_format($lnkt_order_info['total_amount'], 0, ',', '.') ?> đ</span>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Các món đồ trong đơn hàng -->
        <div class="col-12 col-lg-7 mb-4">
            <div class="card card-pastel p-4 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-box text-peach mr-2"></i>Danh Sách Sản Phẩm Đã Mua (<code>tgb_order_items</code>)
                    </h6>
                    <span class="badge badge-pill badge-sky px-3 py-1"><?= count($lnkt_order_items) ?> mặt hàng</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light small">
                            <tr>
                                <th>Tên Sản Phẩm</th>
                                <th>Phân Loại Size</th>
                                <th class="text-center">SL</th>
                                <th class="text-right">Thành Tiền</th>
                                <th class="text-center">Đánh Giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lnkt_order_items as $item): ?>
                                <tr>
                                    <td class="font-weight-bold small text-slate"><?= htmlspecialchars($item['name']) ?></td>
                                    <td><span class="badge badge-sky"><?= htmlspecialchars($item['size']) ?></span></td>
                                    <td class="text-center font-weight-bold"><?= $item['qty'] ?></td>
                                    <td class="text-right font-weight-bold text-peach">
                                        <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?> đ
                                    </td>
                                    <td class="text-center">
                                        <div class="lnkt-review-cta-wrap">
                                            <button type="button" class="btn btn-buttercup-custom btn-sm py-1 px-3 small" data-toggle="modal" data-target="#modalReviewProduct">
                                                &#9733; Viết đánh giá sản phẩm
                                            </button>
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
</div>

<?php
if (file_exists("../modals/modal-review.php")) {
    require_once "../modals/modal-review.php";
}
require_once "../modals/modal-suggest-size.php";
require_once "../layouts/footer.php";
?>
