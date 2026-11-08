<?php
/**
 * Bộ định tuyến Front Controller (core/App.php)
 * Phân tích và điều hướng yêu cầu theo cả 3 quy ước:
 *  1. ?c=auth&a=ajaxCheckDuplicate (viết tắt)
 *  2. ?controller=auth&action=ajaxCheckDuplicate (tường minh)
 *  3. ?url=auth/ajaxCheckDuplicate hoặc /auth/ajaxCheckDuplicate (friendly URL)
 */

class App {
    protected $controller = 'HomeController';
    protected $action     = 'index';
    protected $params     = [];

    public function __construct() {
        // Đảm bảo session sẵn sàng
        if (session_status() === PHP_SESSION_NONE) {
            $savePath = session_save_path();
            if (empty($savePath) || !is_writable($savePath)) {
                $tempDir = sys_get_temp_dir();
                if (is_writable($tempDir)) {
                    @session_save_path($tempDir);
                }
            }
            session_start();
        }

        $url = $this->parseUrl();

        // 1. Kiểm tra và xác định Controller
        if (!empty($url[0])) {
            $candidate = ucfirst(strtolower($url[0])) . 'Controller';
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
     * Tách và làm sạch chuỗi URL, hỗ trợ linh hoạt ?c=&a=, ?controller=&action= và ?url=
     *
     * @return array
     */
    protected function parseUrl(): array {
        // Quy tắc 1: Tham số ?c= và ?a= hoặc ?controller= và ?action=
        $c = $_GET['c'] ?? $_GET['controller'] ?? null;
        $a = $_GET['a'] ?? $_GET['action'] ?? null;
        if ($c !== null) {
            $parts = [trim($c)];
            if ($a !== null && trim($a) !== '') {
                $parts[] = trim($a);
            }
            return $parts;
        }

        // Quy tắc 2: Tham số ?url=controller/action
        if (isset($_GET['url'])) {
            $rawUrl = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $rawUrl);
        }

        // Quy tắc 3: PATH_INFO nếu URL Rewrite được bật
        if (isset($_SERVER['PATH_INFO'])) {
            $rawPath = trim($_SERVER['PATH_INFO'], '/');
            if (!empty($rawPath)) {
                return explode('/', filter_var($rawPath, FILTER_SANITIZE_URL));
            }
        }

        return [];
    }
}
