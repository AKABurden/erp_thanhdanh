<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Internal_proposal_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCode($id = '')
    {
        if (empty($id)) { // create new code
            $code = 'DXNB' . '-' . sprintf('%06d', ch_getMaxID('id', 'tblinternal_proposal') + 1);
        } else { // get existed
            $code = get_table_where('tblinternal_proposal', ['id' => $id], '', 'row', '', 'code')->code;
        }
        return $code;
    }

    public function isExistCode($code)
    {
        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.code', $code);
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    public function approvedBy ($id)
    {
        $result = get_table_where('tblinternal_proposal', ['id' => $id], '', 'row');
        $approvedBy = !empty($result) ? (isset($result->status) ? $result->status : '0') : '0';
        return $approvedBy;
    }

    public function isApproved ($id)
    {
        $isApproved	= $this->approvedBy($id);
        if ($isApproved == 0) {
            return false;
        } else {
            return true;
        }
    }
    public function approvedBy_staff ($id)
    {
        $result = get_table_where('tblinternal_proposal', ['id' => $id], '', 'row');
        $approvedBy = !empty($result) ? (isset($result->status_staff) ? $result->status_staff : '0') : '0';
        return $approvedBy;
    }

    public function isApproved_staff ($id)
    {
        $isApproved	= $this->approvedBy_staff($id);
        if ($isApproved == 0) {
            return false;
        } else {
            return true;
        }
    }
}