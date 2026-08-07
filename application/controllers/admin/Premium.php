<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Premium extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title']          = _l('Premium');
        $this->load->view('admin/premium/manage', $data);
    }
}