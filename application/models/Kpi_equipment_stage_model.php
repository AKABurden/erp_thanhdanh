<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_equipment_stage_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get KPI equipment stage record by ID or all records
     * Lấy bản ghi KPI thiết bị công đoạn theo ID hoặc toàn bộ
     */
    public function get($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tbl_kpi_equipment_stage')->row();
        }
        return $this->db->get('tbl_kpi_equipment_stage')->result_array();
    }

    /**
     * Add new KPI equipment stage record
     * Thêm mới bản ghi KPI thiết bị công đoạn
     */
    public function add($data)
    {
        $this->db->insert('tbl_kpi_equipment_stage', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('Add KPI Equipment Stage [ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * Update KPI equipment stage record
     * Cập nhật bản ghi KPI thiết bị công đoạn
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_kpi_equipment_stage', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Update KPI Equipment Stage [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete KPI equipment stage record
     * Xóa bản ghi KPI thiết bị công đoạn
     */
    public function delete($id)
    {
        $record = $this->get($id);

        if (!$record) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete('tbl_kpi_equipment_stage');

        if ($this->db->affected_rows() > 0) {
            log_activity('Delete KPI Equipment Stage [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}
