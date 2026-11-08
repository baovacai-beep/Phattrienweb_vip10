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
 * Nguyên tắc: Model KHÔNG giả lập thành công khi mất kết nối CSDL. Mọi thao tác cần CSDL
 * sẽ ném RuntimeException để Controller trả về mã lỗi 503 rõ ràng.
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../config/app.php';

class UserModel extends Model {
    /** Whitelist tên cột được phép kiểm tra trùng lặp (chống SQL Injection ở phần tên cột) */
    private const DUPLICATE_FIELDS = ['ten_dang_nhap', 'thu_dien_tu'];

    private const SESSION_OTP      = 'hgb_otp_data';
    private const SESSION_VERIFIED = 'hgb_email_verified';

    // ========================================================================
    // 1. KIỂM TRA TRÙNG LẶP
    // ========================================================================

    /**
     * Kiểm tra Tên đăng nhập hoặc Email đã tồn tại trong CSDL chưa.
     *
     * @throws InvalidArgumentException Khi tên trường không nằm trong whitelist
     * @throws RuntimeException         Khi không truy vấn được CSDL
     */
    public function kiemTraTrungLap(string $truong, string $giaTri): bool {
        if (!in_array($truong, self::DUPLICATE_FIELDS, true)) {
            throw new InvalidArgumentException('Trường kiểm tra không hợp lệ.');
        }
        $this->yeuCauKetNoi();

        try {
            // Tên cột đã được whitelist nên an toàn khi nối chuỗi; giá trị luôn đi qua prepared statement
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM hgb_nguoi_dung WHERE {$truong} = :val");
            $stmt->execute([':val' => trim($giaTri)]);
            $res = $stmt->fetch();
            return ((int)($res['total'] ?? 0)) > 0;
        } catch (PDOException $e) {
            error_log('[hgb] kiemTraTrungLap: ' . $e->getMessage());
            throw new RuntimeException('Không thể kiểm tra dữ liệu lúc này, vui lòng thử lại sau.', 0, $e);
        }
    }

    // ========================================================================
    // 2. OTP 6 CHỮ SỐ LƯU TRONG SESSION
    // ========================================================================

    /**
     * Khởi tạo mã OTP 6 chữ số cho một email và lưu vào $_SESSION['hgb_otp_data'].
     * Session chỉ giữ bản băm HMAC của mã, không giữ mã gốc.
     *
     * @return array{ok:bool, otp?:string, retry_after?:int, message?:string}
     */
    public function taoMaOtp(string $email): array {
        $email = strtolower(trim($email));
        $cu    = $_SESSION[self::SESSION_OTP] ?? null;

        // Giới hạn tần suất gửi lại để tránh spam email
        if (is_array($cu) && ($cu['email'] ?? '') === $email) {
            $conLai = OTP_RESEND_COOLDOWN - (time() - (int)($cu['sent_at'] ?? 0));
            if ($conLai > 0) {
                return [
                    'ok'          => false,
                    'retry_after' => $conLai,
                    'message'     => "Vui lòng đợi {$conLai} giây trước khi yêu cầu gửi lại mã.",
                ];
            }
        }

        $otp = (string)random_int(100000, 999999);
        $now = time();
        $_SESSION[self::SESSION_OTP] = [
            'email'      => $email,
            'otp_hash'   => $this->bamOtp($otp),
            'sent_at'    => $now,
            'expired_at' => $now + OTP_TTL,
            'attempts'   => 0,
        ];
        // Yêu cầu mã mới thì trạng thái xác thực cũ của email khác không còn giá trị
        unset($_SESSION[self::SESSION_VERIFIED]);

        return ['ok' => true, 'otp' => $otp];
    }

    /**
     * Kiểm tra mã OTP người dùng nhập.
     *
     * @return array{valid:bool, message:string, code?:string}
     */
    public function xacThucOtp(string $email, string $otpInput): array {
        $email = strtolower(trim($email));
        $data  = $_SESSION[self::SESSION_OTP] ?? null;

        if (!is_array($data)) {
            return ['valid' => false, 'code' => 'missing', 'message' => 'Mã OTP chưa được khởi tạo hoặc đã bị xóa. Vui lòng yêu cầu mã mới.'];
        }
        if (time() > (int)$data['expired_at']) {
            unset($_SESSION[self::SESSION_OTP]);
            return ['valid' => false, 'code' => 'expired', 'message' => 'Mã OTP đã hết hiệu lực (quá ' . (OTP_TTL / 60) . ' phút). Vui lòng yêu cầu mã mới.'];
        }
        if (!hash_equals((string)$data['email'], $email)) {
            return ['valid' => false, 'code' => 'email_mismatch', 'message' => 'Email xác thực không trùng khớp với email đã nhận mã.'];
        }
        if ((int)$data['attempts'] >= OTP_MAX_ATTEMPTS) {
            unset($_SESSION[self::SESSION_OTP]);
            return ['valid' => false, 'code' => 'locked', 'message' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng yêu cầu mã mới.'];
        }

        if (!hash_equals((string)$data['otp_hash'], $this->bamOtp(trim($otpInput)))) {
            $_SESSION[self::SESSION_OTP]['attempts']++;
            $conLai = OTP_MAX_ATTEMPTS - (int)$_SESSION[self::SESSION_OTP]['attempts'];
            if ($conLai <= 0) {
                unset($_SESSION[self::SESSION_OTP]);
                return ['valid' => false, 'code' => 'locked', 'message' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng yêu cầu mã mới.'];
            }
            return ['valid' => false, 'code' => 'wrong', 'message' => "Mã xác thực 6 số không chính xác. Bạn còn {$conLai} lần thử."];
        }

        // Thành công: ghi cờ đã xác thực (kèm thời điểm) và hủy mã để không dùng lại được
        $_SESSION[self::SESSION_VERIFIED] = ['email' => $email, 'at' => time()];
        unset($_SESSION[self::SESSION_OTP]);

        return ['valid' => true, 'message' => 'Xác thực OTP thành công!'];
    }

    /**
     * Email này đã được xác thực OTP trong phiên hiện tại (và chưa quá hạn) hay chưa
     */
    public function emailDaXacThuc(string $email): bool {
        $v = $_SESSION[self::SESSION_VERIFIED] ?? null;
        if (!is_array($v)) {
            return false;
        }
        if (time() - (int)$v['at'] > OTP_VERIFIED_TTL) {
            unset($_SESSION[self::SESSION_VERIFIED]);
            return false;
        }
        return hash_equals((string)$v['email'], strtolower(trim($email)));
    }

    /** Hủy trạng thái xác thực sau khi đăng ký xong */
    public function xoaTrangThaiXacThuc(): void {
        unset($_SESSION[self::SESSION_OTP], $_SESSION[self::SESSION_VERIFIED]);
    }

    private function bamOtp(string $otp): string {
        return hash_hmac('sha256', $otp, session_id() . '|fashionkids-hgb');
    }

    // ========================================================================
    // 3. ĐĂNG KÝ / ĐĂNG NHẬP
    // ========================================================================

    /**
     * Đăng ký thành viên qua Stored Procedure sp_hgb_dang_ky_thanh_vien.
     *
     * @return int Mã người dùng mới
     * @throws DomainException  Lỗi nghiệp vụ do SP báo (SQLSTATE 45000: trùng tên/email, SĐT sai...)
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
    // 4. ƯU ĐÃI VIP
    // ========================================================================

    /**
     * Tính % giảm giá theo hạng thẻ qua Stored Function fn_hgb_tinh_phan_tram_giam_gia
     */
    public function layPhanTramGiamGia(int $maNguoiDung): float {
        $this->yeuCauKetNoi();
        try {
            $stmt = $this->db->prepare('SELECT fn_hgb_tinh_phan_tram_giam_gia(:id) AS phan_tram');
            $stmt->execute([':id' => $maNguoiDung]);
            return (float)($stmt->fetch()['phan_tram'] ?? 0.0);
        } catch (PDOException $e) {
            error_log('[hgb] layPhanTramGiamGia: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Cộng điểm thưởng và tự động thăng hạng qua sp_hgb_tich_diem_nang_hang
     */
    public function tichDiem(int $maNguoiDung, int $diem): void {
        if ($diem <= 0) {
            return;
        }
        $this->yeuCauKetNoi();
        try {
            $stmt = $this->db->prepare('CALL sp_hgb_tich_diem_nang_hang(:uid, :pts)');
            $stmt->execute([':uid' => $maNguoiDung, ':pts' => $diem]);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            error_log('[hgb] tichDiem: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // Tiện ích nội bộ
    // ========================================================================

    private function yeuCauKetNoi(): void {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Không kết nối được cơ sở dữ liệu. Vui lòng kiểm tra máy chủ MySQL.');
        }
    }

    /** Bỏ tiền tố mã lỗi "Lỗi [HGB_ERR_02]: " để hiển thị thông điệp thân thiện cho người dùng */
    private function lamSachThongDiepSp(string $msg): string {
        return trim(preg_replace('/^Lỗi\s*\[[A-Z_0-9]+\]\s*:\s*/u', '', $msg));
    }
}
