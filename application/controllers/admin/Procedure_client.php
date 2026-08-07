<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Procedure_client extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $data['title'] = _l('cong_procedure_careof');
        $data['procedure_client'] = get_table_where(db_prefix().'procedure_client');
        $this->load->view('admin/procedure_client/manage', $data);
    }

    public function table($id = "")
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }
        $this->app->get_table_data('procedure_client_detail', ['id_detail' => $id]);
    }
    public function modal_procedure()
    {
        $id = $this->input->post('id');
        $id_detail = $this->input->post('id_detail');
        $data = [];
        if(!empty($id))
        {
            $data['procedure_detail'] = get_table_where(db_prefix().'procedure_client_detail', ['id' => $id], '', 'row');
        }
        if(!empty($id_detail) || !empty($id))
        {
            $data['id_detail'] = $id_detail;

            $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname');
            $data['staff'] = $this->db->get('tblstaff')->result_array();
            
            $this->load->view('admin/procedure_client/modal', $data);
        }
    }
    public function detail()
    {
        if(!empty($this->input->post()))
        {
            $data = $this->input->post();
            if(!empty($data['id']))
            {
                $this->db->where('id', $data['id']);
                $success_update = $this->db->update(db_prefix().'procedure_client_detail', [
                    'name' => $data['name'],
                    'leadtime' => $data['leadtime'],
                    'staff_id' => implode(",", $data['staff_id'])
                ]);
                if(!empty($success_update))
                {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_update_true')
                    ]);die();
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ]);die();
            }
            else
            {
                $this->db->select('max(orders) as max_orders');
                $this->db->where('id_detail', $data['id_detail']);
                $procedure_max = $this->db->get(db_prefix().'procedure_client_detail')->row();

                $this->db->insert(db_prefix().'procedure_client_detail', [
                    'name' => $data['name'],
                    'leadtime' => $data['leadtime'],
                    'id_detail' => $data['id_detail'],
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id(),
                    'orders' => ($procedure_max->max_orders + 1),
                    'staff_id' => implode(",", $data['staff_id'])
                ]);
                if($this->db->insert_id())
                {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_add_true')
                        ]);die();
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_add_false')
                ]);die();
            }
        }
    }

    public function delete_procedure()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            if($this->db->delete(db_prefix().'procedure_client_detail'))
            {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_delete_true')
                ]);die();
            }
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => _l('cong_delete_false')
            ]);die();
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_delete_false')
        ]);die();
    }

    public function OrdersProcedure()
    {
       if($this->input->post())
       {
           $data = $this->input->post();
           $assUpdate = 0;
           foreach($data as $key => $value)
           {
               $this->db->where('id', $key);
               if($this->db->update(db_prefix().'procedure_client_detail', ['orders' => $value]))
               {
                    ++$assUpdate;
               }
           }
           if($assUpdate == count($data))
           {
               echo json_encode([
                   'success' => true,
                   'alert_type' => 'success',
                   'message' => _l('cong_orders_update_true')
               ]);die();
           }
       }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_orders_update_false')
        ]);die();
    }

}
