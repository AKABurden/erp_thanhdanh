<style>
    #tb-items tr th {
        min-width: 100px;
        width: 100px;
    }

    /* #tb-items tr td:nth-child(2) {
        min-width: 100px;
        width: 100px;
    }*/

    .div-table-data-production-list input::placeholder {
        /* font-size: 8px; */
    }
</style>

<?php
$type_productionlist_id = $this->input->post('type_productionlist_id');
$production_list_id = $this->input->post('production_list_id');
$start_date = $this->input->post('start_date');
$end_date = $this->input->post('end_date');

$tbProductionsPlanOrdersByOrders = "(
    SELECT
        tbl_productions_plan_orders.productions_order_id as productions_order_id,
        GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders
    FROM tbl_productions_plan_orders
    INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
    INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
    WHERE tbl_productions_plan_orders.object_type = 'orders'
    GROUP BY tbl_productions_plan_orders.productions_order_id
) tb_orders";

$tbProductionsPlanOrdersByBusinessPlan = "(
    SELECT
        tbl_productions_plan_orders.productions_order_id as productions_order_id,
        GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
    FROM tbl_productions_plan_orders
    INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
    WHERE tbl_productions_plan_orders.object_type = 'business_plan'
    GROUP BY tbl_productions_plan_orders.productions_order_id
) tb_business_plan";

$tbPurchasesErrors = "(
    SELECT
        tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
        SUM(tbl_purchase_products.total_quantity) as quantity_errors
    FROM tbl_purchase_products
    WHERE tbl_purchase_products.is_errors = 1
    GROUP BY tbl_purchase_products.productions_orders_details_id
) tb_purchases_errors";

// $tbDateDelivery = "(
//     SELECT
//         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
//         tbl_productions_orders_items.items_id as items_id,
//         MIN(tbl_order_item_shippings.date_shipping) as date_shipping
//     FROM tbl_productions_orders_items
//     LEFT JOIN tbl_order_item_shippings ON tbl_order_item_shippings.order_item_id = tbl_productions_orders_items.production_plan_item_id AND object_item_type = 'orders'
//     LEFT JOIN tbl_business_plan_items_date ON tbl_business_plan_items_date.business_plan_items_id = tbl_productions_orders_items.production_plan_item_id AND object_item_type = 'business_plan'
//     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
//  ) tb_date_delivery";

// $tbDateDelivery = "(
//     SELECT
//         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
//         tbl_productions_orders_items.items_id as items_id,
//         MIN(tbl_productions_plan_details.date) as date_shipping
//     FROM tbl_productions_plan_items
//     INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
//     JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
//     WHERE tbl_productions_plan_items.is_preventive = 0
//     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
//  ) tb_date_delivery";

$tbDateDelivery = "(
    SELECT
        tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        MIN(tbl_productions_plan_details.date) as date_shipping
    FROM tbl_productions_plan_items
    INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
    JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
    WHERE tbl_productions_plan_items.is_preventive = 0
    GROUP BY tbl_productions_orders_items.productions_orders_id
) tb_date_delivery";

$tbDateExport = "(
    SELECT
        tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
        tbl_productions_orders_items_stages.date_active as date_active
    FROM tbl_productions_orders_items_stages
    WHERE tbl_productions_orders_items_stages.stage_id = '" . STAGES_MATERIAL . "' AND tbl_productions_orders_items_stages.date_active IS NOT NULL
    GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
 ) tb_date_export";


//  LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id AND tb_date_delivery.items_id = tbl_productions_orders_items.items_id
$tbProductionsOrderItems = "(
    SELECT
        tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        tb_date_delivery.date_shipping as date_shipping,
        tb_date_export.date_active as date_export,
        tbl_productions_plan.note as note_plan,
        tbl_productions_orders_items.items_id as items_id,
        tbl_productions_orders_items.items_name as items_name,
        tbl_productions_orders_items.items_code as items_code,
        tbl_products.quantity_child_molds as quantity_child_molds,
        SUM(tbl_productions_orders_items.quantity) as quantity,
        SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        tbl_productions_orders_items.plan_id as plan_id,
        SUM(tb_purchases_errors.quantity_errors) as quantity_errors
    FROM tbl_productions_orders_items
    INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
    INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
    INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
    LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
    LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
    LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
    GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
) tb_production_order_item";
// print_arrays($tbProductionsOrderItems);

$this->db->select('
    tbl_productions_orders.id as id,
    tbl_productions_orders_items_stages.stage_id as stage_id,
    tbl_productions_orders.date as date,
    tbl_productions_orders.reference_no as reference_no,
    tbl_products.code as items_code,
    tbl_products.name as items_name,
    tb_production_order_item.date_shipping as date_delivery,
    tb_production_order_item.date_export as date_export,
    tb_production_order_item.quantity_child_molds as quantity_child_molds,
    tb_production_order_item.quantity as quantity,
    tb_production_order_item.quantity_warehoused as quantity_finished,
    tb_production_order_item.quantity_errors as quantity_errors,
    tbl_category_stages.name as name_category_stage,
    tbl_stages.name as name_tagert,
    tb_production_order_item.note_plan as note,
    tbl_type_print.name as name_type_print,
    tbl_productions_orders.status_details,
    tb_orders.reference_no_orders as reference_no_orders,
    tb_business_plan.reference_no_business_plan as reference_no_business_plan,
    tbl_productions_orders.total_quantity as total_quantity,
    tb_production_order_item.items_id as items_id,
    tbl_productions_orders.status_orders as status_orders,
    tb_production_order_item.plan_id as plan_id,
    tbl_category_stages.is_in as is_in,
    GROUP_CONCAT(DISTINCT tb_production_order_item.items_id) as items_id,
    tbl_products.quantity_child_molds_offset as quantity_child_molds_offset,
    tbl_products.quantity_child_molds_flexo as quantity_child_molds_flexo,
    GROUP_CONCAT(DISTINCT tbl_productions_orders_items_stages.face) as face,
    GROUP_CONCAT(DISTINCT tbl_productions_orders_items_stages.face_after) as face_after,
    GROUP_CONCAT(DISTINCT IF(tbl_category_stages.is_in = 1, tbl_stages.name, "") SEPARATOR "<br>") as stage_name,
    GROUP_CONCAT(DISTINCT IF(tbl_category_stages.is_in = 1, tbl_stages.id, "") SEPARATOR ",") as str_stage_id,
', false);
$this->db->from('tbl_productions_orders');
$this->db->join($tbProductionsOrderItems, 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id', 'inner');
$this->db->join('tbl_products', 'tbl_products.id = tb_production_order_item.items_id');
$this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
$this->db->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left');
$this->db->join($tbProductionsPlanOrdersByBusinessPlan, 'tb_business_plan.productions_order_id = tbl_productions_orders.id', 'left');

$this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
$this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
$this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
// $this->db->join('tbl_type_productionlist', 'tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id');
$this->db->where('tbl_productions_orders.date >=', '2023-02-01 00:00:00');
$this->db->where('tbl_productions_orders.date >=', $start_date.' 00:00:00');
$this->db->where('tbl_productions_orders.date <=', $end_date.' 23:59:59');
// $this->db->where('tbl_category_stages.is_in', 1);
$this->db->where('tbl_category_stages.type_productionlist_id', $type_productionlist_id);
// $this->db->where(' exists (
//     SELECT tbl_productions_orders_items_stages.id
//     FROM tbl_productions_orders_items_stages
//     INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
//     INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
//     WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id AND tbl_category_stages.type_productionlist_id = '.$type_productionlist_id.'
// )', false, false);

$this->db->where(' NOT EXISTS (
    SELECT 1
    FROM tbl_production_lists_total
    INNER JOIN tbl_production_lists_items ON tbl_production_lists_items.production_list_total_id = tbl_production_lists_total.id
    WHERE tbl_production_lists_total.type_productionlist_id = '.$type_productionlist_id.' AND tbl_production_lists_items.stage_id = tbl_productions_orders_items_stages.stage_id AND tbl_productions_orders.id = tbl_production_lists_items.po_id AND tbl_production_lists_total.production_list_id != '.$production_list_id.'
)', false, false);

$this->db->group_by('tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id');
$result = $this->db->get()->result_array();
// print_arrays($result);
$dtCategoryStages = get_table_where('tbl_type_productionlist', ['id' => $type_productionlist_id], '', 'row_array');

//edit
$this->db->select('tbl_production_lists_total.*', false);
$this->db->from('tbl_production_lists_total');
$this->db->where('tbl_production_lists_total.production_list_id', $production_list_id);
$this->db->where('tbl_production_lists_total.type_productionlist_id', $type_productionlist_id);
$production_lists_total = $this->db->get()->row_array();

$trHtml = '';
$group_id = 0;
$counter = 0;
if (!empty($result))
    $this->db->select('tbl_machines.id, tbl_machines.code, tbl_machines.name');
    $this->db->join('tbl_machines_stage', 'tbl_machines_stage.machines_id = tbl_machines.id', 'inner');
    $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_machines_stage.category_stage_id', 'inner');
    $this->db->where('tbl_category_stages.type_productionlist_id', $type_productionlist_id);
    $this->db->order_by('tbl_machines.code');
    $optionMachine = $this->db->get('tbl_machines')->result_array();
    foreach ($result as $key => $aRow) {

        $productions_orders_id = $aRow['id'];
        $items_id = $aRow['items_id'];

        $flagGroup = false;
        if ($group_id != $productions_orders_id && $aRow['is_in']) {
            $group_id = $productions_orders_id;
            $flagGroup = true;
        }

        $this->db->select('
            tbl_products.code as product_code,
            tbl_products.name as product_name,
            GROUP_CONCAT(DISTINCT ppb_materials.landscape_print_size SEPARATOR "<br>") as landscape_print_size,
            GROUP_CONCAT(DISTINCT ppb_materials.number_children_size SEPARATOR "<br>") as number_children_size,
            SUM(ppb_materials.paper_exchange) as paper_exchange,
        ', false);
        $this->db->from('tbl_productions_plan_bom ppb_primary');
        $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        $this->db->where('ppb_primary.parent_id', 0);
        $this->db->where('(ppb_materials.item_type)', 'materials');
        $this->db->where('(tbl_productions_orders_items.items_id)', $items_id);
        $dtQuantityNew = $this->db->get()->row_array();

        $plan_id = $aRow['plan_id'];
        if ($aRow['is_in'] == 1) {
            $this->db->select('
                (ppb_materials.item_type) as type, 
                (ppb_materials.item_id), 
                (ppb_materials.landscape_print_size), 
                (ppb_materials.number_children_size), 
                (ppb_materials.unit_parent_id), 
                (ppb_materials.quantity_single),
                SUM(ppb_materials.quantity) as quantity,
                (ppb_materials.quantity_single) as quantity_single,
            ', false);
        } else {
            $this->db->select('
                (ppb_materials.item_type) as type, 
                (ppb_materials.item_id), 
                (ppb_materials.landscape_print_size), 
                (ppb_materials.number_children_size), 
                (ppb_materials.unit_parent_id), 
                (ppb_materials.quantity_single),
                SUM(ppb_materials.quantity) as quantity,
                (ppb_materials.quantity_single) as quantity_single,
            ', false);
        }
        $this->db->from('tbl_productions_plan_bom ppb_primary');
        $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        $this->db->where('ppb_primary.parent_id', 0);
        $this->db->where('(tbl_productions_orders_items.items_id IN (' . $items_id . '))');

        $this->db->where('(
            ppb_materials.item_type IN ("semi_products", "semi_products_outside")
            OR exists (
                SELECT
                    tbl_materials.id
                FROM tbl_materials
                INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
            )
        )', false, false);

        $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
        $bom = $this->db->get()->result_array();

        $total_paper_exchange = 0;
        $total_quantity_compensation = 0;
        if (!empty($bom)) {
            foreach ($bom as $kB => $vB) {
                $item_id = $vB['item_id'];
                $type = $vB['type'];
                $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

                $quantity = ceil($vB['quantity']);
                $quantity_single = $vB['quantity_single'];
                $quantity_need = $quantity + $quantity_compensation;
                $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                $total_paper_exchange += $paper_exchange;

                $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                $total_quantity_compensation += $quantity_compensation;
            }
        }
        $quantityNew = $total_paper_exchange;
        //

        $face = array_unique(explode(',', $aRow['face']));
        $face_after = array_unique(explode(',', $aRow['face_after']));
        $countFace = 0;
        if (in_array(1, $face)) {
            $countFace++;
        }

        if (in_array(2, $face_after)) {
            $countFace++;
        }

        //edit
        $dtProductionListsItem = null;
        if (!empty($production_list_id)) {
            $this->db->select('tbl_production_lists_items.*', false);
            $this->db->from('tbl_production_lists_items');
            $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
            $this->db->where('tbl_production_lists_items.po_id', $aRow['id']);
            $this->db->where('tbl_production_lists_items.stage_id', $aRow['stage_id']);
            $dtProductionListsItem = $this->db->get()->row_array();
        }    

        $this->db->select('SUM(quantity) as quantity');
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('productions_orders_id',$productions_orders_id);
        $this->db->where('items_id IN ('.$items_id.')');
        $this->db->where('object_item_type','business_plan');
        $quantityDp = $this->db->get()->row_array()['quantity'];


        $this->db->select('SUM(quantity) as quantity,SUM(quantity_warehoused) as quantity_warehoused');
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_orders_details','tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->where('tbl_productions_orders_items.productions_orders_id',$productions_orders_id);
        $this->db->where('tbl_productions_orders_items.items_id IN ('.$items_id.')');
        $this->db->where('object_item_type','orders');
        $quantityAll = $this->db->get()->row_array();
        $aRow['quantity'] = (float)$quantityAll['quantity'] + (float)$quantityDp;

        $_item_id = '';
        if ($aRow['items_id']) {
            $_item_id = explode(',', $aRow['items_id']);
            $_item_id = $_item_id[0];
        }

        $tdLenhSanXuat = '<td>
            <input type="hidden" name="items[' . $counter . '][po_id]" class="form-control" value="'.$aRow['id'].'">
            <input type="hidden" name="items[' . $counter . '][item_id]" class="form-control" value="'.$_item_id.'">
            <input type="hidden" name="items[' . $counter . '][to_in]" class="form-control" value="'.$quantityNew.'">
            <input type="hidden" name="items[' . $counter . '][ngay_mo_lsx]" class="form-control" value="'.date_format(date_create($aRow['date']), 'd/m/Y').'">
            <input type="hidden" name="items[' . $counter . '][ngay_giao_hang_he_thong]" class="form-control" value="'._d($aRow['date_delivery']).'">
            <input type="hidden" name="items[' . $counter . '][stage_name]" class="form-control" value="'.(str_replace('<br>', ',', $aRow['stage_name'])).'">
            <input type="hidden" name="items[' . $counter . '][str_stage_id]" class="form-control" value="'.($aRow['str_stage_id']).'">
            <input type="hidden" name="items[' . $counter . '][so_con_tren_to_in]" class="form-control" value="'.($dtQuantityNew['number_children_size']).'">
            <input type="hidden" name="items[' . $counter . '][so_con_tren_kb_offset]" class="form-control" value="'.($aRow['quantity_child_molds_offset']).'">
            <input type="hidden" name="items[' . $counter . '][thoi_gian_con_lai]" class="form-control thoi_gian_con_lai" value="0">

            <input type="hidden" name="items[' . $counter . '][so_luong_san_xuat]" class="form-control so_luong_san_xuat" value="'.$aRow['quantity'].'">
            <input type="hidden" name="items[' . $counter . '][so_con_tren_kb_flexo]" class="form-control so_con_tren_kb_flexo" value="'.$aRow['quantity_child_molds_flexo'].'">
            <input type="hidden" name="items[' . $counter . '][so_con_tren_kb]" class="form-control so_con_tren_kb" value="'.$aRow['quantity_child_molds'].'">
            <input type="hidden" name="items[' . $counter . '][stage_id]" class="form-control stage_id" value="'.$aRow['stage_id'].'">
            ' . $aRow['reference_no'] . '
        </td>';
        $tdMaSanPham = '<td>' . $aRow['items_code'] . '</td>';
        $tdSoLuongSanXuat = '<td class="text-right td-so-luong-san-xuat">' . formatNumber($aRow['quantity']) . '</td>';
        $tdSoToIn = '<td class="text-right td-to-in">' . formatNumber($quantityNew) . '</td>';
        $tdSoConTrenToIn = '<td class="text-right td-so-con-tren-to-in">' . $dtQuantityNew['number_children_size'] . '</td>';
        $tdSoConTrenKBFlexo = '<td class="text-right td-so-con-tren-kb-flexo">' . formatNumber($aRow['quantity_child_molds_flexo']) . '</td>';

        // $quantityTuaInFlexo = 0;
        // if ($aRow['quantity_child_molds_flexo'] > 0) {
        //     $quantityTuaInFlexo = $aRow['quantity'] / $aRow['quantity_child_molds_flexo'];
        // ' . formatNumber($quantityTuaInFlexo) . '
        // }

        $tdSoTuaInFlexo = '<td class="text-right td-so-tua-in-flexo"></td>';
        $tdThoiGianIn = '<td class="text-right td-thoi-gian-in"></td>';
        $tdThoiGianCanhBai = '<td class="text-right td-thoi-gian-canh-bai">
            <input type="text" name="items[' . $counter . '][thoi_gian_canh_bai]" placeholder="'.lang('Thời gian canh bài').'" onchange="totalProductionList('.$type_productionlist_id.', 1)" style="padding: 5; height: 30px;" class="form-control number-format thoi_gian_canh_bai" value="'.(!empty($dtProductionListsItem['thoi_gian_canh_bai']) ? ($dtProductionListsItem['thoi_gian_canh_bai']) : '').'">
        </td>';
        $tdThoiGianXuLy = '<td class="text-right td-thoi-gian-xu-ly"></td>';
        $tdNgayMoLSX = '<td class="text-right td-ngay-mo-lsx">'.date_format(date_create($aRow['date']), 'd/m/Y').'</td>';
        $tdNgayGiaoHangHeThong = '<td class="text-center td-ngay-giao-hang-he-thong">'._d($aRow['date_delivery']).'</td>';
        $tdNgayGiaoHang = '<td class="text-right td-ngay-giao-hang">
            <input type="text" name="items[' . $counter . '][ngay_giao_hang]" placeholder="'.lang('Ngày giao hàng').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control ngay_giao_hang datepicker" value="'.(!empty($dtProductionListsItem['ngay_giao_hang']) ? _d($dtProductionListsItem['ngay_giao_hang']) : '').'">
        </td>';
        $tdNgayVeNVLDuKien = '<td class="text-right td-ngay-ve-nvl-du-kien">
            <input type="text" name="items[' . $counter . '][ngay_ve_nvl_du_kien]" placeholder="'.lang('Ngày về NVL dự kiến').'" style="padding: 5; height: 30px;" class="form-control ngay_ve_nvl_du_kien datepicker" value="'.(!empty($dtProductionListsItem['ngay_ve_nvl_du_kien']) ? _d($dtProductionListsItem['ngay_ve_nvl_du_kien']) : '').'">
        </td>';
        $tdNgayBanGiaoSanXuat = '<td class="text-right td-ngay-ban-giao-san-xuat">
            <input type="text" name="items[' . $counter . '][ngay_ban_giao_san_xuat]" placeholder="'.lang('Ngày bàn giao sản xuất').'" style="padding: 5; height: 30px;" class="form-control ngay_ban_giao_san_xuat datepicker" value="'.(!empty($dtProductionListsItem['ngay_ban_giao_san_xuat']) ? _d($dtProductionListsItem['ngay_ban_giao_san_xuat']) : '').'">
        </td>';
        $tdNgayBatDauDuKien = '<td class="text-right td-ngay-bat-dau-du-kien">
            <input type="text" name="items[' . $counter . '][ngay_bat_dau_du_kien]" placeholder="'.lang('Ngày bắt đầu dự kiến').'" onchange="totalProductionList('.$type_productionlist_id.', 1)" style="padding: 5; height: 30px;" class="form-control ngay_bat_dau_du_kien datepicker" value="'.(!empty($dtProductionListsItem['ngay_bat_dau_du_kien']) ? _d($dtProductionListsItem['ngay_bat_dau_du_kien']) : '').'">
        </td>';
        $tdNgayKetThuc = '<td class="text-right td-ngay-ket-thuc">
            <input type="text" name="items[' . $counter . '][ngay_ket_thuc]" placeholder="'.lang('Ngày kết thúc').'" style="padding: 5; height: 30px;" class="form-control ngay_ket_thuc datepicker" value="'.(!empty($dtProductionListsItem['ngay_ket_thuc']) ? _d($dtProductionListsItem['ngay_ket_thuc']) : '').'">
        </td>';
        $tdTinhTrang = '<td class="text-right td-tinh-trang">
            <input type="text" name="items[' . $counter . '][tinh_trang]" placeholder="'.lang('Tình trạng').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control tinh_trang" value="'.(!empty($dtProductionListsItem['tinh_trang']) ? ($dtProductionListsItem['tinh_trang']) : '').'">
        </td>';
        $tdGhiChu = '<td class="text-right td-ghi-chu">
            <input type="text" name="items[' . $counter . '][ghi_chu]" placeholder="'.lang('Ghi chú').'" style="padding: 5; height: 30px;" class="form-control tinh_trang" value="'.(!empty($dtProductionListsItem['ghi_chu']) ? ($dtProductionListsItem['ghi_chu']) : '').'">
        </td>';
        // $tdMayIn = '<td class="text-right td-may-in">
        //     <input type="text" name="items[' . $counter . '][may_in]" placeholder="'.lang('Máy in').'" style="padding: 5; height: 30px;" class="form-control may_in" value="'.(!empty($dtProductionListsItem['may_in']) ? ($dtProductionListsItem['may_in']) : '').'">
        // </td>';
        $tdMayIn = '<td class="text-right td-may-in">
            '.render_select('items[' . $counter . '][may_in]', $optionMachine, ['id', 'code', 'name'], '', (!empty($dtProductionListsItem['may_in']) ? ($dtProductionListsItem['may_in']) : '')).'
        </td>';
        $tdCongDoan = '<td class="text-center td-cong-doan">'.$aRow['stage_name'].'</td>';
        $tdThoiGianConLai = '<td class="text-right td-thoi-gian-con-lai"></td>';

        $tdTongTua = '<td class="text-right td-tong-tua"></td>';
        $tdNgayHoanThanhIn = '<td class="text-right td-ngay-hoan-thanh-in">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_in]" placeholder="'.lang('Ngày hoàn thành in').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control ngay_hoan_thanh_in datepicker" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_in']) ? _d($dtProductionListsItem['ngay_hoan_thanh_in']) : '').'">
        </td>';
        $tdSoConTrenKBOffset = '<td class="text-right td-so-con-tren-kb-offset">' . formatNumber($aRow['quantity_child_molds_offset']) . '</td>';
        $tdTuaSauIn = '<td class="text-right td-tua-sau-in"></td>';
        $tdSoMatIn = '<td class="text-right td-so-mat-in">
            <input type="text" name="items[' . $counter . '][so_mat_in]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control number-format so_mat_in" value="' . (!empty($dtProductionListsItem) ? $dtProductionListsItem['so_mat_in'] : $countFace) . '">
        </td>';

        $tdBongMang = '<td class="text-right td-bong-mang">
            <input type="text" name="items[' . $counter . '][bong_mang]" placeholder="'.lang('Bóng màng').'" style="padding: 5; height: 30px;" class="form-control bong_mang" value="'.(!empty($dtProductionListsItem['bong_mang']) ? ($dtProductionListsItem['bong_mang']) : '').'">
        </td>';

        $tdHoanThanh = '<td class="text-right td-hoan-thanh">
            <input type="text" name="items[' . $counter . '][hoan_thanh]" placeholder="'.lang('Hoàn thành').'" style="padding: 5; height: 30px;" class="form-control hoan-thanh" value="'.(!empty($dtProductionListsItem['hoan_thanh']) ? ($dtProductionListsItem['hoan_thanh']) : '').'">
        </td>';

        $tdDauIn = '<td class="text-right td-dau-in">
            <input type="text" name="items[' . $counter . '][dau_in]" placeholder="'.lang('Đầu in').'" style="padding: 5; height: 30px;" class="form-control number-format dau-in" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['dau_in']) : '300').'">
        </td>';
        $tdNangSuat = '<td class="text-right td-nang-suat"></td>';
        $tdSoTuaIn = '<td class="text-right td-so-tua-in"></td>';
        $tdThoiGianIn = '<td class="text-right td-thoi-gian-in"></td>';

        $tdLoai = '<td class="text-right td-loai">
            <input type="text" name="items[' . $counter . '][loai]" placeholder="'.lang('Loại').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control loai" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['hoan_thanh']) : 'T/D').'">
        </td>';
        $tdGhiChu2 = '<td class="text-right td-ghi-chu-2">
            <input type="text" name="items[' . $counter . '][ghi_chu_2]" placeholder="'.lang('Ghi chú 2').'" style="padding: 5; height: 30px;" class="form-control loai" value="'.(!empty($dtProductionListsItem['ghi_chu_2']) ? ($dtProductionListsItem['ghi_chu_2']) : '').'">
        </td>';

        $tdSoMauIn = '<td class="text-right td-so-mau-in">
            <input type="text" name="items[' . $counter . '][so_mau_in]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control number-format so_mau_in" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_mau_in']) : '1').'">
        </td>';

        $tdBeXaCat = '<td class="text-right td-be-xa-cat">
            <input type="text" name="items[' . $counter . '][be_xa_cat]" placeholder="'.lang('Bế/Xả/Cắt').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control be_xa_cat" value="'.(!empty($dtProductionListsItem['be_xa_cat']) ? ($dtProductionListsItem['be_xa_cat']) : '').'">
        </td>';

        $tdSoMat = '<td class="text-right td-so-mat">
            <input type="text" name="items[' . $counter . '][so_mat]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control number-format so_mat" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_mat_in']) : $countFace).'">
        </td>';
        $tdTongThoiGian = '<td class="text-right td-tong-thoi-gian"></td>';
        $tdNgayHoanThanhMang = '<td class="text-right td-ngay-hoan-thanh-mang">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_mang]" placeholder="'.lang('Ngày hoàn thành màng').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control ngay_hoan_thanh_mang datepicker" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_mang']) ? _d($dtProductionListsItem['ngay_hoan_thanh_mang']) : '').'">
        </td>';

        $tdBeXaKhoan = '<td class="text-right td-be-xa-khoan">
            <input type="text" name="items[' . $counter . '][be_xa_khoan]" placeholder="'.lang('Bể/Xả/Khoan').'" style="padding: 5; height: 30px;" class="form-control number-format dau-in" value="'.(!empty($dtProductionListsItem['be_xa_khoan']) ? ($dtProductionListsItem['be_xa_khoan']) : '').'">
        </td>';

        $tdSoMatPhunBong = '<td class="text-right td-so-mat-phun-bong">
            <input type="text" name="items[' . $counter . '][so_mat_phun_bong]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control number-format so_mat_phun_bong" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_mat_phun_bong']) : $countFace).'">
        </td>';

        $tdNgayHoanThanhBong = '<td class="text-right td-ngay-hoan-thanh-bong">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_bong]" placeholder="'.lang('Ngày hoàn thành bóng').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;" class="form-control ngay_hoan_thanh_bong datepicker" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_bong']) ? _d($dtProductionListsItem['ngay_hoan_thanh_bong']) : '').'">
        </td>';
        $tdBoi = '<td class="text-right td-boi">
            <input type="text" name="items[' . $counter . '][boi]" style="padding: 5; height: 30px;" placeholder="'.lang('Bồi').'" class="form-control boi" value="'.(!empty($dtProductionListsItem['boi']) ? ($dtProductionListsItem['boi']) : '').'">
        </td>';
        $tdBeXaKhoanLo2 = '<td class="text-right td-be-xa-khoan-lo2">
            <input type="text" name="items[' . $counter . '][be_xa_khoan_lo_2]" placeholder="'.lang('Bế/ Xả/ Khoan lỗ 2').'" style="padding: 5; height: 30px;" placeholder="'.lang('Bế/ Xả/ Khoan lỗ 2').'" class="form-control be_xa_khoan_lo_2" value="'.(!empty($dtProductionListsItem['be_xa_khoan_lo_2']) ? ($dtProductionListsItem['be_xa_khoan_lo_2']) : '').'">
        </td>';

        $tdSoConTrenKB = '<td class="text-right td-so-con-tren-kb">'.formatNumber($aRow['quantity_child_molds']).'</td>';
        $tdLoaiBoi = '<td class="text-right td-loai-boi">
            <input type="text" name="items[' . $counter . '][loai_boi]" placeholder="'.lang('Loại bồi').'" style="padding: 5; height: 30px;"  class="form-control loai_boi" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['loai_boi']) : '1').'">
        </td>';

        $tdNgayHoanThanh = '<td class="text-right td-ngay-hoan-thanh">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh]" placeholder="'.lang('Ngày hoàn thành').'" style="padding: 5; height: 30px;" class="form-control ngay_hoan_thanh datepicker" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh']) ? _d($dtProductionListsItem['ngay_hoan_thanh']) : '').'">
        </td>';

        $tdLoaiGiay = '<td class="text-right td-loai-giay">
            <input type="text" name="items[' . $counter . '][loai_giay]" onchange="totalProductionList('.$type_productionlist_id.')" placeholder="'.lang('Loại giấy').'" style="padding: 5; height: 30px;" class="form-control loai_giay" value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['loai_giay']) : 'Thường').'">
        </td>';
        $tdNgayHoanThanhCanMang = '<td class="text-right td-ngay-hoan-thanh-can-mang">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_can_mang]" placeholder="'.lang('Ngày hoàn thành cán màng').'" style="padding: 5; height: 30px;" class="form-control datepicker ngay_hoan_thanh_can_mang" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_can_mang']) ? _d($dtProductionListsItem['ngay_hoan_thanh_can_mang']) : '').'">
        </td>';
        $tdNgayHoanThanhBoi = '<td class="text-right td-ngay-hoan-thanh-boi">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_boi]" placeholder="'.lang('Ngày hoàn thành bồi').'" style="padding: 5; height: 30px;" class="form-control datepicker ngay_hoan_thanh_boi" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_boi']) ? _d($dtProductionListsItem['ngay_hoan_thanh_boi']) : '').'">
        </td>';
        $tdNgayHoanThanhLua = '<td class="text-right td-ngay-hoan-thanh-lua">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_lua]" placeholder="'.lang('Ngày hoàn thành lụa').'" style="padding: 5; height: 30px;" class="form-control datepicker ngay_hoan_thanh_boi" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_lua']) ? _d($dtProductionListsItem['ngay_hoan_thanh_lua']) : '').'">
        </td>';
        $tdNgayHoanThanhFlexo = '<td class="text-right td-ngay-hoan-thanh-flexo">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_flexo]" placeholder="'.lang('Ngày hoàn thành Flexo').'" style="padding: 5; height: 30px;" class="form-control datepicker ngay_hoan_thanh_flexo" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_flexo']) ? _d($dtProductionListsItem['ngay_hoan_thanh_flexo']) : '').'">
        </td>';
        $tdNgayHoanThanhHP = '<td class="text-right td-ngay-hoan-thanh-hp">
            <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_hp]" placeholder="'.lang('Ngày hoàn thành HP').'" style="padding: 5; height: 30px;" class="form-control datepicker ngay_hoan_thanh_hp" value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_hp']) ? _d($dtProductionListsItem['ngay_hoan_thanh_hp']) : '').'">
        </td>';

        if ($type_productionlist_id == 1) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoMatIn . '
                ' . $tdTongTua . '
                ' . $tdThoiGianIn . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdThoiGianXuLy . '
                ' . $tdNgayMoLSX . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayVeNVLDuKien . '
                ' . $tdNgayBanGiaoSanXuat . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayHoanThanhIn . '
                ' . $tdTinhTrang . '
                ' . $tdMayIn . '
                ' . $tdThoiGianConLai . '
                ' . $tdGhiChu . '
                ' . $tdSoConTrenToIn . '
                ' . $tdSoConTrenKBOffset . '
                ' . $tdTuaSauIn . '
                ' . $tdCongDoan . '
                ' . $tdBongMang . '
                ' . $tdHoanThanh . '
            </tr>';
        } else if ($type_productionlist_id == 2) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoLuongSanXuat . '
                ' . $tdSoToIn . '
                ' . $tdSoConTrenToIn . '
                ' . $tdSoConTrenKBFlexo . '
                ' . $tdSoTuaInFlexo . '
                ' . $tdThoiGianIn . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdThoiGianXuLy . '
                ' . $tdNgayMoLSX . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayVeNVLDuKien . '
                ' . $tdNgayBanGiaoSanXuat . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayKetThuc . '
                ' . $tdTinhTrang . '
                ' . $tdGhiChu . '
                ' . $tdMayIn . '
                ' . $tdCongDoan . '
                ' . $tdThoiGianConLai . '
            </tr>';
        } else if ($type_productionlist_id == 3) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoLuongSanXuat . '
                ' . $tdSoConTrenToIn . '
                ' . $tdDauIn . '
                ' . $tdNangSuat . '
                ' . $tdSoTuaIn . '
                ' . $tdThoiGianIn . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdThoiGianXuLy . '
                ' . $tdNgayMoLSX . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayVeNVLDuKien . '
                ' . $tdNgayBanGiaoSanXuat . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayKetThuc . '
                ' . $tdTinhTrang . '
                ' . $tdGhiChu . '
                ' . $tdThoiGianConLai . '
                '.$tdMayIn.'
            </tr>';
        } else if ($type_productionlist_id == 4) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoMatIn . '
                ' . $tdLoai . '
                ' . $tdNangSuat . '
                ' . $tdSoTuaIn . '
                ' . $tdThoiGianIn . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdThoiGianXuLy . '
                ' . $tdNgayMoLSX . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayVeNVLDuKien . '
                ' . $tdNgayBanGiaoSanXuat . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayKetThuc . '
                ' . $tdTinhTrang . '
                ' . $tdThoiGianConLai . '
                ' . $tdGhiChu . '
                ' . $tdGhiChu2 . '
                '.$tdMayIn.'
            </tr>';
        } else if ($type_productionlist_id == 5) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoMatIn . '
                ' . $tdSoMauIn . '
                ' . $tdSoTuaIn . '
                ' . $tdThoiGianIn . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdThoiGianXuLy . '
                ' . $tdNgayMoLSX . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayVeNVLDuKien . '
                ' . $tdNgayBanGiaoSanXuat . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayKetThuc . '
                ' . $tdTinhTrang . '
                ' . $tdThoiGianConLai . '
                ' . $tdBeXaCat . '
                ' . $tdHoanThanh . '
                '.$tdMayIn.'
            </tr>';
        } else if ($type_productionlist_id == 6) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoMat . '
                ' . $tdTongTua . '
                ' . $tdThoiGianXuLy . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdTongThoiGian . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayHoanThanhIn . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayHoanThanhMang . '
                ' . $tdTinhTrang . '
                ' . $tdThoiGianConLai . '
                ' . $tdGhiChu . '
                ' . $tdCongDoan . '
                ' . $tdBeXaKhoan . '
                ' . $tdHoanThanh . '
                '.$tdMayIn.'
            </tr>';
        } else if ($type_productionlist_id == 7) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoMatPhunBong . '
                ' . $tdTongTua . '
                ' . $tdThoiGianXuLy . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdTongThoiGian . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayHoanThanhIn . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayHoanThanhBong . '
                ' . $tdTinhTrang . '
                ' . $tdGhiChu . '
                ' . $tdThoiGianConLai . '
                ' . $tdCongDoan . '
                ' . $tdBoi . '
                ' . $tdBeXaKhoanLo2 . '
                ' . $tdHoanThanh . '
                '.$tdMayIn.'
            </tr>';
        } else if ($type_productionlist_id == 8) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoConTrenToIn . '
                ' . $tdSoConTrenKB . '
                ' . $tdTongTua . '
                ' . $tdLoaiBoi . '
                ' . $tdNangSuat . '
                ' . $tdThoiGianXuLy . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdTongThoiGian . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayHoanThanhBong . '
                ' . $tdNgayHoanThanhMang . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayHoanThanh . '
                ' . $tdTinhTrang . '
                ' . $tdGhiChu . '
                ' . $tdThoiGianConLai . '
                ' . $tdBeXaKhoan . '
                ' . $tdHoanThanh . '
                '.$tdMayIn.'
            </tr>';
        } else if ($type_productionlist_id == 9) {
            $trHtml .= '<tr>
                ' . $tdLenhSanXuat . '
                ' . $tdMaSanPham . '
                ' . $tdSoToIn . '
                ' . $tdSoConTrenToIn . '
                ' . $tdSoConTrenKB . '
                ' . $tdTongTua . '
                ' . $tdLoaiGiay . '
                ' . $tdNangSuat . '
                ' . $tdThoiGianXuLy . '
                ' . $tdThoiGianCanhBai . '
                ' . $tdTongThoiGian . '
                ' . $tdNgayGiaoHangHeThong . '
                ' . $tdNgayGiaoHang . '
                ' . $tdNgayHoanThanhIn . '
                ' . $tdNgayHoanThanhBong . '
                ' . $tdNgayHoanThanhCanMang . '
                ' . $tdNgayHoanThanhBoi . '
                ' . $tdNgayHoanThanhLua . '
                ' . $tdNgayHoanThanhFlexo . '
                ' . $tdNgayHoanThanhHP . '
                ' . $tdNgayBatDauDuKien . '
                ' . $tdNgayHoanThanh . '
                ' . $tdTinhTrang . '
                ' . $tdGhiChu . '
                ' . $tdThoiGianConLai . '
                ' . $tdCongDoan . '
                '.$tdMayIn.'
            </tr>';
        }

        $counter++;
    }
?>

<?php
// $start_date = '01/01/2023';
// $end_date = '31/01/2023';
$start_date = date('01/m/Y');
$end_date  = date('t/m/Y');

$_start_date = to_sql_date($start_date);
$minus_date = minusDate($start_date, $end_date);
$arrDate = [];
for ($i = 0; $i <= $minus_date; $i++) {
    $_date = date('d/m/Y', strtotime($_start_date . ' + ' . $i . ' days'));

    $arr_date = explode('/', $_date);
    $day_month = $arr_date[0] . '/' . $arr_date[1];

    $arrDate[] = [
        '_date' => $_date,
        'day_month' => $day_month,
    ];
}

$chunkArrDate = array_chunk($arrDate, 7);
?>


<input type="hidden" name="type_productionlist_id" id="type_productionlist_id" class="form-control" value="<?= $type_productionlist_id ?>">

<?php if ($type_productionlist_id == 1) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tờ in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Số con/Tờ in') ?></th>
                        <th class="text-center"><?= lang('Số con/KB') ?></th>
                        <th class="text-center"><?= lang('Tua sau in') ?></th>
                        <th class="text-center"><?= lang('Công đoạn in') ?></th>
                        <th class="text-center"><?= lang('Bóng màng') ?></th>
                        <th class="text-center"><?= lang('Hoàn thành') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 2) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_tho" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Số lượng SX (con)') ?></th>
                        <th class="text-center"><?= lang('Số tờ in') ?></th>
                        <th class="text-center"><?= lang('Số con/ tờ') ?></th>
                        <th class="text-center"><?= lang('Số con/KB') ?></th>
                        <th class="text-center"><?= lang('Số tua in') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 3) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="7" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_tho" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 300:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_300" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_300" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_300']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 600:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_600" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_600" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_600']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Số lượng SX (con)') ?></th>
                        <th class="text-center"><?= lang('Số con/ tờ') ?></th>
                        <th class="text-center"><?= lang('Đầu in') ?></th>
                        <th class="text-center"><?= lang('Năng suất') ?></th>
                        <th class="text-center"><?= lang('Số tua in') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 4) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="so_luong_tho" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in trắng/đen:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_dau_in_trang_den" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_dau_in_trang_den" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_dau_in_trang_den']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in màu:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_dau_in_mau" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_dau_in_mau" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_dau_in_mau']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài in trắng/đen:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="thoi_gian_canh_bai_in_trang_den" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_canh_bai_in_trang_den" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_canh_bai_in_trang_den']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài in màu:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="thoi_gian_canh_bai_in_mau" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_canh_bai_in_mau" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_canh_bai_in_mau']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Số tờ in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Năng suất') ?></th>
                        <th class="text-center"><?= lang('Số tua in') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Ghi chú 2') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 5) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" name="so_luong_tho" onchange="totalProductionList(<?= $type_productionlist_id ?>)" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất kéo tay:') ?></td>
                            <td>
                                <input type="text" name="nang_suat_keo_tay" onchange="totalProductionList(<?= $type_productionlist_id ?>)" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_keo_tay" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_keo_tay']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" name="_thoi_gian_canh_bai" onchange="totalProductionList(<?= $type_productionlist_id ?>)" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" name="thoi_gian_lam_viec_chuan" onchange="totalProductionList(<?= $type_productionlist_id ?>)" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" name="thoi_gian_lam_viec_ot" onchange="totalProductionList(<?= $type_productionlist_id ?>)" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Số tờ in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Số màu in') ?></th>
                        <th class="text-center"><?= lang('Số tua in') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Bế/Xả/Cắt') ?></th>
                        <th class="text-center"><?= lang('Hoàn thành') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 6) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tờ in') ?></th>
                        <th class="text-center"><?= lang('Số mặt') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành màng') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Bế/ Xả/ Khoan') ?></th>
                        <th class="text-center"><?= lang('Hoàn thành') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 7) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tờ in') ?></th>
                        <th class="text-center"><?= lang('Số mặt phun bóng') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành bóng') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Bồi') ?></th>
                        <th class="text-center"><?= lang('Bế/ Xả/ Khoan lỗ 2') ?></th>
                        <th class="text-center"><?= lang('Hoàn thành') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 8) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy bồi 1 mặt:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_boi_mot_mat" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_boi_mot_mat" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_boi_mot_mat']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy bồi 2 mặt:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_boi_hai_mat" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_boi_hai_mat" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_boi_hai_mat']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)"  name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tờ in') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Số con/KB') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Loại bồi') ?></th>
                        <th class="text-center"><?= lang('Năng suất') ?></th>
                        <th class="text-center"><?= lang('Thời gian Xử lý') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành bóng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành màng') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Bế/ Xả/ Khoan') ?></th>
                        <th class="text-center"><?= lang('Hoàn thành') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 9) : ?>
    <div class="div-type_productionlist_id-<?= $type_productionlist_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy (bế giấy thường):') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_may_be_giay_thuong" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_be_giay_thuong" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_be_giay_thuong']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy (demi, bế giấy bồi/ PET):') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nang_suat_may_demi_be_giay_boi_pet" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_demi_be_giay_boi_pet" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_demi_be_giay_boi_pet']) : '' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tờ in') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Số con/KB') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Loại giấy') ?></th>
                        <th class="text-center"><?= lang('Năng suất') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                        <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành bóng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành cán màng') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành bồi') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành lụa') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành flexo') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành HP') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành') ?></th>
                        <th class="text-center"><?= lang('Tình trạng') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Máy in') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    var dtItems = '';
    $(document).ready(function () {
        totalProductionList(<?= $type_productionlist_id ?>, 1);
        init_datepicker();
        init_selectpicker();

        dtItems = $('#tb-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            scrollY: '400px',
            scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
            },
        });
    });
</script>