<?php
$this->db->select('
    tbl_tranfer_business_item.item_id as id_items,
    "product" as type,
    tbl_tranfer_business_item.quantity as quantity_net,
    tbl_business_plan_items.items_name as items_name,
    tbl_business_plan.reference_no as reference_no
', false);
$this->db->from('tbl_tranfer_business_item');
$this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_tranfer_business_item.id_business_plan', 'inner');
$this->db->join('tbl_business_plan_items', 'tbl_business_plan_items.id = tbl_tranfer_business_item.business_plan_item_id', 'inner');
$this->db->where('tbl_tranfer_business_item.tranfer_business_id', $transfer_id);
$transfer_items = $this->db->get()->result_array();
?>
<table class="table no-table-borderd" style="margin-top: 0px; width: 90%; margin: auto;">
    <tbody>
    <tr class="bold">
        <td><?= lang('tnh_numbers') ?></td>
        <td><?= lang('Mã TP') ?></td>
        <td><?= lang('Tên TP') ?></td>
        <td><?= lang('KHKD') ?></td>
        <td class="text-center"><?= lang('Số lượng') ?></td>
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

            ?>
            <tr>
                <td class="text-center" style="width: 10px;"><?= ++$key ?></td>
                <td class="text-left" style="width: 120px;"><?= $info['code'] ?></td>
                <td class="text-left" style="width: 120px;"><?= $info['name'] ?></td>
                <td class="text-left" style="width: 120px;"><?= '<div>KHKD: '.$value['reference_no'] ?></div><div><?= $value['items_name'] ?></div></td>
                <td class="text-center" style="width: 100px;"><?= formatNumber($value['quantity_net']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
