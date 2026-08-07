<?php

defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('suppliers_group', '', 'delete');
    $hasPermissionEdit = has_permission('suppliers_group', '', 'edit');
$aColumns[] = 'code';
$aColumns[] = 'name';

$sIndexColumn = 'id';
$sTable       = db_prefix().'suppliers_groups';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = '<a href="#" data-toggle="modal" data-target="#customer_group_modal" data-id="' . $aRow['id'] . '">' . $aRow[$aColumns[$i]] . '</a>';

        $row[] = $_data;
    }
    $options ='';
    if($hasPermissionEdit){
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#suppliers_group_modal', 'data-id' => $aRow['id']]);
	}
	if($hasPermissionDelete){
    $options .= icon_btn('suppliers/delete_group/' . $aRow['id'], 'remove', 'btn-danger _delete_ch');
	}
	$row[]   = $options ;
    $output['aaData'][] = $row;
}
