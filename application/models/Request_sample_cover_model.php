<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Request_sample_cover_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRequestSampleCover($data) {
        $this->db->insert('tbl_request_sample_cover', $data);
        return $this->db->insert_id();
    }

    public function updateRequestSampleCover($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_request_sample_cover', $data);
    }

    public function getRequestSampleCover($id) {
        $this->db->select('tbl_request_sample_cover.*', false);
        $this->db->from('tbl_request_sample_cover');
        $this->db->where('tbl_request_sample_cover.id', $id);
        return $this->db->get()->row_array();
    }

    public function deleteRequestSampleCover($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_request_sample_cover');
    }
}