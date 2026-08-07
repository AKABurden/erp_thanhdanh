<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-aggregate-fund-balance thead tr th{
        text-align: center;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(1){
        white-space: inherit;
        text-align: center;
        min-width: 50px;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 200px;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-aggregate-fund-balance tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
</style>
    <div id="aggregate-fund-balance" class="hide">
      <table class="table table table-striped table-aggregate-fund-balance">
         <thead>
            <tr>
               <th rowspan="2" class="text-center"><?php echo ucwords(_l('STT')); ?></th>
               <th rowspan="2" class="text-center"><?php echo ucwords(_l('ch_name_acounts')); ?></th>
               <th colspan="2" class="text-center"><?php echo ucwords(_l('opening_balance')); ?></th>
               <th colspan="2" class="text-center"><?php echo ucwords(_l('ch_incurred')); ?></th>
               <th colspan="2" class="text-center"><?php echo ucwords(_l('ch_ending_balance')); ?></th>
            </tr>
            <tr>
               <th class="text-center"><span class="hide"><?php echo _l('opening_balance'); ?> - </span><?php echo ucwords(_l('ch_collect_money')); ?></th>
               <th class="text-center"><span class="hide"><?php echo _l('opening_balance'); ?> - </span><?php echo ucwords(_l('chch_pay')); ?></th>
               <th class="text-center"><span class="hide"><?php echo _l('ch_incurred'); ?> - </span><?php echo ucwords(_l('ch_collect_money')); ?></th>
               <th class="text-center"><span class="hide"><?php echo _l('ch_incurred'); ?> - </span><?php echo ucwords(_l('chch_pay')); ?></th>
               <th class="text-center"><span class="hide"><?php echo _l('ch_ending_balance'); ?> - </span><?php echo ucwords(_l('ch_collect_money')); ?></th>
               <th class="text-center"><span class="hide"><?php echo _l('ch_ending_balance'); ?> - </span><?php echo ucwords(_l('chch_pay')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td class="text-center" colspan="2"><?=ucwords(_l('invoice_dt_table_heading_amount'))?></td>
               <td class="debt_start text-right"></td>
               <td class="pay_start  text-right"></td>
               <td class="debt text-right"></td>
               <td class="pay text-right"></td>
               <td class="debt_end text-right"></td>
               <td class="pay_end text-right"></td>
            </tr>
         </tfoot>
      </table>
   </div>
