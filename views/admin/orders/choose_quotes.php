<?php echo form_open('admin/orders/choose_quotes/' . $id, array('id' => 'choose-quotes')); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_choose_quotes') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <b><?= lang('Đơn hàng') ?>:</b>
                    <span><?= $order['reference_no'] ?></span>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_quotes', 'quote_id_chonse') ?>
                        <input type="text" name="quote_id_chonse" id="quote_id_chonse" data-placeholder="<?= lang('tnh_quotes') ?>" class="quote_id_chonse modal-select2" style="width: 100%;" value="<?= !empty($order_sub['quote_id_chonse']) ? $order_sub['quote_id_chonse'] : '' ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add-choose-quotes"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function () {
        ajaxSelectParams('#quote_id_chonse', 'admin/quotes/searchPreReferenceNoQuotes', $('#quote_id_chonse').val(), false, true);
        appValidateForm($('#choose-quotes'), {
        }, handlingChooseQuotes);

        function handlingChooseQuotes(form) {
            $('.add-choose-quotes').attr('disabled', 'disabled');
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
                        $('.add-choose-quotes').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    $('.add-choose-quotes').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
    })
</script>