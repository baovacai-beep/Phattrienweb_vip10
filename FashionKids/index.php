<?php
/**
 * index.php - Front Controller & Router duy nhất của FashionKids
 * Điều phối toàn bộ các yêu cầu HTTP theo mô hình MVC hướng đối tượng
 * Hỗ trợ linh hoạt cả 2 kiểu tham số: ?c=product&a=catalog hoặc ?url=product/catalog
 */

if (session_status() === PHP_SESSION_NONE) {
    $savePath = session_save_path();
    if (empty($savePath) || !is_writable($savePath)) {
        $tempDir = sys_get_temp_dir();
        if (is_writable($tempDir)) {
            @session_save_path($tempDir);
        }
    }
    @session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

// 1. Phân tích tham số định tuyến (ưu tiên ?c=...&a=..., sau đó ?url=...)
$controllerKey = 'product';
$actionKey     = 'catalog';
$params        = [];

if (isset($_GET['c'])) {
    $controllerKey = trim($_GET['c']);
    $actionKey     = isset($_GET['a']) ? trim($_GET['a']) : 'catalog';
} elseif (isset($_GET['url'])) {
    $urlParts = explode('/', trim($_GET['url'], '/'));
    if (!empty($urlParts[0])) $controllerKey = $urlParts[0];
    if (!empty($urlParts[1])) $actionKey     = $urlParts[1];
    if (count($urlParts) > 2) $params        = array_slice($urlParts, 2);
}

// 2. Định danh tên lớp Controller
$controllerName = ucfirst($controllerKey) . 'Controller';
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionKey)) {
            call_user_func_array([$controller, $actionKey], $params);
            exit;
        } elseif (method_exists($controller, 'index')) {
            $controller->index();
            exit;
        }
    }
}

// 3. Fallback mặc định an toàn: hiển thị Cửa hàng sản phẩm
require_once __DIR__ . '/controllers/ProductController.php';
$defaultCtrl = new ProductController();
$defaultCtrl->catalog();
