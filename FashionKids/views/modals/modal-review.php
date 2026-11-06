<!-- ==========================================
     MH 19: MODAL ĐÁNH GIÁ SẢN PHẨM (TGB_)
     ========================================== -->
<div class="modal fade" id="modalWriteReview" tabindex="-1" role="dialog" aria-labelledby="modalReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow rounded-lg">
            <div class="modal-header bg-milk-cream border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold text-slate" id="modalReviewLabel">
                    <i class="fas fa-star text-warning mr-2"></i>Đánh giá chất lượng trang phục
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSubmitReview" action="/controllers/ReviewController.php?action=create" method="POST" enctype="multipart/form-data">
                <div class="modal-body pt-2">
                    <input type="hidden" name="ma_san_pham" value="1">
                    <input type="hidden" name="ma_don_hang" value="1">

                    <!-- Chọn số sao tương tác -->
                    <div class="text-center my-3">
                        <div class="star-rating-box mb-2">
                            <i class="fas fa-star rating-star-interactive active" data-rating="1"></i>
                            <i class="fas fa-star rating-star-interactive active" data-rating="2"></i>
                            <i class="fas fa-star rating-star-interactive active" data-rating="3"></i>
                            <i class="fas fa-star rating-star-interactive active" data-rating="4"></i>
                            <i class="fas fa-star rating-star-interactive active" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="so_sao_danh_gia" id="ratingScoreInput" value="5">
                        <span class="badge badge-light px-3 py-1 text-slate font-weight-bold" id="ratingLabel">Rất hài lòng (5 sao)</span>
                    </div>

                    <!-- Nhận xét chi tiết -->
                    <div class="form-group">
                        <label class="font-weight-bold text-slate small">Cảm nhận thực tế của phụ huynh:</label>
                        <textarea class="form-control rounded-lg" name="noi_dung_danh_gia" rows="4" placeholder="Chất vải có mềm mát không? Kích cỡ bé mặc có thoải mái vận động không?" required></textarea>
                    </div>

                    <!-- Upload ảnh / video -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-slate small">Đính kèm ảnh/video bé diện đồ (Tối đa 3 tệp, < 10MB):</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="reviewMediaFiles" name="tep_dinh_kem[]" multiple accept="image/*,video/mp4">
                            <label class="custom-file-label text-truncate" for="reviewMediaFiles">Chọn ảnh chụp bé hoặc video mở hộp...</label>
                        </div>
                        <small class="form-text text-muted">Đánh giá có ảnh thực tế sẽ giúp cộng đồng phụ huynh chọn size chuẩn xác hơn.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-peach rounded-pill px-4 font-weight-bold text-white">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>