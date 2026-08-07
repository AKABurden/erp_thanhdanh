<?php echo form_open('admin/manufactures/purchaseInternal/'.$id, array('id'=>'add-purchase-internal')); ?>
<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_add_purchase_internal') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_reference_productions_orders_details', 'productions_orders_detail_id') ?>
						<div><?= $pod['reference_no'] ?></div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('date', 'date') ?>
						<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_enter_name', 'enter_name') ?>
						<?php echo form_input('enter_name', (isset($_POST['enter_name']) ? $_POST['enter_name'] : ''), 'placeholder="'.lang('tnh_enter_name').'" id="enter_name" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-4">
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
				<div class="col-md-8">
					<div class="form-group">
						<td><?= lang('note', 'note') ?></td>
						<textarea name="note" id="note" class="form-control" rows="3"></textarea>
					</div>
				</div>
    			<div class="col-md-12 mtop10">
    				<div class="table-responsive">
    					<table id="tb-pi" class="table dt-tnh table-hover table-condensed tnh-table" style="width: 100%;">
    						<thead>
    							<tr>
    								<th class="text-center" style="width: 5%;">
    									<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
    								</th>
    								<th style="width: 150px;"><span class="red">*</span><?= lang('tnh_material_code') ?></th>
    								<th style="width: 150px;"><?= lang('tnh_material_name') ?></th>
    								<th style="width: 50px;"><?= lang('tnh_unit') ?></th>
    								<th style="width: 100px;"><span class="red">*</span><?= lang('tnh_location_warehouse') ?></th>
    								<th style="width: 100px;"><?= lang('quantity') ?></th>
    								<!-- <th style="width: 100px;"><?= lang('price') ?></th> -->
    								<!-- <th style="width: 100px;"><?= lang('tnh_total') ?></th> -->
    								<th style="width: 150px;"><?= lang('note') ?></th>
    								<th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
    							</tr>
    						</thead>
    						<tbody>
    						</tbody>
    						<tfoot>
    							<tr>
    								<th class="text-center"><a class="btn btn-info btn-icon add-row-foot"><i class="fa fa-plus"></i></a></th>
    								<th></th>
    								<th></th>
    								<th></th>
    								<th></th>
    								<th class="th-total-quantity text-center"></th>
    								<th></th>
    								<!-- <th class="th-total-money text-center"></th> -->
    								<!-- <th></th> -->
    								<th></th>
    							</tr>
    						</tfoot>
    					</table>
    				</div>
    			</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="save" id="" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	var locations = false;
	var counter = 0;
	var dt = '';
	function totalPurchaseInternal()
	{
	    tb = '#tb-pi tbody tr:not("[class^=not-tr]")';
	    var table = $(tb).length;
	    var stt = 0;
	    var total_quantity = 0;
	    var total_quantity_exchange = 0;
	    var grand_total = 0;
	    count_errors = 0;
	    var flag = false;
	    for (ii = 0; ii < table; ii++)
	    {
	        stt++;
	        element = $(tb)[ii];
	        $(element).find('.stt').html(stt);
	        item_id_current = $(element).find('input.items_id').val();
	        suggest_exporting_items_id = $(element).find('.suggest_exporting_items_id').val();
	        if (item_id_current || suggest_exporting_items_id) {
	            quantity = intVal($(element).find('.quantity').val());
	            price = intVal($(element).find('.price-internal').val());
	            totalInternal = quantity * price;
	            $(element).find('.td-total-internal').html(tnhFormatMoney(totalInternal));
	            grand_total+= totalInternal;
	            total_quantity+= quantity;
	            flag = true;
	        }
	    }
	    $('.th-total-quantity').html(tnhFormatNumber(total_quantity));
	    $('.th-total-money').html(tnhFormatMoney(grand_total));
	    if (flag) {
	        $('#warehouses').select2('readonly', true);
	        $('#productions_orders_detail_id').select2('readonly', true);
	    } else {
	        $('#warehouses').select2('readonly', false);
	        $('#productions_orders_detail_id').select2('readonly', false);
	    }
	}

	function getWarehouseLocations()
	{
		warehouse_id = $('#warehouses').val();
		locations = false;
		if (warehouse_id) {
			$.ajax({
				url: site.base_url+'admin/stock/getLocationWarehouses',
				type: 'POST',
				dataType: 'json',
				data: {
					warehouse_id: warehouse_id,
					csrf_token_name: hash,
				},
			})
			.done(function(data) {
				if (data) {
					locations = data.locations;
					console.log(locations);
				}
			})
			.fail(function() {
				console.log("error");
			});
		}
	}

	$(document).ready(function() {
		$('#warehouses').select2();

		//hanlding warehouse locations
	    getWarehouseLocations();
	    $(document).on('change', '#warehouses', function(event) {
	        event.preventDefault();
	        getWarehouseLocations();
	    });
	    //end hanlding warehouse locations

	    dt = $('#tb-pi').DataTable({
			"language": lang_datatables,
			'searching': false,
			'ordering': false,
			'paging': false,
	        "info": false,
	        // 'fixedHeader': true,
	        // scrollY: true,
			// scrollY: '150px',
			// scrollX: true,
	        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
	        },
	        "initComplete": function(settings, json) {
	            var t = this;
	            t.parents('.table-loading').removeClass('table-loading');
	            t.removeClass('dt-table-loading');
	            mainWrapperHeightFix();
	        },
		});

	    $('.add-row').on('click', function(event) {
			event.preventDefault();
	        productions_orders_detail_id = <?= $id ?>;
	        warehouses = $('#warehouses').val();
	        if (!warehouses) {
	            bootbox.alert(lang_pi['tnh_please_chosen_warehouse']);
	            return;
	        }

			tdRef = '<div class="stt text-center"></div>';
			tdItem = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">\
	            <input type="text" name="items_id[]" id="items_'+counter+'" class="items_id modal-select2" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="">';
	        tdImage = '<div class="td-image">'+
	                    '<div class="preview_image" style="width: auto;">'+
	                        '<div class="display-block contract-attachment-wrapper img">'+
	                            '<div style="width:45px;">'+
	                                '<a href="'+site.base_url+'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">'+
	                                    '<div class="">'+
	                                        '<img src="'+site.base_url+'assets/images/tnh/no_image.png" style="border-radius: 50%">'+
	                                    '</div>'+
	                                '</a>'+
	                            '</div>'+
	                        '</div>'+
	                    '</div>'+
	            '</div>';
	        tdItemName = '<div class="td-item-name"></div>';
	        tdUnit = '<div class="td-unit"></div>';
	        tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 180px;"></select></div>';
	        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[]" id="quantity[]" class="form-control quantity quantity-internal quantity-format" style="width: 100px;" value="1"></div>';
	        tdPrice = '<div class="td-price-internal text-price"><input type="text" name="price_internal[]" id="price_internal[]" class="form-control price-internal money-format" style="width: 100px;" value="0"></div>';
	        tdTotal = '<div class="td-total-internal text-right"></div>';
	        tdNote = '<div class="text-center td-note"><textarea name="note_item[]" id="note_item" class="form-control note_item" rows="3"></textarea></div>';
			tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

			rowNode = dt.row.add( [
	            tdRef,
	            tdItem,
	            tdItemName,
	            tdUnit,
	            tdLocation,
	            tdQuantity,
	            tdNote,
	            tdActions
	        ] ).draw( false ).node();
	        ajaxSelectParams($('#items_'+ counter +''), 'admin/stock/searchItemsForPurchaseInternal', 0, {productions_orders_detail_id: productions_orders_detail_id});
	        $('select.locations').select2();
	        $(rowNode).find('select.locations').html(locations);
	        counter++;
	        totalPurchaseInternal();
		});

	    appValidateForm($('#add-purchase-internal'), {
	        date: 'required',
	        enter_name: 'required',
	       	warehouses: 'required',
	    }, db);

	    function db(form) {
	    	$('.add').attr('disabled', 'disabled');
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
	});
</script>