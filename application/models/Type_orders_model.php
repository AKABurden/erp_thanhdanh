<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Type_orders_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertTypeOrders($data)
    {
        $this->db->insert('tbl_type_orders', $data);
        return $this->db->insert_id();
    }

    public function updateTypeOrders($id, $data)
    {
        $this->db->where('tbl_type_orders.id', $id);
        return $this->db->update('tbl_type_orders', $data);
    }

    public function deleteTypeOrders($id) {
        $this->db->where('tbl_type_orders.id', $id);
        return $this->db->delete('tbl_type_orders');
    }

    public function getTypeOrdersById($id) {
        $this->db->select('*');
        $this->db->from('tbl_type_orders');
        $this->db->where('tbl_type_orders.id', $id);
        return $this->db->get()->row_array();
    }

    public function getTypeOrders() {
        $this->db->select('*');
        $this->db->from('tbl_type_orders');
        $this->db->where('tbl_type_orders.id !=', 3);
        return $this->db->get()->result_array();
    }

    public function rowTypeOrdersByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_type_orders');
        if ($option == "like") {
            $this->db->like('tbl_type_orders.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_type_orders.code', $code);
        }
        return $this->db->get()->row_array();
    }

    public function isUseTypeOrders($id) {
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.type_orders', $id);
        $q = $this->db->count_all_results();
        if (!empty($q)) {
            return $q;
        }
        return false;
    }
}