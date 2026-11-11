<!-- ==========================================================================
     MODAL TRA CỨU & GỢI Ý SIZE THƯỚC DÂY - MH 04
      (lnkt_) - Stored Function: lnkt_suggest_size
     ========================================================================== -->
<div class="modal fade" id="modalSuggestSize" tabindex="-1" role="dialog" aria-labelledby="lnkt_modalSuggestSizeTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg card-pastel">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0 pt-4 px-4 px-md-5 d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center min-w-0">
                    <span class="lnkt-modal-icon-circle mr-3 shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-ruler-combined text-peach h4 mb-0"></i>
                    </span>
                    <div class="min-w-0">
                        <span class="badge badge-pill badge-sky px-2 py-1 small mb-1 text-nowrap">
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>Tư Vấn Thông Minh
                        </span>
                        <h5 class="modal-title font-weight-bold text-slate mb-0" id="lnkt_modalSuggestSizeTitle">
                            Thước Dây Thông Minh &amp; Gợi Ý Kích Cỡ Cho Bé
                        </h5>
                    </div>
                </div>
                <button type="button" class="close text-slate flex-shrink-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4 p-md-5">
                <p class="small text-muted-custom mb-3">
                    Nhập số đo thực tế của bé (đơn vị: <strong>cm</strong>), hệ thống tự động đối chiếu bảng quy chuẩn hệ thống theo danh mục để trả về kích cỡ vừa vặn nhất.
                </p>

                <!-- Cấu trúc 2 Tab Giao Diện: Quần áo vs Giày dép -->
                <ul class="nav nav-pills nav-pills-custom nav-justified mb-4" id="lnkt_sizeChartTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="lnkt_tabBtnClothing" data-toggle="pill" href="#lnkt_tabPaneClothing"
                           role="tab" aria-controls="lnkt_tabPaneClothing" aria-selected="true"
                           onclick="lnktSwitchSizeTab('quan_ao')">
                            <i class="fa-solid fa-shirt mr-1"></i> Quần áo (Đo theo chiều cao)
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="lnkt_tabBtnShoes" data-toggle="pill" href="#lnkt_tabPaneShoes"
                           role="tab" aria-controls="lnkt_tabPaneShoes" aria-selected="false"
                           onclick="lnktSwitchSizeTab('giay_dep')">
                            <i class="fa-solid fa-shoe-prints mr-1"></i> Giày dép (Đo dài bàn chân)
                        </a>
                    </li>
                </ul>

                <input type="hidden" id="lnkt_activeSizeType" value="quan_ao">

                <!-- Minh họa Thước Dây Ảo Trực Quan (Virtual Tape Measure - Giai đoạn 4) -->
                <div class="lnkt-virtual-tape-box p-3 mb-3 rounded border shadow-sm">
                    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-1">
                        <span class="small font-weight-bold text-slate min-w-0">
                            <i class="fa-solid fa-tape text-peach mr-1"></i> Thước dây ảo đo tự động (50 cm &ndash; 160 cm):
                        </span>
                        <span id="lnkt_tapeValueBadge" class="badge badge-pill badge-peach px-3 py-1 font-weight-bold text-nowrap flex-shrink-0">
                            88 cm
                        </span>
                    </div>
                    <div class="lnkt-tape-track position-relative rounded-pill overflow-hidden my-2" id="lnkt_virtualTapeRuler">
                        <div id="lnkt_tapeProgressFill" class="lnkt-tape-fill"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted-custom fk-fs-075 font-weight-bold">
                        <span>50 cm (Sơ sinh)</span>
                        <span>80 cm</span>
                        <span>105 cm</span>
                        <span>130 cm</span>
                        <span>160 cm (Trẻ lớn)</span>
                    </div>
                </div>

                <!-- Form Thước Dây Gợi Ý Size (AJAX Submit -> ProductController@ajaxSuggestSize) -->
                <form id="lnkt_form_suggest_size" action="/FashionKids/index.php?c=product&amp;a=ajaxSuggestSize" method="POST" novalidate>
                    <input type="hidden" id="lnkt_modal_cat_id" name="ma_danh_muc" value="1">

                <!-- Nội dung 2 Tab -->
                <div class="tab-content" id="lnkt_sizeChartTabContent">
                    <!-- TAB 1: QUẦN ÁO (ĐO THEO CHIỀU CAO) -->
                    <div class="tab-pane fade show active" id="lnkt_tabPaneClothing" role="tabpanel" aria-labelledby="lnkt_tabBtnClothing">
                        <div class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="form-row align-items-end">
                                <div class="form-group col-12 col-md-5 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-slate mb-1" for="lnkt_clothingCategory">
                                        Danh mục trang phục:
                                    </label>
                                    <select id="lnkt_clothingCategory" class="custom-select form-control">
                                        <option value="1" data-stretch="Vải sợi tre phối ren hoa co giãn 4 chiều (+8%), tùng váy xòe rộng thoải mái.">Váy công chúa (Bé gái)</option>
                                        <option value="2" data-stretch="Thun sợi tre Bamboo kháng khuẩn đàn hồi 4 chiều (+12%), bo chun bụng siêu mềm không hằn da.">Bộ thun (Bé trai / Unisex)</option>
                                        <option value="3" data-stretch="Sợi tre dệt kim 2 lớp chống tia UV, phom khoác ngoài rộng rãi dễ mặc lót thêm áo.">Áo khoác (Unisex)</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-4 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-slate mb-1" for="lnkt_inputHeightCm">
                                        Chiều cao của bé (<strong>50 - 160 cm</strong>):
                                    </label>
                                    <div class="input-group">
                                        <input type="text" inputmode="decimal" id="lnkt_inputHeightCm" name="chieu_cao" class="form-control font-weight-bold text-slate"
                                               placeholder="VD: 88 (50 - 160)" value="88" min="50" max="160" step="0.5" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold bg-white text-peach">cm</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-3 mb-0">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="lnkt_chubbyCheck">
                                        <label class="custom-control-label small font-weight-bold text-slate" for="lnkt_chubbyCheck">
                                            Bé bụ bẫm (+1 size)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Gợi ý chọn nhanh số đo chiều cao mẫu -->
                            <div class="mt-3 pt-2 border-top d-flex flex-wrap align-items-center">
                                <span class="small text-muted-custom mr-2 mb-1">Chọn nhanh số đo:</span>
                                <button type="button" onclick="lnktQuickFillMeasure('quan_ao', 65)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">65 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('quan_ao', 78)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">78 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('quan_ao', 88)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small font-weight-bold text-peach">88 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('quan_ao', 102)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">102 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('quan_ao', 115)" class="btn btn-light btn-sm border rounded-pill mb-1 py-0 px-2 small">115 cm</button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: GIÀY DÉP (ĐO DÀI BÀN CHÂN) -->
                    <div class="tab-pane fade" id="lnkt_tabPaneShoes" role="tabpanel" aria-labelledby="lnkt_tabBtnShoes">
                        <div class="p-3 rounded bg-milk-cream border mb-3">
                            <div class="form-row align-items-end">
                                <div class="form-group col-12 col-md-6 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-slate mb-1" for="lnkt_shoesCategory">
                                        Danh mục giày dép:
                                    </label>
                                    <select id="lnkt_shoesCategory" class="custom-select form-control">
                                        <option value="4" data-stretch="Đế cao su non uốn dẻo 360 độ kết hợp lót đệm sợi tre êm ái, quai dán Velcro dễ điều chỉnh độ rộng mu bàn chân.">Giày sandal tập đi &amp; Dạo phố</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-6 mb-0">
                                    <label class="small font-weight-bold text-slate mb-1" for="lnkt_inputFootCm">
                                        Chiều dài bàn chân bé từ gót đến ngón cái (<strong>cm</strong>):
                                    </label>
                                    <div class="input-group">
                                        <input type="text" inputmode="decimal" id="lnkt_inputFootCm" name="dai_chan" class="form-control font-weight-bold text-slate"
                                               placeholder="VD: 13.8 (10 - 25)" value="13.8" min="10.0" max="25.0" step="0.1" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold bg-white text-peach">cm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gợi ý chọn nhanh số đo bàn chân mẫu -->
                            <div class="mt-3 pt-2 border-top d-flex flex-wrap align-items-center">
                                <span class="small text-muted-custom mr-2 mb-1">Chọn nhanh dài chân:</span>
                                <button type="button" onclick="lnktQuickFillMeasure('giay_dep', 12.0)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">12.0 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('giay_dep', 13.0)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">13.0 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('giay_dep', 13.8)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small font-weight-bold text-peach">13.8 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('giay_dep', 15.0)" class="btn btn-light btn-sm border rounded-pill mr-1 mb-1 py-0 px-2 small">15.0 cm</button>
                                <button type="button" onclick="lnktQuickFillMeasure('giay_dep', 16.5)" class="btn btn-light btn-sm border rounded-pill mb-1 py-0 px-2 small">16.5 cm</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nút kích hoạt Gợi Ý Kích Cỡ Ngay (AJAX Submit + Spinner Loading) -->
                <button type="submit" id="lnkt_btnCalculateSize"
                        class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm mb-3">
                    <span id="lnkt_btnSpinner" class="spinner-border spinner-border-sm mr-2 d-none" role="status" aria-hidden="true"></span>
                    <i id="lnkt_btnMagicIcon" class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                    <span id="lnkt_btnCalculateText">Gợi Ý Kích Cỡ Ngay</span>
                </button>
                </form>

                <!-- Hộp thông báo Alert Pastel màu xanh Mint (#E8F5E9) / Cảnh báo lỗi Validation -->
                <div id="lnkt_size_result_box" class="alert lnkt-alert-mint-pastel p-3 mb-3 shadow-sm d-none" role="alert"></div>

                <!-- KHUNG THÔNG BÁO KẾT QUẢ NỔI BẬT TRÊN NỀN XANH MINT (#B8E0D2) -->
                <div id="lnkt_sizeResultContainer" class="lnkt-size-result-mint p-3 p-md-4 mb-4 shadow-sm">
                    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-2">
                        <span class="badge badge-light px-3 py-1 rounded-pill font-weight-bold text-mint-dark shadow-sm text-nowrap">
                            <i class="fa-solid fa-circle-check text-success mr-1"></i> Kết quả gợi ý chuẩn từ hệ thống
                        </span>
                        <span id="lnkt_resultInputSummary" class="small font-weight-bold text-mint-dark text-nowrap">
                            Số đo: Chiều cao 88 cm
                        </span>
                    </div>

                    <div class="row align-items-center mt-2">
                        <div class="col-12 col-md-7 mb-3 mb-md-0">
                            <div class="small text-mint-dark font-weight-bold text-uppercase">Kích cỡ phù hợp nhất cho bé:</div>
                            <div id="lnkt_suggestedSizeBadge" class="h3 font-weight-bold text-mint-dark mb-1">
                                Size 2 (2-3T)
                            </div>
                            <div id="lnkt_suggestedSubSpec" class="small font-weight-bold text-slate mb-1">
                                Khung chuẩn: Cao 82 - 92 cm • Ngực 50 - 54 cm • Eo 48 - 51 cm
                            </div>
                            <p id="lnkt_materialStretchNote" class="small text-mint-dark mb-0">
                                <i class="fa-solid fa-leaf mr-1"></i><strong>Tư vấn độ co giãn chất liệu:</strong> Vải sợi tre Organic Bamboo co giãn 4 chiều tự nhiên (+8% đến +12%), thấm hút mồ hôi gấp 2 lần và không co rút sau khi giặt máy.
                            </p>
                        </div>

                        <div class="col-12 col-md-5 text-md-right">
                            <button type="button" onclick="lnktApplySuggestedSizeToPdp()"
                                    class="btn btn-light font-weight-bold rounded-pill px-4 py-2 shadow-sm border text-mint-dark text-nowrap">
                                <i class="fa-solid fa-check-double text-success mr-1"></i> Chọn Size Này
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bảng tham chiếu nhanh trần/sàn lnkt_size_charts -->
                <div class="border rounded p-3 bg-white">
                    <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-2">
                        <span class="small font-weight-bold text-slate min-w-0">
                            <i class="fa-solid fa-table-list text-peach mr-1"></i> Bảng đối chiếu thông số kích cỡ
                        </span>
                        <?php if (Rbac::isAdmin()): ?>
                        <a href="/FashionKids/views/pages/admin-size-charts.php" class="small font-weight-bold text-peach text-decoration-none text-nowrap flex-shrink-0">
                            Quản trị Bảng Size &raquo;
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center small mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Ký Hiệu Size</th>
                                    <th>Độ Tuổi</th>
                                    <th>Chiều Cao (Quần áo)</th>
                                    <th>Vòng Ngực / Eo</th>
                                    <th>Size Giày / Dài Chân</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold text-peach">Size 1 (0-1T)</td>
                                    <td>0 - 1 tuổi</td>
                                    <td>55 - 72 cm</td>
                                    <td>44-47 / 42-45 cm</td>
                                    <td>Size 21 (11.5 - 12.5 cm)</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-peach">Size 2 (1-2T)</td>
                                    <td>1 - 2 tuổi</td>
                                    <td>73 - 83 cm</td>
                                    <td>48-51 / 46-48 cm</td>
                                    <td>Size 22 (12.6 - 13.4 cm)</td>
                                </tr>
                                <tr class="table-success">
                                    <td class="font-weight-bold text-mint-dark">Size 2 (2-3T) / Size 3</td>
                                    <td>2 - 3 tuổi</td>
                                    <td>84 - 94 cm</td>
                                    <td>52-55 / 49-51 cm</td>
                                    <td>Size 24 (13.5 - 14.4 cm)</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-peach">Size 4 (3-4T)</td>
                                    <td>3 - 4 tuổi</td>
                                    <td>95 - 106 cm</td>
                                    <td>56-59 / 52-54 cm</td>
                                    <td>Size 26 (14.5 - 15.5 cm)</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-peach">Size 5 (5-6T)</td>
                                    <td>5 - 6 tuổi</td>
                                    <td>107 - 122 cm</td>
                                    <td>60-64 / 55-58 cm</td>
                                    <td>Size 28 (15.6 - 17.0 cm)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
