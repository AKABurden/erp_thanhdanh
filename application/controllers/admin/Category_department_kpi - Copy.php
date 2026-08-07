<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_department_kpi extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewCategoryDepartmentKpi = true;
        $this->preAddCategoryDepartmentKpi = true;
        $this->preEditCategoryDepartmentKpi = true;
        $this->preDeleteCategoryDepartmentKpi = true;
    }

    public function index()
    {
        if (!$this->preViewCategoryDepartmentKpi) {
            access_denied();
        }
        $data['title'] = _l('dt_category_department_kpi');
        $this->load->view('admin/category_department_kpi/index', $data);
    }

    public function getCategoryDepartmentKpi()
    {

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_category_department_kpi.id as id',
            'tbldepartments.name as name_department',
            'tbl_category_department_kpi.name as name',
            'tbl_category_department_kpi.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_category_department_kpi';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbldepartments ON tbldepartments.departmentid = tbl_category_department_kpi.department_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [

        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_department']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';


            $edit = $this->preEditCategoryDepartmentKpi ? '<a class="tnh-modal" href="' . base_url('admin/category_department_kpi/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';
            $delete = $this->preDeleteCategoryDepartmentKpi ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_department_kpi/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
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

    public function detail($id = 0)
    {
        $data = [];
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('department_id', lang("Phòng ban"), 'required');
                $this->form_validation->set_rules('name', lang("Danh mục đánh giá"), 'required');
                if ($this->form_validation->run() == true) {
                    $department_id = $this->input->post('department_id');
                    $name = $this->input->post('name');
                    $fields = [
                        'department_id' => $department_id,
                        'name' => $name,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_category_department_kpi', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
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
                echo json_encode($data);
                die();
            } else {
                $this->form_validation->set_rules('department_id', lang("Phòng ban"), 'required');
                $this->form_validation->set_rules('name', lang("Danh mục đánh giá"), 'required');
                if ($this->form_validation->run() == true) {
                    $department_id = $this->input->post('department_id');
                    $name = $this->input->post('name');
                    $fields = [
                        'department_id' => $department_id,
                        'name' => $name,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_category_department_kpi', $fields);
                    if ($success) {
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
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$this->preAddCategoryDepartmentKpi) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_category_department_kpi');
            } else {
                if (!$this->preEditCategoryDepartmentKpi) {
                    accessDenied(true);
                }
                $this->db->select('
                    tbl_category_department_kpi.*,
                ');
                $this->db->from('tbl_category_department_kpi');
                $this->db->where('tbl_category_department_kpi.id', $id);
                $dtData = $this->db->get()->row_array();

                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_category_department_kpi');
            }
        }
        $data['id'] = $id;
        $dtDepartment = get_table_where('tbldepartments', ['type' => 0, 'active_departments' => 1]);
        $data['dtDepartment'] = $dtDepartment;
        $this->load->view('admin/category_department_kpi/detail', $data);
    }

    public function delete($id){
        if (!$this->preDeleteCategoryDepartmentKpi){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_category_department_kpi.*');
        $this->db->from('tbl_category_department_kpi');
        $this->db->where('tbl_category_department_kpi.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_category_department_kpi');
        if ($success){
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }
}