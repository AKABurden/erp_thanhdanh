<?php

defined('BASEPATH') or exit('No direct script access allowed');
$beginMonth =  '';
$endMonth   =  '';
$months_report = $this->ci->input->post('report_months');

if ($months_report != '') {
    $custom_date_select = '';
    if (is_numeric($months_report)) {
        // Last month
        if ($months_report == '1') {
            $beginMonth = date('Y-m-01', strtotime('first day of last month'));
            $endMonth   = date('Y-m-t', strtotime('last day of last month'));
        } else {
            $months_report = (int) $months_report;
            $months_report--;
            $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
            $endMonth   = date('Y-m-t');
        }
    } elseif ($months_report == 'this_month') {
        $beginMonth = date('Y-m-01');
        $endMonth   = date('Y-m-t');
    } elseif ($months_report == 'this_year') {
        $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
        $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
    } elseif ($months_report == 'last_year') {
        $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
        $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
    } elseif ($months_report == 'custom') {
        $from_date = to_sql_date($this->ci->input->post('report_from'));
        $to_date   = to_sql_date($this->ci->input->post('report_to'));
        if ($from_date == $to_date) {
            $beginMonth =  $to_date;
            $endMonth   =  $to_date;
        } else {
            $beginMonth =  $from_date;
            $endMonth   =  $to_date;
        }
    }
    // $this->db->where($custom_date_select);
}


$warehouse_id = $this->ci->input->post('warehouse_id_array');
$custom_item_select = $this->ci->input->post('custom_item_select_inventory');
$type_items = $this->ci->input->post('type_items_new');
$type_itemss = $this->ci->input->post('type_itemss');
$category_search = $this->ci->input->post('category_search_new');

$aColumns = [
    '11',
    'tblwarehouse_product.id',
    'tblwarehouse_product.type_items',
    '1',
    '2',
    '4',
    '5',
    '6',
    '7',
    '8',
    '9',
    '10',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'warehouse_product';

$join         = array(
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion',
    'LEFT JOIN tbltype_items ON tbltype_items.type = tblwarehouse_product.type_items',

    'LEFT JOIN tbl_materials ON tbl_materials.id = tblwarehouse_product.product_id and tblwarehouse_product.type_items ="nvl"',
    'LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id',
    'LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_product.product_id and tblwarehouse_product.type_items ="product"',
    'LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',

);
if ($warehouse_id == -1) {
    $warehouse_id = 0;
}
$where = array();
if (!empty($warehouse_id)) {
    $where = array(
        'AND tblwarehouse_product.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
    );
}
if (!empty($type_items) && !empty($custom_item_select)) {
    array_push($where, 'AND tblwarehouse_product.product_id =', $custom_item_select);
    array_push($where, 'AND tblwarehouse_product.type_items = "' . $type_items . '"');
}
if (!empty($type_itemss)) {
    if ($type_itemss != -1) {
        array_push($where, 'AND tblwarehouse_product.type_items = "' . $type_itemss . '"');
    }
}

if (!empty($category_search)) {
    $category_search_nvl = array();
    $category_search_tools = array();
    foreach ($category_search as $key => $value) {
        $check_value = explode('_', $value);
        if ($check_value[0] == 'nvl') {
            $category_search_nvl[] = $check_value[1];
        }
        if ($check_value[0] == 'product') {
            $category_search_tools[] = $check_value[1];
        }
    }

    if (!empty($category_search_nvl) && !empty($category_search_tools)) {
        array_push($where, 'AND (tbl_materials.category_id IN (' . implode(',', $category_search_nvl) . ') or tbl_products.category_id IN (' . implode(',', $category_search_tools) . '))');
    } else
    if (!empty($category_search_nvl) && empty($category_search_tools)) {
        array_push($where, 'AND (tbl_materials.category_id IN (' . implode(',', $category_search_nvl) . '))');
    } elseif (empty($category_search_nvl) && !empty($category_search_tools)) {
        array_push($where, 'AND (tbl_products.category_id IN (' . implode(',', $category_search_tools) . '))');
    }
}
$group_by = "GROUP BY tblwarehouse_product.product_id,tblwarehouse_product.type_items";
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_product.product_id', 'tbltype_items.name as name_type', 'tbl_category_items.name as name_cate', 'tbl_category_products.name as name_cate_product'], $group_by);
$output  = $result['output'];
$rResult = $result['rResult'];

usort($rResult, ch_make_cmp(['tblwarehouse_product.type_items' => "desc", 'product_id' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
$footer_data = array(
    'sldk' => 0,
    'gtdk' => 0,
    'slnk' => 0,
    'gtnk' => 0,
    'slxk' => 0,
    'gtxk' => 0,
    'slck' => 0,
    'gtck' => 0,
);
foreach ($rResult as $r => $aRow) {
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['tblwarehouse_product.type_items']);
    $sumFExistsQall = getStartInventoryArray($aRow['product_id'], $aRow['tblwarehouse_product.type_items'], $warehouse_id, $beginMonth);
    $sumFExistsQall_price = getStartInventory_v2Array($aRow['product_id'], $aRow['tblwarehouse_product.type_items'], $warehouse_id, $beginMonth);
    $price_trongki = getStartInventory_trongkiArray($aRow['product_id'], $aRow['tblwarehouse_product.type_items'], $warehouse_id, $beginMonth, $endMonth);
    $sumFExistsQall_import = getStartInventory_importArray($aRow['product_id'], $aRow['tblwarehouse_product.type_items'], $warehouse_id, $beginMonth, $endMonth);
    $sumFExistsQall_export = getStartInventory_exportArray($aRow['product_id'], $aRow['tblwarehouse_product.type_items'], $warehouse_id, $beginMonth, $endMonth);
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == '11') {
            if ($aRow['tblwarehouse_product.type_items'] == 'nvl') {
                $_data = '<div class="text-center">' . $aRow['name_cate'] . '<div>';
            } else {
                $_data = '<div class="text-center">' . $aRow['name_cate_product'] . '<div>';
            }
        }
        if ($aColumns[$i] == 'tblwarehouse_product.id') {
            $_data = $get_items->code;
        }
        if ($aColumns[$i] == '1') {
            $_data = '<div class="text-center">' . $get_items->unit_name_stock . '<div>';
        }
        if ($aColumns[$i] == '2') {
            $_data = '<div class="text-center">' . formatNumber($sumFExistsQall) . '<div>';
            $footer_data['sldk'] += $sumFExistsQall;
        }
        if ($aColumns[$i] == '4') {
            // $_data = '<div class="text-right">' . formatNumber($sumFExistsQall * $get_items->price) . '<div>';
            // $footer_data['gtdk'] += $sumFExistsQall * $get_items->price;
            if ($sumFExistsQall_price > -0.01 && $sumFExistsQall_price < 0.01) {
                $sumFExistsQall_price = 0;
            }
            if ($sumFExistsQall_price < 0) {
                $sumFExistsQall_price = 0;
            }
            $_data = '<div class="text-right">' . formatNumber($sumFExistsQall_price) . '<div>';
            $footer_data['gtdk'] += $sumFExistsQall_price;
        }
        if ($aColumns[$i] == '5') {
            $_data = '<div class="text-center"><a onclick="viewinventorywarehouse(1,' . $aRow['product_id'] . ',\'' . $aRow["tblwarehouse_product.type_items"] . '\'); return false">' . formatNumber($sumFExistsQall_import) . '</a><div>';
            $footer_data['slnk'] += $sumFExistsQall_import;
        }
        if ($aColumns[$i] == '6') {
            if ($price_trongki['exists_quantity_import'] > -0.01 && $price_trongki['exists_quantity_import'] < 0.01) {
                $price_trongki['exists_quantity_import'] = 0;
            }
            if ($price_trongki['exists_quantity_import'] < 0) {
                $price_trongki['exists_quantity_import'] = 0;
            }
            $_data = '<div class="text-right">' . formatNumber($price_trongki['exists_quantity_import']) . '<div>';
            $footer_data['gtnk'] += $price_trongki['exists_quantity_import'];
            // $_data = '<div class="text-right">' . formatNumber($sumFExistsQall_import * $get_items->price) . '<div>';
            // $footer_data['gtnk'] += $sumFExistsQall_import * $get_items->price;
        }
        if ($aColumns[$i] == '7') {
            $_data = '<div class="text-center"><a onclick="viewinventorywarehouse(2,' . $aRow['product_id'] . ',\'' . $aRow["tblwarehouse_product.type_items"] . '\'); return false">' . formatNumber($sumFExistsQall_export) . '</a><div>';
            $footer_data['slxk'] += $sumFExistsQall_export;
        }
        if ($aColumns[$i] == '8') {
            // $_data = '<div class="text-right">' . formatNumber($sumFExistsQall_export * $get_items->price) . '<div>';
            // $footer_data['gtxk'] += $sumFExistsQall_export * $get_items->price;
            if ($price_trongki['exists_quantity_export'] > -0.01 && $price_trongki['exists_quantity_export'] < 0.01) {
                $price_trongki['exists_quantity_export'] = 0;
            }
            if ($price_trongki['exists_quantity_export'] < 0) {
                $price_trongki['exists_quantity_export'] = 0;
            }
            $_data = '<div class="text-right">' . formatNumber($price_trongki['exists_quantity_export']) . '<div>';
            $footer_data['gtxk'] += $price_trongki['exists_quantity_export'];
        }
        if ($aColumns[$i] == '9') {
            // $sumFExistsQall-=$sumFExistsQall_export;
            $_data = '<div class="text-center">' . formatNumber($sumFExistsQall + $sumFExistsQall_import - $sumFExistsQall_export) . '<div>';
            $footer_data['slck'] += ($sumFExistsQall + $sumFExistsQall_import - $sumFExistsQall_export);
        }
        if ($aColumns[$i] == '10') {
            $sumFExistsQall_prices = $sumFExistsQall_price + $price_trongki['exists_quantity_import'] - $price_trongki['exists_quantity_export'];
            if ($sumFExistsQall_prices > -0.01 && $sumFExistsQall_prices < 0.01) {
                $sumFExistsQall_prices = 0;
            }
           
            // $_data = '<div class="text-right">' . formatNumber(($sumFExistsQall + $sumFExistsQall_import - $sumFExistsQall_export) * $get_items->price) . '<div>';
            // $footer_data['gtck'] += ($sumFExistsQall + $sumFExistsQall_import - $sumFExistsQall_export) * $get_items->price;
            if ($sumFExistsQall_prices > -0.01 && $sumFExistsQall_prices < 0.01) {
                $sumFExistsQall_prices = 0;
            }
            if ($sumFExistsQall_prices < 0) {
                $sumFExistsQall_prices = 0;
            }
            $_data = '<div class="text-right">' . formatNumber($sumFExistsQall_prices) . '<div>';
            $footer_data['gtck'] += $sumFExistsQall_prices;
        }
        if ($aColumns[$i] == 'tblwarehouse_product.type_items') {
            $_data = $get_items->name;
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;
$output['title_excel'] = ['Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') . ' - ' . (!empty($endMonth) ? _dt($endMonth) : ''), 'KHO HÀNG: TẤT CẢ'];
$warehouse_id_array = $this->ci->input->post('warehouse_id_array');
if(!empty($warehouse_id_array)) {
    $this->ci->db->where_in('id', $warehouse_id_array);
    $listWarehouseSearch = $this->ci->db->get('tblwarehouse')->result_array();
    if(!empty($listWarehouseSearch)) {
        $output['title_excel'][1] = 'KHO HÀNG: ';
        foreach($listWarehouseSearch as $key => $value) {
            $output['title_excel'][1] .= $value['name'] .', ';
        }
        $output['title_excel'][1] = trim($output['title_excel'][1], ', ');
    }
}

