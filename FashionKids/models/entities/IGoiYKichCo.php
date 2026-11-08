<?php
/**
 * Interface Gợi ý kích cỡ chuẩn (models/entities/IGoiYKichCo.php)
 * Minh họa tính Đa hình (Polymorphism) trong OOP: 
 * Quần áo tính theo Chiều cao, Giày dép tính theo Chiều dài bàn chân.
 */

interface IGoiYKichCo {
    /**
     * Thuật toán đa hình gợi ý size chuẩn dựa trên số đo vật lý
     *
     * @param float $soDo Số đo (cm)
     * @return string Tên kích cỡ đề xuất (VD: 'Size 1', 'Size 22')
     */
    public function goiYKichCoChuan(float $soDo): string;
}
