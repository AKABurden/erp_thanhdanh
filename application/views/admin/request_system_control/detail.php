<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_system_control/detail/' . $id),
        ['id' => 'request_system_control']
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
                        <td><?= lang('Loại hệ thống', 'type_system') ?></td>
                        <td colspan="1">
                            <select name="type_system" id="type_system" class="type_system" data-placeholder="<?= lang('Loại hệ thống') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtTyleSystem)) { ?>
                                    <?php foreach ($dtTyleSystem as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['type_system'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Phân hệ', 'subsystem') ?></td>
                        <td colspan="1">
                            <input type="text" name="subsystem" id="subsystem" class="subsystem form-control" data-placeholder="<?= lang('Phân hệ') ?>" value="<?= !empty($dtData) ? $dtData['subsystem'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Danh Mục Hệ Thống', 'system') ?></td>
                        <td colspan="1">
                            <select name="system" id="system" class="system" data-placeholder="<?= lang('Nhóm Hệ Thống') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtSystem)) { ?>
                                    <?php foreach ($dtSystem as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['system'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Nhóm Hệ Thống', 'category_system') ?></td>
                        <td colspan="1">
                            <select name="category_system" id="category_system" class="category_system" data-placeholder="<?= lang('Nhóm Hệ Thống') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtCateforySystem)) { ?>
                                    <?php foreach ($dtCateforySystem as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['category_system'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td><?= lang('Chi tiết', 'detail_system') ?></td>
                        <td colspan="1">
                            <textarea name="detail_system" id="detail_system" class="form-control detail_system" cols="3" rows="4"><?= !empty($dtData) ? $dtData['detail_system'] : '' ?></textarea>
                        </td>
                        <td><?= lang('Người Yêu Cầu', 'staff_request') ?></td>
                        <td colspan="1">
                            <select name="staff_request" id="staff_request" class="staff_request" data-placeholder="<?= lang('Người Yêu Cầu') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['staff_request'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td><?= lang('Người Tiếp Nhận', 'staff_tn') ?></td>
                        <td colspan="1">
                            <select name="staff_tn" id="staff_tn" class="staff_tn" data-placeholder="<?= lang('Người Tiếp Nhận') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['staff_tn'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Người Hoàn Thành', 'staff_ht') ?></td>
                        <td colspan="1">
                            <select name="staff_ht" id="staff_ht" class="staff_ht" data-placeholder="<?= lang('Người Hoàn Thành') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['staff_ht'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
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
    ajaxSelectParams('#client_id', 'admin/request_system_control/searchClients', $("#client_id").val(), true, true);
    $("#branch_id").select2();
    $("#result_id").select2();
    $("#category_system").select2();
    $("#type_system").select2();
    $("#system").select2();
    $("#staff_request").select2();
    $("#staff_tn").select2();
    $("#staff_ht").select2();
    $("#brand_id").select2();
    appValidateForm($('#request_system_control'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        category_system: 'required',
        type_system: 'required',
        system: 'required',
        staff_request: 'required',
        staff_tn: 'required',
        staff_ht: 'required',
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