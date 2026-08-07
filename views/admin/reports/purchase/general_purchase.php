<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-general-purchase-report thead tr th{
        text-align: center;
    }
    .table-general-purchase-report tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 300px;
    }
    .table-general-purchase-report tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 80px;
        text-align: center;
    }
    .table-general-purchase-report tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-general-purchase-report tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-general-purchase-report tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 110px;
        text-align: center;
    }
    .table-general-purchase-report tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-general-purchase-report tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 110px;
        text-align: right;
    }
    .table-general-purchase-report tbody tr td:nth-child(9){
        white-space: inherit;
        min-width: 120px;
        text-align: right;
    }
</style>
<div id="general-purchase-report" class="hide">
      <table class="table table-general-purchase-report scroll-responsive">
         <thead>
            <tr>
               <th><?php echo ucwords(_l('code_item')); ?></th>
               <th><?php echo ucwords(_l('name_item')); ?></th>
               <th><?php echo ucwords(_l('item_unit')); ?></th>
               <th><?php echo ucwords(_l('ch_quantity_purchased')); ?></th>
               <th><?php echo ucwords(_l('ch_worth_buying')); ?></th>
               <th><?php echo ucwords(_l('ch_quantitli_returned')); ?></th>
               <th><?php echo ucwords(_l('ch_return_value')); ?></th>
               <th><?php echo ucwords(_l('ch_value_discount')); ?></th>
               <th><?php echo ucwords(_l('ch_total_purchase_value')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <td colspan="3"><?=_l('invoice_dt_table_heading_amount')?></td>
            <td class="quantity text-center"></td>
            <td class="total_s text-right"></td>
            <td class="quantity_return text-center"></td>
            <td class="total_return text-right"></td>
            <td class="promotion_expected text-right"></td>
            <td class="subtotal text-right"></td>
         </tfoot>
      </table>
   </div>