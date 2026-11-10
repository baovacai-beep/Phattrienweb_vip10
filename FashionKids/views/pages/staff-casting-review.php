<?php
require_once __DIR__ . "/../../core/Rbac.php";
Rbac::enforceCurrentViewAccess('staff-casting-review.php');
$isAdminRole = Rbac::isAdmin();
$page_title = "Duyệt Hồ Sơ KOL Nhí / Casting (MH 17)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
?>
<!-- views/pages/staff-casting-review.php -->
<section class="casting-review-section py-4 bg-milk-cream" id="lnkt_kol_review_section">
  <div class="container-fluid px-lg-5">
    <!-- Tiêu đề & Bộ lọc độ tuổi -->
    <div class="row mb-4 align-items-center">
      <div class="col-12 col-md-6">
        <h3 class="font-weight-bold text-slate mb-1">
          <i class="fa-solid fa-crown text-peach mr-2"></i>Duyệt Hồ Sơ KOL Nhí / Casting (MH 17)
        </h3>
        <p class="text-muted-custom small mb-0">
          Kiểm duyệt thông tin, chiều cao, cân nặng và hình ảnh đăng ký người mẫu nhí (<code>sp_lnkt_duyet_ho_so_kol_nhi</code>)
        </p>
      </div>
      <div class="col-12 col-md-6 d-flex flex-wrap justify-content-md-end align-items-center mt-2 mt-md-0">
        <span class="badge badge-pill <?= $isAdminRole ? 'badge-danger' : 'badge-info' ?> px-3 py-2 mr-3">
          <i class="fa-solid fa-user-shield mr-1"></i>
          <?= $isAdminRole ? 'Quyền Tối Cao: Quản Trị Viên (Admin)' : 'Quyền Sơ Tuyển: Nhân Viên Vận Hành (Staff)' ?>
        </span>
        <span class="font-weight-bold small mr-2 text-slate">Nhóm tuổi:</span>
        <div class="btn-group" role="group">
          <button type="button" onclick="lnktFilterKolReviewAge('all', this)" class="btn btn-pastel-primary btn-sm active font-weight-bold lnkt-kol-age-btn">Tất cả</button>
          <button type="button" onclick="lnktFilterKolReviewAge('1-2', this)" class="btn btn-outline-secondary btn-sm font-weight-bold lnkt-kol-age-btn">1 - 2 Tuổi</button>
          <button type="button" onclick="lnktFilterKolReviewAge('3-4', this)" class="btn btn-outline-secondary btn-sm font-weight-bold lnkt-kol-age-btn">3 - 4 Tuổi</button>
          <button type="button" onclick="lnktFilterKolReviewAge('5-6', this)" class="btn btn-outline-secondary btn-sm font-weight-bold lnkt-kol-age-btn">5 - 6 Tuổi</button>
        </div>
      </div>
    </div>

    <div id="lnkt_kolReviewAlert" class="alert d-none small font-weight-bold mb-4" role="alert"></div>

    <!-- Lưới hồ sơ đăng ký -->
    <div class="row">
      <!-- Thẻ hồ sơ bé 1 -->
      <div class="col-12 col-md-6 col-lg-4 mb-4 lnkt-kol-review-card" data-age-group="3-4">
        <div class="card card-pastel border-0 shadow-sm rounded-lg overflow-hidden h-100">
          <img src="/FashionKids/images/banner-mua-he-2026.jpg" class="card-img-top img-product-card" alt="Bé Cherry">
          <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="font-weight-bold text-slate mb-0">Bé Cherry (Bảo Hân)</h5>
              <span class="badge badge-pill badge-gold" id="lnkt_kolStatusBadge_1">Chờ duyệt • 3 Tuổi</span>
            </div>
            <div class="text-muted-custom small mb-3">
              <div><i class="fas fa-ruler-vertical mr-2 text-peach"></i>Chiều cao: 96 cm</div>
              <div><i class="fas fa-weight mr-2 text-peach"></i>Cân nặng: 14.5 kg</div>
              <div><i class="fas fa-phone mr-2 text-peach"></i>Phụ huynh: Chị Lan (0912.xxx.789)</div>
            </div>
            <p class="small text-slate flex-grow-1">
              "Bé rất dạn dĩ trước ống kính, từng có kinh nghiệm chụp mẫu đồ Tết và biểu cảm tự nhiên."
            </p>
            <!-- Nút xử lý nghiệp vụ phân quyền RBAC: Staff chỉ có "Mời phỏng vấn", Admin có thêm "Quyết định Trúng tuyển / Từ chối" -->
            <div class="pt-3 border-top">
              <div class="row no-gutters">
                <div class="<?= $isAdminRole ? 'col-4 pr-1' : 'col-12' ?>">
                  <button type="button" onclick="lnktHandleKolReviewAction(1, 'moi_phong_van', 'Bé Cherry (Bảo Hân)')" class="btn btn-pastel-sky btn-sm btn-block rounded-pill font-weight-bold" title="Sơ tuyển: Mời phỏng vấn & ghi hình Studio">
                    <i class="fas fa-camera mr-1"></i>Mời phỏng vấn
                  </button>
                </div>
                <?php if ($isAdminRole): ?>
                  <div class="col-4 px-1 fk-admin-only-action">
                    <button type="button" onclick="lnktHandleKolReviewAction(1, 'trung_tuyen', 'Bé Cherry (Bảo Hân)')" class="btn btn-success btn-sm btn-block rounded-pill font-weight-bold" title="Quyết định Trúng tuyển (sp_lnkt_duyet_ho_so_kol_nhi)">
                      <i class="fas fa-check mr-1"></i>Trúng tuyển
                    </button>
                  </div>
                  <div class="col-4 pl-1 fk-admin-only-action">
                    <button type="button" onclick="lnktHandleKolReviewAction(1, 'tu_choi', 'Bé Cherry (Bảo Hân)')" class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold" title="Quyết định Từ chối hồ sơ">
                      <i class="fas fa-times mr-1"></i>Từ chối
                    </button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Thẻ hồ sơ bé 2 -->
      <div class="col-12 col-md-6 col-lg-4 mb-4 lnkt-kol-review-card" data-age-group="5-6">
        <div class="card card-pastel border-0 shadow-sm rounded-lg overflow-hidden h-100">
          <img src="/FashionKids/images/banner-kol-nhi-2026.jpg" class="card-img-top img-product-card" alt="Bé Ken">
          <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="font-weight-bold text-slate mb-0">Bé Ken (Gia Khiêm)</h5>
              <span class="badge badge-pill badge-mint" id="lnkt_kolStatusBadge_2">Chờ duyệt • 5 Tuổi</span>
            </div>
            <div class="text-muted-custom small mb-3">
              <div><i class="fas fa-ruler-vertical mr-2 text-peach"></i>Chiều cao: 110 cm</div>
              <div><i class="fas fa-weight mr-2 text-peach"></i>Cân nặng: 18.0 kg</div>
              <div><i class="fas fa-phone mr-2 text-peach"></i>Phụ huynh: Anh Hùng (0988.xxx.123)</div>
            </div>
            <p class="small text-slate flex-grow-1">
              "Bé năng động, yêu thích trang phục thể thao đường phố, tự tin tạo dáng theo nhạc."
            </p>
            <div class="pt-3 border-top">
              <div class="row no-gutters">
                <div class="<?= $isAdminRole ? 'col-4 pr-1' : 'col-12' ?>">
                  <button type="button" onclick="lnktHandleKolReviewAction(2, 'moi_phong_van', 'Bé Ken (Gia Khiêm)')" class="btn btn-pastel-sky btn-sm btn-block rounded-pill font-weight-bold" title="Sơ tuyển: Mời phỏng vấn">
                    <i class="fas fa-camera mr-1"></i>Mời phỏng vấn
                  </button>
                </div>
                <?php if ($isAdminRole): ?>
                  <div class="col-4 px-1 fk-admin-only-action">
                    <button type="button" onclick="lnktHandleKolReviewAction(2, 'trung_tuyen', 'Bé Ken (Gia Khiêm)')" class="btn btn-success btn-sm btn-block rounded-pill font-weight-bold" title="Quyết định Trúng tuyển (sp_lnkt_duyet_ho_so_kol_nhi)">
                      <i class="fas fa-check mr-1"></i>Trúng tuyển
                    </button>
                  </div>
                  <div class="col-4 pl-1 fk-admin-only-action">
                    <button type="button" onclick="lnktHandleKolReviewAction(2, 'tu_choi', 'Bé Ken (Gia Khiêm)')" class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold" title="Quyết định Từ chối hồ sơ">
                      <i class="fas fa-times mr-1"></i>Từ chối
                    </button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Thẻ hồ sơ bé 3 (Seed CSDL: Hồ Bảo Sam) -->
      <div class="col-12 col-md-6 col-lg-4 mb-4 lnkt-kol-review-card" data-age-group="3-4">
        <div class="card card-pastel border-0 shadow-sm rounded-lg overflow-hidden h-100">
          <img src="/FashionKids/images/vay-cong-chua-hoa-cuc.jpg" class="card-img-top img-product-card" alt="Bé Sam">
          <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="font-weight-bold text-slate mb-0">Bé Hồ Bảo Sam</h5>
              <span class="badge badge-pill badge-sky" id="lnkt_kolStatusBadge_3">Chờ duyệt • 4 Tuổi</span>
            </div>
            <div class="text-muted-custom small mb-3">
              <div><i class="fas fa-ruler-vertical mr-2 text-peach"></i>Chiều cao: 96.5 cm</div>
              <div><i class="fas fa-weight mr-2 text-peach"></i>Cân nặng: 14.2 kg</div>
              <div><i class="fas fa-phone mr-2 text-peach"></i>Phụ huynh: Võ Thị Thanh Thảo (0918889999)</div>
            </div>
            <p class="small text-slate flex-grow-1">
              "Bé dạn dĩ trước máy ảnh, thích tạo dáng công chúa lookbook sợi tre."
            </p>
            <div class="pt-3 border-top">
              <div class="row no-gutters">
                <div class="<?= $isAdminRole ? 'col-4 pr-1' : 'col-12' ?>">
                  <button type="button" onclick="lnktHandleKolReviewAction(3, 'moi_phong_van', 'Bé Hồ Bảo Sam')" class="btn btn-pastel-sky btn-sm btn-block rounded-pill font-weight-bold" title="Sơ tuyển: Mời phỏng vấn">
                    <i class="fas fa-camera mr-1"></i>Mời phỏng vấn
                  </button>
                </div>
                <?php if ($isAdminRole): ?>
                  <div class="col-4 px-1 fk-admin-only-action">
                    <button type="button" onclick="lnktHandleKolReviewAction(3, 'trung_tuyen', 'Bé Hồ Bảo Sam')" class="btn btn-success btn-sm btn-block rounded-pill font-weight-bold" title="Quyết định Trúng tuyển (sp_lnkt_duyet_ho_so_kol_nhi)">
                      <i class="fas fa-check mr-1"></i>Trúng tuyển
                    </button>
                  </div>
                  <div class="col-4 pl-1 fk-admin-only-action">
                    <button type="button" onclick="lnktHandleKolReviewAction(3, 'tu_choi', 'Bé Hồ Bảo Sam')" class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold" title="Quyết định Từ chối hồ sơ">
                      <i class="fas fa-times mr-1"></i>Từ chối
                    </button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
