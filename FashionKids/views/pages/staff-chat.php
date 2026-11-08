<!-- views/pages/staff-chat.php -->
<section class="staff-chat-workspace py-4 bg-light">
  <div class="container-fluid">
    <div class="row">
      <!-- Cột 1: Danh sách các phiên hội thoại khách đang chờ (3 Col) -->
      <div class="col-12 col-md-3 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm rounded-lg h-100">
          <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center border-bottom">
            <span>Khách đang chờ</span>
            <span class="badge badge-pill badge-primary">4 khách</span>
          </div>
          <div class="list-group list-group-flush chat-scroll-staff">
            <a href="#" class="list-group-item list-group-item-action active border-0 p-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold">Nguyễn Thị Mai</span>
                <small class="badge badge-light">13:05</small>
              </div>
              <p class="small mb-1 text-truncate">Bé gái 3 tuổi 14kg mặc vừa size nào...</p>
              <span class="badge badge-pill bg-pastel-mint text-pastel-mint">Đã đăng nhập</span>
            </a>
            <a href="#" class="list-group-item list-group-item-action border-0 p-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold">Khách vãng lai #108</span>
                <small class="text-muted">12:58</small>
              </div>
              <p class="small text-muted mb-1 text-truncate">Shop có ship hỏa tốc nội thành không?</p>
              <span class="badge badge-pill badge-secondary">Khách vãng lai</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Cột 2: Khung hội thoại chính (6 Col) -->
      <div class="col-12 col-md-6 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm rounded-lg h-100 d-flex flex-column">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-pastel-mint text-pastel-mint p-2 mr-3 font-weight-bold">NM</div>
              <div>
                <h6 class="font-weight-bold mb-0">Nguyễn Thị Mai (mai.nguyen@gmail.com)</h6>
                <small class="text-success"><i class="fas fa-circle mr-1"></i>Đang trực tuyến</small>
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm rounded-pill font-weight-bold">Kết thúc phiên</button>
          </div>

          <div class="card-body chat-scroll-staff bg-white p-3 d-flex flex-column">
            <!-- Khách hỏi -->
            <div class="d-flex mb-3">
              <div class="p-3 bg-light text-dark small rounded-lg chat-bubble-received">
                Bé gái 3 tuổi, nặng 14kg cao 95cm thì nên chọn mẫu váy công chúa size nào vậy shop?
              </div>
            </div>
            <!-- Nhân viên đáp -->
            <div class="d-flex justify-content-end mb-3">
              <div class="p-3 bg-primary text-white small rounded-lg chat-bubble-sent">
                Dạ chào chị Mai! Với bé 14kg và 95cm, chị nên chọn size 4T (khoảng 14-16kg) để bé mặc vừa vặn và thoải mái vận động nhất ạ!
              </div>
            </div>
          </div>

          <div class="card-footer bg-white border-top p-3">
            <form class="d-flex">
              <input type="text" class="form-control rounded-pill mr-2" placeholder="Nhập câu trả lời tư vấn...">
              <button class="btn btn-primary rounded-pill px-4 font-weight-bold">Gửi</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Cột 3: Tra cứu nhanh tồn kho & Bảng kích cỡ (3 Col) -->
      <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm rounded-lg mb-3">
          <div class="card-header bg-white font-weight-bold border-bottom">
            <i class="fas fa-search mr-2 text-primary"></i>Tra cứu tồn kho nhanh
          </div>
          <div class="card-body p-3">
            <input type="text" class="form-control form-control-sm rounded-pill mb-3" placeholder="Nhập mã SP / tên đồ...">
            <ul class="list-unstyled small mb-0">
              <li class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                <span>Váy voan công chúa (Size 3T)</span>
                <span class="badge badge-success">Còn 12</span>
              </li>
              <li class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                <span>Váy voan công chúa (Size 4T)</span>
                <span class="badge badge-success">Còn 5</span>
              </li>
              <li class="d-flex justify-content-between">
                <span>Váy voan công chúa (Size 5T)</span>
                <span class="badge badge-danger">Hết hàng</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-lg">
          <div class="card-header bg-white font-weight-bold border-bottom">
            <i class="fas fa-ruler-combined mr-2 text-info"></i>Quy chuẩn kích cỡ
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
                  <tr><td>2T</td><td>11-13kg</td><td>85-90cm</td></tr>
                  <tr><td>3T</td><td>13-15kg</td><td>90-98cm</td></tr>
                  <tr><td>4T</td><td>15-17kg</td><td>98-105cm</td></tr>
                  <tr><td>5T</td><td>17-20kg</td><td>105-115cm</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>