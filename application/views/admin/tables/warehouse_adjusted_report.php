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
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_adjusted = $this->ci->input->post('type_adjusted');
$type_items = $this->ci->input->post('type_items');

$select = array(
    'tbladjusted.date_create as warehouseman_date',
    'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as full_name',
    'tbladjusted.date as date',
    'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
    'concat(tblinventory.prefix,"",tblinventory.code) as code_inventory',
    '1',
    '2',
    '3',
    'tblwarehouse.name as name_warehouse',
    'tbladjusted_items.localtion as localtion_id',
    'tbladjusted_items.quantity_net as quantity_net'
);

$where = array();
if (!empty($warehouse_id)) {
    $where = array(
        'AND tbladjusted.warehouse_id IN (' . implode(',', $warehouse_id) . ')'
    );
}
if (!empty($type_items)) {
    array_push($where, 'AND tbladjusted_items.product_id =', $custom_item_select);
    array_push($where, 'AND tbladjusted_items.type = "' . $type_items . '"');
}
if (!empty($type_adjusted)) {
    array_push($where, 'AND tbladjusted.type = "' . $type_adjusted . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where, 'AND tbladjusted.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where, 'AND tbladjusted.date <=' . '"' . $endMonth . ' 23:59:59"');
}
$aColumns     = $select;
$sIndexColumn = "id";
$sTable       = 'tbladjusted_items';
$join         = array(
    'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tbladjusted.warehouse_id',
    'LEFT JOIN tblinventory ON tblinventory.id = tbladjusted.id_inventory',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tbladjusted.staff_id',
);

$order_by = 'order by product_id asc';
$result   = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tbladjusted.id as id_main,tbladjusted_items.product_id as product_id,tbladjusted_items.type as type,tbladjusted.type as ch_type'));

$output  = $result['output'];
$rResult = $result['rResult'];
$footer_data['quantity'] = 0;
foreach ($rResult as $key => $aRow) {
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['type']);
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == '1') {
            $_data = $get_items->code;
        }
        if ($aColumns[$i] == '2') {
            $_data = $get_items->name;
        }
        if ($aColumns[$i] == '3') {
            $_data = $get_items->unit_name_stock;
        }
        if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as full_name') {
            $_data = '<div class="">' . ($aRow['full_name']) . '<div>';
        }
        if ($aColumns[$i] == 'tbladjusted.date_create as warehouseman_date') {
            $_data = '<div class="text-center">' . _dt($aRow['warehouseman_date']) . '<div>';
        }
        if ($aColumns[$i] == 'tbladjusted.date as date') {
            $_data = '<div class="text-center">' . _dhau($aRow['date']) . '<div>';
        }
        if ($aColumns[$i] == 'tbladjusted_items.localtion as localtion_id') {
            $_data = '<div class="text-center">' . get_listname_localtion_warehouse($aRow['localtion_id']) . '<div>';
        }
        if ($aColumns[$i] == 'tbladjusted_items.quantity_net as quantity_net') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_net']) . '<div>';
            $footer_data['quantity']+=$aRow['quantity_net'];
        }
        if ($aColumns[$i] == 'concat(tblinventory.prefix,"",tblinventory.code) as code_inventory') {

            $_data = $_data;
        }
        if ($aColumns[$i] == 'concat(tbladjusted.prefix,"",tbladjusted.code) as code') {
            if ($aRow['ch_type'] == 1) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a> ';
            } else {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>';
            }
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