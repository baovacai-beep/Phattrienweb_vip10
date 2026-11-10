<?php
/**
 * Lớp Model Chat CSKH Trực Tuyến (models/ChatModel.php)
 * Đảm nhiệm: Gửi nhận tin nhắn tư vấn và đánh dấu đã đọc (Trần Gia Bảo - tgb_)
 * Tích hợp Stored Procedures: tgb_send_chat_message, tgb_mark_chat_as_read
 */

require_once __DIR__ . '/BaseModel.php';

class ChatModel extends BaseModel {
    private array $mockSessions = [
        [
            'ma_phien_khach'    => 'sess_mai_nguyen_01',
            'ho_va_ten'         => 'Nguyễn Thị Mai',
            'thu_dien_tu'       => 'mai.nguyen@gmail.com',
            'da_dang_nhap'      => 1,
            'tin_nhan_cuoi'     => 'Bé gái 3 tuổi, nặng 14kg cao 95cm thì nên chọn mẫu váy công chúa size nào vậy shop?',
            'thoi_gian_cuoi'    => '13:05',
            'so_tin_chua_doc'   => 1
        ],
        [
            'ma_phien_khach'    => 'sess_guest_123456',
            'ho_va_ten'         => 'Khách vãng lai #108',
            'thu_dien_tu'       => 'Khách vãng lai',
            'da_dang_nhap'      => 0,
            'tin_nhan_cuoi'     => 'Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa size mấy thì vừa ạ?',
            'thoi_gian_cuoi'    => '12:58',
            'so_tin_chua_doc'   => 0
        ],
        [
            'ma_phien_khach'    => 'sess_me_be_sam_03',
            'ho_va_ten'         => 'Võ Thị Thanh Thảo (Mẹ bé Sam)',
            'thu_dien_tu'       => 'thanhthao@gmail.com',
            'da_dang_nhap'      => 1,
            'tin_nhan_cuoi'     => 'Shop kiểm tra giúp mình đơn #FK-ORD-202608 đã đóng gói chưa nhé!',
            'thoi_gian_cuoi'    => '12:40',
            'so_tin_chua_doc'   => 2
        ]
    ];

    /**
     * Gửi tin nhắn tư vấn (hỗ trợ cả SP và INSERT trực tiếp vào bảng tgb_tin_nhan_tu_van)
     */
    public function sendMessage(string $sessionId, ?int $userId, string $sender, string $message): bool {
        $message = trim($message);
        if ($message === '') {
            return false;
        }
        if (!in_array($sender, ['khach_hang', 'nhan_vien', 'quan_tri'], true)) {
            $sender = 'nhan_vien';
        }

        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `tgb_send_chat_message`(:sid, :uid, :sender, :msg)");
                $ok = $stmt->execute([
                    ':sid'    => $sessionId,
                    ':uid'    => $userId,
                    ':sender' => $sender,
                    ':msg'    => $message
                ]);
                $stmt->closeCursor();
                return $ok;
            } catch (PDOException $e) {
                try {
                    $stmt2 = $this->db->prepare(
                        "INSERT INTO `tgb_tin_nhan_tu_van` (`ma_phien_khach`, `ma_nguoi_dung`, `nguoi_gui`, `noi_dung_tin_nhan`, `da_xem`, `ngay_gui`)
                         VALUES (:sid, :uid, :sender, :msg, :seen, NOW())"
                    );
                    return $stmt2->execute([
                        ':sid'    => $sessionId,
                        ':uid'    => $userId,
                        ':sender' => $sender,
                        ':msg'    => $message,
                        ':seen'   => ($sender === 'khach_hang') ? 0 : 1
                    ]);
                } catch (PDOException $e2) {
                    error_log("[tgb] sendMessage error: " . $e2->getMessage());
                }
            }
        }
        return true;
    }

    /**
     * Đánh dấu tin nhắn đã đọc (hỗ trợ cả tgb_danh_dau_tin_da_doc, tgb_mark_chat_as_read và UPDATE trực tiếp)
     */
    public function markAsRead(string $sessionId): bool {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("CALL `tgb_danh_dau_tin_da_doc`(:sid)");
                $ok = $stmt->execute([':sid' => $sessionId]);
                $stmt->closeCursor();
                return $ok;
            } catch (PDOException $e) {
                try {
                    $stmt2 = $this->db->prepare("CALL `tgb_mark_chat_as_read`(:sid)");
                    $ok2 = $stmt2->execute([':sid' => $sessionId]);
                    $stmt2->closeCursor();
                    return $ok2;
                } catch (PDOException $e2) {
                    try {
                        $stmt3 = $this->db->prepare(
                            "UPDATE `tgb_tin_nhan_tu_van` SET `da_xem` = 1 WHERE `ma_phien_khach` = :sid AND `da_xem` = 0"
                        );
                        return $stmt3->execute([':sid' => $sessionId]);
                    } catch (PDOException $e3) {
                        error_log("[tgb] markAsRead error: " . $e3->getMessage());
                    }
                }
            }
        }
        return true;
    }

    /**
     * Alias tiếng Việt theo chuẩn đặc tả: tgb_danh_dau_tin_da_doc
     */
    public function danhDauTinDaDoc(string $maPhienKhach): bool {
        return $this->markAsRead($maPhienKhach);
    }

    /**
     * Lấy lịch sử hội thoại của một phiên
     */
    public function getMessages(string $sessionId): array {
        if ($this->hasConnection()) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM `tgb_tin_nhan_tu_van` WHERE `ma_phien_khach` = :sid ORDER BY `ngay_gui` ASC");
                $stmt->execute([':sid' => $sessionId]);
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (PDOException $e) {}
        }

        // Fallback dữ liệu hội thoại mẫu theo từng phiên
        if ($sessionId === 'sess_guest_123456') {
            return [
                [
                    'ma_tin_nhan'       => 1,
                    'ma_phien_khach'    => 'sess_guest_123456',
                    'nguoi_gui'         => 'khach_hang',
                    'noi_dung_tin_nhan' => 'Shop ơi cho mình hỏi bé cao 92cm nặng 13kg mặc váy Elsa size mấy thì vừa ạ?',
                    'da_xem'            => 1,
                    'ngay_gui'          => '12:56'
                ],
                [
                    'ma_tin_nhan'       => 2,
                    'ma_phien_khach'    => 'sess_guest_123456',
                    'nguoi_gui'         => 'nhan_vien',
                    'noi_dung_tin_nhan' => 'Dạ chào bạn, với số đo 92cm bé mặc Size 2 (2-3T) là vừa vặn và thoải mái nhất ạ!',
                    'da_xem'            => 1,
                    'ngay_gui'          => '12:58'
                ]
            ];
        }
        if ($sessionId === 'sess_me_be_sam_03') {
            return [
                [
                    'ma_tin_nhan'       => 3,
                    'ma_phien_khach'    => 'sess_me_be_sam_03',
                    'nguoi_gui'         => 'khach_hang',
                    'noi_dung_tin_nhan' => 'Shop kiểm tra giúp mình đơn #FK-ORD-202608 đã đóng gói chưa nhé!',
                    'da_xem'            => 1,
                    'ngay_gui'          => '12:40'
                ],
                [
                    'ma_tin_nhan'       => 4,
                    'ma_phien_khach'    => 'sess_me_be_sam_03',
                    'nguoi_gui'         => 'nhan_vien',
                    'noi_dung_tin_nhan' => 'Dạ chào chị Thảo! Đơn #FK-ORD-202608 của chị đang được bộ phận kho đóng gói ưu tiên VIP Vàng và sẽ bàn giao bưu cục chiều nay ạ!',
                    'da_xem'            => 1,
                    'ngay_gui'          => '12:42'
                ]
            ];
        }

        return [
            [
                'ma_tin_nhan'       => 10,
                'ma_phien_khach'    => $sessionId,
                'nguoi_gui'         => 'khach_hang',
                'noi_dung_tin_nhan' => 'Bé gái 3 tuổi, nặng 14kg cao 95cm thì nên chọn mẫu váy công chúa size nào vậy shop?',
                'da_xem'            => 1,
                'ngay_gui'          => '13:04'
            ],
            [
                'ma_tin_nhan'       => 11,
                'ma_phien_khach'    => $sessionId,
                'nguoi_gui'         => 'nhan_vien',
                'noi_dung_tin_nhan' => 'Dạ chào chị Mai! Với bé 14kg và 95cm, chị nên chọn Size 2 (2-3T) hoặc Size 3 (3-4T) tùy theo phom bé mặc ôm hay rộng rãi thoải mái vận động nhất ạ!',
                'da_xem'            => 1,
                'ngay_gui'          => '13:05'
            ]
        ];
    }

    /**
     * Lấy danh sách các phiên hội thoại đang hoạt động
     */
    public function getSessions(): array {
        return $this->mockSessions;
    }
}
