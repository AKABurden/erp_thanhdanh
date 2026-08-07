<?php echo form_open('admin/allowance_reduce/edit_callowance_reduce', array('id' => 'edit-allowance_reduce')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Sửa phụ cấp - giảm trừ'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tiêu chí', 'name') ?>
                        <?php echo form_input('name',
                            (isset($_POST['name']) ? $_POST['name'] : $allowance_reduce['name']),
                            'placeholder="' . lang('name') . '" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Loại', 'type') ?>
                        <select name="type" class="type" style="width: 100%" id="type">
                            <option value=""></option>
                            <option <?= $allowance_reduce['type'] == 1 ? 'selected' : '' ?> value="1">Phụ cấp</option>
                            <option <?= $allowance_reduce['type'] == 2 ? 'selected' : '' ?> value="2">Giảm trừ</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('lưu') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function () {
        $("#type").select2();

        appValidateForm($('#edit-allowance_reduce'), {
            name: 'required',
            type: 'required',
        }, addNew);

        function addNew(form) {
            $('.add').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/allowance_reduce/edit_callowance_reduce/' + <?= $allowance_reduce['id'] ?>,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function (data) {
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
                .fail(function () {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>