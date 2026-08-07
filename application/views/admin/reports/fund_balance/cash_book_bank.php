   <style>
      .table-cash-book-bank tbody tr td:nth-child(1){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-cash-book-bank tbody tr:not(:first-child) td:nth-child(2){
        white-space: inherit;
        text-align: center;
        min-width: 120px;
      }
      .table-cash-book-bank tbody tr td:nth-child(3){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-cash-book-bank tbody tr:not(:first-child) td:nth-child(4){
        white-space: inherit;
        min-width: 200px;
      }
      .table-cash-book-bank tbody tr:not(:first-child) td:nth-child(5){
        white-space: inherit;
        min-width: 200px;
      }
      .table-cash-book-bank tbody tr td:nth-child(6){
        white-space: inherit;
        text-align: right;
        min-width: 100px;
      }
      .table-cash-book-bank tbody tr td:nth-child(7){
        white-space: inherit;
        text-align: right;
        min-width: 100px;
      }
      .table-cash-book-bank tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
      }
   </style>
    <div id="cash-book-bank" class="hide">
      <table class="table table table-striped table-cash-book-bank">
         <thead>
           <tr>
               <th class="text-center"><?php echo ucwords(_l('estimate_dt_table_heading_date')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('custom_field_staff')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_number_code')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('cong_object')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('announcement_message')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_collect_money')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_pay')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_fund_balance')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td class="text-center" colspan="4"><?=mb_strtoupper(_l('ch_ending_balance'), 'UTF-8')?></td>
               <td></td>
               <td class="thu text-right"></td>
               <td class="chi text-right"></td>
               <td class="total text-right"></td>
            </tr>
         </tfoot>
      </table>
   </div>

  
   