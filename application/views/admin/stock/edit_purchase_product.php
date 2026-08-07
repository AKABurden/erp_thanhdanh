<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/stock/edit_purchase_product/'.$id, array('id' => 'purchase_product')); ?>
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
						<table class="tnh-tb table-bordered table-hover">
							<tbody>
								<tr>
									<td style="width: 15%;">
										<?= lang('tnh_reference_purchase_products', 'reference_no') ?>
									</td>
									<td style="width: 35%;">
										<div class="form-group">
												<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $purchaseProduct['reference_no'] ?>" readonly="" aria-invalid="false">
											</div>
										</div>
									</td>
									<td style="width: 15%;">
										<?= lang('date', 'date') ?>
									</td>
									<td style="width: 35%;">
										<?= form_input('date', set_value('date') ? set_value('date') : _d($purchaseProduct['date'], true), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_warehouses', 'warehouses') ?></td>
									<td colspan="1">
										<select name="warehouses" id="warehouses" class="warehouses" data-placeholder="<?= lang('tnh_warehouses') ?>" style="width: 100%;">
											<option value=""></option>
											<?php foreach ($warehouses as $key => $value): ?>
												<option <?= $value['id'] == $purchaseProduct['warehouse_id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
											<?php endforeach ?>
										</select>
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
                                                    <option <?= $purchaseProduct['branch_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
								</tr>
								<tr>
									<td><?= lang('tnh_reference_productions_orders', 'po_id') ?></td>
									<td>
										<input type="text" name="po_id" id="po_id" class="po_id" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" style="width: 100%;" value="<?= !empty($purchaseProduct['po_id']) ? $purchaseProduct['po_id'] : '
										' ?>" title="">
									</td>
									<td colspan="2">
										<div class="checkbox checkbox-danger">
											<input type="checkbox" <?= $purchaseProduct['is_pass'] == 1 ? 'checked' : '' ?> id="is_pass" name="is_pass" value="1">
											<label for="is_pass"><?= lang('Vượt') ?></label>
										</div>
									</td>
								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="3">
										<textarea name="note" id="note" class="form-control note" rows="3"><?= $purchaseProduct['note'] ?></textarea>
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
				<div class="tabset">
				  	<!-- Tab 1 -->
				  	<input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
				  	<label for="tab1"><?= lang('tnh_items') ?></label>

				  	<div class="tab-panels">
				    	<section id="marzen" class="tab-panel">
				    		<div class="tb-height">
				    			<div class="table-responsive">
				    				<table id="tb-purchases" class="dt-tnh table table-hover" style="width: 100%;">
				    					<thead>
				    						<tr>
				    							<th class="text-center" style="width: 30px;">
				    								<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
				    							</th>
				    							<th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
				    							<th style="width: 50px"><?= lang('tnh_images') ?></th>
				    							<th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
				    							<th style="width: 50px;"><?= lang('tnh_conversion_unit') ?></th>
				    							<th style="width: 100px;"><span class="red">*</span><?= lang('tnh_location_warehouse') ?></th>
				    							<th style="width: 100px;"><?= lang('quantity') ?></th>
				    							<th style="width: 100px;"><?= lang('note') ?></th>
				    							<th style="width: 50px;"><?= lang('actions') ?></th>
				    						</tr>
				    					</thead>
				    					<tbody>
				    						<?= $bodyItems ?>
				    					</tbody>
				    				</table>
				    			</div>
				    		</div>
				  		</section>
				  </div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="edit" id="" class="form-control" value="1">
				<?php if($this->perApproveWarehousePurchaseProducts): ?>
					<div class="checkbox checkbox-info" style="position: absolute; right: 120px;">
						<input type="checkbox" checked="true" name="save_and_warehouse" <?= ($purchaseProduct['save_and_warehouse'] == 1 ? 'checked' : '') ?> class="save_and_warehouse" id="save_and_warehouse" value="1">
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
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
	var dt = '';
	var lang_purchase = <?= json_encode(['tnh_please_chosen_warehouse' => lang('tnh_please_chosen_warehouse')]) ?>;
	var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 1;
	var counter = <?= $counter ?>;
	var count_errors = 0;
	var delivery_id = 0;
	var locations = '<?= $locations ?>';
</script>

<script type="text/javascript" src="<?= js('purchase_products.js?vs=1.6') ?>"></script>