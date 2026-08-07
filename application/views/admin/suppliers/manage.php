<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
	.new_contact_form {
		border: 1px solid #bfcbd9;
	}

	.new_contact_form:hover i {
		color: #afafaf;
	}

	.btn_add_contact {
		border: 1px dotted;
	}

	.remove_contact_panel {
		text-align: right;
	}

	.remove_contact_panel i {
		color: #fff;
		font-size: 18px;
	}

	.remove_contact_panel i:hover {
		cursor: pointer;
	}

	.btn_add_contact i {
		font-size: 5em;
		color: #adadad;
	}

	.add_new_contact p {
		margin: 0;
		color: #adadad;
	}

	.add_new_contact {
		text-align: center;
		padding: 10px 0;
	}

	.add_new_contact:hover p,
	.add_new_contact:hover i {
		color: #7b7b7b;
		cursor: pointer;
	}

	.append_html>div {
		padding: 0 0 15px 5px;
	}

	.table-suppliers tr td:nth-child(1) {
		min-width: 20px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(2) {
		min-width: 110px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(3) {
		min-width: 110px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(4) {
		min-width: 80px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(5) {
		min-width: 110px;
		white-space: unset;
	}

	.table-suppliers tr td:nth-child(6) {
		min-width: 80px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(7) {
		min-width: 80px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(8) {
		min-width: 110px;
		white-space: unset;
		text-align: center;
	}

	.table-suppliers tr td:nth-child(9) {
		min-width: 110px;
		white-space: unset;
	}

	.table-suppliers tr td:nth-child(10) {
		min-width: 110px;
		white-space: unset;
	}

	.table-suppliers tr td:nth-child(11) {
		min-width: 120px;
		white-space: unset;
	}

	.table-suppliers tr td:nth-child(12) {
		min-width: 80px;
		white-space: unset;
	}

	<?php for ($i = 13; $i < 24; $i++) { ?>.table-suppliers tr td:nth-child(<?= $i ?>) {
		min-width: 80px;
		white-space: unset;
	}

	<?php  } ?>
</style>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="H_scroll">
		<div class="panel-body _buttons">
			<div class="_buttons">
				<span class="bold uppercase fsize18 H_title"><?= $title ?></span>
				<div class="pull-right mright5 H_border">
					<!-- <a data-toggle="modal" data-target="#export_excel_suppliers" class="btn btn-info test H_action_button">
						<?php echo _l('Export excel'); ?></a> -->
					<a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
				</div>
				<?php if (has_permission('suppliers', '', 'create')) { ?>
					<div class="pull-right mright5 H_border">
						<a href="<?php echo admin_url('suppliers/import_suppliers'); ?>" class="btn btn-info hidden-xs H_action_button">
							<?php echo _l('Import excel'); ?></a>
					</div>
					<div class="pull-right mright5 H_border">
						<a href="#" onclick="int_suppliers_view('','true'); return false;" id="suppliers_modal" class="btn btn-info test H_action_button">
							<?php echo _l('create_add_new'); ?></a>
					</div>
				<?php } ?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div id="search-tnh" class="collapse in" aria-expanded="true">
					<div class="col-md-3">
						<?= lang('supplier') ?>
						<input type="text" name="supplier" data-placeholder="<?= lang('supplier') ?>" id="supplier" class="supplier" style="width: 100%;" value="">
					</div>
					<div class="col-md-3">
						<?= lang('customer_groups') ?>
						<input type="text" name="groups_ch" data-placeholder="<?= lang('customer_groups') ?>" id="groups_ch" class="groups_ch" style="width: 100%;" value="<?= !empty($_GET['category_id']) ? $_GET['category_id'] : '' ?>">
					</div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                               id="start_date_search" autocomplete="off" class="start_date_search datepicker form-control"
                               style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                               id="end_date_search" autocomplete="off" class="end_date_search datepicker form-control" style="width: 100%;"
                               value="">
                    </div>
				</div>
			</div>
			<div class="col-md-12 hide_btn_options">
				<div class="panel_s">
					<div class="panel-body">
						<div class="horizontal-scrollable-tabs">
							<div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
							<div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
							<div class="horizontal-tabs">
								<ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
									<li class="active">
										<a class="H_filter" data-id="all">
											<?php
											$arrBranch = get_branch_staff();
											$this->db->select('COUNT(*) as count');
											$this->db->from('tblsuppliers');
											if (!is_admin()) {
												if (!empty($arrBranch)) {
													$coverStrBranch = implode(",", $arrBranch);
													$this->db->where('(tblsuppliers.id IN (
                                                            SELECT tbl_suppliers_branch.suppliers_id FROM tbl_suppliers_branch WHERE tbl_suppliers_branch.branch_id IN (' . $coverStrBranch . ') )  
                                                    )');
												} else {
													$this->db->where('tblsuppliers.id = 0');
												}
											}
											$all = $this->db->get()->row_array()['count'];

											$this->db->select('COUNT(*) as count');
											$this->db->from('tblsuppliers');
											if (!is_admin()) {
												if (!empty($arrBranch)) {
													$coverStrBranch = implode(",", $arrBranch);
													$this->db->where('(tblsuppliers.id IN (
                                                            SELECT tbl_suppliers_branch.suppliers_id FROM tbl_suppliers_branch WHERE tbl_suppliers_branch.branch_id IN (' . $coverStrBranch . ') )  
                                                    )');
												} else {
													$this->db->where('tblsuppliers.id = 0');
												}
											}
											$this->db->where('tblsuppliers.type = 0');
											$supplier = $this->db->get()->row_array()['count'];

											$this->db->select('COUNT(*) as count');
											$this->db->from('tblsuppliers');
											if (!is_admin()) {
												if (!empty($arrBranch)) {
													$coverStrBranch = implode(",", $arrBranch);
													$this->db->where('(tblsuppliers.id IN (
                                                            SELECT tbl_suppliers_branch.suppliers_id FROM tbl_suppliers_branch WHERE tbl_suppliers_branch.branch_id IN (' . $coverStrBranch . ') )  
                                                    )');
												} else {
													$this->db->where('tblsuppliers.id = 0');
												}
											}
											$this->db->where('tblsuppliers.type = 1');
											$transporters = $this->db->get()->row_array()['count'];
											?>
											<?= _l('leads_all') ?> (<span class="all"><?= formatNumber($all) ?></span>)
										</a>
									</li>
									<li>
										<a class="H_filter" data-id="0">
											<?= _l('supplier') ?> (<?= formatNumber($supplier) ?>)
										</a>
									</li>
									<li>
										<a class="H_filter" data-id="1">
											<?= _l('tnh_transporters') ?> (<?= formatNumber($transporters) ?>)
										</a>
									</li>
								</ul>
							</div>
						</div>
						<input type="hidden" id="filterStatus" name="filterStatus" value="" />
						<div class="clearfix"></div>
						<?php
						$table_data = array();
						$table_data = array(
							'STT',
							_l('Mã nhóm NCC'),
							_l('Tên nhóm NCC'),
							_l('Mã NCC'),
							_l('ch_name_suppliers'),
							lang('Tên viết tắt'),
							_l('ch_type_suppliers'),
							_l('clients_vat'),
							_l('ch_code_nxk'),
							_l('VAT'),
							_l('tnh_bank_account'),
							_l('tnh_name_account'),
							_l('ch_address_bank'),
							_l('acs_sales_payment_modes_submenu'),
							_l('invoice_add_edit_currency'),
							_l('Công thức chuyển đổi'),
							_l('tnh_time_payment'),
							_l('Loại hợp đồng'),
							_l('Hợp đồng số'),
							_l('ch_renewal_date'),
							_l('client_address'),
							_l('client_representative'),
							_l('Thông số kiện'),
							_l('Mã khoản/Nhóm chi phí'),
							_l('Ngày bắt đầu hoạt động'),
							// _l('clients_phone'),
							// _l('ch_address_delivery'),
							_l('customer_active'),
							_l('ch_date_created'),
							_l('Chi nhánh'),
						);
						$table_data = hooks()->apply_filters('customers_table_columns', $table_data);

						render_datatable($table_data, 'suppliers', [], [
							'data-last-order-identifier' => 'suppliers',
							'data-default-order'         => get_table_last_order('suppliers_'),
						]);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php $this->load->view('admin/suppliers/suppliers_group'); ?>
<?php init_tail(); ?>
<?php
include_once(APPPATH . 'views/admin/suppliers/export_suppliers.php');
?>
<script>
	$('.H_filter').click(function(e) {
		var target = $(e.currentTarget);
		var value = target.attr('data-id');
		target.parent().parent().find('li').removeClass('active');
		target.parent().addClass('active');
		$('input[name="filterStatus"]').val(value);
		$('input[name="filterStatus"]').change();
	});

	function int_suppliers_view_nopupup(id = null, edit) {
		$('#suppliers_modal_data').html('');
		$.get(admin_url + 'suppliers/int_suppliers_view_data/' + edit + '/' + id).done(function(response) {
			$('#suppliers_modal_data').html(response);
			add_html_evaluate(id);
			init_selectpicker();
			init_datepicker();
			$('#view_submit').addClass('hide');
			$('.view').removeClass('hide');
			$('.edit').addClass('hide');
			$('.report_debt').removeClass('hide');
		}).fail(function(error) {
			var response = JSON.parse(error.responseText);
			alert_float('danger', response.message);
		});
	}

	function int_suppliers_view(id = null, edit) {
		$('#suppliers_view_data').html('');
		$.get(admin_url + 'suppliers/int_suppliers_view/' + edit + '/' + id).done(function(response) {
			$('#suppliers_view_data').html(response);
			add_html_evaluate(id);
			$('#suppliers_add').modal({
				show: true,
				backdrop: 'static'
			});
			init_selectpicker();
			init_datepicker();
		}).fail(function(error) {
			var response = JSON.parse(error.responseText);
			alert_float('danger', response.message);
		});
	}

	<?php if ($this->input->get('modal') == true) { ?>
		$(document).ready(function() {
			int_suppliers_view('', true);
		});
	<?php } ?>

	function int_suppliers_edit(id = '') {
		$.ajax({
				url: admin_url + 'suppliers/get_suppliers/' + id,
				dataType: 'json',
			})
			.done(function(data) {
				$('[name="id"]').val(data.id);
				$('[name="supplier_code"]').val(data.code);
				$('[name="company"]').val(data.company);
				$('[name="phone"]').val(data.phone);
				$('[name="email"]').val(data.email);
				$('[name="note"]').val(data.note);
				$('[name="vat"]').val(data.vat);
				$('[name="address"]').val(data.address);
				$('[name="default_currency"]').selectpicker('val', data.default_currency);
				$('[name="groups_in"]').selectpicker('val', data.groups_in);
				$('#suppliers_add').modal('show');
			});
	}
	$('body').on('click', '#suppliers_modal', function() {
		$.ajax({
				url: admin_url + 'suppliers/get_suppliers/',
				dataType: 'json',
			})
			.done(function(data) {
				$('#suppliers_add').modal('show');
			});
	});

	function new_suppliers_source_inline(e = 'groups_in') {
		var t = "";
		($("body").hasClass("suppliers-email-integration") || $("body").hasClass("web-to-suppliers-form")) && (e = "suppliers_" + e), t = '<div id="new_suppliers_' + e + '_inline" class="form-group"><label for="new_' + e + '_name">' + $('label[for="' + e + '"]').html() + '</label><div class="input-group"><input type="text" id="new_' + e + '_name" name="new_' + e + '_name" class="form-control"><div class="input-group-addon"><a href="#" onclick="suppliers_add_inline_select_submit(\'' + e + '\'); return false;" class="suppliers-add-inline-submit-' + e + '"><i class="fa fa-check"></i></a></div></div></div>', $(".form-group-select-input-" + e).after(t), $("body").find("#new_" + e + "_name").focus(), $('.suppliers-save-btn,#form_info button[type="submit"],#suppliers-email-integration button[type="submit"],.btn-import-submit').prop("disabled", !0), $(".suppliers-field-new").addClass("disabled").css("opacity", .5), $(".form-group-select-input-" + e).addClass("hide")
	}

	function suppliers_add_inline_select_submit(e) {
		var t = $("#new_" + e + "_name").val();
		if ("" !== t) {
			var a = 'group';
			e.indexOf("suppliers_") > -1 && (a = a.replace("suppliers_", ""));
			var i = {
				[csrfData['token_name']]: csrfData['hash']
			};
			i.name = t, i.inline = !0, $.post(admin_url + "suppliers/" + a, i).done(function(a) {
				if (!0 === (a = JSON.parse(a)).success || "true" == a.success) {
					var i = $("body").find("select#" + e);
					i.append('<option value="' + a.id + '">' + t + "</option>"), i.selectpicker("val", a.id), i.selectpicker("refresh"), i.parents(".form-group").removeClass("has-error")
				}
			})
		}
		$("#new_suppliers_" + e + "_inline").remove(), $(".form-group-select-input-" + e).removeClass("hide"), $('.suppliers-save-btn,#form_info button[type="submit"],#suppliers-email-integration button[type="submit"],.btn-import-submit').prop("disabled", !1), $(".suppliers-field-new").removeClass("disabled").removeAttr("style")
	}
	$(function() {
		var CustomersServerParams = {
			'filterStatus': '[name="filterStatus"]',
			'id_supplier': '[name="supplier"]',
			'group': '[name="groups_ch"]',
			'start_date_search': '[name="start_date_search"]',
			'end_date_search': '[name="end_date_search"]',
		};
		$.each($('._hidden_inputs._filters input'), function() {
			CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
		});
		CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

		var tAPI = initDataTable('.table-suppliers', admin_url + 'suppliers/table', [], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>);
		$.each(CustomersServerParams, function(filterIndex, filterItem) {
			$(filterItem).on('change', function() {
				tAPI.draw('page');
			});
		});
		$('input[name="exclude_inactive"]').on('change', function() {
			tAPI.ajax.reload();
		});
	});


	//Hoàng CRM
	function add_contact_view() {
		var length = $('.new_contact_form').length;
		console.log(length);
		var opt = {
			format: "<?= substr(get_option('dateformat'), 0, 5); ?>",
			timepicker: false,
			scrollInput: false,
			lazyInit: true,
			dayOfWeekStart: 0,
		};
		var html = '<div class="col-md-6">\
                    <div class="col-md-12 new_contact_form">\
                      <div class="remove_contact_panel">\
                        <a class="remove_html" title="Xóa"><i class="fa fa-trash"></i></a>\
                      </div>\
                      <div class="col-md-12">\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][name]"><label for="alo" class="control-label"><?= _l('clients_list_full_name') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][name]" name="contact[' + (length + 1) + '][name]" class="form-control" value="">\
                          </div>\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][phone]"><label for="alo" class="control-label"><?= _l('leads_dt_phonenumber') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][phone]" name="contact[' + (length + 1) + '][phone]" class="form-control" value="">\
                          </div>\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][email]"><label for="alo" class="control-label"><?= _l('client_email') ?></label>\
                          <input type="email" id="contact[' + (length + 1) + '][email]" name="contact[' + (length + 1) + '][email]" class="form-control" value="">\
                          </div>\
                      </div>\
                      <div class="col-md-12">\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][address]"><label for="alo" class="control-label"><?= _l('settings_sales_address') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][address]" name="contact[' + (length + 1) + '][address]" class="form-control" value="">\
                          </div>\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][birthday]"><label for="alo" class="control-label"><?= _l('birthday') ?></label>\
                                <div class="input-group date">\
                                    <input type="text" class="form-control datepicker" id="date_dk" name="contact[' + (length + 1) + '][birthday]">\
                                    <div class="input-group-addon">\
                                    <i class="fa fa-calendar calendar-icon"></i>\
                                    </div>\
                                </div>\
                            </div>\
                           <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][sex]"><label for="contact[' + (length + 1) + '][sex]" class="control-label"><?= _l('sex') ?></label><div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="contact[' + (length + 1) + '][sex]" name="contact[' + (length + 1) + '][sex]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98"><option value=""></option><option value="1">Nam</option><option value="2">Nữ</option><option value="3">Khác</option></select><div class="dropdown-menu open" role="combobox"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off" role="textbox" aria-label="Search"></div><div class="inner open" role="listbox" aria-expanded="false" tabindex="-1"><ul class="dropdown-menu inner "></ul></div></div></div></div>\
                        </div>\
                      <div class="col-md-12">\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][note]"><label for="alo" class="control-label"><?= _l('note') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][note]" name="contact[' + (length + 1) + '][note]" class="form-control" value="">\
                          </div>\
                      </div>\
                      <div class="col-md-6 form-group">\
                          <input type="checkbox" value="1" name="contact[' + (length + 1) + '][receive_email]"><?= _l('get_email') ?> </div>\ <div class = "col-md-6 form-group" > \
			<input type = "checkbox"value = "1"name = "contact[' + (length + 1) + '][main_contact]" > <?= _l('key_contact') ?> </div>\ </div>\ </div>';
		$('.append_html').append(html);
		$('#date_dk').datetimepicker(opt);
		init_selectpicker();
		init_datepicker();
	}
	$(document).on('click', '.remove_html', (e) => {
		$(e.currentTarget).parent().parent().parent().remove();
	});

	function add_html_evaluate(id_supplier) {
		var val = $('.evaluate_view').attr('data-val');
		$('.evaluate_view').html('');
		$('.evaluate_view_left').html('');
		$.ajax({
				url: admin_url + 'suppliers/get_html_evaluate/' + id_supplier + '/' + val,
				dataType: 'json',
			})
			.done(function(data) {
				$('.evaluate_view').html(data.data);
				$('.evaluate_view_left').html(data.data_left);
			});
	}
	//end
	$(document).on('click', '.delete-remind', function() {
		var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
		if (r == false) {
			return false;
		} else {
			$.get($(this).attr('href'), function(response) {
				alert_float(response.alert_type, response.message);
				$('.table-suppliers').DataTable().ajax.reload();
				$('.table-contacts').DataTable().ajax.reload();
			}, 'json');
		}
		return false;
	});

	function ajaxSelectCallBack(element, url, id, types = '') {
		if (id > 0) {
			$(element).val(id).select2({
				// minimumInputLength: 1,
				width: 'resolve',
				allowClear: true,
				initSelection: function(element, callback) {
					$.ajax({
						type: "get",
						async: false,
						url: url + '/' + id + '/' + types,
						dataType: "json",
						success: function(data) {
							callback(data.results[0].children[0]);
						}
					});
				},
				ajax: {
					url: url,
					dataType: 'json',
					quietMillis: 15,
					data: function(term, page) {
						return {
							type: $('#type_items').val(),
							types: types,
							term: term,
							limit: 50
						};
					},
					results: function(data, page) {
						if (data.results != null) {
							return {
								results: data.results
							};
						} else {
							return {
								results: [{
									id: '',
									text: 'No Match Found'
								}]
							};
						}
					}
				},
				formatResult: repoFormatSelection,
				formatSelection: repoFormatSelection,
				dropdownCssClass: "bigdrop",
				escapeMarkup: function(m) {
					return m;
				}
			});
		} else {
			$(element).select2({
				// minimumInputLength: 1,
				width: 'resolve',
				allowClear: true,
				ajax: {
					url: url + '/' + $(element).val(),
					dataType: 'json',
					quietMillis: 15,
					data: function(term, page) {
						return {
							type: $('#type_items').val(),
							types: types,
							term: term,
							limit: 50
						};
					},
					results: function(data, page) {
						if (data.results != null) {
							return {
								results: data.results
							};
						} else {
							return {
								results: [{
									code_client: '',
									id: '',
									text: 'No Match Found'
								}]
							};
						}
					}
				},
				formatResult: repoFormatSelection,
				formatSelection: repoFormatSelection,
				dropdownCssClass: "bigdrop",
				escapeMarkup: function(m) {
					return m;
				}
			});
		}
	}
	var base_url = '<?= base_url() ?>';

	function repoFormatSelection(state) {
		if (!state.id) return state.text;

		return state.text;
	}
	ajaxSelectCallBack($('#supplier'), "<?= admin_url('suppliers/SearchSupplier') ?>", 0);
	ajaxSelectCallBack($('#groups_ch'), "<?= admin_url('suppliers/SearchGroup') ?>", $('#groups_ch').val());

	function exportExcel() {
		groups_ch = $('[name="groups_ch"]').val();
		$.ajax({
			type: "POST",
			url: site.base_url + 'admin/suppliers/exportExcelSuppliers',
			data: {
				csrf_token_name: hash,
				groups_ch: groups_ch,
                start_date_search: $("#start_date_search").val(),
                end_date_search: $("#end_date_search").val(),
				export_excel: 1,
			},
			dataType: "json",
			success: function(response) {
				if (response.result) {
					alert_float('success', response.message);
					download(response.filename, response.file);
				} else {
					alert_float('danger', response.message);
				}
			}
		});
	}
</script>
</body>

</html>