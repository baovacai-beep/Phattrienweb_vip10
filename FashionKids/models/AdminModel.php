<?php
/**
 * Lớp Model nghiệp vụ Quản trị viên (models/AdminModel.php)
 * Phụ trách: cấu hình hạng VIP (hgb_hang_thanh_vien), bảng kích cỡ (lnkt_bang_kich_co),
 * quyết định KOL nhí (sp_lnkt_duyet_ho_so_kol_nhi), xuất bản/xóa bài viết Tạp chí.
 * Mọi lỗi nghiệp vụ được ném ra dưới dạng InvalidArgumentException / PDOException (SIGNAL 45000).
 */

require_once __DIR__ . '/../core/Model.php';

class AdminModel extends Model {

    // ------------------------------------------------------------------ VIP (MH 15)

    public function layHangThanhVien(): array {
        return $this->requireDb()
            ->query('SELECT * FROM hgb_hang_thanh_vien ORDER BY diem_toi_thieu ASC')
            ->fetchAll();
    }

    /**
     * Lưu đồng loạt cấu hình các hạng thẻ.
     * @param array $tiers [['ma_hang'=>1,'diem_toi_thieu'=>0,'phan_tram_giam_gia'=>0], ...]
     */
    public function luuHangThanhVien(array $tiers): int {
        if (empty($tiers)) {
            throw new InvalidArgumentException('Không có cấu hình hạng thẻ nào được gửi lên!');
        }

        $pdo     = $this->requireDb();
        $clean   = [];
        foreach ($tiers as $t) {
            $id     = filter_var($t['ma_hang'] ?? null, FILTER_VALIDATE_INT);
            $points = filter_var($t['diem_toi_thieu'] ?? null, FILTER_VALIDATE_INT);
            $pct    = filter_var($t['phan_tram_giam_gia'] ?? null, FILTER_VALIDATE_FLOAT);

            if ($id === false || $id <= 0) {
                throw new InvalidArgumentException('Mã hạng thẻ không hợp lệ!');
            }
            if ($points === false || $points < 0) {
                throw new InvalidArgumentException('Điểm sàn phải là số nguyên >= 0 (hạng #' . $id . ')!');
            }
            if ($pct === false || $pct < 0 || $pct > 100) {
                throw new InvalidArgumentException('% giảm giá phải nằm trong khoảng 0 - 100 (hạng #' . $id . ')!');
            }
            $clean[$id] = ['points' => $points, 'pct' => (float)$pct];
        }

        // Hạng càng cao (mã càng lớn) thì điểm sàn phải lớn hơn và chiết khấu không được thấp hơn
        ksort($clean);
        $prev = null;
        foreach ($clean as $id => $c) {
            if ($prev !== null) {
                if ($c['points'] <= $prev['points']) {
                    throw new InvalidArgumentException('Điểm sàn của hạng cao hơn phải lớn hơn hạng thấp hơn (hạng #' . $id . ')!');
                }
                if ($c['pct'] < $prev['pct']) {
                    throw new InvalidArgumentException('% giảm giá của hạng cao hơn không được thấp hơn hạng thấp hơn (hạng #' . $id . ')!');
                }
            }
            $prev = $c;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE hgb_hang_thanh_vien SET diem_toi_thieu = :p, phan_tram_giam_gia = :d WHERE ma_hang = :id');
            $affected = 0;
            foreach ($clean as $id => $c) {
                $stmt->execute([':p' => $c['points'], ':d' => $c['pct'], ':id' => $id]);
                $affected += $stmt->rowCount();
            }
            $pdo->commit();
            return $affected;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // ------------------------------------------------------------------ Bảng kích cỡ (MH 14)

    /** Thêm mới (id = 0) hoặc cập nhật một dòng lnkt_bang_kich_co. Trả về mã dòng. */
    public function luuBangKichCo(array $d): int {
        $pdo = $this->requireDb();

        $id      = (int)($d['ma_kich_co'] ?? 0);
        $catId   = filter_var($d['ma_danh_muc'] ?? null, FILTER_VALIDATE_INT);
        $name    = trim((string)($d['ten_kich_co'] ?? ''));
        $gender  = (string)($d['gioi_tinh'] ?? 'chung');
        $gender  = ['girl' => 'be_gai', 'boy' => 'be_trai', 'unisex' => 'chung'][$gender] ?? $gender;

        if ($catId === false || $catId <= 0) {
            throw new InvalidArgumentException('Vui lòng chọn danh mục hợp lệ!');
        }
        $cat = $pdo->prepare('SELECT loai_hang FROM lbn_danh_muc WHERE ma_danh_muc = :id');
        $cat->execute([':id' => $catId]);
        $loaiHang = $cat->fetchColumn();
        if ($loaiHang === false) {
            throw new InvalidArgumentException('Lỗi [LBN_ERR_01]: Mã danh mục không tồn tại trên hệ thống!');
        }
        // Loại hàng luôn lấy theo danh mục để tránh sai lệch dữ liệu
        if ($name === '' || mb_strlen($name, 'UTF-8') > 20) {
            throw new InvalidArgumentException('Tên kích cỡ bắt buộc nhập và tối đa 20 ký tự!');
        }
        if (!in_array($gender, ['be_trai', 'be_gai', 'chung'], true)) {
            throw new InvalidArgumentException('Giới tính không hợp lệ!');
        }

        // Cặp thông số trần/sàn: [cột_min, cột_max, nhãn, bắt buộc?]
        $ranges = $loaiHang === 'giay_dep'
            ? [['chieu_dai_chan', 'Chiều dài bàn chân', true]]
            : [['chieu_cao', 'Chiều cao', true], ['vong_nguc', 'Vòng ngực', false], ['vong_eo', 'Vòng eo', false]];

        $values = [
            'chieu_cao_toi_thieu' => null, 'chieu_cao_toi_da' => null,
            'vong_nguc_toi_thieu' => null, 'vong_nguc_toi_da' => null,
            'vong_eo_toi_thieu'   => null, 'vong_eo_toi_da'   => null,
            'chieu_dai_chan_toi_thieu' => null, 'chieu_dai_chan_toi_da' => null,
        ];
        foreach ($ranges as [$prefix, $label, $required]) {
            $min = $this->soThuc($d[$prefix . '_toi_thieu'] ?? null);
            $max = $this->soThuc($d[$prefix . '_toi_da'] ?? null);
            if ($min === null && $max === null && !$required) {
                continue;
            }
            if ($min === null || $max === null || $min <= 0 || $max <= 0) {
                throw new InvalidArgumentException($label . ': cần nhập đủ trần/sàn là số lớn hơn 0!');
            }
            if ($min > $max) {
                throw new InvalidArgumentException($label . ': giá trị sàn không được lớn hơn giá trị trần!');
            }
            $values[$prefix . '_toi_thieu'] = $min;
            $values[$prefix . '_toi_da']    = $max;
        }

        $params = array_merge($values, [
            'ma_danh_muc' => $catId, 'loai_hang' => $loaiHang,
            'gioi_tinh'   => $gender, 'ten_kich_co' => $name,
        ]);

        if ($id > 0) {
            $sets = [];
            foreach (array_keys($params) as $col) {
                $sets[] = "$col = :$col";
            }
            $params['ma_kich_co'] = $id;
            $stmt = $pdo->prepare('UPDATE lnkt_bang_kich_co SET ' . implode(', ', $sets) . ' WHERE ma_kich_co = :ma_kich_co');
            $stmt->execute($params);
            $exists = $pdo->prepare('SELECT COUNT(*) FROM lnkt_bang_kich_co WHERE ma_kich_co = :id');
            $exists->execute([':id' => $id]);
            if ((int)$exists->fetchColumn() === 0) {
                throw new InvalidArgumentException('Dòng kích cỡ cần cập nhật không tồn tại!');
            }
            return $id;
        }

        $cols = array_keys($params);
        $stmt = $pdo->prepare(
            'INSERT INTO lnkt_bang_kich_co (' . implode(', ', $cols) . ') VALUES (:' . implode(', :', $cols) . ')'
        );
        $stmt->execute($params);
        return (int)$pdo->lastInsertId();
    }

    public function xoaBangKichCo(int $id): void {
        $stmt = $this->requireDb()->prepare('DELETE FROM lnkt_bang_kich_co WHERE ma_kich_co = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Dòng kích cỡ cần xóa không tồn tại!');
        }
    }

    private function soThuc($v): ?float {
        if ($v === null || $v === '') {
            return null;
        }
        $f = filter_var($v, FILTER_VALIDATE_FLOAT);
        if ($f === false) {
            throw new InvalidArgumentException('Thông số kích cỡ phải là số!');
        }
        return (float)$f;
    }

    // ------------------------------------------------------------------ KOL nhí (MH 17)

    /** Gọi sp_lnkt_duyet_ho_so_kol_nhi (có biến OUT) – chỉ quan_tri; trả về trạng thái cuối. */
    public function quyetDinhKol(int $maHoSo, int $maQuanTri, string $quyetDinh): string {
        if (!in_array($quyetDinh, ['trung_tuyen', 'tu_choi'], true)) {
            throw new InvalidArgumentException('Quyết định không hợp lệ (chỉ trung_tuyen hoặc tu_choi)!');
        }
        $out = $this->callProcedure(
            'CALL sp_lnkt_duyet_ho_so_kol_nhi(:hs, :ad, :qd, @ket_qua)',
            [':hs' => $maHoSo, ':ad' => $maQuanTri, ':qd' => $quyetDinh],
            ['@ket_qua']
        );
        return (string)($out['ket_qua'] ?? $quyetDinh);
    }

    // ------------------------------------------------------------------ Tạp chí (MH 21/22)

    public function xuatBanBaiViet(int $maBaiViet): void {
        $pdo  = $this->requireDb();
        $stmt = $pdo->prepare("SELECT tieu_de, noi_dung_chi_tiet, trang_thai_bai_viet FROM tgb_bai_viet_tin_tuc WHERE ma_bai_viet = :id");
        $stmt->execute([':id' => $maBaiViet]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new InvalidArgumentException('Bài viết không tồn tại!');
        }
        if ($row['trang_thai_bai_viet'] === 'xuat_ban') {
            throw new InvalidArgumentException('Bài viết đã được xuất bản trước đó!');
        }
        if (trim((string)$row['tieu_de']) === '' || trim(strip_tags((string)$row['noi_dung_chi_tiet'])) === '') {
            throw new InvalidArgumentException('Bài viết thiếu tiêu đề hoặc nội dung, không thể xuất bản!');
        }
        $up = $pdo->prepare("UPDATE tgb_bai_viet_tin_tuc SET trang_thai_bai_viet = 'xuat_ban', ngay_dang = NOW() WHERE ma_bai_viet = :id");
        $up->execute([':id' => $maBaiViet]);
    }

    public function xoaBaiViet(int $maBaiViet): void {
        $stmt = $this->requireDb()->prepare('DELETE FROM tgb_bai_viet_tin_tuc WHERE ma_bai_viet = :id');
        $stmt->execute([':id' => $maBaiViet]);
        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Bài viết cần xóa không tồn tại!');
        }
    }
}
