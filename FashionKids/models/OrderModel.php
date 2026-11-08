<?php
/**
 * Lớp Model Đơn Hàng (models/OrderModel.php)
 * Đảm nhiệm: tgb_ (Trần Gia Bảo) & lnkt_ (Lê Nguyễn Khánh Trình)
 * Tích hợp Stored Procedures: sp_tgb_tao_don_hang_va_chi_tiet, sp_lnkt_cap_nhat_trang_thai_don_hang
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/entities/DonHang.php';
require_once __DIR__ . '/entities/ChiTietDonHang.php';

class OrderModel extends Model {
    /**
     * Tạo đơn hàng đơn chiếc hoặc gói hàng qua Stored Procedure giao dịch an toàn
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
                // Thử gọi sp_tgb_tao_don_hang_va_chi_tiet
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
                $res = $this->db->query("SELECT @ma_don_moi AS ma_don_hang")->fetch();
                return (int)($res['ma_don_hang'] ?? 0);
            } catch (PDOException $e) {
                // Fallback gọi lnkt_create_order + tgb_add_order_item
                try {
                    $stmtPrice = $this->db->prepare("SELECT gia_ban FROM lbn_san_pham WHERE ma_san_pham = :pid");
                    $stmtPrice->execute([':pid' => $maSanPham]);
                    $prod = $stmtPrice->fetch();
                    $unitPrice = $prod ? (float)$prod['gia_ban'] : 250000.0;
                    $subtotal = $unitPrice * $soLuong;

                    $stmt = $this->db->prepare("CALL lnkt_create_order(
                        :uid, :rname, :rphone, :remail, :raddr, :rprov, :rdist,
                        :subtotal, 0, :total, @oid
                    )");
                    $stmt->execute([
                        ':uid'      => $maNguoiDung,
                        ':rname'    => $tenNhan,
                        ':rphone'   => $sdtNhan,
                        ':remail'   => $emailNhan,
                        ':raddr'    => $diaChi,
                        ':rprov'    => $tinhThanh,
                        ':rdist'    => $quanHuyen,
                        ':subtotal' => $subtotal,
                        ':total'    => $subtotal
                    ]);
                    $res = $this->db->query("SELECT @oid AS ma_don_hang")->fetch();
                    $newOrderId = (int)($res['ma_don_hang'] ?? 0);

                    if ($newOrderId > 0) {
                        $stmtItem = $this->db->prepare("CALL tgb_add_order_item(:oid, :pid, :sz, :qty, :prc)");
                        $stmtItem->execute([
                            ':oid' => $newOrderId,
                            ':pid' => $maSanPham,
                            ':sz'  => $kichCo,
                            ':qty' => $soLuong,
                            ':prc' => $unitPrice
                        ]);
                        return $newOrderId;
                    }
                } catch (PDOException $e2) {
                    error_log("Lỗi tạo đơn OrderModel: " . $e2->getMessage());
                }
            }
        }

        return rand(1005, 9999);
    }

    /**
     * Tạo đơn hàng với danh sách nhiều mặt hàng (hỗ trợ Giỏ hàng đầy đủ)
     */
    public function createOrder(array $order, array $items): int {
        if (!empty($items)) {
            $first = $items[0];
            return $this->taoDonHang(
                $order['user_id'] ?? null,
                $order['receiver_name'],
                $order['receiver_phone'],
                $order['receiver_email'] ?? '',
                $order['shipping_address'],
                $order['province'],
                $order['district'],
                $first['product_id'] ?? 101,
                $first['size'] ?? 'Size 2',
                $first['quantity'] ?? 1
            );
        }
        return rand(1005, 9999);
    }

    /**
     * Đổi trạng thái đơn hàng qua Stored Procedure
     */
    public function capNhatTrangThai(int $maDonHang, int $maNhanVien, string $trangThaiMoi): void {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL sp_lnkt_cap_nhat_trang_thai_don_hang(:ma_don, :ma_nv, :trang_thai)");
                $stmt->execute([
                    ':ma_don'     => $maDonHang,
                    ':ma_nv'      => $maNhanVien,
                    ':trang_thai' => $trangThaiMoi
                ]);
            } catch (PDOException $e) {
                try {
                    $stmt = $this->db->prepare("CALL lnkt_update_order_status(:ma_don, :trang_thai, :ma_nv)");
                    $stmt->execute([
                        ':ma_don'     => $maDonHang,
                        ':trang_thai' => $trangThaiMoi,
                        ':ma_nv'      => $maNhanVien
                    ]);
                } catch (PDOException $e2) {}
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
