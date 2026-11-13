<?php
$page_title = "Chi Tiết Sản Phẩm & Đánh Giá Thực Tế";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/ProductModel.php";
require_once __DIR__ . "/../../models/UserModel.php";
require_once __DIR__ . "/../../models/ReviewModel.php";

$pdpProductModel = new ProductModel();
$pdpUserModel    = new UserModel();
$pdpReviewModel  = new ReviewModel();

$currentUid = Rbac::currentUserId();
if ($currentUid) {
    $pdpUserModel->dongBoSessionNguoiDung($currentUid);
}

// Danh mục chuẩn của hệ thống
$lbn_categories = [
    1 => ['id' => 1, 'slug' => 'vay_cong_chua', 'name' => 'Váy công chúa', 'type' => 'quan_ao',  'icon' => 'fa-wand-magic-sparkles text-peach'],
    2 => ['id' => 2, 'slug' => 'bo_thun',       'name' => 'Bộ thun',       'type' => 'quan_ao',  'icon' => 'fa-shirt text-info'],
    3 => ['id' => 3, 'slug' => 'ao_khoac',      'name' => 'Áo khoác',      'type' => 'quan_ao',  'icon' => 'fa-vest-patches text-success'],
    4 => ['id' => 4, 'slug' => 'giay_sandal',   'name' => 'Giày sandal',   'type' => 'giay_dep', 'icon' => 'fa-shoe-prints text-warning']
];

// Danh mục chi tiết mở rộng cho PDP (phối hợp đồng bộ với CSDL)
$lbn_products = [
    101 => [
        'id'             => 101,
        'sku'            => 'FK-LBN-01',
        'image'          => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 285000,
        'original_price' => 340000,
        'stock_quantity' => 18,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => '100% Sợi Tre Kháng Khuẩn Tự Nhiên',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-person-dress text-peach',
        'colors'         => [
            ['name' => 'Hồng Phấn Pastel', 'code' => '#FFD1DC', 'active' => true],
            ['name' => 'Kem Sữa Dịu Ngọt', 'code' => '#FFFDD0', 'active' => false],
            ['name' => 'Xanh Bạc Hà Mint', 'code' => '#B8E0D2', 'active' => false]
        ],
        'description'    => 'Váy công chúa được dệt từ 100% sợi tre Organic kết hợp tùng ren hoa cúc họa mi thêu nổi mềm mại. Chất vải kháng khuẩn tự nhiên, chống nấm mốc và giảm tĩnh điện, bảo vệ làn da mỏng manh của bé suốt cả ngày dài.',
        'material'       => '95% Vải sợi tre hữu cơ tự nhiên (Organic Bamboo Fiber), 5% Spandex tăng đàn hồi co giãn 4 chiều.',
        'certifications' => ['OEKO-TEX® Standard 100', 'Kháng khuẩn 99.8% Test Intertek', 'Không hóa chất Formaldehyde'],
        'wash_guide'     => 'Giặt máy chế độ nhẹ hoặc giặt tay nước ấm < 30°C. Tránh phơi trực tiếp dưới nắng gắt để giữ màu pastel tươi sáng.'
    ],
    102 => [
        'id'             => 102,
        'sku'            => 'FK-LBN-02',
        'image'          => '/FashionKids/images/bo-thun-khung-long.jpg',
        'category_id'    => 2,
        'category_slug'  => 'bo_thun',
        'category_name'  => 'Bộ thun',
        'name'           => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 225000,
        'original_price' => 280000,
        'stock_quantity' => 24,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Sợi Tre Bamboo Thoáng Khí Làm Mát -3°C',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-shirt text-info',
        'colors'         => [
            ['name' => 'Xanh Da Trời Soft Sky', 'code' => '#A8DADC', 'active' => true],
            ['name' => 'Vàng Bơ Buttercup', 'code' => '#FDE2B8', 'active' => false],
            ['name' => 'Ghi Sáng Pastel', 'code' => '#E2E8F0', 'active' => false]
        ],
        'description'    => 'Bộ thun cộc tay sợi tre mát lạnh, in hình chú khủng long nhí tinh nghịch bằng mực in hữu cơ gốc nước không bong tróc, bo chun bụng siêu mềm không tạo vết hằn đỏ trên bụng bé.',
        'material'       => '100% sợi tre Bamboo kết hợp công nghệ dệt Cool-Touch làm mát tức thì.',
        'certifications' => ['Chứng nhận an toàn da liễu cho trẻ sơ sinh', 'OEKO-TEX Class 1 Baby'],
        'wash_guide'     => 'Có thể giặt máy, lộn mặt trái khi giặt và là ủi ở nhiệt độ trung bình.'
    ],
    103 => [
        'id'             => 103,
        'sku'            => 'FK-LBN-03',
        'image'          => '/FashionKids/images/giay-sandal-tap-di.jpg',
        'category_id'    => 4,
        'category_slug'  => 'giay_sandal',
        'category_name'  => 'Giày sandal',
        'name'           => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-gold',
        'price'          => 210000,
        'original_price' => 260000,
        'stock_quantity' => 14,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Lót Sợi Tre Khử Mùi & Đế Uốn Gập 360°',
        'size_range'     => ['Size 21 (12cm)', 'Size 22 (13cm)', 'Size 24 (14cm)', 'Size 26 (15cm)', 'Size 28 (16.5cm)'],
        'default_size'   => 'Size 22 (13cm)',
        'type'           => 'giay_dep',
        'icon'           => 'fa-shoe-prints text-warning',
        'colors'         => [
            ['name' => 'Nâu Be Tự Nhiên', 'code' => '#E2D4C0', 'active' => true],
            ['name' => 'Hồng Pastel', 'code' => '#FFD1DC', 'active' => false],
            ['name' => 'Xanh Mint Dịu Nhẹ', 'code' => '#B8E0D2', 'active' => false]
        ],
        'description'    => 'Sandal hỗ trợ những bước đi đầu đời vững chắc cho bé. Đế cao su non tự nhiên uốn gập 360 độ siêu dẻo, rãnh chống trượt tổ ong cùng lót đệm sợi tre kháng khuẩn khử mùi hôi chân hiệu quả.',
        'material'       => 'Thân quai dệt sợi tổng hợp cao cấp, lót trong sợi tre êm ái, đế cao su tự nhiên an toàn.',
        'certifications' => ['Đạt chuẩn công thái học tập đi cho trẻ 1-4 tuổi', 'Không kim loại nặng'],
        'wash_guide'     => 'Lau sạch bằng khăn ẩm hoặc giặt tay với xà phòng dịu nhẹ, phơi nơi râm mát thoáng gió.'
    ],
    104 => [
        'id'             => 104,
        'sku'            => 'FK-LBN-04',
        'image'          => '/FashionKids/images/ao-khoac-cardigan.jpg',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-mint',
        'price'          => 320000,
        'original_price' => 380000,
        'stock_quantity' => 5,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Sợi Tre Dệt Kim UPF 50+ Chống Tia Cực Tím',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-vest-patches text-success',
        'colors'         => [
            ['name' => 'Xanh Mint Pastel', 'code' => '#B8E0D2', 'active' => true],
            ['name' => 'Vàng Bơ Dịu Dàng', 'code' => '#FDE2B8', 'active' => false]
        ],
        'description'    => 'Áo khoác Cardigan sợi tre dệt 2 lớp mềm xốp, trang bị chỉ số chống nắng tự nhiên UPF 50+ cản 98% tia UVA/UVB, cúc gỗ mài nhẵn không xước da bé.',
        'material'       => '100% Sợi tre dệt kim tự nhiên Organic Bamboo.',
        'certifications' => ['Chứng nhận chống tia cực tím UPF 50+', 'OEKO-TEX Standard 100'],
        'wash_guide'     => 'Giặt tay nhẹ nhàng hoặc dùng túi giặt chế độ đồ len em bé.'
    ],
    105 => [
        'id'             => 105,
        'sku'            => 'FK-LBN-05',
        'image'          => '/FashionKids/images/vay-cong-chua-no-pastel.jpg',
        'category_id'    => 1,
        'category_slug'  => 'vay_cong_chua',
        'category_name'  => 'Váy công chúa',
        'name'           => 'Váy Công Chúa Tùng Xòe Pastel Nơ Bản To Dự Tiệc',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 365000,
        'original_price' => 420000,
        'stock_quantity' => 11,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Lót Sợi Tre Hữu Cơ & Voan Tùng Xòe Cao Cấp',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-crown text-peach',
        'colors'         => [
            ['name' => 'Cam Đào Pastel', 'code' => '#FF9E93', 'active' => true],
            ['name' => 'Kem Sữa Công Chúa', 'code' => '#FAF8F5', 'active' => false]
        ],
        'description'    => 'Thiết kế đầm dự tiệc lộng lẫy với 3 lớp voan mềm phối nơ lụa bản to phía trước, lớp lót trong 100% sợi tre thấm hút mồ hôi tuyệt đối.',
        'material'       => 'Lớp trong 100% Organic Bamboo, lớp ngoài Tulle mềm không gây ngứa.',
        'certifications' => ['OEKO-TEX® Standard 100', 'An toàn cho làn da nhạy cảm'],
        'wash_guide'     => 'Giặt tay nhẹ nhàng, treo phẳng tự nhiên.'
    ],
    106 => [
        'id'             => 106,
        'sku'            => 'FK-LBN-06',
        'image'          => '/FashionKids/images/bo-thun-gau-nhi.jpg',
        'category_id'    => 2,
        'category_slug'  => 'bo_thun',
        'category_name'  => 'Bộ thun',
        'name'           => 'Bộ Thun Sợi Tre Cộc Tay Gấu Nhí Thoáng Khí Mùa Hè',
        'gender'         => 'unisex',
        'gender_label'   => 'Unisex',
        'gender_badge'   => 'badge-gold',
        'price'          => 195000,
        'original_price' => 240000,
        'stock_quantity' => 32,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => '100% Sợi Tre Kháng Khuẩn Thoáng Khí',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-baby text-success',
        'colors'         => [
            ['name' => 'Vàng Bơ Kem Sữa', 'code' => '#FDE2B8', 'active' => true],
            ['name' => 'Xanh Mint Nhạt', 'code' => '#B8E0D2', 'active' => false]
        ],
        'description'    => 'Bộ đồ mặc nhà và dạo phố cộc tay thêu hình chú gấu nhí đáng yêu, chất vải sợi tre mát mịn giúp bé ngủ ngon suốt đêm hè.',
        'material'       => '95% Bamboo Fiber, 5% Spandex.',
        'certifications' => ['OEKO-TEX® Class 1 Baby', 'Không chứa BPA'],
        'wash_guide'     => 'Giặt máy chế độ nhẹ dưới 30°C.'
    ],
    107 => [
        'id'             => 107,
        'sku'            => 'FK-LBN-07',
        'image'          => '/FashionKids/images/ao-khoac-hoodie-tai-tho.jpg',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Hoodie Tai Thỏ Sợi Tre Ấm Áp Cho Bé Gái',
        'gender'         => 'girl',
        'gender_label'   => 'Bé gái',
        'gender_badge'   => 'badge-peach',
        'price'          => 340000,
        'original_price' => 395000,
        'stock_quantity' => 4,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Nỉ Bông Sợi Tre Giữ Ấm & Chống Nắng',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 2 (2-3T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-shirt text-peach',
        'colors'         => [
            ['name' => 'Hồng Đào Pastel', 'code' => '#FFD1DC', 'active' => true],
            ['name' => 'Trắng Kem Sữa', 'code' => '#FAF8F5', 'active' => false]
        ],
        'description'    => 'Áo khoác có mũ phối đôi tai thỏ rủ siêu cưng cho bé gái, khóa kéo bọc vải chống kẹt cằm và giữ ấm ngực bé khi ra ngoài.',
        'material'       => 'Nỉ bông sợi tre hữu cơ Organic Bamboo.',
        'certifications' => ['OEKO-TEX® Standard 100', 'Kháng khuẩn 99.8%'],
        'wash_guide'     => 'Giặt máy trong túi giặt, phơi nơi thoáng mát.'
    ],
    108 => [
        'id'             => 108,
        'sku'            => 'FK-LBN-08',
        'image'          => '/FashionKids/images/giay-sandal-be-trai.jpg',
        'category_id'    => 4,
        'category_slug'  => 'giay_sandal',
        'category_name'  => 'Giày sandal',
        'name'           => 'Giày Sandal Bé Trai Quai Dán Chống Trượt Siêu Nhẹ',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 245000,
        'original_price' => 295000,
        'stock_quantity' => 16,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Lót Sợi Tre Êm Chân & Quai Dán Linh Hoạt',
        'size_range'     => ['Size 22 (13cm)', 'Size 24 (14cm)', 'Size 26 (15cm)', 'Size 28 (16.5cm)', 'Size 30 (17.5cm)'],
        'default_size'   => 'Size 24 (14cm)',
        'type'           => 'giay_dep',
        'icon'           => 'fa-shoe-prints text-info',
        'colors'         => [
            ['name' => 'Xanh Sky Phối Kem', 'code' => '#A8DADC', 'active' => true]
        ],
        'description'    => 'Giày sandal thể thao siêu nhẹ cho bé trai chạy nhảy, 2 quai dán điều chỉnh độ rộng dễ dàng và đế chống trượt an toàn.',
        'material'       => 'Vải canvas phối lót sợi tre khử mùi, đế EVA cao su non.',
        'certifications' => ['Chuẩn công thái học bàn chân trẻ em'],
        'wash_guide'     => 'Vệ sinh bằng bàn chải mềm và khăn ẩm.'
    ],
    109 => [
        'id'             => 109,
        'sku'            => 'FK-LBN-09',
        'image'          => '/FashionKids/images/ao-khoac-bomber.jpg',
        'category_id'    => 3,
        'category_slug'  => 'ao_khoac',
        'category_name'  => 'Áo khoác',
        'name'           => 'Áo Khoác Bomber Sợi Tre Phi Hành Gia Nhí Bé Trai',
        'gender'         => 'boy',
        'gender_label'   => 'Bé trai',
        'gender_badge'   => 'badge-sky',
        'price'          => 350000,
        'original_price' => 410000,
        'stock_quantity' => 9,
        'rating'         => 0.0,
        'reviews'        => 0,
        'sold_count'     => 0,
        'is_bamboo'      => 1,
        'bamboo_label'   => 'Sợi Tre 2 Lớp Cản Gió & Thoáng Khí',
        'size_range'     => ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
        'default_size'   => 'Size 3 (3-4T)',
        'type'           => 'quan_ao',
        'icon'           => 'fa-user-astronaut text-primary',
        'colors'         => [
            ['name' => 'Xanh Vũ Trụ Phối Kem', 'code' => '#A8DADC', 'active' => true]
        ],
        'description'    => 'Áo khoác dáng Varsity Bomber cực ngầu với huy hiệu phi hành gia thêu nổi sắc nét, bo viền cổ và tay áo mềm mại.',
        'material'       => 'Vải sợi tre dệt dày 2 lớp cản gió, lót cotton hữu cơ.',
        'certifications' => ['OEKO-TEX® Standard 100'],
        'wash_guide'     => 'Giặt máy chế độ nhẹ, lộn mặt trái khi phơi.'
    ]
];

// Đồng bộ dữ liệu giá, tồn kho, tên, ảnh, đánh giá thực và số người mua từ CSDL thực tế
$liveCatalog = $pdpProductModel->layTatCaSanPhamChuanHoa();
foreach ($liveCatalog as $liveRow) {
    $lid = (int)$liveRow['id'];
    if (isset($lbn_products[$lid])) {
        $lbn_products[$lid]['name']           = $liveRow['name'];
        $lbn_products[$lid]['price']          = (float)$liveRow['price'];
        $lbn_products[$lid]['original_price'] = max((float)$liveRow['price'] + 55000, round($liveRow['price'] * 1.18, -3));
        $lbn_products[$lid]['stock_quantity'] = (int)$liveRow['stock_quantity'];
        $lbn_products[$lid]['image']          = $liveRow['image'];
        $lbn_products[$lid]['gallery']        = $liveRow['gallery'] ?? [];
        if (!empty($liveRow['has_custom_colors']) && !empty($liveRow['colors'])) {
            $lbn_products[$lid]['colors'] = $liveRow['colors'];
        }
        $lbn_products[$lid]['material']       = $liveRow['material'];
        $lbn_products[$lid]['category_id']    = (int)$liveRow['category_id'];
        $lbn_products[$lid]['category_slug']  = $liveRow['category_slug'];
        $lbn_products[$lid]['category_name']  = $liveRow['category_name'];
        $lbn_products[$lid]['rating']         = (float)($liveRow['rating'] ?? 0.0);
        $lbn_products[$lid]['reviews']        = (int)($liveRow['reviews'] ?? 0);
        $lbn_products[$lid]['buyer_count']    = (int)($liveRow['buyer_count'] ?? 0);
        $lbn_products[$lid]['sold_count']     = (int)($liveRow['buyer_count'] ?? 0);
    } else {
        $isShoes = ($liveRow['type'] === 'giay_dep');
        $lbn_products[$lid] = [
            'id'             => $lid,
            'sku'            => $liveRow['sku'],
            'image'          => $liveRow['image'],
            'gallery'        => $liveRow['gallery'] ?? [],
            'category_id'    => (int)$liveRow['category_id'],
            'category_slug'  => $liveRow['category_slug'],
            'category_name'  => $liveRow['category_name'],
            'name'           => $liveRow['name'],
            'gender'         => $liveRow['gender'],
            'gender_label'   => $liveRow['gender_label'],
            'gender_badge'   => $liveRow['gender_badge'],
            'price'          => (float)$liveRow['price'],
            'original_price' => round($liveRow['price'] * 1.18, -3),
            'stock_quantity' => (int)$liveRow['stock_quantity'],
            'rating'         => (float)($liveRow['rating'] ?? 0.0),
            'reviews'        => (int)($liveRow['reviews'] ?? 0),
            'buyer_count'    => (int)($liveRow['buyer_count'] ?? 0),
            'sold_count'     => (int)($liveRow['buyer_count'] ?? 0),
            'is_bamboo'      => 1,
            'bamboo_label'   => '100% Vải Sợi Tre Kháng Khuẩn',
            'size_range'     => $isShoes
                ? ['Size 21 (12cm)', 'Size 22 (13cm)', 'Size 24 (14cm)', 'Size 26 (15cm)', 'Size 28 (16.5cm)']
                : ['Size 1 (1-2T)', 'Size 2 (2-3T)', 'Size 3 (3-4T)', 'Size 4 (4-5T)', 'Size 5 (5-6T)'],
            'default_size'   => $isShoes ? 'Size 24 (14cm)' : 'Size 2 (2-3T)',
            'type'           => $liveRow['type'],
            'icon'           => $liveRow['icon'],
            'colors'         => !empty($liveRow['colors']) ? $liveRow['colors'] : [['name' => 'Bright Pastel', 'code' => '#FAF8F5', 'active' => true]],
            'description'    => $liveRow['material'],
            'material'       => $liveRow['material'],
            'certifications' => ['OEKO-TEX® Standard 100', 'An toàn cho làn da nhạy cảm của bé'],
            'wash_guide'     => 'Giặt nhẹ nước mát dưới 30°C, phơi nơi thoáng gió.'
        ];
    }
}

// Lấy ID sản phẩm từ URL (mặc định ID 101)
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 101;
if (!isset($lbn_products[$product_id])) {
    $product_id = 101;
}
$prod = $lbn_products[$product_id];

// Lấy hạng thẻ VIP & chiết khấu thực tế từ CSDL (chỉ đọc, tự động theo tài khoản)
$vipInfo               = $pdpUserModel->layThongTinVipHienTai($currentUid);
$init_discount_percent = (float)($vipInfo['discount_percent'] ?? 0.0);
$vipIsGuest            = !empty($vipInfo['is_guest']);
$vipTierTitle          = (string)($vipInfo['tier_name'] ?? 'Đồng (Bronze)');
$vipNextTierName       = $vipInfo['next_tier_name'] ?? null;
$vipPointsNeeded       = (int)($vipInfo['points_needed'] ?? 0);

$init_discount_amount = round(($prod['price'] * $init_discount_percent) / 100);
$init_vip_price       = max(0, $prod['price'] - $init_discount_amount);

// Lấy đánh giá thực tế và số người mua thực tế từ CSDL
$dbReviews   = $pdpReviewModel->layDanhGiaTheoSanPham($product_id);
$reviewCount = count($dbReviews);
$buyerCount  = $pdpReviewModel->demSoNguoiMua($product_id);
$starCounts  = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
if ($reviewCount > 0) {
    foreach ($dbReviews as $rev) {
        $s = max(1, min(5, (int)($rev['so_sao_danh_gia'] ?? 5)));
        $starCounts[$s]++;
    }
    $avgRating = round($pdpReviewModel->tinhSaoTrungBinh($product_id), 1);
} else {
    $avgRating = 0.0;
}

$reviewEligibility = $currentUid
    ? $pdpReviewModel->kiemTraQuyenDanhGia($currentUid, $product_id)
    : ['eligible' => false, 'already_reviewed' => false, 'eligible_order_id' => 0];

$isOutOfStock = ((int)$prod['stock_quantity'] <= 0);
?>

<div class="container my-4 my-md-5" id="lbn_pdp_section">
    <!-- Breadcrumb Điều Hướng -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3 small">
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/home.php" class="text-muted-custom">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php" class="text-muted-custom">Cửa hàng</a></li>
            <li class="breadcrumb-item"><a href="/FashionKids/views/pages/shop-catalog.php?category=<?= $prod['category_slug'] ?>" class="text-muted-custom"><?= htmlspecialchars($prod['category_name']) ?></a></li>
            <li class="breadcrumb-item active text-peach font-weight-bold" aria-current="page"><?= htmlspecialchars($prod['name']) ?></li>
        </ol>
    </nav>

    <!-- ======================================================================
         KHỐI TRÊN PDP: HÌNH ẢNH, CHI TIẾT SẢN PHẨM & TƯƠNG TÁC
         ====================================================================== -->
    <div class="card card-pastel border-0 shadow-sm mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="row">
                <!-- CỘT TRÁI (6 CỘT): SLIDER ẢNH ĐA GÓC CHỤP & THUMBNAILS -->
                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <!-- Khung Carousel Ảnh Chính -->
                    <div id="lbn_pdpImageSlider" class="carousel slide lbn-pdp-slider-main shadow-sm position-relative mb-3" data-ride="carousel" data-interval="false">
                        <!-- Badges Nổi Bật -->
                        <div class="position-absolute fk-pos-tl-15 d-flex flex-wrap align-items-center">
                            <span class="lbn-badge-bamboo lbn-pdp-bamboo-badge shadow-sm mr-2 mb-1">
                                <i class="fa-solid fa-leaf mr-1"></i><?= htmlspecialchars($prod['bamboo_label']) ?>
                            </span>
                            <span class="badge badge-pill <?= $prod['gender_badge'] ?> lbn-pdp-gender-badge shadow-sm font-weight-bold mb-1">
                                <?= htmlspecialchars($prod['gender_label']) ?>
                            </span>
                        </div>

                        <!-- Cảnh báo tồn kho nếu <= 5 -->
                        <?php if ($prod['stock_quantity'] <= 5): ?>
                            <div class="position-absolute fk-pos-tr-15">
                                <span class="badge lbn-badge-low-stock shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i><?= $prod['stock_quantity'] <= 0 ? 'Tạm hết hàng' : ('Chỉ còn ' . (int)$prod['stock_quantity'] . ' cái') ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php
                        $gData = $prod['gallery'] ?? [];
                        $isDemoCatalog = ((int)$prod['id'] <= 109);
                        $pdpSlides = [
                            [
                                'src'   => !empty($gData['front']) ? $gData['front'] : $prod['image'],
                                'thumb' => 'Mặt Trước',
                                'title' => 'Ảnh Mặt Trước (Góc Chụp Chuẩn Studio)',
                                'desc'  => '100% Sợi Tre Organic • Đường May Chìm Êm Da',
                            ],
                        ];
                        $slotDefinitions = [
                            ['key' => 'detail', 'thumb' => 'Chi Tiết', 'title' => 'Ảnh Chi Tiết Thiết Kế & Đường May',   'desc' => 'Cúc Bấm & Bo Chun An Toàn • Không Cấn Làn Da Bé',   'fallback' => $isDemoCatalog ? $prod['image'] : ''],
                            ['key' => 'fabric', 'thumb' => 'Cận Vải',  'title' => 'Cận Cảnh Sớ Vải Dệt Kháng Khuẩn',     'desc' => 'Mềm Mịn Như Lụa • Thấm Hút Mồ Hôi Gấp 2 Lần Cotton', 'fallback' => $isDemoCatalog ? $prod['image'] : ''],
                            ['key' => 'model',  'thumb' => 'Bé Mặc',   'title' => 'Ảnh Bé Mẫu Thực Tế (Lookbook 2026)', 'desc' => 'Hình ảnh mẫu nhí diện sản phẩm thực tế',            'fallback' => $isDemoCatalog ? '/FashionKids/images/banner-mua-he-2026.jpg' : ''],
                        ];
                        foreach ($slotDefinitions as $sd) {
                            $slotSrc = !empty($gData[$sd['key']]) ? (string)$gData[$sd['key']] : (string)$sd['fallback'];
                            if ($slotSrc !== '') {
                                $pdpSlides[] = [
                                    'src'   => $slotSrc,
                                    'thumb' => $sd['thumb'],
                                    'title' => $sd['title'],
                                    'desc'  => $sd['desc'],
                                ];
                            }
                        }
                        if (!empty($gData['extra']) && is_array($gData['extra'])) {
                            foreach ($gData['extra'] as $exIdx => $exSrc) {
                                if (!empty($exSrc)) {
                                    $pdpSlides[] = [
                                        'src'   => (string)$exSrc,
                                        'thumb' => 'Ảnh ' . (count($pdpSlides) + 1),
                                        'title' => 'Ảnh Chi Tiết Bổ Sung #' . ($exIdx + 1),
                                        'desc'  => 'Hình ảnh thực tế sản phẩm ' . $prod['name'],
                                    ];
                                }
                            }
                        }
                        ?>

                        <div class="carousel-inner text-center">
                            <?php foreach ($pdpSlides as $sIdx => $slide): ?>
                                <div class="carousel-item <?= $sIdx === 0 ? 'active' : '' ?>">
                                    <div class="lbn-pdp-slide-item">
                                        <img src="<?= htmlspecialchars($slide['src']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>" class="lbn-pdp-main-img mb-2">
                                        <h6 class="font-weight-bold text-slate mb-0"><?= htmlspecialchars($slide['title']) ?></h6>
                                        <small class="text-muted-custom"><?= htmlspecialchars($slide['desc']) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($pdpSlides) > 1): ?>
                            <!-- Điều Hướng Trái / Phải -->
                            <a class="carousel-control-prev" href="#lbn_pdpImageSlider" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon rounded-circle bg-dark p-2 fk-opacity-50" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#lbn_pdpImageSlider" role="button" data-slide="next">
                                <span class="carousel-control-next-icon rounded-circle bg-dark p-2 fk-opacity-50" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Hàng Thumbnail Bấm Đổi Slide Ảnh -->
                    <div class="row no-gutters">
                        <?php foreach ($pdpSlides as $sIdx => $slide): ?>
                            <div class="col-3 p-1">
                                <div class="lbn-pdp-thumb <?= $sIdx === 0 ? 'active' : '' ?>" onclick="lbnSwitchPdpSlide(<?= (int)$sIdx ?>, this)">
                                    <img src="<?= htmlspecialchars($slide['src']) ?>" alt="<?= htmlspecialchars($slide['thumb']) ?>" class="lbn-pdp-thumb-img">
                                    <span class="small font-weight-bold text-slate fk-fs-075"><?= htmlspecialchars($slide['thumb']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 3 Cam Kết Vàng Về Sản Phẩm -->
                    <div class="row mt-4 pt-3 border-top text-center text-muted-custom small">
                        <div class="col-4">
                            <i class="fa-solid fa-shield-halved text-success h5 d-block mb-1"></i>
                            <span class="font-weight-bold text-slate d-block">100% Sợi Tre</span>
                            Chuẩn OEKO-TEX
                        </div>
                        <div class="col-4 border-left border-right">
                            <i class="fa-solid fa-rotate-left text-peach h5 d-block mb-1"></i>
                            <span class="font-weight-bold text-slate d-block">15 Ngày Đổi Trả</span>
                            Miễn phí nếu bé chật
                        </div>
                        <div class="col-4">
                            <i class="fa-solid fa-truck-fast text-info h5 d-block mb-1"></i>
                            <span class="font-weight-bold text-slate d-block">Giao Hỏa Tốc 2H</span>
                            Được kiểm hàng trước
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI (6 CỘT): THÔNG TIN GIÁ, SIZE & ĐẶT MUA -->
                <div class="col-12 col-lg-6 pl-lg-4 min-w-0">
                    <div class="d-flex flex-wrap align-items-center fk-btn-toolbar mb-2">
                        <span class="badge badge-pill badge-light border font-weight-bold text-slate text-nowrap">
                            Mã SP: <strong class="text-slate"><?= htmlspecialchars($prod['sku']) ?></strong>
                        </span>
                        <span class="badge badge-pill badge-light border text-muted-custom text-nowrap">
                            Danh mục: <?= htmlspecialchars($prod['category_name']) ?>
                        </span>
                    </div>

                    <!-- Tiêu Đề Sản Phẩm -->
                    <h1 class="h3 font-weight-bold text-slate mb-2" id="lbn_pdpProductTitle">
                        <?= htmlspecialchars($prod['name']) ?>
                    </h1>

                    <!-- Đánh Giá Sao & Số Người Mua Thực Tế -->
                    <div class="d-flex flex-wrap align-items-center fk-btn-toolbar mb-3">
                        <div id="pdp_rating_stars_top" class="text-warning text-nowrap <?= $reviewCount > 0 ? '' : 'd-none' ?>">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span class="font-weight-bold text-slate ml-1" id="pdp_avg_rating_top"><?= number_format($avgRating, 1) ?></span>
                        </div>
                        <span id="pdp_no_review_badge_top" class="badge badge-pill fk-no-review-badge text-nowrap <?= $reviewCount > 0 ? 'd-none' : '' ?>">
                            Chưa có đánh giá
                        </span>
                        <span class="text-muted-custom small border-left pl-2 text-nowrap">
                            <a href="#pdp_review_section" class="text-muted-custom text-decoration-none">
                                <strong id="pdp_review_count_top"><?= $reviewCount ?></strong> đánh giá thực tế
                            </a>
                        </span>
                        <span class="text-muted-custom small border-left pl-2 text-nowrap">
                            <strong id="pdp_buyer_count_top"><?= (int)$buyerCount ?></strong> người mua
                        </span>
                    </div>

                    <!-- Khung Giá & Chiết Khấu VIP Tự Động Theo Tài Khoản (Chỉ đọc) -->
                    <div class="p-3 rounded mb-3 bg-milk-cream border">
                        <div class="d-flex flex-wrap align-items-baseline fk-btn-toolbar mb-2">
                            <span class="h2 font-weight-bold text-peach mb-0 text-nowrap" id="lbn_pdpBasePrice" data-base-price="<?= (int)$prod['price'] ?>">
                                <?= number_format($prod['price'], 0, ',', '.') ?> đ
                            </span>
                            <span class="text-muted-custom text-decoration-line-through small text-nowrap">
                                <?= number_format($prod['original_price'], 0, ',', '.') ?> đ
                            </span>
                            <span class="badge badge-pill badge-danger small font-weight-bold text-nowrap">
                                -<?= max(5, round((($prod['original_price'] - $prod['price']) / max(1, $prod['original_price'])) * 100)) ?>%
                            </span>
                        </div>

                        <div class="p-3 rounded lbn-vip-price-box">
                            <div class="d-flex justify-content-between align-items-center fk-card-header-row mb-1 flex-wrap">
                                <span class="small font-weight-bold text-slate min-w-0" id="lbn_pdpVipBadgeLabel">
                                    <i class="fa-solid fa-crown text-warning mr-1"></i>
                                    <?php if ($vipIsGuest): ?>
                                        Khách vãng lai (Giảm 0%)
                                    <?php else: ?>
                                        Hạng thẻ của bạn: <strong><?= htmlspecialchars($vipTierTitle) ?></strong> (Giảm <?= (int)$init_discount_percent ?>%)
                                    <?php endif; ?>
                                </span>
                                <span class="h5 font-weight-bold text-success mb-0 text-nowrap flex-shrink-0" id="lbn_pdpFinalVipPrice">
                                    <?= number_format($init_vip_price, 0, ',', '.') ?> đ
                                </span>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row small">
                                <span class="text-muted-custom min-w-0" id="lbn_pdpSavedAmount">
                                    <?php if ($init_discount_amount > 0): ?>
                                        Tiết kiệm ngay <strong class="text-success"><?= number_format($init_discount_amount, 0, ',', '.') ?> đ</strong> với thẻ VIP
                                    <?php elseif ($vipIsGuest): ?>
                                        <a href="/FashionKids/views/pages/login-register.php?redirect=<?= urlencode('/FashionKids/views/pages/product-detail.php?id=' . $product_id) ?>" class="font-weight-bold text-peach">Đăng nhập</a> để nhận chiết khấu VIP (lên đến 15%)
                                    <?php else: ?>
                                        <?php if ($vipNextTierName && $vipPointsNeeded > 0): ?>
                                            Tích lũy thêm <strong><?= number_format($vipPointsNeeded) ?> điểm</strong> để lên <?= htmlspecialchars($vipNextTierName) ?>
                                        <?php else: ?>
                                            Ưu đãi áp dụng tự động theo cấp bậc tài khoản
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </span>
                                <span class="text-slate font-weight-bold text-nowrap flex-shrink-0">Tích lũy: +<?= floor($init_vip_price / 10000) ?> điểm</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chọn Màu Sắc -->
                    <?php
                    $pdpColors = !empty($prod['colors']) && is_array($prod['colors'])
                        ? $prod['colors']
                        : [['name' => 'Bright Pastel', 'code' => '#FAF8F5', 'active' => true]];
                    $defaultColorName = (string)($pdpColors[0]['name'] ?? 'Bright Pastel');
                    ?>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-slate mb-2 d-block">
                            Màu sắc trang phục: <span id="lbn_selectedColorText" class="text-peach font-weight-bold"><?= htmlspecialchars($defaultColorName) ?></span>
                        </label>
                        <div class="d-flex align-items-center flex-wrap" id="lbn_pdpColorOptionsGroup">
                            <?php foreach ($pdpColors as $cIdx => $c): ?>
                                <?php $isActiveColor = ($cIdx === 0); ?>
                                <button type="button"
                                        class="btn btn-sm btn-light border rounded-pill mr-2 mb-2 font-weight-bold text-slate text-nowrap lbn-color-chip-btn <?= $isActiveColor ? 'active border-primary shadow-sm' : '' ?>"
                                        onclick="lbnSelectPdpColor('<?= htmlspecialchars((string)$c['name'], ENT_QUOTES) ?>', this)">
                                    <span class="d-inline-block rounded-circle mr-1 fk-color-dot" data-fk-bg="<?= htmlspecialchars((string)$c['code']) ?>"></span>
                                    <?= htmlspecialchars((string)$c['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="lbn_selectedColorInput" value="<?= htmlspecialchars($defaultColorName) ?>">
                    </div>

                    <!-- Chọn Kích Cỡ & Nút Gợi Ý Size Thước Dây -->
                    <div class="mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-2">
                            <label class="small font-weight-bold text-slate mb-0">
                                Kích cỡ đã chọn: <span id="lbn_selectedSizeText" class="text-peach font-weight-bold text-nowrap"><?= htmlspecialchars($prod['default_size']) ?></span>
                            </label>
                            <button type="button" class="btn btn-pastel-sky btn-sm font-weight-bold shadow-sm text-nowrap flex-shrink-0"
                                    data-toggle="modal" data-target="#modalSuggestSize">
                                <i class="fa-solid fa-ruler-combined text-peach mr-1"></i> Thước Dây Gợi Ý Size
                            </button>
                        </div>

                        <div class="d-flex flex-wrap" id="lbn_pdpSizeOptionsGroup">
                            <?php foreach ($prod['size_range'] as $s): ?>
                                <button type="button"
                                        class="lbn-size-option-btn mr-2 mb-2 <?= $s === $prod['default_size'] ? 'active' : '' ?>"
                                        onclick="lbnSelectPdpSize('<?= htmlspecialchars($s, ENT_QUOTES) ?>', this)">
                                    <?= htmlspecialchars($s) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="lbn_pdpProductId" name="ma_san_pham" value="<?= (int)$prod['id'] ?>">
                        <input type="hidden" id="lbn_selectedSizeInput" name="size" value="<?= htmlspecialchars($prod['default_size']) ?>">
                    </div>

                    <!-- Chọn Số Lượng & Tồn Kho Thực Tế -->
                    <div class="mb-3">
                        <label class="small font-weight-bold text-slate mb-2 d-block">
                            Số lượng đặt mua:
                        </label>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="input-group input-group-sm mr-3 fk-w-120">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-light border font-weight-bold" onclick="lbnChangePdpQty(-1)">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </div>
                                <input type="number" id="lbn_pdpQuantity" name="quantity" class="form-control text-center font-weight-bold"
                                       value="1" min="1" max="<?= max(1, (int)$prod['stock_quantity']) ?>" data-max="<?= (int)$prod['stock_quantity'] ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-light border font-weight-bold" onclick="lbnChangePdpQty(1)">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <span class="small text-muted-custom" id="lbn_pdpStockLabelWrap">
                                Còn <strong class="text-slate" id="lbn_pdpStockCount"><?= (int)$prod['stock_quantity'] ?></strong> sản phẩm sẵn tại kho hàng
                            </span>
                        </div>
                    </div>

                    <!-- Thông báo kiểm tra tồn kho Realtime AJAX -->
                    <div id="lbn_pdpStockAlert" class="alert small py-2 px-3 mb-3 <?= $isOutOfStock ? 'alert-danger d-block' : 'd-none' ?>" role="alert">
                        <?php if ($isOutOfStock): ?>
                            <i class="fa-solid fa-circle-exclamation mr-1"></i><strong>Tạm hết hàng:</strong> Sản phẩm này hiện đã hết hàng trong kho.
                        <?php endif; ?>
                    </div>

                    <!-- Cụm Nút Hành Động: Thêm Vào Giỏ & Mua Ngay -->
                    <div class="form-row mb-3">
                        <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                            <button type="button" id="lbn_btnAddToCartPdp" class="btn btn-pastel-outline btn-block py-3 font-weight-bold <?= $isOutOfStock ? 'disabled' : '' ?>" <?= $isOutOfStock ? 'disabled' : '' ?> onclick="lbnAddToCartPdp()">
                                <i class="fa-solid fa-cart-plus mr-2"></i><span id="lbn_btnAddToCartText"><?= $isOutOfStock ? 'Tạm hết hàng' : 'Thêm Vào Giỏ Hàng' ?></span>
                            </button>
                        </div>
                        <div class="col-12 col-sm-6">
                            <button type="button" id="lbn_btnBuyNowPdp" class="btn btn-pastel-primary btn-block py-3 font-weight-bold shadow <?= $isOutOfStock ? 'disabled' : '' ?>" <?= $isOutOfStock ? 'disabled' : '' ?> onclick="lbnBuyNowPdp()">
                                <i class="fa-solid fa-bolt mr-2"></i><span id="lbn_btnBuyNowText">Mua Ngay Cho Bé</span>
                            </button>
                        </div>
                    </div>

                    <!-- Chia Sẻ Mạng Xã Hội -->
                    <div class="d-flex align-items-center pt-2 border-top small text-muted-custom">
                        <span class="mr-3 font-weight-bold text-slate"><i class="fa-solid fa-share-nodes mr-1"></i>Chia sẻ:</span>
                        <a href="javascript:void(0)" onclick="lbnShareProductSocial('Facebook')" class="btn btn-sm btn-light rounded-circle mr-2 text-primary border shadow-sm">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="lbnShareProductSocial('Zalo')" class="btn btn-sm btn-light rounded-circle mr-2 text-info border shadow-sm">
                            <i class="fa-solid fa-comment-dots"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="lbnShareProductSocial('copy')" class="btn btn-sm btn-light rounded-pill px-3 border shadow-sm font-weight-bold text-slate">
                            <i class="fa-solid fa-link mr-1"></i>Sao chép link
                        </a>
                    </div>
                </div>
            </div>

            <!-- TAB CHI TIẾT THÔNG SỐ VẢI SỢI TRE & HƯỚNG DẪN GIẶT -->
            <div class="row mt-5 pt-4 border-top">
                <div class="col-12">
                    <ul class="nav nav-pills nav-pills-custom mb-3" id="pdpTabList" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-desc-link" data-toggle="pill" href="#tab-desc" role="tab">
                                <i class="fa-solid fa-circle-info mr-1"></i> Mô Tả &amp; Chất Liệu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-cert-link" data-toggle="pill" href="#tab-cert" role="tab">
                                <i class="fa-solid fa-shield-halved mr-1"></i> Chứng Nhận An Toàn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-wash-link" data-toggle="pill" href="#tab-wash" role="tab">
                                <i class="fa-solid fa-shirt mr-1"></i> Hướng Dẫn Giặt Giũ
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4 rounded bg-white border" id="pdpTabContent">
                        <div class="tab-pane fade show active" id="tab-desc" role="tabpanel">
                            <h6 class="font-weight-bold text-slate mb-2">Đặc Tính Sợi Tre Tự Nhiên Cho Làn Da Nhạy Cảm Của Bé</h6>
                            <p class="text-muted-custom small mb-3"><?= htmlspecialchars($prod['description']) ?></p>
                            <div class="p-3 rounded bg-milk-cream border small">
                                <strong>Thành phần chi tiết:</strong> <?= htmlspecialchars($prod['material']) ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-cert" role="tabpanel">
                            <h6 class="font-weight-bold text-slate mb-2">Chứng Nhận Kiểm Định Chất Lượng Quốc Tế</h6>
                            <ul class="list-unstyled small mb-0">
                                <?php foreach ($prod['certifications'] as $cert): ?>
                                    <li class="mb-2 text-slate">
                                        <i class="fa-solid fa-circle-check text-success mr-2"></i><?= htmlspecialchars($cert) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="tab-wash" role="tabpanel">
                            <h6 class="font-weight-bold text-slate mb-2">Bảo Quản Trang Phục Bền Màu &amp; Mềm Mại</h6>
                            <p class="text-muted-custom small mb-0"><?= htmlspecialchars($prod['wash_guide']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         KHỐI DƯỚI PDP: HỆ THỐNG ĐÁNH GIÁ THỰC TẾ TỪ ĐƠN HÀNG ĐÃ HOÀN TẤT
         ====================================================================== -->
    <section class="reviews-section bg-milk-cream py-4 rounded-lg border shadow-sm mb-5" id="pdp_review_section">
        <div class="container-fluid px-3 px-md-4">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap fk-card-header-row">
                    <div class="fk-header-title-grow">
                        <h4 class="font-weight-bold text-slate mb-1">
                            <i class="fa-solid fa-comments text-peach mr-2"></i>Đánh Giá Từ Phụ Huynh Đã Mua Hàng
                        </h4>
                        <p class="text-muted-custom small mb-0">Nhận xét thực tế được xác thực từ các đơn hàng đã giao thành công tại FashionKids</p>
                    </div>
                    <?php if (!empty($reviewEligibility['eligible'])): ?>
                        <button type="button"
                                class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm text-nowrap flex-shrink-0"
                                data-toggle="modal" data-target="#modalReview"
                                onclick="lnktOpenProductReviewModal('<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>', <?= (int)$prod['id'] ?>, <?= (int)$reviewEligibility['eligible_order_id'] ?>)">
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Viết Đánh Giá
                        </button>
                    <?php elseif (!empty($reviewEligibility['already_reviewed'])): ?>
                        <span class="badge badge-pill badge-mint px-3 py-2 font-weight-bold badge-wrap-safe flex-shrink-0">
                            <i class="fa-solid fa-circle-check mr-1"></i>Bạn đã gửi đánh giá sản phẩm này
                        </span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-light border text-muted-custom px-3 py-2 badge-wrap-safe flex-shrink-0">
                            <i class="fa-solid fa-lock mr-1"></i>Đánh giá khả dụng sau khi đơn hàng Hoàn tất
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hàng thống kê tổng quan và tỷ lệ sao thực tế -->
            <div class="row align-items-center mb-4 bg-white p-4 rounded-lg shadow-sm border">
                <div class="col-12 col-md-4 text-center border-right mb-3 mb-md-0">
                    <div class="<?= $reviewCount > 0 ? '' : 'd-none' ?>" id="pdp_rating_stars_card">
                        <div class="display-4 font-weight-bold text-warning mb-1" id="pdp_avg_rating_card"><?= number_format($avgRating, 1) ?></div>
                        <div class="mb-2 text-warning">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                    <div class="mb-2 <?= $reviewCount > 0 ? 'd-none' : '' ?>" id="pdp_no_review_badge_card">
                        <span class="badge badge-pill fk-no-review-badge px-3 py-2">
                            Chưa có đánh giá
                        </span>
                    </div>
                    <span class="text-muted-custom small">Dựa trên <strong id="pdp_review_count_card"><?= $reviewCount ?></strong> nhận xét đã xác thực • <strong id="pdp_buyer_count_card"><?= (int)$buyerCount ?></strong> người mua</span>
                </div>

                <div class="col-12 col-md-8 pl-md-4">
                    <?php for ($star = 5; $star >= 1; $star--):
                        $cnt = $starCounts[$star];
                        $pct = $reviewCount > 0 ? round(($cnt / $reviewCount) * 100) : 0;
                    ?>
                        <div class="d-flex align-items-center <?= $star > 1 ? 'mb-2' : '' ?>">
                            <span class="small font-weight-bold mr-2 text-nowrap fk-w-50"><?= $star ?> <i class="fa-solid fa-star text-warning"></i></span>
                            <div class="progress flex-grow-1 rounded-pill fk-h-10">
                                <div class="progress-bar bg-warning rounded-pill" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100" data-fk-width="<?= $pct ?>"></div>
                            </div>
                            <span class="small text-muted-custom ml-2 font-weight-bold fk-w-35"><?= $cnt ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Danh Sách Đánh Giá Chi Tiết (Cập nhật trực tiếp khi phụ huynh gửi) -->
            <div class="row" id="pdp_review_list">
                <?php if (!empty($dbReviews)): ?>
                    <?php foreach ($dbReviews as $rev):
                        $stars = max(1, min(5, (int)($rev['so_sao_danh_gia'] ?? 5)));
                        $author = (string)($rev['ho_va_ten'] ?? 'Phụ huynh FashionKids');
                        $initials = mb_strtoupper(mb_substr($author, 0, 2, 'UTF-8'), 'UTF-8');
                        $dateFormatted = !empty($rev['ngay_danh_gia']) ? date('d/m/Y H:i', strtotime($rev['ngay_danh_gia'])) : date('d/m/Y');
                    ?>
                        <div class="col-12 mb-3">
                            <div class="card border-0 rounded-lg shadow-sm p-3 bg-white">
                                <div class="d-flex flex-wrap justify-content-between align-items-start fk-card-header-row mb-2">
                                    <div class="d-flex align-items-center min-w-0">
                                        <div class="rounded-circle bg-sky text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 flex-shrink-0 avatar-44">
                                            <?= htmlspecialchars($initials) ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-weight-bold text-slate">
                                                <?= htmlspecialchars($author) ?>
                                                <span class="badge badge-pill badge-mint ml-2 text-nowrap">
                                                    <i class="fa-solid fa-circle-check mr-1"></i>Đã mua tại FashionKids
                                                </span>
                                            </div>
                                            <div class="small">
                                                <span class="text-warning mr-2">
                                                    <?php for ($sIdx = 1; $sIdx <= 5; $sIdx++): ?>
                                                        <i class="fa-<?= $sIdx <= $stars ? 'solid' : 'regular' ?> fa-star"></i>
                                                    <?php endfor; ?>
                                                </span>
                                                <span class="text-muted-custom">Đơn hàng #<?= (int)($rev['ma_don_hang'] ?? 0) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-muted-custom small text-nowrap flex-shrink-0"><?= htmlspecialchars($dateFormatted) ?></span>
                                </div>
                                <p class="text-slate small mb-2">
                                    <?= nl2br(htmlspecialchars((string)($rev['noi_dung_danh_gia'] ?? ''))) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12" id="pdp_empty_review_notice">
                        <div class="card border-0 rounded-lg shadow-sm p-4 bg-white text-center">
                            <i class="fa-regular fa-comment-dots text-peach h3 mb-2"></i>
                            <div class="font-weight-bold text-slate">Chưa có đánh giá nào từ đơn hàng đã hoàn tất</div>
                            <p class="small text-muted-custom mb-0">Hãy mua sắm và hoàn tất đơn hàng để chia sẻ cảm nhận thực tế về sản phẩm này cùng các phụ huynh khác.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ======================================================================
         SẢN PHẨM LIÊN QUAN CÙNG BỘ SƯU TẬP
         ====================================================================== -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-3">
            <h4 class="font-weight-bold text-slate mb-0 fk-header-title-grow">
                <i class="fa-solid fa-sparkles text-peach mr-2"></i>Bé Cũng Sẽ Thích Những Món Này
            </h4>
            <a href="/FashionKids/views/pages/shop-catalog.php" class="small font-weight-bold text-peach text-nowrap flex-shrink-0">
                Xem tất cả cửa hàng <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="row">
            <?php $relShown = 0; foreach ($lbn_products as $other_id => $other_p): ?>
                <?php if ($other_id !== $product_id && $relShown < 6): $relShown++; ?>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card lbn-product-card h-100 shadow-sm border-0">
                            <div class="lbn-product-img-wrap position-relative">
                                <span class="badge badge-pill <?= $other_p['gender_badge'] ?> position-absolute fk-pos-tl-10">
                                    <?= htmlspecialchars($other_p['gender_label']) ?>
                                </span>
                                <a href="/FashionKids/views/pages/product-detail.php?id=<?= $other_id ?>" class="lbn-img-link"><img src="<?= htmlspecialchars($other_p['image']) ?>" alt="<?= htmlspecialchars($other_p['name']) ?>" class="lbn-product-img"></a>
                            </div>
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <small class="text-muted-custom"><?= htmlspecialchars($other_p['category_name']) ?></small>
                                    <h6 class="font-weight-bold text-slate mb-1">
                                        <a href="/FashionKids/views/pages/product-detail.php?id=<?= $other_id ?>" class="text-slate text-decoration-none">
                                            <?= htmlspecialchars($other_p['name']) ?>
                                        </a>
                                    </h6>
                                    <?php
                                        $relReviews = (int)($other_p['reviews'] ?? 0);
                                        $relBuyers  = (int)($other_p['buyer_count'] ?? 0);
                                    ?>
                                    <div class="small mb-2 d-flex flex-wrap align-items-center justify-content-between fk-card-header-row">
                                        <?php if ($relReviews > 0): ?>
                                            <span class="text-warning font-weight-bold text-nowrap">
                                                <i class="fa-solid fa-star"></i> <?= number_format((float)$other_p['rating'], 1) ?>
                                                <span class="text-muted-custom font-weight-normal">(<?= $relReviews ?>)</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-pill fk-no-review-badge text-nowrap">Chưa có đánh giá</span>
                                        <?php endif; ?>
                                        <span class="fk-buyer-count-pill">
                                            <i class="fa-solid fa-bag-shopping text-peach mr-1"></i><?= $relBuyers ?> người mua
                                        </span>
                                    </div>
                                    <small class="text-muted-custom d-block mb-2">Kho: <?= (int)$other_p['stock_quantity'] ?> SP</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="font-weight-bold text-peach h6 mb-0"><?= number_format($other_p['price'], 0, ',', '.') ?> đ</span>
                                    <a href="/FashionKids/views/pages/product-detail.php?id=<?= $other_id ?>" class="btn btn-sm btn-pastel-primary rounded-pill px-3">
                                        Xem Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../modals/modal-suggest-size.php";
require_once __DIR__ . "/../modals/modal-review.php";
require_once __DIR__ . "/../layouts/footer.php";
?>
