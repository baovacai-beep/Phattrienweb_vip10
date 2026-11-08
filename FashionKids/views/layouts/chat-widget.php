<!-- views/layouts/chat-widget.php -->
<!-- Nút tròn kích hoạt chat nổi -->
<button class="btn btn-info rounded-circle shadow-lg d-flex align-items-center justify-content-center chat-floating-button" type="button" id="triggerChatBtn" data-toggle="collapse" data-target="#customerChatWidget" aria-expanded="false">
  <i class="fas fa-comments fa-lg text-white"></i>
</button>

<!-- Khung hộp thoại 360x480px -->
<div class="collapse chat-widget-box shadow-lg bg-white" id="customerChatWidget">
  <div class="card h-100 border-0 rounded-lg overflow-hidden d-flex flex-column">
    <!-- Header -->
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center py-3">
      <div class="d-flex align-items-center">
        <span class="badge badge-pill badge-light mr-2 p-1"><i class="fas fa-robot text-info"></i></span>
        <div>
          <div class="font-weight-bold small">Hỗ Trợ Khách Hàng</div>
          <div class="small text-light">FashionKids CSKH 24/7</div>
        </div>
      </div>
      <button type="button" class="close text-white" data-toggle="collapse" data-target="#customerChatWidget">
        <span>&times;</span>
      </button>
    </div>

    <!-- Vùng tin nhắn cuộn -->
    <div class="card-body chat-scroll-area p-3 bg-light d-flex flex-column">
      <!-- Tin nhắn phản hồi của Bot -->
      <div class="d-flex mb-3">
        <div class="p-3 bg-white text-dark small shadow-sm chat-bubble-received">
          Xin chào quý khách! Em là trợ lý thời trang FashionKids. Em có thể hỗ trợ quý khách chọn size cho bé hay giải đáp câu hỏi nào ạ?
        </div>
      </div>
      <!-- Tin nhắn người dùng gửi -->
      <div class="d-flex justify-content-end mb-3">
        <div class="p-3 bg-info text-white small shadow-sm chat-bubble-sent">
          Bé gái 3 tuổi, nặng 14kg cao 95cm thì nên chọn mẫu váy công chúa size nào vậy shop?
        </div>
      </div>
    </div>

    <!-- Khung nhập liệu tin nhắn -->
    <div class="card-footer bg-white border-top p-2">
      <form class="d-flex align-items-center" id="clientChatForm">
        <input type="text" class="form-control rounded-pill form-control-sm mr-2" placeholder="Nhập câu hỏi của bạn...">
        <button type="submit" class="btn btn-info btn-sm rounded-circle p-2">
          <i class="fas fa-paper-plane text-white"></i>
        </button>
      </form>
    </div>
  </div>
</div>