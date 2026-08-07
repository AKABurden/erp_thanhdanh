<?php
    $this->db->select('
        tbltblexport_different_items.product_id as product_id,
        tbltblexport_different_items.type as type,
        tbltblexport_different_items.quantity_net as quantity_net,
        tbltblexport_different_items.price as price,
        tbltblexport_different_items.amount as amount,
        tblwarehouse.name as warehouse_name,
        tbllocaltion_warehouses.name as localtion_warehouses_name,
    ', false);
    $this->db->from('tbltblexport_different_items');
    $this->db->join('tblwarehouse', 'tblwarehouse.id = tbltblexport_different_items.warehouses_id', 'left');
    $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tbltblexport_different_items.localtion_warehouses_id', 'left');
    $this->db->where('tbltblexport_different_items.id_export_different', $export_outsource);
    $export_items = $this->db->get()->result_array();
?>
<table class="table no-table-borderd" style="margin-top: 0px; width: 90%; margin: auto;">
    <tbody>
        <tr class="bold">
            <td class="text-center"><?= lang('tnh_numbers') ?></td>
            <td><?= lang('image') ?></td>
            <td><?= lang('tnh_items') ?></td>
            <td><?= lang('quantity') ?></td>
            <td class="text-center"><?= lang('Đơn giá') ?></td>
            <td class="text-center"><?= lang('Thành tiền') ?></td>
            <td><?= lang('Kho hàng') ?></td>
            <td><?= lang('Vị trí') ?></td>
        </tr>
        <?php if(!empty($export_items)): ?>
        <?php foreach($export_items as $key => $value): ?>
        <?php
                    $type = $value['type'];
                    $id_items = $value['product_id'];
                    $images = '';
                    if ($type == "nvl") {
                        $info = $this->items_model->rowMaterial($id_items);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/'.$info['images']);
                        }
                    } else if ($type == "product") {
                        $info = $this->products_model->rowProduct($id_items);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    } else {
                        continue;
                    }

                    if (empty($images)) {
                        $images = base_url('assets/images/tnh/no_image.png');
                    }

                    $tdImage = '<div class="td-image">'.
                            '<div class="preview_image" style="width: auto;">'.
                                '<div class="display-block contract-attachment-wrapper img">'.
                                    '<div style="width:45px;">'.
                                        '<a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">'.
                                            '<div class="">'.
                                                '<img src="'.$images.'" style="border-radius: 50%">'.
                                            '</div>'.
                                        '</a>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                    '</div>';
                ?>
        <tr>
            <td class="text-center" style="width: 50px;"><?= ++$key ?></td>
            <td class="text-center" style="width: 80px;">
                <?= $tdImage ?>
            </td>
            <td class="text-left" style="width: 120px;"><?= $info['name'] ?>(<?= $info['code'] ?>)</td>
            <td class="text-center" style="width: 50px;"><?= formatNumber($value['quantity_net']) ?></td>
            <td class="text-center" style="width: 120px;"><?= formatMoney($value['price']) ?></td>
            <td class="text-center" style="width: 120px;"><?= formatMoney($value['amount']) ?></td>
            <td class="text-left" style="width: 120px;"><?= $value['warehouse_name'] ?></td>
            <td class="text-left" style="width: 120px;"><?= $value['localtion_warehouses_name'] ?> </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>