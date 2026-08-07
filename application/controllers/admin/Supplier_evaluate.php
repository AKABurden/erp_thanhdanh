<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Supplier_evaluate extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = _l('supplier_evaluate');
        $this->load->view('admin/supplier_evaluate/manage', $data);
    }
    public function table_evaluate($value='')
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('evaluate');
        }
    }
    public function table_evaluate_result($value='')
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('evaluate_result');
        }
    }
}
