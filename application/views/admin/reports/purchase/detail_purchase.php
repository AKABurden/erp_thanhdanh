<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-detail-purchase-report thead tr th{
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(1){
        white-space: inherit;
        min-width: 120px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 110px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 200px;
    }
    .table-detail-purchase-report tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 70px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(9){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-detail-purchase-report tbody tr td:nth-child(10){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-detail-purchase-report tbody tr td:nth-child(11){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-detail-purchase-report tbody tr td:nth-child(12){
        white-space: inherit;
        min-width: 120px;
        text-align: right;
    }
    .table-detail-purchase-report tbody tr td:nth-child(13){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-detail-purchase-report tbody tr td:nth-child(14){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
</style>
<div id="detail-purchase-report" class="hide">
      <table class="table table-detail-purchase-report scroll-responsive">
         <thead>
            <tr>
               <th><?php echo ucwords(_l('Ngày đặt hàng')); ?></th>
               <th><?php echo ucwords(_l('ch_date_p')); ?></th>
               <th><?php echo ucwords(_l('Số ycmh')); ?></th>
               <th><?php echo ucwords(_l('cong_code_orders')); ?></th>
               <th><?php echo ucwords(_l('Mã phiếu nhập')); ?></th>
               <th><?php echo ucwords(_l('supplier')); ?></th>
               <th><?php echo ucwords(_l('ch_date_invoice')); ?></th>
               <th><?php echo ucwords(_l('tnh_reference_bill')); ?></th>
               <th><?php echo ucwords(_l('code_item')); ?></th>
               <th><?php echo ucwords(_l('name_item')); ?></th>
               <th><?php echo ucwords(_l('tnh_dvt')); ?></th>
               <th><?php echo ucwords(_l('item_quantity')); ?></th>
               <th><?php echo ucwords(_l('ch_price')); ?></th>
               <th><?php echo ucwords(_l('Tổng tiền trước thuế')); ?></th>
               <th><?php echo ucwords(_l('tnh_money_tax')); ?></th>
               <th><?php echo ucwords(_l('ch_ncc_promotion')); ?></th>
               <th><?php echo ucwords(_l('Tổng tiền sau thuế')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
             <tr>
                 <td><?=_l('invoice_dt_table_heading_amount')?></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td ></td>
                 <td ></td>
                 <td ></td>
                 <td ></td>
                 <td ></td>
                 <td ></td>
                 <td ></td>
                 <td class="total_quantity text-center"></td>
                 <td ></td>
                 <td class="total_amount text-right"></td>
                 <td class="total_tax text-right"></td>
                 <td class="total_pro text-right"></td>
                 <td class="subtotals text-right"></td>
             </tr>
         </tfoot>
      </table>
   </div>