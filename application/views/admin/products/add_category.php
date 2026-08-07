<?php echo form_open('admin/products/add_category', array('id' => 'add-category')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_add_category'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_category_code', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : ''), 'placeholder="' . lang('code') . '" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_category_name', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ''), 'placeholder="' . lang('name') . '" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_group_parent', 'parent_id') ?>
						<select name="parent_id" id="parent_id" data-placeholder="<?= lang('tnh_group_parent') ?>" class="modal-select2" style="width: 100%;">
							<option value=""></option>
							<?= recursiveCategoryProducts() ?>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_customers_groups', 'customers_groups') ?>
						<select name="customers_groups[]" id="customers_groups" class="form-control selectpicker ajax-select" data-language="vi_VN" data-live-search="true" multiple="true" data-actions-box="1" data-none-selected-text="<?= lang('tnh_customers_groups') ?>">
						<?php if(!empty($customers_groups)): ?>
							<?php foreach($customers_groups as $key => $value): ?>
								<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="' . lang('note') . '" id="note" class="form-control input-tip tinymce"'); ?>
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
	$(function() {
		init_selectpicker();
		$('#parent_id').select2({
			allowClear: true
		});
		appValidateForm($('#add-category'), {
			code: 'required',
			name: 'required'
		}, addCategory);

		function addCategory(form) {
			$('.add').attr('disabled', 'disabled');
			tinymce.get('note').save();
			var data = $(form).serialize();
			var url = form.action;
			$.ajax({
					url: site.base_url + 'admin/products/add_category',
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