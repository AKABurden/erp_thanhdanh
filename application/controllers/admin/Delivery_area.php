<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_area extends AdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data['title'] = _l('Khu vực giao hàng');
		$this->load->view('admin/delivery_area/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('delivery_area');
	}

	public function get_row_delivery_area($id = '')
	{
		$data['main'] = get_table_where('tbldelivery_area', array('id' => $id), '', 'row');
		$data['main_detail'] = get_table_where('tbldelivery_area_detail', array('id_delivery_area' => $id));
		$main_detail_v2 = array();
		foreach ($data['main_detail'] as $key => $value) {
			$main_detail_v2[] = $value['id_district'];
		}
		$data['main_detail_v2'] = $main_detail_v2;
		echo json_encode($data);
	}

	public function get_district()
	{
		if ($this->input->post()) {
			$id_province = $this->input->post('id_province');
			$id = $this->input->post('id');
			if (!empty($id)) {
				$districtid = get_table_where('tbldelivery_area_detail', array('id_delivery_area !=' => $id));
			} else {
				$districtid = get_table_where('tbldelivery_area_detail');
			}
			$district = array();
			foreach ($districtid as $key => $value) {
				$district[] = $value['id_district'];
			}
			if (!empty($id_province)) {
				$this->db->where('provinceid', $id_province);
				if (!empty($district)) {
					$this->db->where_not_in('districtid', $district);
				}
				$district = $this->db->get('tbldistrict')->result_array();
				echo json_encode($district);
				die();
			}
		}
		echo json_encode(array());
		die();
	}

	public function add_delivery_area()
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$_data['code'] = $data['code'];
			$_data['city'] = $data['city'];
			$_data['note'] = $data['note'];
			$this->db->insert('tbldelivery_area', $_data);
			$id = $this->db->insert_id();
			if ($id) {
				foreach ($data['district'] as $key => $value) {
					$__data['id_district'] = $value;
					$__data['id_delivery_area'] = $id;
					$this->db->insert('tbldelivery_area_detail', $__data);
				}
				$success = true;
				$message = _l('Thêm thành công');
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function update_delivery_area($id = '')
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$_data['code'] = $data['code'];
			$_data['city'] = $data['city'];
			$_data['note'] = $data['note'];
			$this->db->update('tbldelivery_area', $_data, array('id' => $id));
			$this->db->delete('tbldelivery_area_detail', array('id_delivery_area' => $id));
			foreach ($data['district'] as $key => $value) {
				$__data['id_district'] = $value;
				$__data['id_delivery_area'] = $id;
				$this->db->insert('tbldelivery_area_detail', $__data);
			}
			$success = true;
			$message = _l('Sửa thành công');
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function delete_delivery_area($id = '')
	{
		if (!$id) {
			die('ch_no_items');
		}
		$alert_type = 'warning';
		$message = _l('ch_no_delete');
//        if (is_admin()) {
		$this->db->where('id', $id);
		$this->db->delete('tbldelivery_area');
		if ($this->db->affected_rows() > 0) {
			$this->db->where('id_delivery_area', $id);
			$this->db->delete('tbldelivery_area_detail');
			$alert_type = 'success';
			$message = _l('ch_delete');
		}
//        }
		echo json_encode(array(
			'alert_type' => $alert_type,
			'message' => $message
		));
	}
}