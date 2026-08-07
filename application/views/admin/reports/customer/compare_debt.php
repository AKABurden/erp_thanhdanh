<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
  .table-compare-debt thead tr {
    background: #f6f8fa;
  }
  .table-compare-debt thead tr th {
    border: 1px solid #93b4d6 !important;
  }
  .no-select {
    background: #ffe7e7;
  }
</style>
<div id="compare-debt" class="view-report hide">
  <table class="table table-compare-debt scroll-responsive">
     <thead>
        <tr>
           <th class="text-center" colspan="4"><?php echo _l('document_debt'); ?></th>
           <th class="text-center" colspan="3"><?php echo _l('document_payment'); ?></th>
           <th class="text-center" rowspan="2"><?php echo _l('difference'); ?></th>
        </tr>
        <tr>
           <th class="text-center"><?php echo _l('ch_date_p'); ?></th>
           <th class="text-center"><?php echo _l('ch_code_p'); ?></th>
           <th class="text-center"><?php echo _l('ch_code_invoice'); ?></th>
           <th class="text-center"><?php echo _l('exchange_amount_value'); ?></th>
           <th class="text-center"><?php echo _l('ch_date_p'); ?></th>
           <th class="text-center"><?php echo _l('ch_code_p'); ?></th>
           <th class="text-center"><?php echo _l('exchange_amount_value'); ?></th>
        </tr>
     </thead>
     <tbody>
       <tr class="no-select">
         <td colspan="8">
           <?=_l('select_customer_choise')?>
         </td>
       </tr>
     </tbody>
  </table>
</div>