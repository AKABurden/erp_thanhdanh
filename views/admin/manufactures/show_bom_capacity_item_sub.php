<div>
	<table id="tb-sub-items<?= $capacity_item_id ?>" class="tnh-tb table-bordered table-hover">
		<thead>
			<tr>
				<th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
				<th class="text-center"><?= lang('tnh_item_code') ?></th>
				<th class="text-center"><?= lang('tnh_item_name') ?></th>
				<th class="text-center"><?= lang('type') ?></th>
				<th class="text-center"><?= lang('unit') ?></th>
				<th class="text-center"><?= lang('tnh_quantity_use') ?></th>
				<th class="text-center"><?= lang('tnh_quantity_purchase') ?></th>
			</tr>
		</thead>
		<tbody>
			<?= $htmlItems ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tb-sub-items<?= $capacity_item_id ?>').DataTable({
			"language": app.lang.datatables,
			"pageLength": app.options.tables_pagination_limit,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
			"initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
		});
	});
</script>