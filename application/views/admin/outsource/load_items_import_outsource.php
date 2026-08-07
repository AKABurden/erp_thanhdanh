<?php
    $this->db->select('
        tbl_import_outsource_items.item_id as item_id,
        tbl_import_outsource_items.type_item as type_item,
        tbl_import_outsource_items.quantity as quantity,
        tbllocaltion_warehouses.name as localtion_warehouses_name,
        tbl_import_outsource_items.object_type as object_type,
        tbl_import_outsource_items.order_id as order_id,
        tbl_import_outsource_items.plan_id as plan_id,
    ', false);
    $this->db->from('tbl_import_outsource_items');
    $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tbl_import_outsource_items.locaiton_to', 'left');
    $this->db->where('tbl_import_outsource_items.import_outsource_id', $import_outsource);
    $import_items = $this->db->get()->result_array();
?>
<table class="table no-table-borderd" style="margin-top: 0px; width: 90%; margin: auto;">
    <tbody>
        <tr class="bold">
            <td class="text-center"><?= lang('tnh_numbers') ?></td>
            <td><?= lang('image') ?></td>
            <td><?= lang('tnh_items') ?></td>
            <td class="text-center"><?= lang('quantity') ?></td>
            <td class="text-center"><?= lang('Đơn vị') ?></td>
            <td><?= lang('Vị trí') ?></td>
        </tr>
        <?php if(!empty($import_items)): ?>
        <?php foreach($import_items as $key => $value): ?>
        <?php
                    $type = $value['type_item'];
                    $id_items = $value['item_id'];
                    $images = '';
                    if ($type == "materials") {
                        $info = $this->items_model->rowMaterial($id_items);
                        $unit = $this->unit_model->rowUnit($info['unit']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/'.$info['images']);
                        }
                    } elseif ($type == "products" || $type == "semi_product") {
                        $info = $this->products_model->rowProduct($id_items);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    } else {
                        continue;
                    }

                    $title_name = '';
                    $order_name = '';
                    if($value['object_type'] == 'orders'){ 
                        $order = get_table_where('tbl_orders',
                        ['id' => $value['order_id']], '', 'row_array');
                        if (!empty($order)) {
                            $order_name = $order['reference_no'];
                        }
                        $title_name = 'Đơn hàng:';
                    } elseif($value['object_type'] == 'business_plan'){
                        $plan = get_table_where('tbl_business_plan',
                        ['id' => $value['plan_id']], '', 'row_array');
                        if (!empty($plan)) {
                            $order_name = $plan['reference_no'];
                        }
                        $title_name = 'KHKD:';
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
            <td class="text-left" style="width: 120px;"><?= $info['name'] ?>(<?= $info['code'] ?>) <div
                    style="font-size:12px;font-style: italic;font-weight: bold;">
                    <?= $title_name?><span><?= $order_name ?></span></div>
            </td>
            <td class="text-center" style="width: 100px;"><?= formatNumber($value['quantity']) ?></td>
            <td class="text-center" style="width: 100px;"><?= $unit['unit'] ?></td>
            <td class="text-left" style="width: 120px;"><?= $value['localtion_warehouses_name'] ?> </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>