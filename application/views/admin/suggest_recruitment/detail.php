<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('suggest_recruitment/detail/' . $id),
        ['id' => 'suggest_recruitment']); ?>
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
                    <td><?= lang('Vị trí tuyển dụng', 'position_recruitment') ?></td>
                    <td colspan="1">
                        <select type="text" name="position_recruitment" id="position_recruitment"
                               class="position_recruitment form-control selectpicker"
                                onchange="changePositionRecruiment(this)"
                                data-live-search="true"
                                data-none-selected-text="<?= lang('Vị trí tuyển dụng') ?>">
                            <option></option>
                            <?php if (!empty($roles)){ ?>
                                <?php foreach ($roles as $key => $value){?>
                                    <option <?= !empty($dtData) ? ($dtData['position_recruitment'] == $value['roleid'] ? 'selected' : '') : '' ?> value="<?= $value['roleid'] ?>"><?= $value['name'].'('.$value['code_role'].')' ?></option>
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
                    <td><?= lang('Số lượng', 'quantity') ?></td>
                    <td>
                        <input type="text" name="quantity" id="quantity"
                               class="quantity number-format form-control"
                               value="<?= !empty($dtData) ? $dtData['quantity'] : '' ?>"
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
                    <td><?= lang('Thời gian làm việc', 'time_work') ?></td>
                    <td>
                        <input type="text" name="time_work" id="time_work"
                               class="time_work form-control"
                               value="<?= !empty($dtData) ? $dtData['time_work'] : '' ?>"
                               title="">
                    </td>
                    <td><?= lang('Giới tính', 'gender') ?></td>
                    <td colspan="1">
                        <select name="gender" id="gender"
                                data-placeholder="<?= lang('tnh_gender') ?>" class="gender"
                                style="width: 100%;">
                            <option value=""></option>
                            <option
                                <?= !empty($dtData['gender']) && $dtData['gender'] == "male" ? 'selected' : '' ?>
                                    value="male"><?= lang('tnh_male') ?></option>
                            <option
                                <?= !empty($dtData['gender']) && $dtData['gender'] == "female" ? 'selected' : '' ?>
                                    value="female"><?= lang('tnh_female') ?></option>
                            <option
                                <?= !empty($dtData['gender']) && $dtData['gender'] == "other" ? 'selected' : '' ?>
                                    value="other"><?= lang('tnh_other') ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Định mức thời gian hoàn thành', 'completion_time_limit') ?></td>
                    <td>
                        <input type="text" name="completion_time_limit" id="completion_time_limit"
                               class="completion_time_limit form-control"
                               value="<?= !empty($dtData) ? $dtData['completion_time_limit'] : '' ?>"
                               title="">
                    </td>
                    <td><?= lang('Tiêu chuẩn/ quy định', 'standard') ?></td>
                    <td colspan="1">
                        <input type="text" name="standard" id="standard"
                               class="standard form-control"
                               value="<?= !empty($dtData) ? $dtData['standard'] : '' ?>"
                               title="">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Mô tả công việc', 'content') ?></td>
                    <td colspan="3">
                        <textarea name="content_work" id="content_work" class="form-control content_work tinymce" cols="3"
                                  rows="4"><?= !empty($dtData) ? $dtData['content_work'] : '' ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Lý do', 'note') ?></td>
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
    $(function() {
        tinyMCE.remove('#content_work');
        init_editor('textarea[name="content_work"]');
    });
    init_datepicker();
    init_selectpicker('refresh');
    ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
    $("#branch_id").select2();
    $("#category_plan").select2();
    $("#staff_id").select2();
    $("#gender").select2();
    appValidateForm($('#suggest_recruitment'), {
        date: 'required',
        position_recruitment: 'required',
        reference_no: 'required',
        branch_id: 'required',
        gender: 'required',
    }, detail);

    function detail(form) {
        $('.add').attr('disabled', 'disabled');
        tinymce.get('content_work').save();
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

    function changePositionRecruiment(_this){
        position_recruiment = $(_this).val();
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/suggest_recruitment/changePositionRecruiment',
            data: {
                csrf_token_name:hash,
                position_recruiment:position_recruiment
            },
            dataType: "json",
            success: function (response) {
                tinymce.get('content_work').setContent(response.html);
            },
            error: function (xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }
</script>