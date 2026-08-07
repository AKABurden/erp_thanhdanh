    <div id="warehouse-inventory-report" class="hide">
       <table class="table table table-striped table-warehouse-inventory-report">
          <thead>
             <tr>
                <th class="text-center"><?= ucwords(_l('Danh mục')); ?></th>
                <th class="text-center"><?= ucwords(_l('tnh_item_code')); ?></th>
                <th class="text-center"><?= ucwords(_l('tnh_item_code')); ?></th>
                <th class="text-center"><?= ucwords(_l('item_unit')); ?></th>
                <th class="text-center"><?= ucwords(_l('inventory_begin')); ?> <?= _l('quantity') ?></th>
                <th class="text-center"><?= ucwords(_l('inventory_begin')); ?> <?= _l('amount') ?></th>
                <th class="text-center"><?= ucwords(_l('warehousing')); ?> <?= _l('quantity') ?></th>
                <th class="text-center"><?= ucwords(_l('warehousing')); ?> <?= _l('amount') ?></th>
                <th class="text-center"><?= ucwords(_l('tnh_export_warehouses')); ?> <?= _l('quantity') ?></th>
                <th class="text-center"><?= ucwords(_l('tnh_export_warehouses')); ?> <?= _l('amount') ?></th>
                <th class="text-center"><?= ucwords(_l('inventory_end')); ?> <?= _l('quantity') ?></th>
                <th class="text-center"><?= ucwords(_l('inventory_end')); ?> <?= _l('amount') ?></th>
             </tr>
             <!-- <tr>
               <th class="text-center"><?php echo ucwords(_l('quantity')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('amount')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('quantity')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('amount')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('quantity')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('amount')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('quantity')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('amount')); ?></th>
            </tr> -->
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