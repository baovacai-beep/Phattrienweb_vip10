<!-- views/modals/modal-shipping-label.php -->
<div class="modal fade" id="modalShippingLabel" tabindex="-1" role="dialog" aria-labelledby="modalShippingLabelTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg fk-label-modal-content">
            <div class="modal-header bg-white border-bottom fk-no-print d-flex justify-content-between align-items-center fk-card-header-row">
                <h5 class="modal-title font-weight-bold text-slate min-w-0" id="modalShippingLabelTitle">
                    <i class="fa-solid fa-barcode text-peach mr-2"></i>Phiếu Giao Hàng, Hóa Đơn Thu Hộ &amp; Mã Vận Đơn
                </h5>
                <div class="d-flex align-items-center fk-btn-toolbar flex-shrink-0">
                    <button type="button" id="lnkt_btnPrintLabelTop" onclick="lnktPrintShippingLabel()" class="btn btn-pastel-primary btn-sm font-weight-bold text-nowrap">
                        <i class="fa-solid fa-print mr-1"></i>In Phiếu / Lưu PDF
                    </button>
                    <button type="button" class="close text-slate ml-1" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-3 p-md-4 bg-milk-cream">
                <div id="lnkt_labelLoadingState" class="text-center py-5 d-none">
                    <i class="fa-solid fa-spinner fa-spin fa-2x text-peach mb-2"></i>
                    <div class="small font-weight-bold text-slate">Đang tạo mã vạch &amp; trích xuất thông tin thu hộ vận đơn...</div>
                </div>

                <div id="lnkt_labelErrorState" class="alert alert-danger small d-none mb-0"></div>

                <!-- Phiếu Giao Hàng Chuẩn In Dán Lên Gói Hàng -->
                <div id="lnkt_shippingLabelPrintSheet" class="fk-label-sheet bg-white p-3 p-md-4 rounded border">
                    <!-- Hàng 1: Thương hiệu + Mã vạch + QR tra cứu -->
                    <div class="row align-items-center pb-3 mb-3 fk-label-divider">
                        <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <img src="/FashionKids/images/logo-fashionkids.jpg" alt="FashionKids" class="fk-brand-logo mr-2">
                                <div>
                                    <div class="font-weight-bold text-peach fk-label-brand">FASHIONKIDS</div>
                                    <small class="text-muted-custom d-block">Thời trang sợi tre thiên nhiên</small>
                                    <small class="text-slate font-weight-bold">Hotline: 1900 6868</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-8 col-sm-5 text-center">
                            <svg id="lnkt_labelBarcodeSvg" class="fk-barcode-svg" viewBox="0 0 240 54" aria-label="Mã vạch vận đơn"></svg>
                            <div class="font-weight-bold text-slate fk-barcode-text" id="lnkt_labelBarcodeText">FKORD000001</div>
                            <div class="small text-muted-custom">
                                Mã vận đơn: <strong class="text-slate" id="lnkt_labelWaybillCode">#FK-ORD-000001</strong>
                            </div>
                        </div>

                        <div class="col-4 col-sm-3 text-right">
                            <img id="lnkt_labelQrImg" src="" alt="QR Vận Đơn" class="fk-label-qr-img border rounded p-1 bg-white">
                            <small class="d-block text-muted-custom fk-label-qr-cap">Quét tra cứu</small>
                        </div>
                    </div>

                    <div id="lnkt_labelStatusAlert" class="alert small py-2 mb-3 d-none"></div>

                    <!-- Hàng 2: Người gửi & Người nhận -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <div class="p-3 rounded border h-100 fk-label-party-box">
                                <div class="small font-weight-bold text-muted-custom text-uppercase mb-1">
                                    <i class="fa-solid fa-warehouse text-peach mr-1"></i>Bên Gửi (Shop)
                                </div>
                                <div class="font-weight-bold text-slate" id="lnkt_labelSenderName">FASHIONKIDS OFFICIAL</div>
                                <div class="small text-slate">SĐT: <strong id="lnkt_labelSenderPhone">1900 6868</strong></div>
                                <div class="small text-muted-custom" id="lnkt_labelSenderAddr">Kho Vận Hành Thời Trang Sợi Tre FashionKids, TP. Hồ Chí Minh</div>
                                <div class="small text-muted-custom mt-2 pt-2 border-top">
                                    Ngày đặt: <strong class="text-slate" id="lnkt_labelCreatedAt">--</strong><br>
                                    NV xử lý: <strong class="text-slate" id="lnkt_labelHandledBy">--</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded border h-100 fk-label-party-box">
                                <div class="small font-weight-bold text-muted-custom text-uppercase mb-1">
                                    <i class="fa-solid fa-location-dot text-peach mr-1"></i>Người Nhận Hàng
                                </div>
                                <div class="font-weight-bold text-slate h6 mb-1" id="lnkt_labelRecvName">--</div>
                                <div class="small text-slate mb-1">SĐT: <strong class="text-peach h6 mb-0" id="lnkt_labelRecvPhone">--</strong></div>
                                <div class="small text-slate" id="lnkt_labelRecvAddr">--</div>
                                <div class="small text-info mt-2 pt-2 border-top">
                                    <strong>Ghi chú:</strong> <span id="lnkt_labelRecvNote">Không có</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hàng 3: Chi tiết sản phẩm đóng gói -->
                    <div class="mb-3">
                        <div class="small font-weight-bold text-slate text-uppercase mb-1">
                            <i class="fa-solid fa-box-open text-peach mr-1"></i>Nội Dung Hàng Hóa (Được đồng kiểm khi nhận hàng)
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0 small">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center fk-w-35">#</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th class="text-center">Size</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-right">Đơn Giá</th>
                                        <th class="text-right">Thành Tiền</th>
                                    </tr>
                                </thead>
                                <tbody id="lnkt_labelItemsBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hàng 4: Tổng hợp thanh toán & Khung Thu Hộ COD nổi bật -->
                    <div class="row align-items-stretch mb-3">
                        <div class="col-12 col-md-5 mb-2 mb-md-0">
                            <div class="p-3 rounded border bg-white h-100 small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted-custom">Tạm tính:</span>
                                    <strong class="text-slate" id="lnkt_labelSubtotal">0 đ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted-custom">Ưu đãi VIP:</span>
                                    <strong class="text-success" id="lnkt_labelDiscount">0 đ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted-custom">Phí vận chuyển:</span>
                                    <strong class="text-success">Miễn phí (0 đ)</strong>
                                </div>
                                <div class="d-flex justify-content-between pt-2 mt-1 border-top">
                                    <span class="font-weight-bold text-slate">Tổng giá trị đơn:</span>
                                    <strong class="text-slate" id="lnkt_labelTotal">0 đ</strong>
                                </div>
                                <div class="mt-2 pt-2 border-top text-muted-custom" id="lnkt_labelPayMeta">
                                    Phương thức: <strong class="text-slate" id="lnkt_labelPayMethodName">--</strong><br>
                                    <span id="lnkt_labelTxRow" class="d-none">Mã GD MB Bank: <strong class="text-success" id="lnkt_labelTxCode">--</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-7">
                            <div id="lnkt_labelCodBox" class="p-3 rounded border h-100 d-flex flex-column justify-content-center fk-label-cod-box">
                                <div class="small font-weight-bold text-uppercase mb-1" id="lnkt_labelCodTitle">
                                    TIỀN THU HỘ BƯU TÁ (SỐ TIỀN CẦN THANH TOÁN):
                                </div>
                                <div class="fk-label-cod-amount font-weight-bold mb-1" id="lnkt_labelCodAmount">
                                    0 đồng
                                </div>
                                <div class="small font-weight-bold" id="lnkt_labelCodInstruction">
                                    --
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hàng 5: Ký xác nhận bàn giao -->
                    <div class="row pt-2 border-top small text-center text-muted-custom">
                        <div class="col-4">
                            <div class="font-weight-bold text-slate">Nhân viên đóng gói</div>
                            <div class="fk-label-sign-space" id="lnkt_labelPrintTime">--</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold text-slate">Bưu tá giao hàng</div>
                            <div class="fk-label-sign-space">(Ký, ghi rõ họ tên)</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold text-slate">Người nhận xác nhận</div>
                            <div class="fk-label-sign-space">(Kiểm tra hàng nguyên vẹn)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top fk-no-print d-flex flex-wrap fk-btn-toolbar">
                <button type="button" class="btn btn-light border rounded-pill px-4 font-weight-bold text-nowrap" data-dismiss="modal">Đóng</button>
                <button type="button" onclick="lnktPrintShippingLabel()" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold text-nowrap">
                    <i class="fa-solid fa-print mr-2"></i>In Tem &amp; Hóa Đơn Dán Đơn Hàng
                </button>
            </div>
        </div>
    </div>
</div>
