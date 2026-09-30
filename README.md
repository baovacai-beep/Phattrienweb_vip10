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

## 📖 1. Giới Thiệu Dự Án

**FashionKids** là nền tảng thương mại điện tử chuyên cung cấp các sản phẩm thời trang an toàn, kháng khuẩn dành riêng cho trẻ em (từ sơ sinh đến 6 tuổi). Hệ thống được xây dựng theo mô hình MVC hiện đại kết hợp với cơ chế quản lý dữ liệu toàn vẹn trên hệ quản trị MySQL thông qua 12 Stored Procedures & Functions nghiệp vụ.

* **Đơn vị:** Khoa Công nghệ Thông tin Kinh doanh - Đại học Kinh tế TP. Hồ Chí Minh (UEH)
* **Học phần:** Phát triển Ứng dụng Web / Lập trình Web
* **Nhóm thực hiện:** Nhóm 10

---

## 👥 2. Thành Viên Nhóm & Phân Công Nhiệm Vụ

Dự án áp dụng quy chuẩn tiền tố cá nhân (`<prefix>_<entity/routine>`) để quản lý mã nguồn và cơ sở dữ liệu:

| STT | Họ và Tên | Vai Trò | Tiền Tố | Thực Thể Phụ Trách | Stored Routines Đảm Nhận |
| :---: | :--- | :---: | :---: | :--- | :--- |
| **1** | **Hồ Gia Bảo** | Nhóm Trưởng | `hgb_` | `hgb_membership_tiers`<br>`hgb_users` | 1. `hgb_insert_user`<br>2. `hgb_update_user_points`<br>3. `hgb_get_discount_percent` |
| **2** | **Lê Bảo Ngọc** | Thành Viên | `lbn_` | `lbn_categories`<br>`lbn_products` | 4. `lbn_insert_product`<br>5. `lbn_update_stock`<br>6. `lbn_count_products_by_category` |
| **3** | **Lê Nguyễn Khánh Trình** | Thành Viên | `lnkt_` | `lnkt_size_charts`<br>`lnkt_orders` | 7. `lnkt_create_order`<br>8. `lnkt_update_order_status`<br>9. `lnkt_suggest_size` |
| **4** | **Trần Gia Bảo** | Thành Viên | `tgb_` | `tgb_order_items`<br>`tgb_chat_messages` | 10. `tgb_add_order_item`<br>11. `tgb_send_chat_message`<br>12. `tgb_mark_chat_as_read` |

---

## ✨ 3. Tính Năng Nổi Bật

### Phân Hệ Khách Hàng (Customer & Guest)
* **Thuật toán gợi ý size tự động (AJAX Size Recommendation):** Nhập chiều cao hoặc độ dài bàn chân của bé, hệ thống tự động trả về kích cỡ chuẩn dựa trên danh mục sản phẩm tương ứng.
* **Hệ thống cấp bậc thành viên VIP:** Tự động tích lũy điểm (10.000 VNĐ = 1 điểm) và tự động thăng hạng (Đồng $\rightarrow$ Bạc $\rightarrow$ Vàng $\rightarrow$ Kim Cương) để nhận mức giảm giá lên đến 15%.
* **Tích hợp Tỉnh / Thành phố:** Tích hợp Provinces Open API chuẩn hóa dữ liệu địa chỉ nhận hàng.
* **Live Chat CSKH thời gian thực:** Hỗ trợ tư vấn phom dáng/chất liệu cho cả khách đăng nhập và khách vãng lai (Guest Tracking qua `session_id`).

### Phân Hệ Vận Hành & Quản Trị (Staff & Admin)
* **Staff Portal:** Tiếp nhận đơn hàng, xử lý đóng gói, chuyển giao vận và trực tiếp giải đáp thắc mắc qua cổng chat trực tuyến.
* **Admin Dashboard:** Thống kê doanh thu, quản trị mẫu mã, quản trị bảng quy chuẩn size, phân quyền tài khoản và cảnh báo tồn kho tự động khi số lượng $\le 5$.

---

## 🎨 4. Hệ Thống Màu Sắc & Thiết Kế Giao Diện

Giao diện áp dụng phong cách **Bright Pastel Palette** theo tỷ lệ thị giác **60 - 30 - 10**:

* **Milk Cream (`#FAF8F5`) - 60%:** Nền tảng trang web, mang lại cảm giác ấm áp và dịu mắt.
* **Peach Blossom (`#FF9E93`) - 25%:** Màu chủ đạo cho Header, thanh điều hướng và nút hành động chính (CTA).
* **Soft Sky (`#A8DADC`) - 5%:** Nhận diện đồ Bé Trai, bong bóng chat của khách và icon chức năng.
* **Mint Macaron (`#B8E0D2`) - 5%:** Nhãn chứng nhận vải an toàn, trạng thái hoàn tất đơn hàng.
* **Buttercup (`#FDE2B8`) - 5%:** Huy hiệu thành viên VIP, badge chiết khấu đơn hàng.
* **Warm Slate (`#3D4852`):** Màu chữ chính, đảm bảo độ tương phản theo chuẩn tiếp cận WCAG 2.1.

---

## 🗄️ 5. Sơ Đồ Thực Thể Quan Hệ (ERD)

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



