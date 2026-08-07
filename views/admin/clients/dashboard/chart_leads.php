<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
   <div class="panel_s user-data">
      <div class="panel-body">
         <div class="widget-dragger"></div>
         <div class="panel panel-primary">
            <div class="panel-heading"><?=_l('chart_leads')?></div>
            <div class="panel-body">
               <div class="relative" style="height:250px">
                  <canvas class="chart" height="250" id="chart-leads_leads_time_stats"></canvas>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>