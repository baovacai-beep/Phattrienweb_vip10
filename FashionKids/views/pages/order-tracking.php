<?php
$page_title = "Theo Dõi Tiến Trình Đơn Hàng (MH 06)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Trạng thái khởi tạo mặc định (có thể truyền qua ?status=hoan_tat, mặc định 'hoan_tat' để hiển thị ngay nút ★ Viết đánh giá sản phẩm hoặc chuyển đổi trực tiếp)
$init_status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 'hoan_tat';
$valid_statuses = ['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy'];
if (!in_array($init_status, $valid_statuses, true)) {
    $init_status = 'hoan_tat';
}

// Danh sách 5 bước trong Stepper Timeline (lnkt_orders.status)
$lnkt_stepper_steps = [
    [
        'key'   => 'cho_duyet',
        'label' => 'Chờ duyệt',
        'time'  => '08/11/2026 08:15',
        'desc'  => 'Đơn hàng mới tạo thành công',
        'icon'  => 'fa-file-invoice'
    ],
    [
        'key'   => 'da_xac_nhan',
        'label' => 'Đã xác nhận',
        'time'  => '08/11/2026 09:00',
        'desc'  => 'Nhân viên đã kiểm kho & đóng gói',
        'icon'  => 'fa-box-open'
    ],
    [
        'key'   => 'dang_giao',
        'label' => 'Đang giao',
        'time'  => '08/11/2026 13:30',
        'desc'  => 'Bưu tá đang trên đường giao hàng',
        'icon'  => 'fa-truck-fast'
    ],
    [
        'key'   => 'hoan_tat',
        'label' => 'Hoàn tất',
        'time'  => '08/11/2026 16:45',
        'desc'  => 'Phụ huynh đã nhận hàng & tích điểm',
        'icon'  => 'fa-circle-check'
    ],
    [
        'key'   => 'da_huy',
        'label' => 'Đã hủy',
        'time'  => '--:--',
        'desc'  => 'Đơn hàng hủy theo yêu cầu',
        'icon'  => 'fa-ban'
    ]
];

// Chi tiết các sản phẩm trong đơn hàng #FK-ORD-202608
$lnkt_tracked_items = [
    [
        'product_id' => 101,
        'sku'        => 'FK-LBN-01',
        'name'       => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'size'       => 'Size 2 (2-3T)',
        'quantity'   => 1,
        'price'      => 285000,
        'icon'       => 'fa-person-dress text-peach'
    ],
    [
        'product_id' => 102,
        'sku'        => 'FK-LBN-02',
        'name'       => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'size'       => 'Size 3 (2-3T)',
        'quantity'   => 2,
        'price'      => 225000,
        'icon'       => 'fa-shirt text-info'
    ],
    [
        'product_id' => 103,
        'sku'        => 'FK-LBN-03',
        'name'       => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'size'       => 'Size 22 (13cm)',
        'quantity'   => 1,
        'price'      => 210000,
        'icon'       => 'fa-shoe-prints text-warning'
    ]
];
?>

<div class="container my-4 my-md-5" id="lnkt_order_tracking_section">
    <!-- Breadcrumb & Header MH 06 -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page">Theo dõi tiến trình đơn hàng (MH 06)</li>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-truck-fast text-peach mr-2"></i>Theo Dõi Tiến Trình Đơn Hàng Trực Tuyến
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-outline btn-sm font-weight-bold mr-2">
                <i class="fa-solid fa-user-gear mr-1"></i> Bàn Làm Việc Nhân Viên Duyệt Đơn (MH 10)
            </a>
            <a href="/FashionKids/views/pages/checkout.php" class="btn btn-pastel-primary btn-sm font-weight-bold">
                <i class="fa-solid fa-basket-shopping mr-1"></i> Giỏ Hàng (MH 05)
            </a>
        </div>
    </div>

    <!-- Thanh Tra Cứu Vận Đơn & Bộ Chuyển Đổi Nhanh 5 Trạng Thái Phục Vụ Kiểm Thử -->
    <div class="card card-pastel p-3 p-md-4 mb-4 shadow-sm bg-white">
        <div class="row align-items-center">
            <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                <label class="small font-weight-bold text-slate mb-1">Tra cứu nhanh theo Mã vận đơn hoặc Số điện thoại:</label>
                <div class="input-group">
                    <input type="text" id="lnkt_trackingCodeInput" class="form-control font-weight-bold text-slate"
                           value="FK-ORD-202608" placeholder="Nhập mã đơn (VD: FK-ORD-202608)...">
                    <div class="input-group-append">
                        <button type="button" onclick="lnktSearchOrderTracking()" class="btn btn-pastel-primary px-3">
                            <i class="fa-solid fa-magnifying-glass mr-1"></i> Tra cứu
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="d-flex flex-wrap justify-content-lg-end align-items-center">
                    <span class="small font-weight-bold text-slate mr-2 mb-2 mb-sm-0">
                        <i class="fa-solid fa-sliders text-peach mr-1"></i>Mô phỏng trạng thái (<code>lnkt_orders.status</code>):
                    </span>
                    <div class="btn-group btn-group-sm flex-wrap" role="group">
                        <button type="button" class="btn btn-outline-secondary font-weight-bold lnkt-status-sim-btn"
                                data-status="cho_duyet" onclick="lnktSetTrackingStatus('cho_duyet')">
                            1. Chờ duyệt
                        </button>
                        <button type="button" class="btn btn-outline-info font-weight-bold lnkt-status-sim-btn"
                                data-status="da_xac_nhan" onclick="lnktSetTrackingStatus('da_xac_nhan')">
                            2. Đã xác nhận
                        </button>
                        <button type="button" class="btn btn-outline-primary font-weight-bold lnkt-status-sim-btn"
                                data-status="dang_giao" onclick="lnktSetTrackingStatus('dang_giao')">
                            3. Đang giao
                        </button>
                        <button type="button" class="btn btn-success font-weight-bold lnkt-status-sim-btn active"
                                data-status="hoan_tat" onclick="lnktSetTrackingStatus('hoan_tat')">
                            4. Hoàn tất (hoan_tat)
                        </button>
                        <button type="button" class="btn btn-outline-danger font-weight-bold lnkt-status-sim-btn"
                                data-status="da_huy" onclick="lnktSetTrackingStatus('da_huy')">
                            5. Đã hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         KHUNG CHÍNH: QUY TRÌNH STEPPER TIMELINE 5 BƯỚC TRỰC QUAN
         Chờ duyệt -> Đã xác nhận -> Đang giao -> Hoàn tất -> Đã hủy
         ==================================================================== -->
    <div class="card card-pastel p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <div>
                <span class="small text-muted-custom font-weight-bold text-uppercase">Mã Vận Đơn Điện Tử:</span>
                <h4 class="font-weight-bold text-slate mb-0 d-inline-block ml-1" id="lnkt_displayOrderCode">#FK-ORD-202608</h4>
                <div class="small text-muted-custom mt-1">
                    Người nhận: <strong>Võ Thị Thanh Thảo</strong> (0918889999) • Quận Bình Thạnh, TP. Hồ Chí Minh
                </div>
            </div>

            <div class="mt-2 mt-md-0 text-md-right">
                <span id="lnkt_currentStatusBadge" class="badge badge-mint px-3 py-2 rounded-pill h6 mb-0">
                    <i class="fa-solid fa-circle-check mr-1"></i> Trạng thái: Hoàn tất (hoan_tat)
                </span>
                <div class="small text-muted-custom mt-1">
                    Nhân viên thụ lý (<code>handled_by</code>): <strong>Lê Nguyễn Khánh Trình (#NV02)</strong>
                </div>
            </div>
        </div>

        <!-- Thanh Tiến Trình 5 Bước (Stepper Timeline) -->
        <div class="lnkt-stepper-wrapper my-3" id="lnkt_stepperTimeline" data-current-status="<?= $init_status ?>">
            <?php foreach ($lnkt_stepper_steps as $idx => $step): ?>
                <div class="lnkt-stepper-item" id="lnkt_step_<?= $step['key'] ?>" data-step-key="<?= $step['key'] ?>" data-step-index="<?= $idx ?>">
                    <div class="lnkt-stepper-circle shadow-sm">
                        <i class="fa-solid <?= $step['icon'] ?>"></i>
                    </div>
                    <div class="lnkt-stepper-title font-weight-bold text-slate mt-2">
                        <?= htmlspecialchars($step['label']) ?>
                    </div>
                    <small class="lnkt-stepper-desc text-muted-custom d-block">
                        <?= htmlspecialchars($step['desc']) ?>
                    </small>
                    <span class="badge badge-light border mt-1 small lnkt-stepper-time">
                        <?= htmlspecialchars($step['time']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- THÔNG BÁO KHI ĐƠN HÀNG ĐẠT TRẠNG THÁI HOÀN TẤT (hoan_tat) & NÚT VÀNG BƠ (#FDE2B8) -->
        <div id="lnkt_completedReviewBanner" class="mt-4 p-3 p-md-4 rounded lnkt-completed-callout shadow-sm">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge badge-mint px-2 py-1 rounded-pill mr-2">Đơn hàng hoan_tat</span>
                        <small class="font-weight-bold text-slate">Đã cộng +85 điểm thưởng vào thẻ VIP Vàng</small>
                    </div>
                    <h5 class="font-weight-bold text-slate mb-1">
                        Cảm ơn phụ huynh đã mua sắm tại FashionKids!
                    </h5>
                    <p class="small text-muted-custom mb-0">
                        Đơn hàng đã giao thành công. Hãy chia sẻ cảm nhận của mẹ về độ mềm mịn của vải sợi tre để nhận thêm điểm thưởng VIP nhé!
                    </p>
                </div>
                <div class="col-12 col-lg-4 text-lg-right">
                    <!-- Nút màu vàng bơ (#FDE2B8) với nhãn "★ Viết đánh giá sản phẩm" mở Modal đánh giá (MH 19) -->
                    <button type="button"
                            class="btn lnkt-btn-buttercup px-4 py-2 shadow-sm font-weight-bold"
                            data-toggle="modal" data-target="#modalReview"
                            onclick="lnktOpenProductReviewModal('Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi')">
                        &#9733; Viết đánh giá sản phẩm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         CHI TIẾT SẢN PHẨM TRONG ĐƠN & DÒNG THỜI GIAN GIAO VẬN
         ==================================================================== -->
    <div class="row">
        <!-- Cột Trái: Danh sách món hàng trong đơn & Nút viết đánh giá từng món -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card card-pastel shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-box-archive text-peach mr-2"></i>Chi Tiết Sản Phẩm Trong Đơn (<code>tgb_order_items</code>)
                    </h6>
                    <span class="small text-muted-custom font-weight-bold"><?= count($lnkt_tracked_items) ?> sản phẩm</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th class="text-center">Kích Cỡ</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-right">Thành Tiền</th>
                                    <th class="text-center">Đánh Giá (MH 19)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lnkt_tracked_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="lbn-inv-thumb mr-3 flex-shrink-0">
                                                    <i class="fa-solid <?= $item['icon'] ?> h4 mb-0"></i>
                                                </span>
                                                <div>
                                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $item['product_id'] ?>"
                                                       class="font-weight-bold text-slate text-decoration-none d-block">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <small class="text-muted-custom">Mã SKU: <code><?= htmlspecialchars($item['sku']) ?></code></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pill badge-sky px-2 py-1"><?= htmlspecialchars($item['size']) ?></span>
                                        </td>
                                        <td class="text-center font-weight-bold">x<?= $item['quantity'] ?></td>
                                        <td class="text-right font-weight-bold text-peach text-nowrap">
                                            <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <!-- Nút vàng bơ (#FDE2B8) xuất hiện khi trạng thái = hoan_tat -->
                                            <button type="button"
                                                    class="btn btn-sm lnkt-btn-buttercup lnkt-item-review-btn px-3 py-1 font-weight-bold shadow-sm"
                                                    data-toggle="modal" data-target="#modalReview"
                                                    onclick="lnktOpenProductReviewModal('<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>')">
                                                &#9733; Viết đánh giá sản phẩm
                                            </button>
                                            <span class="small text-muted-custom lnkt-item-pending-review d-none">
                                                Chờ hoàn tất đơn
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Tổng kết thanh toán của vận đơn -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card card-pastel shadow-sm h-100 p-4">
                <h6 class="font-weight-bold text-slate mb-3 pb-2 border-bottom">
                    <i class="fa-solid fa-receipt text-peach mr-2"></i>Tóm Tắt Thanh Toán Đơn Hàng
                </h6>

                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted-custom">Tiền tạm tính (<code>subtotal</code>):</span>
                    <span class="font-weight-bold text-slate">945.000 đ</span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted-custom">Chiết khấu VIP Vàng (-10%):</span>
                    <span class="font-weight-bold text-success">-94.500 đ</span>
                </div>
                <div class="d-flex justify-content-between small mb-3 pb-2 border-bottom">
                    <span class="text-muted-custom">Phí vận chuyển:</span>
                    <span class="badge badge-mint px-2 py-0 rounded-pill">Miễn phí</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="font-weight-bold text-slate">Tổng thu thực tế:</span>
                    <span class="h5 font-weight-bold text-peach mb-0">850.500 đ</span>
                </div>

                <div class="p-3 rounded bg-milk-cream border small text-slate mt-auto">
                    <div class="font-weight-bold mb-1">
                        <i class="fa-solid fa-location-dot text-peach mr-1"></i>Địa chỉ nhận hàng:
                    </div>
                    <div>Số 120 Điện Biên Phủ, Phường 17, Quận Bình Thạnh, TP. Hồ Chí Minh</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đánh Giá Dự Phòng (Chỉ mở khi #modalReview của MH 19 chưa được nạp nội dung) -->
<div class="modal fade" id="lnkt_fallbackReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg card-pastel">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-slate">
                    <i class="fa-solid fa-star text-warning mr-1"></i> Đánh Giá Sản Phẩm (MH 19)
                </h5>
                <button type="button" class="close text-slate" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted-custom mb-2">Sản phẩm đang đánh giá:</p>
                <div id="lnkt_reviewModalProductName" class="font-weight-bold text-peach mb-3">
                    Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold text-slate">Mức độ hài lòng của phụ huynh:</label>
                    <div class="h4 text-warning mb-0">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <span class="small text-slate font-weight-bold ml-2">5.0 / 5 (Tuyệt vời)</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold text-slate">Nhận xét thực tế về chất liệu &amp; phom dáng:</label>
                    <textarea id="lnkt_reviewCommentText" class="form-control" rows="3">Vải sợi tre cực kỳ mềm mát, bé mặc vừa in theo gợi ý size thước dây!</textarea>
                </div>
                <button type="button" onclick="lnktSubmitFallbackReview()" class="btn btn-pastel-primary btn-block font-weight-bold py-2">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Gửi Đánh Giá Sản Phẩm
                </button>
            </div>
        </div>
    </div>
</div>

<?php
if (file_exists("../modals/modal-review.php")) {
    require_once "../modals/modal-review.php";
}
require_once "../layouts/footer.php";
?>
