<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_management_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get audit by ID
     */
    public function getAuditById($id)
    {
        return $this->db->get_where('tbl_audit', ['id' => $id])->row();
    }

    /**
     * Get all audits
     */
    public function getAllAudits()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('tbl_audit')->result();
    }

    /**
     * Get audit count by month
     */
    public function getAuditCountByMonth($month)
    {
        $this->db->like('created_at', $month, 'after');
        return $this->db->count_all_results('tbl_audit');
    }

    /**
     * Get critical issues count
     */
    public function getCriticalIssuesCount()
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('tbl_audit_checklist');
        // $this->db->where('critical', 1);
        $this->db->where('status', 'no');
        $result = $this->db->get()->row();
        return $result ? $result->count : 0;
    }

    /**
     * Get completion rate
     */
    public function getCompletionRate()
    {
        // Get total completed audits this month
        $this->db->where('status', 'COMPLETED');
        $this->db->like('created_at', date('Y-m'), 'after');
        $completed = $this->db->count_all_results('tbl_audit');

        // Get total audits this month
        $this->db->like('created_at', date('Y-m'), 'after');
        $total = $this->db->count_all_results('tbl_audit');

        if ($total == 0) return 100;
        
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get next audit number for code generation
     */
    public function getNextAuditNumber()
    {
        $this->db->like('audit_code', 'AUD-' . date('Y'), 'after');
        $count = $this->db->count_all_results('tbl_audit');
        return $count + 1;
    }

    /**
     * Insert new audit
     */
    public function insertAudit($data)
    {
        $this->db->insert('tbl_audit', $data);
        return $this->db->insert_id();
    }

    /**
     * Update audit
     */
    public function updateAudit($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_audit', $data);
    }

    /**
     * Delete audit
     */
    public function deleteAudit($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_audit');
    }

    /**
     * Get checklist by audit ID
     */
    public function getChecklistByAuditId($audit_id)
    {
        $this->db->select('tbl_audit_checklist.*, tbl_room.name as room_name, tbldepartments.name as department_name');
        $this->db->from('tbl_audit_checklist');
        $this->db->join('tbl_room', 'tbl_room.id = tbl_audit_checklist.room_id', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_audit_checklist.department_id', 'left');
        $this->db->where('tbl_audit_checklist.audit_id', $audit_id);
        $this->db->order_by('tbl_audit_checklist.id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Update checklist item
     */
    public function updateChecklistItem($item_id, $data)
    {
        $this->db->where('id', $item_id);
        return $this->db->update('tbl_audit_checklist', $data);
    }

    /**
     * Delete checklist by audit ID
     */
    public function deleteChecklistByAuditId($audit_id)
    {
        $this->db->where('audit_id', $audit_id);
        return $this->db->delete('tbl_audit_checklist');
    }

    /**
     * Get CAPA by audit ID
     */
    public function getCapaByAuditId($audit_id)
    {
        $this->db->where('audit_id', $audit_id);
        return $this->db->get('tbl_audit_capa')->result();
    }

    /**
     * Update CAPA
     */
    public function updateCapa($capa_id, $data)
    {
        $this->db->where('id', $capa_id);
        return $this->db->update('tbl_audit_capa', $data);
    }
}
