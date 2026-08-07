<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Plan_propose_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function insertBatchTrain($data)
    {
        return $this->db->insert_batch('tblplan_propose_train', $data);
    }
    public function insertBatchtime($data)
    {
        return $this->db->insert_batch('tblplan_propose_time', $data);
    }
    public function infotb($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tbl_machines')->row_array();
    }
    public function infocost($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblcosts')->row_array();
    }
    public function infounit($id)
    {
        $this->db->where('unitid', $id);
        return $this->db->get('tblunits')->row_array();
    }
    public function infounitcost($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblcurrencies')->row_array();
    }
    public function infoobject($id)
    {
        $id = explode('__', $id);
        if ($id[0] == 'suppliers') {
            $this->db->select('CONCAT("suppliers__",tblsuppliers.id) as id, CONCAT(tblsuppliers.company," (",tblsuppliers.code,")") as text', false);
            $this->db->from('tblsuppliers');
            $this->db->where('id', $id[1]);
            return $this->db->get()->row_array();
        } else {
            $this->db->select('CONCAT("client__",tblclients.userid) as id, CONCAT(tblclients.company," (",tblclients.zcode,")") as text', false);
            $this->db->from('tblclients');
            $this->db->where('userid', $id[1]);
            return $this->db->get()->row_array();
        }
    }
    public function approvedBy($id)
    {
        $result = get_table_where('tblplan_propose', ['id' => $id], '', 'row');
        $approvedBy = !empty($result) ? (isset($result->status) ? $result->status : '0') : '0';
        return $approvedBy;
    }

    public function isApproved($id)
    {
        $isApproved    = $this->approvedBy($id);
        if ($isApproved == 0) {
            return false;
        } else {
            return true;
        }
    }
}
