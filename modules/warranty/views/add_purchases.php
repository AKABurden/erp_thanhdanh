<style>
</style>
<div class="modal fade in" id="add_purchases_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <?php echo form_open('admin/warranty/add_purchases_form/'.$id, array('id'=>'add-purchases-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('purchases'); ?> </span>
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
                          <label for="number" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('code_purchases'); ?>
                          </label>
                        </td>
                        <td>
                          <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><?php echo get_option('prefix_purchase');?></span>
                              <?php
                                $number = sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1);
                              ?>
                              <input type="text" name="number" class="form-control" value="<?= $number ?>" readonly>
                            </div>
                          </div>
                        </td>
                        <td>
                          <label for="date" class="control-label">
                            <small class="req text-danger">* </small>
                            <?php echo _l('ch_date_p'); ?>
                          </label>
                        </td>
                        <td>
                          <?php $value = _d(date('Y-m-d H:i:s')); ?>
                          <?php echo render_datetime_input('date', '', $value); ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <label for="name" class="control-label">
                            <?php echo _l('ch_name_p'); ?>
                          </label>
                        </td>
                        <td>
                          <?php $value = _l('ch_purchases'); ?>
                          <?php echo render_input('name', '', $value); ?>
                          <input type="hidden" name="type_items" value="-1">
                        </td>
                        <td>
                          <label for="reason" class="control-label">
                            <?php echo _l('ch_note_t'); ?>
                          </label>
                        </td>
                        <td>
                          <?php echo render_textarea('reason', ''); ?>
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
                <div>
                  <div class="col-md-9">
                    <div class="form-group">
                      <label for="select-item-purchase" class="control-label"><?=_l('tnh_tools_supplies')?></label>
                      <input type="text" id="select-item-purchase" class="select-item-purchase" style="width: 100%;">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <a class="btn btn-success" onclick="get_all_item(<?= $id_warranty ?>); return false;" style="margin-top: 28px;"><?= _l('tnh_check_all') ?></a>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <br>
                <div class="col-md-12">
                  <table class="tnh-tb table-item-purchase table-bordered table-hover m-group0" style="table-layout: fixed;">
                    <thead>
                      <tr>
                        <th style="width: 5%;" class="text-center">STT</th>
                        <th style="width: 10%;" class="text-center"><?=_l('image')?></th>
                        <th style="width: 15%;" class="text-center"><?=_l('code_supplies')?></th>
                        <th style="width: 20%;" class="text-center"><?=_l('name_supplies')?></th>
                        <th style="width: 10%;" class="text-center"><?=_l('unit')?></th>
                        <th style="width: 10%;" class="text-center"><?=_l('quantity')?></th>
                        <th style="width: 10%;" class="text-center"><?=_l('item_quantity_confirm')?></th>
                        <th style="width: 15%;" class="text-center"><?=_l('note')?></th>
                        <th style="width: 5%;" class="text-center"></th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
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
    ajaxSelect('#select-item-purchase', 'admin/warranty/searchItemPurchase');
  });

  function ajaxSelect(element, url, id)
  {
    $(element).select2({
        width: 'resolve',
        escapeMarkup: function(m) {
            return m;
        },
        ajax: {
            url: site.base_url + url,
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    id_warranty: <?= $id_warranty ?>,
                    term: term,
                    limit: 50
                };
            },
            results: function (data, page) {
                if(data.results != null) {
                    return { results: data.results };
                } else {
                    return { results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
  }

  function resetStt() {
    var countSTT = $('.table-item-purchase').find('tbody tr');
    var stt = 1;
    $.each(countSTT, function(i, v){
        $(v).find('.stt').text(stt);
        stt++;
    });
  }

  var unique = 0;
  $(document).on('change','#select-item-purchase', function (e) {
    var target = $(e.currentTarget);
    if(typeof target != 'undefined') {
      var allTr = $('.table-item-purchase').find('tbody tr');
      var checkExists = 0;
      $.each(allTr, function(i, v){
          if(target.val() == $(v).find('.id_item').val()) {
              checkExists++;
          }
      });
      if(checkExists == 1) {
          alert_float('danger','<?=_l('supplies_exists')?>');
          return;
      }
      else {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_item'] = target.val();
        if(target.val()) {
          $.post(admin_url+'warranty/getDetail_ItemWarranty', data).done(function(response){
              response = JSON.parse(response);
              $.each(response, function(i, v){
                  var html = '<tr>\
                                <td class="text-center stt"></td>\
                                <td class="text-center">'+v.img_item+'</td>\
                                <td class="text-left">\
                                  '+v.code+'\
                                  <input type="hidden" name="item['+unique+'][id_item]" class="id_item" value="'+v.id+'">\
                                </td>\
                                <td class="text-left">'+v.name+'</td>\
                                <td class="text-center">'+v.unit+'</td>\
                                <td class="text-center">\
                                  <input type="text" name="item['+unique+'][quantity]" class="form-control quantity" onkeyup="formatNumBerKeyUp(this)" value="1">\
                                </td>\
                                <td class="text-center">\
                                  <input type="text" name="item['+unique+'][quantity_net]" class="form-control quantity_net" onkeyup="formatNumBerKeyUp(this)" value="1">\
                                </td>\
                                <td class="text-center">\
                                  <textarea name="item['+unique+'][note]" class="form-control" rows="4"></textarea>\
                                </td>\
                                <td class="text-center">\
                                  <span class="btn btn-danger deleteTritem"><i class="fa fa-times"></i></span>\
                                </td>\
                            </tr>';
                  $('.table-item-purchase').find('tbody').append(html);
                  resetStt();
                  unique++;
              });
          });
        }
      }
    }
  });

  $(document).on('change','.quantity', function (e) {
    var target = $(e.currentTarget);
    target.parents('tr').find('.quantity_net').val(formatNumber(target.val()));
  });

  $(document).on('click','.deleteTritem', function (e) {
    var target = $(e.currentTarget);
    target.parents('tr').remove();
    resetStt();
  });

  function get_all_item(id_warranty) {
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['id_warranty'] = id_warranty;
    $.post(admin_url+'warranty/getAll_ItemWarranty', data).done(function(response){
      response = JSON.parse(response);
      $.each(response, function(i, v){
        if($('.id_item[value="'+v.id+'"]').length == 0) {
          var html = '<tr>\
                        <td class="text-center stt"></td>\
                        <td class="text-center">'+v.img_item+'</td>\
                        <td class="text-left">\
                          '+v.code+'\
                          <input type="hidden" name="item['+unique+'][id_item]" class="id_item" value="'+v.id+'">\
                        </td>\
                        <td class="text-left">'+v.name+'</td>\
                        <td class="text-center">'+v.unit+'</td>\
                        <td class="text-center">\
                          <input type="text" name="item['+unique+'][quantity]" class="form-control quantity" onkeyup="formatNumBerKeyUp(this)" value="1">\
                        </td>\
                        <td class="text-center">\
                          <input type="text" name="item['+unique+'][quantity_net]" class="form-control quantity_net" onkeyup="formatNumBerKeyUp(this)" value="1">\
                        </td>\
                        <td class="text-center">\
                          <textarea name="item['+unique+'][note]" class="form-control" rows="4"></textarea>\
                        </td>\
                        <td class="text-center">\
                          <span class="btn btn-danger deleteTritem"><i class="fa fa-times"></i></span>\
                        </td>\
                    </tr>';
          $('.table-item-purchase').find('tbody').append(html);
          unique++;
        }
      });
      resetStt();
    });
  }

  appValidateForm($('#add-purchases-form'), {number: 'required', date: 'required', name: 'required'}, manage_purchases);
  function manage_purchases(form) {
      if($('.table-item-purchase').find('tbody tr').length == 0) {
        alert_float('danger', 'Vui lòng chọn mặt hàng cần mua!');
        return;
      }
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
              alert_float(response.alert_type, response.message);
              $('#add_purchases_modal').modal('hide');
              tAPI.draw('page');
          }
          else if (response.success == false) {
              alert_float(response.alert_type, response.message);
          }
      });
      return false;
  }
</script>