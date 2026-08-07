<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_srceen_accounting extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }
    function loadpage()
    {
        sendSocket(['data' => 123], [], 'update_dashboard');
    }
    function index()
    {
        $data = [];
        // Ngày giờ
        $weekdayMap = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $w = (int)date('w');
        $dateStr = $weekdayMap[$w] . ', ' . date('d/m/Y');
        $timeStr = date('g:i A');
        $link_connect_socket = get_option('link_connect_socket');
        $data = [
            'dateStr' => $dateStr,
            'timeStr' => $timeStr,
            'dbname'  => APP_DB_NAME,
            'link_connect_socket'  => $link_connect_socket,
        ];
        $data['departments'] = $this->db->get('tbl_room')->result_array();
        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_category_stages.type_productionlist_id >', 0);
        $data['categoryStage'] = $this->db->get()->result_array();

        $this->load->view('admin/dashboard_srceen_accounting/dashboard_orchestrator', $data);
    }
}
