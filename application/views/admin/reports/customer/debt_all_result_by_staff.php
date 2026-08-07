<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-debt-all-result-by-staff tbody tr td:nth-child(3){
        text-align: right;
    }
    .table-debt-all-result-by-staff tbody tr td:nth-child(4){
        text-align: right;
    }
    .table-debt-all-result-by-staff tbody tr td:nth-child(5){
        text-align: right;
    }
    .table-debt-all-result-by-staff tbody tr td:nth-child(6){
        text-align: right;
    }
    .table-debt-all-result-by-staff tbody tr td:nth-child(7){
        text-align: right;
    }
    .table-debt-all-result-by-staff tbody tr td:nth-child(8){
        text-align: right;
    }
</style>
<div id="debt-all-result-by-staff" class="view-report hide">
      <table class="table table-debt-all-result-by-staff scroll-responsive">
         <thead>
            <tr>
               <th class="text-center" rowspan="2"><?php echo _l('code_customer'); ?></th>
               <th class="text-center" rowspan="2"><?php echo _l('name_customer'); ?></th>
               <th class="text-center" colspan="2"><?php echo _l('opening_balance'); ?></th>
               <th class="text-center" colspan="2"><?php echo _l('ch_incurred'); ?></th>
               <th class="text-center" colspan="2"><?php echo _l('ch_ending_balance'); ?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('chch_debt'); ?></th>
               <th class="text-center"><?php echo _l('chch_co'); ?></th>
               <th class="text-center"><?php echo _l('chch_debt'); ?></th>
               <th class="text-center"><?php echo _l('chch_co'); ?></th>
               <th class="text-center"><?php echo _l('chch_debt'); ?></th>
               <th class="text-center"><?php echo _l('chch_co'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <td colspan="2"><?=_l('invoice_dt_table_heading_amount')?></td>
            <td class="text-right total1"></td>
            <td class="text-right total2"></td>
            <td class="text-right total3"></td>
            <td class="text-right total4"></td>
            <td class="text-right total5"></td>
            <td class="text-right total6"></td>
         </tfoot>
      </table>
   </div>