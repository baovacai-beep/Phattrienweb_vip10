<?php
/**
 * Lớp Thanh toán VietQR Napas 247 (models/entities/ThanhToanVietQR.php)
 * Hiện thực hóa interface IPhuongThucThanhToan - Sinh mã QR thanh toán động MB Bank
 */

require_once __DIR__ . '/../../config/payment.php';
require_once __DIR__ . '/../interfaces/IPhuongThucThanhToan.php';

class ThanhToanVietQR implements IPhuongThucThanhToan {
    private string $bankId;
    private string $bankName;
    private string $accountNo;
    private string $accountName;
    private string $template;
    private string $transferPrefix;

    public function __construct() {
        $this->bankId         = defined('VIETQR_BANK_ID') ? VIETQR_BANK_ID : 'MB';
        $this->bankName       = defined('VIETQR_BANK_NAME') ? VIETQR_BANK_NAME : 'MB Bank';
        $this->accountNo      = defined('VIETQR_ACCOUNT_NO') ? VIETQR_ACCOUNT_NO : '0913672861';
        $this->accountName    = defined('VIETQR_ACCOUNT_NAME') ? VIETQR_ACCOUNT_NAME : 'HO GIA BAO';
        $this->template       = defined('VIETQR_TEMPLATE') ? VIETQR_TEMPLATE : 'compact2';
        $this->transferPrefix = defined('VIETQR_TRANSFER_PREFIX') ? VIETQR_TRANSFER_PREFIX : 'FK';
    }

    public function getBankInfo(): array {
        return [
            'bank_id'         => $this->bankId,
            'bank_name'       => $this->bankName,
            'account_no'      => $this->accountNo,
            'account_name'    => $this->accountName,
            'transfer_prefix' => $this->transferPrefix,
        ];
    }

    public function buildTransferContent(int $maDonHang): string {
        return $this->transferPrefix . $maDonHang;
    }

    public function thucHienThanhToan(int $maDonHang, float $soTien): array {
        $amountInt = max(0, (int)round($soTien));
        $noiDung   = $this->buildTransferContent($maDonHang);
        $qrUrl     = sprintf(
            'https://img.vietqr.io/image/%s-%s-%s.png?amount=%d&addInfo=%s&accountName=%s',
            rawurlencode($this->bankId),
            rawurlencode($this->accountNo),
            rawurlencode($this->template),
            $amountInt,
            urlencode($noiDung),
            urlencode($this->accountName)
        );

        return [
            'phuong_thuc'   => 'VietQR',
            'trang_thai'    => 'cho_chuyen_khoan',
            'qr_image'      => $qrUrl,
            'bank_id'       => $this->bankId,
            'bank_name'     => $this->bankName,
            'account_no'    => $this->accountNo,
            'account_name'  => $this->accountName,
            'transfer_code' => $noiDung,
            'amount'        => $amountInt,
            'thong_tin'     => "Quét mã để chuyển khoản đúng {$amountInt}đ qua {$this->bankName} ({$this->accountNo} - {$this->accountName})."
        ];
    }
}
