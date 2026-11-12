<?php
/**
 * Bộ nạp biến môi trường từ tệp .env (core/Env.php)
 * Không phụ thuộc thư viện ngoài; an toàn với XAMPP & Hosting PHP 8.x
 */

class Env {
    private static bool $loaded = false;
    private static array $vars = [];

    public static function load(?string $envPath = null): void {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;

        $path = $envPath ?: (dirname(__DIR__) . '/.env');
        if (!is_file($path)) {
            return;
        }

        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return;
        }
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        if (!is_array($lines)) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#') || str_starts_with($trimmed, ';')) {
                continue;
            }
            if (str_starts_with($trimmed, 'export ')) {
                $trimmed = trim(substr($trimmed, 7));
            }
            $eqPos = strpos($trimmed, '=');
            if ($eqPos === false) {
                continue;
            }

            $key = trim(substr($trimmed, 0, $eqPos));
            $val = trim(substr($trimmed, $eqPos + 1));

            if ($key === '') {
                continue;
            }

            // Gỡ cặp nháy đơn / nháy kép bao ngoài nếu có
            $len = strlen($val);
            if ($len >= 2) {
                $first = $val[0];
                $last  = $val[$len - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $val = substr($val, 1, -1);
                } else {
                    // Bỏ comment inline kiểu KEY=value # comment khi không bọc nháy
                    $hashPos = strpos($val, ' #');
                    if ($hashPos !== false) {
                        $val = trim(substr($val, 0, $hashPos));
                    }
                }
            }

            self::$vars[$key] = $val;
            if (getenv($key) === false && !isset($_ENV[$key])) {
                @putenv($key . '=' . $val);
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }

    public static function get(string $key, string $default = ''): string {
        self::load();
        if (isset(self::$vars[$key]) && self::$vars[$key] !== '') {
            return (string)self::$vars[$key];
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string)$_ENV[$key];
        }
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return (string)$val;
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string)$_SERVER[$key];
        }
        return $default;
    }
}
