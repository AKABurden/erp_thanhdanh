<?php
defined('BASEPATH') or exit('No direct script access allowed');

$status_table = $this->ci->input->post('status_table');
$role_search = $this->ci->input->post('role_search');

$aColumns = [
    'id',
    'code',
    'departments',
	'tblroles.name as name_role_1',
	'role_2.name as name_role_2',
    'time',
    'content',
    'type',
    '1',
];
$sWhere = [' AND tblcategory_tasks.hide  = 0'];
$sIndexColumn = 'id';
$sTable       = 'tblcategory_tasks';

$join = [
	'LEFT JOIN tblroles ON tblroles.roleid = tblcategory_tasks.role_id_1',
	'LEFT JOIN tblroles as role_2 ON role_2.roleid = tblcategory_tasks.role_id_2',
];

if (!empty($status_table) && $status_table != 'all') {
	array_push($sWhere, ' AND FIND_IN_SET('.$status_table.', tblcategory_tasks.departments) > 0');
}

if (!empty($role_search)) {
	$str_role_search = implode(',', $role_search);
	array_push($sWhere, ' AND (tblcategory_tasks.role_id_1 IN ('.$str_role_search.') OR tblcategory_tasks.role_id_2 IN ('.$str_role_search.'))');
}


$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere);
$output       = $result['output'];
$rResult      = $result['rResult'];
$key = 0;
$start = $this->ci->input->post('start');
foreach ($rResult as $key => $aRow) {
	$process = $this->ci->db->get_where('tblcategory_tasks_process', ['id_category_tasks' => $aRow['id']])->result_array();


    $row = [];
  	$row[] = ($start + $key + 1);
  	$row[] = $aRow['code'];
  	$departments = '';
  	if(!empty($aRow['departments'])){
		$this->ci->db->select('GROUP_CONCAT(tbldepartments.name  SEPARATOR ",<br>") as name_s');
		$this->ci->db->where_in('departmentid', explode(',',$aRow['departments']));
		$departments = $this->ci->db->get('tbldepartments')->row()->name_s;
	}
  	$row[] = $departments;

	$row[] = $aRow['name_role_1'];
	$row[] = $aRow['name_role_2'];

  	$row[] = $aRow['time'];
  	$row[] = $aRow['content'];
  	$row[] = !empty($process[0]) ? $process[0]['name'] : '';
	if(isset($process[0])) {
		unset($process[0]);
	}

	$type = '';
	if ($aRow['type'] == 1) {
		$type = 'Ngày';
	} else if ($aRow['type'] == 2) {
		$type = 'Tháng';
	} else if ($aRow['type'] == 3) {
		$type = 'Năm';
	}

	$row[] = '<div class="text-center">'.$type.'</div>';

	$option = '';
	$option .= '<a class="btn btn-icon btn-default c_modal" href="'.admin_url('category_tasks/modal/' . $aRow['id']).'"><i class="fa fa-edit"></i></a>';
	$option .= '<a class="btn btn-icon btn-danger _delete_row" href="'.admin_url('category_tasks/delete/' . $aRow['id']).'"><i class="fa fa-remove"></i></a>';
	$row[] = $option;
	$row['DT_RowClass'] = 'warning';
    $output['aaData'][] = $row;

	if(!empty($process)) {
		foreach($process as $k => $v) {
			$row = [];
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = $v['name'];
			$row[] = '';
			$row[] = '';
			$output['aaData'][] = $row;
		}
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

	$row['DT_RowClass'] = 'bg-sive';
	$output['aaData'][] = $row;



}
