<?php
defined('BASEPATH') or exit('No direct script access allowed');

$status_table = $this->ci->input->post('status_table');
$role_search = $this->ci->input->post('role_search');

$aColumns = [
	'tblcategory_tasks.id as id',
	'tblcategory_tasks.code as code',
	'departments',
	'role_2.name_position as name_role_1',
	'role_2.name as name_role_2',
	'time',
	'content',
	'tblcategory_tasks.type as type',
	'6',
	'5',
	'1',
	'2',
	'3',
	'4',
	'tbl_department_work_norms.quota as quota',
	'tblcategory_tasks.date_approve as date_approve',
	'tblcategory_tasks.active as active',
];
$sWhere = [' AND tblcategory_tasks.hide  = 0'];
$sIndexColumn = 'id';
$sTable       = 'tblcategory_tasks';

$join = [
	'LEFT JOIN tblroles as role_2 ON role_2.roleid = tblcategory_tasks.role_id_2',
	'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tblcategory_tasks.role_id_1',
	'LEFT JOIN tbl_department_work_norms ON tbl_department_work_norms.code_task = tblcategory_tasks.id',
	// tbldepartments.departmentid = tblroles.departments_id
];

if (!empty($status_table) && $status_table != 'all') {
	array_push($sWhere, ' AND FIND_IN_SET(' . $status_table . ', tblcategory_tasks.departments) > 0');
}

if (!empty($role_search)) {
	$str_role_search = implode(',', $role_search);
	array_push($sWhere, ' AND (tblcategory_tasks.role_id_1 IN (' . $str_role_search . ') OR tblcategory_tasks.role_id_2 IN (' . $str_role_search . '))');
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
	$departments = '';

	if (!empty($aRow['departments'])) {
		$this->ci->db->select('GROUP_CONCAT(tbl_room.name  SEPARATOR ",<br>") as name_s');
		$this->ci->db->where_in('id', explode(',', $aRow['departments']));
		$departments = $this->ci->db->get('tbl_room')->row()->name_s;
	}
	$row[] = $departments;
	$row[] = $aRow['code'];
	$row[] = $aRow['content'];
	$departments = '';


	$row[] = $aRow['name_role_1'];
	$row[] = $aRow['name_role_2'];

	$row[] = !empty($process[0]) ? $process[0]['name'] : '';
	$row[] = !empty($process[0]) ? $process[0]['kpi_plus'] : '';
	$row[] = !empty($process[0]) ? $process[0]['kpi_minus'] : '';

	$process_o = 0;
	if (isset($process[0])) {
		$process_o = $process[0];
		unset($process[0]);
	}
	$process_chil = [];
	if (!empty($process_o)) {
		$this->ci->db->select('tblcategory_tasks_process_child.*, tblroles.code_role as code_role, tblroles.name as name_role');
		$this->ci->db->join('tblroles', 'tblroles.roleid = tblcategory_tasks_process_child.role_processing', 'left');
		$process_chil = $this->ci->db->get_where('tblcategory_tasks_process_child', ['id_category_tasks_process' => $process_o['id']])->result_array();
		$row[] = !empty($process_chil[0]) ? $process_chil[0]['name'] : '';
		$row[] = !empty($process_chil[0]['code_role']) ? $process_chil[0]['code_role'] : '';
		$row[] = !empty($process_chil[0]) ? $process_chil[0]['approval_standards'] : '';
		$row[] = !empty($process_chil[0]) ? $process_chil[0]['completion_control_standards'] : '';
		if (isset($process_chil[0])) {
			unset($process_chil[0]);
		}
	} else {
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
	}
	$type = '';
	if ($aRow['type'] == 1) {
		$type = 'Ngày';
	} else if ($aRow['type'] == 2) {
		$type = 'Tháng';
	} else if ($aRow['type'] == 3) {
		$type = 'Năm';
	}
	$row[] = $aRow['time'];
	$row[] = '<div class="text-center">' . $type . '</div>';
	$row[] = '<div class="text-center">' . formatNumber($aRow['quota']) . '</div>';
	$row[] = !empty($aRow['date_approve']) ? _dt($aRow['date_approve'])  : '';
	$row[] = !empty($aRow['active']) ? ($aRow['active'] == 1 ? 'Đang Sử Dụng' : 'Ngưng Sử Dụng')  : 'Chưa Sử Dụng';
	$option = '';
	$option .= '<a class="btn btn-icon btn-default c_modal" href="' . admin_url('category_tasks/modal/' . $aRow['id']) . '"><i class="fa fa-edit"></i></a>';
	$option .= '<a class="btn btn-icon btn-danger _delete_row" href="' . admin_url('category_tasks/delete/' . $aRow['id']) . '"><i class="fa fa-remove"></i></a>';
	$row[] = $option;
	$row['DT_RowClass'] = 'warning';
	$output['aaData'][] = $row;
	if (!empty($process_o)) {
		if (!empty($process_chil)) {
			foreach ($process_chil as $kk => $vv) {
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
				$row[] = $vv['name'];
				$row[] = $vv['code_role'] ?? '';
				$row[] = $vv['approval_standards'];
				$row[] = $vv['completion_control_standards'];
				$row[] = '';
				$row[] = '';
				$row[] = '';
				$row[] = '';
				$row[] = '';
				$row[] = '';
				$output['aaData'][] = $row;
			}
		}
	}
	if (!empty($process)) {
		foreach ($process as $k => $v) {
			$name_stages = '';
			// if ($v['stages']) {
			// 	$stages = get_table_where('tbl_stages', array('id' => $v['stages']), '', 'row_array');
			// 	$name_stages = '<span style="color:red;">(Công đoạn bàn giao: ' . $stages['code'] . ')</span>';
			// }

			$this->ci->db->select('tblcategory_tasks_process_child.*, tblroles.code_role as code_role, tblroles.name as name_role');
			$this->ci->db->join('tblroles', 'tblroles.roleid = tblcategory_tasks_process_child.role_processing', 'left');
			$process_chil = $this->ci->db->get_where('tblcategory_tasks_process_child', ['id_category_tasks_process' => $v['id']])->result_array();
			$row = [];
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = $v['name'] . ' ' . $name_stages;
			$row[] = $v['kpi_plus'];
			$row[] = $v['kpi_minus'];
			$row[] = !empty($process_chil[0]) ? $process_chil[0]['name'] : '';
			$row[] = !empty($process_chil[0]['code_role']) ? $process_chil[0]['code_role'] : '';
			$row[] = !empty($process_chil[0]) ? $process_chil[0]['approval_standards'] : '';
			$row[] = !empty($process_chil[0]) ? $process_chil[0]['completion_control_standards'] : '';
			if (isset($process_chil[0])) {
				unset($process_chil[0]);
			}

			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$output['aaData'][] = $row;
			if (!empty($process_chil)) {
				foreach ($process_chil as $kk => $vv) {
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
					$row[] = $vv['name'];
					$row[] = $vv['code_role'] ?? '';
					$row[] = $vv['approval_standards'];
					$row[] = $vv['completion_control_standards'];
					$row[] = '';
					$row[] = '';
					$row[] = '';
					$row[] = '';
					$row[] = '';
					$row[] = '';
					$output['aaData'][] = $row;
				}
			}
		}
	}
	$row = [];
	for ($col_idx = 0; $col_idx < 19; $col_idx++) {
		$row[] = '';
	}

	$row['DT_RowClass'] = 'bg-sive';
	$output['aaData'][] = $row;
}
