<!-- MODAL TRA CỨU & GỢI Ý SIZE THƯỚC DÂY - MH 04 (Đảm nhiệm: Lê Nguyễn Khánh Trình - lnkt_) -->
<div class="modal fade" id="modalSuggestSize" tabindex="-1" role="dialog" aria-labelledby="lnkt_modalSizeTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg card-pastel">
            <div class="modal-header border-0 pb-0 pt-4 px-4 px-md-5 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm p-3 bg-milk-cream border mr-3">
                        <i class="fa-solid fa-ruler-combined text-peach h4 mb-0"></i>
                    </span>
                    <div>
                        <h5 class="font-weight-bold mb-0 text-slate" id="lnkt_modalSizeTitle">
                            Tra Cứu &amp; Gợi Ý Size Thước Dây Thông Minh (MH 04)
                        </h5>
                        <small class="text-muted-custom">
                            Thuật toán đối chiếu trực tiếp từ bảng quy chuẩn <code>lnkt_size_charts</code> (Routine: <code>lnkt_suggest_size</code>)
                        </small>
                    </div>
                </div>
                <button type="button" class="close text-slate" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 p-md-5">
                <!-- Cấu trúc Tab giao diện: Tab 1 Quần áo & Tab 2 Giày dép -->
                <ul class="nav nav-pills nav-fill nav-pills-custom mb-4" id="lnkt_sizeTabList" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="lnkt_tabBtnClothing" data-toggle="pill" href="#lnkt_paneClothing" role="tab">
                            <i class="fa-solid fa-shirt mr-1"></i> Quần áo (Đo theo chiều cao)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="lnkt_tabBtnShoes" data-toggle="pill" href="#lnkt_paneShoes" role="tab">
                            <i class="fa-solid fa-shoe-prints mr-1"></i> Giày dép (Đo dài bàn chân)
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- TAB 1: QUẦN ÁO (ĐO THEO CHIỀU CAO) -->
                    <div class="tab-pane fade show active" id="lnkt_paneClothing" role="tabpanel">
                        <form id="lnkt_formSuggestClothing" onsubmit="event.preventDefault(); window.lnktSuggestClothingSize();">
                            <div class="form-row align-items-end">
                                <div class="form-group col-12 col-md-5">
                                    <label class="small font-weight-bold text-slate">Danh mục &amp; Giới tính của bé:</label>
                                    <select id="lnkt_selectClothingGender" class="form-control">
                                        <option value="girl">Đầm &amp; Đồ Bộ Bé Gái</option>
                                        <option value="boy">Áo Thun &amp; Đồ Bộ Bé Trai</option>
                                        <option value="unisex">Bodysuit Sợi Tre Sơ Sinh (Unisex)</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-4">
                                    <label class="small font-weight-bold text-slate">Chiều cao của bé (đơn vị: cm):</label>
                                    <div class="input-group">
                                        <input type="number" step="0.5" min="45" max="135" id="lnkt_inputHeight" class="form-control font-weight-bold" value="90" placeholder="Ví dụ: 90" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold bg-milk-cream">cm</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-3">
                                    <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                                        Gợi Ý Kích Cỡ Ngay
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Khung thông báo kết quả nổi bật trên nền xanh mint (#B8E0D2) -->
                        <div id="lnkt_clothingResultBox" class="lnkt-size-result-box p-4 mt-3 shadow-sm">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                <span class="badge badge-light px-3 py-1 rounded-pill font-weight-bold text-mint-dark shadow-sm">
                                    <i class="fa-solid fa-wand-magic-sparkles text-peach mr-1"></i> KẾT QUẢ GỢI Ý TỰ ĐỘNG
                                </span>
                                <span class="small font-weight-bold">
                                    <i class="fa-solid fa-leaf mr-1"></i> 100% Vải Sợi Tre Kháng Khuẩn
                                </span>
                            </div>
                            <h4 class="font-weight-bold mb-1" id="lnkt_clothingSizeResult">
                                Size 2 (2-3T) — Đầm &amp; Đồ Bộ Bé Gái
                            </h4>
                            <p class="small mb-0" id="lnkt_clothingSizeAdvice">
                                Phù hợp chiều cao 86 - 95 cm. Chất liệu sợi tre làm mát tự nhiên có độ đàn hồi cao (co giãn 4 chiều +3cm biên độ); nếu bé bụ bẫm mẹ có thể chọn nhích lên Size 3 (3-4T).
                            </p>
                        </div>

                        <!-- Bảng tham chiếu nhanh Quần áo -->
                        <div class="table-responsive mt-4">
                            <table class="table table-sm table-bordered text-center small mb-0 bg-white">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Ký Hiệu Size</th>
                                        <th>Độ Tuổi Gợi Ý</th>
                                        <th>Chiều Cao Trần - Sàn (cm)</th>
                                        <th>Vòng Ngực (cm)</th>
                                        <th>Độ Co Giãn Chất Liệu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold">Size 1 (0-1T)</td>
                                        <td>0 - 12 tháng</td>
                                        <td>50.0 - 74.0 cm</td>
                                        <td>44 - 48 cm</td>
                                        <td><span class="badge badge-mint">Co giãn 4 chiều êm dịu</span></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td class="font-weight-bold">Size 2 (2-3T)</td>
                                        <td>2 - 3 tuổi</td>
                                        <td>86.0 - 95.0 cm</td>
                                        <td>52 - 55 cm</td>
                                        <td><span class="badge badge-mint">Đàn hồi tự nhiên (+3cm)</span></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Size 3 (3-4T)</td>
                                        <td>3 - 4 tuổi</td>
                                        <td>96.0 - 105.0 cm</td>
                                        <td>55 - 58 cm</td>
                                        <td><span class="badge badge-mint">Bo chun mềm không hằn</span></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Size 4 (4-5T)</td>
                                        <td>4 - 5 tuổi</td>
                                        <td>106.0 - 115.0 cm</td>
                                        <td>58 - 61 cm</td>
                                        <td><span class="badge badge-mint">Thoáng khí năng động</span></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Size 5 (5-6T)</td>
                                        <td>5 - 6 tuổi</td>
                                        <td>116.0 - 130.0 cm</td>
                                        <td>61 - 65 cm</td>
                                        <td><span class="badge badge-mint">Co giãn vận động mạnh</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: GIÀY DÉP (ĐO DÀI BÀN CHÂN) -->
                    <div class="tab-pane fade" id="lnkt_paneShoes" role="tabpanel">
                        <form id="lnkt_formSuggestShoes" onsubmit="event.preventDefault(); window.lnktSuggestShoeSize();">
                            <div class="form-row align-items-end">
                                <div class="form-group col-12 col-md-5">
                                    <label class="small font-weight-bold text-slate">Dòng sản phẩm giày dép:</label>
                                    <select id="lnkt_selectShoeType" class="form-control">
                                        <option value="sandal">Sandal Tập Đi Đế Mềm Chống Trượt</option>
                                        <option value="sneaker">Giày Sneaker Vải Canvas Họa Tiết</option>
                                        <option value="doll">Giày Búp Bê Quai Dán Bé Gái</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-4">
                                    <label class="small font-weight-bold text-slate">Chiều dài bàn chân bé (đơn vị: cm):</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" min="10" max="22" id="lnkt_inputFootLength" class="form-control font-weight-bold" value="14.2" placeholder="Ví dụ: 14.2" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold bg-milk-cream">cm</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-3">
                                    <button type="submit" class="btn btn-pastel-primary btn-block font-weight-bold py-2 shadow-sm">
                                        Gợi Ý Kích Cỡ Ngay
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Khung thông báo kết quả Giày dép trên nền xanh mint (#B8E0D2) -->
                        <div id="lnkt_shoeResultBox" class="lnkt-size-result-box p-4 mt-3 shadow-sm">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                <span class="badge badge-light px-3 py-1 rounded-pill font-weight-bold text-mint-dark shadow-sm">
                                    <i class="fa-solid fa-shoe-prints text-peach mr-1"></i> KẾT QUẢ GỢI Ý SIZE GIÀY DÉP
                                </span>
                                <span class="small font-weight-bold">
                                    <i class="fa-solid fa-shield-heart mr-1"></i> Đế Cao Su Non Êm Chân
                                </span>
                            </div>
                            <h4 class="font-weight-bold mb-1" id="lnkt_shoeSizeResult">
                                Size 24 (13.6 - 14.5 cm)
                            </h4>
                            <p class="small mb-0" id="lnkt_shoeSizeAdvice">
                                Tư vấn bổ sung: Đế cao su non mềm dẻo chống trượt, quai vải êm co giãn nhẹ. Hệ thống đã cộng trừ hao 0.5 cm để bé mang kèm tất/vớ thoải mái.
                            </p>
                        </div>

                        <!-- Bảng tham chiếu nhanh Giày dép -->
                        <div class="table-responsive mt-4">
                            <table class="table table-sm table-bordered text-center small mb-0 bg-white">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Ký Hiệu Size Giày</th>
                                        <th>Chiều Dài Bàn Chân (cm)</th>
                                        <th>Lòng Giày Khuyến Nghị</th>
                                        <th>Đặc Tính Đế &amp; Quai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold">Size 21</td>
                                        <td>11.5 - 12.5 cm</td>
                                        <td>13.0 cm</td>
                                        <td>Đế mềm tập đi siêu nhẹ</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Size 22</td>
                                        <td>12.6 - 13.5 cm</td>
                                        <td>14.0 cm</td>
                                        <td>Quai dán co giãn êm cổ chân</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td class="font-weight-bold">Size 24</td>
                                        <td>13.6 - 14.5 cm</td>
                                        <td>15.0 cm</td>
                                        <td>Lót đệm thoáng khí kháng khuẩn</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Size 26</td>
                                        <td>14.6 - 15.5 cm</td>
                                        <td>16.0 cm</td>
                                        <td>Đế rãnh chống trượt an toàn</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Size 28</td>
                                        <td>15.6 - 17.0 cm</td>
                                        <td>17.5 cm</td>
                                        <td>Vải canvas mềm co giãn nhẹ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
