<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<style>
    .wrap-container-process {
        min-width: 575px;
    }
    .wrap-content-process {
        float: left;
        text-align: center;
        width: 110px;
    }
    .wrap-step-process {
        position: relative;
        width: 10px;
        margin: auto;
        height: 10px;
        background: #7b7b7b;
        border-radius: 50%;
    }
    .wrap-title-process {
        color: #676767;
        font-size: 10px;
    }
    .wrap-user-process {
        color: #676767;
        font-size: 10px;
    }
    .wrap-step-process.line:before {
        content: "";
        position: absolute;
        top: 40%;
        left: 10px;
        width: 110px;
        height: 2px;
        background: #7b7b7b;
    }
    .wrap-content-process.active .wrap-step-process {
        background: #55b776;
    }
    .wrap-content-process.active .wrap-step-process.line:before {
        background: #55b776;
    }
    .table-warranty_list tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 100px;
    }
    .table-warranty_list tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 200px;
    }
    .table-warranty_list tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 100px;
    }
    .table-warranty_list tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 250px;
    }
    .table-warranty_list tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 100px;
    }
    .table-warranty_list tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
    }
    .table-warranty_list thead tr th:nth-child(8){
        background: #ffedcb;
    }
    .table-warranty_list tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 100px;
        background: #ffedcb;
    }
    .table-warranty_list tbody tr td:nth-child(9){
        white-space: inherit;
        min-width: 100px;
    }
    .table-warranty_list tbody tr td:nth-child(10){
        white-space: inherit;
        min-width: 100px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a class="search_person btn btn-info pull-right mright5 H_action_button">
               <?php echo _l('search'); ?>
            </a>
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
                                          <?=_l('leads_all')?> (<?= total_rows('tblwarranty'); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                          <?=_l('dont_approve')?> (<?= total_rows('tblwarranty',array('status'=>0)); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                          <?=_l('tnh_approved')?>  (<?= total_rows('tblwarranty',array('status'=>1)); ?>)
                                        </a>
                                    </li>

                                    <li>
                                        <a class="H_filter" data-id="3">
                                          <?=_l('warranty_process_create')?> (<?= total_rows('tblwarranty'); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                          <?=_l('warranty_process_export_supplies')?> (<?php
                                                                                            $this->db->select('count(tblwarranty.id) as count');
                                                                                            $this->db->where('tblwarranty.id IN (SELECT id_warranty FROM tblwarranty_export_supplies)');
                                                                                            echo $this->db->get('tblwarranty')->row()->count;
                                                                                        ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="5">
                                          <?=_l('warranty_process_purchases')?> (<?php
                                                                                    $this->db->select('count(tblwarranty.id) as count');
                                                                                    $this->db->where('tblwarranty.id IN (SELECT tblwarranty_export_supplies.id_warranty FROM tblwarranty_export_supplies WHERE tblwarranty_export_supplies.id_purchases IS NOT NULL) OR tblwarranty.id IN (SELECT tblwarranty_export_supplies.id_warranty FROM tblwarranty_export_supplies WHERE tblwarranty_export_supplies.id IN (SELECT tblexport_different.id_warranty_export_supplies FROM tblexport_different))');
                                                                                    echo $this->db->get('tblwarranty')->row()->count;
                                                                                ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="6">
                                          <?=_l('warranty_process_export_warehouse')?> (<?php
                                                                                            $this->db->select('count(tblwarranty.id) as count');
                                                                                            $this->db->where('tblwarranty.id IN (SELECT tblwarranty_export_supplies.id_warranty FROM tblwarranty_export_supplies WHERE tblwarranty_export_supplies.id IN (SELECT tblexport_different.id_warranty_export_supplies FROM tblexport_different))');
                                                                                            echo $this->db->get('tblwarranty')->row()->count;
                                                                                        ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="7">
                                          <?=_l('warranty_process_done')?> (<?php
                                                                                $this->db->select('count(tblwarranty.id) as count');
                                                                                $this->db->where('tblwarranty.status_done', 1);
                                                                                echo $this->db->get('tblwarranty')->row()->count;
                                                                            ?>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <table class="table table-warranty_list">
                            <thead>
                                <tr>
                                   <th><?=_l('#')?></th>
                                   <th><?=_l('code_warranty')?></th>
                                   <th><?=_l('Mã tiếp nhận BH')?></th>
                                   <th><?=_l('date_created')?></th>
                                   <th><?=_l('clients')?></th>
                                   <th><?=_l('total_c')?></th>
                                   <th><?=_l('total_v')?></th>
                                   <th><?=_l('total')?></th>
                                   <th><?=_l('status')?></th>
                                   <th><?=_l('cong_create_by')?></th>
                                   <th><div class="text-center"><?=_l('ch_process')?></div></th>
                                   <th><?=_l('evaluate')?></th>
                                   <th><?=_l('ch_option')?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <td colspan="5" style="background: #fff;"><?=_l('total')?></td>
                                <td class="text-right total1"></td>
                                <td class="text-right total2"></td>
                                <td class="text-right total3" style="background: #ffedcb;"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="load_view_warranty_receive"></div>
<div class="load_view_warranty_list"></div>
<div class="load_add_export_supplies"></div>
<div class="load_view_export_supplies"></div>
<div id="evaluate_modal"></div>
<div class="load_additional_supplies"></div>
<div class="load_add_export_warehouse_done"></div>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<?php $this->load->view('count_js')?>
<?php $this->load->view('popup_deadline')?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
$('.H_filter').click(function(e) {
    var target = $(e.currentTarget);
    var value = target.attr('data-id');
    target.parent().parent().find('li').removeClass('active');
    target.parent().addClass('active');
    $('input[name="filterStatus"]').val(value);
    $('input[name="filterStatus"]').change();
});
var tAPI;
$(function(){
    var CustomersServerParams = {
        'filterStatus' : '[name="filterStatus"]',
        'search_date' : '[name="search_date"]',
        'search_code' : '[name="search_code"]',
        'search_client' : '[name="search_client"]',
    };
    tAPI = initDataTableCustom('.table-warranty_list', admin_url+'warranty/table_warranty_list', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 3, rightColumns: 0});
    $.each(CustomersServerParams, function(filterIndex, filterItem){
        $(filterItem).on('change', function(){
            tAPI.draw('page');
        });
    });
});

$('.table-warranty_list').on('draw.dt', function() {
    var paymentReceivedReportsTable = $(this).DataTable();
    var sums = paymentReceivedReportsTable.ajax.json().sums;
    $('.dataTables_scrollFoot').find('table').find('.total1').text(sums.total1);
    $('.dataTables_scrollFoot').find('table').find('.total2').text(sums.total2);
    $('.dataTables_scrollFoot').find('table').find('.total3').text(sums.total3);
});

$(document).on('click','.js-status', function (e) {
    var target = $(e.currentTarget);
    var id = target.attr('data-id');
    var status = target.attr('data-status');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/update_status_list/'+id+'/'+status, data).done(function(response){
        response = JSON.parse(response);
        alert_float(response.alert_type, response.message);
        tAPI.draw('page');
        $("[data-toggle='popover']").popover('hide');
    });
});

function delete_warranty_list(id) {
    if (confirm("Bạn có chắc chắn muốn thực hiện thao tác này?")) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'warranty/delete_warranty_list/'+id, data).done(function(response){
            response = JSON.parse(response);
            alert_float(response.alert_type, response.message);
            tAPI.draw('page');
        });
    }
    else {
        return false;
    }
}

function view_warranty_receive(id) {
    $('.load_view_warranty_receive').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_warranty_receive/'+id, data).done(function(response){
        $('.load_view_warranty_receive').html(response);
        changeRowNew_ch('tblwarranty_receive', id);
        $('#view_warranty_receive').modal({backdrop: 'static', keyboard: false});
    });
}

function view_warranty_list(id) {
    $('.load_view_warranty_list').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_warranty_list/'+id, data).done(function(response){
        $('.load_view_warranty_list').html(response);
        changeRowNew_ch('tblwarranty', id);
        $('#view_warranty_list').modal({backdrop: 'static', keyboard: false});
        tAPI.draw('page');
    });
}

function add_export_supplies(id) {
    $('.load_add_export_supplies').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_export_supplies/'+id, data).done(function(response){
        $('.load_add_export_supplies').html(response);
        $('#add_export_supplies').modal({backdrop: 'static', keyboard: false});
        tAPI.draw('page');
    });
}

var inner_popover_template = '<div class="popover" style="width:1000px;max-width:1500px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
$(document).on('click','.search_person',function(e){
    var dropdown_menu='<div class="col-md-4">\
                            <label for="search_code" class="control-label"><?=_l('ch_code_number')?></label>\
                            <input type="text" id="search_code" class="search_code" name="search_code" style="width: 100%;">\
                        </div>\
                        <div class="col-md-4">\
                            <label for="search_client" class="control-label"><?=_l('clients')?></label>\
                            <input type="text" id="search_client" class="search_client" name="search_client" style="width: 100%;">\
                        </div>\
                        <div class="col-md-4">\
                            <div class="form-group">\
                                <label for="search_date" class="control-label"><?=_l('date_created')?></label>\
                                <div class="input-group">\
                                    <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">\
                                    <div class="input-group-addon">\
                                        <i class="fa fa-calendar calendar-icon"></i>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';

    $(this).popover({
        html: true,
        container: 'body',
        placement: "bottom",
        trigger: 'click focus',
        title:'<?=_l('search')?><button type="button" class="close">&times;</button>',
        content: function() {
            return dropdown_menu;
        },
        template: inner_popover_template
    });
    search_daterangepicker();
    ajaxSelect('#search_code', 'admin/warranty/searchCodeWarranty');
    ajaxSelect('#search_client', 'admin/warranty/searchClientWarranty');
});

$(document).on('click','.close',function(e){
   $('.search_person').popover('hide');
});

function ajaxSelect(element, url, id)
{
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
                    term: term,
                    limit: 5000
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

var search_daterangepicker = () => {
    $('input[name="search_date"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false, 
        isInvalidDate: false,
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
    $('input[name="search_date"]').val('').datepicker("refresh");
    $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        tAPI.draw('page');
    });
    $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        tAPI.draw('page');
    });
};

$(document).on('change','#search_code', function (e) {
    tAPI.draw('page');
});

$(document).on('change','#search_client', function (e) {
    tAPI.draw('page');
});

function view_export_supplies(id) {
    $('.load_view_export_supplies').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_export_supplies_modal/'+id, data).done(function(response){
        $('.load_view_export_supplies').html(response);
        $('#view_export_supplies').modal({backdrop: 'static', keyboard: false});
        tAPI.draw('page');
    });
}

function update_process_done(id, type_localtion_warranty) {
    if(type_localtion_warranty == 1) {
        Swal.fire({
            title: 'HOÀN THÀNH PHIẾU BẢO HÀNH (TẠI CÔNG TY)',
            html: '<div style="width: 410px; margin: auto; text-align: left;">\
                        <div class="radio radio-primary">\
                            <input type="radio" name="status_done" value="1" checked>\
                            <label for="single"><?=_l('Xuất kho trả khách')?></label>\
                        </div>\
                        <div class="radio radio-primary">\
                            <input type="radio" name="status_done" value="2">\
                            <label for="single"><?=_l('Thu hồi sản phẩm')?></label>\
                        </div>\
                    </div>',
            type: "success",
            width: 600,
            showCancelButton: true,
            cancelButtonColor: '#DD6B55',
            cancelButtonText: "Đóng",
            confirmButtonColor: '#2ec48c',
            confirmButtonText: 'Hoàn Thành',
            closeOnConfirm: false,
            closeOnCancel: false
        }).then((result) => {
            if (result.value) {
                var type_status_done = $('input[name="status_done"]:checked').val();
                if(type_status_done == 1) {
                    $('.load_add_export_warehouse_done').html('');
                    var data = {};
                    if (typeof(csrfData) !== 'undefined') {
                      data[csrfData['token_name']] = csrfData['hash'];
                    }
                    $.post(admin_url+'warranty/getView_add_export_warehouse_done/'+id+'/'+type_status_done, data).done(function(response){
                        $('.load_add_export_warehouse_done').html(response);
                        $('#add_export_warehouse_done').modal({backdrop: 'static', keyboard: false});
                    });
                }
                else if(type_status_done == 2) {
                    $('.load_add_export_warehouse_done').html('');
                    var data = {};
                    if (typeof(csrfData) !== 'undefined') {
                      data[csrfData['token_name']] = csrfData['hash'];
                    }
                    // $.post(admin_url+'warranty/getView_add_transfer_warehouse_done/'+id+'/'+type_status_done, data).done(function(response){
                    //     $('.load_add_export_warehouse_done').html(response);
                    //     $('#add_transfer_warehouse_done').modal({backdrop: 'static', keyboard: false});
                    // });
                    $.post(admin_url+'warranty/add_transfer_warehouse_done/'+id+'/'+type_status_done, data).done(function(response){
                        response = JSON.parse(response);
                        alert_float(response.alert_type, response.message);
                        tAPI.draw('page');
                    });
                }
            }
        });
    }
    else {
        Swal.fire({
            title: 'HOÀN THÀNH PHIẾU BẢO HÀNH (TẠI KHÁCH HÀNG)',
            type: "success",
            width: 600,
            showCancelButton: true,
            cancelButtonColor: '#DD6B55',
            cancelButtonText: "Đóng",
            confirmButtonColor: '#2ec48c',
            confirmButtonText: 'Hoàn Thành',
            closeOnConfirm: false,
            closeOnCancel: false
        }).then((result) => {
            if (result.value) {
                var data = {};
                if (typeof(csrfData) !== 'undefined') {
                  data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(admin_url+'warranty/add_warehouse_done_by_customer/'+id, data).done(function(response){
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    tAPI.draw('page');
                });
            }
        });
    }
}

$(document).on('click','.wap-icon',function(e){
    var target = $(e.currentTarget);
    $('.wap-icon').removeClass('active');
    target.addClass('active');
    var points = target.attr('data-points');
    $('.points').val(points);
});

function add_evaluate(id) {
    $('#evaluate_modal').html('');
    dataString={[csrfData['token_name']] : csrfData['hash']};
    jQuery.ajax({
        type: "post",
        url: "<?=admin_url()?>warranty/evaluate_modal",
        data: dataString,
        cache: false,
        success: function (data) {
            $('#evaluate_modal').html(data);

            $('.add_title_evaluate').removeClass('hide');
            $('.edit_title_evaluate').addClass('hide');
            $('#evaluate_form').attr("action","<?=admin_url('warranty/add_evaluate/')?>"+id);
            $('#evaluate_modal_data').modal({show:true,backdrop:'static'});
        }
    });
}

function edit_evaluate(id) {
    $('#evaluate_modal').html('');
    dataString={[csrfData['token_name']] : csrfData['hash']};
    jQuery.ajax({
        type: "post",
        url: "<?=admin_url()?>warranty/evaluate_modal/"+id,
        data: dataString,
        cache: false,
        success: function (data) {
            $('#evaluate_modal').html(data);

            $('.add_title_evaluate').addClass('hide');
            $('.edit_title_evaluate').removeClass('hide');
            $('#evaluate_form').attr("action","<?=admin_url('warranty/edit_evaluate/')?>"+id);
            $('#evaluate_modal_data').modal({show:true,backdrop:'static'});
        }
    });
}

function additional_supplies(id_warranty) {
    $('.load_additional_supplies').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_additional_supplies/'+id_warranty, data).done(function(response){
        $('.load_additional_supplies').html(response);
        $('#additional_supplies').modal({backdrop: 'static', keyboard: false});
    });
}
</script>