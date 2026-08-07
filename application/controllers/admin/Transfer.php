<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transfer extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('transfer_model');
		$this->load->model('outsource_model');
	}

	public function warehouse_id_chose()
	{
		$warehouse_id = $this->input->post('warehouse_id');
		$items = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse_id, 'product_quantity >' => 0));
		echo json_encode($items);
	}

	public function index()
	{
		if (!has_permission('transfer', '', 'view') && !has_permission('transfer', '', 'view_own')) {
			access_denied('Debt suppliers');
		}
		$data['title'] = _l('ch_transfer_warehouse');
		// $full_costs = $this->costs_model->get_full_costs();
		$this->load->view('admin/transfer/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('transfer');
	}

	public function SearchItems($id = '', $tyle_chose = '')
	{
		$data = [];
		$search = $this->input->get('term');
		$type = $this->input->get('type');
		if (empty($type)) {
			$type = $tyle_chose;
		}
		$limit_one = 12;
		$limit_two = 12;
		$limit_three = 12;
		$limit_all = 50;
		if ($type == -1) {
			$this->db->select(
				'
                    id,
                    "" as mode,
                    tblitems.name as text,
                    tblitems.code as code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tblitems.name', $search);
				$this->db->or_like('tblitems.code', $search);
				$this->db->group_end();
			}
			$this->db->order_by('name', 'DESC');
			$this->db->limit($limit_one);
			$items = $this->db->get('tblitems')->result_array();
			if (!empty($items)) {
				$data['results'][] =
					[
						'text' => _l('Sản phẩm'),
						'children' => $items
					];
			}
			$count_items = count($items);
			$this->db->select(
				'
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tbl_products.name', $search);
				$this->db->or_like('tbl_products.code', $search);
				$this->db->group_end();
			}
			$this->db->order_by('tbl_products.name', 'DESC');
			$this->db->limit($limit_two);
			// $this->db->limit(($limit_all - $count_product));
			$product = $this->db->get('tbl_products')->result_array();
			if (!empty($product)) {
				$data['results'][] =
					[
						'text' => _l('Thành phẩm'),
						'children' => $product
					];
			}
			$count_product = count($product);
			$this->db->select(
				'
                id as id,
                "" as mode,
                tbl_tools_supplies.code as code,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tbl_tools_supplies.name', $search);
				$this->db->or_like('tbl_tools_supplies.code', $search);
				$this->db->group_end();
			}
			$this->db->order_by('tbl_tools_supplies.name', 'DESC');
			$this->db->limit($limit_three);
			$tools = $this->db->get('tbl_tools_supplies')->result_array();
			if (!empty($tools)) {
				$data['results'][] =
					[
						'text' => _l('Công cụ - Vật tư'),
						'children' => $tools
					];
			}
			$count_tools = count($tools);
			$this->db->select(
				'
                id as id,
                mode,
                tbl_materials.code as code,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tbl_materials.name', $search);
				$this->db->or_like('tbl_materials.code', $search);
				$this->db->group_end();
			}
			$this->db->order_by('tbl_materials.name', 'DESC');
			$this->db->limit(($limit_all - $count_product - $count_tools - $count_items));
			$product = $this->db->get('tbl_materials')->result_array();
			if (!empty($product)) {
				$data['results'][] =
					[
						'text' => _l('Nguyên vật liệu'),
						'children' => $product
					];
			}
		} else
			if ($type == 'items') {
				$this->db->select(
					'
                    id as id,
                    "" as mode,
                    tblitems.code as code,
                    tblitems.name as text,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
					false
				);
				if (!empty($search)) {
					$this->db->group_start();
					$this->db->like('tblitems.name', $search);
					$this->db->or_like('tblitems.code', $search);
					$this->db->group_end();
				} else {
					if ($id > 0) {
						$this->db->group_start();
						$this->db->where('tblitems.id', $id);
						$this->db->group_end();
					}
				}
				$this->db->order_by('name', 'DESC');
				$this->db->limit(50);
				$items = $this->db->get('tblitems')->result_array();
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
						'id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
						false
					);
					if (!empty($search)) {
						$this->db->group_start();
						$this->db->like('tbl_products.name', $search);
						$this->db->or_like('tbl_products.code', $search);
						$this->db->group_end();
					} else {
						if ($id > 0) {
							$this->db->group_start();
							$this->db->where('tbl_products.id', $id);
							$this->db->group_end();
						}
					}
					$this->db->order_by('tbl_products.name', 'DESC');
					$this->db->limit(50);
					// $this->db->limit(($limit_all - $count_product));
					$product = $this->db->get('tbl_products')->result_array();
					if (!empty($product)) {
						$data['results'][] =
							[
								'text' => _l('Thành phẩm'),
								'children' => $product
							];
					}
				} elseif ($type == 'nvl') {
					$this->db->select(
						'
                id as id,
                mode,
                tbl_materials.code as code,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
						false
					);
					if (!empty($search)) {
						$this->db->group_start();
						$this->db->like('tbl_materials.name', $search);
						$this->db->or_like('tbl_materials.code', $search);
						$this->db->group_end();
					} else {
						if ($id > 0) {
							$this->db->group_start();
							$this->db->where('tbl_materials.id', $id);
							$this->db->group_end();
						}
					}
					$this->db->order_by('tbl_materials.name', 'DESC');
					$this->db->limit(50);
					$product = $this->db->get('tbl_materials')->result_array();
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
                "" as mode,
                tbl_tools_supplies.code as code,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
						false
					);
					if (!empty($search)) {
						$this->db->group_start();
						$this->db->like('tbl_tools_supplies.name', $search);
						$this->db->or_like('tbl_tools_supplies.code', $search);
						$this->db->group_end();
					} else {
						if ($id > 0) {
							$this->db->group_start();
							$this->db->where('tbl_tools_supplies.id', $id);
							$this->db->group_end();
						}
					}
					$this->db->order_by('tbl_tools_supplies.name', 'DESC');
					$this->db->limit(50);
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

	public function detail($id = '')
	{
		if (!has_permission('transfer', '', 'create')) {
			ajax_access_denied();
		}
		if ($this->input->post()) {
			if ($id == '') {
				if (!has_permission('transfer', '', 'create')) {
					access_denied('transfer');
				}
				$data = $this->input->post();
				$data['note'] = $this->input->post('note', true);
				if (isset($data['items']) && count($data['items']) > 0) {
					$id = $this->transfer_model->add($data);
				}
				if ($id) {
					set_alert('success', _l('ch_added_successfuly'));
					redirect(admin_url('transfer'));
				}
			} else {
				if (!has_permission('transfer', '', 'edit')) {
					access_denied('transfer');
				}
				$data = $this->input->post();
				$data['note'] = $this->input->post('note', true);
				$success = $this->transfer_model->update($data, $id);
				if ($success == true) {
					set_alert('success', _l('ch_updated_successfuly'));
				}
				redirect(admin_url('transfer/detail/' . $id));
			}
		}
		if ($id != '') {
			if (!has_permission('transfer', '', 'edit')) {
				access_denied('transfer');
			}
			$data['title'] = _l('ch_edit_transfers');
			$data['items'] = $this->transfer_model->get($id);
		} else {
			if (!has_permission('transfer', '', 'create')) {
				access_denied('transfer');
			}
			$data['title'] = _l('ch_add_transfers');
		}
		$data['taxes'] = get_taxes_dropdown_template('', 0);
		$data['tax'] = get_table_where('tbltaxes');
		$type_items = get_table_where('tbltype_items', array('active' => 1));
		$count = 0;
		$data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
		foreach ($type_items as $key => $value) {
			$count++;
			$data['type_items'][$count] = $value;
		}
		$data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
		$data['warehouse'] = get_table_where('tblwarehouse');
		$data['localtion_warehouses'] = array();
//        $_data = $this->input->get();
//        if(!empty($_data['report'])) {
//            $data['items_report'] = $_data['items'];
//        }
		$this->load->view('admin/transfer/detail', $data);
	}

	public function detail_report($id = '')
	{
		if (!has_permission('transfer', '', 'create')) {
			ajax_access_denied();
		}
		if ($this->input->post()) {
			if ($id == '') {
				if (!has_permission('transfer', '', 'create')) {
					access_denied('transfer');
				}
				$data = $this->input->post();
				$data['note'] = $this->input->post('note', true);
				if (isset($data['items']) && count($data['items']) > 0) {
					$id = $this->transfer_model->add($data);
				}
				if ($id) {
					set_alert('success', _l('ch_added_successfuly'));
					redirect(admin_url('transfer'));
				}
			} else {
				if (!has_permission('transfer', '', 'edit')) {
					access_denied('transfer');
				}
				$data = $this->input->post();
				$data['note'] = $this->input->post('note', true);
				$success = $this->transfer_model->update($data, $id);
				if ($success == true) {
					set_alert('success', _l('ch_updated_successfuly'));
				}
				redirect(admin_url('transfer/detail/' . $id));
			}
		}
		if ($id != '') {
			if (!has_permission('transfer', '', 'edit')) {
				access_denied('transfer');
			}
			$data['title'] = _l('ch_edit_transfers');
			$data['items'] = $this->transfer_model->get($id);
		} else {
			if (!has_permission('transfer', '', 'create')) {
				access_denied('transfer');
			}
			$data['title'] = _l('ch_add_transfers');
		}
		$data['taxes'] = get_taxes_dropdown_template('', 0);
		$data['tax'] = get_table_where('tbltaxes');
		$type_items = get_table_where('tbltype_items', array('active' => 1));
		$count = 0;
		$data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
		foreach ($type_items as $key => $value) {
			$count++;
			$data['type_items'][$count] = $value;
		}
		$data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
		$data['warehouse'] = get_table_where('tblwarehouse');
		$data['localtion_warehouses'] = array();
		$data['items_report'] = array();
		$_data = $this->input->get();
		if (!empty($_data['report'])) {
			$data['items_report'] = $_data['items'];
		}
		$this->load->view('admin/transfer/detail_report', $data);
	}

	public function add()
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			unset($data['id']);
			if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
				$data['lever'] = 1;
			} else {
				$lever = 1;
				$parent = $data['costs_parent'];
				while ($parent > 0) {
					$ktr = get_table_where('tblcosts', array('id' => $parent), '', 'row');
					$parent = $ktr->costs_parent;
					$lever++;
				}
				$data['lever'] = $lever;
			}
			$this->db->insert('tblcosts', $data);
			$id = $this->db->insert_id();
			if ($id) {
				$success = true;
				$message = _l('ch_added_successfuly');
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function update()
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$id = $data['id'];
			unset($data['id']);
			$this->db->where('id', $id);
			$idd = $this->db->update('tblcosts', $data);
			if ($id) {
				$success = true;
				$message = _l('ch_updated_successfuly');
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function test_quantity()
	{
		$warehouse_id_main = $this->input->post('warehouse_id_main');
		$test_quantity = 0;
		$product = explode(',', trim($this->input->post('product_id'), ','));
		$products = array();
		foreach ($product as $key => $v) {
			$product_id = explode('|', $v);
			$get_local = get_table_where('tblwarehouse_items', array('id' => $product_id[2]), '', 'row');
			$products[$key]['localtion'] = $get_local->localtion;
			$products[$key]['warehouse_id'] = $get_local->warehouse_id;
			$products[$key]['lot_code'] = $get_local->lot_code;
			$products[$key]['date_sx'] = $get_local->date_sx;
			$products[$key]['date_sd'] = $get_local->date_sd;
			$products[$key]['date_use'] = $get_local->date_use;
			$products[$key]['data'] = $v;
		}
		$this->db->select('count(*) as count');
		foreach ($products as $key => $v) {
			$product_id = explode('|', $v['data']);
			$this->db->or_group_start();
			$this->db->where('id_items', $product_id[1]);
			$this->db->where('type_items', $product_id[0]);
			$this->db->where('localtion', $v['localtion']);
			$this->db->where('lot_code', $v['lot_code']);
			$this->db->where('date_sx', $v['date_sx']);
			$this->db->where('date_sd', $v['date_sd']);
			$this->db->where('date_use', $v['date_use']);
			$this->db->where('product_quantity >=', $product_id[3]);
			$this->db->where('warehouse_id', $v['warehouse_id']);
			$this->db->group_end();
		}
		$result = $this->db->get('tblwarehouse_items')->row();
		if ($result->count == count($product)) {
			$data['success'] = true;
		} else {
			$data['success'] = false;
			foreach ($product as $key => $v) {
				$product_id = explode('|', $v);
				$get_local = get_table_where('tblwarehouse_items', array('id' => $product_id[2]), '', 'row');
				$this->db->select('*');
				$this->db->where('id_items', $product_id[1]);
				$this->db->where('type_items', $product_id[0]);
				$this->db->where('localtion', $get_local->localtion);
				$this->db->where('warehouse_id', $get_local->warehouse_id);
				$this->db->where('lot_code', $get_local->lot_code);
				$this->db->where('date_sx', $get_local->date_sx);
				$this->db->where('date_sd', $get_local->date_sd);
				$this->db->where('date_use', $get_local->date_use);
				$data['items'][$key] = $this->db->get('tblwarehouse_items')->row();
			}
		}
		echo json_encode($data);
		die;
	}

	public function delete($id)
	{
		if (!has_permission('transfer', '', 'delete')) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('ch_no_delete')
			));
			die;
		}
		//tnh
		$this->db->select('tbl_outsource.*');
		$this->db->from('tbl_outsource');
		$this->db->where('tbl_outsource.tranfer_id', $id);
		$outsource = $this->db->get()->row_array();
		if (!empty($outsource) && $outsource['workflow'] > 1) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('Đã nhập gia công không thể xóa')
			));
			die;
		}
		//
		$dtData = get_table_where('tbltransfer_warehouse',array('id'=>$id),'','row_array');
		$items = get_table_where('tbltransfer_warehouse_detail', ['tbltransfer_warehouse_detail.id_transfer' => $id]);
		$response = $this->transfer_model->delete($id);
		$alert_type = 'warning';
		$message = _l('ch_no_delete');
		if ($response) {
			//tnh
			if (!empty($outsource) && $outsource['workflow'] == 1) {
				$this->outsource_model->updateOutsource($outsource['id'], ['workflow' => 0, 'tranfer_id' => 0]);
			}
			//
			//dt
			if (!empty($items)) {
				foreach ($items as $key => $value) {
					if ($value['order_id_item']) {
						$order_item = get_table_where(
							'tbl_order_items',
							['id' => $value['order_id_item']],
							'',
							'row_array'
						);
						$qty = $order_item['quantity_condition'] - $value['quantity_net'];
						$this->db->where('id', $value['order_id_item']);
						$this->db->update(
							'tbl_order_items',
							['quantity_condition' => $qty]
						);
					}

                    if ($value['tranfer_business_item_id']){
                        $tranfer_business_item = get_table_where(
                            'tbl_tranfer_business_item',
                            ['id' => $value['tranfer_business_item_id']],
                            '',
                            'row_array'
                        );
                        $qty = $tranfer_business_item['quantity_hold'] - $value['quantity_net'];
                        $this->db->where('id', $value['tranfer_business_item_id']);
                        $this->db->update(
                            'tbl_tranfer_business_item',
                            ['quantity_hold' => $qty]
                        );
                    }
				}
			}
            $this->db->where('tranfer_id',$id);
            $this->db->delete('tbl_tranfer_to_tranfer_business');

			 insertActivityLog([
                'type_parent_obj' => 'transfer_warehouse',
                'table_obj' => 'tbltransfer_warehouse',
                'id_obj' => $id,
                'name_obj' => $dtData['prefix'].'-'.$dtData['code'],
                'content' => lang('Xóa phiếu chuyển kho').' ['.$dtData['prefix'].'-'.$dtData['code'].']',
                'actions' => 'delete'
            ]);
			//
			$alert_type = 'success';
			$message = _l('ch_delete');
		}
		echo json_encode(array(
			'alert_type' => $alert_type,
			'message' => $message
		));
	}

	public function confirm_warehous()
	{
		if (!has_permission('transfer', '', 'approve_warehouse')) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('ch_approve_not')
			));
			die;
		}
		$id = $this->input->post('id');
		$warehouseman_id = $this->input->post('warehouseman_id');
		if (!$id) {
			die('ch_no_items');
		}
		if (!test_quantity_tranfer($id)) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('alert_quanliti_ware'),
			));
			die;
		} else {
			$data = array(
				'warehouseman_id' => get_staff_user_id(),
				'warehouseman_date' => date('Y-m-d H:i:s')
			);
			if (empty($warehouseman_id)) {
				log_activity('Transfer Warehouse items approved [ID Import: ' . $id);
				$this->transfer_model->increaseTranfersWarehouse($id);
				$alert_type = 'success';
				$message = _l('ch_successful_approval');
				$success = $this->db->update('tbltransfer_warehouse', $data, array('id' => $id));
			}
		}
		echo json_encode(array(
			'alert_type' => $alert_type,
			'message' => $message
		));
	}

	public function transfer_data($id = '')
	{
		$data['items'] = $this->transfer_model->get($id);
		$data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tbltransfer_warehouse', 'id_obj' => $id), 'id DESC');
		$this->load->view('admin/transfer/view_modal', $data);
	}

	public function update_status($value = '')
	{
		if (!has_permission('transfer', '', 'approve')) {
			echo json_encode(array(
				'success' => false,
				'message' => _l('ch_approve_not')
			));
			die;
		}
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$import = get_table_where('tbltransfer_warehouse', array('id' => $id), '', 'row');
			if ($import->status == 2) {
				die;
			}
			$staff_id = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$history_status = $import->history_status;
			$history_status .= '|' . $staff_id . ',' . $date;
			$data = array(
				'history_status' => $history_status,
				'status' => ($status + 1),
			);
			$success = $this->transfer_model->update_status($id, $data);
		}
		if ($success) {
			echo json_encode(array(
				'success' => $success,
				'alert_type' => 'success',
				'message' => _l('ch_successful_approval')
			));
		} else {
			echo json_encode(array(
				'success' => $success,
				'alert_type' => 'danger',
				'message' => _l('ch_no_successful_approval')
			));
		}
		die;
	}

	public function get_items($id = '', $type = '')
	{
		$this->load->model('invoice_items_model');
		$data = $this->invoice_items_model->get_full_item($id, $type);
		$data->html = format_item_color($id, $type);
		$data->avatar = (!empty($data->avatar) ? (file_exists($data->avatar) ? base_url($data->avatar) : (file_exists('uploads/materials/' . $data->avatar) ? base_url('uploads/materials/' . $data->avatar) : (file_exists('uploads/products/' . $data->avatar) ? base_url('uploads/products/' . $data->avatar) : (file_exists('uploads/tools_supplies/' . $data->avatar) ? base_url('uploads/tools_supplies/' . $data->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
		echo json_encode($data);
	}

	public function print_transfer_old($id)
	{
		ob_end_clean();
		$data = [];
		$this->db->select('*');
		$this->db->from('tbltransfer_warehouse');
		$this->db->where('id', $id);
		$tranfer = $this->db->get()->row_array();
		// $id_branch = get_staff_user_id_branch_v3();
		$id_branch_take = '5';
		$numbercode = $tranfer['prefix'] . '-' . $tranfer['code'];
		$date = $tranfer['date'];
		$items = $this->transfer_model->get($id);
		$number_print = GetNumberPrint($id, '1');
		$text_number = '<div class="text-left" style="font-size: 9px">
        <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
        <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
		$data['number_print'] = $text_number;
		$data['title'] = lang('sum_print_transfers');
		$data['type'] = 'P';
		$data['img'] = '';
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <h1 class="text-center uppercase">' . lang('sum_transfers') . '</h1>
            <div class="text-left">
                <span ><span class="bold">' . _l('ch_number_code') . ':</span> ' . $numbercode . '</span><br>
                <span ><span class="bold">' . _l('date') . ':</span> ' . _d($date) . '</span><br>'; ?>
		<?php
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '
                <span ><span class="bold">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>
          ';
		}
		if ($tranfer['productions_capacity_id'] > 0) {
			$tbProductionsOrders_text = '';
			$tbProductionsOrders = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                WHERE tbl_productions_orders_items.plan_id = " . $tranfer['productions_capacity_id'] . "
                GROUP BY tbl_productions_orders_items.plan_id
            ) ";
			$ProductionsOrders = $this->db->query($tbProductionsOrders)->row();
			if (!empty($ProductionsOrders)) {
				$tbProductionsOrders_text = $ProductionsOrders->reference_no;
			}
			$productions_plan = get_table_where('tbl_productions_plan', array('id' => $tranfer['productions_capacity_id']), '', 'row');
			echo '
                <span ><span class="bold">' . _l('Số kế hoạch NPL') . ':</span> ' . $productions_plan->reference_no . '</span><br>
                <span ><span class="bold">' . _l('Lệnh sản xuất tổng') . ':</span> ' . ($tbProductionsOrders_text) . '</span>
          ';
		}
		?>
		<?php
		echo '
            </div>'; ?>
		<?php
		$totalQuantity = 0;
		$total = 0;
		?>
        <div></div>
        <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <thead>
            <tr nobr="true" class="text-center bold">
                <th colspan="1" style="width: 6%;"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="width: 17%;"><?php echo _l('Mã hàng'); ?></th>
                <th colspan="1" style="width: 23%;"><?php echo _l('ch_items_name_t'); ?></th>
                <th colspan="1" style="width: 22%;"><?php echo _l('Kho hàng'); ?></th>
                <th colspan="1" style="width: 8%;"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="width: 10%;"><?php echo _l('item_quantity'); ?></th>
                <th colspan="1" style="width: 14%;"><?php echo _l('note'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($items->items as $key => $value) { ?>
                <tr>
                    <td class="text-center" style="width: 6%;">
						<?php echo($key + 1); ?>
                    </td>
                    <td class="text-left" style="width: 17%"><?= $value['code_item'] ?>
                    </td>
                    <td style="width: 23%;"><?php echo $value['name_item']; ?>
                    </td>
                    <td style="width: 22%;">-<?php echo $value['warehouse_name_id']; ?>
                        <br>-<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;"><?= _l('Lot') ?>:<?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
                    <td style="width: 8%;" class="text-center">
						<?php echo $value['unit_name_stock']; ?>
                    </td>
                    <td class="text-center" style="width: 10%;">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
                    <td class="text-left" style="width: 14%;">
						<?php echo $value['note']; ?>
                    </td>
                </tr>
				<?php
				$totalQuantity += $value['quantity_net'];
				$total += $value['amount'];
			} ?>
            </tbody>
        </table>
		<?php
		echo '
            <p class="text-right"><span>Ngày ' . date("d") . ' tháng ' . date("m") . ' năm ' . date("Y") . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$pdf = print_pdf_tnhs($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}
	public function print_transfer($id)
	{
		ob_end_clean();
		$data = [];
		$this->db->select('*');
		$this->db->from('tbltransfer_warehouse');
		$this->db->where('id', $id);
		$tranfer = $this->db->get()->row_array();
		// $id_branch = get_staff_user_id_branch_v3();
		$id_branch_take = '5';
		$numbercode = $tranfer['prefix'] . '-' . $tranfer['code'];
		$date = $tranfer['date'];
		$items = $this->transfer_model->get($id);
		$number_print = GetNumberPrint($id, '1');
		$text_number = '<div class="text-left" style="font-size: 9px">
        <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
        <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
		$data['number_print'] = $text_number;
		$data['title'] = lang('sum_print_transfers');
		$data['type'] = 'L';
		$data['img'] = '';
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <h1 class="text-center uppercase">' . lang('sum_transfers') . '</h1>
            <div class="text-left">
                <span ><span class="bold" style="text-transform: capitalize;">' . _l('ch_number_code') . ':</span> ' . $numbercode . '</span><br>
                <span ><span class="bold">' . _l('date') . ':</span> ' . _d($date) . '</span><br>'; ?>
		<?php
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '
                <span ><span class="bold">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>
          ';
		  $this->db->select('GROUP_CONCAT(DATE_FORMAT(date_shipping, "%d/%m/%Y")) as list_date');
			$this->db->from('tbl_order_item_shippings');
			$this->db->where('tbl_order_items.order_id', $tranfer['order_id_new']);
			$this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
			$sub_date = $this->db->get()->row('list_date');
			echo '<span ><span class="bold" style="text-transform: capitalize;">' . _l('Ngày giao hàng dự kiến') . ':</span> ' . $sub_date . '</span><br>';
		}
		if ($tranfer['productions_capacity_id'] > 0) {
			$tbProductionsOrders_text = '';
			$tbProductionsOrders = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                WHERE tbl_productions_orders_items.plan_id = " . $tranfer['productions_capacity_id'] . "
                GROUP BY tbl_productions_orders_items.plan_id
            ) ";
			$ProductionsOrders = $this->db->query($tbProductionsOrders)->row();
			if (!empty($ProductionsOrders)) {
				$tbProductionsOrders_text = $ProductionsOrders->reference_no;
			}
			$productions_plan = get_table_where('tbl_productions_plan', array('id' => $tranfer['productions_capacity_id']), '', 'row');
			echo '
                <span ><span class="bold">' . _l('Số Kế Hoạch NPL') . ':</span> ' . $productions_plan->reference_no . '</span><br>
                <span ><span class="bold">' . _l('Lệnh Sản Xuất Tổng') . ':</span> ' . ($tbProductionsOrders_text) . '</span>
          ';
		}
		?>
		<?php
		echo '
            </div>'; ?>
		<?php
		$totalQuantity = 0;
		$total = 0;
		$width1 = 'width: 4%;';
		$width2 = 'width: 12%;';
		$width3 = 'width: 12%;';
		$width4 = 'width: 12%;';
		$width5 = 'width: 12%;';
		$width6 = 'width: 11%;';
		$width7 = 'width: 4%;';
		$width8 = 'width: 5%;';
		$width9 = 'width: 5%;';
		$width10 = 'width: 5%;';
		$width11 = 'width: 5%;';
		$width12 = 'width: 8%;';
		$width13 = 'width: 5%;';
		?>
        <div></div>
        <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <thead>
            <tr nobr="true" class="text-center bold">
                <!-- <th colspan="1" style="width: 6%;"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="width: 17%;"><?php echo _l('Mã hàng'); ?></th>
                <th colspan="1" style="width: 23%;"><?php echo _l('ch_items_name_t'); ?></th>
                <th colspan="1" style="width: 22%;"><?php echo _l('Kho hàng'); ?></th>
                <th colspan="1" style="width: 8%;"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="width: 10%;"><?php echo _l('item_quantity'); ?></th>
                <th colspan="1" style="width: 14%;"><?php echo _l('note'); ?></th> -->

				<th colspan="1" style="<?= $width1 ?>"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="<?= $width2 ?>">Vị Trí<br>Kho Hàng</th>
                <th colspan="1" style="<?= $width3 ?>">Vị Trí Kho<br>Chuyển Đến</th>
                <th colspan="1" style="<?= $width4 ?>"><?php echo _l('Mã Hàng Hóa'); ?></th>
                <th colspan="1" style="<?= $width5 ?>"><?php echo _l('Tên Hàng Hóa'); ?></th>
                <th colspan="1" style="<?= $width6 ?>">Kích Thước NPL<br>(Cao x Ngang)</th>
                <th colspan="1" style="<?= $width7 ?>"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="<?= $width8 ?>"><?php echo _l('SL Tồn'); ?></th>
                <th colspan="1" style="<?= $width9 ?>"><?php echo _l('SL Thực Xuất'); ?></th>
                <th colspan="1" style="<?= $width10 ?>"><?php echo _l('SL Bù Hao'); ?></th>
                <th colspan="1" style="<?= $width11 ?>"><?php echo _l('Tổng SL Xuất'); ?></th>
                <th colspan="1" style="<?= $width12 ?>"><?php echo _l('Tổng Chiều Cao Giấy Xuất'); ?></th>
                <th colspan="1" style="<?= $width13 ?>"><?php echo _l('SL Còn Lại'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($items->items as $key => $value) { ?>
                <tr>
				<td class="text-center" style="<?= $width1 ?>">
						<?php echo($key + 1); ?>
                    </td>
					<td style="<?= $width2 ?>">
						-<?php echo $value['warehouse_name_id']; ?><br>
                        -<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;">
							<?= _l('Lot') ?>: <?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
					<td style="<?= $width3 ?>">
						-<?php echo $value['warehouse_name_to']; ?><br>
                        -<?php echo $value['localtion_name_to']; ?>
                        <div style="font-size: 10px;font-style: italic;">
							<?= _l('Lot') ?>: <?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
                    <td class="text-left" style="<?= $width4 ?>"><?= $value['code_item'] ?>
                    </td>
                    <td style="<?= $width5 ?>"><?php echo $value['name_item']; ?>
                    </td>
					<td class="text-center" style="<?= $width6 ?>"><?php echo $value['height'] . ' x ' . $value['wide']; ?></td>
					<td style="<?= $width7 ?>" class="text-center">
						<?php echo $value['unit_name_stock']; ?>
                    </td>
					<td class="text-center" style="<?= $width8 ?>">
						<?php $sumquanliti = $this->sumquanliti_order($tranfer['order_id_new'], $value['id_items'], $value['type']) ?>
						<?php echo formatNumber($sumquanliti); ?>
                    </td>
					<td class="text-center" style="<?= $width9 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
					<td class="text-center" style="<?= $width10 ?>"></td>
					<td class="text-center" style="<?= $width11 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>

					<td class="text-center" style="<?= $width12 ?>">
						<?php $totalHeight = ''; if (!empty($value['height'])) {
							$totalHeight = formatNumber((float)$value['height'] * (float)$value['quantity_net']);
						}?>
						<?php echo $totalHeight ?>
					</td>
                    
                    <td class="text-center" style="<?= $width13 ?>">
						<?php $sumquanlitiTransfer = $this->sumquanlitiTransfer_order($tranfer['order_id_new'], $value['id_items'], $value['type']) ?>
						<?php echo formatNumber(($sumquanliti - $sumquanlitiTransfer)); ?>
                    </td>
                    <!-- <td class="text-center" style="width: 6%;">
						<?php echo($key + 1); ?>
                    </td>
                    <td class="text-left" style="width: 17%"><?= $value['code_item'] ?>
                    </td>
                    <td style="width: 23%;"><?php echo $value['name_item']; ?>
                    </td>
                    <td style="width: 22%;">-<?php echo $value['warehouse_name_id']; ?>
                        <br>-<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;"><?= _l('Lot') ?>:<?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
                    <td style="width: 8%;" class="text-center">
						<?php echo $value['unit_name_stock']; ?>
                    </td>
                    <td class="text-center" style="width: 10%;">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
                    <td class="text-left" style="width: 14%;">
						<?php echo $value['note']; ?>
                    </td> -->
                </tr>
				<?php
				$totalQuantity += $value['quantity_net'];
				$total += $value['amount'];
			} ?>
            </tbody>
        </table>
		<?php
		echo '
			<p class="text-right"><span>Ngày ' . date("d") . ' tháng ' . date("m") . ' năm ' . date("Y") . '</span></p>
			<table style="width: 100%">
				<tr>
					<td class="text-center">
						<span class="bold">Người Giao</span><br>
						<span>(Ký, ghi rõ họ tên)</span>
					</td>
					<td class="text-center">
						<span class="bold">Người Nhận</span><br>
						<span>(Ký, ghi rõ họ tên)</span>
					</td>
					<td class="text-center">
						<span class="bold">Trưởng Bộ Phận</span><br>
						<span>(Ký, ghi rõ họ tên)</span>
					</td>
				</tr>
			</table>
		';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$pdf = print_pdf_tnhs($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}

	public function print_transfer_nvl_old($id)
	{
		ob_end_clean();
		$data = [];
		$this->db->select('*');
		$this->db->from('tbltransfer_warehouse');
		$this->db->where('id', $id);
		$tranfer = $this->db->get()->row_array();
		// $id_branch = get_staff_user_id_branch_v3();
		$id_branch_take = '5';
		$numbercode = $tranfer['prefix'] . '-' . $tranfer['code'];
		$date = $tranfer['date'];
		$items = $this->transfer_model->get($id);
		$number_print = GetNumberPrint($id, '1');
		$text_number = '<div class="text-left" style="font-size: 9px">
        <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
        <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
		$data['number_print'] = $text_number;
		$data['title'] = lang('sum_print_transfers');
		$data['type'] = 'P';
		$data['img'] = '';
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <h1 class="text-center uppercase">' . lang('sum_transfers') . '</h1>
            <div class="text-left"><span ><span class="bold">' . _l('ch_number_code') . ':</span> ' . $numbercode . '</span><br><span ><span class="bold">' . _l('date') . ':</span> ' . _d($date) . '</span><br>'; ?>
		<?php
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '
                <span ><span class="bold">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>
          ';
		}
		if ($tranfer['productions_capacity_id'] > 0) {
			$tbProductionsOrders_text = '';
			$tbProductionsOrders = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                WHERE tbl_productions_orders_items.plan_id = " . $tranfer['productions_capacity_id'] . "
                GROUP BY tbl_productions_orders_items.plan_id
            ) ";
			$ProductionsOrders = $this->db->query($tbProductionsOrders)->row();
			if (!empty($ProductionsOrders)) {
				$tbProductionsOrders_text = $ProductionsOrders->reference_no;
			}
			$productions_plan = get_table_where('tbl_productions_plan', array('id' => $tranfer['productions_capacity_id']), '', 'row');
			$tbOrdersItemsShipping = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no,'___', DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%d/%m/%Y'))) as date_shipping
            FROM tbl_order_item_shippings
            JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
            JOIN tbl_orders ON tbl_orders.id = tbl_order_items.order_id
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_orders_items_shipping";
			$tbBusinessItemsShipping = "(
            SELECT
                tbl_business_plan_items_date.business_plan_items_id as business_plan_items_id,
                GROUP_CONCAT(CONCAT(tbl_business_plan.reference_no,'___', DATE_FORMAT(tbl_business_plan_items_date.date, '%d/%m/%Y'))) as date_shipping
            FROM tbl_business_plan_items_date
            JOIN tbl_business_plan_items ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
            JOIN tbl_business_plan ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
            GROUP BY tbl_business_plan_items_date.business_plan_items_id
        ) tb_business_items_shipping";
			$productions_capacity_id = $tranfer['productions_capacity_id'];
			$tbDateDelivery = "(
            SELECT
                tbl_productions_plan_items.productions_plan_id as plan_id,
                GROUP_CONCAT(tb_orders_items_shipping.date_shipping) as date_order,
                GROUP_CONCAT(tb_business_items_shipping.date_shipping) as date_business
            FROM tbl_productions_plan_items
            LEFT JOIN $tbOrdersItemsShipping ON tb_orders_items_shipping.order_item_id = tbl_productions_plan_items.item_object_id AND tbl_productions_plan_items.type_object = 'orders'
            LEFT JOIN $tbBusinessItemsShipping ON tb_business_items_shipping.business_plan_items_id = tbl_productions_plan_items.item_object_id AND tbl_productions_plan_items.type_object = 'business_plan'
            WHERE tbl_productions_plan_items.productions_plan_id = $productions_capacity_id
        )";
			$date_ship = $this->db->query($tbDateDelivery)->result_array();
			$string_date = [];
			foreach ($date_ship as $key => $value) {
				if (!empty($value['date_order'])) {
					$string_date[] = str_replace('___', ' - ', $value['date_order']);
				}
				if (!empty($value['date_business'])) {
					$string_date[] = str_replace('___', ' - ', $value['date_business']);
				}
			}
			echo '<span class="bold">' . _l('Số kế hoạch NPL') . ':</span> ' . $productions_plan->reference_no . '<br>';
			echo '<span class="bold">' . _l('Lệnh sản xuất tổng') . ':</span> ' . ($tbProductionsOrders_text) . '<br>';
			echo '<span class="bold">' . _l('Ngày giao hàng dự kiến') . ':</span> ' . (implode(', <br/>', $string_date)) . '<br>';
		}
		?>
		<?php
		echo '<span ><span class="bold">' . _l('note') . ':</span> ' . ($tranfer['note']) . '</span>
            </div>'; ?>
		<?php
		$totalQuantity = 0;
		$total = 0;
		$width1 = 'width: 6%;';
		$width2 = 'width: 12%;';
		$width3 = 'width: 18%;';
		$width4 = 'width: 18%;';
		$width5 = 'width: 7%;';
		$width6 = 'width: 12%;';
		$width7 = 'width: 12%;';
		$width8 = 'width: 15%;';
		?>
        <div></div>
        <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <thead>
            <tr nobr="true" class="text-center bold">
                <th colspan="1" style="<?= $width1 ?>"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="<?= $width2 ?>"><?php echo _l('Mã hàng'); ?></th>
                <th colspan="1" style="<?= $width3 ?>"><?php echo _l('ch_items_name_t'); ?></th>
                <th colspan="1" style="<?= $width4 ?>"><?php echo _l('Kho hàng'); ?></th>
                <th colspan="1" style="<?= $width5 ?>"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="<?= $width6 ?>"><?php echo _l('Số lượng cần'); ?></th>
                <th colspan="1" style="<?= $width7 ?>"><?php echo _l('Số lượng thực chuyển'); ?></th>
                <th colspan="1" style="<?= $width8 ?>"><?php echo _l('Còn lại'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($items->items as $key => $value) { ?>
                <tr style="font-size: 10px;">
                    <td class="text-center" style="<?= $width1 ?>">
						<?php echo($key + 1); ?>
                    </td>
                    <td class="text-left" style="<?= $width2 ?>"><?= $value['code_item'] ?>
                    </td>
                    <td style="<?= $width3 ?>"><?php echo $value['name_item']; ?>
                    </td>
                    <td style="<?= $width4 ?>">-<?php echo $value['warehouse_name_id']; ?>
                        <br>-<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;"><?= _l('Lot') ?>:<?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
                    <td style="<?= $width5 ?>" class="text-center">
						<?php echo $value['unit_name_stock']; ?>
                    </td>
                    <td class="text-center" style="<?= $width6 ?>">
						<?php
						$sumquanliti = $this->sumquanliti($tranfer['productions_capacity_id'], $value['id_items'], $value['type'])
						?>
						<?php echo formatNumber($sumquanliti); ?>
                    </td>
                    <td class="text-center" style="<?= $width7 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
                    <td class="text-center" style="<?= $width8 ?>">
						<?php
						$sumquanlitiTransfer = $this->sumquanlitiTransfer($tranfer['productions_capacity_id'], $value['id_items'], $value['type'])
						?>
						<?php echo formatNumber(($sumquanliti - $sumquanlitiTransfer)); ?>
                    </td>
                </tr>
				<?php
				$totalQuantity += $value['quantity_net'];
				$total += $value['amount'];
			} ?>
            </tbody>
        </table>
		<?php
		echo '
            <p class="text-right"><span>Ngày ' . date("d") . ' tháng ' . date("m") . ' năm ' . date("Y") . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$pdf = print_pdf_tnhs($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}
	public function print_transfer_nvl($id)
	{
		ob_end_clean();
		$data = [];
		$this->db->select('*');
		$this->db->from('tbltransfer_warehouse');
		$this->db->where('id', $id);
		$tranfer = $this->db->get()->row_array();
		// $id_branch = get_staff_user_id_branch_v3();
		$id_branch_take = '5';
		$numbercode = $tranfer['prefix'] . '-' . $tranfer['code'];
		$date = $tranfer['date'];
		$items = $this->transfer_model->get($id);
		$number_print = GetNumberPrint($id, '1');
		$text_number = '<div class="text-left" style="font-size: 9px">
        <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
        <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '
                <span ><span class="bold" style="text-transform: capitalize;">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>
          ';
		}
		$data['number_print'] = $text_number;
		$data['title'] = lang('sum_print_transfers');
		$data['type'] = 'L';
		$data['img'] = '';
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <h1 class="text-center uppercase">' . lang('sum_transfers') . '</h1>
            <div class="text-left"><span ><span class="bold" style="text-transform: capitalize;">' . _l('ch_number_code') . ':</span> ' . $numbercode . '</span><br><span ><span class="bold">' . _l('date') . ':</span> ' . _d($date) . '</span><br>'; ?>
		<?php
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '
                <span ><span class="bold" style="text-transform: capitalize;">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>
          ';
		}
		if ($tranfer['productions_capacity_id'] > 0) {
			$tbProductionsOrders_text = '';
			$tbProductionsOrders = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                WHERE tbl_productions_orders_items.plan_id = " . $tranfer['productions_capacity_id'] . "
                GROUP BY tbl_productions_orders_items.plan_id
            ) ";
			$ProductionsOrders = $this->db->query($tbProductionsOrders)->row();
			if (!empty($ProductionsOrders)) {
				$tbProductionsOrders_text = $ProductionsOrders->reference_no;
			}
			$productions_plan = get_table_where('tbl_productions_plan', array('id' => $tranfer['productions_capacity_id']), '', 'row');
			$tbOrdersItemsShipping = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no,'___', DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%d/%m/%Y'))) as date_shipping
            FROM tbl_order_item_shippings
            JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
            JOIN tbl_orders ON tbl_orders.id = tbl_order_items.order_id
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_orders_items_shipping";
			$tbBusinessItemsShipping = "(
            SELECT
                tbl_business_plan_items_date.business_plan_items_id as business_plan_items_id,
                GROUP_CONCAT(CONCAT(tbl_business_plan.reference_no,'___', DATE_FORMAT(tbl_business_plan_items_date.date, '%d/%m/%Y'))) as date_shipping
            FROM tbl_business_plan_items_date
            JOIN tbl_business_plan_items ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
            JOIN tbl_business_plan ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
            GROUP BY tbl_business_plan_items_date.business_plan_items_id
        ) tb_business_items_shipping";
			$productions_capacity_id = $tranfer['productions_capacity_id'];
			$tbDateDelivery = "(
            SELECT
                tbl_productions_plan_items.productions_plan_id as plan_id,
                GROUP_CONCAT(tb_orders_items_shipping.date_shipping) as date_order,
                GROUP_CONCAT(tb_business_items_shipping.date_shipping) as date_business
            FROM tbl_productions_plan_items
            LEFT JOIN $tbOrdersItemsShipping ON tb_orders_items_shipping.order_item_id = tbl_productions_plan_items.item_object_id AND tbl_productions_plan_items.type_object = 'orders'
            LEFT JOIN $tbBusinessItemsShipping ON tb_business_items_shipping.business_plan_items_id = tbl_productions_plan_items.item_object_id AND tbl_productions_plan_items.type_object = 'business_plan'
            WHERE tbl_productions_plan_items.productions_plan_id = $productions_capacity_id
        )";
			$date_ship = $this->db->query($tbDateDelivery)->result_array();
			$string_date = [];
			foreach ($date_ship as $key => $value) {
				if (!empty($value['date_order'])) {
					$string_date[] = str_replace('___', ' - ', $value['date_order']);
				}
				if (!empty($value['date_business'])) {
					$string_date[] = str_replace('___', ' - ', $value['date_business']);
				}
			}
			echo '<span class="bold">' . _l('Số Kế Hoạch NPL') . ':</span> ' . $productions_plan->reference_no . '<br>';
			echo '<span class="bold" style="text-transform: capitalize;">' . _l('Lệnh sản xuất tổng') . ':</span> ' . ($tbProductionsOrders_text) . '<br>';
			echo '<span class="bold">' . _l('Ngày giao hàng dự kiến') . ':</span> ' . (implode(', <br/>', $string_date)) . '<br>';
		}
		?>
		<?php
		echo '<span ><span class="bold" style="text-transform: capitalize;">' . _l('note') . ':</span> ' . ($tranfer['note']) . '</span>
            </div>'; ?>
		<?php
		$totalQuantity = 0;
		$total = 0;
		$width1 = 'width: 4%;';
		$width2 = 'width: 12%;';
		$width3 = 'width: 12%;';
		$width4 = 'width: 12%;';
		$width5 = 'width: 12%;';
		$width6 = 'width: 11%;';
		$width7 = 'width: 4%;';
		$width8 = 'width: 5%;';
		$width9 = 'width: 5%;';
		$width10 = 'width: 5%;';
		$width11 = 'width: 5%;';
		$width12 = 'width: 8%;';
		$width13 = 'width: 5%;';
		?>
        <div></div>
        <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <thead>
            <tr nobr="true" class="text-center bold">
				<th colspan="1" style="<?= $width1 ?>"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="<?= $width2 ?>">Vị Trí<br>Kho Hàng</th>
                <th colspan="1" style="<?= $width3 ?>">Vị Trí Kho<br>Chuyển Đến</th>
                <th colspan="1" style="<?= $width4 ?>"><?php echo _l('Mã Hàng Hóa'); ?></th>
                <th colspan="1" style="<?= $width5 ?>"><?php echo _l('Tên Hàng Hóa'); ?></th>
                <th colspan="1" style="<?= $width6 ?>">Kích Thước NPL<br>(Cao x Ngang)</th>
                <th colspan="1" style="<?= $width7 ?>"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="<?= $width8 ?>"><?php echo _l('SL Tồn'); ?></th>
                <th colspan="1" style="<?= $width9 ?>"><?php echo _l('SL Thực Xuất'); ?></th>
                <th colspan="1" style="<?= $width10 ?>"><?php echo _l('SL Bù Hao'); ?></th>
                <th colspan="1" style="<?= $width11 ?>"><?php echo _l('Tổng SL Xuất'); ?></th>
                <th colspan="1" style="<?= $width12 ?>"><?php echo _l('Tổng Chiều Cao Giấy Xuất'); ?></th>
                <th colspan="1" style="<?= $width13 ?>"><?php echo _l('SL Còn Lại'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($items->items as $key => $value) { ?>
                <tr style="font-size: 10px;">
                    <td class="text-center" style="<?= $width1 ?>">
						<?php echo($key + 1); ?>
                    </td>
					<td style="<?= $width2 ?>">
						-<?php echo $value['warehouse_name_id']; ?><br>
                        -<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;">
							<?= _l('Lot') ?>: <?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
					<td style="<?= $width3 ?>">
						-<?php echo $value['warehouse_name_to']; ?><br>
                        -<?php echo $value['localtion_name_to']; ?>
                        <div style="font-size: 10px;font-style: italic;">
							<?= _l('Lot') ?>: <?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
                    <td class="text-left" style="<?= $width4 ?>"><?= $value['code_item'] ?>
                    </td>
                    <td style="<?= $width5 ?>"><?php echo $value['name_item']; ?>
                    </td>
					<td class="text-center" style="<?= $width6 ?>"><?php echo $value['height'] . ' x ' . $value['wide']; ?></td>
					<td style="<?= $width7 ?>" class="text-center">
						<?php echo $value['unit_name_stock']; ?>
                    </td>
					<td class="text-center" style="<?= $width8 ?>">
						<?php $sumquanliti = $this->sumquanliti_order($tranfer['order_id_new'], $value['id_items'], $value['type']) ?>
						<?php echo formatNumber($sumquanliti); ?>
                    </td>
					<td class="text-center" style="<?= $width9 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
					<td class="text-center" style="<?= $width10 ?>"></td>
					<td class="text-center" style="<?= $width11 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>

					<td class="text-center" style="<?= $width12 ?>">
						<?php $totalHeight = ''; if (!empty($value['height'])) {
							$totalHeight = formatNumber((float)$value['height'] * (float)$value['quantity_net']);
						}?>
						<?php echo $totalHeight ?>
					</td>
                    
                    <td class="text-center" style="<?= $width13 ?>">
						<?php $sumquanlitiTransfer = $this->sumquanlitiTransfer_order($tranfer['order_id_new'], $value['id_items'], $value['type']) ?>
						<?php echo formatNumber(($sumquanliti - $sumquanlitiTransfer)); ?>
                    </td>
                </tr>
				<?php
				$totalQuantity += $value['quantity_net'];
				$total += $value['amount'];
			} ?>
            </tbody>
        </table>
		<?php
		echo '
            <p class="text-right"><span>Ngày ' . date("d") . ' tháng ' . date("m") . ' năm ' . date("Y") . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$pdf = print_pdf_tnhs($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}

	public function productionsPlanCompensation($productions_plan_id, $item_id, $item_type)
	{
		$this->db->select('
            tbl_productions_plan_compensation.*
        ', false);
		$this->db->from('tbl_productions_plan_compensation');
		$this->db->where('tbl_productions_plan_compensation.productions_plan_id', $productions_plan_id);
		$this->db->where('tbl_productions_plan_compensation.item_id', $item_id);
		$this->db->like('tbl_productions_plan_compensation.item_type', $item_type);
		return $this->db->get()->row_array();
	}

	public function sumquanliti($productions_capacity_id = 0, $id_items = 0, $type = '')
	{
		$quanliti = 0;
		if ($type == 'nvl') {
			$productionsPlanMaterials = $this->viewProductionsPlanMaterials($productions_capacity_id, $id_items, $type);
			$productionsPlanCompensation = $this->productionsPlanCompensation($productions_capacity_id, $id_items, 'materials');
			$productionsPlanMaterials['quantity_primary'] = $productionsPlanMaterials['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary'];
			$quanliti = $productionsPlanMaterials['quantity_primary'] / $productionsPlanMaterials['exchange_standard_unit'] * $productionsPlanMaterials['exchange_unit'];
		} elseif ($type == 'product') {
			$productionsPlanProducts = $this->viewProductionsPlanProducts($productions_capacity_id, $id_items, $type);
			$productionsPlanCompensation = $this->productionsPlanCompensation($productions_capacity_id, $id_items, 'products');
			$quanliti = $productionsPlanProducts['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary'];
		}
		// return round($quanliti);
		return ceil($quanliti);
	}

	public function sumquanlitiTransfer($productions_plan_id, $id_items, $type)
	{
		$tbTransferWarehouse = "(
            SELECT
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type = '" . $type . "' AND tbltransfer_warehouse.productions_capacity_id = " . $productions_plan_id . " AND tbltransfer_warehouse_detail.id_items = " . $id_items . "
        )";
		$dtKeep = $this->db->query($tbTransferWarehouse)->row_array();
		$quantityKeep = (float)$dtKeep['quantity_net'];
		return round($quantityKeep);
	}

	public function viewProductionsPlanProducts($productions_plan_id, $id_items)
	{
		$this->db->select('
            tbl_products.images as images,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_productions_plan_bom.item_id,
            SUM(tbl_productions_plan_bom.quantity_primary) as quantity_primary,
            tblunits.unit as unit_name,
            tbl_productions_plan_bom.item_type as item_type
        ', false);
		$this->db->from('tbl_productions_plan_bom');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_bom.item_id');
		$this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
		$this->db->where_in('tbl_productions_plan_bom.item_type', ['semi_products', 'semi_products_outside']);
		$this->db->where('tbl_productions_plan_bom.productions_plan_id', $productions_plan_id);
		$this->db->where('tbl_productions_plan_bom.item_id', $id_items);
		$this->db->group_by('tbl_productions_plan_bom.item_id');
		$productions_plan_bom = $this->db->get()->row_array();
		return $productions_plan_bom;
	}

	public function viewProductionsPlanMaterials($productions_plan_id, $id_items)
	{
		// SUM(tblwarehouse_items.product_quantity) as product_quantity
		// SUM(tblwarehouse_items.product_quantity_unit) as product_quantity
		$tbWarehouse = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . "
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";
		$this->db->select('
            tbl_productions_plan_bom.item_id as item_id,
            tbl_productions_plan_bom.id as id,
            tbl_materials.code as item_code,
            tbl_materials.name as item_name,
            SUM(tbl_productions_plan_bom.quantity_primary) as quantity_primary,
            SUM(tbl_productions_plan_bom.quantity) as quantity,
            SUM(tbl_productions_plan_bom.quantity_compensation) as quantity_compensation,
            SUM(tbl_productions_plan_bom.quantity_compensation_primary) as quantity_compensation_primary,
            SUM(tbl_productions_plan_bom.quantity_compensation_sm_primary) as quantity_compensation_sm_primary,
            tblunits.unit as unit_name,
            unit_primary.unit as unit_primary_name,
            tb_quantity_warehouse.product_quantity as quantity_inventory,
            tbl_materials.images as images,
            unit_stock.unit as unit_name_stock,
            tbl_materials.exchange_standard_unit as exchange_standard_unit,
            tbl_materials.exchange_unit as exchange_unit
        ', false);
		$this->db->from('tbl_productions_plan_bom');
		$this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id');
		$this->db->join('tblunits', 'tblunits.unitid = tbl_productions_plan_bom.unit_id', 'left');
		$this->db->join('tblunits unit_primary', 'unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id', 'left');
		$this->db->join('tblunits unit_stock', 'unit_stock.unitid = tbl_materials.standard_unit', 'left');
		$this->db->join($tbWarehouse, 'tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id', 'left');
		$this->db->where('tbl_productions_plan_bom.productions_plan_id', $productions_plan_id);
		$this->db->where('tbl_productions_plan_bom.item_id', $id_items);
		$this->db->where_in('tbl_productions_plan_bom.item_type', ['materials']);
		$this->db->group_by('tbl_productions_plan_bom.item_id');
		return $this->db->get()->row_array();
	}

	function change_status_active_transfer()
	{
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		if (!empty($id) && is_numeric($status)) {
			$this->db->where('id', $id);
			$transfer = $this->db->get('tbltransfer_warehouse')->row();
			if ($transfer->status_active_transfer == $status) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => 'Phiếu đã thay đổi trạng thái trước đó vui lòng kiểm tra lại'
				]);
				die();
			}
			if ($status == 1) {
				$this->db->where('id', $id);
				$success = $this->db->update('tbltransfer_warehouse', [
					'status_active_transfer' => 1,
					'staff_acvive_transfer' => get_staff_user_id(),
					'date_active_transfer' => date('Y-m-d H:i:s')
				]);
			} else if ($status == 0) {
				$this->db->where('id', $id);
				$success = $this->db->update('tbltransfer_warehouse', [
					'status_active_transfer' => 0,
					'staff_acvive_transfer' => NULL,
					'date_active_transfer' => NULL
				]);
			}
		}
		if (!empty($success)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Cập nhật trạng thái thành công'
			]);
			die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Cập nhật trạng thái không thành công'
		]);
		die();
	}
	// public function searchproductions_data($q, $limit)
	// {
	//     $this->db->select('tbl_productions_plan.id as id, tbl_productions_plan.reference_no as text', false);
	//     $this->db->from('tbl_productions_plan');
	//     if (!empty($q)) {
	//         $this->db->group_start();
	//         $this->db->like('tbl_productions_plan.reference_no', $q);
	//         $this->db->or_like('tbl_productions_plan.reference_no', $q);
	//         $this->db->or_like('tbl_productions_plan.note', $q);
	//         $this->db->group_end();
	//     }
	//     $this->db->limit($limit);
	//     return $this->db->get()->result_array();
	// }
	// public function searchproductions()
	// {
	//     $data = [];
	//     $term = $this->input->get('term');
	//     $limit = 50;
	//     $data['results'] = $this->searchproductions_data($term, $limit);
	//     echo json_encode($data);
	// }
	public function searchproductionss($q, $limit = 50)
	{
		$this->db->select('tbl_productions_plan.id as id, tbl_productions_plan.reference_no as text', false);
		$this->db->from('tbl_productions_plan');
		if (!empty($q)) {
			$this->db->group_start();
			$this->db->like('tbl_productions_plan.reference_no', $q);
			$this->db->or_like('tbl_productions_plan.reference_no', $q);
			$this->db->group_end();
		}
		$this->db->limit($limit);
		return $this->db->get()->result_array();
	}

	public function searchproductions($id = false)
	{
		$data = [];
		$term = $this->input->get('term');
		$limit = 50;
		$data['results'] = $this->searchproductionss($term, $limit);
		echo json_encode($data);
	}

	public function searchtransfers($q, $limit = 50)
	{
		$this->db->select('tbltransfer_warehouse.id as id, CONCAT(prefix,"-",code) as text', false);
		$this->db->from('tbltransfer_warehouse');
		if (!empty($q)) {
			$this->db->group_start();
			$this->db->like('CONCAT(prefix,"-",code)', $q);
			$this->db->group_end();
		}
		$this->db->limit($limit);
		return $this->db->get()->result_array();
	}

	public function searchtransfer($id = false)
	{
		$data = [];
		$term = $this->input->get('term');
		$limit = 50;
		$data['results'] = $this->searchtransfers($term, $limit);
		echo json_encode($data);
	}

	public function print_transfer_product_old($id)
	{
		ob_end_clean();
		$data = [];
		$this->db->select('*');
		$this->db->from('tbltransfer_warehouse');
		$this->db->where('id', $id);
		$tranfer = $this->db->get()->row_array();
		// $id_branch = get_staff_user_id_branch_v3();
		$id_branch_take = '5';
		$numbercode = $tranfer['prefix'] . '-' . $tranfer['code'];
		$date = $tranfer['date'];
		$items = $this->transfer_model->get($id);
		$number_print = GetNumberPrint($id, '1');
		$text_number = '<div class="text-left" style="font-size: 9px">
                            <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
                            <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
		$data['number_print'] = $text_number;
		$data['title'] = lang('sum_print_transfers');
		$data['type'] = 'P';
		$data['img'] = '';
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <h1 class="text-center uppercase">' . lang('sum_transfers') . '</h1>
            <div class="text-left"><span ><span class="bold">' . _l('ch_number_code') . ':</span> ' . $numbercode . '</span><br><span ><span class="bold">' . _l('date') . ':</span> ' . _d($date) . '</span><br>'; ?>
		<?php
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '<span ><span class="bold">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>';
			$this->db->select('GROUP_CONCAT(DATE_FORMAT(date_shipping, "%d/%m/%Y")) as list_date');
			$this->db->from('tbl_order_item_shippings');
			$this->db->where('tbl_order_items.order_id', $tranfer['order_id_new']);
			$this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
			$sub_date = $this->db->get()->row('list_date');
			echo '<span ><span class="bold">' . _l('Ngày giao hàng dự kiến') . ':</span> ' . $sub_date . '</span><br>';
		}
		if ($tranfer['productions_capacity_id'] > 0) {
			$tbProductionsOrders_text = '';
			$tbProductionsOrders = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                WHERE tbl_productions_orders_items.plan_id = " . $tranfer['productions_capacity_id'] . "
                GROUP BY tbl_productions_orders_items.plan_id
            ) ";
			$ProductionsOrders = $this->db->query($tbProductionsOrders)->row();
			if (!empty($ProductionsOrders)) {
				$tbProductionsOrders_text = $ProductionsOrders->reference_no;
			}
			$productions_plan = get_table_where('tbl_productions_plan', array('id' => $tranfer['productions_capacity_id']), '', 'row');
			echo '<span ><span class="bold">' . _l('Số kế hoạch NPL') . ':</span> ' . $productions_plan->reference_no . '</span><br><span ><span class="bold">' . _l('Lệnh sản xuất tổng') . ':</span> ' . ($tbProductionsOrders_text) . '</span><br>
          ';
		}
		?>
		<?php
		echo '<span ><span class="bold">' . _l('note') . ':</span> ' . ($tranfer['note']) . '</span>
            </div>'; ?>
		<?php
		$totalQuantity = 0;
		$total = 0;
		$width1 = 'width: 6%;';
		$width2 = 'width: 12%;';
		$width3 = 'width: 18%;';
		$width4 = 'width: 18%;';
		$width5 = 'width: 7%;';
		$width6 = 'width: 12%;';
		$width7 = 'width: 12%;';
		$width8 = 'width: 15%;';
		?>
        <div></div>
        <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <thead>
            <tr nobr="true" class="text-center bold">
                <th colspan="1" style="<?= $width1 ?>"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="<?= $width2 ?>"><?php echo _l('Mã hàng'); ?></th>
                <th colspan="1" style="<?= $width3 ?>"><?php echo _l('ch_items_name_t'); ?></th>
                <th colspan="1" style="<?= $width4 ?>"><?php echo _l('Kho hàng'); ?></th>
                <th colspan="1" style="<?= $width5 ?>"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="<?= $width6 ?>"><?php echo _l('Số lượng đặt'); ?></th>
                <th colspan="1" style="<?= $width7 ?>"><?php echo _l('Số lượng xuất'); ?></th>
                <th colspan="1" style="<?= $width8 ?>"><?php echo _l('Còn lại'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($items->items as $key => $value) { ?>
                <tr style="font-size: 10px;">
                    <td class="text-center" style="<?= $width1 ?>">
						<?php echo($key + 1); ?>
                    </td>
                    <td class="text-left" style="<?= $width2 ?>"><?= $value['code_item'] ?>
                    </td>
                    <td style="<?= $width3 ?>"><?php echo $value['name_item']; ?>
                    </td>
                    <td style="<?= $width4 ?>">-<?php echo $value['warehouse_name_id']; ?>
                        <br>-<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;"><?= _l('Lot') ?>:<?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
                    <td style="<?= $width5 ?>" class="text-center">
						<?php echo $value['unit']; ?>
                    </td>
                    <td class="text-center" style="<?= $width6 ?>">
						<?php
						$sumquanliti = $this->sumquanliti_order($tranfer['order_id_new'], $value['id_items'], $value['type'])
						?>
						<?php echo formatNumber($sumquanliti); ?>
                    </td>
                    <td class="text-center" style="<?= $width7 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
                    <td class="text-center" style="<?= $width8 ?>">
						<?php
						$sumquanlitiTransfer = $this->sumquanlitiTransfer_order($tranfer['order_id_new'], $value['id_items'], $value['type'])
						?>
						<?php echo formatNumber(($sumquanliti - $sumquanlitiTransfer)); ?>
                    </td>
                </tr>
				<?php
				$totalQuantity += $value['quantity_net'];
				$total += $value['amount'];
			} ?>
            </tbody>
        </table>
		<?php
		echo '
            <p class="text-right"><span>Ngày ' . date("d") . ' tháng ' . date("m") . ' năm ' . date("Y") . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$pdf = print_pdf_tnhs($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}
	public function print_transfer_product($id)
	{
		ob_end_clean();
		$data = [];
		$this->db->select('*');
		$this->db->from('tbltransfer_warehouse');
		$this->db->where('id', $id);
		$tranfer = $this->db->get()->row_array();
		// $id_branch = get_staff_user_id_branch_v3();
		$id_branch_take = '5';
		$numbercode = $tranfer['prefix'] . '-' . $tranfer['code'];
		$date = $tranfer['date'];
		$items = $this->transfer_model->get($id);
		$number_print = GetNumberPrint($id, '1');
		$text_number = '<div class="text-left" style="font-size: 9px">
                            <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
                            <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
		$data['number_print'] = $text_number;
		$data['title'] = lang('sum_print_transfers');
		$data['type'] = 'L';
		$data['img'] = '';
		$message = "";
		ob_start();
		stylePdf();
		echo '
            <h1 class="text-center uppercase" style="text-transform: capitalize;">' . lang('sum_transfers') . '</h1>
            <div class="text-left"><span ><span class="bold" style="text-transform: capitalize;">' . _l('ch_number_code') . ':</span> ' . $numbercode . '</span><br><span ><span class="bold">' . _l('date') . ':</span> ' . _d($date) . '</span><br>'; ?>
		<?php
		// echo '<span ><span class="bold">' . _l('Số Kế Hoạch NPL') . ':</span> ' . '' . '</span><br>';
			// <span ><span class="bold">' . _l('Lệnh Sản Xuất Tổng') . ':</span> ' . '' . '</span><br>'; // Chưa có
		
		if ($tranfer['order_id_new'] > 0) {
			$orders = get_table_where('tbl_orders', array('id' => $tranfer['order_id_new']), '', 'row');
			echo '<span ><span class="bold" style="text-transform: capitalize;">' . _l('Đơn hàng bán') . ':</span> ' . $orders->reference_no . '</span><br>';
			$this->db->select('GROUP_CONCAT(DATE_FORMAT(date_shipping, "%d/%m/%Y")) as list_date');
			$this->db->from('tbl_order_item_shippings');
			$this->db->where('tbl_order_items.order_id', $tranfer['order_id_new']);
			$this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
			$sub_date = $this->db->get()->row('list_date');
			echo '<span ><span class="bold" style="text-transform: capitalize;">' . _l('Ngày giao hàng dự kiến') . ':</span> ' . $sub_date . '</span><br>';
		}
		if ($tranfer['productions_capacity_id'] > 0) {
			$tbProductionsOrders_text = '';
			$tbProductionsOrders = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                WHERE tbl_productions_orders_items.plan_id = " . $tranfer['productions_capacity_id'] . "
                GROUP BY tbl_productions_orders_items.plan_id
            ) ";
			$ProductionsOrders = $this->db->query($tbProductionsOrders)->row();
			if (!empty($ProductionsOrders)) {
				$tbProductionsOrders_text = $ProductionsOrders->reference_no;
			}
			$productions_plan = get_table_where('tbl_productions_plan', array('id' => $tranfer['productions_capacity_id']), '', 'row');
			echo '<span ><span class="bold">' . _l('Số Kế Hoạch NPL') . ':</span> ' . $productions_plan->reference_no . '</span><br><span ><span class="bold">' . _l('Lệnh Sản Xuất Tổng') . ':</span> ' . ($tbProductionsOrders_text) . '</span><br>
          ';
		}
		?>
		<?php
		echo '<span ><span class="bold" style="text-transform: capitalize;">' . _l('note') . ':</span> ' . ($tranfer['note']) . '</span>
            </div>'; ?>
		<?php
		$totalQuantity = 0;
		$total = 0;
		$width1 = 'width: 4%;';
		$width2 = 'width: 12%;';
		$width3 = 'width: 12%;';
		$width4 = 'width: 12%;';
		$width5 = 'width: 12%;';
		$width6 = 'width: 11%;';
		$width7 = 'width: 4%;';
		$width8 = 'width: 5%;';
		$width9 = 'width: 5%;';
		$width10 = 'width: 5%;';
		$width11 = 'width: 5%;';
		$width12 = 'width: 8%;';
		$width13 = 'width: 5%;';
		?>
        <div></div>
        <table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <thead>
            <tr nobr="true" class="text-center bold">
                <th colspan="1" style="<?= $width1 ?>"><?php echo _l('STT'); ?></th>
                <th colspan="1" style="<?= $width2 ?>">Vị Trí<br>Kho Hàng</th>
                <th colspan="1" style="<?= $width3 ?>">Vị Trí Kho<br>Chuyển Đến</th>
                <th colspan="1" style="<?= $width4 ?>"><?php echo _l('Mã Hàng Hóa'); ?></th>
                <th colspan="1" style="<?= $width5 ?>"><?php echo _l('Tên Hàng Hóa'); ?></th>
                <th colspan="1" style="<?= $width6 ?>">Kích Thước NPL<br>(Cao x Ngang)</th>
                <th colspan="1" style="<?= $width7 ?>"><?php echo _l('tnh_dvt'); ?></th>
                <th colspan="1" style="<?= $width8 ?>"><?php echo _l('SL Tồn'); ?></th>
                <th colspan="1" style="<?= $width9 ?>"><?php echo _l('SL Thực Xuất'); ?></th>
                <th colspan="1" style="<?= $width10 ?>"><?php echo _l('SL Bù Hao'); ?></th>
                <th colspan="1" style="<?= $width11 ?>"><?php echo _l('Tổng SL Xuất'); ?></th>
                <th colspan="1" style="<?= $width12 ?>"><?php echo _l('Tổng Chiều Cao Giấy Xuất'); ?></th>
                <th colspan="1" style="<?= $width13 ?>"><?php echo _l('SL Còn Lại'); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($items->items as $key => $value) { ?>
                <tr style="font-size: 10px;">
                    <td class="text-center" style="<?= $width1 ?>">
						<?php echo($key + 1); ?>
                    </td>

					<td style="<?= $width2 ?>">
						-<?php echo $value['warehouse_name_id']; ?><br>
                        -<?php echo $value['localtion_name_id']; ?>
                        <div style="font-size: 10px;font-style: italic;">
							<?= _l('Lot') ?>: <?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>
					
					<td style="<?= $width3 ?>">
						-<?php echo $value['warehouse_name_to']; ?><br>
                        -<?php echo $value['localtion_name_to']; ?>
                        <div style="font-size: 10px;font-style: italic;">
							<?= _l('Lot') ?>: <?= $value['lot_code'] ?>
							<?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
							<?php } ?>
                        </div>
                    </td>

                    <td class="text-left" style="<?= $width4 ?>"><?= $value['code_item'] ?></td>
                    <td style="<?= $width5 ?>"><?php echo $value['name_item']; ?></td>
                    
					<td class="text-center" style="<?= $width6 ?>"><?php echo $value['height'] . ' x ' . $value['wide']; ?></td>
                    
                    <td style="<?= $width7 ?>" class="text-center">
						<?php echo $value['unit']; ?>
                    </td>
					
                    <td class="text-center" style="<?= $width8 ?>">
						<?php $sumquanliti = $this->sumquanliti_order($tranfer['order_id_new'], $value['id_items'], $value['type']) ?>
						<?php echo formatNumber($sumquanliti); ?>
                    </td>
					<td class="text-center" style="<?= $width9 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>
					<td class="text-center" style="<?= $width10 ?>"></td>
					<td class="text-center" style="<?= $width11 ?>">
						<?php echo formatNumber($value['quantity_net']); ?>
                    </td>

					<td class="text-center" style="<?= $width12 ?>">
						<?php $totalHeight = ''; if (!empty($value['height'])) {
							$totalHeight = formatNumber((float)$value['height'] * (float)$value['quantity_net']);
						}?>
						<?php echo $totalHeight ?>
					</td>
                    
                    <td class="text-center" style="<?= $width13 ?>">
						<?php $sumquanlitiTransfer = $this->sumquanlitiTransfer_order($tranfer['order_id_new'], $value['id_items'], $value['type']) ?>
						<?php echo formatNumber(($sumquanliti - $sumquanlitiTransfer)); ?>
                    </td>
                </tr>
				<?php
				$totalQuantity += $value['quantity_net'];
				$total += $value['amount'];
			} ?>
            </tbody>
        </table>
		<?php
		echo '
            <p class="text-right"><span>Ngày ' . date("d") . ' tháng ' . date("m") . ' năm ' . date("Y") . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';
		$content = ob_get_contents();
		ob_end_clean();
		$data['content'] = $content;
		$pdf = print_pdf_tnhs($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}

	public function sumquanliti_order($order_id = 0, $id_items = 0, $type = '')
	{
		if ($type == 'product') {
			$type = 'products';
		} else
			if ($type == 'nvl') {
				$type = 'materials';
			} else
				if ($type == 'tools') {
					$type = 'tools_supplies';
				}
		$quanliti = 0;
		$this->db->select('SUM(total_quantity_item) as quantity');
		$this->db->where('order_id', $order_id);
		$this->db->where('item_id', $id_items);
		$this->db->like('type_item', $type);
		$this->db->group_by('item_id,type_item');
		$quanliti = $this->db->get('tbl_order_items')->row()->quantity ?? 0;
		return round($quanliti);
	}

	public function sumquanlitiTransfer_order($order_id, $id_items, $type)
	{
		$tbTransferWarehouse = "(
            SELECT
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type = '" . $type . "' AND tbltransfer_warehouse.order_id_new = " . $order_id . " AND tbltransfer_warehouse_detail.id_items = " . $id_items . "
        )";
		$dtKeep = $this->db->query($tbTransferWarehouse)->row_array();
		$quantityKeep = (float)$dtKeep['quantity_net'];
		return round($quantityKeep);
	}

    public function searchPurchaseProduct(){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_purchase_products.id as id, 
            tbl_purchase_products.reference_no as text
        ', false);
        $this->db->from('tbl_purchase_products');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_purchase_products.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $business_plan = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Nhập kho thành phẩm'), 'children' => $business_plan];
        echo json_encode($data);
    }

    public function searchTranferBusiness(){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_tranfer_business.id as id, 
            tbl_tranfer_business.reference_no as text
        ', false);
        $this->db->from('tbl_tranfer_business');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_tranfer_business.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $business_plan = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Giữ kho (Trên chuyền)'), 'children' => $business_plan];
        echo json_encode($data);
    }
}
