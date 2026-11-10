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

    // =========================================================================
    // GIAI ĐOẠN 4 - LÊ BẢO NGỌC (lbn_): WEBSERVICE JSON & AJAX ENDPOINTS
    // =========================================================================

    /**
     * Endpoint 1 (MH 02): Lọc đa tiêu chí, Tìm kiếm sản phẩm & Thống kê danh mục bằng AJAX JSON
     * URL: index.php?c=product&a=ajaxFilterCatalog
     */
    public function ajaxFilterCatalog(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['GET', 'POST'], true)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức HTTP không hợp lệ!'], 405);
        }

        $req = ($method === 'POST') ? $_POST : $_GET;

        try {
            $rawMinPrice = isset($req['min_price']) && $req['min_price'] !== '' ? trim((string)$req['min_price']) : '0';
            $rawMaxPrice = isset($req['max_price']) && $req['max_price'] !== '' ? trim((string)$req['max_price']) : '500000';

            if (!is_numeric($rawMinPrice) || !is_numeric($rawMaxPrice)) {
                throw new InvalidArgumentException('Khoảng giá lọc không hợp lệ! Vui lòng chỉ nhập giá trị số.');
            }

            $minPrice = (float)$rawMinPrice;
            $maxPrice = (float)$rawMaxPrice;
            if ($minPrice < 0 || $maxPrice <= 0 || $minPrice > $maxPrice) {
                throw new InvalidArgumentException('Khoảng giá lọc không được là số âm và giá tối đa phải lớn hơn giá tối thiểu!');
            }

            $filters = [
                'category'    => trim((string)($req['category'] ?? $req['cat'] ?? 'all')),
                'gender'      => trim((string)($req['gender'] ?? 'all')),
                'min_price'   => $minPrice,
                'max_price'   => $maxPrice,
                'keyword'     => trim((string)($req['keyword'] ?? '')),
                'only_bamboo' => $req['only_bamboo'] ?? '0',
                'sort'        => trim((string)($req['sort'] ?? 'newest'))
            ];

            $products       = $this->productModel->locSanPhamDaTieuChi($filters);
            $categoryCounts = $this->productModel->demSanPhamTheoDanhMuc();

            $this->jsonResponse([
                'status'          => 'success',
                'code'            => 200,
                'products'        => $products,
                'visible_count'   => count($products),
                'total_count'     => $categoryCounts['all'] ?? count($products),
                'category_counts' => $categoryCounts,
                'filters'         => $filters,
                'routine'         => 'lbn_count_products_by_category',
                'message'         => 'Đã lọc thành công ' . count($products) . ' sản phẩm phù hợp.'
            ], 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Alias tương thích cho bộ lọc sản phẩm AJAX
     */
    public function ajaxFilterProducts(): void {
        $this->ajaxFilterCatalog();
    }

    /**
     * Endpoint 2 (MH 03 & MH 02): Kiểm tra tồn kho khả dụng (fn_lbn_kiem_tra_ton_kho) & Thêm vào giỏ hàng AJAX
     * URL: index.php?c=product&a=ajaxCheckStock
     */
    public function ajaxCheckStock(): void {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Phương thức không hợp lệ (Chỉ chấp nhận POST)!'], 405);
        }

        try {
            $rawId   = trim((string)($_POST['product_id'] ?? $_POST['ma_san_pham'] ?? '101'));
            $rawQty  = trim((string)($_POST['quantity'] ?? $_POST['so_luong'] ?? '1'));
            $size    = trim((string)($_POST['size'] ?? $_POST['kich_co_chon'] ?? 'Size 2 (2-3T)'));
            $actType = trim((string)($_POST['action_type'] ?? 'check'));

            if ($rawId === '' || !is_numeric($rawId) || (int)$rawId <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_08]: Mã sản phẩm tra cứu tồn kho không hợp lệ!');
            }
            if ($rawQty === '' || !is_numeric($rawQty) || (int)$rawQty <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_07]: Số lượng kiểm tra đặt mua phải là số nguyên lớn hơn 0!');
            }

            $productId = (int)$rawId;
            $quantity  = (int)$rawQty;

            $stockInfo = $this->productModel->kiemTraTonKhoChiTiet($productId, $quantity);

            if (!$stockInfo['du_hang']) {
                $this->jsonResponse([
                    'status'          => 'error',
                    'code'            => 409,
                    'du_hang'         => false,
                    'available_stock' => $stockInfo['available_stock'],
                    'requested_qty'   => $quantity,
                    'is_low_stock'    => $stockInfo['is_low_stock'],
                    'routine'         => $stockInfo['routine'],
                    'message'         => 'Số lượng đặt mua (' . $quantity . ') vượt quá tồn kho hiện tại (' . $stockInfo['available_stock'] . ' sản phẩm)!'
                ], 409);
            }

            // Nếu người dùng bấm Thêm vào giỏ hoặc Mua ngay, lưu vào $_SESSION['cart']
            if (in_array($actType, ['add_to_cart', 'buy_now', 'quick_add'], true)) {
                if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                $cartKey = $productId . '_' . $size;
                $currentInCart = (int)($_SESSION['cart'][$cartKey]['quantity'] ?? 0);
                $_SESSION['cart'][$cartKey] = [
                    'product_id'   => $productId,
                    'product_name' => $stockInfo['product_name'],
                    'sku'          => $stockInfo['sku'],
                    'price'        => $stockInfo['price'],
                    'size'         => $size,
                    'quantity'     => min($stockInfo['available_stock'], $currentInCart + $quantity)
                ];
            }

            $totalCartItems = 0;
            if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $cItem) {
                    $totalCartItems += (int)($cItem['quantity'] ?? 1);
                }
            }

            $msg = ($actType === 'check')
                ? 'Kiểm tra fn_lbn_kiem_tra_ton_kho hợp lệ: Còn ' . $stockInfo['available_stock'] . ' sản phẩm sẵn sàng.'
                : 'Đã kiểm tra tồn kho (fn_lbn_kiem_tra_ton_kho) & thêm ' . $quantity . ' x "' . $stockInfo['product_name'] . '" (' . $size . ') vào giỏ hàng!';

            $this->jsonResponse(array_merge($stockInfo, [
                'status'     => 'success',
                'code'       => 200,
                'size'       => $size,
                'cart_count' => $totalCartItems,
                'message'    => $msg
            ]), 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'du_hang' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Endpoint 3 (MH 13): Cập nhật nhanh số lượng tồn kho & Cảnh báo tự động <= 5 (sp_lbn_nhap_them_kho_hang)
     * URL: index.php?c=product&a=ajaxUpdateInventoryStock
     */
    public function ajaxUpdateInventoryStock(): void {
        // RBAC: Admin + Staff được vào; Staff bị ép chỉ "nhập thêm" (mode=add), không được ghi đè/giảm kho
        $this->guardAjax('stock.restock');
        if (Rbac::isStaff()) {
            $_POST['mode'] = 'add';
            $qtyCheck = (int)($_POST['new_stock'] ?? $_POST['stock_quantity'] ?? $_POST['qty_change'] ?? 0);
            if ($qtyCheck <= 0) {
                $this->jsonResponse([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => 'Lỗi [LBN_ERR_05]: Nhân viên chỉ được nhập thêm kho với số lượng lớn hơn 0!'
                ], 422);
            }
        }

        try {
            $rawId    = trim((string)($_POST['product_id'] ?? $_POST['ma_san_pham'] ?? ''));
            $rawStock = trim((string)($_POST['new_stock'] ?? $_POST['stock_quantity'] ?? $_POST['qty_change'] ?? ''));
            $mode     = trim((string)($_POST['mode'] ?? 'set'));

            if ($rawId === '' || !is_numeric($rawId) || (int)$rawId <= 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_06]: Mã sản phẩm cần cập nhật kho không tồn tại!');
            }
            if ($rawStock === '' || !is_numeric($rawStock)) {
                throw new InvalidArgumentException('Số lượng tồn kho không hợp lệ! Vui lòng chỉ nhập chữ số nguyên.');
            }

            $productId = (int)$rawId;
            $newStock  = (int)$rawStock;
            if ($newStock < 0) {
                throw new InvalidArgumentException('Lỗi [LBN_ERR_04]: Số lượng tồn kho không được là số âm (phải >= 0)!');
            }

            $result = $this->productModel->capNhatTonKhoSanPham($productId, $newStock, $mode);

            $warningNote = $result['is_low_stock']
                ? ' [CẢNH BÁO: Tồn kho <= 5, cần nhập bổ sung ngay!]'
                : ' [Trạng thái: Đủ hàng an toàn]';

            $this->jsonResponse(array_merge($result, [
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Đã thực thi ' . $result['routine'] . ' cho mã ' . $result['sku'] . ': Tồn kho = ' . $result['new_stock'] . ' SP.' . $warningNote
            ]), 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Endpoint 4 (MH 13): Thêm sản phẩm mới (sp_lbn_them_san_pham_moi) hoặc Cập nhật sản phẩm qua AJAX
     * URL: index.php?c=product&a=ajaxSaveProduct
     */
    public function ajaxSaveProduct(): void {
        // RBAC: thêm/sửa sản phẩm là quyền Admin (RBAC + POST + CSRF + ob_clean)
        $this->guardAjax('product.manage');

        $productId   = trim((string)($_POST['product_id'] ?? ''));
        $sku         = trim((string)($_POST['sku'] ?? ''));
        $name        = trim((string)($_POST['name'] ?? $_POST['ten_san_pham'] ?? ''));
        $categoryId  = trim((string)($_POST['category_id'] ?? $_POST['ma_danh_muc'] ?? '1'));
        $gender      = trim((string)($_POST['gender'] ?? 'girl'));
        $rawPrice    = trim((string)($_POST['price'] ?? $_POST['gia_ban'] ?? ''));
        $rawStock    = trim((string)($_POST['stock_quantity'] ?? $_POST['so_luong_ton_kho'] ?? ''));
        $sizeType    = trim((string)($_POST['lbn_size_type'] ?? $_POST['size_type'] ?? 'quan_ao'));
        $description = trim((string)($_POST['description'] ?? $_POST['mo_ta_chat_lieu'] ?? ''));

        $errors = [];
        if ($sku === '' || strlen($sku) < 3) {
            $errors['sku'] = 'Mã SKU không được để trống (tối thiểu 3 ký tự, VD: FK-LBN-10).';
        }
        if ($name === '' || mb_strlen($name, 'UTF-8') < 4) {
            $errors['name'] = 'Lỗi [LBN_ERR_02]: Tên sản phẩm không được để trống (tối thiểu 4 ký tự).';
        }
        if (!is_numeric($categoryId) || !in_array((int)$categoryId, [1, 2, 3, 4], true)) {
            $errors['category_id'] = 'Lỗi [LBN_ERR_01]: Danh mục sản phẩm không tồn tại trên hệ thống.';
        }
        if ($rawPrice === '' || !is_numeric($rawPrice) || (float)$rawPrice <= 0) {
            $errors['price'] = 'Lỗi [LBN_ERR_03]: Đơn giá bán sản phẩm phải là số lớn hơn 0 VNĐ.';
        }
        if ($rawStock === '' || !is_numeric($rawStock) || (int)$rawStock < 0) {
            $errors['stock_quantity'] = 'Lỗi [LBN_ERR_04]: Số lượng tồn kho không được là số âm.';
        }
        if ($description === '') {
            $errors['description'] = 'Vui lòng nhập mô tả chất liệu an toàn cho bé.';
        }

        if (!empty($errors)) {
            $firstErr = reset($errors);
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 422,
                'errors'  => $errors,
                'message' => $firstErr
            ], 422);
        }

        // Xử lý file ảnh tải lên nếu có
        $uploadedImagePath = '';
        if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts, true)) {
                $this->jsonResponse([
                    'status'  => 'error',
                    'code'    => 422,
                    'errors'  => ['image' => 'Định dạng ảnh không hợp lệ (Chỉ hỗ trợ JPG, PNG, WEBP).'],
                    'message' => 'Định dạng ảnh tải lên không hợp lệ!'
                ], 422);
            }
            $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
            $fileName = ($safeBase ?: 'sp_lbn') . '.' . $ext;
            $targetDir = __DIR__ . '/../images/products/';
            if (is_dir($targetDir) && is_writable($targetDir)) {
                $destPath = $targetDir . $fileName;
                if (@move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                    $uploadedImagePath = '/FashionKids/images/products/' . $fileName;
                }
            }
            if ($uploadedImagePath === '') {
                $uploadedImagePath = '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg';
            }
        }

        try {
            $saved = $this->productModel->luuSanPhamKhoHang([
                'product_id'     => $productId,
                'sku'            => $sku,
                'name'           => $name,
                'category_id'    => (int)$categoryId,
                'gender'         => $gender,
                'price'          => (float)$rawPrice,
                'stock_quantity' => (int)$rawStock,
                'size_type'      => $sizeType,
                'description'    => $description,
                'image_path'     => $uploadedImagePath
            ]);

            $httpCode = $saved['is_edit'] ? 200 : 201;
            $actionText = $saved['is_edit']
                ? 'Đã cập nhật sản phẩm "' . $saved['product']['name'] . '" (Mã: ' . $saved['product']['sku'] . ') thành công!'
                : 'Đã thực thi Stored Procedure sp_lbn_them_san_pham_moi: Thêm mới "' . $saved['product']['name'] . '" (Mã: ' . $saved['product']['sku'] . ') vào kho thành công!';

            $this->jsonResponse(array_merge($saved, [
                'status'  => 'success',
                'code'    => $httpCode,
                'message' => $actionText
            ]), $httpCode);
        } catch (Exception $e) {
            $this->jsonResponse([
                'status'  => 'error',
                'code'    => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
