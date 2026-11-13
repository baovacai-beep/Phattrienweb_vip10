-- =========================================================================
-- HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ THỜI TRANG TRẺ EM FASHIONKIDS
-- BƯỚC 1: LÀM SẠCH THEO THỨ TỰ TOPO (BẢNG CON XÓA TRƯỚC, BẢNG CHA XÓA SAU)
-- VÀ KHỞI TẠO 15 BẢNG CÙNG SEED DATA CHUẨN HÓA 3NF TRÊN PHPMYADMIN
-- =========================================================================

CREATE DATABASE IF NOT EXISTS `db_fashionkids` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_fashionkids`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. XÓA BẢNG CON TRƯỚC (Tránh lỗi #1451 tuyệt đối)
DROP TABLE IF EXISTS `tgb_tep_tin_danh_gia`;
DROP TABLE IF EXISTS `tgb_luot_thich_danh_gia`;
DROP TABLE IF EXISTS `tgb_danh_gia_san_pham`;
DROP TABLE IF EXISTS `tgb_binh_luan_bai_viet`;
DROP TABLE IF EXISTS `tgb_bai_viet_tin_tuc`;
DROP TABLE IF EXISTS `tgb_tin_nhan_tu_van`;
DROP TABLE IF EXISTS `tgb_chi_tiet_don_hang`;
DROP TABLE IF EXISTS `lnkt_yeu_cau_hoan_hang`;
DROP TABLE IF EXISTS `lnkt_ma_giam_gia`;
DROP TABLE IF EXISTS `lnkt_dang_ky_kol_nhi`;
DROP TABLE IF EXISTS `lnkt_don_hang`;
DROP TABLE IF EXISTS `lnkt_banner_su_kien`;
DROP TABLE IF EXISTS `lnkt_bang_kich_co`;
DROP TABLE IF EXISTS `lbn_san_pham`;
DROP TABLE IF EXISTS `lbn_danh_muc`;
DROP TABLE IF EXISTS `hgb_nguoi_dung`;
DROP TABLE IF EXISTS `hgb_hang_thanh_vien`;

-- 2. TẠO 15 BẢNG THỰC THỂ
CREATE TABLE `hgb_hang_thanh_vien` (
    `ma_hang` INT AUTO_INCREMENT PRIMARY KEY,
    `ten_hang` VARCHAR(50) NOT NULL,
    `diem_toi_thieu` INT NOT NULL DEFAULT 0,
    `phan_tram_giam_gia` FLOAT NOT NULL DEFAULT 0,
    `mo_ta_quyen_loi` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hgb_nguoi_dung` (
    `ma_nguoi_dung` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_hang_thanh_vien` INT NULL,
    `ten_dang_nhap` VARCHAR(50) NOT NULL UNIQUE,
    `mat_khau` VARCHAR(255) NOT NULL,
    `ho_va_ten` VARCHAR(100) NOT NULL,
    `thu_dien_tu` VARCHAR(100) NOT NULL UNIQUE,
    `so_dien_thoai` VARCHAR(20) NULL,
    `dia_chi_nha` VARCHAR(255) NULL,
    `diem_tich_luy` INT NOT NULL DEFAULT 0,
    `vai_tro` ENUM('quan_tri', 'nhan_vien', 'khach_hang') NOT NULL DEFAULT 'khach_hang',
    `da_xac_thuc_otp` TINYINT(1) NOT NULL DEFAULT 0,
    `google_id` VARCHAR(100) NULL DEFAULT NULL UNIQUE,
    `auth_provider` ENUM('local', 'google') NOT NULL DEFAULT 'local',
    `avatar_url` VARCHAR(255) NULL DEFAULT NULL,
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_nguoi_dung_hang` 
        FOREIGN KEY (`ma_hang_thanh_vien`) REFERENCES `hgb_hang_thanh_vien`(`ma_hang`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lbn_danh_muc` (
    `ma_danh_muc` INT AUTO_INCREMENT PRIMARY KEY,
    `ten_danh_muc` VARCHAR(100) NOT NULL,
    `gioi_tinh` ENUM('be_trai', 'be_gai', 'chung') NOT NULL DEFAULT 'chung',
    `loai_hang` ENUM('quan_ao', 'giay_dep') NOT NULL DEFAULT 'quan_ao'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lbn_san_pham` (
    `ma_san_pham` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_danh_muc` INT NOT NULL,
    `ten_san_pham` VARCHAR(255) NOT NULL,
    `gia_ban` DECIMAL(10,2) NOT NULL,
    `so_luong_ton_kho` INT NOT NULL DEFAULT 10,
    `duong_dan_anh` VARCHAR(255) NOT NULL,
    `danh_sach_anh` TEXT NULL,
    `danh_sach_mau` TEXT NULL,
    `mo_ta_chat_lieu` TEXT NULL,
    `loai_kich_co` ENUM('quan_ao', 'giay_dep') NOT NULL DEFAULT 'quan_ao',
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_san_pham_danh_muc` 
        FOREIGN KEY (`ma_danh_muc`) REFERENCES `lbn_danh_muc`(`ma_danh_muc`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lnkt_bang_kich_co` (
    `ma_kich_co` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_danh_muc` INT NULL,
    `loai_hang` ENUM('quan_ao', 'giay_dep') NOT NULL DEFAULT 'quan_ao',
    `gioi_tinh` ENUM('be_trai', 'be_gai', 'chung') NOT NULL DEFAULT 'chung',
    `ten_kich_co` VARCHAR(20) NOT NULL,
    `chieu_cao_toi_thieu` FLOAT NULL,
    `chieu_cao_toi_da` FLOAT NULL,
    `vong_nguc_toi_thieu` FLOAT NULL,
    `vong_nguc_toi_da` FLOAT NULL,
    `vong_eo_toi_thieu` FLOAT NULL,
    `vong_eo_toi_da` FLOAT NULL,
    `vong_mong_toi_thieu` FLOAT NULL,
    `vong_mong_toi_da` FLOAT NULL,
    `chieu_dai_chan_toi_thieu` FLOAT NULL,
    `chieu_dai_chan_toi_da` FLOAT NULL,
    CONSTRAINT `kn_kich_co_danh_muc` 
        FOREIGN KEY (`ma_danh_muc`) REFERENCES `lbn_danh_muc`(`ma_danh_muc`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lnkt_don_hang` (
    `ma_don_hang` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_nguoi_dung` INT NULL,
    `ma_nhan_vien_xu_ly` INT NULL,
    `ten_nguoi_nhan` VARCHAR(100) NOT NULL,
    `so_dien_thoai_nhan` VARCHAR(20) NOT NULL,
    `thu_dien_tu_nhan` VARCHAR(100) NULL,
    `dia_chi_giao_hang` VARCHAR(255) NOT NULL,
    `tinh_thanh_pho` VARCHAR(100) NOT NULL,
    `quan_huyen` VARCHAR(100) NOT NULL,
    `phuong_xa` VARCHAR(100) NULL DEFAULT NULL,
    `dich_vu_van_chuyen` VARCHAR(30) NOT NULL DEFAULT 'standard',
    `tien_hang_tam_tinh` DECIMAL(12,2) NOT NULL,
    `tien_giam_gia` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `phi_van_chuyen` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `trong_luong_gram` INT NOT NULL DEFAULT 300,
    `ma_voucher` VARCHAR(50) NULL DEFAULT NULL,
    `tien_giam_voucher` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `tong_tien_thanh_toan` DECIMAL(12,2) NOT NULL,
    `phuong_thuc_thanh_toan` ENUM('cod', 'vietqr') NOT NULL DEFAULT 'cod',
    `trang_thai_thanh_toan` ENUM('chua_thanh_toan', 'cho_chuyen_khoan', 'da_thanh_toan') NOT NULL DEFAULT 'chua_thanh_toan',
    `ma_giao_dich_ngan_hang` VARCHAR(100) NULL DEFAULT NULL,
    `cac_ma_giao_dich` TEXT NULL DEFAULT NULL,
    `so_tien_da_nhan` DECIMAL(12,2) NULL DEFAULT NULL,
    `thoi_gian_thanh_toan` DATETIME NULL DEFAULT NULL,
    `han_thanh_toan` DATETIME NULL DEFAULT NULL,
    `noi_dung_chuyen_khoan` VARCHAR(50) NULL DEFAULT NULL,
    `ghi_chu` VARCHAR(255) NULL,
    `don_vi_van_chuyen` VARCHAR(50) NULL DEFAULT NULL,
    `ma_van_don_hang_vc` VARCHAR(100) NULL DEFAULT NULL,
    `trang_thai_don_hang` ENUM('cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy') NOT NULL DEFAULT 'cho_duyet',
    `ngay_dat_hang` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_lnkt_ma_gd_ngan_hang` (`ma_giao_dich_ngan_hang`),
    CONSTRAINT `kn_don_hang_nguoi_dung` 
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `kn_don_hang_nhan_vien` 
        FOREIGN KEY (`ma_nhan_vien_xu_ly`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lnkt_yeu_cau_hoan_hang` (
    `ma_hoan_hang` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_don_hang` INT NOT NULL,
    `ma_nguoi_dung` INT NOT NULL,
    `ma_nhan_vien_xu_ly` INT NULL,
    `ly_do_hoan` VARCHAR(255) NOT NULL,
    `mo_ta_chi_tiet` TEXT NULL,
    `hinh_anh_minh_chung` TEXT NULL,
    `so_tien_hoan` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `thong_tin_nhan_tien` VARCHAR(255) NULL,
    `trang_thai_hoan` ENUM('cho_duyet_hoan', 'da_duyet_hoan', 'dang_thu_hoi', 'hoan_tat_hoan', 'tu_choi_hoan') NOT NULL DEFAULT 'cho_duyet_hoan',
    `ghi_chu_xu_ly` TEXT NULL,
    `ngay_yeu_cau` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_lnkt_hoan_don_hang` (`ma_don_hang`),
    CONSTRAINT `kn_hoan_don_hang`
        FOREIGN KEY (`ma_don_hang`) REFERENCES `lnkt_don_hang`(`ma_don_hang`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_hoan_nguoi_dung`
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_hoan_nhan_vien`
        FOREIGN KEY (`ma_nhan_vien_xu_ly`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lnkt_ma_giam_gia` (
    `ma_voucher` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_code` VARCHAR(50) NOT NULL UNIQUE,
    `mo_ta` VARCHAR(255) NOT NULL,
    `loai_giam` ENUM('percent', 'fixed', 'freeship') NOT NULL DEFAULT 'fixed',
    `gia_tri_giam` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `giam_toi_da` DECIMAL(12,2) NULL DEFAULT NULL,
    `don_toi_thieu` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `gioi_han_luot` INT NULL DEFAULT NULL,
    `da_su_dung` INT NOT NULL DEFAULT 0,
    `ngay_bat_dau` DATETIME NULL DEFAULT NULL,
    `ngay_het_han` DATETIME NULL DEFAULT NULL,
    `trang_thai` TINYINT(1) NOT NULL DEFAULT 1,
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lnkt_banner_su_kien` (
    `ma_banner` INT AUTO_INCREMENT PRIMARY KEY,
    `tieu_de` VARCHAR(255) NOT NULL,
    `duong_dan_anh` VARCHAR(255) NOT NULL,
    `duong_dan_dieu_huong` VARCHAR(255) NOT NULL,
    `loai_su_kien` ENUM('theo_mua', 'tuyen_chon_kol', 'khuyen_mai') NOT NULL DEFAULT 'theo_mua',
    `ngay_bat_dau` DATETIME NOT NULL,
    `ngay_ket_thuc` DATETIME NOT NULL,
    `dang_kich_hoat` TINYINT(1) NOT NULL DEFAULT 1,
    `thu_tu_hien_thi` INT NOT NULL DEFAULT 0,
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lnkt_dang_ky_kol_nhi` (
    `ma_ho_so` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_nguoi_dung` INT NULL,
    `ho_ten_phu_huynh` VARCHAR(100) NOT NULL,
    `so_dien_thoai` VARCHAR(20) NOT NULL,
    `thu_dien_tu` VARCHAR(100) NOT NULL,
    `ho_ten_be` VARCHAR(100) NOT NULL,
    `ngay_sinh_be` DATE NOT NULL,
    `gioi_tinh_be` ENUM('be_trai', 'be_gai') NOT NULL,
    `chieu_cao_be` FLOAT NOT NULL,
    `can_nang_be` FLOAT NOT NULL,
    `danh_sach_anh` TEXT NOT NULL,
    `nang_khieu_kinh_nghiem` TEXT NULL,
    `trang_thai_duyet` ENUM('cho_duyet', 'moi_phong_van', 'trung_tuyen', 'tu_choi') NOT NULL DEFAULT 'cho_duyet',
    `ngay_nop_ho_so` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_kol_nguoi_dung` 
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_chi_tiet_don_hang` (
    `ma_chi_tiet_don` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_don_hang` INT NOT NULL,
    `ma_san_pham` INT NOT NULL,
    `kich_co_chon` VARCHAR(20) NOT NULL,
    `so_luong` INT NOT NULL DEFAULT 1,
    `don_gia_mua` DECIMAL(10,2) NOT NULL,
    CONSTRAINT `kn_chi_tiet_don_hang` 
        FOREIGN KEY (`ma_don_hang`) REFERENCES `lnkt_don_hang`(`ma_don_hang`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_chi_tiet_san_pham` 
        FOREIGN KEY (`ma_san_pham`) REFERENCES `lbn_san_pham`(`ma_san_pham`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_tin_nhan_tu_van` (
    `ma_tin_nhan` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_phien_khach` VARCHAR(100) NOT NULL,
    `ma_nguoi_dung` INT NULL,
    `nguoi_gui` ENUM('khach_hang', 'nhan_vien', 'quan_tri') NOT NULL,
    `noi_dung_tin_nhan` TEXT NOT NULL,
    `da_xem` TINYINT(1) NOT NULL DEFAULT 0,
    `ngay_gui` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_chat_nguoi_dung` 
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_bai_viet_tin_tuc` (
    `ma_bai_viet` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_tac_gia` INT NOT NULL,
    `tieu_de` VARCHAR(255) NOT NULL,
    `duong_dan_tinh` VARCHAR(255) NOT NULL UNIQUE,
    `anh_dai_dien` VARCHAR(255) NOT NULL,
    `tom_tat_ngan` VARCHAR(500) NOT NULL,
    `noi_dung_chi_tiet` LONGTEXT NOT NULL,
    `so_luot_thich` INT NOT NULL DEFAULT 0,
    `so_luot_xem` INT NOT NULL DEFAULT 0,
    `trang_thai_bai_viet` ENUM('ban_nhap', 'xuat_ban', 'luu_tru') NOT NULL DEFAULT 'xuat_ban',
    `ngay_dang` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_bai_viet_tac_gia` 
        FOREIGN KEY (`ma_tac_gia`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_binh_luan_bai_viet` (
    `ma_binh_luan` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_bai_viet` INT NOT NULL,
    `ma_nguoi_dung` INT NOT NULL,
    `ma_binh_luan_cha` INT NULL,
    `noi_dung_binh_luan` TEXT NOT NULL,
    `ngay_binh_luan` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_binh_luan_bai_viet` 
        FOREIGN KEY (`ma_bai_viet`) REFERENCES `tgb_bai_viet_tin_tuc`(`ma_bai_viet`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_binh_luan_nguoi_dung` 
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_binh_luan_cha` 
        FOREIGN KEY (`ma_binh_luan_cha`) REFERENCES `tgb_binh_luan_bai_viet`(`ma_binh_luan`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_danh_gia_san_pham` (
    `ma_danh_gia` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_san_pham` INT NOT NULL,
    `ma_nguoi_dung` INT NOT NULL,
    `ma_don_hang` INT NOT NULL,
    `so_sao_danh_gia` TINYINT NOT NULL CHECK (`so_sao_danh_gia` BETWEEN 1 AND 5),
    `noi_dung_danh_gia` TEXT NOT NULL,
    `so_luot_huu_ich` INT NOT NULL DEFAULT 0,
    `ngay_danh_gia` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `kn_danh_gia_san_pham` 
        FOREIGN KEY (`ma_san_pham`) REFERENCES `lbn_san_pham`(`ma_san_pham`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_danh_gia_nguoi_dung` 
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_danh_gia_don_hang` 
        FOREIGN KEY (`ma_don_hang`) REFERENCES `lnkt_don_hang`(`ma_don_hang`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_tep_tin_danh_gia` (
    `ma_tep_tin` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_danh_gia` INT NOT NULL,
    `loai_tep_tin` ENUM('hinh_anh', 'video') NOT NULL DEFAULT 'hinh_anh',
    `duong_dan_tep_tin` VARCHAR(255) NOT NULL,
    CONSTRAINT `kn_tep_tin_danh_gia` 
        FOREIGN KEY (`ma_danh_gia`) REFERENCES `tgb_danh_gia_san_pham`(`ma_danh_gia`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tgb_luot_thich_danh_gia` (
    `ma_nguoi_dung` INT NOT NULL,
    `ma_danh_gia` INT NOT NULL,
    `ngay_thich` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ma_nguoi_dung`, `ma_danh_gia`),
    CONSTRAINT `kn_thich_nguoi_dung` 
        FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung`(`ma_nguoi_dung`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `kn_thich_danh_gia` 
        FOREIGN KEY (`ma_danh_gia`) REFERENCES `tgb_danh_gia_san_pham`(`ma_danh_gia`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. NẠP SEED DATA KHÔNG ĐỤNG ĐỘ
INSERT INTO `hgb_hang_thanh_vien` (`ma_hang`, `ten_hang`, `diem_toi_thieu`, `phan_tram_giam_gia`, `mo_ta_quyen_loi`) VALUES
(1, 'Đồng (Bronze)', 0, 0, 'Hạng khởi đầu mặc định khi đăng ký tài khoản'),
(2, 'Bạc (Silver)', 500, 5, 'Tích lũy từ 500 điểm, chiết khấu 5% trên mọi đơn hàng'),
(3, 'Vàng (Gold)', 1500, 10, 'Tích lũy từ 1500 điểm, chiết khấu 10% trên mọi đơn hàng'),
(4, 'Kim Cương (Diamond)', 3000, 15, 'Tích lũy từ 3000 điểm, chiết khấu 15% đặc quyền VIP')
ON DUPLICATE KEY UPDATE `ten_hang`=VALUES(`ten_hang`);

INSERT INTO `hgb_nguoi_dung` (`ma_nguoi_dung`, `ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`, `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`, `vai_tro`, `da_xac_thuc_otp`) VALUES
(1, NULL, 'admin_fashion', 'e10adc3949ba59abbe56e057f20f883e', 'Hồ Gia Bảo', 'admin@fashionkids.vn', '0901234567', 'Quận 1, TP.HCM', 0, 'quan_tri', 1),
(2, NULL, 'staff_tuvan', 'e10adc3949ba59abbe56e057f20f883e', 'Lê Nguyễn Khánh Trình', 'staff@fashionkids.vn', '0907654321', 'Quận 3, TP.HCM', 0, 'nhan_vien', 1),
(3, 3, 'me_be_sam', 'e10adc3949ba59abbe56e057f20f883e', 'Võ Thị Thanh Thảo', 'thanhthao@gmail.com', '0918889999', 'Số 120 Điện Biên Phủ, Bình Thạnh', 1600, 'khach_hang', 1)
ON DUPLICATE KEY UPDATE `ho_va_ten`=VALUES(`ho_va_ten`), `mat_khau`=VALUES(`mat_khau`);

INSERT INTO `lbn_danh_muc` (`ma_danh_muc`, `ten_danh_muc`, `gioi_tinh`, `loai_hang`) VALUES
(1, 'Váy đầm công chúa bé gái', 'be_gai', 'quan_ao'),
(2, 'Bộ quần áo thun thể thao bé trai', 'be_trai', 'quan_ao'),
(3, 'Áo khoác dạo phố năng động', 'chung', 'quan_ao'),
(4, 'Giày sandal tập đi êm chân', 'chung', 'giay_dep')
ON DUPLICATE KEY UPDATE `ten_danh_muc`=VALUES(`ten_danh_muc`);

INSERT INTO `lnkt_bang_kich_co` 
(`ma_kich_co`, `ma_danh_muc`, `loai_hang`, `gioi_tinh`, `ten_kich_co`, `chieu_cao_toi_thieu`, `chieu_cao_toi_da`, `vong_nguc_toi_thieu`, `vong_nguc_toi_da`, `vong_eo_toi_thieu`, `vong_eo_toi_da`, `vong_mong_toi_thieu`, `vong_mong_toi_da`, `chieu_dai_chan_toi_thieu`, `chieu_dai_chan_toi_da`) 
VALUES
(1, 1, 'quan_ao', 'be_gai', 'Size 1 (1-2T)', 80, 90, 48, 52, 46, 50, 50, 54, NULL, NULL),
(2, 1, 'quan_ao', 'be_gai', 'Size 2 (2-3T)', 90, 100, 52, 56, 50, 53, 54, 58, NULL, NULL),
(3, 2, 'quan_ao', 'be_trai', 'Size 3 (3-4T)', 100, 110, 56, 60, 53, 55, 58, 62, NULL, NULL),
(4, 2, 'quan_ao', 'be_trai', 'Size 4 (4-5T)', 110, 120, 60, 64, 55, 57, 62, 66, NULL, NULL),
(5, 4, 'giay_dep', 'chung', 'Size 24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14.0, 14.9),
(6, 4, 'giay_dep', 'chung', 'Size 26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15.0, 15.9),
(7, 4, 'giay_dep', 'chung', 'Size 28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16.0, 16.9)
ON DUPLICATE KEY UPDATE `ten_kich_co`=VALUES(`ten_kich_co`);

INSERT INTO `lbn_san_pham` (`ma_san_pham`, `ma_danh_muc`, `ten_san_pham`, `gia_ban`, `so_luong_ton_kho`, `duong_dan_anh`, `mo_ta_chat_lieu`, `loai_kich_co`) VALUES
(1, 1, 'Váy Công Chúa Elsa Bạch Tuyết Cao Cấp', 450000.00, 25, 'vay_cong_chua_elsa.jpg', 'Vải cotton sợi tre 100% hữu cơ, kháng khuẩn, không gây ngứa da', 'quan_ao'),
(2, 2, 'Bộ Thun Thể Thao Khủng Long Năng Động', 280000.00, 30, 'bo_thun_khung_long.jpg', 'Chất cotton 4 chiều co giãn thoáng mồ hôi cho bé trai vận động', 'quan_ao'),
(3, 3, 'Áo Khoác Gió Bomber Trẻ Em 2 Lớp', 390000.00, 20, 'ao_khoac_bomber.jpg', 'Chất vải dù cản gió chống nước nhẹ, giữ ấm ngày mưa gió', 'quan_ao'),
(4, 4, 'Giày Sandal Tập Đi Êm Ái Chống Trượt', 320000.00, 15, 'sandal_tap_di.jpg', 'Đế cao su non uốn dẻo, bảo vệ xương bàn chân non nớt của bé', 'giay_dep'),
(101, 1, 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi', 285000.00, 18, '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg', 'Vải cotton sợi tre 100% hữu cơ, kháng khuẩn, không gây ngứa da', 'quan_ao'),
(102, 2, 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động', 225000.00, 24, '/FashionKids/images/bo-thun-khung-long.jpg', 'Chất sợi tre 4 chiều co giãn thoáng mồ hôi cho bé trai vận động', 'quan_ao'),
(103, 4, 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân', 210000.00, 14, '/FashionKids/images/giay-sandal-tap-di.jpg', 'Đế cao su non uốn dẻo, bảo vệ xương bàn chân non nớt của bé', 'giay_dep'),
(104, 3, 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV', 320000.00, 5, '/FashionKids/images/ao-khoac-cardigan.jpg', 'Sợi tre dệt kim UPF 50+ chống tia cực tím, cúc gỗ tự nhiên', 'quan_ao'),
(105, 1, 'Váy Công Chúa Tùng Xòe Pastel Nơ Bản To Dự Tiệc', 365000.00, 11, '/FashionKids/images/vay-cong-chua-no-pastel.jpg', 'Lớp lót 100% sợi tre kháng khuẩn phối voan tùng xòe dự tiệc', 'quan_ao'),
(106, 2, 'Bộ Thun Sợi Tre Cộc Tay Gấu Nhí Thoáng Khí Mùa Hè', 195000.00, 32, '/FashionKids/images/bo-thun-gau-nhi.jpg', '100% sợi tre tự nhiên làm mát, thêu gấu nhí đáng yêu', 'quan_ao'),
(107, 3, 'Áo Khoác Hoodie Tai Thỏ Sợi Tre Ấm Áp Cho Bé Gái', 340000.00, 4, '/FashionKids/images/ao-khoac-hoodie-tai-tho.jpg', 'Nỉ bông sợi tre hữu cơ kháng khuẩn, mũ phối tai thỏ', 'quan_ao'),
(108, 4, 'Giày Sandal Bé Trai Quai Dán Chống Trượt Siêu Nhẹ', 245000.00, 16, '/FashionKids/images/giay-sandal-be-trai.jpg', 'Quai vải canvas phối sợi tre êm cổ chân, đế rãnh chống trượt', 'giay_dep'),
(109, 3, 'Áo Khoác Bomber Sợi Tre Phi Hành Gia Nhí Bé Trai', 350000.00, 9, '/FashionKids/images/ao-khoac-bomber.jpg', 'Chất vải sợi tre 2 lớp cản gió, huy hiệu phi hành gia thêu nổi', 'quan_ao')
ON DUPLICATE KEY UPDATE `ten_san_pham`=VALUES(`ten_san_pham`);

INSERT INTO `lnkt_don_hang` (`ma_don_hang`, `ma_nguoi_dung`, `ma_nhan_vien_xu_ly`, `ten_nguoi_nhan`, `so_dien_thoai_nhan`, `thu_dien_tu_nhan`, `dia_chi_giao_hang`, `tinh_thanh_pho`, `quan_huyen`, `tien_hang_tam_tinh`, `tien_giam_gia`, `tong_tien_thanh_toan`, `trang_thai_don_hang`) VALUES
(1, 3, 2, 'Võ Thị Thanh Thảo', '0918889999', 'thanhthao@gmail.com', 'Số 120 Điện Biên Phủ, P.17', 'Thành phố Hồ Chí Minh', 'Quận Bình Thạnh', 900000.00, 90000.00, 810000.00, 'hoan_tat'),
(2, 3, NULL, 'Võ Thị Thanh Thảo', '0918889999', 'thanhthao@gmail.com', 'Số 120 Điện Biên Phủ, P.17', 'Thành phố Hồ Chí Minh', 'Quận Bình Thạnh', 280000.00, 28000.00, 252000.00, 'cho_duyet')
ON DUPLICATE KEY UPDATE `ten_nguoi_nhan`=VALUES(`ten_nguoi_nhan`);

INSERT INTO `tgb_chi_tiet_don_hang` (`ma_chi_tiet_don`, `ma_don_hang`, `ma_san_pham`, `kich_co_chon`, `so_luong`, `don_gia_mua`) VALUES
(1, 1, 1, 'Size 2 (2-3T)', 2, 450000.00),
(2, 2, 2, 'Size 3 (3-4T)', 1, 280000.00)
ON DUPLICATE KEY UPDATE `don_gia_mua`=VALUES(`don_gia_mua`);

INSERT INTO `lnkt_ma_giam_gia` (`ma_code`, `mo_ta`, `loai_giam`, `gia_tri_giam`, `giam_toi_da`, `don_toi_thieu`, `gioi_han_luot`, `da_su_dung`, `ngay_bat_dau`, `ngay_het_han`, `trang_thai`) VALUES
('FASHION10', 'Giảm 10% tối đa 50.000đ cho đơn từ 200.000đ', 'percent', 10.00, 50000.00, 200000.00, 500, 0, '2025-01-01 00:00:00', '2028-12-31 23:59:59', 1),
('SOITRE30K', 'Giảm trực tiếp 30.000đ cho đơn từ 250.000đ', 'fixed', 30000.00, 30000.00, 250000.00, 300, 0, '2025-01-01 00:00:00', '2028-12-31 23:59:59', 1),
('FREESHIPFK', 'Miễn phí vận chuyển GHN (tối đa 35.000đ) đơn từ 150.000đ', 'freeship', 35000.00, 35000.00, 150000.00, 1000, 0, '2025-01-01 00:00:00', '2028-12-31 23:59:59', 1)
ON DUPLICATE KEY UPDATE `mo_ta`=VALUES(`mo_ta`);

INSERT INTO `lnkt_banner_su_kien` (`ma_banner`, `tieu_de`, `duong_dan_anh`, `duong_dan_dieu_huong`, `loai_su_kien`, `ngay_bat_dau`, `ngay_ket_thuc`, `dang_kich_hoat`, `thu_tu_hien_thi`) VALUES
(1, 'Bộ sưu tập Mùa Hè 2026 - Giảm đến 30%', 'banner_mua_he.jpg', '/danh-muc/mua-he', 'theo_mua', '2026-06-01 00:00:00', '2026-08-31 23:59:59', 1, 1),
(2, 'Tìm Kiếm Ngôi Sao Nhí KOL FashionKids', 'banner_kol_nhi.jpg', '/tuyen-chon-kol', 'tuyen_chon_kol', '2026-06-15 00:00:00', '2026-09-15 23:59:59', 1, 2)
ON DUPLICATE KEY UPDATE `tieu_de`=VALUES(`tieu_de`);

INSERT INTO `lnkt_dang_ky_kol_nhi` (`ma_ho_so`, `ma_nguoi_dung`, `ho_ten_phu_huynh`, `so_dien_thoai`, `thu_dien_tu`, `ho_ten_be`, `ngay_sinh_be`, `gioi_tinh_be`, `chieu_cao_be`, `can_nang_be`, `danh_sach_anh`, `nang_khieu_kinh_nghiem`, `trang_thai_duyet`) VALUES
(1, 3, 'Võ Thị Thanh Thảo', '0918889999', 'thanhthao@gmail.com', 'Bé Hồ Bảo Sam (Sam)', '2022-05-15', 'be_gai', 96.5, 14.2, '["/FashionKids/images/kol/kol-bao-sam.jpg", "/FashionKids/images/kol/kol-casting-studio-demo.jpg"]', 'Bé dạn dĩ trước máy ảnh, thích tạo dáng công chúa lookbook', 'cho_duyet'),
(2, 3, 'Anh Hoàng Nam', '0912445566', 'hoangnam@gmail.com', 'Bé Trần Gia Huy (Ken)', '2024-02-10', 'be_trai', 88.0, 12.5, '["/FashionKids/images/kol/kol-gia-huy-ken.jpg"]', 'Cười tươi tỏa nắng, năng động trong trang phục bộ thun khủng long', 'moi_phong_van'),
(3, 3, 'Chị Thanh Hà', '0989778899', 'thanhha@gmail.com', 'Bé Lê Hà My (Bông)', '2021-04-20', 'be_gai', 112.0, 19.2, '["/FashionKids/images/kol/kol-ha-my-bong.jpg"]', 'Catwalk thiếu nhi chuyên nghiệp, từng chụp Lookbook thời trang', 'trung_tuyen'),
(4, 3, 'Chị Minh Thư', '0908112233', 'minhthu@gmail.com', 'Bé Nguyễn Bảo An (Sữa)', '2023-05-15', 'be_gai', 96.0, 14.5, '["/FashionKids/images/kol/kol-bao-an-sua.jpg"]', 'Biểu cảm rạng rỡ tự nhiên, hợp các bộ sưu tập đầm hè sợi tre Bright Pastel', 'cho_duyet'),
(5, 3, 'Chị Lan Hương', '0934556677', 'lanhuong@gmail.com', 'Bé Trần Minh Khôi (Bơ)', '2021-11-08', 'be_trai', 108.0, 17.5, '["/FashionKids/images/kol/kol-minh-khoi-bo.jpg"]', 'Phong thái mini-runway tự tin, phối đồ áo cardigan sợi tre thanh lịch', 'trung_tuyen'),
(6, 3, 'Chị Thu Trang', '0977223344', 'thutrang@gmail.com', 'Bé Phạm Thảo Chi (Mây)', '2023-08-20', 'be_gai', 94.0, 13.8, '["/FashionKids/images/kol/kol-thien-than-hoa-cuc.jpg"]', 'Nét dịu dàng trong veo, hợp chụp Lookbook váy công chúa cổ sen hoa cúc', 'cho_duyet')
ON DUPLICATE KEY UPDATE `ho_ten_phu_huynh`=VALUES(`ho_ten_phu_huynh`), `danh_sach_anh`=VALUES(`danh_sach_anh`);

INSERT INTO `tgb_tin_nhan_tu_van` (`ma_tin_nhan`, `ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`) VALUES
(1, 'sess_guest_123456', NULL, 'khach_hang', 'Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa size mấy thì vừa ạ?', 1),
(2, 'sess_guest_123456', 2, 'nhan_vien', 'Dạ chào bạn, với số đo 92cm bé mặc Size 2 (2-3T) là vừa vặn và thoải mái nhất ạ!', 1)
ON DUPLICATE KEY UPDATE `noi_dung_tin_nhan`=VALUES(`noi_dung_tin_nhan`);

INSERT INTO `tgb_bai_viet_tin_tuc` (`ma_bai_viet`, `ma_tac_gia`, `tieu_de`, `duong_dan_tinh`, `anh_dai_dien`, `tom_tat_ngan`, `noi_dung_chi_tiet`, `so_luot_thich`, `so_luot_xem`, `trang_thai_bai_viet`) VALUES
(1, 1, 'Top 5 Cách Phối Đồ Cho Bé Gái Đi Tiệc Cực Đáng Yêu Mùa Hè 2026', 'top-5-cach-phoi-do-cho-be-gai-mua-he-2026', 'blog_be_gai_tiec.jpg', 'Gợi ý các công thức phối đầm công chúa và phụ kiện xinh xắn chuẩn phong cách lookbook.', '<p>Chi tiết bài viết hướng dẫn chọn chất liệu vải sợi tre thoáng khí và kết hợp giày sandal an toàn cho bé...</p>', 25, 340, 'xuat_ban')
ON DUPLICATE KEY UPDATE `tieu_de`=VALUES(`tieu_de`);

INSERT INTO `tgb_binh_luan_bai_viet` (`ma_binh_luan`, `ma_bai_viet`, `ma_nguoi_dung`, `ma_binh_luan_cha`, `noi_dung_binh_luan`) VALUES
(1, 1, 3, NULL, 'Bài viết rất hữu ích, mình vừa đặt cho bé chiếc váy Elsa ở trên shop rồi!'),
(2, 1, 1, 1, 'Cảm ơn mẹ bé Sam đã tin tưởng ủng hộ FashionKids ạ!')
ON DUPLICATE KEY UPDATE `noi_dung_binh_luan`=VALUES(`noi_dung_binh_luan`);

SET FOREIGN_KEY_CHECKS = 1;


-- =========================================================================
-- BƯỚC 2: KHỞI TẠO 12 THỦ TỤC & HÀM CHỐNG XUNG ĐỘT COLLATION #1267
-- =========================================================================
DELIMITER //

-- 1. sp_hgb_dang_ky_thanh_vien
DROP PROCEDURE IF EXISTS `sp_hgb_dang_ky_thanh_vien`//
CREATE PROCEDURE `sp_hgb_dang_ky_thanh_vien`(
    IN  p_ten_dang_nhap VARCHAR(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_mat_khau       VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_ho_va_ten      VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_thu_dien_tu    VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_so_dien_thoai  VARCHAR(20)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_dia_chi_nha    VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    OUT p_ma_nguoi_dung_moi INT
)
BEGIN
    DECLARE v_ma_hang_mac_dinh INT DEFAULT NULL;
    IF TRIM(p_ten_dang_nhap) = '' OR TRIM(p_mat_khau) = '' OR TRIM(p_ho_va_ten) = '' OR TRIM(p_thu_dien_tu) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_01]: Tên đăng nhập, mật khẩu, họ tên và email không được để trống!';
    END IF;
    IF EXISTS (
        SELECT 1 FROM `hgb_nguoi_dung` 
        WHERE `ten_dang_nhap` = p_ten_dang_nhap COLLATE utf8mb4_unicode_ci
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_02]: Tên đăng nhập này đã được sử dụng trên hệ thống!';
    END IF;
    IF EXISTS (
        SELECT 1 FROM `hgb_nguoi_dung` 
        WHERE `thu_dien_tu` = p_thu_dien_tu COLLATE utf8mb4_unicode_ci
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_03]: Hòm thư điện tử này đã được tài khoản khác đăng ký!';
    END IF;
    IF p_so_dien_thoai IS NOT NULL AND (LENGTH(TRIM(p_so_dien_thoai)) < 9 OR LENGTH(TRIM(p_so_dien_thoai)) > 11) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_04]: Số điện thoại cung cấp không hợp lệ (yêu cầu 9-11 chữ số)!';
    END IF;
    SELECT `ma_hang` INTO v_ma_hang_mac_dinh 
    FROM `hgb_hang_thanh_vien` 
    ORDER BY `diem_toi_thieu` ASC 
    LIMIT 1;
    INSERT INTO `hgb_nguoi_dung` (
        `ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`,
        `thu_dien_tu`, `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`,
        `vai_tro`, `da_xac_thuc_otp`, `ngay_tao`
    ) VALUES (
        v_ma_hang_mac_dinh, TRIM(p_ten_dang_nhap), p_mat_khau, TRIM(p_ho_va_ten),
        TRIM(p_thu_dien_tu), TRIM(p_so_dien_thoai), TRIM(p_dia_chi_nha), 0,
        'khach_hang', 0, NOW()
    );
    SET p_ma_nguoi_dung_moi = LAST_INSERT_ID();
END//

-- 2. sp_hgb_tich_diem_nang_hang
DROP PROCEDURE IF EXISTS `sp_hgb_tich_diem_nang_hang`//
CREATE PROCEDURE `sp_hgb_tich_diem_nang_hang`(
    IN p_ma_nguoi_dung INT,
    IN p_diem_cong      INT
)
BEGIN
    DECLARE v_diem_hien_tai INT;
    DECLARE v_diem_moi      INT;
    DECLARE v_ma_hang_moi   INT DEFAULT NULL;
    IF p_diem_cong <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_05]: Số điểm thưởng cộng thêm phải lớn hơn 0!';
    END IF;
    SELECT `diem_tich_luy` INTO v_diem_hien_tai 
    FROM `hgb_nguoi_dung` 
    WHERE `ma_nguoi_dung` = p_ma_nguoi_dung;
    IF v_diem_hien_tai IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_06]: Mã người dùng không tồn tại trong hệ thống!';
    END IF;
    SET v_diem_moi = v_diem_hien_tai + p_diem_cong;
    SELECT `ma_hang` INTO v_ma_hang_moi
    FROM `hgb_hang_thanh_vien`
    WHERE `diem_toi_thieu` <= v_diem_moi
    ORDER BY `diem_toi_thieu` DESC
    LIMIT 1;
    UPDATE `hgb_nguoi_dung`
    SET `diem_tich_luy` = v_diem_moi,
        `ma_hang_thanh_vien` = v_ma_hang_moi
    WHERE `ma_nguoi_dung` = p_ma_nguoi_dung;
END//

-- 3. fn_hgb_tinh_phan_tram_giam_gia
DROP FUNCTION IF EXISTS `fn_hgb_tinh_phan_tram_giam_gia`//
CREATE FUNCTION `fn_hgb_tinh_phan_tram_giam_gia`(
    p_ma_nguoi_dung INT
) RETURNS FLOAT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_phan_tram FLOAT DEFAULT 0.0;
    IF p_ma_nguoi_dung IS NULL THEN
        RETURN 0.0;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = p_ma_nguoi_dung) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_07]: Mã tài khoản khách hàng không tồn tại để tính chiết khấu!';
    END IF;
    SELECT COALESCE(h.`phan_tram_giam_gia`, 0.0) INTO v_phan_tram
    FROM `hgb_nguoi_dung` u
    LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
    WHERE u.`ma_nguoi_dung` = p_ma_nguoi_dung;
    RETURN v_phan_tram;
END//

-- 4. sp_lbn_them_san_pham_moi
DROP PROCEDURE IF EXISTS `sp_lbn_them_san_pham_moi`//
CREATE PROCEDURE `sp_lbn_them_san_pham_moi`(
    IN  p_ma_danh_muc     INT,
    IN  p_ten_san_pham    VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_gia_ban         DECIMAL(10,2),
    IN  p_so_luong_ton_kho INT,
    IN  p_duong_dan_anh   VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_mo_ta_chat_lieu TEXT         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_loai_kich_co    ENUM('quan_ao', 'giay_dep'),
    OUT p_ma_san_pham_moi INT
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `lbn_danh_muc` WHERE `ma_danh_muc` = p_ma_danh_muc) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_01]: Mã danh mục không tồn tại trên hệ thống!';
    END IF;
    IF TRIM(p_ten_san_pham) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_02]: Tên sản phẩm không được phép để trống!';
    END IF;
    IF p_gia_ban <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_03]: Giá bán sản phẩm phải lớn hơn 0 VNĐ!';
    END IF;
    IF p_so_luong_ton_kho < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_04]: Số lượng hàng tồn kho ban đầu không được là số âm!';
    END IF;
    INSERT INTO `lbn_san_pham` (
        `ma_danh_muc`, `ten_san_pham`, `gia_ban`, `so_luong_ton_kho`,
        `duong_dan_anh`, `mo_ta_chat_lieu`, `loai_kich_co`, `ngay_tao`
    ) VALUES (
        p_ma_danh_muc, TRIM(p_ten_san_pham), p_gia_ban, p_so_luong_ton_kho,
        TRIM(p_duong_dan_anh), p_mo_ta_chat_lieu, p_loai_kich_co, NOW()
    );
    SET p_ma_san_pham_moi = LAST_INSERT_ID();
END//

-- 5. sp_lbn_nhap_them_kho_hang
DROP PROCEDURE IF EXISTS `sp_lbn_nhap_them_kho_hang`//
CREATE PROCEDURE `sp_lbn_nhap_them_kho_hang`(
    IN p_ma_san_pham     INT,
    IN p_so_luong_nhap   INT
)
BEGIN
    IF p_so_luong_nhap <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_05]: Số lượng nhập hàng bổ sung phải lớn hơn 0!';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM `lbn_san_pham` WHERE `ma_san_pham` = p_ma_san_pham) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_06]: Mã sản phẩm cần nhập kho không tồn tại!';
    END IF;
    UPDATE `lbn_san_pham`
    SET `so_luong_ton_kho` = `so_luong_ton_kho` + p_so_luong_nhap
    WHERE `ma_san_pham` = p_ma_san_pham;
END//

-- 6. fn_lbn_kiem_tra_ton_kho
DROP FUNCTION IF EXISTS `fn_lbn_kiem_tra_ton_kho`//
CREATE FUNCTION `fn_lbn_kiem_tra_ton_kho`(
    p_ma_san_pham       INT,
    p_so_luong_can_mua  INT
) RETURNS TINYINT(1)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_ton_kho INT;
    IF p_so_luong_can_mua <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_07]: Số lượng kiểm tra đặt mua phải lớn hơn 0!';
    END IF;
    SELECT `so_luong_ton_kho` INTO v_ton_kho
    FROM `lbn_san_pham`
    WHERE `ma_san_pham` = p_ma_san_pham;
    IF v_ton_kho IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LBN_ERR_08]: Sản phẩm tra cứu tồn kho không tồn tại!';
    END IF;
    IF v_ton_kho >= p_so_luong_can_mua THEN
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END//

-- 7. fn_lnkt_goi_y_kich_co_chuan
DROP FUNCTION IF EXISTS `fn_lnkt_goi_y_kich_co_chuan`//
CREATE FUNCTION `fn_lnkt_goi_y_kich_co_chuan`(
    p_ma_danh_muc   INT,
    p_chieu_cao     FLOAT,
    p_vong_nguc     FLOAT,
    p_vong_eo       FLOAT,
    p_chieu_dai_chan FLOAT
) RETURNS VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_loai_hang ENUM('quan_ao', 'giay_dep');
    DECLARE v_size_goi_y VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Chưa có size phù hợp';
    SELECT `loai_hang` INTO v_loai_hang
    FROM `lbn_danh_muc`
    WHERE `ma_danh_muc` = p_ma_danh_muc;
    IF v_loai_hang IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_01]: Danh mục không tồn tại để tra cứu kích cỡ!';
    END IF;
    IF v_loai_hang = 'quan_ao' THEN
        IF p_chieu_cao IS NULL OR p_chieu_cao <= 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_02]: Chiều cao của bé phải lớn hơn 0 cm để tính size quần áo!';
        END IF;
        SELECT `ten_kich_co` INTO v_size_goi_y
        FROM `lnkt_bang_kich_co`
        WHERE `ma_danh_muc` = p_ma_danh_muc
          AND p_chieu_cao BETWEEN `chieu_cao_toi_thieu` AND `chieu_cao_toi_da`
          AND (p_vong_nguc IS NULL OR `vong_nguc_toi_thieu` IS NULL OR p_vong_nguc BETWEEN `vong_nguc_toi_thieu` AND `vong_nguc_toi_da`)
          AND (p_vong_eo IS NULL OR `vong_eo_toi_thieu` IS NULL OR p_vong_eo BETWEEN `vong_eo_toi_thieu` AND `vong_eo_toi_da`)
        ORDER BY `chieu_cao_toi_thieu` ASC
        LIMIT 1;
    ELSEIF v_loai_hang = 'giay_dep' THEN
        IF p_chieu_dai_chan IS NULL OR p_chieu_dai_chan <= 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_03]: Chiều dài bàn chân bé phải lớn hơn 0 cm để chọn size giày!';
        END IF;
        SELECT `ten_kich_co` INTO v_size_goi_y
        FROM `lnkt_bang_kich_co`
        WHERE `ma_danh_muc` = p_ma_danh_muc
          AND p_chieu_dai_chan BETWEEN `chieu_dai_chan_toi_thieu` AND `chieu_dai_chan_toi_da`
        ORDER BY `chieu_dai_chan_toi_thieu` ASC
        LIMIT 1;
    END IF;
    IF v_size_goi_y IS NULL THEN
        SET v_size_goi_y = 'Chưa có size phù hợp';
    END IF;
    RETURN v_size_goi_y;
END//

-- 8. sp_lnkt_cap_nhat_trang_thai_don_hang
DROP PROCEDURE IF EXISTS `sp_lnkt_cap_nhat_trang_thai_don_hang`//
CREATE PROCEDURE `sp_lnkt_cap_nhat_trang_thai_don_hang`(
    IN p_ma_don_hang        INT,
    IN p_ma_nhan_vien       INT,
    IN p_trang_thai_moi     ENUM('cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy')
)
BEGIN
    DECLARE v_trang_thai_cu ENUM('cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy');
    DECLARE v_vai_tro_nv    ENUM('quan_tri', 'nhan_vien', 'khach_hang');
    DECLARE v_ma_nguoi_dung INT;
    DECLARE v_tong_tien     DECIMAL(12,2);
    DECLARE v_diem_cong     INT;
    SELECT `trang_thai_don_hang`, `ma_nguoi_dung`, `tong_tien_thanh_toan`
    INTO v_trang_thai_cu, v_ma_nguoi_dung, v_tong_tien
    FROM `lnkt_don_hang`
    WHERE `ma_don_hang` = p_ma_don_hang;
    IF v_trang_thai_cu IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_04]: Đơn hàng cần xử lý không tồn tại!';
    END IF;
    IF v_trang_thai_cu IN ('hoan_tat', 'da_huy') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_05]: Đơn hàng đã hoàn tất hoặc đã hủy không thể chỉnh sửa trạng thái!';
    END IF;
    SELECT `vai_tro` INTO v_vai_tro_nv 
    FROM `hgb_nguoi_dung` 
    WHERE `ma_nguoi_dung` = p_ma_nhan_vien;
    IF v_vai_tro_nv IS NULL OR v_vai_tro_nv NOT IN ('nhan_vien', 'quan_tri') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_06]: Người xử lý đơn hàng phải có thẩm quyền Nhân viên hoặc Quản trị!';
    END IF;
    IF p_trang_thai_moi = 'da_huy' THEN
        UPDATE `lbn_san_pham` sp
        JOIN `tgb_chi_tiet_don_hang` ct ON sp.`ma_san_pham` = ct.`ma_san_pham`
        SET sp.`so_luong_ton_kho` = sp.`so_luong_ton_kho` + ct.`so_luong`
        WHERE ct.`ma_don_hang` = p_ma_don_hang;
    END IF;
    IF p_trang_thai_moi = 'hoan_tat' AND v_ma_nguoi_dung IS NOT NULL THEN
        SET v_diem_cong = FLOOR(v_tong_tien / 10000);
        IF v_diem_cong > 0 THEN
            CALL `sp_hgb_tich_diem_nang_hang`(v_ma_nguoi_dung, v_diem_cong);
        END IF;
    END IF;
    UPDATE `lnkt_don_hang`
    SET `trang_thai_don_hang` = p_trang_thai_moi,
        `ma_nhan_vien_xu_ly`  = p_ma_nhan_vien
    WHERE `ma_don_hang` = p_ma_don_hang;
END//

-- 9. sp_lnkt_xet_duyet_kol_nhi
DROP PROCEDURE IF EXISTS `sp_lnkt_xet_duyet_kol_nhi`//
CREATE PROCEDURE `sp_lnkt_xet_duyet_kol_nhi`(
    IN p_ma_ho_so        INT,
    IN p_ma_nhan_vien    INT,
    IN p_trang_thai_duyet ENUM('cho_duyet', 'moi_phong_van', 'trung_tuyen', 'tu_choi')
)
BEGIN
    DECLARE v_vai_tro ENUM('quan_tri', 'nhan_vien', 'khach_hang');
    IF NOT EXISTS (SELECT 1 FROM `lnkt_dang_ky_kol_nhi` WHERE `ma_ho_so` = p_ma_ho_so) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_07]: Mã hồ sơ ứng tuyển KOL nhí không tồn tại!';
    END IF;
    SELECT `vai_tro` INTO v_vai_tro FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = p_ma_nhan_vien;
    IF v_vai_tro IS NULL OR v_vai_tro NOT IN ('nhan_vien', 'quan_tri') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_08]: Bạn không có thẩm quyền xét duyệt hồ sơ KOL nhí!';
    END IF;
    UPDATE `lnkt_dang_ky_kol_nhi`
    SET `trang_thai_duyet` = p_trang_thai_duyet
    WHERE `ma_ho_so` = p_ma_ho_so;
END//

-- 10. sp_tgb_tao_don_hang_va_chi_tiet
DROP PROCEDURE IF EXISTS `sp_tgb_tao_don_hang_va_chi_tiet`//
CREATE PROCEDURE `sp_tgb_tao_don_hang_va_chi_tiet`(
    IN  p_ma_nguoi_dung      INT,
    IN  p_ten_nguoi_nhan     VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_so_dien_thoai_nhan VARCHAR(20)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_thu_dien_tu_nhan   VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_dia_chi_giao_hang  VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_tinh_thanh_pho     VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_quan_huyen         VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_ma_san_pham        INT,
    IN  p_kich_co_chon       VARCHAR(20)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_so_luong           INT,
    OUT p_ma_don_hang_moi    INT
)
BEGIN
    DECLARE v_gia_ban            DECIMAL(10,2);
    DECLARE v_ton_kho            INT;
    DECLARE v_phan_tram_giam_gia FLOAT DEFAULT 0.0;
    DECLARE v_tien_hang_tam_tinh DECIMAL(12,2);
    DECLARE v_tien_giam_gia      DECIMAL(12,2);
    DECLARE v_tong_thanh_toan    DECIMAL(12,2);
    IF TRIM(p_ten_nguoi_nhan) = '' OR TRIM(p_so_dien_thoai_nhan) = '' OR TRIM(p_dia_chi_giao_hang) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_01]: Tên người nhận, SĐT và địa chỉ giao hàng không được để trống!';
    END IF;
    IF p_so_luong <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_02]: Số lượng sản phẩm đặt mua phải từ 1 trở lên!';
    END IF;
    SELECT `gia_ban`, `so_luong_ton_kho` 
    INTO v_gia_ban, v_ton_kho
    FROM `lbn_san_pham` 
    WHERE `ma_san_pham` = p_ma_san_pham;
    IF v_gia_ban IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_03]: Sản phẩm khách chọn không tồn tại!';
    END IF;
    IF v_ton_kho < p_so_luong THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_04]: Số lượng hàng trong kho không đủ để đáp ứng đơn hàng!';
    END IF;
    SET v_tien_hang_tam_tinh = v_gia_ban * p_so_luong;
    SET v_phan_tram_giam_gia = `fn_hgb_tinh_phan_tram_giam_gia`(p_ma_nguoi_dung);
    SET v_tien_giam_gia      = (v_tien_hang_tam_tinh * v_phan_tram_giam_gia) / 100.0;
    SET v_tong_thanh_toan    = v_tien_hang_tam_tinh - v_tien_giam_gia;
    START TRANSACTION;
        INSERT INTO `lnkt_don_hang` (
            `ma_nguoi_dung`, `ma_nhan_vien_xu_ly`, `ten_nguoi_nhan`,
            `so_dien_thoai_nhan`, `thu_dien_tu_nhan`, `dia_chi_giao_hang`,
            `tinh_thanh_pho`, `quan_huyen`, `tien_hang_tam_tinh`,
            `tien_giam_gia`, `tong_tien_thanh_toan`, `trang_thai_don_hang`, `ngay_dat_hang`
        ) VALUES (
            p_ma_nguoi_dung, NULL, TRIM(p_ten_nguoi_nhan),
            TRIM(p_so_dien_thoai_nhan), TRIM(p_thu_dien_tu_nhan), TRIM(p_dia_chi_giao_hang),
            p_tinh_thanh_pho, p_quan_huyen, v_tien_hang_tam_tinh,
            v_tien_giam_gia, v_tong_thanh_toan, 'cho_duyet', NOW()
        );
        SET p_ma_don_hang_moi = LAST_INSERT_ID();
        INSERT INTO `tgb_chi_tiet_don_hang` (
            `ma_don_hang`, `ma_san_pham`, `kich_co_chon`, `so_luong`, `don_gia_mua`
        ) VALUES (
            p_ma_don_hang_moi, p_ma_san_pham, TRIM(p_kich_co_chon), p_so_luong, v_gia_ban
        );
        UPDATE `lbn_san_pham`
        SET `so_luong_ton_kho` = `so_luong_ton_kho` - p_so_luong
        WHERE `ma_san_pham` = p_ma_san_pham;
    COMMIT;
END//

-- 11. sp_tgb_gui_danh_gia_san_pham
DROP PROCEDURE IF EXISTS `sp_tgb_gui_danh_gia_san_pham`//
CREATE PROCEDURE `sp_tgb_gui_danh_gia_san_pham`(
    IN p_ma_san_pham       INT,
    IN p_ma_nguoi_dung     INT,
    IN p_ma_don_hang       INT,
    IN p_so_sao_danh_gia   TINYINT,
    IN p_noi_dung_danh_gia TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_trang_thai_don ENUM('cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy');
    IF p_so_sao_danh_gia < 1 OR p_so_sao_danh_gia > 5 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_05]: Số sao đánh giá chất lượng phải từ 1 đến 5 sao!';
    END IF;
    SELECT `trang_thai_don_hang` INTO v_trang_thai_don
    FROM `lnkt_don_hang`
    WHERE `ma_don_hang` = p_ma_don_hang AND `ma_nguoi_dung` = p_ma_nguoi_dung;
    IF v_trang_thai_don IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_06]: Bạn không sở hữu đơn hàng này để viết đánh giá!';
    END IF;
    IF v_trang_thai_don != 'hoan_tat' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_07]: Đơn hàng phải hoàn tất giao dịch thành công mới được đánh giá!';
    END IF;
    IF NOT EXISTS (
        SELECT 1 FROM `tgb_chi_tiet_don_hang` 
        WHERE `ma_don_hang` = p_ma_don_hang AND `ma_san_pham` = p_ma_san_pham
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_08]: Sản phẩm này không tồn tại trong gói hàng đã mua!';
    END IF;
    IF EXISTS (
        SELECT 1 FROM `tgb_danh_gia_san_pham`
        WHERE `ma_don_hang` = p_ma_don_hang AND `ma_san_pham` = p_ma_san_pham AND `ma_nguoi_dung` = p_ma_nguoi_dung
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_09]: Bạn đã gửi đánh giá cho sản phẩm này trong đơn hàng rồi!';
    END IF;
    INSERT INTO `tgb_danh_gia_san_pham` (
        `ma_san_pham`, `ma_nguoi_dung`, `ma_don_hang`,
        `so_sao_danh_gia`, `noi_dung_danh_gia`, `so_luot_huu_ich`, `ngay_danh_gia`
    ) VALUES (
        p_ma_san_pham, p_ma_nguoi_dung, p_ma_don_hang,
        p_so_sao_danh_gia, TRIM(p_noi_dung_danh_gia), 0, NOW()
    );
END//

-- 12. fn_tgb_tinh_sao_trung_binh_san_pham
DROP FUNCTION IF EXISTS `fn_tgb_tinh_sao_trung_binh_san_pham`//
CREATE FUNCTION `fn_tgb_tinh_sao_trung_binh_san_pham`(
    p_ma_san_pham INT
) RETURNS DECIMAL(3,1)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_sao_tb DECIMAL(3,1) DEFAULT 0.0;
    IF NOT EXISTS (SELECT 1 FROM `lbn_san_pham` WHERE `ma_san_pham` = p_ma_san_pham) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_10]: Không tìm thấy mã sản phẩm cần tính sao trung bình!';
    END IF;
    SELECT ROUND(AVG(`so_sao_danh_gia`), 1) INTO v_sao_tb
    FROM `tgb_danh_gia_san_pham`
    WHERE `ma_san_pham` = p_ma_san_pham;
    IF v_sao_tb IS NULL THEN
        SET v_sao_tb = 0.0;
    END IF;
    RETURN v_sao_tb;
END//

-- 13. fn_tgb_dem_so_nguoi_mua
DROP FUNCTION IF EXISTS `fn_tgb_dem_so_nguoi_mua`//
CREATE FUNCTION `fn_tgb_dem_so_nguoi_mua`(
    p_ma_san_pham INT
) RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_so_nguoi_mua INT DEFAULT 0;
    SELECT COUNT(DISTINCT CASE
        WHEN dh.`ma_nguoi_dung` IS NOT NULL THEN CONCAT('U', dh.`ma_nguoi_dung`)
        ELSE CONCAT('O', dh.`ma_don_hang`)
    END) INTO v_so_nguoi_mua
    FROM `tgb_chi_tiet_don_hang` ct
    JOIN `lnkt_don_hang` dh ON ct.`ma_don_hang` = dh.`ma_don_hang`
    WHERE ct.`ma_san_pham` = p_ma_san_pham
      AND dh.`trang_thai_don_hang` = 'hoan_tat'
      AND NOT EXISTS (
          SELECT 1
          FROM `lnkt_yeu_cau_hoan_hang` hh
          WHERE hh.`ma_don_hang` = dh.`ma_don_hang`
            AND hh.`trang_thai_hoan` = 'hoan_tat_hoan'
      );
    RETURN COALESCE(v_so_nguoi_mua, 0);
END//

-- 14. sp_lnkt_tao_yeu_cau_hoan_hang
DROP PROCEDURE IF EXISTS `sp_lnkt_tao_yeu_cau_hoan_hang`//
CREATE PROCEDURE `sp_lnkt_tao_yeu_cau_hoan_hang`(
    IN  p_ma_don_hang      INT,
    IN  p_ma_nguoi_dung    INT,
    IN  p_ly_do            VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_mo_ta            TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_hinh_anh         TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_thong_tin_tien   VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    OUT p_ma_hoan_moi      INT
)
BEGIN
    DECLARE v_trang_thai_don ENUM('cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy');
    DECLARE v_tong_tien      DECIMAL(12,2) DEFAULT 0.00;
    IF TRIM(COALESCE(p_ly_do, '')) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_01]: Vui lòng chọn lý do hoàn hàng / trả hàng!';
    END IF;
    SELECT `trang_thai_don_hang`, `tong_tien_thanh_toan`
    INTO v_trang_thai_don, v_tong_tien
    FROM `lnkt_don_hang`
    WHERE `ma_don_hang` = p_ma_don_hang
      AND `ma_nguoi_dung` = p_ma_nguoi_dung
    LIMIT 1;
    IF v_trang_thai_don IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_02]: Bạn không sở hữu đơn hàng này hoặc đơn hàng không tồn tại!';
    END IF;
    IF v_trang_thai_don != 'hoan_tat' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_03]: Chỉ đơn hàng đã giao thành công (hoàn tất) mới được gửi yêu cầu hoàn hàng!';
    END IF;
    IF EXISTS (
        SELECT 1 FROM `lnkt_yeu_cau_hoan_hang`
        WHERE `ma_don_hang` = p_ma_don_hang
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_04]: Đơn hàng này đã gửi yêu cầu hoàn hàng / trả hàng trước đó!';
    END IF;
    INSERT INTO `lnkt_yeu_cau_hoan_hang` (
        `ma_don_hang`, `ma_nguoi_dung`, `ma_nhan_vien_xu_ly`,
        `ly_do_hoan`, `mo_ta_chi_tiet`, `hinh_anh_minh_chung`,
        `so_tien_hoan`, `thong_tin_nhan_tien`, `trang_thai_hoan`, `ngay_yeu_cau`
    ) VALUES (
        p_ma_don_hang, p_ma_nguoi_dung, NULL,
        TRIM(p_ly_do), NULLIF(TRIM(COALESCE(p_mo_ta, '')), ''), NULLIF(TRIM(COALESCE(p_hinh_anh, '')), ''),
        COALESCE(v_tong_tien, 0.00), NULLIF(TRIM(COALESCE(p_thong_tin_tien, '')), ''), 'cho_duyet_hoan', NOW()
    );
    SET p_ma_hoan_moi = LAST_INSERT_ID();
END//

-- 15. sp_lnkt_xu_ly_hoan_hang
DROP PROCEDURE IF EXISTS `sp_lnkt_xu_ly_hoan_hang`//
CREATE PROCEDURE `sp_lnkt_xu_ly_hoan_hang`(
    IN p_ma_hoan_hang    INT,
    IN p_ma_nhan_vien    INT,
    IN p_trang_thai_moi  VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_ghi_chu         TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_vai_tro         ENUM('quan_tri', 'nhan_vien', 'khach_hang');
    DECLARE v_ma_don_hang     INT;
    DECLARE v_ma_nguoi_dung   INT;
    DECLARE v_so_tien_hoan    DECIMAL(12,2) DEFAULT 0.00;
    DECLARE v_trang_thai_cu   ENUM('cho_duyet_hoan', 'da_duyet_hoan', 'dang_thu_hoi', 'hoan_tat_hoan', 'tu_choi_hoan');
    DECLARE v_diem_tru        INT DEFAULT 0;
    DECLARE v_diem_hien_tai   INT DEFAULT 0;
    DECLARE v_diem_moi        INT DEFAULT 0;
    DECLARE v_ma_hang_moi     INT DEFAULT NULL;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT `vai_tro` INTO v_vai_tro
    FROM `hgb_nguoi_dung`
    WHERE `ma_nguoi_dung` = p_ma_nhan_vien
    LIMIT 1;
    IF v_vai_tro IS NULL OR v_vai_tro NOT IN ('quan_tri', 'nhan_vien') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_05]: Chỉ Nhân viên hoặc Quản trị viên mới được phép xử lý yêu cầu hoàn hàng!';
    END IF;

    IF p_trang_thai_moi NOT IN ('da_duyet_hoan', 'dang_thu_hoi', 'hoan_tat_hoan', 'tu_choi_hoan') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_06]: Trạng thái xử lý hoàn hàng không hợp lệ!';
    END IF;

    SELECT `ma_don_hang`, `ma_nguoi_dung`, `so_tien_hoan`, `trang_thai_hoan`
    INTO v_ma_don_hang, v_ma_nguoi_dung, v_so_tien_hoan, v_trang_thai_cu
    FROM `lnkt_yeu_cau_hoan_hang`
    WHERE `ma_hoan_hang` = p_ma_hoan_hang
    LIMIT 1;

    IF v_trang_thai_cu IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_07]: Không tìm thấy yêu cầu hoàn hàng cần xử lý!';
    END IF;

    IF v_trang_thai_cu IN ('hoan_tat_hoan', 'tu_choi_hoan') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_08]: Yêu cầu hoàn hàng đã kết thúc, không thể thay đổi trạng thái!';
    END IF;

    IF p_trang_thai_moi = 'tu_choi_hoan' AND TRIM(COALESCE(p_ghi_chu, '')) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_09]: Vui lòng nhập lý do/ghi chú xử lý khi từ chối yêu cầu hoàn hàng!';
    END IF;

    IF p_trang_thai_moi = 'dang_thu_hoi' AND v_trang_thai_cu != 'da_duyet_hoan' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_10]: Phải duyệt hoàn hàng trước khi chuyển sang bước đang thu hồi hàng!';
    END IF;

    IF p_trang_thai_moi = 'hoan_tat_hoan' AND v_trang_thai_cu NOT IN ('da_duyet_hoan', 'dang_thu_hoi') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_RET_11]: Chỉ có thể hoàn tất hoàn tiền sau khi đã duyệt và thu hồi hàng!';
    END IF;

    IF p_trang_thai_moi = 'hoan_tat_hoan' THEN
        START TRANSACTION;
            UPDATE `lnkt_yeu_cau_hoan_hang`
            SET `trang_thai_hoan`    = 'hoan_tat_hoan',
                `ma_nhan_vien_xu_ly` = p_ma_nhan_vien,
                `ghi_chu_xu_ly`      = COALESCE(NULLIF(TRIM(COALESCE(p_ghi_chu, '')), ''), `ghi_chu_xu_ly`),
                `ngay_cap_nhat`      = NOW()
            WHERE `ma_hoan_hang` = p_ma_hoan_hang;

            UPDATE `lbn_san_pham` sp
            JOIN `tgb_chi_tiet_don_hang` ct ON sp.`ma_san_pham` = ct.`ma_san_pham`
            SET sp.`so_luong_ton_kho` = sp.`so_luong_ton_kho` + ct.`so_luong`
            WHERE ct.`ma_don_hang` = v_ma_don_hang;

            SET v_diem_tru = FLOOR(COALESCE(v_so_tien_hoan, 0) / 10000);
            IF v_ma_nguoi_dung IS NOT NULL AND v_diem_tru > 0 THEN
                SELECT COALESCE(`diem_tich_luy`, 0) INTO v_diem_hien_tai
                FROM `hgb_nguoi_dung`
                WHERE `ma_nguoi_dung` = v_ma_nguoi_dung
                FOR UPDATE;

                SET v_diem_moi = GREATEST(0, COALESCE(v_diem_hien_tai, 0) - v_diem_tru);

                SELECT `ma_hang` INTO v_ma_hang_moi
                FROM `hgb_hang_thanh_vien`
                WHERE `diem_toi_thieu` <= v_diem_moi
                ORDER BY `diem_toi_thieu` DESC
                LIMIT 1;

                UPDATE `hgb_nguoi_dung`
                SET `diem_tich_luy`      = v_diem_moi,
                    `ma_hang_thanh_vien` = v_ma_hang_moi
                WHERE `ma_nguoi_dung` = v_ma_nguoi_dung;
            END IF;
        COMMIT;
    ELSE
        UPDATE `lnkt_yeu_cau_hoan_hang`
        SET `trang_thai_hoan`    = p_trang_thai_moi,
            `ma_nhan_vien_xu_ly` = p_ma_nhan_vien,
            `ghi_chu_xu_ly`      = COALESCE(NULLIF(TRIM(COALESCE(p_ghi_chu, '')), ''), `ghi_chu_xu_ly`),
            `ngay_cap_nhat`      = NOW()
        WHERE `ma_hoan_hang` = p_ma_hoan_hang;
    END IF;
END//

DELIMITER ;


-- =========================================================================
-- BƯỚC 3: KIỂM THỬ TẤT CẢ ROUTINES (HAPPY PATH - KHÔNG ĐỤNG ĐỘ DỮ LIỆU)
-- Chạy khối lệnh này trên phpMyAdmin để nghiệm thu kết quả tự động
-- =========================================================================

-- Làm sạch dữ liệu kiểm thử cũ (đảm bảo chạy lại nhiều lần vẫn báo XANH 100%)
DELETE FROM `lnkt_yeu_cau_hoan_hang` WHERE `ma_don_hang` = 1 AND `ma_nguoi_dung` = 3;
DELETE FROM `tgb_danh_gia_san_pham` WHERE `ma_don_hang` = 1 AND `ma_san_pham` = 1 AND `ma_nguoi_dung` = 3;
DELETE FROM `lnkt_don_hang` WHERE `thu_dien_tu_nhan` = 'hoa.nguyen@gmail.com';
DELETE FROM `hgb_nguoi_dung` WHERE `ten_dang_nhap` = 'khach_hang_moi_1' OR `thu_dien_tu` = 'hoa.nguyen@gmail.com';
DELETE FROM `lbn_san_pham` WHERE `ten_san_pham` = 'Đầm Công Chúa Ren Cao Cấp Mới';
UPDATE `lnkt_don_hang` SET `trang_thai_don_hang` = 'cho_duyet', `ma_nhan_vien_xu_ly` = NULL WHERE `ma_don_hang` = 2;
UPDATE `lnkt_dang_ky_kol_nhi` SET `trang_thai_duyet` = 'cho_duyet' WHERE `ma_ho_so` = 1;

-- Test 1: Đăng ký thành viên mới hoàn toàn (Hồ Gia Bảo - Nhận Hạng Đồng)
CALL sp_hgb_dang_ky_thanh_vien('khach_hang_moi_1', 'e10adc3949ba59abbe56e057f20f883e', 'Nguyễn Thị Hoa', 'hoa.nguyen@gmail.com', '0912345678', 'Q.7, TP.HCM', @ma_user_moi);
SELECT @ma_user_moi AS ma_khach_hang_moi;

-- Test 2: Tích điểm thăng hạng (Cộng 1.600 điểm -> Khách tự nhảy lên Hạng Vàng)
CALL sp_hgb_tich_diem_nang_hang(@ma_user_moi, 1600);
SELECT ma_nguoi_dung, ho_va_ten, diem_tich_luy, ma_hang_thanh_vien FROM hgb_nguoi_dung WHERE ma_nguoi_dung = @ma_user_moi;

-- Test 3: Lấy % giảm giá VIP (Hạng Vàng trả về 10%)
SELECT fn_hgb_tinh_phan_tram_giam_gia(@ma_user_moi) AS phan_tram_giam;

-- Test 4: Thêm sản phẩm mới (Lê Bảo Ngọc)
CALL sp_lbn_them_san_pham_moi(1, 'Đầm Công Chúa Ren Cao Cấp Mới', 390000, 20, 'dam_ren.jpg', 'Vải cotton lụa thoáng mát', 'quan_ao', @ma_sp_moi);
SELECT @ma_sp_moi AS ma_sp_vua_tao;

-- Test 5: Nhập thêm kho hàng (+15 chiếc)
CALL sp_lbn_nhap_them_kho_hang(@ma_sp_moi, 15);

-- Test 6: Kiểm tra tồn kho khả dụng (Đủ hàng 35 >= 30 -> Trả về 1)
SELECT fn_lbn_kiem_tra_ton_kho(@ma_sp_moi, 30) AS du_hang_ban;

-- Test 7: Gợi ý size thông minh theo chiều cao (Lê Nguyễn Khánh Trình -> Ra Size 2)
SELECT fn_lnkt_goi_y_kich_co_chuan(1, 95, 53, 51, NULL) AS size_goi_y;

-- Test 8: Cập nhật đơn hàng số 2 từ 'cho_duyet' sang 'hoan_tat' (Nhân viên 2 duyệt -> Tự tích điểm)
CALL sp_lnkt_cap_nhat_trang_thai_don_hang(2, 2, 'hoan_tat');
SELECT ma_don_hang, trang_thai_don_hang, ma_nhan_vien_xu_ly FROM lnkt_don_hang WHERE ma_don_hang = 2;

-- Test 9: Duyệt hồ sơ casting KOL nhí số 1 sang vòng phỏng vấn
CALL sp_lnkt_xet_duyet_kol_nhi(1, 2, 'moi_phong_van');
SELECT ma_ho_so, ho_ten_be, trang_thai_duyet FROM lnkt_dang_ky_kol_nhi WHERE ma_ho_so = 1;

-- Test 10: Đặt hàng tự động và trừ tồn kho (Trần Gia Bảo - Tự giảm giá VIP 10%)
CALL sp_tgb_tao_don_hang_va_chi_tiet(@ma_user_moi, 'Nguyễn Thị Hoa', '0912345678', 'hoa.nguyen@gmail.com', 'Khu Sunrise City', 'TP.HCM', 'Quận 7', 1, 'Size 2 (2-3T)', 2, @don_hang_moi);
SELECT * FROM lnkt_don_hang WHERE ma_don_hang = @don_hang_moi;

-- Test 11: Đánh giá sản phẩm đã mua trên Đơn hàng 1 (Đã hoàn tất)
CALL sp_tgb_gui_danh_gia_san_pham(1, 3, 1, 5, 'Vải sợi tre rất êm ái, bé mặc vừa vặn và rất thích!');

-- Test 12: Tính điểm đánh giá sao trung bình của sản phẩm
SELECT fn_tgb_tinh_sao_trung_binh_san_pham(1) AS diem_danh_gia_sao;
