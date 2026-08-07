<?php

defined('BASEPATH') or exit('No direct script access allowed');
function get_staff_permission($name_permission = '', $staff = '')
{
	$staff = get_table_where('tblstaff', array('id_zalo !=' => '', 'staffid !=' => $staff));
	$array = array();
	foreach ($staff as $key => $value) {
		if (has_permission($name_permission, $value['staffid'], 'view') || has_permission($name_permission, $value['staffid'], 'view_own')) {
			$array[] = $value['id_zalo'];
		}
	}
	return $array;
}
function create_purchase($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase = get_table_where('tblpurchases', array('id' => $id), '', 'row');
	$CI = &get_instance();
	$CI->db->where('purchases_id', $id);
	$CI->db->limit(4);
	$items = $CI->db->get('tblpurchases_items')->result_array();
	$staff = get_staff_permission('purchases', $purchase->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				$dataZalo = [
					'recipient' => [
						'user_id' => $value,
					],
					'message' => [
						'attachment' => [
							"type" => "template",
							"payload" => [
								"template_type" => "list",
								"elements" => [
									[
										'title' => 'YÊU CẦU MUA HÀNG',
										'subtitle' => 'Đơn yêu cầu mua hàng vừa được tạo: ' . $purchase->prefix . $purchase->code . ', Vào lúc: ' . _dt($purchase->date_create) . '. Người tạo: ' . get_staff_full_name($purchase->staff_create),
										'image_url' => 'https://stc-developers.zdn.vn/images/bg_1.jpg',
										'default_action' =>
										[
											'type' => 'oa.open.url',
											'url'  =>  admin_url('purchases?zalo_purchase=' . $id)
										]
									]
								]
							],
						]
					],
				];
				$index = 1;
				foreach ($items as $k => $v) {
					$item = get_items($v['product_id'], $v['type']);
					$dataZalo['message']['attachment']['payload']['elements'][$index]['title'] = $item->name . ' (SL: ' . number_format($v['quantity_net']) . ')';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['subtitle'] = $item->name;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['image_url'] = $item->avatar_1;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['type'] = 'oa.open.url';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['url'] = admin_url('purchases?zalo_purchase=' . $id);
					$index++;
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				$response = curl_exec($curl);
			}
		}
	}
}
function approve_purchase_app1($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase = get_table_where('tblpurchases', array('id' => $id), '', 'row');
	$staff = get_staff_permission('purchases', $purchase->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$subtitle = 'Phiếu Yêu Cầu Mua Hàng: ' . $purchase->prefix . $purchase->code . ' vừa được xác nhận, Vào lúc: ' . _d(date('Y-m-d H:i:s')) . ' Người xác nhận: ' . get_staff_full_name();
				$text = 'Xác nhận phiếu yêu cầu mua hàng';
				$link = admin_url('purchases?zalo_purchase=' . $id);
				$link_img = base_url('uploads/validation.png');
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://openapi.zalo.me/v2.0/oa/message?access_token=" . $token_zalo,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => "   {\r\n    \"recipient\" :{\r\n                \"user_id\" : \"$value\"\r\n                },\r\n    \"message\" :{\r\n        \"attachment\" :{\r\n            \"type\" : \"template\",\r\n            \"payload\" :{\r\n                \"template_type\" : \"list\",\r\n                \"elements\": [{\r\n                \"title\": \"$text\",\r\n                \"subtitle\": \"$subtitle\",\r\n                \"image_url\": \"$link_img\",\r\n                \"default_action\": {\r\n                    \"type\": \"oa.open.url\",\r\n                    \"url\": \"$link\"\r\n                    }\r\n            }]\r\n            }\r\n        }\r\n    }\r\n}",
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
					),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
			}
		}
	}
}
function approve_purchase_app2($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase = get_table_where('tblpurchases', array('id' => $id), '', 'row');
	$staff = get_staff_permission('purchases', $purchase->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$subtitle = 'Phiếu Yêu Cầu Mua Hàng: ' . $purchase->prefix . $purchase->code . ' vừa được duyệt, Vào lúc: ' . _d(date('Y-m-d H:i:s')) . ' Người duyệt: ' . get_staff_full_name();
				$text = 'Duyệt phiếu yêu cầu mua hàng';
				$link = admin_url('purchases?zalo_purchase=' . $id);
				$link_img = base_url('uploads/validation.png');
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://openapi.zalo.me/v2.0/oa/message?access_token=" . $token_zalo,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => "   {\r\n    \"recipient\" :{\r\n                \"user_id\" : \"$value\"\r\n                },\r\n    \"message\" :{\r\n        \"attachment\" :{\r\n            \"type\" : \"template\",\r\n            \"payload\" :{\r\n                \"template_type\" : \"list\",\r\n                \"elements\": [{\r\n                \"title\": \"$text\",\r\n                \"subtitle\": \"$subtitle\",\r\n                \"image_url\": \"$link_img\",\r\n                \"default_action\": {\r\n                    \"type\": \"oa.open.url\",\r\n                    \"url\": \"$link\"\r\n                    }\r\n            }]\r\n            }\r\n        }\r\n    }\r\n}",
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
					),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
			}
		}
	}
}
function create_purchase_to_rfq($id)
{
	$token_zalo = get_option('token_zalo');
	$price = get_table_where('tblrfq_ask_price', array('id' => $id), '', 'row');
	$purchase = get_table_where('tblpurchases', array('id' => $price->id_purchases), '', 'row');
	$CI = &get_instance();
	$CI->db->where_in('id', explode(',', $price->suppliers_id));
	$CI->db->limit(4);
	$items = $CI->db->get('tblsuppliers')->result_array();
	$staff = get_staff_permission('purchases', $price->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				$dataZalo = [
					'recipient' => [
						'user_id' => $value,
					],
					'message' => [
						'attachment' => [
							"type" => "template",
							"payload" => [
								"template_type" => "list",
								"elements" => [
									[
										'title' => 'TỪ YÊU CẦU MUA HÀNG TẠO PHIẾU HỎI GIÁ',
										'subtitle' => 'Phiếu hỏi giá: ' . $price->prefix . '-' . $price->code . ' vừa được tạo từ YCMH: ' . $purchase->prefix . $purchase->code . ', Vào lúc: ' . _dt($price->date_create) . '. Người tạo: ' . get_staff_full_name($price->staff_create),
										'image_url' => 'https://stc-developers.zdn.vn/images/bg_1.jpg',
										'default_action' =>
										[
											'type' => 'oa.open.url',
											'url'  =>  admin_url('RFQ?zalo_purchase=' . $price->id_purchases)
										]
									]
								]
							],
						]
					],
				];
				$index = 1;
				foreach ($items as $k => $v) {
					$dataZalo['message']['attachment']['payload']['elements'][$index]['title'] = $v['company'];
					$dataZalo['message']['attachment']['payload']['elements'][$index]['subtitle'] = $v['company'];
					$dataZalo['message']['attachment']['payload']['elements'][$index]['image_url'] = base_url('uploads/avatar_supplier.jpg');
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['type'] = 'oa.open.url';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['url'] = admin_url('RFQ?zalo_purchase=' . $price->id_purchases);
					$index++;
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				$response = curl_exec($curl);
			}
		}
	}
}
function create_purchase_to_order($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
	$purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
	$CI = &get_instance();
	$CI->db->where('id_purchase_order', $id);
	$CI->db->limit(4);
	$items = $CI->db->get('tblpurchase_order_items')->result_array();
	$staff = get_staff_permission('purchase_order', $purchase_order->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				$dataZalo = [
					'recipient' => [
						'user_id' => $value,
					],
					'message' => [
						'attachment' => [
							"type" => "template",
							"payload" => [
								"template_type" => "list",
								"elements" => [
									[
										'title' => 'TỪ YÊU CẦU MUA HÀNG TẠO ĐƠN HÀNG MUA',
										'subtitle' => 'Đơn hàng mua: ' . $purchase_order->prefix . '-' . $purchase_order->code . ' vừa được tạo từ YCMH: ' . $purchase->prefix . $purchase->code . ', Vào lúc: ' . _dt($purchase_order->date_create) . '. Người tạo: ' . get_staff_full_name($purchase_order->staff_create),
										'image_url' => 'https://stc-developers.zdn.vn/images/bg_1.jpg',
										'default_action' =>
										[
											'type' => 'oa.open.url',
											'url'  =>  admin_url('purchase_order?zalo_purchase=' . $id)
										]
									]
								]
							],
						]
					],
				];
				$index = 1;
				foreach ($items as $k => $v) {
					$item = get_items($v['product_id'], $v['type']);
					$dataZalo['message']['attachment']['payload']['elements'][$index]['title'] = $item->name . ' (SL: ' . number_format($v['quantity_suppliers']) . ')';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['subtitle'] = $item->name;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['image_url'] = $item->avatar_1;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['type'] = 'oa.open.url';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['url'] = admin_url('purchase_order?zalo_purchase=' . $id);
					$index++;
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				$response = curl_exec($curl);
			}
		}
	}
}
function create_purchase_to_quotes($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase_order = get_table_where('tblsupplier_quotes', array('id' => $id), '', 'row');
	$purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
	$supplier = get_table_where('tblsuppliers', array('id' => $purchase_order->suppliers_id), '', 'row');
	$CI = &get_instance();
	$CI->db->where('id_supplier_quotes', $id);
	$CI->db->limit(4);
	$items = $CI->db->get('tblsupplier_quote_items')->result_array();
	$staff = get_staff_permission('supplier_quotes', $purchase_order->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				$dataZalo = [
					'recipient' => [
						'user_id' => $value,
					],
					'message' => [
						'attachment' => [
							"type" => "template",
							"payload" => [
								"template_type" => "list",
								"elements" => [
									[
										'title' => 'TỪ YÊU CẦU MUA HÀNG TẠO BÁO GIÁ',
										'subtitle' => 'Phiếu báo giá: ' . $purchase_order->prefix . '-' . $purchase_order->code . ' vừa được tạo từ YCMH: ' . $purchase->prefix . $purchase->code . ', Vào lúc: ' . _dt($purchase_order->date_create) . '. Người tạo: ' . get_staff_full_name($purchase_order->staff_create) . ' Nhà cung cấp: ' . $supplier->company,
										'image_url' => 'https://stc-developers.zdn.vn/images/bg_1.jpg',
										'default_action' =>
										[
											'type' => 'oa.open.url',
											'url'  =>  admin_url('supplier_quotes?zalo_purchase=' . $id)
										]
									]
								]
							],
						]
					],
				];
				$index = 1;
				foreach ($items as $k => $v) {
					$item = get_items($v['product_id'], $v['type']);
					$dataZalo['message']['attachment']['payload']['elements'][$index]['title'] = $item->name . ' (SL: ' . number_format($v['quantity']) . ')';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['subtitle'] = $item->name;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['image_url'] = $item->avatar_1;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['type'] = 'oa.open.url';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['url'] = admin_url('supplier_quotes?zalo_purchase=' . $id);
					$index++;
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				$response = curl_exec($curl);
			}
		}
	}
}
function create_order($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
	$CI = &get_instance();
	$CI->db->where('id_purchase_order', $id);
	$CI->db->limit(4);
	$items = $CI->db->get('tblpurchase_order_items')->result_array();
	$staff = get_staff_permission('purchase_order', $purchase_order->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				$dataZalo = [
					'recipient' => [
						'user_id' => $value,
					],
					'message' => [
						'attachment' => [
							"type" => "template",
							"payload" => [
								"template_type" => "list",
								"elements" => [
									[
										'title' => 'TẠO ĐƠN HÀNG MUA',
										'subtitle' => 'Đơn hàng mua: ' . $purchase_order->prefix . '-' . $purchase_order->code . ' vừa được tạo , Vào lúc: ' . _dt($purchase_order->date_create) . '. Người tạo: ' . get_staff_full_name($purchase_order->staff_create),
										'image_url' => 'https://stc-developers.zdn.vn/images/bg_1.jpg',
										'default_action' =>
										[
											'type' => 'oa.open.url',
											'url'  =>  admin_url('purchase_order?zalo_purchase=' . $id)
										]
									]
								]
							],
						]
					],
				];
				$index = 1;
				foreach ($items as $key => $v) {
					$item = get_items($v['product_id'], $v['type']);
					$dataZalo['message']['attachment']['payload']['elements'][$index]['title'] = $item->name . ' (SL: ' . number_format($v['quantity_suppliers']) . ')';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['subtitle'] = $item->name;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['image_url'] = $item->avatar_1;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['type'] = 'oa.open.url';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['url'] = admin_url('purchase_order?zalo_purchase=' . $id);
					$index++;
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				$response = curl_exec($curl);
			}
		}
	}
}
function approve_order_app($id)
{
	$token_zalo = get_option('token_zalo');
	$purchase =  get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
	$staff = get_staff_permission('purchases', $purchase->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$subtitle = 'Đơn hàng mua: ' . $purchase->prefix . '-' . $purchase->code . ' vừa được duyệt, Vào lúc: ' . _d(date('Y-m-d H:i:s')) . ' Người duyệt: ' . get_staff_full_name();
				$text = 'Duyệt đơn hàng mua';
				$link = admin_url('purchase_order?zalo_purchase=' . $id);
				$link_img = base_url('uploads/validation.png');
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://openapi.zalo.me/v2.0/oa/message?access_token=" . $token_zalo,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => "   {\r\n    \"recipient\" :{\r\n                \"user_id\" : \"$value\"\r\n                },\r\n    \"message\" :{\r\n        \"attachment\" :{\r\n            \"type\" : \"template\",\r\n            \"payload\" :{\r\n                \"template_type\" : \"list\",\r\n                \"elements\": [{\r\n                \"title\": \"$text\",\r\n                \"subtitle\": \"$subtitle\",\r\n                \"image_url\": \"$link_img\",\r\n                \"default_action\": {\r\n                    \"type\": \"oa.open.url\",\r\n                    \"url\": \"$link\"\r\n                    }\r\n            }]\r\n            }\r\n        }\r\n    }\r\n}",
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
					),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
			}
		}
	}
}
function create_order_to_import($id)
{
	$token_zalo = get_option('token_zalo');
	$import = get_table_where('tblimport', array('id' => $id), '', 'row');
	$purchase_order = get_table_where('tblpurchase_order', array('id' => $import->id_order), '', 'row');
	$CI = &get_instance();
	$CI->db->where('id_import', $id);
	$CI->db->limit(4);
	$items = $CI->db->get('tblimport_items')->result_array();
	$staff = get_staff_permission('import', $import->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$url = "https://openapi.zalo.me/v2.0/oa/message";
				$access_token = get_option('token_zalo');
				$query_fields = ['access_token' => $access_token];
				$curl = curl_init($url . '?' . http_build_query($query_fields));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				$dataZalo = [
					'recipient' => [
						'user_id' => $value,
					],
					'message' => [
						'attachment' => [
							"type" => "template",
							"payload" => [
								"template_type" => "list",
								"elements" => [
									[
										'title' => 'TỪ ĐƠN HÀNG MUA TẠO PHIẾU NHẬP HÀNG',
										'subtitle' => 'Phiếu nhập hàng: ' . $import->prefix . '-' . $import->code . ' vừa được tạo từ đơn hàng mua: ' . $purchase_order->prefix . '-' . $purchase_order->code . ', Vào lúc: ' . _dt($import->date_create) . '. Người tạo: ' . get_staff_full_name($import->staff_create),
										'image_url' => 'https://stc-developers.zdn.vn/images/bg_1.jpg',
										'default_action' =>
										[
											'type' => 'oa.open.url',
											'url'  =>  admin_url('import?zalo_purchase=' . $id)
										]
									]
								]
							],
						]
					],
				];
				$index = 1;
				foreach ($items as $k => $v) {
					$item = get_items($v['product_id'], $v['type']);
					$dataZalo['message']['attachment']['payload']['elements'][$index]['title'] = $item->name . ' (SL: ' . number_format($v['quantity_net']) . ')';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['subtitle'] = $item->name;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['image_url'] = $item->avatar_1;
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['type'] = 'oa.open.url';
					$dataZalo['message']['attachment']['payload']['elements'][$index]['default_action']['url'] = admin_url('import?zalo_purchase=' . $id);
					$index++;
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($dataZalo, JSON_UNESCAPED_UNICODE));
				$response = curl_exec($curl);
			}
		}
	}
}
function approve_import_app($id)
{
	$token_zalo = get_option('token_zalo');
	$import = get_table_where('tblimport', array('id' => $id), '', 'row');
	$staff = get_staff_permission('import', $import->staff_create);
	if (!empty($staff)) {
		foreach ($staff as $key => $value) {
			if (!empty($value)) {
				$subtitle = 'Phiếu nhập kho: ' . $import->prefix . '-' . $import->code . ' vừa được duyệt, Vào lúc: ' . _d(date('Y-m-d H:i:s')) . ' Người duyệt: ' . get_staff_full_name();
				$text = 'Duyệt phiếu nhập kho';
				$link = admin_url('import?zalo_purchase=' . $id);
				$link_img = base_url('uploads/validation.png');
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://openapi.zalo.me/v2.0/oa/message?access_token=" . $token_zalo,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => "   {\r\n    \"recipient\" :{\r\n                \"user_id\" : \"$value\"\r\n                },\r\n    \"message\" :{\r\n        \"attachment\" :{\r\n            \"type\" : \"template\",\r\n            \"payload\" :{\r\n                \"template_type\" : \"list\",\r\n                \"elements\": [{\r\n                \"title\": \"$text\",\r\n                \"subtitle\": \"$subtitle\",\r\n                \"image_url\": \"$link_img\",\r\n                \"default_action\": {\r\n                    \"type\": \"oa.open.url\",\r\n                    \"url\": \"$link\"\r\n                    }\r\n            }]\r\n            }\r\n        }\r\n    }\r\n}",
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
					),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
			}
		}
	}
}
