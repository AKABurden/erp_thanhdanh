<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Quotes_model_temp extends App_Model
{
    public function __construct()
    {
        parent::__construct();
		$this->is_branch = true;
    }

    public function searchPreReferenceNoQuotes($q, $limit = 50)
    {
        $customer = "(
            SELECT IF(tblclients.fullname IS NOT null, tblclients.fullname, CONCAT(tblclients.prefix_client, '', tblclients.code_client))
            FROM tblclients
            WHERE tblclients.userid = tbl_quotes.customer_id
        )";

        $lead = "(
            SELECT tblleads.name
            FROM tblleads
            WHERE tblleads.id = tbl_quotes.customer_id
        )";

        $this->db->select("
            tbl_quotes.id as id,
            tbl_quotes.reference_no as text,
            tbl_quotes.date as date,
            IF (tbl_quotes.type_customer = 'customers', $customer, $lead) as customer_name
            ", false);
        $this->db->from('tbl_quotes');
        // $this->db->join('tblclients', 'tblclients.userid = tbl_quotes.customer_id', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_quotes.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_quotes.date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowQuotesById($id)
    {
    	$this->db->select('*');
    	$this->db->from('tbl_quotes');
    	$this->db->where('tbl_quotes.id', $id);
    	return $this->db->get()->row_array();
    }

    public function countQuotesByParentId($id)
    {
    	$this->db->from('tbl_quotes');
    	$this->db->where('tbl_quotes.parent_id', $id);
    	return $this->db->get()->num_rows();
    }

    public function checkExistQuotesReferenceNo($reference_no)
    {
        $this->db->from('tbl_quotes');
        $this->db->where('tbl_quotes.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function insertQuotes($data)
    {
        $this->db->insert('tbl_quotes', $data);
        return $this->db->insert_id();
    }

    public function insertQuoteItems($data)
    {
        $this->db->insert('tbl_quote_items', $data);
        return $this->db->insert_id();
    }

    public function insertQuoteCharges($data)
    {
        $this->db->insert('tbl_quote_charges', $data);
        return $this->db->insert_id();
    }

    public function insertBatchQuoteCharges($data)
    {
        return $this->db->insert_batch('tbl_quote_charges', $data);
    }

    public function insertBatchQuotePayments($data)
    {
        return $this->db->insert_batch('tbl_quote_payments', $data);
    }

    public function deleteQuotesById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_quotes');
    }

    public function deleteQuoteItemsByQuoteId($quote_id)
    {
        $this->db->where('quote_id', $quote_id);
        return $this->db->delete('tbl_quote_items');
    }

    public function deleteQuotePaymentsQuoteId($quote_id)
    {
        $this->db->where('quote_id', $quote_id);
        return $this->db->delete('tbl_quote_payments');
    }

    public function deleteQuoteChargesByQuoteId($quote_id)
    {
        $this->db->where('quote_id', $quote_id);
        return $this->db->delete('tbl_quote_charges');
    }

    public function getQuotePayments($quote_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quote_payments');
        $this->db->where('quote_id', $quote_id);
        return $this->db->get()->result_array();
    }

    public function getQuoteItemsByQuoteId($quote_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quote_items');
        $this->db->where('quote_id', $quote_id);
        return $this->db->get()->result_array();
    }

    public function updateQuotes($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_quotes', $data);
    }

    public function getQuoteChargesByQuoteId($quote_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quote_charges');
        $this->db->where('quote_id', $quote_id);
        return $this->db->get()->result_array();
    }

    public function rowQuoteItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quote_items');
        $this->db->where('tbl_quote_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertQuotesNoteDefault($data)
    {
        $this->db->insert('tbl_quotes_note_default', $data);
        return $this->db->insert_id();
    }

    public function updateQuotesNoteDefault($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_quotes_note_default', $data);
    }

    public function rowQuotesNoteDefault($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quotes_note_default');
        $this->db->where('tbl_quotes_note_default.id', $id);
        return $this->db->get()->row_array();
    }

    public function deleteQuotesNoteDefault($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_quotes_note_default');
    }

    public function getQuotesNoteDefaultText($arrId = [])
    {
        $this->db->select('GROUP_CONCAT(tbl_quotes_note_default.note SEPARATOR ",") as note_default');
        $this->db->from('tbl_quotes_note_default');
        $this->db->where_in('tbl_quotes_note_default.id', $arrId);
        // print_arrays($this->db->get_compiled_select(), FALSE);

        return $this->db->get()->row_array();
    }

    public function checkQuotesNoteDefault($id)
    {
        $this->db->from('tbl_quotes');
        $this->db->where("FIND_IN_SET($id, tbl_quotes.note_default_id) > 0");
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function countQuotesStatus($status)
    {
        $perViewQuotes = has_permission('quotes', '', 'view');
        $perViewOwnQuotes = has_permission('quotes', '', 'view_own');
        if (empty($perViewQuotes) && empty($perViewOwnQuotes)) return 0;

		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_quotes.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tbl_quotes.id = 0', false, false);
				}
			}
		}


        $this->db->from('tbl_quotes');
        if (!empty($status) && $status != 'all')
        {
            if ($status == 'un_approved') {
                $this->db->where('tbl_quotes.status', 'un_approved');
            } else if ($status == 'approved') {
                $this->db->where('tbl_quotes.status', 'approved');
            } else if ($status == 'un_created_an_order') {
                $this->db->where('tbl_quotes.order_id', 0);
            } else if ($status == 'created_an_order') {
                $this->db->where('tbl_quotes.order_id >', 0);
            }
        }
        if (!$perViewQuotes) {
            $this->db->where('tbl_quotes.created_by', get_staff_user_id());
        }
        return $this->db->get()->num_rows();
    }
}