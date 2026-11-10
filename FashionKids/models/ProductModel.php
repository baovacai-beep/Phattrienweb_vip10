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
        if ($quantityChange <= 0) {
            return false;
        }
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lbn_nhap_them_kho_hang(:pid, :qty)");
                $res = $stmt->execute([':pid' => $productId, ':qty' => $quantityChange]);
                $stmt->closeCursor();
                return $res;
            } catch (PDOException $e) {
                error_log("[lbn] updateStock error: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Thêm sản phẩm mới qua Stored Procedure sp_lbn_them_san_pham_moi
     * Bắt lỗi ngoại lệ nếu giá bán <= 0 hoặc tồn kho < 0, đồng thời gọi $stmt->closeCursor() sau khi gọi SP có biến OUT.
     *
     * @throws InvalidArgumentException Khi dữ liệu đầu vào vi phạm ràng buộc (giá bán <= 0, tồn kho < 0, tên trống)
     * @throws DomainException Khi Stored Procedure SIGNAL SQLSTATE '45000'
     */
    public function themSanPhamMoi(
        int $maDanhMuc,
        string $tenSanPham,
        float $giaBan,
        int $soLuongTonKho,
        string $duongDanAnh,
        string $moTaChatLieu,
        string $loaiKichCo = 'quan_ao'
    ): int {
        $tenSanPham = trim($tenSanPham);
        if ($tenSanPham === '') {
            throw new InvalidArgumentException('Lỗi [LBN_ERR_02]: Tên sản phẩm không được phép để trống!');
        }
        if ($giaBan <= 0) {
            throw new InvalidArgumentException('Lỗi [LBN_ERR_03]: Giá bán sản phẩm phải lớn hơn 0 VNĐ!');
        }
        if ($soLuongTonKho < 0) {
            throw new InvalidArgumentException('Lỗi [LBN_ERR_04]: Số lượng hàng tồn kho ban đầu không được là số âm!');
        }
        if (!in_array($loaiKichCo, ['quan_ao', 'giay_dep'], true)) {
            $loaiKichCo = ($maDanhMuc === 4) ? 'giay_dep' : 'quan_ao';
        }
        if (trim($duongDanAnh) === '') {
            $duongDanAnh = '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg';
        }

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare(
                    "CALL sp_lbn_them_san_pham_moi(:ma_dm, :ten_sp, :gia_ban, :ton_kho, :anh, :chat_lieu, :loai_kc, @ma_sp_moi)"
                );
                $stmt->execute([
                    ':ma_dm'     => $maDanhMuc,
                    ':ten_sp'    => $tenSanPham,
                    ':gia_ban'   => $giaBan,
                    ':ton_kho'   => $soLuongTonKho,
                    ':anh'       => $duongDanAnh,
                    ':chat_lieu' => $moTaChatLieu,
                    ':loai_kc'   => $loaiKichCo,
                ]);
                // Đóng con trỏ truy vấn trước khi đọc biến OUT để tránh lỗi PDO 2014
                $stmt->closeCursor();

                $out = $this->db->query("SELECT @ma_sp_moi AS ma_san_pham_moi")->fetch();
                return (int)($out['ma_san_pham_moi'] ?? 0);
            } catch (PDOException $e) {
                if (($e->errorInfo[0] ?? '') === '45000') {
                    $cleanMsg = preg_replace('/^SQLSTATE\[45000\]:.*?:\s*\d+\s*/u', '', (string)($e->errorInfo[2] ?? $e->getMessage()));
                    throw new DomainException(trim($cleanMsg ?: 'Dữ liệu sản phẩm không hợp lệ!'), 0, $e);
                }
                error_log("[lbn] themSanPhamMoi error: " . $e->getMessage());
            }
        }

        return random_int(110, 999);
    }

    /**
     * Tra cứu nhanh tồn kho sản phẩm theo từ khóa / mã SP (Phục vụ MH 11 Live Chat Staff)
     */
    public function traCuuTonKhoNhanh(string $keyword = ''): array {
        $kw = mb_strtolower(trim($keyword));
        $all = $this->layDanhSachSanPham();
        if ($kw === '') {
            return array_slice($all, 0, 6);
        }
        $matched = [];
        foreach ($all as $item) {
            $haystack = mb_strtolower(($item['ma_san_pham'] ?? '') . ' ' . ($item['ten_san_pham'] ?? '') . ' ' . ($item['ten_danh_muc'] ?? ''));
            if (mb_strpos($haystack, $kw) !== false) {
                $matched[] = $item;
            }
        }
        return $matched;
    }
}
