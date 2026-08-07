<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/stock/add_exporting_production/'.$id_pod, array('id'=>'add-exporting')); ?>
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
									<td style="width: 35%;">
										<div class="form-group">
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
										</div>
									</td>
									<td style="width: 15%;"><?= lang('date', 'date') ?></td>
									<td style="width: 35%;">
										<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_export_name', 'export_name') ?></td>
									<td colspan="3">
										<div class="form-group">
											<?php echo form_input('export_name', (isset($_POST['export_name']) ? $_POST['export_name'] : ''), 'placeholder="'.lang('tnh_export_name').'" id="export_name" class="form-control input-tip"'); ?>
										</div>
									</td>
								</tr>
								<tr class="hide">
									<td>
										<?= lang('tnh_reference_productions_orders_details', 'productions_orders_detail_id') ?>
									</td>
									<td colspan="3">
										<input type="text" name="productions_orders_detail_id" id="productions_orders_detail_id" class="productions_orders_detail_id" data-placeholder="<?= lang('choose') ?>" style="width: 100%;" value="" title="">
									</td>
									<!-- <td><?= lang('tnh_warehouses', 'warehouses') ?></td>
									<td colspan="">
										<div class="form-group">
											<select name="warehouses" data-placeholder="<?= lang('tnh_warehouses') ?>" id="warehouses" required="required" style="width: 100%;">
												<option value=""></option>
												<?php foreach ($warehouses as $key => $value): ?>
													<option <?= ($key == 0) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</td> -->
								</tr>
								<tr>
									<td><?= lang('tnh_reference_productions_orders', 'po_id') ?></td>
									<td colspan="1">
										<input type="text" name="po_id" id="po_id" class="po_id" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" style="width: 100%;" value="" title="">
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
                                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="3">
										<textarea name="note" id="note" class="form-control" rows="3"></textarea>
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
							<?php if (!empty($id_pod)): ?>
								<div class="text-right">
									<div class="btn btn-danger mbot5" onClick="loadData()"><i class=""></i> <?= lang('load_item') ?></div>
								</div>
							<?php endif ?>
							<div class="table-responsive">
								<table id="tb-items" class="dt-tnh table table-hover" style="width: 1400px; min-width: 100%;">
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
											<th class="th-total-quantity-exchange text-center"></th>
											<th></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="" class="form-control" value="1">
				<?php if($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion): ?>
					<div class="checkbox checkbox-info" style="position: absolute; right: 120px;">
						<input type="checkbox" checked="true" name="save_and_warehouse" class="save_and_warehouse" id="save_and_warehouse" value="1">
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
	var edit = 0;
	var counter = 0;
	var count_errors = 0;
	var arr_productions_plan_id = [];
	var id_pod = <?= $id_pod ?>;
	var dt;
</script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('exporting_production.js?vs=2.4') ?>"></script>