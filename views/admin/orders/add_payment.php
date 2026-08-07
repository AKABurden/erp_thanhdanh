<?php echo form_open('admin/orders/add_payment/'.$id, array('id' => 'add-payment')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('payment'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('ch_date_p', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y')), 'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_reference_orders', 'reference_no') ?>
                        <input type="text" name="" id="input" class="form-control" value="<?= $order['reference_no'] ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="" id="input" class="form-control" value="<?= $order['customer_name'] ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('staff_coupon', 'staff') ?>
                        <?php echo render_select('staff', $staff, array('staffid','fullname'), ''); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo lang('acs_sales_payment_modes_submenu', 'payment_mode'); ?>
                        <?php echo render_select('payment_mode', $payment_modes, array('id', 'name'), ''); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_total_amount', 'total_amount') ?>
                        <input type="text" name="total_amount" id="total_amount" class="form-control total_amount" value="<?= formatMoney($order['grand_total']) ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_paid', 'paid') ?>
                        <input type="text" name="paid" id="paid" class="form-control paid" value="<?= formatMoney($order['total_payment'] + $order['price_other_expenses']) ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_htlgtcn', 'htlgtcn') ?>
                        <input type="text" name="total_return" id="total_return" class="form-control total_return" value="<?= formatMoney($total_returns['total_return']) ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_payment', 'payment') ?>
                        <?php
                            $payment = $order['grand_total'] - $order['total_payment'] - $order['price_other_expenses'] - $total_returns['total_return'];
                            if ($payment < 0) $payment = 0;
                        ?>
                        <input type="text" name="payment" id="payment" class="form-control payment number-format" value="<?= formatMoney($payment) ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_note', 'note') ?>
                        <textarea name="note" id="note" class="form-control note" rows="2"></textarea>
                    </div>
                </div>
            </div>
		</div>
		<div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    counter = 0;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var count_errors = 0;
    var order_id = '<?= $id ?>';
</script>
<script>
    $(function(){
        // $('#items').select2();
        init_datepicker();
        init_selectpicker();

       	appValidateForm($('#add-payment'), {
            'date': 'required',
            'staff': 'required',
            'payment_mode': 'required',
            'payment': 'required',
        }, payment);

        function payment(form) {

            payment = intVal($('#payment').val());
            total_amount = intVal($('#total_amount').val());
            paid = intVal($('#paid').val());
            total_return = intVal($('#total_return').val());
            total_rest = total_amount - paid - total_return;
            total_rest = formatNumberFixed(total_rest);

            if (payment <= 0) {
                $('.add').removeAttr('disabled', 'disabled');
                alert_float('danger', '<?= lang('Số tiền thanh toán phải lớn hơn 0') ?>');
                return;
            }

            if (payment > total_rest) {
                $('.add').removeAttr('disabled', 'disabled');
                alert_float('danger', '<?= lang('Số tiền thanh toán vượt quá cho phép') ?>');
                return;
            }

        	$('.add').attr('disabled', 'disabled');
            var url = form.action;
            // var data = $(form).serialize();
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
            	url : url,
            	type : 'POST',
            	dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
            	data: formData,
            })
            .done(function(data) {
            	if (data.result) {
            		alert_float('success', data.message);
            		if (typeof oTable != 'undefined' && oTable != '') {
            			oTable.draw();
            		}
            		$('.modal-dialog .close').trigger('click');
            	} else {
            		alert_float('danger', data.message);
            		$('.add').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
            	alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    })
</script>