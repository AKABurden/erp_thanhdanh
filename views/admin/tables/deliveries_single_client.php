<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_deliveries.date',
    'tbl_deliveries.reference_no',
    'tbl_deliveries.customer_name',
    'tblshipping_client.address',
    '5',
    'tbl_deliveries.grand_total',
    'CONCAT(tblstaff.firstname, " ", tblstaff.lastname)',
    'tbl_deliveries.status',
    'tbl_deliveries.note'
];

$sIndexColumn = 'id';
$sTable       = 'tbl_deliveries';

$join         = array(
    'LEFT JOIN tblshipping_client ON tblshipping_client.id = tbl_deliveries.address_delivery_id',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_deliveries.created_by',
);

$where        = array(
);

array_push($where, 'AND tbl_deliveries.customer_id = '.$clientid);
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tbl_deliveries.date BETWEEN "' . to_sql_date($data_start[0]).' 00:00:00' . '" and "' . to_sql_date($data_start[1]).' 23:59:59' . '"');
    }
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbl_deliveries.id',
    'tbl_deliveries.order_id'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tbl_deliveries.date') {
            $_data = _dt($aRow['tbl_deliveries.date']);
        }
        else if ($aColumns[$i] == '5') {
            $arr = explode(',', $aRow['order_id']);
            $str = '';
            foreach ($arr as $key => $value) {
                $str .= get_table_where('tbl_orders',array('id'=>$value),'','row')->reference_no.', ';
            }
            $_data = trim($str, ', ');
        }
        else if ($aColumns[$i] == 'tbl_deliveries.grand_total') {
            $_data = number_format($aRow['tbl_deliveries.grand_total']);
        }
        else if ($aColumns[$i] == 'tbl_deliveries.status') {
            if($aRow['tbl_deliveries.status'] == 'un_approved') {
                $_data = '<span class="label label-danger">'._l('tnh_un_approved').'</span>';
            }
            else {
                $_data = '<span class="label label-success">'._l('tnh_approved').'</span>';
            }
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}