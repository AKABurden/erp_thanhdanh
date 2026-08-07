<?php echo form_open('admin/orders/pass_fail/' . $id, array('id' => 'pass-fail')); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_pass_fail') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <b><?= lang('Đơn hàng') ?>:</b>
                    <span><?= $order['reference_no'] ?></span>
                </div>
                <div class="col-md-12">
                    <div class="flex">
                        <div class="radio radio-info">
                            <input type="radio" name="is_pass_fail" id="is_pass_fail_1" value="1" <?= !empty($order_sub['is_pass_fail']) && $order_sub['is_pass_fail'] == 1 ? 'checked' : (!isset($order_sub['is_pass_fail']) ? 'checked' : '') ?> checked="checked">
                            <label for="is_pass_fail_1"><?= lang('Đạt') ?></label>
                        </div>
                        <div class="radio radio-info" style="margin-top: 10px; margin-left: 10px;">
                            <input type="radio" name="is_pass_fail" id="is_pass_fail_2" <?= !empty($order_sub['is_pass_fail']) && $order_sub['is_pass_fail'] == 2 ? 'checked' : '' ?> value="2">
                            <label for="is_pass_fail_2"><?= lang('Không đạt') ?></label>
                        </div>
                        <div class="radio radio-info" style="margin-top: 10px; margin-left: 10px;">
                            <input type="radio" name="is_pass_fail" id="is_pass_fail_0" <?= !empty($order_sub) && $order_sub['is_pass_fail'] == 0 ? 'checked' : '' ?> value="0">
                            <label for="is_pass_fail_0"><?= lang('Bỏ chọn') ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_note_pass_fail', 'note_pass_fail') ?>
                        <textarea name="note_pass_fail" id="note_pass_fail" class="form-control note_pass_fail" placeholder="<?= lang('tnh_note_pass_fail') ?>" rows="3" required="required"><?= !empty($order_sub['note_pass_fail']) ? $order_sub['note_pass_fail'] : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add-cancel"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function () {
        appValidateForm($('#pass-fail'), {
            'note_pass_fail': 'required',
        }, handlingPassFail);
        function handlingPassFail(form) {
            $('.add-cancel').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw(false);
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add-cancel').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    $('.add-cancel').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
    })
</script>