<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
	<div class="col-md-12">
		<table class="tnh-table-settings">
			<tr>
				<td class="text-primary bg-primary bold"><?= lang('orders') ?></td>
			</tr>
			<tr>
				<td><?= lang('tnh_prefix_orders') ?></td>
			</tr>
			<tr>
				<td>
					<input type="text" name="settings[prefix_orders]" id="settings[prefix_orders]" class="form-control" value="<?= get_option('prefix_orders') ?>" placeholder="<?= lang('tnh_prefix_orders') ?>">
				</td>
			</tr>
			<tr>
				<td><?= lang('tnh_default_staff_orders') ?></td>
			</tr>
			<tr>
				<td>
					<select name="settings[default_staff_orders]" id="settings[default_staff_orders]" data-placeholder="<?= lang('tnh_default_staff_orders') ?>" class="default_staff_orders" style="width: 100%;">
						<option value=""></option>
						<?php foreach ($staff as $key => $value): ?>
							<option <?= get_option('default_staff_orders') == $value['staffid'] ? 'selected' : '' ?> value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
						<?php endforeach ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?= lang('tnh_auto_agree', 'auto_agree_orders') ?>
					<div class="from-group">
						<div class="radio radio-inline radio-primary">
							<input type="radio" name="settings[auto_agree_orders]" id="auto_agree_orders-yes" value="1" <?= get_option('auto_agree_orders') == 1 ? "checked" : '' ?>>
							<label for="auto_agree_orders-yes"><?= lang('yes') ?></label>
						</div>
						<div class="radio radio-inline radio-primary">
							<input type="radio" name="settings[auto_agree_orders]" id="auto_agree_orders-no" value="0" <?= empty(get_option('auto_agree_orders')) ? "checked" : '' ?>>
							<label for="auto_agree_orders-no"><?= lang('no') ?></label>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.default_staff_orders').select2({'allowClear': true})
	});
</script>