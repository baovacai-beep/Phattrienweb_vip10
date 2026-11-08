<!-- views/pages/product-detail.php: Khối Đánh giá Giai đoạn 2 -->
<section class="reviews-section bg-pastel-cream py-5">
  <div class="container">
    <!-- Hàng tiêu đề phân tích đánh giá -->
    <div class="row mb-4">
      <div class="col-12">
        <h3 class="font-weight-bold text-dark mb-1">Đánh Giá Từ Khách Hàng</h3>
        <p class="text-muted small">Nhận xét thực tế từ các phụ huynh đã mua sắm tại FashionKids</p>
      </div>
    </div>

    <!-- Hàng thống kê tổng quan và tỷ lệ sao -->
    <div class="row align-items-center mb-4 bg-white p-4 rounded-lg shadow-sm border-0">
      <div class="col-12 col-md-4 text-center border-right">
        <div class="display-4 font-weight-bold text-warning mb-1">4.9</div>
        <div class="mb-2">
          <i class="fas fa-star star-rating-butter"></i>
          <i class="fas fa-star star-rating-butter"></i>
          <i class="fas fa-star star-rating-butter"></i>
          <i class="fas fa-star star-rating-butter"></i>
          <i class="fas fa-star star-rating-butter"></i>
        </div>
        <span class="text-muted small">Dựa trên 128 nhận xét thực tế</span>
        <div class="mt-3">
          <button class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalReview">
            <i class="fas fa-pen mr-2"></i>Viết đánh giá
          </button>
        </div>
      </div>

      <div class="col-12 col-md-8 pl-md-4 mt-3 mt-md-0">
        <div class="d-flex align-items-center mb-2">
          <span class="small font-weight-bold mr-2 text-nowrap">5 <i class="fas fa-star text-warning"></i></span>
          <div class="progress flex-grow-1 rounded-pill">
            <div class="progress-bar bg-warning rounded-pill w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span class="small text-muted ml-2">96</span>
        </div>
        <div class="d-flex align-items-center mb-2">
          <span class="small font-weight-bold mr-2 text-nowrap">4 <i class="fas fa-star text-warning"></i></span>
          <div class="progress flex-grow-1 rounded-pill">
            <div class="progress-bar bg-warning rounded-pill w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span class="small text-muted ml-2">22</span>
        </div>
        <div class="d-flex align-items-center mb-2">
          <span class="small font-weight-bold mr-2 text-nowrap">3 <i class="fas fa-star text-warning"></i></span>
          <div class="progress flex-grow-1 rounded-pill">
            <div class="progress-bar bg-warning rounded-pill w-0" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span class="small text-muted ml-2">6</span>
        </div>
        <div class="d-flex align-items-center mb-2">
          <span class="small font-weight-bold mr-2 text-nowrap">2 <i class="fas fa-star text-warning"></i></span>
          <div class="progress flex-grow-1 rounded-pill">
            <div class="progress-bar bg-warning rounded-pill w-0" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span class="small text-muted ml-2">3</span>
        </div>
        <div class="d-flex align-items-center">
          <span class="small font-weight-bold mr-2 text-nowrap">1 <i class="fas fa-star text-warning"></i></span>
          <div class="progress flex-grow-1 rounded-pill">
            <div class="progress-bar bg-warning rounded-pill w-0" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span class="small text-muted ml-2">1</span>
        </div>
      </div>
    </div>

    <!-- Hàng nút lọc đánh giá nhanh -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex flex-wrap align-items-center">
          <span class="font-weight-bold small mr-3 mb-2">Lọc theo:</span>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 active font-weight-bold">Tất cả (128)</button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold"><i class="fas fa-camera mr-1"></i> Có hình ảnh/video (42)</button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">5 Sao (96)</button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">4 Sao (22)</button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">3 Sao (6)</button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mr-2 mb-2 font-weight-bold">2 Sao (3)</button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill mb-2 font-weight-bold">1 Sao (1)</button>
        </div>
      </div>
    </div>

    <!-- Hàng danh sách đánh giá chi tiết -->
    <div class="row">
      <!-- Đánh giá 1 -->
      <div class="col-12 mb-3">
        <div class="card border-0 rounded-lg shadow-sm p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-pastel-blue text-pastel-blue font-weight-bold d-flex align-items-center justify-content-center mr-3 p-3">
                MB
              </div>
              <div>
                <div class="font-weight-bold text-dark">
                  m***8
                  <span class="badge badge-pill bg-pastel-mint text-pastel-mint ml-2">
                    <i class="fas fa-check-circle mr-1"></i>Đã mua hàng tại FashionKids
                  </span>
                </div>
                <div class="small">
                  <span class="text-warning mr-2">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                  </span>
                  <span class="text-muted">Phân loại: Váy voan công chúa / Size 4T</span>
                </div>
              </div>
            </div>
            <span class="text-muted small">04/11/2026</span>
          </div>

          <p class="text-secondary mb-3">
            Vải voan mềm mịn không gây ngứa cho bé, đường may tỉ mỉ, form dáng xòe phồng chuẩn ảnh mẫu. Bé nhà mình 15kg mặc size 4T vừa vặn thoải mái luôn!
          </p>

          <!-- Dàn ảnh thực tế của bé mặc -->
          <div class="d-flex flex-wrap mb-3">
            <img src="/uploads/reviews/review-sample-1.jpg" alt="Review 1" class="img-thumbnail rounded review-media-thumbnail mr-2 mb-2">
            <img src="/uploads/reviews/review-sample-2.jpg" alt="Review 2" class="img-thumbnail rounded review-media-thumbnail mr-2 mb-2">
          </div>

          <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-outline-light text-muted rounded-pill mr-2 border">
              <i class="far fa-thumbs-up mr-1"></i> Hữu ích (14)
            </button>
            <span class="small text-muted">Đã phản hồi bởi Shop</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>