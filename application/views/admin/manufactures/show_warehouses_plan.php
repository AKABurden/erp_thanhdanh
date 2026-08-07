<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Chi tiết kho') ?></h4>
        </div>
        <div class="modal-body">
            <?php
                $dtProduct = $this->products_model->rowProduct($product_id); 
            ?>
            <div class="row hide">
                <div class="col-md-12">
                    <b><?= lang('tnh_product_code') ?>:</b> <?= $dtProduct['code'] ?>
                </div>
                <div class="col-md-12">
                    <b><?= lang('tnh_product_name') ?>:</b> <?= $dtProduct['name'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mtop10">
                    <table id="table-view-keep-orders" class="table dataTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;"></th>
                                <th class="text-center"><?= lang('tnh_product_code') ?></th>
                                <th class="text-center"><?= lang('tnh_product_name') ?></th>
                                <th class="text-center"><?= lang('Đơn hàng bán/KHTP') ?></th>
                                <th class="text-center"><?= lang('tnh_warehouses') ?></th>
                                <th class="text-center"><?= lang('tnh_location_warehouse') ?></th>
                                <th class="text-center"><?= lang('quantity') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="bold">
                                <td></td>
                                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" id="wp_product_id" class="form-control wp_product_id" value="<?= $product_id ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
    var oTableWarehousesPlan = '';
    var fnserverparamsWarehousesPlan = {
        wp_product_id: "#wp_product_id",
    };

    $(document).ready(function() {
        oTableKeepOrders = tnhInitDataTable('#table-view-keep-orders', '', {
            'order': [
                [1, 'desc']
            ],
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures_temp/getWarehousesPlan') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsWarehousesPlan) {
                        d[key] = $(fnserverparamsWarehousesPlan[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    $('#table-view-keep-orders tfoot tr td:nth-child(7)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity)+'</div>');
                    return json.aaData;
                }
            },
        });
    });
</script>