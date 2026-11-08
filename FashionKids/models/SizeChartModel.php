<?php
/**
 * Lớp Model Bảng Kích Cỡ (models/SizeChartModel.php)
 * Đảm nhiệm: lnkt_ (Lê Nguyễn Khánh Trình)
 * Khớp chuẩn 100% với CSDL phpMyAdmin (lnkt_bang_kich_co)
 * Tích hợp Stored Function: fn_lnkt_goi_y_kich_co_chuan
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/entities/BangKichCo.php';

class SizeChartModel extends Model {

    /**
     * Gọi Stored Function fn_lnkt_goi_y_kich_co_chuan đối chiếu bảng size
     */
    public function goiYSizeChuan(
        int $maDanhMuc,
        float $chieuCao,
        ?float $vongNguc = null,
        ?float $vongEo = null,
        ?float $daiChan = null
    ): string {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT fn_lnkt_goi_y_kich_co_chuan(:ma_dm, :cao, :nguc, :eo, :chan) AS size_goi_y");
                $stmt->execute([
                    ':ma_dm' => $maDanhMuc,
                    ':cao'   => $chieuCao,
                    ':nguc'  => $vongNguc,
                    ':eo'    => $vongEo,
                    ':chan'  => $daiChan
                ]);
                $res = $stmt->fetch();
                if (!empty($res['size_goi_y'])) {
                    return $res['size_goi_y'];
                }
            } catch (PDOException $e) {
                error_log("Lỗi gọi Stored Function fn_lnkt_goi_y_kich_co_chuan: " . $e->getMessage());
            }
        }

        // Fallback thuật toán nếu MySQL chưa khởi động
        if ($maDanhMuc === 4) {
            $val = $daiChan ?: 14.5;
            if ($val <= 14.9) return 'Size 24';
            if ($val <= 15.9) return 'Size 26';
            return 'Size 28';
        } else {
            $val = $chieuCao ?: 88.0;
            if ($val < 90) return 'Size 1 (1-2T)';
            if ($val <= 100) return 'Size 2 (2-3T)';
            if ($val <= 110) return 'Size 3 (3-4T)';
            return 'Size 4 (4-5T)';
        }
    }

    public function getAllCharts(): array {
        if ($this->hasConnection()) {
            try {
                $sql = "SELECT bc.*, dm.ten_danh_muc 
                        FROM lnkt_bang_kich_co bc 
                        JOIN lbn_danh_muc dm ON bc.ma_danh_muc = dm.ma_danh_muc 
                        ORDER BY bc.ma_danh_muc ASC, bc.chieu_cao_toi_thieu ASC";
                return $this->db->query($sql)->fetchAll();
            } catch (PDOException $e) {}
        }
        return [];
    }

    public function getByCategory(int $categoryId): array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM lnkt_bang_kich_co WHERE ma_danh_muc = :cid ORDER BY chieu_cao_toi_thieu ASC");
                $stmt->execute([':cid' => $categoryId]);
                return $stmt->fetchAll();
            } catch (PDOException $e) {}
        }
        return [];
    }
}
