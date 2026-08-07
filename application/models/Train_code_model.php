<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Train_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertTrainCode($data)
    {
        $this->db->insert('tbl_train_code', $data);
        return $this->db->insert_id();
    }

    public function updateTrainCode($id, $data)
    {
        $this->db->where('tbl_train_code.id', $id);
        return $this->db->update('tbl_train_code', $data);
    }

    public function deleteTrainCode($id) {
        $this->db->where('tbl_train_code.id', $id);
        return $this->db->delete('tbl_train_code');
    }

    public function getTrainCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_train_code');
        $this->db->where('tbl_train_code.id', $id);
        return $this->db->get()->row_array();
    }
    //
}