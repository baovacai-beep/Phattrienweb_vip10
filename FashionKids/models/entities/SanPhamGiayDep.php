<?php
/**
 * Lớp Sản phẩm Giày dép (models/entities/SanPhamGiayDep.php)
 * Kế thừa Product, implement IGoiYKichCo (Đa hình theo Độ dài bàn chân)
 */

require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/../interfaces/IGoiYKichCo.php';

class SanPhamGiayDep extends Product implements IGoiYKichCo {
    public function __construct(
        int $ma_san_pham = 0,
        int $ma_danh_muc = 4,
        string $ten_san_pham = '',
        float $gia_ban = 1000.0,
        int $so_luong_ton_kho = 0,
        string $duong_dan_anh = ''
    ) {
        parent::__construct(
            $ma_san_pham, $ma_danh_muc, $ten_san_pham, $gia_ban, $so_luong_ton_kho, $duong_dan_anh, 'giay_dep'
        );
    }

    /**
     * Triển khai đa hình: Dự đoán cỡ giày dựa trên chiều dài bàn chân của bé (cm)
     */
    public function duDoanSize(float $chieuDaiChan): string {
        if ($chieuDaiChan <= 14.9) return "Size 24";
        if ($chieuDaiChan <= 15.9) return "Size 26";
        return "Size 28";
    }

    /**
     * Alias tương thích chuẩn sơ đồ lớp
     */
    public function goiYKichCoChuan(float $soDo): string {
        return $this->duDoanSize($soDo);
    }
}
