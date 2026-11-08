<!-- ==========================================
     MH 08: LIVE CHAT WIDGET NỔI GÓC PHẢI (TGB_)
     ========================================== -->
<!-- Nút tròn bật Chat -->
<button class="tgb-chat-floating-btn" id="btnToggleChat" title="Tư vấn size & chất vải">
    <i class="fas fa-comment-dots"></i>
</button>

<!-- Khung hộp thoại Chat -->
<div class="tgb-chat-box-popup" id="boxChatPopup">
    <!-- Header hộp chat -->
    <div class="p-3 bg-peach text-white d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="position-relative mr-2">
                <i class="fas fa-headset fa-2x"></i>
                <span class="badge badge-success rounded-circle position-absolute fk-chat-dot"> </span>
            </div>
            <div>
                <h6 class="mb-0 font-weight-bold">Tư vấn FashionKids</h6>
                <small class="text-white-50">Thường phản hồi trong 2 phút</small>
            </div>
        </div>
        <button type="button" class="close text-white" id="btnCloseChat" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- Vùng tin nhắn -->
    <div class="tgb-chat-messages-container" id="chatMessagesArea">
        <div class="text-center text-muted my-2"><small>Hôm nay</small></div>
        <div class="chat-bubble chat-bubble-staff">
            Dạ FashionKids chào ba mẹ ạ! Ba mẹ cần tư vấn chọn kích cỡ hay xem mẫu đồ cho bé trai hay bé gái ạ?
        </div>
    </div>

    <!-- Ô gõ tin nhắn -->
    <div class="p-2 bg-white border-top">
        <form id="formSendChat" class="d-flex align-items-center">
            <input type="text" class="form-control border-0 mr-2" id="inputChatMessage" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" required>
            <button type="submit" class="btn btn-peach rounded-circle px-3 py-2 text-white">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
