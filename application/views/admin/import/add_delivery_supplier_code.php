<?php echo form_open('admin/import/add_delivery_supplier_code/'.$id, array('id' => 'handling-update-code')); ?>
<style>
    .table-keep tr td {
        padding: 5px;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 40%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Cập nhập phiếu giao hàng nhà cung cấp') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <?php
                $delivery_supplier_code = !empty($dataImport['delivery_supplier_code']) ? ($dataImport['delivery_supplier_code']) : null;
                ?>
                <div class="col-md-12">
                    <?= lang('Số phiếu giao hàng', 'delivery_supplier_code') ?>
                    <input type="text" name="delivery_supplier_code" class="form-control delivery_supplier_code" value="<?= $delivery_supplier_code ?>">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add-update-date"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {
        init_selectpicker();
        appValidateForm($('#handling-update-code'), {
        }, handlingUpdateDate);

        function handlingUpdateDate(form) {
            $('.add-update-date').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        tAPI.draw(false);
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add-update-date').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    $('.add-update-date').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }

        $('.status').selectpicker();
    })
</script>