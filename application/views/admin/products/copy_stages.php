<?php echo form_open("admin/products/copy_stages/$id", array('id'=>'form-copy-stages')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_copy_stages') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('products') ?>: <br><b><?= $products['name'] ?><br>(<?= $products['code'] ?>)</b>
                </div>
            </div>
            <div class="row mtop10">
                <div class="col-md-12">
                    <?= lang('Sản phẩm muốn sao chép công đoạn', 'stages') ?>
                    <input type="text" name="stages" id="stages" data-placeholder="<?= lang('stages') ?>" class="modal-select2" style="width: 100%;" value="">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control save" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary copy-stages"><?= lang('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
    	init_datepicker();
        ajaxSelectParamsCallback('#stages', 'admin/products/searchDesignStages', 0, {type_products: '<?= $products['type_products'] ?>'});

    	appValidateForm($('#form-copy-stages'), {
            'stages': 'required'
        }, copystages);
	    function copystages(form) {
	    	$('.copy-stages').attr('disabled', 'disabled');
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
	        		$('.copy-stages').removeAttr('disabled', 'disabled');
	        	}
	        })
	        .fail(function() {
	        	alert_float('danger', 'error');
	            $('.copy-stages').removeAttr('disabled', 'disabled');
	        });
	        return false;
	    }
    });
</script>