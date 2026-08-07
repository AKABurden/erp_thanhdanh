<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Salary_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertSalaryScoreboard($data) {
        $this->db->insert('tbl_salary_scoreboard', $data);
        return $this->db->insert_id();
    }

    public function updateSalaryScoreboard($role_id, $leve_id, $data) {
        $this->db->where('tbl_salary_scoreboard.role_id', $role_id);
        $this->db->where('tbl_salary_scoreboard.leve_id', $leve_id);
        return $this->db->update('tbl_salary_scoreboard', $data);
    }

    public function getSalaryScoreboard($role_id, $leve_id) {
        $this->db->select('*');
        $this->db->from('tbl_salary_scoreboard');
        return $this->db->get()->row_array();
    }
}