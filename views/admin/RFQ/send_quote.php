<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-'.$id; ?>" id="send_quote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open('admin/RFQ/send_to_email/', array('id' => 'send_mail-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $title; ?>
                </h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('cc','CC'); ?>
                            <?php echo render_input('subject', 'ch_subject',$emailtemplates->subject); ?>
                            <div id="text"></div>
                            
                            <?php echo form_hidden('id',$id); ?>
                            <?php echo form_hidden('suppliers',$suppliers_id); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                        <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"  class="btn btn-info"><?php echo _l('send'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
<script type="text/javascript">
    $(document).ready(function() {
        init_reset();
        init_editor();
    });
    $(function(){
        // validate_invoice_form();
        _validate_form($('#send_mail-form'), {
        email: "required",
        subject: "required",
        content: "required",
    },send_mail);
    });
  function send_mail(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#send_quote').modal('hide');
                alert_float('success', response.message);
            }
        })
        return false;
    }
</script>