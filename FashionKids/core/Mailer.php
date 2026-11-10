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

        $safeName = htmlspecialchars($toName !== '' ? $toName : 'Quý khách', ENT_QUOTES, 'UTF-8');
        $safeOtp  = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $subject  = "[FashionKids] Mã xác thực đăng ký tài khoản: {$otp}";
        $bodyHtml = "
            <div style='font-family: Arial, sans-serif; background-color: #F8FAFC; padding: 25px;'>
                <div style='max-width: 500px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; padding: 25px; border-top: 4px solid #6366F1; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>
                    <h2 style='color: #1E293B; text-align: center; margin-bottom: 10px;'>Kích Hoạt Tài Khoản FashionKids</h2>
                    <p style='color: #64748B; font-size: 14px;'>Xin chào <strong>{$safeName}</strong>,</p>
                    <p style='color: #64748B; font-size: 14px;'>Bạn vừa yêu cầu đăng ký tài khoản tại FashionKids. Dưới đây là mã bảo vệ OTP của bạn:</p>
                    <div style='text-align: center; margin: 25px 0;'>
                        <span style='display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #6366F1; background: #EEF2FF; padding: 10px 24px; border-radius: 8px;'>{$safeOtp}</span>
                    </div>
                    <p style='color: #EF4444; font-size: 13px; text-align: center;'>* Mã OTP này có hiệu lực trong vòng <strong>5 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.</p>
                </div>
            </div>";

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
