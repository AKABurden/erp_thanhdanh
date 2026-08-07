<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Bot_fanpage extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = _l('cong_bot_fanpage');
        $this->load->view('admin/bot_fanpage/manage', $data);
    }

    public function table()
    {
        $this->app->get_table_data('bot_fanpage');
    }

    public function getModal()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $data['title'] = _l('cong_title_add_bot_fanpage');
            $this->db->where('id', $id);
            $data['bot_fanpage'] = $this->db->get('tblbot_fanpage')->row();
        }
        else
        {
            $data['title'] = _l('cong_title_update_bot_fanpage');
        }
        $this->load->view('admin/bot_fanpage/modal', $data);
    }

    public function detail()
    {
        if($this->input->post())
        {
            $id = $this->input->post('id');
            $data = $this->input->post();
            unset($data['id']);
            if(!empty($id))
            {
                $this->db->where('id', $id);
                $success = $this->db->update('tblbot_fanpage', ['name' => $data['name']]);
                if(!empty($success))
                {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_add_true')
                    ]);
                    die();
                }
            }
            else
            {
                $arrayAdd = [
                    'name' => $data['name'],
                    'prefix' => get_option('bot-prefix'),
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id()
                ];

                $this->db->insert('tblbot_fanpage', $arrayAdd);
                if($this->db->insert_id())
                {
                    $id = $this->db->insert_id();
                    $this->db->where('id', $id);
                    $this->db->update('tblbot_fanpage', ['code' =>  sprintf("%06s", $id)]);

                    echo json_encode([
                        'message' => _l('cong_add_true'),
                        'alert_type' => 'success',
                        'success' => true
                    ]); die();
                }
                echo json_encode([
                    'message' => _l('cong_add_false'),
                    'alert_type' => 'danger',
                    'success' => false
                ]); die();
            }
        }
    }

    public function SetupModal($id = "")
    {
//        $id = $this->input->post('id');
        if(!empty($id))
        {
            $data['title'] = _l('cong_setup_bot_fanpage');
            $this->db->where('id', $id);
            $data['bot_fanpage'] = $this->db->get('tblbot_fanpage')->row();
            $this->load->view('admin/bot_fanpage/setup', $data['bot_fanpage']);
        }
    }

    public function addButtonEvent()
    {
        $id_data_item = $this->input->post('id_data_item');
        $id_orders_item = $this->input->post('id_orders_item');
        $data['id_data_item'] = $id_data_item;
        $data['id_orders_item'] = $id_orders_item + 1;
        $this->load->view('admin/bot_fanpage/_button', $data);
    }
    public function addButtonInput()
    {
        $id_data_item = $this->input->post('id_data_item');
        $id_orders_item = $this->input->post('id_orders_item');
        $data['id_data_item'] = $id_data_item;
        $data['id_orders_item'] = $id_orders_item + 1;
        $this->load->view('admin/bot_fanpage/_input', $data);
    }

    public function reply_step()
    {
        $id_orders = $this->input->post('id_orders');
        $data['id_orders'] = $id_orders;
        $id_data = $this->input->post('id_data');
        $data['id_data'] = ($id_data+1);
        $this->load->view('admin/bot_fanpage/reply_step', $data);
    }
}
