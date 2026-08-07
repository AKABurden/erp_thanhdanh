<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblsupplier_classify.id',
    'tblsupplier_classify.code_supplier_classify',
    'tblsupplier_classify.name',
    'tblsupplier_classify.percent',
    'tblsupplier_classify.result_warning',
    '5'
];

$sIndexColumn = 'id';
$sTable       = 'tblsupplier_classify';

$join         = array(
);
$where         = array();
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblsupplier_classify.code_supplier_classify',
    'tblsupplier_classify.compare',
    'tblsupplier_classify.status',
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblsupplier_classify.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == 'tblsupplier_classify.percent') {
            $_data = '<span class="inline-block label label-info">'.$aRow['compare'].$aRow['tblsupplier_classify.percent'].' %'.'</span>';
        }
        else if ($aColumns[$i] == '5') {
            $_data = '';
            if($aRow['status'] == 0) {
                $_data .= '<div style="text-align: center;">';
                $_data .= '<a class="btn btn-default btn-icon" onclick="edit('.$aRow['tblsupplier_classify.id'].'); return false"><i class="fa fa-pencil-square-o"></i></a>';
                $_data .= '<a class="btn btn-danger btn-icon" onclick="delete_supplier_classify('.$aRow['tblsupplier_classify.id'].'); return false"><i class="fa fa-remove"></i></a>';
                $_data .= '</div>';
            }
            else {
                $_data .= '<div style="position: relative; text-align: center;">';
                $_data .= '<div class="inline-block" style="position: absolute; z-index: 9; left: calc(50% - 80px);border: 1px solid #c2c2c2; padding: 2px 5px; border-radius: 11px;">Không được phép chỉnh sửa</div>';
                $_data .= '<a class="btn btn-default btn-icon" style="filter:blur(5px); -webkit-filter:blur(5px);"><i class="fa fa-pencil-square-o"></i></a>';
                $_data .= '<a class="btn btn-danger btn-icon" style="filter:blur(5px); -webkit-filter:blur(5px);"><i class="fa fa-remove"></i></a>';
                $_data .= '</div>';
            }
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
