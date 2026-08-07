<?php echo form_open('admin/vouchers_coupon/edit_bank/' . $id, array('id' => 'edit-bank')); ?>
<div class="modal-dialog modal-lg" style="width: 30%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Cập nhật phiếu hải quan'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('date') ?>: </div>
                            <div class="ml-at t-bold"><?= _dhau($voucher['date_vouchers']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_deliveries') ?>: </div>
                            <div class="ml-at t-bold"><?= ($voucher['code_vouchers']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mbot10">
                    <div class="from-group">
                        <?= lang('Phiếu báo có NH', 'code_bank') ?>
                        <input type="text" name="code_bank" id="code_bank" class="form-control code_bank" value="<?= ($voucher['code_bank'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-12 mbot10">
                    <div class="from-group">
                        <?= lang('Ngày báo có NH', 'date_bank') ?>
                        <input type="text" name="date_bank" id="date_bank" class="form-control date_bank datepicker" value="<?= !empty($voucher['date_bank']) ? _dhau($voucher['date_bank']) : '' ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add" data-type="1"><?= _l('save') ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
</script>
<script>
    $(function() {
        init_datepicker();
        appValidateForm($('#edit-bank'), {
        }, editDiscount);

        function editDiscount(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            // var data = $(form).serialize();
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

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
                        if (typeof tAPIs != 'undefined' && tAPIs != '') {
                            tAPIs.draw();
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