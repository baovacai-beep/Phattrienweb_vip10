<?php
/**
 * Dịch vụ Google OAuth 2.0 (core/GoogleOAuthService.php)
 * Thuần PHP cURL - Không cần Composer google/apiclient
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/integrations.php';

class GoogleOAuthService {
    public static function isConfigured(): bool {
        return defined('GOOGLE_CLIENT_ID') && defined('GOOGLE_CLIENT_SECRET')
            && trim((string)GOOGLE_CLIENT_ID) !== ''
            && trim((string)GOOGLE_CLIENT_SECRET) !== '';
    }

    public static function getRedirectUri(): string {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443)
            || (strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');
        $scheme = $isHttps ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $hostOnly = strtolower(preg_replace('/:\d+$/', '', (string)$host));

        $configured = defined('GOOGLE_REDIRECT_URI') ? trim((string)GOOGLE_REDIRECT_URI) : '';
        if ($configured !== '') {
            $cfgHost = strtolower((string)(parse_url($configured, PHP_URL_HOST) ?? ''));
            if ($cfgHost !== '' && $cfgHost === $hostOnly) {
                return $configured;
            }
        }

        $endpoint = defined('APP_ENDPOINT') ? APP_ENDPOINT : '/FashionKids/index.php';
        return $scheme . '://' . $host . $endpoint . '?c=auth&a=googleCallback';
    }

    public static function getAuthUrl(string $state): string {
        $params = [
            'client_id'     => trim((string)GOOGLE_CLIENT_ID),
            'redirect_uri'  => self::getRedirectUri(),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Đổi authorization code lấy access_token rồi gọi OIDC userinfo endpoint
     */
    public static function exchangeCodeForUser(string $code): array {
        if (!self::isConfigured()) {
            return [
                'success' => false,
                'message' => 'Chưa cấu hình GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET trong tệp .env.'
            ];
        }
        if (!function_exists('curl_init')) {
            return [
                'success' => false,
                'message' => 'Máy chủ PHP chưa bật cURL.'
            ];
        }

        // Bước 1: Đổi code lấy token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => trim((string)GOOGLE_CLIENT_ID),
                'client_secret' => trim((string)GOOGLE_CLIENT_SECRET),
                'redirect_uri'  => self::getRedirectUri(),
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $tokenRaw  = curl_exec($ch);
        $tokenHttp = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $tokenErr  = curl_error($ch);
        curl_close($ch);

        $tokenJson = json_decode((string)$tokenRaw, true);
        if ($tokenRaw === false || $tokenHttp < 200 || $tokenHttp >= 300 || empty($tokenJson['access_token'])) {
            $desc = is_array($tokenJson) ? ($tokenJson['error_description'] ?? ($tokenJson['error'] ?? '')) : '';
            error_log('[GoogleOAuthService] Token exchange failed: ' . $tokenErr . ' ' . $desc);
            return [
                'success' => false,
                'message' => 'Không thể đổi mã xác thực Google (' . ($desc ?: ('HTTP ' . $tokenHttp)) . ').'
            ];
        }

        // Bước 2: Lấy hồ sơ người dùng từ OpenID Connect UserInfo
        $ch2 = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
        curl_setopt_array($ch2, [
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenJson['access_token']],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $infoRaw  = curl_exec($ch2);
        $infoHttp = (int)curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        $info = json_decode((string)$infoRaw, true);
        if ($infoRaw === false || $infoHttp < 200 || $infoHttp >= 300 || empty($info['sub']) || empty($info['email'])) {
            return [
                'success' => false,
                'message' => 'Không lấy được thông tin Email/Hồ sơ từ tài khoản Google.'
            ];
        }

        return [
            'success'        => true,
            'google_id'      => (string)$info['sub'],
            'email'          => strtolower(trim((string)$info['email'])),
            'name'           => trim((string)($info['name'] ?? ($info['given_name'] ?? 'Phụ huynh FashionKids'))),
            'avatar'         => trim((string)($info['picture'] ?? '')),
            'email_verified' => !empty($info['email_verified']),
        ];
    }
}
