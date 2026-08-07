<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hr_lifecycle extends AdminController
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = _l('Vòng Đời Nhân Sự');
        $this->load->view('admin/hr_lifecycle/index', $data);
    }
}