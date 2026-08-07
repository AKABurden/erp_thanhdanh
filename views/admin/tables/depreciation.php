<?php
$hasPermissionDelete = has_permission('record_increased', '', 'delete');
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns         = [
    'tbldepreciation.id',
    'tbldepreciation.code',
    'tbldepreciation.date',
    'tbldepreciation.note',
    'tbldepreciation.total',
];
$sIndexColumn     = 'id';
$sTable           = 'tbldepreciation';
$additionalSelect = [
];
$join             = [
 
];

$where    = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[ $aColumns[$i] ];
        if ($aColumns[$i] == 'tbldepreciation.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        if ($aColumns[$i] == 'tbldepreciation.code') {
            $record_increase  = $aRow['tbldepreciation.code'];
            $isPerson = false;
            $record_increase = '<a href="#" onclick="edit_depreciation('.$aRow['tbldepreciation.id'].'); return false;" >' . $record_increase . '</a>';
            $record_increase .= '<div class="row-options">';
            $record_increase .= '<a href="#" onclick="edit_depreciation('.$aRow['tbldepreciation.id'].'); return false;" >' . _l('view') . '</a>';
            if ($hasPermissionDelete) {
                $record_increase .= ' | <a href="' . admin_url('depreciation/delete/' . $aRow['tbldepreciation.id']) . '" class="text-danger delete-remind">' . _l('delete') . '</a>';
            }   
            $record_increase .= '</div>';

            $_data = $record_increase;
        }
        else if($aColumns[$i] == 'tbldepreciation.total') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tbldepreciation.date') {
            $_data = _d($aRow[$aColumns[$i]]);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
