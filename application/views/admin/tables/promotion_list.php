<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblpromotion_list.id',
	'tblpromotion_list.name',
    '3'
];

$sIndexColumn = 'id';
$sTable       = 'tblpromotion_list';

$join         = array(
);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], array(
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblpromotion_list.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == '3') {
            $_data = '';
            $_data .= '<a onclick="edit_promotion('.$aRow['tblpromotion_list.id'].');return false;" class="btn btn-default mright5"><i class="fa fa-edit"></i></a>';
            $_data .= '<a onclick="delete_promotion_list('.$aRow['tblpromotion_list.id'].');return false;" class="btn btn-danger delete-remind"><i class="fa fa-remove"></i></a>';
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
