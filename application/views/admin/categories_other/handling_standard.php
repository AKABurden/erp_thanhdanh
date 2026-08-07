<?php echo form_open_multipart('admin/categories_other/handlingStandard/'.$id.'/'.$status, array('id' => 'handling-standard')); ?>
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
                        <?= lang('Loại tiêu chuẩn', 'type') ?>
                        <input type="text" name="type" id="type" placeholder="<?= lang('Loại tiêu chuẩn') ?>" class="form-control type" value="<?= !empty($standard) ? $standard['type'] : '' ?>" required="required">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tên tiêu chuẩn', 'name') ?>
                        <input type="text" name="name" id="name" placeholder="<?= lang('Tên tiêu chuẩn') ?>" class="form-control name" value="<?= !empty($standard) ? $standard['name'] : '' ?>" required="required">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Chỉ số', 'numeral') ?>
                        <input type="text" name="numeral" id="numeral" placeholder="<?= lang('Chỉ số') ?>" class="form-control storage_time number-format" value="<?= !empty($standard) ? $standard['numeral'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Thời gian lưu kho', 'storage_time') ?>
                        <input type="text" name="storage_time" id="storage_time" placeholder="<?= lang('Thời gian lưu kho') ?>" class="form-control storage_time number-format" value="<?= !empty($standard) ? $standard['storage_time'] : '' ?>">
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
        appValidateForm($('#handling-standard'), {
            type: 'required',
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