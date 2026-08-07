<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/costing/add_costing', array('id'=>'add-costing')); ?>
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
						<h3 class="panel-title"><?= lang('tnh_period_of_cost_calculation') ?></h3>
					</div>
					<div class="panel-body">
						<table class="tnh-tb table-bordered table-hover">
							<tbody>
								<tr>
									<td style="width: 10%;">
										<?= lang('start_date', 'start_date') ?>
									</td>
									<td style="width: 40%;">
										<div class="form-group">
											<?= form_input('start_date', set_value('start_date') ? set_value('start_date') : date('d/m/Y'), 'id="start_date" class="form-control datepicker" placeholder="'.lang('tnh_start_date').'" required ') ?>
										</div>
									</td>
									<td style="width: 10%;"><?= lang('end_date', 'end_date') ?></td>
									<td style="width: 40%;">
										<div class="form-group">
											<?= form_input('end_date', set_value('end_date') ? set_value('end_date') : date('d/m/Y'), 'id="end_date" class="form-control datepicker" placeholder="'.lang('tnh_end_date').'" required ') ?>
										</div>
									</td>
								</tr>
								<tr>
									<td><?= lang('name', 'name') ?></td>
									<td colspan="3">
										<div class="form-group">
											<input type="text" name="name" id="name" placeholder="<?= lang('name') ?>" class="form-control" value="" title="" required>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<button type="button" onclick="loadData(this)" class="btn btn-primary"><?= lang('tnh_load_data') ?></button>
										<button type="button" onclick="refereshData(this)" class="btn btn-danger"><?= lang('tnh_referesh') ?></button>
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
						<h3 class="panel-title"><?= lang('info') ?></h3>
					</div>
					<div class="panel-body">
						<div class="tb-height info-costing">
						</div>
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
	var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 0;
	var counter = 0;
	var count_errors = 0;
</script>

<script type="text/javascript">

	function refereshData()
	{
		$('.info-costing').html('');
		$('#start_date').removeAttr('readonly');
		$('#end_date').removeAttr('readonly');
	}

	function loadData(el) {
		var cData = $('#add-costing').serialize();
		$.ajax({
			url: site.base_url+'admin/costing/showInfoCosting',
			type: 'POST',
			dataType: 'html',
			data: cData
		})
		.done(function(data) {
			$('.info-costing').html(data);
			$('#start_date').attr('readonly', 'true');
			$('#end_date').attr('readonly', 'true');
		})
		.fail(function() {
			console.log("error");
		});
	}

	$(document).ready(function() {

		$(document).on('click', '#tb-cs_wrapper .btn-dt-reload', function(event){
			oTableModal.draw();
		})

		appValidateForm($('#add-costing'), {
			start_date: 'required',
	        end_date: 'required',
	        name: 'required',
	    }, db);

	    function db(form) {
	        // if (count_errors > 0) {
	        //     alert_float('danger', lang_core['check_date_enter']);
	        //     return;
	        // }
	    	$('.add').attr('disabled', 'disabled');
	        var url = form.action;
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

	        $.ajax({
	        	url : url,
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
	        		// $('.add').removeAttr('disabled', 'disabled');
	        		window.location.href = site.base_url+'admin/costing';
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
