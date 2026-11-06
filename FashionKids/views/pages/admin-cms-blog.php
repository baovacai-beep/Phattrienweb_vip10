<!-- ==========================================
     MH 22: CMS BÀI VIẾT & KIỂM DUYỆT (TGB_)
     ========================================== -->
<div class="container-fluid py-4">
    <h4 class="font-weight-bold text-slate mb-4"><i class="fas fa-newspaper mr-2"></i>Quản Trị Truyền Thông & Kiểm Duyệt Cộng Đồng</h4>

    <!-- 2 Tab chính: Quản lý bài viết & Kiểm duyệt -->
    <ul class="nav nav-pills mb-4" id="cmsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 mr-2" id="posts-tab" data-toggle="pill" href="#tabPosts">Bài viết thời trang</a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4" id="moderation-tab" data-toggle="pill" href="#tabModeration">Kiểm duyệt đánh giá & bình luận</a>
        </li>
    </ul>

    <div class="tab-content" id="cmsTabsContent">
        <!-- TAB 1: Danh sách bài viết -->
        <div class="tab-pane fade show active" id="tabPosts" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-slate mb-0">Danh sách bài đã đăng</h6>
                    <button class="btn btn-peach rounded-pill text-white btn-sm px-3"><i class="fas fa-plus mr-1"></i>Soạn bài viết mới</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-milk-cream text-slate">
                            <tr>
                                <th>Ảnh</th>
                                <th>Tiêu đề bài viết</th>
                                <th>Tác giả</th>
                                <th>Tương tác</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="/uploads/blogs/blog_be_gai_tiec.jpg" width="60" class="rounded" alt="Thumb"></td>
                                <td><strong>Top 5 Cách Phối Đồ Cho Bé Gái Đi Tiệc...</strong></td>
                                <td>Hồ Gia Bảo</td>
                                <td><span class="text-peach mr-2"><i class="fas fa-heart"></i> 25</span> <span class="text-muted"><i class="fas fa-eye"></i> 340</span></td>
                                <td><span class="badge badge-success">Đã xuất bản</span></td>
                                <td>
                                    <button class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-light text-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Kiểm duyệt đánh giá & bình luận -->
        <div class="tab-pane fade" id="tabModeration" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-lg p-3 bg-white">
                <h6 class="font-weight-bold text-slate mb-3">Đánh giá sản phẩm mới cần duyệt</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-milk-cream text-slate">
                            <tr>
                                <th>Khách hàng</th>
                                <th>Sản phẩm</th>
                                <th>Số sao</th>
                                <th>Nội dung nhận xét</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Mẹ Bé Sam</td>
                                <td>Váy Công Chúa Elsa</td>
                                <td><span class="text-warning">★★★★★</span></td>
                                <td>Vải sợi tre rất êm ái, bé mặc vừa vặn và rất thích!</td>
                                <td>
                                    <button class="btn btn-sm btn-success rounded-pill px-3">Duyệt</button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Ẩn/Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>