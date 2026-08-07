<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting_date_evaluate extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = _l('setting_date_evaluate');
        $this->load->view('admin/setting_date_evaluate/manage', $data);
    }
    public function table_evaluate($value='')
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('evaluate');
        }
    }

    public function setData()
    {
        $data = $this->input->post();
        $in = array(
            'month' => $data['month']
        );
        $this->db->insert('tblsetting_date_evaluate', $in);
        $insert_id = $this->db->insert_id();
        echo json_encode(array('id'=>$insert_id));
    }

    public function deleteData()
    {
        $data = $this->input->post();
        $this->db->where('id', $data['id']);
        $this->db->delete('tblsetting_date_evaluate');
    }

    public function updateData()
    {
        $data = $this->input->post();
        $this->db->set('month', $data['month']);
        $this->db->where('id', $data['id']);
        $this->db->update('tblsetting_date_evaluate');
    }

    public function updateCycle_evaluate()
    {
        $data = $this->input->post();
        $this->db->set('value', $data['cycle_evaluate']);
        $this->db->where('name', 'cycle_evaluate');
        $this->db->update('tbloptions');
    }
}
