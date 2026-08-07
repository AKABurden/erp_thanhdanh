<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblresult_evaluate.id',
    'tblresult_evaluate.setting_date_evaluate',
    'tblsuppliers.company',
    'tblresult_evaluate.percent',
    'tblresult_evaluate.month',
    'tblsupplier_classify.name'
];

$sIndexColumn = 'id';
$sTable       = 'tblresult_evaluate';

$join         = array(
    'LEFT JOIN tblsuppliers on tblsuppliers.id = tblresult_evaluate.id_suppliers',
    'LEFT JOIN tblsupplier_classify on tblsupplier_classify.id = tblresult_evaluate.id_classify'
);
$where         = array();
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblsupplier_classify.result_warning'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblresult_evaluate.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == 'tblsuppliers.company') {
            $_data = $aRow['tblsuppliers.company'].'<br>';
            if($aRow['result_warning'] && $aRow['result_warning'] != '') {
                $_data .= '<span class="inline-block" style="background:#f00; color:#fff; padding:5px 10px; border-radius:15px;">'.$aRow['result_warning'].'</span>';
            }
        }
        else if ($aColumns[$i] == 'tblresult_evaluate.percent') {
            $_data = '<span class="inline-block label label-info">'.$aRow['tblresult_evaluate.percent'].' %'.'</span>';
        }
        else if ($aColumns[$i] == 'tblsupplier_classify.name') {
            $_data = '<span class="inline-block label label-warning">'.$aRow['tblsupplier_classify.name'].'</span>';
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
