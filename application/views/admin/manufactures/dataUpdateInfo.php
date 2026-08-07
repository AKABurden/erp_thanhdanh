<?php $value['id'] = $ois_id ?>
<?php if (!empty($update_info_stage)): ?>
<input type="hidden" name="max_id" id="max_id" class="form-control max_id" value="<?= $maxId ?>">
<?php foreach ($update_info_stage as $k => $val): ?>
	<tr data-id="<?= $value['id'] ?>">
		<td class="text-center td-number"><?= ++$k ?></td>
		<td>
			<input type="hidden" name="update_info_stage_id" id="update_info_stage_id" class="form-control update_info_stage_id" value="<?= $val['id'] ?>">
			<div class="show-edit hide">
				<select name="employee_id" id="employee_id" data-placeholder="<?= lang('chosen') ?>" class="employee_id" style="width: 100%;">
					<option></option>
					<?php foreach ($employees as $e => $v): ?>
						<option <?= $v['staffid'] == $val['employee_id'] ? 'selected' : '' ?> value="<?= $v['staffid'] ?>"><?= $v['fullname'] ?></option>
					<?php endforeach ?>
				});
				</select>
			</div>
			<div class="hide-edit">
				<?= $val['employee_name'] ?>
			</div>
		</td>
		<td>
			<div class="show-edit hide">
				<input type="text" name="datetime_start" placeholder="<?= lang('tnh_datetime_start') ?>" id="datetime_start" class="form-control datepicker datetime_start" value="<?= _d($val['date_start']) ?>" title="">
			</div>
			<div class="hide-edit">
				<?= _d($val['date_start']) ?>
			</div>
		</td>
		<td>
			<div class="show-edit hide">
				<input type="text" name="datetime_end" placeholder="<?= lang('tnh_datetime_end') ?>" id="datetime_end" class="form-control datepicker datetime_end" value="<?= _d($val['date_end']) ?>" title="">
			</div>
			<div class="hide-edit">
				<?= _d($val['date_end']) ?>
			</div>
		</td>
		<td class="text-center">
			<div class="td-total-time">
				<?= $val['total_time'] ?>
			</div>
		</td>
		<td class="text-center">
			<div class="show-edit hide">
				<input type="number" name="quantity_bad" id="quantity_bad" class="form-control quantity_bad" value="<?= $val['quantity_bad'] ?>">
			</div>
			<div class="hide-edit">
				<?= formatNumber($val['quantity_bad']) ?>
			</div>
		</td>
		<td class="text-center">
			<div class="show-edit hide">
				<input type="number" name="quantity_success" id="quantity_success" class="form-control quantity_success" readonly value="<?= $val['quantity_success'] ?>">
			</div>
			<div class="hide-edit">
				<?= formatNumber($val['quantity_success']) ?>
			</div>
		</td>
		<td class="text-center">
			<?php if ($maxId == $val['id']): ?>
			<div class="show-edit hide">
				<i onclick="updateSub(this, <?= $value['id'] ?>)" class="btn btn-success fa fa-save up-sub"></i>
				<i title="<?= lang('close') ?>" onclick="closeEdit(this, <?= $value['id'] ?>)" class="btn btn-danger fa fa-undo"></i>
			</div>
			<div class="hide-edit">
				<i onclick="showEdit(this, <?= $value['id'] ?>)" title="<?= lang('edit') ?>" class="btn btn-primary fa fa-pencil up-sub"></i>
				<i onclick="removeDetail(this, <?= $val['id'] ?>, <?= $value['id'] ?>)" title="<?= lang('delete') ?>" class="btn btn-warning fa fa-remove"></i>
			</div>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
<?php endif ?>