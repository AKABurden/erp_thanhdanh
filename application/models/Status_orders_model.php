<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Status_orders_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertStatusOrders($data)
    {
        $this->db->insert('tbl_status_orders', $data);
        return $this->db->insert_id();
    }

    public function updateStatusOrders($id, $data)
    {
        $this->db->where('tbl_status_orders.id', $id);
        return $this->db->update('tbl_status_orders', $data);
    }

    public function deleteStatusOrders($id) {
        $this->db->where('tbl_status_orders.id', $id);
        return $this->db->delete('tbl_status_orders');
    }

    public function getStatusOrdersById($id) {
        $this->db->select('*');
        $this->db->from('tbl_status_orders');
        $this->db->where('tbl_status_orders.id', $id);
        return $this->db->get()->row_array();
    }

    public function getStatusOrders() {
        $this->db->select('*');
        $this->db->from('tbl_status_orders');
        return $this->db->get()->result_array();
    }

    public function rowStatusOrdersByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_status_orders');
        if ($option == "like") {
            $this->db->like('tbl_status_orders.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_status_orders.code', $code);
        }
        return $this->db->get()->row_array();
    }

    public function isUseStatusOrders($id) {
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.status_orders', $id);
        $q = $this->db->count_all_results();
        if (!empty($q)) {
            return $q;
        }
        return false;
    }
}