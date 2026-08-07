<?php echo form_open('admin/quality_control/add_category_error',array('id'=>'add-category-error')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_add_category_error'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_category_code_error', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : ''), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_category_name_error', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ''), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_group_parent', 'parent_id') ?>
                        <select name="parent_id" id="parent_id" data-placeholder="<?= lang('tnh_group_parent') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?= recursiveCategoryErrors() ?>
                        </select>
                    </div>
                </div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('add') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $('#parent_id').select2({allowClear: true});
       	appValidateForm($('#add-category-error'), {
           code: 'required',
           name: 'required'
        }, addCategory);

        function addCategory(form) {
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
            	alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
        init_editor('textarea[name="note"]');
    })
</script>