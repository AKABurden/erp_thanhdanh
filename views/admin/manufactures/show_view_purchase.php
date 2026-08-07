<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Thông tin số lượng đã yêu cầu') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $arr_id = explode('__', $id);
                        $item_id = $arr_id[1];
                        $type_item = $arr_id[0];
                        if ($type_item == "materials") {
                            $dtInfo = $this->items_model->rowMaterial($item_id);
                            $unit = $this->unit_model->rowUnit($dtInfo['unit_id']);
                        } else if ($type_item == "products") {
                            $dtInfo = $this->products_model->rowProduct($item_id);
                            $unit = $this->unit_model->rowUnit($dtInfo['unit_id']);
                        }
                    ?>
                    <div><b><?= lang('tnh_item_code') ?>:</b> <?= $dtInfo['code'] ?></div>
                    <div><b><?= lang('tnh_item_name') ?>:</b> <?= $dtInfo['name'] ?></div>
                    <div><b><?= lang('tnh_unit_stock') ?>:</b> <?= $unit['unit'] ?></div>
                </div>
                <div class="col-md-12">
                    <div class="info">
                        <div class="body-modal">
                            <table id="tb-purchase" class="table table table-striped table-tb-purchase">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang('Ngày'); ?></th>
                                        <th class="text-center"><?php echo lang('Số phiếu'); ?></th>
                                        <th class="text-center"><?php echo lang('Số lượng yc'); ?></th>
                                        <th class="text-center"><?php echo lang('Trạng thái'); ?></th>
                                        <th class="text-center"><?php echo lang('Trạng thái đặt hàng'); ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="bold">
                                        <td class="text-center"><?= lang('tnh_grand_total') ?></td>
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
            </div>
            <div class="modal-footer">
                
                <input type="hidden" id="_item_id" class="form-control _item_id" value="<?= $id ?>">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var oTablePurchase = '';
    var fnserverparamsPurchase = {
        _item_id: "#_item_id",
        productions_plan_search: "#productions_plan_search",
        start_date_search: "#start_date_search",
        end_date_search: "#end_date_search",
    };

    $(document).ready(function() {
        oTablePurchase = tnhInitDataTable('#tb-purchase', '', {
            'order': [
                [1, 'desc']
            ],
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures_temp/getPurchase') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsPurchase) {
                        d[key] = $(fnserverparamsPurchase[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    $('#tb-purchase tfoot tr td:nth-child(3)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity)+'</div>');
                    return json.aaData;
                }
            },
        });
    });
</script>