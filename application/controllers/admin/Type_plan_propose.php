<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Type_plan_propose extends AdminController
{
    function __construct()
    {
        parent::__construct();
    }
    /* List all tasks */
    public function index()
    {
        $data['title'] = _l('ch_type_plan_propose');
        $this->load->view('admin/type_plan_propose/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('type_plan_propose');
    }
    /* Get task data in a right pane */
    public function delete_type_plan_propose($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        $this->db->where('type_plan_propose', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row_array();
        if (!empty($internal_proposal)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => 'Loại kế hoạch đã được sử dụng không thể xóa'
            ));
            die;
        }
        $success    = $this->db->delete('tbl_type_plan_propose', array('id' => $id));
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function add_type_plan_propose()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $data['id'] = time();
            $this->db->insert('tbl_type_plan_propose', $data);
            $success = true;
            $message = _l('cong_add_true');
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }
    public function update_type_plan_propose($id = "")
    {
        if ($id != "") {
            $message    = '';
            $alert_type = 'warning';
            if ($this->input->post()) {
                $data = $this->input->post();
                $success = $this->db->update('tbl_type_plan_propose', $data, array('id' => $id));
                if ($success) {
                    $message    = 'ch_updated_successfuly';
                };
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        } else {
            if ($this->input->post()) {
                $data = $this->input->post();
                $data['id'] = time();
                $this->db->insert('tbl_type_plan_propose', $data);
                $alert_type = 'success';
                $message    = 'cong_add_true';
            }
            echo json_encode(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
        }
        die;
    }



    public function get_row_type_plan_propose($id)
    {
        echo json_encode(get_table_where('tbl_type_plan_propose', array('id' => $id), '', 'row'));
    }
}
