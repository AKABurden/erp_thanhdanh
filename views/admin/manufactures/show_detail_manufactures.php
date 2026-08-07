<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_list_manufactures') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <div class="mbot10"><span class="label btn-success"><?= lang('orders') ?></span></div>
                        <table id="table-view-items" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"></th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('customers') ?></th>
                                    <th class="text-center"><?= lang('tnh_orders') ?></th>
                                    <th class="text-center"><?= lang('quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="bold">
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <div class="mbot10"><span class="label btn-primary"><?= lang('business_plan') ?></span></div>
                        <table id="table-view-items-business" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"></th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('tnh_business_plan') ?></th>
                                    <th class="text-center"><?= lang('tnh_plan_name') ?></th>
                                    <th class="text-center"><?= lang('quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="bold">
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
    var oTableModal = '';
    var fnserverparamsModal = {
        start_date_search: "<?= $this->input->post('start_date_search') ?>",
        end_date_search: "<?= $this->input->post('end_date_search') ?>",
        customer_search: "<?= $this->input->post('customer_search') ?>",
        product_id: "<?= $this->input->post('product_id') ?>",
    };

    $(document).ready(function() {
        oTableModal = tnhInitDataTable('#table-view-items', '', {
            'order': [
                [1, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getShowDetailManufactures') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsModal) {
                        d[key] = fnserverparamsModal[key];
                    }
                },
                "dataSrc": function(json) {
                    $('#table-view-items tfoot tr td:nth-child(5)').html('<div class="text-center">'+tnhFormatNumber(json.totalQuantity)+'</div>');
                    return json.aaData;
                }
            },
        });

        oTableModalBusinessPlan = tnhInitDataTable('#table-view-items-business', '', {
            'order': [
                [1, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getShowDetailManufacturesBusinessPlan') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsModal) {
                        d[key] = fnserverparamsModal[key];
                    }
                },
                "dataSrc": function(json) {
                    $('#table-view-items-business tfoot tr td:nth-child(5)').html('<div class="text-center">'+tnhFormatNumber(json.totalQuantity)+'</div>');
                    return json.aaData;
                }
            },
        });
    });
</script>