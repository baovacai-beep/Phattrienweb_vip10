<?php
/**
 * PHPMailer Exception class.
 * @package PHPMailer\PHPMailer
 */

namespace PHPMailer\PHPMailer;

class Exception extends \Exception {
    /**
     * Prettify error message output.
     * @return string
     */
    public function errorMessage(): string {
        return '<strong>' . htmlspecialchars($this->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</strong><br />\n";
    }
}
