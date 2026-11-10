<?php
/**
 * Lớp Base Controller (core/Controller.php)
 * Đóng vai trò lớp cơ sở cung cấp tiện ích hiển thị View, nạp Model và trả về JSON chuẩn cho AJAX.
 * Tuân thủ nghiêm ngặt 4 Quy chuẩn Kỹ thuật Giai đoạn 4:
 *  1. Khử rác dữ liệu trước khi xuất JSON (ob_clean / clean buffer).
 *  2. Cấu trúc JSON phản hồi đồng nhất (Standard Envelope: status, code, data, message).
 *  3. HTTP Status Code thực tế qua http_response_code($code).
 *  4. Tương thích ngược toàn diện với các khóa phẳng (exists, demo_debug, demo_otp, v.v.).
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
        $_SERVER['FK_CURRENT_VIEW_PAGE'] = basename($viewPath);
        if (class_exists('Rbac')) {
            Rbac::enforceCurrentViewAccess(basename($viewPath));
        }
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
     * Trả về dữ liệu chuẩn JSON cho AJAX tuân thủ Quy chuẩn Kỹ thuật Giai đoạn 4:
     * - Khử rác bộ đệm
     * - Standard Envelope: status, code, data, message
     * - HTTP Status Code thực tế
     * - Giữ tương thích ngược với các trường phẳng (exists, demo_debug, demo_otp, ...)
     *
     * @param array $data Dữ liệu cần phản hồi
     * @param int $statusCode Mã HTTP status (200, 400, 404, 405, 409, 500, ...)
     * @return void
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        // 1. Khử sạch mọi dữ liệu đệm (warning / BOM / khoảng trắng thừa) để chuỗi JSON luôn hợp lệ
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // 2. Thiết lập HTTP Status Code thực tế
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        // 3. Chuẩn hóa Standard Envelope
        $status  = $data['status'] ?? (($statusCode >= 200 && $statusCode < 300) ? 'success' : 'error');
        $code    = (int)($data['code'] ?? $statusCode);
        $message = (string)($data['message'] ?? '');

        // Trích xuất dữ liệu nghiệp vụ vào $envelopeData (tôn trọng giá trị explicit 'data' => null)
        $hasExplicitData = array_key_exists('data', $data);
        if ($hasExplicitData && $data['data'] === null) {
            $finalData = null;
        } elseif ($hasExplicitData && (is_array($data['data']) || is_object($data['data']))) {
            $envelopeData = (array)$data['data'];
            $finalData = empty($envelopeData) ? new stdClass() : $envelopeData;
        } else {
            $envelopeData = array_diff_key($data, array_flip(['status', 'code', 'data', 'message']));
            $finalData = empty($envelopeData) ? new stdClass() : $envelopeData;
        }

        // Tạo mảng phản hồi chuẩn: vừa có {status, code, data, message},
        // vừa có các trường phẳng nguyên bản (exists, demo_debug, demo_otp) để script client nào cũng đọc được
        $response = array_merge($data, [
            'status'  => $status,
            'code'    => $code,
            'data'    => $finalData,
            'message' => $message,
        ]);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Alias cho jsonResponse để đảm bảo tính tương thích
     */
    public function json(mixed $data, int $statusCode = 200): void {
        if (is_array($data)) {
            $this->jsonResponse($data, $statusCode);
        } else {
            $this->jsonResponse(['result' => $data], $statusCode);
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
