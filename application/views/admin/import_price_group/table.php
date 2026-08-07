<table class="table detail_supplier" style="width: 100%;">
    <thead>
    <tr>
        <th rowspan="2" class="text-center tdSTT" style="width: 5%;"><a class="btn btn-info btn-icon add_item_detail" onclick="createItemsDetail(this)"><i class="fa fa-plus"></i> Thêm</a></th>
        <th rowspan="2"  class="text-center tdImage" style="width: 10%;"><?php echo _l('dt_product_image'); ?></th>
        <th rowspan="2" class="text-center tdCodeProduct" style="width: 200px;"><?php echo _l('dt_product_code'); ?></th>
        <th rowspan="2" class="text-center tdNameProduct" style="width: 20%;"><?php echo _l('dt_product_name'); ?></th>
        <th colspan="2" class="text-center" style="width: 20%;"><?php echo _l('MOQ'); ?></th>
        <th rowspan="2" style="width: 15%;" class="text-center tdPrice"><?php echo _l('Giá'); ?></th>
        <th rowspan="2"  class="text-center tdType" style="width: 10%;"><?php echo _l('dt_product_type') ?></th>
    </tr>
    <tr>
        <th class="tdQuantityTo" style="width:10%;">SL Từ</th>
        <th class="tdQuantityFrom" style="width:10%;">SL Đến</th>
    </tr>
    </thead>
    <tbody>
	<?php if(!empty($data)) {?>
		<?php foreach ($data as $key => $value) {?>
            <?php if(!empty($value->id_item_data)) {
				$item = (object)[
					'avatar_1' => $value->avatar,
					'code' => $value->code_item,
					'name' => $value->name_item,
					'unit_name' => $value->unit_name,
				];
			}
			else {?>
			<?php
                $item = @get_full_item($value->product_id, $value->product_type);
			    if(empty($item->id)) continue;
            } ?>
            <tr>
                <td class="text-center">
                    <?php echo $key + 1; ?><br/><br/>
                    <a class="btn btn-icon btn-danger" onclick="removeTrDetail(<?=$value->id?>)"><i class="fa fa-remove"></i></a>
                </td>
                <td class="text-center">
					<?="<img src='" . $item->avatar_1 . "' width='50px' height='50px' />";?>
                </td>
                <td><?=$item->code?></td>
                <td><?php echo $item->name?></td>
                <td class="text-right" data-id="money_start_text_v2_">
                    <div class="type_v1">
						<?= dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($value->money_start), $value->id, '', '<a class="pointer" id="money_start_text_v2_' . $value->id . '" target="_blank" >' . number_format_data_four($value->money_start) . '</a>', '', admin_url('import_price_group/update_money/start/' . $value->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"') ?></div>
                    <div class="type_v2 hide" data-id="<?= $value->id ?>" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_start" class="height_auto  money_start H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>
                </td>
                <td class="text-right" data-id="money_end_text_v2_">
                    <div class="type_v1">
						<?= dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($value->money_end), $value->id, '', '<a class="pointer" id="money_end_text_v2_' . $value->id . '" target="_blank" >' . number_format_data_four($value->money_end) . '</a>', '', admin_url('import_price_group/update_money/end/' . $value->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"') ?></div>
                    <div class="type_v2 hide" data-id="<?= $value->id ?>" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_end" id="money_end" class="height_auto  money_end H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>
                </td>
                <td class="text-right" data-id="quantitys_text_v2_">
                    <div class="type_v1">
						<?= dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($value->price), $value->id, '', '<a class="pointer" id="quantitys_text_v2_' . $value->id . '" target="_blank" >' . number_format_data_four($value->price) . '</a>', '', admin_url('import_price_group/quantity/' . $value->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"') ?></div>
                    <div class="type_v2 hide" data-id="<?= $value->id ?>" class="quantitys_input"><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="quantitys" id="quantitys" class="height_auto  quantitys H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>
                </td>
                <td class="text-center">
					<?= !empty($value->label_span) ? $value->label_span : format_item_purchases($value->product_type) ?><br/>
                </td>
            </tr>
		<?php } ?>
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