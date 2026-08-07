<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPrint = has_permission('production_report', '', 'print');
$hasEdit = has_permission('production_report', '', 'edit');
$hasDelete = has_permission('production_report', '', 'delete');
$hasViewOwn = has_permission('production_report', '', 'view_own');
$hasView = has_permission('production_report', '', 'view');
$this->is_branch = true;
$aColumns = [
    'tblproduction_report.id as id',
    'tblproduction_report.date as date',
    'tblproduction_report.name_report as name_report',
	'tblbranch.name as name_branch',
    'tbldepartments.name as name_departments',
    'tbl_productions_orders.reference_no as reference_no',
    'tbl_orders.reference_no as code_orders',
	'tblsuppliers.company as company',
	'CONCAT(coalesce(group_rl.name, ""), ": ", coalesce(tbl_recommended_list.name, "")) as name_recommended_list',
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

$join[] = 'LEFT JOIN tbldepartments on tbldepartments.departmentid = tblproduction_report.id_departments';
$join[] = 'LEFT JOIN tbl_productions_orders on tbl_productions_orders.id = tblproduction_report.id_production_orders';
//$join[] = 'LEFT JOIN tbl_productions_orders_items on tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id';
//$join[] = 'LEFT JOIN tbl_productions_orders_items_stages on tbl_productions_orders_items_stages.id = tblproduction_report.production_stage';
$join[] = 'LEFT JOIN tbl_stages on tbl_stages.id = tblproduction_report.production_stage';
$join[] = 'LEFT JOIN tbl_orders on tbl_orders.id = tblproduction_report.id_orders';
$join[] = 'LEFT JOIN tblbranch on tblbranch.id = tblproduction_report.id_branch';
$join[] = 'LEFT JOIN tblsuppliers on tblsuppliers.id = tblproduction_report.suppler_id';
$join[] = 'LEFT JOIN tbl_recommended_list group_rl ON group_rl.id = tblproduction_report.recommended_list_group_id';
$join[] = 'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblproduction_report.recommended_list_id';
$join[] = 'LEFT JOIN tbltrouble_violation_point ON tbltrouble_violation_point.id = tblproduction_report.trouble_violation_point_id';
$join[] = 'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tblproduction_report.category_tasks';
$join[] = 'LEFT JOIN tblroles ON tblroles.roleid = tblproduction_report.role_id';

if($this->ci->input->post('code_items')) {
//	$where[] = 'AND tbl_productions_orders_items.items_code LIKE "%'.$this->ci->input->post('code_items').'%"';
	$where[] = 'AND EXISTS (
						SELECT 1 FROM tbl_productions_orders_items 
						WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
						AND tbl_productions_orders_items.items_code LIKE "%'.$this->ci->input->post('code_items').'%"
					)';
}

if($this->ci->input->post('suppler_id')) {
	$where[] = 'AND tblproduction_report.suppler_id = '.$this->ci->input->post('suppler_id');
}
if($this->ci->input->post('role_id')) {
	$where[] = 'AND tblproduction_report.role_id = '.$this->ci->input->post('role_id');
}
if($this->ci->input->post('customer_search')) {
	$where[] = 'AND EXISTS (
		SELECT tbl_orders.*
		FROM tbl_orders
		WHERE tbl_orders.id = tblproduction_report.id_orders
		AND tbl_orders.customer_id = '.$this->ci->input->post('customer_search').')';
}
if($this->ci->input->post('code_production_orders')) {
	$where[] = 'AND tbl_productions_orders.reference_no LIKE "%'.$this->ci->input->post('code_production_orders').'%"';
}

if($this->ci->input->post('date_start')) {
	$where[] = 'AND tblproduction_report.date >= "'.to_sql_date($this->ci->input->post('date_start'), true).'"';
}
if($this->ci->input->post('date_end')) {
	$where[] = 'AND tblproduction_report.date <= "'.to_sql_date($this->ci->input->post('date_end'), true).'"';
}
if(!$hasView && $hasViewOwn) {
	$where[] = 'AND tblproduction_report.create_by = "'.get_staff_user_id().'"';
}
$status_table = $this->ci->input->post('status_table');
if (!empty($status_table) && $status_table != 'all') {
	$where[] = 'AND tblproduction_report.recommended_list_group_id = ' . $status_table . '';
}

if(!empty($this->is_branch)) {
	if (!is_admin()) {
		$list_branch = get_list_branch_staff();
		if (!empty($list_branch)) {
			$where[] = 'AND (tblproduction_report.id_branch IN (' . $list_branch . '))';
		} else {
			$where[] = 'AND tblproduction_report.id = 0';
		}
	}
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
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
	'tbltrouble_violation_point.name as trouble_violation_name'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
foreach ($rResult as $aRow) {
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
	if(!empty($production_report_items)) {
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
			'Nguyên phụ liệu (Material) - ('.(!empty($total_report_items) ? round($data_Chart[0] / $total_report_items * 100).'%' : '0%').')',
			'Nhân lực (Man) - ('.(!empty($total_report_items) ? round($data_Chart[1] / $total_report_items * 100).'%' : '0%').')',
			'Máy móc (Machine) - ('.(!empty($total_report_items) ? round($data_Chart[2] / $total_report_items * 100).'%' : '0%').')',
			'Phương pháp (Method)) - ('.(!empty($total_report_items) ? round($data_Chart[3] / $total_report_items * 100).'%' : '0%').')',
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
    $row[] = $aRow['id'];
	$htmlDate = _dt_new($aRow['date']);
	if(!empty($aRow['countTasks'])) {
		if($aRow['countTasks'] == 1) {
			$htmlDate .= '<br/><span class="inline-block label mleft5 mtop5 pointer" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e" onclick="init_task_modal('.$aRow['ListTasks'].')">'.($aRow['countTasks']).' Phiếu Công Việc</span>';
		}
		else {
			$ListTasks = explode(',', $aRow['ListTasks']);
			foreach($ListTasks as $kL => $vL) {
				$htmlDate .= '<br/><span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e" onclick="init_task_modal('.$vL.')">Phiếu Công Việc '.($kL + 1).'</span>';
			}

		}
	}
	if (!empty($aRow['trouble_violation_name'])) {
		$labelColor = '#dc3545';
		if ($aRow['trouble_violation_name'] == 'Nhắc nhở') {
			$labelColor = '#ffc107';
		} else if ($aRow['trouble_violation_name'] == 'Khiển trách') {
			$labelColor = '#fd7e14';
		} else if ($aRow['trouble_violation_name'] == 'Cảnh báo') {
			$labelColor = '#dc3545';
		}
		$htmlDate .= '<br><span class="inline-block label mleft5 mtop5" style="font-size: 12px;color:'.$labelColor.';border:1px solid '.$labelColor.'">'.($aRow['trouble_violation_name']).' (trừ '.$aRow['trouble_violation_point'].' điểm)</span>';
	}
//    $row[] = _dt($aRow['date']) . (!empty($aRow['countTasks']) ? '<br/><span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e">'.($aRow['countTasks']).' Phiếu Công Việc</span>' : '');
    $row[] = $htmlDate;


	$strBranch = '';
    if (!empty($aRow['name_branch'])){
        $strBranch = '<div style="font-style: italic;font-size: 12px">'.$aRow['name_branch'].'</div>';
    }
    $row[] = '<a class="c_modal" href="'.(admin_url('production_report/modal/' . $aRow['id'])).'">' . $aRow['name_report'] .'</a>';
	$row[] = $strBranch;
    $row[] = $aRow['name_departments'];
    $row[] = '<div class="text-center">' . $aRow['reference_no'] . '</div>';

	$code_orders = '';
	if(!empty($aRow['id_orders'])) {
		$code_orders = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url().'orders/view_order/'.$aRow['id_orders'].'" data-toggle="modal" data-target="#myModal">'.$aRow['code_orders'].'</a>';
	}
    // $row[] = '<div class="text-center">' . $code_orders . '</div>';
	// $row[] = '<div class="">' . $aRow['company'] . '</div>';
	$row[] = !empty($aRow['name_recommended_list']) && $aRow['name_recommended_list'] != ': ' ? ('<div>' . $aRow['name_recommended_list'] . '</div>') : '';
	$this->ci->db->where('id_production_report', $aRow['id']);
	$handler = $this->ci->db->get('tblproduction_report_handler')->result_array();
	$data_handler = '';
	$data_handlerFullname = '';
	if(!empty($handler)) {
		foreach($handler as $key => $value) {
//			$FullName = get_staff_full_name($value['staff_id']);
			$FullName = $this->ci->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
				->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname');
			$data_handler .= staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullName));
			$data_handlerFullname .= $FullName.",\n";
		}
	}
	$row[] = $data_handler . '<span class="hide">' . trim($data_handlerFullname, ",\n").'</span>';

	$this->ci->db->where('id_production_report', $aRow['id']);
	$assigned = $this->ci->db->get('tblproduction_report_assigned')->result_array();
	$data_assigned = '';
	$data_assignedFullname = '';
	if(!empty($assigned)) {
		foreach($assigned as $key => $value) {
//			$FullName = get_staff_full_name($value['staff_id']);
			$FullName = $this->ci->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
				->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname');
			$data_assigned .= staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullName));
			$data_assignedFullname .= $FullName.",\n";
		}
	}
	$row[] = $data_assigned . '<span class="hide">' . trim($data_assignedFullname, ",\n").'</span>';

//	$FullNameCreateBy = get_staff_full_name($aRow['create_by']);
	$FullNameCreateBy = $this->ci->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
		->get_where('tblstaff', ['staffid' => $aRow['create_by']])->row('fullname');
	$row[] = staff_profile_image($aRow['create_by'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullNameCreateBy)).'<span class="hide">'.$FullNameCreateBy.',</span>';





	$row[] = '<div class="text-center">' . number_format_data($aRow['quantity']) . '</div>';
    $row[] = !empty($aRow['stage']) ? $aRow['stage'] : '-';
    $row[] = '<u class="text-danger" '.(!empty($aRow['list_items_name']) ? 'style="white-space: nowrap;"' : '').'>'.$aRow['list_items_name'].'</u>';
    $row[] = ($aRow['described']);
    $row[] = _dt($aRow['time_of_recording']);
    $row[] = '<canvas id="canvasChart_'.$aRow['id'].'" data-json="'.$canvasChart.'" class="canvasChart" width="auto" height="150"></canvas>';
    $row[] = '<div class="text-left">' . ($aRow['name_role']) . '</div>';
    $row[] = '<div class="text-left">' . ($aRow['code_category_task']) . '</div>';
	$options = '';
	if($hasEdit) {
		$options .= '<a class="btn btn-default btn-icon" href="' . (admin_url('production_report/detail/' . $aRow['id'])) . '"><i class="fa fa-edit" aria-hidden="true"></i></a>';
	}
	if($hasPrint) {
		$options .= '<a class="btn btn-default btn-icon mleft5" target="_blank" href="' . (admin_url('production_report/pdf/' . $aRow['id'])) . '"><i class="fa fa-print" aria-hidden="true"></i></a>';
	}
	if($hasDelete) {
		$options .= '<a class="btn btn-danger btn-icon remove_production_report mleft5" data-id="' . $aRow['id'] . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
	}
	$row[] = '<div style="min-width: 100px">' . $options . '</div>';
    $output['aaData'][] = $row;
}
