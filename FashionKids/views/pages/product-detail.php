<!-- ==========================================
     MH 03 (KHỐI DƯỚI): ĐÁNH GIÁ SẢN PHẨM (TGB_)
     ========================================== -->
<div class="container my-5" id="productReviewsSection">
    <div class="card border-0 shadow-sm rounded-lg p-4 bg-white">
        <h4 class="font-weight-bold text-slate mb-4">
            <i class="fas fa-star-half-alt text-warning mr-2"></i>Đánh giá từ các phụ huynh
        </h4>

        <!-- Khối thống kê tổng quan -->
        <div class="row align-items-center mb-4 pb-3 border-bottom">
            <div class="col-12 col-md-4 text-center border-right-md mb-3 mb-md-0">
                <div class="display-4 font-weight-bold text-peach">4.9</div>
                <div class="text-warning mb-1">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <small class="text-muted">Dựa trên 128 đánh giá có xác thực đơn mua</small>
            </div>
            <div class="col-12 col-md-5">
                <div class="d-flex align-items-center mb-1">
                    <small class="mr-2 text-muted" style="width: 45px;">5 sao</small>
                    <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 88%;"></div>
                    </div>
                    <small class="ml-2 text-muted">112</small>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <small class="mr-2 text-muted" style="width: 45px;">4 sao</small>
                    <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 10%;"></div>
                    </div>
                    <small class="ml-2 text-muted">12</small>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <small class="mr-2 text-muted" style="width: 45px;">3 sao</small>
                    <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 2%;"></div>
                    </div>
                    <small class="ml-2 text-muted">4</small>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <small class="mr-2 text-muted" style="width: 45px;">1-2 sao</small>
                    <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 0%;"></div>
                    </div>
                    <small class="ml-2 text-muted">0</small>
                </div>
            </div>
            <div class="col-12 col-md-3 text-center mt-3 mt-md-0">
                <button type="button" class="btn btn-outline-peach rounded-pill btn-block py-2" data-toggle="modal" data-target="#modalWriteReview">
                    <i class="fas fa-edit mr-2"></i>Viết nhận xét
                </button>
                <small class="d-block text-muted mt-2">Dành riêng cho khách đã nhận hàng</small>
            </div>
        </div>

        <!-- Bộ lọc Review -->
        <div class="d-flex flex-wrap align-items-center mb-4">
            <span class="font-weight-bold text-muted mr-3 mb-2">Lọc theo:</span>
            <div class="btn-group btn-group-toggle flex-wrap mb-2" data-toggle="buttons">
                <label class="btn btn-sm btn-outline-secondary active rounded-pill mr-2 mb-1">
                    <input type="radio" name="filterRating" checked> Tất cả
                </label>
                <label class="btn btn-sm btn-outline-secondary rounded-pill mr-2 mb-1">
                    <input type="radio" name="filterRating"> Có ảnh/video (46)
                </label>
                <label class="btn btn-sm btn-outline-secondary rounded-pill mr-2 mb-1">
                    <input type="radio" name="filterRating"> 5 Sao (112)
                </label>
                <label class="btn btn-sm btn-outline-secondary rounded-pill mr-2 mb-1">
                    <input type="radio" name="filterRating"> 4 Sao (12)
                </label>
            </div>
        </div>

        <!-- Danh sách nhận xét -->
        <div class="review-item border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="font-weight-bold text-slate">Mẹ Bé Sam (v***o)</span>
                    <span class="badge-verified-buyer ml-2"><i class="fas fa-check-circle mr-1"></i>Đã mua tại FashionKids</span>
                </div>
                <small class="text-muted">26/10/2026</small>
            </div>
            <div class="text-warning mb-2">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                <small class="text-muted ml-2 font-weight-normal">Phân loại: Size 2 (2-3T) - Hồng phấn</small>
            </div>
            <p class="text-slate mb-2">
                Váy công chúa chất vải sợi tre siêu mềm mát, đường may viền cổ lót kỹ nên bé mặc không hề bị dặm ngứa. Mình tra size theo thước dây 95cm mặc vừa in. Sẽ ủng hộ shop tiếp!
            </p>
            <!-- Ảnh thực tế -->
            <div class="d-flex flex-wrap mb-2">
                <img src="/uploads/reviews/review_sam_1.jpg" class="review-media-thumbnail mr-2 mb-2 border" alt="Review 1">
                <img src="/uploads/reviews/review_sam_2.jpg" class="review-media-thumbnail mr-2 mb-2 border" alt="Review 2">
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-light text-muted rounded-pill px-3 mr-3" onclick="handleLikeReview(1, this)">
                    <i class="far fa-thumbs-up mr-1"></i>Hữu ích (<span class="like-count">18</span>)
                </button>
                <small class="text-muted"><i class="fas fa-shield-alt mr-1"></i>Đánh giá đã kiểm duyệt</small>
            </div>
        </div>
    </div>
</div>