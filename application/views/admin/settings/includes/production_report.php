<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css" rel="stylesheet" />
<div role="tabpanel" class="tab-pane" id="production_report">
	<table class="table-bordered table" id="table_production_report">
		<thead>
			<tr>
				<th>Quy trình</th>
				<th>Vị trí duyệt quy trình</th>
				<th>Tiêu chí kiểm</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($config_production_report)) { ?>
				<?php foreach ($config_production_report as $key => $v) {
					// if ($v['id'] == 1 || $v['id'] == 2) {
					// 	continue;
					// }
				?>
					<tr>
						<td class="text-left">
							<?= $v['name'] ?>
						</td>
						<td>
							<select id="role_id" class="selectpicker" name="role_id[<?= $v['id'] ?>]" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
								<option></option>
								<?php if (!empty($data_roles)) {
									foreach ($data_roles as $key => $value) { ?>
										<option data-subtext="<?= $value['code_role'] ?>" <?= ($v['id_role'] == $value['roleid'] ? 'selected' : '') ?> value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
								<?php }
								} ?>
							</select>
						</td>
						<td>
							<?php echo render_select('inspection_criteria[' . $v['id'] . '][]', (!empty($data_inspection_criteria) ? $data_inspection_criteria : []), ['id', 'name'], '', $v['setting_production_report_inspection_criteria'], ['multiple' => true], [], '', '', false) ?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>
<script>
</script>