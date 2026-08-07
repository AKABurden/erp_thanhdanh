<?php echo form_open('admin/contracts_sales/sendMail/'.$id, array('id'=>'send-email')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('Email'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_title_email', 'title') ?>
                        <?php echo form_input('title', $contract->subject.' '.$contract->prefix.'-'.$contract->code, 'placeholder="'.lang('tnh_title_email').'" id="title" class="form-control input-tip" required'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Email', 'email') ?>
                        <?php echo form_input('email', $customer['email_client'], 'placeholder="'.lang('Email').'" id="email" class="form-control input-tip" required'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('email_template_email_message', 'content') ?>
                        <?php echo form_textarea('content', (isset($_POST['content']) ? $_POST['content'] : ''), 'placeholder="'.lang('content').'" id="content" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
            </div>
		</div>
		<div class="modal-footer">
            <input type="hidden" name="send" id="send" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('send') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        init_editor('textarea[name="content"]');
       	appValidateForm($('#send-email'), {
            'title': 'required',
            'email': 'required',
        }, convert);

        function convert(form) {
        	$('.add').attr('disabled', 'disabled');
            tinymce.get('content').save();
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
            	url : url,
            	type : 'POST',
            	dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
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