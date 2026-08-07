<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggestion extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->perViewsuggestion = has_permission('suggestion', '', 'view');
		$this->perViewOwnsuggestion = has_permission('suggestion', '', 'view_own');
		$this->perAddsuggestion = has_permission('suggestion', '', 'create');
		$this->perEditsuggestion = has_permission('suggestion', '', 'edit');
		$this->perDeletesuggestion = has_permission('suggestion', '', 'delete');
		$this->perApprovesuggestion = has_permission('suggestion', '', 'approve_accept');
	}

	public function index()
	{
		if (!$this->perViewsuggestion && !$this->perViewOwnsuggestion) {
			access_denied('suggestion');
		}
		$data['title'] = _l('ch_suggestion');
		$this->load->view('admin/suggestion/manage', $data);
	}

	public function view_modal($id = '')
	{
		$data['items'] = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
		$type[1] = 'Mua vật tư';
		$type[2] = 'Tạm ứng';
		$type[3] = 'Thanh toán';
		$type[4] = 'Tạm ứng & Thanh toán';
		$status[1] = 'Gấp';
		$status[2] = 'Bình thường';
		$data['items']->type = $type[$data['items']->type];
		$data['items']->status = $status[$data['items']->status];
		$data['items']->item = get_table_where('tblsuggestion_detal', array('id_suggestion' => $id));
		$data['items']->file = get_table_where('tblfiles', array('rel_id' => $id, 'rel_type' => 'suggestion'));
		$this->load->view('admin/suggestion/view_modal', $data);
	}

	public function detail($id = '')
	{
		if (!has_permission('suggestion', '', 'create') && !has_permission('suggestion', '', 'edit')) {
			access_denied('suggestion');
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($id == '') {
				if (!has_permission('suggestion', '', 'create')) {
					$success = false;
					$alert_type = 'danger';
					$message = _l('Bạn không có quyền thêm');
					echo json_encode(array(
						'success' => $success,
						'alert_type' => $alert_type,
						'message' => $message
					));
					die;
				}
				if ($data['type'] == 1) {
					$items = $data['items'];
					$check = 0;
					foreach ($items as $key => $value) {
						if (!empty($value['ustom_item_select'])) {
							$check++;
						}
					}
					if ($check == 0) {
						$success = false;
						$alert_type = 'danger';
						$message = _l('Vui lòng chọn vật tư');
						echo json_encode(array(
							'success' => $success,
							'alert_type' => $alert_type,
							'message' => $message
						));
						die;
					}
				}
				$ins = array();
				$ins['date'] = to_sql_date($data['date']) . ' ' . date('H:i:s');
				$ins['code'] = 'DX-' . sprintf('%06d', ch_getMaxID('id', 'tblsuggestion') + 1);
				$ins['type'] = ($data['type']);
				$ins['status'] = ($data['status']);
				$ins['note'] = ($data['note']);
				$ins['staffid'] = ($data['staffid']);
				$ins['staff_create'] = (get_staff_user_id());
				$ins['date_create'] = (date('Y-m-d H:i:s'));
				$ins['price_total'] = str_replace(',', '', $data['price']);

				$ins['staff_browse'] = !empty($data['staff_browse']) ? $data['staff_browse'] : NULL;
				$ins['id_payment_modes'] = !empty($data['id_payment_modes']) ? $data['id_payment_modes'] : 0;
				$this->db->insert('tblsuggestion', $ins);
				$idd = $this->db->insert_id();
				if (!empty($idd)) {
					$files = null;
					$this->load->library('upload');
					if (
						isset($_FILES['file']['name'])
						&& ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
					) {
						if (!is_array($_FILES['file']['name'])) {
							$_FILES['file']['name'] = [$_FILES['file']['name']];
							$_FILES['file']['type'] = [$_FILES['file']['type']];
							$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
							$_FILES['file']['error'] = [$_FILES['file']['error']];
							$_FILES['file']['size'] = [$_FILES['file']['size']];
						}
						// $path = get_upload_path_by_type('project') . $project_id . '/';
						$path = 'uploads/suggestion/' . $idd . '/';
						for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
							if (_perfex_upload_error($_FILES['file']['error'][$i])) {
								$errors[$_FILES['file']['name'][$i]] = _perfex_upload_error($_FILES['file']['error'][$i]);
								continue;
							}
							$tmpFilePath = $_FILES['file']['tmp_name'][$i];
							// Make sure we have a filepath
							if (!empty($tmpFilePath) && $tmpFilePath != '') {
								// _maybe_create_upload_path($path);
								if (!file_exists('uploads/suggestion/')) {
									mkdir('uploads/suggestion/', 0777);
								}
								if (!file_exists($path)) {
									mkdir($path, 0777);
									fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
								}
								$filename = vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
								// In case client side validation is bypassed
								if (!_upload_extension_allowed($filename)) {
									continue;
								}
								$newFilePath = $path . $filename;
								// Upload the file into the company uploads dir
								if (move_uploaded_file($tmpFilePath, $newFilePath)) {
									$typeFile = $_FILES['file']['type'][$i];
									if (file_exists($newFilePath)) {
										$this->db->insert('tblfiles', [
											'rel_id' => $idd,
											'rel_type' => 'suggestion',
											'file_name' => $filename,
											'filetype' => $typeFile,
											'staffid' => get_staff_user_id(),
											'dateadded' => date('Y-m-d H:i:s'),
										]);
									}
								}
							}
						}
					}
					if (!empty($data['items'])) {
						$items = $data['items'];
						foreach ($items as $key => $value) {
							$ins_item = array();
							$ins_item['id_suggestion'] = $idd;
							$ins_item['type'] = $value['type'];
							$ins_item['id_items'] = $value['ustom_item_select'];
							$ins_item['quantity'] = str_replace(',', '', $value['quanliti']);
							$ins_item['price'] = str_replace(',', '', $value['price']);
							$ins_item['amount'] = $ins_item['price'] * $ins_item['quantity'];
							if (!empty($value['ustom_item_select'])) {
								$this->db->insert('tblsuggestion_detal', $ins_item);
							}
						}
					}
					$success = true;
					$alert_type = 'success';
					$message = _l('ch_added_successfuly');
					echo json_encode(array(
						'success' => $success,
						'alert_type' => $alert_type,
						'message' => $message
					));
					die;
				}
			} else {
				if (!has_permission('suggestion', '', 'edit')) {
					$success = false;
					$alert_type = 'danger';
					$message = _l('Bạn không có quyền sửa');
					echo json_encode(array(
						'success' => $success,
						'alert_type' => $alert_type,
						'message' => $message
					));
					die;
				}
				if ($data['type'] == 1) {
					$items = $data['items'];
					$check = 0;
					foreach ($items as $key => $value) {
						if (!empty($value['ustom_item_select'])) {
							$check++;
						}
					}
					if ($check == 0) {
						$success = false;
						$alert_type = 'danger';
						$message = _l('Vui lòng chọn vật tư');
						echo json_encode(array(
							'success' => $success,
							'alert_type' => $alert_type,
							'message' => $message
						));
						die;
					}
				}
				$ins = array();
				$ins['type'] = ($data['type']);
				$ins['status'] = ($data['status']);
				$ins['note'] = ($data['note']);
				$ins['staffid'] = ($data['staffid']);
				$ins['price_total'] = str_replace(',', '', $data['price']);
				$ins['staff_browse'] = !empty($data['staff_browse']) ? $data['staff_browse'] : NULL;
				$ins['id_payment_modes'] = !empty($data['id_payment_modes']) ? $data['id_payment_modes'] : 0;


				$this->db->where('id', $id);
				$this->db->update('tblsuggestion', $ins);
				if (!empty($id)) {
					$this->db->where('id_suggestion', $id);
					$this->db->delete('tblsuggestion_detal');
					$files = null;
					$this->load->library('upload');
					if (
						isset($_FILES['file']['name'])
						&& ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
					) {
						if (!is_array($_FILES['file']['name'])) {
							$_FILES['file']['name'] = [$_FILES['file']['name']];
							$_FILES['file']['type'] = [$_FILES['file']['type']];
							$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
							$_FILES['file']['error'] = [$_FILES['file']['error']];
							$_FILES['file']['size'] = [$_FILES['file']['size']];
						}
						// $path = get_upload_path_by_type('project') . $project_id . '/';
						$path = 'uploads/suggestion/' . $id . '/';
						for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
							if (_perfex_upload_error($_FILES['file']['error'][$i])) {
								$errors[$_FILES['file']['name'][$i]] = _perfex_upload_error($_FILES['file']['error'][$i]);
								continue;
							}
							$tmpFilePath = $_FILES['file']['tmp_name'][$i];
							// Make sure we have a filepath
							if (!empty($tmpFilePath) && $tmpFilePath != '') {
								// _maybe_create_upload_path($path);
								if (!file_exists('uploads/suggestion/')) {
									mkdir('uploads/suggestion/', 0777);
								}
								if (!file_exists($path)) {
									mkdir($path, 0777);
									fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
								}
								$filename = time() . vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
								// In case client side validation is bypassed
								if (!_upload_extension_allowed($filename)) {
									continue;
								}
								$newFilePath = $path . $filename;
								// Upload the file into the company uploads dir
								if (move_uploaded_file($tmpFilePath, $newFilePath)) {
									$typeFile = $_FILES['file']['type'][$i];
									if (file_exists($newFilePath)) {
										$this->db->insert('tblfiles', [
											'rel_id' => $id,
											'rel_type' => 'suggestion',
											'file_name' => $filename,
											'filetype' => $typeFile,
											'staffid' => get_staff_user_id(),
											'dateadded' => date('Y-m-d H:i:s'),
										]);
									}
								}
							}
						}
					}
					if (!empty($data['items'])) {
						$items = $data['items'];
						foreach ($items as $key => $value) {
							$ins_item = array();
							$ins_item['id_suggestion'] = $id;
							$ins_item['type'] = $value['type'];
							$ins_item['id_items'] = $value['ustom_item_select'];
							$ins_item['quantity'] = str_replace(',', '', $value['quanliti']);
							$ins_item['price'] = str_replace(',', '', $value['price']);
							$ins_item['amount'] = $ins_item['price'] * $ins_item['quantity'];
							if (!empty($value['ustom_item_select'])) {
								$this->db->insert('tblsuggestion_detal', $ins_item);
							}
						}
					}
					$success = true;
					$alert_type = 'success';
					$message = _l('ch_updated_successfuly');
					echo json_encode(array(
						'success' => $success,
						'alert_type' => $alert_type,
						'message' => $message
					));
					die;
				}
			}
		} else {
			if ($id != '') {
				$data['invoice'] = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
				$data['invoice']->item = get_table_where('tblsuggestion_detal', array('id_suggestion' => $id));
				$data['invoice']->file = get_table_where('tblfiles', array('rel_id' => $id, 'rel_type' => 'suggestion'));
				$data['title'] = _l('ch_edit_suggestion');
			} else {
				$data['title'] = _l('ch_add_suggestion');
			}

			$data['payment_modes'] = $this->db->get('tblpayment_modes')->result_array();
		}
		$this->load->view('admin/suggestion/detail', $data);
	}

	public function delele_file($id = "")
	{
		if (is_numeric($id)) {
			$files = get_table_where('tblfiles', array('id' => $id), '', 'row');
			$this->db->where('id', $id);
			$this->db->delete('tblfiles');
			$path = 'uploads/suggestion/' . $files->rel_id . '/' . $files->file_name;
			if (file_exists($path)) {
				unlink($path);
			}
		}
	}

	public function SearchStaff($id = '')
	{
		$data = [];
		$search = $this->input->get('term');
		$limit_one = 20;
		$this->db->select(
			'
            tblstaff.staffid as id,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as text',
			false
		);
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('CONCAT(tblstaff.firstname," ",tblstaff.lastname)', $search);
			$this->db->group_end();
		}
		if (!empty($id)) {
			$this->db->where('tblstaff.staffid', $id);
		}
		$this->db->limit($limit_one);
		$suppliers = $this->db->get('tblstaff')->result_array();
		$data['results'] = $suppliers;
		echo json_encode($data);
		die();
	}

	public function SearchItems_ch($id = '', $types = '')
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
		if ($type == -1) {
			$this->db->select(
				'
                    id,
                    tblitems.name as text,
                    tblitems.code,
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
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                tbl_tools_supplies.price_import as price,
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
			$count_tools = count($tools);
			$this->db->select(
				'
                id as id,
                tbl_materials.name as text,
                tbl_materials.code,
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
			$this->db->limit(($limit_all - $count_tools - $count_items));
			$product = $this->db->get('tbl_materials')->result_array();
			if (!empty($product)) {
				$data['results'][] =
					[
						'text' => _l('Nguyên vật liệu'),
						'children' => $product
					];
			}
			$this->db->select(
				'
                id as id,
                tbl_products.name as text,
                tbl_products.code,
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
			// $this->db->where('tbl_products.type_products', 'semi_products_outside');
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
		} else
			if ($type == 'items') {
			$this->db->select(
				'
                    id as id,
                    tblitems.name as text,
                    tblitems.code,
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
					$this->db->where('tblitems.id', $id);
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
				'
                id as id,
                tbl_products.name as text,
                tbl_products.code,
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
			// $this->db->where('tbl_products.type_products', 'semi_products_outside');
			$this->db->order_by('tbl_products.name', 'DESC');
			$this->db->limit(50);
			// $this->db->limit(($limit_all - $count_product));
			$product = $this->db->get('tbl_products')->result_array();
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
                tbl_materials.name as text,
                tbl_materials.code,
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
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                tbl_tools_supplies.price_import as price,
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

	public function table_suggestion()
	{
		if ($this->input->is_ajax_request()) {
			$select = [
				'tblsuggestion.id as id',
				'tblsuggestion.date as date',
				'tblsuggestion.code as code',
				'tblsuggestion.type as type',
				'tblsuggestion.status as status',
				'tblpayment_modes.name as name_payment_modes',
				'CONCAT(tblstaff_browse.firstname," ",tblstaff_browse.lastname) as fullname_browse',

				'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
				'tblsuggestion.price_total as price_total',
				'tblsuggestion.payments as payments',
				'tblsuggestion.detail_suggest_muti_id as detail_suggest_muti_id',
				'tblsuggestion.status_dn as status_dn',
				'tblsuggestion.status_tp as status_tp',
				'tblsuggestion.treasurer as treasurer',
				'7',
			];
			$where = [];
			if (has_permission('suggestion', '', 'view_own') && !is_admin()) {
				array_push($where, 'AND (tblsuggestion.staff_create = ' . get_staff_user_id() . ' OR tblsuggestion.staffid = ' . get_staff_user_id() . ')');
			}
			$search_date = $this->input->post('search_date');
			if ($search_date) {
				$data_start = explode(' - ', $search_date);
				array_push($where, 'AND tblsuggestion.date BETWEEN "' . to_sql_date($data_start[0]) . ' 00:00:00" and "' . to_sql_date($data_start[1]) . ' 23:59:59"');
			}
			if ($this->input->post('filterStatus')) {
				if (is_numeric($this->input->post('filterStatus'))) {
					array_push($where, 'AND tblsuggestion.type = ' . $this->input->post('filterStatus'));
				}
			}
			if ($this->input->post('staffid')) {
				if (is_numeric($this->input->post('staffid'))) {
					array_push($where, 'AND tblsuggestion.staffid = ' . $this->input->post('staffid'));
				}
			}
			if ($this->input->post('status')) {
				if (is_numeric($this->input->post('status'))) {
					array_push($where, 'AND tblsuggestion.status = ' . $this->input->post('status'));
				}
			}
			if ($this->input->post('status_dn')) {
				if (is_numeric($this->input->post('status_dn'))) {
					array_push($where, 'AND tblsuggestion.status_dn = ' . ($this->input->post('status_dn') - 1));
				}
			}
			if ($this->input->post('status_tp')) {
				if (is_numeric($this->input->post('status_tp'))) {
					array_push($where, 'AND tblsuggestion.status_tp = ' . ($this->input->post('status_tp') - 1));
				}
			}
			// if ($this->input->post('status_tq')) {
			// 	if (is_numeric($this->input->post('status_tq'))) {
			// 		array_push($where, 'AND tblsuggestion.treasurer = ' . ($this->input->post('status_tq') - 1));
			// 	}
			// }
			if ($this->input->post('status_tq')) {
				$status_tq = $this->input->post('status_tq');
				if (is_numeric($status_tq)) {
					if (($status_tq == 2)) {
						array_push($where, 'AND tblsuggestion.treasurer > 0');
					} else {
						array_push($where, 'AND (tblsuggestion.treasurer is NULL or tblsuggestion.treasurer = 0)');
					}
				}
			}
			if ($this->input->post('filterCheckTotal')) {
				$filterCheckTotal = $this->input->post('filterCheckTotal');
				if ($filterCheckTotal == 'cash') {
					$where[] = 'AND tblpayment_modes.cash = 1';
					$where[] = 'AND tblsuggestion.treasurer IS NULL';
				} else {
					$where[] = 'AND tblpayment_modes.bank = 1';
					$where[] = 'AND tblsuggestion.treasurer IS NULL';
				}
			}
			$aColumns = $select;
			$sIndexColumn = 'id';
			$sTable = db_prefix() . 'suggestion';
			$join = [
				'LEFT JOIN tblstaff ON tblstaff.staffid = tblsuggestion.staffid',
				'LEFT JOIN tblstaff tblstaff_browse ON tblstaff_browse.staffid = tblsuggestion.staff_browse',
				'LEFT JOIN tblpayment_modes ON tblpayment_modes.id = tblsuggestion.id_payment_modes',
			];
			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
				'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
				'tblsuggestion.price_total',
				'tblsuggestion.note',
				'tblsuggestion.staff_status_dn',
				'tblsuggestion.date_status_dn',
				'tblsuggestion.staff_status_tp',
				'tblsuggestion.date_status_tp',
				'tblsuggestion.staffid',
				'tblsuggestion.staff_browse',
				'tblsuggestion.treasurer_time',
				'tblsuggestion.id_internal_proposal',
				'(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id_suggestion = tblsuggestion.id LIMIT 1) as internal_proposal',
				'(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id = tblsuggestion.id_internal_proposal LIMIT 1) as internal_proposalv2',
				'(SELECT CONCAT(tblpurchase_order.id, "__", tblpurchase_order.prefix,"-",tblpurchase_order.code) FROM tblpurchase_order WHERE tblpurchase_order.id = tblsuggestion.purchase_order_id LIMIT 1) as purchase_order'
			]);
			$output = $result['output'];
			$rResult = $result['rResult'];
			$j = 0;
			$type_text[1] = 'Mua vật tư';
			$type_text[2] = 'Tạm ứng';
			$type_text[3] = 'Thanh toán';
			$type_text[4] = 'Tạm ứng & Thanh toán';
			$status_text[1] = 'Gấp';
			$status_text[2] = 'Bình thường';
			$footer_data = array(
				'total' => 0,
				'pay' => 0,
			);
			foreach ($rResult as $aRow) {
				$row = array();
				$j++;
				for ($i = 0; $i < count($aColumns); $i++) {
					if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
						$_data = $aRow[strafter($aColumns[$i], 'as ')];
					} else {
						$_data = $aRow[$aColumns[$i]];
					}
					if ($aColumns[$i] == 'tblsuggestion.id as id') {
						$_data = $j;
					}
					if ($aColumns[$i] == 'tblsuggestion.date as date') {
						$_data = _dt($aRow['date']);
					}
					if ($aColumns[$i] == 'tblsuggestion.type as type') {
						$_data = $type_text[$aRow['type']];
					}
					if ($aColumns[$i] == 'tblsuggestion.detail_suggest_muti_id as detail_suggest_muti_id') {
						if (!empty($aRow['detail_suggest_muti_id'])) {
							$this->db->select('tbl_category_payslip.name');
							$this->db->from('tbl_suggest_payslips_items');
							$this->db->join('tbl_category_payslip', 'tbl_category_payslip.id = tbl_suggest_payslips_items.category_payslip', 'left');
							// category_payslip
							$this->db->where('tbl_suggest_payslips_items.suggest_payslips_id', $aRow['detail_suggest_muti_id']);
							$suggest_payslips_items = $this->db->get()->result_array();
							$_data = '';
							foreach ($suggest_payslips_items as $key => $value) {
								$_data .= $value['name'] . ',';
							}
							$_data = rtrim($_data, ',');
						}
						// $get_internal_proposal = $this->db->get_where('tblinternal_proposal', array('id' => $aRow['id_internal_proposal']))->row_array();
						// if(!empty($get_internal_proposal)) {
						// 	if($get_internal_proposal['type_plan_propose'] == 'repair'){

						// 	}
						// }
					}
					if ($aColumns[$i] == 'tblsuggestion.code as code') {
						$_data = '<a data-tnh="modal" style="" class="tnh-modal" href="' . base_url('admin/suggestion/view_modal/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['code'] . '</a>';
						if (!empty($aRow['internal_proposal'])) {
							$internal_proposal = explode('__', $aRow['internal_proposal']);
							$_data .= '<span class="label label-success pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('internal_proposal/view/' . $internal_proposal[0])) . '">' . $internal_proposal[1] . '</a></span>';
						}
						if (!empty($aRow['internal_proposalv2'])) {
							$internal_proposalv2 = explode('__', $aRow['internal_proposalv2']);
							$_data .= '<span class="label label-success pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('internal_proposal/view/' . $internal_proposalv2[0])) . '">' . $internal_proposalv2[1] . '</a></span>';
						}
						if (!empty($aRow['purchase_order'])) {
							$purchase_order = explode('__', $aRow['purchase_order']);
							$_data .= '<span class="label label-info pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a onclick="view_purchase_order(' . $purchase_order[0] . '); return false;" >' . $purchase_order[1] . '</a></span>';
						}
					}
					if ($aColumns[$i] == 'tblsuggestion.status as status') {
						$_data = $status_text[$aRow['status']];
					}
					if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname') {
						$_data = !empty($aRow['staffid']) ? (staff_profile_image($aRow['staffid'], array('staff-profile-image-small mright5'), 'small', array()) . get_staff_full_name($aRow['staffid'])) : '';
					}
					if ($aColumns[$i] == 'CONCAT(tblstaff_browse.firstname," ",tblstaff_browse.lastname) as fullname_browse') {
						$_data = !empty($aRow['staff_browse']) ? (staff_profile_image($aRow['staff_browse'], array('staff-profile-image-small mright5'), 'small', array()) . get_staff_full_name($aRow['staff_browse'])) : '';
					}
					if ($aColumns[$i] == 'tblsuggestion.price_total as price_total') {
						$footer_data['total'] += $aRow['price_total'];
						$_data = '<div class="text-right">' . formatNumber($aRow['price_total'], 0) . '</div>';
					}

					if ($aColumns[$i] == 'tblsuggestion.payments as payments') {
						$title = formatMoney($aRow['payments'], 0);
						$status = 0;
						if (($aRow['payments'] < $aRow['price_total']) && ($aRow['price_total'] > 0) && ($aRow['payments'] > 0)) {
							$status = 1;
						}
						if ((($aRow['payments'] + 0.1) >= $aRow['price_total']) && ($aRow['price_total'] > 0)) {
							$status = 2;
						}
						if ($aRow['payments'] > 0) {
							$status_other = '<div class="text-center">' . format_status_pay_slip_new($status, $aRow['payments']) . '<div>';
						} else {
							$status_other = '<div class="text-center">' . format_status_pay_slip($status) . '<div>';
						}
						$footer_data['pay'] += $aRow['payments'];
						$_data = $status_other;
					}
					if ($aColumns[$i] == 'tblsuggestion.status_dn as status_dn') {
						$_data = '';
						if ($aRow['status_dn'] == 0) {
							$type = 'warning';
							$status = _l('dont_approve');
						} elseif ($aRow['status_dn'] == 1) {
							$type = 'info';
							$status = _l('ch_confirm_22');
						}
						$status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['status_dn'] . '">' . $status . '';
						if ($aRow['status_dn'] == 0) {
							$status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['status_dn'] . ',' . $aRow['id'] . '); return false">
                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
						} else {
							$status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="unvar_status(' . $aRow['status_dn'] . ',' . $aRow['id'] . '); return false">
                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
						}
						$status .= '</a>
                                    </span><br>';
						if ($aRow['status_dn'] == 1) {
							$status .= staff_profile_image($aRow['staff_status_dn'], array('staff-profile-image-small mright5'), 'small', array(
								'data-toggle' => 'tooltip',
								'data-title' => ' Vào lúc: ' . _dt($aRow['date_status_dn'])
							)) . get_staff_full_name($aRow['staff_status_dn']) . '<br>';
						}
						$_data = $status;
					}
					if ($aColumns[$i] == 'tblsuggestion.status_tp as status_tp') {
						$_data = '';
						if ($aRow['status_tp'] == 0) {
							$type = 'warning';
							$status = _l('dont_approve');
						} elseif ($aRow['status_tp'] == 1) {
							$type = 'info';
							$status = _l('ch_confirm_22');
						}
						$status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['status_tp'] . '">' . $status . '';
						if ($aRow['status_tp'] == 0) {
							$status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status_tp(' . $aRow['status_tp'] . ',' . $aRow['id'] . '); return false">
                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
						} else {
							$status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="unvar_status_tp(' . $aRow['status_tp'] . ',' . $aRow['id'] . '); return false">
                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
						}
						$status .= '</a>
                                    </span><br>';
						if ($aRow['status_tp'] == 1) {
							$status .= staff_profile_image($aRow['staff_status_tp'], array('staff-profile-image-small mright5'), 'small', array(
								'data-toggle' => 'tooltip',
								'data-title' => ' Vào lúc: ' . _dt($aRow['date_status_tp'])
							)) . get_staff_full_name($aRow['staff_status_tp']) . '<br>';
						}
						$_data = $status;
					}

					if ($aColumns[$i] == 'tblsuggestion.treasurer as treasurer') {
						$_data = '';
						if (empty($aRow['treasurer'])) {
							$type = 'warning';
							$status = _l('dont_approve');
						} else {
							$type = 'info';
							$status = _l('ch_confirm_22');
						}
						$status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['treasurer'] . '">' . $status . '';
						if (empty($aRow['treasurer'])) {
							$status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="approved_treasurer(' . $aRow['id'] . '); return false">
                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
							$status .= '</a>
                                    </span><br>';
						} else {
							$status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="approved_treasurer(' . $aRow['id'] . '); return false">
                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
							$status .= '</a>
                                    </span><br>';
							$status .= staff_profile_image($aRow['treasurer'], array('staff-profile-image-small mright5'), 'small', array(
								'data-toggle' => 'tooltip',
								'data-title' => ' Vào lúc: ' . _dt($aRow['treasurer_time'])
							)) . get_staff_full_name($aRow['treasurer']) . '<br>';
						}
						$_data = $status;
					}
					if ($aColumns[$i] == '7') {
						$_data = '';
						if ($this->perEditsuggestion) {
							// $_data .= icon_btn('suggestion/detail/' . $aRow['id'], 'pencil', 'btn-info');
							// $column[2] = '<div><a class="c_modal" href="'.admin_url('internal_proposal/view/' . $aRow['id']).'">' . $aRow['code'] . '</a></div>';
							$_data .= icon_btn('suggestion/modal_suggestion/' . $aRow['id'], 'edit', 'btn-success c_modal');
						}
						if ($this->perEditsuggestion && ($aRow['status_dn'] == 0) && ($aRow['status_tp'] == 0)) {
							$_data .= icon_btn('suggestion/detail/' . $aRow['id'], 'pencil', 'btn-info');
						}
						$_data .= icon_btn('suggestion/pdf/' . $aRow['id'], 'file-pdf-o', 'btn-warning', array('target' => '_blank'));
						if ($this->perDeletesuggestion) {
							$_data .= icon_btn('suggestion/delete/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');
						}
					}
					$row[] = $_data;
				}
				$output['aaData'][] = $row;
			}
			foreach ($footer_data as $key => $total) {
				$footer_data[$key] = formatNumber($total, 0);
			}
			$output['sums'] = $footer_data;

			$output['total']['total_cash'] = $this->db->join('tblpayment_modes', 'tblpayment_modes.id = tblsuggestion.id_payment_modes')
				->where('treasurer is null', false, false)
				->get_where('tblsuggestion', ['cash' => 1])->num_rows();
			$output['total']['total_bank'] = $this->db->join('tblpayment_modes', 'tblpayment_modes.id = tblsuggestion.id_payment_modes')
				->where('treasurer is null', false, false)
				->get_where('tblsuggestion', ['bank' => 1])->num_rows();

			echo json_encode($output);
			die();
		}
	}
	public function modal_suggestion($id = '')
	{

		$data['id'] = $id;
		$data['suggestion'] = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
		$data['title'] = 'Cập nhật thông tin phiếu đề xuất tài chính';
		$data['payment_modes'] = $this->db->get('tblpayment_modes')->result_array();
		$data['staff_browse'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
		$this->load->view('admin/suggestion/modal_suggestion', $data);
	}
	public function update_suggestion($id = '')
	{
		if (!$this->perEditsuggestion) {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Bạn không có quyền sửa phiếu đề xuất tài chính']);
			die();
		}

		$id_payment_modes = $this->input->post('id_payment_modes');
		if (empty($id_payment_modes)) {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Vui lòng chọn phương thức thanh toán']);
			die();
		}
		$staff_browse = $this->input->post('staff_browse');
		$ins = array();
		$ins['id_payment_modes'] = $id_payment_modes;
		$ins['staff_browse'] = !empty($staff_browse) ? $staff_browse : NULL;
		$this->db->where('id', $id);
		$suggestion = $this->db->update('tblsuggestion', $ins);
		if (!empty($suggestion)) {
			echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật phiếu đề xuất tài chính thành công']);
			die();
		}
		echo json_encode(['success' => true, 'alert_type' => 'danger', 'message' => 'Cập nhật phiếu đề xuất tài chính không thành công']);
		die();
	}
	public function delete($id)
	{
		if (!has_permission('suggestion', '', 'delete')) {
			$success = false;
			$alert_type = 'danger';
			$message = _l('Bạn không có quyền xóa');
			echo json_encode(array(
				'success' => $success,
				'alert_type' => $alert_type,
				'message' => $message
			));
			die;
		}
		if (!$id) {
			die('ch_no_items');
		}


		$this->db->where('id', $id);
		$suggestion = $this->db->get('tblsuggestion')->row();
		if (!empty($suggestion->status_dn)) {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Người đã xuất đã duyệt không thể xóa']);
			die();
		}
		if (!empty($suggestion->status_tp)) {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Trưởng phòng đã duyệt không thể xóa']);
			die();
		}
		if (!empty($suggestion->treasurer)) {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thủ quỷ hoàn đã duyệt không thể xóa']);
			die();
		}


		$dtData = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
		$this->db->where('id', $id);
		$this->db->delete('tblsuggestion');
		$alert_type = 'warning';
		$message = _l('ch_no_delete');
		if ($this->db->affected_rows() > 0) {
			if (!empty($suggestion->id_internal_proposal)) {
				$this->db->where('id', $suggestion->id_internal_proposal);
				$this->db->update('tblinternal_proposal', ['id_suggestion' => NULL]);
			}
			// $files = get_table_where('tblfiles',array('rel_type'=>'suggestion','rel_id'=>$id))
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'suggestion');
			$this->db->delete('tblfiles');

			$this->db->where('id_suggestion', $id);
			$this->db->update('tblinternal_proposal', ['id_suggestion' => NULL]);

			$this->db->where('id_suggestion', $id);
			$this->db->delete('tblsuggestion_detal');
			$path = 'uploads/suggestion/' . $id . '/';
			if (file_exists($path)) {
				rmdir($path);
			}

			insertActivityLog([
				'type_parent_obj' => 'suggestion',
				'table_obj' => 'tblsuggestion',
				'id_obj' => $id,
				'name_obj' => $dtData->code,
				'content' => lang('Xóa phiếu đề xuất tài chính') . ' [' . $dtData->code . ']',
				'actions' => 'delete'
			]);

			$alert_type = 'success';
			$message = _l('ch_delete');
		}
		echo json_encode(array(
			'alert_type' => $alert_type,
			'message' => $message
		));
	}

	public function pdf($id = '')
	{
		ob_start();
		$data = new stdClass();
		$data->title = lang('Phiếu đề xuất tài chính');
		$dataMain = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
		$table = '';
		$data->content = '';
		$data->content .= '<span style="text-align: right; font-style: italic;">Ngày chứng từ: ' . _dhau($dataMain->date) . '</span><br><br>';
		$data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">PHIẾU ĐỀ XUẤT TÀI CHÍNH</span><br><br>';
		$checkbox1 = [];
		for ($i = 0; $i <= 2; $i++) {
			if ($i == $dataMain->type - 1) {
				$checkbox1[$i] = base_url('/uploads/icon_hau/checkbox.png');
			} else {
				$checkbox1[$i] = base_url('/uploads/icon_hau/empty_checkbox.png');
			};
		}
		if ($dataMain->type == 4) {
			$checkbox1[1] = base_url('/uploads/icon_hau/checkbox.png');
			$checkbox1[2] = base_url('/uploads/icon_hau/checkbox.png');
		}
		$checkbox2 = [];
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $dataMain->status - 1) {
				$checkbox2[$i] = base_url('/uploads/icon_hau/checkbox.png');
			} else {
				$checkbox2[$i] = base_url('/uploads/icon_hau/empty_checkbox.png');
			};
		}
		$table = '
            <table class="table" border="0" width="100%">
                <thead></thead>
                <tbody>
                    <tr>
                        <td width="20%"><span style="font-weight: bold" >Loại:</span></td>
                        <td width="28%">
                            <table class="table" border="0" width="100%">
                                <thead>
                                    <tr>
                                        <td width="25px"><img style="float:left;" src="' . $checkbox1[0] . '"></td>
                                        <td><span style="font-weight: bold">Mua vật tư</span></td>
                                    </tr>
                                </thead>
                            </table>
                        </td>
                        <td width="30%">
                            <table class="table" border="0" width="100%">
                                <thead>
                                    <tr>
                                        <td width="25px"><img style="float:left;" src="' . $checkbox1[1] . '"></td>
                                        <td><span style="font-weight: bold">Tạm ứng</span></td>
                                    </tr>
                                </thead>
                            </table>
                        </td>
                        <td width="28%">
                            <table class="table" border="0" width="100%">
                                <thead>
                                    <tr>
                                        <td width="25px"><img style="float:left;" src="' . $checkbox1[2] . '"></td>
                                        <td><span style="font-weight: bold">Thanh toán</span></td>
                                    </tr>
                                </thead>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="text-align: left; font-weight: bold">Trạng thái:</span>
                        </td>
                        <td>
                            <table class="table" border="0" width="100%">
                                <thead>
                                    <tr>
                                        <td width="25px"><img style="float:left;" src="' . $checkbox2[0] . '"></td>
                                        <td><span style="text-align: left; font-weight: bold">Gấp</span></td>
                                    </tr>
                                </thead>
                            </table>   
                        </td>
                        <td>
                            <table class="table" border="0" width="100%">
                                <thead>
                                    <tr>
                                        <td width="25px"><img style="float:left;" src="' . $checkbox2[1] . '"></td>
                                        <td><span style="text-align: left; font-weight: bold">Bình thường</span></td>
                                    </tr>
                                </thead>
                            </table>   
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>';
		$data->content .= $table;
		$data->content .= '<span style="font-size: 15px;font-weight: bold;">I. Phần của người đề xuất:</span><br>';
		$data->content .= '<span style="font-style: italic;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1. <u>Trường hợp đề xuất tạm ứng, thanh toán chi phí</u>:</span><br>';
		$staff = get_table_where('tblstaff', array('staffid' => $dataMain->staffid), '', 'row', '');
		$table = '
            <table class="table" border="0" width="100%">
                <thead></thead>
                <tbody>
                    <tr>
                        <td><span style="">Họ và tên người đề xuất: ' . $staff->firstname . '&nbsp;' . $staff->lastname . '</span></td>
                        <td><span style="">MSNV: ' . $staff->code . '</span></td>
                    </tr>
                    <tr>
                        <td><span style="">Nội dung đề xuất: ' . $dataMain->note . '</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><span style="">Số tiền đề xuất: ' . formatNumber($dataMain->price_total) . '</span></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>';
		$data->content .= $table;
		$data->content .= '<span style="font-style: italic;">&nbsp;&nbsp;(Bằng chữ: ' . ucfirst(convert_number_to_words($dataMain->price_total)) . ' đồng)</span><br>';
		$table = '<table class="table" border="0" width="100%">
            <thead><tr>
                <td width="25px" ><img style="float:left;" src="' . base_url('/uploads/icon_hau/empty_checkbox.png') . '"></td>
                <td>Kèm theo chứng từ: </td>
            </tr></thead>
        </table>';
		$data->content .= $table;
		$data->content .= '<br><span style="font-style: italic;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2. <u>Trường hợp đề xuất mua vật tư</u>:</span><br><br>';
		$width1 = 'width: 6%;';
		$width2 = 'width: 20%;';
		$width3 = 'width: 20%;';
		$width4 = 'width: 8%;';
		$width5 = 'width: 11%;';
		$width6 = 'width: 10%;';
		$width7 = 'width: 12%;';
		$width8 = 'width: 14%;';
		$table = '<table class="table table-bordered" border="1" width="100%">
        <thead>
            <tr>
                <td style="' . $width1 . ' text-align: center;font-weight: bold;">' . _l('STT') . '</td>
                <td style="' . $width2 . ' text-align: center;font-weight: bold;">' . _l('Mã vật tư') . '</td>
                <td style="' . $width3 . ' text-align: center;font-weight: bold;">' . _l('Vật tư') . '</td>
                <td style="' . $width4 . ' text-align: center;font-weight: bold;">' . _l('tnh_dvt') . '</td>
                <td style="' . $width5 . ' text-align: center;font-weight: bold;">' . _l('Quy cách') . '</td>
                <td style="' . $width6 . ' text-align: center;font-weight: bold;">' . _l('Số lượng') . '</td>
                <td style="' . $width7 . ' text-align: center;font-weight: bold;">' . _l('Đơn giá') . '</td>
                <td style="' . $width8 . ' text-align: center;font-weight: bold;">' . _l('Thành tiền') . '</td>
            </tr>
        </thead>
        <tbody>';
		$items = get_table_where('tblsuggestion_detal', array('id_suggestion' => $id));
		$quantity = 0;
		$amount = 0;
		foreach ($items as $key => $value) {
			$items_detail = get_items($value['id_items'], $value['type']);
			$quantity += $value['quantity'];
			$amount += $value['amount'];
			$table .= '
                    <tr>
                        <td style="' . $width1 . '">
                            <span style="text-align: center;">' . ($key + 1) . '</span>
                        </td>
                        <td style="' . $width2 . '">
                            <span>' . $items_detail->code . '</span>
                        </td>
                        <td style="' . $width3 . '">
                            <span>' . $items_detail->name . '</span>
                        </td>
                        <td style="' . $width4 . '">
                            <span style="text-align: center;">' . $items_detail->unit_name . '</span>
                        </td>
                        <td style="' . $width5 . '">
                            <span>' . $items_detail->mode . '</span>
                        </td>
                        <td style="' . $width6 . '">
                            <span style="text-align: right;">' . formatNumber($value['quantity']) . '</span>
                        </td>
                        <td style="' . $width7 . '">
                            <span style="text-align: right;">' . formatNumber($value['price']) . '</span>
                        </td>
                        <td style="' . $width8 . '">
                            <span style="text-align: right;">' . formatNumber($value['amount']) . '</span>
                        </td>
                    </tr>';
		}
		$table .= '</tbody>
            <footer>
                <tr>
                    <td colspan="5">Tổng</td>
                    <td style="text-align: right;">' . formatNumber($quantity) . '</td>
                    <td></td>
                    <td style="text-align: right;">' . formatNumber($amount) . '</td>
                </tr>
            </footer>
        </table>';
		$data->content .= $table;
		$data->content .= '<br><br><span style="font-size: 15px;font-weight: bold;">II. Phần của BP thu mua:</span><br><br>';
		$table = '<table class="table" border="0" width="100%">
            <tbody>
                <tr>
                    <td width="25px" ><img style="float:left;" src="' . base_url('/uploads/icon_hau/empty_checkbox.png') . '"></td>
                    <td width="98%">Xác nhận đơn giá - thành tiền: ..................................................................................................................................</td>
                </tr>
                <tr>
                    <td width="25px" ><img style="float:left;" src="' . base_url('/uploads/icon_hau/empty_checkbox.png') . '"></td>
                    <td width="98%">Xác nhận nhà cung cấp: ...........................................................................................................................................</td>
                </tr>
            </tbody>
        </table>';
		$data->content .= $table;
		$data->content .= '<br><br><span style="font-size: 15px;font-weight: bold;">III. Phần của BP Kế toán:</span><br><br>';
		$data->content .= '<span>Người nhận tiền: ..............................................................................................................................................................</span><br><br>';
		$data->content .= '<span>Ngày ký nhận: ................................................................... Ký nhận: ...............................................................................</span><br><br>';
		$table = '<table class="table table-bordered" width="100%">
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề xuất</span>' .
			// <span style="font-weight: bold;">' . $staff->firstname . '&nbsp;' . $staff->lastname . '</span>
			'</td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">BP Thu mua</span><br>
                            <span></span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">BP Kế toán</span><br>
                            <span></span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Xác nhận</span><br>
                            <span style="font-style: italic;">GĐNM/Trưởng BP</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Duyệt</span><br>
                            <span style="font-style: italic;">Ban giám đốc</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
		$data->content .= $table;
		$pdf = print_pdf($data);
		$type = 'I';
		$pdf->Output(slug_it('Phieu_de_xuat_tai_chinh') . '.pdf', $type);
	}

	public function updatestatus($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('tblsuggestion', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	public function update_status($id = '')
	{
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$import = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			if (!is_admin()) {
				if (($import->staffid != get_staff_user_id())) {
					echo json_encode(array(
						'alert_type' => 'warning',
						'message' => _l('Bạn không phải người đề nghị')
					));
					die;
				}
			}
			if ($import->status_dn == 1) {
				die;
			}
			$staff_id = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$data = array(
				'staff_status_dn' => $staff_id,
				'date_status_dn' => $date,
				'status_dn' => ($status + 1),
			);
			$success = $this->updatestatus($id, $data);
		}
		if ($success) {
			$get_code = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			activity_log_v2('suggestion', 'tblsuggestion', $id, $get_code->code, 'Cập nhật trạng thái người đề xuất phiếu đề xuất [' . $get_code->code . ']');
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

	public function update_status_tp($id = '')
	{
		if (!$this->perApprovesuggestion) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('ch_approve_not')
			));
			die;
		}
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$import = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			if ($import->status_tp == 1) {
				die;
			}
			$staff_id = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$data = array(
				'staff_status_tp' => $staff_id,
				'date_status_tp' => $date,
				'status_tp' => ($status + 1),
			);
			$success = $this->updatestatus($id, $data);
		}
		if ($success) {
			$get_code = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			activity_log_v2('suggestion', 'tblsuggestion', $id, $get_code->code, 'Cập nhật trạng thái người đề xuất phiếu đề xuất [' . $get_code->code . ']');
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

	public function unupdate_status($id = '')
	{
		// if (!has_permission('import', '', 'approve')) {
		//     echo json_encode(array(
		//         'alert_type' => 'warning',
		//         'message' => _l('ch_approve_not')
		//     ));
		//     die;
		// }
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$import = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			if (!is_admin()) {
				if (($import->staffid != get_staff_user_id())) {
					echo json_encode(array(
						'alert_type' => 'warning',
						'message' => _l('Bạn không phải người đề nghị')
					));
					die;
				}
			}
			if ($import->status_dn == 0) {
				die;
			}
			$staff_id = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$data = array(
				'staff_status_dn' => NULL,
				'date_status_dn' => NULL,
				'status_dn' => 0,
			);
			$success = $this->updatestatus($id, $data);
		}
		if ($success) {
			$get_code = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			activity_log_v2('suggestion', 'tblsuggestion', $id, $get_code->code, 'Cập nhật trạng thái người đề xuất phiếu đề xuất [' . $get_code->code . ']');
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

	public function unupdate_status_tp($id = '')
	{
		if (!$this->perApprovesuggestion) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('ch_approve_not')
			));
			die;
		}
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$import = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			if ($import->status_tp == 0) {
				die;
			}
			$staff_id = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$data = array(
				'staff_status_tp' => NULL,
				'date_status_tp' => NULL,
				'status_tp' => 0,
			);
			$success = $this->updatestatus($id, $data);
		}
		if ($success) {
			$get_code = get_table_where('tblsuggestion', array('id' => $id), '', 'row');
			activity_log_v2('suggestion', 'tblsuggestion', $id, $get_code->code, 'Cập nhật trạng thái người đề xuất phiếu đề xuất [' . $get_code->code . ']');
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

	public function ApprovedTreasurer()
	{
		// if (!$this->perApprovesuggestion) {
		// 	echo json_encode(array(
		// 		'alert_type' => 'warning',
		// 		'message' => _l('ch_approve_not')
		// 	));
		// 	die;
		// }
		if ($this->input->post()) {
			$id = $this->input->post('id');
			$treasurerStatus = get_table_where('tblsuggestion', array('id' => $id), '', 'row', '', 'treasurer');
			if (!empty($treasurerStatus)) {
				if (empty($treasurerStatus->treasurer)) { // chưa duyệt
					$staff_id = get_staff_user_id();
					$date = date('Y-m-d H:i:s');
					$data = array(
						'treasurer' => $staff_id,
						'treasurer_time' => $date,
					);
					$this->db->where('tblsuggestion.id', $id);
					$success = $this->db->update('tblsuggestion', $data);
					if ($success) {
						echo json_encode(array(
							'success' => $success,
							'alert_type' => 'success',
							'message' => _l('ch_successful_approval')
						));
						die;
					} else {
						echo json_encode(array(
							'success' => $success,
							'alert_type' => 'danger',
							'message' => _l('ch_no_successful_approval')
						));
						die;
					}
				} else {
					$data = array(
						'treasurer' => 0,
						'treasurer_time' => '',
					);
					$this->db->where('tblsuggestion.id', $id);
					$success = $this->db->update('tblsuggestion', $data);
					if ($success) {
						echo json_encode(array(
							'success' => $success,
							'alert_type' => 'success',
							'message' => _l('ch_successful_approval_cance')
						));
						die;
					} else {
						echo json_encode(array(
							'success' => $success,
							'alert_type' => 'danger',
							'message' => _l('ch_no_successful_approval_cance')
						));
						die;
					}
				}
			} else {
				echo json_encode(array(
					'success' => '',
					'alert_type' => 'danger',
					'message' => _l('ch_no_successful_approval')
				));
				die;
			}
		}
	}

	/*public function scanAddSuggestionTax()
	{
		$arr_suggestion_detal = [];
		$suggestion = get_table_where('tblsuggestion');
		foreach ($suggestion as $suggestion_key => $suggestion_value) {
			if (!empty($suggestion_value['purchase_order_id'])) {
				$purchase_order = get_table_where('tblpurchase_order', ['id' => $suggestion_value['purchase_order_id']], '', 'row_array');
				$purchase_order_items = get_table_where('tblpurchase_order_items', ['id_purchase_order' => $purchase_order['id']]);
				foreach ($purchase_order_items as $purchase_order_items_key => $purchase_order_items_value) {
					$suggestion_detal = get_table_where('tblsuggestion_detal', ['id_suggestion' => $suggestion_value['id'], 'id_items'=>$purchase_order_items_value['product_id']]);
					foreach ($suggestion_detal as $suggestion_detal_key => $suggestion_detal_value) {
						if (!empty($purchase_order_items_value['tax_id'])) {
							$suggestion_detal[$suggestion_detal_key]['tax_id_new'] = $purchase_order_items_value['tax_id'];
							$suggestion_detal[$suggestion_detal_key]['tax_rate_new'] = $purchase_order_items_value['tax_rate'];

							$amount = $suggestion_detal_value['price'] * (1 + $purchase_order_items_value['tax_rate']/100) * $suggestion_detal_value['quantity'];
							$this->db->update('tblsuggestion_detal', ['tax_id'=>$purchase_order_items_value['tax_id'], 'tax_rate'=>$purchase_order_items_value['tax_rate'], 'amount' => $amount], ['id'=>$suggestion_detal_value['id']]);


						}
					}
					$arr_suggestion_detal = array_merge($arr_suggestion_detal, $suggestion_detal);
				}

				$price_total = get_table_where('tblsuggestion_detal', ['id_suggestion' => $suggestion_value['id']], '', 'row_array', '', 'SUM(tblsuggestion_detal.amount) as price_total')['price_total'];
				$this->db->update('tblsuggestion', ['price_total' => $price_total], ['id' => $suggestion_value['id']]);
			}
		}

		echo '<pre>'; var_dump($arr_suggestion_detal);
	}*/
}
