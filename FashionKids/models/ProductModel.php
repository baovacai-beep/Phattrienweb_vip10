<?php
/**
 * Lớp Model Sản Phẩm (models/ProductModel.php)
 * Đảm nhiệm: lbn_ (Lê Bảo Ngọc)
 * Khớp chuẩn 100% với CSDL phpMyAdmin (lbn_san_pham & lbn_danh_muc)
 * Tích hợp Stored Function: fn_lbn_kiem_tra_ton_kho & Stored Procedure: sp_lbn_nhap_them_kho_hang
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/entities/SanPhamQuanAo.php';
require_once __DIR__ . '/entities/SanPhamGiayDep.php';

class ProductModel extends Model {
        private array $mockProducts = [
        [
            'ma_san_pham'     => 101,
            'ma_danh_muc'     => 1,
            'ten_san_pham'    => 'Váy Công Chúa Sợi Tre Phối Ren Hoa Cúc Họa Mi',
            'gia_ban'         => 285000.00,
            'so_luong_ton_kho'=> 18,
            'duong_dan_anh'   => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
            'mo_ta_chat_lieu' => 'Vải cotton sợi tre 100% hữu cơ, kháng khuẩn, không gây ngứa da',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Váy đầm công chúa bé gái',
            'gioi_tinh'       => 'be_gai'
        ],
        [
            'ma_san_pham'     => 102,
            'ma_danh_muc'     => 2,
            'ten_san_pham'    => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
            'gia_ban'         => 225000.00,
            'so_luong_ton_kho'=> 24,
            'duong_dan_anh'   => '/FashionKids/images/bo-thun-khung-long.jpg',
            'mo_ta_chat_lieu' => 'Chất sợi tre 4 chiều co giãn thoáng mồ hôi cho bé trai vận động',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Bộ quần áo thun thể thao bé trai',
            'gioi_tinh'       => 'be_trai'
        ],
        [
            'ma_san_pham'     => 103,
            'ma_danh_muc'     => 4,
            'ten_san_pham'    => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
            'gia_ban'         => 210000.00,
            'so_luong_ton_kho'=> 14,
            'duong_dan_anh'   => '/FashionKids/images/giay-sandal-tap-di.jpg',
            'mo_ta_chat_lieu' => 'Đế cao su non uốn dẻo, bảo vệ xương bàn chân non nớt của bé',
            'loai_kich_co'    => 'giay_dep',
            'ten_danh_muc'    => 'Giày sandal tập đi êm chân',
            'gioi_tinh'       => 'chung'
        ],
        [
            'ma_san_pham'     => 104,
            'ma_danh_muc'     => 3,
            'ten_san_pham'    => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
            'gia_ban'         => 320000.00,
            'so_luong_ton_kho'=> 5,
            'duong_dan_anh'   => '/FashionKids/images/ao-khoac-cardigan.jpg',
            'mo_ta_chat_lieu' => 'Sợi tre dệt kim UPF 50+ chống tia cực tím, cúc gỗ tự nhiên',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Áo khoác dạo phố năng động',
            'gioi_tinh'       => 'chung'
        ],
        [
            'ma_san_pham'     => 105,
            'ma_danh_muc'     => 1,
            'ten_san_pham'    => 'Váy Công Chúa Tùng Xòe Pastel Nơ Bản To Dự Tiệc',
            'gia_ban'         => 365000.00,
            'so_luong_ton_kho'=> 11,
            'duong_dan_anh'   => '/FashionKids/images/vay-cong-chua-no-pastel.jpg',
            'mo_ta_chat_lieu' => 'Lớp lót 100% sợi tre kháng khuẩn phối voan tùng xòe dự tiệc',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Váy đầm công chúa bé gái',
            'gioi_tinh'       => 'be_gai'
        ],
        [
            'ma_san_pham'     => 106,
            'ma_danh_muc'     => 2,
            'ten_san_pham'    => 'Bộ Thun Sợi Tre Cộc Tay Gấu Nhí Thoáng Khí Mùa Hè',
            'gia_ban'         => 195000.00,
            'so_luong_ton_kho'=> 32,
            'duong_dan_anh'   => '/FashionKids/images/bo-thun-gau-nhi.jpg',
            'mo_ta_chat_lieu' => '100% sợi tre tự nhiên làm mát, thêu gấu nhí đáng yêu',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Bộ quần áo thun thể thao bé trai',
            'gioi_tinh'       => 'chung'
        ],
        [
            'ma_san_pham'     => 107,
            'ma_danh_muc'     => 3,
            'ten_san_pham'    => 'Áo Khoác Hoodie Tai Thỏ Sợi Tre Ấm Áp Cho Bé Gái',
            'gia_ban'         => 340000.00,
            'so_luong_ton_kho'=> 4,
            'duong_dan_anh'   => '/FashionKids/images/ao-khoac-hoodie-tai-tho.jpg',
            'mo_ta_chat_lieu' => 'Nỉ bông sợi tre hữu cơ kháng khuẩn, mũ phối tai thỏ',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Áo khoác dạo phố năng động',
            'gioi_tinh'       => 'be_gai'
        ],
        [
            'ma_san_pham'     => 108,
            'ma_danh_muc'     => 4,
            'ten_san_pham'    => 'Giày Sandal Bé Trai Quai Dán Chống Trượt Siêu Nhẹ',
            'gia_ban'         => 245000.00,
            'so_luong_ton_kho'=> 16,
            'duong_dan_anh'   => '/FashionKids/images/giay-sandal-be-trai.jpg',
            'mo_ta_chat_lieu' => 'Quai vải canvas phối sợi tre êm cổ chân, đế rãnh chống trượt',
            'loai_kich_co'    => 'giay_dep',
            'ten_danh_muc'    => 'Giày sandal tập đi êm chân',
            'gioi_tinh'       => 'be_trai'
        ],
        [
            'ma_san_pham'     => 109,
            'ma_danh_muc'     => 3,
            'ten_san_pham'    => 'Áo Khoác Bomber Sợi Tre Phi Hành Gia Nhí Bé Trai',
            'gia_ban'         => 350000.00,
            'so_luong_ton_kho'=> 9,
            'duong_dan_anh'   => '/FashionKids/images/ao-khoac-bomber.jpg',
            'mo_ta_chat_lieu' => 'Chất vải sợi tre 2 lớp cản gió, huy hiệu phi hành gia thêu nổi',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Áo khoác dạo phố năng động',
            'gioi_tinh'       => 'be_trai'
        ]
    ];

    private static bool $catalogSeeded = false;

    public function __construct() {
        parent::__construct();
        $this->ensureCatalogProductsInDb();
    }

    /**
     * Đảm bảo 9 sản phẩm catalog chuẩn (101..109) luôn tồn tại trong bảng lbn_san_pham.
     * Chỉ chèn INSERT IGNORE các mã còn thiếu, không ghi đè tồn kho/giá thực tế đang vận hành.
     */
    public function ensureCatalogProductsInDb(): void {
        if (self::$catalogSeeded || !$this->hasConnection()) {
            return;
        }
        self::$catalogSeeded = true;
        try {
            $cntStmt = $this->db->query("SELECT COUNT(*) AS cnt FROM lbn_san_pham WHERE ma_san_pham BETWEEN 101 AND 109");
            $cnt = (int)($cntStmt->fetch()['cnt'] ?? 0);
            if ($cnt >= count($this->mockProducts)) {
                return;
            }
            $ins = $this->db->prepare("
                INSERT IGNORE INTO lbn_san_pham
                    (ma_san_pham, ma_danh_muc, ten_san_pham, gia_ban, so_luong_ton_kho, duong_dan_anh, mo_ta_chat_lieu, loai_kich_co)
                VALUES
                    (:id, :cat, :name, :price, :stock, :img, :desc, :type)
            ");
            foreach ($this->mockProducts as $p) {
                $ins->execute([
                    ':id'    => (int)$p['ma_san_pham'],
                    ':cat'   => (int)$p['ma_danh_muc'],
                    ':name'  => (string)$p['ten_san_pham'],
                    ':price' => (float)$p['gia_ban'],
                    ':stock' => (int)$p['so_luong_ton_kho'],
                    ':img'   => (string)$p['duong_dan_anh'],
                    ':desc'  => (string)$p['mo_ta_chat_lieu'],
                    ':type'  => (string)$p['loai_kich_co'],
                ]);
            }
        } catch (PDOException $e) {
            error_log('[lbn] ensureCatalogProductsInDb: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách sản phẩm theo danh mục và lọc giá
     */
    public function layDanhSachSanPham(?int $maDanhMuc = null, float $minPrice = 0, float $maxPrice = 10000000): array {
        if ($this->hasConnection()) {
            try {
                $sql = "SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                               COALESCE(fn_tgb_tinh_sao_trung_binh_san_pham(sp.ma_san_pham), 0.0) AS sao_trung_binh,
                               (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                               COALESCE(fn_tgb_dem_so_nguoi_mua(sp.ma_san_pham), 0) AS so_nguoi_mua
                        FROM lbn_san_pham sp 
                        JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                        WHERE sp.ma_san_pham >= 101 AND sp.gia_ban BETWEEN :minPrice AND :maxPrice";
                $params = [':minPrice' => $minPrice, ':maxPrice' => $maxPrice];

                if ($maDanhMuc !== null && $maDanhMuc > 0) {
                    $sql .= " AND sp.ma_danh_muc = :maDanhMuc";
                    $params[':maDanhMuc'] = $maDanhMuc;
                }
                $sql .= " ORDER BY sp.ma_san_pham DESC";

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (PDOException $e) {
                // Dự phòng khi Shared Hosting chặn Stored Function (fn_tgb_*)
                try {
                    $fallbackSql = "SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                                           ROUND(COALESCE((SELECT AVG(dg.so_sao_danh_gia) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham), 0.0), 1) AS sao_trung_binh,
                                           (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                                           (SELECT COUNT(DISTINCT CASE WHEN dh.ma_nguoi_dung IS NOT NULL THEN CONCAT('U', dh.ma_nguoi_dung) ELSE CONCAT('O', dh.ma_don_hang) END)
                                            FROM tgb_chi_tiet_don_hang ct
                                            JOIN lnkt_don_hang dh ON ct.ma_don_hang = dh.ma_don_hang
                                            WHERE ct.ma_san_pham = sp.ma_san_pham
                                              AND dh.trang_thai_don_hang = 'hoan_tat'
                                              AND NOT EXISTS (SELECT 1 FROM lnkt_yeu_cau_hoan_hang hh WHERE hh.ma_don_hang = dh.ma_don_hang AND hh.trang_thai_hoan = 'hoan_tat_hoan')
                                           ) AS so_nguoi_mua
                                    FROM lbn_san_pham sp
                                    JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                                    WHERE sp.ma_san_pham >= 101 AND sp.gia_ban BETWEEN :minPrice AND :maxPrice";
                    if ($maDanhMuc !== null && $maDanhMuc > 0) {
                        $fallbackSql .= " AND sp.ma_danh_muc = :maDanhMuc";
                    }
                    $fallbackSql .= " ORDER BY sp.ma_san_pham DESC";
                    $stmtFb = $this->db->prepare($fallbackSql);
                    $stmtFb->execute($params);
                    $rowsFb = $stmtFb->fetchAll();
                    if (!empty($rowsFb)) {
                        return $rowsFb;
                    }
                } catch (PDOException $e2) {
                    error_log("Lỗi lấy danh sách sản phẩm: " . $e2->getMessage());
                }
            }
        }
        return $this->mockProducts;
    }

    public function getAllProducts(array $filters = []): array {
        $catId = $filters['category_id'] ?? null;
        return $this->layDanhSachSanPham($catId);
    }

    public function getFeaturedProducts(int $limit = 8): array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                                                   COALESCE(fn_tgb_tinh_sao_trung_binh_san_pham(sp.ma_san_pham), 0.0) AS sao_trung_binh,
                                                   (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                                                   COALESCE(fn_tgb_dem_so_nguoi_mua(sp.ma_san_pham), 0) AS so_nguoi_mua
                                            FROM lbn_san_pham sp 
                                            JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                                            WHERE sp.ma_san_pham >= 101 AND sp.so_luong_ton_kho > 0 
                                            ORDER BY sp.ma_san_pham ASC 
                                            LIMIT :limit");
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (PDOException $e) {
                try {
                    $stmtFb = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                                                         ROUND(COALESCE((SELECT AVG(dg.so_sao_danh_gia) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham), 0.0), 1) AS sao_trung_binh,
                                                         (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                                                         (SELECT COUNT(DISTINCT CASE WHEN dh.ma_nguoi_dung IS NOT NULL THEN CONCAT('U', dh.ma_nguoi_dung) ELSE CONCAT('O', dh.ma_don_hang) END)
                                                          FROM tgb_chi_tiet_don_hang ct
                                                          JOIN lnkt_don_hang dh ON ct.ma_don_hang = dh.ma_don_hang
                                                          WHERE ct.ma_san_pham = sp.ma_san_pham
                                                            AND dh.trang_thai_don_hang = 'hoan_tat'
                                                            AND NOT EXISTS (SELECT 1 FROM lnkt_yeu_cau_hoan_hang hh WHERE hh.ma_don_hang = dh.ma_don_hang AND hh.trang_thai_hoan = 'hoan_tat_hoan')
                                                         ) AS so_nguoi_mua
                                                  FROM lbn_san_pham sp
                                                  JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                                                  WHERE sp.ma_san_pham >= 101 AND sp.so_luong_ton_kho > 0
                                                  ORDER BY sp.ma_san_pham ASC
                                                  LIMIT :limit");
                    $stmtFb->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $stmtFb->execute();
                    $rowsFb = $stmtFb->fetchAll();
                    if (!empty($rowsFb)) {
                        return $rowsFb;
                    }
                } catch (PDOException $e2) {}
            }
        }
        return array_slice($this->mockProducts, 0, $limit);
    }

    /**
     * Chi tiết sản phẩm phục vụ trang PDP
     */
    public function layChiTietSanPham(int $id): ?array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                                                   COALESCE(fn_tgb_tinh_sao_trung_binh_san_pham(sp.ma_san_pham), 0.0) AS sao_trung_binh,
                                                   (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                                                   COALESCE(fn_tgb_dem_so_nguoi_mua(sp.ma_san_pham), 0) AS so_nguoi_mua
                                            FROM lbn_san_pham sp 
                                            JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                                            WHERE sp.ma_san_pham = :id 
                                            LIMIT 1");
                $stmt->execute([':id' => $id]);
                $product = $stmt->fetch();
                return $product ?: null;
            } catch (PDOException $e) {
                try {
                    $stmtFb = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                                                         ROUND(COALESCE((SELECT AVG(dg.so_sao_danh_gia) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham), 0.0), 1) AS sao_trung_binh,
                                                         (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                                                         (SELECT COUNT(DISTINCT CASE WHEN dh.ma_nguoi_dung IS NOT NULL THEN CONCAT('U', dh.ma_nguoi_dung) ELSE CONCAT('O', dh.ma_don_hang) END)
                                                          FROM tgb_chi_tiet_don_hang ct
                                                          JOIN lnkt_don_hang dh ON ct.ma_don_hang = dh.ma_don_hang
                                                          WHERE ct.ma_san_pham = sp.ma_san_pham
                                                            AND dh.trang_thai_don_hang = 'hoan_tat'
                                                            AND NOT EXISTS (SELECT 1 FROM lnkt_yeu_cau_hoan_hang hh WHERE hh.ma_don_hang = dh.ma_don_hang AND hh.trang_thai_hoan = 'hoan_tat_hoan')
                                                         ) AS so_nguoi_mua
                                                  FROM lbn_san_pham sp
                                                  JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                                                  WHERE sp.ma_san_pham = :id
                                                  LIMIT 1");
                    $stmtFb->execute([':id' => $id]);
                    $productFb = $stmtFb->fetch();
                    return $productFb ?: null;
                } catch (PDOException $e2) {
                    error_log("Lỗi chi tiết sản phẩm: " . $e2->getMessage());
                }
            }
        }

        foreach ($this->mockProducts as $p) {
            if ($p['ma_san_pham'] === $id) return $p;
        }
        return $this->mockProducts[0] ?? null;
    }

    public function getProductById(int $productId): ?array {
        return $this->layChiTietSanPham($productId);
    }

    /**
     * Gọi Stored Function kiểm tra tồn kho trước khi bấm mua
     */
    public function kiemTraTonKho(int $maSanPham, int $soLuongCanMua): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT fn_lbn_kiem_tra_ton_kho(:id, :sl) AS du_hang");
                $stmt->execute([':id' => $maSanPham, ':sl' => $soLuongCanMua]);
                $result = $stmt->fetch();
                return (bool)($result['du_hang'] ?? false);
            } catch (PDOException $e) {
                try {
                    $stmt = $this->db->prepare("SELECT so_luong_ton_kho FROM lbn_san_pham WHERE ma_san_pham = :id");
                    $stmt->execute([':id' => $maSanPham]);
                    $row = $stmt->fetch();
                    return $row ? ($row['so_luong_ton_kho'] >= $soLuongCanMua) : true;
                } catch (PDOException $e2) {}
            }
        }
        return true;
    }

    /**
     * Gợi ý kích cỡ thông minh qua thực thể Đa hình OOP
     */
    public function suggestSize(int $categoryId, float $measurement): string {
        if ($categoryId === 4) {
            $entity = new SanPhamGiayDep();
        } else {
            $entity = new SanPhamQuanAo();
        }
        return $entity->duDoanSize($measurement);
    }

    /**
     * Nhập thêm kho hàng qua sp_lbn_nhap_them_kho_hang (có fallback UPDATE trực tiếp cho Hosting)
     */
    public function updateStock(int $productId, int $quantityChange): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lbn_nhap_them_kho_hang(:pid, :qty)");
                $res = $stmt->execute([':pid' => $productId, ':qty' => $quantityChange]);
                $stmt->closeCursor();
                return $res;
            } catch (PDOException $e) {
                if ((string)$e->getCode() === '45000' || strpos($e->getMessage(), '1644') !== false) {
                    return false;
                }
                try {
                    if ($quantityChange === 0) {
                        return true;
                    }
                    if ($quantityChange > 0) {
                        $fb = $this->db->prepare("UPDATE lbn_san_pham SET so_luong_ton_kho = so_luong_ton_kho + :qty WHERE ma_san_pham = :pid");
                        return $fb->execute([':qty' => $quantityChange, ':pid' => $productId]);
                    }
                    $fb = $this->db->prepare(
                        "UPDATE lbn_san_pham SET so_luong_ton_kho = so_luong_ton_kho + :qty
                         WHERE ma_san_pham = :pid AND (so_luong_ton_kho + :qtyCheck) >= 0"
                    );
                    $fb->execute([':qty' => $quantityChange, ':qtyCheck' => $quantityChange, ':pid' => $productId]);
                    return $fb->rowCount() > 0;
                } catch (PDOException $e2) {
                    error_log('[lbn] updateStock fallback error: ' . $e2->getMessage());
                    return false;
                }
            }
        }
        return true;
    }

    // =========================================================================
    // GIAI ĐOẠN 4 - LÊ BẢO NGỌC (lbn_): CÁC PHƯƠNG THỨC WEBSERVICE JSON & AJAX
    // =========================================================================

    /**
     * Bản đồ ánh xạ Danh mục chuẩn lbn_danh_muc <-> Giao diện Catalog/Inventory
     */
    private function getCategoryMetadataMap(): array {
        return [
            1 => [
                'id'         => 1,
                'slug'       => 'vay_cong_chua',
                'name'       => 'Váy công chúa',
                'db_name'    => 'Váy đầm công chúa bé gái',
                'gender'     => 'girl',
                'db_gender'  => 'be_gai',
                'type'       => 'quan_ao',
                'type_label' => 'Quần áo (Size 1-5)',
                'size_range' => 'Size 1 - Size 5',
                'icon'       => 'fa-person-dress text-peach'
            ],
            2 => [
                'id'         => 2,
                'slug'       => 'bo_thun',
                'name'       => 'Bộ thun',
                'db_name'    => 'Bộ quần áo thun thể thao bé trai',
                'gender'     => 'boy',
                'db_gender'  => 'be_trai',
                'type'       => 'quan_ao',
                'type_label' => 'Quần áo (Size 1-5)',
                'size_range' => 'Size 1 - Size 5',
                'icon'       => 'fa-shirt text-info'
            ],
            3 => [
                'id'         => 3,
                'slug'       => 'ao_khoac',
                'name'       => 'Áo khoác',
                'db_name'    => 'Áo khoác dạo phố năng động',
                'gender'     => 'unisex',
                'db_gender'  => 'chung',
                'type'       => 'quan_ao',
                'type_label' => 'Quần áo (Size 2-5)',
                'size_range' => 'Size 2 - Size 5',
                'icon'       => 'fa-vest-patches text-success'
            ],
            4 => [
                'id'         => 4,
                'slug'       => 'giay_sandal',
                'name'       => 'Giày sandal',
                'db_name'    => 'Giày sandal tập đi êm chân',
                'gender'     => 'unisex',
                'db_gender'  => 'chung',
                'type'       => 'giay_dep',
                'type_label' => 'Giày dép (Size 21-30)',
                'size_range' => 'Size 21 - Size 28',
                'icon'       => 'fa-shoe-prints text-warning'
            ]
        ];
    }

    /**
     * Chuẩn hóa 1 dòng dữ liệu sản phẩm để phục vụ đồng nhất cho cả MH 02, MH 03 và MH 13
     */
    private function normalizeProductRow(array $row, int $index = 0): array {
        $catMap = $this->getCategoryMetadataMap();
        $id     = (int)($row['ma_san_pham'] ?? $row['id'] ?? (101 + $index));
        $catId  = (int)($row['ma_danh_muc'] ?? $row['category_id'] ?? 1);
        if (!isset($catMap[$catId])) {
            $catId = 1;
        }
        $catMeta = $catMap[$catId];

        // Khi có CSDL thật, luôn ưu tiên tồn kho thực trong bảng lbn_san_pham (tránh override phiên che khuất DB)
        $stockOverrides = $this->hasConnection() ? [] : ($_SESSION['lbn_stock_overrides'] ?? []);
        $stock = isset($stockOverrides[$id])
            ? (int)$stockOverrides[$id]
            : (int)($row['so_luong_ton_kho'] ?? $row['stock_quantity'] ?? 10);

        $rawGender = strtolower((string)($row['gioi_tinh'] ?? $row['gender'] ?? $catMeta['db_gender']));
        if (in_array($rawGender, ['be_gai', 'girl'], true)) {
            $genderUi    = 'girl';
            $genderDb    = 'be_gai';
            $genderLabel = 'Bé gái';
            $genderBadge = 'badge-peach';
        } elseif (in_array($rawGender, ['be_trai', 'boy'], true)) {
            $genderUi    = 'boy';
            $genderDb    = 'be_trai';
            $genderLabel = 'Bé trai';
            $genderBadge = 'badge-sky';
        } else {
            $genderUi    = 'unisex';
            $genderDb    = 'chung';
            $genderLabel = 'Unisex';
            $genderBadge = 'badge-gold';
        }

        $rawImg = trim((string)($row['duong_dan_anh'] ?? $row['image'] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg'));
        if ($rawImg !== '' && strpos($rawImg, '/') === false) {
            $rawImg = '/FashionKids/images/' . $rawImg;
        }

        $skuNum = $id >= 100 ? ($id - 100) : $id;
        $sku    = $row['sku'] ?? ('FK-LBN-' . str_pad((string)$skuNum, 2, '0', STR_PAD_LEFT));
        $price  = (float)($row['gia_ban'] ?? $row['price'] ?? 250000);
        $name   = (string)($row['ten_san_pham'] ?? $row['name'] ?? 'Sản phẩm thời trang trẻ em');
        $desc   = (string)($row['mo_ta_chat_lieu'] ?? $row['material'] ?? $row['description'] ?? '100% sợi tre hữu cơ kháng khuẩn tự nhiên');
        $type   = (string)($row['loai_kich_co'] ?? $row['type'] ?? $catMeta['type']);

        $reviewCount = max(0, (int)($row['tong_danh_gia'] ?? $row['reviews'] ?? 0));
        $avgRating   = $reviewCount > 0 ? round((float)($row['sao_trung_binh'] ?? $row['rating'] ?? 0.0), 1) : 0.0;
        $buyerCount  = max(0, (int)($row['so_nguoi_mua'] ?? $row['buyer_count'] ?? 0));

        return [
            // Các trường chuẩn CSDL
            'ma_san_pham'      => $id,
            'ma_danh_muc'      => $catId,
            'ten_san_pham'     => $name,
            'gia_ban'          => $price,
            'so_luong_ton_kho' => $stock,
            'duong_dan_anh'    => $rawImg,
            'mo_ta_chat_lieu'  => $desc,
            'loai_kich_co'     => $type,
            'ten_danh_muc'     => $catMeta['db_name'],
            'gioi_tinh'        => $genderDb,
            // Các trường chuẩn giao diện JSON / MH 02 / MH 03 / MH 13
            'id'               => $id,
            'sku'              => $sku,
            'image'            => $rawImg,
            'category_id'      => $catId,
            'category_slug'    => $catMeta['slug'],
            'category_name'    => $catMeta['name'],
            'name'             => $name,
            'gender'           => $genderUi,
            'gender_label'     => $genderLabel,
            'gender_badge'     => $genderBadge,
            'price'            => $price,
            'price_formatted'  => number_format($price, 0, ',', '.') . ' đ',
            'stock_quantity'   => $stock,
            'is_low_stock'     => ($stock <= 5),
            'rating'           => $avgRating,
            'sao_trung_binh'   => $avgRating,
            'reviews'          => $reviewCount,
            'tong_danh_gia'    => $reviewCount,
            'buyer_count'      => $buyerCount,
            'so_nguoi_mua'     => $buyerCount,
            'sold_count'       => $buyerCount,
            'is_bamboo'        => (int)($row['is_bamboo'] ?? 1),
            'bamboo_label'     => (string)($row['bamboo_label'] ?? 'Vải Sợi Tre Kháng Khuẩn'),
            'size_range'       => (string)($row['size_range'] ?? ($type === 'giay_dep' ? 'Size 21 - Size 28' : $catMeta['size_range'])),
            'type'             => $type,
            'type_label'       => ($type === 'giay_dep') ? 'Giày dép (Size 21-30)' : 'Quần áo (Size 1-5)',
            'material'         => $desc,
            'icon'             => $catMeta['icon']
        ];
    }

    /**
     * Lấy toàn bộ danh sách sản phẩm đã chuẩn hóa (kết hợp CSDL, mockProducts và sản phẩm thêm mới trong phiên)
     */
    public function layTatCaSanPhamChuanHoa(): array {
        $byId = [];
        $loadedFromDb = false;

        // 1. Khi có kết nối CSDL, đồng bộ trực tiếp toàn bộ sản phẩm catalog (>= 101) từ bảng lbn_san_pham & lbn_danh_muc
        if ($this->hasConnection()) {
            try {
                $sql = "SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                               COALESCE(fn_tgb_tinh_sao_trung_binh_san_pham(sp.ma_san_pham), 0.0) AS sao_trung_binh,
                               (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                               COALESCE(fn_tgb_dem_so_nguoi_mua(sp.ma_san_pham), 0) AS so_nguoi_mua
                        FROM lbn_san_pham sp 
                        JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                        WHERE sp.ma_san_pham >= 101
                        ORDER BY sp.ma_san_pham ASC";
                $rows = $this->db->query($sql)->fetchAll();
                if (!empty($rows)) {
                    $loadedFromDb = true;
                    foreach ($rows as $idx => $dbRow) {
                        $norm = $this->normalizeProductRow($dbRow, $idx);
                        $byId[$norm['id']] = $norm;
                    }
                }
            } catch (PDOException $e) {
                try {
                    $fallbackSql = "SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh,
                                           ROUND(COALESCE((SELECT AVG(dg.so_sao_danh_gia) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham), 0.0), 1) AS sao_trung_binh,
                                           (SELECT COUNT(*) FROM tgb_danh_gia_san_pham dg WHERE dg.ma_san_pham = sp.ma_san_pham) AS tong_danh_gia,
                                           (SELECT COUNT(DISTINCT CASE WHEN dh.ma_nguoi_dung IS NOT NULL THEN CONCAT('U', dh.ma_nguoi_dung) ELSE CONCAT('O', dh.ma_don_hang) END)
                                            FROM tgb_chi_tiet_don_hang ct
                                            JOIN lnkt_don_hang dh ON ct.ma_don_hang = dh.ma_don_hang
                                            WHERE ct.ma_san_pham = sp.ma_san_pham
                                              AND dh.trang_thai_don_hang = 'hoan_tat'
                                              AND NOT EXISTS (SELECT 1 FROM lnkt_yeu_cau_hoan_hang hh WHERE hh.ma_don_hang = dh.ma_don_hang AND hh.trang_thai_hoan = 'hoan_tat_hoan')
                                           ) AS so_nguoi_mua
                                    FROM lbn_san_pham sp
                                    JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                                    WHERE sp.ma_san_pham >= 101
                                    ORDER BY sp.ma_san_pham ASC";
                    $rowsFb = $this->db->query($fallbackSql)->fetchAll();
                    if (!empty($rowsFb)) {
                        $loadedFromDb = true;
                        foreach ($rowsFb as $idx => $dbRow) {
                            $norm = $this->normalizeProductRow($dbRow, $idx);
                            $byId[$norm['id']] = $norm;
                        }
                    }
                } catch (PDOException $e2) {
                    error_log("Lỗi đồng bộ lbn_san_pham: " . $e2->getMessage());
                }
            }
        }

        // 2. Dự phòng khi chạy offline không có CSDL
        if (!$loadedFromDb) {
            foreach ($this->mockProducts as $idx => $item) {
                $norm = $this->normalizeProductRow($item, $idx);
                $byId[$norm['id']] = $norm;
            }
            if (!empty($_SESSION['lbn_custom_products']) && is_array($_SESSION['lbn_custom_products'])) {
                foreach ($_SESSION['lbn_custom_products'] as $customRow) {
                    $norm = $this->normalizeProductRow($customRow);
                    $byId[$norm['id']] = $norm;
                }
            }
        }

        return $this->sortCatalogItemsForDisplay(array_values($byId));
    }

    /**
     * Đưa các sản phẩm mới tạo (> 109) lên đầu theo thứ tự mới nhất trước, giữ nhóm 101..109 theo thứ tự chuẩn.
     */
    private function sortCatalogItemsForDisplay(array $items): array {
        usort($items, static function (array $a, array $b): int {
            $idA = (int)($a['id'] ?? $a['ma_san_pham'] ?? 0);
            $idB = (int)($b['id'] ?? $b['ma_san_pham'] ?? 0);
            $isNewA = $idA > 109;
            $isNewB = $idB > 109;
            if ($isNewA !== $isNewB) {
                return $isNewA ? -1 : 1;
            }
            if ($isNewA && $isNewB) {
                return $idB <=> $idA;
            }
            return $idA <=> $idB;
        });
        return $items;
    }

    /**
     * Lọc đa tiêu chí & Tìm kiếm sản phẩm phục vụ AJAX MH 02 (shop-catalog.php)
     */
    public function locSanPhamDaTieuChi(array $filters = []): array {
        $allProducts    = $this->layTatCaSanPhamChuanHoa();
        $selectedCat    = trim((string)($filters['category'] ?? 'all'));
        $selectedGender = trim((string)($filters['gender'] ?? 'all'));
        $minPrice       = isset($filters['min_price']) ? (float)$filters['min_price'] : 0.0;
        $maxPrice       = isset($filters['max_price']) ? (float)$filters['max_price'] : 10000000.0;
        $keyword        = mb_strtolower(trim((string)($filters['keyword'] ?? '')), 'UTF-8');
        $onlyBamboo     = !empty($filters['only_bamboo']) && $filters['only_bamboo'] !== '0' && $filters['only_bamboo'] !== 'false';
        $sortMode       = trim((string)($filters['sort'] ?? 'newest'));

        $filtered = array_filter($allProducts, function (array $prod) use ($selectedCat, $selectedGender, $minPrice, $maxPrice, $keyword, $onlyBamboo) {
            if ($selectedCat !== '' && $selectedCat !== 'all') {
                $matchCat = ($prod['category_slug'] === $selectedCat) || ((string)$prod['category_id'] === $selectedCat);
                if (!$matchCat) return false;
            }

            if ($selectedGender !== '' && $selectedGender !== 'all') {
                $matchGender = ($prod['gender'] === $selectedGender) || ($prod['gioi_tinh'] === $selectedGender);
                if (!$matchGender) return false;
            }

            if ($prod['price'] < $minPrice || $prod['price'] > $maxPrice) {
                return false;
            }

            if ($onlyBamboo && empty($prod['is_bamboo'])) {
                return false;
            }

            if ($keyword !== '') {
                $haystack = mb_strtolower(
                    $prod['name'] . ' ' . $prod['category_name'] . ' ' . $prod['sku'] . ' ' . $prod['material'],
                    'UTF-8'
                );
                if (mb_strpos($haystack, $keyword, 0, 'UTF-8') === false) {
                    return false;
                }
            }

            return true;
        });

        $result = array_values($filtered);

        usort($result, function (array $a, array $b) use ($sortMode) {
            if ($sortMode === 'price_asc') {
                return $a['price'] <=> $b['price'];
            }
            if ($sortMode === 'price_desc') {
                return $b['price'] <=> $a['price'];
            }
            return $b['id'] <=> $a['id'];
        });

        return $result;
    }

    /**
     * Thống kê số lượng sản phẩm theo từng danh mục (lbn_count_products_by_category)
     */
    public function demSanPhamTheoDanhMuc(): array {
        $allProducts = $this->layTatCaSanPhamChuanHoa();
        $counts = [
            'all'           => count($allProducts),
            'vay_cong_chua' => 0,
            'bo_thun'       => 0,
            'ao_khoac'      => 0,
            'giay_sandal'   => 0,
            'by_id'         => [1 => 0, 2 => 0, 3 => 0, 4 => 0]
        ];

        foreach ($allProducts as $p) {
            $slug  = $p['category_slug'];
            $catId = (int)$p['category_id'];
            if (isset($counts[$slug])) {
                $counts[$slug]++;
            }
            if (isset($counts['by_id'][$catId])) {
                $counts['by_id'][$catId]++;
            }
        }

        return $counts;
    }

    /**
     * Kiểm tra tồn kho chi tiết qua Stored Function fn_lbn_kiem_tra_ton_kho phục vụ AJAX MH 03 & MH 02
     */
    public function kiemTraTonKhoChiTiet(int $maSanPham, int $soLuongCanMua): array {
        $foundProduct = null;
        $tonKhoHienTai = 0;
        $duHang = false;
        $usedRoutine = false;

        if ($this->hasConnection()) {
            try {
                $checkStmt = $this->db->prepare("
                    SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh
                    FROM lbn_san_pham sp
                    LEFT JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                    WHERE sp.ma_san_pham = :id
                    LIMIT 1
                ");
                $checkStmt->execute([':id' => $maSanPham]);
                $dbRow = $checkStmt->fetch();
                $checkStmt->closeCursor();

                if ($dbRow) {
                    $foundProduct = $this->normalizeProductRow($dbRow);
                    $tonKhoHienTai = (int)$dbRow['so_luong_ton_kho'];

                    try {
                        $fnStmt = $this->db->prepare("SELECT fn_lbn_kiem_tra_ton_kho(:id, :sl) AS du_hang");
                        $fnStmt->execute([':id' => $maSanPham, ':sl' => $soLuongCanMua]);
                        $fnRes = $fnStmt->fetch();
                        $fnStmt->closeCursor();
                        if ($fnRes !== false && isset($fnRes['du_hang'])) {
                            $duHang = (bool)$fnRes['du_hang'] && ($tonKhoHienTai >= $soLuongCanMua);
                            $usedRoutine = true;
                        } else {
                            $duHang = ($tonKhoHienTai >= $soLuongCanMua);
                        }
                    } catch (PDOException $fnEx) {
                        // Dự phòng khi Shared Hosting không hỗ trợ Stored Function
                        $duHang = ($tonKhoHienTai >= $soLuongCanMua);
                    }
                } else {
                    $tonKhoHienTai = 0;
                    $duHang = false;
                }
            } catch (PDOException $e) {
                error_log("Lỗi gọi fn_lbn_kiem_tra_ton_kho: " . $e->getMessage());
            }
        } else {
            $allProducts = $this->layTatCaSanPhamChuanHoa();
            foreach ($allProducts as $p) {
                if ((int)$p['id'] === $maSanPham) {
                    $foundProduct = $p;
                    break;
                }
            }
            $tonKhoHienTai = $foundProduct ? (int)$foundProduct['stock_quantity'] : 0;
            $duHang = ($foundProduct !== null && $tonKhoHienTai >= $soLuongCanMua);
        }

        return [
            'product_id'       => $maSanPham,
            'product_name'     => $foundProduct['name'] ?? ('Sản phẩm #' . $maSanPham),
            'sku'              => $foundProduct['sku'] ?? ('FK-LBN-' . $maSanPham),
            'price'            => $foundProduct['price'] ?? 285000,
            'image'            => $foundProduct['image'] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
            'requested_qty'    => $soLuongCanMua,
            'available_stock'  => $tonKhoHienTai,
            'du_hang'          => $duHang,
            'is_low_stock'     => ($tonKhoHienTai <= 5),
            'routine'          => 'fn_lbn_kiem_tra_ton_kho',
            'used_sql_routine' => $usedRoutine
        ];
    }

    /**
     * Cập nhật số lượng tồn kho trên trang Quản trị Kho
     */
    public function capNhatTonKhoSanPham(int $maSanPham, int $soLuongMoi, string $mode = 'set'): array {
        if ($soLuongMoi < 0) {
            throw new InvalidArgumentException('Số lượng hàng tồn kho không được là số âm!');
        }

        $targetProd = null;
        if ($this->hasConnection()) {
            $rawRow = $this->layChiTietSanPham($maSanPham);
            if ($rawRow) {
                $targetProd = $this->normalizeProductRow($rawRow);
            }
        }
        if (!$targetProd) {
            foreach ($this->layTatCaSanPhamChuanHoa() as $p) {
                if ((int)$p['id'] === $maSanPham) {
                    $targetProd = $p;
                    break;
                }
            }
        }

        $oldStock = $targetProd ? (int)$targetProd['stock_quantity'] : 0;
        $finalStock = ($mode === 'add') ? ($oldStock + $soLuongMoi) : $soLuongMoi;
        if ($finalStock < 0) {
            $finalStock = 0;
        }
        $delta = $finalStock - $oldStock;

        $dbUpdated = false;
        if ($this->hasConnection()) {
            try {
                if ($delta > 0) {
                    try {
                        $stmt = $this->db->prepare("CALL sp_lbn_nhap_them_kho_hang(:pid, :qty)");
                        $stmt->execute([':pid' => $maSanPham, ':qty' => $delta]);
                        $stmt->closeCursor();
                    } catch (PDOException $spEx) {
                        if ((string)$spEx->getCode() === '45000' || strpos($spEx->getMessage(), '1644') !== false) {
                            throw new InvalidArgumentException($spEx->getMessage());
                        }
                        // Dự phòng trực tiếp khi Hosting không có Stored Procedure sp_lbn_nhap_them_kho_hang
                        $fbStmt = $this->db->prepare("UPDATE lbn_san_pham SET so_luong_ton_kho = :stock WHERE ma_san_pham = :pid");
                        $fbStmt->execute([':stock' => $finalStock, ':pid' => $maSanPham]);
                    }
                } else {
                    $stmt = $this->db->prepare("UPDATE lbn_san_pham SET so_luong_ton_kho = :stock WHERE ma_san_pham = :pid");
                    $stmt->execute([':stock' => $finalStock, ':pid' => $maSanPham]);
                }
                $dbUpdated = true;
                unset($_SESSION['lbn_stock_overrides'][$maSanPham]);
            } catch (InvalidArgumentException $e) {
                throw $e;
            } catch (PDOException $e) {
                error_log("Lỗi cập nhật tồn kho sp_lbn_nhap_them_kho_hang: " . $e->getMessage());
                throw new RuntimeException('Không thể lưu cập nhật tồn kho vào cơ sở dữ liệu: ' . $e->getMessage());
            }
        }

        if (!$dbUpdated) {
            if (!isset($_SESSION['lbn_stock_overrides']) || !is_array($_SESSION['lbn_stock_overrides'])) {
                $_SESSION['lbn_stock_overrides'] = [];
            }
            $_SESSION['lbn_stock_overrides'][$maSanPham] = $finalStock;
        }

        if (isset($_SESSION['lbn_custom_products'][$maSanPham])) {
            $_SESSION['lbn_custom_products'][$maSanPham]['so_luong_ton_kho'] = $finalStock;
            $_SESSION['lbn_custom_products'][$maSanPham]['stock_quantity']   = $finalStock;
        }

        $stats = $this->layThongKeKhoHang();

        return [
            'product_id'       => $maSanPham,
            'sku'              => $targetProd['sku'] ?? ('FK-LBN-' . $maSanPham),
            'product_name'     => $targetProd['name'] ?? ('Sản phẩm #' . $maSanPham),
            'old_stock'        => $oldStock,
            'new_stock'        => $finalStock,
            'delta'            => $delta,
            'is_low_stock'     => ($finalStock <= 5),
            'routine'          => 'sp_lbn_nhap_them_kho_hang',
            'stats'            => $stats
        ];
    }

    /**
     * Thêm mới hoặc Cập nhật sản phẩm vào kho hàng (MH 13 - triệu gọi sp_lbn_them_san_pham_moi)
     */
    public function luuSanPhamKhoHang(array $data): array {
        $productId  = !empty($data['product_id']) ? (int)$data['product_id'] : 0;
        $isEdit     = ($productId > 0);
        $catId      = (int)($data['category_id'] ?? 1);
        $name       = trim((string)($data['name'] ?? ''));
        $sku        = trim((string)($data['sku'] ?? ''));
        $gender     = trim((string)($data['gender'] ?? 'girl'));
        $price      = (float)($data['price'] ?? 0);
        $stock      = (int)($data['stock_quantity'] ?? 0);
        $sizeType   = in_array($data['size_type'] ?? '', ['quan_ao', 'giay_dep'], true) ? $data['size_type'] : 'quan_ao';
        $material   = trim((string)($data['description'] ?? ''));
        $imagePath  = trim((string)($data['image_path'] ?? ''));

        $catMap = $this->getCategoryMetadataMap();
        if (!isset($catMap[$catId])) {
            throw new InvalidArgumentException('Danh mục sản phẩm không tồn tại trên hệ thống!');
        }
        if ($name === '') {
            throw new InvalidArgumentException('Tên sản phẩm không được phép để trống!');
        }

        if ($sizeType === 'giay_dep' || $catId === 4) {
            $entity = new SanPhamGiayDep($productId, $catId, $name, $price, $stock, $imagePath);
        } else {
            $entity = new SanPhamQuanAo($productId, $catId, $name, $price, $stock, $imagePath);
        }

        if ($imagePath === '' && $isEdit && $this->hasConnection()) {
            try {
                $imgStmt = $this->db->prepare("SELECT duong_dan_anh FROM lbn_san_pham WHERE ma_san_pham = :id LIMIT 1");
                $imgStmt->execute([':id' => $productId]);
                $imgRow = $imgStmt->fetch();
                if (!empty($imgRow['duong_dan_anh'])) {
                    $imagePath = (string)$imgRow['duong_dan_anh'];
                }
            } catch (PDOException $e) {
                // Giữ nguyên fallback bên dưới nếu không đọc được ảnh cũ
            }
        }

        if ($imagePath === '') {
            $defaultImages = [
                1 => '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg',
                2 => '/FashionKids/images/bo-thun-khung-long.jpg',
                3 => '/FashionKids/images/ao-khoac-cardigan.jpg',
                4 => '/FashionKids/images/giay-sandal-tap-di.jpg'
            ];
            $imagePath = $defaultImages[$catId] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg';
        }

        $usedRoutine = false;
        if (!$isEdit) {
            $newId = 0;
            if ($this->hasConnection()) {
                try {
                    $stmt = $this->db->prepare(
                        "CALL sp_lbn_them_san_pham_moi(:cat, :name, :price, :stock, :img, :desc, :type, @ma_sp_moi)"
                    );
                    $stmt->execute([
                        ':cat'   => $entity->getMaDanhMuc(),
                        ':name'  => $entity->getTenSanPham(),
                        ':price' => $entity->getGiaBan(),
                        ':stock' => $entity->getSoLuongTonKho(),
                        ':img'   => $imagePath,
                        ':desc'  => $material,
                        ':type'  => $entity->getLoaiKichCo()
                    ]);
                    $stmt->closeCursor();

                    $outRow = $this->db->query("SELECT @ma_sp_moi AS ma_san_pham_moi")->fetch();
                    $newId  = (int)($outRow['ma_san_pham_moi'] ?? 0);
                    if ($newId > 0) {
                        $usedRoutine = true;
                    }
                } catch (PDOException $e) {
                    if ((string)$e->getCode() === '45000' || strpos($e->getMessage(), '1644') !== false) {
                        throw new InvalidArgumentException($e->getMessage());
                    }
                    error_log("Lỗi gọi sp_lbn_them_san_pham_moi (chuyển sang INSERT trực tiếp): " . $e->getMessage());
                }

                // Dự phòng khi Hosting (InfinityFree) không có Stored Procedure sp_lbn_them_san_pham_moi
                if ($newId <= 0) {
                    try {
                        $insStmt = $this->db->prepare(
                            "INSERT INTO lbn_san_pham
                                (ma_danh_muc, ten_san_pham, gia_ban, so_luong_ton_kho, duong_dan_anh, mo_ta_chat_lieu, loai_kich_co, ngay_tao)
                             VALUES
                                (:cat, :name, :price, :stock, :img, :desc, :type, NOW())"
                        );
                        $insStmt->execute([
                            ':cat'   => $entity->getMaDanhMuc(),
                            ':name'  => $entity->getTenSanPham(),
                            ':price' => $entity->getGiaBan(),
                            ':stock' => $entity->getSoLuongTonKho(),
                            ':img'   => $imagePath,
                            ':desc'  => $material,
                            ':type'  => $entity->getLoaiKichCo()
                        ]);
                        $newId = (int)$this->db->lastInsertId();
                    } catch (PDOException $e2) {
                        error_log("Lỗi INSERT trực tiếp lbn_san_pham: " . $e2->getMessage());
                        throw new RuntimeException('Không thể lưu sản phẩm mới vào cơ sở dữ liệu: ' . $e2->getMessage());
                    }
                }

                if ($newId <= 0) {
                    throw new RuntimeException('Không thể lưu sản phẩm mới vào cơ sở dữ liệu (không lấy được mã sản phẩm).');
                }
            } else {
                $all = $this->layTatCaSanPhamChuanHoa();
                $maxId = 109;
                foreach ($all as $item) {
                    if ((int)$item['id'] > $maxId) {
                        $maxId = (int)$item['id'];
                    }
                }
                $newId = $maxId + 1;
            }
            $productId = $newId;
        } else {
            if ($this->hasConnection()) {
                try {
                    $stmt = $this->db->prepare(
                        "UPDATE lbn_san_pham 
                         SET ma_danh_muc = :cat, ten_san_pham = :name, gia_ban = :price, 
                             so_luong_ton_kho = :stock, duong_dan_anh = :img, 
                             mo_ta_chat_lieu = :desc, loai_kich_co = :type 
                         WHERE ma_san_pham = :id"
                    );
                    $stmt->execute([
                        ':cat'   => $entity->getMaDanhMuc(),
                        ':name'  => $entity->getTenSanPham(),
                        ':price' => $entity->getGiaBan(),
                        ':stock' => $entity->getSoLuongTonKho(),
                        ':img'   => $imagePath,
                        ':desc'  => $material,
                        ':type'  => $entity->getLoaiKichCo(),
                        ':id'    => $productId
                    ]);
                } catch (PDOException $e) {
                    error_log("Lỗi cập nhật sản phẩm: " . $e->getMessage());
                    throw new RuntimeException('Không thể cập nhật sản phẩm vào cơ sở dữ liệu: ' . $e->getMessage());
                }
            }
        }

        if ($sku === '') {
            $skuNum = $productId >= 100 ? ($productId - 100) : $productId;
            $sku = 'FK-LBN-' . str_pad((string)$skuNum, 2, '0', STR_PAD_LEFT);
        }

        $savedRecord = $this->normalizeProductRow([
            'ma_san_pham'      => $productId,
            'ma_danh_muc'      => $catId,
            'ten_san_pham'     => $name,
            'gia_ban'          => $price,
            'so_luong_ton_kho' => $stock,
            'duong_dan_anh'    => $imagePath,
            'mo_ta_chat_lieu'  => $material,
            'loai_kich_co'     => $sizeType,
            'sku'              => $sku,
            'gender'           => $gender
        ]);

        if ($this->hasConnection()) {
            unset($_SESSION['lbn_custom_products'][$productId], $_SESSION['lbn_stock_overrides'][$productId]);
        } else {
            if (!isset($_SESSION['lbn_custom_products']) || !is_array($_SESSION['lbn_custom_products'])) {
                $_SESSION['lbn_custom_products'] = [];
            }
            $_SESSION['lbn_custom_products'][$productId] = $savedRecord;

            if (!isset($_SESSION['lbn_stock_overrides']) || !is_array($_SESSION['lbn_stock_overrides'])) {
                $_SESSION['lbn_stock_overrides'] = [];
            }
            $_SESSION['lbn_stock_overrides'][$productId] = $stock;
        }

        return [
            'is_edit' => $isEdit,
            'product' => $savedRecord,
            'routine' => $isEdit ? 'lbn_update_product' : 'sp_lbn_them_san_pham_moi',
            'stats'   => $this->layThongKeKhoHang()
        ];
    }

    /**
     * Tổng hợp 4 chỉ số KPI kho hàng trên MH 13 (Tổng SKU, Tổng tồn kho, Số mã <= 5, Đếm theo danh mục)
     */
    public function layThongKeKhoHang(): array {
        $allProducts   = $this->layTatCaSanPhamChuanHoa();
        $totalSku      = count($allProducts);
        $totalUnits    = 0;
        $lowStockCount = 0;

        foreach ($allProducts as $p) {
            $st = (int)$p['stock_quantity'];
            $totalUnits += $st;
            if ($st <= 5) {
                $lowStockCount++;
            }
        }

        $catCounts = $this->demSanPhamTheoDanhMuc();

        return [
            'total_sku'       => $totalSku,
            'total_units'     => $totalUnits,
            'low_stock_count' => $lowStockCount,
            'category_counts' => $catCounts,
            'summary_text'    => sprintf(
                'Váy: %d • Bộ thun: %d • Khoác: %d • Sandal: %d',
                $catCounts['vay_cong_chua'] ?? 0,
                $catCounts['bo_thun'] ?? 0,
                $catCounts['ao_khoac'] ?? 0,
                $catCounts['giay_sandal'] ?? 0
            )
        ];
    }
}
