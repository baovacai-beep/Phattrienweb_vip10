<?php
/**
 * Interface Gợi ý kích cỡ thông minh (models/interfaces/IGoiYKichCo.php)
 * Hiện thực hóa tính ĐA HÌNH (Polymorphism) trong OOP:
 * Quần áo dự đoán size theo chiều cao (cm), Giày dép dự đoán theo độ dài bàn chân (cm).
 */

interface IGoiYKichCo {
    /**
     * Thuật toán đa hình dự đoán size chuẩn dựa trên số đo vật lý
     *
     * @param float $soDo Chiều cao hoặc chiều dài bàn chân của bé (cm)
     * @return string Tên kích cỡ đề xuất (VD: 'Size 1 (1-2T)', 'Size 24')
     */
    public function duDoanSize(float $soDo): string;

    /**
     * Phương thức tương thích chuẩn sơ đồ lớp
     */
    public function goiYKichCoChuan(float $soDo): string;
}
