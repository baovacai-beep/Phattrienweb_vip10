<?php
/**
 * Lớp Quản Trị Viên (models/entities/QuanTriVien.php)
 * Kế thừa từ NguoiDung - Toàn quyền quản trị kho hàng, danh mục, banner và cấu hình VIP
 */

require_once __DIR__ . '/NguoiDung.php';

class QuanTriVien extends NguoiDung {
    public function __construct(
        int $maNguoiDung = 0,
        string $tenDangNhap = '',
        string $matKhau = '',
        string $hoVaTen = '',
        string $thuDienTu = '',
        string $soDienThoai = '',
        string $diaChiNha = ''
    ) {
        parent::__construct(
            $maNguoiDung, $tenDangNhap, $matKhau, $hoVaTen, $thuDienTu, $soDienThoai, $diaChiNha, 'quan_tri'
        );
    }

    public function quanLySanPham(object $sanPham, string $thaoTac): bool {
        return in_array($thaoTac, ['them', 'sua', 'xoa', 'cap_nhat_kho']);
    }

    public function quanLyDanhMuc(object $danhMuc, string $thaoTac): bool {
        return in_array($thaoTac, ['them', 'sua', 'xoa']);
    }

    public function quanLyBanner(object $banner, string $thaoTac): bool {
        return in_array($thaoTac, ['them', 'sua', 'xoa', 'kich_hoat']);
    }

    public function cauHinhHangThanhVien(object $hang, float $phanTram): bool {
        return $phanTram >= 0 && $phanTram <= 100;
    }

    public function dangBaiVietTinTuc(object $baiViet): bool {
        return true;
    }

    public function kiemDuyetDanhGia(int $maDanhGia, bool $duyet): bool {
        return $maDanhGia > 0;
    }
}
