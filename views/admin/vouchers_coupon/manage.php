<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-vouchers_coupon img {
        width: 22px;
        height: 22px;
    }
    .table-vouchers_coupon tr td:nth-child(1)
    {
                min-width: 100px;
                text-align: center;
                white-space: unset;
    }
    .table-vouchers_coupon tr td:nth-child(2)
    {
                min-width: 100px;
                text-align: center;
                white-space: unset;
    }
    .table-vouchers_coupon tr td:nth-child(7)
    {
                min-width: 120px;
                text-align: right;
                white-space: unset;
    }
    .table-vouchers_coupon tr td:nth-child(8)
    {
                min-width: 120px;
                text-align: right;
                white-space: unset;
    }
    .table-vouchers_coupon tr td:nth-child(9)
    {
                min-width: 120px;
                white-space: unset;
    }
    .table-vouchers_coupon tbody tr td:nth-child(9) {
      white-space: inherit;
      min-width: 170px;
    }
    .table-vouchers_coupon tbody .dropdown {
      text-align: center;
    }
</style>
<style type="text/css">
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?=$title?></span>
                <?php if (has_permission('vouchers_coupon','','create')) { ?>
                <div class="pull-right mright5 H_border">
                    <a class="btn btn-info H_action_button" onclick="add(); return false;">
                        <?php echo _l('create_add_new'); ?>
                    </a>
                    <!-- sum note -->
                    <a class="btn btn-info pull-right mleft5 H_action_button option_barcode" data-toggle="collapse" data-target="#searchStatistics" aria-expanded="true">
                        <i class="fa fa-filter"></i>
                        <?= lang('tnh_seach_statistical') ?>
                    </a>
                    <!-- ./sum note -->
                </div>
                <?php }?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
        <!-- sum note -->
            <div class="col-md-12">
                <div id="searchStatistics" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-4">
                        <?= lang('client_lowercase', 'business_plan_search') ?>
                        <input type="text" name="clients_id" data-placeholder="<?= lang('client_lowercase') ?>" id="clients_id" class="business_plan_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-4">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-4">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
        <!-- ./sum note -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <?php if(has_permission('vouchers_coupon','','view_own')&&!is_admin())
                                { ?>
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                          <?=_l('leads_all')?>(<?=total_rows('tblvouchers_coupon',array('staff'=>get_staff_user_id()));?>)
                                        </a>
                                    </li>
                                </ul>
                                <?php }else{?>
                                 <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                          <?=_l('leads_all')?>(<?=total_rows('tblvouchers_coupon');?>)
                                        </a>
                                    </li>
                                </ul>   
                                <?php }?>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <div class="clearfix mtop20"></div>
                            <?php $table_data = array(
                                _l('ch_code_p'),
                                _l('ch_date_p'),
                                _l('client'),
                                _l('cong_code_orders'),
                                _l('staff_coupon'),
                                _l('acs_sales_payment_modes_submenu'),
                                // _l('ch_total_total'),
                                _l('ch_total_payment'),
                                _l('note'),
                                _l('ch_option'),
                            );
                            render_datatable_tfoot_ch($table_data,'vouchers_coupon');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal hóa đơn -->
<div id="modal_payment"></div>
<!-- end -->
<!-- modal view -->
<div id="modal_view"></div>
<!-- end -->
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
var tAPIs;
var base_url = "<?=base_url();?>";
//////// lam tiep bo loc theo khách hàng
function ajaxSelectParamsv1(element, url, id, params = false, clearSl2 = false)
    {
    console.log(clearSl2);
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                        if (data.row) {
                            if (data.row.id === 0) {
                                $(element).val(0);
                            }
                        }
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        params: params,
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
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: true,
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        params: params,
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
//////// ./lam tiep bo loc theo khách hàng
$(function(){
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    var CustomersServerParams = {
      'filterStatus' : '[name="filterStatus"]',
    //   sum note
      'clients_id' : '[name="clients_id"]',
      'start_date_search' : '[name="start_date_search"]',
      'end_date_search' : '[name="end_date_search"]',
    //   ./sum note

    };
    tAPIs = initDataTableCustom('.table-vouchers_coupon', admin_url+'vouchers_coupon/table', [0], [0], CustomersServerParams,[], fixedColumns = {leftColumns: 2, rightColumns: 1});
    $.each(CustomersServerParams, function(filterIndex, filterItem){
      $('' + filterItem).on('change', function(){
        tAPIs.draw('page');
      });
    });
});
$('.table-vouchers_coupon').on('draw.dt', function() {
    var itemsTable = $(this).DataTable();
    var sums = itemsTable.ajax.json().sums;
    $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
    $('.DTFC_LeftFootWrapper').css("background","#ffff");
    $('.dataTables_scrollFoot').find('tfoot td').eq(5).html('<div class="text-right">'+sums.total+'</div>');   
    $('.dataTables_scrollFoot').find('tfoot td').eq(6).html('<div class="text-right">'+sums.pay+'</div>');    
});
$('body').on('hidden.bs.modal', '#payment_vouchers_coupon', function() {
    $('#modal_payment').html('');
    tAPIs.draw('page');
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

function add() {
    $('#modal_payment').html('');
    $.get(admin_url + 'vouchers_coupon/modal').done(function(response) {
        $('#modal_payment').html(response);
        $('#payment_vouchers_coupon').modal({backdrop: 'static', keyboard: false});

        var opt = {
            format: 'd/m/Y',
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        $('#date_vouchers').datetimepicker(opt);

        $('select[name="code_orders[]"]').selectpicker('refresh');
        $('select[name="staff"]').selectpicker('refresh');
        $('select[name="payment_mode"]').selectpicker('refresh');
    });
}

var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>'; 
$(document).on('click','.popover-menu',function(e){
    $(this).popover({
     html: true,
     placement: "right",
     trigger: 'click',
     title:'<?php echo _l('coupon'); ?> <span class="pull-right text-danger close_popover"><i class="fa fa-times"></i></span>',
     content: function() {
       return $(this).find('.content_code_orders').html();
     },
     template: inner_popover_template
   });
});

$(document).on('click', '.close_popover',function (e) {
    $('.popover-menu').popover('hide');
});

function delete_vouchers_coupon(id) {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
            if (r == false) {
                return false;
            } else {
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'vouchers_coupon/delete_vouchers_coupon/'+id, data).done(function(response){
        response = JSON.parse(response);
        alert_float(response.alert_type, response.message);
        tAPIs.draw('page');
    });
    }
    return false;
}

function var_status(status,id) {
    dataString = {id:id, status:status, [csrfData['token_name']] : csrfData['hash']};
    jQuery.ajax({   
        type: "post",
        url:"<?=admin_url()?>vouchers_coupon/update_status",
        data: dataString,
        cache: false,
        success: function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                tAPIs.draw('page');
                alert_float('success', response.message);
            }
        }
    });
    return false;
}
$(document).on('change','select[name="code_orders[]"]', function (e) {
    var total = 0;
    if($(this).val().length > 0) {
        $.each($(this).val(), function(i, v){
            if($('select[name="code_orders[]').find('option[value='+v+']').length > 0) {
                total += Number(unformat_number($('select[name="code_orders[]').find('option[value='+v+']').attr('data-subtext')));
            }
        });
        $('#total').val(formatNumber(total));
    }
    else {
        $('#total').val(formatNumber(total));    }
});
function view_vouchers_coupon(id) {
    $('#modal_view').html('');
    $.get(admin_url + 'vouchers_coupon/view/'+id).done(function(response) {
        $('#modal_view').html(response);
        $('#view_vouchers_coupon').modal({backdrop: 'static', keyboard: false});
    });
}

<?php if($this->input->get('modal') == true) { ?>
$( document ).ready(function() {
    add();
});
<?php } ?>
$( document ).ready(function() {
    ajaxSelectParamsv1('#clients_id', 'admin/clients/searchCustomers', 0, true, true);
});
</script>
