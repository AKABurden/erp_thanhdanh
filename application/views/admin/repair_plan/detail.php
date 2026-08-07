<div class="modal fade" id="modal_repair_plan" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 60%;">
        <?php echo form_open(
            admin_url('repair_plan/detail/' . $id),
            ['id' => 'repair_plan']
        ); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= !empty($title) ? $title : '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td style="width: 15%;">
                                <?= lang('Số Phiếu Kế Hoạch', 'reference_no') ?>
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
                                    set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                    'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                                ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang('Đơn Vị Sửa Chữa', 'unit_repair') ?></td>
                            <td colspan="1">
                                <input type="text" name="unit_repair" id="unit_repair" class="unit_repair form-control" data-placeholder="<?= lang('Đơn Vị Sửa Chữa') ?>" value="<?= !empty($dtData) ? $dtData['unit_repair'] : '' ?>" title="">
                            </td>
                            <td><?= lang('Số Lượng', 'quantity') ?></td>
                            <td colspan="1">
                                <input type="text" name="quantity" id="quantity" onchange="total();" class="quantity form-control number-format" data-placeholder="<?= lang('Số lượng') ?>" value="<?= !empty($dtData) ? formatNumber($dtData['quantity']) : '' ?>" title="">
                            </td>
                        <tr>
                            <td><?= lang('Đơn Giá Sửa Chữa', 'price') ?></td>
                            <td colspan="1">
                                <input type="text" name="price" id="price" onchange="total();" class="price form-control number-format" data-placeholder="<?= lang('Đơn Giá Sửa Chữa') ?>" value="<?= !empty($dtData) ? formatMoney($dtData['price']) : '' ?>" title="">
                            </td>
                            <td><?= lang('Thành Tiền', 'total_amount') ?></td>
                            <td colspan="1" class="total_amount"><?= !empty($dtData) ? formatMoney($dtData['price'] * $dtData['quantity']) : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm Bảo Dưỡng', 'category_maintenance') ?></td>
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
                            <td><?= lang('Bộ Phận Bảo Dưỡng', 'bp_maintenance') ?></td>
                            <td colspan="1">
                                <input type="text" name="bp_maintenance" id="bp_maintenance" class="bp_maintenance form-control" data-placeholder="<?= lang('Bộ Phận bảo dưỡng') ?>" value="<?= !empty($dtData) ? $dtData['bp_maintenance'] : '' ?>" title="">
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang('Chi Tiết Bảo Dưỡng', 'detail_maintenance') ?></td>
                            <td colspan="1">
                                <input type="text" name="detail_maintenance" id="detail_maintenance" class="detail_maintenance form-control" data-placeholder="<?= lang('Chi Tiết bảo dưỡng') ?>" value="<?= !empty($dtData) ? $dtData['detail_maintenance'] : '' ?>" title="">
                            </td>
                            <td><?= lang('Người Đề Xuất', 'employees') ?></td>
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
                            <td><?= lang('Thiết Bị', 'machines_id') ?></td>
                            <td colspan="1">
                                <input type="text" name="machines_id" id="machines_id" class="machines_id" data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['machines_id'] : '' ?>" title="">
                            </td>
                            <td><?= lang('Chi Nhánh', 'branch_id') ?></td>
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
                        <tr>
                            <td><?= lang('Kết Quả', 'result_id') ?></td>
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
                        <tr>
                            <td><?= lang('Đánh Giá Đơn Vị Sửa Chữa', 'evaluate') ?></td>
                            <td colspan="1">
                                <input type="text" name="evaluate" id="evaluate" class="evaluate form-control" data-placeholder="<?= lang('Đánh Giá Đơn Vị Sửa Chữa') ?>" value="<?= !empty($dtData) ? $dtData['evaluate'] : '' ?>" title="">
                            </td>
                            <td><?= lang('Tiêu Chuẩn/ Quy Định', 'standard') ?></td>
                            <td colspan="1">
                                <input type="text" name="standard" id="standard" class="standard form-control" data-placeholder="<?= lang('Tiêu Chuẩn/ Quy Định') ?>" value="<?= !empty($dtData) ? $dtData['standard'] : '' ?>" title="">
                            </td>
                        </tr>
                        <tr>
                            <td><?= lang('Hoàn Thành Thanh Toán', 'payment') ?></td>
                            <td colspan="1">
                                <input type="text" name="payment" id="payment" class="payment form-control" data-placeholder="<?= lang('Hoàn Thành Thanh Toán') ?>" value="<?= !empty($dtData) ? $dtData['evaluate'] : '' ?>" title="">
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
    $('#modal_repair_plan').modal('show');
    init_datepicker();
    init_selectpicker('refresh');
    ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
    $("#branch_id").select2();
    $("#category_maintenance").select2();
    $("#employees").select2();
    $("#result_id").select2();
    appValidateForm($('#repair_plan'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        result_id: 'required',
        category_maintenance: 'required',
        employees: 'required',
        machines_id: 'required'
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