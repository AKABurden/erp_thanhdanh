<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Species_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertSpecies($data)
    {
        $this->db->insert('tbl_species', $data);
        return $this->db->insert_id();
    }

    public function updateSpecies($id, $data)
    {
        $this->db->where('tbl_species.id', $id);
        return $this->db->update('tbl_species', $data);
    }

    public function deleteSpecies($id) {
        $this->db->where('tbl_species.id', $id);
        return $this->db->delete('tbl_species');
    }

    public function getSpeciesById($id) {
        $this->db->select('*');
        $this->db->from('tbl_species');
        $this->db->where('tbl_species.id', $id);
        return $this->db->get()->row_array();
    }

    public function getSpecies() {
        $this->db->select('*');
        $this->db->from('tbl_species');
        return $this->db->get()->result_array();
    }

    public function rowSpeciesByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_species');
        if ($option == "like") {
            $this->db->like('tbl_species.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_species.code', $code);
        }
        return $this->db->get()->row_array();
    }

    // yct start
    public function checkCodeExistSpecies($code) {
        $this->db->from('tbl_species');
        $this->db->where('tbl_species.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }
    // yct end
}