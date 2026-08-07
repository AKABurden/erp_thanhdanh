<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPrint = has_permission('maintenance', '', 'print');
$hasDelete = has_permission('maintenance', '', 'delete');
$hasViewOwn = has_permission('maintenance', '', 'view_own');
$hasView = has_permission('maintenance', '', 'view');

$aColumns = [
    'tblmaintenance_ticket.id as id',
    'tblmaintenance_ticket.date as date',
    'tblmaintenance_ticket.name as name',
	"(SELECT name FROM tbl_machines WHERE tbl_machines.id = tblmaintenance_ticket.id_machines) as name_machines",
	"(
		SELECT 
			GROUP_CONCAT(CONCAT('- ', tbl_machines_maintenance.name) SEPARATOR '<br>')
		FROM tblmaintenance_ticket_machines
		LEFT JOIN tbl_machines_maintenance ON tbl_machines_maintenance.id = tblmaintenance_ticket_machines.id_maintenance
		WHERE tblmaintenance_ticket_machines.id_maintenance_ticket = tblmaintenance_ticket.id
	 ) as name_maintenance",
	"1",
    'tblmaintenance_ticket.quantity_pcs as quantity_pcs',
    'tblbranch.name as name_branch',
    'tblmaintenance_ticket.note_main as note_main',
];
$sIndexColumn = 'id';
$sTable       = 'tblmaintenance_ticket';
$where        = [];

if(!empty($is_branch)) {
	if (!is_admin()) {
		$list_branch = get_list_branch_staff();
		if (!empty($list_branch)) {
			$where[] = 'AND (tblmaintenance_ticket.id_branch IN (' . $list_branch . '))';
		}
		else
		{
			$where[] = 'AND tblmaintenance_ticket.id = 0';
		}
	}
}

$filter = [];
$join = [];
//$join[] = 'LEFT JOIN tblmaintenance on tblmaintenance.id = tblmaintenance_ticket.id_maintenance_list';
//$join[] = 'LEFT JOIN tbl_machines on tbl_machines.id = tblmaintenance_ticket.id_machines';
//$join[] = 'LEFT JOIN tbl_machines_maintenance on tbl_machines_maintenance.id = tblmaintenance_ticket.id_maintenance';

$join[] = 'LEFT JOIN tblbranch on tblbranch.id = tblmaintenance_ticket.id_branch';

if($this->ci->input->post('date_start')) {
	$where[] = 'AND DATE_FORMAT(tblmaintenance_ticket.date, "%Y-%m-%d") >= "'.to_sql_date($this->ci->input->post('date_start'), true).'"';
}
if($this->ci->input->post('date_end')) {
	$where[] = 'AND DATE_FORMAT(tblmaintenance_ticket.date, "%Y-%m-%d") <= "'.to_sql_date($this->ci->input->post('date_end'), true).'"';
}
if($this->ci->input->post('machines_search')) {
//	$where[] = 'AND tbl_machines.id IN ('.implode(',', $this->ci->input->post('machines_search')).')';
	$where[] = 'AND EXISTS (
					SELECT 1 
					FROM tblmaintenance_ticket_machines 
					WHERE tblmaintenance_ticket_machines.id_maintenance_ticket = tblmaintenance_ticket.id
					AND tblmaintenance_ticket_machines.id_machines IN ('.implode(',', $this->ci->input->post('machines_search')).')
			)';
}
if($this->ci->input->post('maintenance_search')) {
//	$where[] = 'AND tbl_machines_maintenance.id IN ('.implode(',', $this->ci->input->post('maintenance_search')).')';
	$where[] = 'AND EXISTS (
					SELECT 1 
					FROM tblmaintenance_ticket_machines 
					WHERE tblmaintenance_ticket_machines.id_maintenance_ticket = tblmaintenance_ticket.id
					AND tblmaintenance_ticket_machines.id_maintenance IN ('.implode(',', $this->ci->input->post('maintenance_search')).')
			)';
}
if(!$hasView && $hasViewOwn) {
	$where[] = 'AND tblmaintenance_ticket.create_by = "'.get_staff_user_id().'"';
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
	'(SELECT COUNT(tbltasks.id) FROM tbltasks WHERE tbltasks.rel_id = tblmaintenance_ticket.id AND tbltasks.rel_type = "maintenance_ticket") as countTasks',
	'tblmaintenance_ticket.id_maintenance'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
$start = $_POST['start'];
foreach ($rResult as $key => $aRow) {
//	$this->ci->db->where('type != "procedure"');
//	$this->ci->db->where('ischeck', 1);
//	$maintenance_ticket_items = $this->ci->db->get_where('tblmaintenance_ticket_items', ['id_maintenance_ticket' => $aRow['id']])->result_array();
//	$data_Chart = [
//		'0' => 0,
//		'1' => 0,
//		'2' => 0,
//		'3' => 0,
//	];
//	$total_report_items = count($maintenance_ticket_items);
//	if(!empty($maintenance_ticket_items)) {
//		foreach ($maintenance_ticket_items as $key => $value) {
//			if ($value['type'] == 'material') {
//				$data_Chart[0]++;
//			} else if ($value['type'] == 'man') {
//				$data_Chart[1]++;
//			} else if ($value['type'] == 'machine') {
//				$data_Chart[2]++;
//			} else if ($value['type'] == 'method') {
//				$data_Chart[3]++;
//			}
//
//		}
//	}
//	$canvasChart = [
//		'labels' => [
//			'Nguyên phụ liệu (Material) - ('.(!empty($total_report_items) ? round($data_Chart[0] / $total_report_items * 100).'%' : '0%').')',
//			'Nhân lực (Man) - ('.(!empty($total_report_items) ? round($data_Chart[1] / $total_report_items * 100).'%' : '0%').')',
//			'Máy móc (Machine) - ('.(!empty($total_report_items) ? round($data_Chart[2] / $total_report_items * 100).'%' : '0%').')',
//			'Phương pháp (Method)) - ('.(!empty($total_report_items) ? round($data_Chart[3] / $total_report_items * 100).'%' : '0%').')',
//		],
//		'datasets' => [
//			[
//				'data' => $data_Chart,
//				'backgroundColor' => [
//					'#84c529',
//					'#c8ae2e',
//					'#c89264',
//					'#9a2a2a',
//				],
//				'hoverBackgroundColor' => [
//					'#84c529',
//					'#c8ae2e',
//					'#c8ae2e',
//					'#c8ae2e',
//				],
//				'label' => "Thống kế sự cố"
//			]
//		]
//	];
//	$canvasChart = htmlentities(json_encode($canvasChart));


    $row = [];
    $row[] = ($key + $start + 1);
    $row[] = _dt($aRow['date']) . (!empty($aRow['countTasks']) ? '<br/><span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e">'.($aRow['countTasks']).' Phiếu Công Việc</span>' : '');
    $row[] = '<a class="c_modal" href="'.(admin_url('maintenance/view_maintenance_stick/' . $aRow['id'])).'">' . $aRow['name'] .'</a>';
//    $row[] = $aRow['name_machines'];
    $row[] = $aRow['name_machines'];
	if(!empty($aRow['id_maintenance'])) {
		$row[] = 'Tất cả bộ phận';
	}
	else {
		$row[] = $aRow['name_maintenance'];
	}
    $row[] = '<div class="text-center">' . number_format_data($aRow['quantity_pcs']) . '</div>';
    $row[] = '<div class="text-center">' . (!empty($aRow['name_branch']) ? $aRow['name_branch'] : '') . '</div>';

	$this->ci->db->select('
                tblmaintenance_stick_category.*,
                tblcategory_maintenance.name as name
            ', false);
	$this->ci->db->from('tblmaintenance_stick_category');
	$this->ci->db->join('tblcategory_maintenance', 'tblcategory_maintenance.id = tblmaintenance_stick_category.id_category');
	$this->ci->db->where('tblmaintenance_stick_category.maintenance_stick', $aRow['id']);
	$delivery_records_task = $this->ci->db->get()->result_array();
	$qualifiedSuccess = 0;
	$qualifiedfail = 0;
	$str_delivery_records_task = '';
	$str_delivery_records_taskMore = '';
	if (!empty($delivery_records_task)) {
		foreach ($delivery_records_task as $k => $value) {
			$qualified = '';
			if($value['active'] == 1) {
				$qualified = ' - <span class="text-primary">(Đạt)</span>';
				$qualifiedSuccess++;
			}
			else if($value['active'] == 2){
				$qualified = ' - <span class="text-danger">(Chưa đạt)</span>';
				$qualifiedfail++;
			}

			if($k < 5) {
				$str_delivery_records_task .= '<div>- ' . $value['name'] . $qualified .($k == 4 && count($delivery_records_task) > 5 ? ' <a data-toggle="collapse" data-target="#viewMore'.$key.'">...</a>' : '') .  '</div>';
			}
			else {
				$str_delivery_records_taskMore .= '<div>- ' . $value['name'] . $qualified .($k == 4 && count($delivery_records_task) > 5 ? ' ...' : '') .  '</div>';
			}
		}
	}
	$str_delivery_records_taskMore = '<div id="viewMore'.$key.'" class="collapse">' . $str_delivery_records_taskMore . ' <div><a data-toggle="collapse" data-target="#viewMore'.$key.'">(Thu gọn)</a></div></div>';
	$row[] = '<div style="min-width: 200px!important;">' . $str_delivery_records_task . $str_delivery_records_taskMore . '</div>';


	$row[] = '<div style="white-space: pre-line;">' . $aRow['note_main'] . '</div>';
//    $row[] = _dt($aRow['time_of_recording']);
//    $row[] = '<canvas id="canvasChart_'.$aRow['id'].'" data-json="'.$canvasChart.'" class="canvasChart" width="auto" height="150"></canvas>';
	$options = '';
	if($hasPrint) {
		if(!empty($aRow['id_maintenance'])) {
			$options .= '<a class="btn btn-default btn-icon" target="_blank" href="' . (admin_url('maintenance/pdf/' . $aRow['id'])) . '"><i class="fa fa-print" aria-hidden="true"></i></a>';
		}
		else {
			$options .= '<a class="btn btn-default btn-icon" target="_blank" href="' . (admin_url('maintenance/pdf_before/' . $aRow['id'])) . '"><i class="fa fa-print" aria-hidden="true"></i></a>';
		}
	}
	if($hasDelete) {
		$options .= '<a class="btn btn-danger btn-icon remove_maintenance mleft5" data-id="' . $aRow['id'] . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
	}
	$row[] = $options;
    $output['aaData'][] = $row;
}
