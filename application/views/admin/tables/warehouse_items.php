<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    '1',
    'tblwarehouse_product.type_items',
    '4',
    'tblwarehouse_product.lot_code',
    '1',
    'tbllocaltion_warehouses.name_parent',
    'SUM(tblwarehouse_product.quantity_left) as quantity',
    'tblwarehouse_product.date_sx',
    'tblwarehouse_product.date_sd',
    'tblwarehouse_product.date_use',
    'SUM(product_quantity_payment_left*price) as total',
    // '(select SUM(tblwarehouse_items.product_quantity) from tblwarehouse_items where warehouse_id = tblwarehouse_product.warehouse_id AND id_items = tblwarehouse_product.product_id AND type_items = tblwarehouse_product.type_items) as product_quantity',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'warehouse_product';

$join         = array(
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion',
    'LEFT JOIN tbltype_items ON tbltype_items.type = tblwarehouse_product.type_items',
);
$where = array();
if ($this->ci->input->post('filterStatus')) {
    array_push($where, 'AND tblwarehouse_product.type_items = "' . $this->ci->input->post('filterStatus') . '"');
}
if ($this->ci->input->post('category_id')) {
    if ($this->ci->input->post('filterStatus') == 'items') {
        $join[] =  'LEFT JOIN tblitems ON tblitems.id = tblwarehouse_product.product_id and type_items = "items"';
        array_push($where, 'AND tblitems.category_id = "' . $this->ci->input->post('category_id') . '"');
    } elseif ($this->ci->input->post('filterStatus') == 'nvl') {
        $join[] =  'LEFT JOIN tbl_materials ON tbl_materials.id = tblwarehouse_product.product_id and type_items = "nvl"';
        array_push($where, 'AND tbl_materials.category_id = "' . $this->ci->input->post('category_id') . '"');
    } elseif ($this->ci->input->post('filterStatus') == 'product') {
        $join[] =  'LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_product.product_id and type_items = "product"';
        array_push($where, 'AND tbl_products.category_id = "' . $this->ci->input->post('category_id') . '"');
    } elseif ($this->ci->input->post('filterStatus') == 'tools') {
        $join[] =  'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tblwarehouse_product.product_id and type_items = "tools"';
        array_push($where, 'AND tbl_tools_supplies.category_id = "' . $this->ci->input->post('category_id') . '"');
    }
}
if (is_numeric($id)) {
    array_push($where, 'AND tblwarehouse_product.warehouse_id = ' . $id);
}
if ($this->ci->input->post('custom_item_select')) {
    array_push($where, 'AND tblwarehouse_product.product_id = ' . $this->ci->input->post('custom_item_select'));
}
if ($this->ci->input->post('lot_code')) {
    array_push($where, 'AND tblwarehouse_product.lot_code = ' . $this->ci->input->post('lot_code'));
}
if ($this->ci->input->post('localtion')) {
    $localtion = [];
    get_full_childs_id($this->ci->input->post('localtion'), $localtion);
    array_push($where, 'AND tblwarehouse_product.localtion IN(' . implode(',', $localtion) . ')');
}
$search = $this->ci->input->post('search')['value'];
if (!empty($search)) {
    array_push($where, 'AND (tblwarehouse_product.product_id IN(select tblitems.id from tblitems where (tblitems.name like "%' . $search . '%") OR tblitems.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "items" AND tblwarehouse_product.warehouse_id = ' . $id . ') OR (tblwarehouse_product.product_id IN(select tbl_tools_supplies.id from tbl_tools_supplies where (tbl_tools_supplies.name like "%' . $search . '%") OR tbl_tools_supplies.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "tools"  AND tblwarehouse_product.warehouse_id = ' . $id . ') OR (tblwarehouse_product.product_id IN(select tbl_products.id from tbl_products where (tbl_products.name like "%' . $search . '%") OR tbl_products.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "product"  AND tblwarehouse_product.warehouse_id = ' . $id . ') OR (tblwarehouse_product.product_id IN(select tbl_materials.id from tbl_materials where (tbl_materials.name like "%' . $search . '%") OR tbl_materials.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "nvl"  AND tblwarehouse_product.warehouse_id = ' . $id . ') ');
}

$group_by = "GROUP BY tblwarehouse_product.product_id,tblwarehouse_product.localtion,tblwarehouse_product.lot_code,date_sx,date_sd,date_use";
$having = "HAVING SUM(tblwarehouse_product.quantity_left) > 0";
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_product.product_id', 'tbltype_items.name as name_type', 'tbllocaltion_warehouses.id as id_localtion,tblwarehouse_product.id'], $group_by, [], [], $having);
$output  = $result['output'];
$rResult = $result['rResult'];

usort($rResult, ch_make_cmp(['tblwarehouse_product.type_items' => "desc", 'product_id' => "desc", 'id_localtion' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
$footer_data = array(
    'slt' => 0,
    'gtt' => 0,
);
foreach ($rResult as $r => $aRow) {
    if ($aRow['quantity'] == 0) {
        continue;
    }
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['tblwarehouse_product.type_items']);

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblwarehouse_product.lot_code') {
            $_data = '<div class="text-center">' . $aRow['tblwarehouse_product.lot_code'] . '</div>';
        }
    
        if ($aColumns[$i] == '1') {
            $_data = '<div style="font-size:16px">' . $get_items->code . '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.type_items') {
            $_data = '<div style="font-size:16px">' . $get_items->name . '</div>';
        }
        if ($aColumns[$i] == '4') {
            $_data = (isset($get_items->unit_name_stock) ? $get_items->unit_name_stock : '');
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_sx') {
            $_data = '<div class="text-center">' . _d($aRow['tblwarehouse_product.date_sx']). '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_sd') {
            $_data = '<div class="text-center">' . _d($aRow['tblwarehouse_product.date_sd']). '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_use') {
            $_data = '<div class="text-center" style="color:red;">' . ($aRow['tblwarehouse_product.date_use']). '</div>';
        }
        if ($aColumns[$i] == '3') {
            $warehouse_detail = get_table_where('tblwarehouse_product', array('warehouse_id' => $id, 'product_id' => $aRow['product_id'], 'type_items' => $aRow['tblwarehouse_product.type_items'], 'localtion' => $aRow['id_localtion'], 'type_export' => 18, 'quantity_left >' => 0));
            $html = '';
            foreach ($warehouse_detail as $key => $value) {
                $purchase_products = get_table_where('tbl_purchase_products', array('id' => $value['import_id']), '', 'row');
                if (!empty($purchase_products->productions_orders_details_id)) {
                    $order_production_details = get_table_where('tbl_productions_orders_details', array('id' => $purchase_products->productions_orders_details_id), '', 'row');
                    if (!empty($order_production_details)) {
                        $html .= $order_production_details->reference_no . ' :SL( ' . $value['quantity_left'] . ' )<br>';
                    }
                }
            }
            $_data = $html;
        }
        if ($aColumns[$i] == 'SUM(product_quantity_payment_left*price) as total') {
            $_data = '<div class="text-right">' . formatNumber($aRow['total']) . '</div>';
            $footer_data['gtt'] += $aRow['total'];
        }
        
        if ($aColumns[$i] == 'SUM(tblwarehouse_product.quantity_left) as quantity') {
            $_data = '<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' . formatNumber($aRow['quantity']) . '</div>';
            $footer_data['slt'] += $aRow['quantity'];
        }
        if ($aColumns[$i] == '(select SUM(tblwarehouse_items.product_quantity) from tblwarehouse_items where warehouse_id = tblwarehouse_product.warehouse_id AND id_items = tblwarehouse_product.product_id AND type_items = tblwarehouse_product.type_items) as product_quantity') {
            $_data = '<div class="text-center" style="font-size:20px;color:red;font-weight: bold">' . formatNumber($aRow['product_quantity']) . '</div>';;
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;
