<?php
/**
 * Lớp Model Sản Phẩm (models/ProductModel.php)
 * Đảm nhiệm: lbn_ (Lê Bảo Ngọc)
 * Tích hợp Stored Procedures: sp_lbn_them_san_pham_moi, fn_lbn_kiem_tra_ton_kho, sp_lbn_nhap_them_kho_hang
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
            'gia_ban'         => 285000,
            'so_luong_ton_kho'=> 45,
            'duong_dan_anh'   => '/FashionKids/public/uploads/products/dress-flower-01.jpg',
            'mo_ta_chat_lieu' => '100% sợi tre tự nhiên mềm mịn, thoáng khí, kháng khuẩn vượt trội.',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Váy công chúa'
        ],
        [
            'ma_san_pham'     => 102,
            'ma_danh_muc'     => 2,
            'ten_san_pham'    => 'Bộ Thun Sợi Tre Làm Mát Khủng Long Nhí Năng Động',
            'gia_ban'         => 225000,
            'so_luong_ton_kho'=> 60,
            'duong_dan_anh'   => '/FashionKids/public/uploads/products/dino-set-02.jpg',
            'mo_ta_chat_lieu' => 'Chất liệu thun sợi tre Bamboo co giãn 4 chiều, thấm hút mồ hôi tối ưu.',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Bộ thun'
        ],
        [
            'ma_san_pham'     => 103,
            'ma_danh_muc'     => 4,
            'ten_san_pham'    => 'Giày Sandal Tập Đi Đế Cao Su Non Lót Sợi Tre Êm Chân',
            'gia_ban'         => 210000,
            'so_luong_ton_kho'=> 35,
            'duong_dan_anh'   => '/FashionKids/public/uploads/products/sandal-bamboo-03.jpg',
            'mo_ta_chat_lieu' => 'Đế cao su non uốn dẻo 360 độ, lót đệm sợi tre chống trơn trượt.',
            'loai_kich_co'    => 'giay_dep',
            'ten_danh_muc'    => 'Giày dép'
        ],
        [
            'ma_san_pham'     => 104,
            'ma_danh_muc'     => 3,
            'ten_san_pham'    => 'Áo Khoác Cardigan Sợi Tre Hữu Cơ Chống Nắng UV',
            'gia_ban'         => 320000,
            'so_luong_ton_kho'=> 20,
            'duong_dan_anh'   => '/FashionKids/public/uploads/products/cardigan-uv-04.jpg',
            'mo_ta_chat_lieu' => 'Sợi tre dệt 2 lớp chống tia cực tím UPF 50+, giữ ấm nhẹ nhàng ban đêm.',
            'loai_kich_co'    => 'quan_ao',
            'ten_danh_muc'    => 'Áo khoác'
        ]
    ];

    /**
     * Lấy danh sách sản phẩm theo danh mục và khoảng giá
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
            } catch (PDOException $e) {}
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
                $stmt = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc 
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
     * Lấy chi tiết sản phẩm
     */
    public function layChiTietSanPham(int $id): ?array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT sp.*, dm.ten_danh_muc 
                                            FROM lbn_san_pham sp 
                                            JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                                            WHERE sp.ma_san_pham = :id LIMIT 1");
                $stmt->execute([':id' => $id]);
                $item = $stmt->fetch();
                return $item ?: null;
            } catch (PDOException $e) {}
        }

        foreach ($this->mockProducts as $p) {
            if ($p['ma_san_pham'] === $id) return $p;
        }
        return null;
    }

    public function getProductById(int $productId): ?array {
        return $this->layChiTietSanPham($productId);
    }

    /**
     * Kiểm tra hàng tồn kho thông qua Stored Function
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
     * Gợi ý kích cỡ thông minh (kết hợp Stored Function và Đa hình OOP)
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
     * Cập nhật tồn kho
     */
    public function updateStock(int $productId, int $quantityChange): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lbn_nhap_them_kho_hang(:pid, :qty)");
                return $stmt->execute([':pid' => $productId, ':qty' => $quantityChange]);
            } catch (PDOException $e) {
                try {
                    $stmt = $this->db->prepare("CALL lbn_update_stock(:pid, :qty)");
                    return $stmt->execute([':pid' => $productId, ':qty' => $quantityChange]);
                } catch (PDOException $e2) {
                    return false;
                }
            }
        }
        return true;
    }
}
