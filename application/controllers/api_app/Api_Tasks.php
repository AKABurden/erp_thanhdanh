<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Tasks extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('quotes_orders_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('deliveries_model');
        $this->load->model('returned_goods_model');
        $this->load->model('clients_model');
        $this->load->model('site_model');
        $this->load->model('tasks_model');
        $this->load->model('misc_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('orders');
        $this->datetime_now = time();

        $tokenAccount = '';
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['tokenAccount'])) {
                    $tokenAccount = $data_post['tokenAccount'];
                }
            }
        } else if ($this->input->post()) {
            $data_post = $this->input->post();
            if (!empty($data_post['tokenAccount'])) {
                $tokenAccount = $data_post['tokenAccount'];
            }
        }



        $staffid = checkTokenLoginApp($tokenAccount);
        $staff = get_table_where('tblstaff', array('staffid' => $staffid), '', 'row');
        if (!empty($staff)) {
            $this->staffid = $staffid;
        } else {
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'result' => false,
            ]);
            die;
        }

        $this->isAdmin = is_admin($this->staffid);
        $this->hasPermissionCreate = ($this->isAdmin ? true : has_permission('tasks', $this->staffid, 'create'));
        $this->hasPermissionEdit = ($this->isAdmin ? true : has_permission('tasks', $this->staffid, 'edit'));
        $this->hasPermissionDelete = ($this->isAdmin ? true : has_permission('tasks', $this->staffid, 'delete'));
        $this->hasPermissionView = ($this->isAdmin ? true : has_permission('tasks', $this->staffid, 'view'));
        $this->hasPermissionViewOwn = ($this->isAdmin ? true : has_permission('tasks', $this->staffid, 'view_own'));
		
        $this->branchID = get_staff_user_id_branch_app($this->staffid);
        if ($this->branchID == 'main') $this->branchID = 0;

        $this->get_statuses = [];
        $get_statuses = $this->tasks_model->get_statuses();
        if (!empty($get_statuses)) {
            foreach ($get_statuses as $key => $value) {
                $this->get_statuses[$value['id']] = $value;
                if ($value['id'] == 1) {
                    $this->get_statuses[$value['id']]['color'] = '#D62839';
                } else if ($value['id'] == 2) {
                    $this->get_statuses[$value['id']]['color'] = '#BA324F';
                } else if ($value['id'] == 3) {
                    $this->get_statuses[$value['id']]['color'] = '#175676';
                } else if ($value['id'] == 4) {
                    $this->get_statuses[$value['id']]['color'] = '#4BA3C3';
                } else if ($value['id'] == 5) {
                    $this->get_statuses[$value['id']]['color'] = '#759AAB';
                }
            }
        }
    }

    public function getTasksList($page = 1, $limit = 10)
    {
        $response['isSuccess'] = true;
        $response['message'] = "Thành công";
        if (!$this->hasPermissionView && !$this->hasPermissionViewOwn) {
            $response['isSuccess'] = false;
            $response['message'] = "Bạn không có quyền xem";
            echo json_encode($response);
            die;
        }


        $arrIDStaff = employee_manage_staff_app($this->staffid);

        $search = '';
        $staff_search = [];
        $departments_search = [];
        $date_start_search = '';
        $date_end_search = '';
        $filterStatus = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    $search = (!empty($data_post['search']) ? $data_post['search'] : '');
                    $staff_search = (!empty($data_post['staff_search']) ? $data_post['staff_search'] : []);
                    $departments_search = (!empty($data_post['departments_search']) ? $data_post['departments_search'] : []);
                    $date_start_search = (!empty($data_post['date_start_search']) ? $data_post['date_start_search'] : '');
                    $date_end_search = (!empty($data_post['date_end_search']) ? $data_post['date_end_search'] : '');
                    $filterStatus = (!empty($data_post['filterStatus']) ? $data_post['filterStatus'] : '');
                }
            }
        }

        $select = '
            tbltasks.id as id,
            tblcategory_tasks.code as task_code,
            tbltasks.name as task_name,
            tbltasks.rel_id,
            recurring,
            tbltasks.id_list_object,
            tbltasks.rel_type as rel_type,
            status,
            tasks.description,
            startdate,
            duedate,
            1 as assignees,        
            (
                SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
                FROM tbltaskstimers 
                WHERE tbltaskstimers.task_id = tbltasks.id
            ) as _minute,
            priority,
            (
                SELECT GROUP_CONCAT(
                    CONCAT(
                        tblproduction_report.id, 
                        "|||", 
                        tblproduction_report.name_report,
                        "|||",
                        tblproduction_report.date
                    ) SEPARATOR ",,,"
                ) 
                FROM tblproduction_report 
                WHERE tblproduction_report.id_tasks = tbltasks.id
            ) as ProductionReport,
            (
                SELECT MAX(id) 
                FROM ' . db_prefix() . 'taskstimers 
                WHERE task_id=' . db_prefix() . 'tasks.id and staff_id=' . $this->staffid . ' and end_time IS NULL
            ) as not_finished_timer_by_current_staff,
            (
				SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
				FROM tbltaskstimers 
				WHERE tbltaskstimers.task_id = tbltasks.id
			) as _minute,
			(SELECT tblcategory_tasks.time FROM tblcategory_tasks WHERE tblcategory_tasks.id = tbltasks.category_tasks) as minute_limit,
			tbltasks.category_tasks';


        $custom_fields = get_table_custom_fields('tasks');
        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            $select .= ' (SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs;
        }

        $this->db->select($select);
        $this->db->from('tbltasks');
        $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbltasks.category_tasks', 'left');
        $this->db->order_by('tbltasks.dateadded', 'desc');
        //        $this->db->where('
        //            CASE WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM tblproject_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1)
        //            THEN rel_type != "project" ELSE 1=1 END');
        if (count($custom_fields) > 4) {
            // Fix for big queries. Some hosting have max_join_limit
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }
        $start = ($page - 1) * $limit;
        $this->db->limit($limit, $start);

        if ($search) {
            $this->db->group_start();
            $this->db->like('tblcategory_tasks.code', $search);
            $this->db->or_like('tbltasks.name', $search);
            $this->db->group_end();
        }

        if ($staff_search) {
            $staffs = '(';
            foreach ($staff_search as $key => $value) {
                $staffs .= $value . ', ';
            }
            $staffs = substr($staffs, 0, -2);
            $staffs .= ')';

            $this->db->where('EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN ' . $staffs . ')');
        }

        if ($departments_search) {
            $departments = '(';
            foreach ($departments_search as $key => $value) {
                $departments .= $value . ', ';
            }
            $departments = substr($departments, 0, -2);
            $departments .= ')';
            $this->db->where('EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN ' . $departments . ')');
        }

        if ($date_start_search) {
            $this->db->where('DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") >= ', to_sql_date($date_start_search));
        }

        if ($date_end_search) {
            $this->db->where('DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <= ', to_sql_date($date_end_search));
            // $whereTotal[] = 'AND DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <= "' . to_sql_date($date_end_search) . '"';
        }
        if ($filterStatus) {
            $this->db->where('tbltasks.status', $filterStatus);
        } else {
            $this->db->where('tbltasks.status != 5');
        }

        //dt
        if ($this->hasPermissionViewOwn && !$this->isAdmin) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->db->where('( tbltasks.id IN ( SELECT taskid FROM tbltask_assigned WHERE  tbltask_assigned.staffid IN (' . $coverStr . ') OR tbltasks.addedfrom IN (' . $coverStr . ')))');
            }
        }
        //end


        $response['results'] = $this->db->get()->result_array();

        // $tasks = $response['results'];
        // $total_tasks = count($tasks);

        foreach ($response['results'] as $key => $task) {
            $response['results'][$key]['name_status'] = '';
            $response['results'][$key]['color_status'] = '';

            if (!empty($this->get_statuses[$task['status']])) {
                $response['results'][$key]['name_status'] = $this->get_statuses[$task['status']]['name'];
                $response['results'][$key]['color_status'] = $this->get_statuses[$task['status']]['color'];
            }


            $related_to_type = '';
            $related_to_name = '';
            if (!empty($task['rel_id'])) {
                $task_rel_data = get_relation_data($task['rel_type'], $task['rel_id']);
                // echo json_encode($task_rel_data);
                $task_rel_value = get_relation_values($task_rel_data, $task['rel_type']);
                if (!empty($task_rel_value['type'])) {
                    $related_to_type = _l('c_tasks_' . $task_rel_value['type']);
                    $related_to_name = $task_rel_value['name'];
                }
            }

            $task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $task['id_list_object']], '', 'row');
            if (!empty($task_rel_value_list)) {
                if (!empty($related_to_name)) {
                    $related_to_name .= ', ';
                }
                $related_to_name .= $task_rel_value_list->name;
            }

            $rowDepartments = [];
            $this->db->select('tbldepartments.*');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
            $departments = $this->db->get_where('tbltask_department', ['task_id' => $task['id']])->result_array();
            if (!empty($departments)) {
                foreach ($departments as $k => $v) {
                    $rowDepartments[] =  $v['name'];
                }
            }

            $this->db->select('tblstaff.staffid, CONCAT(firstname, \' \', lastname) as staff_name, profile_image');
            $this->db->from('tbltask_assigned');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbltask_assigned.staffid');
            $this->db->where('taskid = ' . $task['id']);
            $this->db->order_by('tbltask_assigned.staffid');
            $arrAssignees = $this->db->get()->result_array();
            $assignees = [];
            foreach ($arrAssignees as $k => $v) {
                $arrAssignees[$k]['profile_image'] = staff_profile_image_url($v['staffid']);
                $assignees[] = $arrAssignees[$k];
            }

            $response['results'][$key]['related_to_type'] = (isset($related_to_type) ? $related_to_type : '');
            $response['results'][$key]['related_to_name'] = (isset($related_to_name) ? $related_to_name : '');
            $response['results'][$key]['department'] = (isset($rowDepartments) ? $rowDepartments : []);
            $response['results'][$key]['assignees'] = (isset($assignees) ? $assignees : []);

            $this->db->order_by('id', 'desc');
            $this->db->where('end_time is null', false, false);
            $this->db->where('staff_id', $this->staffid);
            $response['results'][$key]['timer'] = $this->db->get_where('tbltaskstimers', ['task_id' => $task['id']])->row();

            $response['results'][$key]['status_time'] = 'Chưa tính giờ';
            if (empty($task['category_tasks'])) {
                $response['results'][$key]['status_time'] = 'Chưa chọn mã công việc';
            } else if (!empty($task['_minute'])) {
                if ($task['_minute'] > $task['minute_limit']) {
                    $response['results'][$key]['status_time'] = 'Chưa đạt';
                }
                if ($task['_minute'] == $task['minute_limit']) {
                    $response['results'][$key]['status_time'] = 'Đạt';
                } else {
                    $response['results'][$key]['status_time'] = 'Vượt KPI';
                }
            } else {
                $response['results'][$key]['status_time'] = 'Chưa tính giờ';
            }

            if (!empty($task['_minute'])) {
                $response['results'][$key]['total_time'] = $task['_minute'];
            }

            $comment = $this->tasks_model->get_task_comments($task['id']);
            $response['results'][$key]['comment_num'] = count($comment);

			$response['results'][$key]['description'] = c_html_to_text($response['results'][$key]['description']);
			$response['results'][$key]['checklist'] = $this->db->get_where('tbltask_checklist_items', ['taskid' => $task['id']])->result_array();
        }
		{ // next page
            $this->db->select($select);
            $this->db->from('tbltasks');
            $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbltasks.category_tasks', 'left');
            //            $this->db->where('
            //            CASE WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM tblproject_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1)
            //            THEN rel_type != "project" ELSE 1=1 END');
            if (count($custom_fields) > 4) {
                // Fix for big queries. Some hosting have max_join_limit
                @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
            }
            $startNext = ($page) * $limit;
            $this->db->limit(1, $startNext);

            if ($staff_search) {
                $staffs = '(';
                foreach ($staff_search as $key => $value) {
                    $staffs .= $value . ', ';
                }
                $staffs = substr($staffs, 0, -2);
                $staffs .= ')';

                $this->db->where('EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN ' . $staffs . ')');
            }

            if ($departments_search) {
                $departments = '(';
                foreach ($departments_search as $key => $value) {
                    $departments .= $value . ', ';
                }
                $departments = substr($departments, 0, -2);
                $departments .= ')';
                $this->db->where('EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN ' . $departments . ')');
            }

            if ($date_start_search) {
                $this->db->where('DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") >= ', to_sql_date($date_start_search));
            }

            if ($date_end_search) {
                $this->db->where('DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <= ', to_sql_date($date_end_search));
                // $whereTotal[] = 'AND DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <= "' . to_sql_date($date_end_search) . '"';
            }
            if ($filterStatus) {
                $this->db->where('tbltasks.status', $filterStatus);
            } else {
                $this->db->where('tbltasks.status != 5');
            }
            //dt
            if ($this->hasPermissionViewOwn && !$this->isAdmin) {
                if ($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $this->db->where('( tbltasks.id IN ( SELECT taskid FROM tbltask_assigned WHERE  tbltask_assigned.staffid IN (' . $coverStr . ') OR tbltasks.addedfrom IN (' . $coverStr . ')))');
                }
            }
            //end


            $response['next'] = $this->db->get()->num_rows();
        }

        echo json_encode($response);
    }

    public function getTasksDetails($id = '')
    {
        $response['isSuccess'] = true;
        $response['message'] = 'Thành công';

        if (empty($id)) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy truyền ID công việc!';
            echo json_encode($response);
            die;
        }

        $tasks_where = [];
        if (!$this->hasPermissionView && !$this->hasPermissionViewOwn) {
            $tasks_where = get_tasks_where_string(false);
        }
        $task = $this->tasks_model->get($id, $tasks_where);

        $related_to = '';
        $task_rel_value = get_table_where('tbldepartments_tasks', ['id' => $task->id_list_object], '', 'row');
        if (!empty($task_rel_value)) {
            $related_to .= $task_rel_value->name;
        }
        $task_rel_data = get_relation_data($task->rel_type, $task->rel_id);
        $task_rel_value = get_relation_values($task_rel_data, $task->rel_type);
        if (!empty($task_rel_value['name'])) {
            // if (!empty($related_to)) {
            //     $related_to .= ', ';
            // }
            // if ($task->rel_type == "orders") {
            //     $related_to .= '<a data-tnh="modal" class="tnh-modal2" data-toggle="modal" data-target="#myModal2" href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
            // } else {
            // $related_to .= '<a href="' . $task_rel_value['link'] . '" target="_blank">' . $task_rel_value['name'] . '</a>';
            $related_to .= ', ' . $task_rel_value['name'];
            // }
        }
        $task->related_to = $related_to;

        if (!$task) {
            $response['isSuccess'] = false;
            $response['message'] = 'Không có công việc này';
            echo json_encode($response);
            die;
        }
        foreach ($task->assignees as $key => $value) {
            $task->assignees[$key]['avatar'] = staff_profile_image_url($value['assigneeid']);
        }
        foreach ($task->followers as $key => $value) {
            $task->followers[$key]['avatar'] = staff_profile_image_url($value['followerid']);
        }
        foreach ($task->attachments as $key => $value) {
            $task->attachments[$key]['staff_full_name'] = get_staff_full_name($value['staffid']);
            $path = get_upload_path_by_type('task') . $task->id . '/' . $value['file_name'];
            $task->attachments[$key]['file'] = site_url(protected_file_url_by_path($path, true));
        }
        foreach ($task->comments as $key => $value) {
			$task->comments[$key]['content'] = c_html_to_text($value['content']);

            foreach ($value['attachments'] as $k => $v) {
                $path = get_upload_path_by_type('task') . $task->id . '/' . $v['file_name'];
                $task->comments[$key]['attachments'][$k]['file'] = site_url(protected_file_url_by_path($path, true));
            }
        }
        foreach ($task->checklist_items as $key => $value) {
            $task->checklist_items[$key]['completed_by'] = '';
            if ($value['finished'] == 1 || $value['addedfrom'] != $this->staffid) {
                // if ($value['addedfrom'] != $this->staffid) {
                $task->checklist_items[$key]['completed_by'] .= _l('task_created_by', get_staff_full_name($value['addedfrom']));
                // }
                if (/*$value['addedfrom'] != $this->staffid &&*/$value['finished'] == 1) {
                    $task->checklist_items[$key]['completed_by'] .= ' - ';
                    // }
                    // if ($value['finished'] == 1) {
                    $task->checklist_items[$key]['completed_by'] .= _l('task_checklist_item_completed_by', get_staff_full_name($value['finished_from']));
                }
            }
        }

        $task->create_by = ($task->is_added_from_contact == 0 ? get_staff_full_name($task->addedfrom) : get_contact_full_name($task->addedfrom));

		$task->create_by_image = '';
		if(!empty($task->addedfrom)) {
			$task->create_by_image = staff_profile_image_url($task->addedfrom);
		}

        $this->db->order_by('id', 'desc');
        $this->db->where('end_time is null', false, false);
        $this->db->where('staff_id', $this->staffid);
        $task->timer = $this->db->get_where('tbltaskstimers', ['task_id' => $task->id])->row();
        $this->db->select('tbldepartments.name');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
        $task->departments = $this->db->get_where('tbltask_department', ['task_id' => $task->id])->result_array();
        $task->category_tasks = get_table_where('tblcategory_tasks', ['id' => $task->category_tasks], '', 'row');
        //        $task->status = format_task_status($task->status, true, true);


        if (!empty($this->get_statuses[$task->status])) {
            $task->name_status = $this->get_statuses[$task->status]['name'];
            $task->color_status = $this->get_statuses[$task->status]['color'];
        }

        foreach (get_tasks_priorities() as $priority) {
            if ($task->priority == $priority['id']) {
                $task->priority = $priority['name'];
                break;
            }
        }
		if($task->custom_recurring == 2) {
			$this->db->select('GROUP_CONCAT(day) as list_day');
			$this->db->where('taskid', $task->id);
			$tasks_repeat = $this->db->get('tbltasks_repeat_day')->row('list_day');
			if(!empty($tasks_repeat)) {
				$task->custom_day = explode(',', $tasks_repeat);
			}
		}

        $task->description = c_html_to_text($task->description);
        $response['results']['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
        $response['results']['task'] = $task;
        // $response['results']['staff'] = $this->staff_model->get('', ['active' => 1]);
        $response['results']['reminders'] = $this->tasks_model->get_reminders($id);
        // $response['results']['staff_reminders'] = $this->tasks_model->get_staff_members_that_can_access_task($id);
        $response['results']['project_deadline'] = null;
        if ($task->rel_type == 'project') {
            $response['results']['project_deadline'] = get_project_deadline($task->rel_id);
        }

        echo json_encode($response);
    }

    public function addTask()
    {
        $response['isSuccess'] = true;
        $response['message'] = 'Thành công';

        if (!$this->hasPermissionCreate) {
            $response['isSuccess'] = false;
            $response['message'] = 'Tài khoản không có quyền tạo';
            echo $response;
            die;
        }

        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }

        if (empty($data['name'])) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy nhập tiêu đề phân công!';
            echo json_encode($response);
            die;
        }
        if (empty($data['startdate'])) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy nhập ngày bắt đầu!';
            echo json_encode($response);
            die;
        }
        if (empty($data['duedate'])) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy nhập hạn chót!';
            echo json_encode($response);
            die;
        }

        $dataInsert = [];
        $dataInsert['billable'] = 'on';
        $dataInsert['hourly_rate'] = '0';
        $dataInsert['shift_work'] = '';
        $dataInsert['quantity_shift_work'] = '0';
        $dataInsert['tags'] = '';

        $dataInsert['name'] = $data['name'];
        $dataInsert['category_tasks'] = (!empty($data['category_tasks']) ? $data['category_tasks'] : '');
        $dataInsert['startdate'] = to_sql_date($data['startdate']);
        $dataInsert['duedate'] = to_sql_date($data['duedate'], true);
        $dataInsert['priority'] = (!empty($data['priority']) ? $data['priority'] : '');
        $dataInsert['department_id'] = (!empty($data['department_id']) ? $data['department_id'] : []);
        $dataInsert['repeat_every'] = (!empty($data['repeat_every']) ? $data['repeat_every'] : '');
        $dataInsert['repeat_every_custom'] = (!empty($data['repeat_every_custom']) ? $data['repeat_every_custom'] : '');
        $dataInsert['repeat_type_custom'] = (!empty($data['repeat_type_custom']) ? $data['repeat_type_custom'] : '');
        $dataInsert['cycles'] = (!empty($data['cycles']) ? $data['cycles'] : (($data['cycles'] == 0) ? '0' : ''));
        //		if(!empty($data['rel_type'])) {
        //			$dataInsert['rel_type'] = (!empty($data['rel_type']) ? $data['rel_type'] : '');
        //		}
        //
        //		if(!empty($data['rel_id'])) {
        //			$dataInsert['rel_id'] = (!empty($data['rel_id']) ? $data['rel_id'] : '');
        //		}
        $dataInsert['id_list_object'] = (!empty($data['id_list_object']) ? $data['id_list_object'] : NULL);
        $dataInsert['description'] = (!empty($data['description']) ? $data['description'] : '');
        $data['_addedfrom'] = $this->staffid;
        unset($data['rel_id']);
        unset($data['rel_type']);

        $id = $this->tasks_model->add($data);
        if (empty($id)) {
            $response['isSuccess'] = false;
            $response['message'] = 'Thêm thất bại';
        }
        echo json_encode($response);
    }

    public function editTask($id = '')
    {
        $response['isSuccess'] = true;
        $response['message'] = 'Thành công';

        if (!$this->hasPermissionEdit) {
            $response['isSuccess'] = false;
            $response['message'] = 'Tài khoản không có quyền sửa';
            echo $response;
            die;
        }

        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }

        if (empty($data['name'])) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy nhập tiêu đề phân công!';
            echo json_encode($response);
            die;
        }
        if (empty($data['startdate'])) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy nhập ngày bắt đầu!';
            echo json_encode($response);
            die;
        }
        if (empty($data['duedate'])) {
            $response['isSuccess'] = false;
            $response['message'] = 'Hãy nhập hạn chót!';
            echo json_encode($response);
            die;
        }
        unset($data['rel_id']);
        unset($data['rel_type']);

        $success = $this->tasks_model->update($data, $id);
        if (empty($success)) {
            $response['isSuccess'] = false;
            $response['message'] = 'Sửa thất bại';
        }
        echo json_encode($response);
    }

    public function filterDepartments()
    {
        $search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    $search = (!empty($data_post['search']) ? $data_post['search'] : '');
                }
            }
        }

        $this->db->select('departmentid as id, name');
        if ($search) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->group_end();
        }
        $data['results'] = $this->db->get('tbldepartments')->result_array();
        echo json_encode($data);
    }

    public function filterStaffs()
    {
        $search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    $search = (!empty($data_post['search']) ? $data_post['search'] : '');
                }
            }
        }

        $this->db->select('staffid as id, CONCAT(firstname, " ", lastname) as name');
        if ($search) {
            $this->db->group_start();
            $this->db->like('CONCAT(firstname, " ", lastname)', $search);
            $this->db->group_end();
        }
        $data['results'] = $this->db->get_where('tblstaff')->result_array();
        foreach ($data['results'] as $key => $value) {
            $data['results'][$key]['name'] = trim($value['name']);
        }
        echo json_encode($data);
    }

    public function getRelation()
    {
        $response['results'] = [];
        $response['results'][] = array('type' => 'customer', 'name' => _l('client'));
        $response['results'][] = array('type' => 'supplier', 'name' => _l('Nhà cung cấp'));
        $response['results'][] = array('type' => 'products', 'name' => _l('Thành phẩm'));
        $response['results'][] = array('type' => 'materials', 'name' => _l('Nguyên vật liệu phẩm'));
        $response['results'][] = array('type' => 'quotes', 'name' => _l('Báo giá'));
        $response['results'][] = array('type' => 'orders', 'name' => _l('Đơn đặt hàng bán'));
        $response['results'][] = array('type' => 'import', 'name' => _l('Nhập kho'));
        $response['results'][] = array('type' => 'order_production_details', 'name' => _l('order_production_details'));
        $response['results'][] = array('type' => 'production_report', 'name' => _l('Phiếu báo cáo'));
        $response['results'][] = array('type' => 'maintenance_ticket', 'name' => _l('Phiếu bảo trì'));
        $response['results'][] = array('type' => 'template', 'name' => _l('Mẫu'));
        $response['results'][] = array('type' => 'khun', 'name' => _l('Khuân'));
        $response['results'][] = array('type' => 'KHTH', 'name' => _l('KHTH'));
        $response['results'][] = array('type' => 'warehouse', 'name' => _l('Kho'));
        $response['results'][] = array('type' => 'releases', 'name' => _l('Giao hàng'));
        $response['results'][] = array('type' => 'QA', 'name' => _l('QA'));
        $response['results'][] = array('type' => 'HCNS', 'name' => _l('HCNS'));
        $response['results'][] = array('type' => 'TCKT', 'name' => _l('TCKT'));
        $response['results'][] = array('type' => 'procurement_supply', 'name' => _l('Cung ứng thu mua'));
        $response['results'][] = array('type' => 'COO', 'name' => _l('COO'));
        echo json_encode($response);
        die;
    }

    public function getRelationData()
    {
        $data_post = file_get_contents('php://input');
        if (!empty($data_post) && !is_array($data_post)) {
            $data_post = json_decode($data_post, true);
            $type = (!empty($data_post['type']) ? $data_post['type'] : '');
            $type_items = (!empty($data_post['type_items']) ? $data_post['type_items'] : '');
            $id_order = (!empty($data_post['id_order']) ? $data_post['id_order'] : '');
            $search = (!empty($data_post['search']) ? $data_post['search'] : '');

            if ($type == 'customer' || $type == 'customers') {
                if (!empty($search)) {
                    $where = array('company_short LIKE' => '%' . $search . '%');
                }
                $data = $this->clients_model->get('', $where);

                foreach ($data as $relation) {
                    $response['results'][] = get_relation_values($relation, $type);
                }
            } else {
                if (!empty($id_order)) {
                    $data = get_relation_data_hau($type, '', $type_items, $id_order);
                } else {
                    $data = $this->get_relation_data($type, '', $type_items, $search);
                }
                $rel_id = (!empty($data_post['rel_id']) ? $data_post['rel_id'] : '');
                $response['results'] = init_relation_options($data, $type, $rel_id);
            }

            echo json_encode($response);
            die;
        }
    }

    public function getCategoryTasks()
    {
        $search = '';
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['search'])) {
                    $search = $data_post['search'];
                }
            }
        }

		if(!is_admin($this->staffid)) {
			$staffNow = $this->staffid;
			$this->db->select('GROUP_CONCAT(departmentid) as list_departments');
			$this->db->where('tblstaff_departments.staffid', $staffNow);
			$staff_departments = $this->db->get('tblstaff_departments')->row('list_departments');
			if(!empty($staff_departments)) {
				$staff_departments = explode(',', $staff_departments);
				$this->db->group_start();
				foreach ($staff_departments as $key => $value) {
					$this->db->or_where('FIND_IN_SET('.$value.', tblcategory_tasks.departments)');
				}
				$this->db->group_end();
			}
			else {
				$this->db->where('id', 0);
			}
		}


        $this->db->select('*');
        $this->db->from('tblcategory_tasks');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tblcategory_tasks.code', $search);
            $this->db->or_like('tblcategory_tasks.content', $search);
            $this->db->group_end();
        }
        $response['results'] = $this->db->get()->result_array();
        echo json_encode($response);
    }

    public function getTasksPriorities()
    {
        $response['results'] = get_tasks_priorities();
        echo json_encode($response);
    }

    // helper
    function get_relation_data($type, $rel_id = '', $type_items = '', $q = '')
    {
        $CI = &get_instance();
        $q = trim($q);
        $data = [];
        if ($type == 'customer' || $type == 'customers') {
            $where_clients = '';
            if ($q) {
                $where_clients .= '(company LIKE "%' . $q . '%" OR company_short LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%") AND ' . db_prefix() . 'clients.active = 1';
            }
            $data = $CI->clients_model->get($rel_id, $where_clients);
        } elseif ($type == 'contact' || $type == 'contacts') {
            if ($rel_id != '') {
                $data = $CI->clients_model->get_contact($rel_id);
            } else {
                $where_contacts = db_prefix() . 'contacts.active=1';
                if ($CI->input->post('tickets_contacts')) {
                    if (!has_permission('customers', '', 'view') && get_option('staff_members_open_tickets_to_all_contacts') == 0) {
                        $where_contacts .= ' AND ' . db_prefix() . 'contacts.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
                    }
                }
                if ($CI->input->post('contact_userid')) {
                    $where_contacts .= ' AND ' . db_prefix() . 'contacts.userid=' . $CI->input->post('contact_userid');
                }
                $search = $CI->misc_model->_search_contacts($q, 0, $where_contacts);
                $data = $search['result'];
            }
        } elseif ($type == 'invoice') {
            if ($rel_id != '') {
                $CI->load->model('invoices_model');
                $data = $CI->invoices_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_invoices($q);
                $data = $search['result'];
            }
        } elseif ($type == 'orders') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tbl_orders')->row();
            } else {
                $q = trim($q);
                $CI->db->like('reference_no', $q);
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultOrders = $CI->db->get('tbl_orders')->result_array();
                //		    $result = [
                //			    'result'         => $resultOrders,
                //			    'type'           => 'orders',
                //			    'search_heading' => _l('items'),
                //		    ];
                $data = $resultOrders;
            }
        } elseif ($type == 'items') {
            if ($rel_id != '') {
                $CI->load->model('invoice_items_model');
                $data = $CI->invoice_items_model->get_full_item($rel_id, $type_items);
            } else {
                $search = $CI->misc_model->_search_items($q, $type_items);
                $data = $search['result'];
            }
        } elseif ($type == 'purchases') {
            if ($rel_id != '') {
                $CI->load->model('purchases_model');
                $data = $CI->purchases_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_purchases($q, $type_items);
                $data = $search['result'];
            }
        } elseif ($type == 'rfq') {
            $search = $CI->misc_model->_search_rfq($q, $type_items);
            $data = $search['result'];
        } elseif ($type == 'supplier_quotes') {
            $search = $CI->misc_model->_search_supplier_quotes($q, $type_items);
            $data = $search['result'];
        } elseif ($type == 'purchase_order') {
            $search = $CI->misc_model->_search_purchase_order($q, $type_items);
            $data = $search['result'];
        }
        //	elseif ($type == 'import') {
        //		$search = $CI->misc_model->_search_import($q, $type_items);
        //		$data = $search['result'];
        //	}
        elseif ($type == 'return_supplier') {
            $search = $CI->misc_model->_return_supplier($q, $type_items);
            $data = $search['result'];
        } elseif ($type == 'color') {
            if ($rel_id != '') {
                $CI->load->model('invoice_items_model');
                $data = $CI->invoice_items_model->color($rel_id);
            } else {
                $search = $CI->misc_model->_search_color($q);
                $data = $search['result'];
            }
        } elseif ($type == 'credit_note') {
            if ($rel_id != '') {
                $CI->load->model('credit_notes_model');
                $data = $CI->credit_notes_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_credit_notes($q);
                $data = $search['result'];
            }
        } elseif ($type == 'estimate') {
            if ($rel_id != '') {
                $CI->load->model('estimates_model');
                $data = $CI->estimates_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_estimates($q);
                $data = $search['result'];
            }
        } elseif ($type == 'contract' || $type == 'contracts') {
            $CI->load->model('contracts_model');
            if ($rel_id != '') {
                $CI->load->model('contracts_model');
                $data = $CI->contracts_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_contracts($q);
                $data = $search['result'];
            }
        } elseif ($type == 'ticket') {
            if ($rel_id != '') {
                $CI->load->model('tickets_model');
                $data = $CI->tickets_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_tickets($q);
                $data = $search['result'];
            }
        } elseif ($type == 'expense' || $type == 'expenses') {
            if ($rel_id != '') {
                $CI->load->model('expenses_model');
                $data = $CI->expenses_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_expenses($q);
                $data = $search['result'];
            }
        } elseif ($type == 'lead' || $type == 'leads') {
            if ($rel_id != '') {
                $CI->load->model('leads_model');
                $data = $CI->leads_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_leads($q, 0, [
                    'junk' => 0,
                ]);
                $data = $search['result'];
            }
        } elseif ($type == 'proposal') {
            if ($rel_id != '') {
                $CI->load->model('proposals_model');
                $data = $CI->proposals_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_proposals($q);
                $data = $search['result'];
            }
        } elseif ($type == 'project') {
            if ($rel_id != '') {
                $CI->load->model('projects_model');
                $data = $CI->projects_model->get($rel_id);
            } else {
                $where_projects = '';
                if ($CI->input->post('customer_id')) {
                    $where_projects .= 'clientid=' . $CI->input->post('customer_id');
                }
                $search = $CI->misc_model->_search_projects($q, 0, $where_projects);
                $data = $search['result'];
            }
        } elseif ($type == 'staff') {
            if ($rel_id != '') {
                $CI->load->model('staff_model');
                $data = $CI->staff_model->get($rel_id);
            } else {
                $search = $CI->misc_model->_search_staff($q);
                $data = $search['result'];
            }
        } elseif ($type == 'tasks' || $type == 'task') {
            // Tasks only have relation with custom fields when searching on top
            if ($rel_id != '') {
                $data = $CI->tasks_model->get($rel_id);
            }
        } else if ($type == 'order_production_details') {
            //task tnh
            if ($rel_id != '') {
                $data = $CI->site_model->getProductionsOD($rel_id);
            } else {
                if (!empty($_POST['limit_search'])) {
                    $data = $CI->site_model->searchProductionsOD($q, $_POST['limit_search']);
                } else {
                    $data = $CI->site_model->searchProductionsOD($q);
                }
            }
        } elseif ($type == 'supplier') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tblsuppliers')->row();
            } else {
                $q = trim($q);
                $CI->db->like('company', $q);
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultSuppliers = $CI->db->get('tblsuppliers')->result_array();
                $data = $resultSuppliers;
            }
        } elseif ($type == 'import') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tblimport')->row();
            } else {
                $q = trim($q);
                $CI->db->like('CONCAT(prefix, "-", code)', $q);
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultImport = $CI->db->get('tblimport')->result_array();
                $data = $resultImport;
            }
        } elseif ($type == 'production_report') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tblproduction_report')->row();
            } else {
                $q = trim($q);
                $CI->db->like('name_report', $q);
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultImport = $CI->db->get('tblproduction_report')->result_array();
                $data = $resultImport;
            }
        } elseif ($type == 'maintenance_ticket') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tblmaintenance_ticket')->row();
            } else {
                $q = trim($q);
                $CI->db->like('name', $q);
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultImport = $CI->db->get('tblmaintenance_ticket')->result_array();
                $data = $resultImport;
            }
        } elseif ($type == 'quotes') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tbl_quotes')->row();
            } else {
                $q = trim($q);
                $CI->db->like('reference_no', $q);
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultImport = $CI->db->get('tbl_quotes')->result_array();
                $data = $resultImport;
            }
        } elseif ($type == 'products') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tbl_products')->row();
            } else {
                $q = trim($q);
                $CI->db->group_start();
                $CI->db->like('code', $q);
                $CI->db->or_like('name', $q);
                $CI->db->group_end();
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }

                $CI->db->where('type_products', 'products');
                $resultImport = $CI->db->get('tbl_products')->result_array();
                $data = $resultImport;
            }
        } elseif ($type == 'materials') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tbl_materials')->row();
            } else {
                $q = trim($q);
                $CI->db->group_start();
                $CI->db->like('code', $q);
                $CI->db->or_like('name', $q);
                $CI->db->group_end();
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultImport = $CI->db->get('tbl_materials')->result_array();
                $data = $resultImport;
            }
        } elseif ($type == 'releases') {
            if ($rel_id != '') {
                $CI->db->where('id', $rel_id);
                $data = $CI->db->get('tbl_deliveries')->row();
            } else {
                $q = trim($q);
                $CI->db->group_start();
                $CI->db->like('reference_no', $q);
                $CI->db->group_end();
                if (!empty($_POST['limit_search'])) {
                    $CI->db->limit($_POST['limit_search']);
                }
                $resultImport = $CI->db->get('tbl_deliveries')->result_array();
                $data = $resultImport;
            }
        }
        return $data;
    }


    public function add_assgined_task()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }

        $assignData = [
            'taskid' => $data['taskid'],
            'staffid' => $data['assignee'],
            'assigned_from' => $this->staffid,
        ];
        $this->db->insert('tbltask_assigned', $assignData);
        $assigneeId = $this->db->insert_id();
        if (!empty($assigneeId)) {
            $taskid = $data['taskid'];
            $rowTask = $this->site_model->rowTasks($taskid);
            $staff_create = get_staff_full_name($this->staffid);

            $task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
            $task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);


            if (!empty($task_rel_value['type'])) {
                $rowName = '<span style="color:black;font-weight:bold">Liên quan đến ' . _l('c_tasks_' . $task_rel_value['type']) . '</span>: <a target="_blank href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
            } else {
                $rowName = '';
            }
            $dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa phân công công vệc ' . $rowName . '</b> vào ngày ' . _dhau($rowTask['startdate']) . ' Vui lòng theo dõi và tiến hành cập nhật công việc !
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
            notificationTaskAssigned($taskid, $this->staffid, $data['assignee']);

            echo json_encode([
                'result' => true
            ]);
            die();
        }
        echo json_encode([
            'success' => false
        ]);
        die();
    }

    public function remove_assgined_task($id = "")
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }


        $this->db->where('id', $id);
        $task_assigned = $this->db->get('tbltask_assigned')->row();

        $this->db->where('id', $id);
        $delete = $this->db->delete('tbltask_assigned');
        if (!empty($delete)) {
            //			$taskid = $task_assigned->taskid;
            //
            //			$rowTask = $this->site_model->rowTasks($taskid);
            //			$staff_create = get_staff_full_name($this->staffid);
            //
            //			$task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
            //			$task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);
            //
            //
            //			if(!empty($task_rel_value['type'])) {
            //				$rowName = '<span style="color:black;font-weight:bold">Liên quan đến ' . _l('c_tasks_' . $task_rel_value['type']) . '</span>: <a target="_blank href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
            //			}
            //			else {
            //				$rowName = '';
            //			}
            //			$dataHtml = '
            //                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '">
            //                        Hệ thống - ' . $staff_create . ' Vừa xóa bạn khỏi phân công công vệc '. $rowName . '</b> vào ngày ' . _dhau($rowTask['startdate']) . '
            //                    ';
            //			$notification_data = [
            //				'date' => date('Y-m-d H:i:s'),
            //				'description' => $dataHtml,
            //				'touserid' => $task_assigned->assignee,
            //				'link' => '#taskid=' . $taskid,
            //				'type' => 6,
            //				'object_id' => $taskid,
            //				'object_type' => 'tasks',
            //				'onclick' => 'init_task_modal(' . $taskid . '); return false;',
            //			];
            //			if (!empty($notification_data)) {
            //				$this->db->insert('tblnotifications', $notification_data);
            //				pusher_trigger_notification($notification_data);
            //			}
            //			notificationTaskAssigned($taskid, $this->staffid, $task_assigned->assignee);

            echo json_encode([
                'result' => true
            ]);
            die();
        }
        echo json_encode([
            'success' => false
        ]);
        die();
    }

    public function add_task_comment()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }
        if (empty($data)) {
            $data = $this->input->post();
        }

        $comment_id = false;
        if ($data['content'] != '' || (isset($_FILES['file']['name']) && is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
            //			$comment_id = $this->tasks_model->add_task_comment($data);

            $this->db->insert(db_prefix() . 'task_comments', [
                'taskid' => $data['taskid'],
                'content' => $data['content'],
                'staffid' => $this->staffid,
                'contact_id' => 0,
                'dateadded' => date('Y-m-d H:i:s'),
            ]);
            $comment_id = $this->db->insert_id();

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
        if (!empty($comment_id)) {
            $taskData = $this->site_model->rowTasks($data['taskid']);

            $this->db->where('taskid', $data['taskid']);
            $this->db->where('staffid != "' . $this->staffid . '"');
            $assignedTasks = $this->db->get('tbltask_assigned')->result_array();
            foreach ($assignedTasks as $k => $val) {
                $dataHtml = '
						<img src="' . base_url('uploads/company/logo.jpg') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
						Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa bình luận <span style="color:black;font-weight:bold">Công Việc</span> của bạn đang phụ trách vào lúc ' . _dt(date('Y-m-d H:i:s'));
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
                    'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa bình luận công việc của bạn đang phụ trách vào lúc ' . _dt(date('Y-m-d H:i:s')),
                    'title' => 'Bình luận công việc',
                    'code' => $taskData['name'],
                    'object_type' => 'tasks'
                ], [$val['staffid']], $this->staffid);
            }
        }
        //
        echo json_encode([
            'result' => $comment_id ? true : false,
        ]);
    }


    public function remove_task_comment($id = '')
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }

        $delete = false;
        //		$this->db->where('id', $id);
        //		$task_comments = $this->db->get('tbltask_comments')->row();

        $this->db->where('id', $id);
        $delete = $this->db->delete('tbltask_comments');
        //
        //		if (!empty($delete)) {
        //			$taskData = $this->site_model->rowTasks($task_comments->taskid);
        //
        //			$this->db->where('taskid', $task_comments->taskid);
        //			$this->db->where('staffid != "'.$this->staffid.'"');
        //			$assignedTasks = $this->db->get('tbltask_assigned')->result_array();
        //			foreach ($assignedTasks as $k => $val) {
        //				$dataHtml = '
        //						<img src="' . base_url('uploads/company/logo.jpg') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
        //						Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa bình luận <span style="color:black;font-weight:bold">Công Việc</span> của bạn đang phụ trách vào lúc ' . _dt(date('Y-m-d H:i:s'));
        //				$notification_data = [
        //					'date' => date('Y-m-d H:i:s'),
        //					'description' => $dataHtml,
        //					'touserid' => $val['staffid'],
        //					'link' => 'tasks/get_task_data/' . $task_comments->taskid,
        //					'type' => 6,
        //					'object_id' => $task_comments->taskid,
        //					'object_type' => 'tasks',
        //					'onclick' => 'init_task_modal(' . $task_comments->taskid . '); return false;',
        //				];
        //				$this->db->insert('tblnotifications', $notification_data);
        //
        //
        //				send_notification_app_c($taskData['id'], [
        //					'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa bình luận công việc của bạn đang phụ trách vào lúc ' . _dt(date('Y-m-d H:i:s')),
        //					'title' => 'Bình luận công việc',
        //					'code' => $taskData['name'],
        //					'object_type' => 'tasks'
        //				], [$val['staffid']], $this->staffid);
        //			}
        //		}
        //
        echo json_encode([
            'result' => $delete ? true : false,
        ]);
    }


    public function add_followers_task()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['data'])) {
                    $data = $data_post['data'];
                }
            }
        }

        $assignData = [
            'taskid' => $data['taskid'],
            'staffid' => $data['follower']
        ];
        $this->db->insert('tbltask_followers', $assignData);
        $assigneeId = $this->db->insert_id();
        if (!empty($assigneeId)) {
            $taskid = $data['taskid'];
            $rowTask = $this->site_model->rowTasks($taskid);
            $staff_create = get_staff_full_name($this->staffid);

            $task_rel_data = get_relation_data($rowTask['rel_type'], $rowTask['rel_id']);
            $task_rel_value = get_relation_values($task_rel_data, $rowTask['rel_type']);


            if (!empty($task_rel_value['type'])) {
                $rowName = '<span style="color:black;font-weight:bold">Liên quan đến ' . _l('c_tasks_' . $task_rel_value['type']) . '</span>: <a target="_blank href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
            } else {
                $rowName = '';
            }
            $dataHtml = '
                        <img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
                        Hệ thống - ' . $staff_create . ' Vừa thêm bạn vào người theo dõi công việc ' . $rowName . '</b> vào ngày ' . _dhau($rowTask['startdate']) . ' Vui lòng theo dõi và tiến hành cập nhật công việc !
                    ';
            $notification_data = [
                'date' => date('Y-m-d H:i:s'),
                'description' => $dataHtml,
                'touserid' => $data['follower'],
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
                'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa thêm bạn vào người theo dõi công việc vào lúc ' . _dt(date('Y-m-d H:i:s')),
                'title' => 'Theo dõi công việc công việc',
                'code' => $rowTask['name'],
                'object_type' => 'tasks'
            ], [$data['follower']], $this->staffid);

            echo json_encode([
                'result' => true
            ]);
            die();
        }
        echo json_encode([
            'result' => false
        ]);
        die();
    }

    public function delete_followers_task($id = '')
    {
        $this->db->where('id', $id);
        $delete = $this->db->delete('tbltask_followers');
        if (!empty($delete)) {
            echo json_encode([
                'result' => true
            ]);
            die();
        }
        echo json_encode([
            'result' => false
        ]);
        die();
    }


    public function delete_task($id = '')
    {
        if (!has_permission('tasks', $this->staffid, 'delete')) {
            echo json_encode([
                'success' => false,
                'message' => 'Tài khoản không có quyền xóa công việc'
            ]);
            die();
        }
        $success = $this->tasks_model->delete_task($id);
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Xóa công việc thành công'
            ]);
            die();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Xóa công việc không thành công'
            ]);
            die();
        }
    }


    public function timer_tracking($task_id = '', $timer_id = '')
    {
        $success = $this->tasks_model->timer_tracking_api($task_id, $timer_id, '', is_admin($this->staffid), $this->staffid);
        if ($success) {
            echo json_encode([
                'success' => true
            ]);
            die();
        } else {
            echo json_encode([
                'success' => false
            ]);
            die();
        }
    }

    public function get_dashboard()
    {
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['fieldStatus'])) {
                    $fieldStatus = $data_post['fieldStatus'];
                }
                if (!empty($data_post['date_start'])) {
                    $date_start = $data_post['date_start'];
                }
                if (!empty($data_post['date_end'])) {
                    $date_end = $data_post['date_end'];
                }
            }
        }

        $_minute = '(
					SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
					FROM tbltaskstimers 
					WHERE tbltaskstimers.task_id = tbltasks.id
				)';
        $minute_limit = '(SELECT tblcategory_tasks.time FROM tblcategory_tasks WHERE tblcategory_tasks.id = tbltasks.category_tasks)';
        $departments = $this->db->get('tbldepartments')->result_array();
        foreach ($departments as $key => $value) {
            //			$this->db->select('COUNT(*) as count, tbltasks.status');
            $this->db->select([
                'COUNT(*) as count',
                'tbltasks.status'
            ]);
            if (!empty($fieldStatus)) {
                if ($fieldStatus == 1) {
                    $this->db->where($_minute . ' > ' . $minute_limit, false, false);
                } else if ($fieldStatus == 2) {
                    $this->db->where($_minute . ' == ' . $minute_limit, false, false);
                } else if ($fieldStatus == 3) {
                    $this->db->where($_minute . ' < ' . $minute_limit, false, false);
                } else if ($fieldStatus == 4) {
                    $this->db->where($_minute . ' IS NULL', false, false);
                }
            }
            if (!empty($date_start)) {
                $this->db->where('DATE_FORMAT(startdate, "%Y-%m-%d") >= "' . to_sql_date($date_start) . '"', false, false);
            }
            if (!empty($date_end)) {
                $this->db->where('DATE_FORMAT(startdate, "%Y-%m-%d") <= "' . to_sql_date($date_end) . '"', false, false);
            }


            $this->db->join('tbltask_department', 'tbltask_department.task_id = tbltasks.id');
            $this->db->where('tbltask_department.department_id', $value['departmentid']);
            $this->db->group_by('tbltasks.status');
            $departments[$key]['count'] = $this->db->get('tbltasks')->result_array();
        }

        echo json_encode([
            'data' => $departments,
            'status' => $this->tasks_model->get_statuses()
        ]);
        die();
    }

    public function edit_task_checklist_items($id = '')
    {
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['description'])) {
                    $description = $data_post['description'];
                }
            }
        }

        $this->db->where('id', $id);
        $success = $this->db->update('tbltask_checklist_items', ['description' => $description]);
        if (!empty($success)) {
            echo json_encode(['result' => true]);
            die();
        }
        echo json_encode(['result' => false]);
        die();
    }

    public function add_task_checklist_items($id_task = '')
    {
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['description'])) {
                    $description = $data_post['description'];
                }
            }
        }
        $this->db->order_by('list_order', 'desc');
        $list_order = $this->db->get_where('tbltask_checklist_items', ['taskid' => $id_task])->row('list_order');
        $list_order = !empty($list_order) ? ($list_order + 1) : '1';

        $success = $this->db->insert('tbltask_checklist_items', [
            'taskid' => $id_task,
            'description' => $description,
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => $this->staffid,
            'list_order' => $list_order
        ]);
        if (!empty($success)) {
            echo json_encode(['result' => true]);
            die();
        }
        echo json_encode(['result' => false]);
        die();
    }

    public function delete_task_checklist_items($id = '')
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
            $success = $this->db->delete('tbltask_checklist_items');
            if (!empty($success)) {
                echo json_encode(['result' => true]);
                die();
            }
        }
        echo json_encode(['result' => false]);
        die();
    }

    public function check_task_checklist_items($id = '', $finished = 0)
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
            $success = $this->db->update('tbltask_checklist_items', ['finished' => $finished]);
            if (!empty($success)) {
				$this->db->where('id', $id);
				$taskid = $this->db->get('tbltask_checklist_items')->row('taskid');
				if(!empty($taskid)) {
					$this->db->where('taskid', $taskid);
					$this->db->where('finished', 0);
					$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
					if (empty($ktChecklist)) {
						$this->tasks_model->mark_as(5, $taskid);
					}
				}

                echo json_encode(['result' => true]);
                die();
            }
        }
        echo json_encode(['result' => false]);
        die();
    }



    public function getListStaff()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['search'])) {
                    $search = $data_post['search'];
                }
            }
        }


        $this->db->select('staffid, CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as fullname');
        if (!empty($search)) {
            $this->db->like('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""))', $search);
        }
        $staff = $this->db->get_where('tblstaff')->result_array();
        foreach ($staff as $key => $value) {
            $staff[$key]['profile_image'] = staff_profile_image_url($value['staffid']);
        }
        echo json_encode($staff);
        die();
    }


    public function get_departments_tasks()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['search'])) {
                    $search = $data_post['search'];
                }
            }
        }

        $this->db->select('tbldepartments_tasks.*');
        if (!empty($search)) {
            $this->db->like('tbldepartments_tasks.NAME', $search);
        }
        $departments_tasks = $this->db->get_where('tbldepartments_tasks')->result_array();
        echo json_encode($departments_tasks);
        die();
    }

    public function getStatus()
    {
        echo json_encode([
            'data' => $this->get_statuses
        ]);
        die();
    }

    public function updateStatus($id_tasks = "", $status = '0')
    {
		if($status == 5) {
			$this->db->where('taskid', $id_tasks);
			$this->db->where('finished', 0);
			$ktChecklist = $this->db->get('tbltask_checklist_items')->row();
			if(!empty($ktChecklist)) {
				echo json_encode([
					'result' => false,
					'message' => 'Vui lòng hoàn thành hết các bước quy trình mới được hoàn thành',
				]);die();
			}
		}

        $isSuccess = $this->tasks_model->mark_as($status, $id_tasks);
        echo json_encode(['result' => $isSuccess]);
        die();
    }

    public function get_count_tasks()
    {
        $get_statuses = [];
        foreach ($this->get_statuses as $key => $value) {
            $this->db->where('tbltasks.status', $value['id']);
            $get_statuses[$value['id']] = $this->db->get('tbltasks')->num_rows();
        }
        $this->db->where('status != 5', false, false);
        $get_statuses['all'] = $this->db->get_where('tbltasks')->num_rows();

        echo json_encode($get_statuses);
        die();
    }
}
