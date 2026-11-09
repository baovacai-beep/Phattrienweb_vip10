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
}
