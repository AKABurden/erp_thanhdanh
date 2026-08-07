<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tab-pane{
        display: none;
    }
    .tab-pane.active{
        display: block;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        </div>
    </div>
    <div class="content">
        <?php echo form_open($this->uri->uri_string(), array('id' => 'delivery_policy-form')); ?>
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 50px;">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#market" aria-controls="market" role="tab" data-toggle="tab"><?=_l('applicable_market')?></a>
                            </li>
                            <li role="presentation">
                                <a href="#products" aria-controls="products" role="tab" data-toggle="tab"><?=_l('applicable_products')?></a>
                            </li>
                        </ul>
                        <!-- tab thị trường -->
                        <div role="tabpanel" class="tab-pane active" id="market">
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading fsize18 bold"><?=_l('lead_general_info')?></div>
                                    <div class="panel-body">
                                        <div class="form-group" app-field-wrapper="city">
                                            <label for="city" class="control-label"><?=_l('cong_client_city')?></label>
                                            <div class="dropdown bootstrap-select bs3" style="width: 100%;">
                                                <select id="city" name="city" class="selectpicker" data-width="100%" data-none-selected-text="<?=_l('dropdown_non_selected_tex')?>" data-live-search="true">
                                                    <option value=""></option>
                                                    <?php foreach ($province as $key => $value) { ?>
                                                        <option value="<?=$value['provinceid']?>"><?=$value['name']?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="panel panel-default">
                                    <div class="panel-heading fsize18 bold"><?=_l('ch_purchases_items')?></div>
                                    <div class="panel-body">
                                        <table class="tnh-tb table-bordered table-hover m-group0 js-table-market" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                    <th class="center" style="width: 60%;"><?=_l('cong_client_city')?></th>
                                                    <th class="center" style="width: 20%;"><?=_l('total_price_order')?></th>
                                                    <th class="center deleteTrItem_all" style="width: 10%; color: red; cursor: pointer;"><?=_l('delete')?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $checkExists = get_table_where('tbldelivery_policy_market',array(),'','row'); ?>
                                                <tr class="empty-item <?=($checkExists ? 'hide' : '')?>">
                                                    <td colspan="4" class="text-center">
                                                        <img src="<?=base_url('assets/images/table-no-data.png')?>">
                                                    </td>
                                                </tr>
                                                <?php $unique = 0; ?>
                                                <?php foreach ($data_market as $key => $value) { ?>
                                                    <tr>
                                                        <td class="text-center stt"><?=++$key?></td>
                                                        <td class="text-left">
                                                            <?=$value['name_province']?>
                                                            <input type="hidden" class="id_city" name="market[<?=$unique?>][id_city]" value="<?=$value['id_province']?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="text" name="market[<?=$unique?>][amount]" class="form-control amount" onchange="formatNumBerKeyUp(this)" value="<?=$value['amount']?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php $unique++; ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab sản phẩm -->
                        <div role="tabpanel" class="tab-pane" id="products">
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading fsize18 bold"><?=_l('lead_general_info')?></div>
                                    <div class="panel-body">
                                        <div class="form-group" app-field-wrapper="group_products">
                                            <label for="group_products" class="control-label"><?=_l('group_products')?></label>
                                            <div class="dropdown bootstrap-select bs3" style="width: 100%;">
                                                <select id="group_products" name="group_products" class="selectpicker" data-width="100%" data-none-selected-text="<?=_l('dropdown_non_selected_tex')?>" data-live-search="true">
                                                    <option value=""></option>
                                                    <?php foreach ($group_product as $key => $value) { ?>
                                                        <option value="<?=$value['id']?>"><?=$value['category']?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="panel panel-default">
                                    <div class="panel-heading fsize18 bold"><?=_l('ch_purchases_items')?></div>
                                    <div class="panel-body">
                                        <table class="tnh-tb table-bordered table-hover m-group0 js-table-products" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                    <th class="center" style="width: 60%;"><?=_l('group_products')?></th>
                                                    <th class="center" style="width: 20%;"><?=_l('total_price_order')?> (%)</th>
                                                    <th class="center deleteTrItem_all_products" style="width: 10%; color: red; cursor: pointer;"><?=_l('delete')?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $checkExists2 = get_table_where('tbldelivery_policy_products',array(),'','row'); ?>
                                                <tr class="empty-item <?=($checkExists2 ? 'hide' : '')?>">
                                                    <td colspan="4" class="text-center">
                                                        <img src="<?=base_url('assets/images/table-no-data.png')?>">
                                                    </td>
                                                </tr>
                                                <?php foreach ($data_products as $key => $value) { ?>
                                                    <tr>
                                                        <td class="text-center stt"><?=++$key?></td>
                                                        <td class="text-left">
                                                            <?=$value['name_categories']?>
                                                            <input type="hidden" class="id_group" name="products[<?=$unique?>][id_group]" value="<?=$value['id_categories']?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="text" name="products[<?=$unique?>][percent]" class="form-control percent" value="<?=$value['percent']?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="btn btn-danger deleteTrItem_products"><i class="fa fa-times"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php $unique++; ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info pull-right">
                <?php echo _l( 'submit'); ?>
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>

var unique = <?=$unique?>;
$(document).on('change','select[name="city"]', function (e) {
    var id_city = $(this).val();
    if(id_city) {
        if($('.js-table-market').find('tbody').find('input[class=id_city][value='+id_city+']').length) {
            alert_float('danger', '<?=_l('city_exists')?>');
        }
        else {
            $('.js-table-market').find('tbody').find('.empty-item').addClass('hide');
            var html = '<tr>\
                            <td class="text-center stt"></td>\
                            <td class="text-left">\
                                '+$("select[name=city] option:selected").html()+'\
                                <input type="hidden" class="id_city" name="market['+unique+'][id_city]" value="'+id_city+'">\
                            </td>\
                            <td class="text-center">\
                                <input type="text" name="market['+unique+'][amount]" class="form-control amount" onchange="formatNumBerKeyUp(this)" value="0">\
                            </td>\
                            <td class="text-center">\
                                <a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a>\
                            </td>\
                        </tr>';
            $('.js-table-market').find('tbody').prepend(html);
            unique++;
            resetSTT_market();
        }
    }
});

$(document).on('change','select[name="group_products"]', function (e) {
    var id_group = $(this).val();
    if(id_group) {
        if($('.js-table-products').find('tbody').find('input[class=id_group][value='+id_group+']').length) {
            alert_float('danger', '<?=_l('group_exists')?>');
        }
        else {
            $('.js-table-products').find('tbody').find('.empty-item').addClass('hide');
            var html = '<tr>\
                            <td class="text-center stt"></td>\
                            <td class="text-left">\
                                '+$("select[name=group_products] option:selected").html()+'\
                                <input type="hidden" class="id_group" name="products['+unique+'][id_group]" value="'+id_group+'">\
                            </td>\
                            <td class="text-center">\
                                <input type="text" name="products['+unique+'][percent]" class="form-control percent" value="0">\
                            </td>\
                            <td class="text-center">\
                                <a class="btn btn-danger deleteTrItem_products"><i class="fa fa-times"></i></a>\
                            </td>\
                        </tr>';
            $('.js-table-products').find('tbody').prepend(html);
            unique++;
            resetSTT_products();
        }
    }
});

$(document).on('keyup', '.percent', (e)=>{
    var current = $(e.currentTarget);
    if(current.val() > 100) {
        current.val(100);
    }
    if(current.val() < 0) {
        current.val(0);
    }
});
function resetSTT_market() {
    var all_tr = $('.js-table-market').find('tbody').find('tr:not(.empty-item)');
    var stt = 1;
    $.each(all_tr, function(i, v){
        $(this).find('.stt').text(stt);
        stt++;
    });
}

function resetSTT_products() {
    var all_tr = $('.js-table-products').find('tbody').find('tr:not(.empty-item)');
    var stt = 1;
    $.each(all_tr, function(i, v){
        $(this).find('.stt').text(stt);
        stt++;
    });
}

$(document).on('click', '.deleteTrItem', (e)=>{
    var current = $(e.currentTarget);
    current.parents('tr').remove();
    if($('.js-table-market').find('tbody').find('tr:not(.empty-item)').length == 0) {
        $('.js-table-market').find('tbody').find('.empty-item').removeClass('hide');
    }
    else {
        resetSTT_market();
    }
});

$(document).on('click', '.deleteTrItem_products', (e)=>{
    var current = $(e.currentTarget);
    current.parents('tr').remove();
    if($('.js-table-products').find('tbody').find('tr:not(.empty-item)').length == 0) {
        $('.js-table-products').find('tbody').find('.empty-item').removeClass('hide');
    }
    else {
        resetSTT_products();
    }
});

$(document).on('click', '.deleteTrItem_all', (e)=>{
    var all_tr = $('.js-table-market').find('tbody').find('tr:not(.empty-item)');
    $.each(all_tr, function(i, v){
        $(this).remove();
        $('.js-table-market').find('tbody').find('.empty-item').removeClass('hide');
    });
});

$(document).on('click', '.deleteTrItem_all_products', (e)=>{
    var all_tr = $('.js-table-products').find('tbody').find('tr:not(.empty-item)');
    $.each(all_tr, function(i, v){
        $(this).remove();
        $('.js-table-products').find('tbody').find('.empty-item').removeClass('hide');
    });
});


</script>
</body>
</html>
