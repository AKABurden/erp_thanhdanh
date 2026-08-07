<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rules_group_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRules_group($data)
    {
        $this->db->insert('tbl_category_regulations', $data);
        return $this->db->insert_id();
    }

    public function updateRules_group($id, $data)
    {
        $this->db->where('tbl_category_regulations.id', $id);
        return $this->db->update('tbl_category_regulations', $data);
    }

    public function deleteRules_group($id) {
        $this->db->where('tbl_category_regulations.id', $id);
        return $this->db->delete('tbl_category_regulations');
    }

    public function getRules_groupById($id) {
        $this->db->select('*');
        $this->db->from('tbl_category_regulations');
        $this->db->where('tbl_category_regulations.id', $id);
        return $this->db->get()->row_array();
    }
    //
}