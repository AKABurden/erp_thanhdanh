<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Probationary_evaluate extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->type = 1;
        if (!empty($this->input->get('type'))) {
            $this->type = $this->input->get('type');
        }
        $this->preViewProbationaryEvaluate = true;
        $this->preViewOwnProbationaryEvaluate = true;
        $this->preAddProbationaryEvaluate= true;
        $this->preEdittProbationaryEvaluate = true;
        $this->preApproveProbationaryEvaluate = true;
        $this->preDeleteProbationaryEvaluate = true;
    }

    public function index()
    {
        if (!$this->preViewProbationaryEvaluate && !$this->preViewOwnProbationaryEvaluate) {
            access_denied();
        }
        if ($this->type == 1) {
            $data['title'] = _l('dt_probationary_evaluate');
        } elseif ($this->type == 2){
            $data['title'] = _l('dt_employee_evaluate');
        } elseif ($this->type == 3){
            $data['title'] = _l('dt_skill_evaluate');
        }
        $this->load->view('admin/probationary_evaluate/index', $data);
    }

    public function getProbationaryEvaluate()
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
            'tbl_probationary_evaluate.id as id',
            'tbl_probationary_evaluate.reference_no as reference_no',
            'tbl_probationary_evaluate.date as date',
            'tbl_suggest_probationary_evaluate.reference_no as reference_no_suggest',
            'tbl_probationary_evaluate.staff_id as staff_id',
            'tblroles.name as name_role',
            'COALESCE(tb_department.name_department,"") as name_department',
            'tbl_probationary_evaluate.status as status',
            'tbl_probationary_evaluate.date_start_probationary as date_start_probationary',
            'tbl_probationary_evaluate.date_end_probationary as date_end_probationary',
            'tbl_probationary_evaluate.staff_manager as staff_manager',
            'tbl_probationary_evaluate.staff_manager_hr as staff_manager_hr',
            'tbl_probationary_evaluate.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_probationary_evaluate';
        $where = [
            'AND tbl_probationary_evaluate.type = '.$type.''
        ];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_probationary_evaluate.staff_id',
            'INNER JOIN tbl_suggest_probationary_evaluate ON tbl_suggest_probationary_evaluate.id = tbl_probationary_evaluate.suggest_probationary_evaluate_id',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN '.$tb_department.' ON tb_department.staffid = tblstaff.staffid',
        ];

        if (!$this->preViewProbationaryEvaluate) {
            array_push($where, 'AND tbl_probationary_evaluate.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_probationary_evaluate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_probationary_evaluate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_probationary_evaluate.id as suggest_id',
            'tbl_probationary_evaluate.date_status',
            'tbl_probationary_evaluate.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/probationary_evaluate/view/' . $aRow['id'].'?type='.$type) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_probationary_evaluate/view/' . $aRow['suggest_id'].'?type='.$type) . '" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no_suggest']) . '</a></div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_id']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_role']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_department']) . '</div>';
            $row[] = '<div class="text-left">' . _dhau($aRow['date_start_probationary']) . '</div>';
            $row[] = '<div class="text-left">' . _dhau($aRow['date_end_probationary']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_manager']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_manager_hr']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/probationary_evaluate/view/' . $aRow['id'].'?type='.$type) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';
            $delete = $this->preDeleteProbationaryEvaluate ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/probationary_evaluate/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function view($id){
        $data = [];
        if ($this->type == 1) {
            $data['title'] = lang('dt_view_probationary_evaluate');
        } elseif ($this->type == 2){
            $data['title'] = lang('dt_view_employee_evaluate');
        } elseif ($this->type == 3){
            $data['title'] = lang('dt_view_skill_evaluate');
        }

        $this->db->select('tbl_probationary_evaluate.*,
        tblbranch.name as name_branch
        ');
        $this->db->from('tbl_probationary_evaluate');
        $this->db->from('tblbranch','tblbranch.id = tbl_probationary_evaluate.branch_id');
        $this->db->where('tbl_probationary_evaluate.id',$id);
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

        $this->db->select('tbl_probationary_evaluate_item.*,
                tbl_category_department_kpi.name as name_evaluation_criteria
            ');
        $this->db->from('tbl_probationary_evaluate_item');
        $this->db->join('tbl_category_department_kpi','tbl_category_department_kpi.id = tbl_probationary_evaluate_item.evaluation_criteria_id AND tbl_probationary_evaluate_item.type = 2','left');
        $this->db->where('tbl_probationary_evaluate_item.probationary_evaluate_id',$id);
        $dtItems = $this->db->get()->result_array();

        $data['dtItems'] = $dtItems;
        $data['dtData'] = $dtData;
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/probationary_evaluate/view',$data);
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
        if (!$this->preDeleteProbationaryEvaluate){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_probationary_evaluate.*');
        $this->db->from('tbl_probationary_evaluate');
        $this->db->where('tbl_probationary_evaluate.id',$id);
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
        $success = $this->db->delete('tbl_probationary_evaluate');
        if ($success){
            $this->db->where('probationary_evaluate_id',$id);
            $this->db->delete('tbl_probationary_evaluate_item');
            insertActivityLog([
                'type_parent_obj' => 'probationary_evaluate',
                'table_obj' => 'tbl_probationary_evaluate',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu đánh giá thử việc') . ' [' . $dtData['reference_no'] . ']',
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

    public function changeResult(){
        $id = $this->input->post('id');
        $result_id = $this->input->post('result_id');
        $type = $this->input->post('type');
        $result = '';
        if ($type == 1) {
            $result = 'result';
        } elseif ($type == 2) {
            $result = 'result_manager';
        } elseif ($type == 3) {
            $result = 'result_manager_hr';
        }
        $this->db->where('id',$id);
        $success = $this->db->update('tbl_probationary_evaluate_item',[
            $result => $result_id
        ]);
        if ($success){
            $data['result'] = true;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = false;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }
}