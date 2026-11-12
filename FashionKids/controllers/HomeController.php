<?php
/**
 * Lớp HomeController (controllers/HomeController.php)
 * Điều hướng Trang chủ (MH 01), Tạp chí & Báo chí (MH 18, MH 19, MH 21),
 * Tin nhắn Tư vấn Trực tuyến Realtime (MH 08, MH 11),
 * Lịch Sự kiện Banner & Tuyển chọn KOL Nhí (MH 16, MH 17, MH 23)
 * Tích hợp kiểm soát truy cập RBAC 4 vai trò (Guest, Customer, Staff, Admin).
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../core/TurnstileService.php';
require_once __DIR__ . '/../models/RealtimeContentModel.php';

class HomeController extends Controller {
    private object $productModel;
    private RealtimeContentModel $realtimeModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        Rbac::syncSession();
        $this->productModel  = $this->model('ProductModel');
        $this->realtimeModel = new RealtimeContentModel();
    }

    /**
     * Hiển thị Trang chủ FashionKids (MH 01)
     */
    public function index(): void {
        $featuredProducts = $this->productModel->getFeaturedProducts(4);

        $data = [
            'page_title'             => 'Trang Chủ & BST Mùa Hè 2026',
            'lnkt_featured_products' => $featuredProducts
        ];

        $this->view('home', $data);
    }

    /**
     * API AJAX Lọc & Tìm kiếm Bài viết Tạp chí / Báo chí Realtime
     * URL: index.php?c=home&a=ajaxBlogFeed
     */
    public function ajaxBlogFeed(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $category = trim($_GET['category'] ?? $_POST['category'] ?? 'all');
        $keyword  = trim($_GET['keyword'] ?? $_POST['keyword'] ?? '');
        $sort     = trim($_GET['sort'] ?? $_POST['sort'] ?? 'newest');

        $articles = $this->realtimeModel->getArticles($category, $keyword, $sort);
        $this->json([
            'status'   => 'success',
            'count'    => count($articles),
            'articles' => $articles
        ]);
    }

    /**
     * API AJAX Thả Tim (Like) Bài viết Tạp chí Realtime
     * URL: index.php?c=home&a=ajaxLikeBlog
     * RBAC: Chỉ dành cho Thành viên đã đăng nhập (Customer / Staff / Admin)
     */
    public function ajaxLikeBlog(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập tài khoản Thành viên để thả tim bài viết tạp chí!'
        );

        $articleId = (int)($_POST['article_id'] ?? $_GET['article_id'] ?? 1);
        $res = $this->realtimeModel->toggleLikeArticle($articleId);
        $this->json([
            'status'     => 'success',
            'article_id' => $res['article_id'],
            'likes'      => $res['likes'],
            'is_liked'   => $res['is_liked'],
            'message'    => $res['is_liked'] ? 'Đã thả tim bài viết!' : 'Đã bỏ thích bài viết.'
        ]);
    }

    /**
     * API AJAX Lưu bài viết (Bookmark)
     * URL: index.php?c=home&a=ajaxBookmarkBlog
     */
    public function ajaxBookmarkBlog(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $articleId = (int)($_POST['article_id'] ?? $_GET['article_id'] ?? 1);
        $res = $this->realtimeModel->toggleBookmarkArticle($articleId);
        $this->json([
            'status'        => 'success',
            'article_id'    => $res['article_id'],
            'is_bookmarked' => $res['is_bookmarked'],
            'total_saved'   => $res['total_saved'],
            'message'       => $res['is_bookmarked'] ? 'Đã lưu bài viết vào danh sách đọc sau!' : 'Đã bỏ lưu bài viết.'
        ]);
    }

    /**
     * API AJAX Gửi Bình luận / Phản hồi đa cấp trong Bài viết Tạp chí (tgb_binh_luan_bai_viet)
     * URL: index.php?c=home&a=ajaxPostBlogComment
     * RBAC: Khách vãng lai (Guest) chỉ được Đọc; Thành viên (Customer/Staff/Admin) mới được Bình luận
     */
    public function ajaxPostBlogComment(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Khách vãng lai chỉ có quyền đọc Tạp chí. Vui lòng đăng nhập tài khoản để gửi bình luận!'
        );

        $articleId  = (int)($_POST['article_id'] ?? 1);
        $parentId   = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $content    = trim($_POST['content'] ?? '');
        $authorName = (string)($_SESSION['user']['ho_va_ten'] ?? trim($_POST['author_name'] ?? ''));

        if ($content === '') {
            $this->json(['status' => 'error', 'message' => 'Vui lòng nhập nội dung bình luận!'], 400);
            return;
        }

        $comment = $this->realtimeModel->addComment($articleId, $content, $parentId, $authorName ?: null);
        $this->json([
            'status'  => 'success',
            'message' => 'Đã đăng bình luận thành công!',
            'comment' => $comment
        ]);
    }

    /**
     * API AJAX Đăng bài viết mới từ Bàn làm việc CMS (admin-cms-blog.php)
     * URL: index.php?c=home&a=ajaxSaveBlogArticle
     * RBAC: Nhân viên (nhan_vien) chỉ được lưu bản nháp (ban_nhap); Quản trị viên (quan_tri) được xuất bản (xuat_ban)
     */
    public function ajaxSaveBlogArticle(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên và Quản trị viên mới được phép chỉnh sửa nội dung Tạp chí.'
        );

        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            $this->json(['status' => 'error', 'message' => 'Tiêu đề bài viết không được để trống!'], 400);
            return;
        }

        $payload = $_POST;
        if (Rbac::isStaff()) {
            $payload['status'] = 'ban_nhap';
        }

        $article = $this->realtimeModel->saveArticle($payload);
        $msg = Rbac::isStaff()
            ? 'Đã lưu bài viết ở trạng thái Bản nháp chờ quản trị viên duyệt xuất bản.'
            : 'Đã xuất bản bài viết mới lên Tạp chí FashionKids thành công!';

        $this->json([
            'status'  => 'success',
            'message' => $msg,
            'article' => $article
        ]);
    }

    /**
     * API AJAX Gửi Tin nhắn Tư vấn Trực tuyến Realtime (tgb_tin_nhan_tu_van)
     * URL: index.php?c=home&a=ajaxSendChat
     * RBAC: Guest lưu theo ma_phien_khach = session_id(); Customer lưu theo ma_nguoi_dung; Staff/Admin phản hồi
     */
    public function ajaxSendChat(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $sessionId = trim($_POST['session_id'] ?? Rbac::chatSessionId());
        $sender    = trim($_POST['sender'] ?? 'khach_hang');
        $message   = trim($_POST['message'] ?? '');

        if ($message === '') {
            $this->json(['status' => 'error', 'message' => 'Tin nhắn không được để trống!'], 400);
            return;
        }

        if (in_array($sender, ['nhan_vien', 'quan_tri'], true) && !Rbac::isStaffOrAdmin()) {
            $this->json(['status' => 'error', 'message' => 'Chỉ nhân viên tư vấn mới được gửi phản hồi từ bàn làm việc CSKH.'], 403);
            return;
        }

        if (!in_array($sender, ['khach_hang', 'nhan_vien', 'quan_tri'], true)) {
            $sender = 'khach_hang';
        }

        $data = $this->realtimeModel->sendChatMessage($sessionId, $sender, $message, $sender === 'khach_hang');
        $this->json([
            'status'     => 'success',
            'sent'       => $data['sent'],
            'auto_reply' => $data['auto_reply'] ?? null,
            'messages'   => $data['messages'],
            'sessions'   => $this->realtimeModel->getChatSessions()
        ]);
    }

    /**
     * API AJAX Đồng bộ Tin nhắn Tư vấn Realtime (Polling) & Đánh dấu đã đọc (tgb_danh_dau_tin_da_doc)
     * URL: index.php?c=home&a=ajaxPollChat
     */
    public function ajaxPollChat(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $sessionId = trim($_GET['session_id'] ?? $_POST['session_id'] ?? Rbac::chatSessionId());
        $markRead  = isset($_GET['mark_read']) ? (int)$_GET['mark_read'] : (isset($_POST['mark_read']) ? (int)$_POST['mark_read'] : (Rbac::isStaffOrAdmin() ? 1 : 0));
        if ($markRead === 1 && Rbac::isStaffOrAdmin()) {
            $this->realtimeModel->markChatSessionAsRead($sessionId);
        }
        $this->json([
            'status'     => 'success',
            'session_id' => $sessionId,
            'messages'   => $this->realtimeModel->getChatMessages($sessionId),
            'sessions'   => Rbac::isStaffOrAdmin() ? $this->realtimeModel->getChatSessions() : [],
            'routine'    => 'tgb_danh_dau_tin_da_doc',
            'timestamp'  => date('H:i:s')
        ]);
    }

    /**
     * API AJAX Đánh dấu đã đọc tin nhắn CSKH (tgb_danh_dau_tin_da_doc) - MH 11
     * URL: index.php?c=home&a=ajaxMarkChatRead
     */
    public function ajaxMarkChatRead(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên CSKH và Quản trị viên mới được phép đồng bộ trạng thái đã đọc.'
        );
        $sessionId = trim($_POST['session_id'] ?? $_GET['session_id'] ?? 'sess_guest_123456');
        $updated   = $this->realtimeModel->markChatSessionAsRead($sessionId);
        $this->json([
            'status'       => 'success',
            'session_id'   => $sessionId,
            'updated_rows' => $updated,
            'sessions'     => $this->realtimeModel->getChatSessions(),
            'routine'      => 'tgb_danh_dau_tin_da_doc',
            'message'      => 'Đã đánh dấu đã đọc toàn bộ tin nhắn phiên ' . $sessionId
        ]);
    }

    /**
     * API AJAX Xuất bản bài viết Tạp chí từ Bản nháp - CHỈ ADMIN (MH 21/22)
     * URL: index.php?c=home&a=ajaxPublishBlogArticle
     */
    public function ajaxPublishBlogArticle(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới có thẩm quyền xuất bản chính thức bài viết Tạp chí!'
        );
        $id = (int)($_POST['article_id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã bài viết không hợp lệ!'], 400);
            return;
        }
        $res = $this->realtimeModel->publishArticle($id);
        $this->json([
            'status'     => 'success',
            'article_id' => $id,
            'article'    => $res,
            'message'    => 'Đã xuất bản chính thức bài viết #' . $id . ' lên Tạp chí FashionKids!'
        ]);
    }

    /**
     * API AJAX Xóa bài viết Tạp chí - CHỈ ADMIN (MH 21/22)
     * URL: index.php?c=home&a=ajaxDeleteBlogArticle
     */
    public function ajaxDeleteBlogArticle(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['quan_tri'],
            'Chỉ Quản trị viên mới có thẩm quyền xóa bài viết khỏi hệ thống Tạp chí!'
        );
        $id = (int)($_POST['article_id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['status' => 'error', 'message' => 'Mã bài viết cần xóa không hợp lệ!'], 400);
            return;
        }
        $this->realtimeModel->deleteArticle($id);
        $this->json([
            'status'     => 'success',
            'article_id' => $id,
            'message'    => 'Đã xóa bài viết #' . $id . ' khỏi hệ thống Tạp chí thành công!'
        ]);
    }

    /**
     * API AJAX Xét duyệt Hồ sơ Siêu sao nhí KOL (sp_lnkt_xet_duyet_kol_nhi)
     * URL: index.php?c=home&a=ajaxReviewKolCasting
     * RBAC: Nhân viên (nhan_vien) chỉ được duyệt Vòng 1 (moi_phong_van); Quản trị viên (quan_tri) quyết định duyệt (trung_tuyen/tu_choi)
     */
    public function ajaxReviewKolCasting(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['nhan_vien', 'quan_tri'],
            'Chỉ Nhân viên và Quản trị viên mới được xét duyệt hồ sơ.'
        );

        $id        = (int)($_POST['id'] ?? $_GET['id'] ?? 1);
        $newStatus = trim($_POST['status'] ?? $_GET['status'] ?? 'moi_phong_van');

        if (Rbac::isStaff() && in_array($newStatus, ['trung_tuyen', 'tu_choi'], true)) {
            $this->json([
                'status'  => 'error',
                'message' => 'Nhân viên chỉ được phép hẹn lịch phỏng vấn (Vòng 1). Quyết định trúng tuyển hoặc từ chối do quản trị viên phê duyệt.'
            ], 403);
            return;
        }

        $res = $this->realtimeModel->reviewKolApplication($id, $newStatus);
        $this->json([
            'status'     => 'success',
            'id'         => $res['id'],
            'new_status' => $res['new_status'],
            'counts'     => $res['counts'],
            'message'    => 'Đã cập nhật trạng thái hồ sơ Siêu sao nhí KOL thành công!'
        ]);
    }

    /**
     * API AJAX Nộp Hồ sơ Ứng tuyển KOL Nhí (lnkt_dang_ky_kol_nhi)
     * URL: index.php?c=home&a=ajaxSubmitKolCasting
     * RBAC: Guest chỉ xem thể lệ; Customer (khach_hang) mới được nộp hồ sơ
     */
    public function ajaxSubmitKolCasting(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        Rbac::requireRolesJson(
            ['khach_hang', 'nhan_vien', 'quan_tri'],
            'Vui lòng đăng nhập tài khoản thành viên để nộp hồ sơ ứng tuyển KOL Nhí.'
        );

        $tsCheck = TurnstileService::verifyToken($_POST['cf-turnstile-response'] ?? $_POST['turnstile_token'] ?? null);
        if (empty($tsCheck['success'])) {
            $this->json(['status' => 'error', 'message' => $tsCheck['message']], 422);
            return;
        }

        $res = $this->realtimeModel->submitKolApplication($_POST);
        $this->json([
            'status'      => 'success',
            'application' => $res,
            'message'     => 'Đã tiếp nhận hồ sơ ứng tuyển của ' . $res['baby_name'] . ' (Mã hồ sơ: #' . $res['code'] . ') thành công!'
        ]);
    }
}
