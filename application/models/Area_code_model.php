<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Area_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertAreaCode($data)
    {
        $this->db->insert('tbl_area_code', $data);
        return $this->db->insert_id();
    }

    public function updateAreaCode($id, $data)
    {
        $this->db->where('tbl_area_code.id', $id);
        return $this->db->update('tbl_area_code', $data);
    }

    public function deleteAreaCode($id) {
        $this->db->where('tbl_area_code.id', $id);
        return $this->db->delete('tbl_area_code');
    }

    public function getAreaCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_area_code');
        $this->db->where('tbl_area_code.id', $id);
        return $this->db->get()->row_array();
    }
    //
}