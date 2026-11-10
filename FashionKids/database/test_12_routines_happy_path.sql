-- =========================================================================
-- MODULE 2 (GIAI ĐOẠN 5): KỊCH BẢN KIỂM THỬ 12 STORED ROUTINES (HAPPY PATH)
-- Hướng dẫn: Mở phpMyAdmin -> Chọn CSDL `db_fashionkids` -> Mở tab SQL
-- Dán toàn bộ nội dung file này vào và nhấn "Go (Thực hiện)" để chụp ảnh minh chứng.
-- =========================================================================

-- Bước chuẩn bị tự động: Khôi phục trạng thái mẫu nếu kịch bản đã từng chạy trước đó
-- (Giúp chạy lại bao nhiêu lần cũng báo dấu tích XANH 100%, không bị lỗi trùng lặp)
DELETE FROM `tgb_danh_gia_san_pham` WHERE `ma_don_hang` = 1 AND `ma_san_pham` = 1 AND `ma_nguoi_dung` = 3;
DELETE FROM `lnkt_don_hang` WHERE `thu_dien_tu_nhan` = 'hoa.nguyen@gmail.com';
DELETE FROM `hgb_nguoi_dung` WHERE `ten_dang_nhap` = 'khach_hang_moi_1' OR `thu_dien_tu` = 'hoa.nguyen@gmail.com';
DELETE FROM `lbn_san_pham` WHERE `ten_san_pham` = 'Đầm Công Chúa Ren Cao Cấp Mới';
UPDATE `lnkt_don_hang` SET `trang_thai_don_hang` = 'cho_duyet', `ma_nhan_vien_xu_ly` = NULL WHERE `ma_don_hang` = 2;
UPDATE `lnkt_dang_ky_kol_nhi` SET `trang_thai_duyet` = 'cho_duyet' WHERE `ma_ho_so` = 1;

-- Test 1: Đăng ký thành viên mới hoàn toàn (Hồ Gia Bảo - Nhận Hạng Đồng)
CALL sp_hgb_dang_ky_thanh_vien('khach_hang_moi_1', 'hash_pass_123', 'Nguyễn Thị Hoa', 'hoa.nguyen@gmail.com', '0912345678', 'Q.7, TP.HCM', @ma_user_moi);
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
