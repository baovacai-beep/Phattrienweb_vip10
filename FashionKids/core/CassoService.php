<?php
/**
 * Dịch vụ Đối soát Ngân hàng MB Bank qua Casso API v2 (core/CassoService.php)
 * Tự động đồng bộ & kiểm tra giao dịch chuyển khoản thật qua tài khoản MB Bank 0913672861 (HO GIA BAO).
 */

require_once __DIR__ . '/../config/payment.php';

class CassoService {
    private string $apiKey;
    private string $apiBase;
    private string $accountNo;
    private string $transferPrefix;
    /** @var callable|null */
    private $httpTransport;

    public function __construct(
        ?string $apiKey = null,
        ?string $accountNo = null,
        ?callable $httpTransport = null
    ) {
        $this->apiKey         = $apiKey ?? (defined('CASSO_API_KEY') ? CASSO_API_KEY : '');
        $this->apiBase        = rtrim(defined('CASSO_API_BASE') ? CASSO_API_BASE : 'https://oauth.casso.vn/v2', '/');
        $this->accountNo      = $accountNo ?? (defined('VIETQR_ACCOUNT_NO') ? VIETQR_ACCOUNT_NO : '0913672861');
        $this->transferPrefix = defined('VIETQR_TRANSFER_PREFIX') ? VIETQR_TRANSFER_PREFIX : 'FK';
        $this->httpTransport  = $httpTransport;
    }

    /**
     * Gọi HTTP tới Casso API v2 với header Authorization: Apikey ...
     */
    private function request(string $method, string $path, ?array $body = null): array {
        if ($this->httpTransport !== null) {
            return call_user_func($this->httpTransport, $method, $path, $body);
        }

        if ($this->apiKey === '') {
            return [
                'ok'        => false,
                'http_code' => 0,
                'error'     => 'Chưa cấu hình CASSO_API_KEY trong config/payment.php.',
                'payload'   => null,
            ];
        }

        if (!function_exists('curl_init')) {
            return [
                'ok'        => false,
                'http_code' => 0,
                'error'     => 'Máy chủ PHP chưa bật ext-curl để kết nối Casso.',
                'payload'   => null,
            ];
        }

        $url = $this->apiBase . '/' . ltrim($path, '/');
        $ch  = curl_init($url);
        $headers = [
            'Authorization: Apikey ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
        }

        curl_setopt_array($ch, $opts);
        $rawBody  = curl_exec($ch);
        $curlErr  = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($rawBody === false || $curlErr !== '') {
            return [
                'ok'        => false,
                'http_code' => $httpCode,
                'error'     => 'Không thể kết nối Casso API: ' . ($curlErr ?: 'Lỗi mạng'),
                'payload'   => null,
            ];
        }

        $decoded = json_decode((string)$rawBody, true);
        if (!is_array($decoded)) {
            return [
                'ok'        => false,
                'http_code' => $httpCode,
                'error'     => 'Phản hồi Casso API không hợp lệ (HTTP ' . $httpCode . ').',
                'payload'   => null,
            ];
        }

        $apiErrorCode = (int)($decoded['error'] ?? 0);
        if ($httpCode >= 400 || $apiErrorCode !== 0) {
            return [
                'ok'        => false,
                'http_code' => $httpCode,
                'error'     => (string)($decoded['message'] ?? ('Casso trả lỗi HTTP ' . $httpCode)),
                'payload'   => $decoded,
            ];
        }

        return [
            'ok'        => true,
            'http_code' => $httpCode,
            'error'     => null,
            'payload'   => $decoded,
        ];
    }

    /**
     * Yêu cầu Casso đồng bộ ngay giao dịch mới từ MB Bank (giới hạn tần suất tránh 429)
     */
    public function triggerSync(bool $manual = false): array {
        $cooldown = $manual
            ? (defined('CASSO_MANUAL_SYNC_COOLDOWN_SEC') ? (int)CASSO_MANUAL_SYNC_COOLDOWN_SEC : 8)
            : (defined('CASSO_SYNC_COOLDOWN_SEC') ? (int)CASSO_SYNC_COOLDOWN_SEC : 15);

        $lockFile = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'fk_casso_sync_' . md5($this->accountNo) . '.ts';
        $lastSync = is_file($lockFile) ? (int)@file_get_contents($lockFile) : 0;
        $now      = time();

        if ($this->httpTransport === null && $lastSync > 0 && ($now - $lastSync) < $cooldown) {
            return [
                'ok'      => true,
                'synced'  => false,
                'skipped' => true,
                'message' => 'Đã đồng bộ gần đây, đang đọc danh sách giao dịch mới nhất.',
            ];
        }

        if ($this->httpTransport === null) {
            @file_put_contents($lockFile, (string)$now);
        }

        $resp = $this->request('POST', 'sync', [
            'bank_acc_id' => $this->accountNo,
        ]);

        return [
            'ok'      => $resp['ok'],
            'synced'  => $resp['ok'],
            'skipped' => false,
            'message' => $resp['ok']
                ? 'Đã gửi lệnh đồng bộ giao dịch MB Bank thành công.'
                : ($resp['error'] ?? 'Không thể đồng bộ tức thì với MB Bank.'),
        ];
    }

    /**
     * Lấy danh sách giao dịch ngân hàng từ Casso API v2
     */
    public function fetchTransactions(int $pageSize = 40, ?string $fromDate = null, bool $useShortCache = false): array {
        $pageSize = max(5, min(100, $pageSize));
        $queryParams = [
            'page'         => 1,
            'pageSize'     => $pageSize,
            'sort'         => 'DESC',
            'bankSubAccId' => $this->accountNo,
        ];
        if ($fromDate !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
            $queryParams['fromDate'] = $fromDate;
        }

        $cacheTtl  = defined('CASSO_POLL_CACHE_TTL_SEC') ? (int)CASSO_POLL_CACHE_TTL_SEC : 4;
        $cacheFile = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'fk_casso_tx_' . md5($this->accountNo . '|' . ($fromDate ?? '') . '|' . $pageSize) . '.json';

        if ($useShortCache && $this->httpTransport === null && is_file($cacheFile)) {
            $age = time() - (int)@filemtime($cacheFile);
            if ($age >= 0 && $age <= $cacheTtl) {
                $cached = json_decode((string)@file_get_contents($cacheFile), true);
                if (is_array($cached) && isset($cached['records'])) {
                    return [
                        'ok'        => true,
                        'fromCache' => true,
                        'error'     => null,
                        'records'   => $cached['records'],
                    ];
                }
            }
        }

        $resp = $this->request('GET', 'transactions?' . http_build_query($queryParams));
        if (!$resp['ok']) {
            return [
                'ok'        => false,
                'fromCache' => false,
                'error'     => $resp['error'] ?? 'Không thể truy vấn giao dịch từ Casso.',
                'records'   => [],
            ];
        }

        $records = $resp['payload']['data']['records'] ?? $resp['payload']['records'] ?? [];
        if (!is_array($records)) {
            $records = [];
        }

        if ($this->httpTransport === null) {
            @file_put_contents($cacheFile, json_encode([
                'saved_at' => time(),
                'records'  => $records,
            ], JSON_UNESCAPED_UNICODE));
        }

        return [
            'ok'        => true,
            'fromCache' => false,
            'error'     => null,
            'records'   => $records,
        ];
    }

    /**
     * Xây dựng mã giao dịch chuẩn để lưu CSDL & chống dùng lại 1 giao dịch cho nhiều đơn
     */
    public function extractTransactionKey(array $tx): string {
        $tid = trim((string)($tx['tid'] ?? ''));
        if ($tid !== '') {
            return $tid;
        }
        $id = $tx['id'] ?? '';
        return ($id !== '' && $id !== null) ? ('CASSO-' . (string)$id) : '';
    }

    /**
     * Kiểm tra nội dung chuyển khoản có khớp chính xác mã đơn hàng hay không (tránh FK4 khớp nhầm FK40)
     */
    public function matchesOrderDescription(string $description, int $orderId): bool {
        if ($orderId <= 0 || trim($description) === '') {
            return false;
        }
        $prefix = preg_quote($this->transferPrefix, '/');
        // Khớp: FK4, FK000004, FK-ORD-000004, FKORD4 ở ranh giới ký tự rõ ràng (sau số không được là chữ số khác)
        $pattern = '/(?:^|[^A-Z0-9])' . $prefix . '(?:[-\s]?ORD[-\s]?)?0*' . $orderId . '(?![0-9])/i';
        return preg_match($pattern, $description) === 1;
    }

    /**
     * Phân tích & đối soát danh sách giao dịch với 1 đơn hàng cụ thể.
     * - Chỉ tính tiền vào (amount > 0)
     * - Kiểm tra khớp tài khoản MB Bank nhận tiền (0913672861)
     * - Loại trừ các mã giao dịch đã dùng cho đơn khác ($usedTxKeys)
     * - Kiểm tra cửa sổ thời gian (cho phép sai lệch -5 phút do Casso làm tròn giây :00)
     * - Trả về trạng thái: paid / underpaid / pending
     */
    public function matchOrderTransactions(array $order, array $records, array $usedTxKeys = []): array {
        $orderId        = (int)($order['ma_don_hang'] ?? 0);
        $expectedAmount = max(0, (int)round((float)($order['tong_tien_thanh_toan'] ?? 0)));
        if ($orderId <= 0) {
            return ['matched' => false, 'status' => 'pending', 'message' => 'Mã đơn hàng không hợp lệ.'];
        }

        $usedLookup = [];
        foreach ($usedTxKeys as $uk) {
            $ukTrim = trim((string)$uk);
            if ($ukTrim !== '') {
                $usedLookup[$ukTrim] = true;
            }
        }

        $createdTs  = !empty($order['ngay_dat_hang']) ? strtotime((string)$order['ngay_dat_hang']) : time();
        $ttlSec     = defined('VIETQR_PAYMENT_TTL_SECONDS') ? (int)VIETQR_PAYMENT_TTL_SECONDS : 86400;
        $deadlineTs = !empty($order['han_thanh_toan'])
            ? strtotime((string)$order['han_thanh_toan'])
            : ($createdTs + $ttlSec);

        // Chấp nhận giao dịch trong khoảng [createdTs - 300s, deadlineTs + 300s] do Casso ghi timestamp tròn phút (:00)
        $minValidTs = $createdTs - 300;
        $maxValidTs = $deadlineTs + 300;

        $candidates = [];
        foreach ($records as $tx) {
            if (!is_array($tx)) {
                continue;
            }
            $amount = (float)($tx['amount'] ?? 0);
            if ($amount <= 0) {
                continue; // Chỉ nhận tiền ghi có (+)
            }

            $subAcc = trim((string)($tx['bankSubAccId'] ?? ''));
            if ($subAcc !== '' && $subAcc !== $this->accountNo) {
                continue; // Khác số tài khoản thụ hưởng
            }

            $txKey = $this->extractTransactionKey($tx);
            if ($txKey === '' || isset($usedLookup[$txKey])) {
                continue; // Giao dịch đã gắn cho đơn hàng khác (chống replay)
            }

            $desc = (string)($tx['description'] ?? '');
            if (!$this->matchesOrderDescription($desc, $orderId)) {
                continue;
            }

            $whenRaw = trim((string)($tx['when'] ?? ''));
            $whenTs  = $whenRaw !== '' ? strtotime($whenRaw) : false;
            if ($whenTs !== false) {
                if ($whenTs < $minValidTs || $whenTs > $maxValidTs) {
                    continue; // Ngoài khung thời gian hiệu lực đơn hàng
                }
            }

            $candidates[] = [
                'tx_key'      => $txKey,
                'tid'         => (string)($tx['tid'] ?? $txKey),
                'casso_id'    => $tx['id'] ?? null,
                'amount'      => (int)round($amount),
                'description' => $desc,
                'when_raw'    => $whenRaw,
                'when_mysql'  => $whenTs !== false ? date('Y-m-d H:i:s', $whenTs) : date('Y-m-d H:i:s'),
                'when_ts'     => $whenTs !== false ? $whenTs : time(),
            ];
        }

        if (empty($candidates)) {
            return [
                'matched'         => false,
                'status'          => 'pending',
                'expected_amount' => $expectedAmount,
                'received_amount' => 0,
                'message'         => 'Chưa ghi nhận giao dịch chuyển khoản mới mang nội dung ' . $this->transferPrefix . $orderId . ' trên MB Bank (' . $this->accountNo . ').',
            ];
        }

        // Sắp xếp giao dịch mới nhất lên đầu trước khi chọn mã GD đại diện
        usort($candidates, static function (array $a, array $b): int {
            $cmpTs = ($b['when_ts'] ?? 0) <=> ($a['when_ts'] ?? 0);
            if ($cmpTs !== 0) {
                return $cmpTs;
            }
            return strcmp((string)($b['tx_key'] ?? ''), (string)($a['tx_key'] ?? ''));
        });

        // 1. Ưu tiên giao dịch đơn lẻ chuyển đủ (>= số tiền cần thanh toán)
        foreach ($candidates as $cand) {
            if ($cand['amount'] >= $expectedAmount) {
                return [
                    'matched'         => true,
                    'status'          => 'paid',
                    'tx_key'          => $cand['tx_key'],
                    'tx_keys'         => [$cand['tx_key']],
                    'tid'             => $cand['tid'],
                    'casso_id'        => $cand['casso_id'],
                    'amount'          => $cand['amount'],
                    'expected_amount' => $expectedAmount,
                    'paid_at'         => $cand['when_mysql'],
                    'description'     => $cand['description'],
                    'message'         => 'Đã xác thực chuyển khoản ' . number_format($cand['amount'], 0, ',', '.') . ' đ từ MB Bank (Mã GD: ' . $cand['tx_key'] . ').',
                ];
            }
        }

        // 2. Hỗ trợ cộng dồn nhiều lần chuyển đúng mã đơn trong cùng cửa sổ hiệu lực nếu ban đầu khách chuyển thiếu
        $sumAmount = 0;
        $allKeys   = [];
        foreach ($candidates as $cand) {
            $sumAmount += $cand['amount'];
            $allKeys[]  = $cand['tx_key'];
        }
        $allKeys = array_values(array_unique(array_filter($allKeys)));
        $latest  = $candidates[0];
        if ($sumAmount >= $expectedAmount) {
            return [
                'matched'         => true,
                'status'          => 'paid',
                'tx_key'          => $latest['tx_key'],
                'tx_keys'         => $allKeys,
                'tid'             => $latest['tid'],
                'casso_id'        => $latest['casso_id'],
                'amount'          => $sumAmount,
                'expected_amount' => $expectedAmount,
                'paid_at'         => $latest['when_mysql'],
                'description'     => $latest['description'],
                'message'         => 'Đã xác thực đủ ' . number_format($sumAmount, 0, ',', '.') . ' đ qua nhiều lần chuyển khoản (Mã GD cuối: ' . $latest['tx_key'] . ').',
            ];
        }

        $shortage = max(0, $expectedAmount - $sumAmount);
        return [
            'matched'         => false,
            'status'          => 'underpaid',
            'expected_amount' => $expectedAmount,
            'received_amount' => $sumAmount,
            'shortage'        => $shortage,
            'latest_tx'       => $latest,
            'message'         => 'MB Bank mới nhận ' . number_format($sumAmount, 0, ',', '.') . ' đ cho mã ' . $this->transferPrefix . $orderId . ' (còn thiếu ' . number_format($shortage, 0, ',', '.') . ' đ so với tổng ' . number_format($expectedAmount, 0, ',', '.') . ' đ). Vui lòng chuyển đủ phần còn thiếu để hệ thống tự xác nhận!',
        ];
    }

    /**
     * Xác thực Secret Token cho Casso Webhook v2 (header Secure-Token)
     */
    public function verifyWebhookToken(string $receivedToken): bool {
        $expected = defined('CASSO_WEBHOOK_SECRET') ? CASSO_WEBHOOK_SECRET : '';
        if ($expected === '' || $receivedToken === '') {
            return false;
        }
        return hash_equals($expected, trim($receivedToken));
    }

    /**
     * Parse dữ liệu Webhook Casso v2 trả về mảng transactions chuẩn
     */
    public function parseWebhookPayload(string $rawJson): array {
        $decoded = json_decode($rawJson, true);
        if (!is_array($decoded)) {
            return [];
        }
        $data = $decoded['data'] ?? null;
        if (!is_array($data)) {
            return [];
        }
        // Webhook v2 gửi { error: 0, data: [ {id, tid, description, amount, when, bank_sub_acc_id/bankSubAccId...} ] }
        $normalized = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }
            $normalized[] = [
                'id'           => $row['id'] ?? null,
                'tid'          => $row['tid'] ?? '',
                'description'  => $row['description'] ?? '',
                'amount'       => $row['amount'] ?? 0,
                'when'         => $row['when'] ?? ($row['transactionDateTime'] ?? ''),
                'bankSubAccId' => $row['bankSubAccId'] ?? ($row['bank_sub_acc_id'] ?? ($row['subAccId'] ?? '')),
            ];
        }
        return $normalized;
    }
}
