<?php
/**
 * Lớp Model Chat CSKH Trực Tuyến (models/ChatModel.php)
 * Đảm nhiệm: Gửi nhận tin nhắn tư vấn và đánh dấu đã đọc (Trần Gia Bảo - tgb_)
 * Đồng bộ "đã đọc" qua Stored Procedure tgb_danh_dau_tin_da_doc (có biến OUT, đóng con trỏ chống lỗi PDO 2014).
 * Cài SP bằng database/patch_rbac_admin_staff.sql.
 */

require_once __DIR__ . '/BaseModel.php';

class ChatModel extends BaseModel {
    /**
     * Gửi tin nhắn (khách hoặc nhân viên) bằng INSERT trực tiếp vào tgb_tin_nhan_tu_van
     */
    public function sendMessage(string $sessionId, ?int $userId, string $sender, string $message): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare(
                    "INSERT INTO `tgb_tin_nhan_tu_van` (`ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`)
                     VALUES (:sid, :uid, :sender, :msg, 0)"
                );
                return $stmt->execute([
                    ':sid'    => $sessionId,
                    ':uid'    => $userId,
                    ':sender' => $sender,
                    ':msg'    => $message
                ]);
            } catch (PDOException $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Đánh dấu toàn bộ tin của khách trong phiên là đã đọc (tgb_danh_dau_tin_da_doc).
     * @return int số tin vừa được đánh dấu
     */
    public function markAsRead(string $sessionId, int $staffId): int {
        $out = $this->callProcedure(
            'CALL tgb_danh_dau_tin_da_doc(:sid, :nv, @so_tin)',
            [':sid' => $sessionId, ':nv' => $staffId],
            ['@so_tin']
        );
        return (int)($out['so_tin'] ?? 0);
    }

    /**
     * Lấy lịch sử hội thoại của một phiên
     */
    public function getMessages(string $sessionId): array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM `tgb_tin_nhan_tu_van` WHERE `ma_phien_khach` = :sid ORDER BY `ngay_gui` ASC");
                $stmt->execute([':sid' => $sessionId]);
                return $stmt->fetchAll();
            } catch (PDOException $e) {}
        }
        return [];
    }
}
