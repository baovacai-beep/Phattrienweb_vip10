<!-- views/modals/modal-return-request.php -->
<div class="modal fade" id="modalReturnRequest" tabindex="-1" role="dialog" aria-labelledby="modalReturnRequestTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content border-0 rounded-lg shadow-lg overflow-hidden">
      <div class="modal-header lnkt-return-modal-hdr border-0 py-3">
        <div>
          <h5 class="modal-title font-weight-bold text-slate mb-0" id="modalReturnRequestTitle">
            <i class="fa-solid fa-rotate-left text-peach mr-2"></i>Yêu Cầu Hoàn Hàng / Trả Hàng
          </h5>
          <small class="text-muted-custom">Hỗ trợ đổi trả miễn phí trong 7 ngày theo chính sách FashionKids</small>
        </div>
        <button type="button" class="close text-slate" data-dismiss="modal" aria-label="Đóng">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="lnkt_formReturnRequest" method="POST" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="order_id" id="lnkt_returnOrderId" value="0">
        <div class="modal-body p-4">
          <div class="p-3 rounded bg-milk-cream border mb-3 d-flex flex-wrap justify-content-between align-items-center fk-card-header-row">
            <div class="small">
              <span class="text-muted-custom">Đơn hàng yêu cầu hoàn trả:</span>
              <strong class="text-slate ml-1" id="lnkt_returnModalOrderCode">#FK-ORD-000000</strong>
            </div>
            <div class="small">
              <span class="text-muted-custom">Số tiền dự kiến hoàn:</span>
              <strong class="text-peach ml-1" id="lnkt_returnModalRefundAmount">0 đ</strong>
            </div>
          </div>

          <div id="lnkt_returnFormAlert" class="alert d-none small py-2 mb-3" role="alert"></div>

          <div class="form-group">
            <label class="font-weight-bold small text-slate" for="lnkt_returnReason">
              1. Lý do hoàn hàng / trả hàng <span class="text-danger">*</span>
            </label>
            <select class="custom-select rounded-lg" id="lnkt_returnReason" name="ly_do_hoan" required>
              <option value="">-- Chọn lý do cụ thể --</option>
              <option value="Sản phẩm bị lỗi đường may / rách / bẩn">Sản phẩm bị lỗi đường may / rách / bẩn</option>
              <option value="Giao sai mẫu mã hoặc sai kích cỡ (size) đã đặt">Giao sai mẫu mã hoặc sai kích cỡ (size) đã đặt</option>
              <option value="Mặc không vừa kích cỡ của bé, muốn hoàn trả">Mặc không vừa kích cỡ của bé, muốn hoàn trả</option>
              <option value="Thiếu sản phẩm hoặc phụ kiện đi kèm">Thiếu sản phẩm hoặc phụ kiện đi kèm</option>
              <option value="Chất lượng sản phẩm không đúng mô tả">Chất lượng sản phẩm không đúng mô tả</option>
              <option value="Lý do khác">Lý do khác</option>
            </select>
          </div>

          <div class="form-group">
            <label class="font-weight-bold small text-slate" for="lnkt_returnDescription">
              2. Mô tả chi tiết tình trạng sản phẩm <span class="text-danger">*</span>
            </label>
            <textarea class="form-control rounded-lg" id="lnkt_returnDescription" name="mo_ta_chi_tiet" rows="3"
                      placeholder="Vui lòng mô tả rõ tình trạng sản phẩm thực tế khi mở hộp (tem mác, lỗi kỹ thuật hoặc size thực tế...)" required></textarea>
          </div>

          <div class="form-group">
            <label class="font-weight-bold small text-slate" for="lnkt_returnBankInfo">
              3. Thông tin tài khoản Ngân hàng / Ví điện tử nhận tiền hoàn <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control rounded-lg" id="lnkt_returnBankInfo" name="thong_tin_nhan_tien"
                   placeholder="VD: MB Bank - 0913672861 - NGUYEN VAN A (hoặc Ví MoMo SĐT)" required>
            <small class="form-text text-muted-custom">FashionKids sẽ hoàn 100% số tiền thanh toán về tài khoản này ngay sau khi thu hồi hàng.</small>
          </div>

          <div class="form-group mb-0">
            <label class="font-weight-bold small text-slate" for="lnkt_returnImagesInput">
              4. Hình ảnh minh chứng thực tế (Từ 1 đến 3 ảnh JPG, PNG, WEBP, tối đa 5MB/ảnh) <span class="text-danger">*</span>
            </label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="lnkt_returnImagesInput" name="evidence_images[]"
                     multiple accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" required>
              <label class="custom-file-label rounded-lg" id="lnkt_returnImagesLabel" for="lnkt_returnImagesInput">
                Chọn 1 - 3 ảnh chụp sản phẩm thực tế &amp; tem mác...
              </label>
            </div>
            <div id="lnkt_returnPreviewGrid" class="d-flex flex-wrap align-items-center mt-3 fk-btn-toolbar d-none"></div>
          </div>
        </div>

        <div class="modal-footer border-0 p-3 bg-light">
          <button type="button" class="btn btn-light border rounded-pill px-4 font-weight-bold" data-dismiss="modal">Đóng</button>
          <button type="submit" id="lnkt_btnSubmitReturn" class="btn btn-pastel-primary rounded-pill px-4 font-weight-bold shadow-sm">
            <i class="fa-solid fa-paper-plane mr-1"></i>Gửi Yêu Cầu Hoàn Hàng
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
