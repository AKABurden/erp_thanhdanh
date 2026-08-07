<style type="text/css">
/*hoàng crm bổ xung*/
.panel_box {
  margin: 0;
  box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
}
.center {
  text-align: center;
}
.tab_container i {
  color: red;
  cursor: pointer;
}
.table-scroll {
  max-height: 310px;
  overflow: auto;
}
.wap-right {
  height: 400px;
}
.tab-pane{
  display: none;
}
.tab-pane.active{
  display: block;
}
.nav-tabs {
  margin-bottom: 0; 
  background: 0 0; 
  border-radius: 0;
}
.thead-row {
  text-align: center;
  text-transform: uppercase;
  font-weight: 700 !important;
  line-height: 40px;
  background: #3f9ad6;
  color: #fff;
}
.mtop25 {
  margin-top: 25px !important;
}
.padding20 {
  padding: 20px 0 !important;
}
.thead-col {
  text-align: center;
  white-space: unset;
}
.input-col {
  text-align: center;
  border: 0 !important;
  outline: 0 !important;
  border-bottom: 1px solid #9e9e9e !important;
}
.border-bottom {
  border-bottom: 1px solid #9e9e9e !important;
}
.mbottom {
  margin-bottom: 15px;
}
.padding0 {
  padding: 0 !important;
}
.boder-lr {
  border-right: 1px solid #a4a4a4;
  border-left: 1px solid #a4a4a4;
}

/*end*/
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/step_by_step.css')?>">
<div class="modal fade in" id="evaluate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false" style="display: block;">
  <div class="modal-dialog modal-lg no-modal-header">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_rfq_modal'); ?> </span>
            </h4>
          </div>
          <?php echo form_open(admin_url('supplier_quotes/add_tagert/'.$ask_price.'/'.$idquote), array('id'=>'add_tagert-form'));?>
            <div class="panel_s">
              <div class="panel-body">
                <?php if(!empty($ask_price)) { ?>
                <div class="tab_content tab_contenttargets">
                  <div class="col-md-12">
                    <div class="wap-left">
                      <?php foreach ($targert as $k => $v) { ?>
                        <div class="wap-left-title bold uppercase event_tab <?=$k == 0 ? 'active' : ''?>" active-tab="<?=$k+1?>">
                          <?= $v['name'] ?>
                        </div>
                      <?php } ?>
                    </div>
                    <div class="wap-right">
                      <?php foreach ($targert as $k => $v) { $i = count($v['targert']) ?>
                      <div class="fieldset <?=$k == 0 ? 'active' : ''?>" role-fieldset="<?=$k+1?>">
                        <?php foreach ($v['targert'] as $ktargert => $vtargert) { ?>
                          <?php $point='';
                            foreach ($vtargert['point'] as $kpoint => $vpoint) {?>
                              <?php if($vpoint['suppliers_id'] == $supplier_id){
                                $point = $vpoint['point'];
                                if ($point == 0) {
                                  $point='';
                                }
                                break;
                            }}?>
                          <div class="form-group">
                            <label for="targert[<?=$supplier_id?>][<?=$v['id']?>][<?=$vtargert['id']?>][point]" class="control-label"><?=$vtargert['name_children'] ?></label>
                            <input class="form-control" type="number" id="targert[<?=$supplier_id?>][<?=$v['id']?>][<?=$vtargert['id']?>][point]" name="targert[<?=$supplier_id?>][<?=$v['id']?>][<?=$vtargert['id']?>][point]" value="<?=$point?>">
                          </div>
                        <?php } ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <?php } ?>  
              </div>
            </div>
            <div class="modal-footer">
                <button  type="submit" class="btn btn-info" ><?=_l('submit')?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
            </div>
            <?php echo form_close(); ?>
      </div>
  </div>

</div>

<script type="text/javascript">
  $(function(){
    appValidateForm($('#add_tagert-form'),{},manage_rfq);
  });
  function manage_rfq(form) {
          $('#evaluate_modal').modal('hide');
    
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if(response.success == true){
              alert_float('success',response.message);
          }else
          {
            alert_float('danger',response.message);
          }
      });
      return false;
  }
  $(".event_tab").click(function(e){
    var currentE = $(e.currentTarget);
    $('.event_tab').removeClass('active');
    currentE.addClass('active');
    currentE.removeClass('validateForm-error');
    var active_tab = currentE.attr('active-tab');

    $('.fieldset').removeClass('active');
    $('[role-fieldset="'+active_tab+'"]').addClass('active');
  });

  $(document).ready(function() {
      reSizeHeight();
  });
  function reSizeHeight() {
      var Height = $(".wap-left").height();
      Height = Number(Height) - 3;
      var right = document.getElementsByClassName("wap-right");
      right[0].style.height = Height+"px";
  }
</script>