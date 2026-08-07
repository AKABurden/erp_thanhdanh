<?php echo form_open_multipart('admin/request_sample_cover/handlingRequestSampleCover/'.$id.'/'.$status, array('id' => 'handling-request-sample-cover')); ?>
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
                        <?= lang('Mã số phiếu', 'code') ?>
                        <input type="text" name="code" id="code" placeholder="<?= lang('Mã số phiếu') ?>" class="form-control code" value="<?= !empty($request_sample_cover) ? $request_sample_cover['code'] : '' ?>" required="required">
                    </div>
                    <div class="form-group">
                        <?= lang('Ngày lập phiếu', 'date') ?>
                        <input type="text" name="date" id="date" autocomplete="off" placeholder="<?= lang('Ngày lập phiếu') ?>" class="form-control date datetimepicker" value="<?= !empty($request_sample_cover) ? $request_sample_cover['date'] : date('d/m/Y H:i:s') ?>" required="required">
                    </div>
                    <div class="form-group">
                        <?= lang('Sản phẩm', 'product_id') ?>
                        <input type="text" name="product_id" id="product_id" data-placeholder="<?= lang('Sản phẩm') ?>" value="<?= !empty($request_sample_cover) ? $request_sample_cover['product_id'] : '' ?>" class="modal-select2" style="width: 100%;" required="required">
                    </div>
                    <div class="form-group">
                        <?= lang('Khách hàng', 'customer_id') ?>
                        <input type="text" name="customer_id" id="customer_id" data-placeholder="<?= lang('Khách hàng') ?>" value="<?= !empty($request_sample_cover) ? $request_sample_cover['customer_id'] : '' ?>" class="modal-select2" style="width: 100%;" required="required">
                    </div>
                    <div class="form-group">
                        <?= lang('Bìa mẫu', 'sample_cover_id') ?>
                        <input type="text" name="sample_cover_id" id="sample_cover_id" data-placeholder="<?= lang('Khách hàng') ?>" value="<?= !empty($request_sample_cover) ? $request_sample_cover['sample_cover_id'] : '' ?>" class="modal-select2" style="width: 100%;" required="required">
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
        init_datepicker();
        ajaxSelectParamsCallback('#product_id', 'admin/products/searchProductsSelect2', $('#product_id').val(), false, true);
        ajaxSelectParams('#customer_id', 'admin/clients/searchOnlyCustomers', $('#customer_id').val(), true, true);
        ajaxSelectParams('#sample_cover_id', 'admin/request_sample_cover/searchStandardSampleCode', $('#sample_cover_id').val(), true, true);

        appValidateForm($('#handling-request-sample-cover'), {
            code: 'required',
            date: 'required',
            product_id: 'required',
            customer_id: 'required',
            sample_cover_id: 'required',
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