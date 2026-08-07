<?php echo form_open('admin/categories/handlingInsurrance/'.$id.'/'.$actions, array('id'=>'handling-insurrance')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= $actions == "add" ? _l('tnh_add_insurrance') : _l('tnh_edit_insurrance'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_hinhthuc', 'ht') ?>
                        <select name="ht" id="ht" class="ht" style="width: 100%;" required="required">
                            <option <?= $insurrance['form'] == 1 ? 'selected' : '' ?> value="1"><?= lang('tnh_bt') ?></option>
                            <option <?= $insurrance['form'] == 2 ? 'selected' : '' ?> value="2"><?= lang('tnh_bg') ?></option>
                        </select>
                    </div>
                </div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('code', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($insurrance) ? $insurrance['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('name', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($insurrance) ? $insurrance['name'] : '')), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_amount_of_money', 'money') ?>
                        <?php echo form_input('money', (isset($_POST['money']) ? $_POST['money'] : (!empty($insurrance) ? formatMoney($insurrance['money']) : '')), 'placeholder="'.lang('name').'" id="money" class="form-control input-tip money-format"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_rate_company', 'rate_company') ?>
                        <?php echo form_input('rate_company', (isset($_POST['rate_company']) ? $_POST['rate_company'] : (!empty($insurrance) ? $insurrance['rate_company'] : '')), 'placeholder="'.lang('tnh_rate_company').'" id="rate_company" class="form-control input-tip money-format" required'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_rate_worker', 'rate_worker') ?>
                        <?php echo form_input('rate_worker', (isset($_POST['rate_worker']) ? $_POST['rate_worker'] : (!empty($insurrance) ? $insurrance['rate_worker'] : '')), 'placeholder="'.lang('tnh_rate_company').'" id="rate_worker" class="form-control input-tip money-format" required'); ?>
                    </div>
                </div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : (!empty($insurrance) ? $insurrance['note'] : '')), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
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

        $('#ht').select2();

       	appValidateForm($('#handling-insurrance'), {
           ht: 'required',
           code: 'required',
           name: 'required',
           rate_company: 'required',
           rate_worker: 'required',
        }, handlinginsurrance);

        function handlinginsurrance(form) {
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