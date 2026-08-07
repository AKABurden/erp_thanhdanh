<style>
    #tb-reports tr th:nth-child(1) {
        width: 130px !important;
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
    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn){
        width: 200px !important;
    }
</style>
<div class="text-center uppercase">
    <h2><?= lang('Báo cáo sản xuất thừa') ?></h2>
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
            <?= lang('Công đoạn', 'stage_search') ?>
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
   <div class="col-md-2"><a class="btn btn-info btn-icon" onclick="create_transfer()">Tạo phiếu chuyển kho</a></div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
        <tr>
            <th class="text-center"><?= lang('id') ?></th>
            <th class="text-center"><?= lang('Đơn hàng bán') ?></th>
            <th class="text-center"><?= lang('Khách hàng') ?></th>
            <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
            <th class="text-center"><?= lang('tnh_reference_productions_orders_details') ?></th>
            <th class="text-center"><?= lang('Mã thành phẩm') ?></th>
            <th class="text-center"><?= lang('tnh_product_name') ?></th>
            <th class="text-center"><?= lang('SL đặt') ?></th>
            <th class="text-center"><?= lang('SL nhập kho theo LSX') ?></th>
            <th class="text-center"><?= lang('SL đã giao') ?></th>
            <th class="text-center"><?= lang('SL loss + làm mẫu') ?></th>
            <th class="text-center"><?= lang('Số lượng còn lại trên LSX') ?></th>
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
    };
    var oTable = '';

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function () {
        init_datepicker();
        init_selectpicker();
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_tnh/getReportOverProductions') ?>', {
            'order': [
                [2, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getReportOverProductions') ?>',
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
                                        d[key+'_text_'+index] = item.text;
                                    } else {
                                        d[key+'_text'] = $(fnserverparams[key]).select2('data').text;
                                    }
                                });
                            } else {
                                d[key+'_text'] = $(fnserverparams[key]).select2('data').text;
                            }
                        } else if ($(fnserverparams[key]).hasClass('selectpicker')) {
                            var selectedText = $(fnserverparams[key]).find('option:selected').text();
                            if (selectedText) {
                                d[key+'_text'] = selectedText;
                            }
                        }
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
                    "name": 'reference_orders',
                    'width': '130px',
                    'className': 'text-left',
                    'sortable': false
                },
                {
                    "targets": 2,
                    "name": 'company',
                    "width": "130px"
                },
                {
                    "targets": 3,
                    "name": 'reference_no_order',
                    "width": "120px"
                },
                {
                    "render": function(data, type, row) {
                        return '<a class="" title="<?= lang('tnh_detail') ?>" target="_blank" href="<?= base_url('admin/manufactures/detail_productions/') ?>' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 4,
                    "name": 'reference_no',
                    "width": "110px"
                },
                {
                    "targets": 5,
                    "name": 'item_code',
                    "width": "130px"
                },
                {
                    "targets": 6,
                    "name": 'item_name',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 7,
                    "name": 'sldat',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 8,
                    "name": 'quantity_finished',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 9,
                    "name": 'sldagiao',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 10,
                    "name": 'slloss',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 11,
                    "name": 'slconlai',
                    "width": "130px"
                }
            ],
            "btnButtons": 1
        });
    });
</script>
<script>
    function create_transfer() {
        var row_stranfer = $('.row_stranfer');
        var data = {};
        var dataString = '';
        $.each(row_stranfer, function(index, value) {
            data[$(value).data('product')] = $(value).data('pod');
            dataString += '&items[' + index + '][product]=' + $(value).data('product');
            dataString += '&items[' + index + '][pod]=' + $(value).data('pod');
            dataString += '&items[' + index + '][quanliti]=' + $(value).data('quanliti');
            dataString += '&items[' + index + '][type]=product';
        })
        window.open(admin_url + 'transfer/detail_report?report=true' + dataString, '_blank');
    }
</script>