<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/manufactures/add_productions_plan', array('id'=>'add-productions-plan')); ?>
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
				<table class="tnh-tb table-bordered table-hover">
					<tbody>
						<tr>
							<td style="width: 15%;">
								<?= lang('tnh_reference_productions_plan', 'reference_no') ?>
							</td>
							<td style="width: 35%;">
								<div class="form-group">
									<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
								</div>
							</td>
							<td style="width: 15%;"><?= lang('date', 'date') ?></td>
							<td style="width: 35%;">
								<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
							</td>
						</tr>
						<tr>
							<td><?= lang('tnh_planning_cycle', 'planning_cycle') ?></td>
							<td colspan="3"><?= form_input('planning_cycle', set_value('date') ? set_value('date') : date('d/m/Y H:i'), 'id="planning_cycle" class="form-control dateranger-cs" placeholder="'.lang('tnh_planning_cycle').'" required') ?></td>
						</tr>
						<tr>
							<td><?= lang('tnh_options') ?></td>
							<td colspan="1">
								<fieldset id="options">
									<div class="checkbox checkbox-info cbobox">
										<input type="checkbox" <?= set_value('options1') == 1 ? 'checked' : '' ?> class="rel_type" name="options1" value="1" id="options1" <?= !empty($_POST['p_id']) ? 'checked' : '' ?>>
										<label for="options1"><?= lang('tnh_sales_orders') ?></label>
									</div>
									<div class="checkbox checkbox-info cbobox">
										<input type="checkbox" <?= set_value('options2') == 1 ? 'checked' : '' ?> class="rel_type" name="options2" value="1" id="options2" <?= !empty($_POST['p_id']) ? 'checked' : '' ?>>
										<label for="options2"><?= lang('tnh_business_plan') ?></label>
									</div>
									<div class="checkbox checkbox-danger cbobox">
										<input type="checkbox" <?= set_value('options3') == 1 ? 'checked' : '' ?> checked name="options3" value="3" id="options3">
										<label for="options3"><?= lang('tnh_auto_productions_orders') ?></label>
									</div>
								</fieldset>
								<?php if(empty($_POST['p_id'])): ?>
									<div class="pull-right">
										<a class="accordion-toggle collapsed" data-toggle="collapse" href="#collapseOrdersBusiness" onclick="collapseOrdersBusiness(this)" role="button" aria-controls="collapseOrdersBusiness"></a>
									</div>
								<?php endif; ?>
								<div class="text-danger"><?= lang('note') ?>: (<?= lang('tnh_only_pick_approved') ?>)</div>
							</td>
							<td><?= lang('tnh_branch', 'id_branch') ?></td>
							<td>
								<select name="id_branch" id="id_branch" class="id_branch" required="required" style="width: 100%;"  data-placeholder="<?= lang('tnh_branch') ?>">
									<option value=""></option>
									<?php if(!empty($branch)): ?>
										<?php foreach($branch as $key => $value): ?>
											<option <?= (!empty($_POST['_id_branch']) && $_POST['_id_branch'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</td>
						</tr>
						<tr class="hide-begin" style="display: none;">
							<td><?= lang('tnh_sales_orders') ?></td>
							<td>
								<input type="text" name="orders[]" id="orders" class="orders" value="" style="width: 100%;" data-placeholder="<?= lang('tnh_sales_orders') ?>">
							</td>
							<td><?= lang('tnh_business_plan') ?></td>
							<td>
								<input type="text" name="business_plans[]" id="business_plan" class="business_plan" style="width: 100%;" value="" data-placeholder="<?= lang('tnh_business_plan') ?>">
							</td>
						</tr>
						<tr class="hide-begin" style="display: none;">
							<td><?= lang('tnh_sales_orders_items') ?></td>
							<td>
								<input type="text" name="orders_items[]" id="orders_items" class="orders_items" value="" style="width: 100%;" data-placeholder="<?= lang('tnh_sales_orders_items') ?>">
							</td>
							<td><?= lang('tnh_business_plan_items') ?></td>
							<td>
								<input type="text" name="business_plans_items[]" id="business_plans_items" class="business_plans_items" style="width: 100%;" value="" data-placeholder="<?= lang('tnh_business_plan_items') ?>">
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
				<div class="">
					<button type="button" class="btn btn-danger mtop10 mbot10 btn-view hide"><?= lang('tnh_preview') ?></button>
				</div>
				<div class="show-table-productions-plan">
				</div>
			</div>
		</div>

		<input type="hidden" name="p_id_post" class="form-control" value="<?= !empty($_POST['p_id']) ? $_POST['p_id'] : '' ?>">
		<input type="hidden" name="cs_id_post" class="form-control" value="<?= !empty($_POST['cs_id']) ? $_POST['cs_id'] : '' ?>">
		<input type="hidden" name="arrObjecOrderstId_post" class="form-control" value="<?= !empty($_POST['arrObjecOrderstId']) ? $_POST['arrObjecOrderstId'] : '' ?>">
		<input type="hidden" name="arrObjecBusinesstId_post" class="form-control" value="<?= !empty($_POST['arrObjecBusinesstId']) ? $_POST['arrObjecBusinesstId'] : '' ?>">
		
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add-p">
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

<link rel="stylesheet" type="text/css" href="<?= css('daterangepicker.css') ?>" />
<script type="text/javascript">
	var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";

	function collapseOrdersBusiness(_this) {
        if ($(_this).hasClass('collapsed')) {
            $(_this).removeClass('collapsed');
			$('.hide-begin').show("slow");
        } else {
            $(_this).addClass('collapsed');
			$('.hide-begin').slideUp();
        }
	}

	function loadOrdersAndBusiness()
	{
		cOrders = 0;
		cBusiness = 0;

		if($('#options1').prop('checked')) {
			cOrders = 1;
			cBusiness = 0;
		}

		if($('#options2').prop('checked')) {
			cBusiness = 1;
			cOrders = 0;
		}

		planningCycle = $('#planning_cycle').val();
		$('#orders').val('');
		$('#business_plan').val('');
		$('#orders_items').val('');
		$('#business_plans_items').val('');

		ajaxSelectMultipleParams('#orders', 'admin/manufactures/searchOrdersOfPlan', 0, {'cOrders': cOrders, 'planningCycle': planningCycle});
		ajaxSelectMultipleParams('#business_plan', 'admin/manufactures/searchBusinessOfPlan', 0, {'cBusiness': cBusiness, 'planningCycle': planningCycle});
	}

	function loadOrdersItemsAndBusinessItems(object_id, type_object)
	{
		planningCycle = $('#planning_cycle').val();
		if (type_object == "orders") {
			$('#orders_items').val('');
			ajaxSelectMultipleParams('#orders_items', 'admin/manufactures/searchOrdersItemsOfPlanItems', 0, {'object_id': object_id, 'type_object': type_object, 'planningCycle': planningCycle});
		} else if (type_object == "business_plan") {
			$('#business_plans_items').val('');
			ajaxSelectMultipleParams('#business_plans_items', 'admin/manufactures/searchOrdersItemsOfPlanItems', 0, {'object_id': object_id, 'type_object': type_object, 'planningCycle': planningCycle});
		}
	}

	$(document).ready(function() {
		// init_editor('textarea[name="note"]');
		$('#id_branch').select2();
		$(document).on('change', '#options1', function(event) {
			if ($('#options1').prop('checked')) 
			{
				$('#options2').prop('checked', false);
			}
			loadOrdersAndBusiness();
			$('.btn-view').click();
		});

		$(document).on('change', '#options2', function(event) {
			if ($('#options2').prop('checked')) 
			{
				$('#options1').prop('checked', false);
			}
			loadOrdersAndBusiness();
			$('.btn-view').click();
		});

		$(document).on('change', '#planning_cycle, #safe_inventory', function(event) {
			event.preventDefault();
			loadOrdersAndBusiness();
			$('.btn-view').click();
		});

		$(document).on('change', '#orders', function(event) {
			event.preventDefault();
			arr_orders_id = $(this).val();
			loadOrdersItemsAndBusinessItems(arr_orders_id, 'orders');
			$('.btn-view').click();
		});

		$(document).on('change', '#business_plan', function(event) {
			event.preventDefault();
			arr_business_plan_id = $(this).val();
			loadOrdersItemsAndBusinessItems(arr_business_plan_id, 'business_plan');
			$('.btn-view').click();
		});

		$(document).on('change', '#orders_items, #business_plans_items', function(event) {
			$('.btn-view').click();
		});

		$(document).on('click', '.btn-view', function(e) {
			e.preventDefault();
			form = $('#add-productions-plan');
			data = form.serialize();
			$.ajax({
				url: site.base_url+'admin/manufactures/show_table_productions_plan_new',
				type: 'POST',
				dataType: 'html',
				data: data,
			})
			.done(function(response) {
				$('.show-table-productions-plan').html(response);
			})
			.fail(function() {
				console.log("error");
			});
			
			return;
			bootbox.confirm({
                message: '<?= lang('tnh_you_want_to_view') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                	if (result) {
                		form = $('#add-productions-plan');
	                	data = form.serialize();
	                	$.ajax({
	                		url: site.base_url+'admin/manufactures/show_table_productions_plan_new',
	                		type: 'GET',
	                		dataType: 'html',
	                		data: data,
	                	})
	                	.done(function(response) {
	                		$('.show-table-productions-plan').html(response);
	                	})
	                	.fail(function() {
	                		console.log("error");
	                	});
                	}
                }
            });
		});

		$(document).on('click', '.referesh-reference', function(event) {
			event.preventDefault();
			$.ajax({
				url: site.base_url+'admin/manufactures/refereshReference',
				type: 'GET',
				dataType: 'JSON',
				data: {
					token: hash,
					'referesh': 1
				},
			})
			.done(function(data) {
				if (data) {
					$('#reference_no').val(data.reference_no);
					alert_float('success', data.message);
				} else {
					alert_float('danger', '<?= lang('tnh_referesh_error') ?>');
				}
			})
			.fail(function() {
				console.log("error");
			});
		});

		start_date_post = '';
		end_date_post = '';
		<?php if(!empty($_POST['start_date'])): ?>
			var start_date_post = '<?= $_POST['start_date'] ?>';
			var end_date_post = '<?= $_POST['end_date'] ?>';
			// $('.btn-view').click();
		<?php endif; ?>
		dateRangerCustom('.dateranger-cs', start_date_post, end_date_post);

		appValidateForm($('#add-productions-plan'), {
			reference_no: 'required',
           	date: 'required',
           	planning_cycle: 'required',
			id_branch: 'required'
        }, add);

        function add(form) {
			var tb = '#table-plan tbody tr:not("[class^=not-tr]")';
			var n = $(tb).length;
			var countError = 0;
			for (ii = 0; ii < n; ii++)
    		{
				element = $(tb)[ii];
				versions_bom = $(element).find('select.versions').val();
				if (typeof versions_bom === "undefined" || versions_bom == '' || versions_bom == null) {
					countError++;
					$(element).find('.show-erros-versions').html('<?= lang('Chọn BOM') ?>');
				} else {
					$(element).find('.show-erros-versions').html('');
				}
			}
			if (countError) {
				alert_float('danger', "<?= lang('Lưu ko thành công, vui lòng xem lại dữ liệu') ?>");
				return;
			}
        	$('.add-p').attr('disabled', 'disabled');
            // var data = $(form).serialize();
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });
            //
            var url = form.action;
            $.ajax({
            	url : site.base_url+'admin/manufactures/add_productions_plan',
            	type : 'POST',
            	dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
            	data: formData,
            })
            .done(function(data) {
            	if (data.result) {
					if (data?.po_id) {
						alert_float('success', data.message);
						new_task(site.base_url+'admin/tasks/task?po_id='+data?.po_id);
					} else {
						alert_float('success', data.message);
            			window.location.href = site.base_url+'admin/manufactures/productions_plan';
					}
            	} else {
            		alert_float('danger', data.message);
            		$('.add-p').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
                alert_float('danger', 'error');
            	$('.add-p').removeAttr('disabled', 'disabled');
            });
            return false;
        }
	});
</script>
<script type="text/javascript">
    var json = {};
    var vProduct = 1;
</script>
<script type="text/javascript" src="<?= js('design_bom.js?vs=1.9') ?>"></script>
<script>
	$(document).on('hidden.bs.modal', '#_task_modal', function () {
		window.location.href = site.base_url+'admin/manufactures/productions_plan';
	});
</script>