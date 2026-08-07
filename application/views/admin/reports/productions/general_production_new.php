<div class="text-center uppercase">
    <h2><?= lang('report_general_production') ?></h2>
</div>
<hr>
<style>
    #tb-general-production tr th:nth-child(1) {
        width: 30px !important;
        min-width: 30px !important;
        max-width: 30px !important;
    }

    #tb-general-production tr th:nth-child(2) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(3) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(4) {
        width: 90px !important;
        min-width: 90px !important;
        max-width: 90px !important;
    }

    #tb-general-production tr th:nth-child(5) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(6) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #tb-general-production tr th:nth-child(7) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(8) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(9) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(10) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(11) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(12) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #tb-general-production tr th:nth-child(13) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }
</style>
<div class="row mbot10">
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
            <input type="text" name="productions_orders_search" onchange="changeTable()" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('tnh_items', 'items_search') ?>
            <input type="text" name="items_search" onchange="changeTable()" data-placeholder="<?= lang('tnh_items') ?>" id="items_search" class="items_search" style="width: 100%;" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('tnh_orders_and_business_plan', 'orders_and_business_plan') ?>
            <input type="text" data-placeholder="<?= lang('ĐHB/Kế hoạch BTP') ?>" onchange="changeTable()" name="orders_and_business_plan" id="orders_and_business_plan" style="width: 100%;" data-placeholder="<?= lang('') ?>" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('customers', 'customers') ?>
            <input type="text" name="customer_search" onchange="changeTable()" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('start_date', 'start_date_search') ?>
            <input type="text" name="start_date_search" onchange="changeTable()" id="start_date_search" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('end_date', 'end_date_search') ?>
            <input type="text" name="end_date_search" onchange="changeTable()" id="end_date_search" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="">
        </div>
    </div>
    <?php
        $CategoryStages = recursiveCategoryStages();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('tnh_type_print', 'type_print_search') ?>
            <select name="type_print_search" id="type_print_search" onchange="changeTable()" data-placeholder="<?= lang('tnh_type_print') ?>" class="modal-select2" style="width: 100%;">
                <option value=""></option>
                <?php if (!empty($CategoryStages)) : ?>
                    <?php foreach ($CategoryStages as $key => $value) : ?>
                        <option class="<?= ($value['main'] == '1' ? 'option_main' : '') ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="tb-general-production" class="table table-hover table-bordered table-condensed dataTable" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('tnh_numbers') ?></th>
                <th class="text-center"><?= lang('Ngày lập lệnh SX') ?></th>
                <th class="text-center"><?= lang('Ngày DK giao hàng') ?></th>
                <th class="text-center"><?= lang('Số lệnh sản xuất tổng') ?></th>
                <th class="text-center"><?= lang('Mã SP') ?></th>
                <th class="text-center"><?= lang('Tên SP') ?></th>
                <th class="text-center"><?= lang('Số lượng đặt') ?></th>
                <th class="text-center"><?= lang('Số lượng sx') ?></th>
                <th class="text-center"><?= lang('Số lượng tờ in') ?></th>
                <th class="text-center"><?= lang('Số lượng hoàn thành') ?></th>
                <th class="text-center"><?= lang('Số lượng lỗi') ?></th>
                <th class="text-center"><?= lang('Trạng thái') ?></th>
                <th class="text-center"><?= lang('Loại hình in') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <td></td>
                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    var paramsGeneralProduction = {
        status_table: '#status_table',
        productions_orders_search: '#productions_orders_search',
        items_search: '#items_search',
        type_print_search: '#type_print_search',
        orders_and_business_plan: '#orders_and_business_plan',
        customer_search: '#customer_search',
        status_search: '#status_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };

    function changeTable() {
        oTableGeneralProduction.draw();
    }

    $(document).ready(function() {
        $('#type_print_search').select2({
            'allowClear': true
        });
        init_datepicker();
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);

        init_datepicker();

        oTableGeneralProduction = tnhInitDataTable('#tb-general-production', '', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            // 'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getGeneralProductionsNew') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in paramsGeneralProduction) {
                        d[key] = $(paramsGeneralProduction[key]).val();

                        if ($(paramsGeneralProduction[key]).data('select2') && $(paramsGeneralProduction[key]).val()) {
                            d[key+'_text'] = $(paramsGeneralProduction[key]).select2('data').text;
                        }
                    }
                },
                "dataSrc": function(json) {
                    $('#tb-general-production tfoot td:nth-child(7)').html(`<div class="text-center">${tnhFormatNumber(json._quantity_order)}</div>`);
                    $('#tb-general-production tfoot td:nth-child(8)').html(`<div class="text-center">${tnhFormatNumber(json._quantity_manufactures)}</div>`);
                    $('#tb-general-production tfoot td:nth-child(9)').html(`<div class="text-center">${tnhFormatNumber(json._quantity_pager)}</div>`);
                    $('#tb-general-production tfoot td:nth-child(10)').html(`<div class="text-center">${tnhFormatNumber(json._quantity_warehoused)}</div>`);
                    $('#tb-general-production tfoot td:nth-child(11)').html(`<div class="text-center">${tnhFormatNumber(json._quantity_errors)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [
            ],
            "btnButtons": 1
        });
    });
</script>