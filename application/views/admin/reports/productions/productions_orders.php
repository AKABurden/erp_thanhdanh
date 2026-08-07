<style>
    #tb-reports tr th:nth-child(1) {
        width: 30px !important;
        max-width: 30px !important;
    }

    #tb-reports tr th:nth-child(2) {
        width: 110px !important;
        max-width: 110px !important;
    }

    #tb-reports tr th:nth-child(3) {
        width: 130px !important;
        max-width: 130px !important;
    }

    #tb-reports tr th:nth-child(4) {
        width: 130px !important;
        max-width: 200px !important;
    }

    #tb-reports tr th:nth-child(5) {
        width: 130px !important;
        max-width: 80px !important;
    }

    #tb-reports tr th:nth-child(6) {
        width: 130px !important;
        max-width: 170px !important;
    }

    #tb-reports tr th:nth-child(7) {
        width: 130px !important;
        max-width: 100px !important;
    }

    #tb-reports tr th:nth-child(8) {
        width: 100px !important;
        max-width: 100px !important;
    }
</style>
<div class="text-center uppercase">
    <h2><?= lang('report_productions_orders') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <?= lang('start_date', 'start_date_search') ?>
        <input type="text" name="start_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('end_date', 'end_date_search') ?>
        <input type="text" name="end_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('tnh_numbers') ?></th>
                <th class="text-center"><?= lang('date') ?></th>
                <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                <th class="text-center"><?= lang('tnh_orders_and_business_plan') ?></th>
                <th class="text-center"><?= lang('tnh_time') ?></th>
                <th class="text-center"><?= lang('note') ?></th>
                <th class="text-center"><?= lang('tnh_note_orders') ?></th>
                <th class="text-center"><?= lang('items') ?></th>
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
    };
    var oTable = '';

    function loadItemsPO(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        productions_orders_items = cData[7];
        cHtml = '';
        if (productions_orders_items != null && productions_orders_items.length > 0) {
            $.each(productions_orders_items, function (index, value) { 
                images = site.base_url+'assets/images/tnh/no_image.png';
                if (value.images) {
                    images = site.base_url+'uploads/products/'+value.images;
                }

                cHtml+= `<div class="row mbot5" style="margin-right: 0px; margin-left: 0px;">
                    <div class="col-md-4" style="padding-right: 0;">
                        <div class="flex-center">
                            <div class="td-image mright5" style="width: 50px;">
                                <div class="preview_image" style="width: auto;">
                                    <div class="display-block contract-attachment-wrapper img">
                                        <div style="width:45px;">
                                            <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                <div class=""><img src="${images}" style="border-radius: 50%"></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="style="width: 80%;">
                                <div class="text-bold">${value.item_name}(${value.item_code})</div>
                                <div class=""><?= lang('quantity') ?>: ${tnhFormatNumber(value.quantity)}</div>
                                <div class="">${value.reference_no}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8" style="padding-right: 0;">
                        ${value.workflow}
                    </div>
                </div>`;
            });
        }
        return `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">${cHtml}</div>`;
    }

    $(document).ready(function () {
        init_datepicker();
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_tnh/getProductionsOrders') ?>', {
            // 'order': [
            //     [2, 'desc']
            // ],
            'fixedHeader': {
                header: true,
            },
            'ordering': false,
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getProductionsOrders') ?>',
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
                    "render": function(data, type, row) {
                        return '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
                    },
                    'width': '30px'
                },
                {
                    "targets": 7,
                    'width': '0px',
                    'visible': false
                },
            ]
        });

        function loadTableReport() {
            oTable.draw();
        };

        $(document).ready(function () {
            $('#tb-reports tbody').on('click', 'td .rows-child', function() {
                var tr = $(this).closest('tr');
                var row = oTable.row(tr);
                if (row.child.isShown()) {
                    $(this).removeClass('fa-caret-down');
                    $(this).addClass('fa-caret-right');
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    $(this).removeClass('fa-caret-right');
                    $(this).addClass('fa-caret-down');
                    row.child( loadItemsPO(row.data()) ).show();
                    tr.addClass('shown');
                }
            });

            $('#tb-reports').on('draw.dt', function() {
                $('.rows-child').click();
            });
        });

    });
</script>