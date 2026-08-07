<div id="modal_step" class="modal fade" role="dialog">
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
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td><?= lang('Người kiểm tra', 'staff_inspector') ?></td>
                        <td colspan="1">
                            <select name="staff_inspector" id="staff_inspector" class="staff_inspector select2" data-placeholder="<?= lang('Người đề xuất') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? ($request_repair['staff_inspector'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Ngày kiểm tra', 'date_inspector') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_inspector',
                                (set_value('date_inspector') ? set_value('date_inspector') : (!empty($request_repair) ? _d($request_repair['date_inspector']) : date('d/m/Y'))),
                                'id="date_inspector" class="form-control datepicker" placeholder="' . lang('Ngày kiểm tra') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Đánh Giá Sự Cố', 'incident') ?>
                        </td>
                        <td>
                            <textarea  name="incident" id="incident" class="form-control" data-placeholder="Đánh Giá Sự Cố"><?= !empty($request_repair) ? $request_repair['incident'] : '' ?></textarea>
                        </td>
                        <td><?= lang('Loại xử lý', 'type_processing') ?></td>
                        <td colspan="1">
                            <select name="type_processing" id="type_processing" class="type_processing select2" data-placeholder="<?= lang('Nhóm bảo dưỡng') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php foreach ($this->type_processing as $key => $value) { ?>
                                    <option <?= !empty($request_repair) ? (!empty($request_repair['type_processing']) && $request_repair['type_processing'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Lý do', 'reason') ?>
                        </td>
                        <td style="width: 35%;">
                            <textarea  name="reason" id="reason" class="form-control" data-placeholder="Lý do"><?= !empty($request_repair) ? $request_repair['reason'] : '' ?></textarea>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Chi tiết bảo dưỡng/ sửa chữa', 'detail_repair') ?>
                        </td>
                        <td style="width: 35%;">
                            <textarea  name="detail_repair" id="detail_repair" class="form-control" data-placeholder="Chi tiết bảo dưỡng/ sửa chữa"><?= !empty($request_repair) ? $request_repair['detail_repair'] : '' ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('thời gian bắt đầu ảnh hưởng (dự kiến)', 'date_start') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_start',
                                (set_value('date_start') ? set_value('date_start') : (!empty($request_repair) ? _dt($request_repair['date_start']) : date('d/m/Y H:i:s'))),
                                'id="date_start" class="form-control datetimepicker" placeholder="thời gian bắt đầu ảnh hưởng (dự kiến)" '
                            ) ?>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('thời gian kết thúc ảnh hưởng(dự kiến)', 'date_end') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_end',
                                (set_value('date_end') ? set_value('date_end') : (!empty($request_repair) ? _dt($request_repair['date_end']) : date('d/m/Y H:i:s'))),
                                'id="date_end" class="form-control datetimepicker" placeholder="thời gian kết thúc ảnh hưởng(dự kiến)" '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Số lượng linh kiện dự kiến', 'number_components') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="number_components" id="number_components" class="form-control" data-type="currency" data-placeholder="Số lượng linh kiện dự kiến" value="<?= !empty($request_repair) ? number_format_data($request_repair['number_components']) : '' ?>">
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Chi phí linh kiện dự kiến', 'expense') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="expense" id="expense" class="form-control" data-type="currency" data-placeholder="Chi phí linh kiện dự kiến" value="<?= !empty($request_repair) ? number_format_data($request_repair['expense']) : '' ?>">
                        </td>
                    </tr>
                    </tbody>
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
    $('#modal_step').modal('show');
    init_datepicker();
    init_selectpicker('refresh');
    // ajaxSelectParams('#machines_id', 'admin/request_repair/searchMachines', $("#machines_id").val(), true, true);
    // ajaxSelectParams('#supplier_id', 'admin/orders/searchSuppliers', $("#supplier_id").val(), {type: 0}, true);
    // ajaxSelectParams('#production_report_id', 'admin/request_repair/searchProductionReport', $("#production_report_id").val(), {type: 0}, true);

    $("select.select2").select2();
    appValidateForm($('#request_repair'), {
        staff_inspector: 'required',
        date_inspector: 'required',
        incident: 'required',
        type_processing: 'required',
        reason: 'required',
        detail_repair: 'required',
        date_start: 'required',
        date_end: 'required',
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