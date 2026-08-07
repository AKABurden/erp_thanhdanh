<?php init_head(); ?>
<style>
	textarea {
		height: 80px !important;
	}

	table.dataTable tr td {
		vertical-align: top !important;
	}
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/kpi/handling/' . $id, array('id' => 'handling-kpi')); ?>
<?php
$not_reached = get_option('not_reached');
$not_reached_from = get_option('not_reached_from');
$not_reached_to = get_option('not_reached_to');
$need_keep_trying = get_option('need_keep_trying');
$need_keep_trying_from = get_option('need_keep_trying_from');
$need_keep_trying_to = get_option('need_keep_trying_to');
$obtain = get_option('obtain');
$obtain_from = get_option('obtain_from');
$obtain_to = get_option('obtain_to');
$pass = get_option('pass');
$pass_from = get_option('pass_from');
$pass_to = get_option('pass_to');
$total_weight_number = 0;
?>
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
				<table class="tnh-tb table-bordered table-hover">
					<tbody>
						<tr>
							<td style="width: 15%;">
								<?= lang('tnh_reference_no', 'reference_no') ?>
							</td>
							<td style="width: 20%;">
								<div class="form-group">
									<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($kpi) ? $kpi['reference_no'] : lang('auto') ?>" readonly="" aria-invalid="false">
								</div>
							</td>
							<td style="width: 10%;">
								<?= lang('start_date', 'start_date') ?>
							</td>
							<td style="width: 20%;">
								<input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control start_date datepicker" placeholder="<?= lang('start_date') ?>" value="<?= !empty($kpi) ? _d($kpi['start_date']) : date('d/m/Y') ?>" required="required">
							</td>
							<td style="width: 10%;">
								<?= lang('end_date', 'end_date') ?>
							</td>
							<td style="width: 20%;">
								<input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control end_date datepicker" value="<?= !empty($kpi) ? _d($kpi['end_date']) : date('d/m/Y') ?>" placeholder="<?= lang('end_date') ?>" required="required">
							</td>
						</tr>
						<tr>
							<?php
							$dtStaff = [];
							if (!empty($kpi)) {
								$dtStaff = $this->site_model->getStaffByStaffId($kpi['staff']);
							}
							?>
							<td>
								<?//= lang('staff', 'staff') ?>
								<label for="staff"><small class="req text-danger">* </small><?= lang('staff') ?>/<?= lang('department') ?></label>
							</td>
							<td>
								<?php
									$type_kpi = 1;
									if (!empty($kpi)) $type_kpi = $kpi['type_kpi'];
								?>
								<div class="row">
									<div class="col-md-6">
										<div class="radio radio-primary">
											<input type="radio" name="type_kpi" class="type_kpi" id="c_staff" value="1" <?= $type_kpi == 1 ? 'checked="checked"' : '' ?> >
											<label for="c_staff"><?= lang('staff') ?></label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="radio radio-primary">
											<input type="radio" name="type_kpi" class="type_kpi" id="c_department" value="2" <?= $type_kpi == 2 ? 'checked="checked"' : '' ?>>
											<label for="c_department"><?= lang('department') ?></label>
										</div>
									</div>
								</div>
								<div class="div-staff" <?= $type_kpi == 1 ? '' : 'style="display: none;"' ?>>
									<select name="staff" data-placeholder="<?= lang('staff') ?>" id="staff" class="modal-select2" style="width: 100%;">
										<option value=""></option>
										<?php if (!empty($staffs)) : ?>
											<?php foreach ($staffs as $key => $value) : ?>
												<option <?= !empty($kpi)  && $kpi['type_kpi'] == 1 && $kpi['staff'] == $value['staffid'] ? 'selected' : '' ?> data-department="<?= $value['name_department'] ?>" data-role="<?= $value['name_role'] ?>" value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</div>
								<div class="div-department" <?= $type_kpi == 2 ? '' : 'style="display: none;"' ?>>
									<select name="_department" id="department" data-none-selected-text="<?= lang('department') ?>" data-actions-box="true" data-live-search="true" class="form-control selectpicker">
										<option value=""></option>
										<?php if(!empty($departments)): 
										?>
											<?php foreach($departments as $key => $value): ?>
												<option <?= (!empty($kpi) && $kpi['type_kpi'] == 2 && $kpi['staff'] == $value['departmentid'] ? 'selected' : '') ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
											<?php endforeach; ?>
										<?php endif; 
										?>
									</select>
								</div>
							</td>
							<td><?= lang('department', 'department') ?></td>
							<td class="txt-department">
								<?php if (!empty($dtStaff)) : ?>
									<?php echo $dtStaff['name_department']; ?>
								<?php endif; ?>
							</td>
							<td><?= lang('role', 'role') ?></td>
							<td class="txt-role">
								<?php if (!empty($dtStaff)) : ?>
									<?php echo $dtStaff['name_role']; ?>
								<?php endif; ?>
							</td>
						</tr>
						<!-- <tr>
							<td>
								<?//= lang('tnh_target_reception_time', 'target_reception_time') ?>
							</td>
							<td>
								<input type="text" name="target_reception_time" id="target_reception_time" autocomplete="off" class="form-control target_reception_time datepicker" value="<?//= !empty($kpi) ? _d($kpi['target_reception_time']) : '' ?>" placeholder="<?//= lang('tnh_target_reception_time') ?>" required="required">
							</td>
						</tr> -->
					</tbody>
				</table>
			</div>
			<div class="col-md-6 mtop10">
				<?= lang('Tiêu chí KPI', 'criteria_kpi') ?>
				<select name="criteria_kpi" id="criteria_kpi" data-none-selected-text="<?= lang('Tiêu chí KPI') ?>" multiple="true" data-actions-box="true" data-live-search="true" class="form-control criteria_kpi selectpicker">
					<option value=""></option>
				</select>
			</div>
			<div class="col-md-4">
				<a href="javascript:void(0)" class="btn btn-primary" style="margin-top: 38px;" onclick="loadDataItemKpi()"><?= lang('tnh_load_data') ?></a>
				<a href="javascript:void(0)" class="btn btn-danger" style="margin-top: 38px;" onclick="refershDataKpi()"><?= lang('tnh_referesh') ?></a>
			</div>
			<div class="col-md-12 mtop10">
				<div class="bold"><?= lang('tnh_title_kpi_1') ?></div>
				<table id="tb-kpi" class="table table-hover dataTable">
					<thead>
						<tr>
							<th class="text-center" rowspan="2" style="width: 30px;"><?= lang('STT') ?></th>
							<th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tiêu chí') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Đơn vị tính') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Mục tiêu') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('tnh_weight_number') ?></th>
							<th class="text-center" style="width: 110px;">1.<?= lang('tnh_not_reached') ?></th>
							<th class="text-center" style="width: 110px;">2.<?= lang('tnh_need_keep_trying') ?></th>
							<th class="text-center" style="width: 110px;">3.<?= lang('tnh_obtain') ?></th>
							<th class="text-center" style="width: 110px;">4.<?= lang('tnh_pass') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Tổng điểm') ?></th>
							<th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Phương pháp đánh giá') ?></th>
							<th class="text-center" rowspan="2" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
						</tr>
						<tr>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(1 điểm)') ?></th>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(2 điểm)') ?></th>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(3 điểm)') ?></th>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(4 điểm)') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$total_weight_number = 0;
						$counter = 0;
						?>
						<?php if (!empty($kpi)) : ?>
							<?php
							$this->db->select('tbl_kpi_items.*, tbl_kpi_criteria.criteria, tbl_kpi_criteria.unit, tbl_kpi_criteria.note_criteria as note_criteria, tbl_kpi_criteria.id as id_kpi_criteria', false);
							$this->db->from('tbl_kpi_items');
							$this->db->join('tbl_kpi_criteria', 'tbl_kpi_criteria.id = tbl_kpi_items.kpi_criteria_id');
							$this->db->where('tbl_kpi_items.kpi_id', $kpi['id']);
							$this->db->where('tbl_kpi_items.type', 0);
							$this->db->order_by('tbl_kpi_items.id ASC');
							$kpi_items = $this->db->get()->result_array();
							?>
							<?php if (!empty($kpi_items)) : ?>
								<?php foreach ($kpi_items as $key => $value) : ?>
									<tr>
										<td class="text-center td-numbers"><?= ++$key ?></td>
										<td><?= $value['criteria'] ?></td>
										<td class="text-center"><?= $value['unit'] ?></td>
										<td class="text-center">
											<textarea type="text" name="_target[]" class="form-control target" value=""><?= $value['target'] ?></textarea>
											<span class="txt-target hide"><?= $value['target'] ?></span>
											<input type="hidden" name="counter[]" class="form-control" value="<?= $counter ?>">
											<input type="hidden" name="type[]" class="form-control" value="0">
										</td>
										<td class="text-center">
										<input type="text" name="_weight_number[]" class="form-control weight_number" onchange="totalKpi()" value="<?= $value['weight_number'] ?>">
											<span class="txt-weight_number hide"><?= $value['weight_number'] ?></span>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control not_reached" value="<?= $value['not_reached'] ?>">
											<input type="hidden" class="form-control not_reached_from" value="<?= $value['not_reached_from'] ?>">
											<input type="hidden" class="form-control not_reached_to" value="<?= $value['not_reached_to'] ?>">
											<?php
											// echo $value['not_reached'] ? calRecipe($value['not_reached']) : '';
											// if (!empty($value['not_reached'])) {
												// echo $value['not_reached_from'] . ($value['not_reached'] == 4 ? ' - ' . $value['not_reached_to'] : '');
												// echo $value['not_reached_from'];
											// }
											// $strNotReached = '
											// 	<input type="text" name="_not_reached_from[]" class="form-control" value="'.$value['not_reached_from'].'">
											// ';
											$strNotReached = '
												<textarea type="text" name="_not_reached_from[]" class="form-control">'.$value['not_reached_from'].'</textarea>
											';
											echo $strNotReached;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" onchange="totalKpi()" class="chonse_not_reached chonse" <?= $value['chonse'] == 1 ? 'checked' : '' ?> id="chonse_not_reached_<?= $counter ?>" value="1">
												<label for="chonse_not_reached_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control need_keep_trying" value="<?= $value['need_keep_trying'] ?>">
											<input type="hidden" class="form-control need_keep_trying_from" value="<?= $value['need_keep_trying_from'] ?>">
											<input type="hidden" class="form-control need_keep_trying_to" value="<?= $value['need_keep_trying_to'] ?>">
											<?php
											// echo $value['need_keep_trying'] ? calRecipe($value['need_keep_trying']) : '';
											// if (!empty($value['need_keep_trying'])) {
												// echo ' ' . $value['need_keep_trying_from'] . ($value['need_keep_trying'] == 4 ? ' - ' . $value['need_keep_trying_to'] : '');
												// echo ' ' . $value['need_keep_trying_from'];
											// }
											// $strNeedKeepTrying = '
											// 	<input type="text" name="_need_keep_trying_from[]" class="form-control" value="'.$value['need_keep_trying_from'].'">
											// ';
											$strNeedKeepTrying = '
												<textarea type="text" name="_need_keep_trying_from[]" class="form-control" value="">'.$value['need_keep_trying_from'].'</textarea>
											';
											echo $strNeedKeepTrying;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" onchange="totalKpi()" class="chonse_need_keep_trying chonse" <?= $value['chonse'] == 2 ? 'checked' : '' ?> id="chonse_need_keep_trying_<?= $counter ?>" value="2">
												<label for="chonse_need_keep_trying_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control obtain" value="<?= $value['obtain'] ?>">
											<input type="hidden" class="form-control obtain_from" value="<?= $value['obtain_from'] ?>">
											<input type="hidden" class="form-control obtain_to" value="<?= $value['obtain_to'] ?>">
											<?php
											// echo $value['obtain'] ? calRecipe($value['obtain']) : '';
											// if (!empty($value['obtain'])) {
												// echo ' ' . $value['obtain_from'] . ($value['obtain'] == 4 ? ' - ' . $value['obtain_to'] : '');
												// echo ' ' . $value['obtain_from'];
											// }
											// $strObtain = '
											// 	<input type="text" name="_obtain_from[]" class="form-control" value="'.$value['obtain_from'].'">
											// ';
											$strObtain = '
												<textarea type="text" name="_obtain_from[]" class="form-control" value="">'.$value['obtain_from'].'</textarea>
											';
											echo $strObtain;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" <?= $value['chonse'] == 3 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_obtain chonse" id="chonse_obtain_<?= $counter ?>" value="3">
												<label for="chonse_obtain_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control pass" value="<?= $value['pass'] ?>">
											<input type="hidden" class="form-control pass_from" value="<?= $value['pass_from'] ?>">
											<input type="hidden" class="form-control pass_to" value="<?= $value['pass_to'] ?>">
											<?php
											// echo $value['pass'] ? calRecipe($value['pass']) : '';
											// if (!empty($value['pass'])) {
												// echo ' ' . $value['pass_from'] . ($value['pass'] == 4 ? ' - ' . $value['pass_to'] : '');
												// echo ' ' . $value['pass_from'];
											// }
											// $strPass = '
											// 	<input type="text" name="_pass_from[]" class="form-control" value="'.$value['pass_from'].'">
											// ';
											$strPass = '
												<textarea type="text" name="_pass_from[]" class="form-control" value="">'.$value['pass_from'].'</textarea>
											';
											echo $strPass;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" <?= $value['chonse'] == 4 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_pass chonse" id="chonse_pass_<?= $counter ?>" value="4">
												<label for="chonse_pass_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" name="kpi_item_id[]" class="form-control kpi_criteria_id" value="<?= !empty($value) ? $value['id'] : 0 ?>">
											<input type="hidden" name="kpi_criteria_id[]" class="form-control kpi_criteria_id" value="<?= $value['id_kpi_criteria'] ?>">
											<input type="hidden" name="result[]" class="form-control result" onchange="totalKpi()" style="width: 100%;" readonly value="<?= !empty($value) ? $value['result'] : 0 ?>">
											<div class="text-center div-result"><?= !empty($value['result']) ? $value['result'] : 0 ?></div>
										</td>
										<td>
											<?= $value['note_criteria'] ?>
										</td>
										<td class="text-center text-danger">
											<i class="fa fa-remove" onclick="removeKpiItem(this)" style="cursor: pointer;"></i>
										</td>
									</tr>
									<?php
									$total_weight_number += $value['weight_number'];
									$counter++;
									?>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr class="not-tr bold uppercase">
							<td style="border-top: 1px solid #cedae6;" colspan="4" class="text-center"><?= lang('tnh_total') ?></td>
							<td style="border-top: 1px solid #cedae6;" class="text-center txt-total-weight text-danger"><?= formatNumber($total_weight_number) ?></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;" class="txt-total_point_with_coefficient text-center text-danger">0</td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
						</tr>
					</tfoot>
				</table>
				<div class="mtop15"></div>
				<div class="bold"><?= lang('tnh_title_kpi_2') ?></div>
				<table id="tb-kpi-2" class="table table-hover dataTable">
					<thead>
						<tr>
							<th class="text-center" rowspan="2" style="width: 30px;"><?= lang('STT') ?></th>
							<th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tiêu chí') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Đơn vị tính') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Mục tiêu') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('tnh_weight_number') ?></th>
							<th class="text-center" style="width: 110px;">1.<?= lang('tnh_not_reached') ?></th>
							<th class="text-center" style="width: 110px;">2.<?= lang('tnh_need_keep_trying') ?></th>
							<th class="text-center" style="width: 110px;">3.<?= lang('tnh_obtain') ?></th>
							<th class="text-center" style="width: 110px;">4.<?= lang('tnh_pass') ?></th>
							<th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Tổng điểm') ?></th>
							<th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Phương pháp đánh giá') ?></th>
							<!-- <th class="text-center" rowspan="2" style="width: 50px;"><i class="fa fa-trash-o"></i></th> -->
						</tr>
						<tr>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(1 điểm)') ?></th>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(2 điểm)') ?></th>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(3 điểm)') ?></th>
							<th class="text-center" style="border-bottom: 1px solid;"><?= lang('(4 điểm)') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$total_weight_number_2 = 0;
						?>
						<?php if (empty($kpi) || !empty($kpi)) : ?>
							<?php
							$_date = date('Y-m-d');

							if (empty($kpi)) {
								$this->db->select('
										tbl_kpi_criteria.*,
										tbl_kpi_criteria.id as id_kpi_criteria
									', false);
								$this->db->from('tbl_kpi_criteria');
								$this->db->where('tbl_kpi_criteria.start_date <=', $_date);
								$this->db->where('tbl_kpi_criteria.end_date >=', $_date);
								$this->db->where('tbl_kpi_criteria.behavior_discipline', 1);
								$kpi_criteria = $this->db->get()->result_array();
							} else {
								$this->db->select('tbl_kpi_items.*, tbl_kpi_criteria.criteria, tbl_kpi_criteria.unit, tbl_kpi_criteria.note_criteria as note_criteria, tbl_kpi_criteria.id as id_kpi_criteria', false);
								$this->db->from('tbl_kpi_items');
								$this->db->join('tbl_kpi_criteria', 'tbl_kpi_criteria.id = tbl_kpi_items.kpi_criteria_id');
								$this->db->where('tbl_kpi_items.kpi_id', $kpi['id']);
								$this->db->where('tbl_kpi_items.type', 1);
								$this->db->order_by('tbl_kpi_items.id ASC');
								$kpi_criteria = $this->db->get()->result_array();
							}
							?>
							<?php if (!empty($kpi_criteria)) : ?>
								<?php foreach ($kpi_criteria as $key => $value) : ?>
									<tr>
										<td class="text-center td-numbers"><?= ++$key ?></td>
										<td><?= $value['criteria'] ?></td>
										<td class="text-center"><?= $value['unit'] ?></td>
										<td class="text-center">
											<textarea type="text" name="_target[]" class="form-control target" value=""><?= $value['target'] ?></textarea>
											<span class="txt-target hide"><?= $value['target'] ?></span>
											<input type="hidden" name="counter[]" class="form-control" value="<?= $counter ?>">
											<input type="hidden" name="type[]" class="form-control" value="1">
										</td>
										<td class="text-center">
											<input type="text" name="_weight_number[]" class="form-control weight_number" onchange="totalKpi()" value="<?= $value['weight_number'] ?>">
											<span class="txt-weight_number hide"><?= $value['weight_number'] ?></span>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control not_reached" value="<?= $value['not_reached'] ?>">
											<input type="hidden" class="form-control not_reached_from" value="<?= $value['not_reached_from'] ?>">
											<input type="hidden" class="form-control not_reached_to" value="<?= $value['not_reached_to'] ?>">
											<?php

											$strNotReached = $value['not_reached'] ? calRecipe($value['not_reached']) : '';
											// echo $value['not_reached'] ? calRecipe($value['not_reached']) : '';
											if (!empty($value['not_reached'])) {
												// echo $value['not_reached_from'] . ($value['not_reached'] == 4 ? ' - ' . $value['not_reached_to'] : '');
												// echo ' '.$value['not_reached_from'];
												$strNotReached.= ' '.$value['not_reached_from'];
											}

											if (!empty($kpi)) {
												$strNotReached = $value['not_reached_from'];
											}

											// $strNotReached = '
											// 	<input type="text" name="_not_reached_from[]" class="form-control" value="'.$strNotReached.'">
											// ';
											$strNotReached = '
												<textarea type="text" name="_not_reached_from[]" class="form-control" value="">'.$strNotReached.'</textarea>
											';
											echo $strNotReached;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 1 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_not_reached chonse" id="chonse_not_reached_<?= $counter ?>" value="1">
												<label for="chonse_not_reached_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control need_keep_trying" value="<?= $value['need_keep_trying'] ?>">
											<input type="hidden" class="form-control need_keep_trying_from" value="<?= $value['need_keep_trying_from'] ?>">
											<input type="hidden" class="form-control need_keep_trying_to" value="<?= $value['need_keep_trying_to'] ?>">
											<?php

											$strNeedKeepTrying = $value['need_keep_trying'] ? calRecipe($value['need_keep_trying']) : '';
											// echo $value['need_keep_trying'] ? calRecipe($value['need_keep_trying']) : '';
											if (!empty($value['need_keep_trying'])) {
												// echo ' ' . $value['need_keep_trying_from'] . ($value['need_keep_trying'] == 4 ? ' - ' . $value['need_keep_trying_to'] : '');
												// echo ' ' . $value['need_keep_trying_from'];
												$strNeedKeepTrying.= ' '.$value['need_keep_trying_from'];
											}

											if (!empty($kpi)) {
												$strNeedKeepTrying = $value['need_keep_trying_from'];
											}

											// $strNeedKeepTrying = '
											// 	<input type="text" name="_need_keep_trying_from[]" class="form-control" value="'.$strNeedKeepTrying.'">
											// ';
											$strNeedKeepTrying = '
												<textarea type="text" name="_need_keep_trying_from[]" class="form-control" value="">'.$strNeedKeepTrying.'</textarea>
											';
											echo $strNeedKeepTrying;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 2 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_need_keep_trying chonse" id="chonse_need_keep_trying_<?= $counter ?>" value="2">
												<label for="chonse_need_keep_trying_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control obtain" value="<?= $value['obtain'] ?>">
											<input type="hidden" class="form-control obtain_from" value="<?= $value['obtain_from'] ?>">
											<input type="hidden" class="form-control obtain_to" value="<?= $value['obtain_to'] ?>">
											<?php
											$strObtain = $value['obtain'] ? calRecipe($value['obtain']) : '';
											// echo $value['obtain'] ? calRecipe($value['obtain']) : '';
											if (!empty($value['obtain'])) {
												// echo ' ' . $value['obtain_from'] . ($value['obtain'] == 4 ? ' - ' . $value['obtain_to'] : '');
												// echo ' ' . $value['obtain_from'];
												$strObtain.= ' '.$value['obtain_from'];
											}

											if (!empty($kpi)) {
												$strObtain = $value['obtain_from'];
											}

											// $strObtain = '
											// 	<input type="text" name="_obtain_from[]" class="form-control" value="'.$strObtain.'">
											// ';
											$strObtain = '
												<textarea type="text" name="_obtain_from[]" class="form-control" value="">'.$strObtain.'</textarea>
											';
											echo $strObtain;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 3 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_obtain chonse" id="chonse_obtain_<?= $counter ?>" value="3">
												<label for="chonse_obtain_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" class="form-control pass" value="<?= $value['pass'] ?>">
											<input type="hidden" class="form-control pass_from" value="<?= $value['pass_from'] ?>">
											<input type="hidden" class="form-control pass_to" value="<?= $value['pass_to'] ?>">
											<?php
											$strPass = $value['pass'] ? calRecipe($value['pass']) : '';
											// echo $value['pass'] ? calRecipe($value['pass']) : '';
											if (!empty($value['pass'])) {
												// echo ' ' . $value['pass_from'] . ($value['pass'] == 4 ? ' - ' . $value['pass_to'] : '');
												// echo ' ' . $value['pass_from'];
												$strPass.= ' '.$value['pass_from'];
											}

											if (!empty($kpi)) {
												$strPass = $value['pass_from'];
											}

											// $strPass = '
											// 	<input type="text" name="_pass_from[]" class="form-control" value="'.$strPass.'">
											// ';
											$strPass = '
												<textarea type="text" name="_pass_from[]" class="form-control" value="">'.$strPass.'</textarea>
											';
											echo $strPass;
											?>
											<div class="radio radio-primary">
												<input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 4 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_pass chonse" id="chonse_pass_<?= $counter ?>" value="4">
												<label for="chonse_pass_<?= $counter ?>"><?= lang('choose') ?></label>
											</div>
										</td>
										<td class="text-center">
											<input type="hidden" name="kpi_item_id[]" class="form-control kpi_item_id" value="<?= !empty($kpi) ? $value['id'] : 0 ?>">
											<input type="hidden" name="kpi_criteria_id[]" class="form-control kpi_criteria_id" value="<?= !empty($value['id_kpi_criteria']) ? $value['id_kpi_criteria'] : 0 ?>">
											<input type="hidden" name="result[]" class="form-control result" readonly onchange="totalKpi()" style="width: 100%;" value="<?= !empty($value['result']) ? $value['result'] : 0 ?>">
											<div class="text-center div-result"><?= !empty($value['result']) ? $value['result'] : 0 ?></div>
										</td>
										<td>
											<?= $value['note_criteria'] ?>
										</td>
									</tr>
									<?php
									$total_weight_number_2 += $value['weight_number'];
									$counter++;
									?>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr class="not-tr bold uppercase">
							<td style="border-top: 1px solid #cedae6;" colspan="4" class="text-center"><?= lang('tnh_total') ?></td>
							<td style="border-top: 1px solid #cedae6;" class="text-center txt-total-weight text-danger"><?= formatNumber($total_weight_number) ?></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<td style="border-top: 1px solid #cedae6;" class="txt-total_point_with_coefficient text-center text-danger">0</td>
							<td style="border-top: 1px solid #cedae6;"></td>
							<!-- <td style="border-top: 1px solid #cedae6;"></td> -->
						</tr>
					</tfoot>
				</table>
				<div class="mtop15"></div>
				<div class="bold"><?= lang('III. LỖI - SỰ CỐ') ?></div>
				<table id="tb-error" class="table table-hover dataTable">
					<thead>
						<th class="text-center" style="width: 50px; background: #f443366b !important;"><?= lang('tnh_numbers') ?></th>
						<th class="text-center" style="background: #f443366b !important;"><?= lang('Vi phạm') ?></th>
						<th class="text-center" style="background: #f443366b !important;"><?= lang('Số phiếu') ?></th>
						<th class="text-center" style="background: #f443366b !important;"><?= lang('Điểm') ?></th>
					</thead>
					<tbody>
						<?php
							$counterPoint = 0;
							if (!empty($kpi)) {
								$this->db->simple_query('SET SESSION group_concat_max_len=1844674407370955161');
								$tb_kpi_trouble_violation_items = "(
									SELECT
										tbl_kpi_trouble_violation_items.kpi_trouble_violation_id as kpi_trouble_violation_id,
										GROUP_CONCAT(tbl_kpi_trouble_violation_items.production_report_id SEPARATOR '|||') as production_report_id
									FROM tbl_kpi_trouble_violation_items
									WHERE tbl_kpi_trouble_violation_items.kpi_id = ".$kpi['id']."
									GROUP BY tbl_kpi_trouble_violation_items.kpi_trouble_violation_id
								) tb_kpi_trouble_violation_items";

								$this->db->select('
									tbl_kpi_trouble_violation.*,
									tbltrouble_violation_point.name,
									tb_kpi_trouble_violation_items.production_report_id as production_report_id
								', false);
								$this->db->from('tbl_kpi_trouble_violation');
								$this->db->join('tbltrouble_violation_point', 'tbltrouble_violation_point.id = tbl_kpi_trouble_violation.trouble_violation_point_id');
								$this->db->join($tb_kpi_trouble_violation_items, 'tb_kpi_trouble_violation_items.kpi_trouble_violation_id = tbl_kpi_trouble_violation.id', 'left');
								$this->db->where('tbl_kpi_trouble_violation.kpi_id', $kpi['id']);
								$kpi_trouble_violation = $this->db->get()->result_array();
							}
						?>
						<?php if(!empty($kpi_trouble_violation)): ?>
							<?php foreach($kpi_trouble_violation as $key => $value): ?>
								<?php
									$tdNumber = '<td class="text-center td-numbers">'.(++$key).'</td>';
									$tdProblem = '<td>'.$value['name'].'</td>';
									$tdVote = '<td class="text-center">'.$value['count_vote'].' phiếu</td>';
									$tdPoint = '<td class="text-center">
										<input type="hidden" name="trouble_violation['.$counterPoint.'][kpi_trouble_violation_id]" class="form-control" value="'.$value['id'].'">
										<input type="hidden" name="trouble_violation['.$counterPoint.'][violation_point]" class="form-control violation_point" value="'.$value['violation_point'].'">
										<input type="hidden" name="trouble_violation['.$counterPoint.'][trouble_violation_point_id]" class="form-control trouble_violation_point_id" value="'.$value['trouble_violation_point_id'].'">
										<input type="hidden" name="trouble_violation['.$counterPoint.'][count_vote]" class="form-control count_vote" value="'.$value['count_vote'].'">
										<input type="hidden" name="trouble_violation['.$counterPoint.'][production_report_id]" class="form-control production_report_id" value="'.$value['production_report_id'].'">
										'.$value['violation_point'].'
									</td>';
				
									echo '<tr>
										'.$tdNumber.'
										'.$tdProblem.'
										'.$tdVote.'
										'.$tdPoint.'
									</tr>';
									$counterPoint++;
								?>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr class="bold">
							<td></td>
							<td class="text-center"><?= lang('TỔNG') ?></td>
							<td></td>
							<td class="text-center txt-grand-total-error text-danger"></td>
						</tr>
					</tfoot>
				</table>
				<div class="mtop15"></div>
				<div class="bold"><?= lang('IV. KHEN THƯỞNG') ?></div>
				<table id="tb-bonus" class="table table-hover dataTable">
					<thead>
						<th class="text-center" style="width: 50px; background: #4caf507d !important;">
							<a onclick="addBonus()" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="true">
								<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
									<path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
								</svg>
							</a>
						</th>
						<th class="text-center" style="background: #4caf507d !important;"><?= lang('Nội dung khen thưởng') ?></th>
						<th class="text-center" style="background: #4caf507d !important;"><?= lang('Điểm') ?></th>
						<th class="text-center" style="background: #4caf507d !important; width: 50px;"><i class="fa fa-trash-o"></i></th>
					</thead>
					<tbody>
						<?php
							$counterBonus = 0;
							$kpi_bonus = !empty($kpi) ? $this->kpi_model->getKpiBonus($kpi['id']) : null;
						?>
						<?php if(!empty($kpi_bonus)): ?>
							<?php foreach($kpi_bonus as $key => $value): ?>
								<?php
									$tdNumber = '<td class="text-center td-numbers">'.(++$key).'</td>';
									$tdContent = '<td>
										<input type="hidden" name="bonus['.$counterBonus.'][kpi_bonus_id]" class="form-control" value="'.$value['id'].'">
										<input type="text" name="bonus['.$counterBonus.'][content]" placeholder="'.lang('Nội dung khen thưởng').'" class="form-control content" onchange="totalKpi()" value="'.$value['content'].'">
									</td>';
									$tdPoint = '<td>
										<input type="text" name="bonus['.$counterBonus.'][point]" onchange="totalKpi()" class="form-control number-format point" value="'.$value['point'].'">
									</td>';
									$tdActions = '<td class="text-center text-danger">
										<i class="fa fa-remove" onclick="removeBonus(this)" style="cursor: pointer;"></i>
									</td>';
							
									echo '<tr>
										'.$tdNumber.'
										'.$tdContent.'
										'.$tdPoint.'
										'.$tdActions.'
									</tr>';
									$counterBonus++;
								?>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr class="bold">
							<td></td>
							<td class="text-center uppercase"><?= lang('tnh_total') ?></td>
							<td class="text-danger text-center td-total-bonus"></td>
							<td></td>
						</tr>
					</tfoot>
				</table>

				<div class="mtop15"></div>
				<div class="bold"><?= lang('KẾT QUẢ ĐÁNH GIÁ CHUNG') ?></div>
				<table class="table table-hover dataTable">
					<tr class="not-tr bold text-danger">
						<td colspan="4" style="border-top: 1px solid #cedae6;" class="text-center"><?= lang('Tổng số') ?></td>
						<td colspan="9" style="border-top: 1px solid #cedae6;" class="txt-point-kpi text-center"></td>
					</tr>
					<tr class="not-tr text-center bold text-primary">
						<td colspan="3">
							<?php
							echo $not_reached ? calRecipe($not_reached) : '';
							if (!empty($not_reached)) {
								echo ' ' . $not_reached_from . ($not_reached == 4 ? ' - ' . $not_reached_to : '');
							}
							?>
						</td>
						<td colspan="3">
							<?php
							echo $need_keep_trying ? calRecipe($need_keep_trying) : '';
							if (!empty($need_keep_trying)) {
								echo ' ' . $need_keep_trying_from . ($need_keep_trying == 4 ? ' - ' . $need_keep_trying_to : '');
							}
							?>
						</td>
						<td colspan="3">
							<?php
							echo $obtain ? calRecipe($obtain) : '';
							if (!empty($obtain)) {
								echo ' ' . $obtain_from . ($obtain == 4 ? ' - ' . $obtain_to : '');
							}
							?>
						</td>
						<td colspan="3">
							<?php
							echo $pass ? calRecipe($pass) : '';
							if (!empty($pass)) {
								echo ' ' . $pass_from . ($pass == 4 ? ' - ' . $pass_to : '');
							}
							?>
						</td>
					</tr>
					<tr class="not-tr text-center bold text-primary">
						<td colspan="3"><?= lang('tnh_not_reached') ?></td>
						<td colspan="3"><?= lang('tnh_need_keep_trying') ?></td>
						<td colspan="3"><?= lang('tnh_obtain') ?></td>
						<td colspan="3"><?= lang('tnh_pass') ?></td>
					</tr>
				</table>
				<div class="mtop15"></div>
				<div class="bold"><?= lang('III. NHẬN XÉT TỔNG THỂ') ?></div>
				<table class="table table-hover dataTable">
					<tfoot>
						<tr class="not-tr">
							<td colspan="1" class="bold" style="border-top: 1px solid #cedae6; width: 150px;"><?= lang('Ưu điểm:') ?></td>
							<td colspan="1" style="border-top: 1px solid #cedae6;">
								<textarea name="advantage" id="advantage" placeholder="<?= lang('Ưu điểm') ?>" class="form-control advantage" rows="3"><?= !empty($kpi) ? $kpi['advantage'] : '' ?></textarea>
							</td>
						</tr>
						<tr class="not-tr">
							<td colspan="1" class="bold"><?= lang('Những mặt cần khác phục, cố gắng hơn:') ?></td>
							<td colspan="1">
								<textarea name="fix_try" id="fix_try" placeholder="<?= lang('Những mặt cần khác phục, cố gắng hơn') ?>" class="form-control fix_try" rows="3"><?= !empty($kpi) ? $kpi['fix_try'] : '' ?></textarea>
							</td>
						</tr>
						<tr class="not-tr">
							<td colspan="1" class="bold"><?= lang('Các nhận xét khác:') ?></td>
							<td colspan="1">
								<textarea name="note" id="note" placeholder="<?= lang('Các nhận xét khác') ?>" class="form-control note" rows="3"><?= !empty($kpi) ? $kpi['note'] : '' ?></textarea>
							</td>
						</tr>
						</tbody>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
			<input type="hidden" name="save" class="form-control" value="1">
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
<script>
	var kpi_id = <?= !empty($id) ? $id : 0 ?>;
	var calRecipe = <?= json_encode(calRecipe()) ?>;
	var counter = <?= $counter ?>;
	var grand_total_weight_number = 0;
	var WARNING_WEIGHT_NUMBER_KPI = <?= WARNING_WEIGHT_NUMBER_KPI ?>;
	var counterPoint = <?= $counterPoint ?>;
	var counterBonus = <?= $counterBonus ?>;

	function totalKpi() {
		tb = '#tb-kpi tbody tr:not("[class^=not-tr]")';
		var n = $(tb).length;
		var stt = 0;
		count_errors = 0;

		total_point_with_coefficient = 0;
		total_weight_number = 0;
		for (ii = 0; ii < n; ii++) {
			stt++;

			element = $(tb)[ii];
			result = intVal($(element).find('.result').val());
			$(element).find('.td-numbers').html(stt);

			target = $(element).find('.target').val();
			weight_number = intVal($(element).find('.weight_number').val());
			not_reached = $(element).find('.not_reached').val();
			not_reached_from = $(element).find('.not_reached_from').val();
			not_reached_to = $(element).find('.not_reached_to').val();

			need_keep_trying = $(element).find('.need_keep_trying').val();
			need_keep_trying_from = $(element).find('.need_keep_trying_from').val();
			need_keep_trying_to = $(element).find('.need_keep_trying_to').val();

			obtain = $(element).find('.obtain').val();
			obtain_from = $(element).find('.obtain_from').val();
			obtain_to = $(element).find('.obtain_to').val();

			pass = $(element).find('.pass').val();
			pass_from = $(element).find('.pass_from').val();
			pass_to = $(element).find('.pass_to').val();

			point_no_coefficient = 0;
			point_with_coefficient = 0;

			chonse = intVal($(element).find('.chonse:checked').val());
			result = chonse * weight_number;
			$(element).find('.result').val(result);
			$(element).find('.div-result').html(result);

			total_weight_number += weight_number;
			total_point_with_coefficient += result;
		}

		tb_2 = '#tb-kpi-2 tbody tr:not("[class^=not-tr]")';
		var n_2 = $(tb_2).length;
		total_point_with_coefficient_2 = 0;
		total_weight_number_2 = 0;
		stt_2 = 0;
		for (ii = 0; ii < n_2; ii++) {
			stt_2++;

			element = $(tb_2)[ii];
			result = intVal($(element).find('.result').val());
			$(element).find('.td-numbers').html(stt_2);

			target = $(element).find('.target').val();
			weight_number = intVal($(element).find('.weight_number').val());
			chonse = intVal($(element).find('.chonse:checked').val());
			result = chonse * weight_number;

			// console.log(result);

			$(element).find('.result').val(result);
			$(element).find('.div-result').html(result);

			total_weight_number_2 += weight_number;
			total_point_with_coefficient_2 += result;
		}

		grand_total_weight_number = total_weight_number;
		$('#tb-kpi .txt-total-weight').html(tnhFormatNumber(total_weight_number));
		$('#tb-kpi .txt-total_point_with_coefficient').html(tnhFormatNumber(total_point_with_coefficient));

		$('#tb-kpi-2 .txt-total-weight').html(tnhFormatNumber(total_weight_number_2));
		$('#tb-kpi-2 .txt-total_point_with_coefficient').html(tnhFormatNumber(total_point_with_coefficient_2));

		//tb-error
		tbError = '#tb-error tbody tr:not("[class^=not-tr]")';
		var nError = $(tbError).length;
		total_violation_point = 0;
		for (ii = 0; ii < nError; ii++) {
			element = $(tbError)[ii];
			violation_point = intVal($(element).find('.violation_point').val());
			total_violation_point+= violation_point;
		}
		$('.txt-grand-total-error').html(tnhFormatNumber(total_violation_point));

		//tb-bonus
		tbBonus = '#tb-bonus tbody tr:not("[class^=not-tr]")';
		var nBonus = $(tbBonus).length;
		total_point = 0;
		var sttBonus = 0;
		for (ii = 0; ii < nBonus; ii++) {
			sttBonus++;
			element = $(tbBonus)[ii];
			$(element).find('.td-numbers').html(sttBonus)
			content = $(element).find('.content').val();
			if (content) {
				point = intVal($(element).find('.point').val());
				total_point+= point;
			}
		}
		$('.td-total-bonus').html(tnhFormatNumber(total_point));

		txt_point_kpi = (total_point_with_coefficient + total_point_with_coefficient_2 - total_violation_point + total_point) / (total_weight_number + total_weight_number_2);
		$('.txt-point-kpi').html(tnhFormatNumber(txt_point_kpi));

	}

	function loadDataKpi() {
		return;
		var dataPOST = {};
		dataPOST[csrfData['token_name']] = csrfData['hash'];
		month = $('#month').val();
		year = $('#year').val();
		staff = $('#staff').val();
		start_date = $('#start_date').val();
		end_date = $('#end_date').val();

		if (!staff) {
			alert_float('danger', 'Vui lòng chọn nhân viên');
			return;
		}

		if (!start_date || !end_date) {
			alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc');
			return;
		}

		dataPOST['month'] = month;
		dataPOST['year'] = year;
		dataPOST['staff'] = staff;
		dataPOST['kpi_id'] = kpi_id;
		dataPOST['start_date'] = start_date;
		dataPOST['end_date'] = end_date;
		$.ajax({
			type: "POST",
			url: site.base_url + 'admin/kpi/loadDataKpi',
			data: dataPOST,
			dataType: "html",
			success: function(response) {
				$('#tb-kpi tbody').html(response);
			}
		});
	}

	function loadKpiCriteria() {
		var dataPOST = {};
		dataPOST[csrfData['token_name']] = csrfData['hash'];
		start_date = $('#start_date').val();
		end_date = $('#end_date').val();
		staff = $('#staff').val();
		department = $('#department').val();
		type_kpi = $('input[name="type_kpi"]:checked').val();

		dataPOST['start_date'] = start_date;
		dataPOST['end_date'] = end_date;
		dataPOST['staff'] = staff;
		dataPOST['department'] = department;
		dataPOST['type_kpi'] = type_kpi;

		$.ajax({
			type: "POST",
			url: site.base_url + 'admin/kpi/loadKpiCriteria',
			data: dataPOST,
			dataType: "json",
			success: function(response) {
				var options = ``;
				if (response.kpi_criteria) {
					$.each(response.kpi_criteria, function(index, value) {
						options += `<option value="${value.id}">${value.criteria}</option>`;
					});
				}
				$('#criteria_kpi').html(options);
				$('#criteria_kpi').selectpicker('refresh');
			}
		});
	}

	function loadDataItemKpi() {
		var dataPOST = {};
		var criteria_kpi = $('#criteria_kpi').val();

		dataPOST[csrfData['token_name']] = csrfData['hash'];
		dataPOST['criteria_kpi'] = criteria_kpi;

		$.ajax({
			type: "POST",
			url: site.base_url + 'admin/kpi/loadDataItemKpi',
			data: dataPOST,
			dataType: "json",
			success: function(response) {
				htmlData = '';

				if (response.kpi_criteria) {
					$.each(response.kpi_criteria, function(index, value) {
						tdNumber = `<td class="text-center td-numbers"></td>`;
						tdCriteria = `<td class="">
							${value['criteria']}
						</td>`;

						tdUnit = `<td class="text-center">
							${value['unit']}
						</td>`;

						// tdTarget = `<td class="text-center">
						// 	<input type="hidden" class="form-control target" value="${value['target']}">
                    	// 	<span class="txt-target">${value['target']}</span>
						// 	<input type="hidden" name="counter[]" class="form-control" value="${counter}">
						// 	<input type="hidden" name="type[]" class="form-control" value="0">
						// </td>`;
						tdTarget = `<td class="text-center">
							<textarea type="text" name="_target[]" class="form-control target" value="">${value['target']}</textarea>
                    		<span class="txt-target hide">${value['target']}</span>
							<input type="hidden" name="counter[]" class="form-control" value="${counter}">
							<input type="hidden" name="type[]" class="form-control" value="0">
						</td>`;

						// tdWeightNumber = `<td class="text-center">
						// 	<input type="hidden" class="form-control weight_number" value="${value['weight_number']}">
                    	// 	<span class="txt-weight_number">${value['weight_number']}</span>
						// </td>`;
						tdWeightNumber = `<td class="text-center">
							<input type="text" name="_weight_number[]" class="form-control weight_number" onchange="totalKpi()" value="${value['weight_number']}">
                    		<span class="txt-weight_number hide">${value['weight_number']}</span>
						</td>`;

						strNotReached = '';
						if (value['not_reached']) {
							$.each(calRecipe, function(i, v) {
								if (i == value['not_reached']) {
									strNotReached += v;
									return;
								}
							});
							// strNotReached+= value['not_reached_from']+(value['not_reached'] == 4 ? ' - ' + value['not_reached_to'] : '');
							// strNotReached += ' '+value['not_reached_from'];
							strNotReached += ' '+''+value['not_reached_from'];
						}

						// strNotReached = `
						// 	<input type="text" name="_not_reached_from[]" class="form-control" value="${strNotReached}">
						// `;

						strNotReached = `
							<textarea type="text" name="_not_reached_from[]" class="form-control" value="">${strNotReached}</textarea>
						`;

						tdNotReached = `<td class="text-center">
							<input type="hidden" class="form-control not_reached" value="${value['not_reached']}">
							<input type="hidden" class="form-control not_reached_from" value="${value['not_reached_from']}">
							<input type="hidden" class="form-control not_reached_to" value="${value['not_reached_to']}">
							${strNotReached}
							<div class="radio radio-primary">
								<input type="radio" name="chonse[${counter}]" onchange="totalKpi()" class="chonse_not_reached chonse" id="chonse_not_reached_${counter}" value="1">
								<label for="chonse_not_reached_${counter}"><?= lang('choose') ?></label>
							</div>
						</td>`;

						strNeedKeepTrying = '';
						if (value['need_keep_trying']) {
							$.each(calRecipe, function(i, v) {
								if (i == value['need_keep_trying']) {
									strNeedKeepTrying += v;
									return;
								}
							});
							// strNeedKeepTrying+= value['need_keep_trying_from']+(value['need_keep_trying'] == 4 ? ' - ' + value['need_keep_trying_to'] : '');
							strNeedKeepTrying += ' '+value['need_keep_trying_from'];
						}

						// strNeedKeepTrying = `
						// 	<input type="text" name="_need_keep_trying_from[]" class="form-control" value="${strNeedKeepTrying}">
						// `;

						strNeedKeepTrying = `
							<textarea type="text" name="_need_keep_trying_from[]" class="form-control" value="">${strNeedKeepTrying}</textarea>
						`;

						tdNeedKeepTrying = `<td class="text-center">
							<input type="hidden" class="form-control need_keep_trying" value="${value['need_keep_trying']}">
							<input type="hidden" class="form-control need_keep_trying_from" value="${value['need_keep_trying_from']}">
							<input type="hidden" class="form-control need_keep_trying_to" value="${value['need_keep_trying_to']}">
							${strNeedKeepTrying}
							<div class="radio radio-primary">
								<input type="radio" name="chonse[${counter}]" onchange="totalKpi()" class="chonse_need_keep_trying chonse" id="chonse_need_keep_trying_${counter}" value="2">
								<label for="chonse_need_keep_trying_${counter}"><?= lang('choose') ?></label>
							</div>
						</td>`;

						strObtain = '';
						if (value['obtain']) {
							$.each(calRecipe, function(i, v) {
								if (i == value['obtain']) {
									strObtain += v;
									return;
								}
							});
							// strObtain+= value['obtain_from']+(value['obtain'] == 4 ? ' - ' + value['obtain_to'] : '');
							strObtain += ' '+value['obtain_from'];
						}

						// strObtain = `
						// 	<input type="text" name="_obtain_from[]" class="form-control" value="${strObtain}">
						// `;
						strObtain = `
							<textarea type="text" name="_obtain_from[]" class="form-control" value="">${strObtain}</textarea>
						`;

						tdObtain = `<td class="text-center">
							<input type="hidden" class="form-control obtain" value="${value['obtain']}">
							<input type="hidden" class="form-control obtain_from" value="${value['obtain_from']}">
							<input type="hidden" class="form-control obtain_to" value="${value['obtain_to']}">
							${strObtain}
							<div class="radio radio-primary">
								<input type="radio" name="chonse[${counter}]" onchange="totalKpi()" class="chonse_obtain chonse" id="chonse_obtain_${counter}" value="3">
								<label for="chonse_obtain_${counter}"><?= lang('choose') ?></label>
							</div>
						</td>`;

						strPass = '';
						if (value['pass']) {
							$.each(calRecipe, function(i, v) {
								if (i == value['pass']) {
									strPass += v;
									return;
								}
							});
							// strPass+= value['pass_from']+(value['pass'] == 4 ? ' - ' + value['pass_to'] : '');
							strPass += ' '+value['pass_from'];
						}

						// strPass = `
						// 	<input type="text" name="_pass_from[]" class="form-control" value="${strPass}">
						// `;

						strPass = `
							<textarea type="text" name="_pass_from[]" class="form-control" value="">${strPass}</textarea>
						`;

						tdPass = `<td class="text-center">
							<input type="hidden" class="form-control pass" value="${value['pass']}">
							<input type="hidden" class="form-control pass_from" value="${value['pass_from']}">
							<input type="hidden" class="form-control pass_to" value="${value['pass_to']}">
							${strPass}
							<div class="radio radio-primary">
								<input type="radio" name="chonse[${counter}]" onchange="totalKpi()" class="chonse_pass chonse" id="chonse_pass_${counter}" value="4">
								<label for="chonse_pass_${counter}"><?= lang('choose') ?></label>
							</div>
						</td>`;

						tdResult = `<td>
							<input type="hidden" name="kpi_item_id[]" class="form-control kpi_criteria_id" value="0">
                    		<input type="hidden" name="kpi_criteria_id[]" class="form-control kpi_criteria_id" value="${value['id']} ">
							<input type="hidden" name="result[]" class="form-control result" onchange="totalKpi()" readonly style="width: 100%;" value="0">
							<div class="text-center div-result"></div>
						</td>`;

						tdNoteCriteria = `<td>
							${value['note_criteria'] ? value['note_criteria'] : ''}
						</td>`;

						tdActions = `<td class="text-center text-danger">
							<i class="fa fa-remove" onclick="removeKpiItem(this)" style="cursor: pointer;"></i>
						</td>`;

						trKpi = `<tr>
							${tdNumber}
							${tdCriteria}
							${tdUnit}
							${tdTarget}
							${tdWeightNumber}
							${tdNotReached}
							${tdNeedKeepTrying}
							${tdObtain}
							${tdPass}
							${tdResult}
							${tdNoteCriteria}
							${tdActions}
						</tr>`;
						htmlData += trKpi;
						counter++;
					});
				}

				$('#tb-kpi tbody').append(htmlData);
				totalKpi();
			}
		});
	}

	function loadDataError() {
		var dataPOST = {};
		dataPOST[csrfData['token_name']] = csrfData['hash'];
		start_date = $('#start_date').val();
		end_date = $('#end_date').val();
		staff = $('#staff').val();
		department = $('#department').val();
		type_kpi = $('input[name="type_kpi"]:checked').val();

		dataPOST['start_date'] = start_date;
		dataPOST['end_date'] = end_date;
		dataPOST['staff'] = staff;
		dataPOST['department'] = department;
		dataPOST['type_kpi'] = type_kpi;

		$.ajax({
			type: "POST",
			url: site.base_url + 'admin/kpi/loadDataError',
			data: dataPOST,
			dataType: "json",
			success: function(response) {
				var trHtml = '';
				$.each(response.trouble_violation_point, function (index, value) {
					tdNumber = `<td class="text-center td-numbers">${++index}</td>`;
					tdProblem = `<td>${value.name}</td>`;
					tdVote = `<td class="text-center">${value.count_vote} phiếu</td>`;
					tdPoint = `<td class="text-center">
						<input type="hidden" name="trouble_violation[${counterPoint}][violation_point]" class="form-control violation_point" value="${value.violation_point}">
						<input type="hidden" name="trouble_violation[${counterPoint}][trouble_violation_point_id]" class="form-control trouble_violation_point_id" value="${value.id}">
						<input type="hidden" name="trouble_violation[${counterPoint}][count_vote]" class="form-control count_vote" value="${value.count_vote}">
						<input type="hidden" name="trouble_violation[${counterPoint}][production_report_id]" class="form-control production_report_id" value="${value.production_report_id}">
						${value.violation_point}
					</td>`;

					trHtml+= `<tr>
						${tdNumber}
						${tdProblem}
						${tdVote}
						${tdPoint}
					</tr>`;
					counterPoint++;
				});
				$('#tb-error tbody').html(trHtml);
				totalKpi();
			}
		});
	}

	function refershDataKpi() {
		$('#tb-kpi tbody').html('');
		totalKpi();
	}

	function removeKpiItem(_this) {
		$(_this).closest('tr').remove();
		totalKpi();
	}

	function removeBonus(_this) {
		$(_this).closest('tr').remove();
		totalKpi();
	}

	function addBonus() {
		tdNumber = `<td class="text-center td-numbers"></td>`;
		tdContent = `<td>
			<input type="text" name="bonus[${counterBonus}][content]" placeholder="<?= lang('Nội dung khen thưởng') ?>" class="form-control content" onchange="totalKpi()" value="">
		</td>`;
		tdPoint = `<td>
			<input type="text" name="bonus[${counterBonus}][point]" onchange="totalKpi()" class="form-control number-format point" value="0">
		</td>`;
		tdActions = `<td class="text-center text-danger">
			<i class="fa fa-remove" onclick="removeBonus(this)" style="cursor: pointer;"></i>
		</td>`;

		trBonus = `<tr>
			${tdNumber}
			${tdContent}
			${tdPoint}
			${tdActions}
		</tr>`;
		$('#tb-bonus tbody').append(trBonus);
		counterBonus++;
		totalKpi();
	}

	$(document).ready(function() {
		$('#month').select2();
		$('#year').select2();

		$('#staff').select2();
		$('#staff').change(function(event) {
			department = $("#staff").select2().find(":selected").data("department");
			role = $("#staff").select2().find(":selected").data("role");

			$('.txt-department').html(department);
			$('.txt-role').html(role);
		});

		$('#month, #year, #staff, #start_date, #end_date, #department').change(function(event) {
			loadDataKpi();
			loadKpiCriteria();
			loadDataError();
		});

		if (kpi_id > 0) {
			loadDataKpi();
			loadKpiCriteria();
		}

		$('input[name="type_kpi"]').change(function(event) {
			type_kpi = $('input[name="type_kpi"]:checked').val();
			$('#staff').val(null).select2();
			$('select#department').val('');
            $('select#department').selectpicker('refresh');
			$('.txt-department').html('');
			$('.txt-role').html('');
			$('#tb-kpi tbody').html('');

			if (type_kpi == 1) {
				$('.div-staff').show();
                $('.div-department').hide();
			} else if (type_kpi == 2) {
				$('.div-staff').hide();
                $('.div-department').show();
			}

			loadKpiCriteria();
			totalKpi();
		});

		totalKpi();
		appValidateForm($('#handling-kpi'), {
			reference_no: 'required',
			month: 'required',
			year: 'required',
			// staff: 'required',
			start_date: 'required',
			end_date: 'required',
			target_reception_time: 'required',
		}, handlingKPI);

		function handlingKPI(form) {
			if (grand_total_weight_number != WARNING_WEIGHT_NUMBER_KPI) {
				alert_float('danger', 'Tổng trọng số I phải = '+WARNING_WEIGHT_NUMBER_KPI);
				return;
			}

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
					url: url,
					type: 'POST',
					dataType: 'JSON',
					cache: false,
					contentType: false,
					processData: false,
					data: formData,
				})
				.done(function(data) {
					if (data.result) {
						alert_float('success', data.message);
						window.location.href = site.base_url + 'admin/kpi/list';
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

	$(document).ready(function () {
		$('.action-menu').click();
	});
</script>