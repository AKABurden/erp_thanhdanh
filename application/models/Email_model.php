<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Email_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertEmail($data)
    {
        $this->db->insert('tbl_email', $data);
        return $this->db->insert_id();
    }

    public function updateEmail($id, $data)
    {
        $this->db->where('tbl_email.id', $id);
        return $this->db->update('tbl_email', $data);
    }

    public function deleteEmail($id) {
        $this->db->where('tbl_email.id', $id);
        return $this->db->delete('tbl_email');
    }

    public function getEmailById($id) {
        $this->db->select('*');
        $this->db->from('tbl_email');
        $this->db->where('tbl_email.id', $id);
        return $this->db->get()->row_array();
    }
    //
}