<?php
/**
 * Bộ định tuyến Front Controller (core/App.php)
 * Phân tích URL định dạng: /controller/action/param1/param2 hoặc index.php?url=controller/action
 */

class App {
    protected $controller = 'HomeController';
    protected $action     = 'index';
    protected $params     = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Kiểm tra và xác định Controller
        if (!empty($url[0])) {
            $candidate = ucfirst($url[0]) . 'Controller';
            $controllerPath = __DIR__ . '/../controllers/' . $candidate . '.php';

            if (file_exists($controllerPath)) {
                $this->controller = $candidate;
                unset($url[0]);
            }
        }

        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller();

        // 2. Kiểm tra và xác định Action / Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }

        // 3. Lấy danh sách các tham số còn lại
        $this->params = $url ? array_values($url) : [];

        // 4. Gọi Controller và Action tương ứng
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    /**
     * Tách và làm sạch chuỗi URL
     *
     * @return array
     */
    protected function parseUrl(): array {
        if (isset($_GET['url'])) {
            $rawUrl = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $rawUrl);
        }

        // Phân tích PATH_INFO nếu URL Rewrite được cấu hình
        if (isset($_SERVER['PATH_INFO'])) {
            $rawPath = trim($_SERVER['PATH_INFO'], '/');
            if (!empty($rawPath)) {
                return explode('/', filter_var($rawPath, FILTER_SANITIZE_URL));
            }
        }

        return [];
    }
}
