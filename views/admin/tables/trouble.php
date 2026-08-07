<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'tbltrouble.id as id',
	'tbl_kpi_criteria.code_criteria as code_criteria',
	'tbldepartments.name as name_departments',
	'tbltrouble.code as code',
	'tbltrouble.name as name',
	'tbltrouble.name_stage as name_stage',
	'tbltrouble.name_task as name_task',
	'CONCAT(tbltrouble_violation_point.name, " (", tbltrouble_violation_point.point, ")") as trouble_violation_name',
	'2',
	'3',
	'4',
	'5',
];
$sWhere = [];
$sIndexColumn = 'id';
$sTable = 'tbltrouble';

$join = [];
$join[] = 'LEFT JOIN tbl_kpi_criteria ON tbl_kpi_criteria.id = tbltrouble.id_criteria';
$join[] = 'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltrouble.id_departments';
$join[] = 'LEFT JOIN tbltrouble_violation_point ON tbltrouble_violation_point.id = tbltrouble.trouble_violation_point_id';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere);
$output = $result['output'];
$rResult = $result['rResult'];
$start = $this->ci->input->post('start');
foreach ($rResult as $key => $aRow) {
	$material = $this->ci->db->get_where('tbltrouble_items', ['id_trouble' => $aRow['id'], 'type' => 'material'])->result_array();
	$man = $this->ci->db->get_where('tbltrouble_items', ['id_trouble' => $aRow['id'], 'type' => 'man'])->result_array();
	$machine = $this->ci->db->get_where('tbltrouble_items', ['id_trouble' => $aRow['id'], 'type' => 'machine'])->result_array();
	$method = $this->ci->db->get_where('tbltrouble_items', ['id_trouble' => $aRow['id'], 'type' => 'method'])->result_array();
	$procedure = $this->ci->db->get_where('tbltrouble_items', ['id_trouble' => $aRow['id'], 'type' => 'procedure'])->result_array();
	$countItems = 0;
	if ($countItems < count($material)) {
		$countItems = count($material);
	}
	if ($countItems < count($man)) {
		$countItems = count($man);
	}
	if ($countItems < count($machine)) {
		$countItems = count($machine);
	}
	if ($countItems < count($method)) {
		$countItems = count($method);
	}
	if ($countItems < count($procedure)) {
		$countItems = count($procedure);
	}
	$row = [];
	$row[] = ($start + $key + 1);
	$row[] = $aRow['code_criteria'];
	$row[] = $aRow['name_departments'];
	$row[] = $aRow['code'];
	$row[] = $aRow['name'];
	$row[] = $aRow['name_stage'];
	$row[] = $aRow['name_task'];
	$row[] = $aRow['trouble_violation_name'];
	$row[] = !empty($material[0]) ? $material[0]['name'] : '';
	if(isset($material[0])) {
		unset($material[0]);
	}

	$row[] = !empty($man[0]) ? $man[0]['name'] : '';
	if(isset($man[0])) {
		unset($man[0]);
	}

	$row[] = !empty($machine[0]) ? $machine[0]['name'] : '';
	if(isset($machine[0])) {
		unset($machine[0]);
	}

	$row[] = !empty($method[0]) ? $method[0]['name'] : '';
	if(isset($method[0])) {
		unset($method[0]);
	}

	$row[] = !empty($procedure[0]) ? $procedure[0]['name'] : '';
	if(isset($procedure[0])) {
		unset($procedure[0]);
	}
	$countItems--;

	$option = '';
	$option .= '<a class="btn btn-icon btn-default c_modal" href="' . admin_url('trouble/modal/' . $aRow['id']) . '"><i class="fa fa-edit"></i></a>';
	$option .= '<a class="btn btn-icon btn-danger _delete_row" href="' . admin_url('trouble/delete/' . $aRow['id']) . '"><i class="fa fa-remove"></i></a>';
	$row[] = $option;
	$row['DT_RowClass'] = 'warning';
	$output['aaData'][] = $row;

	for($i = 1; $i <= $countItems; $i++) {
		$row = [];
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = !empty($material[$i]) ? $material[$i]['name'] : '';
		$row[] = !empty($man[$i]) ? $man[$i]['name'] : '';
		$row[] = !empty($machine[$i]) ? $machine[$i]['name'] : '';
		$row[] = !empty($method[$i]) ? $method[$i]['name'] : '';
		$row[] = !empty($procedure[$i]) ? $procedure[$i]['name'] : '';
		$row[] = '';
		$output['aaData'][] = $row;
	}

	$row = [];
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row[] = '';
	$row['DT_RowClass'] = 'bg-sive';
	$output['aaData'][] = $row;
}
