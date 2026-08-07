<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Advisory_lead extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /* List all leads */
    public function index()
    {
        $data['title'] = _l('cong_advisory_lead');
        $this->db->select(db_prefix().'procedure_client_detail.*');
        $this->db->join(db_prefix().'procedure_client', db_prefix().'procedure_client.id = '.db_prefix().'procedure_client_detail.id_detail');
        $this->db->where('type', 'lead');
        $data['client_detail'] = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
        $this->load->view('admin/advisory_lead/manage', $data);
    }

    public function table()
    {
        $this->app->get_table_data('advisory_lead');
    }

    public function table_advisory_lead_tab($id_lead = "")
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data('advisory_lead_tab', ['id_lead' => $id_lead]);
    }

    public function getModal()
    {

        $this->db->select(db_prefix().'procedure_client_detail.*');
        $this->db->where('type' ,'lead');
        $this->db->join(db_prefix().'procedure_client', db_prefix().'procedure_client.id = '.db_prefix().'procedure_client_detail.id_detail');
        $this->db->order_by('orders', 'asc');
        $data['procedure_detail'] = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
        if($this->input->post('id'))
        {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $data['advisory_lead'] = $this->db->get(db_prefix().'advisory_lead')->row();

            $this->db->select('count(id) as count_id');
            $this->db->where('id_advisory', $id);
            $this->db->where('active', 1);
            $advisory_lead_procedure = $this->db->get(db_prefix() . 'procedure_advisory_lead')->num_rows();
            if(!empty($advisory_lead_procedure))
            {
                $data['advisory_lead']->active = 1;
            }
        }

        $this->load->view('admin/advisory_lead/modal', $data);
    }

    public function detail()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            if(!empty($data['id']))
            {
                $id = $data['id'];
                unset($data['id']);
                $this->db->where('id', $id);
                $advisory_lead = $this->db->get('tbladvisory_lead')->row();
                if(!empty($advisory_lead)) {
                    $array_update = [
                        'product_other_buy' => $data['product_other_buy'],
                        'address_other_buy' => $data['address_other_buy'],
                    ];
                    if (!empty($data['date'])) {
                        $array_update['date'] = to_sql_date($data['date']);
                    }

                    $object = explode('_', $data['object']);
                    $id_object = $object[1];
                    $type_object = $object[0];

                    $type_code = 'NC';

                    if($type_object == 'client')
                    {
                        $this->db->where('client', $object[1]);
                        $numrows = $this->db->get('tblorders')->num_rows();
                        if(!empty($numrows))
                        {
                            $type_code = 'OC';
                        }
                    }

                    $array_update['id_object'] = $id_object;
                    $array_update['type_code'] = $type_code;
                    $array_update['type_object'] = $type_object;


                    $this->db->select('count(id) as count_id');
                    $this->db->where('id_advisory', $id);
                    $this->db->where('active', 1);
                    $advisory_lead_procedure = $this->db->get(db_prefix() . 'procedure_advisory_lead')->row();

                    if (empty($advisory_lead_procedure->count_id)) {
                        if($advisory_lead->status_first != $data['status_first'] || $advisory_lead->date != $array_update['date'])
                        {
                            $array_update['status_first'] = $data['status_first'];
                        }
                    } else {
                        echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => _l('cong_data_to_change_not_update')
                        ]);
                        die();
                    }


                    $this->db->where('id', $id);
                    if ($this->db->update(db_prefix() . 'advisory_lead', $array_update)) {

                        if((!empty($array_update['date']) && $array_update['date'] != $advisory_lead->date)||
                           (!empty($data['status_first']) && $data['status_first'] != $advisory_lead->status_first))
                        {

                            $this->db->where('id_advisory', $id);
                            $this->db->delete('tblprocedure_advisory_lead');

                            $this->db->where('id', $data['status_first']);
                            $procedure_detail = $this->db->get(db_prefix().'procedure_client_detail')->row();
                            if(!empty($procedure_detail))
                            {
                                $this->db->order_by('orders', 'ASC');
                                $this->db->where('id_detail', $procedure_detail->id_detail);
                                $this->db->where('orders >= ', $procedure_detail->orders);
                                $advisory_procedure = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
                                $_date = $array_update['date'];
                                foreach($advisory_procedure as $key => $value)
                                {
                                    $leadtime = $value['leadtime'];
                                    $_date =  date("Y-m-d", strtotime("$_date +$leadtime day"));
                                    $this->db->insert('tblprocedure_advisory_lead', [
                                        'id_advisory' => $id,
                                        'name_status' => $value['name'],
                                        'orders_status' => $value['orders'],
                                        'leadtime' => $value['leadtime'],
                                        'status_procedure' => $value['id'],
                                        'date_expected ' => $_date
                                    ]);
                                }
                            }
                        }

                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'success',
                            'message' => _l('cong_update_true')
                        ]);
                        die();
                    }
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ]);die();
            }
            else
            {
                $this->db->where('id', $data['status_first']);
                $procedure_detail = $this->db->get(db_prefix().'procedure_client_detail')->row();
                if(!empty($procedure_detail))
                {
                    $this->db->order_by('orders', 'ASC');
                    $this->db->where('id_detail', $procedure_detail->id_detail);
                    $this->db->where('orders >= ', $procedure_detail->orders);
                    $advisory_procedure = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
                }

                $object = explode('_', $data['object']);
                $id_object = $object[1];
                $type_object = $object[0];

                $type_code = 'NC';

                if($type_object == 'client')
                {
                    $this->db->where('client', $object[1]);
                    $numrows = $this->db->get('tblorders')->num_rows();
                    if(!empty($numrows))
                    {
                        $type_code = 'OC';
                    }
                }

                $array_add = [
                    'id_object' => $id_object,
                    'type_object' => $type_object,
                    'type_code' => $type_code,
                    'date' => to_sql_date($data['date']),
                    'status_first' => $data['status_first'],
                    'status_active' => 0,
                    'product_other_buy' => $data['product_other_buy'],
                    'address_other_buy' => $data['address_other_buy'],
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id()
                ];

                $this->db->insert(db_prefix().'advisory_lead', $array_add);
                if($this->db->insert_id())
                {
                    $id = $this->db->insert_id();
                    //Thêm Mã tư vấn cho Phiếu
                    CreateCode('advisory', $id);
                    //End thêm mã tư vấn cho phiếu
                    $_date = to_sql_date($data['date']);
                    foreach($advisory_procedure as $key => $value)
                    {
                        $leadtime = $value['leadtime'];
                        $_date =  date("Y-m-d", strtotime("$_date +$leadtime day"));
                        $this->db->insert('tblprocedure_advisory_lead', [
                            'id_advisory' => $id,
                            'name_status' => $value['name'],
                            'orders_status' => $value['orders'],
                            'leadtime' => $value['leadtime'],
                            'status_procedure' => $value['id'],
                            'date_expected ' => $_date
                        ]);
                    }

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

    public function delete_advisory_lead()
    {
        if($this->input->post('id'))
        {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            if($this->db->delete(db_prefix().'advisory_lead'))
            {
                $this->db->where('id_advisory', $id);
                $this->db->delete('procedure_advisory_lead');

                $this->db->where('id_advisory', $id);
                $this->db->delete('tbladvisory_detail_experience');
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
    }

    public function update_status()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            if(!empty($data['id']) && !empty($data['status_procedure']))
            {
                $this->db->where('id', $data['id']);
                $advisory_lead = $this->db->get('tbladvisory_lead')->row();
                if(!empty($advisory_lead))
                {
                    //Kiểm tra phiếu đã kế thúc chưa
                    if($advisory_lead->status_break == 1)
                    {
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'danger',
                            'message' => _l('cong_advisory_status_break')
                        ]);die();
                    }

                    //kiểm tra phiếu đã được duyệt qua chưa
                    $this->db->where('id_advisory', $data['id']);
                    $this->db->where('status_procedure', $data['status_procedure']);
                    $action_advisory = $this->db->get('tblprocedure_advisory_lead')->row();
                    if(!empty($action_advisory))
                    {
                        if($action_advisory->active == 1)
                        {
                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'danger',
                                'message' => _l('cong_update_isset')
                            ]);die();
                        }
                        else
                        {
                            $this->db->where('id', $action_advisory->id);
                            $success_update = $this->db->update('tblprocedure_advisory_lead', [
                                'active' => 1,
                                'create_by' => get_staff_user_id(),
                                'date_create' => date('Y-m-d H:i:s')
                            ]);
                            if($success_update)
                            {
                                $this->db->where('id', $advisory_lead->id);
                                $this->db->update('tbladvisory_lead', ['status_advisory' => $data['status_procedure']]);
                                addLog_advisory_lead([
                                    'type_object' => $advisory_lead->type_object,
                                    'id_object' => $advisory_lead->id_object,
                                    'name_status' => $action_advisory->name_status,
                                    'staff' => get_staff_user_id(),
                                    'id_procedure' => $action_advisory->status_procedure
                                ], 1);
                            }
                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'message' => _l('cong_update_true')
                            ]);die();
                        }
                    }
                    else
                    {
                        echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => _l('cong_dont_isset_status')
                        ]);die();
                    }
                }
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_update_false')
        ]);die();
    }


    //end Sửa
    public function restore_advisory_lead()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            if(!empty($data['id']))
            {
                $id = $data['id'];
                $this->db->where('id', $id);
                $advisory_lead = $this->db->get('tbladvisory_lead')->row();
                if(!empty($advisory_lead))
                {
                    $this->db->where('id_advisory', $advisory_lead->id);
                    $this->db->where('active', 1);
                    $this->db->order_by('orders_status', 'desc');
                    $action_advisory = $this->db->get('tblprocedure_advisory_lead')->row();
                    if(!empty($action_advisory))
                    {
                        $this->db->where('id', $action_advisory->id);
                        if($this->db->update('tblprocedure_advisory_lead', ['active' => 0, 'date_create' => NULL]))
                        {
                            //Thêm vào lịch sử chăn sóc khách hàng
                            addLog_advisory_lead([
                                'type_object' => $advisory_lead->type_object,
                                'id_object' => $advisory_lead->id_object,
                                'name_status' => $action_advisory->name_status,
                                'staff' => get_staff_user_id(),
                                'id_procedure' => $action_advisory->status_procedure
                            ], 0);


                            $this->db->where('id_advisory', $advisory_lead->id);
                            $this->db->where('active', 1);
                            $this->db->order_by('orders_status', 'desc');
                            $action_advisory_before = $this->db->get('tblprocedure_advisory_lead')->row();

                            $this->db->where('id', $advisory_lead->id);
                            $this->db->update('tbladvisory_lead', ['status_advisory' => (!empty($action_advisory_before->status_procedure) ? $action_advisory_before->status_procedure : 0)]);

                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'message' => _l('cong_restore_advisory_true')
                            ]);die();
                        }
                    }
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'danger',
                        'message' => _l('cong_data_not_isset')
                    ]);die();
                }
            }
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => _l('cong_update_false')
            ]);die();
        }
    }

    public function break_advisory()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $status = $this->input->post('status');
            $this->db->where('id', $id);
            $advisory_lead = $this->db->get(db_prefix().'advisory_lead')->row();
            if(!empty($advisory_lead))
            {
                $this->db->where('id', $id);
                $success = $this->db->update(db_prefix().'advisory_lead' , [
                    'status_break' => $status
                ]);
                if($success)
                {
                    $this->db->where('id_advisory', $id);
                    $this->db->order_by('date_create', 'desc');
                    $action_advisory = $this->db->get(db_prefix().'procedure_advisory_lead')->row();

                    addLog_advisory_lead([
                        'type_object' => $advisory_lead->type_object,
                        'id_object' => $advisory_lead->id_object,
                        'name_status' => $action_advisory->name_status,
                        'staff' => get_staff_user_id(),
                        'id_procedure' => $action_advisory->status_procedure
                    ], 3);

                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_break_advisory_true')
                    ]);die();
                }
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'false',
            'message' => _l('cong_break_advisory_false')
        ]);die();
    }
    //end Sủa



    public function SearchObject($id = "")
    {
        if(!empty($id))
        {
            $id = explode('_', $id);
        }
        $data = [];
        $search = $this->input->get('term');
        $limit_one = 50;
        $limit_all = 100;
        $this->db->select('
            concat("lead_", id) as id,
            concat(name_system) as text,
            CONCAT("download/preview_image?path=uploads/leads/", id,"/", "small_", lead_image) as img'
            , false);
        if(!empty($id) && $id[0] == 'lead')
        {
            $this->db->where('id', $id[1]);
            $leads = $this->db->get('tblleads')->row();
            if(!empty($leads)) {
                $data['results'] = $leads;
                echo json_encode($data);die();
            }
        }
        else
        {
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('name', $search);
                $this->db->or_like('CONCAT(prefix_lead,code_lead," - ", code_type)', $search);
                $this->db->group_end();
            }
            $this->db->where('status !=' , 1);
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $leads = $this->db->get('tblleads')->result_array();
            if(!empty($leads))
            {
                $data['results'][] =
                    [
                        'text' => _l('cong_lead'),
                        'children' => $leads
                    ];
            }
        }



        $count_leads = count($leads);
        $this->db->select('
                concat("client_", userid) as id,
                concat(name_system) as text,
                CONCAT("download/preview_image?path=uploads/clients/", userid,"/", "small_", client_image) as img'
            , false);
        if(!empty($id) && $id[0] == 'client')
        {
            $this->db->where('userid', $id[1]);
            $clients = $this->db->get('tblclients')->row();
            if(!empty($clients)) {
                $data['results'] = $clients;
                echo json_encode($data);die();
            }
        }
        else
        {
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('fullname', $search);
                $this->db->or_like('name_system', $search);
                $this->db->or_like('concat(prefix_client, code_client," - ", code_type)', $search);
                $this->db->group_end();
            }
            $this->db->order_by('fullname', 'DESC');
            $this->db->limit(($limit_all - $count_leads));
            $clients = $this->db->get('tblclients')->result_array();
            if(!empty($clients)) {
                $data['results'][] =
                    [
                        'text' => _l('cong_client'),
                        'children' => $clients
                    ];
            }
        }
        echo json_encode($data);die();
    }

    // change trải nghiệm tư vấn
    public function ChangeErience()
    {
        $id = $this->input->post('id');
        $data = $this->input->post('erience');
        if(!empty($id))
        {
            foreach($data as $key => $value)
            {
                $arrayNotDelete = [];
                foreach($value as $kv => $vV)
                {
                    $exrience_detail = get_table_where('tblexperience_advisory_detail', ['id' => $vV], '', 'row');

                    $this->db->where('id_advisory', $id);
                    $this->db->where('id_experience', $key);
                    $this->db->where('id_experience_detail', $exrience_detail->id);
                    $ktDetail = $this->db->get('tbladvisory_detail_experience')->row();
                    if(!empty($ktDetail))
                    {
                            $arrayNotDelete[] = $ktDetail->id;
                    }
                    else
                    {

                        $this->db->insert('tbladvisory_detail_experience', [
                            'id_experience' => $key,
                            'id_advisory' => $id,
                            'date_create' => date('Y-m-d H:i:s'),
                            'create_by' => get_staff_user_id(),
                            'name' => $exrience_detail->name,
                            'id_experience_detail' => $exrience_detail->id,
                        ]);
                        if($this->db->insert_id())
                        {
                            $arrayNotDelete[] = $this->db->insert_id();
                        }
                    }
                }

                $this->db->where('id_experience', $key);
                $this->db->where('id_advisory', $id);
                if(!empty($arrayNotDelete))
                {
                    $this->db->where_not_in('id', $arrayNotDelete);
                }
                $this->db->delete('tbladvisory_detail_experience');

                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_change_true')
                ]);die();
            }
        }

        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_change_false')
        ]);die();
    }


    //update status active
    public function updateStatus()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $advisory_lead = $this->db->get('tbladvisory_lead')->row();
            if(!empty($advisory_lead))
            {
                $this->db->where('id', $id);
                $success = $this->db->update('tbladvisory_lead', ['status_active' => $status]);
                if($success)
                {
                    echo json_encode([
                        'alert_type' => 'success',
                        'message' => _l('cong_update_true'),
                        'success' => true
                    ]);die();
                }
                echo json_encode([
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false'),
                    'success' => false
                ]);die();
            }
            echo json_encode([
                'alert_type' => 'danger',
                'message' => _l('cong_data_isset_change'),
                'success' => false
            ]);die();
        }
        echo json_encode([
            'alert_type' => 'danger',
            'message' => _l('cong_update_false'),
            'success' => false
        ]);die();
    }



    public function updateCriteria()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $colums = $this->input->post('colums');
        if(!empty($id) && !empty($status) && !empty($colums))
        {
            $arrayUpdate = [];
            if($colums == 'criteria_one')
            {
                $arrayUpdate['criteria_one'] = $status;
                $arrayUpdate['date_criteria_one'] = date('Y-m-d H:i:s');
            }
            else if($colums == 'criteria_two')
            {
                $arrayUpdate['criteria_two'] = $status;
                $arrayUpdate['date_criteria_two'] = date('Y-m-d H:i:s');
            }
            $success = false;
            if(!empty($arrayUpdate))
            {
                $this->db->where('id', $id);
                $success = $this->db->update('tbladvisory_lead', $arrayUpdate);
            }
            if(!empty($success))
            {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_update_true')
                ]);die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_update_false')
        ]);die();
    }

}
