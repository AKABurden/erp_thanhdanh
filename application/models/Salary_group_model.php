<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Salary_group_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRegulationsCode($data)
    {
        $this->db->insert('tbl_salary_group', $data);
        return $this->db->insert_id();
    }

    public function updateRegulationsCode($id, $data)
    {
        $this->db->where('tbl_salary_group.id', $id);
        return $this->db->update('tbl_salary_group', $data);
    }

    public function deleteRegulationsCode($id) {
        $this->db->where('tbl_salary_group.id', $id);
        return $this->db->delete('tbl_salary_group');
    }

    public function getRegulationsCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_salary_group');
        $this->db->where('tbl_salary_group.id', $id);
        return $this->db->get()->row_array();
    }
    //
}