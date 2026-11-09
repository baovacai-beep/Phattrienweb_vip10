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
        $catId,
        $height,
        $chest = null,
        $waist = null,
        $footLength = null
    ): string {
        $maDanhMuc = (int)$catId;
        $chieuCao  = (float)$height;
        $vongNguc  = ($chest !== null && $chest !== '') ? (float)$chest : null;
        $vongEo    = ($waist !== null && $waist !== '') ? (float)$waist : null;
        $daiChan   = ($footLength !== null && $footLength !== '') ? (float)$footLength : null;

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT fn_lnkt_goi_y_kich_co_chuan(:cat, :h, :c, :w, :f) AS size_chuan");
                $stmt->execute([
                    ':cat' => $maDanhMuc,
                    ':h'   => $chieuCao,
                    ':c'   => $vongNguc,
                    ':w'   => $vongEo,
                    ':f'   => $daiChan
                ]);
                $res = $stmt->fetch();
                $sizeChuan = $res['size_chuan'] ?? $res['size_goi_y'] ?? '';
                if (!empty($sizeChuan) && $sizeChuan !== 'Chưa có size phù hợp') {
                    return $sizeChuan;
                }
            } catch (PDOException $e) {
                error_log("Lỗi gọi Stored Function fn_lnkt_goi_y_kich_co_chuan: " . $e->getMessage());
            }
        }

        // Fallback thuật toán nếu MySQL chưa khởi động hoặc ngoài khoảng seed mẫu
        if ($maDanhMuc === 4) {
            $val = $daiChan ?: 14.5;
            if ($val <= 12.5) return 'Size 21 (12cm)';
            if ($val <= 13.4) return 'Size 22 (13cm)';
            if ($val <= 14.9) return 'Size 24';
            if ($val <= 15.9) return 'Size 26';
            if ($val <= 17.0) return 'Size 28';
            return 'Size 30';
        } else {
            $val = $chieuCao ?: 88.0;
            if ($val <= 72) return 'Size 1 (0-1T)';
            if ($val < 90) return 'Size 1 (1-2T)';
            if ($val <= 100) return 'Size 2 (2-3T)';
            if ($val <= 110) return 'Size 3 (3-4T)';
            if ($val <= 122) return 'Size 4 (4-5T)';
            return 'Size 5 (5-6T)';
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
