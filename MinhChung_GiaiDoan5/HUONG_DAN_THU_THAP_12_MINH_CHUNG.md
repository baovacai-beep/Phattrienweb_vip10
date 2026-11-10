# CẨM NANG NỘP MINH CHỨNG GIAI ĐOẠN 5 - DỰ ÁN FASHIONKIDS (NHÓM 10)

Cấu trúc thư mục nộp minh chứng Giai đoạn 5 đã được chuẩn hóa gồm **4 thư mục** tương ứng với **12 ảnh chụp minh chứng bắt buộc**:

```text
MinhChung_GiaiDoan5/
├── 01_Git_Network/
│   ├── 01_GitHub_Network_Graph_4_Branches.png
│   ├── 02_Closed_Pull_Requests_List.png
│   └── 03_PR_Code_Review_Comments_Merged.png
├── 02_Database_Evidence/
│   ├── 04_phpMyAdmin_12_Routines_Happy_Path.png
│   ├── 05_Config_Database_Multi_Env.png
│   └── 06_LAN_Client_Access_Success.png
├── 03_Team_Collaboration/
│   ├── 07_Trello_Kanban_Board_Overview.png
│   ├── 08_Trello_Card_Checklist_Detail.png
│   └── 09_MS_Teams_Live_Code_Review.png
└── 04_Hosting_Deployment/
    ├── 10_InfinityFree_Control_Panel_Active.png
    ├── 11_Hosting_phpMyAdmin_15_Tables.png
    └── 12_Live_Website_Domain_AJAX_Working.png
```

---

## MODULE 1: ĐỒNG BỘ 4 NHÁNH TÍNH NĂNG GIT & QUY TRÌNH PULL REQUEST (`01_Git_Network/`)

### 1. Danh sách 4 nhánh tính năng chuẩn hóa đã tạo sẵn trong Git:
| Nhánh tính năng | Thành viên phụ trách | Mã nguồn & Module nghiệp vụ |
| :--- | :--- | :--- |
| `feature/hgb-auth-layout` | **Hồ Gia Bảo** | Layout chung (`header.php`, `footer.php`), Xác thực (`AuthController.php`, `UserModel.php`, OTP Session, cấu hình đa môi trường `config/database.php`, bảng `hgb_hang_thanh_vien`, `hgb_nguoi_dung`) |
| `feature/lbn-catalog-stock` | **Lê Bảo Ngọc** | Danh mục & Sản phẩm (`CatalogController.php`, `ProductController.php`, `ProductModel.php`, quản lý tồn kho realtime, bảng `lbn_danh_muc`, `lbn_san_pham`) |
| `feature/lnkt-order-size` | **Lê Nguyễn Khánh Trình** | Tư vấn chọn kích cỡ (`SizeController.php`, `SizeChartModel.php`, `OrderManagementModel.php`, bảng `lnkt_bang_kich_co`, `lnkt_don_hang`, `lnkt_dang_ky_kol_nhi`) |
| `feature/tgb-checkout-ajax` | **Trần Gia Bảo** | Giỏ hàng, Đặt hàng AJAX & Đánh giá (`CartController.php`, `OrderController.php`, `ReviewModel.php`, bảng `tgb_chi_tiet_don_hang`, `tgb_danh_gia_san_pham`, `tgb_tin_nhan_tu_van`) |

### 2. Lệnh đẩy toàn bộ 4 nhánh và nhánh `develop` lên GitHub:
Mở Terminal tại thư mục gốc dự án và chạy:
```bash
git push origin feature/hgb-auth-layout feature/lbn-catalog-stock feature/lnkt-order-size feature/tgb-checkout-ajax develop
```

### 3. Ba ảnh chụp cần lưu vào `01_Git_Network/`:
1. **`01_GitHub_Network_Graph_4_Branches.png`**: Truy cập `https://github.com/baovacai-beep/Phattrienweb_vip10/network` -> Chụp biểu đồ nhánh có đường rẽ và gộp của 4 thành viên.
2. **`02_Closed_Pull_Requests_List.png`**: Vào tab **Pull requests** -> Chọn bộ lọc **Closed** -> Chụp danh sách các PR đã hoàn tất gộp vào `develop`.
3. **`03_PR_Code_Review_Comments_Merged.png`**: Mở chi tiết 1 PR -> Chụp phần bình luận **Review Code** của Nhóm trưởng và trạng thái màu tím **Merged**.

---

## MODULE 2: KIỂM THỬ CSDL & KẾT NỐI SHARED DATABASE MẠNG LAN (`02_Database_Evidence/`)

### 1. Chạy kịch bản kiểm thử 12 Stored Routines (Happy Path):
- Mở phpMyAdmin (`http://localhost/phpmyadmin` hoặc `http://192.168.1.17/phpmyadmin`) -> Chọn CSDL **`db_fashionkids`** -> Mở tab **SQL**.
- Mở file [`FashionKids/database/test_12_routines_happy_path.sql`](../../FashionKids/database/test_12_routines_happy_path.sql), copy toàn bộ nội dung dán vào ô SQL và nhấn **Go (Thực hiện)**.
- File đã tích hợp sẵn khối lệnh khôi phục trạng thái mẫu ở đầu nên chạy lại bao nhiêu lần cũng báo dấu tích **Xanh 100%** và hiển thị đầy đủ:
  - `ma_khach_hang_moi`
  - `phan_tram_giam` = `10` (Hạng Vàng VIP)
  - `size_goi_y` = `Size 2 (2-3T)`
  - `diem_danh_gia_sao` = `5.0`

### 2. Cấp quyền tài khoản `team_fashionkids` cho mạng LAN:
- Mở tab **SQL** ở trang chủ phpMyAdmin trên máy Hồ Gia Bảo, chạy nội dung file [`FashionKids/database/setup_shared_lan_user.sql`](../../FashionKids/database/setup_shared_lan_user.sql).

### 3. Ba ảnh chụp cần lưu vào `02_Database_Evidence/`:
1. **`04_phpMyAdmin_12_Routines_Happy_Path.png`**: Ảnh kết quả chạy 12 câu lệnh SQL (`CALL sp_...`, `SELECT fn_...`) báo xanh kèm các bảng dữ liệu trả về.
2. **`05_Config_Database_Multi_Env.png`**: Mở file [`FashionKids/config/database.php`](../../FashionKids/config/database.php) và [`FashionKids/core/Database.php`](../../FashionKids/core/Database.php) trong VS Code -> Chụp đoạn code cấu hình tự động 3 môi trường (InfinityFree Hosting, Localhost, LAN `192.168.1.15`).
3. **`06_LAN_Client_Access_Success.png`**: Chụp màn hình máy của thành viên khác truy cập website hoặc phpMyAdmin qua IP LAN (`192.168.1.15` / `192.168.1.17`) hiển thị đầy đủ sản phẩm từ CSDL máy chủ.

---

## MODULE 3: TRELLO KANBAN & HỌP TRỰC TUYẾN MICROSOFT TEAMS (`03_Team_Collaboration/`)

### 1. Thiết lập bảng Trello `[Kỹ thuật Web] Nhóm 10 - Dự án FashionKids`:
Tạo 5 cột: `Backlog` | `To Do` | `In Progress` | `Code Review` | `Done` và 5 thẻ công việc:
- **Thẻ 1 (Done - Nhãn Đỏ)**: `[HGB] Xây dựng Layout MVC, Đăng ký/Đăng nhập & OTP Session` (Checklist: Thiết kế bảng `hgb_nguoi_dung` và `hgb_hang_thanh_vien`; Viết `sp_hgb_dang_ky_thanh_vien`; Tích hợp AJAX kiểm tra trùng tài khoản và gửi OTP).
- **Thẻ 2 (Done - Nhãn Xanh lá)**: `[LBN] Phân hệ Danh mục, Tìm kiếm & Quản lý tồn kho sản phẩm` (Checklist: Thiết kế bảng `lbn_danh_muc`, `lbn_san_pham`; Viết `fn_lbn_kiem_tra_ton_kho`; Lọc sản phẩm theo giá không reload trang).
- **Thẻ 3 (Code Review - Nhãn Vàng)**: `[LNKT] Thuật toán gợi ý kích cỡ thông minh & Duyệt KOL nhí` (Checklist: Viết hàm `fn_lnkt_goi_y_kich_co_chuan`; Xây dựng modal nhập số đo chiều cao/vòng ngực cho bé).
- **Thẻ 4 (In Progress - Nhãn Tím)**: `[TGB] Giỏ hàng, Đặt hàng Transaction & Đánh giá 5 sao` (Checklist: Viết `sp_tgb_tao_don_hang_va_chi_tiet` tự động trừ kho; Tích hợp API chọn Tỉnh/Thành phố, Quận/Huyện).
- **Thẻ 5 (In Progress - Nhãn Xanh dương)**: `[All] Triển khai mã nguồn và CSDL lên Hosting InfinityFree`.

### 2. Ba ảnh chụp cần lưu vào `03_Team_Collaboration/`:
1. **`07_Trello_Kanban_Board_Overview.png`**: Toàn cảnh bảng Trello với 5 cột, nhãn màu và Avatar thành viên.
2. **`08_Trello_Card_Checklist_Detail.png`**: Chi tiết 1 thẻ Trello có Checklist tiến độ đạt 100% và đính kèm link PR GitHub.
3. **`09_MS_Teams_Live_Code_Review.png`**: Phòng họp Microsoft Teams (bật Camera, chia sẻ màn hình VS Code / phpMyAdmin và khung Chat thảo luận).

---

## MODULE 4: TRIỂN KHAI WEBSITE LÊN HOSTING INFINITYFREE (`04_Hosting_Deployment/`)

### 1. Nhập CSDL lên InfinityFree không bị lỗi `#1044` hoặc `#1227`:
- Trên trình quản lý **MySQL Databases** của InfinityFree, nhấn **phpMyAdmin** của CSDL `if0_..._fashionkids`.
- Chuyển sang tab **Import (Nhập)** -> Chọn file đã được chuẩn hóa riêng cho Hosting:
  👉 [`FashionKids/database/db_fashionkids_hosting_infinityfree.sql`](../../FashionKids/database/db_fashionkids_hosting_infinityfree.sql)
  *(File này đã loại bỏ hoàn toàn câu lệnh `CREATE DATABASE`, `USE db_fashionkids` và mệnh đề `DEFINER=` nên nạp 15 bảng + 12 Stored Routines thành công 100% trên tài khoản Free Hosting).*

### 2. Cập nhật thông số kết nối Hosting trong `FashionKids/config/database.php`:
- Điền `DB_HOST` (ví dụ `sql205.infinityfree.com`), `DB_NAME` (`if0_..._fashionkids`), `DB_USER` (`if0_...`), `DB_PASS` của tài khoản InfinityFree vào nhánh `if ($isHosting)` trong [`FashionKids/config/database.php`](../../FashionKids/config/database.php).
- Nén toàn bộ nội dung bên trong thư mục `FashionKids/` thành file `fashionkids_deploy.zip`, tải lên thư mục `/htdocs/` của InfinityFree File Manager và nhấn **Extract**.
- Nhờ file [`.htaccess`](../../FashionKids/.htaccess) và [`config/app.php`](../../FashionKids/config/app.php) đã được cấu hình tự động nhận diện đường dẫn gốc, toàn bộ giao diện, hình ảnh, CSS và các tính năng AJAX sẽ hoạt động trơn tru ngay lập tức.

### 3. Ba ảnh chụp cần lưu vào `04_Hosting_Deployment/`:
1. **`10_InfinityFree_Control_Panel_Active.png`**: Giao diện Control Panel của InfinityFree hiển thị trạng thái **Active** và thông số MySQL Hostname / Database Name.
2. **`11_Hosting_phpMyAdmin_15_Tables.png`**: Giao diện phpMyAdmin trên máy chủ Hosting (`sqlXXX.infinityfree.com`) hiển thị 15 bảng của CSDL `if0_..._fashionkids`.
3. **`12_Live_Website_Domain_AJAX_Working.png`**: Toàn màn hình trình duyệt chạy website trên tên miền thật (`http://fashionkids-nhom10.infinityfreeapp.com`), thấy rõ thanh URL công khai và tính năng gợi ý size / lọc sản phẩm hoạt động.
