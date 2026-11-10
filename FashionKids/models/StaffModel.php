<?php
/**
 * Lớp Model nghiệp vụ Nhân viên vận hành (models/StaffModel.php)
 * Phụ trách: danh sách & cập nhật tiến trình đơn hàng, nhập thêm kho, tra cứu tồn kho nhanh,
 * sơ tuyển KOL nhí ("Mời phỏng vấn"), lưu bản nháp bài viết, và Live Chat CSKH.
 * Mọi Stored Procedure đều đi qua Model::callProcedure() (có closeCursor() chống lỗi PDO 2014).
 */

require_once __DIR__ . '/../core/Model.php';

class StaffModel extends Model {

    /** Các bước chuyển trạng thái hợp lệ của đơn hàng. */
    private const ORDER_FLOW = [
        'cho_duyet'   => ['da_xac_nhan', 'da_huy'],
        'da_xac_nhan' => ['dang_giao', 'da_huy'],
        'dang_giao'   => ['hoan_tat', 'da_huy'],
    ];

    // ------------------------------------------------------------------ Đơn hàng (MH 10)

    public function layDanhSachDonHang(int $limit = 200): array {
        $sql = "SELECT d.ma_don_hang, d.ten_nguoi_nhan, d.so_dien_thoai_nhan, d.dia_chi_giao_hang,
                       d.tinh_thanh_pho, d.quan_huyen, d.tien_hang_tam_tinh, d.tien_giam_gia,
                       d.tong_tien_thanh_toan, d.trang_thai_don_hang, d.ngay_dat_hang,
                       h.ten_hang, h.phan_tram_giam_gia, nv.ho_va_ten AS ten_nhan_vien,
                       (SELECT GROUP_CONCAT(CONCAT(ct.so_luong, 'x ', sp.ten_san_pham, ' (', ct.kich_co_chon, ')') SEPARATOR ', ')
                          FROM tgb_chi_tiet_don_hang ct
                          JOIN lbn_san_pham sp ON sp.ma_san_pham = ct.ma_san_pham
                         WHERE ct.ma_don_hang = d.ma_don_hang) AS tom_tat_san_pham
                  FROM lnkt_don_hang d
                  LEFT JOIN hgb_nguoi_dung u  ON u.ma_nguoi_dung = d.ma_nguoi_dung
                  LEFT JOIN hgb_hang_thanh_vien h ON h.ma_hang = u.ma_hang_thanh_vien
                  LEFT JOIN hgb_nguoi_dung nv ON nv.ma_nguoi_dung = d.ma_nhan_vien_xu_ly
                 ORDER BY d.ngay_dat_hang DESC
                 LIMIT " . (int)$limit;
        return $this->requireDb()->query($sql)->fetchAll();
    }

    /**
     * Tiếp nhận / cập nhật tiến trình giao hàng qua sp_lnkt_cap_nhat_trang_thai_don_hang.
     * @return string trạng thái mới
     */
    public function capNhatTienTrinhDon(int $maDon, int $maNhanVien, string $trangThaiMoi): string {
        $pdo  = $this->requireDb();
        $stmt = $pdo->prepare('SELECT trang_thai_don_hang FROM lnkt_don_hang WHERE ma_don_hang = :id');
        $stmt->execute([':id' => $maDon]);
        $current = $stmt->fetchColumn();
        $stmt->closeCursor();

        if ($current === false) {
            throw new InvalidArgumentException('Lỗi [LNKT_ERR_04]: Đơn hàng cần xử lý không tồn tại!');
        }
        if (!isset(self::ORDER_FLOW[$current])) {
            throw new InvalidArgumentException('Lỗi [LNKT_ERR_05]: Đơn hàng đã hoàn tất hoặc đã hủy không thể chỉnh sửa trạng thái!');
        }
        if (!in_array($trangThaiMoi, self::ORDER_FLOW[$current], true)) {
            throw new InvalidArgumentException('Không thể chuyển đơn từ "' . $current . '" sang "' . $trangThaiMoi . '". Hãy xử lý theo đúng tiến trình giao hàng!');
        }

        $this->callProcedure(
            'CALL sp_lnkt_cap_nhat_trang_thai_don_hang(:ma_don, :ma_nv, :trang_thai)',
            [':ma_don' => $maDon, ':ma_nv' => $maNhanVien, ':trang_thai' => $trangThaiMoi]
        );
        return $trangThaiMoi;
    }

    public function demDonTheoTrangThai(): array {
        $counts = ['cho_duyet' => 0, 'da_xac_nhan' => 0, 'dang_giao' => 0, 'hoan_tat' => 0, 'da_huy' => 0];
        $rows = $this->requireDb()->query('SELECT trang_thai_don_hang AS st, COUNT(*) AS n FROM lnkt_don_hang GROUP BY trang_thai_don_hang')->fetchAll();
        foreach ($rows as $r) {
            $counts[$r['st']] = (int)$r['n'];
        }
        return $counts;
    }

    // ------------------------------------------------------------------ Kho (MH 11, MH 13)

    /** Nút "Nhập thêm kho": sp_lbn_nhap_them_kho_hang (SP tự chặn số lượng <= 0). */
    public function nhapThemKho(int $maSanPham, int $soLuong): array {
        $this->callProcedure('CALL sp_lbn_nhap_them_kho_hang(:pid, :qty)', [':pid' => $maSanPham, ':qty' => $soLuong]);
        $stmt = $this->requireDb()->prepare('SELECT ma_san_pham, ten_san_pham, so_luong_ton_kho FROM lbn_san_pham WHERE ma_san_pham = :pid');
        $stmt->execute([':pid' => $maSanPham]);
        $row = $stmt->fetch() ?: [];
        return [
            'product_id'   => $maSanPham,
            'product_name' => $row['ten_san_pham'] ?? '',
            'new_stock'    => (int)($row['so_luong_ton_kho'] ?? 0),
            'is_low_stock' => (int)($row['so_luong_ton_kho'] ?? 0) <= 5,
        ];
    }

    /** Tra cứu nhanh theo mã hoặc tên; nếu có $soLuong thì kiểm tra thêm bằng fn_lbn_kiem_tra_ton_kho. */
    public function traCuuTonKho(string $keyword, int $soLuong = 0): array {
        $pdo = $this->requireDb();
        $keyword = trim($keyword);
        if ($keyword === '') {
            throw new InvalidArgumentException('Vui lòng nhập mã hoặc tên sản phẩm cần tra cứu!');
        }
        $stmt = $pdo->prepare(
            'SELECT ma_san_pham, ten_san_pham, gia_ban, so_luong_ton_kho, loai_kich_co
               FROM lbn_san_pham
              WHERE ma_san_pham = :id OR ten_san_pham LIKE :kw
              ORDER BY so_luong_ton_kho ASC LIMIT 10'
        );
        $stmt->execute([':id' => ctype_digit($keyword) ? (int)$keyword : 0, ':kw' => '%' . $keyword . '%']);
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        if ($soLuong > 0) {
            $fn = $pdo->prepare('SELECT fn_lbn_kiem_tra_ton_kho(:id, :qty) AS ok');
            foreach ($rows as &$r) {
                $fn->execute([':id' => $r['ma_san_pham'], ':qty' => $soLuong]);
                $r['du_hang'] = (bool)$fn->fetchColumn();
                $fn->closeCursor();
            }
            unset($r);
        }
        return $rows;
    }

    // ------------------------------------------------------------------ KOL nhí (MH 17)

    public function layHoSoKol(): array {
        return $this->requireDb()
            ->query("SELECT * FROM lnkt_dang_ky_kol_nhi ORDER BY FIELD(trang_thai_duyet,'cho_duyet','moi_phong_van','trung_tuyen','tu_choi'), ngay_nop_ho_so DESC")
            ->fetchAll();
    }

    /** Sơ tuyển: chỉ chuyển cho_duyet -> moi_phong_van qua sp_lnkt_xet_duyet_kol_nhi. */
    public function moiPhongVanKol(int $maHoSo, int $maNhanVien): void {
        $stmt = $this->requireDb()->prepare('SELECT trang_thai_duyet FROM lnkt_dang_ky_kol_nhi WHERE ma_ho_so = :id');
        $stmt->execute([':id' => $maHoSo]);
        $st = $stmt->fetchColumn();
        $stmt->closeCursor();
        if ($st === false) {
            throw new InvalidArgumentException('Lỗi [LNKT_ERR_07]: Mã hồ sơ ứng tuyển KOL nhí không tồn tại!');
        }
        if ($st !== 'cho_duyet') {
            throw new InvalidArgumentException('Chỉ có thể mời phỏng vấn hồ sơ đang ở trạng thái "Chờ duyệt"!');
        }
        $this->callProcedure(
            'CALL sp_lnkt_xet_duyet_kol_nhi(:hs, :nv, :st)',
            [':hs' => $maHoSo, ':nv' => $maNhanVien, ':st' => 'moi_phong_van']
        );
    }

    // ------------------------------------------------------------------ Bài viết (MH 21/22)

    public function layDanhSachBaiViet(): array {
        return $this->requireDb()
            ->query("SELECT b.ma_bai_viet, b.tieu_de, b.tom_tat_ngan, b.trang_thai_bai_viet, b.ngay_dang, u.ho_va_ten AS tac_gia
                       FROM tgb_bai_viet_tin_tuc b JOIN hgb_nguoi_dung u ON u.ma_nguoi_dung = b.ma_tac_gia
                      ORDER BY b.ngay_dang DESC LIMIT 100")
            ->fetchAll();
    }

    /** Lưu bản nháp (thêm mới, hoặc sửa bản nháp đang có). Nhân viên KHÔNG thể đổi trạng thái khác ban_nhap. */
    public function luuBanNhap(int $maTacGia, string $tieuDe, string $tomTat, string $noiDung, string $anh = '', int $maBaiViet = 0): int {
        $pdo = $this->requireDb();
        $tieuDe = trim($tieuDe);
        $tomTat = trim($tomTat);
        $noiDung = trim($noiDung);
        if ($tieuDe === '' || mb_strlen($tieuDe, 'UTF-8') > 255) {
            throw new InvalidArgumentException('Tiêu đề bài viết bắt buộc nhập (tối đa 255 ký tự)!');
        }
        if ($tomTat === '' || mb_strlen($tomTat, 'UTF-8') > 500) {
            throw new InvalidArgumentException('Tóm tắt ngắn bắt buộc nhập (tối đa 500 ký tự)!');
        }
        if ($noiDung === '') {
            throw new InvalidArgumentException('Nội dung chi tiết không được để trống!');
        }
        $anh = $anh !== '' ? $anh : '/FashionKids/images/banner-mua-he-2026.jpg';

        if ($maBaiViet > 0) {
            $stmt = $pdo->prepare("UPDATE tgb_bai_viet_tin_tuc
                                      SET tieu_de = :t, tom_tat_ngan = :s, noi_dung_chi_tiet = :c, anh_dai_dien = :a
                                    WHERE ma_bai_viet = :id AND trang_thai_bai_viet = 'ban_nhap'");
            $stmt->execute([':t' => $tieuDe, ':s' => $tomTat, ':c' => $noiDung, ':a' => $anh, ':id' => $maBaiViet]);
            $chk = $pdo->prepare("SELECT COUNT(*) FROM tgb_bai_viet_tin_tuc WHERE ma_bai_viet = :id AND trang_thai_bai_viet = 'ban_nhap'");
            $chk->execute([':id' => $maBaiViet]);
            if ((int)$chk->fetchColumn() === 0) {
                throw new InvalidArgumentException('Chỉ được sửa bài viết đang ở trạng thái Bản nháp!');
            }
            return $maBaiViet;
        }

        $stmt = $pdo->prepare("INSERT INTO tgb_bai_viet_tin_tuc
                (ma_tac_gia, tieu_de, duong_dan_tinh, anh_dai_dien, tom_tat_ngan, noi_dung_chi_tiet, trang_thai_bai_viet)
                VALUES (:u, :t, :slug, :a, :s, :c, 'ban_nhap')");
        $stmt->execute([
            ':u' => $maTacGia, ':t' => $tieuDe, ':slug' => $this->taoSlug($tieuDe),
            ':a' => $anh, ':s' => $tomTat, ':c' => $noiDung,
        ]);
        return (int)$pdo->lastInsertId();
    }

    private function taoSlug(string $title): string {
        $map = [
            'a' => 'àáạảãâầấậẩẫăằắặẳẵ', 'e' => 'èéẹẻẽêềếệểễ', 'i' => 'ìíịỉĩ',
            'o' => 'òóọỏõôồốộổỗơờớợởỡ', 'u' => 'ùúụủũưừứựửữ', 'y' => 'ỳýỵỷỹ', 'd' => 'đ',
        ];
        $s = mb_strtolower($title, 'UTF-8');
        foreach ($map as $plain => $chars) {
            foreach (preg_split('//u', $chars, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
                $s = str_replace($ch, $plain, $s);
            }
        }
        $s = trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
        return substr($s !== '' ? $s : 'bai-viet', 0, 200) . '-' . substr(bin2hex(random_bytes(4)), 0, 8);
    }

    // ------------------------------------------------------------------ Live Chat (MH 11)

    public function layPhienChat(): array {
        $sql = "SELECT s.ma_phien_khach, s.tin_chua_doc, s.last_id, m.noi_dung_tin_nhan AS tin_cuoi, m.ngay_gui AS gio_cuoi,
                       u.ho_va_ten, u.thu_dien_tu
                  FROM (SELECT ma_phien_khach,
                               MAX(ma_tin_nhan) AS last_id,
                               SUM(CASE WHEN nguoi_gui = 'khach_hang' AND da_xem = 0 THEN 1 ELSE 0 END) AS tin_chua_doc,
                               MAX(CASE WHEN nguoi_gui = 'khach_hang' THEN ma_nguoi_dung END) AS uid
                          FROM tgb_tin_nhan_tu_van GROUP BY ma_phien_khach) s
                  JOIN tgb_tin_nhan_tu_van m ON m.ma_tin_nhan = s.last_id
                  LEFT JOIN hgb_nguoi_dung u ON u.ma_nguoi_dung = s.uid
                 ORDER BY s.last_id DESC LIMIT 50";
        return $this->requireDb()->query($sql)->fetchAll();
    }

    public function layTinNhan(string $maPhien, int $sauMa = 0): array {
        $pdo  = $this->requireDb();
        $stmt = $pdo->prepare('SELECT ma_tin_nhan, nguoi_gui, noi_dung_tin_nhan, da_xem, ngay_gui
                                 FROM tgb_tin_nhan_tu_van
                                WHERE ma_phien_khach = :sid AND ma_tin_nhan > :after ORDER BY ma_tin_nhan ASC');
        $stmt->execute([':sid' => $maPhien, ':after' => $sauMa]);
        $messages = $stmt->fetchAll();

        // Danh sách tin của nhân viên mà khách đã xem -> client cập nhật dấu "Đã xem"
        $seen = $pdo->prepare("SELECT ma_tin_nhan FROM tgb_tin_nhan_tu_van WHERE ma_phien_khach = :sid AND nguoi_gui <> 'khach_hang' AND da_xem = 1");
        $seen->execute([':sid' => $maPhien]);
        return ['messages' => $messages, 'seen_ids' => array_map('intval', $seen->fetchAll(PDO::FETCH_COLUMN))];
    }

    public function guiTinNhan(string $maPhien, int $maNhanVien, string $vaiTro, string $noiDung): int {
        $noiDung = trim($noiDung);
        if ($noiDung === '' || mb_strlen($noiDung, 'UTF-8') > 1000) {
            throw new InvalidArgumentException('Tin nhắn không được để trống và tối đa 1000 ký tự!');
        }
        $pdo = $this->requireDb();
        $exists = $pdo->prepare('SELECT COUNT(*) FROM tgb_tin_nhan_tu_van WHERE ma_phien_khach = :sid');
        $exists->execute([':sid' => $maPhien]);
        if ((int)$exists->fetchColumn() === 0) {
            throw new InvalidArgumentException('Phiên hội thoại không tồn tại!');
        }
        $nguoiGui = $vaiTro === 'quan_tri' ? 'quan_tri' : 'nhan_vien';
        $stmt = $pdo->prepare('INSERT INTO tgb_tin_nhan_tu_van (ma_phien_khach, ma_nguoi_dung, nguoi_gui, noi_dung_tin_nhan, da_xem)
                               VALUES (:sid, :uid, :ng, :nd, 0)');
        $stmt->execute([':sid' => $maPhien, ':uid' => $maNhanVien, ':ng' => $nguoiGui, ':nd' => $noiDung]);
        return (int)$pdo->lastInsertId();
    }
}
