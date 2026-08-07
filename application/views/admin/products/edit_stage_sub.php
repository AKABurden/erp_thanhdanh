<?php echo form_open('admin/products/edit_stage_sub/'.$stage['id'], array('id'=>'edit-stage')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_edit_stage'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('code', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : $stage['code']), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('name', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : $stage['name']), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('departments', 'departments') ?>
                        <select name="departments" data-placeholder="<?= lang('departments') ?>" id="departments" class="" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($departments as $key => $value): ?>
                                <option <?= $stage['departments_id'] == $value['departmentid'] ? 'selected' : '' ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_number_hours', 'number_hours') ?>
                        <?php echo form_input('number_hours', (isset($_POST['number_hours']) ? $_POST['number_hours'] : $stage['number_hours']), 'placeholder="'.lang('tnh_number_hours').'" id="number_hours" onkeyup="formatNumBerKeyUpCus(this)" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_sequence', 'sequence') ?>
                        <input type="number" name="sequence" id="sequence" class="form-control" value="<?= $stage['sequence'] ?>" step="1">
                    </div>
                </div>
				<div class="col-md-12">
					<div class="form-group">
						<div class="checkbox checkbox-info">
							<input <?= (!empty($stage['status_qc']) ? 'checked' : '') ?> type="checkbox" name="status_qc" id="status_qc" value="1">
							<label for="status_qc"><?= lang('tnh_status_qc') ?></label>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $stage['note']), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('tnh_edit') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $('#departments').select2();
       	appValidateForm($('#edit-stage'), {
           code: 'required',
           name: 'required'
        }, addstage);

        function addstage(form) {
        	$('.add').attr('disabled', 'disabled');
            tinymce.get('note').save();
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
            	url: site.base_url+'admin/products/edit_stage_sub/<?= $stage['id'] ?>',
            	type: 'POST',
            	dataType: 'JSON',
            	data: data,
            })
            .done(function(data) {
            	if (data.result) {
            		alert_float('success', data.message);
            		if (typeof oTable != 'undefined') {
            			oTable.draw('page');
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