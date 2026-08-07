<table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
    <thead>
    <tr>
        <th class="text-center" style="width: 40px;"><?= lang('tnh_numbers') ?></th>
        <th><?= lang('Mã thiết bị') ?></th>
        <th><?= lang('Tên thiết bị') ?></th>
        <th><?= lang('Nhà cung cấp') ?></th>
        <th class="text-center"><?= lang('quantity') ?></th>
        <th class="text-center"><?= lang('Đơn giá') ?></th>
        <th class="text-center"><?= lang('Thành tiền') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($dtDataItems)){ ?>
        <?php foreach ($dtDataItems as $key => $value){ ?>
            <?php
            $machines_id = $value['machines_id'];
            $dtMachines = get_table_where('tbl_materials',['id' => $machines_id],'','row_array');

            ?>
            <tr>
                <td class="text-center"><?= (++$key) ?></td>
                <td><div class="code_item">
                        <?= $dtMachines['code'] ?>
                    </div>
                </td>
                <td><div class="name_item"><?= $dtMachines['name'] ?></div></td>
                <td><div class="supplier_name"><?= $value['company'] ?></div></td>
                <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                <td class="text-right"><?= formatMoney($value['price']) ?></td>
                <td class="text-right"><?= formatMoney($value['amount']) ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" class="text-center bold" style="text-transform: uppercase;"><?= lang('tnh_grand_total') ?></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </tfoot>
</table>