<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_salary extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function step_salary(){
        $this->perAddStepSalary = true;
        $perViewStepSalary = true;
        if (!$perViewStepSalary){
            access_denied('step_salary');
        }
        $data['title'] = _l('dt_step_salary');
        $this->load->view('admin/category_salary/step_salary', $data);
    }

    public function getStepSalary(){
        $perEditStepSalary = true;
        $perDeleteStepSalary = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_step_salary.id as id',
            'tbl_step_salary.code as code',
            'tbl_step_salary.name as name',
            'tbl_step_salary.coefficient as coefficient',
            'tbl_step_salary.salary as salary',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_step_salary';
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
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['coefficient'].'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary']).'</div>';

            $edit = $perEditStepSalary ? '<a class="tnh-modal" href="' . base_url('admin/category_salary/detail_step_salary/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $perDeleteStepSalary ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_salary/delete_step_salary/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
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

    public function detail_step_salary($id = 0){
        $data = [];
        $perEditStepSalary = true;
        $perAddStepSalary = true;
        $this->db->select('tbl_step_salary.*');
        $this->db->from('tbl_step_salary');
        $this->db->where('tbl_step_salary.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_step_salary.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_step_salary.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $coefficient = number_unformat($this->input->post('coefficient'));
                    $salary = number_unformat($this->input->post('salary'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'coefficient' => $coefficient,
                        'salary' => $salary,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_step_salary',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'step_salary',
                            'table_obj' => 'tbl_step_salary',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới bậc lương') . ' [' . $code . ']',
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
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $coefficient = number_unformat($this->input->post('coefficient'));
                    $salary = number_unformat($this->input->post('salary'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'coefficient' => $coefficient,
                        'salary' => $salary,
                        'note' => $note,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_step_salary',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'step_salary',
                            'table_obj' => 'tbl_step_salary',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa bậc lương') . ' [' . $dtData['code'] . ']',
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
                if (!$perAddStepSalary){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_step_salary');
            } else {
                if (!$perEditStepSalary){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_step_salary');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/category_salary/detail_step_salary',$data);
    }

    public function delete_step_salary($id){
        $preDeleteStepSalary = true;
        if (!$preDeleteStepSalary){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_step_salary.*');
        $this->db->from('tbl_step_salary');
        $this->db->where('tbl_step_salary.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_step_salary');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'step_salary',
                'table_obj' => 'tbl_step_salary',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa bậc lương') . ' [' . $dtData['code'] . ']',
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

    public function coefficient_salary(){
        $this->perAddCoeficientSalary = true;
        $perViewCoeficientSalary = true;
        if (!$perViewCoeficientSalary){
            access_denied('coefficient_salary');
        }
        $data['title'] = _l('dt_coefficient_salary');
        $this->load->view('admin/category_salary/coefficient_salary', $data);
    }

    public function getCoefficientSalary(){
        $perEditCoefficientSalary = true;
        $perDeleteCoefficientSalary = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_coefficient_salary.id as id',
            'tbl_coefficient_salary.code as code',
            'tbl_coefficient_salary.name as name',
            'tbl_coefficient_salary.coefficient as coefficient',
            'tbl_coefficient_salary.type as type',
            'tblroles.name as name_role',
            'tbl_coefficient_salary.note as note',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_coefficient_salary';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid = tbl_coefficient_salary.role_id'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['coefficient'].'</div>';
            $row[] = '<div class="text-center">'.($aRow['type'] == 1 ? '<div class="label label-info">Chức vụ</div>' : '<div class="label label-danger">Trách nhiệm</div>').'</div>';
            $row[] = '<div class="text-left">'.($aRow['name_role']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['note']).'</div>';

            $edit = $perEditCoefficientSalary ? '<a class="tnh-modal" href="' . base_url('admin/category_salary/detail_coefficient_salary/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $perDeleteCoefficientSalary ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_salary/delete_coefficient_salary/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
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

    public function detail_coefficient_salary($id = 0){
        $data = [];
        $perEditCoefficientSalary = true;
        $perAddCoefficientSalary = true;
        $this->db->select('tbl_coefficient_salary.*');
        $this->db->from('tbl_coefficient_salary');
        $this->db->where('tbl_coefficient_salary.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_coefficient_salary.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_coefficient_salary.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('type', lang("Loại"), 'required');
            $this->form_validation->set_rules('role_id', lang("Chức vụ"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $coefficient = number_unformat($this->input->post('coefficient'));
                    $type = ($this->input->post('type'));
                    $role_id = ($this->input->post('role_id'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'coefficient' => $coefficient,
                        'type' => $type,
                        'role_id' => $role_id,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_coefficient_salary',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        $this->db->from('tblstaff');
                        $this->db->where('tblstaff.role',$role_id);
                        $dtStaff = $this->db->get()->result_array();
                        $arrUpdateStaff = [];
                        if (!empty($dtStaff)){
                            foreach ($dtStaff as $key => $value){
                                if ($type == 1){
                                    $arrUpdateStaff[] = [
                                        'staffid' => $value['staffid'],
                                        'coefficient_position' => $coefficient
                                    ];
                                } else {
                                    $arrUpdateStaff[] = [
                                        'staffid' => $value['staffid'],
                                        'coefficient_responsibility' => $coefficient
                                    ];
                                }
                            }
                        }
                        if (!empty($arrUpdateStaff)){
                            $this->db->update_batch('tblstaff',$arrUpdateStaff,'staffid');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'coefficient_salary',
                            'table_obj' => 'tbl_coefficient_salary',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới hệ số lương') . ' [' . $code . ']',
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
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $coefficient = number_unformat($this->input->post('coefficient'));
                    $type = ($this->input->post('type'));
                    $role_id = ($this->input->post('role_id'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'type' => $type,
                        'role_id' => $role_id,
                        'coefficient' => $coefficient,
                        'note' => $note,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_coefficient_salary',$fields);
                    if ($success){
                        $this->db->from('tblstaff');
                        $this->db->where('tblstaff.role',$role_id);
                        $dtStaff = $this->db->get()->result_array();
                        $arrUpdateStaff = [];
                        if (!empty($dtStaff)){
                            foreach ($dtStaff as $key => $value){
                                if ($type == 1){
                                    $arrUpdateStaff[] = [
                                        'staffid' => $value['staffid'],
                                        'coefficient_position' => $coefficient
                                    ];
                                } else {
                                    $arrUpdateStaff[] = [
                                        'staffid' => $value['staffid'],
                                        'coefficient_responsibility' => $coefficient
                                    ];
                                }
                            }
                        }
                        if (!empty($arrUpdateStaff)){
                            $this->db->update_batch('tblstaff',$arrUpdateStaff,'staffid');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'coefficient_salary',
                            'table_obj' => 'tbl_coefficient_salary',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa hệ số lương') . ' [' . $dtData['code'] . ']',
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
                if (!$perAddCoefficientSalary){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_coefficient_salary');
            } else {
                if (!$perEditCoefficientSalary){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_coefficient_salary');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/category_salary/detail_coefficient_salary',$data);
    }

    public function delete_coefficient_salary($id){
        $preDeleteCoefficientSalary = true;
        if (!$preDeleteCoefficientSalary){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_coefficient_salary.*');
        $this->db->from('tbl_coefficient_salary');
        $this->db->where('tbl_coefficient_salary.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_coefficient_salary');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'coefficient_salary',
                'table_obj' => 'tbl_coefficient_salary',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa hệ số lương') . ' [' . $dtData['code'] . ']',
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

    public function category_permission(){
        $this->perAddCategoryPermission = true;
        $perViewCategoryPermission = true;
        if (!$perViewCategoryPermission){
            access_denied('category_permission');
        }
        $data['title'] = _l('dt_category_permission');
        $this->load->view('admin/category_salary/category_permission', $data);
    }

    public function getCategoryPermission(){
        $perEditCategoryPermission = true;
        $perDeleteCategoryPermission = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_category_permission.id as id',
            'tbl_category_permission.code as code',
            'tbl_category_permission.name as name',
            'tbl_category_permission.note as note',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_category_permission';
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
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['note']).'</div>';

            $edit = $perEditCategoryPermission ? '<a class="tnh-modal" href="' . base_url('admin/category_salary/detail_category_permission/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $perDeleteCategoryPermission ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_salary/delete_category_permission/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
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

    public function detail_category_permission($id = 0){
        $data = [];
        $perEditCategoryPermission = true;
        $perAddCategoryPermission = true;
        $this->db->select('tbl_category_permission.*');
        $this->db->from('tbl_category_permission');
        $this->db->where('tbl_category_permission.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_category_permission.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_category_permission.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_category_permission',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'category_permission',
                            'table_obj' => 'tbl_category_permission',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới nhóm phép') . ' [' . $code . ']',
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
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'note' => $note,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_category_permission',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'category_permission',
                            'table_obj' => 'tbl_category_permission',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa nhóm phép') . ' [' . $dtData['code'] . ']',
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
                if (!$perAddCategoryPermission){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_category_permission');
            } else {
                if (!$perEditCategoryPermission){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_category_permission');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/category_salary/detail_category_permission',$data);
    }

    public function delete_category_permission($id){
        $preDeleteCategoryPermission = true;
        if (!$preDeleteCategoryPermission){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_category_permission.*');
        $this->db->from('tbl_category_permission');
        $this->db->where('tbl_category_permission.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->from('tbl_permission');
        $this->db->where('tbl_permission.category_permission_id',$id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)){
            $data['result'] = 0;
            $data['message'] = lang('Nhóm phép đã được sử dụng bên phép không thể xóa!');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_category_permission');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'category_permission',
                'table_obj' => 'tbl_category_permission',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa nhóm phép') . ' [' . $dtData['code'] . ']',
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

		public function permission(){
        $this->perAddPermission = true;
        $perViewPermission = true;
        if (!$perViewPermission){
            access_denied('permission');
        }
        $data['title'] = _l('dt_permission');
        $this->load->view('admin/category_salary/permission', $data);
    }

    public function getPermission(){
        $perEditPermission = true;
        $perDeletePermission = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_permission.id as id',
            'tbl_permission.code as code',
            'tbl_permission.name as name',
            'tbl_category_permission.name as name_category',
            'tbl_permission.day_off as day_off',
            'tbl_permission.conditions as conditions',
            'tbl_permission.receive_salary as receive_salary',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_permission';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_category_permission ON tbl_category_permission.id = tbl_permission.category_permission_id'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['name_category']).'</div>';
            $row[] = '<div class="text-center">'.($aRow['day_off']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['conditions']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['receive_salary']).'</div>';

            $edit = $perEditPermission ? '<a class="tnh-modal" href="' . base_url('admin/category_salary/detail_permission/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $perDeletePermission ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_salary/delete_permission/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
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

    public function detail_permission($id = 0){
        $data = [];
        $perEditPermission = true;
        $perAddPermission  = true;
        $this->db->select('tbl_permission.*');
        $this->db->from('tbl_permission');
        $this->db->where('tbl_permission.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_permission.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_permission.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $category_permission_id = ($this->input->post('category_permission_id'));
                    $receive_salary = ($this->input->post('receive_salary'));
                    $conditions = ($this->input->post('conditions'));
                    $day_off = ($this->input->post('day_off'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'category_permission_id' => $category_permission_id,
                        'receive_salary' => $receive_salary,
                        'conditions' => $conditions,
                        'day_off' => $day_off,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_permission',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'permission',
                            'table_obj' => 'tbl_permission',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới phép') . ' [' . $code . ']',
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
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $category_permission_id = ($this->input->post('category_permission_id'));
                    $receive_salary = ($this->input->post('receive_salary'));
                    $conditions = ($this->input->post('conditions'));
                    $day_off = ($this->input->post('day_off'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'category_permission_id' => $category_permission_id,
                        'receive_salary' => $receive_salary,
                        'conditions' => $conditions,
                        'day_off' => $day_off,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_permission',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'permission',
                            'table_obj' => 'tbl_permission',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa phép') . ' [' . $dtData['code'] . ']',
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
                if (!$perAddPermission){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_permission');
            } else {
                if (!$perEditPermission){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_permission');
            }
        }
        $data['id'] = $id;
        $data['dtCategoryPermission'] = get_table_where('tbl_category_permission');
        $this->load->view('admin/category_salary/detail_permission',$data);
    }

    public function delete_permission($id){
        $preDeletePermission = true;
        if (!$preDeletePermission){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_permission.*');
        $this->db->from('tbl_permission');
        $this->db->where('tbl_permission.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_permission');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'permission',
                'table_obj' => 'tbl_permission',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa phép') . ' [' . $dtData['code'] . ']',
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

    public function contract_labor(){
        $this->perAddContractLabor = true;
        $perViewContractLabor = true;
        if (!$perViewContractLabor){
            access_denied('contract_labor');
        }
        $data['title'] = _l('dt_contract_labor');
        $this->load->view('admin/category_salary/contract_labor', $data);
    }

    public function getContractLabor(){
        $perEditContractLabor = true;
        $perDeletePermission = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_contract_labor.id as id',
            'tbl_contract_labor.code as code',
            'tblstaff.code as code_staff',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff',
            'tbl_type_contract.name as name_type_contract',
            'tbl_contract_labor.salary_basic as salary_basic',
            'tbl_contract_labor.salary_position as salary_position',
            'tbl_contract_labor.date_probation as date_probation',
            'tbl_contract_labor.date_sign_contract as date_sign_contract',
            'tbl_contract_labor.date_start as date_start',
            'tbl_contract_labor.date_end as date_end',
            'tbl_contract_labor.date_sign as date_sign',
            'tbl_contract_labor.status as status',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_contract_labor';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_type_contract ON tbl_type_contract.id = tbl_contract_labor.type_contract_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_contract_labor.staff_id',
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_contract_labor.user_status as user_status',
            'tbl_contract_labor.date_status as date_status',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['code_staff'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['name_staff']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['name_type_contract']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary_basic']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary_position']).'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_probation']) ? _dhau($aRow['date_probation']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_sign_contract']) ? _dhau($aRow['date_sign_contract']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '').'</div>';
            $row[] = (!empty($aRow['date_sign']) ? _dhau($aRow['date_sign']) : '');

            $active_manager = '';
            $staff_manager = '';
            $status = $aRow['status'];
            $agree_manager = '';
            if ($status == 0) {
                $html = "<p>
                    <a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                    <a id='reject' value='2' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Không Duyệt</a>
                    <button class='btn po-close btn-icon'>Thoát</button></p>";
                
                $agree_manager = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . htmlspecialchars($html, ENT_QUOTES) . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
                $staff_manager = '';
            } elseif ($status == 1) {
                $html = "<p>
                    <a id='reject' value='2' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Không Duyệt</a>
                    <button class='btn po-close btn-icon'>Thoát</button></p>";
                
                $agree_manager = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . htmlspecialchars($html, ENT_QUOTES) . '" class="label label-success po" data-original-title="Trạng thái">Đã duyệt</span></div>';
                $staff_manager = '' . staff_profile_image($aRow['user_status'], ['staff-profile-image-small-2x mbot5'], 'small') . ''.get_staff_full_name($aRow['user_status']).' <br/> Vào lúc: ' . _dt($aRow['date_status']) . '';
                $active_manager = 'active';
                
            } elseif ($status == 2) {
                $html = "<p>
                    <a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                    <button class='btn po-close btn-icon'>Thoát</button></p>";
                
                $agree_manager = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . htmlspecialchars($html, ENT_QUOTES) . '" class="label label-danger po" data-original-title="Trạng thái">Không duyệt</span></div>';
                $staff_manager = '' . staff_profile_image($aRow['user_status'], ['staff-profile-image-small-2x mbot5'], 'small') . ' '.get_staff_full_name($aRow['user_status']).' <br/> Vào lúc: ' . _dt($aRow['date_status']) . '';
                $active_manager = 'active';
            } else {
                $agree_manager = '<div class="mbot5"><span class="label label-default">Không xác định</span></div>';
                $staff_manager = '';
            }

            $process = '<div>
                <div class="wrap-content-process  ' . $active_manager . '">
                    <div class="wrap-step-process"></div>
                        <div class="wrap-title-process">
                            ' . lang('BOD duyệt') . '
                        </div>
                        <div class="wrap-title-process" style="">
                            <div style="margin-top: 5px;">' . $agree_manager . '</div>
                            ' . $staff_manager . '
                        </div>
                    </div>
                </div>
                
            </div>';
            $row[] = $process;

            $edit = $perEditContractLabor ? '<a class="tnh-modal" href="' . base_url('admin/category_salary/detail_contract_labor/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $perDeletePermission ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_salary/delete_contract_labor/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
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

    public function detail_contract_labor($id = 0){
        $data = [];
        $perEditContractLabor = true;
        $perAddContractLabor  = true;
        $this->db->select('tbl_contract_labor.*');
        $this->db->from('tbl_contract_labor');
        $this->db->where('tbl_contract_labor.id',$id);
        $dtData = $this->db->get()->row_array();

        if ($dtData['status'] == 1) {
            refererModel(lang('Hợp đồng đã được duyệt, không thể chỉnh sửa'));
            return;
        }

        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_contract_labor.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_contract_labor.code]');
            }
            $this->form_validation->set_rules('type_contract_id', lang("Loại hợp đồng"), 'required');
            $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $staff_id = ($this->input->post('staff_id'));
                    $type_contract_id = ($this->input->post('type_contract_id'));
                    $salary_basic = !empty($this->input->post('salary_basic')) ? number_unformat($this->input->post('salary_basic')) : 0;
                    $salary_position = !empty($this->input->post('salary_position')) ? number_unformat($this->input->post('salary_position')) : 0;
                    $date_start = !empty($this->input->post('date_start')) ? to_sql_date($this->input->post('date_start')) : null;
                    $date_end = !empty($this->input->post('date_end')) ? to_sql_date($this->input->post('date_end')) : null;
                    $date_sign = !empty($this->input->post('date_sign')) ? to_sql_date($this->input->post('date_sign')) : null;
                    $date_probation = !empty($this->input->post('date_probation')) ? to_sql_date($this->input->post('date_probation')) : null;
                    $date_sign_contract = !empty($this->input->post('date_sign_contract')) ? to_sql_date($this->input->post('date_sign_contract')) : null;
                    $data_staff = get_table_where('tblstaff',['staffid' => $staff_id], '', 'row', '', 'status_work');
                    $status_work = $data_staff->status_work;
                    $status_work_old = !empty($dtData) ? $dtData['status_work'] : '';

                    $fields = [
                        'code' => $code,
                        'staff_id' => $staff_id,
                        'type_contract_id' => $type_contract_id,
                        'salary_basic' => $salary_basic,
                        'salary_position' => $salary_position,
                        'date_start' => $date_start,
                        'date_end' => $date_end,
                        'date_sign' => $date_sign,
                        'date_probation' => $date_probation,
                        'date_sign_contract' => $date_sign_contract,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'status_work_old' => $status_work_old,
                    ];
                    $this->db->insert('tbl_contract_labor',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'contract_labor',
                            'table_obj' => 'tbl_contract_labor',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới hợp đồng lao động') . ' [' . $code . ']',
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
                    $code = ($this->input->post('code'));
                    $staff_id = ($this->input->post('staff_id'));
                    $type_contract_id = ($this->input->post('type_contract_id'));
                    $salary_basic = !empty($this->input->post('salary_basic')) ? number_unformat($this->input->post('salary_basic')) : 0;
                    $salary_position = !empty($this->input->post('salary_position')) ? number_unformat($this->input->post('salary_position')) : 0;
                    $date_start = !empty($this->input->post('date_start')) ? to_sql_date($this->input->post('date_start')) : null;
                    $date_end = !empty($this->input->post('date_end')) ? to_sql_date($this->input->post('date_end')) : null;
                    $date_sign = !empty($this->input->post('date_sign')) ? to_sql_date($this->input->post('date_sign')) : null;
                    $date_probation = !empty($this->input->post('date_probation')) ? to_sql_date($this->input->post('date_probation')) : null;
                    $date_sign_contract = !empty($this->input->post('date_sign_contract')) ? to_sql_date($this->input->post('date_sign_contract')) : null;
                    $fields = [
                        'code' => $code,
                        'staff_id' => $staff_id,
                        'type_contract_id' => $type_contract_id,
                        'salary_basic' => $salary_basic,
                        'salary_position' => $salary_position,
                        'date_start' => $date_start,
                        'date_end' => $date_end,
                        'date_sign' => $date_sign,
                        'date_probation' => $date_probation,
                        'date_sign_contract' => $date_sign_contract,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_contract_labor',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'contract_labor',
                            'table_obj' => 'tbl_contract_labor',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa hợp đồng lao động') . ' [' . $dtData['code'] . ']',
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
                if (!$perAddContractLabor){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_contract_labor');
            } else {
                if (!$perEditContractLabor){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_contract_labor');
            }
        }
        $data['id'] = $id;
        $data['dtTypeContract'] = get_table_where('tbl_type_contract');
        $data['dtStaff'] = get_table_where('tblstaff',['active' => 1]);
        $this->load->view('admin/category_salary/detail_contract_labor',$data);
    }

    public function delete_contract_labor($id){
        $preDeleteContractLabor = true;
        if (!$preDeleteContractLabor){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_contract_labor.*');
        $this->db->from('tbl_contract_labor');
        $this->db->where('tbl_contract_labor.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Hợp đồng đã được duyệt, không thể xóa');
            echo json_encode($data); die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_contract_labor');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'contract_labor',
                'table_obj' => 'tbl_contract_labor',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa hợp đồng lao động') . ' [' . $dtData['code'] . ']',
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

    public function change_status_contract_labor(){
        $data = [];
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        if (empty($id)){
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy dữ liệu');
            echo json_encode($data);
            die();
        }
        
        $this->db->select('tbl_contract_labor.*');
        $this->db->from('tbl_contract_labor');
        $this->db->where('tbl_contract_labor.id', $id);
        $dtData = $this->db->get()->row_array();
        
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy hợp đồng lao động');
            echo json_encode($data);
            die();
        }
        
        $updateData = [
            'status' => $status,
            'date_status' => date('Y-m-d H:i:s'),
            'user_status' => get_staff_user_id()
        ];
        
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_contract_labor', $updateData);

        $status_work_old = $dtData['status_work_old'];
        $type_contract_id = $dtData['type_contract_id'];
        
        if ($success){
            $statusText = '';
            if ($status == 1){
                $status_work = $type_contract_id == 1 ? 0 : 1;
                $statusText = 'Đã duyệt';
                $this->db->where('staffid', $dtData['staff_id']);
                $this->db->update('tblstaff', ['status_work' => $status_work]);
            } elseif ($status == 2){
                $statusText = 'Không duyệt';
                $this->db->where('staffid', $dtData['staff_id']);
                $this->db->update('tblstaff', ['status_work' => $status_work_old]);
            }
            
            insertActivityLog([
                'type_parent_obj' => 'contract_labor',
                'table_obj' => 'tbl_contract_labor',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Thay đổi trạng thái hợp đồng lao động') . ' [' . $dtData['code'] . '] - ' . $statusText,
                'actions' => 'update'
            ]);
            
            $data['result'] = 1;
            $data['message'] = lang('Cập nhật trạng thái thành công: ' . $statusText);
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Cập nhật trạng thái thất bại');
        }
        
        echo json_encode($data);
    }

    public function import_contract_labor()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

            $tmpFile = $_FILES['file']['tmp_name'];
            $ext = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['XLS', 'XLSX'])) {
                echo json_encode(['success' => false, 'message' => 'File không hợp lệ']);
                die;
            }

            $excel = PHPExcel_IOFactory::load($tmpFile);
            $sheet = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $count = 0;
            for ($row = 2; $row <= $highestRow; $row++) {

                $code = trim($sheet->getCell("A$row")->getValue()) ?? NULL;
                if(empty($code)) {
                    $code = 'HDLD-' . time() . '-' . $row;
                }


                /** ---------------- Nhân viên đề xuất ---------------- */
                $staffUsername = trim($sheet->getCell("B$row")->getValue());
                $staff = $this->db
//                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
                    ->where('code', $staffUsername)
                    ->get('tblstaff')
                    ->row();
                if (!$staff) continue;

                /** ---------------- JD + Version ---------------- */
                $code_type_contract  = trim($sheet->getCell("C$row")->getValue());

                $type_contract = $this->db
                    ->where('code', $code_type_contract)
                    ->get('tbl_type_contract')
                    ->row();
                if (!$type_contract) continue;

                $salary_basic = trim($sheet->getCell("D$row")->getValue());
                $salary_position = trim($sheet->getCell("E$row")->getValue());


                $date_probation = $sheet->getCell("F$row")->getValue();
                if(is_numeric($date_probation)) {
                    $unix = ($date_probation - 25569) * 86400;
                    $date_probation = date('Y-m-d', $unix);
                }
                else {
                    $date_probation = to_sql_date($sheet->getCell("F$row")->getValue(), true);
                }


                $date_sign_contract = $sheet->getCell("G$row")->getValue();
                if(is_numeric($date_sign_contract)) {
                    $unix = ($date_sign_contract - 25569) * 86400;
                    $date_sign_contract = date('Y-m-d', $unix);
                }
                else {
                    $date_sign_contract = to_sql_date($sheet->getCell("G$row")->getValue(), true);
                }


                $date_start = $sheet->getCell("H$row")->getValue();
                if(is_numeric($date_start)) {
                    $unix = ($date_start - 25569) * 86400;
                    $date_start = date('Y-m-d', $unix);
                }
                else {
                    $date_start = to_sql_date($sheet->getCell("H$row")->getValue(), true);
                }



                $date_end = $sheet->getCell("I$row")->getValue();
                if(is_numeric($date_end)) {
                    $unix = ($date_end - 25569) * 86400;
                    $date_end = date('Y-m-d', $unix);
                }
                else {
                    $date_end = to_sql_date($sheet->getCell("I$row")->getValue(), true);
                }

                $date_sign = $sheet->getCell("J$row")->getValue();
                if(is_numeric($date_sign)) {
                    $unix = ($date_sign - 25569) * 86400;
                    $date_sign = date('Y-m-d', $unix);
                }
                else {
                    $date_sign = to_sql_date($sheet->getCell("J$row")->getValue(), true);
                }



                $insertData = [
                    'code'          => $code,
                    'staff_id'          => $staff->staffid ?? 0,
                    'type_contract_id'  => $type_contract->id ?? 0,
                    'salary_basic'      => number_format_data($salary_basic, false),
                    'salary_position'   => number_format_data($salary_position, false),
                    'date_probation'   => $date_probation ?? NULL,
                    'date_sign_contract'   => $date_sign_contract?? NULL,
                    'date_start'   => $date_start?? NULL,
                    'date_end'   => $date_end?? NULL,
                    'date_sign'   => $date_sign?? NULL,
                ];



                // tránh import trùng mã
                if(!empty($code)) {
                    if ($this->db->where('code', $code)->get('tbl_contract_labor')->row()) {
                        continue;
                    }
                }
                $success = $this->db->insert('tbl_contract_labor', $insertData);
                if(!empty($success)) {
                    $id = $this->db->insert_id();
                }
                $count++;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Import thành công ' . $count . ' hợp đồng lao động'
            ]);
            die;
        }
        $data['title'] = _l('Import danh sách yêu cầu tuyển dụng');
        $this->load->view('admin/category_salary/import_contract_labor', $data);
    }
}