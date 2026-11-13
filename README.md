<div align="center">

# FASHIONKIDS - NỀN TẢNG THƯƠNG MẠI ĐIỆN TỬ D2C THỜI TRANG TRẺ EM AN TOÀN

**Hệ thống bán lẻ trực tiếp từ thương hiệu đến người tiêu dùng (Direct-to-Consumer) chuyên biệt về trang phục sợi tre hữu cơ kháng khuẩn dành cho trẻ em từ sơ sinh đến 6 tuổi.**

<p>
  <img src="https://img.shields.io/badge/PHP-8.x_OOP_MVC-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.x" />
  <img src="https://img.shields.io/badge/MySQL-8.0_InnoDB_3NF-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0" />
  <img src="https://img.shields.io/badge/Bootstrap-4.6.2-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 4.6.2" />
  <img src="https://img.shields.io/badge/JavaScript-ES6_%26_jQuery_3.6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript ES6" />
  <img src="https://img.shields.io/badge/Status-Production_Ready-1F883D?style=for-the-badge" alt="Status" />
  <img src="https://img.shields.io/badge/License-MIT-0969DA?style=for-the-badge" alt="License MIT" />
</p>

<p>
  <a href="https://fashionkids-nhom10.free.nf"><strong>Trang Web Trực Tuyến (Live Demo)</strong></a>
    |  
  <a href="https://github.com/baovacai-beep/Phattrienweb_vip10"><strong>Kho Mã Nguồn GitHub</strong></a>
    |  
  <a href="#5-thiết-kế-cơ-sở-dữ-liệu-3nf"><strong>Lược Đồ Cơ Sở Dữ Liệu</strong></a>
    |  
  <a href="#7-hướng-dẫn-cài-đặt-và-vận-hành"><strong>Hướng Dẫn Cài Đặt</strong></a>
</p>

</div>

---

## Mục Lục

- [1. Giới Thiệu Dự Án](#1-giới-thiệu-dự-án)
- [2. Thông Tin Học Phần và Nhóm Thực Hiện](#2-thông-tin-học-phần-và-nhóm-thực-hiện)
- [3. Tính Năng Nổi Bật Theo Phân Quyền](#3-tính-năng-nổi-bật-theo-phân-quyền)
- [4. Kiến Trúc Hệ Thống và Công Nghệ Sử Dụng](#4-kiến-trúc-hệ-thống-và-công-nghệ-sử-dụng)
- [5. Thiết Kế Cơ Sở Dữ Liệu (3NF)](#5-thiết-kế-cơ-sở-dữ-liệu-3nf)
- [6. Cấu Trúc Thư Mục Dự Án](#6-cấu-trúc-thư-mục-dự-án)
- [7. Hướng Dẫn Cài Đặt và Vận Hành](#7-hướng-dẫn-cài-đặt-và-vận-hành)
- [8. Tài Khoản Kiểm Thử Phân Quyền (RBAC)](#8-tài-khoản-kiểm-thử-phân-quyền-rbac)
- [9. Quy Trình Cộng Tác Nhóm (Git Workflow)](#9-quy-trình-cộng-tác-nhóm-git-workflow)
- [10. Bản Quyền và Tài Liệu Tham Khảo](#10-bản-quyền-và-tài-liệu-tham-khảo)

---

## 1. Giới Thiệu Dự Án

Khi mua sắm quần áo trẻ em trực tuyến, các bậc phụ huynh thường đối mặt với hai rào cản chính là sự thiếu minh bạch về độ an toàn của chất liệu vải đối với làn da nhạy cảm của trẻ nhỏ và tình trạng chọn nhầm kích cỡ do vóc dáng của bé thay đổi nhanh theo từng tháng tuổi.

**FashionKids** được xây dựng theo mô hình bán lẻ trực tiếp từ thương hiệu đến người tiêu dùng (**Direct-to-Consumer - D2C**), tập trung vào các dòng trang phục sợi tre hữu cơ (**Bamboo**) và cotton kháng khuẩn. Hệ thống giải quyết trọn vẹn chuỗi nghiệp vụ từ tư vấn kích cỡ thông minh theo số đo thực tế của bé, tự động tính chiết khấu hạng thẻ thành viên VIP, thanh toán đối soát tự động cho đến quản lý tồn kho và xử lý hoàn trả hàng theo thời gian thực.

- **Địa chỉ triển khai trực tuyến (Production Hosting):** `https://fashionkids-nhom10.free.nf`
- **Kho lưu trữ mã nguồn (GitHub Repository):** `https://github.com/baovacai-beep/Phattrienweb_vip10`

---

## 2. Thông Tin Học Phần và Nhóm Thực Hiện

- **Cơ sở đào tạo:** Trường Công nghệ và Thiết kế – Đại học Kinh tế TP. Hồ Chí Minh (UEH)
- **Ngành / Chuyên ngành:** Kỹ thuật Phần mềm / Công nghệ Phần mềm (Khóa K50)
- **Học phần:** Phát Triển Ứng Dụng Web
- **Giảng viên hướng dẫn:** TS. Đặng Ngọc Hoàng Thành
- **Nhóm thực hiện:** Nhóm 10

Toàn bộ mã nguồn, thực thể cơ sở dữ liệu, thủ tục lưu trữ và nhánh phát triển Git được phân công rành mạch cho 04 thành viên với tỷ lệ đóng góp **25% mỗi thành viên** theo quy chuẩn tiền tố cá nhân:

| STT | Họ và Tên | MSSV | Vai Trò | Tiền Tố | Nhánh Git Phụ Trách | Phạm Vi Nghiệp Vụ & Kỹ Thuật Đảm Nhận | Đóng Góp |
| :---: | :--- | :---: | :---: | :---: | :--- | :--- | :---: |
| **1** | **Hồ Gia Bảo** | `31241023360` | Nhóm trưởng | `hgb_` | `feature/hgb-auth-layout` | Kiến trúc lõi MVC, kết nối PDO Singleton đa môi trường, Middleware RBAC 4 vai trò, xác thực OTP Gmail SMTP, Google OAuth 2.0, hệ thống hạng thẻ VIP và khung giao diện Bright Pastel. | `25%` |
| **2** | **Lê Bảo Ngọc** | `31241025411` | Thành viên | `lbn_` | `feature/lbn-catalog-stock` | Quản lý danh mục và sản phẩm, tính đóng gói OOP (`Product`), bộ lọc sản phẩm AJAX đa tiêu chí, kiểm tra tồn kho thời gian thực và quản trị nhập kho cảnh báo hàng sắp hết. | `25%` |
| **3** | **Lê Nguyễn Khánh Trình** | `31241026087` | Thành viên | `lnkt_` | `feature/lnkt-order-size` | Công cụ thước đo gợi ý kích cỡ AJAX, tính đa hình (`IGoiYKichCo`), điều phối trạng thái đơn hàng, tích điểm thăng hạng VIP, quy trình duyệt hoàn hàng 4 bước và cổng tuyển chọn KOL Nhí. | `25%` |
| **4** | **Trần Gia Bảo** | `31241026999` | Thành viên | `tgb_` | `feature/tgb-checkout-ajax` | Giỏ hàng và thanh toán một trang, tiêu thụ Webservice Vietnam Provinces Open API 3 cấp, trừu tượng hóa thanh toán (`COD` & `VietQR Casso`), đánh giá đa phương tiện, tạp chí và hỗ trợ trực tuyến. | `25%` |

---

## 3. Tính Năng Nổi Bật Theo Phân Quyền

Hệ thống triển khai cơ chế kiểm soát truy cập dựa trên vai trò (**Role-Based Access Control - RBAC**) phân tách quyền hạn cho 04 nhóm người dùng:

### 3.1. Khách vãng lai (Guest)
- Duyệt danh mục thời trang bé trai, bé gái, đồ sơ sinh và giày tập đi trên giao diện đáp ứng đa thiết bị (Desktop `1920x1080` và Mobile `375px`).
- Tìm kiếm và lọc sản phẩm theo từ khóa, danh mục, giới tính, thanh trượt khoảng giá và chất liệu vải sợi tre hữu cơ bằng AJAX mà không tải lại trang.
- Sử dụng công cụ thước đo kích cỡ thông minh để nhận gợi ý size chuẩn theo chiều cao hoặc chiều dài bàn chân của bé.
- Thêm sản phẩm vào giỏ hàng tạm theo phiên trình duyệt (`PHPSESSID`), đọc bài viết tạp chí phối đồ và gửi tin nhắn tư vấn trực tuyến.

### 3.2. Khách hàng thành viên (Customer)
- Đăng ký tài khoản bảo mật hai tầng: kiểm tra trùng lặp tên đăng nhập/email ngay khi rời ô nhập liệu (`blur`) và xác thực mã OTP 6 chữ số gửi qua Gmail SMTP (`PHPMailer`).
- Hỗ trợ đăng nhập nhanh một chạm thông qua **Google OAuth 2.0**.
- Tự động áp dụng tỷ lệ chiết khấu theo cấp bậc thẻ thành viên VIP (**Đồng 0%**, **Bạc 5%**, **Vàng 10%**, **Kim Cương 15%**) kết hợp mã giảm giá Voucher.
- Chọn địa chỉ nhận hàng 3 cấp (Tỉnh/Thành phố, Quận/Huyện, Phường/Xã) nạp tự động từ **Vietnam Provinces Open API**.
- Thanh toán linh hoạt bằng tiền mặt (**COD**) hoặc chuyển khoản ngân hàng tự động qua mã **VietQR MB Bank** đối soát giao dịch qua **Casso API v2**.
- Theo dõi tiến trình giao hàng 5 bước theo thời gian thực; gửi đánh giá 1 đến 5 sao đính kèm hình ảnh/video thực tế (chỉ mở khóa đối với đơn đã giao thành công); gửi yêu cầu hoàn trả hàng 4 bước và đăng ký hồ sơ cho bé ứng tuyển Đại sứ nhí (**KOL Casting**).

### 3.3. Nhân viên vận hành (Staff)
- Tiếp nhận và xử lý đơn hàng mới theo thời gian thực, đối soát giao dịch chuyển khoản VietQR và in tem nhãn vận đơn.
- Vận hành hộp thoại Live Chat hỗ trợ khách hàng 2 chiều kết hợp tra cứu nhanh tồn kho sản phẩm.
- Sơ tuyển hồ sơ ứng tuyển KOL Nhí vòng phỏng vấn, nhập bổ sung số lượng tồn kho và xử lý yêu cầu hoàn trả hàng của khách.

### 3.4. Quản trị viên (Admin)
- Theo dõi bảng điều khiển thống kê doanh thu, cơ cấu đơn hàng và danh sách cảnh báo các mẫu sản phẩm sắp hết hàng (`so_luong_ton_kho <= 5`).
- Thêm mới, chỉnh sửa danh mục hàng hóa, sản phẩm và cấu hình bảng quy chuẩn kích cỡ theo từng nhóm tuổi.
- Quản lý tài khoản người dùng, phân quyền vai trò, thiết lập mốc điểm thăng hạng VIP, điều hành Banner Carousel trang chủ và xuất bản bài viết tạp chí.

---

## 4. Kiến Trúc Hệ Thống và Công Nghệ Sử Dụng

### 4.1. Ngăn xếp công nghệ (Technology Stack)

| Tầng Kiến Trúc | Công Nghệ / Thư Viện | Phiên Bản / Tiêu Chuẩn | Vai Trò Trong Hệ Thống |
| :--- | :--- | :--- | :--- |
| **Giao diện (Frontend)** | HTML5, CSS3, Bootstrap | HTML5 / CSS3 / BS 4.6.2 | Cấu trúc ngữ nghĩa, hệ màu Bright Pastel `60-30-10` tách biệt tại `style-pastel.css` và lưới 12 cột Responsive. |
| **Tương tác (Client)** | JavaScript & jQuery | ES6 / jQuery 3.6.0 | Xử lý sự kiện DOM, giao tiếp bất đồng bộ AJAX định dạng JSON và kiểm tra biểu mẫu phía trình duyệt. |
| **Xử lý (Backend)** | PHP & PDO | PHP 8.x (OOP + MVC) | Front Controller tập trung, 4 trụ cột OOP, Singleton PDO chống SQL Injection, Middleware RBAC và CSRF. |
| **Cơ sở dữ liệu** | MySQL / MariaDB | MySQL 8.x (`InnoDB`) | Lưu trữ 17 bảng chuẩn hóa bậc 3 (`utf8mb4_unicode_ci`), 22 khóa ngoại và 15 Stored Procedures / Functions. |
| **Dịch vụ tích hợp** | Provinces API, VietQR, Casso v2, PHPMailer, Google OAuth 2.0, Gemini API | RESTful JSON / SMTP TLS | Nạp địa giới hành chính 3 cấp, đối soát chuyển khoản tự động, gửi thư OTP, đăng nhập Google và trợ lý AI. |

### 4.2. Sơ đồ luồng xử lý kiến trúc OOP + MVC

```text
+-----------------------+       HTTP / AJAX (JSON)       +-----------------------------------+
|   Trình Duyệt Khách   | -----------------------------> |    index.php (Front Controller)   |
|  (Bootstrap 4 & ES6)  | <----------------------------- |   Khởi tạo Session & Định tuyến   |
+-----------------------+       HTML View / JSON         +-----------------------------------+
                                                                           |
                                                                           v
                                                         +-----------------------------------+
                                                         |     core/Rbac.php & Csrf.php      |
                                                         |  Kiểm soát phân quyền 4 vai trò   |
                                                         +-----------------------------------+
                                                                           |
                                                                           v
                                                         +-----------------------------------+
                                                         |     Controllers (controllers/)    |
                                                         |  Home, Product, Order, Auth,      |
                                                         |  Staff, Admin Controllers         |
                                                         +-----------------------------------+
                                                           /                               \
                                                          v                                 v
                                 +---------------------------------+               +-------------------+
                                 |     Models & OOP Entities       |               |   Views (views/)  |
                                 |  Đóng gói, Kế thừa, Đa hình,    |               |  Layouts, Pages,  |
                                 |  Trừu tượng hóa + Singleton PDO |               |  Modals (BS 4)    |
                                 +---------------------------------+               +-------------------+
                                           |               |
                        Prepared Statements|               | RESTful JSON / SMTP
                                           v               v
                       +-----------------------+       +-----------------------------------+
                       |   MySQL 8.x (InnoDB)  |       |     Dịch Vụ Webservice Ngoài      |
                       |   17 Bảng 3NF & 15    |       |  Provinces API, Casso VietQR,     |
                       |   Stored Routines     |       |  Gmail SMTP, OAuth 2.0, Gemini    |
                       +-----------------------+       +-----------------------------------+
```
### 4.3. Hiện thực hóa 04 trụ cột Lập trình Hướng đối tượng (OOP)

1. **Tính Đóng gói (Encapsulation):** Lớp trừu tượng `Product` (`models/entities/Product.php`) bảo vệ các thuộc tính ở mức `private` và kiểm soát toàn vẹn dữ liệu qua các phương thức `setter` (ném ngoại lệ `InvalidArgumentException` nếu đơn giá `<= 0` hoặc tồn kho `< 0`).
2. **Tính Kế thừa (Inheritance):** Cây phân cấp từ lớp cơ sở `User` (`models/entities/User.php`) xuống các lớp con `Customer`, `Staff` và `Admin`, ghi đè phương thức kiểm tra thẩm quyền `coQuyenQuanTri(): bool`.
3. **Tính Đa hình (Polymorphism):** Giao diện `IGoiYKichCo` (`models/interfaces/IGoiYKichCo.php`) định nghĩa phương thức `duDoanSize(float $soDo): string`, được triển khai theo hai thuật toán độc lập ở `SanPhamQuanAo` (tính theo chiều cao) và `SanPhamGiayDep` (tính theo chiều dài bàn chân).
4. **Tính Trừu tượng hóa (Abstraction):** Giao diện `IPhuongThucThanhToan` (`models/interfaces/IPhuongThucThanhToan.php`) tách biệt quy trình thanh toán khỏi bộ điều khiển đơn hàng thông qua hai lớp thực thi `ThanhToanCOD` và `ThanhToanVietQR`.

---

## 5. Thiết Kế Cơ Sở Dữ Liệu (3NF)

Cơ sở dữ liệu **`db_fashionkids`** được chuẩn hóa đạt **dạng chuẩn bậc 3 (3NF)** nhằm loại bỏ dư thừa dữ liệu và ngăn chặn các bất thường khi thêm, sửa, xóa bản ghi.

### 5.1. Danh mục 17 bảng thực thể

| Nhóm Phân Hệ | Tên Bảng Thực Thể | Khóa Chính (PK) | Chức Năng Lưu Trữ |
| :--- | :--- | :--- | :--- |
| **Tài khoản & VIP (`hgb_`)** | `hgb_hang_thanh_vien` | `ma_hang` | Cấp bậc thẻ VIP (Đồng, Bạc, Vàng, Kim Cương), điểm sàn và tỷ lệ chiết khấu. |
| | `hgb_nguoi_dung` | `ma_nguoi_dung` | Thông tin tài khoản, phân quyền RBAC, điểm tích lũy và liên kết Google OAuth. |
| **Danh mục & Kho (`lbn_`)** | `lbn_danh_muc` | `ma_danh_muc` | Nhóm sản phẩm theo giới tính (`be_trai`, `be_gai`, `chung`) và loại hàng. |
| | `lbn_san_pham` | `ma_san_pham` | Thông tin mẫu sản phẩm, giá niêm yết, số lượng tồn kho và mô tả chất liệu. |
| **Đơn hàng & Sự kiện (`lnkt_`)** | `lnkt_bang_kich_co` | `ma_kich_co` | Bảng quy chuẩn kích cỡ (chiều cao, vòng ngực, vòng eo, dài bàn chân). |
| | `lnkt_don_hang` | `ma_don_hang` | Hóa đơn mua hàng, địa chỉ 3 cấp, chiết khấu VIP/Voucher, thanh toán và vận đơn. |
| | `lnkt_yeu_cau_hoan_hang` | `ma_hoan_hang` | Hồ sơ yêu cầu trả hàng, ảnh minh chứng, tài khoản hoàn tiền và tiến trình duyệt. |
| | `lnkt_ma_giam_gia` | `ma_voucher` | Quản lý mã khuyến mãi Voucher (giảm phần trăm, giảm tiền cố định, freeship). |
| | `lnkt_banner_su_kien` | `ma_banner` | Quản lý slide Banner chiến dịch xoay vòng trên trang chủ. |
| | `lnkt_dang_ky_kol_nhi` | `ma_ho_so` | Hồ sơ phụ huynh đăng ký cho bé ứng tuyển người mẫu ảnh nhí (KOL Casting). |
| **Chi tiết & Cộng đồng (`tgb_`)** | `tgb_chi_tiet_don_hang` | `ma_chi_tiet_don` | Chi tiết từng dòng sản phẩm, kích cỡ, số lượng và đơn giá chốt lúc đặt hàng. |
| | `tgb_tin_nhan_tu_van` | `ma_tin_nhan` | Lịch sử hội thoại CSKH trực tuyến theo phiên khách vãng lai hoặc tài khoản. |
| | `tgb_bai_viet_tin_tuc` | `ma_bai_viet` | Bài viết tạp chí phối đồ, cẩm nang chăm sóc bé và điểm tin sự kiện. |
| | `tgb_binh_luan_bai_viet` | `ma_binh_luan` | Bình luận và phản hồi phân cấp lồng nhau dưới các bài viết tạp chí. |
| | `tgb_danh_gia_san_pham` | `ma_danh_gia` | Đánh giá chất lượng 1 đến 5 sao xác thực từ khách hàng đã hoàn tất đơn mua. |
| | `tgb_tep_tin_danh_gia` | `ma_tep_tin` | Đường dẫn hình ảnh và video thực tế đính kèm bài đánh giá sản phẩm. |
| | `tgb_luot_thich_danh_gia` | `(ma_nguoi_dung, ma_danh_gia)` | Ghi nhận lượt bình chọn đánh giá hữu ích của từng tài khoản. |

### 5.2. Danh mục 15 Stored Procedures và Stored Functions

Hệ thống đóng gói các quy tắc nghiệp vụ trọng yếu ngay tại tầng cơ sở dữ liệu kết hợp bẫy lỗi `SIGNAL SQLSTATE '45000'` và giao dịch `ACID`:

| STT | Tên Routine | Loại | Chức Năng Nghiệp Vụ & Mã Lỗi Xử Lý |
| :---: | :--- | :---: | :--- |
| 1 | `sp_hgb_dang_ky_thanh_vien` | Procedure | Đăng ký tài khoản mới, bắt trùng username/email (`[HGB_ERR_01..04]`) và gán Hạng Đồng. |
| 2 | `sp_hgb_tich_diem_nang_hang` | Procedure | Cộng điểm thưởng tích lũy và tự động nâng cấp hạng thẻ VIP tương ứng (`[HGB_ERR_05..06]`). |
| 3 | `fn_hgb_tinh_phan_tram_giam_gia` | Function | Trả về tỷ lệ chiết khấu VIP hiện tại của khách hàng (`0%`, `5%`, `10%`, `15%`). |
| 4 | `sp_lbn_them_san_pham_moi` | Procedure | Thêm mẫu sản phẩm mới, kiểm tra giá bán `> 0` và tồn kho `>= 0` (`[LBN_ERR_01..04]`). |
| 5 | `sp_lbn_nhap_them_kho_hang` | Procedure | Bổ sung số lượng tồn kho an toàn cho sản phẩm hiện có (`[LBN_ERR_05..06]`). |
| 6 | `fn_lbn_kiem_tra_ton_kho` | Function | Kiểm tra số lượng tồn kho khả dụng so với số lượng khách cần mua (`[LBN_ERR_07..08]`). |
| 7 | `fn_lnkt_goi_y_kich_co_chuan` | Function | Đối chiếu chiều cao/vòng ngực/eo hoặc dài bàn chân để gợi ý size chuẩn (`[LNKT_ERR_01..03]`). |
| 8 | `sp_lnkt_cap_nhat_trang_thai_don_hang` | Procedure | Cập nhật tiến trình đơn, hoàn kho khi hủy đơn và tự tích điểm khi `hoan_tat` (`[LNKT_ERR_04..06]`). |
| 9 | `sp_lnkt_xet_duyet_kol_nhi` | Procedure | Kiểm tra quyền Nhân viên/Admin và cập nhật trạng thái hồ sơ KOL Nhí (`[LNKT_ERR_07..08]`). |
| 10 | `sp_lnkt_tao_yeu_cau_hoan_hang` | Procedure | Tạo phiếu yêu cầu trả hàng cho đơn đã `hoan_tat`, chống gửi trùng (`[LNKT_RET_01..04]`). |
| 11 | `sp_lnkt_xu_ly_hoan_hang` | Procedure | Duyệt hoàn hàng 4 bước; khi `hoan_tat_hoan` tự cộng lại kho và thu hồi điểm VIP (`[LNKT_RET_05..11]`). |
| 12 | `sp_tgb_tao_don_hang_va_chi_tiet` | Procedure | Mở Transaction tạo hóa đơn, tính chiết khấu VIP, lưu chi tiết và trừ tồn kho (`[TGB_ERR_01..04]`). |
| 13 | `sp_tgb_gui_danh_gia_san_pham` | Procedure | Xác minh khách đã mua đơn `hoan_tat` và lưu đánh giá 1–5 sao chống spam (`[TGB_ERR_05..09]`). |
| 14 | `fn_tgb_tinh_sao_trung_binh_san_pham` | Function | Tính điểm đánh giá sao trung bình thực tế (trả về `0.0` nếu chưa có đánh giá nào). |
| 15 | `fn_tgb_dem_so_nguoi_mua` | Function | Đếm chính xác số người mua thực tế từ các đơn `hoan_tat` (loại trừ đơn đã hoàn trả). |

---

## 6. Cấu Trúc Thư Mục Dự Án

```text
Phattrienweb_vip10/
├── Diagrams/                                    # Hệ thống sơ đồ phân tích thiết kế (Workflow, BFD, Use Case, Class, ERD)
├── MinhChung_GiaiDoan5/                         # Hồ sơ minh chứng Git Network, Shared DB LAN, Trello/Teams và Hosting
└── FashionKids/                                 # Thư mục mã nguồn chính của ứng dụng Web
    ├── index.php                                # Điểm tiếp nhận điều hướng trung tâm (Front Controller)
    ├── .env.example                             # Tệp mẫu cấu hình các khóa API tích hợp
    ├── config/
    │   ├── app.php                              # Cấu hình múi giờ, đường dẫn gốc APP_BASE_URL và tham số OTP
    │   ├── database.php                         # Cấu hình kết nối CSDL tự động nhận diện 3 môi trường
    │   ├── integrations.php                     # Cấu hình dịch vụ GHN, Google OAuth 2.0, Turnstile và Gemini AI
    │   └── payment.php                          # Cấu hình tài khoản ngân hàng VietQR MB Bank và Casso API v2
    ├── core/
    │   ├── App.php                              # Bộ định tuyến URL linh hoạt 3 chuẩn tham số
    │   ├── Controller.php                       # Lớp Base Controller chuẩn hóa phản hồi JSON Envelope
    │   ├── Database.php                         # Lớp kết nối PDO theo mẫu thiết kế Singleton
    │   ├── Rbac.php                             # Middleware kiểm soát phân quyền 4 vai trò (RBAC)
    │   ├── Csrf.php                             # Cơ chế sinh và xác thực mã chống giả mạo yêu cầu CSRF
    │   ├── Mailer.php                           # Dịch vụ gửi thư điện tử qua Gmail SMTP (PHPMailer)
    │   ├── GeminiService.php                    # Dịch vụ trợ lý tư vấn kích cỡ và chất liệu AI Gemini
    │   ├── GoogleOAuthService.php               # Dịch vụ xác thực đăng nhập Google OAuth 2.0
    │   ├── ShippingService.php                  # Dịch vụ tính cước và đối soát vận chuyển GHN
    │   └── VietQrBankService.php                # Dịch vụ sinh mã QR động và đối soát giao dịch VietQR
    ├── models/
    │   ├── entities/                            # Các lớp thực thể OOP (User, Customer, Staff, Admin, Product, DonHang...)
    │   ├── interfaces/                          # Các giao diện hợp đồng (IGoiYKichCo, IPhuongThucThanhToan)
    │   ├── UserModel.php                        # Xử lý dữ liệu tài khoản, OTP và hạng thẻ VIP
    │   ├── ProductModel.php                     # Xử lý dữ liệu danh mục, sản phẩm, bộ lọc và tồn kho
    │   ├── OrderModel.php                       # Xử lý giỏ hàng, giao dịch đặt hàng ACID, VietQR và hoàn hàng
    │   ├── SizeChartModel.php                   # Xử lý tra cứu bảng kích cỡ chuẩn
    │   ├── ReviewModel.php                      # Xử lý đánh giá sản phẩm và tệp đính kèm
    │   └── RealtimeContentModel.php             # Xử lý dữ liệu Live Chat, Banner sự kiện, Blog và KOL Nhí
    ├── controllers/
    │   ├── HomeController.php                   # Điều phối Trang chủ, Tạp chí, KOL Nhí và Live Chat CSKH
    │   ├── ProductController.php                # Điều phối Cửa hàng, Chi tiết sản phẩm và API gợi ý size AJAX
    │   ├── OrderController.php                  # Điều phối Giỏ hàng, Thanh toán, Theo dõi đơn và Hoàn hàng
    │   ├── AuthController.php                   # Điều phối Đăng ký OTP, Đăng nhập, Google OAuth và Hồ sơ VIP
    │   ├── StaffController.php                  # Điều phối Bàn làm việc Nhân viên vận hành
    │   └── AdminController.php                  # Điều phối Bảng điều khiển Quản trị viên
    ├── views/
    │   ├── layouts/                             # Khung giao diện dùng chung (header, navbar, footer, chat-widget)
    │   ├── modals/                              # Các hộp thoại tương tác (thước đo size, OTP, đánh giá, hoàn hàng)
    │   └── pages/                               # 19 trang giao diện chức năng cho Khách hàng, Nhân viên và Admin
    ├── public/
    │   ├── css/                                 # Bootstrap 4.6.2 và bảng màu Bright Pastel (style-pastel.css)
    │   ├── js/                                  # Thư viện jQuery 3.6.0 và các kịch bản xử lý AJAX/DOM
    │   └── uploads/                             # Thư mục lưu trữ hình ảnh minh chứng đánh giá, hoàn hàng và KOL
    └── database/
        ├── db_fashionkids.sql                   # Kịch bản khởi tạo 17 bảng 3NF, dữ liệu mẫu và 15 Stored Routines
        ├── db_fashionkids_hosting_infinityfree.sql # Kịch bản SQL tối ưu tương thích Shared Hosting
        ├── setup_shared_lan_user.sql            # Kịch bản cấp quyền truy cập cơ sở dữ liệu dùng chung mạng LAN
        └── test_12_routines_happy_path.sql      # Kịch bản kiểm thử tự động các thủ tục và hàm lưu trữ
```

---

## 7. Hướng Dẫn Cài Đặt và Vận Hành

### 7.1. Yêu cầu môi trường

- Máy chủ web cục bộ **XAMPP** (hỗ trợ **PHP >= 8.1** và **MySQL 8.0 / MariaDB**).
- Trình duyệt web hiện đại (Google Chrome, Microsoft Edge) và công cụ **Git**.

### 7.2. Các bước cài đặt trên Localhost (XAMPP)

1. **Sao chép mã nguồn về máy:**
   ```bash
   git clone [https://github.com/baovacai-beep/Phattrienweb_vip10.git](https://github.com/baovacai-beep/Phattrienweb_vip10.git)
   ```
   Sao chép thư mục `FashionKids` vào thư mục gốc của Apache (mặc định là `C:\xampp\htdocs\FashionKids`).

2. **Khởi động dịch vụ máy chủ:**
   Mở **XAMPP Control Panel** và nhấn **Start** cho hai dịch vụ **Apache** và **MySQL**.

3. **Khởi tạo cơ sở dữ liệu:**
   - Truy cập `http://localhost/phpmyadmin`.
   - Tạo cơ sở dữ liệu mới tên **`db_fashionkids`** với bảng mã **`utf8mb4_unicode_ci`**.
   - Chọn tab **Nhập (Import)**, trỏ tới tệp `FashionKids/database/db_fashionkids.sql` và nhấn **Thực hiện (Go)** để khởi tạo đầy đủ 17 bảng, dữ liệu mẫu và 15 Stored Routines.

4. **Cấu hình biến môi trường (Tùy chọn cho dịch vụ tích hợp):**
   ```bash
   cd C:\xampp\htdocs\FashionKids
   copy .env.example .env
   ```
   Mở tệp `.env` và điền thông tin khóa API của bạn (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GEMINI_API_KEY`, `GHN_TOKEN`) nếu muốn sử dụng các dịch vụ mở rộng.

5. **Truy cập ứng dụng:**
   Mở trình duyệt tại đường dẫn: `http://localhost/FashionKids/index.php`

### 7.3. Hỗ trợ kết nối đa môi trường tự động (`config/database.php`)

Hệ thống tự động nhận diện địa chỉ máy chủ để chuyển đổi cấu hình kết nối mà không cần sửa mã nguồn:

- **Localhost XAMPP:** Kết nối `localhost` với tài khoản `root`.
- **Shared Database LAN:** Các máy trạm trong cùng mạng nội bộ tự động kết nối tới máy chủ cơ sở dữ liệu trung tâm (`192.168.1.17`) thông qua tài khoản `team_fashionkids`.
- **Production Hosting:** Tự động nhận diện tên miền `free.nf` / `infinityfree.com` và kích hoạt cơ chế truy vấn SQL dự phòng tương thích với Shared Hosting.

---

## 8. Tài Khoản Kiểm Thử Phân Quyền (RBAC)

Hệ thống đã khởi tạo sẵn bộ tài khoản mẫu trong bảng `hgb_nguoi_dung` để phục vụ kiểm thử đầy đủ các luồng nghiệp vụ theo từng vai trò:

| Vai Trò Hệ Thống | Tên Đăng Nhập | Mật Khẩu | Cấp Bậc / Định Danh | Phạm Vi Kiểm Thử Chính |
| :--- | :--- | :---: | :--- | :--- |
| **Quản trị viên** (`quan_tri`) | `admin_fashion` | `123456` | Quản trị tối cao | Truy cập `admin-dashboard.php`, quản lý sản phẩm/kho, cấu hình size, hạng VIP, banner và duyệt hoàn hàng. |
| **Nhân viên** (`nhan_vien`) | `staff_tuvan` | `123456` | Nhân viên vận hành | Truy cập `staff-orders.php`, duyệt đơn hàng, đối soát VietQR, in nhãn vận đơn, trực Live Chat và nhập kho. |
| **Khách hàng VIP Vàng** (`khach_hang`) | `me_be_sam` | `123456` | Hạng Vàng (`1.600đ` - Giảm `10%`) | Kiểm thử chiết khấu tự động `10%` khi đặt hàng, viết đánh giá sản phẩm và gửi yêu cầu hoàn hàng. |
| **Khách hàng Kim Cương** (`khach_hang`) | `vip_kim_cuong` | `123456` | Hạng Kim Cương (`>= 3.000đ` - Giảm `15%`) | Kiểm thử mức chiết khấu VIP cao nhất `15%` trên tổng giá trị đơn hàng. |
| **Khách vãng lai** (`guest`) | *(Không đăng nhập)* | — | Phiên `PHPSESSID` | Duyệt hàng, gợi ý size AJAX, thêm giỏ hàng tạm và kiểm thử chặn thanh toán (`HTTP 401`). |

---

## 9. Quy Trình Cộng Tác Nhóm (Git Workflow)

Dự án áp dụng mô hình phân nhánh tính năng (**Feature Branch Workflow**) trên GitHub để bảo đảm tính độc lập và kiểm soát chất lượng mã nguồn:

- **`main`:** Nhánh phát hành ổn định, đồng bộ trực tiếp lên môi trường Production Hosting.
- **`develop`:** Nhánh tích hợp chính thức của nhóm trước khi nghiệm thu.
- **04 nhánh tính năng độc lập:**
  - `feature/hgb-auth-layout` (Hồ Gia Bảo)
  - `feature/lbn-catalog-stock` (Lê Bảo Ngọc)
  - `feature/lnkt-order-size` (Lê Nguyễn Khánh Trình)
  - `feature/tgb-checkout-ajax` (Trần Gia Bảo)

Mọi thay đổi từ nhánh tính năng đều được tạo **Pull Request (PR)** vào `develop` và thực hiện kiểm tra chéo mã nguồn (**Code Review**) trước khi hợp nhất (**Merge**).

---

## 10. Bản Quyền và Tài Liệu Tham Khảo

- **Bản quyền:** Sản phẩm thuộc đồ án môn học **Phát Triển Ứng Dụng Web** của **Nhóm 10 – Đại học Kinh tế TP. Hồ Chí Minh (UEH)**. Phát hành phục vụ mục đích học tập và nghiên cứu theo giấy phép **MIT License**.
- **Tài liệu kỹ thuật tham chiếu:**
  - Tài liệu hướng dẫn học phần *Phát triển ứng dụng Web* – TS. Đặng Ngọc Hoàng Thành (UEH, 2026).
  - PHP 8.x Documentation – Object-Oriented Programming & PDO (`https://www.php.net/manual/en/`).
  - MySQL 8.0 Reference Manual – Stored Routines & InnoDB Storage Engine (`https://dev.mysql.com/doc/refman/8.0/en/`).
  - Bootstrap v4.6.2 Documentation (`https://getbootstrap.com/docs/4.6/`).
  - Vietnam Provinces Open API (`https://provinces.open-api.vn/`) và VietQR / Casso API v2 (`https://casso.vn/`).
