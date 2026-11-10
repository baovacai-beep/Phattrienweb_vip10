<?php
/**
 * PHPMailer - PHP email creation and transport class.
 * Hỗ trợ gửi thư HTML UTF-8 qua Gmail SMTP (STARTTLS / SMTPS).
 * @package PHPMailer\PHPMailer
 */

namespace PHPMailer\PHPMailer;

require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/SMTP.php';

class PHPMailer {
    public const CHARSET_UTF8 = 'UTF-8';
    public const ENCRYPTION_STARTTLS = 'tls';
    public const ENCRYPTION_SMTPS = 'ssl';

    public string $Mailer = 'mail';
    public string $Host = 'localhost';
    public int $Port = 587;
    public bool $SMTPAuth = false;
    public string $Username = '';
    public string $Password = '';
    public string $SMTPSecure = '';
    public array $SMTPOptions = [];
    public int $Timeout = 15;
    public int $SMTPDebug = 0;

    public string $CharSet = self::CHARSET_UTF8;
    public string $ContentType = 'text/plain';
    public string $From = 'root@localhost';
    public string $FromName = 'Root User';
    public string $Subject = '';
    public string $Body = '';
    public string $AltBody = '';
    public string $ErrorInfo = '';

    protected bool $exceptions = false;
    protected array $to = [];
    protected array $ReplyTo = [];

    public function __construct(bool $exceptions = false) {
        $this->exceptions = $exceptions;
    }

    public function isSMTP(): void {
        $this->Mailer = 'smtp';
    }

    public function isMail(): void {
        $this->Mailer = 'mail';
    }

    public function isHTML(bool $isHtml = true): void {
        $this->ContentType = $isHtml ? 'text/html' : 'text/plain';
    }

    public function setFrom(string $address, string $name = '', bool $auto = true): bool {
        $address = trim($address);
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            $this->setError("Địa chỉ người gửi không hợp lệ: {$address}");
            return false;
        }
        $this->From = $address;
        $this->FromName = trim($name);
        return true;
    }

    public function addAddress(string $address, string $name = ''): bool {
        $address = trim($address);
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            $this->setError("Địa chỉ người nhận không hợp lệ: {$address}");
            return false;
        }
        $this->to[] = [$address, trim($name)];
        return true;
    }

    public function addReplyTo(string $address, string $name = ''): bool {
        $address = trim($address);
        if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
            $this->ReplyTo[] = [$address, trim($name)];
            return true;
        }
        return false;
    }

    public function clearAddresses(): void {
        $this->to = [];
    }

    /**
     * Mã hóa tiêu đề/tên theo chuẩn RFC 2047 UTF-8 Base64 để không bao giờ lỗi font tiếng Việt
     */
    protected function encodeHeader(string $str): string {
        $str = trim(str_replace(["\r", "\n"], '', $str));
        if ($str === '') {
            return '';
        }
        if (!preg_match('/[^\x20-\x7E]/', $str)) {
            return $str;
        }
        return '=?' . $this->CharSet . '?B?' . base64_encode($str) . '?=';
    }

    protected function formatAddress(string $email, string $name = ''): string {
        $email = trim(str_replace(["\r", "\n"], '', $email));
        if ($name === '') {
            return "<{$email}>";
        }
        return $this->encodeHeader($name) . " <{$email}>";
    }

    /**
     * Thực hiện gửi email qua giao thức SMTP (hoặc mail() nếu không bật isSMTP)
     */
    public function send(): bool {
        if (empty($this->to)) {
            $this->setError('Chưa có địa chỉ người nhận (addAddress).');
            return false;
        }

        if ($this->Mailer === 'smtp') {
            return $this->smtpSend();
        }

        return $this->mailSend();
    }

    protected function smtpSend(): bool {
        $smtp = new SMTP();
        $smtp->do_debug = $this->SMTPDebug;
        $smtp->Timeout  = $this->Timeout;

        $host = trim($this->Host);
        $port = (int)$this->Port;
        $connectHost = ($this->SMTPSecure === self::ENCRYPTION_SMTPS) ? "ssl://{$host}" : "tcp://{$host}";

        if (!$smtp->connect($connectHost, $port, $this->Timeout, $this->SMTPOptions)) {
            $err = $smtp->getError();
            $this->setError('Lỗi kết nối SMTP: ' . ($err['error'] ?? '') . ' ' . ($err['detail'] ?? ''));
            return false;
        }

        $rawHost = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $localHost = preg_replace('/[^A-Za-z0-9.-]/', '', (string)$rawHost);
        if (empty($localHost)) {
            $localHost = 'localhost';
        }

        if (!$smtp->hello($localHost)) {
            $err = $smtp->getError();
            $smtp->close();
            $this->setError('Lỗi lệnh EHLO: ' . ($err['detail'] ?? ''));
            return false;
        }

        if ($this->SMTPSecure === self::ENCRYPTION_STARTTLS) {
            if (!$smtp->startTLS()) {
                $err = $smtp->getError();
                $smtp->close();
                $this->setError('Lỗi khởi tạo STARTTLS: ' . ($err['error'] ?? '') . ' ' . ($err['detail'] ?? ''));
                return false;
            }
            if (!$smtp->hello($localHost)) {
                $err = $smtp->getError();
                $smtp->close();
                $this->setError('Lỗi EHLO sau STARTTLS: ' . ($err['detail'] ?? ''));
                return false;
            }
        }

        if ($this->SMTPAuth) {
            if (!$smtp->authenticate($this->Username, $this->Password)) {
                $err = $smtp->getError();
                $smtp->close();
                $this->setError('Xác thực SMTP thất bại (Kiểm tra lại Gmail & Mật khẩu ứng dụng 16 ký tự): ' . ($err['detail'] ?? ''));
                return false;
            }
        }

        if (!$smtp->mail($this->From)) {
            $err = $smtp->getError();
            $smtp->close();
            $this->setError('Lỗi MAIL FROM: ' . ($err['detail'] ?? ''));
            return false;
        }

        foreach ($this->to as $recipient) {
            if (!$smtp->recipient($recipient[0])) {
                $err = $smtp->getError();
                $smtp->close();
                $this->setError("Lỗi RCPT TO ({$recipient[0]}): " . ($err['detail'] ?? ''));
                return false;
            }
        }

        $rawMessage = $this->createMimeMessage();
        if (!$smtp->data($rawMessage)) {
            $err = $smtp->getError();
            $smtp->close();
            $this->setError('Lỗi gửi dữ liệu thư (DATA): ' . ($err['detail'] ?? ''));
            return false;
        }

        $smtp->quit();
        return true;
    }

    protected function createMimeMessage(): string {
        $headers = [];
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'From: ' . $this->formatAddress($this->From, $this->FromName);

        $toFormatted = [];
        foreach ($this->to as $r) {
            $toFormatted[] = $this->formatAddress($r[0], $r[1]);
        }
        $headers[] = 'To: ' . implode(', ', $toFormatted);
        $headers[] = 'Subject: ' . $this->encodeHeader($this->Subject);
        $headers[] = 'Message-ID: <' . md5(uniqid((string)time(), true)) . '@fashionkids.vn>';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Content-Type: {$this->ContentType}; charset={$this->CharSet}";
        $headers[] = 'Content-Transfer-Encoding: base64';

        $encodedBody = chunk_split(base64_encode($this->Body), 76, "\r\n");

        return implode("\r\n", $headers) . "\r\n\r\n" . $encodedBody;
    }

    protected function mailSend(): bool {
        $toEmails = array_map(fn($r) => $r[0], $this->to);
        $headers = [
            'MIME-Version: 1.0',
            "Content-Type: {$this->ContentType}; charset={$this->CharSet}",
            'From: ' . $this->formatAddress($this->From, $this->FromName),
        ];
        $ok = @mail(implode(', ', $toEmails), $this->encodeHeader($this->Subject), $this->Body, implode("\r\n", $headers));
        if (!$ok) {
            $this->setError('Hàm mail() nội bộ không thể gửi thư.');
            return false;
        }
        return true;
    }

    protected function setError(string $msg): void {
        $this->ErrorInfo = $msg;
        if ($this->exceptions) {
            throw new Exception($msg);
        }
    }
}
