<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Supplier_classify extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function index()
		{
			$data['title'] = _l('supplier_classify');
			$this->load->view('admin/supplier_classify/manage', $data);
		}
		
		public function table_supplier_classify($value = '')
		{
			if ($this->input->is_ajax_request()) {
				$this->app->get_table_data('supplier_classify');
			}
		}
		
		public function getData($id = '')
		{
			$data = get_table_where('tblsupplier_classify', array('id' => $id), '', 'row');
			echo json_encode($data);
		}
		
		public function add()
		{
			$data = $this->input->post();
			$data['code_supplier_classify'] = trim($data['code_supplier_classify']);
			
			$this->db->where('code_supplier_classify', $data['code_supplier_classify']);
			$kt_code = $this->db->get('tblsupplier_classify')->row();
			if(!empty($kt_code)) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => 'Mã phân loại nhà cung cấp đã tồn tại'
				]);die();
			}
			$in = array(
				'code_supplier_classify' => $data['code_supplier_classify'],
				'name' => $data['name'],
				'compare' => $data['compare'],
				'percent' => $data['percent'],
				'result_warning' => $data['result_warning']
			);
			$insert_id = $this->db->insert('tblsupplier_classify', $in);
			$alert_type = 'danger';
			$message = _l('edit_slide_false');
			$success = false;
			if ($insert_id) {
				$alert_type = 'success';
				$message = _l('edit_slide_true');
				$success = true;
			}
			echo json_encode(array(
				'success' => $success,
				'alert_type' => $alert_type,
				'message' => $message
			));
			die;
		}
		
		public function edit($id = '')
		{
			$data = $this->input->post();
			$data['code_supplier_classify'] = trim($data['code_supplier_classify']);
			$this->db->where('code_supplier_classify', $data['code_supplier_classify']);
			$this->db->where('id != "'.$id.'"', false, false);
			$kt_code = $this->db->get('tblsupplier_classify')->row();
			if(!empty($kt_code)) {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Mã phân loại nhà cung cấp đã tồn tại']);die();
			}
			$in = array(
				'code_supplier_classify' => $data['code_supplier_classify'],
				'name' => $data['name'],
				'compare' => $data['compare'],
				'percent' => $data['percent'],
				'result_warning' => $data['result_warning']
			);
			$this->db->where('id', $id);
			$insert_id = $this->db->update('tblsupplier_classify', $in);
			$alert_type = 'danger';
			$message = _l('edit_slide_false');
			$success = false;
			if ($insert_id) {
				$alert_type = 'success';
				$message = _l('edit_slide_true');
				$success = true;
			}
			echo json_encode(array(
				'success' => $success,
				'alert_type' => $alert_type,
				'message' => $message
			));
			die;
		}
		
		public function delete_supplier_classify($id = '')
		{
			$this->db->where('id', $id);
			$result = $this->db->delete('tblsupplier_classify');
			$alert_type = 'danger';
			$message = _l('ch_delete_successfuly_no');
			$success = false;
			if ($result) {
				$alert_type = 'success';
				$message = _l('ch_delete_successfuly');
				$success = true;
			}
			echo json_encode(array(
				'success' => $success,
				'alert_type' => $alert_type,
				'message' => $message
			));
			die;
		}
	}
