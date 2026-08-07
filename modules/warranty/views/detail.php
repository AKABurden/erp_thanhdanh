<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tab-pane{
        display: none;
    }
    .tab-pane.active{
        display: block;
    }
    .show_detail {
        cursor: pointer;
        background: #22f13c;
        padding: 2px 5px;
        border-radius: 10px;
        color: #fff;
        font-weight: bold;
    }
    .hide_detail {
        cursor: pointer;
        background: #ff3939;
        padding: 2px 7px;
        border-radius: 10px;
        color: #fff;
        font-weight: bold;
    }
    .TrSub {
        background: #e4f5ff;
    }
    .img-issue img {
        width: 100px;
        height: 80px;
    }
    .img-issue {
        position: relative;
        float: left;
        margin-top: 10px;
        margin-left: 10px;
    }
    .img-delete {
        cursor: pointer;
        position: absolute;
        width: 20px;
        top: 0;
        right: 0px;
        background: #fff;
    }
    .dont_remove {
        cursor: no-drop;
        opacity: 0.2;
    }
    .dont-change-select {
        cursor: no-drop;
    }
    .dont-change-select div.id_item {
        pointer-events: none;
    }
    .dont-change-select a.select2-choice {
        pointer-events: none;
        background: #f3eeee;
    }
    .wrap-line-sp {
        height: 1px;
        width: 100%;
        background: #c5c5c5;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <?php echo form_open('admin/warranty/add_detail/'.$id.'/'.$id_edit,array('id'=>'warranty-detail', 'enctype'=>'multipart/form-data')); ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12 mbot50">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel panel-primary">
                            <?php 
                                $type = '';
                                $status = '';
                                if (!$dataMain) {
                                    $type = '';
                                }
                                else if ($dataMain->status == 0) {
                                    $type = 'danger';
                                    $status = _l('dont_approve');
                                }
                                else if ($dataMain->status == 1) {
                                    $type = 'success';
                                    $status = _l('tnh_approved');
                                }
                            ?>
                            <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                <span><?= $status ?></span>
                            </div>
                            <div class="panel-heading"><?=_l('info')?></div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= _l('code_warranty') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= !empty($dataMain->code) ? $dataMain->code : '' ?></span>
                                    </div>
                                    <div class="wap-content second">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= _l('date') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= !empty($dataMain->date) ? $dataMain->date : '' ?></span>
                                    </div>
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= _l('clients') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= !empty($client->company) ? $client->company : '' ?></span>
                                    </div>
                                    <div class="wap-content second">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= _l('name_of_machine') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= !empty($dataMain->name_of_machine) ? $dataMain->name_of_machine : '' ?></span>
                                    </div>
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= _l('service_type') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= !empty($dataMain->service_type) ? $dataMain->service_type : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                        echo render_input('code','Mã phiếu bảo hành',(!empty($dataWarranty->code) ? $dataWarranty->code : $code), 'text', array('readonly'=>'readonly'));
                                    ?>
                                    <?php
                                        echo render_select('employees_id[]',$staff,array('staffid','fullname'),'tnh_employees_charge',(!empty($dataWarranty->employees_id) ? explode(',', $dataWarranty->employees_id) : ''),array('multiple'=>true, 'data-actions-box'=>true),array(),'','',false);
                                    ?>
                                    <?php $arrToString = array(
                                        array(
                                            'id'=>'1',
                                            'name'=>'Công ty'
                                        ),
                                        array(
                                            'id'=>'2',
                                            'name'=>'Khách hàng'
                                        ),
                                    );?>
                                    <?php 
                                        echo render_select('localtion_warranty',$arrToString,array('id','name'),'localtion_warranty',!empty($dataWarranty->localtion_warranty) ? $dataWarranty->localtion_warranty : 1, array(), array(), '', '', false);
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <!-- tab content -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#issue_AND_solution" aria-controls="issue_AND_solution" role="tab" data-toggle="tab"><?=_l('issue_AND_solution')?></a>
                            </li>
                            <li role="presentation">
                                <a href="#expenses" aria-controls="expenses" role="tab" data-toggle="tab"><?=_l('client_expenses_tab')?></a>
                            </li>
                            <li role="presentation">
                                <a href="#supplies" aria-controls="supplies" role="tab" data-toggle="tab"><?=_l('supplies')?></a>
                            </li>
                        </ul>
                        <div role="tabpanel" class="tab-pane active" id="issue_AND_solution" style="margin-bottom: 200px;">
                            <table class="tnh-tb table-issue table-bordered table-hover m-group0" style="table-layout: fixed;">
                                <thead>
                                    <tr style="background: #337ab7; color: #fff;">
                                        <th style="width: 5%;" class="text-center">STT</th>
                                        <th style="width: 10%;" class="text-center"><?=_l('image')?></th>
                                        <th style="width: 15%;" class="text-center"><?=_l('tnh_product_code')?></th>
                                        <th style="width: 20%;" class="text-center"><?=_l('tnh_product_name')?></th>
                                        <th style="width: 15%;" class="text-center"><?=_l('series')?></th>
                                        <th style="width: 10%;" class="text-center"><?=_l('Vị trí kho')?></th>
                                        <th style="width: 15%;" class="text-center"><?=_l('warranty_time')?></th>
                                        <th style="width: 10%;" class="text-center"><?=_l('warranty_end_time')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $unique = 0; ?>
                                    <?php foreach ($dataITem as $key => $value) { ?>
                                        <tr class="TrMain" data-idseries="<?=$value['id_series']?>" data-warranty-item="<?=$value['id_warranty_item']?>" style="background: #fff;">
                                            <td class="text-center">
                                                <span class="show_detail hide">+</span>
                                                <span class="hide_detail">-</span>
                                            </td>
                                            <td class="text-center">
                                                <img width="50" src="<?= $value['img_item'] ?>">
                                            </td>
                                            <td class="text-left">
                                                <?=$value['code_item']?>
                                            </td>
                                            <td class="text-left">
                                                <?=$value['name_item']?>
                                            </td>
                                            <td class="text-center">
                                                <?=$value['series']?>
                                                <input type="hidden" name="series[<?= $key ?>][id_series]" value="<?=$value['id_series']?>">
                                            </td>
                                            <td class="text-center check-select">
                                                <select style="width: 100%;" data-id="<?= !empty($value['localtion_warehouse']) ? $value['localtion_warehouse'] : '' ?>" class="localtion_warehouse" id="localtion_warehouse_<?= $key ?>" name="series[<?= $key ?>][localtion_warehouse]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <?=$value['month_warranty']?>
                                            </td>
                                            <td class="text-center">
                                                <?=$value['deadline_warranty']?>
                                            </td>
                                        </tr>
                                        <tr class="TrSub" data-idseries="<?=$value['id_series']?>" data-warranty-item="<?=$value['id_warranty_item']?>">
                                            <td class="text-center">
                                                <span class="btn btn-success addIssue"><i class="fa fa-plus"></i></span>
                                            </td>
                                            <td colspan="2" class="text-center">Vấn đề</td>
                                            <td class="text-center">Giải pháp</td>
                                            <td colspan="4" class="text-center">Hình ảnh</td>
                                        </tr>

                                        <?php $getIssue = get_table_where('tblwarranty_issue',array('id_warranty_item'=>$value['id_warranty_item'])); ?>
                                        <?php foreach ($getIssue as $keyIssue => $valueIssue) { ?>
                                            <tr class="TrIssue" data-idseries="<?=$value['id_series']?>" data-unique="<?=$unique?>" data-issue="<?=$valueIssue['id_issue']?>">
                                                <td class="text-center stt"><?=++$keyIssue?></td>
                                                <td colspan="2" class="text-center">
                                                    <div class="js-width">
                                                        <div class="input-group">
                                                            <div class="form-group">
                                                                <input type="text" id="issue_<?=$unique?>" name="item[<?=$unique?>][issue]" class="issue" style="width: 100%;">
                                                            </div>
                                                            <span class="input-group-addon add_item_issue pointer">
                                                                <i class="fa fa-plus"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <textarea name="item[<?=$unique?>][solution]" class="form-control solution" rows="4"><?=$valueIssue['solution']?></textarea>
                                                    <input type="hidden" name="item[<?=$unique?>][id_warranty_item]" value="<?=$value['id_warranty_item']?>">
                                                    <input type="hidden" name="item[<?=$unique?>][id]" value="<?=$valueIssue['id']?>">
                                                </td>
                                                <td colspan="4" class="text-center">
                                                    <input type="file" name="file[<?=$unique?>][]" id="images_multiple" multiple class="form-control">
                                                    <div class="data-file">
                                                        <?php $get_file = get_table_where('tblwarranty_file',array('id_warranty_issue'=>$valueIssue['id'])); ?>
                                                        <?php foreach ($get_file as $key_file => $value_file) { ?>
                                                            <div class="img-issue">
                                                                <img src="<?= base_url('modules/warranty/uploads/warranty/'.$valueIssue['id'].'/'.$value_file['name']); ?>">
                                                                <div class="img-delete"><i class="fa fa-times text-danger"></i></div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $unique++; ?>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="expenses" style="margin-bottom: 200px;">
                            <table class="tnh-tb table-expenses table-bordered table-hover m-group0" style="table-layout: fixed;">
                                <thead>
                                    <tr style="background: #337ab7; color: #fff;">
                                        <th style="width: 5%;" class="text-center">
                                            <span class="btn btn-success add_expenses"><i class="fa fa-plus"></i></span>
                                        </th>
                                        <th style="width: 50%;" class="text-center"><?=_l('name_expenses')?></th>
                                        <th style="width: 20%;" class="text-center"><?=_l('ch_costs')?></th>
                                        <th style="width: 20%;" class="text-center"><?=_l('amount_expenses')?></th>
                                        <th style="width: 5%;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!$expenses) { ?>
                                        <tr>
                                            <td class="text-center stt">
                                                1
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group">
                                                    <div class="form-group">
                                                        <select name="expenses[<?=$unique?>][name]" class="selectpicker expenses_select" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                            <option value=""></option>
                                                            <?php foreach ($costs as $key => $value) { ?>
                                                                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="input-group-addon pointer" onclick="new_costs(); return false;">
                                                        <i class="fa fa-plus"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php $arrType = array(
                                                    array(
                                                        'id'=>1,
                                                        'name'=>'Khách chịu'
                                                    ),
                                                    array(
                                                        'id'=>2,
                                                        'name'=>'Công ty chịu'
                                                    )
                                                ); ?>
                                                <select name="expenses[<?=$unique?>][type]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                    <?php foreach ($arrType as $keyType => $valueType) { ?>
                                                        <option value="<?=$valueType['id']?>" <?= $keyType==0 ? 'selected' : '' ?>><?=$valueType['name']?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="text" name="expenses[<?=$unique?>][amount]" class="form-control expenses_amount" onkeyup="formatNumBerKeyUp(this)" value="0">
                                            </td>
                                            <td class="text-center">
                                                <span class="btn btn-danger remove_expenses"><i class="fa fa-times"></i></span>
                                            </td>
                                        </tr>
                                        <?php $unique++; ?>
                                    <?php } else { ?>
                                        <?php foreach ($expenses as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center stt">
                                                    <?=++$key?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="input-group">
                                                        <div class="form-group">
                                                            <select name="expenses[<?=$unique?>][name]" class="selectpicker expenses_select" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                                <option value=""></option>
                                                                <?php foreach ($costs as $key_costs => $value_costs) { ?>
                                                                    <option value="<?=$value_costs['id']?>" <?=($value_costs['id'] == $value['name'] ? 'selected' : '')?>><?=$value_costs['name']?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <span class="input-group-addon pointer" onclick="new_costs(); return false;">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php $arrType = array(
                                                        array(
                                                            'id'=>1,
                                                            'name'=>'Khách chịu'
                                                        ),
                                                        array(
                                                            'id'=>2,
                                                            'name'=>'Công ty chịu'
                                                        )
                                                    ); ?>
                                                    <select name="expenses[<?=$unique?>][type]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                        <?php foreach ($arrType as $keyType => $valueType) { ?>
                                                            <option value="<?=$valueType['id']?>" <?=($valueType['id'] == $value['type'] ? 'selected' : '')?>><?=$valueType['name']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <input type="text" name="expenses[<?=$unique?>][amount]" class="form-control expenses_amount" onkeyup="formatNumBerKeyUp(this)" value="<?=number_format($value['amount'])?>">
                                                </td>
                                                <td class="text-center">
                                                    <span class="btn btn-danger remove_expenses"><i class="fa fa-times"></i></span>
                                                </td>
                                            </tr>
                                            <?php $unique++; ?>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center">
                                            <span class="btn btn-success add_expenses"><i class="fa fa-plus"></i></span>
                                        </td>
                                        <td class="text-left bold"><?=_l('tnh_grand_total')?></td>
                                        <td class="text-right"></td>
                                        <td class="text-right bold grand_total">0</td>
                                        <td class="text-right"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="supplies" style="margin-bottom: 200px;">
                            <div class="text-danger">
                                <?php if($supplies) { ?>
                                    * Không thể xóa các vật tư đã xuất kho.
                                <?php } ?>
                            </div>
                            <table class="tnh-tb table-supplies table-bordered table-hover m-group0" style="table-layout: fixed;">
                                <thead>
                                    <tr style="background: #337ab7; color: #fff;">
                                        <th style="width: 5%;" class="text-center">
                                            <span class="btn btn-success add_supplies"><i class="fa fa-plus"></i></span>
                                        </th>
                                        <th style="width: 15%;" class="text-center"><?=_l('code_supplies')?></th>
                                        <th style="width: 5%;" class="text-center"><?=_l('image')?></th>
                                        <th style="width: 15%;" class="text-center"><?=_l('name_supplies')?></th>
                                        <th style="width: 10%;" class="text-center"><?=_l('item_quantity')?></th>
                                        <th style="width: 10%;" class="text-center"><?=_l('ch_costs')?></th>
                                        <th style="width: 10%;" class="text-center"><?=_l('ch_price')?></th>
                                        <th style="width: 10%;" class="text-center"><?=_l('tnh_subtotal')?></th>
                                        <th style="width: 15%;" class="text-center"><?=_l('note')?></th>
                                        <th style="width: 5%;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!$supplies) { ?>
                                        <tr class="TrSupplies" data-unique="<?=$unique?>">
                                            <td class="text-center stt">1</td>
                                            <td class="text-center">
                                                <input type="text" name="supplies[<?=$unique?>][id_item]" class="id_item" style="width: 100%;">
                                            </td>
                                            <td class="text-left img_item"></td>
                                            <td class="text-left name_item"></td>
                                            <td class="text-center">
                                                <input type="number" name="supplies[<?=$unique?>][quantity]" class="form-control quantity" value="0">
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
                                                <td class="text-center <?= $value['export_warehouse'] > 0 ? 'dont-change-select' : '' ?>">
                                                    <input type="text" name="supplies[<?=$unique?>][id_item]" class="id_item" style="width: 100%;">
                                                    <?= $value['export_warehouse'] > 0 ? '<div class="text-danger">* Đã xuất kho (SL xuất: '.$value['export_warehouse'].')</div>' : '' ?>
                                                </td>
                                                <td class="text-left img_item">
                                                    <?=$img?>
                                                </td>
                                                <td class="text-left name_item">
                                                    <?=$name?>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" name="supplies[<?=$unique?>][quantity]" class="form-control quantity" value="<?=$value['quantity']?>" data-quantity="<?= $value['export_warehouse'] ?>">
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
                                                    <span class="btn btn-danger <?= $value['export_warehouse'] > 0 ? 'dont_remove' : 'remove_supplies' ?>"><i class="fa fa-times"></i></span>
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
                                        <td></td>
                                        <td class="text-center"></td>
                                        <td class="text-center bold quantity_total">0</td>
                                        <td class="text-center"></td>
                                        <td class="text-right bold amount_total">0</td>
                                        <td class="text-right bold grand_total">0</td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- end -->
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <a class="btn btn-info pull-right" onclick="checkSubmit(); return false;"><?=_l('submit')?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<div class="modal fade" id="warranty_issue_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('add_issue'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/warranty/add_issue',array('id'=>'warranty-issue')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name_issue','name_issue'); ?>
                    </div>
                    <div class="clearfix"></div>
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

<div class="modal fade" id="costs_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('financial_control/add'), array('id' => 'costs-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('ch_edit'); ?></span>
                    <span class="add-title"><?php echo _l('ch_add'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="additional"></div>
                    <div class="col-md-12">
                        <?php echo render_input('code_costs', 'ch_code_costs', '', '', array('autocomplete' => 'off')); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('name_costs', 'ch_name_costs', '', '', array('autocomplete' => 'off')); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('type') ?>
                            <select name="type_costs" id="type_costs" class="form-control selectpicker" data-none-selected-text="<?= lang('type') ?>" data-placeholder="<?= lang('type') ?>">
                                <option value="0"></option>
                                <option value="1"><?= lang('tnh_cpncsx') ?></option>
                                <option value="2"><?= lang('tnh_cpsxc') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_select('costs_parent', $costs, array('id', 'name'), 'ch_chose_parent'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<?php $this->load->view('count_js')?>
<script>
$( document ).ready(function() {
    var TrIssue = $('.TrIssue');
    $.each(TrIssue, function(i, v){
        ajaxSelectSeries('#issue_'+$(v).attr('data-unique'), 'admin/warranty/searchIssue', $(v).attr('data-issue'));
        rewidth($('#issue_'+$(v).attr('data-unique')));
    });

    var TrSupplies = $('.TrSupplies');
    $.each(TrSupplies, function(i, v){
        var typeamount = $(v).attr('data-typeamount');
        if(typeof typeamount == "undefined") {
            typeamount = 1;
        }
        ajaxSelectSeries('input[name="supplies['+$(v).attr('data-unique')+'][id_item]"]', 'admin/warranty/searchSupplies', $(v).attr('data-id'));
        ajaxSelectSeries('input[name="supplies['+$(v).attr('data-unique')+'][type_amount]"]', 'admin/warranty/searchType_amount', typeamount);
        $('input[name="supplies['+$(v).attr('data-unique')+'][type_amount]"]').trigger('change');
    });
    resetGrand_total_expenses();
    resetGrand_total_supplies();

    if($('select[name="localtion_warranty"]').val() == 1) {
        var TrLocaltion_warehouse = $('.localtion_warehouse');
        $.each(TrLocaltion_warehouse, function(i, v){
            $('#localtion_warehouse_'+i).select2();
            loadLocaltion_warehouses($(v));
        });
        $('select[name="localtion_warranty"]').trigger('change');
    }
    else {
        var TrLocaltion_warehouse = $('.localtion_warehouse');
        $.each(TrLocaltion_warehouse, function(i, v){
            $('#localtion_warehouse_'+i).select2();
        });
        $('select[name="localtion_warranty"]').trigger('change');
    }
});

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

$(document).on('click','.show_detail', function (e) {
    var target = $(e.currentTarget);
    target.parents('td').find('.hide_detail').removeClass('hide');
    target.parents('td').find('.show_detail').addClass('hide');
    $('.TrSub[data-idseries="'+target.parents('.TrMain').attr('data-idseries')+'"]').removeClass('hide');
    $('.TrIssue[data-idseries="'+target.parents('.TrMain').attr('data-idseries')+'"]').removeClass('hide');
});

$(document).on('click','.hide_detail', function (e) {
    var target = $(e.currentTarget);
    target.parents('td').find('.hide_detail').addClass('hide');
    target.parents('td').find('.show_detail').removeClass('hide');
    $('.TrSub[data-idseries="'+target.parents('.TrMain').attr('data-idseries')+'"]').addClass('hide');
    $('.TrIssue[data-idseries="'+target.parents('.TrMain').attr('data-idseries')+'"]').addClass('hide');
});

$(document).on('change','.issue', function (e) {
    var target = $(e.currentTarget);
    var data_idseries = target.parents('tr').attr('data-idseries');

    var allTr = $('.table-issue').find('.TrIssue[data-idseries="'+data_idseries+'"]');
    var checkAddTr = 0;
    $.each(allTr, function(i, v){
        if(!$(v).find('input.issue').val()) {
            checkAddTr++;
        }
    });
    if(checkAddTr == 0) {
        $('.TrSub[data-idseries="'+data_idseries+'"]').find('.addIssue').trigger('click');
    }
});

$(document).on('change','select[class="selectpicker expenses_select"]', function (e) {
    var target = $(e.currentTarget);

    var allTr = $('.table-expenses').find('tbody tr');
    var checkAddTr = 0;
    $.each(allTr, function(i, v){
        if(!$(v).find('select.expenses_select').val()) {
            checkAddTr++;
        }
    });
    if(checkAddTr == 0) {
        target.parents('table').find('thead').find('.add_expenses').trigger('click');
    }
});

var unique = <?=$unique?>;
$(document).on('click','.addIssue', function (e) {
    var target = $(e.currentTarget);

    var data_idseries = target.parents('tr').attr('data-idseries');
    var allTr = $('.table-issue').find('.TrIssue[data-idseries="'+data_idseries+'"]');
    var checkAddTr = 0;
    $.each(allTr, function(i, v){
        if(!$(v).find('input.issue').val()) {
            checkAddTr++;
        }
    });
    if(checkAddTr > 0) {
        return;
    }

    var tr = target.parents('.TrSub');
    var html = '<tr class="TrIssue" data-idseries="'+target.parents('.TrSub').attr('data-idseries')+'">\
                    <td class="text-center stt">1</td>\
                    <td colspan="2" class="text-center">\
                        <div class="js-width">\
                            <div class="input-group">\
                                <div class="form-group">\
                                    <input type="text" name="item['+unique+'][issue]" class="issue" style="width: 100%;">\
                                </div>\
                                <span class="input-group-addon add_item_issue pointer">\
                                    <i class="fa fa-plus"></i>\
                                </span>\
                            </div>\
                        </div>\
                    </td>\
                    <td class="text-center">\
                        <textarea name="item['+unique+'][solution]" class="form-control solution" rows="4"></textarea>\
                        <input type="hidden" name="item['+unique+'][id_warranty_item]" value="'+target.parents('.TrSub').attr('data-warranty-item')+'">\
                    </td>\
                    <td colspan="4" class="text-center">\
                        <input type="file" name="file['+unique+'][]" id="images_multiple" multiple="" class="form-control">\
                    </td>\
                </tr>';
    $(html).insertAfter(tr);
    resetIssue(target);
    ajaxSelectSeries('input[name="item['+unique+'][issue]"]', 'admin/warranty/searchIssue');
    rewidth($('input[name="item['+unique+'][issue]"]'));
    unique++;
});

$(document).on('click','.add_item_issue', function (e) {
    $('#warranty_issue_modal').modal({backdrop: 'static', keyboard: false});
});

appValidateForm($('#warranty-issue'), {name_issue: 'required'}, manage_warranty_issue);
function manage_warranty_issue(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float(response.alert_type, response.message);
        }
        $('#warranty_issue_modal').modal('hide');
    });
    return false;
}

$(document).on('click','.add_expenses', function (e) {
    var target = $(e.currentTarget);

    var allTr = $('.table-expenses').find('tbody tr');
    var checkAddTr = 0;
    $.each(allTr, function(i, v){
        if(!$(v).find('select.expenses_select').val()) {
            checkAddTr++;
        }
    });
    if(checkAddTr > 0) {
        return;
    }

    var html = '<tr>\
                    <td class="text-center stt">\
                    </td>\
                    <td class="text-center">\
                        <div class="input-group">\
                            <div class="form-group">\
                                <select name="expenses['+unique+'][name]" class="selectpicker expenses_select" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">\
                                    <option value=""></option>\
                                    <?php foreach ($costs as $key => $value) { ?>
                                        <option value="<?=$value['id']?>"><?=$value['name']?></option>\
                                    <?php } ?>
                                </select>\
                            </div>\
                            <span class="input-group-addon pointer" onclick="new_costs(); return false;">\
                                <i class="fa fa-plus"></i>\
                            </span>\
                        </div>\
                    </td>\
                    <td class="text-center">\
                        <?php $arrType = array(
                            array(
                                'id'=>1,
                                'name'=>'Khách chịu'
                            ),
                            array(
                                'id'=>2,
                                'name'=>'Công ty chịu'
                            )
                        ); ?>
                        <select name="expenses['+unique+'][type]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">\
                            <?php foreach ($arrType as $keyType => $valueType) { ?>
                                <option value="<?=$valueType['id']?>" <?= $keyType==0 ? 'selected' : '' ?>><?=$valueType['name']?></option>\
                            <?php } ?>
                        </select>\
                    </td>\
                    <td class="text-center">\
                        <input type="text" name="expenses['+unique+'][amount]" class="form-control expenses_amount" onkeyup="formatNumBerKeyUp(this)" value="0">\
                    </td>\
                    <td class="text-center">\
                        <span class="btn btn-danger remove_expenses"><i class="fa fa-times"></i></span>\
                    </td>\
                </tr>';
    $('.table-expenses').find('tbody').append(html);
    $('select[name="expenses['+unique+'][name]"]').selectpicker('refresh');
    $('select[name="expenses['+unique+'][type]"]').selectpicker('refresh');
    resetExpenses();
    unique++;
});

$(document).on('click','.remove_expenses', function (e) {
    var target = $(e.currentTarget);
    target.parents('tr').remove();
    resetExpenses();
    resetGrand_total_expenses();
});

$(document).on('click','.add_supplies', function (e) {
    var target = $(e.currentTarget);

    var allTr = $('.table-supplies').find('tbody tr');
    var checkAddTr = 0;
    $.each(allTr, function(i, v){
        if(!$(v).find('input.id_item').val()) {
            checkAddTr++;
        }
    });
    if(checkAddTr > 0) {
        return;
    }

    var html = '<tr class="TrSupplies" data-unique="'+unique+'">\
                    <td class="text-center stt"></td>\
                    <td class="text-center">\
                        <input type="text" name="supplies['+unique+'][id_item]" class="id_item" style="width: 100%;">\
                    </td>\
                    <td class="text-left img_item"></td>\
                    <td class="text-left name_item"></td>\
                    <td class="text-center">\
                        <input type="number" name="supplies['+unique+'][quantity]" class="form-control quantity" value="0">\
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
    $('.table-supplies').find('tbody').append(html);
    ajaxSelectSeries('input[name="supplies['+unique+'][id_item]"]', 'admin/warranty/searchSupplies');
    ajaxSelectSeries('input[name="supplies['+unique+'][type_amount]"]', 'admin/warranty/searchType_amount', 1);
    $('input[name="supplies['+unique+'][type_amount]"]').trigger('change');
    resetSupplies();
    unique++;
});

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

function resetGrand_total_expenses() {
    var countSTT = $('.table-expenses').find('tbody tr');
    var total = 0;
    $.each(countSTT, function(i, v){
        total += Number(unformatNumber($(v).find('.expenses_amount').val()));
    });
    $('.table-expenses').find('tfoot .grand_total').text(formatNumber(total));
}

function resetGrand_total_supplies() {
    var countSTT = $('.table-supplies').find('tbody tr');
    var total_supplies = 0;
    var quantity_supplies = 0;
    var amount_supplies = 0;
    $.each(countSTT, function(i, v){
        total_supplies += Number(unformatNumber($(v).find('.total_item').text()));
        quantity_supplies += Number(unformatNumber($(v).find('.quantity').val()));
        amount_supplies += Number(unformatNumber($(v).find('.amount').val()));
    });
    $('.table-supplies').find('tfoot .quantity_total').text(formatNumber(quantity_supplies));
    $('.table-supplies').find('tfoot .amount_total').text(formatNumber(amount_supplies));
    $('.table-supplies').find('tfoot .grand_total').text(formatNumber(total_supplies));
}

function resetIssue(trItem) {
    var id_series = trItem.parents('tr').attr('data-idseries');

    var countSTT = $('.table-issue').find('tbody tr.TrIssue[data-idseries="'+id_series+'"]');
    var stt = 1;
    $.each(countSTT, function(i, v){
        $(v).find('.stt').text(stt);
        stt++;
    });
}

function resetExpenses() {
    var countSTT = $('.table-expenses').find('tbody tr');
    var stt = 1;
    $.each(countSTT, function(i, v){
        $(v).find('.stt').text(stt);
        stt++;
    });
}

function resetSupplies() {
    var countSTT = $('.table-supplies').find('tbody tr');
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

$(document).on('change','.id_item', function (e) {
    var target = $(e.currentTarget);
    var allTr = $('.table-supplies').find('.TrSupplies');
    var checkExists = 0;
    if(target.val()) {
        $.each(allTr, function(i, v){
            var val_unique = $(v).attr('data-unique');
            if(target.val() == $(v).find('input[name="supplies['+val_unique+'][id_item]"]').val()) {
                checkExists++;
            }
        });
    }
    //checkExists == 1 : chính nó
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
                // target.parents('table').find('thead').find('.add_supplies').trigger('click');
            });
        }
    }
});

$(document).on('change','.expenses_amount', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    resetGrand_total_expenses();
});

$(document).on('click','.quantity', function (e) {
    var target = $(e.currentTarget);
    var quantity_export_warehouse = Number(target.attr('data-quantity'));
    if(Number(target.val()) <= quantity_export_warehouse) {
        target.val(quantity_export_warehouse);
        target.css({'border':'1px solid #f00', 'color':'#f00'});
        if(target.parents('td').find('.text-warning-warehouse').length == 0) {
            target.parents('td').append('<div class="text-danger text-warning-warehouse">SL không hợp lệ</div>');
        }
    }
    else {
        target.css({'border':'1px solid #bfcbd9', 'color':'#494992'});
        target.parents('td').find('.text-warning-warehouse').remove();
        resetAmount(target);
        resetGrand_total_supplies();
    }
});

$(document).on('change','.quantity', function (e) {
    var target = $(e.currentTarget);
    var quantity_export_warehouse = Number(target.attr('data-quantity'));
    if(Number(target.val()) < quantity_export_warehouse) {
        target.val(quantity_export_warehouse);
        target.css({'border':'1px solid #f00', 'color':'#f00'});
        if(target.parents('td').find('.text-warning-warehouse').length == 0) {
            target.parents('td').append('<div class="text-danger text-warning-warehouse">SL không hợp lệ</div>');
        }
    }
    else {
        target.css({'border':'1px solid #bfcbd9', 'color':'#494992'});
        target.parents('td').find('.text-warning-warehouse').remove();
        resetAmount(target);
        resetGrand_total_supplies();
    }
});

$(document).on('change','.amount', function (e) {
    var target = $(e.currentTarget);
    resetAmount(target);
    resetGrand_total_supplies();
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

$(document).on('click','.remove_supplies', function (e) {
    var target = $(e.currentTarget);
    target.parents('tr').remove();
    resetSupplies();
    resetGrand_total_supplies();
});

function rewidth(trItem) {
    var width = Number(trItem.parents('.js-width').width()) - 35;
    trItem.parents('.js-width').find('div.form-group').css({'width':width+'px'});
}

function loadLocaltion_warehouses(trItem){
    var localtion_warehouse = trItem;
    var checked = localtion_warehouse.attr('data-id');
    localtion_warehouse.attr('required',true);
    localtion_warehouse.find('option:gt(0)').remove();
    if(localtion_warehouse.length) {
        $.post(admin_url+"warehouse/list_localtion",{warehouse : 14, checked : checked, [csrfData['token_name']] : csrfData['hash']},function(data){
            localtion_warehouse.html(data).find('option').attr('disabled','disabled').parents('.localtion_warehouse').find('option[child="1"]').removeAttr('disabled');
            localtion_warehouse.find('option:nth-child(1)').removeAttr('disabled');
            localtion_warehouse.select2('val',checked);
        })
    }
}

$(document).on('change','select[name="localtion_warranty"]', function (e) {
    var target = $(e.currentTarget);
    if(target.val() == 1) {
        var TrLocaltion_warehouse = $('select.localtion_warehouse');
        $.each(TrLocaltion_warehouse, function(i, v){
            loadLocaltion_warehouses($(v));
            $(v).parents('.check-select').find('div.localtion_warehouse').css({'cursor':'pointer'});
            $(v).parents('.check-select').find('a.select2-choice').css({'background':'#f9f9f9', 'pointer-events':'unset'});
        });
    }
    else {
        var TrLocaltion_warehouse = $('select.localtion_warehouse');
        $.each(TrLocaltion_warehouse, function(i, v){
            $('#localtion_warehouse_'+i).select2('val','');
            $(v).parents('.check-select').find('div.localtion_warehouse').css({'cursor':'no-drop'});
            $(v).parents('.check-select').find('a.select2-choice').css({'background':'#eee', 'pointer-events':'none'});
        });
    }
});

function new_costs() {
    $('#costs_modal').modal('show');
    $('.edit-title').addClass('hide');
    $('#code_costs').val('');
    $('#name_costs').val('');
    $('#type_costs').selectpicker('val', '');
    $('#costs_parent').selectpicker('val', '');
    $('#costs-form').prop('action', admin_url + 'costs/add');
}
_validate_form($('#costs-form'), {code_costs: 'required', name_costs: 'required'}, manage_costs);

function manage_costs(form) {
    // var data = $(form).serialize();
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['code'] = $('#code_costs').val();
    data['name'] = $('#name_costs').val();
    data['type'] = $('#type_costs').val();
    data['costs_parent'] = $('#costs_parent').val();
    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float('success', response.message);
        }
        $('#costs_modal').modal('hide');
        var row = {};
        if (typeof(csrfData) !== 'undefined') {
          row[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'warranty/loadCosts', row).done(function (response_costs) {
            response_costs = JSON.parse(response_costs);
            var getAllTr = $('.table-expenses').find('tbody tr');
            $.each(getAllTr, function(i, v){
                var select_id = $(v).find('select.expenses_select').val();
                $(v).find('select.expenses_select').html('');
                var option = '<option value=""></option>';
                $.each(response_costs, function(i_costs, v_costs){
                    var select = '';
                    if(v_costs.id == select_id) {
                        select = 'selected';
                    }
                    option += '<option value="'+v_costs.id+'" '+select+'>'+v_costs.name+'</option>';
                });
                $(v).find('select.expenses_select').html(option);
                $(v).find('select.expenses_select').selectpicker('refresh');
            });
        });
    });
    return false;
}

function checkSubmit() {
    if($('#localtion_warranty').val() == 1) {
        var TrLocaltion_warehouse = $('select.localtion_warehouse');
        var checkTrue = true;
        $.each(TrLocaltion_warehouse, function(i, v){
            if(!$('#localtion_warehouse_'+i).val()) {
                checkTrue = false;
            }
        });
        if(checkTrue == true) {
            $('#warranty-detail').submit();
        }
        else {
            alert_float('danger', 'Vui lòng chọn vị trí kho cho các sản phẩm (Series)');
        }
    }
    else {
        $('#warranty-detail').submit();
    }
}
</script>