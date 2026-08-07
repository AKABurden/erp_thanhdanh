<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Function used to get related data based on rel_id and rel_type
 * Eq in the tasks section there is field where this task is related eq invoice with number INV-0005
 * @param string $type
 * @param string $rel_id
 * @return mixed
 */
function get_relation_data($type, $rel_id = '', $type_items = '')
{
	$CI = &get_instance();
	$q = '';
	if ($CI->input->post('q')) {
		$q = $CI->input->post('q');
		$q = trim($q);
	}
	$data = [];
	if ($type == 'customer' || $type == 'customers') {
		$where_clients = '';
		if ($q) {
			$where_clients .= '(company LIKE "%' . $q . '%" OR company_short LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%") AND ' . db_prefix() . 'clients.active = 1';
		}
		$data = $CI->clients_model->get($rel_id, $where_clients);
	} elseif ($type == 'contact' || $type == 'contacts') {
		if ($rel_id != '') {
			$data = $CI->clients_model->get_contact($rel_id);
		} else {
			$where_contacts = db_prefix() . 'contacts.active=1';
			if ($CI->input->post('tickets_contacts')) {
				if (!has_permission('customers', '', 'view') && get_option('staff_members_open_tickets_to_all_contacts') == 0) {
					$where_contacts .= ' AND ' . db_prefix() . 'contacts.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
				}
			}
			if ($CI->input->post('contact_userid')) {
				$where_contacts .= ' AND ' . db_prefix() . 'contacts.userid=' . $CI->input->post('contact_userid');
			}
			$search = $CI->misc_model->_search_contacts($q, 0, $where_contacts);
			$data = $search['result'];
		}
	} elseif ($type == 'invoice') {
		if ($rel_id != '') {
			$CI->load->model('invoices_model');
			$data = $CI->invoices_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_invoices($q);
			$data = $search['result'];
		}
	} elseif ($type == 'orders') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_orders')->row();
		} else {
			$q = trim($q);
			// $CI->db->select('*, reference_no as name');
			$CI->db->like('reference_no', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultOrders = $CI->db->get('tbl_orders')->result_array();
//		    $result = [
//			    'result'         => $resultOrders,
//			    'type'           => 'orders',
//			    'search_heading' => _l('items'),
//		    ];
			$data = $resultOrders;
		}
	}
	elseif ($type == 'items') {
		if ($rel_id != '') {
			$CI->load->model('invoice_items_model');
			$data = $CI->invoice_items_model->get_full_item($rel_id, $type_items);
		} else {
			$search = $CI->misc_model->_search_items($q, $type_items);
			$data = $search['result'];
		}
	} elseif ($type == 'purchases') {
		if ($rel_id != '') {
			$CI->load->model('purchases_model');
			$data = $CI->purchases_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_purchases($q, $type_items);
			$data = $search['result'];
		}
	} elseif ($type == 'rfq') {
		$search = $CI->misc_model->_search_rfq($q, $type_items);
		$data = $search['result'];
	} elseif ($type == 'supplier_quotes') {
		$search = $CI->misc_model->_search_supplier_quotes($q, $type_items);
		$data = $search['result'];
	} elseif ($type == 'purchase_order') {
		$search = $CI->misc_model->_search_purchase_order($q, $type_items);
		$data = $search['result'];
	}
//	elseif ($type == 'import') {
//		$search = $CI->misc_model->_search_import($q, $type_items);
//		$data = $search['result'];
//	}
	elseif ($type == 'return_supplier') {
		$search = $CI->misc_model->_return_supplier($q, $type_items);
		$data = $search['result'];
	} elseif ($type == 'color') {
		if ($rel_id != '') {
			$CI->load->model('invoice_items_model');
			$data = $CI->invoice_items_model->color($rel_id);
		} else {
			$search = $CI->misc_model->_search_color($q);
			$data = $search['result'];
		}
	} elseif ($type == 'credit_note') {
		if ($rel_id != '') {
			$CI->load->model('credit_notes_model');
			$data = $CI->credit_notes_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_credit_notes($q);
			$data = $search['result'];
		}
	} elseif ($type == 'estimate') {
		if ($rel_id != '') {
			$CI->load->model('estimates_model');
			$data = $CI->estimates_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_estimates($q);
			$data = $search['result'];
		}
	} elseif ($type == 'contract' || $type == 'contracts') {
		$CI->load->model('contracts_model');
		if ($rel_id != '') {
			$CI->load->model('contracts_model');
			$data = $CI->contracts_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_contracts($q);
			$data = $search['result'];
		}
	} elseif ($type == 'ticket') {
		if ($rel_id != '') {
			$CI->load->model('tickets_model');
			$data = $CI->tickets_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_tickets($q);
			$data = $search['result'];
		}
	} elseif ($type == 'expense' || $type == 'expenses') {
		if ($rel_id != '') {
			$CI->load->model('expenses_model');
			$data = $CI->expenses_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_expenses($q);
			$data = $search['result'];
		}
	} elseif ($type == 'lead' || $type == 'leads') {
		if ($rel_id != '') {
			$CI->load->model('leads_model');
			$data = $CI->leads_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_leads($q, 0, [
				'junk' => 0,
			]);
			$data = $search['result'];
		}
	} elseif ($type == 'proposal') {
		if ($rel_id != '') {
			$CI->load->model('proposals_model');
			$data = $CI->proposals_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_proposals($q);
			$data = $search['result'];
		}
	} elseif ($type == 'project') {
		if ($rel_id != '') {
			$CI->load->model('projects_model');
			$data = $CI->projects_model->get($rel_id);
		} else {
			$where_projects = '';
			if ($CI->input->post('customer_id')) {
				$where_projects .= 'clientid=' . $CI->input->post('customer_id');
			}
			$search = $CI->misc_model->_search_projects($q, 0, $where_projects);
			$data = $search['result'];
		}
	} elseif ($type == 'staff') {
		if ($rel_id != '') {
			$CI->load->model('staff_model');
			$data = $CI->staff_model->get($rel_id);
		} else {
			$search = $CI->misc_model->_search_staff($q);
			$data = $search['result'];
		}
	} elseif ($type == 'tasks' || $type == 'task') {
		// Tasks only have relation with custom fields when searching on top
		if ($rel_id != '') {
			$data = $CI->tasks_model->get($rel_id);
		}
	} else if ($type == 'order_production_details') {
		//task tnh
		if ($rel_id != '') {
			$data = $CI->site_model->getProductionsOD($rel_id);
		} else {
			if (!empty($_POST['limit_search'])) {
				$data = $CI->site_model->searchProductionsOD($q, $_POST['limit_search']);
			} else {
				$data = $CI->site_model->searchProductionsOD($q);
			}
		}
	} elseif ($type == 'supplier') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tblsuppliers')->row();
		} else {
			$q = trim($q);
			$CI->db->like('company', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultSuppliers = $CI->db->get('tblsuppliers')->result_array();
			$data = $resultSuppliers;
		}
	} elseif ($type == 'import') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tblimport')->row();
		} else {
			$q = trim($q);
			$CI->db->like('CONCAT(prefix, "-", code)', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tblimport')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'production_report') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tblproduction_report')->row();
		} else {
			$q = trim($q);
			$CI->db->like('name_report', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tblproduction_report')->result_array();
			$data = $resultImport;
		}
	}

	elseif ($type == 'maintenance_ticket') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tblmaintenance_ticket')->row();
		} else {
			$q = trim($q);
			$CI->db->like('name', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tblmaintenance_ticket')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'quotes') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_quotes')->row();
		} else {
			$q = trim($q);
			$CI->db->like('reference_no', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tbl_quotes')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'products') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_products')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('code', $q);
			$CI->db->or_like('name', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}

			$CI->db->where('type_products', 'products');
			$resultImport = $CI->db->get('tbl_products')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'materials') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_materials')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('code', $q);
			$CI->db->or_like('name', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tbl_materials')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'releases') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_deliveries')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('reference_no', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tbl_deliveries')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'internal_proposal') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tblinternal_proposal')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('tblinternal_proposal.code', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$CI->db->select('tblinternal_proposal.*, tblcategory_tasks.code as code_category_tasks');
			$CI->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
			$resultImport = $CI->db->get('tblinternal_proposal')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'plan_propose') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tblplan_propose')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('tblplan_propose.code', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$CI->db->select('tblplan_propose.*, tblcategory_tasks.code as code_category_tasks');
			$CI->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblplan_propose.category_tasks', 'left');
			$resultImport = $CI->db->get('tblplan_propose')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'productions_orders') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_productions_orders')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('reference_no', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tbl_productions_orders')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'productions_orders_detail') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_productions_orders_details')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('reference_no', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tbl_productions_orders_details')->result_array();
			$data = $resultImport;
		}
	}
	elseif ($type == 'suggest_check') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_suggest_check')->row();
		} else {
			$q = trim($q);
			$CI->db->group_start();
			$CI->db->like('reference_no', $q);
			$CI->db->group_end();
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$resultImport = $CI->db->get('tbl_suggest_check')->result_array();
			$data = $resultImport;
		}
	} elseif ($type == 'work_plan_task') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_work_plan_task')->row();
		} else {
			$q = trim($q);
			$CI->db->like('content', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$rs = $CI->db->get('tbl_work_plan_task')->result_array();
			$data = $rs;
		}
	}
    elseif ($type == 'PTM') {
		if ($rel_id != '') {
			$CI->db->where('id', $rel_id);
			$data = $CI->db->get('tbl_orders_ptm')->row();
		} else {
			$q = trim($q);
			$CI->db->like('ptm_no', $q);
			if (!empty($_POST['limit_search'])) {
				$CI->db->limit($_POST['limit_search']);
			}
			$rs = $CI->db->get('tbl_orders_ptm')->result_array();
			$data = $rs;
		}
	}
	return $data;
}

/**
 * Ger relation values eq invoice number or project name etc based on passed relation parsed results
 * from function get_relation_data
 * $relation can be object or array
 * @param mixed $relation
 * @param string $type
 * @return mixed
 */
function get_relation_data_hau($type, $rel_id = '', $type_items = '', $id_order = '')
{
	$CI = &get_instance();
	$q = '';
	if ($CI->input->post('q')) {
		$q = $CI->input->post('q');
		$q = trim($q);
	}
	$data = [];
	if ($type == 'items') {
		if ($rel_id != '') {
			$CI->load->model('invoice_items_model');
			$data = $CI->invoice_items_model->get_full_item($rel_id, $type_items, $id_order);
		} else {
			$search = $CI->misc_model->_search_items($q, $type_items, 5, $id_order);
			$data = $search['result'];
		}
	}
	return $data;
}

/**
 * Ger relation values eq invoice number or project name etc based on passed relation parsed results
 * from function get_relation_data
 * $relation can be object or array
 * @param mixed $relation
 * @param string $type
 * @return mixed
 */
function get_relation_data_order_quote($type, $rel_id = '', $type_items = '', $id_quotes = '')
{
	$CI = &get_instance();
	$q = '';
	if ($CI->input->post('q')) {
		$q = $CI->input->post('q');
		$q = trim($q);
	}
	$data = [];
	if ($type == 'items') {
		if ($rel_id != '') {
			$CI->load->model('invoice_items_model');
			$data = $CI->invoice_items_model->get_full_item($rel_id, $type_items, $id_quotes);
		} else {
			$search = $CI->misc_model->_search_items_order_quote($q, $type_items, 5, $id_quotes);
			$data = $search['result'];
		}
	}
	return $data;
}

/**
 * Ger relation values eq invoice number or project name etc based on passed relation parsed results
 * from function get_relation_data
 * $relation can be object or array
 * @param mixed $relation
 * @param string $type
 * @return mixed
 */
function get_relation_data_order_purchases($type, $rel_id = '', $type_items = '', $id_purchases = '')
{
	$CI = &get_instance();
	$q = '';
	if ($CI->input->post('q')) {
		$q = $CI->input->post('q');
		$q = trim($q);
	}
	$data = [];
	if ($type == 'items') {
		if ($rel_id != '') {
			$CI->load->model('invoice_items_model');
			$data = $CI->invoice_items_model->get_full_item($rel_id, $type_items, $id_purchases);
		} else {
			$search = $CI->misc_model->_search_items_order_id_purchases($q, $type_items, 5, $id_purchases);
			$data = $search['result'];
		}
	}
	return $data;
}

/**
 * Ger relation values eq invoice number or project name etc based on passed relation parsed results
 * from function get_relation_data
 * $relation can be object or array
 * @param mixed $relation
 * @param string $type
 * @return mixed
 */
function get_relation_values($relation, $type)
{
	if ($relation == '') {
		return [
			'name' => '',
			'code' => '',
			'id' => '',
			'link' => '',
			'full_link' => '',
			'mode' => '',
			'addedfrom' => 0,
			'subtext' => '',
			'quantity_rest' => 0,
		];
	}
	$addedfrom = 0;
	$name = '';
	$code = '';
	$id = '';
	$mode = '';
	$link = '';
	$full_link = '';
	$subtext = '';
	$type_items = '';
	$quantity_warehoue = '';
	if ($type == 'customer' || $type == 'customers') {
		if (is_array($relation)) {
			$id = $relation['userid'];
			$name = $relation['company_short'];
		} else {
			$id = $relation->userid;
			$name = $relation->company_short;
		}
		$link = admin_url('clients/client/' . $id);
	} elseif ($type == 'contact' || $type == 'contacts') {
		if (is_array($relation)) {
			$userid = isset($relation['userid']) ? $relation['userid'] : $relation['relid'];
			$id = $relation['id'];
			$name = $relation['firstname'] . ' ' . $relation['lastname'];
		} else {
			$userid = $relation->userid;
			$id = $relation->id;
			$name = $relation->firstname . ' ' . $relation->lastname;
		}
		$subtext = get_company_name($userid);
		$link = admin_url('clients/client/' . $userid . '?contactid=' . $id);
	} elseif ($type == 'invoice') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$addedfrom = $relation['addedfrom'];
		} else {
			$id = $relation->id;
			$addedfrom = $relation->addedfrom;
		}
		$name = format_invoice_number($id);
		$link = admin_url('invoices/list_invoices/' . $id);
	} elseif ($type == 'orders') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$addedfrom = $relation['created_by'];
			$name = $relation['reference_no'];
			$subtext = $relation['customer_name'];
		} else {
			$id = $relation->id;
			$addedfrom = $relation->created_by;
			$name = $relation->reference_no;
			$subtext = $relation->customer_name;
		}
		$link = admin_url('orders/view_order/' . $id);
	} elseif ($type == 'credit_note') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$addedfrom = $relation['addedfrom'];
		} else {
			$id = $relation->id;
			$addedfrom = $relation->addedfrom;
		}
		$name = format_credit_note_number($id);
		$link = admin_url('credit_notes/list_credit_notes/' . $id);
	} elseif ($type == 'estimate') {
		if (is_array($relation)) {
			$id = $relation['estimateid'];
			$addedfrom = $relation['addedfrom'];
		} else {
			$id = $relation->id;
			$addedfrom = $relation->addedfrom;
		}
		$name = format_estimate_number($id);
		$link = admin_url('estimates/list_estimates/' . $id);
	} elseif ($type == 'contract' || $type == 'contracts') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['subject'];
			$addedfrom = $relation['addedfrom'];
		} else {
			$id = $relation->id;
			$name = $relation->subject;
			$addedfrom = $relation->addedfrom;
		}
		$link = admin_url('contracts/contract/' . $id);
	} elseif ($type == 'items') {
		if (is_array($relation)) {
			if (!empty($relation['code'])) {
				$name = '[' . $relation['code'] . '] ' . $relation['name'];
			} else {
				$name = $relation['name'];
			}
			$id = $relation['id'];
			$type_items = $relation['type_items'];
			if (!empty($relation['quantity_warehoue'])) {
				$quantity_warehoue = $relation['quantity_warehoue'];
			}
			if (isset($relation['mode'])) {
				$mode = $relation['mode'];
			}
			$addedfrom = '';
		} else {
			if (!empty($relation->code)) {
				$name = '[' . $relation->code . '] ' . $relation->name;
			} else {
				$name = $relation->name;
			}
			if (!empty($relation->quantity_warehoue)) {
				$quantity_warehoue = $relation->quantity_warehoue;
			}
			if (isset($relation->mode)) {
				$mode = $relation->mode;
			}
			$id = $relation->id;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'purchases') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . $relation['code'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->prefix . $relation->code;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'rfq') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . '-' . $relation['code'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->prefix . '-' . $relation->code;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'supplier_quotes') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . '-' . $relation['code'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->prefix . '-' . $relation->code;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'purchase_order') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . '-' . $relation['code'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->prefix . '-' . $relation->code;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'return_supplier') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . '-' . $relation['code'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->prefix . '-' . $relation->code;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'import') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . '-' . $relation['code'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->prefix . '-' . $relation->code;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'color') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
			$addedfrom = '';
		} else {
			$id = $relation->id;
			$name = $relation->name;
			$addedfrom = '';
		}
		$link = '';
	} elseif ($type == 'ticket') {
		if (is_array($relation)) {
			$id = $relation['ticketid'];
			$name = '#' . $relation['ticketid'];
			$name .= ' - ' . $relation['subject'];
		} else {
			$id = $relation->ticketid;
			$name = '#' . $relation->ticketid;
			$name .= ' - ' . $relation->subject;
		}
		$link = admin_url('tickets/ticket/' . $id);
	} elseif ($type == 'expense' || $type == 'expenses') {
		if (is_array($relation)) {
			$id = $relation['expenseid'];
			$name = $relation['category_name'];
			$addedfrom = $relation['addedfrom'];
			if (!empty($relation['expense_name'])) {
				$name .= ' (' . $relation['expense_name'] . ')';
			}
		} else {
			$id = $relation->expenseid;
			$name = $relation->category_name;
			$addedfrom = $relation->addedfrom;
			if (!empty($relation->expense_name)) {
				$name .= ' (' . $relation->expense_name . ')';
			}
		}
		$link = admin_url('expenses/list_expenses/' . $id);
	} elseif ($type == 'lead' || $type == 'leads') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
			if ($relation['email'] != '') {
				$name .= ' - ' . $relation['email'];
			}
		} else {
			$id = $relation->id;
			$name = $relation->name;
			if ($relation->email != '') {
				$name .= ' - ' . $relation->email;
			}
		}
		$link = admin_url('leads/index/' . $id);
	} elseif ($type == 'proposal') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$addedfrom = $relation['addedfrom'];
			if (!empty($relation['subject'])) {
				$name .= ' - ' . $relation['subject'];
			}
		} else {
			$id = $relation->id;
			$addedfrom = $relation->addedfrom;
			if (!empty($relation->subject)) {
				$name .= ' - ' . $relation->subject;
			}
		}
		$name = format_proposal_number($id);
		$link = admin_url('proposals/list_proposals/' . $id);
	} elseif ($type == 'tasks' || $type == 'task') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
		} else {
			$id = $relation->id;
			$name = $relation->name;
		}
		$link = admin_url('tasks/view/' . $id);
	} elseif ($type == 'staff') {
		if (is_array($relation)) {
			$id = $relation['staffid'];
			$name = $relation['firstname'] . ' ' . $relation['lastname'];
		} else {
			$id = $relation->staffid;
			$name = $relation->firstname . ' ' . $relation->lastname;
		}
		$link = admin_url('profile/' . $id);
	} elseif ($type == 'project') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
			$clientId = $relation['clientid'];
		} else {
			$id = $relation->id;
			$name = $relation->name;
			$clientId = $relation->clientid;
		}
		$name = '#' . $id . ' - ' . $name . ' - ' . get_company_name($clientId);
		$link = admin_url('projects/view/' . $id);
	} elseif ($type == 'order_production_details') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
			$quantity_rest = $relation['quantity_rest'];
		} else {
			$id = $relation->id;
			$name = $relation->name;
			$quantity_rest = $relation->quantity_rest;
		}
		$link = admin_url('manufactures/detail_productions/' . $id);
	} elseif ($type == 'supplier') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['company'];
		} else {
			$id = $relation->id;
			$name = $relation->company;
		}
	}
	elseif ($type == 'import') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['prefix'] . '-' . $relation['code'];
		} else {
			$id = $relation->id;
			$name = $relation->prefix . '-' . $relation->code;
		}
	}
	elseif ($type == 'production_report') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name_report'];
			$code = $relation['reference_no'] ?? '';
		} else {
			$id = $relation->id;
			$name = $relation->name_report;
			$code = $relation->reference_no ?? '';
			$full_link = '<a class="c_modal" href="'.admin_url('production_report/modal/'.$relation->id).'">'.$relation->name_report.'</a>';
		}
	}
	elseif ($type == 'maintenance_ticket') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
		} else {
			$id = $relation->id;
			$name = $relation->name;
		}

	}
	elseif ($type == 'quotes') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['reference_no'];
		} else {
			$id = $relation->id;
			$name = $relation->reference_no;
			$full_link = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('quotes/view_quotes/'.$relation->id).'" data-toggle="modal" data-target="#myModal">'.$relation->reference_no.'</a>';
		}
	}
	elseif ($type == 'products') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
			$relation['subtext'] = $relation['code'];
		}
		else {
			$id = $relation->id;
			$name = $relation->name;
			$subtext = $relation->code;
			$full_link = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('products/view_product/'.$relation->id).'" data-toggle="modal" data-target="#myModal">'.$relation->name.'</a>';
		}
	}
	elseif ($type == 'materials') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['name'];
			$relation['subtext'] = $relation['code'];
		}
		else {
			$id = $relation->id;
			$name = $relation->name;
			$subtext = $relation->code;
			$full_link = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('items/view_item/'.$relation->id).'" data-toggle="modal" data-target="#myModal">'.$relation->name.'</a>';
		}
	}
	elseif ($type == 'releases') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['reference_no'];
			$relation['subtext'] = $relation['customer_name'];
		}
		else {
			$id = $relation->id;
			$name = $relation->reference_no;
			$subtext = $relation->customer_name;
			$full_link = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('releases/view_delivery/'.$relation->id).'" data-toggle="modal" data-target="#myModal">'.$relation->reference_no.'</a>';
		}
	}
	elseif ($type == 'internal_proposal') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['code'];
			$subtext = !empty($relation['code_category_tasks']) ? $relation['code_category_tasks'] : '';
		}
		else {
			$id = $relation->id;
			$name = $relation->code;
			$subtext = !empty($relation->code_category_tasks) ? $relation->code_category_tasks : '';
			$full_link = '<a class="c_modal" href="'.admin_url('internal_proposal/view/'.$relation->id).'">'.$relation->code.'</a>';
		}
	}
	elseif ($type == 'productions_orders') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['reference_no'];
		}
		else {
			$id = $relation->id;
			$name = $relation->reference_no;
			$full_link = '<a href="'.admin_url('manufactures/detail_productions_orders/'.$relation->id).'">'.$relation->reference_no.'</a>';
		}
	}
	elseif ($type == 'productions_orders_detail') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['reference_no'];
		}
		else {
			$id = $relation->id;
			$name = $relation->reference_no;
			$full_link = '<a href="'.admin_url('manufactures/detail_productions/'.$relation->id).'">'.$relation->reference_no.'</a>';
		}
	}
	elseif ($type == 'plan_propose') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['code'];
			$subtext = !empty($relation['code_category_tasks']) ? $relation['code_category_tasks'] : '';
		}
		else {
			$id = $relation->id;
			$name = $relation->code;
			$subtext = !empty($relation->code_category_tasks) ? $relation->code_category_tasks : '';
			$full_link = '<a class="c_modal" href="'.admin_url('plan_propose/view/'.$relation->id).'">'.$relation->code.'</a>';
		}
	}
	elseif ($type == 'suggest_check') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['reference_no'];
		}
		else {
			$id = $relation->id;
			$name = $relation->reference_no;
			$full_link = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('suggest_check/view/'.$relation->id).'">'.$relation->reference_no.'</a>';
		}
	}
	elseif ($type == 'work_plan_task') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['content'];
		}
		else {
			$id = $relation->id;
			$name = $relation->content;
			$full_link = '';
		}
	}
	elseif ($type == 'PTM') {
		if (is_array($relation)) {
			$id = $relation['id'];
			$name = $relation['ptm_no'];
		}
		else {
			$id = $relation->id;
			$name = $relation->ptm_no;
			$full_link = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('ptm/view_modal/'.$relation->id).'">'.$relation->ptm_no.'</a>';;
		}
	}

	if (!empty($relation) && is_array($relation) && !empty($relation['subtext'])) {
		$subtext = $relation['subtext'];
	}
	if (!empty($relation) && is_array($relation) && !empty($relation['data_content'])) {
		$content = $relation['data_content'];
	}
	return hooks()->apply_filters('relation_values', [
		'id' => $id,
		'name' => $name,
		'link' => $link,
		'full_link' => $full_link,
		'mode' => $mode,
		'addedfrom' => $addedfrom,
		'subtext' => $subtext,
		'content' => !empty($content) ? $content : '',
		'type' => $type,
		'type_items' => $type_items,
		'quantity_warehoue' => $quantity_warehoue,
		'quantity_rest' => !empty($quantity_rest) ? $quantity_rest : 0,
		'code' => $code
	]);
}

/**
 * Function used to render <option> for relation
 * This function will do all the necessary checking and return the options
 * @param mixed $data
 * @param string $type rel_type
 * @param string $rel_id rel_id
 * @return string
 */
function init_relation_options($data, $type, $rel_id = '')
{
	$_data = [];
	$has_permission_projects_view = has_permission('projects', '', 'view');
	$has_permission_customers_view = has_permission('customers', '', 'view');
	$has_permission_contracts_view = has_permission('contracts', '', 'view');
	$has_permission_invoices_view = has_permission('invoices', '', 'view');
	$has_permission_estimates_view = has_permission('estimates', '', 'view');
	$has_permission_expenses_view = has_permission('expenses', '', 'view');
	$has_permission_proposals_view = has_permission('proposals', '', 'view');
	$is_admin = is_admin();
	$CI = &get_instance();
	$CI->load->model('projects_model');
	foreach ($data as $relation) {
		$relation_values = get_relation_values($relation, $type);
		if ($type == 'project') {
			if (!$has_permission_projects_view) {
				if (!$CI->projects_model->is_member($relation_values['id']) && $rel_id != $relation_values['id']) {
					continue;
				}
			}
		} elseif ($type == 'lead') {
			if (!has_permission('leads', '', 'view')) {
				if ($relation['assigned'] != get_staff_user_id() && $relation['addedfrom'] != get_staff_user_id() && $relation['is_public'] != 1 && $rel_id != $relation_values['id']) {
					continue;
				}
			}
		} elseif ($type == 'customer') {
			if (!$has_permission_customers_view && !have_assigned_customers() && $rel_id != $relation_values['id']) {
				continue;
			} elseif (have_assigned_customers() && $rel_id != $relation_values['id'] && !$has_permission_customers_view) {
				if (!is_customer_admin($relation_values['id'])) {
					continue;
				}
			}
		} elseif ($type == 'contract') {
			if (!$has_permission_contracts_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
				continue;
			}
		} elseif ($type == 'invoice') {
			if (!$has_permission_invoices_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
				continue;
			}
		} elseif ($type == 'estimate') {
			if (!$has_permission_estimates_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
				continue;
			}
		} elseif ($type == 'expense') {
			if (!$has_permission_expenses_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
				continue;
			}
		} elseif ($type == 'proposal') {
			if (!$has_permission_proposals_view && $rel_id != $relation_values['id'] && $relation_values['addedfrom'] != get_staff_user_id()) {
				continue;
			}
		}
		$_data[] = $relation_values;
		//  echo '<option value="' . $relation_values['id'] . '"' . $selected . '>' . $relation_values['name'] . '</option>';
	}
	return $_data;
}
