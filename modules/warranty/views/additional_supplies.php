<style>
  .wrap-item-exists {
    float: left;
    margin-right: 10px;
    margin-bottom: 10px;
    background: #ffe393;
    padding: 5px 10px;
    border-radius: 20px;
  }
  .js-add-item {
    cursor: pointer;
  }
  td.default-type-amount div.type_amount {
    pointer-events: none;
  }
  td.default-type-amount a.select2-choice {
    background: #eef1f6;
  }
  td.default-note textarea {
    pointer-events: none;
    background: #eef1f6;
  }
</style>
<div class="modal fade in" id="additional_supplies" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <?php echo form_open('admin/warranty/additional_supplies/'.$id, array('id'=>'additional-supplies-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('additional_supplies'); ?> </span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-primary">
              <div class="panel-heading text-center"><?= _l('item_exists_list') ?></div>
              <div class="panel-body">
                <?php if(isset($item_exists)) { ?>
                  <div class="item_exists_list">
                    <?php foreach ($item_exists as $key => $value) { ?>
                      <div class="js-add-item">
                        <input type="hidden" class="item_exists_id" value="<?= $value['id'] ?>">
                        <div class="wrap-item-exists"><?= $value['text'] ?></div>
                      </div>
                    <?php } ?>
                  </div>
                <?php } else { ?>
                  <div class="text-center"><?= _l('item_exists_list_empty') ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="text-danger">
              * Bổ sung những vật tư đã có sẽ làm tăng số lượng, không thay đổi về LOẠI CHI PHÍ - ĐƠN GIÁ - THÀNH TIỀN - GHI CHÚ.
            </div>
            <table class="tnh-tb table-additional-supplies table-bordered table-hover m-group0" style="table-layout: fixed;">
              <thead>
                <tr style="background: #337ab7; color: #fff;">
                    <th style="width: 5%;" class="text-center">
                        <span class="btn btn-success add_supplies"><i class="fa fa-plus"></i></span>
                    </th>
                    <th style="width: 15%;" class="text-center"><?=_l('code_supplies')?></th>
                    <th style="width: 8%;" class="text-center"><?=_l('image')?></th>
                    <th style="width: 15%;" class="text-center"><?=_l('name_supplies')?></th>
                    <th style="width: 10%;" class="text-center"><?=_l('item_quantity')?></th>
                    <th style="width: 10%;" class="text-center"><?=_l('ch_costs')?></th>
                    <th style="width: 10%;" class="text-center"><?=_l('ch_price')?></th>
                    <th style="width: 10%;" class="text-center"><?=_l('tnh_subtotal')?></th>
                    <th style="width: 12%;" class="text-center"><?=_l('note')?></th>
                    <th style="width: 5%;" class="text-center"></th>
                </tr>
              </thead>
              <tbody>
                <?php $unique = 0; ?>
                <?php if(!$supplies) { ?>
                  <tr class="TrSupplies" data-unique="<?=$unique?>">
                      <td class="text-center stt">1</td>
                      <td class="text-center">
                          <input type="text" name="supplies[<?=$unique?>][id_item]" class="id_item" style="width: 100%;">
                      </td>
                      <td class="text-center img_item"></td>
                      <td class="text-left name_item"></td>
                      <td class="text-center">
                          <input type="number" name="supplies[<?=$unique?>][quantity]" class="form-control quantity" value="1">
                      </td>
                      <td class="text-center">
                          <input type="text" name="supplies[<?=$unique?>][type_amount]" class="type_amount" style="width: 100%;">
                      </td>
                      <td class="text-center">
                          <input type="text" name="supplies[<?=$unique?>][amount]" class="form-control amount" onkeyup="formatNumBerKeyUp(this)" value="0">
                      </td>
                      <td class="text-right total_item"></td>
                      <td class="text-center">
                          <textarea name="supplies[<?=$unique?>][note]" class="form-control" rows="4"></textarea>
                      </td>
                      <td class="text-center">
                          <span class="btn btn-danger remove_supplies"><i class="fa fa-times"></i></span>
                      </td>
                  </tr>
                  <?php $unique++; ?>
                <?php } else { ?>
                  <?php foreach ($supplies as $key => $value) { ?>
                    <?php
                        $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
                        if($value['type_item'] == 'materials') {
                            $getDetail = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                            $name = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
                            if($getDetail && !empty($getDetail->images)) {
                                $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
                            }
                        }
                        else if($value['type_item'] == 'supplies') {
                            $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                            $name = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
                            if($getDetail && !empty($getDetail->images)) {
                                $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
                            }
                        }
                    ?>
                    <tr class="TrSupplies" data-unique="<?=$unique?>" data-id="<?= $value['type_item'].'_'.$value['id_item'] ?>" data-typeamount="<?=$value['type_amount']?>">
                        <td class="text-center stt"><?=++$key?></td>
                        <td class="text-center">
                            <input type="text" name="supplies[<?=$unique?>][id_item]" class="id_item" style="width: 100%;">
                        </td>
                        <td class="text-center img_item">
                            <?=$img?>
                        </td>
                        <td class="text-left name_item">
                            <?=$name?>
                        </td>
                        <td class="text-center">
                            <input type="number" name="supplies[<?=$unique?>][quantity]" class="form-control quantity" value="<?=$value['quantity']?>">
                        </td>
                        <td class="text-center">
                            <input type="text" name="supplies[<?=$unique?>][type_amount]" class="type_amount" style="width: 100%;">
                        </td>
                        <td class="text-center">
                            <input type="text" name="supplies[<?=$unique?>][amount]" class="form-control amount" onkeyup="formatNumBerKeyUp(this)" value="<?=number_format($value['amount'])?>" <?=($value['type_amount'] == 1 ? 'readonly' : '')?>>
                            <input type="hidden" class="checkAmount" value="<?= $value['amount'] ?>">
                        </td>
                        <td class="text-right total_item"><?=number_format($value['total'])?></td>
                        <td class="text-center">
                            <textarea name="supplies[<?=$unique?>][note]" class="form-control" rows="4"><?=$value['note']?></textarea>
                        </td>
                        <td class="text-center">
                            <span class="btn btn-danger remove_supplies"><i class="fa fa-times"></i></span>
                        </td>
                    </tr>
                    <?php $unique++; ?>
                  <?php } ?>
                <?php } ?>
              </tbody>
              <tfoot>
                  <tr>
                      <td class="text-center">
                          <span class="btn btn-success add_supplies"><i class="fa fa-plus"></i></span>
                      </td>
                      <td class="text-left bold"><?=_l('tnh_grand_total')?></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center bold quantity_total">1</td>
                      <td class="text-center"></td>
                      <td class="text-right bold amount_total">0</td>
                      <td class="text-right bold grand_total">0</td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                  </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info pull-right"><?=_l('submit')?></button>
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

function ajaxSelectSeries(element, url, id)
{
    var customer_id = $('#customer_id').val();
    if (id)
    {
        $(element).val(id).select2({
            width: 'resolve',
            escapeMarkup: function(m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        customer_id: customer_id,
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    } else {
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
                        customer_id: customer_id,
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
}

var unique = <?=$unique?>;
$( document ).ready(function() {
  var TrSupplies = $('.TrSupplies');
  $.each(TrSupplies, function(i, v){
      var typeamount = $(v).attr('data-typeamount');
      if(typeof typeamount == "undefined") {
          typeamount = 1;
      }
      ajaxSelectSeries('input[name="supplies['+$(v).attr('data-unique')+'][id_item]"]', 'admin/warranty/searchSupplies', $(v).attr('data-id'));
      ajaxSelectSeries('input[name="supplies['+$(v).attr('data-unique')+'][type_amount]"]', 'admin/warranty/searchType_amount', typeamount);
      $('input[name="supplies['+$(v).attr('data-unique')+'][type_amount]"]').trigger('change');
      resetGrand_total_supplies();
  });
});

function resetSupplies() {
    var countSTT = $('.table-additional-supplies').find('tbody tr');
    var stt = 1;
    $.each(countSTT, function(i, v){
        $(v).find('.stt').text(stt);
        stt++;
    });
}

function resetAmount(thisItem) {
    var quantity = thisItem.parents('tr').find('.quantity').val();
    var amount = thisItem.parents('tr').find('.amount').val();

    var val_unique = thisItem.parents('tr').attr('data-unique');
    var type_amount = $('input[name="supplies['+val_unique+'][type_amount]"]').val();
    if(type_amount == 2) {
        var total = Number(unformatNumber(quantity)) * Number(unformatNumber(amount));
    }
    else if(type_amount == 1) {
        var total = 0;
    }
    thisItem.parents('tr').find('.total_item').text(formatNumber(total));
}

function resetGrand_total_supplies() {
    var countSTT = $('.table-additional-supplies').find('tbody tr');
    var total_supplies = 0;
    var quantity_supplies = 0;
    var amount_supplies = 0;
    $.each(countSTT, function(i, v){
        total_supplies += Number(unformatNumber($(v).find('.total_item').text()));
        quantity_supplies += Number(unformatNumber($(v).find('.quantity').val()));
        amount_supplies += Number(unformatNumber($(v).find('.amount').val()));
    });
    $('.table-additional-supplies').find('tfoot .quantity_total').text(formatNumber(quantity_supplies));
    $('.table-additional-supplies').find('tfoot .amount_total').text(formatNumber(amount_supplies));
    $('.table-additional-supplies').find('tfoot .grand_total').text(formatNumber(total_supplies));
}

$('.add_supplies').click(function(e){
    var target = $(e.currentTarget);
    var html = '<tr class="TrSupplies" data-unique="'+unique+'">\
                    <td class="text-center stt"></td>\
                    <td class="text-center">\
                        <input type="text" name="supplies['+unique+'][id_item]" class="id_item" style="width: 100%;">\
                    </td>\
                    <td class="text-center img_item"></td>\
                    <td class="text-left name_item"></td>\
                    <td class="text-center">\
                        <input type="number" name="supplies['+unique+'][quantity]" class="form-control quantity" value="1">\
                    </td>\
                    <td class="text-center">\
                        <input type="text" name="supplies['+unique+'][type_amount]" class="type_amount" style="width: 100%;">\
                    </td>\
                    <td class="text-center">\
                        <input type="text" name="supplies['+unique+'][amount]" class="form-control amount" onkeyup="formatNumBerKeyUp(this)" value="0">\
                    </td>\
                    <td class="text-right total_item"></td>\
                    <td class="text-center">\
                        <textarea name="supplies['+unique+'][note]" class="form-control" rows="4"></textarea>\
                    </td>\
                    <td class="text-center">\
                        <span class="btn btn-danger remove_supplies"><i class="fa fa-times"></i></span>\
                    </td>\
                </tr>';
    $('.table-additional-supplies').find('tbody').append(html);
    ajaxSelectSeries('input[name="supplies['+unique+'][id_item]"]', 'admin/warranty/searchSupplies');
    ajaxSelectSeries('input[name="supplies['+unique+'][type_amount]"]', 'admin/warranty/searchType_amount', 1);
    $('input[name="supplies['+unique+'][type_amount]"]').trigger('change');
    resetSupplies();
    resetGrand_total_supplies();
    unique++;
});

$(document).on('change','.id_item', function (e) {
    var target = $(e.currentTarget);

    var all_list = $('.item_exists_list').find('.item_exists_id');
    var checkExists_new = 0;
    if(target.val()) {
        $.each(all_list, function(i, v){
            if(target.val() == $(v).val()) {
                target.parents('tr').find('.type_amount').parents('td').addClass('default-type-amount');
                target.parents('tr').find('textarea').parents('td').addClass('default-note');
            }
        });
    }

    var allTr = $('.table-additional-supplies').find('.TrSupplies');
    var checkExists = 0;
    if(target.val()) {
        $.each(allTr, function(i, v){
            var val_unique = $(v).attr('data-unique');
            if(target.val() == $(v).find('input[name="supplies['+val_unique+'][id_item]"]').val()) {
                checkExists++;
            }
        });
    }
    if(checkExists == 2) {
        alert_float('danger','<?=_l('supplies_exists')?>');
        target.parents('tr').find('.id_item').val('');
        target.parents('tr').find('.img_item').html('');
        target.parents('tr').find('.name_item').html('');
        target.parents('tr').find('.amount').val(0);
        target.parents('tr').find('.total_item').text(0);
        return;
    }
    else {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_item'] = target.val();
        if(target.val()) {
            $.post(admin_url+'warranty/getDetail_ItemSupplies', data).done(function(response){
                response = JSON.parse(response);
                $.each(response, function(i, v){
                    target.parents('tr').find('.img_item').html(v.img_item);
                    target.parents('tr').find('.name_item').html(v.name);
                    if($('input[name="supplies['+target.parents('tr').attr('data-unique')+'][type_amount]"]').val() == 1) {
                        target.parents('tr').find('.amount').val(0);
                    }
                    else {
                        target.parents('tr').find('.amount').val(v.price);
                        if(target.parents('tr').find('.checkAmount').length > 0) {
                            var checkAmount = formatNumber(target.parents('tr').find('.checkAmount').val());
                            target.parents('tr').find('.amount').val(checkAmount);
                        }
                    }
                });
                resetAmount(target);
                resetGrand_total_supplies();
            });
        }
    }
});

$(document).on('change','.type_amount', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    if(target.val() == 1) {
        target.parents('tr').find('.amount').prop("readonly",true);
        $('input[name="supplies['+target.parents('tr').attr('data-unique')+'][id_item]"]').trigger('change');
    }
    else if(target.val() == 2) {
        target.parents('tr').find('.amount').prop("readonly",false);
        $('input[name="supplies['+target.parents('tr').attr('data-unique')+'][id_item]"]').trigger('change');
    }
    resetGrand_total_supplies();
});

$(document).on('change','.expenses_amount', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    resetGrand_total_expenses();
});

$(document).on('click','.quantity', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    resetGrand_total_supplies();
});

$(document).on('change','.quantity', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    resetGrand_total_supplies();
});

$(document).on('change','.amount', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    resetGrand_total_supplies();
});

$(document).on('click','.remove_supplies', function (e) {
    var target = $(e.currentTarget);
    target.parents('tr').remove();
    resetSupplies();
    resetGrand_total_supplies();
});

$(document).on('click','.js-add-item', function (e) {
  var target = $(e.currentTarget);
  var idItem = target.find('.item_exists_id').val();
  var allTr = $('.table-additional-supplies').find('tbody').find('tr');
  var checkTr = false;
  $.each(allTr, function(i, v){
    var val_unique = $(v).attr('data-unique');
    if(idItem == $(v).find('input[name="supplies['+val_unique+'][id_item]"]').val()) {
        checkTr = true;
        var quantity = Number($(v).find('.quantity').val()) + 1;
        $(v).find('.quantity').val(quantity);
        $(v).find('.quantity').trigger('change');
    }
  });

  if(checkTr == false) {
    var html = '<tr class="TrSupplies" data-unique="'+unique+'">\
                    <td class="text-center stt"></td>\
                    <td class="text-center">\
                        <input type="text" name="supplies['+unique+'][id_item]" class="id_item" style="width: 100%;">\
                    </td>\
                    <td class="text-center img_item"></td>\
                    <td class="text-left name_item"></td>\
                    <td class="text-center">\
                        <input type="number" name="supplies['+unique+'][quantity]" class="form-control quantity" value="1">\
                    </td>\
                    <td class="text-center default-type-amount">\
                        <input type="text" name="supplies['+unique+'][type_amount]" class="type_amount" style="width: 100%;">\
                    </td>\
                    <td class="text-center">\
                        <input type="text" name="supplies['+unique+'][amount]" class="form-control amount" onkeyup="formatNumBerKeyUp(this)" value="0">\
                    </td>\
                    <td class="text-right total_item"></td>\
                    <td class="text-center default-note">\
                        <textarea name="supplies['+unique+'][note]" class="form-control" rows="4"></textarea>\
                    </td>\
                    <td class="text-center">\
                        <span class="btn btn-danger remove_supplies"><i class="fa fa-times"></i></span>\
                    </td>\
                </tr>';
    $('.table-additional-supplies').find('tbody').prepend(html);
    ajaxSelectSeries('input[name="supplies['+unique+'][id_item]"]', 'admin/warranty/searchSupplies', idItem);
    ajaxSelectSeries('input[name="supplies['+unique+'][type_amount]"]', 'admin/warranty/searchType_amount', 1);
    $('input[name="supplies['+unique+'][type_amount]"]').trigger('change');
    resetSupplies();
    resetGrand_total_supplies();
    unique++;
  }
});

appValidateForm($('#additional-supplies-form'), {}, manage_additional_warranty);
function manage_additional_warranty(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float(response.alert_type, response.message);
            $('#additional_supplies').modal('hide');
        }
    });
    return false;
}
</script>