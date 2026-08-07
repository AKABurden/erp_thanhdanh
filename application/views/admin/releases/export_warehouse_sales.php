<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/releases/export_warehouse_sales/'.$id, array('id' => 'export-warehouse')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        	<?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
	<div class="content ae-content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('info') ?></h3>
					</div>
					<div class="panel-body">
						<div class="lead-view" id="leadViewWrapper">
							<div class="wap-content firt">
								<span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
								<span class="bold font-medium-xs lead-name"><?= _dt($delivery['date']) ?></span>
							</div>
							<div class="wap-content second">
								<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_deliveries') ?>: </span>
								<span class="bold font-medium-xs lead-name"><?= ($delivery['reference_no']) ?></span>
							</div>
							<div class="wap-content firt">
								<span class="text-muted lead-field-heading no-mtop bold"><?= lang('customers') ?>: </span>
								<span class="bold font-medium-xs lead-name"><?= $delivery['customer_name'] ?></span>
							</div>
							<div class="wap-content firt">
								<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_orders') ?>: </span>
								<span class="bold font-medium-xs lead-name"><?= !empty($referenceOrder) ? $referenceOrder['reference_order'] : '' ?></span>
							</div>
							<div class="wap-content second">
								<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_address_delivery') ?>: </span>
								<span class="bold font-medium-xs lead-name"><?= !empty($address_delivery) ? $address_delivery['address'] : '' ?></span>
							</div>
							<div class="wap-content firt">
								<span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
								<span class="bold font-medium-xs lead-name"><?= $delivery['note'] ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" ><!-- style="border-left: 1px solid #c1c1c1;" -->
				<div class="tabset">
					<input type="radio" name="tabset1" id="tab2" aria-controls="marzen1" checked>
				  	<label for="tab2"><?= lang('tnh_export_warehouses') ?></label>

				  	<input type="radio" name="tabset1" id="tab3" aria-controls="marzen2">
				  	<label for="tab3">
				  		<?= lang('tnh_item_surpasses_the_quantity_delivered') ?>
				  		(<span class="surpasses_delivery">0</span>)
				  	</label>
				  	<div class="tab-panels">
				    	<section id="marzen1" class="tab-panel">
				    		<div class="row mbot10">
				    			<div class="col-md-8">
				    				<?= lang('tnh_items', 'tnh_items') ?>
				    				<select name="items" id="items" data-placeholder="<?= lang('tnh_items') ?>" style="width: 100%;">
				    					<option value=""></option>
				    					<?php foreach ($items as $key => $value): ?>
				    						<option data-delivery-item-id="<?= $value['delivery_item_id'] ?>" data-reference-order="<?= $value['reference_order'] ?>" data-type="<?= $value['type_item'] ?>" data-code="<?= $value['item_code'] ?>" data-name="<?= $value['item_name'] ?>" value="<?= $value['type_item'].'__'.$value['item_id'] ?>"><?= $value['item_name'] ?>(<?= $value['item_code'] ?>)(<?= $value['reference_order'] ?>)</option>
				    					<?php endforeach ?>
				    				</select>
				    			</div>
				    			<div class="col-md-4">
				    				<button type="button" style="margin-top: 20px;" class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
				    			</div>
				    		</div>
				    		<div class="table-responsive">
			    				<table id="tb-items" class="tnh-table table-bordered table-hover" style="width: 100%;">
			    					<thead>
				    					<tr>
			    							<th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
			    							<th style="width: 100px;"><?= lang('tnh_orders') ?></th>
			    							<th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
			    							<th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
			    							<th style="width: 100px;"><span class="red">*</span><?= lang('tnh_warehouses') ?></th>
			    							<th style="width: 100px;"><span class="red">*</span><?= lang('tnh_location_warehouse') ?></th>
			    							<th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_warehouses') ?></th>
			    							<th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
			    							<th class="" style="width: 100px;"><?= lang('note') ?></th>
			    							<th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
			    						</tr>
			    					</thead>
			    					<tbody>
			    						<?php $counter = 0; ?>
			    						<?php foreach ($items as $key => $value): ?>
			    						<tr>
			    							<input type="hidden" name="counter[]" id="counter[]" class="form-control" value="<?= $counter ?>">
			    							<input type="hidden" name="item_id[<?= $counter ?>]" id="item_id[]" class="form-control item_id" value="<?= $value['type_item'].'__'.$value['item_id'] ?>">
			    							<input type="hidden" name="delivery_item_id[<?= $counter ?>]" id="delivery_item_id[]" class="form-control delivery_item_id" value="<?= $value['delivery_item_id'] ?>">

			    							<td class="text-center td-numbers"><?= ++$key ?></td>
			    							<td><?= $value['reference_order'] ?></td>
			    							<td><?= $value['item_code'] ?></td>
				    						<td><?= $value['item_name'] ?></td>
			    							<td>
			    								<select name="warehouses[<?= $counter ?>]" style="width: 100%;" data-placeholder="<?= lang('tnh_warehouses') ?>" id="warehouses" class="warehouses">
			    									<option value=""></option>
			    									<?php foreach ($warehouses as $k => $val): ?>
			    										<option value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
			    									<?php endforeach ?>
			    								</select>
			    							</td>
			    							<td class="td-location-warehouse">
			    								<select name="locations[<?= $counter ?>]" data-placeholder="<?= lang('choose') ?>" id="locations" class="locations" style="width: 100%;">
			    									<option value=""></option>
			    								</select>
			    							</td>
			    							<td class="td-quantity-warehouse text-center">
			    							</td>
			    							<td>
			    								<input type="number" name="quantity[<?= $counter ?>]" id="input" class="form-control quantity" value="<?= $value['quantity'] ?>">
			    							</td>
			    							<td>
			    								<textarea name="note_item[<?= $counter ?>]" id="note_item[]" class="form-control" rows="3"></textarea>
			    							</td>
			    							<td class="text-center"><a href="javascript:void(0)" onclick="removeItem(this)" ><i class="fa fa-remove"></i></a></td>
			    						</tr>
			    						<?php $counter++; ?>
			    						<?php endforeach ?>
			    					</tbody>
			    				</table>
			    			</div>
				    	</section>
				    	<section id="marzen2" class="tab-panel">
				    		<div class="table-responsive">
				    			<table id="tb-list-export" class="tnh-table table-bordered table-hover" style="width: 100%;">
				    				<thead>
				    					<tr>
				    						<th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
				    						<th style="width: 100px;"><?= lang('tnh_orders') ?></th>
				    						<th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
				    						<th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
				    						<th class="text-center" style="width: 90px;"><?= lang('quantity') ?></th>
				    						<th style="width: 50px;" class="text-center"><?= lang('tnh_check') ?></th>
				    					</tr>
				    				</thead>
				    				<tbody>
				    					<?php foreach ($items as $key => $value): ?>
				    						<tr data-item-id="<?= $value['type_item'].'__'.$value['item_id'] ?>" data-delivery-item-id="<?= $value['delivery_item_id'] ?>">
				    							<td class="text-center"><?= ++$key ?></td>
				    							<td><?= $value['reference_order'] ?></td>
				    							<td><?= $value['item_code'] ?></td>
				    							<td><?= $value['item_name'] ?></td>
				    							<td class="text-center td-quantity-export"><?= formatNumber($value['quantity']) ?></td>
				    							<td class="td-condition text-center">
				    								<span class="label label-danger"><?= lang('tnh_cd') ?></span>
				    							</td>
				    						</tr>
				    					<?php endforeach ?>
				    				</tbody>
				    			</table>
				    		</div>
				    	</section>
				    </div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="add" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add">
					<?php echo _l( 'submit'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
	var dtList = '';
	var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var counter = <?= $counter ?>;
	var count_errors = 0;
	var warehouses_errors = 0;
	var locations_errors = 0;
	var warehouses = <?= !empty($warehouses) ? json_encode($warehouses) : '{}' ?>;
	var arr_id = [];
    var arr_info = [];
</script>
<script type="text/javascript">
	function totalItems()
	{
		tb = '#tb-items tbody tr:not("[class^=not-tr]")';
	    var n = $(tb).length;
	    var stt = 0;
	    count_errors = 0;
	    warehouses_errors = 0;
	    locations_errors = 0;
	    arr_id = [];
    	arr_info = [];
	    for (ii = 0; ii < n; ii++)
	    {
	        stt++;
	        element = $(tb)[ii];
	        $(element).find('.td-numbers').html(stt);
	        item_current_id = $(element).find('.item_id').val();
	        quantity = intVal($(element).find('.quantity').val());
	        delivery_current_item_id = $(element).find('.delivery_item_id').val();

	        // index = jQuery.inArray(item_current_id, arr_id);
	        // if (index !== -1)
	        // {
	        //     arr_info[index].quantity = parseFloat(arr_info[index].quantity) + parseFloat(quantity);
	        // } else {
	        //     arr_id.push(item_current_id);
	        //     object = {"quantity": quantity};
	        //     arr_info.push(object);
	        // }

	        index = jQuery.inArray(delivery_current_item_id, arr_id);
	        if (index !== -1)
	        {
	            arr_info[index].quantity = parseFloat(arr_info[index].quantity) + parseFloat(quantity);
	        } else {
	            arr_id.push(delivery_current_item_id);
	            object = {"quantity": quantity};
	            arr_info.push(object);
	        }


	        //check exist wareouse and location
	        warehouse = $(element).find('select.warehouses').val();
	        if (!warehouse) {
	        	warehouses_errors++;
	        }

	        lc = $(element).find('select.locations').val();
	        if (!lc) {
	        	locations_errors++;
	        }
	        //end
	    }


	    //check done
	    nItems = $('#tb-list-export tbody tr');
	    for (k = 0; k < nItems.length; k++)
	    {
	    	el = $(nItems)[k];
	    	// item_current_id = $(el).attr('data-item-id');
	    	delivery_current_item_id = $(el).attr('data-delivery-item-id');
	    	quantity_delivery = intVal($(el).find('.td-quantity-export').html());
	    	index = jQuery.inArray(delivery_current_item_id, arr_id);
	    	if (index == -1) {
	    		$(el).find('.td-condition').html('<span class="label label-danger"><?= lang('tnh_cd') ?></span>');
	    		count_errors++;
	    	} else {
	    		quantity_current = arr_info[index]['quantity'];
	    		if (quantity_current == quantity_delivery) {
	    			$(el).find('.td-condition').html('<span class="label label-success"><?= lang('tnh_d') ?></span>');
	    			$(el).find('.td-condition').closest('tr').hide();
	    		} else {
	    			$(el).find('.td-condition').html('<span class="label label-danger"><?= lang('tnh_cd') ?></span>');
	    			$(el).find('.td-condition').closest('tr').show();
	    			count_errors++;
	    		}
	    	}
	    }

	    $('.surpasses_delivery').html(count_errors);
	    if (count_errors > 0) {
	    	$('.surpasses_delivery').closest('label').addClass('text-danger');
	    } else {
	    	$('.surpasses_delivery').closest('label').removeClass('text-danger');
	    }
	}

	function removeItem(el)
	{
		$(el).closest('tr').remove();
		totalItems();
	}

	function getWarehouses() {
	    var option = '<option value=""></option>';
	    $.each(warehouses, function(index, el) {
	        option+= '<option value="'+el.id+'">'+el.name+'</option>';
	    });
	    return option;
	}

	function getLocations(locations) {
		var option = '<option value=""></option>';
	    $.each(locations, function(index, el) {
	        option+= '<option data-quantity="'+el.product_quantity+'" value="'+el.localtion+'">'+el.location_name+'</option>';
	    });
	    return option;
	}

	$(document).on('change', 'select.warehouses', function(event) {
		event.preventDefault();
		tr = $(this).closest('tr');
		item_id = tr.find('.item_id').val();
		warehouse_id = $(this).val();
		//ajax
        $.ajax({
            url: site.base_url+'admin/releases/rowItemLocationWarehouse',
            type: 'POST',
            dataType: 'json',
            data: {
            	csrf_token_name: hash,
            	item_id: item_id,
            	warehouse_id: warehouse_id,
            }
        })
        .done(function(data) {
            if (data) {
                tr.find('select.locations').html(getLocations(data.warehouses));
            }
        })
        .fail(function() {
            console.log("error");
        })
	});

	$(document).on('change', 'select.locations', function(event) {
		event.preventDefault();
		tr = $(this).closest('tr');
		elm = $(this).select2().find(":selected");
		quantity_warehouse = elm.data("quantity");
		tr.find('.td-quantity-warehouse').html(tnhFormatNumber(quantity_warehouse));
	});

	$(document).on('change', '#items', function(event) {
		event.preventDefault();
		elm = $(this).select2().find(":selected");
		item_id = elm.val();
		type_item = elm.data('type');
		item_code = elm.data('code');
		item_name = elm.data('name');
		delivery_item_id = elm.data('delivery-item-id');
		reference_order = elm.data('reference-order');
		$(this).val('');
		if (item_id) {
			trHtml = '<tr>'+
				'<input type="hidden" name="counter[]" id="counter[]" class="form-control" value="'+counter+'">'+
				'<input type="hidden" name="item_id['+counter+']" id="item_id[]" class="form-control item_id" value="'+item_id+'">'+
				'<input type="hidden" name="delivery_item_id['+counter+']" id="delivery_item_id[]" class="form-control delivery_item_id" value="'+delivery_item_id+'">'+
				'<td class="text-center td-numbers"></td>'+
				'<td>'+reference_order+'</td>'+
				'<td>'+item_code+'</td>'+
				'<td>'+item_name+'</td>'+
				'<td>'+
					'<select name="warehouses['+counter+']" style="width: 100%;" data-placeholder="<?= lang('tnh_warehouses') ?>" id="warehouses" class="warehouses">'+
						'<option value=""></option>'+
						getWarehouses()+
					'</select>'+
				'</td>'+
				'<td class="td-location-warehouse">'+
					'<select name="locations['+counter+']" data-placeholder="<?= lang('choose') ?>" id="locations" class="locations" style="width: 100%;">'+
						'<option value=""></option>'+
					'</select>'+
				'</td>'+
				'<td class="td-quantity-warehouse text-center">'+
				'</td>'+
				'<td>'+
					'<input type="number" name="quantity['+counter+']" id="input" class="form-control quantity" value="0">'+
				'</td>'+
				'<td>'+
					'<textarea name="note_item['+counter+']" id="note_item[]" class="form-control" rows="3"></textarea>'+
				'</td>'+
				'<td class="text-center"><a href="javascript:void(0)" onclick="removeItem(this)" ><i class="fa fa-remove"></i></a></td>'+
			'</tr>';

			$('#tb-items tbody').append(trHtml);
			totalItems();
			$('select.warehouses').select2();
			$('select.locations').select2();
			counter++;
		}
	});

	$(document).on('change', '.quantity', function(event) {
		event.preventDefault();
		totalItems();
	});

	$(document).on('click', '.ev-referesh', function(event) {
		event.preventDefault();
		bootbox.confirm({
	        message: lang_core['tnh_you_are_referesh'],
	        buttons: {
	            confirm: {
	                label: lang_core['yes'],
	                className: 'btn-success'
	            },
	            cancel: {
	                label: lang_core['no'],
	                className: 'btn-danger'
	            }
	        },
	        callback: function (result) {
	            if (result) {
	                // dt.rows().remove().draw();
	                $('#tb-items tbody').html('');
	                totalItems();
	            }
	        }
	    });
	});

	$(document).ready(function() {
		$('#items').select2();
		$('select.warehouses').select2();
		$('select.locations').select2();

		dtList = $('#tb-list-exports').DataTable({
			"language": lang_datatables,
			// 'searching': false,
			'ordering': false,
			// 'paging': false,
	        // "info": false,
	        // 'fixedHeader': true,
	        // scrollY: true,
			// scrollY: '150px',
			// scrollX: true,
	        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
	        },
		});

		totalItems();
	});

	appValidateForm($('#export-warehouse'), {
		add: 'required'
    }, db);

    function db(form) {
    	totalItems();
    	if (count_errors > 0) {
    		alert_float('danger', '<?= lang('tnh_dsmh_cd') ?>');
    		return;
    	}
    	if (warehouses_errors > 0)
    	{
    		alert_float('danger', '<?= lang('tnh_please_not_empty_warehouses') ?>');
    		return;
    	}
    	if (locations_errors > 0)
    	{
    		alert_float('danger', '<?= lang('tnh_please_not_empty_location_warehouses') ?>');
    		return;
    	}

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
        		window.location.href = site.base_url+'admin/releases/deliveries';
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
</script>
