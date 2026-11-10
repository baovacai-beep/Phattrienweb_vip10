<?php
require_once __DIR__ . "/../../models/RealtimeContentModel.php";

$rtModel   = new RealtimeContentModel();
$articleId = isset($_GET['id']) ? max(1, (int)$_GET['id']) : 1;
$article   = $rtModel->getArticleById($articleId, true);
$comments  = $rtModel->getCommentsByArticle((int)$article['id']);
$allPosts  = $rtModel->getArticles('all', '', 'newest');

$page_title = $article['title'] . " - Tạp Chí FashionKids";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";

// Danh sách sản phẩm gợi ý gắn kèm bài viết
$relatedProductsCatalog = [
    101 => [
        'id'    => 101,
        'name'  => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'price' => 285000,
        'image' => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
        'sku'   => 'FK-LBN-01'
    ],
    102 => [
        'id'    => 102,
        'name'  => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'price' => 225000,
        'image' => '/FashionKids/images/bo-thun-khung-long.jpg',
        'sku'   => 'FK-LBN-02'
    ],
    103 => [
        'id'    => 103,
        'name'  => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre',
        'price' => 210000,
        'image' => '/FashionKids/images/giay-sandal-tap-di.jpg',
        'sku'   => 'FK-LBN-03'
    ],
    104 => [
        'id'    => 104,
        'name'  => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'price' => 320000,
        'image' => '/FashionKids/images/ao-khoac-cardigan.jpg',
        'sku'   => 'FK-LBN-04'
    ]
];
?>

<div class="container my-4 my-md-5" id="fk_blog_detail_section">
    <!-- Breadcrumb Điều Hướng -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white rounded-pill px-4 py-2 shadow-sm border mb-0 small">
            <li class="breadcrumb-item">
                <a href="/FashionKids/views/pages/home.php" class="text-muted-custom text-decoration-none">
                    <i class="fa-solid fa-house mr-1 text-peach"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="/FashionKids/views/pages/blog-feeds.php" class="text-muted-custom text-decoration-none">
                    Tạp chí Mẫu nhí
                </a>
            </li>
            <li class="breadcrumb-item active font-weight-bold text-peach" aria-current="page">
                <?= htmlspecialchars($article['category_name']) ?>
            </li>
        </ol>
    </nav>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (8 CỘT): NỘI DUNG BÀI VIẾT & BÌNH LUẬN ĐA CẤP REALTIME
             ================================================================ -->
        <div class="col-12 col-lg-8 mb-4">
            <!-- Khung Nội Dung Bài Viết Chính -->
            <div class="card card-pastel border-0 shadow-sm p-4 p-md-5 mb-4 bg-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <span class="badge <?= htmlspecialchars($article['badge_class']) ?> px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-feather-pointed mr-1"></i><?= htmlspecialchars($article['category_name']) ?>
                    </span>
                    <div class="small text-muted-custom font-weight-bold mt-2 mt-sm-0">
                        <span class="mr-3"><i class="fa-regular fa-calendar text-peach mr-1"></i><?= htmlspecialchars($article['published_at']) ?></span>
                        <span class="mr-3"><i class="fa-regular fa-clock text-info mr-1"></i><?= htmlspecialchars($article['read_time']) ?></span>
                        <span><i class="fa-regular fa-eye text-success mr-1"></i><strong id="fk_detailViewCount"><?= number_format($article['views']) ?></strong> lượt xem</span>
                    </div>
                </div>

                <h2 class="font-weight-bold text-slate mb-3">
                    <?= htmlspecialchars($article['title']) ?>
                </h2>

                <!-- Thông tin Tác giả -->
                <div class="d-flex align-items-center p-3 rounded bg-milk-cream border mb-4">
                    <div class="rounded-circle bg-peach text-white d-flex align-items-center justify-content-center avatar-48 mr-3 font-weight-bold">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-slate"><?= htmlspecialchars($article['author']) ?></div>
                        <small class="text-muted-custom"><?= htmlspecialchars($article['author_role']) ?> • Đã kiểm duyệt nội dung chuẩn Y khoa &amp; Thời trang</small>
                    </div>
                </div>

                <!-- Ảnh Bìa Bài Viết -->
                <img src="<?= htmlspecialchars($article['image']) ?>" class="blog-header-banner mb-4 shadow-sm" alt="<?= htmlspecialchars($article['title']) ?>">

                <!-- Nội Dung Chi Tiết Bài Viết -->
                <div class="article-body text-slate">
                    <?= $article['content'] ?>
                </div>

                <!-- Thẻ Tags Bài Viết -->
                <div class="mt-4 pt-3 border-top">
                    <span class="small font-weight-bold text-slate mr-2"><i class="fa-solid fa-tags text-peach mr-1"></i>Từ khóa chuyên đề:</span>
                    <?php foreach ($article['tags'] as $tg): ?>
                        <a href="/FashionKids/views/pages/blog-feeds.php" class="badge badge-pill badge-light border text-slate px-3 py-1 mr-1 mb-1 text-decoration-none">
                            <?= htmlspecialchars($tg) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Thanh Nút Tương Tác: Thả Tim, Lưu Bài Viết & Chia Sẻ -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-4 border-top">
                    <div class="d-flex align-items-center mb-2 mb-sm-0">
                        <button type="button"
                                onclick="fkToggleLikeArticle(<?= $article['id'] ?>, this)"
                                class="btn btn-light border fk-btn-like-blog px-4 py-2 mr-2 shadow-sm <?= !empty($article['is_liked']) ? 'liked' : '' ?>"
                                data-article-id="<?= $article['id'] ?>">
                            <i class="fa-solid fa-heart text-danger mr-1"></i>
                            Thả tim bài viết (<span class="fk-like-count-<?= $article['id'] ?>"><?= number_format($article['likes']) ?></span>)
                        </button>
                        <button type="button"
                                onclick="fkToggleBookmarkArticle(<?= $article['id'] ?>, this)"
                                class="btn btn-light border fk-btn-bookmark-blog px-3 py-2 shadow-sm <?= !empty($article['is_bookmarked']) ? 'bookmarked' : '' ?>"
                                data-article-id="<?= $article['id'] ?>">
                            <i class="fa-solid fa-bookmark text-warning mr-1"></i>Lưu cẩm nang
                        </button>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button"
                                onclick="fkShareBlogLink(<?= $article['id'] ?>, '<?= htmlspecialchars($article['title'], ENT_QUOTES) ?>')"
                                class="btn btn-pastel-sky btn-sm px-3 py-2 font-weight-bold">
                            <i class="fa-solid fa-link mr-1"></i>Sao chép link bài viết
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 KHỐI BÌNH LUẬN ĐA CẤP REALTIME (tgb_binh_luan_bai_viet)
                 ============================================================ -->
            <div class="card card-pastel border-0 shadow-sm p-4 p-md-5 bg-white" id="fk_commentsSection">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold text-slate mb-0">
                        <i class="fa-solid fa-comments text-peach mr-2"></i>Bình Luận &amp; Chia Sẻ Từ Phụ Huynh (<span id="fk_totalCommentsCounter"><?= count($comments) ?></span>)
                    </h5>
                    <span class="badge badge-pill badge-mint px-3 py-1">
                        <i class="fa-solid fa-bolt mr-1"></i>Bình luận trực tuyến
                    </span>
                </div>

                <?php if (Rbac::isGuest()): ?>
                    <!-- Khách vãng lai: Chỉ được Đọc, muốn Bình luận / Thả tim phải Đăng nhập -->
                    <div class="p-4 rounded bg-milk-cream border mb-4 text-center">
                        <i class="fa-solid fa-user-lock text-peach h3 mb-2"></i>
                        <h6 class="font-weight-bold text-slate mb-1">Chức năng Bình luận &amp; Thả tim dành cho Thành viên</h6>
                        <p class="small text-muted-custom mb-3">
                            Quý phụ huynh có thể tự do đọc toàn bộ bài viết Tạp chí. Vui lòng đăng nhập tài khoản thành viên để gửi bình luận và lưu bài viết yêu thích nhé!
                        </p>
                        <div class="d-flex justify-content-center flex-wrap">
                            <a href="/FashionKids/views/pages/login-register.php?redirect=<?= urlencode('/FashionKids/views/pages/blog-detail.php?id=' . $article['id']) ?>" class="btn btn-pastel-primary btn-sm rounded-pill px-4 m-1 font-weight-bold">
                                <i class="fa-solid fa-right-to-bracket mr-1"></i>Đăng nhập để bình luận
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Form Gửi Bình Luận Mới (Chỉ hiển thị khi đã Đăng nhập) -->
                    <form id="fk_formBlogComment" class="mb-4 p-3 rounded bg-milk-cream border">
                        <input type="hidden" id="fk_commentArticleId" value="<?= $article['id'] ?>">
                        <div class="form-row mb-2">
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <label class="small font-weight-bold text-slate mb-1" for="fk_commentAuthorName">Tài khoản bình luận:</label>
                                <input type="text" id="fk_commentAuthorName" class="form-control form-control-sm rounded-pill px-3 bg-white"
                                       value="<?= htmlspecialchars($_SESSION['user']['ho_va_ten'] ?? 'Phụ Huynh Thành Viên') ?>"
                                       readonly>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <small class="text-muted-custom">
                                    <i class="fa-solid fa-shield-heart text-success mr-1"></i>Thành viên đã xác thực tài khoản
                                </small>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <textarea id="fk_commentContent" class="form-control rounded-lg" rows="3"
                                      placeholder="Chia sẻ cảm nhận của ba mẹ hoặc đặt câu hỏi về cách phối đồ, chọn size sợi tre cho bé..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-flex flex-wrap">
                                <button type="button" onclick="fkQuickInsertComment('Bài viết rất bổ ích, cảm ơn FashionKids!')"
                                        class="btn btn-light btn-sm border rounded-pill mr-1 py-0 px-2 small text-slate">
                                    + Bài viết rất bổ ích!
                                </button>
                                <button type="button" onclick="fkQuickInsertComment('Chất vải sợi tre của shop mặc mát và êm da bé lắm ạ.')"
                                        class="btn btn-light btn-sm border rounded-pill py-0 px-2 small text-slate">
                                    + Vải sợi tre rất mát!
                                </button>
                            </div>
                            <button type="submit" id="fk_btnSubmitComment" class="btn btn-pastel-primary px-4 py-2 font-weight-bold shadow-sm">
                                <i class="fa-solid fa-paper-plane mr-1"></i>Gửi Bình Luận
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- Danh Sách Bình Luận Cha & Phản Hồi Con (Nested Replies) -->
                <div id="fk_blogCommentsList">
                    <?php foreach ($comments as $com): ?>
                        <div class="fk-comment-item mb-3" id="fk_commentThread_<?= $com['id'] ?>">
                            <!-- Bình luận gốc (Cha) -->
                            <div class="fk-comment-box p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="rounded-circle bg-peach text-white d-inline-flex align-items-center justify-content-center avatar-40 mr-2 font-weight-bold">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <div>
                                            <strong class="text-slate mr-2"><?= htmlspecialchars($com['author_name']) ?></strong>
                                            <span class="badge badge-pill badge-gold small"><?= htmlspecialchars($com['vip_badge'] ?? 'Thành Viên') ?></span>
                                        </div>
                                    </div>
                                    <small class="text-muted-custom"><?= htmlspecialchars($com['created_at']) ?></small>
                                </div>
                                <p class="mb-2 text-slate small pl-5"><?= htmlspecialchars($com['content']) ?></p>
                                <?php if (!Rbac::isGuest()): ?>
                                    <div class="pl-5">
                                        <button type="button"
                                                onclick="fkToggleReplyForm(<?= $com['id'] ?>)"
                                                class="btn btn-link btn-sm p-0 text-peach font-weight-bold text-decoration-none mr-3">
                                            <i class="fa-solid fa-reply mr-1"></i>Phản hồi
                                        </button>
                                    </div>

                                    <!-- Khung nhập phản hồi đa cấp ẩn -->
                                    <div id="fk_replyFormBox_<?= $com['id'] ?>" class="mt-3 pl-4 d-none">
                                        <div class="input-group input-group-sm">
                                            <input type="text" id="fk_replyInput_<?= $com['id'] ?>" class="form-control rounded-pill mr-2"
                                                   placeholder="Viết phản hồi gửi tới <?= htmlspecialchars($com['author_name']) ?>...">
                                            <div class="input-group-append">
                                                <button type="button" onclick="fkSubmitReplyComment(<?= $article['id'] ?>, <?= $com['id'] ?>)"
                                                        class="btn btn-pastel-primary btn-sm rounded-pill px-3 font-weight-bold">
                                                    Gửi phản hồi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Danh sách phản hồi cấp 2 (Con) -->
                            <div class="ml-4 ml-md-5 mt-2" id="fk_repliesContainer_<?= $com['id'] ?>">
                                <?php if (!empty($com['replies'])): ?>
                                    <?php foreach ($com['replies'] as $rep): ?>
                                        <div class="fk-comment-reply-box p-3 mb-2 shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div>
                                                    <strong class="text-slate small mr-1">
                                                        <i class="fa-solid fa-circle-check text-success mr-1"></i><?= htmlspecialchars($rep['author_name']) ?>
                                                    </strong>
                                                    <span class="badge badge-pill badge-mint small"><?= htmlspecialchars($rep['vip_badge'] ?? 'Quản Trị Viên') ?></span>
                                                </div>
                                                <small class="text-muted-custom"><?= htmlspecialchars($rep['created_at']) ?></small>
                                            </div>
                                            <p class="mb-0 text-slate small"><?= htmlspecialchars($rep['content']) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (4 CỘT): SẢN PHẨM GỢI Ý TRONG BÀI & BÀI VIẾT LIÊN QUAN
             ================================================================ -->
        <div class="col-12 col-lg-4">
            <!-- Sản phẩm xuất hiện trong bài viết -->
            <div class="card card-pastel border-0 shadow-sm p-4 mb-4 bg-white">
                <h6 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-bag-shopping text-peach mr-2"></i>Sản Phẩm Gợi Ý Trong Bài Viết
                </h6>
                <?php foreach ($article['related_product_ids'] as $pid): ?>
                    <?php if (isset($relatedProductsCatalog[$pid])): $rp = $relatedProductsCatalog[$pid]; ?>
                        <div class="p-2 rounded bg-milk-cream border mb-3 d-flex align-items-center">
                            <a href="/FashionKids/views/pages/product-detail.php?id=<?= $rp['id'] ?>" class="lbn-inv-thumb mr-3 flex-shrink-0">
                                <img src="<?= htmlspecialchars($rp['image']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="lbn-thumb-img">
                            </a>
                            <div class="flex-grow-1">
                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $rp['id'] ?>" class="small font-weight-bold text-slate d-block text-decoration-none mb-1">
                                    <?= htmlspecialchars($rp['name']) ?>
                                </a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small font-weight-bold text-peach"><?= number_format($rp['price'], 0, ',', '.') ?> đ</span>
                                    <button type="button"
                                            onclick="lbnQuickAddToCart('<?= htmlspecialchars($rp['name'], ENT_QUOTES) ?>', <?= $rp['price'] ?>, <?= $rp['id'] ?>)"
                                            class="btn btn-pastel-primary btn-sm py-0 px-2 small">
                                        <i class="fa-solid fa-cart-plus mr-1"></i>Mua
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <a href="/FashionKids/views/pages/shop-catalog.php" class="btn btn-pastel-outline btn-sm btn-block font-weight-bold rounded-pill mt-2">
                    Khám phá toàn bộ Cửa Hàng &raquo;
                </a>
            </div>

            <!-- Danh sách bài viết nổi bật khác -->
            <div class="card card-pastel border-0 shadow-sm p-4 bg-white">
                <h6 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-fire text-warning mr-2"></i>Chuyên Đề Tạp Chí Đáng Đọc
                </h6>
                <?php foreach ($allPosts as $other): ?>
                    <?php if ((int)$other['id'] !== (int)$article['id']): ?>
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $other['id'] ?>" class="lbn-inv-thumb mr-3 flex-shrink-0">
                                <img src="<?= htmlspecialchars($other['image']) ?>" alt="<?= htmlspecialchars($other['title']) ?>" class="lbn-thumb-img">
                            </a>
                            <div>
                                <span class="badge <?= htmlspecialchars($other['badge_class']) ?> small mb-1"><?= htmlspecialchars($other['category_name']) ?></span>
                                <a href="/FashionKids/views/pages/blog-detail.php?id=<?= $other['id'] ?>" class="small font-weight-bold text-slate d-block text-decoration-none">
                                    <?= htmlspecialchars($other['title']) ?>
                                </a>
                                <small class="text-muted-custom">
                                    <i class="fa-solid fa-heart text-danger mr-1"></i><?= number_format($other['likes']) ?> • <?= htmlspecialchars($other['read_time']) ?>
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
