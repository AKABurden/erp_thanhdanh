<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
	<div class="col-md-12">
		<table class="tnh-table-settings">
			<tr>
				<td class="text-primary bg-primary bold"><?= lang('quotes') ?></td>
			</tr>
			<tr>
				<td><?= lang('note') ?></td>
			</tr>
			<tr>
				<td>
                    
                    <textarea name="settings[note_quotes]" id="note_quotes" class="form-control settings[note_quotes]" rows="3"><?= get_option('note_quotes') ?></textarea>
				</td>
			</tr>
		</table>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		init_editor('#note_quotes');
	});
</script>