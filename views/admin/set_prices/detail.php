<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a class="btn btn-info pull-right H_action_button" onclick="import_data(<?=$id?>); return false;">
                <?php echo _l('IMPORT'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <?php
            echo form_open($this->uri->uri_string(), array('id' => 'set-prices-detail-form'));
        ?>
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 60px;">
                <div class="wap_result_import <?=($this->session->flashdata('message') ? '' : 'hide')?>">
                    <hr />
                    <div class="col-md-12">
                        <div class="result_import bold"><?=$this->session->flashdata('message')?></div>
                    </div>
                    <div class="clearfix"></div>
                    <hr />
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-heading fsize18 bold"><?=_l('cong_info_items')?></div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?=_l('select_categories')?>
                                    <select name="category" id="category" data-placeholder="<?= lang('ch_categories') ?>" class="modal-select2" style="width: 100%;">
                                        <option value=""></option>
                                        <?= recursive_Category_By_Items() ?>
                                        <?= recursive_Category_By_Products() ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?=_l('select_item')?>
                                    <input data-placeholder="<?=_l('choise_item')?>" class="custom_item_select" style="width: 100%" id="custom_item_select">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div style="max-height: 400px; overflow: auto;">
                                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                <?php echo _l('ch_image'); ?>
                                            </th>
                                            <th class="text-center">
                                                <?php echo _l('item_name'); ?>
                                            </th>
                                            <th class="text-right">
                                                <?php echo _l('cost_price'); ?>
                                            </th>
                                            <th class="text-right">
                                                <?php echo _l('item_price_last'); ?>
                                            </th>
                                            <th class="text-right">
                                                <?php echo _l('item_new_price'); ?>
                                            </th>
                                            <th class="text-center">
                                                <?php echo _l('delete'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table">
                                        <?php $i = 0; ?>
                                        <?php foreach ($dataSub as $key => $value) { ?>
                                            <?php
                                                $type_name = '';
                                                if($value['type_item'] == 'product') {
                                                    if(empty($value['images'])) {
                                                        $value['images'] = 'uploads/no-img.jpg';
                                                    }
                                                    else {
                                                        $value['images'] = 'uploads/products/'.$value['images'];
                                                    }
                                                    $type_name = '<br><br><span class="label label-warning">Thành phẩm</span>';
                                                }
                                                else if($value['type_item'] == 'items') {
                                                    if(empty($value['images'])) {
                                                        $value['images'] = 'uploads/no-img.jpg';
                                                    }
                                                    $type_name = '<br><br><span class="label label-warning">Hàng hóa</span>';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <img width="50" src="<?=base_url().$value['images']?>">
                                                    <?= $type_name; ?>
                                                    <input type="hidden" class="id_items" name="items[<?=$i?>][id_items]" value="<?=$value['type_item'].'_'.$value['id_item']?>">
                                                </td>
                                                <td class="text-center"><?=$value['name_item']?> (<?=$value['code_item']?>)</td>
                                                <td class="text-right"><?=number_format($value['price_import'])?></td>
                                                <td class="text-right"><?=number_format($value['last_price'])?></td>
                                                <td>
                                                    <input style="width: 100%;" type="text" class="text-right padding10" name="items[<?=$i?>][prices_new]" onkeyup="formatNumBerKeyUp(this)" value="<?=number_format($value['prices_new'])?>">
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-danger" onclick="deleteTrItem(this); return false;">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                    <div class="clearfix"></div>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading fsize18 bold"><?=_l('ch_purchases_items')?></div>
                        <div class="panel-body">
                            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php echo _l('name_set_prices'); ?>
                                        </td>
                                        <td>
                                            <?=$dataMain->name?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php echo _l('date_active'); ?>
                                        </td>
                                        <td>
                                            <?php
                                                if($dataMain->checkbox_date == 1) {
                                                    echo '<span class="inline-block label label-success">'._l('no_limit').'</span>';
                                                }
                                                else {
                                                    echo _d($dataMain->date_start).' - '._d($dataMain->date_end);
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php echo _l('status'); ?>
                                        </td>
                                        <td>
                                            <?php
                                                if ($dataMain->status == 1) {
                                                    echo '<span class="inline-block label label-success">'._l('apply').'</span>';
                                                }
                                                else {
                                                    echo '<span class="inline-block label label-danger">'._l('dont_apply').'</span>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?=_l('range_customer')?>
                                        </td>
                                        <td>
                                            <?php
                                                $count = explode(',', $dataMain->id_groups);
                                                if(!$dataMain->id_groups) {
                                                    $count = array();
                                                }
                                                if($dataMain->type_customer == 1) {
                                                    echo '<span class="inline-block label label-warning">'._l('all_customer').'</span>';
                                                }
                                                else if($dataMain->type_customer == 2) {
                                                    if(count($count) > 0) {
                                                        $this->db->select('tblcustomers_groups.*');
                                                        $this->db->where_in('id',$count);
                                                        $get_data_customers_groups = $this->db->get('tblcustomers_groups')->result_array();
                                                    }
                                                    
                                                    $_data = '<span class="inline-block label label-danger pointer '.(count($count) > 0 ? 'js-menu-status' : '').'">'._l('customer_group').' ('.count($count).')';
                                                    $_data .= '<div class="content-menu hide">';
                                                    if(isset($get_data_customers_groups)) {
                                                        foreach ($get_data_customers_groups as $key => $value) {
                                                            $_data .= '<div>'.$value['name'].'</div>';
                                                        }
                                                    }
                                                    $_data .= '</div>';
                                                    $_data .= '</span>';
                                                    echo $_data;
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php echo _l('item_price'); ?>
                                        </td>
                                        <td>
                                            <?php if(is_numeric($dataMain->type_price_setting)) { ?>
                                                <?php $get_name = get_table_where('tbl_set_prices',array('id'=>$dataMain->type_price_setting),'','row'); ?>
                                                [<?= $get_name->name .' '. ($dataMain->sum_OR_sub == 'sum' ? '+' : '-') .' '. number_format($dataMain->value_price_setting) .' '. ($dataMain->vnd_OR_percent == 'vnd' ? '(vnd)' : '(%)')?>]
                                            <?php } else { ?>
                                                [<?= $dataMain->type_price_setting .' '. ($dataMain->sum_OR_sub == 'sum' ? '+' : '-') .' '. number_format($dataMain->value_price_setting) .' '. ($dataMain->vnd_OR_percent == 'vnd' ? '(vnd)' : '(%)')?>]
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <button class="btn btn-info pull-right form-submitersss"><?=_l('submit')?></button>
                <a href="<?=admin_url('set_prices')?>" class="btn btn-default pull-right mright5"><?=_l('go_back')?></a>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal_import"></div>
<?php init_tail(); ?>
<script>
    ajaxSelectCallBack($('#custom_item_select'), "<?=admin_url('set_prices/SearchItems')?>", 0);
    function ajaxSelectCallBack(element, url, id, types = '')
    {
        $(element).select2({
            width: 'resolve',
            allowClear: true,
            ajax: {
                url: url + '/' + $(element).val(),
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        types: types,
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{code_client:'',id: '', text: 'No Match Found'}]};
                    }
                }
            },
            formatResult: repoFormatSelection_code,
            formatSelection: repoFormatSelection_code,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; }
        });
    }
    function repoFormatSelection_code(state) {
        if (!state.id) return state.text;
        if(state.img)
        {
            var img = '<img class="img_option" src="'+site_url +state.img+'"/> ';
        }
        else
        {
            var img = '<img class="img_option" src="'+site_url +'download/preview_image"/> ';
        }
        var Stringreturn = img + state.text;
        if(state.price)
        {
            Stringreturn += ' - '+ C_formatNumber(state.price)
        }
        if(state.code)
        {
            Stringreturn += ' ('+ state.code +') ';
        }

        return  Stringreturn ;
    }
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
    $('#category').select2({'allowClear': true});
    var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>'; 
    $(document).on('mouseenter','.js-menu-status', function (e) {
        $(this).popover({
            html: true,
            placement: "left",
            trigger: 'hover',
            title:'<?php echo _l('range_customer'); ?>',
            content: function() {
                return $(this).find('.content-menu').html();
            },
            template: inner_popover_template
        });
    }).on('mouseleave','.js-menu-status',  function(){
        $('.js-menu-status').popover('hide');
    });

    var value_price_setting = <?=(!empty($dataMain->value_price_setting) ? $dataMain->value_price_setting : 0)?>;
    var unique = <?=$i?>;
    $('#category').change(function(e){
        var current = $(e.currentTarget);
        var id = current.val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/getData_items_by_category/'+id, data).done(function(response){
            response = JSON.parse(response);
            $.each(response, function(i, v){
                var checkValue = true;
                if($('.body-table').find('input[class=id_items][value='+v.type_item+'_'+v.id+']').length) {
                    checkValue = false;
                }
                if(checkValue === true) {
                    var type_name = '';
                    var newTr = $('<tr></tr>');
                    if(v.type_item == 'product') {
                        if(!v.images || v.images == '') {
                            v.images = 'uploads/no-img.jpg';
                        }
                        else {
                            v.images = 'uploads/products/'+v.images;
                        }
                        type_name = '<br><br><span class="label label-warning">Thành phẩm</span>';
                    }
                    if(v.type_item == 'items') {
                        if(!v.images || v.images == '') {
                            v.images = 'uploads/no-img.jpg';
                        }
                        type_name = '<br><br><span class="label label-warning">Hàng hóa</span>';
                    }
                    var td1 = $('<td class="text-center"><img width="50" src="<?=base_url()?>'+v.images+'">'+type_name+'<input type="hidden" class="id_items" name="items['+unique+'][id_items]" value="'+v.type_item+'_'+v.id+'"></td>');
                    var td2 = $('<td class="text-center">'+v.name+' ('+v.code+')'+'</td>');
                    var td3 = $('<td class="text-right">'+formatNumber(v.price_import)+'</td>');
                    var td4 = $('<td class="text-right">'+formatNumber(v.last_price)+'</td>');

                    //tính giá mới
                    <?php if($dataMain->type_price_setting == 'giá vốn') { ?>
                        <?php if($dataMain->sum_OR_sub == 'sum') { ?>
                            <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                                var prices_new = Number(v.price_import) + Number(value_price_setting);
                            <?php } else { ?>
                                var prices_new = Number(v.price_import) + (Number(v.price_import)*Number(value_price_setting)/100);
                            <?php } ?>
                        <?php } else { ?>
                            <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                                var prices_new = Number(v.price_import) - Number(value_price_setting);
                            <?php } else { ?>
                                var prices_new = Number(v.price_import) - (Number(v.price_import)*Number(value_price_setting)/100);
                            <?php } ?>
                        <?php } ?>
                    <?php } else if($dataMain->type_price_setting == 'giá nhập cuối') { ?>
                        <?php if($dataMain->sum_OR_sub == 'sum') { ?>
                            <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                                var prices_new = Number(v.last_price) + Number(value_price_setting);
                            <?php } else { ?>
                                var prices_new = Number(v.last_price) + (Number(v.last_price)*Number(value_price_setting)/100);
                            <?php } ?>
                        <?php } else { ?>
                            <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                                var prices_new = Number(v.last_price) - Number(value_price_setting);
                            <?php } else { ?>
                                var prices_new = Number(v.last_price) - (Number(v.last_price)*Number(value_price_setting)/100);
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        var prices_new = 0;
                    <?php } ?>


                    var td5 = $('<td><input style="width: 100%;" type="text" class="text-right padding10" name="items['+unique+'][prices_new]" onkeyup="formatNumBerKeyUp(this)" value="'+formatNumber(prices_new)+'"></td>');
                    var td6 = $('<td class="text-center"><a class="btn btn-danger" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a><div class="clearfix"></div></td>');
                    //end
                    newTr.append(td1);
                    newTr.append(td2);
                    newTr.append(td3);
                    newTr.append(td4);
                    newTr.append(td5);
                    newTr.append(td6);
                    $('.body-table').prepend(newTr);
                    unique++;
                }
            });
        });
    });
    $('#custom_item_select').change(function(e){
        var current = $(e.currentTarget);
        var id = current.val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/getData_items_by_item/'+id, data).done(function(response){
            response = JSON.parse(response);
            var checkValue = true;
            if($('.body-table').find('input[class=id_items][value='+response.type_item+'_'+response.id+']').length) {
                checkValue = false;
                alert_float('warning','Sản phẩm đã được thêm, vui lòng kiểm tra lại!');
            }
            if(checkValue === true) {
                var type_name = '';
                var newTr = $('<tr></tr>');
                if(response.type_item == 'product') {
                    if(!response.images || response.images == '') {
                        response.images = 'uploads/no-img.jpg';
                    }
                    else {
                        response.images = 'uploads/products/'+response.images;
                    }
                    type_name = '<br><br><span class="label label-warning">Thành phẩm</span>';
                }
                if(response.type_items == 'items') {
                    if(!response.images || response.images == '') {
                        response.images = 'uploads/no-img.jpg';
                    }
                    type_name = '<br><br><span class="label label-warning">Hàng hóa</span>';
                }
                var td1 = $('<td class="text-center"><img width="50" src="<?=base_url()?>'+response.images+'">'+type_name+'<input type="hidden" class="id_items" name="items['+unique+'][id_items]" value="'+response.type_item+'_'+response.id+'"></td>');
                var td2 = $('<td class="text-center">'+response.name+' ('+response.code+')'+'</td>');
                var td3 = $('<td class="text-right">'+formatNumber(response.price_import)+'</td>');
                var td4 = $('<td class="text-right">'+formatNumber(response.last_price)+'</td>');

                //tính giá mới
                <?php if($dataMain->type_price_setting == 'giá vốn') { ?>
                    <?php if($dataMain->sum_OR_sub == 'sum') { ?>
                        <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                            var prices_new = Number(response.price_import) + Number(value_price_setting);
                        <?php } else { ?>
                            var prices_new = Number(response.price_import) + (Number(response.price_import)*Number(value_price_setting)/100);
                        <?php } ?>
                    <?php } else { ?>
                        <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                            var prices_new = Number(response.price_import) - Number(value_price_setting);
                        <?php } else { ?>
                            var prices_new = Number(response.price_import) - (Number(response.price_import)*Number(value_price_setting)/100);
                        <?php } ?>
                    <?php } ?>
                <?php } else if($dataMain->type_price_setting == 'giá nhập cuối') { ?>
                    <?php if($dataMain->sum_OR_sub == 'sum') { ?>
                        <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                            var prices_new = Number(response.last_price) + Number(value_price_setting);
                        <?php } else { ?>
                            var prices_new = Number(response.last_price) + (Number(response.last_price)*Number(value_price_setting)/100);
                        <?php } ?>
                    <?php } else { ?>
                        <?php if($dataMain->vnd_OR_percent == 'vnd') { ?>
                            var prices_new = Number(response.last_price) - Number(value_price_setting);
                        <?php } else { ?>
                            var prices_new = Number(response.last_price) - (Number(response.last_price)*Number(value_price_setting)/100);
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    var prices_new = 0;
                <?php } ?>

                var td5 = $('<td><input style="width: 100%;" type="text" class="text-right padding10" name="items['+unique+'][prices_new]" onkeyup="formatNumBerKeyUp(this)" value="'+formatNumber(prices_new)+'"></td>');
                var td6 = $('<td class="text-center"><a class="btn btn-danger" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a><div class="clearfix"></div></td>');
                //end
                newTr.append(td1);
                newTr.append(td2);
                newTr.append(td3);
                newTr.append(td4);
                newTr.append(td5);
                newTr.append(td6);
                $('.body-table').prepend(newTr);
                unique++;
            }
        });
    });
    function deleteTrItem(trItem){
        var current = $(trItem).parents('tr');
        current.remove();
    };

    function import_data(id) {
        $('.modal_import').html('');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/getData_import_data/'+id, data).done(function(response){
            $('.modal_import').append(response);
            $('#modal_import_data').modal({backdrop: 'static', keyboard: false});
        });
    }

    //load trang voi action
    _validate_form($('#import_form'),{},add_import_form);

    function add_import_form(form) {
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form);
            $('.wap-result-import').removeClass('hide');
            $('.result_import').append(form.message);
        }), !1
    }
    //end
</script>
</body>
</html>
