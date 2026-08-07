<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-coupon_support img {
        height: 20px;
        width: 20px;
    }
    .table-coupon_support tr td:nth-child(2){
        min-width: 100px;
        white-space: unset;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info H_action_button" onclick="add(); return false;">
                    <?php echo _l('create_add_new'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                          <?=_l('leads_all')?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                          <?=_l('Sắp đến hạn')?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                          <?=_l('Đến hạn')?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                          <?=_l('Quá hạn')?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                          <?=_l('Hoàn thành')?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <?php render_datatable(array(
                            _l('#'),
                            _l('ch_code_number'),
                            _l('clients'),
                            _l('coupon_support_date'),
                            _l('method1'),
                            _l('method2'),
                            _l('method3'),
                            _l('tnh_employees_charge'),
                            _l('note'),
                            _l('ch_option'),
                        ),'coupon_support'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="view_add"></div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
var tAPI;

$('.H_filter').click(function(e) {
    var target = $(e.currentTarget);
    var value = target.attr('data-id');
    target.parent().parent().find('li').removeClass('active');
    target.parent().addClass('active');
    $('input[name="filterStatus"]').val(value);
    $('input[name="filterStatus"]').change();
});

$(function(){
    var CustomersServerParams = {
        'filterStatus' : '[name="filterStatus"]',
    };
    tAPI = initDataTableCustom('.table-coupon_support', admin_url+'coupon_support/table_coupon_support', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
        $('' + filterItem).on('change', function(){
            tAPI.draw('page');
        });
    });
});

function add() {
    $('.view_add').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'coupon_support/getView_add', data).done(function(response){
        $('.view_add').html(response);
        var opt = {
            format: 'd/m/Y H:i',
            timepicker: true,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        $('#appointment_date').datetimepicker(opt);
        $('#add_coupon_support').modal({backdrop: 'static', keyboard: false});
    });
}

function edit(id) {
    $('.view_add').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'coupon_support/getView_edit/'+id, data).done(function(response){
        $('.view_add').html(response);
        var opt = {
            format: 'd/m/Y H:i',
            timepicker: true,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        $('#appointment_date').datetimepicker(opt);
        $('#edit_coupon_support').modal({backdrop: 'static', keyboard: false});
    });
}

$(document).on('change','.checkbox_method', function (e) {
    var target = $(e.currentTarget);
    var change_id = target.attr('data-id');
    var val = target.attr('value');

    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['id'] = change_id;
    data['type'] = val;
    $.post(admin_url+'coupon_support/change_type', data).done(function(response){
        tAPI.draw('page');
    });
});

function change_status(id) {
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'coupon_support/change_status/'+id, data).done(function(response){
        response = JSON.parse(response);
        alert_float(response.alert_type, response.message);
        tAPI.draw('page');
    });
}

function delete_coupon_support(id) {
    if (confirm("Bạn có chắc chắn muốn thực hiện thao tác này?")) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'coupon_support/delete_coupon_support/'+id, data).done(function(response){
            response = JSON.parse(response);
            alert_float(response.alert_type, response.message);
            tAPI.draw('page');
        });
    }
    else {
        return false;
    }
}
</script>
</body>
</html>