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
                $res = $stmt->execute([
                    ':sid'    => $sessionId,
                    ':uid'    => $userId,
                    ':sender' => $sender,
                    ':msg'    => $message
                ]);
                $stmt->closeCursor();
                return $res;
            } catch (PDOException $e) {
                try {
                    $ins = $this->db->prepare("
                        INSERT INTO `tgb_tin_nhan_tu_van` (`ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`, `ngay_gui`)
                        VALUES (:sid, :uid, :sender, :msg, :seen, NOW())
                    ");
                    return $ins->execute([
                        ':sid'    => $sessionId,
                        ':uid'    => $userId,
                        ':sender' => $sender,
                        ':msg'    => $message,
                        ':seen'   => ($sender === 'nhan_vien' ? 1 : 0),
                    ]);
                } catch (PDOException $e2) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Đánh dấu đã đọc qua SP tgb_mark_chat_as_read (có fallback SQL thuần)
     */
    public function markAsRead(string $sessionId): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `tgb_mark_chat_as_read`(:sid)");
                $res = $stmt->execute([':sid' => $sessionId]);
                $stmt->closeCursor();
                return $res;
            } catch (PDOException $e) {
                try {
                    $upd = $this->db->prepare("
                        UPDATE `tgb_tin_nhan_tu_van`
                        SET `da_xem` = 1
                        WHERE `ma_phien_khach` = :sid AND `nguoi_gui` = 'khach_hang' AND `da_xem` = 0
                    ");
                    return $upd->execute([':sid' => $sessionId]);
                } catch (PDOException $e2) {
                    return false;
                }
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
