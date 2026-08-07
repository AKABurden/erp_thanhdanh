<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Print_type_model extends App_Model
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

        $this->db->insert('tbl_type_print', $data);
        $rs = $this->db->insert_id();
        return $rs;
    }

    public function isExistCode($code)
    {
        $this->db->from('tbl_type_print');
        $this->db->where('tbl_type_print.code', $code);
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
        $this->db->where('type_print', $id);
        $tbl_products = $this->db->get('tbl_products')->row();

        if (!empty($tbl_products)) {
            return true;
        } else {
            return false;
        }
    }

    public function get ($id = '')
    {
        if (empty($id)) {
            return get_table_where('tbl_type_print', [], 'id desc');
        } else {
            return get_table_where('tbl_type_print', ['id' => $id], '', 'row');
        }
    }
}
