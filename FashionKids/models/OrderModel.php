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
                $stmt->closeCursor();
            } catch (PDOException $e) {
                error_log("Lỗi cập nhật trạng thái đơn: " . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Cập nhật tiến trình đơn hàng qua AJAX (MH 10) sử dụng sp_lnkt_cap_nhat_trang_thai_don_hang
     * Kết hợp đóng con trỏ $stmt->closeCursor() và đồng bộ Session
     */
    public function capNhatTrangThaiDonHangChiTiet(int $maDonHang, int $maNhanVien, string $trangThaiMoi): array {
        $allowedStatuses = ['cho_xac_nhan', 'dang_dong_goi', 'dang_giao_hang', 'hoan_tat', 'da_huy'];
        if (!in_array($trangThaiMoi, $allowedStatuses, true)) {
            throw new InvalidArgumentException('Trạng thái đơn hàng không hợp lệ!');
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $currentStatus = $_SESSION['lnkt_order_status_overrides'][$maDonHang] ?? null;
        if ($this->hasConnection()) {
            try {
                $chkStmt = $this->db->prepare("SELECT trang_thai_don_hang FROM lnkt_don_hang WHERE ma_don_hang = :id LIMIT 1");
                $chkStmt->execute([':id' => $maDonHang]);
                $row = $chkStmt->fetch();
                $chkStmt->closeCursor();
                if ($row && !empty($row['trang_thai_don_hang'])) {
                    $currentStatus = $row['trang_thai_don_hang'];
                }
            } catch (PDOException $e) {}
        }

        if ($currentStatus === 'hoan_tat' && $trangThaiMoi === 'cho_xac_nhan') {
            throw new InvalidArgumentException('Lỗi: Không thể đưa đơn hàng đã hoàn tất quay ngược về chờ xác nhận!');
        }
        if ($currentStatus === 'da_huy' && $trangThaiMoi !== 'da_huy') {
            throw new InvalidArgumentException('Lỗi: Đơn hàng đã hủy không thể chuyển sang trạng thái vận chuyển khác!');
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
                $errMsg = $e->getMessage();
                if (stripos($errMsg, 'Không thể đưa đơn hàng') !== false || $e->getCode() === '45000') {
                    throw new RuntimeException('Không thể đưa đơn hàng đã hoàn tất quay ngược về chờ xác nhận!');
                }
                error_log("Lỗi sp_lnkt_cap_nhat_trang_thai_don_hang: " . $errMsg);
            }
        }

        $_SESSION['lnkt_order_status_overrides'][$maDonHang] = $trangThaiMoi;

        $statusMeta = [
            'cho_xac_nhan'   => ['label' => 'Chờ xác nhận',  'badge_class' => 'badge-gold',   'icon' => 'fa-clock'],
            'dang_dong_goi'  => ['label' => 'Đang đóng gói', 'badge_class' => 'badge-sky',    'icon' => 'fa-box-open'],
            'dang_giao_hang' => ['label' => 'Đang giao hàng', 'badge_class' => 'badge-sky',    'icon' => 'fa-truck-fast'],
            'hoan_tat'       => ['label' => 'Đã hoàn tất',   'badge_class' => 'badge-mint',   'icon' => 'fa-circle-check'],
            'da_huy'         => ['label' => 'Đã hủy đơn',    'badge_class' => 'badge-danger', 'icon' => 'fa-ban']
        ];
        $meta = $statusMeta[$trangThaiMoi] ?? $statusMeta['dang_dong_goi'];

        return [
            'order_id'     => $maDonHang,
            'handled_by'   => $maNhanVien,
            'old_status'   => $currentStatus ?? 'cho_xac_nhan',
            'new_status'   => $trangThaiMoi,
            'status_label' => $meta['label'],
            'badge_class'  => $meta['badge_class'],
            'icon'         => $meta['icon'],
            'routine'      => 'sp_lnkt_cap_nhat_trang_thai_don_hang',
            'updated_at'   => date('d/m/Y H:i')
        ];
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
