<?php
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess('admin-cms-blog.php');
$isAdminRole = Rbac::isAdmin();
$page_title = "Quản Trị CMS & Kiểm Duyệt Nội Dung (MH 21/22)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
?>
<!-- views/pages/admin-cms-blog.php -->
<section class="admin-cms-section py-4 bg-milk-cream" id="tgb_admin_cms_blog_section">
  <div class="container-fluid px-lg-5">
    <div class="row mb-4 align-items-center">
      <div class="col-12 col-md-7">
        <h3 class="font-weight-bold text-slate mb-1">
          <i class="fa-solid fa-newspaper text-peach mr-2"></i>Quản Trị CMS Tạp Chí &amp; Kiểm Duyệt Nội Dung (MH 21/22)
        </h3>
        <p class="text-muted-custom small mb-0">
          Soạn thảo bài viết Tạp chí (<code>tgb_bai_viet_tin_tuc</code>) và kiểm duyệt bình luận/đánh giá từ cộng đồng
        </p>
      </div>
      <div class="col-12 col-md-5 text-md-right mt-2 mt-md-0">
        <span class="badge badge-pill <?= $isAdminRole ? 'badge-danger' : 'badge-info' ?> px-3 py-2 mr-2">
          <i class="fa-solid fa-user-shield mr-1"></i>
          <?= $isAdminRole ? 'Chế độ Admin: Quyền Xuất Bản & Xóa' : 'Chế độ Staff: Chỉ Lưu Bản Nháp' ?>
        </span>
        <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-pastel-outline btn-sm font-weight-bold rounded-pill px-3">
          <i class="fa-solid fa-book-open mr-1"></i> Xem Tạp Chí
        </a>
      </div>
    </div>

    <div id="tgb_cmsBlogAlert" class="alert d-none small font-weight-bold mb-4" role="alert"></div>

    <!-- Hàng chuyển Tab chức năng -->
    <div class="row mb-4">
      <div class="col-12">
        <ul class="nav nav-pills" id="cmsTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active font-weight-bold rounded-pill px-4" id="write-tab" data-toggle="pill" href="#tabWriteArticle" role="tab">
              <i class="fas fa-feather-alt mr-2"></i>Soạn Thảo Bài Viết Mới
            </a>
          </li>
          <?php if ($isAdminRole): ?>
            <li class="nav-item ml-2 fk-admin-only-action">
              <a class="nav-link font-weight-bold rounded-pill px-4" id="moderation-tab" data-toggle="pill" href="#tabModeration" role="tab">
                <i class="fas fa-shield-alt mr-2"></i>Kiểm Duyệt Bình Luận &amp; Quản Lý Bài Viết
                <span class="badge badge-danger ml-2" id="tgb_violationCountBadge">3 Mục</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="tab-content" id="cmsTabContent">
      <!-- Tab 1: Soạn thảo bài viết mới -->
      <div class="tab-pane fade show active" id="tabWriteArticle" role="tabpanel">
        <div class="card card-pastel border-0 shadow-sm rounded-lg p-4 bg-white">
          <form id="tgb_formCmsBlog" action="/FashionKids/index.php?c=admin&a=ajaxPublishBlog" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-12 col-md-8">
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-slate">Tiêu đề bài viết (<span class="text-danger">*</span>)</label>
                  <input type="text" class="form-control rounded-lg" id="tgb_blogTitle" name="post_title" value="Cẩm Nang Chọn Vải Sợi Tre Kháng Khuẩn Cho Trẻ Sơ Sinh Mùa Hè" placeholder="Nhập tiêu đề hấp dẫn..." required>
                </div>
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-slate">Tóm tắt ngắn (Excerpt)</label>
                  <textarea class="form-control rounded-lg" id="tgb_blogExcerpt" name="post_excerpt" rows="2" placeholder="Hiển thị ở lưới thẻ bài viết ngoài trang chủ...">Hướng dẫn ba mẹ nhận biết chất liệu sợi tre hữu cơ đạt chuẩn OEKO-TEX 100 an toàn tuyệt đối cho làn da nhạy cảm của bé.</textarea>
                </div>
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-slate">Nội dung chi tiết (Rich-Text)</label>
                  <textarea class="form-control rounded-lg" id="tgb_blogContent" name="post_content" rows="8" placeholder="Viết nội dung bài viết tại đây..." required>Làn da của trẻ nhỏ mỏng hơn người lớn gấp 5 lần, vì vậy chất liệu sợi tre tự nhiên với khả năng kháng khuẩn 99.8% và làm mát tự nhiên luôn là ưu tiên hàng đầu tại FashionKids.</textarea>
                </div>
              </div>

              <div class="col-12 col-md-4">
                <div class="form-group mb-3">
                  <label class="font-weight-bold small text-slate">Chuyên mục</label>
                  <select class="form-control rounded-pill" id="tgb_blogCategory" name="post_category">
                    <option value="tips">Mẹo phối đồ</option>
                    <option value="backstage">Hậu trường mẫu nhí</option>
                    <option value="fabric" selected>Chất liệu an toàn</option>
                  </select>
                </div>
                <div class="form-group mb-4">
                  <label class="font-weight-bold small text-slate">Ảnh bìa bài viết</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="blogThumbnailFile" name="post_thumbnail">
                    <label class="custom-file-label rounded-pill" for="blogThumbnailFile">Chọn ảnh bìa...</label>
                  </div>
                </div>

                <!-- Nút lưu Bản nháp (Dành cho cả Staff và Admin) -->
                <button type="button" id="tgb_btnSaveBlogDraft" onclick="tgbSaveBlogDraftAjax()" class="btn btn-pastel-sky btn-block rounded-pill font-weight-bold py-2 shadow-sm mb-2">
                  <i class="fas fa-file-pen mr-2"></i>Lưu Bản Nháp (ban_nhap)
                </button>

                <!-- Nút Xuất bản & Xóa bài viết (Quyền tối cao: Chỉ hiển thị cho Admin, ẩn hoàn toàn với Staff) -->
                <?php if ($isAdminRole): ?>
                  <button type="button" id="tgb_btnPublishBlog" onclick="tgbPublishBlogAjax()" class="btn btn-pastel-primary btn-block rounded-pill font-weight-bold py-2 shadow-sm mb-2 fk-admin-only-action">
                    <i class="fas fa-paper-plane mr-2"></i>Xuất Bản Bài Viết (xuat_ban)
                  </button>
                  <button type="button" id="tgb_btnDeleteBlogCurrent" onclick="tgbDeleteBlogItemAjax(1, 0, '#tgb_blogRow_1')" class="btn btn-outline-danger btn-block rounded-pill font-weight-bold py-2 fk-admin-only-action">
                    <i class="fas fa-trash-can mr-2"></i>Xóa Bài Viết Khỏi Tạp Chí
                  </button>
                <?php else: ?>
                  <div class="p-3 rounded bg-milk-cream border small text-muted-custom mt-3">
                    <i class="fa-solid fa-lock text-peach mr-1"></i>
                    Tài khoản <code>nhan_vien</code> chỉ được phép lưu <strong>Bản nháp</strong>. Quyền <strong>Xuất bản</strong> hoặc <strong>Xóa</strong> bài viết thuộc thẩm quyền Quản trị viên (Admin).
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tab 2: Kiểm duyệt bình luận & Quản lý bài viết (Chỉ dành cho Admin) -->
      <?php if ($isAdminRole): ?>
        <div class="tab-pane fade fk-admin-only-action" id="tabModeration" role="tabpanel">
          <div class="card card-pastel border-0 shadow-sm rounded-lg p-3 bg-white">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Thời gian</th>
                    <th>Tác giả / Người dùng</th>
                    <th>Loại nội dung</th>
                    <th>Tiêu đề / Nội dung kiểm duyệt</th>
                    <th class="text-center">Thao tác Admin</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <tr id="tgb_blogRow_1">
                    <td>05/11 10:00</td>
                    <td><strong>Hồ Gia Bảo (Admin)</strong></td>
                    <td><span class="badge badge-pill badge-mint">Bài viết Tạp chí #1</span></td>
                    <td>Top 5 Cách Phối Đồ Cho Bé Gái Đi Tiệc Cực Đáng Yêu Mùa Hè 2026</td>
                    <td class="text-center text-nowrap">
                      <button type="button" onclick="tgbPublishBlogAjax('Top 5 Cách Phối Đồ Cho Bé Gái Đi Tiệc Cực Đáng Yêu Mùa Hè 2026')" class="btn btn-sm btn-success rounded-pill mr-1 font-weight-bold">Xuất bản</button>
                      <button type="button" onclick="tgbDeleteBlogItemAjax(1, 0, '#tgb_blogRow_1')" class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold">Xóa bài viết</button>
                    </td>
                  </tr>
                  <tr id="tgb_commentRow_101">
                    <td>06/11 08:30</td>
                    <td><strong>user_spam_99</strong></td>
                    <td><span class="badge badge-pill badge-secondary">Bình luận Blog</span></td>
                    <td>Vào ngay trang web abc-link-lua-dao.com để nhận voucher 500k...</td>
                    <td class="text-center text-nowrap">
                      <button type="button" onclick="tgbKeepModerationItem('#tgb_commentRow_101')" class="btn btn-sm btn-success rounded-pill mr-1 font-weight-bold">Giữ lại</button>
                      <button type="button" onclick="tgbDeleteBlogItemAjax(0, 101, '#tgb_commentRow_101')" class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold">Xóa vi phạm</button>
                    </td>
                  </tr>
                  <tr id="tgb_commentRow_102">
                    <td>06/11 09:15</td>
                    <td><strong>Khách hàng ẩn danh</strong></td>
                    <td><span class="badge badge-pill badge-warning">Đánh giá 1 Sao</span></td>
                    <td>Giao hàng quá lâu, dịch vụ tệ bạc không chấp nhận được...</td>
                    <td class="text-center text-nowrap">
                      <button type="button" onclick="tgbKeepModerationItem('#tgb_commentRow_102')" class="btn btn-sm btn-success rounded-pill mr-1 font-weight-bold">Giữ lại</button>
                      <button type="button" onclick="tgbDeleteBlogItemAjax(0, 102, '#tgb_commentRow_102')" class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold">Xóa vi phạm</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
