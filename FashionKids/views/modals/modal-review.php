<!-- views/modals/modal-review.php -->
<div class="modal fade" id="modalReview" tabindex="-1" role="dialog" aria-labelledby="modalReviewTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 rounded-lg shadow-lg">
      <div class="modal-header bg-pastel-butter border-0">
        <h5 class="modal-title font-weight-bold text-dark" id="modalReviewTitle">
          <i class="fas fa-star text-warning mr-2"></i>Đánh Giá Sản Phẩm
        </h5>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="tgb_formReview" method="POST" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="product_id" id="tgb_review_product_id" value="101">
        <div class="modal-body p-4">
          <div id="tgb_reviewAlert" class="alert d-none small py-2 mb-3"></div>
          <!-- Chọn số sao tương tác -->
          <div class="text-center mb-4">
            <div class="d-flex justify-content-center mb-2 star-picker-group">
              <i class="fas fa-star star-picker-item active mx-1" data-value="1"></i>
              <i class="fas fa-star star-picker-item active mx-1" data-value="2"></i>
              <i class="fas fa-star star-picker-item active mx-1" data-value="3"></i>
              <i class="fas fa-star star-picker-item active mx-1" data-value="4"></i>
              <i class="fas fa-star star-picker-item active mx-1" data-value="5"></i>
            </div>
            <input type="hidden" name="rating_score" id="ratingScoreInput" value="5">
            <span id="starRatingLabel" class="badge badge-pill bg-pastel-butter text-pastel-butter font-weight-bold px-3 py-2">Tuyệt vời</span>
          </div>

          <!-- Nhập cảm nhận chất lượng -->
          <div class="form-group mb-3">
            <label class="font-weight-bold small text-secondary" for="tgb_review_content">Cảm nhận của bạn về sản phẩm</label>
            <textarea class="form-control rounded-lg" id="tgb_review_content" name="review_content" rows="4" placeholder="Chia sẻ về độ mềm của vải, form dáng, đường may và trải nghiệm của bé khi mặc..." required></textarea>
          </div>

          <!-- Tải tệp hình ảnh/video thực tế -->
          <div class="form-group mb-2">
            <label class="font-weight-bold small text-secondary">Ảnh hoặc Video thực tế (Tối đa 3 ảnh hoặc 1 video ngắn)</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="reviewMediaFiles" name="media_files[]" multiple accept="image/*,video/*">
              <label class="custom-file-label rounded-pill" for="reviewMediaFiles">Chọn ảnh chụp bé hoặc video mở hộp...</label>
            </div>
            <small class="form-text text-muted">Định dạng JPG, PNG, MP4. Dung lượng tối đa 25MB.</small>
          </div>
        </div>

        <div class="modal-footer border-0 p-3 bg-light">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Hủy</button>
          <button type="submit" id="tgb_btnSubmitReview" class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm">Gửi Đánh Giá</button>
        </div>
      </form>
    </div>
  </div>
</div>