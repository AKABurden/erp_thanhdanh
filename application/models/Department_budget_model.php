<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Department_budget_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lấy bản ghi theo ID hoặc toàn bộ
     */
    public function get($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tbl_department_budget')->row();
        }
        return $this->db->get('tbl_department_budget')->result_array();
    }

    /**
     * Thêm mới bản ghi
     */
    public function add($data)
    {
        $this->db->insert('tbl_department_budget', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Add Department Budget [ID: ' . $insert_id . ']');
        }
        return $insert_id;
    }

    /**
     * Cập nhật bản ghi
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_department_budget', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Update Department Budget [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Xóa bản ghi
     */
    public function delete($id)
    {
        $record = $this->get($id);
        if (!$record) {
            return false;
        }
        $this->db->where('id', $id);
        $this->db->delete('tbl_department_budget');
        if ($this->db->affected_rows() > 0) {
            log_activity('Delete Department Budget [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
