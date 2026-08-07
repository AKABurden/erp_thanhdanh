<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['code'];
$aColumns[] = 'name';
$aColumns[] = 'color';

$sIndexColumn = 'id';
$sTable       = 'tblstatus_client';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[]  = '<a href="#" data-toggle="modal" data-target="#customer_group_modal" data-id="' . $aRow['id'] . '">' . $aRow['code'] . '</a>';
    $row[]  = '<a href="#" data-toggle="modal" data-target="#customer_group_modal" data-id="' . $aRow['id'] . '">' . $aRow['name'] . '</a>';
    $row[]  = '<p style="width:100px;background-color:'.$aRow['color'].' ">'.$aRow['color'].'</p>';
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#customer_group_modal', 'data-id' => $aRow['id']]);
    $row[]   = $options .= icon_btn('clients/delete_status_client/' . $aRow['id'], 'remove', 'btn-danger delete-remind');

    $output['aaData'][] = $row;
}
