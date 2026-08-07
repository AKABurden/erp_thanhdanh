<div style="padding-left: 120px;">
	<table id="sub-products-<?= $product_id ?>" class="tnh-tb table-bordered table-hover">
		<thead>
			<tr class="">
				<th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
				<th class="text-center" style="width: 100px;"><?= lang('Mã NPL/BTP') ?></th>
				<th class="text-center" style="width: 100px;"><?= lang('Tên NPL/BTP') ?></th>
				<th class="text-center" style="width: 50px;"><?= lang('unit') ?></th>
				<th class="text-center" style="width: 80px;"><?= lang('type') ?></th>

				<th class="text-center" style="width: 50px;"><?= lang('tnh_landscape_print_size') ?></th>
				<th class="text-center" style="width: 50px;"><?= lang('tnh_number_children_size') ?></th>
				<th class="text-center" style="width: 50px;"><?= lang('tnh_exchange_value') ?></th>
				<th class="text-center" style="width: 50px;"><?= lang('tnh_paper_exchange') ?></th>
				<th class="text-center" style="width: 50px;"><?= lang('tnh_quantity_compensation') ?></th>
				<th class="text-center" style="width: 50px;"><?= lang('tnh_stage') ?></th>
			</tr>
		</thead>
		<tbody>
			<?= $html_bom ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#sub-products-<?= $product_id ?>').DataTable({
			"language": app.lang.datatables,
			"pageLength": app.options.tables_pagination_limit,
			// "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
			"initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                // mainWrapperHeightFix();
            },
		});
		/*tnhDatatable(
			'#sub-products-<?= $product_id ?>',
			{
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
				"initComplete": function(settings, json) {
	                var t = this;
	                t.parents('.table-loading').removeClass('table-loading');
	                t.removeClass('dt-table-loading');
	                // mainWrapperHeightFix();
	            },
			}
		);*/
	});
</script>