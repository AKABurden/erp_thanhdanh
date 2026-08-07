<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
   <div class="panel_s user-data">
      <div class="panel-body">
         <div class="widget-dragger"></div>
         <div class="panel panel-default">
            <div class="panel-heading"><?=_l('contact')?></div>
            <div class="panel-body">
               <div>
                  <img src="<?=base_url('uploads/contact.png')?>" width="35" height="35">
               </div>
               <div class="font_2em">
                   <?php
                       $contact = get_table_where('tblcontacts',[],'','row','','count(id) as maxid');
                   ?>
                  <?=number_format($contact->maxid);?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
