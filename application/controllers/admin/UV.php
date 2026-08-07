<?php
defined('BASEPATH') or exit('No direct script access allowed');
class UV extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('empl_model');
    }
    /* Open also all taks if user access this /tasks url */
    public function index()
    {
        $data['title'] = _l('h_uv');
        $data['departments'] = get_table_where('tbldepartments', [], '', 'result_array', '', 'departmentid,name');
        $data['roles'] = get_table_where('tblroles', [], '', 'result_array', '', 'roleid,name');
        $this->load->view('admin/uv/list', $data);
    }

    public function add_empl()
    {
        if ($this->input->post()) {
            $success = true;
            $message = '';
            $id = $this->empl_model->add_empl($this->input->post(NULL, FALSE));
            if ($id) {
                $success = true;
                $message = _l('added_successfuly', _l('als_units'));
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }

    public function table()
    {
        $this->app->get_table_data('tbluv');
    }

    public function update_empl($id)
    {
        $updateData = $this->empl_model->update_employee($this->input->post(), $id);
        if ($id) {
            $success = true;
            $message = _l('added_successfuly', _l('als_units'));
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function delete_empl($id = '')
    {
        if (!$id) {
            die('ch_no_items');
        }
        $delData = $this->empl_model->delete_empl($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($delData) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function get_row($id = null)
    {
        $data = $this->empl_model->get_row_empl($id);
        echo json_encode($data);
    }
}