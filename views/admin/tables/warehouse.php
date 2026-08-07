<?php

defined('BASEPATH') or exit('No direct script access allowed');
    $hasPermissionDelete = has_permission('warehouse', '', 'delete');
    $hasPermissionEdit = has_permission('warehouse', '', 'edit');
$aColumns = [
    'tblwarehouse.id',
    'tblgroup_warehouse.name',
	'tblwarehouse.code',
	'tblwarehouse.name',
    'tblwarehouse.note',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'warehouse';
$where = array();
$join         = array(
    'LEFT JOIN tblgroup_warehouse ON tblgroup_warehouse.id = tblwarehouse.id_group_warehouse'
);
if(is_numeric($this->ci->input->post('filterStatus')))
{   
    if($this->ci->input->post('filterStatus') == 1)
    {
        $where[] = 'AND tblwarehouse.supplier_id = 0';
    }elseif($this->ci->input->post('filterStatus') == 2)
    {
        $where[] = 'AND tblwarehouse.supplier_id != 0';
    }
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblwarehouse.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        $row[] = $_data;
    }

    $options = icon_btn(admin_url('warehouse/detail_warehouse/'.$aRow['tblwarehouse.id']), 'eye', 'btn-default',['target'=>'_blank']);
    if($hasPermissionEdit)
    {
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit('.$aRow['tblwarehouse.id'].'); return false;']);
    }
    if($hasPermissionDelete)
    {
    if($aRow['tblwarehouse.id'] != 8){
    $options .= icon_btn('#', 'remove', 'btn-danger delete-remind', ['onclick' => 'delete_main('.$aRow['tblwarehouse.id'].'); return false;']);
    }
    }
    $row[]   = $options;
    $output['aaData'][] = $row;
}
