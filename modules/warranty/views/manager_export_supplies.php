<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                                          <?=_l('leads_all')?> (<?= total_rows('tblwarranty_export_supplies'); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                          <?=_l('dont_approve')?> (<?= total_rows('tblwarranty_export_supplies', array('status'=>0)); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                          <?=_l('tnh_approved')?> (<?= total_rows('tblwarranty_export_supplies', array('status'=>1)); ?>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <?php render_datatable(array(
                            _l('#'),
                            _l('Mã phiếu xuất'),
                            _l('Mã phiếu bảo hành'),
                            _l('tnh_export_name'),
                            _l('Ngày đề nghị'),
                            _l('date_deadline'),
                            _l('status'),
                            _l('ch_status'),
                            _l('ch_catestaff_create'),
                            _l('ch_option'),
                        ),'warranty_export_supplies'); ?>
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
<div class="load_add_purchases"></div>
<div class="load_add_export_warehouse"></div>
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
    tAPI = initDataTableCustom('.table-warranty_export_supplies', admin_url+'warranty/table_warranty_export_supplies', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
        $('' + filterItem).on('change', function(){
            tAPI.draw('page');
        });
    });
});

function view_warranty_list(id) {
    $('.load_view_warranty_list').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_warranty_list/'+id, data).done(function(response){
        $('.load_view_warranty_list').html(response);
        $('#view_warranty_list').modal({backdrop: 'static', keyboard: false});
        tAPI.draw('page');
    });
}

function view_export_supplies(id) {
    $('.load_view_export_supplies').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_export_supplies_modal/'+id, data).done(function(response){
        $('.load_view_export_supplies').html(response);
        changeRowNew_ch('tblwarranty_export_supplies', id);
        $('#view_export_supplies').modal({backdrop: 'static', keyboard: false});
        tAPI.draw('page');
    });
}

$(document).on('click','.js-status', function (e) {
    var target = $(e.currentTarget);
    var id = target.attr('data-id');
    var status = target.attr('data-status');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/update_status_export_supplies/'+id+'/'+status, data).done(function(response){
        tAPI.draw('page');
        $("[data-toggle='popover']").popover('hide');
    });
});

function add_purchases(id) {
    $('.load_add_purchases').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_add_purchases/'+id, data).done(function(response){
        $('.load_add_purchases').html(response);
        $('#add_purchases_modal').modal({backdrop: 'static', keyboard: false});
    });
}

function add_export_warehouse(id) {
    $('.load_add_export_warehouse').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getView_add_export_warehouse/'+id, data).done(function(response){
        $('.load_add_export_warehouse').html(response);
        $('#add_export_warehouse').modal({backdrop: 'static', keyboard: false});
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
                                <label for="search_date" class="control-label"><?=_l('Ngày đề nghị')?></label>\
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
    ajaxSelect('#search_code', 'admin/warranty/searchCodeWarranty_export_supplies');
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

function delete_export_supplies(id) {
    if (confirm("Bạn có chắc chắn muốn thực hiện thao tác này?")) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'warranty/delete_export_supplies/'+id, data).done(function(response){
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