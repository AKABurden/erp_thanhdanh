<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Care_of_clients extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /* List all leads */
    public function index()
    {
        $data['title'] = _l('cong_care_of_clients');
        $this->db->select(db_prefix().'procedure_client_detail.*');
        $this->db->join(db_prefix().'procedure_client', db_prefix().'procedure_client.id = '.db_prefix().'procedure_client_detail.id_detail');
        $this->db->where('type', 'client');
        $data['client_detail'] = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
        $this->load->view('admin/care_of_clients/manage', $data);
    }

    public function table($userid = "")
    {
        $this->app->get_table_data('care_of_clients', ['id_client' => $userid]);
    }
    public function table_tab($userid = "")
    {
        $this->app->get_table_data('care_of_clients_tab', ['id_client' => $userid]);
    }

    public function getModal()
    {

        $this->db->select(db_prefix().'procedure_client_detail.*');
        $this->db->where('type' ,'client');
        $this->db->join(db_prefix().'procedure_client', db_prefix().'procedure_client.id = '.db_prefix().'procedure_client_detail.id_detail');
        $data['procedure_detail'] = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
        if($this->input->post('id'))
        {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $data['care_of_clients'] = $this->db->get(db_prefix().'care_of_clients')->row();
            if(!empty($data['care_of_clients']))
            {
                $this->db->select('count(id) as count_id');
                $this->db->where('id_care_of', $id);
                $data['care_of_clients']->count_care_of = $this->db->get(db_prefix().'procedure_care_of')->row();
            }
        }

        $this->db->select('group_concat(client) as list_id');
        $this->db->where('status_break' , 0);
        $list_care_of = $this->db->get(db_prefix().'care_of_clients')->row();

        if(empty($data['care_of_clients']->count_care_of))
        {
            $this->db->select(db_prefix().'clients.*, concat(prefix_client,code_client,"-",code_type) as full_code');
            if(!empty(explode(',', $list_care_of->list_id)))
            {
                $this->db->where_not_in('userid', explode(',', $list_care_of->list_id));
                if(!empty($data['care_of_clients']))
                {
                    $this->db->or_where('userid', $data['care_of_clients']->client);
                }
            }
            $data['clients'] = $this->db->get(db_prefix().'clients' )->result_array();
        }
        else
        {
            $this->db->where('userid', $data['care_of_clients']->client);
            $data['clients'] = $this->db->get(db_prefix().'clients' )->result_array();
        }

        $data['rating'] = [
            ['id' => '1', 'name' => _l('cong_1_start')],
            ['id' => '2', 'name' => _l('cong_2_start')],
            ['id' => '3', 'name' => _l('cong_3_start')],
            ['id' => '4', 'name' => _l('cong_4_start')],
            ['id' => '5', 'name' => _l('cong_5_start')]
        ];


        $this->load->view('admin/care_of_clients/modal', $data);
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
                $array_update = [
                    'client' => $data['client'],
                    'note' => $data['note'],
                    'solution' => $data['solution'],
                    'theme_of' => $data['theme_of'],
                    'event_care_of' => $data['event_care_of'],
                    'rating' => $data['rating'],
                    'date_contact' => $data['date_contact']
                ];
                if(!empty($data['date']))
                {
                    $array_update['date'] = to_sql_date($data['date']);
                }
                $this->db->where('id', $id);
                if($this->db->update(db_prefix().'care_of_clients', $array_update))
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
                $this->db->where('id', $data['status_procedure']);
                $procedure_detail = $this->db->get(db_prefix().'procedure_client_detail')->row();
                $_date = to_sql_date($data['date']);
                $date =  date("Y-m-d", strtotime("$_date +$procedure_detail->leadtime day"));
                $array_add = [
                    'client' => $data['client'],
                    'date' => to_sql_date($data['date']),
                    'date_expected' => $date,
                    'status_first' => $data['status_procedure'],
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id(),
                    'rating' => $data['rating'],
                    'id_orders' => !empty($data['id_orders']) ? $data['id_orders'] :'',
                    'priority' => !empty($data['priority']) ? $data['priority'] :'',
                    'theme_of' => !empty($data['theme_of']) ? $data['theme_of'] :'',
                    'event_care_of' => !empty($data['event_care_of']) ? $data['event_care_of'] :'',
                    'solution' => !empty($data['solution']) ? $data['solution'] :'',
                    'staff_success' => !empty($data['staff_success']) ? $data['staff_success'] :'',
                    'date_contact' => !empty($data['date_contact']) ? to_sql_date($data['date_contact'], true) :'',
                    'date_success' => !empty($data['date_success']) ? to_sql_date($data['date_success'], true) :'',
                    'feedback' => !empty($data['feedback']) ? to_sql_date($data['feedback'], true) :'',
                ];

                $this->db->insert(db_prefix().'care_of_clients', $array_add);
                if($this->db->insert_id())
                {
                    $id = $this->db->insert_id();
                    //Thêm Mã tư vấn cho Phiếu
                    $code   =   sprintf("%06s", $id);
                    $prefix = get_option('care_of_clients_prefix');
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix().'care_of_clients', ['code' => $code, 'prefix' => $prefix]);
                    //End thêm mã tư vấn cho phiếu

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
    public function delete_care_of_clients()
    {
        if($this->input->post('id'))
        {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            if($this->db->delete(db_prefix().'care_of_clients'))
            {
                $this->db->where('id_care_of', $id);
                $this->db->delete('procedure_care_of');
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
                $care_of_clients = $this->db->get(db_prefix().'care_of_clients')->row();
                if(!empty($care_of_clients))
                {
                    //Kiểm tra phiếu đã kế thúc chưa
                    if($care_of_clients->status_break == 1)
                    {
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'danger',
                            'message' => _l('cong_advisory_status_break')
                        ]);die();
                    }

                    //kiểm tra phiếu đã được duyệt qua chưa
                    $this->db->where('id_care_of', $data['id']);
                    $this->db->where('status_procedure', $data['status_procedure']);
                    $action_care_of = $this->db->get(db_prefix().'procedure_care_of')->row();
                    if(!empty($action_care_of))
                    {
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'danger',
                            'message' => _l('cong_update_isset')
                        ]);die();
                    }
                    else
                    {
                        //Duyệt trạng thái
                        $this->db->insert(db_prefix().'procedure_care_of', [
                            'create_by' => get_staff_user_id(),
                            'date_create' => date('Y-m-d H:i:s'),
                            'id_care_of' => $data['id'],
                            'status_procedure' => $data['status_procedure'],
                            'active' => 1
                        ]);

                        if($this->db->insert_id())
                        {

                            $this->db->where('id_care_of', $data['id']);
                            $this->db->where('id !='. $this->db->insert_id());
                            $this->db->update(db_prefix().'procedure_care_of', ['active' => 0]);
                            //Thêm vào lịch sử chăn sóc khách hàng
                            addLog_care_of([
                                'id_client' => $care_of_clients->client,
                                'staff' => get_staff_user_id(),
                                'id_procedure' => $data['status_procedure']
                            ], 1);

                            $array_update_care_of = [
                                'date_expected' => date('Y-m-d')
                            ];

                            $this->db->where('id', $data['status_procedure']);
                            $client_detail = $this->db->get(db_prefix().'procedure_client_detail')->row(); // lấy quy trình cập nhật
                            if(!empty($client_detail))
                            {
                                $this->db->where('orders > ', $client_detail->orders);
                                $this->db->order_by('orders', 'asc');
                                $this->db->where('id_detail', $client_detail->id_detail);
                                $client_detail_next = $this->db->get(db_prefix().'procedure_client_detail')->row();
                                if(empty($client_detail_next))
                                {
                                    $array_update_care_of['status_break'] = 1;
                                    $array_update_care_of['staff_cuccess'] = get_staff_user_id();
                                    $array_update_care_of['date_success'] = date('Y-m-d H:i:s');
                                    addLog_care_of([
                                        'id_client' => $care_of_clients->client,
                                        'staff' => get_staff_user_id(),
                                        'id_procedure' => $data['status_procedure']
                                    ], 3);
                                }
                                else
                                {
                                    $_date = date('Y-m-d');
                                    $date =  date("Y-m-d", strtotime("$_date +$client_detail_next->leadtime day"));
                                    $array_update_care_of['date_expected'] = $date;
                                }
                            }

                            $this->db->where('id', $data['id']);
                            $this->db->update(db_prefix().'care_of_clients', $array_update_care_of);
                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'message' => _l('cong_update_true')
                            ]);die();
                        }
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

    public function restore_care_of_clients()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            if(!empty($data['id']))
            {
                $id = $data['id'];
                $this->db->where('id', $id);
                $care_of_clients = $this->db->get(db_prefix().'care_of_clients')->row();
                if(!empty($care_of_clients))
                {
                    $this->db->where('id_care_of', $id);
                    $this->db->order_by('date_create', 'desc');
                    $action_care_of = $this->db->get(db_prefix().'procedure_care_of')->row();
                    if(!empty($action_care_of))
                    {
                        $this->db->where('id', $action_care_of->id);
                        if($this->db->delete(db_prefix().'procedure_care_of'))
                        {
                            $this->db->where('id_care_of', $id);
                            $this->db->order_by('date_create', 'desc');
                            $action_advisory_old = $this->db->get(db_prefix().'procedure_care_of')->row(); // lui lại bước để làm active
                            if(!empty($action_advisory_old))
                            {
                                $this->db->where('id', $action_advisory_old->id);
                                $this->db->update(db_prefix().'procedure_care_of', ['active' => 1]);
                            }
                            //Thêm vào lịch sử chăn sóc khách hàng
                            addLog_care_of([
                                'id_client' => $care_of_clients->client,
                                'staff' => get_staff_user_id(),
                                'id_procedure' => $action_care_of->status_procedure
                            ], 0);

                            $this->db->where('id', $action_care_of->status_procedure);
                            $client_detail = $this->db->get(db_prefix().'procedure_client_detail')->row(); // lấy quy trình cập nhật
                            if(!empty($client_detail))
                            {
                                $this->db->where('orders > ', $client_detail->orders);
                                $this->db->order_by('orders', 'asc');
                                $this->db->where('id_detail', $client_detail->id_detail);
                                $client_detail_next = $this->db->get(db_prefix().'procedure_client_detail')->row();
                                if(!empty($client_detail_next)) {
                                    $_date = $care_of_clients->date_expected;
                                    $date = date("Y-m-d", strtotime("$_date -$client_detail_next->leadtime day"));
                                    $this->db->where('id', $id);
                                    $this->db->update(db_prefix() . 'care_of_clients', ['date_expected' => $date]);
                                }

                                echo json_encode([
                                    'success' => true,
                                    'alert_type' => 'success',
                                    'message' => _l('cong_restore_advisory_true')
                                ]);die();
                            }
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

    public function break_care_of()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $status = $this->input->post('status');
            $this->db->where('id', $id);
            $care_of_clients = $this->db->get(db_prefix().'care_of_clients')->row();
            if(!empty($care_of_clients))
            {
                $this->db->where('id', $id);
                if($this->db->update(db_prefix().'care_of_clients' , [
                    'status_break' => $status
                ]))
                {
                    $this->db->where('id_care_of', $id);
                    $this->db->order_by('date_create', 'desc');
                    $action_care_of = $this->db->get(db_prefix().'procedure_care_of')->row();

                    addLog_care_of([
                        'id_client' => $care_of_clients->client,
                        'staff' => get_staff_user_id(),
                        'id_procedure' => !empty($action_care_of->status_procedure) ? $action_care_of->status_procedure : $care_of_clients->status_first
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

    public function change_priority()
    {
        $id = $this->input->post('id');
        $priority = $this->input->post('priority');
        if(!empty($id) && isset($priority))
        {
            $this->db->where('id', $id);
            $success = $this->db->update(db_prefix().'care_of_clients', ['priority' => $priority]);
            if(!empty($success))
            {
                echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('cong_change_status_true')]);die();
            }
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_change_status_false')]);die();
    }


}
