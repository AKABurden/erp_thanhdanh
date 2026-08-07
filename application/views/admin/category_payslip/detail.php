<?php echo form_open_multipart('admin/category_payslip/detail/' . (!empty($id) ? $id : ''),
    array('id' => 'category-payslip')); ?>
<div class="modal-dialog" style="min-width: 40%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã danh mục', 'code') ?>
                        <input type="text" name="code" id="code" placeholder="<?= lang('Mã danh mục') ?>"
                               class="form-control code" value="<?= !empty($dtData) ? $dtData['code'] : '' ?>"
                               required="required">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tên danh mục', 'name') ?>
                        <input type="text" name="name" id="name" placeholder="<?= lang('Tên danh mục') ?>"
                               class="form-control name" value="<?= !empty($dtData) ? $dtData['name'] : '' ?>"
                               required="required">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function () {
        init_selectpicker();
        appValidateForm($('#category-payslip'), {
            name: 'required',
            code: 'required',
        }, handlingData);

        function handlingData(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(formParams, function (i, val) {
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
                .done(function (data) {
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
                .fail(function () {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })

</script>