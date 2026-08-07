<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Suggestion_type_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        if (empty($data['code']) || empty($data['name'])) {
            return 0;
        }
        if ($this->isExistCode($data['code'])) {
            return 0;
        }

        $this->db->insert('tblsuggestion_type', $data);
        $rs = $this->db->insert_id();
        return $rs;
    }

    public function isExistCode($code)
    {
        $this->db->from('tblsuggestion_type');
        $this->db->where('tblsuggestion_type.code', $code);
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    public function isUsed($id = '')
    {
        // $this->db->where('id_costs', $id);
        // $tblother_payslips_coupon = $this->db->get('tblother_payslips_coupon')->row();

        // $this->db->where('id_costs', $id);
        // $vouchers_coupon = $this->db->get('tblvouchers_coupon')->row();
        // if (!empty($tblother_payslips_coupon) || !empty($vouchers_coupon)) {
        //     return true;
        // } else {
        //     return false;
        // }

        return false;
    }

    public function get ($id = '')
    {
        if (empty($id)) {
            return get_table_where('tblsuggestion_type', [], 'id desc');
        } else {
            return get_table_where('tblsuggestion_type', ['id' => $id], '', 'row');
        }
    }
}
