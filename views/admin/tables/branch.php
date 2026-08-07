<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'address',
    'number_phone',
];
$sIndexColumn = 'id';
$sTable       = 'tblbranch';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $ps    = '';
        if ($aColumns[$i] == 'name') {
            $_data = '<a onclick="edit_branch(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['name'] . '" data-address="' . $aRow['address'] . '" data-number_phone="' . $aRow['number_phone'] . '">' . $_data . '</a>';
        }
        $row[] = $_data;
    }

    $options = '<a class="btn btn-default btn-icon" onclick="edit_branch(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['name'] . '" data-address="' . $aRow['address'] . '" data-number_phone="' . $aRow['number_phone'] . '"><i class="fa fa-pencil-square-o"></i></a>';
    if ($aRow['id'] == 1) {
        $row[] = $options;
    } else {
        $row[] = $options .= icon_btn('branch/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    }

    $output['aaData'][] = $row;
}
