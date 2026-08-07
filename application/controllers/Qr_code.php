<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Qr_code extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('ciqrcode');
    }
    public function get($code)
    {
//        header("Content-Type: image/png");
        $params['level'] = 'H';
        $params['size'] = 300;
        $params['data'] = $code;
        header("Content-Type: image/png");
        $this->ciqrcode->generate($params);
    }
}
