<style>
</style>
<div class="modal fade in" id="add_export_warehouse_done" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <?php echo form_open('admin/warranty/add_export_warehouse_done/'.$id, array('id'=>'add-export-warehouse')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('add_export_warehouse'); ?> </span>
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
                            <?php echo _l('code_purchases'); ?>
                          </label>
                        </td>
                        <td>
                          <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><?php echo get_option('prefix_export_different');?>-</span>
                              <?php
                                $number = sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1);
                              ?>
                              <input type="text" name="number_export_warehouse" class="form-control" value="<?= $number ?>" readonly>
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
                          <?php echo render_date_input('date_export_warehouse', '', $value); ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <label for="object_export_warehouse" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('ch_type_objects'); ?>
                          </label>
                        </td>
                        <td>
                          <input type="text" class="form-control" value="<?= _l('KHÁCH HÀNG') ?>" readonly>
                          <input type="hidden" name="object_export_warehouse" value="1">
                          <input type="hidden" name="type_items_export_warehouse" value="666">
                          <input type="hidden" name="type_status_done" value="<?= $type_status_done ?>">
                          
                        </td>
                        <td>
                          <label for="id_object" class="control-label">
                            <?php echo _l('cong_object'); ?>
                          </label>
                        </td>
                        <td>
                          <input type="text" class="form-control text_object_export_warehouse" value="<?= isset($client) ? $client->company : '' ?>" readonly>
                          <input type="hidden" name="id_object_export_warehouse" value="<?= isset($client) ? $client->userid : '' ?>">
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <label for="reason_export_warehouse" class="control-label">
                            <?php echo _l('ch_note_t'); ?>
                          </label>
                        </td>
                        <td colspan="3">
                          <?php echo render_textarea('reason_export_warehouse', ''); ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-md-12">
            <div class="panel panel-info">
              <div class="panel-heading">
                <?= lang('tnh_info_items') ?>
              </div>
              <div class="panel-body">
                <div class="col-md-12">
                  <div style="overflow: auto;">
                    <table class="tnh-tb table-export-warehouse table-bordered table-hover m-group0" style="table-layout: fixed;">
                      <thead>
                        <tr>
                          <th style="width: 50px;" class="text-center">STT</th>
                          <th style="width: 100px;" class="text-center"><?=_l('image')?></th>
                          <th style="width: 200px;" class="text-center"><?=_l('Series')?></th>
                          <th style="width: 150px;" class="text-center"><?=_l('warehouse')?></th>
                          <th style="width: 150px;" class="text-center"><?=_l('warehouse_localtion')?></th>
                          <th style="width: 100px;" class="text-center"><?=_l('item_unit')?></th>
                          <th style="width: 150px;" class="text-center"><?=_l('ch_warehouse_reports')?></th>
                          <th style="width: 100px;" class="text-center"><?=_l('quantity')?></th>
                          <th style="width: 150px;" class="text-center"><?=_l('cong_price_thinh')?></th>
                          <th style="width: 150px;" class="text-center"><?=_l('invoice_total')?></th>
                          <th style="width: 200px;" class="text-center"><?=_l('note')?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($item as $key => $value) { ?>
                          <tr data-key="<?= $key ?>">
                            <td style="width: 50px;" class="text-center"><?= $key+1 ?></td>
                            <td style="width: 100px;" class="text-center"><?= $value['img_item'] ?></td>
                            <td style="width: 200px;" class="text-center">
                              <?= $value['name'] ?>
                              <input type="hidden" class="id_item" name="items[<?= $key ?>][id]" value="<?= $value['id'] ?>">
                              <input type="hidden" class="type_item" name="items[<?= $key ?>][type]" value="<?= $value['type'] ?>">
                            </td>
                            <td style="width: 150px;" class="text-center">
                              <div class="form-group " style="width: 100%">
                                <select class="warehouses_id" id="warehouses_id_<?= $key ?>" name="items[<?= $key ?>][warehouses_id]" style="width: 100%;" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                  <?=$html_warehouse;?>
                                </select>
                              </div>
                            </td>
                            <td style="width: 150px;" class="text-center">
                              <div class="form-group " style="width: 100%">
                                <select class="localtion_warehouses_id" id="localtion_warehouses_id_<?= $key ?>" name="items[<?= $key ?>][localtion_warehouses_id]" style="width: 100%;" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                </select>
                              </div>
                            </td>
                            <td style="width: 100px;" class="text-center">
                              <?= $value['unit'] ?>
                            </td>
                            <td style="width: 100px;" class="text-center quantity_inventory"></td>
                            <td style="width: 100px;" class="text-center quantity_text">
                              <?= number_format($value['quantity']) ?>
                              <input type="hidden" class="input_quantity_inventory" value="">
                              <input type="hidden" class="quantity_export_supplies" name="items[<?= $key ?>][quantity_net]" value="<?= $value['quantity'] ?>">
                            </td>
                            <td style="width: 150px;" class="text-right">
                              <input type="text" class="form-control price" name="items[<?= $key ?>][price]" onkeyup="formatNumBerKeyUp(this)" value="0">
                            </td>
                            <td style="width: 150px;" class="text-right total_price">0</td>
                            <td style="width: 200px;" class="text-center">
                              <textarea class="form-control" rows="4" name="items[<?= $key ?>][note]"></textarea>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <a class="btn btn-info submit-form"><?php echo _l('submit'); ?></a>
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

  $( document ).ready(function() {
    var opt = {
        format: 'd/m/Y',
        timepicker: false,
        scrollInput: false,
        lazyInit: true,
        dayOfWeekStart: 0,
    };
    $('#date_export_warehouse').datetimepicker(opt);

    var allTr = $('.table-export-warehouse').find('tbody tr');
    $.each(allTr, function(i, v){
      $('#warehouses_id_'+$(v).attr('data-key')).select2();
      $('#localtion_warehouses_id_'+$(v).attr('data-key')).select2();
      loadLocaltion_warehouses($('#warehouses_id_'+$(v).attr('data-key')));
    });
  });

  function loadLocaltion_warehouses(currentQuantityInput){
    var warehouse = currentQuantityInput.val();
    var localtion_id = currentQuantityInput.parents('tr').find('select.localtion_warehouses_id');
    localtion_id.select2();
    localtion_id.find('option:gt(0)').remove();
    if(localtion_id.length) {
      $.post(admin_url+"warehouse/list_localtion",{warehouse:warehouse,[csrfData['token_name']] : csrfData['hash']},function(data){
        localtion_id.html(data).find('option').attr('disabled','disabled').parents(localtion_id).find('option[child="1"]').removeAttr('disabled');
        localtion_id.find('option:nth-child(1)').removeAttr('disabled');
        localtion_id.select2('val','');
        localtion_id.trigger('change');
      })
    }
  }

  $(document).on('change', '.localtion_warehouses_id', (e)=>{
    var currentQuantityInput = $(e.currentTarget);
    currentQuantityInput.parents('td').find('div.form-group').css({"border":"0"});
    currentQuantityInput.parents('tr').find('.quantity_inventory').css({"color":"#383a45"});
    currentQuantityInput.parents('tr').find('.quantity_text').css({"color":"#383a45"});
    var items = currentQuantityInput.parents('tr').find('.id_item').val();
    var warehouse_id = currentQuantityInput.parents('tr').find('select.warehouses_id').val();
    var id = $(currentQuantityInput).val();

    if(id == '') {
        currentQuantityInput.parents('tr').find('.quantity_inventory').text('');
    }
    else {
      $.post(admin_url + 'warranty/getQuantityWarehouse/'+items+'/'+warehouse_id+'/'+id,{[csrfData['token_name']] : csrfData['hash']}, function(item){
        var item = JSON.parse(item);
        currentQuantityInput.parents('tr').find('.quantity_inventory').text(formatNumber(item));
        currentQuantityInput.parents('tr').find('.input_quantity_inventory').val(item);
      });
    }
  });

  $(document).on('change', '.price', (e)=>{
    var currentQuantityInput = $(e.currentTarget);
    var quantity_export_supplies = currentQuantityInput.parents('tr').find('.quantity_export_supplies').val();
    var total = Number(unformatNumber(currentQuantityInput.val())) * Number(quantity_export_supplies);
    
    currentQuantityInput.parents('tr').find('.total_price').text(formatNumber(total));
  });

  $('.submit-form').click(function(e) {
    var form_true = true;
    var allTr = $('.table-export-warehouse').find('tbody tr');
    $.each(allTr, function(i, v){
      if(!$(v).find('select.warehouses_id').val()) {
        $(v).find('select.warehouses_id').parents('td').find('div.form-group').css({"border":"2px solid red"});
        form_true = false;
      }
      if(!$(v).find('select.localtion_warehouses_id').val()) {
        $(v).find('select.localtion_warehouses_id').parents('td').find('div.form-group').css({"border":"2px solid red"});
        form_true = false;
      }
      if(Number($(v).find('.input_quantity_inventory').val()) < Number($(v).find('.quantity_export_supplies').val())) {
        $(v).find('.quantity_inventory').css({"color":"#f00"});
        $(v).find('.quantity_inventory').find('div.text-danger').remove();
        $(v).find('.quantity_inventory').append('<div class="text-danger">\
                                                  SL không đủ\
                                                </div>');
        $(v).find('.quantity_text').css({"color":"#f00"});
        form_true = false;
      }
    });
    if(form_true === false) {
      return;
    }
    $('#add-export-warehouse').submit();
  });

  appValidateForm($('#add-export-warehouse'), {date_export_warehouse: 'required'}, manage_warehouse);
  function manage_warehouse(form) {
      if($('.table-export-warehouse').find('tbody tr').length == 0) {
        alert_float('danger', 'Vui lòng chọn mặt hàng cần xuất kho!');
        return;
      }
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
              alert_float(response.alert_type, response.message);
              $('#add_export_warehouse_done').modal('hide');
              tAPI.draw('page');
          }
          else if (response.success == false) {
              alert_float(response.alert_type, response.message);
          }
      });
      return false;
  }
</script>