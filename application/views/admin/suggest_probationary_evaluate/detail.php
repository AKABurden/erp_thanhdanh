<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('suggest_probationary_evaluate/detail/' . $id.'?type='.$this->type),
        ['id' => 'suggest_probationary_evaluate']); ?>
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
                        <?= form_input('date', set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) :date('d/m/Y H:i'),
                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Nhân viên', 'staff_id') ?></td>
                    <td colspan="1">
                        <select onchange="changeStaff(this)" name="staff_id" id="staff_id" class="staff_id"
                                data-placeholder="<?= lang('Nhân viên') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['staff_id'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= get_staff_full_name($value['staffid']) ?></option>
                                <?php } ?>
                            <?php } ?>
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
                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Mã vị trí', 'code_role') ?></td>
                    <td colspan="1">
                        <div class="code_role"><?= !empty($dtData) ? $dtData['code_role'] : '' ?></div>
                    </td>
                    <td><?= lang('Phòng ban', 'name_department') ?></td>
                    <td colspan="1">
                        <div class="name_department"><?= !empty($dtData) ? $dtData['name_department'] : '' ?></div>
                    </td>
                </tr>
                <?php if ($this->type == 1 || $this->type == 3){ ?>
                <tr>
                    <td><?= lang('Ngày bắt đầu thử việc', 'date_start_probationary') ?></td>
                    <td colspan="1">
                        <input type="text" name="date_start_probationary" class="date_start_probationary form-control datepicker" id="date_start_probationary" value="<?= !empty($dtData) ? _dhau($dtData['date_start_probationary']) : '' ?>">
                    </td>
                    <td><?= lang('Ngày kết thúc thử việc', 'date_end_probationary') ?></td>
                    <td colspan="1">
                        <input type="text" name="date_end_probationary" class="date_end_probationary form-control datepicker" id="date_end_probationary"  value="<?= !empty($dtData) ? _dhau($dtData['date_end_probationary']) : '' ?>">
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td><?= lang('Quản lý trực tiếp', 'staff_manager') ?></td>
                    <td colspan="1">
                        <select name="staff_manager" id="staff_manager" class="staff_manager"
                                data-placeholder="<?= lang('Quản lý trực tiếp') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['staff_manager'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= get_staff_full_name($value['staffid']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Trưởng phòng nhân sự', 'staff_manager_hr') ?></td>
                    <td colspan="1">
                        <select name="staff_manager_hr" id="staff_manager_hr" class="staff_manager_hr"
                                data-placeholder="<?= lang('Trưởng phòng nhân sự') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['staff_manager_hr'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= get_staff_full_name($value['staffid']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Ghi chú', 'note') ?></td>
                    <td colspan="3">
                        <textarea  name="note" class="note form-control" rows="4" id="note"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
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
    $("#staff_id").select2();
    $("#staff_manager").select2();
    $("#staff_manager_hr").select2();
    <?php if ($this->type == 1 || $this->type == 3){ ?>
    appValidateForm($('#suggest_probationary_evaluate'), {
        date: 'required',
        reference_no: 'required',
        staff_id: 'required',
        branch_id: 'required',
        staff_manager: 'required',
        staff_manager_hr: 'required',
        date_start_probationary: 'required',
        date_end_probationary: 'required'
    }, detail);
    <?php } else { ?>
    appValidateForm($('#suggest_probationary_evaluate'), {
        date: 'required',
        reference_no: 'required',
        staff_id: 'required',
        branch_id: 'required',
        staff_manager: 'required',
        staff_manager_hr: 'required',
    }, detail);
    <?php } ?>

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

    function changeStaff(_this){
        staff_id = $(_this).val();
        $.ajax({
            url: "<?= admin_url('suggest_probationary_evaluate/getInfoStaff') ?>",
            type: 'POST',
            dataType: 'JSON',
            data: {
                staff_id:staff_id,
                csrf_token_name:"<?= $this->security->get_csrf_hash() ?>"
            },
        }).done(function(data) {
            code_role = data.dtStaff.code_role != undefined ? data.dtStaff.code_role : '';
            name_department = data.dtStaff.name_department != undefined ? data.dtStaff.name_department : '';
            $(".code_role").html(code_role);
            $(".name_department").html(name_department);
        }).fail(function() {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
    }
</script>