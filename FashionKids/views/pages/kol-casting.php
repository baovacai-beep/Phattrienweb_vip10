<?php
$page_title = "Cổng Tuyển Chọn Siêu Sao Nhí KOL (MH 16)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";

// Danh sách các hồ sơ gương mặt KOL nhí nổi bật
$lnkt_kol_stars = [
    [
        'name'   => 'Bé Nguyễn Bảo An (Sữa)',
        'age'    => '3 tuổi 4 tháng',
        'spec'   => 'Cao 96 cm • Nặng 14.5 kg',
        'talent' => 'Tạo dáng Lookbook, Biểu cảm tự nhiên trước ống kính',
        'badge'  => 'Quán Quân Mùa Xuân',
        'icon'   => 'fa-crown text-warning'
    ],
    [
        'name'   => 'Bé Trần Minh Khôi (Bơ)',
        'age'    => '4 tuổi 2 tháng',
        'spec'   => 'Cao 104 cm • Nặng 16.8 kg',
        'talent' => 'Catwalk năng động, Nhảy hiện đại thiếu nhi',
        'badge'  => 'Gương Mặt Ấn Tượng',
        'icon'   => 'fa-star text-peach'
    ],
    [
        'name'   => 'Bé Lê Hà My (Bông)',
        'age'    => '2 tuổi 6 tháng',
        'spec'   => 'Cao 89 cm • Nặng 12.8 kg',
        'talent' => 'Cười tỏa nắng, Phối đồ Váy công chúa sợi tre',
        'badge'  => 'Thiên Thần Sợi Tre',
        'icon'   => 'fa-heart text-danger'
    ]
];
?>

<div class="container my-4 my-md-5" id="lnkt_kol_casting_section">
    <!-- Banner Giới Thiệu Cổng Casting KOL Nhí (MH 16) -->
    <div class="lnkt-hero-slide lnkt-slide-kol p-4 p-md-5 mb-5 shadow-sm rounded-lg">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                <span class="badge badge-gold px-3 py-2 rounded-pill mb-2 shadow-sm">
                    <i class="fa-solid fa-crown mr-1"></i>Chiến Dịch Tuyển Chọn Đại Sứ Thương Hiệu Nhí 2026 (MH 16)
                </span>
                <h2 class="font-weight-bold text-slate mb-2">
                    Cổng Casting <span class="text-peach">Siêu Sao Nhí KOL</span> FashionKids
                </h2>
                <p class="text-muted-custom mb-0">
                    Tìm kiếm những gương mặt thiên thần nhí từ 0 - 6 tuổi đồng hành cùng Bộ Sưu Tập Vải Sợi Tre Mùa Hè 2026. Nhận ngay thẻ VIP Kim Cương và học bổng tài năng nhí.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right">
                <a href="/FashionKids/views/pages/home.php" class="btn btn-light border rounded-pill px-4 py-2 font-weight-bold text-slate shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-1 text-peach"></i> Về Trang Chủ (MH 01)
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI (7 CỘT): BIỂU MẪU NỘP HỒ SƠ CASTING DÀNH CHO BÉ
             ================================================================ -->
        <div class="col-12 col-lg-7 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-slate mb-0">
                            <i class="fa-solid fa-id-card-clip text-peach mr-2"></i>Biểu Mẫu Đăng Ký Hồ Sơ KOL Nhí
                        </h5>
                        <small class="text-muted-custom">Vui lòng điền đầy đủ thông số của bé và đính kèm từ 2 đến 5 ảnh</small>
                    </div>
                    <span class="badge badge-pill badge-mint px-3 py-1">Đang mở nhận hồ sơ</span>
                </div>

                <div class="card-body p-4 pt-2">
                    <form id="lnkt_formKolCasting" enctype="multipart/form-data">
                        <!-- 1. Họ tên bé & Ngày sinh (Tự động tính tuổi) -->
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_kolBabyName">
                                    Họ và tên của bé (kèm biệt danh) (<span class="text-danger">*</span>):
                                </label>
                                <input type="text" id="lnkt_kolBabyName" name="baby_name" class="form-control"
                                       value="Nguyễn Ngọc Bảo Sam (Bé Sam)" placeholder="VD: Nguyễn Ngọc Bảo Sam (Bé Sam)" required>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small font-weight-bold text-slate mb-0" for="lnkt_kolBirthDate">
                                        Ngày sinh của bé (<span class="text-danger">*</span>):
                                    </label>
                                    <span id="lnkt_kolAutoAgeBadge" class="badge badge-pill badge-sky px-2 py-1">
                                        Tuổi tự động: 2 tuổi 8 tháng
                                    </span>
                                </div>
                                <input type="date" id="lnkt_kolBirthDate" name="birth_date" class="form-control"
                                       value="2024-03-15" onchange="lnktCalculateBabyAge(this.value)" required>
                                <input type="hidden" id="lnkt_kolCalculatedAgeText" name="calculated_age" value="2 tuổi 8 tháng">
                            </div>
                        </div>

                        <!-- 2. Chiều cao, Cân nặng & Giới tính -->
                        <div class="form-row">
                            <div class="form-group col-6 col-md-4">
                                <label class="small font-weight-bold text-slate" for="lnkt_kolHeight">
                                    Chiều cao (<strong>cm</strong>) (<span class="text-danger">*</span>):
                                </label>
                                <div class="input-group">
                                    <input type="number" id="lnkt_kolHeight" name="height_cm" class="form-control font-weight-bold"
                                           value="90" min="45" max="135" step="0.5" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white small font-weight-bold text-peach">cm</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-6 col-md-4">
                                <label class="small font-weight-bold text-slate" for="lnkt_kolWeight">
                                    Cân nặng (<strong>kg</strong>) (<span class="text-danger">*</span>):
                                </label>
                                <div class="input-group">
                                    <input type="number" id="lnkt_kolWeight" name="weight_kg" class="form-control font-weight-bold"
                                           value="13.2" min="3" max="35" step="0.1" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white small font-weight-bold text-peach">kg</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-12 col-md-4">
                                <label class="small font-weight-bold text-slate" for="lnkt_kolGender">
                                    Giới tính của bé:
                                </label>
                                <select id="lnkt_kolGender" name="gender" class="custom-select form-control">
                                    <option value="girl" selected>Bé Gái</option>
                                    <option value="boy">Bé Trai</option>
                                </select>
                            </div>
                        </div>

                        <!-- 3. Năng khiếu nổi bật -->
                        <div class="form-group">
                            <label class="small font-weight-bold text-slate mb-1" for="lnkt_kolTalents">
                                Năng khiếu nổi bật &amp; Sở thích của bé (<span class="text-danger">*</span>):
                            </label>
                            <textarea id="lnkt_kolTalents" name="talents" rows="2" class="form-control mb-2"
                                      placeholder="Mô tả năng khiếu: tạo dáng trước ống kính, biểu diễn catwalk, nhảy múa, cười tươi..." required>Tự tin tạo dáng trước ống kính, thích mặc đầm công chúa sợi tre và nhảy theo nhạc thiếu nhi.</textarea>
                            <div class="d-flex flex-wrap">
                                <button type="button" onclick="lnktAppendKolTalent('Tự tin tạo dáng chụp Lookbook')"
                                        class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small text-slate">
                                    + Tạo dáng Lookbook
                                </button>
                                <button type="button" onclick="lnktAppendKolTalent('Biểu diễn Catwalk nhí')"
                                        class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small text-slate">
                                    + Catwalk nhí
                                </button>
                                <button type="button" onclick="lnktAppendKolTalent('Quay video TikTok/Reels tự nhiên')"
                                        class="btn btn-light btn-sm border rounded-pill mb-1 py-0 px-2 small text-slate">
                                    + Quay TikTok/Reels
                                </button>
                            </div>
                        </div>

                        <!-- 4. Thông tin phụ huynh / Người giám hộ -->
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_kolParentName">
                                    Họ tên Phụ huynh / Người giám hộ (<span class="text-danger">*</span>):
                                </label>
                                <input type="text" id="lnkt_kolParentName" name="guardian_name" class="form-control"
                                       value="Võ Thị Thanh Thảo" required>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate" for="lnkt_kolParentPhone">
                                    Số điện thoại liên hệ (<span class="text-danger">*</span>):
                                </label>
                                <input type="tel" id="lnkt_kolParentPhone" name="guardian_phone" class="form-control"
                                       value="0918889999" required>
                            </div>
                        </div>

                        <!-- 5. BỘ TẢI LÊN TỆP TIN MEDIA (HỖ TRỢ ĐÍNH KÈM TỪ 2 ĐẾN 5 ẢNH CHÂN DUNG & TOÀN THÂN) -->
                        <div class="form-group">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                <label class="small font-weight-bold text-slate mb-0">
                                    Bộ ảnh Media ứng tuyển (Từ <strong>2 đến 5 ảnh</strong> chân dung &amp; toàn thân) (<span class="text-danger">*</span>):
                                </label>
                                <button type="button" onclick="lnktUseDemoKolPhotos()"
                                        class="btn btn-link btn-sm p-0 small font-weight-bold text-peach">
                                    <i class="fa-solid fa-images mr-1"></i>Đính kèm nhanh 3 ảnh mẫu
                                </button>
                            </div>

                            <label for="lnkt_kolMediaInput" class="lbn-upload-dropzone w-100 mb-2">
                                <i class="fa-solid fa-camera-retro text-peach h2 mb-2 d-block"></i>
                                <span id="lnkt_kolMediaStatusTitle" class="small font-weight-bold text-slate d-block">
                                    Nhấn để chọn từ 2 đến 5 ảnh chân dung &amp; toàn thân của bé (JPG, PNG, WEBP)
                                </span>
                                <small class="text-muted-custom">Hỗ trợ chọn nhiều tệp cùng lúc (Giữ phím Ctrl hoặc Shift khi chọn ảnh)</small>
                            </label>
                            <input type="file" id="lnkt_kolMediaInput" name="kol_photos[]" class="d-none"
                                   accept="image/*" multiple onchange="lnktHandleKolMediaUpload(this.files)">

                            <!-- Khung hiển thị danh sách từ 2 đến 5 ảnh đã chọn -->
                            <div id="lnkt_kolMediaPreviewList" class="row mx-n1 mt-2">
                                <div class="col-4 px-1 mb-2">
                                    <div class="p-2 rounded border bg-milk-cream text-center small">
                                        <i class="fa-solid fa-face-smile-beam text-peach h4 mb-1 d-block"></i>
                                        <span class="font-weight-bold text-slate d-block text-truncate">1. chan-dung-be-sam.jpg</span>
                                        <small class="text-success">Ảnh chân dung</small>
                                    </div>
                                </div>
                                <div class="col-4 px-1 mb-2">
                                    <div class="p-2 rounded border bg-milk-cream text-center small">
                                        <i class="fa-solid fa-child-dress text-info h4 mb-1 d-block"></i>
                                        <span class="font-weight-bold text-slate d-block text-truncate">2. toan-than-vay-tre.jpg</span>
                                        <small class="text-success">Ảnh toàn thân</small>
                                    </div>
                                </div>
                                <div class="col-4 px-1 mb-2">
                                    <div class="p-2 rounded border bg-milk-cream text-center small">
                                        <i class="fa-solid fa-camera text-warning h4 mb-1 d-block"></i>
                                        <span class="font-weight-bold text-slate d-block text-truncate">3. goc-nghieng-studio.jpg</span>
                                        <small class="text-success">Góc tự nhiên</small>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="lnkt_kolPhotoCount" value="3">
                        </div>

                        <!-- 6. CHECKBOX XÁC NHẬN CAM KẾT TỪ NGƯỜI GIÁM HỘ HỢP PHÁP -->
                        <div class="form-group p-3 rounded bg-milk-cream border mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="lnkt_guardianConsentCheck" checked required>
                                <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_guardianConsentCheck">
                                    Tôi xác nhận là <strong>người giám hộ hợp pháp</strong> của bé và đồng ý cho phép FashionKids sử dụng hình ảnh đính kèm để xét duyệt Casting &amp; truyền thông chiến dịch Mùa Hè 2026. (<span class="text-danger">*</span>)
                                </label>
                            </div>
                        </div>

                        <button type="submit" id="lnkt_btnSubmitKolCasting" class="btn btn-pastel-primary btn-block font-weight-bold py-3 shadow">
                            <i class="fa-solid fa-paper-plane mr-2"></i>Nộp Hồ Sơ Ứng Tuyển KOL Nhí Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI (5 CỘT): ĐẶC QUYỀN ĐẠI SỨ NHÍ & GƯƠNG MẶT TIÊU BIỂU
             ================================================================ -->
        <div class="col-12 col-lg-5">
            <!-- Đặc quyền KOL Nhí -->
            <div class="card card-pastel p-4 shadow-sm mb-4">
                <h5 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-gift text-peach mr-2"></i>Đặc Quyền Khi Trở Thành KOL Nhí
                </h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-3 d-flex align-items-start">
                        <span class="badge badge-mint p-2 rounded-circle mr-3"><i class="fa-solid fa-check"></i></span>
                        <div>
                            <strong class="text-slate d-block">Tài trợ trang phục sợi tre 1 năm</strong>
                            <span class="text-muted-custom">Nhận toàn bộ thiết kế mới nhất trị giá 15.000.000 VNĐ.</span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <span class="badge badge-gold p-2 rounded-circle mr-3"><i class="fa-solid fa-crown"></i></span>
                        <div>
                            <strong class="text-slate d-block">Đặc cách nâng hạng VIP Kim Cương (-15%)</strong>
                            <span class="text-muted-custom">Tặng ngay 3.500 điểm thưởng tích lũy vĩnh viễn cho tài khoản mẹ.</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <span class="badge badge-sky p-2 rounded-circle mr-3"><i class="fa-solid fa-camera"></i></span>
                        <div>
                            <strong class="text-slate d-block">Xuất hiện trên Banner Trang Chủ (MH 01)</strong>
                            <span class="text-muted-custom">Tham gia buổi chụp Lookbook chuyên nghiệp cùng ekip FashionKids.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Gương mặt KOL Nhí tiêu biểu -->
            <div class="card card-pastel p-4 shadow-sm">
                <h6 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-sparkles text-warning mr-2"></i>Gương Mặt Siêu Sao Nhí Nổi Bật
                </h6>
                <?php foreach ($lnkt_kol_stars as $star): ?>
                    <div class="p-3 rounded bg-milk-cream border mb-3 d-flex align-items-center">
                        <span class="lbn-inv-thumb mr-3 flex-shrink-0">
                            <i class="fa-solid <?= $star['icon'] ?> h4 mb-0"></i>
                        </span>
                        <div>
                            <div class="d-flex flex-wrap align-items-center mb-1">
                                <strong class="text-slate mr-2"><?= htmlspecialchars($star['name']) ?></strong>
                                <span class="badge badge-pill badge-gold small"><?= htmlspecialchars($star['badge']) ?></span>
                            </div>
                            <div class="small font-weight-bold text-peach"><?= htmlspecialchars($star['age']) ?> • <?= htmlspecialchars($star['spec']) ?></div>
                            <small class="text-muted-custom d-block"><?= htmlspecialchars($star['talent']) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once "../layouts/footer.php"; ?>
