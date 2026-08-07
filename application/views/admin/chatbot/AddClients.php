<?php echo form_open('admin/clients/addFlashCustomerai', array('id' => 'add-flash-customer')); ?>
<div class="modal-dialog modal2">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_add_customer') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="zcode"><?php echo _l('cong_zcode'); ?></label>
                        <input type="text" name="zcode" id="zcode" class="form-control zcode" value="" placeholder="<?= _l('system_default_string') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('clients_list_company', 'company') ?>
                        <?php echo form_input('company', (isset($_POST['company']) ? $_POST['company'] : ''), 'placeholder="' . lang('clients_list_company') . '" id="company" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('c_company_short', 'company_short') ?>
                        <?php echo form_input('company_short', (isset($_POST['company_short']) ? $_POST['company_short'] : ''), 'placeholder="' . lang('c_company_short') . '" id="company_short" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_phone', 'phone') ?>
                        <?php echo form_input('phone', (isset($_POST['phone']) ? $_POST['phone'] : ''), 'placeholder="' . lang('tnh_phone') . '" id="phone" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Email', 'email') ?>
                        <?php echo form_input('email', (isset($_POST['email']) ? $_POST['email'] : ''), 'placeholder="' . lang('email') . '" id="email" class="form-control input-tip"'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary add-submit"><?= lang('save') ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    function addCustomerFlash(form) {
        $('.add-submit').attr('disabled', 'disabled');
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
                if (data.result) {
                    addBotBubble(data.reply, true);
                    alert_float('success', data.message);
                    $('.modal2 .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add-submit').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add-submit').removeAttr('disabled', 'disabled');
            });
        return false;
    }

    $(document).ready(function() {
        appValidateForm($('#add-flash-customer'), {
            company: 'required',
            company_short: 'required',
        }, addCustomerFlash);


    });
</script>