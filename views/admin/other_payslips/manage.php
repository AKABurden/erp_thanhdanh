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
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(4) {
        min-width: 200px;
        white-space: unset;

    }

    .table-other_payslips tr td:nth-child(5) {
        min-width: 100px;
        white-space: unset;
    }

    .table-other_payslips tr td:nth-child(6) {
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(7) {
        min-width: 90px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(8) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-other_payslips tr td:nth-child(9) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-other_payslips tr td:nth-child(10) {
        min-width: 120px;
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
                <a class="search_person btn btn-info pull-right mleft5 H_action_button option_barcode">
                    <span style="font-size: 16px;margin-bottom: 3px;" class="lnr lnr-funnel"></span>
                    <?php echo _l('ch_seach_statistical'); ?>
                </a>
                <?php if (has_permission('other_payslips', '', 'create')) { ?>
                    <div class="line-sp"></div>
                    <a href="" onclick="new_other_payslips(); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?></a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
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
                                            <?= _l('leads_all') ?>(<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                            <?= _l('ch_pay_client') ?>(<span class="pay_client">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                            <?= _l('ch_pay_suppliers') ?>(<span class="pay_suppliers">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="5">
                                            <?= _l('ch_pay_staff') ?>(<span class="pay_staff">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="6">
                                            <?= _l('ch_pay_other') ?>(<span class="pay_other">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="7">
                                            <?= _l('ch_pay_tscd') ?>(<span class="pay_tscd">0</span>)
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
                            _l('c_object_nhan'),
                            _l('ch_objects'),
                            _l('ch_type_of_document'),
                            _l('ch_code_p'),
                            _l('ch_HTTT'),
                            _l('ch_costs'),
                            _l('expense_add_edit_amount'),
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
<div id="view_other_payslips"></div>
<div id="view_other_payslips_data"></div>
<div id="export_different_data"></div>

<div id="return_suppliers_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    function view_return_suppliers(id = null, edit = false) {
        $('#return_suppliers_data').html('');
        $.get(admin_url + 'return_suppliers/int_return_suppliers_view/' + id).done(function(response) {
            $('#return_suppliers_data').html(response);
            $('#return_suppliers').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew('tblreturn_suppliers', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#return_suppliers', function() {
        $('#return_suppliers_data').html('');
        tAPI.draw('page');
    });
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

    function new_other_payslips() {
        $('#view_other_payslips').html('');
        $.get(admin_url + 'other_payslips/other_payslips/').done(function(response) {
            $('#view_other_payslips').html(response);
            $('#other_payslips').modal('show');
            init_editor();
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

    function edit_other_payslips(id) {
        $('#view_other_payslips').html('');
        $.get(admin_url + 'other_payslips/other_payslips/' + id).done(function(response) {
            $('#view_other_payslips').html(response);
            $('#other_payslips').modal('show');
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
            'objects_idd': '[name="objects_idd"]',
            'objects_ids': '[name="objects_ids"]',
            'objects_texts': '[name="objects_texts"]',
            'search_date': '[name="search_date"]',
        };
        tAPI = initDataTableCustom('.table-other_payslips', admin_url + 'other_payslips/table', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>, fixedColumns = {
            leftColumns: 2,
            rightColumns: 1
        });
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
            $('.text-muted.payment').text(sums.payment);
            $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
            $('.DTFC_LeftFootWrapper').css("background", "#ffff");
            $('.dataTables_scrollFoot').find('tfoot td').eq(8).html('<div class="text-right">' + sums.payment + '</div>');
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
    var inner_popover_template = '<div class="popover" style="width:1300px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person', function(e) {
        $('.add_contact_person_invoice').popover('hide');
        var dropdown_menu = '\
            <div class="col-md-2 col-xs-2 border-right">\
                <h4 class="bold text-muted all_orther">\
                    0\
                </h4>\
                <span style="color:red" class="text-danger">\
                    <?= _l('Tổng phiếu') ?>\
                </span>\
            </div>\
            <div class="col-md-2 col-xs-2 border-right">\
                <h4 class="bold text-muted payment">\
                    0\
                </h4>\
                <span style="color:red" class="text-danger">\
                    <?= _l('Tổng tiền') ?>\
                </span>\
            </div>\
            <div class="col-md-2">\
            <?php $list_objectss = array(array('id' => 1, 'name' => _l('ch_IN_client')), array('id' => 2, 'name' => _l('ch_IN_suppliers')), array('id' => 3, 'name' => _l('ch_IN_staff')),  array('id' => 4, 'name' => _l('ch_IN_other')),); ?>\
            <?php echo render_select('objects_idd', $list_objectss, array('id', 'name'), 'Loại đối tượng'); ?>\
            </div>\
            <div class="col-md-3 append_id_objects">\
            <div class="form-group id ">\
                    <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                    <input data-placeholder="<?= _l('ch_list_objects') ?>" name="objects_ids" style="width: 100%" id="objects_ids">\
                </div>\
            </div>\
            <div class="col-md-3">\
            <div class="form-group">\
               <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>\
               <div class="input-group">\
                  <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">\
                  <div class="input-group-addon">\
                     <i class="fa fa-calendar calendar-icon"></i>\
                  </div>\
               </div>\
            </div>\
            </div><br>';
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            // trigger: 'focus',
            title: '<?= _l('tnh_seach_statistical') ?><button type="button" class="close close_pay">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template
        });

        init_selectpicker();
        ch_daterangepicker();
        ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", 0);
        tAPI.draw('page');
        $("#objects_idd").change(function() {
            $('#objects_ids').selectpicker('refresh');
            var id = $('#objects_idd').val();
            var id_objects_id = 0;
            if (id == 1) {
                var html = '<div class="form-group id ">\
                              <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                              <input data-placeholder="Khách hàng" name="objects_ids" style="width: 100%" value="' + id_objects_id + '" id="objects_ids">\
                          </div>';
                $('.append_id_objects').html(html);
                ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            } else if (id == 2) {
                var html = '<div class="form-group id ">\
                              <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                              <input data-placeholder="Nhà cung cấp" name="objects_ids" style="width: 100%" value="' + id_objects_id + '" id="objects_ids">\
                          </div>';
                $('.append_id_objects').html(html);
                ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            } else if (id == 3) {
                var html = '<div class="form-group id ">\
                              <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                              <input data-placeholder="Nhân viên" name="objects_ids" style="width: 100%" value="' + id_objects_id + '" id="objects_ids">\
                          </div>';
                $('.append_id_objects').html(html);
                ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            } else if (id == 4) {

                var html1 = '<div class="form-group id">\
                              <label for="objects_texts" class="control-label"><small class="req text-danger">* </small><?= _l('ch_list_objects') ?></label>\
                              <input type="text" id="objects_texts" name="objects_texts" class="form-control objects_texts" value="<?= (!empty($items) ? $items->objects_texts : '') ?>">\
                      </div>';
                $('.append_id_objects').html(html1);
            }
        });
    });
    $(document).on('click', '.close', function(e) {
        $('.search_person').popover('hide');
    });
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

    function view_other_payslips(id) {
        $('#view_other_payslips_data').html('');
        $.get(admin_url + 'other_payslips/view_modal/' + id).done(function(response) {
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
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_export_different', function() {
        $('#export_different_data').html('');
    });
</script>