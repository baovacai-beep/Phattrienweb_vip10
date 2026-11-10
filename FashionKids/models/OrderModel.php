<?php
/**
 * Lớp Model Đơn Hàng (models/OrderModel.php)
 * Đảm nhiệm: tgb_ (Trần Gia Bảo) & lnkt_ (Lê Nguyễn Khánh Trình)
 * Khớp chuẩn 100% với CSDL phpMyAdmin (lnkt_don_hang & tgb_chi_tiet_don_hang)
 * Tích hợp ACID Transaction, Row Locking (FOR UPDATE) và các Stored Routines:
 *  - sp_tgb_tao_don_hang_va_chi_tiet
 *  - sp_lnkt_cap_nhat_trang_thai_don_hang
 *  - fn_hgb_tinh_phan_tram_giam_gia
 *  - sp_hgb_tich_diem_nang_hang
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/entities/DonHang.php';
require_once __DIR__ . '/entities/ChiTietDonHang.php';
require_once __DIR__ . '/entities/ThanhToanVietQR.php';
require_once __DIR__ . '/entities/ThanhToanCOD.php';

class OrderModel extends Model {
    private static bool $schemaChecked = false;

    public const VALID_STATUSES = ['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_tat', 'da_huy'];

    public function __construct() {
        parent::__construct();
        $this->ensureOrderSchema();
    }

    /**
     * Đảm bảo bảng lnkt_don_hang có đủ các cột thanh toán, ghi chú & thời gian cập nhật.
     * Dùng SHOW COLUMNS trước khi ALTER TABLE để tương thích cả MySQL 8 và MariaDB.
     */
    public function ensureOrderSchema(): void {
        if (self::$schemaChecked || !$this->hasConnection()) {
            return;
        }
        self::$schemaChecked = true;
        try {
            $cols = [];
            $stmt = $this->db->query("SHOW COLUMNS FROM `lnkt_don_hang`");
            foreach ($stmt->fetchAll() as $col) {
                if (!empty($col['Field'])) {
                    $cols[$col['Field']] = true;
                }
            }
            $stmt->closeCursor();

            if (!isset($cols['phuong_thuc_thanh_toan'])) {
                $this->db->exec("ALTER TABLE `lnkt_don_hang` ADD COLUMN `phuong_thuc_thanh_toan` ENUM('cod', 'vietqr') NOT NULL DEFAULT 'cod' AFTER `tong_tien_thanh_toan`");
            }
            if (!isset($cols['trang_thai_thanh_toan'])) {
                $this->db->exec("ALTER TABLE `lnkt_don_hang` ADD COLUMN `trang_thai_thanh_toan` ENUM('chua_thanh_toan', 'cho_chuyen_khoan', 'da_thanh_toan') NOT NULL DEFAULT 'chua_thanh_toan' AFTER `phuong_thuc_thanh_toan`");
            }
            if (!isset($cols['ghi_chu'])) {
                $this->db->exec("ALTER TABLE `lnkt_don_hang` ADD COLUMN `ghi_chu` VARCHAR(255) NULL AFTER `trang_thai_thanh_toan`");
            }
            if (!isset($cols['ngay_cap_nhat'])) {
                $this->db->exec("ALTER TABLE `lnkt_don_hang` ADD COLUMN `ngay_cap_nhat` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `ngay_dat_hang`");
            }
        } catch (PDOException $e) {
            error_log('[lnkt] ensureOrderSchema: ' . $e->getMessage());
        }
    }

    /**
     * Chuẩn hóa alias trạng thái cũ về đúng ENUM chuẩn của CSDL lnkt_don_hang
     */
    public function normalizeStatus(string $status): string {
        $s = strtolower(trim($status));
        $aliases = [
            'cho_xac_nhan'   => 'cho_duyet',
            'dang_dong_goi'  => 'da_xac_nhan',
            'dang_giao_hang' => 'dang_giao',
        ];
        return $aliases[$s] ?? $s;
    }

    /**
     * Siêu dữ liệu hiển thị cho 5 trạng thái đơn hàng
     */
    public function getStatusMeta(string $status): array {
        $normalized = $this->normalizeStatus($status);
        $map = [
            'cho_duyet' => [
                'code'        => 'cho_duyet',
                'step'        => 1,
                'label'       => 'Chờ duyệt',
                'badge_class' => 'badge-gold',
                'icon'        => 'fa-clock',
                'description' => 'Đơn hàng mới tạo thành công, đang chờ nhân viên tiếp nhận'
            ],
            'da_xac_nhan' => [
                'code'        => 'da_xac_nhan',
                'step'        => 2,
                'label'       => 'Đã xác nhận',
                'badge_class' => 'badge-sky',
                'icon'        => 'fa-box-open',
                'description' => 'Nhân viên đã kiểm kho và đóng gói sản phẩm'
            ],
            'dang_giao' => [
                'code'        => 'dang_giao',
                'step'        => 3,
                'label'       => 'Đang giao',
                'badge_class' => 'badge-sky',
                'icon'        => 'fa-truck-fast',
                'description' => 'Bưu tá đang trên đường giao tận tay phụ huynh'
            ],
            'hoan_tat' => [
                'code'        => 'hoan_tat',
                'step'        => 4,
                'label'       => 'Hoàn tất',
                'badge_class' => 'badge-mint',
                'icon'        => 'fa-circle-check',
                'description' => 'Giao hàng thành công & đã cộng điểm VIP cho tài khoản'
            ],
            'da_huy' => [
                'code'        => 'da_huy',
                'step'        => 5,
                'label'       => 'Đã hủy',
                'badge_class' => 'badge-danger',
                'icon'        => 'fa-ban',
                'description' => 'Đơn hàng đã hủy & hoàn trả tồn kho tự động'
            ],
        ];
        return $map[$normalized] ?? $map['cho_duyet'];
    }

    public function formatTrackingCode(int $orderId): string {
        return 'FK-ORD-' . str_pad((string)$orderId, 6, '0', STR_PAD_LEFT);
    }

    private function cleanSqlErrorMessage(PDOException $e): string {
        $msg = $e->getMessage();
        $msg = preg_replace('/^SQLSTATE\[[^\]]+\]:.*?:\s*\d+\s*/u', '', $msg);
        return trim((string)$msg) ?: 'Không thể xử lý giao dịch đơn hàng lúc này.';
    }

    /**
     * Tạo đơn hàng 1 sản phẩm trực tiếp qua sp_tgb_tao_don_hang_va_chi_tiet (giữ tương thích API cũ)
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
        if (!$this->hasConnection()) {
            throw new RuntimeException('Hệ thống chưa kết nối được cơ sở dữ liệu đơn hàng.');
        }

        $safeSize = mb_substr(trim($kichCo) ?: 'Size 2 (2-3T)', 0, 20, 'UTF-8');
        try {
            $sql = "CALL sp_tgb_tao_don_hang_va_chi_tiet(
                :ma_user, :ten_nhan, :sdt, :email, :dia_chi, :tinh, :huyen, :ma_sp, :kich_co, :sl, @ma_don_moi
            )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':ma_user'  => $maNguoiDung,
                ':ten_nhan' => trim($tenNhan),
                ':sdt'      => trim($sdtNhan),
                ':email'    => trim($emailNhan),
                ':dia_chi'  => trim($diaChi),
                ':tinh'     => trim($tinhThanh),
                ':huyen'    => trim($quanHuyen),
                ':ma_sp'    => $maSanPham,
                ':kich_co'  => $safeSize,
                ':sl'       => $soLuong
            ]);
            $stmt->closeCursor();

            $res = $this->db->query("SELECT @ma_don_moi AS ma_don_hang")->fetch();
            $orderId = (int)($res['ma_don_hang'] ?? 0);
            if ($orderId <= 0) {
                throw new RuntimeException('Không nhận được mã đơn hàng mới từ hệ thống.');
            }
            return $orderId;
        } catch (PDOException $e) {
            error_log("Lỗi tạo đơn qua sp_tgb_tao_don_hang_va_chi_tiet: " . $e->getMessage());
            throw new RuntimeException($this->cleanSqlErrorMessage($e), 0, $e);
        }
    }

    public function createOrder(array $order, array $items): int {
        $result = $this->taoDonHangHoanChinh($order, $items);
        return (int)$result['ma_don_hang'];
    }

    /**
     * Luồng tạo đơn hàng hoàn chỉnh chuẩn ACID cho cả giỏ hàng 1 sản phẩm và nhiều sản phẩm.
     * - Mọi đơn giá, tồn kho và % giảm giá VIP được tính hoàn toàn phía Server từ DB.
     * - 1 sản phẩm: gọi sp_tgb_tao_don_hang_va_chi_tiet (NGOÀI PDO transaction vì SP tự mở transaction).
     * - Nhiều sản phẩm: chạy PDO transaction khóa hàng FOR UPDATE theo thứ tự tăng dần mã SP tránh deadlock.
     */
    public function taoDonHangHoanChinh(array $payload, array $items): array {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu để khởi tạo đơn hàng.');
        }

        $userId   = !empty($payload['user_id']) ? (int)$payload['user_id'] : null;
        $name     = trim((string)($payload['receiver_name'] ?? ''));
        $phone    = trim((string)($payload['receiver_phone'] ?? ''));
        $email    = trim((string)($payload['receiver_email'] ?? ''));
        $address  = trim((string)($payload['shipping_address'] ?? ''));
        $province = trim((string)($payload['province'] ?? ''));
        $district = trim((string)($payload['district'] ?? ''));
        $note     = mb_substr(trim((string)($payload['note'] ?? '')), 0, 255, 'UTF-8');

        $rawMethod = strtolower(trim((string)($payload['payment_method'] ?? 'cod')));
        $paymentMethod = ($rawMethod === 'vietqr') ? 'vietqr' : 'cod';
        $paymentStatus = ($paymentMethod === 'vietqr') ? 'cho_chuyen_khoan' : 'chua_thanh_toan';

        if ($name === '' || $phone === '' || $address === '') {
            throw new InvalidArgumentException('Lỗi [TGB_ERR_01]: Tên người nhận, SĐT và địa chỉ giao hàng không được để trống!');
        }
        if ($province === '' || $district === '') {
            throw new InvalidArgumentException('Vui lòng chọn đầy đủ Tỉnh / Thành phố và Quận / Huyện giao hàng!');
        }
        if (empty($items)) {
            throw new InvalidArgumentException('Giỏ hàng của bạn đang trống, vui lòng chọn sản phẩm trước khi đặt hàng!');
        }

        // Chuẩn hóa danh sách dòng hàng & gom tổng số lượng theo từng ma_san_pham (nhiều size cùng 1 sản phẩm)
        $normalizedItems = [];
        $qtyByProduct = [];
        foreach ($items as $it) {
            $pid  = (int)($it['product_id'] ?? $it['ma_san_pham'] ?? 0);
            $qty  = (int)($it['quantity'] ?? $it['so_luong'] ?? 0);
            $size = mb_substr(trim((string)($it['size'] ?? $it['kich_co_chon'] ?? 'Size 2 (2-3T)')), 0, 20, 'UTF-8');
            if ($size === '') {
                $size = 'Size 2 (2-3T)';
            }
            if ($pid <= 0 || $qty <= 0) {
                throw new InvalidArgumentException('Lỗi [TGB_ERR_02]: Số lượng sản phẩm đặt mua phải từ 1 trở lên!');
            }
            $normalizedItems[] = [
                'product_id' => $pid,
                'quantity'   => $qty,
                'size'       => $size,
            ];
            $qtyByProduct[$pid] = ($qtyByProduct[$pid] ?? 0) + $qty;
        }
        ksort($qtyByProduct);

        $orderId = 0;

        if (count($normalizedItems) === 1) {
            // 1 dòng sản phẩm -> Dùng trực tiếp Stored Procedure sp_tgb_tao_don_hang_va_chi_tiet ngoài PDO tx
            $single = $normalizedItems[0];
            $orderId = $this->taoDonHang(
                $userId,
                $name,
                $phone,
                $email,
                $address,
                $province,
                $district,
                $single['product_id'],
                $single['size'],
                $single['quantity']
            );

            $updStmt = $this->db->prepare("
                UPDATE `lnkt_don_hang`
                SET `phuong_thuc_thanh_toan` = :pm,
                    `trang_thai_thanh_toan`  = :ps,
                    `ghi_chu`                = :note,
                    `ngay_cap_nhat`          = NOW()
                WHERE `ma_don_hang` = :id
            ");
            $updStmt->execute([
                ':pm'   => $paymentMethod,
                ':ps'   => $paymentStatus,
                ':note' => ($note !== '' ? $note : null),
                ':id'   => $orderId,
            ]);
        } else {
            // Nhiều dòng sản phẩm -> Explicit ACID Transaction + FOR UPDATE row locking
            try {
                $this->db->beginTransaction();

                $lockStmt = $this->db->prepare("
                    SELECT `ma_san_pham`, `ten_san_pham`, `gia_ban`, `so_luong_ton_kho`
                    FROM `lbn_san_pham`
                    WHERE `ma_san_pham` = :id
                    FOR UPDATE
                ");
                $lockedProducts = [];
                foreach ($qtyByProduct as $pid => $neededQty) {
                    $lockStmt->execute([':id' => $pid]);
                    $prodRow = $lockStmt->fetch();
                    $lockStmt->closeCursor();

                    if (!$prodRow) {
                        throw new RuntimeException('Lỗi [TGB_ERR_03]: Sản phẩm khách chọn không tồn tại!');
                    }
                    $available = (int)$prodRow['so_luong_ton_kho'];
                    if ($available < $neededQty) {
                        throw new RuntimeException(
                            'Lỗi [TGB_ERR_04]: Số lượng hàng trong kho của "' . $prodRow['ten_san_pham'] . '" chỉ còn ' . $available . ' sản phẩm, không đủ đáp ứng (' . $neededQty . ')!'
                        );
                    }
                    $lockedProducts[$pid] = $prodRow;
                }

                // Tính tổng tạm tính hoàn toàn theo giá bán trong DB
                $subtotal = 0.0;
                foreach ($normalizedItems as $line) {
                    $unitPrice = (float)$lockedProducts[$line['product_id']]['gia_ban'];
                    $subtotal += $unitPrice * $line['quantity'];
                }

                // Gọi Stored Function fn_hgb_tinh_phan_tram_giam_gia tính % VIP tự động từ DB
                $discountPct = 0.0;
                if ($userId !== null && $userId > 0) {
                    $vipStmt = $this->db->prepare("SELECT `fn_hgb_tinh_phan_tram_giam_gia`(:uid) AS phan_tram");
                    $vipStmt->execute([':uid' => $userId]);
                    $vipRes = $vipStmt->fetch();
                    $vipStmt->closeCursor();
                    $discountPct = (float)($vipRes['phan_tram'] ?? 0.0);
                }

                $discountAmount = round(($subtotal * $discountPct) / 100.0, 2);
                $totalAmount    = max(0.0, round($subtotal - $discountAmount, 2));

                $insOrder = $this->db->prepare("
                    INSERT INTO `lnkt_don_hang` (
                        `ma_nguoi_dung`, `ma_nhan_vien_xu_ly`, `ten_nguoi_nhan`,
                        `so_dien_thoai_nhan`, `thu_dien_tu_nhan`, `dia_chi_giao_hang`,
                        `tinh_thanh_pho`, `quan_huyen`, `tien_hang_tam_tinh`,
                        `tien_giam_gia`, `tong_tien_thanh_toan`,
                        `phuong_thuc_thanh_toan`, `trang_thai_thanh_toan`, `ghi_chu`,
                        `trang_thai_don_hang`, `ngay_dat_hang`, `ngay_cap_nhat`
                    ) VALUES (
                        :uid, NULL, :ten,
                        :sdt, :email, :diachi,
                        :tinh, :huyen, :tam_tinh,
                        :giam_gia, :tong_tien,
                        :pm, :ps, :note,
                        'cho_duyet', NOW(), NOW()
                    )
                ");
                $insOrder->execute([
                    ':uid'       => $userId,
                    ':ten'       => $name,
                    ':sdt'       => $phone,
                    ':email'     => $email,
                    ':diachi'    => $address,
                    ':tinh'      => $province,
                    ':huyen'     => $district,
                    ':tam_tinh'  => $subtotal,
                    ':giam_gia'  => $discountAmount,
                    ':tong_tien' => $totalAmount,
                    ':pm'        => $paymentMethod,
                    ':ps'        => $paymentStatus,
                    ':note'      => ($note !== '' ? $note : null),
                ]);

                $orderId = (int)$this->db->lastInsertId();

                $insDetail = $this->db->prepare("
                    INSERT INTO `tgb_chi_tiet_don_hang` (
                        `ma_don_hang`, `ma_san_pham`, `kich_co_chon`, `so_luong`, `don_gia_mua`
                    ) VALUES (
                        :order_id, :pid, :size, :qty, :price
                    )
                ");
                foreach ($normalizedItems as $line) {
                    $insDetail->execute([
                        ':order_id' => $orderId,
                        ':pid'      => $line['product_id'],
                        ':size'     => $line['size'],
                        ':qty'      => $line['quantity'],
                        ':price'    => (float)$lockedProducts[$line['product_id']]['gia_ban'],
                    ]);
                }

                $deductStmt = $this->db->prepare("
                    UPDATE `lbn_san_pham`
                    SET `so_luong_ton_kho` = `so_luong_ton_kho` - :qty
                    WHERE `ma_san_pham` = :pid
                ");
                foreach ($qtyByProduct as $pid => $deductQty) {
                    $deductStmt->execute([
                        ':qty' => $deductQty,
                        ':pid' => $pid,
                    ]);
                }

                $this->db->commit();
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                if ($e instanceof PDOException) {
                    throw new RuntimeException($this->cleanSqlErrorMessage($e), 0, $e);
                }
                throw $e;
            }
        }

        // Xóa session stock override cũ nếu có để UI đọc tồn kho thật từ DB
        if (isset($_SESSION['lbn_stock_overrides']) && is_array($_SESSION['lbn_stock_overrides'])) {
            foreach (array_keys($qtyByProduct) as $pid) {
                unset($_SESSION['lbn_stock_overrides'][$pid]);
            }
        }

        // Lấy lại toàn bộ thông tin đơn hàng chính xác đã lưu trong DB
        $orderRow = $this->getOrderById($orderId);
        $finalTotal    = (float)($orderRow['tong_tien_thanh_toan'] ?? 0);
        $finalSubtotal = (float)($orderRow['tien_hang_tam_tinh'] ?? $finalTotal);
        $finalDiscount = (float)($orderRow['tien_giam_gia'] ?? 0);
        $appliedPct    = $finalSubtotal > 0 ? round(($finalDiscount / $finalSubtotal) * 100, 1) : 0.0;

        $paymentProvider = ($paymentMethod === 'vietqr') ? new ThanhToanVietQR() : new ThanhToanCOD();
        $paymentResult   = $paymentProvider->thucHienThanhToan($orderId, $finalTotal);
        $qrUrl           = $paymentResult['qr_image'] ?? null;

        return [
            'ma_don_hang'           => $orderId,
            'ma_van_don'            => $this->formatTrackingCode($orderId),
            'tien_hang_tam_tinh'    => $finalSubtotal,
            'phan_tram_giam'        => $appliedPct,
            'tien_giam_vip'         => $finalDiscount,
            'tong_tien'             => $finalTotal,
            'phuong_thuc'           => $paymentMethod,
            'trang_thai_thanh_toan' => $paymentStatus,
            'qr_url'                => $qrUrl,
            'payment_result'        => $paymentResult,
            'order'                 => $orderRow,
        ];
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
                    ':trang_thai' => $this->normalizeStatus($trangThaiMoi)
                ]);
                $stmt->closeCursor();
            } catch (PDOException $e) {
                error_log("Lỗi cập nhật trạng thái đơn: " . $e->getMessage());
                throw new RuntimeException($this->cleanSqlErrorMessage($e), 0, $e);
            }
        }
    }

    /**
     * Cập nhật tiến trình đơn hàng qua AJAX (MH 10) sử dụng sp_lnkt_cap_nhat_trang_thai_don_hang
     * Tự động tích điểm 10.000đ = 1 điểm & nâng hạng VIP khi đạt 'hoan_tat', hoàn kho khi 'da_huy'.
     */
    public function capNhatTrangThaiDonHangChiTiet(int $maDonHang, int $maNhanVien, string $trangThaiMoi): array {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Chưa kết nối cơ sở dữ liệu đơn hàng.');
        }

        $normalizedNew = $this->normalizeStatus($trangThaiMoi);
        if (!in_array($normalizedNew, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException('Trạng thái đơn hàng không hợp lệ! Chỉ chấp nhận: Chờ duyệt, Đã xác nhận, Đang giao, Hoàn tất, Đã hủy.');
        }

        $chkStmt = $this->db->prepare("SELECT `trang_thai_don_hang`, `ma_nguoi_dung`, `tong_tien_thanh_toan`, `phuong_thuc_thanh_toan` FROM `lnkt_don_hang` WHERE `ma_don_hang` = :id LIMIT 1");
        $chkStmt->execute([':id' => $maDonHang]);
        $existing = $chkStmt->fetch();
        $chkStmt->closeCursor();

        if (!$existing) {
            throw new RuntimeException('Lỗi [LNKT_ERR_04]: Đơn hàng #' . $maDonHang . ' không tồn tại trên hệ thống!');
        }

        $currentStatus = $this->normalizeStatus((string)$existing['trang_thai_don_hang']);
        if (in_array($currentStatus, ['hoan_tat', 'da_huy'], true)) {
            throw new RuntimeException('Lỗi [LNKT_ERR_05]: Đơn hàng đã hoàn tất hoặc đã hủy không thể chỉnh sửa trạng thái!');
        }

        $pointsAdded = 0;
        $orderUserId = !empty($existing['ma_nguoi_dung']) ? (int)$existing['ma_nguoi_dung'] : 0;
        if ($normalizedNew === 'hoan_tat' && $orderUserId > 0) {
            $pointsAdded = (int)floor(((float)$existing['tong_tien_thanh_toan']) / 10000);
        }

        try {
            $stmt = $this->db->prepare("CALL sp_lnkt_cap_nhat_trang_thai_don_hang(:ma_don, :ma_nv, :trang_thai)");
            $stmt->execute([
                ':ma_don'     => $maDonHang,
                ':ma_nv'      => $maNhanVien,
                ':trang_thai' => $normalizedNew
            ]);
            $stmt->closeCursor();

            if ($normalizedNew === 'hoan_tat') {
                $payUpd = $this->db->prepare("UPDATE `lnkt_don_hang` SET `trang_thai_thanh_toan` = 'da_thanh_toan', `ngay_cap_nhat` = NOW() WHERE `ma_don_hang` = :id");
                $payUpd->execute([':id' => $maDonHang]);
            } else {
                $tsUpd = $this->db->prepare("UPDATE `lnkt_don_hang` SET `ngay_cap_nhat` = NOW() WHERE `ma_don_hang` = :id");
                $tsUpd->execute([':id' => $maDonHang]);
            }
        } catch (PDOException $e) {
            error_log("Lỗi sp_lnkt_cap_nhat_trang_thai_don_hang: " . $e->getMessage());
            throw new RuntimeException($this->cleanSqlErrorMessage($e), 0, $e);
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        unset($_SESSION['lnkt_order_status_overrides'][$maDonHang], $_SESSION['lbn_stock_overrides']);

        $updatedOrder = $this->getOrderById($maDonHang);
        $meta = $this->getStatusMeta($normalizedNew);

        $customerTier = null;
        $customerPoints = null;
        if ($orderUserId > 0) {
            $uStmt = $this->db->prepare("
                SELECT u.diem_tich_luy, COALESCE(h.ten_hang, 'Đồng (Bronze)') AS ten_hang
                FROM hgb_nguoi_dung u
                LEFT JOIN hgb_hang_thanh_vien h ON u.ma_hang_thanh_vien = h.ma_hang
                WHERE u.ma_nguoi_dung = :uid LIMIT 1
            ");
            $uStmt->execute([':uid' => $orderUserId]);
            $uInfo = $uStmt->fetch();
            $uStmt->closeCursor();
            if ($uInfo) {
                $customerPoints = (int)$uInfo['diem_tich_luy'];
                $customerTier   = (string)$uInfo['ten_hang'];
            }
        }

        return [
            'order_id'        => $maDonHang,
            'ma_van_don'      => $this->formatTrackingCode($maDonHang),
            'handled_by'      => $maNhanVien,
            'handled_by_name' => $updatedOrder['nhan_vien_phu_trach'] ?? ($_SESSION['user']['ho_va_ten'] ?? 'Nhân viên FashionKids'),
            'old_status'      => $currentStatus,
            'new_status'      => $normalizedNew,
            'status_label'    => $meta['label'],
            'badge_class'     => $meta['badge_class'],
            'icon'            => $meta['icon'],
            'points_added'    => $pointsAdded,
            'customer_points' => $customerPoints,
            'customer_tier'   => $customerTier,
            'counts'          => $this->getOrderStatusCounts(),
            'routine'         => 'sp_lnkt_cap_nhat_trang_thai_don_hang',
            'updated_at'      => date('d/m/Y H:i')
        ];
    }

    public function updateStatus(int $orderId, string $newStatus, int $handledBy): bool {
        $this->capNhatTrangThai($orderId, $handledBy, $newStatus);
        return true;
    }

    /**
     * Hydrate thông tin đơn hàng đầy đủ kèm chi tiết sản phẩm, nhân viên xử lý & trạng thái đánh giá
     */
    public function getOrderById(int $orderId): ?array {
        if (!$this->hasConnection() || $orderId <= 0) {
            return null;
        }
        try {
            $stmt = $this->db->prepare("
                SELECT dh.*,
                       nv.ho_va_ten AS nhan_vien_phu_trach,
                       kh.diem_tich_luy AS khach_hang_diem,
                       COALESCE(h.ten_hang, 'Đồng (Bronze)') AS khach_hang_hang,
                       COALESCE(h.phan_tram_giam_gia, 0) AS khach_hang_giam_gia
                FROM lnkt_don_hang dh
                LEFT JOIN hgb_nguoi_dung nv ON dh.ma_nhan_vien_xu_ly = nv.ma_nguoi_dung
                LEFT JOIN hgb_nguoi_dung kh ON dh.ma_nguoi_dung = kh.ma_nguoi_dung
                LEFT JOIN hgb_hang_thanh_vien h ON kh.ma_hang_thanh_vien = h.ma_hang
                WHERE dh.ma_don_hang = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch();
            $stmt->closeCursor();

            if (!$order) {
                return null;
            }

            $itemStmt = $this->db->prepare("
                SELECT ct.*, sp.ten_san_pham, sp.duong_dan_anh, sp.ma_danh_muc,
                       dg.ma_danh_gia, dg.so_sao_danh_gia, dg.noi_dung_danh_gia
                FROM tgb_chi_tiet_don_hang ct
                JOIN lbn_san_pham sp ON ct.ma_san_pham = sp.ma_san_pham
                LEFT JOIN tgb_danh_gia_san_pham dg
                    ON dg.ma_don_hang = ct.ma_don_hang
                   AND dg.ma_san_pham = ct.ma_san_pham
                   AND dg.ma_nguoi_dung = :uid
                WHERE ct.ma_don_hang = :id
                ORDER BY ct.ma_chi_tiet_don ASC
            ");
            $itemStmt->execute([
                ':id'  => $orderId,
                ':uid' => !empty($order['ma_nguoi_dung']) ? (int)$order['ma_nguoi_dung'] : 0
            ]);
            $items = $itemStmt->fetchAll();
            $itemStmt->closeCursor();

            foreach ($items as &$it) {
                $img = trim((string)($it['duong_dan_anh'] ?? ''));
                if ($img !== '' && strpos($img, '/') === false) {
                    $img = '/FashionKids/images/' . $img;
                }
                $it['image'] = $img !== '' ? $img : '/FashionKids/images/vay-cong-chua-hoa-cuc.jpg';
                $it['da_danh_gia'] = !empty($it['ma_danh_gia']);
            }
            unset($it);

            $order['items'] = $items;
            $order['ma_van_don'] = $this->formatTrackingCode($orderId);
            $order['status_meta'] = $this->getStatusMeta((string)($order['trang_thai_don_hang'] ?? 'cho_duyet'));

            $pm = strtolower((string)($order['phuong_thuc_thanh_toan'] ?? 'cod'));
            $order['phuong_thuc_thanh_toan'] = $pm === 'vietqr' ? 'vietqr' : 'cod';
            if ($order['phuong_thuc_thanh_toan'] === 'vietqr') {
                $qr = new ThanhToanVietQR();
                $res = $qr->thucHienThanhToan($orderId, (float)$order['tong_tien_thanh_toan']);
                $order['qr_url'] = $res['qr_image'];
            } else {
                $order['qr_url'] = null;
            }

            return $order;
        } catch (PDOException $e) {
            error_log('[lnkt] getOrderById: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tra cứu đơn hàng theo Mã vận đơn (#FK-ORD-000014 / 14 / FK14) hoặc Số điện thoại
     */
    public function findOrderForTracking(string $query): ?array {
        if (!$this->hasConnection()) {
            return null;
        }
        $clean = trim($query);
        if ($clean === '') {
            return null;
        }

        // 1. Thử khớp mã đơn số (VD: #FK-ORD-000014, FK-ORD-14, FK14, 14)
        $upper = strtoupper($clean);
        if (preg_match('/^(?:#?FK-?ORD-?|#?FK|#)?0*(\d+)$/i', $upper, $m)) {
            $id = (int)$m[1];
            if ($id > 0) {
                $byId = $this->getOrderById($id);
                if ($byId) {
                    return $byId;
                }
            }
        }

        // 2. Thử khớp theo SĐT (chỉ khi độ dài >= 8 số)
        $digitsOnly = preg_replace('/\D+/', '', $clean);
        if ($digitsOnly !== '' && strlen($digitsOnly) >= 8) {
            try {
                $stmt = $this->db->prepare("
                    SELECT `ma_don_hang`
                    FROM `lnkt_don_hang`
                    WHERE `so_dien_thoai_nhan` = :phone
                    ORDER BY `ma_don_hang` DESC
                    LIMIT 1
                ");
                $stmt->execute([':phone' => $digitsOnly]);
                $row = $stmt->fetch();
                $stmt->closeCursor();
                if ($row && !empty($row['ma_don_hang'])) {
                    return $this->getOrderById((int)$row['ma_don_hang']);
                }
            } catch (PDOException $e) {}
        }

        return null;
    }

    /**
     * Lấy danh sách đơn hàng gần nhất của một Khách hàng (hiển thị lịch sử nhanh trên Tra cứu đơn)
     */
    public function getOrdersByUserId(int $userId, int $limit = 10): array {
        if (!$this->hasConnection() || $userId <= 0) {
            return [];
        }
        try {
            $stmt = $this->db->prepare("
                SELECT dh.*,
                       (SELECT COUNT(*) FROM tgb_chi_tiet_don_hang ct WHERE ct.ma_don_hang = dh.ma_don_hang) AS item_count
                FROM lnkt_don_hang dh
                WHERE dh.ma_nguoi_dung = :uid
                ORDER BY dh.ma_don_hang DESC
                LIMIT :lim
            ");
            $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $stmt->closeCursor();

            foreach ($rows as &$r) {
                $r['ma_van_don'] = $this->formatTrackingCode((int)$r['ma_don_hang']);
                $r['status_meta'] = $this->getStatusMeta((string)$r['trang_thai_don_hang']);
            }
            unset($r);
            return $rows;
        } catch (PDOException $e) {
            error_log('[lnkt] getOrdersByUserId: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách đơn hàng phục vụ Cổng Nhân viên (MH 10) từ CSDL thực tế
     */
    public function getAllOrdersForStaff(string $statusFilter = 'all', string $keyword = ''): array {
        if (!$this->hasConnection()) {
            return [];
        }
        try {
            $sql = "
                SELECT dh.*,
                       nv.ho_va_ten AS nhan_vien_phu_trach,
                       COALESCE(GROUP_CONCAT(CONCAT(sp.ten_san_pham, ' (', ct.kich_co_chon, ' x', ct.so_luong, ')') SEPARATOR ' + '), 'Sản phẩm FashionKids') AS item_summary,
                       COUNT(ct.ma_chi_tiet_don) AS item_lines,
                       COALESCE(SUM(ct.so_luong), 0) AS total_qty
                FROM lnkt_don_hang dh
                LEFT JOIN hgb_nguoi_dung nv ON dh.ma_nhan_vien_xu_ly = nv.ma_nguoi_dung
                LEFT JOIN tgb_chi_tiet_don_hang ct ON dh.ma_don_hang = ct.ma_don_hang
                LEFT JOIN lbn_san_pham sp ON ct.ma_san_pham = sp.ma_san_pham
                WHERE 1=1
            ";
            $params = [];
            $normFilter = $this->normalizeStatus($statusFilter);
            if ($statusFilter !== 'all' && $statusFilter !== '' && in_array($normFilter, self::VALID_STATUSES, true)) {
                $sql .= " AND dh.trang_thai_don_hang = :st";
                $params[':st'] = $normFilter;
            }
            if (trim($keyword) !== '') {
                $kw = '%' . trim($keyword) . '%';
                $sql .= " AND (dh.ten_nguoi_nhan LIKE :kw1 OR dh.so_dien_thoai_nhan LIKE :kw2 OR CAST(dh.ma_don_hang AS CHAR) LIKE :kw3)";
                $params[':kw1'] = $kw;
                $params[':kw2'] = $kw;
                $params[':kw3'] = $kw;
            }
            $sql .= " GROUP BY dh.ma_don_hang ORDER BY dh.ma_don_hang DESC LIMIT 100";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            $stmt->closeCursor();

            foreach ($rows as &$r) {
                $r['ma_van_don'] = $this->formatTrackingCode((int)$r['ma_don_hang']);
                $r['status_meta'] = $this->getStatusMeta((string)($r['trang_thai_don_hang'] ?? 'cho_duyet'));
            }
            unset($r);
            return $rows;
        } catch (PDOException $e) {
            error_log('[lnkt] getAllOrdersForStaff: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số lượng đơn hàng theo 5 trạng thái từ lnkt_don_hang
     */
    public function getOrderStatusCounts(): array {
        $counts = [
            'all'         => 0,
            'cho_duyet'   => 0,
            'da_xac_nhan' => 0,
            'dang_giao'   => 0,
            'hoan_tat'    => 0,
            'da_huy'      => 0,
        ];
        if (!$this->hasConnection()) {
            return $counts;
        }
        try {
            $rows = $this->db->query("SELECT trang_thai_don_hang, COUNT(*) AS cnt FROM lnkt_don_hang GROUP BY trang_thai_don_hang")->fetchAll();
            foreach ($rows as $r) {
                $st = $this->normalizeStatus((string)$r['trang_thai_don_hang']);
                $cnt = (int)$r['cnt'];
                if (isset($counts[$st])) {
                    $counts[$st] += $cnt;
                }
                $counts['all'] += $cnt;
            }
        } catch (PDOException $e) {
            error_log('[lnkt] getOrderStatusCounts: ' . $e->getMessage());
        }
        return $counts;
    }

    /**
     * Polling đơn hàng mới cho Cổng Nhân viên (4 giây/lần)
     */
    public function getNewOrdersSince(int $lastOrderId): array {
        if (!$this->hasConnection()) {
            return ['new_orders' => [], 'max_order_id' => $lastOrderId, 'counts' => $this->getOrderStatusCounts()];
        }
        try {
            $maxStmt = $this->db->query("SELECT COALESCE(MAX(ma_don_hang), 0) AS max_id FROM lnkt_don_hang");
            $maxId = (int)($maxStmt->fetch()['max_id'] ?? 0);
            $maxStmt->closeCursor();

            $newOrders = [];
            if ($lastOrderId > 0 && $maxId > $lastOrderId) {
                $stmt = $this->db->prepare("
                    SELECT dh.*,
                           COALESCE(GROUP_CONCAT(CONCAT(sp.ten_san_pham, ' (', ct.kich_co_chon, ' x', ct.so_luong, ')') SEPARATOR ' + '), 'Sản phẩm FashionKids') AS item_summary,
                           COALESCE(SUM(ct.so_luong), 0) AS total_qty
                    FROM lnkt_don_hang dh
                    LEFT JOIN tgb_chi_tiet_don_hang ct ON dh.ma_don_hang = ct.ma_don_hang
                    LEFT JOIN lbn_san_pham sp ON ct.ma_san_pham = sp.ma_san_pham
                    WHERE dh.ma_don_hang > :last_id
                    GROUP BY dh.ma_don_hang
                    ORDER BY dh.ma_don_hang DESC
                ");
                $stmt->execute([':last_id' => $lastOrderId]);
                $newOrders = $stmt->fetchAll();
                $stmt->closeCursor();
                foreach ($newOrders as &$r) {
                    $r['ma_van_don'] = $this->formatTrackingCode((int)$r['ma_don_hang']);
                    $r['status_meta'] = $this->getStatusMeta((string)($r['trang_thai_don_hang'] ?? 'cho_duyet'));
                }
                unset($r);
            }

            return [
                'new_orders'   => $newOrders,
                'max_order_id' => $maxId,
                'counts'       => $this->getOrderStatusCounts(),
            ];
        } catch (PDOException $e) {
            error_log('[lnkt] getNewOrdersSince: ' . $e->getMessage());
            return ['new_orders' => [], 'max_order_id' => $lastOrderId, 'counts' => $this->getOrderStatusCounts()];
        }
    }

    /**
     * Khách hàng xác nhận đã quét mã thanh toán VietQR (chỉ khi phương thức = vietqr)
     */
    public function confirmVietQrPaid(int $orderId, int $userId, bool $isStaffOrAdmin = false): array {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Chưa kết nối cơ sở dữ liệu.');
        }
        $order = $this->getOrderById($orderId);
        if (!$order) {
            throw new RuntimeException('Không tìm thấy đơn hàng #' . $orderId . '.');
        }
        if (!$isStaffOrAdmin && (int)($order['ma_nguoi_dung'] ?? 0) !== $userId) {
            throw new RuntimeException('Bạn không có quyền xác nhận thanh toán cho đơn hàng này.');
        }
        if (($order['phuong_thuc_thanh_toan'] ?? 'cod') !== 'vietqr') {
            throw new RuntimeException('Đơn hàng này sử dụng hình thức COD, không áp dụng xác nhận VietQR.');
        }
        if ($order['trang_thai_don_hang'] === 'da_huy') {
            throw new RuntimeException('Đơn hàng đã hủy, không thể xác nhận chuyển khoản.');
        }

        $existingNote = trim((string)($order['ghi_chu'] ?? ''));
        $tag = '[Khách báo đã chuyển khoản VietQR]';
        $newNote = $existingNote === '' ? $tag : (strpos($existingNote, $tag) === false ? mb_substr($existingNote . ' ' . $tag, 0, 255, 'UTF-8') : $existingNote);

        $stmt = $this->db->prepare("
            UPDATE `lnkt_don_hang`
            SET `trang_thai_thanh_toan` = 'da_thanh_toan',
                `ghi_chu`               = :note,
                `ngay_cap_nhat`         = NOW()
            WHERE `ma_don_hang` = :id
        ");
        $stmt->execute([':note' => $newNote, ':id' => $orderId]);

        return [
            'order_id'              => $orderId,
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'ghi_chu'               => $newNote,
        ];
    }

    /**
     * Khách hàng tự hủy đơn hàng của mình (CHỈ khi đơn đang ở trạng thái 'cho_duyet')
     * Tự động hoàn trả tồn kho trong giao dịch ACID.
     */
    public function cancelOrderByOwner(int $orderId, int $userId): array {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Chưa kết nối cơ sở dữ liệu.');
        }

        try {
            $this->db->beginTransaction();

            $chkStmt = $this->db->prepare("
                SELECT `ma_don_hang`, `ma_nguoi_dung`, `trang_thai_don_hang`
                FROM `lnkt_don_hang`
                WHERE `ma_don_hang` = :id
                FOR UPDATE
            ");
            $chkStmt->execute([':id' => $orderId]);
            $row = $chkStmt->fetch();
            $chkStmt->closeCursor();

            if (!$row) {
                throw new RuntimeException('Đơn hàng không tồn tại.');
            }
            if ((int)($row['ma_nguoi_dung'] ?? 0) !== $userId) {
                throw new RuntimeException('Bạn không có quyền hủy đơn hàng này.');
            }
            if ($this->normalizeStatus((string)$row['trang_thai_don_hang']) !== 'cho_duyet') {
                throw new RuntimeException('Chỉ có thể hủy đơn hàng khi đơn đang ở trạng thái "Chờ duyệt". Vui lòng liên hệ CSKH nếu cần hỗ trợ.');
            }

            $restStmt = $this->db->prepare("
                UPDATE `lbn_san_pham` sp
                JOIN `tgb_chi_tiet_don_hang` ct ON sp.`ma_san_pham` = ct.`ma_san_pham`
                SET sp.`so_luong_ton_kho` = sp.`so_luong_ton_kho` + ct.`so_luong`
                WHERE ct.`ma_don_hang` = :id
            ");
            $restStmt->execute([':id' => $orderId]);

            $updStmt = $this->db->prepare("
                UPDATE `lnkt_don_hang`
                SET `trang_thai_don_hang` = 'da_huy',
                    `ngay_cap_nhat`       = NOW()
                WHERE `ma_don_hang` = :id
            ");
            $updStmt->execute([':id' => $orderId]);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        unset($_SESSION['lnkt_order_status_overrides'][$orderId], $_SESSION['lbn_stock_overrides']);

        return [
            'order_id'   => $orderId,
            'new_status' => 'da_huy',
            'counts'     => $this->getOrderStatusCounts(),
        ];
    }

    /**
     * Tổng hợp dữ liệu thực cho Bảng điều khiển Quản trị (MH 12)
     */
    public function getAdminDashboardData(): array {
        $data = [
            'completed_revenue'  => 0.0,
            'pipeline_revenue'   => 0.0,
            'total_orders'       => 0,
            'pending_orders'     => 0,
            'completed_orders'   => 0,
            'total_customers'    => 0,
            'total_kol_profiles' => 0,
            'low_stock_count'    => 0,
            'low_stock_products' => [],
            'recent_orders'      => [],
            'status_counts'      => $this->getOrderStatusCounts(),
            'revenue_7_days'     => [],
            'category_breakdown' => [],
        ];

        if (!$this->hasConnection()) {
            return $data;
        }

        try {
            $revRow = $this->db->query("
                SELECT
                    COALESCE(SUM(CASE WHEN trang_thai_don_hang = 'hoan_tat' THEN tong_tien_thanh_toan ELSE 0 END), 0) AS completed_rev,
                    COALESCE(SUM(CASE WHEN trang_thai_don_hang != 'da_huy' THEN tong_tien_thanh_toan ELSE 0 END), 0) AS pipeline_rev
                FROM lnkt_don_hang
            ")->fetch();
            $data['completed_revenue'] = (float)($revRow['completed_rev'] ?? 0);
            $data['pipeline_revenue']  = (float)($revRow['pipeline_rev'] ?? 0);

            $counts = $data['status_counts'];
            $data['total_orders']     = (int)($counts['all'] ?? 0);
            $data['pending_orders']   = (int)($counts['cho_duyet'] ?? 0);
            $data['completed_orders'] = (int)($counts['hoan_tat'] ?? 0);

            $custRow = $this->db->query("SELECT COUNT(*) AS cnt FROM hgb_nguoi_dung WHERE vai_tro = 'khach_hang'")->fetch();
            $data['total_customers'] = (int)($custRow['cnt'] ?? 0);

            $kolRow = $this->db->query("SELECT COUNT(*) AS cnt FROM lnkt_dang_ky_kol_nhi")->fetch();
            $data['total_kol_profiles'] = (int)($kolRow['cnt'] ?? 0);

            $lowRows = $this->db->query("
                SELECT sp.ma_san_pham, sp.ten_san_pham, sp.so_luong_ton_kho, sp.gia_ban, sp.duong_dan_anh,
                       COALESCE(dm.ten_danh_muc, 'Thời trang trẻ em') AS ten_danh_muc
                FROM lbn_san_pham sp
                LEFT JOIN lbn_danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                WHERE sp.ma_san_pham >= 101 AND sp.so_luong_ton_kho <= 5
                ORDER BY sp.so_luong_ton_kho ASC, sp.ma_san_pham ASC
            ")->fetchAll();
            $data['low_stock_products'] = $lowRows;
            $data['low_stock_count']    = count($lowRows);

            $recent = $this->getAllOrdersForStaff('all', '');
            $data['recent_orders'] = array_slice($recent, 0, 8);

            // Biểu đồ doanh thu 7 ngày gần nhất
            $daysMap = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $daysMap[$d] = [
                    'date'    => $d,
                    'label'   => date('d/m', strtotime($d)),
                    'revenue' => 0.0,
                    'orders'  => 0,
                ];
            }
            $dayStmt = $this->db->query("
                SELECT DATE(ngay_dat_hang) AS d,
                       COUNT(*) AS orders_cnt,
                       COALESCE(SUM(CASE WHEN trang_thai_don_hang != 'da_huy' THEN tong_tien_thanh_toan ELSE 0 END), 0) AS rev
                FROM lnkt_don_hang
                WHERE ngay_dat_hang >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(ngay_dat_hang)
            ");
            foreach ($dayStmt->fetchAll() as $dr) {
                $key = (string)$dr['d'];
                if (isset($daysMap[$key])) {
                    $daysMap[$key]['revenue'] = (float)$dr['rev'];
                    $daysMap[$key]['orders']  = (int)$dr['orders_cnt'];
                }
            }
            $data['revenue_7_days'] = array_values($daysMap);

            // Biểu đồ cơ cấu doanh số theo danh mục từ tgb_chi_tiet_don_hang
            $catRows = $this->db->query("
                SELECT dm.ten_danh_muc,
                       COALESCE(SUM(ct.so_luong), 0) AS qty,
                       COALESCE(SUM(ct.so_luong * ct.don_gia_mua), 0) AS revenue
                FROM lbn_danh_muc dm
                LEFT JOIN lbn_san_pham sp ON sp.ma_danh_muc = dm.ma_danh_muc
                LEFT JOIN tgb_chi_tiet_don_hang ct ON ct.ma_san_pham = sp.ma_san_pham
                LEFT JOIN lnkt_don_hang dh ON ct.ma_don_hang = dh.ma_don_hang AND dh.trang_thai_don_hang != 'da_huy'
                GROUP BY dm.ma_danh_muc, dm.ten_danh_muc
                ORDER BY dm.ma_danh_muc ASC
            ")->fetchAll();
            $data['category_breakdown'] = $catRows;
        } catch (PDOException $e) {
            error_log('[lnkt] getAdminDashboardData: ' . $e->getMessage());
        }

        return $data;
    }
}
