<style>
  .wrap-title-payment {
    text-align: center;
    font-size: 18px;
    font-weight: 500;
    text-transform: uppercase;
  }
  .wrap-note-payment {
    text-align: center;
    padding: 15px 0;
  }
  .wrap-text-company {
    font-weight: 500;
  }
</style>
<div class="modal fade in" id="modal_payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <div class="wrap-title-payment">
          đăng ký gia hạn thành công
        </div>
        <div class="wrap-note-payment">
          Vui lòng chuyển khoản với nội dung: <span class="wrap-text-company"><?= get_option('invoice_company_name'); ?></span> vào một trong các tài khoản sau:
        </div>
        <div class="wrap-list-bank">
                <div class="wrap-content-bank">
                    <div class="wrap-img-bank">
                        <img src="<?= base_url('uploads/ACB_logo.png'); ?>">
                    </div>
                    <div class="wrap-text-bank">
                        TÊN TK: CÔNG TY TNHH GIẢI PHÁP PHẦN MỀM FOSO
                    </div>
                    <div class="wrap-text-bank">
                        SỐ TK: 881688
                    </div>
                    <div class="wrap-text-bank">
                        NGÂN HÀNG: Á CHÂU (ACB) - CN: TP.HCM
                    </div>
                </div>
                <div class="wrap-content-bank">
                    <div class="wrap-img-bank">
                        <img src="<?= base_url('uploads/ACB_logo.png'); ?>">
                    </div>
                    <div class="wrap-text-bank">
                        TÊN TK: BÙI PHẠM THANH THUỶ
                    </div>
                    <div class="wrap-text-bank">
                        SỐ TK: 18694 6789
                    </div>
                    <div class="wrap-text-bank">
                        NGÂN HÀNG: Á CHÂU (ACB) - CN: TP.HCM
                    </div>
                </div>
                <div class="wrap-content-bank not-border">
                    <div class="wrap-img-bank">
                        <img src="<?= base_url('uploads/sacombank_logo.png'); ?>">
                    </div>
                    <div class="wrap-text-bank">
                        TÊN TK: BÙI PHẠM THANH THUỶ
                    </div>
                    <div class="wrap-text-bank">
                        SỐ TK: 0600 8673 4888
                    </div>
                    <div class="wrap-text-bank">
                        NGÂN HÀNG: SACOMBANK - CN: LĂNG CHA CẢ
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<script>
</script>