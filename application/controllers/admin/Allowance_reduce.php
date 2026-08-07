<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Allowance_reduce extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = _l('Phụ cấp - Giảm trừ');
        $this->load->view('admin/allowance_reduce/index', $data);
    }

    public function add_allowance_reduce()
    {
        $data = [];
        if ($this->input->post())
        {
            $this->form_validation->set_rules('name', lang("name"), 'trim|required|is_unique[tbl_allowance_reduce.name]');
            $this->form_validation->set_rules('type', lang("Loại"), 'required');
            if ($this->form_validation->run() == true)
            {
                $name = $this->input->post('name');
                $type = $this->input->post('type');

                $options = [
                    'name' => $name,
                    'type' => $type,
                    'date_created' => date('Y-m-d H:i:s'),
                    'staff_id' => get_staff_user_id(),
                ];

                $this->db->insert('tbl_allowance_reduce',$options);
                $id = $this->db->insert_id();
                if ($id) {
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
        } else {
            $this->load->view('admin/allowance_reduce/add', $data);
        }
    }

    public function edit_callowance_reduce($id)
    {
        $data = [];
        $allowance_reduce = get_table_where('tbl_allowance_reduce',['id' => $id],'','row_array');
        if ($this->input->post())
        {
            if ($allowance_reduce['name'] != $this->input->post('name')) {
                $this->form_validation->set_rules('name', lang("name"), 'trim|required|is_unique[tbl_allowance_reduce.name]');
            }
            $this->form_validation->set_rules('type', lang("Loại"), 'required');
            if ($this->form_validation->run() == true)
            {
                $name = $this->input->post('name');
                $type = $this->input->post('type');

                $options = [
                    'name' => $name,
                    'type' => $type,
                ];

                $this->db->where('id',$id);
                $success = $this->db->update('tbl_allowance_reduce',$options);
                if ($success) {
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
        } else {
            $data['allowance_reduce'] = $allowance_reduce;
            $this->load->view('admin/allowance_reduce/edit', $data);
        }
    }

    function getAllowanceReduce()
    {
        $this->datatables->select("
            tbl_allowance_reduce.id as id,
            tbl_allowance_reduce.name as name,
            tbl_allowance_reduce.type as type,
            ", FALSE)
            ->from('tbl_allowance_reduce');

        $this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url().'admin/allowance_reduce/edit_callowance_reduce/$1"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\''.base_url('admin/allowance_reduce/delete_allowance_reduce/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
                        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
        $result = json_decode($this->datatables->generate());
        echo (json_encode($result));
    }

    function delete_allowance_reduce($id)
    {
        $data = [];
        if ($id) {
            $dtData = get_table_where('tbl_allowance_reduce',['id' => $id],'','row_array');
            $arrDelete = [ALLOWANCE_DH,ALLOWANCE_PCCC,ALLOWANCE_FSC];
            if (in_array($dtData['id'],$arrDelete)){
                $data['result'] = 0;
                $data['message'] = lang('Phụ cấp - giảm trừ này mặc định của hệ thống không thể xóa !');
                echo json_encode($data);die();
            }
            $this->db->from('tbl_salary_allowance');
            $this->db->where('category_id',$id);
            $checkAllowance =$this->db->count_all_results();

            $this->db->from('tbl_salary_reduce');
            $this->db->where('category_id',$id);
            $checkReduce =$this->db->count_all_results();

            $this->db->from('tbl_staff_allowance');
            $this->db->where('category_id',$id);
            $checkStaffAllowance =$this->db->count_all_results();

            $this->db->from('tbl_staff_reduce');
            $this->db->where('category_id',$id);
            $checkStaffReduce =$this->db->count_all_results();

            if (!empty($checkAllowance) || !empty($checkReduce) || !empty($checkStaffAllowance) || !empty($checkStaffReduce)){
                $data['result'] = 0;
                $data['message'] = lang('Phụ cấp - giảm trừ này đã được sử dụng không thể xóa !');
                echo json_encode($data);die();
            }
            $this->db->where('id',$id);
            $success = $this->db->delete('tbl_allowance_reduce');
            if ($success) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function searchAllowanceReduce($id = false){
        $data = [];
        if ($this->input->get())
        {
            $q = $this->input->get('q');
            $type = $this->input->get('types');
            $limit = 50;
            $this->db->select('tbl_allowance_reduce.id as id, tbl_allowance_reduce.name as text', false);
            $this->db->from('tbl_allowance_reduce');
            if ($type == 1){
                $this->db->where('type',1);
            } else {
                $this->db->where('type',2);
            }
            if (!empty($q))
            {
                $this->db->group_start();
                $this->db->or_like('tbl_allowance_reduce.name', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $data['results'] = $this->db->get()->result_array();
        }
        if ($id) {
            $product = get_table_where('tbl_allowance_reduce',['id' => $id],'','row_array');
            $data['row'] = ['id' => $product['id'], 'text' => $product['name']];
        }
        echo json_encode($data);
    }

    public function apply_allowance_staff(){
        //67,168
        $data = [];
        $this->db->select('tbl_salary_allowance.*');
        $this->db->from('tbl_salary_allowance');
        $dtSalaryAllowance = $this->db->get()->result_array();
        $arrInsert = [];
        if (!empty($dtSalaryAllowance)){
            foreach ($dtSalaryAllowance as $key => $value){
                $arrInsert[] = [
                    'category_id' => $value['category_id'],
                    'amount' => $value['amount'],
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];
            }
        }
        $arrInsertNew = [];
        if (!empty($arrInsert)){
            $this->db->select('tblstaff.staffid as staffid');
            $this->db->from('tblstaff');
            $dtStaff = $this->db->get()->result_array();
            if (!empty($dtStaff)){
                foreach ($dtStaff as $kk => $vv){
                    foreach ($arrInsert as $k => $v){
                        $arrInsertNew[] = [
                            'category_id' => $v['category_id'],
                            'staff_id' => $vv['staffid'],
                            'amount' => $v['amount'],
                            'date_created' => $v['date_created'],
                            'created_by' => $v['created_by']
                        ];
                    }
                }
            }
        }
       if (!empty($arrInsertNew)){
           $success = false;
           $this->db->where('id !=',0);
           $this->db->delete('tbl_staff_allowance');
           $success = $this->db->insert_batch('tbl_staff_allowance',$arrInsertNew);
           if ($success){
               $data['result'] = 1;
               $data['message'] = lang('Áp dụng thành công !');
           } else {
               $data['result'] = 0;
               $data['message'] = lang('Áp dụng thất bại !');
           }
       } else {
           $data['result'] = 0;
           $data['message'] = lang('Đã áp đủ vào bên nhân viên rồi !');
       }
       echo json_encode($data);
    }

    public function apply_reduce_staff(){
        //67,168
        $data = [];
        $this->db->select('tbl_salary_reduce.*');
        $this->db->from('tbl_salary_reduce');
        $dtSalaryAllowance = $this->db->get()->result_array();
        $arrInsert = [];
        if (!empty($dtSalaryAllowance)){
            foreach ($dtSalaryAllowance as $key => $value){
                $arrInsert[] = [
                    'category_id' => $value['category_id'],
                    'amount' => $value['amount'],
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];
            }
        }
        $arrInsertNew = [];
        if (!empty($arrInsert)){
            $this->db->select('tblstaff.staffid as staffid');
            $this->db->from('tblstaff');
            $dtStaff = $this->db->get()->result_array();
            if (!empty($dtStaff)){
                foreach ($dtStaff as $kk => $vv){
                    foreach ($arrInsert as $k => $v){
                        $arrInsertNew[] = [
                            'category_id' => $v['category_id'],
                            'staff_id' => $vv['staffid'],
                            'amount' => $v['amount'],
                            'date_created' => $v['date_created'],
                            'created_by' => $v['created_by']
                        ];
                    }
                }
            }
        }
        if (!empty($arrInsertNew)){
            $success = false;
            $this->db->where('id !=',0);
            $this->db->delete('tbl_staff_reduce');
            $success = $this->db->insert_batch('tbl_staff_reduce',$arrInsertNew);
            if ($success){
                $data['result'] = 1;
                $data['message'] = lang('Áp dụng thành công !');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Áp dụng thất bại !');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Đã áp đủ vào bên nhân viên rồi !');
        }
        echo json_encode($data);
    }
}