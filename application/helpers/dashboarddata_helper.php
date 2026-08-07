<?php

defined('BASEPATH') or exit('No direct script access allowed');
function dataPurchases($id = '')
{
    $CI = &get_instance();

    $query = '(SELECT COALESCE(SUM(tblimport_items.quantity_stock),0) as quantity_stock,id_purchase_order_items FROM tblimport_items GROUP BY id_purchase_order_items) import_items';
    $CI->db->select("
        tblpurchase_order.id,
        tblpurchase_order.date,
        tblpurchase_order_items.id as idd,
        CONCAT(tblpurchase_order.prefix,'-', tblpurchase_order.code) as code,
        tbl_materials.code as code_items,
        tbl_materials.name as name_items,
        tblpurchase_order_items.type as type,
        tblpurchase_order_items.product_id as product_id,
        tblpurchase_order.plan_id as plan_id,
        tblpurchase_order.id as id_purchase_order_items,
        COALESCE(import_items.quantity_stock, 0) as quantity_stock_import,
        tblpurchase_order_items.quantity_unit as quantity_unit,
        tblpurchase_order_items.quantity_stock as quantity_stock,
        tblpurchase_order_items.quantity_payment as quantity_payment,
        tblunits.unit as unit,
        payment_unit.unit as unit_name_payment,
        stock_unit.unit as unit_name_stock
        ");
    $CI->db->where('tblpurchase_order.id', $id);
    // Chỉ lọc khi đã có phiếu nhập (import_items.quantity_stock > 0), nếu chưa có thì lấy hết
    // $CI->db->group_start();
    // $CI->db->where('import_items.quantity_stock IS NULL', null, false);
    // $CI->db->or_where('tblpurchase_order_items.quantity_stock > import_items.quantity_stock', null, false);
    // $CI->db->group_end();
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order', 'left');
    $CI->db->join('tbl_materials', 'tbl_materials.id = tblpurchase_order_items.product_id', 'left');
    $CI->db->join('tblunits', 'tblunits.unitid=tbl_materials.unit_id', 'left');
    $CI->db->join('tblunits payment_unit', 'payment_unit.unitid=tbl_materials.unit_payment', 'left');
    $CI->db->join('tblunits stock_unit', 'stock_unit.unitid=tbl_materials.standard_unit', 'left');
    $CI->db->join($query, 'import_items.id_purchase_order_items = tblpurchase_order_items.id', 'left', false);
    $purchase_order = $CI->db->get('tblpurchase_order_items')->result_array();
    foreach ($purchase_order as $key => $value) {
        $updatedRow = _api_row_warehousepurchaseOrder($value);
        sendSocket([
            'action'     => 'update',
            'updatedRow' => $updatedRow
        ], [], 'update_dashboard_purchase');
    }
    sendSocket([], [], 'update_dashboard_warehouse');
}
function _api_row_warehousepurchaseOrder($r)
{
    // Chuẩn hóa row theo format frontend đang dùng
    return [
        'id' => $r['id'],
        'idd' => $r['idd'],
        'date' => _d($r['date']),
        'code' => ($r['code']),
        'quantity_stock_import'        => formatNumber($r['quantity_stock_import']),
        'quantity_unit'        => formatNumber($r['quantity_unit']),
        'quantity_stock'        => formatNumber($r['quantity_stock']),
        'quantity_payment'        => formatNumber($r['quantity_payment']),
        'quantity_stock_left'        => formatNumber($r['quantity_stock'] - $r['quantity_stock_import']),
        'code_items'      => $r['code_items'],
        'name_items'   => $r['name_items'],
        'unit'   => $r['unit'],
        'unit_name_payment'   => $r['unit_name_payment'],
        'unit_name_stock'   => $r['unit_name_stock'],
        'statusClass'   => $r['quantity_stock_import'] == 0 ? 'red_purchaseorder' : 'yellow_purchaseorder'
    ];
}
