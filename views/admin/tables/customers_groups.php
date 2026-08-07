<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
//	'tblcustomers_groups.id as id',
	'tblcustomers_groups.name as name',
//	'tblcustomers_groups.color as color',
//	'1'
];
//$aColumns[] = 'color';

$sIndexColumn = 'id';
$sTable       = 'tblcustomers_groups';

$join = [];
$where = [];
$where[] = 'AND id_parent = 0';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'tblcustomers_groups.id',
	'tblcustomers_groups.color as color',
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

	$this->ci->db->where('id_parent', $aRow['id']);
	$data_parent = $this->ci->db->get('tblcustomers_groups')->result_array();

    $row = [];
//	$row[] = $aRow['id'];
    $row[]  = '<a href="#" class="rowTD" data-toggle="modal" data-target="#customer_group_modal" data-parent="" data-color="' . $aRow['color'] . '" data-id="' . $aRow['id'] . '">' . $aRow['name'] . '</a>';
//    $row[]  = '<p style="width:100px;background-color:'.$aRow['color'].' ">'.$aRow['color'].'</p>';
//    $row[]  = '';
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#customer_group_modal', 'data-id' => $aRow['id']]);
    $row[]   = $options .= icon_btn('clients/delete_group/' . $aRow['id'], 'remove', 'btn-danger _delete');
	$row['DT_RowClass'] = 'warning';
    $output['aaData'][] = $row;

	if(!empty($data_parent)) {
		foreach($data_parent as $key => $value) {
			$row = [];
//			$row[] = '';
			$row[]  = '<div class="col-md-1"></div><div class="col-md-11"><a class="rowTD" href="#" data-toggle="modal" data-target="#customer_group_modal" data-parent="'.$aRow['id'].'" data-color="' . $value['color'] . '" data-id="' . $value['id'] . '">' . $value['name'] . '</a></div>';
//			$row[]  = '<p style="width:100px;background-color:'.$value['color'].' ">'.$value['color'].'</p>';
//			$row[]  = '<p data-id="'.$aRow['id'].'">'.$aRow['name'].'</p>';
			$options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#customer_group_modal', 'data-id' => $value['id']]);
			$row[]   = $options .= icon_btn('clients/delete_group/' . $value['id'], 'remove', 'btn-danger _delete');
			$output['aaData'][] = $row;
		}
	}
}
