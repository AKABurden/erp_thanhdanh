<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_repair/detail/' . $id),
        ['id' => 'request_repair']
    ); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="title"><?= $title ?></span>
            </h4>
        </div>
        <div class="modal-body">
            <table class="tnh-tb table-bordered table-hover">
                <tbody>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('dt_reference_suggest', 'reference_no') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('date', 'date') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date',
                                (set_value('date') ? set_value('date') : (!empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'))),
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr class="hide">
                        <td><?= lang('Đơn Vị Sửa Chữa', 'unit_repair') ?></td>
                        <td colspan="1">
                            <input type="text" name="unit_repair" id="unit_repair" class="unit_repair form-control" data-placeholder="<?= lang('Đơn Vị Sửa Chữa') ?>" value="<?= !empty($dtData) ? $dtData['unit_repair'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Số lượng', 'quantity') ?></td>
                        <td colspan="1">
                            <input type="text" name="quantity" id="quantity" onchange="total();" class="quantity form-control number-format" data-placeholder="<?= lang('Số lượng') ?>" value="<?= !empty($dtData) ? formatNumber($dtData['quantity']) : '' ?>" title="">
                        </td>
                    </tr>
                    <tr class="hide">
                        <td><?= lang('Đơn Giá Sửa Chữa', 'price') ?></td>
                        <td colspan="1">
                            <input type="text" name="price" id="price" onchange="total();" class="price form-control number-format" data-placeholder="<?= lang('Đơn Giá Sửa Chữa') ?>" value="<?= !empty($dtData) ? formatMoney($dtData['price']) : '' ?>" title="">
                        </td>
                        <td><?= lang('Thành tiền', 'total_amount') ?></td>
                        <td colspan="1" class="total_amount"><?= !empty($dtData) ? formatMoney($dtData['price'] * $dtData['quantity']) : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Nhóm bảo dưỡng', 'category_maintenance') ?></td>
                        <td colspan="1">
                            <select name="category_maintenance" id="category_maintenance" class="category_maintenance" data-placeholder="<?= lang('Nhóm bảo dưỡng') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtCategorymaintenance)) { ?>
                                    <?php foreach ($dtCategorymaintenance as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['category_maintenance'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Bộ Phận bảo dưỡng', 'bp_maintenance') ?></td>
                        <td colspan="1">
                            <input type="text" name="bp_maintenance" id="bp_maintenance" class="bp_maintenance form-control" data-placeholder="<?= lang('Bộ Phận bảo dưỡng') ?>" value="<?= !empty($dtData) ? $dtData['bp_maintenance'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi Tiết bảo dưỡng', 'detail_maintenance') ?></td>
                        <td colspan="1">
                            <input type="text" name="detail_maintenance" id="detail_maintenance" class="detail_maintenance form-control" data-placeholder="<?= lang('Chi Tiết bảo dưỡng') ?>" value="<?= !empty($dtData) ? $dtData['detail_maintenance'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Người đề xuất', 'employees') ?></td>
                        <td colspan="1">
                            <select name="employees" id="employees" class="employees" data-placeholder="<?= lang('Người đề xuất') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['employees'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Thiết bị', 'machines_id') ?></td>
                        <td colspan="1">
                            <input type="text" name="machines_id" id="machines_id" class="machines_id" data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['machines_id'].'__'.$dtData['cost_id'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                        <td colspan="1">
                            <?php
                            $branchs = getListBranch();
                            ?>
                            <select name="branch_id" id="branch_id" class="branch_id" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($branchs)) { ?>
                                    <?php foreach ($branchs as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="hide">
                        <td><?= lang('Kết quả', 'result_id') ?></td>
                        <td colspan="1">
                            <select name="result_id" id="result_id" class="result_id" data-placeholder="<?= lang('Kết quả') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtResult)) { ?>
                                    <?php foreach ($dtResult as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['result_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Biên Bản Nghiệm Thu', 'test_records') ?></td>
                        <td colspan="1">
                            <input type="text" name="test_records" id="test_records" class="test_records form-control" data-placeholder="<?= lang('Biên Bản Nghiệm Thu') ?>" value="<?= !empty($dtData) ? $dtData['test_records'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr class="hide">
                        <td><?= lang('Đánh Giá Đơn Vị Sửa Chữa', 'evaluate') ?></td>
                        <td colspan="1">
                            <input type="text" name="evaluate" id="evaluate" class="evaluate form-control" data-placeholder="<?= lang('Đánh Giá Đơn Vị Sửa Chữa') ?>" value="<?= !empty($dtData) ? $dtData['evaluate'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Tiêu Chuẩn/ Quy Định', 'standard') ?></td>
                        <td colspan="1">
                            <input type="text" name="standard" id="standard" class="standard form-control" data-placeholder="<?= lang('Tiêu Chuẩn/ Quy Định') ?>" value="<?= !empty($dtData) ? $dtData['standard'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr class="hide">
                        <td><?= lang('Hoàn Thành Thanh Toán', 'payment') ?></td>
                        <td colspan="1">
                            <input type="text" name="payment" id="payment" class="payment form-control" data-placeholder="<?= lang('Hoàn Thành Thanh Toán') ?>" value="<?= !empty($dtData) ? $dtData['evaluate'] : '' ?>" title="">
                        </td>
                        <td>
                            <?= lang('tnh_suppliers', 'supplier_id') ?>
                        </td>
                        <td>
                            <input type="text" name="supplier_id" id="supplier_id" class="modal-select2" data-placeholder="<?= lang('tnh_suppliers') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['supplier_id'] : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Loại Yêu Cầu', 'type_repair') ?></td>
                        <td>
                            <select name="type_repair" id="type_repair" class="type_repair" data-placeholder="<?= lang('Loại yêu cầu') ?>" style="width: 100%;">
                                <?php foreach($this->type_repair as $key => $value) {?>
                                    <option value="<?=$value['id']?>" <?=(!empty($dtData['type_repair']) && $dtData['type_repair'] == $value['id'] ? 'selected' : '')?>><?=$value['name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Báo cáo không phù hợp', 'production_report_id') ?></td>
                        <td>
                            <input type="text" name="production_report_id" id="production_report_id" class="modal-select2" data-placeholder="<?= lang('Báo cáo không phù hợp') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['production_report_id'] : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Mức độ ưu tiên', 'priority') ?></td>
                        <td>
                            <select name="priority" id="priority" class="priority" data-placeholder="<?= lang('Mức độ ưu tiên') ?>" style="width: 100%;">
                                <?php foreach($this->list_priority as $key => $value) {?>
                                    <option value="<?=$value['id']?>" <?=(!empty($dtData['priority']) && $dtData['priority'] == $value['id'] ? 'selected' : '')?>><?=$value['name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Ngày yêu cầu hoàn thành', 'completion_date') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'completion_date',
                                (set_value('completion_date') ? set_value('completion_date') : (!empty($dtData) ? _d($dtData['completion_date']) : date('d/m/Y'))),
                                'id="date" class="form-control datepicker" placeholder="Ngày yêu cầu hoàn thành" '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Chi tiết sự cố/ yêu cầu', 'detailed') ?>
                        </td>
                        <td>
                            <textarea  name="detailed" id="detailed" class="form-control" data-placeholder="Chi tiết sự cố/ yêu cầu"><?= !empty($dtData) ? $dtData['detailed'] : '' ?></textarea>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('File đính kèm', 'file') ?>
                        </td>
                        <td>
                            <input type="file"  name="file" id="file" class="form-control" data-placeholder="File đính kèm"/>
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
<script type="text/javascript">
    init_datepicker();
    init_selectpicker('refresh');
    ajaxSelectParams('#machines_id', 'admin/request_repair/searchMachines', $("#machines_id").val(), true, true);
    ajaxSelectParams('#supplier_id', 'admin/orders/searchSuppliers', $("#supplier_id").val(), {type: 0}, true);
    ajaxSelectParams('#production_report_id', 'admin/request_repair/searchProductionReport', $("#production_report_id").val(), {type: 0}, true);

    $("#branch_id").select2();
    $("#category_maintenance").select2();
    $("#employees").select2();
    $("#result_id").select2();
    $("#type_repair").select2();
    $("#priority").select2();
    appValidateForm($('#request_repair'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        // result_id: 'required',
        category_maintenance: 'required',
        employees: 'required',
        machines_id: 'required',
        // supplier_id: 'required',
    }, detail);

    function total() {
        quantity = intVal($('.quantity').val());
        price = intVal($('.price').val());
        total_amount = quantity * price;
        $('.total_amount').html(tnhFormatMoney(total_amount));
    }

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
</script>