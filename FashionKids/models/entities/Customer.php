<?php
/**
 * Lớp Khách Hàng (models/entities/Customer.php)
 * Kế thừa User - Đóng gói quyền lợi VIP và tích điểm thưởng
 */

require_once __DIR__ . '/User.php';

class Customer extends User {
    private int $diem_tich_luy;
    private ?int $ma_hang_thanh_vien;
    private float $phan_tram_giam_gia;

    public function __construct(
        int $id = 0,
        string $username = '',
        string $name = '',
        string $email = '',
        string $phone = '',
        int $points = 0,
        ?int $tierId = 1,
        float $discount = 0.0
    ) {
        parent::__construct($id, $username, $name, $email, $phone, 'khach_hang');
        $this->diem_tich_luy      = $points;
        $this->ma_hang_thanh_vien = $tierId;
        $this->phan_tram_giam_gia = $discount;
    }

    public function getDiemTichLuy(): int { return $this->diem_tich_luy; }
    public function getMaHangThanhVien(): ?int { return $this->ma_hang_thanh_vien; }
    public function getPhanTramGiamGia(): float { return $this->phan_tram_giam_gia; }

    public function setDiemTichLuy(int $pts): void { $this->diem_tich_luy = $pts; }
    public function setPhanTramGiamGia(float $dc): void { $this->phan_tram_giam_gia = $dc; }

    public function coQuyenQuanTri(): bool {
        return false;
    }
}
