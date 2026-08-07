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
}


$warehouse_id = $this->ci->input->post('warehouse_id');
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
$select = array(
    'tblpurchases.date',
    'concat(tblpurchases.prefix,"",tblpurchases.code) as po_code',
    '1',
    '2',
    '3',
    'tblpurchases_items.quantity_net as quantity_net',
    'tblpurchases_items.id as id_purchases_items',
    '6',
    '7',
    'tblpurchases.explanation',
);
$where = array();
if (!empty($type_items)) {
    array_push($where, 'AND tblpurchases_items.product_id =', $custom_item_select);
    array_push($where, 'AND tblpurchases_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where, 'AND tblpurchases.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where, 'AND tblpurchases.date <=' . '"' . $endMonth . ' 23:59:59"');
}
$aColumns     = $select;
$sIndexColumn = "id";
$sTable       = 'tblpurchases_items';
$join         = array(
    'LEFT JOIN tblpurchases ON tblpurchases.id = tblpurchases_items.purchases_id',
);

$order_byimport = 'order by product_id asc';
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblpurchases_items.id as id_main,tblpurchases_items.product_id as product_id,tblpurchases_items.type as type'));

$output  = $result['output'];
$rResult = $result['rResult'];
$footer_data['quantityyc'] = 0;
$footer_data['quantitydt'] = 0;
$footer_data['quantitycl'] = 0;

$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
foreach ($rResult as $key => $aRow) {
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['type']);
    $quantili_po = get_quantili_po($aRow['id_purchases_items']);
    $left = $aRow['quantity_net'] - $quantili_po;
    $footer_data['quantityyc'] += $aRow['quantity_net'];
    $footer_data['quantitydt'] += $quantili_po;
    $footer_data['quantitycl'] += $left;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblpurchases.date') {
            $_data = _d($aRow['tblpurchases.date']);
        }
        if ($aColumns[$i] == '1') {
            $_data = $get_items->code;
        }
        if ($aColumns[$i] == '2') {
            $_data = $get_items->name;
        }
        if ($aColumns[$i] == '3') {
            $_data =  '<div class="text-center">' . $get_items->unit_name_payment . '</div>';
        }
        if ($aColumns[$i] == 'tblpurchases_items.quantity_net as quantity_net') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_net']) . '</div>';
        }
        if ($aColumns[$i] == 'tblpurchases_items.id as id_purchases_items') {
            $_data = '<div class="text-center">' . formatNumber($quantili_po) . '</div>';
        }
        if ($aColumns[$i] == '6') {
            $_data = '<div class="text-center">' . formatNumber($left) . '</div>';
        }
        if ($aColumns[$i] == '7') {
            if ($quantili_po == 0) {
                $data_check = '<div class="label label-danger">Chưa đặt</div>';
            } else {
                if ($left <= 0) {
                    $data_check = '<div class="label label-info">Đã đặt đủ</div>';
                } elseif ($left > 0) {
                    $data_check = '<div class="label label-warning">Tạo 1 phần đơn hàng</div>';
                }
            }
            $_data = '<div class="text-center">' . $data_check . '</div>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums']              = $footer_data;
