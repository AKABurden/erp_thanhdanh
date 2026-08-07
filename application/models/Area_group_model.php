<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Area_group_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertAreaGroup($data)
    {
        $this->db->insert('tbl_area_group', $data);
        return $this->db->insert_id();
    }

    public function updateAreaGroup($id, $data)
    {
        $this->db->where('tbl_area_group.id', $id);
        return $this->db->update('tbl_area_group', $data);
    }

    public function deleteAreaGroup($id) {
        $this->db->where('tbl_area_group.id', $id);
        return $this->db->delete('tbl_area_group');
    }

    public function getAreaGroupById($id) {
        $this->db->select('*');
        $this->db->from('tbl_area_group');
        $this->db->where('tbl_area_group.id', $id);
        return $this->db->get()->row_array();
    }
    //
}