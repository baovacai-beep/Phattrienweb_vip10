<?php
/**
 * Dịch vụ Danh mục Ngân hàng Việt Nam VietQR (core/VietQrBankService.php)
 * Nguồn chính: https://api.vietqr.io/v2/banks (cache 24h) + danh sách dự phòng 25 ngân hàng lớn
 */

require_once __DIR__ . '/../config/integrations.php';

class VietQrBankService {
    public static function getBanks(bool $forceRefresh = false): array {
        $cacheFile = rtrim(sys_get_temp_dir(), '/\\') . '/fk_vietqr_banks_v2.json';
        if (!$forceRefresh && is_file($cacheFile) && (time() - (int)@filemtime($cacheFile)) < 86400) {
            $cached = json_decode((string)@file_get_contents($cacheFile), true);
            if (is_array($cached) && !empty($cached['banks'])) {
                return [
                    'source' => 'cache',
                    'banks'  => $cached['banks'],
                ];
            }
        }

        if (function_exists('curl_init')) {
            $url = defined('VIETQR_BANKS_API_URL') ? VIETQR_BANKS_API_URL : 'https://api.vietqr.io/v2/banks';
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $raw = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($raw !== false && $code >= 200 && $code < 300) {
                $json = json_decode($raw, true);
                if (is_array($json) && !empty($json['data']) && is_array($json['data'])) {
                    $banks = [];
                    foreach ($json['data'] as $item) {
                        if (empty($item['bin'])) {
                            continue;
                        }
                        $banks[] = [
                            'id'              => (int)($item['id'] ?? 0),
                            'name'            => (string)($item['name'] ?? ''),
                            'code'            => (string)($item['code'] ?? ''),
                            'bin'             => (string)$item['bin'],
                            'shortName'       => (string)($item['shortName'] ?? ($item['code'] ?? '')),
                            'logo'            => (string)($item['logo'] ?? ''),
                            'transferSupported' => (int)($item['transferSupported'] ?? 1),
                        ];
                    }
                    if (!empty($banks)) {
                        @file_put_contents($cacheFile, json_encode(['banks' => $banks]));
                        return [
                            'source' => 'vietqr_live',
                            'banks'  => $banks,
                        ];
                    }
                }
            }
        }

        return [
            'source' => 'fallback',
            'banks'  => self::getFallbackBanks(),
        ];
    }

    public static function getFallbackBanks(): array {
        return [
            ['bin' => '970422', 'code' => 'MB',      'shortName' => 'MBBank',         'name' => 'Ngân hàng TMCP Quân đội', 'logo' => 'https://api.vietqr.io/img/MB.png'],
            ['bin' => '970436', 'code' => 'VCB',     'shortName' => 'Vietcombank',    'name' => 'Ngân hàng TMCP Ngoại Thương Việt Nam', 'logo' => 'https://api.vietqr.io/img/VCB.png'],
            ['bin' => '970407', 'code' => 'TCB',     'shortName' => 'Techcombank',    'name' => 'Ngân hàng TMCP Kỹ thương Việt Nam', 'logo' => 'https://api.vietqr.io/img/TCB.png'],
            ['bin' => '970415', 'code' => 'ICB',     'shortName' => 'VietinBank',     'name' => 'Ngân hàng TMCP Công thương Việt Nam', 'logo' => 'https://api.vietqr.io/img/ICB.png'],
            ['bin' => '970418', 'code' => 'BIDV',    'shortName' => 'BIDV',           'name' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam', 'logo' => 'https://api.vietqr.io/img/BIDV.png'],
            ['bin' => '970405', 'code' => 'VBA',     'shortName' => 'Agribank',       'name' => 'Ngân hàng Nông nghiệp và Phát triển Nông thôn', 'logo' => 'https://api.vietqr.io/img/VBA.png'],
            ['bin' => '970416', 'code' => 'ACB',     'shortName' => 'ACB',            'name' => 'Ngân hàng TMCP Á Châu', 'logo' => 'https://api.vietqr.io/img/ACB.png'],
            ['bin' => '970432', 'code' => 'VPB',     'shortName' => 'VPBank',         'name' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng', 'logo' => 'https://api.vietqr.io/img/VPB.png'],
            ['bin' => '970423', 'code' => 'TPB',     'shortName' => 'TPBank',         'name' => 'Ngân hàng TMCP Tiên Phong', 'logo' => 'https://api.vietqr.io/img/TPB.png'],
            ['bin' => '970403', 'code' => 'STB',     'shortName' => 'Sacombank',      'name' => 'Ngân hàng TMCP Sài Gòn Thương Tín', 'logo' => 'https://api.vietqr.io/img/STB.png'],
            ['bin' => '970441', 'code' => 'VIB',     'shortName' => 'VIB',            'name' => 'Ngân hàng TMCP Quốc tế Việt Nam', 'logo' => 'https://api.vietqr.io/img/VIB.png'],
            ['bin' => '970437', 'code' => 'HDB',     'shortName' => 'HDBank',         'name' => 'Ngân hàng TMCP Phát triển TP.HCM', 'logo' => 'https://api.vietqr.io/img/HDB.png'],
            ['bin' => '970426', 'code' => 'MSB',     'shortName' => 'MSB',            'name' => 'Ngân hàng TMCP Hàng Hải', 'logo' => 'https://api.vietqr.io/img/MSB.png'],
            ['bin' => '970448', 'code' => 'OCB',     'shortName' => 'OCB',            'name' => 'Ngân hàng TMCP Phương Đông', 'logo' => 'https://api.vietqr.io/img/OCB.png'],
            ['bin' => '970443', 'code' => 'SHB',     'shortName' => 'SHB',            'name' => 'Ngân hàng TMCP Sài Gòn - Hà Nội', 'logo' => 'https://api.vietqr.io/img/SHB.png'],
            ['bin' => '970431', 'code' => 'EIB',     'shortName' => 'Eximbank',       'name' => 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam', 'logo' => 'https://api.vietqr.io/img/EIB.png'],
            ['bin' => '970449', 'code' => 'LPB',     'shortName' => 'LPBank',         'name' => 'Ngân hàng TMCP Lộc Phát Việt Nam', 'logo' => 'https://api.vietqr.io/img/LPB.png'],
            ['bin' => '970440', 'code' => 'SEAB',    'shortName' => 'SeABank',        'name' => 'Ngân hàng TMCP Đông Nam Á', 'logo' => 'https://api.vietqr.io/img/SEAB.png'],
            ['bin' => '970454', 'code' => 'VCCB',    'shortName' => 'BVBank',         'name' => 'Ngân hàng TMCP Bản Việt', 'logo' => 'https://api.vietqr.io/img/VCCB.png'],
            ['bin' => '546034', 'code' => 'CAKE',    'shortName' => 'CAKE by VPBank', 'name' => 'Ngân hàng số CAKE by VPBank', 'logo' => 'https://api.vietqr.io/img/CAKE.png'],
            ['bin' => '963388', 'code' => 'TIMO',    'shortName' => 'Timo',           'name' => 'Ngân hàng số Timo by Ban Viet Bank', 'logo' => 'https://api.vietqr.io/img/TIMO.png'],
        ];
    }
}
