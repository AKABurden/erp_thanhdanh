<div class="modal fade" id="view_modal" role="dialog">
    <div class="modal-dialog" style="min-width: 10%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/production_report/add_production_report/', array('id' => 'production_report')); ?>
            <div class="modal-body">
                <input class="hide" id="process_id" name="process_id" value="<?= $process_id ?>">
                <input class="hide" id="id" name="id" value="<?= $id ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="div-items-delivery_records"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <?php if ($is == 0) { ?>
                    <button type="submit" class="btn btn-primary add-finished-stages"><?= _l('Xác nhận') ?></button>
                    <div class="checkbox checkbox-danger pull-right hide">
                        <input type="checkbox" class="save_create_production_report" id="save_create_production_report" value="1">
                        <label for="save_create_production_report">Tạo phiếu báo cáo</label>
                    </div>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $('#view_modal').modal('show');
    var data = {
        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
        'process_id': '<?= $process_id ?>',
        'id': '<?= $id ?>',
        'is': '<?= $is ?>',
    };
    $.post(admin_url + 'production_report/get_table_delivery_records_internal_proposal', data, function(result) {
        $('.div-items-delivery_records').html(result);
    })
    $(function() {
        appValidateForm($('#production_report'), {
            receiver: "required",
        }, saveHandlingProducts);

        function saveHandlingProducts(form) {
            $('.add').attr('disabled', 'disabled');
            // var data = $(form).serialize();
            var url = form.action;
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
                    if (data.success) {
                        if (data.id_delivery_records && $('#save_create_production_report').prop('checked')) {
                            window.open(data.href, '_blank');
                        }
                        $('.add').removeAttr('disabled', 'disabled');
                        alert_float(data.alert_type, data.message);
                        if (typeof TableData != 'undefined' && TableData != '') {
                            TableData.draw('page');
                        }
                        $('.modal-dialog .close').trigger('click');
                        return false;
                    }

                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof TableData != 'undefined' && TableData != '') {
                            TableData.draw('page');
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
        <?php if (empty($category_hand_over)) { ?>
            // setTimeout(function() {
            //     var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
            //     if (r == false) {
            //         $('.modal-dialog .close').trigger('click');
            //     } else {
            //         $('.add-finished-stages').click();
            //     }
            // }, 1000);
        <?php } ?>
    })
    init_selectpicker();
</script>