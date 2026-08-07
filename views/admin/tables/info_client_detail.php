<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblclient_info_detail.id',
    'tblclient_info_group.name',
    'tblclient_info_detail.name'
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'client_info_detail';

$join         = array(
    'LEFT JOIN tblclient_info_group ON tblclient_info_group.id = tblclient_info_detail.id_info_group'
);
$where        = array(
);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblclient_info_group.id',
    'tblclient_info_group.color'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblclient_info_detail.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == 'tblclient_info_group.name') {
            $_data = '<span class="label label-default" style="border: 1px solid '.$aRow['color'].';">'.$aRow['tblclient_info_group.name'].'</span>';
        }
        $row[] = $_data;
    }
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit('.$aRow['tblclient_info_detail.id'].'); return false;']);
    $row[]   = $options .= icon_btn('#', 'remove', 'btn-danger delete-remind', ['onclick' => 'delete_main('.$aRow['tblclient_info_detail.id'].'); return false;']);

    $output['aaData'][] = $row;
}
