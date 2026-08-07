<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Salary_deadline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertSalaryDeadline($data)
    {
        $this->db->insert('tbl_salary_deadline', $data);
        return $this->db->insert_id();
    }

    public function updateSalaryDeadline($id, $data)
    {
        $this->db->where('tbl_salary_deadline.id', $id);
        return $this->db->update('tbl_salary_deadline', $data);
    }

    public function deleteSalaryDeadline($id) {
        $this->db->where('tbl_salary_deadline.id', $id);
        return $this->db->delete('tbl_salary_deadline');
    }

    public function getSalaryDeadlineById($id) {
        $this->db->select('*');
        $this->db->from('tbl_salary_deadline');
        $this->db->where('tbl_salary_deadline.id', $id);
        return $this->db->get()->row_array();
    }
    //
}