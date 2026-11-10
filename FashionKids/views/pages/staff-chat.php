<?php
$page_title = "Live Chat CSKH & Tra Cứu Tồn Kho (MH 11)";
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess(__FILE__); // RBAC: Admin + Nhân viên
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
?>

<div class="container-fluid my-4 px-lg-5" id="fk_staff_chat_section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1">Live Chat Chăm Sóc Khách Hàng (MH 11)</h4>
            <small class="text-muted-custom">Tin nhắn cập nhật mỗi 3 giây · tự đồng bộ trạng thái "đã đọc" (<code>tgb_danh_dau_tin_da_doc</code>)</small>
        </div>
        <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-left mr-1"></i> Duyệt đơn hàng
        </a>
    </div>

    <div id="fk_chatAlert" class="d-none"></div>

    <div class="row">
        <!-- Cột 1: Danh sách phiên hội thoại -->
        <div class="col-12 col-md-3 mb-3">
            <div class="card card-pastel shadow-sm h-100">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Khách đang chờ</span>
                    <span class="badge badge-pill badge-sky" id="fk_chatSessionCount">0</span>
                </div>
                <div class="list-group list-group-flush fk-chat-scroll" id="fk_chatSessions">
                    <div class="list-group-item small text-muted-custom">Đang tải danh sách phiên...</div>
                </div>
            </div>
        </div>

        <!-- Cột 2: Khung hội thoại -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card card-pastel shadow-sm h-100 d-flex flex-column">
                <div class="card-header bg-white d-flex align-items-center py-3">
                    <i class="fa-solid fa-comments text-peach mr-2"></i>
                    <h6 class="font-weight-bold mb-0 text-slate" id="fk_chatTitle">Chọn một phiên hội thoại bên trái</h6>
                </div>
                <div class="card-body bg-milk-cream fk-chat-scroll d-flex flex-column" id="fk_chatMessages"></div>
                <div class="card-footer bg-white p-3">
                    <form id="fk_chatForm" class="d-flex" autocomplete="off">
                        <input type="text" id="fk_chatInput" class="form-control rounded-pill mr-2" maxlength="1000"
                               placeholder="Nhập câu trả lời tư vấn..." disabled>
                        <button type="submit" id="fk_chatSend" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold" disabled>
                            <i class="fa-solid fa-paper-plane mr-1"></i> Gửi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột 3: Tra cứu nhanh tồn kho -->
        <div class="col-12 col-md-3 mb-3">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fa-solid fa-magnifying-glass text-peach mr-2"></i>Tra cứu tồn kho nhanh
                </div>
                <div class="card-body p-3">
                    <form id="fk_stockLookupForm" autocomplete="off">
                        <div class="form-group mb-2">
                            <input type="text" id="fk_stockKeyword" class="form-control form-control-sm rounded-pill"
                                   placeholder="Mã SP hoặc tên sản phẩm..." required>
                        </div>
                        <div class="form-group mb-2">
                            <input type="number" id="fk_stockQty" class="form-control form-control-sm rounded-pill"
                                   min="1" placeholder="Số lượng khách cần (tuỳ chọn)">
                        </div>
                        <button type="submit" class="btn btn-pastel-primary btn-sm btn-block rounded-pill font-weight-bold">
                            <i class="fa-solid fa-boxes-stacked mr-1"></i> Kiểm tra
                        </button>
                    </form>
                    <ul class="list-unstyled small mt-3 mb-0" id="fk_stockResults"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
