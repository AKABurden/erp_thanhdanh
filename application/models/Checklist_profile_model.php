<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checklist_profile_model extends App_Model
{
    private $table = 'tbl_checklist_profile';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get checklist by ID
     */
    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    /**
     * Insert new checklist
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update checklist
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete checklist
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get all checklists with filters
     */
    public function getAll($filters = [])
    {
        $this->db->from($this->table);
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('ma_checklist', $filters['search']);
            $this->db->or_like('ho_ten', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('ngay_tao', 'DESC');
        return $this->db->get()->result();
    }
}
