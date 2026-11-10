<?php
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess('staff-chat.php');
$page_title = "Bàn Làm Việc CSKH Trực Tuyến 3 Cột (MH 11)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
?>
<!-- views/pages/staff-chat.php -->
<section class="staff-chat-workspace py-4 bg-milk-cream" id="tgb_staff_chat_section">
  <div class="container-fluid px-lg-5">
    <!-- Header Bàn Làm Việc Live Chat 3 Cột (MH 11) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
      <div>
        <h4 class="font-weight-bold text-slate mb-1">
          <i class="fa-solid fa-headset text-peach mr-2"></i>Bàn Làm Việc CSKH Trực Tuyến 3 Cột (MH 11)
        </h4>
        <small class="text-muted-custom">
          Đảm nhiệm: Trần Gia Bảo (<code>tgb_</code>) — Tư vấn thời gian thực (<code>tgb_tin_nhan_tu_van</code>), đồng bộ trạng thái đã đọc (<code>tgb_danh_dau_tin_da_doc</code>) &amp; Tra cứu nhanh tồn kho
        </small>
      </div>
      <div class="mt-2 mt-md-0">
        <span class="badge badge-pill badge-mint px-3 py-2 mr-2 font-weight-bold" id="tgb_chatReadStatusBadge">
          <i class="fa-solid fa-check-double mr-1"></i>Đã đồng bộ trạng thái đọc (tgb_danh_dau_tin_da_doc)
        </span>
        <a href="/FashionKids/views/pages/staff-orders.php" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3">
          <i class="fa-solid fa-clipboard-check mr-1"></i> Duyệt Đơn Hàng (MH 10)
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Cột 1: Danh sách các phiên hội thoại khách đang chờ (3 Col) -->
      <div class="col-12 col-md-3 mb-3 mb-md-0">
        <div class="card card-pastel border-0 shadow-sm rounded-lg h-100">
          <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center border-bottom py-3">
            <span class="text-slate"><i class="fa-solid fa-comments text-peach mr-2"></i>Khách đang chờ</span>
            <span class="badge badge-pill badge-peach px-2 py-1" id="tgb_waitingSessionCount">3 phiên</span>
          </div>
          <div class="list-group list-group-flush chat-scroll-staff" id="tgb_chatSessionList">
            <a href="javascript:void(0)"
               onclick="tgbSwitchChatSession('sess_mai_nguyen_01', 'Nguyễn Thị Mai (mai.nguyen@gmail.com)', 'NM', this)"
               data-session="sess_mai_nguyen_01"
               class="list-group-item list-group-item-action active border-0 p-3 tgb-chat-session-item">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold">Nguyễn Thị Mai</span>
                <small class="badge badge-light">13:05</small>
              </div>
              <p class="small mb-1 text-truncate" id="tgb_preview_sess_mai_nguyen_01">Bé gái 3 tuổi 14kg mặc vừa size nào...</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge badge-pill badge-mint">Đã đăng nhập</span>
                <span class="badge badge-pill badge-light border small tgb-read-indicator">Đã đọc</span>
              </div>
            </a>

            <a href="javascript:void(0)"
               onclick="tgbSwitchChatSession('sess_guest_123456', 'Khách vãng lai #108 (sess_guest_123456)', 'KV', this)"
               data-session="sess_guest_123456"
               class="list-group-item list-group-item-action border-0 p-3 tgb-chat-session-item">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold text-slate">Khách vãng lai #108</span>
                <small class="text-muted-custom">12:58</small>
              </div>
              <p class="small text-muted-custom mb-1 text-truncate" id="tgb_preview_sess_guest_123456">Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa...</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge badge-pill badge-secondary">Khách vãng lai</span>
                <span class="badge badge-pill badge-danger small tgb-read-indicator">1 mới</span>
              </div>
            </a>

            <a href="javascript:void(0)"
               onclick="tgbSwitchChatSession('sess_me_be_sam_03', 'Võ Thị Thanh Thảo (thanhthao@gmail.com)', 'VT', this)"
               data-session="sess_me_be_sam_03"
               class="list-group-item list-group-item-action border-0 p-3 tgb-chat-session-item">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold text-slate">Võ Thị Thanh Thảo</span>
                <small class="text-muted-custom">12:40</small>
              </div>
              <p class="small text-muted-custom mb-1 text-truncate" id="tgb_preview_sess_me_be_sam_03">Shop kiểm tra giúp mình đơn #FK-ORD-202608 đã đóng gói chưa...</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge badge-pill badge-gold">VIP Vàng</span>
                <span class="badge badge-pill badge-danger small tgb-read-indicator">2 mới</span>
              </div>
            </a>
          </div>
        </div>
      </div>

      <!-- Cột 2: Khung hội thoại chính thời gian thực (6 Col) -->
      <div class="col-12 col-md-6 mb-3 mb-md-0">
        <div class="card card-pastel border-0 shadow-sm rounded-lg h-100 d-flex flex-column">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
              <div class="rounded-circle badge-mint p-2 mr-3 font-weight-bold d-flex align-items-center justify-content-center avatar-40" id="tgb_activeCustomerAvatar">NM</div>
              <div>
                <h6 class="font-weight-bold text-slate mb-0" id="tgb_activeCustomerTitle">Nguyễn Thị Mai (mai.nguyen@gmail.com)</h6>
                <small class="text-success" id="tgb_activeCustomerSub"><i class="fas fa-circle mr-1"></i>Đang trực tuyến • Đã đọc (da_xem = 1)</small>
              </div>
            </div>
            <div>
              <button type="button" onclick="tgbMarkCurrentSessionRead()" class="btn btn-outline-success btn-sm rounded-pill font-weight-bold mr-1" title="Đồng bộ trạng thái đã đọc (tgb_danh_dau_tin_da_doc)">
                <i class="fa-solid fa-check-double mr-1"></i>Đã đọc
              </button>
              <button type="button" onclick="tgbEndChatSession()" class="btn btn-outline-danger btn-sm rounded-pill font-weight-bold">Kết thúc phiên</button>
            </div>
          </div>

          <div class="card-body chat-scroll-staff bg-white p-3 d-flex flex-column" id="tgb_staffChatMessageContainer">
            <!-- Khách hỏi -->
            <div class="d-flex mb-3">
              <div class="p-3 bg-light text-slate small rounded-lg chat-bubble-received border">
                Bé gái 3 tuổi, nặng 14kg cao 95cm thì nên chọn mẫu váy công chúa size nào vậy shop?
                <div class="text-muted-custom fk-fs-075 mt-1">13:04 • Khách hàng</div>
              </div>
            </div>
            <!-- Nhân viên đáp -->
            <div class="d-flex justify-content-end mb-3">
              <div class="p-3 bg-peach text-white small rounded-lg chat-bubble-sent shadow-sm">
                Dạ chào chị Mai! Với bé 14kg và 95cm, chị nên chọn Size 2 (2-3T) hoặc Size 3 (3-4T) để bé mặc vừa vặn và thoải mái vận động nhất ạ!
                <div class="text-white-50 fk-fs-075 mt-1 text-right">13:05 • Nhân viên CSKH <i class="fa-solid fa-check-double ml-1"></i></div>
              </div>
            </div>
          </div>

          <!-- Cụm nút câu trả lời mẫu nhanh -->
          <div class="px-3 py-2 bg-milk-cream border-top d-flex flex-wrap">
            <button type="button" onclick="tgbInsertQuickReply('Dạ mẫu này chất liệu 100% sợi tre hữu cơ kháng khuẩn, hiện tại shop đang còn sẵn hàng đủ size cho bé ạ!')" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small text-slate">
              + Tư vấn vải sợi tre
            </button>
            <button type="button" onclick="tgbInsertQuickReply('Dạ với chiều cao 95cm nặng 14kg, bé mặc Size 2 (2-3T) là chuẩn phom nhất ạ!')" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small text-slate">
              + Báo Size 2 (2-3T)
            </button>
            <button type="button" onclick="tgbSimulateIncomingCustomerMsg()" class="btn btn-light btn-sm border rounded-pill mb-1 py-0 px-2 small text-peach font-weight-bold">
              <i class="fa-solid fa-bolt mr-1"></i>Mô phỏng tin nhắn khách tới
            </button>
          </div>

          <div class="card-footer bg-white border-top p-3">
            <form class="d-flex" id="tgb_formStaffChatReply">
              <input type="hidden" id="tgb_activeSessionId" value="sess_mai_nguyen_01">
              <input type="text" id="tgb_staffChatInput" class="form-control rounded-pill mr-2" placeholder="Nhập câu trả lời tư vấn cho phụ huynh..." autocomplete="off" required>
              <button type="submit" id="tgb_btnSendStaffChat" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold">
                <i class="fa-solid fa-paper-plane mr-1"></i>Gửi
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Cột 3: Tra cứu nhanh tồn kho & Bảng kích cỡ (3 Col) -->
      <div class="col-12 col-md-3">
        <div class="card card-pastel border-0 shadow-sm rounded-lg mb-3">
          <div class="card-header bg-white font-weight-bold border-bottom py-3 text-slate">
            <i class="fas fa-search mr-2 text-peach"></i>Tra cứu tồn kho nhanh
          </div>
          <div class="card-body p-3">
            <form id="tgb_formQuickStockLookup" class="mb-3">
              <div class="input-group input-group-sm">
                <input type="text" id="tgb_quickStockKeyword" class="form-control rounded-pill mr-1" placeholder="Nhập mã SP / tên đồ (VD: Váy, Sandal)...">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-pastel-sky rounded-pill px-3 py-1 font-weight-bold">
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </button>
                </div>
              </div>
            </form>
            <ul class="list-unstyled small mb-0" id="tgb_quickStockResultList">
              <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div>
                  <strong class="text-slate d-block">Váy Công Chúa Sợi Tre (#101)</strong>
                  <span class="text-peach font-weight-bold">285.000 đ</span>
                </div>
                <span class="badge badge-mint px-2 py-1 rounded-pill">Còn 18</span>
              </li>
              <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div>
                  <strong class="text-slate d-block">Áo Khoác Cardigan UV (#104)</strong>
                  <span class="text-peach font-weight-bold">320.000 đ</span>
                </div>
                <span class="badge badge-danger px-2 py-1 rounded-pill">Sắp hết (5)</span>
              </li>
              <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div>
                  <strong class="text-slate d-block">Bộ Thun Khủng Long (#102)</strong>
                  <span class="text-peach font-weight-bold">225.000 đ</span>
                </div>
                <span class="badge badge-mint px-2 py-1 rounded-pill">Còn 24</span>
              </li>
              <li class="d-flex justify-content-between align-items-center">
                <div>
                  <strong class="text-slate d-block">Sandal Tập Đi Êm Chân (#103)</strong>
                  <span class="text-peach font-weight-bold">210.000 đ</span>
                </div>
                <span class="badge badge-mint px-2 py-1 rounded-pill">Còn 14</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="card card-pastel border-0 shadow-sm rounded-lg">
          <div class="card-header bg-white font-weight-bold border-bottom py-3 text-slate">
            <i class="fas fa-ruler-combined mr-2 text-info"></i>Quy chuẩn kích cỡ nhanh
          </div>
          <div class="card-body p-2">
            <div class="table-responsive">
              <table class="table table-sm table-bordered text-center small mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Size</th>
                    <th>Cân nặng</th>
                    <th>Chiều cao</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td>Size 1 (1-2T)</td><td>9-12kg</td><td>80-90cm</td></tr>
                  <tr><td>Size 2 (2-3T)</td><td>12-14kg</td><td>90-100cm</td></tr>
                  <tr><td>Size 3 (3-4T)</td><td>14-17kg</td><td>100-110cm</td></tr>
                  <tr><td>Size 4 (4-5T)</td><td>17-20kg</td><td>110-120cm</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
