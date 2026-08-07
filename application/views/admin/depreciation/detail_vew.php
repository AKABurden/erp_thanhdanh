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
<div class="modal fade" id="depreciation" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="book-title"><?php echo $title ?> </span>
                    </h4>
            </div>
            <?php $value = (isset($items) ? $items->id : ''); ?>
                <div class="modal-body" style="background: #f1f1f1">
                    <div class="col-md-12">
                        <div class="panel_s panel_box">
                            <div class="panel-body">
                                <div class="tab_container" style="position: relative;">
                                    <div class="col-md-4  pull-left">
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Thông tin</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="well well-sm">
                                                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 40%;">
                                                                        <label for="number" class="control-label">
                                                                            <?php echo _l('ch_code_p'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td colspan="3">
                                                                        <div class="form-group">
                                                                            <?php $value = (isset($items) ? $items->code : $code); ?>
                                                                            <input type="text" id="code" name="code" class="form-control " readonly value="<?=$value?>">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 40%">
                                                                        <label for="date" class="control-label">
                                                                            <small class="req text-danger">* </small>
                                                                            <?php echo _l('ch_date_p'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td colspan="3">
                                                                        <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                                                        <?php echo render_date_input('date','',$value); ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td >
                                                                        <label for="date" class="control-label">
                                                                            <small class="req text-danger">* </small>
                                                                            <?php echo _l('ch_explain'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td colspan="3">
                                                                        <?php $value = (isset($items) ? $items->note : 'Khấu hao tháng '.$month.' năm '.$year); ?>
                                                                        <?php echo render_textarea('note','',$value); ?>
                                                                    </td>
                                                                </tr> 
                                                                <tr>
                                                                    <td >
                                                                        <label for="date" class="control-label">
                                                                            <small class="req text-danger">* </small>
                                                                            <?php echo _l('months'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td >
                                                                        <?php $value = (isset($items) ? $items->month :''); ?>
                                                                        <?=$value?>
                                                                    </td>
                                                                    <td >
                                                                        <label for="date" class="control-label">
                                                                            <small class="req text-danger">* </small>
                                                                            <?php echo _l('years'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td >
                                                                        <?php $value = (isset($items) ? $items->year : ''); ?>
                                                                        <?=$value?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>  
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation">
                                           <a href="#depreciations"  class="active" aria-controls="depreciations" role="tab" data-toggle="tab">
                                               <?=_l('ch_amortization')?>
                                           </a>
                                        </li>   
                                        <li role="presentation">
                                           <a href="#attribution" aria-controls="attribution" role="tab" data-toggle="tab">
                                               <?=_l('ch_attribution')?>
                                           </a>
                                        </li>            
                                    </ul>
                                    <div class="clearfix"></div>
                                    <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane" id="attribution">
                                        <div style="height: calc(400px - 40px);overflow: auto;">
                                            <table style="table-layout: fixed;" class="dt-tnh table item-attribution table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 30px;" class="text-left">
                                                            #
                                                        </th>
                                                        <th style="width: 150px;"><?=_l('Tên đối tượng phân bổ')?></th>
                                                        <th style="width: 70px;"><?=_l('Tỷ lệ PB(%)')?></th>
                                                        <th style="width: 70px;"><?=_l('Thành tiền')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 0;
                                                     foreach ($attribution as $key => $value) {?>
                                                        <?php
                                                            if($key == 0)
                                                            {
                                                                $datas = $value['increased_id'];
                                                            ?>
                                                                <tr class="alert-header bold warning">
                                                                    <td colspan="3">
                                                                        <span class="bold"><?=($key+1)?> - <?=$value['increased']?></span>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <span class="bold "><?=number_format(round($value['subtotals']))?></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <span class="bold"></span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="bold"><?=$value['attribution_name']?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="bold"><?=$value['percent']?></span>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <span class="bold "><?=number_format(round($value['total']))?></span>
                                                                    </td>                                                 
                                                                </tr>
                                                            <?php }else{ 
                                                                if($value['increased_id'] != $datas)
                                                                {
                                                                ?>
                                                                <tr class="alert-header bold warning">
                                                                    <td colspan="3">
                                                                        <span class="bold"><?=($key+1)?> - <?=$value['increased']?></span>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <span class="bold "><?=number_format(round($value['subtotals']))?></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <span class="bold"></span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="bold"><?=$value['attribution_name']?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="bold"><?=$value['percent']?></span>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <span class="bold "><?=number_format(round($value['total']))?></span>
                                                                    </td>                                                 
                                                                </tr>
                                                                <?php $datas = $value['increased_id']; 
                                                                }else{ 
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="bold"></span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="bold"><?=$value['attribution_name']?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="bold"><?=$value['percent']?></span>
                                                                    </td>
                                                                    <td class=" text-right">
                                                                        <span class="bold"><?=number_format(round($value['total']))?></span>
                                                                    </td>                                                 
                                                                </tr>
                                                                <?php
                                                            } ?>
                                                    <?php 
                                                    }} ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane active" id="depreciations">
                                    <div style="height: calc(400px - 40px);overflow: auto;">
                                        <table style="table-layout: fixed;" class="dt-tnh table item-record_increased table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 30px;" class="text-left">
                                                        #
                                                    </th>
                                                    <th style="width: 70px;"><?=_l('ch_code_asset')?></th>
                                                    <th style="width: 150px;"><?=_l('ch_name_asset')?></th>
                                                    <th style="width: 150px;"><?=_l('ch_type_asset')?></th>
                                                    <th style="width: 110px;"><?php echo _l('ch_units_used'); ?></th>
                                                    <th style="width: 150px;"><?php echo _l('ch_monthly_depreciation_value'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total = 0;
                                                $i = 0;
                                                 foreach ($increased as $key => $value) {  $i++;?>
                                                    <tr>
                                                        <td>
                                                            <span class="bold"><?=($key+1)?></span>
                                                        </td>
                                                        <td>
                                                            <span class="bold"><?=$value['property_code']?></span>
                                                        </td>
                                                        <td>
                                                            <span class="bold"><?=$value['asset_name']?></span>
                                                        </td>
                                                        <td>
                                                            <span class="bold"><?=$value['name_type']?></span>
                                                        </td>
                                                        <td>
                                                            <span class="bold"><?=$value['departments']?></span>
                                                        </td>
                                                        <td class="text-right">
                                                            <span class="bold"><?=number_format($value['total'])?></span>
                                                        </td>
                                                    </tr>
                                                <?php $total+=$value['total'];
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                    </div>
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
                                                            <?=number_format($i);?>
                                                        </td>
                                                        <td style="width: 30%">
                                                            <span class="bold"><?php echo _l('total_price'); ?> :</span>
                                                        </td>
                                                        <td style="width: 20%"  class="text-right total_price">
                                                            <?=number_format($total);?>
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
            <div class="clearfix"></div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        // validate_invoice_form();
        _validate_form($('#record_increased-form'), {
        date: "required",
        note: "required",
        code: "required",

    },add_quotes_client_s);

            function add_quotes_client_s(form) {
               var data = $(form).serialize(),
                   action = form.action;
               return $.post(action, data).done(function(form) {
                    form = JSON.parse(form),
                    alert_float(form.alert_type, form.message);
                    $('#depreciation').modal('hide');
                    tAPI.draw('page');
               }), !1
            }
    });
    $('body').on('hidden.bs.modal', '#record_increased', function() {
            $('#view_new_record_increased').html('');
        });
    $(function(){
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
    });
</script>