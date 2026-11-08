<?php
/**
 * Lớp Sản phẩm Quần áo (models/entities/SanPhamQuanAo.php)
 * Kế thừa Product, implement IGoiYKichCo (Đa hình theo Chiều cao)
 */

require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/../interfaces/IGoiYKichCo.php';

class SanPhamQuanAo extends Product implements IGoiYKichCo {
    public function __construct(
        int $ma_san_pham = 0,
        int $ma_danh_muc = 1,
        string $ten_san_pham = '',
        float $gia_ban = 1000.0,
        int $so_luong_ton_kho = 0,
        string $duong_dan_anh = ''
    ) {
        parent::__construct(
            $ma_san_pham, $ma_danh_muc, $ten_san_pham, $gia_ban, $so_luong_ton_kho, $duong_dan_anh, 'quan_ao'
        );
    }

    /**
     * Triển khai đa hình: Dự đoán size quần áo dựa trên chiều cao của bé
     */
    public function duDoanSize(float $chieuCao): string {
        if ($chieuCao < 90) return "Size 1 (1-2T)";
        if ($chieuCao <= 100) return "Size 2 (2-3T)";
        if ($chieuCao <= 110) return "Size 3 (3-4T)";
        return "Size 4 (4-5T)";
    }

    /**
     * Alias tương thích chuẩn sơ đồ lớp
     */
    public function goiYKichCoChuan(float $soDo): string {
        return $this->duDoanSize($soDo);
    }
}
