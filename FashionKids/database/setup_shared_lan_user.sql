-- =========================================================================
-- MODULE 2.2 (GIAI ĐOẠN 5): CẤP QUYỀN TÀI KHOẢN DÙNG CHUNG QUA MẠNG LAN (SHARED DATABASE)
-- Chạy kịch bản này trên phpMyAdmin của máy chủ Hồ Gia Bảo (Tab SQL ở trang chủ phpMyAdmin)
-- để 3 máy thành viên (Lê Bảo Ngọc, Lê Nguyễn Khánh Trình, Trần Gia Bảo) kết nối qua LAN.
-- =========================================================================

CREATE USER IF NOT EXISTS 'team_fashionkids'@'%' IDENTIFIED BY '123456';
ALTER USER 'team_fashionkids'@'%' IDENTIFIED BY '123456';

GRANT ALL PRIVILEGES ON `db_fashionkids`.* TO 'team_fashionkids'@'%' WITH GRANT OPTION;
GRANT EXECUTE ON `db_fashionkids`.* TO 'team_fashionkids'@'%';

FLUSH PRIVILEGES;

-- Kiểm tra tài khoản đã được khởi tạo với Host = '%'
SELECT User, Host, plugin FROM mysql.user WHERE User = 'team_fashionkids';
