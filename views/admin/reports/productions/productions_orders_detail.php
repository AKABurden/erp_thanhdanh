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
</style>
<div class="text-center uppercase">
    <h2><?= lang('report_order_progress') ?></h2>
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
                <th class="text-center"><?= lang('id') ?></th>
                <th class="text-center"><?= lang('tnh_orders_and_business_plan') ?></th>
                <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                <th class="text-center"><?= lang('tnh_reference_productions_orders_details') ?></th>
                <th class="text-center"><?= lang('tnh_product_name') ?></th>
                <th class="text-center"><?= lang('tnh_quantity_nk') ?></th>
                <th class="text-center"><?= lang('tnh_status') ?></th>
                <th class="text-center"><?= lang('tnh_time') ?></th>
                <th class="text-center"><?= lang('tnh_note_orders') ?></th>
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

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function () {
        init_datepicker();
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_tnh/getProductionsOrdersDetail') ?>', {
            'order': [
                [2, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getProductionsOrdersDetail') ?>',
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
                    "name": 'reference_orders',
                    'width': '130px',
                    'className': 'text-left',
                    'sortable': false
                },
                {
                    "targets": 2,
                    "name": 'reference_no_order',
                    "width": "130px"
                },
                {
                    "render": function(data, type, row) {
                        return '<a class="" title="<?= lang('tnh_detail') ?>" target="_blank" href="<?= base_url('admin/manufactures/detail_productions/') ?>' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 3,
                    "name": 'reference_no',
                    "width": "110px"
                },
                {
                    "render": function(data, type, row) {
                        images = site.base_url + 'assets/images/tnh/no_image.png';
                        if (data) {
                            data = data.split('___');
                            txtReferenceObject = data[1];
                            sl = data[2];
                            precent = data[3];
                            data = data[0].split('||');
                            if (data[0]) {
                                images = site.base_url + 'uploads/products/' + data[0];
                            }
                            str = '<table class="tnh-table" style="width: 100%;"><tbody>';
                            str += `<tr>
                                <td style="width: 5%; padding: 5px !important;">
                                    <div class="td-image">
                                        <div class="preview_image" style="width: auto;">
                                            <div class="display-block contract-attachment-wrapper img">
                                                <div style="width:35px;">
                                                    <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                        <div class="">
                                                            <img src="${images}" style="border-radius: 50%">
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 95%; padding: 5px !important;">
                                    ${data[1]}
                                    <div><?= lang('quantity') ?>: ${tnhFormatNumber(sl)}</div>
                                    ${txtReferenceObject}
                                    <div class="progress" style="margin-bottom: 0;">
                                        <div class="progress-bar progress-bar-success-green progress-bar-cs" role="progressbar" aria-valuenow="${precent}"
                                        aria-valuemin="0" aria-valuemax="100" style="width:${formatDecimalToFixed(precent, 0)}%">
                                            ${formatDecimalToFixed(precent, 0)}%
                                        </div>
                                    </div>
                                </td>
                            </tr>`;
                            str += '</tbody></table>';
                            return str;
                        }
                        return data;
                    },
                    "targets": 4,
                    "name": 'item_name',
                    "width": "200px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 5,
                    "name": 'quantity_finished',
                    "width": "100px"
                },
                {
                    'className': 'text-left',
                    "targets": 6,
                    "name": 'status',
                    "width": "170px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + (data) + '</div>';
                    },
                    "targets": 7,
                    "name": 'time',
                    "width": "100px"
                },
            ]
        });
    });
</script>