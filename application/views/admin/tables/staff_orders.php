<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns         = [
    'tbl_orders.id',
    'tbl_orders.date',
    'tbl_orders.reference_no',
    'tbl_orders.customer_id',
    'tbl_orders.grand_total'
];
$sIndexColumn     = 'id';
$sTable           = 'tbl_orders';
$additionalSelect = [
];
$join             = [
];

$where    = [];
$staff_id = get_staff_user_id();
if ($this->ci->input->post('staff_id')) {
    $staff_id = $this->ci->input->post('staff_id');
}
array_push($where, ' AND tbl_orders.employee_id = '.$staff_id);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[ $aColumns[$i] ];
        if ($aColumns[$i] == 'tbl_orders.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if($aColumns[$i] == 'tbl_orders.date') {
            $_data = _d($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tbl_orders.customer_id') {
            $customer = get_table_where('tblclients',array('userid'=>$aRow[$aColumns[$i]]),'','row');
            $_data = (!empty($customer->company) ? $customer->company : '');
        }
        else if($aColumns[$i] == 'tbl_orders.grand_total') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
