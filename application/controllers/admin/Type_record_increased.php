<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Type_record_increased extends AdminController
{
    function __construct()
    {
        parent::__construct();
    }
    /* List all tasks */
    public function index()
    {
        $data['title'] = _l('ch_type_record_increased');
        $this->load->view('admin/type_record_increased/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('type_record_increased');
    }
    /* Get task data in a right pane */
    public function delete_type_record_increased($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        $success    = $this->db->delete('tbltype_record_increased',array('id'=>$id));
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
     public function add_type_record_increased()
    {
        if ($this->input->post()) {
            $message = '';
                $data = $this->input->post();
                $data['staff_id'] = get_staff_user_id();
                $data['date_create'] = date('Y-m-d H:i:s');
                $this->db->insert('tbltype_record_increased',$data);
                $id = $this->db->insert_id();
                if ($id) {
                    $success = true;
                    $message = _l('cong_add_true');
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            die;
        }
    }
    public function update_type_record_increased($id="")
    {
        if($id!=""){
            $message    = '';
            $alert_type = 'warning';
            if ($this->input->post()) {
                $data = $this->input->post();
                $data['staff_edit'] = get_staff_user_id();
                $data['date_edit'] = date('Y-m-d H:i:s');
                $success = $this->db->update('tbltype_record_increased',$data,array('id'=>$id));
                if ($success) {
                    $message    = 'ch_updated_successfuly';
                };
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
        else
        {
            if ($this->input->post()) {
                $data = $this->input->post();
                $data['staff_id'] = get_staff_user_id();
                $data['date_create'] = date('Y-m-d H:i:s');
                $this->db->insert('tbltype_record_increased',$data);
                $success = $this->db->insert_id();
                if ($success) {
                    $alert_type = 'success';
                    $message    = 'cong_add_true';
                }
            }
            echo json_encode(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
        }
        die;
    }



    public function get_row_type_record_increased($id)
    {
        echo json_encode(get_table_where('tbltype_record_increased',array('id'=>$id),'','row'));
    }


}
