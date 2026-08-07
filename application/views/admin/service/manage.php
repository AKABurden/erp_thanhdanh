<?php init_head(); ?>
<style>
    .table-service img {
        height: 20px;
        width: 20px;
    }

    .table-service tr td:nth-child(1) {
        text-align: center;
    }

    .table-service tr td:nth-child(2) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-service tr td:nth-child(3) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-service tr td:nth-child(4) {
        min-width: 250px;
        white-space: unset;
    }

    .table-service tr td:nth-child(5) {
        min-width: 110px;
        white-space: unset;
        text-align: right;
    }

    .table-service tr td:nth-child(6) {
        min-width: 110px;
        white-space: unset;
        text-align: right;
    }

    .table-service tr td:nth-child(7) {
        min-width: 110px;
        white-space: unset;
        text-align: right;
    }

    .table-service tr td:nth-child(8) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-service tr td:nth-child(9) {
        min-width: 110px;
        white-space: unset;
        text-align: right;
    }

    .table-service tr td:nth-child(10) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-service tr td:nth-child(11) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-service tr td:nth-child(12) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-service tr td:nth-child(13) {
        min-width: 160px;
        white-space: unset;
        text-align: center;
    }

    
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if (has_permission('service', '', 'create')) { ?>
                    <div class="line-sp"></div>
                    <a href="<?= admin_url('service/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?></a>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- <?php echo render_select('suppliers_id', $data_supplier, array('id', 'company'), 'supplier'); ?> -->
                <label for="suppliertid" class="control-label"><?php echo _l('ch_service_suppliers'); ?></label>
                <input data-placeholder="<?= _l('ch_service_suppliers') ?>" value="0" name="suppliers_id" style="width: 100%" id="suppliers_id">
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
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php $table_data = array(
                            _l('#'),
                            _l('ch_date_p'),
                            _l('ch_code_p'),
                            _l('supplier'),
                            _l('ch_tax_value'),
                            _l('cong__discount'),
                            _l('total_price'),
                            _l('dt_type_service'),
                            _l('invoice_dt_table_heading_status'),
                            _l('ch_total_expenses'),
                            _l('ch_status'),
                            _l('ch_addedfrom'),
                        );
                        array_push($table_data, _l('ch_option'));
                        render_datatable($table_data, 'service');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var tAPI;
    $(function() {
        var CustomersServerParams = {
            'date': '[name="search_date"]',
            'suppliers': '[name="suppliers_id"]',
        };
        tAPI = initDataTableCustom('.table-service', admin_url + 'service/table', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>, fixedColumns = {
            leftColumns: 3,
            rightColumns: 1
        });
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.ajax.reload();
            });
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
                url: "<?= admin_url() ?>service/update_status",
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
    search_daterangepicker();

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
    $(function(e) {
        ajaxSelectCallBack_hau($('#suppliers_id'), "admin/service/SearchSuppliert", $('#suppliers_id').val());
    });
</script>