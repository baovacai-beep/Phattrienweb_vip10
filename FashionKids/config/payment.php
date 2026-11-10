<?php
/**
 * Cấu hình Thanh toán VietQR MB Bank & Đối soát giao dịch Casso API v2 (config/payment.php)
 */

if (!defined('VIETQR_BANK_ID'))                define('VIETQR_BANK_ID', 'MB');
if (!defined('VIETQR_BANK_NAME'))              define('VIETQR_BANK_NAME', 'MB Bank');
if (!defined('VIETQR_BANK_BIN'))               define('VIETQR_BANK_BIN', '970422');
if (!defined('VIETQR_ACCOUNT_NO'))             define('VIETQR_ACCOUNT_NO', '0913672861');
if (!defined('VIETQR_ACCOUNT_NAME'))           define('VIETQR_ACCOUNT_NAME', 'HO GIA BAO');
if (!defined('VIETQR_TEMPLATE'))               define('VIETQR_TEMPLATE', 'compact2');
if (!defined('VIETQR_TRANSFER_PREFIX'))        define('VIETQR_TRANSFER_PREFIX', 'FK');
if (!defined('VIETQR_PAYMENT_TTL_HOURS'))      define('VIETQR_PAYMENT_TTL_HOURS', 24);
if (!defined('VIETQR_PAYMENT_TTL_SECONDS'))    define('VIETQR_PAYMENT_TTL_SECONDS', VIETQR_PAYMENT_TTL_HOURS * 3600);

// API Key Casso v2 (Đối soát tự động giao dịch MB Bank 0913672861)
if (!defined('CASSO_API_KEY')) {
    define('CASSO_API_KEY', 'AK_CS.1cb91270dd2711f199d49bc26a4c3301.5gl00OMMisQeus4aQSefyGumrVL0jAoWhZsBRf0grt67QNG7SO6Jq0cGFu8EzxHmVdQCj5U4');
}
if (!defined('CASSO_API_BASE'))                define('CASSO_API_BASE', 'https://oauth.casso.vn/v2');
if (!defined('CASSO_SYNC_COOLDOWN_SEC'))       define('CASSO_SYNC_COOLDOWN_SEC', 15);
if (!defined('CASSO_MANUAL_SYNC_COOLDOWN_SEC'))define('CASSO_MANUAL_SYNC_COOLDOWN_SEC', 8);
if (!defined('CASSO_POLL_CACHE_TTL_SEC'))      define('CASSO_POLL_CACHE_TTL_SEC', 4);
if (!defined('CASSO_WEBHOOK_SECRET'))          define('CASSO_WEBHOOK_SECRET', 'FK_CASSO_WEBHOOK_2026_MB0913672861');
