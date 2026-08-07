<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Salary3P extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewCategoryGrade = true;
        $this->preAddCategoryGrade = true;
        $this->preEditCategoryGrade = true;
        $this->preDeleteCategoryGrade = true;

        $this->preViewSalary3P = true;
        $this->preAddSalary3P = true;
        $this->preEditSalary3P = true;
        $this->preDeleteSalary3P = true;
    }

    public function index()
    {
        if (!$this->preViewSalary3P) {
            access_denied('salary_3p');
        }
        $data['title'] = _l('Khung lương 3P');
        $this->load->view('admin/salary3P/index', $data);
    }

    public function getSalary3P()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_salary_3p.id as id',
            'tbl_salary_3p.code as code',
            'tblroles.code_role as code_role',
            'tbl_salary_3p.version as version',
            'tbl_salary_3p.status as status',
            'tbl_grade.code as code_grade',
            'tbl_role_level.name as name_role_level',
            'tbl_grade.seniority_from_month as seniority_from_month',
            'tbl_grade.seniority_to_month as seniority_to_month',
            'tbl_salary_3p.coef as coef',
            'tbl_salary_3p.salary_p1 as salary_p1',
            'tbl_salary_3p.salary_p2 as salary_p2',
            'tbl_salary_3p.salary_p3 as salary_p3',
            'tbl_salary_3p.allowed_p3 as allowed_p3',
            'tbl_salary_3p.allowed_p3_note as allowed_p3_note',
            'tbl_salary_3p.effective_from as effective_from',
            'tbl_salary_3p.effective_to as effective_to',
            'tbl_salary_3p.note as note',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_salary_3p';
        $where = [

        ];
        $filter = [];
        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_salary_3p.role_id',
            'INNER JOIN tbl_grade ON tbl_grade.id = tbl_salary_3p.grade_id',
            'INNER JOIN tbl_role_level ON tbl_role_level.id = tbl_salary_3p.role_level_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['code_role'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['version'].'</div>';
            $checked = '';
            if ($aRow['status'] == 1) {
                $checked = 'checked';
            }
            $_data = '<div class="onoffswitch onoffswitch_salary" data-switch-url="' . admin_url() . 'salary3P/changeStatus/'.$aRow['id'].'/'.$aRow['status'].'">
                        <input type="checkbox" data-switch-url="' . admin_url() . 'salary3P/changeStatus" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_new' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_new' . $aRow['id'] . '"></label>
                    </div>';
            $row[] = '<div class="text-left">'.$_data.'</div>';
            $row[] = '<div class="text-left">'.($aRow['name_role_level']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['code_grade']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['seniority_from_month']).' (Tháng)</div>';
            $row[] = '<div class="text-left">'.$aRow['seniority_to_month'].' (Tháng)</div>';
            $row[] = '<div class="text-center">'.$aRow['coef'].'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary_p1']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary_p2']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary_p3']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['allowed_p3']).'</div>';
            $row[] = '<div class="text-left">'.$aRow['allowed_p3_note'].'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['effective_from']) ? _dhau($aRow['effective_from']) : '').'</div>';
            $row[] = '<div class="text-center">'.(!empty($aRow['effective_to']) ? _dhau($aRow['effective_to']) : '').'</div>';
            $row[] = '<div class="text-left">'.($aRow['note']).'</div>';


            $view = '<a class="tnh-modal" href="' . base_url('admin/salary3P/view/' . $aRow['id'].'') . '"><i class="fa fa-eye width-icon-actions"></i> ' . lang('Xem phiếu') . '</a>';
            $copy = '<a class="tnh-modal" href="' . base_url('admin/salary3P/detail/' . $aRow['id'].'/copy') . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Tạo phiên bản mới') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/salary3P/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $copy . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0,$action = '')
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('code', lang("Mã khung lương 3P"), 'required');
            $this->form_validation->set_rules('version', lang("Version"), 'required');
            $this->form_validation->set_rules('effective_from', lang("Ngày hiệu lực"), 'required');
            $this->form_validation->set_rules('grade_id', lang("Danh mục thâm niên"), 'required');
            $this->form_validation->set_rules('role_level_id', lang("Danh mục cấp bậc"), 'required');
            $this->form_validation->set_rules('role_id', lang("Mã vị trí"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $version = $this->input->post('version');
                $effective_from = to_sql_date($this->input->post('effective_from'));
                $effective_to = to_sql_date($this->input->post('effective_to'));
                $grade_id = $this->input->post('grade_id');
                $role_level_id = $this->input->post('role_level_id');
                $note = $this->input->post('note');
                $role_id = $this->input->post('role_id');
                $coef = number_unformat($this->input->post('coef'));
                $salary_p1 = number_unformat($this->input->post('salary_p1'));
                $salary_p2 = number_unformat($this->input->post('salary_p2'));
                $salary_p3 = number_unformat($this->input->post('salary_p3'));
                $allowed_p3 = number_unformat($this->input->post('allowed_p3'));
                $allowed_p3_note = $this->input->post('allowed_p3_note');

                $this->db->where('code',$code);
                $this->db->from('tbl_salary_3p');
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã khung lương 3P đã tồn tại');
                    echo json_encode($data);die();
                }

                $this->db->where('role_id',$role_id);
                $this->db->where('grade_id',$grade_id);
                $this->db->where('role_level_id',$grade_id);
                $this->db->where('version',$version);
                $this->db->from('tbl_salary_3p');
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Version vị trí danh mục thâm niên đã tồn tại');
                    echo json_encode($data);die();
                }


                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'version' => $version,
                    'status' => 0,
                    'coef' => $coef,
                    'salary_p1' => $salary_p1,
                    'salary_p2' => $salary_p2,
                    'salary_p3' => $salary_p3,
                    'allowed_p3' => $allowed_p3,
                    'allowed_p3_note' => $allowed_p3_note,
                    'effective_from' => ($effective_from),
                    'effective_to' => ($effective_to),
                    'note' => $note,
                    'role_id' => $role_id,
                    'grade_id' => $grade_id,
                    'role_level_id' => $role_level_id,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_salary_3p',$option);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id){
                        updateReference('salary_3p');
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    if ($action == 'copy'){
                        $this->db->insert('tbl_salary_3p',$option);
                        $insert_id = $this->db->insert_id();
                        if ($insert_id){
                            updateReference('salary_3p');
                            $data['result'] = 1;
                            $data['message'] = lang('Sao chép thành công');
                        } else {
                            $data['result'] = 0;
                            $data['message'] = lang('Sao chép thất bị');
                        }
                    }
                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddSalary3P){
                accessDenied($js = true);
            }
            $title = lang('Thêm mới khung lương 3P');
        } else {
            if (!$this->preEditSalary3P){
                accessDenied($js = true);
            }
            $title = lang('Tạo phiên bản mới khung lương 3P');
            $dtData = get_table_where('tbl_salary_3p',['id' => $id],'','row_array');

            $this->db->where('salary_3p',$id);
            $this->db->from('tblproduction_report');
            $checkExists = $this->db->get()->row_array();
            if(empty($checkExists)){
                refererModel(lang('Vui lòng tạo báo cáo không phù hợp trước'));
            }
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;
        $data['action'] = $action;

        $this->load->view('admin/salary3P/detail',$data);
    }

    public function changeStatus($id,$status)
    {
        $data = [];
        if (!$this->preEditSalary3P){
            $data['result'] = 0;
            $data['message'] = lang('Không có quyền thực hiện');
            echo json_encode($data);die();
        }
        $this->db->where('id',$id);
        $dtData = $this->db->get('tbl_salary_3p')->row_array();
        if (empty($dtData)){
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy dữ liệu');
            echo json_encode($data);die();
        }
        if (empty($status)){
            $status = 1;
        } else {
            $status = 0;
        }
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_salary_3p', [
            'status' => $status
        ]);

        if ($success) {
            if ($status == 1) {
                $this->db->where('role_id', $dtData['role_id']);
                $this->db->where('grade_id', $dtData['grade_id']);
                $this->db->where('role_level_id', $dtData['role_level_id']);
                $this->db->where('id !=',$id);
                $this->db->update('tbl_salary_3p', [
                    'status' => 0
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

    public function delete($id)
    {
        if (!$this->preDeleteSalary3P){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_salary_3p.*');
        $this->db->from('tbl_salary_3p');
        $this->db->where('tbl_salary_3p.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_salary_3p');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'salary_3p',
                'table_obj' => 'tbl_salary_3p',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa khung lương 3P') . ' [' . $dtData['code'] . ']',
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

    public function category_grade()
    {
        if (!$this->preViewCategoryGrade) {
            access_denied('category_grade');
        }
        $data['title'] = _l('Danh mục thâm niên');
        $this->load->view('admin/salary3P/category_grade', $data);
    }

    public function getCategoryGrade()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_grade.id as id',
            'tbl_grade.code as code',
            'tbl_grade.seniority_from_month as seniority_from_month',
            'tbl_grade.seniority_to_month as seniority_to_month',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_grade';
        $where = [

        ];
        $filter = [];
        $join = [
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['seniority_from_month'].' (Tháng)</div>';
            $row[] = '<div class="text-left">'.$aRow['seniority_to_month'].' (Tháng)</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/salary3P/detail_category_grade/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('Sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/salary3P/delete_category_grade/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_category_grade($id = 0)
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('code', lang("Mã danh mục thâm niên"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $seniority_from_month = number_unformat($this->input->post('seniority_from_month'));
                $seniority_to_month = number_unformat($this->input->post('seniority_to_month'));

                $this->db->where('code',$code);
                $this->db->from('tbl_grade');
                $this->db->where('id !=',$id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã danh mục thâm niên đã tồn tại');
                    echo json_encode($data);die();
                }

                $option = [
                    'code' => $code,
                    'seniority_from_month' => $seniority_from_month,
                    'seniority_to_month' => $seniority_to_month,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_grade',$option);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id){
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    $this->db->where('id',$id);
                    $this->db->update('tbl_grade',$option);
                    $insert_id = $id;
                    if ($insert_id){
                        $data['result'] = 1;
                        $data['message'] = lang('Cập nhập thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Cập nhập thất bị');
                    }
                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddCategoryGrade){
                accessDenied($js = true);
            }
            $title = lang('Thêm mới danh mục thâm niên');
        } else {
            if (!$this->preEditCategoryGrade){
                accessDenied($js = true);
            }
            $title = lang('Chỉnh sửa danh mục thâm niên');
            $dtData = get_table_where('tbl_grade',['id' => $id],'','row_array');
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;

        $this->load->view('admin/salary3P/detail_category_grade',$data);
    }

    public function delete_category_grade($id)
    {
        if (!$this->preDeleteCategoryGrade){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_grade.*');
        $this->db->from('tbl_grade');
        $this->db->where('tbl_grade.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_grade');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'grade',
                'table_obj' => 'tbl_grade',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa danh mục thâm niên') . ' [' . $dtData['code'] . ']',
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

    public function searchCategoryGrade($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_grade.id as id, 
             CONCAT(
                tbl_grade.code,
                " (",
                tbl_grade.seniority_from_month,
                "-",
                tbl_grade.seniority_to_month,
                ,
                " Tháng",
                ")"
            ) AS text,
            tbl_grade.code as code
        ', false);
        $this->db->from('tbl_grade');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_grade.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mã danh mục thâm niên'), 'children' => $pod];
        if (!empty($id)){
            $dtData = get_table_where('tbl_grade',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['code'].' ('.$dtData['seniority_from_month'].'-'.$dtData['seniority_to_month'].' Tháng)'];
        }
        echo json_encode($data);
    }

    public function searchRoleLevel($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_role_level.id as id, 
            tbl_role_level.name as text,
            tbl_role_level.code as code
        ', false);
        $this->db->from('tbl_role_level');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_role_level.code', $term);
            $this->db->or_like('tbl_role_level.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mã danh mục cấp bậc vai trò'), 'children' => $pod];
        if (!empty($id)){
            $dtData = get_table_where('tbl_role_level',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['name']];
        }
        echo json_encode($data);
    }

    public function getCodeAuto()
    {
        $role_id = $this->input->get('role_id');
        $grade_id = $this->input->get('grade_id');
        $role_level_id = $this->input->get('role_level_id');
        $dtRole = get_table_where('tblroles',['roleid' => $role_id],'','row_array');
        $dtGrade = get_table_where('tbl_grade',['id' => $grade_id],'','row_array');
        $dtRoleLevel = get_table_where('tbl_role_level',['id' => $role_level_id],'','row_array');
        $code = ($dtRole['code_role'] ?? '').'-'.$dtRoleLevel['code'].'-'.($dtGrade['code'] ?? '');
        $code = getReferenceNew('salary_3p',$code);

        $this->db->select('version');
        $this->db->from('tbl_salary_3p');
        $this->db->where('role_id', $role_id);
        $this->db->where('grade_id', $grade_id);
        $this->db->where('role_level_id', $role_level_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $row = $this->db->get()->row();

        if (empty($row)) {
            $new_version = 'v1.0';
        } else {
            $num = (float) str_replace('v', '', $row->version); // 1.9
            $new_version = 'v' . number_format($num + 0.1, 1); // v2.0
        }

        $data['code'] = $code;
        $data['version'] = $new_version;
        echo json_encode($data);die();
    }

    public function import()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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
                // $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");

                $total_sheets = $objPHPExcel->getSheetCount();

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('Q');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $count = 0;
                foreach ($arraydata as $key => $value) {

                    $code_role = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $status = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $code_grade = (preg_replace('/\s+/', ' ', trim($value[3])));
                    $coef = (preg_replace('/\s+/', ' ', trim($value[4])));
                    $salary_p1 = (preg_replace('/\s+/', ' ', trim($value[5])));
                    $salary_p2 = (preg_replace('/\s+/', ' ', trim($value[6])));
                    $salary_p3 = (preg_replace('/\s+/', ' ', trim($value[7])));
                    $allowed_p3 = (preg_replace('/\s+/', ' ', trim($value[8])));
                    $allowed_p3_note = (preg_replace('/\s+/', ' ', trim($value[9])));
                    $effective_from = (preg_replace('/\s+/', ' ', trim($value[10])));
                    $effective_to = (preg_replace('/\s+/', ' ', trim($value[11])));
                    $note = (preg_replace('/\s+/', ' ', trim($value[12])));

                    if (gettype($effective_from) == 'double' || gettype($effective_from) == 'int') {
                        $effective_from = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($effective_from));
                    } else if (gettype($effective_from) == 'string') {
                        $effective_from = to_sql_date($effective_from);
                    }
                    if (gettype($effective_to) == 'double' || gettype($effective_to) == 'int') {
                        $effective_to = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($effective_to));
                    } else if (gettype($effective_to) == 'string') {
                        $effective_to = to_sql_date($effective_to);
                    }

                    $this->db->from('tblroles');
                    $this->db->where('code_role',$code_role);
                    $dtRole = $this->db->get()->row_array();
                    if (empty($dtRole)){
                        $errors .= '<div>'.lang('Mã '.$code_role.' vị trí không tồn tại').'</div>';
                        continue;
                    }

                    $this->db->where('code',$code_grade);
                    $this->db->from('tbl_grade');
                    $dtGrade = $this->db->get()->row_array();
                    if (empty($dtGrade)){
                        $errors .= '<div>'.lang('Mã '.$code_grade.' thâm niên không tồn tại').'</div>';
                        continue;
                    }

                    $role_id = $dtRole['roleid'];
                    $grade_id = $dtGrade['id'];

                    $code = ($dtRole['code_role'] ?? '').'-'.($dtGrade['code'] ?? '');
                    $code = getReferenceNew('salary_3p',$code);

                    $this->db->select('version');
                    $this->db->from('tbl_salary_3p');
                    $this->db->where('role_id', $role_id);
                    $this->db->where('grade_id', $grade_id);
                    $this->db->order_by('id', 'DESC');
                    $this->db->limit(1);
                    $row = $this->db->get()->row();

                    if (empty($row)) {
                        $new_version = 'v1.0';
                    } else {
                        $num = (float) str_replace('v', '', $row->version); // 1.9
                        $new_version = 'v' . number_format($num + 0.1, 1); // v2.0
                    }

                    $option = [
                        'date' => date('Y-m-d H:i:s'),
                        'code' => $code,
                        'role_id' => $role_id,
                        'grade_id' => $grade_id,
                        'version' => $new_version,
                        'status' => $status,
                        'coef' => $coef,
                        'salary_p1' => number_unformat($salary_p1),
                        'salary_p2' => number_unformat($salary_p2),
                        'salary_p3' => number_unformat($salary_p3),
                        'allowed_p3' => number_unformat($allowed_p3),
                        'allowed_p3_note' => $allowed_p3_note,
                        'effective_from' => $effective_from,
                        'effective_to' => $effective_to,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tbl_salary_3p',$option);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id){
                        updateReference('salary_3p');
                        if ($status == 1) {
                            $this->db->where('role_id', $role_id);
                            $this->db->where('grade_id', $grade_id);
                            $this->db->where('id !=', $insert_id);
                            $this->db->update('tbl_salary_3p', [
                                'status' => 0
                            ]);
                        }
                        $count ++;
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới thành công ' . $count . ' khung luương 3P',
                    ]
                );
                die();

            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import khung lương 3P');
        $this->load->view('admin/salary3P/import', $data);
    }

    public function view($id)
    {
        $title = lang('Xem khung lương 3P');
        $this->db->select('
            tbl_salary_3p.*,
            tbl_grade.code as code_grade,
            tbl_grade.seniority_from_month as seniority_from_month,
            tbl_grade.seniority_to_month as seniority_to_month,
            tblroles.code_role as code_role,
            tbl_role_level.code as code_role_level,
        ');
        $this->db->from('tbl_salary_3p');
        $this->db->join('tbl_grade','tbl_grade.id = tbl_salary_3p.grade_id');
        $this->db->join('tbl_role_level','tbl_role_level.id = tbl_salary_3p.role_level_id');
        $this->db->join('tblroles','tblroles.roleid = tbl_salary_3p.role_id');
        $this->db->where('tbl_salary_3p.id',$id);
        $dtData = $this->db->get()->row_array();
        $data['title'] = $title;
        $data['dtData'] = $dtData;
        $this->load->view('admin/salary3P/view', $data);
    }
}