<?php
$panelReturnRequests = isset($lnkt_return_requests) && is_array($lnkt_return_requests) ? $lnkt_return_requests : [];
$panelReturnCounts   = isset($returnCounts) && is_array($returnCounts) ? $returnCounts : [
    'all'            => count($panelReturnRequests),
    'cho_duyet_hoan' => 0,
    'da_duyet_hoan'  => 0,
    'dang_thu_hoi'   => 0,
    'hoan_tat_hoan'  => 0,
    'tu_choi_hoan'   => 0,
];
?>
<!-- views/partials/return-management-panel.php -->
<div id="lnkt_returnMgmtPanel">
    <div class="card card-pastel shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center fk-card-header-row mb-3">
                <div class="fk-header-title-grow">
                    <h5 class="font-weight-bold text-slate mb-1">
                        <i class="fa-solid fa-rotate-left text-peach mr-2"></i>Quản Lý Yêu Cầu Hoàn Hàng &amp; Trả Hàng
                    </h5>
                    <small class="text-muted-custom">
                        Duyệt minh chứng hoàn hàng, điều phối bưu tá thu hồi và tự động nhập lại kho + trừ điểm VIP khi hoàn tất
                    </small>
                </div>
                <button type="button" class="btn btn-pastel-outline btn-sm rounded-pill font-weight-bold text-nowrap" onclick="lnktRefreshReturnRequests(false)">
                    <i class="fa-solid fa-rotate mr-1"></i>Làm mới danh sách
                </button>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between fk-card-header-row">
                <div class="d-flex flex-wrap fk-btn-toolbar" id="lnkt_returnStatusFilterPills">
                    <button type="button" class="btn btn-pastel-primary btn-sm rounded-pill font-weight-bold lnkt-ret-filter-pill active" data-ret-status="all" onclick="lnktFilterReturnRequests('all', this)">
                        Tất cả (<span id="lnkt_retCnt_all"><?= (int)($panelReturnCounts['all'] ?? 0) ?></span>)
                    </button>
                    <button type="button" class="btn btn-light border btn-sm rounded-pill font-weight-bold text-slate lnkt-ret-filter-pill" data-ret-status="cho_duyet_hoan" onclick="lnktFilterReturnRequests('cho_duyet_hoan', this)">
                        Chờ duyệt (<span id="lnkt_retCnt_cho_duyet_hoan"><?= (int)($panelReturnCounts['cho_duyet_hoan'] ?? 0) ?></span>)
                    </button>
                    <button type="button" class="btn btn-light border btn-sm rounded-pill font-weight-bold text-slate lnkt-ret-filter-pill" data-ret-status="da_duyet_hoan" onclick="lnktFilterReturnRequests('da_duyet_hoan', this)">
                        Đã duyệt (<span id="lnkt_retCnt_da_duyet_hoan"><?= (int)($panelReturnCounts['da_duyet_hoan'] ?? 0) ?></span>)
                    </button>
                    <button type="button" class="btn btn-light border btn-sm rounded-pill font-weight-bold text-slate lnkt-ret-filter-pill" data-ret-status="dang_thu_hoi" onclick="lnktFilterReturnRequests('dang_thu_hoi', this)">
                        Đang thu hồi (<span id="lnkt_retCnt_dang_thu_hoi"><?= (int)($panelReturnCounts['dang_thu_hoi'] ?? 0) ?></span>)
                    </button>
                    <button type="button" class="btn btn-light border btn-sm rounded-pill font-weight-bold text-slate lnkt-ret-filter-pill" data-ret-status="hoan_tat_hoan" onclick="lnktFilterReturnRequests('hoan_tat_hoan', this)">
                        Đã hoàn tiền (<span id="lnkt_retCnt_hoan_tat_hoan"><?= (int)($panelReturnCounts['hoan_tat_hoan'] ?? 0) ?></span>)
                    </button>
                    <button type="button" class="btn btn-light border btn-sm rounded-pill font-weight-bold text-slate lnkt-ret-filter-pill" data-ret-status="tu_choi_hoan" onclick="lnktFilterReturnRequests('tu_choi_hoan', this)">
                        Đã từ chối (<span id="lnkt_retCnt_tu_choi_hoan"><?= (int)($panelReturnCounts['tu_choi_hoan'] ?? 0) ?></span>)
                    </button>
                </div>

                <div class="fk-header-title-grow">
                    <input type="text" id="lnkt_returnSearchInput" class="form-control form-control-sm rounded-pill"
                           placeholder="Tìm Mã hoàn, Mã đơn, Tên khách, SĐT, Lý do..." onkeyup="lnktApplyReturnClientFilter()">
                </div>
            </div>
        </div>

        <div class="card-body p-3 pt-2">
            <div id="lnkt_returnMgmtAlert" class="alert small py-2 mb-3 d-none" role="alert"></div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Mã Hoàn &amp; Đơn Hàng</th>
                            <th>Khách Hàng &amp; Lý Do</th>
                            <th>Minh Chứng</th>
                            <th class="text-right">Hoàn Tiền &amp; TK Nhận</th>
                            <th class="text-center">Trạng Thái</th>
                            <th class="text-center fk-minw-215">Thao Tác Xử Lý</th>
                        </tr>
                    </thead>
                    <tbody id="lnkt_returnMgmtBody">
                        <?php if (empty($panelReturnRequests)): ?>
                            <tr id="lnkt_returnEmptyRow">
                                <td colspan="6" class="text-center py-4 text-muted-custom">
                                    <i class="fa-solid fa-box-open mr-2"></i>Hiện chưa có yêu cầu hoàn hàng / trả hàng nào từ khách hàng.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($panelReturnRequests as $ret):
                                $rid       = (int)$ret['ma_hoan_hang'];
                                $rcode     = (string)$ret['ma_hoan_code'];
                                $oid       = (int)$ret['ma_don_hang'];
                                $ocode     = (string)$ret['ma_van_don'];
                                $rst       = (string)$ret['trang_thai_hoan'];
                                $rmeta     = $ret['status_meta'] ?? [];
                                $custName  = (string)($ret['khach_hang_ten'] ?? $ret['ten_nguoi_nhan'] ?? 'Khách hàng');
                                $custPhone = (string)($ret['khach_hang_sdt'] ?? $ret['so_dien_thoai_nhan'] ?? '');
                                $imgs      = is_array($ret['hinh_anh_list'] ?? null) ? $ret['hinh_anh_list'] : [];
                                $noteExec  = trim((string)($ret['ghi_chu_xu_ly'] ?? ''));
                                $searchTxt = $rcode . ' ' . $ocode . ' ' . $rid . ' ' . $oid . ' ' . $custName . ' ' . $custPhone . ' ' . ($ret['ly_do_hoan'] ?? '');
                            ?>
                                <tr class="lnkt-return-mgmt-row"
                                    id="lnkt_returnRow_<?= $rid ?>"
                                    data-return-id="<?= $rid ?>"
                                    data-status="<?= htmlspecialchars($rst) ?>"
                                    data-search="<?= htmlspecialchars($searchTxt) ?>">
                                    <td>
                                        <div class="font-weight-bold text-peach text-nowrap">#<?= htmlspecialchars($rcode) ?></div>
                                        <a href="/FashionKids/index.php?c=order&a=tracking&id=<?= $oid ?>" target="_blank" class="small font-weight-bold text-slate text-nowrap">
                                            Đơn #<?= htmlspecialchars($ocode) ?>
                                        </a>
                                        <small class="d-block text-muted-custom"><?= htmlspecialchars((string)$ret['ngay_yeu_cau_formatted']) ?></small>
                                        <?php if (!empty($ret['nhan_vien_xu_ly_ten'])): ?>
                                            <small class="d-block text-mint-dark">NV: <strong><?= htmlspecialchars((string)$ret['nhan_vien_xu_ly_ten']) ?></strong></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-slate"><?= htmlspecialchars($custName) ?></div>
                                        <small class="text-muted-custom d-block mb-1">
                                            <i class="fa-solid fa-phone mr-1"></i><?= htmlspecialchars($custPhone) ?>
                                        </small>
                                        <div class="small font-weight-bold text-danger"><?= htmlspecialchars((string)$ret['ly_do_hoan']) ?></div>
                                        <?php if (!empty($ret['mo_ta_chi_tiet'])): ?>
                                            <small class="text-muted-custom d-block"><?= htmlspecialchars((string)$ret['mo_ta_chi_tiet']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($ret['item_summary'])): ?>
                                            <small class="text-slate d-block mt-1"><i class="fa-solid fa-shirt mr-1 text-peach"></i><?= htmlspecialchars((string)$ret['item_summary']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($imgs)): ?>
                                            <div class="d-flex flex-wrap fk-btn-toolbar">
                                                <?php foreach ($imgs as $imgUrl): ?>
                                                    <img src="<?= htmlspecialchars((string)$imgUrl) ?>"
                                                         alt="Ảnh minh chứng hoàn hàng"
                                                         class="lnkt-return-thumb"
                                                         onclick="lnktOpenReturnLightbox('<?= htmlspecialchars((string)$imgUrl, ENT_QUOTES) ?>')">
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="small text-muted-custom">Không có ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="font-weight-bold text-peach text-nowrap"><?= htmlspecialchars((string)$ret['so_tien_hoan_formatted']) ?></div>
                                        <small class="text-slate d-block font-weight-bold"><?= htmlspecialchars((string)($ret['thong_tin_nhan_tien'] ?? 'Chưa ghi TK')) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill <?= htmlspecialchars((string)($rmeta['badge_class'] ?? 'badge-gold')) ?> px-2 py-1 text-nowrap">
                                            <i class="fa-solid <?= htmlspecialchars((string)($rmeta['icon'] ?? 'fa-hourglass-half')) ?> mr-1"></i><?= htmlspecialchars((string)($rmeta['label'] ?? 'Chờ duyệt hoàn')) ?>
                                        </span>
                                        <?php if ($noteExec !== ''): ?>
                                            <small class="d-block text-muted-custom mt-1">Ghi chú: <?= htmlspecialchars($noteExec) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($rst === 'cho_duyet_hoan'): ?>
                                            <button type="button" class="btn btn-outline-info btn-sm btn-block rounded-pill font-weight-bold mb-1"
                                                    onclick="lnktProcessReturnRequest(<?= $rid ?>, 'da_duyet_hoan', '', this)">
                                                <i class="fa-solid fa-circle-check mr-1"></i>Chấp nhận hoàn hàng
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-block rounded-pill font-weight-bold"
                                                    onclick="lnktOpenReturnRejectModal(<?= $rid ?>, '<?= htmlspecialchars($rcode, ENT_QUOTES) ?>')">
                                                <i class="fa-solid fa-circle-xmark mr-1"></i>Từ chối yêu cầu
                                            </button>
                                        <?php elseif ($rst === 'da_duyet_hoan'): ?>
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-block rounded-pill font-weight-bold mb-1"
                                                    onclick="lnktProcessReturnRequest(<?= $rid ?>, 'dang_thu_hoi', '', this)">
                                                <i class="fa-solid fa-truck-arrow-right mr-1"></i>Shipper đang thu hồi hàng
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm btn-block rounded-pill font-weight-bold"
                                                    onclick="lnktProcessReturnRequest(<?= $rid ?>, 'hoan_tat_hoan', '', this)">
                                                <i class="fa-solid fa-hand-holding-dollar mr-1"></i>Hoàn tiền &amp; Nhập kho
                                            </button>
                                        <?php elseif ($rst === 'dang_thu_hoi'): ?>
                                            <button type="button" class="btn btn-outline-success btn-sm btn-block rounded-pill font-weight-bold"
                                                    onclick="lnktProcessReturnRequest(<?= $rid ?>, 'hoan_tat_hoan', '', this)">
                                                <i class="fa-solid fa-hand-holding-dollar mr-1"></i>Xác nhận hoàn tiền &amp; Nhập lại kho
                                            </button>
                                        <?php else: ?>
                                            <span class="small text-muted-custom">Đã hoàn tất xử lý</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nhập Lý Do Từ Chối Hoàn Hàng -->
<div class="modal fade" id="modalReturnRejectNote" tabindex="-1" role="dialog" aria-labelledby="modalReturnRejectNoteTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-lg shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-0 py-3">
                <h6 class="modal-title font-weight-bold text-danger mb-0" id="modalReturnRejectNoteTitle">
                    <i class="fa-solid fa-circle-xmark mr-2"></i>Từ Chối Yêu Cầu Hoàn Hàng <span id="lnkt_rejectModalCode"></span>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="lnkt_formRejectReturnNote" novalidate>
                <input type="hidden" id="lnkt_rejectReturnId" value="0">
                <div class="modal-body p-4">
                    <div id="lnkt_rejectNoteAlert" class="alert alert-danger d-none small py-2 mb-3"></div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-slate" for="lnkt_rejectReasonNoteInput">
                            Lý do từ chối hoàn trả (hiển thị trực tiếp cho khách hàng) <span class="text-danger">*</span>
                        </label>
                        <textarea id="lnkt_rejectReasonNoteInput" class="form-control rounded-lg" rows="3"
                                  placeholder="VD: Sản phẩm đã qua giặt ủi hoặc quá thời hạn 7 ngày hỗ trợ đổi trả..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light border rounded-pill px-4 font-weight-bold" data-dismiss="modal">Hủy</button>
                    <button type="submit" id="lnkt_btnConfirmRejectReturn" class="btn btn-danger rounded-pill px-4 font-weight-bold">
                        Xác Nhận Từ Chối
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lightbox Xem Ảnh Minh Chứng Hoàn Hàng -->
<div class="modal fade" id="modalReturnImagePreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 rounded-lg shadow-lg bg-white">
            <div class="modal-header border-0 py-2">
                <h6 class="modal-title font-weight-bold text-slate">
                    <i class="fa-regular fa-image text-peach mr-2"></i>Ảnh Minh Chứng Hoàn Hàng
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="lnkt_returnLightboxImg" src="" alt="Ảnh minh chứng hoàn hàng" class="lnkt-lightbox-img">
            </div>
        </div>
    </div>
</div>
