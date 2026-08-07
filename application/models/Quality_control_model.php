<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Quality_control_model extends App_Model
{
    private $contact_columns;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertCategoryErrors($data)
    {
        $this->db->insert('tbl_category_errors', $data);

        return $this->db->insert_id();
    }

    public function rowCategoryErrors($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_category_errors');
        $this->db->where('id', $id);

        return $this->db->get()->row_array();
    }

    public function updateCategoryErrors($id, $data)
    {
        $this->db->where('id', $id);

        return $this->db->update('tbl_category_errors', $data);
    }

    public function deleteCategoryErrors($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete('tbl_category_errors');
    }

    public function checkExistCategoryError($id)
    {
        $this->db->from('tbl_detail_errors');
        $this->db->where('tbl_detail_errors.category_error_id', $id);
        $this->db->limit(1);

        return $this->db->get()->num_rows();
    }

    public function checkParentCategoryErrorId($id)
    {
        $this->db->from('tbl_category_errors');
        $this->db->where('tbl_category_errors.parent_id', $id);

        return $this->db->get()->num_rows();
    }

    public function insertCategoryChecks($data)
    {
        $this->db->insert('tbl_category_checks', $data);

        return $this->db->insert_id();
    }

    public function rowCategoryChecks($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_category_checks');
        $this->db->where('id', $id);

        return $this->db->get()->row_array();
    }

    public function updateCategoryChecks($id, $data)
    {
        $this->db->where('id', $id);

        return $this->db->update('tbl_category_checks', $data);
    }

    public function deleteCategoryChecks($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete('tbl_category_checks');
    }

    public function checkExistCategoryChecks($id)
    {
        // $this->db->from('tbl_products');
        // $this->db->where('tbl_products.category_id', $id);
        // $this->db->limit(1);
        // return $this->db->get()->num_rows();
        return 0;
    }

    public function insertCategoryCauseErrors($data)
    {
        $this->db->insert('tbl_category_cause_errors', $data);

        return $this->db->insert_id();
    }

    public function rowCategoryCauseErrors($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_category_cause_errors');
        $this->db->where('id', $id);

        return $this->db->get()->row_array();
    }

    public function updateCategoryCauseErrors($id, $data)
    {
        $this->db->where('id', $id);

        return $this->db->update('tbl_category_cause_errors', $data);
    }

    public function deleteCategoryCauseErrors($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete('tbl_category_cause_errors');
    }

    public function checkExistCategoryCauseErrors($id)
    {
        // $this->db->from('tbl_products');
        // $this->db->where('tbl_products.category_id', $id);
        // $this->db->limit(1);
        // return $this->db->get()->num_rows();
        return 0;
    }

    public function insertCategoryResults($data)
    {
        $this->db->insert('tbl_category_results', $data);

        return $this->db->insert_id();
    }

    public function rowCategoryResults($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_category_results');
        $this->db->where('id', $id);

        return $this->db->get()->row_array();
    }

    public function updateCategoryResults($id, $data)
    {
        $this->db->where('id', $id);

        return $this->db->update('tbl_category_results', $data);
    }

    public function deleteCategoryResults($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete('tbl_category_results');
    }

    public function checkExistCategoryResults($id)
    {
        // $this->db->from('tbl_products');
        // $this->db->where('tbl_products.category_id', $id);
        // $this->db->limit(1);
        // return $this->db->get()->num_rows();
        return 0;
    }

    public function insertDetailErrors($data)
    {
        $this->db->insert('tbl_detail_errors', $data);

        return $this->db->insert_id();
    }

    public function rowDetailErrors($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_detail_errors');
        $this->db->where('id', $id);

        return $this->db->get()->row_array();
    }

    public function updateDetailErrors($id, $data)
    {
        $this->db->where('id', $id);

        return $this->db->update('tbl_detail_errors', $data);
    }

    public function deleteDetailErrors($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete('tbl_detail_errors');
    }

    public function checkExistDetailErrors($id)
    {
        // $this->db->from('tbl_products');
        // $this->db->where('tbl_products.category_id', $id);
        // $this->db->limit(1);
        // return $this->db->get()->num_rows();
        return 0;
    }

    public function getCategoryErrors()
    {
        $this->db->select('*');
        $this->db->from('tbl_category_errors');

        return $this->db->get()->result_array();
    }

    public function getCategoryResults()
    {
        $this->db->select('*');
        $this->db->from('tbl_category_results');

        return $this->db->get()->result_array();
    }

    public function getCategoryChecks()
    {
        $this->db->select('*');
        $this->db->from('tbl_category_checks');

        return $this->db->get()->result_array();
    }

    public function getCategoryCauseErrors()
    {
        $this->db->select('*');
        $this->db->from('tbl_category_cause_errors');

        return $this->db->get()->result_array();
    }

    public function searchDetailErrors($q, $limit = 50, $category_error_id)
    {
        $this->db->select('tbl_detail_errors.id as id, tbl_detail_errors.name as text', false);
        $this->db->from('tbl_detail_errors');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_detail_errors.code', $q);
            $this->db->or_like('tbl_detail_errors.name', $q);
            $this->db->group_end();
        }
        if (!empty($category_error_id)) {
            $this->db->where('tbl_detail_errors.category_error_id', $category_error_id);
        }
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    public function insertCheckQuality($data)
    {
        $this->db->insert('tbl_check_quality', $data);

        return $this->db->insert_id();
    }

    public function insertCheckQualityItem($data)
    {
        $this->db->insert('tbl_check_quality_items', $data);

        return $this->db->insert_id();
    }

    public function insertCheckQualityItemError($data)
    {
        $this->db->insert('tbl_check_quality_items_error', $data);

        return $this->db->insert_id();
    }

    public function insertBatchCheckQualityItem($data)
    {
        return $this->db->insert_batch('tbl_check_quality_items', $data);
    }

    public function rowCheckQuality($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_check_quality');
        $this->db->where('tbl_check_quality.id', $id);

        return $this->db->get()->row_array();
    }

    public function getCheckQualityItems($check_quality_id)
    {
        $this->db->select('
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_category_checks.name as category_check_name,
            tbl_category_results.name as category_result_name,
            tbl_category_errors.name as category_error_name,
            tbl_detail_errors.name as detail_error_name,
            tbl_category_cause_errors.name as category_cause_error_name,
        ', false);
        $this->db->from('tbl_check_quality_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_check_quality_items.item_id', 'left');
        $this->db->join('tbl_category_checks', 'tbl_category_checks.id = tbl_check_quality_items.category_check_id',
            'left');
        $this->db->join('tbl_category_results', 'tbl_category_results.id = tbl_check_quality_items.category_result_id',
            'left');
        $this->db->join('tbl_category_errors', 'tbl_category_errors.id = tbl_check_quality_items.category_error_id',
            'left');
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items.detail_error_id', 'left');
        $this->db->join('tbl_category_cause_errors',
            'tbl_category_cause_errors.id = tbl_check_quality_items.category_cause_error_id', 'left');
        $this->db->where('tbl_check_quality_items.check_quality_id', $check_quality_id);

        return $this->db->get()->result_array();
    }

    public function deleteCheckQuality($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete('tbl_check_quality');
    }

    public function deleteCheckQualityItems($check_quality_id)
    {
        $this->db->where('check_quality_id', $check_quality_id);

        return $this->db->delete('tbl_check_quality_items');
    }

    public function deleteCheckQualityItemsError($id_check_quality_item)
    {
        $this->db->where('id_check_quality_item', $id_check_quality_item);

        return $this->db->delete('tbl_check_quality_items_error');
    }

    public function updateCheckQuality($id, $data = [])
    {
        $this->db->where('id', $id);

        return $this->db->update('tbl_check_quality', $data);
    }

    public function checkExistCheckQualityReferenceNo($reference_no)
    {
        $this->db->from('tbl_check_quality');
        $this->db->where('tbl_check_quality.reference_no', $reference_no);

        return $this->db->get()->num_rows();
    }

    public function countQcByStatus($status_table)
    {
        $this->db->from('tbl_check_quality');
        if ($status_table == "manager_approve") {
            $this->db->where('tbl_check_quality.status_process', 1);
        } elseif ($status_table == "gdx_approve") {
            $this->db->where('tbl_check_quality.status_process', 0);
        }

        return $this->db->get()->num_rows();
    }

    // yct start
    public function isExist_CategoryError_byCode($code)
    {
        if (empty($code))
            return false;
        $this->db->from('tbl_category_errors');
        $this->db->where('tbl_category_errors.code', $code);
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
    public function getCategoryError_byCode($code)
    {
        $result = get_table_where('tbl_category_errors', ['code' => $code], '', 'row');
        return $result;
    }

    public function isExist_DetailError_byCode($code)
    {
        if (empty($code))
            return false;
        $this->db->from('tbl_detail_errors');
        $this->db->where('tbl_detail_errors.code', $code);
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    // yct end
}