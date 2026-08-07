<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/stock/edit_purchase_internal/'.$id, array('id'=>'add-purchase-internal')); ?>
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
										<?= lang('tnh_reference_purchase_internal', 'reference_no') ?>
									</td>
									<td style="width: 35%;">
										<div class="form-group">
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $purchaseInternal['reference_no'] ?>" readonly="" aria-invalid="false">
										</div>
									</td>
									<td style="width: 15%;"><?= lang('date', 'date') ?></td>
									<td style="width: 35%;">
										<?= form_input('date', set_value('date') ? set_value('date') : _d($purchaseInternal['date']), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_enter_name', 'enter_name') ?></td>
									<td colspan="3">
										<div class="form-group">
											<?php echo form_input('enter_name', (isset($_POST['enter_name']) ? $_POST['enter_name'] : $purchaseInternal['enter_name']), 'placeholder="'.lang('tnh_enter_name').'" id="enter_name" required class="form-control input-tip"'); ?>
										</div>
									</td>
                                    <td><?= lang('tnh_warehouses', 'warehouses') ?></td>
                                    <td colspan="">
                                        <div class="form-group">
                                            <select name="warehouses" data-placeholder="<?= lang('tnh_warehouses') ?>" id="warehouses" required="required" style="width: 100%;">
                                                <option value=""></option>
												<?php foreach ($warehouses as $key => $value): ?>
                                                    <option <?= ($value['id'] == $purchaseInternal['warehouse_id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
												<?php endforeach ?>
                                            </select>
                                        </div>
                                    </td>
								</tr>
                                <tr>
                                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                                    <td colspan="3">
                                        <?php
                                        $branchs = getListBranch();
                                        ?>
                                        <select name="branch_id" id="branch_id" class="branch_id" required="required" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if (!empty($branchs)) { ?>
                                                <?php foreach ($branchs as $key => $value) { ?>
                                                    <option <?= $purchaseInternal['branch_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
								<tr>
									<td class="hide">
										<input type="hidden" name="productions_orders_detail_id" id="productions_orders_detail_id" class="form-control" value="<?= $purchaseInternal['pod_id'] ?>">
										<?= lang('tnh_reference_productions_orders_details', 'productions_orders_detail_id') ?>
									</td>
									<td colspan=""  class="hide">
										<?= $pod['reference_no'] ?>
									</td>

								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="3">
										<textarea name="note" id="note" class="form-control" rows="3"><?= $purchaseInternal['note'] ?></textarea>
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
							<table id="tb-items" class="dt-tnh table table-bordered table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th class="text-center" style="width: 5%;">
											<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
		                                </th>
		                                <th style="width: 15%;"><span class="red">*</span><?= lang('tnh_material_code') ?></th>
		                                <th style="width: 15%;"><?= lang('tnh_material_name') ?></th>
		                                <th style="width: 5%;"><?= lang('tnh_unit') ?></th>
		                                <th style="width: 15%;"><span class="red">*</span><?= lang('tnh_location_warehouse') ?></th>
		                                <th style="width: 10%;"><?= lang('quantity') ?></th>
		                                <th style="width: 10%;"><?= lang('price') ?></th>
		                                <th style="width: 10%;"><?= lang('tnh_subtotal') ?></th>
		                                <th style="width: 10%;"><?= lang('note') ?></th>
		                                <th style="width: 5%;"><?= lang('actions') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $counter = 0; ?>
									<?php foreach ($purchaseInternalItems as $key => $value): ?>
										<tr data-id="<?= $value['id'] ?>">
											<td class="text-center">
												<div class="stt text-center"><?= ++$key ?></div>
											</td>
											<td>
												<input type="hidden" name="counter[]" id="input" class="form-control" value="<?= $counter ?>">
												<input type="hidden" name="items_id[]" id="items_'<?= $counter ?>'" class="items_id" style="width: 100%;" value="<?= $value['type_item'].'__'.$value['item_id'] ?>">
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
													<select name="locations[]" data-placeholder="<?= lang('choose') ?>" id="locations" class="locations" style="width: 180px;">
														<option></option>
														<?= $locations ?>
													</select>
												</div>
											</td>
											<td>
												<div class="td-quantity"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" style="width: 100%;" class="form-control quantity" value="<?= formatNumber($value['quantity']) ?>"></div>
											</td>
											<td>
												<div class="td-price"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price[]" id="price[]" class="form-control price" value="<?= formatNumber($value['price']) ?>"></div>
											</td>
											<td>
												<div class="td-amount text-right"><?= formatMoney($value['amount']) ?></div>
											</td>
											<td>
												<div class="text-center td-note">
													<textarea name="note_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"><?= $value['note_item'] ?></textarea>
												</div>
											</td>
											<td>
												<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>
											</td>
										</tr>
										<script type="text/javascript">
											$(document).ready(function() {
												$('tr[data-id="<?= $value['id'] ?>"]').find('#locations').val(<?= $value['location_id'] ?>).trigger('change');
											});
										</script>
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
										<th class="th-total-quantity text-center"><?= formatNumber($purchaseInternal['total_quantity']) ?></th>
										<th></th>
										<th class="th-grand-total text-right"><?= formatMoney($purchaseInternal['grand_total']) ?></th>
										<th></th>
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
	var lang_pi = <?= json_encode(['tnh_please_chosen_warehouse' => lang('tnh_please_chosen_warehouse')]) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var paramsData = {};
    paramsData[token] = hash;
	var edit = 1;
	var counter = 0;
	var count_errors = 0;
	var arr_productions_plan_id = [];
	var locations = false;
</script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('purchase_internal.js?vs=1.1') ?>"></script>