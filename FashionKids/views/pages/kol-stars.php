<?php
$page_title = "Danh Sách Siêu Sao & Đại Sứ Nhí KOL 2026";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../../models/RealtimeContentModel.php";

$rtModel = new RealtimeContentModel();
$allApplications = $rtModel->getKolApplications();

// Chỉ hiển thị công khai các hồ sơ không bị từ chối
$kolStars = array_values(array_filter($allApplications, static function ($item) {
    return ($item['status'] ?? 'cho_duyet') !== 'tu_choi';
}));

// Sắp xếp ưu tiên Đại sứ Trúng tuyển -> Mời phỏng vấn -> Hồ sơ nổi bật
$statusPriority = [
    'trung_tuyen'   => 1,
    'moi_phong_van' => 2,
    'cho_duyet'     => 3,
];
usort($kolStars, static function ($a, $b) use ($statusPriority) {
    $pa = $statusPriority[$a['status'] ?? 'cho_duyet'] ?? 9;
    $pb = $statusPriority[$b['status'] ?? 'cho_duyet'] ?? 9;
    if ($pa !== $pb) {
        return $pa <=> $pb;
    }
    // Đưa các gương mặt siêu sao mới cập nhật (ID cao hơn) lên hàng đầu
    return ((int)($b['id'] ?? 0)) <=> ((int)($a['id'] ?? 0));
});

$countWinner    = 0;
$countInterview = 0;
$countFeatured  = 0;
foreach ($kolStars as $k) {
    $st = $k['status'] ?? 'cho_duyet';
    if ($st === 'trung_tuyen') $countWinner++;
    elseif ($st === 'moi_phong_van') $countInterview++;
    else $countFeatured++;
}

$requestedKolId = isset($_GET['kol_id']) ? (int)$_GET['kol_id'] : 0;
?>

<div class="container my-4 my-md-5" id="fk_kol_stars_directory">
    <!-- ====================================================================
         1. HERO SHOWCASE DANH SÁCH SAO NHÍ & NÚT NHỎ SANG TRANG ĐĂNG KÝ
         ==================================================================== -->
    <div class="fk-kol-dir-hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                    <span class="badge badge-pill badge-gold px-3 py-2 shadow-sm">
                        <i class="fa-solid fa-crown mr-1"></i>ĐẠI SỨ THƯƠNG HIỆU NHÍ FASHIONKIDS 2026
                    </span>
                    <span class="badge badge-pill badge-mint px-3 py-2">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1"></i><?= count($kolStars) ?> Gương Mặt Tiêu Biểu
                    </span>
                </div>
                <h1 class="font-weight-bold text-slate mb-2" style="font-size: clamp(1.6rem, 2.5vw, 2.25rem);">
                    Thiên Thần &amp; <span class="text-peach">Siêu Sao Nhí KOL</span> FashionKids
                </h1>
                <p class="text-muted-custom mb-3" style="max-width: 680px; line-height: 1.65;">
                    Chiêm ngưỡng bộ ảnh Lookbook, thông số chiều cao – cân nặng và năng khiếu nổi bật của các bạn nhỏ đang đồng hành cùng bộ sưu tập Bright Pastel 100% Sợi Tre Organic.
                </p>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <span class="fk-mag-kpi-chip">
                        <i class="fa-solid fa-award text-success mr-1"></i><?= $countWinner ?> Đại Sứ Chính Thức
                    </span>
                    <span class="fk-mag-kpi-chip">
                        <i class="fa-solid fa-microphone-lines text-peach mr-1"></i><?= $countInterview ?> Đang Phỏng Vấn Studio
                    </span>
                    <span class="fk-mag-kpi-chip">
                        <i class="fa-solid fa-star text-info mr-1"></i><?= $countFeatured ?> Gương Mặt Triển Vọng
                    </span>
                </div>
            </div>
            <div class="col-12 col-lg-4 text-lg-right">
                <div class="d-inline-flex flex-wrap justify-content-lg-end" style="gap: 8px;">
                    <a href="/FashionKids/views/pages/blog-feeds.php" class="btn btn-light btn-sm rounded-pill border px-3 py-2 font-weight-bold text-slate shadow-sm">
                        <i class="fa-solid fa-arrow-left text-peach mr-1"></i>Về Tạp Chí
                    </a>
                    <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-pastel-primary btn-sm rounded-pill px-3 py-2 font-weight-bold shadow-sm">
                        <i class="fa-solid fa-feather-pointed mr-1"></i>Đăng ký KOL Nhí ngay
                    </a>
                </div>
                <div class="mt-2">
                    <small class="text-muted-custom font-weight-bold">
                        <i class="fa-solid fa-shield-heart text-success mr-1"></i>Thông tin liên hệ phụ huynh được bảo mật riêng tư
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         2. THANH LỌC THEO TRẠNG THÁI, GIỚI TÍNH & TÌM KIẾM NHANH
         ==================================================================== -->
    <div class="card card-pastel p-3 p-md-4 mb-4 shadow-sm border-0">
        <div class="row align-items-center">
            <div class="col-12 col-xl-8 mb-3 mb-xl-0">
                <div class="d-flex flex-wrap" id="fk_kolFilterGroup" style="gap: 8px;">
                    <button type="button" class="btn fk-blog-filter-btn active" data-filter="all" onclick="fkFilterKolStars('all', this)">
                        <i class="fa-solid fa-users mr-1"></i>Tất cả (<?= count($kolStars) ?>)
                    </button>
                    <button type="button" class="btn fk-blog-filter-btn" data-filter="trung_tuyen" onclick="fkFilterKolStars('trung_tuyen', this)">
                        <i class="fa-solid fa-crown mr-1"></i>Đại sứ chính thức (<?= $countWinner ?>)
                    </button>
                    <button type="button" class="btn fk-blog-filter-btn" data-filter="moi_phong_van" onclick="fkFilterKolStars('moi_phong_van', this)">
                        <i class="fa-solid fa-camera mr-1"></i>Mời phỏng vấn (<?= $countInterview ?>)
                    </button>
                    <button type="button" class="btn fk-blog-filter-btn" data-filter="be_gai" onclick="fkFilterKolStars('be_gai', this)">
                        <i class="fa-solid fa-venus mr-1"></i>Bé gái
                    </button>
                    <button type="button" class="btn fk-blog-filter-btn" data-filter="be_trai" onclick="fkFilterKolStars('be_trai', this)">
                        <i class="fa-solid fa-mars mr-1"></i>Bé trai
                    </button>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-pill-left">
                            <i class="fa-solid fa-magnifying-glass text-peach"></i>
                        </span>
                    </div>
                    <input type="text" id="fk_kolSearchInput" class="form-control border-left-0"
                           placeholder="Tìm tên bé, mã KOL, năng khiếu..."
                           onkeyup="fkApplyKolSearch()">
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         3. LƯỚI HỒ SƠ CÁC SIÊU SAO NHÍ (PUBLIC LOOKBOOK PORTFOLIO)
         ==================================================================== -->
    <div class="row" id="fk_kolStarsGrid">
        <?php foreach ($kolStars as $star):
            $st = $star['status'] ?? 'cho_duyet';
            $badgeMap = [
                'trung_tuyen'   => ['Đại sứ chính thức', 'badge-mint', 'fa-crown'],
                'moi_phong_van' => ['Mời phỏng vấn Studio', 'badge-peach', 'fa-camera'],
                'cho_duyet'     => ['Gương mặt nổi bật', 'badge-sky', 'fa-star'],
            ];
            $bInfo = $badgeMap[$st] ?? $badgeMap['cho_duyet'];
            $genderKey   = ($star['gender'] ?? 'be_gai') === 'be_trai' ? 'be_trai' : 'be_gai';
            $genderLabel = $genderKey === 'be_trai' ? 'Bé trai' : 'Bé gái';
            $heightVal   = (float)($star['height_cm'] ?? 95);
            $weightVal   = (float)($star['weight_kg'] ?? 14);

            // Gợi ý size tham khảo theo chiều cao/cân nặng thực tế
            $sizeSuggest = 'Size 3 (3–4T)';
            if ($heightVal < 85 || $weightVal < 11.5) $sizeSuggest = 'Size 1 (1–2T)';
            elseif ($heightVal < 95 || $weightVal < 14) $sizeSuggest = 'Size 2 (2–3T)';
            elseif ($heightVal < 105 || $weightVal < 17) $sizeSuggest = 'Size 3 (3–4T)';
            elseif ($heightVal < 115 || $weightVal < 21) $sizeSuggest = 'Size 4 (4–5T)';
            else $sizeSuggest = 'Size 5 (5–6T)';

            $photosJson = htmlspecialchars(json_encode(array_values($star['photos'] ?? [$star['image']]), JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
            $searchBlob = mb_strtolower(($star['baby_name'] ?? '') . ' ' . ($star['code'] ?? '') . ' ' . ($star['talent'] ?? '') . ' ' . $genderLabel . ' ' . ($star['age_label'] ?? ''), 'UTF-8');
        ?>
            <div class="col-12 col-md-6 col-lg-4 mb-4 fk-kol-star-col"
                 data-id="<?= (int)$star['id'] ?>"
                 data-status="<?= htmlspecialchars($st) ?>"
                 data-gender="<?= htmlspecialchars($genderKey) ?>"
                 data-search="<?= htmlspecialchars($searchBlob) ?>">
                <div class="card fk-kol-dir-card h-100 shadow-sm border-0 <?= $requestedKolId === (int)$star['id'] ? 'fk-kol-highlight-ring' : '' ?>">
                    <!-- Ảnh Lookbook Chân Dung -->
                    <div class="fk-kol-dir-photo-wrap">
                        <img src="<?= htmlspecialchars($star['image']) ?>"
                             alt="<?= htmlspecialchars($star['baby_name']) ?>"
                             class="fk-kol-dir-photo">
                        <span class="badge <?= $bInfo[1] ?> fk-kol-dir-status">
                            <i class="fa-solid <?= $bInfo[2] ?> mr-1"></i><?= $bInfo[0] ?>
                        </span>
                        <span class="fk-kol-dir-code">
                            <?= htmlspecialchars($star['code'] ?? 'KOL-2026') ?>
                        </span>
                        <div class="fk-kol-dir-bottom-bar">
                            <span class="badge badge-light text-slate px-2 py-1 rounded-pill font-weight-bold">
                                <i class="fa-solid <?= $genderKey === 'be_trai' ? 'fa-mars text-info' : 'fa-venus text-peach' ?> mr-1"></i><?= $genderLabel ?>
                            </span>
                            <span class="badge badge-light text-slate px-2 py-1 rounded-pill font-weight-bold">
                                <i class="fa-solid fa-cake-candles text-warning mr-1"></i><?= htmlspecialchars($star['age_label']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Thông Tin Chi Tiết Bé -->
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="font-weight-bold text-slate mb-2">
                            <?= htmlspecialchars($star['baby_name']) ?>
                        </h5>

                        <!-- Bảng Thông Số Hình Thể -->
                        <div class="row no-gutters fk-kol-metrics-box p-2 mb-3 text-center">
                            <div class="col-4 border-right">
                                <small class="text-muted-custom d-block" style="font-size: 0.7rem;">Chiều cao</small>
                                <strong class="text-slate small"><?= rtrim(rtrim(number_format($heightVal, 1, '.', ''), '0'), '.') ?> cm</strong>
                            </div>
                            <div class="col-4 border-right">
                                <small class="text-muted-custom d-block" style="font-size: 0.7rem;">Cân nặng</small>
                                <strong class="text-slate small"><?= rtrim(rtrim(number_format($weightVal, 1, '.', ''), '0'), '.') ?> kg</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted-custom d-block" style="font-size: 0.7rem;">Size gợi ý</small>
                                <strong class="text-peach small"><?= htmlspecialchars(explode(' ', $sizeSuggest)[0] . ' ' . (explode(' ', $sizeSuggest)[1] ?? '')) ?></strong>
                            </div>
                        </div>

                        <?php
                        $rawTalent  = (string)($star['talent'] ?? '');
                        $styleTitle = (string)($star['style_tag'] ?? '');
                        $outfitInfo = (string)($star['outfit_info'] ?? '');
                        $noteTalent = $rawTalent;

                        if ($outfitInfo === '' && preg_match('/^Phong cách:\s*([^•]+?)\s*•\s*Outfit:\s*(.+)$/u', $rawTalent, $tm)) {
                            if ($styleTitle === '') {
                                $styleTitle = trim($tm[1]);
                            }
                            $parts      = preg_split('/(?<=\.)\s+/u', trim($tm[2]), 2);
                            $outfitInfo = trim($parts[0] ?? $tm[2]);
                            $noteTalent = trim($parts[1] ?? '');
                        }
                        ?>

                        <?php if ($styleTitle !== '' || $outfitInfo !== ''): ?>
                            <div class="rounded bg-milk-cream border p-2 mb-3 flex-grow-1" style="line-height: 1.45;">
                                <?php if ($styleTitle !== ''): ?>
                                    <div class="mb-1">
                                        <span class="badge badge-pill badge-peach px-2 py-1 small">
                                            <i class="fa-solid fa-sparkles mr-1"></i><?= htmlspecialchars($styleTitle) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($outfitInfo !== ''): ?>
                                    <div class="small text-slate font-weight-bold mb-1">
                                        <i class="fa-solid fa-shirt text-peach mr-1"></i>Info đồ: <span class="font-weight-normal text-muted-custom"><?= htmlspecialchars($outfitInfo) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($noteTalent !== ''): ?>
                                    <small class="text-muted-custom d-block">
                                        <i class="fa-solid fa-wand-magic-sparkles text-info mr-1"></i><?= htmlspecialchars($noteTalent) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted-custom small mb-3 flex-grow-1" style="line-height: 1.55;">
                                <i class="fa-solid fa-wand-magic-sparkles text-peach mr-1"></i>
                                <?= htmlspecialchars($rawTalent) ?>
                            </p>
                        <?php endif; ?>

                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <small class="text-muted-custom font-weight-bold">
                                <i class="fa-regular fa-images text-info mr-1"></i><?= count($star['photos'] ?? [$star['image']]) ?> ảnh Lookbook
                            </small>
                            <button type="button"
                                    class="btn btn-pastel-primary btn-sm rounded-pill px-3 py-1 font-weight-bold shadow-sm fk-btn-open-kol-modal"
                                    data-id="<?= (int)$star['id'] ?>"
                                    data-code="<?= htmlspecialchars($star['code'] ?? '') ?>"
                                    data-name="<?= htmlspecialchars($star['baby_name']) ?>"
                                    data-age="<?= htmlspecialchars($star['age_label']) ?>"
                                    data-gender="<?= htmlspecialchars($genderLabel) ?>"
                                    data-height="<?= htmlspecialchars((string)$heightVal) ?>"
                                    data-weight="<?= htmlspecialchars((string)$weightVal) ?>"
                                    data-size="<?= htmlspecialchars($sizeSuggest) ?>"
                                    data-status="<?= htmlspecialchars($bInfo[0]) ?>"
                                    data-badge="<?= htmlspecialchars($bInfo[1]) ?>"
                                    data-talent="<?= htmlspecialchars($star['talent']) ?>"
                                    data-guardian="<?= htmlspecialchars($star['guardian_name'] ?? 'Phụ huynh đồng hành') ?>"
                                    data-photos="<?= $photosJson ?>"
                                    onclick="fkOpenKolStarModal(this)">
                                Xem hồ sơ <i class="fa-solid fa-expand ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="fk_kolEmptyAlert" class="card card-pastel p-5 text-center d-none my-3">
        <i class="fa-solid fa-child-reaching text-peach h1 mb-3"></i>
        <h5 class="font-weight-bold text-slate">Không tìm thấy hồ sơ sao nhí phù hợp</h5>
        <p class="text-muted-custom small mb-3">Ba mẹ hãy thử từ khóa khác hoặc bấm chọn Tất cả để xem toàn bộ danh sách nhé.</p>
        <div>
            <button type="button" class="btn btn-pastel-primary btn-sm rounded-pill px-4 font-weight-bold" onclick="fkResetKolFilters()">
                Hiển thị toàn bộ Sao Nhí
            </button>
        </div>
    </div>

    <!-- ====================================================================
         4. CALLOUT CHUYỂN QUA TRANG ĐĂNG KÝ KOL NHÍ (NHỎ GỌN, THANH LỊCH)
         ==================================================================== -->
    <div class="fk-kol-cta-strip p-4 mt-4 d-flex flex-wrap justify-content-between align-items-center shadow-sm" style="gap: 16px;">
        <div class="min-w-0">
            <span class="badge badge-pill badge-peach px-3 py-1 mb-2">
                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>TUYỂN CHỌN ĐẠI SỨ NHÍ 2026
            </span>
            <h5 class="font-weight-bold text-slate mb-1">
                Bé nhà bạn tự tin trước ống kính &amp; yêu thích thời trang sợi tre?
            </h5>
            <p class="text-muted-custom small mb-0">
                Chụp Lookbook Studio miễn phí &amp; nhận gói tài trợ trang phục lên tới 15.000.000đ cùng thẻ VIP Kim Cương (-15%).
            </p>
        </div>
        <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-pastel-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm text-nowrap flex-shrink-0">
            <i class="fa-solid fa-paper-plane mr-1"></i>Chuyển sang trang Đăng ký KOL Nhí
        </a>
    </div>
</div>

<!-- ========================================================================
     MODAL XEM CHI TIẾT HỒ SƠ & BỘ ẢNH LOOKBOOK CỦA SAO NHÍ
     ======================================================================== -->
<div class="modal fade" id="fk_modalKolStarDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content card-pastel border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-white border-bottom py-3">
                <div>
                    <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                        <span id="fk_kolModalStatus" class="badge badge-pill badge-mint px-3 py-1">Đại sứ chính thức</span>
                        <small id="fk_kolModalCode" class="font-weight-bold text-muted-custom">KOL-2026-01</small>
                    </div>
                    <h5 class="modal-title font-weight-bold text-slate" id="fk_kolModalName">Tên Sao Nhí</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="rounded-lg overflow-hidden border bg-white shadow-sm">
                            <img id="fk_kolModalMainPhoto" src="" alt="KOL Photo" class="w-100" style="height: 330px; object-fit: cover; object-position: center 15%;">
                        </div>
                        <div id="fk_kolModalThumbs" class="d-flex flex-wrap mt-2" style="gap: 8px;"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <h6 class="font-weight-bold text-slate mb-3">
                            <i class="fa-solid fa-id-card text-peach mr-1"></i>Thông Số &amp; Hồ Sơ Nổi Bật
                        </h6>
                        <div class="row no-gutters fk-kol-metrics-box p-3 mb-3">
                            <div class="col-6 mb-2 pr-2">
                                <small class="text-muted-custom d-block">Độ tuổi &amp; Giới tính</small>
                                <strong class="text-slate" id="fk_kolModalAgeGender">4 tuổi • Bé gái</strong>
                            </div>
                            <div class="col-6 mb-2 pl-2">
                                <small class="text-muted-custom d-block">Size FashionKids phù hợp</small>
                                <strong class="text-peach" id="fk_kolModalSize">Size 3 (3–4T)</strong>
                            </div>
                            <div class="col-6 pr-2">
                                <small class="text-muted-custom d-block">Chiều cao</small>
                                <strong class="text-slate" id="fk_kolModalHeight">96 cm</strong>
                            </div>
                            <div class="col-6 pl-2">
                                <small class="text-muted-custom d-block">Cân nặng</small>
                                <strong class="text-slate" id="fk_kolModalWeight">14.2 kg</strong>
                            </div>
                        </div>

                        <div class="p-3 rounded bg-white border mb-3">
                            <small class="font-weight-bold text-slate d-block mb-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-warning mr-1"></i>Năng khiếu &amp; Dấu ấn Lookbook
                            </small>
                            <p class="small text-muted-custom mb-0" id="fk_kolModalTalent" style="line-height: 1.6;"></p>
                        </div>

                        <div class="small text-muted-custom">
                            <i class="fa-solid fa-user-shield text-success mr-1"></i>
                            Người giám hộ đồng hành: <strong class="text-slate" id="fk_kolModalGuardian">Phụ huynh</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center" style="gap: 8px;">
                <a href="/FashionKids/views/pages/kol-casting.php" class="btn btn-light btn-sm rounded-pill border px-3 py-2 font-weight-bold text-slate">
                    <i class="fa-solid fa-crown text-peach mr-1"></i>Đăng ký cho bé nhà bạn ngay
                </a>
                <button type="button" class="btn btn-pastel-primary btn-sm rounded-pill px-4 py-2 font-weight-bold" data-dismiss="modal">
                    Đóng hồ sơ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var requestedId = <?= (int)$requestedKolId ?>;
    if (requestedId > 0) {
        var btn = document.querySelector('.fk-btn-open-kol-modal[data-id="' + requestedId + '"]');
        if (btn && typeof window.fkOpenKolStarModal === 'function') {
            setTimeout(function () { window.fkOpenKolStarModal(btn); }, 250);
        }
    }
});
</script>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
