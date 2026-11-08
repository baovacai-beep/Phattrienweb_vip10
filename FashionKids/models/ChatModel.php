<?php
/**
 * Lớp Model Chat CSKH Trực Tuyến (models/ChatModel.php)
 * Đảm nhiệm: Gửi nhận tin nhắn tư vấn và đánh dấu đã đọc (Trần Gia Bảo - tgb_)
 * Tích hợp Stored Procedures: tgb_send_chat_message, tgb_mark_chat_as_read
 */

require_once __DIR__ . '/BaseModel.php';

class ChatModel extends BaseModel {
    /**
     * Gửi tin nhắn qua SP tgb_send_chat_message
     */
    public function sendMessage(string $sessionId, ?int $userId, string $sender, string $message): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `tgb_send_chat_message`(:sid, :uid, :sender, :msg)");
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
     * Đánh dấu đã đọc qua SP tgb_mark_chat_as_read
     */
    public function markAsRead(string $sessionId): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `tgb_mark_chat_as_read`(:sid)");
                return $stmt->execute([':sid' => $sessionId]);
            } catch (PDOException $e) {
                return false;
            }
        }
        return true;
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
