<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Branch extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            access_denied('branch');
        }
    }

    public function index() {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('branch');
        }
        $data['title'] = _l('h_branch');
        $this->load->view('admin/branch/manage', $data);
    }

    public function detail($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $in = array(
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'number_phone' => $data['number_phone']
                );
                $id = $this->db->insert('tblbranch', $in);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('h_branch'));
                }
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message
                ]);
            } else {
                $id = $data['id'];
                unset($data['id']);
                $in = array(
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'number_phone' => $data['number_phone']
                );
                $this->db->where('id', $id);
                $success = $this->db->update('tblbranch', $in);
                if ($success) {
                    $message = _l('updated_successfully', _l('h_branch'));
                }
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message
                ]);
            }
            die;
        }
    }

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('branch'));
        }

        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.id_branch', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            set_alert('warning', _l('Đã sự dụng không thể xóa'));
            redirect(admin_url('branch')); die;
        }

        $this->db->from('tblstaff');
        $this->db->where('tblstaff.id_branch', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            set_alert('warning', _l('Đã sự dụng không thể xóa'));
            redirect(admin_url('branch')); die;
        }

        $this->db->from('tblwarehouse');
        $this->db->where('tblwarehouse.id_branch', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            set_alert('warning', _l('Đã sự dụng không thể xóa'));
            redirect(admin_url('branch')); die;
        }

        $this->db->from('tbl_productions_plan');
        $this->db->where('tbl_productions_plan.id_branch', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            set_alert('warning', _l('Đã sự dụng không thể xóa'));
            redirect(admin_url('branch')); die;
        }


        $this->db->where('id', $id);
        $response = $this->db->delete('tblbranch');
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('h_branch')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('h_branch')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('h_branch')));
        }
        redirect(admin_url('branch'));
    }
}