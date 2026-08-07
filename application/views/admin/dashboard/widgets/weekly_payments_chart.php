<!-- <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('Biểu đồ kho hàng'); ?>">
   <div class="row" id="weekly_payments">
      <div class="col-md-12">
         <div class="panel_s">
            <div class="panel-body padding-10">
               <div class="widget-dragger"></div>
               <div class="col-md-12">
                  <p class="pull-left mtop5"><?php echo _l('Biểu đồ kho hàng'); ?></p>
                  <div class="clearfix"></div>
                  <div class="col-md-12">
                  <select name="year" id="year" size="1"></select>
                  <select name="month" id="month" size="1"></select>
                  <select name="day" id="day" size="1">
                  <option value=" " selected="selected">-- <?=_l('day')?> --</option>
                  </select>
                  </div>  
                  <div class="clearfix"></div>
                  <div class="row mtop5">
                     <hr class="hr-panel-heading-dashboard">
                  </div>

                  <?php if (is_using_multiple_currencies()) { ?>
                    <select class="selectpicker pull-left mbot15" name="currency" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                       <?php foreach($currencies as $currency){
                          $selected = '';
                          if($currency['isdefault'] == 1){
                           $selected = 'selected';
                        }
                        ?>
                        <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $currency['name']; ?>"><?php echo $currency['symbol']; ?></option>
                        <?php } ?>
                     </select>
                   <?php } ?>
                   <canvas height="130" class="weekly-payments-chart-dashboard" id="weekly-payment-statistics"></canvas>
                   <div class="clearfix"></div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div> -->

