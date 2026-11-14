<?php
/**
 * Lớp Model Nội Dung Thời Gian Thực (models/RealtimeContentModel.php)
 * Đồng bộ Tạp chí & Báo chí (tgb_bai_viet_tin_tuc, tgb_binh_luan_bai_viet),
 * Tin nhắn CSKH trực tuyến (tgb_tin_nhan_tu_van),
 * Lịch sự kiện Banner (lnkt_banner_su_kien) và Tuyển chọn KOL Nhí (lnkt_dang_ky_kol_nhi).
 */

require_once __DIR__ . '/BaseModel.php';

class RealtimeContentModel extends BaseModel {

    public function __construct() {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            $savePath = session_save_path();
            $canWrite = false;
            if (!empty($savePath) && is_dir($savePath)) {
                $probe = @tempnam($savePath, 'fk_');
                if ($probe !== false) {
                    @unlink($probe);
                    $canWrite = true;
                }
            }
            if (!$canWrite) {
                $tempDir = sys_get_temp_dir();
                if (is_dir($tempDir)) {
                    @session_save_path($tempDir);
                }
            }
            @session_start();
        }
        $this->ensureSeedDataInDb();
    }

    /**
     * Danh sách 14 chuyên đề tạp chí & điểm tin báo chí FashionKids (chia 3 trang: 6 bài/trang)
     * Sử dụng bộ ảnh bìa chuyên biệt tại thư mục /FashionKids/images/magazine/
     */
    public function getDefaultArticles(): array {
        return [
            1 => [
                'id'            => 1,
                'title'         => 'Top 5 Cách Phối Đồ Cho Bé Gái Đi Tiệc & Dạo Phố Cực Đáng Yêu Mùa Hè 2026',
                'slug'          => 'top-5-cach-phoi-do-cho-be-gai-mua-he-2026',
                'category_key'  => 'meo_phoi_do',
                'category_name' => 'Mẹo phối đồ',
                'badge_class'   => 'badge-peach',
                'image'         => '/FashionKids/images/magazine/mag-phoi-do-du-tiec.jpg',
                'author'        => 'Stylist Bảo Ngọc',
                'author_role'   => 'Giám Đốc Sáng Tạo FashionKids',
                'read_time'     => '5 phút đọc',
                'published_at'  => '10/11/2026',
                'views'         => 1820,
                'likes'         => 346,
                'status'        => 'xuat_ban',
                'is_featured'   => true,
                'summary'       => 'Gợi ý những công thức phối đầm công chúa sợi tre tone Bright Pastel vừa thoáng mát, thấm hút mồ hôi gấp 2 lần cotton vừa giúp bé tỏa sáng trong mọi khung hình.',
                'tags'          => ['#PhoiDoBeGai', '#VaiSoiTre', '#Lookbook2026', '#BrightPastel'],
                'related_product_ids' => [101, 103, 104],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Mùa hè nhiệt đới là thời điểm các bé gái thỏa sức diện những mẫu váy đầm xòe bồng bềnh đi dự tiệc sinh nhật hay dạo phố cuối tuần cùng ba mẹ. Tuy nhiên, làn da trẻ nhỏ mỏng hơn người lớn tới 30% và rất dễ đổ mồ hôi lưng. Dưới đây là 5 bí quyết phối đồ vừa thời thượng chuẩn Lookbook vừa bảo vệ làn da nhạy cảm của bé.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">1. Ưu tiên chất liệu Vải Sợi Tre (Organic Bamboo) &amp; Cotton Hữu Cơ</h4>
                    <p class="text-slate">Làn da của bé từ 1 đến 6 tuổi cực kỳ nhạy cảm với ma sát và nhiệt độ cao. Thay vì các loại vải ren tổng hợp gây ngứa rát, mẹ hãy chọn đầm có lớp lót <strong>100% sợi tre tự nhiên kháng khuẩn 99.8%</strong> đạt chuẩn <em>OEKO-TEX® Standard 100</em>. Cấu trúc vi lỗ thoáng khí của sợi tre giúp hạ nhiệt bề mặt da bé tới 2°C ngay khi mặc.</p>
                    <div class="blog-quote-pastel my-4 p-4">
                        <i class="fa-solid fa-quote-left text-peach h3 mb-2 d-block"></i>
                        <p class="mb-1 font-italic text-slate h6">"Mỗi thiết kế của FashionKids đều sử dụng cúc bấm gỗ tự nhiên bo tròn và chỉ may hữu cơ không chứa formaldehyde, giúp bé thoải mái vận động suốt 12 giờ."</p>
                        <small class="text-muted-custom font-weight-bold">— Đội ngũ Thiết kế R&amp;D FashionKids</small>
                    </div>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">2. Quy tắc phối màu Bright Pastel 60 - 30 - 10</h4>
                    <p class="text-slate">Những gam màu <strong>Hồng Đào Phấn (Soft Peach #FF9E93)</strong>, <strong>Xanh Bạc Hà (Mint #B8E0D2)</strong>, <strong>Vàng Bơ (Buttercup #FDE2B8)</strong> và <strong>Trắng Kem Sữa (#FAF8F5)</strong> không chỉ ít hấp thụ nhiệt dưới ánh nắng mặt trời mà còn tôn lên nét trong trẻo, đáng yêu của bé.</p>
                    <ul class="text-slate mb-4">
                        <li class="mb-2"><strong>Combo Dạo Phố:</strong> Váy Công Chúa Sợi Tre Hoa Cúc Họa Mi phối cùng mũ cói rộng vành và tất ren cổ ngắn.</li>
                        <li class="mb-2"><strong>Combo Dự Tiệc Tối:</strong> Đầm Voan Tơ Sợi Tre Nơ Bản To kết hợp áo khoác Cardigan mỏng chống gió điều hòa.</li>
                        <li class="mb-2"><strong>Combo Năng Động:</strong> Kết hợp cùng Giày Sandal Tập Đi Đế Cao Su Non chống trượt bảo vệ cổ chân bé.</li>
                    </ul>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">3. Chọn kích cỡ chuẩn xác theo chiều cao thực tế</h4>
                    <p class="text-slate">Mẹ đừng quên sử dụng công cụ <strong>Thước Dây Gợi Ý Size Thông Minh</strong> ngay trên website FashionKids để đo chiều cao (cm) và độ dài bàn chân (cm) của bé trước khi đặt hàng nhé!</p>
                '
            ],
            2 => [
                'id'            => 2,
                'title'         => 'Hậu Trường Buổi Chụp Lookbook Tuyển Chọn Siêu Sao Nhí KOL FashionKids 2026',
                'slug'          => 'hau-truong-buoi-chup-lookbook-sieu-sao-nhi-kol-2026',
                'category_key'  => 'hau_truong',
                'category_name' => 'Hậu trường mẫu nhí',
                'badge_class'   => 'badge-mint',
                'image'         => '/FashionKids/images/magazine/mag-studio-kol-nhi.jpg',
                'author'        => 'Lê Nguyễn Khánh Trình',
                'author_role'   => 'Đạo Diễn Hình Ảnh & Casting',
                'read_time'     => '4 phút đọc',
                'published_at'  => '09/11/2026',
                'views'         => 1385,
                'likes'         => 264,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Cùng đột nhập vào phim trường tràn ngập tiếng cười của các thiên thần nhí trong buổi thử đồ và chụp hình chiến dịch Bộ Sưu Tập Mùa Hè 2026.',
                'tags'          => ['#CastingKOLNhi', '#LookbookStudio', '#SieuSaoNhi', '#FashionKids2026'],
                'related_product_ids' => [102, 101],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Hơn 120 hồ sơ ứng tuyển Đại sứ thương hiệu nhí (KOL Nhí) đã gửi về cổng Casting trực tuyến chỉ trong tuần đầu tiên mở cổng. Cuối tuần qua, 15 gương mặt xuất sắc nhất đã có buổi thử đồ và chụp hình Lookbook tại Studio Quận 1, TP.HCM.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">1. Phim trường tiêu chuẩn an toàn dành riêng cho bé</h4>
                    <p class="text-slate">Để các bé từ 1 đến 6 tuổi tự nhiên tỏa sáng trước ống kính, toàn bộ hệ thống đèn chiếu sáng tại Studio đều sử dụng công nghệ Softbox khuếch tán ánh sáng dịu nhẹ, tuyệt đối không dùng đèn Flash chói mắt bé. Nhiệt độ phòng luôn duy trì ở mức 26°C kèm khu vực vui chơi cầu trượt và bàn tiệc bánh quy hữu cơ.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">2. Đặc quyền trị giá 15.000.000đ cho Quán Quân KOL Nhí</h4>
                    <p class="text-slate">Các bé trúng tuyển vòng Studio sẽ nhận ngay gói tài trợ trang phục sợi tre suốt 1 năm trị giá 15 triệu đồng, nâng cấp thẻ thành viên của mẹ lên hạng <strong>VIP Kim Cương (-15% trọn đời)</strong> và xuất hiện trên Banner chính thức của FashionKids.</p>
                '
            ],
            3 => [
                'id'            => 3,
                'title'         => 'Vì Sao Vải Sợi Tre Kháng Khuẩn Là Chân Ái Cho Làn Da Nhạy Cảm Của Trẻ Sơ Sinh & Trẻ Nhỏ?',
                'slug'          => 'vi-sao-vai-soi-tre-khang-khuan-la-chan-ai-cho-be',
                'category_key'  => 'cham_soc_be',
                'category_name' => 'Chăm sóc bé',
                'badge_class'   => 'badge-sky',
                'image'         => '/FashionKids/images/magazine/mag-soi-tre-khang-khuan.jpg',
                'author'        => 'BS. Nhi Khoa Minh Thảo',
                'author_role'   => 'Chuyên Gia Da Liễu Nhi Khoa',
                'read_time'     => '6 phút đọc',
                'published_at'  => '08/11/2026',
                'views'         => 2180,
                'likes'         => 412,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Tìm hiểu cơ chế kháng khuẩn tự nhiên Bamboo Kun giúp ngăn ngừa rôm sảy, hăm da tới 99.8% và chống tia cực tím UPF 50+ vượt trội.',
                'tags'          => ['#VaiSoiTre', '#KhangKhuanTuNhien', '#ChamSocDaBe', '#OekoTex100'],
                'related_product_ids' => [102, 104],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Theo thống kê da liễu nhi khoa, hơn 65% trường hợp rôm sảy, mẩn ngứa và viêm da cơ địa ở trẻ dưới 5 tuổi bắt nguồn từ việc mặc quần áo bí bách, tồn dư hóa chất nhuộm hoặc sợi vải thô cứng cọ xát vào da.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">1. Hợp chất sinh học "Bamboo Kun" kháng khuẩn 99.8%</h4>
                    <p class="text-slate">Trong thân cây tre tự nhiên chứa hợp chất kháng khuẩn sinh học quý giá mang tên <strong>Bamboo Kun</strong>. Ngay cả sau 50 lần giặt, sợi vải tre vẫn giữ nguyên khả năng ức chế vi khuẩn gây mùi và nấm mốc trên bề mặt da bé mà không cần bất kỳ hóa chất kháng khuẩn nhân tạo nào.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">2. Thấm hút gấp 2 lần Cotton &amp; Chống nắng UPF 50+</h4>
                    <p class="text-slate">Mặt cắt ngang của sợi tre chứa hàng triệu vi lỗ nhỏ li ti giúp hút ẩm và bay hơi mồ hôi chỉ trong 3 giây, giữ cho lưng bé luôn khô thoáng, phòng tránh cảm lạnh ngược do mồ hôi thấm ngược vào phổi.</p>
                '
            ],
            4 => [
                'id'            => 4,
                'title'         => '[Điểm Tin Báo Chí] FashionKids Đạt Chứng Nhận Quốc Tế OEKO-TEX® Standard 100 Hạng I Cho Trẻ Em',
                'slug'          => 'diem-tin-bao-chi-fashionkids-dat-chung-nhan-oeko-tex-100',
                'category_key'  => 'bao_chi',
                'category_name' => 'Báo chí & Sự kiện',
                'badge_class'   => 'badge-gold',
                'image'         => '/FashionKids/images/magazine/mag-chung-nhan-oekotex.jpg',
                'author'        => 'Ban Biên Tập Báo Chí',
                'author_role'   => 'Truyền Thông Thương Hiệu',
                'read_time'     => '3 phút đọc',
                'published_at'  => '07/11/2026',
                'views'         => 1650,
                'likes'         => 318,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Toàn bộ dây chuyền sản xuất vải sợi tre và phụ liệu cúc bấm của FashionKids chính thức vượt qua 100 tiêu chí kiểm định khắt khe nhất châu Âu dành cho trẻ sơ sinh.',
                'tags'          => ['#BaoChi', '#OekoTexStandard100', '#ThuongHieuXanh', '#SuKien2026'],
                'related_product_ids' => [101, 104],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">TP. Hồ Chí Minh, ngày 07/11/2026 — Thương hiệu thời trang trẻ em FashionKids vinh dự đón nhận chứng chỉ quốc tế OEKO-TEX® Standard 100 Product Class I (cấp độ khắt khe nhất dành cho trẻ sơ sinh và trẻ nhỏ dưới 36 tháng tuổi).</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Cam kết 100% Minh bạch nguồn gốc nguyên liệu</h4>
                    <p class="text-slate">Từ sợi chỉ may, nhãn mác in nhiệt mềm cho đến từng chiếc cúc gỗ sồi tự nhiên trên sản phẩm của FashionKids đều được kiểm nghiệm độc lập, đảm bảo 0% kim loại nặng, 0% chất tẩy trắng quang học và thân thiện hoàn toàn với môi trường.</p>
                '
            ],
            5 => [
                'id'            => 5,
                'title'         => 'Bí Quyết Chọn Giày Sandal Tập Đi Đế Cao Su Non Bảo Vệ Hệ Xương Bàn Chân Non Nớt Của Bé',
                'slug'          => 'bi-quyet-chon-giay-sandal-tap-di-de-cao-su-non-cho-be',
                'category_key'  => 'meo_phoi_do',
                'category_name' => 'Mẹo phối đồ',
                'badge_class'   => 'badge-peach',
                'image'         => '/FashionKids/images/magazine/mag-chon-sandal-tap-di.jpg',
                'author'        => 'Trần Gia Bảo',
                'author_role'   => 'Chuyên Viên Phụ Kiện Trẻ Em',
                'read_time'     => '4 phút đọc',
                'published_at'  => '06/11/2026',
                'views'         => 1190,
                'likes'         => 215,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Hướng dẫn ba mẹ cách đo chiều dài bàn chân bé chuẩn milimet và chọn sandal đế cao su non uốn dẻo 180 độ chống trơn trượt.',
                'tags'          => ['#SandalTapDi', '#DeCaoSuNon', '#ChonSizeGiayBe', '#BaoVeBanChan'],
                'related_product_ids' => [103],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Giai đoạn từ 10 tháng đến 3 tuổi là thời kỳ vàng hình thành vòm bàn chân và dáng đi của trẻ. Một đôi giày quá cứng hoặc sai kích cỡ có thể ảnh hưởng trực tiếp đến dáng đi của bé sau này.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Công thức cộng thêm 0.5cm - 0.8cm chiều dài bàn chân</h4>
                    <p class="text-slate">Khi đo từ gót chân đến đầu ngón chân cái dài nhất của bé, mẹ hãy cộng thêm 0.5cm (với bàn chân thon) hoặc 0.8cm (với bàn chân bụ bẫm) để ngón chân bé có không gian cử động thoải mái khi bước đi.</p>
                '
            ],
            6 => [
                'id'            => 6,
                'title'         => 'Cẩm Nang Giặt & Bảo Quản Quần Áo Sợi Tre Luôn Mềm Mịn Như Lụa Sau Hàng Trăm Lần Giặt',
                'slug'          => 'cam-nang-giat-va-bao-quan-quan-ao-soi-tre-ben-dep',
                'category_key'  => 'cham_soc_be',
                'category_name' => 'Chăm sóc bé',
                'badge_class'   => 'badge-sky',
                'image'         => '/FashionKids/images/magazine/mag-giat-bao-quan-soi-tre.jpg',
                'author'        => 'Hồ Gia Bảo',
                'author_role'   => 'Quản Trị Chất Lượng FashionKids',
                'read_time'     => '4 phút đọc',
                'published_at'  => '05/11/2026',
                'views'         => 1320,
                'likes'         => 248,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => '4 lưu ý vàng khi giặt máy và phơi đồ sợi tre hữu cơ để sợi vải không bị xù lông, co rút hay giảm khả năng kháng khuẩn.',
                'tags'          => ['#BaoQuanDoSoiTre', '#MeoVatMeBim', '#GiatDoChoBe'],
                'related_product_ids' => [104, 101],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Vải sợi tre tự nhiên có cấu trúc mềm mượt như lụa tơ tằm. Để giữ cho quần áo của bé luôn giữ phom dáng và độ êm ái tối đa, mẹ chỉ cần lưu ý 4 nguyên tắc đơn giản sau.</p>
                    <ul class="text-slate mb-4">
                        <li class="mb-2"><strong>Giặt với nước mát dưới 30°C:</strong> Nước quá nóng có thể làm co sợi cellulose tự nhiên.</li>
                        <li class="mb-2"><strong>Sử dụng túi giặt lưới:</strong> Khi giặt máy, hãy cho đồ của bé vào túi giặt và chọn chế độ giặt nhẹ (Delicate/Baby Care).</li>
                        <li class="mb-2"><strong>Không dùng thuốc tẩy mạnh:</strong> Nên dùng nước giặt hữu cơ dịu nhẹ dành riêng cho trẻ em.</li>
                        <li class="mb-2"><strong>Phơi nơi thoáng gió:</strong> Tránh phơi trực tiếp dưới nắng gắt buổi trưa để giữ màu Pastel luôn tươi mới.</li>
                    </ul>
                '
            ],
            7 => [
                'id'            => 7,
                'title'         => 'Gợi Ý 4 Set Đồ Sợi Tre Đi Học Mầm Non Siêu Thoáng Mát Giúp Bé Tự Tin Vận Động Cả Ngày',
                'slug'          => 'goi-y-4-set-do-soi-tre-di-hoc-mam-non-sieu-thoang-mat',
                'category_key'  => 'meo_phoi_do',
                'category_name' => 'Mẹo phối đồ',
                'badge_class'   => 'badge-peach',
                'image'         => '/FashionKids/images/magazine/mag-set-do-di-hoc-mam-non.jpg',
                'author'        => 'Stylist Bảo Ngọc',
                'author_role'   => 'Giám Đốc Sáng Tạo FashionKids',
                'read_time'     => '5 phút đọc',
                'published_at'  => '04/11/2026',
                'views'         => 1540,
                'likes'         => 289,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Bí quyết chuẩn bị balo đồ đi học mầm non cho bé từ 2 đến 5 tuổi với những set bộ thun sợi tre co giãn 4 chiều, lưng thun êm bụng không hằn đỏ.',
                'tags'          => ['#DoDiHocMamNon', '#SetBoSoiTre', '#PhoiDoChoBe', '#BackToSchool'],
                'related_product_ids' => [102, 104],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Khi bé bắt đầu đi học mầm non, trang phục không chỉ cần đẹp mắt mà còn phải ưu tiên sự thoải mái tối đa cho các hoạt động chạy nhảy ngoài sân trường, giờ ăn trưa và giấc ngủ trưa tại lớp.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">1. Thiết kế lưng thun bản mềm &amp; cúc vai dễ thay</h4>
                    <p class="text-slate">Mỗi ngày ở trường mầm non, cô giáo thường thay từ 2 đến 3 bộ quần áo cho bé. Những set áo Polo sợi tre cổ mềm hoặc bộ thun Gấu Nhí dùng chun bụng dệt nguyên khối của FashionKids giúp bé tự mặc đồ dễ dàng mà không để lại vết hằn trên bụng.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">2. Luôn chuẩn bị thêm 1 chiếc Cardigan mỏng trong balo</h4>
                    <p class="text-slate">Phòng học mầm non thường bật máy lạnh xuyên suốt giờ ngủ trưa. Một chiếc áo khoác Cardigan dệt kim sợi tre mỏng nhẹ sẽ bảo vệ cổ và ngực bé khỏi nhiễm lạnh.</p>
                '
            ],
            8 => [
                'id'            => 8,
                'title'         => 'Bí Quyết Chống Tia Cực Tím UPF 50+ Khi Cho Bé Đi Biển & Dã Ngoại Mùa Hè Cùng Gia Đình',
                'slug'          => 'bi-quyet-chong-tia-cuc-tim-upf-50-khi-cho-be-di-bien',
                'category_key'  => 'cham_soc_be',
                'category_name' => 'Chăm sóc bé',
                'badge_class'   => 'badge-sky',
                'image'         => '/FashionKids/images/magazine/mag-chong-nang-uv-mua-he.jpg',
                'author'        => 'BS. Nhi Khoa Minh Thảo',
                'author_role'   => 'Chuyên Gia Da Liễu Nhi Khoa',
                'read_time'     => '5 phút đọc',
                'published_at'  => '03/11/2026',
                'views'         => 1410,
                'likes'         => 272,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Kết hợp áo khoác chống nắng sợi tre tự nhiên chỉ số UPF 50+ và mũ rộng vành giúp cản tới 98% tia UVA/UVB gây bỏng nắng làn da non nớt của trẻ.',
                'tags'          => ['#ChongNangChoBe', '#UPF50Plus', '#DiBienMuaHe', '#BaoVeDaBe'],
                'related_product_ids' => [104, 103],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Tia cực tím UVA và UVB tại vùng biển nhiệt đới có cường độ rất mạnh từ 9h sáng đến 16h chiều. Bên cạnh kem chống nắng vật lý dành cho trẻ em, lớp trang phục chống tia UV tự nhiên đóng vai trò lá chắn đầu tiên bảo vệ bé.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Khả năng cản tia UV tự nhiên của cấu trúc sợi tre</h4>
                    <p class="text-slate">Nhờ cấu trúc sợi dệt mật độ cao kết hợp hợp chất pectin tự nhiên trong tre, các mẫu áo khoác Hoodie Tai Thỏ và Bomber mỏng của FashionKids đạt chỉ số chống nắng <strong>UPF 50+ (cản 98.4% tia UV)</strong> nhưng vẫn giữ độ thoáng khí tuyệt vời.</p>
                '
            ],
            9 => [
                'id'            => 9,
                'title'         => 'Dấu Ấn Dàn Mẫu Nhí FashionKids Trên Sàn Catwalk Tuần Lễ Thời Trang Trẻ Em Việt Nam 2026',
                'slug'          => 'dau-an-dan-mau-nhi-fashionkids-tren-san-catwalk-2026',
                'category_key'  => 'hau_truong',
                'category_name' => 'Hậu trường mẫu nhí',
                'badge_class'   => 'badge-mint',
                'image'         => '/FashionKids/images/magazine/mag-catwalk-tuan-le-thoi-trang.jpg',
                'author'        => 'Lê Nguyễn Khánh Trình',
                'author_role'   => 'Đạo Diễn Hình Ảnh & Casting',
                'read_time'     => '4 phút đọc',
                'published_at'  => '01/11/2026',
                'views'         => 1760,
                'likes'         => 355,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Chiêm ngưỡng những bước sải chân đầy tự tin và thần thái chuyên nghiệp của 20 đại sứ nhí FashionKids trong đêm trình diễn Bộ sưu tập Bright Pastel.',
                'tags'          => ['#VietnamKidsFashionWeek', '#CatwalkMauNhi', '#HauTruongShow', '#KOLNhi'],
                'related_product_ids' => [101, 104],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Đêm diễn mở màn Tuần lễ Thời trang Trẻ em Việt Nam 2026 đã chứng kiến màn trình diễn bùng nổ của 20 mẫu nhí trưởng thành từ chương trình Tuyển chọn KOL Nhí FashionKids.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Từ cổng đăng ký trực tuyến đến ánh đèn sân khấu lớn</h4>
                    <p class="text-slate">Chỉ sau 2 buổi tập luyện làm quen nhịp nhạc cùng biên đạo catwalk, các bé từ 3 đến 6 tuổi đã tự tin trình diễn những thiết kế Bomber Pastel, Đầm Công Chúa Sợi Tre và Set Đồ Năng Động trước hơn 500 khách mời và cơ quan báo chí.</p>
                '
            ],
            10 => [
                'id'            => 10,
                'title'         => 'Chuyên Gia Nhi Khoa Hướng Dẫn Phòng Ngừa Rôm Sảy & Mồ Hôi Trộm Ban Đêm Cho Bé Yêu',
                'slug'          => 'chuyen-gia-huong-dan-phong-ngua-rom-say-va-mo-hoi-trom-ban-dem',
                'category_key'  => 'cham_soc_be',
                'category_name' => 'Chăm sóc bé',
                'badge_class'   => 'badge-sky',
                'image'         => '/FashionKids/images/magazine/mag-phong-ngua-rom-say-be.jpg',
                'author'        => 'BS. Nhi Khoa Minh Thảo',
                'author_role'   => 'Chuyên Gia Da Liễu Nhi Khoa',
                'read_time'     => '6 phút đọc',
                'published_at'  => '29/10/2026',
                'views'         => 1930,
                'likes'         => 384,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Giải pháp chọn đồ ngủ pijama sợi tre mỏng nhẹ kết hợp kiểm soát nhiệt độ và độ ẩm phòng ngủ giúp bé ngủ sâu giấc, không còn đổ mồ hôi lưng.',
                'tags'          => ['#MoHoiTrom', '#PhongNguaRomSay', '#GiacNguChoBe', '#PijamaSoiTre'],
                'related_product_ids' => [102],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Hệ thần kinh giao cảm điều hòa thân nhiệt ở trẻ dưới 4 tuổi chưa hoàn thiện, khiến bé rất dễ ra mồ hôi trộm ở vùng gáy và lưng trong 2 giờ đầu sau khi chìm vào giấc ngủ.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Quy tắc chọn đồ ngủ điều hòa thân nhiệt</h4>
                    <p class="text-slate">Ba mẹ nên tránh mặc đồ ngủ pha nilon/polyester cho bé. Thay vào đó, một bộ đồ ngủ <strong>100% sợi tre tự nhiên định lượng 180gsm</strong> sẽ tự động thấm hút mồ hôi ngay khi tiết ra và giữ ấm nhẹ nhàng khi nhiệt độ phòng hạ thấp về sáng.</p>
                '
            ],
            11 => [
                'id'            => 11,
                'title'         => 'Xu Hướng Phối Đồ Đôi Anh Chị Em Tone Pastel Unisex Thanh Lịch Chuẩn Hàn Quốc 2026',
                'slug'          => 'xu-huong-phoi-do-doi-anh-chi-em-tone-pastel-unisex-2026',
                'category_key'  => 'meo_phoi_do',
                'category_name' => 'Mẹo phối đồ',
                'badge_class'   => 'badge-peach',
                'image'         => '/FashionKids/images/magazine/mag-phoi-do-anh-em-unisex.jpg',
                'author'        => 'Stylist Bảo Ngọc',
                'author_role'   => 'Giám Đốc Sáng Tạo FashionKids',
                'read_time'     => '4 phút đọc',
                'published_at'  => '27/10/2026',
                'views'         => 1275,
                'likes'         => 239,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Bí quyết phối đồ đồng điệu (Sibling Matching Outfits) giữa bé trai và bé gái bằng bảng màu Xanh Mint, Kem Sữa và Đào Phấn mà không cần mặc giống hệt nhau.',
                'tags'          => ['#DoDoiAnhEm', '#PastelUnisex', '#SiblingLook', '#PhongCachHanQuoc'],
                'related_product_ids' => [101, 102, 104],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Chụp ảnh kỷ niệm gia đình có cả bé trai và bé gái sẽ trở nên gắn kết và nghệ thuật hơn rất nhiều khi mẹ áp dụng công thức phối đồ đồng điệu theo bảng màu (Color-Coordinated Sibling Look).</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Đồng điệu về chất liệu và sắc độ màu</h4>
                    <p class="text-slate">Anh trai có thể diện <strong>Bộ Thun Sợi Tre Khủng Long Xanh Mint</strong> kết hợp giày sandal thể thao, trong khi em gái diện <strong>Đầm Công Chúa Sợi Tre Kem Đào</strong> phối cùng chiếc kẹp tóc hoặc áo khoác Cardigan cùng tông Xanh Mint với anh trai.</p>
                '
            ],
            12 => [
                'id'            => 12,
                'title'         => '[Báo Chí Tiêu Dùng] FashionKids Vinh Dự Vào Top 10 Thương Hiệu Xanh An Toàn Cho Trẻ Em Việt Nam',
                'slug'          => 'bao-chi-tieu-dung-fashionkids-top-10-thuong-hieu-xanh-an-toan',
                'category_key'  => 'bao_chi',
                'category_name' => 'Báo chí & Sự kiện',
                'badge_class'   => 'badge-gold',
                'image'         => '/FashionKids/images/magazine/mag-bao-chi-thuong-hieu-xanh.jpg',
                'author'        => 'Ban Biên Tập Báo Chí',
                'author_role'   => 'Truyền Thông Thương Hiệu',
                'read_time'     => '3 phút đọc',
                'published_at'  => '25/10/2026',
                'views'         => 1490,
                'likes'         => 295,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Giải thưởng ghi nhận nỗ lực tiên phong ứng dụng sợi tre sinh thái tự phân hủy và bao bì giấy tái chế thân thiện với môi trường của FashionKids.',
                'tags'          => ['#ThuongHieuXanh', '#BaoChiTieuDung', '#ThoiTrangBenVung', '#EcoKids'],
                'related_product_ids' => [101, 102],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Tại Diễn đàn Tiêu dùng Xanh &amp; Sản phẩm An toàn cho Mẹ và Bé năm 2026, FashionKids đã được bình chọn vào Top 10 Thương hiệu Thời trang Trẻ em Bền vững được cộng đồng phụ huynh tin dùng nhất.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">100% Bao bì túi vải không dệt &amp; hộp giấy Kraft tái chế</h4>
                    <p class="text-slate">Không chỉ dừng lại ở chất liệu quần áo an toàn, mỗi đơn hàng giao đến tay ba mẹ đều được đóng gói trong túi vải tái sử dụng và hộp giấy sinh thái kèm hạt giống hoa hướng dương để bé tập trồng cây tại nhà.</p>
                '
            ],
            13 => [
                'id'            => 13,
                'title'         => 'Mẹo Chụp 3 Góc Ảnh Chân Dung & Toàn Thân Tại Nhà Giúp Bé Ghi Điểm Tuyệt Đối Vòng Casting KOL',
                'slug'          => 'meo-chup-3-goc-anh-chan-dung-va-toan-than-tai-nha-casting-kol',
                'category_key'  => 'hau_truong',
                'category_name' => 'Hậu trường mẫu nhí',
                'badge_class'   => 'badge-mint',
                'image'         => '/FashionKids/images/magazine/mag-bi-quyet-anh-casting-kol.jpg',
                'author'        => 'Lê Nguyễn Khánh Trình',
                'author_role'   => 'Đạo Diễn Hình Ảnh & Casting',
                'read_time'     => '5 phút đọc',
                'published_at'  => '22/10/2026',
                'views'         => 1620,
                'likes'         => 332,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Không cần máy ảnh chuyên nghiệp, ba mẹ vẫn có thể chụp bộ hồ sơ từ 2 đến 5 ảnh cực nét bằng điện thoại nhờ tận dụng ánh sáng cửa sổ tự nhiên.',
                'tags'          => ['#MeoChupAnhBe', '#HoSoCastingKOL', '#MauNhiTaiNang', '#GocChupDep'],
                'related_product_ids' => [101, 103],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Ban giám khảo cổng Tuyển chọn KOL Nhí luôn đánh giá cao những bức ảnh thể hiện rõ nét hồn nhiên, nụ cười tự nhiên và tỷ lệ vóc dáng thực tế của bé hơn là những bức ảnh chỉnh sửa quá đà.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Công thức 3 bức ảnh vàng trong hồ sơ ứng tuyển</h4>
                    <ul class="text-slate mb-4">
                        <li class="mb-2"><strong>Ảnh 1 (Chân dung cận mặt):</strong> Đặt máy ngang tầm mắt của bé, chụp cạnh cửa sổ vào khoảng 8h–10h sáng để đôi mắt bé có ánh sáng lấp lánh (catchlight).</li>
                        <li class="mb-2"><strong>Ảnh 2 (Toàn thân chính diện):</strong> Cho bé mặc trang phục gọn gàng, sáng màu để hội đồng chuyên môn dễ chọn size đồ thử tại Studio.</li>
                        <li class="mb-2"><strong>Ảnh 3 (Khoảnh khắc chuyển động tự nhiên):</strong> Chụp liên tục (Burst mode) khi bé đang cười đùa, xoay váy hoặc bước đi.</li>
                    </ul>
                '
            ],
            14 => [
                'id'            => 14,
                'title'         => '[Tiêu Điểm Chất Lượng] Tiêu Chuẩn Cúc Gỗ Sồi Bo Tròn & Chỉ May Hữu Cơ Không Chứa Formaldehyde',
                'slug'          => 'tieu-diem-chat-luong-tieu-chuan-cuc-go-soi-bo-tron-va-chi-may-huu-co',
                'category_key'  => 'bao_chi',
                'category_name' => 'Báo chí & Sự kiện',
                'badge_class'   => 'badge-gold',
                'image'         => '/FashionKids/images/magazine/mag-phu-lieu-cuc-go-an-toan.jpg',
                'author'        => 'Hồ Gia Bảo',
                'author_role'   => 'Quản Trị Chất Lượng FashionKids',
                'read_time'     => '4 phút đọc',
                'published_at'  => '20/10/2026',
                'views'         => 1180,
                'likes'         => 219,
                'status'        => 'xuat_ban',
                'is_featured'   => false,
                'summary'       => 'Khám phá quy trình kiểm định lực kéo đứt phụ liệu và kỹ thuật may giấu đường vắt sổ bên trong giúp trang phục FashionKids không bao giờ làm xước da bé.',
                'tags'          => ['#PhuLieuAnToan', '#CucGoTuNhien', '#KhongFormaldehyde', '#KiemDinhChatLuong'],
                'related_product_ids' => [104, 102],
                'content'       => '
                    <p class="lead font-weight-bold text-slate mb-4">Một bộ trang phục trẻ em đạt chuẩn quốc tế không chỉ nằm ở bề mặt vải chính mà còn được quyết định bởi sự an toàn tuyệt đối của từng chiếc cúc áo, khóa kéo và đường chỉ may tiếp xúc với da.</p>
                    <h4 class="font-weight-bold text-slate mt-4 mb-3">Kiểm tra lực kéo 90 Newton &amp; May lộn giấu đường chỉ</h4>
                    <p class="text-slate">Toàn bộ cúc gỗ tự nhiên trên áo Cardigan và đầm FashionKids đều được mài bo tròn cạnh vi điểm và vượt qua bài kiểm tra lực giật 90N để đảm bảo không bao giờ bung rời khi bé cầm nắm. Đường may sườn áo sử dụng chỉ tơ cotton mảnh kết hợp kỹ thuật may cuốn biên êm ái.</p>
                '
            ]
        ];
    }

    /**
     * Đồng bộ dữ liệu mẫu 14 bài viết vào CSDL tgb_bai_viet_tin_tuc cùng bộ ảnh bìa chuyên biệt
     */
    private function ensureSeedDataInDb(): void {
        if (!$this->hasConnection()) {
            return;
        }
        try {
            $count = (int)$this->db->query("SELECT COUNT(*) FROM `tgb_bai_viet_tin_tuc`")->fetchColumn();
            $oldImgCount = (int)$this->db->query("SELECT COUNT(*) FROM `tgb_bai_viet_tin_tuc` WHERE `ma_bai_viet` <= 14 AND (`anh_dai_dien` IS NULL OR `anh_dai_dien` NOT LIKE '/FashionKids/images/magazine/%' OR `noi_dung_chi_tiet` LIKE '%D2C%' OR `noi_dung_chi_tiet` LIKE '%MH 0%' OR `noi_dung_chi_tiet` LIKE '%MH 1%')")->fetchColumn();
            if ($count < 14 || $oldImgCount > 0) {
                $defaults = $this->getDefaultArticles();
                $stmt = $this->db->prepare("
                    INSERT INTO `tgb_bai_viet_tin_tuc`
                        (`ma_bai_viet`, `ma_tac_gia`, `tieu_de`, `duong_dan_tinh`, `anh_dai_dien`, `tom_tat_ngan`, `noi_dung_chi_tiet`, `so_luot_thich`, `so_luot_xem`, `trang_thai_bai_viet`, `ngay_dang`)
                    VALUES
                        (:id, 1, :title, :slug, :img, :summary, :content, :likes, :views, :status, NOW())
                    ON DUPLICATE KEY UPDATE
                        `tieu_de` = VALUES(`tieu_de`),
                        `anh_dai_dien` = VALUES(`anh_dai_dien`),
                        `tom_tat_ngan` = VALUES(`tom_tat_ngan`),
                        `noi_dung_chi_tiet` = VALUES(`noi_dung_chi_tiet`)
                ");
                foreach ($defaults as $art) {
                    $stmt->execute([
                        ':id'      => $art['id'],
                        ':title'   => $art['title'],
                        ':slug'    => $art['slug'],
                        ':img'     => $art['image'],
                        ':summary' => $art['summary'],
                        ':content' => $art['content'],
                        ':likes'   => $art['likes'],
                        ':views'   => $art['views'],
                        ':status'  => $art['status']
                    ]);
                }
            }

            $kolCount = (int)$this->db->query("SELECT COUNT(*) FROM `lnkt_dang_ky_kol_nhi`")->fetchColumn();
            $kolLegacyCount = (int)$this->db->query("SELECT COUNT(*) FROM `lnkt_dang_ky_kol_nhi` WHERE `ma_ho_so` <= 6 AND (`danh_sach_anh` IS NULL OR `danh_sach_anh` LIKE '%uploads/casting/%' OR `danh_sach_anh` NOT LIKE '%/FashionKids/images/kol/%')")->fetchColumn();
            $kolDefaults = $this->getDefaultKolCandidates();
            if ($kolCount < 6 || $kolLegacyCount > 0) {
                $kolStmt = $this->db->prepare("
                    INSERT INTO `lnkt_dang_ky_kol_nhi`
                        (`ma_ho_so`, `ma_nguoi_dung`, `ho_ten_phu_huynh`, `so_dien_thoai`, `thu_dien_tu`, `ho_ten_be`, `ngay_sinh_be`, `gioi_tinh_be`, `chieu_cao_be`, `can_nang_be`, `danh_sach_anh`, `nang_khieu_kinh_nghiem`, `trang_thai_duyet`)
                    VALUES
                        (:id, 3, :parent, :phone, :email, :baby, :dob, :gender, :h, :w, :photos, :talent, :status)
                    ON DUPLICATE KEY UPDATE
                        `danh_sach_anh` = IF(`danh_sach_anh` IS NULL OR `danh_sach_anh` LIKE '%uploads/casting/%' OR `danh_sach_anh` NOT LIKE '%/FashionKids/images/kol/%', VALUES(`danh_sach_anh`), `danh_sach_anh`)
                ");
                foreach ($kolDefaults as $kol) {
                    if ((int)$kol['id'] > 6) {
                        continue;
                    }
                    $kolStmt->execute([
                        ':id'     => $kol['id'],
                        ':parent' => $kol['guardian_name'],
                        ':phone'  => $kol['phone'],
                        ':email'  => 'kol' . $kol['id'] . '@fashionkids.vn',
                        ':baby'   => $kol['baby_name'],
                        ':dob'    => $kol['birth_date'],
                        ':gender' => $kol['gender'],
                        ':h'      => $kol['height_cm'],
                        ':w'      => $kol['weight_kg'],
                        ':photos' => json_encode($kol['photos'], JSON_UNESCAPED_SLASHES),
                        ':talent' => $kol['talent'],
                        ':status' => $kol['status']
                    ]);
                }
            }

            // Tự động đồng bộ 4 gương mặt Sao Nhí Catwalk mới (nếu CSDL chưa có hoặc bản ghi mẫu cũ chưa có Phong cách/Outfit chuẩn)
            $chkPhotoStmt = $this->db->prepare("SELECT `ma_ho_so`, `nang_khieu_kinh_nghiem`, `danh_sach_anh` FROM `lnkt_dang_ky_kol_nhi` WHERE `danh_sach_anh` LIKE :pat OR `ho_ten_be` = :bname LIMIT 1");
            $chkIdStmt    = $this->db->prepare("SELECT COUNT(*) FROM `lnkt_dang_ky_kol_nhi` WHERE `ma_ho_so` = :id");
            $updSeedStmt  = $this->db->prepare("UPDATE `lnkt_dang_ky_kol_nhi` SET `danh_sach_anh` = :photos, `nang_khieu_kinh_nghiem` = :talent, `trang_thai_duyet` = :status WHERE `ma_ho_so` = :id");
            $insWithId    = $this->db->prepare("
                INSERT INTO `lnkt_dang_ky_kol_nhi`
                    (`ma_ho_so`, `ma_nguoi_dung`, `ho_ten_phu_huynh`, `so_dien_thoai`, `thu_dien_tu`, `ho_ten_be`, `ngay_sinh_be`, `gioi_tinh_be`, `chieu_cao_be`, `can_nang_be`, `danh_sach_anh`, `nang_khieu_kinh_nghiem`, `trang_thai_duyet`)
                VALUES
                    (:id, 3, :parent, :phone, :email, :baby, :dob, :gender, :h, :w, :photos, :talent, :status)
            ");
            $insAutoId    = $this->db->prepare("
                INSERT INTO `lnkt_dang_ky_kol_nhi`
                    (`ma_nguoi_dung`, `ho_ten_phu_huynh`, `so_dien_thoai`, `thu_dien_tu`, `ho_ten_be`, `ngay_sinh_be`, `gioi_tinh_be`, `chieu_cao_be`, `can_nang_be`, `danh_sach_anh`, `nang_khieu_kinh_nghiem`, `trang_thai_duyet`)
                VALUES
                    (3, :parent, :phone, :email, :baby, :dob, :gender, :h, :w, :photos, :talent, :status)
            ");
            foreach ($kolDefaults as $kol) {
                if ((int)$kol['id'] <= 6) {
                    continue;
                }
                $baseImg = basename((string)$kol['image']);
                $chkPhotoStmt->execute([
                    ':pat'   => '%' . $baseImg . '%',
                    ':bname' => $kol['baby_name']
                ]);
                $existingRow = $chkPhotoStmt->fetch(PDO::FETCH_ASSOC);
                $chkPhotoStmt->closeCursor();
                $encodedPhotos = json_encode($kol['photos'], JSON_UNESCAPED_SLASHES);

                if ($existingRow) {
                    $curTalent = (string)($existingRow['nang_khieu_kinh_nghiem'] ?? '');
                    $curPhotos = (string)($existingRow['danh_sach_anh'] ?? '');
                    if (strpos($curTalent, 'Outfit:') === false || strpos($curPhotos, $baseImg) === false) {
                        $updSeedStmt->execute([
                            ':photos' => $encodedPhotos,
                            ':talent' => $kol['talent'],
                            ':status' => $kol['status'],
                            ':id'     => (int)$existingRow['ma_ho_so']
                        ]);
                    }
                    continue;
                }

                $chkIdStmt->execute([':id' => $kol['id']]);
                $idTaken = (int)$chkIdStmt->fetchColumn() > 0;
                $chkIdStmt->closeCursor();

                $params = [
                    ':parent' => $kol['guardian_name'],
                    ':phone'  => $kol['phone'],
                    ':email'  => 'kol' . $kol['id'] . '@fashionkids.vn',
                    ':baby'   => $kol['baby_name'],
                    ':dob'    => $kol['birth_date'],
                    ':gender' => $kol['gender'],
                    ':h'      => $kol['height_cm'],
                    ':w'      => $kol['weight_kg'],
                    ':photos' => $encodedPhotos,
                    ':talent' => $kol['talent'],
                    ':status' => $kol['status']
                ];
                if (!$idTaken) {
                    $params[':id'] = $kol['id'];
                    $insWithId->execute($params);
                } else {
                    $insAutoId->execute($params);
                }
            }

            // Tự động đồng bộ 2 Banner Sự Kiện mới vào bảng lnkt_banner_su_kien nếu chưa tồn tại
            $eventBannerSeeds = $this->getFeaturedEvents();
            $chkBnStmt = $this->db->prepare("SELECT COUNT(*) FROM `lnkt_banner_su_kien` WHERE `duong_dan_anh` LIKE :pat");
            $insBnStmt = $this->db->prepare("
                INSERT INTO `lnkt_banner_su_kien`
                    (`tieu_de`, `duong_dan_anh`, `duong_dan_dieu_huong`, `loai_su_kien`, `ngay_bat_dau`, `ngay_ket_thuc`, `dang_kich_hoat`, `thu_tu_hien_thi`)
                VALUES
                    (:title, :img, :url, :etype, :sdate, :edate, 1, :sorder)
            ");
            foreach ($eventBannerSeeds as $ev) {
                if (empty($ev['sync_to_banner_db'])) {
                    continue;
                }
                $imgBase = basename((string)$ev['image']);
                $chkBnStmt->execute([':pat' => '%' . $imgBase . '%']);
                $bnExists = (int)$chkBnStmt->fetchColumn() > 0;
                $chkBnStmt->closeCursor();
                if (!$bnExists) {
                    $insBnStmt->execute([
                        ':title'  => $ev['title'],
                        ':img'    => $ev['image'],
                        ':url'    => $ev['target_url'],
                        ':etype'  => $ev['db_type'],
                        ':sdate'  => $ev['db_start'],
                        ':edate'  => $ev['db_end'],
                        ':sorder' => $ev['sort_order']
                    ]);
                }
            }
        } catch (PDOException $e) {
            // Bỏ qua nếu chưa có bảng
        }
    }

    /**
     * Suy luận chuyên mục từ tiêu đề/slug hoặc dữ liệu mặc định
     */
    private function enrichArticleMeta(array $row, array $defaults): array {
        $id = (int)($row['ma_bai_viet'] ?? $row['id'] ?? 1);
        $def = $defaults[$id] ?? null;

        $title = $row['tieu_de'] ?? ($def['title'] ?? 'Bài viết Tạp chí FashionKids');
        $lowerTitle = mb_strtolower($title, 'UTF-8');

        $catKey = $def['category_key'] ?? 'meo_phoi_do';
        $catName = $def['category_name'] ?? 'Mẹo phối đồ';
        $badgeClass = $def['badge_class'] ?? 'badge-peach';

        if (!$def) {
            if (strpos($lowerTitle, 'hậu trường') !== false || strpos($lowerTitle, 'kol') !== false || strpos($lowerTitle, 'lookbook') !== false || strpos($lowerTitle, 'catwalk') !== false) {
                $catKey = 'hau_truong';
                $catName = 'Hậu trường mẫu nhí';
                $badgeClass = 'badge-mint';
            } elseif (strpos($lowerTitle, 'sợi tre') !== false || strpos($lowerTitle, 'chăm sóc') !== false || strpos($lowerTitle, 'giặt') !== false || strpos($lowerTitle, 'da ') !== false || strpos($lowerTitle, 'rôm sảy') !== false || strpos($lowerTitle, 'uv') !== false) {
                $catKey = 'cham_soc_be';
                $catName = 'Chăm sóc bé';
                $badgeClass = 'badge-sky';
            } elseif (strpos($lowerTitle, 'báo chí') !== false || strpos($lowerTitle, 'sự kiện') !== false || strpos($lowerTitle, 'chứng nhận') !== false || strpos($lowerTitle, 'tiêu điểm') !== false) {
                $catKey = 'bao_chi';
                $catName = 'Báo chí & Sự kiện';
                $badgeClass = 'badge-gold';
            }
        }

        // Ưu tiên bộ ảnh bìa chuyên biệt trong /FashionKids/images/magazine/ cho 14 bài viết chuẩn
        if ($def && !empty($def['image'])) {
            $img = $def['image'];
        } else {
            $img = $row['anh_dai_dien'] ?? '/FashionKids/images/magazine/mag-phoi-do-du-tiec.jpg';
            if (strpos($img, '/') !== 0 && strpos($img, 'http') !== 0) {
                $img = '/FashionKids/images/' . $img;
            }
            if ($img === '/FashionKids/images/blog_be_gai_tiec.jpg') {
                $img = '/FashionKids/images/magazine/mag-phoi-do-du-tiec.jpg';
            }
        }

        $likedList = $_SESSION['fk_liked_articles'] ?? [];
        $isLiked = in_array($id, $likedList, true);
        $bookmarkedList = $_SESSION['fk_bookmarked_articles'] ?? [];
        $isBookmarked = in_array($id, $bookmarkedList, true);

        return [
            'id'                  => $id,
            'title'               => $title,
            'slug'                => $row['duong_dan_tinh'] ?? ($def['slug'] ?? ('bai-viet-' . $id)),
            'category_key'        => $catKey,
            'category_name'       => $catName,
            'badge_class'         => $badgeClass,
            'image'               => $img,
            'author'              => $def['author'] ?? 'Ban Biên Tập FashionKids',
            'author_role'         => $def['author_role'] ?? 'Chuyên Gia Thời Trang Trẻ Em',
            'read_time'           => $def['read_time'] ?? '4 phút đọc',
            'published_at'        => !empty($row['ngay_dang']) ? date('d/m/Y', strtotime($row['ngay_dang'])) : ($def['published_at'] ?? date('d/m/Y')),
            'views'               => max((int)($row['so_luot_xem'] ?? 0), (int)($def['views'] ?? 120)),
            'likes'               => max((int)($row['so_luot_thich'] ?? 0), (int)($def['likes'] ?? 25)),
            'status'              => $row['trang_thai_bai_viet'] ?? ($def['status'] ?? 'xuat_ban'),
            'is_featured'         => ($id === 1),
            'is_liked'            => $isLiked,
            'is_bookmarked'       => $isBookmarked,
            'summary'             => $row['tom_tat_ngan'] ?? ($def['summary'] ?? ''),
            'content'             => $row['noi_dung_chi_tiet'] ?? ($def['content'] ?? ''),
            'tags'                => $def['tags'] ?? ['#FashionKids', '#VaiSoiTre', '#ThoiTrangChoBe'],
            'related_product_ids' => $def['related_product_ids'] ?? [101, 102]
        ];
    }

    /**
     * Lấy danh sách bài viết tạp chí có lọc theo chuyên mục, từ khóa và sắp xếp
     */
    public function getArticles(string $category = 'all', string $keyword = '', string $sort = 'newest', bool $includeDrafts = false): array {
        $defaults = $this->getDefaultArticles();
        $articlesMap = [];

        foreach ($defaults as $id => $art) {
            $articlesMap[$id] = $this->enrichArticleMeta($art, $defaults);
        }

        if ($this->hasConnection()) {
            try {
                $sql = "SELECT * FROM `tgb_bai_viet_tin_tuc` ORDER BY `ma_bai_viet` DESC";
                $rows = $this->db->query($sql)->fetchAll();
                foreach ($rows as $r) {
                    $id = (int)$r['ma_bai_viet'];
                    $articlesMap[$id] = $this->enrichArticleMeta($r, $defaults);
                }
            } catch (PDOException $e) {}
        }

        // Bổ sung bài viết mới tạo trong session (nếu có)
        if (!empty($_SESSION['fk_custom_articles']) && is_array($_SESSION['fk_custom_articles'])) {
            foreach ($_SESSION['fk_custom_articles'] as $id => $cArt) {
                $articlesMap[(int)$id] = $cArt;
            }
        }

        // Đồng bộ trạng thái xuất bản / xóa trong phiên (dành cho Admin MH 21/22)
        if (!empty($_SESSION['fk_published_articles']) && is_array($_SESSION['fk_published_articles'])) {
            foreach ($_SESSION['fk_published_articles'] as $pubId => $pubStatus) {
                $pid = (int)$pubId;
                if (isset($articlesMap[$pid])) {
                    $articlesMap[$pid]['status'] = (string)$pubStatus;
                }
            }
        }
        if (!empty($_SESSION['fk_deleted_articles']) && is_array($_SESSION['fk_deleted_articles'])) {
            foreach ($_SESSION['fk_deleted_articles'] as $delId) {
                unset($articlesMap[(int)$delId]);
            }
        }

        $result = [];
        $kwLower = mb_strtolower(trim($keyword), 'UTF-8');

        foreach ($articlesMap as $art) {
            if (!$includeDrafts && ($art['status'] ?? 'xuat_ban') !== 'xuat_ban') {
                continue;
            }
            if ($category !== 'all' && $category !== '' && $art['category_key'] !== $category) {
                continue;
            }
            if ($kwLower !== '') {
                $haystack = mb_strtolower($art['title'] . ' ' . $art['summary'] . ' ' . $art['category_name'] . ' ' . implode(' ', $art['tags']), 'UTF-8');
                if (mb_strpos($haystack, $kwLower) === false) {
                    continue;
                }
            }
            $result[] = $art;
        }

        usort($result, function ($a, $b) use ($sort) {
            if ($sort === 'popular' || $sort === 'views') {
                return $b['views'] <=> $a['views'];
            }
            if ($sort === 'likes') {
                return $b['likes'] <=> $a['likes'];
            }
            // Bài viết mới xuất bản từ CMS (id > 14) ưu tiên lên đầu, các bài chuẩn 1..14 xếp theo thứ tự ngày đăng mới nhất (1 -> 14)
            $isCustomA = ($a['id'] > 14) ? 1 : 0;
            $isCustomB = ($b['id'] > 14) ? 1 : 0;
            if ($isCustomA !== $isCustomB) {
                return $isCustomB <=> $isCustomA;
            }
            if ($isCustomA === 1) {
                return $b['id'] <=> $a['id'];
            }
            return $a['id'] <=> $b['id'];
        });

        return $result;
    }

    /**
     * Lấy chi tiết 1 bài viết và tự động +1 lượt xem realtime
     */
    public function getArticleById(int $id, bool $incrementView = true): array {
        $all = $this->getArticles('all', '', 'newest', true);
        $found = null;
        foreach ($all as $art) {
            if ((int)$art['id'] === $id) {
                $found = $art;
                break;
            }
        }
        if (!$found) {
            $found = reset($all);
            $id = (int)$found['id'];
        }

        if ($incrementView) {
            $found['views']++;
            if ($this->hasConnection()) {
                try {
                    $stmt = $this->db->prepare("UPDATE `tgb_bai_viet_tin_tuc` SET `so_luot_xem` = GREATEST(`so_luot_xem`, :base) + 1 WHERE `ma_bai_viet` = :id");
                    $stmt->execute([':base' => $found['views'] - 1, ':id' => $id]);
                } catch (PDOException $e) {}
            }
        }

        return $found;
    }

    /**
     * Thả tim / Bỏ tim bài viết tạp chí Realtime
     */
    public function toggleLikeArticle(int $id): array {
        if (!isset($_SESSION['fk_liked_articles']) || !is_array($_SESSION['fk_liked_articles'])) {
            $_SESSION['fk_liked_articles'] = [];
        }
        $art = $this->getArticleById($id, false);
        $isLiked = in_array($id, $_SESSION['fk_liked_articles'], true);

        if ($isLiked) {
            $_SESSION['fk_liked_articles'] = array_values(array_diff($_SESSION['fk_liked_articles'], [$id]));
            $newLikes = max(0, $art['likes'] - 1);
            $likedNow = false;
        } else {
            $_SESSION['fk_liked_articles'][] = $id;
            $newLikes = $art['likes'] + 1;
            $likedNow = true;
        }

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("UPDATE `tgb_bai_viet_tin_tuc` SET `so_luot_thich` = :likes WHERE `ma_bai_viet` = :id");
                $stmt->execute([':likes' => $newLikes, ':id' => $id]);
            } catch (PDOException $e) {}
        }

        return [
            'article_id' => $id,
            'likes'      => $newLikes,
            'is_liked'   => $likedNow
        ];
    }

    /**
     * Lưu / Bỏ lưu bài viết yêu thích (Bookmark)
     */
    public function toggleBookmarkArticle(int $id): array {
        if (!isset($_SESSION['fk_bookmarked_articles']) || !is_array($_SESSION['fk_bookmarked_articles'])) {
            $_SESSION['fk_bookmarked_articles'] = [];
        }
        $isSaved = in_array($id, $_SESSION['fk_bookmarked_articles'], true);
        if ($isSaved) {
            $_SESSION['fk_bookmarked_articles'] = array_values(array_diff($_SESSION['fk_bookmarked_articles'], [$id]));
            $savedNow = false;
        } else {
            $_SESSION['fk_bookmarked_articles'][] = $id;
            $savedNow = true;
        }
        return [
            'article_id'    => $id,
            'is_bookmarked' => $savedNow,
            'total_saved'   => count($_SESSION['fk_bookmarked_articles'])
        ];
    }

    /**
     * Lấy danh sách bình luận đa cấp của bài viết (tgb_binh_luan_bai_viet)
     */
    public function getCommentsByArticle(int $articleId): array {
        $comments = [
            1 => [
                'id'          => 1,
                'article_id'  => $articleId,
                'parent_id'   => null,
                'author_name' => 'Võ Thị Thanh Thảo',
                'author_role' => 'khach_hang',
                'vip_badge'   => 'Hạng Vàng (-10%)',
                'content'     => 'Bài viết rất hữu ích! Mình vừa áp dụng cách phối váy sợi tre nơ hồng pastel với giày sandal đế cao su non cho bé đi tiệc cuối tuần, ai cũng khen bé xinh và thoáng mát.',
                'created_at'  => '2 giờ trước',
                'replies'     => [
                    [
                        'id'          => 2,
                        'article_id'  => $articleId,
                        'parent_id'   => 1,
                        'author_name' => 'Ban Biên Tập FashionKids',
                        'author_role' => 'quan_tri',
                        'vip_badge'   => 'Quản Trị Viên',
                        'content'     => 'Dạ FashionKids cảm ơn chị Thanh Thảo rất nhiều ạ! Chúc bé luôn khỏe mạnh và tỏa sáng trong các thiết kế sợi tre kháng khuẩn của shop nhé!',
                        'created_at'  => '1 giờ trước'
                    ]
                ]
            ],
            3 => [
                'id'          => 3,
                'article_id'  => $articleId,
                'parent_id'   => null,
                'author_name' => 'Trần Minh Anh',
                'author_role' => 'khach_hang',
                'vip_badge'   => 'Hạng Bạc (-5%)',
                'content'     => 'Từ ngày chuyển sang mặc bộ thun sợi tre của FashionKids, bé nhà mình ngủ ngon hẳn, không còn bị mồ hôi trộm ướt lưng nữa.',
                'created_at'  => '30 phút trước',
                'replies'     => []
            ]
        ];

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    SELECT b.*, u.ho_va_ten, u.vai_tro
                    FROM `tgb_binh_luan_bai_viet` b
                    LEFT JOIN `hgb_nguoi_dung` u ON b.ma_nguoi_dung = u.ma_nguoi_dung
                    WHERE b.ma_bai_viet = :aid
                    ORDER BY b.ma_binh_luan ASC
                ");
                $stmt->execute([':aid' => $articleId]);
                $rows = $stmt->fetchAll();

                if (!empty($rows)) {
                    $dbTree = [];
                    foreach ($rows as $r) {
                        $cid = (int)$r['ma_binh_luan'];
                        $pid = !empty($r['ma_binh_luan_cha']) ? (int)$r['ma_binh_luan_cha'] : null;
                        $role = $r['vai_tro'] ?? 'khach_hang';
                        $item = [
                            'id'          => $cid,
                            'article_id'  => (int)$r['ma_bai_viet'],
                            'parent_id'   => $pid,
                            'author_name' => $r['ho_va_ten'] ?: 'Phụ huynh FashionKids',
                            'author_role' => $role,
                            'vip_badge'   => ($role === 'quan_tri' || $role === 'nhan_vien') ? 'Quản Trị Viên' : 'Thành Viên VIP',
                            'content'     => $r['noi_dung_binh_luan'],
                            'created_at'  => !empty($r['ngay_binh_luan']) ? date('d/m/Y H:i', strtotime($r['ngay_binh_luan'])) : 'Vừa xong',
                            'replies'     => []
                        ];
                        if ($pid === null) {
                            $dbTree[$cid] = $item;
                        } elseif (isset($dbTree[$pid])) {
                            $dbTree[$pid]['replies'][] = $item;
                        } else {
                            $dbTree[$cid] = $item;
                        }
                    }
                    // Gộp bình luận mẫu số 3 nếu DB mới có 2 bình luận đầu
                    if (count($dbTree) < 2 && isset($comments[3])) {
                        $dbTree[999] = $comments[3];
                    }
                    $comments = $dbTree;
                }
            } catch (PDOException $e) {}
        }

        // Bổ sung bình luận lưu trong session
        if (!empty($_SESSION['fk_blog_comments'][$articleId])) {
            foreach ($_SESSION['fk_blog_comments'][$articleId] as $sCom) {
                $pid = $sCom['parent_id'];
                if ($pid && isset($comments[$pid])) {
                    $comments[$pid]['replies'][] = $sCom;
                } else {
                    $comments[$sCom['id']] = $sCom;
                }
            }
        }

        return array_values($comments);
    }

    /**
     * Thêm bình luận hoặc phản hồi mới vào bài viết tạp chí
     */
    public function addComment(int $articleId, string $content, ?int $parentId = null, ?string $customAuthor = null): array {
        $userId = isset($_SESSION['user']['ma_nguoi_dung']) ? (int)$_SESSION['user']['ma_nguoi_dung'] : 3;
        $authorName = $customAuthor ?: ($_SESSION['user']['ho_va_ten'] ?? 'Phụ Huynh Thành Viên');
        $role = $_SESSION['user']['vai_tro'] ?? 'khach_hang';
        $vipBadge = ($role === 'quan_tri' || $role === 'nhan_vien') ? 'Quản Trị Viên' : ($_SESSION['user']['ten_hang'] ?? 'Thành Viên VIP');

        $newId = time() % 100000;
        $savedToDb = false;

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO `tgb_binh_luan_bai_viet` (`ma_bai_viet`, `ma_nguoi_dung`, `ma_binh_luan_cha`, `noi_dung_binh_luan`, `ngay_binh_luan`)
                    VALUES (:aid, :uid, :pid, :content, NOW())
                ");
                $stmt->execute([
                    ':aid'     => $articleId,
                    ':uid'     => $userId,
                    ':pid'     => $parentId ?: null,
                    ':content' => $content
                ]);
                $newId = (int)$this->db->lastInsertId();
                $savedToDb = true;
            } catch (PDOException $e) {}
        }

        $newComment = [
            'id'          => $newId,
            'article_id'  => $articleId,
            'parent_id'   => $parentId ?: null,
            'author_name' => $authorName,
            'author_role' => $role,
            'vip_badge'   => $vipBadge,
            'content'     => $content,
            'created_at'  => 'Vừa xong (' . date('H:i') . ')',
            'replies'     => []
        ];

        if (!$savedToDb) {
            $_SESSION['fk_blog_comments'][$articleId][] = $newComment;
        }

        return $newComment;
    }

    /**
     * Đăng bài viết mới / Cập nhật bài viết từ CMS (admin-cms-blog.php)
     */
    public function saveArticle(array $payload): array {
        $title    = trim($payload['title'] ?? '');
        $catKey   = trim($payload['category_key'] ?? 'meo_phoi_do');
        $summary  = trim($payload['summary'] ?? '');
        $content  = trim($payload['content'] ?? '');
        $status   = trim($payload['status'] ?? 'xuat_ban');
        $image    = trim($payload['image'] ?? '/FashionKids/images/vay-cong-chua-no-pastel.jpg');

        $catNames = [
            'meo_phoi_do' => ['name' => 'Mẹo phối đồ', 'badge' => 'badge-peach'],
            'hau_truong'  => ['name' => 'Hậu trường mẫu nhí', 'badge' => 'badge-mint'],
            'cham_soc_be' => ['name' => 'Chăm sóc bé', 'badge' => 'badge-sky'],
            'bao_chi'     => ['name' => 'Báo chí & Sự kiện', 'badge' => 'badge-gold']
        ];
        $meta = $catNames[$catKey] ?? $catNames['meo_phoi_do'];
        $slug = 'bai-viet-' . time();
        $newId = count($this->getDefaultArticles()) + rand(10, 99);

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO `tgb_bai_viet_tin_tuc`
                        (`ma_tac_gia`, `tieu_de`, `duong_dan_tinh`, `anh_dai_dien`, `tom_tat_ngan`, `noi_dung_chi_tiet`, `so_luot_thich`, `so_luot_xem`, `trang_thai_bai_viet`, `ngay_dang`)
                    VALUES
                        (1, :title, :slug, :img, :summary, :content, 18, 125, :status, NOW())
                ");
                $stmt->execute([
                    ':title'   => $title,
                    ':slug'    => $slug,
                    ':img'     => $image,
                    ':summary' => $summary,
                    ':content' => '<p>' . nl2br(htmlspecialchars($content)) . '</p>',
                    ':status'  => $status
                ]);
                $newId = (int)$this->db->lastInsertId();
            } catch (PDOException $e) {}
        }

        $articleObj = [
            'id'                  => $newId,
            'title'               => $title,
            'slug'                => $slug,
            'category_key'        => $catKey,
            'category_name'       => $meta['name'],
            'badge_class'         => $meta['badge'],
            'image'               => $image,
            'author'              => $_SESSION['user']['ho_va_ten'] ?? 'Ban Biên Tập FashionKids',
            'author_role'         => 'Biên Tập Viên Tạp Chí',
            'read_time'           => '4 phút đọc',
            'published_at'        => date('d/m/Y'),
            'views'               => 125,
            'likes'               => 18,
            'status'              => $status,
            'is_featured'         => false,
            'is_liked'            => false,
            'is_bookmarked'       => false,
            'summary'             => $summary,
            'content'             => '<p>' . nl2br(htmlspecialchars($content)) . '</p>',
            'tags'                => ['#FashionKids', '#TapChiMauNhi', '#2026'],
            'related_product_ids' => [101, 102]
        ];

        $_SESSION['fk_custom_articles'][$newId] = $articleObj;
        return $articleObj;
    }

    /**
     * Xuất bản bài viết từ Bản nháp (ban_nhap -> xuat_ban) - CHỈ ADMIN (MH 21/22)
     */
    public function publishArticle(int $id): array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("UPDATE `tgb_bai_viet_tin_tuc` SET `trang_thai_bai_viet` = 'xuat_ban', `ngay_dang` = NOW() WHERE `ma_bai_viet` = :id");
                $stmt->execute([':id' => $id]);
                $stmt->closeCursor();
            } catch (PDOException $e) {}
        }

        $_SESSION['fk_published_articles'][$id] = 'xuat_ban';
        if (isset($_SESSION['fk_custom_articles'][$id])) {
            $_SESSION['fk_custom_articles'][$id]['status'] = 'xuat_ban';
        }

        return [
            'id'           => $id,
            'status'       => 'xuat_ban',
            'status_label' => 'Đã xuất bản',
            'published_at' => date('d/m/Y')
        ];
    }

    /**
     * Xóa / Ẩn bài viết Tạp chí khỏi hệ thống - CHỈ ADMIN (MH 21/22)
     */
    public function deleteArticle(int $id): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("DELETE FROM `tgb_bai_viet_tin_tuc` WHERE `ma_bai_viet` = :id");
                $stmt->execute([':id' => $id]);
                $stmt->closeCursor();
            } catch (PDOException $e) {}
        }

        if (!isset($_SESSION['fk_deleted_articles']) || !is_array($_SESSION['fk_deleted_articles'])) {
            $_SESSION['fk_deleted_articles'] = [];
        }
        if (!in_array($id, $_SESSION['fk_deleted_articles'], true)) {
            $_SESSION['fk_deleted_articles'][] = $id;
        }
        unset($_SESSION['fk_custom_articles'][$id]);

        return true;
    }

    /**
     * ========================================================================
     * PHÂN HỆ 2: TIN NHẮN TƯ VẤN TRỰC TUYẾN REALTIME (tgb_tin_nhan_tu_van)
     * ========================================================================
     */
    public function getChatSessions(): array {
        $baseSeedTs = strtotime('2026-01-01 09:00:00') ?: 1767232800;
        $sessions = [
            'sess_guest_123456' => [
                'session_id'    => 'sess_guest_123456',
                'customer_name' => 'Võ Thị Thanh Thảo',
                'vip_label'     => 'VIP Hạng Vàng (-10%)',
                'badge_class'   => 'badge-gold',
                'online'        => true,
                'last_message'  => 'Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa size mấy thì vừa ạ?',
                'last_time'     => 'Vừa xong',
                'unread'        => 1,
                'sort_ts'       => $baseSeedTs + 300,
                'max_id'        => 3
            ],
            'sess_vip_me_chip' => [
                'session_id'    => 'sess_vip_me_chip',
                'customer_name' => 'Trần Lan Anh',
                'vip_label'     => 'VIP Kim Cương (-15%)',
                'badge_class'   => 'badge-mint',
                'online'        => true,
                'last_message'  => 'Kiểm tra giúp chị đơn hàng #1085 giao về Bình Thạnh tới đâu rồi em nhé?',
                'last_time'     => '5 phút trước',
                'unread'        => 2,
                'sort_ts'       => $baseSeedTs + 200,
                'max_id'        => 2
            ],
            'sess_kol_casting' => [
                'session_id'    => 'sess_kol_casting',
                'customer_name' => 'Phụ Huynh Bé Minh Khôi',
                'vip_label'     => 'Ứng Viên KOL Nhí',
                'badge_class'   => 'badge-sky',
                'online'        => false,
                'last_message'  => 'Lịch chụp Lookbook Studio đợt 1 vào sáng Chủ Nhật tuần này đúng không shop?',
                'last_time'     => '18 phút trước',
                'unread'        => 0,
                'sort_ts'       => $baseSeedTs + 100,
                'max_id'        => 1
            ]
        ];

        $dbConnected = false;
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->query("
                    SELECT `ma_phien_khach`,
                           MAX(`ngay_gui`) AS last_time,
                           MAX(`ma_tin_nhan`) AS max_id,
                           MAX(CASE WHEN `nguoi_gui` = 'khach_hang' THEN `ma_nguoi_dung` ELSE NULL END) AS cust_uid,
                           COUNT(*) AS total_msg,
                           SUM(CASE WHEN `da_xem` = 0 AND `nguoi_gui` = 'khach_hang' THEN 1 ELSE 0 END) AS unread_cnt
                    FROM `tgb_tin_nhan_tu_van`
                    GROUP BY `ma_phien_khach`
                    ORDER BY last_time DESC, max_id DESC
                ");
                $rows = $stmt->fetchAll();
                $stmt->closeCursor();
                $dbConnected = true;

                // Tra cứu tên & hạng VIP thật của khách hàng từ bảng hgb_nguoi_dung
                $userMap = [];
                $uidSet = [];
                foreach ($rows as $r) {
                    $cid = !empty($r['cust_uid']) ? (int)$r['cust_uid'] : 0;
                    if ($cid <= 0 && preg_match('/^sess_user_(\d+)$/', (string)$r['ma_phien_khach'], $m)) {
                        $cid = (int)$m[1];
                    }
                    if ($cid > 0) {
                        $uidSet[$cid] = $cid;
                    }
                }
                if (!empty($uidSet)) {
                    try {
                        $inPlaceholders = implode(',', array_map('intval', array_values($uidSet)));
                        $uStmt = $this->db->query("
                            SELECT u.`ma_nguoi_dung`, u.`ho_va_ten`, u.`thu_dien_tu`,
                                   COALESCE(h.`ten_hang`, 'Đồng (Bronze)') AS ten_hang,
                                   COALESCE(h.`phan_tram_giam_gia`, 0) AS phan_tram_giam_gia
                            FROM `hgb_nguoi_dung` u
                            LEFT JOIN `hgb_hang_thanh_vien` h ON u.`ma_hang_thanh_vien` = h.`ma_hang`
                            WHERE u.`ma_nguoi_dung` IN ({$inPlaceholders})
                        ");
                        foreach ($uStmt->fetchAll() as $ur) {
                            $userMap[(int)$ur['ma_nguoi_dung']] = $ur;
                        }
                        $uStmt->closeCursor();
                    } catch (PDOException $ue) {}
                }

                foreach ($rows as $r) {
                    $sid = (string)$r['ma_phien_khach'];
                    $lastStmt = $this->db->prepare("SELECT `noi_dung_tin_nhan`, `nguoi_gui`, `ngay_gui` FROM `tgb_tin_nhan_tu_van` WHERE `ma_phien_khach` = :sid ORDER BY `ma_tin_nhan` DESC LIMIT 1");
                    $lastStmt->execute([':sid' => $sid]);
                    $lastRow = $lastStmt->fetch();
                    $lastStmt->closeCursor();

                    $cid = !empty($r['cust_uid']) ? (int)$r['cust_uid'] : 0;
                    if ($cid <= 0 && preg_match('/^sess_user_(\d+)$/', $sid, $m)) {
                        $cid = (int)$m[1];
                    }
                    $uInfo = $cid > 0 ? ($userMap[$cid] ?? null) : null;

                    $lastPreview = (string)($lastRow['noi_dung_tin_nhan'] ?? '');
                    if (($lastRow['nguoi_gui'] ?? '') === 'nhan_vien' && $lastPreview !== '') {
                        $lastPreview = 'Shop: ' . $lastPreview;
                    }
                    $sortTs = !empty($lastRow['ngay_gui']) ? (strtotime((string)$lastRow['ngay_gui']) ?: time()) : time();
                    $timeDisplay = !empty($lastRow['ngay_gui']) ? date('H:i', strtotime((string)$lastRow['ngay_gui'])) : 'Vừa xong';

                    if (!isset($sessions[$sid])) {
                        $custName = $uInfo ? (string)$uInfo['ho_va_ten'] : ('Khách trực tuyến #' . substr($sid, -5));
                        $vipPct   = $uInfo ? (float)($uInfo['phan_tram_giam_gia'] ?? 0) : 0.0;
                        $vipName  = $uInfo ? (string)$uInfo['ten_hang'] : 'Khách Trực Tuyến';
                        $vipLabel = $uInfo
                            ? ($vipPct > 0 ? ($vipName . ' (-' . rtrim(rtrim((string)$vipPct, '0'), '.') . '%)') : 'Thành viên ' . $vipName)
                            : 'Khách Ghé Thăm';
                        $badgeCls = $vipPct >= 15 ? 'badge-mint' : ($vipPct >= 10 ? 'badge-gold' : ($uInfo ? 'badge-sky' : 'badge-peach'));

                        $sessions[$sid] = [
                            'session_id'    => $sid,
                            'customer_name' => $custName,
                            'vip_label'     => $vipLabel,
                            'badge_class'   => $badgeCls,
                            'online'        => true,
                            'last_message'  => $lastPreview,
                            'last_time'     => $timeDisplay,
                            'unread'        => (int)$r['unread_cnt'],
                            'sort_ts'       => $sortTs,
                            'max_id'        => (int)($r['max_id'] ?? 0)
                        ];
                    } else {
                        if ($lastRow) {
                            $sessions[$sid]['last_message'] = $lastPreview;
                            $sessions[$sid]['last_time']    = $timeDisplay;
                            $sessions[$sid]['sort_ts']      = $sortTs;
                            $sessions[$sid]['max_id']       = (int)($r['max_id'] ?? 0);
                        }
                        if ($uInfo && $sid !== 'sess_guest_123456') {
                            $sessions[$sid]['customer_name'] = (string)$uInfo['ho_va_ten'];
                        }
                        $sessions[$sid]['unread'] = (int)$r['unread_cnt'];
                    }
                }
            } catch (PDOException $e) {}
        }

        // Chỉ áp dụng cache đọc trong session khi không có DB (demo offline)
        if (!$dbConnected && !empty($_SESSION['fk_chat_read_sessions']) && is_array($_SESSION['fk_chat_read_sessions'])) {
            foreach ($_SESSION['fk_chat_read_sessions'] as $readSid => $isRead) {
                if ($isRead && isset($sessions[$readSid])) {
                    $sessions[$readSid]['unread'] = 0;
                }
            }
        }

        $list = array_values($sessions);
        usort($list, static function (array $a, array $b): int {
            $cmp = ((int)($b['sort_ts'] ?? 0)) <=> ((int)($a['sort_ts'] ?? 0));
            if ($cmp !== 0) {
                return $cmp;
            }
            return ((int)($b['max_id'] ?? 0)) <=> ((int)($a['max_id'] ?? 0));
        });

        return $list;
    }

    /**
     * Đánh dấu toàn bộ tin nhắn của khách hàng trong phiên chat là Đã đọc (tgb_danh_dau_tin_da_doc) - MH 11
     */
    public function markChatSessionAsRead(string $sessionId): int {
        $updatedRows = 0;
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `tgb_danh_dau_tin_da_doc`(:sid)");
                $stmt->execute([':sid' => $sessionId]);
                $stmt->closeCursor();
                $updatedRows = 1;
            } catch (PDOException $e) {
                try {
                    $stmt2 = $this->db->prepare("
                        UPDATE `tgb_tin_nhan_tu_van`
                        SET `da_xem` = 1
                        WHERE `ma_phien_khach` = :sid AND `nguoi_gui` = 'khach_hang' AND `da_xem` = 0
                    ");
                    $stmt2->execute([':sid' => $sessionId]);
                    $updatedRows = $stmt2->rowCount();
                    $stmt2->closeCursor();
                } catch (PDOException $e2) {}
            }
        }

        if (!isset($_SESSION['fk_chat_read_sessions']) || !is_array($_SESSION['fk_chat_read_sessions'])) {
            $_SESSION['fk_chat_read_sessions'] = [];
        }
        $_SESSION['fk_chat_read_sessions'][$sessionId] = true;

        return $updatedRows;
    }

    public function getChatMessages(string $sessionId = 'sess_guest_123456'): array {
        $defaultSeed = [
            'sess_guest_123456' => [
                [
                    'id'         => 1,
                    'session_id' => 'sess_guest_123456',
                    'sender'     => 'nhan_vien',
                    'content'    => 'Xin chào ba mẹ! Em là Chuyên viên tư vấn thời trang sợi tre FashionKids. Ba mẹ cần hỗ trợ chọn size cho bé hay kiểm tra đơn hàng ạ?',
                    'time'       => '09:15'
                ],
                [
                    'id'         => 2,
                    'session_id' => 'sess_guest_123456',
                    'sender'     => 'khach_hang',
                    'content'    => 'Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa size mấy thì vừa ạ?',
                    'time'       => '09:16'
                ],
                [
                    'id'         => 3,
                    'session_id' => 'sess_guest_123456',
                    'sender'     => 'nhan_vien',
                    'content'    => 'Dạ chào chị Thanh Thảo, với chiều cao 92cm và cân nặng 13kg, bé mặc Size 2 (2-3T, khung chuẩn 90-100cm) là vừa vặn và thoải mái nhất ạ!',
                    'time'       => '09:17'
                ]
            ],
            'sess_vip_me_chip' => [
                [
                    'id'         => 10,
                    'session_id' => 'sess_vip_me_chip',
                    'sender'     => 'khach_hang',
                    'content'    => 'Kiểm tra giúp chị đơn hàng #1085 giao về Bình Thạnh tới đâu rồi em nhé?',
                    'time'       => '09:20'
                ],
                [
                    'id'         => 11,
                    'session_id' => 'sess_vip_me_chip',
                    'sender'     => 'nhan_vien',
                    'content'    => 'Dạ đơn hàng #1085 của chị đã được hưởng ưu đãi VIP và đang bàn giao cho bưu tá giao trong chiều nay ạ!',
                    'time'       => '09:21'
                ]
            ],
            'sess_kol_casting' => [
                [
                    'id'         => 20,
                    'session_id' => 'sess_kol_casting',
                    'sender'     => 'khach_hang',
                    'content'    => 'Lịch chụp Lookbook Studio đợt 1 vào sáng Chủ Nhật tuần này đúng không shop?',
                    'time'       => '08:45'
                ],
                [
                    'id'         => 21,
                    'session_id' => 'sess_kol_casting',
                    'sender'     => 'nhan_vien',
                    'content'    => 'Dạ đúng rồi ạ! Ekip Studio FashionKids hẹn gặp bé lúc 09:00 sáng Chủ Nhật tại Phim trường Quận 1 ạ.',
                    'time'       => '08:47'
                ]
            ]
        ];

        $messages = $defaultSeed[$sessionId] ?? [];

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM `tgb_tin_nhan_tu_van` WHERE `ma_phien_khach` = :sid ORDER BY `ma_tin_nhan` ASC");
                $stmt->execute([':sid' => $sessionId]);
                $rows = $stmt->fetchAll();
                $stmt->closeCursor();
                if (!empty($rows)) {
                    $messages = [];
                    foreach ($rows as $r) {
                        $messages[] = [
                            'id'         => (int)$r['ma_tin_nhan'],
                            'session_id' => $r['ma_phien_khach'],
                            'sender'     => $r['nguoi_gui'],
                            'content'    => $r['noi_dung_tin_nhan'],
                            'time'       => !empty($r['ngay_gui']) ? date('H:i', strtotime($r['ngay_gui'])) : date('H:i')
                        ];
                    }
                }
            } catch (PDOException $e) {}
        }

        if (!empty($_SESSION['fk_chat_messages'][$sessionId])) {
            foreach ($_SESSION['fk_chat_messages'][$sessionId] as $sm) {
                $messages[] = $sm;
            }
        }

        return $messages;
    }

    public function sendChatMessage(string $sessionId, string $sender, string $message, bool $autoReplyForCustomer = true): array {
        $userId = isset($_SESSION['user']['ma_nguoi_dung'])
            ? (int)$_SESSION['user']['ma_nguoi_dung']
            : (isset($_SESSION['auth_user']['id']) ? (int)$_SESSION['auth_user']['id'] : null);
        if ($userId !== null && $userId <= 0) {
            $userId = null;
        }
        $savedDb = false;
        $newMsgId = time();

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO `tgb_tin_nhan_tu_van` (`ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`, `ngay_gui`)
                    VALUES (:sid, :uid, :sender, :msg, :seen, NOW())
                ");
                $stmt->execute([
                    ':sid'    => $sessionId,
                    ':uid'    => $userId,
                    ':sender' => $sender,
                    ':msg'    => $message,
                    ':seen'   => ($sender === 'khach_hang') ? 0 : 1
                ]);
                $newMsgId = (int)$this->db->lastInsertId();
                $savedDb = true;
            } catch (PDOException $e) {
                // Dự phòng nếu ma_nguoi_dung không tồn tại trong FK
                if ($userId !== null) {
                    try {
                        $stmtFb = $this->db->prepare("
                            INSERT INTO `tgb_tin_nhan_tu_van` (`ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`, `ngay_gui`)
                            VALUES (:sid, NULL, :sender, :msg, :seen, NOW())
                        ");
                        $stmtFb->execute([
                            ':sid'    => $sessionId,
                            ':sender' => $sender,
                            ':msg'    => $message,
                            ':seen'   => ($sender === 'khach_hang') ? 0 : 1
                        ]);
                        $newMsgId = (int)$this->db->lastInsertId();
                        $savedDb = true;
                    } catch (PDOException $e2) {}
                }
            }
        }

        $msgObj = [
            'id'         => $newMsgId,
            'session_id' => $sessionId,
            'sender'     => $sender,
            'content'    => $message,
            'time'       => date('H:i')
        ];

        if (!$savedDb) {
            $_SESSION['fk_chat_messages'][$sessionId][] = $msgObj;
        }

        if ($sender === 'khach_hang') {
            $_SESSION['fk_chat_read_sessions'][$sessionId] = false;
        } else {
            $this->markChatSessionAsRead($sessionId);
        }

        $autoReplyObj = null;
        if ($sender === 'khach_hang' && $autoReplyForCustomer) {
            require_once __DIR__ . '/../core/GeminiService.php';
            $sizeCharts = [];
            $products   = [];
            $history    = $this->getChatMessages($sessionId);
            if ($this->hasConnection()) {
                try {
                    $sizeCharts = $this->db->query("SELECT * FROM `lnkt_bang_kich_co` ORDER BY `ma_danh_muc` ASC, `chieu_cao_toi_thieu` ASC LIMIT 20")->fetchAll();
                } catch (PDOException $e) {}
                try {
                    $products = $this->db->query("SELECT `ten_san_pham`, `gia_ban`, `so_luong_ton_kho`, `mo_ta_chat_lieu` FROM `lbn_san_pham` ORDER BY `ma_san_pham` ASC LIMIT 12")->fetchAll();
                } catch (PDOException $e) {}
            }

            $aiRes     = GeminiService::replyToCustomer($message, $history, $sizeCharts, $products);
            $replyText = trim((string)($aiRes['text'] ?? ''));

            if ($replyText !== '') {
                $replyId = $newMsgId + 1;
                if ($this->hasConnection()) {
                    try {
                        $stmt2 = $this->db->prepare("
                            INSERT INTO `tgb_tin_nhan_tu_van` (`ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`, `ngay_gui`)
                            VALUES (:sid, 2, 'nhan_vien', :msg, 1, NOW())
                        ");
                        $stmt2->execute([':sid' => $sessionId, ':msg' => $replyText]);
                        $replyId = (int)$this->db->lastInsertId();
                    } catch (PDOException $e) {}
                } else {
                    $_SESSION['fk_chat_messages'][$sessionId][] = [
                        'id'         => $replyId,
                        'session_id' => $sessionId,
                        'sender'     => 'nhan_vien',
                        'content'    => $replyText,
                        'time'       => date('H:i')
                    ];
                }
                $autoReplyObj = [
                    'id'      => $replyId,
                    'source'  => $aiRes['source'] ?? 'local_ai',
                    'content' => $replyText,
                ];
            }
        }

        return [
            'sent'       => $msgObj,
            'auto_reply' => $autoReplyObj,
            'messages'   => $this->getChatMessages($sessionId)
        ];
    }

    /**
     * ========================================================================
     * PHÂN HỆ 3: LỊCH SỰ KIỆN & TUYỂN CHỌN KOL NHÍ (lnkt_dang_ky_kol_nhi)
     * ========================================================================
     */
    private function resolveKolImages(?string $danhSachAnhJson, int $id, string $gender, array $defaultMap): array {
        $resolved = [];
        $baseDir = dirname(__DIR__);
        if (!empty($danhSachAnhJson)) {
            $decoded = json_decode($danhSachAnhJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $rawPath) {
                    $p = trim((string)$rawPath);
                    if ($p === '') {
                        continue;
                    }
                    if (preg_match('#^https?://#i', $p)) {
                        $resolved[] = $p;
                        continue;
                    }
                    $rel = preg_replace('#^/?FashionKids/#i', '', ltrim($p, '/'));
                    $diskPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
                    if (is_file($diskPath)) {
                        $resolved[] = '/FashionKids/' . $rel;
                    }
                }
            }
        }
        if (!empty($resolved)) {
            return $resolved;
        }
        if (!empty($defaultMap[$id]['image'])) {
            return [$defaultMap[$id]['image']];
        }
        return [
            $gender === 'be_trai'
                ? '/FashionKids/images/kol/kol-minh-khoi-bo.jpg'
                : '/FashionKids/images/kol/kol-thien-than-hoa-cuc.jpg',
            '/FashionKids/images/kol/kol-casting-studio-demo.jpg'
        ];
    }

    public function getDefaultKolCandidates(): array {
        return [
            1 => [
                'id'            => 1,
                'code'          => 'KOL-2026-01',
                'baby_name'     => 'Bé Hồ Bảo Sam (Sam)',
                'birth_date'    => '2022-05-15',
                'age_label'     => '4 tuổi',
                'age_group'     => '3_4',
                'gender'        => 'be_gai',
                'height_cm'     => 96.5,
                'weight_kg'     => 14.2,
                'guardian_name' => 'Võ Thị Thanh Thảo',
                'phone'         => '0918889999',
                'talent'        => 'Bé dạn dĩ trước máy ảnh, thích tạo dáng công chúa lookbook',
                'image'         => '/FashionKids/images/kol/kol-bao-sam.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-bao-sam.jpg', '/FashionKids/images/kol/kol-casting-studio-demo.jpg'],
                'status'        => 'moi_phong_van'
            ],
            2 => [
                'id'            => 2,
                'code'          => 'KOL-2026-02',
                'baby_name'     => 'Bé Trần Gia Huy (Ken)',
                'birth_date'    => '2024-02-10',
                'age_label'     => '2 tuổi',
                'age_group'     => '1_2',
                'gender'        => 'be_trai',
                'height_cm'     => 88,
                'weight_kg'     => 12.5,
                'guardian_name' => 'Anh Hoàng Nam',
                'phone'         => '0912445566',
                'talent'        => 'Cười tươi tỏa nắng, năng động trong trang phục bộ thun khủng long',
                'image'         => '/FashionKids/images/kol/kol-gia-huy-ken.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-gia-huy-ken.jpg'],
                'status'        => 'moi_phong_van'
            ],
            3 => [
                'id'            => 3,
                'code'          => 'KOL-2026-03',
                'baby_name'     => 'Bé Lê Hà My (Bông)',
                'birth_date'    => '2021-04-20',
                'age_label'     => '5 tuổi',
                'age_group'     => '5_6',
                'gender'        => 'be_gai',
                'height_cm'     => 112,
                'weight_kg'     => 19.2,
                'guardian_name' => 'Chị Thanh Hà',
                'phone'         => '0989778899',
                'talent'        => 'Catwalk thiếu nhi chuyên nghiệp, từng chụp Lookbook thời trang',
                'image'         => '/FashionKids/images/kol/kol-ha-my-bong.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-ha-my-bong.jpg'],
                'status'        => 'trung_tuyen'
            ],
            4 => [
                'id'            => 4,
                'code'          => 'KOL-2026-04',
                'baby_name'     => 'Bé Nguyễn Bảo An (Sữa)',
                'birth_date'    => '2023-05-15',
                'age_label'     => '3 tuổi',
                'age_group'     => '3_4',
                'gender'        => 'be_gai',
                'height_cm'     => 96,
                'weight_kg'     => 14.5,
                'guardian_name' => 'Chị Minh Thư',
                'phone'         => '0908112233',
                'talent'        => 'Biểu cảm rạng rỡ tự nhiên, hợp các bộ sưu tập đầm hè sợi tre Bright Pastel',
                'image'         => '/FashionKids/images/kol/kol-bao-an-sua.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-bao-an-sua.jpg'],
                'status'        => 'cho_duyet'
            ],
            5 => [
                'id'            => 5,
                'code'          => 'KOL-2026-05',
                'baby_name'     => 'Bé Trần Minh Khôi (Bơ)',
                'birth_date'    => '2021-11-08',
                'age_label'     => '5 tuổi',
                'age_group'     => '5_6',
                'gender'        => 'be_trai',
                'height_cm'     => 108,
                'weight_kg'     => 17.5,
                'guardian_name' => 'Chị Lan Hương',
                'phone'         => '0934556677',
                'talent'        => 'Phong thái mini-runway tự tin, phối đồ áo cardigan sợi tre thanh lịch',
                'image'         => '/FashionKids/images/kol/kol-minh-khoi-bo.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-minh-khoi-bo.jpg'],
                'status'        => 'trung_tuyen'
            ],
            6 => [
                'id'            => 6,
                'code'          => 'KOL-2026-06',
                'baby_name'     => 'Bé Phạm Thảo Chi (Mây)',
                'birth_date'    => '2023-08-20',
                'age_label'     => '3 tuổi',
                'age_group'     => '3_4',
                'gender'        => 'be_gai',
                'height_cm'     => 94,
                'weight_kg'     => 13.8,
                'guardian_name' => 'Chị Thu Trang',
                'phone'         => '0977223344',
                'talent'        => 'Nét dịu dàng trong veo, hợp chụp Lookbook váy công chúa cổ sen hoa cúc',
                'image'         => '/FashionKids/images/kol/kol-thien-than-hoa-cuc.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-thien-than-hoa-cuc.jpg'],
                'status'        => 'cho_duyet'
            ],
            7 => [
                'id'            => 7,
                'code'          => 'KOL-2026-07',
                'baby_name'     => 'Bé Lê Nhã Uyên (Mochi)',
                'birth_date'    => '2021-06-12',
                'age_label'     => '5 tuổi',
                'age_group'     => '5_6',
                'gender'        => 'be_gai',
                'height_cm'     => 110,
                'weight_kg'     => 18.5,
                'guardian_name' => 'Chị Minh Ngọc',
                'phone'         => '0909334455',
                'style_tag'     => 'Dân Gian Cá Tính',
                'outfit_info'   => 'Áo dài linen kem thêu chim họa mi + Chân váy navy tua rua + Băng đô thổ cẩm & chùm pom-pom sắc màu',
                'talent'        => 'Phong cách: Dân Gian Cá Tính • Outfit: Áo dài linen kem thêu chim họa mi phối chân váy xanh navy tua rua, băng đô thổ cẩm & chùm pom-pom sắc màu. Thần thái catwalk chuẩn "nàng thơ phố núi", tạo dáng cực cuốn trước ống kính.',
                'image'         => '/FashionKids/images/kol/kol-catwalk-tho-cam.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-catwalk-tho-cam.jpg'],
                'status'        => 'trung_tuyen'
            ],
            8 => [
                'id'            => 8,
                'code'          => 'KOL-2026-08',
                'baby_name'     => 'Bé Nguyễn Minh Nhật (Khoai Tây)',
                'birth_date'    => '2020-09-18',
                'age_label'     => '6 tuổi',
                'age_group'     => '5_6',
                'gender'        => 'be_trai',
                'height_cm'     => 114,
                'weight_kg'     => 20,
                'guardian_name' => 'Anh Quang Huy',
                'phone'         => '0916778822',
                'style_tag'     => 'Nghệ Sĩ Nhí Bảnh Bao',
                'outfit_info'   => 'Áo dài cách tân xanh cobalt vẽ tranh phố phường tuổi thơ + Quần tây xám than & Sneaker trắng năng động',
                'talent'        => 'Phong cách: Nghệ Sĩ Nhí Bảnh Bao • Outfit: Áo dài cách tân xanh cobalt vẽ tranh sắc màu tuổi thơ phối quần tây & sneaker trắng năng động. Sải bước sân khấu lớn cực tự tin, gương mặt sáng chuẩn soái ca nhí.',
                'image'         => '/FashionKids/images/kol/kol-catwalk-ao-dai-trai.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-catwalk-ao-dai-trai.jpg'],
                'status'        => 'trung_tuyen'
            ],
            9 => [
                'id'            => 9,
                'code'          => 'KOL-2026-09',
                'baby_name'     => 'Bé Trần Khả Hân (Cốm)',
                'birth_date'    => '2021-03-22',
                'age_label'     => '5 tuổi',
                'age_group'     => '5_6',
                'gender'        => 'be_gai',
                'height_cm'     => 109,
                'weight_kg'     => 18,
                'guardian_name' => 'Chị Bảo Trân',
                'phone'         => '0938221199',
                'style_tag'     => 'Streetstyle Pastel',
                'outfit_info'   => 'Áo khoác phao tím lilac họa tiết pop-art + Boots kem cổ thấp & Tóc bím ruy-băng tai thỏ cực chất',
                'talent'        => 'Phong cách: Streetstyle Pastel Cá Tính • Outfit: Áo khoác phao tím lilac họa tiết pop-art phối boots kem & tóc bím ruy-băng tai thỏ siêu cưng. Bước catwalk cool ngầu nhưng má phính đáng yêu hết nấc.',
                'image'         => '/FashionKids/images/kol/kol-catwalk-street-pastel.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-catwalk-street-pastel.jpg'],
                'status'        => 'trung_tuyen'
            ],
            10 => [
                'id'            => 10,
                'code'          => 'KOL-2026-10',
                'baby_name'     => 'Bé Vũ Ngọc Diệp (Kẹo Bông)',
                'birth_date'    => '2021-08-05',
                'age_label'     => '5 tuổi',
                'age_group'     => '5_6',
                'gender'        => 'be_gai',
                'height_cm'     => 107,
                'weight_kg'     => 17.2,
                'guardian_name' => 'Chị Ngọc Mai',
                'phone'         => '0975664433',
                'style_tag'     => 'Công Chúa Cổ Tích',
                'outfit_info'   => 'Đầm voan tầng trắng tinh khôi đính nơ hồng pastel + Nón hoa pha lê lấp lánh & Giày búp bê Mary Jane',
                'talent'        => 'Phong cách: Công Chúa Cổ Tích Ngọt Ngào • Outfit: Đầm voan tầng trắng tinh khôi phối nơ hồng phấn pastel, nón hoa pha lê & giày búp bê Mary Jane. Phong thái kiêu sa nhẹ nhàng trên sàn diễn.',
                'image'         => '/FashionKids/images/kol/kol-catwalk-cong-chua-trang.jpg',
                'photos'        => ['/FashionKids/images/kol/kol-catwalk-cong-chua-trang.jpg'],
                'status'        => 'trung_tuyen'
            ]
        ];
    }

    /**
     * Danh sách Sự Kiện Tiêu Biểu & Banner Sự Kiện FashionKids 2026
     */
    public function getFeaturedEvents(): array {
        return [
            [
                'id'                => 1,
                'code'              => 'EV-2026-01',
                'badge_text'        => 'Sự Kiện Sinh Nhật & Kỷ Niệm Bé Yêu',
                'badge_class'       => 'badge-peach',
                'title'             => 'Khoảnh Khắc Sinh Nhật & Nhật Ký Lớn Khôn Của Bé',
                'subtitle'          => 'Lưu giữ từng dấu mốc khôn lớn từ 0–6 tuổi cùng bộ sưu tập đồ tiệc & đồ mặc nhà sợi tre Organic. Tặng ngay voucher sinh nhật -15% và hộp quà kỷ niệm cho đơn từ 350.000đ.',
                'image'             => '/FashionKids/images/banners/banner-sinh-nhat-nhat-ky-be-yeu.webp',
                'date_label'        => '01/06/2026 – 31/12/2026',
                'perk_pills'        => ['Tặng Thiệp & Hộp Quà Bé Yêu', 'Giảm 15% Tuần Lễ Sinh Nhật'],
                'cta_label'         => 'Chọn Quà Sinh Nhật Cho Bé',
                'target_url'        => '/FashionKids/views/pages/shop-catalog.php',
                'db_type'           => 'khuyen_mai',
                'db_start'          => '2026-06-01 00:00:00',
                'db_end'            => '2026-12-31 23:59:59',
                'sort_order'        => 3,
                'sync_to_banner_db' => true,
            ],
            [
                'id'                => 2,
                'code'              => 'EV-2026-02',
                'badge_text'        => 'Ấn Phẩm & Lễ Hội Ngôi Sao Nhỏ',
                'badge_class'       => 'badge-mint',
                'title'             => 'Lễ Hội Ngôi Sao Nhỏ – Tỏa Sáng Cổ Tích & Quà Tặng Thú Bông',
                'subtitle'          => 'Chào đón ấn phẩm đặc biệt Ngôi Sao Nhỏ cùng sân chơi Siêu Sao Nhí KOL FashionKids 2026. Tặng ngay 01 thú bông xinh xắn phiên bản giới hạn cho bé ứng tuyển KOL hoặc đơn đầm công chúa.',
                'image'             => '/FashionKids/images/banners/banner-ngoi-sao-nho-qua-tang.webp',
                'date_label'        => '15/06/2026 – 30/11/2026',
                'perk_pills'        => ['Quà Tặng Kèm 01 Thú Bông Xinh', 'Đặc Cách Studio Ngôi Sao Nhỏ'],
                'cta_label'         => 'Khám Phá Ngôi Sao Nhỏ',
                'target_url'        => '/FashionKids/views/pages/kol-stars.php',
                'db_type'           => 'tuyen_chon_kol',
                'db_start'          => '2026-06-15 00:00:00',
                'db_end'            => '2026-11-30 23:59:59',
                'sort_order'        => 4,
                'sync_to_banner_db' => true,
            ],
            [
                'id'                => 3,
                'code'              => 'EV-2026-03',
                'badge_text'        => 'Chiến Dịch Casting Toàn Quốc',
                'badge_class'       => 'badge-gold',
                'title'             => 'Tuyển Chọn Siêu Sao Nhí KOL & Lookbook Sợi Tre 2026',
                'subtitle'          => 'Cơ hội sải bước trên sàn diễn chuyên nghiệp, chụp ảnh Lookbook tại Studio Quận 1 và nhận gói tài trợ trang phục 15.000.000đ cùng thẻ VIP Kim Cương.',
                'image'             => '/FashionKids/images/banners/banner-kol-nhi-2026.jpg',
                'date_label'        => 'Đang mở cổng nhận hồ sơ',
                'perk_pills'        => ['Tài Trợ Trang Phục 15 Triệu', 'Thẻ VIP Kim Cương (-15%)'],
                'cta_label'         => 'Nộp Hồ Sơ KOL Nhí',
                'target_url'        => '/FashionKids/views/pages/kol-casting.php',
                'db_type'           => 'tuyen_chon_kol',
                'db_start'          => '2026-05-15 08:00:00',
                'db_end'            => '2026-12-30 22:00:00',
                'sort_order'        => 2,
                'sync_to_banner_db' => false,
            ],
        ];
    }

    private function formatKolRow(array $r, array $defaultMap = []): array {
        $id = (int)$r['ma_ho_so'];
        $years = 3;
        if (!empty($r['ngay_sinh_be'])) {
            $ts = strtotime((string)$r['ngay_sinh_be']);
            if ($ts !== false) {
                $years = max(1, (int)date('Y') - (int)date('Y', $ts));
            }
        }
        $ageGroup = '3_4';
        if ($years <= 2) {
            $ageGroup = '1_2';
        } elseif ($years >= 5) {
            $ageGroup = '5_6';
        }

        $gender = (string)($r['gioi_tinh_be'] ?? 'be_gai');
        $resolvedPhotos = $this->resolveKolImages($r['danh_sach_anh'] ?? null, $id, $gender, $defaultMap);

        return [
            'id'            => $id,
            'user_id'       => !empty($r['ma_nguoi_dung']) ? (int)$r['ma_nguoi_dung'] : null,
            'code'          => 'KOL-2026-' . str_pad((string)$id, 2, '0', STR_PAD_LEFT),
            'baby_name'     => (string)$r['ho_ten_be'],
            'birth_date'    => (string)$r['ngay_sinh_be'],
            'age_label'     => $years . ' tuổi',
            'age_group'     => $ageGroup,
            'gender'        => $gender,
            'height_cm'     => (float)$r['chieu_cao_be'],
            'weight_kg'     => (float)$r['can_nang_be'],
            'guardian_name' => (string)$r['ho_ten_phu_huynh'],
            'phone'         => (string)$r['so_dien_thoai'],
            'talent'        => ($r['nang_khieu_kinh_nghiem'] ?? '') !== '' ? (string)$r['nang_khieu_kinh_nghiem'] : 'Tự tin tạo dáng trước ống kính',
            'image'         => $resolvedPhotos[0],
            'photos'        => $resolvedPhotos,
            'status'        => (string)($r['trang_thai_duyet'] ?? 'cho_duyet'),
            'created_at'    => !empty($r['ngay_nop_ho_so']) ? date('d/m/Y H:i', strtotime((string)$r['ngay_nop_ho_so'])) : date('d/m/Y H:i'),
        ];
    }

    public function getKolApplications(): array {
        $defaults = $this->getDefaultKolCandidates();
        $ordered  = [];

        if ($this->hasConnection()) {
            try {
                $rows = $this->db->query("
                    SELECT * FROM `lnkt_dang_ky_kol_nhi`
                    ORDER BY CASE WHEN `trang_thai_duyet` = 'cho_duyet' THEN 0 ELSE 1 END ASC, `ma_ho_so` DESC
                ")->fetchAll();
                foreach ($rows as $r) {
                    $id = (int)$r['ma_ho_so'];
                    $ordered[$id] = $this->formatKolRow($r, $defaults);
                }
            } catch (PDOException $e) {
                error_log('[lnkt] getKolApplications: ' . $e->getMessage());
            }
        }

        // Bổ sung các hồ sơ mẫu nếu DB chưa có
        foreach ($defaults as $defId => $defItem) {
            if (!isset($ordered[$defId])) {
                $ordered[$defId] = $defItem;
            }
        }

        if (!empty($_SESSION['fk_kol_status_overrides'])) {
            foreach ($_SESSION['fk_kol_status_overrides'] as $id => $st) {
                if (isset($ordered[$id])) {
                    $ordered[$id]['status'] = $st;
                }
            }
        }

        return array_values($ordered);
    }

    /**
     * Lấy danh sách hồ sơ KOL Nhí do chính khách hàng đang đăng nhập đã nộp
     */
    public function getKolApplicationsByUser(int $userId): array {
        if ($userId <= 0 || !$this->hasConnection()) {
            return [];
        }
        $defaults = $this->getDefaultKolCandidates();
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM `lnkt_dang_ky_kol_nhi`
                WHERE `ma_nguoi_dung` = :uid
                ORDER BY `ma_ho_so` DESC
            ");
            $stmt->execute([':uid' => $userId]);
            $rows = $stmt->fetchAll();
            $stmt->closeCursor();

            $result = [];
            foreach ($rows as $r) {
                $item = $this->formatKolRow($r, $defaults);
                if (!empty($_SESSION['fk_kol_status_overrides'][$item['id']])) {
                    $item['status'] = $_SESSION['fk_kol_status_overrides'][$item['id']];
                }
                $result[] = $item;
            }
            return $result;
        } catch (PDOException $e) {
            error_log('[lnkt] getKolApplicationsByUser: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Xét duyệt hồ sơ KOL Nhí qua Stored Procedure sp_lnkt_duyet_ho_so_kol_nhi / sp_lnkt_xet_duyet_kol_nhi (kèm fallback UPDATE trực tiếp)
     */
    public function reviewKolApplication(int $id, string $newStatus, ?int $reviewerId = null): array {
        $allowed = ['cho_duyet', 'moi_phong_van', 'trung_tuyen', 'tu_choi'];
        if (!in_array($newStatus, $allowed, true)) {
            $newStatus = 'moi_phong_van';
        }
        $staffOrAdminId = $reviewerId ?? (int)($_SESSION['user']['ma_nguoi_dung'] ?? $_SESSION['auth_user']['id'] ?? 2);
        $dbSaved = false;

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `sp_lnkt_duyet_ho_so_kol_nhi`(:id, :uid, :st)");
                $stmt->execute([':id' => $id, ':uid' => $staffOrAdminId, ':st' => $newStatus]);
                $stmt->closeCursor();
                $dbSaved = true;
            } catch (PDOException $e) {
                try {
                    $stmt2 = $this->db->prepare("CALL `sp_lnkt_xet_duyet_kol_nhi`(:id, :uid, :st)");
                    $stmt2->execute([':id' => $id, ':uid' => $staffOrAdminId, ':st' => $newStatus]);
                    $stmt2->closeCursor();
                    $dbSaved = true;
                } catch (PDOException $e2) {
                    try {
                        $stmt3 = $this->db->prepare("UPDATE `lnkt_dang_ky_kol_nhi` SET `trang_thai_duyet` = :st WHERE `ma_ho_so` = :id");
                        $stmt3->execute([':st' => $newStatus, ':id' => $id]);
                        $stmt3->closeCursor();
                        $dbSaved = true;
                    } catch (PDOException $e3) {
                        error_log('[lnkt] reviewKolApplication fallback error: ' . $e3->getMessage());
                    }
                }
            }
        }

        if ($dbSaved) {
            unset($_SESSION['fk_kol_status_overrides'][$id]);
        } else {
            $_SESSION['fk_kol_status_overrides'][$id] = $newStatus;
        }

        $all = $this->getKolApplications();
        $counts = ['all' => count($all), 'cho_duyet' => 0, 'moi_phong_van' => 0, 'trung_tuyen' => 0, 'tu_choi' => 0];
        foreach ($all as $item) {
            if (isset($counts[$item['status']])) {
                $counts[$item['status']]++;
            }
        }

        return [
            'id'         => $id,
            'new_status' => $newStatus,
            'counts'     => $counts
        ];
    }

    /**
     * Nộp hồ sơ ứng tuyển KOL Nhí mới từ trang kol-casting.php
     */
    public function submitKolApplication(array $data): array {
        $babyName     = trim((string)($data['baby_name'] ?? ''));
        if ($babyName === '') {
            $babyName = 'Bé Ứng Tuyển Mới';
        }
        $rawBirthDate = trim((string)($data['birth_date'] ?? ''));
        $birthDate    = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawBirthDate) ? $rawBirthDate : '2023-06-01';
        $heightCm     = max(40.0, (float)($data['height_cm'] ?? 95));
        $weightKg     = max(2.0, (float)($data['weight_kg'] ?? 14));
        $gender       = (($data['gender'] ?? 'girl') === 'boy' || ($data['gender'] ?? '') === 'be_trai') ? 'be_trai' : 'be_gai';
        $guardianName = trim((string)($data['guardian_name'] ?? ''));
        if ($guardianName === '') {
            $guardianName = trim((string)($_SESSION['user']['ho_va_ten'] ?? 'Phụ Huynh'));
        }
        $phone        = trim((string)($data['guardian_phone'] ?? ''));
        if ($phone === '') {
            $phone = trim((string)($_SESSION['user']['so_dien_thoai'] ?? '0918889999'));
        }
        $talents      = trim((string)($data['talents'] ?? '')) ?: 'Tự tin tạo dáng chụp Lookbook';
        $email        = trim((string)($_SESSION['user']['thu_dien_tu'] ?? '')) ?: 'parent@fashionkids.vn';
        $rawUid       = class_exists('Rbac') ? Rbac::currentUserId() : null;
        $userId       = $rawUid ?: (!empty($_SESSION['user']['ma_nguoi_dung']) ? (int)$_SESSION['user']['ma_nguoi_dung'] : null);
        if ($userId !== null && $userId <= 0) {
            $userId = null;
        }

        $photoList = [];
        if (!empty($_FILES['kol_photos']['name']) && is_array($_FILES['kol_photos']['name'])) {
            $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'kol' . DIRECTORY_SEPARATOR . 'uploads';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            foreach ($_FILES['kol_photos']['name'] as $idx => $origName) {
                $err = $_FILES['kol_photos']['error'][$idx] ?? UPLOAD_ERR_NO_FILE;
                $tmp = $_FILES['kol_photos']['tmp_name'][$idx] ?? '';
                $size = (int)($_FILES['kol_photos']['size'][$idx] ?? 0);
                if ($err !== UPLOAD_ERR_OK || $tmp === '' || !is_uploaded_file($tmp) || $size <= 0 || $size > 5 * 1024 * 1024) {
                    continue;
                }
                $ext = strtolower(pathinfo((string)$origName, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt, true) || @getimagesize($tmp) === false) {
                    continue;
                }
                $safeFile = 'kol_upload_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                if (is_dir($uploadDir) && @move_uploaded_file($tmp, $uploadDir . DIRECTORY_SEPARATOR . $safeFile)) {
                    $photoList[] = '/FashionKids/images/kol/uploads/' . $safeFile;
                }
                if (count($photoList) >= 5) {
                    break;
                }
            }
        }

        if (empty($photoList)) {
            $photoList = ($gender === 'be_trai')
                ? ['/FashionKids/images/kol/kol-minh-khoi-bo.jpg', '/FashionKids/images/kol/kol-gia-huy-ken.jpg', '/FashionKids/images/kol/kol-casting-studio-demo.jpg']
                : ['/FashionKids/images/kol/kol-bao-sam.jpg', '/FashionKids/images/kol/kol-thien-than-hoa-cuc.jpg', '/FashionKids/images/kol/kol-casting-studio-demo.jpg'];
        }

        $newId = 0;
        if ($this->hasConnection()) {
            if ($userId !== null) {
                try {
                    $uChk = $this->db->prepare("SELECT 1 FROM `hgb_nguoi_dung` WHERE `ma_nguoi_dung` = :uid LIMIT 1");
                    $uChk->execute([':uid' => $userId]);
                    if (!$uChk->fetchColumn()) {
                        $userId = null;
                    }
                    $uChk->closeCursor();
                } catch (PDOException $ue) {
                    $userId = null;
                }
            }

            try {
                $stmt = $this->db->prepare("
                    INSERT INTO `lnkt_dang_ky_kol_nhi`
                        (`ma_nguoi_dung`, `ho_ten_phu_huynh`, `so_dien_thoai`, `thu_dien_tu`, `ho_ten_be`, `ngay_sinh_be`, `gioi_tinh_be`, `chieu_cao_be`, `can_nang_be`, `danh_sach_anh`, `nang_khieu_kinh_nghiem`, `trang_thai_duyet`, `ngay_nop_ho_so`)
                    VALUES
                        (:uid, :parent, :phone, :email, :baby, :dob, :gender, :h, :w, :photos, :talent, 'cho_duyet', NOW())
                ");
                $stmt->execute([
                    ':uid'    => $userId,
                    ':parent' => mb_substr($guardianName, 0, 100, 'UTF-8'),
                    ':phone'  => mb_substr($phone, 0, 20, 'UTF-8'),
                    ':email'  => mb_substr($email, 0, 100, 'UTF-8'),
                    ':baby'   => mb_substr($babyName, 0, 100, 'UTF-8'),
                    ':dob'    => $birthDate,
                    ':gender' => $gender,
                    ':h'      => $heightCm,
                    ':w'      => $weightKg,
                    ':photos' => json_encode($photoList, JSON_UNESCAPED_SLASHES),
                    ':talent' => $talents
                ]);
                $newId = (int)$this->db->lastInsertId();
            } catch (PDOException $e) {
                error_log('[lnkt] submitKolApplication DB error: ' . $e->getMessage());
                throw new RuntimeException('Không thể lưu hồ sơ ứng tuyển KOL Nhí vào cơ sở dữ liệu lúc này. Vui lòng thử lại!', 0, $e);
            }
        } else {
            $newId = rand(100, 999);
        }

        $years = max(1, (int)date('Y') - (int)date('Y', strtotime($birthDate)));
        $ageGroup = ($years <= 2) ? '1_2' : (($years >= 5) ? '5_6' : '3_4');

        return [
            'id'            => $newId,
            'user_id'       => $userId,
            'code'          => 'KOL-2026-' . str_pad((string)$newId, 2, '0', STR_PAD_LEFT),
            'baby_name'     => $babyName,
            'birth_date'    => $birthDate,
            'age_label'     => $years . ' tuổi',
            'age_group'     => $ageGroup,
            'gender'        => $gender,
            'height_cm'     => $heightCm,
            'weight_kg'     => $weightKg,
            'guardian_name' => $guardianName,
            'phone'         => $phone,
            'image'         => $photoList[0],
            'photos'        => $photoList,
            'talent'        => $talents,
            'status'        => 'cho_duyet',
            'created_at'    => date('d/m/Y H:i'),
        ];
    }
}
