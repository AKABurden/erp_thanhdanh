<?php echo form_open('admin/clients/addShipping/'.$id, array('id'=>'add-shipping')); ?>
<div class="modal-dialog modal2">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_add_shipping') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('cong_name_shipping_client', 'name_shipping') ?>
						<?php echo form_input('name_shipping', (isset($_POST['name_shipping']) ? $_POST['name_shipping'] : ''), 'placeholder="'.lang('cong_name_shipping_client').'" id="name_shipping" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('cong_address_shipping_client', 'address_shipping') ?>
						<?php echo form_input('address_shipping', (isset($_POST['address_shipping']) ? $_POST['address_shipping'] : ''), 'placeholder="'.lang('cong_address_shipping_client').'" id="address_shipping" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('cong_phone_shipping_client', 'phone_shipping') ?>
						<?php echo form_input('phone_shipping', (isset($_POST['phone_shipping']) ? $_POST['phone_shipping'] : ''), 'placeholder="'.lang('cong_phone_shipping_client').'" id="phone_shipping" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('cong_client_city', 'city') ?>
						<?php echo form_input('city', (isset($_POST['city']) ? $_POST['city'] : ''), 'placeholder="'.lang('cong_client_city').'" id="city" required style="width: 100%;" class="input-tip modal-select2"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('cong_client_district', 'district') ?>
						<?php echo form_input('district', (isset($_POST['district']) ? $_POST['district'] : ''), 'placeholder="'.lang('cong_client_district').'" id="district" required style="width: 100%;" class="input-tip modal-select2"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<div class="checkbox checkbox-primary">
							<input type="checkbox" id="address_primary" class="address_primary modal-select2" name="address_primary" value="1">
							<label for="address_primary" data-toggle="tooltip"><?=_l('cong_address_primary')?></label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
				<button type="submit" class="btn btn-primary add-shipping"><?= lang('save') ?></button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		ajaxSelectParamsCallback('#city', 'admin/clients/searchProvince', 0, false, true);

		$('#city').change(function(event) {
			province_id = $(this).val();
			ajaxSelectParamsCallback('#district', 'admin/clients/searchDistrictByProvince', 0, {province_id: province_id}, true);
			$('#district').val('');
		});
		appValidateForm($('#add-shipping'), {
			name_shipping: 'required',
			address_shipping: 'required',
			phone_shipping: 'required',
			city: 'required',
			district: 'required',
        }, addShipping);

        function addShipping(form)
        {
        	$('.add-shipping').attr('disabled', 'disabled');
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
            		$('.modal2 .close').trigger('click');
            	} else {
            		alert_float('danger', data.message);
            		$('.add-shipping').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
            	alert_float('danger', 'error');
                $('.add-shipping').removeAttr('disabled', 'disabled');
            });
            return false;
        }
	});
</script>