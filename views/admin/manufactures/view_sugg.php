<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_export_other') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="">
                        <?= lang('tnh_reference_productions_plan') ?>: <?= $productions_plan['reference_no'] ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-view-sugg" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"></th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('Số phiếu xuất kho') ?></th>
                                    <th class="text-center"><?= lang('tnh_material_code') ?></th>
                                    <th class="text-center"><?= lang('tnh_material_name') ?></th>
                                    <th class="text-center"><?= lang('tnh_quantity_export') ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="productions_orders_id_s" id="productions_orders_id_s" class="form-control" value="<?= $productions_orders_id ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
    var oTablePurchases = '';
    var fnserverparamsPurchases = {
        productions_orders_id_s: "#productions_orders_id_s",
    };

    $(document).ready(function() {
        oTablePurchases = tnhInitDataTable('#table-view-sugg', '', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures_temp/getViewSugg') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsPurchases) {
                        d[key] = $(fnserverparamsPurchases[key]).val();
                    }
                    // if (table.attr('data-last-order-identifier')) {
                    //     d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    // }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [
                {
                    "targets": 0,
                    'width': '50px',
                    'searchable': false,
                    'sortable': false,
                    'visible': false
                },
                // {
                //     "targets": 4,
                //     'width': '100px',
                //     'searchable': false,
                //     'sortable': false
                // },
                // {
                //     "targets": 5,
                //     'width': '60px',
                //     'searchable': false,
                //     'sortable': false
                // },
            ]
        })
    });
</script>