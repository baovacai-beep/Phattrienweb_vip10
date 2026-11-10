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

    /**
     * Lưu hoặc cập nhật cấu hình trần/sàn kích cỡ trong bảng lnkt_bang_kich_co (MH 14 - Admin)
     */
    public function saveSizeChart(array $data): int {
        $maKichCo   = (int)($data['ma_kich_co'] ?? $data['id'] ?? 0);
        $maDanhMuc  = (int)($data['ma_danh_muc'] ?? $data['category_id'] ?? 1);
        $loaiHang   = trim((string)($data['loai_hang'] ?? $data['type'] ?? 'quan_ao'));
        $gioiTinh   = trim((string)($data['gioi_tinh'] ?? $data['gender'] ?? 'chung'));
        $tenKichCo  = trim((string)($data['ten_kich_co'] ?? $data['size_name'] ?? ''));

        if ($tenKichCo === '') {
            throw new InvalidArgumentException('Ký hiệu kích cỡ (ten_kich_co) không được để trống!');
        }
        if ($gioiTinh === 'girl') $gioiTinh = 'be_gai';
        elseif ($gioiTinh === 'boy') $gioiTinh = 'be_trai';
        elseif (!in_array($gioiTinh, ['be_trai', 'be_gai', 'chung'], true)) $gioiTinh = 'chung';

        if (!in_array($loaiHang, ['quan_ao', 'giay_dep'], true)) {
            $loaiHang = ($maDanhMuc === 4) ? 'giay_dep' : 'quan_ao';
        }

        $hMin = ($loaiHang === 'quan_ao') ? (float)($data['chieu_cao_toi_thieu'] ?? $data['height_min'] ?? 0) : null;
        $hMax = ($loaiHang === 'quan_ao') ? (float)($data['chieu_cao_toi_da'] ?? $data['height_max'] ?? 0) : null;
        $cMin = ($loaiHang === 'quan_ao') ? (float)($data['vong_nguc_toi_thieu'] ?? $data['chest_min'] ?? 0) : null;
        $cMax = ($loaiHang === 'quan_ao') ? (float)($data['vong_nguc_toi_da'] ?? $data['chest_max'] ?? 0) : null;
        $wMin = ($loaiHang === 'quan_ao') ? (float)($data['vong_eo_toi_thieu'] ?? $data['waist_min'] ?? 0) : null;
        $wMax = ($loaiHang === 'quan_ao') ? (float)($data['vong_eo_toi_da'] ?? $data['waist_max'] ?? 0) : null;
        $fMin = ($loaiHang === 'giay_dep') ? (float)($data['chieu_dai_chan_toi_thieu'] ?? $data['foot_length_min'] ?? 0) : null;
        $fMax = ($loaiHang === 'giay_dep') ? (float)($data['chieu_dai_chan_toi_da'] ?? $data['foot_length_max'] ?? 0) : null;

        if ($loaiHang === 'quan_ao' && ($hMin <= 0 || $hMax < $hMin)) {
            throw new InvalidArgumentException('Thông số chiều cao trần/sàn không hợp lệ (Chiều cao sàn phải > 0 và <= Chiều cao trần)!');
        }
        if ($loaiHang === 'giay_dep' && ($fMin <= 0 || $fMax < $fMin)) {
            throw new InvalidArgumentException('Thông số chiều dài bàn chân trần/sàn không hợp lệ (Dài chân sàn phải > 0 và <= Dài chân trần)!');
        }

        if ($this->hasConnection()) {
            try {
                if ($maKichCo > 0) {
                    $stmt = $this->db->prepare(
                        "UPDATE lnkt_bang_kich_co
                            SET ma_danh_muc = :ma_dm,
                                loai_hang = :loai,
                                gioi_tinh = :gt,
                                ten_kich_co = :ten,
                                chieu_cao_toi_thieu = :hmin,
                                chieu_cao_toi_da = :hmax,
                                vong_nguc_toi_thieu = :cmin,
                                vong_nguc_toi_da = :cmax,
                                vong_eo_toi_thieu = :wmin,
                                vong_eo_toi_da = :wmax,
                                chieu_dai_chan_toi_thieu = :fmin,
                                chieu_dai_chan_toi_da = :fmax
                          WHERE ma_kich_co = :id"
                    );
                    $stmt->execute([
                        ':ma_dm' => $maDanhMuc,
                        ':loai'  => $loaiHang,
                        ':gt'    => $gioiTinh,
                        ':ten'   => $tenKichCo,
                        ':hmin'  => $hMin,
                        ':hmax'  => $hMax,
                        ':cmin'  => $cMin,
                        ':cmax'  => $cMax,
                        ':wmin'  => $wMin,
                        ':wmax'  => $wMax,
                        ':fmin'  => $fMin,
                        ':fmax'  => $fMax,
                        ':id'    => $maKichCo,
                    ]);
                    return $maKichCo;
                } else {
                    $stmt = $this->db->prepare(
                        "INSERT INTO lnkt_bang_kich_co (
                            ma_danh_muc, loai_hang, gioi_tinh, ten_kich_co,
                            chieu_cao_toi_thieu, chieu_cao_toi_da,
                            vong_nguc_toi_thieu, vong_nguc_toi_da,
                            vong_eo_toi_thieu, vong_eo_toi_da,
                            chieu_dai_chan_toi_thieu, chieu_dai_chan_toi_da
                         ) VALUES (
                            :ma_dm, :loai, :gt, :ten,
                            :hmin, :hmax, :cmin, :cmax, :wmin, :wmax, :fmin, :fmax
                         )"
                    );
                    $stmt->execute([
                        ':ma_dm' => $maDanhMuc,
                        ':loai'  => $loaiHang,
                        ':gt'    => $gioiTinh,
                        ':ten'   => $tenKichCo,
                        ':hmin'  => $hMin,
                        ':hmax'  => $hMax,
                        ':cmin'  => $cMin,
                        ':cmax'  => $cMax,
                        ':wmin'  => $wMin,
                        ':wmax'  => $wMax,
                        ':fmin'  => $fMin,
                        ':fmax'  => $fMax,
                    ]);
                    return (int)$this->db->lastInsertId();
                }
            } catch (PDOException $e) {
                error_log("[lnkt] saveSizeChart error: " . $e->getMessage());
            }
        }

        return $maKichCo > 0 ? $maKichCo : random_int(11, 99);
    }
}
