<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Physical_deadline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertPhysicalDeadline($data)
    {
        $this->db->insert('tbl_physical_deadline', $data);
        return $this->db->insert_id();
    }

    public function updatePhysicalDeadline($id, $data)
    {
        $this->db->where('tbl_physical_deadline.id', $id);
        return $this->db->update('tbl_physical_deadline', $data);
    }

    public function deletePhysicalDeadline($id) {
        $this->db->where('tbl_physical_deadline.id', $id);
        return $this->db->delete('tbl_physical_deadline');
    }

    public function getPhysicalDeadlineById($id) {
        $this->db->select('*');
        $this->db->from('tbl_physical_deadline');
        $this->db->where('tbl_physical_deadline.id', $id);
        return $this->db->get()->row_array();
    }
    //
}