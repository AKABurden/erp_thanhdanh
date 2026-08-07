<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .progressbar:not(.hoang) {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }

    .progressbar li span {
        font-size: 11px;
    }

    .progressbar li:not(.hoang) {
        list-style-type: none;
        width: 22%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }

    .progressbar li:not(.hoang):before {
        width: 10px;
        height: 10px;
        content: ' ';
        counter-increment: step;
        line-height: 51px;
        border: 5px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
    }

    .progressbar li:not(.hoang):after {
        width: 100% !important;
        height: 2px !important;
        content: '' !important;
        position: absolute !important;
        background-color: #7d7d7d !important;
        top: 4px !important;
        left: -50% !important;
        z-index: -1 !important;
    }

    .progressbar li:first-child:after {
        content: none;
        display: none;
    }

    .progressbar li.active:not(.hoang) {
        color: green;
    }

    .progressbar li.active:not(.hoang):before {
        border-color: #55b776;
    }

    .progressbar li.cancel:before {
        border-color: red;
    }

    .progressbar li.active+li:after {
        background-color: #55b776 !important;
    }

    .font11 {
        font-size: 11px;
    }

    .btn-info.active,
    .btn-info:active {
        background-color: #094865;
    }

    .table-purchases tbody tr th {
        text-align: center;
    }

    .table-purchases tbody tr td:nth-child(9) {
        white-space: inherit;
        min-width: 400px;
    }

    .table-purchases tbody tr td:nth-child(6) {
        white-space: inherit;
        min-width: 100px;
    }

    .table-purchases tbody tr td:nth-child(1) {
        white-space: inherit;
        min-width: 100px;
    }

    .table-purchases tbody tr td:nth-child(2) {
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }

    .table-purchases tbody tr td:nth-child(3) {
        white-space: inherit;
        min-width: 120px;
    }

    .table-purchases tbody tr td:nth-child(7) {
        white-space: inherit;
        min-width: 150px;
        text-align: center;
    }

    .table-purchases tbody tr td:nth-child(8) {
        white-space: inherit;
        min-width: 150px;
        text-align: center;
    }

    .table-purchases tbody tr td:nth-child(4) {
        white-space: inherit;
        min-width: 110px;
        text-align: center;
    }

    .table-purchases tbody tr td:nth-child(5) {
        white-space: inherit;
        min-width: 180px;
        text-align: left;
        line-height: 2;
    }

    .table-purchases tbody tr td:nth-child(9) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-purchases tbody .dropdown {
        text-align: center;
    }

    .table-purchases img {
        height: 20px;
        width: 20px;
    }

    .progressbar_img {
        text-align: center !important;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .progressbar_img img {
        height: 25px;
        width: 25px;
    }

    ul.progressbar_img li.active_img img {
        border: 2px solid #00ff50;
    }

    ul.progressbar_img li.cancel img {
        border: 2px solid red;
    }

    ul.progressbar_img li.cancel_all img {
        border: 2px solid blue;
    }

    ul.progressbar_img li {
        width: 87px;
        float: left;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <!-- <div class="dropdown pull-right">
                    <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        <?= lang('actions') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                        <li>
                            <a href="<?= admin_url('option_pdf') ?>" class="" target="_blank"><i class="fa fa-file-pdf-o"></i>
                                <?php echo _l('option_pdf'); ?></a>
                        </li>
                        <li>
                            <a class="btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><i class="fa fa-filter"></i>
                                <?= lang('ch_seach_statistical') ?></a>
                        </li>
                    </ul>
                </div> -->
                <?php if (has_permission('purchase_order', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a class="search_person_ch btn btn-info test H_action_button">
                            <?php echo _l('Tạo đơn hàng tổng'); ?></a>
                    </div>
                <?php } ?>
                <?php if (has_permission('purchases', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= admin_url('purchases/detail') ?>" class="btn btn-info test H_action_button">
                            <?php echo _l('create_add_new'); ?></a>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?php echo render_select('search_code', array(), array('id', 'company'), 'ch_code_p'); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_select('search_staff[]', $dataStaff, array('staffid', 'name'), 'ch_staff_p', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>
                            <div class="input-group">
                                <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="custom_item_select" class="control-label"><?= _l('Hàng hóa') ?></label>
                            <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select" name="custom_item_select" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
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
                                            <?= _l('leads_all') ?> (<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                            <?= _l('dont_confirm') ?> (<span class="status1">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('dont_approve') ?> (<span class="status2">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                            <?= _l('ch_confirm_22') ?> (<span class="status3">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                            <?= _l('ch_cancel') ?> (<span class="status4">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="5">
                                            <?= _l('ch_productions_capacitys') ?> (<span class="productions">0</span>)
                                        </a>
                                    </li>


                                    <li>
                                        <a class="H_filter" data-id="7">
                                            <?= _l('Chưa đặt') ?> (<span class="not_po">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="8">
                                            <?= _l('Đặt 1 phần') ?> (<span class="part_po">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="9">
                                            <?= _l('Đặt đủ') ?> (<span class="all_po">0</span>)
                                        </a>
                                    </li>
                                    <!--  <li>
                                <a class="H_filter" data-id="4">
                                  <?= _l('finished') ?>
                                </a>
                            </li> -->
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = array(
                            _l('ch_date_p'),
                            _l('ch_code_p'),
                            _l('ch_code_old'),
                            _l('ch_staff_p'),
                            _l('invoice_dt_table_heading_status'),
                            _l('note'),
                            '<p class="center">' . _l('Trạng thái đặt hàng') . '</p>',
                            _l('ch_option'),
                        );
                        $custom_fields = get_custom_fields('purchases', array('show_on_table' => 1));
                        foreach ($custom_fields as $field) {
                            array_push($table_data, $field['name']);
                        }
                        render_datatable($table_data, 'purchases');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/popup_purchase_order/manage') ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
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
    $(function() {
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('purchases/SearchItems') ?>", 0);
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'search_code': '[name="search_code"]',
            'search_staff': '[name="search_staff[]"]',
            'search_date': '[name="search_date"]',
            'custom_item_select': '[name="custom_item_select"]',
        };
        tAPI = initDataTableCustom('.table-purchases', admin_url + 'purchases/table', [0], [0],
            CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>,
            fixedColumns = {
                leftColumns: 0,
                rightColumns: 0
            });

        // var tAPI = initDataTable('.table-purchases', admin_url+'purchases/table', [0], [0], CustomersServerParams,['1', 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $(filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });

        $('.table-purchases').on('draw.dt', function() {
            checkNoDrop();
            get_total_limit();
        });
    });

    function view_supplier_quotes(id) {
        $('#view_supplier_quotes').html('');
        $.get(admin_url + 'supplier_quotes/view_supplier_quotes/' + id).done(function(response) {
            $('#view_supplier_quotes').html(response);
            $('#views_items').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $('body').on('hidden.bs.modal', '#views_items', function() {
        $('#view_supplier_quotes').html('');
    });

    function view_purchase_order(id) {
        $('#purchase_order_data').html('');
        $.get(admin_url + 'purchase_order/view_purchase_order/' + id).done(function(response) {
            $('#purchase_order_data').html(response);
            $('#view_purchase_order').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $('body').on('hidden.bs.modal', '#view_purchase_order', function() {
        $('#purchase_order_data').html('');
    });
    $('body').on('hidden.bs.modal', '#views_purchases', function() {
        $('#purchases_data').html('');
        tAPI.draw('page');
    });
    $(document).on('click', '.delete-remind', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
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
    $(document).on('click', '.change_type', function() {
        setTimeout(function() {
            tAPI.draw('page');
        }, 500);
    });

    function var_status(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>purchases/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        tAPI.draw('page');
                        alert_float('success', response.message);
                    }
                }
            });
            return false;
        }
    }

    function send_quote_suppliers(supplier_id, ask_price) {
        $('#send_quote_suppliers').html('');
        $.get(admin_url + 'RFQ/send_quote_suppliers/' + supplier_id + '/' + ask_price).done(function(response) {
            $('#send_quote_suppliers').html(response);
            $('#send_quote').modal('show');
            init_editor();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function save_contact_person(id) {
        var note_cancel = $('#note_cancel').val();
        dataString = {
            note_cancel: note_cancel,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchases/note_cancel/" + id,
            data: dataString,
            cache: false,
            success: function(data) {
                $('.add_contact_person').popover('hide');
                tAPI.draw('page');
                $('.popover').popover('hide');
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message)
            }
        });
    }

    $(document).on('click', '.close', function(e) {
        $('.add_contact_person').popover('hide');
    });
    $(document).on('click', '.load_items', function(e) {
        var currentQuantityInput = $(e.currentTarget);
        var supplier_id = $(e.currentTarget).attr('data-id');
        var selected = $('.items_ch_' + supplier_id).find('option:selected', e);
        $('.items_ch_' + supplier_id).selectpicker('val', '-1');
        selected.each(function() {
            var values = $(this).val();
            var type = $(this).attr('data-idd');
            // results += Number($(this).attr('left-id'));
            if (values != '') {
                if ($('table.table-striped_' + supplier_id + ' tbody tr').find('td input[value=' + values +
                        ']').length) {
                    alert_float('warning', "<?= _l('ch_exsit_items_rfq') ?>");
                    return;
                } else {
                    var lengths = $('table.table-striped_' + supplier_id + ' tbody tr').length;
                    var td = '<tr>\
        <td>' + (lengths + 1) + '</td>\
        <td class="text-center"><div class="preview_image text-center" style="width: 135px;margin-bottom:0;margin-top:0">\
                                    <div class="display-block contract-attachment-wrapper ">\
                                        <div>\
                                            <a href="' + findpurchase(values).avatar_1 +
                        '" data-lightbox="customer-profile" class="display-block mbot5">\
                                                    <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="' +
                        findpurchase(values).avatar_1 + '">' +
                        findItemtypetemtype(type) + '\
                                            </a>\
                                        </div>\
                                    </div>\
                                </div>\
        </td>\
        <td>\
          <input type="text" name="items[' + supplier_id + '][' + lengths + '][product_id]" class="hide" value="' +
                        values + '"><input type="text" name="items[' + supplier_id + '][' + lengths +
                        '][type]" class="hide" value="' + type + '">' + findpurchase(values).name_item +
                        '<br>(' + findpurchase(values).code_item + ')\
        </td>\
        <td class="text-center">' + findpurchase(values).html + '</td>\
        <td class="center"><input type="text" class="hide" name="items[' + supplier_id + '][' + lengths +
                        '][quantity_net]" value="' + findpurchase(values).quantity_net + '">' +
                        findpurchase(values).quantity_net + '\
        </td>\
        <td class="center"><i onclick="deleteTrItem(this); return false;" class="fa fa-times"></i></td>\
      </tr>';
                    $('table.table-striped_' + supplier_id + ' tbody').append(td);
                }
            }
        });
    });


    //hoàng crm bổ xung search
    var inner_popover_template =
        '<div class="popover" style="width:1000px;max-width:2000px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person', function(e) {
        var dropdown_menu = `
                  <div class="col-md-3">
                    <?php echo render_select('search_code', array(), array('id', 'company'), 'ch_code_p'); ?>
                  </div>
                  <div class="col-md-3">
                    <?php echo render_select('search_staff[]', $dataStaff, array('staffid', 'name'), 'ch_staff_p', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>
                        <div class="input-group">
                           <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar calendar-icon"></i>
                           </div>
                        </div>
                     </div>
                  </div>`;

        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            title: '<?= _l('ch_seach_statistical') ?><button type="button" class="close">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template
        });
        init_selectpicker();
        init_ajax_searchs('purchases', '#search_code');
        search_daterangepicker();
    });
    $(document).on('click', '.close', function(e) {
        $('.search_person').popover('hide');
    });

    function init_ajax_searchs(e, t, a, i) {
        var n = $("body").find(t);
        var h = t;
        if (n.length) {
            var s = {
                ajax: {
                    url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                    data: function() {
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
                        return t.type = e, t.rel_id = "", t.q = "{{{q}}}", void 0 !== a && jQuery.extend(t, a), t
                    }
                },
                locale: {
                    emptyTitle: app.lang.search_ajax_empty,
                    statusInitialized: app.lang.search_ajax_initialized,
                    statusSearching: app.lang.search_ajax_searching,
                    statusNoResults: app.lang.not_results_found,
                    searchPlaceholder: app.lang.search_ajax_placeholder,
                    currentlySelected: app.lang.currently_selected
                },
                requestDelay: 500,
                cache: !1,
                preprocessData: function(e) {
                    var t = [];
                    var _temp_all = {
                        'value': 'all',
                        'text': 'Tất cả',
                    };
                    t.push(_temp_all);
                    for (var a = e.length, i = 0; i < a; i++) {
                        var n = {
                            value: e[i].id,
                            text: e[i].name
                        };
                        t.push(n)
                    }
                    return t;
                },
                preserveSelectedPosition: "after",
                preserveSelected: !0
            };
            n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);
        }
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
        }, function(start, end, label) {});
        $('input[name="search_date"]').val('').datepicker("refresh");
        $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date").trigger("change");
        });
        $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date").trigger("change");
        });
    };

    $(document).on('change', '#search_code', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', 'select[name="search_staff[]"]', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', '#search_date', function(e) {
        tAPI.draw('page');
    });
    $(document).on('click', '.po-close', function(e) {
        $('.popover').popover('hide');
    });
    // $(document).ready(function() {
    //     $(document).ready(function() {
    //         $(document).on('click', '.dropdown-menu .not-outside', function(event) {
    //             event.stopPropagation();
    //         });
    //     });
    // });
    //end
    function get_total_limit() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchases/count_all/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.status1').html(data.status1);
                $('.status2').html(data.status2);
                $('.status3').html(data.status3);
                $('.status4').html(data.status4);
                $('.not_po').html(data.not_po);
                $('.part_po').html(data.part_po);
                $('.all_po').html(data.all_po);
                $('.productions').html(data.productions);
            }
        });
    }

    //tạo đơn hàng tổng
    function create_order() {
        var type_plan = $('#type_plan').val();
        var id_purchases = $('[name="id_purchases[]"]').val();
        console.log(id_purchases);
        if (empty(id_purchases)) {
            alert("<?= _l('Vui lòng chọn những phiếu cần tạo đơn hàng tổng!') ?>");
            return;
        } else {
            var purchases = '';
            $.each(id_purchases, function(key, value) {
                purchases += value + ',';
            });
            window.open('<?= admin_url('purchase_order/create_detail_all?id=') ?>' + purchases.trim(',') + '&type_plan=' +
                type_plan);
            return false;
        }
    }

    //đơn hàng tổng
    <?php
    $array = array(
        array(
            'id' => 2,
            'name' => 'Từ tạo mới'
        ),
        array(
            'id' => 1,
            'name' => 'Từ hoạch định SX'
        ),
    );
    ?>
    var inner_popover_template_ch =
        '<div class="popover" style="width:300px;max-width:2000px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person_ch', function(e) {
        $('#type_plan').val('');
        $('[name="id_purchases[]"]').val('');
        var dropdown_menu_ch = '\
        <?php echo render_select('type_plan', $array, array('id', 'name'), 'Tạo từ'); ?>\
        <?php echo render_select('id_purchases[]', array(), array('id', 'name'), 'Mã YCMH', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>\
        <button type="button" onclick="create_order();return false;" class="btn btn-info btn-block mtop15"><?php echo _l('submit'); ?></button>\
   ';

        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            title: '<?= _l('ch_seach_statistical') ?><button type="button" class="close close_ch">&times;</button>',
            content: function() {
                return dropdown_menu_ch;
            },
            template: inner_popover_template_ch
        });
        init_selectpicker();
        init_ajax_searchs('purchases', '#search_code');
        search_daterangepicker();
    });
    $(document).on('click', '.close_ch', function(e) {
        $('.search_person_ch').popover('hide');
    });
    $(document).on('change', '#type_plan', function() {
        var id = $('#type_plan').val();
        var id_purchases = $('[name="id_purchases[]"]');
        id_purchases.find('option').remove();
        id_purchases.selectpicker('refresh');
        dataString = {
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchases/get_id_purchases",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                $.each(response, function(key, value) {
                    id_purchases.append('<option data-subtext="' + value.date + '" value="' +
                        value.id + '">' + value.prefix + value.code + '</option>');
                });
                id_purchases.selectpicker('refresh');
            }
        });
        return false;
    });
    <?php
    if (!empty($this->input->get('zalo_purchase'))) {
    ?>
        view_purchases(<?= $this->input->get('zalo_purchase') ?>);
    <?php
    }
    ?>
    init_ajax_searchs('purchases', '#search_code');
    search_daterangepicker();

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
</script>