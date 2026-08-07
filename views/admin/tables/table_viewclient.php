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



$selectimport = array(
    'tbl_deliveries.date as date',
    'tbl_deliveries.reference_no as code',
    'tbl_deliveries.note as note',
    'tbl_deliveries.grand_total as grand_total',
);
$whereimport = array(
    'AND tbl_deliveries.customer_id = ' . $client_id,
    'AND tbl_orders.type_orders NOT IN (2, 4, 11)'
);
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($whereimport, 'AND tbl_deliveries.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($whereimport, 'AND tbl_deliveries.date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($whereimport, 'AND tbl_deliveries.id  = -1');
}
$aColumnsimport     = $selectimport;
$sIndexColumnimport = "id";
$sTableimport       = 'tbl_deliveries';
$joinimport         = array(
    'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id',
);
$order_byimport = 'order by id asc';
$resultimport  = data_tables_init($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tbl_deliveries.id as id_main'));

$outputimport  = $resultimport['output'];
$rResultimport = $resultimport['rResult'];

//thu tien
$selectvouchers = array(
    'tblvouchers_coupon.date_vouchers as date',
    'code_vouchers as code',
    'tblvouchers_coupon.note as note',
    'tblvouchers_coupon.payment as grand_total',
);
$wherevouchers = array(
    'AND tblvouchers_coupon.customer = ' . $client_id
);
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($wherevouchers, 'AND tblvouchers_coupon.date_vouchers >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($wherevouchers, 'AND tblvouchers_coupon.date_vouchers <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($wherevouchers, 'AND tblvouchers_coupon.id  = -1');
}
$aColumnsvouchers     = $selectvouchers;
$sIndexColumnvouchers = "id";
$sTablevouchers       = 'tblvouchers_coupon';
$joinvouchers         = array(
);
$order_byvouchers = 'order by id asc';
$resultvouchers  = data_tables_init($aColumnsvouchers, $sIndexColumnvouchers, $sTablevouchers, $joinvouchers, $wherevouchers, array('tblvouchers_coupon.id as id_main'));

$outputvouchers  = $resultvouchers['output'];
$rResultvouchers = $resultvouchers['rResult'];


//thu tien khác
$selectvouchersother = array(
    'tblother_payslips_coupon.date as date',
    'CONCAT(prefix,"-",code) as code',
    'tblother_payslips_coupon.note as note',
    'tblother_payslips_coupon.total as grand_total',
);
$wherevouchersother = array(
    'AND tblother_payslips_coupon.objects = 1',
    'AND tblother_payslips_coupon.objects_id = ' . $client_id
);
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($wherevouchersother, 'AND tblother_payslips_coupon.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($wherevouchersother, 'AND tblother_payslips_coupon.date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($wherevouchersother, 'AND tblother_payslips_coupon.id  = -1');
}
$aColumnsvouchersother     = $selectvouchersother;
$sIndexColumnvouchersother = "id";
$sTablevouchersother       = 'tblother_payslips_coupon';
$joinvouchersother         = array(
);
$order_byvouchersother = 'order by id asc';
$resultvouchersother  = data_tables_init($aColumnsvouchersother, $sIndexColumnvouchersother, $sTablevouchersother, $joinvouchersother, $wherevouchersother, array('tblother_payslips_coupon.id as id_main'));

$outputvouchersother  = $resultvouchersother['output'];
$rResultvouchersother = $resultvouchersother['rResult'];


$selectreturn = array(
    'tbl_returned_goods.date as date',
    'tbl_returned_goods.reference_no as code',
    'tbl_returned_goods.note as note',
    'tbl_returned_goods.grand_total as grand_total',
);
$wherereturn = array(
    'AND tbl_returned_goods.customer_id = ' . $client_id
);
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($wherereturn, 'AND tbl_returned_goods.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($wherereturn, 'AND tbl_returned_goods.date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($wherereturn, 'AND tbl_returned_goods.id  = -1');
}
$aColumnsreturn     = $selectreturn;
$sIndexColumnreturn = "id";
$sTablereturn       = 'tbl_returned_goods';
$joinreturn         = array(
);
$order_byreturn = 'order by id asc';
$resultreturn  = data_tables_init($aColumnsreturn, $sIndexColumnreturn, $sTablereturn, $joinreturn, $wherereturn, array('tbl_returned_goods.id as id_main'));

$outputreturn  = $resultreturn['output'];
$rResultreturn = $resultreturn['rResult'];

$aColumnsG = array(
    'date',
    'code',
    'note',
    'grand_total'
);
$rResultG = array();
if (!empty($rResultimport)) {
    $rResultG = array_merge($rResultG, $rResultimport);
}
if (!empty($rResultvouchers)) {
    $rResultG = array_merge($rResultG, $rResultvouchers);
}
if (!empty($rResultvouchersother)) {
    $rResultG = array_merge($rResultG, $rResultvouchersother);
}
if (!empty($rResultreturn)) {
    $rResultG = array_merge($rResultG, $rResultreturn);
}
if (!empty($rResultG)) {
    // usort($rResultG, ch_make_cmp(['type' => "desc", 'product_id' => "desc", 'localtion_id' => "desc", 'warehouseman_date' => "asc"]));
}
$output = $outputimport;
$output['iTotalRecords'] = $outputimport['iTotalRecords'];


$output['iTotalDisplayRecords'] = $outputimport['iTotalDisplayRecords'];
$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
$footer_data['grand_total'] = 0;

// $row= array();
foreach ($rResultG as $key => $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == 'date') {
            $_data = '<div class="text-center">' . _dhau($aRow['date']) . '<div>';
        }
        // if ($aColumnsG[$i] == 'warehouseman_date') {
        //     $_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
        // }
        if ($aColumnsG[$i] == 'grand_total') {
            $footer_data['grand_total'] += $aRow['grand_total'];
            $_data = '<div class="text-right">' . formatNumber($aRow['grand_total']) . '<div>';
        }
        // if ($aColumnsG[$i] == '1') {
        //     $_data = '';
        // }
        // if ($aColumnsG[$i] == 'warehouse_id') {
        //     $_data = '';
        //     if (!empty($aRow['warehouse_id'])) {
        //         $warehous = get_table_where('tblwarehouse', array('id' => $aRow['warehouse_id']), '', 'row');
        //         if (!empty($warehous)) {
        //             $_data = $warehous->name;
        //         }
        //     }
        // }
        // if ($aColumnsG[$i] == 'code') {
        //     if ($aRow['exists_quantity'] == 1) {
        //         $_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-warning">' . _l('ch_importss') . '</span>';
        //     } elseif ($aRow['exists_quantity'] == 99) {
        //         $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_delivery/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('Giao hàng') . '</span>';
        //     } 
        // }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums']              = $footer_data;