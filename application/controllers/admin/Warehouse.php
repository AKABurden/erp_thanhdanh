<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('invoice_items_model');
		$this->isAdmin = is_admin();
	}

	public function index()
	{
		if (!has_permission('warehouse', '', 'view')) {
			access_denied('warehouse');
		}
		$arrBranch = get_branch_staff();
		$staff = $this->site_model->getStaff();
		$data['staff'] = $staff;
		//		$data['branch'] = $this->site_model->getBranch();
		$data['branch'] = getListBranch();
		$this->db->select('tblwarehouse.*');
		$this->db->from('tblwarehouse');
		if (!$this->isAdmin) {
			if (!empty($arrBranch)) {
				$coverStrBranch = implode(",", $arrBranch);
				$this->db->where('tblwarehouse.id_branch IN (' . $coverStrBranch . ')');
			} else {
				$this->db->where('tblwarehouse.id', 0);
			}
		}
		$this->db->order_by('id DESC');
		$data['warehouse'] = $this->db->get()->result_array();
		$data['group'] = get_table_where('tblgroup_warehouse');
		$data['title'] = _l('warehouse');
		$this->load->view('admin/warehouse/manage', $data);
	}
	// public function count_all()
	// {
	//     $count = get_table_where_select('count(*) as alls','tblwarehouse',array(),'','row');
	//     $warehouse_cty = get_table_where_select('count(*) as warehouse_cty','tblwarehouse',array('supplier_id'=>0),'','row');
	//     $warehouse_gcong = get_table_where_select('count(*) as warehouse_gcong','tblwarehouse',array('supplier_id !='=>0),'','row');
	//     $data['all'] = $count->alls;
	//     $data['warehouse_cty'] = $warehouse_cty->warehouse_cty;
	//     $data['warehouse_gcong'] = $warehouse_gcong->warehouse_gcong;
	//     echo json_encode($data);
	// }
	public function detail($id = '')
	{
		$data = $this->input->post();
		if ($id == "") {
			if (!has_permission('warehouse', '', 'create')) {
				echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền tạo kho')));
				die;
			}
			$in = array(
				'id_group_warehouse' => $data['id_group_warehouse'],
				'code' => $data['code'],
				'name' => $data['name'],
				'address' => $data['address'],
				'note' => $data['note'],
				'id_branch' => $data['id_branch'],
			);
			$this->db->insert('tblwarehouse', $in);
			$insert_id = $this->db->insert_id();
			//handling staff
			$staff_id = $this->input->post('staff_id');
			if (!empty($staff_id)) {
				$dataStaff = [];
				foreach ($staff_id as $key => $value) {
					$dataStaff[$key]['warehouse_id'] = $insert_id;
					$dataStaff[$key]['staff_id'] = $value;
				}
				$this->site_model->insertBatchWarehouseStaff($dataStaff);
			}
			//end hangdling staff
			$get_code = get_table_where('tblwarehouse', array('id' => $insert_id), '', 'row');
			activity_log_v2('warehouse', 'tblwarehouse', $insert_id, $get_code->name, 'Thêm mới kho hàng [' . $get_code->name . ']');
			echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_warehouse_success')));
		} else {
			if (!has_permission('warehouse', '', 'create')) {
				echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền sửa kho')));
				die;
			}
			$in = array(
				'id_group_warehouse' => $data['id_group_warehouse'],
				'code' => $data['code'],
				'name' => $data['name'],
				'address' => $data['address'],
				'note' => $data['note'],
				'id_branch' => $data['id_branch'],
			);
			$this->db->where('id', $id);
			$up = $this->db->update('tblwarehouse', $in);
			if ($up) {
				//handling staff
				$this->site_model->deleteWarehouseStaff($id);
				$staff_id = $this->input->post('staff_id');
				if (!empty($staff_id)) {
					$dataStaff = [];
					foreach ($staff_id as $key => $value) {
						$dataStaff[$key]['warehouse_id'] = $id;
						$dataStaff[$key]['staff_id'] = $value;
					}
					$this->site_model->insertBatchWarehouseStaff($dataStaff);
				}
				//end hangdling staff
			}
			$get_code = get_table_where('tblwarehouse', array('id' => $id), '', 'row');
			activity_log_v2('warehouse', 'tblwarehouse', $id, $get_code->name, 'Cập nhật kho hàng [' . $get_code->name . ']');
			echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('edit_warehouse_success')));
		}
	}

	public function group()
	{
		if (!has_permission('warehouse_group', '', 'view')) {
			access_denied('warehouse_group');
		}
		$data['title'] = _l('kb_dt_group_name');
		$this->load->view('admin/warehouse/group', $data);
	}

	public function table_warehouse($value = '')
	{
		$this->app->get_table_data('warehouse');
	}

	public function table_warehouse_group($value = '')
	{
		$this->app->get_table_data('warehouse_group');
	}

	public function getData($id = '')
	{
		$data = get_table_where('tblwarehouse', array('id' => $id), '', 'row');
		$staffWarehouse = $this->site_model->getStaffWarehouse($id);
		$data->staff_warehouse = $staffWarehouse;
		echo json_encode($data);
	}

	public function getData_group($id = '')
	{
		$data = get_table_where('tblgroup_warehouse', array('id' => $id), '', 'row');
		echo json_encode($data);
	}

	public function group_detail($id = '')
	{
		$data = $this->input->post();
		if ($id == "") {
			if (!has_permission('warehouse_group', '', 'create')) {
				echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền tạo nhóm kho')));
				die;
			}
			$in = array(
				'code' => $data['code'],
				'name' => $data['name'],
			);
			$this->db->insert('tblgroup_warehouse', $in);
			$insert_id = $this->db->insert_id();
			$get_code = get_table_where('tblgroup_warehouse', array('id' => $insert_id), '', 'row');
			activity_log_v2('warehouse', 'tblgroup_warehouse', $insert_id, $get_code->name, 'Thêm mới nhóm kho hàng [' . $get_code->name . ']');
			echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_warehouse_group_success')));
		} else {
			if (!has_permission('warehouse_group', '', 'edit')) {
				echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền sửa nhóm kho')));
				die;
			}
			$in = array(
				'code' => $data['code'],
				'name' => $data['name'],
			);
			$this->db->where('id', $id);
			$this->db->update('tblgroup_warehouse', $in);
			$get_code = get_table_where('tblgroup_warehouse', array('id' => $id), '', 'row');
			activity_log_v2('warehouse', 'tblgroup_warehouse', $id, $get_code->name, 'Cập nhật nhóm kho hàng [' . $get_code->name . ']');
			echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('edit_warehouse_group_success')));
		}
	}

	public function delete_group($id)
	{
		if (!has_permission('warehouse_group', '', 'delete')) {
			echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền xóa nhóm kho')));
			die;
		}
		if (!$id) {
			redirect(admin_url('warehouse/group'));
		}
		$checkWarehouse = get_table_where('tblwarehouse', array('id_group_warehouse' => $id), '', 'row');
		if (!$checkWarehouse) {
			$get_code = get_table_where('tblgroup_warehouse', array('id' => $id), '', 'row');
			activity_log_v2('warehouse', 'tblgroup_warehouse', $id, $get_code->name, 'Xóa nhóm kho hàng [' . $get_code->name . ']');
			$this->db->where('id', $id);
			$response = $this->db->delete('tblgroup_warehouse');
		} else {
			$response = false;
		}
		if ($response == true) {
			echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('delete_warehouse_group')));
		} else {
			echo json_encode(array('success' => false, 'alert_type' => 'warning', 'message' => _l('problem_deleting')));
		}
	}

	public function delete_main($id)
	{
		if (!has_permission('warehouse', '', 'delete')) {
			echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền xóa kho')));
			die;
		}
		if (!$id) {
			redirect(admin_url('warehouse'));
		}
		if (($id == WAREHOUSES_CAPACITY || $id == WAREHOUSES_HOLD || $id == WAREHOUSES_ERRORS)) {
			echo json_encode(array('success' => false, 'alert_type' => 'warning', 'message' => _l('Kho hệ thống không thể xóa')));
			die;
		}
		$ktr = get_table_where('tblwarehouse_product', array('warehouse_id' => $id), '', 'row');
		if (!empty($ktr)) {
			echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Kho đã được sử dụng không thể xóa')));
			die;
		}
		$get_code = get_table_where('tblwarehouse', array('id' => $id), '', 'row');
		activity_log_v2('warehouse', 'tblwarehouse', $id, $get_code->name, 'Xóa kho hàng [' . $get_code->name . ']');
		$this->db->where('id', $id);
		$response = $this->db->delete('tblwarehouse');
		if ($response == true) {
			$this->site_model->deleteWarehouseStaff($id);
			echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('delete_warehouse')));
		} else {
			echo json_encode(array('success' => false, 'alert_type' => 'warning', 'message' => _l('problem_deleting')));
		}
	}

	public function localtion()
	{
		if (!has_permission('warehouse_localtion', '', 'view')) {
			access_denied('warehouse_localtion');
		}
		$data['warehouse'] = get_table_where('tblwarehouse');
		$data['title'] = _l('warehouse_localtion_t');
		$this->load->view('admin/warehouse/warehouse_localtion', $data);
	}

	public function get_exist($id = '')
	{
		$localtion = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
		if (exsit_localtion($localtion->warehouse, $id)) {
			echo json_encode(array('alert_type' => 'danger', 'message' => _l('Vị trí đã sử dụng, Không thể xóa')));
			die();
		}
		$this->db->where_in('id', $id);
		if ($this->db->delete('tbllocaltion_warehouses')) {
			$id_parent = get_table_where('tbllocaltion_warehouses', array('id_parent' => $localtion->id_parent), '', 'row');
			if (empty($id_parent)) {
				$this->db->update('tbllocaltion_warehouses', array('child' => 1), array('id' => $localtion->id_parent));
			}
			echo json_encode(array('alert_type' => 'success', 'message' => _l('cong_action_delete_false')));
			die();
		}
		echo json_encode(array('alert_type' => 'warning', 'message' => _l('cong_action_delete_false')));
		die();
	}

	public function table_warehouse_localtion()
	{
		$this->app->get_table_data('warehouse_localtion');
	}

	public function add_location_warehouse()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!empty($data['type_excel'])) {
				if ($data['type_excel'] == 'on') {
					$type_excel = 1;
				} else {
					$type_excel = 0;
				}
			} else {
				$type_excel = 0;
			}
			$data['type_excel'] = $type_excel;
			if (!empty($data['id'])) {
				$data['child'] = 0;
				$kt_parent = get_table_where('tbllocaltion_warehouses', array('id_parent' => $data['id']), '', 'row');
				if (empty($kt_parent) && !empty($data['id_parent'])) {
					$data['child'] = 1;
				}
				if (empty($kt_parent) && empty($data['id_parent'])) {
					$data['child'] = 1;
				}
				$id = $data['id'];
				unset($data['id']);
				$this->db->where('id', $id);
				if ($this->db->update('tbllocaltion_warehouses', $data)) {
					if (!empty($data['id_parent'])) {
						$get_parent = get_table_where('tbllocaltion_warehouses', array('id' => $data['id_parent']), '', 'row');
						$name_parent = $get_parent->name_parent . " <i class='fa fa-caret-right text-danger' aria-hidden='true'></i> " . $data['name'];
					} else {
						$name_parent = $data['name'];
					}
					$this->db->where('id', $id);
					$this->db->update('tbllocaltion_warehouses', array('name_parent' => $name_parent));
					if (!empty($data['id_parent'])) {
						$this->db->where('id', $data['id_parent']);
						$this->db->update('tbllocaltion_warehouses', array('child' => 0));
					}
					$this->update_parents($id, $name_parent);
					$get_code = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
					activity_log_v2('warehouse', 'tbllocaltion_warehouses', $id, $get_code->name, 'Cập nhật vị trí kho hàng [' . $get_code->name . ']');
					echo json_encode(array('success' => true, 'message' => 'Cập nhật dữ liệu thành công'));
					die();
				}
				echo json_encode(array('success' => false, 'message' => 'Cập nhật dữ liệu không thành công'));
				die();
			} else {
				unset($data['id']);
				$data['child'] = 1;
				$data['create_by'] = get_staff_user_id();
				$data['date_create'] = date('Y-m-d H:i:s');
				if (empty($data['id_parent'])) {
					$data['lever'] = 1;
				} else {
					$lever = 1;
					$parent = $data['id_parent'];
					while ($parent > 0) {
						$ktr = get_table_where('tbllocaltion_warehouses', array('id' => $parent, 'warehouse' => $data['warehouse']), '', 'row');
						$parent = $ktr->id_parent;
						$lever++;
					}
					$data['lever'] = $lever;
				}
				$this->db->insert('tbllocaltion_warehouses', $data);
				$idd = $this->db->insert_id();
				if ($idd) {
					if (!empty($data['id_parent'])) {
						$this->db->update('tbllocaltion_warehouses', array('child' => 0), array('id' => $data['id_parent']));
					}
					if (!empty($data['id_parent'])) {
						$get_parent = get_table_where('tbllocaltion_warehouses', array('id' => $data['id_parent']), '', 'row');
						$name_parent = $get_parent->name_parent . " <i class='fa fa-caret-right text-danger' aria-hidden='true'></i> " . $data['name'];
					} else {
						$name_parent = $data['name'];
					}
					$this->db->where('id', $idd);
					$this->db->update('tbllocaltion_warehouses', array('name_parent' => $name_parent));
					$get_code = get_table_where('tbllocaltion_warehouses', array('id' => $idd), '', 'row');
					activity_log_v2('warehouse', 'tbllocaltion_warehouses', $idd, $get_code->name, 'Thêm mới vị trí kho hàng [' . $get_code->name . ']');
					echo json_encode(array('success' => true, 'message' => 'Thêm dữ liệu thành công'));
					die();
				}
				echo json_encode(array('success' => false, 'message' => 'Thêm dữ liệu không thành công'));
				die();
			}
		}
	}

	// update lại tên cha của code
	public function update_parents($id_parent = "", $name_parent = "")
	{
		if (!empty($id_parent)) {
			$this->db->where('id_parent', $id_parent);
			$localtion_warehouses = $this->db->get('tbllocaltion_warehouses')->result_array();
			foreach ($localtion_warehouses as $key => $value) {
				$this->db->where('id', $value['id']);
				$new_name_parent = (!empty($name_parent) ? ($name_parent . " <i class='fa fa-caret-right text-danger' aria-hidden='true'></i> " . $value['name']) : $value['name']);
				$this->db->update('tbllocaltion_warehouses', array('name_parent' => $new_name_parent));
				$this->update_parents($value['id'], $new_name_parent);
			}
		}
	}

	public function updadte_all()
	{
		$this->db->where('(id_parent is null or id_parent = 0)');
		$localtion_parent = $this->db->get('tbllocaltion_warehouses')->result_array();
		foreach ($localtion_parent as $key => $value) {
			$this->db->where('id', $value['id']);
			$this->db->update('tbllocaltion_warehouses', array('name_parent' => $value['name']));
			$this->update_parents($value['id'], $value['name']);
		}
	}

	public function change_warehouse_localtion_v2($id = '')
	{
		$parent = get_table_where('tbllocaltion_warehouses', array('id_parent' => $id));
		if (!empty($parent)) {
			foreach ($parent as $key => $value) {
				$this->db->where('id', $value['id']);
				$this->db->update('tbllocaltion_warehouses', [
					'status' => 2,
				]);
				$this->change_warehouse_localtion_v2($value['id']);
			}
		} else {
			return false;
		}
	}

	public function change_warehouse_localtion_v3($id = '')
	{
		$parent = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
		if (!empty($parent->id_parent)) {
			$this->db->where('id', $parent->id_parent);
			$this->db->update('tbllocaltion_warehouses', [
				'status' => 0,
			]);
			$this->change_warehouse_localtion_v3($parent->id_parent);
		} else {
			return false;
		}
	}

	public function change_warehouse_localtion_status($id, $status)
	{
		if (!has_permission('warehouse_localtion', '', 'edit')) {
			echo json_encode([
				'alert_type' => 'warning',
				'message' => 'Bạn không có quyền sửa trạng thái',
			]);
			die;
		}
		if ($id == LOCATIONS_DEFAULT_MANUFACTURES) {
			echo json_encode([
				'alert_type' => 'warning',
				'message' => 'Vị trí mặc định không thể thao tác',
			]);
			die;
		}
		if ($status == 0) {
			$status = 2;
		} elseif ($status == 2) {
			$status = 0;
		}
		$this->db->where('id', $id);
		$this->db->update('tbllocaltion_warehouses', [
			'status' => $status,
		]);
		$message = 'Chuyển không thành công!';
		$success = 'warning';
		if ($this->db->affected_rows() > 0) {
			if ($status == 2) {
				$this->change_warehouse_localtion_v2($id);
			} elseif ($status == 0) {
				$this->change_warehouse_localtion_v3($id);
			}
			$get_code = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
			activity_log_v2('warehouse', 'tbllocaltion_warehouses', $id, $get_code->name, 'Cập nhật trạng thái vị trí kho hàng [' . $get_code->name . ']');
			log_activity('Warehouse localtion Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
			$message = 'Chuyển thành công!';
			$success = 'success';
		}
		echo json_encode([
			'alert_type' => $success,
			'message' => $message,
		]);
		die;
	}

	public function delete_location_warehouse()
	{
		if (!has_permission('warehouse_localtion', '', 'delete')) {
			echo json_encode(array('success' => false, 'message' => 'Xóa dữ liệu không thành công'));
			die();
		}
		$id = $this->input->post('id');
		if ($id == LOCATIONS_DEFAULT_MANUFACTURES) {
			echo json_encode(array('success' => false, 'message' => 'Xóa dữ liệu không thành công'));
			die();
		}
		$id_new = $this->input->post('id_new');
		if (!empty($id)) {
			$list_id = array();
			$localtion = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
			if (exsit_localtion($localtion->warehouse, $id)) {
				echo json_encode(array('success' => false, 'message' => 'Vị trí đã sử dụng, Không thể xóa'));
				die();
			}
			$list_id[] = $localtion->id;
			$this->get_list_id_child($localtion->id, $list_id);
			foreach ($list_id as $key => $value) {
				if ($value == $id_new) {
					echo json_encode(array('success' => false, 'message' => 'Xóa dữ liệu không thành công vì vị trí chuyển đến sẽ bị xóa'));
					die();
				}
			}
			$array_localtion_product = array();
			foreach ($list_id as $key => $value) {
				$product_warehouse = get_table_where('tblwarehouses_products', array('localtion' => $value, 'warehouse_id' => $localtion->warehouse));
				foreach ($product_warehouse as $k => $v) {
					if (!empty($v['product_id'])) {
						if (empty($array_localtion_product[$v['product_id']])) {
							$array_localtion_product[$v['product_id']] = $v['product_quantity'];
						} else {
							$array_localtion_product[$v['product_id']] += $v['product_quantity'];
						}
					}
				}
			}
			$localtion_warehouse_new = get_table_where('tbllocaltion_warehouses', array('id' => $id_new), '', 'row');
			foreach ($array_localtion_product as $key => $value) {
				$kt_warehouse = get_table_where('tblwarehouses_products', array(
					'localtion' => $id_new,
					'warehouse_id' => $localtion_warehouse_new->warehouse,
					'product_id' => $key
				), '', 'row');
				if (empty($kt_warehouse)) {
					$this->db->insert('tblwarehouses_products', array(
						'warehouse_id' => $localtion_warehouse_new->warehouse,
						'localtion' => $id_new, 'product_id' => $key, 'product_quantity' => $value
					));
				} else {
					$this->db->where('id', $kt_warehouse->id);
					$this->db->update('tblwarehouses_products', array('product_quantity' => ($kt_warehouse->product_quantity + $value)));
				}
			}
			$this->db->where_in('localtion', $list_id);
			$this->db->where('warehouse_id', $localtion_warehouse_new->warehouse);
			$this->db->delete('tblwarehouses_products');
			if (!empty($localtion->id_parent)) {
				$this->db->where('id_parent', $localtion->id_parent);
				$this->db->where_not_in('id', $list_id);
				$kt_localtion_parent = $this->db->get('tbllocaltion_warehouses')->result_array();
				if (empty($kt_localtion_parent)) {
					$this->db->where('id', $localtion->id_parent);
					$this->db->update('tbllocaltion_warehouses', array('child' => 0));
				}
			}
			$this->db->where_in('id', $list_id);
			if ($this->db->delete('tbllocaltion_warehouses')) {
				echo json_encode(array('success' => true, 'message' => 'cong_action_delete_true'));
				die();
			}
		}
		echo json_encode(array('success' => false, 'message' => 'Xóa dữ liệu không thành công'));
		die();
	}

	public function get_localtion_warehouse($id = "") // lấy vị trí
	{
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			$localtion_warehouses = $this->db->get('tbllocaltion_warehouses')->row();
			echo json_encode($localtion_warehouses);
			die();
		}
	}

	public function list_localtion() // lấy danh sách vị trí
	{
		$warehouse = $this->input->post('warehouse');
		$lever = $this->input->post('lever');
		$checked = $this->input->post('checked');
		if (!empty($warehouse)) {
			echo get_localtion_warehouses(array('warehouse' => $warehouse), $lever, $checked);
			die();
		}
	}

	public function list_localtion_kk() // lấy danh sách vị trí
	{
		$warehouse = $this->input->post('warehouse');
		$lever = $this->input->post('lever');
		$checked = $this->input->post('checked');
		if (!empty($warehouse)) {
			echo get_localtion_warehouses_kk(array('warehouse' => $warehouse), $lever, $checked);
			die();
		}
	}

	public function list_localtion_new() // lấy danh sách vị trí
	{
		$warehouse = $this->input->post('warehouse');
		$lever = $this->input->post('lever');
		$checked = $this->input->post('checked');
		$plan_id = $this->input->post('plan_id');
		if (empty($checked)) {
			if (!empty($plan_id) && $plan_id != 'null' && $warehouse == WAREHOUSES_CAPACITY) {
				$location = LOCATIONS_DEFAULT_MANUFACTURES;
				if (!empty($location)) {
					$checked = $location;
				}
			}
		}
		if (!empty($warehouse)) {
			echo json_encode(get_localtion_warehouses_new(array('warehouse' => $warehouse), $lever, $checked));
			die();
		}
	}

	public function list_localtion_v2() // lấy danh sách vị trí
	{
		$warehouse = $this->input->post('warehouse');
		$lever = $this->input->post('lever');
		$checked = $this->input->post('checked');
		if (!empty($warehouse)) {
			echo get_localtion_warehouses(array('warehouse' => $warehouse), $lever, $checked);
			die();
		}
	}

	public function detail_warehouse($id = '')
	{
		if (!has_permission('warehouse', '', 'view')) {
			access_denied('warehouse');
		}
		// $data['localtion'] = get_localtion_warehouses(array('warehouse' => $id));
		$data['type_items'] = get_table_where('tbltype_items', array('active' => 1));
		$data['id'] = $id;
		$data['tnh'] = true;
		$warehouse = get_table_where('tblwarehouse', array('id' => $id), '', 'row');
		$data['title'] = _l('ch_warehouse_t') . ' ' . $warehouse->name;
		$this->load->view('admin/warehouse/detail', $data);
	}

	public function table_warehouse_items($id = '')
	{
		$this->app->get_table_data('warehouse_items', array('id' => $id));
	}

	public function table_warehouse_items_product($id = '')
	{
		$this->app->get_table_data('warehouse_items_product', array('id' => $id, 'type' => 'product'));
	}

	public function table_warehouse_items_productsemi($id = '')
	{
		$this->app->get_table_data('warehouse_items_product', array('id' => $id, 'type' => 'semi_products'));
	}

	public function table_warehouse_items_nvl($id = '')
	{
		$this->app->get_table_data('warehouse_items', array('id' => $id, 'type' => 'nvl'));
	}

	public function table_warehouse_items_tools($id = '')
	{
		$this->app->get_table_data('warehouse_items', array('id' => $id, 'type' => 'tools'));
	}

	public function list_localtion_v3() // lấy danh sách vị trí
	{
		$lever = $this->input->post('lever');
		$checked = $this->input->post('checked');
		echo get_localtion_warehouses_transfer(array(), $lever, $checked);
		die();
	}

	public function list_localtion_product_transfer_report()
	{
		$warehouse = $this->input->post('warehouse');
		$id_product = $this->input->post('id_product');
		$type = $this->input->post('type');
		$pod_id = $this->input->post('pod_id');
		$checked = 0;
		if (!empty($pod_id)) {
			$this->db->select('tblwarehouse_items.id');
			$this->db->where('tbllocaltion_warehouses.pod_id', $pod_id);
			$this->db->where('tbllocaltion_warehouses.stage_id', 0);
			$this->db->where('tblwarehouse_items.product_quantity >', 0);
			$this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id=tblwarehouse_items.localtion', 'left');
			$warehouse = $this->db->get('tblwarehouse_items')->row();
			if (!empty($warehouse)) {
				$checked = $warehouse->id;
			}
		}
		$data = array();
		$data['html'] = get_localtion_warehouses_product_tranfer_report(array('id_items' => $id_product, 'type_items' => $type));
		$data['checked'] = $checked;
		echo json_encode($data);
		die();
	}

	public function list_localtion_product_transfer()
	{
		$warehouse = $this->input->post('warehouse');
		$id_product = $this->input->post('id_product');
		$type = $this->input->post('type');
		$type = $this->input->post('type');
		if (!empty($type)) {
			echo get_localtion_warehouses_product_tranfer(array('id_items' => $id_product, 'type_items' => $type));
			die();
		} else {
			echo get_localtion_warehouses_product_tranfer(array('id_items' => $id_product));
			die();
		}
	}

	public function list_localtion_product_export()
	{
		$warehouse = $this->input->post('warehouse');
		$id_product = $this->input->post('id_product');
		$type = $this->input->post('type');
		if (!empty($type)) {
			echo get_localtion_warehouses_product_export(array('id_items' => $id_product, 'type_items' => $type));
			die();
		} else {
			echo get_localtion_warehouses_product_export(array('id_items' => $id_product));
			die();
		}
	}

	public function list_localtion_product_v2()
	{
		$warehouse = $this->input->post('warehouse');
		$id_product = $this->input->post('id_product');
		$type = $this->input->post('type');
		if (!empty($type)) {
			echo get_localtion_warehouses_product_v2(array('id_items' => $id_product, 'type_items' => $type));
			die();
		} else {
			echo get_localtion_warehouses_product_v2(array('id_items' => $id_product));
			die();
		}
	}

	public function list_localtion_product()
	{
		$warehouse = $this->input->post('warehouse');
		$id_product = $this->input->post('id_product');
		$type = $this->input->post('type');
		if (!empty($type) && !empty($warehouse)) {
			echo get_localtion_warehouses_product(array('warehouse' => $warehouse, 'id_items' => $id_product, 'type_items' => $type));
			die();
		}
		if (!empty($warehouse)) {
			echo get_localtion_warehouses_product(array('warehouse' => $warehouse, 'id_items' => $id_product));
			die();
		}
	}

	public function get_localtion()
	{
		$data = array();
		$date = to_sql_date($this->input->post('date'));
		$id = $this->input->post('id');
		$warehouses = $this->input->post('warehouses');
		$type = $this->input->post('type');
		$localtion_id = $this->input->post('localtion');
		$lot_code = $this->input->post('lot_code');
		$date_sx = to_sql_date($this->input->post('date_sx'));
		$date_sd = to_sql_date($this->input->post('date_sd'));
		$date_use = $this->input->post('date_use');
		if (empty($localtion_id)) {
			$localtion = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouses, 'id_items' => $id, 'type_items' => $type, 'product_quantity >' => 0));
			foreach ($localtion as $key => $value) {
				if ($date != date('Y-m-d')) {
					$whereJoin = array();
					$whereJoin['where'] = array(
						'date_warehouse <= ' => $date . ' 23:59:59',
						'product_id ' => $id,
						'localtion ' => $value['localtion'],
						'type_items ' => $type,
						'lot_code ' => $value['lot_code'],
						'date_sx ' => $value['date_sx'],
						'date_sd ' => $value['date_sd'],
						'date_use ' => $value['date_use'],
					);
					$whereJoin['join'] = array();
					$whereJoin['field'] = 'quantity';
					$get_quantity_import = sum_from_table_join('tblwarehouse_product', $whereJoin);
					$whereJoin_export = array();
					$whereJoin_export['where'] = array(
						'date_warehouse <= ' => $date . ' 23:59:59',
						'product_id ' => $id,
						'localtion ' => $value['localtion'],
						'type_items ' => $type,
						'lot_code ' => $value['lot_code'],
						'date_sx ' => $value['date_sx'],
						'date_sd ' => $value['date_sd'],
						'date_use ' => $value['date_use'],
					);
					$whereJoin_export['join'] = array();
					$whereJoin_export['field'] = 'quantity';
					$get_quantity_export = sum_from_table_join('tblwarehouse_export', $whereJoin_export);
					if (empty($get_quantity_export)) {
						$get_quantity_export = 0;
					}
					if (empty($get_quantity_import)) {
						$get_quantity_import = 0;
					}
				} else {
					$get_quantity_export = 0;
					$get_quantity_import = $value['product_quantity'];
				}
				$data[$key]['lot_code'] = (empty($value['lot_code']) ? NULL : ($value['lot_code']));
				$data[$key]['date_sx'] = (empty($value['date_sx']) ? NULL : _d($value['date_sx']));
				$data[$key]['date_sd'] = (empty($value['date_sd']) ? NULL : _d($value['date_sd']));
				$data[$key]['date_use'] = (empty($value['date_use']) ? NULL : ($value['date_use']));
				$data[$key]['localtion'] = $value['localtion'];
				$data[$key]['name_localtion'] = get_listname_localtion_warehouse($value['localtion']);
				$data[$key]['get_quantity_import'] = $get_quantity_import - $get_quantity_export;
				$data[$key]['items'] = $this->invoice_items_model->get_full_item($id, $type);
				$data[$key]['mode'] = '';
				if (isset($data[$key]['items']->mode)) {
					$data[$key]['mode'] = '<br>' . _l('ch_items_specification') . ': ' . $data[$key]['items']->mode;
				}
			}
		} else {
			if ($date != date('Y-m-d')) {
				$whereJoin = array();
				$whereJoin['where'] = array(
					'date_warehouse <= ' => $date . ' 23:59:59',
					'product_id ' => $id,
					'localtion ' => $localtion_id,
					'type_items ' => $type,
					'lot_code ' => $lot_code,
					'date_sx ' => $date_sx,
					'date_sd ' => $date_sd,
					'date_use ' => $date_use,
				);
				$whereJoin['join'] = array();
				$whereJoin['field'] = 'quantity';
				$get_quantity_import = sum_from_table_join('tblwarehouse_product', $whereJoin);
				$whereJoin_export = array();
				$whereJoin_export['where'] = array(
					'date_warehouse <= ' => $date . ' 23:59:59',
					'product_id ' => $id,
					'localtion ' => $localtion_id,
					'type_items ' => $type,
					'lot_code ' => $lot_code,
					'date_sx ' => $date_sx,
					'date_sd ' => $date_sd,
					'date_use ' => $date_use,
				);
				$whereJoin_export['join'] = array();
				$whereJoin_export['field'] = 'quantity';
				$get_quantity_export = sum_from_table_join('tblwarehouse_export', $whereJoin_export);
				if (empty($get_quantity_export)) {
					$get_quantity_export = 0;
				}
				if (empty($get_quantity_import)) {
					$get_quantity_import = 0;
				}
			} else {
				$lot_code = (empty($lot_code) ? NULL : ($lot_code));
				$date_sx = (empty($date_sx) ? NULL : ($date_sx));
				$date_sd = (empty($date_sd) ? NULL : ($date_sd));
				$date_use = (empty($date_use) ? NULL : ($date_use));
				$localtion = get_table_where('tblwarehouse_items', array('localtion' => $localtion_id, 'warehouse_id' => $warehouses, 'id_items' => $id, 'type_items' => $type, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use), '', 'row');

				$get_quantity_export = 0;
				$get_quantity_import = 0;
				if (!empty($localtion)) {
					$get_quantity_import = $localtion->product_quantity;
				}
			}
			$data[0]['lot_code'] = (empty($lot_code) ? NULL : ($lot_code));
			$data[0]['date_sx'] = (empty($date_sx) ? NULL : _d($date_sx));
			$data[0]['date_sd'] = (empty($date_sd) ? NULL : _d($date_sd));
			$data[0]['date_use'] = (empty($date_use) ? NULL : ($date_use));
			$data[0]['localtion'] = $localtion_id;
			$data[0]['name_localtion'] = get_listname_localtion_warehouse($localtion_id);
			$data[0]['get_quantity_import'] = $get_quantity_import - $get_quantity_export;
			$data[0]['items'] = $this->invoice_items_model->get_full_item($id, $type);
			$data[0]['mode'] = '';
			if (isset($data[0]['items']->mode)) {
				$data[0]['mode'] = '<br>' . _l('ch_items_specification') . ': ' . $data[0]['items']->mode;
			}
		}
		echo json_encode($data);
	}

	public function list_localtion_return() // lấy danh sách vị trí
	{
		$warehouse = $this->input->post('warehouse');
		$items = $this->input->post('items');
		$checked = $this->input->post('checked');
		$type = $this->input->post('type');
		if (!empty($warehouse)) {
			echo get_localtion_warehouses_return(array('warehouse' => $warehouse), $items, $checked, $warehouse, $type);
			die();
		}
	}

	public function getLocationLotByItem() // lấy danh sách vị trí
	{
		$warehouse = $this->input->post('warehouse');
		$items = $this->input->post('items');
		$checked = $this->input->post('checked');
		$type = $this->input->post('type');
		if (!empty($warehouse)) {
			echo getLocationLot(array('warehouse' => $warehouse), $items, $checked, $warehouse, $type);
			die();
		}
	}

	public function warehouse_items_deal_items($id = '')
	{
		//Nhập kho
		$selectimport = array(
			'tblimport.warehouseman_date as warehouseman_date',
			'tblimport.date as date',
			'concat(tblimport.prefix,"-",tblimport.code) as code',
			'tblimport.note as reason',
			'tblunits.unit as unit',
			'tblimport_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'1 as exists_quantity'
		);
		$whereimport = array(
			'AND tblimport.warehouse_id =' . $id,
			'AND tblimport_items.type = "items"',
			'AND tblimport.warehouseman_id != 0',
		);
		$aColumnsimport = $selectimport;
		$sIndexColumnimport = "id";
		$sTableimport = 'tblimport_items';
		$joinimport = array(
			'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id',
			'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_byimport = 'order by product_id asc';
		$resultimport = data_tables_init_nolimt($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tblitems.name', 'tblitems.code  as code_items,tblimport.id as id_main,tblitems.id as product_id'));
		$outputimport = $resultimport['output'];
		$rResultimport = $resultimport['rResult'];
		//Xuất kho
		$selectexport = array(
			'tbl_export_warehouses.date_warehouseman as warehouseman_date',
			'tbl_export_warehouses.date as date',
			'tbl_export_warehouses.reference_no as code',
			'tbl_export_warehouses.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbl_export_warehous_items.quantity as export_quantity',
			'2 as exists_quantity'
		);
		$whereexport = array(
			'AND tbl_export_warehous_items.warehouse_id =' . $id,
			'AND tbl_export_warehous_items.type_item = "items"',
			'AND tbl_export_warehouses.warehouseman_id != 0',
		);
		$aColumnsexport = $selectexport;
		$sIndexColumnexport = "id";
		$sTableexport = 'tbl_export_warehous_items';
		$joinexport = array(
			'LEFT JOIN tblitems ON tblitems.id = tbl_export_warehous_items.item_id',
			'LEFT JOIN tbl_export_warehouses ON tbl_export_warehouses.id = tbl_export_warehous_items.export_warehouse_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_byexport = 'order by item_id asc';
		$resultexport = data_tables_init_nolimt($aColumnsexport, $sIndexColumnexport, $sTableexport, $joinexport, $whereexport, array('tblitems.name', 'tblitems.code  as code_items,tbl_export_warehouses.id as id_main,tblitems.id as product_id'));
		$outputexport = $resultexport['output'];
		$rResultexport = $resultexport['rResult'];
		//trả hàng NCC
		$select_return = array(
			'tblreturn_suppliers.data_warehouseman as warehouseman_date',
			'tblreturn_suppliers.date as date',
			'concat(tblreturn_suppliers.prefix,"",tblreturn_suppliers.code) as code',
			'tblreturn_suppliers.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tblreturn_suppliers_items.quantity_net as export_quantity',
			'3 as exists_quantity'
		);
		$where_return = array(
			'AND tblreturn_suppliers.warehouse_id =' . $id,
			'AND tblreturn_suppliers_items.type = "items"',
			'AND tblreturn_suppliers.warehouseman_id != 0',
		);
		$aColumns_return = $select_return;
		$sIndexColumn_return = "id";
		$sTable_return = 'tblreturn_suppliers_items';
		$join_return = array(
			'LEFT JOIN tblitems ON tblitems.id = tblreturn_suppliers_items.product_id',
			'LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_by_return = 'order by product_id asc';
		$result_return = data_tables_init_nolimt($aColumns_return, $sIndexColumn_return, $sTable_return, $join_return, $where_return, array('tblitems.name', 'tblitems.code  as code_items,tblreturn_suppliers.id as id_main,tblitems.id as product_id'));
		$output_return = $result_return['output'];
		$rResult_return = $result_return['rResult'];
		//Điều chỉnh kho giảm
		$select_adjustedG = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbladjusted_items.quantity_net as export_quantity',
			'4 as exists_quantity'
		);
		$where_adjustedG = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "items"',
			'AND tbladjusted.type = 2',
		);
		$aColumns_adjustedG = $select_adjustedG;
		$sIndexColumn_adjustedG = "id";
		$sTable_adjustedG = 'tbladjusted_items';
		$join_adjustedG = array(
			'LEFT JOIN tblitems ON tblitems.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_by_adjustedG = 'order by product_id asc';
		$result_adjustedG = data_tables_init_nolimt($aColumns_adjustedG, $sIndexColumn_adjustedG, $sTable_adjustedG, $join_adjustedG, $where_adjustedG, array('tblitems.name', 'tblitems.code  as code_items,tbladjusted.id as id_main,tblitems.id as product_id'));
		$output_adjustedG = $result_adjustedG['output'];
		$rResult_adjustedG = $result_adjustedG['rResult'];
		//Điều chỉnh kho tăng
		$select_adjustedT = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'tbladjusted_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'5 as exists_quantity'
		);
		$where_adjustedT = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "items"',
			'AND tbladjusted.type = 1',
		);
		$aColumns_adjustedT = $select_adjustedT;
		$sIndexColumn_adjustedT = "id";
		$sTable_adjustedT = 'tbladjusted_items';
		$join_adjustedT = array(
			'LEFT JOIN tblitems ON tblitems.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_by_adjustedT = 'order by product_id asc';
		$result_adjustedT = data_tables_init_nolimt($aColumns_adjustedT, $sIndexColumn_adjustedT, $sTable_adjustedT, $join_adjustedT, $where_adjustedT, array('tblitems.name', 'tblitems.code  as code_items,tbladjusted.id as id_main,tblitems.id as product_id'));
		$output_adjustedT = $result_adjustedT['output'];
		$rResult_adjustedT = $result_adjustedT['rResult'];
		//Chuyển kho: nhận
		$select_TranfersN = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'tbltransfer_warehouse_detail.quantity_net as import_quantity',
			'0 as export_quantity',
			'6 as exists_quantity'
		);
		$where_TranfersN = array(
			'AND tbltransfer_warehouse_detail.warehouses_to =' . $id,
			'AND tbltransfer_warehouse_detail.type = "items"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersN = $select_TranfersN;
		$sIndexColumn_TranfersN = "id";
		$sTable_TranfersN = 'tbltransfer_warehouse_detail';
		$join_TranfersN = array(
			'LEFT JOIN tblitems ON tblitems.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_by_TranfersN = 'order by id_items asc';
		$result_TranfersN = data_tables_init_nolimt($aColumns_TranfersN, $sIndexColumn_TranfersN, $sTable_TranfersN, $join_TranfersN, $where_TranfersN, array('tblitems.name', 'tblitems.code  as code_items,tbltransfer_warehouse.id as id_main,tblitems.id as product_id'));
		$output_TranfersN = $result_TranfersN['output'];
		$rResult_TranfersN = $result_TranfersN['rResult'];
		//Chuyển kho: di
		$select_TranfersD = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltransfer_warehouse_detail.quantity_net as export_quantity',
			'7 as exists_quantity'
		);
		$where_TranfersD = array(
			'AND tbltransfer_warehouse_detail.warehouses_id =' . $id,
			'AND tbltransfer_warehouse_detail.type = "items"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersD = $select_TranfersD;
		$sIndexColumn_TranfersD = "id";
		$sTable_TranfersD = 'tbltransfer_warehouse_detail';
		$join_TranfersD = array(
			'LEFT JOIN tblitems ON tblitems.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_by_TranfersD = 'order by id_items asc';
		$result_TranfersD = data_tables_init_nolimt($aColumns_TranfersD, $sIndexColumn_TranfersD, $sTable_TranfersD, $join_TranfersD, $where_TranfersD, array('tblitems.name', 'tblitems.code  as code_items,tbltransfer_warehouse.id as id_main,tblitems.id as product_id'));
		$output_TranfersD = $result_TranfersD['output'];
		$rResult_TranfersD = $result_TranfersD['rResult'];
		//Xuất kho khác
		$select_different = array(
			'tblexport_different.warehouseman_date as warehouseman_date',
			'tblexport_different.date as date',
			'concat(tblexport_different.prefix,"-",tblexport_different.code) as code',
			'tblexport_different.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltblexport_different_items.quantity_net as export_quantity',
			'15 as exists_quantity'
		);
		$where_different = array(
			'AND tbltblexport_different_items.warehouses_id =' . $id,
			'AND tbltblexport_different_items.type = "items"',
			'AND tblexport_different.warehouseman_id != 0',
		);
		$aColumns_different = $select_different;
		$sIndexColumn_different = "id";
		$sTable_different = 'tbltblexport_different_items';
		$join_different = array(
			'LEFT JOIN tblitems ON tblitems.id = tbltblexport_different_items.product_id',
			'LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different',
			'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
		);
		$order_by_different = 'order by item_id asc';
		$result_different = data_tables_init_nolimt($aColumns_different, $sIndexColumn_different, $sTable_different, $join_different, $where_different, array('tblitems.name', 'tblitems.code  as code_items,tblexport_different.id as id_main,tblitems.id as product_id'));
		$output_different = $result_different['output'];
		$rResult_different = $result_different['rResult'];
		$aColumnsG = array(
			'warehouseman_date',
			'date',
			'code',
			'reason',
			'unit',
			'import_quantity',
			'export_quantity',
			'exists_quantity'
		);
		$rResultG = array();
		if (!empty($rResultimport)) {
			$rResultG = array_merge($rResultG, $rResultimport);
		}
		if (!empty($rResultexport)) {
			$rResultG = array_merge($rResultG, $rResultexport);
		}
		if (!empty($rResult_return)) {
			$rResultG = array_merge($rResultG, $rResult_return);
		}
		if (!empty($rResult_adjustedG)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedG);
		}
		if (!empty($rResult_adjustedT)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedT);
		}
		if (!empty($rResult_TranfersN)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersN);
		}
		if (!empty($rResult_TranfersD)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersD);
		}
		if (!empty($rResult_different)) {
			$rResultG = array_merge($rResultG, $rResult_different);
		}
		if (!empty($rResultG)) {
			usort($rResultG, ch_make_cmp(['product_id' => "desc", 'warehouseman_date' => "asc", 'exists_quantity' => "asc"]));
		}
		$outputG = $outputimport;
		$outputG['iTotalRecords'] = $outputimport['iTotalRecords'] + $outputexport['iTotalRecords'] + $output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords'] + $output_TranfersN['iTotalRecords'] + $output_adjustedT['iTotalRecords'] + $output_TranfersD['iTotalRecords'] + $output_different['iTotalRecords'];
		$outputG['iTotalDisplayRecords'] = $outputimport['iTotalDisplayRecords'] + $outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'] + $output_different['iTotalDisplayRecords'];
		$currentPage = $this->input->post('start');
		$sumFExistsQ = 0;
		// $row= array();
		foreach ($rResultG as $key => $aRow) {
			if ($key == 0) {
				$date = $aRow['product_id'];
				$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
				$row = array(
					$aRow['name'] . ' (' . $aRow['code_items'] . ')',
					'',
					'',
					'',
					'',
					'',
					'',
					'TỔNG TỒN: ' . formatNumber($sumExistsQall)
				);
				$row['DT_RowClass'] = 'alert-header bold warning';
				for ($i = 0; $i < count($aColumnsG); $i++) {
					$row[] = "";
				}
				$outputG['aaData'][] = $row;
			} else {
				if ($aRow['product_id'] != $date) {
					$date = $aRow['product_id'];
					$sumFExistsQ = 0;
					$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
					$row = array(
						$aRow['name'] . ' (' . $aRow['code_items'] . ')',
						'',
						'',
						'',
						'',
						'',
						'',
						'TỔNG TỒN: ' . formatNumber($sumExistsQall)
					);
					$row['DT_RowClass'] = 'alert-header bold warning';
					for ($i = 0; $i < count($aColumnsG); $i++) {
						$row[] = "";
					}
					$outputG['aaData'][] = $row;
				}
			}
			$row = [];
			for ($i = 0; $i < count($aColumnsG); $i++) {
				if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
					$_data = $aRow[strafter($aColumnsG[$i], 'as ')];
				} else {
					$_data = $aRow[$aColumnsG[$i]];
				}
				if ($aColumnsG[$i] == 'date') {
					$_data = '<div class="text-center">' . _d($aRow['date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'warehouseman_date') {
					$_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'import_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['import_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'export_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['export_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'unit') {
					$_data = '<div class="text-center">' . $aRow['unit'] . '<div>';
				}
				if ($aColumnsG[$i] == 'exists_quantity') {
					$sumFExistsQ += $aRow['import_quantity'] - $aRow['export_quantity'];
					$_data = '<div class="text-center">' . formatNumber($sumFExistsQ) . '<div>';
				}
				if ($aColumnsG[$i] == 'code') {
					if ($aRow['exists_quantity'] == 1) {
						$_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-warning">' . _l('ch_importss') . '</span>';
					} elseif ($aRow['exists_quantity'] == 2) {
						$_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_export_warehouse/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('tnh_export_warehouse_sales') . '</span>';
					} elseif ($aRow['exists_quantity'] == 3) {
						$_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
					} elseif ($aRow['exists_quantity'] == 4) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
					} elseif ($aRow['exists_quantity'] == 5) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedT') . '</span>';
					} elseif ($aRow['exists_quantity'] == 6) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_N') . '</span>';
					} elseif ($aRow['exists_quantity'] == 7) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
					} elseif ($aRow['exists_quantity'] == 15) {
						$_data = '<a href="#" onclick="view_export_different(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_export_different') . '</span>';
					}
				}
				$row[] = $_data;
			}
			$outputG['aaData'][] = $row;
		}
		echo json_encode($outputG);
	}

	public function warehouse_items_deal_product($id = '')
	{
		//Nhập kho
		$selectimport = array(
			'tblimport.warehouseman_date as warehouseman_date',
			'tblimport.date as date',
			'concat(tblimport.prefix,"-",tblimport.code) as code',
			'tblimport.note as reason',
			'tblunits.unit as unit',
			'tblimport_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'1 as exists_quantity'
		);
		$whereimport = array(
			'AND tblimport.warehouse_id =' . $id,
			'AND tblimport_items.type = "product"',
			'AND tblimport.warehouseman_id != 0',
		);
		$aColumnsimport = $selectimport;
		$sIndexColumnimport = "id";
		$sTableimport = 'tblimport_items';
		$joinimport = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tblimport_items.product_id',
			'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_byimport = 'order by product_id asc';
		$resultimport = data_tables_init_nolimt($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tbl_products.name', 'tbl_products.code  as code_items,tblimport.id as id_main,tbl_products.id as product_id'));
		$outputimport = $resultimport['output'];
		$rResultimport = $resultimport['rResult'];
		//Xuất kho
		$selectexport = array(
			'tbl_export_warehouses.date_warehouseman as warehouseman_date',
			'tbl_export_warehouses.date as date',
			'tbl_export_warehouses.reference_no as code',
			'tbl_export_warehouses.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbl_export_warehous_items.quantity as export_quantity',
			'2 as exists_quantity'
		);
		$whereexport = array(
			'AND tbl_export_warehous_items.warehouse_id =' . $id,
			'AND tbl_export_warehous_items.type_item = "products"',
			'AND tbl_export_warehouses.warehouseman_id != 0',
		);
		$aColumnsexport = $selectexport;
		$sIndexColumnexport = "id";
		$sTableexport = 'tbl_export_warehous_items';
		$joinexport = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbl_export_warehous_items.item_id',
			'LEFT JOIN tbl_export_warehouses ON tbl_export_warehouses.id = tbl_export_warehous_items.export_warehouse_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_byexport = 'order by item_id asc';
		$resultexport = data_tables_init_nolimt($aColumnsexport, $sIndexColumnexport, $sTableexport, $joinexport, $whereexport, array('tbl_products.name', 'tbl_products.code  as code_items,tbl_export_warehouses.id as id_main,tbl_products.id as product_id'));
		$outputexport = $resultexport['output'];
		$rResultexport = $resultexport['rResult'];
		//trả hàng NCC
		$select_return = array(
			'tblreturn_suppliers.data_warehouseman as warehouseman_date',
			'tblreturn_suppliers.date as date',
			'concat(tblreturn_suppliers.prefix,"",tblreturn_suppliers.code) as code',
			'tblreturn_suppliers.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tblreturn_suppliers_items.quantity_net as export_quantity',
			'3 as exists_quantity'
		);
		$where_return = array(
			'AND tblreturn_suppliers.warehouse_id =' . $id,
			'AND tblreturn_suppliers_items.type = "product"',
			'AND tblreturn_suppliers.warehouseman_id != 0',
		);
		$aColumns_return = $select_return;
		$sIndexColumn_return = "id";
		$sTable_return = 'tblreturn_suppliers_items';
		$join_return = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tblreturn_suppliers_items.product_id',
			'LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_return = 'order by product_id asc';
		$result_return = data_tables_init_nolimt($aColumns_return, $sIndexColumn_return, $sTable_return, $join_return, $where_return, array('tbl_products.name', 'tbl_products.code  as code_items,tblreturn_suppliers.id as id_main,tbl_products.id as product_id'));
		$output_return = $result_return['output'];
		$rResult_return = $result_return['rResult'];
		//Điều chỉnh kho giảm
		$select_adjustedG = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbladjusted_items.quantity_net as export_quantity',
			'4 as exists_quantity'
		);
		$where_adjustedG = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "product"',
			'AND tbladjusted.type = 2',
		);
		$aColumns_adjustedG = $select_adjustedG;
		$sIndexColumn_adjustedG = "id";
		$sTable_adjustedG = 'tbladjusted_items';
		$join_adjustedG = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_adjustedG = 'order by product_id asc';
		$result_adjustedG = data_tables_init_nolimt($aColumns_adjustedG, $sIndexColumn_adjustedG, $sTable_adjustedG, $join_adjustedG, $where_adjustedG, array('tbl_products.name', 'tbl_products.code  as code_items,tbladjusted.id as id_main,tbl_products.id as product_id'));
		$output_adjustedG = $result_adjustedG['output'];
		$rResult_adjustedG = $result_adjustedG['rResult'];
		//Điều chỉnh kho tăng
		$select_adjustedT = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'tbladjusted_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'5 as exists_quantity'
		);
		$where_adjustedT = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "product"',
			'AND tbladjusted.type = 1',
		);
		$aColumns_adjustedT = $select_adjustedT;
		$sIndexColumn_adjustedT = "id";
		$sTable_adjustedT = 'tbladjusted_items';
		$join_adjustedT = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_adjustedT = 'order by product_id asc';
		$result_adjustedT = data_tables_init_nolimt($aColumns_adjustedT, $sIndexColumn_adjustedT, $sTable_adjustedT, $join_adjustedT, $where_adjustedT, array('tbl_products.name', 'tbl_products.code  as code_items,tbladjusted.id as id_main,tbl_products.id as product_id'));
		$output_adjustedT = $result_adjustedT['output'];
		$rResult_adjustedT = $result_adjustedT['rResult'];
		//Chuyển kho: nhận
		$select_TranfersN = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'tbltransfer_warehouse_detail.quantity_net as import_quantity',
			'0 as export_quantity',
			'6 as exists_quantity'
		);
		$where_TranfersN = array(
			'AND tbltransfer_warehouse_detail.warehouses_to =' . $id,
			'AND tbltransfer_warehouse_detail.type = "product"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersN = $select_TranfersN;
		$sIndexColumn_TranfersN = "id";
		$sTable_TranfersN = 'tbltransfer_warehouse_detail';
		$join_TranfersN = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_TranfersN = 'order by id_items asc';
		$result_TranfersN = data_tables_init_nolimt($aColumns_TranfersN, $sIndexColumn_TranfersN, $sTable_TranfersN, $join_TranfersN, $where_TranfersN, array('tbl_products.name', 'tbl_products.code  as code_items,tbltransfer_warehouse.id as id_main,tbl_products.id as product_id'));
		$output_TranfersN = $result_TranfersN['output'];
		$rResult_TranfersN = $result_TranfersN['rResult'];
		//Chuyển kho: di
		$select_TranfersD = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltransfer_warehouse_detail.quantity_net as export_quantity',
			'7 as exists_quantity'
		);
		$where_TranfersD = array(
			'AND tbltransfer_warehouse_detail.warehouses_id =' . $id,
			'AND tbltransfer_warehouse_detail.type = "product"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersD = $select_TranfersD;
		$sIndexColumn_TranfersD = "id";
		$sTable_TranfersD = 'tbltransfer_warehouse_detail';
		$join_TranfersD = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_TranfersD = 'order by id_items asc';
		$result_TranfersD = data_tables_init_nolimt($aColumns_TranfersD, $sIndexColumn_TranfersD, $sTable_TranfersD, $join_TranfersD, $where_TranfersD, array('tbl_products.name', 'tbl_products.code  as code_items,tbltransfer_warehouse.id as id_main,tbl_products.id as product_id'));
		$output_TranfersD = $result_TranfersD['output'];
		$rResult_TranfersD = $result_TranfersD['rResult'];
		//Nhập kho thành phẩm
		$select_import_tp = array(
			'tbl_purchase_products.date_warehouseman as warehouseman_date',
			'tbl_purchase_products.date as date',
			'tbl_purchase_products.reference_no as code',
			'tbl_purchase_products.note as reason',
			'tblunits.unit as unit',
			'tbl_purchase_product_items.quantity as import_quantity',
			'0 as export_quantity',
			'8 as exists_quantity'
		);
		$where_import_tp = array(
			'AND tbl_purchase_products.warehouse_id =' . $id,
			'AND tbl_purchase_product_items.type_item = "products"',
			'AND tbl_purchase_products.warehouseman_id != 0',
		);
		$aColumns_import_tp = $select_import_tp;
		$sIndexColumn_import_tp = "id";
		$sTable_import_tp = 'tbl_purchase_product_items';
		$join_import_tp = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbl_purchase_product_items.item_id',
			'LEFT JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_import_tp = 'order by item_id asc';
		$result_import_tp = data_tables_init_nolimt($aColumns_import_tp, $sIndexColumn_import_tp, $sTable_import_tp, $join_import_tp, $where_import_tp, array('tbl_products.name', 'tbl_products.code  as code_items,tbl_purchase_products.id as id_main,tbl_products.id as product_id'));
		$output_import_tp = $result_import_tp['output'];
		$rResult_import_tp = $result_import_tp['rResult'];
		//Xuất kho khác
		$select_different = array(
			'tblexport_different.warehouseman_date as warehouseman_date',
			'tblexport_different.date as date',
			'concat(tblexport_different.prefix,"-",tblexport_different.code) as code',
			'tblexport_different.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltblexport_different_items.quantity_net as export_quantity',
			'15 as exists_quantity'
		);
		$where_different = array(
			'AND tbltblexport_different_items.warehouses_id =' . $id,
			'AND tbltblexport_different_items.type = "product"',
			'AND tblexport_different.warehouseman_id != 0',
		);
		$aColumns_different = $select_different;
		$sIndexColumn_different = "id";
		$sTable_different = 'tbltblexport_different_items';
		$join_different = array(
			'LEFT JOIN tbl_products ON tbl_products.id = tbltblexport_different_items.product_id',
			'LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
		);
		$order_by_different = 'order by item_id asc';
		$result_different = data_tables_init_nolimt($aColumns_different, $sIndexColumn_different, $sTable_different, $join_different, $where_different, array('tbl_products.name', 'tbl_products.code  as code_items,tblexport_different.id as id_main,tbl_products.id as product_id'));
		$output_different = $result_different['output'];
		$rResult_different = $result_different['rResult'];
		$aColumnsG = array(
			'warehouseman_date',
			'date',
			'code',
			'reason',
			'unit',
			'import_quantity',
			'export_quantity',
			'exists_quantity'
		);
		$rResultG = array();
		if (!empty($rResultimport)) {
			$rResultG = array_merge($rResultG, $rResultimport);
		}
		if (!empty($rResultexport)) {
			$rResultG = array_merge($rResultG, $rResultexport);
		}
		if (!empty($rResult_return)) {
			$rResultG = array_merge($rResultG, $rResult_return);
		}
		if (!empty($rResult_adjustedG)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedG);
		}
		if (!empty($rResult_adjustedT)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedT);
		}
		if (!empty($rResult_TranfersN)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersN);
		}
		if (!empty($rResult_TranfersD)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersD);
		}
		if (!empty($rResult_import_tp)) {
			$rResultG = array_merge($rResultG, $rResult_import_tp);
		}
		if (!empty($rResult_different)) {
			$rResultG = array_merge($rResultG, $rResult_different);
		}
		if (!empty($rResultG)) {
			usort($rResultG, ch_make_cmp(['product_id' => "desc", 'warehouseman_date' => "asc", 'exists_quantity' => "asc"]));
		}
		$outputG = $outputimport;
		$outputG['iTotalRecords'] = $outputimport['iTotalRecords'] + $outputexport['iTotalRecords'] + $output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords'] + $output_TranfersN['iTotalRecords'] + $output_adjustedT['iTotalRecords'] + $output_TranfersD['iTotalRecords'] + $output_import_tp['iTotalRecords'] + $output_different['iTotalRecords'];
		$outputG['iTotalDisplayRecords'] = $outputimport['iTotalDisplayRecords'] + $outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'] + $output_import_tp['iTotalDisplayRecords'] + $output_different['iTotalDisplayRecords'];
		$currentPage = $this->input->post('start');
		$sumFExistsQ = 0;
		// $row= array();
		foreach ($rResultG as $key => $aRow) {
			if ($key == 0) {
				$date = $aRow['product_id'];
				$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
				$row = array(
					$aRow['name'] . ' (' . $aRow['code_items'] . ')',
					'',
					'',
					'',
					'',
					'',
					'',
					'TỔNG TỒN: ' . formatNumber($sumExistsQall)
				);
				$row['DT_RowClass'] = 'alert-header bold warning';
				for ($i = 0; $i < count($aColumnsG); $i++) {
					$row[] = "";
				}
				$outputG['aaData'][] = $row;
			} else {
				if ($aRow['product_id'] != $date) {
					$date = $aRow['product_id'];
					$sumFExistsQ = 0;
					$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
					$row = array(
						$aRow['name'] . ' (' . $aRow['code_items'] . ')',
						'',
						'',
						'',
						'',
						'',
						'',
						'TỔNG TỒN: ' . formatNumber($sumExistsQall)
					);
					$row['DT_RowClass'] = 'alert-header bold warning';
					for ($i = 0; $i < count($aColumnsG); $i++) {
						$row[] = "";
					}
					$outputG['aaData'][] = $row;
				}
			}
			$row = [];
			for ($i = 0; $i < count($aColumnsG); $i++) {
				if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
					$_data = $aRow[strafter($aColumnsG[$i], 'as ')];
				} else {
					$_data = $aRow[$aColumnsG[$i]];
				}
				if ($aColumnsG[$i] == 'date') {
					$_data = '<div class="text-center">' . _d($aRow['date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'warehouseman_date') {
					$_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'import_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['import_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'export_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['export_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'unit') {
					$_data = '<div class="text-center">' . $aRow['unit'] . '<div>';
				}
				if ($aColumnsG[$i] == 'exists_quantity') {
					$sumFExistsQ += $aRow['import_quantity'] - $aRow['export_quantity'];
					$_data = '<div class="text-center">' . formatNumber($sumFExistsQ) . '<div>';
				}
				if ($aColumnsG[$i] == 'code') {
					if ($aRow['exists_quantity'] == 1) {
						$_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-warning">' . _l('ch_importss') . '</span>';
					} elseif ($aRow['exists_quantity'] == 2) {
						$_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_export_warehouse/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('tnh_export_warehouse_sales') . '</span>';
					} elseif ($aRow['exists_quantity'] == 3) {
						$_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
					} elseif ($aRow['exists_quantity'] == 4) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
					} elseif ($aRow['exists_quantity'] == 5) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedT') . '</span>';
					} elseif ($aRow['exists_quantity'] == 6) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_N') . '</span>';
					} elseif ($aRow['exists_quantity'] == 7) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
					} elseif ($aRow['exists_quantity'] == 15) {
						$_data = '<a href="#" onclick="view_export_different(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_export_different') . '</span>';
					} else {
						$_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('stock/view_purchase_product/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-success">' . _l('purchase_products') . '</span>';
					}
				}
				$row[] = $_data;
			}
			$outputG['aaData'][] = $row;
		}
		echo json_encode($outputG);
	}

	public function warehouse_items_deal_nvl($id = '')
	{
		//Nhập kho
		$selectimport = array(
			'tblimport.warehouseman_date as warehouseman_date',
			'tblimport.date as date',
			'concat(tblimport.prefix,"-",tblimport.code) as code',
			'tblimport.note as reason',
			'tblunits.unit as unit',
			'tblimport_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'1 as exists_quantity'
		);
		$whereimport = array(
			'AND tblimport.warehouse_id =' . $id,
			'AND tblimport_items.type = "nvl"',
			'AND tblimport.warehouseman_id != 0',
		);
		$aColumnsimport = $selectimport;
		$sIndexColumnimport = "id";
		$sTableimport = 'tblimport_items';
		$joinimport = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tblimport_items.product_id',
			'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_byimport = 'order by product_id asc';
		$resultimport = data_tables_init_nolimt($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tbl_materials.name', 'tbl_materials.code  as code_items,tblimport.id as id_main,tbl_materials.id as product_id'));
		$outputimport = $resultimport['output'];
		$rResultimport = $resultimport['rResult'];
		//Xuất kho
		$selectexport = array(
			'tbl_export_warehouses.date_warehouseman as warehouseman_date',
			'tbl_export_warehouses.date as date',
			'tbl_export_warehouses.reference_no as code',
			'tbl_export_warehouses.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbl_export_warehous_items.quantity as export_quantity',
			'2 as exists_quantity'
		);
		$whereexport = array(
			'AND tbl_export_warehous_items.warehouse_id =' . $id,
			'AND tbl_export_warehous_items.type_item = "nvl"',
			'AND tbl_export_warehouses.warehouseman_id != 0',
		);
		$aColumnsexport = $selectexport;
		$sIndexColumnexport = "id";
		$sTableexport = 'tbl_export_warehous_items';
		$joinexport = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_export_warehous_items.item_id',
			'LEFT JOIN tbl_export_warehouses ON tbl_export_warehouses.id = tbl_export_warehous_items.export_warehouse_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_byexport = 'order by item_id asc';
		$resultexport = data_tables_init_nolimt($aColumnsexport, $sIndexColumnexport, $sTableexport, $joinexport, $whereexport, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbl_export_warehouses.id as id_main,tbl_materials.id as product_id'));
		$outputexport = $resultexport['output'];
		$rResultexport = $resultexport['rResult'];
		//trả hàng NCC
		$select_return = array(
			'tblreturn_suppliers.data_warehouseman as warehouseman_date',
			'tblreturn_suppliers.date as date',
			'concat(tblreturn_suppliers.prefix,"",tblreturn_suppliers.code) as code',
			'tblreturn_suppliers.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tblreturn_suppliers_items.quantity_net as export_quantity',
			'3 as exists_quantity'
		);
		$where_return = array(
			'AND tblreturn_suppliers.warehouse_id =' . $id,
			'AND tblreturn_suppliers_items.type = "nvl"',
			'AND tblreturn_suppliers.warehouseman_id != 0',
		);
		$aColumns_return = $select_return;
		$sIndexColumn_return = "id";
		$sTable_return = 'tblreturn_suppliers_items';
		$join_return = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tblreturn_suppliers_items.product_id',
			'LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_return = 'order by product_id asc';
		$result_return = data_tables_init_nolimt($aColumns_return, $sIndexColumn_return, $sTable_return, $join_return, $where_return, array('tbl_materials.name', 'tbl_materials.code  as code_items,tblreturn_suppliers.id as id_main,tbl_materials.id as product_id'));
		$output_return = $result_return['output'];
		$rResult_return = $result_return['rResult'];
		//Điều chỉnh kho giảm
		$select_adjustedG = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbladjusted_items.quantity_net as export_quantity',
			'4 as exists_quantity'
		);
		$where_adjustedG = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "nvl"',
			'AND tbladjusted.type = 2',
		);
		$aColumns_adjustedG = $select_adjustedG;
		$sIndexColumn_adjustedG = "id";
		$sTable_adjustedG = 'tbladjusted_items';
		$join_adjustedG = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_adjustedG = 'order by product_id asc';
		$result_adjustedG = data_tables_init_nolimt($aColumns_adjustedG, $sIndexColumn_adjustedG, $sTable_adjustedG, $join_adjustedG, $where_adjustedG, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbladjusted.id as id_main,tbl_materials.id as product_id'));
		$output_adjustedG = $result_adjustedG['output'];
		$rResult_adjustedG = $result_adjustedG['rResult'];
		//Điều chỉnh kho tăng
		$select_adjustedT = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'tbladjusted_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'5 as exists_quantity'
		);
		$where_adjustedT = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "nvl"',
			'AND tbladjusted.type = 1',
		);
		$aColumns_adjustedT = $select_adjustedT;
		$sIndexColumn_adjustedT = "id";
		$sTable_adjustedT = 'tbladjusted_items';
		$join_adjustedT = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_adjustedT = 'order by product_id asc';
		$result_adjustedT = data_tables_init_nolimt($aColumns_adjustedT, $sIndexColumn_adjustedT, $sTable_adjustedT, $join_adjustedT, $where_adjustedT, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbladjusted.id as id_main,tbl_materials.id as product_id'));
		$output_adjustedT = $result_adjustedT['output'];
		$rResult_adjustedT = $result_adjustedT['rResult'];
		//Chuyển kho: nhận
		$select_TranfersN = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'tbltransfer_warehouse_detail.quantity_net as import_quantity',
			'0 as export_quantity',
			'6 as exists_quantity'
		);
		$where_TranfersN = array(
			'AND tbltransfer_warehouse_detail.warehouses_to =' . $id,
			'AND tbltransfer_warehouse_detail.type = "nvl"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersN = $select_TranfersN;
		$sIndexColumn_TranfersN = "id";
		$sTable_TranfersN = 'tbltransfer_warehouse_detail';
		$join_TranfersN = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_TranfersN = 'order by id_items asc';
		$result_TranfersN = data_tables_init_nolimt($aColumns_TranfersN, $sIndexColumn_TranfersN, $sTable_TranfersN, $join_TranfersN, $where_TranfersN, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbltransfer_warehouse.id as id_main,tbl_materials.id as product_id'));
		$output_TranfersN = $result_TranfersN['output'];
		$rResult_TranfersN = $result_TranfersN['rResult'];
		//Chuyển kho: di
		$select_TranfersD = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltransfer_warehouse_detail.quantity_net as export_quantity',
			'7 as exists_quantity'
		);
		$where_TranfersD = array(
			'AND tbltransfer_warehouse_detail.warehouses_id =' . $id,
			'AND tbltransfer_warehouse_detail.type = "nvl"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersD = $select_TranfersD;
		$sIndexColumn_TranfersD = "id";
		$sTable_TranfersD = 'tbltransfer_warehouse_detail';
		$join_TranfersD = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_TranfersD = 'order by id_items asc';
		$result_TranfersD = data_tables_init_nolimt($aColumns_TranfersD, $sIndexColumn_TranfersD, $sTable_TranfersD, $join_TranfersD, $where_TranfersD, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbltransfer_warehouse.id as id_main,tbl_materials.id as product_id'));
		$output_TranfersD = $result_TranfersD['output'];
		$rResult_TranfersD = $result_TranfersD['rResult'];
		//Xuất kho sản xuất
		$select_exportsx = array(
			'tbl_suggest_exporting.date_warehouseman as warehouseman_date',
			'tbl_suggest_exporting.date as date',
			'tbl_suggest_exporting.reference_stock as code',
			'tbl_suggest_exporting.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbl_suggest_exporting_items.quantity_exchange as export_quantity',
			'8 as exists_quantity'
		);
		$where_exportsx = array(
			'AND tbl_suggest_exporting.warehouse_id =' . $id,
			'AND tbl_suggest_exporting_items.type_item = "materials"',
			'AND tbl_suggest_exporting.status_stock is not NULL',
			'AND tbl_suggest_exporting.warehouseman_id != 0',
		);
		$aColumns_exportsx = $select_exportsx;
		$sIndexColumn_exportsx = "id";
		$sTable_exportsx = 'tbl_suggest_exporting_items';
		$join_exportsx = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_suggest_exporting_items.item_id',
			'LEFT JOIN tbl_suggest_exporting ON tbl_suggest_exporting.id = tbl_suggest_exporting_items.suggest_exporting_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_exportsx = 'order by item_id asc';
		$result_exportsx = data_tables_init_nolimt($aColumns_exportsx, $sIndexColumn_exportsx, $sTable_exportsx, $join_exportsx, $where_exportsx, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbl_suggest_exporting.id as id_main,tbl_materials.id as product_id'));
		$output_exportsx = $result_exportsx['output'];
		$rResult_exportsx = $result_exportsx['rResult'];
		//Xuất kho khác
		$select_different = array(
			'tblexport_different.warehouseman_date as warehouseman_date',
			'tblexport_different.date as date',
			'concat(tblexport_different.prefix,"-",tblexport_different.code) as code',
			'tblexport_different.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltblexport_different_items.quantity_net as export_quantity',
			'15 as exists_quantity'
		);
		$where_different = array(
			'AND tbltblexport_different_items.warehouses_id =' . $id,
			'AND tbltblexport_different_items.type = "nvl"',
			'AND tblexport_different.warehouseman_id != 0',
		);
		$aColumns_different = $select_different;
		$sIndexColumn_different = "id";
		$sTable_different = 'tbltblexport_different_items';
		$join_different = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbltblexport_different_items.product_id',
			'LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_different = 'order by item_id asc';
		$result_different = data_tables_init_nolimt($aColumns_different, $sIndexColumn_different, $sTable_different, $join_different, $where_different, array('tbl_materials.name', 'tbl_materials.code  as code_items,tblexport_different.id as id_main,tbl_materials.id as product_id'));
		$output_different = $result_different['output'];
		$rResult_different = $result_different['rResult'];
		//Nhập kho phe lieu
		$selecti_internal = array(
			'tbl_purchase_internal.date_warehouseman as warehouseman_date',
			'tbl_purchase_internal.date as date',
			'reference_no as code',
			'tbl_purchase_internal.note as reason',
			'tblunits.unit as unit',
			'tbl_purchase_internal_items.quantity as import_quantity',
			'0 as export_quantity',
			'16 as exists_quantity'
		);
		$where_internal = array(
			'AND tbl_purchase_internal.warehouse_id =' . $id,
			'AND tbl_purchase_internal.warehouseman_id != 0',
		);
		$aColumns_internal = $selecti_internal;
		$sIndexColumn_internal = "id";
		$sTable_internal = 'tbl_purchase_internal_items';
		$join_internal = array(
			'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_purchase_internal_items.item_id',
			'LEFT JOIN tbl_purchase_internal ON tbl_purchase_internal.id = tbl_purchase_internal_items.purchase_internal_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id',
		);
		$order_by_internal = 'order by item_id asc';
		$result_internal = data_tables_init_nolimt($aColumns_internal, $sIndexColumn_internal, $sTable_internal, $join_internal, $where_internal, array('tbl_materials.name', 'tbl_materials.code  as code_items,tbl_purchase_internal.id as id_main,tbl_materials.id as product_id'));
		$output_internal = $result_internal['output'];
		$rResult_internal = $result_internal['rResult'];
		$aColumnsG = array(
			'warehouseman_date',
			'date',
			'code',
			'reason',
			'unit',
			'import_quantity',
			'export_quantity',
			'exists_quantity'
		);
		$rResultG = array();
		if (!empty($rResultimport)) {
			$rResultG = array_merge($rResultG, $rResultimport);
		}
		if (!empty($rResultexport)) {
			$rResultG = array_merge($rResultG, $rResultexport);
		}
		if (!empty($rResult_return)) {
			$rResultG = array_merge($rResultG, $rResult_return);
		}
		if (!empty($rResult_adjustedG)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedG);
		}
		if (!empty($rResult_adjustedT)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedT);
		}
		if (!empty($rResult_TranfersN)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersN);
		}
		if (!empty($rResult_TranfersD)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersD);
		}
		if (!empty($rResult_exportsx)) {
			$rResultG = array_merge($rResultG, $rResult_exportsx);
		}
		if (!empty($rResult_different)) {
			$rResultG = array_merge($rResultG, $rResult_different);
		}
		if (!empty($rResult_internal)) {
			$rResultG = array_merge($rResultG, $rResult_internal);
		}
		if (!empty($rResultG)) {
			usort($rResultG, ch_make_cmp(['product_id' => "desc", 'warehouseman_date' => "asc", 'exists_quantity' => "asc"]));
		}
		$outputG = $outputimport;
		$outputG['iTotalRecords'] = $outputimport['iTotalRecords'] + $outputexport['iTotalRecords'] + $output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords'] + $output_TranfersN['iTotalRecords'] + $output_adjustedT['iTotalRecords'] + $output_TranfersD['iTotalRecords'] + $output_exportsx['iTotalRecords'] + $output_different['iTotalRecords'] + $output_internal['iTotalRecords'];
		$outputG['iTotalDisplayRecords'] = $outputimport['iTotalDisplayRecords'] + $outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'] + $output_exportsx['iTotalDisplayRecords'] + $output_different['iTotalDisplayRecords'] + $output_internal['iTotalDisplayRecords'];
		$currentPage = $this->input->post('start');
		$sumFExistsQ = 0;
		// $row= array();
		foreach ($rResultG as $key => $aRow) {
			if ($key == 0) {
				$date = $aRow['product_id'];
				$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
				$row = array(
					$aRow['name'] . ' (' . $aRow['code_items'] . ')',
					'',
					'',
					'',
					'',
					'',
					'',
					'TỔNG TỒN: ' . formatNumber($sumExistsQall)
				);
				$row['DT_RowClass'] = 'alert-header bold warning';
				for ($i = 0; $i < count($aColumnsG); $i++) {
					$row[] = "";
				}
				$outputG['aaData'][] = $row;
			} else {
				if ($aRow['product_id'] != $date) {
					$date = $aRow['product_id'];
					$sumFExistsQ = 0;
					$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
					$row = array(
						$aRow['name'] . ' (' . $aRow['code_items'] . ')',
						'',
						'',
						'',
						'',
						'',
						'',
						'TỔNG TỒN: ' . formatNumber($sumExistsQall)
					);
					$row['DT_RowClass'] = 'alert-header bold warning';
					for ($i = 0; $i < count($aColumnsG); $i++) {
						$row[] = "";
					}
					$outputG['aaData'][] = $row;
				}
			}
			$row = [];
			for ($i = 0; $i < count($aColumnsG); $i++) {
				if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
					$_data = $aRow[strafter($aColumnsG[$i], 'as ')];
				} else {
					$_data = $aRow[$aColumnsG[$i]];
				}
				if ($aColumnsG[$i] == 'date') {
					$_data = '<div class="text-center">' . _d($aRow['date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'warehouseman_date') {
					$_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'import_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['import_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'export_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['export_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'unit') {
					$_data = '<div class="text-center">' . $aRow['unit'] . '<div>';
				}
				if ($aColumnsG[$i] == 'exists_quantity') {
					$sumFExistsQ += $aRow['import_quantity'] - $aRow['export_quantity'];
					$_data = '<div class="text-center">' . formatNumber($sumFExistsQ) . '<div>';
				}
				if ($aColumnsG[$i] == 'code') {
					if ($aRow['exists_quantity'] == 1) {
						$_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-warning">' . _l('ch_importss') . '</span>';
					} elseif ($aRow['exists_quantity'] == 2) {
						$_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_export_warehouse/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('tnh_export_warehouse_sales') . '</span>';
					} elseif ($aRow['exists_quantity'] == 3) {
						$_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
					} elseif ($aRow['exists_quantity'] == 4) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
					} elseif ($aRow['exists_quantity'] == 5) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedT') . '</span>';
					} elseif ($aRow['exists_quantity'] == 6) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_N') . '</span>';
					} elseif ($aRow['exists_quantity'] == 7) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
					} elseif ($aRow['exists_quantity'] == 15) {
						$_data = '<a href="#" onclick="view_export_different(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_export_different') . '</span>';
					} elseif ($aRow['exists_quantity'] == 16) {
						$_data = '<a class="tnh-modal" title="' . _l('purchase_internal') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_purchase_internal/' . $aRow['id_main']) . '">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('purchase_internal') . '</span>';
					} else {
						$_data = '<a class="tnh-modal" title="' . _l('tnh_exporting_stock_producion') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_exporting_production/' . $aRow['id_main']) . '">' . $_data . '</a>  <span class="inline-block label label-success">' . _l('tnh_exporting_stock_producion') . '</span>';
					}
				}
				$row[] = $_data;
			}
			$outputG['aaData'][] = $row;
		}
		echo json_encode($outputG);
	}

	public function warehouse_items_deal_tools($id = '')
	{
		//Nhập kho
		$selectimport = array(
			'tblimport.warehouseman_date as warehouseman_date',
			'tblimport.date as date',
			'concat(tblimport.prefix,"-",tblimport.code) as code',
			'tblimport.note as reason',
			'tblunits.unit as unit',
			'tblimport_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'1 as exists_quantity'
		);
		$whereimport = array(
			'AND tblimport.warehouse_id =' . $id,
			'AND tblimport_items.type = "tools"',
			'AND tblimport.warehouseman_id != 0',
		);
		$aColumnsimport = $selectimport;
		$sIndexColumnimport = "id";
		$sTableimport = 'tblimport_items';
		$joinimport = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tblimport_items.product_id',
			'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_byimport = 'order by product_id asc';
		$resultimport = data_tables_init_nolimt($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tblimport.id as id_main,tbl_tools_supplies.id as product_id'));
		$outputimport = $resultimport['output'];
		$rResultimport = $resultimport['rResult'];
		//Xuất kho
		$selectexport = array(
			'tbl_export_warehouses.date_warehouseman as warehouseman_date',
			'tbl_export_warehouses.date as date',
			'tbl_export_warehouses.reference_no as code',
			'tbl_export_warehouses.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbl_export_warehous_items.quantity as export_quantity',
			'2 as exists_quantity'
		);
		$whereexport = array(
			'AND tbl_export_warehous_items.warehouse_id =' . $id,
			'AND tbl_export_warehous_items.type_item = "tools"',
			'AND tbl_export_warehouses.warehouseman_id != 0',
		);
		$aColumnsexport = $selectexport;
		$sIndexColumnexport = "id";
		$sTableexport = 'tbl_export_warehous_items';
		$joinexport = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbl_export_warehous_items.item_id',
			'LEFT JOIN tbl_export_warehouses ON tbl_export_warehouses.id = tbl_export_warehous_items.export_warehouse_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_byexport = 'order by item_id asc';
		$resultexport = data_tables_init_nolimt($aColumnsexport, $sIndexColumnexport, $sTableexport, $joinexport, $whereexport, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tbl_export_warehouses.id as id_main,tbl_tools_supplies.id as product_id'));
		$outputexport = $resultexport['output'];
		$rResultexport = $resultexport['rResult'];
		//trả hàng NCC
		$select_return = array(
			'tblreturn_suppliers.data_warehouseman as warehouseman_date',
			'tblreturn_suppliers.date as date',
			'concat(tblreturn_suppliers.prefix,"",tblreturn_suppliers.code) as code',
			'tblreturn_suppliers.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tblreturn_suppliers_items.quantity_net as export_quantity',
			'3 as exists_quantity'
		);
		$where_return = array(
			'AND tblreturn_suppliers.warehouse_id =' . $id,
			'AND tblreturn_suppliers_items.type = "tools"',
			'AND tblreturn_suppliers.warehouseman_id != 0',
		);
		$aColumns_return = $select_return;
		$sIndexColumn_return = "id";
		$sTable_return = 'tblreturn_suppliers_items';
		$join_return = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tblreturn_suppliers_items.product_id',
			'LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_return = 'order by product_id asc';
		$result_return = data_tables_init_nolimt($aColumns_return, $sIndexColumn_return, $sTable_return, $join_return, $where_return, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tblreturn_suppliers.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_return = $result_return['output'];
		$rResult_return = $result_return['rResult'];
		//Điều chỉnh kho giảm
		$select_adjustedG = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbladjusted_items.quantity_net as export_quantity',
			'4 as exists_quantity'
		);
		$where_adjustedG = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "tools"',
			'AND tbladjusted.type = 2',
		);
		$aColumns_adjustedG = $select_adjustedG;
		$sIndexColumn_adjustedG = "id";
		$sTable_adjustedG = 'tbladjusted_items';
		$join_adjustedG = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_adjustedG = 'order by product_id asc';
		$result_adjustedG = data_tables_init_nolimt($aColumns_adjustedG, $sIndexColumn_adjustedG, $sTable_adjustedG, $join_adjustedG, $where_adjustedG, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tbladjusted.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_adjustedG = $result_adjustedG['output'];
		$rResult_adjustedG = $result_adjustedG['rResult'];
		//Điều chỉnh kho tăng
		$select_adjustedT = array(
			'tbladjusted.date_create as warehouseman_date',
			'tbladjusted.date as date',
			'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
			'tbladjusted.note as reason',
			'tblunits.unit as unit',
			'tbladjusted_items.quantity_net as import_quantity',
			'0 as export_quantity',
			'5 as exists_quantity'
		);
		$where_adjustedT = array(
			'AND tbladjusted.warehouse_id =' . $id,
			'AND tbladjusted_items.type = "tools"',
			'AND tbladjusted.type = 1',
		);
		$aColumns_adjustedT = $select_adjustedT;
		$sIndexColumn_adjustedT = "id";
		$sTable_adjustedT = 'tbladjusted_items';
		$join_adjustedT = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbladjusted_items.product_id',
			'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_adjustedT = 'order by product_id asc';
		$result_adjustedT = data_tables_init_nolimt($aColumns_adjustedT, $sIndexColumn_adjustedT, $sTable_adjustedT, $join_adjustedT, $where_adjustedT, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tbladjusted.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_adjustedT = $result_adjustedT['output'];
		$rResult_adjustedT = $result_adjustedT['rResult'];
		//Chuyển kho: nhận
		$select_TranfersN = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'tbltransfer_warehouse_detail.quantity_net as import_quantity',
			'0 as export_quantity',
			'6 as exists_quantity'
		);
		$where_TranfersN = array(
			'AND tbltransfer_warehouse_detail.warehouses_to =' . $id,
			'AND tbltransfer_warehouse_detail.type = "tools"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersN = $select_TranfersN;
		$sIndexColumn_TranfersN = "id";
		$sTable_TranfersN = 'tbltransfer_warehouse_detail';
		$join_TranfersN = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_TranfersN = 'order by id_items asc';
		$result_TranfersN = data_tables_init_nolimt($aColumns_TranfersN, $sIndexColumn_TranfersN, $sTable_TranfersN, $join_TranfersN, $where_TranfersN, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tbltransfer_warehouse.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_TranfersN = $result_TranfersN['output'];
		$rResult_TranfersN = $result_TranfersN['rResult'];
		//Chuyển kho: di
		$select_TranfersD = array(
			'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
			'tbltransfer_warehouse.date as date',
			'concat(tbltransfer_warehouse.prefix,"",tbltransfer_warehouse.code) as code',
			'tbltransfer_warehouse.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltransfer_warehouse_detail.quantity_net as export_quantity',
			'7 as exists_quantity'
		);
		$where_TranfersD = array(
			'AND tbltransfer_warehouse_detail.warehouses_id =' . $id,
			'AND tbltransfer_warehouse_detail.type = "tools"',
			'AND tbltransfer_warehouse.warehouseman_id != 0',
		);
		$aColumns_TranfersD = $select_TranfersD;
		$sIndexColumn_TranfersD = "id";
		$sTable_TranfersD = 'tbltransfer_warehouse_detail';
		$join_TranfersD = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbltransfer_warehouse_detail.id_items',
			'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_TranfersD = 'order by id_items asc';
		$result_TranfersD = data_tables_init_nolimt($aColumns_TranfersD, $sIndexColumn_TranfersD, $sTable_TranfersD, $join_TranfersD, $where_TranfersD, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tbltransfer_warehouse.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_TranfersD = $result_TranfersD['output'];
		$rResult_TranfersD = $result_TranfersD['rResult'];
		//Xuất kho sản xuất
		$select_exportsx = array(
			'tbl_suggest_exporting.date_warehouseman as warehouseman_date',
			'tbl_suggest_exporting.date as date',
			'tbl_suggest_exporting.reference_stock as code',
			'tbl_suggest_exporting.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbl_suggest_exporting_items.quantity_exchange as export_quantity',
			'8 as exists_quantity'
		);
		$where_exportsx = array(
			'AND tbl_suggest_exporting.warehouse_id =' . $id,
			'AND tbl_suggest_exporting_items.type_item = "tools_supplies"',
			'AND tbl_suggest_exporting.status_stock is not NULL',
			'AND tbl_suggest_exporting.warehouseman_id != 0',
		);
		$aColumns_exportsx = $select_exportsx;
		$sIndexColumn_exportsx = "id";
		$sTable_exportsx = 'tbl_suggest_exporting_items';
		$join_exportsx = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbl_suggest_exporting_items.item_id',
			'LEFT JOIN tbl_suggest_exporting ON tbl_suggest_exporting.id = tbl_suggest_exporting_items.suggest_exporting_id',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_exportsx = 'order by item_id asc';
		$result_exportsx = data_tables_init_nolimt($aColumns_exportsx, $sIndexColumn_exportsx, $sTable_exportsx, $join_exportsx, $where_exportsx, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tbl_suggest_exporting.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_exportsx = $result_exportsx['output'];
		$rResult_exportsx = $result_exportsx['rResult'];
		//Xuất kho khác
		$select_different = array(
			'tblexport_different.warehouseman_date as warehouseman_date',
			'tblexport_different.date as date',
			'concat(tblexport_different.prefix,"-",tblexport_different.code) as code',
			'tblexport_different.note as reason',
			'tblunits.unit as unit',
			'0 as import_quantity',
			'tbltblexport_different_items.quantity_net as export_quantity',
			'15 as exists_quantity'
		);
		$where_different = array(
			'AND tbltblexport_different_items.warehouses_id =' . $id,
			'AND tbltblexport_different_items.type = "tools"',
			'AND tblexport_different.warehouseman_id != 0',
		);
		$aColumns_different = $select_different;
		$sIndexColumn_different = "id";
		$sTable_different = 'tbltblexport_different_items';
		$join_different = array(
			'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tbltblexport_different_items.product_id',
			'LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different',
			'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',
		);
		$order_by_different = 'order by item_id asc';
		$result_different = data_tables_init_nolimt($aColumns_different, $sIndexColumn_different, $sTable_different, $join_different, $where_different, array('tbl_tools_supplies.name', 'tbl_tools_supplies.code  as code_items,tblexport_different.id as id_main,tbl_tools_supplies.id as product_id'));
		$output_different = $result_different['output'];
		$rResult_different = $result_different['rResult'];
		$aColumnsG = array(
			'warehouseman_date',
			'date',
			'code',
			'reason',
			'unit',
			'import_quantity',
			'export_quantity',
			'exists_quantity'
		);
		$rResultG = array();
		if (!empty($rResultimport)) {
			$rResultG = array_merge($rResultG, $rResultimport);
		}
		if (!empty($rResultexport)) {
			$rResultG = array_merge($rResultG, $rResultexport);
		}
		if (!empty($rResult_return)) {
			$rResultG = array_merge($rResultG, $rResult_return);
		}
		if (!empty($rResult_adjustedG)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedG);
		}
		if (!empty($rResult_adjustedT)) {
			$rResultG = array_merge($rResultG, $rResult_adjustedT);
		}
		if (!empty($rResult_TranfersN)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersN);
		}
		if (!empty($rResult_TranfersD)) {
			$rResultG = array_merge($rResultG, $rResult_TranfersD);
		}
		if (!empty($rResult_exportsx)) {
			$rResultG = array_merge($rResultG, $rResult_exportsx);
		}
		if (!empty($rResult_different)) {
			$rResultG = array_merge($rResultG, $rResult_different);
		}
		if (!empty($rResultG)) {
			usort($rResultG, ch_make_cmp(['product_id' => "desc", 'warehouseman_date' => "asc", 'exists_quantity' => "asc"]));
		}
		$outputG = $outputimport;
		$outputG['iTotalRecords'] = $outputimport['iTotalRecords'] + $outputexport['iTotalRecords'] + $output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords'] + $output_TranfersN['iTotalRecords'] + $output_adjustedT['iTotalRecords'] + $output_TranfersD['iTotalRecords'] + $output_exportsx['iTotalRecords'] + $output_different['iTotalRecords'];
		$outputG['iTotalDisplayRecords'] = $outputimport['iTotalDisplayRecords'] + $outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'] + $output_exportsx['iTotalDisplayRecords'] + $output_different['iTotalDisplayRecords'];
		$currentPage = $this->input->post('start');
		$sumFExistsQ = 0;
		// $row= array();
		foreach ($rResultG as $key => $aRow) {
			if ($key == 0) {
				$date = $aRow['product_id'];
				$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
				$row = array(
					$aRow['name'] . ' (' . $aRow['code_items'] . ')',
					'',
					'',
					'',
					'',
					'',
					'',
					'TỔNG TỒN: ' . formatNumber($sumExistsQall)
				);
				$row['DT_RowClass'] = 'alert-header bold warning';
				for ($i = 0; $i < count($aColumnsG); $i++) {
					$row[] = "";
				}
				$outputG['aaData'][] = $row;
			} else {
				if ($aRow['product_id'] != $date) {
					$date = $aRow['product_id'];
					$sumFExistsQ = 0;
					$sumExistsQall = sumExistsQ_all_ch($aRow['product_id'], $rResultG, $key);
					$row = array(
						$aRow['name'] . ' (' . $aRow['code_items'] . ')',
						'',
						'',
						'',
						'',
						'',
						'',
						'TỔNG TỒN: ' . formatNumber($sumExistsQall)
					);
					$row['DT_RowClass'] = 'alert-header bold warning';
					for ($i = 0; $i < count($aColumnsG); $i++) {
						$row[] = "";
					}
					$outputG['aaData'][] = $row;
				}
			}
			$row = [];
			for ($i = 0; $i < count($aColumnsG); $i++) {
				if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
					$_data = $aRow[strafter($aColumnsG[$i], 'as ')];
				} else {
					$_data = $aRow[$aColumnsG[$i]];
				}
				if ($aColumnsG[$i] == 'date') {
					$_data = '<div class="text-center">' . _dhau($aRow['date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'warehouseman_date') {
					$_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
				}
				if ($aColumnsG[$i] == 'import_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['import_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'export_quantity') {
					$_data = '<div class="text-center">' . formatNumber($aRow['export_quantity']) . '<div>';
				}
				if ($aColumnsG[$i] == 'unit') {
					$_data = '<div class="text-center">' . $aRow['unit'] . '<div>';
				}
				if ($aColumnsG[$i] == 'exists_quantity') {
					$sumFExistsQ += $aRow['import_quantity'] - $aRow['export_quantity'];
					$_data = '<div class="text-center">' . formatNumber($sumFExistsQ) . '<div>';
				}
				if ($aColumnsG[$i] == 'code') {
					if ($aRow['exists_quantity'] == 1) {
						$_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-warning">' . _l('ch_importss') . '</span>';
					} elseif ($aRow['exists_quantity'] == 2) {
						$_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_export_warehouse/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('tnh_export_warehouse_sales') . '</span>';
					} elseif ($aRow['exists_quantity'] == 3) {
						$_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
					} elseif ($aRow['exists_quantity'] == 4) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
					} elseif ($aRow['exists_quantity'] == 5) {
						$_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedT') . '</span>';
					} elseif ($aRow['exists_quantity'] == 6) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_N') . '</span>';
					} elseif ($aRow['exists_quantity'] == 7) {
						$_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
					} elseif ($aRow['exists_quantity'] == 15) {
						$_data = '<a href="#" onclick="view_export_different(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_export_different') . '</span>';
					} else {
						$_data = '<a class="tnh-modal" title="' . _l('tnh_exporting_stock_producion') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_exporting_production/' . $aRow['id_main']) . '">' . $_data . '</a>  <span class="inline-block label label-success">' . _l('tnh_exporting_stock_producion') . '</span>';
					}
				}
				$row[] = $_data;
			}
			$outputG['aaData'][] = $row;
		}
		echo json_encode($outputG);
	}
	public function SearchLocaltion($id = '', $types = '')
	{
		$data = [];
		$search = $this->input->get('term');
		$warehouse = $this->input->get('warehouse');
		$warehouse_name = get_table_where('tblwarehouse', array('id' => $warehouse), '', 'row');
		$this->db->select(
			'id as id,
            tbllocaltion_warehouses.name as text',
			false
		);
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('tbllocaltion_warehouses.name', $search);
			$this->db->group_end();
		}
		$this->db->where('status', 0);
		$this->db->where('warehouse', $warehouse);
		$this->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
		$this->db->order_by('name', 'DESC');
		$this->db->limit(50);
		$items = $this->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($items)) {
			$data['results'][] =
				[
					'text' => $warehouse_name->name,
					'children' => $items
				];
		}
		echo json_encode($data);
		die();
	}
	public function SearchCategory($id = '', $types = '')
	{
		$data = [];
		$search = $this->input->get('term');
		$type = $this->input->get('type');
		if (empty($type)) {
			$type = $types;
		}
		$limit_one = 12;
		$limit_two = 12;
		$limit_three = 12;
		$limit_all = 50;
		if ($type == 'items') {
			$this->db->select(
				'
                    id as id,
                    tblcategories.category as text',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tblcategories.category', $search);
				$this->db->group_end();
			}
			$this->db->order_by('category', 'DESC');
			$this->db->limit(50);
			$items = $this->db->get('tblcategories')->result_array();
			if (!empty($items)) {
				$data['results'][] =
					[
						'text' => _l('Sản phẩm'),
						'children' => $items
					];
			}
		} else
			if ($type == 'product') {
			$this->db->select(
				'
                id as id,
                tbl_category_products.name as text',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tbl_category_products.name', $search);
				$this->db->group_end();
			}
			$this->db->order_by('tbl_category_products.name', 'DESC');
			$this->db->limit(50);
			$product = $this->db->get('tbl_category_products')->result_array();
			if (!empty($product)) {
				$data['results'][] =
					[
						'text' => _l('Bán thành phẩm'),
						'children' => $product
					];
			}
		} elseif ($type == 'nvl') {
			$this->db->select(
				'
                id as id,
                tbl_category_items.name as text',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tbl_category_items.name', $search);
				$this->db->group_end();
			}
			$this->db->order_by('tbl_category_items.name', 'DESC');
			$this->db->limit(50);
			$product = $this->db->get('tbl_category_items')->result_array();
			if (!empty($product)) {
				$data['results'][] =
					[
						'text' => _l('Nguyên vật liệu'),
						'children' => $product
					];
			}
		} elseif ($type == 'tools') {
			$this->db->select(
				'
                id as id,
                tbl_category_tools_supplies.name as text,
                ',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tbl_category_tools_supplies.name', $search);
				$this->db->group_end();
			}
			$this->db->order_by('tbl_category_tools_supplies.name', 'DESC');
			$this->db->limit($limit_two);
			// $this->db->limit(($limit_all - $count_product));
			$tools = $this->db->get('tbl_tools_supplies')->result_array();
			if (!empty($tools)) {
				$data['results'][] =
					[
						'text' => _l('Công cụ - Vật tư'),
						'children' => $tools
					];
			}
		}
		echo json_encode($data);
		die();
	}

	public function GetDate()
	{
		$months_report = $this->input->post('report_months');
		$text = '';
		if ($months_report != '') {
			$custom_date_select = '';
			if (is_numeric($months_report)) {
				// Last month
				if ($months_report == '1') {
					$beginMonth = date('Y-m-01', strtotime('first day of last month'));
					$endMonth = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int)$months_report;
					$months_report--;
					$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
					$endMonth = date('Y-m-t');
				}
			} elseif ($months_report == 'this_month') {
				$beginMonth = date('Y-m-01');
				$endMonth = date('Y-m-t');
			} elseif ($months_report == 'this_year') {
				$beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
				$endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
			} elseif ($months_report == 'last_year') {
				$beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date = to_sql_date($this->input->post('report_to'));
				if ($from_date == $to_date) {
					$beginMonth = $to_date;
					$endMonth = $to_date;
				} else {
					$beginMonth = $from_date;
					$endMonth = $to_date;
				}
			}
			$text = 'Từ ngày ' . _d($beginMonth) . ' Đến ngày ' . _d($endMonth);
		} else {
			$text = 'Từ trước đến nay';
		}
		echo ($text);
	}


	public function export_excel()
	{
		if (!has_permission('warehouse', '', 'export')) {
			access_denied();
		}
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');

		$cloumns = $this->input->post('cloumns');
		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		$style_excel['Background_header_one'] = $style_excel['Background_header'];
		$style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';




		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);

		$numberRow = 1;
		$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'Mã Nhóm')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Tên Nhóm')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Mã Kho')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Tên Kho')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Địa chỉ')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Chi nhánh xưởng')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$numberRow++;


		$this->db->select([
			'tblwarehouse.*',
			'tblgroup_warehouse.code as code_group',
			'tblgroup_warehouse.name as name_group',
			'tblbranch.name as name_branch',
		]);
		$this->db->join('tblgroup_warehouse', 'tblgroup_warehouse.id = tblwarehouse.id_group_warehouse', 'left');
		$this->db->join('tblbranch', 'tblbranch.id = tblwarehouse.id_branch', 'left');
		$data_warehouse = $this->db->get('tblwarehouse')->result_array();
		if (!empty($data_warehouse)) {
			foreach ($data_warehouse as $key => $value) {
				$i = 0;
				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_group'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
				$i++;

				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_group'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
				$i++;

				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
				$i++;

				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
				$i++;

				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['address'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
				$i++;

				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_branch'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
				$i++;
				$numberRow++;
			}
		}


		$filename = lang('Danh_sach_kho_hang') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	public function update_price_warehouse()
	{
		$items = get_table_where('tblwarehouse_product', array('price' => 0, 'type_items' => 'nvl'), '', 'result_array', 'product_id,type_items');
		foreach ($items as $key => $value) {
			$this->db->where('product_id', $value['product_id']);
			$this->db->where('product_type', $value['type_items']);
			$this->db->order_by('id desc');
			$this->db->limit(1);
			$price = $this->db->get('tblsuppliers_price_detail')->row();
			if (!empty($price)) {
				$this->db->where('price', 0);
				$this->db->where('product_id', $value['product_id']);
				$this->db->where('type_items', $value['type_items']);
				$this->db->update('tblwarehouse_product', array('price' => $price->price));
			}
		}
	}
	public function update_price_export()
	{
		$main = get_table_where('tbl_suggest_exporting');
		foreach ($main as $key => $value) {
			$sub_total = 0;
			$items = get_table_where('tbl_suggest_exporting_items', array('suggest_exporting_id' => $value['id']));
			foreach ($items as $kk => $vv) {
				$array = explode('|', $vv['id_import']);
				$amount = 0;
				foreach ($array as $k => $v) {
					if (!empty($v)) {
						$waretos = explode('-', $v);
						$quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
						$price = $quantity_nets->price;
						$quantitys = $waretos[3];
						if ($quantitys == 0) {
							$quantitys = $waretos[1];
						}
						$amount += $quantitys * $price;
						$__data['id_suggest_exporting'] = $value['id'];
						$__data['id_suggest_exporting_items'] = $vv['id'];
						$__data['quantity'] = $quantitys;
						$__data['price'] = $price;
						$this->db->insert('tbl_suggest_exporting_items_pirce', $__data);
					}
				}
				$sub_total += $amount;
				$this->db->update('tbl_suggest_exporting_items', array('amount' => $amount), array('id' => $vv['id']));
			}
			$this->db->update('tbl_suggest_exporting', array('grand_total' => $sub_total, 'check_update' => 1), array('id' => $value['id']));
		}
	}
	public function kiemtraware($warehouse_id = 2)
	{
		$ware = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse_id));
		$array = array();
		$dem = 0;
		foreach ($ware as $key => $value) {
			$ktr = get_table_where_select('COALESCE(SUM(tblwarehouse_product.quantity_left),0) as quantity_left', 'tblwarehouse_product', array('product_id' => $value['id_items'], 'localtion' => $value['localtion'], 'warehouse_id' => $value['warehouse_id'], 'type_items' => $value['type_items'], 'lot_code' => $value['lot_code'], 'date_sx' => $value['date_sx'], 'date_sd' => $value['date_sd'], 'date_use' => $value['date_use']), '', 'row');
			if (empty($ktr)) {
				$array[$dem]['quantity_left'] = $ktr->quantity_left;
				$array[$dem]['product_quantity'] = $value['product_quantity'];
				$array[$dem]['items'] = $value['id_items'];
				$array[$dem]['warehouse'] = $value['warehouse_id'];
				$array[$dem]['localhost'] = $value['localtion'];
				$array[$dem]['series'] = $value['series'];
				$array[$dem]['idd'] = $value['id'];
				$array[$dem]['type_items'] = $value['type_items'];
				$array[$dem]['items'] = get_items($value['id_items'], $value['type_items']);


				$dem++;
			} elseif ($ktr->quantity_left != $value['product_quantity']) {
				$array[$dem]['quantity_left'] = $ktr->quantity_left;
				$array[$dem]['product_quantity'] = $value['product_quantity'];
				$array[$dem]['items'] = $value['id_items'];
				$array[$dem]['warehouse'] = $value['warehouse_id'];
				$array[$dem]['localhost'] = $value['localtion'];
				$array[$dem]['series'] = $value['series'];
				$array[$dem]['idd'] = $value['id'];
				$array[$dem]['type_items'] = $value['type_items'];
				$array[$dem]['items'] = get_items($value['id_items'], $value['type_items']);



				$dem++;
			}
		}

		echo '<pre>';
		var_dump($array);
		die;
	}

	public function excel_import_location()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		$count = 0;
		$data = [];
		if (!empty($_FILES['file'])) {
			$fullfile = $_FILES['file']['tmp_name'];
			$nameFile = $_FILES['file']['name'];
			$extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			if ($extension != 'XLSX' && $extension != 'XLS') {
				echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
				die();
			}
			$inputFileType = PHPExcel_IOFactory::identify($fullfile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			// $objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");

			$total_sheets = $objPHPExcel->getSheetCount();

			$allSheetName       = $objPHPExcel->getSheetNames();
			$objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
			$highestRow         = $objWorksheet->getHighestRow();
			$highestColumn      = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('D');
			$arraydata          = array();

			$fields = $this->input->post('fields');
			for ($row = 2; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 2][$col] = $value;
				}
			}

			// echo '<pre>'; var_dump($arraydata);
			// echo json_encode(
			// 	[
			// 		'success' => true,
			// 		'alert_type' => 'success',
			// 		'message' => 'Import thành công 0 dòng',
			// 	]
			// );
			// die();

			$errList = [];
			foreach ($arraydata as $key => $value) {
				// 0: location_code
				// 1: location_name
				// 2: warehouse_code
				// 3: type_excel

				$location_code = $value[0];
				$location_name = $value[1];
				$warehouse_code = $value[2];
				$type_excel = $value[3];

				if (!empty($warehouse_code)) {
					$warehouseResult = get_table_where('tblwarehouse', ['code' => $warehouse_code], '', 'row_array', '', 'id');
					$warehouse_id = !empty($warehouseResult['id']) ? $warehouseResult['id'] : '';
				} else {
					continue;
				}

				if (empty($type_excel)) {
					$type_excel = 0;
				} else {
					$type_excel = 1;
				}

				if (empty($location_code) || empty($location_name) || empty($warehouse_id)) {
					continue;
				}
				$data = [];

				$data['code'] = $location_code;
				$data['name'] = $location_name;
				$data['name_parent'] = $location_name;
				$data['warehouse'] = $warehouse_id;
				$data['type_excel'] = $type_excel;
				$data['child'] = 1;
				$data['create_by'] = get_staff_user_id();
				$data['date_create'] = date('Y-m-d H:i:s');
				// if (empty($data['id_parent'])) {
				$data['lever'] = 0;
				// }
				// else {
				// 	$lever = 1;
				// 	$parent = $data['id_parent'];
				// 	while ($parent > 0) {
				// 		$ktr = get_table_where('tbllocaltion_warehouses', array('id' => $parent, 'warehouse' => $data['warehouse']), '', 'row');
				// 		$parent = $ktr->id_parent;
				// 		$lever++;
				// 	}
				// 	$data['lever'] = $lever;
				// }

				$existedCode = get_table_where('tbllocaltion_warehouses', array('code' => $location_code, 'warehouse' => $warehouse_id), '', 'row_array', '', 'id, code, name, warehouse');

				if (empty($existedCode['id'])) {	// insert
					$this->db->insert('tbllocaltion_warehouses', $data);
					$idd = $this->db->insert_id();
					if ($idd) {
						// if (!empty($data['id_parent'])) {
						// 	$this->db->update('tbllocaltion_warehouses', array('child' => 0), array('id' => $data['id_parent']));
						// }
						// if (!empty($data['id_parent'])) {
						// 	$get_parent = get_table_where('tbllocaltion_warehouses', array('id' => $data['id_parent']), '', 'row');
						// 	$name_parent = $get_parent->name_parent . " <i class='fa fa-caret-right text-danger' aria-hidden='true'></i> " . $data['name'];
						// } else {
						// $name_parent = $data['name'];
						// }
						// $this->db->where('id', $idd);
						// $this->db->update('tbllocaltion_warehouses', array('name_parent' => $name_parent));
						$get_code = get_table_where('tbllocaltion_warehouses', array('id' => $idd), '', 'row');
						activity_log_v2('warehouse', 'tbllocaltion_warehouses', $idd, $get_code->name, 'Thêm mới vị trí kho hàng [' . $get_code->name . ']');
						// echo json_encode(array('success' => true, 'message' => 'Thêm dữ liệu thành công'));
						// die();
						$count++;
					} else {
						$errList[] = ['code' => $location_code, 'result' => 'Thêm không thành công'];
					}
					// echo json_encode(array('success' => false, 'message' => 'Thêm dữ liệu không thành công'));
					// die();
				} else {	//update
					// $data['child'] = 0;
					// $kt_parent = get_table_where('tbllocaltion_warehouses', array('id_parent' => $data['id']), '', 'row');
					// if (empty($kt_parent) && !empty($data['id_parent'])) {
					// 	$data['child'] = 1;
					// }
					// if (empty($kt_parent) && empty($data['id_parent'])) {
					// 	$data['child'] = 1;
					// }
					$id = $existedCode['id'];
					$this->db->where('id', $id);
					if ($this->db->update('tbllocaltion_warehouses', $data)) {
						// if (!empty($data['id_parent'])) {
						// 	$get_parent = get_table_where('tbllocaltion_warehouses', array('id' => $data['id_parent']), '', 'row');
						// 	$name_parent = $get_parent->name_parent . " <i class='fa fa-caret-right text-danger' aria-hidden='true'></i> " . $data['name'];
						// } else {
						// $name_parent = $data['name'];
						// }
						// $this->db->where('id', $id);
						// $this->db->update('tbllocaltion_warehouses', array('name_parent' => $name_parent));
						// if (!empty($data['id_parent'])) {
						// 	$this->db->where('id', $data['id_parent']);
						// 	$this->db->update('tbllocaltion_warehouses', array('child' => 0));
						// }
						// $this->update_parents($id, $name_parent);
						$get_code = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
						activity_log_v2('warehouse', 'tbllocaltion_warehouses', $id, $get_code->name, 'Cập nhật vị trí kho hàng [' . $get_code->name . ']');
						// echo json_encode(array('success' => true, 'message' => 'Cập nhật dữ liệu thành công'));
						// die();
						$count++;
					} else {
						$errList[] = ['code' => $location_code, 'result' => 'Thêm không thành công'];
					}
					// echo json_encode(array('success' => false, 'message' => 'Cập nhật dữ liệu không thành công'));
					// die();
				}
			}
		}
		echo json_encode(
			[
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Import thành công ' . $count . ' dòng',
			]
		);
		die();
	}
}
