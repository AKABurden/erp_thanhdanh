<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_srceen_vp_clone extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function a() {

    }

    // function updatefinancialAccountingclient_contract()
    // {
    //     $this->db->select("
    //         tbl_contracts_sales.id,
    //         CONCAT(tbl_contracts_sales.prefix,'', tbl_contracts_sales.code) as code,
    //         tbl_contracts_sales.date_start,
    //         tbl_contracts_sales.date_end,
    //         tblclients.zcode as company
    //     ");
    //     $this->db->where('tbl_contracts_sales.date_end !=', NULL);
    //     $this->db->where('tbl_contracts_sales.date_end <', date('Y-m-d'));
    //     $this->db->join('tblclients', 'tblclients.userid=tbl_contracts_sales.customer_id', 'left');
    //     // Lấy hợp đồng có id lớn nhất cho mỗi customer
    //     $this->db->where('tbl_contracts_sales.id IN (
    //         SELECT MAX(id) FROM tbl_contracts_sales
    //         WHERE date_end IS NOT NULL AND date_end < "' . date('Y-m-d') . '"
    //         GROUP BY customer_id
    //     )', NULL, FALSE);

    //     $contracts_clients = $this->db->get('tbl_contracts_sales')->result_array();
    //     $stats = [];
    //     header('Content-Type: application/json');
    //     echo json_encode([
    //         'success'    => true,
    //         'stats' => $stats,
    //         'contracts_clients'  => array_map([$this, '_api_row_ncc_contract'], $contracts_clients),
    //         'changed_id' => null
    //     ]);
    // }

    // private function _api_row_ncc_contract($r)
    // {
    //     // Chuẩn hóa row theo format frontend đang dùng
    //     return [
    //         'id' => $r['id'],
    //         'date_start' => _d($r['date_start']),
    //         'date_end' => _d($r['date_end']),
    //         'code' => ($r['code']),
    //         'company' => ($r['company'])
    //     ];
    // }
}