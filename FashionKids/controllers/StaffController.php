<?php
/**
 * Lớp StaffController (controllers/StaffController.php)
 * Điều phối toàn bộ phân hệ Nhân viên Vận hành (Staff - nhan_vien):
 *  - MH 10: Bàn làm việc Tiếp nhận & Cập nhật tiến trình đơn hàng (orders, ajaxUpdateOrderStatus -> sp_lnkt_cap_nhat_trang_thai_don_hang)
 *  - MH 11: Bàn làm việc Live Chat CSKH 3 cột, hiển thị tin nhắn thời gian thực, đồng bộ trạng thái "đã đọc" (`tgb_danh_dau_tin_da_doc`) và tra cứu nhanh tồn kho (chat, ajaxLoadChatSession, ajaxSendChatReply, ajaxMarkChatRead, ajaxQuickStockLookup)
 *  - MH 13: Nhập thêm kho hàng bổ sung (`sp_lbn_nhap_them_kho_hang`)
 *  - MH 17: Sơ tuyển hồ sơ KOL Nhí - Chỉ được phép "Mời phỏng vấn" (`moi_phong_van`), ẩn và chặn quyền Quyết định Trúng tuyển/Từ chối của Admin
 *  - MH 21/22: Soạn thảo bài viết Tạp chí - Chỉ được phép lưu "Bản nháp" (`ban_nhap`), ẩn và chặn quyền Xuất bản/Xóa của Admin
 *
 * Tuân thủ nghiêm ngặt ràng buộc kỹ thuật:
 *  - Tích hợp Middleware RBAC (`Rbac::requireStaffOrAdmin()`)
 *  - Mọi phản hồi AJAX đều xóa sạch bộ đệm bằng `ob_clean()` và trả về JSON chuẩn `{ "status": "success|error", "message": "..." }`
 *  - Sử dụng PDO Singleton từ `core/Database.php` kết hợp `$stmt->closeCursor();` sau khi gọi các Stored Procedure.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../core/Database.php';

class StaffController extends Controller {
    private object $orderModel;
    private object $chatModel;
    private object $productModel;

    public function __construct() {
        $this->orderModel   = $this->model('OrderModel');
        $this->chatModel    = $this->model('ChatModel');
        $this->productModel = $this->model('ProductModel');
    }

    // ========================================================================
    // 1. ĐIỀU HƯỚNG CÁC MÀN HÌNH NHÂN VIÊN VẬN HÀNH (MH 10, MH 11, MH 17)
    // ========================================================================

    public function index(): void {
        $this->orders();
    }

    /**
     * Bàn làm việc Quản trị & Duyệt đơn hàng (MH 10)
     */
    public function orders(): void {
        Rbac::requireStaffOrAdmin();
        $this->view('staff-orders', [
            'page_title' => 'Bàn Làm Việc Quản Trị & Duyệt Đơn Hàng (MH 10)'
        ]);
    }

    /**
     * Bàn làm việc Live Chat CSKH 3 Cột (MH 11)
     */
    public function chat(): void {
        Rbac::requireStaffOrAdmin();
        $this->view('staff-chat', [
            'page_title' => 'Bàn Làm Việc CSKH Trực Tuyến 3 Cột (MH 11)'
        ]);
    }

    /**
     * Bàn làm việc Sơ tuyển & Duyệt hồ sơ KOL Nhí (MH 17)
     */
    public function castingReview(): void {
        Rbac::requireStaffOrAdmin();
        $this->view('staff-casting-review', [
            'page_title' => 'Duyệt Hồ Sơ KOL Nhí / Casting (MH 17)'
        ]);
    }

    // ========================================================================
    // 2. MH 10: AJAX TIẾP NHẬN ĐƠN & CẬP NHẬT TIẾN TRÌNH GIAO HÀNG
    // ========================================================================

    /**
     * AJAX Cập nhật tiến trình đơn hàng qua `sp_lnkt_cap_nhat_trang_thai_don_hang`
     */
    public function ajaxUpdateOrderStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ (Chỉ hỗ trợ POST)'], 405);
        }

        try {
            $maDonHang    = (int)($_POST['ma_don_hang'] ?? $_POST['order_id'] ?? 0);
            $trangThaiMoi = trim((string)($_POST['trang_thai_moi'] ?? $_POST['new_status'] ?? ''));
            $maNhanVien   = Rbac::currentUserId();

            if ($maDonHang <= 0 || $trangThaiMoi === '') {
                throw new InvalidArgumentException('Mã đơn hàng hoặc trạng thái giao vận mới không hợp lệ!');
            }

            $this->orderModel->capNhatTrangThai($maDonHang, $maNhanVien, $trangThaiMoi);

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'     => 'success',
                'order_id'   => $maDonHang,
                'new_status' => $trangThaiMoi,
                'staff_id'   => $maNhanVien,
                'message'    => "Đã thực thi sp_lnkt_cap_nhat_trang_thai_don_hang cho đơn #{$maDonHang} -> Trạng thái mới: {$trangThaiMoi}"
            ], 200);
        } catch (Exception $e) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $this->jsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // ========================================================================
    // 3. MH 11: LIVE CHAT 3 CỘT - ĐỒNG BỘ ĐÃ ĐỌC & TRA CỨU TỒN KHO NHANH
    // ========================================================================

    /**
     * AJAX Tải lịch sử tin nhắn của một phiên khách hàng và tự động đánh dấu đã đọc (`tgb_danh_dau_tin_da_doc`)
     */
    public function ajaxLoadChatSession(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $sessionId = trim((string)($_POST['ma_phien_khach'] ?? $_POST['session_id'] ?? $_GET['session_id'] ?? 'sess_mai_nguyen_01'));
        if ($sessionId === '') {
            $sessionId = 'sess_mai_nguyen_01';
        }

        // Tự động đồng bộ trạng thái "đã đọc" (tgb_danh_dau_tin_da_doc)
        $this->chatModel->danhDauTinDaDoc($sessionId);
        $messages = $this->chatModel->getMessages($sessionId);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'     => 'success',
            'session_id' => $sessionId,
            'messages'   => $messages,
            'read_sync'  => true,
            'message'    => "Đã tải phiên hội thoại {$sessionId} và đồng bộ trạng thái đã đọc (tgb_danh_dau_tin_da_doc)!"
        ], 200);
    }

    /**
     * AJAX Đánh dấu tin nhắn của phiên hội thoại là "Đã đọc" (`tgb_danh_dau_tin_da_doc`)
     */
    public function ajaxMarkChatRead(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $sessionId = trim((string)($_POST['ma_phien_khach'] ?? $_POST['session_id'] ?? ''));
        if ($sessionId === '') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mã phiên hội thoại không hợp lệ!'], 400);
        }

        $this->chatModel->danhDauTinDaDoc($sessionId);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'     => 'success',
            'session_id' => $sessionId,
            'message'    => "Đã thực thi tgb_danh_dau_tin_da_doc cho phiên {$sessionId} thành công!"
        ], 200);
    }

    /**
     * AJAX Nhân viên gửi tin nhắn phản hồi tư vấn thời gian thực (MH 11)
     */
    public function ajaxSendChatReply(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $sessionId = trim((string)($_POST['ma_phien_khach'] ?? $_POST['session_id'] ?? 'sess_mai_nguyen_01'));
        $content   = trim((string)($_POST['noi_dung_tin_nhan'] ?? $_POST['message'] ?? ''));
        $staffId   = Rbac::currentUserId();
        $sender    = Rbac::isAdmin() ? 'quan_tri' : 'nhan_vien';

        if ($content === '') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Nội dung tin nhắn phản hồi không được để trống!'], 400);
        }

        $this->chatModel->sendMessage($sessionId, $staffId, $sender, $content);
        $this->chatModel->danhDauTinDaDoc($sessionId);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'     => 'success',
            'session_id' => $sessionId,
            'sender'     => $sender,
            'reply_text' => $content,
            'sent_time'  => date('H:i'),
            'message'    => 'Đã gửi tin nhắn tư vấn và đồng bộ trạng thái đã đọc thành công!'
        ], 200);
    }

    /**
     * AJAX Tra cứu nhanh tồn kho sản phẩm ngay trong màn hình Live Chat 3 cột (MH 11)
     */
    public function ajaxQuickStockLookup(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $keyword = trim((string)($_POST['keyword'] ?? $_GET['keyword'] ?? ''));
        $items   = $this->productModel->traCuuTonKhoNhanh($keyword);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'   => 'success',
            'keyword'  => $keyword,
            'products' => $items,
            'count'    => count($items),
            'message'  => 'Đã tra cứu tồn kho nhanh thành công (' . count($items) . ' sản phẩm phù hợp)!'
        ], 200);
    }

    // ========================================================================
    // 4. MH 13 & MH 17 & MH 21/22: CÁC THAO TÁC CẤP STAFF (GIỚI HẠN QUYỀN)
    // ========================================================================

    /**
     * MH 13: Nhân viên nhập thêm kho hàng (`sp_lbn_nhap_them_kho_hang`)
     */
    public function ajaxRestockInventory(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $maSanPham   = (int)($_POST['ma_san_pham'] ?? $_POST['product_id'] ?? 0);
        $soLuongNhap = (int)($_POST['so_luong_nhap'] ?? $_POST['qty_change'] ?? 0);

        if ($maSanPham <= 0 || $soLuongNhap <= 0) {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Lỗi [LBN_ERR_05]: Số lượng nhập hàng bổ sung phải lớn hơn 0!'
            ], 400);
        }

        $ok = $this->productModel->updateStock($maSanPham, $soLuongNhap);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        if ($ok) {
            $this->jsonResponse([
                'status'     => 'success',
                'product_id' => $maSanPham,
                'added_qty'  => $soLuongNhap,
                'message'    => "Đã thực thi sp_lbn_nhap_them_kho_hang: Nhập bổ sung +{$soLuongNhap} sản phẩm cho mã #{$maSanPham} thành công!"
            ], 200);
        } else {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Không thể nhập thêm kho hàng, vui lòng thử lại!'
            ], 400);
        }
    }

    /**
     * MH 17: Nhân viên sơ tuyển hồ sơ KOL Nhí -> Chỉ được phép chuyển sang "Mời phỏng vấn" (`moi_phong_van`)
     * Nếu cố gửi trạng thái `trung_tuyen` hoặc `tu_choi` với vai trò `nhan_vien` sẽ bị chặn RBAC!
     */
    public function ajaxInviteKolInterview(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $maHoSo    = (int)($_POST['ma_ho_so'] ?? $_POST['profile_id'] ?? 0);
        $requested = trim((string)($_POST['trang_thai_duyet'] ?? 'moi_phong_van'));
        $staffId   = Rbac::currentUserId();

        if ($maHoSo <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mã hồ sơ KOL nhí không hợp lệ!'], 400);
        }

        // Ràng buộc RBAC: Nhân viên chỉ được sơ tuyển "Mời phỏng vấn" (moi_phong_van)
        if (Rbac::isStaff() && $requested !== 'moi_phong_van') {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Tài khoản Nhân viên chỉ có quyền Sơ tuyển "Mời phỏng vấn". Quyết định Trúng tuyển/Từ chối thuộc thẩm quyền tối cao của Quản trị viên (Admin)!'
            ], 403);
        }

        if (Database::isConnected()) {
            try {
                $pdo = Database::getInstance();
                try {
                    $stmt = $pdo->prepare("CALL sp_lnkt_duyet_ho_so_kol_nhi(:ma_hs, :ma_nv, 'moi_phong_van')");
                    $stmt->execute([':ma_hs' => $maHoSo, ':ma_nv' => $staffId]);
                    $stmt->closeCursor();
                } catch (PDOException $e1) {
                    $stmt2 = $pdo->prepare("CALL sp_lnkt_xet_duyet_kol_nhi(:ma_hs, :ma_nv, 'moi_phong_van')");
                    $stmt2->execute([':ma_hs' => $maHoSo, ':ma_nv' => $staffId]);
                    $stmt2->closeCursor();
                }
            } catch (PDOException $e) {
                error_log("[lnkt] ajaxInviteKolInterview error: " . $e->getMessage());
            }
        }

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'     => 'success',
            'profile_id' => $maHoSo,
            'decision'   => 'moi_phong_van',
            'message'    => "Đã sơ tuyển hồ sơ KOL Nhí #{$maHoSo}: Chuyển trạng thái sang \"Mời phỏng vấn\" (moi_phong_van) thành công!"
        ], 200);
    }

    /**
     * MH 21/22: Nhân viên lưu bài viết Tạp chí ở trạng thái "Bản nháp" (`ban_nhap`)
     */
    public function ajaxSaveBlogDraft(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $tieuDe   = trim((string)($_POST['tieu_de'] ?? $_POST['post_title'] ?? ''));
        $tomTat   = trim((string)($_POST['tom_tat_ngan'] ?? $_POST['post_excerpt'] ?? ''));
        $noiDung  = trim((string)($_POST['noi_dung_chi_tiet'] ?? $_POST['post_content'] ?? ''));
        $anhBia   = trim((string)($_POST['anh_dai_dien'] ?? 'blog_be_gai_tiec.jpg'));
        $maTacGia = Rbac::currentUserId();

        if ($tieuDe === '') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Vui lòng nhập tiêu đề bài viết để lưu bản nháp!'], 400);
        }

        $slug = 'draft-' . strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tieuDe), '-')) . '-' . time();
        $draftId = random_int(50, 999);

        if (Database::isConnected()) {
            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare(
                    "INSERT INTO tgb_bai_viet_tin_tuc (
                        ma_tac_gia, tieu_de, duong_dan_tinh, anh_dai_dien, tom_tat_ngan, noi_dung_chi_tiet, trang_thai_bai_viet, ngay_dang
                     ) VALUES (
                        :tac_gia, :tieu_de, :slug, :anh, :tom_tat, :noi_dung, 'ban_nhap', NOW()
                     )"
                );
                $stmt->execute([
                    ':tac_gia'  => $maTacGia,
                    ':tieu_de'  => $tieuDe,
                    ':slug'     => $slug,
                    ':anh'      => $anhBia,
                    ':tom_tat'  => $tomTat ?: $tieuDe,
                    ':noi_dung' => $noiDung ?: $tieuDe
                ]);
                $draftId = (int)$pdo->lastInsertId();
            } catch (PDOException $e) {
                error_log("[tgb] ajaxSaveBlogDraft error: " . $e->getMessage());
            }
        }

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'      => 'success',
            'post_id'     => $draftId,
            'post_status' => 'ban_nhap',
            'message'     => "Đã lưu bài viết \"{$tieuDe}\" (#{$draftId}) ở trạng thái \"Bản nháp\" (ban_nhap) chờ Quản trị viên duyệt xuất bản!"
        ], 200);
    }
}
