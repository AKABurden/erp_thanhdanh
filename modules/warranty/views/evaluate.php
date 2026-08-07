<style>
  .wap-icon {
    float: left;
    width: 20%;
  }
  .wap-icon img {
    cursor: pointer;
    position: relative;
  }
  .wap-icon img:hover {
    top: -5px;
    transition: all 0.5s;
  }
  .wap-icon.active .wap-title span {
    color: #2887d4;
    border: 2px solid #2887d46b;
    padding: 5px 25px;
  }
  .wap-icon.active .wap-title span::before {
    content: "✔";
    margin-right: 5px;
  }
  .wap-title {
    margin-top: 10px;
  }
  .wap-title-status {
    margin-top: 20px;
  }
  .wap-title-status {
    position: relative;
  }
  .wap-title-status::before {
    content: "";
    width: 10px;
    height: 10px;
    position: absolute;
    background: #7d7d7d;
    border-radius: 50%;
    top: -14px;
    left: calc(50% - 5px);
  }
  .wap-title-status.success::before {
    background: #4ab138;
  }
</style>
<div class="modal fade" id="evaluate_modal_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title add_title_evaluate"><?php echo _l('evaluate'); ?></h4>
        <h4 class="modal-title edit_title_evaluate"><?php echo _l('update_evaluate'); ?></h4>
      </div>
      <?php echo form_open('admin/warranty/add_evaluate',array('id'=>'evaluate_form')); ?>
      <div class="modal-body">
          <div class="panel_s">
            <div class="panel-body">
              <div class="text-center bold uppercase fsize18"><?=_l('rate_evaluate')?></div>
              <hr />
              <div style="display: flex; justify-content: center;">
                <input type="hidden" name="points" class="points" value="<?=(isset($dataMain) ? $dataMain->points : '')?>">
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 1 ? 'active' : '')?>" data-points="1">
                  <img src="<?=base_url('uploads/icon_rate/1.png');?>">
                  <div class="bold uppercase wap-title"><span>Not Happy</span></div>
                </div>
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 2 ? 'active' : '')?>" data-points="2">
                  <img src="<?=base_url('uploads/icon_rate/3.png');?>">
                  <div class="bold uppercase wap-title"><span>Happy</span></div>
                </div>
                <div class="wap-icon text-center <?=(isset($dataMain) && $dataMain->points == 3 ? 'active' : '')?>" data-points="3">
                  <img src="<?=base_url('uploads/icon_rate/5.png');?>">
                  <div class="bold uppercase wap-title"><span>Very Happy</span></div>
                </div>
                <div class="clearfix"></div>
              </div>
              <hr />
              <?php $value = (isset($dataMain) && !empty($dataMain->note) ? $dataMain->note : ''); ?>
              <?php echo render_textarea('note','Phản hồi của khách hàng',$value) ?>
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