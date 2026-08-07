<?php echo form_open('admin/salary3P/detail/'.$id.'/'.$action, array('id'=>'step-salary')); ?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã vị trí', 'role_id') ?>
                            <input type="text" name="role_id" id="role_id" class="role_id modal-select2"
                                   data-placeholder="<?= lang('Mã vị trí') ?>" onchange="getCodeAuto(this)" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_id'] : '' ?>"
                                   title="">
                        </div>
                    </div>
                     <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã cấp bậc vai trò', 'role_level_id') ?>
                            <input type="text" name="role_level_id" id="role_level_id" class="role_level_id modal-select2"
                                   data-placeholder="<?= lang('Mã cấp bậc vai trò') ?>" onchange="getCodeAuto(this)" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_level_id'] : '' ?>"
                                   title="">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã danh mục thâm niên', 'grade_id') ?>
                            <input type="text" name="grade_id" id="grade_id" class="grade_id modal-select2"
                                   data-placeholder="<?= lang('Mã danh mục thâm niên') ?>" onchange="getCodeAuto(this)" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['grade_id'] : '' ?>"
                                   title="">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('code', 'code') ?>
                            <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('code').'" id="code" readonly  required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Version', 'version') ?>
                            <?php echo form_input('version', (isset($_POST['version']) ? $_POST['version'] : (!empty($dtData) ? $dtData['version'] : '')), 'placeholder="'.lang('Version').'" readonly id="version" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày hiệu lực', 'effective_from') ?>
                            <?php echo form_input('effective_from', (isset($_POST['effective_from']) ? $_POST['effective_from'] : (!empty($dtData) ? _dhau($dtData['effective_from']) : date('d/m/Y'))), 'placeholder="'.lang('Ngày hiệu lực').'" id="effective_from" class="form-control datepicker input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày hết hiệu lực', 'effective_to') ?>
                            <?php echo form_input('effective_to', (isset($_POST['effective_to']) ? $_POST['effective_to'] : (!empty($dtData) ? _dhau($dtData['effective_to']) : date('d/m/Y'))), 'placeholder="'.lang('Ngày hết hiệu lực').'" id="effective_to" class="form-control datepicker input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('note', 'note') ?>
                            <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : (!empty($dtData) ? $dtData['note'] : '')), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Hệ số', 'coef') ?>
                            <?php echo form_input('coef', (isset($_POST['coef']) ? $_POST['coef'] : (!empty($dtData) ? $dtData['coef'] : 1)), 'id="coef" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Lương P1', 'salary_p1') ?>
                            <?php echo form_input('salary_p1', (isset($_POST['salary_p1']) ? $_POST['salary_p1'] : (!empty($dtData) ? formatMoney($dtData['salary_p1']) : 0)), 'id="salary_p1" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Lương P2', 'salary_p2') ?>
                            <?php echo form_input('salary_p2', (isset($_POST['salary_p2']) ? $_POST['salary_p2'] : (!empty($dtData) ? formatMoney($dtData['salary_p2']) : 0)), 'id="salary_p2" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Lương P3', 'salary_p3') ?>
                            <?php echo form_input('salary_p3', (isset($_POST['salary_p3']) ? $_POST['salary_p3'] : (!empty($dtData) ? formatMoney($dtData['salary_p3']) : 0)), 'id="salary_p3" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Phụ cấp P3', 'allowed_p3') ?>
                            <?php echo form_input('allowed_p3', (isset($_POST['allowed_p3']) ? $_POST['allowed_p3'] : (!empty($dtData) ? formatMoney($dtData['allowed_p3']) : 0)), 'id="allowed_p3" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ghi chú phụ cấp P3', 'allowed_p3_note') ?>
                            <?php echo form_textarea('allowed_p3_note', (isset($_POST['allowed_p3_note']) ? $_POST['allowed_p3_note'] : (!empty($dtData) ? $dtData['allowed_p3_note'] : '')), 'id="allowed_p3_note" class="form-control input-tip tinymce"'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : ($action == 'copy' ? _l('save') : _l('edit')); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        ajaxSelectParams('#grade_id', 'admin/salary3P/searchCategoryGrade', $("#grade_id").val(), true, true);
        ajaxSelectParams('#role_level_id', 'admin/salary3P/searchRoleLevel', $("#role_level_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            code: 'required',
            grade_id: 'required',
            role_level_id: 'required',
            version: 'required',
            effective_from: 'required',
            role_id: 'required',
        }, handling);

        function handling(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var data = $(form).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
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
                })
                .fail(function() {
                    console.log("error");
                });
            return false;
        }
    })

    <?php if (!empty($id)){ ?>
    getCodeAuto();
    <?php } ?>

    function getCodeAuto(){
        role_id = $("#role_id").val();
        grade_id = $("#grade_id").val();
        role_level_id = $("#role_level_id").val();
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['role_id'] = role_id;
        dataPOST['grade_id'] = grade_id;
        dataPOST['role_level_id'] = role_level_id;

        $.ajax({
            type: "get",
            url: site.base_url + 'admin/salary3P/getCodeAuto',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                $("#code").val(response.code);
                $("#version").val(response.version);
            },
            error: function(xhr, status, error) {
            },
        });
    }
</script>