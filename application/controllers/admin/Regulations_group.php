<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Regulations_group extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('rules_group_model');
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Nhóm Quy Định Chung');
        $data['type'] = 2;
        $this->load->view('admin/regulations_group/manage', $data);
    }
    function handling($id = '', $type = 1)
    {

        $data = [];
        $rules_group = $id ? $this->rules_group_model->getRules_groupById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($rules_group['code'] != $this->input->post('code')) {
                    // $this->form_validation->set_rules('code', lang("Mã nhóm nội quy"), 'trim|required|is_unique[tbl_category_regulations.reference_no]');
                    $this->db->where('code', $this->input->post('code'));
                    $this->db->where('type', $type);
                    $check_id = $this->db->get('tbl_category_regulations')->row_array();
                    if (!empty($check_id)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mã trùng vui lòng kiểm tra lại');
                        echo json_encode($data);
                    }
                }
            } else {
                // $this->form_validation->set_rules('code', lang("Mã nhóm nội quy"), 'trim|required|is_unique[tbl_category_regulations.code]');
                $this->db->where('code', $this->input->post('code'));
                $this->db->where('type', $type);
                $check_id = $this->db->get('tbl_category_regulations')->row_array();
                if (!empty($check_id)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Mã trùng vui lòng kiểm tra lại');
                    echo json_encode($data);
                }
            }
            $this->form_validation->set_rules('name', lang("Tên nhóm nội quy"), 'required');
            if ($this->form_validation->run() == true) {
                $code = ($this->input->post('code'));
                $name = ($this->input->post('name'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                ];
                if ($id) {
                    $ins = $this->rules_group_model->updateRules_group($id, $option);
                    $standard_id = $id;
                } else {
                    $option['create_by'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->rules_group_model->insertRules_group($option);
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
        $data['dtData'] = $rules_group;
        $title = '';
        $title = $id ? lang('Sửa Danh Sách Nhóm quy định') : lang('Thêm Danh Sách Nhóm quy định');
        $data['title'] = $title;
        $this->load->view('admin/regulations_group/handling', $data);
    }
    function modal_excel($type = 0)
    {
        $title = '';
        $title = lang('Import Danh Sách Nhóm Nội Quy Chung');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/regulations_group/import_excel', $data);
    }
}