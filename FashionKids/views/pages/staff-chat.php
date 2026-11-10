<?php
$page_title = "Bàn Làm Việc CSKH Trực Tuyến";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";

$rtModel  = new RealtimeContentModel();
$sessions = $rtModel->getChatSessions();
$activeSessionId = $_GET['session_id'] ?? ($sessions[0]['session_id'] ?? 'sess_guest_123456');
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

// Danh sách sản phẩm phục vụ Tra cứu Tồn kho Nhanh ngay trong màn hình Chat
$quickStockProducts = [
    [
        'id'    => 101,
        'sku'   => 'FK-LBN-01',
        'name'  => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'price' => 285000,
        'stock' => 25,
        'sizes' => 'Size 1 (Còn 8) • Size 2 (Còn 10) • Size 3 (Còn 7)',
        'image' => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg'
    ],
    [
        'id'    => 102,
        'sku'   => 'FK-LBN-02',
        'name'  => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'price' => 225000,
        'stock' => 30,
        'sizes' => 'Size 1 (Còn 10) • Size 2 (Còn 12) • Size 3 (Còn 8)',
        'image' => '/FashionKids/images/bo-thun-khung-long.jpg'
    ],
    [
        'id'    => 103,
        'sku'   => 'FK-LBN-03',
        'name'  => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'price' => 210000,
        'stock' => 14,
        'sizes' => 'Size 22 (Còn 5) • Size 24 (Còn 5) • Size 26 (Còn 4)',
        'image' => '/FashionKids/images/giay-sandal-tap-di.jpg'
    ],
    [
        'id'    => 104,
        'sku'   => 'FK-LBN-04',
        'name'  => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'price' => 320000,
        'stock' => 5,
        'sizes' => 'Size 2 (Còn 2) • Size 3 (Còn 3) — Sắp hết hàng',
        'image' => '/FashionKids/images/ao-khoac-cardigan.jpg'
    ]
];
?>

<div class="container-fluid my-4 px-lg-5" id="fk_staff_chat_section">
    <!-- Header Bàn Làm Việc CSKH Trực Tuyến -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="font-weight-bold text-slate mb-1">
                <i class="fa-solid fa-headset text-peach mr-2"></i>Bàn Làm Việc CSKH &amp; Tư Vấn Trực Tuyến
            </h3>
            <small class="text-muted-custom">
                Đồng bộ tin nhắn hai chiều giữa Hộp thoại Khách hàng &amp; Nhân viên Tư vấn
            </small>
        </div>
        <div class="mt-2 mt-md-0 d-flex align-items-center">
            <span class="badge badge-pill badge-mint px-3 py-2 mr-2 font-weight-bold">
                <span class="fk-live-pulse-dot mr-1"></span>Đang trực tuyến (<span id="fk_staffPollClock"><?= date('H:i:s') ?></span>)
            </span>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Bàn Làm Việc Admin
                </a>
            <?php else: ?>
                <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-primary btn-sm font-weight-bold rounded-pill px-3">
                    <i class="fa-solid fa-boxes-packing mr-1"></i> Bàn Duyệt Đơn
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thanh Điều Hướng Quản Trị Hệ Thống -->
    <div class="card card-pastel border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap justify-content-start align-items-center">
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-dashboard.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                    <i class="fa-solid fa-chart-pie text-peach mr-1"></i> Tổng Quan
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/admin-inventory.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-boxes-stacked text-peach mr-1"></i> Kho Hàng
            </a>
            <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-truck-ramp-box text-warning mr-1"></i> Duyệt Đơn
            </a>
            <a href="/FashionKids/views/pages/staff-chat.php" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold mr-2 mb-1">
                <i class="fa-solid fa-headset mr-1"></i> CSKH Trực Tuyến
            </a>
            <a href="/FashionKids/views/pages/staff-casting-review.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                <i class="fa-solid fa-crown text-warning mr-1"></i> Duyệt KOL Nhí
            </a>
            <?php if (Rbac::isAdmin()): ?>
                <a href="/FashionKids/views/pages/admin-banners.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mr-2 mb-1 border">
                    <i class="fa-solid fa-images text-info mr-1"></i> Lịch Sự Kiện Banner
                </a>
            <?php endif; ?>
            <a href="/FashionKids/views/pages/admin-cms-blog.php" class="btn btn-light btn-sm rounded-pill font-weight-bold text-slate mb-1 border">
                <i class="fa-solid fa-newspaper text-primary mr-1"></i> Quản Lý Tạp Chí
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT 1 (3 CỘT): DANH SÁCH PHIÊN KHÁCH HÀNG ĐANG CHAT REALTIME
             ================================================================ -->
        <div class="col-12 col-lg-3 mb-4">
            <div class="card card-pastel border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-users-viewfinder text-peach mr-1"></i> Phiên Hội Thoại (<span id="fk_staffSessionCount"><?= count($sessions) ?></span>)
                    </h6>
                    <button type="button" onclick="fkPollStaffChatNow()" class="btn btn-light btn-sm border rounded-pill px-2 py-0 small" title="Làm mới ngay">
                        <i class="fa-solid fa-arrows-rotate text-peach"></i>
                    </button>
                </div>

                <div class="list-group list-group-flush" id="fk_staffSessionList">
                    <?php foreach ($sessions as $idx => $sess): ?>
                        <?php $isActive = ($sess['session_id'] === $activeSessionId); ?>
                        <a href="javascript:void(0)"
                           onclick="fkSwitchStaffChatSession('<?= htmlspecialchars($sess['session_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sess['customer_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sess['vip_label'], ENT_QUOTES) ?>', this)"
                           class="list-group-item list-group-item-action py-3 fk-staff-session-item <?= $isActive ? 'bg-milk-cream border-left-peach' : '' ?>"
                           data-session-id="<?= htmlspecialchars($sess['session_id']) ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-weight-bold text-slate small">
                                    <span class="fk-live-pulse-dot mr-1"></span><?= htmlspecialchars($sess['customer_name']) ?>
                                </h6>
                                <?php if (!empty($sess['unread'])): ?>
                                    <span class="badge badge-danger badge-pill fk-session-unread"><?= $sess['unread'] ?> mới</span>
                                <?php else: ?>
                                    <small class="text-muted-custom"><?= htmlspecialchars($sess['last_time']) ?></small>
                                <?php endif; ?>
                            </div>
                            <span class="badge <?= htmlspecialchars($sess['badge_class']) ?> small mb-1"><?= htmlspecialchars($sess['vip_label']) ?></span>
                            <p class="mb-0 small text-muted-custom text-truncate"><?= htmlspecialchars($sess['last_message']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT 2 (6 CỘT): CỬA SỔ HỘI THOẠI TRỰC TUYẾN & MẪU CÂU TRẢ LỜI NHANH
             ================================================================ -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card card-pastel border-0 shadow-sm h-100 d-flex flex-column bg-white">
                <!-- Header Phiên Chat Đang Mở -->
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <span class="font-weight-bold text-slate" id="fk_activeChatCustomerName"><?= htmlspecialchars((string)($activeSessionMeta['customer_name'] ?? 'Khách hàng trực tuyến')) ?></span>
                        <span class="badge badge-pill <?= htmlspecialchars((string)($activeSessionMeta['badge_class'] ?? 'badge-peach')) ?> ml-2" id="fk_activeChatVipBadge"><?= htmlspecialchars((string)($activeSessionMeta['vip_label'] ?? 'Khách Trực Tuyến')) ?></span>
                        <div class="small text-muted-custom">
                            Mã phiên: <span id="fk_activeChatSessionCode" class="font-weight-bold text-slate"><?= htmlspecialchars($activeSessionId) ?></span>
                        </div>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-pastel-sky btn-sm rounded-pill px-3 font-weight-bold">
                            <i class="fa-solid fa-file-invoice mr-1"></i>Kiểm tra đơn khách
                        </a>
                    </div>
                </div>

                <!-- Khu vực hiển thị tin nhắn cuộn Realtime -->
                <div class="card-body chat-scroll-staff p-4" id="fk_staffChatMessagesBox">
                    <?php foreach ($messages as $m): ?>
                        <?php if ($m['sender'] === 'khach_hang'): ?>
                            <div class="d-flex mb-3">
                                <div class="p-3 bg-white text-slate shadow-sm chat-bubble-received border">
                                    <div class="small font-weight-bold text-peach mb-1">
                                        <i class="fa-solid fa-user mr-1"></i>Phụ huynh • <?= htmlspecialchars($m['time']) ?>
                                    </div>
                                    <div><?= htmlspecialchars($m['content']) ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-end mb-3">
                                <div class="p-3 shadow-sm chat-bubble-sent">
                                    <div class="small text-light mb-1">
                                        <i class="fa-solid fa-headset mr-1"></i>Chuyên viên CSKH • <?= htmlspecialchars($m['time']) ?>
                                    </div>
                                    <div><?= htmlspecialchars($m['content']) ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Thanh câu trả lời mẫu nhanh (Quick Replies) -->
                <div class="px-3 py-2 bg-milk-cream border-top d-flex flex-wrap align-items-center">
                    <small class="font-weight-bold text-slate mr-2"><i class="fa-solid fa-bolt text-peach mr-1"></i>Trả lời nhanh:</small>
                    <button type="button" onclick="fkStaffQuickReply('Dạ với chiều cao 90-100cm và nặng 12-15kg, bé mặc Size 2 (2-3T) chất sợi tre là vừa đẹp ạ!')"
                            class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small text-slate">
                        + Tư vấn Size 2 (90-100cm)
                    </button>
                    <button type="button" onclick="fkStaffQuickReply('Dạ đơn hàng của mẹ đã được xác nhận và đang đóng gói giao nhanh trong hôm nay ạ!')"
                            class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small text-slate">
                        + Báo tiến độ đơn hàng
                    </button>
                    <button type="button" onclick="fkStaffQuickReply('Dạ vải sợi tre FashionKids kháng khuẩn 99.8% chuẩn OEKO-TEX 100, mẹ yên tâm cho bé mặc nhé!')"
                            class="btn btn-light btn-sm border rounded-pill mb-1 py-0 px-2 small text-slate">
                        + Cam kết sợi tre
                    </button>
                </div>

                <!-- Form nhập tin nhắn phản hồi của Nhân viên -->
                <div class="card-footer bg-white border-top p-3">
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

        <!-- ================================================================
             CỘT 3 (3 CỘT): TRA CỨU TỒN KHO NHANH & GỬI BÁO GIÁ VÀO CHAT
             ================================================================ -->
        <div class="col-12 col-lg-3 mb-4">
            <div class="card card-pastel border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-magnifying-glass-chart text-peach mr-1"></i> Tra Cứu Tồn Kho Nhanh
                    </h6>
                    <small class="text-muted-custom">Kiểm tra tồn kho hệ thống &amp; chèn báo giá vào khung chat</small>
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

                    <div id="fk_staffQuickStockList">
                        <?php foreach ($quickStockProducts as $qp): ?>
                            <div class="p-3 rounded bg-milk-cream border mb-3 fk-staff-stock-card"
                                 data-search="<?= htmlspecialchars(mb_strtolower($qp['name'] . ' ' . $qp['sku'], 'UTF-8')) ?>">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="lbn-inv-thumb mr-2 flex-shrink-0">
                                        <img src="<?= htmlspecialchars($qp['image']) ?>" alt="<?= htmlspecialchars($qp['name']) ?>" class="lbn-thumb-img">
                                    </span>
                                    <div>
                                        <div class="font-weight-bold text-slate small"><?= htmlspecialchars($qp['name']) ?></div>
                                        <small class="text-peach font-weight-bold"><?= number_format($qp['price'], 0, ',', '.') ?> đ</small>
                                        <span class="badge badge-pill <?= $qp['stock'] <= 5 ? 'badge-danger' : 'badge-mint' ?> ml-1">
                                            Kho: <?= $qp['stock'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="small text-muted-custom mb-2">
                                    <?= htmlspecialchars($qp['sizes']) ?>
                                </div>
                                <button type="button"
                                        onclick="fkStaffQuickReply('Dạ mẫu <?= htmlspecialchars($qp['name'], ENT_QUOTES) ?> (Mã <?= $qp['sku'] ?>) giá <?= number_format($qp['price'], 0, ',', '.') ?>đ hiện đang còn hàng (<?= htmlspecialchars($qp['sizes'], ENT_QUOTES) ?>) ạ!')"
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
