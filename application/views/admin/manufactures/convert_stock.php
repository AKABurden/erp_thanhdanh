<?php echo form_open('admin/manufactures/convert_stock/' . $id, array('id' => 'add-purchase')); ?>
<div class="modal-dialog modal-lg" style="width: 90%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_convert_to_export_stock') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="cs-table" class="tnh-table table-hover table-condensed table-bordered dataTable">
							<thead>
								<tr>
									<th class="text-center" style="width: 50px;">
										#
									</th>
									<th style="width: 150px;"><span class="red">*</span><?= lang('tnh_material_code') ?></th>
									<th style="width: 150px;"><?= lang('tnh_material_name') ?></th>
									<th style="width: 50px;" class="text-center"><?= lang('tnh_unit') ?></th>
									<th style="width: 180px;"><span class="red">*</span><?= lang('tnh_location_warehouse') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_warehouses') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_stock') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_export') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_value_exchange') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_exchange') ?></th>
									<th class="text-center" style="width: 50px;"><?= lang('actions') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php $counter = 0; ?>
								<?php foreach ($suggest_exporting_items as $key => $value) : ?>
									<?php
									$warehouses = $this->stock_model->getWarehouseItems($value['item_id'], $value['type_item']);
									foreach ($warehouses as $k => $val) {
										$warehouses[$k]['location_name'] = $val['name_warehouse'] . '->' . recursiveLocations($val['localtion']);
									}
									?>
									<tr value="<?= $value['id'] ?>">
										<input type="hidden" name="counter[]" id="input" class="form-control" value="<?= $counter ?>">
										<input type="hidden" name="suggest_exporting_items_id[<?= $counter ?>]" id="input" class="form-control suggest_exporting_items_id" value="<?= $value['id'] ?>">
										<td class="text-center td-number"><?= ++$key ?></td>
										<td>
											<?= $value['item_code'] ?>
											<?php if ($value['type_item'] == 'semi_products_outside') : ?>
												<div style="margin-bottom: 5px;"></div>
												<div class="label label-danger" style="margin-top: 5px;"><?= lang('semi_products_outside') ?></div>
											<?php endif ?>
											<?php if ($value['type_item'] == 'semi_products') : ?>
												<div style="margin-bottom: 5px;"></div>
												<div class="label label-warning" style="margin-top: 5px;"><?= lang('semi_products') ?></div>
											<?php endif ?>
										</td>
										<td><?= $value['item_name'] ?></td>
										<td class="text-center"><?= $value['unit_name'] ?></td>
										<td>
											<select name="locations[<?= $counter ?>]" onchange="changeLocationsNew(this)" data-placeholder="<?= lang('choose') ?>" id="locations[<?= $counter ?>]" class="locations modal-select2" required style="width: 100%;">
												<option></option>
												<?php $quantityW = 0; ?>
												<?php if (!empty($warehouses)) : ?>
													<?php foreach ($warehouses as $k => $val) : ?>
														<?php
														$select = '';
														if ($val['warehouse_id'] == 8 && empty($select)) {
															$select = "selected";
															$quantityW = $val['product_quantity'];
														}
														?>
														<option <?= $select ?> data-quantity="<?= $val['product_quantity'] ?>" value="<?= $val['warehouse_id'] . '__' . $val['localtion'] ?>"><?= $val['location_name'] ?></option>
													<?php endforeach ?>
												<?php endif ?>
											</select>
										</td>
										<td class="text-center quantity-warehouse"><?= formatNumber($quantityW) ?></td>
										<td class="text-center quantity-had-export"><?= formatNumber($value['quantity_convert_stock']) ?></td>
										<td class="text-center">
											<?php
											$quantity_export = $value['quantity_export'] - $value['quantity_convert_stock'];
											?>
											<input type="hidden" name="quantity_had_export[<?= $counter ?>]" id="quantity_had_export" class="form-control quantity_had_export" value="<?= formatNumber($quantity_export) ?>">
											<input type="text" onchange="changeQuantityExport(this)" name="quantity_export[<?= $counter ?>]" id="quantity_export" class="form-control quantity_export number-format" value="<?= formatNumber($quantity_export) ?>">
											<div class="show-errors text-danger">
											</div>
										</td>
										<td class="text-center number-exchange"><?= formatNumber($value['number_exchange']) ?></td>
										<td class="quantity-exchange">
											<div class="input-group">
												<input type="text" onchange="changeQuantityExchange(this)" name="quantity_exchange[<?= $counter ?>]" id="quantity_exchange" class="form-control number-format quantity_exchange" value="<?= formatNumber($quantity_export / $value['number_exchange']) ?>">
												<span class="input-group-addon"><?= $value['unit_name_parent'] ?></span>
											</div>
										</td>
										<td class="text-center">
											<a href="javascript:void(0)" onclick="removeRow(this)"><i class="fa fa-remove"></i></a>
										</td>
									</tr>
									<?php $counter++; ?>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<?php if ($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion) : ?>
				<div class="col-md-12">
					<div class="checkbox checkbox-info">
						<input type="checkbox" name="save_and_warehouse" checked="true" class="save_and_warehouse" id="save_and_warehouse" value="1">
						<label for="save_and_warehouse" class="text-danger"><?= lang('tnh_save_and_warehouse') ?></label>
					</div>
				</div>
			<?php endif; ?>
			<input type="hidden" name="save" id="save" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	var validation = {
		'warehouses': 'required'
	};
	var counter = <?= $counter ? $counter : 0 ?>;
	var count_errors = 0;

	function getLocations(locations) {
		var option = '<option value=""></option>';
		$.each(locations, function(index, el) {
			option += '<option value="' + el.location_id + '">' + el.location_name + '</option>';
		});
		return option;
	}

	function totalConvertStock() {
		tb = '#cs-table tbody tr:not("[class^=not-tr]")';
		var n = $(tb).length;
		var stt = 0;
		count_errors = 0;
		for (ii = 0; ii < n; ii++) {
			stt++;
			element = $(tb)[ii];
			$(element).find('.td-number').html(stt);

			quantity_export = intVal($(element).find('.quantity_export').val());
			number_exchange = intVal($(element).find('.number-exchange').html());
			// quantity_exchange = quantity_export/number_exchange;
			// $(element).find('.quantity-exchange').html(tnhFormatNumber(quantity_exchange));
			quantity_had_export = intVal($(element).find('.quantity_had_export').val());
			if (quantity_export > quantity_had_export) {
				$(element).find('.show-errors').html('<?= lang('tnh_slnb') ?>' + tnhFormatNumber(quantity_had_export));
				count_errors++;
			} else {
				$(element).find('.show-errors').html('');
			}
		}
	}

	function changeLocationsNew(_this) {
		curTr = $(_this).closest('tr');
		dtW = $(_this).select2().find(':selected').data("quantity");
		quantityW = 0;
		if (dtW) {
			quantityW = dtW;
		}
		curTr.find('.quantity-warehouse').html(tnhFormatNumber(quantityW));
	}

	function changeQuantityExport(_this) {
		_tr = $(_this).closest('tr');
		quantity_export = intVal(_tr.find('.quantity_export').val());
		number_exchange = intVal(_tr.find('.number-exchange').html());
		quantity_exchange = quantity_export / number_exchange;
		_tr.find('.quantity_exchange').val(tnhFormatNumber(quantity_exchange));
		totalConvertStock();
	}

	function changeQuantityExchange(_this) {
		_tr = $(_this).closest('tr');
		_tr = $(_this).closest('tr');
		quantity_exchange = intVal(_tr.find('.quantity_exchange').val());
		number_exchange = intVal(_tr.find('.number-exchange').html());
		quantity_export = quantity_exchange * number_exchange;
		_tr.find('.quantity_export').val(tnhFormatNumber(quantity_export));
		totalConvertStock();
	}

	function removeRow(_this) {
		$(_this).closest('tr').remove();
		totalConvertStock();
	}

	function changeLocations(_this) {
		curTr = $(_this).closest('tr');
		curLocationId = $(_this).val();
		curWarehouseId = $('#warehouses').val();
		curSugExportingItemId = curTr.find('.suggest_exporting_items_id').val();
		if (curWarehouseId > 0 && curLocationId > 0 && curSugExportingItemId > 0) {
			$.ajax({
					url: '<?= site_url('admin/manufactures/getQuantityLocationConvertStock') ?>',
					type: 'POST',
					dataType: 'json',
					data: {
						"<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
						curLocationId: curLocationId,
						curWarehouseId: curWarehouseId,
						curSugExportingItemId: curSugExportingItemId,
					},
				})
				.done(function(data) {
					curTr.find('.quantity-warehouse').html('');
					if (data) {
						curTr.find('.quantity-warehouse').html(tnhFormatNumber(data.warehouse.product_quantity));
					}
				})
				.fail(function() {
					console.log("error");
				});
		}
	}

	$('.quantity_export').change(function(event) {
		// totalConvertStock();
	});

	$(document).ready(function() {
		$('#warehouses').select2();
		$('select.locations').select2();

		if (counter >= 0) {
			for (i = 0; i <= counter; i++) {
				validation['locations[' + i + ']'] = 'required';
			}
		}

		$('#warehouses').change(function(event) {
			warehouse_id = $(this).val();
			$('select.locations').val(null).trigger('change');
			$('select.locations').html('');
			if (warehouse_id) {
				$.ajax({
						url: site.base_url + 'admin/manufactures/getLocationsForItems',
						type: 'POST',
						dataType: 'json',
						data: {
							"<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
							warehouse_id: warehouse_id,
							id: <?= $id ?>

						},
					})
					.done(function(data) {
						if (data) {
							$.each(data.locations, function(index, el) {
								$('#cs-table').find('tr[value="' + index + '"] select.locations').html(getLocations(el));
							});
						}
					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
						console.log("complete");
					});
			}
		});


		appValidateForm($('#add-purchase'), validation, convert);

		function convert(form) {
			if (count_errors > 0) {
				return;
			}
			$('.add').attr('disabled', 'disabled');
			var url = form.action;
			// var data = $(form).serialize();
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
			//
			$.ajax({
					url: url,
					type: 'POST',
					dataType: 'JSON',
					cache: false,
					contentType: false,
					processData: false,
					data: formData,
				})
				.done(function(data) {
					if (data.result) {
						alert_float('success', data.message);
						if (typeof oTable != 'undefined' && oTable != '') {
							oTable.draw();
						}
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