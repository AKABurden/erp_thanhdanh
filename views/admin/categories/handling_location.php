<?php echo form_open('admin/categories/handlingLocation/'.$id.'/'.$actions, array('id'=>'handling-location')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= $actions == "add" ? _l('tnh_add_location') : _l('tnh_edit_location'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('code', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($location) ? $location['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('name', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($location) ? $location['name'] : '')), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : (!empty($location) ? $location['note'] : '')), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= $actions == "add" ? _l('add') : _l('edit'); ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
       	appValidateForm($('#handling-location'), {
           code: 'required',
           name: 'required'
        }, handlingLocation);

        function handlingLocation(form) {
        	$('.add').attr('disabled', 'disabled');
            var url = form.action;
            tinymce.get('note').save();
            var data = $(form).serialize();
            $.ajax({
            	url: url,
            	type: 'POST',
            	dataType: 'JSON',
            	data: data,
            })
            .done(function(data) {
            	if (data.result) {
            		alert_float('success', data.message);
            		if (typeof oTable != 'undefined') {
            			oTable.draw();
            		}
            		$('.modal-dialog .close').trigger('click');
            	} else {
            		alert_float('danger', data.message);
            		$('.add').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
            	console.log("error");
            });
            return false;
        }
        init_editor('textarea[name="note"]');
    })
</script>