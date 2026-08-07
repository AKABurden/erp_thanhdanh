<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPrint = has_permission('production_report', '', 'print');
$hasEdit = has_permission('production_report', '', 'edit');
$hasDelete = has_permission('production_report', '', 'delete');
$hasViewOwn = has_permission('production_report', '', 'view_own');
$hasView = has_permission('production_report', '', 'view');
// $dtProcess = get_table_where('tbl_process');
// $this->ci->db->select('tbl_process.*,tbl_setting_production_report.id_role,tbl_setting_production_report.id as idd');
// $this->ci->db->join('tbl_setting_production_report', 'tbl_setting_production_report.id_process = tbl_process.id', 'left');
// $this->ci->db->order_by('tbl_process.id', 'asc');
// $dtProcess = $this->ci->db->get('tbl_process')->result_array();


$this->is_branch = true;
$aColumns = [
	'tblproduction_report.id as id',
	'tblproduction_report.date as date',
	'tblproduction_report.reference_no as reference_no_report',
	'tblproduction_report.name_report as name_report',
	'tblbranch.name as name_branch',
	'tbl_room.name as name_departments',
	'object_type',
	'object_id',
	'tbl_productions_orders.reference_no as reference_no',
	'tbl_orders.reference_no as code_orders',
	'tblsuppliers.company as company',
	'CONCAT(coalesce(group_rl.name, ""), ": ", coalesce(tbl_relate.name, "")) as name_recommended_list',
	// 'CONCAT(coalesce(group_rl.name, ""), ": ", coalesce(tbl_relate.name, "")) as name_recommended_list',
	"1",
	"2",
	"tblproduction_report.create_by as create_by",
	'tblproduction_report.quantity as quantity',
	'tbl_stages.name as stage',
	'(
		SELECT 
		GROUP_CONCAT(distinct CONCAT("- ", items_code) separator "<br/>") 
		FROM tbl_productions_orders_items 
		WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
	) as list_items_name',
	'tblproduction_report.described as described',
	'tblproduction_report.time_of_recording as time_of_recording',
	'tblroles.name as name_role',
	'tblcategory_tasks.code as code_category_task',
];
$sIndexColumn = 'id';
$sTable       = 'tblproduction_report';
$where        = [];

$filter = [];

$join[] = 'LEFT JOIN tbl_room on tbl_room.id = tblproduction_report.id_departments';
$join[] = 'LEFT JOIN tbl_productions_orders on tbl_productions_orders.id = tblproduction_report.id_production_orders';
//$join[] = 'LEFT JOIN tbl_productions_orders_items on tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id';
//$join[] = 'LEFT JOIN tbl_productions_orders_items_stages on tbl_productions_orders_items_stages.id = tblproduction_report.production_stage';
$join[] = 'LEFT JOIN tbl_stages on tbl_stages.id = tblproduction_report.production_stage';
$join[] = 'LEFT JOIN tbl_orders on tbl_orders.id = tblproduction_report.id_orders';
$join[] = 'LEFT JOIN tblbranch on tblbranch.id = tblproduction_report.id_branch';
$join[] = 'LEFT JOIN tblsuppliers on tblsuppliers.id = tblproduction_report.suppler_id';
// $join[] = 'LEFT JOIN tbl_recommended_list group_rl ON group_rl.id = tblproduction_report.recommended_list_group_id';
// $join[] = 'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblproduction_report.recommended_list_id';
$join[] = 'LEFT JOIN tbl_relate group_rl ON group_rl.id = tblproduction_report.recommended_list_group_id';
$join[] = 'LEFT JOIN tbl_relate ON tbl_relate.id = tblproduction_report.recommended_list_id';
$join[] = 'LEFT JOIN tbltrouble_violation_point ON tbltrouble_violation_point.id = tblproduction_report.trouble_violation_point_id';
$join[] = 'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tblproduction_report.category_tasks';
$join[] = 'LEFT JOIN tblroles ON tblroles.roleid = tblproduction_report.role_id';
$join[] = 'LEFT JOIN tbl_violation_group ON tbl_violation_group.id = tblproduction_report.violation_group';

$join[] = 'LEFT JOIN tbltrouble ON tbltrouble.id = tblproduction_report.id_trouble';

if ($this->ci->input->post('code_items')) {
	//	$where[] = 'AND tbl_productions_orders_items.items_code LIKE "%'.$this->ci->input->post('code_items').'%"';
	$where[] = 'AND EXISTS (
						SELECT 1 FROM tbl_productions_orders_items 
						WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
						AND tbl_productions_orders_items.items_code LIKE "%' . $this->ci->input->post('code_items') . '%"
					)';
}

if ($this->ci->input->post('suppler_id')) {
	$where[] = 'AND tblproduction_report.suppler_id = ' . $this->ci->input->post('suppler_id');
}
if ($this->ci->input->post('role_id')) {
	$where[] = 'AND tblproduction_report.role_id = ' . $this->ci->input->post('role_id');
}
if ($this->ci->input->post('customer_search')) {
	$where[] = 'AND EXISTS (
		SELECT tbl_orders.*
		FROM tbl_orders
		WHERE tbl_orders.id = tblproduction_report.id_orders
		AND tbl_orders.customer_id = ' . $this->ci->input->post('customer_search') . ')';
}
if ($this->ci->input->post('code_production_orders')) {
	$where[] = 'AND tbl_productions_orders.reference_no LIKE "%' . $this->ci->input->post('code_production_orders') . '%"';
}

if ($this->ci->input->post('date_start')) {
	$where[] = 'AND tblproduction_report.date >= "' . to_sql_date($this->ci->input->post('date_start'), true) . '"';
}
if ($this->ci->input->post('date_end')) {
	$where[] = 'AND tblproduction_report.date <= "' . to_sql_date($this->ci->input->post('date_end'), true) . '"';
}
if (!$hasView && $hasViewOwn) {
	$where[] = 'AND tblproduction_report.create_by = "' . get_staff_user_id() . '"';
}
$status_table = $this->ci->input->post('status_table');
if (!empty($status_table) && $status_table != 'all') {
	$where[] = 'AND tblproduction_report.recommended_list_group_id = ' . $status_table . '';
}

$_room_id = $this->ci->input->post('_room_id');
if ($_room_id) {
	$where[] = ' AND EXISTS (
		SELECT 1
		FROM tblstaff_departments
		INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
		WHERE tblstaff_departments.staffid = tblproduction_report.staff_manage AND tbldepartments.room_id = ' . $_room_id . '
	)';
}

if (!empty($this->is_branch)) {
	if (!is_admin()) {
		$list_branch = get_list_branch_staff();
		if (!empty($list_branch)) {
			$where[] = 'AND (tblproduction_report.id_branch IN (' . $list_branch . '))';
		} else {
			$where[] = 'AND tblproduction_report.id = 0';
		}
	}
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	//	'tbl_productions_orders_items.items_code as items_code',
	'(SELECT COUNT(tbltasks.id) FROM tbltasks WHERE tbltasks.rel_id = tblproduction_report.id AND tbltasks.rel_type = "production_report") as countTasks',
	'(
		SELECT GROUP_CONCAT(tbltasks.id)
		FROM tbltasks 
		WHERE tbltasks.rel_id = tblproduction_report.id AND tbltasks.rel_type = "production_report"
	) as ListTasks',
	'tblproduction_report.id_orders',
	'tblbranch.name as name_branch',
	'tblproduction_report.trouble_violation_point as trouble_violation_point',
	'tbltrouble_violation_point.name as trouble_violation_name',
	'tblproduction_report.staff_evaluate as staff_evaluate',
	'tblproduction_report.type_report as type_report',
	'tblproduction_report.violate as violate',
	'tblproduction_report.category_recommended_id',
	'tblcategory_tasks.content as name_category_task',
	'tblproduction_report.id_internal_proposal as id_internal_proposal',
	'tblproduction_report.id_internal_proposal_process as id_internal_proposal_process',
	'tblproduction_report.id_internal_proposal_process_child as id_internal_proposal_process_child',
	'tbl_violation_group.code as code_violation_group',
	'tbl_violation_group.name as name_violation_group',
	'tbl_violation_group.detail as detail_violation_group',
    'tbltrouble.code as code_trouble',
    'tbltrouble.name as name_trouble',
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);


foreach ($rResult as $aRow) {
	$this->ci->db->select('tbl_process_production_report.*,tbl_role_production_report.id_role,tbl_role_production_report.id as idd,tbl_process_production_report.process_id as id');
	$this->ci->db->join('tbl_role_production_report', 'tbl_role_production_report.id_process = tbl_process_production_report.process_id AND tbl_role_production_report.id_production_report = tbl_process_production_report.production_report_id', 'left');
	$this->ci->db->where('tbl_process_production_report.production_report_id', $aRow['id']);
	$this->ci->db->group_by('tbl_process_production_report.id');
	$this->ci->db->order_by('tbl_process_production_report.process_id', 'asc');
	$dtProcess = $this->ci->db->get('tbl_process_production_report')->result_array();
	$color = '';
	if ($aRow['date'] < date('Y-m-d 00:0:00')) {
		$color = 'color:red;';
	}
	$this->ci->db->where('type != "procedure"');
	$this->ci->db->where('ischeck', 1);
	$production_report_items = $this->ci->db->get_where('tblproduction_report_items', ['id_production_report' => $aRow['id']])->result_array();
	$data_Chart = [
		'0' => 0,
		'1' => 0,
		'2' => 0,
		'3' => 0,
	];
	$total_report_items = count($production_report_items);
	if (!empty($production_report_items)) {
		foreach ($production_report_items as $key => $value) {
			if ($value['type'] == 'material') {
				$data_Chart[0]++;
			} else if ($value['type'] == 'man') {
				$data_Chart[1]++;
			} else if ($value['type'] == 'machine') {
				$data_Chart[2]++;
			} else if ($value['type'] == 'method') {
				$data_Chart[3]++;
			}
		}
	}
	$canvasChart = [
		'labels' => [
			'Nguyên phụ liệu (Material) - (' . (!empty($total_report_items) ? round($data_Chart[0] / $total_report_items * 100) . '%' : '0%') . ')',
			'Nhân lực (Man) - (' . (!empty($total_report_items) ? round($data_Chart[1] / $total_report_items * 100) . '%' : '0%') . ')',
			'Máy móc (Machine) - (' . (!empty($total_report_items) ? round($data_Chart[2] / $total_report_items * 100) . '%' : '0%') . ')',
			'Phương pháp (Method)) - (' . (!empty($total_report_items) ? round($data_Chart[3] / $total_report_items * 100) . '%' : '0%') . ')',
		],
		'datasets' => [
			[
				'data' => $data_Chart,
				'backgroundColor' => [
					'#84c529',
					'#c8ae2e',
					'#c89264',
					'#9a2a2a',
				],
				'hoverBackgroundColor' => [
					'#84c529',
					'#c8ae2e',
					'#c8ae2e',
					'#c8ae2e',
				],
				'label' => "Thống kế sự cố"
			]
		]
	];
	$canvasChart = htmlentities(json_encode($canvasChart));


	$row = [];
	$row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" id="rows-child-' . $aRow['id'] . '" class="rows-child fa fa-caret-right"></a></div>';
	$htmlDate = _dt_new($aRow['date']);
	if (!empty($aRow['countTasks'])) {
		if ($aRow['countTasks'] == 1) {
			$htmlDate .= '<br/><span class="inline-block label mleft5 mtop5 pointer" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e" onclick="init_task_modal(' . $aRow['ListTasks'] . ')">' . ($aRow['countTasks']) . ' Phiếu Công Việc</span>';
		} else {
			$ListTasks = explode(',', $aRow['ListTasks']);
			foreach ($ListTasks as $kL => $vL) {
				$htmlDate .= '<br/><span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e" onclick="init_task_modal(' . $vL . ')">Phiếu Công Việc ' . ($kL + 1) . '</span>';
			}
		}
	}

	$violate = $aRow['violate'];
	if (!empty($aRow['trouble_violation_name'])) {
		$labelColor = '#dc3545';
		if ($aRow['trouble_violation_name'] == 'Nhắc nhở') {
			$labelColor = '#ffc107';
		} else if ($aRow['trouble_violation_name'] == 'Khiển trách') {
			$labelColor = '#fd7e14';
		} else if ($aRow['trouble_violation_name'] == 'Cảnh báo') {
			$labelColor = '#dc3545';
		}

		if ($aRow['violate']) {
			$htmlDate .= '<br><span class="inline-block label mleft5 mtop5" style="font-size: 12px;color:' . $labelColor . ';border:1px solid ' . $labelColor . '">' . ($aRow['trouble_violation_name']) . ' (trừ ' . $aRow['trouble_violation_point'] . ' điểm)</span>';
		}
	}
	//    $row[] = _dt($aRow['date']) . (!empty($aRow['countTasks']) ? '<br/><span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e">'.($aRow['countTasks']).' Phiếu Công Việc</span>' : '');
	$row[] = $htmlDate;

	$type_report = $aRow['type_report'];
	$strTypeReport = '';
	if ($type_report == 1) {
		$strTypeReport = '<div class="mtop5"><div class="label btn-success">Báo cáo không phù hợp</div></div>';
	} else if ($type_report == 2) {
		$strTypeReport = '<div class="mtop5"><label class="label btn-primary">Báo cáo vượt</label></div>';
	} else if ($type_report == 3) {
		$strTypeReport = '<div class="mtop5"><label class="label btn-warning">Báo cáo cải tiến</label></div>';
	}	else if ($type_report == 4) {
		$strTypeReport = '<div class="mtop5"><label class="label btn-warning">Báo cáo vi phạm</label></div>';
	}

	$strBranch = '';
	if (!empty($aRow['name_branch'])) {
		$strBranch = '<div style="font-style: italic;font-size: 12px">' . $aRow['name_branch'] . '</div>';
	}
	$htmls = '';
	if ($aRow['id_internal_proposal']) {
		$internal_proposal = get_table_where('tblinternal_proposal', array('id' => $aRow['id_internal_proposal']), '', 'row_array');
		$htmls .= '<div class="mtop5"><div class="label btn-success">' . $internal_proposal['code'] . '</div></div>';
		if ($aRow['id_internal_proposal_process']) {
			$internal_proposal_process = get_table_where('tbl_internal_proposal_process', array('id' => $aRow['id_internal_proposal_process']), '', 'row_array');
			$htmls .= $internal_proposal_process['name'];
		}
	}
	$row[] = $aRow['reference_no_report'] . '<br>' . $htmls;
	$row[] = '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $aRow['id'])) . '">' . $aRow['name_report'] . '</a>' . $strTypeReport;
	$row[] = $strBranch;
	$row[] = $aRow['name_departments'];
//	if (!empty($aRow['object_type'])) {
//		$this->ci->db->like('id', $aRow['category_recommended_id']);
//		$object_type = $this->ci->db->get('tbl_category_recommended')->row_array();
//
//		if (!empty($object_type)) {
//			$row[] = $object_type['name'];
//			// $row[] = $aRow['object_id'];
//			$code_Suggest = '';
//			$dtSuggest = get_table_where($object_type['name_table'], ['id' => $aRow['object_id']], '', 'row_array');
//
//			if (!empty($dtSuggest)) {
//				if (!empty($dtSuggest['reference_no'])) {
//					$code_Suggest = $dtSuggest['reference_no'];
//				}
//				if (!empty($dtSuggest['code'])) {
//					$code_Suggest = $dtSuggest['code'];
//				}
//				$link = '';
//				$name_table = explode('tbl_', $object_type['name_table']);
//				if (count($name_table) > 1) {
//					$link = $name_table[1];
//				} else {
//					$name_table_v2 = explode('tbl', $object_type['name_table']);
//					if (count($name_table_v2) > 1) {
//						$link = $name_table_v2[1];
//					}
//				}
//				$html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
//				// if ($aRow['type_kpi'] == 1) {
//				// 	$html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
//				// }
//				// $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
//				$code_Suggest = $html;
//			}
//			$row[] = $code_Suggest;
//		} else {
//			$row[] = '';
//			$row[] = '';
//		}
//	} else {
//		$row[] = '';
//		$row[] = '';
//	}

    $row[] = $aRow['code_trouble'];
    $row[] = $aRow['name_trouble'];
	$row[] = '<div class="text-center">' . $aRow['reference_no'] . '</div>';

	$code_orders = '';
	if (!empty($aRow['id_orders'])) {
		$code_orders = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url() . 'orders/view_order/' . $aRow['id_orders'] . '" data-toggle="modal" data-target="#myModal">' . $aRow['code_orders'] . '</a>';
	}
	// $row[] = '<div class="text-center">' . $code_orders . '</div>';
	// $row[] = '<div class="">' . $aRow['company'] . '</div>';
	$row[] = !empty($aRow['name_recommended_list']) && $aRow['name_recommended_list'] != ': ' ? ('<div>' . $aRow['name_recommended_list'] . '</div>') : '';
	$this->ci->db->where('id_production_report', $aRow['id']);
	$handler = $this->ci->db->get('tblproduction_report_handler')->result_array();
	$data_handler = '';
	$data_handlerFullname = '';
	if (!empty($handler)) {
		foreach ($handler as $key => $value) {
			//			$FullName = get_staff_full_name($value['staff_id']);
			$FullName = $this->ci->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
				->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname');
			$data_handler .= staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullName));
			$data_handlerFullname .= $FullName . ",\n";
		}
	}
	$row[] = $data_handler . '<span class="hide">' . trim($data_handlerFullname, ",\n") . '</span>';

	$this->ci->db->where('id_production_report', $aRow['id']);
	$assigned = $this->ci->db->get('tblproduction_report_assigned')->result_array();
	$data_assigned = '';
	$data_assignedFullname = '';
	if (!empty($assigned)) {
		foreach ($assigned as $key => $value) {
			//			$FullName = get_staff_full_name($value['staff_id']);
			$FullName = $this->ci->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
				->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname');
			$data_assigned .= staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullName));
			$data_assignedFullname .= $FullName . ",\n";
		}
	}
	$row[] = $data_assigned . '<span class="hide">' . trim($data_assignedFullname, ",\n") . '</span>';

	//	$FullNameCreateBy = get_staff_full_name($aRow['create_by']);
	$FullNameCreateBy = $this->ci->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
		->get_where('tblstaff', ['staffid' => $aRow['create_by']])->row('fullname');
	$row[] = staff_profile_image($aRow['create_by'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullNameCreateBy)) . '<span class="hide">' . $FullNameCreateBy . ',</span>';

	if (!empty($aRow['staff_evaluate'])) {
		$row[] = staff_profile_image(
			$aRow['staff_evaluate'],
			array('staff-profile-image-small mright5'),
			'small',
			array(
				'data-toggle' => 'tooltip',
				'data-title' => get_staff_full_name($aRow['staff_evaluate'])
			)
		) . '<span class="hide">' . get_staff_full_name($aRow['staff_evaluate']) . ',</span>';
	} else {
		$row[] = '';
	}




	$row[] = '<div class="text-center">' . number_format_data($aRow['quantity']) . '</div>';
	$row[] = !empty($aRow['stage']) ? $aRow['stage'] : '-';
	$row[] = '<u class="text-danger" ' . (!empty($aRow['list_items_name']) ? 'style="white-space: nowrap;"' : '') . '>' . $aRow['list_items_name'] . '</u>';
	$row[] = ($aRow['described']);
	$row[] = _dt($aRow['time_of_recording']);
	$row[] = '<canvas id="canvasChart_' . $aRow['id'] . '" data-json="' . $canvasChart . '" class="canvasChart" width="auto" height="150"></canvas>';
	$row[] = '<div class="text-left">' . ($aRow['name_role']) . '</div>';
	$row[] = '<div class="text-left">' . ($aRow['code_category_task']) . ' - ' . ($aRow['name_category_task'] ?? '') . '</div>';
	$row[] = '<div class="text-left">' . ($aRow['code_violation_group']) . ' - '. $aRow['name_violation_group']. ' - ' . ($aRow['detail_violation_group'] ?? '') . '</div>';
	$options = '';
	if ($hasEdit) {
		$options .= '<a class="btn btn-info" onclick="updatedStatusAll(' . $aRow['id'] . ')">Duyệt all</a>';
		$options .= '<a class="btn btn-default btn-icon" href="' . (admin_url('production_report/detail/' . $aRow['id'])) . '"><i class="fa fa-edit" aria-hidden="true"></i></a>';
	}
	if ($hasPrint) {
		$options .= '<a class="btn btn-default btn-icon mleft5" target="_blank" href="' . (admin_url('production_report/pdf/' . $aRow['id'])) . '"><i class="fa fa-print" aria-hidden="true"></i></a>';
	}
	if ($hasDelete) {
		$options .= '<a class="btn btn-danger btn-icon remove_production_report mleft5" data-id="' . $aRow['id'] . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
	}
	$row[] = '<div style="min-width: 200px">' . $options . '</div>';
	$li = '';
	$li_img = '';
	if (!empty($dtProcess)) {
		$check_active = 0;
		foreach ($dtProcess as $kk => $vv) {
			// $dtProcessProduction = get_table_where('tbl_process_production_report', ['production_report_id' => $aRow['id'], 'process_id' => $vv['id']], '', 'row_array');
			if ($vv['staff_process'] == 0) {
				$check_active = 1;
			}
		}
		if ($check_active == 0) {
			$color = '';
		}
		foreach ($dtProcess as $kk => $vv) {
			// $dtProcessProduction = get_table_where('tbl_process_production_report', ['production_report_id' => $aRow['id'], 'process_id' => $vv['id']], '', 'row_array');
			$date_status = '';
			if (!empty($vv['date_process'])) {
				$date_status = explode(' ', $vv['date_process'])[0] . ' 00:00:00';
			}
			$imageDefault_in = '';
			$border = '';
			if ($vv['staff_process'] > 0) {
				$border = 'border: 1px solid #00ff50;';
			}
			// if ($aRow['id'] > 2747) {
			if (!empty($vv['id_role'])) {
				$this->ci->db->where('role', $vv['id_role']);
				$check_role = $this->ci->db->get('tblstaff')->row();

				$this->ci->db->where('roleid', $vv['id_role']);
				$role = $this->ci->db->get('tblroles')->row();
				$code_role = '';
				if (!empty($role)) {
					$code_role = $role->code_role;
				}
				if (!empty($check_role)) {
					$FullName = get_staff_full_name($check_role->staffid);
					$imageDefault_in = '<div>' . staff_profile_image(
						$check_role->staffid,
						array('staff-profile-image-small mright5'),
						'small',
						array('data-toggle' => 'tooltip', 'data-title' => $FullName, 'style' => $border . 'width: 18px;height: 18px;')
					) . $FullName . ' (' . $code_role . ')</div>';
				} else {
					$imageDefault_in = '<div>' . staff_profile_image(
						0,
						array('staff-profile-image-small mright5'),
						'small',
						array('style' => $border . 'width: 18px;height: 18px;')
					) . '</div>';
				}
			} else {
				if (($vv['process_id'] <= 2)) {
					$FullName = get_staff_full_name($aRow['create_by']);
					$imageDefault_in = '<div>' . staff_profile_image(
						$aRow['create_by'],
						array('staff-profile-image-small mright5'),
						'small',
						array('data-toggle' => 'tooltip', 'data-title' => $FullName, 'style' => $border . 'width: 18px;height: 18px;')
					) . $FullName . '</div>';
				} else {

					$imageDefault_in = staff_profile_image(
						0,
						array('staff-profile-image-small mright5'),
						'small',
						array('style' => $border . 'width: 18px;height: 18px;')
					);
				}
			}
			// } else {
			// 	$FullName = get_staff_full_name($aRow['create_by']);
			// 	$imageDefault_in = '<div>' . staff_profile_image(
			// 		$aRow['create_by'],
			// 		array('staff-profile-image-small mright5'),
			// 		'small',
			// 		array('data-toggle' => 'tooltip', 'data-title' => $FullName, 'style' => $border . 'width: 18px;height: 18px;')
			// 	) . $FullName . '</div>';
			// }
			$imageDefault = staff_profile_image(
				0,
				array('staff-profile-image-small'),
				'small',
				array('style' => 'border: 1px solid #00ff50;width: 18px;height: 18px;')
			);

			$htmlImg = (!empty($vv) && $vv['staff_process'] > 0 ? staff_profile_image(
				$vv['staff_process'],
				array('staff-profile-image-small'),
				'small',
				array('style' => 'border: 1px solid #00ff50;width: 25px;height: 25px;')
			) : '');

			if (!empty($vv) && $vv['staff_process'] > 0) {
				$color = '';
				if ($aRow['date'] . ' 23:59:59' < $date_status) {
					$color = 'color:#ff9b00e3;';
				}
				$strStaffActive = '';
				if ($vv['id'] > 2) {
					$strStaffActive = '<div style="border: 1px solid blue;border-radius: 5px;font-size: 10px;color: blue;width:90px;margin-left:10px"><a style="color: blue" class="c_modal" href="' . admin_url('production_report/inspection_criteria/' . $aRow['id'] . '/' . $vv['id'] . '/' . $vv['id']) . '">Xem tiêu chí</a></div>';
				}
				$htmlProcess = '<div class="text-center" style="font-size: 18px; cursor: pointer;"><div style="font-style: italic;font-size: 11px;color: black">' . _dt($vv['date_process']) . '</div><i style="' . $color . '" class="wrap-icon-check text-danger fa fa-remove" data-toggle="tooltip" data-placement="bottom" title="" onclick="updatedStatus(' . $aRow['id'] . ',' . $vv['id'] . ',0)" data-original-title="' . _l('tnh_un_agree') . '"></i></div>' . $strStaffActive;
			} else {
				if ($aRow['date'] < date('Y-m-d 00:0:00')) {
					$color = 'color:red;';
				}
				// $htmlProcess = '<div class="text-center" style="font-size: 18px; cursor: pointer;"><i class="wrap-icon-check fa fa-check-circle-o" data-toggle="tooltip" data-placement="bottom" title="" onclick="updatedStatus(' . $aRow['id'] . ',' . $vv['id'] . ',1)" data-original-title="' . _l('approve') . '"></i></div>';


				$htmlProcess = '<div class="text-center" style="font-size: 18px; cursor: pointer;"><a style="font-size:15px;" data-toggle="tooltip" title="' . lang('Duyệt tiêu chí') . '" class="c_modal" href="' . admin_url('production_report/inspection_criteria/' . $aRow['id'] . '/' . $vv['id']) . '"><i style="' . $color . '" class="wrap-icon-check fa fa-check-circle-o"></i></a></div>';
			}
			$htmlActive = (!empty($vv) && $vv['staff_process'] > 0) ? 'active' : '';
			$li_img .= '<li class="pointer hoang" style="list-style-type: none;width: 110px;float: left;font-size: 12px;position: relative;text-align: center;color: #7d7d7d;z-index: 0;font-size: 9px;">
                                        ' . $imageDefault_in . '
                                    </li>';
			$li .= '<li class="' . $htmlActive . ' width-progressbar">
                    <div class="text-primary bold" style="' . $color . 'font-size: 11px;">' . $vv['name'] . '</div>
                    ' . $htmlImg . '
                    <div>' . $htmlProcess . '</div>
                </li>';
		}
	}
	$row[] = '<div  class="display: table; justify-content: center;">
		<ul class="progressbar" style="display: flex; flex-direction: row;justify-content: start;">
			 ' . $li_img . '
        </ul>
        <ul class="progressbar" style="display: flex; flex-direction: row;justify-content: start;">
             ' . $li . '
        </ul>
    </div>';
	$output['aaData'][] = $row;
}
