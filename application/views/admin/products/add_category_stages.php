<?php echo form_open('admin/products/add_category_stages/' . $id, array('id' => 'add-category')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= !$id ? _l('tnh_add_category_stages') : _l('tnh_edit_category_stages'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_code_category_stages', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($categoryStages) ? $categoryStages['code'] : '')), 'placeholder="' . lang('tnh_code_category_stages') . '" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_name_category_stages', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($categoryStages) ? $categoryStages['name'] : '')), 'placeholder="' . lang('tnh_name_category_stages') . '" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<?= lang('tnh_type', 'type_productionlist_id') ?>
					<select name="type_productionlist_id" data-live-search="true" data-none-selected-text="<?= lang('tnh_type') ?>" id="type_productionlist_id" class="form-control selectpicker">
						<option value=""></option>
						<?php if(!empty($type_production_list)): ?>
							<?php foreach($type_production_list as $key => $value): ?>
								<option <?= (!empty($categoryStages) && $categoryStages['type_productionlist_id'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['code'] ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="checkbox checkbox-primary">
							<input type="checkbox" name="is_in" id="is_in" <?php echo (!empty($categoryStages['is_in']) ? 'checked' : '') ?>>
							<label for="is_in">Công đoạn in</label>
						</div>
					</div>
				</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="check_offset" id="check_offset" <?php echo (!empty($categoryStages['check_offset']) ? 'checked' : '') ?>>
                            <label for="check_offset">Công đoạn xuất kẽm</label>
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
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
			// tinymce.get('note').save();
			var data = $(form).serialize();
			var url = form.action;
			$.ajax({
					url: site.base_url + 'admin/products/add_category_stages/<?= $id ?>',
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

	})
</script>