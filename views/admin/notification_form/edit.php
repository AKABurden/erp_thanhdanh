<?php echo form_open('admin/notification_form/edit/'.$category['id'], array('id'=>'edit-category')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('edit_notification_form'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('code_notification_form', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : $category['code']), 'placeholder="'.lang('code_notification_form').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('name_notification_form', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : $category['name']), 'placeholder="'.lang('name_notification_form').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('type', 'type') ?>
                        <select name="type" id="type" data-placeholder="<?= lang('type') ?>" style="width: 100%" class="modal-select2" required="required">
                            <option value=""></option>
                            <?php foreach (typeNotificationForm() as $key => $value): ?>
                                <option <?= $category['type'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary edit"><?= _l('edit') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $('#type').select2();
        appValidateForm($('#edit-category'), {
           code: 'required',
           name: 'required',
           type: 'required'
        }, editCategory);

        function editCategory(form) {
            $('.edit').attr('disabled', 'disabled');
            var url = form.action;
            // tinymce.get('note').save();
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
                    $('.edit').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                console.log("error");
            });
            return false;
        }
    })
</script>