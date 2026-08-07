<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#tab-sub<?= $purchase_id ?>" aria-controls="tab-sub<?= $purchase_id ?>" role="tab" data-toggle="tab"><?= lang('tnh_procedure') ?></a>
		</li>
		<li role="presentation">
			<a href="#home-sub<?= $purchase_id ?>" aria-controls="home-sub<?= $purchase_id ?>" role="tab" data-toggle="tab"><?= lang('tnh_items') ?></a>
		</li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane" id="home-sub<?= $purchase_id ?>">
			<div style="">
				<table id="tb-sub-items<?= $purchase_id ?>" class="tnh-tb table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
							<th class="text-center"><?= lang('tnh_item_code') ?></th>
							<th class="text-center"><?= lang('tnh_item_name') ?></th>
							<th class="text-center"><?= lang('type') ?></th>
							<th class="text-center"><?= lang('unit') ?></th>
							<th class="text-center"><?= lang('quantity') ?></th>
							<th class="text-center"><?= lang('tnh_qty_bought') ?></th>
							<th class="text-center"><?= lang('tnh_rest') ?></th>
						</tr>
					</thead>
					<tbody>
						<?= $htmlItems ?>
					</tbody>
				</table>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane active" id="tab-sub<?= $purchase_id ?>">
			<div>
				<table id="sub-workflow" class="tnh-tb table-bordered table-hover">
					<thead>
						<tr class="">
							<th class=""><?= lang('tnh_quotes_suppliers') ?></th>
							<th class=""><?= lang('tnh_number_po') ?></th>
							<th class=""><?= lang('add_items') ?></th>
							<!-- <th class=""><?= lang('ch_return') ?></th> -->
						</tr>
					</thead>
					<tbody>
						<?= $hmtlQuotes ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tb-sub-items<?= $purchase_id ?>').DataTable({
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