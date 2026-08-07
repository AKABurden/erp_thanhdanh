<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/releases/edit_delivery/'.$id, array('id' => 'orders')); ?>
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
										<?= lang('tnh_reference_deliveries', 'reference_no') ?>
									</td>
									<td style="width: 35%;">
										<div class="form-group">
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $delivery['reference_no'] ?>" readonly="" aria-invalid="false">
										</div>
									</td>
									<td style="width: 15%;">
										<?= lang('date', 'date') ?>
									</td>
									<td style="width: 35%;">
										<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('customers', 'customers') ?></td>
									<td>
										<div class="form-group">
											<input type="hidden" name="customers" data-placeholder="<?= lang('customers') ?>" id="customers" class="customers" required style="width: 100%;" value="<?= 'customers__'.$delivery['customer_id'] ?>">
											<?= $delivery['customer_name'] ?>
										</div>
									</td>
									<td><?= lang('tnh_address_delivery') ?></td>
									<td>
										<div class="input-group">
											<input type="tel" name="address_delivery" id="address_delivery" data-placeholder="<?= lang('tnh_address_delivery') ?>" class="modal-select2" style="width: 100%;" value="<?= $delivery['address_delivery_id'] ?>">
											<span class="input-group-addon">
												<a href="javascript:void(0)" class="add-address-delivery"><i class="fa fa-plus"></i></a>
											</span>
										</div>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_reference_orders', 'reference_orders') ?></td>
									<td>
										<div class="form-group">
											<input type="hidden" name="reference_orders[]" data-placeholder="<?= lang('tnh_reference_orders') ?>" id="reference_orders" class="reference_orders" required style="width: 100%;" value="<?= $delivery['order_id'] ?>">
											<?= $referenceOrder['reference_order'] ?>
										</div>
									</td>
									<td><?= lang('tnh_employees_charge', 'employees') ?></td>
									<td colspan="">
										<select name="employees" id="employees" data-placeholder="<?= lang('tnh_employees_charge') ?>" style="width: 100%;" class="" required="required">
											<option value=""></option>
											<?php foreach ($employees as $key => $value): ?>
												<option <?= $value['staffid'] == $delivery['employee_id'] ? 'selected' : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
											<?php endforeach ?>
										</select>
									</td>
								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="4">
										<textarea name="note" id="note" class="form-control note" rows="3"><?= $delivery['note'] ?></textarea>
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
				    		<div class="row mbot10">
				    			<div class="col-md-8">
				    				<?= lang('tnh_items', 'tnh_items') ?>
				    				<input type="text" name="" id="items" class="" value="" style="width: 100%;">
				    			</div>
				    			<!-- <div class="col-md-4">
				    				<button type="button" style="margin-top: 25px;" class="btn btn-primary ev-all"><?= lang('tnh_check_all') ?></button>
				    				<button type="button" style="margin-top: 25px;" onclick="refershTable()" class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
				    			</div> -->
				    		</div>
				    		<div class="tb-height">
				    			<div class="table-responsive">
				    				<table id="tb-deliveries" class="dt-tnh table table-bordered table-hover" style="width: 100%;">
				    					<thead>
				    						<tr>
				    							<th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
				    							<th style="width: 100px;"><?= lang('tnh_reference_orders') ?></th>
				    							<th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
				    							<th style="width: 50px"><?= lang('tnh_images') ?></th>
				    							<th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
				    							<th style="width: 50px;"><?= lang('tnh_unit') ?></th>
				    							<th style="width: 100px;"><?= lang('quantity') ?></th>
				    							<th style="width: 100px;"><?= lang('quantity_had_delivery') ?></th>
				    							<th style="width: 100px;"><?= lang('quantity_delivery') ?></th>
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
	var lang_delivery = <?= json_encode(['tnh_please_chosen_customer' => lang('tnh_please_chosen_customer'), 'tnh_expected_date' => lang('tnh_expected_date'), 'tnh_quantity_delivery_less' => lang('tnh_quantity_delivery_less')]) ?>;
	var taxs = <?= json_encode($taxs) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 1;
	var counter = 0;
	var count_errors = 0;
	var delivery_id = <?= $id ?>;
</script>

<script type="text/javascript" src="<?= js('delivery.js?vs=1.1') ?>"></script>