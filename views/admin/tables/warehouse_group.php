<?php

defined('BASEPATH') or exit('No direct script access allowed');
    $hasPermissionDelete = has_permission('warehouse_group', '', 'delete');
    $hasPermissionEdit = has_permission('warehouse_group', '', 'edit');

$aColumns = [
	'code',
	'name',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'group_warehouse';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        $row[] = $_data;
    }
    $options='';
    if($hasPermissionDelete)
    {
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_group('.$aRow['id'].'); return false;']);
    }
    if($hasPermissionEdit)
    {
    $options .= icon_btn('#', 'remove', 'btn-danger delete-remind', ['onclick' => 'delete_group('.$aRow['id'].'); return false;']);
    }
    $row[]   = $options;
    $output['aaData'][] = $row;
}
