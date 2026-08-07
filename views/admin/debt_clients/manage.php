<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-debt_client thead tr th {
        text-align: center;
    }

    .table-debt_client tr td:nth-child(2) {
        min-width: 200px;
        white-space: unset;
    }

    .table-debt_client tr td:nth-child(3) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_client tr td:nth-child(4) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_client tr td:nth-child(5) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_client tr td:nth-child(6) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_client tr td:nth-child(7) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_client tr td:nth-child(8) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_client tr td:nth-child(9) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
        background: #f1eab5;
        font-weight: bold;
    }

    .popover {
        max-width: 2000px;
        height: 140px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="pull-right mright5 H_border">
                    <!-- sum note -->
                    <a class="btn btn-info pull-right mleft5 H_action_button option_barcode" data-toggle="collapse" data-target="#searchStatistics" aria-expanded="true">
                        <i class="fa fa-filter"></i>
                        <?= lang('tnh_seach_statistical') ?>
                    </a>
                    <!-- ./sum note -->
                </div>
                <div class="clearfix"></div>
            </div>
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
                        <div class="col-md-12">
                            <div class="report_debt ">
                                <div class="text-center col-md-3 col-xs-3 border-right border-left">
                                    <h4 class="bold text-muted debt_total">
                                        0
                                    </h4>
                                    <span style="color:red" class="text-danger">
                                        <?= _l('Công nợ đầu kỳ') ?>
                                    </span>
                                </div>
                                <div class="text-center col-md-2 col-xs-2 border-right border-left">
                                    <h4 class="bold text-muted debt_totalps">
                                        0
                                    </h4>
                                    <span style="color:red" class="text-danger">
                                        <?= _l('Công nợ phát sinh') ?>
                                    </span>
                                </div>
                                <div class="text-center col-md-2 col-xs-2 border-right border-left">
                                    <h4 class="bold text-muted returns">
                                        0
                                    </h4>
                                    <span style="color:red" class="text-danger">
                                        <?= _l('Khoảng giảm trừ') ?>
                                    </span>
                                </div>
                                <div class="text-center col-md-2 col-xs-2 border-right border-left">
                                    <h4 class="bold text-muted pay">
                                        0
                                    </h4>
                                    <span style="color:red" class="text-danger">
                                        <?= _l('Tổng thu') ?>
                                    </span>
                                </div>
                                <div class="text-center col-md-2 col-xs-2 border-right border-left">
                                    <h4 class="bold text-muted lefts">
                                        0
                                    </h4>
                                    <span style="color:red" class="text-danger">
                                        <?= _l('Còn lại') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <br>
                        <br>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
											<?= _l('leads_all') ?>(<span class="all_debt">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
											<?= _l('Vượt mức công nợ') ?>(<span class="all_debt_limit">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
											<?= _l('Vượt mức thời gian thanh toán') ?>(<span class="all_debt_limit_day">0</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <div class="clearfix"></div>
                        <?php $table_data = array(
                            _l('#'),
                            _l('client'),
                            _l('Đầu kỳ'),
                            _l('PS trong kỳ'),
                            _l('Khoảng giảm trừ'),
                            _l('Tổng thu'),
                            _l('ch_total_left'),
                        );
                        render_datatable($table_data, 'debt_client');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="view_client_detail"></div>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
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
    $(function() {
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'suppliers_id': '[name="suppliers_id"]',
            //   sum note
            'clients_id': '[name="clients_id"]',
            'start_date_search': '[name="start_date_search"]',
            'end_date_search': '[name="end_date_search"]',
            //   ./sum note
        };
        tAPI = initDataTable('.table-debt_client', admin_url + 'debt_clients/table', [0], [0], CustomersServerParams, [0, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.ajax.reload();
            });
        });
    });
    $('.table-debt_client').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $('.debt_total').html(sums.begin);
        $('.debt_totalps').html(sums.debt_total);
        $('.pay').html(sums.pay);
        $('.lefts').html(sums.lefts);
        $('.returns').html(sums.returns);
        get_total_debt_limit();
    });

    function get_total_debt_limit() {
        var clients_id = $('#clients_id').val();
        dataString = {clients_id: clients_id, [csrfData['token_name']]: csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>debt_clients/count_debt/",
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                $('.all_debt').html(data.all);
                $('.all_debt_limit').html(data.count_limit);
                $('.all_debt_limit_day').html(data.all_debt_limit_day);
            }
        });
    }

    function client_detail(id) {
        $('#view_client_detail').html('');

        var data = {};
        var filterStatus = $('#filterStatus').val();
        if(filterStatus == 2) {
            data['filterType'] = 2;
        }
        $.get(admin_url + 'debt_clients/client_detail/' + id, data).done(function(response) {
            $('#view_client_detail').html(response);
            $('#client_detail').modal('show');
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };

    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr = nStr.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        return nStr;
    };
    $(document).on('change', '.checkbox_ch', function() {
        var rows = $('.table-detail_client').find('tbody tr');
        var total = 0;
        var count = 0;
        var idd = '';
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(1)).find('input');
            if (checkbox.prop('checked') === true) {
                count++;
                total += parseFloat(checkbox.attr('data-id'));
                idd += checkbox.val() + ',';
            }
        });
        if (total > 0) {
            $('.text_wanring').html('<span style="color: red;font-size: 20px" class="bold text-center"><?= _l('tnh_reference_orders') ?>:  ' + count + '  -  <?= _l('exchange_amount_value') ?>: ' + formatNumber(total) + '</span>');
        } else {
            $('.text_wanring').html('<span style="color: red;font-size: 20px" class="bold text-center"><?= _l('ch_chose_orders') ?></span>');
        }
        $('#idd').val(idd);
        $('#totals').val(formatNumber(total));
        $('#votes_total').val(formatNumber(total));
    });
    var tables_pagination_limit = "<?php echo get_option('tables_pagination_limit'); ?>";
    var dt_length_menu_all = "<?php echo _l('dt_length_menu_all'); ?>";
    var dt_lang = <?php echo json_encode(get_datatables_language_array()); ?>;
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

    function initDataTableFixedHeader_hau(table, url, notsearchable, notsortable, fnserverparams, defaultorder, fixedColumns) {
        // alert(table);
        var _table_name = table;
        if ($(table).length == 0) {
            return;
        }
        if (fnserverparams == 'undefined' || typeof(fnserverparams) == 'undefined') {
            fnserverparams = []
        }
        // If not order is passed order by the first column
        if (typeof(defaultorder) == 'undefined') {
            defaultorder = [
                [0, 'ASC']
            ];
        } else {
            if (defaultorder.length == 1) {
                defaultorder = [defaultorder]
            }
        }

        var length_options = [10, 25, 50, 100];
        var length_options_names = [10, 25, 50, 100];

        tables_pagination_limit = parseFloat(tables_pagination_limit);

        if ($.inArray(tables_pagination_limit, length_options) == -1) {
            length_options.push(tables_pagination_limit)
            length_options_names.push(tables_pagination_limit)
        }

        length_options.sort(function(a, b) {
            return a - b;
        });
        length_options_names.sort(function(a, b) {
            return a - b;
        });

        length_options.push(-1);
        length_options_names.push(dt_length_menu_all);

        var table = $('body').find(table).dataTable({
            fixedColumns: fixedColumns || false,
            "sScrollX": "100%",
            "sScrollXInner": "100%",
            "bScrollCollapse": true,
            scrollY: '80vh',
            scrollCollapse: true,
            "language": dt_lang,
            "processing": true,
            "retrieve": true,
            "serverSide": true,
            'paginate': true,
            'searchDelay': 700,
            "bDeferRender": true,
            "responsive": false,
            "autoWidth": false,
            dom: "<'mbot25'B><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
            "pageLength": tables_pagination_limit,
            "lengthMenu": [length_options, length_options_names],
            "columnDefs": [{
                "searchable": false,
                "targets": 2,
            }, {
                "sortable": false,
                "targets": 2
            }],
            "fnCreatedRow": function(nRow, aData, iDataIndex) {
                // If tooltips found
                $(nRow).attr('data-title', aData.Data_Title)
                $(nRow).attr('data-toggle', aData.Data_Toggle)
            },
            "initComplete": function(settings, json) {
                var _table = $(table);
                var th_last_child = _table.find('thead th:last-child');
                var th_first_child = _table.find('thead th:first-child');
                if (th_last_child.text().trim() == '<?= _l('opption') ?>') {
                    th_last_child.addClass('not-export');
                }
                if (th_first_child.find('input[type="checkbox"]').length > 0) {
                    th_first_child.addClass('not-export');
                }
            },
            "columnDefs": [{
                "render": function(data, type, row) {
                    return '';
                },
                "className": 'details-control',
                "targets": 0,
                "name": 'records',
                'orderable': false,
                'width': '5px'
            }, {
                "targets": 1,
                'orderable': false,
            }],
            "order": defaultorder,
            "ajax": {
                "url": url,
                "type": "POST",
                "data": function(d) {
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    d[csrfData['token_name']] = csrfData['hash'];
                }
            },
        });

        var tableApi = table.DataTable();
        var hiddenHeadings = $(table).find('th.not_visible');
        $.each(hiddenHeadings, function() {
            tableApi.columns(this.cellIndex).visible(false, false);
        });
        // Fix for hidden tables colspan not correct if the table is empty
        if ($(_table_name).is(':hidden')) {
            $(_table_name).find('.dataTables_empty').attr('colspan', $(_table_name).find('thead th').length);
        }

        return tableApi;
    }

    // $('body').on('hidden.bs.modal', '#client_detail', function() {
    //     tAPI.draw('page');
    // });
    $(document).ready(function() {
        ajaxSelectParams('#clients_id', 'admin/clients/searchCustomers', 0, true, true);
    });

    $(document).on('click', 'table.table-detail_client tbody td.details-control', function() {
        var tr = $(this).closest('tr');
        var row = tAPIss.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            format(row.data(), row, tr);
            // row.child( format(row.data()) ).show();
            // tr.addClass('shown');
        }
    });
</script>