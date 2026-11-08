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
     * API AJAX: Gợi ý kích thước thước dây (MH 04)
     * Triệu gọi Stored Function từ SizeChartModel và trả về JSON chuẩn
     */
    public function ajaxSuggestSize(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ'], 405);
        }

        $maDanhMuc = (int)($_POST['ma_danh_muc'] ?? $_POST['category_id'] ?? 0);
        $chieuCao  = (float)($_POST['chieu_cao'] ?? $_POST['measurement'] ?? 0);
        $daiChan   = (float)($_POST['dai_chan'] ?? ($maDanhMuc === 4 ? $chieuCao : 0));

        try {
            $size = $this->sizeChartModel->goiYSizeChuan($maDanhMuc, $chieuCao, null, null, $daiChan);

            $this->jsonResponse([
                'status'           => 'success',
                'recommended_size' => $size,
                'category_id'      => $maDanhMuc,
                'measurement'      => $chieuCao,
                'note'             => 'Kích cỡ tính toán tự động dựa trên quy chuẩn Stored Function & chất liệu sợi tre co giãn 4 chiều.'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
