<?php
/**
 * Lớp Model Quản Lý Đơn Hàng & Duyệt KOL Nhí (models/OrderManagementModel.php)
 * Phụ trách nhánh: feature/lnkt-order-size (Lê Nguyễn Khánh Trình - lnkt_)
 * Làm việc với các bảng: lnkt_don_hang, lnkt_dang_ky_kol_nhi, lnkt_banner_su_kien
 * Tích hợp Stored Procedures: sp_lnkt_cap_nhat_trang_thai_don_hang, sp_lnkt_xet_duyet_kol_nhi
 */

require_once __DIR__ . '/OrderModel.php';

class OrderManagementModel extends OrderModel {

    /**
     * Xét duyệt hồ sơ KOL nhí qua sp_lnkt_xet_duyet_kol_nhi
     */
    public function xetDuyetHoSoKol(int $maHoSo, int $maNhanVien, string $trangThaiDuyet): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lnkt_xet_duyet_kol_nhi(:ma_hs, :ma_nv, :trang_thai)");
                $stmt->execute([
                    ':ma_hs'      => $maHoSo,
                    ':ma_nv'      => $maNhanVien,
                    ':trang_thai' => $trangThaiDuyet,
                ]);
                $stmt->closeCursor();
                return true;
            } catch (PDOException $e) {
                error_log("Lỗi xét duyệt KOL nhí qua sp_lnkt_xet_duyet_kol_nhi: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Lấy danh sách banner sự kiện đang kích hoạt từ bảng lnkt_banner_su_kien
     */
    public function layDanhSachBannerKichHoat(): array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->query("SELECT * FROM lnkt_banner_su_kien WHERE dang_kich_hoat = 1 ORDER BY thu_tu_hien_thi ASC");
                return $stmt->fetchAll();
            } catch (PDOException $e) {}
        }
        return [];
    }
}
