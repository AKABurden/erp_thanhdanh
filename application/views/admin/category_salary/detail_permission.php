<?php echo form_open('admin/category_salary/detail_permission/'.$id, array('id'=>'permission')); ?>
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
                        <?= lang('Nhóm phép', 'category_permission_id') ?>
                        <select name="category_permission_id" id="category_permission_id" class="category_permission_id" required="required" style="width: 100%;"  data-placeholder="<?= lang('Nhóm phép') ?>">
                            <option value=""></option>
                            <?php if(!empty($dtCategoryPermission)): ?>
                                <?php foreach($dtCategoryPermission as $key => $value): ?>
                                    <option <?= (!empty($dtData) && $dtData['category_permission_id'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Số Ngày Nghỉ Theo QĐ', 'day_off') ?>
                        <?php echo form_input('day_off', (isset($_POST['day_off']) ? $_POST['day_off'] : (!empty($dtData) ? $dtData['day_off'] : '')), 'placeholder="'.lang('Số Ngày Nghỉ Theo QĐ').'" id="day_off" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Hưởng Lương', 'receive_salary') ?>
                        <?php echo form_input('receive_salary', (isset($_POST['receive_salary']) ? $_POST['receive_salary'] : (!empty($dtData) ? $dtData['receive_salary'] : '')), 'placeholder="'.lang('Hưởng Lương').'" id="receive_salary" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Điều Kiện Xác Định', 'conditions') ?>
                        <?php echo form_textarea('conditions', (isset($_POST['conditions']) ? $_POST['conditions'] : (!empty($dtData) ? $dtData['conditions'] : '')), ' id="conditions" class="form-control input-tip"'); ?>
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
        $("#category_permission_id").select2();
        appValidateForm($('#permission'), {
            code: 'required',
            name: 'required',
            category_permission_id: 'required',
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