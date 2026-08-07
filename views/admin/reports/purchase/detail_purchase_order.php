<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-purchase_order-report thead tr th{
        text-align: center;
    }
    .table-purchase_order-report tbody tr td:nth-child(1){
        white-space: inherit;
        min-width: 100px;
    }
    .table-purchase_order-report tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 80px;
    }
    .table-purchase_order-report tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 120px;
        text-align: left;
    }
    .table-purchase_order-report tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-purchase_order-report tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 120px;
    }
    .table-purchase_order-report tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-purchase_order-report tbody tr td:nth-child(7){
        white-space: inherit;
        text-align: center;
        min-width: 80px;
    }
    .table-purchase_order-report tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 70px;
        text-align: center;
    }
    .table-purchase_order-report tbody tr td:nth-child(9){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-purchase_order-report tbody tr td:nth-child(10){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
    .table-purchase_order-report tbody tr td:nth-child(11){
        white-space: inherit;
        min-width: 100px;
    }
    .table-purchase_order-report tbody tr td:nth-child(12){
        white-space: inherit;
        min-width: 120px;
        text-align: right;
    }
    .table-purchase_order-report tbody tr td:nth-child(13){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }
</style>
<div id="detail-purchase_order-report" class="hide">
      <table class="table table-purchase_order-report scroll-responsive">
         <thead>
            <tr>
               <th><?php echo ucwords(_l('Ngày đặt hàng')); ?></th>
               <th><?php echo ucwords(_l('Số chứng từ')); ?></th>
               <th><?php echo ucwords(_l('Nhà cung cấp')); ?></th>
               <th><?php echo ucwords(_l('code_item')); ?></th>
               <th><?php echo ucwords(_l('name_item')); ?></th>
               <th><?php echo ucwords(_l('Ngày dự kiến giao hàng')); ?></th>
               <th><?php echo ucwords(_l('tnh_dvt')); ?></th>
               <th><?php echo ucwords(_l('Số lượng')); ?></th>
               <th><?php echo ucwords(_l('Số lượng đã nhập')); ?></th>
               <th><?php echo ucwords(_l('Còn lại')); ?></th>
               <th><?php echo ucwords(_l('Ngày nhập hàng')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
<!--          <tfoot>
            <td colspan="3"><?=_l('invoice_dt_table_heading_amount')?></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td class="total_quantity text-center"></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td class="subtotals text-right"></td>
         </tfoot> -->
      </table>
   </div>