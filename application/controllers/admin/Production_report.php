<?php
defined('BASEPATH') or exit('No direct script access allowed');

//cong
class Production_report extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->view = has_permission('production_report', '', 'view');
		$this->view_own = has_permission('production_report', '', 'view_own');
		$this->create = has_permission('production_report', '', 'create');
		$this->edit = has_permission('production_report', '', 'edit');
		$this->delete = has_permission('production_report', '', 'delete');
		$this->print = has_permission('production_report', '', 'print');
		$this->load->model('recommended_list_model');
		$this->load->model('kpi_model');
		$this->is_branch = true;
		$this->hide_departments = true;
		$this->hide_role = true;
		$this->upload_path = get_upload_path_by_type('production_report');
	}
	public function incident_tracking_bk()
	{
		if (!$this->view && !$this->view_own) {
			access_denied();
		}
		$data['suppler'] = $this->db->get_where('tblsuppliers', ['active' => 1])->result_array();
		$data['recommended_list'] = $this->recommended_list_model->getRecommendedListParent([0]);
		// $data['arrDepartment'] = $this->kpi_model->getDepartments();
		// $data['arrRole'] = get_table_where('tblroles', ['active_role' => 1], '', 'result_array', '', 'roleid, name');
		$data['arrRole'] = [];
		$this->get_parent_role(0, $data['arrRole'], [], 0);
		$data['title'] = _l('Phiếu theo dõi sự cố');
		$this->load->view('admin/production_report/incident_tracking', $data);
	}

	public function incident_tracking()
	{
		if (!$this->view && !$this->view_own) {
			access_denied();
		}
		$data['suppler'] = $this->db->get_where('tblsuppliers', ['active' => 1])->result_array();
		$data['recommended_list'] = $this->recommended_list_model->getRecommendedListParent([0]);
		// $data['arrDepartment'] = $this->kpi_model->getDepartments();
		// $data['arrRole'] = get_table_where('tblroles', ['active_role' => 1], '', 'result_array', '', 'roleid, name');
		$data['arrRole'] = [];
		$this->get_parent_role(0, $data['arrRole'], [], 0);
		$data['title'] = _l('Phiếu theo dõi sự cố');
		$data['room'] = $this->getRomList();
		$this->load->view('admin/production_report/incident_tracking_new', $data);
	}

	public function get_incident_tracking_table_bk()
	{
		$filter = $this->input->get('filter');
		$arr_recommended_list = $this->recommended_list_model->getRecommendedListParent([0], 1);
		$col_num = count($arr_recommended_list) + 1;
		$tableData = $this->getRoleList($filter);
		$tableBody = '';
		$department_id = '';
		foreach ($tableData as $key => $value) {
			if ($department_id != $value['department_id']) {
				$department_id = $value['department_id'];
				$tableBody .= '<tr>';
				$tableBody .= '<td colspan="' . $col_num . '" style="font-weight: bold">' . $value['department_name'] . '</td>';
				$tableBody .= '</tr>';
			}
			if ($value['level'] == 2) {
				$levelMargin = '&nbsp;&nbsp;&nbsp;&nbsp;';
				$classStyle = 'font-style: italic;';
			} else {
				$levelMargin = '';
				$classStyle = '';
			}
			$tableBody .= '<tr>';
			$tableBody .= '<td class="nowrap" style=""><div style="width: 150px; ' . $classStyle . '" >' . $levelMargin . $value['role_name'] . '</div></td>';
			foreach ($arr_recommended_list as $recommended_list_key => $recommended_list_value) {
				$production_report_count = $this->count_incident_tracking(
					$recommended_list_value['id'],
					$value['role_id'],
					$filter
				);
				$params = $filter;
				$params['recommend_id'] = $recommended_list_value['id'];
				$params['role_id'] = $value['role_id'];
				// var_dump($params);die;
				// $params = implode('&', $params);
				$params = http_build_query($params);
				$production_report_link = '<a href="' . admin_url('production_report') . '?' . $params . '" target="_blank">' . $production_report_count . '</a>';
				$tableBody .= '<td style="font-size: 1.7rem" class="center">' . $production_report_link . '</td>';
			}
			$tableBody .= '</tr>';
		}
		$data['tableBody'] = $tableBody;
		$data['recommended_list'] = $arr_recommended_list;
		$this->load->view('admin/production_report/incident_tracking_table', $data);
	}

	public function getRomList($filter = [])
	{
		$result = [];
		$this->db->select('tbl_room.id, tbl_room.code, tbl_room.name', false);
		$this->db->from('tbl_room');
		if (!empty($filter['room_id'])) {
			$this->db->where('tbl_room.id', $filter['room_id']);
		}
		$result = $this->db->get()->result_array();
		return $result;
	}

	public function get_incident_tracking_table()
	{
		$filter = $this->input->get('filter');
		$arr_recommended_list = $this->recommended_list_model->getRecommendedListParent([0], 1);
		// print_arrays($arr_recommended_list);

		$col_num = count($arr_recommended_list) + 1;
		// $tableData = $this->getRoleList($filter);
		$tableData = $this->getRomList($filter);
		$tableBody = '';
		$department_id = '';
		foreach ($tableData as $key => $value) {
			// if ($department_id != $value['department_id']) {
			// 	$department_id = $value['department_id'];
			// 	$tableBody .= '<tr>';
			// 	$tableBody .= '<td colspan="' . $col_num . '" style="font-weight: bold">' . $value['department_name'] . '</td>';
			// 	$tableBody .= '</tr>';
			// }
			// if ($value['level'] == 2) {
			// 	$levelMargin = '&nbsp;&nbsp;&nbsp;&nbsp;';
			// 	$classStyle = 'font-style: italic;';
			// } else {
			// 	$levelMargin = '';
			// 	$classStyle = '';
			// }

			$levelMargin = '';
			$classStyle = '';
			$tableBody .= '<tr>';
			$tableBody .= '<td class="nowrap" style=""><div style="width: 150px; ' . $classStyle . '" >' . $levelMargin . $value['name'] . '</div></td>';
			foreach ($arr_recommended_list as $recommended_list_key => $recommended_list_value) {
				$production_report_count = $this->count_incident_tracking_new(
					$recommended_list_value['id'],
					$value['id'],
					$filter
				);
				// print_arrays($this->db->last_query());

				$params = $filter;
				$params['recommend_id'] = $recommended_list_value['id'];
				$params['rom_id'] = $value['id'];

				// $params = implode('&', $params);
				$params = http_build_query($params);
				$production_report_link = '<a href="' . admin_url('production_report') . '?' . $params . '" target="_blank">' . $production_report_count . '</a>';
				$tableBody .= '<td style="font-size: 1.7rem" class="center">' . $production_report_link . '</td>';
			}
			$tableBody .= '</tr>';
		}
		$data['tableBody'] = $tableBody;
		$data['recommended_list'] = $arr_recommended_list;
		$this->load->view('admin/production_report/incident_tracking_table_new', $data);
	}

	public function count_incident_tracking_new($recommended_id, $room_id, $filter = [])
	{
		$this->db->select('COUNT(tblproduction_report.id) as count_production_report');
		$this->db->join('tblstaff', 'tblstaff.staffid = tblproduction_report.staff_manage');
		$this->db->where('tblproduction_report.recommended_list_group_id', $recommended_id);
		$this->db->where(' EXISTS (
				SELECT 1
				FROM tblstaff_departments
				INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
				WHERE tblstaff_departments.staffid = tblstaff.staffid AND tbldepartments.room_id = ' . $room_id . '
			)', false, false);

		if (!empty($filter['date_start'])) {
			$filter['date_start'] = to_sql_date($filter['date_start']);
			$this->db->where('tblproduction_report.date >= ', $filter['date_start'] . ' 00:00:00');
		}
		if (!empty($filter['date_end'])) {
			$filter['date_end'] = to_sql_date($filter['date_end']);
			$this->db->where('tblproduction_report.date <= ', $filter['date_end'] . ' 23:59:59');
		}
		if (!empty($filter['customer'])) {
			$this->db->where('EXISTS (
					SELECT tbl_orders.*
					FROM tbl_orders
					WHERE tbl_orders.id = tblproduction_report.id_orders
					AND tbl_orders.customer_id = ' . $filter['customer'] . '
			)');
		}
		$result = $this->db->get('tblproduction_report')->row();
		$result = !empty($result->count_production_report) ? $result->count_production_report : '';
		return $result;
	}

	public function count_incident_tracking($recommended_id, $role_id, $filter = [])
	{
		$this->db->select('COUNT(tblproduction_report.id) as count_production_report');
		$this->db->where('tblproduction_report.role_id', $role_id);
		$this->db->where('tblproduction_report.recommended_list_group_id', $recommended_id);
		if (!empty($filter['date_start'])) {
			$filter['date_start'] = to_sql_date($filter['date_start']);
			$this->db->where('tblproduction_report.date >= ', $filter['date_start']);
		}
		if (!empty($filter['date_end'])) {
			$filter['date_end'] = to_sql_date($filter['date_end']);
			$this->db->where('tblproduction_report.date <= ', $filter['date_end'] . ' 23:59:59');
		}
		if (!empty($filter['customer'])) {
			$this->db->where('EXISTS (
				SELECT tbl_orders.*
				FROM tbl_orders
				WHERE tbl_orders.id = tblproduction_report.id_orders
				AND tbl_orders.customer_id = ' . $filter['customer'] . '
			)');
		}
		$result = $this->db->get('tblproduction_report')->row();
		$result = !empty($result->count_production_report) ? $result->count_production_report : '';
		return $result;
	}

	public function getRoleList($filter = [])
	{
		$this->db->select('
			tbldepartments.departmentid as department_id,
			tbldepartments.name as department_name,
			tblroles.roleid as role_id,
			tblroles.name as role_name,
			1 as level,
		');
		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
		$this->db->group_by('tblroles.roleid');
		$this->db->order_by('tbldepartments.departmentid asc, tblroles.roleid asc');
		$this->db->where('tblroles.active_role', 1);
		$this->db->where('tblroles.roles_parent', 0);
		$this->db->where('tblroles.type', 0);
		if (!empty($filter['role_id'])) {
			$this->db->group_start();
			$this->db->where('EXISTS (
				SELECT tb_role_chile.roleid
				FROM tblroles tb_role_chile
				WHERE tb_role_chile.roles_parent = tblroles.roleid AND tb_role_chile.roleid = ' . $filter['role_id'] . '
			)');
			$this->db->or_where('tblroles.roleid', $filter['role_id']);
			$this->db->group_end();
		}
		if (!empty($filter['department'])) {
			$this->db->where('tbldepartments.departmentid', $filter['department']);
		}
		$resultTemp = $this->db->get('tblroles')->result_array();
		$result = [];
		foreach ($resultTemp as $key => $value) {
			$result[] = $value;
			$this->db->select('
				tbldepartments.departmentid as department_id,
				tbldepartments.name as department_name,
				tblroles.roleid as role_id,
				tblroles.name as role_name,
				2 as level,
			');
			$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
			$this->db->group_by('tblroles.roleid');
			$this->db->order_by('tbldepartments.departmentid asc, tblroles.roleid asc');
			$this->db->where('tblroles.active_role', 1);
			$this->db->where('tblroles.roles_parent', $value['role_id']);
			$this->db->where('tblroles.type', 0);
			if (!empty($filter['role_id'])) {
				$this->db->where('tblroles.roleid', $filter['role_id']);
			}
			$resultChild = $this->db->get('tblroles')->result_array();
			if (!empty($resultChild)) {
				$result = array_merge($result, $resultChild);
			}
		}
		return $result;
	}

	public function index()
	{
		if (!$this->view && !$this->view_own) {
			access_denied();
		}
		$filter = $this->input->get();
		if (!empty($filter)) {
			$data['filter'] = $filter;
		}
		$data['suppler'] = $this->db->get_where('tblsuppliers', ['active' => 1])->result_array();
		$data['recommended_list'] = $this->recommended_list_model->getRecommendedListParent([0], 1);
		// $data['recommended_list'] = $this->recommended_list_model->getRelate();
		$data['data_roles'] = [];
		$this->get_parent_role(0, $data['data_roles'], [], 0);
		$data['title'] = _l('c_production_report');
		$this->load->view('admin/production_report/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('production_report');
	}

	public function inspection_criteria($id = '', $process_id = '', $is = 0)
	{
		$data['title'] = 'Kiểm tiêu chí';
		$data['id'] = $id;
		$data['process_id'] = $process_id;
		$data['is'] = $is;
		$this->db->select('tbl_inspection_criteria.*');
		$this->db->join('tbl_setting_production_report_inspection_criteria', 'tbl_setting_production_report_inspection_criteria.id_inspection_criteria = tbl_inspection_criteria.id');
		$this->db->join('tbl_setting_production_report', 'tbl_setting_production_report.id = tbl_setting_production_report_inspection_criteria.id_setting_production_report');
		$this->db->where('tbl_setting_production_report.id_process', $process_id);
		$data['category_hand_over'] = $this->db->get('tbl_inspection_criteria')->result_array();
		$this->load->view('admin/production_report/inspection_criteria', $data);
	}

	function add_production_report()
	{
		$_data = $this->input->post();
		$isCheck = !empty($_data['isCheck']) ? $_data['isCheck'] : NULL;
		$id = $this->input->post('id');
		$process_id = $this->input->post('process_id');
		$status = 1;
		$type = 1;
		$this->db->where('production_report_id', $id);
		$this->db->where('process_id', $process_id);
		$internal_proposal = $this->db->get('tbl_process_production_report')->row();
		if (!empty($process_production_report) && !empty($process_production_report->staff_process)) {
			$data['result'] = 0;
			$data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
			echo json_encode($data);
			die;
		}
		$this->db->where('production_report_id', $id);
		$this->db->where('id <', $internal_proposal->id);
		$this->db->order_by('id', 'desc');
		$check_status_bef = $this->db->get('tbl_process_production_report')->row_array();
		if (!empty($check_status_bef)) {
			if ($check_status_bef['staff_process'] == 0) {
				$data['result'] = 0;
				$data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa duyệt, Không thể duyệt bước này');
				echo json_encode($data);
				die;
			}
		}


		$staff_id = get_staff_user_id();
		$date = date('Y-m-d H:i:s');
		$data = array(
			'staff_process' => $staff_id,
			'date_process' => $date,
		);
		$idd = '';
		$this->db->from('tbl_process_production_report');
		$this->db->where('production_report_id', $id);
		$this->db->where('process_id', $process_id);
		$dtProcess = $this->db->get()->row_array();
		if (!empty($dtProcess)) {
			$this->db->where('id', $dtProcess['id']);
			$success = $this->db->update('tbl_process_production_report', $data);
			$idd = $dtProcess['id'];
		} else {
			$success = $this->db->insert('tbl_process_production_report', [
				'production_report_id' => $id,
				'process_id' => $process_id,
				'staff_process' => $staff_id,
				'date_process' => $date,
			]);
			$idd = $this->db->insert_id();
		}
		if (!empty($success)) {
			if (!empty($isCheck)) {
				foreach ($isCheck as $key => $value) {
					$ins_detail = [];
					$ins_detail['production_report'] = $id;
					$ins_detail['id_production_report_process'] = $idd;
					$ins_detail['process_id'] = $process_id;
					$ins_detail['inspection_criteria'] = $key;
					$ins_detail['isCheck'] = 1;
					$this->db->insert('tbl_setting_production_report_inspection_criteria_process', $ins_detail);
				}
			}

			//Kiểm tra xem báo cáo vi phạm này có phải thuộc công việc tiến trình
			$this->load->model('tasks_approval_model');
			$this->tasks_approval_model->check_mail_production_report($id);


			$this->load->model('entrance_ticket_model');
			$this->entrance_ticket_model->check_status($id);
			// $production_report = get_table_where('tblproduction_report', ['id' => $id], '', 'row_array');
			// if (!empty($production_report)) {
			// 	$id_tasks_process = $production_report['id_tasks_process'];
			// 	$id_tasks_process_child = $production_report['id_tasks_process_child'];
			// 	if (!empty($id_tasks_process_child) && !empty($id_tasks_process)) {
			// 		//Biết nó từ công việc qui trình tạo qua rùi kiểm tra trạng thái duyệt hết chưa
			// 		$this->db->from('tbl_process_production_report');
			// 		$this->db->where('production_report_id', $id);
			// 		$this->db->where('staff_process', 0);
			// 		$this->db->limit(1);
			// 		$check = $this->db->get()->num_rows();
			// 		if (empty($check)) {
			// 			$this->load->model('tasks_approval_model');
			// 			$task_id = $production_report['id_tasks'];
			// 			$process = get_table_where(
			// 				'tbl_tasks_inspection_criteria_process',
			// 				[
			// 					'tasks' => $task_id,
			// 					'id_tasks_process' => $id_tasks_process,
			// 					'inspection_criteria' => $id_tasks_process_child
			// 				],
			// 				'',
			// 				'row_array'
			// 			);

			// 			$this->db->select('tblcategory_tasks_process_child.role_processing');
			// 			$this->db->from('tbl_tasks_process_child');
			// 			$this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_tasks_process_child.id_category_tasks_process', 'left');
			// 			$this->db->where('tbl_tasks_process_child.task', $task_id);
			// 			$this->db->where('tbl_tasks_process_child.id_category_tasks', $process['process_id']);
			// 			$process_child = $this->db->get()->row_array();

			// 			$role_processing = $process_child['role_processing'] ?? 0;
			// 			$this->tasks_approval_model->send_email_next_role($task_id, $process['process_id'], $id_tasks_process, $role_processing, $id_tasks_process_child);
			// 		}

			// 	}
			// }
			//

			$data['success'] = true;
			$data['alert_type'] = 'success';
			$data['message'] = lang('Duyệt thành công');
			echo json_encode($data);
			die;
		} else {
			$data['result'] = true;
			$data['alert_type'] = 'warning';
			$data['message'] = lang('Duyệt không thành công');
			echo json_encode($data);
			die;
		}
		//end cong
	}

	public function get_table_delivery_records_internal_proposal()
	{
		$process_id = $this->input->post('process_id');
		$id = $this->input->post('id');
		$is = $this->input->post('is');
		$this->db->select('tbl_inspection_criteria.*');
		$this->db->join('tbl_setting_production_report_inspection_criteria', 'tbl_setting_production_report_inspection_criteria.id_inspection_criteria = tbl_inspection_criteria.id');
		$this->db->join('tbl_setting_production_report', 'tbl_setting_production_report.id = tbl_setting_production_report_inspection_criteria.id_setting_production_report');
		$this->db->where('tbl_setting_production_report.id_process', $process_id);
		$data['category_hand_over'] = $this->db->get('tbl_inspection_criteria')->result_array();
		// if (!empty($data['category_hand_over'])) {
		//     $this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
		//     $this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
		//     $this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
		//     $this->db->where('tbl_hand_over_task.category_hand_over_id', $data['category_hand_over']->id);
		//     $this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
		//     $this->db->where('tbl_hand_over_task.type_hide', 0);
		//     $hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
		//     $data['category_hand_over']->task = $hand_over_task;
		// }
		// $this->db->where('id_create', $internal_proposal_id);
		// $this->db->where('type_create', 'internal_proposal');
		// $data['delivery_records'] = $this->db->get('tbl_delivery_records')->row();
		$data['id'] = $id;
		$data['process_id'] = $process_id;
		$data['is'] = $is;
		$this->load->view('admin/production_report/table_production', $data);
	}

	public function detail($id = '')
	{
		if (!$this->create && empty($id)) {
			access_denied();
		}
		if (!$this->edit && !empty($id)) {
			access_denied();
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			// var_dump($_FILES);die;
			if (!empty($data)) {

				$jd_responsibility = !empty($data['jd_responsibility']) ? $data['jd_responsibility'] : [];
				unset($data['jd_responsibility']);
				$jd_jurisdiction = !empty($data['jd_jurisdiction']) ? $data['jd_jurisdiction'] : [];
				unset($data['jd_jurisdiction']);
				$jd_requirement = !empty($data['jd_requirement']) ? $data['jd_requirement'] : [];
				unset($data['jd_requirement']);
				$jd_competency_standard = !empty($data['jd_competency_standard']) ? $data['jd_competency_standard'] : [];
				unset($data['jd_competency_standard']);


				$items = !empty($data['items']) ? $data['items'] : [];
				unset($data['items']);
				$trouble_item_id = !empty($data['trouble_item_id']) ? $data['trouble_item_id'] : [];
				unset($data['trouble_item_id']);
				$id_trouble = $data['id_trouble'];
				$id_list = !empty($data['id_list']) ? $data['id_list'] : [];
				unset($data['id_list']);
				$assigned = !empty($data['staff_assigned']) ? $data['staff_assigned'] : [];
				unset($data['staff_assigned']);
				$staff_handler = !empty($data['staff_handler']) ? $data['staff_handler'] : [];
				unset($data['staff_handler']);
				$checked = !empty($data['checked']) ? $data['checked'] : [];
				if (empty($checked)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Vui lòng chọn quy trình xử lý'
					]);
					die();
				}
				unset($data['checked']);
				$category_kpi_criteria_id = !empty($data['category_kpi_criteria_id']) ? $data['category_kpi_criteria_id'] : [];
				unset($data['category_kpi_criteria_id']);
				$arrKpi = [];
				if (!empty($category_kpi_criteria_id)) {
					foreach ($category_kpi_criteria_id as $kk => $vv) {
						$dtCategoryKpi = get_table_where('tbl_category_kpi_criteria', ['id' => $vv], '', 'row_array');
						$arrKpi[] = [
							'category_kpi_id' => $dtCategoryKpi['category_kpi_id'],
							'category_kpi_criteria_id' => $vv
						];
					}
				}
				$violation_point = 0;
				$violation_id = 0;
				$countTrouble = 0;
				if (!empty($data['id_trouble'])) {
					$this->db->select('tbltrouble_violation_point.id as id, tbltrouble_violation_point.point as point');
					$this->db->join(
						'tbltrouble_violation_point',
						'tbltrouble_violation_point.id = tbltrouble.trouble_violation_point_id',
						'inner'
					);
					$this->db->where('tbltrouble.id', $data['id_trouble']);
					$trouble_violation = $this->db->get('tbltrouble')->row_array();
					if (!empty($trouble_violation['id'])) {
						$violation_id = $trouble_violation['id'];
						$violation_point = $trouble_violation['point'];
					}
					$dates = to_sql_date(_dt($data['date']), true);
					$dates = strtotime($dates);
					$dates = strftime("%m", $dates);
					$countTrouble = countTrouble($data['id_trouble'], $dates);
				}

				$countViolate = 0;
				if (!empty($data['violate']) && !empty($data['staff_responsible'])) {
					$dates = to_sql_date(_dt($data['date']), true);
					// $dates = strtotime($dates);
					// $dates = strftime("%Y", $dates);
					$_datas = getQuarterStartAndEndDate($dates);
					$countViolate = countViolate($data['staff_responsible'], $_datas['start'], $_datas['end']);
				}

				$_trouble = $data['trouble'] ?? null;
				$dtReason = $data['reason'] ?? null;
				// if(isset($data['trouble'])) unset($data['trouble']);
				if (isset($data['reason'])) {
					unset($data['reason']);
				}
				$type_report = $data['type_report'] ?? 0;
				if (empty($type_report)) {
					echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Vui lòng chọn loại']);
					die();
				}
				$responsible_type = $data['responsible_type'] ?? '';
				$staff_responsible = $data['staff_responsible'] ?? 0;
				$department_responsible = $data['department_responsible'] ?? 0;
				$id_trouble = $data['id_trouble'] ?? 0;
				if ($type_report == 1) {
					if ($responsible_type == 'staff') {
						if (empty($staff_responsible)) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn người chịu trách nhiệm'
							]);
							die();
						}
						if (empty($id_trouble)) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn sứ cố'
							]);
							die();
						}
					} else {
						if (empty($department_responsible)) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn BP chịu trách nhiệm'
							]);
							die();
						}
					}
				}
				if (empty($id)) {
					if ($data['type_report'] == 4) {
						if (empty($data['violation_group'])) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn nhóm vi phạm'
							]);
							die();
						}
					} else {
						if (empty($data['category_tasks'])) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn mã công việc'
							]);
							die();
						}
					}

					$kpi_list_criteria_department_id = $data['kpi_list_criteria_department'] ?? 0;
					$kpi_list_criteria_department_child_post = $data['kpi_list_criteria_department_child'] ?? null;
					$kpi_list_criteria_department_violate = $data['kpi_list_criteria_department_violate'] ?? null;
					$kpi_list_criteria_department_id_child = 0;
					$kpi_list_criteria_department_id_childd = 0;
					if (!empty($kpi_list_criteria_department_child_post)) {
						$kpi_list_criteria_department_child_post = explode('-', $kpi_list_criteria_department_child_post);
						$kpi_list_criteria_department_id_child = $kpi_list_criteria_department_child_post[0];
						$kpi_list_criteria_department_id_childd = $kpi_list_criteria_department_child_post[1];
					}
					if ($data['type_report'] != 4 && $data['type_report'] != 2) {
						$kpi_list_criteria_department_id = 0;
						$kpi_list_criteria_department_id_child = 0;
						$kpi_list_criteria_department_id_childd = 0;
						$kpi_list_criteria_department_violate = null;
					}
					$machines_id = $data['machines_id'] ?? 0;
					$downtime = $data['downtime'] ?? 0;

					$reference_no = getReference('production_report');
					$options = [
						'reference_no' => $reference_no,
						'date' => to_sql_date(_dt($data['date']), true),
						'name_report' => !empty($data['name_report']) ? $data['name_report'] : '',
						'category_tasks' => !empty($data['category_tasks']) ? $data['category_tasks'] : '',
						'id_departments' => !empty($data['id_departments']) ? $data['id_departments'] : 0,
						'id_production_detail' => !empty($data['id_production_detail']) ? $data['id_production_detail'] : null,
						'id_production_orders' => !empty($data['id_production_orders']) ? $data['id_production_orders'] : null,
						'id_orders' => !empty($data['id_orders']) ? $data['id_orders'] : null,
						'suppler_id' => !empty($data['suppler_id']) ? $data['suppler_id'] : null,
						'production_stage' => $data['production_stage'],
						'detail_tasks' => $data['detail_tasks'],
						'type_stage_1' => !empty($data['type_stage_1']) ? $data['type_stage_1'] : 0,
						'type_stage_2' => !empty($data['type_stage_2']) ? $data['type_stage_2'] : 0,
						'type_stage_3' => !empty($data['type_stage_3']) ? $data['type_stage_3'] : 0,
						'type_stage_4' => !empty($data['type_stage_4']) ? $data['type_stage_4'] : 0,
						'id_trouble' => !empty($data['id_trouble']) ? $data['id_trouble'] : 0,
						'countTrouble' => $countTrouble,
						'countViolate' => $countViolate,
						'trouble_violation_point_id' => $violation_id,
						'trouble_violation_point' => $violation_point,
						'responsible_type' => !empty($data['responsible_type']) ? $data['responsible_type'] : '',
						'department_responsible' => !empty($data['department_responsible']) ? $data['department_responsible'] : 0,
						'staff_responsible' => !empty($data['staff_responsible']) ? $data['staff_responsible'] : 0,
						'quantity_pcs' => number_format_data($data['quantity_pcs'], false),
						'quantity' => number_format_data($data['quantity'], false),
						'time_of_recording' => to_sql_date($data['time_of_recording'], true),
						'action_now_1' => !empty($data['action_now_1']) ? $data['action_now_1'] : 0,
						'action_now_2' => !empty($data['action_now_2']) ? $data['action_now_2'] : 0,
						'action_now_3' => !empty($data['action_now_3']) ? $data['action_now_3'] : 0,
						'action_now_4' => !empty($data['action_now_4']) ? $data['action_now_4'] : 0,
						'reason' => !empty($data['reason']) ? $data['reason'] : null,
						'described' => !empty($data['described']) ? $data['described'] : null,
						'overcome' => !empty($data['overcome']) ? $data['overcome'] : null,
						'note' => $data['note'],
						'id_tasks' => !empty($data['id_tasks']) ? $data['id_tasks'] : null,
						'id_delivery_records' => !empty($data['id_delivery_records']) ? $data['id_delivery_records'] : null,
						'id_delivery_records_detail' => !empty($data['id_delivery_records_detail']) ? $data['id_delivery_records_detail'] : null,
						'create_by' => get_staff_user_id(),
						'date_create' => date('Y-m-d H:i:s'),
						'id_branch' => $data['id_branch'],
						'recommended_list_group_id' => $data['recommended_list_group_id'],
						// 'recommended_list_id' => (!empty($data['recommended_list_id'][0]) ? $data['recommended_list_id'][0] : 0),
						'recommended_list_id' => $data['recommended_list_id'],
						'role_id' => $data['role_id'] ?? NULL,
						'staff_evaluate' => !empty($data['staff_evaluate']) ? $data['staff_evaluate'] : 0,
						'note_fix' => !empty($data['note_fix']) ? $data['note_fix'] : null,
						'staff_handover' => !empty($data['staff_handover']) ? $data['staff_handover'] : 0,
						'quantity_kpi' => !empty($data['quantity_kpi']) ? number_unformat($data['quantity_kpi']) : 0,
						'object_id' => !empty($_GET['object_id']) ? ($_GET['object_id']) : (!empty($data['object_id']) ? ($data['object_id']) : 0),
						'object_type' => !empty($_GET['object_type']) ? ($_GET['object_type']) : (!empty($data['object_type']) ? ($data['object_type']) : null),
						'violate' => !empty($data['violate']) ? $data['violate'] : 0,
						'big_risk' => !empty($data['big_risk']) ? $data['big_risk'] : 0,

						'type_report' => !empty($data['type_report']) ? $data['type_report'] : 0,
						'category_recommended_id' => !empty($data['category_recommended_id']) ? $data['category_recommended_id'] : 0,
						'suggest_id' => !empty($data['suggest_id']) ? $data['suggest_id'] : 0,
						'suggest_id_detail' => !empty($data['suggest_id_detail']) ? $data['suggest_id_detail'] : 0,
						'staff_manage' => !empty($data['staff_manage']) ? $data['staff_manage'] : 0,
						'id_internal_proposal_process' => !empty($data['id_internal_proposal_process']) ? $data['id_internal_proposal_process'] : 0,
						'id_internal_proposal_process_child' => !empty($data['id_internal_proposal_process_child']) ? $data['id_internal_proposal_process_child'] : 0,
						'id_internal_proposal' => !empty($data['id_internal_proposal']) ? $data['id_internal_proposal'] : 0,
						'violation_group' => !empty($data['violation_group']) ? $data['violation_group'] : 0,
						'id_quotes' => !empty($data['id_quotes']) ? $data['id_quotes'] : 0,
						'damage_cost' => !empty($data['damage_cost']) ? number_unformat($data['damage_cost']) : 0,
						'id_tasks_process' => !empty($data['id_tasks_process']) ? $data['id_tasks_process'] : 0,
						'id_tasks_process_child' => !empty($data['id_tasks_process_child']) ? $data['id_tasks_process_child'] : 0,
						'id_audit_item' => !empty($data['id_audit_item']) ? $data['id_audit_item'] : 0,
						'audit_id' => !empty($data['audit_id']) ? $data['audit_id'] : 0,

						'in_and_out_of_work_item' => !empty($data['in_and_out_of_work_item']) ? $data['in_and_out_of_work_item'] : 0,
						'in_and_out_of_work' => !empty($data['in_and_out_of_work']) ? $data['in_and_out_of_work'] : 0,
						'entrance_ticket_id' => !empty($data['entrance_ticket_id']) ? $data['entrance_ticket_id'] : 0,
						'step' => !empty($data['step']) ? $data['step'] : 0,
						'kpi_list_criteria_department_id' => $kpi_list_criteria_department_id,
						'kpi_list_criteria_department_id_child' => $kpi_list_criteria_department_id_child,
						'kpi_list_criteria_department_id_childd' => $kpi_list_criteria_department_id_childd,
						'kpi_list_criteria_department_violate' => $kpi_list_criteria_department_violate,
						'machines_id' => $machines_id,
						'downtime' => $downtime,
						'role_id_jd' => !empty($data['role_id_jd']) ? $data['role_id_jd'] : 0,
						'jd_tasks' => !empty($data['jd_tasks']) ? $data['jd_tasks'] : 0,
						'salary_3p' => !empty($data['salary_3p']) ? $data['salary_3p'] : 0,
						'point_kpi' => !empty($data['point_kpi']) ? $data['point_kpi'] : 0,

					];
					$success = $this->db->insert('tblproduction_report', $options);
					if (!empty($success)) {
						$id = $this->db->insert_id();
						$big_risk = !empty($data['big_risk']) ? $data['big_risk'] : 0;
						$staff_responsible = !empty($data['staff_responsible']) ? $data['staff_responsible'] : 0;
						if (!empty($big_risk)) {
							createEvaluationEmployee($id, $staff_responsible);
						}
						$process = get_table_where('tbl_process');
						foreach ($process as $k => $v) {
							$ins = [];
							$ins['production_report_id'] = $id;
							$ins['process_id'] = $v['id'];
							$ins['name'] = $v['name'];
							$this->db->insert('tbl_process_production_report', $ins);
						}
						$setting_production_report = get_table_where('tbl_setting_production_report');
						foreach ($setting_production_report as $k => $v) {
							$ins = [];
							$ins['id_production_report'] = $id;
							$ins['id_process'] = $v['id_process'];
							$ins['id_role'] = $v['id_role'];
							$this->db->insert('tbl_role_production_report', $ins);
						}
						updateReference('production_report');
						if (!empty($_FILES['image']['name'])) {
							foreach ($_FILES['image']['name'] as $key => $value) {
								if (!empty($_FILES['image']['name'][$key]) && $_FILES['image']['size'][$key] > 0) {
									if (!file_exists($this->upload_path)) {
										mkdir($this->upload_path);
									}
									if (!file_exists($this->upload_path . $id . '/')) {
										mkdir($this->upload_path . $id . '/');
									}
									$arrayFile = [];
									$tmpFilePath = $_FILES['image']['tmp_name'][$key];
									if (!empty($tmpFilePath) && $tmpFilePath != '') {
										$filename = vn_to_str(@unique_filename(
											($this->upload_path . $id . '/'),
											'image' . '_' . time() . $_FILES['image']['name'][$key]
										));
										// if (_upload_extension_allowed($filename)) {
										$newFilePath = $this->upload_path . $id . '/' . $filename;
										if (move_uploaded_file($tmpFilePath, $newFilePath)) {
											if (file_exists($newFilePath)) {
												$arrayFile[] = [
													'file_name' => $this->upload_path . $id . '/' . $filename,
													'rel_id' => $id,
													'rel_type' => 'production_report',
													'filetype' => $_FILES['image']['type'][$key],
													'staffid' => get_staff_user_id(),
													'dateadded' => date('Y-m-d H:i:s')
												];
											}
										}
										// }
									}
									if (!empty($arrayFile)) {
										$this->db->insert_batch('tblfiles', $arrayFile);
									}
								}
							}
						}

						if (!empty($jd_responsibility)) {
							foreach ($jd_responsibility as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_responsibility',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($jd_jurisdiction)) {
							foreach ($jd_jurisdiction as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_jurisdiction',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($jd_requirement)) {
							foreach ($jd_requirement as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_requirement',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($jd_competency_standard)) {
							foreach ($jd_competency_standard as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_competency_standard',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($id_trouble)) {
							foreach ($items as $type => $item) {
								foreach ($item as $key => $value) {
									if (!empty($value)) {
										$this->db->insert('tblproduction_report_items', [
											'type' => $type,
											'id_production_report' => $id,
											'name' => $value,
											'ischeck' => !empty($checked[$type][$key]) ? $checked[$type][$key] : 0,
										]);
										if (empty($trouble_item_id[$type][$key])) { // Thêm sự cố mới
											$this->db->insert('tbltrouble_items', [
												'id_trouble' => $id_trouble,
												'name' => $value,
												'type' => $type
											]);
										}
									}
								}
							}
						}
						if (!empty($assigned)) {
							foreach ($assigned as $key => $value) {
								$this->db->insert('tblproduction_report_assigned', [
									'id_production_report' => $id,
									'staff_id' => $value
								]);
							}
						}
						if (!empty($staff_handler)) {
							foreach ($staff_handler as $key => $value) {
								$this->db->insert('tblproduction_report_handler', [
									'id_production_report' => $id,
									'staff_id' => $value
								]);
							}
						}
						if (!empty($arrKpi)) {
							foreach ($arrKpi as $key => $value) {
								$value['production_report_id'] = $id;
								$this->db->insert('tbl_production_report_kpi', $value);
							}
						}
						$_data = [
							'name' => $data['name_report'],
							'hourly_rate' => 0,
							'category_tasks' => !empty($data['category_tasks']) ? $data['category_tasks'] : '',
							'startdate' => $data['date'],
							'duedate' => null,
							'priority' => 2,
							'repeat_every_custom' => 1,
							'repeat_type_custom' => 'day',
							'rel_type' => 'production_report',
							'rel_id' => $id,
							'id_branch' => $data['id_branch'],
							'department_id' => !empty($data['id_departments']) ? [$data['id_departments']] : null,
							'description' => !empty($data['described']) ? $data['described'] : null,
						];
						$attachments = $this->db->get_where(
							'tblfiles',
							['rel_id' => $id, 'rel_type' => 'production_report']
						)->result_array();
						if (!empty($attachments)) {
							$_data['copy_attachments'] = $attachments;
						}
						$id_tasks = $this->tasks_model->add($_data, false, false);
						if (!empty($id_tasks)) {
							// $this->db->where('type', 'procedure');
							$this->db->where('ischeck', '1');
							$this->db->where('id_production_report', $id);
							$this->db->group_start();
							// $this->db->where('type', 'material');
							// $this->db->or_where('type', 'man');
							// $this->db->or_where('type', 'machine');
							// $this->db->or_where('type', 'method');
							// $this->db->or_where('type', 'environment');
							$this->db->where('type', 'procedure');
							$this->db->group_end();
							$procedure = $this->db->get('tblproduction_report_items')->result_array();
							if (!empty($procedure)) {
								foreach ($procedure as $key => $value) {
									$this->db->insert('tbltask_checklist_items', [
										'taskid' => $id_tasks,
										'description' => $value['name'],
										'dateadded' => date('Y-m-d H:i:s'),
										'addedfrom' => get_staff_user_id(),
										'list_order' => ($key + 1),
									]);
								}
							}
							$staffNow = get_staff_user_id();
							$this->db->where('id_production_report', $id);
							$internal_assigned = $this->db->get('tblproduction_report_assigned')->result_array();
							if (!empty($internal_assigned)) {
								foreach ($internal_assigned as $key => $value) {
									$this->db->insert('tbltask_followers', [
										'staffid' => $value['staff_id'],
										'taskid' => $id_tasks,
									]);
								}
							}
							$this->db->where('id_production_report', $id);
							$this->db->where('staff_id != "' . $staffNow . '"', false, false);
							$internal_handler = $this->db->get('tblproduction_report_handler')->result_array();
							if (!empty($internal_handler)) {
								foreach ($internal_handler as $key => $value) {
									$this->db->insert('tbltask_assigned', [
										'staffid' => $value['staff_id'],
										'taskid' => $id_tasks,
										'assigned_from' => $staffNow,
									]);
								}
							}
							$listEmail = [];
							$this->db->select('group_concat(email) as list_email');
							$this->db->where('id_production_report', $id);
							$this->db->where('email is not null', false, false);
							$this->db->join('tblstaff', 'tblstaff.staffid = tblproduction_report_handler.staff_id');
							$listEmailHandler = $this->db->get('tblproduction_report_handler')->row('list_email');
							if (!empty($listEmailHandler)) {
								$listEmailHandler = explode(',', $listEmailHandler);
								send_email($listEmailHandler, $this->get_content($id, 1));
							}
							// $this->db->select('group_concat(email) as list_email');
							// $this->db->where('id_production_report', $id);
							// $this->db->where('email is not null', false, false);
							// $this->db->join('tblstaff', 'tblstaff.staffid = tblproduction_report_assigned.staff_id');
							// $listEmailAssigned = $this->db->get('tblproduction_report_assigned')->row('list_email');
							// if (!empty($listEmailAssigned)) {
							//     $listEmailAssigned = explode(',', $listEmailAssigned);
							//     if (!empty($listEmailAssigned)) {
							//         send_email($listEmailAssigned, $this->get_content($id, 2));
							//     }
							// }
						}
						$arrProductionReportReason = [];
						if ($_trouble) {
							foreach ($_trouble as $kT => $vT) {
								if ($vT == 1) {
									$reason = _string($dtReason[$kT] ?? '');
									$arrProductionReportReason[] = [
										'pr_id' => $id,
										'trouble' => $kT,
										'is_check' => 1,
										'reason' => $reason,
									];
								}
							}
						}
						if (!empty($arrProductionReportReason)) {
							$this->recommended_list_model->insertBatchProductionReportReason($arrProductionReportReason);
						}

						//Cập phiếu hàng mang ra cổng
						if (!empty($data['entrance_ticket_id']) && !empty($data['step'])) {
							$this->load->model('entrance_ticket_model');
							$this->entrance_ticket_model->handlingProductionReportEntranceTicketStep($id, $data['entrance_ticket_id'], $data['step']);
						}

						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Thêm dữ liệu thành công',
							'idtask' => $id_tasks
						]);
						die();
					} else {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Thêm dữ liệu không thành công'
						]);
						die();
					}
				} else {
					if ($data['type_report'] == 4) {
						if (empty($data['violation_group'])) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn nhóm vi phạm'
							]);
							die();
						}
					} else {
						if (empty($data['category_tasks'])) {
							echo json_encode([
								'success' => false,
								'alert_type' => 'danger',
								'message' => 'Vui lòng chọn mã công việc'
							]);
							die();
						}
					}
					$dtProductionReport = get_table_where(
						'tblproduction_report',
						['id' => $id],
						'',
						'row_array',
						'',
						'reference_no'
					);

					$kpi_list_criteria_department_id = $data['kpi_list_criteria_department'] ?? 0;
					$kpi_list_criteria_department_child_post = $data['kpi_list_criteria_department_child'] ?? null;
					$kpi_list_criteria_department_violate = $data['kpi_list_criteria_department_violate'] ?? null;
					$kpi_list_criteria_department_id_child = 0;
					$kpi_list_criteria_department_id_childd = 0;
					if (!empty($kpi_list_criteria_department_child_post)) {
						$kpi_list_criteria_department_child_post = explode('-', $kpi_list_criteria_department_child_post);
						$kpi_list_criteria_department_id_child = $kpi_list_criteria_department_child_post[0];
						$kpi_list_criteria_department_id_childd = $kpi_list_criteria_department_child_post[1];
					}

					if ($data['type_report'] != 4 && $data['type_report'] != 2) {
						$kpi_list_criteria_department_id = 0;
						$kpi_list_criteria_department_id_child = 0;
						$kpi_list_criteria_department_id_childd = 0;
						$kpi_list_criteria_department_violate = null;
					}
					$machines_id = $data['machines_id'] ?? 0;
					$downtime = $data['downtime'] ?? 0;

					$options = [
						'date' => to_sql_date($data['date'], true),
						'name_report' => !empty($data['name_report']) ? $data['name_report'] : '',
						'category_tasks' => !empty($data['category_tasks']) ? $data['category_tasks'] : '',
						'id_departments' => !empty($data['id_departments']) ? $data['id_departments'] : 0,
						'id_production_detail' => !empty($data['id_production_detail']) ? $data['id_production_detail'] : null,
						'id_production_orders' => !empty($data['id_production_orders']) ? $data['id_production_orders'] : null,
						'id_orders' => !empty($data['id_orders']) ? $data['id_orders'] : null,
						'suppler_id' => !empty($data['suppler_id']) ? $data['suppler_id'] : null,
						'production_stage' => $data['production_stage'],
						'type_stage_1' => !empty($data['type_stage_1']) ? $data['type_stage_1'] : 0,
						'type_stage_2' => !empty($data['type_stage_2']) ? $data['type_stage_2'] : 0,
						'type_stage_3' => !empty($data['type_stage_3']) ? $data['type_stage_3'] : 0,
						'id_trouble' => !empty($data['id_trouble']) ? $data['id_trouble'] : 0,
						'trouble_violation_point_id' => $violation_id,
						'trouble_violation_point' => $violation_point,
						'responsible_type' => !empty($data['responsible_type']) ? $data['responsible_type'] : '',
						'department_responsible' => !empty($data['department_responsible']) ? $data['department_responsible'] : 0,
						'staff_responsible' => !empty($data['staff_responsible']) ? $data['staff_responsible'] : 0,
						'type_stage_4' => !empty($data['type_stage_4']) ? $data['type_stage_4'] : 0,
						'quantity_pcs' => number_format_data($data['quantity_pcs'], false),
						'quantity' => number_format_data($data['quantity'], false),
						'time_of_recording' => to_sql_date($data['time_of_recording'], true),
						'action_now_1' => !empty($data['action_now_1']) ? $data['action_now_1'] : 0,
						'action_now_2' => !empty($data['action_now_2']) ? $data['action_now_2'] : 0,
						'action_now_3' => !empty($data['action_now_3']) ? $data['action_now_3'] : 0,
						'action_now_4' => !empty($data['action_now_4']) ? $data['action_now_4'] : 0,
						'reason' => !empty($data['reason']) ? $data['reason'] : null,
						'described' => !empty($data['described']) ? $data['described'] : null,
						'overcome' => !empty($data['overcome']) ? $data['overcome'] : null,
						'note' => $data['note'],
						'id_tasks' => !empty($data['id_tasks']) ? $data['id_tasks'] : '',
						'id_branch' => $data['id_branch'],
						'recommended_list_group_id' => $data['recommended_list_group_id'],
						'recommended_list_id' => $data['recommended_list_id'],
						'role_id' => $data['role_id'],
						'staff_evaluate' => !empty($data['staff_evaluate']) ? $data['staff_evaluate'] : 0,
						'note_fix' => !empty($data['note_fix']) ? $data['note_fix'] : null,
						'staff_handover' => !empty($data['staff_handover']) ? $data['staff_handover'] : 0,
						'quantity_kpi' => !empty($data['quantity_kpi']) ? number_unformat($data['quantity_kpi']) : 0,
						'violate' => !empty($data['violate']) ? $data['violate'] : 0,
						'type_report' => !empty($data['type_report']) ? $data['type_report'] : 0,
						'staff_manage' => !empty($data['staff_manage']) ? $data['staff_manage'] : 0,
						'violation_group' => !empty($data['violation_group']) ? $data['violation_group'] : 0,
						'id_quotes' => !empty($data['id_quotes']) ? $data['id_quotes'] : 0,
						'damage_cost' => !empty($data['damage_cost']) ? number_unformat($data['damage_cost']) : 0,
						'id_tasks_process' => !empty($data['id_tasks_process']) ? $data['id_tasks_process'] : 0,
						'id_tasks_process_child' => !empty($data['id_tasks_process_child']) ? $data['id_tasks_process_child'] : 0,
						'kpi_list_criteria_department_id' => $kpi_list_criteria_department_id,
						'kpi_list_criteria_department_id_child' => $kpi_list_criteria_department_id_child,
						'kpi_list_criteria_department_id_childd' => $kpi_list_criteria_department_id_childd,
						'kpi_list_criteria_department_violate' => $kpi_list_criteria_department_violate,
						'machines_id' => $machines_id,
						'downtime' => $downtime,
						'role_id_jd' => !empty($data['role_id_jd']) ? $data['role_id_jd'] : 0,
						'jd_tasks' => !empty($data['jd_tasks']) ? $data['jd_tasks'] : 0,
						'salary_3p' => !empty($data['salary_3p']) ? $data['salary_3p'] : 0,
						'point_kpi' => !empty($data['point_kpi']) ? $data['point_kpi'] : 0,
						'entrance_ticket_id' => !empty($data['entrance_ticket_id']) ? $data['entrance_ticket_id'] : 0,
						'step' => !empty($data['step']) ? $data['step'] : 0,
					];
					if (empty($dtProductionReport['reference_no'])) {
						$options['reference_no'] = getReference('production_report');
					}
					// print_arrays($data);
					$this->db->where('id', $id);
					$success = $this->db->update('tblproduction_report', $options);
					if (!empty($success)) {
						if (empty($dtProductionReport['reference_no'])) {
							updateReference('production_report');
						}
						if (!empty($data['file_remove'])) {
							$this->db->where_in('id', $data['file_remove']);
							$this->db->where('rel_type', 'production_report');
							$this->db->where('rel_id', $id);
							$files_remove = $this->db->get('tblfiles')->result_array();
							if (!empty($files_remove)) {
								foreach ($files_remove as $key => $value) {
									if (file_exists($value['file_name'])) {
										unlink($value['file_name']);
									}
								}
								$this->db->where_in('id', $data['file_remove']);
								$this->db->where('rel_type', 'production_report');
								$this->db->where('rel_id', $id);
								$this->db->delete('tblfiles');
							}
						}
						if (!empty($_FILES['image']['name'])) {
							foreach ($_FILES['image']['name'] as $key => $value) {
								if (!empty($_FILES['image']['name'][$key]) && $_FILES['image']['size'][$key] > 0) {
									//									$old_images = get_table_where('tblfiles', ['rel_type' => 'production_report', 'rel_id' => $id]);
									//									foreach ($old_images as $old_image) {
									//										if (!empty($old_image['file_name'])) {
									//											if (file_exists($old_image['file_name'])) {
									//												// Thực hiện xóa tệp tin
									//												if (unlink($old_image['file_name'])) {
									//
									//												}
									//											}
									//											$this->db->delete('tblfiles', ['id' => $old_image['id']]);
									//										}
									//									}
									if (!file_exists($this->upload_path)) {
										mkdir($this->upload_path);
									}
									if (!file_exists($this->upload_path . $id . '/')) {
										mkdir($this->upload_path . $id . '/');
									}
									$arrayFile = [];
									$tmpFilePath = $_FILES['image']['tmp_name'][$key];
									if (!empty($tmpFilePath) && $tmpFilePath != '') {
										$filename = vn_to_str(@unique_filename(
											($this->upload_path . $id . '/'),
											'image' . '_' . time() . $_FILES['image']['name'][$key]
										));
										// if (_upload_extension_allowed($filename)) {
										$newFilePath = $this->upload_path . $id . '/' . $filename;
										if (move_uploaded_file($tmpFilePath, $newFilePath)) {
											if (file_exists($newFilePath)) {
												$arrayFile[] = [
													'file_name' => $this->upload_path . $id . '/' . $filename,
													'rel_id' => $id,
													'rel_type' => 'production_report',
													'filetype' => $_FILES['image']['type'][$key],
													'staffid' => get_staff_user_id(),
													'dateadded' => date('Y-m-d H:i:s')
												];
											}
										}
										// }
									}
									if (!empty($arrayFile)) {
										$this->db->insert_batch('tblfiles', $arrayFile);
									}
								}
							}
						}
						if (!empty($id_trouble)) {
							foreach ($items as $type => $item) {
								foreach ($item as $key => $value) {
									if (!empty($value)) {
										if (!empty($id_list[$type][$key])) {
											$this->db->where('id', $id_list[$type][$key]);
											$this->db->update('tblproduction_report_items', [
												'name' => $value,
												'ischeck' => !empty($checked[$type][$key]) ? $checked[$type][$key] : 0,
											]);
										} else {
											$this->db->insert('tblproduction_report_items', [
												'type' => $type,
												'id_production_report' => $id,
												'name' => $value,
												'ischeck' => !empty($checked[$type][$key]) ? $checked[$type][$key] : 0,
											]);
										}
										if (empty($trouble_item_id[$type][$key])) { // Thêm sự cố mới
											$this->db->insert('tbltrouble_items', [
												'id_trouble' => $id_trouble,
												'name' => $value,
												'type' => $type
											]);
										}
									}
								}
							}
						}
						$this->db->where('id_production_report', $id)->delete('tblproduction_report_assigned');
						if (!empty($assigned)) {
							foreach ($assigned as $key => $value) {
								$this->db->insert('tblproduction_report_assigned', [
									'id_production_report' => $id,
									'staff_id' => $value
								]);
							}
						}
						if (!empty($arrKpi)) {
							$this->db->where('tbl_production_report_kpi.production_report_id', $id);
							$this->db->delete('tbl_production_report_kpi');
							foreach ($arrKpi as $key => $value) {
								$value['production_report_id'] = $id;
								$this->db->insert('tbl_production_report_kpi', $value);
							}
						}
						$this->db->where('id_production_report', $id)->delete('tblproduction_report_handler');
						if (!empty($staff_handler)) {
							foreach ($staff_handler as $key => $value) {
								$this->db->insert('tblproduction_report_handler', [
									'id_production_report' => $id,
									'staff_id' => $value
								]);
							}
						}
						//handling reason
						$arrProductionReportReason = [];
						if ($_trouble) {
							foreach ($_trouble as $kT => $vT) {
								if ($vT == 1) {
									$reason = _string($dtReason[$kT] ?? '');
									$arrProductionReportReason[] = [
										'pr_id' => $id,
										'trouble' => $kT,
										'is_check' => 1,
										'reason' => $reason,
									];
								}
							}
						}
						$this->db->where('id_production_report', $id)->delete('tblproduction_report_jd');
						if (!empty($jd_responsibility)) {
							foreach ($jd_responsibility as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_responsibility',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($jd_jurisdiction)) {
							foreach ($jd_jurisdiction as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_jurisdiction',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($jd_requirement)) {
							foreach ($jd_requirement as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_requirement',
									'id_job_detail_child' => $value
								]);
							}
						}
						if (!empty($jd_competency_standard)) {
							foreach ($jd_competency_standard as $key => $value) {
								$this->db->insert('tblproduction_report_jd', [
									'id_production_report' => $id,
									'type' => 'jd_competency_standard',
									'id_job_detail_child' => $value
								]);
							}
						}


						$this->recommended_list_model->deleteProductionReportReason($id);
						if (!empty($arrProductionReportReason)) {
							$this->recommended_list_model->insertBatchProductionReportReason($arrProductionReportReason);
						}
						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Cập nhật dữ liệu thành công'
						]);
						die();
					}
				}
			}
		} else {
			$data['staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
			$data['suppler'] = $this->db->get_where('tblsuppliers', ['active' => 1])->result_array();
			$data['trouble'] = $this->db->get('tbltrouble')->result_array();
			// $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
			$data['category_tasks'] = $this->site_model->getCategoryTasks();
			$this->db->select('tbl_stages.name as name, tbl_stages.code as code, tbl_stages.id as id');
			$this->db->where('tbl_stages.name NOT LIKE "%_(cũ)"');
			$data['stages'] = $this->db->get('tbl_stages')->result_array();
			$data['branch'] = $this->db->get('tblbranch')->result_array();
			$data['staff_responsible'] = [];
			if (!empty($id)) {
				if (!empty($this->is_branch)) {
					if (!is_admin()) {
						$list_branch = get_array_branch_staff();
						if (!empty($list_branch)) {
							$this->db->group_start();
							$this->db->where_in('tblproduction_report.id_branch', $list_branch);
							$this->db->group_end();
							$this->db->where('id', $id);
							$ktData = $this->db->get('tblproduction_report')->row();
						} else {
							$ktData = false;
						}
						if (empty($ktData)) {
							access_denied();
						}
					}
				}
				$data['production_report'] = $this->db->get_where('tblproduction_report', ['id' => $id])->row();
				$this->db->select("
						tblstaff.staffid as staffid,
						tblstaff.firstname as firstname,
						tblstaff.lastname as lastname,
						tblstaff.code as code,
						CONCAT(firstname,' ',lastname) as fullname
					");
				$this->db->from('tblstaff');
				$this->db->where('tblstaff.role', $data['production_report']->role_id);
				$data['staff_responsible'] = $this->db->get()->result_array();
				//				if(!empty($data['production_report']->id_production_detail)) {
				//					$this->db->where('tbl_productions_orders_details.productions_orders_item_id', $data['production_report']->id_production_detail);
				//					$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
				//					$data['data_product'] = $this->db->get('tbl_productions_orders_details')->row();
				//				}
				if (!empty($data['production_report']->id_production_orders)) {
					$this->db->select('GROUP_CONCAT(distinct CONCAT(" - ", items_code, " (", items_name, ")") separator "<br/>") as list_name_items');
					$this->db->where(
						'tbl_productions_orders_items.productions_orders_id',
						$data['production_report']->id_production_orders
					);
					$data['data_product'] = $this->db->get('tbl_productions_orders_items')->row();
				}
				$this->db->select('group_concat(staff_id) as staff_assigned');
				$data['production_report']->staff_assigned = $this->db->get_where(
					'tblproduction_report_assigned',
					['id_production_report' => $id]
				)->row('staff_assigned');
				$data['production_report']->staff_assigned = explode(',', $data['production_report']->staff_assigned);
				$this->db->select('group_concat(staff_id) as staff_handler');
				$data['production_report']->staff_handler = $this->db->get_where(
					'tblproduction_report_handler',
					['id_production_report' => $id]
				)->row('staff_handler');
				$data['production_report']->staff_handler = explode(',', $data['production_report']->staff_handler);
				$data['production_report']->material = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'material'
				])->result_array();
				$data['production_report']->material_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'material',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$data['production_report']->man = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'man'
				])->result_array();
				$data['production_report']->man_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'man',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$data['production_report']->machine = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'machine'
				])->result_array();
				$data['production_report']->machine_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'machine',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$data['production_report']->method = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'method'
				])->result_array();
				$data['production_report']->method_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'method',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$data['production_report']->environment = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'environment'
				])->result_array();
				$data['production_report']->environment_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'environment',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$data['production_report']->procedure = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'procedure'
				])->result_array();
				$data['production_report']->procedure_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'procedure',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$data['production_report']->fix = $this->db->get_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'fix'
				])->result_array();
				$data['production_report']->fix_checked = get_table_where('tblproduction_report_items', [
					'id_production_report' => $id,
					'type' => 'fix',
					'ischeck' => 1
				], '', 'row_array', '', 'ischeck')['ischeck'] ?? 0;
				$arr_id = !empty($data['production_report']->category_tasks) ? [$data['production_report']->category_tasks] : null;
				$data['category_tasks'] = $this->site_model->getCategoryTasks($arr_id);
				$data['title'] = _l('c_edit_production_report');
				// if(!empty($this->hide_departments)) {
				// 	$this->db->where('active_departments', 1);
				// }
				if (!empty($data['production_report']->id_departments)) {
					$this->db->or_where('id', $data['production_report']->id_departments);
				}
				$data['departments'] = $this->db->get('tbl_room')->result_array();
				if (empty($data['data_tasks'])) {
					$where = [];
					if (!empty($data['production_report']->id_departments)) {
						//						$where['departments_id'] = $data['production_report']->id_departments;
					}
					$data['data_roles'] = [];
					$this->get_parent_role(0, $data['data_roles'], $where, 0);
				}
				$this->db->select("
                    tbl_category_kpi_criteria.id as id,
                    tbl_category_kpi_criteria.name as name,
                    tbl_category_kpi_criteria.code as code,
                    IF(tbl_category_kpi_criteria.type = 1,'Năng Lực','Tiêu Chuẩn') as type
                ");
				$this->db->from('tblstaff');
				$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
				$this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tblroles.kpi_category_id');
				$this->db->join(
					'tbl_category_kpi_criteria',
					'tbl_category_kpi_criteria.category_kpi_id = tbl_category_kpi.id'
				);
				$this->db->where('tblstaff.staffid', $data['production_report']->staff_responsible);
				$dtcategoryKpi = $this->db->get()->result_array();
				$dtProductionReportKpi = get_table_where('tbl_production_report_kpi', ['production_report_id' => $id]);
				$arrSelectKpi = [];
				if (!empty($dtProductionReportKpi)) {
					foreach ($dtProductionReportKpi as $kk => $vv) {
						$arrSelectKpi[] = $vv['category_kpi_criteria_id'];
					}
				}
				$data['dtcategoryKpi'] = $dtcategoryKpi;
				$data['arrSelectKpi'] = $arrSelectKpi;
				$data['departments_bp'] = get_table_where('tbldepartments', array('type' => 0));
				$data['dtViolationGroup'] = get_table_where('tbl_violation_group', array('active' => 1));


				$dtDepartment = get_table_where('tbldepartments', array('room_id' => $data['production_report']->id_departments), '', 'row_array');

				$deparment_id = !empty($dtDepartment) ? $dtDepartment['departmentid'] : 0;

				$this->db->from('tbl_kpi_list_criteria_department');
				$this->db->where('tbl_kpi_list_criteria_department.department_id', $deparment_id);
				$this->db->where('tbl_kpi_list_criteria_department.parent_id', 0);
				$dtData = $this->db->get()->result_array();
				$data['kpi_list_criteria_department'] = $dtData;

				$this->db->from('tbl_kpi_list_criteria_department');
				$this->db->where('tbl_kpi_list_criteria_department.parent_id', (!empty($data['production_report']->kpi_list_criteria_department_id) ? $data['production_report']->kpi_list_criteria_department_id : -1));
				$dtData = $this->db->get()->result_array();
				$dtDataNew = [];
				if (!empty($dtData)) {
					foreach ($dtData as $key => $value) {
						$this->db->from('tbl_kpi_list_criteria_department');
						$this->db->where('tbl_kpi_list_criteria_department.parent_id', $value['id']);
						$dtDataChild = $this->db->get()->result_array();
						if (!empty($dtDataChild)) {
							foreach ($dtDataChild as $kk => $vv) {
								$dtDataNew[] = array(
									'id' => $value['id'],
									'id_child_kpi' => $vv['id'],
									'name' => $value['name'],
									'code' => $value['code'],
									'evaluation_criteria' => $vv['evaluation_criteria'],
									'violate' => $vv['violate'],
								);
							}
						} else {
							$dtDataNew[] = array(
								'id' => $value['id'],
								'id_child_kpi' => 0,
								'name' => $value['name'],
								'code' => $value['code'],
								'evaluation_criteria' => $value['evaluation_criteria'],
								'violate' => $value['violate'],
							);
						}
					}
				}
				$data['kpi_list_criteria_department_child'] = $dtDataNew;

				$kpi_list_criteria_department_id_new_vs1 = $data['production_report']->kpi_list_criteria_department_id_childd;
				$kpi_list_criteria_department_id_new = $data['production_report']->kpi_list_criteria_department_id_child;
				if (!empty($kpi_list_criteria_department_id_new_vs1)) {
					$this->db->from('tbl_kpi_list_criteria_department');
					$this->db->where('tbl_kpi_list_criteria_department.id', $kpi_list_criteria_department_id_new_vs1);
				} else {
					$this->db->from('tbl_kpi_list_criteria_department');
					$this->db->where('tbl_kpi_list_criteria_department.id', $kpi_list_criteria_department_id_new);
				}
				$dtData = $this->db->get()->result_array();
				$dtDataNew = [];
				if (!empty($dtData)) {
					foreach ($dtData as $key => $value) {
						$violate = $value['violate'];
						$violate = explode(',', $violate);
						if (!empty($violate)) {
							foreach ($violate as $k => $v) {
								$dtDataNew[] = [
									'id' => $v,
									'name' => $v
								];
							}
						}
					}
				}
				$data['kpi_list_criteria_department_violate'] = $dtDataNew;

				// $data['production_report']
				// jd_tasks	
				$data['jd_tasks'] = get_table_where('tbl_job_detail', [
					'role_id' => $data['production_report']->role_id_jd,
					'status' => 1,
				]);
				$this->db->select("
					tbl_job_detail_child.id as id,
					tbl_job_detail_child.name as name,
					tbl_job_detail_child.type as type,
				");
				$this->db->from('tbl_job_detail_child');
				$this->db->where('tbl_job_detail_child.job_detail_id', $data['production_report']->jd_tasks);
				$job_detail = $this->db->get()->result_array();
				$responsibility = [];
				$jurisdiction = [];
				$requirement = [];
				$competency_standard = [];
				foreach ($job_detail as $key => $value) {
					if ($value['type'] == 1) {
						$responsibility[] = $value;
					}
					if ($value['type'] == 2) {
						$jurisdiction[] = $value;
					}
					if ($value['type'] == 3) {
						$requirement[] = $value;
					}
					if ($value['type'] == 4) {
						$competency_standard[] = $value;
					}
				}
				$data['responsibility'] = $responsibility;
				$data['jurisdiction'] = $jurisdiction;
				$data['requirement'] = $requirement;
				$data['competency_standard'] = $competency_standard;

				$this->db->from('tblproduction_report_jd');
				$this->db->where('tblproduction_report_jd.id_production_report', $id);
				$production_report_jd = $this->db->get()->result_array();
				$data_responsibility = [];
				$data_jurisdiction = [];
				$data_requirement = [];
				$data_competency_standard = [];
				foreach ($production_report_jd as $key => $value) {
					if ($value['type'] == 'jd_responsibility') {
						$data_responsibility[] = $value['id_job_detail_child'];
					}
					if ($value['type'] == 'jd_jurisdiction') {
						$data_jurisdiction[] = $value['id_job_detail_child'];
					}
					if ($value['type'] == 'jd_requirement') {
						$data_requirement[] = $value['id_job_detail_child'];
					}
					if ($value['type'] == 'jd_competency_standard') {
						$data_competency_standard[] = $value['id_job_detail_child'];
					}
				}
				$data['data_responsibility'] = $data_responsibility;
				$data['data_jurisdiction'] = $data_jurisdiction;
				$data['data_requirement'] = $data_requirement;
				$data['data_competency_standard'] = $data_competency_standard;


				$this->load->view('admin/production_report/detail', $data);
			} else {
				if (!empty($this->hide_departments)) {
					// $this->db->where('active_departments', 1);
				}
				$data['departments'] = $this->db->get('tbl_room')->result_array();
				$data['title'] = _l('c_create_production_report');
				$data['id_tasks'] = $this->input->get('id_tasks');
				$data['id_tasks_process'] = $this->input->get('id_tasks_process');
				$data['id_tasks_process_child'] = $this->input->get('id_tasks_process_child');
				$data['audit_id'] = $this->input->get('audit_id');
				$data['id_audit_item'] = $this->input->get('id_audit_item');
				$data['in_and_out_of_work'] = $this->input->get('in_and_out_of_work');
				$data['in_and_out_of_work_item'] = $this->input->get('in_and_out_of_work_item');
				$data['entrance_ticket_id'] = $this->input->get('entrance_ticket_id');
				$data['step'] = $this->input->get('step');

				if (!empty($data['id_tasks'])) {
					$this->db->select([
						'tbltasks.id',
						'tbltasks.name',
						'tbltasks.startdate',
						'tbltasks.category_tasks',
						'tblcategory_tasks.code as code_category_tasks',
						'tblcategory_tasks.content as content_category_tasks'
					]);
					$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbltasks.category_tasks', 'left');
					$data['data_tasks'] = $this->db->get_where('tbltasks', ['tbltasks.id' => $data['id_tasks']])->row();
					if (!empty($data['data_tasks'])) {
						$data['title'] .= ' (Từ phiếu công việc ' . $data['data_tasks']->name . ' - ' . _dt($data['data_tasks']->startdate) . ')';
					}
				}

				$data['quantity_err'] = $this->input->get('quantity_err');
				$data['id_delivery_records'] = $this->input->get('id_delivery_records');
				$data['id_delivery_records_detail'] = $this->input->get('id_delivery_records_detail');
				$data['id_suggest_check_detail'] = $this->input->get('id_suggest_check_detail');
				$data['id_suggest_educate_detail'] = $this->input->get('id_suggest_educate_detail');
				$data['id_suggest_pccc_detail'] = $this->input->get('id_suggest_pccc_detail');
				$data['id_suggest_accreditation_detail'] = $this->input->get('id_suggest_accreditation_detail');
				$data['id_suggest_plan_outsource_detail'] = $this->input->get('id_suggest_plan_outsource_detail');
				$data['id_internal_proposal'] = $this->input->get('id_internal_proposal');
				$data['id_internal_proposal_process'] = $this->input->get('id_internal_proposal_process');
				$data['id_internal_proposal_process_child'] = $this->input->get('id_internal_proposal_process_child');
				$data['category_recommended_id'] = NULL;
				$data['suggest_id'] = NULL;
				$data['suggest_id_detail'] = NULL;
				$data['object_type'] = NULL;
				$data['object_id'] = NULL;
				$data['detail_tasks'] = '';
				$data['department_audit_id'] = '';
				$data['violation_group_id'] = '';
				if (!empty($data['id_delivery_records'])) {
					$this->db->select([
						'tbl_delivery_records.*',
					]);
					$data['data_delivery_records'] = $this->db->get_where(
						'tbl_delivery_records',
						['id' => $data['id_delivery_records']]
					)->row();
					if (!empty($data['data_delivery_records'])) {
						$data['title'] .= ' (Từ phiếu bàn giao ' . $data['data_delivery_records']->reference_no . ' - ' . _dt($data['data_delivery_records']->date) . ')';
						if (!empty($data['data_delivery_records'])) {
							if ($data['data_delivery_records']->type_object == 'productions_orders') {
								$data['id_production_orders'] = $data['data_delivery_records']->id_create;
							}
						}
						if (!empty($data['data_delivery_records']) && $data['data_delivery_records']->type_create == 'productions_orders_detail' && !empty($data['data_delivery_records']->id_create)) {
							$data['id_production_detail'] = $data['data_delivery_records']->id_create;
							//							if(!empty($data['id_production_detail'])) {
							//							}
							$this->db->select('GROUP_CONCAT(tbl_hand_over_task.name SEPARATOR "\n") as name_over_task');
							$this->db->join(
								'tbl_hand_over_task',
								'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id',
								'left'
							);
							$data['described'] = $this->db->get_where(
								'tbl_delivery_records_task',
								['tbl_delivery_records_task.delivery_records_id' => $data['id_delivery_records']]
							)->row('name_over_task');
						}
					}
					if (!empty($data['id_delivery_records_detail'])) {
						$this->db->select('tbl_delivery_records_task.*, tbl_hand_over_task.id_stage, CONCAT(tbl_packaging.code,(IF(tbl_delivery_records_task.task_hand_over_qualified = 1, " (Đạt)", IF(tbl_delivery_records_task.task_hand_over_qualified = 0, "", " (Không Đạt)"))))  as name_over_task');
						$this->db->join(
							'tbl_hand_over_task',
							'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id'
						);
						$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard');
						$data['data_delivery_records_detail'] = $this->db->get_where(
							'tbl_delivery_records_task',
							['tbl_delivery_records_task.id' => $data['id_delivery_records_detail']]
						)->row();
						$data['described'] = $data['data_delivery_records_detail']->name_over_task;
						if (!empty($data['data_delivery_records_detail']->id_stage)) {
							$id_stage = $data['id_stage'] = $data['data_delivery_records_detail']->id_stage;
						}
						$this->db->select('tbl_hand_over_task.name, method');
						$this->db->from('tbl_delivery_records_task');
						$this->db->where('tbl_delivery_records_task.id', $data['id_delivery_records_detail']);
						$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id');
						$hand_over_task = $this->db->get()->row_array();
						if (!empty($hand_over_task)) {
							$data['detail_tasks'] = $hand_over_task['name'] . ' - ' . $hand_over_task['method'];
						}
					} else {
						$this->db->select('GROUP_CONCAT(CONCAT(tbl_packaging.code,(IF(tbl_delivery_records_task.task_hand_over_qualified = 1, " (Đạt)", IF(tbl_delivery_records_task.task_hand_over_qualified = 0, "", " (Không Đạt)")))) separator "\n") as name_over_task');
						$this->db->join(
							'tbl_hand_over_task',
							'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id'
						);
						$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard');
						if (is_numeric($data['quantity_err'])) {
							$this->db->where('task_hand_over_qualified', 2);
						}
						$name_over_task = $this->db->get_where(
							'tbl_delivery_records_task',
							['tbl_delivery_records_task.delivery_records_id' => $data['id_delivery_records']]
						)->row();
						$data['described'] = $name_over_task->name_over_task;
					}
				} elseif (!empty($data['id_suggest_check_detail'])) {
					$this->db->where('id', $data['id_suggest_check_detail']);
					$suggest_check_item = $this->db->get('tbl_suggest_check_item')->row();
					if (!empty($suggest_check_item)) {
						$data['is_note'] = $suggest_check_item->regulation_5s . ' (Không đạt)';
					}
					$this->db->like('name', 'Bàn Giao Hoàn Thành Kiểm Tra Vệ Sinh 5S Ngày');
					$list_id_stage = $this->db->get('tbl_stages')->row();
					if (!empty($list_id_stage)) {
						$data['id_stage'] = !empty($list_id_stage) ? $list_id_stage->id : '';
					}
				} elseif (!empty($data['id_suggest_educate_detail'])) {
					$this->db->select('tbl_evaluate.name_evaluate as name_evaluate,tbl_suggest_educate_item.suggest_plan_educate_id');
					$this->db->where('tbl_suggest_educate_item.id', $data['id_suggest_educate_detail']);
					$this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_educate_item.evaluate_id', 'left');
					$suggest_educate_item = $this->db->get('tbl_suggest_educate_item')->row();
					if (!empty($suggest_educate_item)) {
						$data['is_note'] = $suggest_educate_item->name_evaluate . ' (Không đạt)';
						$data['suggest_id'] = $suggest_educate_item->suggest_plan_educate_id;
						$data['object_id'] = $suggest_educate_item->suggest_plan_educate_id;
						$data['object_type'] = 'suggest_educate';
					}
					$data['suggest_id_detail'] = $data['id_suggest_educate_detail'];
					$this->db->where('name_table', 'tbl_suggest_educate');
					$category_recommended = $this->db->get('tbl_category_recommended')->row();
					if (!empty($category_recommended)) {
						$data['category_recommended_id'] = !empty($category_recommended) ? $category_recommended->id : '';
					}
					$this->db->like('name', 'Bàn Giao Hoàn Thành Dữ Liệu Đào Tạo');
					$list_id_stage = $this->db->get('tbl_stages')->row();
					if (!empty($list_id_stage)) {
						$data['id_stage'] = !empty($list_id_stage) ? $list_id_stage->id : '';
					}
				} elseif (!empty($data['id_suggest_pccc_detail'])) {
					$this->db->select('tbl_suggest_check_item.suggest_check_id,tbl_suggest_check_item.regulation_5s');
					$this->db->where('tbl_suggest_check_item.id', $data['id_suggest_pccc_detail']);
					$suggest_check_item = $this->db->get('tbl_suggest_check_item')->row();
					if (!empty($suggest_check_item)) {
						$data['is_note'] = $suggest_check_item->regulation_5s . ' (Không đạt)';
						$data['suggest_id'] = $suggest_check_item->suggest_check_id;
						$data['object_id'] = $suggest_check_item->suggest_check_id;
						$data['object_type'] = 'suggest_check';
					}
					$data['suggest_id_detail'] = $data['id_suggest_pccc_detail'];
					$this->db->where('name_table', 'tbl_suggest_check');
					$this->db->where('ballot_type', 1);
					$category_recommended = $this->db->get('tbl_category_recommended')->row();
					if (!empty($category_recommended)) {
						$data['category_recommended_id'] = !empty($category_recommended) ? $category_recommended->id : '';
					}
				} elseif (!empty($data['id_suggest_accreditation_detail'])) {
					$this->db->select('tbl_suggest_check_item.suggest_check_id,tbl_suggest_check_item.regulation_5s');
					$this->db->where('tbl_suggest_check_item.id', $data['id_suggest_accreditation_detail']);
					$suggest_check_item = $this->db->get('tbl_suggest_check_item')->row();
					if (!empty($suggest_check_item)) {
						$data['is_note'] = $suggest_check_item->regulation_5s . ' (Không đạt)';
						$data['suggest_id'] = $suggest_check_item->suggest_check_id;
						$data['object_id'] = $suggest_check_item->suggest_check_id;
						$data['object_type'] = 'suggest_check';
					}
					$data['suggest_id_detail'] = $data['id_suggest_accreditation_detail'];
					$this->db->where('name_table', 'tbl_suggest_check');
					$this->db->where('ballot_type', 2);
					$category_recommended = $this->db->get('tbl_category_recommended')->row();
					if (!empty($category_recommended)) {
						$data['category_recommended_id'] = !empty($category_recommended) ? $category_recommended->id : '';
					}
				} elseif (!empty($data['id_suggest_plan_outsource_detail'])) {
					$data['suggest_id_detail'] = $data['id_suggest_plan_outsource_detail'];

					$this->db->where('id', $data['suggest_id_detail']);
					$data['suggest_id'] = $this->db->get('tbl_suggest_plan_outsource_item')->row('suggest_plan_outsource_id');

					$this->db->where('name_table', 'tbl_suggest_plan_outsource');
					$category_recommended = $this->db->get('tbl_category_recommended')->row();
					if (!empty($category_recommended)) {
						$data['category_recommended_id'] = !empty($category_recommended) ? $category_recommended->id : '';
					}
					$data['object_type'] = 'suggest_plan_outsource_detail';
					$data['object_id'] = $data['suggest_id'];
				} elseif (!empty($_GET['object_type']) && $_GET['object_type'] == 'suggest_evaluate_item') {
					$this->db->like('name', 'Bàn Giao Hoàn Thành Đánh Giá');
					$list_id_stage = $this->db->get('tbl_stages')->row();
					if (!empty($list_id_stage)) {
						$data['id_stage'] = !empty($list_id_stage) ? $list_id_stage->id : '';
					}
					$this->db->where('id', $_GET['object_id']);
					$suggest_evaluate_item = $this->db->get('tbl_suggest_evaluate_item')->row();
					if (!empty($suggest_evaluate_item)) {
						$data['is_note'] = $suggest_evaluate_item->content;
					}
				} else if (!empty($_GET['object_type']) && $_GET['object_type'] == 'suggest_repalce') {
					$this->db->where('name_table', 'tbl_suggest_repalce');
					$data['category_recommended_id'] = $this->db->get('tbl_category_recommended')->row('id');
					if (!empty($_GET['object_id'])) {
						$data['object_id'] = $_GET['object_id'];
					}
				} elseif (!empty($data['id_internal_proposal_process_child'])) {
					$this->db->where('id', $data['id_internal_proposal_process_child']);
					$internal_proposal_process_child = $this->db->get('tbl_internal_proposal_process_child')->row_array();
					if (!empty($internal_proposal_process_child)) {
						$data['detail_tasks'] = $internal_proposal_process_child['name'];
					}
				} elseif (!empty($data['id_tasks_process_child'])) {
					$this->db->where('id', $data['id_tasks_process_child']);
					$tasks_process_child = $this->db->get('tbl_tasks_process_child')->row_array();
					if (!empty($tasks_process_child)) {
						$data['detail_tasks'] = $tasks_process_child['name'];
						$this->db->where('id_task_procedure', $tasks_process_child['id_category_tasks_process']);
						$kpi_list_criteria_department = $this->db->get('tbl_kpi_list_criteria_department')->row_array();
						if ($kpi_list_criteria_department) {
							$this->db->where('departmentid', $kpi_list_criteria_department['department_id']);
							$department_audit_id = $this->db->get('tbldepartments')->row_array();
							if ($department_audit_id) {
								$data['department_audit_id'] = $department_audit_id['room_id'];
							}
							$counts = 0;
							$parent_id = $kpi_list_criteria_department['id'];
							$dtData = [];

							while ($counts <= 3) {
								$counts++;
								if ($parent_id > 0) {
									$this->db->from('tbl_kpi_list_criteria_department');
									$this->db->where('tbl_kpi_list_criteria_department.id', $parent_id);
									$dtData = $this->db->get()->result_array();

									if (!empty($dtData)) {
										$parent_id = $dtData[0]['parent_id'];
									}
									if (empty($dtData)) {
										$counts = 5;
									}
								}
							}

							$data['kpi_list_criteria_department'] = $dtData;


							$this->db->from('tbl_kpi_list_criteria_department');
							$this->db->where('tbl_kpi_list_criteria_department.id', $kpi_list_criteria_department['id']);
							$dtData = $this->db->get()->result_array();

							$dtDataNew = [];
							if (!empty($dtData)) {
								foreach ($dtData as $key => $value) {
									$this->db->from('tbl_kpi_list_criteria_department');
									$this->db->where('tbl_kpi_list_criteria_department.parent_id', $value['id']);
									$dtDataChild = $this->db->get()->result_array();
									if (!empty($dtDataChild)) {
										foreach ($dtDataChild as $kk => $vv) {
											$dtDataNew[] = array(
												'id' => $value['id'],
												'id_child_kpi' => $vv['id'],
												'name' => $value['name'],
												'code' => $value['code'],
												'evaluation_criteria' => $vv['evaluation_criteria'],
												'violate' => $vv['violate'],
											);
										}
									} else {
										$dtDataNew[] = array(
											'id' => $value['id'],
											'id_child_kpi' => 0,
											'name' => $value['name'],
											'code' => $value['code'],
											'evaluation_criteria' => $value['evaluation_criteria'],
											'violate' => $value['violate'],
										);
									}
								}

								$this->db->from('tbl_kpi_list_criteria_department');
								$this->db->where('tbl_kpi_list_criteria_department.id', $kpi_list_criteria_department['id']);
								$dtData = $this->db->get()->result_array();
								if (!empty($dtData)) {
									$dtDataNewviolate = [];
									foreach ($dtData as $key => $value) {
										$violate = $value['violate'];
										$violate = explode(',', $violate);
										if (!empty($violate)) {
											foreach ($violate as $k => $v) {
												$dtDataNewviolate[] = [
													'id' => $v,
													'name' => $v
												];
											}
										}
									}
								}
								$data['kpi_list_criteria_department_violate'] = $dtDataNewviolate;
							}
							$data['kpi_list_criteria_department_child'] = $dtDataNew;
						}
					}
				} elseif (!empty($data['id_audit_item'])) {
					$this->db->where('id', $data['id_audit_item']);
					$audit_checklist = $this->db->get('tbl_audit_checklist')->row_array();
					if (!empty($audit_checklist)) {
						$data['detail_tasks'] = $audit_checklist['item_text'];
					}
					if (!empty($data['audit_id'])) {
						$this->db->where('id', $data['audit_id']);
						$audit = $this->db->get('tbl_audit')->row_array();
						if (!empty($audit)) {
							$data['department_audit_id'] = $audit['dept_id'];
						}
					}
				} elseif (!empty($data['in_and_out_of_work_item'])) {
					$this->db->where('id', $data['in_and_out_of_work_item']);
					$in_and_out_of_work_item = $this->db->get('tbl_in_and_out_of_work_items')->row_array();
					if (!empty($in_and_out_of_work_item)) {
						$data['detail_tasks'] = $in_and_out_of_work_item['detail_reference_no'];
					}
				}


				$data['id_maintenance'] = $this->input->get('maintenance');
				if (!empty($data['id_maintenance'])) {
					$this->db->select([
						'tblmaintenance_ticket.*',
					]);
					$data['data_maintenance_ticket'] = $this->db->get_where(
						'tblmaintenance_ticket',
						['id' => $data['id_maintenance']]
					)->row();
					$data['title'] .= ' (Từ phiếu bảo trì ' . $data['data_maintenance_ticket']->name . ' - ' . _dt($data['data_maintenance_ticket']->date) . ')';
					$data['id_category'] = $this->input->get('id_category');
					if (!empty($data['id_category'])) {
						$this->db->select('GROUP_CONCAT(tblcategory_maintenance.name separator "\n") as described');
						$this->db->join(
							'tblcategory_maintenance',
							'tblcategory_maintenance.id = tblmaintenance_stick_category.id_category'
						);
						$this->db->where('tblmaintenance_stick_category.maintenance_stick', $data['id_maintenance']);
						$this->db->where('tblmaintenance_stick_category.id_category', $data['id_category']);
						$data['described'] = $this->db->get_where('tblmaintenance_stick_category')->row('described');
					} else {
						$this->db->select('GROUP_CONCAT(tblcategory_maintenance.name separator "\n") as described');
						$this->db->join(
							'tblcategory_maintenance',
							'tblcategory_maintenance.id = tblmaintenance_stick_category.id_category'
						);
						$this->db->where('tblmaintenance_stick_category.maintenance_stick', $data['id_maintenance']);
						$data['described'] = $this->db->get_where('tblmaintenance_stick_category')->row('described');
					}
				}
				$data['category_tasks'] = $this->site_model->getCategoryTasks();
				$defaultValue = $this->input->get();
				if (!empty($defaultValue['default_value']['id_order'])) {
					// var_dump($defaultValue['default_value']['id_order']); die;
					$data['default_value'] = new stdClass();
					$data['default_value']->id_orders = $defaultValue['default_value']['id_order'];
				}
				if (empty($data['data_tasks'])) {
					$where = [];
					$data['data_roles'] = [];
					$this->get_parent_role(0, $data['data_roles'], $where, 0);
				}
				$data['departments_bp'] = get_table_where('tbldepartments', array('type' => 0));
				$data['dtViolationGroup'] = get_table_where('tbl_violation_group', array('active' => 1));

				$this->load->view('admin/production_report/detail', $data);
			}
		}
	}

	public function get_list_category_tasks()
	{
		$data = [];
		$role_id = $this->input->post('role_id');
		$id_departments = $this->input->post('id_departments');
		$internal_proposal = $this->input->post('internal_proposal');
		if (!empty($role_id)) {
			$roles_parent = [];
			$this->get_parent_role($role_id, $roles_parent, [], 0);
			$list_role = [$role_id];
			if (!empty($roles_parent)) {
				foreach ($roles_parent as $key => $value) {
					$list_role[] = $value['roleid'];
				}
			}
		}
		$this->db->select('id, code, content, departments');
		if (!empty($id_departments)) {
			if ($id_departments != 17 && $id_departments != 18) {
				$this->db->where('FIND_IN_SET(' . $id_departments . ', tblcategory_tasks.departments)');
			}
		}
		if (!empty($list_role)) {
			if (!empty($list_role)) {
				$this->db->group_start();
				$this->db->where_in('role_id_1', $list_role);
				$this->db->or_where_in('role_id_2', $list_role);
				$this->db->group_end();
			}
		}
		// if (!empty($internal_proposal)) {
		// 	$this->db->group_start();
		// 	$this->db->like('content', 'đề xuất');
		// 	$this->db->or_like('code', 'đề xuất');
		// 	$this->db->or_where('code like "DX%"', false, false);
		// 	$this->db->or_where('code like "ĐX%"', false, false);
		// 	$this->db->group_end();
		// }
		$this->db->where('tblcategory_tasks.hide', 0);
		$category_tasks = $this->db->get('tblcategory_tasks')->result_array();
		$data['category_tasks'] = $category_tasks;

		$dtDepartment = get_table_where('tbldepartments', array('room_id' => $id_departments), '', 'row_array');

		$deparment_id = !empty($dtDepartment) ? $dtDepartment['departmentid'] : 0;

		$this->db->from('tbl_kpi_list_criteria_department');
		$this->db->where('tbl_kpi_list_criteria_department.department_id', $deparment_id);
		$this->db->where('tbl_kpi_list_criteria_department.parent_id', 0);
		$dtData = $this->db->get()->result_array();
		$data['kpi_list_criteria_department'] = $dtData;
		echo json_encode($data);
		die();
	}

	public function get_list_kpi_list_criteria_department()
	{
		$data = [];
		$kpi_list_criteria_department_id = !empty($this->input->post('kpi_list_criteria_department_id')) ? $this->input->post('kpi_list_criteria_department_id') : -1;

		$this->db->from('tbl_kpi_list_criteria_department');
		$this->db->where('tbl_kpi_list_criteria_department.parent_id', $kpi_list_criteria_department_id);
		$dtData = $this->db->get()->result_array();
		$dtDataNew = [];
		if (!empty($dtData)) {
			foreach ($dtData as $key => $value) {
				$this->db->from('tbl_kpi_list_criteria_department');
				$this->db->where('tbl_kpi_list_criteria_department.parent_id', $value['id']);
				$dtDataChild = $this->db->get()->result_array();
				if (!empty($dtDataChild)) {
					foreach ($dtDataChild as $kk => $vv) {
						$dtDataNew[] = array(
							'id' => $value['id'],
							'id_child_kpi' => $vv['id'],
							'name' => $value['name'],
							'code' => $value['code'],
							'evaluation_criteria' => $vv['evaluation_criteria'],
							'violate' => $vv['violate'],
						);
					}
				} else {
					$dtDataNew[] = array(
						'id' => $value['id'],
						'id_child_kpi' => 0,
						'name' => $value['name'],
						'code' => $value['code'],
						'evaluation_criteria' => $value['evaluation_criteria'],
						'violate' => $value['violate'],
					);
				}
			}
		}
		$data['kpi_list_criteria_department'] = $dtDataNew;
		echo json_encode($data);
		die();
	}

	public function kpi_list_criteria_department_violate()
	{
		$data = [];
		$kpi_list_criteria_department_id_child = !empty($this->input->post('kpi_list_criteria_department_id_child')) ? $this->input->post('kpi_list_criteria_department_id_child') : 0;
		if (!empty($kpi_list_criteria_department_id_child)) {
			$kpi_list_criteria_department_id_child = explode('-', $kpi_list_criteria_department_id_child);
			$kpi_list_criteria_department_id_new = $kpi_list_criteria_department_id_child[0];
			$kpi_list_criteria_department_id_new_vs1 = $kpi_list_criteria_department_id_child[1];
		}
		if (!empty($kpi_list_criteria_department_id_new_vs1)) {
			$this->db->from('tbl_kpi_list_criteria_department');
			$this->db->where('tbl_kpi_list_criteria_department.id', $kpi_list_criteria_department_id_new_vs1);
		} else {
			$this->db->from('tbl_kpi_list_criteria_department');
			$this->db->where('tbl_kpi_list_criteria_department.id', $kpi_list_criteria_department_id_new);
		}
		$dtData = $this->db->get()->result_array();
		$dtDataNew = [];
		if (!empty($dtData)) {
			foreach ($dtData as $key => $value) {
				$violate = $value['violate'];
				$violate = explode(',', $violate);
				if (!empty($violate)) {
					foreach ($violate as $k => $v) {
						$dtDataNew[] = [
							'id' => $v,
							'name' => $v
						];
					}
				}
			}
		}
		$data['kpi_list_criteria_department_violate'] = $dtDataNew;
		echo json_encode($data);
		die();
	}

	public function get_list_category_tasks_new()
	{
		$role_id = $this->input->post('role_id');
		$internal_proposal = $this->input->post('internal_proposal');
		$this->db->select('id, code, content, departments');
		if (!empty($id_departments)) {
		}
		$this->db->where('tblcategory_tasks.hide', 0);
		$category_tasks = $this->db->get('tblcategory_tasks')->result_array();
		echo json_encode($category_tasks);
		die();
	}

	public function get_parent_role($id_parent = 0, &$array_category = [], $where = [], $level = 0)
	{
		if (is_numeric($level)) {
			if (!empty($where)) {
				$this->db->where($where);
			}
			if (!empty($this->hide_role)) {
				$this->db->where('active_role', 1);
			}
			$this->db->where('tblroles.type', 0);
			$this->db->where(array('roles_parent' => $id_parent));
			$current_level = $this->db->get('tblroles')->result_array();
			if ($current_level) {
				foreach ($current_level as $key => $value) {
					$sub = "";
					for ($i = 0; $i < $level; $i++) {
						$sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					$sub .= "&#10154;";
					$current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
					array_push($array_category, $current_level[$key]);
					$this->get_parent_role($value['roleid'], $array_category, $where, $level + 1);
				}
			} else {
				return;
			}
		}
	}

	public function get_list_role_search($id_board_search = 0, $id_block_search = 0, $id_departments_search = 0)
	{
		$where = [];
		$id_board_search = $this->input->post('id_board_search');
		$id_block_search = $this->input->post('id_block_search');
		$id_departments_search = $this->input->post('id_departments_search');
		if (!empty($id_departments_search)) {
			$where['id_room'] = $id_departments_search;
		}
		if (!empty($id_block_search)) {
			$where['id_block'] = $id_block_search;
		}
		if (!empty($id_board_search)) {
			$where['id_board'] = $id_board_search;
		}
		if (!empty($where)) {
			$this->db->where($where);
		}
		$where['type'] = 0;
		$this->db->where('active_role', 1);
		$current_level = $this->db->get('tblroles')->result_array();
		// $data['roles_parent'] = [];
		// $this->get_parent_role(0, $data['roles_parent'], $where, 0);
		echo json_encode($current_level);
		die();
	}
	// public function get_list_role_search($id_board_search = 0, $id_block_search = 0, $id_departments_search = 0)
	// {
	// 	$where = [];
	// 	if (!empty($id_departments_search)) {
	// 		$where['id_room'] = $id_departments_search;
	// 	}
	// 	if (!empty($id_block_search)) {
	// 		$where['id_block'] = $id_block_search;
	// 	}
	// 	if (!empty($id_board_search)) {
	// 		$where['id_board'] = $id_board_search;
	// 	}
	// 	$data['roles_parent'] = [];
	// 	$this->get_parent_role(0, $data['roles_parent'], $where, 0);
	// 	echo json_encode($data['roles_parent']);
	// 	die();
	// }
	public function get_list_role($departments_id = 0)
	{
		$where = [];
		if (!empty($departments_id)) {
			$where['id_room'] = $departments_id;
		}
		$data['roles_parent'] = [];
		$this->get_parent_role(0, $data['roles_parent'], $where, 0);
		echo json_encode($data['roles_parent']);
		die();
	}

	public function search_production_detail($id = "")
	{
		$data = [];
		$term = $this->input->get('term', true);
		$limit = get_option('select2_limit');
		$data['results'] = [];
		if (empty($id)) {
			$this->db->select('
				tbl_productions_orders_details.id as id,
				tbl_productions_orders_details.reference_no as code,
				CONCAT(type_items, "__", tbl_productions_orders_items.items_id) as id_item,
				tbl_productions_orders_items.items_code as items_code,
				tbl_productions_orders_items.items_name as items_name,
				tbl_productions_orders_items.quantity as quantity,
				tbl_productions_orders_items.id as id_orders_item,
			', false);
			$this->db->from('tbl_productions_orders_details');
			if (!empty($term)) {
				$this->db->like('tbl_productions_orders_details.reference_no', $term);
			}
			$this->db->join(
				'tbl_productions_orders_items',
				'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id'
			);
			//			$this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND object_type = "orders"');
			$this->db->limit($limit);
			$productions_detail = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Lệnh SX chi tiết'), 'children' => $productions_detail];
		} elseif (!empty($id)) {
			$this->db->select('
				tbl_productions_orders_details.id as id,
				tbl_productions_orders_details.reference_no as code,
				CONCAT(type_items, "__", tbl_productions_orders_items.items_id) as id_item,
				tbl_productions_orders_items.items_code as items_code,
				tbl_productions_orders_items.items_name as items_name,
				tbl_productions_orders_items.quantity as quantity,
				tbl_productions_orders_items.id as id_orders_item,
			', false);
			$this->db->where('tbl_productions_orders_details.id', $id);
			$this->db->from('tbl_productions_orders_details');
			$this->db->join(
				'tbl_productions_orders_items',
				'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id'
			);
			if (!empty($term)) {
				$this->db->like('tbl_productions_orders_details.reference_no', $term);
			}
			$this->db->limit($limit);
			$productions_detail = $this->db->get()->row();
			$data['results'] = $productions_detail;
		}
		echo json_encode($data);
	}

	public function search_production_orders($id = "")
	{
		$data = [];
		$term = $this->input->get('term', true);
		$limit = get_option('select2_limit');
		$data['results'] = [];
		if (empty($id)) {
			$this->db->select('
				tbl_productions_orders.id as id,
				tbl_productions_orders.reference_no as code,
				tbl_productions_orders.productions_plan_reference_no as code_orders,
				(SELECT GROUP_CONCAT(distinct CONCAT(" - ", items_code, " (", items_name, ")") separator "<br/>") FROM tbl_productions_orders_items WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id) as list_items_name
			', false);
			$this->db->from('tbl_productions_orders');
			if (!empty($term)) {
				$this->db->like('tbl_productions_orders.reference_no', $term);
			}
			$this->db->limit($limit);
			$productions_orders = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Lệnh SX tổng'), 'children' => $productions_orders];
		} elseif (!empty($id)) {
			$this->db->select('
				tbl_productions_orders.id as id,
				tbl_productions_orders.reference_no as code,
				tbl_productions_orders.productions_plan_reference_no as code_orders,
				(SELECT GROUP_CONCAT(distinct CONCAT(" - ", items_code, " (", items_name, ")") separator "<br/>") FROM tbl_productions_orders_items WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id) as list_items_name
			', false);
			$this->db->where('tbl_productions_orders.id', $id);
			$this->db->from('tbl_productions_orders');
			if (!empty($term)) {
				$this->db->like('tbl_productions_orders.reference_no', $term);
			}
			$this->db->limit($limit);
			$productions_orders = $this->db->get()->row();
			$data['results'] = $productions_orders;
		}
		echo json_encode($data);
		die();
	}

	public function search_orders($id = "")
	{
		$data = [];
		$term = $this->input->get('term', true);
		$limit = get_option('select2_limit');
		$data['results'] = [];
		if (empty($id)) {
			$this->db->select('
				tbl_orders.id as id,
				tbl_orders.reference_no as code,
				customer_name
			', false);
			$this->db->from('tbl_orders');
			if (!empty($term)) {
				$this->db->like('tbl_orders.reference_no', $term);
			}
			$this->db->limit($limit);
			$orders = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Đơn hàng bán'), 'children' => $orders];
		} elseif (!empty($id)) {
			$this->db->where('id', $id);
			$this->db->select('
				tbl_orders.id as id,
				tbl_orders.reference_no as code,
				customer_name
			', false);
			$this->db->from('tbl_orders');
			if (!empty($term)) {
				$this->db->like('tbl_orders.reference_no', $term);
			}
			$this->db->limit($limit);
			$orders = $this->db->get()->row();
			$data['results'] = $orders;
		}
		echo json_encode($data);
	}

	public function get_stage($productions_detail_id = '')
	{
		$stages = [];
		if (!empty($productions_detail_id)) {
			$this->db->select('tbl_stages.name as name, tbl_productions_orders_items_stages.id as id');
			$this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_detail_id);
			$this->db->join(
				'tbl_productions_orders_items_stages',
				'tbl_productions_orders_items_stages.stage_id = tbl_stages.id'
			);
			$this->db->order_by('number', 'asc');
			$stages = $this->db->get('tbl_stages')->result_array();
		}
		echo json_encode($stages);
		die();
	}

	public function pdf($id = '')
	{
		if (!$this->print) {
			access_denied();
		}
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tblproduction_report.id_branch', $list_branch);
					$this->db->group_end();
					$this->db->where('id', $id);
					$ktData = $this->db->get('tblproduction_report')->row();
				} else {
					$ktData = false;
				}
				if (empty($ktData)) {
					access_denied();
				}
			}
		}
		ob_end_clean();
		$data = [];
		$imgcheck = '<img style="width:10%;" src="' . base_url('uploads/check.png') . '" width="10" height="10">';
		$imgnocheck = '◻️';
		$this->db->select([
			'tblproduction_report.id as id',
			'tblproduction_report.date as date',
			'tbl_room.name as name_departments',
			'tbl_productions_orders.reference_no as reference_no',
			'tblproduction_report.quantity_pcs as quantity_pcs',
			'tbl_stages.name as stage',
			'(
				SELECT
				GROUP_CONCAT(distinct CONCAT("&nbsp;&nbsp;&nbsp;- ", items_code) separator "<br/>")
				FROM tbl_productions_orders_items
				WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
			) as list_items_name',
			//			'tbl_productions_orders_items.items_code as items_code',
			'tblproduction_report.time_of_recording as time_of_recording',
			'tblproduction_report.*',
			'tbl_orders.reference_no as code_orders'
		]);
		//		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblproduction_report.id_departments', 'left');
		$this->db->join('tbl_room', 'tbl_room.id = tblproduction_report.id_departments', 'left');
		//		$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tblproduction_report.id_production_detail', 'left');
		$this->db->join(
			'tbl_productions_orders',
			'tbl_productions_orders.id = tblproduction_report.id_production_orders',
			'left'
		);
		//		$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
		$this->db->join('tbl_stages', 'tbl_stages.id = tblproduction_report.production_stage', 'left');
		$this->db->join('tbl_orders', 'tbl_orders.id = tblproduction_report.id_orders', 'left');
		$this->db->where('tblproduction_report.id', $id);
		$items = $this->db->get('tblproduction_report')->row_array();
		$htmlReason = '';
		$items['material'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'material'
		])->result_array();
		if (!empty($items['material'])) {
			$html_material = '';
			foreach ($items['material'] as $key => $value) {
				$html_material .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<table cellspacing="2" width="100%;"><tr><td colspan="2">Nguyên phụ liệu (Material)</td></tr>' . $html_material . '</table>';;
		}
		$items['man'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'man'
		])->result_array();
		if (!empty($items['man'])) {
			$htmlReason .= '<br/>';
			$html_man = '';
			foreach ($items['man'] as $key => $value) {
				$html_man .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<table cellspacing="2" width="100%;"><tr><td colspan="2">Nhân lực (Man)</td></tr>' . $html_man . '</table>';
		}
		$items['machine'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'machine'
		])->result_array();
		if (!empty($items['machine'])) {
			$htmlReason .= '<br/>';
			$html_machine = '';
			foreach ($items['machine'] as $key => $value) {
				$html_machine .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<br/><table cellspacing="2" width="100%;"><tr><td colspan="2">Máy móc (Machine)</td></tr>' . $html_machine . '</table>';
		}
		$items['method'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'method'
		])->result_array();
		if (!empty($items['method'])) {
			$htmlReason .= '<br/>';
			$html_method = '';
			foreach ($items['method'] as $key => $value) {
				$html_method .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<br/><table cellspacing="2" width="100%;"><tr><td colspan="2">Phương pháp (Method)</td></tr>' . $html_method . '</table>';
		}
		$items['procedure'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'procedure'
		])->result_array();
		$items['fix'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'fix'
		])->result_array();
		$viewImg = '';
		$list_files = $this->db->get_where('tblfiles', [
			'rel_type' => 'production_report',
			'rel_id' => $id
		])->result_array();
		if (!empty($list_files)) {
			$viewImg = '<br><table cellspacing="1" width="100%;">';
			$arrayView = [];
			foreach ($list_files as $key => $value) {
				$arrayView[] = '<td><img style="height:70px;width: 100px;" src="' . base_url($value['file_name']) . '"/></td>';
				if (count($arrayView) == 2) {
					$viewImg .= '<tr>' . implode('', $arrayView) . '</tr>';
					$arrayView = [];
				}
			}
			if (!empty($arrayView)) {
				if (count($arrayView) == 2) {
					$viewImg .= '<tr>' . implode('', $arrayView) . '</tr>';
					$arrayView = [];
				} else {
					$viewImg .= '<tr>' . implode('', $arrayView) . '<td></td></tr>';
					$arrayView = [];
				}
			}
			$viewImg .= '</table>';
		}
		$htmlProcedure = '';
		if (!empty($items['procedure'])) {
			$html_procedure = '';
			foreach ($items['procedure'] as $key => $value) {
				$html_procedure .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlProcedure .= '<table cellspacing="2" width="100%;">' . $html_procedure . '</table>';
		}
		$htmlFix = '';
		if (!empty($items['fix'])) {
			$html_fix = '';
			foreach ($items['fix'] as $key => $value) {
				$html_fix .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlFix .= '<table cellspacing="2" width="100%;">' . $html_fix . '</table>';
		}
		// $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
		$data['title'] = lang('tnh_print_order');
		$data['type'] = 'P';
		$data['img'] = '';
		$bodyItems = '';
		$totalBox = 0;
		$day = date_format(date_create($items['date']), 'd');
		$month = date_format(date_create($items['date']), 'm');
		$year = date_format(date_create($items['date']), 'Y');
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('PHIẾU BÁO CÁO SỰ KHÔNG PHÙ HỢP') . '</h1></td>
                </tr>
            </table>
            <br><br><br><br>
            <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <tr>
                    <td colspan="2">Ngày ' . $day . ' Tháng ' . $month . ' Năm ' . $year . '</td>
                </tr>
                <tr>
                    <td>Bộ phận: ' . $items['name_departments'] . '</td>
                    <td>Công đoạn phát hiện: ' . $items['stage'] . '</td>
                </tr>
                <tr nobr="true">
                    <td>LSX Tổng: ' . $items['reference_no'] . '     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="float: right;">Số lượng ' . number_format_data($items['quantity_pcs']) . ' pcs</span></td>
                    <td>
						<table>
							<tr>
								<td>Chạy mẫu : </td>
								<td>' . (!empty($items['type_stage_1']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    </td>
                </tr>
                <tr nobr="true">
                   <td>' . (!empty($items['id_orders']) ? ('Đơn hàng bán: ' . $items['code_orders']) : '') . '</td>
					<td>
						<table>
							<tr>
								<td>Chạy hàng + mẫu :</td>
								<td>' . (!empty($items['type_stage_2']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
					</td>
                </tr>
                 <tr nobr="true">
                    <td>Mã SP: ' . (!empty($items['list_items_name']) ? ('<br/>' . $items['list_items_name']) : '-') . '</td>
                    <td>
                    	<table>
							<tr>
								<td>Chạy hàng :</td>
								<td>' . (!empty($items['type_stage_3']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
							<tr>
								<td>Chạy bù hàng :</td>
								<td>' . (!empty($items['type_stage_3']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    </td>
                </tr>
            </table>
            <table class="" cellspacing="0" cellpadding="5" border="1" width="100%;" style="width: 100%;">
                <tr nobr="true">
                    <td class="bold text-center">Nội dung KPH</td>
                    <td class="bold text-center">Hành động xử lý lập tức </td>
                    <td class="bold text-center">Nguyên nhân & khắc phục</td>
                </tr>
                <tr>
                    <td class="text-left">Thời điểm ghi nhận : ' . _dt($items['time_of_recording']) . '</td>
                    <td rowspan="10">
                    <br/>
                    <br/>
                    <br/>
                    	<table>
							<tr>
								<td>Chấp nhận :</td>
								<td>' . (!empty($items['action_now_1']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    <br/>
                    <br/>
						<table>
							<tr>
								<td>Loại bỏ :</td>
								<td>' . (!empty($items['action_now_2']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    <br/>
                    <br/>
						<table>
							<tr>
								<td>Làm lại :</td>
								<td>' . (!empty($items['action_now_3']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
						
                    <br/>
                    <br/>
						<table>
							<tr>
								<td>Khác :</td>
								<td>' . (!empty($items['action_now_4']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
					</td>
                    <td>Nguyên nhân:</td>
                </tr>
                <tr>
                	<td></td>
                	<td rowspan="4" style="text-align: left">' . $htmlReason . '</td>
				</tr>
				<tr>
                	<td>Mô tả sự KHP :</td>
				</tr>
            	<tr>
                	<td rowspan="1"><i>' . $items['described'] . '</i>' . $viewImg . '</td>
				</tr>
				<tr>
                	<td>Số lượng : ' . number_format_data($items['quantity']) . '</td>
				</tr>
				<tr>
                	<td></td>
                	<td>Xử lý:</td>
				</tr>
				<tr>
                	<td>Ghi chú:</td>
                	<td rowspan="2"><i>' . $htmlProcedure . '</i></td>
				</tr>
				<tr>
                	<td rowspan="1"><i>' . $items['note'] . '</i></td>
				</tr>
				<tr>
                	<td></td>
                	<td>Khắc phục:</td>
				</tr>
				<tr>
                	<td></td>
                	<td>' . $htmlFix . '</td>
				</tr>
				<tr><td colspan="3">
					<table>
						<tr><td class="text-center">Người BC</td><td class="text-center">QA</td><td class="text-center">QLSX</td></tr>
					</table>
				</td></tr>
            </table>
        ';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$qrStyle = array(
			'border' => 0,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0, 0, 0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);
		$data['qrCode'] = [
			'code' => 'production_report||' . $id,
			'type' => 'QRCODE,Q',
			'x' => 170,
			'y' => 28,
			'width' => 28,
			'height' => 28,
			'style' => $qrStyle,
			'align' => 'N',
		];
		$pdf = @print_pdf_tnh_reports($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}

	private function get_content($id = '', $type = '1')
	{
		$imgcheck = '<img style="width:10px" src="' . base_url('uploads/check.png') . '" width="10" height="10">';
		$imgnocheck = '◻️';
		$this->db->select([
			'tblproduction_report.id as id',
			'tblproduction_report.date as date',
			'tbl_room.name as name_departments',
			'tbl_productions_orders.reference_no as reference_no',
			'tblproduction_report.quantity_pcs as quantity_pcs',
			'tbl_stages.name as stage',
			'(
				SELECT
				GROUP_CONCAT(distinct CONCAT("&nbsp;&nbsp;&nbsp;- ", items_code) separator "<br/>")
				FROM tbl_productions_orders_items
				WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
			) as list_items_name',
			//			'tbl_productions_orders_items.items_code as items_code',
			'tblproduction_report.time_of_recording as time_of_recording',
			'tblproduction_report.*',
			'tbl_orders.reference_no as code_orders'
		]);
		$this->db->join('tbl_room', 'tbl_room.id = tblproduction_report.id_departments', 'left');
		//		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblproduction_report.id_departments', 'left');
		//		$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tblproduction_report.id_production_detail', 'left');
		$this->db->join(
			'tbl_productions_orders',
			'tbl_productions_orders.id = tblproduction_report.id_production_orders',
			'left'
		);
		//		$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
		$this->db->join('tbl_stages', 'tbl_stages.id = tblproduction_report.production_stage', 'left');
		$this->db->join('tbl_orders', 'tbl_orders.id = tblproduction_report.id_orders', 'left');
		$this->db->where('tblproduction_report.id', $id);
		$items = $this->db->get('tblproduction_report')->row_array();
		$htmlReason = '';
		$items['material'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'material'
		])->result_array();
		if (!empty($items['material'])) {
			$html_material = '';
			foreach ($items['material'] as $key => $value) {
				$html_material .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<table cellspacing="2" width="100%;"><tr><td colspan="2">Nguyên phụ liệu (Material)</td></tr>' . $html_material . '</table>';;
		}
		$items['man'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'man'
		])->result_array();
		if (!empty($items['man'])) {
			$htmlReason .= '<br/>';
			$html_man = '';
			foreach ($items['man'] as $key => $value) {
				$html_man .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<table cellspacing="2" width="100%;"><tr><td colspan="2">Nhân lực (Man)</td></tr>' . $html_man . '</table>';
		}
		$items['machine'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'machine'
		])->result_array();
		if (!empty($items['machine'])) {
			$htmlReason .= '<br/>';
			$html_machine = '';
			foreach ($items['machine'] as $key => $value) {
				$html_machine .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<br/><table cellspacing="2" width="100%;"><tr><td colspan="2">Máy móc (Machine)</td></tr>' . $html_machine . '</table>';
		}
		$items['method'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'method'
		])->result_array();
		if (!empty($items['method'])) {
			$htmlReason .= '<br/>';
			$html_method = '';
			foreach ($items['method'] as $key => $value) {
				$html_method .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlReason .= '<br/><table cellspacing="2" width="100%;"><tr><td colspan="2">Phương pháp (Method)</td></tr>' . $html_method . '</table>';
		}
		$items['procedure'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'procedure'
		])->result_array();
		$htmlProcedure = '';
		if (!empty($items['procedure'])) {
			$html_procedure = '';
			foreach ($items['procedure'] as $key => $value) {
				$html_procedure .= '<tr><td width="10%;">' . (!empty($value['ischeck']) ? $imgcheck : $imgnocheck) . '</td><td width="90%;">' . $value['name'] . '</td></tr>';
			}
			$htmlProcedure .= '<table cellspacing="2" width="100%;">' . $html_procedure . '</table>';
		}
		$day = date_format(date_create($items['date']), 'd');
		$month = date_format(date_create($items['date']), 'm');
		$year = date_format(date_create($items['date']), 'Y');
		$title = '';
		if ($type == '1') {
			$title = 'Bạn vừa được phân công theo dõi phiếu báo cáo sự cố [' . $items['name_report'] . ']';
		} elseif ($type == '2') {
			$title = 'Bạn vừa được phân công xử lý phiếu báo cáo sự cố [' . $items['name_report'] . ']';
		}
		return '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8">' . $title . '</td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <tr>
                    <td colspan="2">Ngày ' . $day . ' Tháng ' . $month . ' Năm ' . $year . '</td>
                </tr>
                <tr>
                    <td>Bộ phận: ' . $items['name_departments'] . '</td>
                    <td>Công đoạn phát hiện: ' . $items['stage'] . '</td>
                </tr>
                <tr nobr="true">
                    <td>LSX Tổng: ' . $items['reference_no'] . '     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="float: right;">Số lượng ' . number_format_data($items['quantity_pcs']) . ' pcs</span></td>
                    <td>
						<table>
							<tr>
								<td>Chạy mẫu : </td>
								<td>' . (!empty($items['type_stage_1']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    </td>
                </tr>
                <tr nobr="true">
                   <td>' . (!empty($items['id_orders']) ? ('Đơn hàng bán: ' . $items['code_orders']) : '') . '</td>
					<td>
						<table>
							<tr>
								<td>Chạy hàng + mẫu :</td>
								<td>' . (!empty($items['type_stage_2']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
					</td>
                </tr>
                 <tr nobr="true">
                    <td>Mã SP: ' . (!empty($items['list_items_name']) ? ('<br/>' . $items['list_items_name']) : '-') . '</td>
                    <td rowspan="2">
                    	<table>
							<tr>
								<td>Chạy hàng :</td>
								<td>' . (!empty($items['type_stage_3']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
							<tr>
								<td>Chạy bù hàng :</td>
								<td>' . (!empty($items['type_stage_3']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    </td>
                </tr>
                <tr nobr="true">
                    <td></td>
                </tr>
            </table>
            <table class="" cellspacing="0" cellpadding="5" border="1" width="100%;" style="width: 100%;">
                <tr nobr="true">
                    <td class="bold text-center" style="text-align: center;"><b>Nội dung KPH</b></td>
                    <td class="bold text-center" style="text-align: center;"><b>Hành động xử lý lập tức</b></td>
                    <td class="bold text-center" style="text-align: center;"><b>Nguyên nhân & khắc phục</b></td>
                </tr>
                <tr>
                    <td class="text-left">Thời điểm ghi nhận : ' . _dt($items['time_of_recording']) . '</td>
                    <td rowspan="13">
                    <br/>
                    <br/>
                    <br/>
                    	<table>
							<tr>
								<td>Chấp nhận :</td>
								<td>' . (!empty($items['action_now_1']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    <br/>
                    <br/>
						<table>
							<tr>
								<td>Loại bỏ :</td>
								<td>' . (!empty($items['action_now_2']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
                    <br/>
                    <br/>
						<table>
							<tr>
								<td>Làm lại :</td>
								<td>' . (!empty($items['action_now_3']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
						
                    <br/>
                    <br/>
						<table>
							<tr>
								<td>Khác :</td>
								<td>' . (!empty($items['action_now_4']) ? $imgcheck : $imgnocheck) . '</td>
							</tr>
						</table>
					</td>
                    <td>Nguyên nhân:</td>
                </tr>
                <tr>
                	<td></td>
                	<td rowspan="6" style="text-align: left">' . $htmlReason . '</td>
				</tr>
				<tr>
                	<td>Mô tả sự KHP :</td>
				</tr>
            	<tr>
                	<td rowspan="3"><i>' . $items['described'] . '</i></td>
				</tr>
				<tr>
				</tr>
				<tr>
				</tr>
				<tr>
                	<td>Số lượng : ' . number_format_data($items['quantity']) . '</td>
				</tr>
				<tr>
                	<td></td>
                	<td>Khắc phục:</td>
				</tr>
				<tr>
                	<td>Ghi chú:</td>
                	<td rowspan="4"><i>' . $htmlProcedure . '</i></td>
				</tr>
				<tr>
                	<td rowspan="3"><i>' . $items['note'] . '</i></td>
				</tr>
            </table>
        ';
	}

	public function delete($id = '')
	{
		if (!$this->delete) {
			ajax_access_denied();
		}
		if (!empty($id)) {
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'production_report');
			$ktTask = $this->db->get('tbltasks')->num_rows();
			if (!empty($ktTask)) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => 'Đang có tồn tại phiếu công việc liên quan đến báo cáo nên không thể xóa'
				]);
				die();
			}
			$dtData = get_table_where('tblproduction_report', ['id' => $id], '', 'row_array');
			$this->db->where('id', $id);
			$success = $this->db->delete('tblproduction_report');
			if (!empty($success)) {
				$this->db->where('production_report_id', $id);
				$this->db->delete('tbl_entrance_ticket_step');
				// $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $id, 'id_internal_proposal_process' => $detail_id, 'id_internal_proposal_process_child' => $value['id']], '', 'row_array');
				if ($dtData['id_internal_proposal'] && !empty($dtData['id_internal_proposal_process']) && !empty($dtData['id_internal_proposal_process_child'])) {
					$check_exist = $this->db->get_where('tbl_internal_proposal_process_child', [
						'id' => $dtData['id_internal_proposal_process_child'],
					])->row();
					if ($check_exist) {
						$this->db->where('id', $check_exist->id);
						$this->db->update('tbl_internal_proposal_process_child', [
							'staff_delete' => get_staff_user_id(),
							'date_delete' => date('Y-m-d H:i:s'),
						]);
					}
				}
				if ($dtData['id_tasks'] && !empty($dtData['id_tasks_process']) && !empty($dtData['id_tasks_process_child'])) {
					$check_exist = $this->db->get_where('tbl_tasks_process_child', [
						'id' => $dtData['id_tasks_process_child'],
					])->row();
					if ($check_exist) {
						$this->db->where('id', $check_exist->id);
						$this->db->update('tbl_tasks_process_child', [
							'staff_delete' => get_staff_user_id(),
							'date_delete' => date('Y-m-d H:i:s'),
						]);
					}
				}

				$this->db->where('id_production_report', $id);
				$this->db->delete('tblproduction_report_items');
				$old_images = get_table_where('tblfiles', ['rel_type' => 'production_report', 'rel_id' => $id]);
				foreach ($old_images as $old_image) {
					if (!empty($old_image['file_name'])) {
						if (file_exists($old_image['file_name'])) {
							// Thực hiện xóa tệp tin
							if (unlink($old_image['file_name'])) {
							}
						}
						$this->db->delete('tblfiles', ['id' => $old_image['id']]);
					}
				}
				insertActivityLog([
					'type_parent_obj' => 'production_report',
					'table_obj' => 'tblproduction_report',
					'id_obj' => $id,
					'name_obj' => $dtData['reference_no'],
					'content' => lang('Xóa báo cáo không phù hợp') . ' [' . $dtData['reference_no'] . ']',
					'actions' => 'delete'
				]);
				$this->recommended_list_model->deleteProductionReportReason($id);
				echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa báo cáo thành công']);
				die();
			}
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa báo cáo không thành công']);
		die();
	}

	public function modal($id = '')
	{
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tblproduction_report.id_branch', $list_branch);
					$this->db->group_end();
					$this->db->where('id', $id);
					$ktData = $this->db->get('tblproduction_report')->row();
				} else {
					$ktData = false;
				}
				if (empty($ktData)) {
					access_denied();
				}
			}
		}
		$this->db->select([
			'tblproduction_report.*',
			'tblproduction_report.id as id',
			'tblcategory_tasks.code as code_category_task',
			'tblcategory_tasks.content as name_category_task',
			'tblproduction_report.reference_no as reference_no_report',
			'tblproduction_report.type_report as type_report',
			'tblproduction_report.date as date',
			//			'tbldepartments.name as name_departments',
			'tbl_room.name as name_departments',
			//			'tbl_productions_orders_details.reference_no as reference_no',
			'tbl_productions_orders.reference_no as reference_no',
			'tblproduction_report.quantity_pcs as quantity_pcs',
			'tbl_stages.name as stage',
			//			'tbl_productions_orders_items.items_code as items_code',
			'(
				SELECT
				GROUP_CONCAT(distinct CONCAT(" - ", items_code, " (", items_name, ")") separator "<br/>")
				FROM tbl_productions_orders_items
				WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
			) as list_items_name',
			'tblproduction_report.time_of_recording as time_of_recording',
			'tbltrouble.code as code_trouble',
			'tbltrouble.name as name_trouble',
			'tblproduction_report.staff_responsible as staff_responsible',
			'tblproduction_report.department_responsible as department_responsible',
			'tbl_orders.reference_no as code_orders',
			'group_rl.name as group_rl_name',
			// 'tbl_relate.name as recommended_list_name',
			'tbl_relate.name as recommended_list_name',
			'tblroles.name as name_role',
		]);
		$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblproduction_report.category_tasks', 'left');
		//		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblproduction_report.id_departments', 'left');
		$this->db->join('tbl_room', 'tbl_room.id = tblproduction_report.id_departments', 'left');
		$this->db->join(
			'tbl_productions_orders',
			'tbl_productions_orders.id = tblproduction_report.id_production_orders',
			'left'
		);
		$this->db->join('tbl_stages', 'tbl_stages.id = tblproduction_report.production_stage', 'left');
		$this->db->join('tbltrouble', 'tbltrouble.id = tblproduction_report.id_trouble', 'left');
		$this->db->join('tbl_orders', 'tbl_orders.id = tblproduction_report.id_orders', 'left');
		$this->db->join('tblroles', 'tblroles.roleid = tblproduction_report.role_id', 'left');
		$this->db->join('tbl_relate as group_rl', 'group_rl.id = tblproduction_report.recommended_list_group_id', 'left');
		$this->db->join('tbl_relate', 'tbl_relate.id = tblproduction_report.recommended_list_id', 'left');
		// $this->db->join(
		//     'tbl_relate as group_rl',
		//     'group_rl.id = tblproduction_report.recommended_list_group_id',
		//     'left'
		// );
		// $this->db->join('tbl_relate', 'tbl_relate.id = tblproduction_report.recommended_list_id', 'left');
		$this->db->where('tblproduction_report.id', $id);
		$data['production_report'] = $this->db->get('tblproduction_report')->row_array();
		if ($this->view_own && !$this->view) {
			if ($data['production_report']->create_by == get_staff_user_id()) {
				ajax_access_denied();
			}
		}
		$data['production_report']['material'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'material'
		])->result_array();
		$data['production_report']['man'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'man'
		])->result_array();
		$data['production_report']['machine'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'machine'
		])->result_array();
		$data['production_report']['method'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'method'
		])->result_array();
		$data['production_report']['environment'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'environment'
		])->result_array();
		$data['production_report']['procedure'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'procedure'
		])->result_array();
		$data['production_report']['fix'] = $this->db->get_where('tblproduction_report_items', [
			'id_production_report' => $id,
			'type' => 'fix'
		])->result_array();
		$data['production_report']['assigned'] = $this->db->get_where('tblproduction_report_assigned', [
			'id_production_report' => $id
		])->result_array();
		$data['production_report']['handler'] = $this->db->get_where('tblproduction_report_handler', [
			'id_production_report' => $id
		])->result_array();
		$data['images'] = get_table_where(
			'tblfiles',
			['rel_type' => 'production_report', 'rel_id' => $id],
			'',
			'result_array',
			'',
			'file_name'
		);
		$data['title'] = 'Phiếu báo cáo: ' . ($data['production_report']['name_report']);

		$this->db->from('tbl_kpi_list_criteria_department');
		$this->db->where('tbl_kpi_list_criteria_department.id', $data['production_report']['kpi_list_criteria_department_id']);
		$dtDataKpi = $this->db->get()->row_array();

		$this->db->from('tbl_kpi_list_criteria_department');
		$this->db->where('tbl_kpi_list_criteria_department.id', $data['production_report']['kpi_list_criteria_department_id_child']);
		$dtDataKpiDetail = $this->db->get()->row_array();

		$this->db->from('tbl_kpi_list_criteria_department');
		$this->db->where('tbl_kpi_list_criteria_department.id', $data['production_report']['kpi_list_criteria_department_id_childd']);
		$dtDataKpiDetailNew = $this->db->get()->row_array();

		$data['production_report']['name_kpi_department'] = !empty($dtDataKpi) ? $dtDataKpi['name'] : '';
		$data['production_report']['name_kpi_department_detail'] = (!empty($dtDataKpiDetail) ? (!empty($dtDataKpiDetail['evaluation_criteria']) ? $dtDataKpiDetail['evaluation_criteria'] : $dtDataKpiDetail['name']) : '') . '-' . (!empty($dtDataKpiDetailNew) ? $dtDataKpiDetailNew['evaluation_criteria'] : '');

		$this->db->from('tbl_machines');
		$this->db->where('tbl_machines.id', $data['production_report']['machines_id']);
		$dtMachines = $this->db->get()->row_array();

		$data['production_report']['name_machine'] = !empty($dtMachines) ? $dtMachines['code'] . ' (' . $dtMachines['name'] . ')' : '';
		$this->load->view('admin/production_report/modal', $data);
	}

	public function changeIscheck($id = '', $ischeck = 0)
	{
		$this->db->where('id', $id);
		$success = $this->db->update('tblproduction_report_items', [
			'ischeck' => $ischeck
		]);
		if (!empty($success)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Cập nhật thành công'
			]);
			die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Cập nhật không thành công'
		]);
		die();
	}

	public function addCustomItem()
	{
		$id_production_report = $this->input->post('id') ?: $this->input->get('id');
		$type = $this->input->post('type') ?: $this->input->get('type');
		$name = $this->input->post('name') ?: $this->input->get('name');

		if (!empty($id_production_report) && !empty($type) && !empty($name)) {
			$this->db->insert('tblproduction_report_items', [
				'type' => $type,
				'id_production_report' => $id_production_report,
				'name' => $name,
				'ischeck' => 1,
			]);
			$new_id = $this->db->insert_id();

			$report = $this->db->get_where('tblproduction_report', ['id' => $id_production_report])->row();
			if ($report && $report->id_trouble) {
				$this->db->insert('tbltrouble_items', [
					'id_trouble' => $report->id_trouble,
					'name' => $name,
					'type' => $type
				]);
			}

			if ($new_id) {
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'new_id' => $new_id,
					'message' => 'Thêm mục mới thành công'
				]);
				die();
			}
		}
		
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Thêm không thành công'
		]);
		die();
	}

	public function getRecommendedListProcess()
	{
		$parent_id = $this->input->post('parent_id');
		if (empty($parent_id)) {
			echo json_encode([]);
			die;
		}
		$this->db->select('tbl_recommended_list_process.*', false);
		$this->db->from('tbl_recommended_list_process');
		$this->db->where('tbl_recommended_list_process.roles >', 0);
		$this->db->where('tbl_recommended_list_process.recommended_list_id', $parent_id);
		$rs = $this->db->get()->result_array();
		$data['mot'] = '';
		$data['hai'] = '';
		$data['ba'] = '';
		$data['bon'] = '';
		$data['sau'] = '';
		$data['bay'] = '';
		$data['tam'] = '';
		$data['chin'] = '';
		foreach ($rs as $key => $value) {
			if ($value['bod'] == 1) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['mot'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 2) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['hai'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 3) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['ba'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 4) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['bon'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 5) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['nam'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 6) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['sau'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 7) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['bay'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 8) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['tam'] = $staff_assigner->staffid;
				}
			}
			if ($value['bod'] == 9) {
				$this->db->where('role', $value['roles']);
				$staff_assigner = $this->db->get('tblstaff')->row();
				if (!empty($staff_assigner)) {
					$data['chin'] = $staff_assigner->staffid;
				}
			}
		}
		echo json_encode($data);
	}

	public function getRecommendedListByParentrecommended()
	{
		$parent_id = $this->input->post('parent_id');
		if (empty($parent_id)) {
			echo json_encode([]);
			die;
		}
		$this->db->select(
			'tbl_recommended_list.id as id, tbl_recommended_list.code as code, tbl_recommended_list.name as name',
			false
		);
		$this->db->from('tbl_recommended_list');
		$this->db->where('tbl_recommended_list.parent_id', $parent_id);
		$rs = $this->db->get()->result_array();
		echo json_encode($rs);
	}
	public function getRecommendedListByParentrecommended_new()
	{
		$id = $this->input->post('id');
		if (empty($id)) {
			echo json_encode([]);
			die;
		}
		$this->db->select(
			'tbl_recommended_list.parent_id as parent_id, tbl_recommended_list.code as code, tbl_recommended_list.name as name',
			false
		);
		$this->db->from('tbl_recommended_list');
		$this->db->where('tbl_recommended_list.id', $id);
		$recommended_list = $this->db->get()->row_array();
		if (empty($recommended_list)) {
			echo json_encode([]);
			die;
		}
		$this->db->select(
			'tbl_recommended_list.id as id, tbl_recommended_list.code as code, tbl_recommended_list.name as name',
			false
		);
		$this->db->from('tbl_recommended_list');
		$this->db->where('tbl_recommended_list.id', $recommended_list['parent_id']);
		$rs = $this->db->get()->result_array();
		echo json_encode($rs);
	}

	public function getRecommendedListByParent()
	{
		$parent_id = $this->input->post('parent_id');
		if (empty($parent_id)) {
			echo json_encode([]);
			die;
		}
		$this->db->select(
			'tbl_relate.id as id, tbl_relate.code as code, tbl_relate.name as name',
			false
		);
		$this->db->from('tbl_relate');
		$this->db->where('tbl_relate.parent_id', $parent_id);
		$rs = $this->db->get()->result_array();
		echo json_encode($rs);
	}

	// public function export_excel()
	// {
	//     ini_set('memory_limit', '3500M');
	//     include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
	//     $this->load->library('PHPExcel');
	//     $objPHPExcel = new PHPExcel();
	//     $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
	//     $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
	//     $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
	//     $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
	//     $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
	//     $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
	//     $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	//     $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	//     $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
	//     $objPHPExcel->getDefaultStyle()->applyFromArray([
	//         'font' => array(
	//             'name' => 'Times New Roman'
	//         ),
	//     ]);
	//     $start_date_search = $this->input->post('start_date_search');
	//     $end_date_search = $this->input->post('end_date_search');
	//     $tblproduction_report_handler = '(
	// 		SELECT GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") AS full_name,
	// 		tblproduction_report_handler.id_production_report AS id_production_report
	// 		FROM tblproduction_report_handler
	// 		JOIN tblstaff ON tblstaff.staffid = tblproduction_report_handler.staff_id
	// 		GROUP BY tblproduction_report_handler.id_production_report
	// 	) tblproduction_report_handler';
	//     $tblproduction_report_assigned = '(
	// 		SELECT GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") AS full_name,
	// 		tblproduction_report_assigned.id_production_report AS id_production_report
	// 		FROM tblproduction_report_assigned
	// 		JOIN tblstaff ON tblstaff.staffid = tblproduction_report_assigned.staff_id
	// 		GROUP BY tblproduction_report_assigned.id_production_report
	// 	) tblproduction_report_assigned';
	//     $this->db->select('
	// 		tblproduction_report.id as id,
	//         tblproduction_report.type_report as type_report,
	// 		tblproduction_report.name_report as name,
	// 		DATE_FORMAT(tblproduction_report.date, "%Y/%m/%d %H:%i:%s") as date,
	// 		group_rl.name as recommended_list_group,
	// 		tbl_orders.reference_no as order,
	// 		tbl_productions_orders.reference_no as production_order,
	// 		tbl_relate.name as recommended_list,
	// 		CONCAT(tbltrouble.name) as trouble,
	// 		CONCAT(tb_staff_responsible.firstname, " ", tb_staff_responsible.lastname) as staff_responsible,
	// 		tblbranch.name as branch,
	// 		tbl_room.name as department,
	// 		tblroles.name as role,
	// 		tblcategory_tasks.code as category_task,
	// 		CONCAT(tblcategory_tasks.code, ": ", tblcategory_tasks.content) as role,
	// 		tbl_stages.name as production_stage,
	// 		CONCAT(tb_staff_handover.firstname, " ", tb_staff_handover.lastname) as staff_handover,
	//         CONCAT(tb_staff_evaluate.firstname, " ", tb_staff_evaluate.lastname) as staff_evaluate,
	// 		tblproduction_report_handler.full_name as staff_handler,
	// 		tblproduction_report_assigned.full_name as staff_assigned,
	// 		tblproduction_report.quantity_kpi as quantity_kpi,
	// 		tblproduction_report.quantity as quantity,
	// 		tblproduction_report.note as note,
	//         tblproduction_report.described as described,
	// 		tblproduction_report.id_production_orders as id_production_orders,
	// 		tblproduction_report.reference_no as reference_no,
	//         tblproduction_report.action_now_1 as action_now_1,
	//         tblproduction_report.action_now_2 as action_now_2,
	//         tblproduction_report.action_now_3 as action_now_3,
	//         tblproduction_report.action_now_4 as action_now_4,
	// 		"" as items,
	// 		"" as price,
	// 	');
	//     $this->db->from('tblproduction_report');
	//     $this->db->join(
	//         'tbl_relate group_rl',
	//         'group_rl.id = tblproduction_report.recommended_list_group_id',
	//         'left'
	//     );
	//     $this->db->join(
	//         'tbl_relate',
	//         'tbl_relate.id = tblproduction_report.recommended_list_id',
	//         'left'
	//     );
	//     $this->db->join('tbl_orders', 'tbl_orders.id = tblproduction_report.id_orders', 'left');
	//     $this->db->join(
	//         'tbl_productions_orders',
	//         'tbl_productions_orders.id = tblproduction_report.id_production_orders',
	//         'left'
	//     );
	//     $this->db->join('tbltrouble', 'tbltrouble.id = tblproduction_report.id_trouble', 'left');
	//     $this->db->join(
	//         'tblstaff tb_staff_responsible',
	//         'tb_staff_responsible.staffid = tblproduction_report.staff_responsible',
	//         'left'
	//     );
	//     $this->db->join('tblbranch', 'tblbranch.id = tblproduction_report.id_branch', 'left');
	//     //		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblproduction_report.id_departments', 'left');
	//     $this->db->join('tbl_room', 'tbl_room.id = tblproduction_report.id_departments', 'left');
	//     $this->db->join('tblroles', 'tblroles.roleid = tblproduction_report.role_id', 'left');
	//     $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblproduction_report.category_tasks', 'left');
	//     $this->db->join('tbl_stages', 'tbl_stages.id = tblproduction_report.production_stage', 'left');
	//     $this->db->join(
	//         'tblstaff tb_staff_handover',
	//         'tb_staff_handover.staffid = tblproduction_report.staff_handover',
	//         'left'
	//     );
	//     $this->db->join(
	//         'tblstaff tb_staff_evaluate',
	//         'tb_staff_evaluate.staffid = tblproduction_report.staff_evaluate',
	//         'left'
	//     );
	//     $this->db->join(
	//         $tblproduction_report_handler,
	//         'tblproduction_report_handler.id_production_report = tblproduction_report.id',
	//         'left'
	//     );
	//     $this->db->join(
	//         $tblproduction_report_assigned,
	//         'tblproduction_report_assigned.id_production_report = tblproduction_report.id',
	//         'left'
	//     );
	//     if (!empty($start_date_search)) {
	//         $this->db->where('tblproduction_report.date >= "' . to_sql_date($start_date_search, true) . '"');
	//     }
	//     if (!empty($end_date_search)) {
	//         $this->db->where('tblproduction_report.date <= "' . to_sql_date($end_date_search, true) . '"');
	//     }
	//     $this->db->group_by('tblproduction_report.id');
	//     $this->db->order_by('tblproduction_report.id', 'desc');
	//     $result = $this->db->get()->result_array();
	//     $styleTitle = [
	//         'borders' => array(
	//             'allborders' => array(
	//                 'style' => PHPExcel_Style_Border::BORDER_THIN
	//             )
	//         ),
	//         'font' => array(
	//             'bold' => true,
	//             'size' => 18,
	//             'name' => 'Times New Roman'
	//         ),
	//         'alignment' => array(
	//             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	//             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	//         )
	//     ];
	//     $styleHeader = [
	//         'borders' => array(
	//             'allborders' => array(
	//                 'style' => PHPExcel_Style_Border::BORDER_THIN
	//             )
	//         ),
	//         'font' => array(
	//             // 'bold' => true,
	//             // 'color' => array('rgb' => '111112'),
	//             'size' => 12,
	//             'name' => 'Times New Roman'
	//         ),
	//         'fill' => array(
	//             'type' => PHPExcel_Style_Fill::FILL_SOLID,
	//             'color' => array('rgb' => '4BACC6'),
	//             'size' => 12,
	//             // 'bold' => true
	//         ),
	//         'alignment' => array(
	//             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	//             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	//         )
	//     ];
	//     $stylePlain = [
	//         'borders' => array(
	//             'allborders' => array(
	//                 'style' => PHPExcel_Style_Border::BORDER_THIN
	//             )
	//         ),
	//         'font' => array(
	//             // 'bold' => false,
	//             // 'color' => array('rgb' => '111112'),
	//             'size' => 11,
	//             'name' => 'Times New Roman'
	//         ),
	//     ];
	//     $headerFillColor = [
	//         'A' => array('rgb' => '9BC2E6'),
	//     ];
	//     $cloumns_excel = cloumns_excel();
	//     $colName = [
	//         'id' => '#',
	//         'type_production_report' => 'Loại',
	//         'name' => 'Tên Phiếu',
	//         'reference_no' => 'Số Phiếu',
	//         'date' => 'Thời Điểm Ghi Nhận',
	//         'recommended_list_group' => 'Liên Quan Đến',
	//         'order' => 'Đơn Hàng',
	//         'production_order' => 'Lệnh SX Tổng',
	//         'recommended_list' => 'Chi Tiết Liên Quan',
	//         'items' => 'Mặt hàng',
	//         'price' => 'Giá',
	//         'trouble' => 'Sự Cố',
	//         'staff_responsible' => 'Người Chịu Trách Nhiệm',
	//         'branch' => 'Chi Nhánh',
	//         'department' => 'Bộ phận',
	//         'role' => 'Tổ - Thiết Bị',
	//         'category_task' => 'Mã Công Việc',
	//         'production_stage' => 'Công Đoạn Phát Hiện',
	//         'staff_handover' => 'Người Lập Báo Cáo',
	//         'staff_handler' => 'Người Chứng Nhận Xử Lý',
	//         'staff_assigned' => 'Người Giám Sát-Phòng Ngừa',
	//         'staff_evaluate' => 'Người đánh giá',
	//         'described' => 'Nội Dung KPH',
	//         'quantity_kpi' => 'KPI',
	//         'quantity' => 'Số Lượng',
	//         'reason' => 'Nguyên nhân',
	//         'procedure' => 'Quy trình xử lý',
	//         'fix' => 'Quy trình khắc phục, phòng ngừa :',
	//         'immediate_action' => 'Hành Động Xử Lý Lập Tức',
	//         'note' => 'Ghi Chú',
	//     ];
	//     $aColumns = array_keys($colName);
	//     $excelRowNum = 1;
	//     $maxCol = count($colName) - 1;
	//     $objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
	//     $objPHPExcel->getActiveSheet()->setCellValue(
	//         'A' . $excelRowNum,
	//         ('PHIẾU BÁO CÁO SỰ CỐ')
	//     )->getStyle("A" . $excelRowNum)->applyFromArray($styleTitle);
	//     // $objPHPExcel->getActiveSheet()->freezePane('A1');
	//     $excelRowNum = 2;
	//     foreach ($aColumns as $key => $value) {
	//         foreach ($headerFillColor as $colIndex => $color) {
	//             if ($cloumns_excel[$key] == $colIndex) {
	//                 $styleHeader['fill']['color'] = $color;
	//                 unset($headerFillColor[$colIndex]);
	//                 break;
	//             }
	//         }
	//         $objPHPExcel->getActiveSheet()->setCellValue(
	//             $cloumns_excel[$key] . $excelRowNum,
	//             ($colName[$value])
	//         )->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
	//         $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
	//     }
	//     // $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(false);
	//     // $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(40);
	//     $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(false);
	//     $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(40);
	//     $excelRowNum = 3;
	//     $arrPOId = array_column($result, 'id_production_orders');
	//     $arrPOId = array_unique(array_filter($arrPOId));
	//     $listPOItems = $this->core_model->getProductionsOrdersItemsAndPriceArrByPOId($arrPOId, 'po_id', 1);
	//     foreach ($result as $key => $aRow) {
	//         if ($aRow['type_report'] == 1) {
	//             $aRow['type_production_report'] = 'Báo cáo không phù hợp';
	//         } else if ($aRow['type_report'] == 2) {
	//             $aRow['type_production_report'] = 'Báo cáo vượt';
	//         } else if ($aRow['type_report'] == 3) {
	//             $aRow['type_production_report'] = 'Báo cáo cải tiến';
	//         }
	//         $idd = $aRow['id'];
	//         $aRow['id'] = ($key + 1);
	//         $po_id = $aRow['id_production_orders'];
	//         $dtPOItem = $listPOItems[$po_id] ?? null;
	//         $strItems = '';
	//         $strItemsPrice = '';
	//         if ($dtPOItem) {
	//             foreach ($dtPOItem as $kP => $vP) {
	//                 $strItems .= $vP['name'] . "\n";
	//                 $strItemsPrice .= formatMoney($vP['price']) . "\n";
	//             }
	//         }
	//         $aRow['items'] = $strItems;
	//         $aRow['price'] = $strItemsPrice;
	//         $material = $this->db->get_where('tblproduction_report_items', [
	//             'id_production_report' => $idd,
	//             'type' => 'material'
	//         ])->result_array();
	//         $man = $this->db->get_where('tblproduction_report_items', [
	//             'id_production_report' => $idd,
	//             'type' => 'man'
	//         ])->result_array();
	//         $machine = $this->db->get_where('tblproduction_report_items', [
	//             'id_production_report' => $idd,
	//             'type' => 'machine'
	//         ])->result_array();
	//         $method = $this->db->get_where('tblproduction_report_items', [
	//             'id_production_report' => $idd,
	//             'type' => 'method'
	//         ])->result_array();
	//         $environment = $this->db->get_where('tblproduction_report_items', [
	//             'id_production_report' => $idd,
	//             'type' => 'environment'
	//         ])->result_array();
	//         $procedure = $this->db->get_where('tblproduction_report_items', [
	//             'id_production_report' => $idd,
	//             'type' => 'procedure'
	//         ])->result_array();
	//         // $fix = $this->db->get_where('tblproduction_report_items', [
	//         //     'id_production_report' => $idd,
	//         //     'type' => 'fix'
	//         // ])->result_array();
	//         // $data['production_report']['assigned'] = $this->db->get_where('tblproduction_report_assigned', [
	//         //     'id_production_report' => $id
	//         // ])->result_array();
	//         // $data['production_report']['handler'] = $this->db->get_where('tblproduction_report_handler', [
	//         //     'id_production_report' => $id
	//         // ])->result_array();
	//         $dtReason = $production_report['id'] ?? null ? $this->recommended_list_model->getProductionReportReason($idd, 'trouble') : null;
	//         $reason = '';
	//         $procedure_text = '';
	//         if (!empty($procedure)) {
	//             foreach ($procedure as $ka => $ve) {
	//                 if (!empty($ve['ischeck'])) {
	//                     $procedure_text .= '- ' . $ve['name'] . "\n";
	//                 }
	//             }
	//         }
	//         $fix_text = '';
	//         if (!empty($fix)) {
	//             foreach ($fix as $ka => $ve) {
	//                 if (!empty($ve['ischeck'])) {
	//                     $fix_text .= '- ' . $ve['name'] . "\n";
	//                 }
	//             }
	//         }
	//         if (!empty($material)) {
	//             $is_check = false;
	//             foreach ($material as $km => $vm) {
	//                 if (!empty($vm['ischeck'])) {
	//                     $is_check = true;
	//                 }
	//             }
	//             if (!empty($is_check)) {
	//                 $reason .= "Nguyên phụ liệu (Material)\n";
	//                 $reason .= !empty($dtReason['material']['reason']) ? $dtReason['material']['reason'] . "\n" : '';
	//                 foreach ($material as $ka => $ve) {
	//                     if (!empty($ve['ischeck'])) {
	//                         $reason .= '  - ' . $ve['name'] . "\n";
	//                     }
	//                 }
	//             }
	//         }
	//         if (!empty($man)) {
	//             $is_check = false;
	//             foreach ($man as $km => $vm) {
	//                 if (!empty($vm['ischeck'])) {
	//                     $is_check = true;
	//                 }
	//             }
	//             if (!empty($is_check)) {
	//                 $reason .= "Nhân lực (Man)\n";
	//                 $reason .= !empty($dtReason['man']['reason']) ? $dtReason['man']['reason'] . "\n" : '';
	//                 foreach ($man as $ka => $ve) {
	//                     if (!empty($ve['ischeck'])) {
	//                         $reason .= '  - ' . $ve['name'] . "\n";
	//                     }
	//                 }
	//             }
	//         }
	//         if (!empty($machine)) {
	//             $is_check = false;
	//             foreach ($machine as $km => $vm) {
	//                 if (!empty($vm['ischeck'])) {
	//                     $is_check = true;
	//                 }
	//             }
	//             if (!empty($is_check)) {
	//                 $reason .= "Máy móc (Machine)\n";
	//                 $reason .= !empty($dtReason['machine']['reason']) ? $dtReason['machine']['reason'] . "\n" : '';
	//                 foreach ($machine as $ka => $ve) {
	//                     if (!empty($ve['ischeck'])) {
	//                         $reason .= '  - ' . $ve['name'] . "\n";
	//                     }
	//                 }
	//             }
	//         }
	//         if (!empty($method)) {
	//             $is_check = false;
	//             foreach ($method as $km => $vm) {
	//                 if (!empty($vm['ischeck'])) {
	//                     $is_check = true;
	//                 }
	//             }
	//             if (!empty($is_check)) {
	//                 $reason .= "Phương pháp (Method)\n";
	//                 $reason .= !empty($dtReason['method']['reason']) ? $dtReason['method']['reason'] . "\n" : '';
	//                 foreach ($method as $ka => $ve) {
	//                     if (!empty($ve['ischeck'])) {
	//                         $reason .= '  - ' . $ve['name'] . "\n";
	//                     }
	//                 }
	//             }
	//         }
	//         if (!empty($environment)) {
	//             $is_check = false;
	//             foreach ($environment as $km => $vm) {
	//                 if (!empty($vm['ischeck'])) {
	//                     $is_check = true;
	//                 }
	//             }
	//             if (!empty($is_check)) {
	//                 $reason .= "Môi trường (Environment)\n";
	//                 $reason .= !empty($dtReason['environment']['reason']) ? $dtReason['environment']['reason'] . "\n" : '';
	//                 foreach ($environment as $ka => $ve) {
	//                     if (!empty($ve['ischeck'])) {
	//                         $reason .= '  - ' . $ve['name'] . "\n";
	//                     }
	//                 }
	//             }
	//         }
	//         foreach ($aColumns as $colIndex => $colCode) {
	//             if (str_contains($colCode, 'date')) {
	//                 $cellValue = (isset($aRow[$colCode]) ? ($aRow[$colCode]) : '');
	//             } else {
	//                 $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
	//             }
	//             if (str_contains($colCode, 'reason') || str_contains($colCode, 'procedure') || str_contains($colCode, 'fix') || str_contains($colCode, 'immediate_action')) {
	//                 if (str_contains($colCode, 'reason')) {
	//                     $objPHPExcel->getActiveSheet()->setCellValue(
	//                         $cloumns_excel[$colIndex] . $excelRowNum,
	//                         $reason
	//                     )->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
	//                 }
	//                 if (str_contains($colCode, 'procedure')) {
	//                     $objPHPExcel->getActiveSheet()->setCellValue(
	//                         $cloumns_excel[$colIndex] . $excelRowNum,
	//                         $procedure_text
	//                     )->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
	//                 }
	//                 if (str_contains($colCode, 'fix')) {
	//                     $objPHPExcel->getActiveSheet()->setCellValue(
	//                         $cloumns_excel[$colIndex] . $excelRowNum,
	//                         $fix_text
	//                     )->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
	//                 }
	//                 if (str_contains($colCode, 'immediate_action')) {
	//                     $immediate_action = '';
	//                     if (!empty($aRow['action_now_1'])) {
	//                         $immediate_action .= "- Chấp nhận\n";
	//                     }
	//                     if (!empty($aRow['action_now_2'])) {
	//                         $immediate_action .= "- Loại bỏ\n";
	//                     }
	//                     if (!empty($aRow['action_now_3'])) {
	//                         $immediate_action .= "- Làm lại\n";
	//                     }
	//                     if (!empty($aRow['action_now_4'])) {
	//                         $immediate_action .= "- Khác\n";
	//                     }
	//                     $objPHPExcel->getActiveSheet()->setCellValue(
	//                         $cloumns_excel[$colIndex] . $excelRowNum,
	//                         $immediate_action
	//                     )->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
	//                 }
	//                 $objPHPExcel->getActiveSheet()->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->getAlignment()->setWrapText(true);
	//             } else {
	//                 if (str_contains($colCode, 'items') || str_contains($colCode, 'price')) {
	//                     $objPHPExcel->getActiveSheet()->setCellValue(
	//                         $cloumns_excel[$colIndex] . $excelRowNum,
	//                         $cellValue
	//                     )->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
	//                 } else {
	//                     $objPHPExcel->getActiveSheet()->setCellValue(
	//                         $cloumns_excel[$colIndex] . $excelRowNum,
	//                         $cellValue
	//                     )->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
	//                 }
	//             }
	//         }
	//         $excelRowNum++;
	//     }
	//     $objPHPExcel->getActiveSheet()->getStyle("T1:T$excelRowNum")->getAlignment()->setWrapText(true);
	//     $objPHPExcel->getActiveSheet()->getStyle("H1:H$excelRowNum")->getAlignment()->setWrapText(true);
	//     $objPHPExcel->getActiveSheet()->getStyle("I1:I$excelRowNum")->getAlignment()->setWrapText(true);
	//     $filename = 'Phieu_bao_cao_KPH' . '.xls';
	//     ob_start();
	//     header('Content-Type: application/vnd.ms-excel');
	//     header('Content-Disposition: attachment;filename="$filename"');
	//     header('Cache-Control: max-age=0');
	//     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	//     $objWriter->save('php://output');
	//     $xlsData = ob_get_contents();
	//     ob_end_clean();
	//     $response = array(
	//         'result' => 1,
	//         'message' => lang('success'),
	//         'filename' => $filename,
	//         'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
	//     );
	//     die(json_encode($response));
	// }

	public function export_excel()
	{
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
		$objPHPExcel->getDefaultStyle()->applyFromArray([
			'font' => array(
				'name' => 'Times New Roman'
			),
		]);
		$start_date_search = $this->input->post('start_date_search');
		$end_date_search = $this->input->post('end_date_search');
		$tblproduction_report_handler = '(
            SELECT GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") AS full_name,
            tblproduction_report_handler.id_production_report AS id_production_report
            FROM tblproduction_report_handler
            JOIN tblstaff ON tblstaff.staffid = tblproduction_report_handler.staff_id
            GROUP BY tblproduction_report_handler.id_production_report
        ) tblproduction_report_handler';
		$tblproduction_report_assigned = '(
            SELECT GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") AS full_name,
            tblproduction_report_assigned.id_production_report AS id_production_report
            FROM tblproduction_report_assigned
            JOIN tblstaff ON tblstaff.staffid = tblproduction_report_assigned.staff_id
            GROUP BY tblproduction_report_assigned.id_production_report
        ) tblproduction_report_assigned';
		$this->db->select('
            tblproduction_report.id as id,
			tblproduction_report.date as duedate,
            tblproduction_report.type_report as type_report,
            tblproduction_report.name_report as name,
            DATE_FORMAT(tblproduction_report.date, "%Y/%m/%d %H:%i:%s") as date,
			DATE_FORMAT(tblproduction_report.time_of_recording, "%Y/%m/%d %H:%i:%s") as time_of_recording,
            group_rl.name as recommended_list_group,
            tbl_orders.reference_no as order,
            tbl_productions_orders.reference_no as production_order,
            tbl_relate.name as recommended_list,
            CONCAT(tbltrouble.name) as trouble,
            CONCAT(tb_staff_responsible.firstname, " ", tb_staff_responsible.lastname) as staff_responsible,
            CONCAT(tb_staff_manage.firstname, " ", tb_staff_manage.lastname) as staff_manage,
            tblbranch.name as branch,
            tbl_room.name as department,
            tblroles.name as role,
            tblcategory_tasks.code as category_task,
            tbl_stages.name as production_stage,
            CONCAT(tb_staff_handover.firstname, " ", tb_staff_handover.lastname) as staff_handover,
            CONCAT(tb_staff_evaluate.firstname, " ", tb_staff_evaluate.lastname) as staff_evaluate,
            tblproduction_report_handler.full_name as staff_handler,
            tblproduction_report_assigned.full_name as staff_assigned,
            tblproduction_report.quantity_kpi as quantity_kpi,
            tblproduction_report.quantity as quantity,
            tblproduction_report.note as note,
            tblproduction_report.note_fix as note_fix,
            tblproduction_report.detail_tasks as detail_tasks,
            tblproduction_report.described as described,
            tblproduction_report.id_production_orders as id_production_orders,
            tblproduction_report.reference_no as reference_no,
            tblproduction_report.action_now_1 as action_now_1,
            tblproduction_report.action_now_2 as action_now_2,
            tblproduction_report.action_now_3 as action_now_3,
            tblproduction_report.action_now_4 as action_now_4,
            tblproduction_report.violate as violate,
			countViolate as countViolate,
            "" as items_code,
            "" as items,
            "" as price,
            tbl_violation_group.name as name_violation_group,
            tbl_violation_group.code as code_violation_group,
            tbl_violation_group.detail as detail_violation_group,
        ');
		$this->db->from('tblproduction_report');
		$this->db->join(
			'tbl_relate group_rl',
			'group_rl.id = tblproduction_report.recommended_list_group_id',
			'left'
		);
		$this->db->join(
			'tbl_relate',
			'tbl_relate.id = tblproduction_report.recommended_list_id',
			'left'
		);
		$this->db->join('tbl_orders', 'tbl_orders.id = tblproduction_report.id_orders', 'left');
		$this->db->join(
			'tbl_productions_orders',
			'tbl_productions_orders.id = tblproduction_report.id_production_orders',
			'left'
		);
		$this->db->join('tbltrouble', 'tbltrouble.id = tblproduction_report.id_trouble', 'left');
		$this->db->join(
			'tblstaff tb_staff_responsible',
			'tb_staff_responsible.staffid = tblproduction_report.staff_responsible',
			'left'
		);
		$this->db->join(
			'tblstaff tb_staff_manage',
			'tb_staff_manage.staffid = tblproduction_report.staff_manage',
			'left'
		);
		$this->db->join('tblbranch', 'tblbranch.id = tblproduction_report.id_branch', 'left');
		$this->db->join('tbl_violation_group', 'tbl_violation_group.id = tblproduction_report.violation_group', 'left');
		//      $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblproduction_report.id_departments', 'left');
		$this->db->join('tbl_room', 'tbl_room.id = tblproduction_report.id_departments', 'left');
		$this->db->join('tblroles', 'tblroles.roleid = tblproduction_report.role_id', 'left');
		$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblproduction_report.category_tasks', 'left');
		$this->db->join('tbl_stages', 'tbl_stages.id = tblproduction_report.production_stage', 'left');
		$this->db->join(
			'tblstaff tb_staff_handover',
			'tb_staff_handover.staffid = tblproduction_report.staff_handover',
			'left'
		);
		$this->db->join(
			'tblstaff tb_staff_evaluate',
			'tb_staff_evaluate.staffid = tblproduction_report.staff_evaluate',
			'left'
		);
		$this->db->join(
			$tblproduction_report_handler,
			'tblproduction_report_handler.id_production_report = tblproduction_report.id',
			'left'
		);
		$this->db->join(
			$tblproduction_report_assigned,
			'tblproduction_report_assigned.id_production_report = tblproduction_report.id',
			'left'
		);
		if (!empty($start_date_search)) {
			$this->db->where('tblproduction_report.date >= "' . to_sql_date($start_date_search, true) . '"');
		}
		if (!empty($end_date_search)) {
			$this->db->where('tblproduction_report.date <= "' . to_sql_date($end_date_search, true) . '"');
		}
		$this->db->group_by('tblproduction_report.id');
		$this->db->order_by('tblproduction_report.id', 'desc');
		$result = $this->db->get()->result_array();
		$styleTitle = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				'bold' => true,
				'size' => 18,
				'name' => 'Times New Roman'
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		];
		$styleHeader = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				// 'bold' => true,
				// 'color' => array('rgb' => '111112'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '4BACC6'),
				'size' => 12,
				// 'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		];
		$stylePlain = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				// 'bold' => false,
				// 'color' => array('rgb' => '111112'),
				'size' => 11,
				'name' => 'Times New Roman'
			),
		];
		$headerFillColor = [
			'A' => array('rgb' => '9BC2E6'),
		];
		$cloumns_excel = cloumns_excel();
		$colName = [
			'id' => '#',
			'type_production_report' => 'Loại',
			'reference_no' => 'Số Phiếu',
			'name' => 'Tên Phiếu',
			'date' => 'Ngày',
			'recommended_list_group' => 'Liên Quan Đến',
			'order' => 'Đơn Hàng',
			'production_order' => 'Lệnh SX Tổng',
			'detail_tasks' => 'Chi Tiết Công Việc',
			'violate' => 'Tích vi Phạm',
			'production_report_kpi' => 'Vi Phạm',
			'countViolate' => 'Vi Phạm trong quý từ lúc tạo phiếu',
			'role' => 'Chức Vụ',
			'staff_responsible' => 'Nhân viên',
			'category_task' => 'Mã Công Việc',
			'violation_group' => 'Nhóm Vi Phạm',
			'branch' => 'Chi Nhánh',
			'department' => 'Khối-Phòng',
			'items_code' => 'Mã Sản Phẩm',
			'items' => 'Tên Sản Phẩm',
			'price' => 'Giá',
			'production_stage' => 'Công Đoạn Phát Hiện',
			'staff_handover' => 'Người Lập Báo Cáo',
			'recommended_list' => 'Chi Tiết Liên Quan',
			'trouble' => 'Sự Cố',
			'staff_manage' => 'Quản Lý Chịu Trách Nhiệm',
			'described' => 'Nội Dung KPH',
			'quantity' => 'Số Lượng',
			'reason' => 'Nguyên nhân',
			'procedure' => 'Quy trình xử lý',
			'note_fix' => 'Quy trình khắc phục, phòng ngừa',
			'immediate_action' => 'Hành Động Xử Lý Lập Tức',
			'staff_handler' => 'Người Xử Lý',
			'staff_assigned' => 'Người Giám Sát-Phòng Ngừa',
			'staff_evaluate' => 'Người đánh giá',
			'time_of_recording' => 'Thời Điểm Ghi Nhận',
			'note' => 'Ghi Chú',
			'checkis' => 'Hoàn thành quy trình',
		];
		$aColumns = array_keys($colName);
		$excelRowNum = 1;
		$maxCol = count($colName) - 1;
		$objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
		$objPHPExcel->getActiveSheet()->setCellValue(
			'A' . $excelRowNum,
			('PHIẾU BÁO CÁO SỰ CỐ')
		)->getStyle("A" . $excelRowNum)->applyFromArray($styleTitle);
		// $objPHPExcel->getActiveSheet()->freezePane('A1');
		$excelRowNum = 3;
		$excelRowNum_quy = 3;

		foreach ($aColumns as $key => $value) {
			foreach ($headerFillColor as $colIndex => $color) {
				if ($cloumns_excel[$key] == $colIndex) {
					$styleHeader['fill']['color'] = $color;
					unset($headerFillColor[$colIndex]);
					break;
				}
			}
			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[$key] . ($excelRowNum - 1),
				''
			)->getStyle($cloumns_excel[$key] . ($excelRowNum - 1))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[$key] . $excelRowNum,
				($colName[$value])
			)->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
		}
		$check = $key;

		// $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(false);
		// $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(40);
		$excelRowNum = 4;
		$arrPOId = array_column($result, 'id_production_orders');
		$arrPOId = array_unique(array_filter($arrPOId));
		$listPOItems = $this->core_model->getProductionsOrdersItemsAndPriceArrByPOIdCs($arrPOId, 'po_id', 1);
		$count_quytrinh = 0;
		foreach ($result as $key => $aRow) {

			$this->db->select('tbl_process_production_report.*,tbl_role_production_report.id_role,tbl_role_production_report.id as idd,tbl_process_production_report.process_id as id');
			$this->db->join('tbl_role_production_report', 'tbl_role_production_report.id_process = tbl_process_production_report.process_id AND tbl_role_production_report.id_production_report = tbl_process_production_report.production_report_id', 'left');
			$this->db->where('tbl_process_production_report.production_report_id', $aRow['id']);
			$this->db->group_by('tbl_process_production_report.id');
			$this->db->order_by('tbl_process_production_report.process_id', 'asc');
			$dtProcess = $this->db->get('tbl_process_production_report')->result_array();
			if (count($dtProcess) > $count_quytrinh) {
				$count_quytrinh = count($dtProcess);
			}
			// NEW: xác định trạng thái thời hạn theo yêu cầu
			$date_start = $aRow['date'] . ' 23:59:59'; // ngày bắt đầu
			$date_end = $aRow['duedate'] . ' 23:59:59'; // ngày kết thúc
			if (empty($date_end)) {
				$date_end = $date_start;
			}
			$now = date('Y-m-d H:i:s');

			$hasPending = false;
			$allDone = true;
			$max_date_status = null;

			foreach ($dtProcess as $v) {
				if (empty($v['status']) || $v['staff_process'] == 0) {
					$hasPending = true;
					$allDone = false;
					// không break ở đây nếu muốn thu thập thêm thông tin; nhưng pending đủ để đánh dấu chưa hoàn thành/trễ
					break;
				} else {
					// status == 1
					if (!empty($v['date_status'])) {
						// lấy max date_status
						if ($max_date_status === null || strtotime($v['date_status']) > strtotime($max_date_status)) {
							$max_date_status = $v['date_status'];
						}
					}
				}
			}

			if ($hasPending) {
				// có bước chưa xong
				if (strtotime($now) > strtotime($date_end)) {
					$aRow['checkis'] = 'Trễ';
				} else {
					$aRow['checkis'] = 'Chưa hoàn thành';
				}
			} elseif ($allDone) {
				// tất cả đã xong (status == 1)
				if (empty($max_date_status)) {
					// đã hoàn thành nhưng không có date_status -> coi là đã hoàn thành (không xác định thời gian)
					$aRow['checkis'] = 'Đã hoàn thành';
				} else {
					if (strtotime($max_date_status) > strtotime($date_end)) {
						$aRow['checkis'] = 'Trễ';
					} elseif (strtotime($max_date_status) < strtotime($date_start)) {
						$aRow['checkis'] = 'Sớm';
					} else {
						// max_date_status nằm trong [date_start, date_end]
						$aRow['checkis'] = 'Đúng';
					}
				}
			} else {
				// fallback
				$aRow['checkis'] = 'Chưa hoàn thành';
			}

			if ($aRow['type_report'] == 1) {
				$aRow['type_production_report'] = 'Báo cáo không phù hợp';
			} else if ($aRow['type_report'] == 2) {
				$aRow['type_production_report'] = 'Báo cáo vượt';
			} else if ($aRow['type_report'] == 3) {
				$aRow['type_production_report'] = 'Báo cáo cải tiến';
			} else if ($aRow['type_report'] == 4) {
				$aRow['type_production_report'] = 'Báo cáo vi phạm';
			}
			$idd = $aRow['id'];
			$aRow['id'] = ($key + 1);
			$po_id = $aRow['id_production_orders'];
			$dtPOItem = $listPOItems[$po_id] ?? null;
			$strItems = '';
			$strItems_code = '';
			$strItemsPrice = '';
			if ($dtPOItem) {
				foreach ($dtPOItem as $kP => $vP) {
					$strItems .= $vP['name'] . "\n";
					$strItems_code .= $vP['code'] . "\n";
					$strItemsPrice .= formatMoney($vP['price']) . "\n";
				}
			}
			$aRow['items'] = $strItems;
			$aRow['items_code'] = $strItems_code;
			$aRow['price'] = $strItemsPrice;
			$material = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'material'
			])->result_array();
			$man = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'man'
			])->result_array();
			$machine = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'machine'
			])->result_array();
			$method = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'method'
			])->result_array();
			$environment = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'environment'
			])->result_array();
			$procedure = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'procedure'
			])->result_array();
			$fix = $this->db->get_where('tblproduction_report_items', [
				'id_production_report' => $idd,
				'type' => 'fix'
			])->result_array();
			$production_report_kpi = $this->db->get_where('tbl_production_report_kpi', [
				'production_report_id' => $idd,
			])->result_array();
			// $data['production_report']['assigned'] = $this->db->get_where('tblproduction_report_assigned', [
			//     'id_production_report' => $id
			// ])->result_array();
			// $data['production_report']['handler'] = $this->db->get_where('tblproduction_report_handler', [
			//     'id_production_report' => $id
			// ])->result_array();
			$dtReason = $production_report['id'] ?? null ? $this->recommended_list_model->getProductionReportReason($idd, 'trouble') : null;
			$production_report_kpi_text = '';
			if (!empty($production_report_kpi)) {
				foreach ($production_report_kpi as $ka => $ve) {
					$names = get_table_where('tbl_category_kpi_criteria', ['id' => $ve['category_kpi_criteria_id']], '', 'row_array');
					$production_report_kpi_text .= '- ' . $names['name'] . "\n";
				}
			}
			$reason = '';
			$procedure_text = '';
			if (!empty($procedure)) {
				foreach ($procedure as $ka => $ve) {
					if (!empty($ve['ischeck'])) {
						$procedure_text .= '- ' . $ve['name'] . "\n";
					}
				}
			}
			$fix_text = '';
			if (!empty($fix)) {
				foreach ($fix as $ka => $ve) {
					if (!empty($ve['ischeck'])) {
						$fix_text .= '- ' . $ve['name'] . "\n";
					}
				}
			}
			if (!empty($material)) {
				$is_check = false;
				foreach ($material as $km => $vm) {
					if (!empty($vm['ischeck'])) {
						$is_check = true;
					}
				}
				if (!empty($is_check)) {
					$reason .= "Nguyên phụ liệu (Material)\n";
					$reason .= !empty($dtReason['material']['reason']) ? $dtReason['material']['reason'] . "\n" : '';
					foreach ($material as $ka => $ve) {
						if (!empty($ve['ischeck'])) {
							$reason .= '  - ' . $ve['name'] . "\n";
						}
					}
				}
			}
			if (!empty($man)) {
				$is_check = false;
				foreach ($man as $km => $vm) {
					if (!empty($vm['ischeck'])) {
						$is_check = true;
					}
				}
				if (!empty($is_check)) {
					$reason .= "Nhân lực (Man)\n";
					$reason .= !empty($dtReason['man']['reason']) ? $dtReason['man']['reason'] . "\n" : '';
					foreach ($man as $ka => $ve) {
						if (!empty($ve['ischeck'])) {
							$reason .= '  - ' . $ve['name'] . "\n";
						}
					}
				}
			}
			if (!empty($machine)) {
				$is_check = false;
				foreach ($machine as $km => $vm) {
					if (!empty($vm['ischeck'])) {
						$is_check = true;
					}
				}
				if (!empty($is_check)) {
					$reason .= "Máy móc (Machine)\n";
					$reason .= !empty($dtReason['machine']['reason']) ? $dtReason['machine']['reason'] . "\n" : '';
					foreach ($machine as $ka => $ve) {
						if (!empty($ve['ischeck'])) {
							$reason .= '  - ' . $ve['name'] . "\n";
						}
					}
				}
			}
			if (!empty($method)) {
				$is_check = false;
				foreach ($method as $km => $vm) {
					if (!empty($vm['ischeck'])) {
						$is_check = true;
					}
				}
				if (!empty($is_check)) {
					$reason .= "Phương pháp (Method)\n";
					$reason .= !empty($dtReason['method']['reason']) ? $dtReason['method']['reason'] . "\n" : '';
					foreach ($method as $ka => $ve) {
						if (!empty($ve['ischeck'])) {
							$reason .= '  - ' . $ve['name'] . "\n";
						}
					}
				}
			}
			if (!empty($environment)) {
				$is_check = false;
				foreach ($environment as $km => $vm) {
					if (!empty($vm['ischeck'])) {
						$is_check = true;
					}
				}
				if (!empty($is_check)) {
					$reason .= "Môi trường (Environment)\n";
					$reason .= !empty($dtReason['environment']['reason']) ? $dtReason['environment']['reason'] . "\n" : '';
					foreach ($environment as $ka => $ve) {
						if (!empty($ve['ischeck'])) {
							$reason .= '  - ' . $ve['name'] . "\n";
						}
					}
				}
			}
			foreach ($aColumns as $colIndex => $colCode) {
				if (str_contains($colCode, 'date')) {
					$cellValue = (isset($aRow[$colCode]) ? ($aRow[$colCode]) : '');
				} else {
					$cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
				}
				if (str_contains($colCode, 'reason') || str_contains($colCode, 'procedure') || str_contains($colCode, 'fix') || str_contains($colCode, 'immediate_action') || str_contains($colCode, 'production_report_kpi') || str_contains($colCode, 'violate')) {
					if (str_contains($colCode, 'violate')) {
						$violate = ($cellValue == 1 ? 'x' : '');
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$violate
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
					if (str_contains($colCode, 'production_report_kpi')) {
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$production_report_kpi_text
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
					if (str_contains($colCode, 'reason')) {
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$reason
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
					if (str_contains($colCode, 'procedure')) {
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$procedure_text
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
					if (str_contains($colCode, 'fix')) {
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$fix_text
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
					if (str_contains($colCode, 'immediate_action')) {
						$immediate_action = '';
						if (!empty($aRow['action_now_1'])) {
							$immediate_action .= "- Chấp nhận\n";
						}
						if (!empty($aRow['action_now_2'])) {
							$immediate_action .= "- Loại bỏ\n";
						}
						if (!empty($aRow['action_now_3'])) {
							$immediate_action .= "- Làm lại\n";
						}
						if (!empty($aRow['action_now_4'])) {
							$immediate_action .= "- Khác\n";
						}
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$immediate_action
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
					$objPHPExcel->getActiveSheet()->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->getAlignment()->setWrapText(true);
				} else {
					if (str_contains($colCode, 'items') || str_contains($colCode, 'price') || str_contains($colCode, 'items_code')) {
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$cellValue
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$colIndex] . $excelRowNum,
							$cellValue
						)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
					}
				}
				if (str_contains($colCode, 'violation_group')) {
					$violation_group = $aRow['code_violation_group'] . ' - ' . $aRow['name_violation_group'] . ' - ' . $aRow['detail_violation_group'];
					$objPHPExcel->getActiveSheet()->setCellValue(
						$cloumns_excel[$colIndex] . $excelRowNum,
						$violation_group
					)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
				}
			}
			$processColIndex = $colIndex;
			foreach ($dtProcess as $kProcess => $vProcess) {
				$processColIndex++;
				$objPHPExcel->getActiveSheet()->setCellValue(
					$cloumns_excel[$processColIndex] . $excelRowNum,
					$vProcess['name']
				)->getStyle($cloumns_excel[$processColIndex] . $excelRowNum)->applyFromArray($stylePlain);
				$processColIndex++;
				$name_status = 'Chưa duyệt';
				$date_status = '';
				$staffName = '';
				if (!empty($vProcess['staff_process'])) {
					$staffName = get_staff_full_name($vProcess['staff_process']);
					$name_status = 'Đã duyệt';
					$date_status = _dt($vProcess['date_process']);
				}
				$objPHPExcel->getActiveSheet()->setCellValue(
					$cloumns_excel[$processColIndex] . $excelRowNum,
					$staffName
				)->getStyle($cloumns_excel[$processColIndex] . $excelRowNum)->applyFromArray($stylePlain);
				$processColIndex++;
				$objPHPExcel->getActiveSheet()->setCellValue(
					$cloumns_excel[$processColIndex] . $excelRowNum,
					$name_status
				)->getStyle($cloumns_excel[$processColIndex] . $excelRowNum)->applyFromArray($stylePlain);
				$processColIndex++;
				$objPHPExcel->getActiveSheet()->setCellValue(
					$cloumns_excel[$processColIndex] . $excelRowNum,
					$date_status
				)->getStyle($cloumns_excel[$processColIndex] . $excelRowNum)->applyFromArray($stylePlain);
			}

			$excelRowNum++;
		}
		$objPHPExcel->getActiveSheet()->setCellValue(
			$cloumns_excel[$check] . ($excelRowNum_quy - 1),
			'Quy trình'
		)->getStyle($cloumns_excel[$check] . ($excelRowNum_quy - 1))->applyFromArray($styleHeader);
		$objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[$check] . ($excelRowNum_quy - 1) . ":" . $cloumns_excel[($check + ($count_quytrinh * 4))] . ($excelRowNum_quy - 1));
		$dems = 0;
		for ($i = 1; $i <= $count_quytrinh; $i++) {
			$check++;
			$dems++;
			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[$check] . ($excelRowNum_quy),
				'Tên bước ' . $dems
			)->getStyle($cloumns_excel[$check] . ($excelRowNum_quy))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$check])->setAutoSize(true);
			$check++;
			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[$check] . ($excelRowNum_quy),
				'Người duyệt ' . $dems
			)->getStyle($cloumns_excel[$check] . ($excelRowNum_quy))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$check])->setAutoSize(true);
			$check++;
			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[$check] . ($excelRowNum_quy),
				'Trạng thái ' . $dems
			)->getStyle($cloumns_excel[$check] . ($excelRowNum_quy))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$check])->setAutoSize(true);
			$check++;
			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[$check] . ($excelRowNum_quy),
				'Thời gian duyệt ' . $dems
			)->getStyle($cloumns_excel[$check] . ($excelRowNum_quy))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$check])->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->getStyle("T1:T$excelRowNum")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle("H1:H$excelRowNum")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle("I1:I$excelRowNum")->getAlignment()->setWrapText(true);
		$filename = 'Phieu_bao_cao_VP' . '.xls';
		ob_start();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="$filename"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$response = array(
			'result' => 1,
			'message' => lang('success'),
			'filename' => $filename,
			'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
		);
		die(json_encode($response));
	}

	public function updatedStatus()
	{
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$process_id = $this->input->post('process_id');
			$this->db->from('tbl_process');
			$this->db->where('tbl_process.id', $process_id);
			$dtProcessNew = $this->db->get()->row_array();
			$this->db->from('tblproduction_report');
			$this->db->where('tblproduction_report.id', $id);
			$dtProductionReport = $this->db->get()->row_array();
			if (empty($dtProductionReport)) {
				echo json_encode(array(
					'success' => false,
					'id' => $id,
					'alert_type' => 'danger',
					'message' => _l('Không tồn tại phiếu')
				));
				die();
			}
			$this->db->where('id_production_report', $dtProductionReport['id']);
			$handler = $this->db->get('tblproduction_report_handler')->result_array();
			$arrHandler = [];
			if (!empty($handler)) {
				foreach ($handler as $key => $value) {
					$arrHandler[] = $value['staff_id'];
				}
			}
			$this->db->where('id_production_report', $dtProductionReport['id']);
			$assigned = $this->db->get('tblproduction_report_assigned')->result_array();
			$arrAssigned = [];
			if (!empty($assigned)) {
				foreach ($assigned as $key => $value) {
					$arrAssigned[] = $value['staff_id'];
				}
			}
			if (!is_admin()) {
				if ($dtProcessNew['type'] == 1) {
					if (!in_array(get_staff_user_id(), $arrHandler)) {
						echo json_encode(array(
							'success' => false,
							'id' => $id,
							'alert_type' => 'danger',
							'message' => _l('Không có quyền duyệt!')
						));
						die();
					}
				} elseif ($dtProcessNew['type'] == 2) {
					if (!in_array(get_staff_user_id(), $arrAssigned)) {
						echo json_encode(array(
							'success' => false,
							'id' => $id,
							'alert_type' => 'danger',
							'message' => _l('Không có quyền duyệt!')
						));
						die();
					}
				}
			}
			if ($status == 1) {
				$staff_id = get_staff_user_id();
				$date = date('Y-m-d H:i:s');
				$data = array(
					'staff_process' => $staff_id,
					'date_process' => $date,
				);
				$this->db->from('tbl_process_production_report');
				$this->db->where('production_report_id', $id);
				$this->db->where('process_id', $process_id);
				$dtProcess = $this->db->get()->row_array();
				$this->db->where('production_report_id', $id);
				$this->db->where('id <', $dtProcess['id']);
				$this->db->order_by('id', 'desc');
				$check_status_bef = $this->db->get('tbl_process_production_report')->row_array();
				if (!empty($check_status_bef)) {
					if ($check_status_bef['staff_process'] == 0) {
						$data['result'] = 0;
						$data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa duyệt, Không thể duyệt bước này');
						echo json_encode($data);
						die;
					}
				}

				if (!empty($dtProcess)) {
					$this->db->where('id', $dtProcess['id']);
					$success = $this->db->update('tbl_process_production_report', $data);
				} else {
					$success = $this->db->insert('tbl_process_production_report', [
						'production_report_id' => $id,
						'process_id' => $process_id,
						'staff_process' => $staff_id,
						'date_process' => $date,
					]);
				}
			} elseif ($status == 0) {
				$this->db->from('tbl_process_production_report');
				$this->db->where('production_report_id', $id);
				$this->db->where('process_id', $process_id);
				$dtProcess = $this->db->get()->row_array();
				$this->db->where('production_report_id', $id);
				$this->db->where('id >', $dtProcess['id']);
				$this->db->order_by('id', 'asc');
				$check_status_bef = $this->db->get('tbl_process_production_report')->row_array();
				if (!empty($check_status_bef)) {
					if ($check_status_bef['staff_process'] != 0) {
						$data['result'] = 0;
						$data['alert_type'] = 'danger';
						$data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa bỏ duyệt duyệt, Không thể bỏ duyệt bước này');
						echo json_encode($data);
						die;
					}
				}

				$this->db->from('tbl_process_production_report');
				$this->db->where('production_report_id', $id);
				$this->db->where('process_id', $process_id);
				$dtProcess = $this->db->get()->row_array();
				$this->db->where('id', $dtProcess['id']);
				$success = $this->db->update('tbl_process_production_report', [
					'staff_process' => 0,
					'date_process' => null,
				]);
				$this->db->where('id_production_report_process', $dtProcess['id']);
				$this->db->delete('tbl_setting_production_report_inspection_criteria_process');
			}
		}
		if ($success) {
			if ($status == 1) {
				activity_log_v2(
					'production_report',
					'tblproduction_report',
					$id,
					$dtProductionReport['name_report'],
					'Cập nhật trạng thái phiếu báo cáo [' . $dtProductionReport['name_report'] . ']'
				);
			} else {
				activity_log_v2(
					'production_report',
					'tblproduction_report',
					$id,
					$dtProductionReport['name_report'],
					'Bỏ duyệt trạng thái [' . $dtProductionReport['name_report'] . ']'
				);
			}
			echo json_encode(array(
				'success' => $success,
				'id' => $id,
				'alert_type' => 'success',
				'message' => _l('Cập nhật trạng thái thành công')
			));
		} else {
			echo json_encode(array(
				'success' => $success,
				'id' => $id,
				'alert_type' => 'danger',
				'message' => _l('Không thể cập nhật dữ liệu')
			));
		}
		die;
	}

	public function updatedStatusAll()
	{
		$success = false;
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = 1;
			$this->db->from('tblproduction_report');
			$this->db->where('tblproduction_report.id', $id);
			$dtProductionReport = $this->db->get()->row_array();
			if (empty($dtProductionReport)) {
				echo json_encode(array(
					'success' => false,
					'id' => $id,
					'alert_type' => 'danger',
					'message' => _l('Không tồn tại phiếu')
				));
				die();
			}
			$dtProcess = get_table_where('tbl_process');
			foreach ($dtProcess as $key => $dtProcessNew) {
				$process_id = $dtProcessNew['id'];
				$this->db->from('tbl_process_production_report');
				$this->db->where('production_report_id', $id);
				$this->db->where('process_id', $process_id);
				$this->db->where('staff_process >', 0);
				$check_dtProcess = $this->db->get()->row_array();
				if (!empty($check_dtProcess)) {
					continue;
				}
				$this->db->where('id_production_report', $dtProductionReport['id']);
				$handler = $this->db->get('tblproduction_report_handler')->result_array();
				$arrHandler = [];
				if (!empty($handler)) {
					foreach ($handler as $key => $value) {
						$arrHandler[] = $value['staff_id'];
					}
				}
				$this->db->where('id_production_report', $dtProductionReport['id']);
				$assigned = $this->db->get('tblproduction_report_assigned')->result_array();
				$arrAssigned = [];
				if (!empty($assigned)) {
					foreach ($assigned as $key => $value) {
						$arrAssigned[] = $value['staff_id'];
					}
				}
				if (!is_admin()) {
					if ($dtProcessNew['type'] == 1) {
						if (!in_array(get_staff_user_id(), $arrHandler)) {
							continue;
						}
					} elseif ($dtProcessNew['type'] == 2) {
						if (!in_array(get_staff_user_id(), $arrAssigned)) {
							continue;
						}
					}
				}
				if ($status == 1) {
					$staff_id = get_staff_user_id();
					$date = date('Y-m-d H:i:s');
					$data = array(
						'staff_process' => $staff_id,
						'date_process' => $date,
					);
					$this->db->from('tbl_process_production_report');
					$this->db->where('production_report_id', $id);
					$this->db->where('process_id', $process_id);
					$dtProcess = $this->db->get()->row_array();
					if (!empty($dtProcess)) {
						$this->db->where('id', $dtProcess['id']);
						$success = $this->db->update('tbl_process_production_report', $data);
					} else {
						$success = $this->db->insert('tbl_process_production_report', [
							'production_report_id' => $id,
							'process_id' => $process_id,
							'staff_process' => $staff_id,
							'date_process' => $date,
						]);
					}
				} elseif ($status == 0) {
					$this->db->from('tbl_process_production_report');
					$this->db->where('production_report_id', $id);
					$this->db->where('process_id', $process_id);
					$dtProcess = $this->db->get()->row_array();
					$this->db->where('id', $dtProcess['id']);
					$success = $this->db->update('tbl_process_production_report', [
						'staff_process' => 0,
						'date_process' => null,
					]);
				}
			}
			if ($success) {
				if ($status == 1) {
					activity_log_v2(
						'production_report',
						'tblproduction_report',
						$id,
						$dtProductionReport['name_report'],
						'Cập nhật trạng thái phiếu báo cáo [' . $dtProductionReport['name_report'] . ']'
					);
				} else {
					activity_log_v2(
						'production_report',
						'tblproduction_report',
						$id,
						$dtProductionReport['name_report'],
						'Bỏ duyệt trạng thái [' . $dtProductionReport['name_report'] . ']'
					);
				}
			}
			echo json_encode(array(
				'success' => $success,
				'id' => $id,
				'alert_type' => 'success',
				'message' => _l('Cập nhật trạng thái thành công')
			));
		} else {
			echo json_encode(array(
				'success' => $success,
				'alert_type' => 'danger',
				'message' => _l('Không thể cập nhật dữ liệu')
			));
		}
		die;
	}

	function getStaff()
	{
		$role_id = !empty($this->input->post('role_id')) ? $this->input->post('role_id') : 0;
		$this->db->select("
            tblstaff.staffid as staffid,
            CONCAT(firstname,' ',lastname) as fullname
        ");
		$this->db->from('tblstaff');
		$this->db->where('tblstaff.role', $role_id);
		$staff = $this->db->get()->result_array();
		echo json_encode($staff);
	}

	public function getKpiByStaff()
	{
		$data = [];
		$staff_responsible = !empty($this->input->post('staff_responsible')) ? $this->input->post('staff_responsible') : 0;
		$this->db->select("
            tbl_category_kpi_criteria.id as id,
            tbl_category_kpi_criteria.name as name,
            tbl_category_kpi_criteria.code as code,
            IF(tbl_category_kpi_criteria.type = 1,'Năng Lực','Tiêu Chuẩn') as type
        ");
		$this->db->from('tblstaff');
		$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
		$this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tblroles.kpi_category_id');
		$this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.category_kpi_id = tbl_category_kpi.id');
		$this->db->where('tblstaff.staffid', $staff_responsible);
		$dtcategoryKpi = $this->db->get()->result_array();
		$data['dtcategoryKpi'] = $dtcategoryKpi;
		echo json_encode($data);
	}

	public function countViolate()
	{
		$data = [];
		$id = $this->input->post('id');
		$date = $this->input->post('date');
		$count = 0;
		if (!empty($id)) {
			if (!empty($date)) {
				$date = to_sql_date($date, true);
				// $date = strtotime($date);
				// $date = strftime("%Y", $date);
			} else {
				$date = date('Y-m-d');
			}
			$_data = getQuarterStartAndEndDate($date);
			$count = countViolate($id, $_data['start'], $_data['end']);
		}

		$data['count'] = $count;
		echo json_encode($data);
		die();
	}

	public function modal_excel()
	{
		$data['title'] = _l('Import excel phiếu báo cáo');
		$this->load->view('admin/production_report/import', $data);
	}

	public function import()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');

		$data = [];
		if (!empty($_FILES['file'])) {
			$fullfile = $_FILES['file']['tmp_name'];
			$nameFile = $_FILES['file']['name'];
			$extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			if ($extension != 'XLSX' && $extension != 'XLS') {
				echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
				die();
			}
			$inputFileType = PHPExcel_IOFactory::identify($fullfile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			$total_sheets = $objPHPExcel->getSheetCount();
			$allSheetName = $objPHPExcel->getSheetNames();

			$vaKey = '';
			$listRow = [
				1 => 'type_report',
				2 => 'name_report',
				3 => 'date',
				4 => 'recommended_list_group_id',
				5 => 'id_orders',
				6 => 'id_production_orders',
				7 => 'violate', //có vi phạm không

				8 => 'kpi_list_criteria_department', //Mục tiêu KPI phòng ban
				9 => 'kpi_list_criteria_department_child', //Chi tiết mục tiêu KPI phòng ban
				10 => 'kpi_list_criteria_department_violate', //Vi phạm


				11 => 'role_id',
				12 => 'staff_responsible',
				13 => 'category_tasks',
				14 => 'violation_group', //Nhóm vi phạm

				15 => 'category_kpi_criteria_id', //nhóm KPI
				16 => 'id_branch', //Chi Nhánh
				17 => 'id_departments', // Mã Khối-Phòng
				18 => 'damage_cost', // Chi phí thiệt hại
				19 => 'production_stage', // mã công đoạn phát hiện

				20 => 'key', // mã công đoạn phát hiện

				21 => 'staff_handover', //Người Lập Báo Cáo
				22 => 'staff_handler', //Người Chứng Nhận Xử Lý
				23 => 'staff_assigned', //Người Giám Sát Phòng Ngừa
				24 => 'staff_evaluate', //Người Đánh Giá
				25 => 'recommended_list_id', //Mã Chi Tiết Liên Quan
				26 => 'id_trouble', //Mã Sự Cố
				27 => 'staff_manage',

				28 => 'machines_id',

				29 => 'quantity_pcs',
				30 => 'note',
			];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$redata = [];
				$stt = 0;
				for ($i = 3; $i <= $highestRow; $i++) {
					for ($j = 1; $j < $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						if ($j == 3) {
							if (is_numeric($Val)) {
								$Val = PHPExcel_Shared_Date::ExcelToPHPObject($Val)->format('d-m-Y H:i:s');
							}
						}

						$redata[$stt][$listRow[$j]] = trim($Val);
					}
					$stt++;
				}
			}
		}
		$count = 0;
		if (!empty($redata)) {
			$dataInsert = [];
			foreach ($redata as $rKey => $rvalue) {
				$rowExcel = $rKey + 3;
				if (empty($rvalue['type_report'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy loại phiếu';
					continue;
				}


				//vì file mẫu lở soạn sai nên fix ở đây luôn
				if ($rvalue['type_report'] == 2) {
					$rvalue['type_report'] = 4;
				} else if ($rvalue['type_report'] == 4) {
					$rvalue['type_report'] = 2;
				}



				if (empty($rvalue['name_report'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Tên phiếu không dược bỏ trống';
					continue;
				}
				if (empty($rvalue['recommended_list_group_id'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Mã liên quan không dược bỏ trống';
					continue;
				}
				if (empty($rvalue['id_branch'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Chi nhánh không được bỏ trống';
					continue;
				}
				if (empty($rvalue['role_id'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Lọc mã công việc theo chức vụ không được bỏ trống';
					continue;
				}
				if (empty($rvalue['id_departments'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Mã Khối-Phòng không được bỏ trống';
					continue;
				}
				if (empty($rvalue['production_stage'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Mã công đoạn không được bỏ trống';
					continue;
				}
				if (empty($rvalue['staff_handover'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Người Lập Báo Cáo không được bỏ trống';
					continue;
				}
				if (empty($rvalue['staff_handler'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Người Chứng Nhận không được bỏ trống';
					continue;
				}
				if (empty($rvalue['staff_assigned'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Người Giám Sát Phòng Ngừa không được bỏ trống';
					continue;
				}
				if (empty($rvalue['staff_evaluate'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Người Đánh Giá không được bỏ trống';
					continue;
				}
				if (empty($rvalue['id_trouble'])) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Mã Sự Cố không được bỏ trống';
					continue;
				}


				$reference_no = getReference('production_report');
				$recommended_list_group_id = NULL;
				if (!empty($rvalue['recommended_list_group_id'])) {
					$recommended_list_group_id = $this->db->get_where(
						'tbl_relate',
						['code' => $rvalue['recommended_list_group_id']]
					)->row('id');
					if (empty($recommended_list_group_id)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy mã liên quan : ' . $rvalue['recommended_list_group_id'];
						continue;
					}
				}

				$id_production_orders = 0;
				if (!empty($rvalue['id_orders'])) {
					$id_orders = $this->db->get_where('tbl_orders', ['reference_no' => $rvalue['id_orders']])->row('id');
				}
				$id_orders = $id_orders ?? 0;

				$id_production_orders = 0;
				if (!empty($rvalue['id_production_orders'])) {
					$id_production_orders = $this->db->get_where('tbl_productions_orders', ['reference_no' => $rvalue['id_production_orders']])->row('id');
				}
				$id_production_orders = $id_production_orders ?? 0;

				$violate = (!empty($rvalue['violate']) && ($rvalue['violate'] == 'x' || $rvalue['violate'] == 'X')) ? 1 : 0;

				$role_id = 0;
				if (!empty($rvalue['role_id'])) {
					$role_id = $this->db->get_where('tblroles', ['code_role' => $rvalue['role_id']])->row('roleid');
					if (empty($role_id)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Chức vụ không tồn tại: ' . $rvalue['role_id'];
						continue;
					}
				}

				$staff_responsible = NULL;
				if (!empty($rvalue['staff_responsible'])) {
					$this->db->where(
						'CONCAT(firstname, " ", lastname) = "' . $rvalue['staff_responsible'] . '"',
						false,
						false
					);
					$staff_responsible = $this->db->get('tblstaff')->row('staffid');
					if (empty($staff_responsible)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy nhân viên : ' . $rvalue['staff_responsible'];
						continue;
					}
				}

				$category_tasks = NULL;
				if (!empty($rvalue['category_tasks'])) {
					$category_tasks = $this->db->get_where(
						'tblcategory_tasks',
						['code' => $rvalue['category_tasks']]
					)->row('id');
					if (empty($category_tasks)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy mã công việc : ' . $rvalue['category_tasks'];
						continue;
					}
				}

				$kpi_list_criteria_department = 0;
				if (!empty($rvalue['kpi_list_criteria_department'])) {
					$kpi_list_criteria_department = $this->db->get_where(
						'tbl_kpi_list_criteria_department',
						['code' => $rvalue['kpi_list_criteria_department']]
					)->row('id');
					if (empty($kpi_list_criteria_department)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Mục tiêu KPI phòng ban : ' . $rvalue['category_tasks'];
						continue;
					}
				}
				$kpi_list_criteria_department_child = 0;
				$kpi_list_criteria_department_childd = 0;
				if (!empty($rvalue['kpi_list_criteria_department_child'])) {
					$k_kpi_list_criteria_department_child = explode(' - ', $rvalue['kpi_list_criteria_department_child']);
					$k_kpi_list_criteria_department_child[0] = trim($k_kpi_list_criteria_department_child[0]);
					if (!empty($k_kpi_list_criteria_department_child[1])) {
						$k_kpi_list_criteria_department_child[1] = trim($k_kpi_list_criteria_department_child[1]);
					}
					$kpi_list_criteria_department_child = $this->db->get_where(
						'tbl_kpi_list_criteria_department',
						[
							'code' => $k_kpi_list_criteria_department_child[0],
							'parent_id' => $kpi_list_criteria_department
						]
					)->row('id');
					if (!empty($k_kpi_list_criteria_department_child[1])) {
						$kpi_list_criteria_department_childd = $this->db->get_where(
							'tbl_kpi_list_criteria_department',
							[
								'code' => $k_kpi_list_criteria_department_child[1],
								'parent_id' => $kpi_list_criteria_department
							]
						)->row('id');
					}

					if (empty($kpi_list_criteria_department_child)) {
						$kpi_list_criteria_department_child_two = $this->db->get_where(
							'tbl_kpi_list_criteria_department',
							[
								'code' => $k_kpi_list_criteria_department_child[0],
							]
						)->row();
						if (!empty($kpi_list_criteria_department_child_two)) {
							$kpi_list_criteria_department = $kpi_list_criteria_department_child_two->parent_id;
							$kpi_list_criteria_department_child = $kpi_list_criteria_department_child_two->id;
							if (!empty($k_kpi_list_criteria_department_child[1]) && empty($kpi_list_criteria_department_childd)) {
								$kpi_list_criteria_department_childd = $this->db->get_where(
									'tbl_kpi_list_criteria_department',
									[
										'code' => $k_kpi_list_criteria_department_child[1],
										'parent_id' => $kpi_list_criteria_department
									]
								)->row('id');
							}
						}

						if (empty($kpi_list_criteria_department_child)) {
							$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Chi tiết mục tiêu KPI phòng ban : ' . $k_kpi_list_criteria_department_child[0];
							continue;
						}
						if (empty($kpi_list_criteria_department_childd) && !empty($k_kpi_list_criteria_department_child[1])) {
							$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Chi tiết mục tiêu thứ 2 KPI phòng ban : ' . $k_kpi_list_criteria_department_child[1];
							continue;
						}
					}
				}

				$category_kpi_criteria_id = NULL;
				$list_category_kpi_criteria_id = !empty($rvalue['category_kpi_criteria_id']) ? explode(',', $rvalue['category_kpi_criteria_id']) : NULL;
				if (!empty($list_category_kpi_criteria_id)) {
					$this->db->group_start();
					foreach ($list_category_kpi_criteria_id as $k => $v) {
						$this->db->or_where('code', $v);
					}
					$this->db->group_end();
					$category_kpi_criteria_id = $this->db->get_where('tbl_category_kpi_criteria')->result_array();
					if (empty($category_kpi_criteria_id)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy nhóm KPI: ' . $rvalue['category_tasks'];
						continue;
					}
				}

				$id_branch = NULL;
				if (!empty($rvalue['id_branch'])) {
					$id_branch = $this->db->get_where('tblbranch', ['name' => $rvalue['id_branch']])->row('id');
					if (empty($id_branch)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy chi nhánh: ' . $rvalue['id_branch'];
						continue;
					}
				}

				$id_departments = NULL;
				if (!empty($rvalue['id_departments'])) {
					$id_departments = $this->db->get_where('tbl_room', ['code' => $rvalue['id_departments']])->row('id');
					if (empty($id_departments)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Mã Khối-Phòng: ' . $rvalue['id_departments'];
						continue;
					}
				}

				$damage_cost = !empty($rvalue['damage_cost']) ? number_format_data($rvalue['damage_cost']) : 0;

				$production_stage = NULL;
				if (!empty($rvalue['production_stage'])) {
					$production_stage = $this->db->get_where('tbl_stages', ['code' => $rvalue['production_stage']])->row('id');
					if (empty($production_stage)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Mã công đoạn phát hiện: ' . $rvalue['production_stage'];
						continue;
					}
				}

				$staff_handover = NULL;
				if (!empty($rvalue['staff_handover'])) {
					$staff_handover = $this->db->where(
						'CONCAT(firstname, " ", lastname) = "' . $rvalue['staff_handover'] . '"',
						false,
						false
					)->get_where('tblstaff')->row('staffid');
					if (empty($staff_handover)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy người lập báo cáo: ' . $rvalue['staff_handover'];
						continue;
					}
				}


				$list_staff_handler = !empty($rvalue['staff_handler']) ? explode(',', $rvalue['staff_handler']) : [];
				$staff_handler = [];
				if (!empty($list_staff_handler)) {
					$this->db->group_start();
					foreach ($list_staff_handler as $staffFullname) {
						$this->db->or_where('CONCAT(firstname, " ", lastname) = "' . trim($staffFullname) . '"', false, false);
					}
					$this->db->group_end();
					$staff_handler_data = $this->db->get('tblstaff')->result_array();
					if (!empty($staff_handler_data)) {
						foreach ($staff_handler_data as $k => $v) {
							$staff_handler[] = $v['staffid'];
						}
					}
				}
				if (empty($staff_handler)) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Người Chứng Nhận Xử Lý: ' . ($rvalue['staff_handler'] ?? '');
					continue;
				}



				$list_staff_assigned = !empty($rvalue['staff_assigned']) ? explode(',', $rvalue['staff_assigned']) : NULL;
				$assigned = [];
				if (!empty($list_staff_assigned)) {
					$this->db->group_start();
					foreach ($list_staff_assigned as $staffFullname) {
						$this->db->or_where('CONCAT(firstname, " ", lastname) = "' . trim($staffFullname) . '"', false, false);
					}
					$this->db->group_end();
					$staff_assigned_data = $this->db->get('tblstaff')->result_array();
					if (!empty($staff_assigned_data)) {
						foreach ($staff_assigned_data as $k => $v) {
							$assigned[] = $v['staffid'];
						}
					}
				}

				if (empty($assigned)) {
					$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy Người Giám Sát Phòng Ngừa: ' . ($rvalue['staff_assigned'] ?? '');
				}

				$staff_evaluate = NULL;
				if (!empty($rvalue['staff_evaluate'])) {
					$staff_evaluate = $this->db->where(
						'CONCAT(firstname, " ", lastname) = "' . trim($rvalue['staff_evaluate']) . '"',
						false,
						false
					)->get_where('tblstaff')->row('staffid');
					if (empty($staff_evaluate)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy người đánh giá: ' . ($rvalue['staff_evaluate'] ?? '');
						continue;
					}
				}

				$staff_manage = NULL;
				if (!empty($rvalue['staff_manage'])) {
					$staff_manage = $this->db->where(
						'CONCAT(firstname, " ", lastname) = "' . trim($rvalue['staff_manage']) . '"',
						false,
						false
					)->get_where('tblstaff')->row('staffid');
					if (empty($staff_manage)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy quản lý chịu trách nhiệm: ' . ($rvalue['staff_manage'] ?? '');
						continue;
					}
				}

				$recommended_list_id = NULL;
				if (!empty($rvalue['recommended_list_id'])) {
					$recommended_list_id = $this->db->get_where(
						'tbl_relate',
						['code' => $rvalue['recommended_list_id']]
					)->row('id');
					if (empty($recommended_list_id)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy mã chi tiết liên quan: ' . ($rvalue['recommended_list_id'] ?? '');
						continue;
					}
				}

				$id_trouble = NULL;
				if (!empty($rvalue['id_trouble'])) {
					$id_trouble = $this->db->get_where('tbltrouble', ['code' => $rvalue['id_trouble']])->row('id');
					if (empty($id_trouble)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy mã sự cố: ' . ($rvalue['staff_manage'] ?? '');
						continue;
					}
				}

				$violation_group = NULL;
				if (!empty($rvalue['violation_group'])) {
					$violation_group = $this->db->get_where('tbl_violation_group', ['code' => $rvalue['violation_group']])->row('id');
					if (empty($violation_group)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy nhóm vi phạm: ' . ($rvalue['violation_group'] ?? '');
						continue;
					}
				}

				$machines_id = NULL;
				if (!empty($rvalue['machines_id'])) {
					$machines_id = $this->db->get_where('tbl_machines', ['code' => $rvalue['machines_id']])->row('id');
					if (empty($machines_id)) {
						$error[] = 'Dòng {' . ($rowExcel) . '} Không tìm thấy máy móc: ' . ($rvalue['machines_id'] ?? '');
						continue;
					}
				}

				$data['detail_tasks'] = '';
				if (!empty($data['id_delivery_records'])) {
					$this->db->select([
						'tbl_delivery_records.*',
					]);
					$data['data_delivery_records'] = $this->db->get_where(
						'tbl_delivery_records',
						['id' => $data['id_delivery_records']]
					)->row();
					if (!empty($data['data_delivery_records'])) {
						$data['title'] .= ' (Từ phiếu bàn giao ' . $data['data_delivery_records']->reference_no . ' - ' . _dt($data['data_delivery_records']->date) . ')';
						if (!empty($data['data_delivery_records'])) {
							if ($data['data_delivery_records']->type_object == 'productions_orders') {
								$data['id_production_orders'] = $data['data_delivery_records']->id_create;
							}
						}
						if (!empty($data['data_delivery_records']) && $data['data_delivery_records']->type_create == 'productions_orders_detail' && !empty($data['data_delivery_records']->id_create)) {
							$data['id_production_detail'] = $data['data_delivery_records']->id_create;
							//							if(!empty($data['id_production_detail'])) {
							//							}
							$this->db->select('GROUP_CONCAT(tbl_hand_over_task.name SEPARATOR "\n") as name_over_task');
							$this->db->join(
								'tbl_hand_over_task',
								'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id',
								'left'
							);
							$data['described'] = $this->db->get_where(
								'tbl_delivery_records_task',
								['tbl_delivery_records_task.delivery_records_id' => $data['id_delivery_records']]
							)->row('name_over_task');
						}
					}
					if (!empty($data['id_delivery_records_detail'])) {
						$this->db->select('tbl_delivery_records_task.*, tbl_hand_over_task.id_stage, CONCAT(tbl_packaging.code,(IF(tbl_delivery_records_task.task_hand_over_qualified = 1, " (Đạt)", IF(tbl_delivery_records_task.task_hand_over_qualified = 0, "", " (Không Đạt)"))))  as name_over_task');
						$this->db->join(
							'tbl_hand_over_task',
							'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id'
						);
						$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard');
						$data['data_delivery_records_detail'] = $this->db->get_where(
							'tbl_delivery_records_task',
							['tbl_delivery_records_task.id' => $data['id_delivery_records_detail']]
						)->row();
						$data['described'] = $data['data_delivery_records_detail']->name_over_task;
						if (!empty($data['data_delivery_records_detail']->id_stage)) {
							$id_stage = $data['id_stage'] = $data['data_delivery_records_detail']->id_stage;
						}
						$this->db->select('tbl_hand_over_task.name, method');
						$this->db->from('tbl_delivery_records_task');
						$this->db->where('tbl_delivery_records_task.id', $data['id_delivery_records_detail']);
						$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id');
						$hand_over_task = $this->db->get()->row_array();
						if (!empty($hand_over_task)) {
							$data['detail_tasks'] = $hand_over_task['name'] . ' - ' . $hand_over_task['method'];
						}
					} else {
						$this->db->select('GROUP_CONCAT(CONCAT(tbl_packaging.code,(IF(tbl_delivery_records_task.task_hand_over_qualified = 1, " (Đạt)", IF(tbl_delivery_records_task.task_hand_over_qualified = 0, "", " (Không Đạt)")))) separator "\n") as name_over_task');
						$this->db->join(
							'tbl_hand_over_task',
							'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id'
						);
						$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard');
						if (is_numeric($data['quantity_err'])) {
							$this->db->where('task_hand_over_qualified', 2);
						}
						$name_over_task = $this->db->get_where(
							'tbl_delivery_records_task',
							['tbl_delivery_records_task.delivery_records_id' => $data['id_delivery_records']]
						)->row();
						$data['described'] = $name_over_task->name_over_task;
					}
				}
				$arrKpi = [];
				if (!empty($category_kpi_criteria_id)) {
					foreach ($category_kpi_criteria_id as $kk => $vv) {
						$dtCategoryKpi = get_table_where('tbl_category_kpi_criteria', ['id' => $vv['id']], '', 'row_array');
						$arrKpi[] = [
							'category_kpi_id' => $dtCategoryKpi['category_kpi_id'],
							'category_kpi_criteria_id' => $vv['id']
						];
					}
				}


				$violation_point = 0;
				$violation_id = 0;
				$countTrouble = 0;
				if (!empty($id_trouble)) {
					$this->db->select('tbltrouble_violation_point.id as id, tbltrouble_violation_point.point as point');
					$this->db->join(
						'tbltrouble_violation_point',
						'tbltrouble_violation_point.id = tbltrouble.trouble_violation_point_id',
						'inner'
					);
					$this->db->where('tbltrouble.id', $id_trouble);
					$trouble_violation = $this->db->get('tbltrouble')->row_array();
					if (!empty($trouble_violation['id'])) {
						$violation_id = $trouble_violation['id'];
						$violation_point = $trouble_violation['point'];
					}
					$dates = to_sql_date(_dt($rvalue['date']), true);
					$dates = strtotime($dates);
					$dates = strftime("%m", $dates);
					$countTrouble = countTrouble($id_trouble, $dates);
				}

				$countViolate = 0;
				if (!empty($violate) && !empty($staff_responsible)) {
					$dates = to_sql_date(_dt($rvalue['date']), true);
					// $dates = strtotime($dates);
					// $dates = strftime("%Y", $dates);
					$_datas = getQuarterStartAndEndDate($dates);
					$countViolate = countViolate($rvalue['staff_responsible'], $_datas['start'], $_datas['end']);
				}



				$options = [
					'reference_no' => $reference_no,
					'date' => to_sql_date(_dt($rvalue['date']), true),
					'name_report' => !empty($rvalue['name_report']) ? $rvalue['name_report'] : '',
					'category_tasks' => !empty($category_tasks) ? $category_tasks : '',
					'id_departments' => !empty($id_departments) ? $id_departments : 0,
					//                    'id_production_detail' => !empty($data['id_production_detail']) ? $data['id_production_detail'] : null,
					'id_production_orders' => !empty($id_production_orders) ? $id_production_orders : null,
					'id_orders' => !empty($id_orders) ? $id_orders : null,
					//                    'suppler_id' => !empty($data['suppler_id']) ? $data['suppler_id'] : null,
					'production_stage' => $production_stage ?? 0,

					//                    'detail_tasks' => $data['detail_tasks'],

					//                    'type_stage_1' => !empty($data['type_stage_1']) ? $data['type_stage_1'] : 0,
					//                    'type_stage_2' => !empty($data['type_stage_2']) ? $data['type_stage_2'] : 0,
					//                    'type_stage_3' => !empty($data['type_stage_3']) ? $data['type_stage_3'] : 0,
					//                    'type_stage_4' => !empty($data['type_stage_4']) ? $data['type_stage_4'] : 0,

					'id_trouble' => !empty($id_trouble) ? $id_trouble : 0,
					'countTrouble' => $countTrouble,
					'countViolate' => $countViolate,
					'trouble_violation_point_id' => $violation_id,
					'trouble_violation_point' => $violation_point,
					//                    'responsible_type' => !empty($data['responsible_type']) ? $data['responsible_type'] : '',
					//                    'department_responsible' => !empty($data['department_responsible']) ? $data['department_responsible'] : 0,
					'staff_responsible' => !empty($staff_responsible) ? $staff_responsible : 0,
					'quantity_pcs' => number_format_data($rvalue['quantity_pcs'], false),
					'quantity' => number_format_data($rvalue['quantity_pcs'], false),
					//                    'time_of_recording' => to_sql_date($data['time_of_recording'], true),
					//                    'action_now_1' => !empty($data['action_now_1']) ? $data['action_now_1'] : 0,
					//                    'action_now_2' => !empty($data['action_now_2']) ? $data['action_now_2'] : 0,
					//                    'action_now_3' => !empty($data['action_now_3']) ? $data['action_now_3'] : 0,
					//                    'action_now_4' => !empty($data['action_now_4']) ? $data['action_now_4'] : 0,
					//                    'reason' => !empty($data['reason']) ? $data['reason'] : null,
					//                    'described' => !empty($data['described']) ? $data['described'] : null,
					//                    'overcome' => !empty($data['overcome']) ? $data['overcome'] : null,
					'note' => $rvalue['note'] ?? NULL,
					'id_tasks' => 0,
					//                    'id_delivery_records' => !empty($data['id_delivery_records']) ? $data['id_delivery_records'] : null,
					//                    'id_delivery_records_detail' => !empty($data['id_delivery_records_detail']) ? $data['id_delivery_records_detail'] : null,
					'create_by' => get_staff_user_id(),
					'date_create' => date('Y-m-d H:i:s'),
					'id_branch' => $id_branch ?? 0,
					'recommended_list_group_id' => $recommended_list_group_id ?? NULL,
					// 'recommended_list_id' => (!empty($data['recommended_list_id'][0]) ? $data['recommended_list_id'][0] : 0),
					'recommended_list_id' => $recommended_list_id ?? NULL,
					'role_id' => $role_id ?? NULL,
					'staff_evaluate' => !empty($staff_evaluate) ? $staff_evaluate : 0,
					//                    'note_fix' => !empty($data['note_fix']) ? $data['note_fix'] : null,
					'staff_handover' => !empty($staff_handover) ? $staff_handover : 0,
					'quantity_kpi' => !empty($rvalue['quantity_kpi']) ? number_unformat($rvalue['quantity_kpi']) : 0,
					'object_id' => 0,
					'object_type' => NULL,
					'violate' => !empty($violate) ? $violate : 0,
					'type_report' => !empty($rvalue['type_report']) ? $rvalue['type_report'] : 0,
					'category_recommended_id' => 0,
					'suggest_id' => 0,
					'suggest_id_detail' => 0,
					'staff_manage' => !empty($staff_manage) ? $staff_manage : 0,
					'id_internal_proposal_process' => 0,
					'id_internal_proposal_process_child' => 0,
					'id_internal_proposal' => 0,
					'violation_group' => $violation_group ?? 0,
					'id_quotes' => 0,
					'damage_cost' => !empty($rvalue['damage_cost']) ? number_unformat($rvalue['damage_cost']) : 0,
					'kpi_list_criteria_department_id' => $kpi_list_criteria_department ?? 0,
					'kpi_list_criteria_department_id_child' => $kpi_list_criteria_department_child ?? 0,
					'kpi_list_criteria_department_id_childd' => $kpi_list_criteria_department_childd ?? 0,
					'kpi_list_criteria_department_violate' => $rvalue['kpi_list_criteria_department_violate'],
					'machines_id' => $machines_id ?? 0,
					'downtime' => 0,
				];

				$success = $this->db->insert('tblproduction_report', $options);
				if (!empty($success)) {
					$id = $this->db->insert_id();
					$process = get_table_where('tbl_process');
					foreach ($process as $k => $v) {
						$ins = [];
						$ins['production_report_id'] = $id;
						$ins['process_id'] = $v['id'];
						$ins['name'] = $v['name'];
						$this->db->insert('tbl_process_production_report', $ins);
					}
					$setting_production_report = get_table_where('tbl_setting_production_report');
					foreach ($setting_production_report as $k => $v) {
						$ins = [];
						$ins['id_production_report'] = $id;
						$ins['id_process'] = $v['id_process'];
						$ins['id_role'] = $v['id_role'];
						$this->db->insert('tbl_role_production_report', $ins);
					}
					updateReference('production_report');
					if (!empty($_FILES['image']['name'])) {
						foreach ($_FILES['image']['name'] as $key => $value) {
							if (!empty($_FILES['image']['name'][$key]) && $_FILES['image']['size'][$key] > 0) {
								if (!file_exists($this->upload_path)) {
									mkdir($this->upload_path);
								}
								if (!file_exists($this->upload_path . $id . '/')) {
									mkdir($this->upload_path . $id . '/');
								}
								$arrayFile = [];
								$tmpFilePath = $_FILES['image']['tmp_name'][$key];
								if (!empty($tmpFilePath) && $tmpFilePath != '') {
									$filename = vn_to_str(
										@unique_filename(
											($this->upload_path . $id . '/'),
											'image' . '_' . time() . $_FILES['image']['name'][$key]
										)
									);
									// if (_upload_extension_allowed($filename)) {
									$newFilePath = $this->upload_path . $id . '/' . $filename;
									if (move_uploaded_file($tmpFilePath, $newFilePath)) {
										if (file_exists($newFilePath)) {
											$arrayFile[] = [
												'file_name' => $this->upload_path . $id . '/' . $filename,
												'rel_id' => $id,
												'rel_type' => 'production_report',
												'filetype' => $_FILES['image']['type'][$key],
												'staffid' => get_staff_user_id(),
												'dateadded' => date('Y-m-d H:i:s')
											];
										}
									}
									// }
								}
								if (!empty($arrayFile)) {
									$this->db->insert_batch('tblfiles', $arrayFile);
								}
							}
						}
					}
					if (!empty($id_trouble)) {

						$this->db->where('id_trouble', $id_trouble);
						$trouble_items = $this->db->get('tbltrouble_items')->result_array();
						if (!empty($trouble_items)) {
							foreach ($trouble_items as $type => $item) {
								if (!empty($item['name'])) {
									$this->db->insert('tblproduction_report_items', [
										'type' => $item['type'],
										'id_production_report' => $id,
										'name' => $item['name'],
										'ischeck' => ($item['type'] == 'procedure' || $item['type'] == 'fix') ? 1 : 0,
									]);
								}
							}
						}
					}
					if (!empty($assigned)) {
						foreach ($assigned as $k => $v) {
							$this->db->insert('tblproduction_report_assigned', [
								'id_production_report' => $id,
								'staff_id' => $v
							]);
						}
					}
					if (!empty($staff_handler)) {
						foreach ($staff_handler as $k => $v) {
							$this->db->insert('tblproduction_report_handler', [
								'id_production_report' => $id,
								'staff_id' => $v
							]);
						}
					}
					if (!empty($arrKpi)) {
						foreach ($arrKpi as $k => $value) {
							$value['production_report_id'] = $id;
							$this->db->insert('tbl_production_report_kpi', $value);
						}
					}
					$_data = [
						'name' => $rvalue['name_report'],
						'hourly_rate' => 0,
						'category_tasks' => !empty($category_tasks) ? $category_tasks : '',
						'startdate' => $rvalue['date'],
						'duedate' => null,
						'priority' => 2,
						'repeat_every_custom' => 1,
						'repeat_type_custom' => 'day',
						'rel_type' => 'production_report',
						'rel_id' => $id,
						'id_branch' => $id_branch ?? 0,
						'department_id' => !empty($id_departments) ? [$id_departments] : null,
						'description' => !empty($rvalue['described']) ? $rvalue['described'] : null,
					];
					$attachments = $this->db->get_where(
						'tblfiles',
						['rel_id' => $id, 'rel_type' => 'production_report']
					)->result_array();
					if (!empty($attachments)) {
						$_data['copy_attachments'] = $attachments;
					}
					$id_tasks = $this->tasks_model->add($_data, false, false);
					if (!empty($id_tasks)) {
						// $this->db->where('type', 'procedure');
						$this->db->where('ischeck', '1');
						$this->db->where('id_production_report', $id);
						$this->db->group_start();
						$this->db->where('type', 'procedure');
						$this->db->group_end();
						$procedure = $this->db->get('tblproduction_report_items')->result_array();
						if (!empty($procedure)) {
							foreach ($procedure as $key => $v) {
								$this->db->insert('tbltask_checklist_items', [
									'taskid' => $id_tasks,
									'description' => $v['name'],
									'dateadded' => date('Y-m-d H:i:s'),
									'addedfrom' => get_staff_user_id(),
									'list_order' => ($key + 1),
								]);
							}
						}
						$staffNow = get_staff_user_id();
						$this->db->where('id_production_report', $id);
						$internal_assigned = $this->db->get('tblproduction_report_assigned')->result_array();
						if (!empty($internal_assigned)) {
							foreach ($internal_assigned as $k => $v) {
								$this->db->insert('tbltask_followers', [
									'staffid' => $v['staff_id'],
									'taskid' => $id_tasks,
								]);
							}
						}
						$this->db->where('id_production_report', $id);
						$this->db->where('staff_id != "' . $staffNow . '"', false, false);
						$internal_handler = $this->db->get('tblproduction_report_handler')->result_array();
						if (!empty($internal_handler)) {
							foreach ($internal_handler as $k => $v) {
								$this->db->insert('tbltask_assigned', [
									'staffid' => $v['staff_id'],
									'taskid' => $id_tasks,
									'assigned_from' => $staffNow,
								]);
							}
						}
						$listEmail = [];
						//                        $this->db->select('group_concat(email) as list_email');
						//                        $this->db->where('id_production_report', $id);
						//                        $this->db->where('email is not null', false, false);
						//                        $this->db->join('tblstaff', 'tblstaff.staffid = tblproduction_report_handler.staff_id');
						//                        $listEmailHandler = $this->db->get('tblproduction_report_handler')->row('list_email');
						//                        if (!empty($listEmailHandler)) {
						//                            $listEmailHandler = explode(',', $listEmailHandler);
						//                            send_email($listEmailHandler, $this->get_content($id, 1));
						//                        }
					}
					$arrProductionReportReason = [];
					//                    if ($_trouble) {
					//                        foreach ($_trouble as $kT => $vT) {
					//                            if ($vT == 1) {
					//                                $reason = _string($dtReason[$kT] ?? '');
					//                                $arrProductionReportReason[] = [
					//                                    'pr_id' => $id,
					//                                    'trouble' => $kT,
					//                                    'is_check' => 1,
					//                                    'reason' => $reason,
					//                                ];
					//                            }
					//                        }
					//                    }
					if (!empty($arrProductionReportReason)) {
						$this->recommended_list_model->insertBatchProductionReportReason($arrProductionReportReason);
					}
				}

				if (!empty($error)) {
					continue;
				}

				$count++;
			}
		}
		echo json_encode(
			[
				'success' => true,
				'error' => !empty($error) ? implode(",<br/>", $error) : "",
				'alert_type' => 'success',
				'message' => 'Import thành công ' . $count . ' Items',
			]
		);
		die();
	}
	function GetJdTasks()
	{
		$data = [];
		$role_id_jd = !empty($this->input->post('role_id_jd')) ? $this->input->post('role_id_jd') : 0;
		$this->db->select("
            tbl_job_detail.id as id,
            tbl_job_detail.title as title,
            tbl_job_detail.code as code
        ");
		$this->db->from('tbl_job_detail');
		$this->db->where('tbl_job_detail.status', 1);
		$this->db->where('tbl_job_detail.role_id', $role_id_jd);
		$job_detail = $this->db->get()->result_array();
		$data['job_detail'] = $job_detail;
		echo json_encode($data);
	}
	function GetDetailJdTasks()
	{
		$data = [];
		$jd_tasks = !empty($this->input->post('jd_tasks')) ? $this->input->post('jd_tasks') : 0;
		$this->db->select("
            tbl_job_detail_child.id as id,
            tbl_job_detail_child.name as name,
            tbl_job_detail_child.type as type,
        ");
		$this->db->from('tbl_job_detail_child');
		$this->db->where('tbl_job_detail_child.job_detail_id', $jd_tasks);
		$job_detail = $this->db->get()->result_array();
		$responsibility = [];
		$jurisdiction = [];
		$requirement = [];
		$competency_standard = [];
		foreach ($job_detail as $key => $value) {
			if ($value['type'] == 1) {
				$responsibility[] = $value;
			}
			if ($value['type'] == 2) {
				$jurisdiction[] = $value;
			}
			if ($value['type'] == 3) {
				$requirement[] = $value;
			}
			if ($value['type'] == 4) {
				$competency_standard[] = $value;
			}
		}
		$data['responsibility'] = $responsibility;
		$data['jurisdiction'] = $jurisdiction;
		$data['requirement'] = $requirement;
		$data['competency_standard'] = $competency_standard;
		echo json_encode($data);
	}

	public function searchSalary3p($id = 0)
	{
		$term = $this->input->get('term');
		$role_id = $this->input->get('role_id') ?? 0;
		$limit = get_option('select2_limit');
		$this->db->select('
            tbl_salary_3p.id as id, 
            tbl_salary_3p.code as text
        ', false);
		$this->db->from('tbl_salary_3p');
		$this->db->where('tbl_salary_3p.role_id', $role_id);
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('role_id.code', $term);
			$this->db->group_end();
		}
		$this->db->limit($limit);
		$pod = $this->db->get()->result_array();

		$data['results'][] = ['text' => lang('Khung lương 3P'), 'children' => $pod];
		if (!empty($id)) {
			$dtRole = get_table_where('tbl_salary_3p', ['id' => $id], '', 'row_array');
			$data['row'] = ['id' => $dtRole['id'], 'text' => $dtRole['code']];
		}
		echo json_encode($data);
	}
}
