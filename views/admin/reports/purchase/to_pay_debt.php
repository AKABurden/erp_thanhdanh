<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-to_pay_debt-report thead tr th{
        text-align: center;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(1){
        white-space: inherit;
        min-width: 120px;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 200px;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-to_pay_debt-report tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
</style>
    <div id="to_pay_debt-report" class="hide">
      <table class="table table table-striped table-to_pay_debt-report">
         <thead>
            <tr>
               <th rowspan="2" class="text-center"><?php echo ucwords(_l('ch_code_suppliers')); ?></th>
               <th rowspan="2" class="text-center"><?php echo ucwords(_l('ch_company')); ?></th>
               <th colspan="2" class="text-center"><?php echo ucwords(_l('opening_balance')); ?></th>
               <th colspan="2" class="text-center"><?php echo ucwords(_l('ch_incurred')); ?></th>
               <th colspan="2" class="text-center"><?php echo ucwords(_l('ch_ending_balance')); ?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo ucwords(_l('chch_debt')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_pay')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_debt')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_pay')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_debt')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_pay')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td colspan="2"><?=_l('invoice_dt_table_heading_amount')?></td>
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
