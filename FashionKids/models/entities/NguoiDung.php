<?php
/**
 * Lớp trừu tượng Người Dùng (models/entities/NguoiDung.php)
 * Đóng gói các thuộc tính cơ bản của một tài khoản trong hệ thống FashionKids.
 */

abstract class NguoiDung {
    protected int $maNguoiDung;
    protected string $tenDangNhap;
    protected string $matKhau;
    protected string $hoVaTen;
    protected string $thuDienTu;
    protected string $soDienThoai;
    protected string $diaChiNha;
    protected string $vaiTro;

    public function __construct(
        int $maNguoiDung = 0,
        string $tenDangNhap = '',
        string $matKhau = '',
        string $hoVaTen = '',
        string $thuDienTu = '',
        string $soDienThoai = '',
        string $diaChiNha = '',
        string $vaiTro = 'khach_hang'
    ) {
        $this->maNguoiDung = $maNguoiDung;
        $this->tenDangNhap = $tenDangNhap;
        $this->matKhau     = $matKhau;
        $this->hoVaTen     = $hoVaTen;
        $this->thuDienTu   = $thuDienTu;
        $this->soDienThoai = $soDienThoai;
        $this->diaChiNha   = $diaChiNha;
        $this->vaiTro      = $vaiTro;
    }

    // Encapsulation: Getters
    public function getMaNguoiDung(): int { return $this->maNguoiDung; }
    public function getTenDangNhap(): string { return $this->tenDangNhap; }
    public function getHoVaTen(): string { return $this->hoVaTen; }
    public function getThuDienTu(): string { return $this->thuDienTu; }
    public function getSoDienThoai(): string { return $this->soDienThoai; }
    public function getDiaChiNha(): string { return $this->diaChiNha; }
    public function getVaiTro(): string { return $this->vaiTro; }

    // Encapsulation: Setters
    public function setMaNguoiDung(int $val): void { $this->maNguoiDung = $val; }
    public function setTenDangNhap(string $val): void { $this->tenDangNhap = $val; }
    public function setMatKhau(string $val): void { $this->matKhau = $val; }
    public function setHoVaTen(string $val): void { $this->hoVaTen = $val; }
    public function setThuDienTu(string $val): void { $this->thuDienTu = $val; }
    public function setSoDienThoai(string $val): void { $this->soDienThoai = $val; }
    public function setDiaChiNha(string $val): void { $this->diaChiNha = $val; }
    public function setVaiTro(string $val): void { $this->vaiTro = $val; }

    /**
     * Xác thực thông tin đăng nhập với mật khẩu mã hóa MD5
     */
    public function dangNhap(string $tenDn, string $matKhau): bool {
        $tenClean = trim($tenDn);
        $stored   = trim($this->matKhau);
        $md5Pass  = md5($matKhau);
        $matchUser = (strcasecmp($this->tenDangNhap, $tenClean) === 0 || ($this->thuDienTu !== '' && strcasecmp($this->thuDienTu, $tenClean) === 0));
        $matchPass = ($stored !== '') && (
            strcasecmp($stored, $md5Pass) === 0 ||
            strcasecmp($stored, md5($md5Pass)) === 0 ||
            password_verify($matKhau, $stored)
        );

        if ($matchUser && $matchPass) {
            $_SESSION['user_id']   = $this->maNguoiDung;
            $_SESSION['username']  = $this->tenDangNhap;
            $_SESSION['full_name'] = $this->hoVaTen;
            $_SESSION['role']      = $this->vaiTro;
            return true;
        }
        return false;
    }

    /**
     * Hủy phiên làm việc người dùng
     */
    public function dangXuat(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name'], $_SESSION['role']);
        }
    }

    /**
     * Cập nhật thông tin hồ sơ cá nhân
     */
    public function capNhatHoSo(string $hoTen, string $sdt, string $diaChi): bool {
        $this->hoVaTen     = $hoTen;
        $this->soDienThoai = $sdt;
        $this->diaChiNha   = $diaChi;
        return true;
    }
}
