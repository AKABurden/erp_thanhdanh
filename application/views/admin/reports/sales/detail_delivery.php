<style>
    #tb-reports tr th:nth-child(1) {
        width: 150px !important;
    }

    #tb-reports tr th:nth-child(2) {
        width: 110px !important;
    }

    #tb-reports tr th:nth-child(3) {
        width: 130px !important;
    }

    #tb-reports tr th:nth-child(4) {
        width: 130px !important;
    }

    #tb-reports tr th:nth-child(5) {
        width: 130px !important;
    }

    #tb-reports tr th:nth-child(6) {
        width: 170px !important;
    }

    #tb-reports tr th:nth-child(7) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(8) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(9) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(10) {
        width: 100px !important;
    }
</style>
<div class="text-center uppercase">
    <h2><?= lang('Báo cáo chi tiết giao hàng') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-2">
        <?= lang('customers', 'customers') ?>
        <input type="text" name="customer_search" onchange="loadTableReport()" data-placeholder="<?= lang('customers') ?>"
            id="customer_search" class="customer_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-2">
        <?= lang('tnh_reference_orders', 'orders_search') ?>
        <input type="text" name="orders_search" id="orders_search" onchange="loadTableReport()" style="width: 100%;"
            data-placeholder="<?= lang('tnh_reference_orders') ?>"
            value="">
    </div>
    <div class="col-md-2">
        <?= lang('Số giao hàng', 'delivery_search') ?>
        <input type="text" name="delivery_search" id="delivery_search" onchange="loadTableReport()" style="width: 100%;"
            data-placeholder="<?= lang('Số giao hàng') ?>"
            value="">
    </div>
    <div class="col-md-2">
        <?= lang('tnh_items', 'items_search') ?>
        <input type="text" onchange="loadTableReport()" name="items_search" data-placeholder="<?= lang('tnh_items') ?>" id="items_search" class="items_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-2">
        <?= lang('start_date', 'start_date_search') ?>
        <input type="text" name="start_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="<?= _d(date('Y-m-01')) ?>">
    </div>
    <div class="col-md-2">
        <?= lang('end_date', 'end_date_search') ?>
        <input type="text" name="end_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="<?= _d(date('Y-m-t')) ?>">
    </div>
    <div class="clearfix"></div>
    <div class="col-md-2">
        <button type="button" onclick="excelDetailDelivery()" style="margin-top: 25px;" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> <?= lang('xuất excel') ?></button>
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('Số phiếu') ?></th>
                <th class="text-center"><?= lang('Ngày lập') ?></th>
                <th class="text-center"><?= lang('Ngày giao thực tế') ?></th>
                <th class="text-center"><?= lang('Mã KH') ?></th>
                <th class="text-center"><?= lang('Brand') ?></th>
                <th class="text-center"><?= lang('Khách hàng') ?></th>
                <th class="text-center"><?= lang('Mã ĐĐH') ?></th>
                <th class="text-center"><?= lang('Mã đơn đặt') ?></th>
                <th class="text-center"><?= lang('Chỉ lệnh') ?></th>
                <th class="text-center"><?= lang('Mã TP') ?></th>
                <th class="text-center"><?= lang('Tên TP') ?></th>
                <th class="text-center"><?= lang('Quy cách') ?></th>
                <th class="text-center"><?= lang('ĐVT') ?></th>
                <th class="text-center"><?= lang('Tổng SL') ?></th>
                <th class="text-center"><?= lang('Đơn giá') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var fnserverparams = {
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
        'customer_search': '#customer_search',
        'items_search': '#items_search',
        'orders_search': '#orders_search',
        'delivery_search': '#delivery_search',
    };
    var oTable = '';

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function() {
        init_datepicker();
        init_selectpicker();
        ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParams('#delivery_search', 'admin/reports_tnh/searchDelivery', 0, true, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_tnh/getDetailDeliveries') ?>', {
            'order': [
                [3, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'searching': false,
            'ordering': false,
            'responsive': true,
            "info": false,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getDetailDeliveries') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();

                        //custom
                        if ($(fnserverparams[key]).data('select2') && $(fnserverparams[key]).val()) {

                            var array_data = $(fnserverparams[key]).select2('data');
                            if (array_data.length) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        d[key + '_text_' + index] = item.text;
                                    } else {
                                        d[key + '_text'] = $(fnserverparams[key]).select2('data').text;
                                    }
                                });
                            } else {
                                d[key + '_text'] = $(fnserverparams[key]).select2('data').text;
                            }
                        } else if ($(fnserverparams[key]).hasClass('selectpicker')) {
                            var selectedText = $(fnserverparams[key]).find('option:selected').text();
                            if (selectedText) {
                                d[key + '_text'] = selectedText;
                            }
                        }
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    // $('.tb-reports tfoot td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityFinished)}</div>`);
                    // $('.tb-reports tfoot td:nth-child(7)').html(`<div class="text-right">${tnhFormatNumber(json.totalCost)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [{
                "targets": 0,
                "name": 'id',
                'width': '45px',
                'className': 'text-center',
                // 'visible': false
            }, ],
            "btnButtons": 1
        });
    });

    function excelDetailDelivery() {
        customer_search = $('#customer_search').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        items_search = $('#items_search').val();
        orders_search = $('#orders_search').val();
        delivery_search = $('#delivery_search').val();

        var url_print = site.base_url + 'admin/reports_tnh/excel_detail_delivery?customer_search=' + customer_search + '&start_date_search=' + start_date_search +
            '&end_date_search=' + end_date_search + '&items_search=' + items_search + '&orders_search=' + orders_search + '&delivery_search=' + delivery_search;
        window.open(url_print, "_blank");
    }
</script>