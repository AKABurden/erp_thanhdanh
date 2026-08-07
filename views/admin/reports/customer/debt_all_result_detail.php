<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
  .table-debt-all-result-detail tbody tr td:nth-child(5){
    text-align: right;
  }
  .table-debt-all-result-detail tbody tr td:nth-child(6){
    text-align: right;
  }
  .table-debt-all-result-detail tbody tr td:nth-child(7){
    text-align: right;
  }
  .table-debt-all-result-detail tbody tr td:nth-child(8){
    text-align: right;
  }
</style>
<div id="debt-all-result-detail" class="view-report hide">
      <table class="table table-debt-all-result-detail scroll-responsive">
         <thead>
            <tr>
               <th class="text-center" rowspan="2"><?php echo _l('ch_accounting_date'); ?></th>
               <th class="text-center" rowspan="2"><?php echo _l('ch_date_p'); ?></th>
               <th class="text-center" rowspan="2"><?php echo _l('ch_code_p'); ?></th>
               <th class="text-center" rowspan="2"><?php echo _l('ch_explain'); ?></th>
               <th class="text-center" colspan="2"><?php echo _l('ch_incurred'); ?></th>
               <th class="text-center" colspan="2"><?php echo _l('ch_surplus'); ?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('chch_debt'); ?></th>
               <th class="text-center"><?php echo _l('chch_co'); ?></th>
               <th class="text-center"><?php echo _l('chch_debt'); ?></th>
               <th class="text-center"><?php echo _l('chch_co'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <td colspan="4"><?=_l('invoice_dt_table_heading_amount')?></td>
            <td class="text-right total1"></td>
            <td class="text-right total2"></td>
            <td class="text-right total3"></td>
            <td class="text-right total4"></td>
         </tfoot>
      </table>
   </div>