<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/returned_goods/edit/'.$id, array('id' => 'returned-goods')); ?>
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
										<?= lang('tnh_reference_no_returned_goods', 'reference_no') ?>
									</td>
									<td style="width: 35%;">
										<div class="form-group">
											<div class="input-group">
												<span title="<?= lang('tnh_referesh') ?>" data-toggle="tooltip" class="input-group-addon btn btn-danger referesh-reference">
													<i class="fa fa-undo"></i>
												</span>
												<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $returned_goods['reference_no'] ?>" readonly="" aria-invalid="false">
											</div>
										</div>
									</td>
									<td style="width: 15%;">
										<?= lang('date', 'date') ?>
									</td>
									<td style="width: 35%;">
										<?= form_input('date', _dt($returned_goods['date']), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('customers', 'customers') ?></td>
									<td>
										<div class="form-group">
											<input type="text" name="customers" data-placeholder="<?= lang('customers') ?>" id="customers" class="customers" required style="width: 100%;" value="<?= 'customers__'.$returned_goods['customer_id'] ?>">
										</div>
									</td>
									<td><?= lang('tnh_reference_orders') ?></td>
									<td>
										<div class="form-group">
											<input type="text" name="order_id" data-placeholder="<?= lang('tnh_reference_orders') ?>" id="order_id" class="order_id" style="width: 100%;" value="<?= $returned_goods['order_id'] ?>">
										</div>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_employees', 'employees') ?></td>
									<td>
										<select name="employees" id="employees" data-placeholder="<?= lang('tnh_employees') ?>" required="required" style="width: 100%;">
											<option value=""></option>
											<?php foreach ($employees as $key => $value): ?>
												<option <?= $returned_goods['employee_id'] == $value['staffid'] ? 'selected' : '' ?> value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
											<?php endforeach ?>
										</select>
									</td>
									<td><?= lang('tnh_handling_solution', 'handling_solution') ?></td>
									<td>
										<select name="handling_solution" id="handling_solution" data-placeholder="<?= lang('tnh_handling_solution') ?>" required="required" style="width: 100%;">
											<option value=""></option>
											<?php foreach (typeHandlingSolution() as $key => $value): ?>
												<option <?= $returned_goods['handling_solution'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
											<?php endforeach ?>
										</select>
									</td>
								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="4">
										<textarea name="note" id="note" placeholder="<?= lang('note') ?>" class="form-control note" rows="3"><?= $returned_goods['note'] ?></textarea>
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
				    				<table id="tb-returned-goods" class="dt-tnh table tnh-table table-bordered table-hover dataTable" style="min-width: 1400px; width: 100%;">
				    					<thead>
											<tr>
				    							<th class="text-center" style="width: 30px;">
				    								<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
				    							</th>
				    							<th class="text-center" style="width: 120px;"><?= lang('tnh_product_code') ?></th>
				    							<th class="text-center" style="width: 50px;"><?= lang('image') ?></th>
				    							<th class="text-center" style="width: 100px;"><?= lang('tnh_product_name') ?></th>
				    							<th class="text-center" style="width: 50px;"><?= lang('tnh_unit') ?></th>
				    							<th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
				    							<th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_loss') ?></th>
				    							<th class="text-center" style="width: 100px;"><?= lang('tnh_sample_quantity') ?></th>
				    							<th class="text-center" style="width: 100px;"><?= lang('tnh_unit_price') ?></th>
				    							<th class="text-center" style="width: 100px;"><?= lang('tnh_total_amount') ?></th>
												<!-- <th class="text-center" style="width: 100px;"><?= ''//lang('tnh_discount_percent') ?></th> -->
				    							<!-- <th class="text-center" style="width: 100px;"><?= ''//lang('tnh_discount_direct') ?></th> -->
				    							<!-- <th class="text-center" style="width: 100px;"><?= ''//lang('tnh_grand_total') ?></th> -->
				    							<th class="text-center" style="width: 120px;"><?= lang('note') ?></th>
				    							<th class="text-center" style="width: 30px;"><?= lang('actions') ?></th>
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
				<div class="total-table hide">
					<table class="tnh-table table">
						<tr class="danger bold">
							<td style="width: 10%;"><?= lang('tnh_total_quantity') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-quantity text-center"><?= formatNumber($returned_goods['total_quantity']) ?></td>
							<td style="width: 10%;"><?= lang('tnh_total_amount') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-amount text-right"><?= formatMoney($returned_goods['total_amount_items']) ?></td>
							<td style="width: 10%;"><?= lang('tnh_discount') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-discount text-right"><?= formatMoney($returned_goods['total_discount_percent_items'] + $returned_goods['total_discount_direct_items']) ?></td>
							<td style="width: 10%;"><?= lang('tnh_grand_total') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="grand-total text-right"><?= formatMoney($returned_goods['grand_total_items']) ?></td>
						</tr>
					</table>
				</div>
				<table class="table tnh-tb table-bordered table-hover">
					<tbody>
						<tr>
							<td style="width: 15%;"><?= lang('tax', 'tax') ?></td>
							<td style="width: 75%;" colspan="3">
								<select name="tax_id" id="tax_id" class="tax_id" data-placeholder="<?= lang('tax') ?>" style="width: 100%;" >
									<option value="0"><?= lang('0%') ?></option>
									<?php foreach ($taxs as $key => $value): ?>
										<option <?= $value['id'] == $returned_goods['tax_id'] ? 'selected' : '' ?> data-rate="<?= $value['taxrate'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
									<?php endforeach ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?= lang('tnh_discount_percent', 'discount_percent') ?></td>
							<td>
								<input type="number" name="discount_percent" id="discount_percent" class="form-control" value="<?= $returned_goods['discount_percent'] ?>" >
							</td>
							<td><?= lang('tnh_discount_direct', 'discount_direct') ?></td>
							<td>
								<input type="text" name="discount_direct" id="discount_direct" class="form-control money-format" value="<?= formatMoney($returned_goods['total_discount_direct']) ?>">
							</td>
						</tr>
						<tr class="success" style="font-weight: 700;">
							<td><?= lang('tnh_grand_total', 'grand_total') ?></td>
							<td colspan="3" class="td-grand-total-all text-right"><?= formatMoney($returned_goods['grand_total']) ?></td>
						</tr>
						<tr class="danger">
							<td><?= lang('tnh_current_debt', 'current_debt') ?></td>
							<td colspan="3" class="td-current-debt text-right">
								0
							</td>
						</tr>
					</tbody>
				</table>
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
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
	var lang_rg = <?= json_encode(['tnh_please_chosen_customer' => lang('tnh_please_chosen_customer')]); ?>;
	var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 1;
	var counter = <?= !empty($counter) ? $counter : 0 ?>;
	var count_errors = 0;
	var return_goods_id = <?= $id ?>;
</script>

<script type="text/javascript" src="<?= js('returned_goods.js?vs=1.9') ?>"></script>