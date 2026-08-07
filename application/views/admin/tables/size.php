<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [];
$aColumns[] = 'tblsize.id';
$aColumns[] = 'tblsize.code_size';
$aColumns[] = 'tblsize.name';

$sIndexColumn = 'id';
$sTable       = 'tblsize';
$join = [];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [
    'id',
    'name',
    'code_size'
]
);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[]  = $aRow['id'];
    $row[]  = $aRow['code_size'];
    $row[]  = $aRow['name'];

    $option = '<a class="btn btn-default btn-icon" type="button" onclick="edit_size('.$aRow['id'].')"><i class="fa fa-edit"></i></a>';
    $option .= '<a class="btn btn-default btn-danger btn-icon" type="button" onclick="delete_size('.$aRow['id'].')"><i class="fa fa-remove"></i></a>';
    $row[] = $option;
    $output['aaData'][] = $row;
}
