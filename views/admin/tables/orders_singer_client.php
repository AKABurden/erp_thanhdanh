<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_orders.date',
    'tbl_orders.reference_no',
    'tbl_orders.customer_name',
    'tblshipping_client.address',
    'tbl_orders.grand_total',
    'CONCAT(tblstaff.firstname, " ", tblstaff.lastname)',
    'tbl_orders.status',
    'tbl_orders.count_delivery',
    'tbl_orders.status_custom',
    'tbl_orders.type_bills',
    'tbl_orders.note'
];

$sIndexColumn = 'id';
$sTable       = 'tbl_orders';

$join         = array(
    'LEFT JOIN tblshipping_client ON tblshipping_client.id = tbl_orders.address_delivery_id',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_orders.created_by',
);

$where        = array(
);

    array_push($where, 'AND tbl_orders.customer_id = '.$clientid);
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tbl_orders.date BETWEEN "' . to_sql_date($data_start[0]).' 00:00:00' . '" and "' . to_sql_date($data_start[1]).' 23:59:59' . '"');
    }
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbl_orders.id'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tbl_orders.date') {
            $_data = _dt($aRow['tbl_orders.date']);
        }
        else if ($aColumns[$i] == 'tbl_orders.reference_no') {
            $_data = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('orders/view_order/'.$aRow['id']).'" data-toggle="modal" data-target="#myModal">'.$aRow['tbl_orders.reference_no'].'</a>';
        }
        else if ($aColumns[$i] == 'tbl_orders.grand_total') {
            $_data = number_format($aRow['tbl_orders.grand_total']);
        }
        else if ($aColumns[$i] == 'tbl_orders.status') {
            if($aRow['tbl_orders.status'] == 'un_approved') {
                $_data = '<span class="label label-danger">'._l('tnh_un_approved').'</span>';
            }
            else {
                $_data = '<span class="label label-success">'._l('tnh_approved').'</span>';
            }
            
        }
        else if ($aColumns[$i] == 'tbl_orders.status_custom') {
            $str = '';
            $lb = '';
            if ($aRow['tbl_orders.status_custom'] == 0) {
                $str = _l('tnh_order_new');
                $lb = 'primary';
            } else if ($aRow['tbl_orders.status_custom'] == 1) {
                $str = _l('tnh_order_check');
                $lb = 'success';
            } else if ($aRow['tbl_orders.status_custom'] == 2) {
                $str = _l('tnh_order_delivery');
                $lb = 'default';
            } else if ($aRow['tbl_orders.status_custom'] == 3) {
                $str = _l('tnh_order_realize');
                $lb = 'warning';
            } else if ($aRow['tbl_orders.status_custom'] == 4) {
                $str = _l('tnh_order_finised');
                $lb = 'danger';
            }
            $_data = '<span class="label label-'.$lb.'">'.$str.'</span>';
        }
        else if ($aColumns[$i] == 'tbl_orders.type_bills') {
            $bill = '';
            if ($aRow['tbl_orders.type_bills'] == 0) {
                $_data = '<span class="label btn-success">'._l('tnh_retail_bill').'</span>';
            } else if ($aRow['tbl_orders.type_bills'] == 1) {
                $_data = '<span class="label btn-danger">'._l('tnh_tax_bill').'</span>';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
