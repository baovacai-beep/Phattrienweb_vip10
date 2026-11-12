<?php
/**
 * Dịch vụ Vận chuyển & Đối soát Đơn vị Vận chuyển (core/ShippingService.php)
 * Tích hợp Giao Hàng Nhanh (GHN API v2) + Bảng cước chuẩn vùng/khối lượng dự phòng tại Việt Nam
 */

require_once __DIR__ . '/../config/integrations.php';

class ShippingService {
    public static function isGhnConfigured(): bool {
        return defined('GHN_TOKEN') && defined('GHN_SHOP_ID')
            && trim((string)GHN_TOKEN) !== ''
            && trim((string)GHN_SHOP_ID) !== '';
    }

    /**
     * Tính phí vận chuyển dựa trên Tỉnh/Thành, Quận/Huyện, Phường/Xã, Khối lượng (gram) & Gói dịch vụ
     */
    public static function calculateFee(
        string $province,
        string $district = '',
        string $ward = '',
        int $weightGram = 300,
        string $serviceType = 'standard',
        float $subtotal = 0.0
    ): array {
        $provinceClean = trim($province);
        $districtClean = trim($district);
        $wardClean     = trim($ward);
        $weight        = max(150, min(30000, $weightGram));
        $service       = ($serviceType === 'express') ? 'express' : 'standard';
        $serviceLabel  = ($service === 'express') ? 'Giao nhanh GHN (24-48h)' : 'Giao tiêu chuẩn GHN (2-4 ngày)';

        $resolvedIds = ['district_id' => 0, 'ward_code' => ''];

        // Nếu đã cấu hình GHN Token & Shop ID: thử tra cứu mã địa chỉ & gọi API tính phí thật
        if (self::isGhnConfigured() && $provinceClean !== '' && $districtClean !== '') {
            $resolvedIds = self::resolveGhnLocationIds($provinceClean, $districtClean, $wardClean);
            if ($resolvedIds['district_id'] > 0) {
                $ghnFee = self::callGhnCalculateFee(
                    $resolvedIds['district_id'],
                    $resolvedIds['ward_code'],
                    $weight,
                    $service,
                    $subtotal
                );
                if ($ghnFee !== null) {
                    return [
                        'shipping_fee'    => $ghnFee,
                        'base_fee'        => $ghnFee,
                        'provider'        => 'ghn_live',
                        'provider_label'  => 'GHN API Trực tuyến',
                        'service_type'    => $service,
                        'service_label'   => $serviceLabel,
                        'weight_gram'     => $weight,
                        'eta_text'        => ($service === 'express') ? 'Dự kiến 1 - 2 ngày' : 'Dự kiến 2 - 4 ngày',
                        'zone_label'      => self::detectVietnamZone($provinceClean)['label'],
                        'ghn_district_id' => $resolvedIds['district_id'],
                        'ghn_ward_code'   => $resolvedIds['ward_code'],
                    ];
                }
            }
        }

        // Bảng cước chuẩn theo vùng xuất kho TP.HCM + khối lượng thực tế (dự phòng khi chưa cấu hình Token GHN)
        $zone = self::detectVietnamZone($provinceClean);
        $extraUnits = ($weight > 500) ? (int)ceil(($weight - 500) / 500) : 0;
        $weightSurcharge = $extraUnits * 4000;
        $expressSurcharge = ($service === 'express') ? 18000 : 0;
        $fee = (float)($zone['base_fee'] + $weightSurcharge + $expressSurcharge);

        return [
            'shipping_fee'    => $fee,
            'base_fee'        => $fee,
            'provider'        => 'ghn_matrix',
            'provider_label'  => 'Biểu phí chuẩn GHN (Vùng & Khối lượng)',
            'service_type'    => $service,
            'service_label'   => $serviceLabel,
            'weight_gram'     => $weight,
            'eta_text'        => ($service === 'express') ? $zone['eta_express'] : $zone['eta_standard'],
            'zone_label'      => $zone['label'],
            'ghn_district_id' => $resolvedIds['district_id'],
            'ghn_ward_code'   => $resolvedIds['ward_code'],
        ];
    }

    /**
     * Đẩy đơn hàng sang đơn vị vận chuyển GHN để sinh mã vận đơn
     */
    public static function createCarrierOrder(array $order): array {
        $oid      = (int)($order['ma_don_hang'] ?? 0);
        $province = (string)($order['tinh_thanh_pho'] ?? '');
        $district = (string)($order['quan_huyen'] ?? '');
        $ward     = (string)($order['phuong_xa'] ?? '');
        $address  = (string)($order['dia_chi_giao_hang'] ?? '');
        $weight   = max(200, (int)($order['trong_luong_gram'] ?? 300));
        $service  = (($order['dich_vu_van_chuyen'] ?? 'standard') === 'express') ? 'express' : 'standard';

        $isPaid   = (strtolower((string)($order['trang_thai_thanh_toan'] ?? '')) === 'da_thanh_toan');
        $codAmt   = $isPaid ? 0 : (int)round((float)($order['tong_tien_thanh_toan'] ?? 0));

        if (self::isGhnConfigured() && $province !== '' && $district !== '') {
            $loc = self::resolveGhnLocationIds($province, $district, $ward);
            if ($loc['district_id'] > 0 && $loc['ward_code'] !== '') {
                $itemsPayload = [];
                foreach (($order['items'] ?? []) as $it) {
                    $itemsPayload[] = [
                        'name'     => mb_substr((string)($it['ten_san_pham'] ?? 'Sản phẩm FashionKids'), 0, 120),
                        'quantity' => max(1, (int)($it['so_luong'] ?? 1)),
                        'price'    => (int)round((float)($it['don_gia_mua'] ?? 0)),
                        'weight'   => 250,
                    ];
                }
                if (empty($itemsPayload)) {
                    $itemsPayload[] = ['name' => 'Đơn hàng FashionKids #' . $oid, 'quantity' => 1, 'price' => $codAmt, 'weight' => $weight];
                }

                $payload = [
                    'payment_type_id'    => 2, // Người nhận trả ship (đã cộng vào COD hoặc miễn phí qua voucher)
                    'note'               => (string)($order['ghi_chu'] ?: 'Cho xem hàng không thử - Thời trang trẻ em FashionKids'),
                    'required_note'      => 'CHOXEMHANGKHONGTHU',
                    'to_name'            => (string)($order['ten_nguoi_nhan'] ?? 'Khách hàng'),
                    'to_phone'           => preg_replace('/\D+/', '', (string)($order['so_dien_thoai_nhan'] ?? '')),
                    'to_address'         => trim($address . ($ward !== '' ? ', ' . $ward : '') . ', ' . $district . ', ' . $province),
                    'to_ward_code'       => (string)$loc['ward_code'],
                    'to_district_id'     => (int)$loc['district_id'],
                    'cod_amount'         => $codAmt,
                    'content'            => 'Thời trang trẻ em sợi tre FashionKids #' . $oid,
                    'weight'             => $weight,
                    'length'             => 25,
                    'width'              => 20,
                    'height'             => 8,
                    'service_type_id'    => ($service === 'express') ? 2 : 2,
                    'client_order_code'  => 'FK-ORD-' . str_pad((string)$oid, 6, '0', STR_PAD_LEFT),
                    'items'              => $itemsPayload,
                ];

                $res = self::ghnRequest('/v2/shipping-order/create', $payload, true);
                if ($res !== null && !empty($res['data']['order_code'])) {
                    return [
                        'success'       => true,
                        'mode'          => 'ghn_live',
                        'carrier_name'  => 'GHN Express',
                        'tracking_code' => (string)$res['data']['order_code'],
                        'expected_time' => (string)($res['data']['expected_delivery_time'] ?? ''),
                        'message'       => 'Đã tạo vận đơn thật trên hệ thống GHN: ' . $res['data']['order_code'],
                    ];
                }
            }
        }

        // Dự phòng chuẩn hóa khi chưa điền GHN_TOKEN / GHN_SHOP_ID
        $simCode = 'GHNFK' . str_pad((string)$oid, 5, '0', STR_PAD_LEFT) . strtoupper(substr(md5($oid . '|' . microtime(true)), 0, 4));
        return [
            'success'       => true,
            'mode'          => 'ghn_sandbox_fallback',
            'carrier_name'  => 'GHN (Demo Sandbox)',
            'tracking_code' => $simCode,
            'expected_time' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'message'       => 'Đã khởi tạo mã vận đơn GHN nội bộ (' . $simCode . '). Nhập GHN_TOKEN & GHN_SHOP_ID vào tệp .env để đẩy thẳng lên cổng GHN thật.',
        ];
    }

    /**
     * Ánh xạ trạng thái từ Webhook đối tác vận chuyển -> trạng thái đơn hàng FashionKids
     */
    public static function mapCarrierStatusToOrderStatus(string $carrierStatus): ?string {
        $s = strtolower(trim($carrierStatus));
        if ($s === '') {
            return null;
        }
        $shippingStatuses = [
            'ready_to_pick', 'picking', 'picked', 'storing', 'transporting',
            'sorting', 'delivering', 'money_collect_delivering', 'dang_giao', 'in_transit'
        ];
        if (in_array($s, $shippingStatuses, true)) {
            return 'dang_giao';
        }
        if (in_array($s, ['delivered', 'completed', 'hoan_tat'], true)) {
            return 'hoan_tat';
        }
        if (in_array($s, ['cancel', 'cancelled', 'returned', 'return', 'lost', 'damage', 'da_huy'], true)) {
            return 'da_huy';
        }
        return null;
    }

    private static function detectVietnamZone(string $province): array {
        $norm = self::normalizeVi($province);
        if ($norm === '') {
            return ['label' => 'Toàn quốc', 'base_fee' => 30000, 'eta_standard' => '2 - 4 ngày', 'eta_express' => '1 - 2 ngày'];
        }
        if (str_contains($norm, 'ho chi minh') || $norm === 'tphcm' || $norm === 'tp hcm') {
            return ['label' => 'Nội vùng TP.HCM', 'base_fee' => 20000, 'eta_standard' => '1 - 2 ngày', 'eta_express' => 'Trong 24h'];
        }
        $southTokens = ['binh duong', 'dong nai', 'ba ria', 'vung tau', 'tay ninh', 'long an', 'tien giang', 'ben tre', 'can tho', 'an giang', 'kien giang', 'vinh long', 'dong thap', 'tra vinh', 'soc trang', 'bac lieu', 'ca mau', 'hau giang', 'binh phuoc'];
        foreach ($southTokens as $t) {
            if (str_contains($norm, $t)) {
                return ['label' => 'Liên tỉnh Miền Nam', 'base_fee' => 28000, 'eta_standard' => '2 - 3 ngày', 'eta_express' => '1 - 2 ngày'];
            }
        }
        $centralTokens = ['da nang', 'hue', 'quang nam', 'quang ngai', 'binh dinh', 'phu yen', 'khanh hoa', 'ninh thuan', 'binh thuan', 'dak lak', 'gia lai', 'lam dong', 'dak nong', 'kon tum', 'quang tri', 'quang binh', 'ha tinh', 'nghe an', 'thanh hoa'];
        foreach ($centralTokens as $t) {
            if (str_contains($norm, $t)) {
                return ['label' => 'Miền Trung & Tây Nguyên', 'base_fee' => 33000, 'eta_standard' => '3 - 4 ngày', 'eta_express' => '1 - 2 ngày'];
            }
        }
        return ['label' => 'Miền Bắc / Hà Nội', 'base_fee' => 37000, 'eta_standard' => '3 - 5 ngày', 'eta_express' => '2 ngày'];
    }

    private static function callGhnCalculateFee(int $toDistrictId, string $toWardCode, int $weight, string $serviceType, float $subtotal): ?float {
        $payload = [
            'from_district_id' => defined('GHN_FROM_DISTRICT_ID') ? (int)GHN_FROM_DISTRICT_ID : 1442,
            'from_ward_code'   => defined('GHN_FROM_WARD_CODE') ? (string)GHN_FROM_WARD_CODE : '20101',
            'service_type_id'  => 2, // E-commerce standard light parcel
            'to_district_id'   => $toDistrictId,
            'to_ward_code'     => $toWardCode !== '' ? $toWardCode : null,
            'height'           => 8,
            'length'           => 25,
            'weight'           => $weight,
            'width'            => 20,
            'insurance_value'  => min(5000000, max(0, (int)round($subtotal))),
        ];
        $res = self::ghnRequest('/v2/shipping-order/fee', $payload, true);
        if ($res !== null && isset($res['data']['total'])) {
            $base = (float)$res['data']['total'];
            if ($serviceType === 'express') {
                $base += 15000.0;
            }
            return max(0.0, round($base, 0));
        }
        return null;
    }

    private static function resolveGhnLocationIds(string $provinceName, string $districtName, string $wardName = ''): array {
        $result = ['district_id' => 0, 'ward_code' => ''];
        $cacheDir = rtrim(sys_get_temp_dir(), '/\\');

        // 1. Lấy danh sách Tỉnh GHN (cache 7 ngày)
        $provFile = $cacheDir . '/fk_ghn_provinces.json';
        $provinces = self::readCacheJson($provFile, 86400 * 7);
        if ($provinces === null) {
            $res = self::ghnRequest('/master-data/province', [], false, 'GET');
            if ($res !== null && !empty($res['data']) && is_array($res['data'])) {
                $provinces = $res['data'];
                @file_put_contents($provFile, json_encode($provinces));
            }
        }
        if (!is_array($provinces)) {
            return $result;
        }

        $targetProv = self::normalizeVi($provinceName);
        $provId = 0;
        foreach ($provinces as $p) {
            $candidates = array_merge([$p['ProvinceName'] ?? ''], (array)($p['NameExtension'] ?? []));
            foreach ($candidates as $cand) {
                $nc = self::normalizeVi((string)$cand);
                if ($nc !== '' && ($nc === $targetProv || str_contains($targetProv, $nc) || str_contains($nc, $targetProv))) {
                    $provId = (int)($p['ProvinceID'] ?? 0);
                    break 2;
                }
            }
        }
        if ($provId <= 0) {
            return $result;
        }

        // 2. Lấy Quận/Huyện GHN (cache 7 ngày theo province)
        $distFile = $cacheDir . '/fk_ghn_districts_' . $provId . '.json';
        $districts = self::readCacheJson($distFile, 86400 * 7);
        if ($districts === null) {
            $res = self::ghnRequest('/master-data/district', ['province_id' => $provId], false, 'POST');
            if ($res !== null && !empty($res['data']) && is_array($res['data'])) {
                $districts = $res['data'];
                @file_put_contents($distFile, json_encode($districts));
            }
        }
        if (!is_array($districts)) {
            return $result;
        }

        $targetDist = self::normalizeVi($districtName);
        foreach ($districts as $d) {
            $candidates = array_merge([$d['DistrictName'] ?? ''], (array)($d['NameExtension'] ?? []));
            foreach ($candidates as $cand) {
                $nc = self::normalizeVi((string)$cand);
                if ($nc !== '' && ($nc === $targetDist || str_contains($targetDist, $nc) || str_contains($nc, $targetDist))) {
                    $result['district_id'] = (int)($d['DistrictID'] ?? 0);
                    break 2;
                }
            }
        }

        // 3. Lấy Phường/Xã GHN nếu đã match Quận/Huyện
        if ($result['district_id'] > 0 && $wardName !== '') {
            $wardFile = $cacheDir . '/fk_ghn_wards_' . $result['district_id'] . '.json';
            $wards = self::readCacheJson($wardFile, 86400 * 7);
            if ($wards === null) {
                $res = self::ghnRequest('/master-data/ward', ['district_id' => $result['district_id']], false, 'POST');
                if ($res !== null && !empty($res['data']) && is_array($res['data'])) {
                    $wards = $res['data'];
                    @file_put_contents($wardFile, json_encode($wards));
                }
            }
            if (is_array($wards)) {
                $targetWard = self::normalizeVi($wardName);
                foreach ($wards as $w) {
                    $candidates = array_merge([$w['WardName'] ?? ''], (array)($w['NameExtension'] ?? []));
                    foreach ($candidates as $cand) {
                        $nc = self::normalizeVi((string)$cand);
                        if ($nc !== '' && ($nc === $targetWard || str_contains($targetWard, $nc) || str_contains($nc, $targetWard))) {
                            $result['ward_code'] = (string)($w['WardCode'] ?? '');
                            break 2;
                        }
                    }
                }
            }
        }

        return $result;
    }

    private static function ghnRequest(string $path, array $payload, bool $includeShopId, string $method = 'POST'): ?array {
        if (!function_exists('curl_init') || !self::isGhnConfigured()) {
            return null;
        }
        $url = rtrim((string)GHN_API_BASE, '/') . $path;
        $headers = [
            'Content-Type: application/json',
            'Token: ' . trim((string)GHN_TOKEN),
        ];
        if ($includeShopId) {
            $headers[] = 'ShopId: ' . trim((string)GHN_SHOP_ID);
        }

        $ch = curl_init($url);
        $opts = [
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
        if (strtoupper($method) === 'POST') {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
        }
        curl_setopt_array($ch, $opts);
        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code < 200 || $code >= 300) {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || (isset($decoded['code']) && (int)$decoded['code'] !== 200)) {
            return null;
        }
        return $decoded;
    }

    private static function readCacheJson(string $file, int $ttl): ?array {
        if (is_file($file) && (time() - (int)@filemtime($file)) < $ttl) {
            $decoded = json_decode((string)@file_get_contents($file), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return null;
    }

    private static function normalizeVi(string $text): string {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $map = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
        foreach ($map as $ascii => $pattern) {
            $text = preg_replace('/(' . $pattern . ')/u', $ascii, $text);
        }
        $text = preg_replace('/^(thanh pho |tp\.?\s*|tinh |quan |huyen |thi xa |phuong |xa |thi tran )/u', '', $text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
