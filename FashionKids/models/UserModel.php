<?php
/**
 * Lớp Model Người Dùng & Phân Quyền (models/UserModel.php)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_)
 *
 * Khớp CSDL db_fashionkids:
 *  - hgb_hang_thanh_vien: khóa chính `ma_hang` / `ma_hang_thanh_vien`
 *  - hgb_nguoi_dung     : khóa ngoại `ma_hang_thanh_vien`
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

    private const SESSION_OTP            = 'hgb_otp_data';
    private const SESSION_VERIFIED       = 'hgb_email_verified';
    private const SESSION_RESET_OTP      = 'hgb_reset_otp_data';
    private const SESSION_RESET_VERIFIED = 'hgb_reset_email_verified';
    private static bool $oauthSchemaChecked = false;

    /** Dữ liệu mẫu đồng bộ 100% với db_fashionkids.sql phục vụ dự phòng khi chạy offline qua LAN */
    private array $seedUsers = [
        [
            'ma_nguoi_dung'      => 1,
            'ma_hang_thanh_vien' => null,
            'ten_dang_nhap'      => 'admin_fashion',
            'mat_khau'           => 'e10adc3949ba59abbe56e057f20f883e',
            'ho_va_ten'          => 'Hồ Gia Bảo',
            'thu_dien_tu'        => 'admin@fashionkids.vn',
            'so_dien_thoai'      => '0901234567',
            'dia_chi_nha'        => 'Quận 1, TP.HCM',
            'diem_tich_luy'      => 0,
            'vai_tro'            => 'quan_tri',
            'da_xac_thuc_otp'    => 1,
            'ten_hang'           => 'Quản trị viên',
            'phan_tram_giam_gia' => 0
        ],
        [
            'ma_nguoi_dung'      => 2,
            'ma_hang_thanh_vien' => null,
            'ten_dang_nhap'      => 'staff_tuvan',
            'mat_khau'           => 'e10adc3949ba59abbe56e057f20f883e',
            'ho_va_ten'          => 'Lê Nguyễn Khánh Trình',
            'thu_dien_tu'        => 'staff@fashionkids.vn',
            'so_dien_thoai'      => '0907654321',
            'dia_chi_nha'        => 'Quận 3, TP.HCM',
            'diem_tich_luy'      => 0,
            'vai_tro'            => 'nhan_vien',
            'da_xac_thuc_otp'    => 1,
            'ten_hang'           => 'Nhân viên',
            'phan_tram_giam_gia' => 0
        ],
        [
            'ma_nguoi_dung'      => 3,
            'ma_hang_thanh_vien' => 3,
            'ten_dang_nhap'      => 'me_be_sam',
            'mat_khau'           => 'e10adc3949ba59abbe56e057f20f883e',
            'ho_va_ten'          => 'Võ Thị Thanh Thảo',
            'thu_dien_tu'        => 'thanhthao@gmail.com',
            'so_dien_thoai'      => '0918889999',
            'dia_chi_nha'        => 'Số 120 Điện Biên Phủ, Bình Thạnh',
            'diem_tich_luy'      => 1600,
            'vai_tro'            => 'khach_hang',
            'da_xac_thuc_otp'    => 1,
            'ten_hang'           => 'Vàng (Gold)',
            'phan_tram_giam_gia' => 10
        ],
        [
            'ma_nguoi_dung'      => 4,
            'ma_hang_thanh_vien' => 4,
            'ten_dang_nhap'      => 'vip_kim_cuong',
            'mat_khau'           => 'e10adc3949ba59abbe56e057f20f883e',
            'ho_va_ten'          => 'Trần Lan Anh',
            'thu_dien_tu'        => 'kimcuong@fashionkids.vn',
            'so_dien_thoai'      => '0909888777',
            'dia_chi_nha'        => 'Khu biệt thự Thảo Điền, TP. Thủ Đức',
            'diem_tich_luy'      => 3500,
            'vai_tro'            => 'khach_hang',
            'da_xac_thuc_otp'    => 1,
            'ten_hang'           => 'Kim Cương (Diamond)',
            'phan_tram_giam_gia' => 15
        ],
        [
            'ma_nguoi_dung'      => 5,
            'ma_hang_thanh_vien' => 1,
            'ten_dang_nhap'      => 'khach_dong',
            'mat_khau'           => 'e10adc3949ba59abbe56e057f20f883e',
            'ho_va_ten'          => 'Nguyễn Minh Khôi',
            'thu_dien_tu'        => 'khachmoi@fashionkids.vn',
            'so_dien_thoai'      => '0912333444',
            'dia_chi_nha'        => 'Số 85 Nguyễn Huệ, Quận 1, TP.HCM',
            'diem_tich_luy'      => 120,
            'vai_tro'            => 'khach_hang',
            'da_xac_thuc_otp'    => 1,
            'ten_hang'           => 'Đồng (Bronze)',
            'phan_tram_giam_gia' => 0
        ]
    ];

    public function __construct() {
        parent::__construct();
        $this->ensureUserOAuthSchema();
        $this->ensureDemoAccountsInDb();
    }

    /**
     * Đảm bảo bảng hgb_nguoi_dung có các cột phục vụ Google OAuth 2.0
     */
    public function ensureUserOAuthSchema(): void {
        if (self::$oauthSchemaChecked || !$this->hasConnection()) {
            return;
        }
        self::$oauthSchemaChecked = true;
        try {
            $cols = [];
            $stmt = $this->db->query("SHOW COLUMNS FROM `hgb_nguoi_dung`");
            foreach ($stmt->fetchAll() as $col) {
                if (!empty($col['Field'])) {
                    $cols[$col['Field']] = true;
                }
            }
            $stmt->closeCursor();

            if (!isset($cols['google_id'])) {
                $this->db->exec("ALTER TABLE `hgb_nguoi_dung` ADD COLUMN `google_id` VARCHAR(100) NULL DEFAULT NULL AFTER `da_xac_thuc_otp`");
                try {
                    $this->db->exec("ALTER TABLE `hgb_nguoi_dung` ADD UNIQUE INDEX `uq_hgb_google_id` (`google_id`)");
                } catch (PDOException $ie) {}
            }
            if (!isset($cols['auth_provider'])) {
                $this->db->exec("ALTER TABLE `hgb_nguoi_dung` ADD COLUMN `auth_provider` ENUM('local', 'google') NOT NULL DEFAULT 'local' AFTER `google_id`");
            }
            if (!isset($cols['avatar_url'])) {
                $this->db->exec("ALTER TABLE `hgb_nguoi_dung` ADD COLUMN `avatar_url` VARCHAR(255) NULL DEFAULT NULL AFTER `auth_provider`");
            }
        } catch (PDOException $e) {
            error_log('[hgb] ensureUserOAuthSchema: ' . $e->getMessage());
        }
    }

    /**
     * Đảm bảo các tài khoản mẫu mặc định luôn sẵn sàng trong bảng hgb_nguoi_dung (Mã hóa MD5).
     * Đối chiếu không phân biệt hoa thường theo ten_dang_nhap / thu_dien_tu để không ghi đè tài khoản đăng ký mới.
     */
    public function ensureDemoAccountsInDb(): void {
        if (!$this->hasConnection()) {
            return;
        }
        try {
            $defaultHash = md5('123456'); // Chuẩn mã hóa MD5: e10adc3949ba59abbe56e057f20f883e
            $validTiers = [];
            try {
                $tierRows = $this->db->query("SELECT `ma_hang` FROM `hgb_hang_thanh_vien`")->fetchAll();
                foreach ($tierRows as $tr) {
                    $validTiers[(int)$tr['ma_hang']] = true;
                }
            } catch (PDOException $te) {
                $validTiers = [];
            }

            $findStmt = $this->db->prepare("
                SELECT `ma_nguoi_dung`, `mat_khau`, `da_xac_thuc_otp`, `ho_va_ten`
                FROM `hgb_nguoi_dung`
                WHERE LOWER(`ten_dang_nhap`) = LOWER(:user) OR LOWER(`thu_dien_tu`) = LOWER(:email)
                LIMIT 1
            ");
            $checkIdStmt = $this->db->prepare("SELECT 1 FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = :id LIMIT 1");
            $updStmt = $this->db->prepare("
                UPDATE `hgb_nguoi_dung`
                SET `ho_va_ten` = :name,
                    `mat_khau` = :pass,
                    `vai_tro` = :role,
                    `ma_hang_thanh_vien` = :tier,
                    `diem_tich_luy` = :pts,
                    `da_xac_thuc_otp` = 1
                WHERE `ma_nguoi_dung` = :id
            ");
            $insWithIdStmt = $this->db->prepare("
                INSERT INTO `hgb_nguoi_dung`
                    (`ma_nguoi_dung`, `ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`, `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`, `vai_tro`, `da_xac_thuc_otp`)
                VALUES
                    (:id, :tier, :user, :pass, :name, :email, :phone, :addr, :pts, :role, 1)
            ");
            $insAutoStmt = $this->db->prepare("
                INSERT INTO `hgb_nguoi_dung`
                    (`ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`, `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`, `vai_tro`, `da_xac_thuc_otp`)
                VALUES
                    (:tier, :user, :pass, :name, :email, :phone, :addr, :pts, :role, 1)
            ");

            foreach ($this->seedUsers as $su) {
                $tierId = ($su['ma_hang_thanh_vien'] !== null && isset($validTiers[(int)$su['ma_hang_thanh_vien']]))
                    ? (int)$su['ma_hang_thanh_vien']
                    : null;

                $findStmt->execute([
                    ':user'  => $su['ten_dang_nhap'],
                    ':email' => $su['thu_dien_tu'],
                ]);
                $existing = $findStmt->fetch();

                if ($existing) {
                    $existingPass = (string)($existing['mat_khau'] ?? '');
                    $existingOtp  = (int)($existing['da_xac_thuc_otp'] ?? 0);
                    $existingName = (string)($existing['ho_va_ten'] ?? '');
                    $hasOutdatedLabel = (
                        strpos($existingName, '(Chủ Shop)') !== false ||
                        strpos($existingName, '(Mẹ Bé Sam)') !== false ||
                        strpos($existingName, '(VIP Kim Cương)') !== false ||
                        strpos($existingName, '(Khách Hạng Đồng)') !== false
                    );

                    if (strcasecmp($existingPass, $defaultHash) !== 0 || $existingOtp !== 1 || $hasOutdatedLabel) {
                        $updStmt->execute([
                            ':name' => $su['ho_va_ten'],
                            ':pass' => $defaultHash,
                            ':role' => $su['vai_tro'],
                            ':tier' => $tierId,
                            ':pts'  => $su['diem_tich_luy'],
                            ':id'   => (int)$existing['ma_nguoi_dung'],
                        ]);
                    }
                    continue;
                }

                $checkIdStmt->execute([':id' => $su['ma_nguoi_dung']]);
                $idTaken = (bool)$checkIdStmt->fetchColumn();

                $params = [
                    ':tier'  => $tierId,
                    ':user'  => $su['ten_dang_nhap'],
                    ':pass'  => $defaultHash,
                    ':name'  => $su['ho_va_ten'],
                    ':email' => $su['thu_dien_tu'],
                    ':phone' => $su['so_dien_thoai'],
                    ':addr'  => $su['dia_chi_nha'],
                    ':pts'   => $su['diem_tich_luy'],
                    ':role'  => $su['vai_tro'],
                ];

                if (!$idTaken) {
                    $insWithIdStmt->execute(array_merge([':id' => $su['ma_nguoi_dung']], $params));
                } else {
                    $insAutoStmt->execute($params);
                }
            }
        } catch (PDOException $e) {
            error_log('[hgb] ensureDemoAccountsInDb: ' . $e->getMessage());
        }
    }

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
     */
    public function kiemTraTrungLap(string $truong, string $giaTri): bool {
        // Whitelist tên cột để chống SQL Injection
        $allowedFields = ['ten_dang_nhap', 'thu_dien_tu'];
        if (!in_array($truong, $allowedFields, true)) {
            throw new InvalidArgumentException("Trường kiểm tra không hợp lệ.");
        }

        $valClean = trim($giaTri);

        if ($this->hasConnection()) {
            try {
                $sql = "SELECT COUNT(*) AS total FROM hgb_nguoi_dung WHERE LOWER({$truong}) = LOWER(:val)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':val' => $valClean]);
                $res = $stmt->fetch();

                return ((int)($res['total'] ?? 0)) > 0;
            } catch (PDOException $e) {
                error_log('[hgb] kiemTraTrungLap: ' . $e->getMessage());
            }
        }

        // Dự phòng đối chiếu danh sách seedUsers + tài khoản vừa đăng ký trong Session
        $allUsers = array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []);
        foreach ($allUsers as $u) {
            if (isset($u[$truong]) && strcasecmp((string)$u[$truong], $valClean) === 0) {
                return true;
            }
        }
        return false;
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
        $_SESSION[self::SESSION_VERIFIED] = trim($email);
        $_SESSION['hgb_otp_verified_email'] = $emailClean;
        unset($_SESSION[self::SESSION_OTP]);

        return ['valid' => true, 'code' => 'success', 'message' => 'Xác thực OTP thành công!'];
    }

    /**
     * Kiểm tra email này đã được xác thực OTP trong phiên hiện tại hay chưa
     */
    public function emailDaXacThuc(string $email): bool {
        $v = $_SESSION[self::SESSION_VERIFIED] ?? $_SESSION['hgb_otp_verified_email'] ?? null;
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
        unset($_SESSION[self::SESSION_OTP], $_SESSION[self::SESSION_VERIFIED], $_SESSION['hgb_otp_verified_email']);
    }

    // ========================================================================
    // 3. ĐĂNG KÝ / ĐĂNG NHẬP VỚI STORED PROCEDURES (hgb_)
    // ========================================================================

    /**
     * Gọi Stored Procedure sp_hgb_dang_ky_thanh_vien tạo người dùng mới (Mã hóa mật khẩu chuẩn MD5)
     */
    public function dangKyNguoiDung(
        string $username,
        string $hashedPassword,
        string $fullname,
        string $email,
        string $phone,
        string $address
    ): int {
        $usernameClean = trim($username);
        $fullnameClean = trim($fullname);
        $emailClean    = strtolower(trim($email));
        $addressClean  = trim($address);

        if ($this->kiemTraTrungLap('ten_dang_nhap', $usernameClean)) {
            throw new DomainException('Tên đăng nhập này đã tồn tại trên hệ thống!');
        }
        if ($this->kiemTraTrungLap('thu_dien_tu', $emailClean)) {
            throw new DomainException('Địa chỉ Email này đã được tài khoản khác đăng ký!');
        }

        $md5Password = (strlen($hashedPassword) === 32 && ctype_xdigit($hashedPassword))
            ? strtolower($hashedPassword)
            : md5($hashedPassword);

        $phoneTrim  = trim($phone);
        $phoneParam = ($phoneTrim === '') ? null : $phoneTrim;
        $newId = 0;

        if ($this->hasConnection()) {
            try {
                $sql = "CALL sp_hgb_dang_ky_thanh_vien(:username, :password, :fullname, :email, :phone, :address, @new_id)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':username' => $usernameClean,
                    ':password' => $md5Password,
                    ':fullname' => $fullnameClean,
                    ':email'    => $emailClean,
                    ':phone'    => $phoneParam,
                    ':address'  => $addressClean
                ]);
                $stmt->closeCursor();

                // Lấy OUT parameter @new_id
                $result = $this->db->query("SELECT @new_id AS new_id")->fetch();
                $newId = (int)($result['new_id'] ?? 0);
            } catch (PDOException $e) {
                if (($e->errorInfo[0] ?? '') === '45000') {
                    throw new DomainException($this->lamSachThongDiepSp((string)($e->errorInfo[2] ?? $e->getMessage())), 0, $e);
                }
                error_log('[hgb] sp_hgb_dang_ky_thanh_vien fallback: ' . $e->getMessage());
            }

            if ($newId <= 0) {
                try {
                    $chk = $this->db->prepare("SELECT `ma_nguoi_dung` FROM `hgb_nguoi_dung` WHERE LOWER(`ten_dang_nhap`) = LOWER(:u) LIMIT 1");
                    $chk->execute([':u' => $usernameClean]);
                    $newId = (int)($chk->fetchColumn() ?: 0);
                } catch (PDOException $e) {}
            }

            // Nếu SP chưa tạo bản ghi (hoặc môi trường chưa nạp SP), thực hiện INSERT trực tiếp chuẩn MD5
            if ($newId <= 0) {
                $defaultTierId = null;
                try {
                    $tierQ = $this->db->query("SELECT `ma_hang` FROM `hgb_hang_thanh_vien` ORDER BY `diem_toi_thieu` ASC LIMIT 1");
                    $tVal = $tierQ->fetchColumn();
                    if ($tVal !== false && $tVal !== null) {
                        $defaultTierId = (int)$tVal;
                    }
                } catch (PDOException $te) {
                    $defaultTierId = null;
                }

                try {
                    $ins = $this->db->prepare("
                        INSERT INTO hgb_nguoi_dung
                            (ma_hang_thanh_vien, ten_dang_nhap, mat_khau, ho_va_ten, thu_dien_tu, so_dien_thoai, dia_chi_nha, diem_tich_luy, vai_tro, da_xac_thuc_otp)
                        VALUES
                            (:tier, :username, :password, :fullname, :email, :phone, :address, 0, 'khach_hang', 1)
                    ");
                    $ins->execute([
                        ':tier'     => $defaultTierId,
                        ':username' => $usernameClean,
                        ':password' => $md5Password,
                        ':fullname' => $fullnameClean,
                        ':email'    => $emailClean,
                        ':phone'    => $phoneParam,
                        ':address'  => $addressClean,
                    ]);
                    $newId = (int)$this->db->lastInsertId();
                } catch (PDOException $insEx) {
                    if (($insEx->errorInfo[0] ?? '') === '23000') {
                        throw new DomainException('Tên đăng nhập hoặc địa chỉ Email đã tồn tại trên hệ thống!', 0, $insEx);
                    }
                    throw $insEx;
                }
            } else {
                // Đảm bảo mật khẩu trong CSDL lưu đúng chuỗi MD5 32 ký tự và đã xác thực OTP
                try {
                    $upd = $this->db->prepare("UPDATE hgb_nguoi_dung SET mat_khau = :md5, da_xac_thuc_otp = 1 WHERE ma_nguoi_dung = :id");
                    $upd->execute([':md5' => $md5Password, ':id' => $newId]);
                } catch (PDOException $e) {
                    error_log('[hgb] sync md5 after register: ' . $e->getMessage());
                }
            }
        }

        // Đồng bộ vào Session để đảm bảo đăng nhập tức thì trong mọi môi trường
        if (!isset($_SESSION['hgb_registered_users']) || !is_array($_SESSION['hgb_registered_users'])) {
            $_SESSION['hgb_registered_users'] = [];
        }
        if ($newId <= 0) {
            $maxSessionId = 10;
            foreach ($_SESSION['hgb_registered_users'] as $sid => $_u) {
                if ((int)$sid >= $maxSessionId) {
                    $maxSessionId = (int)$sid + 1;
                }
            }
            $newId = $maxSessionId;
        }
        $_SESSION['hgb_registered_users'][$newId] = [
            'ma_nguoi_dung'      => $newId,
            'ma_hang_thanh_vien' => 1,
            'ten_dang_nhap'      => $usernameClean,
            'mat_khau'           => $md5Password,
            'ho_va_ten'          => $fullnameClean,
            'thu_dien_tu'        => $emailClean,
            'so_dien_thoai'      => $phoneTrim,
            'dia_chi_nha'        => $addressClean,
            'diem_tich_luy'      => 0,
            'vai_tro'            => 'khach_hang',
            'da_xac_thuc_otp'    => 1,
            'ten_hang'           => 'Đồng (Bronze)',
            'phan_tram_giam_gia' => 0
        ];
        return $newId;
    }

    /**
     * Đăng ký thành viên qua Stored Procedure sp_hgb_dang_ky_thanh_vien (mã hóa MD5).
     * Gọi closeCursor() để chống lỗi xung đột con trỏ PDO 2014.
     *
     * @return int Mã người dùng mới
     * @throws DomainException Lỗi nghiệp vụ do SP báo (SQLSTATE 45000: trùng tên/email, SĐT sai...)
     * @throws RuntimeException Lỗi hạ tầng
     */
    public function dangKy(
        string $username,
        string $password,
        string $fullname,
        string $email,
        string $phone = '',
        string $address = ''
    ): int {
        if ($this->kiemTraTrungLap('ten_dang_nhap', $username)) {
            throw new DomainException('Tên đăng nhập đã tồn tại trên hệ thống.');
        }
        if ($this->kiemTraTrungLap('thu_dien_tu', $email)) {
            throw new DomainException('Địa chỉ Email đã được đăng ký tài khoản.');
        }

        $md5Hash = md5($password);
        return $this->dangKyNguoiDung($username, $md5Hash, $fullname, $email, $phone, $address);
    }

    /**
     * Đánh dấu tài khoản đã xác thực OTP trong CSDL
     */
    public function danhDauDaXacThuc(int $maNguoiDung): void {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare('UPDATE hgb_nguoi_dung SET da_xac_thuc_otp = 1 WHERE ma_nguoi_dung = :id');
                $stmt->execute([':id' => $maNguoiDung]);
            } catch (PDOException $e) {
                error_log('[hgb] danhDauDaXacThuc: ' . $e->getMessage());
            }
        }
    }

    /**
     * Kiểm tra mật khẩu người dùng nhập với chuỗi lưu trữ (chuẩn MD5, tương thích ngược BCRYPT tự động nâng cấp sang MD5 khi đăng nhập đúng)
     */
    private function kiemTraMatKhauHopLe(string $plainPassword, string $storedHash): bool {
        $stored = trim($storedHash);
        if ($stored === '') {
            return false;
        }
        $md5Pass = md5($plainPassword);
        if (strcasecmp($stored, $md5Pass) === 0 || strcasecmp($stored, md5($md5Pass)) === 0) {
            return true;
        }
        if (password_verify($plainPassword, $stored)) {
            return true;
        }
        return false;
    }

    /**
     * Đăng nhập bằng Tên đăng nhập hoặc Email (Chuẩn bảo mật MD5, không phân biệt hoa/thường tên đăng nhập & email).
     */
    public function dangNhap(string $username, string $password): ?array {
        $tenClean = trim($username);
        $md5Password = md5($password);

        if ($this->hasConnection()) {
            $user = null;
            try {
                $stmt = $this->db->prepare(
                    "SELECT u.*,
                            COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                            COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                       FROM hgb_nguoi_dung u
                       LEFT JOIN hgb_hang_thanh_vien h ON u.ma_hang_thanh_vien = h.ma_hang
                      WHERE LOWER(u.ten_dang_nhap) = LOWER(:ten_dn) OR LOWER(u.thu_dien_tu) = LOWER(:email)
                      LIMIT 1"
                );
                $stmt->execute([
                    ':ten_dn' => $tenClean,
                    ':email'  => $tenClean,
                ]);
                $user = $stmt->fetch();
            } catch (PDOException $e) {
                try {
                    $stmt = $this->db->prepare(
                        "SELECT u.*
                           FROM hgb_nguoi_dung u
                          WHERE LOWER(u.ten_dang_nhap) = LOWER(:ten_dn) OR LOWER(u.thu_dien_tu) = LOWER(:email)
                          LIMIT 1"
                    );
                    $stmt->execute([
                        ':ten_dn' => $tenClean,
                        ':email'  => $tenClean,
                    ]);
                    $user = $stmt->fetch();
                    if ($user) {
                        $user['ten_hang'] = $user['ten_hang'] ?? 'Đồng (Bronze)';
                        $user['phan_tram_giam_gia'] = $user['phan_tram_giam_gia'] ?? 0;
                    }
                } catch (PDOException $e2) {
                    error_log('[hgb] dangNhap: ' . $e2->getMessage());
                }
            }

            if ($user) {
                if ($this->kiemTraMatKhauHopLe($password, (string)($user['mat_khau'] ?? ''))) {
                    // Tự động chuẩn hóa mật khẩu về MD5 32 ký tự trong CSDL nếu tài khoản cũ chưa ở dạng MD5
                    if (strcasecmp((string)($user['mat_khau'] ?? ''), $md5Password) !== 0 && !empty($user['ma_nguoi_dung'])) {
                        try {
                            $upd = $this->db->prepare('UPDATE hgb_nguoi_dung SET mat_khau = :md5 WHERE ma_nguoi_dung = :id');
                            $upd->execute([':md5' => $md5Password, ':id' => (int)$user['ma_nguoi_dung']]);
                        } catch (PDOException $e) {
                            // Bỏ qua nếu không thể cập nhật
                        }
                    }
                    unset($user['mat_khau']);
                    $uid = (int)($user['ma_nguoi_dung'] ?? 0);
                    $statusOverride = $_SESSION['hgb_user_admin_overrides'][$uid]['status'] ?? 1;
                    $user['trang_thai'] = (int)$statusOverride;
                    return $user;
                }
                return null;
            }
        }

        // Kiểm tra dự phòng trong seedUsers + tài khoản vừa tạo trong Session
        $allUsers = array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []);
        foreach ($allUsers as $u) {
            if (strcasecmp((string)$u['ten_dang_nhap'], $tenClean) === 0 || strcasecmp((string)$u['thu_dien_tu'], $tenClean) === 0) {
                $stored = (string)($u['mat_khau'] ?? md5('123456'));
                if ($this->kiemTraMatKhauHopLe($password, $stored)) {
                    unset($u['mat_khau']);
                    $uid = (int)($u['ma_nguoi_dung'] ?? 0);
                    $statusOverride = $_SESSION['hgb_user_admin_overrides'][$uid]['status'] ?? 1;
                    $u['trang_thai'] = (int)$statusOverride;
                    return $u;
                }
            }
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
        if ($maNguoiDung <= 0) {
            return 0.0;
        }
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare('SELECT fn_hgb_tinh_phan_tram_giam_gia(:id) AS giam_gia');
                $stmt->execute([':id' => $maNguoiDung]);
                $res = $stmt->fetch();
                $stmt->closeCursor();
                return (float)($res['giam_gia'] ?? 0.0);
            } catch (PDOException $e) {
                error_log('[hgb] tinhPhanTramGiamGia: ' . $e->getMessage());
            }
        }
        foreach (array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []) as $u) {
            if ((int)($u['ma_nguoi_dung'] ?? 0) === $maNguoiDung) {
                return (float)($u['phan_tram_giam_gia'] ?? 0.0);
            }
        }
        return 0.0;
    }

    /**
     * Truy vấn đầy đủ thông tin Hạng thành viên & % Chiết khấu VIP từ CSDL (Chế độ chỉ đọc - Tự động)
     * Tuyệt đối không cho phép client tự ý thay đổi hạng thẻ.
     */
    public function layThongTinVipHienTai(?int $userId): array {
        $tiers = $this->layDanhSachHangThanhVien();
        $defaultTier = $tiers[0] ?? ['ma_hang' => 1, 'ten_hang' => 'Đồng (Bronze)', 'diem_toi_thieu' => 0, 'phan_tram_giam_gia' => 0];
        $nextDefault = $tiers[1] ?? ['ten_hang' => 'Bạc (Silver)', 'diem_toi_thieu' => 500, 'phan_tram_giam_gia' => 5];

        if ($userId === null || $userId <= 0) {
            return [
                'is_guest'         => true,
                'user_id'          => 0,
                'tier_id'          => 1,
                'tier_name'        => 'Khách vãng lai (Hạng Đồng)',
                'tier_short'       => 'Đồng',
                'discount_percent' => 0.0,
                'points'           => 0,
                'role'             => 'guest',
                'next_tier_name'   => (string)($nextDefault['ten_hang'] ?? 'Bạc (Silver)'),
                'next_tier_min'    => (int)($nextDefault['diem_toi_thieu'] ?? 500),
                'points_needed'    => (int)($nextDefault['diem_toi_thieu'] ?? 500),
                'progress_percent' => 0,
                'badge_class'      => 'tier-bronze',
            ];
        }

        $discountPercent = $this->tinhPhanTramGiamGia($userId);
        $userRow = null;

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    SELECT u.ma_nguoi_dung, u.ten_dang_nhap, u.ho_va_ten, u.thu_dien_tu,
                           u.so_dien_thoai, u.dia_chi_nha, u.vai_tro, u.diem_tich_luy,
                           u.ma_hang_thanh_vien,
                           COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                           COALESCE(h.phan_tram_giam_gia, 0) AS tier_discount
                    FROM hgb_nguoi_dung u
                    LEFT JOIN hgb_hang_thanh_vien h ON u.ma_hang_thanh_vien = h.ma_hang
                    WHERE u.ma_nguoi_dung = :id
                    LIMIT 1
                ");
                $stmt->execute([':id' => $userId]);
                $userRow = $stmt->fetch();
                $stmt->closeCursor();
            } catch (PDOException $e) {
                error_log('[hgb] layThongTinVipHienTai: ' . $e->getMessage());
            }
        }

        if (!$userRow) {
            foreach (array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []) as $su) {
                if ((int)($su['ma_nguoi_dung'] ?? 0) === $userId) {
                    $userRow = $su;
                    break;
                }
            }
        }

        $role     = (string)($userRow['vai_tro'] ?? ($_SESSION['user']['vai_tro'] ?? 'khach_hang'));
        $points   = (int)($userRow['diem_tich_luy'] ?? 0);
        $tierId   = (int)($userRow['ma_hang_thanh_vien'] ?? 1);
        $tierName = (string)($userRow['ten_hang'] ?? $defaultTier['ten_hang']);

        if (empty($userRow['ma_hang_thanh_vien']) && in_array($role, ['quan_tri', 'nhan_vien'], true)) {
            $tierId   = 1;
            $tierName = 'Thành viên nội bộ';
            $discountPercent = 0.0;
        }

        // Xác định hạng kế tiếp dựa trên diem_tich_luy và bảng hgb_hang_thanh_vien
        $nextTierName = null;
        $nextTierMin  = $points;
        foreach ($tiers as $t) {
            $minPts = (int)($t['diem_toi_thieu'] ?? 0);
            if ($minPts > $points) {
                $nextTierName = (string)$t['ten_hang'];
                $nextTierMin  = $minPts;
                break;
            }
        }
        $pointsNeeded = $nextTierName !== null ? max(0, $nextTierMin - $points) : 0;
        $progressPct  = ($nextTierName !== null && $nextTierMin > 0)
            ? min(100, (int)round(($points / $nextTierMin) * 100))
            : 100;

        $badgeClass = 'tier-bronze';
        $shortName  = 'Đồng';
        $lowerTier  = mb_strtolower($tierName, 'UTF-8');
        if (strpos($lowerTier, 'kim cương') !== false || strpos($lowerTier, 'diamond') !== false || $tierId === 4) {
            $badgeClass = 'tier-diamond';
            $shortName  = 'Kim Cương';
        } elseif (strpos($lowerTier, 'vàng') !== false || strpos($lowerTier, 'gold') !== false || $tierId === 3) {
            $badgeClass = 'tier-gold';
            $shortName  = 'Vàng';
        } elseif (strpos($lowerTier, 'bạc') !== false || strpos($lowerTier, 'silver') !== false || $tierId === 2) {
            $badgeClass = 'tier-silver';
            $shortName  = 'Bạc';
        }

        return [
            'is_guest'         => false,
            'user_id'          => $userId,
            'tier_id'          => $tierId,
            'tier_name'        => $tierName,
            'tier_short'       => $shortName,
            'discount_percent' => $discountPercent,
            'points'           => $points,
            'role'             => $role,
            'next_tier_name'   => $nextTierName,
            'next_tier_min'    => $nextTierMin,
            'points_needed'    => $pointsNeeded,
            'progress_percent' => $progressPct,
            'badge_class'      => $badgeClass,
            'user_row'         => $userRow,
        ];
    }

    /**
     * Đồng bộ lại $_SESSION['user'] và $_SESSION['auth_user'] trực tiếp từ CSDL (sau khi tích điểm / thăng hạng)
     */
    public function dongBoSessionNguoiDung(?int $userId = null): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $uid = $userId ?? (int)($_SESSION['user']['ma_nguoi_dung'] ?? $_SESSION['auth_user']['id'] ?? 0);
        if ($uid <= 0) {
            return null;
        }
        $vip = $this->layThongTinVipHienTai($uid);
        $row = $vip['user_row'] ?? null;
        if (!$row) {
            return $_SESSION['user'] ?? null;
        }
        unset($row['mat_khau']);
        $row['ten_hang']           = $vip['tier_name'];
        $row['phan_tram_giam_gia'] = $vip['discount_percent'];
        $row['diem_tich_luy']      = $vip['points'];
        $row['ma_hang_thanh_vien'] = $vip['tier_id'];

        $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $row);
        if (class_exists('Rbac')) {
            Rbac::syncSession();
        }
        return $_SESSION['user'];
    }

    /**
     * Tích điểm và tự động nâng hạng thẻ thành viên
     */
    public function tichDiemNangHang(int $maNguoiDung, int $diemCong): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare('CALL sp_hgb_tich_diem_nang_hang(:id, :diem)');
                $kq = $stmt->execute([
                    ':id'   => $maNguoiDung,
                    ':diem' => $diemCong,
                ]);
                $stmt->closeCursor();
                return $kq;
            } catch (PDOException $e) {
                error_log('[hgb] tichDiemNangHang fallback: ' . $e->getMessage());
                if ($diemCong <= 0 || $maNguoiDung <= 0) {
                    return false;
                }
                try {
                    $uStmt = $this->db->prepare('SELECT COALESCE(diem_tich_luy, 0) FROM hgb_nguoi_dung WHERE ma_nguoi_dung = :id LIMIT 1');
                    $uStmt->execute([':id' => $maNguoiDung]);
                    $curPts = $uStmt->fetchColumn();
                    $uStmt->closeCursor();
                    if ($curPts === false) {
                        return false;
                    }
                    $newPts = (int)$curPts + $diemCong;
                    $tStmt = $this->db->prepare('SELECT ma_hang FROM hgb_hang_thanh_vien WHERE diem_toi_thieu <= :pts ORDER BY diem_toi_thieu DESC LIMIT 1');
                    $tStmt->execute([':pts' => $newPts]);
                    $newTier = $tStmt->fetchColumn();
                    $tStmt->closeCursor();
                    $upd = $this->db->prepare('UPDATE hgb_nguoi_dung SET diem_tich_luy = :pts, ma_hang_thanh_vien = :tier WHERE ma_nguoi_dung = :id');
                    return $upd->execute([
                        ':pts'  => $newPts,
                        ':tier' => ($newTier !== false && $newTier !== null) ? (int)$newTier : null,
                        ':id'   => $maNguoiDung,
                    ]);
                } catch (PDOException $e2) {
                    error_log('[hgb] tichDiemNangHang SQL fallback error: ' . $e2->getMessage());
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Lấy danh sách hạng thành viên
     */
    public function layDanhSachHangThanhVien(): array {
        $tiers = [
            ['ma_hang' => 1, 'ten_hang' => 'Đồng (Bronze)', 'diem_toi_thieu' => 0, 'phan_tram_giam_gia' => 0],
            ['ma_hang' => 2, 'ten_hang' => 'Bạc (Silver)', 'diem_toi_thieu' => 500, 'phan_tram_giam_gia' => 5],
            ['ma_hang' => 3, 'ten_hang' => 'Vàng (Gold)', 'diem_toi_thieu' => 1500, 'phan_tram_giam_gia' => 10],
            ['ma_hang' => 4, 'ten_hang' => 'Kim Cương (Diamond)', 'diem_toi_thieu' => 3000, 'phan_tram_giam_gia' => 15],
        ];
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->query('SELECT * FROM hgb_hang_thanh_vien ORDER BY ma_hang ASC');
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    $tiers = $rows;
                }
            } catch (PDOException $e) {
                error_log('[hgb] layDanhSachHangThanhVien: ' . $e->getMessage());
            }
        }
        if (!empty($_SESSION['hgb_vip_tiers_overrides']) && is_array($_SESSION['hgb_vip_tiers_overrides'])) {
            foreach ($tiers as &$t) {
                $id = (int)$t['ma_hang'];
                if (isset($_SESSION['hgb_vip_tiers_overrides'][$id])) {
                    $t['diem_toi_thieu'] = (int)$_SESSION['hgb_vip_tiers_overrides'][$id]['min'];
                    $t['phan_tram_giam_gia'] = (float)$_SESSION['hgb_vip_tiers_overrides'][$id]['discount'];
                }
            }
            unset($t);
        }
        return $tiers;
    }

    /**
     * Lưu cấu hình mức điểm tối thiểu và tỷ lệ giảm giá VIP cho trang Quản trị
     */
    public function luuCauHinhHangThanhVien(array $tiersInput): array {
        if (!isset($_SESSION['hgb_vip_tiers_overrides']) || !is_array($_SESSION['hgb_vip_tiers_overrides'])) {
            $_SESSION['hgb_vip_tiers_overrides'] = [];
        }
        $updStmt = $this->hasConnection()
            ? $this->db->prepare("UPDATE `hgb_hang_thanh_vien` SET `diem_toi_thieu` = :min, `phan_tram_giam_gia` = :disc WHERE `ma_hang` = :id")
            : null;

        foreach ($tiersInput as $id => $cfg) {
            $tierId   = (int)$id;
            $minPts   = max(0, (int)($cfg['min'] ?? 0));
            $discount = max(0, min(50, (float)($cfg['discount'] ?? 0)));
            $_SESSION['hgb_vip_tiers_overrides'][$tierId] = ['min' => $minPts, 'discount' => $discount];
            if ($updStmt) {
                try {
                    $updStmt->execute([':min' => $minPts, ':disc' => $discount, ':id' => $tierId]);
                } catch (PDOException $e) {
                    error_log('[hgb] luuCauHinhHangThanhVien: ' . $e->getMessage());
                }
            }
        }
        return $this->layDanhSachHangThanhVien();
    }

    /**
     * Lấy danh sách người dùng cho trang Quản trị Người dùng & Hạng VIP
     */
    public function layDanhSachNguoiDung(): array {
        $byId = [];
        foreach ($this->seedUsers as $su) {
            $id = (int)$su['ma_nguoi_dung'];
            $byId[$id] = [
                'id'          => $id,
                'username'    => $su['ten_dang_nhap'],
                'name'        => $su['ho_va_ten'],
                'email'       => $su['thu_dien_tu'],
                'role'        => $su['vai_tro'],
                'tier'        => $su['ten_hang'],
                'points'      => (int)$su['diem_tich_luy'],
                'otp_verified'=> (int)$su['da_xac_thuc_otp'],
                'status'      => (int)($_SESSION['hgb_user_admin_overrides'][$id]['status'] ?? 1),
            ];
        }

        if ($this->hasConnection()) {
            try {
                $sql = "SELECT u.ma_nguoi_dung, u.ten_dang_nhap, u.ho_va_ten, u.thu_dien_tu, u.vai_tro,
                               u.diem_tich_luy, u.da_xac_thuc_otp,
                               COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang
                          FROM hgb_nguoi_dung u
                          LEFT JOIN hgb_hang_thanh_vien h ON u.ma_hang_thanh_vien = h.ma_hang
                         ORDER BY u.ma_nguoi_dung ASC";
                $rows = $this->db->query($sql)->fetchAll();
                foreach ($rows as $r) {
                    $id = (int)$r['ma_nguoi_dung'];
                    $role = (string)($r['vai_tro'] ?? 'khach_hang');
                    $tierLabel = $role === 'quan_tri' ? 'Quản trị viên' : ($role === 'nhan_vien' ? 'Nhân viên CSKH' : (string)$r['ten_hang']);
                    $byId[$id] = [
                        'id'           => $id,
                        'username'     => (string)$r['ten_dang_nhap'],
                        'name'         => (string)$r['ho_va_ten'],
                        'email'        => (string)$r['thu_dien_tu'],
                        'role'         => $role,
                        'tier'         => $tierLabel,
                        'points'       => (int)($r['diem_tich_luy'] ?? 0),
                        'otp_verified' => (int)($r['da_xac_thuc_otp'] ?? 1),
                        'status'       => (int)($_SESSION['hgb_user_admin_overrides'][$id]['status'] ?? 1),
                    ];
                }
            } catch (PDOException $e) {
                error_log('[hgb] layDanhSachNguoiDung: ' . $e->getMessage());
            }
        }

        if (!empty($_SESSION['hgb_registered_users']) && is_array($_SESSION['hgb_registered_users'])) {
            foreach ($_SESSION['hgb_registered_users'] as $sid => $su) {
                $id = (int)($su['ma_nguoi_dung'] ?? $sid);
                $byId[$id] = [
                    'id'           => $id,
                    'username'     => (string)$su['ten_dang_nhap'],
                    'name'         => (string)$su['ho_va_ten'],
                    'email'        => (string)$su['thu_dien_tu'],
                    'role'         => (string)($su['vai_tro'] ?? 'khach_hang'),
                    'tier'         => (string)($su['ten_hang'] ?? 'Đồng (Bronze)'),
                    'points'       => (int)($su['diem_tich_luy'] ?? 0),
                    'otp_verified' => (int)($su['da_xac_thuc_otp'] ?? 1),
                    'status'       => (int)($_SESSION['hgb_user_admin_overrides'][$id]['status'] ?? 1),
                ];
            }
        }

        ksort($byId);
        return array_values($byId);
    }

    /**
     * Khóa / Mở khóa trạng thái tài khoản người dùng (không khóa Quản trị viên)
     */
    public function capNhatTrangThaiNguoiDung(int $userId, int $status): bool {
        $users = $this->layDanhSachNguoiDung();
        foreach ($users as $u) {
            if ((int)$u['id'] === $userId) {
                if (($u['role'] ?? '') === 'quan_tri' && $status === 0) {
                    return false;
                }
                if (!isset($_SESSION['hgb_user_admin_overrides']) || !is_array($_SESSION['hgb_user_admin_overrides'])) {
                    $_SESSION['hgb_user_admin_overrides'] = [];
                }
                $_SESSION['hgb_user_admin_overrides'][$userId]['status'] = ($status === 1) ? 1 : 0;
                return true;
            }
        }
        return false;
    }

    /**
     * Cập nhật thông tin cá nhân (kèm tùy chọn đổi tên đăng nhập cho thành viên)
     */
    public function capNhatThongTinCaNhan(
        int $userId,
        string $fullname,
        string $phone,
        string $address,
        ?string $newUsername = null
    ): array {
        $nameClean     = trim($fullname);
        $phoneClean    = trim($phone);
        $addrClean     = trim($address);
        $usernameClean = $newUsername !== null ? trim($newUsername) : null;

        if (mb_strlen($nameClean, 'UTF-8') < 2) {
            throw new DomainException('Họ và tên phải có ít nhất 2 ký tự.');
        }
        if ($phoneClean !== '' && !preg_match('/^\d{9,11}$/', $phoneClean)) {
            throw new DomainException('Số điện thoại phải gồm từ 9 đến 11 chữ số.');
        }
        if ($usernameClean !== null && $usernameClean !== '') {
            if (!preg_match('/^[a-zA-Z0-9_]{4,30}$/', $usernameClean)) {
                throw new DomainException('Tên đăng nhập phải từ 4-30 ký tự, chỉ gồm chữ cái không dấu, số hoặc dấu gạch dưới (_).');
            }
            if ($this->kiemTraTrungLapNgoaiTru('ten_dang_nhap', $usernameClean, $userId)) {
                throw new DomainException('Tên đăng nhập "' . $usernameClean . '" đã có người sử dụng. Vui lòng chọn tên khác.');
            }
        }

        if ($this->hasConnection() && $userId > 0) {
            try {
                if ($usernameClean !== null && $usernameClean !== '') {
                    $stmt = $this->db->prepare("
                        UPDATE `hgb_nguoi_dung`
                        SET `ten_dang_nhap` = :uname,
                            `ho_va_ten` = :name,
                            `so_dien_thoai` = :phone,
                            `dia_chi_nha` = :addr
                        WHERE `ma_nguoi_dung` = :id
                    ");
                    $stmt->execute([
                        ':uname' => $usernameClean,
                        ':name'  => $nameClean,
                        ':phone' => $phoneClean !== '' ? $phoneClean : null,
                        ':addr'  => $addrClean,
                        ':id'    => $userId,
                    ]);
                } else {
                    $stmt = $this->db->prepare("
                        UPDATE `hgb_nguoi_dung`
                        SET `ho_va_ten` = :name,
                            `so_dien_thoai` = :phone,
                            `dia_chi_nha` = :addr
                        WHERE `ma_nguoi_dung` = :id
                    ");
                    $stmt->execute([
                        ':name'  => $nameClean,
                        ':phone' => $phoneClean !== '' ? $phoneClean : null,
                        ':addr'  => $addrClean,
                        ':id'    => $userId,
                    ]);
                }
            } catch (PDOException $e) {
                error_log('[hgb] capNhatThongTinCaNhan: ' . $e->getMessage());
                if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                    throw new DomainException('Tên đăng nhập này đã tồn tại trong hệ thống.');
                }
            }
        }

        if (isset($_SESSION['hgb_registered_users'][$userId])) {
            if ($usernameClean !== null && $usernameClean !== '') {
                $_SESSION['hgb_registered_users'][$userId]['ten_dang_nhap'] = $usernameClean;
            }
            $_SESSION['hgb_registered_users'][$userId]['ho_va_ten']     = $nameClean;
            $_SESSION['hgb_registered_users'][$userId]['so_dien_thoai'] = $phoneClean;
            $_SESSION['hgb_registered_users'][$userId]['dia_chi_nha']   = $addrClean;
        }

        $res = [
            'ho_va_ten'     => $nameClean,
            'so_dien_thoai' => $phoneClean,
            'dia_chi_nha'   => $addrClean,
        ];
        if ($usernameClean !== null && $usernameClean !== '') {
            $res['ten_dang_nhap'] = $usernameClean;
        }
        return $res;
    }

    /**
     * Kiểm tra trùng lặp nhưng loại trừ chính ID người dùng hiện tại
     */
    public function kiemTraTrungLapNgoaiTru(string $truong, string $giaTri, int $excludeUserId): bool {
        $allowedFields = ['ten_dang_nhap', 'thu_dien_tu'];
        if (!in_array($truong, $allowedFields, true)) {
            throw new InvalidArgumentException('Trường kiểm tra không hợp lệ.');
        }
        $valClean = trim($giaTri);
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) AS total
                    FROM `hgb_nguoi_dung`
                    WHERE LOWER(`{$truong}`) = LOWER(:val)
                      AND `ma_nguoi_dung` <> :excl
                ");
                $stmt->execute([':val' => $valClean, ':excl' => $excludeUserId]);
                $res = $stmt->fetch();
                $stmt->closeCursor();
                return ((int)($res['total'] ?? 0)) > 0;
            } catch (PDOException $e) {
                error_log('[hgb] kiemTraTrungLapNgoaiTru: ' . $e->getMessage());
            }
        }
        $allUsers = array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []);
        foreach ($allUsers as $u) {
            if ((int)($u['ma_nguoi_dung'] ?? 0) === $excludeUserId) {
                continue;
            }
            if (isset($u[$truong]) && strcasecmp((string)$u[$truong], $valClean) === 0) {
                return true;
            }
        }
        return false;
    }

    /** Làm sạch chuỗi thông báo từ Stored Procedure MySQL */
    private function lamSachThongDiepSp(string $raw): string {
        $clean = preg_replace('/^SQLSTATE\[45000\]:.*?:\s*\d+\s*/u', '', $raw);
        $clean = preg_replace('/^(?:Lỗi\s*)?\[(?:HGB|LBN|LNKT|TGB)_ERR_\d+\]\s*:\s*/iu', '', (string)$clean);
        return trim($clean ?: 'Dữ liệu không hợp lệ theo quy định hệ thống.');
    }

    // ========================================================================
    // 4. QUÊN MẬT KHẨU (EMAIL OTP RESET) & GOOGLE OAUTH 2.0 (hgb_)
    // ========================================================================

    /**
     * Tìm kiếm tài khoản theo Email (kiểm tra CSDL + danh sách dự phòng)
     */
    public function timTheoEmail(string $email): ?array {
        $emailClean = strtolower(trim($email));
        if ($emailClean === '') {
            return null;
        }
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    SELECT u.*,
                           COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                           COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                      FROM `hgb_nguoi_dung` u
                      LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
                     WHERE LOWER(u.`thu_dien_tu`) = LOWER(:email)
                     LIMIT 1
                ");
                $stmt->execute([':email' => $emailClean]);
                $row = $stmt->fetch();
                $stmt->closeCursor();
                if ($row) {
                    unset($row['mat_khau']);
                    return $row;
                }
            } catch (PDOException $e) {
                error_log('[hgb] timTheoEmail: ' . $e->getMessage());
            }
        }
        $all = array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []);
        foreach ($all as $u) {
            if (strcasecmp((string)($u['thu_dien_tu'] ?? ''), $emailClean) === 0) {
                unset($u['mat_khau']);
                return $u;
            }
        }
        return null;
    }

    public function taoMaOtpQuenMatKhau(string $email): string {
        $otp        = (string)random_int(100000, 999999);
        $emailClean = strtolower(trim($email));
        $ttl        = defined('OTP_TTL') ? OTP_TTL : 300;

        $_SESSION[self::SESSION_RESET_OTP] = [
            'email'      => $emailClean,
            'otp'        => $otp,
            'attempts'   => 0,
            'sent_at'    => time(),
            'expired_at' => time() + $ttl,
        ];
        unset($_SESSION[self::SESSION_RESET_VERIFIED]);
        return $otp;
    }

    public function xacThucOtpQuenMatKhau(string $email, string $otpInput): array {
        if (!isset($_SESSION[self::SESSION_RESET_OTP])) {
            return ['valid' => false, 'code' => 'missing', 'message' => 'Mã OTP khôi phục chưa được tạo hoặc đã hết hạn.'];
        }
        $data       = $_SESSION[self::SESSION_RESET_OTP];
        $emailClean = strtolower(trim($email));

        if (time() > (int)($data['expired_at'] ?? 0)) {
            unset($_SESSION[self::SESSION_RESET_OTP]);
            return ['valid' => false, 'code' => 'expired', 'message' => 'Mã OTP đã hết hiệu lực (quá 5 phút). Vui lòng gửi lại mã mới!'];
        }
        if (strtolower(trim((string)($data['email'] ?? ''))) !== $emailClean) {
            return ['valid' => false, 'code' => 'email_mismatch', 'message' => 'Email xác thực không trùng khớp với yêu cầu ban đầu.'];
        }
        $maxAtt = defined('OTP_MAX_ATTEMPTS') ? OTP_MAX_ATTEMPTS : 5;
        $attempts = (int)($data['attempts'] ?? 0);
        if ($attempts >= $maxAtt) {
            unset($_SESSION[self::SESSION_RESET_OTP]);
            return ['valid' => false, 'code' => 'locked', 'message' => 'Bạn đã nhập sai OTP quá 5 lần. Vui lòng yêu cầu gửi mã mới.'];
        }

        if ((string)($data['otp'] ?? '') !== trim($otpInput)) {
            $_SESSION[self::SESSION_RESET_OTP]['attempts'] = $attempts + 1;
            $rem = max(0, $maxAtt - ($attempts + 1));
            return ['valid' => false, 'code' => 'wrong', 'message' => "Mã OTP không đúng. Bạn còn {$rem} lần thử."];
        }

        $_SESSION[self::SESSION_RESET_VERIFIED] = [
            'email'       => $emailClean,
            'verified_at' => time(),
        ];
        unset($_SESSION[self::SESSION_RESET_OTP]);
        return ['valid' => true, 'code' => 'success', 'message' => 'Xác thực OTP thành công! Hãy nhập mật khẩu mới cho tài khoản của bạn.'];
    }

    public function emailResetDaXacThuc(string $email): bool {
        $v = $_SESSION[self::SESSION_RESET_VERIFIED] ?? null;
        if (!is_array($v) || empty($v['email'])) {
            return false;
        }
        if (time() - (int)($v['verified_at'] ?? 0) > (defined('OTP_VERIFIED_TTL') ? OTP_VERIFIED_TTL : 1800)) {
            unset($_SESSION[self::SESSION_RESET_VERIFIED]);
            return false;
        }
        return strtolower(trim((string)$v['email'])) === strtolower(trim($email));
    }

    public function datLaiMatKhauBangEmail(string $email, string $newPassword): bool {
        $emailClean = strtolower(trim($email));
        $md5Pass    = md5($newPassword);
        $updated    = false;

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    UPDATE `hgb_nguoi_dung`
                    SET `mat_khau` = :pass, `da_xac_thuc_otp` = 1
                    WHERE LOWER(`thu_dien_tu`) = LOWER(:email)
                ");
                $stmt->execute([
                    ':pass'  => $md5Pass,
                    ':email' => $emailClean,
                ]);
                $updated = ($stmt->rowCount() > 0);
                // Nếu rowCount = 0 do mật khẩu mới trùng mật khẩu cũ nhưng email có tồn tại
                if (!$updated && $this->timTheoEmail($emailClean) !== null) {
                    $updated = true;
                }
            } catch (PDOException $e) {
                error_log('[hgb] datLaiMatKhauBangEmail: ' . $e->getMessage());
            }
        }

        if (!empty($_SESSION['hgb_registered_users']) && is_array($_SESSION['hgb_registered_users'])) {
            foreach ($_SESSION['hgb_registered_users'] as $sid => $su) {
                if (strcasecmp((string)($su['thu_dien_tu'] ?? ''), $emailClean) === 0) {
                    $_SESSION['hgb_registered_users'][$sid]['mat_khau'] = $md5Pass;
                    $updated = true;
                }
            }
        }

        unset($_SESSION[self::SESSION_RESET_OTP], $_SESSION[self::SESSION_RESET_VERIFIED]);
        return $updated;
    }

    /**
     * Gợi ý tên đăng nhập dễ đọc (không còn tiền tố gg_) cho tài khoản Google mới
     */
    public function taoGoiYTenDangNhapGoogle(string $email, string $googleId = ''): string {
        $localPart = explode('@', strtolower(trim($email)))[0] ?? 'thanhvien';
        $baseSlug  = preg_replace('/[^a-z0-9_]/', '', $localPart);
        if (strlen($baseSlug) < 4) {
            $baseSlug = str_pad($baseSlug ?: 'thanhvien', 4, '0');
        }
        $baseSlug = substr($baseSlug, 0, 22);
        $candidate = $baseSlug;
        $suffix    = 1;
        while ($this->kiemTraTrungLap('ten_dang_nhap', $candidate)) {
            $candidate = substr($baseSlug, 0, 18) . '_' . $suffix;
            $suffix++;
            if ($suffix > 50) {
                $candidate = substr($baseSlug, 0, 16) . '_' . substr(md5($googleId . microtime(true)), 0, 4);
                break;
            }
        }
        return $candidate;
    }

    /**
     * Kiểm tra tài khoản Google OAuth 2.0 đã sẵn sàng đăng nhập hay cần hoàn tất hồ sơ / xác nhận liên kết
     */
    public function kiemTraTrangThaiTaiKhoanGoogle(array $profile): array {
        $googleId = trim((string)($profile['google_id'] ?? ''));
        $email    = strtolower(trim((string)($profile['email'] ?? '')));
        $avatar   = trim((string)($profile['avatar'] ?? ''));

        if ($googleId === '' || $email === '') {
            throw new InvalidArgumentException('Thông tin tài khoản Google không đầy đủ.');
        }

        if ($this->hasConnection()) {
            // 1. Kiểm tra theo google_id trước (đã từng liên kết trước đó)
            $stmtGid = $this->db->prepare("
                SELECT u.*,
                       COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                       COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                  FROM `hgb_nguoi_dung` u
                  LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
                 WHERE u.`google_id` = :gid
                 LIMIT 1
            ");
            $stmtGid->execute([':gid' => $googleId]);
            $byGid = $stmtGid->fetch();
            $stmtGid->closeCursor();

            if ($byGid) {
                if ($avatar !== '' && $avatar !== (string)($byGid['avatar_url'] ?? '')) {
                    try {
                        $updAv = $this->db->prepare("UPDATE `hgb_nguoi_dung` SET `avatar_url` = :av WHERE `ma_nguoi_dung` = :id");
                        $updAv->execute([':av' => $avatar, ':id' => (int)$byGid['ma_nguoi_dung']]);
                        $byGid['avatar_url'] = $avatar;
                    } catch (PDOException $e) {}
                }
                unset($byGid['mat_khau']);

                $phone = trim((string)($byGid['so_dien_thoai'] ?? ''));
                $addr  = trim((string)($byGid['dia_chi_nha'] ?? ''));
                $uname = trim((string)($byGid['ten_dang_nhap'] ?? ''));
                if ($phone === '' || $addr === '' || strpos($uname, 'gg_') === 0) {
                    return [
                        'status'             => 'needs_onboarding',
                        'mode'               => 'complete_missing',
                        'user'               => $byGid,
                        'suggested_username' => strpos($uname, 'gg_') === 0
                            ? $this->taoGoiYTenDangNhapGoogle($email, $googleId)
                            : $uname,
                    ];
                }
                return [
                    'status' => 'ready',
                    'user'   => $byGid,
                ];
            }

            // 2. Kiểm tra theo Email nếu chưa có google_id
            $stmtEmail = $this->db->prepare("
                SELECT u.*,
                       COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                       COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                  FROM `hgb_nguoi_dung` u
                  LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
                 WHERE LOWER(u.`thu_dien_tu`) = LOWER(:email)
                 LIMIT 1
            ");
            $stmtEmail->execute([':email' => $email]);
            $byEmail = $stmtEmail->fetch();
            $stmtEmail->closeCursor();

            if ($byEmail) {
                unset($byEmail['mat_khau']);
                return [
                    'status'             => 'needs_onboarding',
                    'mode'               => 'link_existing',
                    'user'               => $byEmail,
                    'suggested_username' => (string)($byEmail['ten_dang_nhap'] ?? ''),
                ];
            }

            // 3. Tài khoản Google hoàn toàn mới
            return [
                'status'             => 'needs_onboarding',
                'mode'               => 'new_user',
                'user'               => null,
                'suggested_username' => $this->taoGoiYTenDangNhapGoogle($email, $googleId),
            ];
        }

        // Chế độ offline session fallback
        $all = array_merge($this->seedUsers, $_SESSION['hgb_registered_users'] ?? []);
        foreach ($all as $u) {
            if (($u['google_id'] ?? '') === $googleId) {
                unset($u['mat_khau']);
                if (trim((string)($u['so_dien_thoai'] ?? '')) === '' || trim((string)($u['dia_chi_nha'] ?? '')) === '') {
                    return [
                        'status'             => 'needs_onboarding',
                        'mode'               => 'complete_missing',
                        'user'               => $u,
                        'suggested_username' => $this->taoGoiYTenDangNhapGoogle($email, $googleId),
                    ];
                }
                return ['status' => 'ready', 'user' => $u];
            }
            if (strcasecmp((string)($u['thu_dien_tu'] ?? ''), $email) === 0) {
                unset($u['mat_khau']);
                return [
                    'status'             => 'needs_onboarding',
                    'mode'               => 'link_existing',
                    'user'               => $u,
                    'suggested_username' => (string)($u['ten_dang_nhap'] ?? ''),
                ];
            }
        }

        return [
            'status'             => 'needs_onboarding',
            'mode'               => 'new_user',
            'user'               => null,
            'suggested_username' => $this->taoGoiYTenDangNhapGoogle($email, $googleId),
        ];
    }

    /**
     * Hoàn tất tạo tài khoản mới hoặc liên kết & cập nhật thông tin từ màn hình Google Onboarding
     */
    public function hoanTatTaiKhoanGoogle(array $pending, array $input): array {
        $googleId = trim((string)($pending['google_id'] ?? ''));
        $email    = strtolower(trim((string)($pending['email'] ?? '')));
        $avatar   = trim((string)($pending['avatar'] ?? ''));
        $mode     = (string)($pending['mode'] ?? 'new_user');
        $existing = is_array($pending['existing_user'] ?? null) ? $pending['existing_user'] : null;
        $existId  = (int)($existing['ma_nguoi_dung'] ?? 0);

        if ($googleId === '' || $email === '') {
            throw new DomainException('Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.');
        }

        $username = trim((string)($input['ten_dang_nhap'] ?? $input['username'] ?? ''));
        $fullname = trim((string)($input['ho_va_ten'] ?? $input['fullname'] ?? ''));
        $phone    = trim((string)($input['so_dien_thoai'] ?? $input['phone'] ?? ''));
        $address  = trim((string)($input['dia_chi_nha'] ?? $input['address'] ?? ''));
        $password = (string)($input['mat_khau'] ?? $input['password'] ?? '');

        if (!preg_match('/^[a-zA-Z0-9_]{4,30}$/', $username)) {
            throw new DomainException('Tên đăng nhập phải từ 4-30 ký tự, chỉ gồm chữ cái không dấu, số hoặc dấu gạch dưới (_).');
        }
        if (mb_strlen($fullname, 'UTF-8') < 2) {
            throw new DomainException('Họ và tên phải có ít nhất 2 ký tự.');
        }
        if (!preg_match('/^\d{9,11}$/', $phone)) {
            throw new DomainException('Số điện thoại phải gồm từ 9 đến 11 chữ số.');
        }
        if (mb_strlen($address, 'UTF-8') < 5) {
            throw new DomainException('Vui lòng nhập đầy đủ địa chỉ giao hàng (ít nhất 5 ký tự).');
        }
        if ($password !== '' && strlen($password) < 6) {
            throw new DomainException('Mật khẩu tùy chọn phải có ít nhất 6 ký tự (hoặc để trống nếu chỉ đăng nhập bằng Google).');
        }

        if ($existId > 0 && in_array($mode, ['link_existing', 'complete_missing'], true)) {
            if ($this->kiemTraTrungLapNgoaiTru('ten_dang_nhap', $username, $existId)) {
                throw new DomainException('Tên đăng nhập "' . $username . '" đã có người sử dụng. Vui lòng chọn tên khác.');
            }
        } else {
            if ($this->kiemTraTrungLap('ten_dang_nhap', $username)) {
                throw new DomainException('Tên đăng nhập "' . $username . '" đã có người sử dụng. Vui lòng chọn tên khác.');
            }
            if ($this->kiemTraTrungLap('thu_dien_tu', $email)) {
                throw new DomainException('Email "' . $email . '" đã tồn tại trong hệ thống.');
            }
        }

        if ($this->hasConnection()) {
            if ($existId > 0 && in_array($mode, ['link_existing', 'complete_missing'], true)) {
                if ($password !== '') {
                    $upd = $this->db->prepare("
                        UPDATE `hgb_nguoi_dung`
                        SET `ten_dang_nhap` = :uname,
                            `ho_va_ten` = :fname,
                            `so_dien_thoai` = :phone,
                            `dia_chi_nha` = :addr,
                            `mat_khau` = :pass,
                            `google_id` = :gid,
                            `avatar_url` = COALESCE(NULLIF(:av, ''), `avatar_url`),
                            `da_xac_thuc_otp` = 1
                        WHERE `ma_nguoi_dung` = :id
                    ");
                    $upd->execute([
                        ':uname' => $username,
                        ':fname' => mb_substr($fullname, 0, 100, 'UTF-8'),
                        ':phone' => $phone,
                        ':addr'  => mb_substr($address, 0, 255, 'UTF-8'),
                        ':pass'  => md5($password),
                        ':gid'   => $googleId,
                        ':av'    => $avatar,
                        ':id'    => $existId,
                    ]);
                } else {
                    $upd = $this->db->prepare("
                        UPDATE `hgb_nguoi_dung`
                        SET `ten_dang_nhap` = :uname,
                            `ho_va_ten` = :fname,
                            `so_dien_thoai` = :phone,
                            `dia_chi_nha` = :addr,
                            `google_id` = :gid,
                            `avatar_url` = COALESCE(NULLIF(:av, ''), `avatar_url`),
                            `da_xac_thuc_otp` = 1
                        WHERE `ma_nguoi_dung` = :id
                    ");
                    $upd->execute([
                        ':uname' => $username,
                        ':fname' => mb_substr($fullname, 0, 100, 'UTF-8'),
                        ':phone' => $phone,
                        ':addr'  => mb_substr($address, 0, 255, 'UTF-8'),
                        ':gid'   => $googleId,
                        ':av'    => $avatar,
                        ':id'    => $existId,
                    ]);
                }

                $stmt = $this->db->prepare("
                    SELECT u.*,
                           COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                           COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                      FROM `hgb_nguoi_dung` u
                      LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
                     WHERE u.`ma_nguoi_dung` = :id
                     LIMIT 1
                ");
                $stmt->execute([':id' => $existId]);
                $fresh = $stmt->fetch();
                $stmt->closeCursor();
                if ($fresh) {
                    unset($fresh['mat_khau']);
                    return $fresh;
                }
            }

            $defaultTierId = 1;
            try {
                $tVal = $this->db->query("SELECT `ma_hang` FROM `hgb_hang_thanh_vien` ORDER BY `diem_toi_thieu` ASC LIMIT 1")->fetchColumn();
                if ($tVal !== false && $tVal !== null) {
                    $defaultTierId = (int)$tVal;
                }
            } catch (PDOException $te) {}

            $finalPassMd5 = ($password !== '')
                ? md5($password)
                : md5('fk_google_' . $googleId . '_' . bin2hex(random_bytes(8)));

            $ins = $this->db->prepare("
                INSERT INTO `hgb_nguoi_dung`
                    (`ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`,
                     `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`, `vai_tro`, `da_xac_thuc_otp`,
                     `google_id`, `auth_provider`, `avatar_url`)
                VALUES
                    (:tier, :uname, :pass, :fname, :email, :phone, :addr, 0, 'khach_hang', 1, :gid, 'google', :av)
            ");
            $ins->execute([
                ':tier'  => $defaultTierId,
                ':uname' => $username,
                ':pass'  => $finalPassMd5,
                ':fname' => mb_substr($fullname, 0, 100, 'UTF-8'),
                ':email' => $email,
                ':phone' => $phone,
                ':addr'  => mb_substr($address, 0, 255, 'UTF-8'),
                ':gid'   => $googleId,
                ':av'    => $avatar !== '' ? $avatar : null,
            ]);
            $newUid = (int)$this->db->lastInsertId();

            return [
                'ma_nguoi_dung'      => $newUid,
                'ma_hang_thanh_vien' => $defaultTierId,
                'ten_dang_nhap'      => $username,
                'ho_va_ten'          => $fullname,
                'thu_dien_tu'        => $email,
                'so_dien_thoai'      => $phone,
                'dia_chi_nha'        => $address,
                'diem_tich_luy'      => 0,
                'vai_tro'            => 'khach_hang',
                'da_xac_thuc_otp'    => 1,
                'google_id'          => $googleId,
                'auth_provider'      => 'google',
                'avatar_url'         => $avatar,
                'ten_hang'           => 'Đồng (Bronze)',
                'phan_tram_giam_gia' => 0,
            ];
        }

        // Offline fallback session
        $targetId = ($existId > 0) ? $existId : (100 + count($_SESSION['hgb_registered_users'] ?? []));
        $userObj = [
            'ma_nguoi_dung'      => $targetId,
            'ma_hang_thanh_vien' => (int)($existing['ma_hang_thanh_vien'] ?? 1),
            'ten_dang_nhap'      => $username,
            'mat_khau'           => $password !== '' ? md5($password) : md5('fk_google_' . $googleId),
            'ho_va_ten'          => $fullname,
            'thu_dien_tu'        => $email,
            'so_dien_thoai'      => $phone,
            'dia_chi_nha'        => $address,
            'diem_tich_luy'      => (int)($existing['diem_tich_luy'] ?? 0),
            'vai_tro'            => (string)($existing['vai_tro'] ?? 'khach_hang'),
            'da_xac_thuc_otp'    => 1,
            'google_id'          => $googleId,
            'auth_provider'      => 'google',
            'avatar_url'         => $avatar,
            'ten_hang'           => (string)($existing['ten_hang'] ?? 'Đồng (Bronze)'),
            'phan_tram_giam_gia' => (float)($existing['phan_tram_giam_gia'] ?? 0),
        ];
        $_SESSION['hgb_registered_users'][$targetId] = $userObj;
        unset($userObj['mat_khau']);
        return $userObj;
    }

    /**
     * Đăng nhập hoặc tự động tạo tài khoản Khách hàng mới từ hồ sơ Google OAuth 2.0 (giữ tương thích ngược)
     */
    public function timHoacTaoNguoiDungGoogle(array $profile): array {
        $googleId = trim((string)($profile['google_id'] ?? ''));
        $email    = strtolower(trim((string)($profile['email'] ?? '')));
        $fullName = trim((string)($profile['name'] ?? 'Phụ huynh FashionKids'));
        $avatar   = trim((string)($profile['avatar'] ?? ''));

        if ($googleId === '' || $email === '') {
            throw new InvalidArgumentException('Thông tin tài khoản Google không đầy đủ.');
        }

        if ($this->hasConnection()) {
            $stmt = $this->db->prepare("
                SELECT u.*,
                       COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang,
                       COALESCE(h.phan_tram_giam_gia, 0)      AS phan_tram_giam_gia
                  FROM `hgb_nguoi_dung` u
                  LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
                 WHERE u.`google_id` = :gid OR LOWER(u.`thu_dien_tu`) = LOWER(:email)
                 LIMIT 1
            ");
            $stmt->execute([':gid' => $googleId, ':email' => $email]);
            $existing = $stmt->fetch();
            $stmt->closeCursor();

            if ($existing) {
                $uid = (int)$existing['ma_nguoi_dung'];
                $upd = $this->db->prepare("
                    UPDATE `hgb_nguoi_dung`
                    SET `google_id` = COALESCE(`google_id`, :gid),
                        `avatar_url` = COALESCE(NULLIF(:av, ''), `avatar_url`),
                        `da_xac_thuc_otp` = 1
                    WHERE `ma_nguoi_dung` = :id
                ");
                $upd->execute([':gid' => $googleId, ':av' => $avatar, ':id' => $uid]);

                $existing['google_id']  = $existing['google_id'] ?: $googleId;
                $existing['avatar_url'] = $avatar ?: ($existing['avatar_url'] ?? '');
                unset($existing['mat_khau']);
                return $existing;
            }

            // Tạo user khách hàng mới từ Google
            $baseSlug = preg_replace('/[^a-z0-9_]/', '', explode('@', $email)[0] ?: 'user');
            if (strlen($baseSlug) < 3) {
                $baseSlug = 'gguser';
            }
            $username = 'gg_' . substr($baseSlug, 0, 14) . '_' . substr(md5($googleId), 0, 5);
            while ($this->kiemTraTrungLap('ten_dang_nhap', $username)) {
                $username = 'gg_' . substr(md5($googleId . microtime(true)), 0, 10);
            }

            $defaultTierId = 1;
            try {
                $tVal = $this->db->query("SELECT `ma_hang` FROM `hgb_hang_thanh_vien` ORDER BY `diem_toi_thieu` ASC LIMIT 1")->fetchColumn();
                if ($tVal !== false && $tVal !== null) {
                    $defaultTierId = (int)$tVal;
                }
            } catch (PDOException $te) {}

            $randomMd5 = md5('fk_google_' . $googleId . '_' . bin2hex(random_bytes(8)));
            $ins = $this->db->prepare("
                INSERT INTO `hgb_nguoi_dung`
                    (`ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`,
                     `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`, `vai_tro`, `da_xac_thuc_otp`,
                     `google_id`, `auth_provider`, `avatar_url`)
                VALUES
                    (:tier, :uname, :pass, :fname, :email, NULL, '', 0, 'khach_hang', 1, :gid, 'google', :av)
            ");
            $ins->execute([
                ':tier'  => $defaultTierId,
                ':uname' => $username,
                ':pass'  => $randomMd5,
                ':fname' => mb_substr($fullName, 0, 100, 'UTF-8'),
                ':email' => $email,
                ':gid'   => $googleId,
                ':av'    => $avatar !== '' ? $avatar : null,
            ]);
            $newUid = (int)$this->db->lastInsertId();

            return [
                'ma_nguoi_dung'      => $newUid,
                'ma_hang_thanh_vien' => $defaultTierId,
                'ten_dang_nhap'      => $username,
                'ho_va_ten'          => $fullName,
                'thu_dien_tu'        => $email,
                'so_dien_thoai'      => '',
                'dia_chi_nha'        => '',
                'diem_tich_luy'      => 0,
                'vai_tro'            => 'khach_hang',
                'da_xac_thuc_otp'    => 1,
                'google_id'          => $googleId,
                'auth_provider'      => 'google',
                'avatar_url'         => $avatar,
                'ten_hang'           => 'Đồng (Bronze)',
                'phan_tram_giam_gia' => 0,
            ];
        }

        // Dự phòng offline session nếu DB chưa chạy
        $newId = 100 + count($_SESSION['hgb_registered_users'] ?? []);
        $userObj = [
            'ma_nguoi_dung'      => $newId,
            'ma_hang_thanh_vien' => 1,
            'ten_dang_nhap'      => 'gg_' . substr(md5($googleId), 0, 8),
            'ho_va_ten'          => $fullName,
            'thu_dien_tu'        => $email,
            'so_dien_thoai'      => '',
            'dia_chi_nha'        => '',
            'diem_tich_luy'      => 0,
            'vai_tro'            => 'khach_hang',
            'da_xac_thuc_otp'    => 1,
            'google_id'          => $googleId,
            'auth_provider'      => 'google',
            'avatar_url'         => $avatar,
            'ten_hang'           => 'Đồng (Bronze)',
            'phan_tram_giam_gia' => 0,
        ];
        $_SESSION['hgb_registered_users'][$newId] = $userObj;
        return $userObj;
    }
}
