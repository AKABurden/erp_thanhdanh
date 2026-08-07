<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
   <div class="panel_s user-data">
      <div class="panel-body">
         <div class="widget-dragger"></div>
         <div class="panel panel-primary">
            <div class="panel-heading"><?=_l('groups_client')?></div>
            <div class="panel-body">
               <div>
                  <img src="<?=base_url('uploads/group_client.png')?>" width="35" height="35">
               </div>
               <div class="font_2em info">
                   <?php $groups_customer = get_table_where(db_prefix().'customers_groups', [],'','row','','count(id) as maxid'); ?>
                   <?=number_format($groups_customer->maxid)?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
