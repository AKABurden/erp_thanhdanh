<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('suggest_plan_deparment/detail/' . $id),
        ['id' => 'suggest_plan_deparment']
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
                        <td><?= lang('Phòng ban', 'deparment_id') ?></td>
                        <td colspan="1">
                            <select name="deparment_id" id="deparment_id" class="deparment_id" data-placeholder="<?= lang('Phòng ban') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($departments)) { ?>
                                    <?php foreach ($departments as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['deparment_id'] == $value['departmentid'] ? 'selected' : '') : '' ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
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
                    <tr>
                        <td><?= lang('Mã công việc', 'category_task_id') ?></td>
                        <td>
                            <select name="category_task_id" id="category_task_id" data-placeholder="<?= lang('Mã công việc') ?>" style="width: 100%;" class="">
                                <option value=""></option>
                                <?php if (!empty($category_tasks)){ ?>
                                <?php foreach ($category_tasks as $key => $value) : ?>
                                    <option <?= !empty($dtData) ? ($dtData['category_task_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['code'].' - '.$value['content'] ?></option>
                                <?php endforeach ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Người phụ trách', 'staff_id') ?></td>
                        <td>
                            <select name="staff_id" id="staff_id" data-placeholder="<?= lang('Người phụ trách') ?>" style="width: 100%;" class="">
                                <option value=""></option>
                                <?php foreach ($employees as $key => $value) : ?>
                                    <option <?= !empty($dtData) ? ($dtData['staff_id'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Người tái thực hiện', 'date_start') ?></td>
                        <td>
                            <input type="text" name="date_start" id="date_start" class="date_start datepicker form-control" value="<?= !empty($dtData) ? _dhau($dtData['date_start']) : '' ?>" title="">
                        </td>
                        <td><?= lang('Ngày nhắc việc', 'date_reminder') ?></td>
                        <td colspan="1">
                            <input type="text" name="date_reminder" id="date_reminder" class="date_reminder datepicker form-control" value="<?= !empty($dtData) ? _dhau($dtData['date_reminder']) : '' ?>" title="">
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
    $("#branch_id").select2();
    $("#category_task_id").select2();
    $("#deparment_id").select2();
    $("#staff_id").select2();
    appValidateForm($('#suggest_plan_deparment'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        staff_id: 'required',
        deparment_id: 'required',
        category_task_id: 'required'
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
</script>