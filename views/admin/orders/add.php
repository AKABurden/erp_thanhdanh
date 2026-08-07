<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/orders/add', array('id' => 'orders')); ?>
<style>
	.table-child {
		margin: 5px !important;
	}
	.table-child tr th {
		background: #0e306340 !important;
		border: 1px solid #0e306340 !important;
    	color: black !important;
	}

	.table-child-size {
		margin: 5px !important;
	}
	.table-child-size tr th {
		background: #0e306340 !important;
		border: 1px solid #0e306340 !important;
    	color: black !important;
	}
</style>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?= $title ?></span>
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
						<div role="tabpanel">
							<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 5px;">
								<li role="presentation" class="active">
									<a href="#home1" aria-controls="home" role="tab" data-toggle="tab"><?= lang('tnh_info_primary') ?></a>
								</li>
								<li role="presentation">
									<a href="#tab-detail" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_detail') ?></a>
								</li>
							</ul>

							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="home1">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;">
													<?= lang('tnh_reference_orders', 'reference_no') ?>
												</td>
												<td style="width: 35%;">
													<div class="form-group">
														<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
													</div>
												</td>
												<td style="width: 15%;">
													<?= lang('date', 'date') ?>
												</td>
												<td style="width: 35%;">
													<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
												</td>
											</tr>
											<tr>
												<td><?= lang('customers', 'customers') ?></td>
												<td>
													<div class="input-group">
														<div class="form-group">
															<input type="text" name="customers" data-placeholder="<?= lang('customers') ?>" id="customers" class="customers" required style="width: 100%;" value="<?= isset($id_customer) ? $id_customer : '' ?>">
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
												<td><?= lang('h_branch', 'branch') ?></td>
												<td colspan="1">
													<select name="id_branch" id="branch" data-placeholder="<?= lang('h_branch') ?>" style="width: 100%;" class="">
														<option value=""></option>
														<?php foreach ($branch as $key => $value) : ?>
															<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
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
											<tr class="">
												<td><?= lang('tnh_type_orders', 'type_orders') ?></td>
												<td colspan="1">
													<select name="type_orders" id="type_orders" data-placeholder="<?= lang('tnh_type_orders') ?>" class="type_orders" style="width: 100%;" required>
														<option value=""></option>
														<?php foreach ($type_orders as $key => $value) : ?>
															<option <?= $value['id'] == ORDER_DEFAULT ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
                                                <td><?= lang('c_type_items', 'type_items') ?></td>
                                                <td colspan="1">
                                                    <select name="type_items" id="type_items" data-placeholder="<?= lang('c_type_items') ?>" class="type_items" style="width: 100%;" required>
                                                        <option value=""></option>
														<?php foreach ($type_items as $key => $value) : ?>
                                                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
                                                    </select>
                                                </td>
											</tr>
                                            <tr>
                                                <td><?= lang('tnh_status_orders', 'status_orders') ?></td>
                                                <td colspan="1">
                                                    <select name="status_orders" id="status_orders" data-placeholder="<?= lang('tnh_status_orders') ?>" class="status_orders" style="width: 100%;">
                                                        <option value=""></option>
														<?php foreach ($status_orders as $key => $value) : ?>
                                                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
                                                    </select>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
											<tr class="orders-separate-guest" style="display: none;">
												<td><?= lang('tnh_so', 'so') ?></td>
												<td>
													<input type="text" name="so" id="so" placeholder="<?= lang('tnh_so') ?>" class="form-control so" value="">
												</td>
												<td><?= lang('tnh_pi', 'pi') ?></td>
												<td>
													<input type="text" name="pi" id="pi" placeholder="<?= lang('tnh_pi') ?>" class="form-control pi" value="">
												</td>
											</tr>
											<tr class="orders-separate-guest" style="display: none;">
												<td><?= lang('tnh_po_style', 'po_style') ?></td>
												<td>
													<input type="text" name="po_style" id="po_style" placeholder="<?= lang('tnh_po_style') ?>" class="form-control po_style" value="">
												</td>
												<td><?= lang('tnh_item_code_tem', 'item_code') ?></td>
												<td>
													<input type="text" name="item_code" id="item_code" placeholder="<?= lang('tnh_item_code_tem') ?>" class="form-control item_code" value="">
												</td>
											</tr>
											<tr class="orders-type-sample-order">
												<td><?= lang('Báo giá mẫu', 'quote_id_chonse') ?></td>
												<td>
													<input type="text" name="quote_id_chonse" id="quote_id_chonse" data-placeholder="<?= lang('tnh_quotes') ?>" class="quote_id_chonse modal-select2" style="width: 100%;" value="">
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr class="orders-type-compensate-order" style="display: none;">
												<td><?= lang('Bù cho đơn hàng', 'orders_choose') ?></td>
												<td>
													<select name="orders_choose[]" id="orders_choose" data-none-selected-text="<?= lang('tnh_orders') ?>" class="form-control" data-live-search="true" data-actions-box="true" multiple>
														<option value=""></option>
													</select>
												</td>
												<td><?= lang('Bù cho LSX', 'productions_orders_choose') ?></td>
												<td>
													<select name="productions_orders_choose[]" id="productions_orders_choose" class="form-control" data-none-selected-text="<?= lang('productions_orders') ?>" data-live-search="true" data-actions-box="true" multiple>
														<option value=""></option>
													</select>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div role="tabpanel" class="tab-pane" id="tab-detail">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr class="">
												<td style="width: 15%;"><?= lang('table_set_prices', 'table_set_prices') ?></td>
												<td style="width: 35%;">
													<input type="text" name="table_price_id" data-placeholder="<?= lang('table_set_prices') ?>" id="table_price_id" class="table_price_id" value="" style="width: 100%;">
												</td>
												<td class="hide" style="width: 15%;"><?= lang('tnh_table_discount', 'table_discount') ?></td>
												<td class="hide" style="width: 35%;">
													<input type="text" name="table_discount_id" data-placeholder="<?= lang('tnh_table_discount') ?>" id="table_discount_id" class="table_discount_id" value="" style="width: 100%;">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_employees_charge', 'employees') ?></td>
												<td colspan="1">
													<select name="employees" id="employees" data-placeholder="<?= lang('tnh_employees_charge') ?>" style="width: 100%;" class="">
														<option value=""></option>
														<?php foreach ($employees as $key => $value) : ?>
															<option <?= get_staff_user_id() == $value['staffid'] ? 'selected' : '' ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td><?= lang('note', 'note') ?></td>
												<td colspan="1">
													<textarea name="note" id="note" placeholder="<?= lang('note') ?>" class="form-control note" rows="3"></textarea>
												</td>
											</tr>
											<tr>
												<td class="hide"><?= lang('tnh_gift', 'gift') ?></td>
												<td class="hide">
													<div class="checkbox checkbox-primary" style="margin-bottom: 0;">
														<input type="checkbox" name="gift" id="gift" class="gift" value="1">
														<label for="gift"><?= lang('choose') ?></label>
													</div>
												</td>
												<td><?= lang('attachments_file', 'attachments') ?></td>
												<td colspan="3">
													<div class="input-group input-file-multiple" name="files[]">
														<span class="input-group-btn">
															<button class="btn btn-default btn-choose" style="height: 36px;" type="button"><?= lang('file') ?></button>
														</span>
														<input type="text" name="text_file" class="form-control" placeholder='<?= lang('choose') ?>' />
														<span class="input-group-btn">
															<button class="btn btn-warning btn-reset" style="height: 36px;" type="button"><?= lang('tnh_reset') ?></button>
														</span>
													</div>
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
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-4">
						<?= lang('File nhập columns', 'file_import') ?>
						<input type="file" name="file_import" id="file_import" class="form-control file_import" value="">
					</div>
					<div class="col-md-4">
						<?= lang('Mã thành phẩm import', 'code_import') ?>
						<input type="text" name="code_import" placeholder="<?= lang('Mã thành phẩm import') ?>" id="code_import" class="form-control code_import" value="">
					</div>
					<div class="col-md-3">
						<!-- <a href="<?= ''//base_url('file/orders/import_columns_orders.xlsx?vs=1.1') ?>" target="_blank" class="btn btn-success" style="margin-top: 27px;"><?= ''//lang('File mẫu') ?></a> -->
						<a href="javascript:void(0)" onclick="exportExcelTemplate()" class="btn btn-success" style="margin-top: 27px;"><?= lang('File mẫu') ?></a>
						<a href="javascript:void(0)" onclick="addImportColumns()" class="btn btn-primary" style="margin-top: 27px;"><?= lang('import') ?></a>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div id="top_search_new" style="width: 100%;">
					<div>
						<input style="float: right; height: 60px" type="search" id="SearchQR" class="form-control"
								placeholder="<?php echo _l('Quét mã qr sản phẩm...'); ?>">
					</div>
					<div id="top_search_button" class="top_search_button_scan" style="top: 15px">
						<button type="button" class="btn"><i class="fa fa-barcode" aria-hidden="true"></i></button>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="tb-height">
					<div class="table-responsive">
						<table id="tb-orders" class="table tnh-table table-bordered table-striped dataTable" style="min-width: 1700px; width: 100%; margin-top: 20px !important;">
							<thead>
								<tr>
									<th class="text-center" style="width: 30px;">
										<a class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
											<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
												<path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
											</svg>
										</a>
                                        <div class="text-right checkbox checkbox-info">
                                            <input type="checkbox" id="checkall">
                                            <label for="checkall"></label>
                                        </div>
									</th>
									<th style="width: 150px;" class="text-center"><?= lang('tnh_product_code') ?><small class="req text-danger">*</small></th>
									<th style="width: 50px" class="text-center"><?= lang('tnh_images') ?></th>
									<th style="width: 150px;" class="text-center"><?= lang('tnh_product_name_customer') ?></th>
									<!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_mode_product') ?></th> -->
									<th style="width: 50px;" class="text-center"><?= lang('tnh_dvt') ?></th>
									<th style="width: 100px;" class="text-center"><?= lang('tnh_sample_quantity') ?></th>
									<th style="width: 100px;" class="text-center"><?= lang('tnh_total_quantity_put') ?></th>
									<th style="width: 100px;" class="text-center"><?= lang('tnh_total_quantity') ?></th>
									<th style="width: 100px;" class="text-center"><?= lang('tnh_unit_price') ?></th>
									<!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_discount_percent') ?></th> -->
									<th style="width: 100px;" class="text-center"><?= lang('tnh_total_amount') ?></th>
									<!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_size') ?></th> -->
									<th style="width: 50px;" class="text-center"><?= lang('tnh_loss') ?></th>
									<th style="width: 120px;" class="text-center"><?= lang('cong_shipment_date') ?></th>
									<th style="width: 120px;" class="text-center"><?= lang('Chi tiết giao hàng') ?></th>
									<th style="width: 200px;" class="text-center"><?= lang('note') ?></th>
									<th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="total-table hide">
					<table class="tnh-table table">
						<tr class="danger bold">
							<td style="width: 10%;"><?= lang('tnh_total_quantity') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-quantity text-center"></td>
							<td style="width: 10%;"><?= lang('tnh_total_amount') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-amount text-right"></td>
							<td style="width: 10%;"><?= lang('tax') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-tax text-right"></td>
							<td style="width: 10%;"><?= lang('tnh_discount') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="total-discount text-right"></td>
							<td style="width: 10%;"><?= lang('tnh_grand_total') ?><?= lang('tnh_i') ?></td>
							<td style="width: 10%;" class="grand-total text-right"></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-md-12">
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
							<td style="width: 15%;"><?= lang('tnh_cost_delivery', 'cost_delivery') ?></td>
							<td style="width: 35%;">
								<input type="text" name="cost_delivery" id="cost_delivery" class="form-control money-format" value="0">
							</td>
						</tr>
						<tr>
							<td><?= lang('tnh_transporters', 'transporters') ?></td>
							<td>
								<div class="input-group">
									<div class="form-group">
										<input type="text" name="transporters" data-placeholder="<?= lang('tnh_transporters') ?>" id="transporters" class="transporters" style="width: 100%;" value="">
									</div>
									<span class="input-group-addon">
										<a href="javascript:void(0)" onclick="add_flash_transporters()"><i class="fa fa-plus"></i></a>
									</span>
								</div>
							</td>
							<td><?= lang('tnh_charge_party', 'transporters') ?></td>
							<td>
								<select name="charge_party" id="charge_party" style="width: 100%;">
									<?php foreach (typeChargeParty() as $key => $value) : ?>
										<option value="<?= $key ?>"><?= $value ?></option>
									<?php endforeach ?>
								</select>
							</td>
						</tr>
						<tr class="success" style="font-weight: 700;">
							<td><?= lang('tnh_grand_total', 'grand_total') ?></td>
							<td colspan="3" class="td-grand-total-all text-right">0</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
			<input type="hidden" name="add" id="" class="form-control" value="1">
			<button type="submit" class="btn btn-info add-order">
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
<script type="text/javascript">
	var lang_orders = <?= json_encode(['tnh_please_chosen_customer' => lang('tnh_please_chosen_customer'), 'tnh_expected_date' => lang('tnh_expected_date'), 'tnh_change_table_prices_when_items_price_change' => lang('tnh_change_table_prices_when_items_price_change'), 'tnh_change_table_discount_when_items_discount_change' => lang('tnh_change_table_discount_when_items_discount_change'), 'tnh_qty_warehoused' => lang('tnh_qty_warehoused'), 'tnh_product_code' => lang('tnh_product_code'), 'tnh_images' => lang('tnh_images'), 'tnh_product_name' => lang('tnh_product_name_customer'), 'tnh_unit' => lang('tnh_unit'), 'quantity' => lang('quantity'), 'tnh_unit_price' => lang('tnh_unit_price'), 'tnh_discount_percent' => lang('tnh_discount_percent'), 'tnh_total_amount' => lang('tnh_total_amount'), 'tnh_size' => lang('tnh_size'), 'tnh_loss' => lang('tnh_loss'), 'cong_shipment_date' => lang('cong_shipment_date'), 'note' => lang('note'), 'actions' => lang('actions')]) ?>;

	var taxs = <?= json_encode($taxs) ?>;
	var size = <?= !empty($size) ? json_encode($size) : '{}' ?>;
	var colors = <?= !empty($colors) ? json_encode($colors) : '{}' ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 0;
	var counter = 0;
	var count_errors = 0;
	var arr_category_id = [];
	var table_discount_default_id = 0;
	var total_amount = 0;
	var counter_child = 0;
	const ORDER_DEFAULT = <?= ORDER_DEFAULT ?>;
	const ORDER_CHANGE = <?= ORDER_CHANGE ?>;
	const ORDER_CHANGE_SIZE = <?= ORDER_CHANGE_SIZE ?>;
	const TYPE_SAMPLE_ORDER = <?= TYPE_SAMPLE_ORDER ?>;
	const TYPE_COMPENSATE_ORDER = <?= TYPE_COMPENSATE_ORDER ?>;
	const TYPE_KH_ORDER = <?= TYPE_KH_ORDER ?>;
	const TYPE_PTM = <?= TYPE_PTM ?>;
	const QUANTITY_PTM = <?= QUANTITY_PTM ?>;
</script>

<script type="text/javascript" src="<?= js('orders.js?vs=5.52') ?>"></script>
<script>
    $('#checkall').change(function() {
        if($(this).prop('checked')) {
            $('.checkbox_item').prop('checked', true).trigger('change');
        }
        else {
            $('.checkbox_item').prop('checked', false).trigger('change');
        }
    })
</script>