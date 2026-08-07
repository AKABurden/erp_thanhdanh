<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('suggest_union/detail/' . $id),
        ['id' => 'suggest_union']
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
                                   value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly=""
                                   aria-invalid="false">
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
                    <td><?= lang('Nhân viên', 'staff_id') ?></td>
                    <td colspan="1">
                        <?php $arrSelect = [];
                        $arrSelect[] = !empty($dtData) ? $dtData['staff_id'] : '' ?>
                        <select class="selectpicker staff_id form-control" name="staff_id" id="staff_id"
                                data-live-search="true"
                                onchange="changePersonel(this)"
                                title='<?php echo _l('Nhân viên'); ?>'
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                        >
                            <?php if (!empty($staff)) { ?>
                                <?php foreach ($staff as $key => $value) { ?>
                                    <optgroup label="<?= $value['name'] ?>">
                                        <?php if (!empty($value['staffs'])) : ?>
                                            <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                <option data-salary="<?= $v['salary_bhxh'] ?>" data-salary-bhxh="<?= $v['salary_bhxh_new'] ?>" data-name-department="<?= $v['name_department'] ?>" data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array($v['staffid'],
                                                        $arrSelect)) ? 'selected' : '' ?>
                                                        value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </optgroup>
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
                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= lang('Phòng ban', 'name_department') ?>
                    </td>
                    <td>
                        <div class="name_department"><?= !empty($dtData) ? $dtData['name_department'] : '' ?></div>
                    </td>
                    <td>
                        <?= lang('Vị trí', 'name_role') ?>
                    </td>
                    <td>
                        <div class="name_role"><?= !empty($dtData) ? $dtData['name_role'] : '' ?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= lang('Số tiền', 'grand_total') ?>
                    </td>
                    <td colspan="3">
                        <input type="text" name="grand_total" class="grand_total form-control number-format" value="<?= !empty($dtData) ? formatMoney($dtData['grand_total']) : 0 ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= lang('Nội dung diễn giải', 'detail') ?>
                    </td>
                    <td colspan="3">
                        <textarea cols="2" rows="2" name="detail" class="form-control detail"><?= !empty($dtData) ? ($dtData['detail']) : '' ?></textarea>
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
    dataPercent = <?= !empty($dataPercent) ? json_encode($dataPercent) : '{}' ?>;


    $("#branch_id").select2();
    appValidateForm($('#suggest_union'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        staff_id: 'required',
        grand_total: 'required',
    }, detail);

    function changePersonel(_this){
        name_department = $(_this).find('option:selected').attr('data-name-department');
        name_role = $(_this).find('option:selected').attr('data-subtext');
        $(".name_department").html((name_department));
        $(".name_role").html((name_role));
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