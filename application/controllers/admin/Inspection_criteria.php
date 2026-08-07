<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inspection_criteria extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // if (!is_admin()) {
        //     accessDenied();
        // 	die;
        // }
    }
    public function index()
    {
        $data = [];
        $data['title'] = lang('Tiêu chí kiểm');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/relate', $data);
    }
}