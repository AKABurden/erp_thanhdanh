<?php echo form_open('admin/manufactures/addWarehousing/'.$id, array('id'=>'add-warehousing')); ?>
<div class="modal-dialog modal-warehousing modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_warehousing') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_warehouses', 'warehouses') ?>
						<select name="warehouses" data-placeholder="<?= lang('tnh_warehouses') ?>" class="modal-select2" id="warehouses" required="required" style="width: 100%;">
							<option value=""></option>
							<?php foreach ($warehouses as $key => $value): ?>
								<option <?= ($key == 0) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('tnh_task', 'task_work') ?>
						<select name="task_work" data-placeholder="<?= lang('tnh_task') ?>" class="modal-select2" id="task_work" required="required" style="width: 100%;">
							<option value=""></option>
							<?php foreach ($tasks as $key => $value): ?>
								<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<!-- <div class="col-md-8">
    				<?= lang('tnh_items', 'tnh_items') ?>
    				<input type="text" name="" id="items" class="" value="" style="width: 100%;">
    			</div>
    			<div class="col-md-4">
    				<button type="button" style="margin-top: 25px;" class="btn btn-primary ev-all"><?= lang('tnh_check_all') ?></button>
    				<button type="button" style="margin-top: 25px;" onclick="refershTable()" class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
    			</div> -->
    			<div class="col-md-12 mtop10">
    				<div class="table-responsive">
    					<table id="tb-items" class="tnh-table table-bordered table-hover dataTable" style="width: 100%;">
    						<thead>
    							<tr>
    								<th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
    								<th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
    								<th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
									<th style="width: 120px;"><?= lang('tnh_location_warehouse') ?></th>
									<th style="width: 80px;" class="text-center"><?= lang('tnh_quantity_task') ?></th>
									<th style="width: 80px;" class="text-center"><?= lang('tnh_quantity_warehoused') ?></th>
    								<!-- <th style="width: 80px;" class="text-center"><?= lang('tnh_quantity_finished') ?></th> -->
    								<!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_quantity_warehoused') ?></th> -->
    								<th style="width: 100px;" class="text-center"><?= lang('quantity') ?></th>
    								<th style="width: 100px;"><?= lang('note') ?></th>
    								<!-- <th style="width: 50px;"><?= lang('actions') ?></th> -->
    							</tr>
    						</thead>
    						<tbody>
    							<tr>
    								<td class="text-center"><?= 1 ?></td>
    								<td><?= $item['items_code'] ?></td>
    								<td><?= $item['items_name'] ?></td>
    								<td>
    									<select name="location_warehouse" id="location_warehouse" data-placeholder="<?= lang('tnh_location_warehouse') ?>" style="width: 100%;" class="modal-select2 location_warehouse" required="required">
    										<option value=""></option>
    									</select>
									</td>
									<td class="text-center td-quantity-task">0</td>
    								<td class="text-center td-quantity-warehoused">0</td>
    								<!-- <td class="text-center td-quantity-finished"><?= $item['quantity_finished'] ?></td>
    								<td class="text-center td-quantity-warehoused"><?= $item['quantity_warehoused'] ?></td> -->
    								<?php
    									// $quantity_enter = $item['quantity_finished'] - $item['quantity_warehoused'];
    									// if ($quantity_enter < 0) $quantity_enter = 0;
    								?>
    								<td>
    									<input type="text" name="quantity" id="quantity" class="form-control number-format" value="0" required="required">
    									<div class="text-danger show-errors">
										</div>
										<?php
											$htmlExchange = '';
											$exchange = $this->site_model->getExchangeProducts($item['items_id']);
											if (!empty($exchange)) {
												foreach ($exchange as $k => $val) {
													$htmlExchange.= '<div class="list-exchange">
														<input type="hidden" class="form-control number-exchange" value="'.$val['number_exchange'].'">
														<span>'.$val['unit_name'].': </span>
														<span class="text-number-exchange">0</span>
													</div>';
												}
											} 
										?>
										<div class="show-exchange text-primary mtop5"><?= $htmlExchange ?></div>
    								</td>
    								<td>
    									<textarea name="note_item" id="note_item" class="form-control note_item" rows="3"></textarea>
    								</td>
    							</tr>
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

	function getTotalPurchaseProducts()
	{
		tb = '#tb-items tbody tr:not("[class^=not-tr]")';
		var n = $(tb).length;
		var stt = 0;

		for (ii = 0; ii < n; ii++)
		{
			stt++;
			element = $(tb)[ii];
			// $(element).find('.td-number').html(stt);
			// quantity = intVal($(element).find('.td-quantity').html());
			quantity = intVal($(element).find('#quantity').val());

			//
			showExchange = $(element).find('.list-exchange');
			nShowExchange = showExchange.length;
			for (jj = 0; jj < nShowExchange; jj++) {
				elementShowExchange = $(showExchange)[jj];
				numberExchange = intVal($(elementShowExchange).find('.number-exchange').val());
				totalQuantityExchange = quantity/numberExchange;
				$(elementShowExchange).find('.text-number-exchange').html(tnhFormatNumber(totalQuantityExchange));
			}
		}
	}

	$(document).ready(function() {
		$('#warehouses').select2();
		$('#location_warehouse').select2();
		$('#task_work').select2();

		

		function getLocationWarehouses()
		{
			warehouse_id = $('#warehouses').val();
			$('select.location_warehouse').val(0).trigger('change');
			$('.location_warehouse .select2-chosen').html('<?= lang('choose') ?>');
			$('select.location_warehouse').html('');
			if (warehouse_id) {
				$.ajax({
					url: site.base_url+'admin/manufactures/getLocationWarehouses',
					type: 'GET',
					dataType: 'json',
					data: {
						warehouse_id: warehouse_id
					},
				})
				.done(function(data) {
					if (data) {
						$('select.location_warehouse').html(data.locations);
						$('select.location_warehouse').val(0).trigger('change');
					}
				})
				.fail(function() {
					console.log("error");
				});
			}
		}

		getLocationWarehouses();

		$('#warehouses').change(function(event) {
			event.preventDefault();
			getLocationWarehouses();
		});

		$('#quantity').change(function(event) {
			quantity = $(this).val();
			quantity_finished = intVal($('.td-quantity-finished').html());
			quantity_warehoused = intVal($('.td-quantity-warehoused').html());
			quantity_less = quantity_finished - quantity_warehoused;
			getTotalPurchaseProducts();
			// if (quantity > quantity_less) {
			// 	$('.show-errors').html('<?= lang('tnh_quantity_less') ?> '+quantity_less);
			// 	$('.add').attr('disabled', 'disabled');
			// } else {
			// 	$('.show-errors').html('');
			// 	$('.add').removeAttr('disabled', 'disabled');
			// }
		});

		$('#task_work').change(function(event){
			task_work_id = $(this).val();
			if (task_work_id > 0) {
				$.ajax({
					type: "GET",
					url: site.base_url+'admin/manufactures/getTaskById',
					data: {
						task_work_id: task_work_id,
						pod_id: '<?= $id ?>'
					},
					dataType: "json",
					success: function (response) {
						if (response) {
							$('.td-quantity-task').html(tnhFormatNumber(response.quantityShiftWork));
							$('.td-quantity-warehoused').html(tnhFormatNumber(response.quantityWarehousing));
							$('#quantity').val(tnhFormatNumber(response.quantityRest));
							getTotalPurchaseProducts();
						}
					}
				});
			}
		});

		appValidateForm($('#add-warehousing'), {
			warehouses: 'required',
			location_warehouse: 'required',
			quantity: 'required',
			task_work: 'required'
	    }, db);

	    function db(form) {
	    	console.log($('#location_warehouse').val());
	    	// if (!$('#location_warehouse').val()) {
	    	// 	bootbox.alert('<?= lang('tnh_please_chonse_location_warehouse') ?>');
	    	// 	return;
	    	// }
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