<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-export_different img {
        height: 20px;
        width: 20px;
    }

    .table-export_different thead th {
        text-align: center;
    }

    .table-export_different tr td:nth-child(2) {
        text-align: center;
        min-width: 100px;
    }

    .table-export_different tr td:nth-child(1) {
        text-align: center;
        min-width: 110px;
    }

    .table-export_different tr td:nth-child(3) {
        text-align: center;
        min-width: 120px;
    }

    .table-export_different tr td:nth-child(4) {
        text-align: center;
        min-width: 250px;
    }

    .table-export_different tr td:nth-child(5) {
        text-align: center;
        min-width: 130px;
    }

    .table-export_different tr td:nth-child(6) {
        text-align: right;
        min-width: 100px;
    }

    .table-export_different tr td:nth-child(7) {
        text-align: center;
        min-width: 130px;
    }

    .table-export_different tr td:nth-child(8) {
        text-align: center;
        min-width: 100px;
    }

    .table-export_different tr td:nth-child(9) {
        min-width: 150px;
    }

    .table-export_different tbody tr td:nth-child(10) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-export_different tbody .dropdown {
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="pull-right mright5 H_border">
                    <a href="<?= admin_url('option_pdf') ?>" class="btn btn-info test H_action_button" target="_blank">
                        <?php echo _l('option_pdf'); ?>
                    </a>
                </div>
                <?php if (has_permission('export_different', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= admin_url('export_different/detail') ?>" class="btn btn-info test H_action_button">
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
                <div class="col-md-3">
                    <?= lang('Chi nhánh', 'branch_search') ?>
                    <select name="branch_search" id="branch_search" class="branch_search"  data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                        <option value=""></option>
                        <?php if (!empty($branch)) { ?>
                            <?php foreach ($branch as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
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
                                            <?= _l('ch_confirm_22') ?> (<span class="ch_confirm_22">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('dont_approve') ?> (<span class="dont_approve">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                            <?= _l('ch_warehouse_d') ?> (<span class="ch_warehouse_d">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                            <?= _l('ch_warehouse_nd') ?> (<span class="ch_warehouse_nd">0</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <input type="hidden" id="suppliers_id" name="suppliers_id" value="" />
                        <div class="clearfix"></div>
                        <?php $table_data = array(
                            _l('ch_date_p'),
                            _l('ch_code_number'),
                            _l('ch_type_objects'),
                            _l('ch_objects'),
                            _l('ticket_dt_status'),
                            _l('expense_add_edit_amount'),
                            _l('ch_addedfrom'),
                            _l('ch_warehoues_app'),
                            _l('ch_note_pay_slips'),
                        );
                        array_push($table_data, _l('ch_option'));
                        render_datatable($table_data, 'export_different');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div class="modal fade" id="confirm_warehous" role="dialog">
    <div class="modal-dialog modal-lm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_export_quantity_missing'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="export_different_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    function view_export_different(id = null) {
        $('#export_different_data').html('');
        $.get(admin_url + 'export_different/int_export_different_view/' + id).done(function(response) {
            $('#export_different_data').html(response);
            $('#view_export_different').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            // changeRowNew('tblexport_different', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_export_different', function() {
        $('#export_different_data').html('');
        tAPI.draw('page');
    });
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
        $("#branch_search").select2({
            'allowClear': true
        })
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'suppliers_id': '[name="suppliers_id"]',
            'search_code': '[name="search_code"]',
            'search_staff': '[name="search_staff[]"]',
            'search_id_suppliers': '[name="search_id_suppliers[]"]',
            'search_date': '[name="search_date"]',
            'branch_search': '[name="branch_search"]',
        };
        tAPI = initDataTableCustom('.table-export_different', admin_url + 'export_different/table', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1, 'desc'))); ?>, fixedColumns = {
            leftColumns: 2,
            rightColumns: 1
        });
        // var tAPI = initDataTable('.table-export_different', admin_url+'export_different/table', [0], [0], CustomersServerParams,[1, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });

        $('.table-export_different').on('draw.dt', function() {
            checkNoDrop();
            get_total_limit();
        });
    });
    $('body').on('hidden.bs.modal', '#views_export_different', function() {
        $('#export_different_data').html('');
        tAPI.draw('page');
    });
    $(document).on('click', '.delete-reminds', function() {
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

    function confirm_warehous(id, warehouseman_id) {
        dataString = {
            id: id,
            warehouseman_id: warehouseman_id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>export_different/confirm_warehous",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                if (response.success == false) {
                    alert_float(response.message.alert_type, response.message.message);
                    var html = '<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
                                <thead>\
                                    <tr>\
                                        <th class="text-center"><?= lang('tnh_items') ?></th>\
                                        <th class="text-center"><?= lang('custom_field_add_edit_type') ?></th>\
                                        <th class="text-center"><?= lang('ch_quantity_missing') ?></th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    $.each(response.item, function(key, value) {
                        html += '<tr>\
                                        <th>' + value.name + '(' + value.code + ')</th>\
                                        <th class="text-center">' + value.type + '</th>\
                                        <th class="text-center">' + formatNumber(value.quantity_net) + '</th>\
                                    </tr>';
                    });
                    html += '</tbody>\
                            </table>';
                    $('#confirm_warehous').modal('show');
                    $('#table_html').html(html);
                } else {
                    alert_float(response.alert_type, response.message);
                }
            }
        });
        return false;
    }

    function var_status(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>export_different/update_status",
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

    // $(document).on('change','#search_code',function(e){
    //    tAPI.draw('page');
    // });
    // $(document).on('change','select[name="search_staff[]"]',function(e){
    //    tAPI.draw('page');
    // });
    // $(document).on('change','select[name="search_id_suppliers[]"]',function(e){
    //    tAPI.draw('page');
    // });
    // $(document).on('change','#search_date',function(e){
    //    tAPI.draw('page');
    // });
    //end
    function get_total_limit() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>export_different/count_all/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.ch_confirm_22').html(data.ch_confirm_22);
                $('.dont_approve').html(data.dont_approve);
                $('.ch_warehouse_d').html(data.ch_warehouse_d);
                $('.ch_warehouse_nd').html(data.ch_warehouse_nd);


            }
        });
    }
</script>