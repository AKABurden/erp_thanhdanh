<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'CONCAT(firstname," ",lastname)',
    'birthday',
    'phonenumber',
    'email',
];

$sIndexColumn = 'staffid';
$sTable       = 'tblstaff';

$join         = array(
);
$where = [];
array_push($where, 'AND month(tblstaff.birthday) = '.date('m'));
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'birthday') {
            $_data = _d($aRow['birthday']);
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
