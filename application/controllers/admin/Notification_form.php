<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_form extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['tnh'] = true;
        $data['title'] = _l('notification_form');
        $this->load->view('admin/notification_form/index', $data);
    }

    public function add()
    {
        $data = [];
        if ($this->input->post())
        {
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_notification_form.code]');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('type', lang("type"), 'required');
            if ($this->form_validation->run() == true)
            {
                $code = $this->input->post('code');
                $name = $this->input->post('name');
                $type = $this->input->post('type');

                $options = [
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                ];

                $id = $this->site_model->insertNotificationForm($options);
                if ($id) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        } else {
            $this->load->view('admin/notification_form/add', $data);
        }
    }

    public function edit($id)
    {
        $data = [];
        $category = $this->site_model->rowNotificationForm($id);
        if ($this->input->post())
        {
            if ($category['code'] != $this->input->post('code'))
            {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_notification_form.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('type', lang("type"), 'required');
            if ($this->form_validation->run() == true)
            {
                $code = $this->input->post('code');
                $name = $this->input->post('name');
                $type = $this->input->post('type');

                $options = [
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                ];

                $id = $this->site_model->updateNotificationForm($id, $options);
                if ($id) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $this->load->view('admin/notification_form/edit', $data);
        }
    }

    public function getNotificationForm()
    {
        $this->datatables->select("
            tbl_notification_form.id as id,
            tbl_notification_form.code as code,
            tbl_notification_form.name as name,
            tbl_notification_form.type as type,
            ", FALSE)
        ->from('tbl_notification_form');


        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url().'admin/notification_form/edit/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\''.base_url('admin/notification_form/delete/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                '.$edit.'
                '.$delete.'
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function delete($id)
    {
        $data = [];
        if ($id) {
            if ($this->site_model->checkNotificationForm($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }

            if ($this->site_model->deleteNotificationForm($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}