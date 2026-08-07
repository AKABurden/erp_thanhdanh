<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('debt_suppliers', '', 'delete');

$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    'tbl_orders.id',
    'tbl_orders.date',
    'tbl_orders.reference_no',
    'tbl_orders.tax_rate as tax_rate',
    'tbl_orders.total_tax as total_tax',
    'tbl_orders.grand_total as total_import',
    'COALESCE((SELECT SUM(tbl_returned_goods.grand_total) FROM tbl_returned_goods WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.order_id = tbl_orders.id),0) as returns',
    'tbl_orders.total_payment as total_payment_import',
    'tbl_orders.price_other_expenses as price_other_expenses_import',
    '7',
];
$sIndexColumn = 'id';
$sTable       = 'tbl_orders';
$where        = [];
$filter = [];
$join = [
    'LEFT JOIN tblclients ON tblclients.userid=tbl_orders.customer_id',
];
$date_start = to_sql_date($this->ci->input->post('date_start'));
array_push($where, 'AND tbl_orders.status_payment != 2');
array_push($where, 'AND tbl_orders.grand_total > 0');
if(!empty($date_start))
{
    array_push($where, 'AND tbl_orders.date >=','"'.$date_start.' 00:00:00"');
}
$date_end = to_sql_date($this->ci->input->post('date_end'));
if(!empty($date_end))
{
   array_push($where, 'AND tbl_orders.date <=', '"'.$date_end.' 00:00:00"');
}
if(!empty($uerid))
{
    array_push($where, 'AND tblclients.userid = '.$uerid);
}
if ($this->ci->input->post('filterType')) {
	if (is_numeric($this->ci->input->post('filterType'))) {
		if ($this->ci->input->post('filterType') == 2) {
			$where[] = 'AND tbl_orders.id IN (
									SELECT tbl_orders.id 
									FROM tbl_orders 
									WHERE tbl_orders.status < 2
									AND tbl_orders.customer_id = tblclients.userid
									AND DATEDIFF(CURDATE(), tbl_orders.date) > tblclients.debt_limit_day
									AND tblclients.debt_limit_day <> 0
								)';
		}
	}
}

$filterStatus = $this->ci->input->post('filterStatus');
if($filterStatus == 1)
{
    // array_push($where, 'AND tblclients.debt_limit > 0 AND tblclients.debt_limit < ((select(SUM(tbl_orders.grand_total)) from tbl_orders where tbl_orders.customer_id=tblclients.userid ) -(select((COALESCE(SUM(tbl_orders.total_payment),0)+ COALESCE(SUM(tbl_orders.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.total_payment),0))) from tbl_orders left JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tbl_orders.red_invoice where ((tbl_orders.status_pay != 2 AND tbl_orders.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tbl_orders.status_pay = 0)) AND tbl_orders.customer_id=tblclients.userid))');
}
$group_by = 'GROUP BY tblclients.userid';
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
'tbl_orders.status'
],'',$group_by);
$output  = $result['output'];
$rResult = $result['rResult'];
$j=0;
$today = date('Y-m-d');
$week30s = strtotime(date("Y-m-d", strtotime($today)) . " -30 day");
$week30 = strftime("%Y-%m-%d", $week30s);
$week60s = strtotime(date("Y-m-d", strtotime($today)) . " -60 day");
$week60 = strftime("%Y-%m-%d", $week60s);
$week90s = strtotime(date("Y-m-d", strtotime($today)) . " -90 day");
$week90 = strftime("%Y-%m-%d", $week90s);

$footer_data = array(
    'debt' => 0,
    'payment' => 0,
    'left' => 0,
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
        if ($aColumns[$i] == 'tbl_orders.id') {
            $_data = $aRow['tbl_orders.id'];
        }
        if ($aColumns[$i] == 'tbl_orders.date') {
        $_data =_dhau($aRow['tbl_orders.date']);
        }
        if ($aColumns[$i] == 'tblclients.company') {
        $_data ='<a onclick="client_detail('.$aRow['tblclients.userid'].')">'.$aRow['tblclients.company'].'</a>';
        }
        if ($aColumns[$i] == 'tbl_orders.total_payment as total_payment_import') {
            $_data = formatNumber($aRow['total_payment_import']);      
        }
        if ($aColumns[$i] == 'tbl_orders.total_tax as total_tax') {
            $_data = formatNumber($aRow['total_tax']);      
        }
        if ($aColumns[$i] == 'COALESCE((SELECT SUM(tbl_returned_goods.grand_total) FROM tbl_returned_goods WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.order_id = tbl_orders.id),0) as returns') {
            $_data = formatNumber($aRow['returns']);      
        }
        if ($aColumns[$i] == 'tbl_orders.tax_rate as tax_rate') {
            $_data = formatNumber($aRow['tax_rate']).'%';      
        }
        if ($aColumns[$i] == '7') {
        $_data =formatNumber($aRow['total_import']-$aRow['total_payment_import'] - $aRow['price_other_expenses_import'] - $aRow['returns']);
        $footer_data['left']+=$aRow['total_import']-$aRow['total_payment_import'] - $aRow['price_other_expenses_import'] - $aRow['returns'];
        }
        if ($aColumns[$i] == 'tbl_orders.price_other_expenses as price_other_expenses_import') {
        $_data =formatNumber($aRow['price_other_expenses_import']); 
        $footer_data['payment']+=$aRow['total_payment_import'];  
        }
        if ($aColumns[$i] == 'tbl_orders.grand_total as total_import') {
        $_data =formatNumber($aRow['total_import']);
        $footer_data['debt']+=$aRow['total_import'];     
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;