   <style>
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(1){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(2){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(3){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
      }
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(5){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 200px;
      }
      .table-diary-of-revenue-and-expenditure tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
      }
   </style>
    <div id="diary-of-revenue-and-expenditure" class="hide">
      <table class="table table table-striped table-diary-of-revenue-and-expenditure">
         <thead>
            <tr>
               <th class="text-center"><?php echo ucwords(_l('custom_field_staff')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_number_code')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('estimate_dt_table_heading_date')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('cong_object')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_list_code')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('announcement_message')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_collect_money')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('chch_pay')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td class="text-center" colspan="6"></td>
               <td class="thu text-right"></td>
               <td class="chi text-right"></td>
            </tr>
         </tfoot>
      </table>
   </div>
   