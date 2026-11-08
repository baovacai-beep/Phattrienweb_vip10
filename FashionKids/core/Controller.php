<?php
/**
 * Lớp Base Controller (core/Controller.php)
 * Đóng vai trò lớp cơ sở cung cấp tiện ích hiển thị View, nạp Model và trả về JSON chuẩn cho AJAX.
 */

abstract class Controller {
    /**
     * Nạp và khởi tạo một đối tượng Model
     *
     * @param string $model Tên lớp Model (ví dụ: 'ProductModel', 'UserModel')
     * @return object
     * @throws Exception
     */
    public function model(string $model): object {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            if (class_exists($model)) {
                return new $model();
            }
        }
        throw new Exception("Không tìm thấy lớp Model: {$model} tại {$modelFile}");
    }

    /**
     * Render view kèm trích xuất biến truyền vào
     *
     * @param string $viewPath Đường dẫn tương đối trong views/ (ví dụ 'pages/shop-catalog' hoặc 'home')
     * @param array $data Mảng biến số truyền sang View
     * @return void
     */
    protected function view(string $viewPath, array $data = []): void {
        extract($data);

        // 1. Thử tìm đường dẫn trực tiếp trong views/
        $filePath = __DIR__ . '/../views/' . $viewPath . '.php';

        // 2. Nếu chưa có, thử tìm trong views/pages/
        if (!file_exists($filePath)) {
            $filePath = __DIR__ . '/../views/pages/' . $viewPath . '.php';
        }

        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            http_response_code(404);
            die("View không tồn tại: " . htmlspecialchars($viewPath));
        }
    }

    /**
     * Trả về dữ liệu chuẩn JSON cho AJAX
     *
     * @param array $data Dữ liệu cần phản hồi
     * @param int $statusCode Mã HTTP status (200, 400, 405, 500, ...)
     * @return void
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Alias cho jsonResponse để đảm bảo tính tương thích
     */
    public function json(mixed $data, int $statusCode = 200): void {
        if (is_array($data)) {
            $this->jsonResponse($data, $statusCode);
        } else {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * Điều hướng trang (Redirect)
     */
    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}
