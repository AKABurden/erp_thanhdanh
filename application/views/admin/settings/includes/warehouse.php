<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
	<div class="col-md-12">
		<table class="tnh-table-settings">
			<tr>
				<td class="text-primary bg-primary bold"><?= lang('warehouse') ?></td>
			</tr>
			<tr>
				<td><?= lang('number_date_limit') ?></td>
			</tr>
			<tr>
				<td>
					<input type="number" name="settings[limit_date]" id="settings[limit_date]" class="form-control" value="<?= get_option('limit_date') ?>" placeholder="<?= lang('number_date_limit') ?>">
				</td>
			</tr>
		</table>
	</div>
</div>