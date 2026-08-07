<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var salesChart;
    var groupsChart;
    var paymentMethodsChart;
    var customersTable;
    var report_from = $('input[name="report-from"]');
    var supplierts = $('#supplierts');
    var report_to = $('input[name="report-to"]');
    var report_general_purchase_report = $('#general-purchase-report');
    var report_from_choose = $('#report-time');
    var detail_purchase_report = $('#detail-purchase-report');
    var detail_purchase_order_report = $('#detail-purchase_order-report');
    var general_purchase_detail_report = $('#general-purchase-detail-report');
    var general_synthetic_purchase_report = $('#general-synthetic-purchase-report');
    var general_detail_import_report = $('#general-detail-import-report');
    var ch_type_items = $('#ch_type_items');
    var items = $('#items');
    var date_range = $('#date-range');
    var to_pay_debt_report = $('#to_pay_debt-report');
    var detail_debt_report = $('#detail_debt-report');
    var fnServerParams = {
        "search_id_suppliers": '[name="search_id_suppliers[]"]',
        "type_items": '[name="type_items"]',
        "custom_item_select": '[name="custom_item_select"]',
        "report_months": '[name="months-report"]',
        "report_from": '[name="report-from"]',
        "report_to": '[name="report-to"]',
        "report_currency": '[name="currency"]',
        "invoice_status": '[name="invoice_status"]',
        "estimate_status": '[name="estimate_status"]',
        "sale_agent_invoices": '[name="sale_agent_invoices"]',
        "sale_agent_items": '[name="sale_agent_items"]',
        "sale_agent_estimates": '[name="sale_agent_estimates"]',
        "proposals_sale_agents": '[name="proposals_sale_agents"]',
        "proposal_status": '[name="proposal_status"]',
        "credit_note_status": '[name="credit_note_status"]',
        "type_import": '[name="type_import"]',
    }
    $('#custom_item_select').on('change', function (e) {
        var currentQuantityInput = $(e.currentTarget);
        var type = currentQuantityInput.select2('data').type;
        $('#type_items').val(type);
        gen_reports();
    });
    $(function () {

        $('select[name="search_id_suppliers[]"],select[name="currency"],select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_items"],select[name="sale_agent_estimates"],select[name="payments_years"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"],select[name="type_import"]').on('change', function () {
            gen_reports();
        });

        report_from.on('change', function () {
            var val = $(this).val();
            var report_to_val = report_to.val();
            if (val != '') {
                report_to.attr('disabled', false);
                if (report_to_val != '') {
                    gen_reports();
                }
            } else {
                report_to.attr('disabled', true);
            }
        });

        report_to.on('change', function () {
            var val = $(this).val();
            if (val != '') {
                gen_reports();
            }
        });
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('purchases/SearchItems') ?>", 0);
        $('select[name="months-report"]').on('change', function () {
            var val = $(this).val();
            report_to.attr('disabled', true);
            report_to.val('');
            report_from.val('');
            if (val == 'custom') {
                date_range.addClass('fadeIn').removeClass('hide');
                return;
            } else {
                if (!date_range.hasClass('hide')) {
                    date_range.removeClass('fadeIn').addClass('hide');
                }
            }
            gen_reports();
        });
        $('.table-general-purchase-report').on('draw.dt', function () {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.total_s').html(sums.total);
            $(this).find('tfoot td.quantity').html(sums.quantity);
            $(this).find('tfoot td.total_return').html(sums.total_return);
            $(this).find('tfoot td.quantity_return').html(sums.quantity_return);
            $(this).find('tfoot td.promotion_expected').html(sums.promotion_expected);
            $(this).find('tfoot td.subtotal').html(sums.subtotal);
        });
        $('.table-detail-purchase-report').on('draw.dt', function () {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.total_quantity').html(sums.total_quantity);
            $(this).find('tfoot td.subtotals').html(sums.subtotals);
            $(this).find('tfoot td.total_amount').html(sums.amount);
            $(this).find('tfoot td.total_tax').html(sums.tax);
            $(this).find('tfoot td.total_pro').html(sums.pro);
        });
        $('.table-to_pay_debt-report').on('draw.dt', function () {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.debt_start').html(sums.debt_start);
            $(this).find('tfoot td.pay_start').html(sums.pay_start);
            $(this).find('tfoot td.debt').html(sums.debt);
            $(this).find('tfoot td.pay').html(sums.pay);
            $(this).find('tfoot td.debt_end').html(sums.debt_end);
            $(this).find('tfoot td.pay_end').html(sums.pay_end);
        });
        $('.table-payments-received-report').on('draw.dt', function () {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
            $(this).find('tfoot td.total').html(sums.total_amount);
        });

        $('.table-general-purchase-detail-report').on('draw.dt', function () {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.quantityyc').html(sums.quantityyc);
            $(this).find('tfoot td.quantitydt').html(sums.quantitydt);
            $(this).find('tfoot td.quantitycl').html(sums.quantitycl);
        });

        $('.table-general-synthetic-purchase-report').on('draw.dt', function () {
            var tableSyntheticReport = $(this).DataTable();
            var sums = tableSyntheticReport.ajax.json();
            grand_total_amount = sums.grand_total_amount;
            grand_total_quantity = sums.grand_total_quantity;
            grand_total_quantity_purchase = sums.grand_total_quantity_purchase;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.quantityyc').html(tnhFormatNumber(grand_total_quantity));
            $(this).find('tfoot td.quantitydt').html(tnhFormatNumber(grand_total_quantity_purchase));
            $(this).find('tfoot td.grand_total').html(tnhFormatMoney(grand_total_amount));
        });

        $('.table-general-detail-import-report').on('draw.dt', function () {
            var tableSyntheticReport = $(this).DataTable();
            var sums = tableSyntheticReport.ajax.json();
            grand_total_amount = sums.grand_total_amount;
            grand_total_quantity = sums.grand_total_quantity;
            grand_total_quantity_purchase = sums.grand_total_quantity_purchase;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.quantityyc').html(tnhFormatNumber(grand_total_quantity));
            $(this).find('tfoot td.quantitydt').html(tnhFormatNumber(grand_total_quantity_purchase));
            $(this).find('tfoot td.grand_total').html(tnhFormatMoney(grand_total_amount));
        });
    });

    function add_common_footer_sums(table, sums) {
        table.find('tfoot').addClass('bold');
        table.find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
        table.find('tfoot td.subtotal').html(sums.subtotal);
        table.find('tfoot td.total').html(sums.total);
        table.find('tfoot td.total_tax').html(sums.total_tax);
        table.find('tfoot td.discount_total').html(sums.discount_total);
        table.find('tfoot td.adjustment').html(sums.adjustment);
    }

    function init_report(e, type) {
        $('.report_chart').addClass('hide');
        var report_wrapper = $('#report');
        report_from_choose.addClass('hide');
        items.addClass('hide');

        if (report_wrapper.hasClass('hide')) {
            report_wrapper.removeClass('hide');
        }

        $('head title').html($(e).text().toUpperCase());
        $('.title_ch').html($(e).text().toUpperCase());
        $('.customers-group-gen').addClass('hide');
        report_general_purchase_report.addClass('hide');
        detail_purchase_report.addClass('hide');
        detail_purchase_order_report.addClass('hide');
        general_purchase_detail_report.addClass('hide');
        general_synthetic_purchase_report.addClass('hide');
        general_detail_import_report.addClass('hide');
        supplierts.addClass('hide');
        ch_type_items.addClass('hide');
        to_pay_debt_report.addClass('hide');
        detail_debt_report.addClass('hide');
        $('#income-years').addClass('hide');
        $('.chart-income').addClass('hide');
        $('.chart-payment-modes').addClass('hide');

        $('select[name="months-report"]').selectpicker('val', 'this_month');
        // Clear custom date picker
        report_to.val('');
        report_from.val('');
        $('#currency').removeClass('hide');
        if (type == 'general-purchase-report') {
            report_general_purchase_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            items.removeClass('hide');
        } else if (type == 'detail-purchase-report') {
            detail_purchase_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            items.removeClass('hide');
        } else if (type == 'detail-purchase_order-report') {
            detail_purchase_order_report.removeClass('hide');
            supplierts.removeClass('hide');
            items.removeClass('hide');
            ch_type_items.removeClass('hide');
            report_from_choose.removeClass('hide');
        } else if (type == 'to_pay_debt-report') {
            to_pay_debt_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            supplierts.removeClass('hide');
        } else if (type == 'detail_debt-report') {
            detail_debt_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            items.removeClass('hide');
            supplierts.removeClass('hide');
        } else if (type == 'general-purchase-detail-report') {
            general_purchase_detail_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            items.removeClass('hide');
        } else if (type == 'general-synthetic-purchase-report'){
            general_synthetic_purchase_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            items.removeClass('hide');
        } else if (type == 'general-detail-import-report'){
            general_detail_import_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            items.removeClass('hide');
        }
        gen_reports();
    }


    function report_general_synthetic_purchase_report() {
        if ($.fn.DataTable.isDataTable('.table-general-synthetic-purchase-report')) {
            $('.table-general-synthetic-purchase-report').DataTable().destroy();
        }
        initDataTableReport('.table-general-synthetic-purchase-report', admin_url + 'reports/general_synthetic_purchase_report', false, [0,1,2,3,4,5,6,7,8,9,10,11], fnServerParams, []);
    }

    function report_general_detail_import_report() {
        if ($.fn.DataTable.isDataTable('.table-general-detail-import-report')) {
            $('.table-general-detail-import-report').DataTable().destroy();
        }
        initDataTableReport('.table-general-detail-import-report', admin_url + 'reports/general_detail_import_report', false, [0,1,2,3,4,5,6,7,8,9,10,11], fnServerParams, []);
    }

    // Generate customers report

    function report_general_purchase_detail_report() {
        if ($.fn.DataTable.isDataTable('.table-general-purchase-detail-report')) {
            $('.table-general-purchase-detail-report').DataTable().destroy();
        }
        initDataTableReport('.table-general-purchase-detail-report', admin_url + 'reports/general_purchase_detail_report', false, false, fnServerParams, [0, 'asc']);
    }

    function report_general_purchase() {
        if ($.fn.DataTable.isDataTable('.table-general-purchase-report')) {
            $('.table-general-purchase-report').DataTable().destroy();
        }
        initDataTableReport('.table-general-purchase-report', admin_url + 'reports/general_purchase_report', false, false, fnServerParams, [0, 'asc']);
    }

    // $('.table-general-purchase-report').on('draw.dt', function() {
    //     var paymentReceivedReportsTable = $(this).DataTable();
    //     var title_excel = paymentReceivedReportsTable.ajax.json().title_excel;
    //     if(title_excel) {
    //         $('table.table-general-purchase-report').attr('title_excel', JSON.stringify(title_excel));
    //     }
    // });

    function detail_purchase_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-detail-purchase-report')) {
            $('.table-detail-purchase-report').DataTable().destroy();
        }
        initDataTableReport('.table-detail-purchase-report', admin_url + 'reports/detail_purchase_report', false, false, fnServerParams, [0, 'asc']);
    }

    function to_pay_debt_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-to_pay_debt-report')) {
            $('.table-to_pay_debt-report').DataTable().destroy();
        }
        initDataTableReport('.table-to_pay_debt-report', admin_url + 'reports/to_pay_debt_report', false, false, fnServerParams, [0, 'asc']);
    }

    function detail_debt_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-detail_debt-report')) {
            $('.table-detail_debt-report').DataTable().destroy();
        }
        initDataTableReport('.table-detail_debt-report', admin_url + 'reports/detail_debt_report', false, false, fnServerParams, [0, 'asc']);
    }

    function detail_purchase_order_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-purchase_order-report')) {
            $('.table-purchase_order-report').DataTable().destroy();
        }
        initDataTableReport('.table-purchase_order-report', admin_url + 'reports/purchase_order_report', false, false, fnServerParams, [0, 'asc']);
    }


    $('.table-purchase_order-report').on('draw.dt', function () {
        row_header(this);
    });


    function row_header(e) {
        var class_tr = $(e).find('.alert-header');
        $.each(class_tr, function (index, value) {
            var data = $(value).find('td').first().html();
            $(value).find('td:eq(1),td:eq(2),td:eq(3)').remove();
            $(value).find('td:eq(0)').attr('colspan', 4);
        })
    }

    // Main generate report function
    function gen_reports() {
        if (!$(report_general_purchase_report).hasClass('hide')) {

            report_general_purchase();
        }
        if (!$(general_purchase_detail_report).hasClass('hide')) {

            report_general_purchase_detail_report();
        }
        if (!$(detail_purchase_order_report).hasClass('hide')) {

            detail_purchase_order_report_v2();
        }
        if (!$(detail_purchase_report).hasClass('hide')) {

            detail_purchase_report_v2();
        }
        if (!$(to_pay_debt_report).hasClass('hide')) {

            to_pay_debt_report_v2();
        }
        if (!$(detail_debt_report).hasClass('hide')) {

            detail_debt_report_v2();
        }

        if (!$(general_synthetic_purchase_report).hasClass('hide')) {

            report_general_synthetic_purchase_report();
        }
        if (!$(general_detail_import_report).hasClass('hide')) {

            report_general_detail_import_report();
        }

    }

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
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
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
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
                    data: function (term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            var c = [{
                                code_client: '',
                                id: '',
                                text: 'Tất cả'
                            }].concat(data.results);
                            return {
                                results: c
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
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }
    }

    var base_url = '<?= base_url() ?>';

    function repoFormatSelection(state) {
        if (!state.id) return state.text;

        return state.text + ' - ' + '(' + state.code + ')';
    }

</script>