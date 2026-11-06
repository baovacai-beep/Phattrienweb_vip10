<?php
$page_title = "Cổng Casting Siêu Sao Nhí KOL – Gương Mặt Đại Sứ (MH 16)";
require_once "../layouts/header.php";
require_once "../layouts/navbar.php";
?>

<div class="container my-4 my-md-5" id="lnkt_kol_casting_container">
    <!-- Banner Giới Thiệu Chương Trình Tuyển Chọn Siêu Sao Nhí KOL -->
    <div class="card card-pastel lnkt-slide-kol p-4 p-md-5 mb-5 shadow-sm border-0">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <span class="badge badge-pill badge-gold px-3 py-2 mb-3 shadow-sm">
                    <i class="fa-solid fa-crown mr-1"></i> CHIẾN DỊCH TÌM KIẾM ĐẠI SỨ NHÍ 2026 (MH 16)
                </span>
                <h2 class="font-weight-bold text-slate mb-2">
                    Cổng Casting Siêu Sao Nhí KOL – FashionKids Lookbook Hè 2026
                </h2>
                <p class="text-slate mb-0">
                    Dành cho các bé từ 0 đến 6 tuổi yêu thích chụp ảnh và biểu diễn thời trang. Điền hồ sơ thông số của bé và đính kèm từ <strong>02 đến 05 bức ảnh</strong> chân dung &amp; toàn thân rõ nét nhất để tham gia ứng tuyển ngay hôm nay!
                </p>
            </div>
            <div class="col-12 col-lg-4 text-center mt-4 mt-lg-0">
                <div class="p-3 rounded bg-white shadow-sm d-inline-block">
                    <i class="fa-solid fa-star text-warning h2 mb-1"></i>
                    <div class="font-weight-bold text-slate">Học Bổng &amp; Quà Tặng</div>
                    <div class="h5 font-weight-bold text-peach mb-0">10.000.000 VNĐ</div>
                    <small class="text-muted-custom">+ 01 năm mặc đồ sợi tre miễn phí</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ================================================================
             CỘT TRÁI: BIỂU MẪU NỘP HỒ SƠ CASTING DÀNH CHO BÉ
             ================================================================ -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card card-pastel shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0 text-slate">
                        <i class="fa-solid fa-id-card-clip text-peach mr-2"></i>Biểu Mẫu Nộp Hồ Sơ Ứng Tuyển KOL Nhí
                    </h5>
                    <span class="badge badge-pill badge-mint px-3 py-1">Đang mở nhận hồ sơ</span>
                </div>

                <div class="card-body p-4 p-md-5 pt-3">
                    <form id="lnkt_formKolCasting" enctype="multipart/form-data">
                        <!-- 1. THÔNG TIN CƠ BẢN CỦA BÉ & TỰ ĐỘNG TÍNH TUỔI -->
                        <h6 class="font-weight-bold text-slate mb-3 pb-2 border-bottom">
                            1. Thông Tin &amp; Số Đo Hình Thể Của Bé
                        </h6>

                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Họ và tên của bé (<span class="text-danger">*</span>):</label>
                                <input type="text" name="baby_full_name" class="form-control" value="Nguyễn Bảo Sam (Bé Sam)" placeholder="Nhập họ tên đầy đủ của bé..." required>
                            </div>
                            <div class="form-group col-12 col-md-3">
                                <label class="small font-weight-bold text-slate">Ngày sinh của bé (<span class="text-danger">*</span>):</label>
                                <input type="date" id="lnkt_babyDob" name="baby_dob" class="form-control" value="2024-03-15" required>
                            </div>
                            <div class="form-group col-12 col-md-3">
                                <label class="small font-weight-bold text-slate">Độ tuổi (Tự động tính):</label>
                                <input type="text" id="lnkt_babyAgeDisplay" class="form-control bg-milk-cream font-weight-bold text-peach" value="2 tuổi 7 tháng" readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12 col-md-4">
                                <label class="small font-weight-bold text-slate">Giới tính của bé:</label>
                                <select name="baby_gender" class="form-control">
                                    <option value="girl" selected>Bé Gái</option>
                                    <option value="boy">Bé Trai</option>
                                </select>
                            </div>
                            <div class="form-group col-6 col-md-4">
                                <label class="small font-weight-bold text-slate">Chiều cao hiện tại (<span class="text-danger">*</span>):</label>
                                <div class="input-group">
                                    <input type="number" step="0.5" min="45" max="135" name="baby_height" class="form-control font-weight-bold" value="90" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-milk-cream font-weight-bold">cm</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-6 col-md-4">
                                <label class="small font-weight-bold text-slate">Cân nặng hiện tại (<span class="text-danger">*</span>):</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="3" max="35" name="baby_weight" class="form-control font-weight-bold" value="13.5" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-milk-cream font-weight-bold">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-slate">Năng khiếu nổi bật &amp; Tính cách của bé (<span class="text-danger">*</span>):</label>
                            <textarea name="baby_talents" class="form-control" rows="3" placeholder="Ví dụ: Bé dạn ống kính, thích cười tươi, biết tạo dáng catwalk nhí, nhảy theo nhạc thiếu nhi..." required>Bé rất dạn dĩ trước ống kính máy ảnh, hay cười tươi, thích mặc đầm xòe và nhảy theo nhạc thiếu nhi.</textarea>
                        </div>

                        <!-- 2. BỘ TẢI LÊN TỆP TIN MEDIA (HỖ TRỢ ĐÍNH KÈM TỪ 2 ĐẾN 5 ẢNH) -->
                        <h6 class="font-weight-bold text-slate mb-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
                            <span>2. Bộ Tải Lên Tệp Tin Media (Ảnh Chân Dung &amp; Toàn Thân)</span>
                            <span id="lnkt_photoCountBadge" class="badge badge-pill badge-mint px-3 py-1">Hỗ trợ từ 2 đến 5 ảnh</span>
                        </h6>

                        <div class="form-group mb-4">
                            <label for="lnkt_castingPhotos" class="lnkt-upload-zone d-block p-4 text-center mb-3">
                                <i class="fa-solid fa-cloud-arrow-up text-peach display-4 mb-2 d-block"></i>
                                <span class="font-weight-bold text-slate d-block">
                                    Nhấp vào đây để chọn tệp ảnh của bé (Bắt buộc từ 02 đến 05 ảnh)
                                </span>
                                <small class="text-muted-custom">
                                    Bao gồm ít nhất 01 ảnh cận mặt (chân dung) và 01 ảnh dáng đứng toàn thân (Định dạng JPG, PNG, WEBP)
                                </small>
                                <input type="file" id="lnkt_castingPhotos" name="casting_photos[]" class="d-none" accept="image/*" multiple>
                            </label>

                            <!-- Khung hiển thị Preview 2-5 ảnh đính kèm -->
                            <div id="lnkt_photoPreviewContainer" class="d-flex flex-wrap align-items-center">
                                <div class="lnkt-preview-card mr-2 mb-2 shadow-sm text-center p-1">
                                    <div>
                                        <i class="fa-solid fa-face-smile-beam text-peach h4 mb-1"></i>
                                        <div class="small font-weight-bold text-slate">1. Chân dung</div>
                                    </div>
                                </div>
                                <div class="lnkt-preview-card mr-2 mb-2 shadow-sm text-center p-1">
                                    <div>
                                        <i class="fa-solid fa-child-reaching text-info h4 mb-1"></i>
                                        <div class="small font-weight-bold text-slate">2. Toàn thân</div>
                                    </div>
                                </div>
                                <div class="lnkt-preview-card mr-2 mb-2 shadow-sm text-center p-1">
                                    <div>
                                        <i class="fa-solid fa-camera text-success h4 mb-1"></i>
                                        <div class="small font-weight-bold text-slate">3. Góc nghiêng</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. THÔNG TIN PHỤ HUYNH & CHECKBOX XÁC NHẬN CAM KẾT NGƯỜI GIÁM HỘ -->
                        <h6 class="font-weight-bold text-slate mb-3 pb-2 border-bottom">
                            3. Thông Tin Phụ Huynh &amp; Cam Kết Người Giám Hộ Hợp Pháp
                        </h6>

                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Họ tên phụ huynh / Người giám hộ (<span class="text-danger">*</span>):</label>
                                <input type="text" name="guardian_name" class="form-control" value="Võ Thị Thanh Thảo" required>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="small font-weight-bold text-slate">Số điện thoại nhận kết quả Casting (<span class="text-danger">*</span>):</label>
                                <input type="tel" name="guardian_phone" class="form-control" value="0918889999" required>
                            </div>
                        </div>

                        <!-- Checkbox xác nhận cam kết từ người giám hộ hợp pháp -->
                        <div class="p-3 rounded bg-milk-cream border mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="lnkt_guardianConsent" name="guardian_consent" checked required>
                                <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_guardianConsent">
                                    Tôi xác nhận là cha/mẹ hoặc người giám hộ hợp pháp của bé, đồng ý cho phép FashionKids sử dụng hình ảnh dự thi của bé trong khuôn khổ sự kiện Casting KOL Nhí 2026 theo quy định bảo vệ quyền trẻ em.
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-3 shadow-sm">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Gửi Hồ Sơ Đăng Ký Casting Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CỘT PHẢI: QUYỀN LỢI KOL NHÍ & TIÊU CHÍ CHẤM ĐIỂM
             ================================================================ -->
        <div class="col-12 col-lg-4">
            <div class="card card-pastel p-4 shadow-sm mb-4 bg-white">
                <h6 class="font-weight-bold text-slate mb-3">
                    <i class="fa-solid fa-gift text-peach mr-2"></i>Đặc Quyền Đại Sứ Nhí FashionKids
                </h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fa-solid fa-circle-check text-success mt-1 mr-2"></i>
                        <span><strong>Tặng ngay 05 bộ sưu tập sợi tre mùa hè</strong> thiết kế riêng theo số đo chuẩn của bé.</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fa-solid fa-circle-check text-success mt-1 mr-2"></i>
                        <span><strong>Xuất hiện trên Banner Trang Chủ (MH 01)</strong> và Tạp chí Thời trang trẻ em FashionKids.</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fa-solid fa-circle-check text-success mt-1 mr-2"></i>
                        <span><strong>Nâng cấp đặc quyền Thẻ Kim Cương (Diamond VIP)</strong> giảm giá 15% trọn đời cho gia đình.</span>
                    </li>
                </ul>
            </div>

            <div class="card card-pastel p-4 shadow-sm bg-white">
                <h6 class="font-weight-bold text-slate mb-2">
                    <i class="fa-solid fa-ruler-combined text-peach mr-2"></i>Kiểm Tra Size Đồ Mẫu Cho Bé
                </h6>
                <p class="small text-muted-custom mb-3">
                    Ban tổ chức sẽ chuẩn bị sẵn trang phục mẫu trong buổi chụp Studio dựa trên chiều cao của bé.
                </p>
                <button type="button" class="btn btn-pastel-outline btn-block btn-sm font-weight-bold" data-toggle="modal" data-target="#modalSuggestSize">
                    Tra Cứu Size Thước Dây (MH 04)
                </button>
            </div>
        </div>
    </div>
</div>

<?php
require_once "../modals/modal-suggest-size.php";
require_once "../layouts/footer.php";
?>
