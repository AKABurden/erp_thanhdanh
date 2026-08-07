<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Department_work_norms_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertDepartmentWorkNorms($data)
    {
        $this->db->insert('tbl_department_work_norms', $data);
        return $this->db->insert_id();
    }

    public function updateDepartmentWorkNorms($id, $data)
    {
        $this->db->where('tbl_department_work_norms.id', $id);
        return $this->db->update('tbl_department_work_norms', $data);
    }

    public function deleteDepartmentWorkNorms($id) {
        $this->db->where('tbl_department_work_norms.id', $id);
        return $this->db->delete('tbl_department_work_norms');
    }

    public function getDepartmentWorkNormsById($id) {
        $this->db->select('*');
        $this->db->from('tbl_department_work_norms');
        $this->db->where('tbl_department_work_norms.id', $id);
        return $this->db->get()->row_array();
    }
    //
}