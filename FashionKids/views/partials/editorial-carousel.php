<?php
/**
 * Shared Full-Bleed Editorial Auto-Rotating Carousel Helper
 * Supports Home, Shop Catalog, Magazine, KOL Stars, KOL Casting & Admin Preview.
 */

if (!function_exists('fkRenderEditorialCarousel')) {
    function fkRenderEditorialCarousel(string $carouselId, array $slides, array $opts = []): void
    {
        $totalSlides = count($slides);
        if ($totalSlides === 0) {
            return;
        }

        $wrapperCls     = $opts['wrapper_cls'] ?? 'shadow-sm mb-4';
        $bannerCls      = $opts['banner_cls'] ?? '';
        $intervalMs     = (int)($opts['interval'] ?? 4500);
        $dotCls         = $opts['dot_cls'] ?? '';
        $fallbackImg    = $opts['fallback_img'] ?? '/FashionKids/images/banners/theme-kol-runway-catwalk.jpg';
        $counterLabel   = $opts['counter_label'] ?? 'Banner';
        $firstH1        = !empty($opts['heading_first_h1']);
        $innerBannerCls = trim('fk-editorial-banner ' . $bannerCls);
        ?>
        <div id="<?= htmlspecialchars($carouselId) ?>"
             class="carousel slide fk-editorial-carousel <?= htmlspecialchars($wrapperCls) ?>"
             data-ride="carousel"
             data-pause="false"
             data-interval="<?= $intervalMs ?>">
            <div class="carousel-inner">
                <?php foreach ($slides as $idx => $s):
                    $counterText = str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT) . ' / ' . str_pad((string)$totalSlides, 2, '0', STR_PAD_LEFT);
                    $headingTag  = ($firstH1 && $idx === 0) ? 'h1' : 'h2';
                    $titleIdAttr = !empty($s['title_id']) ? ' id="' . htmlspecialchars($s['title_id']) . '"' : '';
                    $subIdAttr   = !empty($s['sub_id']) ? ' id="' . htmlspecialchars($s['sub_id']) . '"' : '';
                    $ctaIdAttr   = !empty($s['cta_id']) ? ' id="' . htmlspecialchars($s['cta_id']) . '"' : '';
                    $topUrl      = $s['url'] ?? ($s['cta_url'] ?? '#');
                    $topJsAttr   = !empty($s['top_js']) ? ' onclick="' . htmlspecialchars($s['top_js']) . ';"' : '';
                    $imgPos      = $s['img_pos'] ?? 'center center';
                    $altText     = $s['alt'] ?? strip_tags($s['title'] ?? 'FashionKids Editorial');
                    $ctaBtnCls   = $s['cta_btn_cls'] ?? 'btn btn-pastel-primary btn-sm rounded-pill px-3 py-1 font-weight-bold shadow-sm text-nowrap';
                    $ctaUrl      = $s['cta_url'] ?? ($s['url'] ?? '#');
                ?>
                    <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                        <div class="<?= htmlspecialchars($innerBannerCls) ?>">
                            <img src="<?= htmlspecialchars($s['image']) ?>"
                                 alt="<?= htmlspecialchars($altText) ?>"
                                 class="fk-editorial-banner-bg"
                                 style="object-position: <?= htmlspecialchars($imgPos) ?>;"
                                 onerror="this.onerror=null;this.src='<?= htmlspecialchars($fallbackImg) ?>';">
                            <div class="fk-editorial-banner-scrim"></div>

                            <!-- Tiêu đề nằm gọn vùng trống bên trái ảnh studio -->
                            <a href="<?= htmlspecialchars($topUrl) ?>"<?= $topJsAttr ?> class="fk-editorial-banner-top text-decoration-none d-block">
                                <?php if (!empty($s['badges']) && is_array($s['badges'])): ?>
                                    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                        <?php foreach ($s['badges'] as $bd): ?>
                                            <span class="badge badge-pill <?= htmlspecialchars($bd['cls'] ?? 'badge-gold') ?> px-3 py-2 shadow-sm badge-wrap-safe">
                                                <?= $bd['html'] ?? '' ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif (!empty($s['eyebrow'])): ?>
                                    <span class="badge badge-pill <?= htmlspecialchars($s['badge_cls'] ?? 'badge-mint') ?> px-3 py-2 mb-3 shadow-sm badge-wrap-safe">
                                        <?= $s['eyebrow'] ?>
                                    </span>
                                <?php endif; ?>
                                <<?= $headingTag ?> class="fk-editorial-banner-title"<?= $titleIdAttr ?>>
                                    <?= $s['title'] ?>
                                </<?= $headingTag ?>>
                            </a>

                            <!-- Thanh đáy thoáng gọn: mô tả 1 dòng + nút thao tác + dấu chấm điều hướng -->
                            <div class="fk-editorial-bottom-dock">
                                <?php if (!empty($s['sub_html'])): ?>
                                    <div class="fk-editorial-dock-breadcrumb"<?= $subIdAttr ?>>
                                        <?= $s['sub_html'] ?>
                                    </div>
                                <?php elseif (isset($s['sub']) && $s['sub'] !== ''): ?>
                                    <p class="fk-editorial-dock-sub d-none d-md-block"<?= $subIdAttr ?>>
                                        <?= htmlspecialchars($s['sub']) ?>
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex flex-wrap align-items-center ml-auto" style="gap: 10px;">
                                    <?php if (!empty($s['cta_text'])): ?>
                                        <?php if (!empty($s['cta_js'])): ?>
                                            <a href="<?= htmlspecialchars($ctaUrl) ?>"<?= $ctaIdAttr ?>
                                               onclick="<?= htmlspecialchars($s['cta_js']) ?>;"
                                               class="<?= htmlspecialchars($ctaBtnCls) ?>">
                                                <?php if (!empty($s['cta_icon'])): ?><i class="fa-solid <?= htmlspecialchars($s['cta_icon']) ?> mr-1"></i><?php endif; ?><?= htmlspecialchars($s['cta_text']) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= htmlspecialchars($ctaUrl) ?>"<?= $ctaIdAttr ?>
                                               class="<?= htmlspecialchars($ctaBtnCls) ?>">
                                                <?php if (!empty($s['cta_icon'])): ?><i class="fa-solid <?= htmlspecialchars($s['cta_icon']) ?> mr-1"></i><?php endif; ?><?= htmlspecialchars($s['cta_text']) ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($s['cta2_text'])): ?>
                                        <?php if (!empty($s['cta2_js'])): ?>
                                            <button type="button"
                                                    onclick="<?= htmlspecialchars($s['cta2_js']) ?>"
                                                    class="btn btn-light btn-sm border rounded-pill px-3 py-1 font-weight-bold text-slate shadow-sm text-nowrap">
                                                <?php if (!empty($s['cta2_icon'])): ?><i class="fa-solid <?= htmlspecialchars($s['cta2_icon']) ?> mr-1 text-peach"></i><?php endif; ?><?= htmlspecialchars($s['cta2_text']) ?>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= htmlspecialchars($s['cta2_url'] ?? '#') ?>"
                                               class="btn btn-light btn-sm border rounded-pill px-3 py-1 font-weight-bold text-slate shadow-sm text-nowrap">
                                                <?php if (!empty($s['cta2_icon'])): ?><i class="fa-solid <?= htmlspecialchars($s['cta2_icon']) ?> mr-1 text-peach"></i><?php endif; ?><?= htmlspecialchars($s['cta2_text']) ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($s['extra_btn'])): ?>
                                        <?= $s['extra_btn'] ?>
                                    <?php endif; ?>

                                    <?php if ($totalSlides > 1): ?>
                                        <div class="fk-slider-dots-bar">
                                            <ul class="fk-slider-dots-list">
                                                <?php for ($d = 0; $d < $totalSlides; $d++): ?>
                                                    <li>
                                                        <button type="button"
                                                                class="fk-slider-dot-btn <?= htmlspecialchars($dotCls) ?> <?= $d === $idx ? 'active' : '' ?>"
                                                                data-target="#<?= htmlspecialchars($carouselId) ?>"
                                                                data-slide-to="<?= $d ?>"
                                                                aria-label="<?= htmlspecialchars($counterLabel) ?> <?= $d + 1 ?>"></button>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                            <span class="fk-slider-counter-text"><?= $counterText ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalSlides > 1): ?>
                <a class="fk-mag-carousel-control fk-mag-carousel-prev" href="#<?= htmlspecialchars($carouselId) ?>" role="button" data-slide="prev" aria-label="Banner trước">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <a class="fk-mag-carousel-control fk-mag-carousel-next" href="#<?= htmlspecialchars($carouselId) ?>" role="button" data-slide="next" aria-label="Banner tiếp theo">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php
    }
}

if (!function_exists('fkGetCompactPromoSlides')) {
    function fkGetCompactPromoSlides(): array
    {
        return [
            [
                'eyebrow'   => '<i class="fa-solid fa-wand-magic-sparkles mr-1"></i>BST Sợi Tre Hè 2026 • Thử Đồ Trực Tiếp',
                'badge_cls' => 'badge-mint',
                'title'     => 'Mặc Đẹp Cho Bé <span class="text-peach">Sợi Tre Êm Dịu</span> &amp; Phong Cách',
                'sub'       => 'Đầm công chúa, bộ thun làm mát -2°C, giao nhanh toàn quốc 48h.',
                'image'     => '/FashionKids/images/banners/theme-shop-tryon-boutique.jpg',
                'alt'       => 'Thử Đồ BST Sợi Tre Bright Pastel 2026',
                'url'       => '/FashionKids/views/pages/shop-catalog.php',
                'cta_text'  => 'Mua Sắm Ngay',
                'cta_icon'  => 'fa-bag-shopping',
                'cta2_text' => 'Tạp Chí Mẫu Nhí',
                'cta2_url'  => '/FashionKids/views/pages/blog-feeds.php',
            ],
            [
                'eyebrow'   => '<i class="fa-solid fa-crown mr-1"></i>Sân Diễn &amp; Casting Đại Sứ Nhí 2026',
                'badge_cls' => 'badge-peach',
                'title'     => 'Siêu Sao <span class="text-peach">KOL Nhí Runway</span> &amp; Gói Quà 15 Triệu',
                'sub'       => 'Tìm kiếm gương mặt nhí tự tin tạo dáng cùng studio chuyên nghiệp.',
                'image'     => '/FashionKids/images/banners/theme-kol-runway-catwalk.jpg',
                'alt'       => 'Sân Diễn Thời Trang KOL Nhí FashionKids 2026',
                'url'       => '/FashionKids/views/pages/kol-stars.php',
                'cta_text'  => 'Xem Sao KOL Nhí',
                'cta_icon'  => 'fa-star',
                'cta2_text' => 'Nộp Hồ Sơ Casting',
                'cta2_url'  => '/FashionKids/views/pages/kol-casting.php',
            ],
            [
                'eyebrow'   => '<i class="fa-solid fa-cake-candles mr-1"></i>Lễ Hội Sinh Nhật Bé Yêu • Mã VIP -15%',
                'badge_cls' => 'badge-gold',
                'title'     => 'Lưu Giữ <span class="text-peach">Khoảnh Khắc Sinh Nhật</span> Cùng Quà Tặng Xinh',
                'sub'       => 'Ưu đãi sinh nhật -15%, thiệp chúc mừng & hộp quà thiết kế riêng cho bé.',
                'image'     => '/FashionKids/images/banners/hero-event-birthday-2026.jpg',
                'alt'       => 'Lễ Hội Sinh Nhật Bé Yêu 2026',
                'url'       => '/FashionKids/views/pages/shop-catalog.php?category=vay_cong_chua',
                'cta_text'  => 'Chọn Quà Cho Bé',
                'cta_icon'  => 'fa-gift',
                'cta2_text' => 'Trang Chủ',
                'cta2_url'  => '/FashionKids/views/pages/home.php',
            ],
            [
                'eyebrow'   => '<i class="fa-solid fa-sparkles mr-1"></i>Xứ Sở Thần Tiên • Lễ Hội Ngôi Sao Nhỏ',
                'badge_cls' => 'badge-sky',
                'title'     => 'Thế Giới <span class="text-peach">Cổ Tích &amp; Studio Quay Phim</span> Nhí',
                'sub'       => 'Tặng kèm 01 thú bông phiên bản giới hạn khi tham gia sự kiện mùa hè.',
                'image'     => '/FashionKids/images/banners/hero-event-littlestar-2026.jpg',
                'alt'       => 'Lễ Hội Ngôi Sao Nhỏ & Xứ Sở Thần Tiên',
                'url'       => '/FashionKids/views/pages/blog-feeds.php',
                'cta_text'  => 'Đọc Tạp Chí Nhí',
                'cta_icon'  => 'fa-book-open',
                'cta2_text' => 'Mua Ngay BST Mới',
                'cta2_url'  => '/FashionKids/views/pages/shop-catalog.php',
            ],
        ];
    }
}

if (!function_exists('fkRenderSidebarPromoCarousel')) {
    function fkRenderSidebarPromoCarousel(string $carouselId, int $intervalMs = 3800): void
    {
        $promoSlides = [
            [
                'badge'     => '<i class="fa-solid fa-fire mr-1"></i>QUẢNG CÁO CHIẾN DỊCH HÈ 2026',
                'badge_cls' => 'badge-peach',
                'title'     => 'BST Sợi Tre Bright Pastel — Mặc Êm Như Mây',
                'desc'      => 'Nhập mã độc quyền dành cho độc giả Tạp chí khi thanh toán đơn từ 200.000đ.',
                'image'     => '/FashionKids/images/banners/promo-mag-bamboo-2026.jpg',
                'alt'       => 'Quảng cáo BST Sợi Tre Bright Pastel 2026',
                'voucher'   => 'FASHION10',
                'cta_url'   => '/FashionKids/views/pages/shop-catalog.php',
                'cta_text'  => 'Mua ngay',
            ],
            [
                'badge'     => '<i class="fa-solid fa-cake-candles mr-1"></i>LỄ HỘI SINH NHẬT BÉ YÊU',
                'badge_cls' => 'badge-gold',
                'title'     => 'Tặng Hộp Quà Sinh Nhật &amp; Giảm 15% BST Công Chúa',
                'desc'      => 'Đầm voan tơ hoa cúc kèm phụ kiện cài tóc đồng bộ rạng rỡ ngày sinh nhật bé.',
                'image'     => '/FashionKids/images/banners/promo-mag-birthday-2026.jpg',
                'alt'       => 'Sự kiện Sinh Nhật Bé Yêu FashionKids',
                'voucher'   => 'FASHION10',
                'cta_url'   => '/FashionKids/views/pages/shop-catalog.php?category=vay_cong_chua',
                'cta_text'  => 'Chọn quà ngay',
            ],
            [
                'badge'     => '<i class="fa-solid fa-crown mr-1"></i>SÂN CHƠI NGÔI SAO NHÍ 2026',
                'badge_cls' => 'badge-mint',
                'title'     => 'Lễ Hội Ngôi Sao Nhỏ &amp; Quà Tặng Thú Bông Độc Quyền',
                'desc'      => 'Đăng ký tuyển chọn KOL nhí để nhận đặc cách Lookbook Studio & học bổng 15 Triệu.',
                'image'     => '/FashionKids/images/banners/promo-mag-kol-2026.jpg',
                'alt'       => 'Lễ Hội Ngôi Sao Nhỏ 2026',
                'voucher'   => '',
                'cta_url'   => '/FashionKids/views/pages/kol-casting.php',
                'cta_text'  => 'Ứng tuyển KOL',
            ],
        ];
        ?>
        <div id="<?= htmlspecialchars($carouselId) ?>"
             class="carousel slide fk-mag-promo-poster fk-auto-carousel"
             data-ride="carousel"
             data-pause="false"
             data-interval="<?= (int)$intervalMs ?>">
            <div class="carousel-inner h-100">
                <?php foreach ($promoSlides as $pIdx => $ps): ?>
                    <div class="carousel-item h-100 <?= $pIdx === 0 ? 'active' : '' ?>">
                        <div class="fk-mag-promo-slide">
                            <img src="<?= htmlspecialchars($ps['image']) ?>"
                                 alt="<?= htmlspecialchars($ps['alt']) ?>"
                                 class="fk-mag-promo-poster-img"
                                 onerror="this.onerror=null;this.src='/FashionKids/images/magazine/mag-catwalk-tuan-le-thoi-trang.jpg';">
                            <div class="fk-mag-promo-scrim"></div>
                            <div class="fk-mag-promo-top-bar">
                                <span class="badge badge-pill <?= htmlspecialchars($ps['badge_cls']) ?> px-3 py-1">
                                    <?= $ps['badge'] ?>
                                </span>
                                <span class="badge badge-pill badge-light text-slate font-weight-bold px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                    <?= ($pIdx + 1) . ' / ' . count($promoSlides) ?>
                                </span>
                            </div>
                            <div class="fk-mag-promo-body">
                                <h5 class="font-weight-bold text-white mb-2">
                                    <?= $ps['title'] ?>
                                </h5>
                                <p class="small mb-3" style="color: rgba(255, 255, 255, 0.9); line-height: 1.45;">
                                    <?= htmlspecialchars($ps['desc']) ?>
                                </p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                                    <?php if (!empty($ps['voucher'])): ?>
                                        <button type="button" onclick="fkCopyMagVoucher('<?= htmlspecialchars($ps['voucher']) ?>', this)" class="fk-mag-voucher-pill" title="Bấm để sao chép mã giảm giá">
                                            <i class="fa-solid fa-ticket"></i>
                                            <span>Mã: <strong><?= htmlspecialchars($ps['voucher']) ?></strong> (-10%)</span>
                                        </button>
                                    <?php else: ?>
                                        <a href="/FashionKids/views/pages/kol-stars.php" class="fk-mag-voucher-pill text-decoration-none">
                                            <i class="fa-solid fa-crown"></i>
                                            <span>Xem Sao KOL Nhí</span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= htmlspecialchars($ps['cta_url']) ?>" class="btn btn-pastel-primary btn-sm rounded-pill px-3 py-2 font-weight-bold shadow-sm text-nowrap">
                                        <?= htmlspecialchars($ps['cta_text']) ?> <i class="fa-solid fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="fk-promo-dots-wrap">
                <?php for ($d = 0; $d < count($promoSlides); $d++): ?>
                    <button type="button"
                            class="fk-promo-dot <?= $d === 0 ? 'active' : '' ?>"
                            data-target="#<?= htmlspecialchars($carouselId) ?>"
                            data-slide-to="<?= $d ?>"
                            aria-label="Banner quảng cáo <?= $d + 1 ?>"></button>
                <?php endfor; ?>
            </div>
            <a class="fk-mag-carousel-control fk-mag-carousel-prev fk-promo-ctrl" href="#<?= htmlspecialchars($carouselId) ?>" role="button" data-slide="prev" aria-label="Quảng cáo trước">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <a class="fk-mag-carousel-control fk-mag-carousel-next fk-promo-ctrl" href="#<?= htmlspecialchars($carouselId) ?>" role="button" data-slide="next" aria-label="Quảng cáo tiếp">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
        <?php
    }
}
