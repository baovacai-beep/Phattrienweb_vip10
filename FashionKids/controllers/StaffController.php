<?php
/**
 * Lớp StaffController (controllers/StaffController.php)
 * Phân hệ Nhân viên vận hành (vai trò nhan_vien; quan_tri cũng được dùng vì cấp cao hơn).
 * Nhân viên KHÔNG có quyền: sửa/xóa sản phẩm, cấu hình VIP/size/banner, quyết định KOL, xuất bản/xóa bài viết.
 *
 * Màn hình : MH 10 orders, MH 11 chat, MH 17 castingReview
 * AJAX     : ajaxUpdateOrderStatus, ajaxRestock, ajaxLookupStock,
 *            ajaxChatSessions, ajaxChatPoll, ajaxChatSend, ajaxChatMarkRead,
 *            ajaxInviteKol, ajaxSaveDraft
 * Phản hồi : JSON { "status": "success|error", "message": "..." }; ob_clean() trước khi xuất.
 */

require_once __DIR__ . '/../core/Controller.php';

class StaffController extends Controller {
    private ?StaffModel $staffModel = null;
    private ?ChatModel $chatModel   = null;

    private function staff(): StaffModel {
        if ($this->staffModel === null) {
            $this->staffModel = $this->model('StaffModel');
        }
        return $this->staffModel;
    }

    private function chatModel(): ChatModel {
        if ($this->chatModel === null) {
            $this->chatModel = $this->model('ChatModel');
        }
        return $this->chatModel;
    }

    // ====================================================================== Trang

    public function orders(): void {
        $this->view('staff-orders', ['page_title' => 'Bàn Làm Việc Duyệt Đơn Hàng (MH 10)']);
    }

    public function chat(): void {
        $this->view('staff-chat', ['page_title' => 'Live Chat CSKH (MH 11)']);
    }

    public function castingReview(): void {
        $this->view('staff-casting-review', ['page_title' => 'Sơ Tuyển Hồ Sơ KOL Nhí (MH 17)']);
    }

    // ====================================================================== MH 10 – Đơn hàng

    /**
     * Tiếp nhận đơn (cho_duyet -> da_xac_nhan) và cập nhật tiến trình giao hàng
     * (da_xac_nhan -> dang_giao -> hoan_tat, hoặc da_huy) qua sp_lnkt_cap_nhat_trang_thai_don_hang.
     * POST: ma_don_hang, trang_thai
     */
    public function ajaxUpdateOrderStatus(): void {
        $this->guardAjax('order.process');
        $orderId = (int)($_POST['ma_don_hang'] ?? 0);
        $status  = (string)($_POST['trang_thai'] ?? '');

        $this->runJson(function () use ($orderId, $status) {
            if ($orderId <= 0) {
                throw new InvalidArgumentException('Mã đơn hàng không hợp lệ!');
            }
            if (!in_array($status, ['da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy'], true)) {
                throw new InvalidArgumentException('Trạng thái đơn hàng không hợp lệ!');
            }
            $new = $this->staff()->capNhatTienTrinhDon($orderId, Rbac::userId(), $status);
            return [
                'ma_don_hang' => $orderId,
                'trang_thai'  => $new,
                'counts'      => $this->staff()->demDonTheoTrangThai(),
                'message'     => "Đã cập nhật đơn #$orderId sang trạng thái \"$new\".",
            ];
        });
    }

    // ====================================================================== MH 11, 13 – Kho

    /** Nút "Nhập thêm kho" – sp_lbn_nhap_them_kho_hang (chỉ cộng thêm, SP chặn số lượng <= 0) */
    public function ajaxRestock(): void {
        $this->guardAjax('stock.restock');
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty       = filter_var($_POST['qty'] ?? null, FILTER_VALIDATE_INT);

        $this->runJson(function () use ($productId, $qty) {
            if ($productId <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_06]: Mã sản phẩm cần nhập kho không tồn tại!');
            }
            if ($qty === false || $qty === null || $qty <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_05]: Số lượng nhập hàng bổ sung phải là số nguyên lớn hơn 0!');
            }
            $info = $this->staff()->nhapThemKho($productId, $qty);
            $info['message'] = 'Đã nhập thêm ' . $qty . ' sản phẩm. Tồn kho hiện tại: ' . $info['new_stock'] . '.'
                . ($info['is_low_stock'] ? ' [Cảnh báo: vẫn <= 5, cần nhập thêm]' : '');
            return $info;
        });
    }

    /** Form tra cứu nhanh tồn kho (MH 11 – cột 3). POST/GET: keyword, qty (tuỳ chọn) */
    public function ajaxLookupStock(): void {
        $this->guardAjaxRead('stock.lookup');
        $keyword = (string)($_REQUEST['keyword'] ?? '');
        $qty     = (int)($_REQUEST['qty'] ?? 0);
        $this->runJson(function () use ($keyword, $qty) {
            $rows = $this->staff()->traCuuTonKho($keyword, $qty);
            return [
                'items'   => $rows,
                'message' => count($rows) . ' sản phẩm phù hợp.',
            ];
        });
    }

    // ====================================================================== MH 11 – Live Chat

    public function ajaxChatSessions(): void {
        $this->guardAjaxRead('chat.reply');
        $this->runJson(function () {
            return ['sessions' => $this->staff()->layPhienChat(), 'message' => 'OK'];
        });
    }

    /** Polling tin nhắn mới (client gọi mỗi 3 giây). GET/POST: session, after */
    public function ajaxChatPoll(): void {
        $this->guardAjaxRead('chat.reply');
        $session = trim((string)($_REQUEST['session'] ?? ''));
        $after   = (int)($_REQUEST['after'] ?? 0);
        $this->runJson(function () use ($session, $after) {
            if ($session === '') {
                throw new InvalidArgumentException('Thiếu mã phiên hội thoại!');
            }
            return array_merge($this->staff()->layTinNhan($session, $after), ['message' => 'OK']);
        });
    }

    public function ajaxChatSend(): void {
        $this->guardAjax('chat.reply');
        $session = trim((string)($_POST['session'] ?? ''));
        $content = (string)($_POST['content'] ?? '');
        $this->runJson(function () use ($session, $content) {
            $id = $this->staff()->guiTinNhan($session, Rbac::userId(), (string)Rbac::role(), $content);
            return ['ma_tin_nhan' => $id, 'message' => 'Đã gửi tin nhắn.'];
        });
    }

    /** Đồng bộ trạng thái "đã đọc" qua SP tgb_danh_dau_tin_da_doc */
    public function ajaxChatMarkRead(): void {
        $this->guardAjax('chat.reply');
        $session = trim((string)($_POST['session'] ?? ''));
        $this->runJson(function () use ($session) {
            if ($session === '') {
                throw new InvalidArgumentException('Thiếu mã phiên hội thoại!');
            }
            $n = $this->chatModel()->markAsRead($session, Rbac::userId());
            return ['marked' => $n, 'message' => "Đã đánh dấu $n tin nhắn là đã đọc."];
        });
    }

    // ====================================================================== MH 17 – KOL nhí (sơ tuyển)

    /** "Mời phỏng vấn": chỉ chuyển cho_duyet -> moi_phong_van. Quyết định cuối cùng thuộc Admin. */
    public function ajaxInviteKol(): void {
        $this->guardAjax('kol.invite');
        $id = (int)($_POST['ma_ho_so'] ?? 0);
        $this->runJson(function () use ($id) {
            $this->staff()->moiPhongVanKol($id, Rbac::userId());
            return ['ma_ho_so' => $id, 'trang_thai' => 'moi_phong_van', 'message' => "Đã mời phỏng vấn hồ sơ #$id."];
        });
    }

    // ====================================================================== MH 21/22 – Bản nháp bài viết

    /** Lưu "Bản nháp" – luôn ép trạng thái ban_nhap, không bao giờ xuất bản. */
    public function ajaxSaveDraft(): void {
        $this->guardAjax('blog.draft');
        $post = $_POST;

        $this->runJson(function () use ($post) {
            $image = '';
            if (!empty($_FILES['thumbnail']['name']) && ($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    throw new InvalidArgumentException('Ảnh bìa chỉ hỗ trợ JPG, PNG, WEBP!');
                }
                if ($_FILES['thumbnail']['size'] > 2 * 1024 * 1024) {
                    throw new InvalidArgumentException('Ảnh bìa tối đa 2MB!');
                }
                $dir = __DIR__ . '/../images/blog/';
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
                $name = 'blog_' . bin2hex(random_bytes(6)) . '.' . $ext;
                if (is_dir($dir) && @move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir . $name)) {
                    $image = '/FashionKids/images/blog/' . $name;
                }
            }
            $id = $this->staff()->luuBanNhap(
                Rbac::userId(),
                (string)($post['tieu_de'] ?? ''),
                (string)($post['tom_tat'] ?? ''),
                (string)($post['noi_dung'] ?? ''),
                $image,
                (int)($post['ma_bai_viet'] ?? 0)
            );
            return ['ma_bai_viet' => $id, 'message' => "Đã lưu bản nháp #$id."];
        });
    }
}
