<?php
    $this->db->select('
        tbltransfer_warehouse_detail.id_items as id_items,
        tbltransfer_warehouse_detail.type as type,
        tbltransfer_warehouse_detail.quantity_net as quantity_net,
        tblwarehouse_id.name as warehouse_id_name,
        tblwarehouse_to.name as warehouse_to_name,
        tbllocaltion_warehouses_id.name as localtion_warehouses_id_name,
        tbllocaltion_warehouses_to.name as localtion_warehouses_to_name,
        tbltransfer_warehouse_detail.lot_code as lot_code,
        tbltransfer_warehouse_detail.date_sx as date_sx,
        tbltransfer_warehouse_detail.date_sd as date_sd,
        tbltransfer_warehouse_detail.date_use as date_use,
        tbltransfer_warehouse_detail.quantity_unit as quantity_unit
    ', false);
    $this->db->from('tbltransfer_warehouse_detail');
    $this->db->join('tblwarehouse tblwarehouse_id', 'tblwarehouse_id.id = tbltransfer_warehouse_detail.warehouses_id', 'left');
    $this->db->join('tblwarehouse tblwarehouse_to', 'tblwarehouse_to.id = tbltransfer_warehouse_detail.warehouses_to', 'left');
    $this->db->join('tbllocaltion_warehouses tbllocaltion_warehouses_id', 'tbllocaltion_warehouses_id.id = tbltransfer_warehouse_detail.localtion_id', 'left');
    $this->db->join('tbllocaltion_warehouses tbllocaltion_warehouses_to', 'tbllocaltion_warehouses_to.id = tbltransfer_warehouse_detail.localtion_to', 'left');
    $this->db->where('tbltransfer_warehouse_detail.id_transfer', $transfer_id);
    $transfer_items = $this->db->get()->result_array();
?>
<table class="table no-table-borderd" style="margin-top: 0px; width: 90%; margin: auto;">
    <tbody>
        <tr class="bold">
            <td><?= lang('tnh_numbers') ?></td>
            <td><?= lang('image') ?></td>
            <td><?= lang('tnh_items') ?></td>
            <td><?= lang('ch_localhost_warehouse') ?></td>
            <td><?= lang('ch_localhost_warehouse_N') ?></td>
            <td><?= lang('tnh_quantity_unit') ?></td>
            <td><?= lang('tnh_quantity_unit_stock') ?></td>
        </tr>
        <?php if(!empty($transfer_items)): ?>
            <?php foreach($transfer_items as $key => $value): ?>
                <?php
                    $type = $value['type'];
                    $id_items = $value['id_items'];
                    $images = '';
                    if ($type == "nvl") {
                        $info = $this->items_model->rowMaterial($id_items);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    } else if ($type == "product") {
                        $info = $this->products_model->rowProduct($id_items);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/'.$info['images']);
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
                    <td class="text-center" style="width: 10px;"><?= ++$key ?></td>
                    <td class="text-center" style="width: 40px;">
                        <?= $tdImage ?>
                    </td>
                    <td class="text-left" style="width: 120px;"><?= $info['name'] ?>(<?= $info['code'] ?>)</td>
                    <td class="text-left" style="width: 120px;"><?= '<div>Kho hàng: '.$value['warehouse_id_name'] ?></div> <div>Vị trí: <?= $value['localtion_warehouses_id_name'] ?></div></td>
                    <td class="text-left" style="width: 250px;">
                        <div><span class="bold">Kho hàng:</span> <?= $value['warehouse_to_name'] ?></div>
                        <div><span class="bold">Vị trí:</span> <?= $value['localtion_warehouses_to_name'] ?></div>
                        <div><span class="bold">Lot:</span> <?= $value['lot_code'] ?></div>
                        <div><span class="bold">Ngày SX:</span> <?= !empty($value['date_sx']) ? _d($value['date_sx']) : '' ?></div>
                        <div><span class="bold">Ngày SD:</span> <?= !empty($value['date_sd']) ? _d($value['date_sd']) : '' ?></div>
                    </td>
                    <td class="text-center" style="width: 100px;"><?= formatNumber($value['quantity_unit']) ?></td>
                    <td class="text-center" style="width: 100px;"><?= formatNumber($value['quantity_net']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
