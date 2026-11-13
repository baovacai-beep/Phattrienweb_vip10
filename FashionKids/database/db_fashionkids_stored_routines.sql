-- =========================================================================
-- DỰ ÁN: FASHIONKIDS - HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ THỜI TRANG TRẺ EM
-- MỤC 4.3: TẬP HỢP TRUY VẤN NÂNG CAO, THỦ TỤC (STORED PROCEDURES) & HÀM (FUNCTIONS)
-- NHÓM THỰC HIỆN: NHÓM 10 (hgb, lbn, lnkt, tgb)
-- Tương thích: MySQL 8.0+, MariaDB 10.4+, phpMyAdmin
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELIMITER //

-- =========================================================================
-- PHẦN 1. PHÂN HỆ KHÁCH HÀNG & THÀNH VIÊN VIP (HỒ GIA BẢO - hgb_)
-- =========================================================================

-- 1.1. Hàm tính % chiết khấu giảm giá VIP tự động (fn_hgb_tinh_phan_tram_giam_gia)
DROP FUNCTION IF EXISTS `fn_hgb_tinh_phan_tram_giam_gia`//
CREATE FUNCTION `fn_hgb_tinh_phan_tram_giam_gia`(
    p_ma_nguoi_dung INT
) RETURNS FLOAT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_phan_tram FLOAT DEFAULT 0.0;
    
    -- Nếu là khách vãng lai (Guest) chưa đăng nhập -> không có chiết khấu VIP
    IF p_ma_nguoi_dung IS NULL OR p_ma_nguoi_dung <= 0 THEN
        RETURN 0.0;
    END IF;
    
    -- Kiểm tra tài khoản có tồn tại trên hệ thống hay không
    IF NOT EXISTS (SELECT 1 FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = p_ma_nguoi_dung) THEN
        RETURN 0.0;
    END IF;
    
    -- Lấy % chiết khấu theo bảng hạng thành viên (Đồng: 0%, Bạc: 5%, Vàng: 10%, Kim Cương: 15%)
    SELECT COALESCE(h.`phan_tram_giam_gia`, 0.0) INTO v_phan_tram
    FROM `hgb_nguoi_dung` u
    LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
    WHERE u.`ma_nguoi_dung` = p_ma_nguoi_dung;
    
    RETURN v_phan_tram;
END//

-- 1.2. Thủ tục tích điểm và tự động nâng hạng VIP (sp_hgb_tich_diem_nang_hang)
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
    
    -- Tìm hạng thành viên cao nhất mà mức điểm mới thỏa mãn điều kiện
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

-- 1.3. Thủ tục đăng ký thành viên mới (sp_hgb_dang_ky_thanh_vien)
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
    
    IF EXISTS (SELECT 1 FROM `hgb_nguoi_dung` WHERE `ten_dang_nhap` = p_ten_dang_nhap COLLATE utf8mb4_unicode_ci) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [HGB_ERR_02]: Tên đăng nhập này đã được sử dụng trên hệ thống!';
    END IF;
    
    IF EXISTS (SELECT 1 FROM `hgb_nguoi_dung` WHERE `thu_dien_tu` = p_thu_dien_tu COLLATE utf8mb4_unicode_ci) THEN
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


-- =========================================================================
-- PHẦN 2. PHÂN HỆ SẢN PHẨM & TỒN KHO (LÊ BẢO NGỌC - lbn_)
-- =========================================================================

-- 2.1. Hàm kiểm tra tồn kho khả dụng trước khi đặt mua (fn_lbn_kiem_tra_ton_kho)
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

-- 2.2. Thủ tục thêm mới sản phẩm vào kho hàng (sp_lbn_them_san_pham_moi)
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

-- 2.3. Thủ tục nhập bổ sung tồn kho hàng hóa (sp_lbn_nhap_them_kho_hang)
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


-- =========================================================================
-- PHẦN 3. PHÂN HỆ ĐÁNH GIÁ & ĐẶT HÀNG (TRẦN GIA BẢO - tgb_)
-- =========================================================================

-- 3.1. Hàm tính điểm đánh giá sao trung bình của sản phẩm (fn_tgb_tinh_sao_trung_binh_san_pham)
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

-- 3.2. Hàm đếm số người mua thực tế đã hoàn tất đơn hàng (fn_tgb_dem_so_nguoi_mua)
DROP FUNCTION IF EXISTS `fn_tgb_dem_so_nguoi_mua`//
CREATE FUNCTION `fn_tgb_dem_so_nguoi_mua`(
    p_ma_san_pham INT
) RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_so_nguoi_mua INT DEFAULT 0;
    
    -- Đếm số khách hàng duy nhất mua sản phẩm thành công và không bị hoàn trả
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

-- 3.3. Thủ tục tạo đơn hàng, áp dụng giảm giá VIP và trừ tồn kho tự động (sp_tgb_tao_don_hang_va_chi_tiet)
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
    
    -- Triệu gọi hàm của Hồ Gia Bảo để tính mức giảm giá VIP tự động
    SET v_phan_tram_giam_gia = `fn_hgb_tinh_phan_tram_giam_gia`(p_ma_nguoi_dung);
    SET v_tien_giam_gia      = (v_tien_hang_tam_tinh * v_phan_tram_giam_gia) / 100.0;
    SET v_tong_thanh_toan    = v_tien_hang_tam_tinh - v_tien_giam_gia;
    
    START TRANSACTION;
        -- 1. Tạo đơn hàng tổng
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
        
        -- 2. Thêm vào chi tiết đơn hàng
        INSERT INTO `tgb_chi_tiet_don_hang` (
            `ma_don_hang`, `ma_san_pham`, `kich_co_chon`, `so_luong`, `don_gia_mua`
        ) VALUES (
            p_ma_don_hang_moi, p_ma_san_pham, TRIM(p_kich_co_chon), p_so_luong, v_gia_ban
        );
        
        -- 3. Trừ số lượng tồn kho tự động
        UPDATE `lbn_san_pham`
        SET `so_luong_ton_kho` = `so_luong_ton_kho` - p_so_luong
        WHERE `ma_san_pham` = p_ma_san_pham;
    COMMIT;
END//

-- 3.4. Thủ tục gửi đánh giá sản phẩm sau khi đơn hoàn tất (sp_tgb_gui_danh_gia_san_pham)
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
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_07]: Đơn hàng phải giao dịch thành công (hoàn tất) mới được đánh giá!';
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


-- =========================================================================
-- PHẦN 4. PHÂN HỆ QUY CHUẨN KÍCH CỠ, ĐƠN HÀNG & HOÀN HÀNG (LÊ NGUYỄN KHÁNH TRÌNH - lnkt_)
-- =========================================================================

-- 4.1. Hàm gợi ý kích cỡ trang phục/giày dép chuẩn xác theo số đo (fn_lnkt_goi_y_kich_co_chuan)
DROP FUNCTION IF EXISTS `fn_lnkt_goi_y_kich_co_chuan`//
CREATE FUNCTION `fn_lnkt_goi_y_kich_co_chuan`(
    p_ma_danh_muc    INT,
    p_chieu_cao      FLOAT,
    p_vong_nguc      FLOAT,
    p_vong_eo        FLOAT,
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
    
    -- Phân nhánh tính toán: Quần áo (theo Chiều cao, Ngực, Eo)
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
        
    -- Phân nhánh tính toán: Giày dép (theo Chiều dài bàn chân)
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

-- 4.2. Thủ tục cập nhật trạng thái đơn hàng & tích điểm thăng hạng VIP (sp_lnkt_cap_nhat_trang_thai_don_hang)
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
    
    -- Nếu hủy đơn -> Hoàn lại số lượng tồn kho tự động
    IF p_trang_thai_moi = 'da_huy' THEN
        UPDATE `lbn_san_pham` sp
        JOIN `tgb_chi_tiet_don_hang` ct ON sp.`ma_san_pham` = ct.`ma_san_pham`
        SET sp.`so_luong_ton_kho` = sp.`so_luong_ton_kho` + ct.`so_luong`
        WHERE ct.`ma_don_hang` = p_ma_don_hang;
    END IF;
    
    -- Nếu đơn hàng hoàn tất và có tài khoản thành viên -> Tích điểm nâng hạng tự động (10.000đ = 1 điểm)
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

-- 4.3. Thủ tục gửi yêu cầu hoàn hàng / đổi trả (sp_lnkt_tao_yeu_cau_hoan_hang)
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
    
    IF EXISTS (SELECT 1 FROM `lnkt_yeu_cau_hoan_hang` WHERE `ma_don_hang` = p_ma_don_hang) THEN
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

-- 4.4. Thủ tục xử lý hoàn hàng, hoàn kho và thu hồi điểm VIP (sp_lnkt_xu_ly_hoan_hang)
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

    -- Khi chuyển trạng thái sang "hoan_tat_hoan": Hoàn tồn kho + Thu hồi điểm thưởng
    IF p_trang_thai_moi = 'hoan_tat_hoan' THEN
        START TRANSACTION;
            UPDATE `lnkt_yeu_cau_hoan_hang`
            SET `trang_thai_hoan`    = 'hoan_tat_hoan',
                `ma_nhan_vien_xu_ly` = p_ma_nhan_vien,
                `ghi_chu_xu_ly`      = COALESCE(NULLIF(TRIM(COALESCE(p_ghi_chu, '')), ''), `ghi_chu_xu_ly`),
                `ngay_cap_nhat`      = NOW()
            WHERE `ma_hoan_hang` = p_ma_hoan_hang;

            -- Hoàn lại hàng vào tồn kho
            UPDATE `lbn_san_pham` sp
            JOIN `tgb_chi_tiet_don_hang` ct ON sp.`ma_san_pham` = ct.`ma_san_pham`
            SET sp.`so_luong_ton_kho` = sp.`so_luong_ton_kho` + ct.`so_luong`
            WHERE ct.`ma_don_hang` = v_ma_don_hang;

            -- Thu hồi điểm tích lũy của khách
            SET v_diem_tru = FLOOR(COALESCE(v_so_tien_hoan, 0) / 10000);
            IF v_ma_nguoi_dung IS NOT NULL AND v_diem_tru > 0 THEN
                SELECT COALESCE(`diem_tich_luy`, 0) INTO v_diem_hien_tai
                FROM `hgb_nguoi_dung`
                WHERE `ma_nguoi_dung` = v_ma_nguoi_dung
                FOR UPDATE;

                SET v_diem_moi = GREATEST(0, COALESCE(v_diem_hien_tai, 0) - v_diem_tru);

                -- Hạ hạng thành viên tương ứng nếu điểm tụt xuống dưới ngưỡng
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
SET FOREIGN_KEY_CHECKS = 1;
