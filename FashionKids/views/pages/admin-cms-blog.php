<!-- views/pages/admin-cms-blog.php -->
<section class="admin-cms-section py-4 bg-light">
  <div class="container-fluid">
    <div class="row mb-4">
      <div class="col-12">
        <h3 class="font-weight-bold text-dark mb-1">Quản Trị CMS & Kiểm Duyệt Nội Dung</h3>
        <p class="text-muted small">Soạn thảo bài viết Tạp chí mới và kiểm duyệt bình luận/đánh giá từ cộng đồng</p>
      </div>
    </div>

    <!-- Hàng chuyển Tab chức năng -->
    <div class="row mb-4">
      <div class="col-12">
        <ul class="nav nav-pills" id="cmsTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active font-weight-bold rounded-pill px-4" id="write-tab" data-toggle="pill" href="#tabWriteArticle" role="tab">
              <i class="fas fa-feather-alt mr-2"></i>Soạn Thảo Bài Viết Mới
            </a>
          </li>
          <li class="nav-item ml-2">
            <a class="nav-link font-weight-bold rounded-pill px-4" id="moderation-tab" data-toggle="pill" href="#tabModeration" role="tab">
              <i class="fas fa-shield-alt mr-2"></i>Kiểm Duyệt Bình Luận & Đánh Giá
              <span class="badge badge-danger ml-2">3 Vi phạm</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="tab-content" id="cmsTabContent">
      <!-- Tab 1: Soạn thảo bài viết mới -->
      <div class="tab-pane fade show active" id="tabWriteArticle" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-lg p-4 bg-white">
          <form action="/controllers/BlogController.php?action=create" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-12 col-md-8">
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-secondary">Tiêu đề bài viết</label>
                  <input type="text" class="form-control rounded-lg" name="post_title" placeholder="Nhập tiêu đề hấp dẫn...">
                </div>
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-secondary">Tóm tắt ngắn (Excerpt)</label>
                  <textarea class="form-control rounded-lg" name="post_excerpt" rows="2" placeholder="Hiển thị ở lưới thẻ bài viết ngoài trang chủ..."></textarea>
                </div>
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-secondary">Nội dung chi tiết (Rich-Text)</label>
                  <textarea class="form-control rounded-lg" name="post_content" rows="10" placeholder="Viết nội dung bài viết tại đây..."></textarea>
                </div>
              </div>

              <div class="col-12 col-md-4">
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-secondary">Chuyên mục</label>
                  <select class="form-control rounded-pill" name="post_category">
                    <option value="tips">Mẹo phối đồ</option>
                    <option value="backstage">Hậu trường mẫu nhí</option>
                    <option value="fabric">Chất liệu an toàn</option>
                  </select>
                </div>
                <div class="form-group mb-4">
                  <label class="font-weight-bold small text-secondary">Ảnh bìa bài viết</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="blogThumbnailFile" name="post_thumbnail">
                    <label class="custom-file-label rounded-pill" for="blogThumbnailFile">Chọn ảnh bìa...</label>
                  </div>
                </div>
                <button type="submit" class="btn btn-warning btn-block rounded-pill font-weight-bold py-2 shadow-sm">
                  <i class="fas fa-paper-plane mr-2"></i>Đăng Bài Viết
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tab 2: Kiểm duyệt bình luận & Đánh giá vi phạm -->
      <div class="tab-pane fade" id="tabModeration" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-lg p-3 bg-white">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Thời gian</th>
                  <th>Người dùng</th>
                  <th>Loại nội dung</th>
                  <th>Nội dung bị báo cáo</th>
                  <th class="text-center">Hành động</th>
                </tr>
              </thead>
              <tbody class="small">
                <tr>
                  <td>06/11 08:30</td>
                  <td><strong>user_spam_99</strong></td>
                  <td><span class="badge badge-pill badge-secondary">Bình luận Blog</span></td>
                  <td>Vào ngay trang web abc-link-lua-dao.com để nhận voucher 500k...</td>
                  <td class="text-center text-nowrap">
                    <button class="btn btn-sm btn-success rounded-pill mr-1 font-weight-bold">Giữ lại</button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold">Xóa vi phạm</button>
                  </td>
                </tr>
                <tr>
                  <td>06/11 09:15</td>
                  <td><strong>Khách hàng ẩn danh</strong></td>
                  <td><span class="badge badge-pill badge-warning">Đánh giá 1 Sao</span></td>
                  <td>Giao hàng quá lâu, dịch vụ tệ bạc không chấp nhận được...</td>
                  <td class="text-center text-nowrap">
                    <button class="btn btn-sm btn-success rounded-pill mr-1 font-weight-bold">Giữ lại</button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold">Xóa vi phạm</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>