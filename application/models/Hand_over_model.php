<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hand_over_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertCategoryHandOver($data)
    {
        $this->db->insert('tbl_category_hand_over', $data);
        return $this->db->insert_id();
    }

    public function updateCategoryHandOver($id, $data)
    {
        $this->db->where('tbl_category_hand_over.id', $id);
        return $this->db->update('tbl_category_hand_over', $data);
    }

    public function deleteCategoryHandOver($id) {
        $this->db->where('tbl_category_hand_over.id', $id);
        return $this->db->delete('tbl_category_hand_over');
    }

    public function getCategoryHandOverById($id) {
        $this->db->select('*');
        $this->db->from('tbl_category_hand_over');
        $this->db->where('tbl_category_hand_over.id', $id);
        return $this->db->get()->row_array();
    }

    public function getModuleHandOver() {
        $this->db->select('tbl_module_hand_over.*');
        $this->db->from('tbl_module_hand_over');
        return $this->db->get()->result_array();
    }

    public function getCategoryHandOver() {
        $this->db->select('*');
        $this->db->from('tbl_category_hand_over');
        return $this->db->get()->result_array();
    }

    //
    public function insertHandOverTask($data)
    {
        $this->db->insert('tbl_hand_over_task', $data);
        return $this->db->insert_id();
    }

    public function updateHandOverTask($id, $data)
    {
        $this->db->where('tbl_hand_over_task.id', $id);
        return $this->db->update('tbl_hand_over_task', $data);
    }

    public function deleteHandOverTask($id) {
        $this->db->where('tbl_hand_over_task.id', $id);
        return $this->db->delete('tbl_hand_over_task');
    }

    public function getHandOverTaskById($id) {
        $this->db->select('*');
        $this->db->from('tbl_hand_over_task');
        $this->db->where('tbl_hand_over_task.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertDeliveryRecords($data) {
        $this->db->insert('tbl_delivery_records', $data);
        return $this->db->insert_id();
    }

    public function updateDeliveryRecords($id, $data) {
        $this->db->where('tbl_delivery_records.id', $id);
        return $this->db->update('tbl_delivery_records', $data);
    }

    public function insertDeliveryRecordsModule($data) {
        return $this->db->insert_batch('tbl_delivery_records_module', $data);
    }

    public function insertDeliveryRecordsTask($data) {
        return $this->db->insert_batch('tbl_delivery_records_task', $data);
    }

    public function deleteDeliveryRecords($id) {
        $this->db->where('tbl_delivery_records.id', $id);
        return $this->db->delete('tbl_delivery_records');
    }

    public function getDeliveryRecordsById($id) {
        $this->db->select('*');
        $this->db->from('tbl_delivery_records');
        $this->db->where('tbl_delivery_records.id', $id);
        return $this->db->get()->row_array();
    }

    public function getDeliveryRecordsModule($delivery_records_id) {
        $this->db->select('*');
        $this->db->from('tbl_delivery_records_module');
        $this->db->where('tbl_delivery_records_module.delivery_records_id', $delivery_records_id);
        return $this->db->get()->result_array();
    }

    public function getDeliveryRecordsTaskById($delivery_records_id, $hand_over_task_id) {
        $this->db->select('*');
        $this->db->from('tbl_delivery_records_task');
        $this->db->where('tbl_delivery_records_task.delivery_records_id', $delivery_records_id);
        $this->db->where('tbl_delivery_records_task.hand_over_task_id', $hand_over_task_id);
        return $this->db->get()->result_array();
    }

	public function getDeliveryRecordsId($delivery_records_id) {
        $this->db->select('tbl_delivery_records_task.*, tbl_hand_over_task.code, tbl_hand_over_task.name, tbl_packaging.code as standard, method, tbl_stages.code as code_stage');
        $this->db->from('tbl_delivery_records_task');
        $this->db->where('tbl_delivery_records_task.delivery_records_id', $delivery_records_id);
		$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id');
		$this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
		$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
        return $this->db->get()->result_array();
    }

    public function deleteDeliveryRecordsTask($id) {
        $this->db->where('tbl_delivery_records_task.delivery_records_id', $id);
        return $this->db->delete('tbl_delivery_records_task');
    }

    public function deleteDeliveryRecordsModule($id) {
        $this->db->where('tbl_delivery_records_module.delivery_records_id', $id);
        return $this->db->delete('tbl_delivery_records_module');
    }

    public function isCategoryHandOver($id) {
        $this->db->from('tbl_hand_over_task');
        $this->db->where('tbl_hand_over_task.category_hand_over_id', $id);
        $this->db->limit(1);
        $rs = $this->db->get()->num_rows();
        if ($rs) {
            return true;
        }

        return false;

    }

    public function isHandOverTask($id) {
        $this->db->from('tbl_delivery_records_task');
        $this->db->where('tbl_delivery_records_task.hand_over_task_id', $id);
        $this->db->limit(1);
        $rs = $this->db->get()->num_rows();
        if ($rs) {
            return true;
        }

        return false;

    }

    public function getCategoryIdByName($name) {
        if (empty($name)) {
            return 0;
        }
        $result = get_table_where('tbl_category_hand_over', ['name' => $name], '', 'row', '', 'id');
        if (!empty($result)) {
            return $result->id;
        } else {
            return 0;
        }
    }
    public function getTaskIdByCode($code) {
        if (empty($code)) {
            return 0;
        }
        $result = get_table_where('tbl_hand_over_task', ['code' => $code], '', 'row', '', 'id');
        if (!empty($result)) {
            return $result->id;
        } else {
            return 0;
        }
    }

    public function getCategoryIdByCode ($code)
    {
        if (empty($code)) {
            return 0;
        }
        $result = get_table_where('tbl_category_hand_over', ['code' => $code], '', 'row', '', 'id');
        if (!empty($result)) {
            return $result->id;
        } else {
            return 0;
        }
    }
}