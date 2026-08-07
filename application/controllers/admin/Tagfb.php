<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Tagfb extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = _l('als_tagfb');
        $this->load->view('admin/tagfb/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('tagfb');
    }

    public function detail()
    {
        $data = $this->input->post();
        if(!empty($data))
        {
            if(!empty($data['id']))
            {
                $id = $data['id'];
                unset($data['id']);
                $this->db->where('id', $id);
                if($this->db->update('tbltagsfb', $data))
                {
                    echo json_encode([
                        'success' => true,
                        'message' => _l('cong_update_true'),
                        'alert_type' => 'success',
                    ]);die();
                }
                echo json_encode([
                    'success' => false,
                    'message' => _l('cong_update_false'),
                    'alert_type' => 'danger',
                ]);die();
            }
            else
            {
                unset($data['id']);
                $this->db->insert('tbltagsfb', $data);
                $id = $this->db->insert_id();
                if(!empty($id))
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
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_event_active_false')
        ]);die();
    }

    public function GetAddTr()
    {
        $this->load->view('admin/tagfb/_tr_insert');
    }
    public function deleteTag()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            if($this->db->delete('tbltagsfb'))
            {
                $this->db->where('tag_id', $id);
                $this->db->delete('tbltaggablesfb');
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_delete_true')
                ]);die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_delete_false')
        ]);die();
    }

}
