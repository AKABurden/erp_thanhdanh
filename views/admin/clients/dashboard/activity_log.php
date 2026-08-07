<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
    <div class="panel_s user-data">
        <div class="panel-body">
          <div class="widget-dragger"></div>
          <div class="panel panel-primary">
            <div class="panel-heading"><?=_l('activity_log_puchases')?></div>
            <div class="panel-body" style="text-align: left;">
              <div class="form-group">
                  <label for="date_space_activity"><?=_l('cong_automations_time')?></label>
                  <div class="input-group" style="width: 100%;">
                      <input type="text" id="date_space_activity" class="form-control date_space_activity" aria-invalid="false" data-module='client'>
                      <div class="input-group-addon">
                          <i class="fa fa-calendar calendar-icon"></i>
                      </div>
                  </div>
              </div>
              <div class="form-group">
                  <?php echo render_select('staff_activity',$staff,array('staffid','name'),'by_staff_log','',array('data-module'=>'client')); ?>
              </div>
              <hr />
              <div class="activity-container" style="max-height: 500px;">
                  <?php foreach ($dataLog as $key => $value) { ?>
                      <div class="feed-item">
                          <div class="activity-text">
                              <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                          </div>
                          <div class="activity-time">
                              <?= time_ago($value['date']) ?> <span class="activity-module"><?=_l($value['table_obj'])?></span>
                          </div>
                          <div>
                              <?=$value['content']?>
                          </div>
                      </div>
                <?php } ?>
              </div>
              <div class="text-center">
                  <a class="btn btn-info more_log" onclick="load_more_log('client'); return false;"><?=_l('load_more')?></a>
              </div>
            </div>
          </div>
      </div>
   </div>
</div>
<script>
  $(function(){
    active_daterangepicker();
  });
  var active_daterangepicker = () => {
      $('.date_space_activity').daterangepicker({
          opens: 'left',
          autoUpdateInput: false, 
          isInvalidDate: false,
          "locale": {
              "format": "DD/MM/YYYY",
              "separator": " - ",
              "applyLabel": lang_daterangepicker.applyLabel,
              "cancelLabel": lang_daterangepicker.cancelLabel,
              "fromLabel": lang_daterangepicker.fromLabel,
              "toLabel": lang_daterangepicker.toLabel,
              "customRangeLabel": lang_daterangepicker.customRangeLabel,
              "daysOfWeek": lang_daterangepicker.daysOfWeek,
              "monthNames": lang_daterangepicker.monthNames
          },
      }, function(start, end, label) {
      });
      $('.date_space_activity').val('').datepicker("refresh");
      $('.date_space_activity').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
          $( "#date_space_activity" ).trigger( "change" );
      });
  };
</script>