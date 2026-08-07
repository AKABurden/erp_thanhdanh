<div class="modal fade" id="modal_create_suggestion" role="dialog">
    <div class="modal-dialog modal-lg">
		<?php echo form_open(admin_url('internal_proposal/create_suggestion/' . $id), array('id' => 'create_suggestion-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="hide">
                            <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?php echo $id ?>">
                        </div>
                        <?= render_select('id_payment_modes', (!empty($payment_modes) ? $payment_modes : []), ['id', 'name', 'description'], 'Hình thức thanh toán');?>

                        <?= render_select('staff_browse', (!empty($staff_browse) ? $staff_browse : []), ['staffid', ['firstname', 'lastname']], 'Người duyệt chi');?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    // init_editor('textarea[name="content"]');
    $('#modal_create_suggestion').modal('show');
    init_selectpicker();
    $(function () {
        appValidateForm($('#create_suggestion-form'), {
            id_payment_modes: 'required',
        }, manage);

        function manage(form) {
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });
            var button = $(form).find('button[type="submit"]');
            button.button({loadingText: 'please wait...'});
            button.button('loading');

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function (response) {
                if (response.success == true) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                oTable.draw();
                $('#modal_create_suggestion').modal('hide');
            })
            .always(function() {
                button.button('reset');
            })
            .fail(function () {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
                button.button('reset');
            });
            return false;
        }
    });
</script>