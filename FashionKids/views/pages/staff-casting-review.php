<!-- views/pages/staff-casting-review.php -->
<section class="casting-review-section py-4 bg-light">
  <div class="container-fluid">
    <!-- Tiêu đề & Bộ lọc độ tuổi -->
    <div class="row mb-4">
      <div class="col-12 col-md-6">
        <h3 class="font-weight-bold text-dark mb-1">Duyệt Hồ Sơ KOL Nhí / Casting</h3>
        <p class="text-muted small">Kiểm duyệt thông tin, chiều cao, cân nặng và hình ảnh đăng ký người mẫu nhí</p>
      </div>
      <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center">
        <span class="font-weight-bold small mr-2">Nhóm tuổi:</span>
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-outline-primary btn-sm active font-weight-bold">Tất cả</button>
          <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold">1 - 2 Tuổi</button>
          <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold">3 - 4 Tuổi</button>
          <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold">5 - 6 Tuổi</button>
        </div>
      </div>
    </div>

    <!-- Lưới hồ sơ đăng ký -->
    <div class="row">
      <!-- Thẻ hồ sơ bé 1 -->
      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
          <img src="/uploads/casting/model-kid-1.jpg" class="card-img-top casting-card-img" alt="Bé Cherry">
          <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="font-weight-bold text-dark mb-0">Bé Cherry (Bảo Hân)</h5>
              <span class="badge badge-pill bg-pastel-butter text-pastel-butter">3 Tuổi</span>
            </div>
            <div class="text-muted small mb-3">
              <div><i class="fas fa-ruler-vertical mr-2"></i>Chiều cao: 96 cm</div>
              <div><i class="fas fa-weight mr-2"></i>Cân nặng: 14.5 kg</div>
              <div><i class="fas fa-phone mr-2"></i>Phụ huynh: Chị Lan (0912.xxx.789)</div>
            </div>
            <p class="small text-secondary flex-grow-1">
              "Bé rất dạn dĩ trước ống kính, từng có kinh nghiệm chụp mẫu đồ Tết và biểu cảm tự nhiên."
            </p>
            <!-- 3 Nút xử lý nghiệp vụ -->
            <div class="pt-3 border-top">
              <div class="row no-gutters">
                <div class="col-4 pr-1">
                  <button class="btn btn-success btn-sm btn-block rounded-pill font-weight-bold" title="Duyệt vào vòng Phỏng vấn">
                    <i class="fas fa-check mr-1"></i>Duyệt
                  </button>
                </div>
                <div class="col-4 px-1">
                  <button class="btn btn-info btn-sm btn-block rounded-pill font-weight-bold" title="Mời ghi hình Studio">
                    <i class="fas fa-camera mr-1"></i>Studio
                  </button>
                </div>
                <div class="col-4 pl-1">
                  <button class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold" title="Từ chối hồ sơ">
                    <i class="fas fa-times mr-1"></i>Loại
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Thẻ hồ sơ bé 2 -->
      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
          <img src="/uploads/casting/model-kid-2.jpg" class="card-img-top casting-card-img" alt="Bé Ken">
          <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="font-weight-bold text-dark mb-0">Bé Ken (Gia Khiêm)</h5>
              <span class="badge badge-pill bg-pastel-mint text-pastel-mint">5 Tuổi</span>
            </div>
            <div class="text-muted small mb-3">
              <div><i class="fas fa-ruler-vertical mr-2"></i>Chiều cao: 110 cm</div>
              <div><i class="fas fa-weight mr-2"></i>Cân nặng: 18.0 kg</div>
              <div><i class="fas fa-phone mr-2"></i>Phụ huynh: Anh Hùng (0988.xxx.123)</div>
            </div>
            <p class="small text-secondary flex-grow-1">
              "Bé năng động, yêu thích trang phục thể thao đường phố, tự tin tạo dáng theo nhạc."
            </p>
            <div class="pt-3 border-top">
              <div class="row no-gutters">
                <div class="col-4 pr-1">
                  <button class="btn btn-success btn-sm btn-block rounded-pill font-weight-bold">
                    <i class="fas fa-check mr-1"></i>Duyệt
                  </button>
                </div>
                <div class="col-4 px-1">
                  <button class="btn btn-info btn-sm btn-block rounded-pill font-weight-bold">
                    <i class="fas fa-camera mr-1"></i>Studio
                  </button>
                </div>
                <div class="col-4 pl-1">
                  <button class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold">
                    <i class="fas fa-times mr-1"></i>Loại
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>