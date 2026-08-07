<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Propose_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertProposeCode($data)
    {
        $this->db->insert('tbl_propose_code', $data);
        return $this->db->insert_id();
    }

    public function updateProposeCode($id, $data)
    {
        $this->db->where('tbl_propose_code.id', $id);
        return $this->db->update('tbl_propose_code', $data);
    }

    public function deleteProposeCode($id) {
        $this->db->where('tbl_propose_code.id', $id);
        return $this->db->delete('tbl_propose_code');
    }

    public function getProposeCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_propose_code');
        $this->db->where('tbl_propose_code.id', $id);
        return $this->db->get()->row_array();
    }
    //
}