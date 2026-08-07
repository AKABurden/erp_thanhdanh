<?php
defined('BASEPATH') or exit('No direct script access allowed');
$months_report = $this->ci->input->post('report_months');
$CI = &get_instance();
$beginMonth = '2000-01-01';
$endMonth = date('Y-m-d');

if ($months_report != '') {
    $custom_date_select = '';
    if (is_numeric($months_report)) {
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
$start_date_search = $beginMonth;
$end_date_search = $endMonth;
if(!empty($start_date_search))
{
    $start_date_search = ($start_date_search).' 00:00:00';
}
if(!empty($end_date_search))
{
    $end_date_search = ($end_date_search).' 23:59:59';
}
$this->ci->db->query("SET sql_mode = ''");

$vouchers_coupon_dk = '
    COALESCE(
      (
        SELECT SUM(tblvouchers_coupon.payment) 
        FROM tblvouchers_coupon 
        WHERE tblvouchers_coupon.customer = tblclients.userid ';
        if(!empty($start_date_search))
        {
            $vouchers_coupon_dk.= 'AND tblvouchers_coupon.date_vouchers < "'.$start_date_search.'"';
        }else{
            $vouchers_coupon_dk.= 'AND tblvouchers_coupon.id = -1';
        }
$vouchers_coupon_dk .= '),0)
      
  ';
$other_dk = '
    COALESCE(
      (
        SELECT SUM(tblother_payslips_coupon.total) 
        FROM tblother_payslips_coupon 
        WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
        ';
        if(!empty($start_date_search))
        {
            $other_dk.= 'AND tblother_payslips_coupon.date < "'.$start_date_search.'"';
        }else{
            $other_dk.= 'AND tblother_payslips_coupon.id = -1';
        }
        $other_dk.= '),0)
      
  ';
$sumGrandTotal_dk = '(
            SELECT SUM(tbl_deliveries.grand_total)
            FROM tbl_deliveries 
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id
            WHERE tbl_deliveries.customer_id = tblclients.userid AND tbl_orders.type_orders NOT IN (2, 4, 11)
            ';
            if(!empty($start_date_search))
            {
                $sumGrandTotal_dk.= 'AND tbl_deliveries.date < "'.$start_date_search.'"';
            }else{
                $sumGrandTotal_dk.= 'AND tbl_deliveries.id = -1';
            }
$sumGrandTotal_dk.= ")";


$return_dk = '
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid ';
            if(!empty($start_date_search))
            {
                $return_dk.= 'AND tbl_returned_goods.date < "'.$start_date_search.'"';
            }else{
                $return_dk.= 'AND tbl_returned_goods.id = -1';
            }
$return_dk.= '),0)';


$vouchers_coupon = '
    COALESCE(
      (
        SELECT COALESCE(SUM(tblvouchers_coupon.payment),0) 
        FROM tblvouchers_coupon 
        WHERE tblvouchers_coupon.customer = tblclients.userid ';
        if(!empty($start_date_search))
        {
            $vouchers_coupon.= 'AND tblvouchers_coupon.date_vouchers >= "'.$start_date_search.'"';
        }
        if(!empty($end_date_search))
        {
            $vouchers_coupon.= 'AND tblvouchers_coupon.date_vouchers <= "'.$end_date_search.'"';
        }
$vouchers_coupon .= '),0)
      
  ';
$other = '
    COALESCE(
      (
        SELECT SUM(tblother_payslips_coupon.total) 
        FROM tblother_payslips_coupon 
        WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
        ';
        if(!empty($start_date_search))
        {
            $other.= 'AND tblother_payslips_coupon.date >= "'.$start_date_search.'"';
        }
        if(!empty($end_date_search))
        {
            $other.= 'AND tblother_payslips_coupon.date <= "'.$end_date_search.'"';
        }
        $other.= '),0)
      
  ';
$sumGrandTotal = '(
            SELECT SUM(tbl_deliveries.grand_total)
            FROM tbl_deliveries 
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id
            WHERE tbl_deliveries.customer_id = tblclients.userid AND tbl_orders.type_orders NOT IN (2, 4, 11)
            ';
            if(!empty($start_date_search))
            {
                $sumGrandTotal.= 'AND tbl_deliveries.date >= "'.$start_date_search.'"';
            }
            if(!empty($end_date_search))
            {
                $sumGrandTotal.= 'AND tbl_deliveries.date <= "'.$end_date_search.'"';
            }
$sumGrandTotal.= ")";

$text_debt = 'COALESCE('.($sumGrandTotal_dk.' ').',0) + tblclients.debt_begin as begin';
$text_vouchers = 'COALESCE('.($vouchers_coupon_dk .' + '. $other_dk .' + '.$return_dk.' ').',0) as vouchers';
$return = '
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid ';
            if(!empty($start_date_search))
            {
                $return.= 'AND tbl_returned_goods.date >= "'.$start_date_search.'"';
            }
            if(!empty($end_date_search))
            {
                $return.= 'AND tbl_returned_goods.date <= "'.$end_date_search.'"';
            }
$return.= '),0)
             ';
// $aColumns     = array(
//     'tblclients.zcode',
//     'tblclients.company',
//     '3',
//     '4',
//     '5',
//     '6',
//     '7',
//     '8'
// );
$aColumns = [
    'tblclients.zcode',
    'tblclients.company',
    $text_debt,
    $text_vouchers,
    "(COALESCE($sumGrandTotal,0)) as total_import",
    '('.$vouchers_coupon.' + '.$other.' + '.$return.')  as total_payment_import',
    '7',
    '8',
];
$sIndexColumn = "userid";
$sTable       = 'tblclients';
$where        = array();

$customer_select = $this->ci->input->post('customer_select');
if ($customer_select) {
    $customer_id = explode('__', $customer_select);
    if (is_numeric($customer_id[1])) {
        array_push($where, 'AND tblclients.userid = ' . $customer_id[1]);
    }
}

$join         = array(
    // 'LEFT JOIN tbl_deliveries ON tbl_deliveries.customer_id = tblitems.id ',
);
$having = 'HAVING (begin + vouchers + total_import + total_payment_import) <> 0';

$result       = data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblclients.userid',
    'tblclients.debt_begin',
),'','',$having);
$output       = $result['output'];
$rResult      = $result['rResult'];
$output = $output;
$output['iTotalRecords'] = $output['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'];
$footer_data['total1'] = 0; //nợ đầu kỳ
$footer_data['total2'] = 0; //có đầu kỳ
$footer_data['total3'] = 0; //nợ phát sinh
$footer_data['total4'] = 0; //có phát sinh
$footer_data['total5'] = 0; //nợ cuối kỳ
$footer_data['total6'] = 0; //có cuối kỳ
foreach ($rResult as $aRow) {
    $row = array();
    $col1 = 0;
    $col2 = 0;
    $col3 = 0;
    $col4 = 0;
    $check1 = 0;
    $check2 = 0;

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblclients.zcode') {
            $_data = $aRow['tblclients.zcode'];
        }
        if ($aColumns[$i] == 'tblclients.company') {
            $_data = $aRow['tblclients.company'];
        }
        if ($aColumns[$i] == '('.$vouchers_coupon.' + '.$other.' + '.$return.')  as total_payment_import') {
            // $_data = formatNumber($aRow['total_payment_import'] );
            $_data = '<div class="text-right"><a onclick="viewclient(2,' . $aRow['userid'].'); return false">' . formatNumber($aRow['total_payment_import']) . '</a><div>';
            $col4 = $aRow['total_payment_import'];
            $footer_data['total4'] += $col4;

        }
        if ($aColumns[$i] == '7') {
            $_data = formatNumber($aRow['total_import'] - $aRow['total_payment_import']+ $aRow['begin']);
            // $footer_data['lefts']+=$aRow['total_import'] - $aRow['total_payment_import'] + $aRow['begin'] - $aRow['returns'];
            // $footer_data['debt_total'] += $aRow['total_import'];
        }
        if ($aColumns[$i] == $text_debt) {
            $_data = formatNumber($aRow['begin']);
            $col1 = $aRow['begin'];
            $footer_data['total1'] += $col1;
            // $footer_data['begin'] += $aRow['begin'];
        }
        if ($aColumns[$i] == $text_vouchers) {
            $_data = formatNumber($aRow['vouchers']);
            $col2 = $aRow['vouchers'];
            $footer_data['total2'] += $col2;

            // $footer_data['returns'] += $aRow['vouchers'];
        }
        if ($aColumns[$i] == "(COALESCE($sumGrandTotal,0)) as total_import") {
            // $_data = formatNumber($aRow['total_import']);
            $_data = '<div class="text-right"><a onclick="viewclient(1,' . $aRow['userid'].'); return false">' . formatNumber($aRow['total_import']) . '</a><div>';
            $col3 = $aRow['total_import'];
            $footer_data['total3'] += $col3;

        }
        if ($aColumns[$i] == '7') {
            $total_col = $col1 + $col3 - $col2 - $col4;
            if ($total_col > 0) {
                $_data = formatNumber($total_col);
                $footer_data['total5'] += $total_col;
            } else {
                $_data = 0;
                $footer_data['total5'] += 0;
            }
        } else if ($aColumns[$i] == '8') {
            $total_col = $col1 + $col3 - $col2 - $col4;
            if ($total_col < 0) {
                $_data = formatNumber(abs($total_col));
                $footer_data['total6'] += abs($total_col);
            } else {
                $_data = 0;
                $footer_data['total6'] += 0;
            }
        }
        $row[] = $_data;
    }
        $output['aaData'][] = $row;
}
$output['sums'] = $footer_data;
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;

$customer_select = $this->ci->input->post('customer_select');
$titleShow = [];
if(!empty($customer_select)) {
    $customer_select = explode('__', $customer_select);
    $this->ci->db->select('zcode, company');
    $this->ci->db->where('userid', $customer_select[0]);
    $get_client = $this->ci->db->get('tblclients')->row();
    $titleShow[] = (!empty($get_client) ? ('Khách Hàng: ' . $get_client->zcode.' ('.$get_client->company.')') : '');
}
$output['title_excel'] = [
    'Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') .' - '. (!empty($endMonth) ? _dt($endMonth) : ''),
    (!empty($titleShow) ? $titleShow : '')
];

