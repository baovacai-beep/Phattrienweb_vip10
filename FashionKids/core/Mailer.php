<?php
/**
 * Dịch vụ gửi thư điện tử qua Gmail SMTP (core/Mailer.php)
 * Phân hệ đảm nhiệm: Hồ Gia Bảo (hgb_)
 * Đóng gói logic gửi email OTP 6 chữ số chuẩn định dạng HTML Bright Pastel qua PHPMailer.
 * Hỗ trợ tự động dự phòng chuyển cổng từ 587 (STARTTLS) sang 465 (SMTPS/SSL) nếu bị chặn cổng.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private static string $lastError = '';

    public static function getLastError(): string {
        return self::$lastError;
    }

    /**
     * Gửi mã xác thực OTP 6 chữ số tới hộp thư Gmail của người dùng
     */
    public static function guiOtpEmail(string $toEmail, string $toName, string $otp): bool {
        $safeName = htmlspecialchars($toName !== '' ? $toName : 'Quý khách', ENT_QUOTES, 'UTF-8');
        $safeOtp  = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $subject  = "[FashionKids] Mã xác thực đăng ký tài khoản: {$otp}";
        $bodyHtml = "
            <div style='font-family: Arial, sans-serif; background-color: #FAF8F5; padding: 25px;'>
                <div style='max-width: 520px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; padding: 28px; border-top: 4px solid #FF8A7A; box-shadow: 0 6px 18px rgba(45,49,66,0.06);'>
                    <h2 style='color: #2D3142; text-align: center; margin-bottom: 10px;'>Kích Hoạt Tài Khoản FashionKids</h2>
                    <p style='color: #6B7280; font-size: 14px;'>Xin chào <strong>{$safeName}</strong>,</p>
                    <p style='color: #6B7280; font-size: 14px;'>Bạn vừa yêu cầu đăng ký tài khoản tại FashionKids. Dưới đây là mã bảo vệ OTP của bạn:</p>
                    <div style='text-align: center; margin: 25px 0;'>
                        <span style='display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #FF8A7A; background: #FFF2F0; padding: 12px 26px; border-radius: 12px; border: 1px solid #FFD6CF;'>{$safeOtp}</span>
                    </div>
                    <p style='color: #EF4444; font-size: 13px; text-align: center;'>* Mã OTP này có hiệu lực trong vòng <strong>5 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.</p>
                </div>
            </div>";

        return self::sendHtmlEmail($toEmail, $toName, $subject, $bodyHtml);
    }

    /**
     * Gửi Email Thư mời Đánh giá sản phẩm & Thông báo quyền Hoàn trả 7 ngày sau khi giao thành công ('hoan_tat')
     */
    public static function guiThuMoiDanhGia(string $toEmail, string $toName, int $maDonHang, array $danhSachSanPham): bool {
        $safeName    = htmlspecialchars($toName !== '' ? $toName : 'Ba Mẹ', ENT_QUOTES, 'UTF-8');
        $orderCode   = 'FK-ORD-' . str_pad((string)$maDonHang, 6, '0', STR_PAD_LEFT);
        $baseUrl     = defined('APP_BASE_URL') ? APP_BASE_URL : '/FashionKids';
        $scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host        = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $trackingUrl = $scheme . '://' . $host . $baseUrl . '/index.php?c=order&a=tracking&id=' . (int)$maDonHang;
        $safeUrl     = htmlspecialchars($trackingUrl, ENT_QUOTES, 'UTF-8');

        $productRowsHtml = '';
        foreach ($danhSachSanPham as $item) {
            $prodName = htmlspecialchars((string)($item['ten_san_pham'] ?? $item['name'] ?? 'Sản phẩm FashionKids'), ENT_QUOTES, 'UTF-8');
            $prodSize = htmlspecialchars((string)($item['kich_co_chon'] ?? $item['size'] ?? 'Tiêu chuẩn'), ENT_QUOTES, 'UTF-8');
            $prodQty  = (int)($item['so_luong'] ?? $item['quantity'] ?? 1);
            $prodAmt  = number_format((float)($item['don_gia_mua'] ?? $item['price'] ?? 0) * $prodQty, 0, ',', '.');
            $productRowsHtml .= "
                <tr>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #F3EFEA; color: #2D3142; font-size: 14px; font-weight: 600;'>{$prodName}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #F3EFEA; color: #6B7280; font-size: 13px; text-align: center;'>{$prodSize}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #F3EFEA; color: #2D3142; font-size: 13px; text-align: center;'>x{$prodQty}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #F3EFEA; color: #FF8A7A; font-size: 13px; font-weight: 700; text-align: right;'>{$prodAmt} đ</td>
                </tr>";
        }

        if ($productRowsHtml === '') {
            $productRowsHtml = "
                <tr>
                    <td colspan='4' style='padding: 12px; color: #6B7280; font-size: 13px; text-align: center;'>
                        Các sản phẩm thời trang sợi tre trong đơn hàng #{$orderCode}
                    </td>
                </tr>";
        }

        $subject  = "[FashionKids] Đơn hàng #{$orderCode} đã giao thành công — Mời Ba Mẹ đánh giá sản phẩm!";
        $bodyHtml = "
            <div style='font-family: Arial, Helvetica, sans-serif; background-color: #FAF8F5; padding: 28px 16px;'>
                <div style='max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 18px; overflow: hidden; border: 1px solid #F0EAE1; box-shadow: 0 8px 24px rgba(45,49,66,0.06);'>
                    <div style='background: linear-gradient(135deg, #FFF0ED 0%, #EAF7F2 100%); padding: 24px 28px; text-align: center; border-bottom: 1px solid #F3EFEA;'>
                        <div style='display: inline-block; background: #D1FAE5; color: #065F46; font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 999px; margin-bottom: 10px;'>
                            GIAO HÀNG THÀNH CÔNG • #{$orderCode}
                        </div>
                        <h2 style='margin: 0; color: #2D3142; font-size: 22px; font-weight: 700;'>Cảm Ơn Ba Mẹ Đã Tin Chọn FashionKids!</h2>
                    </div>

                    <div style='padding: 26px 28px;'>
                        <p style='color: #2D3142; font-size: 15px; line-height: 1.6; margin-top: 0;'>
                            Xin chào <strong>{$safeName}</strong>,
                        </p>
                        <p style='color: #6B7280; font-size: 14px; line-height: 1.65;'>
                            Đơn hàng <strong style='color: #FF8A7A;'>#{$orderCode}</strong> đã được giao hoàn tất đến gia đình mình.
                            FashionKids rất mong nhận được đánh giá thực tế từ Ba Mẹ về độ mềm mịn, kích cỡ và trải nghiệm mặc của bé để cải thiện chất lượng phục vụ mỗi ngày!
                        </p>

                        <table style='width: 100%; border-collapse: collapse; margin: 18px 0; background: #FAF8F5; border-radius: 12px; overflow: hidden;'>
                            <thead>
                                <tr style='background: #F4EFE8;'>
                                    <th style='padding: 10px 12px; text-align: left; font-size: 12px; color: #4B5563; text-transform: uppercase;'>Sản phẩm</th>
                                    <th style='padding: 10px 12px; text-align: center; font-size: 12px; color: #4B5563; text-transform: uppercase;'>Size</th>
                                    <th style='padding: 10px 12px; text-align: center; font-size: 12px; color: #4B5563; text-transform: uppercase;'>SL</th>
                                    <th style='padding: 10px 12px; text-align: right; font-size: 12px; color: #4B5563; text-transform: uppercase;'>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$productRowsHtml}
                            </tbody>
                        </table>

                        <div style='text-align: center; margin: 26px 0 22px;'>
                            <a href='{$safeUrl}'
                               style='display: inline-block; background-color: #FF8A7A; color: #FFFFFF; text-decoration: none; font-size: 15px; font-weight: 700; padding: 13px 28px; border-radius: 999px; box-shadow: 0 4px 12px rgba(255,138,122,0.35);'>
                                Đánh giá sản phẩm ngay
                            </a>
                        </div>

                        <div style='background-color: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;'>
                            <div style='color: #92400E; font-size: 13px; font-weight: 700; margin-bottom: 4px;'>
                                Chính sách Hỗ trợ Đổi trả / Hoàn hàng 7 ngày
                            </div>
                            <div style='color: #78350F; font-size: 13px; line-height: 1.55;'>
                                Nếu sản phẩm không vừa size hoặc phát sinh lỗi từ nhà sản xuất trong <strong>7 ngày</strong> kể từ khi nhận hàng, Ba Mẹ có thể bấm vào nút <strong>Yêu cầu hoàn hàng / Trả hàng</strong> ngay tại trang chi tiết đơn hàng trên website để được thu hồi tận nơi &amp; hoàn tiền nhanh chóng.
                            </div>
                        </div>

                        <p style='color: #9CA3AF; font-size: 12px; text-align: center; margin-bottom: 0;'>
                            Hoặc truy cập trực tiếp đường dẫn: <a href='{$safeUrl}' style='color: #6B7280;'>{$safeUrl}</a>
                        </p>
                    </div>
                </div>
            </div>";

        return self::sendHtmlEmail($toEmail, $toName, $subject, $bodyHtml);
    }

    /**
     * Hàm lõi gửi email HTML qua PHPMailer (hỗ trợ dự phòng cổng 587 STARTTLS -> 465 SMTPS)
     */
    private static function sendHtmlEmail(string $toEmail, string $toName, string $subject, string $bodyHtml): bool {
        self::$lastError = '';

        $smtpHost = trim(defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com');
        $smtpUser = trim(defined('SMTP_USER') ? SMTP_USER : '');
        $smtpPass = str_replace(' ', '', trim(defined('SMTP_PASS') ? SMTP_PASS : ''));
        $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'FashionKids Support';
        $primaryPort = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;

        if ($smtpUser === '' || $smtpUser === 'email_cua_ban@gmail.com') {
            self::$lastError = 'Chưa cấu hình địa chỉ Gmail người gửi (SMTP_USER) trong config/app.php.';
            error_log('[FashionKids Mailer] ' . self::$lastError);
            return false;
        }
        if ($smtpPass === '' || strlen($smtpPass) !== 16) {
            self::$lastError = 'Mật khẩu ứng dụng Gmail (SMTP_PASS) phải gồm đúng 16 ký tự chữ cái (không tính dấu cách).';
            error_log('[FashionKids Mailer] ' . self::$lastError);
            return false;
        }
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = 'Địa chỉ email người nhận không đúng định dạng.';
            error_log('[FashionKids Mailer] ' . self::$lastError . ' (' . $toEmail . ')');
            return false;
        }
        if (!extension_loaded('openssl')) {
            self::$lastError = 'Máy chủ PHP chưa bật tiện ích mở rộng OpenSSL để kết nối SMTP bảo mật.';
            error_log('[FashionKids Mailer] ' . self::$lastError);
            return false;
        }

        // Thử phương thức chính (mặc định 587 STARTTLS), chỉ fallback sang 465 SMTPS nếu lỗi kết nối/TLS (không lặp lại nếu đã báo sai mật khẩu 535)
        $attempts = [
            ['port' => $primaryPort, 'secure' => ($primaryPort === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS],
        ];
        if ($primaryPort !== 465) {
            $attempts[] = ['port' => 465, 'secure' => PHPMailer::ENCRYPTION_SMTPS];
        }

        foreach ($attempts as $cfg) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUser;
                $mail->Password   = $smtpPass;
                $mail->SMTPSecure = $cfg['secure'];
                $mail->Port       = $cfg['port'];
                $mail->CharSet    = 'UTF-8';
                $mail->Timeout    = 15;

                // Tương thích chứng chỉ SSL trên môi trường XAMPP Windows
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ];

                $mail->setFrom($smtpUser, $fromName);
                $mail->addAddress($toEmail, $toName !== '' ? $toName : 'Quý khách');
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $bodyHtml;

                if ($mail->send()) {
                    return true;
                }
            } catch (Exception $e) {
                self::$lastError = $mail->ErrorInfo ?: $e->getMessage();
                error_log("[FashionKids Mailer Port {$cfg['port']}] " . self::$lastError);

                // Nếu lỗi xác thực 535 (sai Gmail hoặc App Password), dừng ngay không thử cổng 465 nữa
                if (strpos(self::$lastError, '535') !== false) {
                    return false;
                }
            }
        }

        return false;
    }
}
