<?php echo form_open('admin/releases/edit_address_delivery/'.$id, array('id'=>'add-order')); ?>
<div class="modal-dialog modal-delivery">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('Sửa địa chỉ giao hàng'); ?></h4>
		</div>
		<div class="modal-body">
            <div class="form-group">
                <?= lang('Địa chỉ giao hàng', 'address_delivery') ?>
                <input type="text" name="address_delivery" id="address_delivery" class="form-control address_delivery" value="<?= $shipping_client['address'] ?>" required="required">
            </div>
		</div>
		<div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control save" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add-delivery" data-type="1"><?= _l('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

<script>
    $(function(){
       	appValidateForm($('#add-order'), {
            'address_delivery': 'required',
        }, convert);

        function convert(form) {
        	$('.add-delivery').attr('disabled', 'disabled');
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
                if (data.result == 1) {
                    alert_float('success', data.message);
                    $('.tnh_address_delivery').html($('#address_delivery').val());
                    if (typeof oTable != 'undefined' && oTable != '') {
                        oTable.draw(false);
                    }
                    $('.modal-delivery .close').trigger('click');
                } else {
                    $('.add-delivery').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
            	alert_float('danger', 'error');
                $('.add-delivery').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    })
</script>