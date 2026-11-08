<?php
/**
 * Lớp Nhân Viên (models/entities/NhanVien.php)
 * Kế thừa từ NguoiDung - Xử lý tiếp nhận đơn hàng, hỗ trợ chat CSKH và xét duyệt hồ sơ KOL
 */

require_once __DIR__ . '/NguoiDung.php';

class NhanVien extends NguoiDung {
    private string $maSoNhanVien;

    public function __construct(
        int $maNguoiDung = 0,
        string $tenDangNhap = '',
        string $matKhau = '',
        string $hoVaTen = '',
        string $thuDienTu = '',
        string $soDienThoai = '',
        string $diaChiNha = '',
        string $maSoNhanVien = 'NV-001'
    ) {
        parent::__construct(
            $maNguoiDung, $tenDangNhap, $matKhau, $hoVaTen, $thuDienTu, $soDienThoai, $diaChiNha, 'nhan_vien'
        );
        $this->maSoNhanVien = $maSoNhanVien;
    }

    public function getMaSoNhanVien(): string { return $this->maSoNhanVien; }
    public function setMaSoNhanVien(string $val): void { $this->maSoNhanVien = $val; }

    public function tiepNhanDonHang(int $maDonHang): bool {
        return $maDonHang > 0;
    }

    public function capNhatTrangThaiDon(int $maDonHang, string $trangThai): bool {
        return $maDonHang > 0 && !empty($trangThai);
    }

    public function traLoiKhachHang(int $maTinNhan, string $noiDung): bool {
        return $maTinNhan > 0 && !empty($noiDung);
    }

    public function duyetHoSoKOL(int $maHoSo, string $trangThai): bool {
        return $maHoSo > 0 && in_array($trangThai, ['da_duyet', 'tu_choi']);
    }
}
