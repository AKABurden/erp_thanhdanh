<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Debt_suppliers extends AdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if (!has_permission('debt_suppliers', '', 'view')) {
			access_denied('Debt suppliers');
		}
		// $this->db->select('tblsuppliers.*');
		// $this->db->join('tblsuppliers','tblsuppliers.id = tblimport.suppliers_id');
		// $this->db->group_by('tblsuppliers.id');
		$data['suppliers'] = $this->db->get('tblsuppliers')->result_array();
		$data['title'] = _l('ch_debt_suppliers');
		$this->load->view('admin/debt_suppliers/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('debt_suppliers');
	}

	public function suppliert_detail($id = '')
	{
		$data['id'] = $id;
		$data['filterType'] = $this->input->get('filterType');
		$suppliers = get_table_where('tblsuppliers', array('id' => $id), '', 'row');
		$data['title'] = _l('debt_suppliers') . ' ' . $suppliers->company;
		if ($data['filterType'] == 2) {
			$data['title'] .= ' (Vượt mức thời gian thanh toán)';
		}
		$data['suppliers'] = $suppliers;


		if ($suppliers->type != 0) {
			$data['code'] = get_option('prefix_other_payslips') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblother_payslips') + 1);
			$data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
			$data['costs'] = array();
			$this->load->model('costs_model');
			$this->costs_model->get_by_id(0, $data['costs']);
			$this->load->view('admin/debt_suppliers/suppliert_detail_v2', $data);
		} else {
			$data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
			$data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
			$data['costs'] = array();
			$this->load->model('costs_model');
			$this->costs_model->get_by_id(0, $data['costs']);
			$this->load->view('admin/debt_suppliers/suppliert_detail', $data);
		}
	}

	public function table_debt_suppliers($id = '')
	{
		$ktr = get_table_where('tblsuppliers', array('id' => $id), '', 'row');
		if ($ktr->type != 0) {
			$this->app->get_table_data('detail_suppliers_v2', array('id' => $id));
		} else {
			$this->app->get_table_data('detail_suppliers', array('id' => $id));
		}
	}

	public function count_debt()
	{
		$id = array();
		$suppliers_id = $this->input->post('suppliers_id');
		if (!empty($suppliers_id)) {
			$id = explode(',', $suppliers_id);
		}
		$this->db->select('SUM(tblimport.total) as total_import,(COALESCE(SUM(tblpurchase_order.amount_paid),0)+ COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.amount_paid),0))  as amount_paid_import,SUM(tblpurchase_order.price_other_expenses) as price_other_expenses_import');
		$this->db->where('((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
		$this->db->where('tblpurchase_order.id IN(select id_order from tblimport)');
		if (!empty($id)) {
			$this->db->where_in('tblsuppliers.id', $id);
		}
		$this->db->having('(total_import - amount_paid_import) > 0');
		$this->db->join('tblpurchase_order', 'tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
		$this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id', 'left');
		$this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'left');
		$this->db->group_by('tblsuppliers.id');
		$count = $this->db->get('tblsuppliers')->result_array();

		$this->db->select('SUM(tblimport.total) as total_import,(COALESCE(SUM(tblpurchase_order.amount_paid),0)+ COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.amount_paid),0))  as amount_paid_import,SUM(tblpurchase_order.price_other_expenses) as price_other_expenses_import');
		if (!empty($id)) {
			$this->db->where_in('tblsuppliers.id', $id);
		}
		$this->db->where('((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
		$this->db->where('tblpurchase_order.id IN(select id_order from tblimport)');
		$this->db->where('tblsuppliers.debt_limit > 0 AND tblsuppliers.debt_limit < ((select(SUM(tblimport.total)) from tblimport where tblimport.suppliers_id=tblsuppliers.id ) -(select((COALESCE(SUM(tblpurchase_order.amount_paid),0)+ COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.amount_paid),0))) from tblimport left JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblimport.red_invoice where ((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0)) AND tblimport.suppliers_id=tblsuppliers.id))');
		$this->db->having('(total_import - amount_paid_import) > 0');
		$this->db->join('tblpurchase_order', 'tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
		$this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id', 'left');
		$this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'left');
		$this->db->group_by('tblsuppliers.id');
		$count_limit = $this->db->get('tblsuppliers')->result_array();
		$data['count_limit'] = count($count_limit);




		if (!empty($id)) {
			$this->db->where_in('tblsuppliers.id', $id);
		}
		$this->db->join('tblpurchase_order', 'tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
		$this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'left');
		$this->db->group_by('tblsuppliers.id');
		$this->db->where('tblpurchase_order.id IN (
								SELECT tblpurchase_order.id 
								FROM tblpurchase_order 
								WHERE tblpurchase_order.amount_paid < tblpurchase_order.totalAll_suppliers
								AND tblpurchase_order.suppliers_id = tblsuppliers.id
								AND DATEDIFF(CURDATE(), tblpurchase_order.date) > tblsuppliers.time_payment
								AND time_payment <> 0
							)', false, false);
		$count_limit_day = $this->db->get('tblsuppliers')->result_array();
		$data['all_debt_limit_day'] = count($count_limit_day);


		$this->db->select('SUM(tbl_orders.cost_delivery) as total_import,SUM(tbl_orders.price_other_expenses_delivery) as price_other_expenses_import');
		if (!empty($id)) {
			$this->db->where_in('tblsuppliers.id', $id);
		}
		$this->db->having('(total_import - price_other_expenses_import) > 0');
		$this->db->join('tbl_orders', 'tbl_orders.transporter_id = tblsuppliers.id', 'left');
		$this->db->group_by('tblsuppliers.id');
		$count_order = $this->db->get('tblsuppliers')->result_array();
		$data['all'] = count($count) + count($count_order);
		echo json_encode($data);
	}

	public function get_total_debt()
	{
		$this->db->select('SUM(tblpurchase_order.totalAll_suppliers) as total_import,(COALESCE(SUM(tblpurchase_order.amount_paid),0)+ COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.amount_paid),0))  as amount_paid_import,SUM(tblpurchase_order.price_other_expenses) as price_other_expenses_import');
		$this->db->where('((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
		$this->db->where('tblpurchase_order.id IN(select id_order from tblimport)');
		$this->db->having('(total_import - amount_paid_import) > 0');
		$this->db->join('tblpurchase_order', 'tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
		$this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'left');
		$this->db->group_by('tblsuppliers.id');
		$count = $this->db->get('tblsuppliers')->row();
		var_dump($count);
		die;
		$data['debt'] = number_format($count->total_import);
		$data['payment'] = number_format($count->amount_paid_import + $count->price_other_expenses_import);
		$data['left'] = number_format($count->total_import - ($count->amount_paid_import + $count->price_other_expenses_import));
		echo json_encode($data);
		die;
	}

	public function count_alls($id = '')
	{
		$PO = array();
		$invoice = array();
		$outsource = array();
		$PO = get_table_where_select('count(*) as PO', 'tblimport', array('total >' => 0, 'status_pay !=' => 2, 'red_invoice' => 0, 'status >' => 1, 'suppliers_id' => $id), '', 'row');
		$invoice = get_table_where_select('count(*) as invoice', 'tblpurchase_invoice', array('total_price_befor_vat >' => 0, 'status !=' => 2, 'id_supplier' => $id), '', 'row');
		$outsource = get_table_where_select('count(*) as outsource', 'tbl_outsource', array('grand_total >' => 0, 'status' => "approved", 'supplier_id' => $id, 'status_pay !=' => 2), '', 'row');
		$data['all'] = $PO->PO + $invoice->invoice + $outsource->outsource;
		$data['PO'] = $PO->PO + $invoice->invoice;
		$data['outsource'] = $outsource->outsource;
		echo json_encode($data);
		// $data['ch_confirm_22'] = $ch_confirm_22->ch_confirm_22;
	}
}
