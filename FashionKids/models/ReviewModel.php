<?php
/**
 * Lớp Model Đánh Giá Sản Phẩm & Tạp Chí (models/ReviewModel.php)
 * Phụ trách nhánh: feature/tgb-checkout-ajax (Trần Gia Bảo - tgb_)
 * Làm việc với các bảng: tgb_danh_gia_san_pham, tgb_tep_tin_danh_gia, tgb_luot_thich_danh_gia
 * Tích hợp Stored Routines: sp_tgb_gui_danh_gia_san_pham, fn_tgb_tinh_sao_trung_binh_san_pham
 */

require_once __DIR__ . '/../core/Model.php';

class ReviewModel extends Model {

    /**
     * Gửi đánh giá sản phẩm qua sp_tgb_gui_danh_gia_san_pham
     */
    public function guiDanhGiaSanPham(
        int $maSanPham,
        int $maNguoiDung,
        int $maDonHang,
        int $soSao,
        string $noiDung
    ): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_tgb_gui_danh_gia_san_pham(:sp, :user, :order, :star, :content)");
                $stmt->execute([
                    ':sp'      => $maSanPham,
                    ':user'    => $maNguoiDung,
                    ':order'   => $maDonHang,
                    ':star'    => $soSao,
                    ':content' => trim($noiDung),
                ]);
                $stmt->closeCursor();
                return true;
            } catch (PDOException $e) {
                $msg = preg_replace('/^SQLSTATE\[[^\]]+\]:.*?:\s*\d+\s*/u', '', $e->getMessage());
                throw new RuntimeException(trim((string)$msg) ?: 'Không thể gửi đánh giá sản phẩm.', 0, $e);
            }
        }
        return true;
    }

    /**
     * Kiểm tra khách hàng có đơn hàng đã HOÀN TẤT chứa sản phẩm này mà chưa đánh giá hay không
     */
    public function kiemTraQuyenDanhGia(int $maNguoiDung, int $maSanPham): array {
        $result = [
            'eligible'          => false,
            'already_reviewed'  => false,
            'eligible_order_id' => 0,
        ];
        if (!$this->hasConnection() || $maNguoiDung <= 0 || $maSanPham <= 0) {
            return $result;
        }
        try {
            // 1. Tìm đơn hoàn tất chứa sản phẩm này nhưng chưa từng đánh giá
            $stmt = $this->db->prepare("
                SELECT dh.ma_don_hang
                FROM lnkt_don_hang dh
                JOIN tgb_chi_tiet_don_hang ct ON dh.ma_don_hang = ct.ma_don_hang
                LEFT JOIN tgb_danh_gia_san_pham dg
                    ON dg.ma_don_hang = dh.ma_don_hang
                   AND dg.ma_san_pham = ct.ma_san_pham
                   AND dg.ma_nguoi_dung = dh.ma_nguoi_dung
                WHERE dh.ma_nguoi_dung = :uid
                  AND dh.trang_thai_don_hang = 'hoan_tat'
                  AND ct.ma_san_pham = :pid
                  AND dg.ma_danh_gia IS NULL
                ORDER BY dh.ma_don_hang DESC
                LIMIT 1
            ");
            $stmt->execute([':uid' => $maNguoiDung, ':pid' => $maSanPham]);
            $row = $stmt->fetch();
            $stmt->closeCursor();

            if ($row && !empty($row['ma_don_hang'])) {
                $result['eligible'] = true;
                $result['eligible_order_id'] = (int)$row['ma_don_hang'];
                return $result;
            }

            // 2. Kiểm tra xem có phải đã đánh giá hết các đơn đã mua rồi không
            $revStmt = $this->db->prepare("
                SELECT 1
                FROM tgb_danh_gia_san_pham
                WHERE ma_nguoi_dung = :uid AND ma_san_pham = :pid
                LIMIT 1
            ");
            $revStmt->execute([':uid' => $maNguoiDung, ':pid' => $maSanPham]);
            $result['already_reviewed'] = (bool)$revStmt->fetch();
            $revStmt->closeCursor();
        } catch (PDOException $e) {
            error_log('[tgb] kiemTraQuyenDanhGia: ' . $e->getMessage());
        }
        return $result;
    }

    /**
     * Tính điểm sao trung bình của sản phẩm qua fn_tgb_tinh_sao_trung_binh_san_pham
     */
    public function tinhSaoTrungBinh(int $maSanPham): float {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT fn_tgb_tinh_sao_trung_binh_san_pham(:sp) AS diem_sao");
                $stmt->execute([':sp' => $maSanPham]);
                $row = $stmt->fetch();
                $stmt->closeCursor();
                return (float)($row['diem_sao'] ?? 5.0);
            } catch (PDOException $e) {
                error_log("Lỗi tính sao trung bình: " . $e->getMessage());
            }
        }
        return 5.0;
    }

    /**
     * Lấy danh sách đánh giá của một sản phẩm từ bảng tgb_danh_gia_san_pham
     */
    public function layDanhGiaTheoSanPham(int $maSanPham): array {
        if ($this->hasConnection()) {
            try {
                $sql = "SELECT dg.*, nd.ho_va_ten
                        FROM tgb_danh_gia_san_pham dg
                        JOIN hgb_nguoi_dung nd ON dg.ma_nguoi_dung = nd.ma_nguoi_dung
                        WHERE dg.ma_san_pham = :sp
                        ORDER BY dg.ngay_danh_gia DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':sp' => $maSanPham]);
                return $stmt->fetchAll();
            } catch (PDOException $e) {}
        }
        return [];
    }
}
