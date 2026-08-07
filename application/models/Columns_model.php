<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Columns_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertColumns($data)
    {
        $this->db->insert('tbl_columns', $data);
        return $this->db->insert_id();
    }

    public function updateColumns($id, $data)
    {
        $this->db->where('tbl_columns.id', $id);
        return $this->db->update('tbl_columns', $data);
    }

    public function deleteColumns($id) {
        $this->db->where('tbl_columns.id', $id);
        return $this->db->delete('tbl_columns');
    }

    public function getColumnsById($id) {
        $this->db->select('*');
        $this->db->from('tbl_columns');
        $this->db->where('tbl_columns.id', $id);
        return $this->db->get()->row_array();
    }

    //
    public function insertColumnsDetail($data)
    {
        $this->db->insert('tbl_columns_detail', $data);
        return $this->db->insert_id();
    }

    public function insertBatchColumnsDetail($data)
    {
        return $this->db->insert_batch('tbl_columns_detail', $data);
    }

    public function updateColumnsDetail($id, $data)
    {
        $this->db->where('tbl_columns_detail.id', $id);
        return $this->db->update('tbl_columns_detail', $data);
    }

    public function deleteColumnsDetail($columns_id) {
        $this->db->where('tbl_columns_detail.columns_id', $columns_id);
        return $this->db->delete('tbl_columns_detail');
    }

    public function getColumnsDetailById($columns_id) {
        $this->db->select('*');
        $this->db->from('tbl_columns_detail');
        $this->db->where('tbl_columns_detail.columns_id', $columns_id);
        return $this->db->get()->row_array();
    }

    public function getColumnsDetail($columns_id) {
        $this->db->select('*');
        $this->db->from('tbl_columns_detail');
        $this->db->where('tbl_columns_detail.columns_id', $columns_id);
        return $this->db->get()->result_array();
    }
    public function getColumns_full_text() {
        $tbColumnsDetail = "(
            SELECT
                tbl_columns_detail.columns_id as columns_id,
                GROUP_CONCAT(tbl_columns_detail.name SEPARATOR '|') as name
            FROM tbl_columns_detail
            GROUP BY tbl_columns_detail.columns_id
        ) tb_columns_detail";
        $this->db->select('tbl_columns.*,tb_columns_detail.name as name_detail');
        $this->db->from('tbl_columns');
        $this->db->join($tbColumnsDetail, 'tb_columns_detail.columns_id=tbl_columns.id', 'left');
        return $this->db->get()->result_array();
    }
    public function getColumns() {
        $this->db->select('*');
        $this->db->from('tbl_columns');
        return $this->db->get()->result_array();
    }

    public function getColumnsByCode($code) {
        $this->db->select('*');
        $this->db->from('tbl_columns');
        $this->db->where('tbl_columns.code', $code);
        return $this->db->get()->row_array();
    }

    public function isUseColumns($columns_id) {
        $this->db->from('tbl_products_columns');
        $this->db->where('tbl_products_columns.columns_id', $columns_id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }
}