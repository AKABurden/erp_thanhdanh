<?php echo form_open('admin/orders/cancel/' . $id.'/'.$is_end, array('id' => 'cancel-orders')); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $is_end ? lang('Kết thúc đơn hàng') : lang('tnh_cancel_order') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <b><?= lang('Đơn hàng') ?>:</b>
                    <span><?= $order['reference_no'] ?></span>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= $is_end ? lang('Ghi chú kết thúc đơn hàng', 'note_cancel') : lang('tnh_note_cancel', 'note_cancel') ?>
                        <textarea name="note_cancel" id="note_cancel" class="form-control note_cancel" placeholder="<?= $is_end ? lang('Ghi chú kết thúc đơn hàng') : lang('tnh_note_cancel') ?>" rows="3" required="required"></textarea>
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
        appValidateForm($('#cancel-orders'), {
            'note_cancel': 'required'
        }, handlingCancel);
        function handlingCancel(form) {
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