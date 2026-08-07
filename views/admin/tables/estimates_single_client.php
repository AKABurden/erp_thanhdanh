<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_quotes.date',
    'tbl_quotes.reference_no',
    'tbl_quotes.pre_reference_no',
    'tbl_quotes.customer_id',
    'tbl_quotes.validity',
    'tbl_quotes.grand_total',
    'tbl_quotes.note_internal',
    'CONCAT(tblstaff.firstname, " ", tblstaff.lastname)',
    'tbl_quotes.status',
    'tbl_orders.reference_no',
    'CONCAT(tbl_contracts_sales.prefix, "-", tbl_contracts_sales.code)'
];

$sIndexColumn = 'id';
$sTable       = 'tbl_quotes';

$join         = array(
    'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_quotes.order_id',
    'LEFT JOIN tbl_contracts_sales ON tbl_contracts_sales.id = tbl_quotes.contract_id',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_quotes.created_by',
);

$where        = array(
);

array_push($where, 'AND tbl_quotes.customer_id = '.$clientid);
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tbl_quotes.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
    }
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbl_quotes.id'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tbl_quotes.date') {
            $_data = _dt($aRow['tbl_quotes.date']);
        }
        else if ($aColumns[$i] == 'tbl_quotes.reference_no') {
            $_data = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('quotes/view_quotes/'.$aRow['id']).'" data-toggle="modal" data-target="#myModal">'.$aRow['tbl_quotes.reference_no'].'</a>';
        }
        else if ($aColumns[$i] == 'tbl_quotes.pre_reference_no') {
            $_data = '';
            if($aRow['tbl_quotes.pre_reference_no'] > 0) {
                $_data = get_table_where('tbl_quotes',array('id'=>$aRow['tbl_quotes.pre_reference_no']),'','row')->reference_no;
            }
        }
        else if ($aColumns[$i] == 'tbl_quotes.customer_id') {
            $_data = '';
            if($aRow['tbl_quotes.customer_id'] > 0) {
                $_data = get_table_where('tblclients',array('userid'=>$aRow['tbl_quotes.customer_id']),'','row')->company;
            }
        }
        else if ($aColumns[$i] == 'tbl_quotes.validity') {
            $_data = _dt($aRow['tbl_quotes.validity']);
        }
        else if ($aColumns[$i] == 'tbl_quotes.grand_total') {
            $_data = number_format($aRow['tbl_quotes.grand_total']);
        }
        else if ($aColumns[$i] == 'tbl_quotes.status') {
            if($aRow['tbl_quotes.status'] == 'un_approved') {
                $_data = '<span class="label label-danger">'._l('tnh_un_approved').'</span>';
            }
            else {
                $_data = '<span class="label label-success">'._l('tnh_approved').'</span>';
            }
        }
        else if ($aColumns[$i] == 'tbl_orders.reference_no') {
            if($aRow['tbl_orders.reference_no']) {
                $_data = '<span class="label label-success">'._l('tnh_created_an_order').'</span>';
            }
            else {
                $_data = '<span class="label label-danger">'._l('tnh_un_created_an_order').'</span>';
            }
        }
        else if ($aColumns[$i] == 'CONCAT(tbl_contracts_sales.prefix, "-", tbl_contracts_sales.code)') {
            if($aRow[$aColumns[$i]]) {
                $_data = '<span class="label label-success">'._l('ch__status_contract').'</span>';
            }
            else {
                $_data = '<span class="label label-danger">'._l('ch__not_status_contract').'</span>';
            }
        }
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
