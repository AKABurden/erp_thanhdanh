<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
	'date',
    '3'
];

$sIndexColumn = 'id';
$sTable       = 'tblcontracts_sales_file';

$join         = array(
);
$where        = array();

array_push($where, 'AND id_contracts_sale = ' . $id);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'id',
    'id_contracts_sale'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="' . base_url('uploads/contracts_sales/' . $aRow['id_contracts_sale'] . '/' . $aRow['name']) . '">'.$aRow['name'].'</a>';
        }
        else if ($aColumns[$i] == 'date') {
            $_data = _dt($aRow['date']);
        }
        else if ($aColumns[$i] == '3') {
            $_data = '<a class="btn btn-danger" onclick="delete_file('.$aRow['id_contracts_sale'].','.$aRow['id'].'); return false;"><i class="fa fa-times"></i></a>';
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
