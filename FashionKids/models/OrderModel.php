<?php
/**
 * Lớp Model Đơn Hàng (models/OrderModel.php)
 * Đảm nhiệm: tgb_ (Trần Gia Bảo) & lnkt_ (Lê Nguyễn Khánh Trình)
 * Khớp chuẩn 100% với CSDL phpMyAdmin (lnkt_don_hang & tgb_chi_tiet_don_hang)
 * Tích hợp Stored Procedure: sp_tgb_tao_don_hang_va_chi_tiet & sp_lnkt_cap_nhat_trang_thai_don_hang
 * Đã bổ sung $stmt->closeCursor() để triệt tiêu lỗi PDO 2014 unbuffered queries.
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/entities/DonHang.php';
require_once __DIR__ . '/entities/ChiTietDonHang.php';

class OrderModel extends Model {

    /**
     * Tạo đơn hàng tự động trừ kho qua sp_tgb_tao_don_hang_va_chi_tiet
     */
    public function taoDonHang(
        ?int $maNguoiDung,
        string $tenNhan,
        string $sdtNhan,
        string $emailNhan,
        string $diaChi,
        string $tinhThanh,
        string $quanHuyen,
        int $maSanPham,
        string $kichCo,
        int $soLuong
    ): int {
        if ($this->hasConnection()) {
            try {
                $sql = "CALL sp_tgb_tao_don_hang_va_chi_tiet(
                    :ma_user, :ten_nhan, :sdt, :email, :dia_chi, :tinh, :huyen, :ma_sp, :kich_co, :sl, @ma_don_moi
                )";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':ma_user'  => $maNguoiDung,
                    ':ten_nhan' => $tenNhan,
                    ':sdt'      => $sdtNhan,
                    ':email'    => $emailNhan,
                    ':dia_chi'  => $diaChi,
                    ':tinh'     => $tinhThanh,
                    ':huyen'    => $quanHuyen,
                    ':ma_sp'    => $maSanPham,
                    ':kich_co'  => $kichCo,
                    ':sl'       => $soLuong
                ]);

                // Đóng con trỏ truy vấn trước khi lấy biến output để tránh lỗi PDO 2014
                $stmt->closeCursor();

                $res = $this->db->query("SELECT @ma_don_moi AS ma_don_hang")->fetch();
                return (int)($res['ma_don_hang'] ?? 0);
            } catch (PDOException $e) {
                error_log("Lỗi tạo đơn qua sp_tgb_tao_don_hang_va_chi_tiet: " . $e->getMessage());
            }
        }

        return rand(1005, 9999);
    }

    public function createOrder(array $order, array $items): int {
        $first = $items[0] ?? [];
        return $this->taoDonHang(
            $order['user_id'] ?? null,
            $order['receiver_name'],
            $order['receiver_phone'],
            $order['receiver_email'] ?? '',
            $order['shipping_address'],
            $order['province'],
            $order['district'],
            (int)($first['product_id'] ?? 1),
            (string)($first['size'] ?? 'Size 2 (2-3T)'),
            (int)($first['quantity'] ?? 1)
        );
    }

    /**
     * Nhân viên duyệt đơn qua sp_lnkt_cap_nhat_trang_thai_don_hang
     * Bắt lỗi ngoại lệ SQLSTATE 45000 nếu đơn đã hoàn tất/đã hủy hoặc nhân viên không đủ quyền.
     */
    public function capNhatTrangThai(int $maDonHang, int $maNhanVien, string $trangThaiMoi): void {
        $validStatuses = ['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy'];
        if (!in_array($trangThaiMoi, $validStatuses, true)) {
            throw new InvalidArgumentException("Trạng thái đơn hàng mới không hợp lệ: {$trangThaiMoi}");
        }

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lnkt_cap_nhat_trang_thai_don_hang(:ma_don, :ma_nv, :trang_thai)");
                $stmt->execute([
                    ':ma_don'     => $maDonHang,
                    ':ma_nv'      => $maNhanVien,
                    ':trang_thai' => $trangThaiMoi
                ]);
                $stmt->closeCursor();
            } catch (PDOException $e) {
                if (($e->errorInfo[0] ?? '') === '45000') {
                    $cleanMsg = preg_replace('/^SQLSTATE\[45000\]:.*?:\s*\d+\s*/u', '', (string)($e->errorInfo[2] ?? $e->getMessage()));
                    throw new DomainException(trim($cleanMsg ?: 'Không thể cập nhật trạng thái đơn hàng!'), 0, $e);
                }
                error_log("Lỗi cập nhật trạng thái đơn: " . $e->getMessage());
            }
        }
    }

    public function updateStatus(int $orderId, string $newStatus, int $handledBy): bool {
        $this->capNhatTrangThai($orderId, $handledBy, $newStatus);
        return true;
    }

    public function getOrderById(int $orderId): ?array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM lnkt_don_hang WHERE ma_don_hang = :id");
                $stmt->execute([':id' => $orderId]);
                $order = $stmt->fetch();
                if ($order) {
                    $itemStmt = $this->db->prepare("SELECT ct.*, sp.ten_san_pham, sp.duong_dan_anh 
                                                    FROM tgb_chi_tiet_don_hang ct 
                                                    JOIN lbn_san_pham sp ON ct.ma_san_pham = sp.ma_san_pham 
                                                    WHERE ct.ma_don_hang = :id");
                    $itemStmt->execute([':id' => $orderId]);
                    $order['items'] = $itemStmt->fetchAll();
                    return $order;
                }
            } catch (PDOException $e) {}
        }
        return null;
    }
}
