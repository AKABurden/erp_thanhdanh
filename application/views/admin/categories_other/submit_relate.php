<?php echo form_open_multipart('admin/categories_other/submit_relate/' . $id . '/' . $status, array('id' => 'handling-relate')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= render_select('parent_id', $arrRelateList, ['id', 'code', 'name'], 'Danh mục cha', ($parent_id ?? ''), ['disabled' => true]) ?>
                    <input name="parent_id" type="hidden" value="<?= ($parent_id ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã', 'code') ?>
                        <input type="text" name="code" id="code" placeholder="<?= lang('Mã') ?>" class="form-control code" value="<?= !empty($relate) ? $relate['code'] : '' ?>" required="required">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tên', 'name') ?>
                        <input type="text" name="name" id="name" placeholder="<?= lang('Tên') ?>" class="form-control name" value="<?= !empty($relate) ? $relate['name'] : '' ?>" required="required">
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
        init_selectpicker();
        appValidateForm($('#handling-relate'), {
            code: 'required',
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