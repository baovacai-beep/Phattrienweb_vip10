# BẢNG DANH MỤC THẨM ĐỊNH TÀI LIỆU YÊU CẦU (SRS REVIEW CHECKLIST)
## Dự án: Nền tảng Thương mại Điện tử D2C FashionKids

- **Tiêu chuẩn tham chiếu:** IEEE Std 830-1998 & ISO/IEC/IEEE 29148:2018
- **Tài liệu thẩm định:** `SRS_FashionKids_v1.0_Draft.md` $\rightarrow$ `SRS_FashionKids_v2.0_Approved.md`
- **Review Leader:** Hồ Gia Bảo (PM & QA Lead)
- **Hội đồng phản biện:** Lê Tô Nguyệt Minh (Technical), Lê Bảo Ngọc (UX & Risk), Lê Nguyễn Khánh Trình (Testability & Scribe)

---

## 1. Bảng tiêu chí đánh giá chất lượng yêu cầu (15 tiêu chí)

| Mã TC | Thuộc tính chất lượng (ISO 29148) | Câu hỏi thẩm định chất lượng yêu cầu | Kết quả rà soát `v1.0-Draft` | Mã lỗi phát hiện | Kết quả sau chuẩn hóa `v2.0-Approved` |
| :---: | :--- | :--- | :---: | :---: | :---: |
| **CHK-01** | Tính đầy đủ *(Completeness)* | Tài liệu đã nêu rõ bối cảnh kinh doanh D2C, mục tiêu nghiệp vụ, phạm vi `In-scope` và `Out-of-scope` chưa? | Đạt | - | Đạt |
| **CHK-02** | Tính đầy đủ *(Completeness)* | Đã liệt kê tường minh danh sách các trang/chức năng bị chặn truy cập cho từng vai trò trong ma trận phân quyền RBAC chưa? | **Chưa đạt** | `SRS-DEF-05` | Đạt |
| **CHK-03** | Tính đầy đủ *(Completeness)* | Đã đặc tả đầy đủ luồng xử lý ngoại lệ khi khách chuyển thiếu tiền VietQR và cơ chế chống gửi lặp mã giao dịch chưa? | **Chưa đạt** | `SRS-DEF-03` | Đạt |
| **CHK-04** | Tính đầy đủ *(Completeness)* | Đã quy định ràng buộc ẩn thông tin cá nhân nhạy cảm của trẻ em trên trang công khai của phân hệ KOL Nhí chưa? | **Chưa đạt** | `SRS-DEF-04` | Đạt |
| **CHK-05** | Tính rõ ràng *(Unambiguity)* | Các yêu cầu chức năng đã loại bỏ hoàn toàn từ ngữ cảm tính (*"nhanh"*, *"chính xác"*) và thay bằng ngưỡng định lượng chưa? | **Chưa đạt** | `SRS-DEF-01` | Đạt |
| **CHK-06** | Tính rõ ràng *(Unambiguity)* | Công thức quy đổi điểm thưởng ($10.000\text{ VNĐ} = 1\text{ điểm}$) đã được diễn đạt tường minh, không gây hiểu nhầm chưa? | Đạt | - | Đạt |
| **CHK-07** | Tính nhất quán *(Consistency)* | Luồng trừ tồn kho khi đặt hàng (`FR-04`) có nhất quán với luồng tự động hủy đơn VietQR sau 24 giờ (`FR-05`) không? | **Chưa đạt** | `SRS-DEF-02` | Đạt |
| **CHK-08** | Tính nhất quán *(Consistency)* | Tỷ lệ chiết khấu của 4 hạng thẻ VIP có thống nhất xuyên suốt giữa `FR-04` và `FR-07`, không bị trùng lặp mâu thuẫn không? | **Chưa đạt** | `SRS-DEF-06` | Đạt |
| **CHK-09** | Tính khả thi *(Feasibility)* | Việc tích hợp Provinces Open API, Casso API v2 và Gmail SMTP có khả thi trên kiến trúc PHP 8 MVC OOP hiện tại không? | Đạt | - | Đạt |
| **CHK-10** | Tính khả thi *(Feasibility)* | Cơ chế giao dịch ACID trừ và hoàn tồn kho có khả thi trên cấu trúc MySQL InnoDB 15 bảng của dự án không? | Đạt | - | Đạt |
| **CHK-11** | Khả năng kiểm chứng *(Verifiability)* | Yêu cầu phi chức năng về giao diện (`NFR-05`) đã có các chỉ số đo lường cụ thể (bảng màu, độ phân giải, điểm SUS) chưa? | **Chưa đạt** | `SRS-DEF-07` | Đạt |
| **CHK-12** | Khả năng kiểm chứng *(Verifiability)* | Cơ chế bảo mật tính giá phía máy chủ (`Zero-Trust`) đã được mô tả đủ rõ để bộ phận QC thiết kế ca kiểm thử chưa? | **Chưa đạt** | `SRS-DEF-08` | Đạt |
| **CHK-13** | Khả năng truy vết *(Traceability)* | Mỗi yêu cầu chức năng và phi chức năng đã được gán mã định danh duy nhất (`FR-01..08`, `NFR-01..05`) để truy vết chưa? | Đạt | - | Đạt |
| **CHK-14** | Khả năng truy vết *(Traceability)* | Tài liệu có ghi nhận đầy đủ thông tin phiên bản, ngày lập, tác giả và người phê duyệt không? | Đạt | - | Đạt |
| **CHK-15** | Khả năng truy vết *(Traceability)* | Các nhóm chức năng và đối tượng sử dụng đã được phân tách rõ ràng theo cấu trúc chuẩn của tài liệu SRS chưa? | Đạt | - | Đạt |

---

## 2. Tổng hợp kết quả đánh giá

- **Tổng số tiêu chí thẩm định:** `15` tiêu chí
- **Kết quả lần rà soát 1 (`v1.0-Draft` – Ngày 19/11/2026):** `07/15` tiêu chí Đạt (`46.7%`), `08/15` tiêu chí Chưa đạt (`53.3%`) $\rightarrow$ **Quyết định: Phê duyệt có điều kiện (Conditional Approval)**.
- **Kết quả thẩm tra lại (`v2.0-Approved` – Ngày 21/11/2026):** `15/15` tiêu chí Đạt (`100.0%`) $\rightarrow$ **Quyết định: Phê duyệt chính thức & Đóng băng tài liệu (Approved & Frozen)**.