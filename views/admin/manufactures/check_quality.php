<?php echo form_open('admin/manufactures/checkQuality/'.$id, array('id'=>'check_quality')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('check_quality') ?> (<?= $pod['reference_no'] ?>)</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('date', 'date') ?>
						<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
					</div>
				</div>
				<!-- <div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_reference_qc', 'reference_no') ?>
						<div class="form-group">
							<div class="input-group">
								<span title="<?= lang('tnh_referesh') ?>" data-toggle="tooltip" class="input-group-addon btn btn-danger referesh-reference">
									<i class="fa fa-undo"></i>
								</span>
								<input type="text" name="reference_no" class="form-control" required id="reference_no" value="<?= $reference_no ?>" aria-invalid="false">
							</div>
						</div>
					</div>
				</div> -->
				<div class="col-md-4">
					<?= lang('tnh_employee_checks', 'employee') ?>
					<div class="form-group">
						<select name="employee" id="employee" data-placeholder="<?= lang('tnh_employee_checks') ?>" style="width: 100%;" class="modal-select2" required="required">
							<option value=""></option>
							<?php foreach ($employees as $key => $value): ?>
								<option value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_total_quantity', 'total_quantity') ?>
						<input type="text" name="" id="input" class="form-control" value="<?= $pod['quantity_finished'] ?>" readonly>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_quantity_qc', 'quantity_qc') ?>
						<input type="number" name="quantity_qc" id="quantity_qc" class="form-control" value="0">
					</div>
				</div>
				<div class="col-md-4">
					<?= lang('status', 'status') ?>
					<div class="form-group">
						<select name="status" id="status" data-placeholder="<?= lang('status') ?>" style="width: 100%;" class="modal-select2" >
							<?php foreach (typeCheckQuality() as $key => $value): ?>
								<option value="<?= $key ?>"><?= $value ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('note', 'note') ?>
						<textarea name="note" id="note" placeholder="<?= lang('note') ?>" class="form-control note" rows="2"></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tb-quality" class="tnh-table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
									<th style="width: 120px;"><?= lang('tnh_product_code') ?></th>
									<th style="width: 120px;"><?= lang('tnh_product_name') ?></th>
									<th style="width: 100px;"><?= lang('category_checks') ?></th>
									<th style="width: 100px;"><?= lang('category_results') ?></th>
									<th style="width: 100px;"><?= lang('category_errors') ?></th>
									<th style="width: 100px;"><?= lang('tnh_detail_errors') ?></th>
									<th style="width: 120px;"><?= lang('category_cause_errors') ?></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="save" id="" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	categoryChecks = <?= !empty($categoryChecks) ? json_encode($categoryChecks) : '{}' ?>;
	categoryErrors = <?= !empty($categoryErrors) ? json_encode($categoryErrors) : '{}' ?>;
	categoryResults = <?= !empty($categoryResults) ? json_encode($categoryResults) : '{}' ?>;
	categoryCauseErrors = <?= !empty($categoryCauseErrors) ? json_encode($categoryCauseErrors) : '{}' ?>;

	function getCategoryChecks()
	{
		var option = '<option value=""></option>';
		$.each(categoryChecks, function(index, el) {
			option+= '<option value="'+el.id+'">'+el.name+'</option>';
		});
		return option;
	}

	function getCategoryErrors()
	{
		var option = '<option value=""></option>';
		$.each(categoryErrors, function(index, el) {
			option+= '<option value="'+el.id+'">'+el.name+'</option>';
		});
		return option;
	}

	function getCategoryResults()
	{
		var option = '<option value=""></option>';
		$.each(categoryResults, function(index, el) {
			option+= '<option value="'+el.id+'">'+el.name+'</option>';
		});
		return option;
	}

	function getCategoryCauseErrors()
	{
		var option = '<option value=""></option>';
		$.each(categoryCauseErrors, function(index, el) {
			option+= '<option value="'+el.id+'">'+el.name+'</option>';
		});
		return option;
	}

	function changeCategoryErrors(el)
	{
		var rowCurrent = $(el).closest('tr');
		var category_error_id = $(el).val();
		var detailErrors = rowCurrent.find('#detail_error');
		ajaxSelectParamsCallback(detailErrors, 'admin/quality_control/searchDetailErrors', 0, {'category_error_id': category_error_id});
		$(detailErrors).val('');
	}

	function addTr(st)
	{
		tdNumber = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+st+'">'+
		'<div class="stt text-center">'+st+'</div>';
		tdCode = '<div class="td-item-code" style="width: 120px;"><?= $pod['items_code'] ?></div>';
		tdName = '<div class="td-item-name" style="width: 120px;"><?= $pod['items_name'] ?></div>';
		tdCategoryCheck = '<div class="td-category-check">'+
			'<select name="category_checks['+st+']" data-placeholder="<?= lang('category_checks') ?>" id="category_checks" class="category_checks modal-select2" style="width: 100%;">'+
				getCategoryChecks()+
			'</select>'+
		'</div>';
		tdCategoryResult = '<div class="td-category-result">'+
			'<select name="category_results['+st+']" data-placeholder="<?= lang('category_results') ?>" id="category_results" class="category_results modal-select2" style="width: 100%;">'+
				getCategoryResults()+
			'</select>'+
		'</div>';
		tdCategoryError = '<div class="td-category-error">'+
			'<select name="category_errors['+st+']" onchange="changeCategoryErrors(this)" data-placeholder="<?= lang('category_errors') ?>" id="category_errors" class="category_errors modal-select2" style="width: 100%;">'+
				getCategoryErrors()+
			'</select>'+
		'</div>';
		tdDetailError = '<div class="td-detail-error">'+
			'<input type="text" name="detail_error['+st+']" id="detail_error" data-placeholder="<?= lang('tnh_detail_errors') ?>" class="modal-select2 detail_error" style="width: 100%;" value="">'+
		'</div>';
		tdCategoryCauseErrors = '<div class="td-category-cause-errors">'+
			'<select name="category_cause_errors['+st+']" data-placeholder="<?= lang('category_cause_errors') ?>" id="category_cause_errors" class="category_cause_errors modal-select2" style="width: 100%;">'+
				getCategoryCauseErrors()+
			'</select>'+
		'</div>';

		tr = '<tr>'+
			'<td>'+tdNumber+'</td>'+
			'<td>'+tdCode+'</td>'+
			'<td>'+tdName+'</td>'+
			'<td>'+tdCategoryCheck+'</td>'+
			'<td>'+tdCategoryResult+'</td>'+
			'<td>'+tdCategoryError+'</td>'+
			'<td>'+tdDetailError+'</td>'+
			'<td>'+tdCategoryCauseErrors+'</td>'+
		'</tr>';

		return tr;
	}

	$(document).ready(function() {
		$('#status').select2();
		$('#employee').select2();

		$('#quantity_qc').change(function(event) {
			quantity_qc = intVal($(this).val());
			$('#tb-quality tbody').html('');
			var st = 1;
			for (var i = 0; i < quantity_qc; i++) {
				$('#tb-quality tbody').append(addTr(st));
				st++;
			}
			$('select.category_results').select2({allowClear: true});
			$('select.category_checks').select2({allowClear: true});
			$('select.category_errors').select2({allowClear: true});
			$('select.category_cause_errors').select2({allowClear: true});
		});

		$('.referesh-reference').click(function(event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url+'admin/manufactures/refereshCheckQuality',
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
                    alert_float('danger', 'fail');
                }
            })
            .fail(function() {
                console.log("error");
            });
        });

	    appValidateForm($('#check_quality'), {
	        date: 'required',
	        reference_no: 'required',
	       	employee: 'required',
	    }, db);

	    function db(form) {
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
	        		if (typeof oTable != 'undefined') {
            			oTable.draw();
            		}
	        		$('.modal-dialog .close').trigger('click');
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