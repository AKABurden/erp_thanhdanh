<div class="modal fade" id="payment" role="dialog">
 <div class="modal-dialog">
  <!-- Modal content-->
  <div class="modal-content">
        <?php 
        echo form_open(admin_url('purchase_order/pay_slip_all/'), array('id' => 'payment-form'));
         ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_pay_slip_all'); ?> </span>
            </h4>
          </div>
        <div class="modal-body" style="height:auto">
            <input id="id_supplierss" class="id_supplierss hide" name="id_supplierss">
            <div class="tab-content true_formation">
             <div class="col-md-6 col-xs-12">
                <input type="text" name="id_old" class="hide" value="<?=$id_old?>">
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
                <input type="hidden"  name="type_invoicesss" class="form-control " value="2">
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
                    <input type="text" id="total" name="total" class="form-control " value="<?=number_format($total)?>" readonly>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="votes_total" class="control-label"><?=_l('ch_total_payment')?></label>
                    <input type="text" id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="payment" class="form-control " readonly value="<?=number_format($total)?>">
                </div>
            </div>
            <div class="clearfix">  </div>
            <div class="col-md-6 col-xs-12">
                <?php echo render_select('id_costs',$costs,array('id','name'),'ch_costs'); ?>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="note" class="control-label"><?=_l('ch_note_pay_slip')?></label>
                    <textarea rows="5" id="note" name="note" class="form-control " value=""></textarea>

                </div>
            </div>
        </div>
        <div class="clearfix">  </div>
    </div>
    <div class="modal-footer">
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
        votes_total: "required",
        id_costs: "required",
    },add_payment);
    });
      function add_payment(form) {
        var total = unformat_number($('#total').val());
        var votes_total = unformat_number($('#votes_total').val());
        if(Number(votes_total) < 0)
        {
            alert('<?=_l('ch_false_pay_slip')?>');return;
        }
        if(Number(votes_total) > Number(total))
        {
            alert('<?=_l('ch_false_pay_slip')?>');return;
        }
        if($('#payment-form input.error').length == 0) {
            $('#submit').button('loading');
        }
         var data = $(form).serialize(),
             action = form.action;
         return $.post(action, data).done(function(form) {
             form = JSON.parse(form),
             alert_float(form.alert_type, form.message);
             
                 tAPI.draw('page');
                 $('#payment').modal('hide');
                 $('#suppliers_id').val('');
                 $('#suppliers_id').change();
                 $('.add_contact_person').popover('hide');

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