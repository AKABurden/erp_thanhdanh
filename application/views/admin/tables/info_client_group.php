<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'color'
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'client_info_group';

$join         = array();

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], []);
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    $row[] = '<a>'.$aRow['id'].'</a>';
    $row[] = $aRow['name'];
    $row[] = '<span style="background: '.$aRow['color'].';">'.$aRow['color'].'</span>';
    $row[] = _l('cong_lable');
    $row[] = '';
    $options = icon_btn('#', 'plus-circle', 'btn-default', [
        'onclick' => "addOne(".$aRow['id']."); return false;",
        'data-toggle' => 'tooltip',
        'title' => _l('cong_add_from')
    ]);
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', [
        'onclick' => 'edit('.$aRow['id'].'); return false;',
        'data-toggle' => 'tooltip',
        'title' => _l('cong_edit_title_from')
    ]);
    $row[]   = $options .= icon_btn('#', 'remove', 'btn-danger delete-remind', ['onclick' => 'delete_main('.$aRow['id'].'); return false;']);
    $output['aaData'][] = $row;
}
