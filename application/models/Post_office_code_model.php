<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Post_office_code_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertPostOfficeCode($data)
    {
        $this->db->insert('tbl_post_office_code', $data);
        return $this->db->insert_id();
    }

    public function updatePostOfficeCode($id, $data)
    {
        $this->db->where('tbl_post_office_code.id', $id);
        return $this->db->update('tbl_post_office_code', $data);
    }

    public function deletePostOfficeCode($id) {
        $this->db->where('tbl_post_office_code.id', $id);
        return $this->db->delete('tbl_post_office_code');
    }

    public function getPostOfficeCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_post_office_code');
        $this->db->where('tbl_post_office_code.id', $id);
        return $this->db->get()->row_array();
    }
    //
}