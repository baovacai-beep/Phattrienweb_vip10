-- =============================================================================
-- HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ THỜI TRANG TRẺ EM FASHIONKIDS
-- Database Schema & 12 Stored Procedures/Functions chuẩn phân công Nhóm 10
-- Quy ước tiền tố:
--   hgb_ : Hồ Gia Bảo (User, Membership Tiers, Points, Auth)
--   lbn_ : Lê Bảo Ngọc (Categories, Products, Stock)
--   lnkt_: Lê Nguyễn Khánh Trình (Size Charts, Orders, Size Suggestion)
--   tgb_ : Trần Gia Bảo (Order Items, Chat, Reviews, News)
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `db_fashionkids` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_fashionkids`;

-- -----------------------------------------------------------------------------
-- 1. BẢNG HẠNG THÀNH VIÊN (hgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hgb_hang_thanh_vien` (
    `ma_hang` INT AUTO_INCREMENT PRIMARY KEY,
    `ten_hang` VARCHAR(50) NOT NULL,
    `diem_toi_thieu` INT NOT NULL DEFAULT 0,
    `phan_tram_giam_gia` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `mo_ta_quyen_loi` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. BẢNG NGƯỜI DÙNG (hgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hgb_nguoi_dung` (
    `ma_nguoi_dung` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_hang_thanh_vien` INT NULL,
    `ten_dang_nhap` VARCHAR(50) NOT NULL UNIQUE,
    `mat_khau` VARCHAR(255) NOT NULL,
    `ho_va_ten` VARCHAR(100) NOT NULL,
    `thu_dien_tu` VARCHAR(100) NOT NULL UNIQUE,
    `so_dien_thoai` VARCHAR(20) NULL,
    `dia_chi_nha` VARCHAR(255) NULL,
    `diem_tich_luy` INT NOT NULL DEFAULT 0,
    `vai_tro` ENUM('khach_hang', 'nhan_vien', 'quan_tri_vien') NOT NULL DEFAULT 'khach_hang',
    `da_xac_thuc_otp` TINYINT(1) NOT NULL DEFAULT 0,
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_user_tier` FOREIGN KEY (`ma_hang_thanh_vien`) REFERENCES `hgb_hang_thanh_vien` (`ma_hang`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. BẢNG DANH MỤC SẢN PHẨM (lbn_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lbn_danh_muc` (
    `ma_danh_muc` INT AUTO_INCREMENT PRIMARY KEY,
    `ten_danh_muc` VARCHAR(100) NOT NULL,
    `gioi_tinh` ENUM('be_trai', 'be_gai', 'unisex') NOT NULL DEFAULT 'unisex',
    `loai_hang` ENUM('quan_ao', 'giay_dep', 'phu_kien') NOT NULL DEFAULT 'quan_ao'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. BẢNG SẢN PHẨM (lbn_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lbn_san_pham` (
    `ma_san_pham` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_danh_muc` INT NOT NULL,
    `ten_san_pham` VARCHAR(255) NOT NULL,
    `gia_ban` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `so_luong_ton_kho` INT NOT NULL DEFAULT 0,
    `duong_dan_anh` VARCHAR(255) NULL,
    `mo_ta_chat_lieu` TEXT NULL,
    `loai_kich_co` ENUM('quan_ao', 'giay_dep') NOT NULL DEFAULT 'quan_ao',
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_product_category` FOREIGN KEY (`ma_danh_muc`) REFERENCES `lbn_danh_muc` (`ma_danh_muc`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. BẢNG BANNER SỰ KIỆN (lnkt_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lnkt_banner_su_kien` (
    `ma_banner` INT AUTO_INCREMENT PRIMARY KEY,
    `tieu_de` VARCHAR(255) NOT NULL,
    `duong_dan_anh` VARCHAR(255) NOT NULL,
    `duong_dan_dieu_huong` VARCHAR(255) NULL,
    `loai_su_kien` VARCHAR(50) NULL,
    `ngay_bat_dau` DATETIME NULL,
    `ngay_ket_thuc` DATETIME NULL,
    `dang_kich_hoat` TINYINT(1) NOT NULL DEFAULT 1,
    `thu_tu_hien_thi` INT NOT NULL DEFAULT 1,
    `ngay_tao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. BẢNG QUY CHUẨN KÍCH CỠ (lnkt_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lnkt_bang_kich_co` (
    `ma_kich_co` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_danh_muc` INT NOT NULL,
    `loai_hang` ENUM('quan_ao', 'giay_dep') NOT NULL DEFAULT 'quan_ao',
    `gioi_tinh` ENUM('be_trai', 'be_gai', 'unisex') NOT NULL DEFAULT 'unisex',
    `ten_kich_co` VARCHAR(20) NOT NULL,
    `chieu_cao_toi_thieu` FLOAT NOT NULL DEFAULT 0,
    `chieu_cao_toi_da` FLOAT NOT NULL DEFAULT 0,
    `vong_nguc_toi_thieu` FLOAT NOT NULL DEFAULT 0,
    `vong_nguc_toi_da` FLOAT NOT NULL DEFAULT 0,
    `vong_eo_toi_thieu` FLOAT NOT NULL DEFAULT 0,
    `vong_eo_toi_da` FLOAT NOT NULL DEFAULT 0,
    `vong_mong_toi_thieu` FLOAT NOT NULL DEFAULT 0,
    `vong_mong_toi_da` FLOAT NOT NULL DEFAULT 0,
    `chieu_dai_chan_toi_thieu` FLOAT NOT NULL DEFAULT 0,
    `chieu_dai_chan_toi_da` FLOAT NOT NULL DEFAULT 0,
    CONSTRAINT `fk_sizechart_category` FOREIGN KEY (`ma_danh_muc`) REFERENCES `lbn_danh_muc` (`ma_danh_muc`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. BẢNG ĐƠN HÀNG (lnkt_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lnkt_don_hang` (
    `ma_don_hang` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_nguoi_dung` INT NULL,
    `ma_nhan_vien_xu_ly` INT NULL,
    `ten_nguoi_nhan` VARCHAR(100) NOT NULL,
    `so_dien_thoai_nhan` VARCHAR(20) NOT NULL,
    `thu_dien_tu_nhan` VARCHAR(100) NULL,
    `dia_chi_giao_hang` VARCHAR(255) NOT NULL,
    `tinh_thanh_pho` VARCHAR(100) NOT NULL,
    `quan_huyen` VARCHAR(100) NOT NULL,
    `tien_hang_tam_tinh` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `tien_giam_gia` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `tong_tien_thanh_toan` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `trang_thai_don_hang` ENUM('cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy') NOT NULL DEFAULT 'cho_xac_nhan',
    `ngay_dat_hang` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_order_user` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_order_staff` FOREIGN KEY (`ma_nhan_vien_xu_ly`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. BẢNG ĐĂNG KÝ KOL NHÍ (lnkt_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lnkt_dang_ky_kol_nhi` (
    `ma_ho_so` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_nguoi_dung` INT NULL,
    `ho_ten_phu_huynh` VARCHAR(100) NOT NULL,
    `so_dien_thoai` VARCHAR(20) NOT NULL,
    `thu_dien_tu` VARCHAR(100) NULL,
    `ho_ten_be` VARCHAR(100) NOT NULL,
    `ngay_sinh_be` DATE NULL,
    `gioi_tinh_be` ENUM('be_trai', 'be_gai') NOT NULL,
    `chieu_cao_be` FLOAT NULL,
    `can_nang_be` FLOAT NULL,
    `danh_sach_anh` TEXT NULL,
    `nang_khieu_kinh_nghiem` TEXT NULL,
    `trang_thai_duyet` ENUM('cho_duyet', 'da_duyet', 'tu_choi') NOT NULL DEFAULT 'cho_duyet',
    `ngay_nop_ho_so` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_kol_user` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. BẢNG BÀI VIẾT TIN TỨC (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_bai_viet_tin_tuc` (
    `ma_bai_viet` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_tac_gia` INT NOT NULL,
    `tieu_de` VARCHAR(255) NOT NULL,
    `duong_dan_tinh` VARCHAR(255) NOT NULL UNIQUE,
    `anh_dai_dien` VARCHAR(255) NULL,
    `tom_tat_ngan` TEXT NULL,
    `noi_dung_chi_tiet` LONGTEXT NOT NULL,
    `so_luot_thich` INT NOT NULL DEFAULT 0,
    `so_luot_xem` INT NOT NULL DEFAULT 0,
    `trang_thai_bai_viet` ENUM('nhap', 'xuat_ban') NOT NULL DEFAULT 'xuat_ban',
    `ngay_dang` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_news_author` FOREIGN KEY (`ma_tac_gia`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. BẢNG BÌNH LUẬN BÀI VIẾT (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_binh_luan_bai_viet` (
    `ma_binh_luan` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_bai_viet` INT NOT NULL,
    `ma_nguoi_dung` INT NOT NULL,
    `ma_binh_luan_cha` INT NULL,
    `noi_dung_binh_luan` TEXT NOT NULL,
    `ngay_binh_luan` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_comment_news` FOREIGN KEY (`ma_bai_viet`) REFERENCES `tgb_bai_viet_tin_tuc` (`ma_bai_viet`) ON DELETE CASCADE,
    CONSTRAINT `fk_comment_user` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE CASCADE,
    CONSTRAINT `fk_comment_parent` FOREIGN KEY (`ma_binh_luan_cha`) REFERENCES `tgb_binh_luan_bai_viet` (`ma_binh_luan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. BẢNG ĐÁNH GIÁ SẢN PHẨM (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_danh_gia_san_pham` (
    `ma_danh_gia` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_san_pham` INT NOT NULL,
    `ma_nguoi_dung` INT NOT NULL,
    `ma_don_hang` INT NULL,
    `so_sao_danh_gia` TINYINT NOT NULL CHECK (`so_sao_danh_gia` BETWEEN 1 AND 5),
    `noi_dung_danh_gia` TEXT NOT NULL,
    `so_luot_huu_ich` INT NOT NULL DEFAULT 0,
    `ngay_danh_gia` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_review_product` FOREIGN KEY (`ma_san_pham`) REFERENCES `lbn_san_pham` (`ma_san_pham`) ON DELETE CASCADE,
    CONSTRAINT `fk_review_user` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE CASCADE,
    CONSTRAINT `fk_review_order` FOREIGN KEY (`ma_don_hang`) REFERENCES `lnkt_don_hang` (`ma_don_hang`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. BẢNG TỆP TIN ĐÍNH KÈM ĐÁNH GIÁ (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_tep_tin_danh_gia` (
    `ma_tep_tin` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_danh_gia` INT NOT NULL,
    `loai_tep_tin` ENUM('hinh_anh', 'video') NOT NULL DEFAULT 'hinh_anh',
    `duong_dan_tep_tin` VARCHAR(255) NOT NULL,
    CONSTRAINT `fk_review_file` FOREIGN KEY (`ma_danh_gia`) REFERENCES `tgb_danh_gia_san_pham` (`ma_danh_gia`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. BẢNG LƯỢT THÍCH ĐÁNH GIÁ (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_luot_thich_danh_gia` (
    `ma_nguoi_dung` INT NOT NULL,
    `ma_danh_gia` INT NOT NULL,
    `ngay_thich` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ma_nguoi_dung`, `ma_danh_gia`),
    CONSTRAINT `fk_like_user` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE CASCADE,
    CONSTRAINT `fk_like_review` FOREIGN KEY (`ma_danh_gia`) REFERENCES `tgb_danh_gia_san_pham` (`ma_danh_gia`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. BẢNG CHI TIẾT ĐƠN HÀNG (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_chi_tiet_don_hang` (
    `ma_chi_tiet_don` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_don_hang` INT NOT NULL,
    `ma_san_pham` INT NOT NULL,
    `kich_co_chon` VARCHAR(20) NOT NULL,
    `so_luong` INT NOT NULL DEFAULT 1,
    `don_gia_mua` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT `fk_item_order` FOREIGN KEY (`ma_don_hang`) REFERENCES `lnkt_don_hang` (`ma_don_hang`) ON DELETE CASCADE,
    CONSTRAINT `fk_item_product` FOREIGN KEY (`ma_san_pham`) REFERENCES `lbn_san_pham` (`ma_san_pham`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. BẢNG TIN NHẮN TƯ VẤN TRỰC TUYẾN (tgb_)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tgb_tin_nhan_tu_van` (
    `ma_tin_nhan` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_phien_khach` VARCHAR(100) NOT NULL,
    `ma_nguoi_dung` INT NULL,
    `nguoi_gui` ENUM('khach_hang', 'nhan_vien') NOT NULL DEFAULT 'khach_hang',
    `noi_dung_tin_nhan` TEXT NOT NULL,
    `da_xem` TINYINT(1) NOT NULL DEFAULT 0,
    `ngay_gui` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_chat_user` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `hgb_nguoi_dung` (`ma_nguoi_dung`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 12 STORED PROCEDURES VÀ STORED FUNCTIONS NGHIỆP VỤ
-- =============================================================================

DELIMITER $$

-- -----------------------------------------------------------------------------
-- SP 1: hgb_insert_user (Hồ Gia Bảo)
-- Thêm tài khoản người dùng mới kèm mật khẩu mã hóa
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `hgb_insert_user`$$
CREATE PROCEDURE `hgb_insert_user`(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_full_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_address VARCHAR(255),
    IN p_role VARCHAR(20),
    OUT p_new_user_id INT
)
BEGIN
    INSERT INTO `hgb_nguoi_dung` (
        `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`, `so_dien_thoai`, `dia_chi_nha`, `vai_tro`, `ma_hang_thanh_vien`, `diem_tich_luy`, `da_xac_thuc_otp`
    ) VALUES (
        p_username, p_password, p_full_name, p_email, p_phone, p_address, p_role, 1, 0, 0
    );
    SET p_new_user_id = LAST_INSERT_ID();
END$$

-- -----------------------------------------------------------------------------
-- SP 2: hgb_update_user_points (Hồ Gia Bảo)
-- Tích lũy điểm thưởng khi thanh toán thành công và tự động xét thăng hạng VIP
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `hgb_update_user_points`$$
CREATE PROCEDURE `hgb_update_user_points`(
    IN p_user_id INT,
    IN p_points_to_add INT
)
BEGIN
    DECLARE v_new_points INT DEFAULT 0;
    DECLARE v_target_tier INT DEFAULT 1;

    UPDATE `hgb_nguoi_dung`
    SET `diem_tich_luy` = `diem_tich_luy` + p_points_to_add
    WHERE `ma_nguoi_dung` = p_user_id;

    SELECT `diem_tich_luy` INTO v_new_points
    FROM `hgb_nguoi_dung`
    WHERE `ma_nguoi_dung` = p_user_id;

    -- Tự động tìm thứ hạng cao nhất thỏa mãn điểm tích lũy
    SELECT `ma_hang` INTO v_target_tier
    FROM `hgb_hang_thanh_vien`
    WHERE `diem_toi_thieu` <= v_new_points
    ORDER BY `diem_toi_thieu` DESC
    LIMIT 1;

    IF v_target_tier IS NOT NULL THEN
        UPDATE `hgb_nguoi_dung`
        SET `ma_hang_thanh_vien` = v_target_tier
        WHERE `ma_nguoi_dung` = p_user_id;
    END IF;
END$$

-- -----------------------------------------------------------------------------
-- FUNCTION 3: hgb_get_discount_percent (Hồ Gia Bảo)
-- Trả về phần trăm chiết khấu VIP của người dùng
-- -----------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `hgb_get_discount_percent`$$
CREATE FUNCTION `hgb_get_discount_percent`(
    p_user_id INT
) RETURNS DECIMAL(5,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_discount DECIMAL(5,2) DEFAULT 0.00;

    SELECT COALESCE(h.`phan_tram_giam_gia`, 0.00) INTO v_discount
    FROM `hgb_nguoi_dung` u
    LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
    WHERE u.`ma_nguoi_dung` = p_user_id;

    RETURN v_discount;
END$$

-- -----------------------------------------------------------------------------
-- SP 4: lbn_insert_product (Lê Bảo Ngọc)
-- Thêm sản phẩm thời trang trẻ em mới vào kho
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `lbn_insert_product`$$
CREATE PROCEDURE `lbn_insert_product`(
    IN p_category_id INT,
    IN p_name VARCHAR(255),
    IN p_price DECIMAL(12,2),
    IN p_stock INT,
    IN p_image VARCHAR(255),
    IN p_description TEXT,
    IN p_size_type VARCHAR(20),
    OUT p_new_product_id INT
)
BEGIN
    INSERT INTO `lbn_san_pham` (
        `ma_danh_muc`, `ten_san_pham`, `gia_ban`, `so_luong_ton_kho`, `duong_dan_anh`, `mo_ta_chat_lieu`, `loai_kich_co`
    ) VALUES (
        p_category_id, p_name, p_price, p_stock, p_image, p_description, p_size_type
    );
    SET p_new_product_id = LAST_INSERT_ID();
END$$

-- -----------------------------------------------------------------------------
-- SP 5: lbn_update_stock (Lê Bảo Ngọc)
-- Cập nhật tồn kho sản phẩm (trừ kho khi bán, cộng kho khi nhập)
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `lbn_update_stock`$$
CREATE PROCEDURE `lbn_update_stock`(
    IN p_product_id INT,
    IN p_quantity_change INT
)
BEGIN
    DECLARE v_current_stock INT;
    
    SELECT `so_luong_ton_kho` INTO v_current_stock 
    FROM `lbn_san_pham` 
    WHERE `ma_san_pham` = p_product_id FOR UPDATE;

    IF (v_current_stock + p_quantity_change) < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Lỗi tồn kho: Số lượng hàng trong kho không đủ để xuất bán!';
    ELSE
        UPDATE `lbn_san_pham`
        SET `so_luong_ton_kho` = `so_luong_ton_kho` + p_quantity_change
        WHERE `ma_san_pham` = p_product_id;
    END IF;
END$$

-- -----------------------------------------------------------------------------
-- FUNCTION 6: lbn_count_products_by_category (Lê Bảo Ngọc)
-- Đếm tổng số sản phẩm còn hàng thuộc danh mục chỉ định
-- -----------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `lbn_count_products_by_category`$$
CREATE FUNCTION `lbn_count_products_by_category`(
    p_category_id INT
) RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_count INT DEFAULT 0;

    SELECT COUNT(*) INTO v_count
    FROM `lbn_san_pham`
    WHERE `ma_danh_muc` = p_category_id AND `so_luong_ton_kho` > 0;

    RETURN v_count;
END$$

-- -----------------------------------------------------------------------------
-- SP 7: lnkt_create_order (Lê Nguyễn Khánh Trình)
-- Tạo đơn hàng mới kèm thông tin nhận hàng và tổng thanh toán
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `lnkt_create_order`$$
CREATE PROCEDURE `lnkt_create_order`(
    IN p_user_id INT,
    IN p_receiver_name VARCHAR(100),
    IN p_receiver_phone VARCHAR(20),
    IN p_receiver_email VARCHAR(100),
    IN p_shipping_address VARCHAR(255),
    IN p_province VARCHAR(100),
    IN p_district VARCHAR(100),
    IN p_subtotal DECIMAL(12,2),
    IN p_discount DECIMAL(12,2),
    IN p_total DECIMAL(12,2),
    OUT p_order_id INT
)
BEGIN
    INSERT INTO `lnkt_don_hang` (
        `ma_nguoi_dung`, `ten_nguoi_nhan`, `so_dien_thoai_nhan`, `thu_dien_tu_nhan`,
        `dia_chi_giao_hang`, `tinh_thanh_pho`, `quan_huyen`,
        `tien_hang_tam_tinh`, `tien_giam_gia`, `tong_tien_thanh_toan`, `trang_thai_don_hang`
    ) VALUES (
        p_user_id, p_receiver_name, p_receiver_phone, p_receiver_email,
        p_shipping_address, p_province, p_district,
        p_subtotal, p_discount, p_total, 'cho_xac_nhan'
    );
    SET p_order_id = LAST_INSERT_ID();
END$$

-- -----------------------------------------------------------------------------
-- SP 8: lnkt_update_order_status (Lê Nguyễn Khánh Trình)
-- Cập nhật tiến trình trạng thái đơn hàng và ghi nhận nhân viên phụ trách
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `lnkt_update_order_status`$$
CREATE PROCEDURE `lnkt_update_order_status`(
    IN p_order_id INT,
    IN p_new_status VARCHAR(50),
    IN p_handled_by INT
)
BEGIN
    UPDATE `lnkt_don_hang`
    SET `trang_thai_don_hang` = p_new_status,
        `ma_nhan_vien_xu_ly` = p_handled_by
    WHERE `ma_don_hang` = p_order_id;
END$$

-- -----------------------------------------------------------------------------
-- FUNCTION 9: lnkt_suggest_size (Lê Nguyễn Khánh Trình)
-- Thuật toán thông minh tự động gợi ý kích cỡ dựa trên số đo thực tế và danh mục
-- -----------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `lnkt_suggest_size`$$
CREATE FUNCTION `lnkt_suggest_size`(
    p_category_id INT,
    p_measure_cm FLOAT
) RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_size_name VARCHAR(20) DEFAULT 'Freesize';
    DECLARE v_loai_hang VARCHAR(20);

    -- Xác định loại hàng của danh mục
    SELECT `loai_hang` INTO v_loai_hang
    FROM `lbn_danh_muc`
    WHERE `ma_danh_muc` = p_category_id
    LIMIT 1;

    IF v_loai_hang = 'giay_dep' THEN
        -- Đối chiếu theo chiều dài bàn chân
        SELECT `ten_kich_co` INTO v_size_name
        FROM `lnkt_bang_kich_co`
        WHERE `ma_danh_muc` = p_category_id
          AND p_measure_cm >= `chieu_dai_chan_toi_thieu`
          AND p_measure_cm <= `chieu_dai_chan_toi_da`
        ORDER BY `chieu_dai_chan_toi_thieu` ASC
        LIMIT 1;
    ELSE
        -- Mặc định đối chiếu theo chiều cao của bé (Quần áo)
        SELECT `ten_kich_co` INTO v_size_name
        FROM `lnkt_bang_kich_co`
        WHERE `ma_danh_muc` = p_category_id
          AND p_measure_cm >= `chieu_cao_toi_thieu`
          AND p_measure_cm <= `chieu_cao_toi_da`
        ORDER BY `chieu_cao_toi_thieu` ASC
        LIMIT 1;
    END IF;

    -- Nếu không khớp khoảng cụ thể, trả về size nhỏ nhất hoặc lớn nhất gần nhất
    IF v_size_name IS NULL THEN
        IF v_loai_hang = 'giay_dep' THEN
            IF p_measure_cm < 12.0 THEN
                SET v_size_name = 'Size 21 (Sơ sinh)';
            ELSE
                SET v_size_name = 'Size 28 (Lớn)';
            END IF;
        ELSE
            IF p_measure_cm < 70.0 THEN
                SET v_size_name = 'Size 1 (0-1 tuổi)';
            ELSE
                SET v_size_name = 'Size 5 (5-6 tuổi)';
            END IF;
        END IF;
    END IF;

    RETURN v_size_name;
END$$

-- -----------------------------------------------------------------------------
-- SP 10: tgb_add_order_item (Trần Gia Bảo)
-- Thêm chi tiết mặt hàng vào đơn và tự động trừ kho
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `tgb_add_order_item`$$
CREATE PROCEDURE `tgb_add_order_item`(
    IN p_order_id INT,
    IN p_product_id INT,
    IN p_size VARCHAR(20),
    IN p_quantity INT,
    IN p_price DECIMAL(12,2)
)
BEGIN
    INSERT INTO `tgb_chi_tiet_don_hang` (
        `ma_don_hang`, `ma_san_pham`, `kich_co_chon`, `so_luong`, `don_gia_mua`
    ) VALUES (
        p_order_id, p_product_id, p_size, p_quantity, p_price
    );

    -- Tự động trừ kho qua SP lbn_update_stock
    CALL `lbn_update_stock`(p_product_id, -p_quantity);
END$$

-- -----------------------------------------------------------------------------
-- SP 11: tgb_send_chat_message (Trần Gia Bảo)
-- Lưu trữ tin nhắn trực tuyến giữa khách hàng và tư vấn viên
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `tgb_send_chat_message`$$
CREATE PROCEDURE `tgb_send_chat_message`(
    IN p_session_id VARCHAR(100),
    IN p_user_id INT,
    IN p_sender VARCHAR(20),
    IN p_message TEXT
)
BEGIN
    INSERT INTO `tgb_tin_nhan_tu_van` (
        `ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`
    ) VALUES (
        p_session_id, p_user_id, p_sender, p_message, 0
    );
END$$

-- -----------------------------------------------------------------------------
-- SP 12: tgb_mark_chat_as_read (Trần Gia Bảo)
-- Đánh dấu các tin nhắn trong phiên chat đã được nhân viên đọc
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `tgb_mark_chat_as_read`$$
CREATE PROCEDURE `tgb_mark_chat_as_read`(
    IN p_session_id VARCHAR(100)
)
BEGIN
    UPDATE `tgb_tin_nhan_tu_van`
    SET `da_xem` = 1
    WHERE `ma_phien_khach` = p_session_id AND `nguoi_gui` = 'khach_hang';
END$$

DELIMITER ;


-- =============================================================================
-- DỮ LIỆU KHỞI TẠO MẪU (SEED DATA CHUẨN ĐỒ ÁN)
-- =============================================================================

-- 1. Hạng thành viên VIP
INSERT INTO `hgb_hang_thanh_vien` (`ma_hang`, `ten_hang`, `diem_toi_thieu`, `phan_tram_giam_gia`, `mo_ta_quyen_loi`) VALUES
(1, 'Hạng Đồng (Bronze)', 0, 0.00, 'Tích lũy 10.000đ = 1 điểm. Nhận thông tin ưu đãi sớm nhất.'),
(2, 'Hạng Bạc (Silver)', 200, 5.00, 'Giảm trực tiếp 5% mọi đơn hàng, quà tặng ngày sinh nhật bé.'),
(3, 'Hạng Vàng (Gold)', 500, 10.00, 'Giảm trực tiếp 10% đơn hàng, miễn phí vận chuyển toàn quốc.'),
(4, 'Kim Cương (Diamond)', 1000, 15.00, 'Giảm 15% trọn đời, quyền ưu tiên casting KOL nhí độc quyền.')
ON DUPLICATE KEY UPDATE `ten_hang`=VALUES(`ten_hang`);

-- 2. Tài khoản người dùng mẫu (Mật khẩu mặc định: 123456)
INSERT INTO `hgb_nguoi_dung` (`ma_nguoi_dung`, `ma_hang_thanh_vien`, `ten_dang_nhap`, `mat_khau`, `ho_va_ten`, `thu_dien_tu`, `so_dien_thoai`, `dia_chi_nha`, `diem_tich_luy`, `vai_tro`, `da_xac_thuc_otp`) VALUES
(1, 4, 'admin', '$2y$10$e.wPZf7bY5z7YVq1Q9pX1e0rK6x3nBf6mPj4rOq8Z4G8kQh8zY9aC', 'Quản Trị Viên Hệ Thống', 'admin@fashionkids.vn', '0909123456', '59C Nguyễn Đình Chiểu, Q.3, TP.HCM', 1250, 'quan_tri_vien', 1),
(2, 2, 'staff01', '$2y$10$e.wPZf7bY5z7YVq1Q9pX1e0rK6x3nBf6mPj4rOq8Z4G8kQh8zY9aC', 'Lê Nguyễn Khánh Trình (Staff)', 'staff01@fashionkids.vn', '0908765432', '279 Nguyễn Tri Phương, Q.10, TP.HCM', 250, 'nhan_vien', 1),
(3, 3, 'customer01', '$2y$10$e.wPZf7bY5z7YVq1Q9pX1e0rK6x3nBf6mPj4rOq8Z4G8kQh8zY9aC', 'Mẹ Bé Bắp (Gold VIP)', 'mebebap@gmail.com', '0912345678', '124 Hoàng Hoa Thám, P.12, Q.Tân Bình, TP.HCM', 680, 'khach_hang', 1)
ON DUPLICATE KEY UPDATE `ho_va_ten`=VALUES(`ho_va_ten`);

-- 3. Danh mục sản phẩm
INSERT INTO `lbn_danh_muc` (`ma_danh_muc`, `ten_danh_muc`, `gioi_tinh`, `loai_hang`) VALUES
(1, 'Váy Công Chúa Bé Gái', 'be_gai', 'quan_ao'),
(2, 'Bộ Thun Thể Thao Bé Trai', 'be_trai', 'quan_ao'),
(3, 'Áo Khoác Sợi Tre Bamboo', 'unisex', 'quan_ao'),
(4, 'Giày Sandal Tập Đi Êm Chân', 'unisex', 'giay_dep')
ON DUPLICATE KEY UPDATE `ten_danh_muc`=VALUES(`ten_danh_muc`);

-- 4. Bảng kích cỡ chuẩn
INSERT INTO `lnkt_bang_kich_co` (`ma_kich_co`, `ma_danh_muc`, `loai_hang`, `gioi_tinh`, `ten_kich_co`, `chieu_cao_toi_thieu`, `chieu_cao_toi_da`, `chieu_dai_chan_toi_thieu`, `chieu_dai_chan_toi_da`) VALUES
(1, 1, 'quan_ao', 'be_gai', 'Size 1 (0-1T)', 50.0, 75.0, 0, 0),
(2, 1, 'quan_ao', 'be_gai', 'Size 2 (1-2T)', 75.1, 85.0, 0, 0),
(3, 1, 'quan_ao', 'be_gai', 'Size 3 (2-3T)', 85.1, 95.0, 0, 0),
(4, 1, 'quan_ao', 'be_gai', 'Size 4 (3-4T)', 95.1, 105.0, 0, 0),
(5, 1, 'quan_ao', 'be_gai', 'Size 5 (4-6T)', 105.1, 120.0, 0, 0),
(6, 2, 'quan_ao', 'be_trai', 'Size 1 (0-1T)', 50.0, 75.0, 0, 0),
(7, 2, 'quan_ao', 'be_trai', 'Size 2 (1-2T)', 75.1, 85.0, 0, 0),
(8, 2, 'quan_ao', 'be_trai', 'Size 3 (2-3T)', 85.1, 95.0, 0, 0),
(9, 2, 'quan_ao', 'be_trai', 'Size 4 (3-4T)', 95.1, 105.0, 0, 0),
(10, 2, 'quan_ao', 'be_trai', 'Size 5 (4-6T)', 105.1, 120.0, 0, 0),
(11, 4, 'giay_dep', 'unisex', 'Size 21', 0, 0, 11.5, 12.5),
(12, 4, 'giay_dep', 'unisex', 'Size 22', 0, 0, 12.6, 13.5),
(13, 4, 'giay_dep', 'unisex', 'Size 24', 0, 0, 13.6, 14.5),
(14, 4, 'giay_dep', 'unisex', 'Size 26', 0, 0, 14.6, 15.5),
(15, 4, 'giay_dep', 'unisex', 'Size 28', 0, 0, 15.6, 17.0)
ON DUPLICATE KEY UPDATE `ten_kich_co`=VALUES(`ten_kich_co`);

-- 5. Sản phẩm mẫu
INSERT INTO `lbn_san_pham` (`ma_san_pham`, `ma_danh_muc`, `ten_san_pham`, `gia_ban`, `so_luong_ton_kho`, `duong_dan_anh`, `mo_ta_chat_lieu`, `loai_kich_co`) VALUES
(101, 1, 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi', 285000.00, 45, '/FashionKids/public/uploads/products/dress-flower-01.jpg', '100% sợi tre tự nhiên mềm mịn, thoáng khí, kháng khuẩn vượt trội.', 'quan_ao'),
(102, 2, 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động', 225000.00, 60, '/FashionKids/public/uploads/products/dino-set-02.jpg', 'Chất liệu thun sợi tre Bamboo co giãn 4 chiều, thấm hút mồ hôi tối ưu.', 'quan_ao'),
(103, 4, 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân', 210000.00, 35, '/FashionKids/public/uploads/products/sandal-bamboo-03.jpg', 'Đế cao su non uốn dẻo 360 độ, lót đệm sợi tre chống trơn trượt.', 'giay_dep'),
(104, 3, 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV', 320000.00, 20, '/FashionKids/public/uploads/products/cardigan-uv-04.jpg', 'Sợi tre dệt 2 lớp chống tia cực tím UPF 50+, giữ ấm nhẹ nhàng ban đêm.', 'quan_ao')
ON DUPLICATE KEY UPDATE `ten_san_pham`=VALUES(`ten_san_pham`);

-- 6. Banner sự kiện mẫu
INSERT INTO `lnkt_banner_su_kien` (`ma_banner`, `tieu_de`, `duong_dan_anh`, `duong_dan_dieu_huong`, `loai_su_kien`, `dang_kich_hoat`, `thu_tu_hien_thi`) VALUES
(1, 'Bộ Sưu Tập Mùa Hè 2026 - 100% Sợi Tre Kháng Khuẩn Tự Nhiên', '/FashionKids/public/uploads/banners/summer-banner-01.jpg', '/FashionKids/views/pages/shop-catalog.php?tag=organic', 'summer_sale', 1, 1),
(2, 'Tuyển Đại Sứ Nhí KOL FashionKids - Nhận Hợp Đồng Trị Giá 20 Triệu', '/FashionKids/public/uploads/banners/kol-casting-banner.jpg', '/FashionKids/views/pages/kol-casting.php', 'casting', 1, 2)
ON DUPLICATE KEY UPDATE `tieu_de`=VALUES(`tieu_de`);


-- =============================================================================
-- CÁC STORED PROCEDURES & FUNCTIONS ĐỒNG BỘ CHUẨN TÊN GỌI K49 (ALIASES)
-- =============================================================================

DELIMITER $$

-- 1. sp_hgb_dang_ky_thanh_vien (Alias cho hgb_insert_user)
DROP PROCEDURE IF EXISTS `sp_hgb_dang_ky_thanh_vien`$$
CREATE PROCEDURE `sp_hgb_dang_ky_thanh_vien`(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_full_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_address VARCHAR(255),
    OUT p_new_user_id INT
)
BEGIN
    CALL `hgb_insert_user`(p_username, p_password, p_full_name, p_email, p_phone, p_address, 'khach_hang', p_new_user_id);
END$$

-- 2. sp_hgb_tich_diem_nang_hang (Alias cho hgb_update_user_points)
DROP PROCEDURE IF EXISTS `sp_hgb_tich_diem_nang_hang`$$
CREATE PROCEDURE `sp_hgb_tich_diem_nang_hang`(
    IN p_user_id INT,
    IN p_points_to_add INT
)
BEGIN
    CALL `hgb_update_user_points`(p_user_id, p_points_to_add);
END$$

-- 3. fn_hgb_tinh_phan_tram_giam_gia (Alias cho hgb_get_discount_percent)
DROP FUNCTION IF EXISTS `fn_hgb_tinh_phan_tram_giam_gia`$$
CREATE FUNCTION `fn_hgb_tinh_phan_tram_giam_gia`(
    p_user_id INT
) RETURNS DECIMAL(5,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    RETURN `hgb_get_discount_percent`(p_user_id);
END$$

-- 4. sp_lbn_them_san_pham_moi (Alias cho lbn_insert_product)
DROP PROCEDURE IF EXISTS `sp_lbn_them_san_pham_moi`$$
CREATE PROCEDURE `sp_lbn_them_san_pham_moi`(
    IN p_category_id INT,
    IN p_name VARCHAR(255),
    IN p_price DECIMAL(12,2),
    IN p_stock INT,
    IN p_image VARCHAR(255),
    IN p_description TEXT,
    IN p_size_type VARCHAR(20),
    OUT p_new_product_id INT
)
BEGIN
    CALL `lbn_insert_product`(p_category_id, p_name, p_price, p_stock, p_image, p_description, p_size_type, p_new_product_id);
END$$

-- 5. sp_lbn_nhap_them_kho_hang (Alias cho lbn_update_stock)
DROP PROCEDURE IF EXISTS `sp_lbn_nhap_them_kho_hang`$$
CREATE PROCEDURE `sp_lbn_nhap_them_kho_hang`(
    IN p_product_id INT,
    IN p_quantity_change INT
)
BEGIN
    CALL `lbn_update_stock`(p_product_id, p_quantity_change);
END$$

-- 6. fn_lbn_kiem_tra_ton_kho (Kiểm tra kho hàng có đủ bán không)
DROP FUNCTION IF EXISTS `fn_lbn_kiem_tra_ton_kho`$$
CREATE FUNCTION `fn_lbn_kiem_tra_ton_kho`(
    p_product_id INT,
    p_quantity_needed INT
) RETURNS TINYINT(1)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_stock INT DEFAULT 0;
    SELECT COALESCE(`so_luong_ton_kho`, 0) INTO v_stock
    FROM `lbn_san_pham`
    WHERE `ma_san_pham` = p_product_id;

    IF v_stock >= p_quantity_needed THEN
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END$$

-- 7. fn_lnkt_goi_y_kich_co_chuan (Alias cho lnkt_suggest_size hỗ trợ đa số đo)
DROP FUNCTION IF EXISTS `fn_lnkt_goi_y_kich_co_chuan`$$
CREATE FUNCTION `fn_lnkt_goi_y_kich_co_chuan`(
    p_category_id INT,
    p_height FLOAT,
    p_chest FLOAT,
    p_waist FLOAT,
    p_foot FLOAT
) RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_measure FLOAT;
    IF p_category_id = 4 AND p_foot IS NOT NULL AND p_foot > 0 THEN
        SET v_measure = p_foot;
    ELSE
        SET v_measure = p_height;
    END IF;
    RETURN `lnkt_suggest_size`(p_category_id, v_measure);
END$$

-- 8. sp_lnkt_cap_nhat_trang_thai_don_hang (Alias cho lnkt_update_order_status)
DROP PROCEDURE IF EXISTS `sp_lnkt_cap_nhat_trang_thai_don_hang`$$
CREATE PROCEDURE `sp_lnkt_cap_nhat_trang_thai_don_hang`(
    IN p_order_id INT,
    IN p_handled_by INT,
    IN p_new_status VARCHAR(50)
)
BEGIN
    CALL `lnkt_update_order_status`(p_order_id, p_new_status, p_handled_by);
END$$

-- 9. sp_tgb_tao_don_hang_va_chi_tiet (Giao dịch hoàn chỉnh đặt hàng)
DROP PROCEDURE IF EXISTS `sp_tgb_tao_don_hang_va_chi_tiet`$$
CREATE PROCEDURE `sp_tgb_tao_don_hang_va_chi_tiet`(
    IN p_user_id INT,
    IN p_receiver_name VARCHAR(100),
    IN p_receiver_phone VARCHAR(20),
    IN p_receiver_email VARCHAR(100),
    IN p_shipping_address VARCHAR(255),
    IN p_province VARCHAR(100),
    IN p_district VARCHAR(100),
    IN p_product_id INT,
    IN p_size VARCHAR(20),
    IN p_quantity INT,
    OUT p_order_id INT
)
BEGIN
    DECLARE v_price DECIMAL(12,2) DEFAULT 0.00;
    DECLARE v_discount_rate DECIMAL(5,2) DEFAULT 0.00;
    DECLARE v_subtotal DECIMAL(12,2) DEFAULT 0.00;
    DECLARE v_discount DECIMAL(12,2) DEFAULT 0.00;
    DECLARE v_total DECIMAL(12,2) DEFAULT 0.00;

    -- Lấy giá bán sản phẩm
    SELECT `gia_ban` INTO v_price
    FROM `lbn_san_pham`
    WHERE `ma_san_pham` = p_product_id;

    SET v_subtotal = v_price * p_quantity;

    -- Lấy chiết khấu VIP nếu có tài khoản
    IF p_user_id IS NOT NULL THEN
        SET v_discount_rate = `hgb_get_discount_percent`(p_user_id);
        SET v_discount = (v_subtotal * v_discount_rate) / 100;
    END IF;

    SET v_total = v_subtotal - v_discount;

    -- 1. Tạo đơn hàng
    CALL `lnkt_create_order`(
        p_user_id, p_receiver_name, p_receiver_phone, p_receiver_email,
        p_shipping_address, p_province, p_district,
        v_subtotal, v_discount, v_total, p_order_id
    );

    -- 2. Thêm dòng chi tiết và trừ tồn kho
    CALL `tgb_add_order_item`(p_order_id, p_product_id, p_size, p_quantity, v_price);

    -- 3. Tích điểm thưởng VIP nếu có tài khoản
    IF p_user_id IS NOT NULL THEN
        CALL `hgb_update_user_points`(p_user_id, FLOOR(v_total / 10000));
    END IF;
END$$

DELIMITER ;
