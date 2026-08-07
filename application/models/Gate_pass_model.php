<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gate_pass_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCode($id = '')
    {
        if (empty($id)) { // create new code
            $code = 'GP' . '-' . sprintf('%06d', ch_getMaxID('id', 'tblgate_pass') + 1);
        } else { // get existed
            $code = get_table_where('tblgate_pass', ['id' => $id], '', 'row', '', 'code')->code;
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
        $result = get_table_where('tblinternal_proposal', ['id' => $id], '', 'row', '', 'approved_by');
        $approvedBy = !empty($result) ? (isset($result->approved_by) ? $result->approved_by : '0') : '0';
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

    public function getObjectList ($objectType = '')
    {
        if ($objectType == 'object_clients' || true) {
			$this->db->select(
				'
            tblclients.userid as id,
            CONCAT(COALESCE(tblclients.company, ""), "<br/><b>TK Ngân hàng: </b>", COALESCE(bank_account, "")) as name,
            CONCAT(tblclients.prefix_client,tblclients.code_client) as code_client',
				false
			);
			// if (!empty($search)) {
			// 	$this->db->group_start();
			// 	$this->db->like('tblclients.company', $search);
			// 	$this->db->or_like('CONCAT(tblclients.prefix_client, tblclients.code_client)', $search);
			// 	$this->db->group_end();
			// }
			// if (!empty($id)) {
			// 	$this->db->where('tblclients.userid', $id);
			// }
			$this->db->order_by('tblclients.company', 'DESC');
			$client = $this->db->get('tblclients')->result_array();
			$data['results'] = $client;
		}
		elseif ($objectType == 'object_suppliers') {
			$this->db->select(
				'
            tblsuppliers.id as id,
            CONCAT(COALESCE(tblsuppliers.company), "<br/><b>TK Ngân hàng: </b>", COALESCE(bank_account, "")) as name,
            CONCAT(tblsuppliers.prefix,tblsuppliers.code) as code_client',
				false
			);
			// if (!empty($search)) {
			// 	$this->db->group_start();
			// 	$this->db->like('tblsuppliers.company', $search);
			// 	$this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $search);
			// 	$this->db->group_end();
			// }
			// if (!empty($id)) {
			// 	$this->db->where('tblsuppliers.id', $id);
			// }
			$this->db->order_by('tblsuppliers.company', 'DESC');
			$suppliers = $this->db->get('tblsuppliers')->result_array();
			$data['results'] = $suppliers;
		}
		elseif ($objectType == 'object_staff') {
			$this->db->select(
				'tblstaff.staffid as id,
				tblstaff.code as code_client,
				CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as name',
				false
			);
			// if (!empty($search)) {
			// 	$this->db->group_start();
			// 	$this->db->like('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""))', $search);
			// 	$this->db->group_end();
			// }
			// if (!empty($id)) {
			// 	$this->db->where('tblstaff.staffid', $id);
			// }
			$suppliers = $this->db->get('tblstaff')->result_array();
			$data['results'] = $suppliers;
		}
        return $data['results'];
    }
}