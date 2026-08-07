<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contract_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertContractCode($data)
    {
        $this->db->insert('tbl_contract_code', $data);
        return $this->db->insert_id();
    }

    public function updateContractCode($id, $data)
    {
        $this->db->where('tbl_contract_code.id', $id);
        return $this->db->update('tbl_contract_code', $data);
    }

    public function deleteContractCode($id) {
        $this->db->where('tbl_contract_code.id', $id);
        return $this->db->delete('tbl_contract_code');
    }

    public function getContractCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_contract_code');
        $this->db->where('tbl_contract_code.id', $id);
        return $this->db->get()->row_array();
    }
    //
}