<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Regulations_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRegulationsCode($data)
    {
        $this->db->insert('tbl_regulations_code', $data);
        return $this->db->insert_id();
    }

    public function updateRegulationsCode($id, $data)
    {
        $this->db->where('tbl_regulations_code.id', $id);
        return $this->db->update('tbl_regulations_code', $data);
    }

    public function deleteRegulationsCode($id) {
        $this->db->where('tbl_regulations_code.id', $id);
        return $this->db->delete('tbl_regulations_code');
    }

    public function getRegulationsCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_regulations_code');
        $this->db->where('tbl_regulations_code.id', $id);
        return $this->db->get()->row_array();
    }
    //
}