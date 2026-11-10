<?php
/**
 * Lớp Base Model (core/Model.php)
 * Cung cấp kết nối PDO dùng chung cho toàn bộ tầng Model.
 * Cho phép tiêm (inject) một PDO có sẵn qua constructor để kiểm thử độc lập.
 */

require_once __DIR__ . '/Database.php';

abstract class Model {
    protected ?PDO $db = null;

    public function __construct(?PDO $db = null) {
        if ($db !== null) {
            $this->db = $db;
            return;
        }
        try {
            $this->db = Database::getInstance();
        } catch (PDOException $e) {
            $this->db = null;
        }
    }

    public function hasConnection(): bool {
        return $this->db instanceof PDO;
    }

    /** Bắt buộc có kết nối CSDL, nếu không ném RuntimeException (không dùng dữ liệu giả). */
    protected function requireDb(): PDO {
        if (!$this->db instanceof PDO) {
            throw new RuntimeException('Không kết nối được cơ sở dữ liệu. Vui lòng kiểm tra MySQL!');
        }
        return $this->db;
    }

    /**
     * Gọi Stored Procedure; luôn nuốt hết result-set và closeCursor() để tránh lỗi PDO 2014
     * (Cannot execute queries while other unbuffered queries are active).
     * Nếu có tham số OUT, đặt tên biến phiên @xxx trong $outVars để đọc về sau khi đóng con trỏ.
     *
     * @param string   $call    Ví dụ: "CALL sp_x(:a, :b, @out)"
     * @param array    $params  Tham số IN
     * @param string[] $outVars Danh sách biến OUT cần đọc, ví dụ ['@out']
     * @return array<string,mixed> Giá trị các biến OUT (khóa không có dấu @)
     */
    protected function callProcedure(string $call, array $params = [], array $outVars = []): array {
        $pdo  = $this->requireDb();
        $stmt = $pdo->prepare($call);
        $stmt->execute($params);
        while ($stmt->nextRowset()) { /* bỏ qua các result-set còn lại */ }
        $stmt->closeCursor();

        $out = [];
        foreach ($outVars as $var) {
            $row = $pdo->query('SELECT ' . $var . ' AS v')->fetch();
            $out[ltrim($var, '@')] = $row['v'] ?? null;
        }
        return $out;
    }

    /** Lấy thông điệp nghiệp vụ gọn từ lỗi SIGNAL SQLSTATE '45000' của Stored Procedure. */
    public static function spMessage(PDOException $e): string {
        $msg = $e->errorInfo[2] ?? $e->getMessage();
        if (($e->getCode() ?? '') === '45000' || stripos($msg, 'Lỗi [') !== false) {
            if (preg_match('/(Lỗi \[[A-Z_0-9]+\][^\r\n]*)/u', $msg, $m)) {
                return $m[1];
            }
            return $msg;
        }
        error_log('[FashionKids] PDO: ' . $e->getMessage());
        return 'Lỗi cơ sở dữ liệu, vui lòng thử lại sau.';
    }
}
