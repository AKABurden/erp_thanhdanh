<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .table-record_increased thead tr th{
        text-align: center;
    }
    .table-record_increased thead tr th:nth-child(1){
        min-width: 20px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased thead tr th:nth-child(2){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased thead tr th:nth-child(3){
        min-width: 80px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased thead tr th:nth-child(4){
        min-width: 70px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased thead tr th:nth-child(5){
        min-width: 140px;
        white-space: unset;
    }
    .table-record_increased tr td:nth-child(1){
        min-width: 20px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(2){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(3){
        min-width: 80px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(4){
        min-width: 70px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(5){
        min-width: 140px;
        white-space: unset;
    }
    .table-record_increased tr td:nth-child(6){
        min-width: 140px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(7){
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(8){
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }
    .table-record_increased tr td:nth-child(9){
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }
    .table-record_increased tr td:nth-child(10){
        min-width: 110px;
        white-space: unset;
        text-align: right;
    }
    .table-record_increased tr td:nth-child(11){
        min-width: 110px;
        white-space: unset;
        text-align: right;
    }
    .table-record_increased tr td:nth-child(12){
        min-width: 170px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(13){
        min-width: 130px;
        white-space: unset;
        text-align: right;
    }
    .table-record_increased tr td:nth-child(14){
        min-width: 150px;
        white-space: unset;
        text-align: center;
    }
    .table-record_increased tr td:nth-child(15){
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
             <a href="" onclick="new_record_reduce(); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                       <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                       <?php echo _l('create_add_new'); ?></a>
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
                      _l('ch_date_up'),
                      _l('ch_code_asset'),
                      _l('ch_name_asset'),
                      _l('ch_type_asset'),
                      _l('ch_units_used'),
                      _l('ch_original_price'),
                      _l('ch_value_of_depreciation'),
                      _l('ch_accumulated_depreciation'),
                      _l('ch_residual_value'),
                      _l('ch_time_of_use'),
                      _l('ch_value_of_depreciation_month'),
                      _l('ch_start_date_of_depreciation'),
                      _l('ch_status_depreciation'),
                    );
                    render_datatable($table_data,'record_increased');
                  ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<div id="view_new_record_increased"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
        $(document).on('change', '.units_useds', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var id = $(currentQuantityInput).val();
            if(id == '') {
            }
            else {
                    createTrItem_v2(id,currentQuantityInput);
            }
        });
        $(document).on('change', '.attribution_percentage', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(total > 100)
            {
                $(currentQuantityInput).val(100);
            }
            if(total < 0)
            {
                $(currentQuantityInput).val(100);
            }
        }); 
        $(document).on('keyup', '.attribution_percentage', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(total > 100)
            {
                $(currentQuantityInput).val(100);
            }
            if(total < 0)
            {
                $(currentQuantityInput).val(100);
            }
        });
        $(document).on('click', '.attribution_percentage', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(total > 100)
            {
                $(currentQuantityInput).val(100);
            }
            if(total < 0)
            {
                $(currentQuantityInput).val(100);
            }
        });       
        $(document).on('change', '#units_used', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var id = $(currentQuantityInput).val();
            var items = $('table.item-attribution tbody').find('tr.item');
            if(items.length == 0)
            {
            createTrItemfist_v2(id);
            }
            console.log(id);
            if(items.length == 2)
            {
            $('table.item-attribution tbody').find('tr.item').remove();
            createTrItemfist_v2(id);
            }
        });
        var tAPI;
        $(function(){
            var CustomersServerParams = {
              // 'filterStatus' : '[name="filterStatus"]',
            };
            tAPI =  initDataTableCustom('.table-record_increased', admin_url+'record_increased/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 5, rightColumns: 0});
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
                function roundToTwo(num) {    
            return +(Math.round(num + "e+2")  + "e-2");
        }
        function residual_value() {
            var residual_value = unformat_number($('#accumulated_depreciation').val());
            if(empty(residual_value))
            {
                residual_value = 0;
            }
            var value_of_depreciation = unformat_number($('#value_of_depreciation').val());
            if(empty(value_of_depreciation))
            {
                value_of_depreciation = 0;
            }
            var total = value_of_depreciation - residual_value;
            if(total < 0)
            {
                total = 0;
            }
            $('#residual_value').val(formatNumber(total));
        }
        $(document).on('change', '#value_of_depreciation', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(empty(total))
            {
                $(currentQuantityInput).val(0);
            }
        });
        $(document).on('keyup', '#value_of_depreciation', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = unformat_number($(currentQuantityInput).val());
            residual_value();
            var used_time = $('#used_time').val();
            var total = $('#number_used_time').val();
            var value_of_depreciation = unformat_number($('#value_of_depreciation').val());
            var monthly_depreciation_rate = $('#monthly_depreciation_rate').val();
            var annual_depreciation_rate = $('#annual_depreciation_rate').val();
            var monthly_depreciation_value = 0;
            var yearly_depreciation_value = 0;
           
            monthly_depreciation_value = value_of_depreciation*monthly_depreciation_rate/100;
            yearly_depreciation_value = value_of_depreciation*annual_depreciation_rate/100;
            $('#monthly_depreciation_value').val(formatNumber(Math.ceil(monthly_depreciation_value)));
            $('#yearly_depreciation_value').val(formatNumber(Math.ceil(yearly_depreciation_value)));
        });
        $(document).on('change', '#accumulated_depreciation', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(empty(total))
            {
                $(currentQuantityInput).val(0);
            }
        });
        $(document).on('keyup', '#accumulated_depreciation', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = unformat_number($(currentQuantityInput).val());
            if(empty(total))
            {
                total = 0;
            }
            residual_value();
        }); 
        $(document).on('change', '#original_price', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(empty(total))
            {
                $(currentQuantityInput).val(0);
            }
        });
        $(document).on('keyup', '#original_price', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = unformat_number($(currentQuantityInput).val());
            if(empty(total))
            {
                total = 0;
            }
            $('#value_of_depreciation').val(formatNumber(total));
            residual_value();
        }); 
        $(document).on('change', '#number_used_time', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(empty(total))
            {
                $(currentQuantityInput).val(0);
            }
        }); 
        $(document).on('change', '#monthly_depreciation_rate', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(empty(total))
            {
                $(currentQuantityInput).val(0);
            }
        }); 
        $(document).on('keyup', '#monthly_depreciation_rate', (e)=>{
            var used_time = $('#used_time').val();
            var value_of_depreciation = unformat_number($('#value_of_depreciation').val());
            var monthly_depreciation_rate = 0;
            var annual_depreciation_rate = 0;
            var monthly_depreciation_value = 0;
            var yearly_depreciation_value = 0;
                monthly_depreciation_rate = unformat_number($('#monthly_depreciation_rate').val());
                if(empty(monthly_depreciation_rate))
                {
                    monthly_depreciation_rate = 0;
                }
                annual_depreciation_rate = 12*monthly_depreciation_rate;
                $('#annual_depreciation_rate').val(roundToTwo(annual_depreciation_rate));

            monthly_depreciation_value = value_of_depreciation*monthly_depreciation_rate/100;
            yearly_depreciation_value = value_of_depreciation*annual_depreciation_rate/100;
            $('#monthly_depreciation_value').val(formatNumber(Math.ceil(monthly_depreciation_value)));
            $('#yearly_depreciation_value').val(formatNumber(Math.ceil(yearly_depreciation_value)));
            
        }); 
        $(document).on('change', '#monthly_depreciation_value', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
            var total = $(currentQuantityInput).val();
            if(empty(total))
            {
                $(currentQuantityInput).val(0);
            }
        }); 
        $(document).on('keyup', '#monthly_depreciation_value', (e)=>{

                monthly_depreciation_rate = unformat_number($('#monthly_depreciation_value').val());
                if(empty(monthly_depreciation_rate))
                {
                    monthly_depreciation_rate = 0;
                }
                annual_depreciation_rate = 12*monthly_depreciation_rate;
                $('#yearly_depreciation_value').val((formatNumber(annual_depreciation_rate)));
        });


        $(document).on('change', '#used_time', (e)=>{
            var used_time = $('#used_time').val();
            var total = $('#number_used_time').val();
            var value_of_depreciation = unformat_number($('#value_of_depreciation').val());
            var monthly_depreciation_rate = 0;
            var annual_depreciation_rate = 0;
            var monthly_depreciation_value = 0;
            var yearly_depreciation_value = 0;
            if(empty(total))
            {
                $('#annual_depreciation_rate').val(0);
                $('#monthly_depreciation_rate').val(0);
            }else{
            if(used_time == 1)
            {
                annual_depreciation_rate = 1/total;
                $('#annual_depreciation_rate').val(roundToTwo(annual_depreciation_rate*100));
                monthly_depreciation_rate = annual_depreciation_rate/12;
                $('#monthly_depreciation_rate').val(roundToTwo(monthly_depreciation_rate*100));

            }else if(used_time == 2){
                annual_depreciation_rate = 12/total;
                $('#annual_depreciation_rate').val(roundToTwo(annual_depreciation_rate*100));
                monthly_depreciation_rate = 1/total;
                $('#monthly_depreciation_rate').val(roundToTwo(monthly_depreciation_rate*100));
            }
            monthly_depreciation_value = value_of_depreciation*monthly_depreciation_rate;
            yearly_depreciation_value = value_of_depreciation*annual_depreciation_rate;
            $('#monthly_depreciation_value').val(formatNumber(Math.ceil(monthly_depreciation_value)));
            $('#yearly_depreciation_value').val(formatNumber(Math.ceil(yearly_depreciation_value)));
            }
        }); 
        $(document).on('keyup', '#number_used_time', (e)=>{
            var used_time = $('#used_time').val();
            var total = $('#number_used_time').val();
            var value_of_depreciation = unformat_number($('#value_of_depreciation').val());
            var monthly_depreciation_rate = 0;
            var annual_depreciation_rate = 0;
            var monthly_depreciation_value = 0;
            var yearly_depreciation_value = 0;
            if(empty(total))
            {
                $('#annual_depreciation_rate').val(0);
                $('#monthly_depreciation_rate').val(0);
            }else{
            if(used_time == 1)
            {
                annual_depreciation_rate = 1/total;
                $('#annual_depreciation_rate').val(roundToTwo(annual_depreciation_rate*100));
                monthly_depreciation_rate = annual_depreciation_rate/12;
                $('#monthly_depreciation_rate').val(roundToTwo(monthly_depreciation_rate*100));

            }else if(used_time == 2){
                annual_depreciation_rate = 12/total;
                $('#annual_depreciation_rate').val(roundToTwo(annual_depreciation_rate*100));
                monthly_depreciation_rate = 1/total;
                $('#monthly_depreciation_rate').val(roundToTwo(monthly_depreciation_rate*100));
            }
            monthly_depreciation_value = value_of_depreciation*monthly_depreciation_rate;
            yearly_depreciation_value = value_of_depreciation*annual_depreciation_rate;
            $('#monthly_depreciation_value').val(formatNumber(Math.ceil(monthly_depreciation_value)));
            $('#yearly_depreciation_value').val(formatNumber(Math.ceil(yearly_depreciation_value)));
            }
        });     
        $(document).on('change', '.custom_item_select', (e)=>{
            var currentQuantityInput = $(e.currentTarget);

            var id = $(currentQuantityInput).val();
            if(id == '') {
            }
            else {
                $.post(admin_url + 'record_increased/get_items/'+id,{[csrfData['token_name']] : csrfData['hash']}, function(item){
                    var item = JSON.parse(item);
                    createTrItem(item,currentQuantityInput);
                });
            }
        });

        function edit_record_increase(id) {
            $('#view_new_fixed_assets').html('');
            $.get(admin_url + 'record_increased/edit_record_increased/'+id).done(function (response) {
                $('#view_new_record_increased').html(response);
                $('#record_increased').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }        
        function new_record_reduce() {
            $('#view_new_fixed_assets').html('');
            $.get(admin_url + 'record_increased/add_record_increased/').done(function (response) {
                $('#view_new_record_increased').html(response);
                $('#record_increased').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }
        function view_record_increase(id) {
            $('#view_new_fixed_assets').html('');
            $.get(admin_url + 'record_increased/view_record_increase/'+id).done(function (response) {
                $('#view_new_record_increased').html(response);
                $('#record_increased').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        } 
        $('body').on('hidden.bs.modal', '#record_increased', function() {
            $('#view_new_record_increased').html('');
        });
</script>
