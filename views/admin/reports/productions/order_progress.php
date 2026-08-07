<style>
    #tb-reports tr th:nth-child(1) {
        width: 120px !important;
    }

    #tb-reports tr th:nth-child(2) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(3) {
        width: 130px !important;
    }

    #tb-reports tr th:nth-child(4) {
        width: 200px !important;
    }

    #tb-reports tr th:nth-child(5) {
        width: 80px !important;
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
    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn){
        width: 200px !important;
    }
</style>
<div class="text-center uppercase">
    <h2><?= lang('Báo cáo trình trạng đơn hàng') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('Đơn hàng bán', 'orders_and_business_plan') ?>
            <input type="text" onchange="loadTableReport()" name="orders_and_business_plan" id="orders_and_business_plan" style="width: 100%;" data-placeholder="<?= lang('Đơn hàng bán') ?>" value="">
        </div>
    </div>
    <div class="col-md-2">
        <?= lang('tnh_items', 'items_search') ?>
        <input type="text" onchange="loadTableReport()" name="items_search" data-placeholder="<?= lang('tnh_items') ?>" id="items_search" class="items_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-2">
        <?= lang('customers', 'customers') ?>
        <input type="text" name="customer_search" onchange="loadTableReport()" data-placeholder="<?= lang('customers') ?>"
               id="customer_search" class="customer_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?php $CategoryStages = recursiveCategoryStagesNew(); ?>
            <?= lang('Công đoạn', 'stage_search') ?><br>
            <select name="stage_search[]" id="stage_search" data-actions-box="true" data-live-search="true" onchange="loadTableReport()" class="selectpicker" multiple style="width: 200px;">
                <option value=""></option>
                <?php if (!empty($CategoryStages)) : ?>
                    <?php foreach ($CategoryStages as $key => $value) : ?>
                        <?php if ($value['main'] == 1){
                            continue;
                        } ?>
                        <option class="<?= ($value['main'] == '1' ? 'option_main' : '') ?>" value="<?= str_replace('detail__','',$value['id']) ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <?= lang('start_date', 'start_date_search') ?>
        <input type="text" name="start_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="col-md-2">
        <?= lang('end_date', 'end_date_search') ?>
        <input type="text" name="end_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="clearfix"></div>
    <div class="col-md-2">
        <?php $arrStatus = [
            [
                'id' => 1,
                'name' => 'Chờ sản xuất'
            ],
            [
                'id' => 2,
                'name' => 'Đang sản xuất'
            ],
            [
                'id' => 3,
                'name' => 'Chờ giao hàng'
            ],
            [
                'id' => 4,
                'name' => 'Hoàn thành'
            ],
        ] ?>
        <?= lang('Trạng thái', 'status_search') ?>
        <select name="status_search" id="status_search" data-live-search="true" onchange="loadTableReport()" class="selectpicker" style="width: 200px;">
            <option value=""></option>
            <?php if (!empty($arrStatus)) : ?>
                <?php foreach ($arrStatus as $key => $value) : ?>
                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="search_date_delivery"
                   class="control-label"><?= _l('Ngày dự kiến giao hàng') ?></label>
            <div class="input-group">
                <input type="text" id="search_date_delivery"
                       onchange="loadTableReport()"
                       name="search_date_delivery"
                       class="form-control search_date_delivery" aria-invalid="false">
                <div class="input-group-addon">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="search_date_delivery_new"
                   class="control-label"><?= _l('Ngày giao hàng thực tế') ?></label>
            <div class="input-group">
                <input type="text" id="search_date_delivery_new"
                       onchange="loadTableReport()"
                       name="search_date_delivery_new"
                       class="form-control search_date_delivery_new" aria-invalid="false">
                <div class="input-group-addon">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('id') ?></th>
                <th class="text-center"><?= lang('Ngày mở đơn') ?></th>
                <th class="text-center"><?= lang('Đơn hàng bán') ?></th>
                <th class="text-center"><?= lang('Khách hàng') ?></th>
                <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                <th class="text-center"><?= lang('Số LSX tổng trên chuyền') ?></th>
                <th class="text-center"><?= lang('Loại đơn hàng') ?></th>
                <th class="text-center"><?= lang('Mã thành phẩm') ?></th>
                <th class="text-center"><?= lang('tnh_product_name') ?></th>
                <th class="text-center"><?= lang('SL đặt') ?></th>
                <th class="text-center"><?= lang('SL giữ kho') ?></th>
                <th class="text-center"><?= lang('SL sản xuất') ?></th>
                <th class="text-center"><?= lang('SL nhập kho') ?></th>
                <th class="text-center"><?= lang('SL giữ chuyền') ?></th>
                <th class="text-center"><?= lang('SL đã giao') ?></th>
                <th class="text-center"><?= lang('SL loss + mẫu') ?></th>
                <th class="text-center"><?= lang('SL còn lại') ?></th>
                <th class="text-center"><?= lang('Trạng thái đơn hàng') ?></th>
                <th class="text-center"><?= lang('Trạng thái LSX tổng') ?></th>
                <th class="text-center"><?= lang('Trạng thái trên truyền') ?></th>
                <th class="text-center"><?= lang('Ngày dự kiến giao') ?></th>
                <th class="text-center"><?= lang('Chi tiết ngày giao hàng') ?></th>
                <th class="text-center"><?= lang('Ngày giao thực tế') ?></th>
                <th class="text-center"><?= lang('SL giao thực tế') ?></th>
                <th class="text-center"><?= lang('Công đoạn') ?></th>
                <th class="text-center"><?= lang('tnh_note_orders') ?></th>
                <th class="text-center"><?= lang('Ghi chú') ?></th>
                <th class="text-center"><?= lang('Trạng thái hủy đơn hàng') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var fnserverparams = {
        'items_search': '#items_search',
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
        'orders_and_business_plan': '#orders_and_business_plan',
        'customer_search': '#customer_search',
        'stage_search': '#stage_search',
        'status_search': '#status_search',
        'search_date_delivery': '#search_date_delivery',
        'search_date_delivery_new': '#search_date_delivery_new',
    };
    var oTable = '';

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function () {
        search_daterangepicker_date_delivery();
        search_daterangepicker_date_delivery_new();
        init_datepicker();
        init_selectpicker();
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_tnh/getProcessOrders') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getProcessOrders') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    // $('.tb-reports tfoot td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantity)}</div>`);
                    $('.tb-reports tfoot td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityFinished)}</div>`);
                    $('.tb-reports tfoot td:nth-child(7)').html(`<div class="text-right">${tnhFormatNumber(json.totalCost)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [
                {
                    "targets": 0,
                    "name": 'id',
                    'width': '45px',
                    'className': 'text-center',
                    'visible': false
                },
                {
                    "targets": 1,
                    "name": 'date',
                    'width': '100px',
                    'className': 'text-left',
                },
                {
                    "targets": 2,
                    "name": 'reference_orders',
                    'width': '150px',
                    'className': 'text-left',
                    'sortable': false
                },
                {
                    "targets": 3,
                    "name": 'company',
                    "width": "130px"
                },
                {
                    "targets": 4,
                    "name": 'reference_no_order',
                    "width": "130px"
                },
                {
                    "targets": 5,
                    "name": 'reference_no_order_tc',
                    "width": "130px"
                },
                {
                    "targets": 6,
                    "name": 'type_order',
                    "width": "80px"
                },
                {
                    "targets": 7,
                    "name": 'item_code',
                    "width": "130px"
                },
                {
                    "targets": 8,
                    "name": 'item_name',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 9,
                    "name": 'sldat',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 10,
                    "name": 'sqlgiu',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 11,
                    "name": 'sqlsanxuat',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 12,
                    "name": 'quantity_finished',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 13,
                    "name": 'sltrenchuyen',
                    "width": "80px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 14,
                    "name": 'sldagiao',
                    "width": "80px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 15,
                    "name": 'slloss',
                    "width": "80px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 16,
                    "name": 'slloss',
                    "width": "80px"
                },
                {
                    'className': 'text-center',
                    "targets": 17,
                    "name": 'ngdk',
                    "width": "170px"
                },
                {
                    'className': 'text-center',
                    "targets": 18,
                    "name": 'detail_date_delivery',
                    "width": "120px"
                },
                {
                    'className': 'text-center',
                    "targets": 19,
                    "name": 'date_delivery',
                    "width": "150px"
                },
                {
                    'className': 'text-center',
                    "targets": 20,
                    "name": 'quantity_delivery',
                    "width": "150px"
                },
                {
                    'className': 'text-left',
                    "targets": 21,
                    "name": 'status',
                    "width": "120px"
                },
                {
                    'className': 'text-left',
                    "targets": 22,
                    "name": 'note',
                    "width": "120px"
                },
                {
                    'className': 'text-left',
                    "targets": 23,
                    "name": 'note',
                    "width": "120px"
                },
                {
                    "targets": 24,
                    "name": 'status_orders',
                    "width": "100px"
                }
            ]
        });
    });
    var search_daterangepicker_date_delivery = () => {
        $('input[name="search_date_delivery"]').daterangepicker({
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
        $('input[name="search_date_delivery"]').val('').datepicker("refresh");
        $('input[name="search_date_delivery"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date_delivery").trigger("change");
        });
        $('input[name="search_date_delivery"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date_delivery").trigger("change");
        });
    };

    var search_daterangepicker_date_delivery_new = () => {
        $('input[name="search_date_delivery_new"]').daterangepicker({
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
        $('input[name="search_date_delivery_new"]').val('').datepicker("refresh");
        $('input[name="search_date_delivery_new"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date_delivery_new").trigger("change");
        });
        $('input[name="search_date_delivery_new"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date_delivery_new").trigger("change");
        });
    };
</script>