<?php
defined('BASEPATH') or exit('No direct script access allowed');
$id = explode('__', $id);
$tbProductionsOrders = "(
    SELECT
        tbl_productions_orders_items.plan_id as plan_id,
        GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
    FROM tbl_productions_orders_items
    INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
    GROUP BY tbl_productions_orders_items.plan_id
) tb_productions_orders";
$wherePQ = '';
$wherePurchase = '';
$tbProductionsPlanCompensation = "(
    SELECT
        tbl_productions_plan_compensation.item_id, 
        tbl_productions_plan_compensation.item_type,
        tbl_productions_plan_compensation.productions_plan_id,
        tbl_productions_plan_compensation.quantity_primary as quantity_primary,
        tbl_productions_plan_compensation.quantity_compensation as quantity_compensation
    FROM tbl_productions_plan_compensation
    $wherePQ
    GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type, tbl_productions_plan_compensation.productions_plan_id
) tb_productions_plan_compensation";

$tbWarehouseProduct = "(
    SELECT
        tblwarehouse_items.id_items as id_items,
        SUM(tblwarehouse_items.product_quantity) as product_quantity,
        SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
    FROM tblwarehouse_items
    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . "
    GROUP BY tblwarehouse_items.id_items
) tb_quantity_warehouse";

$tbWarehouseMaterials = "(
    SELECT
        tblwarehouse_items.id_items as id_items,
        SUM(tblwarehouse_items.product_quantity) as product_quantity,
        SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
    FROM tblwarehouse_items
    WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . "
    GROUP BY tblwarehouse_items.id_items
) tb_quantity_warehouse";
$tbPurchase = "(
    SELECT
        IF(tblpurchases_items.type = 'nvl', 'materials', 'products') as type_item, 
        tblpurchases_items.type as type,
        tblpurchases_items.product_id as product_id,
        SUM(tblpurchases_items.quantity_net) as quantity_net
    FROM tblpurchases
    INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
    WHERE tblpurchases.is_plans = 1 AND tblpurchases_items.type IN ('product', 'nvl') $wherePurchase
    GROUP BY tblpurchases_items.type, tblpurchases_items.product_id
) tb_purchase";
// SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity
$tbTransfer = "(
    SELECT
        tbltransfer_warehouse.productions_capacity_id,
        tbltransfer_warehouse_detail.type as type, 
        tbltransfer_warehouse_detail.id_items as id_items,
        SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
        SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity_unit
    FROM tbltransfer_warehouse
    INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
    WHERE tbltransfer_warehouse.productions_capacity_id > 0
    GROUP BY tbltransfer_warehouse_detail.type, tbltransfer_warehouse_detail.id_items, tbltransfer_warehouse.productions_capacity_id
) tb_transfer";
if ($id[0] == 'materials') {
    $aColumns     = array(
        'tbl_productions_plan.reference_no as code_productions_plan',
        'tb_productions_orders.reference_no as productions_orders',
        '((SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0)) * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit) as quantity_primary',
        'tb_transfer.quantity as quantity_transfer'
    );
    $sIndexColumn = "id";
    $sTable       = 'tbl_productions_plan_bom';
    // $id = explode('__', $id);
    $where        = array(
        'AND tbl_productions_plan_bom.item_type = "' . $id[0] . '" and tbl_productions_plan_bom.item_id = "' . $id[1] . '"',
        // " AND (coalesce(tbl_productions_plan_bom.quantity_primary, 0) - coalesce(tb_quantity_warehouse.product_quantity, 0) - coalesce(tb_purchase.quantity_net * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit, 0) - coalesce(tb_transfer.quantity, 0)) > 0"
    );

    $join         = array(
        'LEFT JOIN tbl_productions_plan  ON tbl_productions_plan.id=tbl_productions_plan_bom.productions_plan_id',
        'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_productions_plan_bom.item_id and tbl_productions_plan_bom.item_type = "materials"',
        'LEFT JOIN ' . $tbProductionsOrders . '  ON tb_productions_orders.plan_id = tbl_productions_plan.id',
        'LEFT JOIN ' . $tbProductionsPlanCompensation . ' ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type and tb_productions_plan_compensation.productions_plan_id =tbl_productions_plan_bom.productions_plan_id',
        'LEFT JOIN ' . $tbWarehouseMaterials . ' ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id',
        'LEFT JOIN ' . $tbTransfer . ' ON tb_transfer.id_items = tbl_materials.id AND tb_transfer.type = "nvl" and tb_transfer.productions_capacity_id = tbl_productions_plan.id',
        'LEFT JOIN ' . $tbPurchase . ' ON tb_purchase.type_item = tbl_productions_plan_bom.item_type AND tb_purchase.product_id = tbl_productions_plan_bom.item_id'
    );
} else {
    $aColumns     = array(
        'tbl_productions_plan.reference_no as code_productions_plan',
        'tb_productions_orders.reference_no as productions_orders',
        'SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0) as quantity_primary',
        'tb_transfer.quantity as quantity_transfer'
    );
    $sIndexColumn = "id";
    $sTable       = 'tbl_productions_plan_bom';
    // $id = explode('__', $id);
    $where        = array(
        'AND tbl_productions_plan_bom.item_type = "' . $id[0] . '" and tbl_productions_plan_bom.item_id = "' . $id[1] . '"'
    );
    $join         = array(
        'LEFT JOIN tbl_productions_plan  ON tbl_productions_plan.id=tbl_productions_plan_bom.productions_plan_id',
        'LEFT JOIN tbl_products ON tbl_products.id = tbl_productions_plan_bom.item_id and tbl_productions_plan_bom.item_type = "semi_products_outside"',
        'LEFT JOIN ' . $tbProductionsOrders . '  ON tb_productions_orders.plan_id = tbl_productions_plan.id',
        'LEFT JOIN ' . $tbProductionsPlanCompensation . ' ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
        LEFT JOIN ' . $tbWarehouseProduct . ' ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
        LEFT JOIN ' . $tbTransfer . ' ON tb_transfer.id_items = tbl_products.id AND tb_transfer.type = "product"',
    );
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    // 'tblroles.name',
    // 'tblroles.roleid'
    'tb_quantity_warehouse.product_quantity as quantity_inventory,
    tb_transfer.quantity as quantity_transfer'
), 'GROUP BY tbl_productions_plan_bom.productions_plan_id,tbl_productions_plan_bom.item_id,tbl_productions_plan_bom.item_type HAVING quantity_primary > 0');
$output       = $result['output'];
$rResult      = $result['rResult'];
//var_dump($rResult);die();

$footer_data = array(
    'all' => 0,
    'transfer' => 0,
);
$j = 0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;

    $aRow['quantity_primary'] = ceil($aRow['quantity_primary']);
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == '((SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0)) * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit) as quantity_primary') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_primary'], 0) . '</div>';
            $footer_data['all'] += $aRow['quantity_primary'];
        }
        if ($aColumns[$i] == 'tb_transfer.quantity as quantity_transfer') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_transfer']) . '</div>';
            $footer_data['transfer'] += $aRow['quantity_transfer'];
        }
        if ($aColumns[$i] == 'coalesce(tb_purchase.quantity_net * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit, 0) as quantity_purchase') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_purchase']) . '</div>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
$footer_data['all'] = formatNumber($footer_data['all'],0);
$footer_data['transfer'] = formatNumber($footer_data['transfer']);
$output['sums'] = $footer_data;
