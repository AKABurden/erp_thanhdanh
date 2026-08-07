<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tab-pane{
        display: none;
    }
    .tab-pane.active{
        display: block;
    }
    .daterangepicker {
        z-index: 999999 !important;
    }
    .w40 {
        width: 40%;
    }
    .w20 {
        width: 20%;
    }
    .wap-btn {
        font-weight: bold;
        background: #bdbdbd;
        color: #fff;
    }
    .wap-btn.active {
        background: #03a9f4;
        color: #fff;
    }
    .bg_no_event {
        background: #9d9d9d;
    }
    .no-event_set {
        pointer-events: none;
        background: #adadad;
    }
    .no-drop_set {
        cursor: pointer;
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
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info H_action_button wrap-change-view" onclick="change_view(2); return false;">
                    <?php echo _l('Xem tổng quan'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="wrap-view">
        <?php $this->load->view('admin/set_prices/content_1'); ?>
    </div>
</div>

<div class="modal fade" id="set_prices_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('add_set_prices'); ?></span>
                    <span class="edit-title"><?php echo _l('edit_set_prices'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/set_prices/group_detail',array('id'=>'set-prices-modal')); ?>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><?=_l('info')?></a>
                    </li>
                    <li role="presentation">
                        <a href="#item_range" aria-controls="item_range" role="tab" data-toggle="tab"><?=_l('range')?></a>
                    </li>
                    <li role="presentation">
                        <a href="#item_setting" aria-controls="item_setting" role="tab" data-toggle="tab"><?=_l('advanced_setting')?></a>
                    </li>
                </ul>
                <div role="tabpanel" class="tab-pane active" id="item_info">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="name" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('name_set_prices'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <?php echo render_input('name',''); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="date_active" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('date_active'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" id="checkbox_date" name="checkbox_date">
                                                <label for="checkbox_date"><?=_l('no_limit')?></label>
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group" style="width: 100%;">
                                                    <input type="text" id="date_active" name="date_active" class="form-control date_active" aria-invalid="false">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="status" class="control-label">
                                                <?php echo _l('status'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <div class="radio radio-primary pull-left">
                                                <input type="radio" name="status" value="1" checked>
                                                <label for="single"><?=_l('active')?></label>
                                            </div>
                                            <div class="radio radio-primary pull-left mbot10 mleft20" style="margin-top: 10px !important;">
                                                <input type="radio" name="status" value="2">
                                                <label for="single"><?=_l('dont_active')?></label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="item_range">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading"><?=_l('range_customer')?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                        <tbody>
                                            <tr>
                                                <td class="w40">
                                                    <div class="radio radio-primary">
                                                        <input type="radio" name="type_customer" value="1" checked>
                                                        <label for="single"><?=_l('all_customer')?></label>
                                                    </div>
                                                </td>
                                                <td colspan="3">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="radio radio-primary">
                                                        <input type="radio" name="type_customer" value="2">
                                                        <label for="single"><?=_l('customer_group')?></label>
                                                    </div>
                                                </td>
                                                <td colspan="3">
                                                    <div class="js-checkSelect">
                                                        <?php
                                                            echo render_select('groups_in[]',$groups,array('id','name'),'','',array('multiple'=>true, 'data-actions-box'=>true),array(),'','',false);
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="panel panel-primary" style="display: none;">
                                <div class="panel-heading"><?=_l('range_item')?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="radio radio-primary">
                                                        <input type="radio" name="type_item" value="1" checked>
                                                        <label for="single"><?=_l('ch_items')?></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="radio radio-primary">
                                                        <input type="radio" name="type_item" value="2">
                                                        <label for="single"><?=_l('tnh_products')?></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="item_setting">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width: 20%;">
                                            <label for="status" class="control-label">
                                                <?php echo _l('item_price'); ?>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <div class="pull-left" style="width: 32%;">
                                                <?php echo render_select('type_price_setting',array(),array('id','name','sub_name'),'','',array(),array(),'','',false); ?>
                                            </div>
                                            <div class="pull-left w20">
                                                <a class="btn btn-default wap-btn mleft10 js-btn active" data-value="sum">+</a>
                                                <a class="btn btn-default wap-btn mleft5 js-btn" data-value="sub">-</a>
                                                <input type="hidden" name="sum_OR_sub" class="sum_OR_sub" value="sum">
                                            </div>
                                            <div class="pull-left w20 mleft10">
                                                <input type="text" name="value_price_setting" class="align_right H_input" onkeyup="formatNumBerKeyUp(this)" value="0">
                                            </div>
                                            <div class="pull-left" style="width: 25%;">
                                                <a class="btn btn-default wap-btn mleft10 js-btn-2 active" data-value="vnd">VND</a>
                                                <a class="btn btn-default wap-btn mleft5 js-btn-2" data-value="percent">%</a>
                                                <input type="hidden" name="vnd_OR_percent" class="vnd_OR_percent" value="vnd">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $('body').on('click', '.btn-search', function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-value');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    changeData_prices(1,'giá vốn');
    $(function(){
        var CustomersServerParams = {
              'filterStatus' : '[name="filterStatus"]',
           };
        var tAPI = initDataTableCustom('.table-set_prices', admin_url+'set_prices/table_set_prices', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
      $.each(CustomersServerParams, function(filterIndex, filterItem){
         $('' + filterItem).on('change', function(){
             tAPI.draw('page');
         });
      });
        appValidateForm($('#set-prices-modal'), {name: 'required'}, manage_set_prices);
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
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        var d_start = dd+'/'+mm+'/'+yyyy;
        active_daterangepicker(d_start,d_start);
        $('.add-title').removeClass('hide');
        $('.edit-title').addClass('hide');
        $('input[name=type_item][value=1]').parents('.panel').removeClass('hide');
        $('#name').val('');
        $('input[name="date_active"]').val('').datepicker("refresh");
        $('input[name=status][value=1]').prop('checked',true);
        $('input[name=type_customer][value=1]').prop('checked',true);
        $('select[name="groups_in[]"]').selectpicker('val','');
        $('input[name=type_item][value=1]').prop('checked',true);
        $('input[name=value_price_setting]').val(0);

        $('#set-prices-modal').attr("action","<?=admin_url('set_prices/group_detail')?>");
        changeData_prices(1,'giá vốn');
        $('#set_prices_modal').modal({backdrop: 'static', keyboard: false});
    }
    function edit_set_prices(id) {
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('input[name=type_item][value=1]').parents('.panel').addClass('hide');
        $('#set-prices-modal').attr("action","<?=admin_url('set_prices/group_detail/')?>"+id);

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/getData/'+id, data).done(function(response){
            response = JSON.parse(response);
            if(response.date_active == '') {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();

                var d_start = dd+'/'+mm+'/'+yyyy;
                active_daterangepicker(d_start,d_start);
            }
            else {
                active_daterangepicker(response.date_start, response.date_end);
            }

            var arrGroup = [];
            $.each(response.id_groups, function(i,v){
                arrGroup.push(v);
            });
            $('#name').val(response.name);
            if(response.checkbox_date == 1) {
                $('input[name=checkbox_date]').prop('checked',true);
                $('input[name="date_active"]').val('').datepicker("refresh");
                $('.date_active').parents('.form-group').find('.date_active').addClass('bg_no_event');
                $('.date_active').parents('.form-group').find('.input-group-addon').addClass('bg_no_event');
                $('#date_active').parents('.form-group').find('.input-group').addClass('none-event');
                $('#date_active').parents('.form-group').addClass('no-drop-v2');
            }
            else {
                $('input[name=checkbox_date]').prop('checked',false);
                $('input[name="date_active"]').val(response.date_active);
                $('#date_active').parents('.form-group').find('.date_active').removeClass('bg_no_event');
                $('#date_active').parents('.form-group').find('.input-group-addon').removeClass('bg_no_event');
                $('#date_active').parents('.form-group').find('.input-group').removeClass('none-event');
                $('#date_active').parents('.form-group').removeClass('no-drop-v2');
            }
            
            $('input[name=status][value='+response.status+']').prop('checked',true);
            $('input[name=type_customer][value='+response.type_customer+']').prop('checked',true);
            $('select[name="groups_in[]"]').selectpicker('val',arrGroup);
            $('input[name=type_item][value='+response.type_item+']').prop('checked',true);
            changeData_prices(response.type_item, response.type_price_setting, response.id);
            //kích hoăc + or -
            $('.js-btn').removeClass('active');
            $('[data-value="'+response.sum_OR_sub+'"]').addClass('active');
            $('.sum_OR_sub').val(response.sum_OR_sub);
            //end
            //kích hoăc vnd or %
            $('.js-btn-2').removeClass('active');
            $('[data-value="'+response.vnd_OR_percent+'"]').addClass('active');
            $('.vnd_OR_percent').val(response.vnd_OR_percent);
            //end
            $('input[name=value_price_setting]').val(formatNumber(response.value_price_setting));
            $('#set_prices_modal').modal({backdrop: 'static', keyboard: false});
        });
    }
    var active_daterangepicker = (startDate, endDate) => {
        $('input[name="date_active"]').daterangepicker({
            opens: 'left',
            isInvalidDate: false,
            startDate: startDate,
            endDate: endDate,
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
    };

    $('body').on('show.bs.modal', '#set_prices_modal', function() {
       checkType_customer();
       checkbox_date();
    });
    $('input[name=type_customer]').change(function() {
        checkType_customer();
    });
    function checkType_customer() {
        var value = $('input[name=type_customer]:checked').val();
        if(value == 1) {
            $('.js-checkSelect').addClass('no-drop-v2');
            $('.js-checkSelect').find('div.form-group').addClass('none-event');
        }
        else if(value == 2) {
            $('.js-checkSelect').removeClass('no-drop-v2');
            $('.js-checkSelect').find('div.form-group').removeClass('none-event');
        }
    }
    $('.js-btn').click(function(e) {
        $('.js-btn').removeClass('active');
        var current = $(e.currentTarget);
        var value = current.attr('data-value');
        current.addClass('active');
        $('.sum_OR_sub').val(value);
    });

    $('.js-btn-2').click(function(e) {
        $('.js-btn-2').removeClass('active');
        var current = $(e.currentTarget);
        var value = current.attr('data-value');
        current.addClass('active');
        $('.vnd_OR_percent').val(value);
        $('input[name="value_price_setting"]').val(0);
    });

    $('input[name="value_price_setting"]').keyup(function(e) {
        var value = $('input[name="value_price_setting"]').val();
        var vnd_OR_percent = $('.vnd_OR_percent').val();
        if(vnd_OR_percent == 'percent') {
            if(Number(unformatNumber(value)) > 100) {
                $('input[name="value_price_setting"]').val(100);
            }
        }
    });

    function manage_set_prices(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float(response.alert_type, response.message);
                $('.table-set_prices').DataTable().ajax.reload();
            }
            $('#set_prices_modal').modal('hide');
        });
        return false;
    }

    var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>'; 
    
    $(document).on('mouseenter','.js-menu-status', function (e) {
        $(this).popover({
            html: true,
            placement: "right",
            trigger: 'hover',
            title:'<?php echo _l('range_customer'); ?>',
            content: function() {
                return $(this).find('.content-menu').html();
            },
            template: inner_popover_template
        });
    }).on('mouseleave','.js-menu-status',  function(){
        $('.js-menu-status').popover('hide');
    });
    $('#checkbox_date').click(function(e) {
        checkbox_date();
    });
    function checkbox_date() {
        if($("#checkbox_date").is(':checked')) {
            $('input[name="date_active"]').val('').datepicker("refresh");
            $('.date_active').parents('.form-group').find('.date_active').addClass('bg_no_event');
            $('.date_active').parents('.form-group').find('.input-group-addon').addClass('bg_no_event');
            $('#date_active').parents('.form-group').find('.input-group').addClass('none-event');
            $('#date_active').parents('.form-group').addClass('no-drop-v2');
        }
        else {
            $('input[name="date_active"]').val('').datepicker("refresh");
            $('#date_active').parents('.form-group').find('.date_active').removeClass('bg_no_event');
            $('#date_active').parents('.form-group').find('.input-group-addon').removeClass('bg_no_event');
            $('#date_active').parents('.form-group').find('.input-group').removeClass('none-event');
            $('#date_active').parents('.form-group').removeClass('no-drop-v2');
        }
    }
    $('input[name=type_item]').change(function() {
        var value = $('input[name=type_item]:checked').val();
        if(value == 1) {
            changeData_prices(1,'giá vốn');
        }
        else if(value == 2) {
            changeData_prices(2,'giá vốn');
        }
    });
    function changeData_prices(type,value,id_remove) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/getData_price/'+type, data).done(function(response){
            response = JSON.parse(response);
            $('#type_price_setting').html('');
            $.each(response,function(i,v){
                var select = '';
                if(value == v.id) {
                    select = 'selected';
                }
                if(!id_remove) {
                    $('#type_price_setting').append('<option value="'+v.id+'" data-subtext="'+v.sub_name+'" '+select+'>'+v.name+'</option>');
                }
                else {
                    if(id_remove == v.id) {
                       return; 
                    }
                    else {
                        $('#type_price_setting').append('<option value="'+v.id+'" data-subtext="'+v.sub_name+'" '+select+'>'+v.name+'</option>');
                    }
                }
            })
            $('#type_price_setting').selectpicker('refresh');
        });
    }

    function delete_set_prices(id) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/delete_set_prices/'+id, data).done(function(response){
            response = JSON.parse(response);
            alert_float(response.alert_type,response.message);
            $('.table-set_prices').DataTable().ajax.reload();
        });
    }

    $(document).on('click','.apply-set-price', function (e) {
        var id = $(this).attr('data-id');
        $('.apply-set-price').removeClass('active');
        $('.wap-load').addClass('hide');
        if($(this).find('.wap-load').hasClass('hide')) {
            //xử lý css
            $(this).find('.wap-load').removeClass('hide');
            $(this).addClass('active');
            var dont_active = $('.apply-set-price');
            $.each(dont_active,function(i,v){
                if(!$(this).hasClass('active')) {
                    $(this).parents('td').addClass('no-drop_set');
                    $(this).addClass('no-event_set');
                }
            });

            //xử lý back-end
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
              data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url+'set_prices/apply_set_price/'+id, data).done(function(response){
                response = JSON.parse(response);
                alert_float(response.alert_type,response.message);
                $('.apply-set-price').removeClass('active');
                $('.wap-load').addClass('hide');
                $('.apply-set-price').parents('td').removeClass('no-drop_set');
                $('.apply-set-price').removeClass('no-event_set');
            });
        }
    });

    function change_view(type) {
        $('.wrap-view').html('');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'set_prices/change_view/'+type, data).done(function(response){
            if (type == 1) {
                $('.wrap-change-view').attr('onclick','change_view(2); return false;');
                $('.wrap-change-view').text('Xem tổng quan');
            } else if (type == 2) {
                $('.wrap-change-view').attr('onclick','change_view(1); return false;');
                $('.wrap-change-view').text('Xem danh sách');
            }
            $('.wrap-view').html(response);
        });
    }
</script>
</body>
</html>
