-- =========================================================================
-- FILE SQL CHUYEN DUNG CHO INFINITYFREE HOSTING (if0_43426037_fashionkids)
-- Bao gom day du 17 bang & Seed Data moi nhat (Khong CREATE DATABASE / Routine gay loi #1044/#1227)
-- Huong dan: Vao phpMyAdmin tren InfinityFree -> chon CSDL if0_43426037_fashionkids -> Import file nay
-- =========================================================================


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

