<?php
/**
 * Dịch vụ Trợ lý AI Tư vấn Size & Chất liệu Sợi Tre (core/GeminiService.php)
 * Sử dụng Google Gemini API kết hợp dữ liệu thực từ lnkt_bang_kich_co & lbn_san_pham
 * Tự động fallback sang AI Rule-based nội bộ khi chưa cấu hình GEMINI_API_KEY
 */

require_once __DIR__ . '/../config/integrations.php';

class GeminiService {
    /**
     * Bảng quy chuẩn Size Quần Áo FashionKids (kết hợp cả Chiều cao cm & Cân nặng kg)
     */
    private const CLOTH_SIZE_BANDS = [
        1 => ['label' => 'Size 1 (1-2T)', 'h_min' => 75,  'h_max' => 89,  'w_min' => 9.0,  'w_max' => 12.0],
        2 => ['label' => 'Size 2 (2-3T)', 'h_min' => 90,  'h_max' => 100, 'w_min' => 12.1, 'w_max' => 15.0],
        3 => ['label' => 'Size 3 (3-4T)', 'h_min' => 101, 'h_max' => 110, 'w_min' => 15.1, 'w_max' => 18.0],
        4 => ['label' => 'Size 4 (4-5T)', 'h_min' => 111, 'h_max' => 120, 'w_min' => 18.1, 'w_max' => 22.0],
        5 => ['label' => 'Size 5 (5-6T)', 'h_min' => 121, 'h_max' => 132, 'w_min' => 22.1, 'w_max' => 26.0],
    ];

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
                    'text'   => self::cleanChatText($aiText),
                ];
            }
        }

        return [
            'ok'     => true,
            'source' => 'local_ai',
            'text'   => self::cleanChatText(self::buildSmartFallbackReply($userMessage, $history, $sizeCharts, $products)),
        ];
    }

    /**
     * Làm sạch ký tự Markdown (** / * / # / `) vì khung chat hiển thị text thuần
     */
    private static function cleanChatText(string $text): string {
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));
        $text = preg_replace('/\*\*([^\*\n]+)\*\*/u', '$1', $text);
        $text = preg_replace('/__([^_\n]+)__/u', '$1', $text);
        $text = preg_replace('/`([^`\n]+)`/u', '$1', $text);
        $text = preg_replace('/^\s*\*\s+/mu', '- ', $text);
        $text = preg_replace('/^\s*#{1,6}\s+/mu', '', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim((string)$text);
    }

    /**
     * Chuẩn hóa tin nhắn từ lịch sử (hỗ trợ cả key từ DB thô và key từ getChatMessages())
     */
    private static function extractHistoryEntry(array $h): ?array {
        $txt = trim((string)($h['content'] ?? $h['noi_dung_tin_nhan'] ?? ''));
        if ($txt === '') {
            return null;
        }
        $sender = (string)($h['sender'] ?? $h['nguoi_gui'] ?? '');
        $role = ($sender === 'khach_hang') ? 'user' : 'model';
        return ['role' => $role, 'text' => $txt];
    }

    /**
     * Trích xuất chiều cao (cm), cân nặng (kg), dài bàn chân (cm) từ tin nhắn mới và hội thoại gần đây
     */
    private static function extractMeasurements(string $userMessage, array $history): array {
        $texts = [$userMessage];
        $recent = array_slice($history, -8);
        for ($i = count($recent) - 1; $i >= 0; $i--) {
            $entry = self::extractHistoryEntry($recent[$i]);
            if ($entry && $entry['role'] === 'user' && $entry['text'] !== trim($userMessage)) {
                $texts[] = $entry['text'];
            }
        }

        $height = null;
        $weight = null;
        $foot   = null;

        foreach ($texts as $idx => $raw) {
            // Chỉ lấy từ lịch sử khi tin mới chưa có đủ số đo, không dùng số đo cũ nếu tin mới đã nhập bộ số mới
            if ($idx > 0 && $height !== null && $weight !== null) {
                break;
            }
            $lower = mb_strtolower($raw, 'UTF-8');
            $asksFoot = (str_contains($lower, 'chân') || str_contains($lower, 'giày') || str_contains($lower, 'dép') || str_contains($lower, 'sandal'));

            // Định dạng mét: 1m05, 1m1, 1.15m
            if ($height === null && preg_match('/\b1\s*m\s*([0-9]{1,2})\b/iu', $lower, $m)) {
                $sub = $m[1];
                $cm = (strlen($sub) === 1) ? (100 + ((int)$sub * 10)) : (100 + (int)$sub);
                if ($cm >= 95 && $cm <= 165) {
                    $height = (float)$cm;
                }
            }

            // Số đo có đơn vị cm / xăng / phân
            if (preg_match_all('/(\d+(?:[\.,]\d+)?)\s*(cm|xăng|phan|phân)/iu', $lower, $matches)) {
                foreach ($matches[1] as $numStr) {
                    $val = (float)str_replace(',', '.', $numStr);
                    if ($val >= 45 && $val <= 165 && $height === null && !$asksFoot) {
                        $height = $val;
                    } elseif ($val >= 9 && $val <= 26 && $foot === null) {
                        $foot = $val;
                    } elseif ($val >= 45 && $val <= 165 && $height === null) {
                        $height = $val;
                    }
                }
            }

            // Số đo chiều cao dạng "cao 95" (không ghi cm)
            if ($height === null && preg_match('/(?:cao|chiều\s*cao)\s*[:=]?\s*(\d+(?:[\.,]\d+)?)\b/iu', $lower, $m)) {
                $val = (float)str_replace(',', '.', $m[1]);
                if ($val >= 45 && $val <= 165) {
                    $height = $val;
                }
            }

            // Số đo cân nặng có đơn vị kg / ký / cân
            if ($weight === null && preg_match('/(\d+(?:[\.,]\d+)?)\s*(kg|ky|ký|ki|kí|cân)/iu', $lower, $m)) {
                $val = (float)str_replace(',', '.', $m[1]);
                if ($val >= 3 && $val <= 55) {
                    $weight = $val;
                }
            }

            // Số đo cân nặng dạng "nặng 15" (không ghi kg)
            if ($weight === null && preg_match('/(?:nặng|cân\s*nặng)\s*[:=]?\s*(\d+(?:[\.,]\d+)?)\b/iu', $lower, $m)) {
                $val = (float)str_replace(',', '.', $m[1]);
                if ($val >= 3 && $val <= 55) {
                    $weight = $val;
                }
            }
        }

        return [
            'height' => $height,
            'weight' => $weight,
            'foot'   => $foot,
        ];
    }

    /**
     * Tính size chuẩn xác theo cả chiều cao & cân nặng (luôn lấy size lớn hơn để bé mặc không bị chật)
     */
    private static function resolveClothRecommendation(?float $height, ?float $weight, array $sizeCharts): array {
        $hBand = null;
        if ($height !== null) {
            if ($height < 90)       $hBand = 1;
            elseif ($height <= 100) $hBand = 2;
            elseif ($height <= 110) $hBand = 3;
            elseif ($height <= 120) $hBand = 4;
            else                    $hBand = 5;
        }

        $wBand = null;
        if ($weight !== null) {
            if ($weight <= 12.0)      $wBand = 1;
            elseif ($weight <= 15.0)  $wBand = 2;
            elseif ($weight <= 18.0)  $wBand = 3;
            elseif ($weight <= 22.0)  $wBand = 4;
            else                      $wBand = 5;
        }

        $finalBand = 2;
        $reason = 'theo bảng size chuẩn FashionKids';
        if ($hBand !== null && $wBand !== null) {
            if ($wBand > $hBand) {
                $finalBand = $wBand;
                $reason = "ưu tiên theo cân nặng {$weight}kg để bé mặc rộng rãi, không chật bụng";
            } elseif ($hBand > $wBand) {
                $finalBand = $hBand;
                $reason = "ưu tiên theo chiều cao {$height}cm để đủ chiều dài váy/áo";
            } else {
                $finalBand = $hBand;
                $reason = "khớp chuẩn cả chiều cao {$height}cm và cân nặng {$weight}kg";
            }
        } elseif ($hBand !== null) {
            $finalBand = $hBand;
            $reason = "theo chiều cao {$height}cm";
        } elseif ($wBand !== null) {
            $finalBand = $wBand;
            $reason = "theo cân nặng {$weight}kg";
        }

        // Bám sát tên size trong DB nếu DB có định nghĩa khớp band (1..4)
        $label = self::CLOTH_SIZE_BANDS[$finalBand]['label'] ?? 'Size 2 (2-3T)';
        if (!empty($sizeCharts)) {
            $prefix = 'Size ' . $finalBand;
            foreach ($sizeCharts as $row) {
                if (($row['loai_hang'] ?? '') === 'giay_dep') continue;
                $name = (string)($row['ten_kich_co'] ?? '');
                if (stripos($name, $prefix) === 0) {
                    $label = $name;
                    break;
                }
            }
        }

        $bandInfo = self::CLOTH_SIZE_BANDS[$finalBand];
        $edgeTip = '';
        if (($height !== null && $height >= $bandInfo['h_max'] - 1) || ($weight !== null && $weight >= $bandInfo['w_max'] - 0.5)) {
            if ($finalBand < 5) {
                $nextLabel = self::CLOTH_SIZE_BANDS[$finalBand + 1]['label'];
                $edgeTip = "Nếu muốn bé mặc thoải mái lâu dài hơn, Ba Mẹ có thể cân nhắc nhích lên {$nextLabel}.";
            }
        }

        return [
            'band'   => $finalBand,
            'label'  => $label,
            'reason' => $reason,
            'tip'    => $edgeTip,
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
        $meas         = self::extractMeasurements($userMessage, $history);

        $precomputedNote = '';
        if ($meas['foot'] !== null) {
            $shoeSize = self::guessShoeSize($meas['foot'], $sizeCharts);
            $precomputedNote = "KẾT QUẢ ĐỐI CHIẾU SỐ ĐO HIỆN TẠI CỦA KHÁCH: Dài chân {$meas['foot']}cm => Chọn chính xác {$shoeSize}.";
        } elseif ($meas['height'] !== null || $meas['weight'] !== null) {
            $rec = self::resolveClothRecommendation($meas['height'], $meas['weight'], $sizeCharts);
            $parts = [];
            if ($meas['height'] !== null) $parts[] = "cao {$meas['height']}cm";
            if ($meas['weight'] !== null) $parts[] = "nặng {$meas['weight']}kg";
            $precomputedNote = "KẾT QUẢ ĐỐI CHIẾU SỐ ĐO HIỆN TẠI CỦA KHÁCH (" . implode(', ', $parts) . "): "
                . "BẮT BUỘC khuyên dùng {$rec['label']} ({$rec['reason']}). " . $rec['tip'];
        }

        $systemLines = [
            'Bạn là Trợ lý CSKH của shop thời trang trẻ em FashionKids (đồ sợi tre Organic Bamboo cho bé 0-6 tuổi).',
            'QUY TẮC BẮT BUỘC:',
            '1. TRẢ LỜI NGẮN GỌN, ĐÚNG TRỌNG TÂM: Tối đa 2-3 câu ngắn (khoảng 25-55 từ). Đi thẳng vào câu hỏi của khách.',
            '2. KHÔNG DÙNG MARKDOWN: Tuyệt đối không viết **, *, #, hay ` trong câu trả lời vì khung chat hiển thị văn bản thuần.',
            '3. KHÔNG QUẢNG CÁO THỪA: Nếu khách chỉ chào hoặc chỉ hỏi size, KHÔNG tự ý liệt kê nhiều sản phẩm hay mã giảm giá; chỉ nhắc sản phẩm/mã giảm giá khi khách hỏi.',
            '4. TƯ VẤN SIZE CHÍNH XÁC: Luôn dùng cả chiều cao (cm) và cân nặng (kg) theo bảng size bên dưới; nếu 2 chỉ số lệch nhau, luôn lấy size lớn hơn để bé mặc không bị chật. Nếu có mục KẾT QUẢ ĐỐI CHIẾU SỐ ĐO, phải trả lời đúng theo size đã tính đó.',
            '5. Xưng "Em", gọi khách là "Ba Mẹ", giọng tự nhiên, thân thiện.',
            '',
            "BẢNG SIZE CHUẨN FASHIONKIDS (CHIỀU CAO & CÂN NẶNG):\n" . $chartSummary,
            '',
            "DANH MỤC SẢN PHẨM ĐANG BÁN (chỉ trích dẫn khi khách hỏi gợi ý/giá):\n" . $prodSummary,
            'MÃ GIẢM GIÁ (chỉ nhắc khi khách hỏi ưu đãi/thanh toán): FASHION10 (-10% đơn từ 200k), SOITRE30K (-30k đơn từ 250k), FREESHIPFK (hỗ trợ ship tới 35k đơn từ 150k).',
        ];
        if ($precomputedNote !== '') {
            $systemLines[] = '';
            $systemLines[] = $precomputedNote;
        }
        $systemPrompt = implode("\n", $systemLines);

        // Chuẩn hóa lịch sử hội thoại, loại bỏ tin mới bị trùng từ DB và ghép lượt cùng role liên tiếp
        $contents = [];
        $tailHistory = array_slice($history, -8);
        $trimmedUserMsg = trim($userMessage);
        if (!empty($tailHistory)) {
            $lastParsed = self::extractHistoryEntry($tailHistory[count($tailHistory) - 1]);
            if ($lastParsed && $lastParsed['role'] === 'user' && $lastParsed['text'] === $trimmedUserMsg) {
                array_pop($tailHistory);
            }
        }
        foreach ($tailHistory as $h) {
            $entry = self::extractHistoryEntry($h);
            if (!$entry) {
                continue;
            }
            $lastIdx = count($contents) - 1;
            if ($lastIdx >= 0 && $contents[$lastIdx]['role'] === $entry['role']) {
                $contents[$lastIdx]['parts'][0]['text'] .= "\n" . $entry['text'];
            } else {
                $contents[] = ['role' => $entry['role'], 'parts' => [['text' => $entry['text']]]];
            }
        }
        $lastIdx = count($contents) - 1;
        if ($lastIdx >= 0 && $contents[$lastIdx]['role'] === 'user') {
            $contents[$lastIdx]['parts'][0]['text'] .= "\n" . $trimmedUserMsg;
        } else {
            $contents[] = ['role' => 'user', 'parts' => [['text' => $trimmedUserMsg]]];
        }

        foreach ($candidateModels as $model) {
            $genConfig = [
                'temperature'     => 0.25,
                'maxOutputTokens' => 280,
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

    private static function buildSmartFallbackReply(string $msg, array $history, array $sizeCharts, array $products): string {
        $lower = mb_strtolower($msg, 'UTF-8');
        $meas = self::extractMeasurements($msg, $history);
        $height = $meas['height'];
        $weight = $meas['weight'];
        $foot   = $meas['foot'];

        if ($foot !== null && (str_contains($lower, 'chân') || str_contains($lower, 'giày') || str_contains($lower, 'dép') || str_contains($lower, 'size') || $height === null)) {
            $shoeSize = self::guessShoeSize($foot, $sizeCharts);
            return "Dạ với chiều dài bàn chân {$foot}cm, Ba Mẹ chọn {$shoeSize} để bé đi êm ái và thoáng mũi chân ạ.";
        }

        if ($height !== null || $weight !== null) {
            $rec = self::resolveClothRecommendation($height, $weight, $sizeCharts);
            $measureText = [];
            if ($height !== null) $measureText[] = "cao {$height}cm";
            if ($weight !== null) $measureText[] = "nặng {$weight}kg";
            $reply = "Dạ với bé " . implode(', ', $measureText) . ", Ba Mẹ chọn {$rec['label']} ({$rec['reason']}) ạ.";
            if ($rec['tip'] !== '') {
                $reply .= ' ' . $rec['tip'];
            }
            return $reply;
        }

        if (str_contains($lower, 'size') || str_contains($lower, 'kích cỡ')) {
            return "Dạ Ba Mẹ cho em xin chiều cao (cm) và cân nặng (kg) của bé để em chọn chuẩn size ngay ạ.";
        }

        if (str_contains($lower, 'sợi tre') || str_contains($lower, 'bamboo') || str_contains($lower, 'vải') || str_contains($lower, 'chất')) {
            return "Dạ vải 100% Sợi Tre Organic tại FashionKids mềm mịn, thoáng khí, kháng khuẩn 75% và an toàn cho làn da nhạy cảm của bé 0-6 tuổi ạ.";
        }

        if (str_contains($lower, 'kol') || str_contains($lower, 'casting') || str_contains($lower, 'siêu sao')) {
            return "Dạ Ba Mẹ đăng nhập và vào mục Tuyển KOL Nhí trên menu để nộp 2-5 ảnh của bé ứng tuyển Đại sứ nhí 2026 ạ.";
        }

        if (str_contains($lower, 'mã giảm') || str_contains($lower, 'voucher') || str_contains($lower, 'ship') || str_contains($lower, 'vận chuyển') || str_contains($lower, 'giảm giá')) {
            return "Dạ tại trang Thanh toán Ba Mẹ có thể dùng mã FASHION10 (-10% đơn từ 200k), SOITRE30K (-30k đơn từ 250k) hoặc FREESHIPFK (giảm ship tới 35k) ạ.";
        }

        if (str_contains($lower, 'mẫu') || str_contains($lower, 'váy') || str_contains($lower, 'bộ') || str_contains($lower, 'áo') || str_contains($lower, 'giá')) {
            if (!empty($products)) {
                $picks = [];
                foreach (array_slice($products, 0, 2) as $p) {
                    $picks[] = ($p['ten_san_pham'] ?? 'Đồ sợi tre') . ' (' . number_format((float)($p['gia_ban'] ?? 250000), 0, ',', '.') . 'đ)';
                }
                return "Dạ shop đang sẵn các mẫu nổi bật: " . implode(', ', $picks) . ". Ba Mẹ nhắn thêm chiều cao, cân nặng để em chốt size cho bé nhé!";
            }
        }

        return "Dạ em chào Ba Mẹ ạ! Ba Mẹ cần tư vấn mẫu nào hoặc cho em xin chiều cao và cân nặng của bé để em chọn size chuẩn nhé.";
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
        $lines = [
            '- Quần áo Size 1 (1-2T): cao 75-89cm, nặng 9-12kg',
            '- Quần áo Size 2 (2-3T): cao 90-100cm, nặng 12.1-15kg',
            '- Quần áo Size 3 (3-4T): cao 101-110cm, nặng 15.1-18kg',
            '- Quần áo Size 4 (4-5T): cao 111-120cm, nặng 18.1-22kg',
            '- Quần áo Size 5 (5-6T): cao 121-132cm, nặng 22.1-26kg',
        ];
        $shoeLines = [];
        foreach ($sizeCharts as $r) {
            if (($r['loai_hang'] ?? '') === 'giay_dep') {
                $sizeName = (string)($r['ten_kich_co'] ?? 'N/A');
                $minFoot  = $r['chieu_dai_chan_toi_thieu'] ?? '?';
                $maxFoot  = $r['chieu_dai_chan_toi_da'] ?? '?';
                $shoeLines[] = "- Giày {$sizeName}: dài chân {$minFoot}-{$maxFoot}cm";
            }
        }
        if (empty($shoeLines)) {
            $shoeLines = [
                '- Giày Size 24: dài chân 14.0-14.9cm',
                '- Giày Size 26: dài chân 15.0-15.9cm',
                '- Giày Size 28: dài chân 16.0-16.9cm',
            ];
        }
        return implode("\n", array_merge($lines, $shoeLines));
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
            $lines[] = "- {$name}: {$price}đ (Còn {$stock} sp)";
        }
        return implode("\n", $lines);
    }
}
