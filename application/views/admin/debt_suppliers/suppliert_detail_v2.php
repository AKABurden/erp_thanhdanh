  <style type="text/css">
    .table-detail_client thead tr th{
        text-align: center;
    }
    .table-detail_client tr td:nth-child(1){
        white-space: unset;
        text-align: center;
    }    
    .table-detail_client tr td:nth-child(2){
        white-space: unset;
        text-align: center;
        min-width: 100px;
    }
    .table-detail_client tr td:nth-child(3){
        white-space: unset;
        text-align: center;
        min-width: 100px;
    }
    .table-detail_client tr td:nth-child(4){
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }
    .table-detail_client tr td:nth-child(5){
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }
    .table-detail_client tr td:nth-child(6){
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }
    .table-detail_client tr td:nth-child(7){
        white-space: unset;
        text-align: right;
        min-width: 100px;
    }
  </style>
  <div class="modal fade in" id="suppliert_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo $title ?> </span>
            </h4>
          </div>
          <div class="modal-body">
              <div class="row">
                 <div class="col-md-12">
                    <div class="panel_s">
                      <div class="panel-body">
                        <div class="col-md-12">
                              <p class="bold"><?php echo _l('filter_by'); ?></p>
                              <div class="col-md-3">
                                <?php
                                     echo render_date_input('date_start','report_sales_from_date');
                                ?>
                              </div>
                              <div class="col-md-3">
                                <?php
                                     echo render_date_input('date_end','report_sales_to_date');
                                ?>
                              </div>
                            <div class="col-md-6">
                                <div class="pull-left" style="margin-top: 30px;">
                                <a class="btn btn-info  " data-toggle="collapse" data-target="#search">
                                    <?php echo _l('ch_create_pay_slip'); ?>
                                </a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <?php echo form_open(admin_url('other_payslips/pay_slip_v2/'), array('id' => 'payment-form')); ?>
                                <input type="text" name="id_suppliert" value="<?=$id?>" class="hide">  
                                <?php if(is_mobile()){ ?>
                                 <fieldset  id="search" style="float: right;" class="collapse">
                                  <legend><?=_l('ch_other_payslips')?></legend>
                                  <input type="text" name="idd" id="idd" class="hide">  
                                  <div class="col-md-12" style="padding: 8px; border: 1px solid #03a9f4; word-wrap: break-word;margin-top: -21px;">
                                        
                                                    <div class="hide">
                                                    <tr style="width: 100%;">
                                                        <label for="number" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('ch_code_p'); ?>
                                                        </label>
                                                    </tr>
                                                    <tr>
                                                        <div class="form-group">
                                                            <?php $value = (isset($items) ? $items->prefix.'-'.$items->code : $code); ?>
                                                            <input type="text" id="code_vouchers" name="" class="form-control " readonly value="<?=$value?>">
                                                        </div>
                                                    </tr>
                                                    </div>
                                                    <tr style="width: 100%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('ch_date_p'); ?>
                                                        </label>
                                                    </tr>
                                                    <tr>
                                                        <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                                        <?php echo render_date_input('date','',$value); ?>
                                                    </tr>
                                                    
                                                    <div class="hide">
                                                    <tr style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('expense_add_edit_amount'); ?>
                                                        </label>
                                                    </tr>
                                                    <tr>
                                                        <?php $total = (isset($items) ? number_format($items->total) : 0); ?>
                                                        <input type="text" readonly id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="total" class="form-control " value="<?=$total?>">
                                                    </tr>
                                                    </div>
                                                    <tr style="width: 17%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('acs_sales_payment_modes_submenu'); ?>
                                                        </label>
                                                    </tr>
                                                    <tr>
                                                        <?php $value_payment_modes = (isset($items) ? $items->payment_modes : ''); ?>
                                                        <?php echo render_select('payment_modes',$payment_modes,array('id','name'),'',$value_payment_modes); ?>
                                                    </tr>
                                                
                                                
                                                    <tr style="width: 17%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('ch_costs'); ?>
                                                        </label>
                                                    </tr>
                                                    <tr>
                                                        <?php $id_costs = (isset($items) ? $items->id_costs : ''); ?>
                                                        <?php echo render_select('id_costs',$costs,array('id','name'),'',$id_costs); ?>
                                                    </tr>
                                                    <tr style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <?php echo _l('note'); ?>
                                                        </label>
                                                    </tr>
                                                    <tr>
                                                        <?php $notes = (isset($items) ? $items->note : ''); ?>
                                                        <textarea rows="2" id="note" name="note" class="form-control" value=""><?=$notes?></textarea>
                                                    </tr>
                                                    
                                                
                                           
                                        <div class="clearfix"></div>
                                        <br>
                                        <div class="text_wanring" style="float: left;width: 80%;text-align: center;">
                                             <span style="color: red;font-size: 20px" class="bold text-center"><?=_l('ch_chose_orders')?></span>   
                                        </div>
                                        <div style="float: right;">
                                            <button type="button" id="ch_close" style="float: right;" class="btn btn-danger" data-toggle="collapse" data-target="#search"><?=_l('ch_close')?></button>
                                            <button type="submit" style="float: right;margin-right: 5px;" class="btn btn-info"  id="submit" autocomplete="off"><?=_l('submit')?></button>
                                        </div>
                                        <div class="clearfix"></div>
                                 </fieldset>   
                                <?php }else{ ?>
                                 <fieldset  id="search" style="float: right;" class="collapse">
                                  <legend><?=_l('ch_other_payslips')?></legend>
                                  <input type="text" name="idd" id="idd" class="hide">
                                  <div  style="padding: 8px; border: 1px solid #03a9f4; word-wrap: break-word;margin-top: -21px;">
                                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                            <tbody>
                                                <tr>
                                                    <td class="hide" style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('ch_code_p'); ?>
                                                        </label>
                                                    </td>
                                                    <td class="hide">
                                                        <div class="form-group">
                                                            <?php $value = (isset($items) ? $items->prefix.'-'.$items->code : $code); ?>
                                                            <input type="text" id="code_vouchers" name="" class="form-control " readonly value="<?=$value?>">
                                                        </div>
                                                    </td>
                                                    <td style="width: 17%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('ch_date_p'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                                        <?php echo render_date_input('date','',$value); ?>
                                                    </td>
                                                    <td style="width: 17%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('acs_sales_payment_modes_submenu'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php $value_payment_modes = (isset($items) ? $items->payment_modes : ''); ?>
                                                        <?php echo render_select('payment_modes',$payment_modes,array('id','name'),'',$value_payment_modes); ?>
                                                    </td>
                                                </tr>
                                                <tr class="hide">
                                                    <td style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('expense_add_edit_amount'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php $total = (isset($items) ? number_format($items->total) : 0); ?>
                                                        <input type="text" readonly id="votes_total" onkeyup="formatNumBerKeyUp(this)" name="total" class="form-control " value="<?=$total?>">
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td style="width: 17%;">
                                                        <label for="date" class="control-label">
                                                            <small class="req text-danger">* </small>
                                                            <?php echo _l('ch_costs'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php $id_costs = (isset($items) ? $items->id_costs : ''); ?>
                                                        <?php echo render_select('id_costs',$costs,array('id','name'),'',$id_costs); ?>
                                                    </td>
                                                    <td style="width: 17%;">
                                                        <label for="number" class="control-label">
                                                            <?php echo _l('note'); ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php $notes = (isset($items) ? $items->note : ''); ?>
                                                        <textarea rows="3" id="note" name="note" class="form-control" value=""><?=$notes?></textarea>
                                                    </td>
                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="clearfix"></div>
                                        <br>
                                        
                                        <div class="text_wanring" style="float: left;width: 80%;text-align: center;">
                                             <span style="color: red;font-size: 20px" class="bold text-center"><?=_l('ch_chose_orders')?></span>   
                                        </div>
                                        <div style="float: right;">
                                            <button type="button" id="ch_close" style="float: right;" class="btn btn-danger" data-toggle="collapse" data-target="#search"><?=_l('ch_close')?></button>
                                            <button type="submit" style="float: right;margin-right: 5px;" class="btn btn-info"  id="submit" autocomplete="off"><?=_l('submit')?></button>
                                        </div>
                                        <div class="clearfix"></div>
                                 </fieldset>
                                <?php } ?>
                          </div>
                        </div>
                        </form>
                        <div class="clearfix"></div>
                          <hr>
                          <?php $table_data = array(
                                '<span class="hide"> - </span><div class="checkbox checkbox_ch mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="detail_client"><label></label></div>',
                              _l('ch_date_p'),
                              _l('ch_code_p'),
                              _l('ch_debt_total'),
                              _l('ch_status_pays_slip'),
                              _l('ch_total_left'),
                            );
                            render_datatable($table_data,'detail_client');
                          ?>
                       </div>
                    </div>
                 </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
              </div>
          </div>
      </div>
  </div>
  <script type="text/javascript">
var tAPI
$(function(){

    var CustomersServerParams = {
      'date_start' : '[name="date_start"]',
      'date_end' : '[name="date_end"]',
    };
    tAPI = initDataTable('.table-detail_client', admin_url+'debt_suppliers/table_debt_suppliers/'+<?=$id?>, [2], [0], CustomersServerParams,[1, 'desc']);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
      $('' + filterItem).on('change', function(){
        tAPI.ajax.reload();
      });
    });
});
$('.table-detail_client').on('draw.dt', function() {
        var rows = $('.table-detail_client').find('tbody tr');
        var total = 0 ;
        var idd ='';
          $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
               total+= parseFloat(checkbox.attr('data-id'));
               idd+=checkbox.val()+',';
            }
          });
          $('#idd').val(idd);
          $('#votes_total').val(formatNumber(total));
});
    _validate_form($('#payment-form'), {
        code_vouchers: "required",
        date: "required",
        objects: "required",
        payment_modes: "required",
        payment: "required",
        objects_id: "required",
        id_costs: "required",
        total: "required",
        objects_text: "required",
    },add_payment_s);
    function add_payment_s(form) {
        var total = unformat_number($('#votes_total').val());
        if(total <= 0)
        {
            alert('<?=_l('ch_alert_check')?>');return;
        }    
        var objects_id = $('#objects_id').val();
        var objects = $('#objects').val();
        var type_vouchers = $('#type_vouchers').val();
        var data = $(form).serialize(),
             action = form.action;
        return $.post(action, data).done(function(form) {
             form = JSON.parse(form),
             alert_float(form.alert_type, form.message);
             if(form.success)
             {
                 tAPI.draw('page');
                 $('#ch_close').click();
             }

        }), !1
    }    
  </script>

  </div>