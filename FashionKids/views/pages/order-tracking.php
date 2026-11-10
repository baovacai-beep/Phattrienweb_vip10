<?php
$page_title = "Theo Dõi Tiến Trình Đơn Hàng - FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/OrderModel.php";

$trackOrderModel = new OrderModel();
$currentUid      = Rbac::currentUserId();
$isStaffOrAdmin  = Rbac::isStaffOrAdmin();

if (!isset($trackedOrder)) {
    $trackedOrder = null;
}

$reqOrderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$reqCode    = trim((string)($_GET['code'] ?? ''));

if (!$trackedOrder) {
    if ($reqOrderId > 0) {
        $trackedOrder = $trackOrderModel->getOrderById($reqOrderId);
    } elseif ($reqCode !== '') {
        $trackedOrder = $trackOrderModel->findOrderForTracking($reqCode);
    } elseif (!empty($_SESSION['recent_order']['order_id'])) {
        $trackedOrder = $trackOrderModel->getOrderById((int)$_SESSION['recent_order']['order_id']);
    } elseif ($currentUid) {
        $myRecent = $trackOrderModel->getOrdersByUserId($currentUid, 1);
        if (!empty($myRecent[0]['ma_don_hang'])) {
            $trackedOrder = $trackOrderModel->getOrderById((int)$myRecent[0]['ma_don_hang']);
        }
    }
}

$myOrderHistory = $currentUid ? $trackOrderModel->getOrdersByUserId($currentUid, 8) : [];

$lnkt_stepper_steps = [
    ['key' => 'cho_duyet',   'label' => 'Chờ duyệt',   'desc' => 'Đơn hàng mới tạo thành công',        'icon' => 'fa-file-invoice'],
    ['key' => 'da_xac_nhan', 'label' => 'Đã xác nhận', 'desc' => 'Nhân viên đã kiểm kho & đóng gói',  'icon' => 'fa-box-open'],
    ['key' => 'dang_giao',   'label' => 'Đang giao',   'desc' => 'Bưu tá đang trên đường giao hàng',  'icon' => 'fa-truck-fast'],
    ['key' => 'hoan_tat',    'label' => 'Hoàn tất',    'desc' => 'Phụ huynh đã nhận hàng & tích điểm', 'icon' => 'fa-circle-check'],
    ['key' => 'da_huy',      'label' => 'Đã hủy',      'desc' => 'Đơn hàng hủy theo yêu cầu',         'icon' => 'fa-ban'],
];

$hasOrder         = is_array($trackedOrder) && !empty($trackedOrder['ma_don_hang']);
$orderId          = $hasOrder ? (int)$trackedOrder['ma_don_hang'] : 0;
$orderCode        = $hasOrder ? (string)$trackedOrder['ma_van_don'] : '';
$statusKey        = $hasOrder ? (string)($trackedOrder['trang_thai_don_hang'] ?? 'cho_duyet') : 'cho_duyet';
$statusMeta       = $hasOrder ? ($trackedOrder['status_meta'] ?? []) : ['label' => 'Chờ duyệt', 'badge_class' => 'badge-warning text-dark', 'icon' => 'fa-clock'];
$isOwner          = ($hasOrder && $currentUid !== null && (int)($trackedOrder['ma_nguoi_dung'] ?? 0) === $currentUid);
$canCancel        = ($isOwner && $statusKey === 'cho_duyet');
$payMethod        = $hasOrder ? (string)($trackedOrder['phuong_thuc_thanh_toan'] ?? 'cod') : 'cod';
$payStatus        = $hasOrder ? (string)($trackedOrder['trang_thai_thanh_toan'] ?? 'chua_thanh_toan') : 'chua_thanh_toan';
$isPaid           = ($payStatus === 'da_thanh_toan');
$isExpired        = $hasOrder && !empty($trackedOrder['is_payment_expired']);
$isVietQrActive   = ($hasOrder && $payMethod === 'vietqr' && !$isPaid && $statusKey !== 'da_huy');
$isCodUnpaid      = ($hasOrder && $payMethod === 'cod' && !$isPaid && $statusKey !== 'da_huy');
$isCancelledExp   = ($hasOrder && $statusKey === 'da_huy' && $isExpired);
$canConfirmQr     = ($isVietQrActive && !$isExpired && ($isOwner || $isStaffOrAdmin));
$canSwitchPayment = ($hasOrder && ($isOwner || $isStaffOrAdmin) && !$isPaid && $statusKey === 'cho_duyet' && !$isExpired);

$bankName         = defined('VIETQR_BANK_NAME') ? VIETQR_BANK_NAME : 'MB Bank';
$bankAccountNo    = defined('VIETQR_ACCOUNT_NO') ? VIETQR_ACCOUNT_NO : '0913672861';
$bankAccountName  = defined('VIETQR_ACCOUNT_NAME') ? VIETQR_ACCOUNT_NAME : 'HO GIA BAO';
$transferPrefix   = defined('VIETQR_TRANSFER_PREFIX') ? VIETQR_TRANSFER_PREFIX : 'FK';
$transferMemo     = $hasOrder ? (string)($trackedOrder['noi_dung_chuyen_khoan'] ?? ($transferPrefix . $orderId)) : '';
$txCode           = $hasOrder ? (string)($trackedOrder['ma_giao_dich_ngan_hang'] ?? '') : '';
$paidAtLabel      = ($hasOrder && !empty($trackedOrder['thoi_gian_thanh_toan'])) ? date('d/m/Y H:i', strtotime((string)$trackedOrder['thoi_gian_thanh_toan'])) : '';
$orderTotal       = $hasOrder ? (float)($trackedOrder['tong_tien_thanh_toan'] ?? 0) : 0;
$orderSubtotal    = $hasOrder ? (float)($trackedOrder['tien_hang_tam_tinh'] ?? 0) : 0;
$orderDiscount    = $hasOrder ? (float)($trackedOrder['tien_giam_gia'] ?? 0) : 0;
$codCollectAmt    = $hasOrder ? (float)($trackedOrder['cod_collect_amount'] ?? ($isPaid || $statusKey === 'da_huy' ? 0 : $orderTotal)) : 0;
$orderRewardPts   = (int)floor($orderTotal / 10000);
$createdTs        = ($hasOrder && !empty($trackedOrder['ngay_dat_hang'])) ? strtotime($trackedOrder['ngay_dat_hang']) : time();
$deadlineTs       = $hasOrder ? (int)($trackedOrder['han_thanh_toan_ts'] ?? ($createdTs + 86400)) : ($createdTs + 86400);
$deadlineLabel    = $deadlineTs > 0 ? date('d/m/Y H:i', $deadlineTs) : '--:--';
$createdLabel     = ($hasOrder && !empty($trackedOrder['ngay_dat_hang'])) ? date('d/m/Y H:i', $createdTs) : '--:--';
$updatedLabel     = ($hasOrder && !empty($trackedOrder['ngay_cap_nhat'])) ? date('d/m/Y H:i', strtotime($trackedOrder['ngay_cap_nhat'])) : $createdLabel;
$trackedItems     = $hasOrder && !empty($trackedOrder['items']) ? $trackedOrder['items'] : [];
$fullAddress      = $hasOrder ? trim(implode(', ', array_filter([
    $trackedOrder['dia_chi_giao_hang'] ?? '',
    $trackedOrder['quan_huyen'] ?? '',
    $trackedOrder['tinh_thanh_pho'] ?? '',
]))) : '';
$firstPendingReviewItem = null;
foreach ($trackedItems as $ti) {
    if (empty($ti['da_danh_gia'])) {
        $firstPendingReviewItem = $ti;
        break;
    }
}
?>

<div class="container my-4 my-md-5"
     id="lnkt_order_tracking_section"
     data-order-id="<?= $orderId ?>"
     data-order-created-ts="<?= $createdTs ?>"
     data-deadline-ts="<?= $deadlineTs ?>"
     data-payment-method="<?= htmlspecialchars($payMethod) ?>"
     data-payment-status="<?= htmlspecialchars($payStatus) ?>"
     data-is-expired="<?= $isExpired ? '1' : '0' ?>">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1 small">
                    <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page">Theo dõi tiến trình đơn hàng</li>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-slate mb-0">
                <i class="fa-solid fa-truck-fast text-peach mr-2"></i>Theo Dõi Tiến Trình Đơn Hàng Trực Tuyến
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <?php if ($isStaffOrAdmin): ?>
                <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-outline btn-sm font-weight-bold mr-2">
                    <i class="fa-solid fa-user-gear mr-1"></i> Quản Lý Đơn Hàng
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary btn-sm font-weight-bold">
                <i class="fa-solid fa-store mr-1"></i> Tiếp Tục Mua Sắm
            </a>
        </div>
    </div>

    <!-- Thanh Tra Cứu Vận Đơn & Lịch Sử Đơn Hàng Của Tôi -->
    <div class="card card-pastel p-3 p-md-4 mb-4 shadow-sm bg-white">
        <div class="row align-items-center">
            <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                <label class="small font-weight-bold text-slate mb-1" for="lnkt_trackingCodeInput">
                    Tra cứu nhanh theo Mã vận đơn hoặc Số điện thoại:
                </label>
                <div class="input-group">
                    <input type="text" id="lnkt_trackingCodeInput" class="form-control font-weight-bold text-slate"
                           value="<?= htmlspecialchars($orderCode !== '' ? $orderCode : $reqCode) ?>"
                           placeholder="VD: FK-ORD-000001 hoặc SĐT 0918...">
                    <div class="input-group-append">
                        <button type="button" id="lnkt_btnTrackSearch" onclick="lnktSearchOrderTracking()" class="btn btn-pastel-primary px-3">
                            <i class="fa-solid fa-magnifying-glass mr-1"></i> Tra cứu
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <?php if (!empty($myOrderHistory)): ?>
                    <div class="small font-weight-bold text-slate mb-1">
                        <i class="fa-solid fa-clock-rotate-left text-peach mr-1"></i>Đơn hàng gần đây của bạn:
                    </div>
                    <div class="d-flex flex-wrap">
                        <?php foreach ($myOrderHistory as $hOrd):
                            $hId = (int)$hOrd['ma_don_hang'];
                            $hActive = ($hId === $orderId);
                            $hBadge = $hOrd['status_meta']['label'] ?? 'Chờ duyệt';
                        ?>
                            <button type="button"
                                    class="btn btn-sm rounded-pill mr-2 mb-2 lnkt-order-history-pill <?= $hActive ? 'btn-pastel-primary active' : 'btn-light border text-slate' ?>"
                                    data-order-id="<?= $hId ?>"
                                    onclick="lnktLoadTrackedOrderById(<?= $hId ?>, this)">
                                #<?= htmlspecialchars($hOrd['ma_van_don']) ?>
                                <span class="badge badge-light ml-1"><?= htmlspecialchars($hBadge) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded bg-milk-cream border small text-muted-custom">
                        <i class="fa-solid fa-circle-info text-peach mr-1"></i>
                        Nhập <strong>Mã đơn hàng</strong> (VD: <code>1</code> hoặc <code>FK-ORD-000001</code>) hoặc <strong>Số điện thoại đặt hàng</strong> để xem tiến trình giao vận trực tiếp từ hệ thống.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div id="lnkt_trackingAlert" class="alert alert-danger small py-2 mt-3 mb-0 d-none" role="alert"></div>
    </div>

    <!-- Thông báo khi chưa tìm thấy / chưa có đơn -->
    <div id="lnkt_trackingEmptyCard" class="card card-pastel p-5 text-center shadow-sm mb-4 <?= $hasOrder ? 'd-none' : '' ?>">
        <i class="fa-solid fa-box-open display-4 text-peach mb-3"></i>
        <h5 class="font-weight-bold text-slate">Chưa có thông tin đơn hàng được chọn</h5>
        <p class="small text-muted-custom mb-3">
            Phụ huynh vui lòng nhập mã vận đơn hoặc số điện thoại ở khung phía trên để theo dõi chi tiết trạng thái xử lý đơn hàng.
        </p>
        <div>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary px-4">
                <i class="fa-solid fa-basket-shopping mr-1"></i> Khám Phá Cửa Hàng
            </a>
        </div>
    </div>

    <!-- ====================================================================
         KHUNG CHÍNH: CHI TIẾT ĐƠN HÀNG & STEPPER TIMELINE 5 BƯỚC
         ==================================================================== -->
    <div id="lnkt_trackingContentWrap" class="<?= $hasOrder ? '' : 'd-none' ?>">
        <div class="card card-pastel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <span class="small text-muted-custom font-weight-bold text-uppercase">Mã Vận Đơn Điện Tử:</span>
                    <h4 class="font-weight-bold text-slate mb-0 d-inline-block ml-1" id="lnkt_displayOrderCode">
                        <?= $hasOrder ? ('#' . htmlspecialchars($orderCode)) : '' ?>
                    </h4>
                    <div class="small text-muted-custom mt-1" id="lnkt_receiverMeta">
                        <?php if ($hasOrder): ?>
                            Người nhận: <strong><?= htmlspecialchars((string)$trackedOrder['ten_nguoi_nhan']) ?></strong>
                            (<?= htmlspecialchars((string)$trackedOrder['so_dien_thoai_nhan']) ?>) • <?= htmlspecialchars($fullAddress) ?>
                        <?php endif; ?>
                    </div>
                    <div class="small text-muted-custom mt-1" id="lnkt_createdTimeMeta">
                        Ngày đặt: <strong><?= htmlspecialchars($createdLabel) ?></strong>
                    </div>
                </div>

                <div class="mt-3 mt-md-0 text-md-right">
                    <span id="lnkt_currentStatusBadge" class="badge <?= htmlspecialchars($statusMeta['badge_class'] ?? 'badge-warning text-dark') ?> px-3 py-2 rounded-pill h6 mb-1">
                        <i class="fa-solid <?= htmlspecialchars($statusMeta['icon'] ?? 'fa-clock') ?> mr-1"></i>
                        Trạng thái: <?= htmlspecialchars($statusMeta['label'] ?? 'Chờ duyệt') ?>
                    </span>
                    <div class="small text-muted-custom mt-1" id="lnkt_staffMeta">
                        Nhân viên phụ trách: <strong><?= htmlspecialchars((string)($trackedOrder['nhan_vien_phu_trach'] ?? 'Đang chờ tiếp nhận')) ?></strong>
                    </div>
                    <div class="mt-2 <?= $canCancel ? '' : 'd-none' ?>" id="lnkt_ownerCancelBar">
                        <button type="button" id="lnkt_btnCancelMyOrder"
                                class="btn btn-outline-danger btn-sm rounded-pill px-3 font-weight-bold"
                                onclick="lnktCancelMyOrder(<?= $orderId ?>)">
                            <i class="fa-solid fa-ban mr-1"></i> Hủy Đơn Hàng
                        </button>
                    </div>
                </div>
            </div>

            <!-- Thanh Tiến Trình 5 Bước -->
            <div class="lnkt-stepper-wrapper my-3" id="lnkt_stepperTimeline" data-current-status="<?= htmlspecialchars($statusKey) ?>">
                <?php foreach ($lnkt_stepper_steps as $idx => $step):
                    $stepTime = '--:--';
                    if ($step['key'] === 'cho_duyet' && $hasOrder) {
                        $stepTime = $createdLabel;
                    } elseif ($step['key'] === $statusKey && $hasOrder) {
                        $stepTime = $updatedLabel;
                    }
                ?>
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
                        <span class="badge badge-light border mt-1 small lnkt-stepper-time" id="lnkt_stepTime_<?= $step['key'] ?>">
                            <?= htmlspecialchars($stepTime) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- CỔNG THANH TOÁN VIETQR MB BANK ĐỘNG (Khi chọn VietQR & chưa hoàn tất chuyển khoản) -->
            <div id="lnkt_vietQrPanel" class="mt-4 p-4 rounded lnkt-vietqr-card shadow-sm <?= $isVietQrActive ? '' : 'd-none' ?>">
                <div class="row align-items-center">
                    <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                        <div class="bg-white p-2 rounded shadow-sm d-inline-block">
                            <img id="lnkt_vietQrImg"
                                 src="<?= $hasOrder && !empty($trackedOrder['qr_url']) ? htmlspecialchars((string)$trackedOrder['qr_url']) : '' ?>"
                                 alt="Mã QR Thanh toán VietQR MB Bank"
                                 class="lnkt-vietqr-img">
                        </div>
                        <small class="d-block text-muted-custom mt-2">
                            <span class="fk-live-pulse-dot mr-1"></span>Tự động kiểm tra Casso mỗi 5 giây
                        </small>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                            <span class="badge badge-pill badge-sky px-3 py-1 font-weight-bold mb-1">
                                <i class="fa-solid fa-qrcode mr-1"></i>VietQR Napas 247 • <?= htmlspecialchars($bankName) ?>
                            </span>
                            <span id="lnkt_vietQrCountdownWrap" class="badge badge-pill badge-warning text-dark px-3 py-1 font-weight-bold mb-1">
                                <i class="fa-regular fa-clock mr-1"></i>Thời hạn TT 24h còn: <span id="lnkt_qrCountdown">24:00:00</span>
                            </span>
                        </div>

                        <h5 class="font-weight-bold text-slate mb-1">Quét mã QR bằng ứng dụng Ngân hàng hoặc Ví điện tử</h5>
                        <p class="small text-muted-custom mb-3">
                            Hạn thanh toán: <strong class="text-slate" id="lnkt_qrDeadlineLabel"><?= htmlspecialchars($deadlineLabel) ?></strong>. Nếu quá 24 giờ chưa thanh toán, hệ thống và shop sẽ tự động không xác nhận &amp; hủy đơn hàng.
                        </p>

                        <div class="row small mb-3 bg-white p-3 rounded border mx-0">
                            <div class="col-sm-6 mb-2">
                                Ngân hàng thụ hưởng: <strong class="text-slate"><?= htmlspecialchars($bankName) ?></strong>
                            </div>
                            <div class="col-sm-6 mb-2">
                                Chủ tài khoản: <strong class="text-slate"><?= htmlspecialchars($bankAccountName) ?></strong>
                            </div>
                            <div class="col-sm-6 mb-2 mb-sm-0">
                                Số tài khoản:
                                <strong class="text-slate" id="lnkt_vietQrAccNo"><?= htmlspecialchars($bankAccountNo) ?></strong>
                                <button type="button" class="btn btn-light btn-xs border rounded-pill ml-1 px-2 py-0 fk-btn-copy"
                                        onclick="lnktCopyBankText('<?= htmlspecialchars($bankAccountNo, ENT_QUOTES) ?>', this)" title="Sao chép số tài khoản">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            <div class="col-sm-6 mb-2 mb-sm-0">
                                Số tiền chính xác:
                                <strong class="text-peach" id="lnkt_vietQrAmount"><?= number_format($orderTotal, 0, ',', '.') ?> đ</strong>
                                <button type="button" id="lnkt_btnCopyAmount" class="btn btn-light btn-xs border rounded-pill ml-1 px-2 py-0 fk-btn-copy"
                                        onclick="lnktCopyBankText('<?= (int)round($orderTotal) ?>', this)" title="Sao chép số tiền">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            <div class="col-12 mt-2 pt-2 border-top">
                                Nội dung chuyển khoản bắt buộc:
                                <strong class="badge badge-light border text-slate px-2 py-1 ml-1" id="lnkt_vietQrContent"><?= htmlspecialchars($transferMemo) ?></strong>
                                <button type="button" id="lnkt_btnCopyMemo" class="btn btn-light btn-xs border rounded-pill ml-1 px-2 py-0 fk-btn-copy"
                                        onclick="lnktCopyBankText('<?= htmlspecialchars($transferMemo, ENT_QUOTES) ?>', this)" title="Sao chép nội dung chuyển khoản">
                                    <i class="fa-regular fa-copy mr-1"></i>Chép nội dung
                                </button>
                            </div>
                        </div>

                        <div id="lnkt_vietQrActionRow" class="d-flex flex-wrap align-items-center <?= ($canConfirmQr || $canSwitchPayment) ? '' : 'd-none' ?>">
                            <?php if ($canConfirmQr): ?>
                                <button type="button" id="lnkt_btnConfirmVietQr"
                                        class="btn btn-pastel-primary font-weight-bold px-4 py-2 shadow-sm mr-2 mb-2"
                                        onclick="lnktCheckVietQrPayment(<?= $orderId ?>, true)">
                                    <i class="fa-solid fa-rotate mr-2"></i>Tôi đã chuyển khoản — Kiểm tra ngay
                                </button>
                            <?php endif; ?>
                            <?php if ($canSwitchPayment): ?>
                                <button type="button" id="lnkt_btnSwitchToCod"
                                        class="btn btn-pastel-outline font-weight-bold px-3 py-2 mb-2"
                                        onclick="lnktSwitchOrderPaymentMethod(<?= $orderId ?>, 'cod')">
                                    <i class="fa-solid fa-money-bill-wave mr-1"></i>Đổi sang Tiền mặt (COD)
                                </button>
                            <?php endif; ?>
                        </div>
                        <div id="lnkt_vietQrAlert" class="alert small py-2 mt-2 mb-0 d-none"></div>
                    </div>
                </div>
            </div>

            <!-- BANNER ĐÃ THANH TOÁN THÀNH CÔNG (Bưu tá thu hộ 0 đồng) -->
            <div id="lnkt_paidReceiptBanner" class="mt-4 p-4 rounded fk-paid-receipt-box shadow-sm <?= ($hasOrder && $isPaid) ? '' : 'd-none' ?>">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <span class="badge badge-pill badge-mint px-3 py-1 font-weight-bold mr-2">
                                <i class="fa-solid fa-circle-check mr-1"></i>ĐÃ THANH TOÁN HOÀN TẤT
                            </span>
                            <small class="text-slate font-weight-bold" id="lnkt_paidTxMeta">
                                <?php if ($txCode !== ''): ?>
                                    MB Bank • Mã GD: <?= htmlspecialchars($txCode) ?><?= $paidAtLabel !== '' ? (' • Lúc ' . htmlspecialchars($paidAtLabel)) : '' ?>
                                <?php else: ?>
                                    Đã ghi nhận thanh toán thành công trên hệ thống FashionKids
                                <?php endif; ?>
                            </small>
                        </div>
                        <h5 class="font-weight-bold text-slate mb-1" id="lnkt_paidHeadline">
                            Shop đã nhận đủ <?= number_format($orderTotal, 0, ',', '.') ?> đồng qua chuyển khoản
                        </h5>
                        <p class="small text-muted-custom mb-0">
                            Nhân viên sẽ đối chiếu và tiến hành xác nhận đóng gói. Khi giao hàng cho bưu tá, trên phiếu dán đơn sẽ ghi rõ thu hộ <strong>0 đồng</strong>.
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-right">
                        <div class="p-3 rounded bg-white border text-center d-inline-block w-100">
                            <small class="text-muted-custom font-weight-bold d-block text-uppercase">Số tiền cần trả khi nhận hàng</small>
                            <div class="h3 font-weight-bold text-success mb-0">0 đồng</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL THANH TOÁN TIỀN MẶT (COD - CHO PHÉP ĐỔI SANG VIETQR KHI CHỜ DUYỆT) -->
            <div id="lnkt_codPaymentPanel" class="mt-4 p-4 rounded fk-cod-panel shadow-sm <?= $isCodUnpaid ? '' : 'd-none' ?>">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                        <span class="badge badge-pill badge-butter px-3 py-1 font-weight-bold mb-2">
                            <i class="fa-solid fa-money-bill-wave mr-1"></i>Thanh toán tiền mặt khi nhận hàng (COD)
                        </span>
                        <h5 class="font-weight-bold text-slate mb-1">
                            Số tiền cần thanh toán cho bưu tá: <span class="text-peach" id="lnkt_codPanelAmount"><?= number_format($orderTotal, 0, ',', '.') ?> đồng</span>
                        </h5>
                        <p class="small text-muted-custom mb-0">
                            Đơn hàng sẽ in hóa đơn ghi rõ tổng tiền thu hộ <strong><?= number_format($orderTotal, 0, ',', '.') ?> đồng</strong> (được đồng kiểm trước khi trả tiền). Hoặc bạn có thể chuyển sang thanh toán online qua MB Bank ngay.
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-right">
                        <button type="button" id="lnkt_btnSwitchToVietQr"
                                class="btn btn-pastel-primary btn-sm font-weight-bold px-3 py-2 <?= $canSwitchPayment ? '' : 'd-none' ?>"
                                onclick="lnktSwitchOrderPaymentMethod(<?= $orderId ?>, 'vietqr')">
                            <i class="fa-solid fa-qrcode mr-1"></i>Đổi sang Quét mã VietQR
                        </button>
                    </div>
                </div>
                <div id="lnkt_codSwitchAlert" class="alert small py-2 mt-2 mb-0 d-none"></div>
            </div>

            <!-- BANNER QUÁ HẠN THANH TOÁN 24 GIỜ -->
            <div id="lnkt_expiredPaymentBanner" class="mt-4 p-4 rounded fk-expired-banner shadow-sm <?= $isCancelledExp ? '' : 'd-none' ?>">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <span class="badge badge-pill badge-danger px-3 py-1 font-weight-bold mb-2">
                            <i class="fa-solid fa-hourglass-end mr-1"></i>Quá Hạn Thanh Toán 24 Giờ
                        </span>
                        <h5 class="font-weight-bold text-danger mb-1">Đơn hàng đã tự động hủy &amp; Shop không xác nhận đơn</h5>
                        <p class="small text-slate mb-0">
                            Đơn hàng VietQR chưa nhận đủ tiền chuyển khoản trong vòng 24 giờ nên hệ thống đã tự động hủy và hoàn sản phẩm về kho.
                        </p>
                    </div>
                    <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-primary btn-sm font-weight-bold mt-2 mt-md-0 px-3 py-2">
                        <i class="fa-solid fa-rotate-left mr-1"></i>Đặt Lại Đơn Mới
                    </a>
                </div>
            </div>

            <!-- THÔNG BÁO KHI ĐƠN HÀNG ĐẠT TRẠNG THÁI HOÀN TẤT -->
            <div id="lnkt_completedReviewBanner" class="mt-4 p-3 p-md-4 rounded lnkt-completed-callout shadow-sm <?= $statusKey === 'hoan_tat' ? '' : 'd-none' ?>">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                        <div class="d-flex align-items-center mb-1 flex-wrap">
                            <span class="badge badge-mint px-2 py-1 rounded-pill mr-2">Giao hàng thành công</span>
                            <small class="font-weight-bold text-slate" id="lnkt_completedPointsText">
                                Đã tích lũy +<?= number_format($orderRewardPts) ?> điểm thưởng vào thẻ VIP <?= htmlspecialchars((string)($trackedOrder['khach_hang_hang'] ?? $trackedOrder['ten_hang'] ?? 'thành viên')) ?>
                            </small>
                        </div>
                        <h5 class="font-weight-bold text-slate mb-1">
                            Cảm ơn phụ huynh đã mua sắm tại FashionKids!
                        </h5>
                        <p class="small text-muted-custom mb-0">
                            Đơn hàng đã giao thành công. Hãy chia sẻ cảm nhận về chất liệu vải sợi tre cho từng sản phẩm ở bảng bên dưới nhé!
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-right" id="lnkt_bannerReviewBtnWrap">
                        <?php if ($statusKey === 'hoan_tat' && $isOwner && $firstPendingReviewItem): ?>
                            <button type="button"
                                    class="btn lnkt-btn-buttercup px-4 py-2 shadow-sm font-weight-bold"
                                    data-toggle="modal" data-target="#modalReview"
                                    onclick="lnktOpenProductReviewModal('<?= htmlspecialchars($firstPendingReviewItem['ten_san_pham'], ENT_QUOTES) ?>', <?= (int)$firstPendingReviewItem['ma_san_pham'] ?>, <?= $orderId ?>)">
                                &#9733; Viết đánh giá sản phẩm
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             CHI TIẾT SẢN PHẨM TRONG ĐƠN & TÓM TẮT THANH TOÁN
             ==================================================================== -->
        <div class="row">
            <div class="col-12 col-lg-8 mb-4">
                <div class="card card-pastel shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-box-archive text-peach mr-2"></i>Chi Tiết Sản Phẩm Trong Đơn
                        </h6>
                        <span class="small text-muted-custom font-weight-bold" id="lnkt_trackedItemsCountLabel"><?= count($trackedItems) ?> sản phẩm</span>
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
                                        <th class="text-center">Đánh Giá</th>
                                    </tr>
                                </thead>
                                <tbody id="lnkt_trackedItemsBody">
                                    <?php foreach ($trackedItems as $item):
                                        $pid      = (int)$item['ma_san_pham'];
                                        $qty      = (int)$item['so_luong'];
                                        $unitP    = (float)$item['don_gia_mua'];
                                        $lineSum  = $qty * $unitP;
                                        $reviewed = !empty($item['da_danh_gia']);
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="lbn-inv-thumb mr-3 flex-shrink-0">
                                                        <img src="<?= htmlspecialchars((string)$item['image']) ?>" alt="<?= htmlspecialchars((string)$item['ten_san_pham']) ?>" class="lbn-thumb-img">
                                                    </span>
                                                    <div>
                                                        <a href="/FashionKids/views/pages/product-detail.php?id=<?= $pid ?>"
                                                           class="font-weight-bold text-slate text-decoration-none d-block">
                                                            <?= htmlspecialchars((string)$item['ten_san_pham']) ?>
                                                        </a>
                                                        <small class="text-muted-custom">Đơn giá: <strong class="text-slate"><?= number_format($unitP, 0, ',', '.') ?> đ</strong></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-pill badge-sky px-2 py-1"><?= htmlspecialchars((string)$item['kich_co_chon']) ?></span>
                                            </td>
                                            <td class="text-center font-weight-bold">x<?= $qty ?></td>
                                            <td class="text-right font-weight-bold text-peach text-nowrap">
                                                <?= number_format($lineSum, 0, ',', '.') ?> đ
                                            </td>
                                            <td class="text-center text-nowrap" data-review-cell-pid="<?= $pid ?>">
                                                <?php if ($statusKey === 'hoan_tat' && $isOwner): ?>
                                                    <?php if ($reviewed): ?>
                                                        <span class="badge badge-mint px-2 py-1 rounded-pill font-weight-bold">
                                                            <i class="fa-solid fa-star text-warning mr-1"></i>Đã đánh giá (<?= (int)($item['so_sao_danh_gia'] ?? 5) ?>/5)
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                                class="btn btn-sm lnkt-btn-buttercup lnkt-item-review-btn px-3 py-1 font-weight-bold shadow-sm"
                                                                data-toggle="modal" data-target="#modalReview"
                                                                onclick="lnktOpenProductReviewModal('<?= htmlspecialchars((string)$item['ten_san_pham'], ENT_QUOTES) ?>', <?= $pid ?>, <?= $orderId ?>)">
                                                            &#9733; Viết đánh giá sản phẩm
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($statusKey === 'da_huy'): ?>
                                                    <span class="small text-muted-custom">Đơn đã hủy</span>
                                                <?php elseif ($statusKey === 'hoan_tat' && !$isOwner): ?>
                                                    <span class="small text-muted-custom">Chỉ chủ đơn mới đánh giá</span>
                                                <?php else: ?>
                                                    <span class="small text-muted-custom lnkt-item-pending-review">Chờ hoàn tất đơn</span>
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

            <!-- Cột Phải: Tổng kết thanh toán của vận đơn -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card card-pastel shadow-sm h-100 p-4">
                    <h6 class="font-weight-bold text-slate mb-3 pb-2 border-bottom">
                        <i class="fa-solid fa-receipt text-peach mr-2"></i>Tóm Tắt Thanh Toán Đơn Hàng
                    </h6>

                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted-custom">Tiền hàng tạm tính:</span>
                        <span class="font-weight-bold text-slate" id="lnkt_sumSubtotal"><?= number_format($orderSubtotal, 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted-custom" id="lnkt_sumVipLabel">
                            Ưu đãi VIP (<?= htmlspecialchars((string)($trackedOrder['khach_hang_hang'] ?? $trackedOrder['ten_hang'] ?? 'Thành viên')) ?>):
                        </span>
                        <span class="font-weight-bold text-success" id="lnkt_sumDiscount">
                            <?= $orderDiscount > 0 ? ('-' . number_format($orderDiscount, 0, ',', '.') . ' đ') : '0 đ' ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted-custom">Phí vận chuyển:</span>
                        <span class="badge badge-mint px-2 py-0 rounded-pill">Miễn phí (0 đ)</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted-custom">Phương thức thanh toán:</span>
                        <span class="font-weight-bold text-slate" id="lnkt_sumPaymentMethod">
                            <?= $payMethod === 'vietqr' ? 'Chuyển khoản VietQR MB Bank' : 'Thanh toán khi nhận hàng (COD)' ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted-custom">Trạng thái thanh toán:</span>
                        <span id="lnkt_sumPaymentStatus" class="font-weight-bold <?= $isPaid ? 'text-success' : ($isCancelledExp ? 'text-danger' : 'text-warning') ?>">
                            <?= $isPaid ? 'Đã thanh toán' : ($isCancelledExp ? 'Quá hạn 24h (Đã hủy)' : ($payMethod === 'vietqr' ? 'Chờ chuyển khoản' : 'Thanh toán khi giao')) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between small mb-3 pb-2 border-bottom">
                        <span class="text-muted-custom">Cần trả bưu tá khi giao:</span>
                        <strong id="lnkt_sumCodCollect" class="<?= $codCollectAmt <= 0 ? 'text-success' : 'text-peach' ?>">
                            <?= number_format($codCollectAmt, 0, ',', '.') ?> đồng
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="font-weight-bold text-slate">Tổng thanh toán:</span>
                        <span class="h5 font-weight-bold text-peach mb-0" id="lnkt_sumTotal"><?= number_format($orderTotal, 0, ',', '.') ?> đ</span>
                    </div>

                    <div class="p-3 rounded bg-milk-cream border small text-slate mt-auto">
                        <div class="font-weight-bold mb-1">
                            <i class="fa-solid fa-location-dot text-peach mr-1"></i>Địa chỉ nhận hàng:
                        </div>
                        <div id="lnkt_sumAddress"><?= htmlspecialchars($fullAddress) ?></div>
                        <?php if ($hasOrder && !empty($trackedOrder['ghi_chu'])): ?>
                            <div class="mt-2 pt-2 border-top text-muted-custom" id="lnkt_sumNoteWrap">
                                <strong>Ghi chú:</strong> <span id="lnkt_sumNote"><?= htmlspecialchars((string)$trackedOrder['ghi_chu']) ?></span>
                            </div>
                        <?php else: ?>
                            <div class="mt-2 pt-2 border-top text-muted-custom d-none" id="lnkt_sumNoteWrap">
                                <strong>Ghi chú:</strong> <span id="lnkt_sumNote"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (file_exists(__DIR__ . "/../modals/modal-review.php")) {
    require_once __DIR__ . "/../modals/modal-review.php";
}
require_once __DIR__ . "/../layouts/footer.php";
?>
