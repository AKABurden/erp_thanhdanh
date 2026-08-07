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
$type_import = $this->ci->input->post('type_import');


$warehouse_id = $this->ci->input->post('warehouse_id_array');
// if($warehouse_id == -1){
//     $warehouse_id = 0;
// }
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
if (!empty($type_items)) {
    if ($type_items == 'product') {
        $_type_items = 'products';
    } else
        if ($type_items == 'nvl') {
        $_type_items = 'materials';
    } else
        if ($type_items == 'tools') {
        $_type_items = 'tools_supplies';
    } else {
        $_type_items = 'items';
    }
}
//Nhập kho
$selectimport = array(
    'tblimport.date as date',
    'concat(tblimport.prefix,"-",tblimport.code) as code',
    'tblsuppliers.company as company',
    '1',
    '2',
    '3',
    '5',
    'tblimport_items.lot_code as lot_code',
    '4',
    'tblwarehouse.name as name_warehouse',
    'tblimport_items.localtion_warehouses_id as localtion_id',
    'tblimport_items.quantity_stock as quantity',
    'tblimport_items.quantity_stock as quantity_net',
    'tblimport_items.price as prices',
    '(tblimport_items.price * tblimport_items.quantity_payment) as total_price'
);
$whereimport = array(
    'AND tblimport.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    array_push($whereimport, 'AND tblimport.warehouse_id IN ('.implode(',',$warehouse_id).')');
}
if (!empty($type_items)) {
    array_push($whereimport, 'AND tblimport_items.product_id =', $custom_item_select);
    array_push($whereimport, 'AND tblimport_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($whereimport, 'AND tblimport.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($whereimport, 'AND tblimport.date <=' . '"' . $endMonth . ' 23:59:59"');
}
if (!empty($type_import)) {
    if ($type_import != 1) {
        array_push($whereimport, 'AND tblimport.id = "-1"');
    }
}
$aColumnsimport     = $selectimport;
$sIndexColumnimport = "id";
$sTableimport       = 'tblimport_items';
$joinimport         = array(
    'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',
    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tblimport.warehouse_id',
    'LEFT JOIN tblsuppliers ON tblsuppliers.id = tblimport.suppliers_id',
);

$order_byimport = 'order by product_id asc';
$resultimport  = data_tables_init($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tblimport.id as id_main,tblimport_items.product_id as product_id,tblimport_items.type as type,1 as exists_quantity,tblimport_items.date_use,tblimport_items.date_sd,tblimport_items.date_sx'));

$outputimport  = $resultimport['output'];
$rResultimport = $resultimport['rResult'];




$aColumnsG = array(
    'date',
    'code',
    'company',
    '1',
    '2',
    '3',
    '5',
    'lot_code',
    '4',
    'name_warehouse',
    'localtion_id',
    'quantity',
    'quantity_net',
    'prices',
    'total_price'

);
$rResultG = array();
if (!empty($rResultimport)) {
    $rResultG = array_merge($rResultG, $rResultimport);
}
if (!empty($rResultG)) {
    usort($rResultG, ch_make_cmp(['date' => "asc"]));
}
$footer_data = array(
    'product_quantity' => 0,
    'product_total' => 0,
);
$output = $outputimport;
// $output['iTotalRecords']=$outputimport['iTotalRecords']+ $output_TranfersN['iTotalRecords']  + $output_adjustedT['iTotalRecords'] + $output_import_tp['iTotalRecords'];
// $output['iTotalDisplayRecords']=$outputimport['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords'] + $output_import_tp['iTotalDisplayRecords'];
$output['iTotalRecords'] = $outputimport['iTotalRecords'] ;
$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
foreach ($rResultG as $key => $aRow) {
    $row = [];
    if ($aRow['type'] == 'items') {
        $_type_items = 'items';
    } elseif ($aRow['type'] == 'nvl') {
        $_type_items = 'materials';
    } elseif ($aRow['type'] == 'tools') {
        $_type_items = 'tools_supplies';
    } else {
        $_type_items = 'products';
    }
    $get_items = get_items($aRow['product_id'], $aRow['type']);
    if (empty($get_items)) {
        echo '<pre>';
        print_arrays($aRow['exists_quantity'], $aRow['type']);
        die;
    }
    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == 'prices') {
            $_data = '<div class="text-right">' . formatNumber($aRow['prices']) . '</div>';
        }
        if ($aColumnsG[$i] == 'total_price') {
            $_data = '<div class="text-right">' . formatNumber($aRow['total_price']) . '</div>';
            $footer_data['product_total']+=$aRow['total_price'];
        }
        if ($aColumnsG[$i] == '1') {
            $_data =format_item_purchases($aRow['type']);
        }
        if ($aColumnsG[$i] == '2') {
            $_data = $get_items->code;
        }
        if ($aColumnsG[$i] == '3') {
            $_data = $get_items->name;
        }
        if ($aColumnsG[$i] == 'lot_code') {
            $_data = '<div class="text-center">' . ($aRow['lot_code']) . '<div>';
        }
        if ($aColumnsG[$i] == '5') {
            $_data = $get_items->unit_name_stock;
        }
        if ($aColumnsG[$i] == '4') {
            $html = '<div class="" style="width: 120px;">
            <div class="">'. _l('ch_date_of_manufacture').': <span style="color:red;">'. _d($aRow['date_sx']).'</span></div>
            <div class="">'. _l('ch_items_dateed').': <span style="color:red;">'. _d($aRow['date_sd']).'</span></div>
            <div class="">'. _l('ch_items_date_use').': <span style="color:red;">'. $aRow['date_use'].'</span></div></div>';
            $_data = $html;
        }
        
        if ($aColumnsG[$i] == 'warehouseman_date') {
            $_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
        }
        if ($aColumnsG[$i] == 'date') {
            $_data = '<div class="text-center">' . _d($aRow['date']) . '<div>';
        }
        if ($aColumnsG[$i] == 'localtion_id') {
            $_data = '<div class="text-center">' . get_listname_localtion_warehouse($aRow['localtion_id']) . '<div>';
        }
        if ($aColumnsG[$i] == 'quantity') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity']) . '<div>';
        }
        if ($aColumnsG[$i] == 'quantity_net') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_net']) . '<div>';
            $footer_data['product_quantity']+=$aRow['quantity_net'];
        }
        if ($aColumnsG[$i] == 'code') {
            if ($aRow['exists_quantity'] == 1) {
                $_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 5) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 6) {
                $_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a> ';
            } elseif ($aRow['exists_quantity'] == 9) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('stock/view_purchase_product/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 16) {
                $_data = '<a class="tnh-modal" title="' . _l('purchase_internal') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_purchase_internal/' . $aRow['id_main']) . '">' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 687) {
                $_data = '<a class="tnh-modal" title="' . _l('Nhập gia công') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('outsource/view_import_outsource/' . $aRow['id_main']) . '">' . $_data . '</a>  ';
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