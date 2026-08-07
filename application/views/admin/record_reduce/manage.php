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
                      _l('Số chứng từ'),
                      _l('Ngày ghi tăng'),
                      _l('Mã tài sản'),
                      _l('Tên tài sản'),
                      _l('Loại tài sản'),
                      _l('Đơn vị sử dụng'),
                      _l('Nguyên giá'),
                      _l('Giá trị tính KH'),
                      _l('Hao mòn lũy kế'),
                      _l('Giá trị còn lại'),
                      _l('Thời gian sử dụng (tháng)'),
                      _l('Giá trị KH (tháng)'),
                      _l('Ngày bắt đầu tính KH'),
                      _l('Tình trạng'),
                    );
                    render_datatable($table_data,'record_reduce');
                  ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<div id="view_new_record_reduce"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
        var tAPI;
        $(function(){
            var CustomersServerParams = {
              // 'filterStatus' : '[name="filterStatus"]',
            };
            tAPI =  initDataTableCustom('.table-record_reduce', admin_url+'record_increased/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 5, rightColumns: 0});
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
        function edit_record_reduce(id) {
            $('#view_new_fixed_assets').html('');
            $.get(admin_url + 'record_reduce/edit_record_reduce/'+id).done(function (response) {
                $('#view_new_record_reduce').html(response);
                $('#record_reduce').modal('show');
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
            $.get(admin_url + 'record_reduce/add_record_reduce/').done(function (response) {
                $('#view_new_record_reduce').html(response);
                $('#record_reduce').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }
        $('body').on('hidden.bs.modal', '#record_reduce', function() {
            $('#view_new_record_reduce').html('');
        });
</script>
