<?php
$this->db->select("
        tblpurchases_items.id as id,
        tblpurchases_items.type as type,
        tblpurchases_items.product_id as product_id,
        tblpurchases_items.quantity_net as quantity_net,
    ", false);
$this->db->from('tblpurchases_items');
$this->db->where('tblpurchases_items.purchases_id', $purchases_id);
$purchases_items = $this->db->get()->result_array();
?>
<table class="table no-table-borderd" style="margin-top: 0px; width: 80%; margin: auto;">
    <tbody>
        <tr class="bold">
            <td class="text-center"><?= lang('tnh_numbers') ?></td>
            <td class="text-center"><?= lang('image') ?></td>
            <td class="text-left"><?= lang('tnh_items') ?></td>
            <td class="text-center"><?= lang('quantity') ?></td>
            <td class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= lang('status') ?></td>
        </tr>
        <?php if (!empty($purchases_items)) : ?>
            <?php foreach ($purchases_items as $key => $value) : ?>
                <?php
                $purchases_item_id = $value['id'];
                $type = $value['type'];
                $id_items = $value['product_id'];
                $images = '';
                if ($type == "nvl") {
                    $info = $this->items_model->rowMaterial($id_items);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } else if ($type == "product") {
                    $info = $this->products_model->rowProduct($id_items);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                } else {
                    continue;
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $purchases_order = "(
                    SELECT
                        tblpurchase_order.id,
                        GROUP_CONCAT(distinct tblpurchase_order.id) as purchase_order_id,
                        SUM(tblpurchase_order_items.quantity_suppliers) as quantity
                    FROM tblpurchase_order
                    INNER JOIN tblpurchase_order_items ON tblpurchase_order_items.id_purchase_order = tblpurchase_order.id
                    WHERE tblpurchase_order_items.type = '$type' AND tblpurchase_order_items.product_id = '$id_items' AND tblpurchase_order.id_purchases LIKE '".$purchases_id."'
                )";
                $dtPurchasesOrder = $this->db->query($purchases_order)->row_array();
                $quantityPurchaseOrder = $dtPurchasesOrder['quantity'];
                $quantityImport = 0;
                if (!empty($quantityPurchaseOrder)) {
                    $purchases_order_id = $dtPurchasesOrder['purchase_order_id'];
                    $import = "(
                        SELECT
                            tblimport.id,
                            GROUP_CONCAT(distinct tblimport.id) as purchase_order_id,
                            GROUP_CONCAT(distinct CONCAT(tblimport.prefix, '-', tblimport.code)) as reference_no,
                            SUM(tblimport_items.quantity_net) as quantity
                        FROM tblimport
                        INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
                        WHERE tblimport_items.type = '$type' AND tblimport_items.product_id = '$id_items' AND tblimport.id_order IN (".$purchases_order_id.")
                    )";
                    $dtImport = $this->db->query($import)->row_array();
                    $quantityImport = $dtImport['quantity'];
                    $isImport = ($dtImport['quantity'] > 0) ? 'active' : '';
                }

                //Đi qua gom hàng
                $purchaseToOrder = "(
                    SELECT
                        SUM(tblpurchase_to_order_items.quantity) as quantity_purchase_order,
                        SUM(tblpurchase_to_order_items.quantyti_import) as quantity_import
                    FROM tblpurchase_to_order_items
                    WHERE tblpurchase_to_order_items.type = '$type' AND tblpurchase_to_order_items.id_items = '$id_items' AND tblpurchase_to_order_items.id_purchase = '".$purchases_id."'
                )";
                $dtPurchaseToOrder = $this->db->query($purchaseToOrder)->row_array();
                $quantityPurchaseOrder = (float)$quantityPurchaseOrder + (float)$dtPurchaseToOrder['quantity_purchase_order'];
                $quantityImport = (float)$quantityImport + (float)$dtPurchaseToOrder['quantity_import'];

                $isPurchaseOrder = ($quantityPurchaseOrder > 0) ? 'active' : '';
                $isImport = ($quantityImport > 0) ? 'active' : '';

                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                        '<div class="display-block contract-attachment-wrapper img">' .
                            '<div style="width:45px; margin: auto;">' .
                                '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                                    '<div class="">' .
                                        '<img src="' . $images . '" style="border-radius: 50%">' .
                                    '</div>' .
                                '</a>' .
                                '</div>' .
                            '</div>' .
                        '</div>' .
                    '</div>';

                $txtPurchaseOrders = $quantityPurchaseOrder > 0 ? 'SL: '.formatNumber($quantityPurchaseOrder) : '';
                $txtImport = $quantityImport > 0 ? 'SL: '.formatNumber($quantityImport) : '';
                ?>
                <tr>
                    <td class="text-center" style="width: 10px;"><?= ++$key ?></td>
                    <td class="text-center" style="width: 40px;">
                        <?= $tdImage ?>
                    </td>
                    <td class="text-left" style="width: 120px;"><?= $info['name'] ?>(<?= $info['code'] ?>)</td>
                    <td class="text-center" style="width: 100px;"><?= formatNumber($value['quantity_net']) ?></td>
                    <td class="text-center" style="width: 100px;">
                        <ul class="progressbar" style="display: flex;">
                            <li class="<?= $isPurchaseOrder ?>">
                                <div><?= lang('ĐẶT HÀNG') ?></div>
                                <div class=""><?= $txtPurchaseOrders ?></div>
                            </li>
                            <li class="<?= $isImport ?>">
                                <div><?= lang('NHẬP HÀNG') ?></div>
                                <div class=""><?= $txtImport ?></div>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>