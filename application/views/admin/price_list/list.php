<?php
    $year = $this->input->post('year');

    $this->db->select('tbl_category_products.*, tbl_category_products_customers.customers_groups_id, tblcustomers_groups.name as name_group_customer', false);
    $this->db->from('tbl_category_products');
    $this->db->join('tbl_category_products_customers', 'tbl_category_products_customers.category_products_id = tbl_category_products.id', 'left');
    $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tbl_category_products_customers.customers_groups_id', 'left');
    $category_products = $this->db->get()->result_array();
?>

<table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center" style="width: 50px;"><?= lang('STT') ?></th>
            <th class="text-center"><?= lang('Nhóm thành phẩm') ?></th>
            <th class="text-center"><?= lang('Nhóm khách hàng') ?></th>
            <th class="text-center" style="width: 250px;"><?= lang('Giá tiền') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($category_products)): ?>
            <?php foreach($category_products as $key => $value): ?>
                <?php
                    $price_list = $this->price_list_model->getRowPriceList($year, $value['id'], $value['customers_groups_id']); 
                ?>
                <tr>
                    <td class="text-center"><?= ++$key ?></td>
                    <td class="text-center">
                        <?= $value['name'] ?>(<?= $value['code'] ?>)
                    </td>
                    <td class="text-center">
                        <?= $value['name_group_customer'] ?>
                    </td>
                    <td class="">
                        <input type="text" name="price" onchange="changePriceList(this, '<?= $value['id'] ?>', '<?= !empty($value['customers_groups_id']) ? $value['customers_groups_id'] : 0 ?>')" autocomplete="off" placeholder="<?= lang('Giá tiền') ?>" class="form-control money-format" value="<?= !empty($price_list) ? formatMoney($price_list['price']) : '' ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
