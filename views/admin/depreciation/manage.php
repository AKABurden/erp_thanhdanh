<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .table-depreciation thead tr th{
        text-align: center;
    }
    .table-depreciation thead tr th:nth-child(1){
        min-width: 20px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation thead tr th:nth-child(2){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation thead tr th:nth-child(3){
        min-width: 80px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation thead tr th:nth-child(4){
        min-width: 70px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation thead tr th:nth-child(5){
        min-width: 140px;
        white-space: unset;
    }
    .table-depreciation tr td:nth-child(1){
        min-width: 20px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation tr td:nth-child(2){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation tr td:nth-child(3){
        min-width: 80px;
        white-space: unset;
        text-align: center;
    }
    .table-depreciation tr td:nth-child(4){
        min-width: 70px;
        white-space: unset;
    }
    .table-depreciation tr td:nth-child(5){
        min-width: 140px;
        white-space: unset;
        text-align: right;
    }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <button type="button" class="btn btn-info mright5 test pull-right H_action_button" data-toggle="modal" data-target="#myModal"><i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                       <?php echo _l('create_add_new'); ?></a></button>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <input type="hidden" id="suppliers_id" name="suppliers_id" value=""/>
                  <div class="clearfix"></div>
                  <?php $table_data = array(
                      _l('#'),
                      _l('ch_number_code'),
                      _l('ch_date_p'),
                      _l('ch_explain'),
                      _l('exchange_amount_value'),
                    );
                    render_datatable($table_data,'depreciation');
                  ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<?php init_tail(); ?>
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="width: 30%">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Chọn kỳ tính khấu hao</h4>
        </div>
        <div class="modal-body">
            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                <tbody>
                    <tr>
                        <td style="width: 20%">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('months'); ?>
                            </label>
                        </td>
                        <td >
                            <select id="month_of_manufacture" name="year_of_manufacture" class="selectpicker" data-width="100%" data-none-selected-text="Chưa chọn năm" data-live-search="true" tabindex="-98">
                                    <?php
                                    $nam = (isset($items) ? $items->year_of_manufacture : date('m'));
                                     for($i=1;$i<=12;$i++){
                                        $selected=($i==$nam?'selected':'');
                                        ?>
                                        <option value="<?=$i?>" <?=$selected?>><?=$i?></option>
                                    <?php }?>
                            </select>
                        </td>
                        <td style="width: 20%">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('years'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="form-group">
                                <select id="year_of_manufacture" name="year_of_manufacture" class="selectpicker" data-width="100%" data-none-selected-text="Chưa chọn tháng" data-live-search="true" tabindex="-98">
                                    <?php
                                    $nam = (isset($items) ? $items->year_of_manufacture : date('Y'));
                                     for($i=2010;$i<=(date('Y')+10);$i++){
                                        $selected=($i==$nam?'selected':'');
                                        ?>
                                        <option value="<?=$i?>" <?=$selected?>><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </td>
                    </tr> 
                </tbody>
            </table> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" onclick="new_depreciation(); return">Đồng ý</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Hủy</button>
        </div>
      </div>
      
    </div>
  </div>
<div id="view_new_depreciation"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>

        var tAPI;
        $(function(){
            var CustomersServerParams = {
              // 'filterStatus' : '[name="filterStatus"]',
            };
            tAPI =  initDataTableCustom('.table-depreciation', admin_url+'depreciation/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 0, rightColumns: 0});
            $.each(CustomersServerParams, function(filterIndex, filterItem){
                  $(filterItem).on('change', function(){
                        tAPI.draw('page');
                  });
            });
        });
        $(document).on('click', '.delete-remind', function() {
            var r = confirm("<?php echo _l('confirm_action_prompt');?>");
            if (r == false) {
                return false;
            } else {
                $.get($(this).attr('href'), function(response) {
                  alert_float(response.alert_type, response.message);
                    tAPI.draw('page');
                }, 'json');
            }
            return false;
        });
        function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
            nStr += '';
            x = nStr.split(decSeperate);
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            x2=x2.substr(0,2);
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
            }
            return x1 + x2;
        };
        function unformat_number(number)
        {
            var _number=0;
            if(number)
            {
                _number=number.replace(/[^\-\d\.]/g, '');
            }
            return _number;
        };
        function edit_depreciation(id) {
            $('#view_new_fixed_assets').html('');
            $.get(admin_url + 'depreciation/edit_depreciation/'+id).done(function (response) {
                $('#view_new_depreciation').html(response);
                $('#depreciation').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }        
        function new_depreciation() {
            $('#view_new_fixed_assets').html('');
            var month = $('#month_of_manufacture').val();
            var year = $('#year_of_manufacture').val();
            $.post(admin_url + 'depreciation/add_depreciation/',{month:month,year:year,[csrfData['token_name']] : csrfData['hash']}).done(function (response) {
                $('#view_new_depreciation').html(response);
                $('#depreciation').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
                $('#myModal').modal('hide');

            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }
        $('body').on('hidden.bs.modal', '#depreciation', function() {
            $('#view_new_depreciation').html('');
        });
</script>
