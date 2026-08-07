<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Departmental_procedures extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function insertDepartmental_procedures($data)
    {
        $this->db->insert('tbl_departmental_procedures', $data);
        return $this->db->insert_id();
    }

    public function updateDepartmental_procedures($id, $data)
    {
        $this->db->where('tbl_departmental_procedures.id', $id);
        return $this->db->update('tbl_departmental_procedures', $data);
    }

    public function deleteDepartmental_procedures($id)
    {
        $this->db->where('tbl_departmental_procedures.id', $id);
        return $this->db->delete('tbl_departmental_procedures');
    }

    public function getDepartmental_proceduresById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_departmental_procedures');
        $this->db->where('tbl_departmental_procedures.id', $id);
        return $this->db->get()->row_array();
    }
    function department()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Quy Trình Công Việc Phòng Ban');
        $data['type'] = 'repartment';
        $this->load->view('admin/departmental_procedures/manage', $data);
    }
    function department_system()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Quy Trình Liên Phòng Ban (Hệ Thống)');
        $data['type'] = 'department_system';
        $this->load->view('admin/departmental_procedures/manage', $data);
    }
    public function getDepartmental_procedures()
    {
        $type_search = $this->input->post('type_search');

        $aColumns = [
            'tbl_departmental_procedures.id as id',
            'tbldepartments.name as name_departments',
            'tblroles.name as name_roles',
            'tbl_departmental_procedures.code as code_group',
            'tbl_departmental_procedures.name as name_group',
            'tbl_departmental_procedures.procedures as procedures',
            '"" as actions'
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_departmental_procedures';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_departmental_procedures.department',
            'LEFT JOIN tblroles ON tblroles.roleid = tbl_departmental_procedures.location',
        ];

        array_push($where, " AND tbl_departmental_procedures.type = '" . $type_search . "'");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $quantity_total = 0;
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/departmental_procedures/handling/' . $id . '/' . $type_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/departmental_procedures/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">' . $start . '</div>';
                    // $_data = '<div class="text-center">
                    //     <div class="checkbox checkbox-info">
                    //         <input type="checkbox" name="standard_id[]" id="check-item' . $id . '" value="' . $id . '">
                    //         <label for="check-item' . $id . '"></label>
                    //     </div>
                    // </div>';
                } else if ($v == 'procedures') {
                    $_data = '<div class="">' . $aRow['procedures'] . '</div>';
                }else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function handling($id = 0, $type = '')
    {

        $data = [];
        $departmental_procedures = $id ? $this->getDepartmental_proceduresById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($departmental_procedures['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã quy trình"), 'trim|required|is_unique[tbl_departmental_procedures.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã quy trình"), 'trim|required|is_unique[tbl_departmental_procedures.code]');
            }
            $this->form_validation->set_rules('department', lang("Phòng ban"), 'required');
            $this->form_validation->set_rules('location', lang("Vị trí"), 'required');
            $this->form_validation->set_rules('name', lang("Tên quy trình"), 'required');
            if ($this->form_validation->run() == true) {
                $department = ($this->input->post('department'));
                $location = ($this->input->post('location'));
                $code = ($this->input->post('code'));
                $name = ($this->input->post('name'));
                $procedures = ($this->input->post('procedures', true));
                $option = [
                    'type' => $type,
                    'department' => $department,
                    'location' => $location,
                    'code' => $code,
                    'name' => $name,
                    'procedures' => $procedures,
                ];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->updateDepartmental_procedures($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->insertDepartmental_procedures($option);
                    $standard_id = $ins;
                }

                if (!empty($standard_id)) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        }

        $data['id'] = $id;
        $data['type'] = $type;
        $data['dtData'] = $departmental_procedures;
        $title = '';
        if ($type == 'repartment') {
            $title = $id ? lang('Sửa Định Mức Công Việc Phòng Ban') : lang('Thêm Định Mức Công Việc Phòng Ban');
        } else if ($type == 'department_system') {
            $title = $id ? lang('Sửa Quy Trình Liên Phòng Ban (Hệ Thống)') : lang('Thêm Quy Trình Liên Phòng Ban (Hệ Thống)');
        }
        $data['dtDepartment'] = get_table_where('tbldepartments');
        // $data['dtRoles'] = get_table_where('tblroles');
        $this->db->where('tblroles.type', 0);
        $this->db->where('tblroles.active_role', 1);
        $data['dtRoles'] = $this->db->get('tblroles')->result_array();
        $data['title'] = $title;
        $this->load->view('admin/departmental_procedures/handling', $data);
    }
    
}
