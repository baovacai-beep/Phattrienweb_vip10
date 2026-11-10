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
                $stmt->closeCursor();
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

    /**
     * Thêm mới hoặc cập nhật mốc quy chuẩn kích cỡ (lnkt_bang_kich_co) - MH 14 (Chỉ Admin)
     */
    public function luuCauHinhSizeChart(array $data): array {
        $sizeId    = (int)($data['size_id'] ?? $data['ma_kich_co'] ?? 0);
        $catId     = (int)($data['category_id'] ?? $data['ma_danh_muc'] ?? 1);
        $sizeLabel = trim((string)($data['size_label'] ?? $data['ten_kich_co'] ?? ''));
        $minVal    = (float)($data['min_val'] ?? $data['chieu_cao_toi_thieu'] ?? 0);
        $maxVal    = (float)($data['max_val'] ?? $data['chieu_cao_toi_da'] ?? 0);
        $chestMax  = isset($data['chest_max']) && $data['chest_max'] !== '' ? (float)$data['chest_max'] : null;
        $waistMax  = isset($data['waist_max']) && $data['waist_max'] !== '' ? (float)$data['waist_max'] : null;

        if ($sizeLabel === '') {
            throw new InvalidArgumentException('Nhãn kích cỡ (Size Label) không được để trống!');
        }
        if ($minVal <= 0 || $maxVal <= 0 || $minVal >= $maxVal) {
            throw new InvalidArgumentException('Khoảng số đo không hợp lệ (Giá trị tối đa phải lớn hơn giá trị tối thiểu và lớn hơn 0)!');
        }

        $isShoes   = ($catId === 4);
        $minHeight = $isShoes ? 75.0 : $minVal;
        $maxHeight = $isShoes ? 135.0 : $maxVal;
        $minFoot   = $isShoes ? $minVal : null;
        $maxFoot   = $isShoes ? $maxVal : null;

        if ($this->hasConnection()) {
            try {
                if ($sizeId > 0) {
                    $stmt = $this->db->prepare("
                        UPDATE lnkt_bang_kich_co
                        SET ma_danh_muc = :cat,
                            ten_kich_co = :label,
                            chieu_cao_toi_thieu = :min_h,
                            chieu_cao_toi_da = :max_h,
                            vong_nguc_tb = :chest,
                            vong_eo_tb = :waist,
                            chieu_dai_chan_min = :min_f,
                            chieu_dai_chan_max = :max_f
                        WHERE ma_kich_co = :id
                    ");
                    $stmt->execute([
                        ':cat'   => $catId,
                        ':label' => $sizeLabel,
                        ':min_h' => $minHeight,
                        ':max_h' => $maxHeight,
                        ':chest' => $chestMax,
                        ':waist' => $waistMax,
                        ':min_f' => $minFoot,
                        ':max_f' => $maxFoot,
                        ':id'    => $sizeId
                    ]);
                } else {
                    $stmt = $this->db->prepare("
                        INSERT INTO lnkt_bang_kich_co
                            (ma_danh_muc, ten_kich_co, chieu_cao_toi_thieu, chieu_cao_toi_da, vong_nguc_tb, vong_eo_tb, chieu_dai_chan_min, chieu_dai_chan_max)
                        VALUES
                            (:cat, :label, :min_h, :max_h, :chest, :waist, :min_f, :max_f)
                    ");
                    $stmt->execute([
                        ':cat'   => $catId,
                        ':label' => $sizeLabel,
                        ':min_h' => $minHeight,
                        ':max_h' => $maxHeight,
                        ':chest' => $chestMax,
                        ':waist' => $waistMax,
                        ':min_f' => $minFoot,
                        ':max_f' => $maxFoot
                    ]);
                    $sizeId = (int)$this->db->lastInsertId();
                }
            } catch (PDOException $e) {
                error_log('Lỗi lưu cấu hình lnkt_bang_kich_co: ' . $e->getMessage());
            }
        }

        if ($sizeId <= 0) {
            $sizeId = rand(400, 999);
        }

        $catNames = [
            1 => 'Váy công chúa',
            2 => 'Bộ thun',
            3 => 'Áo khoác',
            4 => 'Giày sandal'
        ];

        $record = [
            'size_id'       => $sizeId,
            'category_id'   => $catId,
            'category_name' => $catNames[$catId] ?? 'Danh mục #' . $catId,
            'size_type'     => $isShoes ? 'giay_dep' : 'quan_ao',
            'size_label'    => $sizeLabel,
            'min_val'       => $minVal,
            'max_val'       => $maxVal,
            'chest_max'     => $chestMax,
            'waist_max'     => $waistMax,
            'unit'          => 'cm'
        ];

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['lnkt_custom_size_charts'][$sizeId] = $record;

        return $record;
    }
}
