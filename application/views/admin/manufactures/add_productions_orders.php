<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/manufactures/add_productions_orders', array('id' => 'add-productions-orders')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?= $title ?></span>
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
										<?= lang('tnh_reference_productions_orders', 'reference_no') ?>
									</td>
									<td style="width: 35%;">
										<div class="form-group">
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
										</div>
									</td>
									<td style="width: 15%;"><?= lang('date', 'date') ?></td>
									<td style="width: 35%;">
										<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_location', 'location') ?></td>
									<td colspan="1">
										<div class="form-group">
											<select name="location" id="location" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_location') ?>" required="required">
												<option value=""></option>
												<?php foreach ($locations as $key => $value) : ?>
													<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</td>
									<td><?= lang('tnh_options', 'options') ?></td>
									<td colspan="1">
										<fieldset id="options">
											<div class="checkbox checkbox-info cbobox">
												<input type="checkbox" <?= set_value('options1') == 1 ? 'checked' : '' ?> class="rel_type" name="options1" value="1" id="options1">
												<label for="options1"><?= lang('tnh_sales_orders') ?></label>
											</div>
											<div class="checkbox checkbox-info cbobox">
												<input type="checkbox" <?= set_value('options2') == 1 ? 'checked' : '' ?> class="rel_type" name="options2" value="1" id="options2">
												<label for="options2"><?= lang('tnh_business_plan') ?></label>
											</div>
										</fieldset>
									</td>
								</tr>
								<tr>
									<td>
										<div style="display: flex;">
											<i class="fa fa-info-circle" style="margin: auto; padding-right: 2px;" data-toggle="tooltip" title="<?= lang('tnh_info_orders_productions_orders') ?>"></i>
											<?= lang('tnh_orders_and_business_plan', 'orders') ?>
										</div>
									</td>
									<td colspan="3">
										<input type="text" name="productions_plan_id" id="productions_plan" class="orders_id" data-placeholder="<?= lang('tnh_orders_and_business_plan') ?>" style="width: 100%;" value="" title="">
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
						<h3 class="panel-title"><?= lang('cong_info_items') ?></h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<?= lang('tnh_items', 'items') ?>
									<input type="text" name="" id="items" class="items" style="width: 100%;" data-placeholder="<?= lang('tnh_items') ?>" value="">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<button type="button" style="margin-top: 25px;" class="btn btn-primary ev-all"><?= lang('tnh_check_all') ?></button>
									<button type="button" style="margin-top: 25px;" onclick="refershTable()" class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
								</div>
							</div>
						</div>
						<div class="tb-height">
							<table id="tb-productions-orders" class="table table-hover dataTable" style="width: 100%;">
								<thead>
									<tr>
										<th class="text-center" style="width: 50px;">
											<?= lang('tnh_numbers') ?>
										</th>
										<th class="text-center" style="width: 150px;"><?= lang('ĐHB/KHBTP') ?></th>
										<th class="text-center" style="width: 70px;" class="text-center"><?= lang('tnh_images') ?></th>
										<th class="text-center" style="width: 150px;"><?= lang('tnh_products') ?></th>
										<th class="text-center" style="width: 150px;"><?= lang('BOM') ?></th>
										<th class="text-center" style="width: 170px;"><?= lang('tnh_detail') ?></th>
										<th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
										<th class="text-center" style="width: 150px;"><?= lang('note') ?></th>
										<th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot class="bold">
									<tr>
										<td class="text-center">
										</td>
										<td class="text-center"><?= lang('tnh_grand_total') ?></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td class="th-total-quantity text-center"></td>
										<td></td>
										<td></td>
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
				<input type="hidden" name="add" id="" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add">
					<?php echo _l('submit'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script type="text/javascript">
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 0;
	var counter = 0;
	var count_errors = 0;
	var arr_productions_plan_id = [];
	var lang_productions_orders = <?= json_encode(['tnh_don_hang' => lang('tnh_don_hang'), 'tnh_khbtp' => lang('tnh_khbtp')]); ?>;
</script>

<script type="text/javascript" src="<?= js('productions_orders.js?vs=2.2') ?>"></script>