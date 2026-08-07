<style>
</style>
<div class="modal fade in" id="add_transfer_warehouse_done" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-lg">
    <?php echo form_open('admin/warranty/add_transfer_warehouse_done/'.$id, array('id'=>'add-transfer-warehouse')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('add_transfer_warehouse'); ?> </span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-primary">
              <div class="panel-heading"><?=_l('lead_general_info')?></div>
                <div class="panel-body">
                  <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                    <tbody>
                      <tr>
                        <td>
                          <label for="number_export_warehouse" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('ch_code_p'); ?>
                          </label>
                        </td>
                        <td>
                          <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><?php echo get_option('prefix_transfer');?>-</span>
                              <?php
                                $number = sprintf('%06d', ch_getMaxID('id', 'tbltransfer_warehouse') + 1);
                              ?>
                              <input type="text" name="number_transfer_warehouse" class="form-control" value="<?= $number ?>" readonly>
                              <input type="hidden" name="type_status_done" value="<?= $type_status_done ?>">
                            </div>
                          </div>
                        </td>
                        <td>
                          <label for="date_export_warehouse" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('ch_date_p'); ?>
                          </label>
                        </td>
                        <td>
                          <?php $value = _d(date('Y-m-d')); ?>
                          <?php echo render_date_input('date_transfer_warehouse', '', $value); ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <label for="transfer_warehouse_to" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('ch_warehouse_to'); ?>
                          </label>
                        </td>
                        <td>
                          <div class="form-group">
                            <select style="width: 100%;" class="transfer_warehouse_to " name="transfer_warehouse_to" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <option value=""></option>
                              <?php foreach ($warehouse as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>" data-subtext="<?= $value['code'] ?>"><?= $value['name'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </td>
                        <td>
                          <label for="transfer_localtion_to" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('ch_localhost_warehouse_N'); ?>
                          </label>
                        </td>
                        <td>
                          <div class="form-group">
                            <select style="width: 100%;" class="transfer_localtion_to " name="transfer_localtion_to" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            </select>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <label for="reason_transfer_warehouse" class="control-label">
                            <?php echo _l('ch_note_t'); ?>
                          </label>
                        </td>
                        <td colspan="3">
                          <?php echo render_textarea('reason_transfer_warehouse', ''); ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-info submit-form"><?php echo _l('submit'); ?></button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
<script type="text/javascript">
function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
  nStr += '';
  x = nStr.split(decSeperate);
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
  }
  return x1 + x2;
}

function unformatNumber(nStr, decSeperate=".", groupSeperate=",") {
    return nStr.replace(/\,/g,'');
}

$(function(){
  $('select[name="transfer_warehouse_to"]').select2();
  $('select[name="transfer_localtion_to"]').select2();
});

$(document).on('change','select[name="transfer_warehouse_to"]', function (e) {
  $.post(admin_url+"warranty/get_localtion_default",{warehouse : $('select[name="transfer_warehouse_to"]').val(), [csrfData['token_name']] : csrfData['hash']},function(data){
    data = JSON.parse(data);
    loadLocaltion_warehouses($('select[name="transfer_localtion_to"]'), $('select[name="transfer_warehouse_to"]').val(), data.data);
  });
});
function loadLocaltion_warehouses(trItem, val, check){
    var localtion_warehouse = trItem;
    var checked = check;
    localtion_warehouse.attr('required',true);
    localtion_warehouse.find('option:gt(0)').remove();
    if(localtion_warehouse.length) {
        $.post(admin_url+"warehouse/list_localtion",{warehouse : val, checked : checked, [csrfData['token_name']] : csrfData['hash']},function(data){
            localtion_warehouse.html(data).find('option').attr('disabled','disabled').parents('.transfer_localtion_to').find('option[child="1"]').removeAttr('disabled');
            localtion_warehouse.find('option:nth-child(1)').removeAttr('disabled');
            localtion_warehouse.select2('val',checked);
        })
    }
}

appValidateForm($('#add-transfer-warehouse'), {date_transfer_warehouse: 'required', transfer_warehouse_to: 'required', transfer_localtion_to: 'required'}, manage_warehouse);
function manage_warehouse(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float(response.alert_type, response.message);
            $('#add_transfer_warehouse_done').modal('hide');
            tAPI.draw('page');
        }
        else if (response.success == false) {
            alert_float(response.alert_type, response.message);
        }
    });
    return false;
}
</script>