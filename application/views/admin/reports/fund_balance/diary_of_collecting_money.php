   <style>
      .table-diary-of-collecting-money tbody tr td:nth-child(1){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-collecting-money tbody tr td:nth-child(2){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-collecting-money tbody tr td:nth-child(3){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-collecting-money tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
      }
      .table-diary-of-collecting-money tbody tr td:nth-child(5){
        white-space: inherit;
        text-align: center;
        min-width: 100px;
      }
      .table-diary-of-collecting-money tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 200px;
      }
      .table-diary-of-collecting-money tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
        text-align: right;
      }
   </style>
    <div id="diary-of-collecting-money" class="hide">
      <table class="table table table-striped table-diary-of-collecting-money">
         <thead>
            <tr>
               <th class="text-center"><?php echo ucwords(_l('custom_field_staff')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_number_code')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('estimate_dt_table_heading_date')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('cong_object')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('ch_list_code')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('announcement_message')); ?></th>
               <th class="text-center"><?php echo ucwords(_l('expense_add_edit_amount')); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td class="text-center" colspan="6"></td>
               <td class="total text-right"></td>
            </tr>
         </tfoot>
      </table>
   </div>
   