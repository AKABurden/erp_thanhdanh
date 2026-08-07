<?php echo form_open_multipart('admin/categories_other/handlingUnitWarehouse/'.$id.'/'.$status, array('id' => 'handling-unit-warehouse')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã đơn vị', 'code_unit_warehouse') ?>
                        <input type="text" name="code_unit_warehouse" id="code_unit_warehouse" placeholder="<?= lang('Mã đơn vị') ?>" class="form-control name" value="<?= !empty($unit_warehouse) ? $unit_warehouse['code_unit_warehouse'] : '' ?>" required="required">
                    </div>
                    <div class="form-group">
                        <?= lang('Tên đơn vị', 'name') ?>
                        <input type="text" name="name" id="name" placeholder="<?= lang('Tên đơn vị') ?>" class="form-control name" value="<?= !empty($unit_warehouse) ? $unit_warehouse['name'] : '' ?>" required="required">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {
        appValidateForm($('#handling-unit-warehouse'), {
            code_unit_warehouse: 'required',
            name: 'required',
        }, handlingData);

        function handlingData(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined' && oTable != '') {
                        oTable.draw();
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    })
</script>