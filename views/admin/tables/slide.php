<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'stt',
    'content',
    'image',
    '4'
    ];
$sIndexColumn = 'id';
$sTable       = 'tbl_slideshow';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
    'id'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'image') {
            $_data = '<img width="50" src="'.base_url($aRow['image']).'">';
        }
        else if ($aColumns[$i] == '4') {
            $_data = icon_btn('#', 'pencil', 'btn-default', array('onclick'=>'edit('.$aRow['id'].'); return false;'));
            $_data .= icon_btn('slide/delete/' . $aRow['id'], 'remove', 'btn-danger');
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
