<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <?php if(has_permission('items','','create')){ ?>
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
        <a href="#" class="btn btn-info pull-left H_action_button" onclick="add(); return false;">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('create_add_new'); ?>
        </a>
        <div class="line-sp"></div>
    </div>
  </div>
  <?php } ?>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php render_datatable(array(
              _l('#'),
              _l('als_kb_groups'),
              _l('name'),
              _l('options'),
            ),'info_client_detail'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="info-detail-add-title"><?php echo _l('add_info_client'); ?></span>
                    <span class="info-detail-edit-title"><?php echo _l('edit_info_client'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/info_client_detail',array('id'=>'info-client-detail-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                      <?php echo render_select('info_group', $info_group, array('id', 'name'), 'als_kb_groups'); ?>
                      <?php echo render_input('name_detail','name'); ?>
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
        initDataTable('.table-info_client_detail', admin_url+'clients/table_info_client_detail', [], [],'',[0,'asc']);
        appValidateForm($('#info-client-detail-modal'), {info_group: 'required', name_detail: 'required'}, manage_client_detail);
  });
  function add() {
    $('.info-detail-add-title').removeClass('hide');
    $('.info-detail-edit-title').addClass('hide');
    $('#info-client-detail-modal').attr("action","<?=admin_url('clients/info_client_detail')?>");
    $('#info_group').selectpicker('val','');
    $('#name_detail').val('');
    $('#add_detail_modal').modal('show');
  }
  function edit(id) {
    $('.info-detail-add-title').addClass('hide');
    $('.info-detail-edit-title').removeClass('hide');
    $('#info-client-detail-modal').attr("action","<?=admin_url('clients/info_client_detail/')?>"+id);
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'clients/getData_client_detail/'+id, data).done(function(response){
       response = JSON.parse(response);
       $('#info_group').selectpicker('val',response.id_info_group);
       $('#name_detail').val(response.name);
       $('#add_detail_modal').modal('show');
    });
  }
  function delete_main(id) {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
          return false;
      } else {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'clients/delete_info_detail/'+id, data).done(function(response){
           response = JSON.parse(response);
           if(response.success == true) {
            $('.table-info_client_detail').DataTable().ajax.reload();
            alert_float(response.alert_type,response.message)
           }
           else {
            alert_float(response.alert_type,response.message)
           }
        });
      }
      return false;
  }
  function manage_client_detail(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            $('#info_group').selectpicker('val','');
            $('#name_detail').val('');
            $('.table-info_client_detail').DataTable().ajax.reload();
            alert_float(response.alert_type, response.message);
        }
        $('#add_detail_modal').modal('hide');
    });
    return false;
  }
</script>
</body>
</html>
