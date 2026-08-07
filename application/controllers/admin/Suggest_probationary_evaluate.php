<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_probationary_evaluate extends AdminController
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

        $this->type = 1;
        if (!empty($this->input->get('type'))) {
            $this->type = $this->input->get('type');
        }

        $this->preViewSuggestProbationaryEvaluate = true;
        $this->preViewOwnSuggestProbationaryEvaluate = true;
        $this->preAddSuggestProbationaryEvaluate= true;
        $this->preEditSuggestProbationaryEvaluate = true;
        $this->preApproveSuggestProbationaryEvaluate = true;
        $this->preDeleteSuggestProbationaryEvaluate = true;
    }

    public function index()
    {
        if (!$this->preViewSuggestProbationaryEvaluate && !$this->preViewOwnSuggestProbationaryEvaluate) {
            access_denied();
        }
        if ($this->type == 1) {
            $data['title'] = _l('dt_suggest_probationary_evaluate');
        } elseif ($this->type == 2) {
            $data['title'] = _l('dt_suggest_employee_evaluate');
        } elseif ($this->type == 3) {
            $data['title'] = _l('dt_suggest_skill_evaluate');
        }
        $this->load->view('admin/suggest_probationary_evaluate/index', $data);
    }

    public function getSuggestProbationaryEvaluate()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_department = "(
            SELECT
                tblstaff_departments.staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $aColumns = [
            'tbl_suggest_probationary_evaluate.id as id',
            'tbl_suggest_probationary_evaluate.reference_no as reference_no',
            'tbl_suggest_probationary_evaluate.date as date',
            'tbl_suggest_probationary_evaluate.staff_id as staff_id',
            'tblroles.name as name_role',
            'COALESCE(tb_department.name_department,"") as name_department',
            'tbl_suggest_probationary_evaluate.status as status',
            'tbl_suggest_probationary_evaluate.date_start_probationary as date_start_probationary',
            'tbl_suggest_probationary_evaluate.date_end_probationary as date_end_probationary',
            'tbl_suggest_probationary_evaluate.staff_manager as staff_manager',
            'tbl_suggest_probationary_evaluate.staff_manager_hr as staff_manager_hr',
            'tbl_suggest_probationary_evaluate.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_probationary_evaluate';
        $where = [
            'AND tbl_suggest_probationary_evaluate.type = '.$type.''
        ];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_probationary_evaluate.staff_id',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN '.$tb_department.' ON tb_department.staffid = tblstaff.staffid',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tbl_suggest_probationary_evaluate" AND tbl_category_recommended.type='.$type.''
        ];

        if (!$this->preViewSuggestProbationaryEvaluate) {
            array_push($where, 'AND tbl_suggest_probationary_evaluate.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_probationary_evaluate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_probationary_evaluate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_probationary_evaluate.date_status',
            'tbl_suggest_probationary_evaluate.staff_status',
            'tbl_category_recommended.id as category_recommended_id',
            '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tbl_suggest_probationary_evaluate.id AND tbl_category_recommended.name_table="tbl_suggest_probationary_evaluate" AND tbl_category_recommended.type='.$type.') as countTask'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_probationary_evaluate/view/' . $aRow['id'].'?type='.$type) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_id']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_role']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_department']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';
            if (!has_permission('tasks', '', 'create') || $aRow['status'] == 0) {
                $_data = '';
            } else {
                $task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
                if (!empty($aRow['countTask'])) {
                    $data_tasks = get_table_where('tbltasks', ['suggest_id' => $aRow['id'], 'category_recommended_id' => $aRow['category_recommended_id']], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
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

                }
                $_data = $task;
            }
            $row[] = '<div class="text-left">'.$_data.'</div>';
            $row[] = '<div class="text-left">' . _dhau($aRow['date_start_probationary']) . '</div>';
            $row[] = '<div class="text-left">' . _dhau($aRow['date_end_probationary']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_manager']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_manager_hr']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_probationary_evaluate/view/' . $aRow['id'].'?type='.$type) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestProbationaryEvaluate ? '<a class="tnh-modal" href="' . base_url('admin/suggest_probationary_evaluate/detail/' . $aRow['id'].'?type='.$type) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';
            $delete = $this->preDeleteSuggestProbationaryEvaluate ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_probationary_evaluate/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_probationary_evaluate.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
                if ($this->type == 1 || $this->type == 3) {
                    $this->form_validation->set_rules('date_start_probationary', lang("Ngày bắt đầu thử việc"),
                        'required');
                    $this->form_validation->set_rules('date_end_probationary', lang("Ngày kết thúc thử việc"),
                        'required');
                }
                if ($this->form_validation->run() == true) {
                    if ($this->type == 1){
                        $reference_no = getReference('suggest_probationary_evaluate');
                    } elseif ($this->type == 2){
                        $reference_no = getReference('suggest_employee_evaluate');
                    } elseif ($this->type == 3){
                        $reference_no = getReference('suggest_skill_evaluate');
                    }
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $staff_manager = $this->input->post('staff_manager');
                    $staff_manager_hr = $this->input->post('staff_manager_hr');
                    $date_start_probationary = !empty($this->input->post('date_start_probationary')) ? to_sql_date($this->input->post('date_start_probationary')) : null;
                    $date_end_probationary = !empty($this->input->post('date_end_probationary')) ? to_sql_date($this->input->post('date_end_probationary')) : null;
                    $note = ($this->input->post('note'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'staff_manager' => $staff_manager,
                        'staff_manager_hr' => $staff_manager_hr,
                        'date_start_probationary' => $date_start_probationary,
                        'date_end_probationary' => $date_end_probationary,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                        'type' => $this->type
                    ];
                    $this->db->insert('tbl_suggest_probationary_evaluate',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if ($this->type == 1){
                            if (getReference('suggest_probationary_evaluate') == $reference_no) {
                                updateReference('suggest_probationary_evaluate');
                            }
                        } elseif ($this->type == 2){
                            if (getReference('suggest_employee_evaluate') == $reference_no) {
                                updateReference('suggest_employee_evaluate');
                            }
                        } elseif ($this->type == 3){
                            if (getReference('suggest_skill_evaluate') == $reference_no) {
                                updateReference('suggest_skill_evaluate');
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_probationary_evaluate',
                            'table_obj' => 'tbl_suggest_probationary_evaluate',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu đánh giá thử việc') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_suggest_probationary_evaluate.*');
                $this->db->from('tbl_suggest_probationary_evaluate');
                $this->db->where('tbl_suggest_probationary_evaluate.id',$id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_probationary_evaluate.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
                if ($this->type == 1 || $this->type == 3) {
                    $this->form_validation->set_rules('date_start_probationary', lang("Ngày bắt đầu thử việc"),
                        'required');
                    $this->form_validation->set_rules('date_end_probationary', lang("Ngày kết thúc thử việc"),
                        'required');
                }
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $staff_manager = $this->input->post('staff_manager');
                    $staff_manager_hr = $this->input->post('staff_manager_hr');
                    $date_start_probationary = !empty($this->input->post('date_start_probationary')) ? to_sql_date($this->input->post('date_start_probationary')) : null;
                    $date_end_probationary = !empty($this->input->post('date_end_probationary')) ? to_sql_date($this->input->post('date_end_probationary')) : null;
                    $note = ($this->input->post('note'));
                    $fields = [
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'staff_manager' => $staff_manager,
                        'staff_manager_hr' => $staff_manager_hr,
                        'date_start_probationary' => $date_start_probationary,
                        'date_end_probationary' => $date_end_probationary,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'type' => $this->type
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_probationary_evaluate',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_probationary_evaluate',
                            'table_obj' => 'tbl_suggest_probationary_evaluate',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá thử việc') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestProbationaryEvaluate){
                    accessDenied(true);
                }
                if($this->type == 1) {
                    $data['title'] = lang('dt_add_suggest_probationary_evaluate');
                } elseif ($this->type == 2){
                    $data['title'] = lang('dt_add_suggest_employee_evaluate');
                } elseif ($this->type == 3){
                    $data['title'] = lang('dt_add_suggest_skill_evaluate');
                }
            } else {
                if (!$this->preEditSuggestProbationaryEvaluate){
                    accessDenied(true);
                }
                $this->db->select('
                    tbl_suggest_probationary_evaluate.*,
                ');
                $this->db->from('tbl_suggest_probationary_evaluate');
                $this->db->where('tbl_suggest_probationary_evaluate.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $staff_id = $dtData['staff_id'];
                $tb_department = "(
                    SELECT
                        tblstaff_departments.staffid,
                        GROUP_CONCAT(tbldepartments.name) as name_department
                    FROM tbldepartments
                    JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                    WHERE tblstaff_departments.staffid = $staff_id
                    GROUP BY tblstaff_departments.staffid
                ) tb_department";

                $this->db->select('
                    tblstaff.staffid as staff_id,
                    tblroles.name as code_role,
                    COALESCE(tb_department.name_department,"") as name_department
                ');
                $this->db->from('tblstaff');
                $this->db->join('tblroles','tblroles.roleid = tblstaff.role','left');
                $this->db->join($tb_department,'tb_department.staffid = tblstaff.staffid','left');
                $this->db->where('tblstaff.staffid',$staff_id);
                $dtStaff = $this->db->get()->row_array();
                $dtData['code_role'] = !empty($dtStaff) ? $dtStaff['code_role'] : '';
                $dtData['name_department'] = !empty($dtStaff) ? $dtStaff['name_department'] : '';

                $data['dtData'] = $dtData;
                if ($this->type == 1) {
                    $data['title'] = lang('dt_edit_suggest_probationary_evaluate');
                } elseif ($this->type == 2){
                    $data['title'] = lang('dt_edit_suggest_employee_evaluate');
                } elseif ($this->type == 3){
                    $data['title'] = lang('dt_edit_suggest_skill_evaluate');
                }
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        if ($this->type == 1){
            $data['reference_no'] = getReference('suggest_probationary_evaluate');
        } elseif ($this->type == 2){
            $data['reference_no'] = getReference('suggest_employee_evaluate');
        } elseif ($this->type == 3){
            $data['reference_no'] = getReference('suggest_skill_evaluate');
        }
        $this->load->view('admin/suggest_probationary_evaluate/detail',$data);
    }

    public function view($id){
        $data = [];
        if ($this->type == 1) {
            $data['title'] = lang('dt_view_suggest_probationary_evaluate');
        } elseif ($this->type == 2){
            $data['title'] = lang('dt_view_suggest_employee_evaluate');
        } elseif ($this->type == 3){
            $data['title'] = lang('dt_view_suggest_skill_evaluate');
        }

        $this->db->select('tbl_suggest_probationary_evaluate.*,
        tblbranch.name as name_branch
        ');
        $this->db->from('tbl_suggest_probationary_evaluate');
        $this->db->from('tblbranch','tblbranch.id = tbl_suggest_probationary_evaluate.branch_id');
        $this->db->where('tbl_suggest_probationary_evaluate.id',$id);
        $dtData = $this->db->get()->row_array();
        $staff_id = $dtData['staff_id'];
        $tb_department = "(
            SELECT
                tblstaff_departments.staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE tblstaff_departments.staffid = $staff_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select('
            tblstaff.staffid as staff_id,
            tblroles.name as code_role,
            COALESCE(tb_department.name_department,"") as name_department
        ');
        $this->db->from('tblstaff');
        $this->db->join('tblroles','tblroles.roleid = tblstaff.role','left');
        $this->db->join($tb_department,'tb_department.staffid = tblstaff.staffid','left');
        $this->db->where('tblstaff.staffid',$staff_id);
        $dtStaff = $this->db->get()->row_array();
        $dtData['code_role'] = !empty($dtStaff) ? $dtStaff['code_role'] : '';
        $dtData['name_department'] = !empty($dtStaff) ? $dtStaff['name_department'] : '';

        $data['dtData'] = $dtData;
        $this->load->view('admin/suggest_probationary_evaluate/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestProbationaryEvaluate) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_probationary_evaluate.*');
        $this->db->from('tbl_suggest_probationary_evaluate');
        $this->db->where('tbl_suggest_probationary_evaluate.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data); return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];
            $optionsCreate = [
                'reference_no' => getReference('probationary_evaluate'),
                'date' => date('Y-m-d H:i:s'),
                'suggest_probationary_evaluate_id' => $dtData['id'],
                'staff_id' => $dtData['staff_id'],
                'date_start_probationary' => $dtData['date_start_probationary'],
                'date_end_probationary' => $dtData['date_end_probationary'],
                'branch_id' => $dtData['branch_id'],
                'staff_manager' => $dtData['staff_manager'],
                'staff_manager_hr' => $dtData['staff_manager_hr'],
                'created_by' => get_staff_user_id(),
                'date_created' => date('Y-m-d H:i:s'),
                'type' => $dtData['type']
            ];
            $items = [];
            foreach (getListFiveCoreValue() as $key => $value){
                $items[] = [
                    'name' => '5 giá trị cốt lõi',
                    'evaluation_criteria_id' => $value['id'],
                    'type' => 1
                ];
            }
            $this->db->from('tblstaff_departments');
            $this->db->where('staffid',$dtData['staff_id']);
            $this->db->order_by('departmentid asc');
            $dtDepartment = $this->db->get()->row_array();
            if(!empty($dtDepartment)){
                $this->db->from('tbl_category_department_kpi');
                $this->db->where('tbl_category_department_kpi.department_id',$dtDepartment['departmentid']);
                $dtCategoryKpi = $this->db->get()->result_array();
                if (!empty($dtCategoryKpi)){
                    foreach ($dtCategoryKpi as $key => $value){
                        $items[] = [
                            'name' => 'KPIs đo mục tiêu',
                            'evaluation_criteria_id' => $value['id'],
                            'type' => 2
                        ];
                    }
                }
            }
            foreach (getListFollow() as $key => $value){
                $items[] = [
                    'name' => 'Tuân thủ',
                    'evaluation_criteria_id' => $value['id'],
                    'type' => 3
                ];
            }
            $this->db->where('id',$suggest_id);
            $up = $this->db->update('tbl_suggest_probationary_evaluate',$options);
            if ($up) {
                if ($status == 1) {
                    if (!empty($optionsCreate)) {
                        $this->db->insert('tbl_probationary_evaluate',$optionsCreate);
                        $id_insert = $this->db->insert_id();
                        if ($id_insert){
                            updateReference('probationary_evaluate');
                            if (!empty($items)){
                                foreach ($items as $key => $value){
                                    $value['probationary_evaluate_id'] = $id_insert;
                                    $this->db->insert('tbl_probationary_evaluate_item',$value);
                                }
                            }
                        }
                    }
                } else {
                    $dtProbationaryEvaluate = get_table_where('tbl_probationary_evaluate',['suggest_probationary_evaluate_id' => $dtData['id']],'','row_array');
                    $this->db->where('tbl_probationary_evaluate.id',$dtProbationaryEvaluate['id']);
                    $success_delete = $this->db->delete('tbl_probationary_evaluate');
                    if (!empty($success_delete)){
                        $this->db->where('tbl_probationary_evaluate_item.probationary_evaluate_id',$dtProbationaryEvaluate['id']);
                        $this->db->delete('tbl_probationary_evaluate_item');
                    }
                }
                insertActivityLog([
                    'type_parent_obj' => 'suggest_probationary_evaluate',
                    'table_obj' => 'tbl_suggest_probationary_evaluate',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu đánh giá thử việc') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'approved'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function delete($id){
        if (!$this->preDeleteSuggestProbationaryEvaluate){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_probationary_evaluate.*');
        $this->db->from('tbl_suggest_probationary_evaluate');
        $this->db->where('tbl_suggest_probationary_evaluate.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtData['status'] == 1){
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_probationary_evaluate');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'suggest_probationary_evaluate',
                'table_obj' => 'tbl_suggest_probationary_evaluate',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu đánh giá thử việc') . ' [' . $dtData['reference_no'] . ']',
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

    public function create_evaluate($id){
        $data = [];
        $this->db->select('tbl_suggest_probationary_evaluate.*');
        $this->db->from('tbl_suggest_probationary_evaluate');
        $this->db->where('tbl_suggest_probationary_evaluate.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            refererModel(lang('not_data_exists'));
        }
        if ($dtData['status'] == 0){
            refererModel(lang('Vui lòng duyệt phiếu trước!'));
        }
        $data['dtData'] = $dtData;
        $data['id'] = $id;
        $data['title'] = lang('dt_add_probationary_evaluate');
        $data['reference_no'] = getReference('probationary_evaluate');
        $this->load->view('admin/suggest_probationary_evaluate/create_evaluate',$data);
    }

    public function getInfoStaff(){
        $staff_id = $this->input->post('staff_id');

        $tb_department = "(
            SELECT
                tblstaff_departments.staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE tblstaff_departments.staffid = $staff_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('
            tblstaff.staffid as staff_id,
            tblroles.name as code_role,
            COALESCE(tb_department.name_department,"") as name_department
        ');
        $this->db->from('tblstaff');
        $this->db->join('tblroles','tblroles.roleid = tblstaff.role','left');
        $this->db->join($tb_department,'tb_department.staffid = tblstaff.staffid','left');
        $this->db->where('tblstaff.staffid',$staff_id);
        $dtStaff = $this->db->get()->row_array();
        $data['dtStaff'] = $dtStaff;
        echo json_encode($data);
    }

    public function exportExcel()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            if ($this->input->post('type') == 1) {
                $inputFileName = 'uploads/import_dt/phieu_yeu_cau_danh_gia_thu_viec.xlsx';
            } elseif ($this->input->post('type') == 2){
                $inputFileName = 'uploads/import_dt/phieu_yeu_cau_danh_gia_nhan_vien.xlsx';
            } elseif($this->input->post('type') == 3) {
                $inputFileName = 'uploads/import_dt/phieu_yeu_cau_danh_gia_tay_nghe.xlsx';
            }
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $tb_department = "(
                SELECT
                    tblstaff_departments.staffid,
                    GROUP_CONCAT(tbldepartments.name) as name_department
                FROM tbldepartments
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                WHERE tblstaff_departments.staffid = $staff_id
                GROUP BY tblstaff_departments.staffid
            ) tb_department";
            $this->db->select('tbl_suggest_probationary_evaluate.*,
                tbl_probationary_evaluate.reference_no as reference_no_evaluate,
                tbl_category_department_kpi.name as name_evaluation_criteria,
                tbl_probationary_evaluate_item.type as type,
                tbl_probationary_evaluate_item.evaluation_criteria_id as evaluation_criteria_id,
                result.name as result,
                result_manager.name as result_manager,
                result_manager_hr.name as result_manager_hr,
                tblroles.name as code_role,
                COALESCE(tb_department.name_department,"") as name_department
            ');
            $this->db->from('tbl_suggest_probationary_evaluate');
            $this->db->join('tblstaff','tblstaff.staffid = tbl_suggest_probationary_evaluate.staff_id','inner');
            $this->db->join('tblroles','tblroles.roleid = tblstaff.role','left');
            $this->db->join($tb_department,'tb_department.staffid = tblstaff.staffid','left');
            $this->db->join('tbl_probationary_evaluate', 'tbl_probationary_evaluate.suggest_probationary_evaluate_id = tbl_suggest_probationary_evaluate.id', 'left');
            $this->db->join('tbl_probationary_evaluate_item', 'tbl_probationary_evaluate_item.probationary_evaluate_id = tbl_probationary_evaluate.id', 'left');
            $this->db->join('tbl_category_department_kpi','tbl_category_department_kpi.id = tbl_probationary_evaluate_item.evaluation_criteria_id AND tbl_probationary_evaluate_item.type = 2','left');
            $this->db->join('tbl_result result','result.id = tbl_probationary_evaluate_item.result','left');
            $this->db->join('tbl_result result_manager','result_manager.id = tbl_probationary_evaluate_item.result_manager','left');
            $this->db->join('tbl_result result_manager_hr','result_manager_hr.id = tbl_probationary_evaluate_item.result_manager_hr','left');
            if (!$this->preViewSuggestProbationaryEvaluate) {
                $this->db->where('(tbl_suggest_probationary_evaluate.created_by = ' . $staff_id . ')');
            }
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_probationary_evaluate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_probationary_evaluate.date <= '" . $end_date_search . "'");
            }
            $this->db->where('tbl_suggest_probationary_evaluate.type',$this->input->post('type'));
            $this->db->order_by('tbl_suggest_probationary_evaluate.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');
            foreach ($items as $key => $value) {
                $name_evaluation_criteria = '';
                if ($value['type'] == 1){
                    $name_evaluation_criteria = getListFiveCoreValue($value['evaluation_criteria_id'])['name'];
                } elseif ($value['type'] == 2){
                    $name_evaluation_criteria = $value['name_evaluation_criteria'];
                } elseif ($value['type'] == 3){
                    $name_evaluation_criteria = getListFollow($value['evaluation_criteria_id'])['name'];
                }
                $row++;
                $dem++;
                $colStt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $dem);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, _dt($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['reference_no_evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_id']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['code_role']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                if ($this->input->post('type') == 1 || $this->input->post('type') == 3) {
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row,
                        _dhau($value['date_start_probationary']), PHPExcel_Cell_DataType::TYPE_STRING);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row,
                        _dhau($value['date_end_probationary']), PHPExcel_Cell_DataType::TYPE_STRING);
                }
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $name_evaluation_criteria);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['result'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['result_manager']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['result_manager_hr'], PHPExcel_Cell_DataType::TYPE_STRING);
//                $colStt ++;

//                if (!empty($value['barcode'])){
//                    $code = $value['barcode'];
//                } else {
//                    $code = 'suggest_additional_personnel||'.$value['id'];
//                    $this->db->where('id',$value['id']);
//                    $this->db->update('tbl_suggest_additional_personnel',['barcode' => $code]);
//                }
//                $qr = vn_to_str(str_replace('||', '__', $code));
//                $folder = FCPATH . 'uploads/suggest_additional_personnel/';
//                if (!file_exists($folder)) {
//                    mkdir($folder);
//                    fopen($folder . 'index.html', 'w');
//                }
//                if (!file_exists($folder . 'qrcode' . '/')) {
//                    mkdir($folder . 'qrcode' . '/');
//                    fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
//                }
//                $params['data'] = $code;
//                $params['level'] = 'H';
//                $params['size'] = 40;
//                $params['savename'] = $folder.'qrcode/'. $qr . '.png';
//                $this->ciqrcode->generate($params);
//                $img = ($folder.'qrcode/'. $qr . '.png');
//                if (!empty($img)) {
//                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
//                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
//                    $objDrawing1->setPath($img);
//                    $objDrawing1->setWidth(90);
//                    $objDrawing1->setHeight(65);
//                    $objDrawing1->setOffsetX(20);
//                    $objDrawing1->setOffsetY(2);
//                    $objDrawing1->setCoordinates($columsExcel[$colStt] . $row);
//                }
//                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
//                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, '')->getStyle($columsExcel[$colStt] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->applyFromArray([
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_danh_gia_thu_viec') . '.xls';
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