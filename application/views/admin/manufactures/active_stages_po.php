<?php
$stage_id = $this->input->post('stage_id');
$productions_orders_id = $this->input->post('m_productions_orders_id');

$tbPOStages = "(
    SELECT
        tbl_productions_orders_items_stages.id as pois_id,
        tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
        tbl_productions_orders_items_stages.number as number,
        tbl_productions_orders_items_stages.active,
        tbl_productions_orders_items_stages.type as type,
        tbl_productions_orders_items_stages.stage_id as stage_id,
        tbl_productions_orders_items_stages.final_stage as final_stage

    FROM tbl_productions_orders_items_stages
    WHERE tbl_productions_orders_items_stages.productions_orders_id = '$productions_orders_id' AND tbl_productions_orders_items_stages.stage_id = '$stage_id' 
) tb_po_stages";

$this->db->select('
    tbl_productions_orders_details.id as pod_id,
    tbl_productions_orders_items.id as poi_id,
    tb_po_stages.pois_id as pois_id,
    tbl_productions_orders_details.reference_no as reference_no,
    tbl_productions_orders_items.type_items as type_items, 
    tbl_productions_orders_items.items_id as items_id,
    tbl_productions_orders_items.quantity as quantity,
    tb_po_stages.number as number, 
    tb_po_stages.type as type, 
    tbl_products.code as item_code,
    tbl_products.name as item_name,
    tblunits.unitid as unit_id,
    tblunits.unit as unit_name,
    tbl_products.id as product_id,
    tb_po_stages.stage_id as stage_id,
    tb_po_stages.active as active,
    tb_po_stages.final_stage as final_stage,
    tbl_productions_orders_details.object_id as object_id,
    tbl_productions_orders_details.object_type as object_type,
', false);
$this->db->from('tbl_productions_orders_items');
$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
$this->db->join($tbPOStages, 'tb_po_stages.poi_id = tbl_productions_orders_items.id');
$this->db->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left');
$this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
// $this->db->group_by('tbl_productions_orders_items.type_items, tbl_productions_orders_items.items_id');
$productions_orders_items = $this->db->get()->result_array();
?>
<table id="tb-handling-products-stages" class="table dataTable">
    <thead>
        <tr>
            <th class="text-center" style="width: 150px;"><?= lang('Số lệnh sản xuất chi tiết') ?></th>
            <th class="text-center" style="width: 150px;"><?= lang('tnh_product_code') ?></th>
            <th class="text-center" style="width: 150px;"><?= lang('tnh_product_name') ?></th>
            <th class="text-center" style="width: 80px;" class="text-center"><?= lang('tnh_unit_manufactures') ?></th>
            <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_to_enter') ?></th>
            <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_entered') ?></th>
            <th class="text-center" style="width: 120px;" class="text-center"><?= lang('quantity') ?></th>
            <th class="text-center" style="width: 120px;" class="text-center"><?= lang('tnh_quantity_errors') ?></th>
            <th class="text-center" style="width: 50px;" class="text-center"><span class="fa fa-trash-o"></span></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $counter = 0; 
            $flagData = false;
        ?>
        <?php if (!empty($productions_orders_items)) : ?>
            <?php foreach ($productions_orders_items as $key => $value) : ?>
                <?php
                    $number = $value['number'];
                    $poi_id = $value['poi_id'];
                    $pod_id = $value['pod_id'];
                    $pois_id = $value['pois_id'];
                    $product_id = $value['product_id'];
                    $object_id = $value['object_id'];
                    $object_type = $value['object_type'];

                    $this->db->select('count(tbl_productions_orders_items_stages.id) as ct', false);
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                    $this->db->where('tbl_productions_orders_items_stages.number <', $number);
                    $this->db->where_in('tbl_productions_orders_items_stages.type', [2]);
                    $ct = $this->db->get()->row_array()['ct'];
                    if ($ct) {
                        $value['type'] = 3;
                    }
                    $type = $value['type'];
                    $stage_id = $value['stage_id'];
                    
                    $quantity_to_enter = 0;// SL đã nhập
                    $quantityInput = 0;
                    $stage_id_pre = 0;
                    if ($type == 3) {
                        $number_pre = $number - 1;
                        $this->db->select('
                            tbl_productions_orders_items_stages.id as id,
                            tbl_stages.id as stage_id,
                            tbl_stages.name as stage_name
                        ');
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                        $this->db->where('tbl_productions_orders_items_stages.number', $number_pre);
                        $dtPoisPre = $this->db->get()->row_array();
                        $stage_id_pre = $dtPoisPre['stage_id'];

                        $query = "(
                            SELECT
                                CONCAT('products', '__', tblwarehouse_items.id_items) as item_cs_id,
                                tblwarehouse_items.type_items as item_type,
                                tblwarehouse_items.id_items as item_id,
                                1 as quantity_singe_primary,
                                1 as quantity_exchange,
                                1 as quantity_single,
                                SUM(tblwarehouse_items.product_quantity) as quantity_primary,
                                SUM(tblwarehouse_items.product_quantity) as quantity,
                                0 as unit_id,
                                0 as unit_parent_id,
                                GROUP_CONCAT(DISTINCT tblwarehouse_items.warehouse_id) as warehouse_id,
                                0 as is_single_use,
                                0 as quantity_order,
                                0 as quota_material_replace_t,
                                1 as landscape_print_size,
                                1 as vertical_print_size,
                                1 as number_children_size,
                                1 as paper_exchange
                            FROM tblwarehouse_items
                            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                            WHERE tblwarehouse_items.type_items = 'product' AND tbllocaltion_warehouses.pod_id = $pod_id AND tbllocaltion_warehouses.stage_id = $stage_id_pre AND tblwarehouse_items.warehouse_id 
                            != ".WAREHOUSES_ERRORS." 
                            GROUP BY tblwarehouse_items.id_items, tblwarehouse_items.type_items
                        )";
                        $item = $this->db->query($query)->row_array();
                        $quantityInput = $item['quantity'];
                        if (empty($quantityInput)) {
                            continue;
                        }

                        // if ($pod_id == 63332) {
                        //     print_arrays($query);
                        // }

                        $this->db->select('SUM(tbl_purchase_product_items.quantity) as quantity', false);
                        $this->db->from('tbl_purchase_products');
                        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                        $this->db->where('tbl_purchase_products.pois_id', $pois_id);
                        $this->db->where('tbl_purchase_product_items.item_id', $product_id);
                        $purchase_products = $this->db->get()->row_array();
                        if (!empty($purchase_products)) {
                            $quantity_to_enter = (float)$purchase_products['quantity'];
                            $item['quantity'] = $quantityInput + $quantity_to_enter;
                        }

                        $flagData = true;
                    } else if ($type == 2) {
                        $this->db->select('
                            tbl_productions_orders_items.id as poi_id,
                            CONCAT("pod__", tbl_products.id) as id,
                            tbl_products.id as product_id,
                            tbl_products.name as name,
                            tbl_products.code as code,
                            tbl_productions_orders_items.quantity as quantity,
                            1 as quantity_primary,
                            1 as quantity_single,
                            1 as quantity_exchange,
                            tbl_productions_orders_items.quantity as quantity_primary,
                            tbl_products.images as images,
                            tbl_products.unit_id as unit_id,
                            tbl_products.unit_id as unit_parent_id,
                            tbl_products.versions as versions,
                            tbl_productions_orders_items.versions_bom as versions_bom,
                            0 as poisub_id
                        ', false);
                        $this->db->from('tbl_productions_orders_details');
                        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                        $this->db->where('tbl_productions_orders_details.id', $pod_id);
                        $item = $this->db->get()->row_array();
                        $quantityInput = $item['quantity'];

                        $this->db->select('SUM(tbl_purchase_product_items.quantity) as quantity', false);
                        $this->db->from('tbl_purchase_products');
                        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                        $this->db->where('tbl_purchase_products.pois_id', $pois_id);
                        $this->db->where('tbl_purchase_product_items.item_id', $product_id);
                        $purchase_products = $this->db->get()->row_array();
                        if (!empty($purchase_products)) {
                            $quantity_to_enter = (float)$purchase_products['quantity'];
                            $tempQuantity = (float)$purchase_products['quantity'] - $item['quantity'];
                            if ($tempQuantity >= 0) {
                                continue;
                            } else {
                                $quantityInput = abs($tempQuantity);
                            }
                        }
                        $flagData = true;
                    } else if ($type == 0 && $value['active']) {
                        continue;
                    }
                    
                    $flagData = true;
                    $tdDelete = '<td class="text-center"><span onclick="removeTrFinished(this)" class="fa fa-remove text-danger pointer"></span></td>';

                    if ($object_type == 'orders') {
                        $this->db->select('tbl_orders.reference_no as reference_no');
                        $this->db->from('tbl_orders');
                        $this->db->where('tbl_orders.id', $object_id);
                        $object = $this->db->get()->row_array();
                    } else if ($object_type == 'business_plan') {
                        $this->db->select('tbl_business_plan.reference_no as reference_no');
                        $this->db->from('tbl_business_plan');
                        $this->db->where('tbl_business_plan.id', $object_id);
                        $object = $this->db->get()->row_array();
                    }
                ?>
                <tr>
                    <td class="text-center">
                        <input type="hidden" name="items[<?= $counter ?>][pod_id]" class="form-control pod_id" value="<?= $value['pod_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][poi_id]" class="form-control poi_id" value="<?= $value['poi_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][pois_id]" class="form-control pois_id" value="<?= $value['pois_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][type_items]" class="form-control type_items" value="<?= $value['type_items'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][items_id]" class="form-control items_id" value="<?= $value['items_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][number]" class="form-control number" value="<?= $value['number'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][type]" class="form-control type" value="<?= $value['type'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][item_code]" class="form-control item_code" value="<?= $value['item_code'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][item_name]" class="form-control item_name" value="<?= $value['item_name'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][unit_id]" class="form-control unit_id" value="<?= $value['unit_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][stage_id]" class="form-control stage_id" value="<?= $value['stage_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][product_id]" class="form-control product_id" value="<?= $value['product_id'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][final_stage]" class="form-control final_stage" value="<?= $value['final_stage'] ?>">
                        <input type="hidden" name="items[<?= $counter ?>][stage_id_pre]" class="form-control stage_id_pre" value="<?= $stage_id_pre ?>">
                        <?= $value['reference_no'] ?>
                        <?php if(!empty($object)): ?>
                            <div class="italic text-primary" style="font-size: 11px;"><?= $object_type == 'orders' ? ''.$object['reference_no'] : ''.$object['reference_no'] ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $value['item_code'] ?></td>
                    <td class="text-center"><?= $value['item_name'] ?></td>
                    <td class="text-center"><?= $value['unit_name'] ?></td>
                    <?php if(!empty($type)): ?>
                        <td class="text-center quantity">
                            <?= formatNumber($item['quantity']) ?>
                        </td>
                        <td class="text-center quantity_to_enter">
                            <?= formatNumber($quantity_to_enter) ?>
                        </td>
                        <td class="text-center">
                            <input type="text" name="items[<?= $counter ?>][quantity_input]" onchange="totalProductsStages()" class="form-control quantity_input" value="<?= formatNumber($quantityInput) ?>">
                        </td>
                        <td class="text-center">
                            <input type="text" name="items[<?= $counter ?>][quantity_error]" onchange="totalProductsStages()" class="form-control quantity_error" value="0">
                        </td>
                    <?php else: ?>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    <?php endif; ?>
                    <?= $tdDelete ?>
                </tr>
                <?php
                    $counter++;
                ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-danger"><?= lang('Không có mặt hàng để hoàn thành') ?></td>
            </tr>
        <?php endif; ?>
        <?php if(!$flagData): ?>
            <tr>
                <td colspan="9" class="text-danger not-data"><?= lang('Không có mặt hàng để hoàn thành') ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="bold">
            <td></td>
            <td class="text-center"><?= lang('tnh_grand_total') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
<script>

    function totalProductsStages() {
        tb = '#tb-handling-products-stages tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var total_quantity_input = 0;
        var total_quantity_error = 0;

        count_errors = 0;
        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];
            quantity_input = intVal($(element).find('.quantity_input').val());
            quantity_error = intVal($(element).find('.quantity_error').val());
            total_quantity_input+= quantity_input;
            total_quantity_error+= quantity_error;
        }

        $('#tb-handling-products-stages tfoot tr td:nth-child(7)').html('<div class="text-center">'+tnhFormatNumber(total_quantity_input)+'</div>');
        $('#tb-handling-products-stages tfoot tr td:nth-child(8)').html('<div class="text-center">'+tnhFormatNumber(total_quantity_error)+'</div>');
    }

    function removeTrFinished(_this) {
        $(_this).closest('tr').remove();
        totalProductsStages();
    }

    $(document).ready(function () {
        totalProductsStages();
    });
</script>