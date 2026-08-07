<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-detail_debt-report thead tr th{
        text-align: center;
    }
    .table-detail_debt-report tbody tr td:nth-child(1){
        white-space: inherit;
    }
    .table-detail_debt-report tbody tr td:nth-child(2){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
    }
    .table-detail_debt-report tbody tr td:nth-child(3){
        white-space: inherit;
    }
    .table-detail_debt-report tbody tr td:nth-child(4){
        white-space: inherit;
    }
    .table-detail_debt-report tbody tr td:nth-child(5){
        white-space: inherit;
        text-align: center;
    }
    .table-detail_debt-report tbody tr td:nth-child(6){
        white-space: inherit;
        text-align: right;
    }
    .table-detail_debt-report tbody tr td:nth-child(7){
        white-space: inherit;
        text-align: right;
    }
    .table-detail_debt-report tbody tr td:nth-child(8){
        white-space: inherit;
        text-align: right;
    }
    .table-detail_debt-report tbody tr td:nth-child(9){
        white-space: inherit;
        text-align: right;
    }
    .table-detail_debt-report tbody tr td:nth-child(10){
        white-space: inherit;
        text-align: right;
        min-width: 100px;

    }
    .table-detail_debt-report tbody tr td:nth-child(11){
        white-space: inherit;
        text-align: right;
        min-width: 100px;
    }
    .table-detail_debt-report tbody tr td:nth-child(12){
        white-space: inherit;
        text-align: right;
        min-width: 100px;
    }
</style>
    <div id="detail_debt-report" class="hide">
      <table class="table table table-striped table-detail_debt-report">
         <thead>
            <tr>
               <th  class="text-center"><?php echo ucwords(_l('ch_date_p')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_code_p')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('supplier')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('tnh_dvt')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('invoice_table_quantity_heading')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_price')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_payables')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_returns_discounts')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_dicount_other')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_number_paid')); ?></th>
               <th  class="text-center"><?php echo ucwords(_l('ch_surplus')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
     <!--     <tfoot>
            <tr>
               <td colspan="2">Tổng cộng</td>
               <td class="debt_start text-right"></td>
               <td class="pay_start  text-right"></td>
               <td class="debt text-right"></td>
               <td class="pay text-right"></td>
               <td class="debt_end text-right"></td>
               <td class="pay_end text-right"></td>
            </tr>
         </tfoot> -->
      </table>
   </div>
