<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('departmental_procedures/handling/' . $id . '/' . $type),
        ['id' => 'departmental_procedures']
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
                        <td><?= lang('Phòng ban', 'department') ?></td>
                        <td>
                            <div class="form-group">
                                <select id="department" class="selectpicker department" data-width="100%" name="department" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option></option>
                                    <?php if (!empty($dtDepartment)) {
                                        foreach ($dtDepartment as $key => $value) { ?>
                                            <option <?= !empty($dtData) && ($dtData['department'] == $value['departmentid']) ? 'selected' : '' ?> data-subtext="<?= $value['code'] ?>" value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </td>
                        <td><?= lang('Vị trí', 'location') ?></td>
                        <td colspan="1">
                            <div class="form-group">
                                <select id="location" class="selectpicker location" data-width="100%" name="location" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option></option>
                                    <?php if (!empty($dtRoles)) {
                                        foreach ($dtRoles as $key => $value) { ?>
                                            <option <?= !empty($dtData) && ($dtData['location'] == $value['roleid']) ? 'selected' : '' ?> data-subtext="<?= $value['code_role'] ?>" value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= lang('Mã quy trình', 'code') ?>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="code" class="form-control code" id="code" value="<?= !empty($dtData) ? ($dtData['code']) : '' ?>">
                            </div>
                        </td>
                        <td>
                            <?= lang('Tên quy trình', 'name') ?>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="name" class="form-control name" id="name" value="<?= !empty($dtData) ? ($dtData['name']) : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Từng Bước Thực Hiện', 'procedures') ?></td>
                        <td colspan="3">
                            <?php echo render_textarea('procedures', '', (!empty($dtData) ? ($dtData['procedures']) : ''), array(), array(), '', 'tinymce'); ?>
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
    init_editor('textarea[name="procedures"]');
    appValidateForm($('#departmental_procedures'), {
        department: 'required',
        location: 'required',
        code: 'required',
        name: 'required',
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
                tinymce.get('procedures').save();
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