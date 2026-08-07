<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('suggest_additional_personnel/detail/' . $id),
        ['id' => 'suggest_additional_personnel']); ?>
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
                            <input type="text" name="reference_no" class="form-control" id="reference_no"
                                   value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly=""
                                   aria-invalid="false">
                        </div>
                    </td>
                    <td style="width: 15%;">
                        <?= lang('date', 'date') ?>
                    </td>
                    <td style="width: 35%;">
                        <?= form_input('date',
                            set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Người yêu cầu', 'staff_suggest') ?></td>
                    <td>
                        <select name="staff_suggest" id="staff_suggest"
                                data-placeholder="<?= lang('Người yêu cầu') ?>" style="width: 100%;"
                                class="">
                            <option value=""></option>
                            <?php foreach ($employees as $key => $value) : ?>
                                <option <?= !empty($dtData) ? ($dtData['staff_suggest'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                    <td colspan="1">
                        <?php
                        $branchs = getListBranch();
                        ?>
                        <select name="branch_id" id="branch_id" class="branch_id"
                                data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($branchs)) { ?>
                                <?php foreach ($branchs as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Người tiếp nhận', 'staff_reciever') ?></td>
                    <td>
                        <select name="staff_reciever" id="staff_reciever"
                                data-placeholder="<?= lang('Người tiếp nhận') ?>" style="width: 100%;"
                                class="">
                            <option value=""></option>
                            <?php foreach ($employees as $key => $value) : ?>
                                <option <?= !empty($dtData) ? ($dtData['staff_reciever'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                    <td><?= lang('Người duyệt', 'staff_agree') ?></td>
                    <td>
                        <select name="staff_agree" id="staff_agree"
                                data-placeholder="<?= lang('Người duyệt') ?>" style="width: 100%;"
                                class="">
                            <option value=""></option>
                            <?php foreach ($employees as $key => $value) : ?>
                                <option <?= !empty($dtData) ? ($dtData['staff_agree'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Số lượng người bổ sung', 'quantity') ?></td>
                    <td>
                        <input type="text" name="quantity" id="quantity"
                               class="quantity number-format form-control"
                               value="<?= !empty($dtData) ? $dtData['quantity'] : '' ?>"
                               title="">
                    </td>
                    <td><?= lang('Vị trí cần bổ sung', 'position_recruitment') ?></td>
                    <td colspan="1">
                    <?php echo render_select('position_recruitment', $roles, array('roleid', 'name'), '',
                                            !empty($dtData) ? $dtData['position_recruitment'] : ''); ?>
                        <!-- <input type="text" name="position_recruitment" id="position_recruitment"
                               class="position_recruitment form-control"
                               data-placeholder="<?= lang('Vị trí tuyển dụng') ?>"
                               value="<?= !empty($dtData) ? $dtData['position_recruitment'] : '' ?>"
                               title=""> -->
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Người quản lý tạm thời', 'staff_admin') ?></td>
                    <td colspan="3">
                        <select name="staff_admin" id="staff_admin"
                                data-placeholder="<?= lang('Người quản lý tạm thời') ?>" style="width: 100%;"
                                class="">
                            <option value=""></option>
                            <?php foreach ($employees as $key => $value) : ?>
                                <option <?= !empty($dtData) ? ($dtData['staff_admin'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Thời gian bắt đầu', 'date_start') ?></td>
                    <td>
                        <input type="text" name="date_start" id="date_start"
                               class="date_start form-control datepicker" autocomplete="off"
                               value="<?= !empty($dtData) ? _dhau($dtData['date_start']) : '' ?>"
                               title="">
                    </td>
                    <td><?= lang('Thời gian kết thúc', 'date_end') ?></td>
                    <td colspan="1">
                        <input type="text" name="date_end" id="date_end"
                               class="date_end form-control datepicker" autocomplete="off"
                               value="<?= !empty($dtData) ? _dhau($dtData['date_end']) : '' ?>"
                               title="">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Đánh giá', 'quantity') ?></td>
                    <td>
                        <input type="text" name="evaluate" id="evaluate"
                               class="evaluate form-control"
                               value="<?= !empty($dtData) ? $dtData['evaluate'] : '' ?>"
                               title="">
                    </td>
                    <td><?= lang('KPIS', 'kpis') ?></td>
                    <td colspan="1">
                        <input type="text" name="kpis" id="kpis"
                               class="kpis form-control"
                               value="<?= !empty($dtData) ? $dtData['kpis'] : '' ?>"
                               title="">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Lý do yêu cầu', 'note') ?></td>
                    <td colspan="3">
                        <textarea name="note" id="note" class="form-control note" cols="3"
                                  rows="4"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
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
    $("#staff_admin").select2();
    $("#staff_reciever").select2();
    $("#staff_agree").select2();
    $("#staff_suggest").select2();
    appValidateForm($('#suggest_additional_personnel'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        staff_suggest: 'required',
        date_start: 'required',
        date_end: 'required'
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
        }).done(function (data) {
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
        }).fail(function () {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
</script>