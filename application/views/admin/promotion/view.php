<?php init_head(); ?>
<style type="text/css">
    .popover-content {
        padding: 0px !important;
    }
    .css-group-addon {
        padding: 5px 15px;
    }
    .css-group-addon:hover {
        background: #ccc;
    }
    .tab-pane{
        display: none;
    }
    .tab-pane.active{
        display: block;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?=admin_url('promotion')?>" class="btn btn-info H_action_button pull-right"><?=_l('go_back')?></a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-heading fsize18 bold"><?=_l('lead_general_info')?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover m-group0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p class="bold margin0"><?=_l('promotion_name')?></p>
                                                </td>
                                                <td>
                                                    <p class="margin0"><?=(!empty($dataMain->name) ? $dataMain->name : '')?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="type" id="type" value="<?=(!empty($dataMain->type) ? $dataMain->type : '')?>">
                                                    <p class="bold margin0"><?=_l('promotion_type')?></p>
                                                </td>
                                                <td>
                                                    <p class="margin0"><?=(isset($dataType) ? $dataType : '')?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="method_of_application" id="method_of_application" value="<?=(!empty($dataMain->method_of_application) ? $dataMain->method_of_application : '')?>">
                                                    <p class="bold margin0"><?=_l('promotion_method_of_application')?></p>
                                                </td>
                                                <td>
                                                    <p class="margin0"><?=(isset($dataMethod) ? $dataMethod : '')?></p>
                                                </td>
                                            </tr>
                                            <tr class="div-area_of_application <?=(!empty($dataMain->method_of_application) && $dataMain->method_of_application == 'other' ? 'hide' : '')?>">
                                                <td>
                                                    <input type="hidden" name="area_of_application" id="area_of_application" value="<?=(!empty($dataMain->area_of_application) ? $dataMain->area_of_application : '')?>">
                                                    <p class="bold margin0"><?=_l('promotion_area_of_application')?></p>
                                                </td>
                                                <td>
                                                    <p class="margin0"><?=(isset($dataArea) ? $dataArea : '')?></p>
                                                </td>
                                            </tr>
                                            <tr class="div-area <?=((!empty($dataMain->area_of_application) && $dataMain->area_of_application == 'area') ? '' : 'hide')?>">
                                                <td>
                                                    <p class="bold margin0"><?=_l('customer_group')?></p>
                                                </td>
                                                <td>
                                                    <p class="margin0"><?=(isset($dataGroup_customer) ? $dataGroup_customer : '')?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="bold margin0"><?=_l('promotion_time')?></p>
                                                </td>
                                                <td>
                                                    <p class="margin0"><?=!empty($dataMain->date_active_start) || !empty($dataMain->date_active_end) ? _d($dataMain->date_active_start) . ' - ' . _d($dataMain->date_active_end) : _d(date('Y-m-d')) . ' - ' . _d(date('Y-m-d'))?></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="panel panel-default">
                                <div class="panel-heading fsize18 bold"><?=_l('ch_purchases_items')?></div>
                                <div class="panel-body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#gift_info" aria-controls="gift_info" role="tab" data-toggle="tab"><?=_l('gift_info')?></a>
                                        </li>
                                        <li role="presentation" class="customer_info">
                                            <a class="event_table" href="#customer_info" aria-controls="customer_info" role="tab" data-toggle="tab"><?=_l('customer_profile_details')?></a>
                                        </li>
                                    </ul>
                                    <div role="tabpanel" class="tab-pane active" id="gift_info">
                                        <!-- KM theo chiết khấu -->
                                        <div class="promotion_by_discount">
                                            <div class="col-md-12">
                                                <div class="form-group margin0">
                                                    <label for="time_sales" class="control-label bold">
                                                        <?php echo _l('promotion_time_sales'); ?>
                                                    </label>
                                                    <div class="input-group" style="width: 100%;">
                                                        <p>Từ <?=!empty($dataSub->time_sales_start) || !empty($dataSub->time_sales_end) ? _d($dataSub->time_sales_start) . ' Đến ' . _d($dataSub->time_sales_end) : _d(date('Y-m-d')) . ' - ' . _d(date('Y-m-d'))?></p>
                                                    </div>
                                                </div>
                                                <div class="radio radio-primary pull-left">
                                                    <input type="radio" name="type_discount" value="1" <?=!isset($dataMain) || (isset($dataSub) && ($dataSub->type_discount == 1)) || ($dataMain->type != 'discount') ? 'checked' : ''?>>
                                                    <label for="single"><?=_l('promotion_type_sales')?></label>
                                                </div>
                                                <div class="radio radio-primary pull-left mbot10 mleft20" style="margin-top: 10px !important;">
                                                    <input type="radio" name="type_discount" value="2" <?=isset($dataMain) && (isset($dataSub) && ($dataSub->type_discount == 2)) ? 'checked' : ''?>>
                                                    <label for="single"><?=_l('promotion_type_sales_gift')?></label>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading fsize18 bold"><?=_l('detail_gift')?></div>
                                                    <div class="panel-body">
                                                        <table class="tnh-tb table-bordered table-hover m-group0" style="table-layout: fixed;">
                                                            <thead>
                                                                <tr>
                                                                    <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                                    <th class="center" style="width: 45%;"><?=_l('promotion_limit_sales')?></th>
                                                                    <th class="center" style="width: 45%;"><?=_l('promotion_limit_discount')?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $i = 0; ?>
                                                                <?php foreach ($dataAmount as $key => $value) { ?>
                                                                    <tr>
                                                                        <td class="center stt">
                                                                            <?=++$key?>
                                                                            <input type="hidden" name="item_discount[<?=$i?>][id_amount]" value="<?=$value['id_amount']?>">
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group">
                                                                                <input type="text" class="form-control limit_sales" name="item_discount[<?=$i?>][limit_sales]" style="width: 100%; text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="<?=$value['limit_sales']?>" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group" app-field-wrapper="limit_discount">
                                                                                <div class="input-group" style="width: 100%;">
                                                                                    <input type="text" class="form-control limit_discount" name="item_discount[<?=$i?>][limit_discount]" style="text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="<?=$value['limit_discount']?>" readonly>
                                                                                    <input type="hidden" name="item_discount[<?=$i?>][type_discount]" class="type_discount" value="<?=$value['type_limit_discount']?>">
                                                                                    <div class="input-group-addon pointer change-group-addon">
                                                                                        <span><i class="fa fa-cog"></i></span>
                                                                                        <span class="text-group-addon"><?=$value['type_limit_discount']?></span>
                                                                                        <div class="content-menu hide">
                                                                                            <div class="pointer css-group-addon val-group-addon">%</div>
                                                                                            <div class="pointer css-group-addon val-group-addon">VNĐ</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php $i++; ?>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="H_content">
                                                    <p class="bold"><?=_l('note')?></p>
                                                    <p>- <?=(isset($dataMain) && isset($dataSub) && $dataMain->type == 'discount' && !empty($dataSub->note) ? $dataSub->note : '')?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end -->
                                        <!-- KM theo bộ -->
                                        <div class="promotion_by_item hide">
                                            <div class="panel panel-default">
                                                <div class="panel-heading fsize18 bold"><?=_l('detail_gift')?></div>
                                                <div class="panel-body">
                                                    <table class="tnh-tb table-bordered table-hover m-group0 js-table-promotion" style="table-layout: fixed;">
                                                        <thead>
                                                            <tr>
                                                                <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                                <th class="center" style="width: 70%;"><?=_l('promotion_item')?></th>
                                                                <th class="center" style="width: 20%;"><?=_l('promotion_number')?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $note_type_item = ''; ?>
                                                            <?php if(isset($dataMain) && $dataMain->type == 'item') { ?>
                                                                <?php foreach ($dataItem as $key => $value) { ?>
                                                                    <tr class="tr-parent">
                                                                        <td class="center stt"><?=++$key?></td>
                                                                        <td>
                                                                            <input class="id_item" type="hidden" name="item[<?=$i?>][id_item]" value="<?=$value['id_item']?>">
                                                                            <?php if($value['img_item'] != '') { ?>
                                                                            <img class="img_option" src="<?=base_url().$value['img_item']?>"> <?=$value['name_item']?>
                                                                            <?php } else { ?>
                                                                                <span> <?=$value['name_item']?></span>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group">
                                                                                <input type="text" name="item[<?=$i?>][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="<?=$value['quantity']?>" readonly>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $note_type_item = $value['note']; ?>
                                                                    <?php $i++; ?>
                                                                    <?php foreach ($value['dataGift'] as $key_Gift => $value_Gift) { ?>
                                                                        <tr>
                                                                            <td>
                                                                                <input class="itemsGift_id" type="hidden" name="items_gift[<?=$i?>][id]" value="<?=$value_Gift['id_item']?>">
                                                                                <input class="items_id" type="hidden" name="items_gift[<?=$i?>][items_id]" value="<?=$value['id_item']?>">
                                                                            </td>
                                                                            <td class="padding20">
                                                                                <span class="inline-block label label-warning"><?=_l('item_gift')?></span>
                                                                                <img class="img_option" src="<?=base_url().$value_Gift['img_item']?>"> <?=$value_Gift['name_item']?>
                                                                            </td>
                                                                            <td class="center">
                                                                                <div class="form-group">
                                                                                    <input style="width: 100%; text-align: right;" type="text" class="form-control" onkeyup="formatNumBerKeyUp(this)" name="items_gift[<?=$i?>][quantity]" value="<?=$value_Gift['quantity']?>" readonly>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php $i++; ?>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="H_content">
                                                    <p class="bold"><?=_l('note')?></p>
                                                    <p>- <?=$note_type_item?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end -->
                                        <!-- KM theo doanh số -->
                                        <div class="promotion_by_sales hide">
                                            <div class="col-md-12">
                                                <div class="form-group margin0">
                                                    <label for="date_active_sales" class="control-label bold">
                                                        <?php echo _l('promotion_time_sales'); ?>
                                                    </label>
                                                    <div class="input-group" style="width: 100%;">
                                                        <p>Từ <?=!empty($dataSub->date_active_sales_start) || !empty($dataSub->date_active_sales_end) ? _d($dataSub->date_active_sales_start) . ' Đến ' . _d($dataSub->date_active_sales_end) : _d(date('Y-m-d')) . ' - ' . _d(date('Y-m-d'))?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" app-field-wrapper="limit_points">
                                                    <label for="limit_points" class="control-label"><?=_l('promotion_limit_sales_points')?></label>
                                                    <div class="input-group" style="width: 100%;">
                                                        <input type="text" name="limit_points" class="form-control text-right limit_points" aria-invalid="false" onkeyup="formatNumBerKeyUp(this)" value="<?=isset($dataMain) && isset($dataSub) && !empty($dataSub->limit_points) ? number_format($dataSub->limit_points) : 0?>" readonly>
                                                        <input type="hidden" name="type_limit_points" class="type_limit_points" value="<?=isset($dataMain) && isset($dataSub) && !empty($dataSub->type_limit_points) ? $dataSub->type_limit_points : _l('money')?>">
                                                        <div class="input-group-addon pointer change-group-addon">
                                                            <span><i class="fa fa-cog"></i></span>
                                                            <span class="text-group-addon"><?=isset($dataMain) && !empty($dataSub->type_limit_points) ? $dataSub->type_limit_points : _l('money')?></span>
                                                            <div class="content-menu hide">
                                                                <div class="pointer css-group-addon val-group-addon"><?=_l('money')?></div>
                                                                <div class="pointer css-group-addon val-group-addon"><?=_l('points')?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="div-type-sales">
                                                <div class="col-md-12">
                                                    <div class="radio radio-primary pull-left">
                                                        <input type="radio" name="type_sales" value="1" <?=!isset($dataMain) || (isset($dataSub) && (!empty($dataSub->type_sales) && $dataSub->type_sales == 1)) || (isset($dataSub) && empty($dataSub->type_sales)) || ($dataMain->type != 'sales') ? 'checked' : ''?>>
                                                        <label for="single"><?=_l('promotion_type_points')?></label>
                                                    </div>
                                                    <div class="radio radio-primary pull-left mbot10 mleft20" style="margin-top: 10px !important;">
                                                        <input type="radio" name="type_sales" value="2" <?=isset($dataMain) && isset($dataSub) && !empty($dataSub->type_sales) && $dataSub->type_sales == 2 ? 'checked' : ''?>>
                                                        <label for="single"><?=_l('promotion_type_points_gift')?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading fsize18 bold"><?=_l('detail_gift')?></div>
                                                    <div class="panel-body">
                                                        <table class="tnh-tb table-bordered table-hover m-group0 js-table-promotion-sales" style="table-layout: fixed;">
                                                            <thead>
                                                                <tr>
                                                                    <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                                    <th class="center" style="width: 70%;"><?=_l('promotion_item_gift')?></th>
                                                                    <th class="center" style="width: 20%;"><?=_l('promotion_number_gift')?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($dataItem_gift as $key => $value) { ?>
                                                                    <tr class="tr-parent">
                                                                        <td class="center stt"><?=++$key?></td>
                                                                        <td>
                                                                            <input class="id_item" type="hidden" name="item[<?=$i?>][id_item]" value="<?=$value['id_item']?>">
                                                                            <img class="img_option" src="<?=base_url().$value['img_item']?>"> <?=$value['name_item']?>
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group">
                                                                                <input type="text" name="item[<?=$i?>][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="<?=$value['quantity']?>" readonly>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php $i++; ?>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                              <?php echo render_input('exchange_amount','exchange_amount_value',(isset($dataMain) && isset($dataSub) && $dataMain->type == 'sales' && !empty($dataSub->exchange_amount) ? number_format($dataSub->exchange_amount) : 0),'text',array('onkeyup'=>'formatNumBerKeyUp(this)','readonly'=>true)); ?>
                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center mtop15">
                                              <span style="font-weight: bold; font-size: 35px;">&#8644;</span>
                                            </div>
                                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                              <?php echo render_input('exchange_points','exchange_points_value',(isset($dataMain) && isset($dataSub) && $dataMain->type == 'sales' && !empty($dataSub->exchange_points) ? number_format($dataSub->exchange_points) : 0),'text',array('onkeyup'=>'formatNumBerKeyUp(this)','readonly'=>true)); ?>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-12">
                                                <div class="H_content">
                                                    <p class="bold"><?=_l('note')?></p>
                                                    <p>- <?=isset($dataMain) && isset($dataSub) && $dataMain->type == 'sales' && !empty($dataSub->note) ? $dataSub->note : ''?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end -->
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="customer_info">
                                        <table class="tnh-tb table-bordered table-hover m-group0 js-table-customer" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                    <th class="center" style="width: 90%;"><?=_l('cong_name_client')?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dataCustomer as $key => $value) { ?>
                                                    <tr class="tr-parent">
                                                        <td class="center stt"><?=++$key?></td>
                                                        <td>
                                                            <input class="id_item" type="hidden" name="customer[<?=$i?>][id_item]" value="<?=$value['customer_id']?>"><?=$value['company']?>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function(){
        $('#type').trigger('change');
        $('#method_of_application').trigger('change');
        $('#area_of_application').trigger('change');
    });
    $('#type').change(function(e){
        var type = $(this).val();
        if(type == 'discount') {
            $('.promotion_by_discount').removeClass('hide');
            $('.promotion_by_item').addClass('hide');
            $('.promotion_by_sales').addClass('hide');
        }
        else if(type == 'item') {
            $('.promotion_by_discount').addClass('hide');
            $('.promotion_by_item').removeClass('hide');
            $('.promotion_by_sales').addClass('hide');
        }
        else if(type == 'sales') {
            $('.promotion_by_discount').addClass('hide');
            $('.promotion_by_item').addClass('hide');
            $('.promotion_by_sales').removeClass('hide');
        }
    });
    $('#method_of_application').change(function(e){
        var type = $(this).val();
        if(type == 'other') {
            $('.div-area_of_application').addClass('hide');
            $('.customer_info').removeClass("no-drop-v2");
            $('.customer_info').find('.event_table').removeClass("none-event");
        }
        else {
            $('.div-area_of_application').removeClass('hide');
            $(".div-area").addClass("hide");
            $('.customer_info').addClass("no-drop-v2");
            $('.customer_info').find('.event_table').addClass("none-event");
        }
    });
    $('#area_of_application').change(function(e){
        var type = $(this).val();
        if($('#method_of_application').val() != 'other') {
            if(type == 'other') {
                $(".div-area").fadeOut("slow", function() {
                    $(this).addClass("hide");
                });
                $('.customer_info').removeClass("no-drop-v2");
                $('.customer_info').find('.event_table').removeClass("none-event");
            }
            else if(type == 'area'){
                $(".div-area").fadeIn("slow", function() {
                    $(this).removeClass("hide");
                });
                $('.customer_info').addClass("no-drop-v2");
                $('.customer_info').find('.event_table').addClass("none-event");
            }
            else{
                $(".div-area").fadeOut("slow", function() {
                    $(this).addClass("hide");
                });
                $('.customer_info').addClass("no-drop-v2");
                $('.customer_info').find('.event_table').addClass("none-event");
            }
        }
    });
</script>
</body>
</html>
