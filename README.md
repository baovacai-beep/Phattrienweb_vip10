# 👶 FashionKids - Website Thương Mại Điện Tử Thời Trang Trẻ Em

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 4" />
  <img src="https://img.shields.io/badge/Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white" alt="Figma" />
  <img src="https://img.shields.io/badge/Status-Completed-success?style=for-the-badge" alt="Status" />
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License" />
</p>

---

## 1. Giới Thiệu Dự Án

**FashionKids** là nền tảng thương mại điện tử chuyên cung cấp các sản phẩm thời trang an toàn, kháng khuẩn dành riêng cho trẻ em (từ sơ sinh đến 6 tuổi). Hệ thống được xây dựng theo mô hình MVC hiện đại kết hợp với cơ chế quản lý dữ liệu toàn vẹn trên hệ quản trị MySQL thông qua 12 Stored Procedures & Functions nghiệp vụ.

* **Đơn vị:** Khoa Công nghệ Thông tin Kinh doanh - Đại học Kinh tế TP. Hồ Chí Minh (UEH)
* **Học phần:** Phát triển Ứng dụng Web / Lập trình Web
* **Nhóm thực hiện:** Nhóm 10

---

## 2. Thành Viên Nhóm & Phân Công Nhiệm Vụ

Dự án áp dụng quy chuẩn tiền tố cá nhân (`<prefix>_<entity/routine>`) để quản lý mã nguồn và cơ sở dữ liệu:

| STT | Họ và Tên | Vai Trò | Tiền Tố | Thực Thể Phụ Trách | Stored Routines Đảm Nhận |
| :---: | :--- | :---: | :---: | :--- | :--- |
| **1** | **Hồ Gia Bảo** | Nhóm Trưởng | `hgb_` | `hgb_membership_tiers`<br>`hgb_users` | 1. `hgb_insert_user`<br>2. `hgb_update_user_points`<br>3. `hgb_get_discount_percent` |
| **2** | **Lê Bảo Ngọc** | Thành Viên | `lbn_` | `lbn_categories`<br>`lbn_products` | 4. `lbn_insert_product`<br>5. `lbn_update_stock`<br>6. `lbn_count_products_by_category` |
| **3** | **Lê Nguyễn Khánh Trình** | Thành Viên | `lnkt_` | `lnkt_size_charts`<br>`lnkt_orders` | 7. `lnkt_create_order`<br>8. `lnkt_update_order_status`<br>9. `lnkt_suggest_size` |
| **4** | **Trần Gia Bảo** | Thành Viên | `tgb_` | `tgb_order_items`<br>`tgb_chat_messages` | 10. `tgb_add_order_item`<br>11. `tgb_send_chat_message`<br>12. `tgb_mark_chat_as_read` |

---

## 3. Tính Năng Nổi Bật

### Phân Hệ Khách Hàng (Customer & Guest)
* **Thuật toán gợi ý size tự động (AJAX Size Recommendation):** Nhập chiều cao hoặc độ dài bàn chân của bé, hệ thống tự động trả về kích cỡ chuẩn dựa trên danh mục sản phẩm tương ứng.
* **Hệ thống cấp bậc thành viên VIP:** Tự động tích lũy điểm (10.000 VNĐ = 1 điểm) và tự động thăng hạng (Đồng $\rightarrow$ Bạc $\rightarrow$ Vàng $\rightarrow$ Kim Cương) để nhận mức giảm giá lên đến 15%.
* **Tích hợp Tỉnh / Thành phố:** Tích hợp Provinces Open API chuẩn hóa dữ liệu địa chỉ nhận hàng.
* **Live Chat CSKH thời gian thực:** Hỗ trợ tư vấn phom dáng/chất liệu cho cả khách đăng nhập và khách vãng lai (Guest Tracking qua `session_id`).

### Phân Hệ Vận Hành & Quản Trị (Staff & Admin)
* **Staff Portal:** Tiếp nhận đơn hàng, xử lý đóng gói, chuyển giao vận và trực tiếp giải đáp thắc mắc qua cổng chat trực tuyến.
* **Admin Dashboard:** Thống kê doanh thu, quản trị mẫu mã, quản trị bảng quy chuẩn size, phân quyền tài khoản và cảnh báo tồn kho tự động khi số lượng $\le 5$.

---

##  4. Hệ Thống Màu Sắc & Thiết Kế Giao Diện

Giao diện áp dụng phong cách **Bright Pastel Palette** theo tỷ lệ thị giác **60 - 30 - 10**:

* **Milk Cream (`#FAF8F5`) - 60%:** Nền tảng trang web, mang lại cảm giác ấm áp và dịu mắt.
* **Peach Blossom (`#FF9E93`) - 25%:** Màu chủ đạo cho Header, thanh điều hướng và nút hành động chính (CTA).
* **Soft Sky (`#A8DADC`) - 5%:** Nhận diện đồ Bé Trai, bong bóng chat của khách và icon chức năng.
* **Mint Macaron (`#B8E0D2`) - 5%:** Nhãn chứng nhận vải an toàn, trạng thái hoàn tất đơn hàng.
* **Buttercup (`#FDE2B8`) - 5%:** Huy hiệu thành viên VIP, badge chiết khấu đơn hàng.
* **Warm Slate (`#3D4852`):** Màu chữ chính, đảm bảo độ tương phản theo chuẩn tiếp cận WCAG 2.1.

---

##  5. Sơ Đồ Thực Thể Quan Hệ (ERD)

Hệ thống gồm **8 bảng thực thể (71 thuộc tính)** và **100% liên kết khép kín**, loại bỏ hoàn toàn tình trạng bảng độc lập:

```mermaid
erDiagram
    hgb_membership_tiers ||--o{ hgb_users : "gán cấp bậc (1:N)"
    hgb_users ||--o{ lnkt_orders : "đặt hàng (user_id)"
    hgb_users ||--o{ lnkt_orders : "thụ lý duyệt (handled_by)"
    hgb_users ||--o{ tgb_chat_messages : "gửi tin CSKH (user_id)"
    lbn_categories ||--|{ lbn_products : "chứa sản phẩm (1:N)"
    lbn_categories ||--o{ lnkt_size_charts : "quy chuẩn size (1:N)"
    lbn_products ||--|{ tgb_order_items : "chốt mua hàng (product_id)"
    lnkt_orders ||--|{ tgb_order_items : "chi tiết dòng đơn (order_id)"

    hgb_membership_tiers {
        int id PK
        string tier_name
        int min_points
        float discount_percent
        text description
    }

    hgb_users {
        int id PK
        int membership_tier_id FK
        string username UK
        string password
        string full_name
        string email UK
        string phone
        string address
        int reward_points
        enum role
        datetime created_at
    }

    lbn_categories {
        int id PK
        string name
        enum gender
        enum type
    }

    lbn_products {
        int id PK
        int category_id FK
        string name
        decimal price
        int stock_quantity
        string image
        text description
        enum type
        datetime created_at
    }

    lnkt_size_charts {
        int id PK
        int category_id FK
        enum type
        enum gender
        string size_name
        float height_min
        float height_max
        float foot_length_min
        float foot_length_max
    }

    lnkt_orders {
        int id PK
        int user_id FK
        int handled_by FK
        string receiver_name
        string receiver_phone
        string shipping_address
        string province
        string district
        decimal subtotal
        decimal discount_amount
        decimal total_amount
        enum status
        datetime created_at
    }

    tgb_order_items {
        int id PK
        int order_id FK
        int product_id FK
        string size
        int quantity
        decimal price
    }

    tgb_chat_messages {
        int id PK
        string session_id
        int user_id FK
        enum sender
        text message
        boolean is_read
        datetime created_at
    }
    graph TD
    ROOT["<b>HỆ THỐNG THỜI TRANG TRẺ EM FASHIONKIDS</b>"]
    
    MOD1["<b>1. QL TÀI KHOẢN & VIP</b><br/><i>Hồ Gia Bảo (hgb_)</i>"]
    MOD2["<b>2. QL DANH MỤC & KHO</b><br/><i>Lê Bảo Ngọc (lbn_)</i>"]
    MOD3["<b>3. BÁN HÀNG & SIZE</b><br/><i>L.N. Khánh Trình (lnkt_)</i>"]
    MOD4["<b>4. ĐƠN HÀNG & CSKH</b><br/><i>Trần Gia Bảo (tgb_)</i>"]

    ROOT --> MOD1
    ROOT --> MOD2
    ROOT --> MOD3
    ROOT --> MOD4

    MOD1 --> F11["1.1. Đăng ký & Đăng nhập (Auth)"]
    MOD1 --> F12["1.2. Quản lý hồ sơ & Địa chỉ nhận hàng"]
    MOD1 --> F13["1.3. Tự động tích lũy điểm mua hàng"]
    MOD1 --> F14["1.4. Phân hạng & Thăng hạng thẻ VIP"]
    MOD1 --> F15["1.5. Phân quyền Admin / Staff / User"]

    MOD2 --> F21["2.1. Phân nhóm danh mục (Trai/Gái/Unisex)"]
    MOD2 --> F22["2.2. Quản lý sản phẩm thời trang & Giá bán"]
    MOD2 --> F23["2.3. Quản lý số lượng tồn kho (Stock)"]
    MOD2 --> F24["2.4. Công bố thông tin an toàn da bé"]
    MOD2 --> F25["2.5. Thống kê sản phẩm theo danh mục"]

    MOD3 --> F31["3.1. Quản lý quy chuẩn size theo danh mục"]
    MOD3 --> F32["3.2. Thuật toán gợi ý size tự động (AJAX)"]
    MOD3 --> F33["3.3. Giỏ hàng & Tự động giảm giá VIP"]
    MOD3 --> F34["3.4. Đặt hàng & Tích hợp Tỉnh/Huyện API"]
    MOD3 --> F35["3.5. Xử lý & Cập nhật trạng thái đơn"]

    MOD4 --> F41["4.1. Chi tiết dòng món đặt (Order Items)"]
    MOD4 --> F42["4.2. Tự động trừ tồn kho khi chốt đơn"]
    MOD4 --> F43["4.3. Chat trực tuyến tư vấn CSKH"]
    MOD4 --> F44["4.4. Định danh phiên chat khách (Session)"]
    MOD4 --> F45["4.5. Đánh dấu đã đọc & Trả lời tin nhắn"]
    fashionkids/
├── assets/
│   ├── css/              # Bảng mã CSS thuần & CSS tùy biến màu Pastel
│   ├── js/               # Xử lý AJAX gợi ý size, provinces API, chatbox
│   └── images/           # Ảnh icon, banner thương hiệu
├── config/
│   └── database.php      # Kết nối CSDL MySQL (PDO/MySQLi UTF-8)
├── controllers/          # Bộ điều khiển tiếp nhận yêu cầu (MVC)
│   ├── AuthController.php
│   ├── ProductController.php
│   ├── OrderController.php
│   └── ChatController.php
├── models/               # Xử lý dữ liệu & gọi Stored Procedures / Functions
│   ├── UserModel.php
│   ├── ProductModel.php
│   ├── OrderModel.php
│   └── ChatModel.php
├── views/                # Giao diện người dùng
│   ├── client/           # 9 Màn hình phía Khách hàng
│   ├── staff/            # 2 Màn hình phía Nhân viên
│   └── admin/            # 4 Màn hình Quản trị viên
├── uploads/              # Thư mục lưu trữ hình ảnh sản phẩm tải lên
├── database/
│   └── fashionkids_db.sql# Kịch bản nạp toàn bộ Bảng và 12 Routines
└── index.php             # Điểm tiếp nhận trung tâm điều hướng (Router)
⚙️ 8. Hướng Dẫn Cài Đặt & Chạy Cục Bộ (Local Deployment)Yêu Cầu Môi TrườngMáy chủ Web: XAMPP (khuyến nghị phiên bản PHP $\ge 8.0$)Hệ quản trị CSDL: MariaDB / MySQL 5.7+Trình duyệt: Chrome, Microsoft Edge, FirefoxCác Bước Thực HiệnSao chép mã nguồn về máy:Bashcd C:/xampp/htdocs/
git clone [https://github.com/](https://github.com/)<tai-khoan-cua-ban>/fashionkids.git
Cấu hình Cơ sở dữ liệu:Khởi động dịch vụ Apache và MySQL trên phần mềm XAMPP Control Panel.Truy cập phpMyAdmin qua đường dẫn: http://localhost/phpmyadmin/.Tạo cơ sở dữ liệu mới có tên: db_fashionkids10 (chọn bảng mã utf8mb4_unicode_ci).Chọn tab SQL, mở tệp database/fashionkids_db.sql, sao chép toàn bộ nội dung dán vào và bấm Go (Thực hiện).Cấu hình kết nối ứng dụng:Mở tệp config/database.php và kiểm tra thông số kết nối:PHPdefine('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_fashionkids10');
Khởi chạy ứng dụng:Mở trình duyệt web và truy cập địa chỉ:Plaintexthttp://localhost/fashionkids/
🧪 9. Kiểm Thử Nghiệp Vụ Stored RoutinesKiểm thử nhanh 4 nghiệp vụ cốt lõi trực tiếp trên tab SQL của phpMyAdmin:SQL-- 1. [hgb_] Thêm khách hàng mới và tự động gán Hạng Đồng (0 điểm)
CALL hgb_insert_user('baobao', 'hashpass123', 'Hồ Gia Bảo', 'bao.ho@ueh.edu.vn', '0901234567', 'TP.HCM', 'customer');

-- 2. [hgb_] Cộng 1.600 điểm tích lũy -> Hệ thống tự động nâng hạng lên Vàng (Gold)
CALL hgb_update_user_points(1, 1600);

-- 3. [lnkt_] Tra cứu thuật toán gợi ý size quần áo cho bé gái cao 95cm
SELECT lnkt_suggest_size('clothing', 'girl', 95.0) AS size_de_xuat;

-- 4. [tgb_] Đánh dấu toàn bộ tin nhắn trong phiên tư vấn là đã đọc
CALL tgb_mark_chat_as_read('sess_client_999');
📜 10. Bản Quyền & Giấy PhépDự án được xây dựng phục vụ mục đích học tập và báo cáo học phần Công nghệ Web tại Trường Đại học Kinh tế TP. Hồ Chí Minh (UEH). Toàn bộ mã nguồn mở được phát hành theo giấy phép MIT License.
