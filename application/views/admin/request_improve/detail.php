<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_improve/detail/' . $id),
        ['id' => 'request_improve']
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
                                set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Loại cải tiến', 'type_improve') ?></td>
                        <td colspan="1">
                            <select name="type_improve" id="type_improve" class="type_improve" data-placeholder="<?= lang('Nhóm hiệu chỉnh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtTypeImprove)) { ?>
                                    <?php foreach ($dtTypeImprove as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['type_improve'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Nhóm cải tiến', 'category_improve') ?></td>
                        <td colspan="1">
                            <select name="category_improve" id="category_improve" class="category_improve" data-placeholder="<?= lang('Nhóm hiệu chỉnh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtCategoryImprove)) { ?>
                                    <?php foreach ($dtCategoryImprove as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['category_improve'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td><?= lang('Chi Tiết Cải Tiến', 'detail_improve') ?></td>
                        <td colspan="1">
                            <input type="text" name="detail_improve" id="detail_improve" class="detail_improve form-control" data-placeholder="<?= lang('Chi Tiết Cải Tiến') ?>" value="<?= !empty($dtData) ? $dtData['detail_improve'] : '' ?>" title="">
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
                        <td><?= lang('Người Tiếp Nhận', 'employees_receive') ?></td>
                        <td colspan="1">
                            <select name="employees_receive" id="employees_receive" class="employees_receive" data-placeholder="<?= lang('Người Tiếp Nhận') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['employees_receive'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Người Đánh Giá', 'employees_evaluate') ?></td>
                        <td colspan="1">
                            <select name="employees_evaluate" id="employees_evaluate" class="employees_evaluate" data-placeholder="<?= lang('Người Đánh Giá') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['employees_evaluate'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
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
                    </tr>
                    <tr>
                        <td><?= lang('Đề Xuất Cải Tiến', 'propose_improve') ?></td>
                        <td colspan="1">
                            <input type="text" name="propose_improve" id="propose_improve" class="propose_improve form-control" data-placeholder="<?= lang('Đề Xuất Cải Tiến') ?>" value="<?= !empty($dtData) ? $dtData['propose_improve'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Tiêu Chuẩn/ Quy Định', 'standard') ?></td>
                        <td colspan="1">
                            <input type="text" name="standard" id="standard" class="standard form-control" data-placeholder="<?= lang('Tiêu Chuẩn/ Quy Định') ?>" value="<?= !empty($dtData) ? $dtData['standard'] : '' ?>" title="">
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
    ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
    $("#branch_id").select2();
    $("#category_improve").select2();
    $("#type_improve").select2();
    $("#result_id").select2({
        allowClear: true
    });
    $("#employees").select2();
    $("#employees_receive").select2();
    $("#employees_evaluate").select2();
    appValidateForm($('#request_improve'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        category_improve: 'required',
        type_improve: 'required',
        employees: 'required',
        employees_receive: 'required',
        employees_evaluate: 'required',
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