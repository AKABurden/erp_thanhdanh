<?php echo form_open('admin/orders/calPriceOrders', array('id' => 'cal-payroll-payment')); ?>
<div class="modal-dialog modal-lg" style="width: 30%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 td-payment">
                    <div class="sub">
                        <?php if(!empty($data_json)){ ?>
                        <?php foreach($data_json as $key => $value){?>
                        <div class="sb">
                            <div class="col-md-7" style="padding: 0px;">
                                <select name="payrollPayment[]" id="payrollPayment"
                                    class="form-control selectpicker payrollPayment" placeholder="" value=""
                                    style="width: 100%;" title="">
                                    <option value=""></option>
                                    <?php if(!empty($payrollPayments)){ ?>
                                    <?php foreach($payrollPayments as $k => $v){ ?>
                                    <option <?= $v['id'] == $value['payrollPayment'] ? 'selected' : ''?>
                                        data-total="<?= ($v['amount'] -$v['quantity_net'] )?>"
                                        data-subtext="<?= formatMoney($v['amount'] - $v['quantity_net']) .'('. $v['date'] .')' ?>"
                                        value="<?= $v['id'] ?>">
                                        <?= $v['code'] ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4" style="padding: 0px;"><input type="text" style="width: 100%;"
                                    name="total_sub[]" onkeyup="formatNumBerKeyUpCus(this)" id="total_sub"
                                    class="form-control total_sub number-format"
                                    value="<?= formatMoney($value['total_sub']) ?>" title=""></div>
                            <div class="col-md-1" style="padding: 0px;">
                                <div style="height: 30px;display: flex;justify-content: center;align-items: center;"><i
                                        class="fa fa-remove remove-sub pointer text-danger"></i></div>
                            </div>
                            <br><br>
                            <div style="margin-top:5px" class="text-danger show-errors"></div>
                            <div class="check_detail"><input type="hidden" id="product_id"
                                    value="<?= $value['payrollPayment'] ?>" /></div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="row col-md-12"><a class="pointer" onclick="addRowShipping(this)"><i
                                class="fa fa-plus"></i> Thêm tạm ứng</a></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="cal_id" id="cal_id" class="form-control cal_id" value="<?= $cId ?>">
            <input type="hidden" name="staff_id" id="staff_id" class="form-control staff_id" value="<?= $cStaffId ?>">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <span class="btn btn-primary" style="float: right;"
                onclick="handlingCalPayment()"><?= lang('chọn') ?></span>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            &nbsp;
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
var payrollPayments = <?= json_encode($payrollPayments) ?>;
var count_errors = 0;
init_selectpicker();

function getPayrollPayment(select_id) {
    var option = '<option value=""></option>';
    $.each(payrollPayments, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option += '<option data-total="' + (el.amount - el.quantity_net) + '" data-subtext="' + tnhFormatMoney(
                el.amount - el.quantity_net) + '(' + el.date + ')' +
            '" value="' + el.id + '">' + el.code + '</option>';
    });
    return option;
}

function addRowShipping(_this) {
    var div = $(_this).closest('.td-payment');

    html = '<div class="sb">' +
        '<div class="col-md-7" style="padding: 0px;"><select  name="payrollPayment[]" id="payrollPayment" class="form-control selectpicker payrollPayment" placeholder="" value="" style="width: 100%;" title="">' +
        getPayrollPayment() + '</select></div>' +
        '<div class="col-md-4" style="padding: 0px;"><input type="text" style="width: 100%;" name="total_sub[]" onkeyup="formatNumBerKeyUpCus(this)"  id="total_sub" class="form-control total_sub number-format" value="0" title=""></div>' +
        '<div class="col-md-1" style="padding: 0px;"><div style="height: 30px;display: flex;justify-content: center;align-items: center;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>' +
        '<br><br><div style="margin-top:5px" class="text-danger show-errors"></div>' +
        '<div class="check_detail"></div>'
    '</div>';
    div.find('.sub').append(html);
    init_selectpicker();
    totalCalPrices();
}
$(document).on('click', '.remove-sub', function(event) {
    event.preventDefault();
    $(this).closest('.sb').remove();
    totalCalPrices();
});
$(document).on('change', '.total_sub ', function(event) {
    totalCalPrices();
});
$(document).on('keyup', '.total_sub ', function(event) {
    totalCalPrices();
});

$(document).on('change', '.payrollPayment', function(event) {
    event.preventDefault();
    sl = this;
    value = $('option:selected', this).val();
    console.log(value);
    total = $('option:selected', this).attr('data-total');
    $(sl).parent().parent().find('.total_sub').val(tnhFormatMoney(total));

    $(sl).parent().parent().find('.check_detail').html('<input type="hidden" id="product_id" value="0" />');
    if (($('.td-payment .sub .sb').find('input[value=' + value + ']#product_id').length >
            0)) {
        alert_float('warning', "Phiếu tạm ứng này đã được thêm, vui lòng kiểm tra lại!");
        $(this).closest('.sb').remove();
        return;
    }
    $(sl).parent().parent().find('.check_detail').html('<input type="hidden" id="product_id" value="' +
        value +
        '" />');

});


function totalCalPrices() {

    var tbQuote = '.td-payment .sub .sb';
    var nQuote = $(tbQuote).length;
    var totalQuote = 0;
    for (iQuote = 0; iQuote < nQuote; iQuote++) {
        elQuote = $(tbQuote)[iQuote];

        total = intVal($(elQuote).find('option:selected', 'select.payrollPayment').attr('data-total'));

        total_sub = 0;
        $.each($(elQuote).find('.total_sub'), function(index, el) {
            total_sub += intVal($(el).val());
        });

        if (total_sub > total) {
            $(elQuote).find('.show-errors').html('Số tiền phải nhỏ hơn hoặc bằng ' + tnhFormatMoney(total));
            count_errors++;
        } else {
            $(elQuote).find('.show-errors').html('');
            count_errors = 0;
        }

    }

}


function handlingCalPayment() {
    if (count_errors > 0) {
        alert_float('danger', 'Kiểm tra lại dữ liệu');
        return;
    }
    var form = $('#cal-payroll-payment'),
        formData = new FormData(),
        formParams = form.serializeArray();

    $.each(formParams, function(i, val) {
        formData.append(val.name, val.value);
    });

    $.ajax({
            url: site.base_url + 'admin/payroll/handlingCalPayment',
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
        })
        .done(function(data) {
            if (data.dataJSonPayment) {
                cTrChonse.find('.data_json_payment').val(data.dataJSonPayment);
                alert_float('success', '<?= lang('success') ?>');
                $('.modal-dialog .close').trigger('click');
                totalSalary();
            } else {
                cTrChonse.find('.data_json_payment').val('');
                alert_float('danger', '<?= lang('errors') ?>');
            }
        })
        .fail(function() {
            alert_float('danger', 'error');
        });
    return false;
}
</script>