<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/quotes/add', array('id' => 'quotes')); ?>
<style type="text/css">
	#tb-productions-quotes tr td {
		vertical-align: top !important;
	}

	#tb-productions-quotes th:nth-child(5),
	#tb-productions-quotes td:nth-child(5) {
		display: none !important;
	}
</style>
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
						<div role="tabpanel">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active">
									<a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?= lang('info') ?></a>
								</li>
								<li role="presentation">
									<a href="#tab" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_detail') ?></a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="home">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;">
													<?= lang('tnh_reference_no_quote', 'reference_no') ?>
												</td style="width: 35%;">
												<td>
													<div class="form-group">
														<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
													</div>
												</td>
												<td style="width: 10%;">
													<?= lang('date', 'date') ?>
												</td>
												<td style="width: 40%;">
													<div class="form-group">
														<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<?= lang('customers', 'customers') ?>
												</td>
												<td>
													<div class="input-group">
														<div class="form-group">
															<input type="text" name="customers" data-placeholder="<?= lang('customers') ?>" id="customers" class="customers" required style="width: 100%;" value="">
														</div>
														<span class="input-group-addon">
															<a href="javascript:void(0)" class="add-flash-customer"><i class="fa fa-plus"></i></a>
														</span>
													</div>
												</td>
												<td><?= lang('tnh_person_contact', 'person_contact') ?></td>
												<td>
													<div class="form-group">
														<input type="text" name="person_contact" data-placeholder="<?= lang('tnh_person_contact') ?>" id="person_contact" class="person_contact" style="width: 100%;" value="">
													</div>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_address_delivery') ?></td>
												<td>
													<div class="input-group">
														<input type="tel" name="address_delivery" id="address_delivery" data-placeholder="<?= lang('tnh_address_delivery') ?>" class="modal-select2" style="width: 100%;" value="">
														<span class="input-group-addon">
															<a href="javascript:void(0)" class="add-address-delivery"><i class="fa fa-plus"></i></a>
														</span>
													</div>
												</td>
												<td><?= lang('tnh_bale_parameters', 'bale_parameters') ?></td>
												<td>
													<textarea name="bale_parameters" placeholder="<?= lang('tnh_bale_parameters') ?>" id="bale_parameters" class="form-control" rows="3"></textarea>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_currencies', 'currencies') ?></td>
												<td>
													<select name="currencies" id="currencies" data-placeholder="<?= lang('tnh_currencies') ?>" class="currencies" style="width: 100%;" required>
														<option value=""></option>
														<?php foreach ($currencies as $key => $value) : ?>
															<option data-amount_to_vnd="<?= $value['amount_to_vnd'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td><?= lang('amount_to_vnd', 'amount_to_vnd') ?></td>
												<td>
													<input type="text" name="amount_to_vnd" id="amount_to_vnd" placeholder="<?= lang('amount_to_vnd') ?>" class="form-control money-format" value="" required>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_delivery_term', 'delivery_term') ?></td>
												<td colspan="1">
													<textarea name="delivery_term" id="delivery_term" placeholder="<?= lang('tnh_delivery_term') ?>" class="form-control delivery_term" rows="2"></textarea>
												</td>
												<td><?= lang('tnh_ship_to', 'ship_to') ?></td>
												<td colspan="1">
													<textarea name="ship_to" id="ship_to" placeholder="<?= lang('tnh_ship_to') ?>" class="form-control ship_to" rows="2"></textarea>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_payment_detail', 'payment_detail') ?></td>
												<td colspan="1">
													<textarea name="payment_detail" id="payment_detail" placeholder="<?= lang('tnh_payment_detail') ?>" class="form-control payment_detail" rows="2"></textarea>
												</td>
												<td><?= lang('tnh_payment_term', 'payment_term') ?></td>
												<td colspan="1">
													<textarea name="payment_term" id="payment_term" placeholder="<?= lang('tnh_payment_term') ?>" class="form-control payment_term" rows="2"></textarea>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_expiration_date', 'expiration_date') ?></td>
												<td colspan="1">
													<input type="text" placeholder="<?= lang('tnh_expiration_date') ?>" name="expiration_date" id="expiration_date" class="form-control expiration_date datepicker" value="">
												</td>
												<td><?= lang('h_branch', 'id_branch') ?></td>
												<td colspan="1">
													<select name="id_branch" id="id_branch" data-placeholder="<?= lang('h_branch') ?>" style="width: 100%;" class="">
														<option value=""></option>
														<?php if (!empty($branch)) { ?>
															<?php foreach ($branch as $key => $value) : ?>
																<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
															<?php endforeach ?>
														<?php } ?>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('Yêu cầu báo giá', 'quotation_request_id') ?></td>
												<td>
													<div class="form-group">
														<input type="text" name="quotation_request_id" data-placeholder="<?= lang('Yêu cầu báo giá') ?>" id="quotation_request_id" class="quotation_request_id" style="width: 100%;" value="">
													</div>
												</td>
												<td><?= lang('Báo giá lại', 'is_quote_again') ?></td>
												<td>
													<div class="checkbox checkbox-danger">
														<input type="checkbox" name="is_quote_again" id="is_quote_again" value="1">
														<label for="is_quote_again"><?= lang('Có') ?></label>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div role="tabpanel" class="tab-pane" id="tab">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td><?= lang('note', 'note') ?></td>
												<td colspan="3">
													<textarea name="note" id="note" class="form-control" placeholder="<?= lang('note') ?>" rows="2"><?= get_option('note_quotes') ?></textarea>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" style="min-height: auto; margin-bottom: 100px;">
			<div class="col-md-4">
				<div class="form-group">
					<?= lang('import', 'import') ?>
					<input type="file" name="file_import_quotes" id="file_import_quotes" class="form-control file_import_quotes" value="">
				</div>
			</div>
			<div class="col-md-3">
				<a href="<?= base_url('file/quotes/mau_import_bao_gia.xlsx?vs=1.3') ?>" class="btn btn-success" style="margin-top: 27px;"><?= lang('File mẫu') ?></a>
				<a href="javascript:void(0)" onclick="addImportQuotes()" class="btn btn-primary btn-import-submit" style="margin-top: 27px; display: none !important;"><?= lang('import') ?></a>
			</div>
			<div class="col-md-12">
				<div class="div-errors-excel text-danger">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12">
				<div id="top_search_new" style="width: 100%;">
					<div>
						<input style="float: right; height: 60px" type="search" id="SearchQR" class="form-control" placeholder="<?php echo _l('Quét mã qr sản phẩm...'); ?>">
					</div>
					<div id="top_search_button" class="top_search_button_scan" style="top: 15px">
						<button type="button" class="btn"><i class="fa fa-barcode" aria-hidden="true"></i></button>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="tb-height">
					<div class="table-responsive">
						<table id="tb-productions-quotes" class="dt-tnh table table-hover" style="width: 100%; min-width: 1600px;">
							<thead>
								<tr>
									<th class="text-center" style="width: 30px;">
										<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
									</th>
									<th class="text-center" style="width: 180px;"><?= lang('tnh_product_code') ?></th>
									<th class="text-center" style="width: 50px"><?= lang('tnh_images') ?></th>
									<th class="text-center" style="width: 130px"><?= lang('Bảng giá công đoạn') ?></th>
									<th class="text-center" style="width: 150px;"><?= lang('tnh_technical_explanation') ?></th>
									<th class="text-center" style="width: 80px;"><?= lang('tnh_unit') ?></th>
									<th class="text-center" style="width: 200px;"><?= lang('tnh_moq') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_unit_price') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_discount_percent') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_leadtime') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('note') ?></th>
									<th class="text-center" style="width: 30px;"><?= lang('actions') ?></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<table class="table tnh-tb table-bordered table-hover">
					<tbody>
						<tr>
							<td style="width: 15%;"><?= lang('tax', 'tax') ?></td>
							<td style="width: 35%;">
								<select name="tax_id" id="tax_id" class="tax_id" data-placeholder="<?= lang('tax') ?>" style="width: 100%;">
									<option value="0"><?= lang('0%') ?></option>
									<?php foreach ($taxs as $key => $value) : ?>
										<option data-rate="<?= $value['taxrate'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
									<?php endforeach ?>
								</select>
							</td>
							<td></td>
							<td></td>
							<td class="hide"><?= lang('tnh_grand_total', 'grand_total') ?></td>
							<td style="width: 35%;" class="td-grand-total-all text-right hide">0</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add-quotes">
					<?php echo _l('submit'); ?>
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
<?= $this->load->view('lang_js') ?>
<script type="text/javascript">
	var lang_orders = <?= json_encode(['tnh_please_chosen_customer' => lang('tnh_please_chosen_customer'), 'tnh_expected_date' => lang('tnh_expected_date'), 'tnh_change_table_prices_when_items_price_change' => lang('tnh_change_table_prices_when_items_price_change'), 'tnh_change_table_discount_when_items_discount_change' => lang('tnh_change_table_discount_when_items_discount_change')]) ?>;
	// var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 0;
	var counterQuotes = 0;
	var count_errors = 0;
	var html_element = <?= json_encode(['html' => htmlInfoItems()]) ?>;
	var cTrChonse = '';
	$('#id_branch').select2({
		allowClear: true
	});
</script>

<script type="text/javascript" src="<?= module_dir_url(MODULE_NAME, 'assets/quotes.js?vs=3.7') ?>"></script>