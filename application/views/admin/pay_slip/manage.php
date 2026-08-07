<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">

    .table-pay_slip img {
        height: 20px;
        width: 20px;
    }

    .table-pay_slip thead tr th {
        text-align: center;
    }

    .table-pay_slip tr td:nth-child(1) {
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-pay_slip tr td:nth-child(2) {
        min-width: 95px;
        white-space: unset;
        text-align: center;

    }

    /* .table-pay_slip tr td:nth-child(3) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    } */

    .table-pay_slip tr td:nth-child(3) {
        min-width: 110px;
        white-space: unset;
        text-align: center;

    }

    .table-pay_slip tr td:nth-child(4) {
        min-width: 250px;
        white-space: unset;
    }

    .table-pay_slip tr td:nth-child(5) {
        min-width: 90px;
        white-space: unset;
    }

    .table-pay_slip tr td:nth-child(6) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    /*  .table-pay_slip tr td:nth-child(8)
      {
                  min-width: 130px;
                  white-space: unset;
                  text-align: center;
      }*/
    .table-pay_slip tr td:nth-child(7) {
        min-width: 130px;
        white-space: unset;
        text-align: center;
    }

    .table-pay_slip tr td:nth-child(8) {
        min-width: 160px;
        white-space: unset;
    }

    .table-pay_slip tr td:nth-child(9) {
        min-width: 150px;
        white-space: unset;
    }

    .table-pay_slip tbody tr td:nth-child(10) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-pay_slip tbody .dropdown {
        text-align: center;
    }
</style>
<div id="wrapper">
    <!-- <div class="panel_s mbot10 H_scroll" id="H_scroll">
              <div class="panel-body ">
                 <div class="_buttons">
                    <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                  </div>
                  <div class="clearfix"></div>
              </div>
           </div> -->
    <!-- sum note -->
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a class="btn btn-info pull-right mleft5 H_action_button option_barcode" data-toggle="collapse"
                   data-target="#search-tnh" aria-expanded="true"><i
                            class="fa fa-filter"></i> <?= lang('tnh_seach_statistical') ?></a>
                <!-- <?php if (has_permission('pay_slip', '', 'create')) { ?>
                    <a class="btn btn-info pull-right H_action_button" onclick="add(); return false;">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?>
                    </a>
                <?php } ?> -->
            </div>
        </div>
    </div>
    <!-- sum note -->
    <div class="content">
        <div class="row">
            <!-- sum note -->
            <div class="col-md-12">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?= lang('supplier', 'business_plan_search') ?>
                        <input type="text" name="suppliers_id" data-placeholder="<?= lang('supplier') ?>"
                               id="suppliers_id" class="business_plan_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                               id="start_date_search" class="start_date_search datepicker form-control"
                               style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                               value="">
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
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                            <?= _l('leads_all') ?> (<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                            <?= _l('ch_invoice_tax') ?> (<span class="ch_invoice_tax">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('ch_retail_invoice') ?> (<span class="ch_retail_invoice">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="5">
                                            <?= _l('Gia công ngoài') ?> (<span class="ch_gcn">0</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <div class="clearfix mtop20"></div>
                        <?php $table_data = array(
                            _l('ch_date_p'),
                            _l('ch_code_pay_slip'),
                            // _l('ch_type_invoice'),
                            _l('ch_code_old'),
                            _l('supplier'),
                            //   _l('ch_all_total'),
                            _l('ch_price_pay_slip'),
                            _l('ch_addedfrom'),
                            _l('acs_sales_payment_modes_submenu'),
                            _l('note'),
                            _l('ch_option'),

                        );
                        render_datatable_tfoot_ch($table_data, 'pay_slip');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="modal_payment"></div>
<div id="view_pay_slip_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $('.H_filter').click(function (e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    var tAPI;
    $(function () {
        // sum note
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'suppliers_id': '[name="suppliers_id"]',
            'start_date_search': '[name="start_date_search"]',
            'end_date_search': '[name="end_date_search"]',
        };
        // ./sum note
        tAPI = initDataTableCustom('.table-pay_slip', admin_url + 'pay_slip/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order',
            json_encode(array(0, 'desc'))); ?>, fixedColumns = {leftColumns: 3, rightColumns: 1});
        // var tAPI = initDataTable('.table-pay_slip', admin_url+'pay_slip/table', [0], [0], CustomersServerParams,[0, 'desc']);
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $('' + filterItem).on('change', function () {
                tAPI.draw('page');
            });
        });
        $('.table-pay_slip').on('draw.dt', function () {
            get_total_limit();
        });
    });
    $('.table-pay_slip').on('draw.dt', function () {
        var itemsTable = $(this).DataTable();
        var sums = itemsTable.ajax.json().sums;
        $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
        $('.DTFC_LeftFootWrapper').css("background", "#ffff");
        $('.dataTables_scrollFoot').find('tfoot td').eq(4).html('<div class="text-right">' + sums.pay + '</div>');
    });

    function var_status(status, id) {
        {
            dataString = {id: id, status: status, [csrfData['token_name']]: csrfData['hash']};
            jQuery.ajax({
                type: "post",
                url: "<?=admin_url()?>pay_slip/update_status",
                data: dataString,
                cache: false,
                success: function (response) {
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

    $(document).on('click', '.delete-remind', function () {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function (response) {
                alert_float(response.alert_type, response.message);
                tAPI.draw('page');
            }, 'json');
        }
        return false;
    });

    function view_pay_slip(id) {
        $('#view_pay_slip_data').html('');
        $.get(admin_url + 'pay_slip/electronic_bill/' + id).done(function (response) {
            $('#view_pay_slip_data').html(response);
            $('#view_pay_slip').modal({show: true, backdrop: 'static'});
            init_selectpicker();
            init_datepicker();
        }).fail(function (error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $('body').on('hidden.bs.modal', '#view_pay_slip', function () {
        $('#view_pay_slip_data').html('');
    });
    $('body').on('hidden.bs.modal', '#views_import', function () {
        $('#import_data').html('');
        $('.table-import').DataTable().ajax.reload();
    });

    function get_total_limit() {
        dataString = {[csrfData['token_name']]: csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>pay_slip/count_all/",
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.ch_invoice_tax').html(data.ch_invoice_tax);
                $('.ch_retail_invoice').html(data.ch_retail_invoice);
                $('.ch_gcn').html(data.ch_gcn);
            }
        });
    }

</script>

<!-- sum note -->
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        business_plan_search: '#business_plan_search',
        start_date: '#start_date',
        end_date: '#end_date'
    };
    var oTable = '';
</script>
<script type="text/javascript">
    function ajaxSelectParamsv1(element, url, id, params = false, clearSl2 = false) {
        console.log(clearSl2);
        if (id) {
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
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        }
    }

    $(document).ready(function () {
        ajaxSelectParamsv1('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        // ajaxSelectParams('#business_plan_search', 'admin/business_plan/searchBusinessPlan', 0, true, true);
        ajaxSelectParamsv1('#suppliers_id', 'admin/suppliers/searchSuppliers', 0, true, true);

        oTable = tnhDatatable(
            '#table-productions-plan',
            {
                'order': [[2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                scrollY: height_body,
                scrollX: true,
                fixedColumns: {
                    leftColumns: 4,
                    rightColumns: 1
                },
                // stateSave: true,
                autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getProductionsPlan') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "drawCallback": function (settings, nRow) {
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    stProductionPlan = aData[9];
                    if (stProductionPlan != 'approved') {
                        $(nRow).find('.tnh-created-productions-capacity').addClass('tnh-disabled');
                    }
                },
                "initComplete": function (settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                },
                "columnDefs": [
                    {"targets": 0, "name": 'id', 'visible': false},
                    {
                        "targets": 1,
                        "name": 'number_records',
                        'className': 'text-center',
                        'sortable': false,
                        'width': '50px'
                    },
                    {
                        "render": function (data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date', 'searchable': false, 'width': '80px'
                    },
                    {
                        "render": function (data, type, row) {
                            return '<a class="tnh-modal" data-tnh="modal" href="' + site.base_url + 'admin/manufactures/view_productions_plan/' + row[0] + '" data-toggle="modal" data-target="#myModal">' + data + '</a>';
                        },
                        "targets": 3, "name": 'reference_no', 'width': '140px'
                    },
                    {"targets": 4, "name": 'planning_cycle', 'width': '150px'},
                    {
                        "render": function (data, type, row) {
                            return data;
                        },
                        "targets": 5, "name": 'pb', 'width': '250px'
                    },
                    {
                        "render": function (data, type, row) {
                            str = '';
                            if (data) {
                                data = data.split('-');
                                if (data[0] == 1) {
                                    str += '<span class="label label-primary"><?= lang('tnh_sales_orders') ?></span>';
                                }
                                if (data[0] == 1 && data[1] == 1) {
                                    str += '</br></br>';
                                }
                                if (data[1] == 1) {
                                    str += '<span class="label label-warning"><?= lang('tnh_business_plan') ?></span>';
                                }
                            }
                            return str;
                        },
                        "targets": 6, "name": 'options', 'width': '130px'
                    },
                    {"targets": 7, "name": 'note', 'width': '150px'},
                    {"targets": 8, "name": 'created_by', 'width': '120px'},
                    {
                        "render": function (data, type, row) {
                            str = '';

                            productions_plan_id = row[0];
                            if (data == "approved" || data == "capacity") {
                                user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' + row[10] + '</div>';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' productions_plan_id=\'' + productions_plan_id + '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' + user_status;
                            } else if (data == "approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' productions_plan_id=\'' + productions_plan_id + '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' + user_status;
                            } else if (data == "capacity") {
                                str = '<div class="text-left"><span class="label label-primary"><?= lang('tnh_capacity') ?></span></div>' + user_status;
                            } else {
                                str = '';
                            }
                            return str;
                        },
                        "targets": 9, "name": 'status', 'width': '120px'
                    },
                    {"targets": 10, "name": 'user_status', 'visible': false},
                    {"targets": 11, "name": 'reference_orders', 'visible': false},
                    {
                        "render": function (data, type, row) {
                            str = '';
                            productions_plan_id = row[0];

                            if (data == 2) {
                                str = '<div class="text-center"><span class="label label-success"><?= lang('tnh_order_finised') ?></span></div>';
                            } else if (data == 0 || data == 1) {
                                title = '<?= lang('tnh_not_produced') ?>';
                                // btnType = "warning";
                                str = '<div class="text-center"><span class="label label-warning">' + title + '</span></div>';
                                if (data == 1) {
                                    title = '<?= lang('tnh_apart_producing') ?>';
                                    // btnType = "primary";
                                    str = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_procedure') ?>\" data-content="<p><a id=\'agree-procedure\' productions_plan_id=\'' + productions_plan_id + '\' value=\'2\' class=\'btn btn-danger\'><?= lang('tnh_order_finised') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-primary po">' + title + '</span></div>';
                                }
                            }
                            return str;
                        },
                        "targets": 12, "name": 'wprocedure', 'width': '120px'
                    },
                    {"targets": 13, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '160px'}
                ]
            }
        );

        $(document).on('click', '#table-productions-plan_wrapper .btn-dt-reload', function (event) {
            oTable.draw();
        });

        $(document).on('change', '#orders_search, #business_plan_search, #start_date, #end_date', function (event) {
            oTable.draw();
        });

        $(document).on('click', '#table-view-plan_wrapper .btn-dt-reload', function (event) {
            oTableProductionsPlan.draw('page');
        });

        $(document).on('click', '#table-view-procedure_wrapper .btn-dt-reload', function (event) {
            oTableProductionsPlanProceduce.draw('page');
        });

        $('#table-productions-plan').on('draw.dt', function (e, settings) {
        })

        $(document).on('click', '.export-excel', function (event) {
            event.preventDefault();
            productions_plan_id = $(this).attr('value');
            bootbox.confirm({
                message: '<?= lang('tnh_you_want_to_export_excel') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        if (productions_plan_id) {
                            $.ajax({
                                url: site.base_url + 'admin/manufactures/export_excel_production_plan',
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    productions_plan_id: productions_plan_id,
                                    export_excel: 1,
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                                },
                            })
                                .done(function (data) {
                                    if (data.result) {
                                        alert_float('success', data.message);
                                        download(data.filename, data.file);
                                    } else {
                                        alert_float('danger', data.message);
                                    }
                                })
                                .fail(function () {
                                    alert_float('danger', 'errors');
                                });
                        }
                    }
                }
            });
        });

        $(document).on('click', '#agree', function (event) {
            event.preventDefault();
            index = this;
            productions_plan_id = $(this).attr('productions_plan_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_plan_id) {
                $.ajax({
                    url: site.base_url + 'admin/manufactures/agreeProductionsPlan',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_plan_id: productions_plan_id,
                        status: status
                    },
                })
                    .done(function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
                        }
                    })
                    .fail(function (data) {
                        alert_float('danger', 'errors');
                        $(index).removeAttr('disabled');
                    })
            }
        });

        $(document).on('click', '#agree-procedure', function (event) {
            event.preventDefault();
            index = this;
            productions_plan_id = $(this).attr('productions_plan_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_plan_id) {
                $.ajax({
                    url: site.base_url + 'admin/manufactures/agreeProductionsPlanProcedure',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_plan_id: productions_plan_id,
                        status: status
                    },
                })
                    .done(function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
                        }
                    })
                    .fail(function (data) {
                        alert_float('danger', 'errors');
                        $(index).removeAttr('disabled');
                    })
            }
        });

        $(document).on('click', '.status-table li a', function (event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
    });

    function add() {
        $('#modal_payment').html('');
        $.get(admin_url + 'pay_slip/modal').done(function(response) {
            $('#modal_payment').html(response);
            $('#payment_vouchers_coupon').modal({
                backdrop: 'static',
                keyboard: false
            });

            var opt = {
                format: 'd/m/Y',
                timepicker: false,
                scrollInput: false,
                lazyInit: true,
                dayOfWeekStart: 0,
            };
            $('#date_vouchers').datetimepicker(opt);
            $('#accounting_date').datetimepicker(opt);

            init_datepicker();

            init_selectpicker();

        });
    }

</script>
<!-- ./sum note -->