<?php echo form_open('admin/category_maintenace/detail_category_maintenance/'.$id, array('id'=>'category_maintenance')); ?>
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
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('name').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Chi tiết', 'detail') ?>
                        <?php echo form_textarea('detail', (isset($_POST['detail']) ? $_POST['detail'] : (!empty($dtData) ? $dtData['detail'] : '')), 'placeholder="'.lang('Chi tiết').'" id="detail" class="form-control input-tip tinymce"'); ?>
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
        appValidateForm($('#category_maintenance'), {
            code: 'required',
            name: 'required',
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