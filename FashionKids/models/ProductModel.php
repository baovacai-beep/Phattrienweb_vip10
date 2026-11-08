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
            'ma_san_pham'     => 1,
            'ma_danh_muc'     => 1,
            'ten_san_pham'    => 'Váy Công Chúa Elsa Bạch Tuyết Cao Cấp',
            'gia_ban'         => 450000.00,
            'so_luong_ton_kho'=> 25,
            'duong_dan_anh'   => 'vay_cong_chua_elsa.jpg',
            'mo_ta_chat_lieu' => 'Vải cotton sợi tre 100% hữu cơ, kháng khuẩn, không gây ngứa da',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Váy đầm công chúa bé gái',
            'gioi_tinh'       => 'be_gai'
        ],
        [
            'ma_san_pham'     => 2,
            'ma_danh_muc'     => 2,
            'ten_san_pham'    => 'Bộ Thun Thể Thao Khủng Long Năng Động',
            'gia_ban'         => 280000.00,
            'so_luong_ton_kho'=> 30,
            'duong_dan_anh'   => 'bo_thun_khung_long.jpg',
            'mo_ta_chat_lieu' => 'Chất cotton 4 chiều co giãn thoáng mồ hôi cho bé trai vận động',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Bộ quần áo thun thể thao bé trai',
            'gioi_tinh'       => 'be_trai'
        ],
        [
            'ma_san_pham'     => 3,
            'ma_danh_muc'     => 3,
            'ten_san_pham'    => 'Áo Khoác Gió Bomber Trẻ Em 2 Lớp',
            'gia_ban'         => 390000.00,
            'so_luong_ton_kho'=> 20,
            'duong_dan_anh'   => 'ao_khoac_bomber.jpg',
            'mo_ta_chat_lieu' => 'Chất vải dù cản gió chống nước nhẹ, giữ ấm ngày mưa gió',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Áo khoác dạo phố năng động',
            'gioi_tinh'       => 'chung'
        ],
        [
            'ma_san_pham'     => 4,
            'ma_danh_muc'     => 4,
            'ten_san_pham'    => 'Giày Sandal Tập Đi Êm Ái Chống Trượt',
            'gia_ban'         => 320000.00,
            'so_luong_ton_kho'=> 15,
            'duong_dan_anh'   => 'sandal_tap_di.jpg',
            'mo_ta_chat_lieu' => 'Đế cao su non uốn dẻo, bảo vệ xương bàn chân non nớt của bé',
            'loai_kich_co'    => 'giay_dep',
            'ten_danh_muc'    => 'Giày sandal tập đi êm chân',
            'gioi_tinh'       => 'chung'
        ]
    ];

    /**
     * Lấy danh sách sản phẩm theo danh mục và lọc giá
     */
    public function layDanhSachSanPham(?int $maDanhMuc = null, float $minPrice = 0, float $maxPrice = 10000000): array {
        if ($this->hasConnection()) {
            try {
                $sql = "SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh 
                        FROM lbn_san_pham sp 
                        JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                        WHERE sp.gia_ban BETWEEN :minPrice AND :maxPrice";
                $params = [':minPrice' => $minPrice, ':maxPrice' => $maxPrice];

                if ($maDanhMuc !== null && $maDanhMuc > 0) {
                    $sql .= " AND sp.ma_danh_muc = :maDanhMuc";
                    $params[':maDanhMuc'] = $maDanhMuc;
                }
                $sql .= " ORDER BY sp.ma_san_pham DESC";

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Lỗi lấy danh sách sản phẩm: " . $e->getMessage());
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
                $stmt = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh 
                                            FROM lbn_san_pham sp 
                                            JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                                            WHERE sp.so_luong_ton_kho > 0 
                                            ORDER BY sp.ma_san_pham DESC 
                                            LIMIT :limit");
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (PDOException $e) {}
        }
        return array_slice($this->mockProducts, 0, $limit);
    }

    /**
     * Chi tiết sản phẩm phục vụ trang PDP
     */
    public function layChiTietSanPham(int $id): ?array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc, dm.gioi_tinh 
                                            FROM lbn_san_pham sp 
                                            JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                                            WHERE sp.ma_san_pham = :id 
                                            LIMIT 1");
                $stmt->execute([':id' => $id]);
                $product = $stmt->fetch();
                return $product ?: null;
            } catch (PDOException $e) {
                error_log("Lỗi chi tiết sản phẩm: " . $e->getMessage());
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
     * Nhập thêm kho hàng qua sp_lbn_nhap_them_kho_hang
     */
    public function updateStock(int $productId, int $quantityChange): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lbn_nhap_them_kho_hang(:pid, :qty)");
                $res = $stmt->execute([':pid' => $productId, ':qty' => $quantityChange]);
                $stmt->closeCursor();
                return $res;
            } catch (PDOException $e) {
                return false;
            }
        }
        return true;
    }
}
