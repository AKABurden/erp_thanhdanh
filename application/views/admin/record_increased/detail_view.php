<style type="text/css">
  
@import url(https://fonts.googleapis.com/css?family=Open+Sans);
a{text-decoration:none;}
li {list-style-type:none;}

p{font:1em 'Open Sans', sans-serif;}
.tit-nivel3{font:1.5em 'Open Sans', sans-serif;}

ul.tabs {
  overflow: auto;
  height: 300px;
}

ul.tabs li {
  margin: 0;
  cursor: pointer;
  padding: 10px;
  font:1em 'Open Sans', sans-serif;
}

.tab_last {
    background:#900!important;
    margin-top: 50px!important;
    color:#fff!important;
    font:1em 'Open Sans', sans-serif;
}

ul.tabs li:hover {
  background:#bbbbbb2e;
  color:#2885d0;
  border-radius: 5px;
}

ul.tabs li.active {
  background:#bbbbbb2e;
  color:#2885d0;
  border-radius: 5px;
}

.tab_content {
  display: none;
}
.tab_container {
    height: 460px;
  }

.tab_drawer_heading { display: none; }

@media screen and (max-width: 620px) {

  .tab_container {
    width: 100%;
  }

  .tabs {
    display: none;
  }
  .tab_drawer_heading {
    background:#1a1a1a;
    color: #fff;
    border-top: 1px solid #333;
    margin: 0;
    padding: 5px 20px;
    display: block;
    cursor: pointer;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .d_active {
    background-color:#111;
    color: #fff!important;
  }
}
/*hoàng crm bổ xung*/
.panel_box {
  margin: 0;
  box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
}
.center {
  text-align: center;
}
.tab_container i {
  cursor: pointer;
}
.table-scroll {
  max-height: 310px;
  overflow: auto;
}
.wap-right_RFQ {
  height: 400px;
}
.tab-pane{
  display: none;
}
.tab-pane.active{
  display: block;
}
.nav-tabs {
  margin-bottom: 0; 
  background: 0 0; 
  border-radius: 0;
}
.thead-row {
  text-align: center;
  text-transform: uppercase;
  font-weight: 700 !important;
  line-height: 40px;
  background: #3f9ad6;
  color: #fff;
}
.mtop25 {
  margin-top: 25px !important;
}
.padding20 {
  padding: 20px 0 !important;
}
.thead-col {
  text-align: center;
  white-space: unset;
}
.input-col {
  text-align: center;
  border: 0 !important;
  outline: 0 !important;
  border-bottom: 1px solid #9e9e9e !important;
}
.border-bottom {
  border-bottom: 1px solid #9e9e9e !important;
}
.mbottom {
  margin-bottom: 15px;
}
.padding0 {
  padding: 0 !important;
}
.boder-lr {
  border-right: 1px solid #a4a4a4;
  border-left: 1px solid #a4a4a4;
}
.table.table-striped tbody td{
border: 1px solid #f0f0f0;
}
.text-muted {
  color: red !important;
}
td .input-group{
    width: 100%
}
</style>
<div class="modal fade" id="record_increased" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="book-title"><?php echo $title ?> </span>
                    </h4>
            </div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                   <a href="#tab_info" aria-controls="tab_setting" role="tab" data-toggle="tab">
                       <?=_l('lead_general_info')?>
                   </a>
                </li>
                <li role="presentation">
                   <a href="#items" aria-controls="tab_setting" role="tab" data-toggle="tab">
                       <?=_l('ch_origin_of')?>
                   </a>
                </li>
                <li role="presentation">
                   <a href="#attribution" aria-controls="tab_setting" role="tab" data-toggle="tab">
                       <?=_l('ch_attribution')?>
                   </a>
                </li>   
                <li role="presentation">
                   <a href="#depreciation" aria-controls="tab_setting" role="tab" data-toggle="tab">
                       <?=_l('ch_amortization')?>
                   </a>
                </li>
            </ul>
            <?php $value = (isset($items) ? $items->id : ''); ?>
        
                        <div role="tabpanel" class="tab-pane" id="attribution">
                <div class="modal-body" style="background: #f1f1f1">
                    <div class="col-md-12">
                        <div class="panel_s panel_box">
                            <div class="panel-body">
                                <div class="tab_container" style="position: relative;">
                                    <div style="height: calc(450px);overflow: auto;">
                                        <table style="table-layout: fixed;" class="dt-tnh table item-attribution table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;" class="text-left">
                                                        <!-- <a onclick="button_create()" class="btn btn-info btn-icon">+</a> -->
                                                        STT
                                                    </th>
                                                    <th style="width: 250px;"><?=_l('Đối tượng phân bổ')?></th>
                                                    <th style="width: 200px;"><?php echo _l('Tỷ lệ PB (%)'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    <?php $j_v2 = 0; if(!empty($attribution)){ ?>
                                                        <?php foreach ($attribution as $key => $value) { ?>
                                                            <tr class="sortable item">
                                                            <td class="dragger avatar" style="text-align: center;"><input type="hidden" class="id" id="attribution_id" name="attribution[<?=$j_v2?>][id]" value="<?=$value['units_useds']?>" /><?=($j_v2 + 1)?></td>
                                                            <td ><input type="hidden" class="count" value="<?=$j_v2?>" /><div class="form-group" style="width: 100%"><select disabled style="width: 100%" class="units_useds" id="units_useds_<?=$j_v2?>" name="attribution[<?=$j_v2?>][units_useds]" style="width: 200px;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
                                                            <?php foreach($departments as $v) { ?>
                                                                <option <?=($v['departmentid']==$value['units_useds'])?'selected':''?> value="<?=$v['departmentid']?>"><?=$v['name']?></option>\
                                                            <?php }?>
                                                            </select></div></td>
                                                            <td style="text-align: center;"><input readonly type="number" id="attribution_percentage" class="attribution_percentage H_input"  value="<?=$value['percent']?>"  name="attribution[<?=$j_v2?>][attribution_percentage]"></td>
                                                            </tr>                                          
                                                        <?php $j_v2++; } ?>
                                                    <?php }?>
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
            <div role="tabpanel" class="tab-pane" id="depreciation">
                <div class="modal-body" style="background: #f1f1f1">
                    <div class="col-md-12">
                        <div class="panel_s panel_box">
                            <div class="panel-body">
                                <div class="tab_container">
                            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width: 16%;">
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_start_date_of_depreciation'); ?>
                                            </label>
                                        </td>
                                        <td style="width: 14%;border-right: 0px !important;">
                                            <?php $value = (isset($items) ? _d($items->date_depreciation) : _d(date('Y-m-d'))); ?>
                                            <?php echo render_date_input('date_depreciation','',$value); ?>
                                        </td>
                                        <td style="width: 15%;border-left: 0px !important;">
                                            
                                        </td>
                                        <td style="width: 16%;">
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_monthly_depreciation_rate'); ?> (%)
                                            </label>
                                        </td>
                                        <td style="width: 39%;">
                                            <div class="form-group">
                                                <?php $value = (isset($items) ? $items->monthly_depreciation_rate : 0); ?>
                                                <?php echo render_input('monthly_depreciation_rate','',formatNumber($value),'text',array('onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>                                            </div>  
                                        </td>
                                    </tr>   
                                    <tr>
                                        <td>
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_used_Time'); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php $value = (isset($items) ? $items->used_time : 1); ?>
                                            <?php $array = array(
                                                array('id'=>1,
                                                      'name'=>_l('years')
                                                ),
                                                array('id'=>2,
                                                      'name'=>_l('months')
                                                )
                                            ); ?>
                                            <?php echo render_select_no_search('used_time', $array, array('id', 'name'),'',$value,array(),array(),'','',false); ?>
                                        </td>
                                        <td>
                                            <?php $value = (isset($items) ? $items->number_used_time : 0); ?>
                                            <?php echo render_input('number_used_time','',$value,'text',array('onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_annual_depreciation_rate'); ?> (%)
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->annual_depreciation_rate : 0); ?>
                                            <?php echo render_input('annual_depreciation_rate','',$value,'text',array('readonly'=>'readonly','onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_original_price'); ?>
                                            </label>
                                        </td>
                                        <td colspan="2">
                                            <?php $value = (isset($items) ? $items->original_price : 0); ?>
                                            <?php echo render_input('original_price','',number_format($value),'text',array('readonly'=>'readonly','onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_monthly_depreciation_value'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->monthly_depreciation_value : 0); ?>
                                            <?php echo render_input('monthly_depreciation_value','',number_format($value),'text',array('onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_value_of_depreciation'); ?>
                                            </label>
                                        </td>
                                        <td colspan="2">
                                            <?php $value = (isset($items) ? $items->value_of_depreciation : 0); ?>
                                            <?php echo render_input('value_of_depreciation','',number_format($value),'text',array('onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                        
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_yearly_depreciation_value'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->yearly_depreciation_value : 0); ?>
                                            <?php echo render_input('yearly_depreciation_value','',number_format($value),'text',array('readonly'=>'readonly','onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  colspan="3" style="border-bottom: 0px !important;">
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_accumulated_depreciation'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->accumulated_depreciation : 0); ?>
                                            <?php echo render_input('accumulated_depreciation','',number_format($value),'text',array('onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  colspan="3" style="border-top: 0px !important;">
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_residual_value'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->residual_value : 0); ?>
                                            <?php echo render_input('residual_value','',number_format($value),'text',array('readonly'=>'readonly','onkeyup'=>'formatNumBerKeyUpCus(this)'),array(),'','text-right'); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="items">
                <div class="modal-body" style="background: #f1f1f1">
                    <div class="col-md-12">
                        <div class="panel_s panel_box">
                            <div class="panel-body">
                                <div class="tab_container" style="position: relative;">
                                    <div style="height: calc(400px - 40px);overflow: auto;">
                                        <table style="table-layout: fixed;" class="dt-tnh table item-record_increased table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;" class="text-left">
                                                        <!-- <a onclick="button_create()" class="btn btn-info btn-icon">+</a> -->
                                                        STT
                                                    </th>
                                                    <th style="width: 250px;"><?=_l('ch_number_code')?></th>
                                                    <th style="width: 100px;"><?php echo _l('ch_date_p'); ?></th>
                                                    <th style="width: 200px;"><?php echo _l('ch_explain'); ?></th>
                                                    <th style="width: 200px;"><?php echo _l('exchange_amount_value'); ?></th>
                                                    <th style="width: 50px;"><?php echo _l('ch_option'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    <?php $j = 0; if(!empty($items)){ ?>
                                                        <?php foreach ($items->items as $key => $value) { ?>
                                                            <tr class="sortable item">
                                                            <td class="dragger avatar" style="text-align: center;"><input type="hidden"  value="hau" /></td>
                                                            <td ><input type="hidden" class="count" value="<?=$j?>" /><input style="width:100%;" data-placeholder="<?=_l('dropdown_non_selected_tex')?>" class="custom_item_select" data-id="<?=$value['id_other_payslips']?>" id="custom_item_select_<?=$j?>"  name="items[<?=$j?>][custom_item_select]"  style="width: 100%"><input type="hidden" name="items[<?=$j?>][idd]" value="<?=$value['id']?>" /></td>
                                                            <td style="text-align: center;" class="date_ch"></td>
                                                            <td style="text-align: left;" class="note_ch"></td>
                                                            <td style="text-align: right;" class="amount_ch"></td>
                                                            <td class="delete"></td>
                                                            </tr>                                          
                                                        <?php $j++; } ?>
                                                    <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div style="position: absolute;bottom: 0;width: 100%;">
                                        <div class="">
                                            <table class="table tnh-tb noMargin table-color_sum">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 30%">
                                                            <span class="bold"><?php echo _l('ch_general_votes'); ?> :</span>
                                                        </td>
                                                        <td style="width: 20%" class="total_quantity_all">
                                                        </td>
                                                        <td style="width: 30%">
                                                            <span class="bold"><?php echo _l('total_price'); ?> :</span>
                                                        </td>
                                                        <td style="width: 20%"  class="text-right total_price">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane active" id="tab_info">
                <div class="modal-body" style="background: #f1f1f1">
                  <div class="col-md-12">
                    <div class="panel_s panel_box">
                      <div class="panel-body">
                        <div class="tab_container">
                            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width: 10%;">
                                            <label for="number" class="control-label">
                                                <?php echo _l('ch_code_p'); ?>
                                            </label>
                                        </td>
                                        <td style="width: 30%;">
                                            <div class="form-group">
                                                <?php $value = (isset($items) ? $items->code_vouchers : $code); ?>
                                                <input type="text" id="code_vouchers" name="code_vouchers" class="form-control " readonly value="<?=$value?>">
                                            </div>
                                        </td>
                                        <td style="width: 13%;">
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_year_of_manufacture'); ?>
                                            </label>
                                        </td>
                                        <td style="width: 13%;">
                                            <div class="form-group">
                                                <select id="year_of_manufacture" name="year_of_manufacture" class="selectpicker" data-width="100%" data-none-selected-text="Chưa chọn năm" data-live-search="true" tabindex="-98">
                                                    <?php
                                                    $nam = (isset($items) ? $items->year_of_manufacture : date('Y'));
                                                     for($i=(date('Y')-10);$i<=date('Y');$i++){
                                                        $selected=($i==$nam?'selected':'');
                                                        ?>
                                                        <option value="<?=$i?>" <?=$selected?>><?=$i?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="width: 8%;">
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_numberss'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->number_sh : ''); ?>
                                            <?php echo render_input('number_sh','',$value); ?>
                                        </td>
                                    </tr>   
                                    <tr>
                                        <td>
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_date_up'); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php $value = (isset($items) ? _d($items->date_of_recording_increases) : _d(date('Y-m-d'))); ?>
                                            <?php echo render_date_input('date_of_recording_increases','',$value); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_producer'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <?php $value = (isset($items) ? $items->Producer : ''); ?>
                                            <?php echo render_input('Producer','',$value); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_units_useds'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->units_used : ''); ?>
                                            <?php $departments = get_table_where('tbldepartments'); ?>
                                            <?php echo render_select('units_used', $departments, array('departmentid', 'name'),'',$value); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_country_of_manufacture'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <?php $value = (isset($items) ? $items->country_of_manufacture : ''); ?>
                                            <?php echo render_input('country_of_manufacture','',$value); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_code_asset'); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php $value = (isset($items) ? $items->property_code : ''); ?>
                                            <?php echo render_input('property_code','',$value); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('Thời gian bảo hành'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <?php $value = (isset($items) ? $items->warranty_period : ''); ?>
                                            <?php echo render_input('ch_warranty_period','',$value); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_name_asset'); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <?php $value = (isset($items) ? $items->asset_name : ''); ?>
                                            <?php echo render_input('asset_name','',$value); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('ch_current_quality'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->current_quality : 1); ?>
                                            <?php $array = array(
                                                array('id'=>1,
                                                      'name'=>_l('ch_works_well')
                                                ),
                                                array('id'=>2,
                                                      'name'=>_l('ch_damaged')
                                                )
                                            ); ?>
                                            <?php echo render_select_no_search('current_quality', $array, array('id', 'name'),'',$value,array(),array(),'','',false); ?>
                                        </td>
                                        <td >
                                            <label for="date" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('ch_type_asset'); ?>
                                            </label>
                                        </td>
                                        <td >
                                            <?php $value = (isset($items) ? $items->type_record_increased : ''); ?>
                                            <?php echo render_select('type_record_increased', $type_record_increased, array('id', 'name'),'',$value); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <label for="date" class="control-label">
                                                <?php echo _l('contracts_notes_tab'); ?>
                                            </label>
                                        </td>
                                        <td colspan="5">
                                            <?php $value = (isset($items) ? $items->note : ''); ?>
                                            <?php echo render_textarea('note','',$value); ?>
                                        </td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  </div> 
                  <div class="clearfix"></div>   
                </div>
            </div>    
            <div class="clearfix"></div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //load trang voi action
    <?php if(!empty($items)){ ?>
    appendtype();
    function appendtype()
    {
        var items = $('table.item-record_increased tbody').find('tr.item');
        $.each(items, (index,value)=>{
            var ID = $('#custom_item_select_'+index).attr('data-id');
            ajaxSelectCallBack($('#custom_item_select_'+index), "<?=admin_url('record_increased/SearchItems')?>", ID,'<?=$items->id?>');
            $('#custom_item_select_'+index).trigger('change');
        });
        var items_v2 = $('table.item-attribution tbody').find('tr.item');
        $.each(items_v2, (index,value)=>{
            $('#units_useds_'+index).select2();
        });
    }
    <?php }?>
    $(function(){
        // validate_invoice_form();
        _validate_form($('#record_increased-form'), {
        date_of_recording_increases: "required",
        units_used: "required",
        type_record_increased: "required",
        property_code: "required",
        asset_name: "required",
        date_depreciation: "required",
        number_used_time: "required",
        original_price: "required",
        value_of_depreciation: "required",
        residual_value: "required",

    },add_quotes_client_s);

            function add_quotes_client_s(form) {
                var residual_value = $('#residual_value').val();
                if(residual_value == 0)
                {
                    alert('<?=_l('ch_alert_lh0')?>');return;
                }
               var data = $(form).serialize(),
                   action = form.action;
               return $.post(action, data).done(function(form) {
                    form = JSON.parse(form),
                    alert_float(form.alert_type, form.message);
                    $('#record_increased').modal('hide');
                    tAPI.draw('page');
               }), !1
            }
    });
    $(function(){
        var dtt = $('.item-attribution').DataTable({
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fixedHeader': true,
            "language": {
              "emptyTable": "Bạn chưa chọn đơn vị sử dụng ở thông tin chung"
            },

            // scrollY: true,
            // scrollY: '150px',
            // scrollX: true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });
        var dt = $('.item-record_increased').DataTable({
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fixedHeader': true,
            // scrollY: true,
            // scrollY: '150px',
            // scrollX: true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });
    //     _validate_form($('#record_increased-form'), {
    //     date: "required",
    //     number: "required",
    //     suppliers_id: "required",
    //     delivery_date: "required",
    // });
    });
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };
    function countrow()
    {
        if(!$('table.item-record_increased tbody').find('input[value=hau]').length)
        {
        }
    }
    var button_create = ()=>{
        if(!$('table.item-record_increased tbody').find('input[value=hau]').length)
        {
        }
    }
    var createTrItem = (item,currentQuantityInput) => {
        
    }
    var uniqueArray = <?=$j?>;
    var createTrItemfist = (item) => {
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatar" style="text-align: center;"><input type="hidden"  value="hau" />'+(uniqueArray+1)+'</td>');
        var td2 = $('<td ><input type="hidden" class="count" value="'+uniqueArray+'" /><input style="width:100%;" data-placeholder="<?=_l('dropdown_non_selected_tex')?>" class="custom_item_select" id="custom_item_select_'+uniqueArray+'"  name="items[' + uniqueArray + '][custom_item_select]" name="custom_item_select_'+uniqueArray+'" style="width: 100%"><br><br><div class="color"><div></td>');
        var td3 = $('<td style="text-align: center;" class="date_ch"></td>');
        var td4 = $('<td style="text-align: left;" class="note_ch"></td>');
        var td5 = $('<td style="text-align: right;" class="amount_ch"></td>');

        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append('<td class="delete"></td');
        $('table.item-record_increased tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        <?php if(!empty($items)){ ?>
        ajaxSelectCallBack($('#custom_item_select_'+uniqueArray), "<?=admin_url('record_increased/SearchItems')?>", 0,'<?=$items->id?>');
        <?php }else{?>
        ajaxSelectCallBack($('#custom_item_select_'+uniqueArray), "<?=admin_url('record_increased/SearchItems')?>", 0);
        <?php }?>
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        uniqueArray++;
    }
    function getTotalPrice()
    {   
        var items = $('table.item-record_increased tbody').find('tr.item');
        var totalQuantity = 0;
        var totalQuantityNet = 0;
        var totalPrice = 0;
        $.each(items, (index,value)=>{
            if(!$(value).find('input[value=hau]').length)
            {
            totalQuantity++;
            totalPrice += parseFloat($(value).find('.amount_ch').text().replace(/\,/g, ''));
            }
        });
        $('.total_quantity_all').text(formatNumber(totalQuantity));
        $('#value_of_depreciation').val(formatNumber(totalPrice));
        $('#original_price').val(formatNumber(totalPrice));
        $('#value_of_depreciation').keyup();
        $('.total_price').text(formatNumber(totalPrice));
        residual_value();

    }   
    function ajaxSelectCallBack(element, url, id, types = '')
            {
                if (id > 0)
                {
                    $(element).val(id).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: false,
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
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: false,
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
    var base_url = '<?=base_url()?>';
    function repoFormatSelection(state) {
        if (!state.id) return state.text;
        
        return  state.text ;
    }
    $('body').on('hidden.bs.modal', '#record_increased', function() {
            $('#view_new_record_increased').html('');
        });

</script>