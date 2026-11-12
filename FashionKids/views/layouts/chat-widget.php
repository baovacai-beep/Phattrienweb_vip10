<?php
require_once __DIR__ . '/../../core/Rbac.php';
$appBase = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';
$chatSessId = Rbac::chatSessionId();
$isStaffOrAdmin = Rbac::isStaffOrAdmin();
$chatIdentityText = Rbac::isGuest()
    ? 'Khách ghé thăm'
    : (($isStaffOrAdmin ? 'Chế độ xem thử • ' : 'Tài khoản: ') . ($_SESSION['user']['ho_va_ten'] ?? 'Khách hàng'));
?>
<!-- views/layouts/chat-widget.php - Hộp Thoại Tư Vấn Trực Tuyến Realtime (tgb_tin_nhan_tu_van) -->
<button class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center chat-floating-button"
        type="button" id="triggerChatBtn" data-toggle="collapse" data-target="#customerChatWidget" aria-expanded="false"
        title="Chat tư vấn trực tuyến cùng FashionKids">
    <i class="fa-solid fa-comments fa-lg text-white"></i>
</button>

<div class="collapse chat-widget-box shadow-lg bg-white" id="customerChatWidget">
    <div class="card h-100 border-0 d-flex flex-column">
        <!-- Header Bright Pastel -->
        <div class="card-header chat-widget-header-pastel d-flex justify-content-between align-items-center py-3 border-0">
            <div class="d-flex align-items-center min-w-0">
                <span class="badge badge-pill badge-light mr-2 p-2 shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-headset text-peach"></i>
                </span>
                <div class="min-w-0">
                    <div class="font-weight-bold small d-flex align-items-center flex-wrap">
                        <span>Tư Vấn FashionKids</span>
                        <span class="badge badge-light text-peach ml-2 px-2 py-0 rounded-pill text-nowrap flex-shrink-0">CSKH &amp; AI 24/7</span>
                        <span class="fk-live-pulse-dot ml-2 flex-shrink-0"></span>
                    </div>
                    <div class="small text-light text-truncate"><?= htmlspecialchars($chatIdentityText) ?></div>
                </div>
            </div>
            <div class="d-flex align-items-center flex-shrink-0">
                <?php if ($isStaffOrAdmin): ?>
                    <a href="<?= $appBase ?>/views/pages/staff-support.php"
                       class="btn btn-light btn-sm rounded-pill px-2 py-0 mr-2 font-weight-bold text-peach small text-nowrap shadow-sm"
                       title="Mở Bàn Làm Việc CSKH trả lời khách hàng">
                        <i class="fa-solid fa-headset mr-1"></i>Bàn CSKH
                    </a>
                <?php endif; ?>
                <button type="button" class="close text-white flex-shrink-0" data-toggle="collapse" data-target="#customerChatWidget">
                    <span>&times;</span>
                </button>
            </div>
        </div>

        <!-- Thanh câu hỏi gợi ý nhanh -->
        <div class="px-2 py-2 bg-white border-bottom d-flex flex-wrap fk-btn-toolbar">
            <button type="button" onclick="fkSendQuickClientChat('Bé cao 95cm nặng 14kg mặc size mấy ạ?')"
                    class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                📏 Tư vấn size 14kg
            </button>
            <button type="button" onclick="fkSendQuickClientChat('Vải sợi tre của shop có mát cho bé sơ sinh không?')"
                    class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                🌿 Vải sợi tre
            </button>
            <button type="button" onclick="fkSendQuickClientChat('Làm sao đăng ký thi KOL Nhí 2026 ạ?')"
                    class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate text-nowrap">
                👑 Thi KOL Nhí
            </button>
        </div>

        <!-- Vùng tin nhắn cuộn Realtime -->
        <div class="card-body chat-scroll-area p-3 d-flex flex-column" id="fk_clientChatMessagesArea" data-session-id="<?= htmlspecialchars($chatSessId) ?>">
            <div class="d-flex mb-3">
                <div class="p-3 bg-white text-slate small shadow-sm chat-bubble-received">
                    Xin chào ba mẹ! Em là Chuyên viên tư vấn thời trang sợi tre <strong>FashionKids</strong>. Ba mẹ cần hỗ trợ chọn size cho bé hay kiểm tra đơn hàng cứ nhắn trực tiếp tại đây ạ!
                </div>
            </div>
        </div>

        <!-- Khung nhập liệu tin nhắn -->
        <div class="card-footer bg-white border-top p-2">
            <form class="d-flex align-items-center" id="clientChatForm">
                <input type="text" id="fk_clientChatInput" class="form-control rounded-pill form-control-sm mr-2"
                       placeholder="Nhập câu hỏi tư vấn size, chất vải..." autocomplete="off" required>
                <button type="submit" class="btn btn-pastel-primary btn-sm rounded-circle p-2 shadow-sm">
                    <i class="fa-solid fa-paper-plane text-white"></i>
                </button>
            </form>
        </div>
    </div>
</div>
