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
$type_items = $this->ci->input->post('type_items');
if ($type_items == "product") {
    $type_items = 'products';
} else if ($type_items == "materials") {
    $type_items = 'nvl';
}

//Xuất kho
$selectexport = array(
    'tbl_deliveries.date as date',
    'tbl_deliveries.reference_no as code',
    '1',
    '2',
    '3',
    'tblwarehouse.name as name_warehouse',
    'tbl_delivery_items.location_id as localtion_id',
    'tbl_delivery_items.quantity as quantity'
);
$whereexport = array();
if (!empty($warehouse_id)) {
    $whereexport = array(
        'AND tbl_delivery_items.warehouse_id IN (' . implode(',', $warehouse_id) . ')'
    );
}
if (!empty($type_items)) {
    array_push($whereexport, 'AND tbl_delivery_items.item_id =', $custom_item_select);
    array_push($whereexport, 'AND tbl_delivery_items.type_item = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($whereexport, 'AND tbl_deliveries.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($whereexport, 'AND tbl_deliveries.date <=' . '"' . $endMonth . ' 23:59:59"');
}
$aColumnsexport     = $selectexport;
$sIndexColumnexport = "id";
$sTableexport       = 'tbl_delivery_items';
$joinexport         = array(
    'LEFT JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id',
    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tbl_delivery_items.warehouse_id',
);

$order_byexport = 'order by item_id asc';
$resultexport   = data_tables_init($aColumnsexport, $sIndexColumnexport, $sTableexport, $joinexport, $whereexport, array('
    tbl_deliveries.id as id_main,
    tbl_delivery_items.item_id as product_id,
    tbl_delivery_items.type_item as type,
    1 as exists_quantity
'));

$outputexport  = $resultexport['output'];
$rResultexport = $resultexport['rResult'];

$aColumnsG = array(
    'date',
    'code',
    '1',
    '2',
    '3',
    'name_warehouse',
    'localtion_id',
    'quantity'
);
$rResultG = array();
if (!empty($rResultexport)) {
    $rResultG = array_merge($rResultG, $rResultexport);
}
if (!empty($rResult_return)) {
    $rResultG = array_merge($rResultG, $rResult_return);
}
if (!empty($rResult_adjustedG)) {
    $rResultG = array_merge($rResultG, $rResult_adjustedG);
}
if (!empty($rResult_TranfersD)) {
    $rResultG = array_merge($rResultG, $rResult_TranfersD);
}
if (!empty($rResult_exportsx)) {
    $rResultG = array_merge($rResultG, $rResult_exportsx);
}
if (!empty($rResultG)) {
    // usort($rResultG, ch_make_cmp(['type' => "desc",'product_id' => "desc",'localtion_id' => "desc",'warehouseman_date' => "asc",'exists_quantity'=> "asc"]));
}
$output = $outputexport;
// $output['iTotalRecords']=$outputexport['iTotalRecords'] +$output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords']  + $output_exportsx['iTotalRecords'] + $output_TranfersD['iTotalRecords'];
// $output['iTotalDisplayRecords']=$outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords']  + $output_exportsx['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'];

$output['iTotalRecords'] = $outputexport['iTotalRecords'];
$output['iTotalDisplayRecords'] = $outputexport['iTotalDisplayRecords'];

$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
// $row= array();
$footer_data['total_quantity'] = 0;
foreach ($rResultG as $key => $aRow) {
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['type']);
    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == 'date') {
            $_data = '<div class="text-center">' . _d($aRow['date']) . '<div>';
        }
        if ($aColumnsG[$i] == 'quantity') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity']) . '<div>';
            $footer_data['total_quantity'] += $aRow['quantity'];
        }
        if ($aColumnsG[$i] == '1') {
            $_data = $get_items->code;
        }
        if ($aColumnsG[$i] == '2') {
            $_data = $get_items->name;
        }
        if ($aColumnsG[$i] == '3') {
            $_data = $get_items->unit_name_stock;
        }
        if ($aColumnsG[$i] == 'localtion_id') {
            $_data = '<div class="text-center">' . get_listname_localtion_warehouse($aRow['localtion_id']) . '<div>';
        }
        if ($aColumnsG[$i] == 'name_warehouse') {
            $_data = '<div class="text-center">' . ($aRow['name_warehouse']) . '<div>';
        }
        if ($aColumnsG[$i] == 'code') {
            if ($aRow['exists_quantity'] == 1) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_delivery/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 2) {
                $_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
            } elseif ($aRow['exists_quantity'] == 3) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
            } elseif ($aRow['exists_quantity'] == 4) {
                $_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
            } elseif ($aRow['exists_quantity'] == 5) {
                $_data = '<a class="tnh-modal" title="Xem" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_exporting_production/' . $aRow['id_main']) . '">' . $_data . '</a>';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}

foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;

$output['title_excel'] = ['Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') . ' - ' . (!empty($endMonth) ? _dt($endMonth) : ''), 'KHO HÀNG: TẤT CẢ'];
$warehouse_id_array = $this->ci->input->post('warehouse_id_array');
if (!empty($warehouse_id_array)) {
    $this->ci->db->where_in('id', $warehouse_id_array);
    $listWarehouseSearch = $this->ci->db->get('tblwarehouse')->result_array();
    if (!empty($listWarehouseSearch)) {
        $output['title_excel'][1] = 'KHO HÀNG: ';
        foreach ($listWarehouseSearch as $key => $value) {
            $output['title_excel'][1] .= $value['name'] . ', ';
        }
        $output['title_excel'][1] = trim($output['title_excel'][1], ', ');
    }
}
