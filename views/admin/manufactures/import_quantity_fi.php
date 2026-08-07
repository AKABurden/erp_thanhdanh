<?php echo form_open('admin/manufactures/importQuantityFi/'.$id, array('id'=>'add-quantity')); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_import_quantity_fi') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<span><b><?= lang('tnh_quantity_order') ?></b></span>: <?= formatNumber($podItem['quantity']) ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<span><b><?= lang('tnh_quantity_fi_pre') ?></b></span>: <?= formatNumber($production_detail['quantity_finished']) ?>
					</div>
				</div>
				<?php
					$quantityHad = $podItem['quantity'] - $production_detail['quantity_finished'];
					if ($quantityHad < 0) $quantityHad = 0;
				?>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('quantity', 'quantity_import_finished') ?>
						<input type="text" name="quantity_import_finished" id="quantity_import_finished" class="form-control format-number" value="<?= formatNumber($quantityHad) ?>" required="required">
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="save" id="inputSave" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		appValidateForm($('#add-quantity'), {
			quantity_import_finished: 'required',
	    }, db);

	    function db(form) {
	    	$('.add').attr('disabled', 'disabled');
	        for (var i = 0; i < tinymce.editors.length; i++) {
	            tinymce.editors[i].save();
	        }
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
	        		$('.modal-dialog .close').trigger('click');
	        		if (typeof oTable != "undefined") {
	        			oTable.draw('page');
	        		}
	        		$('.td-quantity-finished').html(tnhFormatNumber(data.quantity_fi));
	        	} else {
	        		alert_float('danger', data.message);
	        		$('.add').removeAttr('disabled', 'disabled');
	        	}
	        })
	        .fail(function() {
	            alert_float('danger', lang_core['errors']);
	        	$('.add').removeAttr('disabled', 'disabled');
	        });
	        return false;
	    }
	});
</script>