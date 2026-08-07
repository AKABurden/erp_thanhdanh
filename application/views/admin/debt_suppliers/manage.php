<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-debt_suppliers thead tr th {
        text-align: center;
    }

    .table-debt_suppliers tr td:nth-child(2) {
        min-width: 200px;
        white-space: unset;
    }

    .table-debt_suppliers tr td:nth-child(3) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_suppliers tr td:nth-child(4) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_suppliers tr td:nth-child(5) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_suppliers tr td:nth-child(6) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_suppliers tr td:nth-child(7) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_suppliers tr td:nth-child(8) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-debt_suppliers tr td:nth-child(9) {
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
                    <a class="search_person btn btn-info H_action_button option_barcode">
						<?php echo _l('ch_seach_statistical'); ?>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <input type="text" name="suppliers_id" class="hide" id="suppliers_id" value=""><!--
           <input type="text" name="date_start" id="date_start" value="">
           <input type="text" name="date_end" id="date_end" value=""> -->
    <div class="col-md-12">
        <div class="report_debt ">
            <div class="text-center col-md-3 col-xs-3 border-right border-left">
                <h4 class="bold text-muted debt_total">
                    0
                </h4>
                <span style="color:red" class="text-danger">
                            <?= _l('ch_debt_total') ?>
                        </span>
            </div>
            <div class="text-center col-md-3 col-xs-3 border-right">
                <h4 class="bold text-muted debt_30N">
                    0
                </h4>
                <span style="color:red" class="text-danger">
                           <?= _l('ch_debt_30N') ?>
                        </span>
            </div>
            <div class="text-center col-md-3 col-xs-3 border-right">
                <h4 class="bold text-muted debt_30N60N">
                    0
                </h4>
                <span style="color:red" class="text-danger">
                            <?= _l('ch_debt_30N60N') ?>
                        </span>
            </div>
            <div class="text-center col-md-3 col-xs-3 border-right">
                <h4 class="bold text-muted debt_60N">
                    0
                </h4>
                <span style="color:red" class="text-danger">
                            <?= _l('ch_debt_60N') ?>
                        </span>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <br>
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
							_l('supplier'),
							_l('ch_debt_total'),
							_l('ch_total_expenses'),
							// _l('ch_other_expenses'),
							_l('ch_debt_30N'),
							_l('ch_debt_30N60N'),
							_l('ch_debt_60N'),
							_l('ch_total_left'),
						);
						render_datatable($table_data, 'debt_suppliers');
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="view_suppliert_detail"></div>
<script>
    $('.H_filter').click(function (e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    $(function () {
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'suppliers_id': '[name="suppliers_id"]',
        };
        var tAPI = initDataTable('.table-debt_suppliers', admin_url + 'debt_suppliers/table', [0, 1, 2, 3, 4, 5, 6, 7], [0, 1, 2, 3, 4, 5, 6, 7], CustomersServerParams, [0, 'desc']);
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $('' + filterItem).on('change', function () {
                tAPI.ajax.reload();
            });
        });
        $('.table-debt_suppliers').on('draw.dt', function () {
            get_total_debt_limit();
        });
    });

    $('.table-debt_suppliers').on('draw.dt', function () {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $('.debt_total').html(sums.debt_total);
        $('.debt_30N').html(sums.debt_30N);
        $('.debt_30N60N').html(sums.debt_30N60N);
        $('.debt_60N').html(sums.debt_60N);
    });

    var suppliers_id = $('#suppliers_id').val();
    $('.table-debt_suppliers').on('draw.dt', function () {
        var invoiceReportsTable = $(this).DataTable();
        var sums = invoiceReportsTable.ajax.json().sums;
        $('.text-muted.debt').text(sums.debt);
        $('.text-muted.payment').text(sums.payment);
        $('.text-muted.left').text(sums.left);
    });
    var inner_popover_template = '<div class="popover" style="width:1000px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person', function (e) {
        $('.add_contact_person_invoice').popover('hide');
        var dropdown_menu = '\
    <div class="col-md-2 col-xs-2 border-right">\
        <h4 class="bold text-muted debt">\
            0\
        </h4>\
        <span style="color:red" class="text-danger">\
            <?=_l('ch_total_arises')?>\
        </span>\
    </div>\
    <div class="col-md-2 col-xs-2 border-right">\
        <h4 class="bold text-muted payment">\
            0\
        </h4>\
        <span style="color:red" class="text-danger">\
            <?=_l('ch_total_payment')?>\
        </span>\
    </div>\
    <div class="col-md-2 col-xs-2 border-right">\
        <h4 class="bold text-muted left">\
            0\
        </h4>\
        <span style="color:red" class="text-danger">\
            <?=_l('ch_total_left')?>\
        </span>\
    </div>\
            <div class="col-md-6">\
            <?php
			echo render_select('id_suppliers[]', $suppliers, array('id', 'company'), 'ch_chose_suppliers', '', array('data-actions-box' => 1, 'multiple' => true));
			?></div><br>';
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            // trigger: 'focus',
            title: '<?=_l('Thống kê và tìm kiếm')?><button type="button" class="close close_pay">&times;</button>',
            content: function () {
                return dropdown_menu;
            },
            template: inner_popover_template
        });
        init_selectpicker();
        var res = [];
        var suppliers_id = $('#suppliers_id').val();
        res = suppliers_id.split(",");
        if (res[0] == '') {
            res.splice(0, 1);
        }
        // $('[name="id_suppliers[]"]').selectpicker('val',[suppliers_id]);
        $('[name="id_suppliers[]"]').val(res).trigger('change');
        if (suppliers_id == '') {
            $.ajax({
                url: admin_url + 'debt_suppliers/get_total_debt/',
                dataType: 'json',
            })
            .done(function (data) {
                $('.text-muted.debt').text(data.debt);
                $('.text-muted.payment').text(data.payment);
                $('.text-muted.left').text(data.left);
            });
        }
    });
    $(document).on('click', '.close', function (e) {
        $('.search_person').popover('hide');
    });
    $(document).on('change', '[name="id_suppliers[]"]', function () {
        $('#suppliers_id').val($('[name="id_suppliers[]"]').val());
        $('#suppliers_id').change();
        var suppliers_id = $('#suppliers_id').val();
        if (suppliers_id == '') {
            $.ajax({
                url: admin_url + 'debt_suppliers/get_total_debt/',
                dataType: 'json',
            })
            .done(function (data) {
                $('.text-muted.debt').text(data.debt);
                $('.text-muted.payment').text(data.payment);
                $('.text-muted.left').text(data.left);
            });
        } else {
            $('.table-debt_suppliers').on('draw.dt', function () {
                var invoiceReportsTable = $(this).DataTable();
                var sums = invoiceReportsTable.ajax.json().sums;
                $('.text-muted.debt').text(sums.debt);
                $('.text-muted.payment').text(sums.payment);
                $('.text-muted.left').text(sums.left);
            });
        }
    });
    // ngày
    // var ch_daterangepicker = () => {
    //   $('input[name="daterange"]').daterangepicker({
    //     opens: 'left',
    //     autoUpdateInput: false,
    //     isInvalidDate: false,
    //     "locale": {
    //             "format": "DD/MM/YYYY",
    //             "separator": " - ",
    //             "applyLabel": lang_daterangepicker.applyLabel,
    //             "cancelLabel": lang_daterangepicker.cancelLabel,
    //             "fromLabel": lang_daterangepicker.fromLabel,
    //             "toLabel": lang_daterangepicker.toLabel,
    //             "customRangeLabel": lang_daterangepicker.customRangeLabel,
    //             "daysOfWeek": lang_daterangepicker.daysOfWeek,
    //             "monthNames": lang_daterangepicker.monthNames
    //         },
    //   }, function(start, end, label) {
    //   });
    //   $('input[name="daterange"]').val('').datepicker("refresh");
    //   $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
    //       $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    //       $('#date_start').val(picker.startDate.format('YYYY-MM-DD'));
    //       $('#date_end').val(picker.endDate.format('YYYY-MM-DD'));
    //       $('#date_end').change();
    //       $('#date_start').change();
    //   });
    //   $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
    //       $(this).val('');
    //       $('#date_start').val('');
    //       $('#date_end').val('');
    //       $('#date_end').change();
    //       $('#date_start').change();
    //   });
    // };
    //         var inner_popover_template = '<div class="popover" style="width:400px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    //           $(document).on('click','.search_person',function(e){
    //             $('#suppliers_id').val('');
    //             $('#suppliers_id').change();
    //             $('.add_contact_person_invoice').popover('hide');
    //             var dropdown_menu='\
    //             <?php
	//                 echo render_select('id_suppliers',$suppliers,array('id','company'),'ch_chose_suppliers');
	//             ?>
    //               <label for="id_suppliers" class="control-label"><?=_l('ch_chose_date')?></label>\
    //               <div class="input-group date">\
    //                 <input readonly type="text" name="daterange" id="daterange" name="daterange" class="form-control daterange" value="" autocomplete="off">\
    //                 <div class="input-group-addon">\
    //                   <i class="fa fa-calendar calendar-icon"></i>\
    //                 </div>\
    //               </div>\
    //             </div><br>';
    //             $('select[name="id_suppliers"]').selectpicker('refresh');
    //             $(this).popover({
    //               html: true,
    //               container: 'body',
    //               placement: "bottom",
    //               trigger: 'click focus',
    //               // trigger: 'focus',
    //               title:'<?=_l('search')?><button type="button" class="close close_pay">&times;</button>',
    //               content: function() {
    //                 return dropdown_menu;
    //               },
    //               template: inner_popover_template
    //             });
    //             $('#suppliers_id').selectpicker('refresh');
    //             ch_daterangepicker();
    //           });
    // end
    function get_total_debt_limit() {
        var suppliers_id = $('#suppliers_id').val();
        dataString = {suppliers_id: suppliers_id, [csrfData['token_name']]: csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>debt_suppliers/count_debt/",
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
    function suppliert_detail(id) {
        $('#view_client_detail').html('');
        var data = {};
        var filterStatus = $('#filterStatus').val();
        if(filterStatus == 2) {
            data['filterType'] = 2;
        }
        $.get(admin_url + 'debt_suppliers/suppliert_detail/' + id, data).done(function (response) {
            $('#view_suppliert_detail').html(response);
            $('#suppliert_detail').modal('show');
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function (error) {
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
    $(document).on('change', '.checkbox_ch', function () {
        var rows = $('.table-detail_client').find('tbody tr');
        var total = 0;
        var idd = '';
        var count = 0;
        $.each(rows, function () {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                total += parseFloat(checkbox.attr('data-id'));
                idd += checkbox.val() + ',';
                count++;
            }
        });
        if (total > 0) {
            $('.text_wanring').html('<span style="color: red;font-size: 20px" class="bold text-center"><?=_l('tnh_reference_orders')?>:  ' + count + '  -  <?=_l('exchange_amount_value')?>: ' + formatNumber(total) + '</span>');
        } else {
            $('.text_wanring').html('<span style="color: red;font-size: 20px" class="bold text-center"><?=_l('ch_chose_orders')?></span>');
        }
        $('#idd').val(idd);
        $('#totals').val(formatNumber(total));
        $('#votes_total').val(formatNumber(total));
    });
    var tables_pagination_limit = "<?php echo get_option('tables_pagination_limit'); ?>";
    var dt_length_menu_all = "<?php echo _l('dt_length_menu_all'); ?>";
    var dt_lang = <?php echo json_encode(get_datatables_language_array()); ?>;
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust();
    });
    function initDataTableFixedHeader_hau(table, url, notsearchable, notsortable, fnserverparams, defaultorder, fixedColumns) {
        // alert(table);
        var _table_name = table;
        if ($(table).length == 0) {
            return;
        }
        if (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') {
            fnserverparams = []
        }
        // If not order is passed order by the first column
        if (typeof (defaultorder) == 'undefined') {
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
        length_options.sort(function (a, b) {
            return a - b;
        });
        length_options_names.sort(function (a, b) {
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
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                // If tooltips found
                $(nRow).attr('data-title', aData.Data_Title)
                $(nRow).attr('data-toggle', aData.Data_Toggle)
            },
            "initComplete": function (settings, json) {
                var _table = $(table);
                var th_last_child = _table.find('thead th:last-child');
                var th_first_child = _table.find('thead th:first-child');
                if (th_last_child.text().trim() == '<?=_l('opption')?>') {
                    th_last_child.addClass('not-export');
                }
                if (th_first_child.find('input[type="checkbox"]').length > 0) {
                    th_first_child.addClass('not-export');
                }
            },
            "columnDefs": [
                {
                    "render": function (data, type, row) {
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
                }
            ],
            "order": defaultorder,
            "ajax": {
                "url": url,
                "type": "POST",
                "data": function (d) {
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    d[csrfData['token_name']] = csrfData['hash'];
                }
            },
        });
        var tableApi = table.DataTable();
        var hiddenHeadings = $(table).find('th.not_visible');
        $.each(hiddenHeadings, function () {
            tableApi.columns(this.cellIndex).visible(false, false);
        });
        // Fix for hidden tables colspan not correct if the table is empty
        if ($(_table_name).is(':hidden')) {
            $(_table_name).find('.dataTables_empty').attr('colspan', $(_table_name).find('thead th').length);
        }
        return tableApi;
    }
    $('body').on('hidden.bs.modal', '#suppliert_detail', function () {
        tAPI.draw('page');
    });
</script>
