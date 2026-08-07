<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(admin_url('outsource/payment_processing/'.$id), array('id' => 'payment-form')); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_payment_processing') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="tab-content true_formation">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="day_vouchers" class="control-label"><?=_l('ch_date_p')?></label>
                            <div class="input-group date">
                                <input type="text" id="day_vouchers" name="day_vouchers"
                                    class="day_vouchers form-control datepicker" value="<?=_d(date('Y-m-d'))?>">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="receiver" class="control-label"><?=_l('ch_receiver')?></label>
                            <input type="text" id="receiver" name="receiver" class="form-control " value="">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <?php echo render_select('payment_mode', $payment_modes, array('id','name'), 'acs_sales_payment_modes_submenu'); ?>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="" class="control-label"><?=_l('ch_total_total')?></label>
                            <input type="text" id="total" name="total" class="form-control "
                                value="<?= formatMoney($rest) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="payment" class="control-label"><?=_l('ch_total_payment')?></label>
                            <input type="text" id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="payment"
                                class="form-control " value="0">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <?php echo render_select('id_costs', $costs, array('id','name'),'ch_costs'); ?>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="note" class="control-label"><?=_l('ch_note_pay_slip')?></label>
                            <textarea rows="5" id="note" name="note" class="form-control " value=""></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
        <div class="modal-footer">
            <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?=_l('submit')?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
$(function() {

    init_selectpicker();
    _validate_form($('#payment-form'), {
        code_vouchers: "required",
        day_vouchers: "required",
        payment_mode: "required",
        payment: "required",
        id_costs: "required",
    }, add_payment_ch);
});

function add_payment_ch(form) {
    var total = unformat_number($('#total').val());
    var payment = unformat_number($('#votes_total').val());
    if (Number(payment) < 0) {
        alert('<?=_l('ch_false_pay_slip')?>');
        return;
    }

    if (Number(payment) > Number(total)) {
        alert('<?=_l('Số tiền thanh toán phải nhỏ hơn ')?>' + formatNumber(total));
        return;
    }

    if ($('#payment-form input.error').length == 0) {
        $('#submit').button('loading');
    }

    var data = $(form).serialize(),
        action = form.action;
    return $.post(action, data).done(function(form) {
        form = JSON.parse(form);
        if (form.success) {
            alert_float(form.alert_type, form.message);
            $('.modal-dialog .close').trigger('click');
            $('#submit').button('reset');
        } else {
            $('#submit').button('reset');
            alert_float(form.alert_type, form.message);
        }
        if (typeof oTable != 'undefined' && oTable != '') {
            oTable.draw('page');
        }
    }), !1
}

function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    x2 = x2.substr(0, 2);
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    return x1 + x2;
};

function unformat_number(number) {
    var _number = 0;
    if (number) {
        _number = number.replace(/[^\-\d\.]/g, '');
    }
    return _number;
};
</script>