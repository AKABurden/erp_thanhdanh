<?php defined('BASEPATH') or exit('No direct script access allowed');
function decreaseexexport_different_WarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		$quantitys = $quantity;
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		$total = 0;
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				$quantitynet = $quantity;
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				if ($quantity < 0) {
					$total += $product_quantity_payment * $value['price'];
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'PKX-' . $transfer . '|' . $value['id_export'];
					// $id_import = $value['id'] . '-' . ($quantitynet) . '|' . $id_import;
					// $quantity = 0;
					// $export = 'PKX-' . $transfer . '|' . $value['id_export'];
					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'id_export' => $export));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					$total += $value['product_quantity_payment_left'] * $value['price'];
					// $id_import = $value['id'] . '-' . ($value['quantity_left']) . '|' . $id_import;
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;

					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
					$export = 'PKX-' . $transfer . '|' . $value['id_export'];

					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export']), 'id_export' => $export));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$price = $total / $quantitys;
		$amount = $total;
		$CI->db->where('id', $transfer);
		$CI->db->update('tbltblexport_different_items', array('id_import' => $id_import, 'price' => $price, 'amount' => $amount));
		return true;
	}
	return false;
}

function export_different_WarehuseQuantity($warehouse_id, $id_export, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_export)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $id_export,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'type_export' => 19,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function increaseProductQuantityimport_outsource($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $pirce)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 31,
			'price' => $pirce,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function increase_purchase_internalProductQuantity($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $price, $type_transfer = 0, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 20,
			'price' => $price,
			'type_transfer' => $type_transfer,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_unit_export' => 0,
			'product_quantity_unit_left' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'product_quantity_payment_export' => 0,
			'product_quantity_payment_left' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function increase_purchase_productsProductQuantity($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 18,
			'date_sx' => NULL,
			'date_sd' => NULL,
			'date_use' => NULL,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_unit_export' => 0,
			'product_quantity_unit_left' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'product_quantity_payment_export' => 0,
			'product_quantity_payment_left' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function exporting_producion_WarehuseQuantity($warehouse_id, $id_export, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_export)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $id_export,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'type_export' => 17,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function decreaseexporting_producion_WarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				$quantitynet = $quantity;
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				$quantity_unit = $quantity_unit - $value['product_quantity_unit_left'];
				$quantity_payment = $quantity_payment - $value['product_quantity_payment_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'XKSX-' . $transfer . '|' . $value['id_export'];
					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'id_export' => $export));

					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;

					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export'])));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tbl_suggest_exporting_items', array('id_import' => $id_import));
		return true;
	}
	return false;
}

function export_WarehuseQuantity($warehouse_id, $id_export, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_export)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $id_export,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'type_export' => 16,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function decreaseexport_WarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				$quantitynet = $quantity;
				$quantity = $quantity - $value['quantity_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '|' . $id_import;
					$quantity = 0;
					$export = 'XKBH-' . $transfer . '|' . $value['id_export'];
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'id_export' => $export));
				} else {
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '|' . $id_import;
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tbl_export_warehous_items', array('id_import' => $id_import));
		return true;
	}
	return false;
}

function export_RetrunWarehuseQuantity($warehouse_id, $id_return, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code = null, $date_sx = null, $date_sd = null, $date_use = null, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_return)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $id_return,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'type_export' => 15,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function export_AdjuWarehuseQuantity($warehouse_id, $adju_id, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = '', $quantity_payment = '')
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($adju_id)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $adju_id,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'type_export' => 3,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function decreaseRetrunWarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = null, $date_sx = null, $date_sd = null, $date_use = null, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		$quantitys = $quantity;
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		// var_dump($TransferWarehuseQuantity);die;
		foreach ($TransferWarehuseQuantity as $key => $value) {
			// if ($quantity > 0) {
			// 	$quantitynet = $quantity;
			// 	$quantity = $quantity - $value['quantity_left'];
			// 	if ($quantity < 0) {
			// 		$id_import = $value['id'] . '-' . ($quantitynet) . '|' . $id_import;
			// 		$quantity = 0;
			// 		$export = 'TH-' . $transfer . '|' . $value['id_export'];
			// 		$CI->db->where('id', $value['id']);
			// 		$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'id_export' => $export));
			// 	} else {
			// 		$id_import = $value['id'] . '-' . ($value['quantity_left']) . '|' . $id_import;
			// 		$CI->db->where('id', $value['id']);
			// 		$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
			// 	}
			// }
			if ($quantity > 0) {
				$quantitynet = $quantity;
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'TH-' . $transfer . '|' . $value['id_export'];
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;
					$export = 'TH-' . $transfer . '|' . $value['id_export'];
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export']), 'id_export' => $export));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tblreturn_suppliers_items', array('id_import' => $id_import));
		return true;
	}
	return false;
}

function decreaseAdjuWarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		// $TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		// usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				$quantitynet = $quantity;
				// $quantity = $quantity - $value['quantity_left'];
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				$quantity_unit = $quantity_unit - $value['product_quantity_unit_left'];
				$quantity_payment = $quantity_payment - $value['product_quantity_payment_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'DC-' . $transfer . '|' . $value['id_export'];
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					// $id_import = $value['id'] . '-' . ($value['quantity_left']) . '|' . $id_import;
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;
					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export'])));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tbladjusted_items', array('id_import' => $id_import));
		return true;
	}
	return false;
}

function decreaseTransferWarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				$quantitynet = $quantity;
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				$quantity_unit = $quantity_unit - $value['product_quantity_unit_left'];
				$quantity_payment = $quantity_payment - $value['product_quantity_payment_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'CK-' . $transfer . '|' . $value['id_export'];
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;

					$product_quantity_unit_check = 0;
					$product_quantity_payment_check = 0;

					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export'])));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tbltransfer_warehouse_detail', array('id_import' => $id_import));
		return true;
	}
	return false;
}

function increaseadjuProductQuantity($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $price, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 3,
			'price' => $price,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_unit_export' => 0,
			'product_quantity_unit_left' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'product_quantity_payment_export' => 0,
			'product_quantity_payment_left' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function increaseProductQuantity($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $pirce, $transfer_ch, $lot_code = NULL, $date_sx = '', $date_sd = '', $date_use = '', $quantity_unit = '', $quantity_payment = '')
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 1,
			'price' => $pirce,
			'lot_code' => $lot_code,
			'type_transfer' => $transfer_ch,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_unit_export' => 0,
			'product_quantity_unit_left' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'product_quantity_payment_export' => 0,
			'product_quantity_payment_left' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function increaseTransferWarehuseQuantity($warehouse_id, $export_id, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($export_id)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $export_id,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'type_export' => 2,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function dincreaseTransferWarehuseQuantity($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $price, $type_transfer, $id_plan, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 2,
			'price' => $price,
			'type_transfer' => $type_transfer,
			'id_plan' => $id_plan,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_unit_export' => 0,
			'product_quantity_unit_left' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'product_quantity_payment_export' => 0,
			'product_quantity_payment_left' => $quantity_payment
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function increaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($localtion)) {
		$product = $CI->db->get_where('tblwarehouse_items', array('id_items' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use))->row();
		if ($product) {
			$total_quantity = $quantity + $product->product_quantity;
			$total_quantity_unit = $quantity_unit + $product->product_quantity_unit;
			$total_quantity_payment = $quantity_payment + $product->product_quantity_payment;
			$CI->db->update('tblwarehouse_items', array('product_quantity' => $total_quantity, 'product_quantity_unit' => $total_quantity_unit, 'product_quantity_payment' => $total_quantity_payment), array('id' => $product->id));
		} else {
			$data = array(
				'id_items' => $product_id,
				'warehouse_id' => $warehouse_id,
				'product_quantity' => $quantity,
				'localtion' => $localtion,
				'lot_code' => $lot_code,
				'type_items' => $type_items,
				'date_sx' => $date_sx,
				'date_sd' => $date_sd,
				'date_use' => $date_use,
				'product_quantity_unit' => $quantity_unit,
				'product_quantity_payment' => $quantity_payment
			);
			$CI->db->insert('tblwarehouse_items', $data);
		}
		if ($CI->db->affected_rows() > 0)
			return true;
	}
	return false;
}

function decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($localtion)) {
		$product = $CI->db->get_where('tblwarehouse_items', array('id_items' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use))->row();
		if ($product) {
			// $total_quantity = $product->product_quantity - $quantity;
			// $CI->db->update('tblwarehouse_items', array('product_quantity' => $total_quantity), array('id' => $product->id));

			$total_quantity = +$product->product_quantity -  $quantity;
			$total_quantity_unit =  $product->product_quantity_unit - $quantity_unit;
			$total_quantity_payment =  $product->product_quantity_payment - $quantity_payment;
			$CI->db->update('tblwarehouse_items', array('product_quantity' => $total_quantity, 'product_quantity_unit' => $total_quantity_unit, 'product_quantity_payment' => $total_quantity_payment), array('id' => $product->id));
		}
		if ($CI->db->affected_rows() > 0)
			return true;
	}
	return false;
}

function get_localtion_warehouses_product($where = array())
{
	$CI = &get_instance();
	$CI->db->select('tbllocaltion_warehouses.*,product_quantity');
	if (!empty($where)) {
		$CI->db->where($where);
	}
	$CI->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
	$CI->db->where('product_quantity >= 0');
	$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
	$string_option = "<option></option>";
	foreach ($localtion as $key => $value) {
		if (!empty($value['id'])) {
			$name = get_listname_localtion_warehouse($value['id']);
			$string_option .= '<option ' . $checkeds . ' quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
		}
	}
	return $string_option;
}

function exsit_localtion($warehouse_id = '', $localtion = '')
{
	$CI = &get_instance();
	$CI->db->where('localtion_warehouses_id', $localtion);
	$import = $CI->db->get('tblimport_items')->row();
	$CI->db->where('localtion_id', $localtion);
	$tran_id = $CI->db->get('tbltransfer_warehouse_detail')->row();
	$CI->db->where('localtion_to', $localtion);
	$tran_to = $CI->db->get('tbltransfer_warehouse_detail')->row();
	$CI->db->where('localtion_warehouses_id', $localtion);
	$return = $CI->db->get('tblreturn_suppliers_items')->row();
	$CI->db->where('localtion', $localtion);
	$inventory = $CI->db->get('tblinventory_items')->row();
	$CI->db->where('localtion', $localtion);
	$adjusted = $CI->db->get('tbladjusted_items')->row();
	//nhapkho thanh pham,thu hoi phe lieu,xuat kho san xuât,de nghi xuat vat tu,xuat kho ban hang,
	$CI->db->where('location_id', $localtion);
	$purchase_products = $CI->db->get('tbl_purchase_product_items')->row();
	$CI->db->where('location_id', $localtion);
	$suggest_exportin = $CI->db->get('tbl_suggest_exporting_items')->row();
	$CI->db->where('location_id', $localtion);
	$purchase_internal = $CI->db->get('tbl_purchase_internal_items')->row();
	$CI->db->where('location_id', $localtion);
	$export_warehous = $CI->db->get('tbl_export_warehous_items')->row();
	if (!empty($import) || !empty($tran_id) || !empty($tran_to) || !empty($return) || !empty($inventory) || !empty($adjusted) || !empty($purchase_products) || !empty($suggest_exportin) || !empty($purchase_internal) || !empty($export_warehous)) {
		return true;
	} else {
		return false;
	}
}

function decreaseSuppliersQuantity($suppliers_id, $localtion, $product_id, $quantity, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($suppliers_id) && is_numeric($quantity) && is_numeric($localtion)) {
		$product = $CI->db->get_where('tblwarehouse_suppliers', array('id_items' => $product_id, 'suppliers_id' => $suppliers_id, 'localtion' => $localtion, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use))->row();
		if ($product) {
			// $total_quantity = $product->product_quantity - $quantity;
			// $CI->db->update('tblwarehouse_suppliers', array('product_quantity' => $total_quantity), array('id' => $product->id));
			$total_quantity = +$product->product_quantity -  $quantity;
			$total_quantity_unit =  $product->product_quantity_unit - $quantity_unit;
			$total_quantity_payment =  $product->product_quantity_payment - $quantity_payment;
			$CI->db->update('tblwarehouse_suppliers', array('product_quantity' => $total_quantity, 'product_quantity_unit' => $total_quantity_unit, 'product_quantity_payment' => $total_quantity_payment), array('id' => $product->id));
		}
		if ($CI->db->affected_rows() > 0)
			return true;
	}
	return false;
}

function increaseSuppliertsQuantity($suppliers_id, $localtion, $product_id, $quantity, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($suppliers_id) && is_numeric($quantity) && is_numeric($localtion)) {
		$product = $CI->db->get_where('tblwarehouse_suppliers', array('id_items' => $product_id, 'suppliers_id' => $suppliers_id, 'localtion' => $localtion, 'type_items' => $type_items))->row();
		if ($product) {
			// $total_quantity = $quantity + $product->product_quantity;
			// $CI->db->update('tblwarehouse_suppliers', array('product_quantity' => $total_quantity), array('id' => $product->id));
			$total_quantity = $quantity + $product->product_quantity;
			$total_quantity_unit = $quantity_unit + $product->product_quantity_unit;
			$total_quantity_payment = $quantity_payment + $product->product_quantity_payment;
			$CI->db->update('tblwarehouse_suppliers', array('product_quantity' => $total_quantity, 'product_quantity_unit' => $total_quantity_unit, 'product_quantity_payment' => $total_quantity_payment), array('id' => $product->id));
		} else {
			$data = array(
				'id_items' => $product_id,
				'suppliers_id' => $suppliers_id,
				'product_quantity' => $quantity,
				'localtion' => $localtion,
				'lot_code' => $lot_code,
				'type_items' => $type_items,
				'date_sx' => $date_sx,
				'date_sd' => $date_sd,
				'date_use' => $date_use,
				'product_quantity_unit' => $quantity_unit,
				'product_quantity_payment' => $quantity_payment
			);
			$CI->db->insert('tblwarehouse_suppliers', $data);
		}
		if ($CI->db->affected_rows() > 0)
			return true;
	}
	return false;
}

function deliveries_WarehuseQuantity($warehouse_id, $id_export, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_export)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $id_export,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'type_export' => 38,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function decreasedeliveries_WarehuseQuantity($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		// $TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0));
		// usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		// $id_import = '';
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				// $quantitynet = $quantity;
				// $quantity = $quantity - $value['quantity_left'];
				$quantitynet = $quantity;
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				$quantity_unit = $quantity_unit - $value['product_quantity_unit_left'];
				$quantity_payment = $quantity_payment - $value['product_quantity_payment_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'XKGH-' . $transfer . '|' . $value['id_export'];
					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'id_export' => $export));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					// $id_import = $value['id'] . '-' . ($value['quantity_left']) . '|' . $id_import;
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;
					$export = 'XKGH-' . $transfer . '|' . $value['id_export'];
					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export']), 'id_export' => $export));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tbl_delivery_items', array('id_import' => $id_import));
		return true;
	}
	return false;
}
function get_localtion_warehouses_transfer($where = array(), $lever = NULL, $checked = '')
{
	$warehouse = get_table_where('tblwarehouse');
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI = &get_instance();
		$CI->db->where('status', 0);
		if (!empty($lever)) {
			$lever = (int)$lever - 1;
			$CI->db->where('lever', $lever);
		} else {
			$CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
		}
		if (!empty($where)) {
			$CI->db->where($where);
		}
		
		$CI->db->where('warehouse', $v['id']);
		$CI->db->where('order_id ', 0);
		$CI->db->where('pod_id ', 0);
		$CI->db->where('stage_id ', 0);
		$CI->db->where('productions_plan_id ', 0);
		$CI->db->where('tranfer_business_id ', 0);
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($localtion)) {
			$string_option .= '<optgroup label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$checkeds = '';
					if ($checked == $value['id']) {
						$checkeds = 'selected';
						$value['child'] = 1;
					}
					$string_option .= '<option ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . ' data-content="' . $value['name_parent'] . '" content="' . $value['name_parent'] . '">' . $value['name'] . '</option>';
					if (empty($lever)) {
						$string_option .= option_child_localtion_warehouses($value['id'], '', $checked);
					}
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}
function get_localtion_warehouses_v3($where = array(), $lever = NULL, $checked = '')
{
	$warehouse = get_table_where('tblwarehouse');
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI = &get_instance();
		$CI->db->where('status', 0);
		if (!empty($lever)) {
			$lever = (int)$lever - 1;
			$CI->db->where('lever', $lever);
		} else {
			$CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
		}
		if (!empty($where)) {
			$CI->db->where($where);
		}
		$CI->db->where('warehouse', $v['id']);
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($localtion)) {
			$string_option .= '<optgroup label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$checkeds = '';
					if ($checked == $value['id']) {
						$checkeds = 'selected';
						$value['child'] = 1;
					}
					$string_option .= '<option ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . ' data-content="' . $value['name_parent'] . '" content="' . $value['name_parent'] . '">' . $value['name'] . '</option>';
					if (empty($lever)) {
						$string_option .= option_child_localtion_warehouses($value['id'], '', $checked);
					}
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}

function get_localtion_warehouses_product_tranfer($where = array())
{
	$CI = &get_instance();
	$warehouse = get_table_where('tblwarehouse');
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI->db->select('tbllocaltion_warehouses.*,product_quantity,tblwarehouse_items.type_items,tblwarehouse_items.lot_code,tblwarehouse_items.date_sx,tblwarehouse_items.date_sd,tblwarehouse_items.date_use,tblwarehouse_items.id as idd,tbllocaltion_warehouses.order_id,tbl_productions_orders_details.object_type,tbllocaltion_warehouses.pod_id');
		if (!empty($where)) {
			$CI->db->where($where);
		}
		$CI->db->where('warehouse_id', $v['id']);
		$CI->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
		$CI->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id=tbllocaltion_warehouses.pod_id','left');
		$CI->db->where('product_quantity > 0');
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		// if($v['id'] == 15){
		// 	echo '<pre>';print_arrays($CI->db->last_query());die;
		// }
		if (!empty($localtion)) {
			$string_option .= '<optgroup data-check ="1" data-text ="' . $v['name'] . '" label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$name = get_listname_localtion_warehouse($value['id']);
					if($value['order_id'] > 0 || $value['object_type'] == "orders"){
						$name.=' (Chờ Giao)';
					}
					if(($value['order_id'] == 0 && $value['object_type'] != "orders") ||($value['order_id'] == 0 && $value['pod_id'] == "orders") ){
						$name.=' (Tồn Sẵn)';
					}
					$string_option .= '<option data-check ="0" data-type= "' . $value['type_items'] . '" data-text ="' . $name . '(' . $value['product_quantity'] . ')" data-lot = "' . $value['lot_code'] . '"  data-date_sx = "' . _d($value['date_sx']) . '"  data-date_sd = "' . _d($value['date_sd']) . '"  data-date_use = "' . _d($value['date_use']) . '"  quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['idd'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}
function get_localtion_warehouses_product_tranfer_report($where = array())
{
	$CI = &get_instance();
	$warehouse = get_table_where('tblwarehouse');
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI->db->select('tbllocaltion_warehouses.*,product_quantity,tblwarehouse_items.type_items,tblwarehouse_items.lot_code,tblwarehouse_items.date_sx,tblwarehouse_items.date_sd,tblwarehouse_items.date_use,tblwarehouse_items.id as idd');
		if (!empty($where)) {
			$CI->db->where($where);
		}
		$CI->db->where('warehouse', $v['id']);
		$CI->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
		$CI->db->where('product_quantity >= 0');
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($localtion)) {
			$string_option .= '<optgroup data-check ="1" data-text ="' . $v['name'] . '" label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$name = get_listname_localtion_warehouse($value['id']);
					$string_option .= '<option data-check ="0" data-type= "' . $value['type_items'] . '" data-text ="' . $name . '(' . $value['product_quantity'] . ')" data-lot = "' . $value['lot_code'] . '"  data-date_sx = "' . _d($value['date_sx']) . '"  data-date_sd = "' . _d($value['date_sd']) . '"  data-date_use = "' . _d($value['date_use']) . '"  quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['idd'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}

function get_localtion_warehouses_product_export($where = array())
{
	$CI = &get_instance();
	$warehouse = get_table_where('tblwarehouse');
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI->db->select('tbllocaltion_warehouses.*,product_quantity,tblwarehouse_items.type_items,tblwarehouse_items.lot_code,tblwarehouse_items.date_sx,tblwarehouse_items.date_sd,tblwarehouse_items.date_use,tblwarehouse_items.id as idd');
		if (!empty($where)) {
			$CI->db->where($where);
		}
		$CI->db->where('warehouse', $v['id']);
		$CI->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
		$CI->db->where('product_quantity >= 0');
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($localtion)) {
			$string_option .= '<optgroup data-check ="1" data-text ="' . $v['name'] . '" label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$name = get_listname_localtion_warehouse($value['id']);
					$string_option .= '<option data-check ="0" data-type= "' . $value['type_items'] . '" data-text ="' . $name . '(' . $value['product_quantity'] . ')" data-lot = "' . $value['lot_code'] . '"  data-date_sx = "' . $value['date_sx'] . '"  data-date_sd = "' . _d($value['date_sd']) . '"  data-date_use = "' . _d($value['date_use']) . '"  quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['idd'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}

function get_localtion_warehouses_product_v2($where = array())
{
	$CI = &get_instance();
	$warehouse = get_table_where('tblwarehouse');
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI->db->select('tbllocaltion_warehouses.*,product_quantity');
		if (!empty($where)) {
			$CI->db->where($where);
		}
		$CI->db->where('warehouse', $v['id']);
		$CI->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
		$CI->db->where('product_quantity >= 0');
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($localtion)) {
			$string_option .= '<optgroup label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$name = get_listname_localtion_warehouse($value['id']);
					$string_option .= '<option  quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}

function get_localtion_warehouses_returned_goods($where = array(), $lever = NULL, $checked = '')
{
	$checked = 0;
	// $warehouse = get_table_where('tblwarehouse');
	$CI = &get_instance();
	// $warehouse = get_table_where('tblwarehouse');
	$CI->db->where('id !=',WAREHOUSES_CAPACITY);
	$CI->db->where('id !=',WAREHOUSES_ERRORS);
	$CI->db->where('id !=',WAREHOUSES_HOLD);
	$warehouse =  $CI->db->get('tblwarehouse')->result_array();
	$string_option = "<option></option>";
	foreach ($warehouse as $k => $v) {
		$CI->db->where('status', 0);
		if (!empty($lever)) {
			$lever = (int)$lever - 1;
			$CI->db->where('lever', $lever);
		} else {
			$CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
		}
		if (!empty($where)) {
			$CI->db->where($where);
		}
		$CI->db->where('warehouse', $v['id']);
		$CI->db->where('tbllocaltion_warehouses.pod_id', 0);
		$CI->db->where('tbllocaltion_warehouses.stage_id', 0);
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		if (!empty($localtion)) {
			$string_option .= '<optgroup label="' . $v['name'] . '">';
			foreach ($localtion as $key => $value) {
				if (!empty($value['id'])) {
					$checkeds = '';
					if ($checked == $value['id']) {
						$checkeds = 'selected';
						$value['child'] = 1;

					}
					$string_option .= '<option data-text="' . $v['name'] . '" ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . ' data-content="' . $value['name_parent'] . '" content="' . $value['name_parent'] . '">' . $value['name'] . '</option>';
					if (empty($lever)) {
						$string_option .= option_child_localtion_warehouses_ch($value['id'], '', $checked);
					}
				}
			}
			$string_option .= '</optgroup>';
		}
	}
	return $string_option;
}

function option_child_localtion_warehouses_ch($name, $id_parent = "", $list_array = "<i class='fa fa-caret-right' aria-hidden='true'></i>", $checked = '', &$string_option = "")
{
	$CI = &get_instance();
	if (!empty($id_parent)) {
		$CI->db->where('id_parent', $id_parent);
		$CI->db->where('status', 0);
		$CI->db->where_not_in('type_local', array(2, 3, 4));
		$localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
		foreach ($localtion as $key => $value) {
			if (!empty($value['id'])) {
				$checkeds = '';
				if ($checked == $value['id']) {
					$checkeds = 'selected';
				}
				$string_option .= '<option data-text="' . $name . '" data-text="' . $value['name'] . '" ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? ('child="' . $value['child'] . '" content="' . (get_listname_localtion_warehouse($value['id']) . '"')) : '') . ' data-content="' . $list_array . ' ' . $value['name'] . '">' . $value['name'] . '</option>';
				option_child_localtion_warehouses($name, $value['id'], ("<i class='fa fa-caret-right' aria-hidden='true'></i>" . $list_array), $checked, $string_option);
			}
		}
		return $string_option;
	}
}

function increaseProductQuantity_returned_bh($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $pirce, $transfer_ch, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 1324,
			'price' => $pirce,
			'type_transfer' => $transfer_ch,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return $CI->db->insert_id();
	}
	return false;
}

function manufacture_WarehuseQuantity($warehouse_id, $id_export, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $price = 0, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_export)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'export_id' => $id_export,
			'type_items' => $type_items,
			'date_export' => $date_export,
			'date_warehouse' => $date_warehouse,
			'type_export' => 3888,
			'lot_code' => $lot_code,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_export', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}

function decreaseManufacture_WarehuseQuantity_v2($warehouse_id, $transfer, $product_id, $quantity, $localtion, $type_items, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL, $quantity_unit = 0, $quantity_payment = 0)
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($transfer)) {
		// $TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('id' => $wareho_items_id));
		//
		// $TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items));
		// usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		// $id_import = '';
		$TransferWarehuseQuantity = get_table_where('tblwarehouse_product', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'quantity_left >' => 0, 'type_items' => $type_items, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use));
		usort($TransferWarehuseQuantity, ch_make_cmp(['date_import' => "ASC"]));
		$id_import = '';
		foreach ($TransferWarehuseQuantity as $key => $value) {
			if ($quantity > 0) {
				$quantitynet = $quantity;
				// $quantity = $quantity - $value['quantity_left'];
				$product_quantity_unit = $quantity_unit;
				$product_quantity_payment = $quantity_payment;
				$quantity = $quantity - $value['quantity_left'];
				$quantity_unit = $quantity_unit - $value['product_quantity_unit_left'];
				$quantity_payment = $quantity_payment - $value['product_quantity_payment_left'];
				if ($quantity < 0) {
					$id_import = $value['id'] . '-' . ($quantitynet) . '-' . ($product_quantity_unit) . '-' . ($product_quantity_payment) . '|' . $id_import;
					$quantity = 0;
					$export = 'XKGH-' . $transfer . '|' . $value['id_export'];
					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'id_export' => $export));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => ($value['quantity_left'] - $quantitynet), 'quantity_export' => ($value['quantity_export'] + $quantitynet), 'product_quantity_unit_left' => ($value['product_quantity_unit_left'] - $product_quantity_unit), 'product_quantity_unit_export' => ($value['product_quantity_unit_export'] + $product_quantity_unit), 'product_quantity_payment_left' => ($value['product_quantity_payment_left'] - $product_quantity_payment), 'product_quantity_payment_export' => ($value['product_quantity_payment_export'] + $product_quantity_payment), 'id_export' => $export));
				} else {
					// $id_import = $value['id'] . '-' . ($value['quantity_left']) . '|' . $id_import;
					$id_import = $value['id'] . '-' . ($value['quantity_left']) . '-' . ($value['product_quantity_unit_left']) . '-' . ($value['product_quantity_payment_left']) . '|' . $id_import;

					// $CI->db->where('id', $value['id']);
					// $CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export'])));
					$CI->db->where('id', $value['id']);
					$CI->db->update('tblwarehouse_product', array('quantity_left' => 0, 'product_quantity_unit_left' => 0, 'product_quantity_payment_left' => 0, 'quantity_export' => ($value['quantity_left'] + $value['quantity_export']), 'product_quantity_unit_export' => ($value['product_quantity_unit_left'] + $value['product_quantity_unit_export']), 'product_quantity_payment_export' => ($value['product_quantity_payment_left'] + $value['product_quantity_payment_export'])));
				}
			}
		}
	}
	if ($CI->db->affected_rows() > 0) {
		$CI->db->where('id', $transfer);
		$CI->db->update('tbl_manufactures_items_bom', array('id_import' => $id_import));
		return true;
	}
	return false;
}

function increaseProductQuantityManufacture($warehouse_id, $id_import, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $pirce, $type_transfer = 0, $lot_code = NULL, $date_sx = '', $date_sd = '', $date_use = '', $quantity_unit = '', $quantity_payment = '')
{
	$CI = &get_instance();
	if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity) && is_numeric($id_import)) {
		$data = array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
			'quantity' => $quantity,
			'localtion' => $localtion,
			'import_id' => $id_import,
			'type_items' => $type_items,
			'date_import' => $date_import,
			'date_warehouse' => $date_warehouse,
			'quantity_left' => $quantity,
			'quantity_export' => 0,
			'type_export' => 3888,
			'price' => $pirce,
			'type_transfer' => $type_transfer,
			'date_sx' => $date_sx,
			'date_sd' => $date_sd,
			'date_use' => $date_use,
			'product_quantity_unit' => $quantity_unit,
			'product_quantity_unit_export' => 0,
			'product_quantity_unit_left' => $quantity_unit,
			'product_quantity_payment' => $quantity_payment,
			'product_quantity_payment_export' => 0,
			'product_quantity_payment_left' => $quantity_payment,
		);
		$CI->db->insert('tblwarehouse_product', $data);
	}
	if ($CI->db->affected_rows() > 0) {
		return true;
	}
	return false;
}
