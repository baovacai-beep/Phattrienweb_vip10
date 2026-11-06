<!-- ==========================================
     MH 21: QUẢN TRỊ DUYỆT HỒ SƠ CASTING KOL (TGB_)
     ========================================== -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-slate mb-1"><i class="fas fa-star text-warning mr-2"></i>Xét Duyệt Hồ Sơ Ngôi Sao Nhí KOL</h4>
            <p class="text-muted small mb-0">Sàng lọc hồ sơ bé tham gia chụp lookbook và làm đại sứ độc quyền</p>
        </div>
        <span class="badge badge-peach px-3 py-2 text-white rounded-pill">12 hồ sơ chờ duyệt</span>
    </div>

    <!-- Bộ lọc ứng viên -->
    <div class="card border-0 shadow-sm rounded-lg p-3 mb-4 bg-white">
        <div class="form-row align-items-center">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select class="custom-select rounded-pill">
                    <option selected>Lọc theo độ tuổi (Tất cả)</option>
                    <option value="1">1 - 2 tuổi (Toddler)</option>
                    <option value="2">3 - 4 tuổi (Preschool)</option>
                    <option value="3">5 - 8 tuổi (Kids)</option>
                </select>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select class="custom-select rounded-pill">
                    <option selected>Giới tính (Tất cả)</option>
                    <option value="be_gai">Bé Gái</option>
                    <option value="be_trai">Bé Trai</option>
                </select>
            </div>
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <input type="text" class="form-control rounded-pill" placeholder="Tìm tên bé hoặc SĐT phụ huynh...">
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-peach rounded-pill btn-block font-weight-bold">Lọc ngay</button>
            </div>
        </div>
    </div>

    <!-- Danh sách thẻ hồ sơ (Gallery Card View) -->
    <div class="row">
        <!-- Hồ sơ 1 -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
                <img src="/uploads/casting/sam_1.jpg" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Bé Sam">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="font-weight-bold text-slate mb-0">Hồ Bảo Sam (4 tuổi)</h5>
                        <span class="badge badge-warning px-2 py-1">Chờ duyệt</span>
                    </div>
                    <ul class="list-unstyled text-muted small mb-3">
                        <li><strong>Phụ huynh:</strong> Võ Thị Thanh Thảo (0918889999)</li>
                        <li><strong>Chỉ số:</strong> Cao 96.5cm - Nặng 14.2kg</li>
                        <li><strong>Năng khiếu:</strong> Rất dạn dĩ trước ống kính máy ảnh, múa hát</li>
                    </ul>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3">Từ chối</button>
                        <button type="button" class="btn btn-sm btn-peach rounded-pill px-3 text-white font-weight-bold">Mời phỏng vấn</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>