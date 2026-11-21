# TÀI LIỆU ĐẶC TẢ YÊU CẦU PHẦN MỀM (SRS)
## Dự án: Nền tảng Thương mại Điện tử D2C FashionKids

- **Mã tài liệu:** `SRS-FK-2026-001`
- **Phiên bản:** `v2.0-Approved` *(Bản chuẩn hóa chính thức sau phiên thẩm định SRSR)*
- **Ngày phê duyệt:** `21/11/2026`
- **Tác giả (Author):** Trần Gia Bảo – Lead Business Analyst
- **Hội đồng thẩm định (Reviewers):** Lê Tô Nguyệt Minh, Lê Bảo Ngọc, Lê Nguyễn Khánh Trình
- **Người phê duyệt (Review Leader):** Hồ Gia Bảo – PM & QA Lead
- **Kho lưu trữ dự án:** `https://github.com/baovacai-beep/Phattrienweb_vip10`

---

## Lịch sử sửa đổi tài liệu (Revision History)

| Phiên bản | Ngày cập nhật | Người thực hiện | Nội dung thay đổi chính |
| :---: | :---: | :--- | :--- |
| `v1.0-Draft` | `17/11/2026` | Trần Gia Bảo | Khởi tạo bản thảo đặc tả 8 nhóm chức năng (`FR-01..08`) và 5 yêu cầu phi chức năng (`NFR-01..05`). |
| `v2.0-Approved` | `21/11/2026` | Trần Gia Bảo | Khắc phục 08 khiếm khuyết (`SRS-DEF-01` đến `SRS-DEF-08`) theo kết luận của Hội đồng SRSR; đạt `15/15` tiêu chí Checklist. |

---

## 1. Giới thiệu tổng quan

### 1.1. Mục tiêu và bối cảnh nghiệp vụ
Hệ thống Thương mại Điện tử D2C **FashionKids** được xây dựng nhằm phục vụ mô hình bán lẻ thời trang trẻ em trực tiếp từ nhãn hàng đến phụ huynh (Direct-to-Consumer), chuyên cung cấp các dòng trang phục từ vải sợi tre và cotton hữu cơ kháng khuẩn cho trẻ từ 1 đến 8 tuổi. Hệ thống giúp doanh nghiệp làm chủ kênh phân phối độc quyền, đồng thời giảm tỷ lệ đổi trả hàng do sai kích cỡ xuống dưới $5\%$.

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
- Người dùng đăng ký tài khoản bằng họ tên, email, số điện thoại và mật khẩu; hệ thống gửi mã xác thực OTP 6 chữ số (hiệu lực 5 phút) qua Gmail SMTP để kích hoạt tài khoản.
- **[Chuẩn hóa `SRS-DEF-05`]** Hệ thống phân tách quyền hạn 4 vai trò (`Guest`, `Customer`, `Staff`, `Admin`) thông qua RBAC Middleware. Quy định tường minh **04 phân hệ chỉ dành riêng cho vai trò `Admin`** gồm:
  1. Dashboard thống kê doanh thu tổng hợp;
  2. Cấu hình bảng quy chuẩn kích cỡ (`lnkt_bang_kich_co`);
  3. Quản trị tài khoản và phân quyền người dùng (`hgb_nguoi_dung`);
  4. Quản trị banner sự kiện (`lnkt_banner_su_kien`).
- Nếu tài khoản `Staff`, `Customer` hoặc `Guest` gửi yêu cầu truy cập vào 04 phân hệ trên, hệ thống bắt buộc từ chối và trả về mã trạng thái **`HTTP 403 Forbidden`**.

### FR-02: Khám phá và Lọc danh mục sản phẩm
- Trang danh mục hiển thị danh sách sản phẩm theo lưới đáp ứng, hỗ trợ lọc động theo danh mục, giới tính và khoảng giá bằng AJAX mà không cần tải lại toàn bộ trang.

### FR-03: Thước dây gợi ý kích cỡ tự động
- Phụ huynh mở hộp thoại thước dây trên trang chi tiết sản phẩm để tra cứu cỡ đồ phù hợp cho bé.
- **[Chuẩn hóa `SRS-DEF-01`]** Hệ thống tiếp nhận thông số đầu vào gồm **chiều cao của bé trong phạm vi từ $50\text{ cm}$ đến $160\text{ cm}$** (đối với quần áo, sai số biên $\pm 0.5\text{ cm}$) hoặc **chiều dài bàn chân trong phạm vi từ $11\text{ cm}$ đến $22\text{ cm}$** (đối với giày dép), gọi Stored Function `fn_lnkt_goi_y_kich_co_chuan` và trả về kết quả định dạng JSON qua AJAX trong thời gian **$\le 100\text{ms}$**, kèm thông tin kích cỡ chuẩn và ghi chú độ co giãn của chất liệu vải sợi tre.

### FR-04: Giỏ hàng, Đặt hàng và Trừ tồn kho ACID
- Khách hàng thêm sản phẩm vào giỏ hàng, chọn địa chỉ giao hàng từ Provinces Open API (63 Tỉnh/Thành phố và Quận/Huyện) và được áp dụng tỷ lệ chiết khấu theo hạng thẻ VIP hiện tại của tài khoản.
- Khi xác nhận đặt hàng, hệ thống gọi thủ tục `sp_tgb_tao_don_hang_va_chi_tiet` trong khối giao dịch ACID (`START TRANSACTION ... COMMIT`) để tạo đơn và trừ lùi ngay số lượng tồn kho trong bảng `lbn_san_pham`; tự động phát sinh ngoại lệ `SIGNAL SQLSTATE '45000'` (`[TGB_ERR_04]`) và `ROLLBACK` nếu số lượng đặt vượt quá tồn kho hiện có.
- *(Ghi chú `SRS-DEF-06`: Quy tắc quy đổi điểm thưởng và bảng tỷ lệ chiết khấu VIP được đặc tả tập trung duy nhất tại mục `FR-07` để đảm bảo tính nhất quán).*

### FR-05: Thanh toán VietQR và Đối soát tự động qua Casso API v2
- Khách hàng có thể chọn thanh toán tiền mặt khi nhận hàng (COD) hoặc chuyển khoản qua mã VietQR Napas 247 được sinh động kèm nội dung chuyển khoản `FK{id}`.
- **[Chuẩn hóa `SRS-DEF-03`]** Dịch vụ `CassoService` đối soát biến động số dư qua Casso API v2 theo các quy tắc sau:
  - Nếu số tiền chuyển khoản $\ge$ tổng thanh toán đơn hàng: tự động cập nhật trạng thái đơn sang `da_thanh_toan`.
  - Nếu số tiền chuyển khoản $<$ tổng thanh toán đơn hàng: ghi nhận trạng thái `thanh_toan_mot_phan` và gửi thông báo số tiền còn thiếu để khách hàng bổ sung.
  - Lưu trữ `ma_giao_dich_ngan_hang` với ràng buộc khóa duy nhất (`UNIQUE`) trong cơ sở dữ liệu nhằm ngăn chặn việc xử lý lặp lại cùng một giao dịch (chống Replay Attack).
- **[Chuẩn hóa `SRS-DEF-02`]** Đối với các đơn hàng chọn phương thức VietQR nhưng chưa thanh toán sau 24 giờ kể từ lúc đặt (hoặc khách hàng hủy đơn hợp lệ trước khi giao vận), khi đơn hàng chuyển sang trạng thái `da_huy`, hệ thống bắt buộc thực thi giao dịch ACID để **cộng hoàn trả lại đầy đủ `so_luong` từng sản phẩm về bảng `lbn_san_pham`**.

### FR-06: Điều phối đơn hàng và Vận hành kho
- Nhân viên vận hành tiếp nhận đơn hàng trên cổng nội bộ, cập nhật trạng thái đơn qua 5 bước (`cho_duyet` $\rightarrow$ `da_xac_nhan` $\rightarrow$ `dang_giao` $\rightarrow$ `hoan_tat` hoặc `da_huy`) và xuất mẫu in tem vận đơn chuẩn khổ in `@media print`.

### FR-07: Tích điểm và Thăng hạng thẻ VIP tự động
- **[Chuẩn hóa `SRS-DEF-06`]** Khi đơn hàng đạt trạng thái `hoan_tat`, hệ thống quy đổi giá trị thanh toán thành điểm tích lũy theo công thức thống nhất **$10.000\text{ VNĐ} = 1\text{ điểm}$** và tự động gọi thủ tục `sp_hgb_tich_diem_nang_hang`.
- Hệ thống tự động nâng hạng thẻ thành viên VIP theo 04 cấp độ chiết khấu thống nhất toàn hệ thống:
  1. **Hạng Đồng:** Giảm giá **$0\%$**;
  2. **Hạng Bạc:** Giảm giá **$5\%$**;
  3. **Hạng Vàng:** Giảm giá **$10\%$**;
  4. **Hạng Kim Cương:** Giảm giá **$15\%$**.

### FR-08: Tuyển chọn KOL Nhí và Đánh giá sản phẩm (Verified Buyer)
- Cổng tuyển chọn KOL Nhí cho phép phụ huynh nộp hồ sơ đăng ký bé tham gia tuyển chọn người mẫu nhí lookbook (lưu mảng ảnh JSON) và xét duyệt qua 3 cấp bởi Staff/Admin.
- **[Chuẩn hóa `SRS-DEF-04`]** Ràng buộc bảo vệ quyền riêng tư trẻ em: trên giao diện Tạp chí mẫu nhí công khai và các API JSON trả về danh sách ứng viên, hệ thống **chỉ được phép hiển thị tên thân mật và độ tuổi của bé**; tuyệt đối ẩn $100\%$ số điện thoại phụ huynh, ngày tháng năm sinh đầy đủ và địa chỉ nhà riêng (chỉ hiển thị cho tài khoản `Staff`/`Admin` đã xác thực trong trang quản trị nội bộ).
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
- **[Chuẩn hóa `SRS-DEF-08`]** Áp dụng nguyên tắc **Zero-Trust Client Data**: khi tiếp nhận yêu cầu tạo đơn hàng, máy chủ tuyệt đối không sử dụng đơn giá sản phẩm hoặc tỷ lệ chiết khấu VIP gửi lên từ phía trình duyệt (Client-side); Backend chỉ nhận `ma_san_pham` và `so_luong` từ giỏ hàng, sau đó tự truy vấn đơn giá gốc và `%` giảm giá thẻ VIP trực tiếp từ MySQL để tính toán tổng tiền thanh toán.

### NFR-05: Tính khả dụng (Usability)
- **[Chuẩn hóa `SRS-DEF-07`]** Giao diện website tuân thủ hệ thống thiết kế bảng màu Bright Pastel theo tỷ lệ phối màu **$60-30-10$** (tách biệt mã CSS chuẩn, không dùng inline style), đảm bảo hiển thị chuẩn Responsive không vỡ bố cục ở hai ngưỡng độ phân giải **$1920 \times 1080\text{px}$** (Desktop) và **$375\text{px}$** (Mobile), đồng thời đạt điểm đánh giá khả dụng **SUS (System Usability Scale) $\ge 80/100$** qua khảo sát người dùng thử nghiệm.