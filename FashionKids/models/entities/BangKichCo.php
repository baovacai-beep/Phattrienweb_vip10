<?php
/**
 * Lớp Bảng Kích Cỡ Quy Chuẩn (models/entities/BangKichCo.php)
 * Đảm nhiệm: Quy chuẩn size theo số đo chiều cao và bàn chân (Lê Nguyễn Khánh Trình - lnkt_)
 */

class BangKichCo {
    private int $maKichCo;
    private int $maDanhMuc;
    private string $tenKichCo;
    private float $chieuCaoToiThieu;
    private float $chieuCaoToiDa;
    private float $chieuDaiChanToiThieu;
    private float $chieuDaiChanToiDa;

    public function __construct(
        int $maKichCo = 0,
        int $maDanhMuc = 0,
        string $tenKichCo = '',
        float $chieuCaoToiThieu = 0.0,
        float $chieuCaoToiDa = 0.0,
        float $chieuDaiChanToiThieu = 0.0,
        float $chieuDaiChanToiDa = 0.0
    ) {
        $this->maKichCo             = $maKichCo;
        $this->maDanhMuc            = $maDanhMuc;
        $this->tenKichCo            = $tenKichCo;
        $this->chieuCaoToiThieu     = $chieuCaoToiThieu;
        $this->chieuCaoToiDa        = $chieuCaoToiDa;
        $this->chieuDaiChanToiThieu = $chieuDaiChanToiThieu;
        $this->chieuDaiChanToiDa    = $chieuDaiChanToiDa;
    }

    public function getMaKichCo(): int { return $this->maKichCo; }
    public function getMaDanhMuc(): int { return $this->maDanhMuc; }
    public function getTenKichCo(): string { return $this->tenKichCo; }
    public function getChieuCaoToiThieu(): float { return $this->chieuCaoToiThieu; }
    public function getChieuCaoToiDa(): float { return $this->chieuCaoToiDa; }
    public function getChieuDaiChanToiThieu(): float { return $this->chieuDaiChanToiThieu; }
    public function getChieuDaiChanToiDa(): float { return $this->chieuDaiChanToiDa; }

    /**
     * Đối soát số đo thực tế để kiểm tra có khớp với size này không
     */
    public function doiSoatSoDo(float $soDo, string $loai = 'quan_ao'): string {
        if ($loai === 'giay_dep') {
            if ($soDo >= $this->chieuDaiChanToiThieu && $soDo <= $this->chieuDaiChanToiDa) {
                return $this->tenKichCo;
            }
        } else {
            if ($soDo >= $this->chieuCaoToiThieu && $soDo <= $this->chieuCaoToiDa) {
                return $this->tenKichCo;
            }
        }
        return '';
    }
}
