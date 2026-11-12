<?php
/**
 * Dịch vụ Trợ lý AI Tư vấn Size & Chất liệu Sợi Tre (core/GeminiService.php)
 * Sử dụng Google Gemini API kết hợp dữ liệu thực từ lnkt_bang_kich_co & lbn_san_pham
 * Tự động fallback sang AI Rule-based nội bộ khi chưa cấu hình GEMINI_API_KEY
 */

require_once __DIR__ . '/../config/integrations.php';

class GeminiService {
    public static function isConfigured(): bool {
        return defined('GEMINI_API_KEY') && trim((string)GEMINI_API_KEY) !== '';
    }

    public static function replyToCustomer(
        string $userMessage,
        array $history = [],
        array $sizeCharts = [],
        array $products = []
    ): array {
        if (self::isConfigured() && function_exists('curl_init')) {
            $aiText = self::callGeminiApi($userMessage, $history, $sizeCharts, $products);
            if ($aiText !== null && trim($aiText) !== '') {
                return [
                    'ok'     => true,
                    'source' => 'gemini',
                    'text'   => trim($aiText),
                ];
            }
        }

        return [
            'ok'     => true,
            'source' => 'local_ai',
            'text'   => self::buildSmartFallbackReply($userMessage, $sizeCharts, $products),
        ];
    }

    private static function callGeminiApi(
        string $userMessage,
        array $history,
        array $sizeCharts,
        array $products
    ): ?string {
        $primaryModel = (defined('GEMINI_MODEL') && trim((string)GEMINI_MODEL) !== '') ? trim((string)GEMINI_MODEL) : 'gemini-3-flash-preview';
        $apiKey       = trim((string)GEMINI_API_KEY);
        $candidateModels = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-3-flash-preview',
            'gemini-3.5-flash-lite',
            'gemini-2.5-pro',
        ])));

        $chartSummary = self::formatSizeChartsForPrompt($sizeCharts);
        $prodSummary  = self::formatProductsForPrompt($products);

        $systemPrompt = implode("\n", [
            'Bạn là Trợ lý AI Chăm sóc Khách hàng thân thiện của thương hiệu thời trang trẻ em FashionKids (chuyên đồ sợi tre Organic Bamboo kháng khuẩn cho bé 0-6 tuổi).',
            'QUY TẮC TRẢ LỜI:',
            '- Trả lời bằng tiếng Việt ấm áp, xưng "Em" và gọi khách là "Ba Mẹ", súc tích (tối đa 4-6 câu hoặc gạch đầu dòng ngắn gọn).',
            '- CHỈ sử dụng số liệu size và giá sản phẩm có trong dữ liệu cửa hàng bên dưới; không tự bịa sản phẩm không có.',
            '- Khi Ba Mẹ nêu chiều cao (cm), cân nặng (kg) hoặc chiều dài chân (cm), hãy đối chiếu chính xác BẢNG SIZE chuẩn để gợi ý 1-2 size phù hợp nhất (nếu bé hơi mũm mĩm gợi ý nhích lên 1 size).',
            '- Mã giảm giá đang mở tại trang Thanh toán: FASHION10 (giảm 10% tối đa 50.000đ cho đơn từ 200.000đ), SOITRE30K (giảm 30.000đ cho đơn từ 250.000đ), FREESHIPFK (hỗ trợ phí vận chuyển tối đa 35.000đ đơn từ 150.000đ).',
            '',
            "BẢNG SIZE CHUẨN FASHIONKIDS:\n" . $chartSummary,
            '',
            "MỘT SỐ SẢN PHẨM NỔI BẬT TRONG KHO:\n" . $prodSummary,
        ]);

        $contents = [];
        $tailHistory = array_slice($history, -6);
        foreach ($tailHistory as $h) {
            $txt = trim((string)($h['noi_dung_tin_nhan'] ?? ''));
            if ($txt === '') {
                continue;
            }
            $role = (($h['nguoi_gui'] ?? '') === 'khach_hang') ? 'user' : 'model';
            $contents[] = ['role' => $role, 'parts' => [['text' => $txt]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        foreach ($candidateModels as $model) {
            $genConfig = [
                'temperature'     => 0.45,
                'maxOutputTokens' => 1200,
            ];
            if (stripos($model, 'flash') !== false) {
                // Tắt thinking tokens cho model Flash để phản hồi chat nhanh và không bị cắt câu do hao hụt token
                $genConfig['thinkingConfig'] = ['thinkingBudget' => 0];
            }
            $payload = json_encode([
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents'         => $contents,
                'generationConfig' => $genConfig,
            ], JSON_UNESCAPED_UNICODE);

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . rawurlencode($apiKey);
            $ch  = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 6,
                CURLOPT_TIMEOUT        => 18,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $raw  = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($raw !== false && $code >= 200 && $code < 300) {
                $json  = json_decode((string)$raw, true);
                $parts = $json['candidates'][0]['content']['parts'] ?? [];
                $txt   = '';
                foreach ($parts as $part) {
                    if (!empty($part['thought'])) {
                        continue;
                    }
                    if (isset($part['text']) && trim((string)$part['text']) !== '') {
                        $txt .= ($txt === '' ? '' : "\n") . trim((string)$part['text']);
                    }
                }
                if ($txt !== '') {
                    return $txt;
                }
            }
            error_log('[GeminiService] Model ' . $model . ' HTTP ' . $code . ' response: ' . substr((string)$raw, 0, 200));
            // Chỉ thử fallback model khác nếu model hiện tại không khả dụng / timeout
            if ($code !== 404 && $code !== 0) {
                break;
            }
        }
        return null;
    }

    private static function buildSmartFallbackReply(string $msg, array $sizeCharts, array $products): string {
        $lower = mb_strtolower($msg, 'UTF-8');

        // 1. Trích xuất số đo chiều cao / cân nặng / dài chân nếu có
        $height = null;
        $weight = null;
        $foot   = null;
        if (preg_match('/(\d+(?:[\.,]\d+)?)\s*(cm|xăng|phan)/iu', $lower, $m)) {
            $val = (float)str_replace(',', '.', $m[1]);
            if ($val >= 50) {
                $height = $val;
            } elseif ($val >= 9 && $val <= 25) {
                $foot = $val;
            }
        }
        if (preg_match('/(\d+(?:[\.,]\d+)?)\s*(kg|ky|kí|cân)/iu', $lower, $m)) {
            $weight = (float)str_replace(',', '.', $m[1]);
        }

        if ($height !== null || $weight !== null || $foot !== null || str_contains($lower, 'size') || str_contains($lower, 'kích cỡ')) {
            if ($foot !== null) {
                $shoeSize = self::guessShoeSize($foot, $sizeCharts);
                return "🤖 [Trợ lý FashionKids] Dạ với chiều dài bàn chân bé khoảng {$foot}cm, Ba Mẹ chọn **{$shoeSize}** giúp bé đi êm ái và dư mũi thoáng chân ạ. Ba Mẹ có thể mở Thước Dây Gợi Ý Size ở trang chi tiết sản phẩm để đối chiếu thêm nha!";
            }
            $estHeight = $height;
            if ($estHeight === null && $weight !== null) {
                if ($weight <= 9)       $estHeight = 72;
                elseif ($weight <= 12)  $estHeight = 84;
                elseif ($weight <= 15)  $estHeight = 95;
                elseif ($weight <= 18)  $estHeight = 105;
                elseif ($weight <= 22)  $estHeight = 115;
                else                    $estHeight = 125;
            }
            $clothSize = self::guessClothSize($estHeight, $sizeCharts);
            $measureText = [];
            if ($height !== null) $measureText[] = "cao {$height}cm";
            if ($weight !== null) $measureText[] = "nặng {$weight}kg";
            $desc = !empty($measureText) ? ('với bé ' . implode(', ', $measureText)) : 'theo bảng size sợi tre chuẩn của shop';
            return "🤖 [Trợ lý FashionKids] Dạ {$desc}, em gợi ý Ba Mẹ chọn **{$clothSize}** ạ. Chất vải sợi tre co giãn 4 chiều rất mềm, nếu bé hơi bụ bẫm Ba Mẹ có thể nhích lên 1 size cho bé mặc thoải mái hơn nhé!";
        }

        if (str_contains($lower, 'sợi tre') || str_contains($lower, 'bamboo') || str_contains($lower, 'vải') || str_contains($lower, 'chất')) {
            return "🌿 [Trợ lý FashionKids] Vải **100% Sợi Tre Organic (Bamboo)** tại FashionKids nổi bật với khả năng kháng khuẩn tự nhiên 75%, thấm hút mồ hôi gấp 2 lần cotton và bề mặt siêu mịn không gây hằn đỏ làn da nhạy cảm của bé sơ sinh - 6 tuổi ạ.";
        }

        if (str_contains($lower, 'kol') || str_contains($lower, 'casting') || str_contains($lower, 'siêu sao')) {
            return "👑 [Trợ lý FashionKids] Chiến dịch **Tuyển KOL Nhí 2026** đang mở nhận hồ sơ trực tuyến! Ba Mẹ chỉ cần đăng nhập và vào mục **Tuyển KOL Nhí** trên thanh menu, tải từ 2-5 ảnh rõ mặt/toàn thân của bé để ứng tuyển (giải thưởng tài trợ đồ sợi tre 1 năm & nâng hạng VIP Kim Cương -15%).";
        }

        if (str_contains($lower, 'mã giảm') || str_contains($lower, 'voucher') || str_contains($lower, 'ship') || str_contains($lower, 'vận chuyển') || str_contains($lower, 'giảm giá')) {
            return "🏷️ [Trợ lý FashionKids] Tại trang Thanh toán, Ba Mẹ có thể nhập mã **FASHION10** (giảm 10% đơn từ 200k), **SOITRE30K** (giảm 30.000đ đơn từ 250k) hoặc **FREESHIPFK** (hỗ trợ phí ship GHN tới 35.000đ). Hệ thống cũng tự động áp dụng thêm ưu đãi VIP hiện có của Ba Mẹ ạ!";
        }

        if (!empty($products)) {
            $first = $products[0];
            $pName = (string)($first['ten_san_pham'] ?? 'Đầm công chúa sợi tre');
            $pPrice = number_format((float)($first['gia_ban'] ?? 250000), 0, ',', '.');
            return "🤖 [Trợ lý FashionKids] Dạ em đã tiếp nhận tin nhắn của Ba Mẹ ạ! Hiện shop đang có mẫu nổi bật **{$pName}** ({$pPrice}đ) làm từ sợi tre kháng khuẩn. Ba Mẹ có thể nhắn chiều cao + cân nặng của bé để em chọn đúng size ngay, hoặc đợi nhân viên CSKH hỗ trợ chi tiết hơn nhé!";
        }

        return "🤖 [Trợ lý FashionKids] Dạ em đã ghi nhận tin nhắn của Ba Mẹ! Ba Mẹ vui lòng cho em biết chiều cao (cm) & cân nặng (kg) của bé để em tư vấn size chuẩn sợi tre ngay trong 5 giây ạ.";
    }

    private static function guessClothSize(?float $height, array $sizeCharts): string {
        if ($height !== null && !empty($sizeCharts)) {
            foreach ($sizeCharts as $row) {
                if (($row['loai_hang'] ?? '') === 'giay_dep') continue;
                $min = (float)($row['chieu_cao_toi_thieu'] ?? 0);
                $max = (float)($row['chieu_cao_toi_da'] ?? 0);
                if ($max > 0 && $height >= $min && $height <= $max) {
                    return (string)($row['ten_kich_co'] ?? 'Size 2 (2-3T)');
                }
            }
        }
        $h = $height ?? 92;
        if ($h <= 75) return 'Size 1 (0-1T)';
        if ($h < 90)  return 'Size 1 (1-2T)';
        if ($h <= 100) return 'Size 2 (2-3T)';
        if ($h <= 110) return 'Size 3 (3-4T)';
        if ($h <= 122) return 'Size 4 (4-5T)';
        return 'Size 5 (5-6T)';
    }

    private static function guessShoeSize(float $footCm, array $sizeCharts): string {
        if (!empty($sizeCharts)) {
            foreach ($sizeCharts as $row) {
                if (($row['loai_hang'] ?? '') !== 'giay_dep') continue;
                $min = (float)($row['chieu_dai_chan_toi_thieu'] ?? 0);
                $max = (float)($row['chieu_dai_chan_toi_da'] ?? 0);
                if ($max > 0 && $footCm >= $min && $footCm <= $max) {
                    return (string)($row['ten_kich_co'] ?? 'Size 24');
                }
            }
        }
        if ($footCm <= 12.5) return 'Size 21 (12cm)';
        if ($footCm <= 13.5) return 'Size 22 (13cm)';
        if ($footCm <= 15.0) return 'Size 24';
        if ($footCm <= 16.0) return 'Size 26';
        return 'Size 28';
    }

    private static function formatSizeChartsForPrompt(array $sizeCharts): string {
        if (empty($sizeCharts)) {
            return "- Quần áo: Size 1 (70-89cm), Size 2 (90-100cm / 12-15kg), Size 3 (101-110cm / 15-18kg), Size 4 (111-122cm / 18-22kg), Size 5 (123-132cm / 22-26kg)\n- Giày dép: Size 21 (12-12.5cm), Size 22 (13-13.5cm), Size 24 (14-14.9cm), Size 26 (15-15.9cm), Size 28 (16-17cm)";
        }
        $lines = [];
        foreach (array_slice($sizeCharts, 0, 14) as $r) {
            $sizeName = (string)($r['ten_kich_co'] ?? 'N/A');
            if (($r['loai_hang'] ?? '') === 'giay_dep') {
                $minFoot = $r['chieu_dai_chan_toi_thieu'] ?? '?';
                $maxFoot = $r['chieu_dai_chan_toi_da'] ?? '?';
                $lines[] = "- Giày {$sizeName}: dài chân {$minFoot}-{$maxFoot}cm";
            } else {
                $minH = $r['chieu_cao_toi_thieu'] ?? $r['chieu_cao_tu'] ?? $r['chieu_cao_min'] ?? '?';
                $maxH = $r['chieu_cao_toi_da'] ?? $r['chieu_cao_den'] ?? $r['chieu_cao_max'] ?? '?';
                $minC = $r['vong_nguc_toi_thieu'] ?? '?';
                $maxC = $r['vong_nguc_toi_da'] ?? '?';
                $lines[] = "- Quần áo {$sizeName}: cao {$minH}-{$maxH}cm, ngực {$minC}-{$maxC}cm";
            }
        }
        return implode("\n", $lines);
    }

    private static function formatProductsForPrompt(array $products): string {
        if (empty($products)) {
            return '- Các mẫu váy, bộ thun sợi tre kháng khuẩn Organic cho bé trai và bé gái (giá 150.000đ - 450.000đ).';
        }
        $lines = [];
        foreach (array_slice($products, 0, 8) as $p) {
            $name  = (string)($p['ten_san_pham'] ?? '');
            $price = number_format((float)($p['gia_ban'] ?? 0), 0, ',', '.');
            $stock = (int)($p['so_luong_ton_kho'] ?? 0);
            $mat   = mb_substr(strip_tags((string)($p['mo_ta_chat_lieu'] ?? 'Sợi tre Organic')), 0, 80);
            $lines[] = "- {$name}: {$price}đ (Còn {$stock} sp) - {$mat}";
        }
        return implode("\n", $lines);
    }
}
