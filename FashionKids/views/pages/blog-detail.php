<!-- views/pages/blog-detail.php -->
<section class="blog-detail-section py-5 bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-9">
        <!-- Banner và Tiêu đề -->
        <span class="badge badge-pill bg-pastel-mint text-pastel-mint font-weight-bold mb-2 px-3 py-2">Mẹo phối đồ</span>
        <h1 class="font-weight-bold text-dark mb-3">5 Cách phối đồ dạo phố cực sành điệu cho bé gái</h1>
        
        <div class="d-flex align-items-center text-muted small mb-4 pb-3 border-bottom">
          <span class="mr-3"><i class="far fa-user mr-1"></i> Ban biên tập FashionKids</span>
          <span class="mr-3"><i class="far fa-calendar mr-1"></i> 05/11/2026</span>
          <span class="mr-3"><i class="far fa-eye mr-1"></i> 1,420 lượt xem</span>
          <span><i class="far fa-heart mr-1 text-danger"></i> 256 yêu thích</span>
        </div>

        <img src="/uploads/banners/blog-cover-1.jpg" class="img-fluid rounded-lg blog-header-banner w-100 mb-4" alt="Chi tiết bài viết">

        <!-- Nội dung Rich-Text -->
        <div class="blog-article-content text-secondary mb-5">
          <p class="lead text-dark font-weight-normal">
            Lựa chọn trang phục cho các bé dạo phố không chỉ cần tính thời trang mà yếu tố mềm mại, thấm hút và độ co giãn của chất liệu luôn là tiêu chí ưu tiên hàng đầu.
          </p>
          <h4 class="font-weight-bold text-dark mt-4">1. Ưu tiên tông màu Pastel mát mắt</h4>
          <p>
            Các gam màu hồng phấn, vàng bơ hay xanh mint mang lại sự ngọt ngào, tinh nghịch mà vẫn bảo vệ thị giác dưới ánh nắng ngoài trời. Khi phối chân váy xòe với áo thun cotton, mẹ nên kết hợp thêm nón cói và giày sandal êm chân.
          </p>
          <h4 class="font-weight-bold text-dark mt-4">2. Thiết kế thoáng khí với form dáng xòe</h4>
          <p>
            Các mẫu váy đầm xòe voan phối cotton cho phép bé tự do chạy nhảy khám phá thế giới xung quanh mà không lo bết dính mồ hôi.
          </p>
        </div>

        <!-- Nút tương tác thả tim -->
        <div class="d-flex justify-content-center my-4 py-3 border-top border-bottom">
          <button class="btn btn-outline-danger rounded-pill px-4 py-2 font-weight-bold shadow-sm">
            <i class="fas fa-heart mr-2"></i> Thả tim bài viết (256)
          </button>
        </div>

        <!-- Khu vực bình luận đa tầng -->
        <div class="comments-area mt-5">
          <h4 class="font-weight-bold text-dark mb-4">Bình luận & Thảo luận (3)</h4>

          <!-- Form gửi bình luận -->
          <form class="mb-4">
            <div class="form-group">
              <textarea class="form-control rounded-lg" rows="3" placeholder="Chia sẻ ý kiến hoặc thắc mắc của bạn về bài viết..."></textarea>
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm">Gửi bình luận</button>
            </div>
          </form>

          <!-- Danh sách bình luận -->
          <!-- Tầng 1: Bình luận cha -->
          <div class="card border-0 bg-light rounded-lg p-3 mb-3">
            <div class="d-flex justify-content-between">
              <h6 class="font-weight-bold text-dark mb-1">Mẹ Bắp Cải</h6>
              <small class="text-muted">1 giờ trước</small>
            </div>
            <p class="text-secondary small mb-2">Bài viết rất hữu ích ạ! Mẫu váy trong ảnh số 2 hiện shop còn hàng cho bé 16kg không?</p>
            <div>
              <button class="btn btn-link btn-sm p-0 text-primary font-weight-bold">Trả lời</button>
            </div>

            <!-- Tầng 2: Phản hồi lồng nhau (Reply) -->
            <div class="card border-0 bg-white rounded-lg p-3 mt-3 ml-4">
              <div class="d-flex justify-content-between">
                <h6 class="font-weight-bold text-dark mb-1">
                  FashionKids Support
                  <span class="badge badge-pill badge-primary ml-1">Admin</span>
                </h6>
                <small class="text-muted">30 phút trước</small>
              </div>
              <p class="text-secondary small mb-0">Dạ chào mẹ, mẫu váy voan công chúa vàng bơ size 4T (15-17kg) hiện còn hàng tại chi nhánh ạ!</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
