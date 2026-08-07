<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    '2',
    'tblclients.company',
    'tbl_contracts_sales.subject',
    'tbl_contracts_sales.quote_id',
    '5',
    'tbl_contracts_sales.amount',
    'tbl_contracts_sales.date_start',
    'tbl_contracts_sales.date_end'
];

$sIndexColumn = 'id';
$sTable       = 'tbl_contracts_sales';

$join         = array(
    'LEFT JOIN tblclients ON tblclients.userid = tbl_contracts_sales.customer_id'
);

$where        = array(
);

array_push($where, 'AND tbl_contracts_sales.customer_id = '.$clientid);
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tbl_contracts_sales.date_start BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
    }
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbl_contracts_sales.id',
    'tbl_contracts_sales.prefix',
    'tbl_contracts_sales.code',
    'tbl_contracts_sales.arr_staff',
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == '2') {
            $_data='<a href="'.admin_url('contracts_sales/detail/'.$aRow['id']).'">'.$aRow['prefix'].$aRow['code'].'</a>';
        }
        else if ($aColumns[$i] == 'tbl_contracts_sales.quote_id') {
            $_data='';
            if(!empty($aRow['tbl_contracts_sales.quote_id']))
            {
                $quote = get_table_where('tbl_quotes',array('id'=>$aRow['tbl_contracts_sales.quote_id']),'','row');
                $_data=$quote->reference_no;
            }
            
        }
        else if ($aColumns[$i] == '5') {
            $_data = "";
            $staff = explode(',', $aRow['arr_staff']);
            foreach ($staff as $key => $value) {
                $_data .= get_staff_full_name($value).',<br>';
            }
            $_data = trim($_data,',<br>');
        }
        else if ($aColumns[$i] == 'tbl_contracts_sales.amount') {
            $_data = number_format($aRow['tbl_contracts_sales.amount']);
        }
        else if ($aColumns[$i] == 'tbl_contracts_sales.amount') {
            $_data = number_format($aRow['tbl_contracts_sales.amount']);
        }
        else if ($aColumns[$i] == 'tbl_contracts_sales.date_start' || $aColumns[$i] == 'tbl_contracts_sales.date_end') {
            $_data = _d($aRow[$aColumns[$i]]);
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}