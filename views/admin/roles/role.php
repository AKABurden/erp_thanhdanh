<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-7">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="no-margin">
							<?php echo $title; ?>
						</h4>
						<hr class="hr-panel-heading" />
						<?php if (isset($role)) { ?>
							<a href="<?php echo admin_url('roles/role'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_role'); ?></a>
							<div class="clearfix"></div>
						<?php } ?>
						<?php echo form_open($this->uri->uri_string()); ?>
						<?php if (isset($role)) { ?>
							<?php if (total_rows(db_prefix() . 'staff', array('role' => $role->roleid)) > 0) { ?>
								<div class="alert alert-warning bold">
									<?php echo _l('change_role_permission_warning'); ?>
									<div class="checkbox">
										<input type="checkbox" name="update_staff_permissions" id="update_staff_permissions">
										<label for="update_staff_permissions"><?php echo _l('role_update_staff_permissions'); ?></label>
									</div>
								</div>
							<?php } ?>
						<?php } ?>

						<?php $attrs = (isset($role) ? array() : array('autofocus' => true)); ?>
						<?php $value = (isset($role) ? $role->departments_id : ''); ?>
						<?php echo render_select('departments_id', $departments, array('departmentid', 'name'), 'departments', $value); ?>
						<?php $value = (isset($role) ? $role->name : ''); ?>
						<?php echo render_input('name', 'role_add_edit_name', $value, 'text', $attrs); ?>

						<?php $value = (isset($role) && $role->roles_parent > 0 ? $role->roles_parent : ''); ?>
						<?php echo render_select('roles_parent', $roles_parent, array('roleid', 'name'), 'parent_role', $value); ?>
						
						<div>
							<div class="form-group">
								<?php $coefficient = (isset($role) && $role->coefficient > 0 ? $role->coefficient : 0); ?>
								<?= lang('tnh_coefficient', 'coefficient') ?>
								<input type="text" name="coefficient" class="form-control coefficient number-format" value="<?= $coefficient ?>">
							</div>
						</div>
						<div>
							<div class="form-group">
								<?= lang('tnh_capacity_ratio', 'capacity_ratio') ?>
								<table id="tb-capacity" class="table table-hover" style="margin: auto;">
									<thead>
										<tr>
											<th class="text-center" style="width: 200px;"><?= lang('name') ?></th>
											<th class="text-center" style="width: 100px;"><?= lang('tnh_point_weight') ?></th>
											<th class="text-center" style="width: 100px;"><?= lang('tnh_point_standard') ?></th>
											<th class="text-center" style="width: 100px;"><?= lang('tnh_point_evaluate') ?></th>
											<th class="text-center" style="width: 100px;"><?= lang('tnh_total_point_standard') ?></th>
											<th class="text-center" style="width: 100px;"><?= lang('tnh_total_point_evaluate') ?></th>
										</tr>
									</thead>
									<tbody>
										<tr style="background: #ffeabc87;" class="bold">
											<td colspan="99" class="text-center">
												<?= lang('tnh_vocational_capacity') ?>
											</td>
										</tr>
										<tr class="tr-child-1-1">
											<td>
												<a href="javascript:void(0)" onclick="addPlusCapacity(1, 1)">
													<span class="fa fa-plus"></span>
												</a>
												<?= lang('tnh_knowledge') ?>
											</td>
											<td>

											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="tr-child-1-2">
											<td>
												<a href="javascript:void(0)" onclick="addPlusCapacity(1, 2)">
													<span class="fa fa-plus"></span>
												</a>
												<?= lang('tnh_skill') ?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="tr-child-1-3">
											<td>
												<a href="javascript:void(0)" onclick="addPlusCapacity(1, 3)">
													<span class="fa fa-plus"></span>
												</a>
												<?= lang('tnh_quality') ?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr style="background: #ffeabc87;" class="bold">
											<td colspan="99" class="text-center">
												<?= lang('tnh_management_capacity') ?>
											</td>
										</tr>
										<tr class="tr-child-2-1">
											<td>
												<a href="javascript:void(0)" onclick="addPlusCapacity(2, 1)">
													<span class="fa fa-plus"></span>
												</a>
												<?= lang('tnh_management') ?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
									<tfoot>
										<tr class="bold">
											<td colspan="4"><?= lang('tnh_total_point') ?></td>
											<td class="grand_total_point_standard text-center"></td>
											<td class="grand_total_point_evaluate text-center"></td>
										</tr>
										<tr class="bold">
											<td colspan="4"><?= lang('Tỷ lệ NL = Tổng điểm thực tế / Tổng điểm tiêu chuẩn') ?></td>
											<td></td>
											<td class="grand_total_ratio text-center"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
						<hr>
						<?php
						if (isset($arr_parent)) {
							$data_permission['role'] = $arr_parent;
							$data_permission['roleid'] = $role->roleid;
						} else {
							$data_permission['role'] = array();
						}
						$this->load->view('admin/staff/permissions', $data_permission);
						?>
						<hr />
						<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
			<?php if (isset($role_staff)) { ?>
				<div class="col-md-5">
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo _l('staff_which_are_using_role'); ?>
							</h4>
							<hr class="hr-panel-heading" />
							<div class="table-responsive">
								<table class="table dt-table">
									<thead>
										<tr>
											<th><?php echo _l('staff_dt_name'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($role_staff as $staff) { ?>
											<tr>
												<td>
													<?php
													echo '<a href="' . admin_url('staff/profile/' . $staff['staffid']) . '">' . staff_profile_image($staff['staffid'], [
														'staff-profile-image-small',
													]) . '</a>';
													echo ' <a href="' . admin_url('staff/member/' . $staff['staffid']) . '">' . $staff['firstname'] . ' ' . $staff['lastname'] . '</a>';
													?>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		$(function() {
			appValidateForm($('form'), {
				name: 'required',
				departments_id: 'required'
			});
			show_permission();
		});
	</script>
	</body>

	</html>

	<script>

		function totalCapacity() {
			tb = '#tb-capacity tbody tr.tr-capacity';
			var n = $(tb).length;
			var grand_total_point_standard = 0;
			var grand_total_point_evaluate = 0;

			for (ii = 0; ii < n; ii++)
    		{
				element = $(tb)[ii];
				point_weight = intVal($(element).find('.point_weight').val());
				point_standard = intVal($(element).find('.point_standard').val());
				point_evaluate = intVal($(element).find('.point_evaluate').val());

				point_standard = point_weight * point_standard;
				point_evaluate = point_weight * point_evaluate;
				$(element).find('.td-point-standard').html(tnhFormatNumber(point_standard));
				$(element).find('.td-point-evaluate').html(tnhFormatNumber(point_evaluate));

				grand_total_point_standard+= point_standard;
				grand_total_point_evaluate+= point_evaluate;
			}

			$('.grand_total_point_standard').html(tnhFormatNumber(grand_total_point_standard));
			$('.grand_total_point_evaluate').html(tnhFormatNumber(grand_total_point_evaluate));
			grand_total_ratio = 0;
			if (grand_total_point_standard > 0) {
				grand_total_ratio = grand_total_point_evaluate/grand_total_point_standard;
			}
			$('.grand_total_ratio').html(tnhFormatNumber(grand_total_ratio));
		}

		function removeCapacity(_this) {
			$(_this).closest('tr').remove();
		}

		function addPlusCapacity(capacity, type) {

			tdName = `<td style="display: flex; align-items: center;">
				<a href="javascript:void(0)" onclick="removeCapacity(this)" class="text-danger" style="margin-right: 5px;"><span class="fa fa-remove"></span></a>
				<input type="hidden" name="capacity[]" class="form-control capacity" value="${capacity}">
				<input type="hidden" name="type[]" class="form-control type" value="${type}">
				<input type="text" name="name[]" placeholder="<?= lang('name') ?>" class="form-control" value="">
			</td>`;
			tdPointWeight = `<td>
				<input type="text" name="point_weight[]" onchange="totalCapacity()" class="form-control point_weight number-format" value="0">
			</td>`;
			tdPointStandard = `<td>
				<input type="text" name="point_standard[]" onchange="totalCapacity()" class="form-control point_standard number-format" value="0">
			</td>`;
			tdPointEvaluate = `<td>
				<input type="text" name="point_evaluate[]" onchange="totalCapacity()" class="form-control point_evaluate number-format" value="0">
			</td>`;

			tdTotalPointStandard = `<td class="td-point-standard" style="vertical-align: middle; text-align: center;">0</td>`;
			tdTotalPointEvaluate = `<td class="td-point-evaluate" style="vertical-align: middle; text-align: center;">0</td>`;

			trHtmlCapacity = `<tr class="tr-capacity">
				${tdName}
				${tdPointWeight}
				${tdPointStandard}
				${tdPointEvaluate}
				${tdTotalPointStandard}
				${tdTotalPointEvaluate}
			</tr>`;
			$('.tr-child-'+capacity+'-'+type).after(trHtmlCapacity);
		}
	</script>