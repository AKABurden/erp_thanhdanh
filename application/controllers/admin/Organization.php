<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Organization extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!has_permission('organization', '', 'view')) {
            access_denied('organization');
        }

        $data['title'] = _l('Sơ đồ tổ chức cấp công ty');
        $this->load->view('admin/organization/index_old', $data);
    }

    public function loadData(){
        $dtData = get_table_where('tbl_organization');
        if (!empty($dtData)) {
            foreach ($dtData as $key => $value) {
                $object_type = $value['object_type'];
                $dtObject = getTypeOrganization($object_type);
                $table = $dtObject['table'] ?? null;
                if (!empty($table)) {
                    $this->db->select('*');
                    $this->db->from($table);
                    if ($object_type == 'department') {
                        $this->db->where('room_id != 0');
                    }
                    $this->db->where($object_type == 'department' ? 'departmentid' : 'id', $value['object_id']);
                    $result = $this->db->get()->row_array();
                    if (!empty($result)) {
                        $dtData[$key]['object_name'] = $result['name'];
                        $dtData[$key]['object_type'] = $dtObject['name'];
                    } else {
                        $dtData[$key]['object_name'] = '';
                        $dtData[$key]['object_type'] = $dtObject['name'] ?? '';
                    }
                } else {
                    $dtData[$key]['object_name'] = '';
                    $dtData[$key]['object_type'] = $dtObject['name'] ?? '';
                }
            }
        }
        $dtData = get_parent_id_referral_level($dtData);
        $data['result'] = true;
        $data['data'] = $dtData ?? [];
        echo json_encode($data);
    }

    public function detail($id = 0,$parent_id = 0){
        if (!has_permission('organization', '', 'create')) {
            accessDenied($js = true);
        }
        if (empty($id)){
            $data['title'] = _l('Thêm mới');
            $data['organization'] = null;
        } else {
            $data['title'] = _l('Cập nhật thông tin');
            $data['organization'] = get_table_where('tbl_organization',['id'=>$id],'','row_array');
            $parent_id = $data['organization']['parent_id'];
            $object_type = $data['organization']['object_type'];
            $dtObject = getTypeOrganization($object_type);
            $table = $dtObject['table'] ?? null;
            if (!empty($table)) {
                $this->db->select('*');
                $this->db->from($table);
                if ($object_type == 'department') {
                    $this->db->where('room_id != 0');
                }
                $result = $this->db->get()->result_array();
            }
            $dtResult = [];
            if (!empty($result)){
                foreach ($result as $key => $value){
                    $dtResult[] = [
                        'id' => $object_type == 'department' ? $value['departmentid'] : $value['id'] ,
                        'name' => $value['name']
                    ];
                }
            }
            $data['dtObject'] = $dtResult;

        }
        if ($this->input->post()){
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (!empty($data['organization']) && $data['organization']['code'] == $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required');
            } else {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_organization.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $type = $this->input->post('type');
                $object_id = $this->input->post('object_id') ?? 0;
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;
                if (!empty($type)){
                    if (empty($object_id)){
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn danh sách liên quan');
                        echo json_encode($data);
                        return;
                    }
                }

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'object_type' => $type,
                    'object_id' => $object_id,
                ];

                if (empty($id)){
                    $options['created_by'] = get_staff_user_id();
                    $options['date_created'] = date('Y-m-d H:i:s');
                    $this->db->insert('tbl_organization', $options);
                    $id = $this->db->insert_id();
                    $success = $id;
                } else {
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_organization',$options);
                }
                if ($success) {

                    $data['result'] = 1;
                    if (empty($id)){
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['message'] = lang('Cập nhật thành công');
                    }
                } else {
                    $data['result'] = 0;
                    if (empty($id)){
                        $data['message'] = lang('Thêm thất bại');
                    } else {
                        $data['message'] = lang('Cập nhật thất bại');
                    }
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        }
        $data['parent_id'] = $parent_id;
        $data['id'] = $id;
        $this->load->view('admin/organization/detail',$data);
    }

    public function removeItem(){
        if (!has_permission('organization', '', 'delete')) {
            $data['result'] = false;
            $data['message'] = lang('access_denied');
            echo json_encode($data);die();
        }
        $organization_id = $this->input->post('organization_id');
        $dtData = get_table_where('tbl_organization',['id' => $organization_id],'','row_array');
        if (empty($dtData)){
            $data['result'] = false;
            $data['message'] = lang('Dữ liệu không tồn tại');
            echo json_encode($data);die();
        }
        $this->db->where('id',$organization_id);
        $success = $this->db->delete('tbl_organization');
        if ($success) {
            $this->db->where('parent_id',$organization_id);
            $this->db->delete('tbl_organization');
            $data['result'] = true;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = false;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function getObjectByType(){
        $object_type = $this->input->post('object_type');
        $dtObject = getTypeOrganization($object_type);
        $table = $dtObject['table'] ?? null;
        $data = [];

        $this->db->select('*');
        $this->db->from($table);
        if ($object_type == 'department'){
            $this->db->where('room_id != 0');
        }
        $result = $this->db->get()->result_array();
        $dtResult = [];
        if (!empty($result)){
            foreach ($result as $key => $value){
                $dtResult[] = [
                    'id' => $object_type == 'department' ? $value['departmentid'] : $value['id'] ,
                    'name' => $value['name']
                ];
            }
        }

        $data['result'] = true;
        $data['data'] = $dtResult;
        echo json_encode($data);
    }
}