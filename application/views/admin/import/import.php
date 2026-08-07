<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <div class="row">
              <div class="col-md-4">
                <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                <?php echo form_hidden('items_import','true'); ?>
                <?php echo render_input('file_excel','ch_choose_excel_file','','file'); ?>
                <?php
                  echo render_select('warehouse_id',$warehouse,array('id','name','code'),'warehouse',array(),array('onchange'=>'loadLocaltion_warehouses()'));
                ?>
                <label for="localtion_warehouses_id" class="control-label">
                    <?php echo _l('Vị trí trong kho'); ?>
                </label>
                <div class="form-group">
                     <select style="width: 100%;" class="localtion_warehouses_id " id="localtion_warehouses_id" name="localtion_warehouses_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                     </select>
                </div>
                <div class="form-group">
                  <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                  <!-- <button type="button" class="btn btn-info simulate btn-import-submit"><?php echo _l('simulate_import'); ?></button> -->
                </div>
                <?php echo form_close(); ?>
                <?php if(isset($loi)) { ?>
                <div class="panel-body" style="margin-bottom: 20px">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <h3>Kết quả nhập</h3> <br />
                        <?php echo $loi?>
                        
                    </div>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
    function loadLocaltion_warehouses(){
        var warehouse_id = $('#warehouse_id').val();
        var localtion_warehouses = $('#localtion_warehouses_id');
        localtion_warehouses.select2('val',0);
        var checked = 0;
        localtion_warehouses.attr('required',true);
        localtion_warehouses.find('option:gt(0)').remove();
        if(localtion_warehouses.length) {
            $.post(admin_url+"warehouse/list_localtion",{warehouse:warehouse_id,checked:checked,[csrfData['token_name']] : csrfData['hash']},function(data){
                localtion_warehouses.html(data).find('option').attr('disabled','disabled').parents('#localtion_warehouses_id').find('option[child="1"]').removeAttr('disabled');
            localtion_warehouses.find('option:nth-child(1)').removeAttr('disabled');
            })
        }
    }
    $(function(){
        $('#localtion_warehouses_id').select2();
        appValidateForm($('#import_form'),{file_excel:{required:true,extension: "xls,xlsx"},warehouse_id:"required",localtion_warehouses_id:"required"});
    });
</script>
</body>
</html>
