<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('finance_overview'); ?>">
   <div class="finance-summary">
      <div class="panel_s">
         <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="row home-summary">
               <!-- <?php echo form_open('admin/flow_chart/add',array('id'=>'suppliers-group-modal','style'=>'text-align: right')); ?>
                  <div class="users-diagram">
                     <div class="items-user">
                        <input type="hidden" name="itemUser[0][id]" value="0">
                        <input type="hidden" name="itemUser[0][name]" value="John Smith">
                        <input type="hidden" name="itemUser[0][title]" value="CEO">
                        <input type="hidden" name="itemUser[0][boss]" value="">
                        <input type="hidden" name="itemUser[0][comment]" value="The CEO of this great company">
                     </div>
                  </div>
                  <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
               <?php echo form_close(); ?> -->
               <div style="width: 100%; height: 100%; overflow: auto;text-align: center;">
                  <canvas id="diagram">
                  </canvas>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script src="<?= base_url('assets/plugins/OrgChart/MindFusion.Common.js');?>" type="text/javascript"></script>
<script src="<?= base_url('assets/plugins/OrgChart/MindFusion.Diagramming.js');?>" type="text/javascript"></script>
<?php $this->load->view('admin/flow_chart/OrgChartEditor_js'); ?>