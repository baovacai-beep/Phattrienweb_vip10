<!-- ==========================================
     MH 18: CHI TIẾT BLOG & BÌNH LUẬN (TGB_)
     ========================================== -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <!-- Bài viết -->
            <article class="bg-white p-4 p-md-5 rounded-lg shadow-sm mb-5">
                <div class="mb-3">
                    <a href="blog-feeds.php" class="text-peach small font-weight-bold"><i class="fas fa-arrow-left mr-1"></i>Quay lại danh mục</a>
                </div>
                <h1 class="font-weight-bold text-slate mb-3">Top 5 Cách Phối Đồ Cho Bé Gái Đi Tiệc Cực Đáng Yêu Mùa Hè 2026</h1>
                <div class="d-flex align-items-center text-muted small mb-4 pb-3 border-bottom">
                    <span class="mr-3"><i class="fas fa-user-edit mr-1"></i>Hồ Gia Bảo (Chủ Shop)</span>
                    <span class="mr-3"><i class="far fa-calendar-alt mr-1"></i>26/10/2026</span>
                    <span><i class="fas fa-eye mr-1"></i>340 lượt đọc</span>
                </div>

                <div class="blog-content text-slate line-height-lg mb-5">
                    <p class="lead font-weight-normal">Mùa tiệc tùng hè này, việc lựa chọn trang phục vừa bảo đảm nét đáng yêu công chúa, vừa giúp bé không bị bí mồ hôi luôn là ưu tiên hàng đầu của mọi phụ huynh.</p>
                    <img src="/uploads/blogs/blog_be_gai_tiec.jpg" class="img-fluid rounded-lg my-4 shadow-sm" alt="Chi tiết bài viết">
                    <h4 class="font-weight-bold text-slate mt-4">1. Ưu tiên chất liệu sợi tre và cotton kháng khuẩn</h4>
                    <p>Khác với các dòng đầm người lớn chuộng form cứng, đầm trẻ em phải có lớp lót trong 100% sợi tre organic giúp thấm hút mồ hôi siêu tốc và không cọ sát làm đỏ làn da nhạy cảm của bé...</p>
                </div>

                <!-- Cụm tương tác: Thả tim & Share -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" id="btnLikeBlog" onclick="handleLikeBlog(1)">
                        <i class="fas fa-heart mr-2"></i>Thả tim (<span id="blogLikeCount">25</span>)
                    </button>
                    <div>
                        <span class="text-muted mr-2 small">Chia sẻ:</span>
                        <button class="btn btn-sm btn-outline-primary rounded-circle mr-1"><i class="fab fa-facebook-f"></i></button>
                        <button class="btn btn-sm btn-outline-info rounded-circle"><i class="fas fa-share-alt"></i></button>
                    </div>
                </div>
            </article>

            <!-- Vùng Bình luận & Trả lời đa cấp -->
            <div class="card border-0 shadow-sm rounded-lg p-4 bg-white">
                <h5 class="font-weight-bold text-slate mb-4">Bình luận từ phụ huynh (2)</h5>

                <!-- Form gửi comment -->
                <form class="mb-4">
                    <div class="form-group">
                        <textarea class="form-control rounded-lg" rows="3" placeholder="Chia sẻ kinh nghiệm hoặc đặt câu hỏi phối đồ cho bé..."></textarea>
                    </div>
                    <button type="button" class="btn btn-peach rounded-pill px-4 text-white font-weight-bold">Gửi bình luận</button>
                </form>

                <!-- Luồng comment phân cấp -->
                <div class="blog-comments-list">
                    <!-- Bình luận cha -->
                    <div class="media mb-4">
                        <img src="/uploads/avatars/avatar_mom1.jpg" class="mr-3 rounded-circle" width="45" height="45" alt="Avatar">
                        <div class="media-body">
                            <h6 class="mt-0 font-weight-bold text-slate mb-1">Mẹ Bé Sam <small class="text-muted ml-2">26/10/2026</small></h6>
                            <p class="text-slate mb-2">Bài viết rất hữu ích! Shop cho mình hỏi mẫu đầm Elsa trắng có size cho bé 4 tuổi nặng 18kg không?</p>
                            <button class="btn btn-sm btn-link text-peach p-0"><i class="fas fa-reply mr-1"></i>Trả lời</button>

                            <!-- Bình luận con (Reply) -->
                            <div class="media blog-comment-reply mt-3">
                                <img src="/uploads/avatars/avatar_admin.jpg" class="mr-3 rounded-circle" width="40" height="40" alt="Admin">
                                <div class="media-body">
                                    <h6 class="mt-0 font-weight-bold text-slate mb-1">Hồ Gia Bảo (Chủ Shop) <span class="badge badge-peach text-white ml-1">Admin</span></h6>
                                    <p class="text-slate mb-1">Dạ chào mẹ bé Sam, bé 18kg mặc Size 3 (3-4T) form thoải mái rất vừa vặn ạ!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>