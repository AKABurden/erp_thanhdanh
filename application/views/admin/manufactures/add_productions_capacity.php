<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/manufactures/add_productions_capacity', array('id'=>'add-productions-capacity')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        	<?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
	<div class="content">
		<div class="row">
			<div class="panel panel-primary" style="margin-bottom: 50px;">
				<div class="panel-heading">
					<h3 class="panel-title"><?= lang('info') ?></h3>
				</div>
				<div class="panel-body">
					<table class="tnh-tb table-bordered table-hover">
						<tbody>
							<tr>
								<td>
									<?= lang('tnh_reference_productions_plan', 'reference_no') ?>
								</td>
								<td>
									<div class="form-group">
										<div class="input-group">
											<span title="<?= lang('tnh_referesh') ?>" data-toggle="tooltip" class="input-group-addon btn btn-danger referesh-reference">
												<i class="fa fa-undo"></i>
											</span>
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $reference_no ?>" readonly="" aria-invalid="false">
										</div>
									</div>
								</td>
								<td><?= lang('date', 'date') ?></td>
								<td>
									<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= lang('productions_plan', 'productions_plan') ?>
								</td>
								<td colspan="3">
									<?php if (!empty($plan)): ?>
										<input type="hidden" name="productions_plan_id[]" id="input" class="form-control" value="<?= $plan['id'] ?>">
										<?= $plan['reference_no'] ?>
										<script type="text/javascript">
											$(document).ready(function() {
												view([<?= $plan['id'] ?>]);
											});
										</script>
									<?php else: ?>
										<select name="productions_plan_id[]" id="productions_plan" class="form-control productions_plan_id ajax-search" data-actions-box="true" data-none-selected-text="<?= lang('choose') ?>" required="required" data-live-search="true">
										</select>
									<?php endif ?>
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
					<div class="show-table-productions-capacity" style="margin-bottom: 20px; margin-top: 10px;">
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="" class="form-control" value="1">
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


<script type="text/javascript">
	var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var arr_productions_plan_id = [];

	function view(arr_productions_plan_id) {
		$.ajax({
			url: site.base_url+'admin/manufactures/viewTableProductionsCapacity',
			type: 'POST',
			dataType: 'html',
			data: {
				"<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
				arr_productions_plan_id: arr_productions_plan_id,
			},
		})
		.done(function(response) {
			$('.show-table-productions-capacity').html(response);
		})
		.fail(function() {
			console.log("error");
		});
	}

	$(document).ready(function() {
		init_editor('textarea[name="note"]');
		selectAjax('#productions_plan', false, 'admin/manufactures/searchProductionsPlan', false, true);

		$(document).on('change', '#productions_plan', function(event) {
			arr_productions_plan = $(this).val();
			if (arr_productions_plan.length > 0 || arr_productions_plan_id.length > 0) {
				if (!arraysEqual(arr_productions_plan_id, arr_productions_plan)) {
					arr_productions_plan_id = arr_productions_plan;
					view(arr_productions_plan_id);
				}
			}
		});

		$(document).on('click', '.referesh-reference', function(event) {
			event.preventDefault();
			$.ajax({
				url: site.base_url+'admin/manufactures/refereshReferenceProductionsCapacity',
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

		appValidateForm($('#add-productions-capacity'), {
			reference_no: 'required',
           	date: 'required',
           	'productions_plan': 'required',
        }, add);

        function add(form) {
        	$('.add').attr('disabled', 'disabled');
            tinymce.get('note').save();
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
            	url : site.base_url+'admin/manufactures/add_productions_capacity',
            	type : 'POST',
            	dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
            	data: formData,
            })
            .done(function(data) {
            	if (data.result) {
            		alert_float('success', data.message);
            		$('.add').removeAttr('disabled', 'disabled');
            		window.location.href = site.base_url+'admin/manufactures/productions_capacity';
            	} else {
            		alert_float('danger', data.message);
            		$('.add').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
                alert_float('danger', 'error');
            	$('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
	});
</script>