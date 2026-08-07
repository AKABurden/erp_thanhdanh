<style>
    #tb-reports tr th:nth-child(1) {
        width: 40px !important;
    }

    #tb-reports tr th:nth-child(2) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(3) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(4) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(5) {
        width: 250px !important;
    }

    #tb-reports tr th:nth-child(6) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(7) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(8) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(9) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(10) {
        width: 70px !important;
    }
</style>
<?php
    $stages = $this->site_model->getStages();
    $nStages = count($stages) - 1;
?>
<div class="text-center uppercase">
    <h2><?= lang('report_product_error') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <?= lang('tnh_reference_productions_orders') ?>
        <input type="text" name="productions_orders_search" onchange="loadTableReport()" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('items') ?>
        <input type="text" name="products" id="products" onchange="loadTableReport()" style="width: 100%;" data-placeholder="<?= lang('cong_item_code') ?>" value="">
    </div>
    <div class="col-md-3">
        <?= lang('start_date') ?>
        <input type="text" name="start_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('end_date') ?>
        <input type="text" name="end_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
            <!-- <tr>
                <th class="text-center"><?= lang('STT') ?></th>
                <th class="text-center"><?= lang('Ngày lập lệnh') ?></th>
                <th class="text-center"><?= lang('Mã lệnh tổng') ?></th>
                <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                <th class="text-center"><?= lang('Số lượng SX') ?></th>
                <th class="text-center"><?= lang('Số lượng nhập') ?></th>
                <th class="text-center"><?= lang('Số lượng lỗi') ?></th>
            </tr> -->
            <tr>
                <th class="text-center"><?= lang('STT') ?></th>
                <th class="text-center"><?= lang('Ngày nhập kho') ?></th>
                <th class="text-center"><?= lang('Số phiếu nhập kho') ?></th>
                <th class="text-center"><?= lang('Mã lệnh sản xuất tổng') ?></th>
                <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                <th class="text-center"><?= lang('Loại hình in') ?></th>
                <th class="text-center"><?= lang('Số lượng SX') ?></th>
                <th class="text-center"><?= lang('Số lượng nhập') ?></th>
                <th class="text-center"><?= lang('Số lượng vượt') ?></th>
                <th class="text-center"><?= lang('Số lượng lỗi') ?></th>
                <th class="text-center"><?= lang('Tổng lỗi') ?></th>
                <th class="text-center"><?= lang('Tỷ lệ') ?></th>
                <th class="text-center"><?php echo (_l('cong_price_thinh')); ?></th>
                <th class="text-center"><?php echo (_l('cong_info_money')); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <th class="hide"></th>
                <th colspan="6" class="text-center"><?= lang('tnh_grand_total') ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    var fnserverparams = {
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
        "products": "#products",
        "productions_orders_search": "#productions_orders_search"
    };
    var oTable = '';

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function () {
        init_datepicker();
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectMultipleParams($('#productions_orders_search'), 'admin/manufactures/searchProductionsOrders', 0, false, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_productions/tableProduct_error') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_productions/tableProduct_error') ?>',
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
                            if (array_data) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        d[key+'_text_'+index] = item.text;
                                    } else {
                                        d[key+'_text'] = $(fnserverparams[key]).select2('data').text;
                                    }
                                });
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
                    var sums = json.sums;
                    $('tfoot th').eq(1).html('<div class="text-center">' + sums.slsx + '</div>');
                    $('tfoot th').eq(2).html('<div class="text-center">' + sums.sln + '</div>');
                    $('tfoot th').eq(3).html('<div class="text-center">' + sums.slv + '</div>');
                    $('tfoot th').eq(4).html('<div class="text-center">' + sums.sll + '</div>');
                    $('tfoot th').eq(8).html('<div class="text-right">' + sums.tt + '</div>');

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
            ],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var grand_total = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total+= intVal(aaData[i][8]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[2].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(grand_total)+'</div>';
            },
            "btnButtons": 1
        });
    });
</script>