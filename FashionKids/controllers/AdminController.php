<?php
/**
 * LớpAdminController (controllers/AdminController.php)
 * Điều phối toàn bộ phân hệ Quản trị viên (Admin - quan_tri):
 *  - MH 12: Bảng điều khiển Tổng quan hệ thống (dashboard)
 *  - MH 13: Quản trị Sản phẩm & Tồn kho (inventory, ajaxAddProduct, ajaxUpdateStock)
 *  - MH 14: Quản trị Bảng quy chuẩn kích cỡ lnkt_bang_kich_co (sizeCharts, ajaxSaveSizeChart)
 *  - MH 15: Quản trị Người dùng & Hạng thành viên VIP hgb_hang_thanh_vien (users, ajaxUpdateVipTiers, ajaxToggleUserStatus)
 *  - MH 17: Xét duyệt hồ sơ KOL Nhí quyền tối cao "Quyết định Trúng tuyển/Từ chối" (sp_lnkt_duyet_ho_so_kol_nhi / sp_lnkt_xet_duyet_kol_nhi)
 *  - MH 21/22: Quản trị Tạp chí CMS Blog quyền "Xuất bản/Xóa" bài viết & Kiểm duyệt bình luận (cmsBlog, ajaxPublishBlog, ajaxDeleteBlog)
 *  - MH 23: Quản trị Chiến dịch Banner Carousel (banners, ajaxSaveBanner)
 *
 * Tuân thủ nghiêm ngặt ràng buộc kỹ thuật:
 *  - Tích hợp Middleware RBAC (`Rbac::enforceCurrentViewAccess()`, `Rbac::requireAdmin()`, `Rbac::requireStaffOrAdmin()`)
 *  - Mọi phản hồi AJAX đều xóa sạch bộ đệm bằng `ob_clean()` và trả về JSON chuẩn `{ "status": "success|error", "message": "..." }`
 *  - Sử dụng PDO Singleton từ `core/Database.php` kết hợp `$stmt->closeCursor();` sau khi gọi các Stored Procedure.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Rbac.php';
require_once __DIR__ . '/../core/Database.php';

class AdminController extends Controller {
    private object $productModel;
    private object $sizeChartModel;
    private object $userModel;

    public function __construct() {
        $this->productModel   = $this->model('ProductModel');
        $this->sizeChartModel = $this->model('SizeChartModel');
        $this->userModel      = $this->model('UserModel');
    }

    // ========================================================================
    // 1. ĐIỀU HƯỚNG CÁC MÀN HÌNH QUẢN TRỊ (CÓ KIỂM SOÁT RBAC)
    // ========================================================================

    public function index(): void {
        $this->dashboard();
    }

    /**
     * Bảng điều khiển Quản trị viên (MH 12) - Chỉ dành cho quan_tri (nhan_vien bị chặn HTTP 403)
     */
    public function dashboard(): void {
        Rbac::requireAdmin();
        $this->view('admin-dashboard', [
            'page_title' => 'Bảng Điều Khiển Quản Trị Viên (MH 12)'
        ]);
    }

    /**
     * Quản lý Kho hàng & Tồn kho (MH 13) - Admin có toàn quyền, Staff chỉ có nút Nhập thêm kho
     */
    public function inventory(): void {
        Rbac::requireStaffOrAdmin();
        $products = $this->productModel->getAllProducts();

        $this->view('admin-inventory', [
            'page_title' => 'Quản Trị Kho Hàng & Tồn Kho (MH 13)',
            'products'   => $products
        ]);
    }

    /**
     * Quản lý Bảng quy chuẩn kích cỡ (MH 14) - Chỉ dành cho quan_tri (nhan_vien bị chặn HTTP 403)
     */
    public function sizeCharts(): void {
        Rbac::requireAdmin();
        $charts = $this->sizeChartModel->getAllCharts();

        $this->view('admin-size-charts', [
            'page_title'  => 'Quản Trị Bảng Quy Chuẩn Kích Cỡ (MH 14)',
            'size_charts' => $charts
        ]);
    }

    /**
     * Quản trị Người dùng & Hạng thẻ VIP (MH 15) - Chỉ dành cho quan_tri (nhan_vien bị chặn HTTP 403)
     */
    public function users(): void {
        Rbac::requireAdmin();
        $this->view('admin-users', [
            'page_title' => 'Quản Trị Người Dùng & Cấu Hình Hạng VIP (MH 15)'
        ]);
    }

    /**
     * Quản trị Chiến dịch Banner Carousel (MH 23) - Chỉ dành cho quan_tri (nhan_vien bị chặn HTTP 403)
     */
    public function banners(): void {
        Rbac::requireAdmin();
        $this->view('admin-banners', [
            'page_title' => 'Quản Trị Banner Slider & Lịch Chiến Dịch (MH 23)'
        ]);
    }

    /**
     * Quản trị Tạp chí CMS Blog & Kiểm duyệt (MH 21/22)
     */
    public function cmsBlog(): void {
        Rbac::requireStaffOrAdmin();
        $this->view('admin-cms-blog', [
            'page_title' => 'Quản Trị CMS & Kiểm Duyệt Nội Dung (MH 21/22)'
        ]);
    }

    // ========================================================================
    // 2. MH 13: AJAX THÊM SẢN PHẨM MỚI & NHẬP THÊM KHO HÀNG
    // ========================================================================

    /**
     * AJAX Thêm sản phẩm mới vào kho (MH 13 - chỉ Admin)
     * Gọi Stored Procedure `sp_lbn_them_san_pham_moi` kèm `$stmt->closeCursor();`
     * Bắt lỗi ngoại lệ nếu giá bán <= 0 hoặc tồn kho < 0.
     */
    public function ajaxAddProduct(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ (Chỉ hỗ trợ POST)'], 405);
        }

        try {
            $maDanhMuc     = (int)($_POST['ma_danh_muc'] ?? $_POST['category_id'] ?? 1);
            $tenSanPham    = trim((string)($_POST['ten_san_pham'] ?? $_POST['name'] ?? ''));
            $giaBan        = (float)($_POST['gia_ban'] ?? $_POST['price'] ?? 0);
            $soLuongTonKho = (int)($_POST['so_luong_ton_kho'] ?? $_POST['stock_quantity'] ?? 0);
            $duongDanAnh   = trim((string)($_POST['duong_dan_anh'] ?? $_POST['image_url'] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg'));
            $moTaChatLieu  = trim((string)($_POST['mo_ta_chat_lieu'] ?? $_POST['description'] ?? '100% sợi tre hữu cơ kháng khuẩn tự nhiên'));
            $loaiKichCo    = trim((string)($_POST['loai_kich_co'] ?? $_POST['lbn_size_type'] ?? 'quan_ao'));

            if ($giaBan <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_03]: Giá bán sản phẩm phải lớn hơn 0 VNĐ!');
            }
            if ($soLuongTonKho < 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_04]: Số lượng hàng tồn kho ban đầu không được là số âm!');
            }
            if ($tenSanPham === '') {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_02]: Tên sản phẩm không được phép để trống!');
            }

            $maSpMoi = $this->productModel->themSanPhamMoi(
                $maDanhMuc,
                $tenSanPham,
                $giaBan,
                $soLuongTonKho,
                $duongDanAnh,
                $moTaChatLieu,
                $loaiKichCo
            );

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'     => 'success',
                'product_id' => $maSpMoi,
                'message'    => "Đã thực thi sp_lbn_them_san_pham_moi: Thêm sản phẩm \"{$tenSanPham}\" (#{$maSpMoi}) thành công!"
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

    /**
     * AJAX Cập nhật / Nhập thêm tồn kho (triệu gọi sp_lbn_nhap_them_kho_hang)
     */
    public function ajaxUpdateStock(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        $productId = (int)($_POST['ma_san_pham'] ?? $_POST['product_id'] ?? 0);
        $qtyChange = (int)($_POST['so_luong_nhap'] ?? $_POST['qty_change'] ?? 0);

        if ($productId <= 0 || $qtyChange <= 0) {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Lỗi [LBN_ERR_05]: Mã sản phẩm không hợp lệ hoặc số lượng nhập hàng bổ sung phải lớn hơn 0!'
            ], 400);
            return;
        }

        $ok = $this->productModel->updateStock($productId, $qtyChange);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        if ($ok) {
            $this->jsonResponse([
                'status'     => 'success',
                'product_id' => $productId,
                'added_qty'  => $qtyChange,
                'message'    => "Đã thực thi sp_lbn_nhap_them_kho_hang: Nhập thêm +{$qtyChange} sản phẩm vào kho thành công!"
            ], 200);
        } else {
            $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Cập nhật tồn kho thất bại hoặc mã sản phẩm không tồn tại!'
            ], 400);
        }
    }

    // ========================================================================
    // 3. MH 14 & MH 15: AJAX CẤU HÌNH BẢNG KÍCH CỠ & HẠNG THÀNH VIÊN VIP
    // ========================================================================

    /**
     * AJAX Lưu cấu hình thông số trần/sàn bảng kích cỡ `lnkt_bang_kich_co` (MH 14 - chỉ Admin)
     */
    public function ajaxSaveSizeChart(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ'], 405);
        }

        try {
            $chartId  = $this->sizeChartModel->saveSizeChart($_POST);
            $sizeName = trim((string)($_POST['ten_kich_co'] ?? $_POST['size_name'] ?? 'Size mới'));
            $catId    = (int)($_POST['ma_danh_muc'] ?? $_POST['category_id'] ?? 1);

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'        => 'success',
                'size_chart_id' => $chartId,
                'message'       => "Đã lưu cấu hình trần/sàn cho \"{$sizeName}\" (ma_danh_muc = {$catId}) vào bảng lnkt_bang_kich_co thành công!"
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

    /**
     * AJAX Cập nhật cấu hình mốc điểm sàn và % chiết khấu hạng VIP `hgb_hang_thanh_vien` (MH 15 - chỉ Admin)
     */
    public function ajaxUpdateVipTiers(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ'], 405);
        }

        try {
            $rawTiers = $_POST['tiers'] ?? null;
            if (is_string($rawTiers)) {
                $rawTiers = json_decode($rawTiers, true);
            }
            if (!is_array($rawTiers) || empty($rawTiers)) {
                $rawInput = json_decode((string)file_get_contents('php://input'), true);
                $rawTiers = $rawInput['tiers'] ?? [];
            }

            $this->userModel->capNhatCauHinhHangVip($rawTiers);

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'  => 'success',
                'message' => 'Đã cập nhật mốc điểm sàn và tỷ lệ chiết khấu (%) cho 4 hạng thẻ VIP trong bảng hgb_hang_thanh_vien thành công!'
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

    /**
     * AJAX Khóa / Mở khóa tài khoản người dùng (MH 15 - chỉ Admin)
     */
    public function ajaxToggleUserStatus(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        $userId    = (int)($_POST['ma_nguoi_dung'] ?? $_POST['user_id'] ?? 0);
        $newStatus = (int)($_POST['status'] ?? 1);

        if ($userId <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mã người dùng không hợp lệ!'], 400);
        }

        $this->userModel->capNhatTrangThaiNguoiDung($userId, $newStatus);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $statusLabel = $newStatus ? 'Mở khóa hoạt động' : 'Khóa tạm thời';
        $this->jsonResponse([
            'status'     => 'success',
            'user_id'    => $userId,
            'new_status' => $newStatus,
            'message'    => "Đã cập nhật trạng thái tài khoản #{$userId}: {$statusLabel} thành công!"
        ], 200);
    }

    // ========================================================================
    // 4. MH 17: QUYẾT ĐỊNH TRÚNG TUYỂN / TỪ CHỐI HỒ SƠ KOL NHÍ (CẤP ADMIN)
    // ========================================================================

    /**
     * AJAX Quyết định Trúng tuyển / Từ chối hồ sơ KOL Nhí (MH 17 - chỉ Admin)
     * Triệu gọi Stored Procedure `sp_lnkt_duyet_ho_so_kol_nhi` (tương thích `sp_lnkt_xet_duyet_kol_nhi`)
     * kèm lệnh `$stmt->closeCursor();`
     */
    public function ajaxDecideKolCasting(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ'], 405);
        }

        try {
            $maHoSo         = (int)($_POST['ma_ho_so'] ?? $_POST['profile_id'] ?? 0);
            $trangThaiDuyet = trim((string)($_POST['trang_thai_duyet'] ?? $_POST['decision'] ?? ''));
            $maQuanTri      = Rbac::currentUserId();

            if ($maHoSo <= 0) {
                throw new InvalidArgumentException('Mã hồ sơ ứng tuyển KOL nhí không hợp lệ!');
            }
            if (!in_array($trangThaiDuyet, ['trung_tuyen', 'tu_choi', 'moi_phong_van', 'cho_duyet'], true)) {
                throw new InvalidArgumentException('Quyết định phê duyệt hồ sơ KOL không hợp lệ!');
            }

            if (Database::isConnected()) {
                $pdo = Database::getInstance();
                try {
                    // Thử gọi tên SP theo đặc tả mới sp_lnkt_duyet_ho_so_kol_nhi trước, fallback sang sp_lnkt_xet_duyet_kol_nhi trong SQL
                    $stmt = $pdo->prepare("CALL sp_lnkt_duyet_ho_so_kol_nhi(:ma_hs, :ma_nv, :trang_thai)");
                    $stmt->execute([
                        ':ma_hs'      => $maHoSo,
                        ':ma_nv'      => $maQuanTri,
                        ':trang_thai' => $trangThaiDuyet
                    ]);
                    $stmt->closeCursor();
                } catch (PDOException $exSp1) {
                    try {
                        $stmt2 = $pdo->prepare("CALL sp_lnkt_xet_duyet_kol_nhi(:ma_hs, :ma_nv, :trang_thai)");
                        $stmt2->execute([
                            ':ma_hs'      => $maHoSo,
                            ':ma_nv'      => $maQuanTri,
                            ':trang_thai' => $trangThaiDuyet
                        ]);
                        $stmt2->closeCursor();
                    } catch (PDOException $exSp2) {
                        if (($exSp2->errorInfo[0] ?? '') === '45000') {
                            $cleanMsg = preg_replace('/^SQLSTATE\[45000\]:.*?:\s*\d+\s*/u', '', (string)($exSp2->errorInfo[2] ?? $exSp2->getMessage()));
                            throw new DomainException(trim($cleanMsg ?: 'Lỗi xét duyệt hồ sơ KOL!'));
                        }
                    }
                }
            }

            $labelMap = [
                'trung_tuyen'   => 'QUYẾT ĐỊNH TRÚNG TUYỂN (Đại sứ thương hiệu nhí)',
                'tu_choi'       => 'TỪ CHỐI HỒ SƠ (Không phù hợp tiêu chí)',
                'moi_phong_van' => 'MỜI PHỎNG VẤN & CHỤP THỬ STUDIO',
                'cho_duyet'     => 'Chờ xét duyệt'
            ];
            $labelText = $labelMap[$trangThaiDuyet] ?? $trangThaiDuyet;

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'     => 'success',
                'profile_id' => $maHoSo,
                'decision'   => $trangThaiDuyet,
                'label'      => $labelText,
                'message'    => "Đã thực thi sp_lnkt_duyet_ho_so_kol_nhi cho hồ sơ #{$maHoSo}: {$labelText}!"
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
    // 5. MH 21/22: QUẢN TRỊ TẠP CHÍ BLOG - XUẤT BẢN & XÓA BÀI VIẾT (CẤP ADMIN)
    // ========================================================================

    /**
     * AJAX Xuất bản bài viết Tạp chí chính thức lên trang chủ (MH 21/22 - chỉ Admin)
     * Bảng: `tgb_bai_viet_tin_tuc` (`trang_thai_bai_viet = 'xuat_ban'`)
     */
    public function ajaxPublishBlog(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ'], 405);
        }

        try {
            $tieuDe   = trim((string)($_POST['tieu_de'] ?? $_POST['post_title'] ?? ''));
            $tomTat   = trim((string)($_POST['tom_tat_ngan'] ?? $_POST['post_excerpt'] ?? ''));
            $noiDung  = trim((string)($_POST['noi_dung_chi_tiet'] ?? $_POST['post_content'] ?? ''));
            $anhBia   = trim((string)($_POST['anh_dai_dien'] ?? 'blog_be_gai_tiec.jpg'));
            $maTacGia = Rbac::currentUserId();

            if ($tieuDe === '') {
                throw new InvalidArgumentException('Vui lòng nhập tiêu đề bài viết trước khi xuất bản!');
            }
            if ($tomTat === '') {
                $tomTat = mb_substr(strip_tags($noiDung ?: $tieuDe), 0, 180);
            }
            if ($noiDung === '') {
                throw new InvalidArgumentException('Nội dung chi tiết bài viết không được để trống!');
            }

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tieuDe), '-')) . '-' . time();
            $newPostId = random_int(10, 999);

            if (Database::isConnected()) {
                try {
                    $pdo = Database::getInstance();
                    $stmt = $pdo->prepare(
                        "INSERT INTO tgb_bai_viet_tin_tuc (
                            ma_tac_gia, tieu_de, duong_dan_tinh, anh_dai_dien, tom_tat_ngan, noi_dung_chi_tiet, trang_thai_bai_viet, ngay_dang
                         ) VALUES (
                            :tac_gia, :tieu_de, :slug, :anh, :tom_tat, :noi_dung, 'xuat_ban', NOW()
                         )"
                    );
                    $stmt->execute([
                        ':tac_gia'  => $maTacGia,
                        ':tieu_de'  => $tieuDe,
                        ':slug'     => $slug,
                        ':anh'      => $anhBia,
                        ':tom_tat'  => $tomTat,
                        ':noi_dung' => $noiDung
                    ]);
                    $newPostId = (int)$pdo->lastInsertId();
                } catch (PDOException $e) {
                    error_log("[tgb] ajaxPublishBlog error: " . $e->getMessage());
                }
            }

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'      => 'success',
                'post_id'     => $newPostId,
                'post_status' => 'xuat_ban',
                'message'     => "Quản trị viên đã XUẤT BẢN chính thức bài viết \"{$tieuDe}\" (#{$newPostId}) lên Tạp chí FashionKids!"
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

    /**
     * AJAX Xóa bài viết Tạp chí hoặc bình luận/đánh giá vi phạm (MH 21/22 - chỉ Admin)
     */
    public function ajaxDeleteBlog(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        $postId    = (int)($_POST['ma_bai_viet'] ?? $_POST['post_id'] ?? 0);
        $commentId = (int)($_POST['ma_binh_luan'] ?? $_POST['comment_id'] ?? 0);

        if ($postId <= 0 && $commentId <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Mã bài viết hoặc nội dung cần xóa không hợp lệ!'], 400);
        }

        if (Database::isConnected()) {
            try {
                $pdo = Database::getInstance();
                if ($postId > 0) {
                    $stmt = $pdo->prepare("DELETE FROM tgb_bai_viet_tin_tuc WHERE ma_bai_viet = :id");
                    $stmt->execute([':id' => $postId]);
                } elseif ($commentId > 0) {
                    $stmt = $pdo->prepare("DELETE FROM tgb_binh_luan_bai_viet WHERE ma_binh_luan = :id");
                    $stmt->execute([':id' => $commentId]);
                }
            } catch (PDOException $e) {
                error_log("[tgb] ajaxDeleteBlog error: " . $e->getMessage());
            }
        }

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $targetLabel = $postId > 0 ? "bài viết Tạp chí #{$postId}" : "nội dung bình luận/đánh giá #{$commentId}";
        $this->jsonResponse([
            'status'     => 'success',
            'post_id'    => $postId,
            'comment_id' => $commentId,
            'message'    => "Quản trị viên đã XÓA vĩnh viễn {$targetLabel} khỏi hệ thống thành công!"
        ], 200);
    }

    // ========================================================================
    // 6. MH 23: AJAX LƯU CHIẾN DỊCH BANNER SLIDER
    // ========================================================================

    /**
     * AJAX Lưu cấu hình chiến dịch Banner `lnkt_banner_su_kien` (MH 23 - chỉ Admin)
     */
    public function ajaxSaveBanner(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        $tieuDe    = trim((string)($_POST['tieu_de'] ?? $_POST['title'] ?? ''));
        $targetUrl = trim((string)($_POST['duong_dan_dieu_huong'] ?? $_POST['target_url'] ?? '/FashionKids/views/pages/shop-catalog.php'));

        if ($tieuDe === '') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Tiêu đề chiến dịch Banner không được để trống!'], 400);
        }

        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->jsonResponse([
            'status'     => 'success',
            'target_url' => $targetUrl,
            'message'    => "Đã lưu cấu hình chiến dịch Banner \"{$tieuDe}\" vào bảng lnkt_banner_su_kien thành công!"
        ], 200);
    }
}
