<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_task extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('purchases_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('transfer_model');

        $this->preViewSuggestTask = true;
        $this->preViewOwnSuggestTask = true;
        $this->preAddSuggestTask = true;
        $this->preEditSuggestTask = true;
        $this->preApproveSuggestTask = true;
        $this->preDeleteSuggestTask = true;

        $this->task_status = $this->tasks_model->get_statuses();
        $this->tasksPriorities = get_tasks_priorities();
    }

    public function index()
    {
        if (!$this->preViewSuggestTask && !$this->preViewOwnSuggestTask) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_task');
        $this->load->view('admin/suggest_task/index', $data);
    }

    public function getSuggestTasks()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_tamp = "(
            SELECT
                tbl_suggest_task_staff.suggest_task_id as suggest_task_id,
                GROUP_CONCAT(tblstaff.staffid) as staff_task,
                GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
            FROM tbl_suggest_task_staff
            JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task_staff.staff_id
            GROUP BY tbl_suggest_task_staff.suggest_task_id
        ) tb_tamp";
        $aColumns = [
            'tbl_suggest_task.id as id',
            'tbl_suggest_task.reference_no as reference_no',
            'tbl_suggest_task.date as date',
            'tblcategory_tasks.code as code_category_tasks',
            'tblroles.code_role as code_role',
            'tbl_room.code as code_departments',
            'tbl_suggest_task.detail_task as detail_task',
            'tbl_suggest_task.regulations as regulations',
            'tbl_suggest_task.date_start as date_start',
            'tbl_suggest_task.date_finish as date_finish',
            'tbl_suggest_task.date_end as date_end',
            'tbl_suggest_task.staff_id as staff_id',
            'tbl_suggest_task.priority as priority',
            'tbl_suggest_task.status as status',
            'tbl_suggest_task.result_id as result_id',
            '1',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_suggest_task.id AND tblproduction_report.object_type = "suggest_task"
            ) as name_report',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_task';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task.staff_id',
            'INNER JOIN tblroles ON tblroles.roleid = tbl_suggest_task.role_id',
            'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tbl_suggest_task.category_tasks',
            'LEFT JOIN tbl_room ON tbl_room.id = tbl_suggest_task.department_id',
            'LEFT JOIN '.$tb_tamp.' ON tb_tamp.suggest_task_id = tbl_suggest_task.id',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tbl_suggest_task"'
        ];

        if (!$this->preViewSuggestTask) {
            array_push($where, 'AND (tbl_suggest_task.created_by = '.get_staff_user_id().' OR tbl_suggest_task.staff_id = '.get_staff_user_id().' 
            OR EXISTS (
                SELECT 1
                FROM tbl_suggest_task_staff
                WHERE tbl_suggest_task_staff.suggest_task_id = tbl_suggest_task.id
                AND tbl_suggest_task_staff.staff_id = '.get_staff_user_id().'
            )
            )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_task.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_task.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_tamp.staff_task',
            'tb_tamp.staff_name_task',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as full_name',
            '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tbl_suggest_task.id AND tbl_category_recommended.name_table="tbl_suggest_task") as countTask',
            'tbl_category_recommended.id as category_recommended_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $j = 0;
        $dtResult = get_table_where('tbl_result');

        // Batch fetch tasks to avoid N+1 query pattern inside the loop
        $suggest_task_ids_for_tasks = [];
        foreach ($rResult as $aRow) {
            if (!empty($aRow['countTask'])) {
                $suggest_task_ids_for_tasks[] = $aRow['id'];
            }
        }
        $batched_tasks = [];
        if (!empty($suggest_task_ids_for_tasks)) {
            $this->db->select('id, name, suggest_id, category_recommended_id');
            $this->db->where_in('suggest_id', $suggest_task_ids_for_tasks);
            $tasks_query_result = $this->db->get('tbltasks')->result_array();
            foreach ($tasks_query_result as $task_item) {
                $key_task = $task_item['suggest_id'] . '_' . $task_item['category_recommended_id'];
                $batched_tasks[$key_task][] = $task_item;
            }
        }

        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_task/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_category_tasks']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_role']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_departments']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_task']) . '</div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left">' . ($aRow['regulations']) . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '') . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_finish']) ? _dhau($aRow['date_finish']) : '') . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '') . '</div>';
            $row[] = '<div class="text-left">'.format_members_by_ids_and_names($aRow['staff_id'], $aRow['full_name']).'</div>';
            if (!empty($aRow['staff_task'])){
                $row[] = '<div class="text-left">'.format_members_by_ids_and_names($aRow['staff_task'], $aRow['staff_name_task']).'</div>';
            } else {
                $row[] =  '';
            }
            $priority = '<select data-id="' . $aRow['id'] . '" data-placeholder="Chọn" data-live-search="true" id="priority_' . $j . '" class="priority modal-select2" style="width: 100%">
                    <option></option>';
            foreach ($this->tasksPriorities as $kk => $vv) {
                $selected = '';
                if ($aRow['priority'] == $vv['id']) {
                    $selected = 'selected';
                }
                $priority .= '<option ' . $selected . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
            }
            $priority .= '</select>';

            $htmlStatus = '<select data-id="' . $aRow['id'] . '" data-placeholder="Chọn" data-live-search="true" id="status_' . $j . '" class="status modal-select2" style="width: 100%">
                    <option></option>';
            foreach ($this->task_status as $kk => $vv) {
                $selected = '';
                if ($aRow['status'] == $vv['id']) {
                    $selected = 'selected';
                }
                $htmlStatus .= '<option ' . $selected . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
            }
            $htmlStatus .= '</select>';

            $htmlResult = '<select data-id="' . $aRow['id'] . '" data-placeholder="Chọn" data-live-search="true" id="result_id_' . $j . '" class="result_id modal-select2" style="width: 100%">
                    <option></option>';
            foreach ($dtResult as $kk => $vv) {
                $selected = '';
                if ($aRow['result_id'] == $vv['id']) {
                    $selected = 'selected';
                }
                $htmlResult .= '<option ' . $selected . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
            }
            $htmlResult .= '</select>';
            $j++;
            $row[] = '<div class="text-left">'.$priority.'</div>';
            $row[] = '<div class="text-left">'.$htmlStatus.'</div>';
            $row[] = '<div class="">'.$htmlResult.'</div>';
            if (!has_permission('tasks', '', 'create')) {
                $row[] = '';
            } else {
                $task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
                if (!empty($aRow['countTask'])) {
                    $task_key = $aRow['id'] . '_' . $aRow['category_recommended_id'];
                    $data_tasks = isset($batched_tasks[$task_key]) ? $batched_tasks[$task_key] : [];
                    $__data = '';
                    $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $aRow['countTask'] . ' Phiếu
                        </button>';
                    foreach ($data_tasks as $kk => $vv) {
                        $__data .= '<li><a href="' . admin_url('tasks/view') . $vv['id'] . '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' . $vv['id'] . '); return false;">' . $vv['name'] . '</a>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                    $task .= $_data;
                    // $column[15] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">' . $aRow['countTask'] . ' phiếu công việc . </span>';
                    // '(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',

                }
                $row[] = $task;
            }
            $arrReport = $aRow['name_report'];
            $htmlReport = '';
            if (!empty($arrReport)) {
                $arrReport = explode('||', $arrReport);
                if (!empty($arrReport)) {
                    foreach ($arrReport as $kk => $vv) {
                        $vv = explode('__', $vv);
                        $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
                    }
                }
            }
            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_task') . '" class="btn btn-info">Tạo phiếu báo cáo</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_task/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestTask ? '<a class="tnh-modal" href="' . base_url('admin/suggest_task/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestTask ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_task/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        $this->db->select('tbl_suggest_task.*');
        $this->db->from('tbl_suggest_task');
        $this->db->where('tbl_suggest_task.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_task.reference_no]');
                }
            } else {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_task.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('role_id', lang("Mã vị trí"), 'required');
            $this->form_validation->set_rules('date_start', lang("Ngày bắt đầu"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_task');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $role_id = $this->input->post('role_id');
                    $staff_id = $this->input->post('staff_id');
                    $date_start = !empty($this->input->post('date_start')) ? to_sql_date($this->input->post('date_start')) : null;
                    $date_end = !empty($this->input->post('date_end')) ? to_sql_date($this->input->post('date_end')) : null;
                    $date_finish = !empty($this->input->post('date_finish')) ? to_sql_date($this->input->post('date_finish')) : null;
                    $regulations = ($this->input->post('regulations'));
                    $detail_task = ($this->input->post('detail_task'));
                    $category_tasks = ($this->input->post('category_tasks'));
                    $department_id = ($this->input->post('department_id'));
                    $arrId = ($this->input->post('suggest_task_staff'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'role_id' => $role_id,
                        'staff_id' => $staff_id,
                        'date_start' => $date_start,
                        'date_end' => $date_end,
                        'date_finish' => $date_finish,
                        'regulations' => $regulations,
                        'detail_task' => $detail_task,
                        'category_tasks' => $category_tasks,
                        'department_id' => $department_id,
                        'status' => 1,
                        'priority' => 1,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_task',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_task') == $reference_no) {
                            updateReference('suggest_task');
                        }
                        $arrId = array_unique($arrId);
                        if (!empty($arrId)){
                            foreach ($arrId as $kk => $vv){
                                $this->db->insert('tbl_suggest_task_staff',[
                                    'suggest_task_id' => $id,
                                    'staff_id' => $vv,
                                ]);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_task',
                            'table_obj' => 'tbl_suggest_task',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu công việc') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $role_id = $this->input->post('role_id');
                    $staff_id = $this->input->post('staff_id');
                    $date_start = !empty($this->input->post('date_start')) ? to_sql_date($this->input->post('date_start')) : null;
                    $date_end = !empty($this->input->post('date_end')) ? to_sql_date($this->input->post('date_end')) : null;
                    $date_finish = !empty($this->input->post('date_finish')) ? to_sql_date($this->input->post('date_finish')) : null;
                    $regulations = ($this->input->post('regulations'));
                    $detail_task = ($this->input->post('detail_task'));
                    $fields = [
                        'date' => $date,
                        'role_id' => $role_id,
                        'staff_id' => $staff_id,
                        'date_start' => $date_start,
                        'date_end' => $date_end,
                        'date_finish' => $date_finish,
                        'regulations' => $regulations,
                        'detail_task' => $detail_task,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_task',$fields);
                    $arrId[] = $staff_id;
                    $arrId[] = $dtData['created_by'];
                    if ($success){

                        $this->db->where('tbl_suggest_task_staff.suggest_task_id',$dtData['id']);
                        $this->db->delete('tbl_suggest_task_staff');

                        $arrId = array_unique($arrId);
                        if (!empty($arrId)){
                            foreach ($arrId as $kk => $vv){
                                $this->db->insert('tbl_suggest_task_staff',[
                                    'suggest_task_id' => $id,
                                    'staff_id' => $vv,
                                ]);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_task',
                            'table_obj' => 'tbl_suggest_task',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu công việc') . ' [' . $dtData['reference_no'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            }
        } else {
            if (empty($id)){
                if (!$this->preAddSuggestTask){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_task');
            } else {
                if (!$this->preEditSuggestTask){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_suggest_task');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_task');
        $data['dtResult'] = get_table_where('tbl_result');
		$data['departments'] = $this->db->get_where('tbldepartments', ['type' => 0])->result_array();
		$data['room'] = $this->db->get_where('tbl_room')->result_array();

        $data['category_tasks'] = $this->site_model->getCategoryTasks();

        $this->load->view('admin/suggest_task/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_task');

        $tb_tamp = "(
            SELECT
                GROUP_CONCAT(tblstaff.staffid) as staff_task,
                GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
            FROM tbl_suggest_task_staff
            JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task_staff.staff_id
            WHERE tbl_suggest_task_staff.suggest_task_id = $id
        )";
        $dtStaff = $this->db->query($tb_tamp)->row_array();
        $this->db->select('
            tbl_suggest_task.*,
            tblroles.name as name_role,
            tbl_result.name as name_result,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as full_name,
        ');
        $this->db->from('tbl_suggest_task');
        $this->db->join('tblroles','tblroles.roleid = tbl_suggest_task.role_id','inner');
        $this->db->join('tblstaff','tblstaff.staffid = tbl_suggest_task.staff_id','inner');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_task.result_id','left');
        $this->db->where('tbl_suggest_task.id',$id);
        $dtData = $this->db->get()->row_array();

        $data['dtData'] = $dtData;
        $data['dtStaff'] = $dtStaff;
        $this->load->view('admin/suggest_task/view',$data);
    }

    public function delete($id){
        if (!$this->preDeleteSuggestTask){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_task.*');
        $this->db->from('tbl_suggest_task');
        $this->db->where('tbl_suggest_task.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.object_type', 'suggest_task');
        $this->db->where('tblproduction_report.object_id', $id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_task');
        if ($success){

            $this->db->where('tbl_suggest_task_staff.suggest_task_id',$id);
            $this->db->delete('tbl_suggest_task_staff');

            insertActivityLog([
                'type_parent_obj' => 'suggest_task',
                'table_obj' => 'tbl_suggest_task',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu công việc') . ' [' . $dtData['reference_no'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function searchRoles($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblroles.roleid as id, 
            CONCAT(tblroles.name,"(",COALESCE(tblroles.code_role,""),")") as text,
            tblroles.code_role as code
        ', false);
        $this->db->from('tblroles');
        $this->db->where('tblroles.active_role',1);
        $this->db->where('tblroles.type',0);    
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblroles.code_role', $term);
            $this->db->or_like('tblroles.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mã vị trí'), 'children' => $pod];
        if (!empty($id)){
            $dtRole = get_table_where('tblroles',['roleid' => $id],'','row_array');
            $data['row'] = ['id' => $dtRole['roleid'], 'text' => $dtRole['name'].'('.$dtRole['code_role'].')'];
        }
        echo json_encode($data);
    }

    public function updateTicket()
    {
        $data = [];
        if (!$this->preApproveSuggestTask){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $priority = $this->input->post('priority');
        $status = $this->input->post('status');
        $result_id = $this->input->post('result_id');
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $this->db->select('tbl_suggest_task.*');
        $this->db->from('tbl_suggest_task');
        $this->db->where('tbl_suggest_task.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($type == 'priority'){
            $this->db->where('id',$id);
            $success = $this->db->update('tbl_suggest_task',[
                'priority' => $priority
            ]);
        } elseif ($type == 'status'){
            $this->db->where('id',$id);
            $success = $this->db->update('tbl_suggest_task',[
                'status' => $status
            ]);
        } elseif ($type == 'result'){
            $this->db->where('id',$id);
            $success = $this->db->update('tbl_suggest_task',[
                'result_id' => $result_id
            ]);
        }
        if($success){
            if ($type == 'priority'){
                insertActivityLog([
                    'type_parent_obj' => 'suggest_task',
                    'table_obj' => 'tbl_suggest_task',
                    'id_obj' => $id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Cập nhập mức độ ưu tiên phiếu yêu cầu công việc') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'update'
                ]);
            } elseif ($type == 'status'){
                insertActivityLog([
                    'type_parent_obj' => 'suggest_task',
                    'table_obj' => 'tbl_suggest_task',
                    'id_obj' => $id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Cập nhập trạng thái tiên phiếu yêu cầu công việc') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'update'
                ]);
            } elseif ($type == 'result'){
                insertActivityLog([
                    'type_parent_obj' => 'suggest_task',
                    'table_obj' => 'tbl_suggest_task',
                    'id_obj' => $id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Cập nhập kết quả phiếu yêu cầu công việc') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'update'
                ]);
            }
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();
            $tb_tamp = "(
                SELECT
                    tbl_suggest_task_staff.suggest_task_id as suggest_task_id,
                    GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
                FROM tbl_suggest_task_staff
                JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task_staff.staff_id
                GROUP BY tbl_suggest_task_staff.suggest_task_id
            ) tb_tamp";

            $this->db->select('
               tbl_suggest_task.id as id,
               tbl_suggest_task.reference_no as reference_no,
               tbl_suggest_task.date as date,
               tblroles.code_role as code_role,
               tbl_suggest_task.detail_task as detail_task,
               tbl_suggest_task.regulations as regulations,
               tbl_suggest_task.date_start as date_start,
               tbl_suggest_task.date_finish as date_finish,
               tbl_suggest_task.date_end as date_end,
               tbl_suggest_task.staff_id as staff_id,
               COALESCE(tb_tamp.staff_name_task,"") as staff_name_task,
               tbl_suggest_task.priority as priority,
               tbl_suggest_task.status as status,
               tbl_result.name as name_result,
               (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_task.id AND tblproduction_report.object_type = "suggest_task"
                ) as name_report
            ');
            $this->db->from('tbl_suggest_task');
            $this->db->join('tblroles','tblroles.roleid = tbl_suggest_task.role_id','inner');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_task.result_id','left');
            $this->db->join($tb_tamp,'tb_tamp.suggest_task_id = tbl_suggest_task.id','left');


            if (!$this->preViewSuggestTask) {
                $this->db->where('(tbl_suggest_task.created_by = '.$staff_id.' OR tbl_suggest_task.staff_id = '.$staff_id.' 
                    OR EXISTS (
                        SELECT 1
                        FROM tbl_suggest_task_staff
                        WHERE tbl_suggest_task_staff.suggest_task_id = tbl_suggest_task.id
                        AND tbl_suggest_task_staff.staff_id = '.$staff_id.'
                    )
                )');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_task.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_task.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_task.id desc');
            $dtData = $this->db->get()->result_array();


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
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            insertCompanyInfo($objPHPExcel, 'C1:Q2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('PHIẾU YÊU CẦU CÔNG VIỆC'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Số Phiếu Công Việc')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Tạo');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Vị Trí');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Chi Tiết Công Việc')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Quy Trình')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Quy Định')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Bắt Đầu')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Ngày Hoàn Thành')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Hạn Chót')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Người Giao Việc')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Người Được Phân Công')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Mức Độ Ưu Tiên')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Trạng Thái')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Kết Quả')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Báo Cáo Sự Cố')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'QR')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:Q$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_role'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['detail_task']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", '')->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['regulations'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",!empty($value['date_start']) ? _dhau($value['date_start']) : '')->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",!empty($value['date_finish']) ? _dhau($value['date_finish']) : '')->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", !empty($value['date_end']) ? _dhau($value['date_end']) : '')->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['staff_name_task'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $priority = task_priority($value['priority']);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", !empty($priority) ? $priority : '')->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $status = get_task_status_by_id($value['status']);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", !empty($status) ? $status['name'] : '')->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['name_result'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['name_report'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_task||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_task',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_task/';
                    if (!file_exists($folder)) {
                        mkdir($folder);
                        fopen($folder . 'index.html', 'w');
                    }
                    if (!file_exists($folder . 'qrcode' . '/')) {
                        mkdir($folder . 'qrcode' . '/');
                        fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                    }
                    $params['data'] = $code;
                    $params['level'] = 'H';
                    $params['size'] = 40;
                    $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                    $this->ciqrcode->generate($params);
                    $img = ($folder.'qrcode/'. $qr . '.png');
                    if (!empty($img)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($img);
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('Q' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", '')->getStyle("Q$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:Q$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_cong_viec') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
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
}