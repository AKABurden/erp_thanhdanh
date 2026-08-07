<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <?php if(has_permission('warehouse_group','','create')){ ?>
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
        <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        <div class="pull-right mright5 H_border">
          <a class="btn btn-info test H_action_button">
                 <?php echo _l('Export excel'); ?></a>
        </div>
        <div class="pull-right mright5 H_border">
          <a href="#" class="btn btn-info H_action_button" onclick="add_group(); return false;">
                 <?php echo _l('create_add_new'); ?>
          </a>
        </div>
    </div>
  </div>
  <?php } ?>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php render_datatable(array(
              _l('code'),
              _l('name'),
              _l('options'),
            ),'warehouse_group'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="warehouse_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('warehouse_group_add_heading'); ?></span>
                    <span class="edit-title"><?php echo _l('warehouse_group_edit_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/warehouse/group_detail',array('id'=>'warehouse-group-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('code','code'); ?>
                        <?php echo render_input('name','name'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
  $(function(){
    initDataTable('.table-warehouse_group', admin_url+'warehouse/table_warehouse_group', [0], [0],'',[0,'asc']);
    appValidateForm($('#warehouse-group-modal'), {code: 'required', name: 'required'}, manage_warehouse_group);
  });
  function add_group() {
    $('.add-title').removeClass('hide');
    $('.edit-title').addClass('hide');
    $('#warehouse-group-modal').attr("action","<?=admin_url('warehouse/group_detail')?>");
    $('#code').val('');
    $('#name').val('');
    $('#warehouse_group_modal').modal('show');
  }
  function edit_group(id) {
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    $('#warehouse-group-modal').attr("action","<?=admin_url('warehouse/group_detail/')?>"+id);
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warehouse/getData_group/'+id, data).done(function(response){
       response = JSON.parse(response);
       $('#code').val(response.code);
       $('#name').val(response.name);
       $('#warehouse_group_modal').modal('show');
    });
  }
  function delete_group(id) {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
          return false;
      } else {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'warehouse/delete_group/'+id, data).done(function(response){
           response = JSON.parse(response);
           if(response.success == true) {
            $('.table-warehouse_group').DataTable().ajax.reload();
            alert_float(response.alert_type,response.message)
           }
           else {
            alert_float(response.alert_type,response.message)
           }
        });
      }
      return false;
  }
  function manage_warehouse_group(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            $('#code').val('');
            $('#name').val('');
            $('.table-warehouse_group').DataTable().ajax.reload();
            alert_float(response.alert_type, response.message);
        }
        $('#warehouse_group_modal').modal('hide');
    });
    return false;
  }
</script>
</body>
</html>
