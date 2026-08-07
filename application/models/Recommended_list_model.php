<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recommended_list_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRecommendedList($data)
    {
        $this->db->insert('tbl_recommended_list', $data);
        return $this->db->insert_id();
    }

    public function rowRecommendedList($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_recommended_list');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowRecommendedListByCode($code)
    {
        $this->db->select('*');
        $this->db->from('tbl_recommended_list');
        $this->db->where('code', $code);
        return $this->db->get()->row_array();
    }

    public function getRecommendedList()
    {
        $this->db->select('*');
        $this->db->from('tbl_recommended_list');
        return $this->db->get()->result_array();
    }

    public function updateRecommendedList($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_recommended_list', $data);
    }

    public function deleteRecommendedList($id)
    {
        $this->db->where('id', $id);
        $rs = $this->db->delete('tbl_recommended_list');
        if ($rs) {
            $this->db->where('tbl_recommended_list.parent_id', $id);
            $this->db->delete('tbl_recommended_list');
            return true;
        }
        return false;
    }

    public function getRecommendedListByCode($code) {
        $this->db->select('*');
        $this->db->from('tbl_recommended_list');
        $this->db->where('tbl_recommended_list.code', $code);
        return $this->db->get()->row_array();
    }
    public function getRelateParent($parent_id = [],$type_show = 0) {
        $this->db->select('tbl_relate.*');
        $this->db->from('tbl_relate');
        if (!empty($parent_id)) {
            $this->db->where_in('tbl_relate.parent_id', $parent_id);
        }
        if (!empty($type_show)){
            $this->db->where('tbl_relate.type_show', 1);
        }

        return $this->db->get()->result_array();
    }
    public function getRecommendedListParent($parent_id = [],$type_show = 0) {
        $this->db->select('tbl_recommended_list.*');
        $this->db->from('tbl_recommended_list');
        if (!empty($parent_id)) {
            $this->db->where_in('tbl_recommended_list.parent_id', $parent_id);
        }
        if (!empty($type_show)){
            $this->db->where('tbl_recommended_list.type_show', 1);
        }

        return $this->db->get()->result_array();
    }

    public function insertBatchProductionReportReason($data) {
        return $this->db->insert_batch('tbl_production_report_reason', $data);
    }

    public function deleteProductionReportReason($pr_id) {
        $this->db->where('pr_id', $pr_id);
        return $this->db->delete('tbl_production_report_reason');
    }

    public function getProductionReportReason($pr_id, $key = '', $is_multiple = false) {
        $this->db->select('
            tbl_production_report_reason.*
        ', false);
        $this->db->from('tbl_production_report_reason');
        $this->db->where('tbl_production_report_reason.pr_id', $pr_id);
        $items = $this->db->get()->result_array();

        if (empty($key)) {
            return $items;
        }

        $grouped = [];
        foreach ($items as $item) {
            if (!array_key_exists($key, $item)) {
                continue;
            }
            $id = $item[$key];
            if ($is_multiple) {
                $grouped[$id][] = $item;
            } else {
                $grouped[$id] = $item;
            }
        }

        return $grouped;
    }

    public function getRelate() {
        $this->db->select('tbl_relate.*');
        $this->db->from('tbl_relate');
        return $this->db->get()->result_array();
    }
}