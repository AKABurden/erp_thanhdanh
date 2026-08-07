<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
      <div class="left">
        <span class="bold uppercase fsize18 H_title"><?=$title?></span>
      </div>
    </div>
  </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                   <?php render_datatable(array(
                       _l('name'),
                       _l('cong_day_birtday'),
                       _l('cong_phone'),
                       _l('cong_email'),
                   ),'staff_birthday_of_month table-bordered'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
  initDataTable('.table-staff_birthday_of_month', admin_url + 'dashboard/table_staff_birthday_of_month' , [0], [0], [], [1, 'desc']);
});
</script>
</body>
</html>
