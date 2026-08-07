<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('projects_model');
		$this->color_department = [
			0 => '#bd4e4e',
			1 => '#18a76b',
			2 => '#4b6812',
			3 => '#b99512',
			4 => '#2116c7',
			5 => '#21783c',
			6 => '#8a6d3b',
			7 => '#ff6f00',
			8 => '#84c529',
		];
		$this->is_branch = true;
	}

	/* Open also all taks if user access this /tasks url */
	public function index($id = '')
	{
		if ($id == '' && $this->input->get('id')) {
			$id = $this->input->get('id');
		}
		$this->list_tasks($id);
	}

	/* List all tasks */
	public function list_tasks($id = '')
	{
		if (!has_permission('tasks', '', 'view') && !has_permission('tasks', '', 'view_own')) {
			access_denied('tasks');
		}
		if (!empty($this->is_branch) && !empty($id)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbltasks.id_branch', $list_branch);
					$this->db->group_end();
					$this->db->where('id', $id);
					$ktQuote = $this->db->get('tbltasks')->row();
				} else {
					$ktQuote = false;
				}
				if (empty($ktQuote)) {
					accessDenied();
				}
			}
		}
		close_setup_menu();
		// If passed from url
		$data['custom_view'] = $this->input->get('custom_view') ? $this->input->get('custom_view') : '';
		$data['taskid'] = $id;
		if ($this->input->get('kanban')) {
			$this->switch_kanban(0, true);
		}
		if ($this->input->get('not_kanban')) {
			$this->session->set_userdata([
				'tasks_kanban_view' => false,
			]);
		}
		$data['switch_kanban'] = false;
		$data['bodyclass'] = 'tasks-page';
		if ($this->session->userdata('tasks_kanban_view') == 'true') {
			$data['switch_kanban'] = true;
			$data['bodyclass'] = 'tasks-page kan-ban-body';
		}
		$data['title'] = _l('tasks');
		$this->db->select('staffid, CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as fullname');
		$data['staff'] = $this->db->get_where('tblstaff')->result_array();
		$data['departments'] = $this->db->get_where('tbldepartments', ['type' => 0])->result_array();
		$data['room'] = $this->db->get_where('tbl_room')->result_array();
		$data['categoryRecommended'] = $this->site_model->getCategoryRecommended();
		$data['category_tasks'] = $this->site_model->getCategoryTasks();
		$this->load->view('admin/tasks/manage', $data);
	}

	public function get_select_staff()
	{
		$room = $this->input->post('room');
		$this->db->select('staffid, CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as fullname');
		if (!empty($room)) {
			$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
			$this->db->where_in('tblroles.id_room', $room);
		}
		$list_staff = $this->db->get_where('tblstaff')->result_array();
		echo json_encode($list_staff);
		die();
	}

	public function table()
	{
		$this->app->get_table_data('tasks', ['color_department' => $this->color_department]);
	}

	public function staff_tasks()
	{
		$this->app->get_table_data('staff_tasks');
	}

	public function kanban()
	{
		$data['color_department'] = $this->color_department;
		echo $this->load->view('admin/tasks/kan_ban', $data, true);
	}

	public function ajax_search_assign_task_to_timer()
	{
		if ($this->input->is_ajax_request()) {
			$q = $this->input->post('q');
			$q = trim($q);
			$this->db->select('name, id,' . tasks_rel_name_select_query() . ' as subtext');
			$this->db->from(db_prefix() . 'tasks');
			$this->db->where('' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')');
			//   $this->db->where('id NOT IN (SELECT task_id FROM '.db_prefix().'taskstimers WHERE staff_id = ' . get_staff_user_id() . ' AND end_time IS NULL)');
			$this->db->where('status != ', 5);
			$this->db->where('billed', 0);
			$this->db->where('(name LIKE "%' . $q . '%" OR ' . tasks_rel_name_select_query() . ' LIKE "%' . $q . '%")');
			echo json_encode($this->db->get()->result_array());
		}
	}

	public function tasks_kanban_load_more()
	{
		$status = $this->input->get('status');
		$page = $this->input->get('page');
		$where = [];
		if ($this->input->get('project_id')) {
			$where['rel_id'] = $this->input->get('project_id');
			$where['rel_type'] = 'project';
		}
		$tasks = $this->tasks_model->do_kanban_query($status, $this->input->get('search'), $page, false, $where);
		foreach ($tasks as $task) {
			$this->load->view('admin/tasks/_kan_ban_card', [
				'task' => $task,
				'status' => $status,
			]);
		}
	}

	public function update_order()
	{
		$this->tasks_model->update_order($this->input->post());
	}

	public function task_id_search()
	{
		if ($this->input->is_ajax_request()) {
			$q = urldecode($this->input->get('q'));
			$q = trim($q);
			$this->db->select('tbltasks.id, tbltasks.id as name');
			$this->db->from(db_prefix() . 'tasks as tbltasks');
			if ($q != '') {
				$this->db->group_start();
				$this->db->like('tbltasks.name', $q);
				$this->db->or_like('tbltasks.id', $q);
				$this->db->group_end();
			}
			$this->db->limit(50);
			$result = $this->db->get()->result_array();
			echo json_encode($result);
			die;
		}
	}

	public function switch_kanban($set = 0, $manual = false)
	{
		if ($set == 1) {
			$set = 'false';
		} else {
			$set = 'true';
		}
		$this->session->set_userdata([
			'tasks_kanban_view' => $set,
		]);
		if ($manual == false) {
			// clicked on VIEW KANBAN from projects area and will redirect again to the same view
			if (strpos($_SERVER['HTTP_REFERER'], 'project_id') !== false) {
				redirect(admin_url('tasks'));
			} else {
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	// Used in invoice add/edit
	public function get_billable_tasks_by_project($project_id)
	{
		if ($this->input->is_ajax_request() && (has_permission('invoices', '', 'edit') || has_permission('invoices', '', 'create'))) {
			$customer_id = get_client_id_by_project_id($project_id);
			echo json_encode($this->tasks_model->get_billable_tasks($customer_id, $project_id));
		}
	}

	// Used in invoice add/edit
	public function get_billable_tasks_by_customer_id($customer_id)
	{
		if ($this->input->is_ajax_request() && (has_permission('invoices', '', 'edit') || has_permission('invoices', '', 'create'))) {
			echo json_encode($this->tasks_model->get_billable_tasks($customer_id));
		}
	}

	public function update_task_description($id)
	{
		if (has_permission('tasks', '', 'edit')) {
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'tasks', [
				'description' => $this->input->post('description', false),
			]);
		}
	}

	public function detailed_overview()
	{
		$overview = [];
		$has_permission_create = has_permission('tasks', '', 'create');
		$has_permission_view = has_permission('tasks', '', 'view');
		if (!$has_permission_view) {
			$staff_id = get_staff_user_id();
		} elseif ($this->input->post('member')) {
			$staff_id = $this->input->post('member');
		} else {
			$staff_id = '';
		}
		$month = ($this->input->post('month') ? $this->input->post('month') : date('m'));
		if ($this->input->post() && $this->input->post('month') == '') {
			$month = '';
		}

		$room_task = $this->input->post('room_task');
		$status = $this->input->post('status');
		$fetch_month_from = 'startdate';
		$year = ($this->input->post('year') ? $this->input->post('year') : date('Y'));
		$project_id = $this->input->get('project_id');
		for ($m = 1; $m <= 12; $m++) {
			if ($month != '' && $month != $m) {
				continue;
			}
			// Task rel_name
			$sqlTasksSelect = '*,' . tasks_rel_name_select_query() . ' as rel_name';
			// Task logged time
			$selectLoggedTime = get_sql_calc_task_logged_time('tmp-task-id');
			// Replace tmp-task-id to be the same like tasks.id
			$selectLoggedTime = str_replace('tmp-task-id', db_prefix() . 'tasks.id', $selectLoggedTime);
			if (is_numeric($staff_id)) {
				$selectLoggedTime .= ' AND staff_id=' . $staff_id;
				$sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
			} else {
				$sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
			}
			$sqlTasksSelect .= ' as total_logged_time';
			// Task checklist items
			$sqlTasksSelect .= ',' . get_sql_select_task_total_checklist_items();
			if (is_numeric($staff_id)) {
				$sqlTasksSelect .= ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_checklist_items WHERE taskid=' . db_prefix() . 'tasks.id AND finished=1 AND finished_from=' . $staff_id . ') as total_finished_checklist_items';
			} else {
				$sqlTasksSelect .= ',' . get_sql_select_task_total_finished_checklist_items();
			}
			// Task total comment and total files
			$selectTotalComments = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_comments WHERE taskid=' . db_prefix() . 'tasks.id';
			$selectTotalFiles = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'files WHERE rel_id=' . db_prefix() . 'tasks.id AND rel_type="task"';
			if (is_numeric($staff_id)) {
				$sqlTasksSelect .= $selectTotalComments . ' AND staffid=' . $staff_id . ') as total_comments_staff';
				$sqlTasksSelect .= $selectTotalFiles . ' AND staffid=' . $staff_id . ') as total_files_staff';
			}
			$sqlTasksSelect .= $selectTotalComments . ') as total_comments';
			$sqlTasksSelect .= $selectTotalFiles . ') as total_files';
			// Task assignees
			$sqlTasksSelect .= ',' . get_sql_select_task_asignees_full_names() . ' as assignees' . ',' . get_sql_select_task_assignees_ids() . ' as assignees_ids';
			$this->db->select($sqlTasksSelect);
			$this->db->where('MONTH(' . $fetch_month_from . ')', $m);
			$this->db->where('YEAR(' . $fetch_month_from . ')', $year);
			if ($project_id && $project_id != '') {
				$this->db->where('rel_id', $project_id);
				$this->db->where('rel_type', 'project');
			}
			if (!$has_permission_view) {
				$sqlWhereStaff = '(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . ')';
				// User dont have permission for view but have for create
				// Only show tasks createad by this user.
				if ($has_permission_create) {
					$sqlWhereStaff .= ' OR addedfrom=' . get_staff_user_id();
				}
				$sqlWhereStaff .= ')';
				$this->db->where($sqlWhereStaff);
			} elseif ($has_permission_view) {
				if (is_numeric($staff_id)) {
					$this->db->where('(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . '))');
				}
			}
			if ($status) {
				$this->db->where('status', $status);
			}

			if (!empty($room_task)) {
				$this->db->where('EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . implode(',', $room_task) . '))', false, false);
			}

			$this->db->order_by($fetch_month_from, 'ASC');
			array_push($overview, $m);
			$overview[$m] = $this->db->get(db_prefix() . 'tasks')->result_array();
		}
		unset($overview[0]);
		$overview = [
			'staff_id' => $staff_id,
			'detailed' => $overview,
		];
		$data['members'] = $this->staff_model->get();
		$data['overview'] = $overview['detailed'];
		// print_arrays($data['overview']);

		$data['years'] = $this->tasks_model->get_distinct_tasks_years(($this->input->post('month_from') ? $this->input->post('month_from') : 'startdate'));
		$data['staff_id'] = $overview['staff_id'];
		$data['room'] = $this->db->get_where('tbl_room')->result_array();
		$data['room_task'] = $room_task;
		$data['title'] = _l('detailed_overview');
		$this->load->view('admin/tasks/detailed_overview', $data);
	}

	public function init_relation_tasks($rel_id, $rel_type)
	{
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data('tasks_relations', [
				'rel_id' => $rel_id,
				'rel_type' => $rel_type,
			]);
		}
	}

	/* Add new task or update existing */
	public function task($id = '')
	{
		if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
			ajax_access_denied();
		}
		$data = [];

		// FOr new task add directly from the projects milestones
		if ($this->input->get('milestone_id')) {
			$this->db->where('id', $this->input->get('milestone_id'));
			$milestone = $this->db->get(db_prefix() . 'milestones')->row();
			if ($milestone) {
				$data['_milestone_selected_data'] = [
					'id' => $milestone->id,
					'due_date' => _d($milestone->due_date),
				];
			}
		}
		if ($this->input->get('start_date')) {
			$data['start_date'] = $this->input->get('start_date');
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['description'] = $this->input->post('description', false);
			if (!empty($this->is_branch)) {
				if (empty($data['id_branch'])) {
					echo json_encode([
						'success' => false,
						'message' => 'Vui lòng chọn chi nhánh',
					]);
					die();
				}
			}
			if ($id == '') {
				if (!has_permission('tasks', '', 'create')) {
					header('HTTP/1.0 400 Bad error');
					echo json_encode([
						'success' => false,
						'message' => _l('access_denied'),
					]);
					die;
				}
				$id = $this->tasks_model->add($data);
				$_id = false;
				$success = false;
				$message = '';
				if ($id) {
					if (!empty($data['category_recommended_id'])) {
						$this->db->select('tbl_category_recommended.id as id,tbl_category_recommended.name_table,type, ballot_type', false);
						$this->db->from('tbl_category_recommended');
						$this->db->where('tbl_category_recommended.id', $data['category_recommended_id']);
						$rs = $this->db->get()->row_array();
						if (!empty($rs)) {

							$this->db->select($rs['name_table'] . '.*');
							$this->db->from($rs['name_table']);
							if ($rs['name_table'] == 'tbl_suggest_probationary_evaluate') {
								$this->db->where($rs['name_table'] . '.type', $rs['type']);
							} elseif ($rs['name_table'] == 'tbl_suggest_evaluate') {
								if (!empty($rs['type'])) {
									$this->db->where($rs['name_table'] . '.object_type', $rs['type']);
								}
							} else if ($rs['name_table'] == 'tblsuggest_test_item_quality') {
								if (!empty($rs['type'])) {
									$this->db->where($rs['name_table'] . '.type', $rs['type']);
								}
								if (!empty($rs['ballot_type'])) {
									if ($rs['ballot_type'] == 2) {
										$this->db->select($rs['name_table'] . '.code_evaluate as code');
										$this->db->where($rs['name_table'] . '.status', 1);
									}
								}
							} elseif ($rs['name_table'] == 'tbl_suggest_task') {
								$this->db->where($rs['name_table'] . '.id', $data['suggest_id']);
							}
							$result = $this->db->get()->result_array();
						}
					}
					if ($data['category_recommended_id'] == 28 && !empty($result)) {
						$suggest_task_staff = get_table_where('tbl_suggest_task_staff', ['suggest_task_id' => $data['suggest_id']]);
						if (!empty($suggest_task_staff)) {
							foreach ($suggest_task_staff as $key => $value) {
								$this->db->insert(db_prefix() . 'task_assigned', [
									'taskid' => $id,
									'staffid' => $value['staff_id'],
									'assigned_from' => $value['staff_id'],
								]);
							}
						}
					}
					//phiếu yêu cầu tuyển dụng v2
					else if (!empty($data['category_recommended_id']) && !empty($data['rel_append_id'])) {
						$this->db->where('id', $data['category_recommended_id']);
						$ktCategoryRecommended = $this->db->get('tbl_category_recommended')->row();
						if (!empty($ktCategoryRecommended)) {
							if ($ktCategoryRecommended->name_table == 'tbl_hr_requirements') {
								$this->db->where('id', $data['rel_append_id']);
								$this->db->where('id_requirements', $data['suggest_id']);
								$requirements_step = $this->db->get('tbl_hr_requirements_step')->row();
								if (!empty($requirements_step)) {
									$this->db->where('id', $requirements_step->id);
									$this->db->update('tbl_hr_requirements_step', [
										'status' => 1,
										'id_tasks' => $id,
									]);
								}
							} else if ($ktCategoryRecommended->name_table == 'tbl_suggest_outsource') {
								$this->db->where('step', $data['rel_append_id']);
								$this->db->where('id_suggest_outsource', $data['suggest_id']);
								$step = $this->db->get('tbl_suggest_outsource_step')->row();
								if (!empty($step)) {
									$this->db->where('id', $step->id);
									$this->db->update('tbl_suggest_outsource_step', [
										'status' => 1,
										'id_tasks' => $id,
									]);
								}
							}
						}
					}
					$success = true;
					$_id = $id;
					$message = _l('added_successfully', _l('task'));
					$uploadedFiles = handle_task_attachments_array($id);
					if ($uploadedFiles && is_array($uploadedFiles)) {
						foreach ($uploadedFiles as $file) {
							$this->misc_model->add_attachment_to_database($id, 'task', [$file]);
						}
					}
					$rowTask = $this->site_model->rowTasks($id);
					$staff_create = get_staff_full_name();
					$task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
					$task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);
					$rowName = '';
					$task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $rowTask['id_list_object']], '', 'row');
					if (!empty($task_rel_value_list)) {
						$rowName .= $task_rel_value_list->name;
					}
					if (!empty($task_rel_value['type'])) {
						if (!empty($rowName)) {
							$rowName .= ' và ';
						}
						$rowName .= _l('c_tasks_' . $task_rel_value['type']) . ' ' . $task_rel_value['name'];
					}
					if (!empty($rowName)) {
						$rowName = '<span style="color:black;font-weight:bold">Liên quan đến: </span>: ' . $rowName;
					}
					$dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa phân công công việc ' . $rowName . ' vào ngày ' . _dhau($rowTask['startdate']) . ' Vui lòng theo dõi và tiến hành cập nhật công việc!';
					$arrId = [];
					$arrIdsendemail = [];
					$this->db->select('tbltask_assigned.staffid');
					$this->db->from('tbltask_assigned');
					$this->db->where('taskid', $id);
					$taskAss = $this->db->get()->result_array();
					if (!empty($taskAss)) {
						foreach ($taskAss as $k => $v) {
							$arrId[] = $v['staffid'];
							$arrIdsendemail[] = $v['staffid'];
						}
					}
					$this->db->select(
						'tblstaff.staffid as staff_id',
						false
					);
					$this->db->from('tblstaff');
					if (!empty($arrId)) {
						$this->db->where_not_in('tblstaff.staffid', $arrId);
					}
					$this->db->where('tblstaff.admin', 1);
					$staffAdmin = $this->db->get()->result_array();
					if (!empty($staffAdmin)) {
						foreach ($staffAdmin as $k => $v) {
							$arrId[] = $v['staff_id'];
						}
					}
					//					$arrId[] = get_staff_user_id();
					$arrId = array_unique($arrId);
					$arrIdsendemail = array_unique($arrIdsendemail);

					if (!empty($arrId)) {
						foreach ($arrId as $key => $value) {
							$notification_data = [
								'date' => date('Y-m-d H:i:s'),
								'description' => $dataHtml,
								'touserid' => $value,
								'link' => '#taskid=' . $id,
								'type' => 6,
								'object_id' => $id,
								'object_type' => 'tasks',
								'onclick' => 'init_task_modal(' . $id . '); return false;',
							];
							$arrNotiWeb[] = $notification_data;
							$notifiedUsers[] = $value;
						}
					}
					if (!empty($data['category_recommended_id']) && $data['category_recommended_id'] == 28) {
						if (!empty($arrIdsendemail)) {
							foreach ($arrIdsendemail as $key => $value) {
								$member = $this->staff_model->get($value);
								@send_mail_task_assignees($member->email, $id);
							}
						}
					}
					if (!empty($arrNotiWeb)) {
						$this->db->insert_batch('tblnotifications', $arrNotiWeb);
					}
					if (!empty($notifiedUsers)) {
						pusher_trigger_notification($notifiedUsers);
					}
				}
				echo json_encode([
					'success' => $success,
					'id' => $_id,
					'message' => $message,
				]);
			} else {
				if (!has_permission('tasks', '', 'edit')) {
					header('HTTP/1.0 400 Bad error');
					echo json_encode([
						'success' => false,
						'message' => _l('access_denied'),
					]);
					die;
				}
				$success = $this->tasks_model->update($data, $id);
				$message = '';
				if ($success) {
					$message = _l('updated_successfully', _l('task'));
				}
				echo json_encode([
					'success' => $success,
					'message' => $message,
					'id' => $id,
				]);
			}
			die;
		}
		$data['milestones'] = [];
		$data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
		if ($id == '') {
			$title = _l('add_new', _l('task_lowercase'));
		} else {
			$data['task'] = $this->tasks_model->get($id);
			if ($data['task']->rel_type == 'project') {
				$data['milestones'] = $this->projects_model->get_milestones($data['task']->rel_id);
			}
			$title = _l('edit', _l('task_lowercase')) . ' ' . $data['task']->name;
			$data['task']->department_id = $this->db->select('GROUP_CONCAT(department_id) as list_department_id')->get_where('tbltask_department', ['task_id' => $id])->row('list_department_id');
			$data['task']->department_id = !empty($data['task']->department_id) ? explode(',', $data['task']->department_id) : [];
		}
		$data['project_end_date_attrs'] = [];
		if ($this->input->get('rel_type') == 'project' && $this->input->get('rel_id')) {
			$project = $this->projects_model->get($this->input->get('rel_id'));
			if ($project->deadline) {
				$data['project_end_date_attrs'] = [
					'data-date-end-date' => $project->deadline,
				];
			}
		}
		$data['typePOD'] = $this->input->get('typePOD');
		$data['podId'] = $this->input->get('podId');
		$data['suggest_id'] = $this->input->get('suggest_id');
		$data['category_recommended_id'] = $this->input->get('category_recommended_id');
		$data['rel_append_id'] = $this->input->get('rel_append_id'); // công bổ sung để sử dụng riêng
		$data['id_room'] = $this->input->get('id_room'); // công bổ sung để sử dụng riêng
		$result = [];
		if (!empty($data['task'])) {
			$this->db->select('tbl_category_recommended.id as id,tbl_category_recommended.name_table, ballot_type, type', false);
			$this->db->from('tbl_category_recommended');
			$this->db->where('tbl_category_recommended.id', $data['task']->category_recommended_id);
			$rs = $this->db->get()->row_array();
			if (!empty($rs)) {
				if ($rs['name_table'] == 'tblsuggest_test_item_quality') {
					$this->db->select($rs['name_table'] . '.*');
					if (!empty($rs['type'])) {
						$this->db->where($rs['name_table'] . '.type', $rs['type']);
					}
					if (!empty($rs['ballot_type'])) {
						if ($rs['ballot_type'] == 2) {
							$this->db->select($rs['name_table'] . '.code_evaluate as code');
							$this->db->where($rs['name_table'] . '.status', 1);
						}
					}
					$this->db->from($rs['name_table']);
					$result = $this->db->get()->row_array();
				} else {
					$this->db->select($rs['name_table'] . '.*');
					$this->db->from($rs['name_table']);
					$result = $this->db->get()->result_array();
				}
			}
			if (!empty($result)) {
				foreach ($result as $key => $value) {
					$reference_no = '';
					if (!empty($value['reference_no'])) {
						$reference_no = $value['reference_no'];
					} elseif (!empty($value['code'])) {
						$reference_no = $value['code'];
					}
					$staff_suggest_name = "";
					if (!empty($value['staff_suggest'])) {
						$staff_suggest_name = get_staff_full_name($value['staff_suggest']);
					}
					$result[$key]['reference_no'] = $reference_no;
					$result[$key]['staff_suggest_name'] = $staff_suggest_name;
				}
			}
		} elseif (!empty($data['category_recommended_id'])) {
			$this->db->select('tbl_category_recommended.id as id,tbl_category_recommended.name_table,type, ballot_type', false);
			$this->db->from('tbl_category_recommended');
			$this->db->where('tbl_category_recommended.id', $data['category_recommended_id']);
			$rs = $this->db->get()->row_array();
			if (!empty($rs)) {

				$this->db->select($rs['name_table'] . '.*');
				$this->db->from($rs['name_table']);
				if ($rs['name_table'] == 'tbl_suggest_probationary_evaluate') {
					$this->db->where($rs['name_table'] . '.type', $rs['type']);
				} elseif ($rs['name_table'] == 'tbl_suggest_evaluate') {
					if (!empty($rs['type'])) {
						$this->db->where($rs['name_table'] . '.object_type', $rs['type']);
					}
				} else if ($rs['name_table'] == 'tblsuggest_test_item_quality') {
					if (!empty($rs['type'])) {
						$this->db->where($rs['name_table'] . '.type', $rs['type']);
					}
					if (!empty($rs['ballot_type'])) {
						if ($rs['ballot_type'] == 2) {
							$this->db->select($rs['name_table'] . '.code_evaluate as code');
							$this->db->where($rs['name_table'] . '.status', 1);
						}
					}
				} elseif ($rs['name_table'] == 'tbl_suggest_task') {
					$this->db->where($rs['name_table'] . '.id', $data['suggest_id']);
				}
				$result = $this->db->get()->result_array();
			}
			if (!empty($result)) {
				foreach ($result as $key => $value) {
					$reference_no = '';
					if (!empty($value['reference_no'])) {
						$reference_no = $value['reference_no'];
					} elseif (!empty($value['code'])) {
						$reference_no = $value['code'];
					}
					$staff_suggest_name = "";
					if (!empty($value['staff_suggest'])) {
						$staff_suggest_name = get_staff_full_name($value['staff_suggest']);
					}
					$result[$key]['reference_no'] = $reference_no;
					$result[$key]['staff_suggest_name'] = $staff_suggest_name;
				}
			}
		}
		$data['dtSuggest'] = $result;
		$data['pod'] = '';
		if (!empty($data['podId'])) {
			$this->load->model('manufactures_model');
			$data['pod'] = $this->manufactures_model->rowProductionsOrdersDetais($data['podId']);
		}
		$data['shiftWork'] = $this->site_model->getShiftWork();
		$data['id'] = $id;
		$data['title'] = $title;
		$data['departments'] = $this->db->get('tbl_room')->result_array();
		//			if (!is_admin()) {
		//				$staffNow = get_staff_user_id();
		//				$this->db->select('GROUP_CONCAT(departmentid) as list_departments');
		//				$this->db->where('tblstaff_departments.staffid', $staffNow);
		//				$staff_departments = $this->db->get('tblstaff_departments')->row('list_departments');
		//				if (!empty($staff_departments)) {
		//					$staff_departments = explode(',', $staff_departments);
		//					$this->db->group_start();
		//					foreach ($staff_departments as $key => $value) {
		//						$this->db->or_where('FIND_IN_SET(' . $value . ', tblcategory_tasks.departments)');
		//					}
		//					$this->db->group_end();
		//				} else {
		//					$this->db->where('id', 0);
		//				}
		//			}
		// $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
		$arr_id = !empty($data['task']->category_tasks) ? [$data['task']->category_tasks] : null;
		$data['category_tasks'] = $this->site_model->getCategoryTasks($arr_id);
		$data['departments_tasks'] = $this->db->get('tbldepartments_tasks')->result_array();
		$data['categoryRecommended'] = $this->site_model->getCategoryRecommended();

		//
		$_purchase_order_id = $_GET['purchase_order_id'] ?? '';
		if ($_purchase_order_id) {
			$data['_purchase_order_id'] = $_purchase_order_id;
			$data['_purchase_order'] = get_table_where('tblpurchase_order', ['id' => $_purchase_order_id], '', 'row_array');
		}

		$_import = $_GET['import_id'] ?? '';
		if ($_import) {
			$data['_import_id'] = $_import;
			$data['_import'] = get_table_where('tblimport', ['id' => $_import], '', 'row_array');
		}

		$_order_id = $_GET['order_id'] ?? '';
		if ($_order_id) {
			$data['_order_id'] = $_order_id;
			$data['_order'] = get_table_where('tbl_orders', ['id' => $_order_id], '', 'row_array');
		}

		$_po_id = $_GET['po_id'] ?? '';
		if ($_po_id) {
			$data['_po_id'] = $_po_id;
			$data['_po'] = get_table_where('tbl_productions_orders', ['id' => $_po_id], '', 'row_array');
		}
		//

		$this->db->select('tbl_stages.id as id,tbl_stages.name as name');
		$this->db->from('tbl_stages');
		$this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.stage_id = tbl_stages.id');
		$this->db->where('tbl_productions_orders_items_stages.productions_orders_id', !empty($data['task']) ? $data['task']->po_id : 0);
		$this->db->group_by('tbl_stages.id');
		$dtStage = $this->db->get()->result_array();
		$data['dtStage'] = $dtStage;
		$this->load->view('admin/tasks/task', $data);
	}

	public function copy()
	{
		if (has_permission('tasks', '', 'create')) {
			$new_task_id = $this->tasks_model->copy($this->input->post());
			$response = [
				'new_task_id' => '',
				'alert_type' => 'warning',
				'message' => _l('failed_to_copy_task'),
				'success' => false,
			];
			if ($new_task_id) {
				$response['message'] = _l('task_copied_successfully');
				$response['new_task_id'] = $new_task_id;
				$response['success'] = true;
				$response['alert_type'] = 'success';
			}
			echo json_encode($response);
		}
	}

	public function get_billable_task_data($task_id)
	{
		$task = $this->tasks_model->get_billable_task_data($task_id);
		$task->description = seconds_to_time_format($task->total_seconds) . ' ' . _l('hours');
		echo json_encode($task);
	}

	/**
	 * Task ajax request modal
	 * @param mixed $taskid
	 * @return mixed
	 */
	public function get_task_data($taskid, $return = false)
	{
		$tasks_where = [];
		if (!has_permission('tasks', '', 'view')) {
			$tasks_where = get_tasks_where_string(false);
		}
		$task = $this->tasks_model->get($taskid, $tasks_where);
		if (!$task) {
			header('HTTP/1.0 404 Not Found');
			echo 'Task not found';
			die();
		}
		$data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
		$data['task'] = $task;
		$data['id'] = $task->id;
		$departments = $this->db->get_where('tbltask_department', ['task_id' => $task->id])->result_array();
		if (!empty($departments)) {
			$arrayDepartments = [];
			foreach ($departments as $key => $value) {
				$arrayDepartments[] = $value['department_id'];
			}
			$this->db->select('*, CONCAT(firstname," ",lastname) as full_name');
			$this->db->group_start();
			$this->db->where('EXISTS (
				SELECT 1 
				FROM tblstaff_departments 
				WHERE departmentid IN (' . implode(',', $arrayDepartments) . ')
				AND tblstaff_departments.staffid = tblstaff.staffid
			)', false, false);
			$this->db->or_where('tblstaff.admin', 1);
			$this->db->group_end();
			$data['staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
		} else {
			$data['staff'] = $this->staff_model->get('', ['active' => 1]);
		}
		$data['staff'] = $this->staff_model->get('', ['active' => 1]);
		$data['reminders'] = $this->tasks_model->get_reminders($taskid);
		$data['staff_reminders'] = $this->tasks_model->get_staff_members_that_can_access_task($taskid);
		$data['project_deadline'] = null;
		if ($task->rel_type == 'project') {
			$data['project_deadline'] = get_project_deadline($task->rel_id);
		}
		$data['color_department'] = $this->color_department;
		if ($return == false) {
			$this->load->view('admin/tasks/view_task_template', $data);
		} else {
			return $this->load->view('admin/tasks/view_task_template', $data, true);
		}
	}

	public function add_reminder($task_id)
	{
		$message = '';
		$alert_type = 'warning';
		if ($this->input->post()) {
			$success = $this->misc_model->add_reminder($this->input->post(), $task_id);
			if ($success) {
				$alert_type = 'success';
				$message = _l('reminder_added_successfully');
			}
		}
		echo json_encode([
			'taskHtml' => $this->get_task_data($task_id, true),
			'alert_type' => $alert_type,
			'message' => $message,
		]);
	}

	public function edit_reminder($id)
	{
		$reminder = $this->misc_model->get_reminders($id);
		if ($reminder && ($reminder->creator == get_staff_user_id() || is_admin()) && $reminder->isnotified == 0) {
			$success = $this->misc_model->edit_reminder($this->input->post(), $id);
			echo json_encode([
				'taskHtml' => $this->get_task_data($reminder->rel_id, true),
				'alert_type' => 'success',
				'message' => ($success ? _l('updated_successfully', _l('reminder')) : ''),
			]);
		}
	}

	public function delete_reminder($rel_id, $id)
	{
		$success = $this->misc_model->delete_reminder($id);
		$alert_type = 'warning';
		$message = _l('reminder_failed_to_delete');
		if ($success) {
			$alert_type = 'success';
			$message = _l('reminder_deleted');
		}
		echo json_encode([
			'taskHtml' => $this->get_task_data($rel_id, true),
			'alert_type' => $alert_type,
			'message' => $message,
		]);
	}

	public function get_staff_started_timers($return = false)
	{
		$data['startedTimers'] = $this->misc_model->get_staff_started_timers();
		$_data['html'] = $this->load->view('admin/tasks/started_timers', $data, true);
		$_data['total_timers'] = count($data['startedTimers']);
		$timers = json_encode($_data);
		if ($return) {
			return $timers;
		}
		echo $timers;
	}

	public function save_checklist_item_template()
	{
		if (has_permission('checklist_templates', '', 'create')) {
			$id = $this->tasks_model->add_checklist_template($this->input->post('description'));
			echo json_encode(['id' => $id]);
		}
	}

	public function remove_checklist_item_template($id)
	{
		if (has_permission('checklist_templates', '', 'delete')) {
			$success = $this->tasks_model->remove_checklist_item_template($id);
			echo json_encode(['success' => $success]);
		}
	}

	public function init_checklist_items()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$post_data = $this->input->post();
				$data['task_id'] = $post_data['taskid'];
				$data['checklists'] = $this->tasks_model->get_checklist_items($post_data['taskid']);
				$this->load->view('admin/tasks/checklist_items_template', $data);
			}
		}
	}

	public function task_tracking_stats($task_id)
	{
		$data['stats'] = json_encode($this->tasks_model->task_tracking_stats($task_id));
		$this->load->view('admin/tasks/tracking_stats', $data);
	}

	public function checkbox_action($listid, $value)
	{
		die;
		$this->db->where('id', $listid);
		$this->db->update(db_prefix() . 'task_checklist_items', [
			'finished' => $value,
		]);
		if ($this->db->affected_rows() > 0) {
			if ($value == 1) {
				$this->db->where('id', $listid);
				$this->db->update(db_prefix() . 'task_checklist_items', [
					'finished_from' => get_staff_user_id(),
					'date_finished' => date('Y-m-d H:i:s'),
				]);
				hooks()->do_action('task_checklist_item_finished', $listid);
				$this->db->where('id', $listid);
				$taskid = $this->db->get('tbltask_checklist_items')->row('taskid');
				if (!empty($taskid)) {
					$this->db->where('taskid', $taskid);
					$this->db->where('finished', 0);
					$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
					if (empty($ktChecklist)) {
						$success = $this->tasks_model->mark_as(5, $taskid);
						if (!empty($success)) {
							echo $taskid;
							die();
						}
					}
				}
			}
		}
	}

	public function add_checklist_item()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				echo json_encode([
					'success' => $this->tasks_model->add_checklist_item($this->input->post()),
				]);
			}
		}
	}

	public function update_checklist_order()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$this->tasks_model->update_checklist_order($this->input->post());
			}
		}
	}

	public function delete_checklist_item($id)
	{
		$list = $this->tasks_model->get_checklist_item($id);
		if (has_permission('tasks', '', 'delete') || $list->addedfrom == get_staff_user_id()) {
			if ($this->input->is_ajax_request()) {
				echo json_encode([
					'success' => $this->tasks_model->delete_checklist_item($id),
				]);
			}
		}
	}

	public function update_checklist_item()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$desc = $this->input->post('description');
				$desc = trim($desc);
				$this->tasks_model->update_checklist_item($this->input->post('listid'), $desc);
				echo json_encode(['can_be_template' => (total_rows(db_prefix() . 'tasks_checklist_templates', ['description' => $desc]) == 0)]);
			}
		}
	}

	public function make_public($task_id)
	{
		if (!has_permission('tasks', '', 'edit')) {
			json_encode([
				'success' => false,
			]);
			die;
		}
		echo json_encode([
			'taskHtml' => $this->get_task_data($task_id, true),
			'success' => $this->tasks_model->make_public($task_id),
		]);
	}

	public function add_external_attachment()
	{
		if ($this->input->post()) {
			$this->tasks_model->add_attachment_to_database($this->input->post('task_id'), $this->input->post('files'), $this->input->post('external'));
		}
	}

	/* Add new task comment / ajax */
	public function add_task_comment()
	{
		$data = $this->input->post();
		$data['content'] = $this->input->post('content', false);
		if ($this->input->post('no_editor')) {
			$data['content'] = nl2br($this->input->post('content'));
		}
		$comment_id = false;
		if (
			$data['content'] != ''
			|| (isset($_FILES['file']['name']) && is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
		) {
			$comment_id = $this->tasks_model->add_task_comment($data);
			if ($comment_id) {
				$commentAttachments = handle_task_attachments_array($data['taskid'], 'file');
				if ($commentAttachments && is_array($commentAttachments)) {
					foreach ($commentAttachments as $file) {
						$file['task_comment_id'] = $comment_id;
						$this->misc_model->add_attachment_to_database($data['taskid'], 'task', [$file]);
					}
					if (count($commentAttachments) > 0) {
						$this->db->query('UPDATE ' . db_prefix() . "task_comments SET content = CONCAT(content, '[task_attachment]')
                            WHERE id = " . $comment_id);
					}
				}
			}
		}
		//send message zalo
		if (!empty($comment_id)) {
			$url = "https://openapi.zalo.me/v2.0/oa/message";
			$access_token = get_option('token_zalo');
			$query_fields = ['access_token' => $access_token];
			$curl = curl_init($url . '?' . http_build_query($query_fields));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			$taskId = $data['taskid'];
			$staffId = get_staff_user_id();
			$staffName = get_staff_full_name($staffId);
			$assignedList = $this->site_model->getTaskAssignedNotStaffId($taskId, $staffId);
			$taskData = $this->site_model->rowTasks($taskId);
			$str = htmlentities(strip_tags($data['content']));
			$str = '[' . $staffName . '] bình luận ' . html_entity_decode($data['content']);
			if (!empty($assignedList)) {
				foreach ($assignedList as $k => $val) {
					$image = base_url('assets/images/user-placeholder.jpg');
					if (!empty($val['profile_image'])) {
						$image = base_url('uploads/staff_profile_images/' . $val['staffid'] . '/' . $val['profile_image']);
					}
					$dataZalo = [
						'recipient' => [
							"user_id" => $val['id_zalo']
						],
						// 'message' => [
						//     'text' => $str
						// ]
						'message' => [
							'attachment' => [
								"type" => "template",
								"payload" => [
									"template_type" => "list",
									"elements" => [
										[
											"title" => $taskData['name'],
											"subtitle" => $str,
											"image_url" => $image,
											"default_action" => [
												"url" => base_url('admin/tasks'),
												"type" => "oa.open.url"
											]
										]
									]
								],
							]
						],
					];
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
					$response = curl_exec($curl);
				}
			}
		}
		if (!empty($comment_id)) {
			$taskData = $this->site_model->rowTasks($data['taskid']);
			$this->db->where('taskid', $data['taskid']);
			$assignedTasks = $this->db->get('tbltask_assigned')->result_array();
			foreach ($assignedTasks as $k => $val) {
				$dataHtml = '
						<img src="' . base_url('uploads/company/logo.jpg') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
						Hệ thống - ' . get_staff_full_name() . ' vừa bình luận <span style="color:black;font-weight:bold">Công Việc</span> của bạn đang phụ trách vào lúc ' . _dt(date('Y-m-d H:i:s'));
				$notification_data = [
					'date' => date('Y-m-d H:i:s'),
					'description' => $dataHtml,
					'touserid' => $val['staffid'],
					'link' => 'tasks/get_task_data/' . $data['taskid'],
					'type' => 6,
					'object_id' => $data['taskid'],
					'object_type' => 'tasks',
					'onclick' => 'init_task_modal(' . $data['taskid'] . '); return false;',
				];
				$this->db->insert('tblnotifications', $notification_data);
				send_notification_app_c($taskData['id'], [
					'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa bình luận công việc của bạn đang phụ trách vào lúc ' . _dt(date('Y-m-d H:i:s')),
					'title' => 'Bình luận công việc',
					'code' => $taskData['name'],
					'object_type' => 'tasks'
				], [$val['staffid']], get_staff_user_id());
			}
		}
		//
		echo json_encode([
			'success' => $comment_id ? true : false,
			'taskHtml' => $this->get_task_data($data['taskid'], true),
		]);
	}

	public function chonseNotifiZalo($taskId, $check)
	{
		$staffId = get_staff_user_id();
		$this->db->where('tbltask_followers.staffid', $staffId);
		$this->db->where('tbltask_followers.taskid', $taskId);
		// print_arrays($this->db->set(['notif_zalo' => $check])->get_compiled_update('tbltask_followers'));
		$this->db->update('tbltask_followers', ['notif_zalo' => $check]);
		$this->db->where('tbltask_assigned.staffid', $staffId);
		$this->db->where('tbltask_assigned.taskid', $taskId);
		$this->db->update('tbltask_assigned', ['notif_zalo' => $check]);
	}

	public function download_files($task_id, $comment_id = null)
	{
		$taskWhere = 'external IS NULL';
		if ($comment_id) {
			$taskWhere .= ' AND task_comment_id=' . $comment_id;
		}
		if (!has_permission('tasks', '', 'view')) {
			$taskWhere .= ' AND ' . get_tasks_where_string(false);
		}
		$files = $this->tasks_model->get_task_attachments($task_id, $taskWhere);
		if (count($files) == 0) {
			redirect($_SERVER['HTTP_REFERER']);
		}
		$path = get_upload_path_by_type('task') . $task_id;
		$this->load->library('zip');
		foreach ($files as $file) {
			$this->zip->read_file($path . '/' . $file['file_name']);
		}
		$this->zip->download('files.zip');
		$this->zip->clear_data();
	}

	/* Add new task follower / ajax */
	public function add_task_followers()
	{
		if (has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) {
			//send message zalo
			if ($this->input->post()) {
				$taskid = $this->input->post('taskid');
				$assignee = $this->input->post('follower');
				$rowTask = $this->site_model->rowTasks($taskid);
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json'
				]);
				// $rowZalo = $this->site_model->rowStaffById($assignee);
				// if (!empty($rowZalo['id_zalo'])) {
				// $str = htmlentities(strip_tags($rowTask['name']), ENT_QUOTES, 'UTF-8');
				// $str = html_entity_decode($rowTask['name']);
				// $dataZalo = [
				// 	'recipient' => [
				// 		"user_id" => $rowZalo['id_zalo']
				// 	],
				// 	'message' => [
				// 		'text' => '[Theo dõi] ' . $str
				// 	]
				// ];
				// curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				// $response = curl_exec($curl);
				// }
			}
			//
			$success = $this->tasks_model->add_task_followers($this->input->post());
			if (!empty($success)) {
				$follower = $this->input->post('follower');
				$taskid = $this->input->post('taskid');
				$staff_create = get_staff_full_name();
				$task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
				$task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);
				$rowName = '';
				//				$task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $rowTask['id_list_object']], '', 'row');
				//				if(!empty($task_rel_value_list)) {
				//					$rowName .= $task_rel_value_list->name;
				//				}
				$this->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
				$this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
				$departments_task_name = $this->db->get_where('tbltask_department', ['task_id' => $rowTask['id']])->row('list_name');
				if (!empty($departments_task_name)) {
					$rowName .= $departments_task_name;
				}
				if (!empty($task_rel_value['type'])) {
					if (!empty($rowName)) {
						$rowName .= ' và ';
					}
					$rowName .= _l('c_tasks_' . $task_rel_value['type']) . ' ' . $task_rel_value['name'];
				}
				if (!empty($rowName)) {
					$rowName = '<span style="color:black;font-weight:bold">Liên quan đến: </span>: ' . $rowName;
				}
				$dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa thêm bạn vào người theo dõi công vệc [' . $rowTask['name'] . '] ' . $rowName . '</b> vào lúc ' . _dhau(date('Y-m-d H:i:s')) . '
                    ';
				$notification_data = [
					'date' => date('Y-m-d H:i:s'),
					'description' => $dataHtml,
					'touserid' => $follower,
					'link' => '#taskid=' . $taskid,
					'type' => 6,
					'object_id' => $taskid,
					'object_type' => 'tasks',
					'onclick' => 'init_task_modal(' . $taskid . '); return false;',
				];
				if (!empty($notification_data)) {
					$this->db->insert('tblnotifications', $notification_data);
					pusher_trigger_notification($notification_data);
				}
				send_notification_app_c($rowTask['id'], [
					'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người theo dõi công việc vào lúc ' . _dt(date('Y-m-d H:i:s')),
					'title' => 'Theo dõi công việc công việc',
					'code' => $rowTask['name'],
					'object_type' => 'tasks'
				], [$follower], get_staff_user_id());
			}
			echo json_encode([
				'success' => $success,
				'taskHtml' => $this->get_task_data($this->input->post('taskid'), true),
			]);
		}
	}

	/* Add task assignees / ajax */
	public function add_task_assignees()
	{
		if (has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) {
			$data = $this->input->post();
			$task = $this->tasks_model->add_task_assignees($data);
			$taskHtml = $this->get_task_data($this->input->post('taskid'), true);
			//send message zalo
			if (!empty($task)) {
				$taskid = $data['taskid'];
				$assignee = $data['assignee'];
				$rowTask = $this->site_model->rowTasks($taskid);
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json'
				]);
				$rowZalo = $this->site_model->rowStaffById($assignee);
				if (!empty($rowZalo['id_zalo'])) {
					$str = htmlentities(strip_tags($rowTask['name']));
					$str = html_entity_decode($rowTask['name']);
					$dataZalo = [
						'recipient' => [
							"user_id" => $rowZalo['id_zalo']
						],
						'message' => [
							'text' => '[Phân công] ' . $str
						]
					];
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
					$response = curl_exec($curl);
				}
				$rowTask = $this->site_model->rowTasks($taskid);
				$staff_create = get_staff_full_name();
				$task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
				$task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);
				$rowName = '';
				//				$task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $rowTask['id_list_object']], '', 'row');
				//				if(!empty($task_rel_value_list)) {
				//					$rowName .= $task_rel_value_list->name;
				//				}
				$this->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
				$this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
				$departments_task_name = $this->db->get_where('tbltask_department', ['task_id' => $rowTask['id']])->row('list_name');
				if (!empty($departments_task_name)) {
					$rowName .= $departments_task_name;
				}
				if (!empty($task_rel_value['type'])) {
					if (!empty($rowName)) {
						$rowName .= ' và ';
					}
					$rowName .= _l('c_tasks_' . $task_rel_value['type']) . ' ' . $task_rel_value['name'];
				}
				if (!empty($rowName)) {
					$rowName = '<span style="color:black;font-weight:bold">Liên quan đến: </span>: ' . $rowName;
				}
				$dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa phân công công việc ' . $rowName . ' vào ngày ' . _dhau($rowTask['startdate']) . ' Vui lòng theo dõi và tiến hành cập nhật công việc !
                    ';
				$notification_data = [
					'date' => date('Y-m-d H:i:s'),
					'description' => $dataHtml,
					'touserid' => $data['assignee'],
					'link' => '#taskid=' . $taskid,
					'type' => 6,
					'object_id' => $taskid,
					'object_type' => 'tasks',
					'onclick' => 'init_task_modal(' . $taskid . '); return false;',
				];
				if (!empty($notification_data)) {
					$this->db->insert('tblnotifications', $notification_data);
					pusher_trigger_notification($notification_data);
				}
				notificationTaskAssigned($taskid, get_staff_user_id(), $data['assignee']);
			}
			//
			echo json_encode([
				'success' => $task,
				'taskHtml' => $taskHtml,
			]);
		}
	}

	public function edit_comment()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['content'] = $this->input->post('content', false);
			if ($this->input->post('no_editor')) {
				$data['content'] = nl2br(clear_textarea_breaks($this->input->post('content')));
			}
			$success = $this->tasks_model->edit_comment($data);
			$message = '';
			if ($success) {
				$message = _l('task_comment_updated');
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
				'taskHtml' => $this->get_task_data($data['task_id'], true),
			]);
		}
	}

	/* Remove task comment / ajax */
	public function remove_comment($id)
	{
		echo json_encode([
			'success' => $this->tasks_model->remove_comment($id),
		]);
	}

	/* Remove assignee / ajax */
	public function remove_assignee($id, $taskid)
	{
		if (has_permission('tasks', '', 'edit') && has_permission('tasks', '', 'create')) {
			$task_assigned = $this->db->get_where('tbltask_assigned', ['id' => $id])->row();
			$success = $this->tasks_model->remove_assignee($id, $taskid);
			if (!empty($success)) {
				$rowTask = $this->site_model->rowTasks($taskid);
				$staff_create = get_staff_full_name();
				$task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
				$task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);
				$rowName = '';
				//				$task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $rowTask['id_list_object']], '', 'row');
				//				if(!empty($task_rel_value_list)) {
				//					$rowName .= $task_rel_value_list->name;
				//				}
				$this->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
				$this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
				$departments_task_name = $this->db->get_where('tbltask_department', ['task_id' => $rowTask['id']])->row('list_name');
				if (!empty($departments_task_name)) {
					$rowName .= $departments_task_name;
				}
				if (!empty($task_rel_value['type'])) {
					if (!empty($rowName)) {
						$rowName .= ' và ';
					}
					$rowName .= _l('c_tasks_' . $task_rel_value['type']) . ' ' . $task_rel_value['name'];
				}
				if (!empty($rowName)) {
					$rowName = '<span style="color:black;font-weight:bold">Liên quan đến: </span>: ' . $rowName;
				}
				$dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa xóa bạn khỏi phân công công vệc [' . $rowTask['name'] . '] ' . $rowName . '</b> vào lúc ' . _dhau(date('Y-m-d H:i:s')) . '
                    ';
				$notification_data = [
					'date' => date('Y-m-d H:i:s'),
					'description' => $dataHtml,
					'touserid' => $task_assigned->staffid,
					'link' => '#taskid=' . $taskid,
					'type' => 6,
					'object_id' => $taskid,
					'object_type' => 'tasks',
					'onclick' => 'init_task_modal(' . $taskid . '); return false;',
				];
				if (!empty($notification_data)) {
					$this->db->insert('tblnotifications', $notification_data);
					pusher_trigger_notification($notification_data);
				}
			}
			$message = '';
			if ($success) {
				$message = _l('task_assignee_removed');
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
				'taskHtml' => $this->get_task_data($taskid, true),
			]);
		}
	}

	/* Remove task follower / ajax */
	public function remove_follower($id, $taskid)
	{
		if (has_permission('tasks', '', 'edit') && has_permission('tasks', '', 'create')) {
			$task_followers = $this->db->get_where('tbltask_followers', ['id' => $id])->row();
			$success = $this->tasks_model->remove_follower($id, $taskid);
			$message = '';
			if ($success) {
				$message = _l('task_follower_removed');
				$rowTask = $this->site_model->rowTasks($taskid);
				$staff_create = get_staff_full_name();
				$task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
				$task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);
				$rowName = '';
				//				$task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $rowTask['id_list_object']], '', 'row');
				//				if(!empty($task_rel_value_list)) {
				//					$rowName .= $task_rel_value_list->name;
				//				}
				$this->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
				$this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
				$departments_task_name = $this->db->get_where('tbltask_department', ['task_id' => $rowTask['id']])->row('list_name');
				if (!empty($departments_task_name)) {
					$rowName .= $departments_task_name;
				}
				if (!empty($task_rel_value['type'])) {
					if (!empty($rowName)) {
						$rowName .= ' và ';
					}
					$rowName .= _l('c_tasks_' . $task_rel_value['type']) . ' ' . $task_rel_value['name'];
				}
				if (!empty($rowName)) {
					$rowName = '<span style="color:black;font-weight:bold">Liên quan đến: </span>: ' . $rowName;
				}
				$dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa xóa bạn khỏi người theo dõi công vệc [' . $rowTask['name'] . '] ' . $rowName . '</b> vào lúc ' . _dhau(date('Y-m-d H:i:s')) . '
                    ';
				$notification_data = [
					'date' => date('Y-m-d H:i:s'),
					'description' => $dataHtml,
					'touserid' => $task_followers->staffid,
					'link' => '#taskid=' . $taskid,
					'type' => 6,
					'object_id' => $taskid,
					'object_type' => 'tasks',
					'onclick' => 'init_task_modal(' . $taskid . '); return false;',
				];
				if (!empty($notification_data)) {
					$this->db->insert('tblnotifications', $notification_data);
					pusher_trigger_notification($notification_data);
				}
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
				'taskHtml' => $this->get_task_data($taskid, true),
			]);
		}
	}

	/* Unmark task as complete / ajax*/
	public function unmark_complete($id)
	{
		if (
			$this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
			|| $this->tasks_model->is_task_creator(get_staff_user_id(), $id)
			|| has_permission('tasks', '', 'edit')
		) {
			$success = $this->tasks_model->unmark_complete($id);
			// Don't do this query if the action is not performed via task single
			$taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';
			$message = '';
			if ($success) {
				$message = _l('task_unmarked_as_complete');
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
				'taskHtml' => $taskHtml,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => '',
				'taskHtml' => '',
			]);
		}
	}

	public function mark_as($status, $id)
	{
		if (
			$this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
			|| $this->tasks_model->is_task_creator(get_staff_user_id(), $id)
			|| has_permission('tasks', '', 'edit')
		) {
			if ($status == 5) {
				$this->db->where('taskid', $id);
				$this->db->where('finished', 0);
				$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
				if (!empty($ktChecklist)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'danger',
						'message' => 'Vui lòng hoàn thành hết các bước quy trình mới được hoàn thành',
						'taskHtml' => '',
					]);
					die();
				}
			}
			$success = $this->tasks_model->mark_as($status, $id);
			// Don't do this query if the action is not performed via task single
			$taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';
			$message = '';
			if ($success) {
				$message = _l('task_marked_as_success', format_task_status($status, true, true));
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
				'taskHtml' => $taskHtml,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => '',
				'taskHtml' => '',
			]);
		}
	}

	public function change_priority($priority_id, $id)
	{
		if (has_permission('tasks', '', 'edit')) {
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'tasks', ['priority' => $priority_id]);
			$success = $this->db->affected_rows() > 0 ? true : false;
			// Don't do this query if the action is not performed via task single
			$taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';
			echo json_encode([
				'success' => $success,
				'taskHtml' => $taskHtml,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'taskHtml' => $taskHtml,
			]);
		}
	}

	public function change_milestone($milestone_id, $id)
	{
		if (has_permission('tasks', '', 'edit')) {
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'tasks', ['milestone' => $milestone_id]);
			$success = $this->db->affected_rows() > 0 ? true : false;
			// Don't do this query if the action is not performed via task single
			$taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';
			echo json_encode([
				'success' => $success,
				'taskHtml' => $taskHtml,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'taskHtml' => $taskHtml,
			]);
		}
	}

	public function task_single_inline_update($task_id)
	{
		if (has_permission('tasks', '', 'edit')) {
			$post_data = $this->input->post();
			foreach ($post_data as $key => $val) {
				$this->db->where('id', $task_id);
				$this->db->update(db_prefix() . 'tasks', [$key => to_sql_date($val, true)]);
			}
		}
	}

	/* Delete task from database */
	public function delete_task($id)
	{
		if (!has_permission('tasks', '', 'delete')) {
			access_denied('tasks');
		}
		$dtData = get_table_where('tbltasks', ['id' => $id], '', 'row_array');
		$success = $this->tasks_model->delete_task($id);
		$message = _l('problem_deleting', _l('task_lowercase'));
		if ($success) {
			insertActivityLog([
				'type_parent_obj' => 'tasks',
				'table_obj' => 'tbltasks',
				'id_obj' => $id,
				'name_obj' => $dtData['name'],
				'content' => lang('Xóa công việc') . ' [' . $dtData['name'] . ']',
				'actions' => 'delete'
			]);
			$message = _l('deleted', _l('task'));
			set_alert('success', $message);
		} else {
			set_alert('warning', $message);
		}
		if (strpos($_SERVER['HTTP_REFERER'], 'tasks/index') !== false || strpos($_SERVER['HTTP_REFERER'], 'tasks/view') !== false) {
			redirect(admin_url('tasks'));
		} elseif (preg_match("/projects\/view\/[1-9]+/", $_SERVER['HTTP_REFERER'])) {
			$project_url = explode('?', $_SERVER['HTTP_REFERER']);
			redirect($project_url[0] . '?group=project_tasks');
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	/**
	 * Remove task attachment
	 * @param mixed $id attachment it
	 * @return json
	 * @since  Version 1.0.1
	 */
	public function remove_task_attachment($id)
	{
		if ($this->input->is_ajax_request()) {
			echo json_encode($this->tasks_model->remove_task_attachment($id));
		}
	}

	/**
	 * Upload task attachment
	 * @since  Version 1.0.1
	 */
	public function upload_file()
	{
		if ($this->input->post()) {
			$taskid = $this->input->post('taskid');
			$files = handle_task_attachments_array($taskid, 'file');
			$success = false;
			if ($files) {
				$i = 0;
				$len = count($files);
				foreach ($files as $file) {
					$success = $this->tasks_model->add_attachment_to_database($taskid, [$file], false, ($i == $len - 1 ? true : false));
					$i++;
				}
			}
			echo json_encode([
				'success' => $success,
				'taskHtml' => $this->get_task_data($taskid, true),
			]);
		}
	}

	public function timer_tracking()
	{
		$task_id = $this->input->post('task_id');
		$adminStop = $this->input->get('admin_stop') && is_admin() ? true : false;
		if ($adminStop) {
			$this->session->set_flashdata('task_single_timesheets_open', true);
		}
		echo json_encode([
			'success' => $this->tasks_model->timer_tracking(
				$task_id,
				$this->input->post('timer_id'),
				nl2br($this->input->post('note')),
				$adminStop
			),
			'taskHtml' => $this->input->get('single_task') === 'true' ? $this->get_task_data($task_id, true) : '',
			'timers' => $this->get_staff_started_timers(true),
		]);
	}

	public function delete_user_unfinished_timesheet($id)
	{
		$this->db->where('id', $id);
		$timesheet = $this->db->get(db_prefix() . 'taskstimers')->row();
		if ($timesheet && $timesheet->end_time == null && $timesheet->staff_id == get_staff_user_id()) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'taskstimers');
		}
		echo json_encode(['timers' => $this->get_staff_started_timers(true)]);
	}

	public function delete_timesheet($id)
	{
		if (has_permission('tasks', '', 'delete') || has_permission('projects', '', 'delete') || total_rows(db_prefix() . 'taskstimers', ['staff_id' => get_staff_user_id(), 'id' => $id]) > 0) {
			$alert_type = 'warning';
			$success = $this->tasks_model->delete_timesheet($id);
			if ($success) {
				$this->session->set_flashdata('task_single_timesheets_open', true);
				$message = _l('deleted', _l('project_timesheet'));
				set_alert('success', $message);
			}
			if (!$this->input->is_ajax_request()) {
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	public function log_time()
	{
		$success = $this->tasks_model->timesheet($this->input->post());
		if ($success === true) {
			$this->session->set_flashdata('task_single_timesheets_open', true);
			$message = _l('added_successfully', _l('project_timesheet'));
		} elseif (is_array($success) && isset($success['end_time_smaller'])) {
			$message = _l('failed_to_add_project_timesheet_end_time_smaller');
		} else {
			$message = _l('project_timesheet_not_updated');
		}
		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
		die;
	}

	public function update_tags()
	{
		if (has_permission('tasks', '', 'create') || has_permission('tasks', '', 'edit')) {
			handle_tags_save($this->input->post('tags'), $this->input->post('task_id'), 'task');
		}
	}

	public function bulk_action()
	{
		hooks()->do_action('before_do_bulk_action_for_tasks');
		$total_deleted = 0;
		if ($this->input->post()) {
			$status = $this->input->post('status');
			$ids = $this->input->post('ids');
			$tags = $this->input->post('tags');
			$assignees = $this->input->post('assignees');
			$milestone = $this->input->post('milestone');
			$priority = $this->input->post('priority');
			$is_admin = is_admin();
			if (is_array($ids)) {
				foreach ($ids as $id) {
					if ($this->input->post('mass_delete')) {
						if (has_permission('tasks', '', 'delete')) {
							if ($this->tasks_model->delete_task($id)) {
								$total_deleted++;
							}
						}
					} else {
						if ($status) {
							if (
								$this->tasks_model->is_task_creator(get_staff_user_id(), $id)
								|| $is_admin
								|| $this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
							) {
								$this->tasks_model->mark_as($status, $id);
							}
						}
						if ($priority || $milestone) {
							$update = [];
							if ($priority) {
								$update['priority'] = $priority;
							}
							if ($milestone) {
								$update['milestone'] = $milestone;
							}
							$this->db->where('id', $id);
							$this->db->update(db_prefix() . 'tasks', $update);
						}
						if ($tags) {
							handle_tags_save($tags, $id, 'task');
						}
						if ($assignees) {
							$notifiedUsers = [];
							foreach ($assignees as $user_id) {
								if (!$this->tasks_model->is_task_assignee($user_id, $id)) {
									$this->db->select('rel_type,rel_id');
									$this->db->where('id', $id);
									$task = $this->db->get(db_prefix() . 'tasks')->row();
									if ($task->rel_type == 'project') {
										// User is we are trying to assign the task is not project member
										if (total_rows(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]) == 0) {
											$this->db->insert(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]);
										}
									}
									$this->db->insert(db_prefix() . 'task_assigned', [
										'staffid' => $user_id,
										'taskid' => $id,
										'assigned_from' => get_staff_user_id(),
									]);
									if ($user_id != get_staff_user_id()) {
										$notification_data = [
											'description' => 'not_task_assigned_to_you',
											'touserid' => $user_id,
											'link' => '#taskid=' . $id,
										];
										$notification_data['additional_data'] = serialize([
											get_task_subject_by_id($id),
										]);
										if (add_notification($notification_data)) {
											array_push($notifiedUsers, $user_id);
										}
									}
								}
							}
							pusher_trigger_notification($notifiedUsers);
						}
					}
				}
			}
			if ($this->input->post('mass_delete')) {
				set_alert('success', _l('total_tasks_deleted', $total_deleted));
			}
		}
	}

	public function calendar_pod()
	{
		$this->perViewCalendarPod = has_permission('tasks', '', 'view');
		$this->perViewOwnCalendarPod = has_permission('tasks', '', 'view_own');
		if (!$this->perViewCalendarPod && !$this->perViewOwnCalendarPod) {
			access_denied('Calendar Pod');
		}
		$data['title'] = lang('Lịch công việc');
		add_calendar_assets();
		$this->db->select('staffid, CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as fullname');
		$data['staff'] = $this->db->get_where('tblstaff')->result_array();
		$data['departments'] = $this->db->get_where('tbldepartments', ['type' => 0])->result_array();
		$data['room'] = $this->db->get_where('tbl_room')->result_array();
		$this->load->view('admin/tasks/calendar_pod', $data);
	}

	public function getCalendarPod()
	{
		$this->is_admin = is_admin();
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		$departments_task = $this->input->post('departments_task');
		$room_task = $this->input->post('room_task');
		$staff_task = $this->input->post('staff_task');
		$arrIDStaff = employee_manage_staff();
		$data = [];
		$staff_id = get_staff_user_id();
		if (has_permission('tasks', '', 'view_own') && !is_admin()) {
			$this->db->where('EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid = ' . get_staff_user_id() . ')');
		}
		if (!empty($staff_task)) {
			$this->db->where('EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN (' . implode(',', $staff_task) . ') LIMIT 1)');
		}
		if (!empty($departments_task)) {
			$this->db->where('EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . implode(',', $departments_task) . ')	 LIMIT 1)');
		}
		if (!empty($room_task)) {
			$this->db->where('EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . implode(',', $room_task) . ')	 LIMIT 1)');
		}
		$this->db->select('tbltasks.*', false);
		$this->db->from('tbltasks');
		$this->db->where('DATE_FORMAT(startdate, "%Y-%m-%d") >= "' . ($start) . '"');
		$this->db->where('DATE_FORMAT(startdate, "%Y-%m-%d") <= "' . ($end) . '"');
		$events_task = $this->db->get()->result_array();
		$data_status = [];
		foreach ($events_task as $key => $value) {
			$_data = '';
			$divFinised = '';
			if ($value['status'] == 5) {
				$divFinised = '<div class="panel-finished" style="margin: auto;width:unset">
                    <div class="">' . lang('tnh_finished_production') . '</div>
                </div>';
			}
			$status = get_task_status_by_id($value['status']);
			$htmlStatus = '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" >' . $status['name'] . '</span>';
			$dataStatus = $htmlStatus;
			$style = '';
			if (!empty($divFinised)) {
				$style = 'text-decoration: line-through;color:#0e5daa';
			} else {
				$style = 'color:#0e5daa';
			}
			$this->db->where('tbltask_assigned.taskid', $value['id']);
			$task_assigned = $this->db->get('tbltask_assigned')->result_array();
			$listAssigned = '';
			if (!empty($task_assigned)) {
				foreach ($task_assigned as $k => $v) {
					$listAssigned .= staff_profile_image($v['staffid'], array('staff-profile-image-small mright5'), 'small', array(
						'data-toggle' => 'tooltip',
						'data-title' => get_staff_full_name($v['staffid'])
					));
				}
			}
			if (!empty($listAssigned)) {
				$listAssigned = '<hr class="mtop5 mbot5"/>' . $listAssigned;
			}
			$row_QL = '';
			$task_rel_data = get_relation_data($value['rel_type'], $value['rel_id']);
			$task_rel_value = get_relation_values($task_rel_data, $value['rel_type']);
			if (!empty($task_rel_value['id'])) {
				$row_QL .= ' <a target="_blank" href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
			}
			//
			//			$task_rel_value = get_table_where('tbldepartments_tasks', ['id' => $value['id_list_object']], '', 'row');
			//			$this->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
			//			$this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
			//			$departments_task_name = $this->db->get_where('tbltask_department', ['task_id' => $value['id']])->row('list_name');
			$this->db->select('GROUP_CONCAT(tbl_room.name) as list_name');
			$this->db->join('tbl_room', 'tbl_room.id = tbltask_department.department_id');
			$room_task_name = $this->db->get_where('tbltask_department', ['task_id' => $value['id']])->row('list_name');
			if (!empty($room_task_name)) {
				if (!empty($row_QL)) {
					$row_QL .= ',<br/><div class="col-md-1"></div>';
				}
				$row_QL .= $room_task_name;
			}
			$content_description = strip_tags(($value['description']));
			if (mb_strlen($content_description, 'UTF-8') >= 150) {
				$content_description = '<span class="show_more pointer mbot5"><t>' . mb_substr($content_description, 0, 150, 'UTF-8') . '... </t></span>';
			}
			$name = '';
			$name .= '<div style="color: black;"><i><b style="text-transform: capitalize;">Ngày bắt đầu:</b></i> ' . _d($value['startdate']) . '</div>';
			if (!empty($row_QL)) {
				$name .= '<div style="color: black;"><b style="text-transform: capitalize;">Liên quan đến: </b>' . $row_QL . '</div>';
			}
			$name .= '<div style="color: black;"><b style="text-transform: capitalize;">Mô tả:</b> ' . $content_description . '</div>';
			$content = '<div style="margin-top:10px;' . $style . '"  onclick="init_task_modal(' . $value['id'] . '); return false;">
                            <div class="bold uppercase" style="color:white;background:#1b4d99;padding:3px;margin-bottom:3px;margin-left:-8px;margin-right:-8px;margin-top:-8px"><a style="color: white;">' . $value['name'] . '</a></div>
                            ' . $name . '
						</div>
                        ' . '<div class="mbot10"><div style="padding: 5px;' . $style . '">' . $dataStatus . '</div></div>' . $listAssigned . '
                        ' . $_data;
			$event_order['_tooltip'] = '';
			$event_order['title'] = $content;
			$event_order['start'] = $value['startdate'];
			$event_order['end'] = $value['startdate'];
			$event_order['public'] = 1;
			$event_order['onclick'] = true;
			$event_order['eventid'] = $value['id'];
			$time = date("H:i", strtotime($value['startdate']));
			$event_order['time'] = $time;
			$getDate = date('Y-m-d');
			$event_order['color'] = '#fff';
			array_push($data, $event_order);
		}
		echo json_encode(hooks()->apply_filters('calendar_data', $data, [
			'start' => $start,
			'end' => $end,
		]));
	}

	public function checkbox_action_list_new()
	{
		$listid = $this->input->post('listid');
		$value = $this->input->post('status');
		if ($value == 0) {
			$this->db->where('id', $listid);
			$task_checklist_items = $this->db->get('tbltask_checklist_items')->row();
			if (!empty($task_checklist_items->stages)) {
				$delivery_records_object = get_table_where('tbl_delivery_records', array('id_create' => $task_checklist_items->taskid, 'type_create' => 'tasks'), '', 'row_array');
				if (!empty($delivery_records_object)) {
					$data['result'] = 0;
					$data['message'] = lang('Đã có biên bản bàn giao ' . $delivery_records_object['reference_no'] . ', Không thể bỏ duyệt');
					echo json_encode($data);
					die;
				}
			}

			$this->db->where('taskid', $task_checklist_items->taskid);
			$this->db->where('id >', $listid);
			$this->db->order_by('id', 'asc');
			$check_status_bef = $this->db->get('tbltask_checklist_items')->row_array();
			if (!empty($check_status_bef)) {
				if ($check_status_bef['finished'] != 0) {
					$data['result'] = 0;
					$data['alert_type'] = 'danger';
					$data['message'] = lang('Bước ' . $check_status_bef['description'] . ' chưa bỏ duyệt duyệt, Không thể bỏ duyệt bước này');
					echo json_encode($data);
					die;
				}
			}
		}

		$this->db->where('id', $listid);
		$success = $this->db->update(db_prefix() . 'task_checklist_items', [
			'finished' => $value,
		]);
		if ($this->db->affected_rows() > 0) {
			if ($value == 1) {
				$this->db->where('id', $listid);
				$this->db->update(db_prefix() . 'task_checklist_items', [
					'finished_from' => get_staff_user_id(),
					'date_finished' => date('Y-m-d H:i:s'),
				]);
				$this->db->where('id', $listid);
				$taskid = $this->db->get('tbltask_checklist_items')->row('taskid');
				if (!empty($taskid)) {
					$this->db->where('taskid', $taskid);
					$this->db->where('finished', 0);
					$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
					if (empty($ktChecklist)) {
						$this->tasks_model->mark_as(5, $taskid);
					}
					hooks()->do_action('task_checklist_item_finished', $listid);
				}
			} else {
				$this->db->where('id', $listid);
				$taskid = $this->db->get('tbltask_checklist_items')->row('taskid');
				$this->db->where('id', $listid);
				$this->db->update(db_prefix() . 'task_checklist_items', [
					'finished_from' => 0,
				]);
				$this->db->where('tasks', $taskid);
				$this->db->where('id_tasks_process', $listid);
				$ktChecklist = $this->db->delete('tbl_tasks_inspection_criteria_process');
			}
		}
		// if(!empty($success)) {
		// 	$checkList = $this->db->get_where('tbltask_checklist_items', ['id' => $listid])->row();
		// 	$checkList->name_finished_from = !empty($checkList->finished_from) ? get_staff_full_name($checkList->finished_from) : '';
		// 	echo json_encode(['success' => true, 'data' => $checkList]);die();
		// }
		if ($success) {
			$data['result'] = 1;
			$data['message'] = lang('success');
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function checkbox_action_list($listid, $value)
	{
		$this->db->where('id', $listid);
		$success = $this->db->update(db_prefix() . 'task_checklist_items', [
			'finished' => $value,
		]);
		if ($this->db->affected_rows() > 0) {
			if ($value == 1) {
				$this->db->where('id', $listid);
				$this->db->update(db_prefix() . 'task_checklist_items', [
					'finished_from' => get_staff_user_id(),
					'date_finished' => date('Y-m-d H:i:s'),
				]);
				$this->db->where('id', $listid);
				$taskid = $this->db->get('tbltask_checklist_items')->row('taskid');
				if (!empty($taskid)) {
					$this->db->where('taskid', $taskid);
					$this->db->where('finished', 0);
					$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
					if (empty($ktChecklist)) {
						$this->tasks_model->mark_as(5, $taskid);
					}
					hooks()->do_action('task_checklist_item_finished', $listid);
				}
			} else {
				$this->db->where('id', $listid);
				$this->db->update(db_prefix() . 'task_checklist_items', [
					'finished_from' => 0,
				]);
			}
		}
		if (!empty($success)) {
			$checkList = $this->db->get_where('tbltask_checklist_items', ['id' => $listid])->row();
			$checkList->name_finished_from = !empty($checkList->finished_from) ? get_staff_full_name($checkList->finished_from) : '';
			echo json_encode(['success' => true, 'data' => $checkList]);
			die();
		}
		echo json_encode(['success' => true]);
		die();
	}

	public function hand_over($id = '')
	{
		$data['title'] = 'Bàn giao';
		$data['id'] = $id;
		$process = get_table_where('tbltask_checklist_items', array('id' => $id), '', 'row_array');
		$data['stages'] = $process['stages'];
		$this->load->view('admin/tasks/hand_over', $data);
	}

	public function add_hand_over()
	{
		$this->load->model('hand_over_model');
		$_data = $this->input->post();
		$id = $this->input->post('id');
		$task_checklist_items_id = $id;
		$status = 1;
		$type = 1;
		$this->db->where('id', $task_checklist_items_id);
		$task_checklist_items = $this->db->get('tbltask_checklist_items')->row();
		if ($status == $task_checklist_items->finished) {
			$data['result'] = 0;
			$data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
			echo json_encode($data);
			die;
		}
		if ($status == 1) {
			$this->db->where('id', $id);
			$tests = $this->db->update('tbltask_checklist_items', [
				'finished_from' => get_staff_user_id(),
				'finished' => 1,
				'date_finished' => date('Y-m-d H:i:s'),
			]);
			$this->db->where('id', $id);
			$taskid = $this->db->get('tbltask_checklist_items')->row('taskid');
			if (!empty($taskid)) {
				$this->db->where('taskid', $taskid);
				$this->db->where('finished', 0);
				$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
				if (empty($ktChecklist)) {
					$this->tasks_model->mark_as(5, $taskid);
				}
				hooks()->do_action('task_checklist_item_finished', $id);
			}
		}
		$process = get_table_where('tbltask_checklist_items', array('id' => $id), '', 'row_array');
		$id_create = $process['taskid'];
		$branch_id = 0;
		$id_delivery_records = '';
		$hand_over_task_id = $this->input->post('hand_over_task_id');
		$category_hand = $this->input->post('category_hand');
		$task_hand_over_qualified = $this->input->post('task_hand_over_qualified');
		//cong
		if (!empty($category_hand) && !empty($hand_over_task_id)) {
			if (empty($id_delivery_records)) {
				$delivery_records = [
					'reference_no' => get_option('prefix_delivery_records') . sprintf('%06d', ch_getMaxID('id', 'tbl_delivery_records') + 1),
					'date' => date('Y-m-d H:i:s'),
					'staff' => get_staff_user_id(),
					'type_object' => 'tasks',
					'category_hand' => $category_hand,
					'type_create' => 'tasks',
					'id_create' => $id_create,
					'id_branch' => $branch_id,
				];
				$delivery_records['created_by'] = get_staff_user_id();
				$delivery_records['date_created'] = date('Y-m-d H:i:s');
				$id_delivery_records = $this->hand_over_model->insertDeliveryRecords($delivery_records);
				if (!empty($id_delivery_records)) {
					$this->db->insert('tbl_delivery_records_object', [
						'id_delivery_records' => $id_delivery_records,
						'id_object' => $id_create
					]);
				}
			}
		}
		$qualified = true;
		$count_task_hand_over = 0;
		$id_delivery_records_detail = 0;
		if (!empty($id_delivery_records)) {
			if (!empty($hand_over_task_id)) {
				foreach ($hand_over_task_id as $key => $value) {
					if ($task_hand_over_qualified[$key] == 2) {
						$qualified = false;
						$count_task_hand_over++;
					}
					$arrayInsert = [
						'delivery_records_id' => $id_delivery_records,
						'hand_over_task_id' => $value,
						'task_hand_over_qualified' => !empty($task_hand_over_qualified[$key]) ? $task_hand_over_qualified[$key] : 0,
					];
					$this->db->where('delivery_records_id', $id_delivery_records);
					$this->db->where('hand_over_task_id', $value);
					$delivery_records_task = $this->db->get('tbl_delivery_records_task')->row();
					if (!empty($delivery_records_task)) {
						$this->db->where('id', $delivery_records_task->id);
						$this->db->update('tbl_delivery_records_task', $arrayInsert);
						if ($task_hand_over_qualified[$key] == 2) {
							$id_delivery_records_detail = $delivery_records_task->id;
						}
					} else {
						if (!empty($arrayInsert['task_hand_over_qualified'])) {
							$arrayInsert['staff_id'] = get_staff_user_id();
							$arrayInsert['date_check'] = date('Y-m-d H:i:s');
						}
						$this->db->insert('tbl_delivery_records_task', $arrayInsert);
						if ($task_hand_over_qualified[$key] == 2) {
							$id_delivery_records_detail = $this->db->insert_id();
						}
					}
				}
				$data['id_delivery_records'] = $id_delivery_records;
			}
		}
		if (empty($qualified)) {
			$total_quantity_errors = 0;
			$data['success'] = true;
			$data['alert_type'] = 'warning';
			$data['id_delivery_records'] = $id_delivery_records;
			$data['href'] = admin_url('production_report/detail?id_delivery_records=' . $id_delivery_records . '&quantity_err=' . $total_quantity_errors . ($count_task_hand_over == 1 ? ('&id_delivery_records_detail=' . $id_delivery_records_detail) : ''));
			$data['message'] = lang('Tạo biên bản bàn giao thành công');
			echo json_encode($data);
			die;
		} else {
			$total_quantity_errors = 0;
			$data['result'] = true;
			$data['alert_type'] = 'warning';
			$data['message'] = lang('Tạo biên bản bàn giao thành công');
			echo json_encode($data);
			die;
		}
		//end cong
	}

	public function export_excel()
	{
		if ($this->input->post('export_excel')) {
			ini_set('memory_limit', '3500M');
			include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
			$this->load->library('PHPExcel');
			$list_id = $this->input->post('list_id');
			if (!empty($list_id)) {

				$this->db->select('tbltasks.*');
				$this->db->select('(
										SELECT
											GROUP_CONCAT(tbl_room.name SEPARATOR ",\n")
										FROM tbltask_department
										JOIN tbl_room ON tbl_room.id = tbltask_department.department_id
										WHERE tbltask_department.task_id = tbltasks.id
									) as room_tasks');
				$this->db->select([
					'tblcategory_tasks.code as code_category_tasks',
					'tblcategory_tasks.content as name_category_tasks',
					(get_sql_select_task_asignees_full_names(true) . ' as assignees'),
					'(
							SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
							FROM tbltaskstimers
							WHERE tbltaskstimers.task_id = tbltasks.id
						) as _minute',
					'(SELECT tblcategory_tasks.time FROM tblcategory_tasks WHERE tblcategory_tasks.id = tbltasks.category_tasks LIMIT 1) as minute_limit',
					'(
							SELECT GROUP_CONCAT(tblproduction_report.name_report SEPARATOR ",\n")
							FROM tblproduction_report
							WHERE tblproduction_report.id_tasks = tbltasks.id
						) as ProductionReport',
					'tbl_category_recommended.type_kpi as type_kpi',
					'tbl_category_recommended.id as id_category_recommended',
					'tbl_category_recommended.name_table as name_table',
					'tbl_category_recommended.name as name_category_recommended',
					'tblinternal_proposal.suggest_id as suggest_id',

					'new_tasks.id as id_category_recommended_task',
					'new_tasks.type_kpi as type_kpi_task',
					'new_tasks.name_table as name_table_task',
					'new_tasks.name as name_category_recommended_task',
					'tbltasks.id as id_task',
					'tbltasks.suggest_id as suggest_id_task',
					'tbl_productions_orders.reference_no as reference_no_po',
					'tbl_productions_orders.id as po_id',
					'tbl_stages.name as name_stage',
				]);
				$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbltasks.category_tasks', 'left');
				$this->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"', 'left');
				$this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');
				$this->db->join('tbl_category_recommended new_tasks', 'new_tasks.id = tbltasks.category_recommended_id', 'left');
				$this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbltasks.po_id', 'left');
				$this->db->join('tbl_stages', 'tbl_stages.id = tbltasks.stage_id', 'left');
				$this->db->where_in('tbltasks.id', $list_id);
				$this->db->order_by('tbltasks.order_by desc');
				$list_tasks = $this->db->get('tbltasks')->result_array();
			}


			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
				->setWidth(20);
			$objPHPExcel->getDefaultStyle()->applyFromArray([
				'font' => array(
					'name' => 'Times New Roman'
				),
			]);

			insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

			$objPHPExcel->getActiveSheet()->setCellValue(
				'A5',
				('DANH SÁCH CÔNG VIỆC')
			)->getStyle("A5")->applyFromArray([
				'font' => array(
					'bold' => true,
					'size' => 16,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			]);
			$cloumns_excel = cloumns_excel();

			$objPHPExcel->getActiveSheet()->mergeCells('A5:L5');
			$sttRow = 2 + 4;
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'ID')->getStyle("A$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'PHÒNG BAN ĐƯỢC PHÂN CÔNG')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'MÃ CÔNG VIỆC')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'TÊN CỦA MÃ CÔNG VIỆC')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'LOẠI PHIẾU')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'MÃ PHIẾU')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'NGÀY BẮT ĐẦU')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'HẠN CHÓT')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'NGƯỜI GIAO VIỆC')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'NGƯỜI ĐƯỢC PHÂN CÔNG')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'TRẠNG THÁI')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'KẾT QUẢ')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'MỨC ĐỘ ƯU TIÊN')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'BÁO CÁO SỰ CỐ')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'HOÀN THÀNH QUY TRÌNH')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
			$demcheck = 0;

			foreach (range('A', 'O') as $columnID) {
				$demcheck++;
				// $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->mergeCells($columnID . ($sttRow) . ":" . $columnID . ($sttRow + 1));
			}
			$demcheck--;
			//				$objPHPExcel->getActiveSheet()->setCellValue('AG'.$sttRow.'', 'Duyệt Kho')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("A$sttRow:O$sttRow")->applyFromArray([
				'font' => array(
					'size' => 12,
					'name' => 'Times New Roman'
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FFFF00'),
				),
			])->getAlignment()->setWrapText(true);
			$sttRow = 2 + 5;
			$excelRowNum_quy = $sttRow;
			$count_quytrinh = 0;

			$objPHPExcel->getActiveSheet()->getStyle("A$sttRow:O$sttRow")->applyFromArray([
				'font' => array(
					'size' => 12,
					'name' => 'Times New Roman'
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FFFF00'),
				),
			])->getAlignment()->setWrapText(true);
			$styleHeader = [
				'font' => array(
					'size' => 12,
					'name' => 'Times New Roman'
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FFFF00'),
				),
			];
			$rowBegin = $sttRow;
			if (!empty($list_tasks)) {
				foreach ($list_tasks as $key => $value) {
					$rowBegin++;
					$staff_create = !empty($value['addedfrom']) ? get_staff_full_name($value['addedfrom']) : '';
					$resultTime = 'Chưa tính giờ';
					if (empty($value['category_tasks'])) {
						$resultTime = 'Chưa chọn mã công việc';
					} else if (!empty($value['_minute'])) {
						if ($value['_minute'] > $value['minute_limit']) {
							$resultTime = 'Chưa đạt';
						} elseif ($value['_minute'] == $value['minute_limit']) {
							$resultTime = 'Đạt';
						} else {
							$resultTime = 'Vượt KPI';
						}
					} else {
						$resultTime = 'Chưa tính giờ';
					}
					if (!empty($value['_minute'])) {
						$resultTime .= 'Tổng TG thực hiện 	' . number_format_data($value['_minute']) . ' (Phút)';
					}

					$priority = task_priority($value['priority']);
					if (!empty($value['assignees'])) {
						$value['assignees'] = explode(',', $value['assignees']);
						$value['assignees'] = implode(",\n", $value['assignees']);
					}
					$object_type = '';
					$object_id = '';
					if (!empty($value['reference_no_po'])) {
						$object_type = 'Lệnh sản xuất';
					} else {
						$object_type = (!empty($value['id_category_recommended']) ? $value['name_category_recommended'] : (!empty($value['id_category_recommended_task']) ? $value['name_category_recommended_task'] : ''));
					}
					$code_Suggest = '';
					if (!empty($value['name_table'])) {
						$dtSuggest = get_table_where($value['name_table'], ['id' => $value['suggest_id']], '', 'row_array');
						if (!empty($dtSuggest)) {
							if (!empty($dtSuggest['reference_no'])) {
								$code_Suggest = $dtSuggest['reference_no'];
							}
							if (!empty($dtSuggest['code'])) {
								$code_Suggest = $dtSuggest['code'];
							}
							$link = '';
							$name_table = explode('tbl_', $value['name_table']);
							if (count($name_table) > 1) {
								$link = $name_table[1];
							} else {
								$name_table_v2 = explode('tbl', $value['name_table']);
								if (count($name_table_v2) > 1) {
									$link = $name_table_v2[1];
								}
							}
							$html = $code_Suggest;
							if ($value['type_kpi'] == 1) {
								$html = $dtSuggest['reference_no'];
							}
							$code_Suggest = $html;
						}
					}
					if (!empty($value['name_table_task'])) {
						$dtSuggest = get_table_where($value['name_table_task'], ['id' => $value['suggest_id_task']], '', 'row_array');
						if (!empty($dtSuggest)) {
							if (!empty($dtSuggest['reference_no'])) {
								$code_Suggest = $dtSuggest['reference_no'];
							}
							if (!empty($dtSuggest['code'])) {
								$code_Suggest = $dtSuggest['code'];
							}
							$link = '';
							$name_table = explode('tbl_', $value['name_table_task']);
							if (count($name_table) > 1) {
								$link = $name_table[1];
							} else {
								$name_table_v2 = explode('tbl', $value['name_table_task']);
								if (count($name_table_v2) > 1) {
									$link = $name_table_v2[1];
								}
							}
							$html = $code_Suggest;
							if ($value['type_kpi'] == 1) {
								$html = $dtSuggest['reference_no'];
							}
							$code_Suggest = $html;
						}
					}
					if (!empty($value['reference_no_po'])) {
						$object_id = $value['reference_no_po'] . "\n" . 'CĐ: ' . $value['name_stage'];
					} else {
						$object_id = !empty($code_Suggest) ? $code_Suggest : '';
					}

					//					$objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", ($key + 1));
					$objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['id']);
					$objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", (!empty($value['room_tasks']) ? $value['room_tasks'] : ''))->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", (!empty($value['code_category_tasks']) ? $value['code_category_tasks'] : ''))->getStyle("C$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", (!empty($value['name_category_tasks']) ? $value['name_category_tasks'] : ''))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $object_type)->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $object_id)->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", (!empty($value['startdate']) ? _dt($value['startdate']) : ''))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", (!empty($value['duedate']) ? _dt($value['duedate']) : ''))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", (!empty($staff_create) ? ($staff_create) : ''))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", (!empty($value['assignees']) ? $value['assignees'] : ''))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", (!empty($value['status']) ? format_task_status($value['status'], true, true) : 'Chưa bắt đầu'))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $resultTime)->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $priority)->getStyle("m$rowBegin")->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['ProductionReport'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);

					$this->db->where('taskid', $value['id_task']);
					$data_checklist_items = $this->db->get('tbltask_checklist_items')->result_array();
					if (count($data_checklist_items) > $count_quytrinh) {
						$count_quytrinh = count($data_checklist_items);
					}
					$date_start = $value['startdate']; // ngày bắt đầu
					$date_end = $value['duedate']; // ngày kết thúc	

					// if (!empty($value['duedate'])) {
					// 	$date_end = date('Y-m-d', strtotime($value['duedate'])) . ' 23:59:59';
					// } else {
					// 	$date_end = date('Y-m-d', strtotime($date_start)) . ' 23:59:59';
					// }
					if (empty($date_end)) {
						$date_end = $date_start;
					}
					$now = date('Y-m-d H:i:s');

					$hasPending = false;
					$allDone = true;
					$max_date_status = null;

					foreach ($data_checklist_items as $v) {
						if (empty($v['finished']) || $v['finished'] == 0) {
							$hasPending = true;
							$allDone = false;
							// không break ở đây nếu muốn thu thập thêm thông tin; nhưng pending đủ để đánh dấu chưa hoàn thành/trễ
							break;
						} else {
							// status == 1
							if (!empty($v['date_finished'])) {
								// lấy max date_status
								if ($max_date_status === null || strtotime($v['date_finished']) > strtotime($max_date_status)) {
									$max_date_status = $v['date_finished'];
								}
							}
						}
					}
					$checkis = '';
					if ($hasPending) {
						// có bước chưa xong
						if (strtotime($now) > strtotime($date_end)) {
							$checkis = 'Trễ';
						} else {
							$checkis = 'Chưa hoàn thành';
						}
					} elseif ($allDone) {
						// tất cả đã xong (status == 1)
						if (empty($max_date_status)) {
							// đã hoàn thành nhưng không có date_status -> coi là đã hoàn thành (không xác định thời gian)
							$checkis = 'Đã hoàn thành';
						} else {
							if (strtotime($max_date_status) > strtotime($date_end)) {
								$checkis = 'Trễ';
							} elseif (strtotime($max_date_status) < strtotime($date_start)) {
								$checkis = 'Sớm';
							} else {
								// max_date_status nằm trong [date_start, date_end]
								$checkis = 'Đúng';
							}
						}
					} else {
						// fallback
						$checkis = 'Chưa hoàn thành';
					}
					$objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $checkis)->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);

					$objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:O$rowBegin")->applyFromArray([
						'font' => array(
							'size' => 12,
							'name' => 'Times New Roman'
						),
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
						)
					])->getAlignment()->setWrapText(true);
					$stylePlain = [
						'font' => array(
							'size' => 12,
							'name' => 'Times New Roman'
						),
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
						)
					];
					$processColIndex = $demcheck;
					foreach ($data_checklist_items as $vProcess) {
						$processColIndex++;
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$processColIndex] . $rowBegin,
							$vProcess['description']
						)->getStyle($cloumns_excel[$processColIndex] . $rowBegin)->applyFromArray($stylePlain);
						$processColIndex++;
						$name_status = 'Chưa duyệt';
						$date_status = '';
						$staffName = '';
						if (!empty($vProcess['finished'])) {
							$staffName = get_staff_full_name($vProcess['finished_from']);
							$name_status = 'Đã duyệt';
							$date_status = _dt($vProcess['date_finished']);
						}
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$processColIndex] . $rowBegin,
							$staffName
						)->getStyle($cloumns_excel[$processColIndex] . $rowBegin)->applyFromArray($stylePlain);
						$processColIndex++;
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$processColIndex] . $rowBegin,
							$name_status
						)->getStyle($cloumns_excel[$processColIndex] . $rowBegin)->applyFromArray($stylePlain);
						$processColIndex++;
						$objPHPExcel->getActiveSheet()->setCellValue(
							$cloumns_excel[$processColIndex] . $rowBegin,
							$date_status
						)->getStyle($cloumns_excel[$processColIndex] . $rowBegin)->applyFromArray($stylePlain);
					}
				}
			}
			$check = $demcheck;

			$objPHPExcel->getActiveSheet()->setCellValue(
				$cloumns_excel[($check + 1)] . ($excelRowNum_quy - 1),
				'Quy trình'
			)->getStyle($cloumns_excel[($check + 1)] . ($excelRowNum_quy - 1))->applyFromArray($styleHeader);
			for ($is = 1; $is < ($count_quytrinh * 4); $is++) {
				$objPHPExcel->getActiveSheet()->setCellValue(
					$cloumns_excel[($check + 1 + $is)] . ($excelRowNum_quy - 1),
					''
				)->getStyle($cloumns_excel[($check + 1 + $is)] . ($excelRowNum_quy - 1))->applyFromArray($styleHeader);
			}
			$objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[($check + 1)] . ($excelRowNum_quy - 1) . ":" . $cloumns_excel[(($check) + ($count_quytrinh * 4))] . ($excelRowNum_quy - 1));
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
			$excelRowNum_quy++;
			$objPHPExcel->getActiveSheet()->getStyle("A$excelRowNum_quy:" . $cloumns_excel[(($demcheck) + ($count_quytrinh * 4))] . $rowBegin)->applyFromArray([
				'font' => array(
					'size' => 12,
					'name' => 'Times New Roman'
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			])->getAlignment()->setWrapText(true);
			$filename = lang('danh_sach_cong_viec') . '.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
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
				'filename' => $filename,
				'message' => lang('success'),
				'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
			);
			die(json_encode($response));
		}
	}

	public function order_by_tasks()
	{
		$id = $this->input->post('id');
		$order_by = $this->input->post('order_by');
		$order_before = $this->input->post('order_before');
		$id_before = $this->input->post('id_before');

		$this->db->where('id', $id);
		$this->db->update('tbltasks', [
			'order_by' => $order_before
		]);

		$this->db->where('id', $id_before);
		$this->db->update('tbltasks', [
			'order_by' => $order_by
		]);
		echo json_encode(['success' => true, 'alert_type' => 'success', 'messsage' => 'Sắp xếp thành công']);
		die();
	}

	public function searchPo($id = 0)
	{
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		$this->db->select('
                tbl_productions_orders.id as id, 
                CONCAT(tbl_productions_orders.reference_no) as text,
            ', false);
		$this->db->from('tbl_productions_orders');
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('tbl_productions_orders.reference_no', $term);
			$this->db->group_end();
		}
		$this->db->limit($limit);
		$dtResult = $this->db->get()->result_array();
		$data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
		if (!empty($id)) {
			$this->db->select('tbl_productions_orders.*');
			$this->db->from('tbl_productions_orders');
			$this->db->where_in('tbl_productions_orders.id', $id);
			$dtData = $this->db->get()->row_array();
			if (!empty($dtData)) {
				$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
			}
		}
		echo json_encode($data);
	}

	public function getStageByPo()
	{
		$data = [];
		$po_id = $this->input->post('po_id');

		$this->db->select('tbl_stages.id as id,tbl_stages.name as name');
		$this->db->from('tbl_stages');
		$this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.stage_id = tbl_stages.id');
		$this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $po_id);
		$this->db->group_by('tbl_stages.id');
		$dtStage = $this->db->get()->result_array();
		$data['dtStage'] = $dtStage;
		echo json_encode($data);
	}
	function inspection_criteria($id = '', $detail_id = '', $process_id = '', $is = 0)
	{
		$data['title'] = 'Kiểm quy trình';
		$data['id'] = $id;
		$data['process_id'] = $process_id;
		$data['detail_id'] = $detail_id;
		$data['is'] = $is;


		$this->db->select('tbl_tasks_process_child.*, tblcategory_tasks_process_child.role_processing, tblroles.code_role as code_role, tblroles.name as name_role');
		$this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_tasks_process_child.id_category_tasks_process', 'left');
		$this->db->join('tblroles', 'tblroles.roleid = tblcategory_tasks_process_child.role_processing', 'left');

		$this->db->where('tbl_tasks_process_child.task', $id);
		$this->db->where('tbl_tasks_process_child.id_category_tasks', $process_id);
		$data['category_hand_over'] = $this->db->get('tbl_tasks_process_child')->result_array();
		$this->load->view('admin/tasks/inspection_criteria', $data);
	}

	public function get_table_delivery_records_internal_proposal()
	{
		$process_id = $this->input->post('process_id');
		$id = $this->input->post('id');
		$is = $this->input->post('is');
		$detail_id = $this->input->post('detail_id');

		$this->db->select('tbl_tasks_process_child.*, tblcategory_tasks_process_child.role_processing, tblroles.code_role as code_role, tblroles.name as name_role,tblcategory_tasks_process_child.id as id_category_tasks_process');
		$this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_tasks_process_child.id_category_tasks_process', 'left');
		$this->db->join('tblroles', 'tblroles.roleid = tblcategory_tasks_process_child.role_processing', 'left');
		$this->db->where('tbl_tasks_process_child.id_category_tasks', $process_id);
		$this->db->where('tbl_tasks_process_child.task', $id);
		$data['category_hand_over'] = $this->db->get('tbl_tasks_process_child')->result_array();

		if (!is_admin()) {
			$this->db->where('tblstaff.staffid', get_staff_user_id());
			$ktStaff = $this->db->get('tblstaff')->row();
			$listRole = [];
			if (!empty($ktStaff->role)) {
				$listRole = [$ktStaff->role];
			}
			if (!empty($ktStaff->role)) {
				//                get_childs_id_role($ktStaff->role, $listRole);
				$listRole = get_all_related_roles($ktStaff->role);
			}
			foreach ($data['category_hand_over'] as $key => $value) {
				$name = '';
				if (!empty($value['role_processing']) && !is_numeric(array_search($value['role_processing'], $listRole))) {
					$data['category_hand_over'][$key]['not_role'] = 1;
				}
			}
		}

		foreach ($data['category_hand_over'] as $key => $value) {
			$name = '';
			$counts = 0;
			$parent_id = 0;
			$this->db->from('tbl_kpi_list_criteria_department');
			$this->db->where('tbl_kpi_list_criteria_department.id_task_procedure', $value['id_category_tasks_process']);
			$dtDatsa = $this->db->get()->row_array();
			if (!empty($dtDatsa)) {
				$parent_id = $dtDatsa['parent_id'];
			}
			while ($counts <= 3) {
				$counts++;
				if ($parent_id > 0) {
					$this->db->from('tbl_kpi_list_criteria_department');
					$this->db->where('tbl_kpi_list_criteria_department.id', $parent_id);
					$dtData = $this->db->get()->row_array();
					if (!empty($dtData)) {
						$name .= '->' . $dtData['name'];
						$parent_id = $dtData['parent_id'];
					}
					if (empty($dtData)) {
						$counts = 5;
					}
				}
			}
			$data['category_hand_over'][$key]['name_category_tasks_process'] = $name;
		}

		$data['id'] = $id;
		$data['process_id'] = $process_id;
		$data['detail_id'] = $detail_id;
		$this->load->view('admin/tasks/table_production', $data);
	}
	function add_task_process()
	{
		$_data = $this->input->post();
		$isCheck = !empty($_data['isCheck']) ? $_data['isCheck'] : NULL;
		$id = $this->input->post('id');
		$process_id = $this->input->post('process_id');
		$detail_id = $this->input->post('detail_id');
		$status = 1;
		$type = 1;
		$this->db->where('id', $detail_id);
		$internal_proposal = $this->db->get('tbltask_checklist_items')->row();
		if (!empty($internal_proposal) && !empty($internal_proposal->finished)) {
			$data['result'] = 0;
			$data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
			echo json_encode($data);
			die;
		}

		$this->db->where('taskid', $id);
		$this->db->where('id <', $internal_proposal->id);
		$this->db->order_by('id', 'desc');
		$check_status_bef = $this->db->get('tbltask_checklist_items')->row_array();
		if (!empty($check_status_bef)) {
			if ($check_status_bef['finished'] == 0) {
				$data['result'] = 0;
				$data['message'] = lang('Bước ' . $check_status_bef['description'] . ' chưa duyệt, Không thể duyệt bước này');
				echo json_encode($data);
				die;
			}
		}
		$CheckCreateBCKPH = $this->CheckCreateBCKPH($id, $process_id, $detail_id);
		if ($CheckCreateBCKPH == 2) {
			$data['result'] = 0;
			$data['message'] = lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại');
			echo json_encode($data);
			die;
		}
		if ($CheckCreateBCKPH > 2) {
			if (!in_array($CheckCreateBCKPH, array_keys($isCheck))) {
				$data['result'] = 0;
				$data['message'] = lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại');
				echo json_encode($data);
				die;
			}
		}

		//
		// $isCheckNot = !empty($_data['isCheckNot']) ? $_data['isCheckNot'] : array();
		// $inspection_criteria_all = !empty($_data['inspection_criteria_id']) ? $_data['inspection_criteria_id'] : array();
		// foreach ($inspection_criteria_all as $criteria_id) {
		// if (!isset($isCheck[$criteria_id]) && !isset($isCheckNot[$criteria_id])) {
		// 	$data['result'] = 0;
		// 	$data['message'] = lang('Vui lòng duyệt hết tất cả các tiêu chí');
		// 	echo json_encode($data);
		// 	die;
		// }

		$isCheckNot = !empty($_data['isCheckNot']) ? $_data['isCheckNot'] : array();
		$inspection_criteria_all = !empty($_data['inspection_criteria_id']) ? $_data['inspection_criteria_id'] : array();
		foreach ($inspection_criteria_all as $criteria_id) {
			if (!isset($isCheck[$criteria_id]) && !isset($isCheckNot[$criteria_id])) {
				$data['result'] = 0;
				$data['message'] = lang('Vui lòng duyệt hết tất cả các tiêu chí');
				echo json_encode($data);
				die;
			}
			if (isset($isCheckNot[$criteria_id])) {
				$production_report = get_table_where('tblproduction_report', ['id_tasks' => $id, 'id_tasks_process' => $detail_id, 'id_tasks_process_child' => $criteria_id], '', 'row_array');
				if (empty($production_report)) {
					$data['result'] = 0;
					$data['message'] = lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại');
					echo json_encode($data);
					die;
				} else {
					$this->db->select('tbl_process_production_report.*');
					$this->db->where('tbl_process_production_report.staff_process', 0);
					$this->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
					$this->db->from('tbl_process_production_report');
					$Success_process = $this->db->get()->num_rows();
					if (!empty($Success_process)) {
						$data['result'] = 0;
						$data['message'] = lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại');
						echo json_encode($data);
						die;
					}
				}
			}
		}
		//

		if (!empty($isCheck)) {
			foreach ($isCheck as $key => $value) {
				$ins_detail = [];
				$ins_detail['tasks'] = $id;
				$ins_detail['id_tasks_process'] = $detail_id;
				$ins_detail['process_id'] = $process_id;
				$ins_detail['inspection_criteria'] = $key;
				$ins_detail['isCheck'] = 1;

				$this->db->where('tasks', $id);
				$this->db->where('id_tasks_process', $detail_id);
				$this->db->where('process_id', $process_id);
				$this->db->where('inspection_criteria', $key);
				$ktProcess = $this->db->get('tbl_tasks_inspection_criteria_process')->row_array();
				if (empty($ktProcess)) {
					$this->db->insert('tbl_tasks_inspection_criteria_process', $ins_detail);
				} else {
					$this->db->where('id', $ktProcess['id']);
					$this->db->update('tbl_tasks_inspection_criteria_process', [
						'isCheck' => 1
					]);
				}
			}
		}

		if (!empty($_data['inspection_criteria_id'])) {
			foreach ($_data['inspection_criteria_id'] as $key => $inspection_criteria_id) {
				$ins_detail = [];
				$ins_detail['tasks'] = $id;
				$ins_detail['id_tasks_process'] = $detail_id;
				$ins_detail['process_id'] = $process_id;
				$ins_detail['inspection_criteria'] = $inspection_criteria_id;

				$this->db->where('tasks', $id);
				$this->db->where('id_tasks_process', $detail_id);
				$this->db->where('process_id', $process_id);
				$this->db->where('inspection_criteria', $inspection_criteria_id);
				$ktProcess = $this->db->get('tbl_tasks_inspection_criteria_process')->row_array();
				if (empty($ktProcess)) {
					$this->db->insert('tbl_tasks_inspection_criteria_process', $ins_detail);
				}
			}
		}

		$this->db->where('isCheck is NULL', false, false);
		$this->db->where('isCheckNot is NULL', false, false);
		$this->db->where('process_id', $process_id);
		$ktProcess = $this->db->get('tbl_tasks_inspection_criteria_process')->num_rows();
		if (empty($ktProcess)) {
			$this->db->where('id', $detail_id);
			$success = $this->db->update(db_prefix() . 'task_checklist_items', [
				'finished_from' => get_staff_user_id(),
				'date_finished' => date('Y-m-d H:i:s'),
				'finished' => 1,
			]);
		} else {
			$data['success'] = true;
			$data['alert_type'] = 'success';
			$data['message'] = lang('Check thành công và chờ ' . $ktProcess . ' bước duyệt còn lại');
			echo json_encode($data);
			die;
		}

		if (!empty($success)) {
			$getTasks = $this->tasks_model->get($id);
			if ($getTasks->status == 0 || $getTasks->status == 1) {
				$this->tasks_model->mark_as(4, $id);
			}



			$this->db->where('taskid', $id);
			$this->db->where('finished', 0);
			$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
			if (empty($ktChecklist)) {
				$this->tasks_model->mark_as(5, $id);
			}
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
	function add_task_process_reject()
	{
		$_data = $this->input->post();
		$isCheck = !empty($_data['isCheck']) ? $_data['isCheck'] : NULL;
		$isCheckNot = !empty($_data['isCheckNot']) ? $_data['isCheckNot'] : NULL;
		$id = $this->input->post('id');
		$process_id = $this->input->post('process_id');
		$detail_id = $this->input->post('detail_id');
		$status = 1;
		$type = 1;
		$this->db->where('id', $detail_id);
		$internal_proposal = $this->db->get('tbltask_checklist_items')->row();
		if (!empty($internal_proposal) && !empty($internal_proposal->finished)) {
			$data['result'] = 0;
			$data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
			echo json_encode($data);
			die;
		}

		$this->db->where('taskid', $id);
		$this->db->where('id <', $internal_proposal->id);
		$this->db->order_by('id', 'desc');
		$check_status_bef = $this->db->get('tbltask_checklist_items')->row_array();
		if (!empty($check_status_bef)) {
			if ($check_status_bef['finished'] == 0) {
				$data['result'] = 0;
				$data['message'] = lang('Bước ' . $check_status_bef['description'] . ' chưa duyệt, Không thể duyệt bước này');
				echo json_encode($data);
				die;
			}
		}
		$CheckCreateBCKPH = $this->CheckCreateBCKPH($id, $process_id, $detail_id);
		if ($CheckCreateBCKPH == 2) {
			$data['result'] = 0;
			$data['message'] = lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại');
			echo json_encode($data);
			die;
		}
		if ($CheckCreateBCKPH > 2) {
			if (is_array($isCheck) && !in_array($CheckCreateBCKPH, array_keys($isCheck))) {
				$data['result'] = 0;
				$data['message'] = lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại');
				echo json_encode($data);
				die;
			}
		}
		// $this->db->where('id', $detail_id);
		$success = true;
		if (!empty($success)) {
			if (!empty($isCheck)) {
				foreach ($isCheck as $key => $value) {
					$ins_detail = [];
					$ins_detail['tasks'] = $id;
					$ins_detail['id_tasks_process'] = $detail_id;
					$ins_detail['process_id'] = $process_id;
					$ins_detail['inspection_criteria'] = $key;
					$check_tasks_inspection_criteria_process = get_table_where('tbl_tasks_inspection_criteria_process', array('tasks' => $id, 'id_tasks_process' => $detail_id, 'inspection_criteria' => $key, 'process_id' => $process_id), '', 'row_array');
					if (!empty($check_tasks_inspection_criteria_process)) {
						$sins_detail['isCheck'] = 1;
						$sins_detail['isCheckNot'] = NULL;
						$this->db->where('id', $check_tasks_inspection_criteria_process['id']);
						$this->db->update('tbl_tasks_inspection_criteria_process', $sins_detail);
					} else {
						$ins_detail['isCheck'] = 1;
						$this->db->insert('tbl_tasks_inspection_criteria_process', $ins_detail);
					}
				}
			}

			$this->load->model('tasks_approval_model');
			if (!empty($isCheckNot)) {
				foreach ($isCheckNot as $key => $value) {
					$ins_detail = [];
					$ins_detail['tasks'] = $id;
					$ins_detail['id_tasks_process'] = $detail_id;
					$ins_detail['process_id'] = $process_id;
					$ins_detail['inspection_criteria'] = $key;
					$check_tasks_inspection_criteria_process = get_table_where('tbl_tasks_inspection_criteria_process', array('tasks' => $id, 'id_tasks_process' => $detail_id, 'inspection_criteria' => $key, 'process_id' => $process_id), '', 'row_array');
					if (!empty($check_tasks_inspection_criteria_process)) {
						$sins_detail['isCheck'] = NULL;
						$sins_detail['isCheckNot'] = 1;
						$this->db->where('id', $check_tasks_inspection_criteria_process['id']);
						$this->db->update('tbl_tasks_inspection_criteria_process', $sins_detail);
					} else {
						$ins_detail['isCheckNot'] = 1;
						$this->db->insert('tbl_tasks_inspection_criteria_process', $ins_detail);
					}

					//cancel email queue
					$this->tasks_approval_model->stop_next_step_reminders($id, $process_id, $detail_id, $key);
					$this->tasks_approval_model->stop_next_step_reminders($id, $process_id, $detail_id, $key);
				}
			}

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
	public function getProcedureTasks()
	{
		$category_tasks_search = $this->input->post('category_tasks_search');
		$this->db->select('tblcategory_tasks_process.*', false);
		$this->db->from('tblcategory_tasks_process');
		$this->db->where('tblcategory_tasks_process.id_category_tasks', $category_tasks_search);
		$result = $this->db->get()->result_array();
		echo json_encode($result);
	}
	function inspection_criteria_all()
	{
		$_data = $this->input->post();
		$this->db->select('tbltasks.id');
		$this->db->where('category_tasks', $_data['category_tasks_search']);
		$this->db->where('EXISTS (SELECT 1
		FROM tbltask_checklist_items 
		WHERE tbltask_checklist_items.finished = 0 AND tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.process_id = ' . $_data['procedure_tasks'] . ')', false, false);
		$this->db->where('NOT EXISTS (SELECT 1
		FROM tbltask_checklist_items 
		WHERE tbltask_checklist_items.finished = 0 AND tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.process_id < ' . $_data['procedure_tasks'] . ')', false, false);
		if (!empty($_data['date_start'])) {
			$this->db->where('DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") >=', to_sql_date($_data['date_start']));
		}
		if (!empty($_data['date_end'])) {
			$this->db->where('DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <=', to_sql_date($_data['date_end']));
		}
		if (!empty($_data['date_start_end'])) {
			$this->db->where('DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") >=', to_sql_date($_data['date_start_end']));
		}
		if (!empty($_data['date_end_end'])) {
			$this->db->where('DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <=', to_sql_date($_data['date_end_end']));
		}
		$task = $this->db->get('tbltasks')->result_array();
		if (empty($task)) {
			set_alert('danger', lang('Không có công việc nào cần duyệt nhanh'));
			die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('admin')) . "'; }, 10);</script>");
		}
		$array_task = [];
		foreach ($task as $key => $value) {
			$array_task[] = $value['id'];
		}
		$data['title'] = 'Kiểm quy trình';
		$data['array_task'] = implode(',', $array_task);
		$data['procedure_tasks'] = $_data['procedure_tasks'];
		$data['category_tasks_search'] = $_data['category_tasks_search'];
		$data['date_start'] = $_data['date_start'];
		$data['date_end'] = $_data['date_end'];
		$data['date_start_end'] = $_data['date_start_end'];
		$data['date_end_end'] = $_data['date_end_end'];
		$this->db->select('tbl_tasks_process_child.*');
		$this->db->where('tbl_tasks_process_child.task', $array_task[0]);
		$this->db->where('tbl_tasks_process_child.id_category_tasks', $_data['procedure_tasks']);
		$data['category_hand_over'] = $this->db->get('tbl_tasks_process_child')->result_array();
		$this->load->view('admin/tasks/inspection_criteria_all', $data);
	}

	function add_task_process_all()
	{
		$_data = $this->input->post();

		$isCheck = !empty($_data['isCheck']) ? $_data['isCheck'] : NULL;
		$array_task = $this->input->post('array_task');
		$array_task = explode(',', $array_task);
		$procedure_tasks = $this->input->post('procedure_tasks');
		foreach ($array_task as $key => $value) {
			$this->db->where('process_id', $procedure_tasks);
			$this->db->where('taskid', $value);
			$internal_proposal = $this->db->get('tbltask_checklist_items')->row_array();
			if (!empty($internal_proposal) && !empty($internal_proposal->finished)) {
				continue;
			}
			$this->db->where('id', $internal_proposal['id']);
			$success = $this->db->update(db_prefix() . 'task_checklist_items', [
				'finished_from' => get_staff_user_id(),
				'date_finished' => date('Y-m-d H:i:s'),
				'finished' => 1,
			]);
			if (!empty($isCheck)) {
				foreach ($isCheck as $k => $v) {


					$this->db->select('tbl_tasks_process_child.*');
					$this->db->where('tbl_tasks_process_child.task', $value);
					$this->db->where('tbl_tasks_process_child.id_category_tasks_process', $k);
					$this->db->where('tbl_tasks_process_child.id_category_tasks', $procedure_tasks);
					$category_hand_over = $this->db->get('tbl_tasks_process_child')->row_array();
					// id_category_tasks_process

					$ins_detail = [];
					$ins_detail['tasks'] = $value;
					$ins_detail['id_tasks_process'] = $internal_proposal['id'];
					$ins_detail['process_id'] = $procedure_tasks;
					$ins_detail['inspection_criteria'] = $category_hand_over['id'];
					$ins_detail['isCheck'] = 1;
					$this->db->insert('tbl_tasks_inspection_criteria_process', $ins_detail);
				}
			}
			$this->db->where('taskid', $value);
			$this->db->where('finished', 0);
			$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
			if (empty($ktChecklist)) {
				$this->tasks_model->mark_as(5, $value);
			}
		}

		if (!empty($success)) {
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

	public function searchCategoryTasks()
	{
		$data = [];

		$room_task = $this->input->get('room_task');
		$category_tasks = $this->site_model->getCategoryTasks([], $room_task);
		$data['category_tasks'] = $category_tasks;
		echo json_encode($data);
	}
	function searchPurchaseOrder($id = 0)
	{
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		$this->db->select('
                tblpurchase_order.id as id, 
                CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as text,
            ', false);
		$this->db->from('tblpurchase_order');
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code)', $term);
			$this->db->group_end();
		}
		$this->db->where('tblpurchase_order.cancel', 0);
		$this->db->limit($limit);
		$this->db->order_by('tblpurchase_order.id', 'desc');
		$dtResult = $this->db->get()->result_array();
		$data['results'][] = ['text' => lang('Đơn đặt hàng mua'), 'children' => $dtResult];
		if (!empty($id)) {
			$this->db->select('tblpurchase_order.*,CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as text');
			$this->db->from('tblpurchase_order');
			$this->db->where_in('tblpurchase_order.id', $id);
			$dtData = $this->db->get()->row_array();
			if (!empty($dtData)) {
				$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['text']];
			}
		}
		echo json_encode($data);
	}
	function searchImport($id = 0)
	{
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		$this->db->select('
                tblimport.id as id, 
                CONCAT(tblimport.prefix,"-",tblimport.code) as text,
            ', false);
		$this->db->from('tblimport');
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('CONCAT(tblimport.prefix,"-",tblimport.code)', $term);
			$this->db->group_end();
		}
		$this->db->where('tblimport.status', 2);
		$this->db->limit($limit);
		$this->db->order_by('tblimport.id', 'desc');
		$dtResult = $this->db->get()->result_array();
		$data['results'][] = ['text' => lang('Nhập kho'), 'children' => $dtResult];
		if (!empty($id)) {
			$this->db->select('tblimport.*,CONCAT(tblimport.prefix,"-",tblimport.code) as text');
			$this->db->from('tblimport');
			$this->db->where_in('tblimport.id', $id);
			$dtData = $this->db->get()->row_array();
			if (!empty($dtData)) {
				$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['text']];
			}
		}
		echo json_encode($data);
	}
	public function searchOrders($id = 0)
	{
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		$this->db->select('
                tbl_orders.id as id, 
                tbl_orders.reference_no as text,
            ', false);
		$this->db->from('tbl_orders');
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('tbl_orders.reference_no', $term);
			$this->db->group_end();
		}
		$this->db->where('tbl_orders.status', 'approved');
		$this->db->limit($limit);
		$dtResult = $this->db->get()->result_array();
		$data['results'][] = ['text' => lang('Đơn đặt hàng'), 'children' => $dtResult];
		if (!empty($id)) {
			$this->db->select('tbl_orders.*');
			$this->db->from('tbl_orders');
			$this->db->where_in('tbl_orders.id', $id);
			$dtData = $this->db->get()->row_array();
			if (!empty($dtData)) {
				$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
			}
		}
		echo json_encode($data);
	}
	function listcodetasks()
	{
		$data = $this->input->post();
		$department = [];
		if (!empty($data['department'])) {
			$department = explode(',', trim($data['department'], ','));
		}
		$category_tasks = $this->site_model->getCategoryTasks([], $department);
		$string_option = "<option></option>";
		foreach ($category_tasks as $key => $value) {
			if (!empty($value['id'])) {
				$checkeds = '';
				// if ($checked == $value['id']) {
				// 	$checkeds = 'selected';
				// 	$value['child'] = 1;
				// }
				$string_option .= '<option ' . $checkeds . ' value="' . $value['id'] . '">' . $value['code'] . ' - ' . $value['content'] . '</option>';
			}
		}
		echo $string_option;
		die;
	}

	public function export_excel_detailed_overview()
	{
		if ($this->input->post('export_excel')) {
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
			$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
				->setWidth(20);
			$objPHPExcel->getDefaultStyle()->applyFromArray([
				'font' => array(
					'name' => 'Times New Roman'
				),
			]);

			insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

			$excel = cloumns_excel();
			$objPHPExcel->getActiveSheet()->setCellValue(
				'A5',
				('TỔNG QUAN PHÂN CÔNG')
			)->getStyle("A5")->applyFromArray([
				'font' => array(
					'bold' => true,
					'size' => 16,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			]);
			$objPHPExcel->getActiveSheet()->mergeCells('A5:L5');

			$rowBegin = 6;
			$iExcel = -1;
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Tên');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Ngày bắt đầu');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Ngày chốt');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Trạng thái');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Tổng số tập tin đính kèm đã thêm');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Tổng số bình luận');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Chi tiết công việc');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Tổng thời gian đăng nhập');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Đã hoàn thành trước hẹn');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Đã chỉ định cho');

			$iExcelEnd = $iExcel;
			$objPHPExcel->getActiveSheet()->getStyle($excel[0] . ($rowBegin - 1) . ':' . $excel[$iExcel] . ($rowBegin))->applyFromArray([
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'font' => array(
					'bold' => true,
				),
			]);

			$has_permission_create = has_permission('tasks', '', 'create');
			$has_permission_view = has_permission('tasks', '', 'view');
			if (!$has_permission_view) {
				$staff_id = get_staff_user_id();
			} elseif ($this->input->post('member')) {
				$staff_id = $this->input->post('member');
			} else {
				$staff_id = '';
			}
			$month = ($this->input->post('month') ? $this->input->post('month') : date('m'));
			if ($this->input->post() && $this->input->post('month') == '') {
				$month = '';
			}

			$room_task = $this->input->post('room_task');
			$status = $this->input->post('status');
			$fetch_month_from = 'startdate';
			$year = ($this->input->post('year') ? $this->input->post('year') : date('Y'));
			$project_id = $this->input->get('project_id');
			$overview = [];
			for ($m = 1; $m <= 12; $m++) {
				if ($month != '' && $month != $m) {
					continue;
				}
				// Task rel_name
				$sqlTasksSelect = '*,' . tasks_rel_name_select_query() . ' as rel_name';
				// Task logged time
				$selectLoggedTime = get_sql_calc_task_logged_time('tmp-task-id');
				// Replace tmp-task-id to be the same like tasks.id
				$selectLoggedTime = str_replace('tmp-task-id', db_prefix() . 'tasks.id', $selectLoggedTime);
				if (is_numeric($staff_id)) {
					$selectLoggedTime .= ' AND staff_id=' . $staff_id;
					$sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
				} else {
					$sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
				}
				$sqlTasksSelect .= ' as total_logged_time';
				// Task checklist items
				$sqlTasksSelect .= ',' . get_sql_select_task_total_checklist_items();
				if (is_numeric($staff_id)) {
					$sqlTasksSelect .= ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_checklist_items WHERE taskid=' . db_prefix() . 'tasks.id AND finished=1 AND finished_from=' . $staff_id . ') as total_finished_checklist_items';
				} else {
					$sqlTasksSelect .= ',' . get_sql_select_task_total_finished_checklist_items();
				}
				// Task total comment and total files
				$selectTotalComments = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_comments WHERE taskid=' . db_prefix() . 'tasks.id';
				$selectTotalFiles = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'files WHERE rel_id=' . db_prefix() . 'tasks.id AND rel_type="task"';
				if (is_numeric($staff_id)) {
					$sqlTasksSelect .= $selectTotalComments . ' AND staffid=' . $staff_id . ') as total_comments_staff';
					$sqlTasksSelect .= $selectTotalFiles . ' AND staffid=' . $staff_id . ') as total_files_staff';
				}
				$sqlTasksSelect .= $selectTotalComments . ') as total_comments';
				$sqlTasksSelect .= $selectTotalFiles . ') as total_files';
				// Task assignees
				$sqlTasksSelect .= ',' . get_sql_select_task_asignees_full_names() . ' as assignees' . ',' . get_sql_select_task_assignees_ids() . ' as assignees_ids';
				$this->db->select($sqlTasksSelect);
				$this->db->where('MONTH(' . $fetch_month_from . ')', $m);
				$this->db->where('YEAR(' . $fetch_month_from . ')', $year);
				if ($project_id && $project_id != '') {
					$this->db->where('rel_id', $project_id);
					$this->db->where('rel_type', 'project');
				}
				if (!$has_permission_view) {
					$sqlWhereStaff = '(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . ')';
					// User dont have permission for view but have for create
					// Only show tasks createad by this user.
					if ($has_permission_create) {
						$sqlWhereStaff .= ' OR addedfrom=' . get_staff_user_id();
					}
					$sqlWhereStaff .= ')';
					$this->db->where($sqlWhereStaff);
				} elseif ($has_permission_view) {
					if (is_numeric($staff_id)) {
						$this->db->where('(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . '))');
					}
				}
				if ($status) {
					$this->db->where('status', $status);
				}

				if (!empty($room_task)) {
					$this->db->where('EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . implode(',', $room_task) . '))', false, false);
				}

				$this->db->order_by($fetch_month_from, 'ASC');
				array_push($overview, $m);
				$overview[$m] = $this->db->get(db_prefix() . 'tasks')->result_array();
			}
			unset($overview[0]);
			$overview = [
				'staff_id' => $staff_id,
				'detailed' => $overview,
			];
			$staff_id = $overview['staff_id'];
			$overview = $overview['detailed'];

			if (!empty($overview)) {
				$start = 0;
				foreach ($overview as $month => $_data) {
					if (count($_data) == 0) {
						continue;
					}

					foreach ($_data as $key => $task) {
						$iExcel = -1;
						$start++;
						$rowBegin++;

						$finished_on_time_class = '';
						$finishedOrder = 0;
						if (date('Y-m-d', strtotime($task['datefinished'])) > $task['duedate'] && $task['status'] == Tasks_model::STATUS_COMPLETE && is_date($task['duedate'])) {
							$finished_on_time_class = 'text-danger';
							$finished_showcase = _l('task_not_finished_on_time_indicator');
						} else if (date('Y-m-d', strtotime($task['datefinished'])) <= $task['duedate'] && $task['status'] == Tasks_model::STATUS_COMPLETE && is_date($task['duedate'])) {
							$finishedOrder = 1;
							$finished_showcase = _l('task_finished_on_time_indicator');
						} else {
							$finished_on_time_class = '';
							$finished_showcase = '';
						}

						$str1 = '';
						if (!is_numeric($staff_id)) {
							$str1 = $task['total_files'];
						} else {
							$str1 = $task['total_files_staff'] . '/' . $task['total_files'];
						}

						$str2 = '';
						if (!is_numeric($staff_id)) {
							$str2 = $task['total_comments'];
						} else {
							$str2 = $task['total_comments_staff'] . '/' . $task['total_comments'];
						}

						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $task['name']);
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _d($task['startdate']));
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _d($task['duedate']));
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, format_task_status($task['status'], false, true));
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $str1);
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $str2);
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $task['total_finished_checklist_items'] . '/' . $task['total_checklist_items']);
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, seconds_to_time_format($task['total_logged_time']));
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $finished_showcase);
						$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, strip_tags(format_members_by_ids_and_names($task['assignees_ids'], $task['assignees'], false)));
					}
				}
			}

			$objPHPExcel->getActiveSheet()->getStyle("" . $excel[0] . "6:" . $excel[$iExcelEnd] . "$rowBegin")->applyFromArray([
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
			])->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("" . $excel[0] . "6:" . $excel[$iExcelEnd] . "$rowBegin")->getAlignment()->setWrapText(true);

			$filename = lang('tongquanphancong') . '.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

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
				'filename' => $filename,
				'message' => lang('success'),
				'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
			);
			die(json_encode($response));
		}
	}

	public function export_excel_tasks()
	{
		if ($this->input->post('export_excel')) {
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
			$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
				->setWidth(20);
			$objPHPExcel->getDefaultStyle()->applyFromArray([
				'font' => array(
					'name' => 'Times New Roman'
				),
			]);

			insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

			$excel = cloumns_excel();
			$objPHPExcel->getActiveSheet()->setCellValue(
				'A5',
				('PHÂN CÔNG')
			)->getStyle("A5")->applyFromArray([
				'font' => array(
					'bold' => true,
					'size' => 16,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			]);
			$objPHPExcel->getActiveSheet()->mergeCells('A5:L5');

			$rowBegin = 6;
			$iExcel = -1;
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'STT');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'ID');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Liên quan đến');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Phòng ban');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Mã công việc');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Tên mã công việc');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Tiêu đề');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Loại phiếu');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Mã phiếu');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Ngày bắt đầu');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Hạn chót');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Người giao việc');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Người được phân công');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Người hoàn thành');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Trạng thái');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Kết quả');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Mức độ ưu tiên');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Số thứ tự ưu tiên');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin . '', 'Báo cáo sự cố');
			$iExcelEnd = $iExcel;
			$objPHPExcel->getActiveSheet()->getStyle($excel[0] . ($rowBegin - 1) . ':' . $excel[$iExcel] . ($rowBegin))->applyFromArray([
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'font' => array(
					'bold' => true,
				),
			]);

			$is_view_per = has_permission('tasks', '', 'view');
			$date_start_search = $this->input->post('date_start_search');
			$date_end_search = $this->input->post('date_end_search');
			if (empty($date_start_search) || empty($date_end_search)) {
				$response = array(
					'result' => 0,
					'message' => lang('Vui lòng lọc ngày bắt đầu và kết thúc'),
				);
				die(json_encode($response));
			}
			$date_start_search = to_sql_date($date_start_search) . ' 00:00:00';
			$date_end_search = to_sql_date($date_end_search) . ' 23:59:59';

			$aColumns = [
				'tbltasks.id as id',
				'tbltasks.category_tasks',
				'tbltasks.name as task_name',
				'tbltasks.rel_type',
				'tbltasks.rel_id',
				'tbltasks.startdate',
				'tbltasks.duedate',
				'tbltasks.addedfrom',
				'tbltasks.status',
				'(
					SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
					FROM tbltaskstimers 
					WHERE tbltaskstimers.task_id = tbltasks.id
				) as _minute',
				'tbltasks.priority',
				'tbltasks.order_by',
				'tbltasks.staff_finished'
			];

			$where = [];
			if (!$is_view_per) {
				array_push($where, get_tasks_where_string());
			}
			array_push($where, ' AND tbltasks.startdate >= "' . $date_start_search . '" AND tbltasks.startdate <= "' . $date_end_search . '"');

			if ($this->input->post('list_departments')) {
				$where[] = 'AND EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . $this->input->post('list_departments') . '))';
				$whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . $this->input->post('list_departments') . '))';
			}

			if ($this->input->post('list_staff')) {
				$where[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltasks.addedfrom != tbltask_assigned.staffid AND tbltask_assigned.staffid IN (' . $this->input->post('list_staff') . '))';
				$whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltasks.addedfrom != tbltask_assigned.staffid AND tbltask_assigned.staffid IN (' . $this->input->post('list_staff') . '))';
			}

			if ($this->input->post('staff_follower_search')) {
				$where[] = 'AND EXISTS (SELECT 1 FROM tbltask_followers WHERE tbltask_followers.taskid = tbltasks.id AND tbltask_followers.staffid IN (' . $this->input->post('staff_follower_search') . '))';
				$whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_followers WHERE tbltask_followers.taskid = tbltasks.id AND tbltask_followers.staffid IN (' . $this->input->post('staff_follower_search') . '))';
			}

			if ($this->input->post('list_staff_create')) {
				$where[] = 'AND tbltasks.addedfrom IN (' . $this->input->post('list_staff_create') . ')';
				$whereTotal[] = 'AND tbltasks.addedfrom IN (' . $this->input->post('list_staff_create') . ')';
			}

			if ($this->input->post('date_start_end_search')) {
				$date_start_end_search = $this->input->post('date_start_end_search');
				$where[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") >= "' . to_sql_date($date_start_end_search) . '"';
				$whereTotal[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") >= "' . to_sql_date($date_start_end_search) . '"';
			}
			if ($this->input->post('date_end_end_search')) {
				$date_end_end_search = $this->input->post('date_end_end_search');
				$where[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <= "' . to_sql_date($date_end_end_search) . '"';
				$whereTotal[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <= "' . to_sql_date($date_end_end_search) . '"';
			}

			$where = stringWhere($where);
			$this->db->select(implode(',', $aColumns), false);
			$this->db->from('tbltasks');
			$this->db->where($where, false, false);
			$tasks = $this->db->get()->result_array();

			if (!empty($tasks)) {
				$arrTasksId = [];
				$arrCategoryTasks = [];
				$arrStaffAddedFrom = [];
				$arrStaffFinished = [];
				foreach ($tasks as $key => $value) {
					$arrTasksId[] = $value['id'];
					$arrCategoryTasks[] = $value['category_tasks'];
					$arrStaffAddedFrom[] = $value['addedfrom'];
					$arrStaffFinished[] = $value['staff_finished'];
				}

				if (!empty($arrTasksId)) {
					$this->db->select('
						tbltask_department.task_id as task_id,
						tbl_room.name as name
					', false);
					$this->db->from('tbltask_department');
					$this->db->join('tbl_room', 'tbl_room.id = tbltask_department.department_id');
					$this->db->where_in('tbltask_department.task_id', $arrTasksId);
					$listTaskDepartment = $this->db->get()->result_array();
					if (!empty($listTaskDepartment)) {
						$listTaskDepartment = array_reduce($listTaskDepartment, function ($carry, $item) {
							$carry[$item['task_id']][] = $item;
							return $carry;
						});
					}

					$this->db->select('
						tbltask_assigned.taskid as task_id,
						tblstaff.staffid as staffid,
						CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname
					', false);
					$this->db->from('tbltask_assigned');
					$this->db->join('tblstaff', 'tblstaff.staffid = tbltask_assigned.staffid');
					$this->db->where_in('tbltask_assigned.taskid', $arrTasksId);
					$listTaskAssigned = $this->db->get()->result_array();
					if (!empty($listTaskAssigned)) {
						$listTaskAssigned = array_reduce($listTaskAssigned, function ($carry, $item) {
							$carry[$item['task_id']][] = $item;
							return $carry;
						});
					}

					// tblproduction_report
					$this->db->select('
						tblproduction_report.id_tasks as task_id,
						tblproduction_report.name_report as name_report
					', false);
					$this->db->from('tblproduction_report');
					$this->db->where_in('tblproduction_report.id_tasks', $arrTasksId);
					$listProductionReport = $this->db->get()->result_array();
					if (!empty($listProductionReport)) {
						$listProductionReport = array_reduce($listProductionReport, function ($carry, $item) {
							$carry[$item['task_id']][] = $item;
							return $carry;
						});
					}
				}

				if (!empty($arrCategoryTasks)) {
					$this->db->select('
						tblcategory_tasks.id as id,
						tblcategory_tasks.code as code,
						tblcategory_tasks.content as content,
						tblcategory_tasks.time
					', false);
					$this->db->from('tblcategory_tasks');
					$this->db->where_in('tblcategory_tasks.id', $arrCategoryTasks);
					$listCategoryTasks = $this->db->get()->result_array();
					if (!empty($listCategoryTasks)) {
						$listCategoryTasks = array_reduce($listCategoryTasks, function ($carry, $item) {
							$carry[$item['id']] = $item;
							return $carry;
						});
					}
				}

				if (!empty($arrStaffAddedFrom)) {
					$this->db->select('
						tblstaff.staffid as staffid,
						CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname
					', false);
					$this->db->from('tblstaff');
					$this->db->where_in('tblstaff.staffid', $arrStaffAddedFrom);
					if (!empty($arrStaffFinished)) {
						$this->db->or_where_in('tblstaff.staffid', $arrStaffFinished);
					}
					$listStaff = $this->db->get()->result_array();
					if (!empty($listStaff)) {
						$listStaff = array_reduce($listStaff, function ($carry, $item) {
							$carry[$item['staffid']] = $item;
							return $carry;
						});
					}
				}

				// print_arrays($listTaskDepartment);
				$start = 0;
				foreach ($tasks as $key => $aRow) {
					$iExcel = -1;
					$start++;
					$rowBegin++;
					$task_id = $aRow['id'];
					$taskDepartment = $listTaskDepartment[$task_id] ?? null;
					$categoryTasks = $listCategoryTasks[$aRow['category_tasks']] ?? null;
					$dtStaff = $listStaff[$aRow['addedfrom']] ?? null;
					$taskAssigned = $listTaskAssigned[$aRow['id']] ?? null;
					$productionReport = $listProductionReport[$aRow['id']] ?? null;
					$staffFinished = $listStaff[$aRow['staff_finished']] ?? null;

					$rel_type = $aRow['rel_type'];
					$rel_id = $aRow['rel_id'];
					$str_rel_type = '';
					$str_rel_id = '';
					if (!empty($rel_type) && !empty($rel_id)) {
						$task_rel_data = get_relation_data($aRow['rel_type'], $aRow['rel_id']);
						$task_rel_value = get_relation_values($task_rel_data, $aRow['rel_type']);
						$str_rel_id = $task_rel_value['name'] ?? '';

						if (!empty($task_rel_value['code'])) {
							$str_rel_id .= '(' . $task_rel_value['code'] . ')';
						}
					}

					if (!empty($rel_type)) {
						$str_rel_type = _l('c_tasks_' . $aRow['rel_type']);
					}

					$strRoom = !empty($taskDepartment) ? implode("\n", array_column($taskDepartment, 'name')) : '';

					$addedfrom = $aRow['addedfrom'];
					if (!empty($taskAssigned)) {
						$taskAssigned = array_filter($taskAssigned, function ($task) use ($addedfrom) {
							return $task['staffid'] !== $addedfrom;
						});
					}
					$strtaskAssigned = !empty($taskAssigned) ? implode("\n", array_column($taskAssigned, 'fullname')) : '';

					$status = $aRow['status'];
					$status = get_task_status_by_id($status);

					$time = $categoryTasks['time'] ?? 0;
					$resultTime = 'Chưa tính giờ';
					if (empty($aRow['category_tasks'])) {
						$resultTime = 'Chưa chọn mã công việc';
					} else if (!empty($aRow['_minute'])) {
						if ($aRow['_minute'] > $time) {
							$resultTime = 'Chưa đạt';
						} elseif ($aRow['_minute'] == $time) {
							$resultTime = 'Đạt';
						} else {
							$resultTime = 'Vượt KPI';
						}
					} else {
						$resultTime = 'Chưa tính giờ';
					}

					if (!empty($aRow['_minute'])) {
						$resultTime .= "\nTổng TG thực hiện 	" . number_format_data($aRow['_minute']) . " (Phút)";
					}

					$priority = $aRow['priority'];
					$strPriority = $aRow['priority'] ? task_priority($aRow['priority']) : '';

					$strProductionReport = !empty($productionReport) ? implode("\n", array_column($productionReport, 'name_report')) : '';
					$strStaffFinished = '';
					if ($aRow['status'] == 5) {
						$strStaffFinished = $staffFinished['fullname'] ?? '';
						if (empty($strStaffFinished)) {
							$strStaffFinished = $strtaskAssigned;
						}
					}

					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $start);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $aRow['id']);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strRoom);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strRoom);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $categoryTasks['code'] ?? '');
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $categoryTasks['content'] ?? '');
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $aRow['task_name']);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $str_rel_type);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $str_rel_id);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _dt($aRow['startdate'] ?? null));
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _dt($aRow['duedate'] ?? null));
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $dtStaff['fullname'] ?? '');
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strtaskAssigned);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strStaffFinished); //Người hoàn thành
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $status['name'] ?? '');
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $resultTime);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strPriority);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $aRow['order_by']);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strProductionReport);
				}
			}

			$objPHPExcel->getActiveSheet()->getStyle("" . $excel[0] . "6:" . $excel[$iExcelEnd] . "$rowBegin")->applyFromArray([
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
			])->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("" . $excel[0] . "6:" . $excel[$iExcelEnd] . "$rowBegin")->getAlignment()->setWrapText(true);

			$filename = lang('phancong') . '.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

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
				'filename' => $filename,
				'message' => lang('success'),
				'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
			);
			die(json_encode($response));
		}
	}
	public function CheckCreateBCKPH($id = '', $process_id = '', $detail_id = '')
	{
		$this->db->select('tbl_tasks_process_child.*');
		$this->db->where('tbl_tasks_process_child.id_category_tasks', $process_id);
		$this->db->where('tbl_tasks_process_child.task', $id);
		$category_hand_over = $this->db->get('tbl_tasks_process_child')->result_array();
		$is_check = 1;
		foreach ($category_hand_over as $key => $value) {
			$check = get_table_where('tbl_tasks_inspection_criteria_process', ['tasks' => $id, 'process_id' => $process_id, 'id_tasks_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');

			$isCheckNot = '';
			if (!empty($check)) {
				if ($check['isCheckNot'] == 1) {
					$isCheckNot = 1;
				}
			}
			if ($isCheckNot == 1) {
				$production_report = get_table_where('tblproduction_report', ['id_tasks' => $id, 'id_tasks_process' => $detail_id, 'id_tasks_process_child' => $value['id']], '', 'row_array');

				if (!empty($production_report)) {
					$this->db->select('tbl_process_production_report.*');
					$this->db->where('tbl_process_production_report.staff_process', 0);
					$this->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
					$this->db->from('tbl_process_production_report');
					$Success_process = $this->db->get()->num_rows();
					if (!empty($Success_process)) {
						$is_check = 2;
					}
				} else {
					$is_check = $value['id'];
				}
			}
		}
		return $is_check;
	}
}
