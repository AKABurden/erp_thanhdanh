<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('debt_suppliers', '', 'delete');
$start_date_search = $this->ci->input->post('start_date_search');
$end_date_search = $this->ci->input->post('end_date_search');
if(!empty($start_date_search))
{
    $start_date_search = to_sql_date($start_date_search).' 00:00:00';
}
if(!empty($end_date_search))
{
    $end_date_search = to_sql_date($end_date_search).' 23:59:59';
}
$this->ci->db->query("SET sql_mode = ''");

$filterStatus = $this->ci->input->post('filterStatus');
$vouchers_coupon_dk = '
    COALESCE(
      (
        SELECT SUM(tblvouchers_coupon.payment) 
        FROM tblvouchers_coupon 
        WHERE tblvouchers_coupon.customer = tblclients.userid ';
        if(!empty($start_date_search))
        {
            $vouchers_coupon_dk.= 'AND tblvouchers_coupon.date_vouchers < "'.$start_date_search.'"';
        }
		else{
            $vouchers_coupon_dk.= 'AND tblvouchers_coupon.id = -1';
        }
$vouchers_coupon_dk .= '),0)';
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
            SELECT SUM(tbl_orders.grand_total)
            FROM tbl_orders 
            WHERE tbl_orders.customer_id = tblclients.userid
            ';
            if(!empty($start_date_search))
            {
                $sumGrandTotal_dk.= 'AND tbl_orders.date < "'.$start_date_search.'"';
            }else{
                $sumGrandTotal_dk.= 'AND tbl_orders.id = -1';
            }

if ($filterStatus == 2) {
	$sumGrandTotal_dk .= ' AND tbl_orders.id IN (
									SELECT tbl_orders.id 
									FROM tbl_orders 
									WHERE tbl_orders.status < 2
									AND tbl_orders.customer_id = tblclients.userid
									AND DATEDIFF(CURDATE(), tbl_orders.date) > tblclients.debt_limit_day
									AND tblclients.debt_limit_day <> 0
								)';
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
            SELECT SUM(tbl_orders.grand_total)
            FROM tbl_orders 
            WHERE tbl_orders.customer_id = tblclients.userid
            ';
            if(!empty($start_date_search))
            {
                $sumGrandTotal.= 'AND tbl_orders.date >= "'.$start_date_search.'"';
            }
            if(!empty($end_date_search))
            {
                $sumGrandTotal.= 'AND tbl_orders.date <= "'.$end_date_search.'"';
            }
		if ($filterStatus == 2) {
			$sumGrandTotal .= ' AND tbl_orders.id IN (
									SELECT tbl_orders.id 
									FROM tbl_orders 
									WHERE tbl_orders.status < 2
									AND tbl_orders.customer_id = tblclients.userid
									AND DATEDIFF(CURDATE(), tbl_orders.date) > tblclients.debt_limit_day
									AND tblclients.debt_limit_day <> 0
								)';
		}
$sumGrandTotal.= ")";

$text = 'COALESCE('.($sumGrandTotal_dk .' - '. $vouchers_coupon_dk .' - '. $other_dk .' - '.$return_dk.' ').',0) + tblclients.debt_begin as begin';

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
             as returns';

$return_where = '
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
$return_where.= '),0)';


$aColumns = [
    'tblclients.userid',
    'tblclients.company',
    $text,
    "(COALESCE($sumGrandTotal,0)) as total_import",
    $return,
    '('.$vouchers_coupon.' + '.$other.')  as total_payment_import',
    '7',
];
$sIndexColumn = 'userid';
$sTable       = 'tblclients';
$where        = [];
//$having = 'HAVING (total_import - total_payment_import) > 0';
$having = 'HAVING (total_import - total_payment_import + begin - returns) <> 0';
$filter = [];
$join = [
    'LEFT JOIN tbl_orders ON tbl_orders.customer_id = tblclients.userid',
    //    'LEFT JOIN tbl_orders ON tbl_orders.customer_id = tblclients.userid',
];
$customer_id = $this->ci->input->post('customer_id');
if (!empty($customer_id)) {
    array_push($where, 'AND tblclients.userid IN(' . trim($customer_id, ',') . ')');
}

if ($filterStatus == 1) {
	$where[] = "AND ((COALESCE($sumGrandTotal,0)) - ($vouchers_coupon + $other) + (COALESCE($sumGrandTotal_dk - $vouchers_coupon_dk - $other_dk - $return_dk, 0)) - $return_where > tblclients.debt_limit)";
	$where[] = "AND tblclients.debt_limit <> 0";
}
else if ($filterStatus == 2) {
	$where[] = 'AND tbl_orders.id IN (
							SELECT tbl_orders.id 
							FROM tbl_orders 
							WHERE tbl_orders.status < 2
							AND tbl_orders.customer_id = tblclients.userid
							AND DATEDIFF(CURDATE(), tbl_orders.date) > tblclients.debt_limit_day
							AND tblclients.debt_limit_day <> 0
						)';
}

// sum note
$clients_id = $this->ci->input->post('clients_id');
if ($clients_id) {
    array_push($where, 'AND tblclients.userid = ' . explode('_', $clients_id)[2]);
}
// $start_date_search = $this->ci->input->post('start_date_search');
// if(!empty($start_date_search))
// {
//     array_push($where, 'AND tbl_orders.date >= "'.to_sql_date($this->ci->input->post('start_date_search')).'"');
// }

// $end_date_search = $this->ci->input->post('end_date_search');
// if(!empty($end_date_search))
// {
//     array_push($where, 'AND tbl_orders.date <= "'.to_sql_date($this->ci->input->post('end_date_search')).'"');
// }
// ./sum note
$group_by = 'GROUP BY tblclients.userid';
$result = data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'tblclients.debt_limit'
], '', $group_by, $having);
$output  = $result['output'];
$rResult = $result['rResult'];
$j = 0;
$today = date('Y-m-d');
$week30s = strtotime(date("Y-m-d", strtotime($today)) . " -30 day");
$week30 = strftime("%Y-%m-%d", $week30s);
$week60s = strtotime(date("Y-m-d", strtotime($today)) . " -60 day");
$week60 = strftime("%Y-%m-%d", $week60s);
$week90s = strtotime(date("Y-m-d", strtotime($today)) . " -90 day");
$week90 = strftime("%Y-%m-%d", $week90s);

$footer_data = array(
    'debt_total' => 0,
    'pay' => 0,
    'lefts' => 0,
    'begin' => 0,
    'returns' => 0,
);

foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblclients.userid') {
            $_data = '<div class="text-center">' . $j . '</div>';
        }
        if ($aColumns[$i] == 'tblclients.company') {
            $_data = '<a onclick="client_detail(' . $aRow['tblclients.userid'] . ')">' . $aRow['tblclients.company'] . '</a>';
        }
        if ($aColumns[$i] == '('.$vouchers_coupon.' + '.$other.')  as total_payment_import') {
            $_data = formatNumber($aRow['total_payment_import'] );
            
            $footer_data['pay']+=$aRow['total_payment_import'];
        }
        if ($aColumns[$i] == '7') {
            $_data = formatNumber($aRow['total_import'] - $aRow['total_payment_import']+ $aRow['begin'] - $aRow['returns']);
            $footer_data['lefts']+=$aRow['total_import'] - $aRow['total_payment_import'] + $aRow['begin'] - $aRow['returns'];
            $footer_data['debt_total'] += $aRow['total_import'];
        }
        if ($aColumns[$i] == $text) {
            $_data = formatNumber($aRow['begin']);
            $footer_data['begin'] += $aRow['begin'];
        }
        if ($aColumns[$i] == $return) {
            $_data = formatNumber($aRow['returns']);
            $footer_data['returns'] += $aRow['returns'];
        }
        if ($aColumns[$i] == "(COALESCE($sumGrandTotal,0)) as total_import") {
            $_data = formatNumber($aRow['total_import']);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;
