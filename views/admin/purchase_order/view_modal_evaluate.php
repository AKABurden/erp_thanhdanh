<div class="modal fade" id="evaluate_modal_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title add_title_evaluate"><?php echo _l('evaluate'); ?></h4>
        <h4 class="modal-title edit_title_evaluate"><?php echo _l('update_evaluate'); ?></h4>
      </div>
      <?php echo form_open('admin/purchase_invoice/add_evaluate',array('id'=>'evaluate_form')); ?>
      <div class="modal-body">
          <div class="panel_s">
            <div class="panel-body">
              <div class="text-center bold uppercase fsize18"><?=_l('rate_evaluate')?></div>
              <hr />
              <div>
                <input type="hidden" name="points" class="points" value="<?=(isset($dataMain) ? $dataMain->points : '')?>">
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 1 ? 'active' : '')?>" data-points="1">
                  <img src="<?=base_url('uploads/icon_rate/1.png');?>">
                  <div class="bold uppercase wap-title"><span><?=_l('rate_1')?></span></div>
                </div>
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 2 ? 'active' : '')?>" data-points="2">
                  <img src="<?=base_url('uploads/icon_rate/2.png');?>">
                  <div class="bold uppercase wap-title"><span><?=_l('rate_2')?></span></div>
                </div>
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 3 ? 'active' : '')?>" data-points="3">
                  <img src="<?=base_url('uploads/icon_rate/3.png');?>">
                  <div class="bold uppercase wap-title"><span><?=_l('rate_3')?></span></div>
                </div>
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 4 ? 'active' : '')?>" data-points="4">
                  <img src="<?=base_url('uploads/icon_rate/4.png');?>">
                  <div class="bold uppercase wap-title"><span><?=_l('rate_4')?></span></div>
                </div>
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 5 ? 'active' : '')?>" data-points="5">
                  <img src="<?=base_url('uploads/icon_rate/5.png');?>">
                  <div class="bold uppercase wap-title"><span><?=_l('rate_5')?></span></div>
                </div>
                <div class="clearfix"></div>
              </div>
              <hr />
              <?php $value = (isset($dataMain) && !empty($dataMain->note) ? $dataMain->note : ''); ?>
              <?php echo render_textarea('note','note',$value) ?>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<script>
  //load trang voi action
  _validate_form($('#evaluate_form'),{},add_evaluate_s);

  function add_evaluate_s(form) {
    if(!$('.points').val() || $('.points').val() == '') {
      alert_float('danger', 'Vui lòng chọn mức đánh giá!');
      return;
    }
    var data = $(form).serialize(),
      action = form.action;
    return $.post(action, data).done(function(form) {
      form = JSON.parse(form),
      alert_float(form.alert_type, form.message);
      if(form.success)
      {
        tAPI.draw('page');
        $('#evaluate_modal_data').modal('hide');
      }
    }), !1
  }
  //end
</script>