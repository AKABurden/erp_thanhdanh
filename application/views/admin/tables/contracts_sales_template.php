<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_contracts_sales_template.id',
    'tbl_contracts_sales_template.name',
];

$sIndexColumn = 'id';
$sTable       = 'tbl_contracts_sales_template';
$where = array();
$join         = array(
);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tbl_contracts_sales.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        $row[] = $_data;
    }
    $_data='';
    $_data .= '<a class="btn btn-default btn-icon" href="'.admin_url('contracts_sales/template_detail/'.$aRow['tbl_contracts_sales_template.id']).'"><i class="fa fa-pencil-square-o"></i></a>';
    
    $row[] = $_data;

    $output['aaData'][] = $row;
}
