<?php
/**
 * Cấu hình tập trung các API bên thứ 3 & Dịch vụ tích hợp (config/integrations.php)
 * Tự động đọc giá trị từ tệp .env (nếu có) thông qua core/Env.php
 */

require_once __DIR__ . '/../core/Env.php';
Env::load(__DIR__ . '/../.env');

// 1. Giao Hàng Nhanh (GHN) & Webhook đối tác vận chuyển
if (!defined('GHN_TOKEN'))             define('GHN_TOKEN', Env::get('GHN_TOKEN', ''));
if (!defined('GHN_SHOP_ID'))           define('GHN_SHOP_ID', Env::get('GHN_SHOP_ID', ''));
if (!defined('GHN_FROM_DISTRICT_ID'))  define('GHN_FROM_DISTRICT_ID', (int)Env::get('GHN_FROM_DISTRICT_ID', '1442'));
if (!defined('GHN_FROM_WARD_CODE'))    define('GHN_FROM_WARD_CODE', Env::get('GHN_FROM_WARD_CODE', '20101'));
if (!defined('GHN_SANDBOX')) {
    $sandboxVal = strtolower(Env::get('GHN_SANDBOX', 'true'));
    define('GHN_SANDBOX', !in_array($sandboxVal, ['false', '0', 'no'], true));
}
if (!defined('GHN_API_BASE')) {
    define('GHN_API_BASE', GHN_SANDBOX
        ? 'https://dev-online-gateway.ghn.vn/shiip/public-api'
        : 'https://online-gateway.ghn.vn/shiip/public-api'
    );
}
if (!defined('SHIPPING_WEBHOOK_SECRET')) {
    define('SHIPPING_WEBHOOK_SECRET', Env::get('SHIPPING_WEBHOOK_SECRET', 'fk_webhook_demo_secret_2026'));
}

// 2. Google OAuth 2.0
if (!defined('GOOGLE_CLIENT_ID'))      define('GOOGLE_CLIENT_ID', Env::get('GOOGLE_CLIENT_ID', ''));
if (!defined('GOOGLE_CLIENT_SECRET'))  define('GOOGLE_CLIENT_SECRET', Env::get('GOOGLE_CLIENT_SECRET', ''));
if (!defined('GOOGLE_REDIRECT_URI'))   define('GOOGLE_REDIRECT_URI', Env::get('GOOGLE_REDIRECT_URI', ''));

// 3. Cloudflare Turnstile
if (!defined('TURNSTILE_SITE_KEY'))    define('TURNSTILE_SITE_KEY', Env::get('TURNSTILE_SITE_KEY', ''));
if (!defined('TURNSTILE_SECRET_KEY'))  define('TURNSTILE_SECRET_KEY', Env::get('TURNSTILE_SECRET_KEY', ''));

// 4. Google Gemini API (AI Tư vấn Size & Chất liệu Sợi Tre)
if (!defined('GEMINI_API_KEY'))        define('GEMINI_API_KEY', Env::get('GEMINI_API_KEY', ''));
if (!defined('GEMINI_MODEL'))          define('GEMINI_MODEL', Env::get('GEMINI_MODEL', 'gemini-3-flash-preview'));

// 5. VietQR Banks Directory API
if (!defined('VIETQR_BANKS_API_URL'))  define('VIETQR_BANKS_API_URL', 'https://api.vietqr.io/v2/banks');
