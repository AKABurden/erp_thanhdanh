<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .wrap_btn_add_series {
        position: relative;
        z-index: 3;
    }
    .wrap_btn_add_series_submit {
        position: relative;
        z-index: 3;
        top: -30px;
    }
    .wrap_form_series {
        position: relative;
        display: flex;
        justify-content: center;
        top: -10px;
        z-index: 2;
    }
    .wrap_content_series {
        overflow: hidden;
        height: 0px;
        width: 60%;
        border: 1px solid #c1c1c1;
        transition: all 0.3s;
    }
    .btn_add_series.active {
        background: #a0a0a0 !important;
        border: 1px solid #a0a0a0 !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a class="search_person btn btn-info pull-right mleft5 H_action_button">
               <?php echo _l('search'); ?>
            </a>
            <a class="btn btn-info pull-right H_action_button" onclick="add(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('create_add_new'); ?>
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
                                          <?=_l('leads_all')?> (<?= total_rows('tblwarranty_receive'); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                          <?=_l('dont_approve')?> (<?= total_rows('tblwarranty_receive',array('status'=>0)); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                          <?=_l('tnh_approved')?> (<?= total_rows('tblwarranty_receive',array('status'=>1)); ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                          <?=_l('Chưa xử lý')?> (<?php
                                                                    $this->db->select('count(tblwarranty_receive.id) as count');
                                                                    $this->db->where('tblwarranty_receive.id NOT IN (SELECT id_warranty_receive FROM tblwarranty)');
                                                                    echo $this->db->get('tblwarranty_receive')->row()->count;
                                                                ?>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                          <?=_l('Đã tạo phiếu bảo hành')?> (<?php
                                                                                $this->db->select('count(tblwarranty_receive.id) as count');
                                                                                $this->db->where('tblwarranty_receive.id IN (SELECT id_warranty_receive FROM tblwarranty)');
                                                                                echo $this->db->get('tblwarranty_receive')->row()->count;
                                                                            ?>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <?php render_datatable(array(
                            _l('#'),
                            _l('code_warranty'),
                            _l('date_machine'),
                            _l('clients'),
                            _l('name_of_machine'),
                            _l('service_type'),
                            _l('status'),
                            _l('ch_status'),
                            _l('leads_dt_assigned'),
                            _l('ch_option'),
                        ),'warranty'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="warranty_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('add_warranty'); ?></span>
                    <span class="edit-title"><?php echo _l('edit_warranty'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/warranty/add',array('id'=>'warranty-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="tnh-tb table-bordered table-hover m-group0" style="table-layout: fixed;">
                            <tbody>
                                <tr>
                                    <td style="width: 15%">
                                        <label for="code" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('code_warranty'); ?>
                                        </label>
                                    </td>
                                    <td style="width: 30%">
                                        <?php echo render_input('code',''); ?>
                                    </td>
                                    <td style="width: 15%">
                                        <label for="customer_id" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('clients'); ?>
                                        </label>
                                    </td>
                                    <td style="width: 40%">
                                        <div class="form-group">
                                            <input type="text" name="customer_id" data-placeholder="<?= lang('customers') ?>" id="customer_id" class="customer_id" style="width: 100%;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="date" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('date_machine'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo render_datetime_input('date',''); ?>
                                    </td>
                                    <td>
                                        <label for="name_of_machine" class="control-label">
                                            <?php echo _l('name_of_machine'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="name_of_machine" data-placeholder="<?= lang('contact_name') ?>" id="name_of_machine" class="name_of_machine" style="width: 100%;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="service_type" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('service_type'); ?>
                                        </label>
                                    </td>
                                    <td colspan="3">
                                        <?php echo render_input('service_type',''); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="col-md-12 text-center">
                        <div class="wrap_btn_add_series">
                            <a class="btn btn-info btn_add_series"><?= _l('add_series_new') ?></a>
                        </div>
                        <div class="wrap_form_series">
                            <div class="wrap_content_series">
                                <div class="col-md-12" style="padding-top: 20px;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="number_series" class="control-label" style="float: left;">Số series</label>
                                        <input type="text" class="form-control" name="number_series">
                                        <span class="pull-left text-danger error_series"></span>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 20px; padding-bottom: 20px;">
                                    <div class="js-width">
                                        <label for="product_by_series" class="control-label" style="float: left;">Thành phẩm</label>
                                        <div class="clearfix"></div>
                                        <div class="input-group">
                                            <div class="form-group" style="margin-bottom: 0;">
                                                <input type="text" name="product_by_series" class="product_by_series" id="product_by_series" style="width: 100%;">
                                            </div>
                                            <span class="input-group-addon pointer btn_add_product">
                                                <i class="fa fa-plus"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <!-- form add product -->
                                    <div class="form_product hide">
                                        <hr>
                                        <div class="form-group">
                                            <label for="category_product" class="control-label" style="float: left;"><?= _l('category') ?></label>
                                            <select name="category_product" id="category_product" data-placeholder="<?= lang('tnh_category_product') ?>" class="modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?= recursiveCategoryProducts() ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <?php $units = get_table_where('tblunits') ?>
                                            <label for="unit_product" class="control-label" style="float: left;"><?= _l('unit') ?></label>
                                            <select name="unit_product" id="unit_product" data-placeholder="<?= lang('unit') ?>" class="modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php foreach ($units as $key => $value): ?>
                                                    <option value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="code_product" class="control-label" style="float: left;">Mã thành phẩm</label>
                                            <input type="text" class="form-control" name="code_product">
                                        </div>
                                        <div>
                                            <label for="name_product" class="control-label" style="float: left;">Tên thành phẩm</label>
                                            <div class="clearfix"></div>
                                            <div class="input-group">
                                                <div class="form-group" style="margin-bottom: 0;">
                                                    <input type="text" class="form-control" name="name_product">
                                                </div>
                                                <span class="input-group-addon pointer add_submit_product">
                                                    <i class="fa fa-check text-success"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end -->
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="wrap_btn_add_series_submit hide">
                            <a class="btn btn-info btn_add_series_submit"><?= _l('submit') ?></a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="col-md-12">
                        <table class="tnh-tb table-item-series table-bordered table-hover m-group0" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="text-center">
                                        <a class="btn btn-primary addTrItem"><i class="fa fa-plus"></i></a>
                                    </th>
                                    <th style="width: 20%;" class="text-center"><?=_l('series')?></th>
                                    <th style="width: 10%;" class="text-center"><?=_l('image')?></th>
                                    <th style="width: 20%;" class="text-center"><?=_l('tnh_product_name')?></th>
                                    <th style="width: 15%;" class="text-center"><?=_l('count_warranty')?></th>
                                    <th style="width: 15%;" class="text-center"><?=_l('Thời hạn bảo hành còn lại')?></th>
                                    <th style="width: 10%;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="load_view_warranty_receive"></div>
<div class="load_view_warranty_list"></div>
<div class="load_view_export_supplies"></div>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<?php $this->load->view('count_js')?>
<?php $this->load->view('popup_deadline')?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
$(document).ready(function() {
    ajaxSelectCustomerFormatTableCallBack('#customer_id', 'admin/clients/searchCustomers', $('#customer_id').val());
    ajaxSelectSeries('#name_of_machine', 'admin/warranty/searchContact');
    $('#category_product').select2({'allowClear': true});
    $('#unit_product').select2({'allowClear': true});
});

function ajaxSelectCustomerFormatTableCallBack(element, url, id)
{
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatCustomer,
            escapeMarkup: function(m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatCustomer,
            // formatSelection: formatTable,
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

function ajaxSelectSeries(element, url, id)
{
    var customer_id = $('#customer_id').val();
    if (id)
    {
        $(element).val(id).select2({
            width: 'resolve',
            escapeMarkup: function(m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        customer_id: customer_id,
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
            }
        });
    } else {
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
                        customer_id: customer_id,
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
        'search_date' : '[name="date_machine"]',
        'search_code' : '[name="search_code"]',
        'search_client' : '[name="search_client"]',
    };
    tAPI = initDataTableCustom('.table-warranty', admin_url+'warranty/table_warranty', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
        $('' + filterItem).on('change', function(){
            tAPI.draw('page');
        });
    });
});

$(document).on('click','.js-status', function (e) {
    var target = $(e.currentTarget);
    var id = target.attr('data-id');
    var status = target.attr('data-status');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/update_status/'+id+'/'+status, data).done(function(response){
        response = JSON.parse(response);
        alert_float(response.alert_type, response.message);
        tAPI.draw('page');
        $("[data-toggle='popover']").popover('hide');
    });
});

function resetSTT() {
    var countSTT = $('.table-item-series').find('tbody tr');
    var stt = 1;
    $.each(countSTT, function(i, v){
        $(v).find('.stt').text(stt);
        stt++;
    });
}

var unique = 0;
$(document).on('click','.addTrItem', function (e) {
    var target = $(e.currentTarget);

    var allTr = $('.table-item-series').find('tbody tr');
    var checkAddTr = 0;
    $.each(allTr, function(i, v){
        if(!$(v).find('input.select_series').val()) {
            checkAddTr++;
        }
    });
    if(checkAddTr > 0) {
        return;
    }

    var html = '<tr class="trMain" data-stt="'+unique+'">\
                    <td class="text-center stt"></td>\
                    <td class="text-center">\
                        <input type="text" class="select_series" name="select_series['+unique+'][id_series]" data-placeholder="<?= lang('series') ?>" style="width: 100%;">\
                    </td>\
                    <td class="text-center img_item"></td>\
                    <td class="text-left name_item"></td>\
                    <td class="text-left count_warranty"></td>\
                    <td class="text-center deadline_item"></td>\
                    <td class="text-center">\
                        <a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a>\
                    </td>\
                </tr>';
    target.parents('.table-item-series').find('tbody').append(html);
    ajaxSelectSeries('input[name="select_series['+unique+'][id_series]"]', 'admin/warranty/searchSeries');
    resetSTT();
    unique++;
});

$(document).on('change','#customer_id', function (e) {
    var trMain = $('.table-item-series').find('tbody tr');
    $.each(trMain, function(i, v){
        $(v).remove();
    });
    $('.addTrItem').trigger('click');
    ajaxSelectSeries('#name_of_machine', 'admin/warranty/searchContact');
});

$(document).on('change','.select_series', function (e) {
    if(!$('#customer_id').val()) {
        alert_float('danger','<?=_l('tnh_please_chosen_customer')?>');
        return;
    }
    var target = $(e.currentTarget);
    var trMain = $('.table-item-series').find('tbody tr');
    var checkExists = 0;
    $.each(trMain, function(i, v){
        var id = $(v).attr('data-stt');
        if(target.val() == $(v).find('input[name="select_series['+id+'][id_series]"]').val()) {
            checkExists++;
        }
    });

    //checkExists == 1 : chính nó
    if(checkExists == 2) {
        alert_float('danger','<?=_l('series_exists')?>');
        target.parents('tr').find('.img_item').html('');
        target.parents('tr').find('.name_item').html('');
        target.parents('tr').find('.count_warranty').html('');
        target.parents('tr').find('.deadline_item').html('');
        target.parents('tr').find('.select_series').val('');
        return;
    }

    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['id'] = target.val();
    $.post(admin_url+'warranty/getDetail', data).done(function(response){
        response = JSON.parse(response);
        target.parents('tr').find('.img_item').html(response.img_item);
        target.parents('tr').find('.name_item').html(response.name_item);
        target.parents('tr').find('.deadline_item').html(response.deadline_warranty);
        target.parents('tr').find('.count_warranty').html(response.strCount);

        $('.addTrItem').trigger('click');
    });
});

$(document).on('click','.deleteTrItem', function (e) {
    var target = $(e.currentTarget);
    target.parents('tr').remove();
    resetSTT();
});

function add() {
    $('.add-title').removeClass('hide');
    $('.edit-title').addClass('hide');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getCode', data).done(function(response){
        response = JSON.parse(response);
        $('#code').val(response.code);
        $('#date').val(response.date);
        $("#customer_id").select2("val", "");
        $('#name_of_machine').val('');
        $('#service_type').val('');
        $('.table-item-series').find('tbody').html('');
        $('#warranty-modal').attr("action","<?=admin_url('warranty/add')?>");
        $('#warranty_modal').modal({backdrop: 'static', keyboard: false});
        $('.addTrItem').trigger('click');
        ajaxSelectProduct('#product_by_series', 'admin/warranty/searchProductWarranty');
        
        $('.js-width').removeClass('hide');
        $('.form_product').addClass('hide');
        $('.btn_add_series').removeClass('active');
        $('.wrap_btn_add_series_submit').addClass('hide');
        $('.wrap_content_series').css({'height':'0px', 'overflow':'hidden'});

        $('input[name="number_series"]').val('');
        $('select[name="category_product"]').select2('val','');
        $('select[name="unit_product"]').select2('val','');
        $('input[name="code_product"]').val('');
        $('input[name="name_product"]').val('');
    });
}

function edit(id) {
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/getDataEdit/'+id, data).done(function(response){
        response = JSON.parse(response);
        $.each(response.results, function(i, v){
            $('#code').val(v.code);
            $('#date').val(v.date);
            ajaxSelectCustomerFormatTableCallBack('#customer_id', 'admin/clients/searchCustomers', v.customer_id);
            ajaxSelectSeries('#name_of_machine', 'admin/warranty/searchContact', v.name_of_machine);
            $('#service_type').val(v.service_type);
            $('.table-item-series').find('tbody').html('');
            $.each(v.seriesItem, function(i_seriesItem, v_seriesItem){
                var html = '<tr class="trMain" data-stt="'+unique+'">\
                                <td class="text-center stt"></td>\
                                <td class="text-center">\
                                    <input type="text" class="select_series" name="select_series['+unique+'][id_series]" data-placeholder="<?= lang('series') ?>" style="width: 100%;">\
                                </td>\
                                <td class="text-center img_item">'+v_seriesItem.img_item+'</td>\
                                <td class="text-left name_item">'+v_seriesItem.name_item+'</td>\
                                <td class="text-left count_warranty">'+v_seriesItem.strCount+'</td>\
                                <td class="text-center deadline_item">'+v_seriesItem.deadline_warranty+'</td>\
                                <td class="text-center">\
                                    <a class="btn btn-danger deleteTrItem"><i class="fa fa-times"></i></a>\
                                </td>\
                            </tr>';
                $('.table-item-series').find('tbody').append(html);
                ajaxSelectSeries('input[name="select_series['+unique+'][id_series]"]', 'admin/warranty/searchSeries', v_seriesItem.id_series);
                resetSTT();
                unique++;
            });
            $('#warranty-modal').attr("action","<?=admin_url('warranty/add/')?>"+id);
            $('#warranty_modal').modal({backdrop: 'static', keyboard: false});
        });
    });
    ajaxSelectProduct('#product_by_series', 'admin/warranty/searchProductWarranty');

    $('.js-width').removeClass('hide');
    $('.form_product').addClass('hide');
    $('.btn_add_series').removeClass('active');
    $('.wrap_btn_add_series_submit').addClass('hide');
    $('.wrap_content_series').css({'height':'0px', 'overflow':'hidden'});

    $('input[name="number_series"]').val('');
    $('select[name="category_product"]').select2('val','');
    $('select[name="unit_product"]').select2('val','');
    $('input[name="code_product"]').val('');
    $('input[name="name_product"]').val('');
}

appValidateForm($('#warranty-modal'), {code: 'required', date: 'required', customer_id: 'required', service_type: 'required'}, manage_warranty);
function manage_warranty(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float(response.alert_type, response.message);
            tAPI.draw('page');
            $('#warranty_modal').modal('hide');
        }
        else if (response.success == false) {
            alert_float(response.alert_type, response.message);
        }
    });
    return false;
}

function delete_warranty(id) {
    if (confirm("Bạn có chắc chắn muốn thực hiện thao tác này?")) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'warranty/delete_warranty/'+id, data).done(function(response){
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
                                <label for="date_machine" class="control-label"><?=_l('date_machine')?></label>\
                                <div class="input-group">\
                                    <input type="text" id="date_machine" name="date_machine" class="form-control date_machine" aria-invalid="false">\
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
    ajaxSelect('#search_code', 'admin/warranty/searchCodeWarranty_receive');
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
    $('input[name="date_machine"]').daterangepicker({
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
    $('input[name="date_machine"]').val('').datepicker("refresh");
    $('input[name="date_machine"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        tAPI.draw('page');
    });
    $('input[name="date_machine"]').on('cancel.daterangepicker', function(ev, picker) {
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

$(document).on('click','.btn_add_series', function (e) {
    var target = $(e.currentTarget);
    if(target.hasClass('active')) {
        target.removeClass('active');
        $('.wrap_btn_add_series_submit').addClass('hide');
        $('.wrap_content_series').css({'height':'0px', 'overflow':'hidden'});
    }
    else {
        target.addClass('active');
        $('.wrap_content_series').css({'height':'465px', 'overflow':'inherit'});
        $('.wrap_btn_add_series_submit').removeClass('hide');
        rewidth($('input[name="product_by_series"]'));
    }
});

$(document).on('click','.btn_add_series_submit', function (e) {
    var customer_id = $('input[name="customer_id"]').val();
    var number_series = $('input[name="number_series"]').val();
    var product_by_series = $('input[name="product_by_series"]').val();
    if(!customer_id || customer_id == '') {
        alert_float('danger', 'Vui lòng chọn khách hàng!');
        return;
    }
    if(!number_series || number_series == '') {
        alert_float('danger', 'Vui lòng nhập số series!');
        return;
    }
    if(!product_by_series || product_by_series == '') {
        alert_float('danger', 'Vui lòng chọn thành phẩm!');
        return;
    }

    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['customer_id'] = customer_id;
    data['number_series'] = number_series;
    data['product_by_series'] = product_by_series;
    $.post(admin_url+'warranty/add_series', data).done(function(response){
        response = JSON.parse(response);
        if(response.type_error == 1) {
            $('input[name="number_series"]').css({'border':'1px solid #f00 !important;', 'color':'#f00'});
            $('.error_series').text('* Số series bị trùng!');
        }
        else if(response.type_error == 2) {
            alert_float('success', 'Thêm thành công sản phẩm mang số [SERIES: '+number_series+']');
            ajaxSelectProduct('#product_by_series', 'admin/warranty/searchProductWarranty');
            $('input[name="number_series"]').val('');
            $('.btn_add_series').trigger('click');
        }
    });
});

$(document).on('keyup','input[name="number_series"]', function (e) {
    $('input[name="number_series"]').css({'border':'1px solid #444444;', 'color':'#444444'});
    $('.error_series').text('');
});

$(document).on('click','.btn_add_product', function (e) {
    var target = $(e.currentTarget);
    $('.js-width').addClass('hide');
    $('.form_product').removeClass('hide');
});

$(document).on('click','.add_submit_product', function (e) {
    var target = $(e.currentTarget);
    var category_product = $('select[name="category_product"]').val();
    var unit_product = $('select[name="unit_product"]').val();
    var code_product = $('input[name="code_product"]').val();
    var name_product = $('input[name="name_product"]').val();

    if(!category_product || category_product == '') {
        alert_float('danger', 'Vui lòng chọn danh mục!');
        return;
    }
    if(!unit_product || unit_product == '') {
        alert_float('danger', 'Vui lòng chọn đơn vị!');
        return;
    }
    if(!code_product || code_product == '') {
        alert_float('danger', 'Vui lòng nhập mã thành phẩm!');
        return;
    }
    if(!name_product || name_product == '') {
        alert_float('danger', 'Vui lòng nhập tên thành phẩm!');
        return;
    }

    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    data['category_product'] = category_product;
    data['unit_product'] = unit_product;
    data['code_product'] = code_product;
    data['name_product'] = name_product;
    $.post(admin_url+'warranty/add_product', data).done(function(response){
        response = JSON.parse(response);
        if(response.success == true) {
            $('.form_product').addClass('hide');
            $('.js-width').removeClass('hide');
            ajaxSelectProduct('#product_by_series', 'admin/warranty/searchProductWarranty', response.id);
        }
    });
});

function ajaxSelectProduct(element, url, id)
{
    if (id) {
        $(element).val(id).select2({
            width: 'resolve',
            escapeMarkup: function(m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    } else {
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

function rewidth(trItem) {
    var width = Number(trItem.parents('.js-width').width()) - 35;
    trItem.parents('.js-width').find('div.form-group').css({'width':width+'px'});
}
</script>