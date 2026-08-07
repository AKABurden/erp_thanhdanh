<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var inventory_nvl_hs = $('#inventory_nvl_hs-report');
    var inventory_tp_hs = $('#inventory_tp_hs-report');
    var inventory_btp_hs = $('#inventory_btp_hs-report');
    var salesChart;
    var groupsChart;
    var paymentMethodsChart;
    var customersTable;
    var report_from = $('input[name="report-from"]');
    var report_to = $('input[name="report-to"]');
    var stock_card_report = $('#stock-card-report');
    var warehouse_import_report = $('#warehouse-import-report');
    var warehouse_import_report_mh = $('#warehouse-import-report_mh');

    var warehouse_export_report = $('#warehouse-export-report');
    var type_limit_date = $('#ch_type_limit_date');
    var warehouse_transfer_report = $('#warehouse-transfer-report');
    var warehouse_adjusted_report = $('#warehouse-adjusted-report');
    var limit_user_date = $('#warehouse-limit_user_date-report');
    var limit_user_date_btp = $('#warehouse-limit_user_date_btp-report');
    var warehouse_inventory_report = $('#warehouse-inventory-report');
    var report_all_of_stock = $('#report_all_of_stock');
    var warehouse_chart_report = $('#warehouse-chart-report');
    var warehouse_exporting_producion_report = $('#warehouse-exporting_producion-report');
    var warehouse_other_report = $('#warehouse-other-report');
    var report_from_choose = $('#report-time');
    var ch_type_adjusted = $('#ch_type_adjusted');
    var ch_status_transfer = $('#ch_status_transfer');
    var ch_type_import = $('#ch_type_import');
    var items = $('#items');
    var items_inventory = $('#items_inventory');
    var warehouse_limit_date = $('#warehouse_limit_dates');
    var ch_type_purchase_products = $('#ch_type_purchase_products');
    var warehouse = $('#warehouse');
    var category_inventory_warehouse_search = $('#category_inventory_warehouse_search');

    var stock_product = $('#stock_product');
    var warehouse_inventory = $('#warehouse_inventory');
    var warehouse_tran_export = $('#warehouse_tran_export');
    var warehouse_tran_import = $('#warehouse_tran_import');
    var warehouse_array = $('#warehouse_array');
    var report_all_of_stock_product = $('#report_all_of_stock_product');
    var type_items = $('#type_items');
    var typeitems = $('#typeitems');
    var date_range = $('#date-range');
    var fnServerParams = {
        "type_purchase_products": '[name="type_purchase_products"]',
        "filterStatus_v2": '[name="filterStatus_v2"]',
        "custom_item_select_inventory": '[name="custom_item_select_inventory"]',
        "category_search_new": '[name="category_search_new[]"]',
        "type_items": '[name="type_items"]',
        "type_items_new": '[name="type_items_new"]',
        "type_itemss": '[name="type_itemss"]',
        "type_adjusted": '[name="type_adjusted"]',
        "status_transfer": '[name="status_transfer"]',
        "type_import": '[name="type_import"]',
        "warehouse_id": '[name="warehouse_id"]',
        "warehouse_id_array": '[name="warehouse_id_array[]"]',
        "warehouse_id_inventory": '[name="warehouse_id_inventory"]',
        "warehouse_id_export": '[name="warehouse_id_export"]',
        "warehouse_id_import": '[name="warehouse_id_import"]',
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
        "material_id": '[name="material_id"]',
        "material_id_hs": '[name="material_id_hs"]',
        "semi_products_id_hs": '[name="semi_products_id_hs"]',
        "products_id_hs": '[name="products_id_hs"]',
        "category_id": '[name="category_search"]',
        "products_search": '[name="products_search"]',
        "category_search_products": '[name="category_search_products"]',
        "warehouse_limit_date": '[name="warehouse_limit_date"]',
        "type_limit_date": '[name="type_limit_date"]',
        "search_type_product": '[name="search_type_product"]',
    }
    $('#custom_item_select').on('change', function(e) {
        var currentQuantityInput = $(e.currentTarget);
        var type = currentQuantityInput.select2('data').type;
        $('#type_items').val(type);

    });
    $('#custom_item_select_inventory').on('change', function(e) {
        var currentQuantityInput = $(e.currentTarget);
        var type = currentQuantityInput.select2('data').type;
        $('#type_items_new').val(type);

    });
    var table_nvl;
    $(function() {
        $('input[name="products_id_hs"],input[name="semi_products_id_hs"],input[name="material_id_hs"],select[name="type_purchase_products"],select[name="type_limit_date"],select[name="warehouse_limit_date"],select[name="category_search_new[]"],select[name="warehouse_id_array[]"],select[name="type_itemss"],select[name="warehouse_id_inventory"],select[name="status_transfer"],select[name="type_adjusted"],select[name="type_import"],select[name="warehouse_id_export"],select[name="warehouse_id_import"],select[name="warehouse_id"],select[name="currency"],select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_items"],select[name="sale_agent_estimates"],select[name="payments_years"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"],select[name="material_id"],select[name="category_search"],input[name="products_search"],select[name="category_search_products"],input[name="filterStatus_v2"]').on('change', function() {

        });

        report_from.on('change', function() {
            var val = $(this).val();
            var report_to_val = report_to.val();
            if (val != '') {
                report_to.attr('disabled', false);
                if (report_to_val != '') {

                }
            } else {
                report_to.attr('disabled', true);
            }
        });

        report_to.on('change', function() {
            var val = $(this).val();
            if (val != '') {

            }
        });
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('inventory/SearchItems_new') ?>", 0);
        ajaxSelectCallBack_inven($('#custom_item_select_inventory'), "<?= admin_url('inventory/SearchItems_new') ?>", 0);

        $('select[name="months-report"]').on('change', function() {
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

        });
        $('.table-payments-received-report').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
            $(this).find('tfoot td.total').html(sums.total_amount);
        });
        $('.table-warehouse-adjusted-report').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng'); ?>");
            $(this).find('tfoot td.total').html(sums.quantity);
        });
        $('.table-warehouse-all-report').on('draw.dt', function() {
            var dem1 = 3;
            var dem2 = 3;

            <?php foreach ($warehouse as $key => $value) { ?>
                table_nvl.columns(dem1).visible(true, true);
                dem1++;
            <?php } ?>
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('estimate_dt_table_heading_amount'); ?>");
            var dem = 3;
            <?php foreach ($warehouse as $key => $value) { ?>
                $(this).find('tfoot td').eq(dem).html('<div class="text-center" style="font-size:12px"><b>' + sums.warehouse_<?= $value['id'] ?> + '</b></div>');
                dem++;
            <?php } ?>
            $(this).find('tfoot td').eq(dem).html('<div class="text-center" style="font-size:20px"><b>' + sums.all + '</b></div>');
            <?php foreach ($warehouse as $key => $value) { ?>
                if (sums.warehouse_<?= $value['id'] ?> == "0") {
                    table_nvl.columns(dem2).visible(false, false);
                }
                dem2++;
            <?php } ?>

        });
        $('.table-warehouse-all-report-product').on('draw.dt', function() {
            var dem1 = 5;
            var dem2 = 5;

            <?php foreach ($warehouse as $key => $value) { ?>
                table_nvl.columns(dem1).visible(true, true);
                dem1++;
            <?php } ?>
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('estimate_dt_table_heading_amount'); ?>");
            var dem = 5;
            <?php foreach ($warehouse as $key => $value) { ?>
                $(this).find('tfoot td').eq(dem).html('<div class="text-center" style="font-size:12px"><b>' + sums.warehouse_<?= $value['id'] ?> + '</b></div>');
                dem++;
            <?php } ?>
            $(this).find('tfoot td').eq(dem).html('<div class="text-center" style="font-size:20px"><b>' + sums.all + '</b></div>');
            <?php foreach ($warehouse as $key => $value) { ?>
                if (sums.warehouse_<?= $value['id'] ?> == "0") {
                    table_nvl.columns(dem2).visible(false, false);
                }
                dem2++;
            <?php } ?>

        });

        $('.table-warehouse-export-report').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
            $(this).find('tfoot td').eq(7).html('<div class="text-center">' + sums.total_quantity + '</div>');
        });
        $('.table-warehouse-exporting_producion-report').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
            $(this).find('tfoot td').eq(10).html('<div class="text-center">' + sums.all + '</div>');
        });
        $('.table-warehouse-other-report').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
            $(this).find('tfoot td').eq(8).html('<div class="text-center">' + sums.all + '</div>');
        });
        $('.table-warehouse-transfer-report').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
            $(this).find('tfoot td').eq(8).html('<div class="text-center">' + sums.quantity_import + '</div>');
            $(this).find('tfoot td').eq(11).html('<div class="text-center">' + sums.quantity_export + '</div>');

        });
    });
    $('.table-stock-card-report').on('draw.dt', function() {
        row_header();
        // setTimeout(function() {
        //     row_header();
        // }, 5000);
    });

    function row_header() {
        var class_tr = $('.alert-header');
        $.each(class_tr, function(index, value) {
            if ($(value).find('td').length > 8) {
                var data = $(value).find('td').first().html();
                $(value).find('td:eq(1),td:eq(2)').remove();
                $(value).find('td:eq(0)').attr('colspan', 3);
            }
        })
    }

    function add_common_footer_sums(table, sums) {
        table.find('tfoot').addClass('bold');
        table.find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
        table.find('tfoot td.subtotal').html(sums.subtotal);
        table.find('tfoot td.total').html(sums.total);
        table.find('tfoot td.total_tax').html(sums.total_tax);
        table.find('tfoot td.discount_total').html(sums.discount_total);
        table.find('tfoot td.adjustment').html(sums.adjustment);
    }

    $('.table-warehouse-inventory-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        if (typeof(paymentReceivedReportsTable.ajax.json()) == 'undefined') {
            return false;
        }
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(4).html('<div class="text-center">' + sums.sldk + '</div>');
        $(this).find('tfoot td').eq(5).html('<div class="text-right">' + sums.gtdk + '</div>');
        $(this).find('tfoot td').eq(6).html('<div class="text-center">' + sums.slnk + '</div>');
        $(this).find('tfoot td').eq(7).html('<div class="text-right">' + sums.gtnk + '</div>');
        $(this).find('tfoot td').eq(8).html('<div class="text-center">' + sums.slxk + '</div>');
        $(this).find('tfoot td').eq(9).html('<div class="text-right">' + sums.gtxk + '</div>');
        $(this).find('tfoot td').eq(10).html('<div class="text-center">' + sums.slck + '</div>');
        $(this).find('tfoot td').eq(11).html('<div class="text-right">' + sums.gtck + '</div>');
    });

    function init_report(e, type) {
        $('input[name="products_id_hs"]').select2('val', '');
        $('input[name="semi_products_id_hs"]').select2('val', '');
        $('input[name="material_id_hs"]').select2('val', '');
        $('#type_export_ch').val(type);
        category_inventory_warehouse_search.addClass('hide');
        var report_wrapper = $('#report');
        inventory_nvl_hs.addClass('hide');
        inventory_tp_hs.addClass('hide');
        inventory_btp_hs.addClass('hide');


        report_from_choose.addClass('hide');
        limit_user_date.addClass('hide');
        limit_user_date_btp.addClass('hide');

        type_limit_date.addClass('hide');
        warehouse_limit_date.addClass('hide');
        items.addClass('hide');
        items_inventory.addClass('hide');
        typeitems.addClass('hide');
        $('[name="type_adjusted"]').selectpicker('val', '');
        warehouse.addClass('hide');
        warehouse_tran_export.addClass('hide');
        warehouse_chart_report.addClass('hide');
        warehouse_tran_import.addClass('hide');
        warehouse_array.addClass('hide');
        ch_type_adjusted.addClass('hide');
        ch_status_transfer.addClass('hide');
        ch_type_import.addClass('hide');
        if (report_wrapper.hasClass('hide')) {
            report_wrapper.removeClass('hide');
        }

        ch_type_purchase_products.addClass('hide');
        $('head title').html($(e).text().toUpperCase());
        $('.title_ch').html($(e).text().toUpperCase());
        $('.customers-group-gen').addClass('hide');
        stock_card_report.addClass('hide');
        warehouse_import_report.addClass('hide');
        warehouse_import_report_mh.addClass('hide');
        warehouse_export_report.addClass('hide');
        report_all_of_stock.addClass('hide');
        warehouse_exporting_producion_report.addClass('hide');
        warehouse_other_report.addClass('hide');
        warehouse_transfer_report.addClass('hide');
        warehouse_adjusted_report.addClass('hide');
        warehouse_inventory_report.addClass('hide');
        report_all_of_stock_product.addClass('hide');
        warehouse_inventory.addClass('hide');
        $('#income-years').addClass('hide');
        $('.chart-income').addClass('hide');
        $('.chart-payment-modes').addClass('hide');

        $('select[name="months-report"]').selectpicker('val', 'this_month');
        // Clear custom date picker
        report_to.val('');
        report_from.val('');
        $('#currency').removeClass('hide');
        if (type == 'stock-card-report') {
            stock_card_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse.removeClass('hide');
            items.removeClass('hide');
        } else
        if (type == 'warehouse-import-report') {
            warehouse_import_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            items.removeClass('hide');
            ch_type_purchase_products.removeClass('hide');
        } else
        if (type == 'warehouse-import-report_mh') {
            warehouse_import_report_mh.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            items.removeClass('hide');
            // ch_type_import.removeClass('hide');
        } else
        if (type == 'warehouse-export-report') {
            warehouse_export_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            items.removeClass('hide');
        } else
        if (type == 'warehouse-exporting_producion-report') {
            warehouse_exporting_producion_report.removeClass('hide');
            category_inventory_warehouse_search.removeClass('hide');

            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            items.removeClass('hide');
        } else
        if (type == 'warehouse-other-report') {
            warehouse_other_report.removeClass('hide');
            category_inventory_warehouse_search.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            items.removeClass('hide');
        } else
        if (type == 'warehouse-transfer-report') {
            warehouse_transfer_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_tran_export.removeClass('hide');
            warehouse_tran_import.removeClass('hide');
            items.removeClass('hide');
            ch_status_transfer.removeClass('hide');
        } else
        if (type == 'warehouse-adjusted-report') {
            warehouse_adjusted_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            ch_type_adjusted.removeClass('hide');
            items.removeClass('hide');
        } else
        if (type == 'warehouse-inventory-report') {
            category_inventory_warehouse_search.removeClass('hide');
            warehouse_inventory_report.removeClass('hide');
            report_from_choose.removeClass('hide');
            warehouse_array.removeClass('hide');
            typeitems.removeClass('hide');
            items_inventory.removeClass('hide');
        } else
        if (type == 'report_all_of_stock') {
            report_all_of_stock.removeClass('hide');
            //  report_from_choose.removeClass('hide');
            //  warehouse.removeClass('hide');
            //  items.removeClass('hide');
        }
        if (type == 'stock_product') {
            stock_product.removeClass('hide');
            //  report_from_choose.removeClass('hide');
            //  warehouse.removeClass('hide');
            //  items.removeClass('hide');
        }
        if (type == 'report_all_of_stock_product' || type == 'report_all_of_stock_semi_product') {
            if (type == 'report_all_of_stock_product') {
                $('#search_type_product').val('products');
            } else {
                $('#search_type_product').val('semi_products');
            }
            report_all_of_stock_product.removeClass('hide');
            //  report_from_choose.removeClass('hide');
            //  warehouse.removeClass('hide');
            //  items.removeClass('hide');
        }
        if (type == 'limit_user_date') {
            limit_user_date.removeClass('hide');
            //  report_from_choose.removeClass('hide');
            // warehouse.removeClass('hide');
            items.removeClass('hide');
            type_limit_date.removeClass('hide');
            warehouse_limit_date.removeClass('hide');
        }
        if (type == 'limit_user_date_btp') {
            limit_user_date_btp.removeClass('hide');
            //  report_from_choose.removeClass('hide');
            // warehouse.removeClass('hide');
            items.removeClass('hide');
            type_limit_date.removeClass('hide');
            warehouse_limit_date.removeClass('hide');
        }
        if (type == 'inventory_nvl_hs') {
            inventory_nvl_hs.removeClass('hide');
        }
        if (type == 'inventory_tp_hs') {
            inventory_tp_hs.removeClass('hide');
        }
        if (type == 'inventory_btp_hs') {
            inventory_btp_hs.removeClass('hide');
        }

    }

    function change_event() {
        gen_reports();
    }
    $(document).on('changed.bs.select', '#warehouse_id_array', function(e, clickedIndex, isSelected, previousValue) {
        var selected = $(this).val() || [];
        if (selected.length > 3) {
            // Bỏ chọn option vừa được chọn
            var option = $(this).find('option').eq(clickedIndex).val();
            $(this).find('option[value="' + option + '"]').prop('selected', false);
            $(this).selectpicker('refresh');

            alert('Chỉ được chọn tối đa 3 kho!');
        }
    });
    // Generate customers report
    function stock_card_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-stock-card-report')) {
            $('.table-stock-card-report').DataTable().destroy();
        }
        initDataTable('.table-stock-card-report', admin_url + 'reports/stock_card_report', false, false, fnServerParams, [0, 'asc']);
    }
    // Generate customers report
    function warehouse_import_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-import-report')) {
            $('.table-warehouse-import-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-import-report', admin_url + 'reports/warehouse_import_report', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_import_report_mh_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-import-report_mh')) {
            $('.table-warehouse-import-report_mh').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-import-report_mh', admin_url + 'reports/warehouse_import_report_mh', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_export_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-export-report')) {
            $('.table-warehouse-export-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-export-report', admin_url + 'reports/warehouse_export_report', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_exporting_producion_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-exporting_producion-report')) {
            $('.table-warehouse-exporting_producion-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-exporting_producion-report', admin_url + 'reports/warehouse_exporting_producion_report', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_other_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-other-report')) {
            $('.table-warehouse-other-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-other-report', admin_url + 'reports/warehouse_other_report', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_transfer_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-transfer-report')) {
            $('.table-warehouse-transfer-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-transfer-report', admin_url + 'reports/warehouse_transfer_report', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_adjusted_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-adjusted-report')) {
            $('.table-warehouse-adjusted-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-adjusted-report', admin_url + 'reports/warehouse_adjusted_report', false, false, fnServerParams,
            [0, 'desc']);
    }

    function warehouse_inventory_report_v2() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-inventory-report')) {
            $('.table-warehouse-inventory-report').DataTable().destroy();
        }
        initDataTableReport('.table-warehouse-inventory-report', admin_url + 'reports/warehouse_inventory_report', [0, 1, 2, 3, 4, 5, 7, 8, 9, 10, 11], [0, 1, 2, 3, 4, 5, 7, 8, 9, 10, 11], fnServerParams, []);
    }

    function warehouse_all_report() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-all-report')) {
            $('.table-warehouse-all-report').DataTable().destroy();
        }
        table_nvl = initDataTableReport('.table-warehouse-all-report', admin_url + 'reports/warehouse_all_report', false, false, fnServerParams, [0, 'asc']);
    }

    function warehouse_all_report_product() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-all-report-product')) {
            $('.table-warehouse-all-report-product').DataTable().destroy();
        }
        table_nvl = initDataTableReport('.table-warehouse-all-report-product', admin_url + 'reports/warehouse_all_report_product', false, false, fnServerParams, [0, 'asc']);
    }

    function limit_user_dates() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-limit_user_date-report')) {
            $('.table-warehouse-limit_user_date-report').DataTable().destroy();
        }
        table_product = initDataTableReport('.table-warehouse-limit_user_date-report', admin_url + 'reports/limit_user_date_report', false, false, fnServerParams, [0, 'asc']);
    }

    function limit_user_date_btps() {
        if ($.fn.DataTable.isDataTable('.table-warehouse-limit_user_date_btp-report')) {
            $('.table-warehouse-limit_user_date_btp-report').DataTable().destroy();
        }
        table_product = initDataTableReport('.table-warehouse-limit_user_date_btp-report', admin_url + 'reports/limit_user_date_btp_report', false, false, fnServerParams, [0, 'asc']);
    }

    function inventory_nvl_hs_table() {
        if ($.fn.DataTable.isDataTable('.table-inventory_nvl_hs-report')) {
            $('.table-inventory_nvl_hs-report').DataTable().destroy();
        }
        table_product = initDataTableReport('.table-inventory_nvl_hs-report', admin_url + 'reports/inventory_nvl_hs_report', false, false, fnServerParams, [0, 'asc']);
    }

    function inventory_tp_hs_table() {
        if ($.fn.DataTable.isDataTable('.table-inventory_tp_hs-report')) {
            $('.table-inventory_tp_hs-report').DataTable().destroy();
        }
        table_product = initDataTableReport('.table-inventory_tp_hs-report', admin_url + 'reports/inventory_tp_hs_report', false, false, fnServerParams, [0, 'asc']);
    }

    function inventory_btp_hs_table() {
        if ($.fn.DataTable.isDataTable('.table-inventory_btp_hs-report')) {
            $('.table-inventory_btp_hs-report').DataTable().destroy();
        }
        table_product = initDataTableReport('.table-inventory_btp_hs-report', admin_url + 'reports/inventory_btp_hs_report', false, false, fnServerParams, [0, 'asc']);
    }

    // Main generate report function
    function gen_reports() {
        if (!$(stock_card_report).hasClass('hide')) {
            stock_card_report_v2();
        }
        if (!$(warehouse_import_report).hasClass('hide')) {
            warehouse_import_report_v2();
        }
        if (!$(warehouse_import_report_mh).hasClass('hide')) {
            warehouse_import_report_mh_v2();
        }
        if (!$(warehouse_export_report).hasClass('hide')) {
            warehouse_export_report_v2();
        }
        if (!$(warehouse_exporting_producion_report).hasClass('hide')) {
            warehouse_exporting_producion_report_v2();
        }
        if (!$(warehouse_other_report).hasClass('hide')) {
            warehouse_other_report_v2();
        }
        if (!$(warehouse_transfer_report).hasClass('hide')) {
            warehouse_transfer_report_v2();
        }
        if (!$(warehouse_adjusted_report).hasClass('hide')) {
            warehouse_adjusted_report_v2();
        }
        if (!$(warehouse_inventory_report).hasClass('hide')) {
            warehouse_inventory_report_v2();
        }
        if (!$(report_all_of_stock).hasClass('hide')) {
            warehouse_all_report();
        }
        if (!$(report_all_of_stock_product).hasClass('hide')) {
            warehouse_all_report_product();
        }
        if (!$(limit_user_date).hasClass('hide')) {
            limit_user_dates();
        }
        if (!$(limit_user_date_btp).hasClass('hide')) {
            limit_user_date_btps();
        }
        if (!$(inventory_nvl_hs).hasClass('hide')) {
            inventory_nvl_hs_table();
        }
        if (!$(inventory_tp_hs).hasClass('hide')) {
            inventory_tp_hs_table();
        }
        if (!$(inventory_btp_hs).hasClass('hide')) {
            inventory_btp_hs_table();
        }
    }

    function ajaxSelectCallBack_inven(element, url, id, types = '') {
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
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
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
                            type: $('#type_itemss').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
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
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }

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
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
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
                escapeMarkup: function(m) {
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

    function viewinventorywarehouse(id = '', id_items = '', type = '') {
        $('#viewinventorywarehouse_data').html('');
        $.get(admin_url + 'reports/viewinventorywarehouse_data/' + id + '/' + id_items + '/' + type).done(function(response) {
            $('#viewinventorywarehouse_data').html(response);
            $('#viewinventorywarehouse').modal({
                show: true,
                backdrop: 'static'
            });
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('.table-warehouse-limit_user_date-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(8).html('<div class="text-center">' + sums.product_quantity + '</div>');
        $(this).find('tfoot td').eq(11).html('<div class="text-right">' + sums.product_total + '</div>');
    });
    $('.table-warehouse-limit_user_date_btp-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(8).html('<div class="text-center">' + sums.product_quantity + '</div>');
        $(this).find('tfoot td').eq(11).html('<div class="text-right">' + sums.product_total + '</div>');
    });
    $('.table-warehouse-import-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(9).html('<div class="text-center">' + sums.product_quantity + '</div>');
        $(this).find('tfoot td').eq(12).html('<div class="text-right">' + sums.product_total + '</div>');
    });
    $('.table-warehouse-import-report_mh').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(12).html('<div class="text-center">' + sums.product_quantity + '</div>');
        $(this).find('tfoot td').eq(14).html('<div class="text-right">' + sums.product_total + '</div>');
    });

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

    });

    function view_adjusted(id) {
        $('#view_adjusted_data').html('');
        $.get(admin_url + 'adjusted/adjusted_data/' + id).done(function(response) {
            $('#view_adjusted_data').html(response);
            $('#view_adjusted').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew_ch('tbladjusted', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_adjusted', function() {
        $('#view_adjusted_data').html('');
    });

    function view_transfer(id) {
        $('#view_transfer_data').html('');
        $.get(admin_url + 'transfer/transfer_data/' + id).done(function(response) {
            $('#view_transfer_data').html(response);
            $('#view_transfer').modal({
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
    $('body').on('hidden.bs.modal', '#view_transfer', function() {
        $('#view_transfer_data').html('');
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
    init_weekly_payment_statistics(<?php echo $weekly_payment_stats; ?>);
    $('select[name="year"]').on('change', function() {
        init_weekly_payment_statistics();
    });
    $('select[name="month"]').on('change', function() {
        init_weekly_payment_statistics();
    });
    $('select[name="day"]').on('change', function() {
        init_weekly_payment_statistics();
    });

    function init_weekly_payment_statistics(data) {
        if ($('#weekly-payment-statistics').length > 0) {
            if (typeof(weekly_payments_statistics) !== 'undefined') {
                weekly_payments_statistics.destroy();
            }
            if (typeof(data) == 'undefined') {
                var year = $('select[name="year"]').val();
                var month = $('select[name="month"]').val();
                var day = $('select[name="day"]').val();
                $.post(admin_url + 'dashboard/weekly_payments_statistics/', {
                    [csrfData['token_name']]: csrfData['hash'],
                    year: year,
                    month: month,
                    day: day
                }, function(response) {
                    weekly_payments_statistics = new Chart($('#weekly-payment-statistics'), {
                        type: 'bar',
                        data: response,
                        options: {
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                    }
                                }]
                            },
                        },
                    });
                }, 'json');
            } else {
                weekly_payments_statistics = new Chart($('#weekly-payment-statistics'), {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
            }

        }
    }
    $(function() {
        //Năm tự động điền vào select
        var seYear = $('#year');
        var date = new Date();
        var cur = date.getFullYear();
        seYear.append('<option value="">-- <?= _l('year') ?> --</option>');
        for (i = cur; i >= 2015; i--) {
            if (i == cur) {
                seYear.append('<option selected value="' + i + '">' + i + '</option>');
            } else {
                seYear.append('<option value="' + i + '">' + i + '</option>');
            }
        };

        //Tháng tự động điền vào select
        var seMonth = $('#month');
        var date = new Date();

        var month = new Array();
        month[1] = "<?= _l('January') ?>";
        month[2] = "<?= _l('February') ?>";
        month[3] = "<?= _l('March') ?>";
        month[4] = "<?= _l('April') ?>";
        month[5] = "<?= _l('May') ?>";
        month[6] = "<?= _l('June') ?>";
        month[7] = "<?= _l('July') ?>";
        month[8] = "<?= _l('August') ?>";
        month[9] = "<?= _l('September') ?>";
        month[10] = "<?= _l('October') ?>";
        month[11] = "<?= _l('November') ?>";
        month[12] = "<?= _l('December') ?>";

        seMonth.append('<option value="">-- <?= _l('month') ?> --</option>');
        for (i = 12; i > 0; i--) {
            seMonth.append('<option value="' + i + '">' + month[i] + '</option>');
        };

        //Ngày tự động điền vào select
        function dayList(month, year) {
            var day = new Date(year, month, 0);
            return day.getDate();
        }

        $('#year, #month').change(function() {
            //Đoạn code lấy id không viết bằng jQuery để phù hợp với đoạn code bên dưới
            var y = document.getElementById('year');
            var m = document.getElementById('month');
            var d = document.getElementById('day');

            var year = y.options[y.selectedIndex].value;
            var month = m.options[m.selectedIndex].value;
            var day = d.options[d.selectedIndex].value;
            if (day == ' ') {
                var days = (year == ' ' || month == ' ') ? 31 : dayList(month, year);
                d.options.length = 0;
                d.options[d.options.length] = new Option('-- <?= _l('day') ?> --', ' ');
                for (var i = 1; i <= days; i++)
                    d.options[d.options.length] = new Option(i, i);
            }
        });
    });
    $('#category_search').select2({
        'allowClear': true
    });
    $('#category_search_products').select2({
        'allowClear': true
    });
    ajaxSelectParamsCallback('#products_search', 'admin/products/searchProductsSelect2', $('#products_search').val(), false, true);

    $('.H_filter_v2').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus_v2"]').val(value);
        $('input[name="filterStatus_v2"]').change();
    });
    ajaxSelectCallBack($('#material_id_hs'), "<?= admin_url('inventory/SearchItems_hs') ?>", 0, 'nvl');
    ajaxSelectCallBack($('#products_id_hs'), "<?= admin_url('inventory/SearchItems_hs') ?>", 0, 'product');
    ajaxSelectCallBack($('#semi_products_id_hs'), "<?= admin_url('inventory/SearchItems_hs') ?>", 0, 'semi_products');

    $('.table-inventory_nvl_hs-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(1).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(4).html('<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' + sums.slt + '</div>');
        $(this).find('tfoot td').eq(6).html('<div class="text-right">' + sums.gtt + '</div>');
    });
    $('.table-inventory_tp_hs-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(1).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(4).html('<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' + sums.slt + '</div>');
        $(this).find('tfoot td').eq(6).html('<div class="text-right">' + sums.gtt + '</div>');
    });
    $('.table-inventory_btp_hs-report').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(1).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(4).html('<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' + sums.slt + '</div>');
        $(this).find('tfoot td').eq(6).html('<div class="text-right">' + sums.gtt + '</div>');
    });
</script>