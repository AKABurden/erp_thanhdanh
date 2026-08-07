<?php echo form_open('admin/payroll/edit_payroll_payment/'.$id, array('id'=>'edit-payroll-payment')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Sửa phiếu tạm ứng'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày tạm ứng', 'date') ?>
                        <?php $value = _d($payrollPayment['date']); ?>
                        <?php echo render_date_input('date','',$value); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã phiếu', 'code') ?>
                        <?php $code = $payrollPayment['code']?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : $code), 'placeholder="'.lang('Mã phiếu').'" id="code" required readonly class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Nhân viên', 'staff_id') ?>
                        <input type="text" name="staff_id" class="staff_id" id="staff_id"
                            data-placeholder="<?= _l('Nhân viên') ?>" style="width: 100%;">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Số tiền', 'amount') ?>
                        <?php $value = formatMoney($payrollPayment['amount']); ?>
                        <?php echo form_input('amount', (isset($_POST['amount']) ? $_POST['amount'] : $value), 'placeholder="'.lang('Số tiền').'" onkeyup="formatNumBerKeyUpCus(this)" id="amount" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ghi chú', 'note') ?>
                        <textarea class="form-control note" id="note" rows="5"
                            name="note"><?= $payrollPayment['note'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('Sửa') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
$(function() {
    init_datepicker();
    ajaxSelectParams('#staff_id', 'admin/payroll/searchStaffPayment', "<?=$payrollPayment['staff_id']?>", true,
        true);
    appValidateForm($('#edit-payroll-payment'), {
        code: 'required',
        staff_id: 'required',
        amount: 'required',
    }, addPayrollPayment);

    function addPayrollPayment(form) {
        $('.add').attr('disabled', 'disabled');
        amount = intVal($("#amount").val());
        console.log(amount);
        if (amount == 0 || amount < 0) {
            alert('<?=_l('Giá trị lớn hơn 0')?>');
            return;
        }
        var url = form.action;
        var data = $(form).serialize();
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined') {
                        oTable.draw();
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                console.log("error");
            });
        return false;
    }
})
</script>