<div id="warehouse-limit_user_date-report" class="hide">
    <?php
    $limit_date = get_option('limit_date');
    if (empty($limit_date)) {
        $limit_date = 0;
    }
     ?>
    <div><p><?=_l('number_date_limit')?>: <span style="color:red;"><?=$limit_date?></span></p></div>
    <table class="table table table-striped table-warehouse-limit_user_date-report">
        <thead>
            <tr>
                <th class="text-center"><?php echo ucwords(_l('code_item')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('name_item')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('tnh_dvt')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('warehouse')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('tnh_vt')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Ngày nhập kho')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Số ngày lưu kho')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('ch_items_date_use')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('quantity')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Tình trạng')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('cong_price_thinh')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('cong_info_money')); ?></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>