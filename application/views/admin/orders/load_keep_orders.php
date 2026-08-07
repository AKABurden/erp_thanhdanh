<?php
$this->db->select('
        tbltransfer_warehouse_detail.id_items as id_items,
        tbltransfer_warehouse_detail.type as type,
        tbltransfer_warehouse_detail.quantity_net as quantity_net,
        tblwarehouse_id.name as warehouse_id_name,
        tblwarehouse_to.name as warehouse_to_name,
        tbllocaltion_warehouses_id.name as localtion_warehouses_id_name,
        tbllocaltion_warehouses_to.name as localtion_warehouses_to_name,
        tbltransfer_warehouse_detail.id_transfer as id_transfer
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
        <td><?= lang('quantity') ?></td>
    </tr>
    <?php if(!empty($transfer_items)): ?>
        <?php foreach($transfer_items as $key => $value): ?>
            <?php
            $type = $value['type'];
            $id_items = $value['id_items'];
            $images = '';
            $info = '';
            $model = '';
            if ($type == "nvl") {
                $info = $this->items_model->rowMaterial($id_items);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/materials/'.$info['images']);
                }
                $model = $info['model'];
            } else if ($type == "product") {
                $info = $this->products_model->rowProduct($id_items);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/products/'.$info['images']);
                }
            } else if ($type == "tools") {
                $info = $this->tools_supplies_model->rowToolsSupplies($id_items);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/tools_supplies/'.$info['images']);
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

            $this->db->select('
                        SUM(tblwarehouse_product.quantity_export) as quantity_export
                    ', false);
            $this->db->from('tblwarehouse_product');
            $this->db->where('tblwarehouse_product.type_export', 2);
            $this->db->where('tblwarehouse_product.import_id', $value['id_transfer']);
            $this->db->where('tblwarehouse_product.product_id', $id_items);
            $this->db->where('tblwarehouse_product.type_items', $type);
            $quantity_export = $this->db->get()->row_array()['quantity_export'];

            ?>
            <tr>
                <td class="text-center" style="width: 10px;"><?= ++$key ?></td>
                <td class="text-center" style="width: 40px;">
                    <?= $tdImage ?>
                </td>
                <td class="text-left" style="width: 120px;">
                    <?= $info['name'] ?>(<?= $info['code'] ?>)
                </td>
                <td class="text-left" style="width: 120px;"><?= $value['warehouse_id_name'] ?> -> <?= $value['localtion_warehouses_id_name'] ?></td>
                <td class="text-left" style="width: 250px;"><?= $value['warehouse_to_name'] ?> -> <?= $value['localtion_warehouses_to_name'] ?></td>
                <td class="text-center" style="width: 50px;"><?= formatNumber($value['quantity_net']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
