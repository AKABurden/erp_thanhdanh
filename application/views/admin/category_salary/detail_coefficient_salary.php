<?php echo form_open('admin/category_salary/detail_coefficient_salary/'.$id, array('id'=>'coefficient-salary')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('code', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('name', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Loại', 'type') ?>
                       <select class="type" name="type" id="type" data-placeholder="Loại" style="width: 100%">
                           <option></option>
                           <option <?= !empty($dtData) && $dtData['type'] == 1 ? 'selected' : '' ?> value="1">Chức vụ</option>
                           <option <?= !empty($dtData) && $dtData['type'] == 2 ? 'selected' : '' ?> value="2">Trách nhiệm</option>
                       </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Chức vụ', 'role_id') ?>
                        <select class="role_id modal-select2" name="role_id" id="role_id" data-placeholder="Chức vụ" style="width: 100%">
                            <option></option>
                            <?= recursiveRoles() ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Hệ số', 'coefficient') ?>
                        <?php echo form_input('coefficient', (isset($_POST['coefficient']) ? $_POST['coefficient'] : (!empty($dtData) ? $dtData['coefficient'] : '')), 'placeholder="'.lang('Hệ số').'" id="coefficient" class="form-control number-format input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : (!empty($dtData) ? $dtData['note'] : '')), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : _l('edit'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $("#type").select2();
        $("#role_id").select2();
        $('#role_id').val(<?= !empty($dtData) ?  $dtData['role_id'] : 0 ?>).trigger('change');
        appValidateForm($('#coefficient-salary'), {
            code: 'required',
            name: 'required',
            type: 'required',
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
</script>