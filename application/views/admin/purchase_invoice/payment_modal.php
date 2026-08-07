<div class="modal fade" id="payment" role="dialog">
 <div class="modal-dialog">
  <!-- Modal content-->
  <div class="modal-content">
        <?php 
        echo form_open(admin_url('purchase_invoice/pay_slip/'.$id_invoice), array('id' => 'payment-form'));
         ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_pay_slip'); ?> </span>
            </h4>
          </div>
        <div class="modal-body" style="height:auto">
            <div class="tab-content true_formation">
             <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="day_vouchers" class="control-label"><?=_l('ch_date_p')?></label>
                    <div class="input-group date">
                        <input type="text" id="day_vouchers" name="day_vouchers" class="day_vouchers form-control datepicker" value="<?=_d(date('Y-m-d'))?>">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar calendar-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <input type="hidden" id="id" name="" class="form-control " value="">
                <div class="form-group">
                    <label for="code_vouchers" class="control-label"><?=_l('ch_code_p')?></label>
                    <input type="text" id="code_vouchers" name="code_vouchers" class="form-control " readonly value="<?=$code?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="receiver" class="control-label"><?=_l('ch_receiver')?></label>
                    <input type="text" id="receiver" name="receiver" class="form-control " value="">
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                  <div class="form-group">
                    <?php echo render_select('payment_mode',$payment_modes,array('id','name'),'acs_sales_payment_modes_submenu'); ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="" class="control-label"><?=_l('ch_total_total')?></label>
                    <input type="text" id="total" name="total" class="form-control " value="<?=number_format($invoice->total_price_befor_vat - $invoice->amount_paid - $invoice->price_other_expenses)?>" readonly>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="payment" class="control-label"><?=_l('ch_total_payment')?></label>
                    <input type="text" id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="payment" class="form-control " value="<?=number_format($invoice->total_price_befor_vat - $invoice->amount_paid - $invoice->price_other_expenses)?>" readonly>
                </div>
            </div>
            <div class="clearfix">  </div>
            <div class="col-md-6 col-xs-12">
                <?php echo render_select('id_costs',$costs,array('id','name'),'ch_costs'); ?>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="note" class="control-label"><?=_l('ch_note_pay_slip')?></label>
                    <textarea rows="8" id="note" name="note" class="form-control " value=""></textarea>

                </div>
            </div>
        </div>
        <div class="clearfix">  </div>
    </div>
    <div class="modal-footer">
        <!-- data-loading-text="<?=_l('wait_text')?>" -->
        <button type="submit" class="btn btn-info"  id="submit" autocomplete="off"><?=_l('submit')?></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
        <!--  -->
    </div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
    $(function(){
    _validate_form($('#payment-form'), {
        code_vouchers: "required",
        day_vouchers: "required",
        receiver: "required",
        payment_mode: "required",
        payment: "required",
    },add_payment);
    });
      function add_payment(form) {
        var total = unformat_number($('#total').val());
        var payment = unformat_number($('#votes_total').val());
        if(Number(payment) < 0)
        {
            alert('Giá trị tiền thanh toán không hợp lệ!');return;
        }
        if(Number(payment) > Number(total))
        {
            alert('Giá trị tiền thanh toán không hợp lệ!');return;
        }
        if($('#payment-form input.error').length == 0) {
            $('#submit').button('loading');
        }
         var data = $(form).serialize(),
             action = form.action;
         return $.post(action, data).done(function(form) {
             form = JSON.parse(form),
             alert_float(form.alert_type, form.message);
             if(form.success)
             {
                 tAPI.draw('page');
                 $('#payment').modal('hide');
             }

         }), !1
      }
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2=x2.substr(0,2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };
    function unformat_number(number)
    {
        var _number=0;
        if(number)
        {
            _number=number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
</script>