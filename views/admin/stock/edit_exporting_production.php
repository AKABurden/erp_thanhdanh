<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/stock/edit_exporting_production/'.$id, array('id'=>'add-exporting')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        	<?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('info') ?></h3>
					</div>
					<div class="panel-body">
						<table class="tnh-tb table-bordered table-hover">
							<tbody>
								<tr>
									<td style="width: 15%;">
										<?= lang('tnh_reference_stock', 'reference_no') ?>
									</td>
									<td>
										<div class="form-group">
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $suggest_exporting['reference_stock'] ?>" readonly="" aria-invalid="false">
										</div>
									</td>
									<td><?= lang('date', 'date') ?></td>
									<td>
										<?= form_input('date', set_value('date') ? set_value('date') : _dt($suggest_exporting['date']), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_export_name', 'export_name') ?></td>
									<td colspan="3">
										<div class="form-group">
											<?php echo form_input('export_name', (isset($_POST['export_name']) ? $_POST['export_name'] : $suggest_exporting['export_name']), 'placeholder="'.lang('tnh_export_name').'" id="export_name" required class="form-control input-tip"'); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<?= lang('tnh_reference_productions_orders_details', 'productions_orders_detail_id') ?>
									</td>
									<td colspan="1">
										<?= $production_orders_detail['reference_no'] ?>
										<div class="hide">
											<input type="text" name="productions_orders_detail_id" id="productions_orders_detail_id" class="productions_orders_detail_id" data-placeholder="<?= lang('choose') ?>" style="width: 100%;" value="<?= $suggest_exporting['productions_orders_details_id'] ?>" title="">
										</div>
									</td>
                                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                                    <td colspan="1">
                                        <?php
                                        $branchs = getListBranch();
                                        ?>
                                        <select name="branch_id" id="branch_id" class="branch_id" required="required" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if (!empty($branchs)) { ?>
                                                <?php foreach ($branchs as $key => $value) { ?>
                                                    <option <?= $suggest_exporting['branch_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
									<!-- <td><?= lang('tnh_warehouses', 'warehouses') ?></td>
									<td colspan="">
										<div class="form-group">
											<select name="warehouses" data-placeholder="<?= lang('tnh_warehouses') ?>" id="warehouses" required="required" style="width: 100%;">
												<option value=""></option>
												<?php foreach ($warehouses as $key => $value): ?>
													<option <?= ($value['id'] == $suggest_exporting['warehouse_id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</td> -->
								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="3">
										<textarea name="note" id="note" class="form-control" rows="3"><?= $suggest_exporting['note'] ?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('tnh_info_items') ?></h3>
					</div>
					<div class="panel-body">
						<div class="tb-height">
							<table id="tb-items" class="dt-tnh table table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th class="text-center" style="width: 5%;">
											<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
		                                </th>
		                                <th style="width: 15%;"><span class="red">*</span><?= lang('tnh_material_code') ?></th>
		                                <th style="width: 15%;"><?= lang('tnh_material_name') ?></th>
		                                <th style="width: 10%;"><?= lang('tnh_unit') ?></th>
		                                <th style="width: 15%;"><span class="red">*</span><?= lang('tnh_location_warehouse') ?></th>
		                                <th style="width: 10%;"><?= lang('tnh_quantity_export') ?></th>
		                                <th style="width: 15%;"><?= lang('tnh_value_exchange') ?></th>
		                                <th style="width: 10%;"><?= lang('tnh_quantity_exchange') ?></th>
		                                <th style="width: 5%;"><?= lang('actions') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $counter = 0; ?>
									<?php foreach ($suggest_exporting_items as $key => $value): ?>
										<?php
											$warehouses = $this->stock_model->getWarehouseItemsByItemIdAndTypeAndWarehouse($value['item_id'], $value['type_item'], $value['warehouse_item_id']);
											$dtWarehouse = $this->site_model->rowWarehouseById($value['warehouse_item_id']);
											foreach ($warehouses as $k => $val) {
												$warehouses[$k]['location_name'] = recursiveLocations($val['localtion']);
											}
										?>
										<tr>
											<td class="text-center">
												<div class="stt text-center"><?= ++$key ?></div>
											</td>
											<td>
												<input type="hidden" name="suggest_exporting_items_id[]" id="suggest_exporting_items_id" class="form-control suggest_exporting_items_id" value="<?= $value['id'] ?>">
												<input type="hidden" name="counter[]" id="input" class="form-control" value="<?= $counter ?>">
												<input type="hidden" name="unit_id_edit[]" id="unit_id" class="form-control unit_id" value="<?= $value['unit_id'] ?>">
												<input type="hidden" name="unit_parent_id_edit[]" id="unit_parent_id" class="form-control unit_parent_id" value="<?= $value['unit_parent_id'] ?>">
												<input type="hidden" name="number_exchange_edit[]" id="number_exchange" class="form-control number_exchange" value="<?= $value['number_exchange'] ?>">
												<?= $value['item_code'] ?>
											</td>
											<td>
												<div class="td-item-name"><?= $value['item_name'] ?></div>
											</td>
											<td>
												<div class="td-unit"><?= $value['unit_name'] ?></div>
											</td>
											<td>
												<div class="td-location">
													<select name="locations_edit[]" data-placeholder="<?= lang('choose') ?>" id="locations" class="locations" style="width: 180px;">
														<option></option>
														<?php foreach ($warehouses as $k => $val): ?>
															<option <?= ($val['warehouse_id'].'__'.$val['localtion'] == $value['warehouse_item_id'].'__'.$value['location_id']) ? 'selected' : '' ?> value="<?= $val['warehouse_id'].'__'.$val['localtion'] ?>"><?= $dtWarehouse['name'] ?>-><?= $val['location_name'] ?></option>
														<?php endforeach ?>
													</select>
												</div>
											</td>
											<td>
												<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity_edit[]" id="quantity[]" class="form-control quantity" value="<?= formatNumber($value['quantity_export']) ?>"></div>
											</td>
											<td>
												<div class="text-center td-value-exchange"><?= $value['number_exchange'] ?></div>
											</td>
											<td>
												<div class="text-center td-quantity-exchange"><?= formatNumber($value['quantity_exchange']) ?></div>
											</td>
											<td>
												<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>
											</td>
										</tr>
										<?php $counter++; ?>
									<?php endforeach ?>
								</tbody>
								<tfoot>
									<tr>
										<th class="text-center"><a class="btn btn-info btn-icon add-row-foot"><i class="fa fa-plus"></i></a></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th class="th-total-quantity text-center"><?= formatNumber($suggest_exporting['total_quantity']) ?></th>
										<th></th>
										<th class="th-total-quantity-exchange text-center"><?= formatNumber($suggest_exporting['total_quantity_exchange']) ?></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="edit" id="" class="form-control" value="1">
				<?php if($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion): ?>
					<div class="checkbox checkbox-info" style="position: absolute; right: 120px;">
						<input type="checkbox" name="save_and_warehouse" <?= ($suggest_exporting['save_and_warehouse'] == 1 ? 'checked' : '') ?> class="save_and_warehouse" id="save_and_warehouse" value="1">
						<label for="save_and_warehouse" class="text-danger"><?= lang('tnh_save_and_warehouse') ?></label>
					</div>
				<?php endif; ?>
				<button type="submit" class="btn btn-info only-save customer-form-submiter add">
					<?php echo _l( 'submit'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script type="text/javascript">
	var lang_ex = <?= json_encode(['tnh_please_chosen_pod' => lang('tnh_please_chosen_pod')]) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var paramsData = {};
    paramsData[token] = hash;
	var edit = 1;
	var counter = <?= !empty($counter) ? $counter : 0 ?>;
	var count_errors = 0;
	var arr_productions_plan_id = [];
</script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('exporting_production.js?vs=1.9') ?>"></script>