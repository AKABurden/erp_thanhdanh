<div id="modal_step_three_one" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 60%;">
        <?php echo form_open(
            admin_url('request_repair/detail_step/' . $id . '/'. $id_request_repair),
            ['id' => 'request_repair']
        ); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= $title ?? '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="type_processing" value="1">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td><?= lang('Người thực hiện', 'staff_performing') ?></td>
                        <td colspan="1">
                                <select name="staff_performing" id="staff_performing" class="staff_performing select2" data-placeholder="<?= lang('Người thực hiện') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? ($request_repair['staff_performing'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Ngày bắt đầu', 'date_performing') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_performing',
                                (set_value('date_performing') ? set_value('date_performing') : (!empty($request_repair) ? _dt($request_repair['date_performing']) : date('d/m/Y'))),
                                'id="date" class="form-control datepicker" placeholder="' . lang('Ngày bắt đầu') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Ngày hoàn thành dự kiến', 'date_expected') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_expected',
                                (set_value('date_expected') ? set_value('date_expected') : (!empty($request_repair) ? _dt($request_repair['date_expected']) : NULL)),
                                'id="date_expected" class="form-control datetimepicker" placeholder="' . lang('Ngày hoàn thành dự kiến') . '" required '
                            ) ?>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Ngày hoàn thành thực tế', 'date_success') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_success',
                                (set_value('date_success') ? set_value('date_success') : (!empty($request_repair) ? _dt($request_repair['date_success']) : NULL)),
                                'id="date_success" class="form-control datetimepicker" placeholder="' . lang('Ngày hoàn thành thực tế') . '" '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Kết quả', 'is_result') ?></td>
                        <td colspan="1">
                            <select name="is_result" id="is_result" class="is_result select2" data-placeholder="<?= lang('Kết quả') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php foreach ($this->list_result as $key => $value) { ?>
                                    <option <?= !empty($request_repair) ? (!empty($request_repair['is_result']) && $request_repair['is_result'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Chi Tiết Thực Hiện', 'category_tasks') ?></td>
                        <td colspan="1">
                            <select name="category_tasks" id="category_tasks" class="category_tasks select2" data-placeholder="<?= lang('Chi tiết thực hiện') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($category_tasks)) { ?>
                                    <?php foreach ($category_tasks as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? (!empty($request_repair['category_tasks']) && $request_repair['category_tasks'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['code'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Số lượng linh kiện thực tế', 'quantity') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="quantity" id="quantity" class="form-control" data-type="currency" data-placeholder="Số lượng linh kiện thực tế" value="<?= !empty($request_repair) ? number_format_data($request_repair['quantity']) : '' ?>">
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Chi phí thực tế', 'amount') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="amount" id="amount" class="form-control" data-type="currency" data-placeholder="Chi phí thực tế" value="<?= !empty($request_repair) ? number_format_data($request_repair['amount']) : '' ?>">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('#modal_step_three_one').modal('show');
    init_datepicker();
    init_selectpicker('refresh');
    // ajaxSelectParams('#machines_id', 'admin/request_repair/searchMachines', $("#machines_id").val(), true, true);
    // ajaxSelectParams('#supplier_id', 'admin/orders/searchSuppliers', $("#supplier_id").val(), {type: 0}, true);
    // ajaxSelectParams('#production_report_id', 'admin/request_repair/searchProductionReport', $("#production_report_id").val(), {type: 0}, true);

    $("select.select2").select2();
    appValidateForm($('#request_repair'), {
        staff_performing: 'required',
        date_performing: 'required',
        date_expected: 'required',
        date_success: 'required',
        is_result: 'required',
        category_tasks: 'required'
    }, detail);



    function detail(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function(data) {
            if (data.result) {
                alert_float('success', data.message);
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }

                if(data.id_tasks) {
                    init_task_modal(data.id_tasks);
                }
                $('.modal-dialog .close').trigger('click');
            } else {
                alert_float('danger', data.message);
                $('.add').removeAttr('disabled', 'disabled');
            }
        }).fail(function() {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
    function total() {
        quantity = intVal($('.quantity').val());
        price = intVal($('.price').val());
        total_amount = quantity * price;
        $('.total_amount').html(tnhFormatMoney(total_amount));
    }

    // Format tiền tệ khi nhập liệu
    $('input[data-type="currency"]').on({
        keyup: function() {
            formatCurrency($(this));
        },
        blur: function() {
            formatCurrency($(this), "blur");
        }
    });
</script>