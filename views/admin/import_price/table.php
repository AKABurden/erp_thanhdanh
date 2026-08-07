<table class="table detail_supplier">
    <thead>
    <th class="text-center"><a class="btn btn-info btn-icon add_item_detail" onclick="createItemsDetail(this)"><i class="fa fa-plus"></i> Thêm</a></th>
    <th class="text-center"><?php echo _l('Hình ảnh'); ?></th>
    <th class="text-center"><?php echo _l('Mã hàng'); ?></th>
    <th class="text-center"><?php echo _l('Tên hàng'); ?></th>
    <th class="text-center"><?php echo _l('price'); ?></th>
    <th class="text-center"><?php echo _l('dt_product_type') ?></th>
    </thead>
    <tbody>
	<?php foreach ($data as $key => $value) { ?>
		<?php $item = get_full_item($value->product_id, $value->product_type);
		?>
        <tr>
            <td class="text-center"><?php echo $key + 1; ?></td>
            <td class="text-center"><?php
				echo "<img src='" . $item->avatar_1 . "' width='50px' height='50px' />";
				//
				?>
            </td>
            <td><?php echo $item->code; ?></td>
            <td><?php echo $item->name; ?></td>
            <td class="text-right">
                <div class="type_v1">
					<?= dt_EditColumSelectInput_pricesupplier(formatNumber($value->price), $value->id, '', '<a class="pointer" id="quantitys_text_v2_' . $value->id . '" target="_blank" >' . formatNumber($value->price) . '</a>', '', admin_url('import_price/quantity/' . $value->id . '/' . $supplier_price->id), 'class="formUpdateDataTable"') ?></div>
                <div class="type_v2 hide" data-id="<?= $value->id ?>" class="quantitys_input"><input onkeyup="formatNumBerKeyUpCus(this)" type="text" name="quantitys" id="quantitys" class="height_auto  quantitys H_input align_right" value="<?= formatNumber($value->price) ?>"></div>
            </td>
            <td class="text-center"><?= format_item_purchases($value->product_type) ?></td>
        </tr>
	<?php } ?>
    </tbody>
</table>
<script type="text/javascript">
    $(document).ready(function() {
        dtItems = $('.detail_supplier').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "ordering": false,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: '300px',
            // fixedColumns:   {
            //     leftColumns: 4,
            //     rightColumns: 0
            // },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            dom: 'Blfrtip',
            buttons: [
                'excelHtml5'
            ],
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            }
        });
        setTimeout(function() {
            dtItems.draw('page');
        }, 150);
    });
</script>