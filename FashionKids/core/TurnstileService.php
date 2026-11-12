<?php
/**
 * Dịch vụ Cloudflare Turnstile (core/TurnstileService.php)
 * Bảo vệ form Đăng nhập, Gửi OTP & Đăng ký KOL Nhí khỏi bot/spam
 */

require_once __DIR__ . '/../config/integrations.php';

class TurnstileService {
    public static function isConfigured(): bool {
        return defined('TURNSTILE_SITE_KEY') && defined('TURNSTILE_SECRET_KEY')
            && trim((string)TURNSTILE_SITE_KEY) !== ''
            && trim((string)TURNSTILE_SECRET_KEY) !== '';
    }

    public static function getSiteKey(): string {
        return defined('TURNSTILE_SITE_KEY') ? trim((string)TURNSTILE_SITE_KEY) : '';
    }

    /**
     * Xác thực mã phản hồi Turnstile với máy chủ Cloudflare.
     * Nếu chưa cấu hình Key trên môi trường phát triển, cho phép đi tiếp với flag skipped=true.
     */
    public static function verifyToken(?string $token, ?string $remoteIp = null): array {
        if (!self::isConfigured()) {
            return [
                'success' => true,
                'skipped' => true,
                'message' => 'Turnstile chưa được cấu hình (bỏ qua trên môi trường cục bộ).'
            ];
        }

        $cleanToken = trim((string)$token);
        if ($cleanToken === '') {
            return [
                'success' => false,
                'skipped' => false,
                'message' => 'Vui lòng hoàn thành xác minh bảo mật Cloudflare Turnstile trước khi tiếp tục.'
            ];
        }

        if (!function_exists('curl_init')) {
            return [
                'success' => false,
                'skipped' => false,
                'message' => 'Máy chủ PHP chưa bật cURL để xác thực Turnstile.'
            ];
        }

        $postFields = [
            'secret'   => trim((string)TURNSTILE_SECRET_KEY),
            'response' => $cleanToken,
        ];
        if (!empty($remoteIp)) {
            $postFields['remoteip'] = $remoteIp;
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $postFields['remoteip'] = $_SERVER['REMOTE_ADDR'];
        }

        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postFields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code < 200 || $code >= 300) {
            error_log('[TurnstileService] cURL error: ' . $err . ' (HTTP ' . $code . ')');
            return [
                'success' => false,
                'skipped' => false,
                'message' => 'Không thể kết nối máy chủ xác minh Turnstile. Vui lòng thử lại!'
            ];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || empty($decoded['success'])) {
            return [
                'success' => false,
                'skipped' => false,
                'message' => 'Xác minh bảo mật Turnstile không hợp lệ hoặc đã hết hạn. Vui lòng tích lại ô xác nhận!'
            ];
        }

        return [
            'success' => true,
            'skipped' => false,
            'message' => 'Xác minh bảo mật Turnstile thành công.'
        ];
    }
}
