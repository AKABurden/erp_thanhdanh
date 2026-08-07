<div class="modal-dialog modal-lg" style="min-width: 40%;">
    <?php echo form_open(
        admin_url('suggest_task/detail/' . $id),
        ['id' => 'suggest_task']
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
                                <input type="text" name="reference_no" class="form-control" id="reference_no"
                                    value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
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
                        <td><?= lang('Mã vị trí', 'role_id') ?></td>
                        <td colspan="1">
                            <input type="text" name="role_id" id="role_id" class="role_id modal-select2"
                                data-placeholder="<?= lang('Mã vị trí') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_id'] : '' ?>"
                                title="">
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
                                        <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Người giao việc', 'staff_id') ?></td>
                        <td colspan="3">
                            <select name="staff_id" id="staff_id"
                                data-placeholder="<?= lang('Người giao việc') ?>" style="width: 100%;"
                                class="">
                                <option value=""></option>
                                <?php foreach ($employees as $key => $value) : ?>
                                    <option data-role="<?= $value['role'] ?>" <?= !empty($dtData) ? ($dtData['staff_id'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Người được phân công', 'suggest_task_staff') ?></td>
                        <td colspan="3" style="width: 100%;">
                            <select data-live-search="true" multiple="true" data-width="100%" name="suggest_task_staff[]" id="suggest_task_staff"
                                data-placeholder="<?= lang('Người được phân công') ?>" style="width: 100%;"
                                class="selectpicker">
                                <option value=""></option>
                                <?php foreach ($employees as $key => $value) : ?>
                                    <option value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Phòng ban phụ trách', 'department_id') ?></td>
                        <td>
                            <?php $value = ''; ?>
                            <?php echo render_select('department_id', (!empty($room) ? $room : []), ['id', 'name'], '', $value) ?>
                        </td>
                        <td><?= lang('Mã công việc', 'category_tasks') ?></td>
                        <td>
                            <div class="form-group" app-field-wrapper="category_tasks">
                                <select name="category_tasks" id="category_tasks" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                                    <option value=""></option>
                                    <?php if (!empty($category_tasks)) : ?>
                                        <?php foreach ($category_tasks as $key => $value) : ?>
                                            <option <?= ((!empty($object->category_tasks) && $object->category_tasks == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['content'] ?>" data-departments="<?= $value['departments'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Ngày bắt đầu', 'date_start') ?></td>
                        <td colspan="1">
                            <input type="text" name="date_start" id="date_start" autocomplete="off" class="date_start datepicker form-control" value="<?= !empty($dtData) ? _dhau($dtData['date_start']) : date('d/m/Y') ?>"
                                title="">
                        </td>
                        <td><?= lang('Hạn chót', 'date_end') ?></td>
                        <td colspan="1">
                            <input type="text" name="date_end" id="date_end" autocomplete="off" class="date_end datepicker form-control" value="<?= !empty($dtData) && !empty($dtData['date_end']) ? _dhau($dtData['date_end']) : date('d/m/Y') ?>"
                                title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Ngày hoành thành', 'date_finish') ?></td>
                        <td colspan="1">
                            <input type="text" name="date_finish" id="date_finish" autocomplete="off" class="date_finish datepicker form-control" value="<?= !empty($dtData) && !empty($dtData['date_finish']) ? _dhau($dtData['date_finish']) : '' ?>"
                                title="">
                        </td>
                        <td><?= lang('Quy định', 'regulations') ?></td>
                        <td colspan="1">
                            <input type="text" name="regulations" id="regulations" autocomplete="off" class="regulations datepicker form-control" value="<?= !empty($dtData) ? ($dtData['regulations']) : '' ?>"
                                title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi tiết công việc', 'detail_task') ?></td>
                        <td colspan="3">
                            <textarea name="detail_task" id="detail_task" class="form-control detail_task" cols="3" rows="4"><?= !empty($dtData) ? $dtData['detail_task'] : '' ?></textarea>
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
    ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
    $("#branch_id").select2();
    $("#staff_id").select2();
    appValidateForm($('#suggest_task'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        role_id: 'required',
        staff_id: 'required',
        date_start: 'required',
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
    $('#staff_id').change();
</script>