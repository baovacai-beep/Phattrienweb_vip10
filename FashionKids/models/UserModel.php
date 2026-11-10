<?php
/**
 * Lớp Model Người Dùng & Phân Quyền (models/UserModel.php)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_)
 *
 * Khớp CSDL db_fashionkids:
 *  - hgb_hang_thanh_vien: khóa chính `ma_hang`
 *  - hgb_nguoi_dung     : khóa ngoại `ma_hang_thanh_vien` -> hgb_hang_thanh_vien.ma_hang
 *  - Stored Procedure   : sp_hgb_dang_ky_thanh_vien, sp_hgb_tich_diem_nang_hang
 *  - Stored Function    : fn_hgb_tinh_phan_tram_giam_gia
 *
 * Hiện thực hóa 4 tính chất OOP: Đóng gói (Encapsulation), Kế thừa (Inheritance),
 * Đa hình (Polymorphism) và Trừu tượng (Abstraction).
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../config/app.php';

class UserModel extends Model {
    /** Whitelist tên cột được phép kiểm tra trùng lặp (chống SQL Injection ở phần tên cột) */
    private const DUPLICATE_FIELDS = ['ten_dang_nhap', 'thu_dien_tu'];

    private const SESSION_OTP      = 'hgb_otp_data';
    private const SESSION_VERIFIED = 'hgb_email_verified';

    // ========================================================================
    // 1. KIỂM TRA TRÙNG LẶP (hgb_)
    // ========================================================================

    /**
     * Kiểm tra xem Tên đăng nhập hoặc Email đã tồn tại trong CSDL chưa.
     * Whitelist tên cột để chống SQL Injection, dùng PDO Prepared Statement.
     *
     * @param string $truong Tên cột cần kiểm tra ('ten_dang_nhap' hoặc 'thu_dien_tu')
     * @param string $giaTri Giá trị người dùng nhập
     * @return bool True nếu đã tồn tại, False nếu chưa
     * @throws InvalidArgumentException Khi tên trường không nằm trong whitelist
     * @throws RuntimeException Khi không kết nối được CSDL
     */
    public function kiemTraTrungLap(string $truong, string $giaTri): bool {
        // Whitelist tên cột để chống SQL Injection
        $allowedFields = ['ten_dang_nhap', 'thu_dien_tu'];
        if (!in_array($truong, $allowedFields, true)) {
            throw new InvalidArgumentException("Trường kiểm tra không hợp lệ.");
        }

        $this->yeuCauKetNoi();

        try {
            $sql = "SELECT COUNT(*) AS total FROM hgb_nguoi_dung WHERE {$truong} = :val";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':val' => trim($giaTri)]);
            $res = $stmt->fetch();

            return ((int)($res['total'] ?? 0)) > 0;
        } catch (PDOException $e) {
            error_log('[hgb] kiemTraTrungLap: ' . $e->getMessage());
            throw new RuntimeException('Không thể kiểm tra dữ liệu lúc này, vui lòng thử lại sau.', 0, $e);
        }
    }

    // ========================================================================
    // 2. KHỞI TẠO & XÁC THỰC MÃ OTP 6 CHỮ SỐ (hgb_)
    // ========================================================================

    /**
     * Khởi tạo mã OTP ngẫu nhiên 6 chữ số và lưu vào Session
     *
     * @param string $email Địa chỉ email nhận mã
     * @return string Mã OTP 6 chữ số vừa tạo
     */
    public function taoMaOtp(string $email): string {
        $otp = (string)random_int(100000, 999999);
        $emailClean = strtolower(trim($email));
        $ttl = defined('OTP_TTL') ? OTP_TTL : 300;

        $_SESSION[self::SESSION_OTP] = [
            'email'      => $emailClean,
            'otp'        => $otp,
            'sent_at'    => time(),
            'expired_at' => time() + $ttl // Hết hạn sau 5 phút
        ];

        // Hủy cờ xác thực cũ khi khởi tạo mã mới
        unset($_SESSION[self::SESSION_VERIFIED]);

        return $otp;
    }

    /**
     * Kiểm tra tính hợp lệ của mã OTP người dùng nhập
     *
     * @param string $email Địa chỉ email cần đối soát
     * @param string $otpInput Mã OTP người dùng gửi lên
     * @return array{valid: bool, message: string, code?: string}
     */
    public function xacThucOtp(string $email, string $otpInput): array {
        if (!isset($_SESSION[self::SESSION_OTP])) {
            return ['valid' => false, 'code' => 'missing', 'message' => 'Mã OTP chưa được khởi tạo hoặc đã bị xóa.'];
        }

        $sessionData = $_SESSION[self::SESSION_OTP];
        $emailClean  = strtolower(trim($email));

        if (time() > (int)($sessionData['expired_at'] ?? 0)) {
            unset($_SESSION[self::SESSION_OTP]);
            return ['valid' => false, 'code' => 'expired', 'message' => 'Mã OTP đã hết hiệu lực (quá 5 phút).'];
        }

        if (strtolower(trim((string)($sessionData['email'] ?? ''))) !== $emailClean) {
            return ['valid' => false, 'code' => 'email_mismatch', 'message' => 'Email xác thực không trùng khớp.'];
        }

        if ((string)($sessionData['otp'] ?? '') !== trim($otpInput)) {
            return ['valid' => false, 'code' => 'wrong', 'message' => 'Mã xác thực 6 số không chính xác.'];
        }

        // Xác thực thành công: Ghi nhận cờ verified và giải phóng mã OTP
        $_SESSION[self::SESSION_VERIFIED] = $emailClean;
        unset($_SESSION[self::SESSION_OTP]);

        return ['valid' => true, 'code' => 'success', 'message' => 'Xác thực OTP thành công!'];
    }

    /**
     * Kiểm tra email này đã được xác thực OTP trong phiên hiện tại hay chưa
     */
    public function emailDaXacThuc(string $email): bool {
        $v = $_SESSION[self::SESSION_VERIFIED] ?? null;
        $target = strtolower(trim($email));
        if (is_string($v)) {
            return strtolower(trim($v)) === $target;
        }
        if (is_array($v) && isset($v['email'])) {
            return strtolower(trim((string)$v['email'])) === $target;
        }
        return false;
    }

    /**
     * Hủy trạng thái xác thực sau khi đăng ký hoàn tất
     */
    public function xoaTrangThaiXacThuc(): void {
        unset($_SESSION[self::SESSION_OTP], $_SESSION[self::SESSION_VERIFIED]);
    }

    // ========================================================================
    // 3. ĐĂNG KÝ / ĐĂNG NHẬP VỚI STORED PROCEDURES (hgb_)
    // ========================================================================

    /**
     * Đăng ký thành viên qua Stored Procedure sp_hgb_dang_ky_thanh_vien.
     * Gọi closeCursor() để chống lỗi xung đột con trỏ PDO 2014.
     *
     * @return int Mã người dùng mới
     * @throws DomainException Lỗi nghiệp vụ do SP báo (SQLSTATE 45000: trùng tên/email, SĐT sai...)
     * @throws RuntimeException Lỗi hạ tầng (mất kết nối, lỗi SQL)
     */
    public function dangKy(
        string $username,
        string $password,
        string $fullname,
        string $email,
        string $phone = '',
        string $address = ''
    ): int {
        $this->yeuCauKetNoi();
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $this->db->prepare(
                'CALL sp_hgb_dang_ky_thanh_vien(:ten_dn, :mk, :ho_ten, :email, :sdt, :dia_chi, @ma_moi)'
            );
            $stmt->execute([
                ':ten_dn'  => $username,
                ':mk'      => $hash,
                ':ho_ten'  => $fullname,
                ':email'   => $email,
                ':sdt'     => $phone,
                ':dia_chi' => $address,
            ]);
            // Giải phóng con trỏ trước khi đọc biến OUT (tránh lỗi PDO 2014)
            $stmt->closeCursor();

            $out = $this->db->query('SELECT @ma_moi AS ma_nguoi_dung_moi')->fetch();
            return (int)($out['ma_nguoi_dung_moi'] ?? 0);
        } catch (PDOException $e) {
            // SIGNAL SQLSTATE '45000' trong SP => lỗi nghiệp vụ có thông điệp tiếng Việt
            if (($e->errorInfo[0] ?? '') === '45000') {
                throw new DomainException($this->lamSachThongDiepSp((string)($e->errorInfo[2] ?? $e->getMessage())), 0, $e);
            }
            error_log('[hgb] dangKy: ' . $e->getMessage());
            throw new RuntimeException('Không thể tạo tài khoản lúc này, vui lòng thử lại sau.', 0, $e);
        }
    }

    /**
     * Đánh dấu tài khoản đã xác thực OTP trong CSDL
     */
    public function danhDauDaXacThuc(int $maNguoiDung): void {
        $this->yeuCauKetNoi();
        try {
            $stmt = $this->db->prepare('UPDATE hgb_nguoi_dung SET da_xac_thuc_otp = 1 WHERE ma_nguoi_dung = :id');
            $stmt->execute([':id' => $maNguoiDung]);
        } catch (PDOException $e) {
            error_log('[hgb] danhDauDaXacThuc: ' . $e->getMessage());
        }
    }

    /**
     * Đăng nhập bằng Tên đăng nhập hoặc Email.
     *
     * @return array|null Thông tin người dùng (không có mật khẩu) hoặc null nếu sai thông tin
     * @throws RuntimeException Khi không truy vấn được CSDL
     */
    public function dangNhap(string $username, string $password): ?array {
        $this->yeuCauKetNoi();

        try {
            $stmt = $this->db->prepare(
                "SELECT u.*,
                        COALESCE(h.ten_hang, 'Thành viên mới') AS ten_hang,
                        COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                   FROM hgb_nguoi_dung u
                   LEFT JOIN hgb_hang_thanh_vien h ON u.ma_hang_thanh_vien = h.ma_hang
                  WHERE u.ten_dang_nhap = :ten OR u.thu_dien_tu = :ten
                  LIMIT 1"
            );
            $stmt->execute([':ten' => trim($username)]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            error_log('[hgb] dangNhap: ' . $e->getMessage());
            throw new RuntimeException('Không thể đăng nhập lúc này, vui lòng thử lại sau.', 0, $e);
        }

        if ($user && password_verify($password, $user['mat_khau'])) {
            unset($user['mat_khau']);
            return $user;
        }
        return null;
    }

    // ========================================================================
    // 4. ƯU ĐÃI VIP & TÍCH ĐIỂM
    // ========================================================================

    /**
     * Tính % giảm giá theo hạng thẻ qua Stored Function fn_hgb_tinh_phan_tram_giam_gia
     */
    public function tinhPhanTramGiamGia(int $maNguoiDung): float {
        $this->yeuCauKetNoi();
        try {
            $stmt = $this->db->prepare('SELECT fn_hgb_tinh_phan_tram_giam_gia(:id) AS giam_gia');
            $stmt->execute([':id' => $maNguoiDung]);
            $res = $stmt->fetch();
            return (float)($res['giam_gia'] ?? 0.0);
        } catch (PDOException $e) {
            error_log('[hgb] tinhPhanTramGiamGia: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Tích điểm và tự động nâng hạng thẻ thành viên
     */
    public function tichDiemNangHang(int $maNguoiDung, int $diemCong): bool {
        $this->yeuCauKetNoi();
        try {
            $stmt = $this->db->prepare('CALL sp_hgb_tich_diem_nang_hang(:id, :diem)');
            $kq = $stmt->execute([
                ':id'   => $maNguoiDung,
                ':diem' => $diemCong,
            ]);
            $stmt->closeCursor();
            return $kq;
        } catch (PDOException $e) {
            error_log('[hgb] tichDiemNangHang: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách hạng thành viên
     */
    public function layDanhSachHangThanhVien(): array {
        $this->yeuCauKetNoi();
        try {
            $stmt = $this->db->query('SELECT * FROM hgb_hang_thanh_vien ORDER BY diem_toi_thieu ASC');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('[hgb] layDanhSachHangThanhVien: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cập nhật cấu hình điểm sàn (diem_toi_thieu) và % chiết khấu (phan_tram_giam_gia) cho bảng hgb_hang_thanh_vien (MH 15 - Admin)
     */
    public function capNhatCauHinhHangVip(array $tiers): bool {
        if (empty($tiers)) {
            throw new InvalidArgumentException('Dữ liệu cấu hình hạng thành viên VIP không được để trống!');
        }

        foreach ($tiers as $tier) {
            $minPts   = (int)($tier['diem_toi_thieu'] ?? $tier['min_points'] ?? 0);
            $discount = (float)($tier['phan_tram_giam_gia'] ?? $tier['discount_percent'] ?? 0);
            if ($minPts < 0) {
                throw new InvalidArgumentException('Điểm sàn tối thiểu của hạng thẻ VIP không được là số âm!');
            }
            if ($discount < 0 || $discount > 100) {
                throw new InvalidArgumentException('Phần trăm chiết khấu VIP phải nằm trong khoảng từ 0% đến 100%!');
            }
        }

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare(
                    "UPDATE hgb_hang_thanh_vien
                        SET diem_toi_thieu = :diem,
                            phan_tram_giam_gia = :giam
                      WHERE ma_hang = :id"
                );
                foreach ($tiers as $tier) {
                    $id       = (int)($tier['ma_hang'] ?? $tier['id'] ?? 0);
                    $minPts   = (int)($tier['diem_toi_thieu'] ?? $tier['min_points'] ?? 0);
                    $discount = (float)($tier['phan_tram_giam_gia'] ?? $tier['discount_percent'] ?? 0);
                    if ($id > 0) {
                        $stmt->execute([
                            ':diem' => $minPts,
                            ':giam' => $discount,
                            ':id'   => $id,
                        ]);
                    }
                }
                return true;
            } catch (PDOException $e) {
                error_log('[hgb] capNhatCauHinhHangVip: ' . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Cập nhật trạng thái khóa/mở khóa hoặc phân quyền tài khoản người dùng (MH 15 - Admin)
     */
    public function capNhatTrangThaiNguoiDung(int $maNguoiDung, int $trangThaiOtp): bool {
        if ($maNguoiDung <= 0) {
            return false;
        }
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("UPDATE hgb_nguoi_dung SET da_xac_thuc_otp = :st WHERE ma_nguoi_dung = :id");
                return $stmt->execute([':st' => $trangThaiOtp ? 1 : 0, ':id' => $maNguoiDung]);
            } catch (PDOException $e) {
                error_log('[hgb] capNhatTrangThaiNguoiDung: ' . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    // ========================================================================
    // HÀM TIỆN ÍCH NỘI BỘ
    // ========================================================================

    /** Đảm bảo kết nối CSDL sẵn sàng trước khi truy vấn */
    private function yeuCauKetNoi(): void {
        if ($this->db === null) {
            throw new RuntimeException('Máy chủ CSDL FashionKids đang bảo trì, vui lòng thử lại sau.');
        }
    }

    /** Làm sạch chuỗi thông báo từ Stored Procedure MySQL */
    private function lamSachThongDiepSp(string $raw): string {
        $clean = preg_replace('/^SQLSTATE\[45000\]:.*?:\s*\d+\s*/u', '', $raw);
        return trim($clean ?: 'Dữ liệu không hợp lệ theo quy định hệ thống.');
    }
}
