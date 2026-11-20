# TÀI LIỆU ĐẶC TẢ YÊU CẦU PHẦN MỀM (SRS)
## Dự án: Nền tảng Thương mại Điện tử D2C FashionKids

- **Mã tài liệu:** `SRS-FK-2026-001`
- **Phiên bản:** `v1.0-Draft` *(Bản thảo phục vụ phiên thẩm định SRSR)*
- **Ngày lập:** `17/11/2026`
- **Tác giả (Author):** Trần Gia Bảo – Lead Business Analyst (Nhóm 4)
- **Người phê duyệt dự kiến:** Hồ Gia Bảo – Review Leader (PM & QA Lead)
- **Kho lưu trữ dự án:** `https://github.com/baovacai-beep/Phattrienweb_vip10`

---

## 1. Giới thiệu tổng quan

### 1.1. Mục tiêu và bối cảnh nghiệp vụ
Hệ thống Thương mại Điện tử D2C **FashionKids** được xây dựng nhằm phục vụ mô hình bán lẻ thời trang trẻ em trực tiếp từ nhãn hàng đến phụ huynh (Direct-to-Consumer), chuyên cung cấp các dòng trang phục từ vải sợi tre và cotton hữu cơ kháng khuẩn cho trẻ từ 1 đến 8 tuổi. Hệ thống giúp doanh nghiệp làm chủ kênh phân phối độc quyền, đồng thời giải quyết khó khăn chọn sai kích cỡ của phụ huynh khi mua sắm trực tuyến.

### 1.2. Phạm vi hệ thống
- **Phạm vi thực hiện (In-scope):** Website thương mại điện tử Responsive trên nền tảng Bootstrap 4; Backend PHP 8 theo mô hình hướng đối tượng MVC; Cơ sở dữ liệu MySQL gồm 15 bảng chuẩn hóa bậc 3 (3NF) kèm 12 Stored Procedures và Functions; tích hợp Provinces Open API, cổng thanh toán VietQR Napas 247 đối soát qua Casso API v2 và gửi mã OTP qua Gmail SMTP.
- **Phạm vi loại trừ (Out-of-scope):** Chưa hỗ trợ giao vận quốc tế ngoài lãnh thổ Việt Nam và chưa kết nối hệ thống hoạch định nguồn lực doanh nghiệp (ERP) của xưởng may.

### 1.3. Phân loại đối tượng sử dụng
1. **Khách vãng lai (Guest):** Người dùng chưa đăng nhập, định danh theo phiên trình duyệt (`session_id`).
2. **Khách hàng thành viên (Customer / VIP):** Phụ huynh đã đăng ký và xác thực tài khoản thành công.
3. **Nhân viên vận hành (Staff):** Nhân sự xử lý đơn hàng, kho vận, chăm sóc khách hàng và sơ tuyển hồ sơ người mẫu nhí.
4. **Quản trị viên hệ thống (Admin):** Chủ thương hiệu và quản trị kỹ thuật vận hành toàn hệ thống.

---

## 2. Đặc tả yêu cầu chức năng (Functional Requirements)

### FR-01: Quản lý định danh và Phân quyền kiểm soát truy cập (RBAC)
- Người dùng đăng ký tài khoản bằng họ tên, email, số điện thoại và mật khẩu; hệ thống gửi mã xác thực OTP 6 chữ số qua Gmail SMTP để kích hoạt tài khoản.
- Hệ thống phân quyền 4 vai trò (`Guest`, `Customer`, `Staff`, `Admin`) thông qua RBAC Middleware; nhân viên (`Staff`) không được phép truy cập vào các trang quản trị cấp cao của quản trị viên.

### FR-02: Khám phá và Lọc danh mục sản phẩm
- Trang danh mục hiển thị danh sách sản phẩm theo lưới đáp ứng, hỗ trợ lọc động theo danh mục, giới tính và khoảng giá bằng AJAX mà không cần tải lại toàn bộ trang.

### FR-03: Thước dây gợi ý kích cỡ tự động
- Phụ huynh mở hộp thoại thước dây trên trang chi tiết sản phẩm để tra cứu cỡ đồ phù hợp cho bé.
- Hệ thống gợi ý size nhanh và chính xác khi phụ huynh nhập chiều cao hoặc cỡ chân của bé tại hộp thoại thước dây.

### FR-04: Giỏ hàng, Đặt hàng và Trừ tồn kho ACID
- Khách hàng thêm sản phẩm vào giỏ hàng, chọn địa chỉ giao hàng từ Provinces Open API (63 Tỉnh/Thành phố và Quận/Huyện).
- Khi xác nhận đặt hàng, hệ thống gọi thủ tục `sp_tgb_tao_don_hang_va_chi_tiet` trong khối giao dịch ACID để tạo đơn và trừ lùi ngay số lượng tồn kho trong bảng `lbn_san_pham`.
- Đơn hàng hoàn tất sẽ được quy đổi điểm thưởng theo tỷ lệ $10.000\text{ VNĐ} = 1\text{ điểm}$ và áp dụng giảm giá thẻ VIP cho khách hàng (hạng Bạc được giảm $3\%$).

### FR-05: Thanh toán VietQR và Đối soát tự động qua Casso API v2
- Khách hàng có thể chọn thanh toán tiền mặt khi nhận hàng (COD) hoặc chuyển khoản qua mã VietQR Napas 247 được sinh động kèm nội dung chuyển khoản `FK{id}`.
- Hệ thống đối soát biến động số dư qua dịch vụ `CassoService` (Casso API v2); khi khách hàng chuyển khoản đúng số tiền đơn hàng với nội dung `FK{id}`, hệ thống tự động cập nhật trạng thái đơn sang đã thanh toán.
- Đối với các đơn hàng chọn phương thức VietQR nhưng chưa thanh toán sau 24 giờ kể từ lúc đặt, hệ thống tự động chuyển trạng thái đơn sang `da_huy`.

### FR-06: Điều phối đơn hàng và Vận hành kho
- Nhân viên vận hành tiếp nhận đơn hàng trên cổng nội bộ, cập nhật trạng thái đơn qua 5 bước (`cho_duyet` $\rightarrow$ `da_xac_nhan` $\rightarrow$ `dang_giao` $\rightarrow$ `hoan_tat` hoặc `da_huy`) và xuất mẫu in tem vận đơn chuẩn khổ in.

### FR-07: Tích điểm và Thăng hạng thẻ VIP tự động
- Khi đơn hàng đạt trạng thái `hoan_tat`, hệ thống quy đổi giá trị thanh toán thành điểm tích lũy theo tỷ lệ $10.000\text{ VNĐ} = 1\text{ điểm}$ và gọi thủ tục `sp_hgb_tich_diem_nang_hang`.
- Hệ thống tự động nâng hạng thẻ thành viên VIP gồm 4 cấp độ chiết khấu: **Đồng ($0\%$)**, **Bạc ($5\%$)**, **Vàng ($10\%$)** và **Kim Cương ($15\%$)**.

### FR-08: Tuyển chọn KOL Nhí và Đánh giá sản phẩm (Verified Buyer)
- Cổng tuyển chọn KOL Nhí cho phép phụ huynh nộp hồ sơ đăng ký bé tham gia tuyển chọn người mẫu nhí lookbook kèm danh sách ảnh và thông tin liên hệ để hiển thị bình chọn trên trang Tạp chí mẫu nhí.
- Chức năng đánh giá sản phẩm từ 1 đến 5 sao kèm hình ảnh/video chỉ mở khóa cho khách hàng đã mua sản phẩm đó với đơn hàng hoàn tất (`sp_tgb_gui_danh_gia_san_pham`).

---

## 3. Đặc tả yêu cầu phi chức năng (Non-Functional Requirements)

### NFR-01: Tính hiệu quả (Efficiency)
- Thời gian phản hồi của các API AJAX lọc sản phẩm và gợi ý kích cỡ không vượt quá $100\text{ms}$; thời gian tải trang danh mục và xử lý tạo đơn hàng dưới $1.5\text{ giây}$ trong điều kiện mạng tiêu chuẩn.

### NFR-02: Năng lực chịu tải (Capacity & Scalability)
- Hệ thống đảm bảo phục vụ ổn định tối thiểu $500$ phiên truy cập đồng thời (Concurrent Users) trong khung giờ mở bán bộ sưu tập mới mà không xảy ra lỗi nghẽn kết nối cơ sở dữ liệu.

### NFR-03: Tính tin cậy (Reliability)
- Duy trì độ sẵn sàng tối thiểu $99.5\%$ trong khung giờ hoạt động chính ($06\text{h}00 - 23\text{h}00$); cơ chế giao dịch CSDL tự động `ROLLBACK` toàn bộ thao tác nếu phát sinh xung đột tồn kho.

### NFR-04: An toàn bảo mật (Security)
- Hệ thống mã hóa đường truyền qua HTTPS (TLS 1.3), băm mật khẩu người dùng bằng thuật toán an toàn và chống tấn công SQL Injection thông qua Prepared Statements.

### NFR-05: Tính khả dụng (Usability)
- Giao diện website sử dụng màu sắc hài hòa, thân thiện với phụ huynh và hiển thị tốt trên điện thoại di động.