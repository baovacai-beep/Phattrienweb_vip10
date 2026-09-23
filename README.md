# 👶 Fashion Kids - Ứng Dụng Thời Trang Cho Trẻ Em

> Dự án website thương mại điện tử chuyên cung cấp trang phục và phụ kiện thời trang dành cho trẻ em, được xây dựng theo kiến trúc module hóa kết hợp hệ thống lưới Responsive chuẩn Bootstrap.

---

## Giới Thiệu Dự Án

**Fashion Kids** là giải pháp mua sắm trực tuyến thân thiện, an toàn và trực quan, giúp các bậc phụ huynh dễ dàng tìm kiếm, lựa chọn và phối đồ cho bé theo từng độ tuổi, giới tính và phong cách.

Dự án được phát triển trong khuôn khổ môn học **Phát triển ứng dụng Web** tại **Trường Đại học Kinh tế TP. Hồ Chí Minh (UEH)**.

---

## Danh Sách Thành Viên & Phân Công Nhiệm Vụ

| STT | Họ và Tên | Vai Trò | Nhiệm Vụ Chính |
| :---: | :--- | :--- | :--- |
| 1 | **Hồ Gia Bảo** | **Team Leader & Backend Core** | Quản lý dự án, kiến trúc hệ thống, xử lý Session/Cookie và điều hướng luồng dữ liệu. |
| 2 | **Lê Nguyễn Khánh Trình** | **Backend & Database** | Xây dựng cấu trúc mảng dữ liệu/CSDL, xử lý Form nhập liệu, Upload file và lọc sản phẩm. |
| 3 | **Lê Bảo Ngọc** | **Frontend & UI/UX** | Dựng khung giao diện chuẩn Bootstrap 4, phụ trách thẩm mỹ, màu sắc và độ tương thích thiết bị. |
| 4 | **Trần Gia Bảo** | **Client Logic & Testing** | Xử lý sự kiện tương tác DOM JavaScript, kiểm thử giao diện và tối ưu hóa trải nghiệm người dùng. |

---

## Tính Năng Chính

* **Phân loại danh mục đa cấp:** Lọc sản phẩm linh hoạt theo độ tuổi (Sơ sinh, 1–5 tuổi, 6–12 tuổi), giới tính (Bé trai, Bé gái) và thương hiệu.
* **Trình diễn sản phẩm trực quan:** Giao diện thẻ Card kèm hiệu ứng hover tương tác, hiển thị giá niêm yết, thông tin chất liệu mềm mại an toàn cho trẻ.
* **Menu điều hướng động:** Tự động bắt tham số truy vấn trên URL (`?gr=...`, `?page=...`) để tải nội dung tương ứng mà không làm gián đoạn trải nghiệm người dùng.
* **Quản lý phiên mua sắm:** Sử dụng cơ chế Session và Cookie để lưu trữ trạng thái giỏ hàng và danh sách sản phẩm yêu thích.
* **Giao diện Responsive toàn diện:** Tự động co giãn tối ưu trên mọi độ phân giải (Desktop, Tablet, Mobile) qua hệ thống Grid và Flexbox.

---

## 🛠️ Công Nghệ Sử Dụng

**Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 4 (CDN).
**Backend:** PHP (Native / Module-based).
**Quản lý mã nguồn:** Git & GitHub.
**Môi trường chạy thử:** XAMPP (Apache Web Server).

---

## Quy Chuẩn Kiến Trúc Giao Diện

Dự án tuân thủ nghiêm ngặt mô hình phân cấp 3 tầng của Bootstrap Grid để đảm bảo tính đồng bộ và không bị lỗi vỡ khung:

$$\text{container} \longrightarrow \text{row} \longrightarrow \text{col-*}$$

**Tầng 1 - Container:** Sử dụng `.container` hoặc `.container-fluid` để căn lề và định vị độ rộng.
**Tầng 2 - Row:** Đặt bên trong container, chỉ chứa duy nhất các phần tử cột (`col-*`) làm thẻ con trực tiếp.
**Tầng 3 - Column:** Phân bổ tỷ lệ cột (`col-12`, `col-sm-6`, `col-md-4`...) cho từng điểm ngắt màn hình (breakpoint). Mọi nội dung, bảng biểu và thẻ card đều nằm trong khối này.

---

## 📂 Cấu Trúc Thư Mục

```text
fashion-kids/
├── css/
│   └── style.css            # Tệp định dạng giao diện tùy chỉnh
├── js/
│   └── main.js              # Tệp xử lý sự kiện tương tác JavaScript
├── modules/
│   ├── session_module.php   # Đóng gói thao tác quản lý Session & Cookie
│   └── upload_module.php    # Xử lý kiểm tra và tải ảnh sản phẩm
├── data/
│   └── data.php             # Mảng dữ liệu nguồn (danh mục & sản phẩm)
├── img/                     # Thư mục chứa hình ảnh sản phẩm & banner
├── lmenu.php                # Khối điều hướng danh mục thời trang bên trái
├── content.php              # Khối hiển thị chi tiết và danh sách sản phẩm
├── index.php                # Trang chủ điều hướng trung tâm
└── README.md                # Tài liệu hướng dẫn dự án
