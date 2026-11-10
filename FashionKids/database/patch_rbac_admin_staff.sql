-- ============================================================================
-- PATCH RBAC Admin/Staff - FashionKids (chạy SAU db_fashionkids.sql)
-- Bổ sung 2 Stored Procedure mà tầng Controller gọi:
--   1. sp_lnkt_duyet_ho_so_kol_nhi : CHỈ quan_tri được quyết định Trúng tuyển / Từ chối
--   2. tgb_danh_dau_tin_da_doc     : đồng bộ trạng thái "đã đọc" của Live Chat
-- ============================================================================
USE `db_fashionkids`;

DELIMITER //

DROP PROCEDURE IF EXISTS `sp_lnkt_duyet_ho_so_kol_nhi`//
CREATE PROCEDURE `sp_lnkt_duyet_ho_so_kol_nhi`(
    IN  p_ma_ho_so    INT,
    IN  p_ma_quan_tri INT,
    IN  p_quyet_dinh  VARCHAR(20),
    OUT p_ket_qua     VARCHAR(20)
)
BEGIN
    DECLARE v_vai_tro   VARCHAR(20);
    DECLARE v_trang_thai VARCHAR(20);

    IF p_quyet_dinh NOT IN ('trung_tuyen', 'tu_choi') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_09]: Quyết định phải là trung_tuyen hoặc tu_choi!';
    END IF;

    SELECT `vai_tro` INTO v_vai_tro FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = p_ma_quan_tri;
    IF v_vai_tro IS NULL OR v_vai_tro <> 'quan_tri' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_10]: Chỉ Quản trị viên mới có quyền quyết định Trúng tuyển/Từ chối hồ sơ KOL nhí!';
    END IF;

    SELECT `trang_thai_duyet` INTO v_trang_thai FROM `lnkt_dang_ky_kol_nhi` WHERE `ma_ho_so` = p_ma_ho_so;
    IF v_trang_thai IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_07]: Mã hồ sơ ứng tuyển KOL nhí không tồn tại!';
    END IF;
    IF v_trang_thai IN ('trung_tuyen', 'tu_choi') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [LNKT_ERR_11]: Hồ sơ đã có quyết định cuối cùng, không thể thay đổi!';
    END IF;

    CALL `sp_lnkt_xet_duyet_kol_nhi`(p_ma_ho_so, p_ma_quan_tri, p_quyet_dinh);
    SET p_ket_qua = p_quyet_dinh;
END//

DROP PROCEDURE IF EXISTS `tgb_danh_dau_tin_da_doc`//
CREATE PROCEDURE `tgb_danh_dau_tin_da_doc`(
    IN  p_ma_phien_khach VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN  p_ma_nhan_vien   INT,
    OUT p_so_tin_da_doc  INT
)
BEGIN
    DECLARE v_vai_tro VARCHAR(20);
    SELECT `vai_tro` INTO v_vai_tro FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = p_ma_nhan_vien;
    IF v_vai_tro IS NULL OR v_vai_tro NOT IN ('nhan_vien', 'quan_tri') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi [TGB_ERR_01]: Chỉ Nhân viên/Quản trị mới được đánh dấu đã đọc tin nhắn!';
    END IF;

    UPDATE `tgb_tin_nhan_tu_van`
    SET `da_xem` = 1
    WHERE `ma_phien_khach` = p_ma_phien_khach
      AND `nguoi_gui` = 'khach_hang'
      AND `da_xem` = 0;
    SET p_so_tin_da_doc = ROW_COUNT();
END//

DELIMITER ;
