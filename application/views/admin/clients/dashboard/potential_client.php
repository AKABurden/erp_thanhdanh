<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
   <div class="panel_s user-data">
      <div class="panel-body">
         <div class="widget-dragger"></div>
         <div class="panel panel-default">
            <div class="panel-heading"><?=_l('leads')?></div>
            <div class="panel-body">
               <div>
                  <img src="<?=base_url('uploads/client_2.png')?>" width="35" height="35">
               </div>
               <div class="font_2em">
                   <?php $leads = get_table_where(db_prefix().'leads', array('status != ' => 1),'','row','','count(id) as maxid'); ?>
                  <?=number_format($leads->maxid)?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
