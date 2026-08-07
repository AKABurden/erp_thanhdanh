<?php echo form_open("admin/products/copy_bom/$id", array('id'=>'form-copy-bom')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_copy_bom') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('products') ?>: <br><b><?= $products['name'] ?><br>(<?= $products['code'] ?>)</b>
                </div>
            </div>
            <div class="row mtop10">
                <div class="col-md-12">
                    <?= lang('Sản phẩm muốn sao chép BOM', 'bom') ?>
                    <input type="text" name="bom" id="bom" data-placeholder="<?= lang('BOM') ?>" class="modal-select2" style="width: 100%;" value="">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control save" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary copy-bom"><?= lang('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
    	init_datepicker();
        ajaxSelectParamsCallback('#bom', 'admin/products/searchBOM', 0, {type_products: '<?= $products['type_products'] ?>'});

    	appValidateForm($('#form-copy-bom'), {
            'bom': 'required'
        }, copyBOM);
	    function copyBOM(form) {
	    	$('.copy-bom').attr('disabled', 'disabled');
	        var url = form.action;
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
	        			oTable.draw(false);
	        		}
	        		$('.modal-dialog .close').trigger('click');
	        	} else {
	        		alert_float('danger', data.message);
	        		$('.copy-bom').removeAttr('disabled', 'disabled');
	        	}
	        })
	        .fail(function() {
	        	alert_float('danger', 'error');
	            $('.copy-bom').removeAttr('disabled', 'disabled');
	        });
	        return false;
	    }
    });
</script>