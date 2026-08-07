<?php
$this->db->select('
    tbl_products.code as code,
    tbl_products.name as name,
    tbl_products.images as images,
    tbl_tranfer_business_item.quantity as quantity,
    tbl_business_plan.reference_no as reference_no
', false);
$this->db->from('tbl_tranfer_business_item');
$this->db->join('tbl_products', 'tbl_products.id = tbl_tranfer_business_item.item_id');
$this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_tranfer_business_item.id_business_plan');
$this->db->where('tbl_tranfer_business_item.tranfer_business_id', $transfer_id);
$this->db->where('tbl_tranfer_business_item.order_id', $order_id);
$transfer_items = $this->db->get()->result_array();
?>
<table class="table no-table-borderd" style="margin-top: 0px; width: 90%; margin: auto;">
    <tbody>
        <tr class="bold">
            <td><?= lang('tnh_numbers') ?></td>
            <td><?= lang('image') ?></td>
            <td><?= lang('Mã TP') ?></td>
            <td><?= lang('Tên TP') ?></td>
            <td><?= lang('KHKD') ?></td>
            <td><?= lang('quantity') ?></td>
        </tr>
        <?php if (!empty($transfer_items)) : ?>
            <?php foreach ($transfer_items as $key => $value) : ?>
                <?php
                $images = '';
                $info = '';
                $model = '';
                if (!empty($value['images'])) {
                    $images = base_url('uploads/products/' . $info['images']);
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                    '<div class="display-block contract-attachment-wrapper img">' .
                    '<div style="width:45px;">' .
                    '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                    '<div class="">' .
                    '<img src="' . $images . '" style="border-radius: 50%">' .
                    '</div>' .
                    '</a>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>';

                ?>
                <tr>
                    <td class="text-center" style="width: 10px;"><?= ++$key ?></td>
                    <td class="text-center" style="width: 40px;">
                        <?= $tdImage ?>
                    </td>
                    <td class="text-center" style="width: 120px;">
                        <?= $value['code'] ?>
                    </td>
                    <td class="text-center" style="width: 120px;">
                        <?= $value['name'] ?>
                    </td>
                    <td class="text-center" style="width: 120px;">
                        <?= $value['reference_no'] ?>
                    </td>
                    <td class="text-center" style="width: 50px;"><?= formatNumber($value['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>