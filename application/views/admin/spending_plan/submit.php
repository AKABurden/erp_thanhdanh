<?php echo form_open('admin/spending_plan/submit/'.($id ?? ''), array('id' => 'submit-form')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('spending_plan') ?></h4>
        </div>
        <div class="modal-body">
            <table style="table-layout: fixed;" class="dt-tnh table table-bordered table-hover mtop0 mbot0">
                <tbody>
                    <tr>
                        <td style="width: 20%;">
                            <label for="code" class="control-label">
                                <?php echo _l('ch_code_p'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <div class="form-group">
                                <input type="text" id="code" name="code" class="form-control " readonly value="<?= $value['code'] ?? 'Tự động hệ thống' ?>">
                            </div>
                        </td>
                        <td style="width: 20%;">
                            <label for="date" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('ch_date_p'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_date_input('date', '', (!empty($value['date']) ? _d($value['date']) : date('d/m/Y'))) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="group_plan" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('Nhóm kế hoạch'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_select('group_plan', $arrGroupPlan, ['code', 'name'], '', $value['group_plan'] ?? '') ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="detail" class="control-label">
                                <?php echo _l('tnh_detail'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('detail', '', $value['detail'] ?? '') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="receiver" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('Người tiếp nhận'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('receiver', '', $value['receiver'] ?? '') ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="approve_staff_id" class="control-label">
                                <?php echo _l('Người duyệt'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_select('approve_staff_id', $arrStaff, ['id', 'full_name'], '', $value['approve_staff_id'] ?? '') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="spending_staff_id" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('Người chi'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_select('spending_staff_id', $arrStaff, ['id', 'full_name'], '', $value['spending_staff_id'] ?? get_staff_user_id()) ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="price" class="control-label">
                                <?php echo _l('Số tiền chi'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('price', '', (!empty($value['price']) ? formatMoney($value['price']) : 0), 'text', ['onchange'=>'calculateAmount()'], [], '', 'number-format') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="tax_id" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('Thuế VAT'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?php foreach($arrTax as $rowIndex => $tax) {
                                $arrTax[$rowIndex]['option_attributes'] = ['data-tax_rate' => $tax['taxrate']];
                            } ?>
                            <?= render_select('tax_id', $arrTax, ['id', 'name'], '', $value['tax_id'] ?? '', ['onchange'=>'calculateAmount()']) ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="amount" class="control-label">
                                <?php echo _l('Tổng tiền (VNĐ)'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('amount', '', (!empty($value['amount']) ? formatMoney($value['amount']) : 0), 'text', ['readonly'=>true], [], '', 'number-format') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="payment_method_id" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('Hình thức thanh toán'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_select('payment_method_id', $arrPaymentMethod, ['id', 'name'], '', $value['payment_method_id'] ?? '') ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="amount" class="control-label">
                                <?php echo _l('Tiền tệ'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_select('currency_id', $arrCurrency, ['id', 'name'], '', $value['currency_id'] ?? '') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="exchange_rate" class="control-label">
                                <?php echo _l('Tỷ giá'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('exchange_rate', '', (!empty($value['exchange_rate']) ? formatMoney($value['exchange_rate']) : ''), 'text', ['onchange'=>'calculateAmount()'], [], '', 'number-format') ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="category_spend" class="control-label">
                                <!-- <small class="req text-danger">* </small> -->
                                <?php echo _l('Mục chi'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('category_spend', '', ($value['category_spend'] ?? '')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            <label for="expense" class="control-label">
                                <?php echo _l('Khoản chi'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_input('expense', '', ($value['expense'] ?? '')) ?>
                        </td>
                        <td style="width: 20%;">
                            <label for="deadline" class="control-label">
                                <?php echo _l('Thời hạn hoàn thành'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <?= render_date_input('deadline', '', (!empty($value['deadline']) ? _d($value['deadline']) : '')) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('submit') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    // var id = "<?= (!empty($value['id']) ? $value['id'] : ''); ?>";
    $(document).ready(function() {
        init_selectpicker();
        init_datepicker();
    });

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };

    function calculateAmount() {
        var price = $('#price').val() ?? 0;
        var exchange_rate = $('#exchange_rate').val() ?? 0;
        var selectTax = $('#tax_id').find('option:selected');
        var tax_rate = selectTax.data('tax_rate') ?? 0;

        if (!exchange_rate) exchange_rate = 0;
        if (!tax_rate) tax_rate = 0;
        if (!price) price = 0;
        
        var amount = unformat_number(price) * (1 + Number(tax_rate)/100) * unformat_number(exchange_rate);
        $('#amount').val(tnhFormatMoney(amount));
    }

    appValidateForm($('#submit-form'), {
        // item_id: 'required',
    }, submit_form);

    // _validate_form($('#form-vouchers_coupon'), {
    //     date_vouchers: "required",
    //     code_vouchers: "required",
    //     customer: "required",
    //     staff: "required",
    //     payment_mode: "required",
    //     id_costs: "required",
    // }, add_form);

    function submit_form(form) {
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form),
                alert_float(form.alert_type, form.message);
            if (form.success) {
                if (typeof oTable != 'undefined' && oTable != '') {
                    oTable.draw();
                }
                // $('#payment_vouchers_coupon').modal('hide');
                $('.modal-dialog .close').trigger('click');
            }
        }), !1
    }

    // function submit_form(form) {
    //     // tinymce.get('note').save();
    //     // var data = $(form).serialize();
    //     var form = $(form),
    //         formData = new FormData(),
    //         formParams = form.serializeArray();

    //     $.each(form.find('input[type="file"]'), function(i, tag) {
    //         $.each($(tag)[0].files, function(i, file) {
    //             formData.append(tag.name, file);
    //         });
    //     });
    //     $.each(formParams, function(i, val) {
    //         formData.append(val.name, val.value);
    //     });

    //     var url = form.action;
    //     $.ajax({
    //             url: site.base_url + 'admin/spending_plan/submit/' + id,
    //             // url: url,
    //             type: 'POST',
    //             dataType: 'JSON',
    //             cache: false,
    //             contentType: false,
    //             processData: false,
    //             data: formData,
    //         })
    //         .done(function(data) {
    //             console.log(data);
    //             return;
    //             if (data.submitId) {
    //                 alert_float('success', data.message);
    //                 if (typeof oTable != 'undefined' && oTable != '') {
    //                     oTable.draw();
    //                 }
    //                 $('.modal-dialog .close').trigger('click');
    //             } else {
    //                 alert_float('danger', data.message);
    //                 $('.add').removeAttr('disabled', 'disabled');
    //             }
    //         })
    //         .fail(function() {
    //             console.log(data);
    //             alert_float('danger', 'error');
    //             $('.add').removeAttr('disabled', 'disabled');
    //         });
    //     return false;
    // }
</script>