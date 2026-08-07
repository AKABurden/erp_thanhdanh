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
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        </div>
    </div>
    <div class="content">
        <?php echo form_open($this->uri->uri_string(), array('id' => 'promotion-form', 'class' => 'promotion-form')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-heading fsize18 bold"><?=_l('lead_general_info')?></div>
                                <div class="panel-body">
                                    <?php echo render_input('name', 'promotion_name',(!empty($dataMain->name) ? $dataMain->name : '')); ?>
                                    <?php
                                        $arr_type = array(
                                            array(
                                                'id'=>'discount',
                                                'name'=>_l('promotion_by_discount')
                                            ),
                                            array(
                                                'id'=>'item',
                                                'name'=>_l('promotion_by_item')
                                            ),
                                            array(
                                                'id'=>'sales',
                                                'name'=>_l('promotion_by_sales')
                                            )
                                        )
                                    ?>
                                    <?php echo render_select('type', $arr_type, array('id', 'name'),'promotion_type',(!empty($dataMain->type) ? $dataMain->type : ''),array(),array(),'','',false); ?>
                                    <?php
                                        $arr_method_of_application = array(
                                            array(
                                                'id'=>'one',
                                                'name'=>_l('promotion_application_one')
                                            ),
                                            array(
                                                'id'=>'all',
                                                'name'=>_l('promotion_application_all')
                                            ),
                                            array(
                                                'id'=>'other',
                                                'name'=>_l('promotion_application_other')
                                            )
                                        )
                                    ?>
                                    <?php echo render_select('method_of_application', $arr_method_of_application, array('id', 'name'),'promotion_method_of_application',(!empty($dataMain->method_of_application) ? $dataMain->method_of_application : '')); ?>
                                    <div class="div-area_of_application <?=(!empty($dataMain->method_of_application) && $dataMain->method_of_application == 'other' ? 'hide' : '')?>">
                                        <?php
                                            $arr_area_of_application = array(
                                                array(
                                                    'id'=>'all',
                                                    'name'=>_l('cong_all')
                                                ),
                                                array(
                                                    'id'=>'area',
                                                    'name'=>_l('promotion_area')
                                                ),
                                                array(
                                                    'id'=>'other',
                                                    'name'=>_l('promotion_area_other')
                                                )
                                            )
                                        ?>
                                        <?php echo render_select('area_of_application', $arr_area_of_application, array('id', 'name'),'promotion_area_of_application',(!empty($dataMain->area_of_application) ? $dataMain->area_of_application : ''),array('required'=>'required')); ?>
                                    </div>
                                    <div class="div-area <?=((!empty($dataMain->area_of_application) && $dataMain->area_of_application == 'area') ? '' : 'hide')?>">
                                        <?php
                                            echo render_select('groups_in[]',$groups,array('id','name'),'customer_group',(!empty($dataMain->groups_in) ? explode(',', $dataMain->groups_in) : ''),array('multiple'=>true, 'data-actions-box'=>true),array(),'','',false);
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_active" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('promotion_time'); ?>
                                        </label>
                                        <div class="input-group" style="width: 100%;">
                                            <input type="text" id="date_active" name="date_active" class="form-control date_active" aria-invalid="false" value="<?=!empty($dataMain->date_active_start) || !empty($dataMain->date_active_end) ? _d($dataMain->date_active_start) . ' - ' . _d($dataMain->date_active_end) : _d(date('Y-m-d')) . ' - ' . _d(date('Y-m-d'))?>">
                                            <div class="input-group-addon" onclick="show_calendar(this); return false;">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
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
                                                <div class="form-group">
                                                    <label for="time_sales" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('promotion_time_sales'); ?>
                                                    </label>
                                                    <div class="input-group" style="width: 100%;">
                                                        <input type="text" id="time_sales" name="time_sales" class="form-control time_sales" aria-invalid="false" value="<?=!empty($dataSub->time_sales_start) || !empty($dataSub->time_sales_end) ? _d($dataSub->time_sales_start) . ' - ' . _d($dataSub->time_sales_end) : _d(date('Y-m-d')) . ' - ' . _d(date('Y-m-d'))?>">
                                                        <div class="input-group-addon" onclick="show_calendar(this); return false;">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
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
                                                                    <th class="center" style="width: 40%;"><?=_l('promotion_limit_sales')?></th>
                                                                    <th class="center" style="width: 40%;"><?=_l('promotion_limit_discount')?></th>
                                                                    <th class="center" style="width: 10%;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="trMain">
                                                                    <td class="center"></td>
                                                                    <td class="center">
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control limit_sales" id="limit_sales" style="width: 100%; text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="0">
                                                                        </div>
                                                                    </td>
                                                                    <td class="center">
                                                                        <div class="form-group" app-field-wrapper="limit_discount">
                                                                            <div class="input-group" style="width: 100%;">
                                                                                <input type="text" id="limit_discount" name="limit_discount" class="form-control limit_discount" aria-invalid="false" style="text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="0">
                                                                                <div class="input-group-addon pointer change-group-addon">
                                                                                    <span><i class="fa fa-cog"></i></span>
                                                                                    <span class="text-group-addon">%</span>
                                                                                    <div class="content-menu hide">
                                                                                        <div class="pointer css-group-addon val-group-addon">%</div>
                                                                                        <div class="pointer css-group-addon val-group-addon">VNĐ</div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="center">
                                                                        <a class="btn btn-info createTrItem_discount"><i class="fa fa-check"></i></a>
                                                                    </td>
                                                                </tr>
                                                                <?php $i = 0; ?>
                                                                <?php foreach ($dataAmount as $key => $value) { ?>
                                                                    <tr>
                                                                        <td class="center stt">
                                                                            <?=++$key?>
                                                                            <input type="hidden" name="item_discount[<?=$i?>][id_amount]" value="<?=$value['id_amount']?>">
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group">
                                                                                <input type="text" class="form-control limit_sales" name="item_discount[<?=$i?>][limit_sales]" style="width: 100%; text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="<?=$value['limit_sales']?>">
                                                                            </div>
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group" app-field-wrapper="limit_discount">
                                                                                <div class="input-group" style="width: 100%;">
                                                                                    <input type="text" class="form-control limit_discount" name="item_discount[<?=$i?>][limit_discount]" style="text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="<?=$value['limit_discount']?>">
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
                                                                        <td class="center">
                                                                            <a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a>
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
                                                <?php echo render_textarea('note_type_discount','note', isset($dataMain) && isset($dataSub) && $dataMain->type == 'discount' && !empty($dataSub->note) ? $dataSub->note : ''); ?>
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
                                                                <th class="center" style="width: 60%;"><?=_l('promotion_item')?></th>
                                                                <th class="center" style="width: 20%;"><?=_l('promotion_number')?></th>
                                                                <th class="center" style="width: 10%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="trMain">
                                                                <td class="center"></td>
                                                                <td class="center td-custom_item_select">
                                                                    <div class="radio radio-primary pull-left">
                                                                        <input type="radio" name="type_select_item" value="1" checked>
                                                                        <label for="single"><?=_l('select_item')?></label>
                                                                    </div>
                                                                    <div class="radio radio-primary pull-left mbot10 mleft20" style="margin-top: 10px !important;">
                                                                        <input type="radio" name="type_select_item" value="2">
                                                                        <label for="single"><?=_l('select_categories')?></label>
                                                                    </div>
                                                                    <input data-placeholder="<?=_l('ch_list_objects')?>" class="custom_item_select" id="custom_item_select" style="width: 100%">
                                                                </td>
                                                                <td class="center">
                                                                    <?php echo render_input('number_request','',0,'text',array('onkeyup'=>'formatNumBerKeyUp(this)','style'=>'text-align: right;'),array(),'','mainQuantity'); ?>
                                                                </td>
                                                                <td class="center">
                                                                    <a class="btn btn-info createTrItem"><i class="fa fa-check"></i></a>
                                                                </td>
                                                            </tr>
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
                                                                            <br>
                                                                            <a class="btn btn-info mtop5 add_gift" data-id="<?=$value['id_item']?>"><?=_l('promotion_item_gift')?></a>
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group">
                                                                                <input type="text" name="item[<?=$i?>][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="<?=$value['quantity']?>">
                                                                            </div>
                                                                        </td>
                                                                        <td class="center">
                                                                            <a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a>
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
                                                                                    <input style="width: 100%; text-align: right;" type="text" class="form-control" onkeyup="formatNumBerKeyUp(this)" name="items_gift[<?=$i?>][quantity]" value="<?=$value_Gift['quantity']?>">
                                                                                </div>
                                                                            </td>
                                                                            <td class="center">
                                                                                <a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a>
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
                                                <?php echo render_textarea('note_type_item','note', $note_type_item); ?>
                                            </div>
                                        </div>
                                        <!-- end -->
                                        <!-- KM theo doanh số -->
                                        <div class="promotion_by_sales hide">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="date_active_sales" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('promotion_time_sales'); ?>
                                                    </label>
                                                    <div class="input-group" style="width: 100%;">
                                                        <input type="text" id="date_active_sales" name="date_active_sales" class="form-control date_active_sales" aria-invalid="false" value="<?=!empty($dataSub->date_active_sales_start) || !empty($dataSub->date_active_sales_end) ? _d($dataSub->date_active_sales_start) . ' - ' . _d($dataSub->date_active_sales_end) : _d(date('Y-m-d')) . ' - ' . _d(date('Y-m-d'))?>">
                                                        <div class="input-group-addon" onclick="show_calendar(this); return false;">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" app-field-wrapper="limit_points">
                                                    <label for="limit_points" class="control-label"><?=_l('promotion_limit_sales_points')?></label>
                                                    <div class="input-group" style="width: 100%;">
                                                        <input type="text" name="limit_points" class="form-control text-right limit_points" aria-invalid="false" onkeyup="formatNumBerKeyUp(this)" value="<?=isset($dataMain) && isset($dataSub) && !empty($dataSub->limit_points) ? number_format($dataSub->limit_points) : 0?>">
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
                                                                    <th class="center" style="width: 60%;"><?=_l('promotion_item_gift')?></th>
                                                                    <th class="center" style="width: 20%;"><?=_l('promotion_number_gift')?></th>
                                                                    <th class="center" style="width: 10%;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="trMain">
                                                                    <td class="center"></td>
                                                                    <td class="center td-custom_item_select">
                                                                        <input data-placeholder="<?=_l('ch_list_objects')?>" class="custom_item_select_sales" id="custom_item_select_sales" style="width: 100%">
                                                                    </td>
                                                                    <td class="center">
                                                                        <?php echo render_input('number_request_sales','',0,'text',array('onkeyup'=>'formatNumBerKeyUp(this)','style'=>'text-align: right;'),array(),'','mainQuantity'); ?>
                                                                    </td>
                                                                    <td class="center">
                                                                        <a class="btn btn-info createTrItem_sales"><i class="fa fa-check"></i></a>
                                                                    </td>
                                                                </tr>
                                                                <?php foreach ($dataItem_gift as $key => $value) { ?>
                                                                    <tr class="tr-parent">
                                                                        <td class="center stt"><?=++$key?></td>
                                                                        <td>
                                                                            <input class="id_item" type="hidden" name="item[<?=$i?>][id_item]" value="<?=$value['id_item']?>">
                                                                            <img class="img_option" src="<?=base_url().$value['img_item']?>"> <?=$value['name_item']?>
                                                                        </td>
                                                                        <td class="center">
                                                                            <div class="form-group">
                                                                                <input type="text" name="item[<?=$i?>][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="<?=$value['quantity']?>">
                                                                            </div>
                                                                        </td>
                                                                        <td class="center">
                                                                            <a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a>
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
                                              <?php echo render_input('exchange_amount','exchange_amount_value',(isset($dataMain) && isset($dataSub) && $dataMain->type == 'sales' && !empty($dataSub->exchange_amount) ? number_format($dataSub->exchange_amount) : 0),'text',array('onkeyup'=>'formatNumBerKeyUp(this)')); ?>
                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center mtop15">
                                              <span style="font-weight: bold; font-size: 35px;">&#8644;</span>
                                            </div>
                                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                              <?php echo render_input('exchange_points','exchange_points_value',(isset($dataMain) && isset($dataSub) && $dataMain->type == 'sales' && !empty($dataSub->exchange_points) ? number_format($dataSub->exchange_points) : 0),'text',array('onkeyup'=>'formatNumBerKeyUp(this)')); ?>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-12">
                                                <?php echo render_textarea('note_type_sales','note', isset($dataMain) && isset($dataSub) && $dataMain->type == 'sales' && !empty($dataSub->note) ? $dataSub->note : ''); ?>
                                            </div>
                                        </div>
                                        <!-- end -->
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="customer_info">
                                        <table class="tnh-tb table-bordered table-hover m-group0 js-table-customer" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th class="center" style="width: 10%;"><?=_l('cong_stt')?></th>
                                                    <th class="center" style="width: 80%;"><?=_l('cong_name_client')?></th>
                                                    <th class="center" style="width: 10%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="trMain">
                                                    <td class="center"></td>
                                                    <td class="center td-custom_item_select">
                                                        <input data-placeholder="<?=_l('ch_list_objects')?>" class="custom_item_select_customer" id="custom_item_select_customer" style="width: 100%">
                                                    </td>
                                                    <td class="center">
                                                        <a class="btn btn-info createTrItem_customer"><i class="fa fa-check"></i></a>
                                                    </td>
                                                </tr>
                                                <?php foreach ($dataCustomer as $key => $value) { ?>
                                                    <tr class="tr-parent">
                                                        <td class="center stt"><?=++$key?></td>
                                                        <td>
                                                            <input class="id_item" type="hidden" name="customer[<?=$i?>][id_item]" value="<?=$value['customer_id']?>"><?=$value['company']?>
                                                        </td>
                                                        <td class="center">
                                                            <a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a>
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
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info pull-right">
                <?php echo _l( 'submit'); ?>
            </button>
            <a href="<?=admin_url('promotion')?>" class="btn btn-default pull-right mright5"><?=_l('go_back')?></a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div id="modal_add_gift" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?=_l('promotion_item_gift')?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="tnh-tb table-bordered table-hover m-group0">
                        <thead>
                            <tr>
                                <th class="center">
                                    <?=_l('cong_stt')?>
                                    <input type="hidden" class="js-id_item">
                                </th>
                                <th class="center"><?=_l('promotion_item_gift')?></th>
                                <th class="center"><?=_l('promotion_number_gift')?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="gift_tbody">
                            <tr class="trMain">
                                <td class="center"></td>
                                <td class="center td-custom_item_select_gift">
                                    <input data-placeholder="<?=_l('ch_list_objects')?>" class="custom_item_select_gift" id="custom_item_select_gift" style="width: 100%">
                                </td>
                                <td class="center">
                                    <?php echo render_input('number_request_gift','',0,'text',array('onkeyup'=>'formatNumBerKeyUp(this)','style'=>'text-align: right;'),array(),'','mainQuantity'); ?>
                                </td>
                                <td class="center">
                                    <a class="btn btn-info createTrItem_gift"><i class="fa fa-check"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info submit_gift"><?php echo _l( 'submit'); ?></button>
            <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
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
    function resetMain(current) {
        current.parents('tr.trMain').find('.mainQuantity').val(0);
        current.parents('tr.trMain').find('#custom_item_select').select2('val','');
        current.parents('tr.trMain').find('#custom_item_select_gift').select2('val','');
        current.parents('tr.trMain').find('#custom_item_select_sales').select2('val','');
        current.parents('tr.trMain').find('#custom_item_select_discount').select2('val','');
        current.parents('tr.trMain').find('#custom_item_select_points').select2('val','');
    }
    function resetHTML_modal() {
        var tr_main = $('#modal_add_gift').find('.gift_tbody').find('tr:gt(0)');
        $.each(tr_main, function(i, v){
            $(this).remove();
        });
    }
    function resetSTT(current) {
        var number = $('.js-table-promotion').find('.tr-parent');
        var stt = 1;
        $.each(number, function(i, v){
            $(this).find('.stt').text(stt);
            stt++;
        });
    }
    function resetSTT_sales() {
        var number = $('.js-table-promotion-sales').find('.tr-parent');
        var stt = 1;
        $.each(number, function(i, v){
            $(this).find('.stt').text(stt);
            stt++;
        });
    }
    function resetSTT_discount() {
        var number = $('.js-table-promotion-discount').find('.tr-parent');
        var stt = 1;
        $.each(number, function(i, v){
            $(this).find('.stt').text(stt);
            stt++;
        });
    }
    function resetSTT_points() {
        var number = $('.js-table-promotion-points').find('.tr-parent');
        var stt = 1;
        $.each(number, function(i, v){
            $(this).find('.stt').text(stt);
            stt++;
        });
    }
    function resetSTT_customer() {
        var number = $('.js-table-customer').find('.tr-parent');
        var stt = 1;
        $.each(number, function(i, v){
            $(this).find('.stt').text(stt);
            stt++;
        });
    }
    $(function(){
        active_daterangepicker();
        $('#type').trigger('change');
        $('#method_of_application').trigger('change');
        $('#area_of_application').trigger('change');
        ajaxSelectCallBack($('.custom_item_select'), "<?=admin_url('promotion/SearchItems')?>", 0);
        ajaxSelectCallBack($('.custom_item_select_gift'), "<?=admin_url('promotion/SearchItems')?>", 0);
        ajaxSelectCallBack($('.custom_item_select_sales'), "<?=admin_url('promotion/SearchItems')?>", 0);
        ajaxSelectCallBack($('.custom_item_select_discount'), "<?=admin_url('promotion/SearchItems')?>", 0);
        ajaxSelectCallBack($('.custom_item_select_points'), "<?=admin_url('promotion/SearchItems')?>", 0);

        ajaxSelectCallBack_customer($('.custom_item_select_customer'), "<?=admin_url('promotion/SearchCustomer')?>", 0);
        appValidateForm($('#promotion-form'), {name: 'required', type: 'required', method_of_application: 'required', date_active: 'required'});
    });
    function ajaxSelectCallBack(element, url, id, types = '')
    {
        if (id > 0)
        {
            $(element).val(id).select2({
                width: 'resolve',
                allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: url + '/' + id,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type:-1,
                            types: types,
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
                },
                    formatResult: repoFormatSelection,
                    formatSelection: repoFormatSelection,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function (m) { return m; }
            });
        } else {
            $(element).select2({
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type:-1,
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
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; }
            });
        }
    }

    function ajaxSelectCallBack_categories(element, url, id, types = '')
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
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; }
        });
    }
    function ajaxSelectCallBack_customer(element, url, id, types = '')
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
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; }
        });
    }
    var active_daterangepicker = (startDate, endDate) => {
        //thời gian áp dụng
        $('input[name="date_active"]').daterangepicker({
            opens: 'right',
            isInvalidDate: false,
            startDate: startDate,
            endDate: endDate,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {
        });
        //thời gian tính doanh số
        $('input[name="time_sales"]').daterangepicker({
            opens: 'right',
            isInvalidDate: false,
            startDate: startDate,
            endDate: endDate,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {
        });
        //thời gian tính doanh số sales
        $('input[name="date_active_sales"]').daterangepicker({
            opens: 'right',
            isInvalidDate: false,
            startDate: startDate,
            endDate: endDate,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {
        });
    };
    var inner_popover_change_group_addon = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click','.change-group-addon', function (e) {
        $(this).popover({
            html: true,
            placement: "left",
            trigger: 'click',
            title:'',
            content: function() {
                return $(this).find('.content-menu').html();
            },
            template: inner_popover_change_group_addon
        });
    });
    $(document).on('click','.val-group-addon', function (e) {
        $(this).parents('.form-group').find('.text-group-addon').text($(this).text());
        $(this).parents('.form-group').find('.type_discount').val($(this).text()); //loại % or VNĐ
        $(this).parents('.form-group').find('.type_limit_points').val($(this).text()); //loại điểm or tiền
        $('.change-group-addon').popover('hide');
        $(this).parents('.form-group').find('.limit_discount').val(0);
        $(this).parents('.form-group').find('.limit_points').val(0);
        // checkType_points($(this));
    });
    $(document).on('keyup','.limit_discount', function (e) {
        if($(this).parent().find('.text-group-addon').text() == '%') {
            if(unformatNumber($(this).val()) > 100) {
                $(this).val(100);
            }
        }
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
            $('.div-area_of_application').find('select[name="area_of_application"]').removeAttr('required');
            $('.customer_info').removeClass("no-drop-v2");
            $('.customer_info').find('.event_table').removeClass("none-event");
        }
        else {
            $('.div-area_of_application').removeClass('hide');
            $('.div-area_of_application').find('select[name="area_of_application"]').attr('required','required');
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
    var unique = <?=$i?>;
    $('.createTrItem').click(function(e){
        var trMain = $(this).parents('tr.trMain');
        if(trMain.find('#custom_item_select').val().slice(0,10) == 'categories') {
            if(!trMain.find('#custom_item_select').val() || !trMain.find('#number_request').val()) {
                alert_float('danger', '<?=_l('pls_input')?>');
            }
            else if(trMain.parents('tbody').find('tr:gt(0) input[value='+trMain.find('#custom_item_select').val()+']').length) {
                alert_float('danger', '<?=_l('item_exists')?>');
            }
            else {
                var newTr = $('<tr class="tr-parent"></tr>');
                var id_item = trMain.find('#custom_item_select').val();
                var stt = Number(trMain.parents('tbody').find('tr.tr-parent').length) + 1;
                var td1 = $('<td class="center stt">'+stt+'</td>');
                var name_item = '<span class="inline-block label label-warning">'+trMain.find('#custom_item_select').select2('data').type_item+'</span> '+trMain.find('#custom_item_select').select2('data').name_item;
                var td2 = $('<td><input class="id_item" type="hidden" name="item['+unique+'][id_item]" value="'+id_item+'">'+name_item+'<br><a class="btn btn-info mtop5 add_gift" data-id="'+id_item+'"><?=_l('promotion_item_gift')?></a></td>');
                var quantity = trMain.find('#number_request').val();
                var td3 = $('<td class="center"><div class="form-group"><input type="text" name="item['+unique+'][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="'+quantity+'"></div></td>');

                newTr.append(td1);
                newTr.append(td2);
                newTr.append(td3);
                newTr.append('<td class="center"><a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a></td>');
                trMain.parents('tbody').append(newTr);
                unique++;
                resetMain($(this));
            }
        }
        else {
            if(!trMain.find('#custom_item_select').val() || !trMain.find('#number_request').val()) {
                alert_float('danger', '<?=_l('pls_input')?>');
            }
            else if(trMain.parents('tbody').find('tr:gt(0) input[value='+trMain.find('#custom_item_select').val()+']').length) {
                alert_float('danger', '<?=_l('item_exists')?>');
            }
            else {
                var newTr = $('<tr class="tr-parent"></tr>');
                var id_item = trMain.find('#custom_item_select').val();
                var stt = Number(trMain.parents('tbody').find('tr.tr-parent').length) + 1;
                var td1 = $('<td class="center stt">'+stt+'</td>');
                var img_item = trMain.find('.td-custom_item_select').find('.select2-chosen').find('img').attr('src');
                var name_item = trMain.find('.td-custom_item_select').find('.select2-chosen').text();
                var td2 = $('<td><input class="id_item" type="hidden" name="item['+unique+'][id_item]" value="'+id_item+'"><img class="img_option" src="'+img_item+'">'+name_item+'<br><a class="btn btn-info mtop5 add_gift" data-id="'+id_item+'"><?=_l('promotion_item_gift')?></a></td>');
                var quantity = trMain.find('#number_request').val();
                var td3 = $('<td class="center"><div class="form-group"><input type="text" name="item['+unique+'][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="'+quantity+'"></div></td>');

                newTr.append(td1);
                newTr.append(td2);
                newTr.append(td3);
                newTr.append('<td class="center"><a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a></td>');
                trMain.parents('tbody').append(newTr);
                unique++;
                resetMain($(this));
            }
        }
    });

    $('.createTrItem_sales').click(function(e){
        var trMain = $(this).parents('tr.trMain');
        if(!trMain.find('#custom_item_select_sales').val() || !trMain.find('#number_request_sales').val()) {
            alert_float('danger', '<?=_l('pls_input')?>');
        }
        else if(trMain.parents('tbody').find('tr:gt(0) input[value='+trMain.find('#custom_item_select_sales').val()+']').length) {
            alert_float('danger', '<?=_l('item_exists')?>');
        }
        else {
            var newTr = $('<tr class="tr-parent"></tr>');
            var id_item = trMain.find('#custom_item_select_sales').val();
            var stt = Number(trMain.parents('tbody').find('tr.tr-parent').length) + 1;
            var td1 = $('<td class="center stt">'+stt+'</td>');
            var img_item = trMain.find('.td-custom_item_select').find('.select2-chosen').find('img').attr('src');
            var name_item = trMain.find('.td-custom_item_select').find('.select2-chosen').text();
            var td2 = $('<td><input class="id_item" type="hidden" name="item['+unique+'][id_item]" value="'+id_item+'"><img class="img_option" src="'+img_item+'">'+name_item+'</td>');
            var quantity = trMain.find('#number_request_sales').val();
            var td3 = $('<td class="center"><div class="form-group"><input type="text" name="item['+unique+'][quantity]" class="form-control" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="'+quantity+'"></div></td>');

            newTr.append(td1);
            newTr.append(td2);
            newTr.append(td3);
            newTr.append('<td class="center"><a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a></td>');
            trMain.parents('tbody').append(newTr);
            unique++;
            resetMain($(this));
        }
    });
    
    $(document).on('click','.deleteTrItem_main', function (e) {
        var id_item = $(this).parents('tr').find('.id_item').val();
        var current_child = $(this).parents('tbody').find('tr:gt(0) .items_id[value='+id_item+']').parents('tr');
        $(this).parents('tr').remove();
        $.each(current_child, function(i, v){
            $(this).remove();
        });
        resetSTT();
        resetSTT_sales();
        resetSTT_discount();
        resetSTT_points();
        resetSTT_customer();
    });

    //modal
    $(document).on('click', '.add_gift', (e)=>{
        var current = $(e.currentTarget);
        var id = current.attr('data-id');
        $('.js-id_item').val(id);
        resetHTML_modal();
        $('#modal_add_gift').modal({backdrop: 'static', keyboard: false});
    });
    $('.createTrItem_gift').click(function(e){
        var trMain = $(this).parents('tr.trMain');
        var idMAIN = $('.js-id_item').val();
        var list_item = $('table.js-table-promotion').find('tbody tr:gt(0) .items_id[value='+idMAIN+']').parents('td');
        var arrExists = [];
        $.each(list_item, function(i, v){
            arrExists.push($(this).find('.itemsGift_id').val());
        });

        if(!trMain.find('#custom_item_select_gift').val() || !trMain.find('#number_request_gift').val()) {
            alert_float('danger', '<?=_l('pls_input')?>');
        }
        else if(trMain.parents('tbody').find('tr:gt(0) input[value='+trMain.find('#custom_item_select_gift').val()+']').length || arrExists.indexOf(trMain.find('#custom_item_select_gift').val()) != -1) {
            alert_float('danger', '<?=_l('item_exists')?>');
        }
        else {
            var newTr = $('<tr></tr>');
            var id_item = trMain.find('#custom_item_select_gift').val();
            var stt = Number(trMain.parents('tbody').find('tr:gt(0)').length) + 1;
            var td1 = $('<td class="center">'+stt+'<input class="id_gift" type="hidden" value="'+id_item+'"></td>');
            var img_item = trMain.find('.td-custom_item_select_gift').find('.select2-chosen').find('img').attr('src');
            var name_item = trMain.find('.td-custom_item_select_gift').find('.select2-chosen').text();
            var td2 = $('<td class="name_gift"><img class="img_option" src="'+img_item+'">'+name_item+'</td>');
            var quantity = trMain.find('#number_request_gift').val();
            var td3 = $('<td class="center"><div class="form-group"><input type="text" class="form-control quantity_gift" onkeyup="formatNumBerKeyUp(this)" style="text-align: right;" value="'+quantity+'"></div></td>');

            newTr.append(td1);
            newTr.append(td2);
            newTr.append(td3);
            newTr.append('<td class="center"><a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a></td>');
            trMain.parents('tbody').append(newTr);
            resetMain($(this));
        }
    });
    $('.submit_gift').click(function(e){
        var current = $(e.currentTarget);
        var tr_main = current.parents('#modal_add_gift').find('.gift_tbody').find('tr:gt(0)');
        var dataID = current.parents('#modal_add_gift').find('.js-id_item').val();

        var table = $('table.js-table-promotion');
        var tr = table.find('tbody tr:gt(0) .id_item[value='+dataID+']').parents('tr');
        var dataHTML = '';
        $.each(tr_main, function(i, v){
            dataHTML += '<tr>';
            dataHTML += '<td><input class="itemsGift_id" type="hidden" name="items_gift[' + unique + '][id]" value="'+ $(this).find('.id_gift').val() +'" /><input class="items_id" type="hidden" name="items_gift[' + unique + '][items_id]" value="'+ dataID +'" /></td>';
            dataHTML += '<td class="padding20"><span class="inline-block label label-warning"><?=_l('item_gift')?></span> '+'<img class="img_option" src="'+$(this).find('.img_option').attr('src')+'">'+ $(this).find('.name_gift').text() +'</td>';
            dataHTML += '<td class="center"><div class="form-group"><input style="width: 100%; text-align: right;" type="text" class="form-control" onkeyup="formatNumBerKeyUp(this)" name="items_gift[' + unique + '][quantity]" value="'+ $(this).find('.quantity_gift').val() +'" /></div></td>';
            dataHTML += '<td class="center"><a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a></td>';
            dataHTML += '</tr>';
            unique++;
        });
        $(dataHTML).insertAfter(tr);
        $('#modal_add_gift').modal('hide');
    });
    $(document).on('click','.deleteTrItem', function (e) {
        $(this).parents('tr').remove();
    });
    //end

    $('.createTrItem_discount').click(function(e){
        var trMain = $(this).parents('tr.trMain');
        var newTr = $('<tr></tr>');
        var stt = Number(trMain.parents('tbody').find('tr:gt(0)').length) + 1;
        var td1 = $('<td class="center stt">'+stt+'</td>');
        var td2 = $('<td class="center"><div class="form-group"><input type="text" class="form-control limit_sales" name="item_discount['+unique+'][limit_sales]" style="width: 100%; text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="'+formatNumber(trMain.find('input.limit_sales').val())+'"></div></td>');
        var td3 = $('<td class="center">\
            <div class="form-group" app-field-wrapper="limit_discount">\
                <div class="input-group" style="width: 100%;">\
                    <input type="text" class="form-control limit_discount" name="item_discount['+unique+'][limit_discount]" style="text-align: right;" onkeyup="formatNumBerKeyUp(this)" value="'+formatNumber(trMain.find('input.limit_discount').val())+'">\
                    <input type="hidden" name="item_discount['+unique+'][type_discount]" class="type_discount" value="'+trMain.find('.text-group-addon').text()+'">\
                    <div class="input-group-addon pointer change-group-addon">\
                        <span><i class="fa fa-cog"></i></span>\
                        <span class="text-group-addon">'+trMain.find('.text-group-addon').text()+'</span>\
                        <div class="content-menu hide">\
                            <div class="pointer css-group-addon val-group-addon">%</div>\
                            <div class="pointer css-group-addon val-group-addon">VNĐ</div>\
                        </div>\
                    </div>\
                </div>\
            </div>\
        </td>');

        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append('<td class="center"><a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a></td>');
        trMain.parents('tbody').append(newTr);
        unique++;
        trMain.find('input.limit_sales').val(0);
        trMain.find('input.limit_discount').val(0);
    });

    $('.createTrItem_customer').click(function(e){
        var trMain = $(this).parents('tr.trMain');
        if(!trMain.find('#custom_item_select_customer').val()) {
            alert_float('danger', '<?=_l('pls_input')?>');
        }
        else if(trMain.parents('tbody').find('tr:gt(0) input[value='+trMain.find('#custom_item_select_customer').val()+']').length) {
            alert_float('danger', '<?=_l('customer_exists')?>');
        }
        else {
            var newTr = $('<tr class="tr-parent"></tr>');
            var id_item = trMain.find('#custom_item_select_customer').val();
            var stt = Number(trMain.parents('tbody').find('tr.tr-parent').length) + 1;
            var td1 = $('<td class="center stt">'+stt+'</td>');
            var name_item = trMain.find('.td-custom_item_select').find('.select2-chosen').text();
            var td2 = $('<td><input class="id_item" type="hidden" name="customer['+unique+'][id_item]" value="'+id_item+'">'+name_item+'</td>');

            newTr.append(td1);
            newTr.append(td2);
            newTr.append('<td class="center"><a class="btn btn-danger deleteTrItem_main"><i class="fa fa-times"></i></a></td>');
            trMain.parents('tbody').append(newTr);
            unique++;
        }
    });

    function checkType_points(current) {
        if(current.parents('.promotion_by_sales').length > 0) {
            var text = current.parents('.form-group').find('.text-group-addon').text();
            if(text == '<?=_l('money')?>') {
                $('.div-type-sales').removeClass('hide');
                if($('.table-type-sales').hasClass('check-hide')) {
                    $('.table-type-sales').removeClass('hide');
                    $('.table-type-sales').removeClass('check-hide');
                }
            }
            else {
                $('.div-type-sales').addClass('hide');
                if(!$('.table-type-sales').hasClass('hide')) {
                    $('.table-type-sales').addClass('hide');
                    $('.table-type-sales').addClass('check-hide');
                }
            }
        }
    }
    function show_calendar(trItem) {
        $(trItem).parent().find('input').click();
    }

    $('input[name=type_select_discount]').click(function(e){
        //theo mặt hàng
        if($(this).val() == 1) {
            ajaxSelectCallBack($('.custom_item_select_discount'), "<?=admin_url('promotion/SearchItems')?>", 0);
        }
        //theo danh mục
        else if($(this).val() == 2) {
            ajaxSelectCallBack_categories($('.custom_item_select_discount'), "<?=admin_url('promotion/SearchCategories')?>", 0);
        }
    });
    $('input[name=type_select_item]').click(function(e){
        //theo mặt hàng
        if($(this).val() == 1) {
            ajaxSelectCallBack($('.custom_item_select'), "<?=admin_url('promotion/SearchItems')?>", 0);
        }
        //theo danh mục
        else if($(this).val() == 2) {
            ajaxSelectCallBack_categories($('.custom_item_select'), "<?=admin_url('promotion/SearchCategories')?>", 0);
        }
    });
    $('input[name=type_select_sales]').click(function(e){
        //theo mặt hàng
        if($(this).val() == 1) {
            ajaxSelectCallBack($('.custom_item_select_points'), "<?=admin_url('promotion/SearchItems')?>", 0);
        }
        //theo danh mục
        else if($(this).val() == 2) {
            ajaxSelectCallBack_categories($('.custom_item_select_points'), "<?=admin_url('promotion/SearchCategories')?>", 0);
        }
    });
</script>
</body>
</html>
