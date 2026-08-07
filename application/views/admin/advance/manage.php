<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-other_payslips img {
        height: 20px;
        width: 20px;
    }

    .table-other_payslips thead tr th {
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(1) {
        min-width: 90px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(2) {
        min-width: 110px;
        white-space: unset;
        text-align: center;

    }

    .table-other_payslips tr td:nth-child(3) {
        min-width: 130px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(4) {
        min-width: 150px;
        white-space: unset;

    }

    .table-other_payslips tr td:nth-child(5) {
        min-width: 100px;
        white-space: unset;
    }

    .table-other_payslips tr td:nth-child(6) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-other_payslips tr td:nth-child(7) {
        min-width: 90px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(8) {
        min-width: 150px;
        white-space: unset;
    }

    .table-other_payslips tr td:nth-child(9) {
        min-width: 150px;
        white-space: unset;
    }

    .table-other_payslips tr td:nth-child(10) {
        min-width: 160px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(11) {
        min-width: 150px;
        white-space: unset;
    }

    .table-other_payslips tr td:nth-child(12) {
        min-width: 160px;
        white-space: unset;
    }

    .table-other_payslips tbody .dropdown {
        text-align: center;
    }

    .popover {
        max-width: 2500px;
        height: 140px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <!-- <a class="search_person btn btn-info pull-right mleft5 H_action_button option_barcode">
                    <span style="font-size: 16px;margin-bottom: 3px;" class="lnr lnr-funnel"></span>
                    <?php echo _l('ch_seach_statistical'); ?>
                </a> -->
                <!-- <?php if (has_permission('other_payslips', '', 'create')) { ?> -->
                <div class="line-sp"></div>
                <a href="" onclick="new_advance(); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <!-- <?php } ?> -->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <label for="staffid" class="control-label"><?php echo _l('Người tạm ứng'); ?></label>
                <input data-placeholder="<?= _l('Người tạm ứng') ?>" value="" name="staffid" style="width: 100%" id="staffid">
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
                                            <?= _l('leads_all') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                            <?= _l('Chưa tất toán') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('Đã tất toán') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <div class="clearfix mtop20"></div>
                        <?php $table_data = array(
                            _l('c_date_lap'),
                            _l('c_code_ctu'),
                            _l('ch_objects'),
                            _l('ch_HTTT'),
                            _l('ch_costs'),
                            _l('expense_add_edit_amount'),
                            _l('ch_advance_payment'),
                            _l('ch_addedfrom'),
                            _l('ch_note_pay_slips'),
                            _l('ch_option'),
                        );
                        render_datatable_tfoot_ch($table_data, 'other_payslips');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="view_advance"></div>
<div id="view_other_payslips_data"></div>
<div id="view_other_payslips_coupon"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var ch_daterangepicker = () => {
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

    function new_advance() {
        $('#view_advance').html('');
        $.get(admin_url + 'advance/advance/').done(function(response) {
            $('#view_advance').html(response);
            $('#advance').modal('show');
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#advance', function() {
        $('#view_advance').html('');
    });

    function edit_other_payslips(id) {
        $('#view_advance').html('');
        $.get(admin_url + 'advance/advance/' + id).done(function(response) {
            $('#view_advance').html(response);
            $('#advance').modal('show');
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
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
    $(function() {

        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'staffid': '[name="staffid"]',
            'search_date': '[name="search_date"]',
        };
        tAPI = initDataTableCustom('.table-other_payslips', admin_url + 'advance/table', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>);
        // var tAPI = initDataTable('.table-other_payslips', admin_url+'other_payslips/table', [0], [0], CustomersServerParams,[0, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
        $('.table-other_payslips').on('draw.dt', function() {
            var invoiceReportsTable = $(this).DataTable();
            var sums = invoiceReportsTable.ajax.json().sums;
            $('.text-muted.all_orther').text(sums.all);
            $('.text-muted.payment').text(sums.payment)
            $('.dataTables_scrollFoot').find('tfoot td').eq(1).html('Tổng');
            $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
            $('.DTFC_LeftFootWrapper').css("background", "#ffff");
            $('.dataTables_scrollFoot').find('tfoot td').eq(5).html('<div class="text-right">' + sums.payment + '</div>');
            $('.dataTables_scrollFoot').find('tfoot td').eq(6).html('<div class="text-right">' + sums.id_payment + '</div>');
            get_total_limit();
        });
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
                url: "<?= admin_url() ?>other_payslips/update_status",
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

    function view_pay_slip(id) {
        $('#view_pay_slip_data').html('');
        $.get(admin_url + 'pay_slip/electronic_bill/' + id).done(function(response) {
            $('#view_pay_slip_data').html(response);
            $('#view_pay_slip').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#other_payslips', function() {
        $('#view_other_payslips').html('');
    });
    $('body').on('hidden.bs.modal', '#views_import', function() {
        $('#import_data').html('');
        $('.table-import').DataTable().ajax.reload();
    });

    function get_total_limit() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>other_payslips/count_all/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.pay_client').html(data.pay_client);
                $('.pay_suppliers').html(data.pay_suppliers);
                $('.pay_staff').html(data.pay_staff);
                $('.pay_other').html(data.pay_other);
                $('.pay_tscd').html(data.pay_tscd);
            }
        });
    }
    $(document).on('change', '#objects_idd', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', '#objects_ids', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', '#search_date', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', '#objects_texts', function(e) {
        tAPI.draw('page');
    });

    function ajaxSelectCallBacks(element, url, id, types = '') {
        console.log(id);
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + $('#objects_idd').val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#objects_idd').val(),
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
                formatResult: repoFormatSelections,
                formatSelection: repoFormatSelections,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#objects_idd').val(),
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
                formatResult: repoFormatSelections,
                formatSelection: repoFormatSelections,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }

    function repoFormatSelections(state) {
        var id = $('#objects').val();
        if (id == 3) {
            return state.text;
        }
        return '[' + state.code_client + '] ' + state.text;
    }

    function view_advance(id) {
        $('#view_other_payslips_data').html('');
        $.get(admin_url + 'advance/view_modal/' + id).done(function(response) {
            $('#view_other_payslips_data').html(response);
            $('#view_other_payslips_view').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_other_payslips_view', function() {
        $('#view_other_payslips_data').html('');
    });

    function new_other_payslips_coupon(id) {
        $('#view_other_payslips_coupon').html('');
        $.get(admin_url + 'advance/other_payslips_coupon/' + id).done(function(response) {
            $('#view_other_payslips_coupon').html(response);
            $('#other_payslips_coupon').modal('show');
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
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
    function ajaxSelectCallBack_hau(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
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
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
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
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    ajaxSelectCallBack_hau($('#staffid'), "admin/suggestion/SearchStaff", 0);

    search_daterangepicker();

</script>