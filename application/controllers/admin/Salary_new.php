<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Salary_new extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('kpi_model');
        $this->load->model('roles_model');
        $this->load->model('misc_model');
        $this->load->model('salary_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('evaluate');
        $this->datetime_now = time();
        $this->tnh = true;
    }

    public function scoreboard() {

        $data['roles'] = $this->roles_model->getRoles();
        $data['level'] = $this->roles_model->getLevel();
        $data['title'] = lang('tnh_scoreboard');
        $this->load->view('admin/salary_new/scoreboard', $data);
    }

    public function index() {
        $data['title'] = lang('tnh_salary');
        $this->load->view('admin/salary_new/index', $data);
    }

    public function p2_person() {
        $data['title'] = lang('tnh_p2_person');
        $this->load->view('admin/salary_new/p2_person', $data);
    }

    public function handling_p2_person($id = 0) {
        $data['title'] = lang('tnh_p2_person');
    }
}