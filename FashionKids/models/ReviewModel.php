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
        }
        return true;
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
