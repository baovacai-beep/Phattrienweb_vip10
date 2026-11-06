<!-- ==========================================
     MH 11: BÀN LÀM VIỆC CHAT CSKH CHO STAFF (TGB_)
     ========================================== -->
<div class="container-fluid py-4">
    <div class="tgb-staff-chat-window border">
        <div class="row no-gutters h-100">
            <!-- Cột 1: Danh sách hội thoại khách hàng (col-12 col-md-3) -->
            <div class="col-12 col-md-3 border-right h-100 d-flex flex-column bg-white">
                <div class="p-3 border-bottom bg-milk-cream">
                    <h6 class="font-weight-bold mb-2 text-slate"><i class="fas fa-comments mr-2"></i>Hội thoại chờ duyệt</h6>
                    <input type="text" class="form-control form-control-sm rounded-pill" placeholder="Tìm theo tên/SĐT khách...">
                </div>
                <div class="list-group list-group-flush flex-grow-1 overflow-auto">
                    <a href="#" class="list-group-item list-group-item-action active-chat-item p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-slate">Khách Vãng Lai #1289</strong>
                            <small class="text-danger font-weight-bold">Mới</small>
                        </div>
                        <small class="text-muted d-block text-truncate">Bé cao 95cm mặc váy Elsa size 2 vừa không shop?</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-slate">Mẹ Bé Sam</strong>
                            <small class="text-muted">10:45</small>
                        </div>
                        <small class="text-muted d-block text-truncate">Đơn hàng của mình đã gửi shipper chưa ạ?</small>
                    </a>
                </div>
            </div>

            <!-- Cột 2: Khung Chat Trực tiếp (col-12 col-md-6) -->
            <div class="col-12 col-md-6 border-right h-100 d-flex flex-column bg-milk-cream">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 font-weight-bold text-slate">Khách Vãng Lai #1289</h6>
                        <small class="text-muted">Đang xem: Váy Công Chúa Elsa Bạch Tuyết</small>
                    </div>
                    <span class="badge badge-success px-3 py-2 rounded-pill">Đang trực tuyến</span>
                </div>
                <div class="flex-grow-1 p-3 overflow-auto" id="staffChatHistory">
                    <div class="chat-bubble chat-bubble-customer">
                        Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa size mấy thì vừa ạ?
                    </div>
                    <div class="chat-bubble chat-bubble-staff">
                        Dạ chào bạn, với chiều cao 92cm bé mặc Size 2 (2-3T) là vừa vặn và thoải mái nhất ạ!
                    </div>
                </div>
                <div class="p-3 bg-white border-top">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-pill mr-2" placeholder="Nhập câu trả lời tư vấn cho khách...">
                        <div class="input-group-append">
                            <button class="btn btn-peach rounded-pill px-4 text-white font-weight-bold" type="button">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột 3: Widget Tra Cứu Nhanh Tồn Kho & Size (col-12 col-md-3) -->
            <div class="col-12 col-md-3 h-100 bg-white p-3 overflow-auto">
                <h6 class="font-weight-bold text-slate border-bottom pb-2 mb-3">
                    <i class="fas fa-search mr-2"></i>Tra cứu nhanh sản phẩm
                </h6>
                <div class="card border-0 bg-milk-cream p-2 mb-3 rounded-lg">
                    <div class="d-flex align-items-center mb-2">
                        <img src="/uploads/products/vay_elsa.jpg" class="rounded mr-2" width="50" height="50" alt="Váy">
                        <div>
                            <small class="font-weight-bold d-block text-truncate" style="max-width: 150px;">Váy Công Chúa Elsa</small>
                            <span class="text-peach font-weight-bold">450.000₫</span>
                        </div>
                    </div>
                    <small class="text-muted">Tồn kho khả dụng: <strong class="text-success">25 chiếc</strong></small>
                    <small class="text-muted">Gợi ý size: <strong>90-100cm $\rightarrow$ Size 2</strong></small>
                </div>
            </div>
        </div>
    </div>
</div>