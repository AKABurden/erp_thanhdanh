<?php echo form_open('admin/manufactures/exportRevoke/'.$id, array('id'=>'export-revoke')); ?>
<div class="modal-dialog modal-warehousing modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_export_revoke') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date_revoke') ?>
                        <?= form_input('date_revoke', set_value('date_revoke') ? set_value('date_revoke') : date('d/m/Y H:i:s'), 'id="date_revoke" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_export_name', 'export_name_revoke') ?>
                        <?php echo form_input('export_name_revoke', (isset($_POST['export_name_revoke']) ? $_POST['export_name_revoke'] : ''), 'placeholder="'.lang('tnh_export_name').'" id="export_name_revoke" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('purchase_internal', 'purchase_internal_revoke') ?>
                        <input type="text" name="purchase_internal_revoke" id="purchase_internal_revoke" data-placeholder="<?= lang('purchase_internal') ?>" style="width: 100%;" class="purchase_internal_revoke modal-select2" value="" required="required">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note_revoke') ?>
                        <textarea name="note_revoke" id="note_revoke" class="form-control" rows="3"></textarea>
                    </div>
                </div>
				
    			<div class="col-md-12 mtop10">
    				<div class="table-responsive">
    					<table id="tb-items-revoke" class="tnh-table table-bordered table-hover dataTable" style="width: 100%;">
    						<thead>
    							<tr>
    								<th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
    								<th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
    								<th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
									<th style="width: 120px;"><?= lang('tnh_location_warehouse') ?></th>
    								<!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_quantity_stock') ?></th> -->
    								<th style="width: 100px;" class="text-center"><?= lang('tnh_quantity_export') ?></th>
    								<!-- <th style="width: 100px;"><?= lang('note') ?></th> -->
    								<!-- <th style="width: 50px;" class="text-center"><?= lang('actions') ?></th> -->
    							</tr>
    						</thead>
    						<tbody>
    						</tbody>
    					</table>
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
		$('#warehouses_revoke').select2();
        ajaxSelectParams('#purchase_internal_revoke', 'admin/manufactures/getPurchaseInternalExport', 0);

        $('#purchase_internal_revoke').change(function(event) {
            purchase_internal_revoke = $(this).val();
            $('#tb-items-revoke tbody').html('');
            if (purchase_internal_revoke) {
                $.ajax({
                    type: "GET",
                    url: site.base_url+'admin/manufactures/getPurchaseInternalItemsExport',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        purchase_internal_revoke: purchase_internal_revoke
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response) {
                            $('#tb-items-revoke tbody').html(response.html);
                        }
                    }
                });
            }
        });

		appValidateForm($('#export-revoke'), {
			date_revoke: 'required',
			export_name_revoke: 'required',
			purchase_internal_revoke: 'required'
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
	        		$('.modal-warehousing .close').trigger('click');
	        		if (typeof oTable != "undefined") {
	        			oTable.draw('page');
	        		}

	        		if (typeof trItem != "undefined")
	        		{
	        			trItem.find('.txt-quantity-finished').html(tnhFormatNumber(data.quantity_warehoused));
	        			dtItems.row(trIndex).data()[6] = data.quantity_warehoused;
	        			dtItems.draw('false');
	        		}
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