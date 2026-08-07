<?php
//tblautomation_proviso
//tblautomation_detail
//tblautomations
//tblautomations_receive
defined('BASEPATH') or exit('No direct script access allowed');

class Automations extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('automations_model');
	}

	/*
	 *
	 * Công
	 *
	 */
	public function index()
	{
		if (!has_permission('automations', '', 'view') && !has_permission('automations', '', 'view_own')) {
			access_denied('automations');
		}
		$data['title'] = _l('automations');
		$this->load->view('admin/automations/manage', $data);
	}

	public function table()
	{
		if (!has_permission('automations', '', 'view')) {
			if (!has_permission('automations', '', 'create')) {
				ajax_access_denied();
			}
		}
		$this->app->get_table_data('automations');
	}

	public function detail($id = "")
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['note'] = $this->input->post('note', false);
			$data['proviso'] = $this->input->post('proviso', false);
			$run_now = $data['run_now'];
			unset($data['run_now']);
			if (!empty($id)) {
				$result_update = $this->automations_model->update($id, $data);
				if (!empty($result_update)) {
					if (!empty($run_now)) {
						$this->automations_model->RunNow($id);
					}
					set_alert('success', _l('cong_update_true'));
					redirect('admin/automations');
				} else {
					set_alert('danger', _l('cong_update_false'));
					redirect('admin/automations/detail/' . $id);
				}
			} else {
				$id_insert = $this->automations_model->add($data);
				if (!empty($id_insert)) {
					if (!empty($run_now)) {
						$this->automations_model->RunNow($id_insert);
					}
					set_alert('success', _l('cong_add_true'));
					redirect('admin/automations');
				} else {
					set_alert('danger', _l('cong_add_false'));
					redirect('admin/automations/detail');
				}
			}
		}
		if (!empty($id)) {
			$data['title'] = _l('cong_update_automations');
			$data['automations'] = $this->automations_model->get($id);
			$data['province'] = get_table_where('province');
			if ($data['automations']->action == 1) {
				$data['group_customer'] = get_table_where('tblcustomers_groups');
			}
			if ($data['automations']->action == 2) {
				$data['leads_sources'] = get_table_where('tblleads_sources');
				$data['leads_status'] = get_table_where('tblleads_status');
			}
		} else {
			$data['title'] = _l('cong_add_automations');
		}
		$this->db->select(db_prefix() . 'customers_groups.*,concat(' . db_prefix() . 'customers_groups.name," (",count(' . db_prefix() . 'customer_groups.customer_id), ")") as full_option');
		$this->db->join(db_prefix() . 'customer_groups', db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id', 'left');
		$this->db->group_by(db_prefix() . 'customer_groups.groupid');
		$data['groups_customer'] = $this->db->get(db_prefix() . 'customers_groups')->result_array();
		$this->db->select('concat(lastname," ",firstname) as fullname,' . db_prefix() . 'staff.*');
		$data['staff'] = get_table_where(db_prefix() . 'staff', array('active' => 1));
		$data['client'] = get_table_where(db_prefix() . 'clients');
		$this->load->view('admin/automations/detail', $data);
	}

	/*
	 *
	 * End Công
	 *
	 */
	public function get_infomation()
	{
		$data['unit'] = $this->input->post('unit');
		$data['active'] = true;
		$this->db->select('concat(lastname," ",firstname) as fullname,' . db_prefix() . 'staff.*');
		$data['staff'] = get_table_where(db_prefix() . 'staff', array('active' => 1));
		$this->load->view('admin/automations/modal_toggle/create_infomation', $data);
	}

	public function get_infomation_sendmail()
	{
		$data['unit'] = $this->input->post('unit');
		$data['active'] = true;
		$this->db->select('concat(lastname," ",firstname) as fullname,' . db_prefix() . 'staff.*');
		$data['staff'] = get_table_where(db_prefix() . 'staff', array('active' => 1));
		$this->load->view('admin/automations/modal_toggle/send_email_client', $data);
	}

	public function get_modal_tasks()
	{
		$data['unit'] = $this->input->post('unit');
		$data['active'] = true;
		$this->db->select('concat(lastname," ",firstname) as fullname,' . db_prefix() . 'staff.*');
		$data['staff'] = get_table_where(db_prefix() . 'staff', array('active' => 1));
		$this->load->view('admin/automations/modal_toggle/create_tasks', $data);
	}

	public function GetObject($typeObject = "")
	{
		if (!empty($typeObject)) {
			if ($typeObject == 1) {
				$this->db->select('userid as id, company as name');
				$data = $this->db->get(db_prefix() . 'clients')->result_array();
			} else {
				$this->db->select('staffid as id, concat(lastname," ",firstname) as name');
				$data = $this->db->get(db_prefix() . 'staff')->result_array();
			}
			echo json_encode($data);
			die();
		}
	}

	public function AddConditionClient()
	{
		$data['Cinit'] = $this->input->post('Cinit');
		$data['province'] = get_table_where('province');
		$data['group_customer'] = get_table_where('tblcustomers_groups');
		$this->load->view('admin/automations/html/condition_client', $data);
	}

	public function AddConditionLead()
	{
		$data['Cinit'] = $this->input->post('Cinit');
		$data['province'] = get_table_where('province');
		$data['leads_sources'] = get_table_where('tblleads_sources');
		$data['leads_status'] = get_table_where('tblleads_status');
		$this->load->view('admin/automations/html/condition_lead', $data);
	}

	public function update_status()
	{
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		if (!empty($id) && is_numeric($status)) {
			$this->db->where('id', $id);
			if ($this->db->update(db_prefix() . 'automations', ['status' => $status])) {
				echo json_encode([
					'message' => _l('cong_update_true'),
					'success' => true,
					'alert_type' => 'success',
				]);
				die();
			}
		}
		echo json_encode([
			'message' => _l('cong_update_false'),
			'success' => false,
			'alert_type' => 'danger',
		]);
		die();
	}

	public function deleteAutomation()
	{
		$id = $this->input->post('id');
		if (!empty($id)) {
			$this->db->where('id', $id);
			if ($this->db->delete(db_prefix() . 'automations')) {
				$this->db->where('id_auto', $id);
				$automation_detail = $this->db->get(db_prefix() . 'automation_detail')->result_array();
				foreach ($automation_detail as $key => $value) {
					$this->db->where('id_detail', $value['id']);
					$this->db->delete(db_prefix() . 'automations_receive');
				}
				$this->db->where('id_auto', $id);
				$this->db->delete(db_prefix() . 'automation_detail');
				$this->db->where('id_auto', $id);
				$this->db->delete(db_prefix() . 'automation_proviso');
				echo json_encode([
					'message' => _l('cong_delete_true'),
					'success' => true,
					'alert_type' => 'success',
				]);
				die();
			}
		}
		echo json_encode([
			'message' => _l('cong_delete_false'),
			'success' => false,
			'alert_type' => 'danger',
		]);
		die();
	}

	public function active_automations()
	{
		var_dump($this->automations_model->active_automations());
		die();
	}
}
