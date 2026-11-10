<?php
/**
 * Lớp ProductController (controllers/ProductController.php)
 * Điều phối Danh mục (MH 02), Chi tiết sản phẩm (MH 03) và API AJAX đo size thước dây (MH 04)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/SizeChartModel.php';

class ProductController extends Controller {
    private ProductModel $productModel;
    private SizeChartModel $sizeChartModel;

    public function __construct() {
        $this->productModel    = new ProductModel();
        $this->sizeChartModel = new SizeChartModel();
    }

    /**
     * Hiển thị danh mục & bộ lọc (MH 02)
     */
    public function catalog(): void {
        $catId = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
        $min   = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0.0;
        $max   = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 2000000.0;

        $products = $this->productModel->layDanhSachSanPham($catId, $min, $max);

        $this->view('pages/shop-catalog', [
            'page_title' => 'Cửa Hàng Thời Trang Trẻ Em An Toàn',
            'products'   => $products,
            'currentCat' => $catId
        ]);
    }

    public function index(): void {
        $this->catalog();
    }

    /**
     * Hiển thị chi tiết sản phẩm PDP (MH 03)
     */
    public function detail(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 101;
        $product = $this->productModel->layChiTietSanPham($id);

        if (!$product) {
            header("Location: index.php?c=product&a=catalog");
            exit;
        }

        $this->view('pages/product-detail', [
            'page_title' => $product['ten_san_pham'] ?? 'Chi Tiết Sản Phẩm',
            'product'    => $product
        ]);
    }

    /**
     * API AJAX: Gợi ý kích thước thước dây (MH 04 - Giai đoạn 4: Lê Nguyễn Khánh Trình)
     * Triệu gọi Stored Function fn_lnkt_goi_y_kich_co_chuan từ SizeChartModel và trả về JSON chuẩn
     */
    public function ajaxSuggestSize(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ (Chỉ chấp nhận POST)'], 405);
        }

        try {
            $rawCat    = trim((string)($_POST['ma_danh_muc'] ?? $_POST['category_id'] ?? ''));
            $rawHeight = trim((string)($_POST['chieu_cao'] ?? $_POST['measurement'] ?? ''));
            $rawFoot   = trim((string)($_POST['dai_chan'] ?? ''));
            $rawChest  = isset($_POST['vong_nguc']) && $_POST['vong_nguc'] !== '' ? trim((string)$_POST['vong_nguc']) : null;
            $rawWaist  = isset($_POST['vong_eo']) && $_POST['vong_eo'] !== '' ? trim((string)$_POST['vong_eo']) : null;

            if ($rawCat === '' || !is_numeric($rawCat) || (int)$rawCat <= 0) {
                throw new Exception('Vui lòng chọn danh mục sản phẩm hợp lệ!');
            }
            $maDanhMuc = (int)$rawCat;

            $chieuCao = 88.0;
            $daiChan  = null;
            $vongNguc = null;
            $vongEo   = null;

            if ($maDanhMuc === 4 && $rawFoot !== '') {
                if (!is_numeric($rawFoot)) {
                    throw new Exception('Chiều dài bàn chân không hợp lệ! Vui lòng chỉ nhập chữ số (cm), không nhập chữ cái.');
                }
                $daiChan = (float)$rawFoot;
                if ($daiChan <= 0) {
                    throw new Exception('Số đo chiều dài bàn chân không được là số âm hoặc bằng 0!');
                }
                if ($daiChan < 10 || $daiChan > 25) {
                    throw new Exception('Chiều dài bàn chân trẻ em phải nằm trong khoảng từ 10cm đến 25cm!');
                }
                if ($rawHeight !== '') {
                    if (!is_numeric($rawHeight)) {
                        throw new Exception('Chiều cao không hợp lệ! Vui lòng chỉ nhập chữ số (cm), không nhập chữ cái.');
                    }
                    $chieuCao = (float)$rawHeight;
                    if ($chieuCao < 50 || $chieuCao > 160) {
                        throw new Exception('Chiều cao của bé phải nằm trong ngưỡng trẻ em hợp lệ (50cm <= cao <= 160cm)!');
                    }
                }
            } else {
                if ($rawHeight === '') {
                    throw new Exception('Vui lòng nhập số đo chiều cao của bé (cm)!');
                }
                if (!is_numeric($rawHeight)) {
                    throw new Exception('Số đo chiều cao không hợp lệ! Vui lòng chỉ nhập số (cm), không nhập ký tự chữ.');
                }
                $chieuCao = (float)$rawHeight;
                if ($chieuCao <= 0) {
                    throw new Exception('Số đo chiều cao không được là số âm hoặc bằng 0!');
                }
                if ($chieuCao < 50 || $chieuCao > 160) {
                    throw new Exception('Chiều cao của bé vượt ngưỡng cho phép! Vui lòng nhập trong khoảng từ 50cm đến 160cm.');
                }
                if ($rawFoot !== '') {
                    if (!is_numeric($rawFoot) || (float)$rawFoot <= 0) {
                        throw new Exception('Số đo chiều dài bàn chân không hợp lệ (không được nhập số âm hoặc chữ cái)!');
                    }
                    $daiChan = (float)$rawFoot;
                }
            }

            if ($rawChest !== null) {
                if (!is_numeric($rawChest) || (float)$rawChest <= 0) {
                    throw new Exception('Số đo vòng ngực không hợp lệ!');
                }
                $vongNguc = (float)$rawChest;
            }

            if ($rawWaist !== null) {
                if (!is_numeric($rawWaist) || (float)$rawWaist <= 0) {
                    throw new Exception('Số đo vòng eo không hợp lệ!');
                }
                $vongEo = (float)$rawWaist;
            }

            $size = $this->sizeChartModel->goiYSizeChuan($maDanhMuc, $chieuCao, $vongNguc, $vongEo, $daiChan);

            $note = ($maDanhMuc === 4)
                ? 'Kích cỡ giày/sandal đối chiếu từ Stored Function fn_lnkt_goi_y_kich_co_chuan & đế cao su non uốn dẻo 360 độ (khuyên cộng dư 0.5cm cho bé mang tất).'
                : 'Kích cỡ tính toán tự động dựa trên quy chuẩn Stored Function fn_lnkt_goi_y_kich_co_chuan & chất liệu sợi tre Organic Bamboo co giãn 4 chiều (+8% đến +12%).';

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'           => 'success',
                'recommended_size' => $size,
                'category_id'      => $maDanhMuc,
                'measurement'      => ($maDanhMuc === 4 && $daiChan !== null) ? $daiChan : $chieuCao,
                'note'             => $note,
                'message'          => 'Gợi ý kích cỡ thành công: ' . $size
            ], 200);
        } catch (Exception $e) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $this->jsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'note'    => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API AJAX: Thêm sản phẩm mới vào kho (MH 13 - chỉ Quản trị viên Admin)
     * Triệu gọi Stored Procedure sp_lbn_them_san_pham_moi.
     * Bắt lỗi ngoại lệ nếu giá bán <= 0 hoặc tồn kho < 0.
     */
    public function ajaxAddProduct(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ (Chỉ chấp nhận POST)'], 405);
        }

        try {
            $maDanhMuc     = (int)($_POST['ma_danh_muc'] ?? $_POST['category_id'] ?? 1);
            $tenSanPham    = trim((string)($_POST['ten_san_pham'] ?? $_POST['name'] ?? ''));
            $giaBan        = (float)($_POST['gia_ban'] ?? $_POST['price'] ?? 0);
            $soLuongTonKho = (int)($_POST['so_luong_ton_kho'] ?? $_POST['stock_quantity'] ?? 0);
            $duongDanAnh   = trim((string)($_POST['duong_dan_anh'] ?? $_POST['image_url'] ?? '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg'));
            $moTaChatLieu  = trim((string)($_POST['mo_ta_chat_lieu'] ?? $_POST['description'] ?? '100% sợi tre hữu cơ kháng khuẩn tự nhiên'));
            $loaiKichCo    = trim((string)($_POST['loai_kich_co'] ?? $_POST['lbn_size_type'] ?? 'quan_ao'));

            if ($giaBan <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_03]: Giá bán sản phẩm phải lớn hơn 0 VNĐ!');
            }
            if ($soLuongTonKho < 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_04]: Số lượng hàng tồn kho ban đầu không được là số âm!');
            }
            if ($tenSanPham === '') {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_02]: Tên sản phẩm không được phép để trống!');
            }

            $maSpMoi = $this->productModel->themSanPhamMoi(
                $maDanhMuc,
                $tenSanPham,
                $giaBan,
                $soLuongTonKho,
                $duongDanAnh,
                $moTaChatLieu,
                $loaiKichCo
            );

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'     => 'success',
                'product_id' => $maSpMoi,
                'message'    => "Đã thực thi sp_lbn_them_san_pham_moi: Thêm sản phẩm \"{$tenSanPham}\" (#{$maSpMoi}) thành công!"
            ], 200);
        } catch (Exception $e) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $this->jsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API AJAX: Nhập thêm kho hàng bổ sung (MH 13 - cho phépทั้ง Admin và Nhân viên Staff)
     * Triệu gọi Stored Procedure sp_lbn_nhap_them_kho_hang.
     */
    public function ajaxRestockProduct(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        Rbac::requireStaffOrAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ (Chỉ chấp nhận POST)'], 405);
        }

        try {
            $maSanPham   = (int)($_POST['ma_san_pham'] ?? $_POST['product_id'] ?? 0);
            $soLuongNhap = (int)($_POST['so_luong_nhap'] ?? $_POST['qty_change'] ?? 0);

            if ($maSanPham <= 0) {
                throw new InvalidArgumentException('Mã sản phẩm cần nhập kho không hợp lệ!');
            }
            if ($soLuongNhap <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_05]: Số lượng nhập hàng bổ sung phải lớn hơn 0!');
            }

            $ok = $this->productModel->updateStock($maSanPham, $soLuongNhap);
            if (!$ok) {
                throw new RuntimeException('Không thể cập nhật tồn kho lúc này, vui lòng kiểm tra lại!');
            }

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $this->jsonResponse([
                'status'     => 'success',
                'product_id' => $maSanPham,
                'added_qty'  => $soLuongNhap,
                'message'    => "Đã thực thi sp_lbn_nhap_them_kho_hang: Nhập thêm +{$soLuongNhap} sản phẩm cho mã #{$maSanPham} thành công!"
            ], 200);
        } catch (Exception $e) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $this->jsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
