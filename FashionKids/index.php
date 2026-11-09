<?php
// 1. Khởi tạo Output Buffering & Session toàn cục tại file điều phối chính (Front Controller)
if (ob_get_level() === 0) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    $savePath = session_save_path();
    $canWrite = false;
    if (!empty($savePath) && is_dir($savePath)) {
        $probe = @tempnam($savePath, 'fk_');
        if ($probe !== false) {
            @unlink($probe);
            $canWrite = true;
        }
    }
    if (!$canWrite) {
        $tempDir = sys_get_temp_dir();
        if (is_dir($tempDir)) {
            @session_save_path($tempDir);
        }
    }
    @session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * 2. Khớp nối quy tắc điều hướng Router
 * Hỗ trợ linh hoạt và đồng nhất cả 3 dạng tham số:
 *  - Dạng viết tắt: ?c=auth&a=ajaxSendOtp
 *  - Dạng tường minh: ?controller=auth&action=ajaxSendOtp
 *  - Dạng RESTful/Friendly URL: ?url=auth/ajaxSendOtp
 */
$cParam = $_GET['c'] ?? $_GET['controller'] ?? null;
$aParam = $_GET['a'] ?? $_GET['action'] ?? null;

$controllerKey = 'product';
$actionKey     = 'catalog';
$params        = [];

if ($cParam !== null) {
    $controllerKey = strtolower(trim($cParam));
    if ($aParam !== null && trim($aParam) !== '') {
        $actionKey = trim($aParam);
    } else {
        $actionKey = ($controllerKey === 'product') ? 'catalog' : 'index';
    }
} elseif (isset($_GET['url'])) {
    $urlParts = explode('/', trim($_GET['url'], '/'));
    if (!empty($urlParts[0])) $controllerKey = strtolower(trim($urlParts[0]));
    if (!empty($urlParts[1])) $actionKey     = trim($urlParts[1]);
    if (count($urlParts) > 2) $params        = array_slice($urlParts, 2);
}

// 3. Định danh tên lớp Controller theo chuẩn PSR
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

// 4. Fallback mặc định an toàn: hiển thị Cửa hàng sản phẩm
require_once __DIR__ . '/controllers/ProductController.php';
$defaultCtrl = new ProductController();
$defaultCtrl->catalog();
