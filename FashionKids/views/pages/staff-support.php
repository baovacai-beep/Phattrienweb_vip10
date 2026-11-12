<?php
$page_title = "Bàn Làm Việc CSKH Trực Tuyến";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
$hasAccess = Rbac::enforceCurrentViewAccess('staff-support.php');
$appBase   = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';

if (!$hasAccess):
?>
<div class="container my-5">
    <div class="card card-pastel border-0 shadow-sm p-5 text-center mx-auto bg-white" style="max-width: 620px;">
        <div class="mb-3">
            <span class="badge badge-pill badge-danger px-3 py-2 font-weight-bold">
                <i class="fa-solid fa-shield-halved mr-1"></i>Giới hạn phân quyền CSKH
            </span>
        </div>
        <h4 class="font-weight-bold text-slate mb-2">Khu vực dành cho Nhân viên CSKH &amp; Quản trị viên</h4>
        <p class="text-muted-custom mb-4">
            Vui lòng đăng nhập bằng tài khoản <strong>Nhân viên</strong> hoặc <strong>Quản trị viên</strong> để truy cập Bàn làm việc CSKH và đồng bộ tin nhắn trực tuyến với khách hàng.
        </p>
        <div class="d-flex flex-wrap justify-content-center fk-btn-toolbar">
            <button type="button" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold" data-toggle="modal" data-target="#authModal">
                <i class="fa-solid fa-right-to-bracket mr-1"></i>Đăng nhập ngay
            </button>
            <a href="<?= $appBase ?>/index.php" class="btn btn-light border rounded-pill px-4 font-weight-bold text-slate">
                <i class="fa-solid fa-house mr-1"></i>Về trang chủ
            </a>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . "/../layouts/footer.php";
return;
endif;

require_once __DIR__ . "/../../models/RealtimeContentModel.php";
require_once __DIR__ . "/../../models/ProductModel.php";

$rtModel  = new RealtimeContentModel();
$sessions = $rtModel->getChatSessions();
$activeSessionId = trim((string)($_GET['session_id'] ?? ($sessions[0]['session_id'] ?? 'sess_guest_123456')));
if ($activeSessionId !== '') {
    $rtModel->markChatSessionAsRead($activeSessionId);
    $sessions = $rtModel->getChatSessions();
}
$messages = $rtModel->getChatMessages($activeSessionId);
$activeSessionMeta = $sessions[0] ?? [
    'session_id'    => $activeSessionId,
    'customer_name' => 'Khách hàng trực tuyến',
    'vip_label'     => 'Khách Trực Tuyến',
    'badge_class'   => 'badge-peach',
];
foreach ($sessions as $sItem) {
    if (($sItem['session_id'] ?? '') === $activeSessionId) {
        $activeSessionMeta = $sItem;
        break;
    }
}

// Tải danh sách sản phẩm thật từ CSDL phục vụ Tra cứu Tồn kho Nhanh
$quickStockProducts = [];
try {
    $prodModel = new ProductModel();
    $dbProducts = $prodModel->layTatCaSanPhamChuanHoa();
    foreach (array_slice($dbProducts, 0, 12) as $p) {
        $pid   = (int)($p['id'] ?? $p['ma_san_pham'] ?? 0);
        $stock = (int)($p['stock'] ?? $p['so_luong_ton_kho'] ?? 0);
        $catType = (string)($p['category_type'] ?? 'quan_ao');
        $imgRaw  = (string)($p['image'] ?? $p['anh_dai_dien'] ?? 'vay-cong-chua-hoa-cuc.jpg');
        $imgSrc  = (preg_match('#^https?://#i', $imgRaw) || strpos($imgRaw, '/') === 0)
            ? $imgRaw
            : ($appBase . '/images/' . ltrim($imgRaw, '/'));
        $sizeHint = ($catType === 'giay_dep')
            ? 'Size 22 • Size 24 • Size 26 (Còn ' . $stock . ' đôi)'
            : 'Size 1 • Size 2 • Size 3 (Tổng kho: ' . $stock . ' cái)';
        $quickStockProducts[] = [
            'id'    => $pid,
            'sku'   => 'FK-SP-' . str_pad((string)$pid, 3, '0', STR_PAD_LEFT),
            'name'  => (string)($p['name'] ?? $p['ten_san_pham'] ?? 'Sản phẩm FashionKids'),
            'price' => (float)($p['price'] ?? $p['gia_ban'] ?? 0),
            'stock' => $stock,
            'sizes' => $sizeHint,
            'image' => $imgSrc,
        ];
    }
} catch (Throwable $e) {}

if (empty($quickStockProducts)) {
    $quickStockProducts = [
        [
            'id'    => 101,
            'sku'   => 'FK-SP-101',
            'name'  => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
            'price' => 285000,
            'stock' => 25,
            'sizes' => 'Size 1 • Size 2 • Size 3 (Còn 25 cái)',
            'image' => $appBase . '/images/vay-cong-chua-hoa-cuc.jpg'
        ],
        [
            'id'    => 102,
            'sku'   => 'FK-SP-102',
            'name'  => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
            'price' => 225000,
            'stock' => 30,
            'sizes' => 'Size 1 • Size 2 • Size 3 (Còn 30 bộ)',
            'image' => $appBase . '/images/bo-thun-khung-long.jpg'
        ],
    ];
}
?>

<div class="container-fluid my-4 px-lg-5" id="fk_staff_chat_section">
    <!-- Header Bàn Làm Việc CSKH Trực Tuyến -->
    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-4 pb-2 border-bottom">
        <div class="min-w-0">
            <h3 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-headset text-peach mr-2"></i>Bàn Làm Việc CSKH &amp; Tư Vấn Trực Tuyến
            </h3>
            <small class="text-muted-custom">
                Đồng bộ tin nhắn hai chiều theo thời gian thực giữa Hộp thoại Khách hàng &amp; Nhân viên/Admin
            </small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center fk-btn-toolbar">
            <span class="badge badge-pill badge-mint px-3 py-2 font-weight-bold text-nowrap">
                <span class="fk-live-pulse-dot mr-1"></span>Đang trực tuyến (<span id="fk_staffPollClock"><?= date('H:i:s') ?></span>)
            </span>
            <?php if (Rbac::isAdmin()): ?>
                <a href="<?= $appBase ?>/views/pages/admin-dashboard.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Bàn Làm Việc Admin
                </a>
            <?php else: ?>
                <a href="<?= $appBase ?>/views/pages/staff-orders.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3 text-nowrap">
                    <i class="fa-solid fa-boxes-packing mr-1"></i> Bàn Duyệt Đơn
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thanh Điều Hướng Quản Trị Hệ Thống -->
    <div class="card card-pastel border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap justify-content-start align-items-center fk-btn-toolbar">
            <?php if (Rbac::isAdmin()): ?>
                <a href="<?= $appBase ?>/views/pages/admin-dashboard.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                    <i class="fa-solid fa-chart-pie text-peach mr-1"></i> Tổng Quan
                </a>
            <?php endif; ?>
            <a href="<?= $appBase ?>/views/pages/admin-inventory.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-boxes-stacked text-peach mr-1"></i> Kho Hàng
            </a>
            <a href="<?= $appBase ?>/views/pages/staff-orders.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-truck-ramp-box text-warning mr-1"></i> Duyệt Đơn
            </a>
            <a href="<?= $appBase ?>/views/pages/staff-support.php" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold text-nowrap">
                <i class="fa-solid fa-headset mr-1"></i> CSKH Trực Tuyến
            </a>
            <a href="<?= $appBase ?>/views/pages/staff-casting-review.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-crown text-warning mr-1"></i> Duyệt KOL Nhí
            </a>
            <?php if (Rbac::isAdmin()): ?>
                <a href="<?= $appBase ?>/views/pages/admin-banners.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                    <i class="fa-solid fa-images text-info mr-1"></i> Lịch Sự Kiện Banner
                </a>
            <?php endif; ?>
            <a href="<?= $appBase ?>/views/pages/admin-cms-blog.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate text-nowrap border">
                <i class="fa-solid fa-newspaper text-primary mr-1"></i> Quản Lý Tạp Chí
            </a>
        </div>
    </div>

    <div class="row">
        <!-- CỘT 1 (3 CỘT): DANH SÁCH PHIÊN KHÁCH HÀNG ĐANG NHẮN TIN REALTIME -->
        <div class="col-12 col-lg-3 mb-4">
            <div class="card card-pastel border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center fk-card-header-row">
                    <h6 class="font-weight-bold text-slate mb-0 min-w-0">
                        <i class="fa-solid fa-users-viewfinder text-peach mr-1"></i> Phiên Hội Thoại (<span id="fk_staffSessionCount"><?= count($sessions) ?></span>)
                    </h6>
                    <button type="button" onclick="fkPollStaffChatNow()" class="btn btn-light btn-sm border rounded-pill px-2 py-1 small flex-shrink-0" title="Làm mới ngay">
                        <i class="fa-solid fa-arrows-rotate text-peach"></i>
                    </button>
                </div>

                <div class="list-group list-group-flush" id="fk_staffSessionList" style="max-height: 540px; overflow-y: auto;">
                    <?php foreach ($sessions as $sess): ?>
                        <?php $isActive = ($sess['session_id'] === $activeSessionId); ?>
                        <a href="javascript:void(0)"
                           onclick="fkSwitchStaffChatSession('<?= htmlspecialchars($sess['session_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sess['customer_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sess['vip_label'], ENT_QUOTES) ?>', this)"
                           class="list-group-item list-group-item-action py-3 fk-staff-session-item <?= $isActive ? 'bg-milk-cream border-left-peach' : '' ?>"
                           data-session-id="<?= htmlspecialchars($sess['session_id']) ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center fk-card-header-row mb-1">
                                <h6 class="mb-0 font-weight-bold text-slate small min-w-0">
                                    <span class="fk-live-pulse-dot mr-1"></span><?= htmlspecialchars($sess['customer_name']) ?>
                                </h6>
                                <?php if (!empty($sess['unread'])): ?>
                                    <span class="badge badge-danger badge-pill fk-session-unread text-nowrap flex-shrink-0"><?= (int)$sess['unread'] ?> mới</span>
                                <?php else: ?>
                                    <small class="text-muted-custom text-nowrap flex-shrink-0"><?= htmlspecialchars($sess['last_time']) ?></small>
                                <?php endif; ?>
                            </div>
                            <span class="badge <?= htmlspecialchars($sess['badge_class']) ?> small mb-1"><?= htmlspecialchars($sess['vip_label']) ?></span>
                            <p class="mb-0 small text-muted-custom text-truncate"><?= htmlspecialchars($sess['last_message']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- CỘT 2 (6 CỘT): CỬA SỔ HỘI THOẠI TRỰC TUYẾN & MẪU CÂU TRẢ LỜI NHANH -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card card-pastel border-0 shadow-sm h-100 d-flex flex-column bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
                    <div class="min-w-0">
                        <span class="font-weight-bold text-slate" id="fk_activeChatCustomerName"><?= htmlspecialchars((string)($activeSessionMeta['customer_name'] ?? 'Khách hàng trực tuyến')) ?></span>
                        <span class="badge badge-pill <?= htmlspecialchars((string)($activeSessionMeta['badge_class'] ?? 'badge-peach')) ?> ml-2 text-nowrap" id="fk_activeChatVipBadge"><?= htmlspecialchars((string)($activeSessionMeta['vip_label'] ?? 'Khách Trực Tuyến')) ?></span>
                        <div class="small text-muted-custom">
                            Mã phiên: <span id="fk_activeChatSessionCode" class="font-weight-bold text-slate"><?= htmlspecialchars($activeSessionId) ?></span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="<?= $appBase ?>/views/pages/staff-orders.php" class="btn btn-pastel-sky btn-sm rounded-pill px-3 font-weight-bold text-nowrap">
                            <i class="fa-solid fa-file-invoice mr-1"></i>Kiểm tra đơn khách
                        </a>
                    </div>
                </div>

                <!-- Khu vực hiển thị tin nhắn cuộn Realtime -->
                <div class="card-body chat-scroll-staff p-4" id="fk_staffChatMessagesBox">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted-custom py-5 small">
                            <i class="fa-regular fa-comments fa-2x mb-2 text-peach"></i>
                            <div>Phiên hội thoại này chưa có tin nhắn nào. Hãy gửi lời chào để bắt đầu tư vấn phụ huynh!</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <?php if ($m['sender'] === 'khach_hang'): ?>
                                <div class="d-flex mb-3">
                                    <div class="p-3 bg-white text-slate shadow-sm chat-bubble-received border">
                                        <div class="small font-weight-bold text-peach mb-1">
                                            <i class="fa-solid fa-user mr-1"></i>Phụ huynh • <?= htmlspecialchars($m['time']) ?>
                                        </div>
                                        <div><?= nl2br(htmlspecialchars($m['content'])) ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="p-3 shadow-sm chat-bubble-sent">
                                        <div class="small text-light mb-1">
                                            <i class="fa-solid fa-headset mr-1"></i>Chuyên viên CSKH • <?= htmlspecialchars($m['time']) ?>
                                        </div>
                                        <div><?= nl2br(htmlspecialchars($m['content'])) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Thanh câu trả lời mẫu nhanh (Quick Replies) -->
                <div class="px-3 py-2 bg-milk-cream border-top d-flex flex-wrap align-items-center fk-btn-toolbar">
                    <small class="font-weight-bold text-slate text-nowrap"><i class="fa-solid fa-bolt text-peach mr-1"></i>Trả lời nhanh:</small>
                    <button type="button" onclick="fkStaffQuickReply('Dạ với chiều cao 90-100cm và nặng 12-15kg, bé mặc Size 2 (2-3T) chất sợi tre là vừa đẹp ạ!')"
                            class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                        + Tư vấn Size 2 (90-100cm)
                    </button>
                    <button type="button" onclick="fkStaffQuickReply('Dạ đơn hàng của mẹ đã được xác nhận và đang đóng gói giao nhanh trong hôm nay ạ!')"
                            class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                        + Báo tiến độ đơn hàng
                    </button>
                    <button type="button" onclick="fkStaffQuickReply('Dạ vải sợi tre FashionKids kháng khuẩn 99.8% chuẩn OEKO-TEX 100, mẹ yên tâm cho bé mặc nhé!')"
                            class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                        + Cam kết sợi tre
                    </button>
                </div>

                <!-- Form nhập tin nhắn phản hồi của Nhân viên / Admin -->
                <div class="card-footer bg-white border-top p-3">
                    <div id="fk_staffChatErrorAlert" class="alert alert-danger small py-1 px-3 rounded-pill d-none mb-2"></div>
                    <form class="d-flex" id="fk_formStaffChat">
                        <input type="hidden" id="fk_staffCurrentSessionId" value="<?= htmlspecialchars($activeSessionId) ?>">
                        <input type="text" id="fk_staffChatInput" class="form-control rounded-pill mr-2"
                               placeholder="Nhập nội dung phản hồi tư vấn cho phụ huynh..." autocomplete="off" required>
                        <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold shadow-sm text-nowrap">
                            Gửi <i class="fa-solid fa-paper-plane ml-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CỘT 3 (3 CỘT): TRA CỨU TỒN KHO NHANH & GỬI BÁO GIÁ VÀO HỘI THOẠI -->
        <div class="col-12 col-lg-3 mb-4">
            <div class="card card-pastel border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-magnifying-glass-chart text-peach mr-1"></i> Tra Cứu Tồn Kho Nhanh
                    </h6>
                    <small class="text-muted-custom">Kiểm tra tồn kho CSDL thật &amp; chèn báo giá vào khung phản hồi</small>
                </div>
                <div class="card-body p-3">
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-search text-peach"></i></span>
                        </div>
                        <input type="text" id="fk_staffStockSearchInput" class="form-control"
                               placeholder="Gõ tên váy, bộ thun, mã SKU..."
                               onkeyup="fkFilterStaffQuickStock(this.value)">
                    </div>

                    <div id="fk_staffQuickStockList" style="max-height: 490px; overflow-y: auto;">
                        <?php foreach ($quickStockProducts as $qp): ?>
                            <div class="p-3 rounded bg-milk-cream border mb-3 fk-staff-stock-card"
                                 data-search="<?= htmlspecialchars(mb_strtolower($qp['name'] . ' ' . $qp['sku'], 'UTF-8')) ?>">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="lbn-inv-thumb mr-2 flex-shrink-0">
                                        <img src="<?= htmlspecialchars($qp['image']) ?>" alt="<?= htmlspecialchars($qp['name']) ?>" class="lbn-thumb-img">
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-weight-bold text-slate small"><?= htmlspecialchars($qp['name']) ?></div>
                                        <small class="text-peach font-weight-bold text-nowrap"><?= number_format($qp['price'], 0, ',', '.') ?> đ</small>
                                        <span class="badge badge-pill <?= $qp['stock'] <= 5 ? 'badge-danger' : 'badge-mint' ?> ml-1 text-nowrap">
                                            Kho: <?= $qp['stock'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="small text-muted-custom mb-2">
                                    <?= htmlspecialchars($qp['sizes']) ?>
                                </div>
                                <button type="button"
                                        onclick="fkStaffQuickReply('Dạ mẫu <?= htmlspecialchars($qp['name'], ENT_QUOTES) ?> (Mã <?= $qp['sku'] ?>) giá <?= number_format($qp['price'], 0, ',', '.') ?>đ hiện đang còn <?= $qp['stock'] ?> sản phẩm ạ!')"
                                        class="btn btn-light btn-sm btn-block border rounded-pill font-weight-bold text-slate py-1">
                                    <i class="fa-solid fa-share-from-square text-peach mr-1"></i>Gửi báo giá &amp; tồn kho vào Chat
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
