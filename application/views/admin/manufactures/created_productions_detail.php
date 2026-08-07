<?php echo form_open('admin/manufactures/created_productions_detail/'.$id, array('id'=>'created-detail')); ?>
<div class="modal-dialog modal-lg" style="width: 90%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">
				<?= lang('created_productions_detail') ?>
			</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
                	<div class="form-group">
                		<div class="checkbox checkbox-primary">
                        	<input type="checkbox" name="task" id="task" value="1">
                        	<label for="task"><?= lang('task') ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                        	<input type="checkbox" name="email" id="email" value="1">
                        	<label for="email"><?= lang('email') ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                        	<input type="checkbox" name="zalo" id="zalo" value="1">
                        	<label for="zalo"><?= lang('Zalo') ?></label>
                        </div>
                	</div>
                </div>
				<div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_status', 'status') ?>
                        <?= $productions_orders['status'] == 'un_approved' ? '<span class="label label-danger">'.lang($productions_orders['status']).'</span>' : '<span class="label label-success">'.lang($productions_orders['status']).'</span>' ?>
                    </div>
                </div>
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="tnh-table table-hover table-bordered dataTable">
							<thead>
								<tr>
									<th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
									<th><?= lang('code') ?></th>
									<th><?= lang('name') ?></th>
									<th class="text-center"><?= lang('quantity') ?></th>
									<th style="width: 180px;">
										<?= lang('tnh_deadline') ?><span style="color:#fc2d6b;">*</span>
									</th>
									<th style="width: 180px;">
										<?= lang('departments') ?><span style="color:#fc2d6b;">*</span>
									</th>
									<th style="width: 400px;">
										<?= lang('staff_admin') ?><span style="color:#fc2d6b;">*</span>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php $index = 0; ?>
								<?php foreach ($items as $key => $value): ?>
									<tr id="primary-<?= $value['id'] ?>">
										<td class="text-center">
											<span class="btn btn-danger" data-toggle="collapse" data-target="#demo-<?= $value['id'] ?>"><?= (++$key) ?></span>
										</td>
										<td><?= $value['items_code'] ?></td>
										<td><?= $value['items_name'] ?></td>
										<td class="text-center"><?= formatNumber($value['quantity']) ?></td>
										<td>
											<input type="text" name="deadline[<?= $index ?>]" placeholder="<?= lang('tnh_deadline') ?>" id="deadline[<?= $index ?>]" autocomplete="off" required class="form-control deadline datepicker" value="" >
										</td>
										<td>
											<select name="departments[<?= $index ?>][]" data-placeholder="<?= lang('departments') ?>" id="departments[<?= $index ?>]" autocomplete="off" multiple class="departments modal-select2" required  style="width: 100%;">
												<option value=""></option>
												<?php foreach ($departments as $k => $val): ?>
													<option value="<?= $val['departmentid'] ?>"><?= $val['name'] ?></option>
												<?php endforeach ?>
											</select>
										</td>
										<td>
											<select name="employess[<?= $index ?>][]" data-placeholder="<?= lang('staff_admin') ?>" id="employess[<?= $index ?>]" autocomplete="off" class="employess modal-select2" multiple required="required" style="width: 100%;">
												<option value=""></option>
											</select>
										</td>
									</tr>
									<tr id="demo-<?= $value['id'] ?>" class="collapse-in">
										<td colspan="7">
											<div class="btn btn-primary" onclick="createdTaks(<?= $value['id'] ?>, <?= $value['quantity'] ?>)">Phân công</div>
											<br></br>
											<input type="hidden" name="phan_cong[<?= $index ?>]" id="phan_cong<?= $value['id'] ?>" class="form-control" value="">
											<table class="table-task table tnh-table dataTable">
												<tr class="bg-primary">
													<td style="width: 180px;"><?= lang('tnh_shift_work') ?></td>
													<td style="width: 180px;"><?= lang('quantity') ?></td>
													<td><?= lang('staff_admin') ?></td>
													<td style="width: 80px;"><?= lang('actions') ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<?php $index++; ?>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="save" id="save" class="form-control" value="1">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">

	var shiftWork = <?= json_encode($shiftWork) ?>;
	var staffsPod = <?= json_encode($staffs) ?>;
	var counterShiftWork = 0;

	function loadShiftWork()
	{
		var option = '<option value=""></option>';
		$.each(shiftWork, function(index, el) {
			option+= '<option value="'+el.id+'">'+el.name+'</option>';
		});
		return option;
	}

	function loadStaffPod()
	{
		var option = '<option value=""></option>';
		$.each(staffsPod, function(index, el) {
			option+= '<option value="'+el.staffid+'">'+el.firstname+' '+el.lastname+'</option>';
		});
		return option;
	}

	function createdTaks(pod_item_id, quantity_pod_item)
	{
		tdShiftWork = '<td><select name="shift_work['+pod_item_id+'][]" class="modal-select2 shift_work" data-placeholder="<?= lang('tnh_shift_work') ?>" style="width: 100%;">'+loadShiftWork()+'</select></td>';
		tdQuantity = '<td><input type="hidden" name="counterShiftWork['+pod_item_id+'][]" value="'+counterShiftWork+'"><input type="text" name="quantity_shift_work['+pod_item_id+'][]"  class="form-control quantity_shift_work" value="0"></td>';
		tdStaff = '<td><select name="staff_shift_work['+counterShiftWork+'][]" class="modal-select2 staff_shift_work" data-placeholder="<?= lang('staff_admin') ?>" style="width: 100%;" multiple>'+loadStaffPod()+'</select></td>';
		tdActions = '<td class="text-center"><i class="btn btn-warning fa fa-remove" onclick="removeTask(this, '+pod_item_id+')"></i></td>';

		trTask = '<tr>'+
			tdShiftWork+
			tdQuantity+
			tdStaff+
			tdActions+
		'</tr>'

		$('#demo-'+pod_item_id+' .table-task').append(trTask);

		nTask = $('#demo-'+pod_item_id+' .table-task tbody tr').length;
		if (nTask > 1) {
			$('#phan_cong'+pod_item_id).val(1);
		} else {
			$('#phan_cong'+pod_item_id).val(0);
		}

		$('select.shift_work').select2();
		$('select.staff_shift_work').select2();
		counterShiftWork++;
	}

	function removeTask(_this, pod_item_id)
	{
		$(_this).closest('tr').remove();
		nTask = $('#demo-'+pod_item_id+' .table-task tbody tr').length;
		if (nTask > 1) {
			$('#phan_cong'+pod_item_id).val(1);
		} else {
			$('#phan_cong'+pod_item_id).val('');
		}
	}

	$(document).ready(function() {
		init_datepicker();
		$('select.departments').select2();
		$('select.employess').select2();

		function loadEmployees(employees)
		{
			var option = '<option value=""></option>';
			$.each(employees, function(index, el) {
				option+= '<option value="'+el.staffid+'">'+el.lastname+' '+el.firstname+'</option>';
			});
			return option;
		}

		$('select.departments').change(function(event) {
			trCurrent = $(this).closest('tr');
			departmentId = $(this).val();
			trCurrent.find('select.employess').select2("val", "");
			trCurrent.find('select.employess').html('');
			console.log(departmentId);
			if (departmentId) {
				$.ajax({
					url: site.base_url+'admin/manufactures/getEmployeesByDeparment',
					type: 'GET',
					dataType: 'JSON',
					data: {
						departmentId: departmentId,
						csrf_token_name: hash
					},
				})
				.done(function(data) {
					if (data) {
						trCurrent.find('select.employess').html(loadEmployees(data.employees));
					}
				})
				.fail(function() {
					console.log("error");
				});
			}
		});

		appValidateForm($('#created-detail'), {
		}, convert);

        function convert(form) {
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
            //
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
            		if (typeof oTable != 'undefined' && oTable != '') {
            			oTable.draw('page');
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
<?php echo form_close(); ?>