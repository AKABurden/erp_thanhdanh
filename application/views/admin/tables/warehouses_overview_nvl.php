<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblwarehouse.name as warehouse_name',
    'tbl_materials.code as item_code',
    'tbl_materials.name as item_name',
    'tblunits.unit as unit_name',
    'tblwarehouse_product.lot_code',
    '3 as specification',
    'tbllocaltion_warehouses.name_parent',
    'SUM(tblwarehouse_product.quantity_left) as quantity',
    'tblwarehouse_product.date_sx',
    'tblwarehouse_product.date_sd',
    'tblwarehouse_product.date_use',
    'SUM(product_quantity_payment_left*price) as total',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'warehouse_product';

$join         = array(
    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_product.warehouse_id',
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion',
    'LEFT JOIN tbl_materials ON tbl_materials.id = tblwarehouse_product.product_id AND tblwarehouse_product.type_items = "nvl"',
    'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
    'LEFT JOIN tbltype_items ON tbltype_items.type = tblwarehouse_product.type_items',
);
$where = array();

// Raw materials only
array_push($where, 'AND tblwarehouse_product.type_items = "nvl"');

// Multi-warehouse filter
$warehouse_id = $this->ci->input->post('warehouse_id');
if (!empty($warehouse_id)) {
    if (is_array($warehouse_id)) {
        $warehouse_id_clean = array_filter(array_map('intval', $warehouse_id));
        if (!empty($warehouse_id_clean)) {
            array_push($where, 'AND tblwarehouse_product.warehouse_id IN (' . implode(',', $warehouse_id_clean) . ')');
        }
    } else if (is_numeric($warehouse_id)) {
        array_push($where, 'AND tblwarehouse_product.warehouse_id = ' . intval($warehouse_id));
    }
}

// Category filter
if ($this->ci->input->post('category_id')) {
    array_push($where, 'AND tbl_materials.category_id = "' . $this->ci->db->escape_str($this->ci->input->post('category_id')) . '"');
}

// Item selection filter
if ($this->ci->input->post('custom_item_select')) {
    array_push($where, 'AND tblwarehouse_product.product_id = ' . intval($this->ci->input->post('custom_item_select')));
}

// Lot code filter
if ($this->ci->input->post('lot_code')) {
    array_push($where, 'AND tblwarehouse_product.lot_code = "' . $this->ci->db->escape_str($this->ci->input->post('lot_code')) . '"');
}

// Location filter
if ($this->ci->input->post('localtion')) {
    $localtion = [];
    get_full_childs_id($this->ci->input->post('localtion'), $localtion);
    if (!empty($localtion)) {
        array_push($where, 'AND tblwarehouse_product.localtion IN(' . implode(',', array_map('intval', $localtion)) . ')');
    }
}

// Search box
$search = $this->ci->input->post('search')['value'];
if (!empty($search)) {
    $search_escaped = $this->ci->db->escape_like_str($search);
    array_push($where, 'AND ((tbl_materials.name LIKE "%' . $search_escaped . '%") OR (tbl_materials.code LIKE "%' . $search_escaped . '%"))');
}

$group_by = "GROUP BY tblwarehouse_product.warehouse_id, tblwarehouse_product.product_id, tblwarehouse_product.localtion, tblwarehouse_product.lot_code, date_sx, date_sd, date_use";
$having   = "HAVING SUM(tblwarehouse_product.quantity_left) > 0";

$result   = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_product.product_id', 'tblwarehouse_product.warehouse_id', 'tbltype_items.name as name_type', 'tbllocaltion_warehouses.id as id_localtion', 'tblwarehouse_product.id'], $group_by, [], [], $having);
$output   = $result['output'];
$rResult  = $result['rResult'];

usort($rResult, ch_make_cmp(['warehouse_id' => "asc", 'product_id' => "asc", 'id_localtion' => "asc"]));

$footer_data = array(
    'slt' => 0,
    'gtt' => 0,
);

foreach ($rResult as $r => $aRow) {
    if ($aRow['quantity'] == 0) {
        continue;
    }
    $row = [];
    $get_items = get_items($aRow['product_id'], 'nvl');

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == 'tblwarehouse.name as warehouse_name') {
            $_data = '<div class="text-left"><strong>' . (isset($aRow['warehouse_name']) ? $aRow['warehouse_name'] : '') . '</strong></div>';
        }
        if ($aColumns[$i] == 'tbl_materials.code as item_code') {
            $_data = '<div style="font-size:15px">' . (isset($get_items->code) ? $get_items->code : $aRow['item_code']) . '</div>';
        }
        if ($aColumns[$i] == 'tbl_materials.name as item_name') {
            $_data = '<div style="font-size:15px">' . (isset($get_items->name) ? $get_items->name : $aRow['item_name']) . '</div>';
        }
        if ($aColumns[$i] == 'tblunits.unit as unit_name') {
            $_data = isset($get_items->unit_name_stock) ? $get_items->unit_name_stock : $aRow['unit_name'];
        }
        if ($aColumns[$i] == 'tblwarehouse_product.lot_code') {
            $_data = '<div class="text-center">' . $aRow['tblwarehouse_product.lot_code'] . '</div>';
        }
        if ($aColumns[$i] == '3 as specification') {
            $warehouse_detail = get_table_where('tblwarehouse_product', array(
                'warehouse_id'  => $aRow['warehouse_id'],
                'product_id'    => $aRow['product_id'],
                'type_items'    => 'nvl',
                'localtion'     => $aRow['id_localtion'],
                'type_export'   => 18,
                'quantity_left >' => 0
            ));
            $html = '';
            if (!empty($warehouse_detail)) {
                foreach ($warehouse_detail as $key => $value) {
                    $purchase_products = get_table_where('tbl_purchase_products', array('id' => $value['import_id']), '', 'row');
                    if (!empty($purchase_products->productions_orders_details_id)) {
                        $order_production_details = get_table_where('tbl_productions_orders_details', array('id' => $purchase_products->productions_orders_details_id), '', 'row');
                        if (!empty($order_production_details)) {
                            $html .= $order_production_details->reference_no . ' :SL( ' . $value['quantity_left'] . ' )<br>';
                        }
                    }
                }
            }
            $_data = $html;
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_sx') {
            $_data = '<div class="text-center">' . _d($aRow['tblwarehouse_product.date_sx']) . '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_sd') {
            $_data = '<div class="text-center">' . _d($aRow['tblwarehouse_product.date_sd']) . '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_use') {
            $_data = '<div class="text-center" style="color:red;">' . ($aRow['tblwarehouse_product.date_use']) . '</div>';
        }
        if ($aColumns[$i] == 'SUM(product_quantity_payment_left*price) as total') {
            $_data = '<div class="text-right">' . formatNumber($aRow['total']) . '</div>';
            $footer_data['gtt'] += $aRow['total'];
        }
        if ($aColumns[$i] == 'SUM(tblwarehouse_product.quantity_left) as quantity') {
            $_data = '<div class="text-center" style="font-size:16px;color:#3c763d;font-weight: bold">' . formatNumber($aRow['quantity']) . '</div>';
            $footer_data['slt'] += $aRow['quantity'];
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;
