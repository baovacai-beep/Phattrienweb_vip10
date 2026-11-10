<?php
/**
 * PHPMailer RFC821/RFC5321 SMTP client class.
 * Hỗ trợ kết nối SMTP Gmail qua cổng 587 (STARTTLS) & 465 (SMTPS) kèm xác thực AUTH LOGIN.
 * @package PHPMailer\PHPMailer
 */

namespace PHPMailer\PHPMailer;

class SMTP {
    public const VERSION = '6.9.1';
    public const CRLF = "\r\n";
    public const DEFAULT_PORT = 25;
    public const MAX_LINE_LENGTH = 998;
    public const DEBUG_OFF = 0;
    public const DEBUG_CLIENT = 1;
    public const DEBUG_SERVER = 2;

    public int $do_debug = self::DEBUG_OFF;
    public int $Timeout = 15;
    public int $Timelimit = 30;

    /** @var resource|null */
    protected $smtp_conn = null;
    protected array $error = [
        'error'        => '',
        'detail'       => '',
        'smtp_code'    => '',
        'smtp_code_ex' => '',
    ];
    protected string $last_reply = '';

    /**
     * Kết nối tới máy chủ SMTP
     */
    public function connect(string $host, int $port = 587, int $timeout = 15, array $options = []): bool {
        $this->error = ['error' => '', 'detail' => '', 'smtp_code' => '', 'smtp_code_ex' => ''];

        if (empty($options['ssl'])) {
            $options['ssl'] = [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ];
        }

        $streamContext = stream_context_create($options);
        $errno = 0;
        $errstr = '';

        $this->smtp_conn = @stream_socket_client(
            "{$host}:{$port}",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $streamContext
        );

        if (!is_resource($this->smtp_conn)) {
            $this->setError("Không thể kết nối tới máy chủ SMTP {$host}:{$port}", "{$errno}: {$errstr}");
            return false;
        }

        stream_set_timeout($this->smtp_conn, $timeout);
        $announce = $this->get_lines();
        $this->last_reply = $announce;
        $code = (int)substr($announce, 0, 3);
        if ($code !== 220) {
            $this->setError('Máy chủ SMTP không phản hồi mã 220 khi mở kết nối', $announce, (string)$code);
            return false;
        }

        return true;
    }

    /**
     * Gửi lệnh EHLO / HELO
     */
    public function hello(string $host = 'localhost'): bool {
        if ($this->sendCommand('EHLO', "EHLO {$host}", 250)) {
            return true;
        }
        return $this->sendCommand('HELO', "HELO {$host}", 250);
    }

    /**
     * Kích hoạt mã hóa STARTTLS trên kết nối hiện tại (Port 587)
     */
    public function startTLS(): bool {
        if (!$this->sendCommand('STARTTLS', 'STARTTLS', 220)) {
            return false;
        }

        $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        }
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
            $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
        }

        $ok = @stream_socket_enable_crypto($this->smtp_conn, true, $cryptoMethod);
        if ($ok !== true) {
            $this->setError('Không thể khởi tạo phiên mã hóa TLS (STARTTLS) với máy chủ SMTP');
            return false;
        }

        return true;
    }

    /**
     * Xác thực tài khoản SMTP (AUTH LOGIN)
     * Tự động loại bỏ khoảng trắng trong Mật khẩu ứng dụng Gmail (16 ký tự)
     */
    public function authenticate(string $username, string $password): bool {
        $cleanPass = str_replace(' ', '', trim($password));

        if (!$this->sendCommand('AUTH LOGIN', 'AUTH LOGIN', 334)) {
            return false;
        }
        if (!$this->sendCommand('Username', base64_encode(trim($username)), 334)) {
            return false;
        }
        if (!$this->sendCommand('Password', base64_encode($cleanPass), 235)) {
            return false;
        }

        return true;
    }

    /**
     * Lệnh MAIL FROM
     */
    public function mail(string $from): bool {
        return $this->sendCommand('MAIL FROM', "MAIL FROM:<{$from}>", 250);
    }

    /**
     * Lệnh RCPT TO
     */
    public function recipient(string $toaddress): bool {
        return $this->sendCommand('RCPT TO', "RCPT TO:<{$toaddress}>", [250, 251]);
    }

    /**
     * Lệnh DATA để truyền nội dung bức thư
     */
    public function data(string $msg_data): bool {
        if (!$this->sendCommand('DATA', 'DATA', 354)) {
            return false;
        }

        // Chuẩn hóa dòng và chống lỗi dấu chấm đầu dòng theo RFC 5321
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $msg_data));
        foreach ($lines as $line) {
            if (isset($line[0]) && $line[0] === '.') {
                $line = '.' . $line;
            }
            $this->client_send($line . self::CRLF);
        }

        return $this->sendCommand('DATA END', '.', 250);
    }

    /**
     * Đóng kết nối SMTP
     */
    public function quit(bool $close_on_error = true): bool {
        $noerror = $this->sendCommand('QUIT', 'QUIT', 221);
        if ($close_on_error || $noerror) {
            $this->close();
        }
        return $noerror;
    }

    public function close(): void {
        if (is_resource($this->smtp_conn)) {
            @fclose($this->smtp_conn);
            $this->smtp_conn = null;
        }
    }

    public function getError(): array {
        return $this->error;
    }

    public function getLastReply(): string {
        return $this->last_reply;
    }

    /**
     * Gửi lệnh tới máy chủ SMTP và kiểm tra mã phản hồi
     * @param string $command Tên lệnh
     * @param string $commandstring Chuỗi lệnh gửi đi
     * @param int|array $expect Mã phản hồi kỳ vọng
     */
    protected function sendCommand(string $command, string $commandstring, $expect): bool {
        if (!is_resource($this->smtp_conn)) {
            $this->setError("Gửi lệnh {$command} thất bại do chưa có kết nối SMTP");
            return false;
        }

        $this->client_send($commandstring . self::CRLF);
        $this->last_reply = $this->get_lines();
        $code = (int)substr($this->last_reply, 0, 3);

        $expectedCodes = is_array($expect) ? $expect : [$expect];
        if (!in_array($code, $expectedCodes, true)) {
            $this->setError(
                "Lệnh {$command} thất bại",
                trim($this->last_reply),
                (string)$code
            );
            return false;
        }

        return true;
    }

    protected function client_send(string $data): int {
        if (!is_resource($this->smtp_conn)) {
            return 0;
        }
        return (int)@fwrite($this->smtp_conn, $data);
    }

    protected function get_lines(): string {
        if (!is_resource($this->smtp_conn)) {
            return '';
        }

        $data = '';
        $endtime = time() + $this->Timelimit;

        while (is_resource($this->smtp_conn) && !feof($this->smtp_conn)) {
            $str = @fgets($this->smtp_conn, 515);
            $data .= $str;
            if (!isset($str[3]) || $str[3] === ' ' || $str[3] === "\r" || $str[3] === "\n") {
                break;
            }
            $info = stream_get_meta_data($this->smtp_conn);
            if (!empty($info['timed_out']) || time() > $endtime) {
                break;
            }
        }

        return $data;
    }

    protected function setError(string $message, string $detail = '', string $smtp_code = ''): void {
        $this->error = [
            'error'        => $message,
            'detail'       => $detail,
            'smtp_code'    => $smtp_code,
            'smtp_code_ex' => '',
        ];
    }
}
