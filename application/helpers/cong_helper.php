<?php
//Helpers của công
defined('BASEPATH') or exit('No direct script access allowed');
/*
 *
 * Lấy colum trong điều kiện của Module automations
 *
 */
function GetAutomationWhereColum($action = "")
{
	if (!empty($action)) {
		if ($action == 1) {
			$colums_where = array(
				['id' => 'datecreated', 'name' => _l('cong_date_create')],
				['id' => 'city', 'name' => _l('cong_city')],
				['id' => 'type_client', 'name' => _l('cong_type_client')],
			);
		} else if ($action == 2) {
			$colums_where = array(
				['id' => 'dateadded', 'name' => _l('cong_date_create')],
				['id' => 'city', 'name' => _l('cong_city')],
				['id' => 'status', 'name' => _l('cong_status_lead')],
				['id' => 'source', 'name' => _l('cong_type_client')],
			);
		}
		return $colums_where;
	}
	return false;
}

function GetActionAutomation($List = true)
{
	if ($List == true) {
		$array_action = array(
			['id' => '1', 'name' => _l('cong_client')],
			['id' => '2', 'name' => _l('cong_lead')],
			['id' => '3', 'name' => _l('create_orders')], //đặt hàng
			['id' => '4', 'name' => _l('create_receipts')], // phiếu thu
			['id' => '5', 'name' => _l('create_pay')], //phiếu chi
			['id' => '6', 'name' => _l('create_purchase_proposal')], //đề xuất mua hàng
			['id' => '7', 'name' => _l('create_imports')], // nhập hàng
			['id' => '8', 'name' => _l('create_exports')], //xuất hàng
			['id' => '9', 'name' => _l('create_sell')], //bán hàng
		);
	} else {
		$array_action = [
			'1' => _l('cong_client'),
			'2' => _l('cong_lead'),
			'3' => _l('create_orders'),
			'4' => _l('create_receipts'),
			'5' => _l('create_pay'),
			'6' => _l('create_purchase_proposal'),
			'7' => _l('create_imports'),
			'8' => _l('create_exports'),
			'9' => _l('create_sell')
		];
	}
	return $array_action;
}

function image_client_upload($userid = '')
{
	if (is_numeric($userid)) {
		if (isset($_FILES['client_image']['name']) && $_FILES['client_image']['name'] != '') {
			$path = get_upload_path_by_type('customer') . $userid . '/';
			// Get the temp file path
			$tmpFilePath = $_FILES['client_image']['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				// Getting file extension
				$extension = strtolower(pathinfo($_FILES['client_image']['name'], PATHINFO_EXTENSION));
				$allowed_extensions = [
					'jpg',
					'jpeg',
					'png',
				];
				if (!in_array($extension, $allowed_extensions)) {
					set_alert('warning', _l('file_php_extension_blocked'));
					return false;
				}
				_maybe_create_upload_path($path);
				$filename = unique_filename($path, $_FILES['client_image']['name']);
				$newFilePath = $path . '/' . $filename;
				// Upload the file into the company uploads dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$CI = &get_instance();
					$config = [];
					$config['image_library'] = 'gd2';
					$config['source_image'] = $newFilePath;
					$config['new_image'] = 'thumb_' . $filename;
					$config['maintain_ratio'] = true;
					$config['width'] = 320;
					$config['height'] = 320;
					$CI->image_lib->initialize($config);
					$CI->image_lib->resize();
					$CI->image_lib->clear();
					$config['image_library'] = 'gd2';
					$config['source_image'] = $newFilePath;
					$config['new_image'] = 'small_' . $filename;
					$config['maintain_ratio'] = true;
					$config['width'] = 32;
					$config['height'] = 32;
					$CI->image_lib->initialize($config);
					$CI->image_lib->resize();
					$CI->db->where('userid', $userid);
					$CI->db->update(db_prefix() . 'clients', [
						'client_image' => $filename,
					]);
					// Remove original image
					unlink($newFilePath);
					return true;
				}
			}
		}
	}
	return false;
}

function image_lead_upload($id = '')
{
	if (is_numeric($id)) {
		if (isset($_FILES['lead_image']['name']) && $_FILES['lead_image']['name'] != '') {
			$path = get_upload_path_by_type('lead') . $id . '/';
			// Get the temp file path
			$tmpFilePath = $_FILES['lead_image']['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				// Getting file extension
				$extension = strtolower(pathinfo($_FILES['lead_image']['name'], PATHINFO_EXTENSION));
				$allowed_extensions = [
					'jpg',
					'jpeg',
					'png',
				];
				if (!in_array($extension, $allowed_extensions)) {
					set_alert('warning', _l('file_php_extension_blocked'));
					return false;
				}
				_maybe_create_upload_path($path);
				$filename = unique_filename($path, $_FILES['lead_image']['name']);
				$newFilePath = $path . '/' . $filename;
				// Upload the file into the company uploads dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$CI = &get_instance();
					$config = [];
					$config['image_library'] = 'gd2';
					$config['source_image'] = $newFilePath;
					$config['new_image'] = 'thumb_' . $filename;
					$config['maintain_ratio'] = true;
					$config['width'] = 320;
					$config['height'] = 320;
					$CI->image_lib->initialize($config);
					$CI->image_lib->resize();
					$CI->image_lib->clear();
					$config['image_library'] = 'gd2';
					$config['source_image'] = $newFilePath;
					$config['new_image'] = 'small_' . $filename;
					$config['maintain_ratio'] = true;
					$config['width'] = 32;
					$config['height'] = 32;
					$CI->image_lib->initialize($config);
					$CI->image_lib->resize();
					$CI->db->where('id', $id);
					$CI->db->update(db_prefix() . 'leads', [
						'lead_image' => $filename,
					]);
					// Remove original image
					unlink($newFilePath);
					$profileImagePath = 'uploads/leads/' . $id . '/thumb_' . $filename;
					$url = base_url('download/preview_image?path=' . $profileImagePath);
					return ['url' => $url, 'name_img' => $filename];
				}
			}
		}
	}
	return false;
}

function get_district($id)
{
	if (!empty($id)) {
		$CI = &get_instance();
		$CI->db->where('districtid', $id);
		$district = $CI->db->get(db_prefix() . 'district')->row();
		if (!empty($district)) {
			return $district;
		}
		return NULL;
	}
}

function get_ward($id = "")
{
	if (!empty($id)) {
		$CI = &get_instance();
		$CI->db->where('wardid', $id);
		$ward = $CI->db->get(db_prefix() . 'ward')->row();
		if (!empty($ward)) {
			return $ward;
		}
		return NULL;
	}
}

function get_DataCombobox($id = "")
{
	if (!empty($id)) {
		$CI = &get_instance();
		$CI->db->where('id', $id);
		$data = $CI->db->get(db_prefix() . 'combobox_client')->row();
		if (!empty($data)) {
			return $data;
		}
	}
	return NULL;
}

function get_type_client($id = "")
{
	if (!empty($id)) {
		$CI = &get_instance();
		$CI->db->where('id', $id);
		$data = $CI->db->get(db_prefix() . 'type_client')->row();
		if (!empty($data)) {
			return $data;
		}
	}
	return NULL;
}

function addLog_advisory_lead($data = [], $type = 1)
{
	$CI = &get_instance();
	if (!empty($data)) {
		$CI->db->insert(db_prefix() . 'log_advisory_lead', [
			'id_object' => $data['id_object'],
			'type_object' => $data['type_object'],
			'staff' => $data['staff'],
			'id_procedure' => $data['id_procedure'],
			'date_create' => date('Y-m-d H:i:s'),
			'create_by' => get_staff_user_id(),
			'note' => $data['name_status'],
			'type' => $type
		]);
		if ($CI->db->insert_id()) {
			return true;
		}
	}
	return false;
}

function addLog_care_of($data = [], $type = 1)
{
	$CI = &get_instance();
	if (!empty($data)) {
		$CI->db->where('id', $data['id_procedure']);
		$procedure_detail = $CI->db->get(db_prefix() . 'procedure_client_detail')->row();
		if (!empty($procedure_detail)) {
			$CI->db->insert(db_prefix() . 'log_care_of', [
				'id_client' => $data['id_client'],
				'staff' => $data['staff'],
				'id_procedure' => $data['id_procedure'],
				'date_create' => date('Y-m-d H:i:s'),
				'create_by' => get_staff_user_id(),
				'note' => $procedure_detail->name,
				'type' => $type
			]);
			if ($CI->db->insert_id()) {
				return true;
			}
		}
	}
	return false;
}

function get_fields_export_excel_client()
{
	$CI = &get_instance();
	$colum_client = $CI->db->list_fields(db_prefix() . 'clients');
	array_unshift($colum_client, "groups_in");
	$colum_client = array_diff($colum_client, [
		'billing_city',
		'code_client',
		'billing_street',
		'billing_state',
		'billing_zip',
		'billing_country',
		'shipping_street',
		'shipping_city',
		'shipping_country',
		'shipping_state',
		'shipping_zip',
		'client_image',
		'longitude',
		'latitude',
		'default_language',
		'show_primary_contact',
		'stripe_id',
		'registration_confirmed',
		'addedfrom',
		'active',
		'leadid',
		'default_currency',
		'facebook',
		'date_contact',
		'name_facebook',
		'name_staff',
		'leadtime',
		'table_price_id',
		'id_discount_client',
		'id_facebook',
		'link_facebook',
		'debt_begin',
		'allowed_vat',
		'code_system',
		'name_system',
		'dt',
		'kt',
	]);
	$colum_info_client = $CI->db->get('tblclient_info_detail')->result_array();
	return ['colum_client' => $colum_client, 'colum_info_client' => $colum_info_client];
}

function get_fields_import_client_excel()
{
	$CI = &get_instance();
	$colum_client = $CI->db->list_fields(db_prefix() . 'clients');
	$colum_client = array_diff($colum_client, [
		'userid',
		'code_client',
		'introduction',
		'billing_city',
		'billing_street',
		'billing_state',
		'billing_zip',
		'billing_country',
		'shipping_street',
		'shipping_city',
		'shipping_country',
		'shipping_state',
		'shipping_zip',
		'client_image',
		'longitude',
		'latitude',
		'default_language',
		'show_primary_contact',
		'stripe_id',
		'registration_confirmed',
		'addedfrom',
		'active',
		'leadid',
		'default_currency',
		'prefix_client',
		'code_system',
		'code_type',
		'type_client',
		'dt',
		'kt',
		'name_system',
		'fullname',
		'marriage',
		'religion',
		'facebook',
		'date_contact',
		'name_facebook',
		'name_staff',
		'leadtime',
		'table_price_id',
		// 'id_discount_client',
		'id_facebook',
		'link_facebook',
		'debt_begin',
		'allowed_vat',
	]);
	$colum_client[] = 'groups_in';
	$colum_client[] = 'customer_id';
//        $colum_info_client = $CI->db->get(db_prefix().'client_info_detail')->result_array();

	$colum_client = [
		'status_clients',
		'groups_in',
		'zcode',
		'company',
		'company_short',
		'representative',
		'vat',
		'phonenumber',
		'address',
		'email_client',
		'introduction',
		'debt_begin',
		'time_payment',
		'name_account',
		'bank_account',
		'contract_number',
		'certification',
		'price_list_approval',
		'bale_parameters',
		'quality_standards',
		'packing_regulations',
		'note',
		'date_create_company',
		// 'discount',
		'discount_id',
		'country',
		'tm_ck',
		'code_xnk',
		'vat_id',
		'address_bank',
		'currency',
		'type_contract',
		'date_renewal',
		'branch_ids',
		'date_accounting',
		'status_activity',
	];
	return ['colum_client' => $colum_client, 'colum_info_client' => []];
}

function get_fields_export_excel_lead()
{
	$CI = &get_instance();
	$colum_client = $CI->db->list_fields(db_prefix() . 'leads');
	$colum_client = array_diff($colum_client, [
		'id',
		'hash',
		'from_form_id',
		'lastcontact',
		'dateassigned',
		'last_status_change',
		'addedfrom',
		'leadorder',
		'date_converted',
		'lost',
		'junk',
		'last_lead_status',
		'is_imported_from_email_integration',
		'email_integration_uid',
		'is_public',
		'default_language',
		'client_id',
		'assigned',
		'lead_image',
		'id_facebook'
	]);
	$colum_client = implode(',', $colum_client);
	$colum_client = str_replace('name', 'leadname', $colum_client);
	$colum_client = explode(',', $colum_client);
	$colum_info_lead = $CI->db->get(db_prefix() . 'client_info_detail')->result_array();
	return ['colum_client' => $colum_client, 'colum_info_lead' => $colum_info_lead];
}

function get_fields_import_lead_excel()
{
	$CI = &get_instance();
	$colum_lead = $CI->db->list_fields(db_prefix() . 'leads');
	$colum_lead = array_diff($colum_lead, [
		'id',
		'country',
		'hash',
		'from_form_id',
		'lastcontact',
		'dateassigned',
		'last_status_change',
		'addedfrom',
		'leadorder',
		'date_converted',
		'lost',
		'junk',
		'last_lead_status',
		'is_imported_from_email_integration',
		'email_integration_uid',
		'is_public',
		'default_language',
		'client_id',
		'assigned',
		'lead_image',
		'id_facebook'
	]);
	$colum_info_client = $CI->db->get(db_prefix() . 'client_info_detail')->result_array();
	return ['colum_client' => $colum_lead, 'colum_info_lead' => $colum_info_client];
}

function get_care_of_clients_priorities()
{
	return [
		[
			'id' => 1,
			'name' => _l('task_priority_low'),
			'color' => '#777',
		],
		[
			'id' => 2,
			'name' => _l('task_priority_medium'),
			'color' => '#03a9f4',
		],
		[
			'id' => 3,
			'name' => _l('task_priority_high'),
			'color' => '#ff6f00',
		],
		[
			'id' => 4,
			'name' => _l('task_priority_urgent'),
			'color' => '#fc2d42',
		],
	];
}

function care_of_priority($id)
{
	foreach (get_care_of_clients_priorities() as $priority) {
		if ($priority['id'] == $id) {
			return $priority;
		}
	}
	// Not exists?
	return '#333';
}

function init_not_head()
{
	$CI = &get_instance();
	$CI->load->view('admin/includes/not_head/not_head');
}

function acction_delete_ajax($type = 1)
{
	if ($type == 1) {
		echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('cong_action_delete_true')]);
		die();
	} else if ($type == 0) {
		echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_action_delete_false')]);
		die();
	} else if ($type == 2) {
		echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_access_denied')]);
		die();
	} else if ($type == 3) {
		echo json_encode(['success' => false, 'alert_type' => 'warning', 'message' => _l('cong_is_referenced')]);
		die();
	}
}

function get_tags_color()
{
	$CI = &get_instance();
	$CI->db->select('group_concat(color order by name asc) as list_color');
	$tags = $CI->db->get('tbltags')->row();
	if (!empty($tags->list_color)) {
		return explode(',', $tags->list_color);
	}
	return [];
}

function get_tags_background_color()
{
	$CI = &get_instance();
	$CI->db->select('group_concat(background_color order by name asc) as list_background_color');
	$tags = $CI->db->get('tbltags')->row();
	if (!empty($tags->list_background_color)) {
		return explode(',', $tags->list_background_color);
	}
	return [];
}

function get_tags_table($where = "", $limit = "")
{
	$CI = &get_instance();
	if (!empty($where)) {
		$CI->db->where($where);
	}
	if (!empty($limit)) {
		$CI->db->limit($limit);
	}
	$CI->db->order_by('id', 'asc');
	$tags = $CI->db->get('tbltags')->result_array();
	return $tags;
}

//Định dạng ngày new
function _dC($date)
{
	$formatted = '';
	if ($date == '' || is_null($date) || $date == '0000-00-00') {
		return $formatted;
	}
	$format = get_current_date_format();
	$formatted = strftime($format, strtotime($date));
	return hooks()->apply_filters('after_format_date', $formatted, $date);
}

function C_initNumber($number = 0)
{
	if (!empty($number)) {
		$number = str_replace(',', '', $number);
		return is_numeric($number) ? $number : '0';
	}
	return 0;
}

function GetProducedure($status = 0)
{
	$CI = &get_instance();
	$CI->db->where('type', 'orders');
	$procedure = $CI->db->get(db_prefix() . 'procedure_client')->row();
	if (!empty($procedure)) {
		if ($status == 0) {
			$CI->db->order_by('orders asc');
			$CI->db->where('id_detail', $procedure->id);
			$procedure_detail = $CI->db->get('tblprocedure_client_detail')->row();
			if (!empty($procedure_detail)) {
				return $procedure_detail;
			}
		} else {
			$CI->db->order_by('orders asc');
			$CI->db->where('orders > (select orders from tblprocedure_client_detail where id = ' . $status . ' and id_detail = ' . $procedure->id . ')');
			$CI->db->where('id_detail', $procedure->id);
			$procedure_detail = $CI->db->get('tblprocedure_client_detail')->row();
			if (!empty($procedure_detail)) {
				return $procedure_detail;
			}
		}
	}
	return false;
}

function ResProducedure($status = 0)
{
	$CI = &get_instance();
	$CI->db->where('type', 'orders');
	$procedure = $CI->db->get(db_prefix() . 'procedure_client')->row();
	if (!empty($procedure)) {
		$CI->db->order_by('orders desc');
		$CI->db->where('orders < (select orders from tblprocedure_client_detail where id = ' . $status . ' and id_detail = ' . $procedure->id . ')');
		$CI->db->where('id_detail', $procedure->id);
		$procedure_detail = $CI->db->get('tblprocedure_client_detail')->row();
		if (!empty($procedure_detail)) {
			return $procedure_detail;
		}
	}
	return false;
}

function getConnectLead($lead = "")
{
	$CI = &get_instance();
	$result = [];
	if (!empty($lead)) {
		$CI->db->where('id_object', $lead);
		$CI->db->where('type_object', 'lead');
		$advisory_lead = $CI->db->get('tbladvisory_lead')->num_rows();
		$CI->db->where('rel_type', 'lead');
		$CI->db->where('rel_id', $lead);
		$tasks = $CI->db->get('tbltasks')->num_rows();
		if ($advisory_lead > 0) {
			$result[] = [
				'message' => _l('cong_isset_list') . _l('cong_advisory_lead'),
				'data' => $advisory_lead
			];
		}
		if ($tasks > 0) {
			$result[] = [
				'message' => _l('cong_isset_list') . _l('cong_tasks'),
				'data' => $tasks
			];
		}
	}
	return $result;
}

function getConnectClient($client = "")
{
	$CI = &get_instance();
	$result = [];
	if (!empty($client)) {
		$CI->db->where('client', $client);
		$care_of_client = $CI->db->get('tblcare_of_clients')->num_rows();
		$CI->db->where('client', $client);
		$orders = $CI->db->get('tblorders')->num_rows();
		$CI->db->where('rel_type', 'customer');
		$CI->db->where('rel_id', $client);
		$tasks = $CI->db->get('tbltasks')->num_rows();
		if ($care_of_client > 0) {
			$result[] = [
				'message' => _l('cong_isset_list') . _l('cong_care_of_clients'),
				'data' => $care_of_client
			];
		}
		if ($tasks > 0) {
			$result[] = [
				'message' => _l('cong_isset_list') . _l('cong_tasks'),
				'data' => $tasks
			];
		}
		if ($orders > 0) {
			$result[] = [
				'message' => _l('cong_isset_list') . _l('cong_orders'),
				'data' => $orders
			];
		}
	}
	return $result;
}

function number_format_data($number, $type = true)
{
	if (!empty($number)) {
		$number = str_replace(",", "", $number);
		$number = str_replace(",", "", number_format($number, 3));
		$_number = explode('.', $number);
		if (rtrim($_number[1], '0') != "") {
			if ($type) {
				return ($_number[0] === '-0' ? '-' : '') . number_format($_number[0]) . '.' . rtrim($_number[1], '0');
			}
			return $_number[0] . '.' . rtrim($_number[1], '0');
		} else {
			if ($type) {
				return ($_number[0] === '-0' ? '-' : '') . number_format($_number[0]);
			}
			return $_number[0];
		}
	} else {
		return $number;
	}
}

function CreateCode($object = "", $id = '', $date = '', $listColum = [], $fullcode = false)
{
	$CI = &get_instance();
	if (empty($id)) {
		return false;
	}
	if (empty($date)) {
		$date = date('Y-m-d');
	}
	$arrayPrefix = [];
	$CI->db->where('date = DATE_FORMAT("' . $date . '", "%Y-%m-%d")');
	$prefix_object = $CI->db->get('tblprefix_object')->row();
	if (empty($prefix_object)) {
		$CI->db->insert('tblprefix_object', [
			'prefix' => strftime('%y%m%d', strtotime($date)),
			'date' => $date
		]);
		if (!empty($CI->db->insert_id())) {
			$CI->db->where('date', $date);
			$prefix_object = $CI->db->get('tblprefix_object')->row();
		}
	}
	if ($object == 'client') {
		$CI->db->select('sum(orders_client) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'prefix_client' => $prefix_object->prefix,
			'code_client' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object_num->sum_row + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('userid', $id);
		if ($CI->db->update('tblclients', $arrayUpdate)) {
			$arrayPrefix['orders_client'] = ($prefix_object->orders_client + 1);
		}
	} else if ($object == 'lead') {
		$CI->db->select('sum(orders_lead) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'prefix_lead' => $prefix_object->prefix,
			'code_lead' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object_num->sum_row + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('id', $id);
		if ($CI->db->update('tblleads', $arrayUpdate)) {
			$arrayPrefix['orders_lead'] = ($prefix_object->orders_lead + 1);
		}
	} else if ($object == 'listfb') {
		$CI->db->select('sum(orders_listfb) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'prefix' => $prefix_object->prefix,
			'code' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object_num->sum_row + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('id', $id);
		if ($CI->db->update('tbllist_fb', $arrayUpdate)) {
			$arrayPrefix['orders_listfb'] = ($prefix_object->orders_listfb + 1);
		}
	} else if ($object == 'advisory') {
		$CI->db->select('sum(prefix_advisory_lead) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'prefix' => 'C-' . $prefix_object->prefix,
			'code' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object_num->sum_row + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('id', $id);
		if ($CI->db->update('tbladvisory_lead', $arrayUpdate)) {
			$arrayPrefix['prefix_advisory_lead'] = ($prefix_object->prefix_advisory_lead + 1);
		}
	} else if ($object == 'care_of_clients') {
		$CI->db->select('sum(prefix_care_of_clients) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'prefix' => 'CS-',
			'code' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object->prefix_care_of_clients + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('id', $id);
		if ($CI->db->update('tblcare_of_clients', $arrayUpdate)) {
			$arrayPrefix['prefix_care_of_clients'] = ($prefix_object->prefix_care_of_clients + 1);
		}
	} else if ($object == 'orders') {
		$CI->db->select('sum(orders) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'code' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object_num->sum_row + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('id', $id);
		if ($CI->db->update('tblorders', $arrayUpdate)) {
			$arrayPrefix['orders'] = ($prefix_object->orders + 1);
		}
	} else if ($object == 'orders_draft') {
		$CI->db->select('sum(orders_draft) as sum_row');
		$prefix_object_num = $CI->db->get('tblprefix_object')->row();
		$arrayUpdate = [
			'code' => sprintf("%06s", ($prefix_object_num->sum_row + 1))
		];
		if (!empty($listColum)) {
			foreach ($listColum as $key => $value) {
				$arrayUpdate[$value] = sprintf("%06s", ($prefix_object->sum_row + 1));
				if (!empty($fullcode)) {
					$arrayUpdate[$value] = $prefix_object->prefix . $arrayUpdate[$value];
				}
			}
		}
		$CI->db->where('id', $id);
		if ($CI->db->update('tblorders', $arrayUpdate)) {
			$arrayPrefix['orders_draft'] = ($prefix_object->orders_draft + 1);
		}
	}
	if (!empty($arrayPrefix)) {
		$CI->db->where('id', $prefix_object->id);
		if ($CI->db->update('tblprefix_object', $arrayPrefix)) {
			return true;
		}
	}
	return false;
}

function DropdownListexpErience($id_detail, $id_active, $idAdvisory)
{
	$CI = &get_instance();
	$CI->db->where('id_detail', $id_detail);
	$experience_detail = $CI->db->get('tblexperience_advisory_detail')->result_array();
	$option = '';
	foreach ($experience_detail as $kDetail => $vDetail) {
		$selected = '';
		if (!empty($id_active)) {
			foreach ($id_active as $kA => $vA) {
				if ($vA == $vDetail['id']) {
					$selected = 'selected';
					break;
				}
			}
		}
		$option .= "<option value='" . $vDetail['id'] . "' " . $selected . ">" . $vDetail['name'] . "</option>";
	}
	$html = "<select name='erience[" . $id_detail . "][]' id-data='" . $idAdvisory . "' class='SelectErience' multiple style='width: 100%'>" . $option . "</select>";
	return $html;
}

function DropdownListexpErienceCare_of($id_detail, $id_active, $idAdvisory, $id_care_items = NULL)
{
	$CI = &get_instance();
	$CI->db->where('id_detail', $id_detail);
	$experience_detail = $CI->db->get('tblexperience_care_of_client_detail')->result_array();
	$option = '';
	foreach ($experience_detail as $kDetail => $vDetail) {
		$selected = '';
		if (!empty($id_active)) {
			foreach ($id_active as $kA => $vA) {
				if ($vA == $vDetail['id']) {
					$selected = 'selected';
					break;
				}
			}
		}
		$option .= "<option value='" . $vDetail['id'] . "' " . $selected . ">" . $vDetail['name'] . "</option>";
	}
	$html = "<select name='erience[" . $id_detail . "][]' id-data='" . $idAdvisory . "' id_care_items='" . $id_care_items . "' class='SelectErience' multiple style='width: 100%'>" . $option . "</select>";
	return $html;
}

function DropdownListexpErienceStaff($id_detail, $id_active, $idAdvisory, $id_care_items = NULL)
{
	$CI = &get_instance();
	$option = "<option value='0'></option>";
	$CI->db->where('active', 1);
	$staff = $CI->db->get('tblstaff')->result_array();
	foreach ($staff as $kDetail => $vDetail) {
		$selected = '';
		if (!empty($id_active) && $vDetail['staffid'] == $id_active) {
			$selected = 'selected';
		}
		$option .= "<option value='" . $vDetail['staffid'] . "' " . $selected . ">" . $vDetail['lastname'] . ' ' . $vDetail['firstname'] . "</option>";
	}
	$html = "<select name='erience[" . $id_detail . "]' id-data='" . $idAdvisory . "' id_care_items='" . $id_care_items . "' class='SelectErience' style='width: 100%'>" . $option . "</select>";
	return $html;
}

function DropdownListexpErienceType($id_detail, $value, $idAdvisory, $class = "", $id_care_items = NULL)
{
	$CI = &get_instance();
	$html = "<div class='form-group'>";
	$html .= "  <input name='erience[" . $id_detail . "]' id-data='" . $idAdvisory . "' id_care_items='" . $id_care_items . "' class='SelectErience form-control " . $class . "' value='" . $value . "'/>";
	$html .= "</div>";
	$html .= "<div class='clearfix'></div>";
	return $html;
}

function DropdownListexpErienceFile($id_detail, $idAdvisory, $class = "", $type = "", $id_care_items = NULL)
{
	$CI = &get_instance();
	$html = "<form action='" . (admin_url('care_of_clients/erience_img/' . $idAdvisory . '/' . $id_detail . '/' . $id_care_items)) . "' id='form_img_care_of' autocomplete='off' enctype='multipart/form-data' method='post' accept-charset='utf-8'>";
	$html .= "  <div class='form-group'>";
	$html .= "      <input type='file' name='file[]' id='file_erience' multiple class='FileErience form-control " . $class . "'/>";
	$html .= "  </div>";
	$html .= "</form>";
	$html .= "<div class='clearfix'></div>";
	return $html;
}

function StatusActiveAdvisory()
{
	$_ArrayStatus = [
		'0' => [
			'name' => _l('create_new'),
			'class' => 'label-warning',
			'orders_procedure' => 1
		], //Phiếu Tư Vấn Vừa Tạo
		'1' => [
			'name' => _l('not_enough_infomation_find'),
			'class' => 'label-default',
			'orders_procedure' => 2
		],//Chưa Đủ Thông Tin Lọc
		'2' => [
			'name' => _l('not_enough_criteria'),
			'class' => 'label-siver',
			'orders_procedure' => 2
		], //Khách KHÔNG Đủ Tiêu Chí
		'3' => [
			'name' => _l('enough_criteria'),
			'class' => 'label-green',
			'orders_procedure' => 2
		], //Khách ĐỦ Tiêu Chí,
		'4' => [
			'name' => _l('send_ls'),
			'class' => 'label-info',
			'orders_procedure' => 3
		],//Gửi LS
		'5' => [
			'name' => _l('active_sale'),
			'class' => 'label-danger',
			'orders_procedure' => 4
		], // Chốt Sales
		'6' => [
			'name' => _l('transfer_money'),
			'class' => 'label-danger',
			'orders_procedure' => 5
		], //Chuyển khoản
		'7' => [
			'name' => _l('stop_advesory'),
			'class' => 'label-c',
			'orders_procedure' => 6
		] //Dừng tư vấn
	];
	return $_ArrayStatus;
}

function priority_level_advisory($status_active = '', $id = '')
{
	$CI = &get_instance();
	if ($status_active == '0') //phiếu vừa mới tạo
	{
		$CI->db->where('orders_status', 2); // Ngày Dự Kiến Lọc Khách
	} else if ($status_active == '1' || $status_active == '3') //[CHƯA ĐỦ THÔNG TIN LỌC] hoặc [KHÁCH ĐỦ TIÊU CHÍ]
	{
		$CI->db->where('orders_status', 3);// Ngày Dự Kiến Gửi LS
	} else if ($status_active == '4') //GỬI LS
	{
		$CI->db->where('orders_status', 4); //Ngày Dự Kiến Chốt Sales
	} else if ($status_active == '5') //CHỐT SALES
	{
		$CI->db->where('orders_status', 5);
	}
	$CI->db->where('id_advisory', $id);
	$procedure_advisory = $CI->db->get('tblprocedure_advisory_lead')->row();
	$_dateDK = '';
	if (!empty($procedure_advisory)) {
		$_dateDK = $procedure_advisory->date_expected;
	}
	$priority = '';
	if ($status_active == '2' || $status_active == '7') {
		$priority = _l('cong_stop');
	} else if ($status_active == '6') {
		$priority = _l('cong_Finish');
	} else if ($status_active == '0' || $status_active == '1' || $status_active == '3' || $status_active == '4' || $status_active == '5') {
		$dateNow = new DateTime(date('Y-m-d'));
		$dateExpected = new DateTime($_dateDK);
		$interval = $dateNow->diff($dateExpected);
		$day = $interval->d;
		if ($day >= 3) {
			$priority = _l('cong_in_safe_time');
		} else if ($day >= 1 && $day < 3) {
			$priority = _l('cong_need_to_do_now');
		} else if ($day >= -3 && $day < 1) {
			$priority = _l('cong_priority_level_1');
		} else if ($day >= -5 && $day < -3) {
			$priority = _l('cong_priority_level_2');
		} else {
			$priority = _l('cong_priority_level_3');
		}
	}
	return $priority;
}

function StatusCriteria()
{
	$_ArrayCriteria = [
		'0' => [
			'name' => _l('not_criteria'),
			'class' => 'label-warning'
		], //Chưa lọc
		'1' => [
			'name' => _l('cong_not_pass'),
			'class' => 'label-danger'
		], //Không duyệt
		'2' => [
			'name' => _l('cong_pass'),
			'class' => 'label-success'
		],//Duyệt
	];
	return $_ArrayCriteria;
}

function createCodeNameSystem($type = 'client', $id)
{
	if (!empty($id)) {
		$CI = &get_instance();
		if ($type == 'client') {
			$CI->db->where('userid', $id);
			$clients = $CI->db->get('tblclients')->row();
			if (!empty($clients)) {
				$nameSystem = "";
				$CI->db->select('CONCAT(COALESCE(tblstaff.lastname)," ",COALESCE(tblstaff.firstname)) as fullname');
				$CI->db->where('customer_id', $clients->userid);
				$CI->db->join('tblstaff', 'tblstaff.staffid = tblcustomer_admins.staff_id');
				$CI->db->order_by('date_assigned', 'desc');
				$customer = $CI->db->get('tblcustomer_admins')->row();
				if (!empty($customer)) {
					$nameSystem .= $customer->fullname;
				}
				$nameSystem .= "-";
				$nameSystem .= $clients->name_facebook . '-' . $clients->fullname . '-' . $clients->zcode;
				$CI->db->where('userid', $id);
				$success = $CI->db->update('tblclients', ['name_system' => $nameSystem]);
				if (!empty($success)) {
					return true;
				}
			}
		} else if ($type == 'lead') {
			$CI->db->where('id', $id);
			$lead = $CI->db->get('tblleads')->row();
			if (!empty($lead)) {
				$nameSystem = "";
				$CI->db->select('CONCAT(COALESCE(tblstaff.lastname)," ",COALESCE(tblstaff.firstname)) as fullname');
				$CI->db->where('id_lead', $lead->id);
				$CI->db->join('tblstaff', 'tblstaff.staffid = tbllead_assigned.staff');
				$CI->db->order_by('date_create', 'desc');
				$customer = $CI->db->get('tbllead_assigned')->row();
				if (!empty($customer)) {
					$nameSystem .= $customer->fullname;
				}
				$nameSystem .= "-";
				$nameSystem .= $lead->name_facebook . '-' . $lead->name . '-' . $lead->zcode;
				$CI->db->where('id', $id);
				$success = $CI->db->update('tblleads', ['name_system' => $nameSystem]);
				if (!empty($success)) {
					return true;
				}
			}
		}
	}
	return false;
}

function StatusThemeCare_of($id = "")
{
	$_ArrayStatus = [
		'1' => [
			'name' => _l('customers_ask_more'),
			'class' => 'label-default',
			'id' => 1,
			'short' => 'AS'
		],//Khách Hàng Hỏi Thêm
		'2' => [
			'name' => _l('not_enough_criteria'),
			'class' => 'label-siver',
			'id' => 2,
			'short' => 'ND'
		], //Khách Hàng Phát Sinh Nhu Cầu Mới
		'3' => [
			'name' => _l('customer_report'),
			'class' => 'label-green',
			'id' => 3,
			'short' => 'CR'
		], //Khách Hàng Báo Cáo
		'4' => [
			'name' => _l('note_type_of_gifts'),
			'class' => 'label-info',
			'id' => 4,
			'short' => 'FG'
		],//Ghi Chú Loại Quà Khách Tặng
		'5' => [
			'name' => _l('special_care_theme_special_occasion'),
			'class' => 'label-danger',
			'id' => 5,
			'short' => 'SO'
		], // Chủ Đề Chăm Sóc KH Dịp Đặc Biệt
		'6' => [
			'name' => _l('feedback_type_on_order'),
			'class' => 'label-danger',
			'id' => 6,
			'short' => 'CF'
		], //Loại Phản Hồi Về Đơn Hàng
	];
	if (!empty($id)) {
		return $_ArrayStatus[$id];
	}
	return $_ArrayStatus;
}

function care_solutions($id = "")
{
	$_ArrayStatus = [
		'1' => [
			'name' => _l('transfer_to_another_department'),
			'class' => 'label-default',
			'id' => 1
		],//+ Chuyển Qua Bộ Phận Khác
		'2' => [
			'name' => _l('direct_consultation_for_customers'),
			'class' => 'label-siver',
			'id' => 2
		], //+ Tư Vấn Trực Tiếp Cho Khách Hàng
		'3' => [
			'name' => _l('calling_customers'),
			'class' => 'label-green',
			'id' => 3
		], //+ Gọi Điện Thoại Cho Khách Hàng
		'4' => [
			'name' => _l('other'),
			'class' => 'label-info',
			'id' => 4
		],//Khác
	];
	if (!empty($id)) {
		return $_ArrayStatus[$id];
	}
	return $_ArrayStatus;
}

function getItemsCare_of_Orders($id_care_of = "")
{
	$CI = &get_instance();
	if (!empty($id_care_of)) {
		$CI->db->select('
                tblcare_of_client_items.*,
                IF(tblcare_of_client_items.type_items = "items", tblitems.name, tbl_products.name) as name,
                IF(tblcare_of_client_items.type_items = "items", tblitems.code, tbl_products.code) as code
            ');
		$CI->db->where('id_care_of', $id_care_of);
		$CI->db->join('tbl_products', 'tbl_products.id = tblcare_of_client_items.id_product and tblcare_of_client_items.type_items = "products"', 'LEFT');
		$CI->db->join('tblitems', 'tblitems.id = tblcare_of_client_items.id_product and tblcare_of_client_items.type_items = "items"', 'LEFT');
		$product_care_of = $CI->db->get('tblcare_of_client_items')->result_array();
		return $product_care_of;
	}
	return false;
}

function get_table_where_select_cong($table = "", $where = [], $orders = '', $select = '', $result = 'result_array')
{
	$CI = &get_instance();
	if (!empty($table)) {
		if (!empty($select)) {
			$CI->db->select($select);
		}
		if (!empty($where)) {
			$CI->db->where($where);
		}
		if (!empty($orders)) {
			$CI->db->order_by($orders);
		}
		$dataTable = $CI->db->get($table)->$result();
		return $dataTable;
	}
	return false;
}

function get_table_query_cong($query = "")
{
	$CI = &get_instance();
	if (!empty($query)) {
		$data = $CI->db->query($query)->result_array();
		return $data;
	}
	return false;
}

function GetPriority_order($status_active = "", $id = "", $id_orders_item = "")
{
	$CI = &get_instance();
	$priority = '';
	if ($status_active == '-3') {
		$priority = '<b class="text-danger">' . _l('cong_stop') . '</b>';
	} else if ($status_active == '8' || $status_active == '0') // đã Chăm Sóc KH Sau Khi Nhận ĐH OR Đã Giải Quyết Xong Vấn Đề => HOÀN THÀNH
	{
		$priority = '<b class="text-success">' . _l('cong_Finish') . '</b>';
	} else if ($status_active == '-2') {
		$priority = '<b class="text-danger">' . _l('cong_priority_level_3') . '</b>';
	} else if ($status_active == '1' || $status_active == '4' || $status_active == '6' || $status_active == '7') {
		if ($status_active == '1') //phiếu vừa mới tạo
		{
			$CI->db->where('order_by', 4); // NGÀY DỰ KIẾN HOÀN THIỆN SẢN PHẨM
		} else if ($status_active == '4') //HOÀN THIỆN SẢN PHẨM
		{
			$CI->db->where('order_by', 6);// NGÀY DỰ KIẾN ĐÓNG GÓI
		} else if ($status_active == '6') //ĐÓNG GÓI
		{
			$CI->db->where('order_by', 7); //Ngày Dự Kiến Book Vận Chuyển
		} else if ($status_active == '7') //CHỐT SALES
		{
			$CI->db->where('order_by', 8);
		}
		$CI->db->where('id_orders', $id);
		$CI->db->where('id_orders_item', $id_orders_item);
		$orders_step = $CI->db->get('tblorders_step')->row();
		if (!empty($orders_step)) {
			$_dateDK = $orders_step->date_expected;
		}
		$dateNow = new DateTime(date('Y-m-d'));
		$dateExpected = new DateTime($_dateDK);
		$interval = $dateNow->diff($dateExpected);
		$day = $interval->d;
		if ($day >= 3) {
			$priority = '<b class="text-info">' . _l('cong_in_safe_time') . '</b>';
		} else if ($day >= 1 && $day < 3) {
			$priority = '<b class="text-primary">' . _l('cong_need_to_do_now') . '</b>';
		} else if ($day >= -3 && $day < 1) {
			$priority = '<b class="text-warning">' . _l('cong_priority_level_1') . '</b>';
		} else if ($day >= -5 && $day < -3) {
			$priority = '<b class="text-danger">' . _l('cong_priority_level_2') . '</b>';
		} else {
			$priority = '<b class="text-danger">' . _l('cong_priority_level_3') . '</b>';
		}
	} else {
		$priority = '<b class="text-info">' . _l('cong_care_of_client') . '</b>';
	}
	return $priority;
}

function TAG_manuals($key = "")
{
	$data = [
		1 => [
			'name' => _l('cong_classify_client'),
			'color' => 'danger'
		],
		2 => [
			'name' => _l('cong_status_advisory'),
			'color' => 'warning'
		],
		3 => [
			'name' => _l('cong_status_receipts'),
			'color' => 'info'
		],
		4 => [
			'name' => _l('cong_status_order'),
			'color' => 'primary'
		],
		5 => [
			'name' => _l('cong_status_care_of'),
			'color' => 'success'
		],
	];
	if (!empty($key)) {
		return $data[$key];
	}
	return $data;
}

function ChangeTag_manuals($type_object = '', $id_object = '', $advisory, $action = [])
{
	$CI = &get_instance();
	$success = false;
	if (!empty($type_object) && !empty($id_object) && !empty($advisory) && !empty($action)) {
		$arrayAction = [];
		if ($type_object == 'client') {
			$client = get_table_where('tblclients', ['userid' => $id_object], '', 'row');
			if (!empty($client)) {
				$arrayAction['id_facebook'] = $client->id_facebook;
			}
		} else if ($type_object == 'lead') {
			$lead = get_table_where('tblleads', ['id' => $id_object], '', 'row');
			if (!empty($lead)) {
				$arrayAction['id_facebook'] = $lead->id_facebook;
			}
		}
		$CI->db->where('advisory', $advisory);
		$tag_manuals = $CI->db->get('tbltag_manuals')->row();
		if (!empty($tag_manuals)) {
			foreach ($action as $key => $value) {
				if ($key != 'status_care_of') {
					$arrayAction[$key] = $value;
				} else {
					$arrayAction['status_care_of'] = 1;
				}
			}
			$CI->db->where('id', $tag_manuals->id);
			$success = $CI->db->update('tbltag_manuals', $arrayAction);
		} else {
			foreach ($action as $key => $value) {
				$arrayAction[$key] = $value;
			}
			$success = $CI->db->insert('tbltag_manuals', $arrayAction);
		}
	}
	return $success;
}

function GetTagMunualsFacebook($id_facebook = "")
{
	$CI = &get_instance();
	if (!empty($id_facebook)) {
		$arrayReturn = [];
		$CI->db->select('tbltag_manuals.*, tbladvisory_lead.id as id_advisory, tblorders.id as id_orders');
		$CI->db->where('id_facebook', $id_facebook);
		$CI->db->join('tbladvisory_lead', 'tbladvisory_lead.id = tbltag_manuals.advisory', 'left');
		$CI->db->join('tblorders', 'tblorders.id = tbltag_manuals.orders', 'left');
		$tag_manuals = $CI->db->get('tbltag_manuals')->result();
		if (!empty($tag_manuals)) {
			foreach ($tag_manuals as $keyTag => $valTag) {
				if (!empty($valTag->status_care_of)) {
					$CI->db->where('id_orders', $valTag->id_orders);
					$numCareof = $CI->db->get('tblcare_of_clients')->num_rows();
					if (!empty($numCareof)) {
						$arrayReturn[] = TAG_manuals(5);
					}
				} else if (!empty($valTag->id_orders)) {
					$arrayReturn[] = TAG_manuals(4);
				} else if (!empty($valTag->id_advisory)) {
					$CI->db->where('id_advisory', $valTag->id_advisory);
					$CI->db->where('status_procedure', 6);
					$CI->db->where('active', 1);
					$receipts = $CI->db->get('tblprocedure_advisory_lead')->num_rows();
					if (!empty($receipts)) {
						$arrayReturn[] = TAG_manuals(3);
					} else {
						$arrayReturn[] = TAG_manuals(2);
					}
				}
			}
		} else {
			$arrayReturn[] = TAG_manuals(1);
		}
		return $arrayReturn;
	}
	return false;
}

function ChangeObjectAssigned($type_one = "", $id_one = "", $type_two = "", $id_two = "")
{
	$CI = &get_instance();
	if (!empty($type_one) && !empty($type_two) && !empty($id_one) && !empty($id_one)) {
		if ($type_one == 'listfb') {
			$CI->db->select('staff');
			$CI->db->where('id_listfb', $id_one);
			$assigned_one = $CI->db->get('tbllistfb_assigned')->result_array();
		} else if ($type_one == 'lead') {
			$CI->db->select('staff');
			$CI->db->where('id_lead', $id_one);
			$assigned_one = $CI->db->get('tbllead_assigned')->result_array();
		}
		if (!empty($assigned_one)) {
			foreach ($assigned_one as $key => $value) {
				if ($type_two == 'lead') {
					$CI->db->insert('tbllead_assigned', [
						'staff' => $value['staff'],
						'id_lead' => $id_two,
						'created_by' => get_staff_user_id(),
						'date_create' => date('Y-m-d H:i:s')
					]);
				}
				if ($type_two == 'client') {
					$CI->db->insert('tblcustomer_admins', [
						'staff_id' => $value['staff'],
						'customer_id' => $id_two,
						'date_assigned' => date('Y-m-d H:i:s')
					]);
				}
			}
		}
	}
}

function get_client_ip()
{
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if (isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

function ktLoginIP()
{
	$CI = &get_instance();
	$ip_client = get_client_ip();
	$CI->db->where('ip', $ip_client);
	$ktIp = $CI->db->get('tbl_ip_login')->row();
	$staffLogin = get_staff_user_id();
	$date_now = date('Y-m-d H:i:s');
	if (!empty($ktIp) || $CI->session->userdata('staff_ip')) {
		return true;
	} else {
		$CI->db->where('DATE_FORMAT(date_end_login, "%Y-%m-%d  %H:%i:%s") >= "' . $date_now . '"');
		$CI->db->where('staffid', $staffLogin);
		$CI->db->where('active', 1);
		$ktIp = $CI->db->get('tbl_ip_login_active')->row();
		if (!empty($ktIp)) {
			$user_data = ['staff_ip' => true];
			$CI->session->set_userdata($user_data);
			return true;
		} else {
			$CI->db->where('DATE_FORMAT(date_end_code, "%Y-%m-%d %H:%i:%s") >= "' . $date_now . '"');
			$CI->db->where('staffid', $staffLogin);
			$CI->db->where('active', 0);
			$ktIp = $CI->db->get('tbl_ip_login_active')->row();
			if (!empty($ktIp)) {
				$CI->load->view('admin/ip_login/manage', [
					'code_active' => $ktIp->code_active,
					'date_end_code' => $ktIp->date_end_code
				]);
				die();
			} else {
				$date_end_code = strtotime('+120 seconds', strtotime($date_now));
				$date_end_code = date('Y-m-d H:i:s', $date_end_code);
				$code_active = rand(100000, 999999);
				$CI->db->insert('tbl_ip_login_active', [
					'ip' => $ip_client,
					'staffid' => $staffLogin,
					'date_end_code' => $date_end_code,
					'active' => 0,
					'code_active' => $code_active,
				]);
				$staff = get_table_where('tblstaff', ['staffid' => $staffLogin], '', 'row');
				$content = "NV " . $staff->firstname . ' ' . $staff->lastname .
					' co email ' . $staff->email .
					' dang login tu IP ' . $ip_client . ' va co Ma Login la: ' . $code_active . ' va co thoi han 120s';
				send_email(get_option('phone_login_ip'), $content);
				$CI->load->view('admin/ip_login/manage', [
					'code_active' => $code_active,
					'date_end_code' => $date_end_code
				]);
				die();
			}
		}
	}
	return false;
}

function send_email($email = "", $content = "")
{
	$CI = &get_instance();
	if (!empty($email)) {
		$CI->load->config('email');
		$template = new StdClass();
		$template->message = get_option('email_header') . $content . get_option('email_footer');
		$template->fromname = get_option('companyname') != '' ? get_option('companyname') : 'ADMIN';
		$template->subject = _l('Thông báo');
		hooks()->do_action('before_send_test_smtp_email');
		$CI->email->initialize();
		if (get_option('mail_engine') == 'phpmailer') {
			$CI->email->set_debug_output(function ($err) {
				if (!isset($GLOBALS['debug'])) {
					$GLOBALS['debug'] = '';
				}
				$GLOBALS['debug'] .= $err . '<br />';
				return $err;
			});
			$CI->email->set_smtp_debug(3);
		}
		$CI->email->set_newline(config_item('newline'));
		$CI->email->set_crlf(config_item('crlf'));
		$CI->email->from(get_option('smtp_email'), $template->fromname);
		$CI->email->to($email);
		//      $systemBCC = get_option('bcc_emails');
		//
		//      if ($systemBCC != '') {
		//          $CI->email->bcc($systemBCC);
		//      }
		$CI->email->subject($template->subject);
		$CI->email->message($template->message);
		if ($CI->email->send(true)) {
			return true;
		}
	}
	return false;
}

function getFeedBackStaff()
{
	$CI = &get_instance();
	$CI->db->select([
		'CONCAT(firstname, " ", lastname) as name',
		'staffid as id',
		'"contact" as type',
		'(
					      CASE 
						      WHEN profile_image IS NOT NULL THEN CONCAT("' . base_url('uploads/staff_profile_images/') . '", staffid, "/small_", profile_image)
						      ELSE CONCAT("' . base_url('assets/images/user-placeholder.jpg') . '")
					      END
			        ) AS avatar'
	]);
	$CI->db->where('active', 1);
	return $CI->db->get('tblstaff')->result_array();
}

function getFeedBackStaff_stages()
{
	$CI = &get_instance();
	$CI->db->select([
		'CONCAT("Nhân Viên: ", firstname, " ", lastname) as name',
		'staffid as id',
		'"contact" as type',
		'(
                          CASE 
                              WHEN profile_image IS NOT NULL THEN CONCAT("' . base_url('uploads/staff_profile_images/') . '", staffid, "/small_", profile_image)
                              ELSE CONCAT("' . base_url('assets/images/user-placeholder.jpg') . '")
                          END
                    ) AS avatar'
	]);
	$CI->db->where('active', 1);
	$staff = $CI->db->get('tblstaff')->result_array();
	$stages = $CI->db->get('tbl_stages')->result_array();
	foreach ($stages as $key => $value) {
		$staff[] = [
			'name' => 'Công Đoạn: ' . $value['name'],
			'id' => 'stages_' . $value['id'],
			"type" => "stages",
			'avatar' => ''
		];
	}
	return $staff;
}

if (!function_exists('c_html_to_text')) {
	function c_html_to_text($text)
	{
		$text = html_entity_decode(trim($text));
		$text = str_replace("&nbsp;", ' ', $text);
		$text = str_replace("<br>", "\n", $text);
		$text = preg_replace("/<br>|\n|<br( ?)\/>/", "\n", $text);
		$text = preg_replace("/&nbsp;/", " ", $text);
		$text = trim(strip_html_tags($text, '<br/>, <br>, <a>'));
		$text = trim(strip_tags($text, '<br/>, <br>, <a>'));
		return $text;
	}
}
function number_format_data_four($number, $type = true)
{
	if (!empty($number)) {
		$number = str_replace(",", "", $number);
		$number = str_replace(",", "", number_format($number, 4));
		$_number = explode('.', $number);
		if (rtrim($_number[1], '0') != "") {
			if ($type) {
				return ($_number[0] === '-0' ? '-' : '') . number_format($_number[0]) . '.' . rtrim($_number[1], '0');
			}
			return $_number[0] . '.' . rtrim($_number[1], '0');
		} else {
			if ($type) {
				return ($_number[0] === '-0' ? '-' : '') . number_format($_number[0]);
			}
			return $_number[0];
		}
	} else {
		return $number;
	}
}



//cong thêm mới thống kê trạng thái phiếu sản xuất
function getNumberStatusProduction($date_start_after, $date_end_after) {
	$CI = &get_instance();

	//Tất cả trừ phiếu trừ phiếu hủy
	$CI->db->where('DATE_FORMAT(date, "%Y-%m-%d") >= "'.$date_start_after.'"')
		->where('DATE_FORMAT(date, "%Y-%m-%d") <= "'.$date_end_after.'"')
		->where('status_orders', 0);
	$data['statusAllNotCancel'] = $CI->db->get('tbl_productions_orders')->num_rows();

	//phiếu sản xuất tạm dừng
	$CI->db->where('DATE_FORMAT(date, "%Y-%m-%d") >= "'.$date_start_after.'"')
		->where('DATE_FORMAT(date, "%Y-%m-%d") <= "'.$date_end_after.'"')
		->where('status_orders', 2);
	$data['statusPause'] = $CI->db->get('tbl_productions_orders')->num_rows();


	//Hoàn thành
	$CI->db->where('tbl_productions_orders.id NOT IN(
                            SELECT productions_orders_id 
                            FROM tbl_productions_orders_items_stages 
                            WHERE final_stage = 1 AND active = 0
                            AND pois_id = 0
                        )', false, false);
	$CI->db->where('EXISTS (
                                SELECT productions_orders_id 
                                FROM tbl_productions_orders_items_stages 
                                WHERE final_stage = 1
                                AND tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id
                                AND pois_id = 0
                            )', false, false);
	$CI->db->where('DATE_FORMAT(date, "%Y-%m-%d") >= "'.$date_start_after.'"')
		->where('DATE_FORMAT(date, "%Y-%m-%d") <= "'.$date_end_after.'"')
		->where('status_orders', 0);
	$data['statusSuccess'] = $CI->db->get('tbl_productions_orders')->num_rows();

	//chưa sản xuất
	$CI->db->where('tbl_productions_orders.id NOT IN(
                                                SELECT productions_orders_id 
                                                FROM tbl_productions_orders_items_stages 
                                                WHERE active = 1 AND tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id
                                                AND pois_id = 0
                                            )', false, false);
	$CI->db->where('EXISTS (
                                SELECT productions_orders_id 
                                FROM tbl_productions_orders_items_stages 
                                WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id
                                AND pois_id = 0
                            )', false, false);
	$CI->db->where('DATE_FORMAT(date, "%Y-%m-%d") >= "'.$date_start_after.'"')
		->where('DATE_FORMAT(date, "%Y-%m-%d") <= "'.$date_end_after.'"')
		->where('status_orders', 0);
	$data['statusNotProduction'] = $CI->db->get('tbl_productions_orders')->num_rows();


	$data['statusProcessing'] = $data['statusAllNotCancel'] - $data['statusSuccess'] - $data['statusNotProduction'];// phiếu đang sản xuất

	$CI->db->where('DATE_FORMAT(date, "%Y-%m-%d") >= "'.$date_start_after.'"')
		->where('DATE_FORMAT(date, "%Y-%m-%d") <= "'.$date_end_after.'"')
		->where('status_orders', 1);
	$data['statusCancel'] = $CI->db->get('tbl_productions_orders')->num_rows();

	$CI->db->where('DATE_FORMAT(date, "%Y-%m-%d") >= "'.$date_start_after.'"')
		->where('DATE_FORMAT(date, "%Y-%m-%d") <= "'.$date_end_after.'"');
	$data['statusAll'] = $CI->db->get('tbl_productions_orders')->num_rows();

	return $data;
}

function get_product_production_top($date_start = '', $date_end = '') {
	$CI = &get_instance();
	$CI->db->select('SUM(quantity) as quantity, items_id, items_code, items_name');
	$CI->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
	if(!empty($date_start)) {
		$CI->db->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >= "'.$date_start.'"');
	}
	if(!empty($date_end)) {
		$CI->db->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <= "'.$date_end.'"');
	}
	$CI->db->limit(10);
	$CI->db->order_by('SUM(quantity)', 'desc');
	$CI->db->group_by('items_id');
	$data_productions = $CI->db->get('tbl_productions_orders_items')->result_array();
	$data = [];
	$color = [
		'rgb(255, 99, 132)',
		'rgb(75, 192, 192)',
		'rgb(255, 205, 86)',
		'rgb(201, 203, 207)',
		'rgb(54, 162, 235)',
		'rgb(255, 162, 235)',
		'rgb(54, 162, 54)',
		'rgb(0,59,147)',
		'rgb(99, 99, 99)',
		'rgb(192, 192, 192)',
	];
	$data['datasets'] = [
		'label' => '',
		'data' => [],
		'backgroundColor' => []
	];
	foreach($data_productions as $key => $value) {
		$data['labels'][] = $value['items_code'];
//			$data['labels'][] = $key;
		$data['datasets']['data'][] = (float)$value['quantity'];
		$data['datasets']['backgroundColor'][] = $color[$key];
	}
	$data['datasets'] = [$data['datasets']];
	return ($data);
}

//lấy danh sách nguyên vật liệu cần thu mua
function get_nvl_need_buy() {
	$CI = &get_instance();
	$wherePL = '';
	$whereTranfer = '';
	$wherePurchase = '';

	$tbWarehouseProduct = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY."
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

	$tbWarehouseMaterials = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY."
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

	$tbTransfer = "(
            SELECT
                tbltransfer_warehouse_detail.type as type, 
                tbltransfer_warehouse_detail.id_items as id_items,
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse.productions_capacity_id > 0 $whereTranfer
            GROUP BY tbltransfer_warehouse_detail.type, tbltransfer_warehouse_detail.id_items
        ) tb_transfer";
	$tbProductionsPlanBom = "(
            (
                SELECT 
                    tbl_productions_plan_bom.item_id as item_id,
                    tbl_productions_plan_bom.id as id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_productions_plan_bom.item_type as item_type,
                    SUM(tbl_productions_plan_bom.quantity_primary) as quantity_primary,
                    SUM(tbl_productions_plan_bom.quantity) as quantity,
                    tblunits.unit as unit_name,
                    unit_primary.unit as unit_primary_name,
                    tb_quantity_warehouse.product_quantity as quantity_inventory,
                    tb_transfer.quantity as quantity_transfer
                FROM tbl_productions_plan_bom
                INNER JOIN tbl_products ON tbl_products.id = tbl_productions_plan_bom.item_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                LEFT JOIN $tbWarehouseProduct ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_products.id AND tb_transfer.type = 'product'
                WHERE tbl_productions_plan_bom.item_type IN ('semi_products_outside') $wherePL
                GROUP BY tbl_productions_plan_bom.item_id
            )
            UNION ALL
            (
                SELECT 
                    tbl_productions_plan_bom.item_id as item_id,
                    tbl_productions_plan_bom.id as id,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_productions_plan_bom.item_type as item_type,
                    SUM(tbl_productions_plan_bom.quantity_primary ) as quantity_primary,
                    SUM(tbl_productions_plan_bom.quantity) as quantity,
                    tblunits.unit as unit_name,
                    unit_primary.unit as unit_primary_name,
                    tb_quantity_warehouse.product_quantity as quantity_inventory,
                    tb_transfer.quantity as quantity_transfer
                FROM tbl_productions_plan_bom
                INNER JOIN tbl_materials ON tbl_materials.id = tbl_productions_plan_bom.item_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                LEFT JOIN $tbWarehouseMaterials ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_materials.id AND tb_transfer.type = 'nvl'
                WHERE tbl_productions_plan_bom.item_type IN ('materials') $wherePL
                GROUP BY tbl_productions_plan_bom.item_id
            )
        ) tb_cs";

	$tbPurchase = "(
            SELECT
                IF(tblpurchases_items.type = 'nvl', 'materials', 'products') as type_item, 
                tblpurchases_items.type as type,
                tblpurchases_items.product_id as product_id,
                SUM(tblpurchases_items.quantity_net) as quantity_net
            FROM tblpurchases
            INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
            WHERE tblpurchases.type_plan = 1 AND tblpurchases_items.type IN ('product', 'nvl') $wherePurchase
            GROUP BY tblpurchases_items.type, tblpurchases_items.product_id
        ) tb_purchase";
	$query = '
				SELECT 
				CONCAT(
					tb_cs.item_type,
					"__",
					tb_cs.item_id
				) AS item_id,
				tb_cs.item_name,
				tb_cs.item_code,
				tb_cs.unit_primary_name AS unit_primary_name,
				tb_cs.quantity_primary AS quantity_primary,
				tb_cs.quantity_inventory AS quantity_inventory,
				tb_cs.quantity_transfer AS quantity_transfer,
				COALESCE(tb_purchase.quantity_net) AS quantity_purchase,
				(
					COALESCE(tb_cs.quantity_primary, 0) - COALESCE(tb_cs.quantity_inventory, 0) - COALESCE(tb_purchase.quantity_net, 0) - COALESCE(tb_cs.quantity_transfer, 0)
				) AS quantity_rest,
				tb_cs.item_type AS item_type,
				tb_cs.item_id AS item_id
			FROM '.$tbProductionsPlanBom.'
			LEFT JOIN '.$tbPurchase.' ON tb_purchase.type_item = tb_cs.item_type AND tb_purchase.product_id = tb_cs.item_id
			WHERE
				(COALESCE(tb_cs.quantity_primary, 0) - COALESCE(tb_cs.quantity_inventory, 0) - COALESCE(tb_purchase.quantity_net, 0) - COALESCE(tb_cs.quantity_transfer, 0)) > 0
			';
	$rResult = $CI->db->query($query)->result_array();
	$data_view = [];
	foreach($rResult as $key => $value) {
		if(empty($data_view[$value['item_id']])) {
			$data_view[$value['item_id']] = $value;
		}
		else {
			$data_view[$value['item_id']]['quantity_rest'] += $value['quantity_rest'];
		}
	}
	return $data_view;
}

//lấy kế hoạch sản xuất
function get_productions_orders($date_start = '', $date_end = '') {
	$CI = &get_instance();
	$tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            GROUP BY tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

	$CI->db->select([
		'tbl_productions_orders.id as id',
		'tbl_productions_orders.date as date',
		'tbl_productions_orders.reference_no as reference_no',
		'tb_production_order_item.quantity as quantity',
		'tb_production_order_item.quantity_warehoused as quantity_finished',
		'coalesce(tb_production_order_item.quantity, 0) - coalesce(tb_production_order_item.quantity_warehoused, 0) as quantity_rest',
		'tbl_productions_orders.note as note',
		'tbl_productions_orders.status_details',
		'tbl_productions_orders.options1 as options1',
		'tbl_productions_orders.options2 as options2',
		'tbl_productions_orders.total_quantity as total_quantity',
		'tbl_productions_orders.status_orders as status_orders'
	]);
	$CI->db->from('tbl_productions_orders');
	$CI->db->join($tbProductionsOrderItems, 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id', 'left');
	if (!empty($date_start)) {
		$CI->db->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >= "'.$date_start.'"');
	}
	if (!empty($date_end)) {
		$CI->db->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <= "'.$date_end.'"');
	}

	$CI->db->where('tbl_productions_orders.id NOT IN (
                            SELECT productions_orders_id 
                            FROM tbl_productions_orders_items_stages 
                            WHERE final_stage = 1 AND active = 1
                            AND pois_id = 0
                        )', false, false);
	$CI->db->where('EXISTS (
                                SELECT productions_orders_id 
                                FROM tbl_productions_orders_items_stages 
                                WHERE active = 1
                                AND tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id
                                AND pois_id = 0
						)', false, false);

	$CI->db->limit(10);
	$dataResult = $CI->db->get()->result_array();
	$data = [];
	$color = [
		'rgb(255, 99, 132)',
		'rgb(75, 192, 192)',
		'rgb(255, 205, 86)',
	];
	if(!empty($dataResult)) {
		foreach($dataResult as $key => $value) {
			$data['labels'][] = $value['reference_no'];
			$data['datasets'][0]['label'] = 'Kế hoạch';
			$data['datasets'][1]['label'] = 'Thực hiện đạt';
			$data['datasets'][2]['label'] = 'Thực hiện';

			$data['datasets'][0]['data'][] = $value['quantity'];
			$data['datasets'][1]['data'][] = $value['quantity_finished'];
			$data['datasets'][2]['data'][] = $value['quantity_rest'];

			$data['datasets'][0]['backgroundColor'][] = $color[0];
			$data['datasets'][1]['backgroundColor'][] = $color[1];
			$data['datasets'][2]['backgroundColor'][] = $color[2];
		}
	}
	return $data;

}

function get_list_branch_staff($staffid = ''){
	$CI = &get_instance();
	if(empty($staffid)) {
		$staffid = get_staff_user_id();
	}
	$list_branch = $CI->db->select('GROUP_CONCAT(id_branch) as list_branch')
		->get_where('tblstaff_branch', ['staffid' => $staffid])->row('list_branch');
	return $list_branch;
}

function get_array_branch_staff($staffid = ''){
	$CI = &get_instance();
	if(empty($staffid)) {
		$staffid = get_staff_user_id();
	}
	$list_branch = $CI->db->select('GROUP_CONCAT(id_branch) as list_branch')
		->get_where('tblstaff_branch', ['staffid' => $staffid])->row('list_branch');
	return !empty($list_branch) ? explode(',', $list_branch) : [];
}


function tableItemsShortMysql()
{
	return '(
				(
					SELECT
						 (id) as product_id,
						 (type_products) as type,
						 (id) as data_product_id,
						 (name) as name_item,
						 (code) as code_item,
						 CONCAT("' . base_url() . 'download/preview_image?path=uploads/products/", (coalesce(images, "")), "") as avatar,
						 tblunits.unit as unit_name,
						 tblunits.unit as unit_name_payment,
						 \'<span class="inline-block label label-warning">Thành phẩm</span>\' as label_span
					FROM tbl_products
					LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
				)
			UNION(
					SELECT
						 (id) as product_id,
						 ("product") as type,
						 (id) as data_product_id,
						 (name) as name_item,
						 (code) as code_item,
						 CONCAT("' . base_url() . 'download/preview_image?path=uploads/products/", (coalesce(images, "")), "") as avatar,
						 tblunits.unit as unit_name,
						 tblunits.unit as unit_name_payment,
						 \'<span class="inline-block label label-warning">Thành phẩm</span>\' as label_span
					FROM tbl_products
					LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
				)
			UNION
				(
					SELECT
						(id) as product_id,
						("materials") as type,
						(id) as data_product_id,
						(name) as name_item,
						(code) as code_item,
						CONCAT("' . base_url() . 'download/preview_image?path=uploads/materials/", (coalesce(images, "")), "") as avatar,
						tblunits.unit as unit_name,
						payment_unit.unit as unit_name_payment,
						\'<span class="inline-block label label-warning">Nguyên vật liệu/span>\' as label_span
					FROM tbl_materials
					LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
					LEFT JOIN tblunits payment_unit ON payment_unit.unitid = tbl_materials.unit_payment
				)
	 ) tblitemsData';
}


if ( ! function_exists('days_in_month'))
{
    /**
     * Number of days in a month
     *
     * Takes a month/year as input and returns the number of days
     * for the given month/year. Takes leap years into consideration.
     *
     * @param	int	a numeric month
     * @param	int	a numeric year
     * @return	int
     */
    function days_in_month($month = 0, $year = '')
    {
        if ($month < 1 OR $month > 12)
        {
            return 0;
        }
        elseif ( ! is_numeric($year) OR strlen($year) !== 4)
        {
            $year = date('Y');
        }

        if (defined('CAL_GREGORIAN'))
        {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($year >= 1970)
        {
            return (int) date('t', mktime(12, 0, 0, $month, 1, $year));
        }

        if ($month == 2)
        {
            if ($year % 400 === 0 OR ($year % 4 === 0 && $year % 100 !== 0))
            {
                return 29;
            }
        }

        $days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        return $days_in_month[$month - 1];
    }
}
